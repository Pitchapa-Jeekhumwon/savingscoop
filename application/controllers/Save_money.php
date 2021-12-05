<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Save_money extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model("Deposit_modal", "deposit_modal");
		$this->load->model("Address_model", "address_model");
	}
	public function index()
	{
		$arr_data = array();
		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_deposit_type_setting as t2';
		$join_arr[$x]['condition'] = 't1.type_id = t2.type_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array('t1.*','t2.type_code'));
		$this->paginater_all->main_table('coop_maco_account as t1');
		$this->paginater_all->where("");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('t1.account_status ASC,t1.created DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], @$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		$this->db->select(array('*'));
		$this->db->from('coop_money_type');
		$this->db->where("money_type = 'DT'");
		$this->db->order_by("id DESC");
		$row_money_type = $this->db->get()->result_array();
		$arr_data['row_money_type'] = $row_money_type; 
		$this->db->select(array('*'));
		$this->db->from('coop_money_type');
		$this->db->where("money_type = 'WT'");
		$this->db->order_by("id DESC");
		$row_money_type_wt = $this->db->get()->result_array();
		$arr_data['row_money_type_wt'] = $row_money_type_wt;

		//ข้อมูลบัญชีธนาคาร
		$get_coop_bank = $this->deposit_modal->get_coop_bank();	
		$arr_data["bank_info"] = $get_coop_bank;
		
		$this->libraries->template('save_money/index',$arr_data);
	}
	//แสดงรายการยัญชีคู่โอนตามประเภทบัญชี
	function dual_list_transfer(){
		$datas = $this->input->post();
		$today = date('Y-m-d');
		$this->db->select('condition_age,account_transfer_close_type_id');
		$this->db->from('coop_deposit_type_setting_detail');
		$this->db->where("type_id = '".$datas['type_id']."' AND start_date <= '".$today."'");
		$this->db->order_by("start_date DESC, type_detail_id DESC");
		$this->db->limit(1);
		$typ_id_transfer = $this->db->get()->result_array();
			if($typ_id_transfer){
				foreach ($typ_id_transfer as $key => $value_typ_id_transfer) {
					$account_transfer_close_type_id=$value_typ_id_transfer['account_transfer_close_type_id'];
					$condition_age=$value_typ_id_transfer['condition_age'];
				}
			}

			if (!empty($datas['type_id'] && $datas['mem_id'])) {
					$this->db->select(array("CONCAT('1|', '', '|', '', '|', account_id) AS id", "CONCAT(account_id, '  ',account_name) AS text"));
				if($condition_age != '4'){
					$maco_account = $this->db->get_where("coop_maco_account", array("mem_id" =>  $datas['mem_id'],"account_status" => "0"))->result_array();
				}else{
					$maco_account = $this->db->get_where("coop_maco_account", array("mem_id" =>  $datas['mem_id'],"account_status" => "0","type_id" =>  $account_transfer_close_type_id))->result_array();
			}

				echo "<option value=''>เลือกบัญชีคู่โอน</option>";
				if(!empty($maco_account)){
					foreach(@$maco_account as $key => $row){
						echo "<option value=".$row['id'].">".$row['text']."</option>";
					}
				}
			
		}
		
	} 
	
	function add_save_money(){
		$arr_data = array();
		$data = $this->input->post();
		if($data['account_id']!=''){
			$account_id = @$data['account_id'] ;
			$arr_data['account_id'] = $account_id;
			
			$this->db->select(array('t1.*','t2.type_name', "CONCAT(transfer_type, '|', dividend_bank_id, '|', dividend_bank_branch_id, '|', dividend_acc_num) AS id_transfer, t3.user_name",'t2.type_code'));
			$this->db->from('coop_maco_account as t1');
			$this->db->join('coop_deposit_type_setting as t2','t1.type_id = t2.type_id','inner');
			$this->db->join('coop_user as t3','t3.user_id = t1.sequester_by','left');
			$this->db->where("t1.account_id = '".$account_id."'");
			$row = $this->db->get()->result_array();
			$arr_data['auto_account_id'] = '';
			$btitle = "แก้ไขบัญชีเงินฝาก";
			$arr_data['row'] = $row[0];
		}else{
			/*$this->db->select('account_id');
			$this->db->from('coop_maco_account');
			$this->db->order_by("account_id DESC");
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			if(!empty($row)){
				$auto_account_id = $row[0]['account_id'] + 1;
			}else{
				$auto_account_id = 1;
			}
			$arr_data['auto_account_id'] = $auto_account_id;*/
			$btitle = "เพิ่มบัญชีเงินฝาก";
			$arr_data['row'] = array();
			$arr_data['account_id'] = '';
		}
		$this->db->select(array('t1.type_id','t1.type_code','t1.type_name','t1.unique_account'));
		$this->db->from('coop_deposit_type_setting as t1');
		$row = $this->db->get()->result_array();
		$arr_data['type_id'] = $row;

		$arr_data['account_list_transfer'] = array();
		if($data['member_id']!=""){
			//หาtype_idจากการตั้งค่าเงินฝากว่าจะโอนเข้าบัญชีประเภทไหน
			$today = date('Y-m-d');
			$this->db->select('condition_age,account_transfer_close_type_id');
			$this->db->from('coop_deposit_type_setting_detail');
			$this->db->where("type_id = '".$data['type_id']."' AND start_date <= '".$today."'");
			$this->db->order_by("start_date DESC, type_detail_id DESC");
			$this->db->limit(1);
			$typ_id_transfer = $this->db->get()->result_array();
			if($typ_id_transfer){
				foreach ($typ_id_transfer as $key => $value_typ_id_transfer) {
					$account_transfer_close_type_id=$value_typ_id_transfer['account_transfer_close_type_id'];
					$condition_age=$value_typ_id_transfer['condition_age'];
				}
			}

			$this->db->select(array("CONCAT('1|', '', '|', '', '|', account_id) AS id", "CONCAT(account_id, '  ',account_name) AS text"));
			if($condition_age != '4'){
				$maco_account = $this->db->get_where("coop_maco_account", array("mem_id" =>  $data['member_id'],"account_status" => "0"))->result_array();
			}else {
				$maco_account = $this->db->get_where("coop_maco_account", array("mem_id" =>  $data['member_id'],"account_status" => "0","type_id" =>  $account_transfer_close_type_id))->result_array();
			}
			
			$this->db->select(array("CONCAT('2|', dividend_bank_id, '|', dividend_bank_branch_id, '|', dividend_acc_num) AS id", "CONCAT('|', dividend_acc_num, '  ', coop_bank.bank_name, ' ', coop_bank_branch.branch_name) AS text"));
			$this->db->join("coop_bank", "coop_bank.bank_id = coop_mem_bank_account.dividend_bank_id");
			$this->db->join("coop_bank_branch", "coop_mem_bank_account.dividend_bank_branch_id = coop_bank_branch.branch_code AND coop_mem_bank_account.dividend_bank_id = coop_bank_branch.bank_id", "LEFT");
			$data_mem_bank_account = $this->db->get_where("coop_mem_bank_account", array("member_id" => $data['member_id']) )->result_array();

			
			if($maco_account){
				foreach ($maco_account as $key => $value) {
					array_push($arr_data['account_list_transfer'], $value);
				}
			}
	
			if($data_mem_bank_account){
				foreach ($data_mem_bank_account as $key => $value) {
					array_push($arr_data['account_list_transfer'], $value);
				}
			}
			
			//เช็คเงินฝากประเภทหลัก
			$main_type_code= $this->db->select(array('type_code'))
						->from('coop_deposit_type_setting')
						->where("main_account = '1'")->get()->row_array()['type_code'];

			$chk_setting_atm = $this->db->select(array('*'))
				->from('coop_setting_atm_online')->where("atm_status = '1'")->limit(1)	
				->get()->row_array();
			if(!empty($chk_setting_atm)){
				$atm_online_status = true;
			}else{
				$atm_online_status = false;
			}
		}
		// echo"<pre>";print_r($condition_age);
		// echo"<pre>";print_r($account_transfer_close_type_id);exit;
		
		$arr_data['btitle'] = $btitle;
		$arr_data['main_type_code'] = $main_type_code;
		$arr_data['atm_online_status'] = $atm_online_status;
		//แสดงตัวเลือกรายการโอน
		$this->db->select(array('*'));
		$this->db->from('coop_money_type');
		$this->db->where("money_type = 'DT'");
		$this->db->order_by("id DESC");
		$row_money_type = $this->db->get()->result_array();
		foreach($row_money_type as $key_type =>$value_type){
			$cut_word=array();
			$money_type_name_th = str_replace('ฝากโดยการ',"",$value_type['money_type_name_th']);
			$cut_word['id'] = str_replace('ฝาก',"",$money_type_name_th);
			$cut_word['money_type_name_th'] = str_replace('ฝาก',"",$money_type_name_th);
			$cut_word['money_type_name_short'] = $value_type['money_type_name_short'];
			$row_money_type_get[]=$cut_word;
		}
		$arr_data['row_money_type'] = $row_money_type_get;

		//ข้อมูลบัญชีธนาคาร
		$get_coop_bank = $this->deposit_modal->get_coop_bank();	
		$arr_data["bank_info"] = $get_coop_bank;

		$this->load->view('save_money/add_save_money',$arr_data);
	}
	
	function save_add_save_money(){
		//echo"<pre>";print_r($this->input->post());echo"</pre>";exit;
		$data = $this->input->post();
		$sequester_amount = (@$data['sequester_status'] == 2)?str_replace(',','',@$data['sequester_amount']):'';
		if(@$data['sequester_status']=='1'){
			$sequester_status_atm = '1';
		}else{
			$sequester_status_atm = @$data['sequester_status_atm'];
		}
		
		if($data['action_type']=='add'){
			$account_id = '';
			$this->db->select('type_code,type_prefix');
			$this->db->from('coop_deposit_type_setting');
			$this->db->where("type_id = '".$data['type_id']."'");
			$row = $this->db->get()->result_array();


			$type_code = @$row[0]['type_code'];
			$type_prefix = @$row[0]['type_prefix'];


			$this->db->select('deposit_setting_id');
			$this->db->from('coop_deposit_setting');
			$this->db->where("deposit_setting_id = '".$data['deposit_setting_id']."'");
			$row = $this->db->get()->result_array();
			$min_first_deposit = $row[0]['min_first_deposit'];


			$this->db->select('account_id');
			$this->db->from('coop_maco_account');
			$this->db->where("type_id = '".$data['type_id']."'");
			$this->db->order_by("account_id DESC");
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			//echo $this->db->last_query(); echo '<br>';
			$digit_run_account = $this->center_function->digit_run_account();

			//echo $digit_run_account; exit;
			if(!empty($row)) {
				$c_id = 1;
				$old_account_id = substr($row[0]["account_id"], (int)$digit_run_account*(-1));
				$old_account_id = (int)$old_account_id;
				$account_id = sprintf("%0".$digit_run_account."d", $old_account_id + ($c_id++));
				$account_id = $type_code.$account_id;

				while(true){
					$this->db->select('account_id');
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$account_id."'");
					$this->db->limit(1);
					$row_account = $this->db->get()->result_array();
					if($row_account){
                        $old_account_id = substr($row[0]["account_id"], (int)$digit_run_account*(-1));
						$old_account_id = (int)$old_account_id;
						$account_id = sprintf("%0".$digit_run_account."d", $old_account_id + ($c_id++));
						$account_id = $type_code.$account_id;
					}else{
						break;
					}

				}

			}else {
				$account_id = $type_code.sprintf("%0".$digit_run_account."d", 1);
			}

			//echo $account_id;exit;
			$data_insert = array();
			//$data_insert['account_id'] = $data['acc_id'];
			$data_insert['account_id'] = ($data['acc_id_yourself'] != '') ? $data['acc_id_yourself'] : $account_id;
			$data_insert['mem_id'] = $data['mem_id'];
			$data_insert['member_name'] = $data['member_name'];
			$data_insert['account_name'] = $data['acc_name'];
			
			$data_insert['account_amount'] = '0';
			$data_insert['book_number'] = '1';
			$data_insert['type_id'] = $data['type_id'];
			$data_insert['atm_number'] = $data['atm_number'];
			$data_insert['account_status'] = '0';
			$data_insert['account_name_eng'] = $data['account_name_eng'];
			$data_insert['barcode'] = $data['barcode'];
			$data_insert['sequester_status'] = @$data['sequester_status'];
			$data_insert['sequester_amount'] = @$sequester_amount;
			$data_insert['sequester_status_atm'] = @$sequester_status_atm;
			//$data_insert['min_first_deposit'] = @$min_first_deposit;
			$tmp_opn_date = explode('/', $data['opn_date']);
			$data_insert['created'] = ($tmp_opn_date[2]-543)."-".$tmp_opn_date[1]."-".$tmp_opn_date[0]." ".date('H:i:s');
			if(@$data['account_id_atm'] != ''){
				$data_insert['account_id_atm'] = @$data['account_id_atm'];
				$data_insert['account_id_atm_update'] = date("Y-m-d H:i:s");
			}
			if($data['account_transfer']!=''){
				$tmp_account_transfer = explode("|", $data['account_transfer']);
				$data_insert['transfer_type'] = $tmp_account_transfer[0];
				$data_insert['dividend_bank_id'] = $tmp_account_transfer[1];
				$data_insert['dividend_bank_branch_id'] = $tmp_account_transfer[2];
				$data_insert['dividend_acc_num'] = $tmp_account_transfer[3];
			}
			// echo '<pre>'; print_r($data_insert); echo '</pre>';
			$this->db->insert('coop_maco_account', $data_insert);

			$min_first_deposit = implode('', explode(',', $data['min_first_deposit']));
			$data_insert_transaction['transaction_time'] 			= $data_insert['created'];
			$data_insert_transaction['transaction_list'] 			= "OPN";
			$data_insert_transaction['transaction_withdrawal'] 		= 0;
			$data_insert_transaction['transaction_deposit'] 		= $min_first_deposit;
			$data_insert_transaction['transaction_balance'] 		= $min_first_deposit;
			$data_insert_transaction['user_id'] 					= $_SESSION['USER_ID'];
			$data_insert_transaction['transaction_no_in_balance'] 	= $min_first_deposit;
			$data_insert_transaction['account_id'] 	= $data_insert['account_id'];

			$data_insert_transaction['pay_type'] = @$data['pay_type_tmp'];
			if(@$data['pay_type_tmp'] == '1'){
				//โอนเงิน			
				$data_insert_transaction['transfer_bank_id'] = @$data['transfer_bank_id'];
				$data_insert_transaction['transfer_bank_branch_id'] = @$data['transfer_bank_branch_id'];
				$data_insert_transaction['transfer_bank_branch_name'] = @$data['transfer_bank_branch_name'];
				$data_insert_transaction['transfer_acc_num'] = @$data['transfer_acc_num'];
			}
			// echo '<pre>'; print_r($data_insert_transaction); echo '</pre>';
			$this->db->insert("coop_account_transaction", $data_insert_transaction);

			$transaction_id = $this->db->insert_id();
			$data_insert_transaction_transfer= array();
			$data_insert_transaction_transfer['transaction_time'] = $data_insert['created'];
			$data_insert_transaction_transfer['transaction_list'] = $data['save_account_type_transfer'];
			$data_insert_transaction_transfer['transaction_withdrawal'] = 0;
			$data_insert_transaction_transfer['transaction_deposit'] = $min_first_deposit;
			$data_insert_transaction_transfer['transaction_balance'] = $min_first_deposit;
			$data_insert_transaction_transfer['account_id'] =$data_insert['account_id'];
			$data_insert_transaction_transfer['pay_type'] = $data['pay_type_tmp'];
			$data_insert_transaction_transfer['transaction_note'] = $data['save_account_note'];
			$data_insert_transaction_transfer['transaction_id'] = $transaction_id;
			// echo '<pre>'; print_r($data_insert_transaction_transfer); echo '</pre>';
			$this->db->insert("coop_account_transaction_transfer", $data_insert_transaction_transfer);

		}else{
			$data_insert = array();
			$data_insert['account_name'] = $data['acc_name'];
			$data_insert['account_name_eng'] = $data['account_name_eng'];
			$data_insert['type_id'] = $data['type_id'];
			$data_insert['atm_number'] = $data['atm_number'];
			$data_insert['sequester_status'] = @$data['sequester_status'];
			if($data['remark']!=""){
				$data_insert['sequester_by'] = @$_SESSION['USER_ID'];
				$data_insert['sequester_remark'] = @$data['remark'];
				$data_insert['sequester_time'] = date("Y-m-d H:i:s");
			}
			$data_insert['sequester_amount'] = @$sequester_amount;
			$data_insert['sequester_status_atm'] = @$sequester_status_atm;
			$tmp_opn_date = explode('/', $data['opn_date']);
			$data_insert['created'] = ($tmp_opn_date[2]-543)."-".$tmp_opn_date[1]."-".$tmp_opn_date[0]." ".date('H:i:s');
			$data_insert['updated']	= date("Y-m-d H:i:s");
			$data_insert['updated_by']	= @$_SESSION['USER_ID'];
			if($data['account_transfer']!=''){
				$tmp_account_transfer = explode("|", $data['account_transfer']);
				$data_insert['transfer_type'] = $tmp_account_transfer[0];
				$data_insert['dividend_bank_id'] = $tmp_account_transfer[1];
				$data_insert['dividend_bank_branch_id'] = $tmp_account_transfer[2];
				$data_insert['dividend_acc_num'] = $tmp_account_transfer[3];
			}

			$data['acc_id'] = implode("", explode("-",  $data['acc_id']) );
			
			//เช็คเลขบัญชีกรุงไทย
			$this->db->select('account_id_atm');
			$this->db->from('coop_maco_account');
			$this->db->where("account_id = '".$data['acc_id']."'");
			$this->db->limit(1);
			$chk_account_id_atm = $this->db->get()->row_array();

			if(@$chk_account_id_atm['account_id_atm'] != ''){
				$data_insert['account_id_atm'] = @$data['account_id_atm'];
				$data_insert['account_id_atm_update'] = date("Y-m-d H:i:s");
			}else{
				if(@$data['account_id_atm'] != ''){
					$data_insert['account_id_atm'] = @$data['account_id_atm'];
					$data_insert['account_id_atm_update'] = date("Y-m-d H:i:s");
				}
			}
			
			if($data['acc_id'] != $data['old_account_no']){
				//---ค้นหาข้อมูลบัญชีเก่า
				$old_account = $this->db->get_where("coop_maco_account", array(
					"account_id" => $data['old_account_no']
				))->result_array()[0];
				//---เพิ่มเลขบัญชีใหม่
				$old_account['account_name'] = $data['acc_name'];
				$old_account['account_name_eng'] = $data['account_name_eng'];
				$old_account['account_id'] = $data['acc_id'];
				$old_account['created'] = $old_account['created'];
				$old_account['updated'] = date("Y-m-d H:i:s");
				$this->db->insert("coop_maco_account", $old_account);
				//---อัพเดทข้อมูล coop_account_transaction
				$data_update_transaction['account_id'] = $old_account['account_id'];
				$this->db->where("account_id", $data['old_account_no']);
				$this->db->update("coop_account_transaction", $data_update_transaction);
				//---ลบข้อมูล row ใน coop_maco_account
				$this->db->where("account_id", $data['old_account_no']);
				$this->db->delete("coop_maco_account");

			}else{
				
				$this->db->where('account_id', $data['acc_id']);
				$this->db->update('coop_maco_account', $data_insert);
			}
			
		}
		//coop_account_atm_log
		
		$account_id = (@$account_id == '')?$data['acc_id']:$account_id;
		$this->db->select('account_id,account_id_atm');
		$this->db->from('coop_account_atm_log');
		$this->db->where("account_id = '".$account_id."'");
		$this->db->order_by('account_id_atm_update DESC');
		$this->db->limit(1);
		$row_account_id_atm = $this->db->get()->row_array();
		
		//สถานะการผูกบัญชีกรุงไทย ATM
		if(@$data['account_atm_status'] != ''){
			$arr_a_atm_status = array();
			if(@$data['account_atm_status'] == 'N'){
				$arr_a_atm_status = array('D','A');
			}else{
				$arr_a_atm_status = array(@$data['account_atm_status']);
			}
			
			if(!empty($arr_a_atm_status)){
				foreach($arr_a_atm_status AS $key_a_atm_status =>$val_a_atm_status){
					$data_insert = array();
					$data_insert['account_id'] = @$account_id;
					$data_insert['account_id_atm'] = @$data['account_id_atm'];
					$data_insert['account_id_atm_update'] = date("Y-m-d H:i:s");
					$data_insert['admin_id'] = @$_SESSION['USER_ID'];
					$data_insert['account_atm_status'] = @$val_a_atm_status;
					$this->db->insert('coop_account_atm_log', $data_insert);
				}
			}			
		}

		$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
		if(@$data['redirectback']!=''){
			echo "<script> document.location.href = '".PROJECTPATH."/save_money".$data['redirectback'].$data['acc_id']."' </script>";
		}else{
			echo "<script> document.location.href = '".PROJECTPATH."/save_money' </script>";
		}
		exit;
	}
	
	function check_account_save(){
		$data = array();
		$data['error'] = '';
		$text_error = '';
		$today = date("Y-m-d");
		
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting');
		$this->db->join("coop_deposit_type_setting_detail","coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id","left");
		$this->db->where("coop_deposit_type_setting.type_id = '{$_POST['type_id']}' AND coop_deposit_type_setting_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_deposit_type_setting_detail.start_date DESC');
		$this->db->limit(1);
		$row_setting = $this->db->get()->row_array();
		
		if($_POST['atm_number']!=''){
			$this->db->select(array('t1.atm_number'));
			$this->db->from('coop_atm_card as t1');
			$this->db->where("atm_number = '".$_POST['atm_number']."' AND member_id != '".$_POST['member_id']."' AND atm_card_status = '0'");
			$row = $this->db->get()->result_array();
			//echo $this->db->last_query();
			if(empty($row)){
				$data['atm_number'] = 'success';
			}else{
				$data['atm_number'] = 'dupplicate';
			}
		}else{
			$data['atm_number'] = 'success';
		}
		
		if($_POST['unique_account']=='1'){
			$this->db->select('*');
			$this->db->from('coop_maco_account');
			$this->db->where("
				type_id = '".$_POST['type_id']."' 
				AND account_id != '".$_POST['account_id']."'
				AND mem_id = '".$_POST['member_id']."'
			");
			$row = $this->db->get()->result_array();

			if(empty($row)){
				$data['unique_account'] = 'success';
			}else{
				$data['unique_account'] = 'dupplicate';
			}

			$account_status = 1;
			foreach($row AS $key=>$val){
				if($val['account_status'] == '0'){
					$account_status = 0;
				}
			}
			$data['account_status'] = $account_status;
		}

		if($_POST['account_id']!=''){
			$this->db->select('*');
			$this->db->from('coop_maco_account');
			$this->db->where("account_id = '".$_POST['account_id']."'");
			$row = $this->db->get()->result_array();

			if(empty($row)){
				$data['acc_number'] = 'success';
			}else{
				$data['acc_number'] = 'dupplicate_account_no';
			}

			$account_status = 1;
			foreach($row AS $key=>$val){
				if($val['account_status'] == '0'){
					$account_status = 0;
				}
			}
			$data['account_status'] = $account_status;
		}
		
		if($_POST["min_first_deposit"] < $row_setting["open_min"] && $row_setting["is_open_min"] && $_POST["action_type"] != "edit") {
			$text_error .= "- ไม่สามารถเปิดบัญชีได้เนื่องจากจำนวนเงินของท่านต่ำกว่าวงเงินที่กำหนด\n";
		}

		if($_POST["min_first_deposit"] > $row_setting["open_max"] && $row_setting["open_max"] != 0 && $row_setting["is_open_min"] && $_POST["action_type"] != "edit") {
			$text_error .= "- ไม่สามารถเปิดบัญชีได้เนื่องจากจำนวนเงินของท่านสูงกว่าวงเงินที่กำหนด\n";
		}

		$data['error'] = $text_error;
		
		echo json_encode($data);
	}
	
	function check_account_delete(){
		$data = $this->input->post();
		$this->db->select('*');
		$this->db->from('coop_maco_account');
		$this->db->where("account_id = '".$data['account_id']."'");
		$row = $this->db->get()->result_array();
		
		if($row[0]['account_amount'] > 0 ){
			echo 'error';
		}else{
			echo'success';
		}
		exit;
	}
	
	function delete_account($account_id){
		$this->db->where('account_id', $account_id);
		$this->db->delete('coop_maco_account');
		$this->center_function->toast('ลบข้อมูลเรียบร้อยแล้ว');
		echo "<script> document.location.href = '".PROJECTPATH."/save_money' </script>";
	}
	
	function close_account(){
		// echo"<pre>";print_r($_POST);exit;
		$data=$this->input->post();
		$arr_date_close = explode('/',@$_POST['date_close_tmp']);
		$date_close = ($arr_date_close[2]-543)."-".$arr_date_close[1]."-".$arr_date_close[0];
		$transaction_time = (@$_POST['date_close_tmp'])?$date_close : date('Y-m-d H:i:s');
		$account_id = $_POST['account_id'];
		$close_account_principal = str_replace(',','',$_POST['close_account_principal']);
		$close_account_interest = str_replace(',','',$_POST['close_account_interest']);
		$close_account_interest_return = str_replace(',','',$_POST['close_account_interest_return']);
		$cal_interest_close_account = $this->deposit_libraries->cal_interest_close_account($account_id,$_POST["interest_rate"],$transaction_time);
		// echo"<pre>";print_r($cal_interest_close_account);exit;
		$interest=$cal_interest_close_account['interest'];
		$transaction_deposit_balance=$cal_interest_close_account['transaction_deposit'];

		$this->db->select(array('transaction_balance','transaction_no_in_balance'));
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$row_transaction = $row[0];
		$transaction_balance = $row_transaction['transaction_balance'];
		$transaction_no_in_balance = $row_transaction['transaction_no_in_balance'];
		if($_POST['cal_type_id']==34){
			if(!empty($interest)){
				foreach ($interest as $key4 =>$value3){
					$transaction_balance += $value3;
					$sum_int[$key4] = $row_transaction['transaction_deposit']+$value3; //จำนวนเงินฝากแต่ละเดือน+ดอกเบี้ยแต่ละรายการ
					$data_insert = array();

					$data_insert["transaction_time"] = $transaction_time;
					$data_insert["transaction_list"] = "INT";
					$data_insert["transaction_withdrawal"] = '0';
					$data_insert["transaction_deposit"] = $value3;
					$data_insert["transaction_balance"] = $transaction_balance;	
					// $data_insert['transaction_no_in_balance'] = $transaction_no_in_balance;		
					$data_insert["user_id"] = $_SESSION['USER_ID'];
					$data_insert["account_id"] =  $account_id;		
					$data_inserts[] = $data_insert;

					$transaction_balance -= $value3;
					$data_insert["transaction_time"] = $transaction_time;
					$data_insert["transaction_list"] = "WCI";		
					$data_insert["transaction_withdrawal"] = $value3;
					$data_insert["transaction_deposit"] = '0';
					$data_insert["transaction_balance"] = $transaction_balance;
					// $data_insert['transaction_no_in_balance'] = $transaction_no_in_balance;
					$data_insert["user_id"] = $_SESSION['USER_ID'];
					$data_insert["account_id"] =  $account_id;
					$data_inserts[] = $data_insert;
	
					$transaction_balance -= $transaction_deposit_balance[$key4];
					$data_insert["transaction_time"] = $transaction_time;
					$data_insert["transaction_list"] = "CMP";
					$data_insert["transaction_withdrawal"] = $transaction_deposit_balance[$key4];
					$data_insert["transaction_deposit"] = "0";
					$data_insert["transaction_balance"] = $transaction_balance;
					// $data_insert['transaction_no_in_balance'] = $transaction_no_in_balance;
					$data_insert["user_id"] = $_SESSION['USER_ID'];
					$data_insert["account_id"] =  $account_id;
					$data_inserts[] = $data_insert;
				}
			}
			$this->db->insert_batch('coop_account_transaction', $data_inserts);
		}else{			
			if($_POST['pay_type'] == '0'){
				$transaction_list = 'CW';
			}else{
				$transaction_list = 'XW';
			}
		// $this->deposit_libraries->cal_deposit_interest_by_account($account_id,'real');
		/*if($row_transaction['transaction_balance'] > $close_account_principal){
			$return_to_coop = $row_transaction['transaction_balance'] - $close_account_principal;
			$transaction_balance = $transaction_balance - $return_to_coop;
			$data_insert = array();
			$data_insert['transaction_time'] = date('Y-m-d H:i:s');
			$data_insert['transaction_list'] = 'RE/COOP';
			$data_insert['transaction_withdrawal'] = $return_to_coop;
			$data_insert['transaction_deposit'] = '0';
			$data_insert['transaction_balance'] = $transaction_balance;
			$data_insert['transaction_no_in_balance'] = $transaction_no_in_balance;
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['account_id'] = $account_id;
			$this->db->insert('coop_account_transaction', $data_insert);
		}*/

		if($close_account_interest != 0){
			$transaction_balance = $transaction_balance+$close_account_interest;
			$data_insert_in = array();
			$data_insert_in['transaction_time'] = @$transaction_time;
			$data_insert_in['transaction_list'] = 'IN';
			$data_insert_in['transaction_withdrawal'] = '0';
			$data_insert_in['transaction_deposit'] = $close_account_interest;
			$data_insert_in['transaction_balance'] = $transaction_balance;
			$data_insert_in['transaction_no_in_balance'] = $transaction_no_in_balance;
			$data_insert_in['user_id'] = $_SESSION['USER_ID'];
			$data_insert_in['account_id'] = $account_id;
			$data_insert['pay_type'] = @$data['pay_type'];
			$this->db->insert('coop_account_transaction', $data_insert_in);
		}
		
		if($close_account_interest_return != 0){
			$transaction_balance = $transaction_balance-$close_account_interest_return;
			$data_insert_in = array();
			$data_insert_in['transaction_time'] = @$transaction_time;
			$data_insert_in['transaction_list'] = 'R/IN';
			$data_insert_in['transaction_withdrawal'] = $close_account_interest_return;
			$data_insert_in['transaction_deposit'] = '0';
			$data_insert_in['transaction_balance'] = $transaction_balance;
			$data_insert_in['transaction_no_in_balance'] = $transaction_no_in_balance;
			$data_insert_in['user_id'] = $_SESSION['USER_ID'];
			$data_insert_in['account_id'] = $account_id;
			$data_insert['pay_type'] = @$data['pay_type'];
			$this->db->insert('coop_account_transaction', $data_insert_in);
		}
	
		if($transaction_balance != 0){
			$data_insert = array();
			$data_insert['transaction_time'] = @$transaction_time;
			$data_insert['transaction_list'] = $transaction_list;
			$data_insert['transaction_withdrawal'] = $transaction_balance;
			$data_insert['transaction_deposit'] = '0';
			$data_insert['transaction_balance'] = '0';
			$data_insert['transaction_no_in_balance'] = '0';
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['account_id'] = $account_id;
			$data_insert['pay_type'] = @$data['pay_type'];

			if(@$data['pay_type'] == '1'){
				//โอนเงิน			
				$data_insert['transfer_bank_id'] = @$data['transfer_bank_id'];
				$data_insert['transfer_bank_branch_id'] = @$data['transfer_bank_branch_id'];
				$data_insert['transfer_bank_branch_name'] = @$data['transfer_bank_branch_name'];
				$data_insert['transfer_acc_num'] = @$data['transfer_acc_num'];
			}
			$this->db->insert('coop_account_transaction', $data_insert);
		}
		
		//บันทึกการรับเงิน แบบโอนเเข้าบัญชีสหกรณ์
		$chk_account_coop = $this->deposit_libraries->save_transfer_in_account_coop($account_id,$transaction_time,$transaction_balance);
	}
	$this->db->select(array('transaction_id'));
	$this->db->from('coop_account_transaction');
	$this->db->where("account_id = '".$data['account_id']."'");
	$this->db->order_by('transaction_time DESC, transaction_id DESC');
	$this->db->limit(1);
	$rows = $this->db->get()->result_array()[0];
		// if ($data['pay_type']==1){		//กรณ๊ปิดบัญชีรับเงินแบบโอนออกหลายรายการ
		// 		if (!empty( $data['pay_type_transfer_wd'] && $data['withdraw_list'] )){		
		// 			$pay_type_transfer=$data['pay_type_transfer_wd'];
		// 			$withdraw_list=$data['withdraw_list'];			
		// 			$balance_list=$close_account_principal+$close_account_interest+$close_account_interest_return;
		// 			foreach($pay_type_transfer as $key =>$value){
		// 				$insert_wd_list[$key] = str_replace(',','',$withdraw_list[$key]);
		// 				$insert_pay_type_transfer[$key] = $value;				
		// 			};
		// 			foreach($insert_wd_list as $key2 =>$value2){
		// 				$balance_list -= $value2;
		// 				$data_insert_transaction_transfer= array();
		// 				$data_insert_transaction_transfer['transaction_time'] = $transaction_time;
		// 				$data_insert_transaction_transfer['transaction_list'] =$insert_pay_type_transfer[$key2];
		// 				$data_insert_transaction_transfer['transaction_withdrawal'] = $value2;
		// 				$data_insert_transaction_transfer['transaction_deposit'] = '';
		// 				$data_insert_transaction_transfer['transaction_balance'] = round($balance_list,2);
		// 				$data_insert_transaction_transfer['account_id'] = $data['account_id'];
		// 				$data_insert_transaction_transfer['pay_type'] = $data['pay_type'];
		// 				$data_insert_transaction_transfer['transaction_note'] = '';	
		// 				$data_insert_transaction_transfer['transaction_id'] = $rows['transaction_id'];
		// 				$insert_transaction_transfesr_wd[]=$data_insert_transaction_transfer;			
		// 			}
		// 			$this->db->insert_batch('coop_account_transaction_transfer', $insert_transaction_transfesr_wd);
		// 		}else{
		// 			$this->center_function->toastDanger("กรุณาตรวจสอบรายการแยกการโอนให้ถูกต้อง ");
		// 		}		
		// }
		$data_insert = array();
		$data_insert['account_amount'] = '0';
		$data_insert['account_status'] = '1';
		$data_insert['sequester_status'] = '1';
		$data_insert['sequester_status_atm'] = '1';
		$data_insert['close_account_date'] = @$transaction_time;
		$data_insert['close_account_pay_type'] = $_POST['pay_type'];
		$this->db->where('account_id', $account_id);
		$this->db->update('coop_maco_account',$data_insert);

		$this->db->where("account_id", $account_id);
		$account_guarantee = $this->db->get("coop_account_guarantee_book_saving")->result_array();
		if($account_guarantee && $transaction_balance!=0){
			$this->db->set("status", "1");
			$this->db->set("update_datetime", date("Y-m-d h:i:s"));
			$this->db->set("update_by", $_SESSION['USER_ID']);
			$this->db->where("account_id", $account_id);
			$this->db->update("coop_account_guarantee_book_saving");

			$member_id = $this->db->get_where("coop_maco_account", array(
				"account_id" => $account_id
			))->result_array()[0]['mem_id'];

			$this->db->where("unique_account", 1);
			$this->db->limit("1");
			$type_id = $this->db->get("coop_deposit_type_setting")->result_array()[0]['type_id'];

			$this->db->where("type_id", $type_id);
			$this->db->where("mem_id", $member_id);
			$this->db->where("account_status = 0");
			$this->db->limit(1);
			$transfer_to_account_id = $this->db->get("coop_maco_account")->result_array()[0]['account_id'];

			$this->db->select(array('transaction_balance','transaction_no_in_balance'));
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '".$transfer_to_account_id."'");
			$this->db->order_by('transaction_time DESC, transaction_id DESC');
			$this->db->limit(1);
			$row_transfer = $this->db->get()->result_array()[0];
			
			$data_insert = array();
			$data_insert['transaction_time'] = @$transaction_time;
			$data_insert['transaction_list'] = "XD";
			$data_insert['transaction_withdrawal'] = "0";
			$data_insert['transaction_deposit'] = $transaction_balance;
			$data_insert['transaction_balance'] = ($row_transfer['transaction_balance'] + $transaction_balance);
			$data_insert['transaction_no_in_balance'] = '0';
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['account_id'] = $transfer_to_account_id;
			$data_insert['pay_type'] = @$data['pay_type'];

			if(@$data['pay_type'] == '1'){
				//โอนเงิน			
				$data_insert['transfer_bank_id'] = @$data['transfer_bank_id'];
				$data_insert['transfer_bank_branch_id'] = @$data['transfer_bank_branch_id'];
				$data_insert['transfer_bank_branch_name'] = @$data['transfer_bank_branch_name'];
				$data_insert['transfer_acc_num'] = @$data['transfer_acc_num'];
			}

			$this->db->insert('coop_account_transaction', $data_insert);
		}


	
		
		$this->center_function->toast('ทำรายการปิดบัญชีเรียบร้อยแล้ว');
		echo "<script> document.location.href = '".PROJECTPATH."/save_money' </script>";
	}
	

	public function account_detail()
	{
		$arr_data = array();

		//Set page num if empty
		if (empty($_GET["page"])) $_GET["page"] = 1;
		
		$account_id = $this->input->get('account_id');
		$arr_data['account_id'] = $account_id;
		
		$saving_slip_setting = 0;
		$setting_deposit = $this->db->select("value")->from("coop_setting_deposit")->where("name = 'saving_slip' AND status = 1")->order_by("created_at DESC")->get()->row_array();
		if(!empty($setting_deposit)) {
			$saving_slip_setting = $setting_deposit['value'];
		}
		$arr_data['saving_slip_setting'] = $saving_slip_setting;

		$this->db->select(array('min_first_deposit','month_conclude'));
		$this->db->from('coop_deposit_setting');
		$this->db->order_by('deposit_setting_id DESC');
		$row = $this->db->get()->result_array();
		
		$arr_data['min_first_deposit'] = $row[0]['min_first_deposit'];
		$arr_data['month_conclude'] = $row[0]['month_conclude'];
		
		$this->db->select(array('t1.*','t3.type_id','t3.type_name','t3.deduct_guarantee_id'));
		$this->db->from('coop_maco_account as t1');
		$this->db->join('coop_deposit_type_setting as t3','t1.type_id = t3.type_id','left');
		$this->db->where("account_id = '".$account_id."'");
		$row = $this->db->get()->result_array();
		$arr_data['row_memberall'] = @$row[0];
		
		$this->db->select('*');
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '".$arr_data['row_memberall']['mem_id']."'");
		$row = $this->db->get()->result_array();
		$arr_data['row_member'] = @$row[0];
		
		$this->db->select(array('transaction_balance'));
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$this->db->order_by("transaction_time DESC,transaction_id DESC");
		$row = $this->db->get()->result_array();
		$arr_data['last_transaction'] = @$row[0];
		
		$show_conclude_checkbox = '0';
		if(@$arr_data['row_memberall']['last_time_print']!=''){
			$diff_last_print = date('Y-m-d',strtotime('- '.$arr_data['month_conclude'].' month'));
			$last_print_date = explode(" ",$arr_data['row_memberall']['last_time_print']);
			$last_print_date = $last_print_date[0];
			$arr_data['last_print_date'] = $last_print_date;
			if($arr_data['row_memberall']['last_time_print'] < $diff_last_print){
				$show_conclude_checkbox = '1';
			}
		}
		$arr_data['show_conclude_checkbox'] = @$show_conclude_checkbox;

		//Count amount of transaction
		$this->db->select('transaction_id');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$transactionNum = count($this->db->get()->result_array());
		
		$maxPage = $transactionNum%26 > 0 ? floor(($transactionNum/26)) + 1 : $transactionNum/26;

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_account_transaction.user_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('coop_account_transaction.*, coop_user.user_name');
		$this->paginater_all->main_table('coop_account_transaction');
		$this->paginater_all->where("account_id = '".$account_id."'");

		//Set First Page is last page
		$this->paginater_all->page_now($maxPage - @$_GET["page"] + 1);
		$this->paginater_all->per_page(26);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('transaction_time ASC, transaction_id ASC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating(intval($_GET["page"]), $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		$this->db->select('*');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$row = $this->db->get()->result_array();
		$num_arr = array();
		$i = 1;
		foreach($row as $key => $value){
			$num_arr[$value['transaction_id']] = $i++;
		}
		
		$arr_data['num_arr'] = $num_arr;
		
		$this->db->select('money_type_name_short');
		$this->db->from('coop_money_type');
		$this->db->where("id='1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_deposit'] = $row[0];
		
		$this->db->select('money_type_name_short');
		$this->db->from('coop_money_type');
		$this->db->where("id='2'");
		$row = $this->db->get()->result_array();
		$arr_data['row_with'] = $row[0];
		
		$this->db->select('user_permission_id');
		$this->db->from('coop_user_permission');
		$this->db->where("user_id = '".$_SESSION['USER_ID']."' AND menu_id = '187'");
		$row = $this->db->get()->result_array();
		if($row[0]['user_permission_id']==''){
			$arr_data['cancel_transaction_display'] = "display:none;";
		}else{
			$arr_data['cancel_transaction_display'] = "";
		}
		
		$this->db->select(array('type_fee','pay_interest','num_month_before','percent_depositor','permission_type', 'staus_close_principal'));
		$this->db->from('coop_deposit_type_setting_detail');
		$this->db->where("type_id = '".$arr_data['row_memberall']['type_id']."'");
		$this->db->order_by("type_detail_id DESC");
		$this->db->limit(1);
		$row_setting_detail = $this->db->get()->result_array();
		$row_setting_detail = $row_setting_detail[0];
		$arr_data['type_fee'] = $row_setting_detail['type_fee'];
		$arr_data['permission_type'] = $row_setting_detail['permission_type'];
		$arr_data['staus_close_principal'] = $row_setting_detail['staus_close_principal'];
		if($row_setting_detail['type_fee'] == '3'){
			if($row_setting_detail['pay_interest'] == '2'){ //ประเภทเงินฝากที่คิดดอกเบี้ย ตามวันที่ฝาก
				$arr_data['fix_withdrawal_amount'] = 0;
				$this->db->select(array('deposit_interest_balance'));
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$account_id."' AND interest_period = '".$row_setting_detail['num_month_before']."' AND fixed_deposit_status = '0'");
				$row = $this->db->get()->result_array();
				if(!empty($row)){
					foreach($row as $key => $value){
						$arr_data['fix_withdrawal_amount'] += $value['deposit_interest_balance'];
						$arr_data['fix_withdrawal_status'] = 'success';
					}
				}else{
					$this->db->select(array('deposit_balance','transaction_time'));
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$account_id."' AND fixed_deposit_status = '0'");
					$row2 = $this->db->get()->result_array();
					if(!empty($row2)){
						foreach($row2 as $key2 => $value2){
							$interest_rate = $row_setting_detail['percent_depositor'];
							$date_start = date('Y-m-d',strtotime($value2['transaction_time']));
							$date_end = date('Y-m-d');
							$diff = @date_diff(date_create($date_start),date_create($date_end));
							$date_count = @$diff->format("%a");
							$date_count = $date_count+1;
							
							$interest = ((($value2['deposit_balance']*@$interest_rate)/100)*$date_count)/365;
							
							$arr_data['fix_withdrawal_amount'] += ($value2['deposit_balance']+$interest); 
						}
						$arr_data['fix_withdrawal_status'] = 'fail';
					}
				}
			}else{
				$create_date = date('Y-m-d',strtotime($arr_data['row_memberall']['created']));
				$end_date = date('Y-m-d',strtotime('+ '.$row_setting_detail['num_month_before'].' month',strtotime($create_date)));
				$date_interest = date('Y-m-d');
				if($date_interest < $end_date){
					$this->db->select(array('transaction_balance','transaction_no_in_balance'));
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$account_id."'");
					$this->db->order_by('transaction_time DESC, transaction_id DESC');
					$this->db->limit(1);
					$row_transaction = $this->db->get()->result_array();
					
					$interest_rate = $row_setting_detail['percent_depositor'];
					$date_start = $create_date;
					$date_end = $date_interest;
					$diff = @date_diff(date_create($date_start),date_create($date_end));
					$date_count = @$diff->format("%a");
					$date_count = $date_count+1;
					
					$interest = ((($row_transaction[0]['transaction_no_in_balance']*@$interest_rate)/100)*$date_count)/365;
					
					$arr_data['fix_withdrawal_amount'] = ($row_transaction[0]['transaction_no_in_balance']+$interest); 
					$arr_data['fix_withdrawal_status'] = 'fail';
				}
			}
		}
		$this->db->select(array('*'));
		$this->db->from('coop_money_type');
		$this->db->where("money_type = 'DT'");
		$this->db->order_by("id DESC");
		$row_money_type = $this->db->get()->result_array();
		$arr_data['row_money_type'] = $row_money_type; 
		$this->db->select(array('*'));
		$this->db->from('coop_money_type');
		$this->db->where("money_type = 'WT'");
		$this->db->order_by("id DESC");
		$row_money_type_wt = $this->db->get()->result_array();
		$arr_data['row_money_type_wt'] = $row_money_type_wt;
		
		//permission
		$this->db->select('user_permission_id');
		$this->db->from('coop_user_permission');
		$this->db->where("user_id = '".$_SESSION['USER_ID']."' AND menu_id = '371'");
		$row = $this->db->get()->result_array();
		if($row[0]['user_permission_id']==''){
			$arr_data['permission']['edit_transaction'] = false;
		}else{
			$arr_data['permission']['edit_transaction'] = true;
		}

		//ข้อมูลบัญชีธนาคาร
		$get_coop_bank = $this->deposit_modal->get_coop_bank();	
		$arr_data["bank_info"] = $get_coop_bank;

		// echo"<pre>";	print_r($arr_data);exit;
		$this->libraries->template('save_money/account_detail',$arr_data);
	}
	
	public function save_transaction(){
	    //echo"<pre>";print_r($this->input->post());echo"</pre>";exit;
		$data = $this->input->post();
		if($data['date_transaction']!="")
			$date_transaction = (explode("/", $data['date_transaction'])[2]-543)."-".(explode("/", $data['date_transaction'])[1])."-".(explode("/", $data['date_transaction'])[0]);
		else
			$date_transaction = date('Y-m-d');
		
		$time_transaction = ($data['time_transaction'] != '')?$data['time_transaction']:date('H:i:s');
		
		//@start เรียกใช้ ข้อมูล ดอกเบี้ยสะสม
		$data_cal = array();
		$data_cal['account_id'] = @$data['account_id'];
		$data_cal['date_cal'] = $date_transaction;
		$data_cal_accu_int = $this->deposit_modal->cal_accu_int($data_cal);		
		//@end เรียกใช้ ข้อมูล ดอกเบี้ยสะสม

		$this->db->select('*');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$data['account_id']."'");
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
		$data['money'] = str_replace(',','',$data['money']);
		$data['total_amount'] = str_replace(',','',$data['total_amount']);
		$data['commission_fee'] = str_replace(',','',$data['commission_fee']);
		
		$this->db->select(array('t1.type_id','t1.mem_id AS member_id'));
		$this->db->from('coop_maco_account as t1');
		$this->db->where("t1.account_id = '".$data['account_id']."'");
		$row_account = $this->db->get()->result_array();
		$row_account = $row_account[0];
		
		$this->db->select("*");
		$this->db->from('coop_deposit_type_setting_detail');
		$this->db->where("type_id = '".$row_account['type_id']."' AND start_date <= '".$date_transaction."'");
		$this->db->order_by("start_date DESC, type_detail_id DESC");
		$this->db->limit(1);
		$row_setting_detail = $this->db->get()->result_array();
		$row_setting_detail = $row_setting_detail[0];
		
		if($data["do"] == "deposit") {
			$sum = $balance + $data['money'];
			$sum_no_in = $balance_no_in + $data['money'];
			if($data['pay_type']=='0'){
				$transaction_list = $data['have_a_book_acc'];
			}else if($data['pay_type']=='1') {
                $transaction_list = $data['transaction_list'];
            }else if($data['pay_type'] =='3'){
			    $transaction_list = 'IN';
			}else{
				$transaction_list = 'YPF';
			}
			$data_insert = array();
			$data_insert['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
			$data_insert['transaction_list'] = $transaction_list;
			$data_insert['transaction_withdrawal'] = '';
			$data_insert['transaction_deposit'] = $data['money'];
			$data_insert['transaction_balance'] = $sum;
			$data_insert['transaction_no_in_balance'] = $sum_no_in;
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['account_id'] = $data['account_id'];
			$data_insert['permission_by_user'] = @$data['custom_by_user_id'];
			$data_insert['createtime'] = date("Y-m-d H:i:s");
			if(isset($data['cheque_number'])) {
                $data_insert['cheque_no'] = $data['cheque_number'];
            }
			if($row_setting_detail['type_fee']=='3'){
				$data_insert['deposit_balance'] = $data['money'];
				$data_insert['fixed_deposit_status'] = '0';
				$data_insert['fixed_deposit_type'] = 'principal';
				$data_insert['date_end_saving'] = date('Y-m-d',strtotime('+ 24 month'));
				$data_insert['day_cal_interest'] = date('d');
			}
			$data_insert['accu_int_item'] = @$data_cal_accu_int['accu_int_item'];
			$data_insert['old_acc_int'] = @$data_cal_accu_int['old_acc_int'];

			$data_insert['pay_type'] = @$data['pay_type']; //การรับเงิน
			if(@$data['pay_type'] == '1'){
				//โอนเงิน			
				$data_insert['transfer_bank_id'] = @$data['transfer_bank_id'];
				$data_insert['transfer_bank_branch_id'] = @$data['transfer_bank_branch_id'];
				$data_insert['transfer_bank_branch_name'] = @$data['transfer_bank_branch_name'];
				$data_insert['transfer_acc_num'] = @$data['transfer_acc_num'];
			}	

            $statement_status = 'debit';   // สถานะการจ่ายเงิน debit = เงินเข้าจากเคาน์เตอร์, credit  = เงินออกจากเคาน์เตอร์,
            $permission_id = $this->permission_model->permission_url($_SERVER['HTTP_REFERER]'],$_SERVER['REQUEST_URI']);
            $this->tranction_financial_drawer->arrange_data_coop_financial_drawer($data_insert,$data['pay_type'],$permission_id,$statement_status,$_SERVER['REQUEST_URI']);

			
			
            if ($this->db->insert('coop_account_transaction', $data_insert)) {
				if ($data['pay_type']==1){	
				//บันทึกลงใน coop_account_transaction_transfer
				$transaction_id = $this->db->insert_id();
				$data_insert_transaction_transfer= array();
				$data_insert_transaction_transfer['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
				$data_insert_transaction_transfer['transaction_list'] = $transaction_list;
				$data_insert_transaction_transfer['transaction_withdrawal'] = '';
				$data_insert_transaction_transfer['transaction_deposit'] = $data['money'];
				$data_insert_transaction_transfer['transaction_balance'] = $sum;
				$data_insert_transaction_transfer['account_id'] = $data['account_id'];
				$data_insert_transaction_transfer['pay_type'] = $data['pay_type'];
					if(isset($data['transaction_note'])) {
						$data_insert_transaction_transfer['transaction_note'] = $data['transaction_note'];
					}
				$data_insert_transaction_transfer['transaction_id'] = $transaction_id;
				$this->db->insert('coop_account_transaction_transfer', $data_insert_transaction_transfer);
				}
				$this->center_function->toast("ทำการฝากเงินเรียบร้อยแล้ว");
				//if($data_insert['permission_by_user']!=""){
					$this->update_st->update_balance_statement(array(
					    'date' => $data_insert['transaction_time'],
                        'account_id' => $data_insert['account_id']
                    ));
				//}
				
				$this->db->select('account_chart_id');
				$this->db->from('coop_account_match');
				$this->db->where("match_id = '".$row_account['type_id']."' AND match_type = 'save_transaction'");
				$this->db->limit(1);
				//echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";
				$row = $this->db->get()->result_array();

                $data_process = array();
                // echo"<pre>";print_r($this->input->post());echo"</pre>";
                $process = 'save_money';
                $money = $data['money'];
                $ref = $_POST['account_id'];
                $match_type = 'save_transaction';
                $match_id = $row_account['type_id'];
                if($data['pay_type']=='0') {
                    $statement = 'credit';
                }else{
                    $statement = 'debit';
                }

                $data_process[] =   $this->account_transaction->set_data_account_trancetion_detail($match_id,$statement,$match_type,$ref,$money,$process);

                $process = 'save_money';
                $money = $data['money'];
                $ref = $_POST['account_id'];
                $match_type = 'main';
                $match_id = '1';
                if($data['pay_type']=='0') {
                    $statement = 'debit';
                }else{
                    $statement = 'credit';
                }

                $data_process[] =   $this->account_transaction->set_data_account_trancetion_detail($match_id,$statement,$match_type,$ref,$money,$process);
                $this->account_transaction->add_account_trancetion_detail($data_process);


			}
			
			
			//@start บันทึกรายการฝั่งเงินฝาก ATM
			//operate_code  022 = ถอน , 023 = ฝาก
			$data_atm_detail = array();
			$data_atm_detail['account_id'] = @$data['account_id'];
			$data_atm_detail['member_id'] = @$row_account['member_id'];
			$data_atm_detail['transaction_date'] = $data_insert['transaction_time'];
			$data_atm_detail['transaction_amount'] = $data_insert['transaction_deposit'];
			$data_atm_detail['operate_code'] = '023'; // ฝาก
			$this->atm_deposit->insert_deposit_atm_detail($data_atm_detail);
			//@end บันทึกรายการฝั่งเงินฝาก ATM
		
			echo "<script> window.location.href = '".base_url(PROJECTPATH.'/save_money/account_detail?account_id='.$data['account_id'])."'</script>"; 
			exit();

		} else if($data["do"] == "withdrawal") {
			if($data['pay_type']=='0'){
				$transaction_list = 'CW';
			}else{
				$transaction_list = 'XW';
			}
			
			if($data['fix_withdrawal_status']!=''){
				if($data['fix_withdrawal_status'] == 'success'){
					$sum = $balance - $data['money'];
					$data_insert = array();
					$data_insert['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
					$data_insert['transaction_list'] = $transaction_list;
					$data_insert['transaction_withdrawal'] = $data['money'];
					$data_insert['transaction_deposit'] = '';
					$data_insert['transaction_balance'] = $sum;
					$data_insert['transaction_no_in_balance'] = $sum;
					$data_insert['user_id'] = $_SESSION['USER_ID'];
					$data_insert['account_id'] = $data['account_id'];
					
					if(@$data['pay_type'] == '1'){
						//โอนเงิน			
						$data_insert['transfer_bank_id'] = @$data['transfer_bank_id'];
						$data_insert['transfer_bank_branch_id'] = @$data['transfer_bank_branch_id'];
						$data_insert['transfer_bank_branch_name'] = @$data['transfer_bank_branch_name'];
						$data_insert['transfer_acc_num'] = @$data['transfer_acc_num'];
					}
					
					$this->db->insert('coop_account_transaction', $data_insert);
					
					//@start บันทึกรายการฝั่งเงินฝาก ATM
					//operate_code  022 = ถอน , 023 = ฝาก
					$data_atm_detail = array();
					$data_atm_detail['account_id'] = @$data['account_id'];
					$data_atm_detail['member_id'] = @$row_account['member_id'];
					$data_atm_detail['transaction_date'] = $data_insert['transaction_time'];
					$data_atm_detail['transaction_amount'] = $data_insert['transaction_withdrawal'];
					$data_atm_detail['operate_code'] = '022'; // ถอน
					$this->atm_deposit->insert_deposit_atm_detail($data_atm_detail);
					//@end บันทึกรายการฝั่งเงินฝาก ATM
					
					$this->db->select(array('ref_transaction_id'));
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$data['account_id']."' AND interest_period = '".$row_setting_detail['num_month_before']."' AND fixed_deposit_status = '0'");
					$row = $this->db->get()->result_array();
					foreach($row as $key => $value){
						$data_insert = array();
						$data_insert['fixed_deposit_status'] = '1';
						$data_insert['deposit_balance'] = '0';
						$this->db->where("transaction_id",$value['ref_transaction_id']);
						$this->db->update('coop_account_transaction',$data_insert);
						
						$data_insert = array();
						$data_insert['fixed_deposit_status'] = '1';
						$this->db->where("ref_transaction_id",$value['ref_transaction_id']);
						$this->db->update('coop_account_transaction',$data_insert);
					}
				}else{
					$this->deposit_libraries->cal_deposit_interest_by_account($data['account_id'],'real');
					
					$data_insert = array();
					$data_insert['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
					$data_insert['transaction_list'] = $transaction_list;
					$data_insert['transaction_withdrawal'] = $data['money'];
					$data_insert['transaction_deposit'] = '';
					$data_insert['transaction_balance'] = '0';
					$data_insert['transaction_no_in_balance'] = '0';
					$data_insert['user_id'] = $_SESSION['USER_ID'];
					$data_insert['account_id'] = $data['account_id'];
					
					$data_insert['pay_type'] = @$data['pay_type']; //การรับเงิน
					if(@$data['pay_type'] == '1'){
						//โอนเงิน			
						$data_insert['transfer_bank_id'] = @$data['transfer_bank_id'];
						$data_insert['transfer_bank_branch_id'] = @$data['transfer_bank_branch_id'];
						$data_insert['transfer_bank_branch_name'] = @$data['transfer_bank_branch_name'];
						$data_insert['transfer_acc_num'] = @$data['transfer_acc_num'];
					}
					
					$this->db->insert('coop_account_transaction', $data_insert);
					
					//@start บันทึกรายการฝั่งเงินฝาก ATM
					//operate_code  022 = ถอน , 023 = ฝาก
					$data_atm_detail = array();
					$data_atm_detail['account_id'] = @$data['account_id'];
					$data_atm_detail['member_id'] = @$row_account['member_id'];
					$data_atm_detail['transaction_date'] = $data_insert['transaction_time'];
					$data_atm_detail['transaction_amount'] = $data_insert['transaction_withdrawal'];
					$data_atm_detail['operate_code'] = '022'; // ถอน
					$this->atm_deposit->insert_deposit_atm_detail($data_atm_detail);
					//@end บันทึกรายการฝั่งเงินฝาก ATM

                    $statement_status = 'credit';   // สถานะการจ่ายเงิน debit = เงินเข้าจากเคาน์เตอร์, credit  = เงินออกจากเคาน์เตอร์,
                    $permission_id = $this->permission_model->permission_url($_SERVER[HTTP_REFERER],$_SERVER[REQUEST_URI]);
                    $this->tranction_financial_drawer->arrange_data_coop_financial_drawer($data_insert,$data['pay_type'],$permission_id,$statement_status,$_SERVER[REQUEST_URI]);

                    $this->db->select(array('transaction_id'));
					$this->db->from('coop_account_transaction');
					$this->db->where("
						account_id = '".$data['account_id']."' 
						AND fixed_deposit_type='principal' 
						AND date_end_saving > '".date('Y-m-d H:i:s')."'
					");
					$row = $this->db->get()->result_array();
					foreach($row as $key => $value){
						$data_insert = array();
						$data_insert['fixed_deposit_status'] = '1';
						$data_insert['deposit_balance'] = '0';
						$this->db->where("transaction_id",$value['transaction_id']);
						$this->db->update('coop_account_transaction',$data_insert);
						
						$data_insert = array();
						$data_insert['fixed_deposit_status'] = '1';
						$this->db->where("ref_transaction_id",$value['transaction_id']);
						$this->db->update('coop_account_transaction',$data_insert);
					}
					
				}
			}else{
				$money = (empty($data['total_amount']))?$data['money']:$data['total_amount'];
				
				$opn_min = 0;
				if($row_setting_detail["type_fee"] == 4) {
					if($row_setting_detail["is_balance_not_min_open"]) {
						// ยอดเปิดบัญชี
						$this->db->select("transaction_balance");
						$this->db->from("coop_account_transaction");
						$this->db->where("account_id = '".$data['account_id']."'");
						$this->db->order_by("transaction_time, transaction_id");
						$this->db->limit(1);
						$_row = $this->db->get()->row_array();
						$opn = $_row["transaction_balance"];
						
						// ยอดขั้นต่ำเปิดบัญชี
						$this->db->select("*");
						$this->db->from("coop_deposit_type_setting_interest");
						$this->db->where("type_detail_id = '".$row_setting_detail['type_detail_id']."' AND amount_deposit <= '".$opn."'");
						$this->db->order_by("amount_deposit DESC");
						$this->db->limit(1);
						$_row = $this->db->get()->row_array();
						
						$opn_min = $_row["amount_deposit"];
						
						if(strtotime($row_setting_detail['withdraw_date_before']) > strtotime($date_transaction)) {
							// ถอนก่อนครบกำหนด
							
							// ยอดคงเหลือไม่รวมดอกเบี้ย
							$this->db->select("SUM(transaction_deposit) AS dep, SUM(transaction_withdrawal) AS wtd");
							$this->db->from("coop_account_transaction");
							$this->db->where("account_id = '".$data['account_id']."' AND transaction_time <= '".$date_transaction." ".$time_transaction."' AND transaction_list NOT IN ('IN', 'INT')");
							$_row = $this->db->get()->row_array();
							$_balance_no_in = $_row["dep"] - $_row["wtd"];
							
							// ยอดรวมดอกเบี้ย
							$this->db->select("SUM(transaction_deposit) AS interest");
							$this->db->from("coop_account_transaction");
							$this->db->where("account_id = '".$data['account_id']."' AND transaction_time <= '".$date_transaction." ".$time_transaction."' AND transaction_list IN ('IN', 'INT')");
							$_row = $this->db->get()->row_array();
							$_interest = $_row["interest"];
							
							// เปอร์เซ็นคืนดอกเบี้ยของยอดถอน
							$percent_return = $_balance_no_in > 0 ? $money / $_balance_no_in * 100 : 0;
							
							// ดอกเบี้ยคืน
							$return_interest = round($_interest * $percent_return / 100, 2);
							
							$sum = round($balance - $return_interest, 2);
							
							$data_insert = array();
							$data_insert['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
							$data_insert['transaction_list'] = 'R/IN';
							$data_insert['transaction_withdrawal'] = $return_interest;
							$data_insert['transaction_deposit'] = '';
							$data_insert['transaction_balance'] = $sum;
							$data_insert['transaction_no_in_balance'] = $money;
							$data_insert['user_id'] = $_SESSION['USER_ID'];
							$data_insert['account_id'] = $data['account_id'];
							$data_insert['pay_type'] = @$data['pay_type']; //การรับเงิน
							
							$this->db->insert('coop_account_transaction', $data_insert);
							
							//@start บันทึกรายการฝั่งเงินฝาก ATM
							//operate_code  022 = ถอน , 023 = ฝาก
							$data_atm_detail = array();
							$data_atm_detail['account_id'] = @$data['account_id'];
							$data_atm_detail['member_id'] = @$row_account['member_id'];
							$data_atm_detail['transaction_date'] = $data_insert['transaction_time'];
							$data_atm_detail['transaction_amount'] = $data_insert['transaction_withdrawal'];
							$data_atm_detail['operate_code'] = '022'; // ถอน
							$this->atm_deposit->insert_deposit_atm_detail($data_atm_detail);
							//@end บันทึกรายการฝั่งเงินฝาก ATM
							
							$balance = $sum;
							
							// ดอกเบี้ยยอดถอน
							$this->db->select("*");
							$this->db->from("coop_account_transaction");
							$this->db->where("account_id = '".$data['account_id']."' AND transaction_time <= '".$date_transaction." ".$time_transaction."' AND transaction_list NOT IN ('IN', 'INT') AND transaction_deposit > 0");
							$this->db->order_by("transaction_time DESC, transaction_id DESC");
							$_rs = $this->db->get()->result_array();
							$_dep = 0;
							foreach($_rs as $_row) {
								$_dep += $_row["transaction_deposit"];
								if($_dep >= $money) {
									$this->db->select(array(
										't1.type_detail_id',
										't1.type_id',
										't1.start_date',
										't2.amount_deposit',
										't2.percent_interest as interest_rate'
									));
									$this->db->from('coop_deposit_type_setting_detail as t1');
									$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
									$this->db->where("t1.type_id = '".$row_setting_detail['ref_type_id']."' AND t1.start_date <= '".$date_transaction."'");
									$this->db->order_by("t1.start_date DESC, t1.type_detail_id DESC");
									$this->db->limit(1);
									$_row_interest_rate = $this->db->get()->row_array();
									
									$_interest = round($this->deposit_libraries->cal_interest($money, $_row_interest_rate["interest_rate"], $_row["transaction_time"], $date_transaction." ".$time_transaction), 2);
									$sum = round($balance + $_interest, 2);
									
									$data_insert = array();
									$data_insert['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
									$data_insert['transaction_list'] = 'IN/S';
									$data_insert['transaction_withdrawal'] = '';
									$data_insert['transaction_deposit'] = $_interest;
									$data_insert['transaction_balance'] = $sum;
									$data_insert['transaction_no_in_balance'] = $balance_no_in;
									$data_insert['user_id'] = $_SESSION['USER_ID'];
									$data_insert['account_id'] = $data['account_id'];
									$data_insert['pay_type'] = @$data['pay_type']; //การรับเงิน
									
									$this->db->insert('coop_account_transaction', $data_insert);
									
									//@start บันทึกรายการฝั่งเงินฝาก ATM
									//operate_code  022 = ถอน , 023 = ฝาก
									$data_atm_detail = array();
									$data_atm_detail['account_id'] = @$data['account_id'];
									$data_atm_detail['member_id'] = @$row_account['member_id'];
									$data_atm_detail['transaction_date'] = $data_insert['transaction_time'];
									$data_atm_detail['transaction_amount'] = $data_insert['transaction_deposit'];
									$data_atm_detail['operate_code'] = '023'; // ฝาก
									$this->atm_deposit->insert_deposit_atm_detail($data_atm_detail);
									//@end บันทึกรายการฝั่งเงินฝาก ATM
									
									$money = round($money + $_interest, 2);
									$balance = $sum;
									break;
								}
							}
						}
					}
				}
				
				$sum = $balance - $money;
				$sum_no_in = $balance_no_in - $money;
				if($sum_no_in <= 0 ){$sum_no_in = 0;}
				if($sum < 0) {
					$this->center_function->toastDanger("ไม่สามารถถอนเงินได้เนื่องจากจำนวนเงินคงเหลือไม่พอ");
				} elseif($sum < $opn_min) {
					$this->center_function->toastDanger("ไม่สามารถถอนเงินได้เนื่องจากจำนวนเงินคงเหลือต่ำกว่า ".number_format($opn_min, 2));
				} else {
					$data_insert = array();
					$data_insert['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
					$data_insert['transaction_list'] = $transaction_list;
					$data_insert['transaction_withdrawal'] = $money;
					$data_insert['transaction_deposit'] = '';
					$data_insert['transaction_balance'] = $sum;
					$data_insert['transaction_no_in_balance'] = $sum_no_in;
					$data_insert['user_id'] = $_SESSION['USER_ID'];
					$data_insert['account_id'] = $data['account_id'];
					$data_insert['accu_int_item'] = @$data_cal_accu_int['accu_int_item'];
					$data_insert['old_acc_int'] = @$data_cal_accu_int['old_acc_int'];
					
					$data_insert['pay_type'] = @$data['pay_type']; //การรับเงิน
					if(@$data['pay_type'] == '1'){
						//โอนเงิน			
						$data_insert['transfer_bank_id'] = @$data['transfer_bank_id'];
						$data_insert['transfer_bank_branch_id'] = @$data['transfer_bank_branch_id'];
						$data_insert['transfer_bank_branch_name'] = @$data['transfer_bank_branch_name'];
						$data_insert['transfer_acc_num'] = @$data['transfer_acc_num'];
					}
					
					$this->db->insert('coop_account_transaction', $data_insert);
					// if ($data['pay_type']==1){	
					
					// 	if (!empty( $data['pay_type_transfer'] && $data['withdraw_list'] )){
					// 		$this->db->insert('coop_account_transaction', $data_insert);
					// 		$pay_type_transfers=$data['pay_type_transfer'];
					// 		$withdraw_lists=$data['withdraw_list'];
					// 		$pay_type_transfer=explode( ',', $pay_type_transfers );
					// 		$withdraw_list=explode( ',', $withdraw_lists);
					// 		$transaction_id = $this->db->insert_id();
					// 		$balance_list=$data['withdrawal_amount_get'];
					// 		// echo"<pre>";print_r($_rs);exit;
					// 		foreach($pay_type_transfer as $key =>$value){
					// 			$insert_wd_list[$key] = $withdraw_list[$key];
					// 			$insert_pay_type_transfer[$key] = $value;
									
					// 		};
							
					// 		foreach($insert_wd_list as $key2 =>$value2){
					// 			$balance_list -= $value2;
					// 			$data_insert_transaction_transfer= array();
					// 			$data_insert_transaction_transfer['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
					// 			$data_insert_transaction_transfer['transaction_list'] =$insert_pay_type_transfer[$key2];
					// 			$data_insert_transaction_transfer['transaction_withdrawal'] = $value2;
					// 			$data_insert_transaction_transfer['transaction_deposit'] = '';
					// 			$data_insert_transaction_transfer['transaction_balance'] = $balance_list;
					// 			$data_insert_transaction_transfer['account_id'] = $data['account_id'];
					// 			$data_insert_transaction_transfer['pay_type'] = $data['pay_type'];
					// 			$data_insert_transaction_transfer['transaction_note'] = '';	
					// 			$data_insert_transaction_transfer['transaction_id'] = $transaction_id;
					// 			$insert_transaction_transfesr_wd[]=$data_insert_transaction_transfer;
					// 		}
					// 		$this->db->insert_batch('coop_account_transaction_transfer', $insert_transaction_transfesr_wd);
					// 	}else{
					// 		$this->center_function->toastDanger("กรุณาตรวจสอบรายการแยกการโอนให้ถูกต้อง ");
					// 	}
						
					// }else{
					// 		$this->db->insert('coop_account_transaction', $data_insert);
					// }
					
					//@start บันทึกรายการฝั่งเงินฝาก ATM
					//operate_code  022 = ถอน , 023 = ฝาก
					$data_atm_detail = array();
					$data_atm_detail['account_id'] = @$data['account_id'];
					$data_atm_detail['member_id'] = @$row_account['member_id'];
					$data_atm_detail['transaction_date'] = $data_insert['transaction_time'];
					$data_atm_detail['transaction_amount'] = $data_insert['transaction_withdrawal'];
					$data_atm_detail['operate_code'] = '022'; // ถอน
					$this->atm_deposit->insert_deposit_atm_detail($data_atm_detail);
					//@end บันทึกรายการฝั่งเงินฝาก ATM

                    $statement_status = 'credit';   // สถานะการจ่ายเงิน debit = เงินเข้าจากเคาน์เตอร์, credit  = เงินออกจากเคาน์เตอร์,
                    $permission_id = $this->permission_model->permission_url($_SERVER['HTTP_REFERER'],$_SERVER['REQUEST_URI']);
                    $this->tranction_financial_drawer->arrange_data_coop_financial_drawer($data_insert,$data['pay_type'],$permission_id,$statement_status,$_SERVER['REQUEST_URI']);


                    //echo $this->db->last_query();
					//ค่าดำเนินการอื่นๆ
					if(@$data['commission_fee']){
						//echo $data['commission_fee'].'<hr>';
						$sum = $sum - $data['commission_fee'];
						$sum_no_in = $sum_no_in - $data['commission_fee'];
						$data_insert = array();
						$data_insert['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
						$data_insert['transaction_list'] = 'CM/FE';
						$data_insert['transaction_withdrawal'] = $data['commission_fee'];
						$data_insert['transaction_deposit'] = '';
						$data_insert['transaction_balance'] = $sum;
						$data_insert['transaction_no_in_balance'] = $sum_no_in;
						$data_insert['user_id'] = $_SESSION['USER_ID'];
						$data_insert['account_id'] = $data['account_id'];
						$data_insert['pay_type'] = @$data['pay_type']; //การรับเงิน
						
						$this->db->insert('coop_account_transaction', $data_insert);
						
						//@start บันทึกรายการฝั่งเงินฝาก ATM
						//operate_code  022 = ถอน , 023 = ฝาก
						$data_atm_detail = array();
						$data_atm_detail['account_id'] = @$data['account_id'];
						$data_atm_detail['member_id'] = @$row_account['member_id'];
						$data_atm_detail['transaction_date'] = $data_insert['transaction_time'];
						$data_atm_detail['transaction_amount'] = $data_insert['transaction_withdrawal'];
						$data_atm_detail['operate_code'] = '022'; // ถอน
						$this->atm_deposit->insert_deposit_atm_detail($data_atm_detail);
						//@end บันทึกรายการฝั่งเงินฝาก ATM
					}
					//echo $this->db->last_query();


                    $data_process = array();

                    // echo"<pre>";print_r($this->input->post());echo"</pre>";
                    $process = 'withdraw_money';
                    $money = $data['money'];
                    $ref = $_POST['account_id'];
                    $match_type = 'save_transaction';
                    $match_id = $row_account['type_id'];
                    if($data['pay_type']=='0') {
                        $statement = 'debit';
                    }else{
                        $statement = 'credit';
                    }

                    $data_process[] =   $this->account_transaction->set_data_account_trancetion_detail($match_id,$statement,$match_type,$ref,$money,$process);

                    $process = 'withdraw_money';
                    $money = $data['money'];
                    $ref = $_POST['account_id'];
                    $match_type = 'main';
                    $match_id = '1';
                    if($data['pay_type']=='0') {
                        $statement = 'credit';
                    }else{
                        $statement = 'debit';
                    }

                    $data_process[] =   $this->account_transaction->set_data_account_trancetion_detail($match_id,$statement,$match_type,$ref,$money,$process);
                    $this->account_transaction->add_account_trancetion_detail($data_process);

                    if($date_transaction!="" && $data['custom_by_user_id']!="") {

                        $st_last = $this->db->select('transaction_time')->from("coop_account_transaction")->where(
                            array(
                                "transaction_time <=" => $data_insert['transaction_time'],
                                "account_id" => $data['account_id']
                            ))->order_by('transaction_time, transaction_id', 'DESC')
                            ->limit(1)->get()->row_array();

                        $data_trigger = array(
                            'date' => $st_last['transaction_time'],
                            'account_id' => $data['account_id']
                        );
                        $this->update_st->update_balance_statement($data_trigger);
                    }
					$this->center_function->toast("ทำการถอนเงินเรียบร้อยแล้ว");
					
				}
			}
			//exit();
			echo "<script> window.location.href = '".base_url(PROJECTPATH.'/save_money/account_detail?account_id='.$data['account_id'])."'</script>"; 
			exit();
		}else if($data["do"] == "update_cover") {
			
			$this->db->select('*');
			$this->db->from('coop_maco_account');
			$this->db->where("account_id = '".$data['account_id']."'");
			$row = $this->db->get()->result_array();
			if($row[0]['book_number'] == $data['book_number']){
				$this->center_function->toastDanger("เล่มบัญชีของท่านเป็นเล่มที่ ".$data['book_number']." แล้ว");
			}else{
				$data_insert = array();
				$data_insert['book_number'] = $data['book_number'];
				$data_insert['print_number_point_now'] = '1';
				$this->db->where('account_id', $data['account_id']);
				$this->db->update('coop_maco_account', $data_insert);
				$this->center_function->toast("เพิ่มเล่มบัญชีเรียบร้อยแล้ว");
			}
			echo "<script> window.location.href = '".base_url(PROJECTPATH.'/save_money/account_detail?account_id='.$data['account_id'])."'</script>"; 
			exit();
		}
		
	}
	
	function book_bank_cover_pdf(){
		$arr_data = array();
		$account_id = $this->input->get('account_id');
		$arr_data['account_id'] = $account_id;
		$this->db->select(array('account_name','mem_id','book_number'));
		$this->db->from('coop_maco_account');
		$this->db->where("account_id = '".$account_id."'");
		$row = $this->db->get()->result_array();
		$arr_data['row'] = $row[0];
		
		$this->db->select(array('mem_group_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '".$row[0]['mem_id']."'");
		$row_group = $this->db->get()->result_array();
		$arr_data['row_group'] = $row_group[0];
		
		$this->db->select(array('mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_id = '".$row_group[0]['mem_group_id']."'");
		$row_gname = $this->db->get()->result_array();
		if(!empty($row_gname)){
			$arr_data['row_gname'] = $row_gname[0];
		}else{
			$arr_data['row_gname']['mem_group_name'] = '';
		}
		
		
		$this->load->view('save_money/book_bank_cover_pdf',$arr_data);
	}

	function book_bank_cover_pdf_customize(){
		$arr_data = array();
		$account_id = $this->input->get('account_id');
		$arr_data['account_id'] = $account_id;
		
		
		$this->db->select(array('mem_group_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '".$row[0]['mem_id']."'");
		$row_group = $this->db->get()->result_array();
		$arr_data['row_group'] = $row_group[0];
		
		$this->db->select(array('mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_id = '".$row_group[0]['mem_group_id']."'");
		$row_gname = $this->db->get()->result_array();
		if(!empty($row_gname)){
			$arr_data['row_gname'] = $row_gname[0];
		}else{
			$arr_data['row_gname']['mem_group_name'] = '';
		}

		$style = $this->db->get_where("coop_book_bank_style", array(
			"style_id" => 1
		))->result_array()[0];
		$arr_data['style'] = $style;

		$rows = $this->db->get_where("coop_book_bank_style_setting", array(
			"style_id" => 1
		))->result_array();
		foreach ($rows as $key => $value) {
			// var_dump($value);
			// echo "<br>";
			$meta = $value['style_value'];
			$text = $meta;
			if($meta == "[account_name]"){
				$this->db->select(array('account_name'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row = $this->db->get()->result_array();
				$text = $row[0]['account_name'];
			}

			if($meta == "[book_number]"){
				$this->db->select(array('book_number'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row = $this->db->get()->result_array();
				$text = $row[0]['book_number'];
			}

			if($meta == "[member_id]"){
				$this->db->select(array('mem_id'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row = $this->db->get()->result_array();
				$text = $row[0]['mem_id'];
			}
			if($meta == "[account_id]"){
				/*$account_id_arr = str_split($account_id, 1);
				$account_id_format = '';
				foreach($account_id_arr as $k => $val){
					if($k == '1' || $k == '4' || $k == '5')
						$account_id_format .= ' - '.$val;
					else
						$account_id_format .= $val;
				}
				*/
				$account_id_format = $this->center_function->format_account_number($account_id);
				$text = $account_id_format;
			}

			if($meta == "[date_now]"){
				$text = date("d/m/").(date("Y")+543);
			}	

			if($meta == "[book_name]"){
				$this->db->select(array('coop_deposit_type_setting.type_name'));
				$this->db->from('coop_deposit_type_setting');
				$this->db->where("coop_deposit_type_setting.type_id = (SELECT coop_maco_account.type_id FROM coop_maco_account WHERE coop_maco_account.account_id = '".$account_id."')");
				$row = $this->db->get()->result_array();
				$text = $row[0]['type_name'];
			}
			if($meta == "[address]"){	
				$this->db->select(array('mem_id'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row_member = $this->db->get()->result_array();
				$address = $this->address_model->address($row_member[0]['mem_id']);		
				$text = $address;
			}

			$rows[$key]['text'] = $text;
			
		}
		$arr_data['rows'] = $rows;
		// var_dump($rows);
		// exit;
		
	
		$this->load->view('save_money/book_bank_cover_pdf_customize',$arr_data);
	}
	
	function book_bank_page_pdf(){
		$arr_data = array();
		
		
		$this->load->view('save_money/book_bank_page_pdf',$arr_data);
	}
	
	function change_status($transaction_id, $account_id){
		$data_insert = array();
		$data_insert['print_status'] = '';
		$data_insert['print_number_point'] = '';
		$data_insert['book_number'] = '';
		$this->db->where(array('transaction_id >=' => $transaction_id, 'account_id'=>$account_id));
		$this->db->update('coop_account_transaction', $data_insert);
		
		$data_insert = array();
		$data_insert['print_number_point_now'] = '';
		$data_insert['last_time_print'] = '';
		$this->db->where('account_id', $account_id);
		$this->db->update('coop_maco_account', $data_insert);
		
		$this->center_function->toast("ยกเลืกพิมพ์รายการเรียบร้อยแล้ว");
		echo "<script> document.location.href = '".base_url(PROJECTPATH.'/save_money/account_detail?account_id='.$account_id)."'</script>";
		exit();
	}
	
	//เช็คฝาเงินต่ำสุด-สูงสุด
	function check_max_min_deposit(){
		$money_deposit = @$_POST['money_deposit'];
		$type_id = @$_POST['type_id'];
		$account_id = @$_POST['account_id'];
		$today = date('Y-m-d');
		
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting');
		$this->db->join("coop_deposit_type_setting_detail","coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id","left");
		$this->db->where("coop_deposit_type_setting.type_id = '{$type_id}' AND coop_deposit_type_setting_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_deposit_type_setting_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		
		$this->db->select('SUM(transaction_deposit) AS sum_int');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."' AND transaction_list IN ('INT', 'IN')");
		$rs = $this->db->get()->result_array();
		$row_int = @$rs[0];
		
		$this->db->select('*');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row_tran = @$rs[0];
		$balance = $row_tran["transaction_balance"] + $money_deposit - (double)$row_int["sum_int"];
		
		if($row['is_deposit_num'] == '1'){
			$this->db->select('money_type_name_short');
			$this->db->from('coop_money_type');
			$this->db->where("id='1'");
			$row_deposit = $this->db->get()->row_array();		
			
			$this->db->select('money_type_name_short');
			$this->db->from('coop_money_type');
			$this->db->where("id='9'");
			$row_error = $this->db->get()->row_array();
			
			//เช็คจำนวนครั้งที่ฝากรายเดือน หรือ ปี
			$check_month = date('Y-m');
			$check_year = date('Y');
			$chk_deposit_num_type = (@$row['deposit_num_type'] == '0')?$check_month:$check_year;
			
			$this->db->select('transaction_time,transaction_list,transaction_deposit');
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '".$account_id."' AND transaction_time LIKE '".$chk_deposit_num_type."%'");
			$this->db->order_by('transaction_time DESC, transaction_id DESC');
			$rs_deposit_num = $this->db->get()->result_array();	
			$n_deposit = 0;
			foreach($rs_deposit_num AS $key=>$row_deposit_num){				
				if(@$row_deposit_num['transaction_list'] == @$row_deposit['money_type_name_short']){
					$n_deposit++;
				}
				
				if(@$row_deposit_num['transaction_list'] == @$row_error['money_type_name_short']){
					$n_deposit--;				
				}
			}		
		}
		
		if($money_deposit < $row['amount_min'] && $row['amount_min'] != 0){
			echo 'การฝากเงินต้องฝากเงินต้นขั้นต่ำ '.number_format($row['amount_min']).' บาท';
		}else if($money_deposit > $row['amount_max_time'] && $row['amount_max_time'] != 0){
			echo 'การฝากเงินสูงสุดต่อครั้งต้องไม่เกิน '.number_format($row['amount_max_time']).' บาท';
		}else if($balance > $row['amount_max'] && $row['amount_max'] != 0){
			echo 'การฝากเงินรวมทั้งหมดต้องไม่เกิน '.number_format($row['amount_max']).' บาท';
		}else if(@$n_deposit >= @$row['deposit_num']){
			//ฝากเงินได้ไม่เกิน เดือน หรือ ปี ละ 2 ครั้ง
			$deposit_num_type = (@$row['deposit_num_type'] == '0')?'เดือน':'ปี';
			echo 'ฝากเงินได้ไม่เกิน '.$deposit_num_type.' ละ '.@$row['deposit_num'].'  ครั้ง';
		}else{
			echo 'Y';
		}
		exit;
	}
	
	//เช็คถอนเงินต่ำสุด-สูงสุด
	function check_max_min_withdrawal(){
		$money = @$_POST['money'];
		$type_id = @$_POST['type_id'];
		$account_id = @$_POST['account_id'];
		$today = date('Y-m-d');
		
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting');
		$this->db->join("coop_deposit_type_setting_detail","coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id","left");
		$this->db->where("coop_deposit_type_setting.type_id = '{$type_id}' AND coop_deposit_type_setting_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_deposit_type_setting_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		
		$this->db->select('*');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row_tran = @$rs[0];
		$balance = $row_tran["transaction_balance"] - $money;
		
		if($money < $row['withdraw_min'] && $row['is_withdraw_min']){
			echo 'การถอนเงินต้องถอนขั้นต่ำ '.number_format($row['withdraw_min']).' บาท';
		}elseif($balance < $row['balance_min'] && $row['is_balance_min']){
			echo 'ต้องมีเงินคงเหลือไม่ต่ำกว่า '.number_format($row['balance_min']).' บาท';
		}else{
			echo 'Y';
		}
		exit;
	}
	
	//เช็คค่าธรรมเนียมการถอน
	function check_fee_withdrawal(){
		$money_withdrawal = @$_POST['money_withdrawal'];
		$type_id = @$_POST['type_id'];
		$account_id = @$_POST['account_id'];
		$today = date('Y-m-d');
		$yymm_now = date('Y-m');
		
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting');
		$this->db->join("coop_deposit_type_setting_detail","coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id","left");
		$this->db->where("coop_deposit_type_setting.type_id = '{$type_id}' AND coop_deposit_type_setting_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_deposit_type_setting_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		
		if($row['type_fee'] == '1'){
			//ไม่มีค่าธรรมเนียมการถอน
			echo '';
		}else if($row['type_fee'] == '2'){
			//มีค่าธรรมเนียมการถอน % ของยอดเงินที่ถอน
			$fee = ($money_withdrawal*$row['percent_fee'])/100;
			echo $fee;
		}else if($row['type_fee'] == '3'){
			//มีค่าธรรมเนียมการถอน เมื่อถอนก่อนกำหนด ผู้ฝากได้รับดอกเบี้ย % ที่เหลือสหกรณ์ได้รับดอกเบี้ย
			//$row['num_month_before'];
			//$row['percent_depositor'];
			echo '';
		}else{
			//ไม่มีค่าธรรมเนียมการถอน
			echo '';
		}
		
		
		if($row['staus_withdraw'] == '1'){
			//ถอนได้เดือน ล่ะ กี่ครั้ง withdraw_num
			//ถ้า ครั้งที่ กำหนด  จะเสีย % ในการถอน  withdraw_num_interest
			// % ที่จะเสียในการถอน withdraw_percent_interest
			$count_withdraw = 0;
			$fee = '';
			$this->db->select("COUNT(*) AS c");
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '{$account_id}' AND transaction_list = 'CW' AND YEAR(transaction_time) = YEAR('{$today}')".($row["withdraw_num_unit"] == "1" ? "" : " AND MONTH(transaction_time) = MONTH('{$today}')"));
			$row_transaction = $this->db->get()->row_array();
			$count_withdraw = $row_transaction["c"];

			$this->db->select("COUNT(*) AS err");
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '{$account_id}' AND transaction_list = 'ERR' AND YEAR(transaction_time) = YEAR('{$today}')".($row["withdraw_num_unit"] == "1" ? "" : " AND MONTH(transaction_time) = MONTH('{$today}')"));
			$row_transaction = $this->db->get()->row_array();
			$count_err = $row_transaction["err"];
			
			if(($count_withdraw-$count_err) >= $row["withdraw_num"]) {
				$fee = ($money_withdrawal*$row['withdraw_percent_interest'])/100;
				$fee = $fee < $row["withdraw_percent_min"] ? $row["withdraw_percent_min"] : $fee;
			}
			
			echo $fee;
		}	
		//echo '<pre>'; print_r($row); echo '</pre>';
		exit;
	}

	function deposit_cal_interest(){
		$arr_data = array();
		
		$this->db->select(array('*'));
		$this->db->from('coop_maco_account as t1');
		$this->db->where("account_status = '0'");
		$row = $this->db->get()->result_array();
		$arr_data['account_data'] = $row;
		if($_POST){
			$arr_data['interest_data'] = $this->test_deposit_interest($_POST);
			//exit;
		}
		$this->libraries->template('save_money/deposit_cal_interest',$arr_data);
	}
	
	function test_deposit_interest($data_post){
		//echo"<pre>";print_r($data_post);echo"</pre>"; //exit;
		$date_interest = $this->center_function->ConvertToSQLDate($data_post['date_interest']);
		$account_id = $data_post['account_id'];
		$day_interest = date('d',strtotime($date_interest));
		$this->db->select(array(
			't1.member_id',
			't2.account_id',
			't2.type_id',
			't2.created as create_account_date'
		));
		$this->db->from('coop_mem_apply as t1');
		$this->db->join('coop_maco_account as t2','t1.member_id = t2.mem_id','inner');
		$this->db->where("t2.account_id = '".$account_id."'");
		$rs_member = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_member as $key_member => $row_member){
			$transaction = $this->deposit_libraries->cal_deposit_interest($row_member, 'test_cal_interest', $date_interest, $day_interest);
		}
		//echo"<pre>";print_r($transaction);exit;
		return $transaction;
		//exit;
	}
	
	public function deposit_month()
	{
		if($this->input->get('member_id')!=''){
			//$mem_id = $this->input->get('member_id');
            //$mem_id = sprintf("%06d", $this->input->get('member_id'));
			$mem_id = $this->center_function->complete_member_id($this->input->get('member_id'));
		}else{
			$mem_id = '';
		}
		$arr_data = array();

		if($mem_id != '') {
			$this->db->select(array('salary','other_income','member_id'));
			$this->db->from('coop_mem_apply');
			$this->db->where("member_id LIKE '%".$mem_id."%'");
			$rs_member = $this->db->get()->result_array();
			$row_member = $rs_member[0];
			@$row_member['salary_income'] = @$row_member['salary']+ @$row_member['other_income'];
			$arr_data['row_member'] = @$row_member;
			$member_id = @$row_member['member_id'];
			
			$this->db->select(array('t1.type_id','t1.type_code','t2.account_id','t2.account_name'));
			$this->db->from('coop_deposit_type_setting AS t1');
			$this->db->join("coop_maco_account AS t2","t1.type_id = t2.type_id","left");
			$this->db->where("t2.mem_id = '".@$member_id."'");
			$rs_account = $this->db->get()->result_array();
			$arr_data['row_accounts'] = $rs_account;
			$row_account = $rs_account[0];
			$arr_data['row_account'] = @$row_account;

			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_user';
			$join_arr[$x]['condition'] = 'coop_deposit_month_transaction.admin_id = coop_user.user_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select(array('coop_deposit_month_transaction.*','coop_user.user_name'));
			$this->paginater_all->main_table('coop_deposit_month_transaction');
			$this->paginater_all->where("member_id = '".$member_id."'");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];


			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			$arr_data['i'] = $i;
		}else{
			$arr_data['data'] = array();
			$arr_data['paging'] = '';
		}
		
		//list เดือน
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		$this->libraries->template('save_money/deposit_month',$arr_data);
	}
	
	function save_deposit_month(){
		$data = $this->input->post();
		
		$data_insert = array();
		$data_insert['member_id'] = @$data['member_id'];
		$data_insert['account_id'] = @$data['account_id'];
		$data_insert['deduction_type'] = @$data['deduction_type'];
		$data_insert['deduction_month'] = @$data['month'];
		$data_insert['deduction_year'] = @$data['year'];
		$data_insert['total_amount'] = str_replace(',','',@$data['total_amount']);
		$data_insert['admin_id'] = @$_SESSION['USER_ID'];		
		$data_insert['updatetime'] = date('Y-m-d H:i:s');	
		
		$this->db->select('*');
		$this->db->from('coop_deposit_month_transaction');
		$this->db->where("member_id = '".@$data['member_id']."' AND deduction_month = '".@$data['month']."' AND deduction_year = '".@$data['year']."'
							AND account_id = '".$data['account_id']."' AND deduction_type ='0'");
		$this->db->order_by('id DESC');			
		$rs_deduction = $this->db->get()->result_array();
		//echo $this->db->last_query();
		if(!empty($rs_deduction)){
			$this->db->where("member_id",@$data['member_id']);
			$this->db->where("deduction_month",@$data['month']);
			$this->db->where("deduction_year",@$data['year']);
			$this->db->update('coop_deposit_month_transaction', $data_insert);

		}else{					
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert('coop_deposit_month_transaction', $data_insert);		
		}
		//exit;
		$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
		
		echo "<script> document.location.href = '".PROJECTPATH."/save_money/deposit_month?member_id=".@$data['member_id']."' </script>";
		exit;
	}
	
	function check_deduction_month(){
		$month_arr = $this->center_function->month_arr();
		$mem_id = @$_POST['member_id'];
		$deduction_month = @$_POST['deduction_month'];
		$deduction_year = @$_POST['deduction_year'];
		$deduction_type = @$_POST['deduction_type'];
		
		$month_now = (int)date('m');
		$year_now = date('Y')+543;
		
		$this->db->select(array('member_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id LIKE '%".$mem_id."%'");
		$rs_member = $this->db->get()->result_array();
		$row_member = $rs_member[0];
		$member_id = @$row_member['member_id'];
		
		$this->db->select('*');
		$this->db->from('coop_deposit_month_transaction');
		$this->db->where("member_id = '".@$member_id."' AND deduction_month = '".@$deduction_month."' AND deduction_year = '".@$deduction_year."'");
		$this->db->order_by('id DESC');			
		$rs_deduction = $this->db->get()->result_array();
		//echo $this->db->last_query();
		//exit;
		$count_all = 0;
		$count_refrain = 0;
		if(!empty($rs_deduction)){
			foreach($rs_deduction AS $key=>$value){
				if(@$value['deduction_type'] == '1'){
					$count_refrain++;
				}
				$count_all++;
			}
		}		
		
		$deduction_day = ($deduction_year-543)."-".sprintf("%02d",$deduction_month)."-01"; //เดือนที่เลือก
		$now_day = (date('Y'))."-".sprintf("%02d",date('m'))."-01"; //เดือนปัจจุบัน
		
		if($deduction_day < $now_day){
			echo "ไม่สามารถเลือกเดือน ".$month_arr[$deduction_month]." ".$deduction_year." ได้ \nเนื่องจากน้อยกว่าเดือน ปัจจุบัน";
		}else if($count_all != 0 && $deduction_type == '1'){
			echo "ไม่สามารถเลือกเดือน ".$month_arr[$deduction_month]." ".$deduction_year." ได้ \nเนื่องจากมีในระบบแล้ว";
		}else if($count_refrain != 0 && $deduction_type == '0'){
			echo "ไม่สามารถเลือกเดือน ".$month_arr[$deduction_month]." ".$deduction_year." ได้ \nเนื่องจากมีในระบบแล้ว";
		}else{	
			echo 'ok';
		}
		exit;
	}
	
	function check_member_id(){
		//$member_id = sprintf("%06d", @$_POST['member_id']);
		$member_id = $this->center_function->complete_member_id(@$_POST['member_id']);
		$arr_data = array();
		$this->db->select(array('id','member_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id LIKE '%".$member_id."%'");
		$this->db->limit(1);
		$rs_member = $this->db->get()->result_array();
		//echo $this->db->last_query();exit;
		$row_member = $rs_member[0];
		if(!empty($row_member)){
			$arr_data = @$row_member;
		}else{
			$arr_data = array();
		}	
		//echo '<pre>'; print_r($arr_data); echo '</pre>';
		echo json_encode($arr_data);	
		exit;
	}	
	
	function cancel_transaction($transaction_id){
		//echo $transaction_id;exit;
		$this->db->select(array(
			'account_id',
			'transaction_withdrawal',
			'transaction_deposit'
		));
		$this->db->from('coop_account_transaction');
		$this->db->where("transaction_id = '".$transaction_id."'");
		$row = $this->db->get()->result_array();
		$transaction_data = $row[0];
		if($transaction_data['transaction_withdrawal'] > 0){
			$data_type = 'transaction_withdrawal';
			$return_amount = $transaction_data['transaction_withdrawal']*(-1);
		}else{
			$data_type = 'transaction_deposit';
			$return_amount = $transaction_data['transaction_deposit']*(-1);
		}
		
		$this->db->select(array(
			'transaction_balance',
			'transaction_no_in_balance'
		));
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$transaction_data['account_id']."'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$last_transaction = @$row[0];
		
		$data_insert = array();
		$data_insert['account_id'] = $transaction_data['account_id'];
		if($data_type == 'transaction_withdrawal'){
			$data_insert['transaction_withdrawal'] = $return_amount;
			$data_insert['transaction_deposit'] = '0';
			$data_insert['transaction_balance'] = $last_transaction['transaction_balance']+$transaction_data['transaction_withdrawal'];
			$data_insert['transaction_no_in_balance'] = $last_transaction['transaction_no_in_balance']+$transaction_data['transaction_withdrawal'];
		}else{
			$data_insert['transaction_deposit'] = $return_amount;
			$data_insert['transaction_withdrawal'] = '0';
			$data_insert['transaction_balance'] = $last_transaction['transaction_balance']-$transaction_data['transaction_deposit'];
			$data_insert['transaction_no_in_balance'] = $last_transaction['transaction_no_in_balance']-$transaction_data['transaction_deposit'];
		}
		$data_insert['transaction_time'] = date('Y-m-d H:i:s');
		$data_insert['transaction_list'] = 'ERR';
		$data_insert['user_id'] = $_SESSION['USER_ID'];
		$data_insert['cancel_ref_transaction_id'] = $transaction_id;
		$this->db->insert('coop_account_transaction',$data_insert);
		
		$data_insert = array();
		$data_insert['cancel_status'] = '1';
		$this->db->where('transaction_id',$transaction_id);
		$this->db->update('coop_account_transaction',$data_insert);
		
		$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
		echo "<script> document.location.href = '".PROJECTPATH."/save_money/account_detail?account_id=".$transaction_data['account_id']."' </script>";
	}

	function book_bank_page_fix_line_pdf(){
		$arr_data = array();
		$this->load->view('save_money/book_bank_page_fix_line_pdf',$arr_data);
	}
	function book_bank_page_fix_line_pdf_customize(){
		$arr_data = array();
		$account_id = $this->input->get('account_id');
		$arr_data['account_id'] = $account_id;
		
		
		$this->db->select(array('mem_group_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '".$row[0]['mem_id']."'");
		$row_group = $this->db->get()->result_array();
		$arr_data['row_group'] = $row_group[0];
		
		$this->db->select(array('mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_id = '".$row_group[0]['mem_group_id']."'");
		$row_gname = $this->db->get()->result_array();
		if(!empty($row_gname)){
			$arr_data['row_gname'] = $row_gname[0];
		}else{
			$arr_data['row_gname']['mem_group_name'] = '';
		}

		$style = $this->db->get_where("coop_book_bank_style", array(
			"style_id" => 1
		))->result_array()[0];
		$arr_data['style'] = $style;

		$rows = $this->db->get_where("coop_book_bank_style_setting", array(
			"style_id" => 1
		))->result_array();
		foreach ($rows as $key => $value) {
			// var_dump($value);
			// echo "<br>";
			$meta = $value['style_value'];
			$text = $meta;
			if($meta == "[account_name]"){
				$this->db->select(array('account_name'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row = $this->db->get()->result_array();
				$text = $row[0]['account_name'];
			}

			if($meta == "[book_number]"){
				$this->db->select(array('book_number'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row = $this->db->get()->result_array();
				$text = $row[0]['book_number'];
			}

			if($meta == "[member_id]"){
				$this->db->select(array('mem_id'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row = $this->db->get()->result_array();
				$text = $row[0]['mem_id'];
			}

			if($meta == "[account_id]"){
				$account_id_arr = str_split($account_id, 1);
				$account_id_format = '';
				foreach($account_id_arr as $k => $val){
					if($k == '1' || $k == '4' || $k == '5')
						$account_id_format .= ' - '.$val;
					else
						$account_id_format .= $val;
				}
				$text = $account_id_format;
			}

			if($meta == "[date_now]"){
				$text = date("d/m/").(date("Y")+543);
			}

			$rows[$key]['text'] = $text;
			
		}
		// var_dump($rows);
		// exit;
		$arr_data['rows'] = $rows;

		// $arr_data['arr_run_row'] = $this->report_accrued->get_run_row_transaction($account_id);
		// $arr_data['arr_date_due'] = $this->report_accrued->get_date_transaction($account_id);
		$this->load->view('save_money/book_bank_page_fix_line_pdf_customize',$arr_data);
	}
	
	function close_account_calculate(){	
		//$date_interest = date('Y-m-d');
		$data = array();
		$account_id = $_POST['account_id'];
		$arr_date_close = explode('/',@$_POST['date_close']);
		$date_close = ($arr_date_close[2]-543)."-".$arr_date_close[1]."-".$arr_date_close[0];
		$date_interest = (@$_POST['date_close'])?$date_close:date('Y-m-d');
		$this->deposit_libraries->close = true;
		$cal_data = $this->deposit_libraries->cal_deposit_interest_by_acc_date($account_id, $date_interest);

		$data['interest_return'] = number_format($cal_data["interest_return"],2);
		$data['interest_rate'] = number_format($cal_data["interest_rate"],2);
		$this->db->select(array('transaction_balance'));
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$this->db->order_by("transaction_time DESC, transaction_id DESC");
		$this->db->limit(1);
		$row_transaction = $this->db->get()->row_array();
		
		$principal = $row_transaction['transaction_balance'];
		
		$this->db->select(array('account_name','DATE(created) AS date_open','type_id'));
		$this->db->from('coop_maco_account');
		$this->db->where("account_id = '".$account_id."'");
		$this->db->limit(1);
		$row_account = $this->db->get()->row_array();
		
		$data['principal'] = number_format($principal,2);
		$data['text_alert'] = "";
		$data['close_account_name'] = @$row_account['account_name'];
		$data['account_date_open'] = @$row_account['date_open'];

		$data['interest'] = number_format($cal_data["interest"],2);
		// $cal_interest_close_account = $this->deposit_libraries->cal_interest_close_account($account_id,$cal_data["interest_rate"],$date_interest);
		// $data['interest_sum']=number_format($cal_interest_close_account['interest_sum'],2);
		// if(	$cal_interest_close_account['type_id']==34){
		// 	$data['interest']=number_format($cal_interest_close_account['interest_sum'],2);
		// }else{
		// 	$data['interest'] = number_format($cal_data["interest"],2);
		// }
		// $data['cal_type_id'] = $cal_interest_close_account["type_id"];
		// $data['interest_rate']=$cal_interest_close_account['interest_rate'];
		
		echo json_encode($data);
	}
	
	function test_close_account_calculate(){
		//$date_interest = date('Y-m-d');
		$date_interest = '2020-03-02';
		$account_id = "11000001";
		$this->deposit_libraries->user_id = 'SYSTEM';
		$this->deposit_libraries->debug = true;
		$this->deposit_libraries->testmode = true;
		$data = $this->deposit_libraries->cal_deposit_interest_by_acc_date($account_id, $date_interest);
		echo"<pre>*** RETURN ***</pre>";
		echo"<pre>";print_r($data);echo"</pre>";
		echo"<pre>*** END RETURN ***</pre>";
	}

	public function update_transaction_balance(){
		$data = $this->input->post();
		$this->update_st->update_balance_statement($data);
		echo "success";
	}

	public function print_statement(){
		$arr_data = array();
		$real_account = $this->uri->segment(3);
		$account_id = $real_account;
		$arr_data['account_name'] = $this->db->get_where("coop_maco_account", array(
			"account_id" => $real_account
		))->result()[0]->account_name;

		$arr_data['account_id'] = $account_id;
		$this->libraries->template('save_money/print_statement',$arr_data);
	}

	public function statement_preview(){
		$this->load->helper('cookie');
		$arr_data = array();
		$data = $this->input->post();
		if(count($data)==0){
			$data['start_date'] = get_cookie('start_date');
			$data['end_date'] = get_cookie('end_date');
			$data['account_id'] = get_cookie('account_id');
			$data['select_type'] = get_cookie('select_type');
		}else{
			set_cookie("start_date", $data['start_date'], 600);
			set_cookie("end_date", $data['end_date'], 600);
			set_cookie("account_id", $data['account_id'], 600);
			set_cookie("select_type", $data['select_type'], 600);
		}


		$real_account = implode( "", explode("-", $data['account_id']) );
		$tmp_start_date = explode("/", $data['start_date']);
		$tmp_end_date = explode("/", $data['end_date']);
		
		$arr_data['account_id'] = $data['account_id'];

		$row_account = $this->db->get_where("coop_maco_account", array(
			"account_id" => $real_account
		))->row();
		$arr_data['account_name'] = $row_account->account_name; //ชื่อบัญชี
		$arr_data['member_name'] = $row_account->member_name; //ชื่อสมาชิก
		$arr_data['open_date'] = $row_account->created; //วันที่เปิดบัญชี
		$arr_data['member_id'] = $row_account->mem_id; //รหัสสมาชิก
		
		$arr_data['st_by_name'] = $this->db->get_where("coop_user", array(
			"user_id" => $_SESSION['USER_ID']
		))->result()[0]->user_name;
		if($data['select_type']=="all"){
		
			$this->db->select('coop_account_transaction.*,coop_user.user_name');
			$this->db->order_by("transaction_time, transaction_id");
			$this->db->join("coop_user","coop_account_transaction.user_id = coop_user.user_id","left");
			$arr_data['st'] = $this->db->get_where("coop_account_transaction", array(
				"transaction_time <= " => ($tmp_end_date[2]-543)."-".$tmp_end_date[1]."-".$tmp_end_date[0]." 23:59:59",
				"account_id" => $real_account
			))->result();
			
			$transaction_time = explode("-", explode(" ", $arr_data['st'][0]->transaction_time)[0]);
			$tmp_start_date[0] = $transaction_time[2];
			$tmp_start_date[1] = $transaction_time[1];
			$tmp_start_date[2] = $transaction_time[0]+543;
			
			$this->db->order_by("transaction_time DESC, transaction_id DESC");
			$this->db->limit(1);
			$arr_data['balance'] = $this->db->get_where("coop_account_transaction", array(
				"transaction_time <= " => ($tmp_end_date[2]-543)."-".$tmp_end_date[1]."-".$tmp_end_date[0]." 23:59:59",
				"account_id" => $real_account
			))->result()[0]->transaction_balance;
		}else{
			$this->db->select('coop_account_transaction.*,coop_user.user_name');
			$this->db->order_by("transaction_time, transaction_id");
			$this->db->join("coop_user","coop_account_transaction.user_id = coop_user.user_id","left");
			$arr_data['st'] = $this->db->get_where("coop_account_transaction", array(
				"transaction_time >= " => ($tmp_start_date[2]-543)."-".$tmp_start_date[1]."-".$tmp_start_date[0]." 00:00:00",
				"transaction_time <= " => ($tmp_end_date[2]-543)."-".$tmp_end_date[1]."-".$tmp_end_date[0]." 23:59:59",
				"account_id" => $real_account
			))->result();
	
			$this->db->order_by("transaction_time DESC, transaction_id DESC");
			$this->db->limit(1);
			$arr_data['balance'] = $this->db->get_where("coop_account_transaction", array(
				"transaction_time >= " => ($tmp_start_date[2]-543)."-".$tmp_start_date[1]."-".$tmp_start_date[0]." 00:00:00",
				"transaction_time <= " => ($tmp_end_date[2]-543)."-".$tmp_end_date[1]."-".$tmp_end_date[0]." 23:59:59",
				"account_id" => $real_account
			))->result()[0]->transaction_balance;
		}


		//echo $this->db->last_query(); exit;

		
		$this->db->join("coop_deposit_type_setting", "type_id");
		$arr_data['account_type'] = $this->db->get_where("coop_maco_account", array(
			"account_id" => $real_account
		))->result()[0]->type_name;
		
		$arr_data['start_date'] = $tmp_start_date[0]." ".$this->center_function->month_arr()[(int)$tmp_start_date[1]]." ".$tmp_start_date[2];
		$arr_data['end_date'] = $tmp_end_date[0]." ".$this->center_function->month_arr()[(int)$tmp_end_date[1]]." ".$tmp_end_date[2];
		
		//ดอกเบี้ยสะสม
		$arr_data['last_old_acc_int'] = $this->db->select(array('old_acc_int'))->from('coop_account_transaction')
									->where("account_id = '".$real_account."'")
									->order_by('transaction_time DESC, transaction_id DESC')
									->limit(1)->get()->row()->old_acc_int;							
		
		//จำนวนเงินที่เปิดบัญชี
		$arr_data['open_balance'] = $this->db->select(array('transaction_deposit'))->from('coop_account_transaction')
									->where("account_id = '".$real_account."' AND transaction_list IN ('CDO','OPT','OPN')")
									->limit(1)->get()->row()->transaction_deposit;
									
		if(@$_GET['download']!=''){
			$this->load->view("save_money/statement_preview", $arr_data);
		}else{
			$this->preview_libraries->template_preview("save_money/statement_preview" ,$arr_data);
		}
		
		
	}
	
	public function get_account_list_transfer(){
		$member_id = $this->input->post("member_id");
		echo $member_id;
		$temp_coop = $this->db->get_where("coop_maco_account", array("mem_id" => $member_id))->result();
	}

	public function remove_transaction(){
		$transcation_id = $this->uri->segment(3);
		$account_id = $this->uri->segment(4);
		if($transcation_id == ""){
			exit;
		}

		if($_SESSION['USER_ID']!=1){
			// var_dump($_SESSION);
			exit;
		}

		$transaction = $this->db->get_where("coop_account_transaction", array("transaction_id" => $transcation_id) )->result_array()[0];
		$this->db->where("transaction_id", $transcation_id);
		$this->db->delete("coop_account_transaction");

		
		$this->db->where("transaction_id", $transcation_id);
		$this->db->delete("coop_account_transaction_transfer");
		// var_dump($transaction);

		$this->update_st->update_deposit_transaction($transaction['account_id'], $transaction['transaction_time']);
		header("Location: ".base_url().'/save_money/account_detail?account_id='.$account_id );
	}

	function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", trim($text)); }

	private function GETVAR($key, $default = null, $prefix = null, $suffix = null) {
        return isset($_GET[$key]) ? $prefix . $_GET[$key] . $suffix : $prefix . $default . $suffix;
    }

	public function print_slip_deposit(){
		$transaction_id = $this->uri->segment(3);

		$this->db->select(array("t1.*", "CONCAT(t4.prename_short,t2.firstname_th, ' ', lastname_th) as fullname", "t5.user_name"));
		$this->db->join("coop_maco_account as t3", "t3.account_id = t1.account_id", "inner");
		$this->db->join("coop_mem_apply as t2", "t2.member_id = t3.mem_id", "left");
		$this->db->join("coop_prename as t4", "t4.prename_id = t2.prename_id", "left");
		$this->db->join("coop_user as t5", "t5.user_id = t1.user_id", "left");
		$transaction = $this->db->get_where("coop_account_transaction as t1", array(
			"transaction_id" => $transaction_id
		))->result_array()[0];
		
		$transaction['transaction_time'] = $this->center_function->mydate2date($transaction['transaction_time'], true);
		$transaction['method'] = ($transaction['transaction_withdrawal']!=0) ? $transaction['transaction_withdrawal'] : $transaction['transaction_deposit'];
		// var_dump($transaction);
		
		$account_id = @$this->center_function->format_account_number($transaction['account_id']);
		$font = $this->GETVAR('font','fontawesome-webfont1','','.php');
		$pdf = new FPDF('P','mm','A4');	
		$pdf->AddPage();
		$pdf->AddFont('THSarabunNew','','THSarabunNew.php');
		$pdf->AddFont('THSarabunNewB','','THSarabunNew-Bold.php');
			
		$pdf->SetFont('THSarabunNew','',14);
		$pdf->setY(8);
		$pdf->Cell( 0 , 14 , $transaction['transaction_time']."   ".
		$account_id."    ".
		$this->U2T($transaction['fullname'])."    ".
		$transaction['transaction_list']."    ".
		number_format($transaction['method'],2)."    ".
		$this->U2T(""), 0,0,'C' );	
		$pdf->Output();
	}

	public function authen_confirm_err_transaction(){
		if(empty($_SESSION['USER_ID']))
			header('HTTP/1.1 500 Internal Server Error');

		$user = $this->input->post("confirm_user");
		$password = $this->input->post("confirm_pwd");
		
		$user_db = $this->db->get_where("coop_user", array(
			"username" => $user,
			"password" => $password
		))->result()[0];
		if($user_db){
			$permission = $this->db->get_where("coop_user_permission", array(
				"user_id" => $user_db->user_id,
				"menu_id" => 230,//ยกเลิกรายการ
			));
			echo json_encode(array("result" => "true", "permission" => ($permission->result() || $_SESSION['USER_ID']==1) ? "true" : "false" ));
		}else{
			echo json_encode(array("result" => "false"));
		}
	}

	public function authen_confirm_user(){
		if(empty($_SESSION['USER_ID']))
			header('HTTP/1.1 500 Internal Server Error');

		$user = $this->input->post("confirm_user");
		$password = $this->input->post("confirm_pwd");
		$menu_id = $this->input->post("permission_id");
		
		$user_db = $this->db->get_where("coop_user", array(
			"username" => $user,
			"password" => $password,
			"user_status" => 1
		))->result()[0];
		if($user_db){
			$permission = $this->db->get_where("coop_user_permission", array(
				"user_id" => $user_db->user_id,
				"menu_id" => $menu_id,//เมนูสิทธิ์
			))->result_array();
			echo json_encode(array("result" => "true", "permission" => ($permission || $_SESSION['USER_ID']==1 || $user_db->user_type_id==1) ? "true" : "false", "user_id" => $user_db->user_id, "sql" => $this->db->last_query() ) );
		}else{
			echo json_encode(array("result" => "false"));
		}
	}

	public function hold_withdraw(){
        if($this->center_function->withdraw_permission($_GET['account'])){
            echo "TRUE";
        }else{
            echo "FALSE";
        }
    }

    function test_interest_calculate(){
        $date_interest = date('Y-m-d');
        //$date_interest = '2018-11-29';
        $account_id = "0001000471";
        $this->deposit_libraries->user_id = 'SYSTEM';
        $this->deposit_libraries->debug = true;
        $this->deposit_libraries->testmode = true;
        $data = $this->deposit_libraries->cal_deposit_interest_by_acc_date($account_id, $date_interest);
        echo"<pre>*** RETURN ***</pre>";
        echo"<pre>";print_r($data);echo"</pre>";
        echo"<pre>*** END RETURN ***</pre>";
    }
	
	function cal_interest() {
		$arr_data["data"] = [
			"start_date" => date("d/m/").(date("Y") + 543),
			"end_date" => date("d/m/").(date("Y") + 543),
			"time" => "05:00"
		];
		
		$this->db->select(array('t1.type_id','t1.type_name','t1.type_code'));
		$this->db->from('coop_deposit_type_setting as t1');
		$row = $this->db->get()->result_array();
		$arr_data['type_id'] = $row;
		
		$this->libraries->template('save_money/cal_interest', $arr_data);
	}
	
	function cal_interest_process() {
		$type_id = $_POST["type_id"];
		$acc_id = $_POST["acc_id"];
		$time = $_POST["time"];

		$start_date = $_POST["start_date"];
		$start_date_arr = explode('/', $start_date);
		$start_day = $start_date_arr[0];
		$start_month = $start_date_arr[1];
		$start_year = $start_date_arr[2];
		$start_year -= 543;
		$start_date = $start_year.'-'.$start_month.'-'.$start_day.' '.$time;

		$end_date = $_POST["end_date"];
		$end_date_arr = explode('/', $end_date);
		$end_day = $end_date_arr[0];
		$end_month = $end_date_arr[1];
		$end_year = $end_date_arr[2];
		$end_year -= 543;
		$end_date = $end_year.'-'.$end_month.'-'.$end_day.' '.$time;

		$where = "";
		if(!empty($type_id)) {
			$where .= " AND type_id = '{$type_id}'";
		}
		if(!empty($acc_id)) {
			$where .= " AND account_id = '{$acc_id}'";
		}

		$this->db->select(array(
			'account_id',
			'type_id',
			'mem_id',
			'created as create_account_date'
		));
		$this->db->from('coop_maco_account');
		$this->db->where("account_status = '0'".$where);
		$rs_member = $this->db->get()->result_array();
		//echo '<pre>'; print_r($rs_member); echo '</pre>'; exit;

		$rs_date = $this->db->query("SELECT DATEDIFF('{$end_date}', '{$start_date}') AS date_count");
		$row_date = $rs_date->row_array();
		for($i = 0; $i <= $row_date["date_count"]; $i++) {
			$rs_date2 = $this->db->query("SELECT DATE_ADD('".$start_date."', INTERVAL ".$i." DAY) AS date_cal");
			$row_date2 = $rs_date2->row_array();

			foreach($rs_member as $key_member => $row_member){
				$this->deposit_libraries->user_id = 'SYSTEM';
				//$this->debug = true;
				//$this->testmode = true;
				$this->deposit_libraries->cal_deposit_interest($row_member, 'cal_interest', $row_date2["date_cal"], date("d", strtotime($row_date2["date_cal"])), '');
			}
		}

		echo json_encode(["result" => "true"]);
	}
	
	//รันยอดส่งหักรายเดือน ตามการครบกำหนดบัญชีเงินฝาก
	function run_deposit_month_due() {
		$this->load->model("Deposit_month_model", "deposit_month");
		//$date_now = '2020-07-02';
		$date_now = date('Y-m-d');
		$result = $this->deposit_month->insert_deposit_month($date_now);
		echo $result;
		exit;
	}

    function saving_slip_pdf_customize($account_id,$transaction_id,$style_id=''){
        $arr_data = array();
        $account_id = $this->center_function->decrypt_text(jwt::urlsafeB64Decode($account_id));
        $transaction_id = $this->center_function->decrypt_text(jwt::urlsafeB64Decode($transaction_id));
        $style_id = $this->center_function->decrypt_text(jwt::urlsafeB64Decode($style_id));

        //check style
        $this->db->select(array('transaction_deposit','transaction_withdrawal'));
        $this->db->from('coop_account_transaction');
        $this->db->where("account_id = '".$account_id."' AND transaction_id = '".$transaction_id."'");
        $row_chk = $this->db->get()->row_array();
        if($row_chk['transaction_deposit'] != 0){
            $style_id = 1;
        }else{
            $style_id = 2;
        }

        $arr_data['account_id'] = $account_id;
        $arr_data['transaction_id'] = $transaction_id;
        $arr_data['style_id'] = $style_id;

        $row_department = $this->db->select("t1.mem_id , t3.mem_group_name AS department1,    t4.mem_group_name AS department2,    t5.mem_group_name AS department3")
                                    ->from('coop_maco_account AS t1')
                                    ->join("coop_mem_apply AS t2","t1.mem_id = t2.member_id","left")
                                    ->join("coop_mem_group AS t3","t2.department = t3.id","left")
                                    ->join("coop_mem_group AS t4","t2.faction = t4.id","left")
                                    ->join("coop_mem_group AS t5","t2.level = t5.id","left")
                                    ->where("t1.account_id = '".$account_id."'")
                                    ->get()->row_array();

        $style = $this->db->get_where("coop_saving_slip_style", array(
            "style_id" => $style_id
        ))->result_array()[0];
        $arr_data['style'] = $style;

        $rows = $this->db->get_where("coop_saving_slip_style_setting", array(
            "style_id" => $style_id
        ))->result_array();

        foreach ($rows as $key => $value) {
            $meta = $value['style_value'];
            $text = $meta;
            if($meta == "[account_name]"){
                $this->db->select(array('account_name'));
                $this->db->from('coop_maco_account');
                $this->db->where("account_id = '".$account_id."'");
                $row = $this->db->get()->result_array();
                $text = $row[0]['account_name'];
            }

            if($meta == "[account_id]"){
                $account_id_arr = str_split($account_id, 1);
                $account_id_format = '';
                foreach($account_id_arr as $k => $val){
                    if($k == '1' || $k == '4' || $k == '5')
                        $account_id_format .= ' - '.$val;
                    else
                        $account_id_format .= $val;
                }
                $text = $account_id_format;
            }

            if($meta == "[date_now]"){
                $text = date("d/m/").(date("Y")+543);
            }
            if($meta == "[department1]"){
                $text = $row_department['department1'];
            }
            if($meta == "[department2]"){
                $text = $row_department['department2'];
            }
            if($meta == "[department3]"){
                $text = $row_department['department3'];
            }
            if($meta == "[deposit]"){
                $this->db->select(array('transaction_deposit'));
                $this->db->from('coop_account_transaction');
                $this->db->where("account_id = '".$account_id."' AND transaction_id = '".$transaction_id."'");
                $row = $this->db->get()->row_array();
                $text = number_format($row['transaction_deposit'],2);
            }
            if($meta == "[deposit_text]"){
                $this->db->select(array('transaction_deposit'));
                $this->db->from('coop_account_transaction');
                $this->db->where("account_id = '".$account_id."' AND transaction_id = '".$transaction_id."'");
                $row = $this->db->get()->row_array();
                $text = $this->center_function->convert($row['transaction_deposit']);
            }
            if($meta == "[withdrawal]"){
                $this->db->select(array('transaction_withdrawal'));
                $this->db->from('coop_account_transaction');
                $this->db->where("account_id = '".$account_id."' AND transaction_id = '".$transaction_id."'");
                $row = $this->db->get()->row_array();
                $text = number_format($row['transaction_withdrawal'],2);
            }
            if($meta == "[withdrawal_text]"){
                $this->db->select(array('transaction_withdrawal'));
                $this->db->from('coop_account_transaction');
                $this->db->where("account_id = '".$account_id."' AND transaction_id = '".$transaction_id."'");
                $row = $this->db->get()->row_array();
                $text = $this->center_function->convert($row['transaction_withdrawal']);
            }
            if($meta == "[date_transaction]"){
                $this->db->select(array('transaction_time'));
                $this->db->from('coop_account_transaction');
                $this->db->where("account_id = '".$account_id."' AND transaction_id = '".$transaction_id."'");
                $row = $this->db->get()->row_array();
                $text = $this->center_function->mydate2date($row['transaction_time']);
            }

            $rows[$key]['text'] = $text;
        }
        $arr_data['rows'] = $rows;
        $this->load->view('save_money/saving_slip_pdf_customize',$arr_data);
    }
	
	public function check_time_transaction(){
		$account = $_POST["account"];
		$date_transaction = ($_POST["date_transaction"] != '')?$this->center_function->ConvertToSQLDate($_POST["date_transaction"]):date('Y-m-d');
		$arr_time = $this->deposit_modal->get_time_transaction($account,$date_transaction);
		$time_now = date("H:i:s");
		$arr_time['time_last'] = ($arr_time['time_last'] > $time_now)?$arr_time['time_last']:$time_now;
		echo json_encode($arr_time);
	}

	public function test(){
		echo '<pre>'; print_r($this->input->post()); echo '</pre>'; exit;
	}

	public function inline_update(){
		header('Content-Type: application/json');
		// echo "<pre>";print_r($_POST);exit;
		$token = $_POST['token'];
		if($token=="") die();
		$authen_token = sha1(md5(@$_POST['transaction_id']));
		if($authen_token != $token) die();

		//permission
		$this->db->select('user_permission_id');
		$this->db->from('coop_user_permission');
		$this->db->where("user_id = '".$_SESSION['USER_ID']."' AND menu_id = '324'");
		$row = $this->db->get()->result_array();
		if($row[0]['user_permission_id']==''){
			json_encode(array("result" => false, "message" => "No permission"));
		}

		$this->db->select("coop_maco_account.*, coop_account_transaction.transaction_time");
		$this->db->join("coop_maco_account", "coop_account_transaction.account_id = coop_maco_account.account_id", "inner");
		$account = $this->db->get_where("coop_account_transaction", array(
			"transaction_id" => $_POST['transaction_id']
		))->row_array();

		$method = @$_POST['method'];
		if($method=="transaction_time"){
			$tmp_date				= explode("/", $_POST['inline_date']);
			$tmp_time				= ($_POST['inline_time'] != '')?$_POST['inline_time']:"00:00:00";
			$transaction_id 		= $_POST['transaction_id'];
			$inline_date 			= ($tmp_date[2]-543) . "-" . $tmp_date[1] . "-" . $tmp_date[0]." ".$tmp_time;
			$status					= true;				
			if(checkdate($tmp_date[1], $tmp_date[0], ($tmp_date[2]-543))){
				$this->db->set("transaction_time", $inline_date);
				$this->db->where("transaction_id", $transaction_id);
				$this->db->update("coop_account_transaction");
				$message = $this->center_function->ConvertToThaiDate($inline_date,1,1,1);
				$this->update_st->update_deposit_transaction($account['account_id'], ($tmp_date[2]-543) . "-" . $tmp_date[1] . "-" . $tmp_date[0]." 00:00:00");
			}else{
				$status = false;
				$message = "FAIL!";
			}
		}else if($method=="transaction_withdrawal"){
			$transaction_id 		= $_POST['transaction_id'];
			$transaction_withdrawal = implode("", explode(",", $_POST['inline_withdrawal']));
			$status					= true;
			if(is_numeric($transaction_withdrawal)){
				$this->db->set("transaction_withdrawal", $transaction_withdrawal);
				$this->db->where("transaction_id", $transaction_id);
				$this->db->update("coop_account_transaction");
				$message = $_POST['inline_withdrawal'];
				$this->update_st->update_deposit_transaction($account['account_id'], $account['transaction_time']);
			}else{
				$status = false;
				$message = "FAIL!";
			}
		}else if($method=="transaction_deposit"){
			$transaction_id 		= $_POST['transaction_id'];
			$transaction_deposit 	= implode("", explode(",", $_POST['inline_deposit']));
			$status					= true;
			if(is_numeric($transaction_deposit)){
				$this->db->set("transaction_deposit", $transaction_deposit);
				$this->db->where("transaction_id", $transaction_id);
				$this->db->update("coop_account_transaction");
				$message = $_POST['inline_deposit'];
				$this->update_st->update_deposit_transaction($account['account_id'], $account['transaction_time']);
			}else{
				$status = false;
				$message = "FAIL!";
			}
		}else if($method=="transaction_balance"){
			$transaction_id 		= $_POST['transaction_id'];
			$transaction_balance 	= implode("", explode(",", $_POST['inline_balance']));
			$status					= true;
			if(is_numeric($transaction_balance)){
				$this->db->set("transaction_balance", $transaction_balance);
				$this->db->where("transaction_id", $transaction_id);
				$this->db->update("coop_account_transaction");
				$message = $_POST['inline_balance'];
				$this->update_st->update_deposit_transaction($account['account_id'], $account['transaction_time']);
			}else{
				$status = false;
				$message = "FAIL!";
			}
		}else if($method=="accu_int_item"){
			$transaction_id 		= $_POST['transaction_id'];
			$accu_int_item 	= implode("", explode(",", $_POST['inline_accu_int']));
			$status					= true;
			if(is_numeric($accu_int_item)){
				$this->db->set("accu_int_item", $accu_int_item);
				$this->db->where("transaction_id", $transaction_id);
				$this->db->update("coop_account_transaction");
				$message = $_POST['inline_accu_int'];
				$this->update_st->update_deposit_transaction($account['account_id'], $account['transaction_time']);
			}else{
				$status = false;
				$message = "FAIL!";
			}
		}else if($method=="old_acc_int"){
			$transaction_id 		= $_POST['transaction_id'];
			$old_acc_int 	= implode("", explode(",", $_POST['inline_old_acc_int']));
			$status					= true;
			if(is_numeric($old_acc_int)){
				$this->db->set("old_acc_int", $old_acc_int);
				$this->db->where("transaction_id", $transaction_id);
				$this->db->update("coop_account_transaction");
				$message = $_POST['inline_old_acc_int'];
				$this->update_st->update_deposit_transaction($account['account_id'], $account['transaction_time']);
			}else{
				$status = false;
				$message = "FAIL!";
			}
		}

		echo json_encode(array("result" => $status, "message" => $message));
	}

	function ajax_get_bank_branch(){
		$bank_id = isset($_POST['bank_id']) ? trim(@$_POST['bank_id']) : "";
		//ข้อมูลบัญชีธนาคาร
		$data = $this->deposit_modal->get_coop_bank_branch($bank_id);
		// echo '<pre>'; print_r($data); echo '</pre>';
		if(!empty($data)){
			echo json_encode($data);
		}else{
			echo 'not_found';
		}
		exit;
	}
}
