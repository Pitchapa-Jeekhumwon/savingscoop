<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update_stament_libbraries extends CI_Model {
	public function __construct()
	{
		parent::__construct();

		$this->menu_path_stack = array();
		$this->is_menu_path_found = FALSE;
    }

    public function update_deposit_transaction($account_id, $transaction_time){
        $this->db->where("transaction_time < '$transaction_time'");
        $this->db->order_by("transaction_time", "ASC");
        $this->db->order_by("transaction_id", "ASC");
        $query = $this->db->get_where("coop_account_transaction", array("account_id" => $account_id) );
        $num_rows = $query->num_rows();
        if($num_rows > 0) {
            foreach ($query->result() as $key => $value) {
                $sql = "UPDATE coop_account_transaction AS main1
            SET transaction_balance = (
                SELECT
                    transaction_balance
                FROM
                    (
                        SELECT
                            *
                        FROM
                            coop_account_transaction AS tmp1 WHERE account_id = '$account_id'
                    ) AS t1
                WHERE t1.account_id = main1.account_id and t1.transaction_time < main1.transaction_time and t1.transaction_id != main1.transaction_id
                ORDER BY transaction_time DESC, transaction_id DESC
                LIMIT 1
            ) + transaction_deposit - transaction_withdrawal
            WHERE
                (
                    account_id = '$account_id'
                    AND transaction_time >= '$transaction_time'
                )";
                // echo "<br><br>".$sql;
                $this->db->query($sql);
            }
        }
	}

	public function update_share_transaction($member_id, $share_start_date,$status_json=''){
		
        $this->db->where("share_date < '$share_start_date'");
        $this->db->where("member_id", $member_id);
        $this->db->order_by("share_date", "DESC");
        $this->db->order_by("share_id", "DESC");
        $last_balance = $this->db->get("coop_mem_share")->result()[0];
        if(!$last_balance){
            return null;
        }

        // SPA + 
        // SPM + 
        // SPL +
        // SRF -
        // SPD -
        // RM -
        $this->db->where("member_id", $member_id);
        $this->db->where("share_date >= '$share_start_date'");
        $this->db->order_by("share_date", 'ASC');
        $this->db->order_by("share_id", 'ASC');
        $row = $this->db->get("coop_mem_share")->result_array();

        $_increase = array('SPA', 'SPM', 'SPL', 'SDP', 'SB');
        $_decrease = array('SRF', 'SPD', 'RM', 'SRP');
        $num = 0;
        $data_update = array();

        foreach ($row as $key => $value) {

            if(in_array($value['share_type'], $_increase)) {
                $data_update[$num]['share_payable']       = $last_balance->share_payable          +   $value['share_early'];
                $data_update[$num]['share_payable_value'] = $last_balance->share_payable_value    +   $value['share_early_value'];
                $data_update[$num]['share_collect']       = $last_balance->share_collect          +   $value['share_early'];
                $data_update[$num]['share_collect_value'] = $last_balance->share_collect_value    +   $value['share_early_value'];
                $data_update[$num]['share_id'] = $value['share_id'];
            }else if(in_array($value['share_type'], $_decrease)){
                $data_update[$num]['share_payable']       = $last_balance->share_payable          -   $value['share_early'];
                $data_update[$num]['share_payable_value'] = $last_balance->share_payable_value    -   $value['share_early_value'];
                $data_update[$num]['share_collect']       = $last_balance->share_collect          -   $value['share_early'];
                $data_update[$num]['share_collect_value'] = $last_balance->share_collect_value    -   $value['share_early_value'];
                $data_update[$num]['share_id'] = $value['share_id'];
            }
			
            if($value['share_status'] == 1){
                $last_balance->share_payable            =    $data_update[$num]['share_payable'];
                $last_balance->share_payable_value      =    $data_update[$num]['share_payable_value'];
                $last_balance->share_collect            =    $data_update[$num]['share_collect'];
                $last_balance->share_collect_value      =    $data_update[$num]['share_collect_value'];
            }
            $num++;

        }

        $this->db->update_batch("coop_mem_share", $data_update, "share_id");
        unset($data_update);
        if($status_json != null){
            echo "success"; exit;
        }
	}

	public function update_loan_transaction($loan_id, $update_start_time){
        $this->db->where("transaction_datetime < '$update_start_time'");
        $this->db->where("loan_id", $loan_id);
        $this->db->order_by("transaction_datetime", "DESC");
        $this->db->order_by("loan_transaction_id", "DESC");
        $last_balance = $this->db->get("coop_loan_transaction")->result()[0];
        if(!$last_balance){
            return null;
        }

        
        $this->db->where("loan_id", $loan_id);
        $this->db->where("transaction_datetime >= '$update_start_time'");
        $this->db->order_by("transaction_datetime", "ASC");
        $this->db->order_by("loan_transaction_id", "ASC");
        $query = $this->db->get("coop_loan_transaction");
        foreach ($query->result() as $key => $value) {
            $finance_transaction = $this->get_finance_transaction($value->receipt_id, $loan_id, null);
            if($finance_transaction){
                $data_update = array();
                $data_update['loan_amount_balance'] = $last_balance->loan_amount_balance - $finance_transaction->principal_payment;
                $last_balance->loan_amount_balance  = $data_update['loan_amount_balance'];

                $this->db->where("loan_transaction_id", $value->loan_transaction_id);
                $this->db->update("coop_loan_transaction", $data_update);
            }

        }
    }

    public function update_loan_atm_transaction($loan_atm_id, $update_start_time){
        $this->db->where("transaction_datetime < '$update_start_time'");
        $this->db->where("loan_atm_id", $loan_atm_id);
        $this->db->order_by("transaction_datetime", "DESC");
        $this->db->order_by("loan_atm_transaction_id", "DESC");
        $last_balance = $this->db->get("coop_loan_atm_transaction")->result()[0];
        if(!$last_balance){
            return null;
        }

        
        $this->db->where("loan_atm_id", $loan_atm_id);
        $this->db->where("transaction_datetime >= '$update_start_time'");
        $this->db->order_by("transaction_datetime", "ASC");
        $this->db->order_by("loan_atm_transaction_id", "ASC");
        $query = $this->db->get("coop_loan_atm_transaction");
        foreach ($query->result() as $key => $value) {
            $finance_transaction = $this->get_finance_transaction($value->receipt_id, null, $loan_atm_id);
            if($finance_transaction){
                $data_update = array();
                $data_update['loan_amount_balance'] = $last_balance->loan_amount_balance - $finance_transaction->principal_payment;
                $last_balance->loan_amount_balance  = $data_update['loan_amount_balance'];

                $this->db->where("loan_atm_transaction_id", $value->loan_atm_transaction_id);
                $this->db->update("coop_loan_atm_transaction", $data_update);
            }

        }
    }
    
    private function get_finance_transaction($receipt_id, $loan_id, $loan_atm_id){
        // $result = $this->db->get_where("coop_finance_transaction", array("receipt_id" => $receipt_id));
        $where = "receipt_id = '".$receipt_id."'";
        if(!empty($loan_id)) {
            $where .= " AND loan_id = '".$loan_id."'";
        }
        if(!empty($loan_atm_id)) {
            $where .= " AND loan_atm_id = '".$loan_atm_id."'";
        }
        $result = $this->db->select("*, SUM(principal_payment) as principal_payment, SUM(interest) as interest")
                            ->from("coop_finance_transaction")
                            ->where($where)
                            ->get()->row();
        return $result;
    }

    public function update_balance_statement($data = array()){

       if($data=="")
            exit;

        $date = $data['date'];
        $account_id = $data['account_id'];
        $this->db->order_by("transaction_time", "ASC");
        $this->db->order_by("transaction_id", "ASC");
		$this->db->where("transaction_time < '".$date."'");
        $this->db->where("account_id", $account_id);
        $query = $this->db->get("coop_account_transaction");
        if(!$query->result()){
            echo "ไม่อนุญาตให้อัพเดทรายการเริ่มต้นได้";
            exit;
        }
        $this->db->order_by("transaction_time", "ASC");
        $this->db->order_by("transaction_id", "ASC");
        $this->db->where("transaction_time >= '".$date."'");
        $this->db->where("account_id", $account_id);
        $query = $this->db->get("coop_account_transaction");
        $first = false;
        $last_balance = 0;
        if(!$query->result_array()){
                echo "ไม่สามารถอัพเดทได้ ตรวจสอบวันที่ให้ถูกต้อง";
                exit;
        }else{
            $this->db->order_by("transaction_time", "DESC");
            $this->db->order_by("transaction_id", "DESC");
            $this->db->limit(1);
            $sub_query = $this->db->get_where("coop_account_transaction", array(
                "account_id" => $account_id,
                "transaction_time < " => $date
            ));

            $last_transaction = $sub_query->result_array()[0];

            if(!$last_transaction){
                $this->db->order_by("transaction_time", "ASC");
                $this->db->order_by("transaction_id", "ASC");
                $this->db->limit(1);
                $sub_query = $this->db->get_where("coop_account_transaction", array(
                    "account_id" => $account_id,
                ));
                $last_transaction = $sub_query->result_array()[0];
            }
            $last_balance = $last_transaction['transaction_balance'];
            $last_no_on_balance = $last_transaction['transaction_no_in_balance'];
        }


        foreach ($query->result_array() as $key => $row) {

            if($row['transaction_list']=="BF"){
                $last_balance = $row['transaction_balance'];
                $last_no_on_balance = $row['transaction_no_in_balance'];
                $skip = false;
                continue;
            }

            if($skip && $row['transaction_list'] != "OPN"){
                $last_balance = $row['transaction_balance'];
                $last_no_on_balance = ($row['transaction_no_in_balance']=='') ? 0 : $row['transaction_no_in_balance'];
                $skip = false;
                continue;
            }else{
                $skip = false;
            }

            if($row['transaction_list'] == "OPN"){
                $last_balance = 0;
                $last_no_on_balance = 0;
            }



            $transaction_id = $row['transaction_id'];

            $new_balance = 0;
            $new_balance_no_in = 0;
            $deposit = $row['transaction_deposit'];
            $withdrawal = $row['transaction_withdrawal'];



            if($deposit!=0 && $withdrawal!=0){
                $new_balance = $last_balance + $deposit - $withdrawal;
                if(!in_array( $row['transaction_list'], ['IN', 'INT', 'WTI'] )){
                    $new_no_in_balance = $last_no_on_balance + $deposit - $withdrawal;
                }else{
                    $new_no_in_balance = $last_no_on_balance;
                }
            }else if($deposit!=0){
                $new_balance = $last_balance + $deposit;
                if(!in_array( $row['transaction_list'], ['IN', 'INT'] )){
                    $new_no_in_balance = $last_no_on_balance + $deposit;
                }else{
                    $new_no_in_balance = $last_no_on_balance;
                }
            }else if($withdrawal!=0){
                $new_balance = $last_balance - $withdrawal;
                if(!in_array( $row['transaction_list'], ['IN', 'INT', 'WTI'] )){
                    $new_no_in_balance = $last_no_on_balance - $withdrawal;
                }else{
                    $new_no_in_balance = $last_no_on_balance;
                }
            }

            // ---------------------------------------------------


            $this->db->set("transaction_balance", $new_balance);
            $this->db->set("transaction_no_in_balance", $new_no_in_balance);
            $this->db->where("transaction_id", $row['transaction_id']);
            $this->db->update("coop_account_transaction");

            $last_balance = $new_balance ;
            $last_no_on_balance = $new_no_in_balance ;
        }
    }

    public function Run_import_excel_update ($data = array()){
        if($data=="")
            exit;
        $date = $data['date'];
        $account_id = $data['account_id'];
        $this->db->order_by("transaction_time", "ASC");
        $this->db->order_by("transaction_id", "ASC");
        $this->db->where("transaction_time < '".$date."'");
        $this->db->where("account_id", $account_id);
        $query = $this->db->get("coop_account_transaction");
        if(!$query->result()){
            echo "ไม่อนุญาตให้อัพเดทรายการเริ่มต้นได้";
            exit;
        }
        $this->db->order_by("transaction_time", "ASC");
        $this->db->order_by("transaction_id", "ASC");
        $this->db->where("transaction_time >= '".$date."'");
        $this->db->where("account_id", $account_id);
        $query = $this->db->get("coop_account_transaction");
        $first = false;
        $last_balance = 0;

        if(!$query->result_array()){
            echo "ไม่สามารถอัพเดทได้ ตรวจสอบวันที่ให้ถูกต้อง";
            exit;
        }else{
            $this->db->order_by("transaction_time", "DESC");
            $this->db->order_by("transaction_id", "DESC");
            $this->db->limit(1);
            $sub_query = $this->db->get_where("coop_account_transaction", array(
                "account_id" => $account_id,
                "transaction_time < " => $date
            ));

            $last_transaction = $sub_query->result_array()[0];

            if(!$last_transaction){
                $this->db->order_by("transaction_time", "ASC");
                $this->db->order_by("transaction_id", "ASC");
                $this->db->limit(1);
                $sub_query = $this->db->get_where("coop_account_transaction", array(
                    "account_id" => $account_id,
                ));
                $last_transaction = $sub_query->result_array()[0];
            }
            $last_balance = $last_transaction['transaction_balance'];
            $last_no_on_balance = $last_transaction['transaction_no_in_balance'];
        }

        foreach ($query->result_array() as $key => $row) {
            if($row['transaction_list']=="BF"){
                $last_balance = $row['transaction_balance'];
                $last_no_on_balance = $row['transaction_no_in_balance'];
                $skip = false;
                continue;
            }

            if($skip && $row['transaction_list'] != "OPN"){
                $last_balance = $row['transaction_balance'];
                $last_no_on_balance = ($row['transaction_no_in_balance']=='') ? 0 : $row['transaction_no_in_balance'];
                $skip = false;
                continue;
            }else{
                $skip = false;
            }

            if($row['transaction_list'] == "OPN"){
                $last_balance = 0;
                $last_no_on_balance = 0;
            }
            $transaction_id = $row['transaction_id'];

            $new_balance = 0;
            $new_balance_no_in = 0;
            $deposit = $row['transaction_deposit'];
            $withdrawal = $row['transaction_withdrawal'];

            if($deposit!=0 && $withdrawal!=0){
                $new_balance = $last_balance + $deposit - $withdrawal;
                if(!in_array( $row['transaction_list'], ['IN', 'INT', 'WTI'] )){
                    $new_no_in_balance = $last_no_on_balance + $deposit - $withdrawal;
                }else{
                    $new_no_in_balance = $last_no_on_balance;
                }
            }else if($deposit!=0){
                $new_balance = $last_balance + $deposit;
                if(!in_array( $row['transaction_list'], ['IN', 'INT'] )){
                    $new_no_in_balance = $last_no_on_balance + $deposit;
                }else{
                    $new_no_in_balance = $last_no_on_balance;
                }
            }else if($withdrawal!=0){
                $new_balance = $last_balance - $withdrawal;
                if(!in_array( $row['transaction_list'], ['IN', 'INT', 'WTI'] )){
                    $new_no_in_balance = $last_no_on_balance - $withdrawal;
                }else{
                    $new_no_in_balance = $last_no_on_balance;
                }
            }

            // ---------------------------------------------------

            $this->db->set("transaction_balance", $new_balance);
            $this->db->set("transaction_no_in_balance", $new_no_in_balance);
            $this->db->where("transaction_id", $row['transaction_id']);
            $this->db->update("coop_account_transaction");

            $last_balance = $new_balance ;
            $last_no_on_balance = $new_no_in_balance ;

        }
    }


    public function update_transaction_balance($account_id, $date){
        ini_set("precision", 12);

        $this->db->order_by("transaction_time", "ASC");
        $this->db->order_by("transaction_id", "ASC");
        $this->db->where("transaction_time >= '".$date."'");
        $this->db->where("account_id", $account_id);
        $query = $this->db->get("coop_account_transaction");
        $first = false;
        $last_balance = 0;
        if(!$query->result_array()){

            $this->db->order_by("transaction_time", "ASC");
            $this->db->order_by("transaction_id", "ASC");
            $this->db->where("account_id", $account_id);
            $query = $this->db->get("coop_account_transaction");
            $first = true;
            // echo "ไม่มีรายการที่เลือก";
            // exit;

            $this->db->where("account_id", $account_id);
            $this->db->order_by("transaction_time", "ASC");
            $this->db->order_by("transaction_id", "ASC");
            $this->db->limit(1);
            $sub_query = $this->db->get("coop_account_transaction");

            $last_transaction = $sub_query->result_array()[0];
			//echo $this->db->last_query(); echo '<br>';
			//echo '<pre>'; print_r($last_transaction); echo '</pre>';
            if(!$last_transaction){
                echo "fail";
                exit;
            }

            $last_balance = $last_transaction['transaction_balance'];
            $last_no_on_balance = $last_transaction['transaction_no_in_balance'];


        }else{
            $this->db->order_by("transaction_time", "DESC");
            $this->db->order_by("transaction_id", "DESC");
            $this->db->limit(1);
            $sub_query = $this->db->get_where("coop_account_transaction", array(
                "account_id" => $account_id,
                "transaction_time < " => $date
            ));

            $last_transaction = $sub_query->result_array()[0];

            if(!$last_transaction){
                $this->db->order_by("transaction_time", "ASC");
                $this->db->order_by("transaction_id", "ASC");
                $this->db->limit(1);
                $sub_query = $this->db->get_where("coop_account_transaction", array(
                    "account_id" => $account_id,
                ));
                $last_transaction = $sub_query->result_array()[0];
            }
            $last_balance = $last_transaction['transaction_balance'];
            $last_no_on_balance = $last_transaction['transaction_no_in_balance'];
        }



        $is_first = self::fine_first_row($account_id, $date);
        foreach ($query->result_array() as $key => $row) {

            //echo $row['transaction_time']." :: ".$row['transaction_list']." :: ".$row['transaction_withdrawal']." :: ".$row['transaction_deposit']." :: ".$row['transaction_balance']." <br>";
            if($is_first){
                $is_first = false;
                if($row['transaction_list'] != "BF" || $row['transaction_list'] != "OPN"){
                    if($row['transaction_deposit'] > 0) {
                        $last_balance = round($row['transaction_balance'] - $row['transaction_deposit'], 2);
                        $last_no_on_balance = round($row['transaction_balance'] - $row['transaction_deposit'], 2);
                    }
                    if($row['transaction_withdrawal'] > 0){
                        $last_balance = round($row['transaction_balance'] + $row['transaction_withdrawal'], 2);
                        $last_no_on_balance = round($row['transaction_balance'] + $row['transaction_withdrawal'], 2);
                    }
                }
            }

            if($row['transaction_list']=="BF"){
                $last_balance = $row['transaction_balance'];
                $last_no_on_balance = $row['transaction_no_in_balance'];
                $skip = false;
                continue;
            }

            if($skip && $row['transaction_list'] != "OPN"){
                $last_balance = $row['transaction_balance'];
                $last_no_on_balance = ($row['transaction_no_in_balance']=='') ? 0 : $row['transaction_no_in_balance'];
                $skip = false;
                continue;
            }else{
                $skip = false;
            }

            if($row['transaction_list'] == "OPN" || $row['transaction_list'] == "OCA"){
                $last_balance = 0;
                $last_no_on_balance = 0;
            }

            $transaction_id = $row['transaction_id'];

            $new_balance = 0;
            $new_balance_no_in = 0;
            $deposit = $row['transaction_deposit'];
            $withdrawal = $row['transaction_withdrawal'];

            if($deposit!=0 && $withdrawal!=0){
                $new_balance = $last_balance + $deposit - $withdrawal;
                if(!in_array( $row['transaction_list'], ['IN', 'INT', 'WTI'] )){
                    $new_no_in_balance = $last_no_on_balance + $deposit - $withdrawal;
                }else{
                    $new_no_in_balance = $last_no_on_balance;
                }
            }else if($deposit!=0){
                $new_balance = $last_balance + $deposit;
                if(!in_array( $row['transaction_list'], ['IN', 'INT'] )){
                    $new_no_in_balance = $last_no_on_balance + $deposit;
                }else{
                    $new_no_in_balance = $last_no_on_balance;
                }
            }else if($withdrawal!=0){
                $new_balance = $last_balance - $withdrawal;
                if(!in_array( $row['transaction_list'], ['IN', 'INT', 'WTI'] )){
                    $new_no_in_balance = $last_no_on_balance - $withdrawal;
                }else{
                    $new_no_in_balance = $last_no_on_balance;
                }
            }else{
                $new_balance = $last_balance;
                $new_no_in_balance = $last_no_on_balance;
            }


            $this->db->set("transaction_balance", round($new_balance, 2));
            $this->db->set("transaction_no_in_balance", round($new_no_in_balance, 2));
            $this->db->where("transaction_id", $row['transaction_id']);
            $this->db->update("coop_account_transaction");

            $last_balance = $new_balance ;
            $last_no_on_balance = $new_no_in_balance ;
        }

        //echo "success";

    }

    private function fine_first_row($account_id, $date){
	     $rs = $this->db->select("transaction_time")->from("coop_account_transaction")->where(array(
	        'account_id' => $account_id
        ))->order_by("transaction_time, transaction_id", "ASC")->limit(1)->get()
            ->row_array();

	     return date('Y-m-d',strtotime($rs['transaction_time'])) === date('Y-m-d', strtotime($date));
    }

    public function update_accu_inter($data = array()){
        $account_id = $data['account_id']; 
		$transaction_time = $data['date'];  

		$affected_rows = 0;
		$sql2 = "	SELECT
						account_id,
						transaction_time,
						transaction_id,
						transaction_list,
						old_acc_int,
						accu_int_item
					FROM
						coop_account_transaction 
					WHERE
						transaction_time > '{$transaction_time}' 
						AND account_id = '{$account_id}'
					ORDER BY
						transaction_time ASC";
		$row2 = $this->db->query($sql2)->result_array();

		$old_acc_int = 0;
		if(!empty($row2)){
			foreach($row2 AS $key2=>$val2){
				//@start เรียกใช้ ข้อมูล ดอกเบี้ยสะสม
				$data_cal = array();
				$data_cal['account_id'] = $val2['account_id'];
				$data_cal['date_cal'] = $val2['transaction_time'];
				$data_cal['date_start_cal'] = $val2['transaction_time'];
		
				$data_cal_accu_int = $this->deposit_modal->cal_accu_int($data_cal);						
				$transaction_id = $val2['transaction_id'];
				
				if(in_array($val2['transaction_list'],array('INT','IN'))){
					$old_acc_int = 0;
					$data_cal_accu_int['old_acc_int'] = 0;
					$data_cal_accu_int['accu_int_item'] = 0;
				}
				
				if($key2 == 0){
					$old_acc_int = $data_cal_accu_int['old_acc_int'];
				}else{
					$old_acc_int += $data_cal_accu_int['accu_int_item'];
				}

				$old_acc_int = number_format($old_acc_int, 2, '.', '');
				$data_insert['old_acc_int'] = $old_acc_int;
				$data_insert['accu_int_item'] = $data_cal_accu_int['accu_int_item'];
				$this->db->where('transaction_id', $transaction_id);
				$this->db->update('coop_account_transaction', $data_insert);
				if($this->db->affected_rows()){
					$affected_rows++;
				}
			}
		}
		if($affected_rows > 0){
			echo json_encode(["result" => "success"]);
			exit;
		}else{
			echo json_encode(["result" => "error"]);
			exit;
		}
		exit;
    }
    
}
