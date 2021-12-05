<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_deposit_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function coop_deposit_setting(){
		$arr_data = array();		
			
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_setting');
		$this->db->order_by('deposit_setting_id DESC');
		$rs = $this->db->get()->result_array();
		$arr_data['row'] = @$rs[0];
			
		$this->libraries->template('setting_deposit_data/coop_deposit_setting',$arr_data);
	}
	
	public function coop_deposit_setting_save(){
		$data_insert = array();
		
		foreach(@$_POST as $key => $value){
			if($key  != 'deposit_setting_id'){
				$data_insert[@$key]	= @$value;
			}		
		}

		$id_edit = @$_POST["deposit_setting_id"] ;

		$table = "coop_deposit_setting";

		
		// edit
		$this->db->where('deposit_setting_id', $id_edit);
		$this->db->update($table, $data_insert);
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");	
		// edit
				
		echo"<script> document.location.href='".PROJECTPATH."/setting_deposit_data/coop_deposit_setting' </script>";            
	}

	public function coop_deposit_type_setting()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		$filter = @$_GET['filter'];
		
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_interest');
			$this->db->where("interest_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();			
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_deposit_type_setting.*');
			$this->paginater_all->main_table('coop_deposit_type_setting');
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('coop_deposit_type_setting.type_seq ASC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
			
			$row_detail = array();
			foreach($row['data'] AS $key => $val){
				//
				$this->db->select(array('*'));
				$this->db->from('coop_deposit_type_setting_detail');
				$this->db->where("type_id = '".$val['type_id']."' AND start_date <= '".date('Y-m-d')."'");
				$this->db->order_by('start_date DESC');
				$this->db->limit(1);
				$rs_detail = $this->db->get()->result_array();
				$rs_detail = @$rs_detail[0];
				$row_detail[$val['type_id']]['start_date'] = @$rs_detail['start_date'];
				$row_detail[$val['type_id']]['condition_interest'] = @$rs_detail['condition_interest'];		
				
				$this->db->select(array('*'));
				$this->db->from('coop_deposit_type_setting_interest');
				$this->db->where("type_detail_id = '".$rs_detail['type_detail_id']."' AND condition_interest = '".@$rs_detail['condition_interest']."'");
				$this->db->limit(4);
				$rs_interest_row = $this->db->get()->result_array();
				$rs_interest = @$rs_interest_row[0];
				if(@$rs_detail['condition_interest'] == 1){
				$row_detail[$val['type_id']]['percent_interest'] = @$rs_interest['percent_interest'];		
				}else{
					$row_detail[$val['type_id']]['percent_interest'] = $rs_interest_row;
				}
			}
			$arr_data['rs_detail'] = @$row_detail;
			$arr_data['text_interest'] = array('1'=>'คงที่','2'=>'ขั้นบันได ตามเดือน','3'=>'ขั้นบันได ตามจำนวนเงิน');	
		}
		
		$this->libraries->template('setting_deposit_data/coop_deposit_type_setting',$arr_data);
	}
	
	public function coop_deposit_type_setting_save()
	{
		$data_insert = array();				
		$this->db->select('MAX(type_seq) as _max');
		$this->db->from('coop_deposit_type_setting');
		$max = $this->db->get()->result_array();
		$type_seq = @$max[0]["_max"] + 1 ;
		
			
		$data_insert['type_code']     = @$_POST["type_code"];		
		$data_insert['type_name']     = @$_POST["type_name"];		
		$data_insert['type_prefix']   = @$_POST["type_prefix"];		
		$data_insert['format_account_number'] = @$_POST["format_account_number"];		
		$data_insert['unique_account'] = (@$_POST["unique_account"] !='')?@$_POST["unique_account"]:'0';	
		$data_insert['main_account'] = (@$_POST["main_account"] !='')?@$_POST["main_account"]:'0';			
		$data_insert['updatetime']    = date('Y-m-d H:i:s');
		
		$id_edit = @$_POST["type_id"] ;
			

		$table = "coop_deposit_type_setting";
		//update main_account null
		$data_update_main['main_account'] = '';
		$this->db->where('main_account', '1');
		$this->db->update($table, $data_update_main);
		
		if ($id_edit == '') {	
		// add		
			$data_insert['type_seq']       = @$type_seq;	
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('type_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		//print_r($this->db->last_query());exit;
		echo"<script> document.location.href='".PROJECTPATH."/setting_deposit_data/coop_deposit_type_setting' </script>"; 

	}	
	public function coop_interest_setting_save()
	{
		$data_insert = array();		
		$data_insert['type_id']      = @$_POST["type_id"];		
		$data_insert['interest_rate']    = @$_POST["interest_rate"];
		$data_insert['start_date']    = $this->center_function->ConvertToSQLDate(@$_POST["start_date"]);

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_interest";

		if ($type_add == 'add') {	
		// add		
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('interest_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_deposit_data/coop_deposit_type_setting' </script>"; 

	}
	
	function check_use_type(){	
		$id = @$_POST['id'];
		$this->db->select(array('*'));
		$this->db->from('coop_interest');
		$this->db->where("type_id = '{$id}'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		if(@$row['type_id']){
			echo false;
		}else{
			echo true;
		}		
		exit;
	}
	
	function del_coop_deposit_type(){	
		$table = @$_POST['table'];
		$table_sub = @$_POST['table_sub'];
		$id = @$_POST['id'];
		$field = @$_POST['field'];


		if (!empty($table_sub)) {
			$this->db->where($field, $id );
			$this->db->delete($table_sub);	
        }

		$this->db->where($field, $id );
		$this->db->delete($table);
		
		if($this->db->affected_rows()){
			if($table == 'coop_deposit_type_setting'){
				$this->db->where($field, $id );
				$this->db->delete('coop_deposit_type_setting_detail');
				if($this->db->affected_rows()){
					$this->db->where($field, $id );
					$this->db->delete('coop_deposit_type_setting_interest');
				}
			}
		}
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
		
	}

	function coop_deposit_type_setting_detail(){
		$arr_data = array();
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting');
		$this->db->where("type_id = '".$_GET['type_id']."'");
		$row = $this->db->get()->result_array();
		$arr_data['row'] = @$row[0];
		
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting_detail');
		$this->db->where("type_id = '".$_GET['type_id']."'");
		$this->db->order_by('start_date DESC');
		$rs_detail = $this->db->get()->result_array();
		foreach($rs_detail as $k => $val){
			$this->db->select(array('*'));
				$this->db->from('coop_deposit_type_setting_interest');
				$this->db->where("type_detail_id = '".$val['type_detail_id']."' AND condition_interest = '".@$val['condition_interest']."'");
				$this->db->limit(4);
				$rs_interest_row = $this->db->get()->result_array();
				$rs_interest = @$rs_interest_row[0];
				if(@$val['condition_interest'] == 1){
				$rs_detail[$k]['percent_interest'] = @$rs_interest['percent_interest'];		
				}else{
					$rs_detail[$k]['percent_interest'] = $rs_interest_row;
				}
		}
		$arr_data['text_interest'] = array('1'=>'คงที่','2'=>'ขั้นบันได ตามเดือน','3'=>'ขั้นบันได ตามจำนวนเงิน');	
		$arr_data['rs_detail'] = @$rs_detail;
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting_detail');
		$this->db->where("type_id = '".$_GET['type_id']."' AND start_date <= '".date('Y-m-d')."'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$rs_status = $this->db->get()->result_array();
		$row_status = @$rs_status[0];
		$arr_data['row_status'] = $row_status;
		//print_r($this->db->last_query()); exit;
		$this->libraries->template('setting_deposit_data/coop_deposit_type_setting_detail',$arr_data);
	}
	function coop_deposit_type_setting_detail_add(){
		$arr_data = array();
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting');
		$this->db->where("type_id = '".@$_GET['type_id']."'");
		$row = $this->db->get()->result_array();
		$arr_data['row'] = @$row[0];
		
		if(@$_GET['type_detail_id']){
			$this->db->select(array('*'));
			$this->db->from('coop_deposit_type_setting_detail');
			$this->db->where("type_detail_id = '".@$_GET['type_detail_id']."'");
			$row = $this->db->get()->result_array();
			$arr_data['row_detail'] = @$row[0];
			$arr_data['row_detail']['amount_min'] = (@$row[0]['amount_min'] == '0')?'':@$row[0]['amount_min'];
			$arr_data['row_detail']['amount_max'] = (@$row[0]['amount_max'] == '0')?'':@$row[0]['amount_max'];
			$arr_data['row_detail']['num_month_before'] = (@$row[0]['num_month_before'] == '0')?'':@$row[0]['num_month_before'];
			$arr_data['row_detail']['percent_depositor'] = (@$row[0]['percent_depositor'] == '0')?'':@$row[0]['percent_depositor'];
			
			$this->db->select(array('*'));
			$this->db->from('coop_deposit_type_setting_interest');
			$this->db->where("type_detail_id = '".@$_GET['type_detail_id']."'");
			$row = $this->db->get()->result_array();
			//echo '<pre>'; print_r($row); echo '</pre>';
			//exit;
			if(!empty($row)){
				foreach($row as $key => $value){
					//$value['percent_interest'] = (@$value['percent_interest'] == '0.00')?'':@$value['percent_interest'];
					//$value['amount_deposit'] = (@$value['amount_deposit'] == '0.00')?'':@$value['amount_deposit'];
					//$value['num_month'] = (@$value['num_month'] == '0')?'':@$value['num_month'];
					$arr_data['row_interest'][$value['condition_interest']][] = $value;
				}
			}
		}
		
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting');
		$this->db->order_by('type_seq');
		$arr_data['type_settings'] = $this->db->get()->result_array();
		
		$this->libraries->template('setting_deposit_data/coop_deposit_type_setting_detail_add',$arr_data);
	}
	
	function coop_deposit_type_setting_detail_save(){
		$data = $_POST;
		
		$start_date = (empty($data['start_date']))?'':$this->center_function->ConvertToSQLDate($data['start_date']);
		$end_date = (empty($data['end_date']))?'':$this->center_function->ConvertToSQLDate($data['end_date']);
		$pay_date1 = (empty($data['pay_date1']))?'':$this->center_function->ConvertToSQLDate($data['pay_date1']);
		$pay_date2 = (empty($data['pay_date2']))?'':$this->center_function->ConvertToSQLDate($data['pay_date2']);
		$type_detail_id = @$data['type_detail_id'];
		$type_id = @$data['type_id'];
		
		$data_insert = array();
		$data_insert['type_id'] = @$type_id;
		$data_insert['start_date'] = @$start_date;
		$data_insert['end_date'] = @$end_date;
		$data_insert['condition_interest'] = @$data['condition_interest'];
		$data_insert['sub_condition_interest'] = @$data['sub_condition_interest'];
		$data_insert['pay_interest'] = @$data['pay_interest'];		
		$data_insert['pay_date1'] = @$pay_date1;
		$data_insert['pay_date2'] = @$pay_date2;
		$data_insert['num_month_maturity'] = @$data['num_month_maturity'];
		$data_insert['type_interest'] = @$data['type_interest'];
		$data_insert['staus_interest'] = @$data['staus_interest'];
		$data_insert['num_month_no_interest'] = @$data['num_month_no_interest'];
		$data_insert['amount_min'] = @$data['amount_min'];
		$data_insert['amount_max'] = @$data['amount_max'];
		$data_insert['type_fee'] = @$data['type_fee'];
		$data_insert['percent_fee'] = @$data['percent_fee'];
		$data_insert['num_month_before'] = @$data['num_month_before'];
		$data_insert['percent_depositor'] = @$data['percent_depositor'];
		$data_insert['type_receive'] = @$data['type_receive'];
		$data_insert['max_month'] = @$data['max_month'];		
		$data_insert['staus_loan_deduct'] = @$data['staus_loan_deduct'];
		$data_insert['staus_close_principal'] = @$data['staus_close_principal'];
		$data_insert['staus_withdraw'] = @$data['staus_withdraw'];
		$data_insert['withdraw_num'] = @$data['withdraw_num'];
		$data_insert['withdraw_num_unit'] = (int)@$data['withdraw_num_unit'];
		$data_insert['withdraw_num_interest'] = @$data['withdraw_num_interest'];
		$data_insert['withdraw_percent_interest'] = @$data['withdraw_percent_interest'];
		$data_insert['withdraw_percent_min'] = (double)@$data['withdraw_percent_min'];
		$data_insert['staus_maturity'] = @$data['staus_maturity'];
		$data_insert['maturity_num_year'] = @$data['maturity_num_year'];
		$data_insert['permission_type'] = @$data['permission_type'];
		$data_insert['hold_withdraw_month'] = @$data['hold_withdraw_month'];
		$data_insert['condition_age'] = @$data['condition_age'];
		$data_insert['is_open_min'] = (int)@$data['is_open_min'];
		$data_insert['open_min'] = (double)@$data['open_min'];
		$data_insert['open_max'] = (double)@$data['open_max'];
		$data_insert['is_balance_min'] = (int)@$data['is_balance_min'];
		$data_insert['balance_min'] = (double)@$data['balance_min'];
		$data_insert['is_withdraw_min'] = (int)@$data['is_withdraw_min'];
		$data_insert['withdraw_min'] = (double)@$data['withdraw_min'];
		$data_insert['is_tax'] = (int)@$data['is_tax'];
		$data_insert['tax_rate'] = (double)@$data['tax_rate'];
		$data_insert['is_withdrawal_specify'] = (int)@$data['is_withdrawal_specify'];
		$data_insert['amount_max_time'] = (double)@$data['amount_max_time'];
		$data_insert['is_day_cal_interest'] = (int)@$data['is_day_cal_interest'];
		$data_insert['is_deposit_num'] = (int)@$data['is_deposit_num'];
		$data_insert['deposit_num_type'] = @$data['deposit_num_type'];
		$data_insert['deposit_num'] = (double)@$data['deposit_num'];
		$data_insert['amount_min_no_interest'] = (double)@$data['amount_min_no_interest'];
		$data_insert['is_balance_not_min_open'] = (int)@$data['is_balance_not_min_open'];
		//เช็คแสดงข้อมูลมีค่าทำเนียมการถอนกอ่นวันที่
		if($data['type_fee'] != 4){ 
			$data_insert['withdraw_date_before'] = "0000-00-00 00:00:00";
			$data_insert['ref_type_id'] = "0";
		}elseif($data['type_fee'] == 4){
			$data_insert['withdraw_date_before'] = (empty($data['withdraw_date_before']))?'':$this->center_function->ConvertToSQLDate($data['withdraw_date_before']);
			$data_insert['ref_type_id'] = (int)@$data['ref_type_id'];;
		}

		$data_insert['deduction_of_year'] = @$data['deduction_of_year'];
		$data_insert['days_in_year'] = (int)@$data['days_in_year'];
		$data_insert['is_transfer_in_account_coop'] = (int)@$data['is_transfer_in_account_coop'];
		$pay_date3 = (empty($data['pay_date3']))?'':$this->center_function->ConvertToSQLDate($data['pay_date3']);//วันที่จ่ายดอกเบี้ย ของการจ่ายดอกเบี้ย 1 ครั้ง ต่อปี
		$data_insert['pay_date3'] = @$pay_date3;
		$data_insert['is_transfer_ktb_app'] = (int)@$data['is_transfer_ktb_app'];
		//เช็คแสดงข้อมูลมีเมื่อครบกำหนดฝากโอนเข้าบัญชี....และปิดบัญชีทันที
		if($data['condition_age'] != 4){
			$data_insert['account_transfer_close_type_id'] = "0";
		}elseif($data['condition_age'] == 4){
			$data_insert['account_transfer_close_type_id'] = (int)@$data['account_transfer_close_type_id'];
		}

		$data_insert['updatetime'] = date('Y-m-d H:i:s');
		if(@$data['type_detail_id'] == ''){
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert('coop_deposit_type_setting_detail', $data_insert);			
			$type_detail_id = $this->db->insert_id();
		}else{
			
			$this->db->where('type_detail_id', $type_detail_id);
			$this->db->update('coop_deposit_type_setting_detail', $data_insert);
			
			$this->db->where('type_detail_id', $type_detail_id);
			$this->db->delete('coop_deposit_type_setting_interest');
		}
		//อัตราดอกเบี้ย คงที่
		foreach(@$data['stable'] as $key => $value){
			$data_insert = array();
			$data_insert['type_detail_id'] = @$type_detail_id;
			$data_insert['type_id'] = @$type_id;
			$data_insert['condition_interest'] = '1';
			$data_insert['num_month'] = @$value['num_month'];
			$data_insert['percent_interest'] = @$value['percent_interest'];
			$data_insert['amount_deposit'] = @$value['amount_deposit'];			
			$this->db->insert('coop_deposit_type_setting_interest', $data_insert);
		}
		//อัตราดอกเบี้ย ขั้นบันได ตามเดือน
		foreach(@$data['staircase_month'] as $key => $value){
			$data_insert = array();
			$data_insert['type_detail_id'] = @$type_detail_id;
			$data_insert['type_id'] = @$type_id;
			$data_insert['condition_interest'] = '2';
			$data_insert['num_month'] = @$value['num_month'];
			$data_insert['percent_interest'] = @$value['percent_interest'];
			$data_insert['amount_deposit'] = @$value['amount_deposit'];
			
			$this->db->insert('coop_deposit_type_setting_interest', $data_insert);
		}
		//อัตราดอกเบี้ย ขั้นบันได ตามจำนวนเงิน
		foreach(@$data['staircase_money'] as $key => $value){
			$data_insert = array();
			$data_insert['type_detail_id'] = @$type_detail_id;
			$data_insert['type_id'] = @$type_id;
			$data_insert['condition_interest'] = '3';
			$data_insert['num_month'] = @$value['num_month'];
			$data_insert['percent_interest'] = @$value['percent_interest'];
			$data_insert['amount_deposit'] = @$value['amount_deposit'];
			
			$this->db->insert('coop_deposit_type_setting_interest', $data_insert);
		}

		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/setting_deposit_data/coop_deposit_type_setting_detail?type_id=".@$type_id."' </script>"; 
	}

	function coop_deposit_type_setting_detail_delete(){
		$this->db->where('type_detail_id', $_GET['type_detail_id']);
		$this->db->delete('coop_deposit_type_setting_detail');	
		
		$this->db->where('type_detail_id', $_GET['type_detail_id']);
		$this->db->delete('coop_deposit_type_setting_interest');	
		
		$this->center_function->toast("ลบข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/setting_deposit_data/coop_deposit_type_setting_detail?type_id=".$_GET['type_id']."' </script>"; 
	}
	
	function check_main_account(){	
		$type_id=$this->input->post('type_id');
		
		$row = $this->db->select(array('type_id','main_account'))->from('coop_deposit_type_setting')
				->where("main_account = '1' AND type_id!='{$type_id}'")->get()->row_array();
		if($row['main_account'] == '1'){
			echo true;
		}else{
			echo false;
		}
		exit;
	}
}
