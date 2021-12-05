<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Receipt_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  public function cancel_receipt($receipt_id)
  {
    // echo "Cancel::" . $receipt_id . "<br>";
    $receipt = $this->db->get_where("coop_finance_transaction", array(
      "receipt_id" => $receipt_id
    ))->result_array();

    if ($this->is_non_pay_receipt($receipt_id)) {
      echo "IS_NON_PAY<br>";
      die("Err");
      $non_pay_receipt = $this->db->get_where("coop_non_pay_receipt", array(
        "receipt_id" => $receipt_id
      ))->result_array()[0];
      $non_pay_detail = $this->db->get_where("coop_non_pay_detail", array(
        "non_pay_id" => $non_pay_receipt['non_pay_id']
      ))->result_array();
      $non_pay_id = $non_pay_receipt['non_pay_id'];
      foreach ($receipt as $key => $value) {
        if ($this->is_loan($value)) {
          // $this->rollback_loan($value);
          $this->rollback_non_pay_loan($value, $non_pay_id);
          echo "IS_LOAN";
          echo "<br>";
        } else if ($this->is_loan_atm($value)) {
          $this->rollback_loan_atm($value);
          echo "IS_LOAN_ATM";
          echo "<br>";
        } else if ($this->is_share($value)) {
          $this->rollback_share($value);
          $this->rollback_non_pay_share($value, $non_pay_id);

          echo "IS_SHARE";
          echo "<br>";
        } else if ($this->is_fund($value)) {
          $this->rollback_fund($value);
          echo "IS_FUND";
          echo "<br>";
        }
      }

      foreach ($non_pay_detail as $key => $value) {
        $deduct_code = $value['deduct_code'];
        $pay_type = $value['pay_type'];
      }
    } else if ($this->is_finance_month_receipt($receipt_id)) {
      echo "IS_FINANCE_MONTH<br>";
      die("Err");
    } else {
      // echo "IS_OTHER_RECEIPT<br>";
      foreach ($receipt as $key => $value) {
        if ($this->is_loan($value)) {
          $this->rollback_loan($value);
          // echo "IS_LOAN";
          // echo "<br>";
        } else if ($this->is_loan_atm($value)) {
          $this->rollback_loan_atm($value);
          // echo "IS_LOAN_ATM";
          // echo "<br>";
        } else if ($this->is_share($value)) {
          $this->rollback_share($value);
          // echo "IS_SHARE";
          // echo "<br>";
        } else if ($this->is_fund($value)) {
          $this->rollback_fund($value);
          // echo "IS_FUND";
          // echo "<br>";
        }
      }
    }

    $this->db->set("cancel_date", date("Y-m-d h:i:s"));
    $this->db->set("order_by", "");
    $this->db->set("cancel_by", $_SESSION['USER_ID']);
    $this->db->set("receipt_status", 2);
    $this->db->where("receipt_id", $receipt_id);
    $this->db->update("coop_receipt");
    echo "success";
  }

  public function is_non_pay_receipt($receipt_id)
  {
    $non_pay = $this->db->get_where("coop_non_pay_receipt", array(
      "receipt_id" => $receipt_id
    ))->result_array();
    if (!empty($non_pay)) return true;
    return false;
  }

  public function is_finance_month_receipt($receipt_id)
  {
    $finance_month_receipt = $this->db->select("sum(period_count) as count")
      ->from("coop_finance_transaction")
      ->where("receipt_id = '" . $receipt_id . "'")
      ->get()
      ->result_array()[0];
    if ($finance_month_receipt['count'] > 0) return true;
    return false;
  }

  public function rollback_share($finance_transaction_row = array())
  {
    $share_transaction = $this->db->get_where("coop_mem_share", array(
      "share_bill" => $finance_transaction_row['receipt_id']
    ))->result_array()[0];
    $latest_transaction = $this->db->select("*")
      ->from("coop_mem_share")
      ->where("member_id = '" . $finance_transaction_row['member_id'] . "'")
      ->order_by("share_date desc, share_id desc")
      ->limit(1)
      ->get()
      ->result_array()[0];
    if ($share_transaction['share_id']) {
      if ($share_transaction['share_id'] == $latest_transaction['share_id']) {
        //is latest
        $this->db->where("share_id", $share_transaction['share_id']);
        $this->db->where("share_bill", $finance_transaction_row['receipt_id']);
        $this->db->delete("coop_mem_share");
      } else {
        //is not latest
        die("Err share");
      }
    }
  }

  public function rollback_deposit($finance_transaction_row = array())
  {
    $this->db->join("coop_maco_account as t2", "t1.account_id = t2.account_id", "inner");
    $deposit_transaction = $this->db->get_where("coop_account_transaction as t1", array(
      "mem_id"            => $finance_transaction_row['member_id'],
      "receipt_id"        => $finance_transaction_row['receipt_id']
    ))->result_array()[0];
    $this->db->order_by("transaction_time desc, transaction_id desc");
    $this->db->limit(1);
    $latest_transaction = $this->db->get_where("coop_account_transaction", array(
      "account_id" => $deposit_transaction['account_id']
    ))->result_array()[0];
    if ($deposit_transaction['transaction_id']) {
      if ($deposit_transaction['transaction_id'] == $latest_transaction['transaction_id']) {
        //is latest
        $this->db->where("account_id", $deposit_transaction['account_id']);
        $this->db->where("transaction_id", $deposit_transaction['transaction_id']);
        $this->db->where("receipt_id", $finance_transaction_row['receipt_id']);
        $this->db->delete("coop_account_transaction");
      } else {
        //is not latest
        die("Err deposit");
      }
    }
  }

  public function rollback_loan($finance_transaction_row = array())
  {
    $loan_id = $finance_transaction_row['loan_id'];
    $principal_payment = $finance_transaction_row['principal_payment'];
    $interest = $finance_transaction_row['interest'];
    $loan_transaction = $this->db->get_where("coop_loan_transaction", array(
      "loan_id" => $loan_id,
      "receipt_id" => $finance_transaction_row['receipt_id']
    ))->result_array()[0];
    $loan_transaction_id = $loan_transaction['loan_transaction_id'];
    if ($loan_transaction_id) {
      //check is last transaction
      $latest_transaction = $this->db->select("*")
        ->from("coop_loan_transaction")
        ->where("loan_id = " . $loan_id)
        ->order_by("transaction_datetime desc, loan_transaction_id desc")
        ->limit(1)
        ->get()
        ->result_array()[0];
      if ($loan_transaction['loan_transaction_id'] == $latest_transaction['loan_transaction_id']) {
        //is latest
        $this->db->where("loan_transaction_id", $loan_transaction['loan_transaction_id']);
        $this->db->limit(1);
        $this->db->delete("coop_loan_transaction");

        //หาค่าสำหรับนำมาอัพเดท coop_loan
        $previous_transaction = $this->db->select("*")
          ->from("coop_loan_transaction")
          ->where("loan_id = " . $loan_id)
          ->order_by("transaction_datetime desc, loan_transaction_id desc")
          ->limit(1)
          ->get()
          ->result_array()[0];

        // echo "<pre>";
        // var_dump($previous_transaction);
        // exit;
        if ($finance_transaction_row['period_count'] != "") {
          $this->db->set("period_now = (period_now-1)");
        }
        if ($previous_transaction['loan_amount_balance'] > 0) {
          $this->db->set("loan_status", 1);
        }
        $this->db->set("updatetimestamp", date("Y-m-d h:i:s"));
        // $this->db->set("updatetime", date("Y-m-d h:i:s"));
        // $this->db->set("lastupdate_datetime", date("Y-m-d h:i:s"));
        // $this->db->set("lastupdate_by", @$_SESSION['USER_ID']);
        $this->db->set("date_last_interest", $previous_transaction['transaction_datetime']);
        $this->db->set("loan_amount_balance", $previous_transaction['loan_amount_balance']);
        $this->db->where("id", $loan_id);
        $this->db->update("coop_loan");
      } else {
        //is not latest
        die("Err loan" . $loan_transaction['loan_transaction_id'] . " || " . $latest_transaction['loan_transaction_id']);
      }
    }

    // var_dump($latest_transaction);
  }

  public function rollback_loan_atm($finance_transaction_row = array())
  {
  }

  public function rollback_fund($finance_transaction_row = array())
  {
  }

  public function rollback_non_pay_loan($finance_transaction_row = array(), $non_pay_id = "")
  {
    $principal        = $finance_transaction_row['principal'];
    $interest         = $finance_transaction_row['interest'];
    if ($principal > 0) {
      $non_pay_detail   = $this->db->get_where("coop_non_pay_detail", array(
        "non_pay_id"    => $non_pay_id,
        "loan_id"       => $finance_transaction_row['loan_id'],
        "pay_type"      => "principal",
        "deduct_code"   => "LOAN"
      ))->result_array()[0];
      $this->db->set("non_pay_amount_balance = (non_pay_amount_balance+" . $principal . ")");
      $this->db->where("run_id", $non_pay_detail['run_id']);
      $this->db->where("non_pay_id", $non_pay_detail['non_pay_id']);
      $this->db->update("coop_non_pay_detail");
    }

    if ($interest > 0) {
      $non_pay_detail   = $this->db->get_where("coop_non_pay_detail", array(
        "non_pay_id"    => $non_pay_id,
        "loan_id"       => $finance_transaction_row['loan_id'],
        "pay_type"      => "interest",
        "deduct_code"   => "LOAN"
      ))->result_array()[0];
      $this->db->set("non_pay_amount_balance = (non_pay_amount_balance+" . $interest . ")");
      $this->db->where("run_id", $non_pay_detail['run_id']);
      $this->db->where("non_pay_id", $non_pay_detail['non_pay_id']);
      $this->db->update("coop_non_pay_detail");
    }
  }

  public function rollback_non_pay_share($finance_transaction_row = array(), $non_pay_id = "")
  {
    $principal        = $finance_transaction_row['principal_payment'];
    $non_pay_detail   = $this->db->get_where("coop_non_pay_detail", array(
      "non_pay_id"    => $non_pay_id,
      "pay_type"      => "principal",
      "deduct_code"   => "SHARE"
    ))->result_array()[0];
    $this->db->set("non_pay_amount_balance", "(non_pay_amount_balance+" . $principal . ")", false);
    $this->db->where("run_id", $non_pay_detail['run_id']);
    $this->db->where("non_pay_id", $non_pay_detail['non_pay_id']);
    $this->db->update("coop_non_pay_detail");
  }

  public function is_loan($finance_transaction_row = array())
  {
    if ($finance_transaction_row['loan_id'] != "") return true;
    return false;
  }

  public function is_loan_atm($finance_transaction_row = array())
  {
    if ($finance_transaction_row['loan_atm_id'] != "") return true;
    return false;
  }

  public function is_share($finance_transaction_row = array())
  {
    $share = $this->db->get_where("coop_mem_share", array(
      "share_bill"          => $finance_transaction_row['receipt_id'],
      "share_early_value"   => $finance_transaction_row['principal_payment']
    ))->result_array();
    if (!empty($share)) return true;
    return false;
  }

  public function is_deposit($finance_transaction_row = array())
  {
    $this->db->join("coop_maco_account as t2", "t1.account_id = t2.account_id", "inner");
    $deposit  = $this->db->get_where("coop_account_transaction as t1", array(
      "mem_id"            => $finance_transaction_row['member_id'],
      "receipt_id"        => $finance_transaction_row['receipt_id']
    ))->result_array()[0];
    if (!empty($deposit)) return true;
    return false;
  }

  public function is_fund($finance_transaction_row = array())
  {
    if ($finance_transaction_row['account_list_id'] == 34) return true;
    return false;
  }
}
