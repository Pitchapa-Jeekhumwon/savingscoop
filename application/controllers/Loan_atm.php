<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loan_atm extends CI_Controller {
	public $loan_atm_status = array('0'=>'รออนุมัติ', '1'=>'อนุมัติ', '2'=>'ขอยกเลิก', '3'=>'ยกเลิกสัญญา', '4'=>'ปิดสัญญา','5'=>'ไม่อนุมัติ');
	public $loan_atm_activate_status = array('0'=>'ปกติ', '1'=>'อายัดบัญชี');
	public $loan_detail_status = array('0'=>'ทำรายการแล้ว', '1'=>'จ่ายครบแล้ว');
	public $transaction_at = array('0'=>'ทำรายการที่สหกรณ์', '1'=>'ทำรายการทีตู้ ATM');
	public $pay_type = array('0'=>'เงินสด', '1'=>'โอนเงิน', '2'=>'ATM');
	
	function __construct()
	{
		parent::__construct();
		$this->load->model("Finance_libraries", "Finance_libraries");
        $this->load->model('Atm_calculator_model', 'ATMCalc');
        $this->load->model("Cashier_loan_model", "cashier_loan");
	}
	public function index()
	{
		$arr_data = array();
		
		if($this->input->get('member_id')!=''){
			$member_id = $this->input->get('member_id');
		}else{
			$member_id = '';
		}
		$arr_data['member_id'] = $member_id;

		$this->db->select('*');
		$this->db->from('coop_share_setting');
		$this->db->order_by('setting_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['share_value'] = $row[0]['setting_value'];
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm_setting');
		$row = $this->db->get()->result_array();
		$arr_data['loan_atm_setting'] = @$row[0];

		if($member_id != '') {
			$this->db->select(array('t1.*',
							't2.mem_group_name AS department_name',
							't3.mem_group_name AS faction_name',
							't4.mem_group_name AS level_name'));
			$this->db->from('coop_mem_apply as t1');			
			$this->db->join("coop_mem_group AS t2","t1.department = t2.id","left");
			$this->db->join("coop_mem_group AS t3","t1.faction = t3.id","left");
			$this->db->join("coop_mem_group AS t4","t1.level = t4.id","left");
			$this->db->where("t1.member_id = '".$member_id."'");
			$rs = $this->db->get()->result_array();
			$row = @$rs[0];
			
			$department = "";
			$department .= @$row['department_name'];
			$department .= (@$row["faction_name"]== 'ไม่ระบุ')?"":"  ".@$row["faction_name"];
			$department .= "  ".@$row["level_name"];
			$row['mem_group_name'] = $department;
			$arr_data['row_member'] = $row;	
			
			//อายุเกษียณ
			$this->db->select(array('retire_age'));
			$this->db->from('coop_profile');
			$rs_retired = $this->db->get()->result_array();
			$arr_data['retire_age'] = $rs_retired[0]['retire_age'];		
			
			//ประเภทสมาชิก
			$this->db->select('mem_type_id, mem_type_name');
			$this->db->from('coop_mem_type');
			$rs_mem_type = $this->db->get()->result_array();
			$mem_type_list = array();
			foreach($rs_mem_type AS $key=>$row_mem_type){
				$mem_type_list[$row_mem_type['mem_type_id']] = $row_mem_type['mem_type_name'];
			}
			
			$arr_data['mem_type_list'] = $mem_type_list;
			
			$this->db->select('*');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2')");
			$this->db->order_by('share_date DESC');
			$this->db->limit(1);
			$row_prev_share = $this->db->get()->result_array();
			$row_prev_share = @$row_prev_share[0];
			
			$arr_data['count_share'] = $row_prev_share['share_collect'];
			$arr_data['cal_share'] = $row_prev_share['share_collect_value'];
			
			$this->db->select(array('*'));
			$this->db->from('coop_loan_atm');
			$this->db->where("member_id = '".$member_id."' AND loan_atm_status = '1'");
			$row = $this->db->get()->result_array();
			if(empty($row)){
				$this->db->select(array('*'));
				$this->db->from('coop_loan_atm');
				$this->db->where("member_id = '".$member_id."' AND loan_atm_status = '0'");
				$row = $this->db->get()->result_array();
				$prev_loan_deduct = $this->db->get_where("coop_loan_atm_prev_deduct", array("loan_atm_id" => $row[0]['loan_atm_id']))->result_array();
				if($prev_loan_deduct)
					$row[0]['prev_loan_deduct'] = $prev_loan_deduct;
				$arr_data['row_loan_atm'] = @$row[0];	
				//หักกลบ ฉฉ
				$arr_data['deduct_amount'] = 0;
				$arr_data['principal_amount'] = 0;
				$this->db->select(array(
					't1.id',
					't1.contract_number',
					't1.loan_amount_balance',
					't1.loan_type'
				));
				$this->db->from("coop_loan t1");
				$this->db->join("coop_loan_name t2",'t1.loan_type = t2.loan_name_id','inner');
				$this->db->join("coop_loan_type t3",'t2.loan_type_id = t3.id','inner');
				$this->db->where("t1.loan_status = '1' AND member_id = '".@$member_id."' AND t3.loan_type_code = 'emergent'");
				$row = $this->db->get()->result_array();
				foreach($row as $key => $value){
					$cal_loan_interest = array();
					$cal_loan_interest['loan_id'] = $value['id'];
					$cal_loan_interest['date_interesting'] = date('Y-m-d');
					// $interest_amount = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
					$loan_amount = $value['loan_amount_balance'];//เงินกู้
					$loan_type = $value['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย
					$loan_id = $value['id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562
					$this->db->select("date(transaction_datetime) as transaction_datetime");
					$this->db->order_by("transaction_datetime desc");
					$this->db->limit(1);
					$date1 = $this->db->get_where("coop_loan_transaction", array(
						"loan_id" => $value['id']
					))->result_array()[0]['transaction_datetime'];//วันคิดดอกเบี้ยล่าสุด
					$tmp_date1 = date("Y-m", strtotime($date1) );
					if($tmp_date1 != date("Y-m"))//ใช้ออกเรียกเก็บ
						$date1 = date("Y-m-t", strtotime("last month"));

					$date_fixed = $this->input->get('date_fixed');
					if(isset($date_fixed) && !empty($date_fixed)){
					    $exp_arr = explode("/", $date_fixed);
					    $exp_arr[0] -= $exp_arr[0]-2 >= (date('Y')+543)-1 ? 543 : 0 ;
                        $date2 = date('Y-m-d', strtotime(join('-', $exp_arr)));
                    }else {
                        $date2 = date("Y-m-d");//วันที่คิดดอกเบี้ย now
                    }

					//$interest_amount = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_id, $date1, $date2);
                    $calc_interest = array();
                    $calc_interest['loan_id'] = $value['id'];
                    $calc_interest['entry_date'] = $date2;
                    $calc_interest['loan_type'] = $value['loan_type'];
                    $interest_arrears = $this->LoanCalc->calc('PL', $calc_interest);
					
					$arr_data['loan_id'] 				= $value['id'];
					$arr_data['principal_amount'] 		= @$value['loan_amount_balance'];
					$arr_data['interest_amount']	 	= @$interest_arrears['interest_arrear_bal'];
					$arr_data['deduct_amount'] 			= @$value['loan_amount_balance']+@$interest_arrears['interest_arrear_bal'];

				}
				//หักกลบ ฉฉ
			
			}else{
				//เช็คยอดหักกลบ
				$arr_data['row_loan_atm'] = @$row[0];
				$arr_data['deduct_amount'] = 0;
				$arr_data['principal_amount'] = 0;

				$this->db->select(array('*'));
				$this->db->from('coop_loan_atm');
				$this->db->where("member_id = '".$member_id."' AND loan_atm_status = '1'");
				$row = $this->db->get()->result_array();
				$principal_amount = 0;
				$interest_amount = 0;
				$total_amount_balance = 0;
				foreach($row as $key => $value){

					$arr_data['principal_amount'] += @$value['total_amount_approve'] - $value['total_amount_balance']; //ต้นคงเหลือ
					$total_amount_balance = @$value['total_amount_approve'] - @$value['total_amount_balance'];//ยอดหนี้คงเหลือ
					
					$cal_loan_interest = array();
					$cal_loan_interest['loan_atm_id'] = $arr_data['row_loan_atm']['loan_atm_id'];
					$cal_loan_interest['entry_date'] = date('Y-m-d');
					// $interest_amount = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
					if($total_amount_balance == 0){
						$interest_amount = 0;
					}else{
						// $interest_amount = $this->loan_libraries->cal_atm_interest_report_test($cal_loan_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false , true)['interest_month'];
						// if($interest_amount<=0){
						// 	$interest_amount = $this->loan_libraries->cal_atm_interest_report_test($cal_loan_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false , true)['interest_month'];
						// }
                        $interest_amount = $this->ATMCalc->calc('PL', $cal_loan_interest)['interest_arrear_bal'];

					}
					
					$arr_data['interest_amount'] = @$interest_amount; //ดอกเบี้ย
					// echo "<hr>";
					// var_dump($interest_amount);
					// echo @$interest_amount;
					// exit;
					$arr_data['deduct_amount'] += ((@$value['total_amount_approve'] - $value['total_amount_balance']) + @$interest_amount); //ยอดรวม
				}

				
				$arr_data['row_loan_atm_detail'] = @$row;
				
				$this->db->select(array('*'));
				$this->db->from('coop_loan_atm_file_attach');
				$this->db->where("loan_atm_id = '".$arr_data['row_loan_atm']['loan_atm_id']."'");
				$row = $this->db->get()->result_array();
				$arr_data['row_loan_atm_file'] = @$row;
				
				$this->db->select(array('loan_id'));
				$this->db->from('coop_loan_atm_detail');
				$this->db->where("
					member_id = '".$member_id."' 
					AND loan_atm_id = '".$arr_data['row_loan_atm']['loan_atm_id']."'
					AND transfer_status = '0'
				");
				$this->db->order_by('loan_id ASC');
				$row = $this->db->get()->result_array();
				if(!empty($row)){
					$arr_data['loan_atm_detail_id_no_transfer'] = $row[0]['loan_id'];
				}
			}
			$this->db->select(array('*'));
			$this->db->from('coop_loan_atm');
			$this->db->where("member_id = '".$member_id."'");
			$this->db->order_by("loan_atm_id DESC");
			$row = $this->db->get()->result_array();
			foreach($row as $key => $value){
				$this->db->select(array('loan_id'));
				$this->db->from('coop_loan_atm_detail');
				$this->db->where("loan_atm_id = '".$value['loan_atm_id']."' AND transfer_status = '0'");
				$this->db->order_by("loan_id ASC");
				$row_detail_no_transfer = $this->db->get()->result_array();
				if(!empty($row_detail_no_transfer)){
					$row[$key]['loan_atm_detail_id_no_transfer'] = $row_detail_no_transfer[0]['loan_id'];
				}
			}
			$arr_data['row_loan_atm_all'] = $row;
			


			$this->db->select(array(
				'account_id','account_name'
			));
			$this->db->from('coop_maco_account');
			$this->db->where("mem_id = '".$member_id."'");
			$row_account = $this->db->get()->result_array();
			$arr_data['account_id'] = $row_account;	

			///////////////////////////////รายการสัญญาเดิมที่หักลบ////////////////////////////////
			$prev_loan_active_arr = array();
			$i=0;
			$this->db->select(array(
				'*'
			));
			$this->db->from('coop_loan as t1');
			$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_status = '1' AND t1.loan_amount_balance <> 0");
			$prev_loan_active = $this->db->get()->result_array();
			
			foreach($prev_loan_active as $key => $value){
				$prev_loan_active_arr[$i]['id'] = $value['id'];
				$prev_loan_active_arr[$i]['contract_number'] = $value['contract_number'];
				$prev_loan_active_arr[$i]['loan_amount_balance'] = $value['loan_amount_balance'];
				
				$rs_type_prev_loan_atm = $this->db->select(array('pay_type'))
				->from('coop_loan_atm_prev_deduct')
				->where("ref_id = '".$value['id']."' AND loan_atm_id = '".$arr_data['row_loan_atm']['loan_atm_id']."'")
				->get()->result_array();			
				$check_type_prev_loan_atm = $rs_type_prev_loan_atm[0]['pay_type'];			

			
				$prev_loan_active_arr[$i]['checked'] = (@$check_type_prev_loan_atm != '')?$check_type_prev_loan_atm:"principal";
				$cal_loan_interest = array();
				$cal_loan_interest['loan_id'] = $value['id'];
				$cal_loan_interest['date_interesting'] = date('Y-m-d');

				$loan_amount = $value['loan_amount_balance'];//เงินกู้
				$loan_type = $value['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย
				$loan_id = $value['id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562
				$this->db->select("date(transaction_datetime) as transaction_datetime");
				$this->db->order_by("transaction_datetime desc");
				$this->db->limit(1);
				$date1 = $this->db->get_where("coop_loan_transaction", array(
					"loan_id" => $value['id']
				))->result_array()[0]['transaction_datetime'];//วันคิดดอกเบี้ยล่าสุด
				$tmp_date1 = date("Y-m", strtotime($date1) );
				if($tmp_date1 != date("Y-m"))//ใช้ออกเรียกเก็บ
					$date1 = date("Y-m-t", strtotime("last month"));
				
				$date2 = date("Y-m-d");//วันที่คิดดอกเบี้ย now

                $calc_atm_interest = array();
                $calc_atm_interest['loan_id'] = $value['id'];
                $calc_atm_interest['entry_date'] = date('Y-m-d');
                $calc_atm_interest['loan_type'] = $value['loan_type'];
                $interest_loan = $this->LoanCalc->calc('PL', $calc_atm_interest)['interest_arrear_bal'];

                $prev_loan_active_arr[$i]['principal'] = $loan_amount;
				$prev_loan_active_arr[$i]['interest'] = $interest_loan;
				$prev_loan_active_arr[$i]['type'] = 'loan';
				$prev_loan_active_arr[$i]['prev_loan_total'] = $value['loan_amount_balance'];
				
				$ref_loan_deduct = $this->db->get_where("coop_loan_atm_prev_deduct", array(
					"ref_id" => $value['id'],
					"loan_atm_id" => $arr_data['row_loan_atm']['loan_atm_id']
				))->result_array()[0];
				if($ref_loan_deduct){
					$prev_loan_active_arr[$i]['ref_loan_deduct'] = $ref_loan_deduct;
				}
				
				$this->db->select(array(
					'*'
				));
				$this->db->from('coop_finance_month_detail as t1');
				$this->db->where("
					t1.loan_id = '".$value['id']."' 
					AND t1.run_status = '0' 
					AND t1.pay_type = 'principal'
				");
				$row = $this->db->get()->result_array();
				$principal_month = 0;
				foreach($row as $key2 => $value2){
					$principal_month += $value2['pay_amount'];
				}
				$prev_loan_active_arr[$i]['principal_without_finance_month'] = $value['loan_amount_balance'] - $principal_month;
				$i++;
			}
			$this->db->select(array(
				'*'
			));
			$this->db->from('coop_loan_atm as t1');
			$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_atm_status = '1' AND (t1.total_amount_approve - t1.total_amount_balance) <> 0");
			$prev_loan_active = $this->db->get()->result_array();
            $create_temp_laon = date('Y-m-d');
			foreach($prev_loan_active as $key => $value){
				$prev_loan_active_arr[$i]['id'] = $value['loan_atm_id'];
				$prev_loan_active_arr[$i]['contract_number'] = $value['contract_number'];
				$prev_loan_active_arr[$i]['loan_amount_balance'] = $value['total_amount_approve'] - $value['total_amount_balance'];
				$prev_loan_active_arr[$i]['checked'] = "all";

				$cal_loan_interest = array();
				$cal_loan_interest['loan_atm_id'] = $arr_data['row_loan_atm']['loan_atm_id'];
				$cal_loan_interest['entry_date'] = date("Y-m-d");
                $interest_atm = $this->ATMCalc->calc('PL', $cal_loan_interest)['interest_arrear_bal'];

				$prev_loan_active_arr[$i]['principal'] = $prev_loan_active_arr[$i]['loan_amount_balance'];
				$prev_loan_active_arr[$i]['interest'] = $interest_atm;
				$prev_loan_active_arr[$i]['type'] = 'atm';
				$prev_loan_active_arr[$i]['prev_loan_total'] = $prev_loan_active_arr[$i]['loan_amount_balance']+$interest_atm;

				$ref_loan_deduct = $this->db->get_where("coop_loan_atm_prev_deduct", array(
					"ref_id" => $value['loan_atm_id']
				))->row_array();

				if($ref_loan_deduct){

                    $new_loan_atm = $this->db->get_where('coop_loan_atm', "loan_atm_id='{$ref_loan_deduct['loan_atm_id']}'")
                        ->row_array();

					$prev_loan_active_arr[$i]['ref_loan_deduct'] = $ref_loan_deduct;
                    $prev_loan_active_arr[$i]['principal'] = $ref_loan_deduct['principal_amount'];
                    $prev_loan_active_arr[$i]['interest'] = $ref_loan_deduct['interest_amount'];
                    $prev_loan_active_arr[$i]['type'] = $ref_loan_deduct['data_type'];
                    $prev_loan_active_arr[$i]['prev_loan_total'] = $ref_loan_deduct['pay_amount'];
                    $prev_loan_active_arr[$i]['createdatetime']  = $new_loan_atm['createdatetime'];
				}

				$this->db->select(array(
					'*'
				));
				$this->db->from('coop_finance_month_detail as t1');
				$this->db->where("
					t1.loan_atm_id = '".$value['loan_atm_id']."' 
					AND t1.run_status = '0' 
					AND t1.pay_type = 'principal'
				");
				$row = $this->db->get()->result_array();
				$principal_month = 0;
				foreach($row as $key2 => $value2){
					$principal_month += $value2['pay_amount'];
				}
				$prev_loan_active_arr[$i]['principal_without_finance_month'] = $prev_loan_active_arr[$i]['loan_amount_balance'] - $principal_month;
				$i++;
			}
			
			//exit;
			//echo '<pre>'; print_r($prev_loan_active_arr); echo '</pre>'; exit;
			$arr_data['prev_loan_active'] = $prev_loan_active_arr;
			////////////////////////////รายการสัญญาเดิมที่หักลบ///////////////////////////////////	
		}
		
		$this->db->select(array(
			'loan_reason_id','loan_reason'
		));
		$this->db->from('coop_loan_reason');
		$rs_loan_reason = $this->db->get()->result_array();
		$arr_data['rs_loan_reason'] = $rs_loan_reason;
			
		$arr_data['loan_atm_status'] = $this->loan_atm_status;
		$arr_data['loan_atm_activate_status'] = $this->loan_atm_activate_status;
		$arr_data['loan_detail_status'] = $this->loan_detail_status;
		
		$this->db->select('share_collect_value');
		$this->db->from('coop_mem_share');
		$this->db->where("member_id = '".$member_id."' AND share_status IN('1')");
		$this->db->order_by('share_date DESC');
		$this->db->limit(1);
		$row_prev_share = $this->db->get()->result_array();
		$arr_data['share_collect_value'] = @$row_prev_share[0]['share_collect_value'];
		
		$this->db->select(array(
			'bank_id','bank_name'
		));
		$this->db->from('coop_bank');
		$row = $this->db->get()->result_array();
		$arr_data['coop_bank'] = $row;
		$this->libraries->template('loan_atm/index',$arr_data);
	}


	function loan_atm_save(){
		//echo"<pre>";print_r($_POST);echo"</pre>";exit;

        if(!empty($_POST['loan_date'])) {
            $loan_date = $this->center_function->ConvertToSQLDate($_POST['loan_date'])." ".date('H:i:s');
        }else{
            $loan_date = date('Y-m-d H:i:s');
        }

		$this->db->select('*');
		$this->db->from("coop_loan_atm_setting");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$row_setting = @$row[0];
		
		$data_insert = array();
		$data_insert['loan_atm_id'] = $_POST['loan_atm_id'];
		$data_insert['member_id'] = $_POST['member_id'];
		$data_insert['loan_amount'] = str_replace(',','',$_POST['loan_amount']);
		$data_insert['loan_amount_balance'] = $data_insert['loan_amount'];
		$data_insert['loan_date'] = $loan_date;
		$data_insert['loan_status'] = '0';
		$data_insert['loan_description'] = 'ทำรายการกู้ATM';
		$data_insert['date_start_period'] = date('Y-m-t',strtotime('+1 month'));
		$data_insert['transaction_at'] = '0';
		$data_insert['transfer_status'] = '0';
		$data_insert['admin_id'] = $_SESSION['USER_ID'];

        $principal_per_month = ceil(($data_insert['loan_amount'] * ( ($row_setting['interest_rate']/100) / 12 ))/( 1-pow(1/(1+( ($row_setting['interest_rate']/100) /12)),$row_setting['max_period'])))*1/1;
		$data_insert['period_payment_amount'] = ceil($principal_per_month);
		//echo"<pre>";print_r($data_insert);exit;

        $data_insert['max_period'] = $row_setting['max_period'];
		$data_insert['petition_number'] = $this->loan_libraries->get_petition_atm_number();
		$data_insert['pay_type'] = $_POST['pay_type'];
		$data_insert['account_id'] = $_POST['account_id'];
		$data_insert['bank_id'] = $_POST['bank_id'];
		$data_insert['bank_account_id'] = $_POST['bank_account_id'];
		$this->db->insert('coop_loan_atm_detail',$data_insert);
		
		$this->db->select(array('total_amount_approve','total_amount_balance'));
		$this->db->from('coop_loan_atm');
		$this->db->where("loan_atm_id = '".$_POST['loan_atm_id']."'");
		$row_loan_atm = $this->db->get()->result_array();
		$total_amount_balance = $row_loan_atm[0]['total_amount_balance'] - str_replace(',','',$_POST['loan_amount']);
		
		$loan_amount_balance = $row_loan_atm[0]['total_amount_approve'] - $total_amount_balance;
		
		$data_insert = array();
        $principal_per_month = ceil(self::getATMPeriod($data_insert['loan_amount'])/100)*100;
        $data_insert['max_period'] = $row_setting['max_period'];
        $data_insert['period_payment_amount'] = $principal_per_month;
		$data_insert['total_amount_balance'] = $total_amount_balance;
		$this->db->where('loan_atm_id',$_POST['loan_atm_id']);
		$this->db->update('coop_loan_atm',$data_insert);
		
		$atm_transaction = array();
		$atm_transaction['loan_atm_id'] = $_POST['loan_atm_id'];
		$atm_transaction['loan_amount_balance'] = $loan_amount_balance;
		$atm_transaction['transaction_datetime'] = $loan_date;
        $this->loan_libraries->atm_transaction_arrears('AT', $atm_transaction);
		
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm?member_id='.@$_POST['member_id'])."' </script>";
		exit;
	}
	
	function loan_contract_save(){

        $this->db->select('*');
        $this->db->from("coop_loan_atm_setting");
        $this->db->limit(1);
        $row_setting = $this->db->get()->row_array();
		
		$data_insert = array();
		$data_insert['member_id'] = $_POST['member_id'];
		$data_insert['total_amount'] = str_replace(',','',$_POST['total_amount']);
		$data_insert['total_amount_balance'] = $data_insert['total_amount'];
		if(!empty($_POST['createdatetime'])) {
            $data_insert['createdatetime'] = $this->center_function->ConvertToSQLDate($_POST['createdatetime'])." ".date('H:i:s');
        }else{
            $data_insert['createdatetime'] = date('Y-m-d H:i:s');
        }
		$data_insert['loan_atm_status'] = '0';
		$data_insert['loan_reason'] = $_POST['loan_reason'];
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['activate_status'] = '0';
		$data_insert['period_payment_amount'] = $_POST['period_payment_amount'];
        $data_insert['max_period'] = $_POST['max_period'];
        $data_insert['pay_type'] = $_POST['pay_type'];
		if(@$_POST['loan_atm_id'] != ''){
			$this->db->where('loan_atm_id',$_POST['loan_atm_id']);
			$this->db->update('coop_loan_atm',$data_insert);
			$loan_atm_id = $_POST['loan_atm_id'];

		}else{
			$data_insert['petition_number'] = $this->loan_libraries->get_petition_atm_number();
			$this->db->insert('coop_loan_atm',$data_insert);
			$loan_atm_id = $this->db->insert_id();

		}


		if(isset($_POST['prev_loan']) && sizeof($_POST['prev_loan'])) {
            foreach ($_POST['prev_loan'] as $key => $value) {

                $this->db->where('ref_id', $value['id']);
                $this->db->delete('coop_loan_atm_prev_deduct');

                if (@$value['id'] != '') {
                    //$pay_type = isset($_POST['interest_amount']) && $_POST['interest_amount'] < 0 ? 'principal' : 'all';
                    $pay_type = $value['pay_type'];
                    if ($pay_type == 'all') {
                        $principal = $value['principal'];
                        $interest = $value['interest'];
                    } else {
                        $principal = str_replace(',', '', $value['amount']);
                        $interest = 0;
                    }

                    $data_insert = array();
                    $data_insert['loan_atm_id'] = $loan_atm_id;
                    $data_insert['ref_id'] = $value['id'];
                    $data_insert['data_type'] = @$value['type'];
                    $data_insert['pay_type'] = $pay_type;
                    $data_insert['pay_amount'] = str_replace(',', '', $value['principal']);
                    $data_insert['principal_amount'] = @$principal;
                    $data_insert['interest_amount'] = @$interest;
                    $this->db->insert('coop_loan_atm_prev_deduct', $data_insert);
                }
            }
        }
		
		if($loan_atm_id!=''){
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/loan_atm_attach/";
			if($_FILES['file_attach']['name'][0]!=''){
				foreach($_FILES['file_attach']['name'] as $key => $value){
					$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES['file_attach']['name'][$key]);
					@copy($_FILES["file_attach"]["tmp_name"][$key],$output_dir.$new_file_name);
					
					$data_insert = array();
					$data_insert['loan_atm_id'] = $loan_atm_id;
					$data_insert['file_old_name'] = $_FILES['file_attach']['name'][$key];
					$data_insert['file_name'] = $new_file_name;
					$data_insert['file_path'] = $output_dir.$new_file_name;
					$data_insert['file_type'] = $_FILES['file_attach']['type'][$key];
					$this->db->insert('coop_loan_atm_file_attach', $data_insert);
				}
			}
		}

		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm?member_id='.@$_POST['member_id'])."' </script>";
	}
	
	function ajax_delete_loan_file_attach(){
		$this->db->select(array('*'));
		$this->db->from("coop_loan_atm_file_attach");
		$this->db->where("id = '".@$_POST['id']."'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];

		//$attach_path = "../uploads/loan_attach/";
		$attach_path = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/loan_atm_attach/";
		$file = @$attach_path.@$row['file_name'];
		unlink($file);

		$this->db->where("id", @$_POST['id'] );
		$this->db->delete("coop_loan_atm_file_attach");	
		if(@$rs){
			echo "success";
		}else{
			echo "error";
		}
		exit;
	}	
	
	public function loan_atm_approve()
	{
		$arr_data = array();
		
		$where = '1=1';
		if(@$_GET['loan_status']!=''){
			if(@$_GET['loan_status'] == '1'){
				$where .= " AND coop_loan_atm.loan_atm_status IN('1','4') ";
			}else{
				$where .= " AND coop_loan_atm.loan_atm_status = '".$_GET['loan_status']." '";
			}
		}else{
			$where .= " AND coop_loan_atm.loan_atm_status IN('0','1','4','5') ";
		}
		
		$order_by = 'createdatetime DESC ,loan_atm_id DESC';
		if($_GET['approve_date']!=''){
			$approve_date_arr = explode('/',$_GET['approve_date']);
			$approve_day = stripslashes($approve_date_arr[0]);
			$approve_month = stripslashes($approve_date_arr[1]);
			$approve_year = stripslashes($approve_date_arr[2]);
			$_GET['approve_date'] = $approve_day."/".$approve_month."/".$approve_year;
			$approve_year -= 543;
			$approve_date = $approve_year.'-'.$approve_month.'-'.$approve_day;
			$where .= " AND coop_loan_atm.approve_date >= '".$approve_date." 00:00:00.000'";
			$order_by = 'coop_loan_atm.approve_date ASC ,loan_atm_id DESC';
		}
		if($_GET['thru_date']!=''){
			$thru_date_arr = explode('/',$_GET['thru_date']);
			$thru_day = stripslashes($thru_date_arr[0]);
			$thru_month = stripslashes($thru_date_arr[1]);
			$thru_year = stripslashes($thru_date_arr[2]);
			$_GET['thru_date'] = $thru_day."/".$thru_month."/".$thru_year;
			$thru_year -= 543;
			$thru_date = $thru_year.'-'.$thru_month.'-'.$thru_day;
			$where .= " AND coop_loan_atm.approve_date <= '".$thru_date." 23:59:59.000'";
			$order_by = 'coop_loan_atm.approve_date ASC ,loan_atm_id DESC';
		}

		$x=0;
		$join_arr = array();
		/*$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_mem_apply.member_id = coop_loan_atm.member_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		*/
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_loan_atm.admin_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		//$this->paginater_all->select('coop_loan_atm.*, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_user.user_name');
		$this->paginater_all->select('
										coop_loan_atm.createdatetime,
										coop_loan_atm.loan_atm_id,
										coop_loan_atm.loan_atm_status,
										coop_loan_atm.total_amount,
										coop_loan_atm.deduct_receipt_id,
										coop_loan_atm.member_id,
										coop_loan_atm.petition_number,
										coop_loan_atm.approve_date,
										coop_user.user_name
									');
		$this->paginater_all->main_table('coop_loan_atm');
		$this->paginater_all->where("{$where}");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(100);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by($order_by);
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];
		if(@$_GET['dev'] == 'dev'){
			echo $this->db->last_query();exit;
		}
		foreach($row['data'] AS $key=>$value){
			$this->db->select('coop_mem_apply.firstname_th, coop_mem_apply.lastname_th');
			$this->db->from("coop_mem_apply");
			$this->db->where("member_id = '".@$value['member_id']."'");
			$rs_member = $this->db->get()->result_array();
			$row_member = @$rs_member[0];
			$row['data'][$key]['firstname_th'] = $row_member['firstname_th'];
			$row['data'][$key]['lastname_th'] = $row_member['lastname_th'];
		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		$loan_type = array();
		$this->db->select('*');
		$this->db->from("coop_loan_type");
		$rs_type = $this->db->get()->result_array();
		foreach($rs_type as $key => $row_type){
			$loan_type[$row_type['id']] = $row_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type; 
		$arr_data['loan_atm_status'] = $arr_data['loan_atm_status'] = $this->loan_atm_status;
		$this->libraries->template('loan_atm/loan_atm_approve',$arr_data);
	}
	
	function loan_atm_not_approve(){
			$this->db->select('*');
			$this->db->from("coop_loan_atm");
			$this->db->where("loan_atm_id = '".@$_GET['loan_atm_id']."'");
			$rs_loan = $this->db->get()->result_array();
			$rs_loan = @$rs_loan[0];
			
			$data_insert = array();
			
			$data_insert['loan_atm_status'] = @$_GET['status_to'];
			$this->db->where('loan_atm_id', @$_GET['loan_atm_id']);
			$this->db->update('coop_loan_atm', $data_insert);
			
			if($rs_loan['change_from']!=''){
				$data_insert = array();
				$data_insert['change_amount_status'] = '';
				$this->db->where('loan_atm_id', @$rs_loan['change_from']);
				$this->db->update('coop_loan_atm', $data_insert);
			}
			
			$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
			echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm/loan_atm_approve')."' </script>";
			exit;
	}
	
	function get_loan_atm_data(){
		$this->db->select(array(
			't1.loan_atm_id',
			't1.petition_number',
			't1.member_id',
			't1.total_amount',
			't2.firstname_th',
			't2.lastname_th',
			't3.prename_short',
			't1.change_from'
		));
		$this->db->from("coop_loan_atm t1");
		$this->db->join("coop_mem_apply t2",'t1.member_id = t2.member_id','inner');
		$this->db->join("coop_prename t3",'t2.prename_id = t3.prename_id','left');
		$this->db->where("loan_atm_id = '".$_POST['loan_atm_id']."'");
		$row = $this->db->get()->result_array();
		$coop_loan_atm = $row[0];
		$data = array();
		foreach($row[0] as $key => $value){
			if($key == 'total_amount'){
				$data['loan_atm_data'][$key] = number_format($value);
			}else{
				$data['loan_atm_data'][$key] = $value;
			}
		}
		
		//หักกลบแบบเก่า
		$this->db->select(array(
			't1.atm_number'
		));
		$this->db->from("coop_atm_card t1");
		$this->db->where("member_id = '".$coop_loan_atm['member_id']."' AND atm_card_status = '0'");
		$row = $this->db->get()->result_array();
		$data['loan_atm_data']['atm_number'] = @$row[0]['atm_number'];
		
		$this->db->select(array(
			't1.id',
			't1.contract_number',
			't1.loan_amount_balance',
		));
		$this->db->from("coop_loan t1");
		$this->db->join("coop_loan_name t2",'t1.loan_type = t2.loan_name_id','inner');
		$this->db->join("coop_loan_type t3",'t2.loan_type_id = t3.id','inner');
		$this->db->where("t1.loan_status = '1' AND member_id = '".$data['loan_atm_data']['member_id']."' AND t3.loan_type_code = 'emergent'");
		$row = $this->db->get()->result_array();
		$loan_amount_balance = 0;
		$prev_loan_number = '';
		$process_balance = 0;
		@$data['loan_atm_data']['sql'] = $this->db->last_query();
		foreach($row as $key => $value){

			$cal_loan_interest = array();
			$cal_loan_interest['loan_id'] = @$value['id'];
			$cal_loan_interest['entry_date'] = date('Y-m-d');
            $interest_amount = $this->ATMCalc->calc('PL', $cal_loan_interest)['interest_arrear_bal'];

			$loan_amount_balance += @$value['loan_amount_balance'];
			$prev_loan_number .= $value['contract_number'].",";

		}
		
		$prev_loan_number = substr($prev_loan_number,0,-1);

		$data['loan_atm_data']['prev_loan'] = @$loan_amount_balance;

		//หักกลบแบบใหม่
		$this->db->select(array('*'));
		$this->db->from("coop_loan_atm_prev_deduct");
		$this->db->where("loan_atm_id = '".@$_POST['loan_atm_id']."'");
		$rs_prev_deduct = $this->db->get()->result_array();		
		$loan_atm_prev_pay_amount = 0;
		$loan_atm_prev_pay_interest_amount = 0;
		foreach($rs_prev_deduct as $key => $value){
			$loan_atm_prev_pay_amount += @$value['principal_amount'];
			$loan_atm_prev_pay_interest_amount += @$value['interest_amount'];			
		}
		$loan_amount_balance_atm = @$loan_atm_prev_pay_amount+@$loan_atm_prev_pay_interest_amount;
		@$data['loan_atm_data']['prev_loan'] = $loan_amount_balance_atm;
		
		@$data['loan_atm_data']['t1'] = number_format(@$loan_atm_prev_pay_amount,2);
		@$data['loan_atm_data']['t2'] = number_format(@$loan_atm_prev_pay_interest_amount,2);
		@$data['loan_atm_data']['t3'] = number_format(@$loan_amount_balance_atm,2);

		$data['loan_atm_data']['total_amount_balance'] = number_format($coop_loan_atm['total_amount'] - @$loan_amount_balance_atm,2);
		echo json_encode($data);
		exit;
	}
	
	function loan_approve_save(){

        if(isset($_POST['approve_date']) && !empty($_POST['approve_date'])){
            $approve_date = $this->center_function->ConvertToSQLDate($_POST['approve_date'])." ".date('H:i:s');
        }else{
            $approve_date = date('Y-m-d H:i:s');
        }
		$rs_month_account = $this->db->select('accm_month_ini')
		->from("coop_account_period_setting")
		->limit(1)
		->get()->result_array();
		$month_account = $rs_month_account[0]['accm_month_ini'];
		$month_now = date('m');
		if((int)$month_now >= (int)$month_account){
			$year = (date('Y')+543)+1;
		}else{
			$year = (date('Y')+543);		
		}
		$year_short = substr($year,2,2);
		$new_contact_number = '';
		$this->db->select('*');
		$this->db->from("coop_loan_atm_setting");
		$this->db->limit(1);
		$rs_term_of_loan = $this->db->get()->result_array();
		$row_term_of_loan = @$rs_term_of_loan[0];
		$new_contact_number = $row_term_of_loan['prefix_code'].$year_short;
		
		$contact_number_now = $this->loan_libraries->get_contract_number($year, $approve_date);
		
		$new_contact_number .= sprintf("% 05d",$contact_number_now);
		//$new_contact_number = $new_contact_number."/".(date('Y')+543);
		
		$this->db->select('loan_atm_account_id');
		$this->db->from("coop_mem_apply");
		$this->db->where("member_id = '".$_POST['member_id']."'");
		$row_loan_atm_account_id = $this->db->get()->result_array();
		$account_id = @$row_loan_atm_account_id[0]['loan_atm_account_id'];
		
		if(@$account_id==''){
			$account_id = '002'.sprintf('%08d',$_POST['member_id']);
			$data_insert = array();
			$data_insert['loan_atm_account_id'] = $account_id;
			$this->db->where('member_id',$_POST['member_id']);
			$this->db->update('coop_mem_apply',$data_insert);
		}
		
		$data_insert = array();
		$data_insert['contract_number'] = @$new_contact_number;
		$data_insert['loan_atm_status'] = '1';
		$data_insert['total_amount_approve'] = str_replace(',','',$_POST['total_amount_approve']);
		$data_insert['total_amount_balance'] = str_replace(',','',$_POST['total_amount_approve']);
		$data_insert['prev_loan'] = str_replace(',','',$_POST['prev_loan']);
		$data_insert['approve_date'] = $approve_date;
		$data_insert['atm_card_number'] = @$_POST['atm_card_number'];
		$data_insert['account_id'] = @$account_id;
		$data_insert['activate_status'] = '0';
		$this->db->where('loan_atm_id', @$_POST['loan_atm_id']);
		$this->db->update('coop_loan_atm', $data_insert);
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm');
		$this->db->where("loan_atm_id = '".@$_POST['loan_atm_id']."'");
		$row = $this->db->get()->result_array();
		$row_atm = @$row[0];
		
/////////////////////////////////////////////////////////// หักกลบ ////////////////////////////////////////////////////////////////
        //get receipt setting data
        $receipt_format = 1;
        $receipt_finance_setting = $this->db->select("*")->from("coop_setting_finance")->where("name = 'receipt_cashier_format' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        if(!empty($receipt_finance_setting)) {
            $receipt_format = $receipt_finance_setting['value'];
        }
        
        if($receipt_format == 1) {
			$yymm = (date("Y")+543).date("m");

			$this->db->select(array('*'));
			$this->db->from('coop_receipt');
			$this->db->where("receipt_id LIKE '".$yymm."%'");
			$this->db->order_by("receipt_id DESC");
			$this->db->limit(1);
			$row_receipt = $this->db->get()->result_array();
			$row_receipt = @$row_receipt[0];

			if(@$row_receipt['receipt_id'] != '') {
				$id = (int) substr($row_receipt["receipt_id"], 6);
				$receipt_id = $yymm.sprintf("%06d", $id + 1);
			}else {
				$receipt_id = $yymm."000001";
			}
        } else {
            $receipt_id = $this->Finance_libraries->generate_cashier_receipt_id($receipt_format, $date);
		}

		$loan_amount_balance = 0;
		$loan_amount_balance_all = 0;
		$receipt_arr = array();
		$r=0;		
		$date_interesting = date('Y-m-d');		
		//////////////////	
		$this->db->select(array('t1.*',
								't2.contract_number',
								't2.loan_amount_balance',
								't2.id',
								't2.createdatetime',
								't2.member_id'
								));
		$this->db->from("coop_loan_atm_prev_deduct t1");
		$this->db->join("coop_loan t2",'t1.ref_id = t2.id','inner');
		$this->db->where("t1.loan_atm_id = '".@$_POST['loan_atm_id']."' AND t1.data_type = 'loan'");
		$row = $this->db->get()->result_array();
		$sum_count = 0;

		foreach($row as $key => $value){				
			//check หนี้ห้อย
			$extra_debt_amount	= 0;//หนี้ห้อย
			if(date("Y-m", strtotime($value['createdatetime']) ) != date("Y-m") ){
				$month = date("m");
				$year = (date("Y")+543);
				
				$this->db->select('profile_id');
				$this->db->from('coop_finance_month_profile');
				$this->db->where("profile_month = '".(int)$month."' AND profile_year = '".$year."' ");
				$profile_id = $this->db->get()->result_array()[0]['profile_id'];
				//echo $this->db->last_query(); echo '<hr>';
				$this->db->select("sum(pay_amount) as sum_of_pay_amount");
				$finance_month_detail = $this->db->get_where("coop_finance_month_detail", array(
					"profile_id" => $profile_id,
					"member_id" => $value['member_id'],
					"loan_id" => $value['ref_id'],
					"pay_type" => "principal",
					"run_status" => 0
				))->result_array()[0];
				//echo $this->db->last_query(); echo '<hr>';
				if($finance_month_detail){
					$extra_debt['total_princical'] += $finance_month_detail['sum_of_pay_amount'];
					$extra_debt_amount	= $finance_month_detail['sum_of_pay_amount'];
				}
					
			}
			//check หนี้ห้อย
			
			$contract_number = @$value['contract_number'];
			$pay_type = @$value['pay_type'];
			if($value['pay_type'] == 'principal'){					
				$loan_id = @$value['id'];
				$loan_amount_balance = @$value['principal_amount'];
				$interest_amount = 0;
			}else{
				$loan_id = @$value['id'];
				$loan_amount_balance = @$value['principal_amount'];
				$interest_amount = @$value['interest_amount'];
			}
			
			$data_insert = array();
			$data_insert['loan_atm_id'] = @$_POST['loan_atm_id'];
			$data_insert['member_id'] = @$_POST['member_id'];
			$data_insert['loan_amount'] = ($loan_amount_balance+$interest_amount);
			$data_insert['loan_amount_balance'] = ($loan_amount_balance+$interest_amount);
			$data_insert['loan_date'] = $approve_date;

			$data_insert['petition_number'] = $this->loan_libraries->get_petition_atm_number();
			$data_insert['loan_status'] = '0';
			$data_insert['loan_description'] = 'หักกลบสัญญาเก่า '.$contract_number;
			$data_insert['date_start_period'] = date('Y-m-t',strtotime($approve_date.' +1 month'));
			$data_insert['transaction_at'] = '0';
			$data_insert['transfer_status'] = '1';
			$this->db->insert('coop_loan_atm_detail', $data_insert);
			
			$loan_amount_balance_all += ($loan_amount_balance+$interest_amount);
			$atm_transaction = array();
			$atm_transaction['loan_atm_id'] = $_POST['loan_atm_id'];
			$atm_transaction['receipt_id'] = $receipt_id;
			$atm_transaction['loan_amount_balance'] = $loan_amount_balance_all;
			$atm_transaction['transaction_datetime'] = $approve_date;
			//$this->loan_libraries->atm_transaction($atm_transaction);
			$this->loan_libraries->atm_transaction_arrears('CL', $atm_transaction);

			$receipt_arr[$r]['receipt_id'] = $receipt_id;
			$receipt_arr[$r]['member_id'] = @$_POST['member_id'];
			$receipt_arr[$r]['loan_id'] = $loan_id;
			$receipt_arr[$r]['account_list_id'] = '15';
			$receipt_arr[$r]['principal_payment'] = $loan_amount_balance;
			$receipt_arr[$r]['interest'] = $interest_amount;
			$receipt_arr[$r]['total_amount'] = $loan_amount_balance+$interest_amount;
			$receipt_arr[$r]['payment_date'] = date('Y-m-d', strtotime($approve_date));
			$receipt_arr[$r]['createdatetime'] = $approve_date;
			$receipt_arr[$r]['loan_amount_balance'] = @$extra_debt_amount;
			$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$contract_number;
			$receipt_arr[$r]['deduct_type'] = @$pay_type;
			$r++;
			
			$data_insert = array();
			
			if(@$extra_debt_amount>=1){
				$data_insert['loan_status'] = '1';
				$data_insert['loan_amount_balance'] = $extra_debt_amount;//คงค้างหนี้ห้อยไว้ รอการผ่านรายการ
			}else{
				$data_insert['loan_status'] = '4';
				$data_insert['loan_amount_balance'] = '0';
			}
			$this->db->where('id', $loan_id);
			$this->db->update('coop_loan', $data_insert);
			
			$loan_transaction = array();
			$loan_transaction['loan_id'] = $loan_id;
			$loan_transaction['receipt_id'] = $receipt_id;
			if(@$extra_debt_amount>=1){
				$loan_transaction['loan_amount_balance'] = @$extra_debt_amount;
			}else{
				$loan_transaction['loan_amount_balance'] = '0';
			}				
			$loan_transaction['transaction_datetime'] = date('Y-m-d H:i:s', strtotime($approve_date));
			//$this->loan_libraries->loan_transaction($loan_transaction);
            $this->loan_libraries->loan_transaction_arrears('CL', $loan_transaction);
			
			$sum_count += @$loan_amount_balance+@$interest_amount;
		}
		//echo '<pre>'; print_r($receipt_arr); echo '</pre>';
		//exit;
		
		
			$this->db->select(array('t1.*',
									't2.contract_number',
									't2.total_amount_approve',
									't2.total_amount_balance',
									't2.loan_atm_id'
									));
			$this->db->from("coop_loan_atm_prev_deduct t1");
			$this->db->join("coop_loan_atm t2",'t1.ref_id = t2.loan_atm_id','inner');
			$this->db->where("t1.loan_atm_id = '".@$_POST['loan_atm_id']."' AND t1.data_type = 'atm'");
			$row = $this->db->get()->result_array();
			//echo '<pre>'; print_r($row); echo '</pre>';
			
			foreach($row as $key => $value){				
				$contract_number = @$value['contract_number'];
				//echo $contract_number.'<hr>';
				$total_amount_approve = @$value['total_amount_approve'];
				$pay_type = @$value['pay_type'];
				if($value['pay_type'] == 'principal'){					
					$loan_id = @$value['loan_atm_id'];
					$loan_amount_balance = @$value['principal_amount'];
					$interest_amount = 0;
				}else{
					$loan_id = @$value['loan_atm_id'];
					$loan_amount_balance = @$value['principal_amount'];
					$interest_amount = @$value['interest_amount'];
				}
				
				$data_insert = array();
				$data_insert['loan_atm_id'] = @$_POST['loan_atm_id'];
				$data_insert['member_id'] = @$_POST['member_id'];
				$data_insert['loan_amount'] = number_format($loan_amount_balance, 2, '.', '');
				$data_insert['loan_amount_balance'] = number_format($loan_amount_balance, 2, '.', '');
				$data_insert['loan_date'] = date('Y-m-d H:i:s', strtotime($approve_date));

				$data_insert['petition_number'] = $this->loan_libraries->get_petition_atm_number();
				$data_insert['loan_status'] = '0';
				$data_insert['loan_description'] = 'หักกลบสัญญาเก่า '.$contract_number;
				$data_insert['date_start_period'] = date('Y-m-t',strtotime( $approve_date.' +1 month'));
				$data_insert['transaction_at'] = '0';
				$data_insert['transfer_status'] = '1';
				$this->db->insert('coop_loan_atm_detail', $data_insert);
				
				$loan_amount_balance_all += ($loan_amount_balance);
                $interest += $interest_amount;
				$atm_transaction = array();
				$atm_transaction['loan_atm_id'] = $_POST['loan_atm_id'];
				$atm_transaction['loan_amount_balance'] = $loan_amount_balance_all;
				$atm_transaction['interest'] = $interest;
				$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s', strtotime($approve_date));
				//$this->loan_libraries->atm_transaction($atm_transaction);
				$this->loan_libraries->atm_transaction_arrears('RR', $atm_transaction);


				$receipt_arr[$r]['receipt_id'] = $receipt_id;
				$receipt_arr[$r]['member_id'] = @$_POST['member_id'];
				$receipt_arr[$r]['loan_atm_id'] = $loan_id;
				$receipt_arr[$r]['account_list_id'] = '31';
				$receipt_arr[$r]['principal_payment'] = $loan_amount_balance;
				$receipt_arr[$r]['interest'] = 0;
				$receipt_arr[$r]['total_amount'] = ($loan_amount_balance);
				$receipt_arr[$r]['payment_date'] = date('Y-m-d', strtotime($approve_date));
				$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s', strtotime($approve_date));
				$receipt_arr[$r]['loan_amount_balance'] = '0';
				$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$contract_number;
				$receipt_arr[$r]['deduct_type'] = $pay_type;
				$r++;
				
				$data_insert = array();
				$data_insert['total_amount_balance'] = $total_amount_approve;
				$data_insert['loan_atm_status'] = '4';
				$this->db->where('loan_atm_id', $loan_id);
				$this->db->update('coop_loan_atm', $data_insert);
					
				$data_insert = array();
				$data_insert['loan_amount_balance'] = '0';
				$data_insert['loan_status'] = '1';
				$this->db->where('loan_atm_id', $loan_id);
				$this->db->update('coop_loan_atm_detail', $data_insert);
				
				$atm_transaction = array();
				$atm_transaction['loan_atm_id'] = $loan_id;
				$atm_transaction['receipt_id'] = $receipt_id;
				$atm_transaction['loan_amount_balance'] = '0';
				$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s', strtotime($approve_date));
				//$this->loan_libraries->atm_transaction($atm_transaction);
				$this->loan_libraries->atm_transaction_arrears('OR', $atm_transaction);

				$sum_count += @$loan_amount_balance;
			}
			
			
			if($sum_count>0){
				$data_insert = array();
				$data_insert['receipt_id'] = $receipt_id;
				$data_insert['member_id'] = @$_POST['member_id'];
				$data_insert['admin_id'] = @$_SESSION['USER_ID'];
				$data_insert['sumcount'] = $sum_count;
				$data_insert['receipt_datetime'] = date('Y-m-d H:i:s', strtotime($approve_date));
				if($this->db->insert('coop_receipt', $data_insert)){
					foreach($receipt_arr as $key => $value){
						$data_insert = array();
						$data_insert['receipt_id'] = $value['receipt_id'];
						$data_insert['receipt_list'] = $value['account_list_id'];
						$data_insert['receipt_count'] = $value['total_amount'];
						$this->db->insert('coop_receipt_detail', $data_insert);
						
						//บันทึกการชำระเงิน
						$data_insert = array();
						$data_insert['receipt_id'] = $value['receipt_id'];
						$data_insert['member_id'] = @$value['member_id'];
						$data_insert['loan_id'] = @$value['loan_id'];
						$data_insert['loan_atm_id'] = @$value['loan_atm_id'];
						$data_insert['account_list_id'] = $value['account_list_id'];
						$data_insert['principal_payment'] = @$value['principal_payment'];
						$data_insert['interest'] = @$value['interest'];
						$data_insert['total_amount'] = @$value['total_amount'];
						$data_insert['payment_date'] = @$value['payment_date'];
						$data_insert['loan_amount_balance'] = @$value['loan_amount_balance'];
						$data_insert['createdatetime'] = @$value['createdatetime'];
						$data_insert['transaction_text'] = @$value['transaction_text'];
						$data_insert['deduct_type'] = @$value['deduct_type'];
						$this->db->insert('coop_finance_transaction', $data_insert);
						
						if(@$value['loan_id'] !='' && @$value['interest'] > 0){
							$data_insert = array();
							$data_insert['date_last_interest'] = date('Y-m-d H:i:s', strtotime($approve_date));
							$this->db->where('id',@$value['loan_id']);
							$this->db->update('coop_loan',$data_insert);
						}
						if(@$value['loan_atm_id'] !='' && @$value['interest'] > 0){
							$data_insert = array();
							$data_insert['date_last_interest'] = date('Y-m-d H:i:s', strtotime($approve_date));
							$this->db->where('loan_atm_id',@$value['loan_atm_id']);
							$this->db->update('coop_loan_atm',$data_insert);
						}
					}
					
					$data_insert = array();
					$data_insert['deduct_receipt_id'] = $receipt_id;
					$data_insert['total_amount_balance'] = $row_atm['total_amount_approve'] - $sum_count;
					$this->db->where('loan_atm_id',$_POST['loan_atm_id']);
					$this->db->update('coop_loan_atm',$data_insert);				
				}
			}

/////////////////////////////////////////////////////////// หักกลบ ////////////////////////////////////////////////////////////////
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm/loan_atm_approve')."' </script>";
		exit;
	}
	
	function loan_approve_save_new(){
		//echo"<pre>";print_r($_POST);exit;
		//ปีในการ gen เลขสัญญา		
		$rs_month_account = $this->db->select('accm_month_ini')
		>from("coop_account_period_setting")
		->limit(1)
		->get()->result_array();
		$month_account = $rs_month_account[0]['accm_month_ini'];
		$month_now = date('m');
		if((int)$month_now >= (int)$month_account){
			$year = (date('Y')+543)+1;
		}else{
			$year = (date('Y')+543);		
		}
		$year_short = substr($year,2,2);
		$new_contact_number = '';
		$this->db->select('*');
		$this->db->from("coop_loan_atm_setting");
		$this->db->limit(1);
		$rs_term_of_loan = $this->db->get()->result_array();
		$row_term_of_loan = @$rs_term_of_loan[0];
		$new_contact_number = $row_term_of_loan['prefix_code'].$year_short;
		
		$contact_number_now = $this->loan_libraries->get_contract_number($year);
		
		$new_contact_number .= sprintf("% 05d",$contact_number_now);
		//$new_contact_number = $new_contact_number."/".(date('Y')+543);
		
		$this->db->select('loan_atm_account_id');
		$this->db->from("coop_mem_apply");
		$this->db->where("member_id = '".$_POST['member_id']."'");
		$row_loan_atm_account_id = $this->db->get()->result_array();
		$account_id = @$row_loan_atm_account_id[0]['loan_atm_account_id'];
		
		if(@$account_id==''){
			$account_id = '002'.sprintf('%08d',$_POST['member_id']);
			$data_insert = array();
			$data_insert['loan_atm_account_id'] = $account_id;
			$this->db->where('member_id',$_POST['member_id']);
			$this->db->update('coop_mem_apply',$data_insert);
		}
		
		$data_insert = array();
		$data_insert['contract_number'] = @$new_contact_number;
		$data_insert['loan_atm_status'] = '1';
		$data_insert['total_amount_approve'] = str_replace(',','',$_POST['total_amount_approve']);
		$data_insert['total_amount_balance'] = str_replace(',','',$_POST['total_amount_approve']);
		$data_insert['prev_loan'] = str_replace(',','',$_POST['prev_loan']);
		$data_insert['approve_date'] = date('Y-m-d H:i:s');
		$data_insert['atm_card_number'] = @$_POST['atm_card_number'];
		$data_insert['account_id'] = @$account_id;
		$data_insert['activate_status'] = '0';
		$this->db->where('loan_atm_id', @$_POST['loan_atm_id']);
		$this->db->update('coop_loan_atm', $data_insert);
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm');
		$this->db->where("loan_atm_id = '".@$_POST['loan_atm_id']."'");
		$row = $this->db->get()->result_array();
		$row_atm = @$row[0];
		
/////////////////////////////////////////////////////////// หักกลบ ////////////////////////////////////////////////////////////////
        //get receipt setting data
        $receipt_format = 1;
        $receipt_finance_setting = $this->db->select("*")->from("coop_setting_finance")->where("name = 'receipt_cashier_format' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        if(!empty($receipt_finance_setting)) {
            $receipt_format = $receipt_finance_setting['value'];
		}

		if($receipt_format == 1) {
			$yymm = (date("Y")+543).date("m");

			$this->db->select(array('*'));
			$this->db->from('coop_receipt');
			$this->db->where("receipt_id LIKE '".$yymm."%'");
			$this->db->order_by("receipt_id DESC");
			$this->db->limit(1);
			$row_receipt = $this->db->get()->result_array();
			$row_receipt = @$row_receipt[0];

			if(@$row_receipt['receipt_id'] != '') {
				$id = (int) substr($row_receipt["receipt_id"], 6);
				$receipt_id = $yymm.sprintf("%06d", $id + 1);
			}else {
				$receipt_id = $yymm."000001";
			}
		} else {
			$receipt_id = $this->Finance_libraries->generate_cashier_receipt_id($receipt_format, $date);
		}
		
		$loan_amount_balance = 0;
		$loan_amount_balance_all = 0;
		$receipt_arr = array();
		$r=0;		
		$date_interesting = date('Y-m-d');		
		//////////////////	
		$this->db->select(array('t1.*',
								't2.contract_number',
								't2.loan_amount_balance',
								't2.id'
								));
		$this->db->from("coop_loan_atm_prev_deduct t1");
		$this->db->join("coop_loan t2",'t1.ref_id = t2.id','inner');
		$this->db->where("t1.loan_atm_id = '".@$_POST['loan_atm_id']."'");
		$row = $this->db->get()->result_array();

		//print_r($this->db->last_query());
		//exit;
		foreach($row as $key => $value){			
			$contract_number = @$value['contract_number'];
			$pay_type = @$value['pay_type'];
			if($value['pay_type'] == 'principal'){					
				$loan_id = @$value['id'];
				$loan_amount_balance = @$value['pay_amount'];
				$interest_amount = 0;
			}else{
				$loan_id = @$value['id'];
				$loan_amount_balance = @$value['loan_amount_balance'];
				
				$cal_loan_interest = array();
				$cal_loan_interest['loan_id'] = @$value['ref_id'];
				$cal_loan_interest['entry_date'] = $this->center_function->ConvertToSQLDate(@$date_interesting);
				//$interest_amount = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
                $interest_amount = $this->ATMCalc->calc('PL', $cal_loan_interest)['interest_arrear_bal'];
			}
			
			$data_insert = array();
			$data_insert['loan_atm_id'] = @$_POST['loan_atm_id'];
			$data_insert['member_id'] = @$_POST['member_id'];
			$data_insert['loan_amount'] = ($loan_amount_balance+$interest_amount);
			$data_insert['loan_amount_balance'] = ($loan_amount_balance+$interest_amount);
			$data_insert['loan_date'] = date('Y-m-d H:i:s');

			$data_insert['petition_number'] = $this->loan_libraries->get_petition_atm_number();
			$data_insert['loan_status'] = '0';
			$data_insert['loan_description'] = 'หักกลบสัญญาเก่า '.$contract_number;
			$data_insert['date_start_period'] = date('Y-m-t',strtotime('+1 month'));
			$data_insert['transaction_at'] = '0';
			$data_insert['transfer_status'] = '1';
			$this->db->insert('coop_loan_atm_detail', $data_insert);
			
			$loan_amount_balance_all += ($loan_amount_balance+$interest_amount);
			$atm_transaction = array();
			$atm_transaction['loan_atm_id'] = $_POST['loan_atm_id'];
			$atm_transaction['loan_amount_balance'] = $loan_amount_balance_all;
			$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
			//$this->loan_libraries->atm_transaction($atm_transaction);
            $this->loan_libraries->atm_transaction_arrears('CL', $atm_transaction);

			$receipt_arr[$r]['receipt_id'] = $receipt_id;
			$receipt_arr[$r]['member_id'] = @$_POST['member_id'];
			$receipt_arr[$r]['loan_id'] = $loan_id;
			$receipt_arr[$r]['account_list_id'] = '15';
			$receipt_arr[$r]['principal_payment'] = $loan_amount_balance;
			$receipt_arr[$r]['interest'] = $interest_amount;
			$receipt_arr[$r]['total_amount'] = $loan_amount_balance+$interest_amount;
			$receipt_arr[$r]['payment_date'] = date('Y-m-d');
			$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
			$receipt_arr[$r]['loan_amount_balance'] = '0';
			$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$contract_number;
			$receipt_arr[$r]['deduct_type'] = @$pay_type;
			$r++;
			
			$data_insert = array();
			$data_insert['loan_amount_balance'] = '0';
			$data_insert['loan_status'] = '4';
			$this->db->where('id', $loan_id);
			$this->db->update('coop_loan', $data_insert);
			
			$loan_transaction = array();
			$loan_transaction['loan_id'] = $loan_id;
			$loan_transaction['loan_amount_balance'] = '0';
			$loan_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
			//$this->loan_libraries->loan_transaction($loan_transaction);
			$this->loan_libraries->loan_transaction_arrears('CL', $loan_transaction);
		}
		
		
			$this->db->select(array('t1.*',
									't2.contract_number',
									't2.total_amount_approve',
									't2.total_amount_balance',
									't2.loan_atm_id'
									));
			$this->db->from("coop_loan_atm_prev_deduct t1");
			$this->db->join("coop_loan_atm t2",'t1.ref_id = t2.loan_atm_id','inner');
			$this->db->where("t1.loan_atm_id = '".@$_POST['loan_atm_id']."'");
			$row = $this->db->get()->result_array();
			//echo '<pre>'; print_r($row); echo '</pre>';

			foreach($row as $key => $value){				
				$contract_number = @$value['contract_number'];
				//echo $contract_number.'<hr>';
				$total_amount_approve = @$value['total_amount_approve'];
				$pay_type = @$value['pay_type'];
				if($value['pay_type'] == 'principal'){					
					$loan_id = @$value['loan_atm_id'];
					$loan_amount_balance = @$value['total_amount_approve'] - @$value['total_amount_balance'];
					$interest_amount = 0;
				}else{
					$loan_id = @$value['loan_atm_id'];
					$loan_amount_balance = @$value['loan_amount_balance'];
					$cal_loan_interest = array();
					$cal_loan_interest['loan_id'] = @$value['ref_id'];
					$cal_loan_interest['entry_date'] = $this->center_function->ConvertToSQLDate(@$date_interesting);
					//$interest_amount = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
                    $interest_amount = $this->ATMCalc->calc('PL', $cal_loan_interest);
				}
				
				$data_insert = array();
				$data_insert['loan_atm_id'] = @$_POST['loan_atm_id'];
				$data_insert['member_id'] = @$_POST['member_id'];
				$data_insert['loan_amount'] = ($loan_amount_balance +$interest_amount);
				$data_insert['loan_amount_balance'] = ($loan_amount_balance +$interest_amount);
				$data_insert['loan_date'] = date('Y-m-d H:i:s');

				$data_insert['petition_number'] = $this->loan_libraries->get_petition_atm_number();
				$data_insert['loan_status'] = '0';
				$data_insert['loan_description'] = 'หักกลบสัญญาเก่า '.$contract_number;
				$data_insert['date_start_period'] = date('Y-m-t',strtotime('+1 month'));
				$data_insert['transaction_at'] = '0';
				$data_insert['transfer_status'] = '1';
				$this->db->insert('coop_loan_atm_detail', $data_insert);
				
				$loan_amount_balance_all += ($loan_amount_balance+$interest_amount);
				$atm_transaction = array();
				$atm_transaction['loan_atm_id'] = $_POST['loan_atm_id'];
				$atm_transaction['loan_amount_balance'] = $loan_amount_balance_all;
				$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
				//$this->loan_libraries->atm_transaction($atm_transaction);
				$this->loan_libraries->atm_transaction_arrears('CL', $atm_transaction);

				$receipt_arr[$r]['receipt_id'] = $receipt_id;
				$receipt_arr[$r]['member_id'] = @$_POST['member_id'];
				$receipt_arr[$r]['loan_atm_id'] = $loan_id;
				$receipt_arr[$r]['account_list_id'] = '31';
				$receipt_arr[$r]['principal_payment'] = $loan_amount_balance;
				$receipt_arr[$r]['interest'] = $interest_amount;
				$receipt_arr[$r]['total_amount'] = ($loan_amount_balance+$interest_amount);
				$receipt_arr[$r]['payment_date'] = date('Y-m-d');
				$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
				$receipt_arr[$r]['loan_amount_balance'] = '0';
				$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$contract_number;
				$receipt_arr[$r]['deduct_type'] = $pay_type;
				$r++;
				
				$data_insert = array();
				$data_insert['total_amount_balance'] = $total_amount_approve;
				$data_insert['loan_atm_status'] = '3';
				$this->db->where('loan_atm_id', $loan_id);
				$this->db->update('coop_loan_atm', $data_insert);
					
				$data_insert = array();
				$data_insert['loan_amount_balance'] = '0';
				$data_insert['loan_status'] = '1';
				$this->db->where('loan_atm_id', $loan_id);
				$this->db->update('coop_loan_atm_detail', $data_insert);
				
				$atm_transaction = array();
				$atm_transaction['loan_atm_id'] = $loan_id;
				$atm_transaction['loan_amount_balance'] = '0';
				$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
				//$this->loan_libraries->atm_transaction($atm_transaction);
				$this->loan_libraries->atm_transaction_arrears('01',$atm_transaction);
			}
			
			$sum_count = 0;
			foreach($receipt_arr as $key => $value){
				$data_insert = array();
				$data_insert['receipt_id'] = $value['receipt_id'];
				$data_insert['receipt_list'] = $value['account_list_id'];
				$data_insert['receipt_count'] = $value['total_amount'];
				$this->db->insert('coop_receipt_detail', $data_insert);
				
				//บันทึกการชำระเงิน
				$data_insert = array();
				$data_insert['receipt_id'] = $value['receipt_id'];
				$data_insert['member_id'] = @$value['member_id'];
				$data_insert['loan_id'] = @$value['loan_id'];
				$data_insert['loan_atm_id'] = @$value['loan_atm_id'];
				$data_insert['account_list_id'] = $value['account_list_id'];
				$data_insert['principal_payment'] = @$value['principal_payment'];
				$data_insert['interest'] = @$value['interest'];
				$data_insert['total_amount'] = @$value['total_amount'];
				$data_insert['payment_date'] = @$value['payment_date'];
				$data_insert['loan_amount_balance'] = @$value['loan_amount_balance'];
				$data_insert['createdatetime'] = @$value['createdatetime'];
				$data_insert['transaction_text'] = @$value['transaction_text'];
				$data_insert['deduct_type'] = @$value['deduct_type'];
				$this->db->insert('coop_finance_transaction', $data_insert);
				$sum_count += @$value['total_amount'];
				
				if(@$value['loan_id'] !='' && @$value['interest'] > 0){
					$data_insert = array();
					$data_insert['date_last_interest'] = date('Y-m-d H:i:s');
					$this->db->where('id',@$value['loan_id']);
					$this->db->update('coop_loan',$data_insert);
				}
				if(@$value['loan_atm_id'] !='' && @$value['interest'] > 0){
					$data_insert = array();
					$data_insert['date_last_interest'] = date('Y-m-d H:i:s');
					$this->db->where('loan_atm_id',@$value['loan_atm_id']);
					$this->db->update('coop_loan_atm',$data_insert);
				}
			}
			if($sum_count>0){
				$data_insert = array();
				$data_insert['receipt_id'] = $receipt_id;
				$data_insert['member_id'] = @$member_id;
				$data_insert['admin_id'] = @$_SESSION['USER_ID'];
				$data_insert['sumcount'] = $sum_count;
				$data_insert['receipt_datetime'] = date('Y-m-d H:i:s');
				$this->db->insert('coop_receipt', $data_insert);
				
				$data_insert = array();
				$data_insert['deduct_receipt_id'] = $receipt_id;
				$data_insert['total_amount_balance'] = $row_atm['total_amount_approve'] - $sum_count;
				$this->db->where('loan_atm_id',$_POST['loan_atm_id']);
				$this->db->update('coop_loan_atm',$data_insert);
			}
/////////////////////////////////////////////////////////// หักกลบ ////////////////////////////////////////////////////////////////
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm/loan_atm_approve')."' </script>";
		exit;
	}
	
	function show_loan_atm_detail($loan_atm_id=''){
		$arr_data = array();
		
		$this->db->select(array('t1.*','t2.firstname_th','t2.lastname_th','t3.prename_short'));
		$this->db->from('coop_loan_atm as t1');
		$this->db->join("coop_mem_apply as t2",'t1.member_id = t2.member_id','inner');
		$this->db->join("coop_prename as t3",'t2.prename_id = t3.prename_id','left');
		$this->db->where("loan_atm_id = '".$loan_atm_id."'");
		$row = $this->db->get()->result_array();
		$row_loan_atm = @$row[0];
		$arr_data['row_loan_atm'] = $row_loan_atm;
		
		$this->db->select(array('t1.*','t2.pay_type'));
		$this->db->from('coop_loan_atm_detail as t1');
		$this->db->join("coop_loan_atm_transfer as t2",'t1.loan_id = t2.loan_id','left');
		$this->db->where("loan_atm_id = '".$loan_atm_id."'");
		$this->db->order_by("loan_date ASC");
		$row = $this->db->get()->result_array();
		$arr_data['row_loan_atm_detail'] = $row;
		
		$arr_data['transaction_at'] = $this->transaction_at;
		$arr_data['pay_type'] = $this->pay_type;
		
		$this->preview_libraries->template_preview('loan_atm/show_loan_atm_detail',$arr_data);
	}
	
	function loan_atm_transfer(){
		$arr_data = array();
			$this->db->select(array(
				't1.loan_id', 
				't1.loan_atm_id', 
				't1.loan_date',
				't1.loan_amount',
				't1.transfer_status',
				't2.contract_number',
				't2.approve_date',
				't3.member_id',
				't3.firstname_th',
				't3.lastname_th',
				't4.id as transfer_id',
				't4.file_name',
				't4.date_transfer',
				't4.pay_type'
			));
			$this->db->from('coop_loan_atm_detail as t1');
			$this->db->join('coop_loan_atm as t2','t2.loan_atm_id = t1.loan_atm_id','inner');
			$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
			$this->db->join("coop_loan_atm_transfer as t4","t1.loan_id = t4.loan_id",'left');
			$this->db->join('coop_maco_account as t5','t4.account_id = t5.account_id','left');
			$this->db->join('coop_user as t6','t6.user_id = t4.admin_id','left');
			$this->db->where("t1.loan_id = '".@$_GET['loan_id']."'");
			$this->db->order_by("t1.loan_id DESC");
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			//echo $this->db->last_query();exit;
			$row = @$row[0];
			$arr_data['row'] = $row;
			//echo"<pre>";print_r($arr_data['row']);echo"</pre>";exit;
			if(@$row['member_id']!=''){
				$this->db->select(array('*'));
				$this->db->from('coop_maco_account');
				$this->db->where("mem_id = '".$row['member_id']."' AND account_status = '0'");
				$rs_account = $this->db->get()->result_array();
				$arr_data['rs_account'] = @$rs_account;
			}
		
		$arr_data['pay_type'] = $this->pay_type;
		
		$this->db->select(array('t1.loan_id','t1.loan_amount','t2.contract_number'));
		$this->db->from('coop_loan_atm_detail as t1');
		$this->db->join('coop_loan_atm as t2','t2.loan_atm_id = t1.loan_atm_id','inner');
		$this->db->where("t1.transfer_status = '0'");
		$row = $this->db->get()->result_array();
		$arr_data['transfer_list'] = $row;
		
		$this->libraries->template('loan_atm/loan_atm_transfer',$arr_data);
	}
	function get_loan_atm_detail_data(){
		$this->db->select(array(
			't1.loan_id', 
			't1.loan_atm_id', 
			't1.loan_date',
			't1.loan_amount',
			't1.transfer_status',
			't2.contract_number',
			't2.approve_date',
			't3.member_id',
			't3.firstname_th',
			't3.lastname_th',
			't4.id as transfer_id',
			't4.file_name',
			't4.date_transfer',
			't4.pay_type'
		));
		$this->db->from('coop_loan_atm_detail as t1');
		$this->db->join('coop_loan_atm as t2','t2.loan_atm_id = t1.loan_atm_id','inner');
		$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
		$this->db->join("coop_loan_atm_transfer as t4","t1.loan_id = t4.loan_id",'left');
		$this->db->join('coop_maco_account as t5','t4.account_id = t5.account_id','left');
		$this->db->join('coop_user as t6','t6.user_id = t4.admin_id','left');
		$this->db->where("t1.loan_id = '".@$_POST['loan_id']."' AND t1.transfer_status = '0'");
		$this->db->order_by("t1.loan_id DESC");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		//echo $this->db->last_query();
		if(!empty($row)){
			$data = array();
			$data['result'] = 'success';
			$data['coop_loan_atm'] = $row[0];
			$data['coop_loan_atm']['loan_amount'] = number_format($data['coop_loan_atm']['loan_amount']);
			$data['coop_loan_atm']['loan_date'] = $this->center_function->mydate2date($data['coop_loan_atm']['loan_date'],true);
		}else{
			$data['result'] = 'not_found';
		}
		echo json_encode($data);	  
		
		exit;
	}
	
	function loan_atm_transfer_save(){
		//echo"<pre>";print_r($_POST);echo"</pre>";exit;
		
		$this->db->select(array(
			'loan_amount'
		));
		$this->db->from('coop_loan_atm_detail');
		$this->db->where("loan_id = '".$_POST['loan_id']."'");
		$row_loan = $this->db->get()->result_array();
		$row_loan = $row_loan[0];
		
		$date_arr = explode('/',$_POST['date_transfer']);
		$date_transfer = ($date_arr[2]-543)."-".$date_arr[1]."-".$date_arr[0]." ".$_POST['time_transfer'];
		if($_POST['account_id']!=''){
			$this->db->select(array(
				'transaction_balance'
			));
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '".$_POST['account_id']."'");
			$this->db->order_by('transaction_id DESC');
			$this->db->limit(1);
			$row_prev_trans = $this->db->get()->result_array();
			$row_prev_trans = $row_prev_trans[0];
			
			$transaction_balance = $row_prev_trans['transaction_balance'] + $row_loan['loan_amount'];
			
			$data_insert = array();
			$data_insert['transaction_time'] = $date_transfer;
			$data_insert['transaction_list'] = 'XD';
			$data_insert['transaction_withdrawal'] = '0';
			$data_insert['transaction_deposit'] = $row_loan['loan_amount'];
			$data_insert['transaction_balance'] = $transaction_balance;
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['account_id'] = $_POST['account_id'];
			$this->db->insert('coop_account_transaction', $data_insert);
			$pay_type = '1';
		}else{
			$pay_type = '0';
		}
		
		$data_insert = array();
		$data_insert['loan_id'] = $_POST['loan_id'];
		$data_insert['account_id'] = $_POST['account_id'];
		$data_insert['date_transfer'] = $date_transfer;
		$data_insert['createdatetime'] = date('Y-m-d H:i:s');
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['transfer_status'] = '0';
		$data_insert['pay_type'] = $pay_type;
		$this->db->insert('coop_loan_atm_transfer', $data_insert);
		
		$last_id = $this->db->insert_id();
		
		$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/loan_atm_transfer_attach/";
		
		if($_FILES['file_attach']['name']!=''){
			$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES['file_attach']['name']);
			@copy($_FILES["file_attach"]["tmp_name"],$output_dir.$new_file_name);
			
			$data_insert = array();
			$data_insert['file_name'] = $new_file_name;
			$this->db->where('id', $last_id);
			$this->db->update('coop_loan_atm_transfer', $data_insert);
		}
		
		$data_insert = array();
		$data_insert['transfer_status'] = '1';
		$this->db->where('loan_id', $_POST['loan_id']);
		$this->db->update('coop_loan_atm_detail', $data_insert);
		
		/*$this->db->select(array(
			't1.account_chart_id',
			't2.account_chart'
		));
		$this->db->from('coop_account_match as t1');
		$this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','left');
		$this->db->where("
			t1.match_type = 'loan'
			AND t1.match_id = '".$row_loan['loan_type']."'
		");
		$row_account_match = $this->db->get()->result_array();
		$row_account_match = @$row_account_match[0];
		
		$data = array();
		$data['coop_account']['account_description'] = "โอนเงินให้".$row_account_match['account_chart'];
		$data['coop_account']['account_datetime'] = $date_transfer;
		
		$i=0;
		$data['coop_account_detail'][$i]['account_type'] = 'debit';
		$data['coop_account_detail'][$i]['account_amount'] = $row_loan['loan_amount'];
		$data['coop_account_detail'][$i]['account_chart_id'] = $row_account_match['account_chart_id'];
		$i++;
		$data['coop_account_detail'][$i]['account_type'] = 'credit';
		$data['coop_account_detail'][$i]['account_amount'] = $row_loan['loan_amount'];
		$data['coop_account_detail'][$i]['account_chart_id'] = '10100';
		$this->account_transaction->account_process($data);*/
	
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm/loan_atm_transfer?loan_id='.$_POST['loan_id'])."' </script>";
		exit;
	}
	
	public function loan_atm_all()
	{
		$arr_data = array();

		$x=0;
		$join_arr = array();
		/*$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_mem_apply.member_id = coop_loan_atm.member_id';
		$join_arr[$x]['type'] = 'left';		
		$x++;
		
		$join_arr[$x]['table'] = 'coop_prename';
		$join_arr[$x]['condition'] = 'coop_prename.prename_id = coop_mem_apply.prename_id';
		$join_arr[$x]['type'] = 'left';
		*/
		
		$this->paginater_all->type(DB_TYPE);
		//$this->paginater_all->select('coop_loan_atm.*,coop_mem_apply.*,coop_prename.prename_short');
		$this->paginater_all->select('
										coop_loan_atm.contract_number,
										coop_loan_atm.member_id,
										coop_loan_atm.total_amount_approve,
										coop_loan_atm.total_amount_balance,
										coop_loan_atm.loan_atm_id
										');
		$this->paginater_all->main_table('coop_loan_atm');
		$this->paginater_all->where("coop_loan_atm.loan_atm_status = '1'");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		//$this->paginater_all->order_by('coop_mem_apply.mem_apply_id DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		foreach($row['data'] AS $key=>$value){
			$this->db->select('coop_mem_apply.firstname_th, coop_mem_apply.lastname_th,coop_prename.prename_short');
			$this->db->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left");
			$this->db->from("coop_mem_apply");
			$this->db->where("member_id = '".@$value['member_id']."'");
			$rs_member = $this->db->get()->result_array();
			$row_member = @$rs_member[0];
			$row['data'][$key]['prename_short'] = $row_member['prename_short'];
			$row['data'][$key]['firstname_th'] = $row_member['firstname_th'];
			$row['data'][$key]['lastname_th'] = $row_member['lastname_th'];
		}
		
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['row'] = $row['data'];
		$arr_data['i'] = $i;

		$this->libraries->template('loan_atm/loan_atm_all',$arr_data);
	}
	
	function get_search_loan(){
		$where = "
		 	 AND (coop_mem_apply.member_id LIKE '%".$this->input->post('search_text')."%'
		 	OR coop_mem_apply.firstname_th LIKE '%".$this->input->post('search_text')."%'
			OR coop_mem_apply.lastname_th LIKE '%".$this->input->post('search_text')."%'
			OR coop_loan_atm.contract_number LIKE '%".$this->input->post('search_text')."%')
		";
		$this->db->select(array(
					'coop_mem_apply.id',
					'coop_mem_apply.member_id',
					'coop_mem_apply.firstname_th',
					'coop_mem_apply.lastname_th',
					'coop_loan_atm.loan_atm_id',
					'coop_loan_atm.contract_number',
					'coop_loan_atm.total_amount_approve',
					'coop_loan_atm.total_amount_balance'
		));
		$this->db->from('coop_loan_atm');
		$this->db->join("coop_mem_apply","coop_mem_apply.member_id = coop_loan_atm.member_id","left");
		$this->db->where("loan_atm_status = '1'".$where);
		$this->db->order_by('coop_mem_apply.mem_apply_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;
		$arr_data['form_target'] = $this->input->post('form_target');
		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->load->view('loan_atm/get_search_loan',$arr_data);
	}
	
	function loan_cancel_contract(){
		//echo"<pre>";print_r($_POST);echo"</pre>";exit;
		$data_insert = array();
		$data_insert['loan_atm_status'] = '3';
		$this->db->where('loan_atm_id',$_POST['loan_atm_id']);
		$this->db->update('coop_loan_atm',$data_insert);
		
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm?member_id='.$_POST['member_id'])."' </script>";
		exit;
	}
	
	function petition_emergent_atm_pdf($loan_atm_id){
		$arr_data = array();
		
		$this->db->select(array(
			't1.*',
			't2.*',
			't3.prename_short',
			't4.district_name',
			't5.amphur_name',
			't6.province_name',
			't7.mem_group_name',
			't8.loan_reason'
		));
		$this->db->from('coop_loan_atm as t1');
		$this->db->join("coop_mem_apply as t2","t2.member_id = t1.member_id","inner");
		$this->db->join("coop_prename as t3","t3.prename_id = t2.prename_id","left");
		$this->db->join("coop_district as t4","t2.c_district_id = t4.district_id","left");
		$this->db->join("coop_amphur as t5","t2.c_amphur_id = t5.amphur_id","left");
		$this->db->join("coop_province as t6","t2.c_province_id = t6.province_id","left");
		$this->db->join("coop_mem_group as t7","t2.level = t7.id","left");
		$this->db->join("coop_loan_reason as t8","t1.loan_reason = t8.loan_reason_id","left");
		$this->db->where("t1.loan_atm_id = '".$loan_atm_id."'");
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row[0];
		
		$this->load->view('loan_atm/petition_emergent_atm_pdf',$arr_data);
	}
	
	function loan_change_amount(){
		// echo"<pre>";print_r($_POST);echo"</pre>";exit;
        $date_req = (@$_POST['createdatetime'] != '')?$this->center_function->ConvertToSQLDate($_POST['createdatetime']).' '.$_POST['time']:date('Y-m-d H:i:s');
		$total_amount = str_replace(',','',$_POST['total_amount']);
		$this->db->select(array('total_amount_approve','total_amount_balance'));
		$this->db->from('coop_loan_atm');
		$this->db->where("loan_atm_id = '".$_POST['loan_atm_id']."'");
		$row = $this->db->get()->result_array();
		$row_loan_atm = @$row[0];
		$loan_amount_balance = $row_loan_atm['total_amount_approve'] - $row_loan_atm['total_amount_balance'];
		//echo $total_amount.":".$loan_amount_balance;exit; 
		$data_insert = array();
		$data_insert['change_amount_status'] = '1';
		$this->db->where('loan_atm_id',$_POST['loan_atm_id']);
		$this->db->update('coop_loan_atm', $data_insert);
		
		$data_insert = array();
		$data_insert['member_id'] = $_POST['member_id'];
		$data_insert['total_amount'] = $total_amount;
		$data_insert['total_amount_balance'] = $data_insert['total_amount'];
		$data_insert['createdatetime'] = $date_req;
		$data_insert['loan_atm_status'] = '0';
		$data_insert['loan_reason'] = $_POST['loan_reason'];
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['change_from'] = $_POST['loan_atm_id'];
		$data_insert['max_period'] = $_POST['max_period'];
		$data_insert['pay_type'] = $_POST['pay_type'];
		$data_insert['period_payment_amount'] = $_POST['period_payment_amount'];

		$data_insert['petition_number'] = $this->loan_libraries->get_petition_atm_number();
		
		$this->db->insert('coop_loan_atm',$data_insert);
		
		$loan_atm_id = $this->db->insert_id();

		//prev loan atm

		$pay_type = isset($_POST['interest_amount']) && $_POST['interest_amount'] < 0 ? 'principal' : 'all';

		$data_prev_loan_atm = array(
			'loan_atm_id' => $loan_atm_id,
			'ref_id' => $_POST['loan_atm_id'],
			'data_type' => 'atm',
			'pay_type' => $pay_type,
			'principal_amount' => $_POST['principal_amount'],
			'interest_amount' => $_POST['interest_amount'],
			'pay_amount' => $_POST['pay_amount']
		);
		$this->db->insert('coop_loan_atm_prev_deduct', $data_prev_loan_atm);

		if($loan_atm_id!=''){
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/loan_atm_attach/";
			if($_FILES['file_attach']['name'][0]!=''){
				foreach($_FILES['file_attach']['name'] as $key => $value){
					$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES['file_attach']['name'][$key]);
					@copy($_FILES["file_attach"]["tmp_name"][$key],$output_dir.$new_file_name);
					
					$data_insert = array();
					$data_insert['loan_atm_id'] = $loan_atm_id;
					$data_insert['file_old_name'] = $_FILES['file_attach']['name'][$key];
					$data_insert['file_name'] = $new_file_name;
					$data_insert['file_path'] = $output_dir.$new_file_name;
					$data_insert['file_type'] = $_FILES['file_attach']['type'][$key];
					$this->db->insert('coop_loan_atm_file_attach', $data_insert);
				}
			}
		}

		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm?member_id='.@$_POST['member_id'])."' </script>";
	}
	
	function loan_atm_transfer_view($transfer_type='',$start_date='',$end_date=''){
		$arr_data = array();
		
		/*$this->db->select(array('t1.*','t2.firstname_th','t2.lastname_th','t3.prename_short'));
		$this->db->from('coop_loan_atm as t1');
		$this->db->join("coop_mem_apply as t2",'t1.member_id = t2.member_id','inner');
		$this->db->join("coop_prename as t3",'t2.prename_id = t3.prename_id','left');
		$this->db->where("loan_atm_id = '".$loan_atm_id."'");
		$row = $this->db->get()->result_array();
		$row_loan_atm = @$row[0];
		$arr_data['row_loan_atm'] = $row_loan_atm;
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm_detail');
		$this->db->where("loan_atm_id = '".$loan_atm_id."'");
		$this->db->order_by("loan_date ASC");
		$row = $this->db->get()->result_array();
		$arr_data['row_loan_atm_detail'] = $row;
		
		$arr_data['transaction_at'] = $this->transaction_at;
		*/
		
		$this->preview_libraries->template_preview('loan_atm/loan_atm_transfer_view',$arr_data);
	}
	
	function payment_slip($loan_atm_detail_id){
		$arr_data = array();
		
		$this->db->select(array(
			't1.*',
			't2.contract_number',
			't2.total_amount_approve',
			't2.total_amount_balance',
			't3.firstname_th',
			't3.lastname_th',
			't4.prename_short',
			't3.dividend_acc_num',
			't5.mem_group_name as level_name',
			't6.mem_group_name as faction_name',
			't7.mem_group_name as department_name'
		));
		$this->db->from('coop_loan_atm_detail as t1');
		$this->db->join('coop_loan_atm as t2','t1.loan_atm_id = t2.loan_atm_id','inner');
		$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
		$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
		$this->db->join('coop_mem_group as t5','t3.level = t5.id','left');
		$this->db->join('coop_mem_group as t6','t3.faction = t6.id','left');
		$this->db->join('coop_mem_group as t7','t3.department = t7.id','left');
		$this->db->where("t1.loan_id = '".$loan_atm_detail_id."'");
		$row = $this->db->get()->result_array();
		//echo"<pre>";print_r($row[0]);echo"</pre>";
		$arr_data['data'] = @$row[0];
		$this->preview_libraries->template_preview('loan_atm/payment_slip',$arr_data);
	}
	
	function loan_atm_payment_detail($loan_atm_id){
		$arr_data = array();
		
		$this->db->select(array(
			't1.*',
			't2.firstname_th',
			't2.lastname_th',
			't3.prename_short'
		));
		$this->db->from('coop_loan_atm as t1');
		$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
		$this->db->join('coop_prename as t3','t2.prename_id = t3.prename_id','left');
		$this->db->where("t1.loan_atm_id = '".$loan_atm_id."'");
		$loan_data = $this->db->get()->result_array();
		$arr_data['loan_data'] = $loan_data[0];
		
		/*
		$this->db->select(array('t1.*','t2.finance_month_profile_id'));
		$this->db->from('coop_finance_transaction as t1');
		$this->db->join('coop_receipt as t2','t1.receipt_id = t2.receipt_id','inner');
		$this->db->where("t1.loan_atm_id = '".$loan_atm_id."'");
		$this->db->order_by('payment_date ASC');
		*/
		// $this->db->select("
		// 					t1.transaction_datetime,
		// 					DATE(t1.transaction_datetime) AS payment_date,
		// 					t1.receipt_id,
		// 					t2.loan_description,
		// 					SUM(IF(t2.loan_amount <> '',t2.loan_amount,t3.principal_payment)) AS principal,
		// 					SUM(t3.interest) AS interest,
		// 					SUM(t3.total_amount) AS total_amount,
		// 					t1.loan_amount_balance,
		// 					IF(t2.loan_description != '',t2.loan_description,IF(t4.finance_month_profile_id != '','ชำระเงินรายเดือน','ชำระเงินอื่นๆ')) AS data_text
		// 				");
		// $this->db->from('coop_loan_atm_transaction AS t1');
		// $this->db->join('coop_loan_atm_detail AS t2','t1.loan_atm_id = t2.loan_atm_id AND t1.transaction_datetime = t2.loan_date ','left');
		// $this->db->join('coop_finance_transaction AS t3','t1.receipt_id = t3.receipt_id AND t1.loan_atm_id = t3.loan_atm_id','left');
		// $this->db->join('coop_receipt AS t4','t3.receipt_id = t4.receipt_id','left');
		// $this->db->where("t1.loan_atm_id = '".$loan_atm_id."'");
		// $this->db->group_by('t1.transaction_datetime');
		// $this->db->order_by('t1.transaction_datetime ASC');
		/*$query = $this->db->query("select * from (SELECT
						`t1`.`transaction_datetime`,
						DATE(t1.transaction_datetime) AS payment_date,
						`t1`.`receipt_id`,
						`t2`.`loan_description`,
						SUM(

							IF (
								t2.loan_amount <> '',
								`t2`.`loan_amount`,
								IF (
									t7.text_error != '',
									t7.loan_amount,
									t3.principal_payment
								)
							)
						) AS principal,
						SUM(t3.interest) AS interest,
						SUM(t3.total_amount) AS total_amount,
						IF (
						! ISNULL(
							(
								SELECT
									ret_id
								FROM
									coop_process_return
								WHERE
									coop_process_return.return_month = t4.month_receipt
								AND coop_process_return.return_year = (t4.year_receipt - 543)
								AND coop_process_return.loan_atm_id = t1.loan_atm_id
								LIMIT 1
							)
						),
						
						IF (
								t2.loan_amount <> '',
								`t2`.`loan_amount`,
								IF( 
										t1.loan_amount_balance <> 0,
										 t1.loan_amount_balance,
										(	
											SELECT
												IF(t9.return_principal > 0,(t9.return_principal) * - 1,0) AS return_principal
											FROM
												coop_finance_transaction AS t7
											LEFT JOIN coop_receipt AS t8 ON t7.receipt_id = t8.receipt_id
											LEFT JOIN coop_process_return AS t9 ON t9.return_month = t8.month_receipt AND t9.return_year = (t8.year_receipt-543) AND t7.loan_atm_id = t9.loan_atm_id
											WHERE
												t7.principal_payment <> 0	AND t7.receipt_id = t3.receipt_id	AND t7.loan_atm_id = t3.loan_atm_id
											LIMIT 1	
										)	
								)
							),
						`t1`.`loan_amount_balance`
					) AS loan_amount_balance,

					IF (
						t2.loan_description != '',
						`t2`.`loan_description`,

					IF (
						t4.finance_month_profile_id != '',
						'ชำระเงินรายเดือน',
						IF (
							t7.text_error != '',
							t7.text_error,
							'ชำระเงินอื่นๆ'
						)
					)
					) AS data_text
					FROM
						`coop_loan_atm_transaction` AS `t1`
					LEFT JOIN `coop_loan_atm_detail` AS `t2` ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id`
					AND `t1`.`transaction_datetime` = `t2`.`loan_date`
					LEFT JOIN `coop_finance_transaction` AS `t3` ON `t1`.`receipt_id` = `t3`.`receipt_id`
					AND `t1`.`loan_atm_id` = `t3`.`loan_atm_id`
					LEFT JOIN `coop_receipt` AS `t4` ON `t3`.`receipt_id` = `t4`.`receipt_id`
					LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
					LEFT JOIN coop_loan_atm_transaction_error AS t7 ON t1.loan_atm_transaction_id = t7.loan_atm_transaction_id
					WHERE
						`t1`.`loan_atm_id` = '".$loan_atm_id."'
					GROUP BY
						t1.transaction_datetime,t1.loan_atm_transaction_id
					UNION ALL
					SELECT return_time,date(return_time),null,CONCAT('คืนเงิน ',receipt_id),IF(return_principal>0,(return_principal)* - 1,(return_amount) * - 1),IF(return_interest>0,(return_interest)* - 1,0),IF(return_amount>0,(return_amount)* - 1,0),null,CONCAT('คืนเงิน ',receipt_id)FROM coop_process_return WHERE return_type = 3 and loan_atm_id = '".$loan_atm_id."'
					) as m
					ORDER BY
					m.`transaction_datetime` ASC"
		);
		*/
		
		$query = $this->db->query(
			"select 
                t1.loan_atm_transaction_id,
                date_format(t1.transaction_datetime, '%Y-%m-%d') as payment_date,
                t1.receipt_id,	
                t2.loan_description,
                IF(t8.return_amount is null, IF( t9.atm_type_code='MR', (null), IF(t9.atm_type_code = 'AT', t2.loan_amount, t3.principal*t9.atm_sign_mode*-1)), t8.return_principal*t9.atm_sign_mode*-1) as principal,
                IF(t8.return_amount is null, IF( t9.atm_type_code='MR', t3.interest*t9.atm_sign_mode, IF(t9.atm_type_code = 'AT', (null), t3.interest*t9.atm_sign_mode*-1)), t8.return_interest*t9.atm_sign_mode*-1) as interest,
                IF(t8.return_amount is null, IF( t9.atm_type_code='MR', t3.interest*t9.atm_sign_mode, IF(t9.atm_type_code = 'AT', t2.loan_amount, t3.total_amount*t9.atm_sign_mode*-1)), t8.return_amount*t9.atm_sign_mode*-1) as total_amount,
                t1.loan_amount_balance as loan_amount_balance,
                t9.atm_type_name as data_text,
                t9.atm_type_code
                FROM
                    `coop_loan_atm_transaction` AS `t1`
                LEFT JOIN `coop_loan_atm_detail` AS `t2` ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id`
                AND `t1`.`transaction_datetime` = `t2`.`loan_date`
                LEFT JOIN (SELECT receipt_id,payment_date,loan_atm_id,sum(principal_payment) AS principal,sum(interest) AS interest, sum(total_amount) as total_amount FROM coop_finance_transaction WHERE loan_atm_id='{$loan_atm_id}' GROUP BY loan_atm_id,payment_date,receipt_id) AS `t3` ON `t1`.`receipt_id` = `t3`.`receipt_id`
                AND `t1`.`loan_atm_id` = `t3`.`loan_atm_id`
                LEFT JOIN `coop_receipt` AS `t4` ON `t3`.`receipt_id` = `t4`.`receipt_id`
                LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
                LEFT JOIN coop_loan_atm_transaction_error AS t7 ON t1.loan_atm_transaction_id = t7.loan_atm_transaction_id
                LEFT JOIN coop_process_return AS t8 ON t1.loan_atm_id = t8.loan_atm_id AND t1.receipt_id = t8.bill_id
                LEFT JOIN coop_atm_type_setting AS t9 ON t1.loan_type_code=t9.atm_type_code
                WHERE
                    `t1`.`loan_atm_id` = '{$loan_atm_id}'
                GROUP BY
                    t1.transaction_datetime ASC,t1.loan_atm_transaction_id ASC
                ");
		
		$row_transaction = $query->result_array();
		if(@$_GET['dev'] == 'dev'){
			echo $this->db->last_query(); exit;
		}
		$arr_data['transaction_data'] = $row_transaction;
		// echo"<pre>";print_r($transaction_data);exit;
		$this->preview_libraries->template_preview('loan_atm/loan_atm_payment_detail',$arr_data);
	}
	
	function loan_atm_lock($loan_atm_id,$member_id){
		$data_insert = array();
		$data_insert['activate_status'] = '1';
		$this->db->where('loan_atm_id',$loan_atm_id);
		$this->db->update('coop_loan_atm',$data_insert);
		$this->center_function->toast("ระงับสัญญาเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm?member_id='.@$member_id)."' </script>";
	}
	function loan_atm_unlock($loan_atm_id,$member_id){
		$data_insert = array();
		$data_insert['activate_status'] = '0';
		$this->db->where('loan_atm_id',$loan_atm_id);
		$this->db->update('coop_loan_atm',$data_insert);
		$this->center_function->toast("ปลดระงับสัญญาเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm?member_id='.@$member_id)."' </script>";
	}
	
	function ajax_get_loan_atm_prev_deduct(){
		$loan_atm_id = isset($_POST['loan_atm_id']) ? trim(@$_POST['loan_atm_id']) : "";
		
		$arr_data = array();
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm');
		$this->db->where("loan_atm_id = '".$loan_atm_id."' AND loan_atm_status = '0'");
		$rs_loan = $this->db->get()->result_array();
		$arr_data['coop_loan_atm'] = @$rs_loan[0];	
		//echo '<pre>'; print_r($arr_data['coop_loan_atm']); echo '</pre>';
		//รายการที่หักกลบ
		$this->db->select(array('*'));
		$this->db->from("coop_loan_atm_prev_deduct");
		$this->db->where("loan_atm_id = '".@$loan_atm_id."'");
		$rs_prev_deduct = $this->db->get()->result_array();
		$arr_data['coop_loan_atm_prev_deduct'] = @$rs_prev_deduct;	
		//echo '<pre>'; print_r($rs_prev_deduct); echo '</pre>';
		//if(!empty($rs_prev_deduct)){
		if(!empty($arr_data)){
			echo json_encode($arr_data);
		}else{
			echo 'not_found';
		}
		exit;
	}
	
	//ระบบ ATM offline
	public function atm_file_upload(){
	    $arr_data = array();

        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $where =" YEAR(`file_date`)='{$year}' AND MONTH(`file_date`)='{$month}' ";
	    $this->db->select(array(
	        'id', 'file_date', 'file_name', 'active_status', 'status', 'createdatetime', 'submit_date'
        ));
	    $this->db->from("coop_loan_atm_file_upload")->where($where);
        $data = $this->db->get()->result_array();
        $list = array();
        foreach ($data as $key => $item){
            $list[date('Y-m-d', strtotime($item['file_date']))] = $item;
        }
        $arr_data['list'] = $list;
        $this->libraries->template('loan_atm/upload_file',$arr_data);
    }

    public function atm_receive(){
	    $arr_data = array();
	    $this->db->select("*");
	    $this->db->from("coop_loan_atm_transaction_receive_file");
	    $this->db->where(array('file_id' => $_GET['id']));
        $arr_data['data'] = $this->db->get()->result_array();

        $this->libraries->template('loan_atm/data_receive_file', $arr_data);
    }

    public function process_file(){
	    if(isset($_POST)){
            $dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/";
	        $this->db->select(array('id','file_name'))->from("coop_loan_atm_file_upload")->where(array("status" => "1", "active_status" => "0", "id" => $_POST['id']));
	        $res = $this->db->get()->row();
	        if(empty($res->file_name)){
	            $data = (array) $res;
                header('content-type: application/json; charset: utf8');
                $result = array('result' => 0, 'msg' => 'file empty', 'status' => 'error', 'data' => $data );
                echo json_encode($result);
                exit;
            }else{
                header('content-type: application/json; charset: utf8');
                if(!file_exists($dir.$res->file_name)) {
                    $result = array('result' => 0, 'msg' => 'file not found', 'status' => 'error');
                }else{
					//ตั้งค่าข้อมูลการธนาคาร Offline
					$setting_atm_file = $this->loan_atm_file->get_setting_loan_atm_file();
					$bank_atm = $setting_atm_file['bank_atm'];
                    $this->load->model('text_file');
                    $file = $this->text_file->read_file_DCSDCOT($res->file_name,$bank_atm);
					$rs = $this->loan_atm_file->loan_atm_receive_file($res->id, $file, $res->file_name, $bank_atm);
					$result = array('result' => $rs, 'msg' => 'success');;
                }
                echo json_encode($result);
                exit;
            }
        }
    }

    public function upload(){
        if(isset($_POST)){
            $output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/";
            if($_FILES['file_attach']['name'][0]!=''){
                foreach($_FILES['file_attach']['name'] as $key => $value){
                    $new_file_name = $_FILES['file_attach']['name'][$key];

                    if($this->file_name_exists($new_file_name)) {
                        @copy($_FILES["file_attach"]["tmp_name"][$key], $output_dir . $new_file_name);

                        $data_insert = array();
                        $data_insert['file_name'] = $new_file_name;
                        $data_insert['file_path'] = $output_dir . $new_file_name;
                        $data_insert['user_id'] = $_SESSION['USER_ID'];
                        $data_insert['file_date'] = date("Y-m-d", strtotime(str_replace("/", "-", $_POST['file_date']) . " -543 year"));
                        $data_insert['active_status'] = 0;
                        $data_insert['createdatetime'] = date('Y-m-d H:i:s');
                        $data_insert['status'] = 1;

                        $this->db->insert('coop_loan_atm_file_upload', $data_insert);
                        $this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
                    }else{
                        $this->center_function->toast("ไม่สามารถบันทึกข้อมูลได้ เนื่องจากไฟล์ซ้ำหรืออัพโหลดไฟล์ย้อนหลัง");
                    }
                }
            }
        }
        //header('location: '.base_url('/loan_atm/atm_file_upload'));
    }

    public function download(){
        $this->libraries->template('loan_atm/download', array('data' => 1));
    }

    public function down_load_file(){
        ini_set("precision", 12);
		
		//ตั้งค่าข้อมูลการธนาคาร Offline
		$setting_atm_file = $this->loan_atm_file->get_setting_loan_atm_file();
		$bank_atm = $setting_atm_file['bank_atm'];		
		$arr_data = $this->loan_atm_file->get_data_load_file($bank_atm);
		
		$file_extension = $setting_atm_file['file_extension'];
		$path_file = $arr_data['path_file'];
		$text = $arr_data['text'];
		$text = iconv('UTF-8', 'TIS-620', $text);
		$file_name = $arr_data['file_name'];
        $file = fopen( $path_file, "w") or die("Unable to open file");
        fwrite($file, $text);
        fclose($file);

        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="'.$file_name.'.'.$file_extension.'"');
        readfile($path_file);
    }

    public function delete_file_update(){
	    $dir_path = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/";
	    header('content-type: application/json; charset: utf8');
	    if(isset($_POST)) {
            $res = $this->db->select(array('file_name', 'active_status', 'status'))->from("coop_loan_atm_file_upload")->where(array("id" => $_POST['id']))->get()->row();
            if($res->status == '2' || $res->active_status == '1'){
                echo json_encode(array('status' => 0, 'msg' => 'unmount file.'));
                exit;
            }else{
				if(!unlink($dir_path.$res->file_name)) {
                    echo json_encode(array('status' => 0, 'msg' => 'unmount file.'));
					exit;
                }else{
                    $this->db->where(array('id' => $_POST['id']));
                    $this->db->delete('coop_loan_atm_file_upload');
                    echo json_encode(array('status' => 1, 'msg' => 'removed file.'));
					exit;
                }
            }
        }
	    echo json_encode(array('status' => 0, 'msg' => 'error'));
	    exit;
    }

    private function file_name_exists($file_name){
	    $this->db->select('file_name')->from('coop_loan_atm_file_upload')->where("file_name = '{$file_name}'");
	    return $this->db->get()->num_rows() ? true : false;
    }

    public function receive_file_transfer_data(){
	    header('content-type: application/json; charset: utf8;');
	    if(isset($_POST)){
            $this->db->select('*');
            $this->db->from("coop_loan_atm_setting");
            $this->db->limit(1);
            $setting = $this->db->get()->row();

			//ตั้งค่าข้อมูลการธนาคาร Offline
			$setting_atm_file = $this->loan_atm_file->get_setting_loan_atm_file();
			$bank_atm = $setting_atm_file['bank_atm'];			
			$res = $this->loan_atm_file->get_transaction_receive_file($_POST['id'], $bank_atm);
	        foreach ($res as $index => $item){
				$this->loan_atm_file->atm_detail_add($item, $setting);
            }

	        $data_update['status'] = 2;
	        $data_update['submit_date'] = date('Y-m-d H:i:s');
	        $this->db->where(array('id'=> $_POST['id']));
	        $this->db->update('coop_loan_atm_file_upload', $data_update);
	        echo json_encode(array('status_code' => 200, "status" => "success", 'msg' => 'success'));
	        exit;
        }
        echo json_encode(array('status_code' => 400,  "status" => "error", 'msg' => 'error'));
        exit;
    }

    private function toString($amt){
        return str_replace('.', '', number_format($amt, 2, '.', ''));
    }

    public function ajax_upload(){
        header('content-type: application/json; charset: utf8;');
        $dir_path = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/";

	    $data = ["status" => 0 , "msg" => "no response"];

        //ตั้งค่าข้อมูลการธนาคาร Offline
		$setting_atm_file = $this->loan_atm_file->get_setting_loan_atm_file();
		$file_extension = $setting_atm_file['file_extension'];
		$file_type = $setting_atm_file['file_type'];
		$bank_atm = $setting_atm_file['bank_atm'];
		
		if($_FILES['file']['type'] !== $file_type){
            $data['msg'] = "กรุณาอัพโหลดไฟล์ที่เป็นนานสกุล ".$file_extension." เท่านั้น" ;
            $data['extension'] = $_FILES['file']['type'];
            echo json_encode($data);
            exit;
        }

	    if(!file_exists($dir_path)){
	        mkdir($dir_path, 0777);
        }

	    if($this->file_name_exists($_FILES['file']['name'])){
            $data['msg'] = "เนื่องจากวันที่ของข้อมูลซ้ำกับข้อมูลในระบบ" ;
            echo json_encode($data);
            exit;
        }

        move_uploaded_file($_FILES['file']['tmp_name'], $dir_path . $_FILES['file']['name']);

	    if($this->file_previous_exists($_FILES['file']['name'])){
	        $data['msg'] = "เนื่องจากวันที่ของข้อมูลย้อนหลังจากไฟล์ในระบบกรุณาอัพโหลดไฟล์ใหม่";
            echo json_encode($data);
            exit;
        }

        $this->load->model('text_file');
        $file_date = $this->text_file->read_date_file_DCSDCOT($_FILES['file']['name'],$bank_atm);

        $data_insert = array();
        $data_insert['file_name'] = $_FILES['file']['name'];
        $data_insert['file_path'] = $dir_path . $_FILES['file']['name'];
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        $data_insert['file_date'] = $file_date;
        $data_insert['active_status'] = 0;
        $data_insert['createdatetime'] = date('Y-m-d H:i:s');
        $data_insert['status'] = 1;

        $this->db->insert('coop_loan_atm_file_upload', $data_insert);

        $data['status'] = 1;
        $data['msg'] = "อัพโหลดไฟล์สำเร็จแล้ว";
        echo json_encode($data);
        exit;
    }

    private function file_previous_exists($file){
		//ตั้งค่าข้อมูลการธนาคาร Offline
		$setting_atm_file = $this->loan_atm_file->get_setting_loan_atm_file();
		$bank_atm = $setting_atm_file['bank_atm'];
		
        $this->load->model('text_file');
        $file_date = $this->text_file->read_date_file_DCSDCOT($file,$bank_atm);
        $date = $file_date;
        $year = date("Y", strtotime($date));
        $month = date("m", strtotime($date));
        $day = date("d", strtotime($date));
        $str = " YEAR(file_date) = '{$year}' AND MONTH(file_date) = '{$month}' AND  DAY(file_date) >= '{$day}' ";
        $res = $this->db->select("*")->from("coop_loan_atm_file_upload")->where($str)->get()->result_array();
        return !!(sizeof($res) > 0);
    }

    public function add_atm_file(){
	    echo $this->loan_atm_file->test();
    }

    public function verify_loan_atm_file(){
	    $arr_data = array();
	    $month = isset($_GET['month']) ? $_GET['month'] : date('m');
	    $year = isset($_GET['year']) ? $_GET['year'] : date('Y');

	    $where =" YEAR(`file_date`)='{$year}' AND MONTH(`file_date`)='{$month}' ";
	    $data = $this->db->select('*')->from('coop_loan_verify_bank_upload')->where($where)->get()->result_array();
	    $list = array();
	    foreach ($data as $key => $item){
            $list[date('Y-m-d', strtotime($item['file_date']))] = $item;
        }
	    $arr_data['list'] = $list;
        $this->libraries->template('loan_atm/verify_loan_atm_file', $arr_data);
    }

    public function upload_check_file(){
        header('content-type: application/json; charset: utf8;');
        $dir_path = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/withdraw/";
        $full_path = $dir_path.$_FILES['file']['name'];
        $file_name = $_FILES['file']['name'];

        $data = ["status" => 0 , "msg" => "no response"];
        if($_FILES['file']['type'] !== "text/plain"){
            $data['msg'] = "กรุณาอัพโหลดไฟล์ที่เป็นนานสกุล txt เท่านั้น" ;
            $data['extension'] = $_FILES['file']['type'];
            echo json_encode($data);
            exit;
        }

        if(!file_exists($dir_path)){
            mkdir($dir_path, 0777);
        }

        move_uploaded_file($_FILES['file']['tmp_name'], $full_path);

        if(!file_exists($full_path)){
            $data['msg'] = "ไม่พบไฟล์ ".$full_path;
            echo json_encode($data);
            exit;
        }

        if($this->loan_atm_file->check_file_exists($file_name)){			
            $file_date = $this->loan_atm_file->get_date_file_DCSBAD60($full_path);
            $data_insert = array();
            $data_insert['file_name'] = $_FILES['file']['name'];
            $data_insert['file_path'] = $dir_path . $_FILES['file']['name'];
            $data_insert['user_id'] = $_SESSION['USER_ID'];
            $data_insert['file_date'] = $file_date;
            $data_insert['active_status'] = 0;
            $data_insert['createdatetime'] = date('Y-m-d H:i:s');
            $data_insert['status'] = 0;

            $this->db->where('id', $this->loan_atm_file->get_data_verify_bank($file_name)['id']);
            $this->db->update('coop_loan_verify_bank_upload', $data_insert);

            $data['status'] = 1;
            $data['msg'] = "ปรับปรุงข้อมูลไฟล์ ".$file_name." วันที่ ".$this->center_function->ConvertToThaiDate($file_date, 2, 1);
            echo json_encode($data);
        }else{			
            $file_date = $this->loan_atm_file->get_date_file_DCSBAD60($full_path);
            $data_insert = array();
            $data_insert['file_name'] = $_FILES['file']['name'];
            $data_insert['file_path'] = $dir_path . $_FILES['file']['name'];
            $data_insert['user_id'] = $_SESSION['USER_ID'];
            $data_insert['file_date'] = $file_date;
            $data_insert['active_status'] = 0;
            $data_insert['createdatetime'] = date('Y-m-d H:i:s');
            $data_insert['status'] = 0;
            $this->db->insert('coop_loan_verify_bank_upload', $data_insert);
            $data['status'] = 1;
            $data['msg'] = "อัพโหลดไฟล์สำเร็จ" ;
            echo json_encode($data);
        }
    }

    public function check_file_update(){
       return $this->loan_atm_file->read_file_DCSBAD60('191114_coa031_loan_r1.t1335.txt');
    }

    public function get_date_file(){

	    echo "file path : ".$_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/withdraw/191114_coa031_loan_r1.t1335.txt";

	    echo "<br>";

	    echo $this->loan_atm_file->get_date_file_DCSBAD60($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/withdraw/191114_coa031_loan_r1.t1335.txt");
    }

    public function get_content(){
	    echo "<pre>";
	    $data = $this->loan_atm_file->get_data_verify_bank('191114_coa031_loan_r1.t1335.txt');
	    print_r($data);
	    echo "</pre>";
    }

    public function update_contract(){
        header('content-type: application/; charset: utf8;');
        $data = ['status' => 0, 'data' => [], 'msg' => 'error'];
        $post = $this->input->post();
	    $result = $this->loan_atm_file->update_contract_by_file_loan($post['id']);
	    if(is_string($result)){
            $data['error_msg'] = $result;
            echo json_encode($data);
            exit;
        }else if(!empty($result)){
            $data['status'] = 1;
            $data['data'] = $result;
            $data['msg'] = 'success';
            echo json_encode($data);
            exit;
        }
        echo json_encode($data);
        exit;
    }

    public function check_list(){
	    $get = $this->input->get();
	    if(isset($get['id'])) {
            $data = $this->loan_atm_file->getDataById($get['id']);
            $html = "";
            $num = 0;
            foreach ($data as $key => $rows){
                $html .= "<tr>";
                $html .= "<td class=\"text-center\">".(++$num)."</td>";
                foreach ($rows as $item){
                    $html .= "<td class=\"text-center\">".$item.'</td>';
                }
                $html .= "</tr>";
            }
            echo $html;
            exit;
        }
	    echo "";
	    exit;

    }

    //generate atm petition number
    public function atm_petition_number($accPeriod = false){

	    //find prefix name in table coop_loan_atm_setting_template
	    $atmTemp = $this->db->select('*')->from('coop_loan_atm_setting_template')->where(array(
	        'start_date <=' => date('Y-m-d H:i:s')
        ))->order_by('start_date', 'desc')->limit(1)->get()->row();

        $year = "";
	    if($accPeriod){

            $accRoundSetting = $this->db->get('coop_account_period_setting')->row();
            $month = $accRoundSetting->accm_month_ini;
            $year = self::account_round_year($month);

        }else{
	        $year = substr(date('Y') + 543, 2, 2);
        }

	    //make prefix petition of loan
        $atmPrefix = '';
        if($atmTemp->prefix_code) {
            $atmPrefix = $year.$atmTemp->prefix_code;
        }

	    //find last petition of loan
        $this->db->select(array('petition_number'));
        $this->db->from('coop_loan_atm');
        $this->db->where("petition_number LIKE '{$atmPrefix}%' ");
        $this->db->order_by('petition_number DESC');
        $this->db->limit(1);
        $row_petition_number = $this->db->get()->row_array();

        $last_petition_number = (int)mb_substr($row_petition_number['petition_number'], 4, 6, 'UTF-8');
        if(!empty($row_petition_number)){
            $petition_number = $last_petition_number+1;
            $petition_number = sprintf('%s%06d', $atmPrefix, $petition_number);
        }else{
            $petition_number = sprintf('%s%06d', $atmPrefix,1);
        }
        return $petition_number;
    }

    public function account_round_year($month)
    {
        $_year = date('Y');
        if((int)$month >= date('n') && (int)$month != 1){
            $_year =  date('Y') + 1;
        }
        return $_year;
    }
	
	public function del_verify_bank_upload(){
        header('content-type: application/; charset: utf8;');
        $data = ['status' => 0, 'data' => [], 'msg' => 'error'];
        $post = $this->input->post();
	    $result = $this->loan_atm_file->del_verify_bank_upload_file($post['id']);
	    if(is_string($result)){
            $data['error_msg'] = $result;
            echo json_encode($data);
            exit;
        }else if(!empty($result)){
            $data['status'] = 1;
            $data['data'] = $result;
            $data['msg'] = 'success';
            echo json_encode($data);
            exit;
        }
        echo json_encode($data);
        exit;
    }
	
	public function check_receive_file(){
	    $get = $this->input->get();
	    if(isset($get['id'])) {
			//ตั้งค่าข้อมูลการธนาคาร Offline
			$setting_atm_file = $this->loan_atm_file->get_setting_loan_atm_file();
			$bank_atm = $setting_atm_file['bank_atm'];
            $data = $this->loan_atm_file->getDataReceiveById($get['id'],$bank_atm);
            $html = "";
            $num = 0;
            foreach ($data as $key => $rows){
                $html .= "<tr>";
                $html .= "<td class=\"text-center\">".(++$num)."</td>";
                foreach ($rows as $key_2=>$item){
					if($key_2 == 'transaction_amount'){
						$html .= "<td class=\"text-right\">".number_format($item,2).'</td>';
					}else if($key_2 == 'transaction_date'){
						$html .= "<td class=\"text-center\">".$this->center_function->ConvertToThaiDate($item,1,1,0).'</td>';
					}else if($key_2 == 'member_id'){
						$html .= "<td class=\"text-center\">".sprintf("%06d", $item).'</td>';
					}else{
						$html .= "<td class=\"text-center \" style=\"". (empty($item) ? "color: red": "") ."\">". (empty($item) ? "ไม่สัญญา": $item) .'</td>';
					}
                }
                $html .= "</tr>";
            }
            echo $html;
            exit;
        }
	    echo "";
	    exit;

    }

    public function get_prev_loan_atm(){
	    $date = $this->input->post('create', 1);
	    $loan_atm_id = $this->input->post('loan_atm_id', 1);

	    $data = array();
	    $data['loan_atm_id']    = $loan_atm_id;
	    $data['entry_date']     = $this->center_function->ConvertToSQLDate($date);
        $data['limit'] = array('transaction_datetime < ' => $this->center_function->ConvertToSQLDate($date));
	    $interest = $this->ATMCalc->calc('PL', $data);

	    header('Content-Type: application/json; charset=utf-8');
	    echo json_encode($interest);
	    exit;
    }

    public function repair_atm(){

	    $member = self::getMemberATM();
        //ตั้งค่าข้อมูลการธนาคาร Offline
        $setting_atm_file = $this->loan_atm_file->get_setting_loan_atm_file();
        $bank_atm = $setting_atm_file['bank_atm'];

        $this->db->select('*');
        $this->db->from("coop_loan_atm_setting");
        $this->db->limit(1);
        $setting = $this->db->get()->row();


	    foreach ($member as $key => $val) {
            $option = array();
            $option['t1.member_id'] = $val['member_id'];

            $last = $this->db->select("loan_date")
                ->from("coop_loan_atm_detail")
                ->where("member_id='" . $val['member_id'] . "'")
                ->order_by("loan_date desc")
                ->limit(1)
                ->get()->row()->loan_date;

            if (empty($last)) {
                $last = $this->db->where('member_id', $val['member_id'])
                    ->get('coop_loan_atm', 1)->row()->approve_date;
            }

            $option['t1.transaction_date >'] = $last;

            $res = $this->loan_atm_file->get_transaction_receive_file_range($bank_atm, $option);
            if (sizeof($res) == 0) {
                echo $val['member_id']." error! find not found. <br>";
                continue;
            }
            foreach ($res as $index => $item) {
                $this->loan_atm_file->atm_detail_add($item, $setting);
            }
        }
    }

    private function getATMPeriod($amount, $max_period = 0){
        $this->db->select('*');
        $this->db->from("coop_loan_atm_setting");
        $this->db->limit(1);
        $setting = $this->db->get()->row();
        if($max_period == 0){
            $max_period = $setting->max_period;
        }
        $interest = $setting->interest_rate;
	    return ($amount*(($interest/100)/12))/(1 - pow(1/(1+(($interest/100)/12)), $max_period));
    }

    private function getMemberATM(){
	    return $this->db->select(array('member_id', 'contract_no'))
            ->from('coop_loan_atm_transaction_receive_file_ktb')
            ->where(array('transaction_date >' => '2021-01-05 00:00:00', 'member_id' => $_GET['member_id']))
            ->group_by('contract_no, member_id')->get()
            ->result_array();
    }

    /**
     * @rounter /loan_atm/check_fine_transfer_data
     */
    public function check_fine_transfer_data(){
        $id = $this->input->post("id", true);
        if(empty($id)){
            $res = array("status_code" => 400, "status" => "error");
        }else{
            $result = $this->loan_atm_file->checkContractByFileId($id);
            if(sizeof($result)){
                $res = array( "status_code" => 400, "status" => "miss_match" , "data" => $result);
            }else {
                $res = array( "status_code" => 200, "status" => "success" , "data" => $result);
            }
        }

        $this->output->set_content_type("application/json", "utf8")->_display();
        echo json_encode($res);
        exit;
    }

}

