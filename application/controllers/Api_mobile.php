<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_mobile extends CI_Controller {
	public $authConfig = array(
		"key" => "KJAUUMJZYRHL5TDX",
		"secret" => "srF4J^+cJ6e9YFV9tt#hrR^ufKENbCVh"
	);
	function __construct()
	{
		parent::__construct();
		$this->load->model("Cashier_loan_model", "cashier_loan");
		$this->load->model("Loan_save_model", "loan_save_model");
	}
	public function pay_loan(){
		$arr_data = array();
		$data = json_decode(file_get_contents('php://input'),true);
        header('Content-Type: application/json; charset: utf-8');
		
		/*
		//data test
		$data = array(			
			'platform'=>'android',
			'member_no'=>'999999',			
			'uid'=>'00000000-0000-0000-5d1b-0634607e6ee4',			
			'account_id'=>'1203299',
			//'loan_id'=>'10598',
			//'loan_type'=>'loan',
			//'loan_id'=>'1216',
			'loan_id'=>'1205',
			'loan_type'=>'atm',
			'pay_amount'=>'20',
			'page_name'=>'โอนเงินกู้'
		);
		*/
		
		//loan_id = รหัสอ้างอิงเงินกู้
		//loan_type=ประเภทเงินกู้  (loan,atm)
		//account_id = บัญชีเงินฝาก
		//pay_amount = ยอดเงินที่ชำระ		
		//echo '<pre>'; print_r($data); echo '</pre>'; exit;
		if(!empty($data)){
			if($data['loan_type'] == 'loan'){
				//หาข้อมูลเงินกู้สามัญ ,พิเศษ,ฉุกเฉิน
				$get_data_loan = $this->cashier_loan->get_data_loan($data['loan_id'],$data['member_no']);
					
				$data_loan = array();
				$data_loan['loan_id'] = $data['loan_id'];
				$data_loan['loan_amount_balance'] = $get_data_loan['loan_amount_balance'];
				$data_loan['loan_type'] = $get_data_loan['loan_type'];
				$data_loan['pay_loan_type'] = $data['loan_type'];
				$data_loan['date_last_interest'] = $get_data_loan['date_last_interest'];
				$data_loan['member_id'] = $data['member_no'];
				$data_loan['account_id'] = $data['account_id'];
				$data_loan['pay_amount'] = $data['pay_amount'];
				
				//check interest
				$get_cal_loan = $this->cashier_loan->get_cal_loan($data_loan);
				$data_loan['interest_all'] = $get_cal_loan['interest'];
			
				//check status
				$check_status = $this->cashier_loan->check_status($data_loan,$data['loan_type'],$data);
				if($check_status['status'] == 'error'){
					$data_arr = $check_status;
					echo json_encode($data_arr);
					exit;				
				}		

				$arr_data = $this->cashier_loan->gen_arr_insert($data_loan);
			}else if($data['loan_type'] == 'atm'){		
				//หาข้อมูลเงินกู้ฉุกเฉิน ATM
				$get_data_loan = $this->cashier_loan->get_data_loan_atm($data['loan_id'],$data['member_no']);

				$data_loan = array();
				$data_loan['loan_id'] = $data['loan_id'];
				$data_loan['loan_amount_balance'] = $get_data_loan['loan_amount_balance'];
				$data_loan['pay_loan_type'] = $data['loan_type'];
				$data_loan['date_last_interest'] = $get_data_loan['date_last_interest'];
				$data_loan['member_id'] = $data['member_no'];
				$data_loan['account_id'] = $data['account_id'];
				$data_loan['pay_amount'] = $data['pay_amount'];
				
				//check interest
				$get_cal_loan_atm = $this->cashier_loan->get_cal_loan_atm($data_loan);
				$data_loan['interest_all'] = $get_cal_loan['interest'];
				
				//check status
				$check_status = $this->cashier_loan->check_status($data_loan,$data['loan_type'],$data);

				if($check_status['status'] == 'error'){
					$data_arr = $check_status;
					echo json_encode($data_arr);
					exit;				
				}
				
				$arr_data = $this->cashier_loan->gen_arr_insert($data_loan);
			}
		
			//โอนเงิน
			$transfer = $this->cashier_loan->save_account_transaction($data);
			if($transfer >= 1){
				//บันทึกข้อมูลใบเสร็จ
				$chek_save = $this->cashier_loan->save_receipt($arr_data);
				
				$data_arr = array();		
				if($chek_save['affected_rows'] < 1){
					$data_arr = $data;
					$data_arr['status'] = 'error';
					$data_arr['msg'] = 'ชำระเงินกู้ไม่สำเร็จ';
				}else{			
					$data_arr = $this->cashier_loan->get_data_receipt($chek_save['receipt_id'],$get_data_loan,$data);
					$data_arr['status'] = 'success';
					$data_arr['msg'] = 'ชำระเงินกู้สำเร็จ';

				}
			}else{
				$data_arr = $data;
				$data_arr['status'] = 'error';
				$data_arr['msg'] = 'ชำระเงินกู้ไม่สำเร็จ';
			}
		}else{
			$data_arr = $data;
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'ชำระเงินกู้ไม่สำเร็จ';
		}
		//echo '<pre>'; print_r($data_arr); echo '</pre>';
		echo json_encode($data_arr);
		exit;
	}

	public function loan_request(){
		$arr_data = array();
		$data = json_decode(file_get_contents('php://input'),true);
        header('Content-Type: application/json; charset: utf-8');
		/*
		//data test
		$data = array(			
			'platform'=>'android',
			'uid'=>'00000000-0000-0000-5d1b-0634607e6ee4',	
			'page_name'=>'ขอกู้ฉุกเฉิน',
			'member_no'=>'000001',
			//'loan_amount'=>'50000',//จำนวนเงินที่ขอกู้
			//'period_amount'=>'9',//จำนวนงวด
		);		
		//echo '<pre>'; print_r($data); echo '</pre>';
		*/
		if(!empty($data)){
			$arr_data = array();		
			$info = $this->info->member($data['member_no']);
			$member = $info->getInfo();
			$data['salary'] = $member->salary;
			$data_loan = $this->contract->getDataLoanMobile($data);

			//check status
			$check_status = $this->contract->check_status($data_loan);
			if($check_status['status'] == 'error'){
				$data_arr = $check_status;
				echo json_encode($data_arr);
				exit;				
			}
			
			if(!empty($data_loan)){
				$data_arr = $data_loan;
				$data_arr['status'] = 'success';
				$data_arr['msg'] = '';
			}else{
				$data_arr = $data;
				$data_arr['status'] = 'error';
				$data_arr['msg'] = 'ไม่พบข้อมูล';
			}
		}else{
			$data_arr = $data;
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'ไม่พบข้อมูล';
		}
		echo json_encode($data_arr);
		exit;
	}

	public function loan_save(){
		$arr_data = array();
		$data = json_decode(file_get_contents('php://input'),true);
        header('Content-Type: application/json; charset: utf-8');
		/*
		//data test
		$data = array(			
			'platform'=>'android',
			'uid'=>'00000000-0000-0000-5d1b-0634607e6ee4',	
			'page_name'=>'ขอกู้ฉุกเฉิน',
			'member_no'=>'000001',
			'loan_amount'=>'100000',//จำนวนเงินที่ขอกู้
			'period_amount'=>'10',//จำนวนงวด
		);	
		*/
		//echo '<pre>'; print_r($data); echo '</pre>';
		
		if(!empty($data)){
			$arr_data = array();		
			$info = $this->info->member($data['member_no']);
			$member = $info->getInfo();
			$data['salary'] = $member->salary;
			$data_loan = $this->contract->getDataLoanMobile($data);

			//check status
			$check_status = $this->contract->check_status($data_loan);
			//echo '<pre>'; print_r($check_status); echo '</pre>'; exit;
			if($check_status['status'] == 'error'){
				$data_arr = $check_status;
				echo json_encode($data_arr);
				exit;				
			}
			
			if(!empty($data_loan)){
				$arr_data = $this->contract->gen_arr_insert($data_loan);
				$data_save = $this->loan_save_model->get_loan_save($arr_data,$data);
				//บันทึกเงินกู้
				$data_arr = $data_save;
			}else{
				$data_arr = $data;
				$data_arr['status'] = 'error';
				$data_arr['msg'] = 'บันทึกข้อมูลไม่สำเร็จ';
			}
		}else{
			$data_arr = $data;
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'บันทึกข้อมูลไม่สำเร็จ';
		}
		echo json_encode($data_arr);
		exit;
	}

	public function pay_loan_query(){
		$arr_data = array();
		$data = json_decode(file_get_contents('php://input'),true);
		header('Content-Type: application/json; charset: utf-8');

		//get setting data.
		$reduce_finance_month = 0;
		$loan_balance_setting = 0;
		$loan_finance_setting = $this->db->select("*")->from("coop_setting_finance")->where("name = 'cashier_loan_balance' AND status = 1")->order_by("created_at DESC")->get()->row_array();
		if(!empty($loan_finance_setting)) {
			$loan_balance_setting = $loan_finance_setting['value'];
		}

		if(!empty($data)){
			if($data['loan_type'] == 'loan'){
				//หาข้อมูลเงินกู้สามัญ ,พิเศษ,ฉุกเฉิน
				$get_data_loan = $this->cashier_loan->get_data_loan($data['loan_id'], NULL);

				$data_loan = array();
				$data_loan['id'] = $data['loan_id'];
				$data_loan['loan_id'] = $data['loan_id'];
				$data_loan['date_close_account'] = $this->center_function->ConvertToThaiDate_sub(date('Y-m-d H:i:s'),'1','0');
				$data_loan['loan_amount'] = $get_data_loan['loan_amount'];
				$data_loan['loan_amount_balance'] = $get_data_loan['loan_amount_balance'];
				$data_loan['loan_type'] = $get_data_loan['loan_type'];
				$data_loan['pay_loan_type'] = $get_data_loan['loan_type'];
				$data_loan['date_last_interest'] = $get_data_loan['date_last_interest'];
				$data_loan['member_id'] = $get_data_loan['member_id'];
				$data_loan['period_amount'] =  ($get_data_loan['period_amount'] == null OR $get_data_loan['period_amount'] == 0) ? 'N/A' : $get_data_loan['period_amount'];
				$data_loan['pay_per_month'] = number_format($get_data_loan['money_per_period'],2);
				$data_loan['loan_status'] = $get_data_loan['loan_status'];
				$data_loan['loan_status_desc'] = $get_data_loan['loan_status'] == 1 ? "ปกติ" : "เบี้ยวหนี้";
				$data_loan['pay_type'] = $get_data_loan['pay_type'];

				$data_loan['contract_number'] = $get_data_loan['contract_number'];
				$data_loan['request_date'] = empty($get_data_loan['request_datetime']) ? 'N/A' : $this->center_function->ConvertToThaiDate($get_data_loan['request_datetime'],'1','0');
				$data_loan['approve_date'] = empty($get_data_loan['approve_date']) ? 'N/A' : $this->center_function->ConvertToThaiDate($get_data_loan['approve_date'],'1','0');

				//Reduce loan balance by check setting
				if($loan_balance_setting == 1) {
					$finance_month = $this->db->select("SUM(pay_amount) as pay_amount")->from("coop_finance_month_detail")->where("run_status = 0 AND loan_id = '".$data['loan_id']."' AND pay_type = 'principal'")->get()->row_array();
				}

				//get interest.
				$get_cal_loan = $this->cashier_loan->get_cal_loan($data_loan);
				$data_loan['interest_all'] = number_format($get_cal_loan['interest'],2);
				$data_loan['loan_close_amount'] =  number_format(($get_data_loan['loan_amount_balance'] + $get_cal_loan['interest'] - $finance_month['pay_amount']),2);

				//Set num format
				$data_loan['loan_amount'] = number_format($get_data_loan['loan_amount'],2);
				$data_loan['loan_amount_balance'] = number_format(($get_data_loan['loan_amount_balance'] - $finance_month['pay_amount']),2);

				//get transaction.
				$get_transactions = $this->cashier_loan->get_loan_transaction_by_loan_id($data['loan_id']);
				$transactions = array();
				foreach($get_transactions as $get_transaction) {
					$data_loan['transactionData'][] = [
														'receipt_id' => $get_transaction['receipt_id'],
														'loan_amount_balance' => $get_transaction['loan_amount_balance'],
														'payment' => !empty($get_transaction['receipt_id']) ? number_format($get_transaction['principal'],2) : (!empty($get_transaction['loan_amount_balance']) ? number_format($get_transaction['loan_amount_balance'],2) : ""),
														'interest' => number_format($get_transaction['interest'],2),
														'receipt_datetime' =>  empty($get_transaction['receipt_datetime']) ? 'N/A' : $this->center_function->ConvertToThaiDate($get_transaction['receipt_datetime'],'1','0'),
														'cancel_date' => empty($get_transaction['cancel_date']) ? 'N/A' : $this->center_function->ConvertToThaiDate($get_transaction['cancel_date'],'1','0'),
														'payment_date' => !empty($get_transaction['receipt_datetime']) ? $this->center_function->ConvertToThaiDate($get_transaction['receipt_datetime'],'1','0') : (!empty($get_transaction['transaction_datetime']) ? $this->center_function->ConvertToThaiDate($get_transaction['transaction_datetime'],'1','0') : ""),
														'receipt_status' => $get_transaction['receipt_status'],
														'receipt_status_desc' => $get_transaction['receipt_id'] == 1 ? "รอการยืนยัน" : ($get_transaction['receipt_id'] == 2 ? "ยกเลิกใบเสร็จแล้ว" : "ปกติ"),
														'transaction_type' => !empty($get_transaction['profile_id']) ? "ชำระรายเดือน" : "ชำระอื่น ๆ",
														'period_count' => $get_transaction['period_count']
													];
				}
				// $data_loan['transactionData'] = $transactions;

				//get guarantee person.
				$get_guarantees = $this->cashier_loan->get_guarantee_by_loan_id($data['loan_id']);
				$guarantees = array();
				foreach($get_guarantees as $get_guarantee) {
					$guarantees[]['name'] =  $get_guarantee['prename_short']." ".$get_guarantee['firstname_th']." ".$get_guarantee['lastname_th'];
				}
				$data_loan['guaranteeData'] = $guarantees;
			}else if($data['loan_type'] == 'atm'){
				//return null on pending.
				$data_loan = array();
				$data_loan['loan_id'] = NULL;
				$data_loan['loan_amount'] = NULL;
				$data_loan['loan_amount_balance'] = NULL;
				$data_loan['loan_type'] = NULL;
				$data_loan['pay_loan_type'] = NULL;
				$data_loan['date_last_interest'] = NULL;
				$data_loan['member_id'] = NULL;
				$data_loan['period_amount'] = NULL;
				$data_loan['money_per_period'] = NULL;
				$data_loan['loan_status'] = NULL;
				$data_loan['request_date'] = NULL;
				$data_loan['approve_date'] = NULL;
				$data_loan['interest_all'] = NULL;
				$data_loan['transaction_data'] = array();
			}
			$data_arr['data'] = $data_loan;
			$data_arr['status'] = 'success';
			$data_arr['msg'] = 'ทำรายการสำเร็จ';

		}else{
			$data_arr = $data;
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'ทำรายการไม่ถูกต้อง';
		}

		echo json_encode($data_arr);
		exit;
	}
}
