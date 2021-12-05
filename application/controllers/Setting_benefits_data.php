<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_benefits_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

	}
	
	
	public function benefits_type() {
		$arr_data = array();
		$id = @$_GET['id'];
		$act = @$_GET['act'];
		$detail_id = @$_GET['detail_id'];
		if(!empty($id)){
			$this->db->select(array('benefits_name'));
			$this->db->from('coop_benefits_type');
			$this->db->where("benefits_id = '".$id."'");
			$rs = $this->db->get()->result_array();
			$arr_data['benefits_type'] = @$rs[0];

			if($act == 'detail'){
				$this->db->select(array('*'));
				$this->db->from('coop_benefits_type_detail');
				$this->db->where("benefits_id = '".$id."'");
				$this->db->order_by("start_date DESC");
				$rs_detail = $this->db->get()->result_array();
				$arr_data['rs_detail'] = @$rs_detail;
			}else{
				$this->db->select(array('*'));
				$this->db->from('coop_benefits_type_detail');
				$this->db->where("id = '".$detail_id."'");
				$rs = $this->db->get()->result_array();
				$arr_data['row'] = @$rs[0];

				$type_choices = $this->db->select("*")->from("coop_benefits_type_choice")->where("benefit_detail_id = '".$detail_id."' AND status = 1")->get()->result_array();
				$multi_choices = array();
				foreach($type_choices as $choice) {
					if(!empty($choice['name'])) {
						$multi_choices[$choice['key']][$choice['id']]['name'] = $choice['name'];
						$multi_choices[$choice['key']][$choice['id']]['amount'] = $choice['amount'];
					} else {
						$multi_choices[$choice['key']]['amount'] = $choice['amount'];
					}
				}
				$arr_data['multi_choices'] = $multi_choices;
			}
		}else{
			$x=0;
			$join_arr = array();

			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_benefits_type');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('benefits_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}

		// echo "<pre>"; print_r($arr_data); exit;
		$this->libraries->template('setting_benefits_data/benefits_type',$arr_data);
	}

	public function benefits_type_save()
	{
		$data_insert = array();			
		$data_insert['benefits_name']    = @$_POST["benefits_name"];
		$data_insert['start_date']    = $this->center_function->ConvertToSQLDate(@$_POST["start_date"]);
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$id_edit = @$_POST["benefits_id"] ;
		$table = "coop_benefits_type";

		if (empty($id_edit)) {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('benefits_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_benefits_data/benefits_type' </script>"; 

	}
	
	public function benefits_type_detail_save() {
		$data_insert = array();	
		$id = @$_POST["id"] ;
		$id_edit = @$_POST["detail_id"];
		$type_add = @$_POST["type_add"];
		$table = "coop_benefits_type_detail";
		$process_datetime = date('Y-m-d H:i:s');

		$data_insert['benefits_id'] = @$id;
		$data_insert['benefits_detail'] = @$_POST["benefits_detail"];
		$data_insert['start_date'] = $this->center_function->ConvertToSQLDate(@$_POST["start_date"]);
		$data_insert['updatetime'] = date('Y-m-d H:i:s');

		$data_insert['age_grester'] = $_POST["age_val"];
		$data_insert['age_grester_status'] = !empty($_POST["age_grester_select"]) ? 1 : 0;
		$data_insert['member_age_grester'] = $_POST["member_age_val"];
		$data_insert['member_age_grester_status'] = !empty($_POST["member_age_grester_select"]) ? 1 : 0;
		$data_insert['work_age_grester'] = $_POST["work_age_val"];
		$data_insert['work_age_grester_status'] = !empty($_POST["work_age_grester_select"]) ? 1 : 0;
		$data_insert['request_time'] = $_POST["req_time_val"];
		$data_insert['request_time_status'] = !empty($_POST["req_time"]) ? 1 : 0;
		$data_insert['request_time_unit'] = $_POST["req_time_type"];
		$data_insert['special_con_selected'] = $_POST["sp_condition"];
		$data_insert['payment_receive'] = str_replace( ',', '',$_POST["payment_receive"]);
		$data_insert['retire_member_receive'] = str_replace( ',', '',$_POST["retire_member_receive"]);
		$data_insert['new_heir_receive'] = str_replace( ',', '',$_POST["new_heir_receive"]);
		$data_insert['pass_away_default_receive'] = str_replace( ',', '',$_POST["pass_away_benefit"]);
		$data_insert['pass_away_year_1'] = $_POST["pass_away_year_1"];
		$data_insert['pass_away_year_2'] = $_POST["pass_away_year_2"];
		$data_insert['pass_away_year_3'] = $_POST["pass_away_year_3"];
		$data_insert['pass_away_year_4'] = $_POST["pass_away_year_4"];
		$data_insert['pass_away_year_5'] = $_POST["pass_away_year_5"];
		$data_insert['pass_away_year_last'] = $_POST["pass_away_year_last"];
		$data_insert['pass_away_receive_1'] = str_replace( ',', '',$_POST["pass_away_receive_1"]);
		$data_insert['pass_away_receive_2'] = str_replace( ',', '',$_POST["pass_away_receive_2"]);
		$data_insert['pass_away_receive_3'] = str_replace( ',', '',$_POST["pass_away_receive_3"]);
		$data_insert['pass_away_receive_4'] = str_replace( ',', '',$_POST["pass_away_receive_4"]);
		$data_insert['pass_away_receive_5'] = str_replace( ',', '',$_POST["pass_away_receive_5"]);
		$data_insert['pass_away_receive_last'] = str_replace( ',', '',$_POST["pass_away_receive_last"]);
		$data_insert['scholarship_kindergarten'] = str_replace( ',', '',$_POST["scholarship_kindergarten"]);
		$data_insert['scholarship_elementary'] = str_replace( ',', '',$_POST["scholarship_elementary"]);
		$data_insert['scholarship_junior_high'] = str_replace( ',', '',$_POST["scholarship_junior_high"]);
		$data_insert['scholarship_senior_high'] = str_replace( ',', '',$_POST["scholarship_senior_high"]);
		$data_insert['scholarship_bachelor'] = str_replace( ',', '',$_POST["scholarship_bachelor"]);
		$data_insert['scholarship_period_date_start'] = $_POST["scholarship_period_date_start"];
		$data_insert['scholarship_period_month_start'] = $_POST["scholarship_period_month_start"];
		$data_insert['atm_coop_selected'] = $_POST["atm_coop_selected"];
		$data_insert['atm_coop_max_receive'] = str_replace( ',', '',$_POST["atm_coop_max_receive"]);
		$data_insert['atm_coop_pass_away'] = str_replace( ',', '',$_POST["atm_coop_pass_away"]);
		$data_insert['atm_coop_tpd'] = str_replace( ',', '',$_POST["atm_coop_tpb"]);
		$data_insert['atm_coop_dismemberment_selected'] = $_POST["atm_coop_dismemberment"];
		$data_insert['atm_coop_e'] = str_replace( ',', '',$_POST["atm_coop_e"]);
		$data_insert['atm_coop_dismember_sub_selected'] = $_POST["atm_coop_dismember_sub_selected"];
		$data_insert['atm_coop_hhffee'] = str_replace( ',', '',$_POST["atm_coop_hhffee"]);
		$data_insert['atm_coop_hf'] = str_replace( ',', '',$_POST["atm_coop_hf"]);
		$data_insert['atm_coop_he'] = str_replace( ',', '',$_POST["atm_coop_he"]);
		$data_insert['atm_coop_fe'] = str_replace( ',', '',$_POST["atm_coop_fe"]);
		$data_insert['atm_coop_h'] = str_replace( ',', '',$_POST["atm_coop_h"]);
		$data_insert['atm_coop_f'] = str_replace( ',', '',$_POST["atm_coop_f"]);
		$data_insert['cremation_receive'] = str_replace( ',', '',$_POST["cremation_receive"]);
		$data_insert['cre_year_last'] = str_replace(',', '', $_POST['cre_year_last']);
		$data_insert['cre_receive_last'] = str_replace(',', '', $_POST['cre_receive_last']);
		$data_insert['tre_i_day_max'] = str_replace(',', '', $_POST['tre_i_day_limit']);
		$data_insert['tre_i_year_last'] = str_replace(',', '', $_POST['tre_i_year_last']);
		$data_insert['tre_i_receive_last'] = str_replace(',', '', $_POST['tre_i_receive_last']);
		$data_insert['tre_o_year_last'] = str_replace(',', '', $_POST['tre_o_year_last']);
		$data_insert['tre_o_receive_last'] = str_replace(',', '', $_POST['tre_o_receive_last']);

		if($type_add == 'add'){
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);	
			$id_edit = $this->db->insert_id();
		}else{
			$this->db->where('id', $id_edit);
			$this->db->update($table, $data_insert);	
		}

		// Insert data for multi choices.
		$data_update = array();
		$data_update['status'] = 2;
		$this->db->where('benefit_detail_id', $id_edit);
		$this->db->update('coop_benefits_type_choice', $data_update);

		if($_POST['sp_condition'] == "pass_away_default_receive") {
			$pass_away_choice_inserts = array();
			foreach($_POST['pass_away_year'] as $key => $year) {
				if($year != "") {
					$amount = str_replace( ',', '',$_POST['pass_away_receive'][$key]);

					$pass_away_choice_insert = array();
					$pass_away_choice_insert['benefit_detail_id'] = $id_edit;
					$pass_away_choice_insert['key'] = 'pass_away';
					$pass_away_choice_insert['name'] = $year;
					$pass_away_choice_insert['amount'] = $amount;
					$pass_away_choice_insert['status'] = 1;
					$pass_away_choice_insert['user_id'] = $_SESSION['USER_ID'];
					$pass_away_choice_insert['created_at'] = date('Y-m-d H:i:s');
					$pass_away_choice_insert['updated_at'] = date('Y-m-d H:i:s');
					$pass_away_choice_inserts[] = $pass_away_choice_insert;
				}
			}

			if (!empty($pass_away_choice_inserts)) {
				$this->db->insert_batch('coop_benefits_type_choice', $pass_away_choice_inserts);
			}
		} else if ($_POST['sp_condition'] == "cremation_receive") {
			$cre_inserts = array();
			foreach($_POST['cre_receive_year'] as $key => $year) {
				if($year != "") {
					$amount = str_replace( ',', '',$_POST['cre_receive_receive'][$key]);
					$cre_insert = array();
					$cre_insert['benefit_detail_id'] = $id_edit;
					$cre_insert['key'] = 'cre_r';
					$cre_insert['name'] = $year;
					$cre_insert['amount'] = $amount;
					$cre_insert['status'] = 1;
					$cre_insert['user_id'] = $_SESSION['USER_ID'];
					$cre_insert['created_at'] = date('Y-m-d H:i:s');
					$cre_insert['updated_at'] = date('Y-m-d H:i:s');
					$cre_inserts[] = $cre_insert;
				}
			}

			if (!empty($cre_inserts)) {
				$this->db->insert_batch('coop_benefits_type_choice', $cre_inserts);
			}
		} else if ($_POST['sp_condition'] == "treat_receive") {
			$cre_inserts = array();
			foreach($_POST['tre_i_year'] as $key => $year) {
				if($year != "") {
					$amount = str_replace( ',', '',$_POST['tre_i_receive'][$key]);
					$cre_insert = array();
					$cre_insert['benefit_detail_id'] = $id_edit;
					$cre_insert['key'] = 'tre_i';
					$cre_insert['name'] = $year;
					$cre_insert['amount'] = $amount;
					$cre_insert['status'] = 1;
					$cre_insert['user_id'] = $_SESSION['USER_ID'];
					$cre_insert['created_at'] = date('Y-m-d H:i:s');
					$cre_insert['updated_at'] = date('Y-m-d H:i:s');
					$cre_inserts[] = $cre_insert;
				}
			}
			foreach($_POST['tre_o_year'] as $key => $year) {
				if($year != "") {
					$amount = str_replace( ',', '',$_POST['tre_o_receive'][$key]);
					$cre_insert = array();
					$cre_insert['benefit_detail_id'] = $id_edit;
					$cre_insert['key'] = 'tre_o';
					$cre_insert['name'] = $year;
					$cre_insert['amount'] = $amount;
					$cre_insert['status'] = 1;
					$cre_insert['user_id'] = $_SESSION['USER_ID'];
					$cre_insert['created_at'] = date('Y-m-d H:i:s');
					$cre_insert['updated_at'] = date('Y-m-d H:i:s');
					$cre_inserts[] = $cre_insert;
				}
			}

			if (!empty($cre_inserts)) {
				$this->db->insert_batch('coop_benefits_type_choice', $cre_inserts);
			}
		} else if ($_POST['sp_condition'] == "disa") {
			$cre_inserts = array();
			foreach($_POST['disa'] as $key=> $amount) {
				$cre_insert = array();
				$cre_insert['benefit_detail_id'] = $id_edit;
				$cre_insert['key'] = $key;
				$cre_insert['name'] = NULL;
				$cre_insert['amount'] = str_replace( ',', '',$amount);
				$cre_insert['status'] = 1;
				$cre_insert['user_id'] = $_SESSION['USER_ID'];
				$cre_insert['created_at'] = date('Y-m-d H:i:s');
				$cre_insert['updated_at'] = date('Y-m-d H:i:s');
				$cre_inserts[] = $cre_insert;
			}

			if (!empty($cre_inserts)) {
				$this->db->insert_batch('coop_benefits_type_choice', $cre_inserts);
			}
		}

		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/setting_benefits_data/benefits_type?act=detail&id={$id}' </script>"; 
	}
	
	function del_coop_data(){	
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
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
		
	}
	
	function check_benefits_type_detail(){	
		$id = @$_POST['id'];
		$this->db->select(array('*'));
		$this->db->from('coop_benefits_type_detail');
		$this->db->where("benefits_id = '{$id}'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		if(@$row['benefits_id']){
			echo false;
		}else{
			echo true;
		}		
		exit;
	}
	
	function check_date_detail(){
		$start_date = $this->center_function->ConvertToSQLDate(@$_POST["start_date"]);
		$id = @$_POST["id"];
		$detail_id = @$_POST["detail_id"];
		
		if(@$detail_id){
			$where = " AND id <> {$detail_id}";
		}else{
			$where = "";
		}
		
		$this->db->select(array('*'));
		$this->db->from('coop_benefits_type_detail');
		$this->db->where("start_date = '{$start_date}' AND benefits_id = '{$id}' {$where}");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0]; 
	
		if(@$row['start_date']){
			echo false;
		}else{
			echo true;
		}
		exit;
	}

}
