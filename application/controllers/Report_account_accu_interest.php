<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_account_accu_interest extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		 $this->load->model('Memgroup_model');
	}
	
	public function coop_report_accu_interest(){
		$arr_data = array();		
		if($_GET['member_id']!=''){
			$member_id = $_GET['member_id'];
		}else{
			$member_id = '';
		}
		$arr_data['member_id'] = $member_id;

		$member_name = '';
		if($member_id != '') {
			$member_info = $this->db->select(array('t1.member_id', 't1.firstname_th', 't1.lastname_th', 't2.prename_full'))
									->from('coop_mem_apply as t1')
									->join("coop_prename as t2","t2.prename_id = t1.prename_id","left")
									->where("t1.member_id = '{$member_id}'")
									->get()->row();
			$member_name = $member_info->prename_full.$member_info->firstname_th." ".$member_info->lastname_th;
		}

		$arr_data['member_name'] = $member_name;
		$this->libraries->template('report_account_accu_interest/coop_report_accu_interest',$arr_data);
	}
	
	function coop_report_accu_interest_preview(){
		$arr_data = array();	
		$arr_data['month_arr'] = $this->month_arr;
		$arr_data['month_short_arr'] = $this->month_short_arr;
		$arr_data["data"] = $this->get_data_report_accu_interest($_GET);
		$arr_data["page_all"] = count($arr_data["data"]);
		$this->preview_libraries->template_preview('report_account_accu_interest/coop_report_accu_interest_preview',$arr_data);
		
	}
	
	public function get_data_report_accu_interest($data) {
		if($data['start_date']){
			$start_date = $this->center_function->ConvertToSQLDate($data['start_date']);
		}
		
		if($data['end_date']){
			$end_date = $this->center_function->ConvertToSQLDate($data['end_date']);
		}
		
		$where_date = "";
		if(!empty($data['start_date']) && empty($data['end_date'])) {
			$where_date = " AND t1.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(!empty($data['start_date']) && !empty($data['end_date'])) {
			$where_date = " AND t1.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}
		
		$where_account = "";
		if(!empty($data['start_account_id']) && empty($data['end_account_id'])) {
			$where_account = " AND account_id BETWEEN '".$data['start_account_id']."' AND '".$data['start_account_id']."' ";
		}else if(!empty($data['start_account_id']) && !empty($data['end_account_id'])) {
			$where_account = " AND account_id BETWEEN '".$data['start_account_id']."' AND '".$data['end_account_id']."' ";
		}

		$data_account = $this->db->select("mem_id AS member_id ,account_id,account_name,created")
											->from("coop_maco_account")
											->where("mem_id = '".$data["member_id"]."' {$where_account}")
											->order_by("account_id ASC")
											->get()->result_array();
		$page = 1;
		foreach($data_account AS $val) {
			$row_transaction = $this->db->select("t1.transaction_time,
													t1.transaction_list,
													t1.transaction_withdrawal,
													t1.transaction_deposit,
													t1.transaction_balance,
													t1.old_acc_int,
													t1.user_id, 
													IF(t2.user_name IS NULL,t1.user_id,t2.user_name) AS user_name")
											->from("coop_account_transaction AS t1")
											->join("coop_user AS t2","t1.user_id = t2.user_id","left")
											->where("t1.account_id = '".$val["account_id"]."' {$where_date} ")
											->order_by("t1.transaction_time ASC")
											->get()->result_array();
			if(!empty($row_transaction)){
				$results[$page]['account'] = $val;
				$results[$page]['account']['level_name'] = $this->Memgroup_model->get_department_member($val["member_id"],$start_date)['level_name'];
				$results[$page]['transaction'] = $row_transaction;
				$page++;
			}			
		}
		return $results;
	}
	
	function check_report_accu_interest(){
		$rs_count = $this->get_data_report_accu_interest($_POST);
		if(!empty($rs_count)){
			echo "success";
		}else{
			echo "";
		}		
	}
	
	
}
