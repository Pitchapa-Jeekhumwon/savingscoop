<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cal_deposit extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model("Deposit_modal", "deposit_modal");
	}
	
	function test_accu_int_item() {
		$data['account_id'] ='1203183';
		$data['date_cal'] ='2020-08-18';
		$result = $this->deposit_modal->cal_accu_int($data);
		echo '<pre>'; print_r($result); echo '</pre>';
		exit;
	}
	
	function accu_int_item_view() {		
		//$account_id = '1203183';
		$arr_data = array();
		if(!empty($_POST)){			
			$data['account_id'] = @$_POST['account_id'];
			$data['date_cal'] = $this->center_function->ConvertToSQLDate(@$_POST['date_cal']);
			$arr_data['data'] = $this->deposit_modal->cal_accu_int($data);
			$arr_data['account_id'] = @$_POST['account_id'];
		}
		
		$this->libraries->template('save_money/accu_int_item_view',$arr_data);
	}
	
	//รัน อัพเดตดอกเบี้ย สะสม
	function update_accu_int() {
		/*
		//@start เรียกใช้ ข้อมูล ดอกเบี้ยสะสม
		$data_cal = array();
		$data_cal['account_id'] = '2003226';
		//$data_cal['date_cal'] = '2021-02-24';
		$data_cal['date_cal'] = '2020-12-07';
		$data_cal['date_start_cal'] = '2020-12-07';
		$data_cal_accu_int = $this->deposit_modal->cal_accu_int($data_cal);		
		//@end เรียกใช้ ข้อมูล ดอกเบี้ยสะสม
		echo '<pre>'; print_r($data_cal_accu_int); echo '<pre>';
		*/
		//old_acc_int	ดอกเบี้ยสะสมรวม
		//accu_int_item	ดอกเบี้ยคำนวน ณ วันทำรายการ

		//$type_account_id = '10'; //เงินฝากออมทรัพย์
		$type_account_id = '1200690'; //เงินฝากออมทรัพย์
		
		//$type_account_id = '11'; //เงินฝากออมทรัพย์ระหว่างสหกรณ์
		
		
		//$type_account_id = '12'; //เงินฝากออมทรัพย์พิเศษ(ATM)
		//$type_account_id = '20'; //เงินฝากออมทรัพย์พิเศษ
		//$type_account_id = '25'; //เงินฝากออมทรัพย์พิเศษสำหรับผู้เกษียณอายุ	
		//$type_account_id = '26'; //เงินฝากออมทรัพย์พิเศษเพื่อการศึกษาบุตร
		//transaction_time > '2020-12-04' 
		$sql = "SELECT
				account_id,
				DATE(transaction_time) AS transaction_time
			FROM
				coop_account_transaction 
			WHERE
				
				transaction_time > '2020-02-02' 
				AND account_id LIKE '{$type_account_id}%'
			GROUP BY
				account_id 
			ORDER BY
				transaction_time ASC";
				
		//AND account_id = '2003226'		
		//AND account_id = '2003016'		
        $row = $this->db->query($sql)->result_array();
		if(!empty($row)){
			foreach($row AS $key=>$val){
				$sql2 = "	SELECT
								account_id,
								transaction_time,
								transaction_id,
								old_acc_int,
								accu_int_item
							FROM
								coop_account_transaction 
							WHERE
								transaction_time > '2020-02-02' 
								AND account_id = '{$val['account_id']}'
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
						//$data_cal['transaction_id'] = $val2['transaction_id'];
						$data_cal_accu_int = $this->deposit_modal->cal_accu_int($data_cal);		
						//@end เรียกใช้ ข้อมูล ดอกเบี้ยสะสม
						//echo '<pre>'; print_r($data_cal_accu_int); echo '<pre>';						
						$transaction_id = $val2['transaction_id'];
						if($key2 == 0){
							$old_acc_int = $data_cal_accu_int['old_acc_int'];
						}else{
							$old_acc_int += $data_cal_accu_int['accu_int_item'];
						}
						$old_acc_int = number_format($old_acc_int, 2, '.', '');
						//echo 'interest='.$data_cal_accu_int['interest'].'<br>';
						//echo 'accu_int_item='.$data_cal_accu_int['accu_int_item'].'<br>';
						//echo 'old_acc_int='.$old_acc_int.'<br>';
						//echo 'date_start='.$data_cal_accu_int['date_start'].'<br>';
						//echo 'date_end='.$data_cal_accu_int['date_end'].'<br>';
						//echo '<hr>';
						echo "<pre>";
						print_r($old_acc_int);
						echo "<pre>";
						print_r($data_cal_accu_int['accu_int_item']);
						echo "<pre>";
						print_r($transaction_id);
						exit;
						$sql_update = "UPDATE coop_account_transaction SET old_acc_int = '{$old_acc_int}', accu_int_item = '{$data_cal_accu_int['accu_int_item']}'
						WHERE transaction_id = '{$transaction_id}';";
						echo $sql_update.'<br>';
					}
				}	
			}
		}
		//echo '<pre>'; print_r($row); echo '<pre>';
		exit;
	}


	function update_accu_inter() {
		$data=$this->input->post();
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
	public function cal_acc_interest_month()
	{
		$arr_data = array();
		$arr_data["month_arr"] = $this->month_arr;
		$this->load->model('Report_deposit_model');
		$arr_data['type_ids'] = $this->Report_deposit_model->get_coop_deposit_type_setting(array('type_id','type_name','type_code'));
		$arr_data['row_mem_apply_type'] = $this->Report_deposit_model->get_coop_mem_apply_type(array('apply_type_id', 'apply_type_name', 'age_limit'));
		$this->libraries->template('report_deposit_data/insert_coop_deposit_acc_interest_month_view',$arr_data);
	}
		public function check_n_insert_coop_deposit_acc_interest_month()
	{

		if($_GET['date']){
			$date = $_GET['date'];
		}
		if($_GET['data']){
			$get = $_GET['data'];
			$acc_type = $get['type_id'];
			$mem_type = $get['apply_type_id'];
			$account_id = $get['acc_id'];
			$date = date('Y-m-t',strtotime(($get['year']-543).'-'.sprintf("%02d",@$get['month']).'-01'));
		}else{
			$acc_type=0;
			$mem_type=0;
		}
		if($date != ''||$date){
			$date_now  = $date;
			$date_last_month  = date('Y-m-t',strtotime($date));
		}else{
			$date_now = date('Y-m-d');
			$date_last_month = date('Y-m-t');
		}
		$date_plus_1_date =  date('Y-m-d',strtotime($date_last_month."+1 days"));
		if ($date_now == $date_last_month) {
			$aff_row_all=0;
			$this->load->model('Report_deposit_model');
			$this->load->model("Deposit_modal", "deposit_modal");
			if($account_id==''||!$account_id){
				$acc_id_rw = $this->Report_deposit_model->get_account_opened_on_time($date_last_month,$mem_type,$acc_type,'account_id');
				foreach ($acc_id_rw as $key => $val) {
					$data_cal = array();
					$data_cal['account_id'] = @$val['account_id'];
					$data_cal['date_cal'] = $date_plus_1_date;
					$data_cal_accu_int = $this->deposit_modal->cal_accu_int($data_cal);
					$insert_data = [
						"account_id" => @$val['account_id'],
						"create_date" => $date_last_month,
						"acc_interest" => $data_cal_accu_int['old_acc_int']
					];
					$aff_row = $this->Report_deposit_model->insert_coop_deposit_acc_interest_month($insert_data);
					$aff_row_all+=$aff_row;
				}
			}else{
					$data_cal = array();
					$data_cal['account_id'] = $account_id;
					$data_cal['date_cal'] = $date_plus_1_date;
					$data_cal_accu_int = $this->deposit_modal->cal_accu_int($data_cal);
					if(!$data_cal_accu_int){
					}else{
						$insert_data = [
							"account_id" => $account_id,
							"create_date" => $date_last_month,
							"acc_interest" => $data_cal_accu_int['old_acc_int']
						];
						$aff_row = $this->Report_deposit_model->insert_coop_deposit_acc_interest_month($insert_data);
						$aff_row_all+=$aff_row;
					}
			}
		$result = [
			'input date'=>$date_now,
			'date_last_month'=>$date_last_month,
			'affected'=>$aff_row_all,
			'result'=>'success'
		];
			echo json_encode($result);
		}else{
			$result = [
				'input date'=>$date_now,
				'date_last_month'=>$date_last_month,
				'result'=>'date is not last date of month'
			];
			echo json_encode($result);
		}
	}
}
