<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_ktb_test extends CI_Controller {
	public $authConfig = array(
		"key" => "KJAUUMJZYRHL5TDX",
		"secret" => "srF4J^+cJ6e9YFV9tt#hrR^ufKENbCVh"
	);
	
	function __construct() {
		parent::__construct();
		
		//$this->load->library('LineConnect');
		$this->load->model("Cashier_loan_model", "cashier_loan");
        $this->load->model('Atm_calculator_model', 'ATMCalc');
	}
	
	function index() {
		if($_POST["_key"] == $this->authConfig["key"] && $_POST["_secret"] == $this->authConfig["secret"]) {
			//check_action_log_atm
			$this->gen_action_log_atm($_POST["_data"],'b_base64_decode');
			$data = base64_decode($_POST["_data"]);	// recive data
			
			writeToLog("data: ".$data, FCPATH."/application/logs/api_ktb_test.log", false);
			////$data = file_get_contents("atm_2020_09_24.log");
			//$data = file_get_contents("20200217023806index.log");
			//$data = file_get_contents("20200218040521index.log");
			//check_action_log_atm
			$this->gen_action_log_atm($data,'index');
			//checkdata account and loan
			$data = $this->check_data($data);
			/*writeToLog("data: ".$data, FCPATH."/application/logs/api_ktb_test.log", false);
			$data = 'check_data='.$data;
			echo json_encode([
				"data_21" => $data	// send data
			]);
			*/
			//echo $data;
			echo json_encode([
				"data" => base64_encode($data)	// send data
			]);
		}
		
		exit;
	}
	
	function check_data($data)
	{
		//check_action_log_atm
		$this->gen_action_log_atm($data,'check_data');
		//HEADER = 70
		$host_header = substr($data, 0, 70);

		//BODY = 160
		$arr_data_file = explode($host_header,$data);		
		$b_n = (strlen($arr_data_file[1]) == 160)?"":"\n";
		$data_body = $arr_data_file[1].$b_n;

		$decryptedMessage = $this->decrypt_des($data_body);
		
        $data = array();
		$data[] = $decryptedMessage;
		
		//check_action_log_atm
		$this->gen_action_log_atm($data,'decrypted_data');
        $arr_data = array();
        foreach ($data as $item){
			$arr_data['transaction_code']		= substr($item,0, 4);
            $arr_data['bank_reference_number']	= substr($item,4, 20);
            $arr_data['transaction_date']		= substr($item,24, 8);
            $arr_data['transaction_time']		= substr($item,32, 8);
            $arr_data['bank_code']				= substr($item,40,3);
            $arr_data['filler_1']				= substr($item,43,3);
            $arr_data['transaction_amount']		= substr($item,46,15);
            $arr_data['list_id']       			= substr($item,61,3);
            $arr_data['from_acct_type']       	= substr($item,64,2);
            $arr_data['coop_code']      		= substr($item,66, 4);
            $arr_data['overdraft_available']	= substr($item,70, 15);
            $arr_data['filler_2']     			= substr($item,85,2);
            $arr_data['coop_member_id']     	= substr($item,87,20);
            $arr_data['company_account']      	= substr($item,107,10);
            $arr_data['customer_coop_account']	= substr($item,117,15);
            $arr_data['payment_channel']		= substr($item,132,4);
            $arr_data['response_code']      	= substr($item,136,4);
            $arr_data['loan_payment_balance']	= substr($item,140,15);
            $arr_data['posted_date']         	= substr($item,155,5);
        }
		
		//รหัสสกรณ์ ฟิกค่าไว้  คือค่า Account No ในไฟล์ 0122-ATM KTB online access Informationออมทรัพย์-รพตำรวจ-2
		$data_setting_atm = $this->get_setting_atm_online();
		$arr_data['company_account'] = $data_setting_atm['company_account'];
		
		//$arr_data['transaction_code'] = '0400';
		//$arr_data['list_id'] = '002';
		//$arr_data['from_acct_type'] = '01';
		//$arr_data['transaction_amount'] = '+00000000010000';

		/*
		$arr_data['transaction_code'] = '0100';
		$arr_data['list_id'] = '001';
		$arr_data['from_acct_type'] = '01';
		$arr_data['transaction_amount'] = '+00000000000000';
		*/
		/*
		//test ชำระเงินกู้ ATM
		$arr_data['transaction_code'] = '0200';
		$arr_data['list_id'] = '003';
		$arr_data['from_acct_type'] = '01';
		$arr_data['transaction_amount'] = '+00000000100000'; //1,000 บาท
		//$arr_data['transaction_amount'] = '+00000000003000'; //30 บาท
		//$arr_data['transaction_amount'] = '+00000000150000'; //1,500 บาท
		//$arr_data['transaction_amount'] = '+00000001500000'; //15,000 บาท
		*/
		//test reversal_refund_increase
		/*$arr_data['transaction_code'] = '0400';
		$arr_data['list_id'] = '003';
		$arr_data['from_acct_type'] = '01';
		$arr_data['transaction_amount'] = '+00000000100000';
		*/
		//echo '<pre>'; print_r($arr_data); echo '</pre>';
		
		//check_action_log_atm
		$this->gen_action_log_atm($arr_data,'check_data_b_save_request');
		//บันทึก request
		$this->save_request($arr_data);
		
		//check_action_log_atm
		$this->gen_action_log_atm($arr_data['list_id'],'check_list_id');
		if($arr_data['list_id'] == '001'){
			$res = $this->balance_inquiry(@$arr_data);
		}else if($arr_data['list_id'] == '002'){
			$res = $this->withdrawlTxn(@$arr_data);
		}else if($arr_data['list_id'] == '003'){
			$res = $this->depositTxn(@$arr_data);
		}
		//echo '<pre>'; print_r($res); echo '</pre>';
		$data_gen_encrypt = $host_header.$this->encrypt_des($res);	

		return $data_gen_encrypt;
	}
	//
	function str_osplit($string, $offset){
		return isset($string[$offset]) ? array(substr($string, 0, $offset), substr($string, $offset)) : false;
    }
	//
	
	function decrypt_des($data){		
		//To decrypt
		$data_setting_atm = $this->get_setting_atm_online();	
		$key = $data_setting_atm['encrypt_key'];
		$method = 'DES-ECB';

		$decryptedMessage = openssl_decrypt($data, $method, hex2bin($key), OPENSSL_NO_PADDING);

		return $decryptedMessage;
	}
	
	function encrypt_des($data){		
		//To encrypt
		$data_setting_atm = $this->get_setting_atm_online();
		$key = $data_setting_atm['encrypt_key'];
		$method = 'DES-ECB';

		$encryptedMessage = openssl_encrypt($data, $method, hex2bin($key), OPENSSL_NO_PADDING);
		
		return $encryptedMessage;
	}
	
	//แปลงจำนวนเงิน เป็นตัวเลขมีทศนิยม
	public function text_to_decimal($data){
		$result = '';
		$data_integer = substr(@$data,0,13);//จำนวนเต็ม
		$data_decimal = substr(@$data,13,2);//ทศนิยม
		$result = (int)$data_integer.'.'.$data_decimal;//ยอดเงินที่ถอน	
		return $result;
	}
	//แปลงจำนวนเงิน เป็นตัวเลขที่มี 0 นำหน้า
	public function decimal_to_text($data){
		$result = '';
		$data = number_format($data, 2, '.', '');
		$arr_data = explode('.',$data);
		$result = sprintf("%015d",$arr_data[0].$arr_data[1]);	
		return $result;
	}
	
	public function gen_text_response($data){
		$result = '';
		foreach($data AS $key=>$value){
			$result .= @$value;
		}
		return $result;
	}
	
	public function gen_file_response($data){
		$result = '';
		$date_gen = date('Ymdhis');
		
		$name_file = "InquiryResponseMessage_Example_".$date_gen.".dat";
		$coop_fiid = "SPKC";
		$path_file = 'test_file_atm_external_body/'.$name_file;
		$path_file_full = FCPATH.$path_file;
		
		$data_file = $data;

		//echo $data."<hr>";
		//exit;
		if(write_file($path_file_full, $data_file) == FALSE)
		{
		   echo '';

		} else {
			echo $path_file;                          
		}
		//InquiryResponseMessage_Example.dat
		//return $result;
	}
	
	//รองรับการสอบถามยอดเงินในบัญชี
	public function balance_inquiry($get_request_data){
		//รายการสอบถามยอดเงินกู้ 
		//รายการสอบถามยอดเงินฝาก
		
		$account_id = @$this->convert_account(@$get_request_data['customer_coop_account'],@$get_request_data['coop_member_id']);
		//echo 'account_id='.$account_id.'<br>'; exit;
		//AvaliableBalance ยอดเงินที่สามารถทำรายการได้
		$avaliable_balance = $this->avaliable_balance(@$account_id,@$get_request_data['from_acct_type'],@$get_request_data['coop_member_id']);

		$transactionYYmmdd = $get_request_data['transaction_date'];
		$response_code = $this->response_code(@$get_request_data,$avaliable_balance,$transactionYYmmdd);
		$transaction_code = $this->message_type(@$get_request_data['transaction_code']);
		
		//เพิ่ม function เช็ค ยอดหนี้เงินกู้คงเหลือ ตอบกลับเมือส่งค่ามาเป็นประเภทเงินกู้ ถ้าไม่ใช่ตอบกลับไปเป็น 0	
		$loan_payment_balance = $this->loan_payment_balance(@$account_id,@$get_request_data['from_acct_type'],@$get_request_data['coop_member_id']);
		
		// response	
		$res_save = array(
			'transaction_code'=>@$transaction_code,
			'bank_reference_number'=>@$get_request_data['bank_reference_number'],
			'transaction_date'=>@$get_request_data['transaction_date'],
			'transaction_time'=>@$get_request_data['transaction_time'],
			'bank_code'=>@$get_request_data['bank_code'],
			'filler_1'=>@$get_request_data['filler_1'],
			'transaction_amount'=>@$get_request_data['transaction_amount'], 
			'list_id'=>@$get_request_data['list_id'],
			'from_acct_type'=>@$get_request_data['from_acct_type'],
			'coop_code'=>@$get_request_data['coop_code'],
			'overdraft_available'=>@$avaliable_balance,
			'filler_2'=>@$get_request_data['filler_2'],
			'coop_member_id'=>@$get_request_data['coop_member_id'],
			'company_account'=>@$get_request_data['company_account'],
			'customer_coop_account'=>@$get_request_data['customer_coop_account'],
			'payment_channel'=>@$get_request_data['payment_channel'],
			'response_code'=>@$response_code,
			'loan_payment_balance'=>@$loan_payment_balance,
			'posted_date'=>@$get_request_data['posted_date']
		);

		//echo '<pre>'; print_r($res_save); echo '</pre>';
		//exit;
		//check_action_log_atm
		$this->gen_action_log_atm($arr_data,'inq_b_save_response');
		//บันทึก response
		$save_response = $this->save_response($res_save);	
		$res = $this->gen_text_response($res_save);	
		return ($res);
	}
	
	//รองรับการทำรายการถอนเงิน
	public function withdrawlTxn($get_request_data){
		
		$account_id = @$this->convert_account(@$get_request_data['customer_coop_account'],@$get_request_data['coop_member_id']);
		//echo 'account_id='.$account_id.'<br>';
		
		//AvaliableBalanceCheck ยอดเงินที่สามารถทำรายการได้
		$avaliable_balance_check = $this->avaliable_balance(@$account_id,@$get_request_data['from_acct_type'],@$get_request_data['coop_member_id']);		

		$transactionYYmmdd = $get_request_data['transaction_date'];
		$response_code = $this->response_code(@$get_request_data,$avaliable_balance_check,$transactionYYmmdd);

		//วันที่เวลาทำรายการ จากการขอทำรายการ
		$createdatetime = $this->convert_datetime(@$get_request_data['transaction_date'],@$get_request_data['transaction_time']);

		//exit;	
		//ค่าธรรมเนียมการทำรายการ
		//$fee_amount = $this->get_amount_fee(@$get_request_data);
		//echo 'fee_amount='.$fee_amount.'<br>';
		if(@$get_request_data['transaction_code'] == '0200' && $response_code == '0000'){	
			//บันทึกการถอนเงิน	
			$arr_data_withdrawl = array();
			$arr_data_withdrawl['coop_member_id'] = @$get_request_data['coop_member_id'];
			//$arr_data_withdrawl['customer_coop_account'] = @$get_request_data['customer_coop_account'];
			$arr_data_withdrawl['customer_coop_account'] = @$account_id;
			$arr_data_withdrawl['from_acct_type'] = @$get_request_data['from_acct_type'];
			$arr_data_withdrawl['transaction_amount'] = $this->text_to_decimal(@$get_request_data['transaction_amount']);//ยอดเงินที่ถอน
			$arr_data_withdrawl['createdatetime'] = $createdatetime;
			
			$result_save_withdrawl = $this->saveWithdrawlTxn(@$arr_data_withdrawl,@$fee_amount);
			if($result_save_withdrawl == '1'){
				$response_code = '0000';
			}else{
				$response_code = '0011';
			}
		}else if(@$get_request_data['transaction_code'] == '0400'){
			//echo '==================reversal================<br>';
			//reversal ต้องคืนเงินให้ลูกค้า
			$arr_data_reversal = array();
			$arr_data_reversal['coop_member_id'] = @$get_request_data['coop_member_id'];
			//$arr_data_reversal['customer_coop_account'] = @$get_request_data['customer_coop_account'];
			$arr_data_reversal['customer_coop_account'] = @$account_id;
			$arr_data_reversal['from_acct_type'] = @$get_request_data['from_acct_type'];
			$arr_data_reversal['transaction_amount'] = $this->text_to_decimal(@$get_request_data['transaction_amount']);//ยอดเงินที่ถอน
			$arr_data_reversal['createdatetime'] = $createdatetime;
			$arr_data_reversal['transaction_code'] = @$get_request_data['transaction_code'];		
			$arr_data_reversal['bank_reference_number'] = @$get_request_data['bank_reference_number'];		
			$arr_data_reversal['transaction_date'] = @$get_request_data['transaction_date'];			
			$reversal = $this->reversal_refund(@$arr_data_reversal);
			if($reversal == '1'){
				$response_code = '0000';
			}else{				
				//067 invalid cash back amt -> เงินคืนที่ไม่ถูกต้อง
				$response_code = '0011';
			}
			
		}
		
		//AvaliableBalance ยอดเงินที่สามารถทำรายการได้		
		$avaliable_balance = $this->avaliable_balance(@$account_id,@$get_request_data['from_acct_type'],@$get_request_data['coop_member_id']);

		//ยอดหนี้เงินกู้คงเหลือ
		$loan_payment_balance = $this->loan_payment_balance(@$account_id,@$get_request_data['from_acct_type'],@$get_request_data['coop_member_id']);

		$transaction_code = $this->message_type(@$get_request_data['transaction_code']);

		// response	
		$res_save = array(
			'transaction_code'=>@$transaction_code,
			'bank_reference_number'=>@$get_request_data['bank_reference_number'],
			'transaction_date'=>@$get_request_data['transaction_date'],
			'transaction_time'=>@$get_request_data['transaction_time'],
			'bank_code'=>@$get_request_data['bank_code'],
			'filler_1'=>@$get_request_data['filler_1'],
			'transaction_amount'=>@$get_request_data['transaction_amount'], 
			'list_id'=>@$get_request_data['list_id'],
			'from_acct_type'=>@$get_request_data['from_acct_type'],
			'coop_code'=>@$get_request_data['coop_code'],
			'overdraft_available'=>@$avaliable_balance,
			'filler_2'=>@$get_request_data['filler_2'],
			'coop_member_id'=>@$get_request_data['coop_member_id'],
			'company_account'=>@$get_request_data['company_account'],
			'customer_coop_account'=>@$get_request_data['customer_coop_account'],
			'payment_channel'=>@$get_request_data['payment_channel'],
			'response_code'=>@$response_code,
			'loan_payment_balance'=>@$loan_payment_balance,
			'posted_date'=>@$get_request_data['posted_date']
		);
		//check_action_log_atm
		$this->gen_action_log_atm($arr_data,'withdrawlTxn_b_save_response');
		//บันทึก response
		$save_response = $this->save_response($res_save);	

		$res = $this->gen_text_response($res_save);	

		return ($res);
	}
	
	//หายอดเงินที่อนุมัติให้ทำรายการได้ -ของ ATM  -ของเงินฝาก
	public function avaliable_balance($from_acct_no,$from_acct_type,$member_id){
		//01 = เงินกู้
		//02 = เงินฝาก
		//echo 'from_acct_no='.$from_acct_no.',from_acct_type='.$from_acct_type.'<hr>';
		$balance = 0;
		//ATM
		if($from_acct_type == '01'){
			$this->db->select(array('*'));
			$this->db->from('coop_loan_atm');
			$this->db->where("member_id = '".@$member_id."' AND loan_atm_status ='1'  AND activate_status = '0' ");
			$this->db->limit(1);	
			$row_loan_atm = $this->db->get()->row_array();
			
			if(!empty($row_loan_atm)){				
				$check_transaction = $this->check_atm_transaction($row_loan_atm);
				if($check_transaction == 1){
					$balance = number_format(@$row_loan_atm['total_amount_balance'], 2, '.', '');
				}else{
					$balance = 0;
				}
			}			
		}
		
		if($from_acct_type == '02'){
			//เงินฝาก อื่นๆ 
			$this->db->select(array('*'));
			$this->db->from('coop_maco_account');			
			$this->db->where("account_id = '".@$from_acct_no."'");	
			$this->db->limit(1);	
			$rs_account = $this->db->get()->result_array();
			$row_account = @$rs_account[0];
			//echo $this->db->last_query();
			if(!empty($row_account)){
				$sequester_status = @$row_account['sequester_status'];
				$sequester_status_atm = @$row_account['sequester_status_atm'];
				$sequester_amount = @$row_account['sequester_amount'];
				$this->db->select(array('*'));
				$this->db->from('coop_account_transaction');			
				$this->db->where("account_id = '".@$from_acct_no."'");	
				$this->db->order_by("transaction_time DESC, transaction_id DESC");	
				$this->db->limit(1);	
				$rs_transaction = $this->db->get()->result_array();
				$row_transaction = @$rs_transaction[0];
				if(!empty($row_transaction)){
					if($sequester_status_atm == '1'){
						//อายัดเงินในบัญชีทั้งหมด
						$balance = number_format(0, 2, '.', '');
					}else if($sequester_status == '2'){
						//อายัดเงินในบัญชีบางส่วน
						$withdrawal_amount = @$row_transaction['transaction_balance']-@$sequester_amount;
						$balance = number_format(@$withdrawal_amount, 2, '.', '');
					}else{
						$balance = number_format(@$row_transaction['transaction_balance'], 2, '.', '');
					}
				}
			}
		}
		return $this->decimal_to_text($balance);
	}
	
	public function loan_payment_balance($from_acct_no,$from_acct_type,$member_id){
		//01 = เงินกู้
		//02 = เงินฝาก
		$balance = 0;
		
		//ATM
		if($from_acct_type == '01'){
			$this->db->select('(total_amount_approve-total_amount_balance) AS loan_payment_balance,loan_atm_id,total_amount');
			$this->db->from('coop_loan_atm');			
			$this->db->where("member_id = '".@$member_id."' AND loan_atm_status ='1'  AND activate_status = '0' ");
			$this->db->limit(1);	
			$row_loan_atm = $this->db->get()->row_array();

			if(!empty($row_loan_atm)){
				$check_transaction = $this->check_atm_transaction($row_loan_atm);
				if($check_transaction == 1){
					$balance = number_format(@$row_loan_atm['loan_payment_balance'], 2, '.', '');
				}else{					
					$row_transaction = $this->atm_transaction_by_id($row_loan_atm['loan_atm_id']);
					$balance = $row_transaction['loan_amount_balance'];
				}
			}
		}
		
		if($from_acct_type == '02'){
			//เงินฝาก อื่นๆ 
			$this->db->select(array('*'));
			$this->db->from('coop_maco_account');			
			$this->db->where("account_id = '".@$from_acct_no."'");	
			$this->db->limit(1);	
			$rs_account = $this->db->get()->result_array();
			$row_account = @$rs_account[0];
			
			if(!empty($row_account)){
				$sequester_status = @$row_account['sequester_status'];
				$sequester_status_atm = @$row_account['sequester_status_atm'];
				$sequester_amount = @$row_account['sequester_amount'];
				$this->db->select(array('*'));
				$this->db->from('coop_account_transaction');			
				$this->db->where("account_id = '".@$from_acct_no."'");	
				$this->db->order_by("transaction_time DESC, transaction_id DESC");	
				$this->db->limit(1);	
				$rs_transaction = $this->db->get()->result_array();
				$row_transaction = @$rs_transaction[0];
				if(!empty($row_transaction)){
					if($sequester_status_atm == '1'){
						//อายัดเงินในบัญชีทั้งหมด
						$balance = number_format(0, 2, '.', '');
					}else if($sequester_status == '2'){
						//อายัดเงินในบัญชีบางส่วน
						$withdrawal_amount = @$row_transaction['transaction_balance']-@$sequester_amount;
						$balance = number_format(@$withdrawal_amount, 2, '.', '');
					}else{
						$balance = number_format(@$row_transaction['transaction_balance'], 2, '.', '');
					}
				}
			}
		}
		return $this->decimal_to_text($balance);
	}
	
	public function save_request($get_request_data){
		$createdatetime = $this->convert_datetime(@$get_request_data['transaction_date'],@$get_request_data['transaction_time']);
		
		$data_insert = array(
			'transaction_code'=>@$get_request_data['transaction_code'],
			'bank_reference_number'=>@$get_request_data['bank_reference_number'],
			'transaction_date'=>@$get_request_data['transaction_date'],
			'transaction_time'=>@$get_request_data['transaction_time'],
			'bank_code'=>@$get_request_data['bank_code'],
			'filler_1'=>@$get_request_data['filler_1'],
			'transaction_amount'=>@$get_request_data['transaction_amount'], 
			'list_id'=>@$get_request_data['list_id'],
			'from_acct_type'=>@$get_request_data['from_acct_type'],
			'coop_code'=>@$get_request_data['coop_code'],
			'overdraft_available'=>@$get_request_data['overdraft_available'],
			'filler_2'=>@$get_request_data['filler_2'],
			'coop_member_id'=>@$get_request_data['coop_member_id'],
			'company_account'=>@$get_request_data['company_account'],
			'customer_coop_account'=>@$get_request_data['customer_coop_account'],
			'payment_channel'=>@$get_request_data['payment_channel'],
			'response_code'=>@$get_request_data['response_code'],
			'loan_payment_balance'=>@$get_request_data['loan_payment_balance'],
			'posted_date'=>@$get_request_data['posted_date'],
			'createdatetime'=>@$createdatetime
		);
		//echo '============data_insert_request=============<br>';
		//echo '<pre>'; print_r($data_insert); echo '</pre>';
		$this->db->insert("message_request_atm_ktb", $data_insert);
		
		//check_action_log_atm
		$this->gen_action_log_atm($data_insert,'a_save_request');
	}
	
	public function save_response($get_request_data){
		$createdatetime = $this->convert_datetime(@$get_request_data['transaction_date'],@$get_request_data['transaction_time']);
		
		$data_insert = array(
			'transaction_code'=>@$get_request_data['transaction_code'],
			'bank_reference_number'=>@$get_request_data['bank_reference_number'],
			'transaction_date'=>@$get_request_data['transaction_date'],
			'transaction_time'=>@$get_request_data['transaction_time'],
			'bank_code'=>@$get_request_data['bank_code'],
			'filler_1'=>@$get_request_data['filler_1'],
			'transaction_amount'=>@$get_request_data['transaction_amount'], 
			'list_id'=>@$get_request_data['list_id'],
			'from_acct_type'=>@$get_request_data['from_acct_type'],
			'coop_code'=>@$get_request_data['coop_code'],
			'overdraft_available'=>@$get_request_data['overdraft_available'],
			'filler_2'=>@$get_request_data['filler_2'],
			'coop_member_id'=>@$get_request_data['coop_member_id'],
			'company_account'=>@$get_request_data['company_account'],
			'customer_coop_account'=>@$get_request_data['customer_coop_account'],
			'payment_channel'=>@$get_request_data['payment_channel'],
			'response_code'=>@$get_request_data['response_code'],
			'loan_payment_balance'=>@$get_request_data['loan_payment_balance'],
			'posted_date'=>@$get_request_data['posted_date'],
			'createdatetime'=>@$createdatetime
		);
		//echo '============data_insert_response=============<br>';
		//echo '<pre>'; print_r($data_insert); echo '</pre>';
		$this->db->insert("message_response_atm_ktb", $data_insert);
		//check_action_log_atm
		$this->gen_action_log_atm($data_insert,'a_save_response');
	}
	
	public function response_code($get_request_data,$avaliable_balance,$transactionYYmmdd){
		$transaction_amount = @$get_request_data['transaction_amount'];
		//$from_acct_no = @$get_request_data['customer_coop_account'];
		$from_acct_no = @$this->convert_account(@$get_request_data['customer_coop_account'],@$get_request_data['coop_member_id']);
		$from_acct_type = @$get_request_data['from_acct_type'];
		$list_id = @$get_request_data['list_id'];
		$member_id = @$get_request_data['coop_member_id'];
		$transaction_code = @$get_request_data['transaction_code'];
		/*รหัสการทำรายการ = list_id
		001 = รายการสอบถามยอดคงเหลือ
		002 = รายการเบิกเงิน
		003 = รายการชำระกู้/ฝากเงิน
		*/
		//echo '<br>--count--'.strlen($transaction_amount).'<hr>';
		//ยอดเงินที่ถอน 
		$request_amount = $this->text_to_decimal($transaction_amount);//ยอดเงินที่ถอน
		$avaliable_balance = $this->text_to_decimal($avaliable_balance);//จำนวนเงินที่สามารถถอนได้
		
		$this->db->select(array('max_withdraw_amount_day'));
		$this->db->from('coop_loan_atm_setting');					
		$rs_atm_setting = $this->db->get()->result_array();
		$row_atm_setting = @$rs_atm_setting[0];
		$max_withdraw_amount_day = @$row_atm_setting['max_withdraw_amount_day'];
		
		$total_withdraw_amount_day = 0;		

		//เช็็คการถอนเงิน ต่อวัน
		if($transaction_code == '0200' AND $list_id == '002'){
			//วันปัจจุบันที่ทำรายการ  $transactionYYmmdd;		
			$this->db->select(array('transaction_amount','response_code','transaction_code'));
			$this->db->from('message_response_atm_ktb');		
			$this->db->where("coop_member_id = '".$member_id."' AND transaction_date = '".$transactionYYmmdd."' AND list_id = '".$list_id."'");			
			$rs_response_atm = $this->db->get()->result_array();
			
			$total_transaction_all = 0; 
			$total_transaction = 0;
			foreach($rs_response_atm AS $key_1=>$row_response_atm){
				if(@$row_response_atm['transaction_code'] == '0210' AND @$row_response_atm['response_code'] == '0000'){
					$total_transaction = $this->text_to_decimal($row_response_atm['transaction_amount']);
					$total_transaction_all +=$total_transaction; 
				}else if(@$row_response_atm['transaction_code'] == '0410' AND @$row_response_atm['response_code'] == '0000'){
					$total_transaction = $this->text_to_decimal($row_response_atm['transaction_amount']);
					$total_transaction_all -=$total_transaction; 
				}
			}
			
			//ยอดที่ถอนของวันปัจจุบันที่ทำรายการ
			$total_withdraw_amount_day = @$total_transaction_all+@$request_amount;
		}
		
		if($from_acct_type == '01'){
			$this->db->select(array('*'));
			$this->db->from('coop_loan_atm');
			$this->db->where("member_id = '".@$member_id."' AND loan_atm_status ='1' AND activate_status = '0'");
			$this->db->limit(1);
			$from_rs_account = $this->db->get()->result_array();

			if(empty($from_rs_account)){
				$from_activate_status = '1';
				$from_account_status = '1';
				//$from_maco_account_id = '';
			}else{
				$from_activate_status = '0';
				$from_account_status = '0';	
				//$from_maco_account_id = @$from_rs_account[0]['account_id'];	//บัญชีต้นทาง
			}
		}else{	
			//เช็คบัญชีต้นทาง
			$this->db->select('*');
			$this->db->from('coop_maco_account');
			$this->db->where("account_id = '".$from_acct_no."'");
			$this->db->limit(1);
			$from_rs_account = $this->db->get()->result_array();
			//echo '<pre>'; print_r($from_rs_account); echo '</pre>';
			$from_account_status = @$from_rs_account[0]['account_status'];	
			//$from_maco_account_id = @$from_rs_account[0]['account_id'];	//บัญชีต้นทาง
			
			//เช็ค อายัดบัญชีเงินฝาก
			if(@$from_rs_account[0]['account_status'] == '0' && @$from_rs_account[0]['sequester_status_atm'] == '1'){
				$from_check_sequester = '1';
			}else{
				$from_check_sequester = '0';
			}
		}
		/*
		response_code
		0000 = Approve
		0011 = Declined (ปฏิเสธ)
		0012 = Please Retry (กรุณาลองใหม่)
		0005 = Stop Sending (หยุดส่ง)
		0095 = Invalid Authentication(การรับรองความถูกต้องไม่ถูกต้อง)
		0099 = System s Unavailable (ระบบไม่พร้อมใช้งาน)
		*/
		//echo 'from_account_status='.$from_account_status.'<hr>';
		//echo 'from_maco_account_id='.$from_maco_account_id.'<hr>';
		//echo 'from_check_sequester='.$from_check_sequester.'<hr>';
		//echo @$request_amount.' > '.@$avaliable_balance.'<hr>';
		//echo @$total_withdraw_amount_day.' > '.@$max_withdraw_amount_day.'<br>';
		
		//เช็คดเงินเหลือในบัญชีไม่ต่ำกว่า 100 บาท  ตามตั้งค่าของเงินฝาก
		$check_deposit_min = $this->check_deposit_balance_min($request_amount,$avaliable_balance,$from_acct_type,$transaction_code,$list_id);
		
		if(@$from_check_sequester == '1'){
			//echo 'A<br>';
			//อายัดบัญชีเงินฝาก
			$response_code = "0011";
		}else if($from_account_status == '1'){
			//echo 'B<br>';
			//บัญชีที่ปิดแล้ว			
			//056 ineligible account -> บัญชีที่ไม่มีสิทธิ์
			$response_code = "0011";
		}else if($from_activate_status == '1'){
			//echo 'C<br>';
			//บัญชีที่ถูกระงับ เงินกู้ฉุกเฉิน			
			//056 ineligible account -> บัญชีที่ไม่มีสิทธิ์
			$response_code = "0011";
		}else if(@$request_amount > @$avaliable_balance AND $list_id != '003'){
			//echo 'D<br>';
			//058 = insufficient funds ยอดเงินที่ถอนเกินยอดเงินที่สามารถถอนได้
			$response_code = "0011";
		}else if(@$total_withdraw_amount_day > @$max_withdraw_amount_day){		
			//echo 'E<br>';
			//061 = withrawal ถอนได้ไม่เกิน วันละ 200,000
			$response_code = "0011";
		}else if(@$check_deposit_min == ''){		
			//echo 'F<br>';
			//เช็คดเงินเหลือในบัญชีไม่ต่ำกว่า 100 บาท  ตามตั้งค่าของเงินฝาก
			$response_code = "0011";
		}else{
			///0000 = Approve
			$response_code = "0000";
		}
		
		//echo 'response_code='.$response_code.'<br>'; exit;
		return $response_code;
	}
	
	public function message_type($transaction_code,$message_type = ''){
		//transaction_code	
		//0100 = 1) รายการสอบถามยอดเงินกู้/สอบถามยอดเงินฝาก
		//0200 = 2) การรับเงินเงินกู้/ถอนเงินฝาก || ฝากเงิน/ชำระเงินกู้
		//0400 = 3) รายการ Reverse การรับเงินกู้/ถอนเงิน
		
		if($transaction_code == '0100'){
			$message_type = '0110';
		}else if($transaction_code == '0200'){
			$message_type = '0210';
		}else if($transaction_code == '0400'){
			$message_type = '0410';
		}else{
			$message_type = '';
		}
		
		return $message_type;
	}
	
	//บันทึกการถอนเงิน
	public function saveWithdrawlTxn($arr_data_withdrawl,$fee_amount){
		//from_acct_type 01 = เงินกู้   ,02 = เงินฝาก
		$from_acct_type = $arr_data_withdrawl['from_acct_type'];
		if($from_acct_type == '01'){
			$result = $this->saveWithdrawlTxnLoan($arr_data_withdrawl,$fee_amount);
		}
		
		if($from_acct_type == '02'){			
			$result = $this->saveWithdrawlTxnSaving($arr_data_withdrawl,$fee_amount);
		}
		
		return $result;
	}

	//บันทึกการถอนเงินจากบัญชี
	public function saveWithdrawlTxnSaving($arr_data_withdrawl,$fee_amount=0){
		$customer_coop_account = @$arr_data_withdrawl['customer_coop_account'];
		$transaction_amount = @$arr_data_withdrawl['transaction_amount'];
		$createdatetime = @$arr_data_withdrawl['createdatetime'];
		$transaction_time = date('Y-m-d H:i:s');
		//get member_id
		$member_id = @$arr_data_withdrawl['coop_member_id'];

		$this->db->select('*');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$customer_coop_account."'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		if(!empty($row)){
			$balance = $row[0]['transaction_balance'];
			$balance_no_in = $row[0]['transaction_no_in_balance'];
		}else{
			$balance = 0;
			$balance_no_in = 0;
		}
		
		$money = @$transaction_amount;
		$sum = @$balance - @$money;
		$sum_no_in = @$balance_no_in - @$money;
		if($sum_no_in <= 0 ){$sum_no_in = 0;}

		if($sum >= 0 && @$balance > 0) {	
			$data_insert = array();
			$data_insert['transaction_time'] = @$transaction_time;
			$data_insert['transaction_date_atm'] = @$createdatetime;
			$data_insert['transaction_list'] = 'WATM';
			$data_insert['transaction_withdrawal'] = @$money;
			$data_insert['transaction_deposit'] = '';
			$data_insert['transaction_balance'] = @$sum;
			$data_insert['transaction_no_in_balance'] = @$sum_no_in;
			$data_insert['member_id_atm'] = @$member_id;
			$data_insert['account_id'] = @$customer_coop_account;
			$data_insert['company_atm'] = 'KTB';

			if($this->db->insert('coop_account_transaction', $data_insert)){
				//check save
				$this->db->select('transaction_time,transaction_list,transaction_withdrawal,transaction_deposit,account_id');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$customer_coop_account."' AND transaction_time = '".@$transaction_time."' AND transaction_withdrawal = '".@$money."'");
				$this->db->order_by('transaction_time DESC, transaction_id DESC');
				$this->db->limit(1);
				$rs_account = $this->db->get()->result_array();
				$row_account = $rs_account[0];
				if($row_account['transaction_time'] != ''){
					$result = true;
					
					// Line notify
					/*$lineconnect = new LineConnect();
					$lineconnect->sendNotifyDeposit([
						"account_id" => $data_insert['account_id'],							// เลขบัญชี
						"transaction_time" => $data_insert['transaction_time'],		// วันเวลาทำรายการ
						"deposit" => 0,																		// ฝาก
						"withdrawal" => $data_insert['transaction_withdrawal'],		// ถอน
						"balance" => $data_insert['transaction_balance']				// คงเหลือ
					]);
					*/
				}else{
					$result = false;
				}
			}else{
				$result = false;
			}					
		}	

		if($fee_amount > 0){
			$sum = $sum - $fee_amount;
			$sum_no_in = $sum_no_in - $fee_amount;
			$data_insert = array();
			$data_insert['transaction_time'] = @$createdatetime;
			$data_insert['transaction_list'] = 'CM/FE';
			$data_insert['transaction_withdrawal'] = $fee_amount;
			$data_insert['transaction_deposit'] = '';
			$data_insert['transaction_balance'] = $sum;
			$data_insert['transaction_no_in_balance'] = $sum_no_in;
			$data_insert['member_id_atm'] = @$member_id;
			$data_insert['account_id'] = @$customer_coop_account;
			$data_insert['company_atm'] = 'KTB';
			$this->db->insert('coop_account_transaction', $data_insert);
		}
				
		return $result;
	}	
	
	//บันทึกการถอนเงินจากเงินกู้ ATM
	public function saveWithdrawlTxnLoan($arr_data_withdrawl,$fee_amount=0){
		$customer_coop_account = $arr_data_withdrawl['customer_coop_account'];
		$from_acct_type = $arr_data_withdrawl['from_acct_type'];
		$transaction_amount = $arr_data_withdrawl['transaction_amount']+@$fee_amount;
		$createdatetime = $arr_data_withdrawl['createdatetime'];
		//get member_id
		$member_id = $arr_data_withdrawl['coop_member_id'];
		

		$this->db->select('*');
		$this->db->from("coop_loan_atm_setting");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$row_setting = @$row[0];
		
		$this->db->select(array('loan_atm_id','member_id','total_amount_approve','total_amount_balance'));
		$this->db->from('coop_loan_atm');
		$this->db->where("member_id = '".@$member_id."' AND loan_atm_status ='1' ");
		$this->db->limit(1);
		$rs_loan_atm = $this->db->get()->result_array();
		$row_loan_atm = @$rs_loan_atm[0];
		//echo $this->db->last_query(); echo '<br>';

		if(!empty($row_loan_atm)){
			$loan_atm_id = @$row_loan_atm['loan_atm_id'];
			$loan_amount = str_replace(',','',$transaction_amount);
			
			//@2019-06-19 เช็ค atm_transaction ติดลบ 
			$this->db->select(array('loan_amount_balance'));
			$this->db->from('coop_loan_atm_transaction');
			$this->db->where("loan_atm_id = '".$loan_atm_id."'");
			$this->db->order_by('transaction_datetime DESC,loan_atm_transaction_id DESC');
			$this->db->limit(1);
			$row_balance_minus = $this->db->get()->row_array();
			if(@$row_balance_minus['loan_amount_balance'] < 0){
				$check_balance_minus = 1;
			}else{
				$check_balance_minus = 0;
			}			
			//
			
			$data_insert = array();
			$data_insert['loan_atm_id'] = @$loan_atm_id;
			$data_insert['member_id'] = @$member_id;

			if($check_balance_minus == 1){
				$loan_amount_balance = @$row_balance_minus['loan_amount_balance']+@$loan_amount;
				
				$data_insert['loan_amount'] = @$loan_amount;
				$data_insert['loan_amount_balance'] = (@$loan_amount_balance <= 0)?0:@$loan_amount_balance;			
				$data_insert['loan_status'] = (@$loan_amount_balance <= 0)?1:0;
				$data_insert['is_rebate'] = '1';
			}else{
				$data_insert['loan_amount'] = @$loan_amount;
				$data_insert['loan_amount_balance'] = @$loan_amount;			
				$data_insert['loan_status'] = '0';
			}
			$data_insert['loan_description'] = 'ทำรายการกู้ATM';
			$data_insert['date_start_period'] = date('Y-m-t',strtotime('+1 month'));
			$data_insert['transaction_at'] = '1';
			$data_insert['transfer_status'] = '1';
			$data_insert['member_id_atm'] = @$member_id;
			$data_insert['loan_date'] = @$createdatetime;
			
			$principal_per_month = $data_insert['loan_amount']/$row_setting['max_period'];
			$data_insert['principal_per_month'] = ceil($principal_per_month);
			
			$this->db->select(array('petition_number'));
			$this->db->from('coop_loan_atm_detail');
			$this->db->order_by('petition_number DESC');
			$this->db->limit(1);
			$row_petition_number = $this->db->get()->result_array();
			if(!empty($row_petition_number)){
				$petition_number = $row_petition_number[0]['petition_number']+1;
				$petition_number = sprintf('%06d',$petition_number);
			}else{
				$petition_number = sprintf('%06d',1);
			}
			$data_insert['petition_number'] = $petition_number;
			$data_insert['trace_no'] = $trace_no;
			$data_insert['term_seq_id'] = $term_seq_id;
			$data_insert['company_atm'] = 'KTB';

			if($this->db->insert('coop_loan_atm_detail',$data_insert)){
			
				$loan_id = $this->db->insert_id();
				
				$total_amount_balance = @$row_loan_atm['total_amount_balance'] - @str_replace(',','',$transaction_amount);
				
				$loan_amount_balance = @$row_loan_atm['total_amount_approve'] - $total_amount_balance;
				
				$data_insert = array();
				$data_insert['total_amount_balance'] = @$total_amount_balance;
				$this->db->where('loan_atm_id',@$loan_atm_id);
				$this->db->update('coop_loan_atm',$data_insert);
				
				$atm_transaction = array();
				$atm_transaction['loan_atm_id'] = @$loan_atm_id;
				$atm_transaction['loan_amount_balance'] = @$loan_amount_balance;
				$atm_transaction['transaction_datetime'] = @$createdatetime;
				//$this->loan_libraries->atm_transaction($atm_transaction);
                $this->loan_libraries->loan_transaction_arrears('AT', $atm_transaction);
				
				$data_insert = array();			
				$data_insert['loan_id'] = @$loan_id;
				$data_insert['date_transfer'] =  @$createdatetime;
				$data_insert['createdatetime'] = @$createdatetime;
				$data_insert['admin_id'] = '';
				$data_insert['transfer_status'] = '0';
				$data_insert['pay_type'] = '2'; //ATM
				$this->db->insert('coop_loan_atm_transfer', $data_insert);	
				
				//check save
				$this->db->select('loan_date,loan_atm_id');
				$this->db->from('coop_loan_atm_detail');
				$this->db->where("loan_atm_id = '".$loan_atm_id."' AND loan_date = '".@$createdatetime."' AND loan_amount = '".@$loan_amount."'");
				$this->db->order_by('loan_date DESC');
				$this->db->limit(1);
				$rs_atm_detail = $this->db->get()->result_array();
				$row_atm_detail = $rs_atm_detail[0];
				
				if($row_atm_detail['loan_date'] != ''){
					$result = true;
				}else{
					$result = false;
				}
			}else{
				$result = false;
			}	
		}	
		return $result;
	}	
	
	//reversal ต้องคืนเงินให้ลูกค้า 
	public function reversal_refund($arr_data_reversal){	
		$customer_coop_account = @$arr_data_reversal['customer_coop_account'];//เลขบัญชีธนาคาร
		$from_acct_type = @$arr_data_reversal['from_acct_type'];
		$transaction_amount = @$arr_data_reversal['transaction_amount'];
		$createdatetime = @$arr_data_reversal['createdatetime'];
		$transaction_code = @$arr_data_reversal['transaction_code'];		
		$transaction_time = date('Y-m-d H:i:s');
		$bank_reference_number = @$arr_data_reversal['bank_reference_number'];
		
		//get member_id
		$member_id = @$arr_data_reversal['coop_member_id'];	

		$check_reference_number = $this->get_bank_reference_number($arr_data_reversal);
		if($check_reference_number == 1){
			//ทำรายการคืนเงิน
			//from_acct_type 01 = เงินกู้ ,02 = เงินฝาก
			if($from_acct_type == '01'){
				$this->db->select(array('loan_atm_id','member_id','total_amount_approve','total_amount_balance'));
				$this->db->from('coop_loan_atm');
				//$this->db->where("account_id = '".@$customer_coop_account."' AND loan_atm_status ='1' ");
				$this->db->where("member_id = '".@$member_id."' AND loan_atm_status ='1' AND loan_atm_status ='1' ");
				$this->db->limit(1);
				$rs_loan_atm = $this->db->get()->result_array();
				$row_loan_atm = @$rs_loan_atm[0];

				if(!empty($row_loan_atm)){
					$loan_atm_id = @$row_loan_atm['loan_atm_id'];
					$member_id = @$row_loan_atm['member_id'];				
					
					$this->db->select(array('loan_id','loan_amount'));
					$this->db->from('coop_loan_atm_detail');
					$this->db->where("loan_atm_id = '".$loan_atm_id ."' AND transaction_at = '1' AND loan_amount = '".$transaction_amount."'");
					$this->db->order_by("loan_id DESC");
					$this->db->limit(1);
					$rs_atm_detail = $this->db->get()->result_array();
					$row_atm_detail = @$rs_atm_detail[0];
					$loan_id = @$row_atm_detail['loan_id'];
					$loan_amount_last = @$row_atm_detail['loan_amount'];

					if(!empty($row_atm_detail)){
						$this->db->where("loan_id = '".@$loan_id."'");
						$this->db->delete('coop_loan_atm_detail');
						
						$total_amount_balance = @$row_loan_atm['total_amount_balance'] + @$transaction_amount;
				
						$loan_amount_balance = @$row_loan_atm['total_amount_approve'] - $total_amount_balance;
						
						$data_insert = array();
						$data_insert['total_amount_balance'] = @$total_amount_balance;
						$this->db->where('loan_atm_id',@$loan_atm_id);
						$this->db->update('coop_loan_atm',$data_insert);
						
						//detail transaction
						
						//ดึงข้อมูลที่มีการบันทึกข้อมูลไปแล้ว เพื่อบันททึกในตาราง error
						$this->db->select(array('loan_atm_transaction_id'));
						$this->db->from('coop_loan_atm_transaction');
						$this->db->where("transaction_datetime = '".@$createdatetime."' AND loan_atm_id = '".@$loan_atm_id."'");
						$this->db->limit(1);
						$transaction_last_error = $this->db->get()->result_array();
						$loan_atm_transaction_id_last = @$transaction_last_error[0]['loan_atm_transaction_id'];
				
						$atm_transaction = array();
						$atm_transaction['loan_atm_id'] = @$loan_atm_id;
						$atm_transaction['loan_amount_balance'] = @$loan_amount_balance;
						$atm_transaction['transaction_datetime'] = @$createdatetime;
						$this->db->insert('coop_loan_atm_transaction',$atm_transaction);	
						$loan_atm_transaction_id = $this->db->insert_id();	
						
						if(@$loan_atm_transaction_id_last != ''){
							$atm_error = array();
							$atm_error['loan_atm_id'] = @$loan_atm_id;
							$atm_error['loan_atm_transaction_id'] = @$loan_atm_transaction_id_last;
							$atm_error['code_error'] = @$transaction_code;
							$atm_error['text_error'] = 'ทำรายการกู้ATM';
							$atm_error['type_error'] = 'atm';
							$atm_error['loan_amount'] = @$loan_amount_last;
							$atm_error['createdatetime'] = @$createdatetime;
							$this->db->insert('coop_loan_atm_transaction_error',$atm_error);
						}
						
						$atm_error = array();
						$atm_error['loan_atm_id'] = @$loan_atm_id;
						$atm_error['loan_atm_transaction_id'] = @$loan_atm_transaction_id;
						$atm_error['code_error'] = @$transaction_code;
						$atm_error['text_error'] = 'ERRA';
						$atm_error['type_error'] = 'atm';
						$atm_error['loan_amount'] = @$transaction_amount;
						$atm_error['createdatetime'] = @$createdatetime;
						$this->db->insert('coop_loan_atm_transaction_error',$atm_error);	
						//detail transaction
						
						$this->db->where("loan_id = '".@$loan_id."'");
						$this->db->delete('coop_loan_atm_transfer');
					
						$result = true;	
					}else{
						$result = false;
					}	
				}else{
					$result = false;
				}
				
			}
			
			if($from_acct_type == '02'){			
				$this->db->select('*');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$customer_coop_account."'");
				$this->db->order_by('transaction_time DESC, transaction_id DESC');
				$this->db->limit(1);
				$row = $this->db->get()->row_array();

				if(!empty($row)){
					$balance = $row['transaction_balance'];
					$balance_no_in = $row['transaction_no_in_balance'];
				}else{
					$balance = 0;
					$balance_no_in = 0;
				}
				$money = @$transaction_amount;
				$sum = $balance + $money ;
				$sum_no_in = $balance_no_in + $money ;

				if($sum > 0) {	
					if($row['transaction_list'] == 'WATM'){
						$data_insert = array();
						$data_insert['transaction_time'] = @$transaction_time;
						$data_insert['transaction_date_atm'] = @$createdatetime;
						$data_insert['transaction_list'] = 'ERRA';
						$data_insert['transaction_withdrawal'] = '';
						$data_insert['transaction_deposit'] = @$money;
						$data_insert['transaction_balance'] = @$sum;
						$data_insert['transaction_no_in_balance'] = @$sum_no_in;
						$data_insert['member_id_atm'] = @$member_id;
						$data_insert['account_id'] = @$customer_coop_account;
						$data_insert['company_atm'] = 'KTB';
						
						if($this->db->insert('coop_account_transaction', $data_insert)){
							//echo $this->db->last_query();
							
							// Line notify
							/*$lineconnect = new LineConnect();
							$lineconnect->sendNotifyDeposit([
								"account_id" => $data_insert['account_id'],					// เลขบัญชี
								"transaction_time" => $data_insert['transaction_time'],		// วันเวลาทำรายการ
								"deposit" => $data_insert['transaction_deposit'],			// ฝาก
								"withdrawal" => 0,											// ถอน
								"balance" => $data_insert['transaction_balance']			// คงเหลือ
							]);
							*/
							$result = true;
						}else{
							$result = false;
						}					
					}else{
						$result = true;
					}				
				}else{
					$result = false;
				}
			}
		
		}else{
			$result = false;
		}
		
		//ส่งเมล์
		$subject = "reversal ATM สป";
		$mail_detail = "";
		$mail_detail .= "customer_coop_account=".$customer_coop_account."<br>";		
		$mail_detail .= "transaction_amount=".$transaction_amount."<br>";
		$mail_detail .= "createdatetime=".$createdatetime."<br>";	
		$to = "tukky2710@gmail.com";
		$this->center_function->send_mj_mail($subject, $mail_detail, $to);
		
		return $result;
	}	
	
	//reversal คืนหนี้ให้ลูกค้า
	public function reversal_refund_increase($arr_data_reversal){	
		$customer_coop_account = @$arr_data_reversal['customer_coop_account'];//เลขบัญชีธนาคาร
		$from_acct_type = @$arr_data_reversal['from_acct_type'];
		$transaction_amount = @$arr_data_reversal['transaction_amount'];
		$createdatetime = @$arr_data_reversal['createdatetime'];
		$transaction_code = @$arr_data_reversal['transaction_code'];	
		$transaction_time = date('Y-m-d H:i:s');

		//get member_id
		$member_id = @$arr_data_reversal['coop_member_id'];	

		//from_acct_type 01 = เงินกู้ ,02 = เงินฝาก
		if($from_acct_type == '01'){
			$this->db->select(array('loan_atm_id','member_id','total_amount_approve','total_amount_balance'));
			$this->db->from('coop_loan_atm');
			//$this->db->where("account_id = '".@$customer_coop_account."' AND loan_atm_status ='1' ");
			$this->db->where("member_id = '".@$member_id."' AND loan_atm_status ='1' ");
			$this->db->limit(1);
			$rs_loan_atm = $this->db->get()->result_array();
			$row_loan_atm = @$rs_loan_atm[0];

			if(!empty($row_loan_atm)){
				$loan_atm_id = @$row_loan_atm['loan_atm_id'];
				$member_id = @$row_loan_atm['member_id'];				
				
				/*$this->db->select(array('loan_id','loan_amount'));
				$this->db->from('coop_loan_atm_detail');
				$this->db->where("loan_atm_id = '".$loan_atm_id ."' AND transaction_at = '1' AND loan_amount = '".$transaction_amount."'");
				$this->db->order_by("loan_id DESC");
				$this->db->limit(1);
				$rs_atm_detail = $this->db->get()->result_array();
				$row_atm_detail = @$rs_atm_detail[0];
				$loan_id = @$row_atm_detail['loan_id'];
				$loan_amount_last = @$row_atm_detail['loan_amount'];

				if(!empty($row_atm_detail)){
					*/
					//$this->db->where("loan_id = '".@$loan_id."'");
					//$this->db->delete('coop_loan_atm_detail');
					
					$total_amount_balance = @$row_loan_atm['total_amount_balance'] - @$transaction_amount;
					
					$loan_amount_balance = @$row_loan_atm['total_amount_approve'] - $total_amount_balance;
					
					$data_insert = array();
					$data_insert['total_amount_balance'] = @$total_amount_balance;
					$this->db->where('loan_atm_id',@$loan_atm_id);
					$this->db->update('coop_loan_atm',$data_insert);
					
					//detail transaction
					
					//ดึงข้อมูลที่มีการบันทึกข้อมูลไปแล้ว เพื่อบันททึกในตาราง error
					$this->db->select(array('loan_atm_transaction_id'));
					$this->db->from('coop_loan_atm_transaction');
					$this->db->where("transaction_datetime = '".@$createdatetime."' AND loan_atm_id = '".@$loan_atm_id."'");
					$this->db->limit(1);
					$transaction_last_error = $this->db->get()->result_array();
					$loan_atm_transaction_id_last = @$transaction_last_error[0]['loan_atm_transaction_id'];
			
					$atm_transaction = array();
					$atm_transaction['loan_atm_id'] = @$loan_atm_id;
					$atm_transaction['loan_amount_balance'] = @$loan_amount_balance;
					$atm_transaction['transaction_datetime'] = @$createdatetime;
					$this->db->insert('coop_loan_atm_transaction',$atm_transaction);	
					$loan_atm_transaction_id = $this->db->insert_id();	
					
					if(@$loan_atm_transaction_id_last != ''){
						$atm_error = array();
						$atm_error['loan_atm_id'] = @$loan_atm_id;
						$atm_error['loan_atm_transaction_id'] = @$loan_atm_transaction_id_last;
						$atm_error['code_error'] = @$transaction_code;
						$atm_error['text_error'] = 'ชำระเงินกู้ ATM';
						$atm_error['type_error'] = 'atm';
						$atm_error['loan_amount'] = @$loan_amount_last;
						$atm_error['createdatetime'] = @$createdatetime;
						$this->db->insert('coop_loan_atm_transaction_error',$atm_error);
					}
					
					$atm_error = array();
					$atm_error['loan_atm_id'] = @$loan_atm_id;
					$atm_error['loan_atm_transaction_id'] = @$loan_atm_transaction_id;
					$atm_error['code_error'] = @$transaction_code;
					$atm_error['text_error'] = 'ERRA';
					$atm_error['type_error'] = 'atm';
					$atm_error['loan_amount'] = @$transaction_amount;
					$atm_error['createdatetime'] = @$createdatetime;
					$this->db->insert('coop_loan_atm_transaction_error',$atm_error);	
					//detail transaction
					
					//$this->db->where("loan_id = '".@$loan_id."'");
					//$this->db->delete('coop_loan_atm_transfer');
				
					$result = true;	
				//}else{
				//	$result = false;
				//}	
			}else{
				$result = false;
			}
			
		}
		
		if($from_acct_type == '02'){			
			$this->db->select('*');
			$this->db->from('coop_account_transaction');
			//$this->db->where("account_id = '".$customer_coop_account."' AND transaction_time = '".@$createdatetime."'");
			$this->db->where("account_id = '".$customer_coop_account."'");
			$this->db->order_by('transaction_time DESC, transaction_id DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			
			if(!empty($row)){
				$balance = $row[0]['transaction_balance'];
				$balance_no_in = $row[0]['transaction_no_in_balance'];
			}else{
				$balance = 0;
				$balance_no_in = 0;
			}
			$money = @$transaction_amount;
			$sum = $balance - $money ;
			$sum_no_in = $balance_no_in - $money ;

			if($sum > 0) {	
				$data_insert = array();
				$data_insert['transaction_time'] = @$transaction_time;
				$data_insert['transaction_date_atm'] = @$createdatetime;
				$data_insert['transaction_list'] = 'ERRA';
				$data_insert['transaction_withdrawal'] = @$money;
				$data_insert['transaction_deposit'] = '';
				$data_insert['transaction_balance'] = @$sum;
				$data_insert['transaction_no_in_balance'] = @$sum_no_in;
				$data_insert['member_id_atm'] = @$member_id;
				$data_insert['account_id'] = @$customer_coop_account;
				$data_insert['company_atm'] = 'KTB';
				
				if($this->db->insert('coop_account_transaction', $data_insert)){
					//echo $this->db->last_query();
					/*
					// Line notify
					$lineconnect = new LineConnect();
					$lineconnect->sendNotifyDeposit([
						"account_id" => $data_insert['account_id'],					// เลขบัญชี
						"transaction_time" => $data_insert['transaction_time'],		// วันเวลาทำรายการ
						"deposit" => 0,												// ฝาก
						"withdrawal" => $data_insert['transaction_withdrawal'],		// ถอน
						"balance" => $data_insert['transaction_balance']			// คงเหลือ
					]);
					*/
					$result = true;
				}else{
					$result = false;
				}
				
			}else{
				$result = false;
			}
		}
		
		//ส่งเมล์
		$subject = "reversal ATM สป";
		$mail_detail = "";
		$mail_detail .= "customer_coop_account=".$customer_coop_account."<br>";		
		$mail_detail .= "transaction_amount=".$transaction_amount."<br>";
		$mail_detail .= "createdatetime=".$createdatetime."<br>";	
		$to = "tukky2710@gmail.com";
		$this->center_function->send_mj_mail($subject, $mail_detail, $to);
		
		return $result;
	}
	
	//reversal คืนหนี้ให้ลูกค้า
	public function reversal_refund_increase_bk_2020_09_28_new_test($arr_data_reversal){	
		$customer_coop_account = @$arr_data_reversal['customer_coop_account'];//เลขบัญชีธนาคาร
		$from_acct_type = @$arr_data_reversal['from_acct_type'];
		$transaction_amount = @$arr_data_reversal['transaction_amount'];
		$createdatetime = @$arr_data_reversal['createdatetime'];
		$transaction_code = @$arr_data_reversal['transaction_code'];	
		$transaction_time = date('Y-m-d H:i:s');

		//get member_id
		$member_id = @$arr_data_reversal['coop_member_id'];	

		//from_acct_type 01 = เงินกู้ ,02 = เงินฝาก
		if($from_acct_type == '01'){
			$this->db->select(array('loan_atm_id','member_id','total_amount_approve','total_amount_balance'));
			$this->db->from('coop_loan_atm');
			//$this->db->where("account_id = '".@$customer_coop_account."' AND loan_atm_status ='1' ");
			$this->db->where("member_id = '".@$member_id."' AND loan_atm_status ='1' ");
			$this->db->limit(1);
			$rs_loan_atm = $this->db->get()->result_array();
			$row_loan_atm = @$rs_loan_atm[0];

			if(!empty($row_loan_atm)){
				$loan_atm_id = @$row_loan_atm['loan_atm_id'];
				$member_id = @$row_loan_atm['member_id'];				
				
				/*$this->db->select(array('loan_id','loan_amount'));
				$this->db->from('coop_loan_atm_detail');
				$this->db->where("loan_atm_id = '".$loan_atm_id ."' AND transaction_at = '1' AND loan_amount = '".$transaction_amount."'");
				$this->db->order_by("loan_id DESC");
				$this->db->limit(1);
				$rs_atm_detail = $this->db->get()->result_array();
				$row_atm_detail = @$rs_atm_detail[0];
				$loan_id = @$row_atm_detail['loan_id'];
				$loan_amount_last = @$row_atm_detail['loan_amount'];

				if(!empty($row_atm_detail)){
					*/
					//$this->db->where("loan_id = '".@$loan_id."'");
					//$this->db->delete('coop_loan_atm_detail');
					
					//$this->db->set("total_amount_balance", "(SELECT total_amount_balance - $amount FROM (SELECT * FROM coop_loan_atm WHERE loan_atm_id = ".$loan_atm_id.") AS t1 )", false);
					//$this->db->set("loan_atm_status", "1", false);
					//$this->db->where("loan_atm_id", $loan_atm_id);
					//$this->db->update("coop_loan_atm");
					
					//ดึงข้อมูลที่มีการบันทึกข้อมูลไปแล้ว เพื่อบันททึกในตาราง error
					//update receipt_status					
					$chek_receipt = $this->db->select(array('receipt_id','interest_from','loan_atm_transaction_id'))->from('coop_loan_atm_transaction')
									->where("transaction_datetime = '".@$createdatetime."' AND loan_atm_id = '".@$loan_atm_id."'")
									->limit(1)->get()->row_array();
					$receipt_id = @$chek_receipt['receipt_id'];
					$date_last_interest = @$chek_receipt['interest_from'];
					
					/////
					if(@$receipt_id != ''){
						$chek_finance_transaction = $this->db->select(array('principal_payment','interest'))
										->from('coop_finance_transaction')
										->where("receipt_id = '".@$receipt_id."' AND loan_atm_id = '".@$loan_atm_id."'")
										->limit(1)->get()->row_array();
						$amount = $chek_finance_transaction["principal_payment"];
						$this->db->where("loan_amount != loan_amount_balance");
						$this->db->order_by("loan_atm_id", "DESC");
						$loan_atm_detail = $this->db->get_where("coop_loan_atm_detail", array("loan_atm_id" =>  $loan_atm_id) );
						$row_loan_atm_detail = $loan_atm_detail->result();
						//echo '<pre>'; print_r($row_loan_atm_detail); echo '</pre>';
						//foreach ($loan_atm_detail->result() as $key => $value) {
						foreach ($row_loan_atm_detail as $key => $value) {
							echo '<pre>'; print_r($value); echo '</pre>';
							$data_insert = array();
							if($amount <= 0)
								break;
							if($value->loan_amount_balance + $amount <= $value->loan_amount){
								//$this->db->set("loan_amount_balance", ($value->loan_amount_balance + $amount));
								$detail_balance = $value->loan_amount_balance + $amount;
							}else{
								//$this->db->set("loan_amount_balance", $value->loan_amount);
								$detail_balance = $value->loan_amount;
								$amount = $amount - ($value->loan_amount - $value->loan_amount_balance);
							}
							$data_insert['loan_amount_balance'] = @$detail_balance;
							//echo '<pre>'; print_r($data_insert); echo '</pre>';
							//$this->db->where("loan_id", $value->loan_id);
							//$this->db->update("coop_loan_atm_detail");
							//$data_insert['loan_id'] = @$createdatetime;
							//$this->db->where('loan_id',$value->loan_id;
							//$this->db->update('coop_loan_atm_detail',$data_insert);
						}
					}
					exit;
					
					$data_insert = array();
					$data_insert['receipt_status'] = 2;
					$data_insert['cancel_date'] = @$createdatetime;
					$this->db->where('receipt_id',@$receipt_id);
					$this->db->update('coop_receipt',$data_insert);
					
					
					$total_amount_balance = @$row_loan_atm['total_amount_balance'] - @$transaction_amount;
					$loan_amount_balance = @$row_loan_atm['total_amount_approve'] - $total_amount_balance;
					
					$data_insert = array();
					$data_insert['total_amount_balance'] = @$total_amount_balance;
					$data_insert['date_last_interest'] = @$date_last_interest;
					$this->db->where('loan_atm_id',@$loan_atm_id);
					$this->db->update('coop_loan_atm',$data_insert);
					
					//detail transaction
					
					//ดึงข้อมูลที่มีการบันทึกข้อมูลไปแล้ว เพื่อบันททึกในตาราง error
					//$this->db->select(array('loan_atm_transaction_id'));
					//$this->db->from('coop_loan_atm_transaction');
					//$this->db->where("transaction_datetime = '".@$createdatetime."' AND loan_atm_id = '".@$loan_atm_id."'");
					//$this->db->limit(1);
					//$transaction_last_error = $this->db->get()->result_array();
					//$loan_atm_transaction_id_last = @$transaction_last_error[0]['loan_atm_transaction_id'];
					$loan_atm_transaction_id_last = @$chek_receipt['loan_atm_transaction_id'];					
					
					$atm_transaction = array();
					$atm_transaction['loan_atm_id'] = @$loan_atm_id;
					$atm_transaction['loan_amount_balance'] = @$loan_amount_balance;
					$atm_transaction['transaction_datetime'] = @$createdatetime;
					$this->db->insert('coop_loan_atm_transaction',$atm_transaction);	
					$loan_atm_transaction_id = $this->db->insert_id();	
					
					if(@$loan_atm_transaction_id_last != ''){
						//ลบข้อมูลใน coop_loan_atm_transaction เพื่อไม่ให้กระทบกับการคำนวณดอกเบี้ย
						$this->db->where("loan_atm_transaction_id = '".@$loan_atm_transaction_id_last."'");
						$this->db->delete('coop_loan_atm_transaction');
			
						$atm_error = array();
						$atm_error['loan_atm_id'] = @$loan_atm_id;
						$atm_error['loan_atm_transaction_id'] = @$loan_atm_transaction_id_last;
						$atm_error['code_error'] = @$transaction_code;
						$atm_error['text_error'] = 'ชำระเงินกู้ ATM';
						$atm_error['type_error'] = 'atm';
						$atm_error['loan_amount'] = @$loan_amount_last;
						$atm_error['createdatetime'] = @$createdatetime;
						$atm_error['receipt_id'] = @$receipt_id;
						$this->db->insert('coop_loan_atm_transaction_error',$atm_error);
					}
					
					$atm_error = array();
					$atm_error['loan_atm_id'] = @$loan_atm_id;
					$atm_error['loan_atm_transaction_id'] = @$loan_atm_transaction_id;
					$atm_error['code_error'] = @$transaction_code;
					$atm_error['text_error'] = 'ERRA';
					$atm_error['type_error'] = 'atm';
					$atm_error['loan_amount'] = @$transaction_amount;
					$atm_error['createdatetime'] = @$createdatetime;
					$atm_error['receipt_id'] = @$receipt_id;
					$this->db->insert('coop_loan_atm_transaction_error',$atm_error);	
					//detail transaction
					
					//$this->db->where("loan_id = '".@$loan_id."'");
					//$this->db->delete('coop_loan_atm_transfer');
				
					$result = true;	
				//}else{
				//	$result = false;
				//}	
			}else{
				$result = false;
			}
			
		}
		
		if($from_acct_type == '02'){			
			$this->db->select('*');
			$this->db->from('coop_account_transaction');
			//$this->db->where("account_id = '".$customer_coop_account."' AND transaction_time = '".@$createdatetime."'");
			$this->db->where("account_id = '".$customer_coop_account."'");
			$this->db->order_by('transaction_time DESC, transaction_id DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			
			if(!empty($row)){
				$balance = $row[0]['transaction_balance'];
				$balance_no_in = $row[0]['transaction_no_in_balance'];
			}else{
				$balance = 0;
				$balance_no_in = 0;
			}
			$money = @$transaction_amount;
			$sum = $balance - $money ;
			$sum_no_in = $balance_no_in - $money ;

			if($sum > 0) {	
				$data_insert = array();
				$data_insert['transaction_time'] = @$transaction_time;
				$data_insert['transaction_date_atm'] = @$createdatetime;
				$data_insert['transaction_list'] = 'ERRA';
				$data_insert['transaction_withdrawal'] = @$money;
				$data_insert['transaction_deposit'] = '';
				$data_insert['transaction_balance'] = @$sum;
				$data_insert['transaction_no_in_balance'] = @$sum_no_in;
				$data_insert['member_id_atm'] = @$member_id;
				$data_insert['account_id'] = @$customer_coop_account;
				$data_insert['company_atm'] = 'KTB';
				
				if($this->db->insert('coop_account_transaction', $data_insert)){
					//echo $this->db->last_query();
					
					// Line notify
					/*$lineconnect = new LineConnect();
					$lineconnect->sendNotifyDeposit([
						"account_id" => $data_insert['account_id'],					// เลขบัญชี
						"transaction_time" => $data_insert['transaction_time'],		// วันเวลาทำรายการ
						"deposit" => 0,												// ฝาก
						"withdrawal" => $data_insert['transaction_withdrawal'],		// ถอน
						"balance" => $data_insert['transaction_balance']			// คงเหลือ
					]);
					*/
					$result = true;
				}else{
					$result = false;
				}
				
			}else{
				$result = false;
			}
		}
		
		//ส่งเมล์
		$subject = "reversal ATM สป";
		$mail_detail = "";
		$mail_detail .= "customer_coop_account=".$customer_coop_account."<br>";		
		$mail_detail .= "transaction_amount=".$transaction_amount."<br>";
		$mail_detail .= "createdatetime=".$createdatetime."<br>";	
		$to = "tukky2710@gmail.com";
		$this->center_function->send_mj_mail($subject, $mail_detail, $to);
		
		return $result;
	}

	public function convert_datetime($transaction_date,$transaction_time){
		if($transaction_date != '' && $transaction_time != ''){
			//วันที่เวลาทำรายการ 
			$transactionDate = $transaction_date;
			$transaction_yy = substr(@$transactionDate,0,4);//ปี
			$transaction_mm = substr(@$transactionDate,4,2);//เดือน
			$transaction_dd = substr(@$transactionDate,6,2);//วัน
			$transaction_h = substr(@$transaction_time,0,2);//ชั่วโมง
			$transaction_i = substr(@$transaction_time,2,2);//นาที
			$transaction_s = substr(@$transaction_time,4,2);//วินาที
			$createdatetime = $transaction_yy.'-'.$transaction_mm.'-'.$transaction_dd.' '.$transaction_h.':'.$transaction_i.':'.$transaction_s;	
		}else{
			$createdatetime = date('Y-m-d H:i:s');
		}
		return $createdatetime;
	}
	
	//ถอนเงิน/ชำระเงินกู้
	public function depositTxn($get_request_data){
		$account_id = @$this->convert_account(@$get_request_data['customer_coop_account'],@$get_request_data['coop_member_id']);
		//echo 'account_id='.$account_id.'<br>';
		
		//AvaliableBalanceCheck ยอดเงินที่สามารถทำรายการได้
		$avaliable_balance_check = $this->avaliable_balance(@$account_id,@$get_request_data['from_acct_type'],@$get_request_data['coop_member_id']);		

		$transactionYYmmdd = $get_request_data['transaction_date'];
		$response_code = $this->response_code(@$get_request_data,$avaliable_balance_check,$transactionYYmmdd);

		//วันที่เวลาทำรายการ จากการขอทำรายการ
		$createdatetime = $this->convert_datetime(@$get_request_data['transaction_date'],@$get_request_data['transaction_time']);
		//exit;	
		
		if(@$get_request_data['transaction_code'] == '0200' && $response_code == '0000'){		
			//บันทึกการถอนเงิน	
			$arr_data_withdrawl = array();
			$arr_data_withdrawl['coop_member_id'] = @$get_request_data['coop_member_id'];
			//$arr_data_withdrawl['customer_coop_account'] = @$get_request_data['customer_coop_account'];
			$arr_data_withdrawl['customer_coop_account'] = @$account_id;
			$arr_data_withdrawl['from_acct_type'] = @$get_request_data['from_acct_type'];
			$arr_data_withdrawl['transaction_amount'] = $this->text_to_decimal(@$get_request_data['transaction_amount']);//ยอดเงินที่ถอน
			$arr_data_withdrawl['createdatetime'] = $createdatetime;
			
			$result_save_withdrawl = $this->saveDepositTxn(@$arr_data_withdrawl);
			if($result_save_withdrawl == '1'){
				$response_code = '0000';
			}else{
				$response_code = '0011';
			}
		}else if(@$get_request_data['transaction_code'] == '0400' && $response_code == '0000'){
			//echo '==================reversal================<br>';
			//reversal ต้องคืนเงินให้ลูกค้า
			$arr_data_reversal = array();
			$arr_data_reversal['coop_member_id'] = @$get_request_data['coop_member_id'];
			//$arr_data_reversal['customer_coop_account'] = @$get_request_data['customer_coop_account'];
			$arr_data_reversal['customer_coop_account'] = @$account_id;
			$arr_data_reversal['from_acct_type'] = @$get_request_data['from_acct_type'];
			$arr_data_reversal['transaction_amount'] = $this->text_to_decimal(@$get_request_data['transaction_amount']);//ยอดเงินที่ถอน
			$arr_data_reversal['createdatetime'] = $createdatetime;
			$arr_data_reversal['transaction_code'] = @$get_request_data['transaction_code'];		
			$reversal = $this->reversal_refund_increase(@$arr_data_reversal);

			if($reversal == '1'){
				$response_code = '0000';
			}else{				
				//067 invalid cash back amt -> เงินคืนที่ไม่ถูกต้อง
				$response_code = '0011';
			}
			
		}
		
		//AvaliableBalance ยอดเงินที่สามารถทำรายการได้		
		//$avaliable_balance = $this->avaliable_balance(@$get_request_data['customer_coop_account'],@$get_request_data['from_acct_type'],@$get_request_data['coop_member_id']);
		$avaliable_balance = $this->avaliable_balance(@$account_id,@$get_request_data['from_acct_type'],@$get_request_data['coop_member_id']);

		//ยอดหนี้เงินกู้คงเหลือ
		//$loan_payment_balance = $this->loan_payment_balance(@$get_request_data['customer_coop_account'],@$get_request_data['from_acct_type'],@$get_request_data['coop_member_id']);
		$loan_payment_balance = $this->loan_payment_balance(@$account_id,@$get_request_data['from_acct_type'],@$get_request_data['coop_member_id']);

		//$loan_payment_balance = $avaliable_balance;
		$transaction_code = $this->message_type(@$get_request_data['transaction_code']);

		// response	
		$res_save = array(
			'transaction_code'=>@$transaction_code,
			'bank_reference_number'=>@$get_request_data['bank_reference_number'],
			'transaction_date'=>@$get_request_data['transaction_date'],
			'transaction_time'=>@$get_request_data['transaction_time'],
			'bank_code'=>@$get_request_data['bank_code'],
			'filler_1'=>@$get_request_data['filler_1'],
			'transaction_amount'=>@$get_request_data['transaction_amount'], 
			'list_id'=>@$get_request_data['list_id'],
			'from_acct_type'=>@$get_request_data['from_acct_type'],
			'coop_code'=>@$get_request_data['coop_code'],
			'overdraft_available'=>@$avaliable_balance,
			'filler_2'=>@$get_request_data['filler_2'],
			'coop_member_id'=>@$get_request_data['coop_member_id'],
			'company_account'=>@$get_request_data['company_account'],
			'customer_coop_account'=>@$get_request_data['customer_coop_account'],
			'payment_channel'=>@$get_request_data['payment_channel'],
			'response_code'=>@$response_code,
			'loan_payment_balance'=>@$loan_payment_balance,
			'posted_date'=>@$get_request_data['posted_date']
		);

		//บันทึก response
		$save_response = $this->save_response($res_save);

		$res = $this->gen_text_response($res_save);	

		return ($res);
	}	
	
	//บันทึกการฝากเงิน
	public function saveDepositTxn($arr_data){
		//from_acct_type 01 = เงินกู้   ,02 = เงินฝาก
		$from_acct_type = $arr_data['from_acct_type'];
		if($from_acct_type == '01'){
			$result = $this->saveDepositTxnLoan($arr_data);
		}
		
		if($from_acct_type == '02'){			
			$result = $this->saveDepositTxnSaving($arr_data);
		}
		
		return $result;
	}

	//บันทึกการฝากเงินจากบัญชี
	public function saveDepositTxnSaving($arr_data){
		//$result = true;	
		$customer_coop_account = @$arr_data['customer_coop_account'];
		$transaction_amount = @$arr_data['transaction_amount'];
		$createdatetime = @$arr_data['createdatetime'];
		$transaction_time = date('Y-m-d H:i:s');
		
		//get member_id
		$member_id = @$arr_data['coop_member_id'];

		$this->db->select('*');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$customer_coop_account."'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		if(!empty($row)){
			$balance = $row[0]['transaction_balance'];
			$balance_no_in = $row[0]['transaction_no_in_balance'];
		}else{
			$balance = 0;
			$balance_no_in = 0;
		}
		
		$money = @$transaction_amount;
		$sum = @$balance + @$money;
		$sum_no_in = @$balance_no_in + @$money;
		if($sum_no_in <= 0 ){$sum_no_in = 0;}
		if($sum > 0) {	
			$data_insert = array();
			$data_insert['transaction_time'] = @$transaction_time;
			$data_insert['transaction_date_atm'] = @$createdatetime;
			$data_insert['transaction_list'] = 'XD';
			$data_insert['transaction_withdrawal'] = '';
			$data_insert['transaction_deposit'] = @$money;
			$data_insert['transaction_balance'] = @$sum;
			$data_insert['transaction_no_in_balance'] = @$sum_no_in;
			$data_insert['member_id_atm'] = @$member_id;
			$data_insert['account_id'] = @$customer_coop_account;
			$data_insert['company_atm'] = 'KTB';

			if($this->db->insert('coop_account_transaction', $data_insert)){
				//check save
				$this->db->select('transaction_time,transaction_list,transaction_withdrawal,transaction_deposit,account_id');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$customer_coop_account."' AND transaction_time = '".@$transaction_time."' AND transaction_deposit = '".@$money."'");
				$this->db->order_by('transaction_time DESC, transaction_id DESC');
				$this->db->limit(1);
				$rs_account = $this->db->get()->result_array();
				
				$row_account = $rs_account[0];
				if($row_account['transaction_time'] != ''){
					$result = true;
					
					// Line notify
					/*$lineconnect = new LineConnect();
					$lineconnect->sendNotifyDeposit([
						"account_id" => $data_insert['account_id'],							// เลขบัญชี
						"transaction_time" => $data_insert['transaction_time'],		// วันเวลาทำรายการ
						"deposit" => $data_insert['transaction_deposit'],					// ฝาก
						"withdrawal" => 0,																	// ถอน
						"balance" => $data_insert['transaction_balance']				// คงเหลือ
					]);
					*/
				}else{
					$result = false;
				}
				
			}else{
				$result = false;
			}					
		}	
			
		return $result;
	}
	
	//บันทึกการ ชำระเงินกู้ เงินกู้ ATM
	public function saveDepositTxnLoan($arr_data){
		//$result = true;	
		$result = false;
		//ปิดไว้เนื่องจาก สป ไม่ได้
		$customer_coop_account = $arr_data['customer_coop_account'];
		$from_acct_type = $arr_data['from_acct_type'];
		$transaction_amount = $arr_data['transaction_amount'];
		$createdatetime = $arr_data['createdatetime'];
		//get member_id
		$member_id = $arr_data['coop_member_id'];
		

		$this->db->select('*');
		$this->db->from("coop_loan_atm_setting");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$row_setting = @$row[0];
		
		$this->db->select(array('loan_atm_id','member_id','total_amount_approve','total_amount_balance'));
		$this->db->from('coop_loan_atm');
		//$this->db->where("account_id = '".@$customer_coop_account."' AND loan_atm_status ='1' ");
		$this->db->where("member_id = '".@$member_id."' AND loan_atm_status ='1' ");
		$this->db->limit(1);
		$rs_loan_atm = $this->db->get()->result_array();
		$row_loan_atm = @$rs_loan_atm[0];
		//echo $this->db->last_query(); echo '<br>';
		//exit;
		if(!empty($row_loan_atm)){
			$loan_atm_id = @$row_loan_atm['loan_atm_id'];
				
			$total_amount_balance = @$row_loan_atm['total_amount_balance'] + @str_replace(',','',$transaction_amount);

			$loan_amount_balance = @$row_loan_atm['total_amount_approve'] - $total_amount_balance;
			
			if($loan_amount_balance >= 0){
				$data_insert = array();
				$data_insert['total_amount_balance'] = @$total_amount_balance;
				$this->db->where('loan_atm_id',@$loan_atm_id);
				$this->db->update('coop_loan_atm',$data_insert);
				
				$atm_transaction = array();
				$atm_transaction['loan_atm_id'] = @$loan_atm_id;
				$atm_transaction['loan_amount_balance'] = @$loan_amount_balance;
				$atm_transaction['transaction_datetime'] = @$createdatetime;
				//$this->loan_libraries->atm_transaction($atm_transaction);
                $this->loan_libraries->atm_transaction_arrears('AT', $atm_transaction);

				$result = true;
			}else{
				$result = false;
			}
		}	
		
		return $result;
		
	}
	
	//บันทึกการ ชำระเงินกู้ เงินกู้ ATM
	public function saveDepositTxnLoan_bk_2020_09_28_test($arr_data){
		//$result = true;	
		$result = false;
		//ปิดไว้เนื่องจาก สป ไม่ได้
		$customer_coop_account = $arr_data['customer_coop_account']; //account_id
		$from_acct_type = $arr_data['from_acct_type'];
		$transaction_amount = @str_replace(',','',$arr_data['transaction_amount']);
		$createdatetime = $arr_data['createdatetime'];
		//get member_id
		$member_id = $arr_data['coop_member_id'];

		$this->db->select('*');
		$this->db->from("coop_loan_atm_setting");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$row_setting = @$row[0];
		
		$this->db->select(array('loan_atm_id','member_id','total_amount_approve','total_amount_balance','date_last_interest'));
		$this->db->from('coop_loan_atm');
		//$this->db->where("account_id = '".@$customer_coop_account."' AND loan_atm_status ='1' ");
		$this->db->where("member_id = '".@$member_id."' AND loan_atm_status ='1' ");
		$this->db->limit(1);
		$row_loan_atm = $this->db->get()->row_array();

		$cal_interest_loan = 0;
		if(!empty($row_loan_atm)){
			$loan_atm_id = @$row_loan_atm['loan_atm_id'];		
			
			$total_amount_balance_old = @$row_loan_atm['total_amount_balance'];	
			
			//@start คำนวณดอกเบี้ยเงินกู้ ATM ณ วันที่่ ทำรายการชำระเงินกู้
			$data_loan = array();
			$data_loan['process'] = 'atm_online';
			$data_loan['loan_id'] = $loan_atm_id;
			$data_loan['loan_amount_balance'] = $total_amount_balance_old;
			$data_loan['pay_loan_type'] = 'atm';
			$data_loan['date_last_interest'] = $row_loan_atm['date_last_interest'];
			$data_loan['member_id'] = $member_id;
			$data_loan['account_id'] = $customer_coop_account;
			$data_loan['pay_amount'] = $transaction_amount;
			$data_loan['fix_date'] = $this->center_function->mydate2date(date("Y-m-d", strtotime($createdatetime))); //วันที่ทำรายการ 
			$data_loan['fix_time'] = date("H:i:s", strtotime($createdatetime));
			//echo '<pre>'; print_r($data_loan); echo '</pre>';
			//check interest
			$get_cal_loan_atm = $this->cashier_loan->get_cal_loan_atm($data_loan);
			$cal_interest_loan = ROUND($get_cal_loan_atm['interest'],2);
			$data_loan['interest_all'] = $cal_interest_loan;
			//$data_loan['fix_date'] = date("Y-m-d", strtotime($createdatetime));
			//$data_loan['fix_time'] = date("H:i:s", strtotime($createdatetime));
			//echo 'cal_interest_loan='.$cal_interest_loan.'<br>';
			//echo '<pre>'; print_r($data_loan); echo '</pre>';
			//echo '<pre>'; print_r($get_cal_loan_atm); echo '</pre>'; //exit;
			//@end คำนวณดอกเบี้ยเงินกู้ ATM ณ วันที่่ ทำรายการชำระเงินกู้			
			
			$pay_transaction = ROUND((@$transaction_amount-@$cal_interest_loan),2);
			$total_amount_balance = @$row_loan_atm['total_amount_balance'] + @$pay_transaction;
			$loan_amount_balance = ROUND(@$row_loan_atm['total_amount_approve'] - @$total_amount_balance,2);

			if($loan_amount_balance >= 0){
				$arr_data = $this->cashier_loan->gen_arr_insert($data_loan);
				//บันทึกข้อมูลใบเสร็จ
				$chek_save = $this->cashier_loan->save_receipt($arr_data);
				if($chek_save['affected_rows'] < 1){
					$result = false;
				}else{
					$result = true;
				}	
				/*$data_insert = array();
				$data_insert['total_amount_balance'] = @$total_amount_balance;
				$this->db->where('loan_atm_id',@$loan_atm_id);
				$this->db->update('coop_loan_atm',$data_insert);
				
				$atm_transaction = array();
				$atm_transaction['loan_atm_id'] = @$loan_atm_id;
				$atm_transaction['loan_amount_balance'] = @$loan_amount_balance;
				$atm_transaction['transaction_datetime'] = @$createdatetime;
				$this->loan_libraries->atm_transaction($atm_transaction);
				*/

				//$result = true;
			}else{
				$result = false;
			}
		}	
		//exit;
		return $result;
		
	}

	//ค่าธรรมเนียมกดเงินกินจำนวนครั้ง
	public function get_amount_fee($arr_data){								
		$coop_member_id = @$arr_data['coop_member_id'];
		$transaction_date = @$arr_data['transaction_date'];
		$transaction_code = @$arr_data['transaction_code'];
		$list_id = @$arr_data['list_id'];		
		$response_code = @$arr_data['response_code'];
		//วันที่เวลาทำรายการ จากการขอทำรายการ
		$createdatetime = $this->convert_datetime(@$arr_data['transaction_date'],@$arr_data['transaction_time']);
		
		$amount_fee = 0;
		if($transaction_code == '0200' AND $list_id == '002'){
			//วันปัจจุบันที่ทำรายการ  $transactionYYmmdd;		
			$this->db->select(array('transaction_amount','response_code','transaction_code'));
			$this->db->from('message_response_atm_ktb');		
			$this->db->where("coop_member_id = '".$coop_member_id."' AND transaction_date = '".$transaction_date."' AND list_id = '".$list_id."'");			
			$rs_response_atm = $this->db->get()->result_array();
			
			$count_atm_success = 0; 
			foreach($rs_response_atm AS $key_1=>$row_response_atm){
				if(@$row_response_atm['transaction_code'] == '0210' AND @$row_response_atm['response_code'] == '0000'){
					$count_atm_success++;
				}else if(@$row_response_atm['transaction_code'] == '0410' AND @$row_response_atm['response_code'] == '0000'){
					$count_atm_success--;
				}
			}
			
			$this->db->select(array('use_atm_count','use_atm_over_count_fee'));
			$this->db->from('coop_loan_atm_setting');
			$this->db->limit(1);
			$rs_atm_setting = $this->db->get()->row_array();
			$row_atm_setting = @$rs_atm_setting;
			if(!empty($row_atm_setting)){
				$use_atm_count = @$row_atm_setting['use_atm_count']; //ถอนเงิน ฟรี x ครั้งต่อเดือน 
				$use_atm_over_count_fee = @$row_atm_setting['use_atm_over_count_fee']; //เกิน x ครั้ง  นั้นมีค่าบริการ
			}
			
			if($count_atm_success > $use_atm_count){
				$amount_fee = $use_atm_over_count_fee;
			}else{
				$amount_fee = 0;
			}
		}
			
		return $amount_fee;
		
	}
	
	public function convert_account($customer_coop_account,$coop_member_id){
		//$account_id = sprintf("%011d", @$customer_coop_account);
		$this->db->select('account_id');
		$this->db->from('coop_maco_account');
		$this->db->where("account_id_atm = '".$customer_coop_account."' AND account_status = '0' AND mem_id = '".$coop_member_id."'");
		$this->db->limit(1);
		$row = $this->db->get()->row_array();

		$account_id = @$row['account_id'];
		return $account_id;
	}	
	
	//check coop_loan_atm_transaction
	public function check_atm_transaction($get_data){
		$data = false;
		if(@$get_data['loan_atm_id'] != ''){
			$row_transaction = $this->atm_transaction_by_id($get_data['loan_atm_id']);
			if(@$get_data['total_amount'] >= @$row_transaction['loan_amount_balance']){
				$data = true;
			}else{
				$data = false;
			}
		}
		//echo 'data='.$data.'<br>';
		return $data;
	}

	private function atm_transaction_by_id($loan_atm_id){
		$row = $this->db->select('loan_amount_balance,transaction_datetime')->from('coop_loan_atm_transaction')
										->where("loan_atm_id = '".$loan_atm_id."'")
										->order_by("transaction_datetime DESC ,loan_atm_transaction_id DESC")->limit(1)->get()->row_array();
		return $row;
    }	
	
	//check deposit setting
	public function check_deposit_balance_min($request_amount,$avaliable_balance,$from_acct_type,$transaction_code,$list_id){
		$data = false;

		$date_interest = date('Y-m-d');
		$row = $this->db->select(array('balance_min'))->from('coop_deposit_type_setting_detail')
											->where("type_id = '2' AND start_date <= '".$date_interest."'")
											->order_by("start_date DESC")->limit(1)->get()->row_array();
		$balance_min = $row['balance_min'];									
		$last_balance = ($avaliable_balance - $request_amount);
		
		if($from_acct_type == '02' && $transaction_code != '0100' && $list_id != '001' && $list_id != '003'){
			if($last_balance >= $balance_min){
				$data = true;
			}else{
				$data = false;
			}	
		}else{
			$data = true;
		}
		//echo 'data='.$data.'<br>';
		return $data;
	}
	
	public function gen_action_log_atm($arr_data,$action='1'){
		$detail = '';
		if(is_array($arr_data)){
			foreach($arr_data AS $key=>$val){
				$detail .='|'.$key.'='.$val;
			}
		}else{
			$detail .= $arr_data;
		}
		
		$tab = '     ';
		$data = '';		
		$data .= 'action='.$action.$tab;
		$data .= $detail;
	
		$gen_date_time = date('Ymdhis');
		$name_file = $gen_date_time.$action.$member_id.'.log';
		$path_file = 'check_log_atm_ktb/'.$name_file;
		$path_file_full = FCPATH.$path_file;
		if(write_file($path_file_full, $data) == FALSE){
			
		}else{
			//echo '<br>';
			//echo $path_file;  
		}
	}

	public function get_bank_reference_number($arr_data){				
		$coop_member_id = @$arr_data['coop_member_id'];
		$transaction_date = @$arr_data['transaction_date'];
		$bank_reference_number = @$arr_data['bank_reference_number'];
		$row = $this->db->select(array('transaction_amount','response_code','transaction_code','transaction_date'))
				->from('message_response_atm_ktb')	
				->where("coop_member_id = '".$coop_member_id."' AND transaction_date = '".$transaction_date."' AND bank_reference_number = '".$bank_reference_number."' AND response_code = '0000'")		
				->get()->result_array();
		if(!empty($row)){
			$result = true;
		}else{
			$result = false;
		}
		return $result;
		
	}	
	
	public function get_setting_atm_online(){				
		$row = $this->db->select(array('company_account','encrypt_key'))
				->from('coop_setting_atm_online')->limit(1)	
				->get()->row_array();
		if(!empty($row)){
			$result = $row;
		}else{
			$result = array();
		}
		return $result;
		
	}
}
