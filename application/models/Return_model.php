<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Return_model extends ci_model
{
    public function __construct(){
        parent::__construct();
        $this->load->model("Finance_libraries", "Finance_libraries");
        $this->load->model("Cashier_loan_model", "cashier_loan");
        $this->load->model('Atm_calculator_model', 'ATMCalc');
    }

    public function returnAfterKeepMonthly(){

    }

    public function getListAfterKeepMonthly($begin, $end){
        $begin = date("Y-m-d", strtotime($begin));
        $end = date("Y-m-d", strtotime($end ." - 1 day"));
        $last_day = date("Y-m-t", strtotime($begin));
        $stmt = "SELECT T.member_id,
T.receipt_id,
T.deduct_code,
T.loan_id,
T.loan_atm_id,
T.principal_payment,
T.interest,
T.payment_date,
L.loan_type,
CONCAT(P.prename_short, M.firstname_th, \" \",M.lastname_th) as member_name,
IF(T.deduct_code = 'LOAN', L.contract_number, IF(T.deduct_code = 'ATM', A.contract_number, T.member_id)) as contract_number,
Year(T.payment_date) as payment_date_year,
Month(T.payment_date) as payment_date_month
FROM (
	SELECT 
		t1.member_id,
		t1.receipt_id,
		t1.payment_date,
		IF(t1.account_list_id=14 or t1.account_list_id=16, \"SHARE\", IF(t1.account_list_id=30, \"DEPOSIT\", IF(t1.loan_id is not null and t1.loan_id <> '', \"LOAN\", IF(t1.loan_atm_id is not null and t1.loan_atm_id <> '', \"ATM\", account_list_id)))) as `deduct_code`,
		IF(t1.loan_id is not null and t1.loan_id <> '', t1.loan_id, IF(t1.loan_atm_id is not null and t1.loan_atm_id <> '', t1.loan_atm_id, t1.member_id)) as id,
	  t1.loan_id,
		t1.loan_atm_id,
		SUM(t1.principal_payment) principal_payment,
		SUM(t1.interest) interest,
		SUM(t1.total_amount) total_amount,
		t1.loan_amount_balance
	FROM coop_finance_transaction t1
	LEFT JOIN coop_receipt t2 ON t1.receipt_id=t2.receipt_id
	WHERE t1.payment_date BETWEEN '{$last_day} 00:00:00' AND '{$last_day} 23:59:59'
	AND (UPPER(t1.receipt_id) LIKE '%B%' OR UPPER(t1.receipt_id) LIKE '%C%') AND (receipt_status = '0' or receipt_status is null)
	GROUP BY t1.member_id, t1.loan_id, t1.loan_atm_id
) T
INNER JOIN (
	SELECT 
		t1.member_id,
		IF(t1.account_list_id=14 or t1.account_list_id=16, \"SHARE\", IF(t1.account_list_id=30, \"DEPOSIT\", IF(t1.loan_id is not null and t1.loan_id <> '', \"LOAN\", IF(t1.loan_atm_id is not null and t1.loan_atm_id <> '', \"ATM\", account_list_id)))) as `deduct_code`,
		IF(t1.loan_id is not null and t1.loan_id <> '', t1.loan_id, IF(t1.loan_atm_id is not null and t1.loan_atm_id <> '', t1.loan_atm_id, t1.member_id)) as id,
	  t1.loan_id,
		t1.loan_atm_id,
		SUM(t1.principal_payment) principal_payment,
		SUM(t1.interest) interest,
		SUM(t1.total_amount) total_amount,
		t1.loan_amount_balance
	FROM coop_finance_transaction t1
	LEFT JOIN coop_receipt t2 ON t1.receipt_id=t2.receipt_id
	WHERE t1.payment_date BETWEEN '{$begin} 00:00:00' AND '{$end} 23:59:59'
	AND (UPPER(t1.receipt_id) NOT LIKE '%B%' OR UPPER(t1.receipt_id) NOT LIKE '%C%') AND (receipt_status = '0' or receipt_status is null)
	GROUP BY t1.member_id, t1.loan_id, t1.loan_atm_id
) Y ON T.member_id=Y.member_id AND T.deduct_code=Y.deduct_code AND T.id=Y.id
INNER JOIN coop_mem_apply M ON T.member_id=M.member_id
LEFT JOIN coop_prename P ON M.prename_id=P.prename_id
LEFT JOIN coop_loan L ON T.loan_id=L.id
LEFT JOIN coop_loan_atm A ON T.loan_atm_id=A.loan_atm_id
GROUP BY T.member_id, T.receipt_id, T.loan_id, T.loan_id, T.deduct_code
ORDER BY T.member_id";

        return $this->db->query($stmt)->result_array();
    }

    public function payback($payback, $data = array()){
        $_exp = explode("#", $payback);
        $type = $_exp[0];
        $id = $_exp[1];
        if(strtoupper($type) == "LOAN_ATM" || strtoupper($type) == "ATM"){
            //do something
            $data["loan_atm_id"] = $id;
            self::paybackATM($data);
            return array();
        }else if(strtoupper($type) === "LOAN"){
            $data["loan_id"] = $id;
            self::paybackLoan($data);
            return array();
        }else if(strtoupper($type) === "SHARE"){
            self::paybackShare($data);
            return array();
        }
    }

    public function paybackLoan($data = array()){

        $account_list_id = 15;
        $loan_id = $data['loan_id'];

        $receipt = self::receipt($data['date']);
        $receipt_number = $receipt->receipt_number;
        self::saveReceipt($data, $receipt);

        $row_loan = $this->getloan($loan_id);

        $transaction_text = $this->Finance_libraries->generate_loan_receipt_text_cashier($loan_id, $account_list_id);

        $cal_interest = array();
        $cal_interest['loan_id'] = $loan_id;
        $cal_interest['entry_date'] = $data['date'];
        $cal_interest['loan_type'] = $this->LoanCalc->get_loan_type($loan_id);
        $arr_interest = $this->LoanCalc->calc("PL", $cal_interest);

        $principal = round($data['total'] - $arr_interest['interest_arrear_bal'], 2, PHP_ROUND_HALF_UP);
        $interest = $arr_interest['interest_arrear_bal'];
        $loan_amount_balance = @$row_loan['loan_amount_balance'] - $principal;

        $loan_transaction = array();
        $loan_transaction['loan_id'] = $loan_id;
        $loan_transaction['loan_amount_balance'] = $loan_amount_balance;
        $loan_transaction['transaction_datetime'] = $data['date'];
        $loan_transaction['receipt_id'] = $receipt_number;
        $loan_transaction['interest'] =$interest;
        $this->loan_libraries->loan_transaction_arrears('PL',$loan_transaction);

        if ($loan_amount_balance <= 0) {
            $loan_amount_balance = 0;
            $data_insert = array();
            $data_insert['loan_amount_balance'] = $loan_amount_balance;
            $data_insert['loan_status'] = '4';
            $this->db->where('id', $loan_id);
            $this->db->update('coop_loan', $data_insert);
        } else {
            $data_insert = array();
            $data_insert['loan_amount_balance'] = number_format($loan_amount_balance, 2, '.', '');
            $this->db->where('id', $loan_id);
            $this->db->update('coop_loan', $data_insert);
        }

        $data_insert = array();
        $data_insert['member_id'] = $data['member_id'];
        $data_insert['receipt_number'] = $receipt_number;
        $data_insert['loan_id'] = $loan_id;
        $data_insert['deduct_type'] = 'all';
        $data_insert['account_list_id'] = $account_list_id;
        $data_insert['principal'] = $principal;
        $data_insert['interest'] = $interest;
        $data_insert['total'] = $data['total'];
        $data_insert['loan_amount_balance'] = $loan_amount_balance;
        $data_insert['date'] = $data['date'];
        $data_insert['transaction_text'] = $transaction_text;
        self::saveStatement($data_insert);
    }

    public function paybackATM($data){

        $account_list_id = 31;
        $loan_atm_id = $data['loan_atm_id'];

        $receipt = self::receipt($data['date']);
        $receipt_number = $receipt->receipt_number;
        self::saveReceipt($data, $receipt);

        $transaction_text = $this->Finance_libraries->generate_loan_atm_receipt_text_finance_month($loan_atm_id, $account_list_id, "principal" );

        $loan_amount_balance = $this->ATMCalc->getStmtPaymentLast($loan_atm_id, $data['date']);

        $cal_interest = array();
        $cal_interest['loan_atm_id'] = $loan_atm_id;
        $cal_interest['entry_date'] = $data['date'];
        $cal_interest['loan_type'] = $this->LoanCalc->get_loan_type($loan_atm_id);

        //echo "<pre>"; print_r($cal_interest); exit;
        $arr_interest = $this->ATMCalc->calc("PL", $cal_interest);

        $principal = round($data['total'] - $arr_interest['interest_arrear_bal'], 2, PHP_ROUND_HALF_UP);
        $interest = $arr_interest['interest_arrear_bal'];
        $loan_amount_balance = (double)$loan_amount_balance - $principal;

        $loan_atm_transaction = array();
        $loan_atm_transaction['loan_atm_id'] = $loan_atm_id;
        $loan_atm_transaction['transaction_datetime'] = $data['date'];
        $loan_atm_transaction['receipt_id'] = $receipt_number;
        $loan_atm_transaction['interest'] =  $interest;
        $loan_atm_transaction['principal'] = $principal;
        $loan_atm_transaction['action'] = 'payment';
        $this->loan_libraries->atm_transaction_arrears("PL", $loan_atm_transaction);

        $this->ATMCalc->balanceAtmDetail($loan_atm_id, $principal);

        $data_insert = array();
        $data_insert['member_id'] = $data['member_id'];
        $data_insert['receipt_number'] = $receipt_number;
        $data_insert['loan_atm_id'] = $loan_atm_id;
        $data_insert['deduct_type'] = 'all';
        $data_insert['account_list_id'] = $account_list_id;
        $data_insert['principal'] = $principal;
        $data_insert['interest'] = $interest;
        $data_insert['total'] = $data['total'];
        $data_insert['loan_amount_balance'] = $loan_amount_balance;
        $data_insert['date'] = $data['date'];
        $data_insert['transaction_text'] = $transaction_text;
        self::saveStatement($data_insert);
    }

    public function paybackShare($data){

        $account_list_id = 31;
        $member_id = $data['member_id'];
        $return_amount = $data['pin'];
        $transaction_text = 'หุ้น';


        $receipt = self::receipt($data['date']);
        $receipt_number = $receipt->receipt_number;
        self::saveReceipt($data, $receipt);

        $this->db->select('setting_value');
        $this->db->from('coop_share_setting');
        $this->db->order_by('setting_id DESC');
        $share_setting = $this->db->get()->row_array();

        $this->db->select('share_collect,share_collect_value');
        $this->db->from('coop_mem_share');
        $this->db->where("member_id = '".$member_id."' AND share_status = '1'");
        $this->db->order_by('share_date DESC, share_id DESC');
        $this->db->limit(1);
        $row_share = $this->db->get()->row_array();

        $data_insert = array();
        $data_insert['member_id'] = $member_id;
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['share_type'] = 'SPA';
        $data_insert['share_date'] = @$data['date'];
        $data_insert['share_payable'] = @$row_share['share_collect'];
        $data_insert['share_payable_value'] = @$row_share['share_collect_value'];
        $data_insert['share_early'] = @$return_amount/@$share_setting['setting_value'];
        $data_insert['share_early_value'] = @$return_amount;
        $data_insert['share_collect'] = @$row_share['share_collect'] + (@$return_amount/@$share_setting['setting_value']);
        $data_insert['share_collect_value'] = @$row_share['share_collect_value']+ @$return_amount;
        $data_insert['share_value'] = @$share_setting['setting_value'];
        $data_insert['share_status'] = '1';
        $data_insert['pay_type'] = '0';
        $data_insert['share_bill'] = @$receipt_number;
        $this->db->insert('coop_mem_share', $data_insert);


        $data_insert = array();
        $data_insert['member_id'] = $data['member_id'];
        $data_insert['receipt_number'] = $receipt_number;
        $data_insert['deduct_type'] = 'all';
        $data_insert['account_list_id'] = $account_list_id;
        $data_insert['principal'] = $return_amount;
        $data_insert['total'] = $return_amount;
        $data_insert['loan_amount_balance'] = @$row_share['share_collect_value']+ @$return_amount;
        $data_insert['date'] = $data['date'];
        $data_insert['transaction_text'] = $transaction_text;
        self::saveStatement($data_insert);

    }

    public function receipt($date){
        //get receipt setting data
        $receipt_format = 1;
        $receipt_finance_setting = $this->db->select("*")->from("coop_setting_finance")->where("name = 'receipt_cashier_format' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        if(!empty($receipt_finance_setting)) {
            $receipt_format = $receipt_finance_setting['value'];
        }

        if($receipt_format == 1) {
            $yymm = (date("Y", strtotime($date)) + 543) . date("m", strtotime($date));
            $mm = date("m", strtotime($date));
            $yy = (date("Y", strtotime($date)) + 543);
            $yy_full = (date("Y", strtotime($date)) + 543);
            $yy = substr($yy, 2);
            $this->db->select('*');
            $this->db->from('coop_receipt');
            $this->db->where("receipt_id LIKE '" . $yy_full . $mm . "%'");
            $this->db->order_by("receipt_id DESC");
            $this->db->limit(1);
            $row = $this->db->get()->result_array();

            if (!empty($row)) {
                $id = (int)substr($row[0]["receipt_id"], 6);
                $receipt_number = $yymm . sprintf("%06d", $id + 1);
            } else {
                $receipt_number = $yymm . "000001";
            }
        } else {
            $receipt_number = $this->Finance_libraries->generate_cashier_receipt_id($receipt_format, $date);
        }
        return (object) array("receipt_number" => $receipt_number, "order_by" => $row["order_by"] + 1);
    }

    public function getloan($id){
        return $this->db->select(array('loan_amount_balance', 'contract_number'))->from('coop_loan')->where("id='{$id}'")->get()->row_array();
    }

    public function saveReceipt($data , $receipt){
        $receipt_number = $receipt->receipt_number;
        $data_insert = array();
        $data_insert['receipt_id'] = $receipt_number;
        $data_insert['member_id'] = $data['member_id']; //member_id
        $data_insert['order_by'] = $receipt->order_by;
        $data_insert['sumcount'] = number_format($data['total'], 2, '.', ''); //total
        $data_insert['receipt_datetime'] = $data['date']; //date
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['pay_type'] = $data["pay_type"] == "transfer" ? "1" : "0"; //paytype
        $this->db->insert('coop_receipt', $data_insert);

        $data_insert = array();
        $data_insert['receipt_id'] = $receipt_number;
        $data_insert['receipt_list'] = '16';
        $data_insert['receipt_count'] = number_format($data['total'], 2, '.', ''); // total
        $this->db->insert('coop_receipt_detail', $data_insert);
    }

    public function saveStatement($data = array()){
        //@end บันทึกข้อมูลดอกเบี้ยค้างชำระสะสม
        $data_insert = array();
        $data_insert['member_id'] = $data['member_id'];
        $data_insert['receipt_id'] = $data['receipt_number'];
        $data_insert['loan_id'] = $data['loan_id'];
        $data_insert['loan_atm_id'] = $data['loan_atm_id'];
        $data_insert['deduct_type'] = $data['deduct_type'];
        $data_insert['account_list_id'] = $data['account_list_id'];
        $data_insert['principal_payment'] = number_format($data['principal'], 2, '.', '');
        $data_insert['interest'] = number_format($data['interest'], 2, '.', '');
        $data_insert['total_amount'] = $data['total'];
        $data_insert['loan_amount_balance'] = number_format($data['loan_amount_balance'], 2, '.', '');
        $data_insert['payment_date'] = $data['date'];
        $data_insert['createdatetime'] = $data['date'];
        $data_insert['transaction_text'] = $data['transaction_text'];
        $this->db->insert('coop_finance_transaction', $data_insert);
    }

    public function get_atm_loan_detail($loan_atm_id){
        $this->db->select(array(
            'loan_id',
            'loan_amount_balance'
        ));
        $this->db->from('coop_loan_atm_detail');
        $this->db->where("loan_atm_id = '{$loan_atm_id}' AND loan_status = '0'");
        $this->db->order_by('loan_id ASC');
        return $this->db->get()->result_array();
    }

    public function get_loan_atm($loan_atm_id){
        $this->db->select(array(
            'loan_atm_id',
            'total_amount_approve',
            'total_amount_balance',
            'contract_number'
        ));
        $this->db->from('coop_loan_atm');
        $this->db->where("loan_atm_id = '{$loan_atm_id}'");
        return $this->db->get()->row_array();
    }

    public function getShareReturn($year, $month){
        return $this->db->where(array("loan_id" => "", "loan_atm_id" => "", "return_year" => $year, "return_month" => $month))
            ->get("coop_process_return")->result_array();
    }


    public function getLoanList($member_id, $begin, $end){
        return $this->db->where("member_id='{$member_id}' and payment_date between '{$begin}' and '{$end}'")
            ->get("coop_finance_transaction")->result_array();
    }
    
    public function save_tranaaction_deposit($data){
        //กรณ๊เลือกแบบโอนแล้วโอนเงินเข้าบัญชีภายในสหกรณ์

        if($data['pay_type']==1){ //ตรวจสอบก่อนว่าเลือกการชำระเงินแบบโอนหรือไม่
            $account_id = $this->db->select(array('transaction_balance'))
            ->where(array('account_id' => $data['account_id']))
            ->order_by("transaction_time DESC, transaction_id DESC")
            ->get("coop_account_transaction", 1)
            ->row_array();
            $transaction_withdrawal=0;
            $transaction_deposit=$data['return_principal'];
            $transaction_balance=$account_id['transaction_balance']+$data['return_principal'];
            
            $data_insert['transaction_time'] = $data['return_date'];
            $data_insert['transaction_list'] = 'TRP';
            $data_insert['transaction_withdrawal'] =$transaction_withdrawal;
            $data_insert['transaction_deposit'] = $transaction_deposit;
            $data_insert['transaction_balance'] = $transaction_balance;
            $data_insert['account_id'] = $data['account_id'];
            $data_insert['user_id'] =  $_SESSION['USER_ID'];
            $data_insert['transaction_text'] = 'return_manual_tranfer_account_deposit';
            $this->db->insert('coop_account_transaction',$data_insert);
        }

         //คืนเงิน เงินฝาก หักจากสัญญาบัญชีที่เลือก
            $account_return = $this->db->select(array('transaction_balance'))
            ->where(array('account_id' => $data['account_return']))
            ->order_by("transaction_time DESC, transaction_id DESC")
            ->get("coop_account_transaction", 1)
            ->row_array();
            $transaction_withdrawal=$data['return_principal'];
            $transaction_deposit=0;
            $transaction_balance=$account_return['transaction_balance']-$data['return_principal'];

            $data_insert['transaction_time'] = $data['return_date'];
            $data_insert['transaction_list'] = 'TRP';
            $data_insert['transaction_withdrawal'] =$transaction_withdrawal;
            $data_insert['transaction_deposit'] = $transaction_deposit;
            $data_insert['transaction_balance'] = $transaction_balance;
            $data_insert['account_id'] = $data['account_return'];
            $data_insert['user_id'] =  $_SESSION['USER_ID'];
            $data_insert['transaction_text'] = 'return_manual_tranfer_account_deposit';
            $this->db->insert('coop_account_transaction',$data_insert);

    }

    public function returnATM($data = array()){
        extract($data);
        if(!isset($member_id, $loan_atm_id, $return_date, $return_principal, $return_interest, $receipt_id, $bill_id)){

            echo __METHOD__." require variable not set. place check again array";
            echo "<pre>"; print_r($data);
            exit;
        }

        $return_amount = $return_principal+$return_interest;

        $sql = "INSERT INTO coop_process_return(member_id, loan_id, loan_atm_id, return_type, pay_type, account_id, receipt_id, bill_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id)
							VALUES('{$member_id}', '{$loan_id}', '{$loan_atm_id}', '3', '4', '', '{$receipt_id}', '{$bill_id}', {$return_principal}, {$return_interest},  '{$return_amount}', YEAR('{$return_date}'), MONTH('{$return_date}'), '{$return_date}' ,'{$_SESSION['USER_ID']}')";
        @$this->db->query($sql);

        $row_trans_atm = $this->db->select(array('loan_amount_balance'))
            ->where("loan_atm_id = '{$loan_atm_id}'")
            ->order_by("loan_atm_transaction_id DESC")
            ->get("coop_loan_atm_transaction", 1)
            ->row_array();

        $loan_amount_balance = (double)$row_trans_atm['loan_amount_balance'];

        $loan_atm_transaction = array();
        $loan_atm_transaction['loan_atm_id'] = $loan_atm_id;
        $loan_atm_transaction['transaction_datetime'] = $return_date;
        $loan_atm_transaction['receipt_id'] = $bill_id;
        $loan_atm_transaction['interest'] =  $return_interest;
        $loan_atm_transaction['principal'] = $return_principal;

        $this->loan_libraries->atm_transaction_arrears("RM", $loan_atm_transaction);
        $loan_amount_balance += $return_principal;

        $this->ATMCalc->balanceAtmDetail($loan_atm_id, $loan_amount_balance);
    }

    public function returnLoan($data = array()){
        extract($data);
        if(!isset($member_id, $loan_id, $return_date, $return_principal, $return_interest, $receipt_id, $bill_id)){
            echo __METHOD__." require variable not set. place check again array";
            echo "<pre>"; print_r($data);
            exit;
        }

        $return_amount = $return_principal+$return_interest;

        $sql = "INSERT INTO coop_process_return(member_id, loan_id, loan_atm_id, return_type, pay_type, account_id, receipt_id, bill_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id)
							VALUES('{$member_id}', '{$loan_id}', '{$loan_atm_id}', '3', '4', '', '{$receipt_id}', '{$bill_id}', {$return_principal}, {$return_interest}, {$return_amount}, YEAR('{$return_date}'), MONTH('{$return_date}'), '{$return_date}' ,'{$_SESSION['USER_ID']}')";
        @$this->db->query($sql);

        $sql = "SELECT loan_amount_balance
								FROM coop_loan_transaction
								WHERE loan_id = '{$loan_id}'
								ORDER BY loan_transaction_id DESC
								LIMIT 1";
        $rs_trans_loan = $this->db->query($sql);
        $row_trans_loan = $rs_trans_loan->row_array();
        $loan_amount_balance = (double)$row_trans_loan['loan_amount_balance'];

        $loan_amount_balance += $return_principal;

        $loan_transaction = array();
        $loan_transaction['loan_id'] = $loan_id;
        $loan_transaction['transaction_datetime'] = $return_date;
        $loan_transaction['receipt_id'] = $bill_id;
        $loan_transaction['interest'] =  $return_interest;
        $loan_transaction['principal'] = $return_principal;
        $this->loan_libraries->loan_transaction_arrears("RM", $loan_transaction);

        $this->db->set('loan_amount_balance', $loan_amount_balance);
        $this->db->where('id', $loan_id);
        $this->db->update('coop_loan');
    }

    public function return_share_statement($data = array()){
        extract($data);
        if(!isset($member_id,$return_date, $return_principal, $return_interest, $receipt_id, $bill_id)){
            echo __METHOD__." :: require variable not set. place check again array";
            echo "<pre>"; print_r($data);
            exit;
        }

        $return_amount = $return_principal+$return_interest;

        $sql = "INSERT INTO coop_process_return(member_id, loan_id, loan_atm_id, return_type, pay_type, account_id, receipt_id, bill_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id)
							VALUES('{$member_id}', '{$loan_id}', '{$loan_atm_id}', '3', '4', '', '{$receipt_id}', '{$bill_id}', {$return_principal}, {$return_interest}, {$return_amount}, YEAR('{$return_date}'), MONTH('{$return_date}'), '{$return_date}' ,'{$_SESSION['USER_ID']}')";
        @$this->db->query($sql);

        $this->db->select('setting_value');
        $this->db->from('coop_share_setting');
        $this->db->order_by('setting_id DESC');
        $row = $this->db->get()->result_array();
        $share_setting = $row[0];

        $this->db->select('share_collect,share_collect_value');
        $this->db->from('coop_mem_share');
        $this->db->where("member_id = '".$member_id."' AND share_status = '1'");
        $this->db->order_by('share_date DESC, share_id DESC');
        $this->db->limit(1);
        $row_share = $this->db->get()->result_array();
        $row_share = @$row_share[0];

        $data_insert = array();
        $data_insert['member_id'] = $member_id;
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['share_type'] = 'RM';
        $data_insert['share_date'] = @$return_date;
        $data_insert['share_payable'] = @$row_share['share_collect'];
        $data_insert['share_payable_value'] = @$row_share['share_collect_value'];
        $data_insert['share_early'] = @$return_principal/@$share_setting['setting_value'];
        $data_insert['share_early_value'] = @$return_principal;
        $data_insert['share_collect'] = @$row_share['share_collect'] - (@$return_principal/@$share_setting['setting_value']);
        $data_insert['share_collect_value'] = @$row_share['share_collect_value']-@$return_principal;
        $data_insert['share_value'] = @$share_setting['setting_value'];
        $data_insert['share_status'] = '1';
        $data_insert['pay_type'] = '0';
        $data_insert['share_bill'] = @$bill_id;
        $this->db->insert('coop_mem_share', $data_insert);
    }

    public function executeReturn($data = array()){
        if($data['deduct_code'] == "SHARE"){
            self::return_share_statement($data);
        }else if($data['deduct_code'] == "ATM"){
            self::returnATM($data);
        }else if($data['deduct_code'] == "LOAN"){
            self::returnLoan($data);
        }else if($data['deduct_code'] == "DEPOSIT"){
            self::save_tranaaction_deposit($data);
        }
    }

}
