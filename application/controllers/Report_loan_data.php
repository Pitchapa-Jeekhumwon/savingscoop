<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_loan_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('report_loan_data_model');
		$this->load->model("Report_loan_person_model");
	}
	
	public function coop_report_loan(){
		$arr_data = array();
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$this->db->order_by('order_by ASC');
		$rs_loan_type = $this->db->get()->result_array();
		
		$loan_type = array();
		if(!empty($rs_loan_type)){
			foreach($rs_loan_type as $key => $row_loan_type){
				$loan_type[$row_loan_type['id']] = @$row_loan_type['loan_type'];
			}
		}
		$arr_data['loan_type'] = $loan_type;
		$this->libraries->template('report_loan_data/coop_report_loan',$arr_data);
	}
	
	function check_report_loan(){
		if(@$_POST['report_date'] != ''){
			$date_arr = explode('/',@$_POST['report_date']);
			$day = (int)@$date_arr[0];
			$month = (int)@$date_arr[1];
			$year = (int)@$date_arr[2];
			$year -= 543;
			$s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
			$e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
			$where = " AND t1.createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
		}else{
			if(@$_POST['month']!='' && @$_POST['year']!=''){
				$day = '';
				$month = $_POST['month'];
				$year = ($_POST['year']-543);
				$s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
				$e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
				$where = " AND t1.createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
			}else{
				$day = '';
				$month = '';
				$year = (@$_POST['year']-543);
				$where = " AND t1.createdatetime BETWEEN '".$year."-01-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";
			}
		}
		
		$this->db->select('t1.id as loan_id');
		$this->db->from('coop_loan as t1');
		$this->db->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner");
		$this->db->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left");
		$this->db->join("coop_loan_reason as t4", "t1.loan_reason = t4.loan_reason_id", "inner");
		$this->db->join("coop_loan_name as t5", "t1.loan_type = t5.loan_name_id", "left");
		$this->db->join("coop_loan_type as t6", "t5.loan_type_id = t6.id", "left");
		$this->db->where("t6.id = '".$_POST['loan_type']."' AND t1.loan_status IN ('1','4') {$where}");
		//$this->db->where("t1.loan_type = '".$_POST['loan_type']."' AND t1.loan_status IN ('1','4') {$where}");
		$this->db->order_by('t1.createdatetime ASC');
		$rs_check = $this->db->get()->result_array();
		$row_check = @$rs_check[0];
		//print_r($this->db->last_query());exit;
		if(@$row_check['loan_id'] != ''){
			echo "success";
		}
	}
	
	function coop_report_loan_normal_excel(){
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$loan_type = array();
		foreach($rs_loan_type as $key => $row_loan_type){
			$loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		
		$this->load->view('report_loan_data/coop_report_loan_normal_excel',$arr_data);
	}
	function coop_report_loan_emergent_excel(){
		$arr_data = array();
		
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$loan_type = array();
		foreach($rs_loan_type as $key => $row_loan_type){
			$loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;

		$this->load->view('report_loan_data/coop_report_loan_emergent_excel',$arr_data);
	}
	

	public function coop_finance_year(){
		$arr_data = array();
		$this->libraries->template('report_loan_data/coop_finance_year',$arr_data);
	}
	
	function coop_finance_year_report(){
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		

		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		$this->load->view('report_loan_data/coop_finance_year_report',$arr_data);
	}	
	
	function coop_finance_year_preview(){
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		

		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		$this->preview_libraries->template_preview('report_loan_data/coop_finance_year_preview',$arr_data);
	}
	
	function coop_report_loan_normal_preview(){
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$loan_type = array();
		foreach($rs_loan_type as $key => $row_loan_type){
			$loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		$this->preview_libraries->template_preview('report_loan_data/coop_report_loan_normal_preview',$arr_data);
	}
	
	function coop_report_loan_emergent_preview(){
		$arr_data = array();
		
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$loan_type = array();
		foreach($rs_loan_type as $key => $row_loan_type){
			$loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		$this->preview_libraries->template_preview('report_loan_data/coop_report_loan_emergent_preview',$arr_data);
	}
	
	function coop_report_loan_detail_preview(){
		$arr_data = array();
        $code_name = 'report_loan_data_model';
        $this->db->select('*');
        $this->db->from('format_setting_report');
        $this->db->where('code_name', $code_name);
        $switch_report = $this->db->get()->row_array();
        if($switch_report['switch_code'] == '1'){
			$arr_data = $this->report_loan_data_model->get_report_loan_data();
			$this->preview_libraries->template_preview('report_loan_data/coop_report_loan_detail_preview', $arr_data);
        }else if($switch_report['switch_code'] == '2'){
			$arr_data = $this->report_loan_data_model->get_report_loan_data_1();
			$this->preview_libraries->template_preview('report_loan_data/coop_report_loan_detail_preview_1', $arr_data);
		}else if($switch_report['switch_code'] == '3'){
            $arr_data = $this->report_loan_data_model->get_report_loan_data_1();
            $createdatetime = $arr_data['row_loan']['createdatetime'];
            $arr_data['loan_order'] = $this->report_loan_data_model->get_report_loan_order($createdatetime);
            if($arr_data['row_loan']['loan_type_id'] == '2'){ // กู้สามัญ
                if($arr_data['row_loan']['loan_type'] == '201') { // กู้สามัญหุ้นค้ำประกัน
                    $this->preview_libraries->template_preview('report_loan_data/bbcoop_report_loan_guarantee_normal_detail_preview', $arr_data);
                }else{
                    $this->preview_libraries->template_preview('report_loan_data/bbcoop_report_loan_guarantee_normal_person_detail_preview', $arr_data);
                }
            }else{
				if($arr_data['row_loan']['loan_type'] == '102') { // เงินกู้ฉุกเฉิน(เพื่อท่องเทียว)
					//count previous loan for check max contract total if exist.
					$prev_loan_type_count = 0;
					foreach($arr_data['loans'] as $loan) {
						if($loan['loan_type'] == '102') {// Check only loan type due to no information about counting period.
							$prev_loan_type_count++;
						}
					}
					$arr_data['prev_loan_type_count'] = $prev_loan_type_count;
					$this->preview_libraries->template_preview('report_loan_data/bbcoop_report_loan_detail_travel_preview', $arr_data);
				} else {
					$this->preview_libraries->template_preview('report_loan_data/bbcoop_report_loan_detail_preview', $arr_data);
                }
            }
        }
    }
	function coop_report_loan_deduct($loan_id=''){
		$arr_data = array();
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_deduct_list');
		$this->db->where("loan_deduct_list_code != 'deduct_pay_prev_loan' AND loan_deduct_status = 1");
		$this->db->order_by('run_id ASC');
		$row = $this->db->get()->result_array();
		$arr_data['loan_deduct_list'] = $row;
		
		$this->db->select(array(
			't1.loan_amount',
			't2.firstname_th',
			't2.lastname_th',
			't3.prename_short',
			't1.pay_type'
		));
		$this->db->from('coop_loan as t1');
		$this->db->where("t1.id = '".$loan_id."'");
		$this->db->join("coop_mem_apply as t2","t2.member_id = t1.member_id","left");
		$this->db->join("coop_prename as t3","t3.prename_id = t2.prename_id","left");
		$row = $this->db->get()->result_array();
		$arr_data['loan_data'] = @$row[0];
		$real_loan_amount = $arr_data['loan_data']['loan_amount'];
		
		/*$this->db->select(array(
			'interest',
			'date_period',
			'total_paid_per_month'
		));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".$loan_id."' AND period_count = '1'");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$arr_data['first_period'] = @$row[0];*/
		
		$this->db->select(array(
			'pay_per_month',
			'date_receive_money',
			'date_first_period',
			'first_interest',
			'event_guarantee',
			'estimate_receive_money'
		));
		$this->db->from('coop_loan_deduct_profile');
		$this->db->where("loan_id = '".$loan_id."'");
		$row = $this->db->get()->result_array();
		$arr_data['loan_deduct_profile'] = @$row[0];
		
		$this->db->select(array(
			'loan_deduct_list_code',
			'loan_deduct_amount'
		));
		$this->db->from('coop_loan_deduct');
		$this->db->where("loan_id = '".$loan_id."' ");
		$row = $this->db->get()->result_array();
		$loan_deduct = array();
		foreach($row as $key => $value){
			$loan_deduct[$value['loan_deduct_list_code']] = $value['loan_deduct_amount'];
			$real_loan_amount -= $value['loan_deduct_amount'];
		}
		$arr_data['loan_deduct'] = $loan_deduct;
		
		$arr_data['real_loan_amount'] = $real_loan_amount;
		$this->preview_libraries->template_preview('report_loan_data/coop_report_loan_deduct',$arr_data);
	}

	function coop_report_loan_deduct_tmp(){
		$arr_data = array();
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_deduct_list');
		$this->db->where("loan_deduct_list_code != 'deduct_pay_prev_loan' AND loan_deduct_status = 1");
		$this->db->order_by('run_id ASC');
		$row = $this->db->get()->result_array();
		$arr_data['loan_deduct_list'] = $row;
		
		$this->db->select(array(
			't1.firstname_th',
			't1.lastname_th',
			't2.prename_short'
		));
		$this->db->from("coop_mem_apply as t1");
		$this->db->join("coop_prename as t2","t1.prename_id = t2.prename_id","left");
		$this->db->where("t1.member_id = '".$_GET['member_id']."'");
		$row = $this->db->get()->result_array();
		$arr_data['member_data'] = @$row[0];
		
		$loan_deduct = array();
		$loan_deduct['deduct_share'] = $_GET['deduct_share'];
		$loan_deduct['deduct_blue_deposit'] = $_GET['deduct_blue_deposit'];
		$loan_deduct['deduct_insurance'] = $_GET['deduct_insurance'];
		$loan_deduct['deduct_person_guarantee'] = $_GET['deduct_person_guarantee'];
		$loan_deduct['deduct_loan_fee'] = $_GET['deduct_loan_fee'];
		$arr_data['loan_deduct'] = $loan_deduct;
		
		$this->preview_libraries->template_preview('report_loan_data/coop_report_loan_deduct_tmp',$arr_data);
	}
	
	function loan_ready_to_transfer_report(){
		$arr_data = array();
		//$where = 't2.id IS NULL ';
		$where = '1=1 AND t10.id is null';
		//$where .= " AND t1.loan_status = '1'";
		if(@$_GET['loan_type']!=''){
			$where .= " AND t6.id = '".$_GET['loan_type']."' ";
		}
		if(@$_GET['loan_name']!=''){
			$where .= " AND t5.loan_name_id = '".$_GET['loan_name']."' ";
		}
		
		if(@$_GET['loan_status']!=''){
			if(@$_GET['loan_status'] == '1'){
				$where .= " AND t1.loan_status IN('1','4') ";
			}else{
				$where .= " AND t1.loan_status = '".$_GET['loan_status']." '";
			}
		}else{
			$where .= " AND t1.loan_status IN('0','1','4','5') ";
		}

		if($_GET['approve_date']!=''){
			$approve_date_arr = explode('/',$_GET['approve_date']);
			$approve_day = stripslashes($approve_date_arr[0]);
			$approve_month = stripslashes($approve_date_arr[1]);
			$approve_year = stripslashes($approve_date_arr[2]);
			$_GET['approve_date'] = $approve_day."/".$approve_month."/".$approve_year;
			$approve_year -= 543;
			$approve_date = $approve_year.'-'.$approve_month.'-'.$approve_day;
			$where .= " AND t1.approve_date >= '".$approve_date." 00:00:00.000'";
		}
		if($_GET['thru_date']!=''){
			$thru_date_arr = explode('/',$_GET['thru_date']);
			$thru_day = stripslashes($thru_date_arr[0]);
			$thru_month = stripslashes($thru_date_arr[1]);
			$thru_year = stripslashes($thru_date_arr[2]);
			$_GET['thru_date'] = $thru_day."/".$thru_month."/".$thru_year;
			$thru_year -= 543;
			$thru_date = $thru_year.'-'.$thru_month.'-'.$thru_day;
			$where .= " AND t1.approve_date <= '".$thru_date." 23:59:59.000'";
		}
		
		$this->db->select(array(
			't1.id',
			't1.approve_date',
			't1.contract_number',
			't7.prename_short',
			't3.firstname_th',
			't3.lastname_th',
			't1.member_id',
			't1.loan_amount',
			't1.deduct_receipt_id',
			't8.estimate_receive_money',
			't3.mobile',
			't1.transfer_type',
			't1.transfer_bank_id',
			't1.transfer_bank_account_id',
			't1.transfer_account_id',
			't9.bank_name',
			't9.bank_code'	
		));
		$this->db->from('coop_loan as t1');
		$this->db->join('coop_loan_transfer as t2','t1.id = t2.loan_id','left');
		$this->db->join('coop_mem_apply as t3','t1.member_id = t3.member_id','inner');
		$this->db->join('coop_prename as t7','t3.prename_id = t7.prename_id','left');
		$this->db->join('coop_user as t4','t1.admin_id = t4.user_id','left');
		$this->db->join('coop_loan_name as t5','t1.loan_type = t5.loan_name_id','left');
		$this->db->join('coop_loan_type as t6','t6.id = t5.loan_type_id','left');
		$this->db->join('coop_loan_deduct_profile as t8','t1.id = t8.loan_id','left');
		$this->db->join('coop_bank as t9','t1.transfer_bank_id = t9.bank_id','left');
		$this->db->join('coop_loan_guarantee_compromise as t10','t1.id = t10.loan_id','left');
		$this->db->where($where);
		$this->db->order_by('t1.approve_date ASC,t1.id ASC');
		$row_loan = $this->db->get()->result_array();
		$row_sum = array();
		//echo $this->db->last_query();exit;
		foreach($row_loan as $key => $value){
			if($value['transfer_type']=='1'){
				$row_loan[$key]['transfer_text'] = 'บัญชีสหกรณ์เลขที่ '.$value['transfer_account_id'];
			}else if($value['transfer_type']=='2'){
				$row_loan[$key]['transfer_text'] = $value['bank_code'].' เลขที่ '.$value['transfer_bank_account_id'];
			}else{
				$row_loan[$key]['transfer_text'] = '';
			}
			
			$this->db->select(array('t1.ref_id','t1.data_type'));
			$this->db->from('coop_loan_prev_deduct as t1');
			$this->db->where("loan_id = '".$value['id']."'");
			$row = $this->db->get()->result_array();
			$i=0;
			foreach($row as $key2 => $value2){
				$where_2 = " receipt_id = '".$value['deduct_receipt_id']."' ";
				if($value2['data_type']=='atm'){
					$where_2 .= " AND t1.loan_atm_id = '".$value2['ref_id']."'";
					$this->db->select(array('t1.*','t2.contract_number'));
					$this->db->from('coop_finance_transaction as t1');
					$this->db->join('coop_loan_atm as t2','t1.loan_atm_id = t2.loan_atm_id','inner');
					$this->db->where($where_2);
				}else if($value2['data_type']=='loan'){
					$where_2 .= " AND t1.loan_id = '".$value2['ref_id']."'";
					$this->db->select(array('t1.*','t2.contract_number'));
					$this->db->from('coop_finance_transaction as t1');
					$this->db->join('coop_loan as t2','t1.loan_id = t2.id','inner');
					$this->db->where($where_2);
				}
				$row_receipt = $this->db->get()->result_array();
				foreach($row_receipt as $key3 => $value3){
					$row_loan[$key]['prev_loan'][$i]['contract_number'] = $value3['contract_number'];
					@$row_loan[$key]['prev_loan'][$i]['principal'] += $value3['principal_payment'];
					@$row_loan[$key]['prev_loan'][$i]['interest'] += $value3['interest'];
				}
				$i++;
			}
			
			$this->db->select(array('t1.loan_deduct_list_code','t1.loan_deduct_amount','t2.deduct_type'));
			$this->db->from('coop_loan_deduct as t1');
			$this->db->join("coop_loan_deduct_list AS t2","t1.loan_deduct_list_code = t2.loan_deduct_list_code","inner");
			$this->db->where("t1.loan_id = '".$value['id']."' AND t1.loan_deduct_list_code != 'deduct_pay_prev_loan' AND t2.loan_deduct_status = 1");
			$row = $this->db->get()->result_array();
			foreach($row as $key2 => $value2){
				if(@$value2['deduct_type'] == 'buy' || @$value2['loan_deduct_list_code'] == 'deduct_loan_other_buy'){
					$row_loan[$key]['loan_deduct']['deduct_loan_other_buy'] += @$value2['loan_deduct_amount'];
				}else{
					$row_loan[$key]['loan_deduct'][$value2['loan_deduct_list_code']] = @$value2['loan_deduct_amount'];
				}
			}
			
			$this->db->select(array('t1.financial_institutions_amount'));
			$this->db->from('coop_loan_financial_institutions as t1');
			$this->db->where("loan_id = '".$value['id']."'");
			$row = $this->db->get()->result_array();
			foreach($row as $key2 => $value2){
				@$row_loan[$key]['financial_institutions_amount'] += $value2['financial_institutions_amount'];
			}
		}
		$arr_data['row_loan'] = $row_loan;
		//echo"<pre>";print_r($row_loan);exit;
		$this->load->view('report_loan_data/loan_ready_to_transfer_report',$arr_data);
	}

	function loan_already_transfer_report_index(){
		$arr_data['loan_type'] = $this->db->get('coop_loan_type')->result();
		$this->libraries->template('report_loan_data/loan_already_transfer_report_index',@$arr_data);
	}
	
	function loan_already_transfer_report(){
		//echo"<pre>";print_r($_GET);exit;
		$arr_data = array();
		if(@$_GET['date_start']!=''){
			$date_start = $this->center_function->ConvertToSQLDate($_GET['date_start']);
		}else{
			$date_start = '';
		}
		if(@$_GET['date_end']!=''){
			$date_end = $this->center_function->ConvertToSQLDate($_GET['date_end']);
		}else{
			$date_end = '';
		}
		if($date_start > $date_end){
			$date_start_where = $date_end;
			$date_end_where = $date_start;
		}else{
			$date_start_where = $date_start;
			$date_end_where = $date_end;
		}
		$where = '1=1 AND t8.id is null';
		if(@$_GET['loan_type']!=''){
			$where .= " AND t4.id = '".$_GET['loan_type']."' ";
		}
		if(@$_GET['loan_name']!=''){
			$where .= " AND t3.loan_name_id = '".$_GET['loan_name']."' ";
		}
		if($date_start_where != '' && $date_end_where!=''){
			if($date_start_where == $date_end_where){
				$where .= " AND date_transfer LIKE '".$date_start_where."%' ";
				$date_text = "วันที่ ".$this->center_function->ConvertToThaiDate($date_start_where);
			}else{
				$where .= " AND date_transfer BETWEEN '".$date_start_where."' AND '".$date_end_where."' ";
				$date_text = "วันที่ ".$this->center_function->ConvertToThaiDate($date_start_where)." ถึง ".$this->center_function->ConvertToThaiDate($date_end_where);
			}
		}else if($date_start_where != '' && $date_end_where == ''){
			$where .= " AND date_transfer LIKE '".$date_start_where."%' ";
			$date_text = "วันที่ ".$this->center_function->ConvertToThaiDate($date_start_where);
		}else if($date_start_where == '' && $date_end_where != ''){
			$where .= " AND date_transfer LIKE '".$date_end_where."%' ";
			$date_text = "วันที่ ".$this->center_function->ConvertToThaiDate($date_end_where);
		}
		$arr_data['date_text'] = $date_text;
		$this->db->select(array(
			"SUBSTRING_INDEX(t2.contract_number,'/',1) as contract",
			"SUBSTRING_INDEX(t2.contract_number,'/',-1) as year",
			't1.date_transfer',
			't2.contract_number',
			't2.member_id',
			't5.firstname_th',
			't5.lastname_th',
			't6.prename_short',
			't1.amount_transfer',
			't1.pay_type',
			't1.dividend_bank_id',
			't1.dividend_acc_num',
			't1.account_id',
			't5.mobile',
			't7.bank_name'
		));
		$this->db->from('coop_loan_transfer as t1');
		$this->db->join('coop_loan as t2','t1.loan_id = t2.id','inner');
		$this->db->join('coop_loan_name as t3','t2.loan_type = t3.loan_name_id','left');
		$this->db->join('coop_loan_type as t4','t3.loan_type_id = t4.id','left');
		$this->db->join('coop_mem_apply as t5','t2.member_id = t5.member_id','inner');
		$this->db->join('coop_prename as t6','t5.prename_id = t6.prename_id','left');
		$this->db->join('coop_bank as t7','t1.dividend_bank_id = t7.bank_id','left');
		$this->db->join('coop_loan_guarantee_compromise as t8','t2.id = t8.loan_id','left');
		$this->db->where($where);
		$this->db->order_by('year asc,contract asc,t1.date_transfer ASC');
		$row_loan = $this->db->get()->result_array();
		//echo $this->db->last_query();
		$arr_data['row_loan'] = $row_loan;
		//echo"<pre>";print_r($row_loan);exit;
		if(@$_GET['download']==""){
			$this->preview_libraries->template_preview('report_loan_data/loan_already_transfer_report',$arr_data);
		}else{
			$this->load->view('report_loan_data/loan_already_transfer_report',$arr_data);
		}
		
	}
	
	function coop_report_loan_atm_detail_preview(){
		$arr_data = array();
		$member_id = @$_GET['member_id'];
		$loan_id = @$_GET['loan_id'];
		if($member_id != '') {
			$this->db->select('coop_mem_apply.*,
							coop_mem_type.mem_type_name,
							department.mem_group_full_name AS department_name,
							faction.mem_group_full_name AS faction_name,
							level.mem_group_full_name AS level_name'
							);
			$this->db->from('coop_mem_apply');
			$this->db->join("coop_mem_type","coop_mem_apply.mem_type_id = coop_mem_type.mem_type_id","left");
			$this->db->join("coop_mem_group as department","coop_mem_apply.department = department.id","left");
			$this->db->join("coop_mem_group as faction","coop_mem_apply.faction = faction.id","left");
			$this->db->join("coop_mem_group as level","coop_mem_apply.level = level.id ","left");
			$this->db->where("coop_mem_apply.member_id = '".$member_id."'");
			$rs_member = $this->db->get()->result_array();
			$row_member = $rs_member[0];
			$arr_data['row_member'] = $row_member;
			
			$this->db->select(array('t1.*','t1.total_amount AS loan_amount','\'ฉุกเฉิน ATM\' AS loan_type_detail','t7.user_name AS admin_name'));
			$this->db->from('coop_loan_atm as t1');	
			$this->db->join("coop_user AS t7","t1.admin_id = t7.user_id","left");	
			$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_atm_id='".$loan_id."'");
			$this->db->order_by("t1.loan_atm_id DESC");
			$rs_loan = $this->db->get()->result_array();
			$row_loan =  @$rs_loan[0];
			if(@$_GET['dev'] == 'dev'){
				echo $this->db->last_query(); 
				echo '<hr>';
			}
			
			$this->db->select(array('*'));
			$this->db->from('coop_loan_atm_setting');			
			$this->db->where("1=1");
			$this->db->limit(1);
			$rs_atm_setting = $this->db->get()->result_array();
			$row_atm_setting =  @$rs_atm_setting[0];
			
			$this->db->select(array('principal_per_month'));
			$this->db->from('coop_loan_atm_detail');			
			$this->db->where("loan_atm_id = '".$loan_id."'");
			$this->db->order_by("loan_date DESC");
			$this->db->limit(1);
			$rs_atm_detail = $this->db->get()->result_array();
			//$principal_per_month = @$rs_atm_detail[0]['principal_per_month'];
			
			//เมื่อในตาราง coop_loan_atm_detail ไม่มีการผ่อน ต่อ งวด
			//if($principal_per_month == ''){
				$principal_per_month = ceil(@$row_loan['loan_amount']/@$row_atm_setting['max_period']/100)*100;
			//}
			
			$row_loan['interest_per_year'] = @$row_atm_setting['interest_rate'];//อัตราดอกเบี้ย
			$row_loan['period_amount'] = @$row_atm_setting['max_period'];//งวด
			$total_paid_per_month = (@$principal_per_month > @$row_atm_setting['min_principal_pay_per_month'])?@$principal_per_month:@$row_atm_setting['min_principal_pay_per_month'];//ผ่อนต่อเดือน
			
			//ดอกเบี้ย 31 วัน ของจากยอดกู้เต็ม
			$date_count = 31;
			$interest_30_day = (((@$row_loan['loan_amount']*@$row_atm_setting['interest_rate'])/100)/365)*@$date_count;
			$interest_30_day = round(@$interest_30_day, 2);
			
			$arr_data['row_loan'] = @$row_loan;			
				
			$createdate_loan = date("Y-m-d", strtotime($row_loan['createdatetime']));
			//echo $this->db->last_query(); 
			
			/*$this->db->select(array(
				'principal_payment',
				'total_paid_per_month'
			));
			$this->db->from('coop_loan_period');
			$this->db->where("loan_id='".$loan_id."' AND date_count = '31'");
			$this->db->limit(1);
			$per_month = $this->db->get()->result_array();
			//echo $this->db->last_query(); 
			if(@$row_loan['pay_type'] == '1'){
				$total_paid_per_month = @round(@$per_month[0]['principal_payment'],-2);
				$pay_type_name = "แบบคงต้น";
			}else{
				$total_paid_per_month = @round(@$per_month[0]['total_paid_per_month'],-2);
				$pay_type_name = "แบบคงยอด";
			}
			*/
			$pay_type_name = "คงต้น";
			$arr_data['total_paid_per_month'] = @$total_paid_per_month;
			$arr_data['interest_30_day'] = @$interest_30_day;
			$arr_data['pay_type'] = @$pay_type_name;
			
			
			$this->db->select('*');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2')");
			$this->db->order_by('share_date DESC');
			$this->db->limit(1);
			$row_prev_share = $this->db->get()->result_array();
			$row_prev_share = @$row_prev_share[0];
			
			$arr_data['count_share'] = $row_prev_share['share_collect'];
			$arr_data['cal_share'] = $row_prev_share['share_collect_value']; //หุ้นที่มี่
			$arr_data['rules_share'] = $row_loan['loan_amount']*20/100; //หุ้นตามหลักเกณฑ์
			$arr_data['old_share'] = 0; //เดิม
			$arr_data['deposit_account_in'] = 0; //เข้าบัญชีเงินฝาก
			
			//////////////////////////////////////
			//รายการผ่อนชำระสหกรณ์ปัจจุบัน/เดือน
			//$month_now = date('n');
			//$year_now = date('Y')+543;
			$month_now = date('n',strtotime(@$row_loan['createdatetime']));
			$year_now = date('Y',strtotime(@$row_loan['createdatetime']))+543;
			$date_month_end = date('Y-m-t',strtotime((@$year_now-543).'-'.sprintf("%02d",@$month_now).'-01'));
			
			$this->db->select(array('coop_finance_month_detail.*','coop_finance_month_profile.*','coop_loan_name.loan_type_id'));
			$this->db->from('coop_finance_month_detail');
			$this->db->join('coop_finance_month_profile', 'coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id', 'left');
			$this->db->join('coop_loan', 'coop_finance_month_detail.loan_id = coop_loan.id', 'left');
			$this->db->join('coop_loan_name', 'coop_loan.loan_type = coop_loan_name.loan_name_id', 'left');
			$this->db->where("
						coop_finance_month_detail.member_id = '".@$member_id."'
						AND coop_finance_month_profile.profile_month = '".$month_now."'
						AND coop_finance_month_profile.profile_year = '".$year_now."'
					");
			$row_finance_month = $this->db->get()->result_array();
			//echo $this->db->last_query()."<br>";
			if ($month_now == $year_now){
				echo 'ไม่เท่ากัน';
			}
			//
			if(!empty($row_finance_month)){
				//ออกรายการเรียกเก็บประจำเดือนแล้ว
				//echo '<pre>'; print_r($row_finance_month); echo '</pre>';
				$arr_list_loan = array();
				foreach($row_finance_month AS $key_month=>$value_month){
					//echo @$value_month['deduct_code'].'<br>';
					if(@$value_month['deduct_code'] == 'SHARE'){
						//หุ้นหักรายเดือน
						$share_month = @$value_month['pay_amount'];
						$share_month_interest = 0;
						$arr_data['share_month'] = @$share_month; //หุ้นหักรายเดือน(เงินต้น)
						$arr_data['share_month_interest'] = @$share_month_interest; //หุ้นหักรายเดือน(ดอกเบี้ย)
						
					}						
					
					if(@$value_month['deduct_code'] == 'DEPOSIT'){
						//เงินฝากหักรายเดือน
						$deposit_month =  @$value_month['pay_amount'];
						$deposit_month_interest = 0;	
						$arr_data['deposit_month'] = @$deposit_month; //เงินฝากหักรายเดือน(เงินต้น)
						$arr_data['deposit_month_interest'] = @$deposit_month_interest; //เงินฝากหักรายเดือน(ดอกเบี้ย)	
											
					}
					
					if(@$value_month['deduct_code'] == 'LOAN'){
						if(@$value_month['pay_type'] == 'principal'){
							$arr_list_loan[@$value_month['loan_type_id']]['loan_principle'] = @$value_month['pay_amount'];//ยอดที่ชำระต่อเดือน เงินต้น
						}
						
						if(@$value_month['pay_type'] == 'interest'){
							$arr_list_loan[@$value_month['loan_type_id']]['loan_interest'] = @$value_month['pay_amount'];//(ดอกเบี้ย)
						}
					}
					
					if(@$value_month['deduct_code'] == 'ATM'){
						if(@$value_month['pay_type'] == 'principal'){
							@$arr_list_loan[7]['loan_principle'] += @$value_month['pay_amount'];//ยอดที่ชำระต่อเดือน เงินต้น
						}
						
						if(@$value_month['pay_type'] == 'interest'){
							@$arr_list_loan[7]['loan_interest'] += @$value_month['pay_amount'];//(ดอกเบี้ย)
						}
					}
					
					
				}			
				
				$this->db->select(array('*'));
				$this->db->from('coop_loan_type');
				$loan_type = $this->db->get()->result_array();
				$list_loan = array();
				$loan_principle_total = 0;
				$loan_interest_total = 0;
				if(!empty($loan_type)){
					foreach($loan_type AS $key=>$value){						
						$list_loan[$value['id']]['loan_name']= $value['loan_type'];//ชื่อเงินกู้หลัก
						$list_loan[$value['id']]['loan_principle']= @$arr_list_loan[$value['id']]['loan_principle'];//ยอดที่ชำระต่อเดือน
						$list_loan[$value['id']]['loan_interest'] = @$arr_list_loan[$value['id']]['loan_interest'];//(ดอกเบี้ย)
						$loan_principle_total += @$arr_list_loan[$value['id']]['loan_principle'];
						$loan_interest_total += @$arr_list_loan[$value['id']]['loan_interest'];
					}
				}
				$arr_data['list_loan'] = @$list_loan;			
			}else{
				//ยังไม่ได้ออกรายการเรียกเก็บประจำเดือน
				$arr_list_loan = array();
				$this->db->select('setting_value');
				$this->db->from('coop_share_setting');
				$this->db->where("setting_id = '1'");
				$row = $this->db->get()->result_array();
				$row_share_value = $row[0];
				$share_value = $row_share_value['setting_value'];
			
				$this->db->select(array('deduct_id','deduct_code','deduct_detail','deduct_type','deduct_format','deposit_type_id','deposit_amount'));
				$this->db->from('coop_deduct');
				$this->db->order_by('deduct_seq ASC');
				$deduct_list = $this->db->get()->result_array();
				//echo '<pre>'; print_r($deduct_list); echo '</pre>';	
				foreach($deduct_list as $key2 => $value2){
					
					//หุ้นหักรายเดือน
					if($value2['deduct_code']=='SHARE'){
						//งดหุ้นชั่วคราว
						$check_refrain_share = 0;
						$this->db->select('*');
						$this->db->from('coop_refrain_share');
						$this->db->where("member_id = '".$row_member['member_id']."' AND type_refrain = '2' AND month_refrain = '".@$month_now."' AND year_refrain = '".@$year_now."'");
						$this->db->order_by('refrain_id DESC');			
						$rs_refrain_temporary = $this->db->get()->result_array();
						if(!empty($rs_refrain_temporary)){
							foreach($rs_refrain_temporary AS $key=>$value){
								$check_refrain_share = 1;
							}
						}
						
						//งดหุ้นถาวร
						$this->db->select('*');
						$this->db->from('coop_refrain_share');
						$this->db->where("member_id = '".$row_member['member_id']."' AND type_refrain = '1'");
						$this->db->order_by('refrain_id DESC');			
						$rs_refrain_permanent = $this->db->get()->result_array();
						if(!empty($rs_refrain_permanent)){
							foreach($rs_refrain_permanent AS $key=>$value){
								$check_refrain_share = 1;
							}
						}				
						
						//ทุนเรือนหุ้น
						if(@$row_member['apply_type_id'] == '1' && $check_refrain_share == 0){															
							$share = @$row_member['share_month'];
							//echo $share.'<hr>';
							$share_month = @$share;
							$share_month_interest = 0;
							$arr_data['share_month'] = @$share_month; //หุ้นหักรายเดือน(เงินต้น)
							$arr_data['share_month_interest'] = @$share_month_interest; //หุ้นหักรายเดือน(ดอกเบี้ย)
						}
						//ทุนเรือนหุ้น
						//echo $row_member['apply_type_id'].'<br>';
						
						/*if(@$row_member['apply_type_id'] == '1'){	
							$this->db->select(array('change_value'));
							$this->db->from('coop_change_share');
							$this->db->where("member_id = '".$row_member['member_id']."' AND change_share_status IN ('1','2')");
							$this->db->order_by("change_share_id DESC");
							$this->db->limit(1);
							$row_change_share = $this->db->get()->result_array();
							$row_change_share = @$row_change_share[0];
							$sum = 0;
							if(@$row_change_share['change_value'] != ''){
								$num_share = @$row_change_share['change_value'];
							}else{
								$this->db->select(array('share_salary'));
								$this->db->from('coop_share_rule');
								$this->db->where("salary_rule <= '".$row_member['salary']."'");
								$this->db->order_by("salary_rule DESC");
								$this->db->limit(1);
								$row_share_rule = $this->db->get()->result_array();
								$row_share_rule = @$row_share_rule[0];
								
								$num_share = @$row_share_rule['share_salary'];
							}
							$share = @$num_share*@$share_value;

							$share_month = @$share;
							$share_month_interest = 0;
							$arr_data['share_month'] = @$share_month; //หุ้นหักรายเดือน(เงินต้น)
							$arr_data['share_month_interest'] = @$share_month_interest; //หุ้นหักรายเดือน(ดอกเบี้ย)
						}
						*/
					}
					
					//เงินฝากหักรายเดือน
					if($value2['deduct_code']=='DEPOSIT'){							
						//เงินฝาก	
						$sum_deposit = 0;
						$DEPOSIT = 0;						
						$deposit_type_id = @$value2['deposit_type_id'];
						$DEPOSIT = @$value2['deposit_amount'];
						//echo $deposit_type_id.'<hr>';
						$deposit_period_count = 1;
						$deposit_balance = $DEPOSIT;
						
						$this->db->select('*');
						$this->db->from('coop_maco_account');
						$this->db->where("mem_id = '".@$row_member['member_id']."' AND type_id = '".@$deposit_type_id."'");
						$this->db->limit(1);
						$rs_account = $this->db->get()->result_array();
						$account_id = @$rs_account[0]['account_id'];
						if(!empty($account_id)){												
							if($DEPOSIT > 0){						
								$sum_deposit += @$DEPOSIT;
							}
						}

						$deposit_month =  @$sum_deposit;
						$deposit_month_interest = 0;	
						$arr_data['deposit_month'] = @$deposit_month; //เงินฝากหักรายเดือน(เงินต้น)
						$arr_data['deposit_month_interest'] = @$deposit_month_interest; //เงินฝากหักรายเดือน(ดอกเบี้ย)							
					}
				
					
					if(@$value2['deduct_code'] == 'LOAN'){										
						$LOAN = array();
						$where = '';
						$where .= " AND (coop_loan.guarantee_for_id = '' OR coop_loan.guarantee_for_id IS NULL) ";
		
						$this->db->select(
							array(
								'coop_loan.id',
								'coop_loan.loan_type',
								'coop_loan.contract_number',
								'coop_loan.loan_amount_balance',
								'coop_loan.interest_per_year',
								'coop_loan_transfer.date_transfer',
								'coop_loan_name.loan_name',
								'coop_loan.pay_type',
								'coop_loan.money_period_1',
								'coop_loan.createdatetime',
								'coop_loan.guarantee_for_id',
								'coop_loan_name.loan_type_id'
							)
						);
						$this->db->from('coop_loan');
						$this->db->join('coop_loan_transfer', 'coop_loan_transfer.loan_id = coop_loan.id', 'left');
						$this->db->join('coop_loan_name', 'coop_loan_name.loan_name_id = coop_loan.loan_type', 'inner');
						$this->db->where("
							coop_loan.loan_amount_balance > 0
							AND coop_loan.member_id = '".$row_member['member_id']."'
							AND coop_loan.loan_status = '1'
							AND coop_loan_transfer.transfer_status = '0'
							AND coop_loan.date_start_period <= '".($year_now-543)."-".sprintf("%02d",$month_now)."-".date('t',strtotime(($year_now-543)."-".$month_now."-01"))."' 
						".$where);
						$row_loan_month = $this->db->get()->result_array();
						//echo $this->db->last_query()."<br>";
						$j=0;
						
						foreach($row_loan_month as $key => $row_normal_loan){
							$this->db->select(array('deduct_id','ref_id'));
							$this->db->from('coop_deduct_detail');
							$this->db->where("deduct_id = '{$value2['deduct_id']}' AND ref_id = '{$row_normal_loan['loan_type']}'");
							$rs_deduct_detail = $this->db->get()->result_array();
							$ref_id = @$rs_deduct_detail[0]['ref_id'];
							if(!empty($ref_id)){
								$deduct_format = @$value2['deduct_format'];
								
								if($row_normal_loan['guarantee_for_id']!=''){
									$for_loan_id = $row_normal_loan['guarantee_for_id'];
								}else{
									$for_loan_id = $row_normal_loan['id'];
								}
								
								$this->db->select(
									array(
										'outstanding_balance',
										'principal_payment',
										'total_paid_per_month'
									)
								);
								$this->db->from('coop_loan_period');
								$this->db->where("loan_id = '".$for_loan_id."'");
								$this->db->limit(1);
								$row_loan_period = $this->db->get()->result_array();
								$row_principal_payment = $row_loan_period[0];
								
								$date_interesting = $date_month_end;
								$cal_loan_interest = array();
								$cal_loan_interest['loan_id'] = $row_normal_loan['id'];
								$cal_loan_interest['date_interesting'] = $date_interesting;
								$interest = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
								
								if($row_principal_payment['principal_payment'] > $row_normal_loan['loan_amount_balance']){
									$principal_payment = @$row_normal_loan['loan_amount_balance'];
									$balance = 0;
								}else{
									$principal_payment = @$row_principal_payment['principal_payment'];
									$balance = @$row_normal_loan['loan_amount_balance']-@$row_principal_payment['principal_payment'];
								}
								
								$LOAN[$j]['loan_id'] = $row_normal_loan['id'];
								$LOAN[$j]['loan_type'] = $row_normal_loan['loan_name'];
								//$LOAN[$j]['loan_type_id'] = $row_normal_loan['loan_type'];
								$LOAN[$j]['loan_type_id'] = $row_normal_loan['loan_type_id'];
								$LOAN[$j]['contract_number'] = $row_normal_loan['contract_number'];
								$LOAN[$j]['money_period_1'] = $row_normal_loan['money_period_1'];
								$LOAN[$j]['pay_loan_type'] = $row_normal_loan['pay_type'];
								if($deduct_format == '2'){
									$LOAN[$j]['text_title'] = 'ต้นเงินกู้เลขที่สัญญา';
									$LOAN[$j]['principal_payment'] = $principal_payment;
									$LOAN[$j]['interest'] = 0;
									$LOAN[$j]['total'] = $principal_payment;
									$LOAN[$j]['pay_type'] = 'principal';
								}else if($deduct_format == '1'){
									$LOAN[$j]['text_title'] = 'ดอกเบี้ยเงินกู้เลขที่สัญญา';
									$LOAN[$j]['principal_payment'] = 0;
									$LOAN[$j]['interest'] = $interest;
									$LOAN[$j]['total'] = $interest;
									$LOAN[$j]['pay_type'] = 'interest';
									$interest_arr[$row_normal_loan['id']] = $interest;
								}	
								$balance = @$row_normal_loan['loan_amount_balance']-$principal_payment-$interest;
								$LOAN[$j]['balance'] = $balance;
								
							}
							$j++;
						}
						//echo"<pre>";print_r($LOAN);echo"</pre>";
						if(!empty($LOAN)){
							foreach($LOAN as $key3 => $value3){
								$arr_list_loan[@$value3['loan_type_id']]['loan_principle'] = @$value3['principal_payment'];//ยอดที่ชำระต่อเดือน เงินต้น							
								$arr_list_loan[@$value3['loan_type_id']]['loan_interest'] = @$value3['interest'];//(ดอกเบี้ย)
							}
						}
						
						//echo '<pre>'; print_r($arr_list_loan); echo '</pre>';
					}
					
					if(@$value2['deduct_code'] == 'ATM'){					
						$ATM = 0;
						$this->db->select(array(
							't1.loan_amount_balance',
							't1.principal_per_month',
							't2.contract_number',
							't2.loan_atm_id',
							't1.date_last_pay',
							't1.loan_date'
						));
						$this->db->from('coop_loan_atm_detail as t1');
						$this->db->join('coop_loan_atm as t2', 't1.loan_atm_id = t2.loan_atm_id', 'inner');
						$this->db->where("
							t2.member_id = '".$row_member['member_id']."'
							AND t2.loan_atm_status = '1'
							AND t1.date_start_period <= '".$date_month_end."'
							AND t1.loan_status = '0'
						");
						$row_atm = $this->db->get()->result_array();
						$principal_per_month = 0;
						$loan_amount_balance = 0;
						if(!empty($row_atm)){
							foreach($row_atm as $key_atm => $value_atm){
								$loan_atm_id = @$value_atm['loan_atm_id'];
								$principal_per_month += @$value_atm['principal_per_month'];
								$loan_amount_balance += @$value_atm['loan_amount_balance'];
							}
							if(@$principal_per_month < @$loan_atm_setting['min_principal_pay_per_month']){
								$principal_per_month = @$loan_atm_setting['min_principal_pay_per_month'];
							}
							if(@$principal_per_month > @$loan_amount_balance){
								$principal_per_month = @$loan_amount_balance;
							}
							
							$cal_loan_interest = array();
							$cal_loan_interest['loan_atm_id'] = @$loan_atm_id;
							$cal_loan_interest['date_interesting'] = @$date_month_end;
							$interest = $this->loan_libraries->cal_atm_interest(@$cal_loan_interest);
							
							
							$deduct_format = @$value2['deduct_format'];
							if($deduct_format == '2'){
								@$arr_list_loan[7]['loan_principle'] += @$principal_per_month;//ยอดที่ชำระต่อเดือน เงินต้น
							}else{
								@$arr_list_loan[7]['loan_interest'] += @$interest;//(ดอกเบี้ย)
							}
						}
					}
				}
				//echo '<pre>'; print_r($arr_list_loan); echo '</pre>';
					
				$this->db->select(array('*'));
				$this->db->from('coop_loan_type');
				$loan_type = $this->db->get()->result_array();
				$list_loan = array();
				$loan_principle_total = 0;
				$loan_interest_total = 0;
				if(!empty($loan_type)){
					foreach($loan_type AS $key=>$value){						
						$list_loan[$value['id']]['loan_name']= $value['loan_type'];//ชื่อเงินกู้หลัก
						$list_loan[$value['id']]['loan_principle']= @$arr_list_loan[$value['id']]['loan_principle'];//ยอดที่ชำระต่อเดือน
						$list_loan[$value['id']]['loan_interest'] = @$arr_list_loan[$value['id']]['loan_interest'];//(ดอกเบี้ย)
						$loan_principle_total += @$arr_list_loan[$value['id']]['loan_principle'];
						$loan_interest_total += @$arr_list_loan[$value['id']]['loan_interest'];
					}
				}
				$arr_data['list_loan'] = @$list_loan;	
			}
			///////////////////////////////////////////////
			
			//เงินฝาก
			$this->db->select(array('coop_maco_account.account_id','coop_maco_account.mem_id','coop_maco_account.account_name','coop_deposit_type_setting.type_name'));
			$this->db->from('coop_maco_account');
			$this->db->join("coop_deposit_type_setting","coop_maco_account.type_id = coop_deposit_type_setting.type_id AND deduct_loan = '1'","inner");
			$this->db->where("coop_maco_account.mem_id = '".@$member_id."'");
			$row_account= $this->db->get()->result_array();
			$account_list = array();
			if(!empty($row_account)){
				foreach($row_account AS $key=>$value){
					$this->db->select(array('transaction_balance','transaction_deposit'));
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$value['account_id']."'");
					$this->db->order_by('transaction_id DESC');
					$this->db->limit(1);
					$row_balance = $this->db->get()->result_array();
					//$account_balance  = @$row_balance[0]['transaction_balance'];
					$account_balance  = @$row_balance[0]['transaction_deposit'];
					
					$account_list[$value['account_id']]['account_id'] =  @$value['account_id'];
					$account_list[$value['account_id']]['account_name'] =  @$value['account_name'];
					$account_list[$value['account_id']]['account_balance'] =  @$account_balance;
					//print_r($this->db->last_query());
				}
			}
			$arr_data['account_list'] = @$account_list;			
			/*
//////////////////////////////////////////////////////////////////////////////////////////////////ปิดสัญญาเดิม//////////////////////////////////////////////////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////// หักกลบ ////////////////////////////////////////////////////////////////	
		$this->db->select(array(
			't1.id',
			't1.contract_number',
			't1.loan_amount_balance'
		));
		$this->db->from("coop_loan t1");
		$this->db->join("coop_loan_name t2",'t1.loan_type = t2.loan_name_id','inner');
		$this->db->join("coop_loan_type t3",'t2.loan_type_id = t3.id','inner');
		$this->db->where("t1.loan_status = '1' AND member_id = '".@$member_id."' AND t3.loan_type_code = 'emergent'");
		$row = $this->db->get()->result_array();
		$list_old_loan = array();
		$index = 0;
		foreach($row as $key => $value){
			$cal_loan_interest = array();
			$cal_loan_interest['loan_id'] = $value['id'];
			$cal_loan_interest['date_interesting'] = date('Y-m-d');
			$interest_amount = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
			
			
			$list_old_loan[$index]['contract_number'] = @$value['contract_number'];
			$list_old_loan[$index]['loan_amount_balance'] = @$value['loan_amount_balance'];
			$list_old_loan[$index]['loan_interest_amount'] = @$interest_amount;	
			$index++;
		}
			
			$this->db->select(array(
				't1.loan_atm_id',
				't1.total_amount_approve',
				't1.total_amount_balance',
				't1.contract_number'
			));
			$this->db->from("coop_loan_atm t1");
			$this->db->where("t1.loan_atm_status = '1' AND loan_atm_id != '".@$loan_id."' AND member_id = '".@$member_id."'");
			$row = $this->db->get()->result_array();
			foreach($row as $key => $value){
				$cal_loan_interest = array();
				$cal_loan_interest['loan_atm_id'] = $value['loan_atm_id'];
				$cal_loan_interest['date_interesting'] = date('Y-m-d');
				$interest_amount = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
				
				$list_old_loan[$index]['contract_number'] = @$value['contract_number'];
				$list_old_loan[$index]['loan_amount_balance'] = (@$value['total_amount_approve']-@$value['total_amount_balance']);
				$list_old_loan[$index]['loan_interest_amount'] = @$interest_amount;
		
				$index++;				
			}
/////////////////////////////////////////////////////////// หักกลบ ////////////////////////////////////////////////////////////////
			
			$arr_data['list_old_loan'] = @$list_old_loan;
			$total_principal = 0;
			$total_interest = 0;
			$total_loan_balance = 0;
			if(!empty($list_old_loan)){
				foreach($list_old_loan AS $key => $value){
					$total_principal += @$value['loan_amount_balance'];
					$total_interest += @$value['loan_interest_amount'];
					$total_loan_balance += @$value['loan_amount_balance']+@$value['loan_interest_amount'];
				}
			}
			
//////////////////////////////////////////////////////////////////////////////////////////////////ปิดสัญญาเดิม//////////////////////////////////////////////////////////////////////////////////////////////////
			
			$arr_data['existing_loan'] = @$total_loan_balance; //หักเงินกู้เดิม
			$arr_data['principal_load'] = @$total_principal;//ภาระเงินต้น
			$arr_data['interest_burden'] = @$total_interest;//ภาระดอกเบี้ย
			*/
			$date_interesting = date('Y-m-d');
			$list_old_loan = array();
			
			$this->db->select(array('t1.*',
									't2.contract_number',
									't2.loan_amount_balance',
									't2.id'
									));
			$this->db->from("coop_loan_atm_prev_deduct t1");
			$this->db->join("coop_loan t2",'t1.ref_id = t2.id','inner');
			$this->db->where("t1.loan_atm_id = '".@$loan_id."' AND t1.data_type = 'loan'");
			$row = $this->db->get()->result_array();
			$index = 0;
			if(@$_GET['dev'] == 'dev'){
				print_r($this->db->last_query()); //exit;
			}
			foreach($row as $key => $value){
				$list_old_loan[$index]['contract_number'] = @$value['contract_number'];
				if($value['pay_type'] == 'principal'){					
					$list_old_loan[$index]['loan_id'] = @$value['id'];
					$list_old_loan[$index]['loan_amount_balance'] = @$value['pay_amount'];
					$list_old_loan[$index]['loan_interest_amount'] = 0;
				}else{
					$list_old_loan[$index]['loan_id'] = @$value['id'];
					$list_old_loan[$index]['loan_amount_balance'] = @$value['loan_amount_balance'];
					$cal_loan_interest = array();
					$cal_loan_interest['loan_id'] = @$value['ref_id'];
					$cal_loan_interest['date_interesting'] = $this->center_function->ConvertToSQLDate(@$date_interesting);
					$interest_data = @$value['interest_amount'];
					$list_old_loan[$index]['loan_interest_amount'] = @$interest_data;
				}
				$index++;
			}	
			
			//ปิดปัญชีกู้ฉุกเฉิน ATM
			$this->db->select(array('t1.*',
									't2.contract_number',
									't2.total_amount_approve',
									't2.total_amount_balance',
									't2.loan_atm_id'
									));
			$this->db->from("coop_loan_atm_prev_deduct t1");
			$this->db->join("coop_loan_atm t2",'t1.ref_id = t2.loan_atm_id','inner');
			$this->db->where("t1.loan_atm_id = '".@$loan_id."' AND t1.data_type = 'atm'");
			$row = $this->db->get()->result_array();
			if(@$_GET['dev'] == 'dev'){
				print_r($this->db->last_query()); //exit;
			}
			
			foreach($row as $key => $value){
				$list_old_loan[$index]['contract_number'] = @$value['contract_number'];
				if($value['pay_type'] == 'principal'){					
					$list_old_loan[$index]['loan_id'] = @$value['loan_atm_id'];
					$list_old_loan[$index]['loan_amount_balance'] = @$value['total_amount_approve'] - @$value['total_amount_balance'];
					$list_old_loan[$index]['loan_interest_amount'] = 0;
				}else{
					$list_old_loan[$index]['loan_id'] = @$value['loan_atm_id'];
					//$list_old_loan[$index]['loan_amount_balance'] = @$value['loan_amount_balance'];
					$list_old_loan[$index]['loan_amount_balance'] = @$value['principal_amount'];
					//$cal_loan_interest = array();
					//$cal_loan_interest['loan_id'] = @$value['ref_id'];
					//$cal_loan_interest['date_interesting'] = $this->center_function->ConvertToSQLDate(@$date_interesting);
					//$interest_atm = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
					//$list_old_loan[$index]['loan_interest_amount'] = @$interest_atm;
					$list_old_loan[$index]['loan_interest_amount'] = @$value['interest_amount'];
				}
				$index++;
			}			
			if(@$_GET['dev'] == 'dev'){
					echo '<pre>'; print_r(@$list_old_loan); echo '</pre>';
			}
			
			$arr_data['list_old_loan'] = @$list_old_loan;
			$total_principal = 0;
			$total_interest = 0;
			$total_loan_balance = 0;
			if(!empty($list_old_loan)){
				foreach($list_old_loan AS $key => $value){
					$total_principal += @$value['loan_amount_balance'];
					$total_interest += @$value['loan_interest_amount'];
					$total_loan_balance += @$value['loan_amount_balance']+@$value['loan_interest_amount'];
				}
			}
			
			$arr_data['existing_loan'] = @$total_loan_balance; //หักเงินกู้เดิม
			$arr_data['principal_load'] = @$total_principal;//ภาระเงินต้น
			$arr_data['interest_burden'] = @$total_interest;//ภาระดอกเบี้ย
			
			$loan_amount = @$row_loan['loan_amount']; //วงเงินที่ขออนุมัติ		
			
			$month_receipt = date("m", strtotime($row_loan['createdatetime']));
			$year_receipt = date("Y", strtotime($row_loan['createdatetime']));
			
			$this->db->select(array('deduct_id','deduct_code','deduct_detail','deduct_type','deduct_format','deposit_type_id','deposit_amount'));
			$this->db->from('coop_deduct');
			$this->db->order_by('deduct_seq ASC');
			$deduct_list = $this->db->get()->result_array();
			$data_arr['deduct_list'] = @$deduct_list;
			//
			
			$deductible =  0 ;			
			//ค่าธรรมเนียม 
			$this->db->select(array('*'));
			$this->db->from('coop_loan_deduct');
			$this->db->where("loan_id = '{$loan_id}'");
			$rs_deduct = $this->db->get()->result_array();
			//echo $loan_id.'<hr>';
			//echo '<pre>'; print_r($rs_deduct); echo '</pre>';
			if(!empty($rs_deduct)){
				foreach($rs_deduct AS $key=>$row_deduct){
					if($row_deduct['loan_deduct_list_code'] != 'deduct_pay_prev_loan'){
						$deductible += @$row_deduct['loan_deduct_amount'];
					}
					
					if($row_deduct['loan_deduct_list_code'] == 'deduct_loan_fee'){
						$arr_data['deduct_loan_fee'] = @$row_deduct['loan_deduct_amount'];
					}
					
					if($row_deduct['loan_deduct_list_code'] == 'deduct_person_guarantee'){
						//มีการกำหนดกรณีไม่ผ่านเกณฑ์
						$arr_data['deduct_person_guarantee'] = @$row_deduct['loan_deduct_amount'];
					}
					if($row_deduct['loan_deduct_list_code'] == 'deduct_insurance'){
						//เบี้ยประกัน
						$arr_data['deduct_insurance'] = @$row_deduct['loan_deduct_amount'];
					}
				}
			}
			//exit;			
			
			$arr_data['deductible'] =  @$deductible; //รายการหัก
			$total_amount = @$loan_amount - @$total_loan_balance - @$deductible;
			$arr_data['total_amount'] = @$total_amount;
			
			$arr_data['total_paid_per_month'] =  @$total_paid_per_month; //รวมชำระต่องวด
			
			//รายการรับเงิน	
			$arr_receiving_money = array();
			//ชำระหนี้สถาบันการเงิน
			/*$this->db->select(array('*'));
			$this->db->from('coop_loan_financial_institutions');
			$this->db->where("loan_id = '".@$loan_id."'");
			$this->db->order_by("order_by ASC");
			$rs_financial_institutions = $this->db->get()->result_array();
			*/
			$i=0;
			$financial_amount = 0;
			/*foreach($rs_financial_institutions AS $key=>$row_financial_institutions){
				$arr_receiving_money[$i]['transfer_type'] = 'จ่ายธนาคาร'.@$row_financial_institutions['financial_institutions_name'];
				$arr_receiving_money[$i]['total_received'] = @$row_financial_institutions['financial_institutions_amount'];
				$financial_amount += @$row_financial_institutions['financial_institutions_amount'];
				$i++;
			}
			*/
			
			//การจ่ายเงินกู้
			/*$transfer_type = array('0'=>'เงินสด','1'=>'โอนเงินบัญชีสหกรณ์','2'=>'โอนเงินบัญชี');
			if(@$row_loan['transfer_type'] == '2'){
				$transfer_type_description = '';
				$transfer_type_description .= @$row_loan['bank_name'];
				$transfer_type_description .= '<br> เลขที่บัญชี '.@$row_loan['transfer_bank_account_id'];
			}else if(@$row_loan['transfer_type'] == '1'){
				$transfer_type_description = ' '.@$row_loan['transfer_account_id'].':'.@$row_loan['account_name'];
			}else{
				$transfer_type_description = '';
			}
			$text_transfer_type = @$transfer_type[@$row_loan['transfer_type']].@$transfer_type_description;
			*/
			$text_transfer_type = 'รับสุทธิ';
			
			//ยอดเงินที่จะได้รับโดยประมาณ
			/*$this->db->select(array('estimate_receive_money'));
			$this->db->from('coop_loan_deduct_profile');
			$this->db->where("loan_id = '".@$loan_id."'");
			$loan_deduct_profile = $this->db->get()->result_array();
			$estimate_receive_money = @$loan_deduct_profile[0]['estimate_receive_money'];			
			*/
			$arr_receiving_money[$i]['transfer_type'] = @$text_transfer_type;
			//$arr_receiving_money[$i]['total_received'] = @$estimate_receive_money-@$financial_amount;
			$arr_receiving_money[$i]['total_received'] = @$total_amount;
			$arr_data['receiving_money'] = $arr_receiving_money;		
					
			$arr_data['total_month'] = @$share_month+@$deposit_month+@$loan_principle_total;//รวมทั้งสิ้น รายการผ่อนชำระสหกรณ์ปัจจุบัน/เดือน
			$arr_data['total_month_interest'] = @$share_month_interest+@$deposit_month_interest+@$loan_interest_total;//รวมทั้งสิ้น รายการผ่อนชำระสหกรณ์ปัจจุบัน/เดือน(ดอกเบี้ย)
			
			//บุคคลค้ำประกัน
			$rs_guarantee = array();
			/*$this->db->select(array(
				't1.*',
				't3.firstname_th',
				't3.lastname_th',
				't3.mem_group_id',
				't4.mem_group_name'
			));
			$this->db->from('coop_loan_guarantee_person as t1');
			$this->db->join('coop_mem_apply as t3','t1.guarantee_person_id = t3.member_id','inner');
			$this->db->join("coop_mem_group as t4", "t3.level = t4.id", "left");
			$this->db->where("t1.loan_id = '{$loan_id}'");
			$this->db->order_by("t1.id ASC");
			$rs_guarantee = $this->db->get()->result_array();
			foreach($rs_guarantee AS $key=>$row_guarantee){
				$this->db->from('coop_loan_guarantee_person as t1');
				$this->db->join('coop_loan as t2','t1.loan_id = t2.id ','inner');
				$this->db->where("
					t1.guarantee_person_id = '".@$row_guarantee['guarantee_person_id']."' 
					AND t2.loan_status IN('1','2')
				");
				$rs_count_guarantee = $this->db->get()->result_array();
				$n=0;
				foreach($rs_count_guarantee as $key_count => $row_count_guarantee){
					$n++;
				}
				@$rs_guarantee[$key]['count_guarantee'] = @$n;
			}
			*/
			$arr_data['row_guarantee'] = @$rs_guarantee;	
			
			//รายการซื้อ	
			$this->db->select(array('*'));
			$this->db->from('coop_loan_deduct_list');
			$this->db->where("loan_deduct_list_code != 'deduct_pay_prev_loan' AND loan_deduct_status = 1");
			$this->db->order_by('run_id ASC');
			$row = $this->db->get()->result_array();
			$arr_data['loan_deduct_list'] = $row;	
			
			$this->db->select(array(
				'loan_deduct_list_code',
				'loan_deduct_amount'
			));
			$this->db->from('coop_loan_deduct');
			$this->db->where("loan_id = '".$loan_id."'");
			$row = $this->db->get()->result_array();
			$loan_deduct = array();
			foreach($row as $key => $value){
				$loan_deduct[$value['loan_deduct_list_code']] = $value['loan_deduct_amount'];
			}
			$arr_data['loan_deduct'] = $loan_deduct;
			
			//ค่าใช้จ่าย
//			$this->db->from('coop_loan_cost');
//			$this->db->where("loan_id = '".$loan_id."' AND member_id = '".$member_id."'");
//			$this->db->limit(1);
//			$rs_cost = $this->db->get()->result_array();
//			$row_cost = @$rs_cost[0];
//			$arr_data['school_benefits'] = $row_cost['school_benefits'];
//			$arr_data['saving'] = @$row_cost['saving'];
//			$arr_data['ch_p_k'] = @$row_cost['ch_p_k'];
//			$arr_data['pension'] = @$row_cost['pension'];
//			$arr_data['k_b_k'] = @$row_cost['k_b_k'];
//			$arr_data['other'] = @$row_cost['other'];


            //ค่าใช้จ่าย
            $this->db->select('outgoing_code, outgoing_name, loan_cost_amount');
            $this->db->from("coop_outgoing");
            $this->db->join("coop_loan_cost_mod", "coop_outgoing.outgoing_code=coop_loan_cost_mod.loan_cost_code", "inner");
            $this->db->where("loan_id = '".$loan_id."' AND member_id = '".$member_id."'");
            $rs_cost = $this->db->get()->result_array();
            $arr_data['loan_cost_code'] = $rs_cost;
//            foreach ($rs_cost as $key => $val){
//                $arr_data['loan_cost_code'][$val['loan_cost_code']] = $val['loan_cost_amount'];
//            }
			
			//อสังหาทรัพย์ค้ำประกัน
			$rs_real_estate = array();
			/*$this->db->select(array('t1.*',
									't2.province_name',
									't3.amphur_name',
									't4.district_name'
								));
			$this->db->from("coop_loan_guarantee_real_estate t1");
			$this->db->join("coop_province t2","t1.province_id = t2.province_id","left");
			$this->db->join("coop_amphur t3","t1.amphur_id = t3.amphur_id","left");
			$this->db->join("coop_district t4","t1.district_id = t4.district_id","left");
			$this->db->where("t1.loan_id = '".$loan_id."'");
			$rs_real_estate = $this->db->get()->result_array();
			*/
			$arr_data['row_real_estate'] = @$rs_real_estate[0];
			$arr_data['check_type_loan'] = 'loan_atm';
			
		}

		$this->preview_libraries->template_preview('report_loan_data/coop_report_loan_detail_preview',$arr_data);
	}
	
	function loan_atm_ready_to_transfer_report(){
		$arr_data = array();
		
		//$where = 't2.loan_atm_id IS NULL ';
		//$where .= " AND t1.loan_atm_status = '1'";
		$where = '1=1';
		if(@$_GET['loan_status']!=''){
			if(@$_GET['loan_status'] == '1'){
				$where .= " AND t1.loan_atm_status IN('1','4') ";
			}else{
				$where .= " AND t1.loan_atm_status = '".$_GET['loan_status']." '";
			}
		}else{
			$where .= " AND t1.loan_atm_status IN('0','1','4','5') ";
		}
		
		// if(@$_GET['approve_date']!=''){
		// 	$approve_date_arr = explode('/',@$_GET['approve_date']);
		// 	$approve_day = $approve_date_arr[0];
		// 	$approve_month = $approve_date_arr[1];
		// 	$approve_year = $approve_date_arr[2];
		// 	$approve_year -= 543;
		// 	$approve_date = $approve_year.'-'.$approve_month.'-'.$approve_day;
		// 	$where .= " AND t1.approve_date BETWEEN '".$approve_date." 00:00:00.000' AND '".$approve_date." 23:59:59.000'";
		// }
		if($_GET['approve_date']!=''){
			$approve_date_arr = explode('/',$_GET['approve_date']);
			$approve_day = stripslashes($approve_date_arr[0]);
			$approve_month = stripslashes($approve_date_arr[1]);
			$approve_year = stripslashes($approve_date_arr[2]);
			$_GET['approve_date'] = $approve_day."/".$approve_month."/".$approve_year;
						$approve_year -= 543;
						$approve_date = $approve_year.'-'.$approve_month.'-'.$approve_day;
			$where .= " AND t1.approve_date >= '".$approve_date." 00:00:00.000'";
		}
		if($_GET['thru_date']!=''){
			$thru_date_arr = explode('/',$_GET['thru_date']);
			$thru_day = stripslashes($thru_date_arr[0]);
			$thru_month = stripslashes($thru_date_arr[1]);
			$thru_year = stripslashes($thru_date_arr[2]);
			$_GET['thru_date'] = $thru_day."/".$thru_month."/".$thru_year;
			$thru_year -= 543;
			$thru_date = $thru_year.'-'.$thru_month.'-'.$thru_day;
			$where .= " AND t1.approve_date <= '".$thru_date." 23:59:59.000'";
		}
		
		$this->db->select(array(
			't1.member_id',
			't1.loan_atm_id',
			't1.approve_date',
			't1.contract_number',
			't7.prename_short',
			't3.firstname_th',
			't3.lastname_th',
			't1.member_id',
			't1.total_amount AS loan_amount',
			't1.deduct_receipt_id',
			't3.mobile',
		));
		$this->db->from('coop_loan_atm as t1');
		/*$this->db->join('coop_loan_atm_transaction as t2','t1.loan_atm_id = t2.loan_atm_id','left');*/
		$this->db->join('coop_mem_apply as t3','t1.member_id = t3.member_id','left');
		$this->db->join('coop_prename as t7','t3.prename_id = t7.prename_id','left');
		$this->db->join('coop_user as t4','t1.admin_id = t4.user_id','left');
		$this->db->where($where);
		$this->db->order_by('t1.approve_date ASC ,loan_atm_id ASC');
		$row_loan = $this->db->get()->result_array();
		$row_sum = array();
		//echo $this->db->last_query();exit;
		foreach($row_loan as $key => $value){
			$row_loan[$key]['transfer_text'] = '';
			
			
			$this->db->select(array('t1.ref_id','t1.data_type'));
			$this->db->from('coop_loan_atm_prev_deduct as t1');
			$this->db->where("loan_atm_id = '".$value['loan_atm_id']."'");
			$row = $this->db->get()->result_array();
			$i=0;
			//echo $this->db->last_query(); echo '<br>';
			foreach($row as $key2 => $value2){
				$where_2 = " receipt_id = '".$value['deduct_receipt_id']."' ";
				
				if($value2['data_type']=='atm'){
					$where_2 .= " AND t1.loan_atm_id = '".$value2['ref_id']."'";
					$this->db->select(array('t1.*','t2.contract_number'));
					$this->db->from('coop_finance_transaction as t1');
					$this->db->join('coop_loan_atm as t2','t1.loan_atm_id = t2.loan_atm_id','inner');
					$this->db->where($where_2);
				}else if($value2['data_type']=='loan'){
					$where_2 .= " AND t1.loan_id = '".$value2['ref_id']."'";
					$this->db->select(array('t1.*','t2.contract_number'));
					$this->db->from('coop_finance_transaction as t1');
					$this->db->join('coop_loan as t2','t1.loan_id = t2.id','inner');
					$this->db->where($where_2);
				}
				
				$row_receipt = $this->db->get()->result_array();
				//echo $this->db->last_query(); echo '<br>';
				foreach($row_receipt as $key3 => $value3){
					@$row_loan[$key]['prev_loan'][$i]['contract_number'] = @$value3['contract_number'];
					@$row_loan[$key]['prev_loan'][$i]['principal'] += @$value3['principal_payment'];
					@$row_loan[$key]['prev_loan'][$i]['interest'] += @$value3['interest'];
					@$row_loan[$key]['prev_loan'][$i]['deduct_loan'] += @$value3['principal_payment']+@$value3['interest'];
				}
				$i++;
			}
			
		}
		$arr_data['row_loan'] = $row_loan;
		//echo"<pre>";print_r($row_loan);exit;
		$this->load->view('report_loan_data/loan_atm_ready_to_transfer_report',$arr_data);
	}

	public function coop_report_loan_repayment(){
		$this->libraries->template('report_loan_data/loan_report_loan_repayment',@$arr_data);
	}

	public function coop_report_loan_repayment_preview(){
		$data = $this->input->get();

		if($data['select_type_date']=="all"){

		}

		if($data['select_type_contract']!="all"){
			$contract_number = $data['contract_number'];

			$this->db->where("contract_number", $contract_number, true);
			$loan = $this->db->get("coop_loan")->result()[0];
			//loan_id
			$this->db->where("loan_id", $loan->id, true);
		}

		if($data['status']=="0"){
			$this->db->where("status", '0');
		}else if($data['status']=="1"){
			$this->db->where("status", '1');
		}

		if($data['select_type_date']!="all"){
			$tmp_start_date = explode("/", $data['start_date']);
			$new_date = ($tmp_start_date[2]-543)."-".$tmp_start_date[1]."-".$tmp_start_date[0];
			//$this->db->where("((coop_loan_repayment.update_time >= '$new_date 00:00:00' AND coop_loan_repayment.update_time <= '$new_date 23:59:59') OR (coop_loan_repayment.transaction_time >= '$new_date 00:00:00' AND coop_loan_repayment.transaction_time <= '$new_date 23:59:59'))");
			$this->db->where("(coop_loan_repayment.transaction_time >= '$new_date 00:00:00' AND coop_loan_repayment.transaction_time <= '$new_date 23:59:59')");
			
		}

		$this->db->select(array("coop_loan_repayment.*", "CONCAT(coop_prename.prename_short, coop_mem_apply.firstname_th, ' ', coop_mem_apply.lastname_th) AS fullname", "coop_loan.member_id", "coop_loan.contract_number"));
		$this->db->join("coop_loan", "coop_loan.id = coop_loan_repayment.loan_id");
		$this->db->join("coop_mem_apply", "coop_loan.member_id = coop_mem_apply.member_id");
		$this->db->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id");
		$this->db->order_by("coop_loan_repayment.update_time", "ASC");
		$arr_data['st'] = $this->db->get("coop_loan_repayment")->result();

		$arr_data['st_by_name'] = $this->db->get_where("coop_user", array(
			"user_id" => $_SESSION['USER_ID']
		))->result()[0]->user_name;

		$show_start_date = (@$arr_data['st'][0]->update_time!="") ? @$arr_data['st'][0]->update_time : $arr_data['st'][0]->transaction_time;
		$show_end_date = (@$arr_data['st'][(count($arr_data['st'])-1)]->update_time!="") ? @$arr_data['st'][(count($arr_data['st'])-1)]->update_time : $arr_data['st'][(count($arr_data['st'])-1)]->transaction_time;
		$arr_data['start_date'] = $this->center_function->ConvertToThaiDate(@$show_start_date,1,0);
		$arr_data['end_date'] = $this->center_function->ConvertToThaiDate(@$show_end_date,1,0);

		if(@$_GET['download']==""){
			$this->preview_libraries->template_preview('report_loan_data/loan_report_loan_repayment_preview',@$arr_data);
		}else{
			$this->load->view('report_loan_data/loan_report_loan_repayment_preview',@$arr_data);
		}
		
	}

	public function coop_report_loan_balance(){
        $arr_data['loan_type'] = $this->db->get('coop_loan_type')->result();

        $this->libraries->template('report_loan_data/coop_report_loan_balance', @$arr_data);
    }

	/*public function coop_report_loan_balance_preview(){

        $arr_data = array();
        $this->db->select(array('id','mem_group_name'));
        $this->db->from('coop_mem_group');
        $rs_group = $this->db->get()->result_array();
        $mem_group_arr = array();
        foreach($rs_group as $key => $row_group){
            $mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
        }
        $arr_data['mem_group_arr'] = $mem_group_arr;

        $this->db->select(array('id','loan_type'));
        $this->db->from('coop_loan_type');
        $rs_loan_type = $this->db->get()->result_array();
        $loan_type = array();
        foreach($rs_loan_type as $key => $row_loan_type){
            $loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
        }
        $arr_data['loan_type'] = $loan_type;

        $this->db->select(array('setting_value'));
        $this->db->from('coop_share_setting');
        $this->db->where("setting_id = '1'");
        $row_share_value = $this->db->get()->result_array();
        $share_value = $row_share_value[0]['setting_value'];
        $arr_data['share_value'] = $share_value;

        if(@$_GET['report_date'] != ''){
            $date_arr = explode('/',@$_GET['report_date']);
            $day = (int)@$date_arr[0];
            $month = (int)@$date_arr[1];
            $year = (int)@$date_arr[2];
            $year -= 543;
            $file_name_text = $day."_".$month_arr[$month]."_".($year+543);
        }else{
            if(@$_GET['month']!='' && @$_GET['year']!=''){
                $day = '';
                $month = @@$_GET['month'];
                $year = (@$_GET['year']-543);
                $file_name_text = @$month_arr[@$month]."_".(@$year+543);
            }else{
                $day = '';
                $month = '';
                $year = (@$_GET['year']-543);
                $file_name_text = (@$year+543);
            }
        }

        if($month!=''){
            $month_start = @$month;
            $month_end = @$month;
        }else{
            $month_start = 1;
            $month_end = 12;
        }
		
		//วันที่ และรูปแบบการค้นหา 
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
			
		}
		
		if(@$_GET['type_date'] == '1'){		
			
			$this->db->select(array('createdatetime'));
			$this->db->from('coop_loan');
			$this->db->where("loan_status = '1'");
			$this->db->order_by("createdatetime ASC");
			$this->db->limit(1);
			$rs_date_loan = $this->db->get()->result_array();
			$date_loan_min  =  date("Y-m-d", strtotime(@$rs_date_loan[0]['createdatetime']));
			
			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_atm_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_atm = $this->db->get()->result_array();
			$date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));

			if($date_loan_min < $date_loan_atm_min){
				$start = $date_loan_min;
			}else {
				$start = $date_loan_atm_min;
			}
			$end = $get_start_date;
		}else{		
			$start = $get_start_date;
			$end = $get_start_date;
		}
		//
		
		//$start  = $start_date ? date('Y-m-d',strtotime(str_replace('/', '-', $start_date) ." -543 year ")) : '';
        //$end    = $end_date   ? date('Y-m-d',strtotime(str_replace('/', '-',$end_date)." -543 year ")) : '';
        $type   = $this->input->get('loan_type')  ? $this->input->get('loan_type') : '';
        $name   = $this->input->get('loan_name')  ? $this->input->get('loan_name') : '';

        if(!empty($start) && !empty($end)){
            if($start != $end) {
                $range = " transaction_datetime BETWEEN '{$start} 00:00:00' AND '{$end} 23:59:59' ";
                $date_title = "วันที่ " . $this->center_function->ConvertToThaiDate($start, 0, 0, 0) . " ถึง " . $this->center_function->ConvertToThaiDate($end, 0, 0, 0);
            }else{
                $range = " transaction_datetime BETWEEN '{$start} 00:00:00' AND '{$end} 23:59:59' ";
                $date_title = "วันที่ " . $this->center_function->ConvertToThaiDate($start, 0, 0, 0);
            }

        }else if(!empty($start) && empty($end)){
            $range = " transaction_datetime >= '{$start}' ";
            $date_title = "วันที่ ".$this->center_function->ConvertToThaiDate($start, 0,0,0)." ถึง ".$this->center_function->ConvertToThaiDate(date('Y-m-d'), 0,0,0);
        }else if(empty($start) && !empty($end)){
            $range = " transaction_datetime <= '{$end}' ";
            $date_title = "วันที่ ".$this->center_function->ConvertToThaiDate($end, 0,0,0);
        }else{
            $range = " 1=1 ";
            $date_title = "";
        }

        $arr_data['date_title'] = $date_title;

        $wh = "";
        $wh .= $type    ? " AND loan_type_id = '{$type}' " : "";
        $wh .= $name     ? " AND loan_name_id = '{$name}' " : "";

        $wh .= " AND loan_status in (1, 3, 4) ";

        $loan = "SELECT * FROM (
(SELECT t1.member_id AS member_id,t1.id AS loan_id,t1.loan_status AS loan_status,t1.contract_number AS contract_number,t1.createdatetime AS createdatetime,t1.period_amount AS period_amount,t1.loan_amount AS loan_amount,t1.loan_type AS loan_type, t1.loan_reason AS loan_reason, t1.pay_type, 'NORMAL' AS type FROM coop_loan t1 ) UNION ALL (
SELECT t1.member_id AS member_id,t1.loan_atm_id AS loan_id,t1.loan_atm_status AS loan_status,t1.contract_number AS contract_number,t1.createdatetime AS createdatetime,IF (t1.max_period IS NULL,t2.max_period,t1.max_period) AS period_amount,t1.total_amount_approve AS loan_amount,'99' AS loan_type, t1.loan_reason AS loan_reason, '1' as pay_type, 'ATM' AS type FROM coop_loan_atm t1 JOIN coop_loan_atm_setting t2)) T1";

        $loan_transaction = "SELECT * FROM (
SELECT T1.*,'NORMAL' AS type FROM coop_loan_transaction T1 INNER JOIN (SELECT loan_id,MAX(transaction_datetime) AS transaction_datetime FROM coop_loan_transaction WHERE {$range} GROUP BY loan_id) T2 ON T1.loan_id=T2.loan_id AND T1.transaction_datetime=T2.transaction_datetime UNION ALL 
SELECT T1.*,'ATM' AS type FROM coop_loan_atm_transaction T1 INNER JOIN (SELECT loan_atm_id,MAX(transaction_datetime) AS transaction_datetime FROM coop_loan_atm_transaction WHERE {$range} GROUP BY loan_atm_id) T2 ON T1.loan_atm_id=T2.loan_atm_id AND T1.transaction_datetime=T2.transaction_datetime) T2 ";

        $this->db->select(array('t1.loan_id','t1.contract_number','t1.createdatetime','t1.member_id','t1.period_amount',
            't1.loan_amount', 't2.loan_amount_balance', 't1.type','t6.loan_type', 't5.loan_name', 't7.loan_reason', 't1.pay_type', 't1.loan_status'))
            ->from('('.$loan.') `t1`')
            ->join('('.$loan_transaction.') t2', 't1.loan_id = t2.loan_id and t1.type = t2.type', 'inner')
            ->join("(SELECT*FROM coop_loan_name UNION ALL SELECT 'เงินกู้ฉุกเฉิน ATM' AS `loan_name`,(NULL),99,7,'') t5", 't1.loan_type = t5.loan_name_id', 'left')
            ->join('coop_loan_type t6', 't5.loan_type_id = t6.`id`', 'left')
            ->join('coop_loan_reason t7', '`t1`.`loan_reason` = `t7`.`loan_reason_id`', 'left')
            ->where(' 1 = 1 '.$wh)
            ->group_by("t1.loan_id")
            ->order_by('t1.member_id ASC');

        $arr_data['data'] =  $this->db->get()->result_array();
		//echo '<pre>'; print_r($arr_data['data']); echo '</pre>'; exit;
        if($this->input->get('debug')){
            echo '<pre>';
            print_r($this->input->get());


            echo $start." "."<br>";
            echo $end." "."<br>";

            echo $this->db->last_query();
            exit;
        }

//        $this->db->select(array('loan_id','contract_number','createdatetime','member_id','employee_id','prename_short','firstname_th','lastname_th','level','period_amount','loan_amount','money_period_1','loan_reason'));
//        $this->db->from('coop_report_loan_normal_excel_1');
//        $this->db->where("loan_type = '".@$_GET['loan_type']."' AND loan_status IN ('1','2','4') {$where}");
//        $this->db->order_by('createdatetime ASC');
//        $rs = $this->db->get()->result_array();
//        $data = array();
//        $i = 0;


        $arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

        $this->preview_libraries->template_preview('report_loan_data/coop_report_loan_balance_preview',$arr_data);
    }
*/	
	public function coop_report_loan_balance_preview(){

        if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}

		if(@$_GET['type_date'] == '1'){
			$this->db->select(array('share_date'));
			$this->db->from('coop_mem_share');
			$this->db->where("share_status IN ('1', '2')");
			$this->db->order_by("share_date ASC");
			$this->db->limit(1);
			$rs_date_share = $this->db->get()->result_array();
			$date_share_min  =  date("Y-m-d", strtotime(@$rs_date_share[0]['share_date']));

			$this->db->select(array('createdatetime'));
			$this->db->from('coop_loan');
			$this->db->where("loan_status = '1'");
			$this->db->order_by("createdatetime ASC");
			$this->db->limit(1);
			$rs_date_loan = $this->db->get()->result_array();
			$date_loan_min  =  date("Y-m-d", strtotime(@$rs_date_loan[0]['createdatetime']));
			
			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_transaction = $this->db->get()->result_array();
			$date_loan_transaction_min  =  date("Y-m-d", strtotime(@$rs_date_loan_transaction[0]['transaction_datetime']));
			
			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_atm_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_atm = $this->db->get()->result_array();
			$date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));

			if($date_loan_transaction_min < $date_share_min){
				$start_date = $date_loan_transaction_min;
			}else if($date_share_min < $date_loan_min){
				$start_date = $date_share_min;
			}else if($date_loan_min < $date_loan_atm_min){
				$start_date = $date_loan_min;
			}else if($date_loan_atm_min < $date_share_min){
				$start_date = $date_loan_atm_min;
			}else{
				$start_date = $date_share_min;
			}
			$end_date = $get_start_date;
		}else{		
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}

		$where_date = "";		
		$where_date_loan = "";		
		$where_date_loan_atm = "";		
		$where_date_loan_atm_transaction = "";		
		$where_date_loan_transaction = "";		
		if(@$_GET['start_date'] != ''){
			$where_date_loan .= " AND coop_loan.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm .= " AND coop_loan_atm.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm_transaction .= " AND coop_loan_atm_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_transaction .= " AND coop_loan_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}		

		$this->db->select(array('id','loan_type'));
        $this->db->from('coop_loan_type');
        $rs_loan_type = $this->db->get()->result_array();
        $loan_type = array();
        foreach($rs_loan_type as $key => $row_loan_type){
            $loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
        }
        $arr_data['loan_type'] = $loan_type;

		if(!empty($start_date) && !empty($end_date)){
            if($start_date != $end_date) {
                $date_title = "วันที่ " . $this->center_function->ConvertToThaiDate($start_date, 0, 0, 0) . " ถึง " . $this->center_function->ConvertToThaiDate($end_date, 0, 0, 0);
            }else{
                $date_title = "วันที่ " . $this->center_function->ConvertToThaiDate($start_date, 0, 0, 0);
            }

        }else if(!empty($start_date) && empty($end_date)){
            $date_title = "วันที่ ".$this->center_function->ConvertToThaiDate($start_date, 0,0,0)." ถึง ".$this->center_function->ConvertToThaiDate(date('Y-m-d'), 0,0,0);
        }else if(empty($start_date) && !empty($end_date)){
            $date_title = "วันที่ ".$this->center_function->ConvertToThaiDate($end_date, 0,0,0);
        }else{
            $date_title = "";
        }
		
		$where_type_loan = '';
		if(@$_GET['loan_type'] != ''){
			$where_type_loan .= " AND t5.loan_type_id = '".@$_GET['loan_type']."'"; 
		}
		
		if(@$_GET['loan_name'] != ''){
			$where_type_loan .= " AND t5.loan_type_name_id = '".@$_GET['loan_name']."'"; 
		}

        $arr_data['date_title'] = $date_title;
		$sql = "SELECT coop_mem_apply.member_id, coop_mem_apply.prename_id, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_mem_apply.department, coop_mem_apply.faction, coop_mem_apply.level,
				coop_prename.prename_full,
				t1.mem_group_id as id, t1.mem_group_name as name,
				t2.mem_group_name as sub_name,
				t3.mem_group_name as main_name,
				t5.loan_id,t5.loan_atm_id, t5.loan_amount_balance, t5.contract_number,
				t5.pay_type, t5.loan_name AS loan_name, t5.loan_type AS loan_type, t5.loan_reason AS loan_reason,
				t5.loan_type_id AS loan_type_id,t5.loan_type_name_id AS loan_type_name_id,
				t5.period_amount, t5.createdatetime, t5.loan_amount, t5.loan_status
				FROM ((SELECT member_id, `prename_id`, `firstname_th`, `lastname_th`, `department`, `faction`, level FROM coop_mem_apply) AS coop_mem_apply)
				LEFT JOIN `coop_prename` ON `coop_prename`.`prename_id` = `coop_mem_apply`.`prename_id`
				LEFT JOIN `coop_mem_group` as `t1` ON `t1`.`id` = `coop_mem_apply`.`level`
				LEFT JOIN `coop_mem_group` as `t2` ON `t2`.`id` = `t1`.`mem_group_parent_id`
				LEFT JOIN `coop_mem_group` as `t3` ON `t3`.`id` = `t2`.`mem_group_parent_id`
				LEFT JOIN (
					(SELECT
							t3.member_id,
							t3.contract_number,
							t3.period_now,
							t1.loan_transaction_id,
							t1.loan_id,
							'' AS loan_atm_id,
							t1.loan_amount_balance,
							t1.transaction_datetime,
							t3.pay_type,
							t4.loan_name AS loan_name,
							t5.loan_type AS loan_type,
							t6.loan_reason AS loan_reason,
							t3.loan_type AS loan_type_name_id,
							t5.id AS loan_type_id,
							t3.period_amount,
							t3.createdatetime,
							t3.loan_amount,
							t3.loan_status
						FROM
							coop_loan_transaction AS t1
						LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id
						LEFT JOIN coop_loan_name AS t4 ON t3.loan_type = t4.loan_name_id
						LEFT JOIN coop_loan_type AS t5 ON t4.loan_type_id = t5.id
						LEFT JOIN coop_loan_reason AS t6 ON t3.loan_reason = t6.loan_reason_id
						WHERE
							t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
						GROUP BY
							t1.loan_id
						ORDER BY
							t1.loan_id DESC,
							t1.loan_transaction_id DESC
					)
					UNION ALL
					(
						SELECT
							t3.member_id,
							t3.contract_number,
							'' AS period_now,
							t1.loan_atm_transaction_id,
							'' AS loan_id,
							t1.loan_atm_id,
							t1.loan_amount_balance AS loan_amount_balance_atm,
							t1.transaction_datetime,
							'1' AS pay_type,
							'เงินกู้ฉุกเฉิน ATM' AS loan_name,
							'เงินกู้ฉุกเฉิน' AS loan_type,
							'' AS loan_reason,
							'0' AS loan_type_name_id,
							'7' AS loan_type_id,
							IF(t3.max_period <> '',
								t3.max_period,
								(SELECT max_period FROM coop_loan_atm_setting LIMIT 1)
							) AS period_amount,
							t3.createdatetime,
							t3.total_amount_approve,
							t3.loan_atm_status
						FROM
							coop_loan_atm_transaction AS t1
						LEFT JOIN coop_loan_atm AS t3 ON t1.loan_atm_id = t3.loan_atm_id
						WHERE
							t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
						GROUP BY
							t1.loan_atm_id
						ORDER BY
							t1.loan_atm_id DESC,
							t1.loan_atm_transaction_id DESC
					)
				) AS t5 ON coop_mem_apply.member_id = t5.member_id
				WHERE (t5.loan_id != '' OR t5.loan_atm_id != '') {$where_type_loan}
				";
		$result = $this->db->query($sql)->result_array();
		//echo $this->db->last_query();exit;
		$member_ids = array_column($result, 'member_id');
		
		//Get Lastest Loan
		$shares = $this->db->query("SELECT `t1`.`member_id`, `t1`.`share_date`, `t1`.`share_period`, `t1`.`share_collect_value`, `t1`.`share_collect`, `t1`.`share_status`,  `t1`.`share_payable`, `t1`.`share_payable_value`
									FROM `coop_mem_share` as `t1`
									INNER JOIN (SELECT member_id, share_date, MAX(cast(share_date as Datetime)) as max FROM coop_mem_share WHERE share_date BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by member_id)
											as t2 ON `t1`.`member_id` = `t2`.`member_id` AND `t1`.`share_date` = `t2`.`max`
									WHERE `t1`.`share_collect_value` > 0 AND t1.member_id IN  (".implode(',',$member_ids).") ORDER BY t1.share_id DESC
									")->result_array();
		$share_members = array_column($shares, 'member_id');
		//Get Lastest Loan Information
		$loan_ids = array_column($result, 'loan_id');
		
		$check_list_loan = implode(',',array_filter($loan_ids));
		if($check_list_loan != ''){
			$loans = $this->db->query("SELECT `t1`.`loan_transaction_id`, `t1`.`loan_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
										FROM `coop_loan_transaction` as `t1`
										INNER JOIN (SELECT loan_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_id)
												as t2 ON `t1`.`loan_id` = `t2`.`loan_id` AND `t1`.`transaction_datetime` = `t2`.`max`
										WHERE t1.loan_id IN  (".implode(',',array_filter($loan_ids)).")
										ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
										")->result_array();
			$loan_members = array_column($loans, 'loan_id');
		}
		
		//echo $this->db->last_query();exit;
		//Get Lastest Loan ATM Information
		$loan_atm_ids = array_column($result, 'loan_atm_id');
		
		$check_list_loan_atm = implode(',',array_filter($loan_atm_ids));
		if($check_list_loan_atm != ''){
			$loan_atms = $this->db->query("SELECT t1.loan_atm_transaction_id, `t1`.`loan_atm_id`, `t1`.`transaction_datetime`,
										t1.loan_amount_balance AS loan_amount_balance
			
										FROM `coop_loan_atm_transaction` as `t1`
										INNER JOIN (SELECT loan_atm_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_atm_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_atm_id)
												as t2 ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id` AND `t1`.`transaction_datetime` = `t2`.`max`
										LEFT JOIN `coop_loan_atm_detail` AS `t3` ON `t1`.`loan_atm_id` = `t3`.`loan_atm_id`	AND `t1`.`transaction_datetime` = `t3`.`loan_date`
										LEFT JOIN `coop_finance_transaction` AS `t4` ON `t1`.`receipt_id` = `t4`.`receipt_id`	AND `t1`.`loan_atm_id` = `t4`.`loan_atm_id`
										LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
										WHERE t1.loan_atm_id IN  (".implode(',',array_filter($loan_atm_ids)).")
										GROUP BY `t1`.`loan_atm_id`
										ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_atm_transaction_id` DESC
										")->result_array();
			$loan_atm_members = array_column($loan_atms, 'loan_atm_id');						
		}
		
		
		//echo $this->db->last_query();exit;
		$run_index = 0;

		//$check_row = "xx";
		$index = 0;
		$row['data'] = array();

		foreach($result AS $key2=>$value2){
			//$loan_type_code = @$arr_loan_type_code[$value2['loan_type_id']];				
			if(@$value2['loan_atm_id'] != ''){
				$arr_data['data'][$run_index]['loan_id'] = @$value2['loan_atm_id'];
				$arr_data['data'][$run_index]['loan_amount_balance'] = $loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'];				
				$arr_data['data'][$run_index]['type'] = 'ATM';
			}else{							
				$arr_data['data'][$run_index]['loan_id'] = @$value2['loan_id'];
				$arr_data['data'][$run_index]['loan_amount_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];				
				$arr_data['data'][$run_index]['type'] = 'NORMAL';
			}
			$arr_data['data'][$run_index]['contract_number'] = @$value2['contract_number'];
			$arr_data['data'][$run_index]['createdatetime'] = @$value2['createdatetime'];					
			$arr_data['data'][$run_index]['period_amount'] = @$value2['period_amount'];					
			$arr_data['data'][$run_index]['loan_amount'] = @$value2['loan_amount'];	
			$arr_data['data'][$run_index]['member_id'] = @$value2['member_id'];					
			$arr_data['data'][$run_index]['loan_reason'] = @$value2['loan_reason'];						
			$arr_data['data'][$run_index]['pay_type'] = @$value2['pay_type'];						
			$arr_data['data'][$run_index]['loan_status'] = @$value2['loan_status'];
			$arr_data['data'][$run_index]['loan_type'] = @$value2['loan_type'];
			$arr_data['data'][$run_index]['loan_name'] = @$value2['loan_name'];
			$run_index++;				
				
		}
		//echo '<pre>'; print_r($arr_data['data']); echo '</pre>'; exit;
        $arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

        $this->preview_libraries->template_preview('report_loan_data/coop_report_loan_balance_preview',$arr_data);
    }


    public function coop_report_loan_person_detail(){

        $loan_id = !empty($this->input->get('loan_id')) ? $this->input->get('loan_id', true) : exit() ;
        $arr_data = $this->report_loan_data_model->get_data_loan_all_report_personal($loan_id);

        $this->preview_libraries->template_preview('report_loan_data/bbcoop_report_loan_person_detail', $arr_data);
    }

	public function coop_report_loan_person(){
		$data=$_GET;
		$arr_data=$this->Report_loan_person_model->coop_report_loan_person_model($data);
		$this->libraries->template('report_loan_data/coop_report_loan_person',$arr_data);
	}

    function coop_report_loan_person_pdf(){
        // echo "<pre>";print_r($_GET);exit;
        $data=$_GET;
		$arr_data=$this->Report_loan_person_model->coop_report_loan_person_pdf_model($data);
		$this->load->view('report_loan_data/coop_report_loan_person_pdf',$arr_data);
	}
	public function coop_report_loan_request()
	{
		$arr_data = array();
		$arr_data['loan_type'] =$this->report_loan_data_model->get_coop_loan_type(array('id','loan_type')); 
		$this->libraries->template('report_loan_data/coop_report_loan_request',$arr_data);
	}
	public function coop_report_loan_request_preview()
	{
		$arr_data = array();
		$type_date = $_GET['type_date'];
		$loan_type = $_GET['loan_type'];
		$loan_name  = $_GET['loan_name'];
		$start_date = date('Y-m-d H:i:s');
		$end_date = date('Y-m-d H:i:s');
		$where = 'where 1=1 ';
		if($type_date =='1'){
			$start_date = $this->center_function->ConvertToSQLDate($_GET['start_date']);
			$end_date = $start_date;
			$where  .= "and t1.createdatetime <= '$start_date 23:59:59' ";
		}
		if($type_date =="2"){
			$start_date = $this->center_function->ConvertToSQLDate($_GET['start_date']);
			$end_date =$this->center_function->ConvertToSQLDate($_GET['end_date']);
			$where .= "and t1.createdatetime >= '$start_date 00:00:00'and t1.createdatetime <= '$end_date 23:59:59' ";
		}
		if($loan_type !=''){
			$where .= "and t3.id = '$loan_type' ";
		}
		if($loan_name !=''){
			$where .= "and t2.loan_name_id = '$loan_name' ";
		}
		$this->report_loan_data_model->coop_report_loan_request_temp_base_data($where);
		$maindata = $this->report_loan_data_model->get_coop_report_loan_request_data();
		$deduct = $this->report_loan_data_model->get_coop_report_loan_request_deduct_data(array('loan_id','loan_deduct_amount','loan_deduct_list_code'));
		$deduct_data = array();
		foreach($deduct  as $k => $val){
			$deduct_data[$val['loan_id']][$val['loan_deduct_list_code']] += $val['loan_deduct_amount'];
		}
	
		$arr_data['head_line'] = 6;
		$arr_data['max_line'] =27;
		$arr_data['max_page'] = ceil(($arr_data['head_line'] + count($maindata))/($arr_data['max_line']-2));
		$arr_data['deduct'] = $deduct_data;
		$arr_data['data'] = $maindata;
		if($_GET['export'] == 'excel'){
			$this->load->view('report_loan_data/coop_report_loan_request_excel',$arr_data);
			return true;
		}
		$this->preview_libraries->template_preview('report_loan_data/coop_report_loan_request_preview',$arr_data);
	}
	public function check_coop_report_loan_request()
	{
		$arr_data = array();
		$type_date = $_POST['type_date'];
		$loan_type = $_POST['loan_type'];
		$loan_name  = $_POST['loan_name'];
		$start_date = date('Y-m-d H:i:s');
		$end_date = date('Y-m-d H:i:s');
		$where= array();
		if($type_date =='1'){
			$start_date = $this->center_function->ConvertToSQLDate($_POST['start_date']);
			$end_date = $start_date;
			$where['createdatetime <=']= $start_date ." 23:59:59";
		}
		if($type_date =="2"){
			$start_date = $this->center_function->ConvertToSQLDate($_POST['start_date']);
			$end_date =$this->center_function->ConvertToSQLDate($_POST['end_date']);
			$where['createdatetime >=']= $start_date ." 00:00:00";
			$where['createdatetime <=']= $end_date ." 23:59:59";
		}
		if($loan_type !=''){
			$where['t3.id']=$loan_type;
		}
		if($loan_name !=''){
			$where['t2.loan_name_id']=$loan_name;
		}
		$count = $this->report_loan_data_model->check_coop_report_loan_request($where)[0]['Count'];
		if($count >0){
			$R = [
				'result'=>true,
				'count'=>$count
			];
			echo json_encode($R);
		}else{
			$R = [
				'result'=>false,
				'count'=>$count
			];
			echo json_encode($R);
		}
	}
}
