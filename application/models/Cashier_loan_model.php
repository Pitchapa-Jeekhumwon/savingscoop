<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cashier_loan_model extends CI_Model {

	public function __construct()
	{
        parent::__construct();
        $this->load->model("Finance_libraries", "Finance_libraries");
    }

    public function get_cal_loan($data){

	    //echo "<pre>"; print_r($data); exit;

        $key = $data['key'];
        $fix_date = $data['fix_date'];
        $member_id = $data['member_id'];
        $mem_type = $data['mem_type'];
        $loan_id = $data['loan_id'];
        $loan_amount = $data['loan_amount_balance'];//เงินกู้
        $loan_type = $data['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย
        $curr_BE = date("Y") + 543;
        $finance_month = $this->db->select("*")
            ->from("coop_finance_month_detail as t1")
            ->join("coop_finance_month_profile as t2", "t1.profile_id = t2.profile_id")
            ->where("t1.loan_id = '".$loan_id."' AND t2.profile_year = '".$curr_BE."' AND t2.profile_month = '".date("m")."' AND t1.run_status = 1")
            ->get()->row();
        $interest_loan = 0;
        $alert_text = "";
        if(empty($finance_month)) {
            $data_interest_loan = $this->get_interest_loan($loan_id,$mem_type,$loan_amount, $loan_type, $member_id, $fix_date);
            ////if(@$_GET['dev']=='dev'){ echo '<pre>'; print_r($data_interest_loan); echo '</pre>';}
        }
        //echo '<pre>'; print_r($data_interest_loan); echo '</pre>'; exit;
        $arr_data['interest'] = ($data_interest_loan['interest'] > 0)?number_format($data_interest_loan['interest'],2, '.', ''):'0.00';
        $arr_data['alert'] = $data_interest_loan['alert'];
        $arr_data['data_cal_int'] = $data_interest_loan['data_cal_int'];
        //$data_interest_debt = $this->get_interest_debt($loan_id,$member_id);
        //$arr_data['loan_interest_debt_total'] = $data_interest_debt['loan_interest_debt_total']; //ดอกเบี้ยค้างชำระ
        //$arr_data['interest_non_pay'] = $data_interest_debt['interest_non_pay'];
        $arr_data['loan_interest_debt_total'] = $data_interest_loan['loan_interest_debt_total']; //ดอกเบี้ยค้างชำระ
        $arr_data['interest_non_pay'] = $data_interest_loan['interest_non_pay'];
        $arr_data['principal_finance_process'] = $this->get_finance_month($loan_id);
        $arr_data['refrain'] = $this->get_refrain($loan_id);
        return $arr_data;
    }

    public function get_interest_loan($loan_id,$mem_type,$loan_amount, $loan_type, $member_id, $fix_date){
        $result = array();
        $arr_data_cal_int = array();
        $profile_id = $this->db->select("*")
            ->from("coop_finance_month_detail AS t1")
            ->where("t1.loan_id = '".$loan_id."' AND run_status = 1")
            ->order_by("t1.profile_id DESC")
            ->limit(1)
            ->get()->result_array()[0]['profile_id'];
        $rs_date1 = $this->db->select("concat( (profile_year-543), '-',(profile_month + 1), '-01') as date1")
            ->from("coop_finance_month_profile")
            ->where("profile_id", $profile_id)
            ->limit(1)
            ->get()->result_array()[0]['date1'];
        $date_last_finance_month = @$rs_date1;//วันคิดดอกเบี้ยล่าสุด

        $date_last_finance_transaction = $this->db->select("payment_date")
            ->from("coop_finance_transaction")
            ->join("coop_receipt", "coop_finance_transaction.receipt_id = coop_receipt.receipt_id")
            ->where("coop_finance_transaction.loan_id = '".$loan_id."'")
            ->where("coop_finance_transaction.member_id = '".$member_id."'")
            ->where("coop_finance_transaction.receipt_id not like '%C%'")
            ->where("(coop_receipt.receipt_status != 2 OR coop_receipt.receipt_status is null)")
            ->order_by("payment_date desc")
            ->limit(1)
            ->get()->result_array()[0]['payment_date'];

        $coop_loan = $this->db->select("date(date_last_interest) as date_last_interest, date(approve_date) as date_transfer ")
            ->from("coop_loan")
            ->where("id", $loan_id)
            ->where("loan_status", 1)
            ->limit(1)
            ->get()->result_array()[0];
        $date_last_interest = 	$coop_loan['date_last_interest'];
        $date1 = $date_last_interest;

        if($date1=="")
            $alert_text = "ไม่มีวันที่คิดดอกเบี้ย ติดต่อผู้ดูแลระบบ.";

        $date2 = date("Y-m-d");
        if(isset($fix_date)){
            $tmp = explode("/", $fix_date);
            $date2 = ($tmp[2]-543)."-".$tmp[1]."-".$tmp[0];
        }

        if(@$_GET['debug'] != "" && @$_GET['loan_id']==$loan_id){
            echo $date1." <br>".$date2;
            exit;
        }

        if(@$_GET['dev']=='dev'){ echo 'date1='.$date1.' <br>date2='.$date2.' <br>'; }

        //หาดอกเบี้ย
        //$arr_interest_loan = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_type, $date1,$date2,true);
        //$arr_data_cal_int = $arr_interest_loan['cal_range'];
        //$interest_loan = $arr_interest_loan['interest_all'];

        //
        $cal_interest = array();
        $cal_interest['loan_id'] = $loan_id;
        $cal_interest['entry_date'] = $date2;
        $cal_interest['loan_type'] = $loan_type;
        $arr_interest = $this->LoanCalc->calc("PL", $cal_interest);
        $arr_data_cal_int = $arr_interest['data_cal_int'];
        $interest_loan = $arr_interest['interest_arrear_bal'];
        $interest_non_pay = $arr_interest['interest_arrears'];
        $loan_interest_debt_total = $arr_interest['interest_arrears'];

        /*--------------------------------------------------------
        =============คิดดอกเบี้ยยอดกู้เพิ่ม เงินกู้สามัญหมุนเวียน=============
        --------------------------------------------------------*/
        $repayment_interest = 0;
        $sql_ck = "SELECT * from coop_loan_repayment where loan_id = ".$loan_id;
        $query_ck = $this->db->query($sql_ck);
        if($query_ck->num_rows() > 0){
            $repayment_interest = $this->loan_libraries->calc_loan_repayment($loan_id, date("Y-m-")."01 00:00:00", date("Y-m-d")." 23:59:59");
        }
        /*-------------------------------------------------------*/
        //exit;
        $interest_loan = round($interest_loan + $repayment_interest,2);
        $result['interest'] = number_format($interest_loan,2, '.', '');
        $result['alert'] = $alert_text;
        $result['data_cal_int'] = $arr_data_cal_int;
        $result['interest_non_pay'] = $interest_non_pay;
        $result['loan_interest_debt_total'] = $loan_interest_debt_total;
        return $result;
    }
	
    public function get_interest_debt($loan_id,$member_id){		
		$result = array();
		//ข้อมูลดอกเบี้ยค้างชำระ
		//Get loan interest non pay
		$loan_interest_remain = $this->db->select("loan_id, SUM(non_pay_amount_balance) as sum")
											->from("coop_non_pay_detail")
											->where("loan_id = '".$loan_id."' AND pay_type = 'interest'")
											->get()->row();
		if(!empty($loan_interest_remain)) {
			$loan_interest_remain_total = @$loan_interest_remain->sum;
		}
		
		//ข้อมูลดอกเบี้ยค้างชำระสะสม
		$loan_interest_debt = $this->db->select("loan_id, SUM(interest_balance) AS sum_interest_balance")
										->from("coop_loan_interest_debt")
										->where("loan_id = '".$loan_id."' AND interest_status = 0 AND member_id = '".$member_id."'")
										->get()->row();

		if(!empty($loan_interest_debt)) {
			$loan_interest_debt_total = @$loan_interest_debt->sum_interest_balance;
		}		
		$result['loan_interest_debt_total'] = $loan_interest_debt_total;
		$interest_non_pay = round(@$loan_interest_remain_total+@$loan_interest_debt_total,2);
		$result['interest_non_pay'] = number_format(@$interest_non_pay,2, '.', '');
		return $result;        
    }

    public function get_finance_month($loan_id){
        $result = array();
        //ยอดเงินหลังประมวลผล
        //month_profile
        $this->db->select(array('t1.pay_amount','t2.profile_month','t2.profile_year'));
        $this->db->from('coop_finance_month_detail AS t1');
        $this->db->join("coop_finance_month_profile AS t2","t1.profile_id = t2.profile_id","left");
        $this->db->where("t1.loan_id = '".$loan_id."' AND t1.pay_type = 'principal' AND t1.deduct_code = 'LOAN' AND t1.run_status = '0'");
        $this->db->limit(1);
        $row_finance_process = $this->db->get()->row_array();
        $result = $row_finance_process['pay_amount'];
        return $result;
    }
		
    public function get_refrain($loan_id){
		$year = date("Y") + 543;
		$month = date("m");
		$loan_refrains = $this->db->select("refrain_loan_id, refrain_type")
									->from("coop_refrain_loan")
									->where("loan_id = '".$loan_id."' AND status != 2 AND (year_start < ".$year." || (year_start = ".$year." AND month_start <= ".$month."))
											 AND ((year_end > ".$year." || (year_end = ".$year." AND month_end >= ".$month.") || period_type = 2))")
									->get()->result_array();
		$loan_refrain = array();
		foreach($loan_refrains as $refrain_row) {
			if($refrain_row["refrain_type"] == 1) {
				$loan_refrain["principal"] = $refrain_row["refrain_loan_id"];
			} else if ($refrain_row["refrain_type"] == 2) {
				$loan_refrain["interest"] = $refrain_row["refrain_loan_id"];
			} else if ($refrain_row["refrain_type"] == 3) {
				$loan_refrain["principal"] = $refrain_row["refrain_loan_id"];
				$loan_refrain["interest"] = $refrain_row["refrain_loan_id"];
			}
		}
		return $loan_refrain;        
    }
	
    public function get_data_loan($loan_id,$member_id){
        $where = "id = '{$loan_id}'";
        if(!empty($member_id)) {
            $where .= " AND member_id = '".$member_id."'";
        }

        $row = $this->db->select(array(
                        'member_id',
                        'contract_number',
                        'approve_date',
                        'createdatetime as request_datetime',
                        'loan_amount',
                        'loan_amount_balance',
                        'loan_type',
                        'date_last_interest',
                        'contract_number',
                        'period_amount',
                        'money_per_period',
                        'loan_status',
                        'pay_type'
                    ))
                    ->from('coop_loan')
                    ->where($where." AND loan_status IN (1,8) AND transfer_status = 0")
                    ->limit(1)
                    ->get()->row_array();
        return $row;
    }

    public function get_loan_transaction_by_loan_id($loan_id) {
        $transactions = $this->db->select("t1.receipt_id,
                                            t1.loan_amount_balance,
                                            t1.transaction_datetime,
                                            SUM(t2.principal_payment) as principal,
                                            SUM(t2.interest) as interest,
                                            t2.period_count as period_count,
                                            t3.receipt_datetime,
                                            t3.cancel_date,
                                            t3.receipt_status,
                                            t3.finance_month_profile_id as profile_id")
                                    ->from("coop_loan_transaction as t1")
                                    ->join("coop_finance_transaction as t2", "t1.receipt_id = t2.receipt_id", "LEFT")
                                    ->join("coop_receipt as t3", "t1.receipt_id = t3.receipt_id", "LEFT")
                                    ->where("t1.loan_id = '".$loan_id."'")
                                    ->group_by("t1.receipt_id")
                                    ->get()->result_array();
        return $transactions;
    }

    public function get_guarantee_by_loan_id($loan_id) {
        $guarantees = $this->db->select("t3.prename_short, t2.firstname_th, t2.lastname_th")
                                ->from("coop_loan_guarantee_person as t1")
                                ->join("coop_mem_apply as t2", "t1.guarantee_person_id = t2.member_id", "inner")
                                ->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
                                ->where("t1.loan_id = '".$loan_id."'")
                                ->get()->result_array();
        return $guarantees;
    }

	public function get_cal_loan_atm($data){
		$fix_date = $data['fix_date'];
		$member_id = $data['member_id'];
		$loan_atm_id = $data['loan_id'];

		$date2 = date("Y-m-d");
		if(isset($fix_date)){
			$tmp = explode("/", $fix_date);
			$date2 = ($tmp[2]-543)."-".$tmp[1]."-".$tmp[0];
		}
		
		$row_detail = $this->db->select(array(
					't1.loan_id',
					't1.loan_amount_balance',
					't1.loan_date',
					't1.date_last_pay',
					't1.date_last_interest'
				))
				->from('coop_loan_atm_detail as t1')
				->where("
					t1.loan_atm_id = '".$loan_atm_id."' 
					AND t1.member_id = '".$member_id."' 
					AND t1.transfer_status = '1' 
					AND t1.loan_status = 0
				")
				->get()->result_array();
		$principal = 0;
		foreach($row_detail as $key_detail => $value_detail){
			@$principal += $value_detail['loan_amount_balance'];
		}
		
		$cal_atm_interest = array();
		$cal_atm_interest['loan_atm_id'] = $loan_atm_id;
		$cal_atm_interest['entry_date'] = $date2;
		//$cal_atm_interest['date_interesting'] = $date2;
		//$arr_atm_interest = $this->loan_libraries->cal_atm_interest_report_test($cal_atm_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false, true );
		$interest = $arr_atm_interest['interest_month'];

		$arr_data['principal'] = @$principal;
		$arr_data['interest'] = number_format($arr_atm_interest['interest_month'],2, '.', '');
		
		$data_interest_debt = $this->get_atm_interest_debt($loan_atm_id,$member_id);
		$arr_data['loan_interest_debt_total'] = $data_interest_debt['loan_interest_debt_total'];
		$arr_data['interest_non_pay'] = $data_interest_debt['interest_non_pay'];
		$arr_data['start_date_cal'] = $arr_atm_interest['start_date_cal'];
		$arr_data['end_date_cal'] = $arr_atm_interest['end_date_cal'];				
		return $arr_data;        
    }
	
	public function get_atm_interest_debt($loan_atm_id,$member_id){		
		$result = array();
		//ข้อมูลดอกเบี้ยค้างชำระ
		//Get loan interest non pay
		$loan_interest_remain = $this->db->select("loan_atm_id, SUM(non_pay_amount_balance) as sum")
											->from("coop_non_pay_detail")
											->where("loan_atm_id = '".$loan_atm_id."' AND pay_type = 'interest'")
											->get()->row();
		if(!empty($loan_interest_remain)) {
			$loan_interest_remain_total = @$loan_interest_remain->sum;
		}
		
		//ข้อมูลดอกเบี้ยค้างชำระสะสม
		$loan_interest_debt = $this->db->select("loan_atm_id, SUM(interest_balance) AS sum_interest_balance")
										->from("coop_loan_interest_debt")
										->where("loan_atm_id = '".$loan_atm_id."' AND interest_status = 0 AND member_id = '".$member_id."'")
										->get()->row();

		if(!empty($loan_interest_debt)) {
			$loan_interest_debt_total = @$loan_interest_debt->sum_interest_balance;
		}
		
		$result['loan_interest_debt_total'] = $loan_interest_debt_total;
		$interest_non_pay = round(@$loan_interest_remain_total+@$loan_interest_debt_total,2);
		$result['interest_non_pay'] = number_format(@$interest_non_pay,2, '.', '');
		return $result;        
    }
		
	public function get_data_loan_atm($loan_atm_id,$member_id){
		$row = $this->db->select(array(
						'member_id',
						'contract_number',
						'ROUND((total_amount_approve-total_amount_balance),2) AS loan_amount_balance',
						'date_last_interest'
					))
					->from('coop_loan_atm')
					->where("loan_atm_id = '{$loan_atm_id}' AND member_id = '{$member_id}' AND loan_atm_status = '1' ")
					->limit(1)
					->get()->row_array();
		return $row;        
    }
	
	public function gen_arr_insert($data_loan){ 
		if($data_loan['pay_loan_type'] == 'loan'){
			$get_cal_loan = $this->get_cal_loan($data_loan);
		}
		
		if($data_loan['pay_loan_type'] == 'atm'){
			$get_cal_loan = $this->get_cal_loan_atm($data_loan);
		}
		$pay_amount = $data_loan['pay_amount'];
		$interest_all = $get_cal_loan['interest'];
		if($pay_amount<=$interest_all){
			$interest = $pay_amount;
			$principal_payment = 0;
		}else{
			$interest = $interest_all;
			$principal_payment = $pay_amount-$interest_all;
		}

		$arr_data = array();
		$arr_data['member_id'] = $data_loan['member_id'];
		
		if($data_loan['pay_loan_type'] == 'loan'){
			$arr_data['loan_id'][0] = $data_loan['loan_id'];
			$arr_data['account_list'][0] = '15';
		}
		
		if($data_loan['pay_loan_type'] == 'atm'){
			$arr_data['loan_atm_id'][0] = $data_loan['loan_id'];			
			$arr_data['account_list'][0] = '31';
		}
		$arr_data['principal_payment'][0] = $principal_payment; //ชำระเงินต้น
		$arr_data['interest_all'][0] = $interest_all; //ดอกเบี้ยที่คำนวณได้
		$arr_data['start_date_cal'][0] = $get_cal_loan['start_date_cal']; //วันที่เริ่มคำนวณ
		$arr_data['end_date_cal'][0] = $get_cal_loan['end_date_cal']; //วันที่สิ้นสุดการคำนวณ
		$arr_data['interest'][0] = $interest; //ชำระเงินดอกเบี้ย
		$arr_data['interest_debt'][0] = '';
		$arr_data['amount'][0] = $data_loan['pay_amount'];
		$arr_data['deduct_type'][0] = 'all';
		$arr_data['pay_type'][0] = 'transfer';
		$arr_data['loan_principal_refrain'][0] = '';
		$arr_data['loan_interest_refrain'][0] = '';
		return $arr_data; 
	}
	
	public function get_receipt_number($date){        
        $result = array();
		$mm = date('m',strtotime($date));
        $yy = (date('Y',strtotime($date)) + 543);
		$yymm = $yy.$mm;
        $yy_full = $yy;

        $this->db->select('*');
        $this->db->from('coop_receipt');
        $this->db->where("receipt_id LIKE '" . $yy_full . $mm . "%'");
        $this->db->order_by("receipt_id DESC");
        $this->db->limit(1);
        $row = $this->db->get()->row_array();

        if (!empty($row)) {
            $id = (int)substr($row["receipt_id"], 6);
             $result['receipt_number'] = $yymm . sprintf("%06d", $id + 1);
        } else {
             $result['receipt_number'] = $yymm . "000001";
        }
        $result['order_by_id'] = $row["order_by"] + 1;
		return $result;
	}
	
	//บันทึกข้อมูลการชำระเงินกู้
	public function save_receipt($data_post){
		$result = array();
		$sum_interest = 0;
        if($data_post['fix_date']!=""){
            $tmp_date = explode("/", $data_post['fix_date']);
            $date = ($tmp_date[2]-543)."-".$tmp_date[1]."-".$tmp_date[0];
        }else{
            $date = date('Y-m-d H:i:s');
        }

        //get receipt setting data
        $receipt_format = 1;
        $receipt_finance_setting = $this->db->select("*")->from("coop_setting_finance")->where("name = 'receipt_cashier_format' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        if(!empty($receipt_finance_setting)) {
            $receipt_format = $receipt_finance_setting['value'];
        }

        if($receipt_format == 1) {
            $arr_receipt_number = $this->get_receipt_number($date); //gen เลขใบเสร็จ
            $receipt_number = $arr_receipt_number['receipt_number'];
        } else {
            $receipt_number = $this->Finance_libraries->generate_cashier_receipt_id($receipt_format, $date);
        }
		$order_by_id = $arr_receipt_number['order_by_id'];
        
        $data_insert = array();
        $data_insert['receipt_id'] = $receipt_number;
        $data_insert['member_id'] = $data_post['member_id'];
        $data_insert['order_by'] = @$order_by_id;
        $total = 0;
        foreach ($data_post['amount'] as $key => $value) {
            $total += $value;
        }
        $data_insert['sumcount'] = number_format($total, 2, '.', '');
        $data_insert['receipt_datetime'] = $date;
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['pay_type'] = $data_post["pay_type"] == "transfer" ? "1" : "0";
        $this->db->insert('coop_receipt', $data_insert);
			
        if ($data_post["pay_type"] == 1) {
            $data_post_pay_type = '1';
        } else {
            $data_post_pay_type = '0';
        }
        $process = 'cashier';
        $money = $total;
        $ref = $receipt_number;;
        $match_type = 'main';
        $match_id = '1';
        if ($data_post_pay_type == 1) {
            $statement = 'credit';
        } else {
            $statement = 'debit';
        }
        $data_process[] = $this->account_transaction->set_data_account_trancetion_detail($match_id, $statement, $match_type, $ref, $money, $process);
        $loan_amount_balance = 0;
		$affected_rows = 0;
        foreach ($data_post['account_list'] as $key => $value) {
            $this->db->select(array('*'));
            $this->db->from('coop_account_list');
            $this->db->where("account_id = '" . @$data_post['account_list'][$key] . "'");
            $this->db->limit(1);
            $row_account_list = $this->db->get()->result_array();
            $row_account_list = @$row_account_list[0];
            $data_insert = array();
            $data_insert['receipt_id'] = $receipt_number;
            $data_insert['receipt_list'] = $data_post['account_list'][$key];
            $data_insert['receipt_count'] = number_format($data_post['amount'][$key], 2, '.', '');
            $this->db->insert('coop_receipt_detail', $data_insert);
            if ($data_post['loan_id'][$key] != '') {
                $this->db->select(array('loan_amount_balance', 'contract_number'));
                $this->db->from('coop_loan');
                $this->db->where("id = '" . $data_post['loan_id'][$key] . "'");
                $row = $this->db->get()->result_array();
                $row_loan = @$row[0];
                $transaction_text = $this->Finance_libraries->generate_loan_receipt_text_cashier($data_post['loan_id'][$key], $data_post['account_list'][$key]);
                $loan_amount_balance = @$row_loan['loan_amount_balance'] - $data_post['principal_payment'][$key];
                if ($loan_amount_balance <= 0) {
                    $loan_amount_balance = 0;
                    $data_insert = array();
                    $data_insert['loan_amount_balance'] = $loan_amount_balance;
                    $data_insert['loan_status'] = '4';
                    $this->db->where('id', $data_post['loan_id'][$key]);
                    $this->db->update('coop_loan', $data_insert);
                } else {
                    $data_insert = array();
                    $data_insert['loan_amount_balance'] = number_format($loan_amount_balance, 2, '.', '');
                    $this->db->where('id', $data_post['loan_id'][$key]);
                    $this->db->update('coop_loan', $data_insert);
                }
                $loan_transaction = array();
                $loan_transaction['loan_id'] = $data_post['loan_id'][$key];
                $loan_transaction['loan_amount_balance'] = $loan_amount_balance;
                $loan_transaction['transaction_datetime'] = $date;
                $loan_transaction['receipt_id'] = $receipt_number;
                $this->loan_libraries->loan_transaction($loan_transaction);
                //Non pay
                $non_pay_sum = $this->db->select("loan_id, sum(non_pay_amount_balance) as sum_amount_balance")
                    ->from("coop_non_pay_detail")
                    ->where("loan_id = '" . $data_post['loan_id'][$key] . "' AND pay_type = 'principal'")
                    ->get()->row();
                if ($non_pay_sum->sum_amount_balance > $loan_amount_balance) {
                    $cal_balance = $non_pay_sum->sum_amount_balance - $loan_amount_balance;
                    $non_pays = $this->db->select("t1.run_id, t1.non_pay_amount_balance, t1.non_pay_id")
                        ->from("coop_non_pay_detail as t1")
                        ->join("coop_non_pay as t2", "t1.non_pay_id = t2.non_pay_id", "inner")
                        ->where("t1.loan_id = '" . $data_post['loan_id'][$key] . "' AND pay_type = 'principal' AND t1.non_pay_amount_balance > 0")
                        ->order_by("t2.non_pay_year, t2.non_pay_month")
                        ->get()->result_array();
                    foreach ($non_pays as $non_pay) {
                        if ($cal_balance >= $non_pay["non_pay_amount_balance"]) {
                            $data_insert = array();
                            $data_insert['non_pay_amount_balance'] = 0;
                            $this->db->where('run_id', $non_pay['run_id']);
                            $this->db->update('coop_non_pay_detail', $data_insert);
                            $cal_balance -= $non_pay["non_pay_amount_balance"];
                        } else {
                            $data_insert = array();
                            $data_insert['non_pay_amount_balance'] = $non_pay["non_pay_amount_balance"] - $cal_balance;
                            $this->db->where('run_id', $non_pay['run_id']);
                            $this->db->update('coop_non_pay_detail', $data_insert);
                            $cal_balance = 0;
                        }
                        $non_pay_details = $this->db->select("sum(non_pay_amount_balance) as sum_balance")
                            ->from("coop_non_pay_detail")
                            ->where("non_pay_id = '" . $non_pay["non_pay_id"] . "'")
                            ->get()->row();
                        $data_insert = array();
                        $data_insert['non_pay_amount_balance'] = $non_pay_details->sum_balance;
                        if ($non_pay_details->sum_balance <= 0) {
                            $data_insert['non_pay_status'] = 2;
                        }
                        $this->db->where('non_pay_id', $non_pay['non_pay_id']);
                        $this->db->update('coop_non_pay', $data_insert);
                    }
                }
                if ($data_post['deduct_type'][$key] == 'all') {
                    $data_insert = array();
                    $data_insert['date_last_interest'] = $date;
                    $this->db->where('id', $data_post['loan_id'][$key]);
                    $this->db->update('coop_loan', $data_insert);
                }
                if (!empty($data_post["loan_interest_refrain"][$key])) {
                    $data_insert = array();
                    $data_insert["refrain_loan_id"] = $data_post["loan_interest_refrain"][$key];
                    $data_insert["member_id"] = $data_post['member_id'];
                    $data_insert["pay_type"] = "interest";
                    $data_insert["org_value"] = $data_post['interest_all'][$key];
                    $data_insert["paid_value"] = 0;
                    $data_insert["status"] = 1;
                    $data_insert["paid_date"] = $date;
                    $data_insert["receipt_id"] = $receipt_number;
                    $data_insert["createdatetime"] = $date;
                    $data_insert["updatedatetime"] = $date;
                    $this->db->insert('coop_loan_refrain_history', $data_insert);
                }
            } else if ($data_post['loan_atm_id'][$key] != '') {
                $this->db->select(array(
                    'loan_id',
                    'loan_amount_balance'
                ));
                $this->db->from('coop_loan_atm_detail');
                $this->db->where("loan_atm_id = '" . $data_post['loan_atm_id'][$key] . "' AND loan_status = '0'");
                $this->db->order_by('loan_id ASC');
                $row = $this->db->get()->result_array();
                $principal_payment = $data_post['principal_payment'][$key];
                foreach ($row as $key_atm => $value_atm) {
                    if ($principal_payment > 0) {
                        if ($principal_payment >= $value_atm['loan_amount_balance']) {
                            $data_insert = array();
                            $data_insert['loan_amount_balance'] = 0;
                            $data_insert['loan_status'] = '1';
                            $data_insert['date_last_pay'] = date('Y-m-d');
                            $this->db->where('loan_id', $value_atm['loan_id']);
                            $this->db->update('coop_loan_atm_detail', $data_insert);
                            $principal_payment = $principal_payment - $value_atm['loan_amount_balance'];
                        } else {
                            $data_insert = array();
                            $data_insert['loan_amount_balance'] = $value_atm['loan_amount_balance'] - $principal_payment;
                            $data_insert['date_last_pay'] = date('Y-m-d');
                            $this->db->where('loan_id', $value_atm['loan_id']);
                            $this->db->update('coop_loan_atm_detail', $data_insert);
                            $principal_payment = 0;
                        }
                    }
                }
                $this->db->select(array(
                    'total_amount_approve',
                    'total_amount_balance',
                    'contract_number'
                ));
                $this->db->from('coop_loan_atm');
                $this->db->where("loan_atm_id = '" . $data_post['loan_atm_id'][$key] . "'");
                $row = $this->db->get()->result_array();
                $row_loan_atm = $row[0];
                $transaction_text = @$row_account_list['account_list'] . "เลขที่สัญญา " . @$row_loan_atm['contract_number'];
                $total_amount_balance = $row_loan_atm['total_amount_balance'] + $data_post['principal_payment'][$key];
                $data_insert = array();
                $data_insert['total_amount_balance'] = $total_amount_balance;
                $this->db->where('loan_atm_id', $data_post['loan_atm_id'][$key]);
                $this->db->update('coop_loan_atm', $data_insert);
                $loan_amount_balance = $row_loan_atm['total_amount_approve'] - $total_amount_balance;
                $atm_transaction = array();
                $atm_transaction['loan_atm_id'] = $data_post['loan_atm_id'][$key];
                $atm_transaction['loan_amount_balance'] = $loan_amount_balance;
                $atm_transaction['transaction_datetime'] = $date;
                $atm_transaction['receipt_id'] = $receipt_number;
                $this->loan_libraries->atm_transaction($atm_transaction);
                if ($data_post['deduct_type'][$key] == 'all') {
                    $data_insert = array();
                    $data_insert['date_last_interest'] = $date;
                    $this->db->where('loan_atm_id', $data_post['loan_atm_id'][$key]);
                    $this->db->update('coop_loan_atm', $data_insert);
                }
                //Non pay
                $non_pay_sum = $this->db->select("loan_atm_id, sum(non_pay_amount_balance) as sum_amount_balance")
                    ->from("coop_non_pay_detail")
                    ->where("loan_atm_id = '" . $data_post['loan_atm_id'][$key] . "' AND pay_type = 'principal'")
                    ->get()->row();
                if ($non_pay_sum->sum_amount_balance > $loan_amount_balance) {
                    $cal_balance = $non_pay_sum->sum_amount_balance - $loan_amount_balance;
                    $non_pays = $this->db->select("t1.run_id, t1.non_pay_amount_balance, t1.non_pay_id")
                        ->from("coop_non_pay_detail as t1")
                        ->join("coop_non_pay as t2", "t1.non_pay_id = t2.non_pay_id", "inner")
                        ->where("t1.loan_atm_id = '" . $data_post['loan_atm_id'][$key] . "' AND pay_type = 'principal' AND t1.non_pay_amount_balance > 0")
                        ->order_by("t2.non_pay_year, t2.non_pay_month")
                        ->get()->result_array();
                    foreach ($non_pays as $non_pay) {
                        if ($cal_balance >= $non_pay["non_pay_amount_balance"]) {
                            $data_insert = array();
                            $data_insert['non_pay_amount_balance'] = 0;
                            $this->db->where('run_id', $non_pay['run_id']);
                            $this->db->update('coop_non_pay_detail', $data_insert);
                            $cal_balance -= $non_pay["non_pay_amount_balance"];
                        } else {
                            $data_insert = array();
                            $data_insert['non_pay_amount_balance'] = $non_pay["non_pay_amount_balance"] - $cal_balance;
                            $this->db->where('run_id', $non_pay['run_id']);
                            $this->db->update('coop_non_pay_detail', $data_insert);
                            $cal_balance = 0;
                        }
                        $non_pay_details = $this->db->select("sum(non_pay_amount_balance) as sum_balance")
                            ->from("coop_non_pay_detail")
                            ->where("non_pay_id = '" . $non_pay["non_pay_id"] . "'")
                            ->get()->row();
                        $data_insert = array();
                        $data_insert['non_pay_amount_balance'] = $non_pay_details->sum_balance;
                        if ($non_pay_details->sum_balance <= 0) {
                            $data_insert['non_pay_status'] = 2;
                        }
                        $this->db->where('non_pay_id', $non_pay['non_pay_id']);
                        $this->db->update('coop_non_pay', $data_insert);
                    }
                }
            } elseif ($value == 47) {
                $total_amount = $data_post['amount'][$key];
                $compromises = $this->db->select("*")
                    ->from("coop_loan_guarantee_compromise")
                    ->where("id = '" . $data_post['compromise_id'][$key] . "'")
                    ->get()->result_array();
                foreach ($compromises as $compromise) {
                    if ($total_amount >= $compromise['other_debt_blance']) {
                        $total_amount -= $compromise['other_debt_blance'];
                        $data_insert = array();
                        $data_insert['other_debt_blance'] = 0;
                        $this->db->where('id', $compromise['id']);
                        $this->db->update('coop_loan_guarantee_compromise', $data_insert);
                    } else {
                        $debt_left = $compromise['other_debt_blance'] - $total_amount;
                        $data_insert = array();
                        $data_insert['other_debt_blance'] = $debt_left;
                        $this->db->where('id', $compromise['id']);
                        $this->db->update('coop_loan_guarantee_compromise', $data_insert);
                        $total_amount = 0;
                    }
                }
                $compromise_detail = $this->db->select("*")
                    ->from("coop_loan_guarantee_compromise")
                    ->where("compromise_id = '" . $data_post['compromise_id'][$key] . "'")
                    ->get()->row();
                $data_post['loan_id'][$key] = $compromise_detail->loan_id;
                $transaction_text = $row_account_list['account_list'];
            } else {
                $transaction_text = @$row_account_list['account_list'];
            }
            $loan_interest_now = @$data_post['interest'][$key]; //ดอกเบี้ยที่จ่าย
            $loan_interest_remain = 0; //ดอกเบี้ยคงเหลือค้างชำระ
            //หาสถานะของสมาชิก
            $this->db->select('mem_type,member_status');
            $this->db->from('coop_mem_apply');
            $this->db->where("member_id = '" . $data_post['member_id'] . "'");
            $this->db->limit(1);
            $row_mem_apply = $this->db->get()->result_array();
            $mem_type = @$row_mem_apply[0]['mem_type'];
            //echo '<pre>'; print_r($data_post); echo '</pre>';
            //@start บันทึกข้อมูลดอกเบี้ยค้างชำระสะสม
            if (($mem_type == '4' || $mem_type == '5' || $mem_type == '7') && empty($data_post["loan_interest_refrain"][$key])) {
                if (@$data_post['interest_all'][$key] != 0 && @$data_post['interest_all'][$key] != @$data_post['interest'][$key]) {
                    $interest_balance = @$data_post['interest_all'][$key];
                    $data_insert = array();
                    $data_insert['member_id'] = $data_post['member_id'];
                    $data_insert['loan_id'] = $data_post['loan_id'][$key];
                    $data_insert['loan_atm_id'] = $data_post['loan_atm_id'][$key];
                    $data_insert['interest_total'] = @$data_post['interest_all'][$key]; //ดอกเบี้ยทั้งหมด ณ วันที่ออกใบเสร็จ
                    $data_insert['interest_balance'] = @$interest_balance; //ดอกเบี้ยคงเหลือค้างชำระ
                    $data_insert['interest_date'] = $date;
                    $data_insert['receipt_id'] = $receipt_number;
                    $data_insert['interest_status'] = 0;
                    $data_insert['admin_id'] = $_SESSION['USER_ID'];
                    $data_insert['created_datetime'] = $date;
                    $data_insert['updated_datetime'] = $date;
                    $this->db->insert('coop_loan_interest_debt', $data_insert);
                }
                if ((@$data_post['interest_debt'][$key] != 0 && @$data_post['interest'][$key] != 0) || (@$data_post['interest'][$key] != 0 && @$data_post['interest_all'][$key] != 0)) {
                    $where_loan = (@$data_post['loan_id'][$key] != "") ? " AND loan_id = '" . $data_post['loan_id'][$key] . "'" : " AND loan_atm_id = '" . $data_post['loan_atm_id'][$key] . "'";
                    $this->db->select("id, loan_id,interest_total,interest_balance");
                    $this->db->from('coop_loan_interest_debt');
                    $this->db->where("interest_balance > 0 {$where_loan} AND interest_status = 0 ");
                    $this->db->order_by("created_datetime ASC");
                    $row_interest_debt = $this->db->get()->result_array();
                    $post_interest_balance = @$data_post['interest'][$key];
                    $interest_debt_pay = 0;
                    foreach ($row_interest_debt as $key_interest_debt => $value_interest_debt) {
                        if ($post_interest_balance > 0) {
                            if ($post_interest_balance >= $value_interest_debt['interest_balance']) {
                                $data_insert = array();
                                $data_insert['interest_balance'] = 0;
                                $data_insert['updated_datetime'] = $date;
                                $this->db->where('id', $value_interest_debt['id']);
                                $this->db->update('coop_loan_interest_debt', $data_insert);
                                $post_interest_balance = $post_interest_balance - $value_interest_debt['interest_balance'];
                            } else {
                                $data_insert = array();
                                $data_insert['interest_balance'] = $value_interest_debt['interest_balance'] - $post_interest_balance;
                                $data_insert['updated_datetime'] = $date;
                                $this->db->where('id', $value_interest_debt['id']);
                                $this->db->update('coop_loan_interest_debt', $data_insert);
                                $post_interest_balance = 0;
                            }
                        }
                    }
                }
                //$loan_interest_now; //ดอกเบี้ยที่จ่าย
                //$loan_interest_remain; //ดอกเบี้ยคงเหลือค้างชำระ
                if (@$data_post['interest'][$key] > @$data_post['interest_debt'][$key]) {
                    $loan_interest_now = (@$data_post['interest'][$key] - @$data_post['interest_debt'][$key] > 0) ? @$data_post['interest'][$key] - @$data_post['interest_debt'][$key] : 0;
                    $loan_interest_remain = (@$data_post['interest'][$key] - $loan_interest_now > 0) ? @$data_post['interest'][$key] - $loan_interest_now : 0;
                } else {
                    $loan_interest_now = 0;
                    $loan_interest_remain = @$data_post['interest'][$key];
                }
            }
            //echo 'เงินต้น = '.$data_post['principal_payment'][$key].'<br>';
            //echo 'ดอกเบี้ย = '.$loan_interest_now.'<br>';
            //echo 'ดอกคงค้าง = '.$loan_interest_remain.'<br>';
            //@end บันทึกข้อมูลดอกเบี้ยค้างชำระสะสม
            $data_insert = array();
            $data_insert['member_id'] = $data_post['member_id'];
            $data_insert['receipt_id'] = $receipt_number;
            $data_insert['loan_id'] = $data_post['loan_id'][$key];
            $data_insert['loan_atm_id'] = $data_post['loan_atm_id'][$key];
            $data_insert['deduct_type'] = $data_post['deduct_type'][$key];
            $data_insert['account_list_id'] = $data_post['account_list'][$key];
            $data_insert['principal_payment'] = number_format($data_post['principal_payment'][$key], 2, '.', '');
            $data_insert['interest'] = number_format($loan_interest_now, 2, '.', '');
            $data_insert['loan_interest_remain'] = number_format($loan_interest_remain, 2, '.', '');
            $data_insert['total_amount'] = $data_post['amount'][$key];
            $data_insert['loan_amount_balance'] = number_format($loan_amount_balance, 2, '.', '');
            $data_insert['payment_date'] = $date;
            $data_insert['createdatetime'] = $date;
            $data_insert['transaction_text'] = $transaction_text;
            $data_insert['interest_cal'] = $data_post['interest_all'][$key];
            $data_insert['start_date_cal'] = $data_post['start_date_cal'][$key];
            $data_insert['end_date_cal'] = $data_post['end_date_cal'][$key];
            $this->db->insert('coop_finance_transaction', $data_insert);
			if($this->db->affected_rows()){
				$affected_rows++;
			}	
            $statement_status = 'debit';   // สถานะการจ่ายเงิน debit = เงินเข้าจากเคาน์เตอร์, credit  = เงินออกจากเคาน์เตอร์,
            $permission_id = $this->permission_model->permission_url($_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI']);
            $this->tranction_financial_drawer->arrange_data_coop_financial_drawer($data_insert, $data_post["pay_type"], $permission_id, $statement_status, $_SERVER['REQUEST_URI']);
            $loan_interest_all = number_format(@$loan_interest_now, 2, '.', '') + number_format(@$loan_interest_remain, 2, '.', '');
            $sum_interest += number_format($loan_interest_all, 2, '.', '');
            if ($data_post['loan_id'][$key] == '') {
                $this->db->select('account_chart_id');
                $this->db->from('coop_account_match');
                $this->db->where("match_id = '" . $data_post['account_list'][$key] . "' AND match_type = 'account_list'");
                $row = $this->db->get()->result_array();
                $row_account_chart = @$row[0];
                $account_chart_id = @$row_account_chart['account_chart_id'];
            } else {
                $this->db->select('coop_account_match.account_chart_id');
                $this->db->from('coop_account_match');
                $this->db->join('coop_loan', 'coop_account_match.match_id = coop_loan.loan_type', 'left');
                $this->db->where("coop_loan.id = '" . $data_post['loan_id'][$key] . "' AND coop_account_match.match_type = 'loan'");
                $row = $this->db->get()->result_array();
                $row_account_chart = @$row[0];
                $account_chart_id = @$row_account_chart['account_chart_id'];
            }
            $process = 'cashier';
            $money = number_format($data_post['principal_payment'][$key], 2, '.', '');
            $ref = $receipt_number;
            $match_type = 'account_list';
            $match_id = $data_post['account_list'][$key];
            if ($data_post_pay_type == 1) {
                $statement = 'debit';
            } else {
                $statement = 'credit';
            }
            $data_process[] = $this->account_transaction->set_data_account_trancetion_detail($match_id, $statement, $match_type, $ref, $money, $process);
        }
        $process = 'cashier';
        $money = number_format($sum_interest, 2, '.', '');
        $ref = $receipt_number;
        $match_type = 'main';
        $match_id = '2';
        if ($data_post_pay_type == 1) {
            $statement = 'debit';
        } else {
            $statement = 'credit';
        }
        $data_process[] = $this->account_transaction->set_data_account_trancetion_detail($match_id, $statement, $match_type, $ref, $money, $process);
		//echo"<pre>";print_r($data_process);
        $this->account_transaction->add_account_trancetion_detail($data_process);
			
		$result['receipt_id'] = $receipt_number;
		$result['affected_rows'] = $affected_rows;
		return $result;
    }
	
	public function get_data_receipt($receipt_id,$data_loan,$data){		
		$result = array();
		$row = $this->db->select(array(
						'finance_transaction_id',
						'receipt_id',
						'principal_payment',
						'interest',
						'total_amount',
						'loan_amount_balance',
						'createdatetime',
						'interest_cal'
					))
					->from('coop_finance_transaction')
					->where("receipt_id = '{$receipt_id}'")
					->limit(1)
					->get()->row_array();
		if(!empty($row)){			
			$result['receipt_id'] = $receipt_id; //เลขที่ใบเสร็จ
			$result['account_id'] = $data['account_id']; //เลขบัญชีเงินฝาก
			$result['contract_number'] = $data_loan['contract_number']; //เลขสัญญาเงินกู้
			$result['fee'] = '0.00'; //ค่าธรรมเนียม
			$result['payment_date'] = $row['createdatetime']; //วันที่ทำรายการ
			$result['pay_amount'] = $data['pay_amount']; //ยอดเงินที่ชำระ
			$result['principal_payment'] = $row['principal_payment']; //ชำระเงินต้น
			$result['interest'] = $row['interest']; //ชำระดอกเบี้ย
			$result['interest_all'] = $row['interest_cal']; //ดอกเบี้ยที่คำนวณได้
			$result['interest_balance'] = ($row['interest_cal']-$row['interest']); //ดอกเบี้ยที่ค้างชำระจากการคำนวณ
			$result['loan_amount_balance'] = $row['loan_amount_balance']; //หนี้คงเหลือ
			$result['finance_transaction_id'] = $row['finance_transaction_id']; //รหัสอ้างอิงการทำรายการ table coop_finance_transaction
		}
		return $result;
    }
	
	public function check_status($data_loan,$loan_type,$data){		
		$data_arr = array();
		$arr_data_loan = array();
		$member_id = $data_loan['member_id'];
		$transaction_balance = $this->get_account_transaction_balance($data_loan['account_id']);
		$data_maco_account = $this->get_maco_account($data_loan['account_id'],$member_id);

		if($loan_type == 'loan'){
			$arr_data_loan = $this->get_data_loan($data_loan['loan_id'],$member_id);
			$loan_amount_balance = $arr_data_loan['loan_amount_balance'];
		}
		
		if($loan_type == 'atm'){
			$arr_data_loan = $this->get_data_loan_atm($data_loan['loan_id'],$member_id);
			$loan_amount_balance = $arr_data_loan['loan_amount_balance'];
		}
		
		$data_arr = $data;
		if(empty($arr_data_loan)){
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'ไม่พบข้อมูลเงินกู้';
		}else if(empty($data_maco_account)){
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'ไม่พบข้อมูลเงินฝากเลขบัญชี '.$data_loan['account_id'];
		}else if($data_maco_account['account_status'] == 1){
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'เลขบัญชี '.$data_loan['account_id'].' ถูกปิดบัญชีแล้ว';
		}else if($transaction_balance < $data_loan['pay_amount']){
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'ยอดเงินในบัญชีมีไม่พอ';
		}else if($data_loan['pay_amount'] > $loan_amount_balance){
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'ยอดเงินที่ชำระมากกว่าหนี้';
		}else{
			$data_arr['status'] = 'ok';
			$data_arr['msg'] = 'ทำรายการต่อ';
		}
		return $data_arr;
    }

	public function get_maco_account($account_id,$member_id){
		$row = $this->db->select(array('account_id','account_status','created'))
					->from('coop_maco_account')
					->where("account_id = '{$account_id}' AND mem_id = '{$member_id}' ")
					->limit(1)
					->get()->row_array();
		return $row;
    }
	
	public function get_account_transaction_balance($account_id){		
		$transaction_balance = $this->db->select(array('transaction_balance'))
					->from('coop_account_transaction')
					->where("account_id = '{$account_id}'")
					->order_by('transaction_time DESC,transaction_id DESC')
					->limit(1)
					->get()->row_array()['transaction_balance'];
		return $transaction_balance;
    }

	public function save_account_transaction($data){	
		//echo '<pre>'; print_r($data); echo '</pre>';	
		$affected_rows = 0 ;
		$transaction_balance = $this->get_account_transaction_balance($data['account_id']);
		$sum = $transaction_balance - $data['pay_amount'];
		$data_insert = array();
		$data_insert['transaction_time'] = ($date_transaction !="" && $data['custom_by_user_id']!="") ? $date_transaction." ".date('H:i:s') : date('Y-m-d H:i:s');
		$data_insert['transaction_list'] = 'XWM';
		$data_insert['transaction_withdrawal'] = $data['pay_amount'];
		$data_insert['transaction_deposit'] = '';
		$data_insert['transaction_balance'] = $sum;
		$data_insert['transaction_no_in_balance'] = $sum;
		$data_insert['account_id'] = $data['account_id'];
		$data_insert['status_process'] = $data['platform'];
		$data_insert['mobile_uid'] = $data['uid'];
		$this->db->insert('coop_account_transaction', $data_insert);
		$transaction_id = $this->db->insert_id();
		if($this->db->affected_rows()){
			$this->save_transaction_mobile($data,$transaction_id);
			$affected_rows++;
		}
		return $affected_rows;
    }
	
	public function save_transaction_mobile($data,$transaction_id){
		$affected_rows = 0 ;
		$data_insert = array();
		$data_insert['uid'] = $data['uid'];
		$data_insert['platform'] = $data['platform'];
		$data_insert['widthdraw_id'] = $transaction_id;
		$data_insert['name_page'] = $data['page_name'];
		$this->db->insert('transaction_mobile', $data_insert);
		if($this->db->affected_rows()){
			$affected_rows++;
		}
		return $affected_rows;
    }

    public function get_loan($id){
	    return $this->db->get_where("coop_loan", "id='{$id}'")->row_array();
    }

    public function get_loan_atm($id){
	    return $this->db->get_where('coop_loan_atm', "loan_atm_id='{$id}'")->row_array();
    }

    public function get_loan_balance($id){
        return $this->db->select(array('*'))
            ->from('coop_loan_transaction')
            ->where(array('loan_id' => $id))
            ->order_by("loan_transaction_id desc, transaction_datetime desc")->limit(1)->get()->row_array();
    }

    public function get_loan_atm_balance($id){
	    return $this->db->select(array('*'))
            ->from('coop_loan_atm_transaction')
            ->where(array('loan_atm_id' => $id))
            ->order_by("loan_atm_transaction_id desc, transaction_datetime desc")->limit(1)->get()->row_array();
    }

    public function find_finance_month($id, $type = "loan"){
	    $type = strtoupper($type);
        $result = array();
        //ยอดเงินหลังประมวลผล
        //month_profile
        $this->db->select(array('t1.pay_amount','t2.profile_month','t2.profile_year', "SUM(if(t1.pay_type='principal', real_pay_amount, 0)) as principal", "SUM(if(t1.pay_type='interest', real_pay_amount, 0)) as interest, t1.loan_amount_balance"));
        $this->db->from('coop_finance_month_detail AS t1');
        $this->db->join("coop_finance_month_profile AS t2","t1.profile_id = t2.profile_id","inner");
        if($type == "ATM"){
            $this->db->where("t1.loan_atm_id = '".$id."' AND t1.pay_type = 'principal' AND t1.deduct_code = '".$type."' AND t1.run_status = '0'");
        }else{
            $this->db->where("t1.loan_id = '".$id."' AND t1.pay_type = 'principal' AND t1.deduct_code = '".$type."' AND t1.run_status = '0'");
        }
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }
}
