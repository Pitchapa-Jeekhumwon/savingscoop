<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Content-Type:text/json;charset=utf-8");
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: X-Requested-With, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');
class Admin extends CI_Controller {

    public $CI;

	function __construct()
	{
		parent::__construct();
        $this->load->library(array('libTFPDF'));
        $this->CI =&get_instance();

	}
	public function index()
	{
		exit;
	}

	public function receipt_pdf(){
		$data_arr = array();
		$this->db->select('*');
		$this->db->from("coop_receipt");
		$this->db->where("receipt_id = '".$this->input->get('receipt_id')."'");
		$row = $this->db->get()->result_array();

		$data_arr['receipt_id'] = $row[0]['receipt_id'];
		$data_arr['member_id'] = $row[0]['member_id'];

		$this->db->select('*');
		$this->db->from("coop_mem_apply");
		$this->db->where("member_id = '".$data_arr['member_id']."'");
		$row = $this->db->get()->result_array();

		$data_arr['name'] = $row[0]['firstname_th'].' '.$row[0]['lastname_th'];
		$data_arr['member_data'] = $row[0];


		$mShort = array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
		$data_arr['str'] = "" ;
		$datetime = date("Y-m-d H:i:s");

		$tmp = explode(" ",$datetime);
		if( $tmp[0] != "0000-00-00" ) {
			$d = explode( "-" , $tmp[0]);

			$month = $mShort ;

			$str = $d[2] . " " . $month[(int)$d[1]].  " ".($d[0]>2500?$d[0]:$d[0]+543);

			$t = strtotime($datetime);
			$data_arr['str']  =$data_arr['str']. " ".date("H:i" , $t ) . " น." ;
		}

		$this->db->select('setting_value');
		$this->db->from("coop_share_setting");
		$this->db->where("setting_id = '1'");
		$row = $this->db->get()->result_array();

		$data_arr['share_value'] = $row[0]['setting_value'];

		$this->db->select(array('id','mem_group_name'));
		$this->db->from("coop_mem_group");
		$row = $this->db->get()->result_array();

		$mem_group_arr = array();
		foreach($row as $key => $value){
			$mem_group_arr[$value['id']] = $value['mem_group_name'];
		}

		$data_arr['mem_group_arr'] = $mem_group_arr;

		$this->db->select(array(
			'coop_finance_transaction.*',
			'coop_loan.contract_number',
			'coop_loan.loan_amount_balance',
			'coop_loan.interest_per_year',
			'coop_loan_type.loan_type',
			'coop_account_list.account_list'));
		$this->db->from("coop_finance_transaction");
		$this->db->join('coop_loan', 'coop_finance_transaction.loan_id = coop_loan.id', 'left');
		$this->db->join('coop_loan_type', 'coop_loan.loan_type = coop_loan_type.id', 'left');
		$this->db->join('coop_account_list', 'coop_finance_transaction.account_list_id = coop_account_list.account_id', 'left');
		$this->db->where("coop_finance_transaction.receipt_id = '".$data_arr['receipt_id']."'");
		$row = $this->db->get()->result_array();
		$data_arr['transaction_data'] = $row;

		//ลายเซ็นต์
		$date_signature = date('Y-m-d');
		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("start_date <= '{$date_signature}'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$data_arr['signature'] = @$row[0];
		
		$this->load->view('admin/receipt_pdf',$data_arr);
	}
	
	public function receipt_month_pdf(){
		$data_get = $this->input->get();
		//echo"<pre>";print_r($data_get);exit;
		$date_month_start = (@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01';
		$date_month_end = date('Y-m-t',strtotime((@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01'));
		$data_arr = array();
		
		$this->db->select('setting_value');
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row = $this->db->get()->result_array();
		$row_share_value = $row[0];
		$share_value = $row_share_value['setting_value'];
		$data_arr['share_value'] = $share_value;
		
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$row = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($row as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$data_arr['mem_group_arr'] = $mem_group_arr;
		
		$where = "1=1 AND member_status = '1' ";
		//เรียกเก็บทุนเรือนหุ้นแค่รายเดือน
		//$where .= " AND apply_type_id = '1' ";
		
		if($data_get['choose_receipt'] == '2'){
			if($data_get['member_id_from']!='' && $data_get['member_id_to']!=''){
				$where .= " AND coop_mem_apply.member_id >= ".$data_get['member_id_from']." AND coop_mem_apply.member_id <= ".$data_get['member_id_to']." ";
			}else if($data_get['member_id_from']!='' && $data_get['member_id_to']==''){
				$where .= " AND coop_mem_apply.member_id >= ".$data_get['member_id_from']."";
			}else if($data_get['member_id_from']=='' && $data_get['member_id_to']!=''){
				$where .= " AND coop_mem_apply.member_id <= ".$data_get['member_id_to']."";
			}else if($data_get['member_id_from'] == $data_get['member_id_to']){
				$where .= " AND coop_mem_apply.member_id = '".$data_get['member_id_from']."'";
			}
		}else if($data_get['choose_receipt'] == '3'){
			if($data_get['employee_id_from']!='' && $data_get['employee_id_to']!=''){
				$where .= " AND employee_id >= '".$data_get['employee_id_from']."' AND employee_id <= '".$data_get['employee_id_to']."' ";
			}else if($data_get['employee_id_from']!='' && $data_get['employee_id_to']==''){
				$where .= " AND employee_id >= '".$data_get['employee_id_from']."'";
			}else if($data_get['employee_id_from']=='' && $data_get['employee_id_to']!=''){
				$where .= " AND employee_id <= '".$data_get['employee_id_to']."'";
			}else if($data_get['employee_id_from'] == $data_get['employee_id_to']){
				$where .= " AND employee_id = '".$data_get['employee_id_from']."'";
			}
		}
		
		$this->db->select(array('coop_mem_apply.*','coop_receipt.receipt_id','coop_receipt.pay_type','coop_receipt.receipt_datetime'));
		$this->db->from('coop_mem_apply');
		$this->db->join('coop_receipt', "coop_receipt.member_id = coop_mem_apply.member_id AND month_receipt = '".$data_get['month']."' AND year_receipt = '".$data_get['year']."'", 'inner');
		$this->db->where($where);
		$this->db->order_by('member_id ASC');
		$row = $this->db->get()->result_array();
		$data_arr['data'] = $row;
		$data_arr['data_get'] = $data_get;
		
		//ลายเซ็นต์
		$date_signature = (@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01';
		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("start_date <= '{$date_signature}'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$data_arr['signature'] = @$row[0];
		$data_arr['pay_type'] = array('0'=>'เงินสด','1'=>'โอนเงิน');
		$this->load->view('admin/receipt_month_pdf',$data_arr);
	}
	
	public function receipt_month_pdf_bk(){
		$data_get = $this->input->get();
		//echo"<pre>";print_r($data_get);exit;
		$date_month_start = (@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01';
		$date_month_end = date('Y-m-t',strtotime((@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01'));
		$data_arr = array();
		
		$this->db->select('setting_value');
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row = $this->db->get()->result_array();
		$row_share_value = $row[0];
		$share_value = $row_share_value['setting_value'];
		$data_arr['share_value'] = $share_value;
		
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$row = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($row as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$data_arr['mem_group_arr'] = $mem_group_arr;
		
		$where = "1=1 AND member_status = '1' ";
		//เรียกเก็บทุนเรือนหุ้นแค่รายเดือน
		//$where .= " AND apply_type_id = '1' ";
		
		if($data_get['choose_receipt'] == '2'){
			if($data_get['member_id_from']!='' && $data_get['member_id_to']!=''){
				$where .= " AND coop_mem_apply.member_id >= ".$data_get['member_id_from']." AND coop_mem_apply.member_id <= ".$data_get['member_id_to']." ";
			}else if($data_get['member_id_from']!='' && $data_get['member_id_to']==''){
				$where .= " AND coop_mem_apply.member_id >= ".$data_get['member_id_from']."";
			}else if($data_get['member_id_from']=='' && $data_get['member_id_to']!=''){
				$where .= " AND coop_mem_apply.member_id <= ".$data_get['member_id_to']."";
			}else if($data_get['member_id_from'] == $data_get['member_id_to']){
				$where .= " AND coop_mem_apply.member_id = '".$data_get['member_id_from']."'";
			}
		}else if($data_get['choose_receipt'] == '3'){
			if($data_get['employee_id_from']!='' && $data_get['employee_id_to']!=''){
				$where .= " AND employee_id >= '".$data_get['employee_id_from']."' AND employee_id <= '".$data_get['employee_id_to']."' ";
			}else if($data_get['employee_id_from']!='' && $data_get['employee_id_to']==''){
				$where .= " AND employee_id >= '".$data_get['employee_id_from']."'";
			}else if($data_get['employee_id_from']=='' && $data_get['employee_id_to']!=''){
				$where .= " AND employee_id <= '".$data_get['employee_id_to']."'";
			}else if($data_get['employee_id_from'] == $data_get['employee_id_to']){
				$where .= " AND employee_id = '".$data_get['employee_id_from']."'";
			}
		}
		
		$this->db->select(array('coop_mem_apply.*','coop_receipt.receipt_id'));
		$this->db->from('coop_mem_apply');
		$this->db->join('coop_receipt', "coop_receipt.member_id = coop_mem_apply.member_id AND month_receipt = '".$data_get['month']."' AND year_receipt = '".$data_get['year']."'", 'left');
		$this->db->where($where);
		$this->db->order_by('member_id ASC');
		$row = $this->db->get()->result_array();
		$data_arr['data'] = $row;
		$data_arr['data_get'] = $data_get;
		
		//ลายเซ็นต์
		$date_signature = (@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01';
		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("start_date <= '{$date_signature}'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$data_arr['signature'] = @$row[0];
		
		$this->db->select(array('deduct_id','deduct_code','deduct_detail','deduct_type','deduct_format','deposit_type_id','deposit_amount'));
		$this->db->from('coop_deduct');
		$this->db->order_by('deduct_seq ASC');
		$deduct_list = $this->db->get()->result_array();
		$data_arr['deduct_list'] = @$deduct_list;
		
		$this->db->select(array('cremation_id','cremation_name_short'));
		$this->db->from('coop_cremation_data');
		$cremation_list = $this->db->get()->result_array();
		$cremation_arr = array();
		foreach($cremation_list as $key => $value){
			$this->db->select(array('cremation_id','pay_type','pay_per_person'));
			$this->db->from('coop_cremation_data_detail');
			$this->db->where("start_date <= '".$date_month_end ."' AND cremation_id = '".$value['cremation_id']."'");
			$this->db->order_by('start_date DESC');
			$this->db->limit(1);			
			$cremation_detail = $this->db->get()->result_array();
			$cremation_detail = @$cremation_detail[0];
			//echo $this->db->last_query(); echo '<br>';
			if($cremation_detail['pay_type'] == '1'){
				$this->db->select(array('cremation_transfer_id'));
				$this->db->from('coop_cremation_transfer as t1');
				$this->db->join('coop_cremation_request_receive as t2','t1.cremation_request_id = t2.cremation_request_id','inner');
				$this->db->where("
					t1.date_transfer >= '".$date_month_start."' 
					AND t1.date_transfer <= '".$date_month_end."'
					AND t2.cremation_type_id = '".$value['cremation_id']."'
				");
				$cremation_req = $this->db->get()->result_array();
				
				$cremation_arr[$value['cremation_id']]['cremation_name_short'] = $value['cremation_name_short'];
				$cremation_arr[$value['cremation_id']]['pay_per_person'] = $cremation_detail['pay_per_person'];
				$cremation_arr[$value['cremation_id']]['count_req'] = count($cremation_req);
				$cremation_arr[$value['cremation_id']]['total_pay'] = $cremation_arr[$value['cremation_id']]['pay_per_person']*$cremation_arr[$value['cremation_id']]['count_req'];
			}
		}
		
		$data_arr['cremation_arr'] = $cremation_arr;
		
		$this->load->view('admin/receipt_month_pdf',$data_arr);
	}
	
	function run_script_deposit_interest(){
		$this->db->select(array(
			't1.member_id',
			't2.account_id',
			't2.type_id'
		));
		$this->db->from('coop_mem_apply as t1');
		$this->db->join('coop_maco_account as t2','t1.member_id = t2.mem_id','inner');
		$this->db->where("t2.account_status = '0'");
		$rs_member = $this->db->get()->result_array();
		$sum_account_interest = 0;
		foreach($rs_member as $key => $row_member){
			
			$this->db->select(array(
				't1.transaction_id',
				't1.transaction_balance', 
				't1.transaction_time',
				't1.account_id'
			));
			$this->db->from('coop_account_transaction as t1');
			$this->db->join('coop_maco_account as t2','t1.account_id = t2.account_id','inner');
			$this->db->where("
				t2.mem_id = '".$row_member['member_id']."'
				AND t1.account_id = '".$row_member['account_id']."'
				AND t2.account_status = '0'
			");
			$this->db->order_by('t1.transaction_id ASC');
			$rs = $this->db->get()->result_array();
			$transaction_arr = array();
			foreach($rs as $key2 => $row){
				//echo $row['transaction_id']." : ".$row['transaction_balance']." : ".$row['transaction_time']."<br>";
				$transaction_arr[] = $row;
			}
			
			//$interest_rate = @$row_member['interest_rate'];
			
			$transaction = array();
			foreach($transaction_arr as $key => $value){
				$transaction[$key]['transaction_id'] = $value['transaction_id'];
				$transaction[$key]['date_start'] = date('Y-m-d',strtotime($value['transaction_time']));
				$transaction[$key]['transaction_balance'] = $value['transaction_balance'];
				$transaction[$key]['account_id'] = $value['account_id'];
				if(@$transaction_arr[($key+1)]['transaction_time'] != ''){
					$transaction[$key]['date_end'] = date('Y-m-d',strtotime($transaction_arr[($key+1)]['transaction_time']));
				}else{
					$transaction[$key]['date_end'] = date('Y-m-d');
				}
			}
			
			$this->db->select(array(
				'interest_rate',
				'start_date'
			));
			$this->db->from('coop_interest');
			$this->db->where("type_id = '".@$row_member['type_id']."'");
			$this->db->order_by("start_date ASC");
			$row_interest_rate = $this->db->get()->result_array();
			foreach($row_interest_rate as $key2 => $value2){
				if(@$row_interest_rate[($key2+1)]['start_date'] != ''){
					$row_interest_rate[$key2]['end_date'] = date('Y-m-d',strtotime($row_interest_rate[($key2+1)]['start_date']));
				}else{
					$row_interest_rate[$key2]['end_date'] = date('Y-m-d');
				}
			}
			
			$i=0;
			$transaction_new = array();
			foreach($transaction as $key => $value){
				$transaction_new[$i] = $value;
				foreach($row_interest_rate as $key2 => $value2){
					if(strtotime($value['date_start']) > strtotime($value2['start_date']) && strtotime($value['date_start']) < strtotime($value2['end_date'])){
						if(strtotime($value['date_start']) > strtotime($value2['start_date'])){
							$transaction_new[$i]['interest_rate'] = $value2['interest_rate'];
						}
						if(strtotime($value['date_end']) > strtotime($value2['end_date'])){
							$transaction_new[$i]['date_end'] = $value2['end_date'];
							
							$i += 1;
							$transaction_new[$i] = $value;
							$transaction_new[$i]['date_start'] = $value2['end_date'];
							$transaction_new[$i]['interest_rate'] = $row_interest_rate[($key2+1)]['interest_rate'];
						}
					}
				}
				$i++;
			}
			$transaction = $transaction_new;
			
			$account_interest = 0;
			foreach($transaction as $key => $value){
				$interest_rate = @$value['interest_rate'];
				$diff = date_diff(date_create($value['date_start']),date_create($value['date_end']));
				$date_count = $diff->format("%a");
				$date_count = $date_count+1;
				$interest = ((($value['transaction_balance']*@$interest_rate)/100)*$date_count)/365;
				$transaction[$key]['interest'] = $interest;
				$account_interest += $interest;
			}
			//echo $row_member['member_id']." : ".$row_member['account_id']." : ".$account_interest."<br>";
			//echo"<pre>";print_r($transaction);echo"</pre>";exit;
			//echo $interest_sum;
			$this->db->select(array(
				'transaction_balance'
			));
			$this->db->from('coop_account_transaction as t1');
			$this->db->where("account_id = '".$row_member['account_id']."'");
			$this->db->order_by('transaction_time DESC');
			$this->db->limit(1);
			$row_balance = $this->db->get()->result_array();
			$row_balance = @$row_balance[0];
			$balance     = $row_balance["transaction_balance"];

			$sum = $balance + $account_interest;
			
			$data_insert = array();
			$data_insert['transaction_time'] = date('Y-m-d H:i:s');
			$data_insert['transaction_list'] = 'IN';
			$data_insert['transaction_withdrawal'] = '';
			$data_insert['transaction_deposit'] = $account_interest;
			$data_insert['transaction_balance'] = $sum;
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['account_id'] = $row_member['account_id'];
			$this->db->insert('coop_account_transaction', $data_insert);
			$sum_account_interest += $account_interest;
		}
		$data['coop_account']['account_description'] = "ดอกเบี้ยเงินฝาก";
		$data['coop_account']['account_datetime'] = date('Y-m-d H:i:s');
		
		$i=0;
		$data['coop_account_detail'][$i]['account_type'] = 'debit';
		$data['coop_account_detail'][$i]['account_amount'] = $sum_account_interest;
		$data['coop_account_detail'][$i]['account_chart_id'] = '50100';
		$i++;
		$data['coop_account_detail'][$i]['account_type'] = 'credit';
		$data['coop_account_detail'][$i]['account_amount'] = $sum_account_interest;
		$data['coop_account_detail'][$i]['account_chart_id'] = '10100';
		$this->account_transaction->account_process($data);
	//echo "<script> window.location.href = \"/?section=deposit\"</script>"; 
	exit;
	}
	
	public function receipt_form_pdf_backup_05_11_2018($receipt_id,$receipt_id2=null){

			$receipt_id2 =!empty($receipt_id2)? '/'.$receipt_id2:'';
		
		$data_arr = array();
		$this->db->select('*');
		$this->db->from("coop_receipt");
		$this->db->where("receipt_id ='".$receipt_id.$receipt_id2."'");
		$row = $this->db->get()->result_array();

		$data_arr['row_receipt'] = $row[0];

		$this->db->select(array('t1.*','t2.mem_group_name','t3.prename_full'));
		$this->db->from("coop_mem_apply as t1");
		$this->db->join("coop_mem_group as t2",'t1.level = t2.id','left');
		$this->db->join("coop_prename as t3",'t1.prename_id = t3.prename_id','left');
		$this->db->where("member_id ='".$data_arr['row_receipt']['member_id']."'");
		$row = $this->db->get()->result_array();
		//echo $this->db->last_query(); exit;

		$data_arr['prename_full'] = @$row[0]['prename_full'];
		$data_arr['name'] = @$row[0]['firstname_th'].' '.@$row[0]['lastname_th'];
		$data_arr['member_data'] = @$row[0];
		$data_arr['member_id'] = @$row[0]['member_id'];
//		echo"<pre>";print_r($row[0]);exit;

		$this->db->select(array(
			't1.*',
		));
		$this->db->from("coop_finance_transaction as t1");
		$this->db->where("t1.receipt_id = '".$receipt_id.$receipt_id2."'");
		$row = $this->db->get()->result_array();
//		echo"<pre>";print_r($row[0]);exit;
		$data_arr['transaction_data'] = $row;

		//ลายเซ็นต์
		$date_signature = date('Y-m-d');
		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("start_date <= '{$date_signature}'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
//		echo"<pre>";print_r($row);exit;
		$data_arr['signature'] = @$row[0];
		
		$this->db->select('*');
		$this->db->from("coop_loan");
		$this->db->where("deduct_receipt_id = '".$receipt_id.$receipt_id2."'");
		$row = $this->db->get()->result_array();
		$data_arr['pay_for_loan']['contract_number'] = @$row[0]['contract_number'];
		
		$this->load->view('admin/receipt_form_pdf',$data_arr);
	}
	
	public function receipt_form_pdf($receipt_id,$receipt_id2=null){
        if(@$_SESSION['USER_ID']=='') {
            header( "location: ".PROJECTPATH."/auth?return_url=%2Fadmin%2Freceipt_form_pdf/".$receipt_id );
        }
        $receipt_id = $this->center_function->decrypt_text(jwt::urlsafeB64Decode($receipt_id));

		$receipt_id2 =!empty($receipt_id2)? '/'.$receipt_id2:'';
		$receipt_id  = urldecode($receipt_id.$receipt_id2);

		$data_arr = array();

        $code_name = 'receipt_form';
        $this->db->select('switch_code');
        $this->db->from('format_setting_report');
        $this->db->where("code_name = '".$code_name."'");
        $setting_report = $this->db->get()->row_array();
        $data_arr['switch_code'] = $setting_report['switch_code'];

		$this->db->select('*');
		$this->db->from("coop_receipt");
		$this->db->join("coop_user", "coop_receipt.admin_id = coop_user.user_id", "left");
		$this->db->where("receipt_id ='".$receipt_id."'");
		$row = $this->db->get()->result_array();

        $arr_pay_type = array('0'=>'เงินสด','1'=>'โอนเงิน');
        $data_arr['pay_type'] =  $arr_pay_type[$row[0]['pay_type']];

		$data_arr['row_receipt'] = $row[0];
		//วันที่ใบเสร็จ
		$receipt_datetime = date("Y-m-d", strtotime($data_arr['row_receipt']['receipt_datetime']));

		$this->db->select(array('t1.*','t2.mem_group_id','t2.mem_group_name','t2.mem_group_full_name','t3.prename_short as prename_full'));
		$this->db->from("(SELECT IF (
										(
											SELECT
												level_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$receipt_datetime."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										(
											SELECT
												level_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$receipt_datetime."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										coop_mem_apply. level
									) AS level, member_id, firstname_th, lastname_th, mem_type_id, prename_id, employee_id FROM coop_mem_apply) as t1");
		$this->db->join("coop_mem_group as t2",'t1.level = t2.id','left');
		$this->db->join("coop_prename as t3",'t1.prename_id = t3.prename_id','left');
		$this->db->where("member_id ='".$data_arr['row_receipt']['member_id']."'");
		$row = $this->db->get()->result_array();

		if(@$_GET['dev']=='dev'){
			echo $this->db->last_query(); exit;
		}

		$data_arr['prename_full'] = @$row[0]['prename_full'];
		$data_arr['name'] = @$row[0]['firstname_th'].' '.@$row[0]['lastname_th'];
		$data_arr['member_data'] = @$row[0];
		$data_arr['member_id'] = @$row[0]['member_id'];
		$data_arr['employee_id'] = @$row[0]['employee_id'];

		$this->db->select(array(
			't1.*',
            't2.contract_number'
		));
		$this->db->from("coop_finance_transaction as t1");
        $this->db->join("coop_loan as t2", "t2.id = t1.loan_id", "left");
		$this->db->where("t1.receipt_id = '".$receipt_id.$receipt_id2."'");
		$row = $this->db->get()->result_array();
        $sum_interest_year = 0;
        if(!empty($row[0]['payment_date'])){
            $payment_date_arr = explode('-', $row[0]['payment_date']);
            $return_year = $payment_date_arr[0];
            $mount_receipt = $payment_date_arr[1];
            $return_yearks = $return_year;

            $this->db->select('accm_month_ini');
            $this->db->from("coop_account_period_setting");
            $row_period_setting = $this->db->get()->result_array();
            $data_arr['row_period_setting'] = $row_period_setting;
            $years = $return_yearks;
            $mount_end = sprintf("%02d",$data_arr['row_period_setting'][0]['accm_month_ini']);
            if ($mount_receipt < $mount_end){
                $years_old = $years-1;
            }else{
                $years_old = $years;
                $years++;
            }
            $mount_old = $mount_end;
            $mount_old = sprintf("%02d",$mount_old);
            $mount_end = $mount_end-1;
            if($mount_end < 1){
                $mount_end = 12;
            }
            $mount_end = sprintf("%02d",$mount_end);
            $date_end  = cal_days_in_month(CAL_GREGORIAN, $mount_end, $years);

            $between_date_start = "".$years_old.'-'.$mount_old."-01";
            $between_date_end = "".$years.'-'.$mount_end."-".$date_end;

            $this->db->select(array(
                'sum(interest) as sum_interest_year'
            ));
            $this->db->from("coop_finance_transaction");
            $this->db->where("receipt_id = '".$receipt_id.$receipt_id2."' AND payment_date BETWEEN '".$between_date_start."' AND '".$between_date_end."'");
            $interest_year = $this->db->get()->row_array();
            $sum_interest_year = $interest_year['sum_interest_year'];
        }
        $data_arr['sum_interest_year'] = $sum_interest_year;

		$transactions = array();
		$account_list = null;
		foreach($row as $transaction) {
			$account_list = $transaction["account_list_id"];
			if (!empty($transaction['loan_id'])) {
				if(!empty($transaction['principal_payment'])) $transactions[$transaction['loan_id']]['transaction_text_main'] = $transaction['transaction_text'];
				$transactions[$transaction['loan_id']]['transaction_text'] = $transaction['contract_number'];
				if(!empty($transaction['period_count'])) $transactions[$transaction['loan_id']]['period_count'] = $transaction['period_count'];
				$transactions[$transaction['loan_id']]['principal_payment'] += $transaction['principal_payment'];
				$transactions[$transaction['loan_id']]['interest'] += $transaction['interest'];
				$transactions[$transaction['loan_id']]['loan_interest_remain'] += $transaction['loan_interest_remain'];
				if(!empty($transaction['loan_amount_balance'])) $transactions[$transaction['loan_id']]['loan_amount_balance'] = $transaction['loan_amount_balance'];
			} else if (!empty($transaction['loan_atm_id'])) {
				if(!empty($transaction['principal_payment'])) $transactions["atm_".$transaction['loan_atm_id']]['transaction_text_main'] = $transaction['transaction_text'];
				$transactions["atm_".$transaction['loan_atm_id']]['transaction_text'] = $transaction['contract_number'];
				if(!empty($transaction['period_count'])) $transactions["atm_".$transaction['loan_atm_id']]['period_count'] = $transaction['period_count'];
				$transactions["atm_".$transaction['loan_atm_id']]['principal_payment'] += $transaction['principal_payment'];
				$transactions["atm_".$transaction['loan_atm_id']]['interest'] += $transaction['interest'];
				$transactions["atm_".$transaction['loan_atm_id']]['loan_interest_remain'] += $transaction['loan_interest_remain'];
				if(!empty($transaction['loan_amount_balance'])) $transactions["atm_".$transaction['loan_atm_id']]['loan_amount_balance'] = $transaction['loan_amount_balance'];
			} else {
				$transactions[] = $transaction;
			}
		}

		$data_arr['transaction_data'] = $transactions;
		$data_arr['account_list'] = $account_list;

		$sql = "SELECT share_collect_value FROM coop_mem_share WHERE member_id = '".$data_arr['row_receipt']['member_id']."' AND share_date <= '".$receipt_datetime." 23:59:59' AND share_status <> 3 ORDER BY share_date DESC LIMIT 1";
		$data_arr['share_collect_value'] = $this->db->query($sql)->row_array()['share_collect_value'];

		//ลายเซ็นต์
		$date_signature = date('Y-m-d');
		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("start_date <= '{$date_signature}'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$data_arr['signature'] = @$row[0];

		$this->db->select('*');
		$this->db->from("coop_loan");
		$this->db->where("deduct_receipt_id = '".$receipt_id.$receipt_id2."'");
		$row = $this->db->get()->result_array();
        $data_arr['pay_for_loan']['contract_number'] = @$row[0]['contract_number'];

        $data_arr['profile'] = $this->db->select('*')->from('coop_profile')->limit(1)->get()->row_array();
        $data_arr['setting_receipt'] = $this->db->select('*')->from('coop_setting_receipt')->where("type = 1 AND status = 1")->order_by("id DESC")->get()->row_array();

		if($account_list == "12") {
			$this->load->view('admin/payment_slip_pdf',$data_arr);
		} else {
			$this->load->view('admin/receipt_form_pdf',$data_arr);
		}
    }
    
    public function receipt_form_pdf_online($receipt_id,$receipt_id2=null){
        // if(@$_SESSION['USER_ID']=='') {
        //     header( "location: ".PROJECTPATH."/auth?return_url=%2Fadmin%2Freceipt_form_pdf/".$receipt_id );
        // }
        $receipt_id = $this->center_function->decrypt_text(jwt::urlsafeB64Decode($receipt_id));

		$receipt_id2 =!empty($receipt_id2)? '/'.$receipt_id2:'';
		$receipt_id  = urldecode($receipt_id.$receipt_id2);

		$data_arr = array();

        $code_name = 'receipt_form';
        $this->db->select('switch_code');
        $this->db->from('format_setting_report');
        $this->db->where("code_name = '".$code_name."'");
        $setting_report = $this->db->get()->row_array();
        $data_arr['switch_code'] = $setting_report['switch_code'];

		$this->db->select('*');
		$this->db->from("coop_receipt");
		$this->db->join("coop_user", "coop_receipt.admin_id = coop_user.user_id", "left");
		$this->db->where("receipt_id ='".$receipt_id."'");
		$row = $this->db->get()->result_array();

        $arr_pay_type = array('0'=>'เงินสด','1'=>'โอนเงิน');
        $data_arr['pay_type'] =  $arr_pay_type[$row[0]['pay_type']];

		$data_arr['row_receipt'] = $row[0];
		//วันที่ใบเสร็จ
		$receipt_datetime = date("Y-m-d", strtotime($data_arr['row_receipt']['receipt_datetime']));

		$this->db->select(array('t1.*','t2.mem_group_id','t2.mem_group_name','t2.mem_group_full_name','t3.prename_short as prename_full'));
		$this->db->from("(SELECT IF (
										(
											SELECT
												level_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$receipt_datetime."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										(
											SELECT
												level_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$receipt_datetime."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										coop_mem_apply. level
									) AS level, member_id, firstname_th, lastname_th, mem_type_id, prename_id, employee_id FROM coop_mem_apply) as t1");
		$this->db->join("coop_mem_group as t2",'t1.level = t2.id','left');
		$this->db->join("coop_prename as t3",'t1.prename_id = t3.prename_id','left');
		$this->db->where("member_id ='".$data_arr['row_receipt']['member_id']."'");
		$row = $this->db->get()->result_array();

		if(@$_GET['dev']=='dev'){
			echo $this->db->last_query(); exit;
		}

		$data_arr['prename_full'] = @$row[0]['prename_full'];
		$data_arr['name'] = @$row[0]['firstname_th'].' '.@$row[0]['lastname_th'];
		$data_arr['member_data'] = @$row[0];
		$data_arr['member_id'] = @$row[0]['member_id'];
		$data_arr['employee_id'] = @$row[0]['employee_id'];

		$this->db->select(array(
			't1.*',
            't2.contract_number'
		));
		$this->db->from("coop_finance_transaction as t1");
        $this->db->join("coop_loan as t2", "t2.id = t1.loan_id", "left");
		$this->db->where("t1.receipt_id = '".$receipt_id.$receipt_id2."'");
		$row = $this->db->get()->result_array();
        $sum_interest_year = 0;
        if(!empty($row[0]['payment_date'])){
            $payment_date_arr = explode('-', $row[0]['payment_date']);
            $return_year = $payment_date_arr[0];
            $mount_receipt = $payment_date_arr[1];
            $return_yearks = $return_year;

            $this->db->select('accm_month_ini');
            $this->db->from("coop_account_period_setting");
            $row_period_setting = $this->db->get()->result_array();
            $data_arr['row_period_setting'] = $row_period_setting;
            $years = $return_yearks;
            $mount_end = sprintf("%02d",$data_arr['row_period_setting'][0]['accm_month_ini']);
            if ($mount_receipt < $mount_end){
                $years_old = $years-1;
            }else{
                $years_old = $years;
                $years++;
            }
            $mount_old = $mount_end;
            $mount_old = sprintf("%02d",$mount_old);
            $mount_end = $mount_end-1;
            if($mount_end < 1){
                $mount_end = 12;
            }
            $mount_end = sprintf("%02d",$mount_end);
            $date_end  = cal_days_in_month(CAL_GREGORIAN, $mount_end, $years);

            $between_date_start = "".$years_old.'-'.$mount_old."-01";
            $between_date_end = "".$years.'-'.$mount_end."-".$date_end;

            $this->db->select(array(
                'sum(interest) as sum_interest_year'
            ));
            $this->db->from("coop_finance_transaction");
            $this->db->where("receipt_id = '".$receipt_id.$receipt_id2."' AND payment_date BETWEEN '".$between_date_start."' AND '".$between_date_end."'");
            $interest_year = $this->db->get()->row_array();
            $sum_interest_year = $interest_year['sum_interest_year'];
        }
        $data_arr['sum_interest_year'] = $sum_interest_year;

		$transactions = array();
		$account_list = null;
		foreach($row as $transaction) {
			$account_list = $transaction["account_list_id"];
			if (!empty($transaction['loan_id'])) {
				if(!empty($transaction['principal_payment'])) $transactions[$transaction['loan_id']]['transaction_text_main'] = $transaction['transaction_text'];
				$transactions[$transaction['loan_id']]['transaction_text'] = $transaction['contract_number'];
				if(!empty($transaction['period_count'])) $transactions[$transaction['loan_id']]['period_count'] = $transaction['period_count'];
				$transactions[$transaction['loan_id']]['principal_payment'] += $transaction['principal_payment'];
				$transactions[$transaction['loan_id']]['interest'] += $transaction['interest'];
				$transactions[$transaction['loan_id']]['loan_interest_remain'] += $transaction['loan_interest_remain'];
				if(!empty($transaction['loan_amount_balance'])) $transactions[$transaction['loan_id']]['loan_amount_balance'] = $transaction['loan_amount_balance'];
			} else if (!empty($transaction['loan_atm_id'])) {
				if(!empty($transaction['principal_payment'])) $transactions["atm_".$transaction['loan_atm_id']]['transaction_text_main'] = $transaction['transaction_text'];
				$transactions["atm_".$transaction['loan_atm_id']]['transaction_text'] = $transaction['contract_number'];
				if(!empty($transaction['period_count'])) $transactions["atm_".$transaction['loan_atm_id']]['period_count'] = $transaction['period_count'];
				$transactions["atm_".$transaction['loan_atm_id']]['principal_payment'] += $transaction['principal_payment'];
				$transactions["atm_".$transaction['loan_atm_id']]['interest'] += $transaction['interest'];
				$transactions["atm_".$transaction['loan_atm_id']]['loan_interest_remain'] += $transaction['loan_interest_remain'];
				if(!empty($transaction['loan_amount_balance'])) $transactions["atm_".$transaction['loan_atm_id']]['loan_amount_balance'] = $transaction['loan_amount_balance'];
			} else {
				$transactions[] = $transaction;
			}
		}

		$data_arr['transaction_data'] = $transactions;
		$data_arr['account_list'] = $account_list;

		$sql = "SELECT share_collect_value FROM coop_mem_share WHERE member_id = '".$data_arr['row_receipt']['member_id']."' AND share_date <= '".$receipt_datetime." 23:59:59' AND share_status <> 3 ORDER BY share_date DESC LIMIT 1";
		$data_arr['share_collect_value'] = $this->db->query($sql)->row_array()['share_collect_value'];

		//ลายเซ็นต์
		$date_signature = date('Y-m-d');
		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("start_date <= '{$date_signature}'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$data_arr['signature'] = @$row[0];

		$this->db->select('*');
		$this->db->from("coop_loan");
		$this->db->where("deduct_receipt_id = '".$receipt_id.$receipt_id2."'");
		$row = $this->db->get()->result_array();
        $data_arr['pay_for_loan']['contract_number'] = @$row[0]['contract_number'];

        $data_arr['profile'] = $this->db->select('*')->from('coop_profile')->limit(1)->get()->row_array();
        $data_arr['setting_receipt'] = $this->db->select('*')->from('coop_setting_receipt')->where("type = 1 AND status = 1")->order_by("id DESC")->get()->row_array();

		if($account_list == "12") {
			$this->load->view('admin/payment_slip_pdf',$data_arr);
		} else {
			$this->load->view('admin/receipt_form_pdf_online',$data_arr);
		}
	}

	public function receipt_account_month_spkt_pdf(){

		// ini_set('max_execution_time', 1800);
		 function U2T($text) { 
			return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); 
		  }
		  function num_format($text) {
			if($text!=''){
				return number_format($text,2);
			}else{
				return '0.00';
			}
		}
		
		 function convert($number) {
			$txtnum1 = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ');
			$txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน');
			$number = str_replace(",","",$number);
			$number = str_replace(" ","",$number);
			$number = str_replace("บาท","",$number);
			$number = explode(".",$number);
			if(sizeof($number) > 2) {
				  return 'ทศนิยมหลายตัวนะจ๊ะ';
				  exit;
			}
			$strlen = strlen($number[0]);
			$convert = '';
			for($i=0;$i<$strlen;$i++){
				  $n = substr($number[0], $i,1);
				  if($n!=0){
					  if($i==($strlen-1) AND $n==1){ $convert .= 'เอ็ด'; }
					  elseif($i==($strlen-2) AND $n==2){ $convert .= 'ยี่'; }
					  elseif($i==($strlen-2) AND $n==1){ $convert .= ''; }
					  else{ $convert .= $txtnum1[$n]; }
					  $convert .= $txtnum2[$strlen-$i-1];
				  }
			}
			  if(!isset($number[1])) $number[1] = 0;
			$convert .= 'บาท';
			if($number[1]=='0' || $number[1]=='00' || $number[1]==''){
			  $convert .= 'ถ้วน';
			}else{
			  $strlen = strlen($number[1]);
			  for($i=0;$i<$strlen;$i++){
				$n = substr($number[1], $i,1);
				if($n!=0){
				  if($number[1] == 01){$convert .= 'หนึ่ง';}
				  elseif($i==($strlen-1) AND $n==1 ){$convert .= 'เอ็ด';}
				  elseif($i==($strlen-2) AND $n==2){$convert .= 'ยี่';}
				  elseif($i==($strlen-2) AND $n==1){$convert .= '';}
				  else{ $convert .= $txtnum1[$n];}
				  $convert .= $txtnum2[$strlen-$i-1];
				}
			  }
			  $convert .= 'สตางค์';
			}
			return $convert;
		  }

		
		$month_arr = array('มกราคม'=>'1','กุมภาพันธ์'=>'2','มีนาคม'=>'3','เมษายน'=>'4','พฤษภาคม'=>'5','มิถุนายน'=>'6','กรกฎาคม'=>'7','สิงหาคม'=>'8','กันยายน'=>'9','ตุลาคม'=>'10','พฤศจิกายน'=>'11','ธันวาคม'=>'12');
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$data_arr = array();
		$this->db->select('*');
		$this->db->from("coop_receipt");
		$this->db->where("receipt_id = '".$receipt_id."'");
		$row = $this->db->get()->result_array();

			$return_year = @$_GET['year'];
			$mount_receipt = $_GET['month'];
			if($mount_receipt == 12){
				$mount_receipt_next = 1 ;
				$year_receipt_next = $_GET['year']+1 ;

			}else{

				$mount_receipt_next = $mount_receipt +1;
				$year_receipt_next = $_GET['year'] ;

			}
			$return_yearks = $return_year-543;

			$where = "1=1  ";

			$pdf = new FPDI('L','mm', array(139.6,202));

			$data_get = $this->input->get();
			// echo"<pre>";print_r($data_get);exit;


			$this->db->select('accm_month_ini');
			$this->db->from("coop_account_period_setting");
			$row_period_setting = $this->db->get()->result_array();
			$data_arr['row_period_setting'] = $row_period_setting;
			  // echo"<pre>";print_r( $data_arr['row_period_setting']);exit;		
			$years = $return_yearks;
			$years_old = $years-1;
			$mount_start = sprintf("%02d",$data_arr['row_period_setting'][0]['accm_month_ini']);
			$mount_old = $mount_start - 1;
			$mount_old = sprintf("%02d",$mount_old);
			if($mount_start == '01' || $mount_start == '1'){
				$mount_old = '12';
			}
			$date_start  = cal_days_in_month(CAL_GREGORIAN, $mount_start , $years);
			
			$between_date_old = "".$years_old.'-'.$mount_old."-01";
			$between_date_start = "".$years.'-'.$mount_start."-".$date_start;

			if($data_get['choose_receipt'] == '2'){
				$where .= !empty($data_get['member_id']) ? "AND t2.member_id = '{$data_get['member_id']}' " : "";
			}else if($data_get['choose_receipt'] == '3'){
				$where .= !empty($data_get['member_id_begin']) ? "AND t2.member_id BETWEEN '{$data_get['member_id_begin']}' AND '{$data_get['member_id_end']}' " : "";
			}else{
				// condition filter on choose receipt 1 By t.tawatsak	
				if(!empty($data_get['department'])){
				$where .="AND (";
				$where .= !empty($data_get['department']) ? "t4.department='{$data_get['department']}' AND t5.department_old is null " : "";
				$where .= !empty($data_get['faction']) ? "AND t4.faction='{$data_get['faction']}' AND t5.faction_old is null " : "";
				$where .= !empty($data_get['level']) ? "AND t4.level='{$data_get['level']}' AND t5.level_old is null " : "";
				$where .=") OR ";
				$where .="(";
				$where .= !empty($data_get['department']) ? " t5.department_old ='{$data_get['department']}' AND t5.department <> '{$data_get['department']}' " : "";
				$where .= !empty($data_get['faction']) ? "AND t5.faction_old = '{$data_get['faction']}' AND t5.faction <> '{$data_get['faction']}' " : "";
				$where .= !empty($data_get['level']) ? "AND t5.level_old ='{$data_get['level']}' AND t5.level <> '{$data_get['level']}' " : "";
				$where .=")  ";
				}
				// selected page number
				// $end_limit = $data_get['page_number'] * 100;
				$end_limit = $data_get['page_number'] * 100;
				$start_limit = $end_limit - 100;

				!empty($start_limit)?$start_limit = $start_limit : $start_limit = 0;
				!empty($end_limit)?$end_limit = $end_limit : $end_limit = 100;

			}
					
//รายการใบเสร็๗ทั้งหมดtop
		$this->db->select(array(
			't1.receipt_id','t3.id','t2.member_id','t1.receipt_datetime','t5.date_move'
		));
		$this->db->from("coop_finance_month_detail as t0 ");
		$this->db->join("coop_receipt as t1", "t1.finance_month_profile_id = t0.profile_id AND t1.year_receipt = {$return_year}  AND t1.month_receipt = {$mount_receipt} ",'inner' );
		$this->db->join("coop_finance_transaction as t2", 't1.receipt_id = t2.receipt_id ','inner' );
		$this->db->join("coop_loan as t3", 't2.loan_id = t3.id', 'left');
		$this->db->join('coop_mem_apply as t4', 't1.member_id = t4.member_id', 'left');
		$this->db->join('coop_mem_group_move as t5',' t5.member_id = t4.member_id
		AND t5.date_move >= t1.receipt_datetime' , 'left');
		// $this->db->where("t1.year_receipt = '{$return_year}' ");
		// $this->db->where("t1.month_receipt = '{$mount_receipt}' ");		
		$this->db->where("t1.receipt_id  NOT LIKE '%C%' ");		
		$this->db->where("(t1.receipt_status  <>  2 OR t1.receipt_status is NULL)");		

		$this->db->where($where);
		//echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
		$this->db->group_by('t1.receipt_id');
		// $this->db->order_by("t4.department,t4.faction,t4.level,t1.member_id  ASC");
		$this->db->order_by("t1.member_id  ASC");
		$this->db->limit($end_limit,$start_limit);
		//echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;

		$row_all = $this->db->get()->result_array();
		$data_arr['transaction_data_all'] = $row_all;
		// echo"<pre>";print_r($data_arr['transaction_data_all']);exit;
		$nn = 1;
		foreach($data_arr['transaction_data_all'] as $key => $transaction_data_all) {


            $receipt_id_run = $transaction_data_all['receipt_id'];
            $member_id_run = $transaction_data_all['member_id'];
            $loan_id_run = $transaction_data_all['id'];
            $date_receipt_run = $transaction_data_all['receipt_datetime'];


            // 		$this->db->select(array('interest_sum'));
            // 		$this->db->from("interest_sum");
            // 		$this->db->where("member_id = {$member_id_run} ");
            //      $this->db->where("member_id = '".$data_arr['receipt_id']['member_id']."'");
            // 		$row_interes = $this->db->get()->result_array();

            //เงินคืน
            $this->db->select(array(
                'SUM(return_amount) as sum_interest_amount'
            ));
            $this->db->from("coop_process_return");
            $this->db->where("member_id = {$member_id_run} ");
            $this->db->where("return_month = '{$mount_receipt}' ");
            $this->db->where("return_year =  '{$return_yearks}' ");

            $row_return_interest = $this->db->get()->result_array();
            $data_arr['return_interest'] = $row_return_interest[0];
            // echo"<pre>";print_r($data_arr['return_interest']);exit;

            //เงินคืน

            //echo $return_interest.'<br>';
            //interest_sum_detail

            //ดอกเบี้ยสะสม (คำนวนดอกเบี้ยจากเงินที่จ่ายทั้งหมด)
            if ($return_year == '2561' && $mount_receipt == '10') {
                $this->db->select(array('interest_sum'));
                $this->db->from("interest_sum_detail");
                $this->db->where("member_id = {$member_id_run} ");
                $this->db->where("month = {$mount_receipt} ");
                $this->db->where("year = {$return_year} ");
//		$this->db->where("member_id = '".$data_arr['receipt_id']['member_id']."'");
                $row_interes = $this->db->get()->result_array();
                //interest_sum_detail
                $row_interes_interest_sum = str_replace(",", "", $row_interes[0]['interest_sum']);
            } else {

                $row_period_setting;

                $Years = $return_year + 543 > 3000 ? $return_year - 543 : $return_year;
                $months = intval($mount_receipt) > 12 ? $mount_receipt - 12 : $mount_receipt;
                $months = str_pad($months, 2, '0', STR_PAD_LEFT);

                $this->db->select('accm_month_ini');
                $this->db->from("coop_account_period_setting");

                $row_period_setting = $this->db->get()->result_array();

                $period_setting = $row_period_setting[0]['accm_month_ini'];
                if ($months == $period_setting) {
                    $Years_start = $return_yearks;
                } else {
                    $Years_start = $return_yearks - 1;
                }
                $init = $Years_start . '-' . $period_setting . '-01';
                // $init = '2018-12-01';

                if ($months == $period_setting) {
                    $init = date('Y-m-d', strtotime($Years . '-' . $months . '-01'));
                    $date_in_m = cal_days_in_month(CAL_GREGORIAN, $months, $Years);

                    if ($months == 12) {
                        $stop = date('Y-m-d', strtotime((intval($Years)) . '-' . ($period_setting) . '-31'));
                    } else {
                        $stop = date('Y-m-d', strtotime((intval($Years) + 1) . '-' . ($period_setting) . '-' . $date_in_m));
                    }

                } else {
                    $date_in_m = cal_days_in_month(CAL_GREGORIAN, $months, $Years);
                    $init = date('Y-m-d', strtotime((intval($Years) - 1) . '-' . ($period_setting) . '-01'));
                    $stop = date('Y-m-d', strtotime($Years . '-' . $months . '-' . $date_in_m));
                }

                $this->load->model('interests');
                $interest = $this->interests->averageReturn($init, $stop, 100, $member_id_run)->interest;

                if ($return_year == '2561' && $mount_receipt == '11') {

                    $Years_t = $return_year + 543 > 3000 ? $return_year - 543 : $return_year;
                    $months_t = intval(10) > 12 ? 10 - 12 : 10;
                    $months_t = str_pad($months_t, 2, '0', STR_PAD_LEFT);

                    $init_t = date('Y-m-t', strtotime((intval($Years_t) - 1) . '-' . ($row_period_setting[0]['accm_month_ini'] - 1)));
                    $stop_t = date('Y-m-t', strtotime($Years_t . '-' . $months_t));

                    $this->load->model('interests');
                    $interest_t = $this->interests->averageReturn($init_t, $stop_t, 100, $member_id_run)->interest;


                    $this->db->select(array('interest_sum'));
                    $this->db->from("interest_sum_detail");
                    $this->db->where("member_id = {$member_id_run} ");
                    $this->db->where("month = '10' ");
                    $this->db->where("year = '2561' ");
                    //$this->db->where("member_id = '".$data_arr['receipt_id']['member_id']."'");
                    $row_interes = $this->db->get()->result_array();
                    //interest_sum_detail
                    $row_interes_interest_sum_diff = str_replace(",", "", $row_interes[0]['interest_sum']);

                    $interest = $interest + ($interest_t - $row_interes_interest_sum_diff);
                }


                $row_interes_interest_sum = str_replace(",", "", $interest);
                // echo"<pre>";echo($interest_t);echo"</pre>";
                // echo"<pre>";echo($row_interes_interest_sum_diff);echo"</pre>";
                // // echo"<pre>";echo($row_interes_interest_sum);echo"</pre>";
                // // echo"<pre>";echo($interest);echo"</pre>";

            }

            $data_arr['interest_sum'] = $row_interes_interest_sum;
            if (empty($row_interes_interest_sum)) {
                $row_interes_interest_sum = 0;
            }
            //ดอกเบี้ยสะสม (คำนวนดอกเบี้ยจากเงินที่จ่ายทั้งหมด)

            if (empty($transaction_data_all['date_move'])) {

                $this->db->select(array('t1.*', 't2.prename_full',
                    "IF(t1.level IS NOT NULL ,t5.mem_group_full_name,IF(t1.faction IS NOT NULL,t4.mem_group_full_name,t3.mem_group_id)) AS mem_group_full_name",
                    "IF(t1.level IS NOT NULL ,t5.mem_group_name,IF(t1.faction IS NOT NULL,t4.mem_group_name,t3.mem_group_id)) AS mem_group_name",
                    "IF(t1.level IS NOT NULL ,t5.mem_group_id,IF(t1.faction IS NOT NULL,t4.mem_group_id,t3.mem_group_id)) AS mem_group_id"));
                $this->db->from("coop_mem_apply as t1");
                $this->db->join("coop_prename as t2", 't1.prename_id = t2.prename_id', 'left');
                $this->db->join("coop_mem_group AS t3", "t1.department = t3.id", "left");
                $this->db->join("coop_mem_group AS t4", "t1.faction = t4.id", "left");
                $this->db->join("coop_mem_group AS t5", "t1.level = t5.id", "left");
                //$this->db->join("coop_mem_group as t3",'t1.level = t3.id','left');
                $this->db->where("t1.member_id = {$member_id_run} ");
                //		$this->db->where("member_id = '".$data_arr['receipt_id']['member_id']."'");
                $row = $this->db->get()->result_array();

            } else {
                $this->db->select(array('t1.*', 't2.prename_full', 't3.mem_group_name', 't3.mem_group_id','t3.mem_group_full_name'));
                $this->db->from("coop_mem_apply as t1");
                $this->db->join("coop_prename as t2", 't1.prename_id = t2.prename_id', 'left');
                $this->db->join('coop_mem_group_move as t5', 't5.member_id = t1.member_id', 'left');
                $this->db->join("coop_mem_group as t3", 't5.level_old = t3.id', 'left');
                $this->db->where("t1.member_id = {$member_id_run} ");
                //		$this->db->where("member_id = '".$data_arr['receipt_id']['member_id']."'");
                $row = $this->db->get()->result_array();

            }


            $share_collect_val = $this->db->select('share_collect_value')->from('coop_mem_share')->where(array('member_id' => $member_id_run, 'share_date <= ' => $date_receipt_run))->order_by('share_date', 'desc')->limit(1)->get()->row_array()['share_collect_value'];

            $data_arr['prename_full'] = @$row[0]['prename_full'];
            $data_arr['name'] = @$row[0]['firstname_th'] . ' ' . @$row[0]['lastname_th'];
            $data_arr['member_data'] = @$row[0];


            //หัว pdf
            $pdf->AddPage();
            $pdf->AddFont('THSarabunNew', '', 'saikrok.php');
            $pdf->SetFont('THSarabunNew', '', 14);
            $pdf->SetMargins(0, 0, 0);
            if(isset($_GET['dev']) && $_GET['dev'] == "2"){
                $border = 1;
            }else {
                $border = 0;
            }
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetAutoPageBreak(false, 0);


            $pdf->SetXY(172, 21);
            $pdf->MultiCell(25, 5, U2T(@$receipt_id_run), $border, "L"); // เลขที่

            $y_point = 30;
            $pdf->SetXY(30, $y_point);
            $pdf->MultiCell(30, 5, U2T($this->center_function->mydate2date($date_receipt_run)), $border, 'L');
            $pdf->SetXY(150, $y_point);
            $pdf->MultiCell(25, 5, number_format(@$share_collect_val, 2), $border, 1);//ดอกเบี้ยสะสม


            $y_point = 36;
            $pdf->SetXY(30, $y_point);
            $pdf->MultiCell(80, 5, U2T(@$row[0]['prename_full'] . @$row[0]['firstname_th'] . ' ' . @$row[0]['lastname_th']), $border, 1); // ชื่อ-นามสกุล
            $pdf->SetXY(120, $y_point);
            $pdf->MultiCell(25, 5, @$row[0]['member_id'], $border, 'L');//รหัสสมาชิก
            $pdf->SetFont('THSarabunNew', '', 13);
            $pdf->SetXY(152, $y_point);
            $pdf->MultiCell(40, 5, U2T('[ '.@$row[0]['mem_group_id'] . ' - ' . @$row[0]['mem_group_full_name'] . ' ]'), $border, 'L');// หน่วย

            $y_point = 42;
            $pdf->SetXY(120, $y_point);
            $pdf->MultiCell(20, 5, U2T(@$row[0]['employee_id']), $border, 'L');// หน่วย

            $data_arr['mount_receipt'] = $month_arr[$mount_receipt_next];
            $data_arr['row_receipt'] = $row[0];

            $mShort = array(1 => "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
            $data_arr['str'] = "";
            $datetime = date("Y-m-d H:i:s");

            $tmp = explode(" ", $datetime);
            if ($tmp[0] != "0000-00-00") {
                $d = explode("-", $tmp[0]);
                $month = $mShort;
                $str = $d[2] . " " . $month[(int)$d[1]] . " " . ($d[0] > 2500 ? $d[0] : $d[0] + 543);
                $t = strtotime($datetime);
                $data_arr['str'] = $data_arr['str'] . " " . date("H:i", $t) . " น.";
            }

            $this->db->select('setting_value');
            $this->db->from("coop_share_setting");
            $this->db->where("setting_id = '1'");
            $row = $this->db->get()->result_array();

            $data_arr['share_value'] = $row[0]['setting_value'];

            $this->db->select(array('id', 'mem_group_name'));
            $this->db->from("coop_mem_group");
            $row = $this->db->get()->result_array();

            $mem_group_arr = array();
            foreach ($row as $key => $value) {
                $mem_group_arr[$value['id']] = $value['mem_group_name'];
            }

            $data_arr['mem_group_arr'] = $mem_group_arr;

            //รายการแต่ละใบเสร็จ
            $row = $this->db->select(array(
                't4.account_list_id', 't6.contract_number as contract_number_2',
                't4.principal_payment', 't4.interest', 't4.loan_amount_balance', 't4.total_amount', 't4.period_count',
                't8.share_id', 't8.share_collect_value', 't5.account_list', 't6.id', 't4.loan_id', 't4.loan_atm_id'
            ))
                ->from("coop_finance_month_profile as t1")
                ->join("coop_finance_month_detail as t2", 't1.profile_id = t2.profile_id', 'INNER')
                ->join("coop_receipt as t3", 't1.profile_id = t3.finance_month_profile_id', 'left')
                ->join("coop_finance_transaction as t4", 't3.receipt_id = t4.receipt_id', 'left')
                ->join("coop_account_list as t5", 't4.account_list_id = t5.account_id', 'left')
                ->join("coop_loan as t6", 't4.loan_id = t6.id', 'left')
                ->join("coop_loan_atm as t7", 't4.loan_atm_id = t7.loan_atm_id', 'left')
                ->join("coop_mem_share as t8", 't4.receipt_id = t8.share_bill', 'left')
                ->join("coop_deduct as t9", 't9.deduct_id = t2.deduct_id', 'INNER')
//		$this->db->where("t2.member_id ='".$data_arr['receipt_id']['member_id']."'");
                ->where("t2.member_id = {$member_id_run} ")
                ->where("t1.profile_month = {$mount_receipt} ")
                ->where("t3.receipt_id = '{$receipt_id_run}' ")
                ->group_by('t4.account_list_id,t4.loan_id')
                ->order_by('t9.seq_list_pdf ASC, t5.account_list ASC')
                ->get()->result_array();
             //echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>"; exit;


            $data_arr['transaction_data'] = $row;
            // echo"<pre>";print_r($data_arr['transaction_data']);
//รายการแต่ละใบเสร็จ

//รายการเดือนถัดไป
            $check_like_mount = "{$return_yearks}-$mount_receipt";
            $next_mount_check_period = sprintf("%02d", $mount_receipt_next);

            $row_next_mount_list =
                $this->db->select(array(
                    't1.pay_amount',
                    't1.pay_type',
                    't1.loan_id',
                    't1.deduct_code',
                    't2.contract_number',
                    't2.loan_amount_balance',
                    't3.share_collect_value',
                    't3.share_period', 't4.account_list_id',
                    "(SELECT MAX(period_count) FROM coop_finance_transaction as t11 where t11.loan_id = `t1`.`loan_id` AND t11.payment_date < '{$year_receipt_next}-{$next_mount_check_period}-01') as period_count",
                    't6.deduct_detail_short'
                ))
                    ->from("coop_finance_month_detail AS t1")
                    ->join("coop_finance_month_profile AS t5", 't5.profile_id = t1.profile_id', 'INNER')
                    ->join("coop_deduct as t6", 't6.deduct_id = t1.deduct_id', 'INNER')
                    ->join("coop_loan AS t2", 't1.loan_id = t2.id', 'left')
                    ->join("coop_finance_transaction AS t4", "t4.loan_id = t2.id AND t4.interest = 0  AND t4.payment_date LIKE '{$check_like_mount}%'", 'left')
                    ->join("coop_mem_share AS t3", 't1.member_id = t3.member_id', 'left')
                    ->where("t1.member_id = {$member_id_run} ")
                    ->where("t5.profile_month = {$mount_receipt_next}")
                    ->where("t5.profile_year = {$year_receipt_next}")
                    ->group_by('t1.deduct_code,t1.loan_id')
                    ->order_by('t6.seq_list_pdf ASC')
// // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";
                    ->get()->result_array();
            $data_arr['row_next_mount_list'] = $row_next_mount_list;
// echo"<pre>";print_r($data_arr['row_next_mount_list']);exit;
//รายการเดือนถัดไป

            //เงินฝากหลักประกัน

            $account_balance_all = array();
            foreach ($data_arr['transaction_data'] as $value) {
                $loan_id_run = $value['loan_id'];

                //เงินฝากหลักประกัน
                $row_account_balance = $this->db->select(array(
                    "account_id
				, (select transaction_balance from coop_account_transaction where coop_account_transaction.account_id = m.account_id AND `transaction_time` <= '" . $date_receipt_run . "'   ORDER BY transaction_time desc limit 1) as transaction_balance"
                ))
                    ->from("(SELECT `t8`.`account_id`
			FROM `coop_loan_type` as `t3`
			INNER JOIN `coop_loan_name` as `t4` ON `t3`.`id` = `t4`.`loan_type_id`
			LEFT JOIN `coop_loan` as `t5` ON `t5`.`loan_type` = `t4`.`loan_name_id`
			INNER JOIN `coop_deduct_guarantee_loan_type` as `t6` ON `t6`.`loan_type` = `t3`.`id`
			INNER JOIN `coop_deposit_type_setting` as `t7` ON ((`t7`.`deduct_guarantee_id` = `t6`.`deduct_guarantee_id`) OR (t7.type_code = 69 ) )
			INNER JOIN `coop_maco_account` as `t8` ON `t7`.`type_id` = `t8`.`type_id` AND `t5`.`member_id` = `t8`.`mem_id`
			WHERE ((`t5`.`id`= '{$loan_id_run}') OR ( t5.member_id = '{$member_id_run} ' AND t7.type_code = 69))
			) as m")
                    //		$this->db->where("t2.member_id ='".$data_arr['receipt_id']['member_id']."'");
                    ->group_by('account_id')
                    // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
                    ->get()->result_array();

                //เงินฝากหลักประกัน

                foreach ($row_account_balance as $value) {
                    $account_balance = array(
                        'account_id' => $value['account_id'],
                        'transaction_balance' => $value['transaction_balance']
                    );
                    array_push($account_balance_all, $account_balance);
                }

                $account_balance_all = array_unique($account_balance_all, SORT_REGULAR);

                $data_arr['transaction_data_account_balance'] = $account_balance_all;
            }
            //  echo"<pre>";print_r($account_balance_all);exit;
            //เงินฝากหลักประกัน
            $return_yearks = $return_year - 543;


            $guarantee_persons = array();
            foreach ($data_arr['transaction_data'] as $value) {
                $loan_id_run = $value['loan_id'];

                //คนค้ำประกัน
                $this->db->select(array(
                    't2.guarantee_person_id', 't3.firstname_th', 't3.lastname_th', 't3.member_id'
                ));
                $this->db->from("coop_loan AS t1 ");
                $this->db->join("coop_loan_guarantee_person AS t2", 't1.id = t2.loan_id', 'INNER');
                $this->db->join("coop_mem_apply as t3", 't2.guarantee_person_id = t3.member_id AND t3.member_status <> 3', 'INNER');
                $this->db->where("t1.id = {$loan_id_run} ");
                //echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";

                $row_guarantee_person = $this->db->get()->result_array();
                foreach ($row_guarantee_person as $value) {
                    $person_array = array(
                        'firstname_th' => $value['firstname_th'],
                        'lastname_th' => $value['lastname_th'],
                        'guarantee_person_id' => $value['guarantee_person_id']
                    );
                    array_push($guarantee_persons, $person_array);
                }
                // $data_arr['transaction_data_guarantee_person'] = $row_guarantee_person;
                // echo"<pre>";print_r($data_arr['transaction_data_guarantee_person']);exit;

//คนค้ำประกัน
            }
            $guarantee_persons = array_unique($guarantee_persons, SORT_REGULAR);
            // $guarantee_persons = array_unique($guarantee_persons);

            $data_arr['transaction_data_guarantee_person'] = $guarantee_persons;

            //echo"<pre>";print_r($guarantee_persons);exit;

            //ยอดทุนเรือนหุ้นสะสม
            $this->db->select(array(
                't1.share_collect_value'
            ));
            $this->db->from("coop_mem_share as t1 ");
            $this->db->where("t1.member_id ={$member_id_run}");
            $this->db->where("t1.share_date <= '{$date_receipt_run}'");
            $this->db->order_by('t1.share_date DESC');
            $this->db->limit(1);

            $row_collect_value = $this->db->get()->result_array();
            $data_arr['share_collect_value'] = $row_collect_value[0];

            //ยอดทุนเรือนหุ้นสะสม

            //ลายเซ็นต์
            $date_signature = date('Y-m-d');
            $this->db->select(array('*'));
            $this->db->from('coop_signature');
            $this->db->where("start_date <= '{$date_signature}'");
            $this->db->order_by('start_date DESC');
            $this->db->limit(1);
            // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;

            $row = $this->db->get()->result_array();
            $data_arr['signature'] = @$row[0];

            $share_all = 0;
            $period_loan_next_mount = array();
            $y_point = 57;
            foreach ($data_arr['transaction_data'] as $value) {

                $principal_payment = 0;$x = 0;
                $interest = 0;
                $total_amount = 0;
                $loan_amount_balance = 0;
                $period_count_num = 0;
                $where_text = '';
                if (!empty($value['loan_id'])) {
                    $where_text = " AND t4.loan_id ={$value['loan_id']}";
                }
                if (!empty($value['loan_atm_id'])) {
                    $where_text = " AND t4.loan_atm_id ={$value['loan_atm_id']}";
                }
                $row_plus = $this->db->select(array(

                    't4.loan_id', 't4.account_list_id', 't4.loan_atm_id', 't4.period_count', 't4.principal_payment', 't4.interest', 't4.loan_amount_balance', 't4.total_amount'
                ))
                    ->from("coop_finance_month_profile as t1")
                    ->join("coop_receipt as t3", 't1.profile_id = t3.finance_month_profile_id', 'left')
                    ->join("coop_finance_transaction as t4", 't3.receipt_id = t4.receipt_id', 'left')
                    //		$this->db->where("t2.member_id ='".$data_arr['receipt_id']['member_id']."'");
                    ->where("t4.receipt_id ='{$receipt_id_run}'")
                    ->where("t4.account_list_id ={$value['account_list_id']} {$where_text}")
                    ->get()->result_array();
                $data_arr['transaction_data_plus'] = $row_plus;

                $y_point += 8;
                $pdf->SetXY(4, $y_point);
                if ($value['account_list_id'] == 16) {
                    $pdf->MultiCell(58, 8, U2T('หุ้น'), $border, 'L');
                    $next_period = $value['period_count'] + 1;
                } else if ($value['account_list_id'] == 15) {
                    $pdf->MultiCell(58, 8, U2T($value['contract_number_2']), $border, 'L');
                    $period_loan_next_mount[$value['loan_id']] = $value['period_count'];
                } else if ($value['account_list_id'] == 30) {
                    $pdf->MultiCell(58, 8, U2T($value['account_list']), $border, 'L');
                    $next_period_deposit = $value['period_count'] + 1;
                } else if ($value['account_list_id'] == 31) {
                    $pdf->MultiCell(58, 8, U2T($value['account_list']), $border, 'L');
                    $next_period_atm = $value['period_count'] + 1;
                } else {
                    $pdf->MultiCell(58, 8, U2T($value['account_list']), $border, 'L');
                }

                $x = 0;
                $pdf->SetXY(62.3 , $y_point);
                //   !empty($value['period_count'])?$value['period_count']:'';
                $pdf->MultiCell(11, 8, U2T(!empty($value['period_count']) ? $value['period_count'] : ''), $border, 'C');
                if ($value['account_list_id'] == 16) {
                    $pdf->SetXY(74 , $y_point);
                    $pdf->MultiCell(30, 8, U2T(number_format($value['principal_payment'], 2)), $border, 'R');
                    $pdf->SetXY(104.9 , $y_point);
                    $pdf->MultiCell(23, 8, U2T(number_format($value['interest'], 2)), $border, 'R');
                    $pdf->SetXY(131 , $y_point);
                    $pdf->MultiCell(33, 8, U2T(number_format($value['total_amount'], 2)), $border, 'R');
                    $pdf->SetXY(166 , $y_point);
                    $pdf->MultiCell(32, 8, U2T(number_format($value['loan_amount_balance'], 2)), $border, 'R');
                    $total_amount_sum = $value['total_amount'];
                    $share_all = $value['loan_amount_balance'];

                } else {
                    foreach ($data_arr['transaction_data_plus'] as $value_plus) {

                        $principal_payment += $value_plus['principal_payment'];
                        $interest += $value_plus['interest'];
                        $total_amount += $value_plus['total_amount'];
                        $loan_amount_balance += $value_plus['loan_amount_balance'];
                        $period_count_num = $value_plus['period_count'];
                    }
                    $pdf->SetXY(74 , $y_point);
                    $pdf->MultiCell(30, 8, U2T(number_format($principal_payment, 2)), $border, 'R');
                    $pdf->SetXY(104.9 , $y_point);
                    $pdf->MultiCell(23, 8, U2T(number_format($interest, 2)), $border, 'R');
                    $pdf->SetXY(131 , $y_point);
                    $pdf->MultiCell(33, 8, U2T(number_format($total_amount, 2)), $border, 'R');
                    $pdf->SetXY(166 , $y_point);
                    $pdf->MultiCell(32, 8, U2T(number_format($loan_amount_balance, 2)), $border, 'R');
                    $total_amount_sum = $total_amount;

                }

                $sum = $sum + $total_amount_sum;
                $i++;
            }

            $y_point = 112;
            $pdf->SetXY(3, $y_point);
            $pdf->MultiCell(102, 8, U2T(convert($sum)), $border, 'C');
            $pdf->SetXY(130, $y_point);
            $pdf->MultiCell(32, 7, U2T(num_format($sum)), $border, 'R');

            //ลายเซ็น
            $y_point = 125;
            $pdf->SetXY(35, $y_point);

            if($_GET['signature'] == 'on'){
                echo   'assets/images/coop_signature/' . $data_arr['signature']['signature_3'];
                echo "\n\n";
                echo base_url() . PROJECTPATH . 'assets/images/coop_signature/' . $data_arr['signature']['signature_2'];
                exit;
            }


            $pdf->SetXY(142, 121);
            $pdf->MultiCell(80, 8, U2T($data_arr['signature']['manager_name']), 'C');

            $pdf->SetXY(45, 121);
            $pdf->MultiCell(80, 8, U2T($data_arr['signature']['finance_name']), 'C');

        }
	if($_GET['is_download'] == 'true'){
	 $pdf->Output('file_print.pdf', 'D');
	}else{
	 $pdf->Output();
	}
	 exit;
	 
		// $this->load->view('admin/receipt_account_month_spkt_pdf',$data_arr);
	}


    public function receipt_account_month_spkt_pdf_rev(){



        // ini_set('max_execution_time', 1800);
        function U2T($text) {
            //return @iconv("UTF-8", "TIS-620//IGNORE", ($text));
            //return @iconv("UTF-8", "cp874",$text);
            return $text;
        }
        function num_format($text) {
            if($text!=''){
                return number_format($text,2);
            }else{
                return '0.00';
            }
        }

//        function GETVAR($key, $default = null, $prefix = null, $suffix = null) {
//            return isset($_GET[$key]) ? $prefix . $_GET[$key] . $suffix : $prefix . $default . $suffix;
//        }

        function convert($number) {
            $txtnum1 = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ');
            $txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน');
            $number = str_replace(",","",$number);
            $number = str_replace(" ","",$number);
            $number = str_replace("บาท","",$number);
            $number = explode(".",$number);
            if(sizeof($number) > 2) {
                return 'ทศนิยมหลายตัวนะจ๊ะ';
                exit;
            }
            $strlen = strlen($number[0]);
            $convert = '';
            for($i=0;$i<$strlen;$i++){
                $n = substr($number[0], $i,1);
                if($n!=0){
                    if($i==($strlen-1) AND $n==1){ $convert .= 'เอ็ด'; }
                    elseif($i==($strlen-2) AND $n==2){ $convert .= 'ยี่'; }
                    elseif($i==($strlen-2) AND $n==1){ $convert .= ''; }
                    else{ $convert .= $txtnum1[$n]; }
                    $convert .= $txtnum2[$strlen-$i-1];
                }
            }
            if(!isset($number[1])) $number[1] = 0;
            $convert .= 'บาท';
            if($number[1]=='0' || $number[1]=='00' || $number[1]==''){
                $convert .= 'ถ้วน';
            }else{
                $strlen = strlen($number[1]);
                for($i=0;$i<$strlen;$i++){
                    $n = substr($number[1], $i,1);
                    if($n!=0){
                        if($number[1] == 01){$convert .= 'หนึ่ง';}
                        elseif($i==($strlen-1) AND $n==1 ){$convert .= 'เอ็ด';}
                        elseif($i==($strlen-2) AND $n==2){$convert .= 'ยี่';}
                        elseif($i==($strlen-2) AND $n==1){$convert .= '';}
                        else{ $convert .= $txtnum1[$n];}
                        $convert .= $txtnum2[$strlen-$i-1];
                    }
                }
                $convert .= 'สตางค์';
            }
            return $convert;
        }

        $month_arr = array('มกราคม'=>'1','กุมภาพันธ์'=>'2','มีนาคม'=>'3','เมษายน'=>'4','พฤษภาคม'=>'5','มิถุนายน'=>'6','กรกฎาคม'=>'7','สิงหาคม'=>'8','กันยายน'=>'9','ตุลาคม'=>'10','พฤศจิกายน'=>'11','ธันวาคม'=>'12');
        $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $data_arr = array();
        $this->db->select('*');
        $this->db->from("coop_receipt");
        $this->db->where("receipt_id = '".$receipt_id."'");
        $row = $this->db->get()->result_array();

        $return_year = @$_GET['year'];
        $mount_receipt = $_GET['month'];
        $mount_receipt_next = $mount_receipt +1;
        $return_yearks = $return_year-543;

        $where = "1=1  ";

        $pdf = new tFPDF('P','mm', "A5");

        $data_get = $this->input->get();
        // echo"<pre>";print_r($data_get);exit;

        $this->db->select('accm_month_ini');
        $this->db->from("coop_account_period_setting");
        $row_period_setting = $this->db->get()->result_array();
        $data_arr['row_period_setting'] = $row_period_setting;
        // echo"<pre>";print_r( $data_arr['row_period_setting']);exit;
        $years = $return_yearks;
        $years_old = $years-1;
        // $mount_start = sprintf("%02d",$data_arr['row_period_setting'][0]['accm_month_ini']);
        $mount_start = sprintf("%02d",$mount_receipt);
        $mount_old = $mount_start - 1;
        $mount_old = sprintf("%02d",$mount_old);
        if($mount_start == '01' || $mount_start == '1'){
            $mount_old = '12';
        }
        $date_start  = cal_days_in_month(CAL_GREGORIAN, $mount_start , $years);

        $between_date_old = "".$years_old.'-'.$mount_old."-01";
        $between_date_start = "".$years.'-'.$mount_start."-".$date_start;



        // $between_date_old = "".$years_old.'-'.$mount_old."-01";
        // $between_date_start = "".$years.'-'.$mount_start."-".$date_start;
        // echo $between_date_old .'<br>';
        // echo $between_date_start .'<br>';
        // exit;

        if($data_get['choose_receipt'] == '2'){
            $where .= !empty($data_get['member_id']) ? "AND t2.member_id = '{$data_get['member_id']}' " : "";
        }else if($data_get['choose_receipt'] == '3'){
            $where .= !empty($data_get['member_id_begin']) ? "AND t2.member_id BETWEEN '{$data_get['member_id_begin']}' AND '{$data_get['member_id_end']}' " : "";
        }else{
            // condition filter on choose receipt 1 By t.tawatsak
            if(!empty($data_get['department'])){
                $where .="AND (";
                $where .= !empty($data_get['department']) ? "t4.department='{$data_get['department']}' AND t5.department_old is null " : "";
                $where .= !empty($data_get['faction']) ? "AND t4.faction='{$data_get['faction']}' AND t5.faction_old is null " : "";
                $where .= !empty($data_get['level']) ? "AND t4.level='{$data_get['level']}' AND t5.level_old is null " : "";
                $where .=") OR ";
                $where .="(";
                $where .= !empty($data_get['department']) ? " t5.department_old ='{$data_get['department']}' AND t5.department <> '{$data_get['department']}' " : "";
                $where .= !empty($data_get['faction']) ? "AND t5.faction_old = '{$data_get['faction']}' AND t5.faction <> '{$data_get['faction']}' " : "";
                $where .= !empty($data_get['level']) ? "AND t5.level_old ='{$data_get['level']}' AND t5.level <> '{$data_get['level']}' " : "";
                $where .=")  ";
            }
            // selected page number
            // $end_limit = $data_get['page_number'] * 100;
            $end_limit = $data_get['page_number'] * 100;
            $start_limit = $end_limit - 100;

            !empty($start_limit) || $start_limit > 0 ? $start_limit = $start_limit : $start_limit = 0;
            !empty($end_limit) || $end_limit > 0 ? $end_limit = $end_limit : $end_limit = 100;

        }

        //รายการใบเสร็๗ทั้งหมดtop
        $this->db->select(array(
            't1.receipt_id','t3.id','t2.member_id','t1.receipt_datetime','t5.date_move'
        ));
        $this->db->from("coop_finance_month_detail as t0 ");
        $this->db->join("coop_receipt as t1", "t1.finance_month_profile_id = t0.profile_id AND t1.year_receipt = {$return_year}  AND t1.month_receipt = {$mount_receipt} ",'inner' );
        $this->db->join("coop_finance_transaction as t2", 't1.receipt_id = t2.receipt_id ','inner' );
        $this->db->join("coop_loan as t3", 't2.loan_id = t3.id', 'left');
        $this->db->join('coop_mem_apply as t4', 't1.member_id = t4.member_id', 'left');
        $this->db->join('coop_mem_group_move as t5',' t5.member_id = t4.member_id
		AND t5.date_move >= t1.receipt_datetime' , 'left');
        //$this->db->where("t1.receipt_id  NOT LIKE '%C%' ");

        $this->db->where($where);
        //echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
        $this->db->group_by('t1.receipt_id');
        // $this->db->order_by("t4.department,t4.faction,t4.level,t1.member_id  ASC");
        $this->db->order_by("t1.member_id  ASC");
        $this->db->limit($end_limit,$start_limit);
        //echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;

        $row_all = $this->db->get()->result_array();
        $data_arr['transaction_data_all'] = $row_all;
        // echo"<pre>";print_r($data_arr['transaction_data_all']);exit;
        $nn = 1;
        foreach($data_arr['transaction_data_all'] as $key => $transaction_data_all){

            $receipt_id_run = $transaction_data_all['receipt_id'];
            $member_id_run = $transaction_data_all['member_id'];
            $loan_id_run = $transaction_data_all['id'];
            $date_receipt_run = $transaction_data_all['receipt_datetime'];

            //เงินคืน
            $this->db->select(array(
                'SUM(return_amount) as sum_interest_amount'
            ));
            $this->db->from("coop_process_return");
            $this->db->where("member_id = {$member_id_run} ");
            $this->db->where("return_month = '{$mount_receipt}' ");
            $this->db->where("return_year =  '{$return_yearks}' ");

            $row_return_interest = $this->db->get()->result_array();
            $data_arr['return_interest'] = $row_return_interest[0];

            //ดอกเบี้ยสะสม (คำนวนดอกเบี้ยจากเงินที่จ่ายทั้งหมด)
            if($return_year == '2561' && $return_year == '10') {
                $this->db->select(array('interest_sum'));
                $this->db->from("interest_sum_detail");
                $this->db->where("member_id = {$member_id_run} ");
                $this->db->where("month = {$mount_receipt} ");
                $this->db->where("year = {$return_year} ");
//		$this->db->where("member_id = '".$data_arr['receipt_id']['member_id']."'");
                $row_interes = $this->db->get()->result_array();
                //interest_sum_detail
                $row_interes_interest_sum = str_replace(",", "", $row_interes[0]['interest_sum']);
            }else{

                $row_period_setting;

                $Years = $return_year + 543 > 3000 ? $return_year - 543 : $return_year;
                $months = intval($mount_receipt) > 12 ? $mount_receipt - 12 : $mount_receipt;
                $months = str_pad($months, 2, '0', STR_PAD_LEFT);

                $init = date('Y-m-t', strtotime(( intval($Years) - 1 ).'-'.($row_period_setting[0]['accm_month_ini'] - 1)));
                $stop = date('Y-m-t', strtotime($Years.'-'.$months));

                $this->load->model('interests');
                $interest = $this->interests->averageReturn($init, $stop, 100, $member_id_run)->interest;
                $row_interes_interest_sum = str_replace(",", "", $interest);
            }

            $row_interes_interest_sum =  str_replace(",","",$row_interes[0]['return_amount']);

            $data_arr['return_amount'] = $row_interes_interest_sum;
            if(empty($row_interes_interest_sum)){
                $row_interes_interest_sum =0;
            }
            //ดอกเบี้ยสะสม (คำนวนดอกเบี้ยจากเงินที่จ่ายทั้งหมด)

            if(empty($transaction_data_all['date_move'])){

                $this->db->select(array('t1.*','t2.prename_full','t3.mem_group_name','t3.mem_group_full_name','t3.mem_group_id'));
                $this->db->from("coop_mem_apply as t1");
                $this->db->join("coop_prename as t2",'t1.prename_id = t2.prename_id','left');
                $this->db->join("coop_mem_group as t3",'t1.level = t3.id','left');
                $this->db->where("t1.member_id = {$member_id_run} ");
                //		$this->db->where("member_id = '".$data_arr['receipt_id']['member_id']."'");
                $row = $this->db->get()->result_array();

            }else{
                $this->db->select(array('t1.*','t2.prename_full','t3.mem_group_name','t3.mem_group_full_name','t3.mem_group_id'));
                $this->db->from("coop_mem_apply as t1");
                $this->db->join("coop_prename as t2",'t1.prename_id = t2.prename_id','left');
                $this->db->join('coop_mem_group_move as t5','t5.member_id = t1.member_id','left');
                $this->db->join("coop_mem_group as t3",'t5.level_old = t3.id','left');
                $this->db->where("t1.member_id = {$member_id_run} ");
                //		$this->db->where("member_id = '".$data_arr['receipt_id']['member_id']."'");
                $row = $this->db->get()->result_array();

            }


            $profile = $this->db->select('*')->from('coop_profile')->limit(1)->get()->row();

            $data_arr['prename_full'] = @$row[0]['prename_full'];
            $data_arr['name'] = @$row[0]['firstname_th'].' '.@$row[0]['lastname_th'];
            $data_arr['member_data'] = @$row[0];

            $logo = 'assets/images/coop_profile/'.$profile->coop_img;
            $watermark = 'assets/images/coop_profile/'.$profile->img_alpha;

            $split = str_split(str_replace('#', '', $profile->color), 2);
            $r = hexdec($split[0]);
            $g = hexdec($split[1]);
            $b = hexdec($split[2]);

            //หัว pdf
            $pdf->AddPage();
            $pdf->AddFont('THSarabunNew','','thsarabunnew-webfont.ttf', 1);
            $pdf->AddFont('THSarabunNew','B','thsarabunnew_bold-webfont.ttf', 1);
            $pdf->SetMargins(0, 0, 0);
            $border = 1;

            $pdf->SetXY(0,0);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(150, 180, '',0, 0, 'C', 1);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetAutoPageBreak(false,0);
            $pdf->Image($watermark, 74-32, 115-32, 64, '', '', '', '', false, -300);

            $_font = 'THSarabunNew';
            //Logo

            $pdf->Image($logo, 10, 5, 20, '', '', '', '', false, 300);


            //Text Color
            $pdf->SetTextColor($r, $g, $b);
            $headerY = 3;
            $subHeaderY = $headerY+6;
            $headerX = 32;

            //Header
            $pdf->SetFont($_font, '', 9.5);
            $pdf->SetXY($headerX, $headerY);
            $pdf->Cell(100, 10, U2T($profile->coop_name_th), 0, 0,"L");

            //Sub Header
            $pdf->SetFont($_font, '', 6);
            $pdf->SetXY($headerX, $subHeaderY);
            $pdf->Cell(100, 8, U2T($profile->coop_name_en), 0, 0,"L");

            /***********************
             *      ที่อยู่สหกรณ์
             ***********************/
            $y_point = $subHeaderY + 4;
            $pdf->SetFont($_font, '', 6);
            $pdf->SetXY($headerX, $y_point);
            $pdf->Cell(40, 8, U2T($profile->address1." ".$profile->address2), 0, 1, "J");

            //Title
            $pdf->SetFont($_font, '', 16);
            $pdf->SetXY(55, $subHeaderY+11);
            $pdf->Cell(148, 12, U2T('ใบเสร็จรับเงิน'), 0, 0);



            //Descripttion
            $descriptY = $subHeaderY + 20;
            $descriptX = 12;
            $pdf->SetFont($_font, '', 9);

            $pdf->SetXY($descriptX, $descriptY);
            $pdf->SetTextColor($r, $g, $b);
            $pdf->Cell(20, 8, U2T("เลขที่ใบเสร็จ"),0,0,'L');
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(65, 8, U2T(@$receipt_id_run),0,0,'L');
            $pdf->SetTextColor($r, $g, $b);
            $pdf->Cell(20, 8, U2T("วันที่"),0,0,'L');
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(40, 8, U2T($this->center_function->mydate2date($date_receipt_run)),0,0,'L');

            $pdf->SetXY($descriptX, $descriptY+=7);
            $pdf->SetTextColor($r, $g, $b);
            $pdf->Cell(20, 8, U2T("ได้รับเงินจาก"),0,0,'L');
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(65, 8, U2T($data_arr['prename_full'].$data_arr['name']),0,0,'L');
            $pdf->SetTextColor($r, $g, $b);
            $pdf->Cell(20, 8, U2T("รหัสสมาชิก"),0,0,'L');
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(40, 8, U2T(@$row[0]['member_id']),0,0,'L');

            $pdf->SetXY($descriptX, $descriptY+=7);
            $pdf->SetTextColor($r, $g, $b);
            $pdf->Cell(20, 8, U2T("หน่วยงาน"),0,0,'L');
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(120, 8, U2T(@$row[0]['mem_group_name']),0,0,'L');

            //รายการ
            $y_point = $descriptY+12;
            $pdf->SetXY( $x = 9, $y_point );
            $pdf->SetFillColor($r, $g, $b);
            $pdf->SetDrawColor($r, $g, $b);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont($_font, '', 9);
            $pdf->Cell(40, 6, U2T("        รายการชำระ"),1,0,'', 1);
            $pdf->Cell(10, 6, U2T(" งวดที่"),1,0,'', 1);;
            $pdf->Cell(20, 6, U2T("    เงินต้น"),1,0,'', 1);
            $pdf->Cell(20, 6, U2T("   ดอกเบี้ย"),1,0,'', 1);
            $pdf->Cell(20, 6, U2T("    เป็นเงิน"),1,0,'', 1);
            $pdf->Cell(20, 6, U2T("    คงเหลือ"),1,0,'', 1);
            $sum=0;
            $pdf->SetFont($_font, '', 9);
            $pdf->SetTextColor($r, $g, $b);
            //หัว pdf

            $data_arr['mount_receipt'] = $month_arr[$mount_receipt_next];

            $data_arr['row_receipt'] = $row[0];


//		echo"<pre>";print_r($row[0]);exit;

            $mShort = array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
            $data_arr['str'] = "" ;
            $datetime = date("Y-m-d H:i:s");

            $tmp = explode(" ",$datetime);
            if( $tmp[0] != "0000-00-00" ) {
                $d = explode( "-" , $tmp[0]);

                $month = $mShort ;

                $str = $d[2] . " " . $month[(int)$d[1]].  " ".($d[0]>2500?$d[0]:$d[0]+543);

                $t = strtotime($datetime);
                $data_arr['str']  =$data_arr['str']. " ".date("H:i" , $t ) . " น." ;
            }

            $this->db->select('setting_value');
            $this->db->from("coop_share_setting");
            $this->db->where("setting_id = '1'");
            $row = $this->db->get()->result_array();

            $data_arr['share_value'] = $row[0]['setting_value'];

            $this->db->select(array('id','mem_group_name'));
            $this->db->from("coop_mem_group");
            $row = $this->db->get()->result_array();

            $mem_group_arr = array();
            foreach($row as $key => $value){
                $mem_group_arr[$value['id']] = $value['mem_group_name'];
            }

            $data_arr['mem_group_arr'] = $mem_group_arr;

            //รายการแต่ละใบเสร็จ
            $row =$this->db->select(array(
                't4.account_list_id', 't6.contract_number as contract_number_2',
                't4.principal_payment', 't4.interest', 't4.loan_amount_balance', 't4.total_amount','t4.period_count',
                't8.share_id','t8.share_collect_value','t5.account_list','t6.id','t4.loan_id','t4.loan_atm_id'
            ))
                ->from("coop_finance_month_profile as t1")
                ->join("coop_finance_month_detail as t2", 't1.profile_id = t2.profile_id','INNER' )
                ->join("coop_receipt as t3", 't1.profile_id = t3.finance_month_profile_id', 'left')
                ->join("coop_finance_transaction as t4",'t3.receipt_id = t4.receipt_id' , 'left')
                ->join("coop_account_list as t5",'t4.account_list_id = t5.account_id' , 'left')
                ->join("coop_loan as t6",'t4.loan_id = t6.id' , 'left')
                ->join("coop_loan_atm as t7",'t4.loan_atm_id = t7.loan_atm_id' , 'left')
                ->join("coop_mem_share as t8",'t4.receipt_id = t8.share_bill' , 'left')
                ->join("coop_deduct as t9",'t9.deduct_id = t2.deduct_id' , 'INNER')
//		$this->db->where("t2.member_id ='".$data_arr['receipt_id']['member_id']."'");
                ->where("t2.member_id = {$member_id_run} ")
                ->where("t1.profile_month = {$mount_receipt} ")
                ->where("t3.receipt_id = '{$receipt_id_run}' ")
                ->group_by('t4.account_list_id,t4.loan_id')
                ->order_by('t9.seq_list_pdf ASC, t5.account_list ASC')
                ->get()->result_array();
            // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";


            $data_arr['transaction_data'] = $row;
            // echo"<pre>";print_r($data_arr['transaction_data']);
//รายการแต่ละใบเสร็จ

//รายการเดือนถัดไป
            $check_like_mount = "{$return_yearks}-$mount_receipt";
            $row_next_mount_list =
                $this->db->select(array(
                    't1.pay_amount',
                    't1.pay_type',
                    't1.loan_id',
                    't1.deduct_code',
                    't2.contract_number',
                    't2.loan_amount_balance',
                    't3.share_collect_value',
                    't3.share_period','t4.account_list_id',
                    't4.period_count','t6.deduct_detail_short'
                ))
                    ->from("coop_finance_month_detail AS t1")
                    ->join("coop_finance_month_profile AS t5", 't5.profile_id = t1.profile_id', 'INNER')
                    ->join("coop_deduct as t6",'t6.deduct_id = t1.deduct_id' , 'INNER')
                    ->join("coop_loan AS t2", 't1.loan_id = t2.id','left' )
                    ->join("coop_finance_transaction AS t4", "t4.loan_id = t2.id AND t4.interest = 0  AND t4.payment_date LIKE '{$check_like_mount}%'", 'left')
                    ->join("coop_mem_share AS t3", 't1.member_id = t3.member_id', 'left')
                    ->where("t1.member_id = {$member_id_run} ")
                    ->where("t5.profile_month = {$mount_receipt_next}")
                    ->where("t5.profile_year = {$year_receipt_next}")
                    ->group_by('t1.deduct_code,t1.loan_id')
                    ->order_by('t6.seq_list_pdf ASC')
//echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";
                    ->get()->result_array();
            $data_arr['row_next_mount_list'] = $row_next_mount_list;
// echo"<pre>";print_r($data_arr['row_next_mount_list']);exit;
//รายการเดือนถัดไป

            //เงินฝากหลักประกัน

            $account_balance_all = array();
            foreach($data_arr['transaction_data'] as $value){
                $loan_id_run = $value['loan_id'];

                //เงินฝากหลักประกัน
                $row_account_balance = $this->db->select(array(
                    "account_id
				, (select transaction_balance from coop_account_transaction where coop_account_transaction.account_id = m.account_id AND `transaction_time` <= '".$date_receipt_run."'   ORDER BY transaction_time desc limit 1) as transaction_balance"
                ))
                    ->from("(SELECT `t8`.`account_id`
			FROM `coop_loan_type` as `t3`
			INNER JOIN `coop_loan_name` as `t4` ON `t3`.`id` = `t4`.`loan_type_id`
			LEFT JOIN `coop_loan` as `t5` ON `t5`.`loan_type` = `t4`.`loan_name_id`
			INNER JOIN `coop_deduct_guarantee_loan_type` as `t6` ON `t6`.`loan_type` = `t3`.`id`
			INNER JOIN `coop_deposit_type_setting` as `t7` ON ((`t7`.`deduct_guarantee_id` = `t6`.`deduct_guarantee_id`) OR (t7.type_code = 69 ) )
			INNER JOIN `coop_maco_account` as `t8` ON `t7`.`type_id` = `t8`.`type_id` AND `t5`.`member_id` = `t8`.`mem_id`
			WHERE ((`t5`.`id`= '{$loan_id_run}') OR ( t5.member_id = '{$member_id_run} ' AND t7.type_code = 69))
			) as m")
                    //		$this->db->where("t2.member_id ='".$data_arr['receipt_id']['member_id']."'");
                    ->group_by('account_id')
                    // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
                    ->get()->result_array();

                //เงินฝากหลักประกัน

                foreach($row_account_balance as $value){
                    $account_balance=array(
                        'account_id'=>$value['account_id'],
                        'transaction_balance'=>$value['transaction_balance']
                    );
                    array_push($account_balance_all,$account_balance);
                }

                $account_balance_all =  array_unique($account_balance_all, SORT_REGULAR);

                $data_arr['transaction_data_account_balance'] = $account_balance_all;
            }
            //  echo"<pre>";print_r($account_balance_all);exit;
            //เงินฝากหลักประกัน
            $return_yearks = $return_year-543;


            $guarantee_persons = array();
            foreach($data_arr['transaction_data'] as $value){
                $loan_id_run = $value['loan_id'];

                //คนค้ำประกัน
                $this->db->select(array(
                    't2.guarantee_person_id','t3.firstname_th','t3.lastname_th','t3.member_id'
                ));
                $this->db->from("coop_loan AS t1 ");
                $this->db->join("coop_loan_guarantee_person AS t2", 't1.id = t2.loan_id','INNER' );
                $this->db->join("coop_mem_apply as t3", 't2.guarantee_person_id = t3.member_id AND t3.member_status <> 3', 'INNER');
                $this->db->where("t1.id = {$loan_id_run} ");
                //echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";

                $row_guarantee_person = $this->db->get()->result_array();
                foreach($row_guarantee_person as $value){
                    $person_array=array(
                        'firstname_th'=>$value['firstname_th'],
                        'lastname_th'=>$value['lastname_th'],
                        'guarantee_person_id'=>$value['guarantee_person_id']
                    );
                    array_push($guarantee_persons,$person_array);
                }
                // $data_arr['transaction_data_guarantee_person'] = $row_guarantee_person;
                // echo"<pre>";print_r($data_arr['transaction_data_guarantee_person']);exit;

            //คนค้ำประกัน
            }
            $guarantee_persons =  array_unique($guarantee_persons, SORT_REGULAR);
            // $guarantee_persons = array_unique($guarantee_persons);

            $data_arr['transaction_data_guarantee_person'] = $guarantee_persons;

            //echo"<pre>";print_r($guarantee_persons);exit;

            //ยอดทุนเรือนหุ้นสะสม
            $this->db->select(array(
                't1.share_collect_value'
            ));
            $this->db->from("coop_mem_share as t1 ");
            //		$this->db->where("t2.member_id ='".$data_arr['receipt_id']['member_id']."'");
            $this->db->where("t1.member_id ={$member_id_run}");
            $this->db->where("t1.share_date <= '{$date_receipt_run}'");
            $this->db->order_by('t1.share_date DESC');
            $this->db->limit(1);
            // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";

            $row_collect_value = $this->db->get()->result_array();
            // $data_arr['share_collect_value'] = $row_collect_value[0];
            $data_arr['share_collect_value'] = $row_collect_value[0];

            //ยอดทุนเรือนหุ้นสะสม

            //รวมระหว่างดอกเบี้ยกับเงินต้น ในแต่ละ loan

            //ลายเซ็นต์
            $date_signature = date('Y-m-d');
            $this->db->select(array('*'));
            $this->db->from('coop_signature');
            $this->db->where("start_date <= '{$date_signature}'");
            $this->db->order_by('start_date DESC');
            $this->db->limit(1);

            $row = $this->db->get()->result_array();
            $data_arr['signature'] = @$row[0];

            //  }
            $share_all = 0;

            $isFirst = false;
            $row = 0;

            $pdf->SetTextColor(0,0,0);
            foreach($data_arr['transaction_data'] as $value){

                $principal_payment =0;
                $interest=0;
                $total_amount =0;
                $loan_amount_balance =0;
                $period_count_num =0;
                $where_text =  '';
                if(!empty($value['loan_id'])){
                    $where_text = " AND t4.loan_id ={$value['loan_id']}" ;
                }
                if(!empty($value['loan_atm_id'])){
                    $where_text = " AND t4.loan_atm_id ={$value['loan_atm_id']}" ;
                }
                $row_plus =$this->db->select(array(

                    't4.loan_id', 't4.account_list_id', 't4.loan_atm_id', 't4.period_count', 't4.principal_payment', 't4.interest', 't4.loan_amount_balance', 't4.total_amount'
                ))
                    ->from("coop_finance_month_profile as t1")
                    ->join("coop_receipt as t3", 't1.profile_id = t3.finance_month_profile_id', 'left')
                    ->join("coop_finance_transaction as t4",'t3.receipt_id = t4.receipt_id' , 'left')
                    //		$this->db->where("t2.member_id ='".$data_arr['receipt_id']['member_id']."'");
                    ->where("t4.receipt_id ='{$receipt_id_run}'")
                    ->where("t4.account_list_id ={$value['account_list_id']} {$where_text}")

                    // ->group_by('transaction_text')
                    // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
                    ->get()->result_array();
                $data_arr['transaction_data_plus'] = $row_plus;

                $border= "LR";
                if($isFirst){
                    $y_point += 8;
                }else{
                    $isFirst = true;
                    $y_point += 6;
                }
                $pdf->SetXY( 9, $y_point );
                if($value['account_list_id'] == 16){
                    $pdf->Cell(40, 8, U2T('หุ้น'), $border, 'L');
                    $next_period = $value['period_count']+1;
                }else if($value['account_list_id'] == 15){
                    $pdf->Cell(40, 8,U2T( $value['contract_number_2']), $border, 0,'L');
                }else if($value['account_list_id'] == 30){
                    $pdf->Cell(40, 8,U2T( $value['account_list']), $border, 0,'L');
                    $next_period_deposit = $value['period_count']+1;
                }else if($value['account_list_id'] == 31){
                    $pdf->Cell(40, 8,U2T( $value['account_list']), $border, 0,'L');
                    $next_period_atm = $value['period_count']+1;
                }else{
                    $pdf->Cell(40, 8,U2T( $value['account_list']), $border, 0,'L');
                }

                $pdf->Cell(10, 8, U2T(!empty($value['period_count'])?$value['period_count']:''), $border, 0,'C');

                if($value['account_list_id'] == 16){
                    $pdf->Cell(20, 8, U2T(number_format($value['principal_payment'],2)), $border, 0,'R');
                    $pdf->Cell(20, 8, U2T(number_format($value['interest'],2)), $border, 0,'R');
                    $pdf->Cell(20, 8, U2T(number_format($value['total_amount'],2)), $border, 0,'R');
                    $pdf->Cell(20, 8, U2T(number_format($value['loan_amount_balance'],2)), $border, 0,'R');
                    $total_amount_sum = $value['total_amount'];
                    $share_all = $value['loan_amount_balance'];

                }else{
                    foreach($data_arr['transaction_data_plus'] as $value_plus){

                        $principal_payment += $value_plus['principal_payment'];
                        $interest += $value_plus['interest'];
                        $total_amount += $value_plus['total_amount'];
                        $loan_amount_balance += $value_plus['loan_amount_balance'];
                        $period_count_num = $value_plus['period_count'];
                    }

                    $pdf->Cell(20, 8, U2T(number_format($principal_payment,2)), $border, 0,'R');
                    $pdf->Cell(20, 8, U2T(number_format($interest,2)), $border, 0,'R');
                    $pdf->Cell(20, 8, U2T(number_format($total_amount,2)), $border, 0,'R');
                    $pdf->Cell(20, 8, U2T(number_format($loan_amount_balance,2)), $border, 0,'R');
                    $total_amount_sum = $total_amount;

                }

                $sum += $total_amount_sum;
                $sum_insterest += $interest;
                $sum_balance += $loan_amount_balance;
                $i++;
                $row++;
            }

            $border= "LR";
            for($x=0; $x <= (12-$row); $x++){
                if($row == 0 && !$isFirst){
                    $y_point += 6;
                    $isFirst = true;
                }else{
                    $y_point += 8;
                }
                $pdf->SetXY( 9, $y_point );
                $pdf->Cell(40, 8, U2T(""), $border, 0,'R');
                $pdf->Cell(10, 8, U2T(""), $border, 0,'R');
                $pdf->Cell(20, 8, U2T(""), $border, 0,'R');
                $pdf->Cell(20, 8, U2T(""), $border, 0,'R');
                $pdf->Cell(20, 8, U2T(""), $border, 0,'R');
                $pdf->Cell(20, 8, U2T(""), $border, 0,'R');
            }

            $y_point += 8;
            $pdf->SetTextColor(254,254,254);
            $pdf->SetXY( 9, $y_point );
            $sum_convert = number_format($sum,2);
            //18
            $lens = 36;
            $txt_len = round(mb_strlen($this->center_function->convert($sum_convert), 'utf-8'));
            $sp = ' ' ;
            if($lens > $txt_len) {
                for ($i = 0; $i < ($lens - $txt_len) + 3; $i++) {
                    $sp .= ' ';
                }
            }

            $pdf->Cell(70, 8, U2T($sp.$this->center_function->convert($sum_convert)),1,0,'', 1);
            $pdf->Cell(20, 8, "","T",0,'R');
            $pdf->Cell(20, 8, number_format($sum,2),1,0,'C', 1);
            $pdf->Cell(20, 8, "","T",0,'C');

            //ลายเซ็น
            $pdf->SetTextColor($r, $g, $b);
            $pdf->SetFont('THSarabunNew', '', 9 );
            $y_point =190;

            $pdf->SetXY( 20, $y_point+5 );
            $pdf->Cell(35, 8, "", "T");
            $pdf->SetXY( 32, $y_point+5 );
            $pdf->Cell(40, 8, U2T("ผู้จัดการ "), 0, 1, "J");

            $pdf->SetXY( 93, $y_point+5 );
            $pdf->Cell(35, 8, "", "T");
            $pdf->SetXY( 98, $y_point+5 );
            $pdf->Cell(40, 8, U2T("หัวหน้าฝ่ายสินเชื่อ"), 0, 1, "J");

            // //ลายเซ็น
            if(file_exists(PROJECTPATH . 'assets/images/coop_signature/' . $data_arr['signature']['signature_3']) && !empty($data_arr['signature']['signature_3'])) {
                $pdf->Image(base_url() . PROJECTPATH . 'assets/images/coop_signature/' . $data_arr['signature']['signature_3'], 26 + 5, $y_point - 5, 15, '', '', '');
            }
            if(file_exists(PROJECTPATH . 'assets/images/coop_signature/' . $data_arr['signature']['signature_2']) && !empty($data_arr['signature']['signature_2'])){
                $pdf->Image(base_url() . PROJECTPATH . 'assets/images/coop_signature/' . $data_arr['signature']['signature_2'], 95 + 10, $y_point - 5, 15, '', '', '');
            }
            log_message('debug', '==================> ' . $nn);
            $nn++;



        }
        if($_GET['is_download'] == 'true') {

            $pdf->Output('file_print.pdf', 'D');

        }else if($_GET['is_image'] == 'true'){

            $_source = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).'/assets/document/'.@$receipt_id_run.'.pdf';
            $_target = '/assets/document/'.@$receipt_id_run.'.pdf';
            $receipt_id = @$receipt_id_run;

            $pdf->Output($_source,'F');
            header('Location: '.base_url('admin/pdf_to_image').'?_target='.$receipt_id);
            exit;

        }else if($_GET['is_base64'] == 'true'){

            $_source = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).'/assets/document/'.@$receipt_id_run.'.pdf';
            $_target = '/assets/document/'.@$receipt_id_run.'.pdf';
            $receipt_id = @$receipt_id_run;

            $pdf->Output($_source,'F');


            $this->imagick($receipt_id_run);

//            $_target = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).'/assets/images/templete_img/receipt';
//
//            $path = $_target. '/' . $receipt_id_run .'_1.jpg';
//            $type = pathinfo($path, PATHINFO_EXTENSION);
//            $data = file_get_contents($path);
//            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

            header('content-type: application/json;charset=utf-8');
            header("Access-Control-Allow-Origin: *");
            header('Access-Control-Allow-Headers: X-Requested-With, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');
            header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            echo json_encode(array('data'=> $receipt_id));
            exit;

        }else{
            header('content-type: application/json;charset=utf-8');
            header("Access-Control-Allow-Origin: *");
            header('Access-Control-Allow-Headers: X-Requested-With, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');
            header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $pdf->Output();
        }
        exit;
    }

    private function imagick($receipt_id_run){

        $_source = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).'/assets/document/'.$receipt_id_run.'.pdf[0]';
        $_target = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).'/assets/images/templete_img/receipt';
        $image = new Imagick($_source);

        $image->setResolution( 150, 150 );
        $image->readImage($_source);
        $num_pages = $image->getNumberImages();
        //$image->setImageCompressionQuality(100);

        for ($i = 0; $i < $num_pages; $i++) {
            $image->setIteratorIndex($i);
            $image->setImageFormat('png');
            $image->writeImage($_target . '/' . $receipt_id_run .'_'. $i .'.png');
        }

        $image->clear();
        $image->destroy();
    }

    public function pdf_to_image(){
        $data_arr['receipt_id'] = $_GET['_target'];
        $data_arr['_target'] = '/assets/document/'.$_GET['_target'].'.pdf';
        $this->load->view('admin/pdf_to_image', $data_arr);
    }

    public function receipt_form_pdf_rev($receipt_id,$receipt_id2=null){
        $receipt_id = $this->center_function->decrypt_text(jwt::urlsafeB64Decode($receipt_id));

        $receipt_id2 =!empty($receipt_id2)? '/'.$receipt_id2:'';
        $receipt_id  = urldecode($receipt_id.$receipt_id2);

        $data_arr = array();
        $this->db->select('*');
        $this->db->from("coop_receipt");
        $this->db->where("receipt_id ='".$receipt_id.$receipt_id2."'");
        $row = $this->db->get()->result_array();

        $data_arr['profile'] = $this->db->get("coop_profile")->row_array();
        $data_arr['row_receipt'] = $row[0];
        //วันที่ใบเสร็จ
        $receipt_datetime = date("Y-m-d", strtotime($data_arr['row_receipt']['receipt_datetime']));

        $this->db->select(array('t1.*','t2.mem_group_name','t3.prename_full'));
        $this->db->from("(SELECT IF (
										(
											SELECT
												level_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$receipt_datetime."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										(
											SELECT
												level_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$receipt_datetime."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										coop_mem_apply. level
									) AS level, member_id, firstname_th, lastname_th, mem_type_id, prename_id FROM coop_mem_apply) as t1");
        $this->db->join("coop_mem_group as t2",'t1.level = t2.id','left');
        $this->db->join("coop_prename as t3",'t1.prename_id = t3.prename_id','left');
        $this->db->where("member_id ='".$data_arr['row_receipt']['member_id']."'");
        $row = $this->db->get()->result_array();
        //echo $this->db->last_query(); exit;

        $data_arr['prename_full'] = @$row[0]['prename_full'];
        $data_arr['name'] = @$row[0]['firstname_th'].' '.@$row[0]['lastname_th'];
        $data_arr['member_data'] = @$row[0];
        $data_arr['member_id'] = @$row[0]['member_id'];
        //		echo"<pre>";print_r($row[0]);exit;

        $this->db->select(array(
            't1.*',
            't2.account_list'
        ));
        $this->db->from("coop_finance_transaction as t1");
        $this->db->join('coop_account_list as t2', 't1.account_list_id = t2.account_id');
        $this->db->where("t1.receipt_id = '".$receipt_id.$receipt_id2."'");
        $this->db->order_by('t1.member_id ASC');
        $row = $this->db->get()->result_array();


        $transactions = array();
        foreach($row as $transaction) {
            if($transaction['account_list_id'] == '30'){
                $transaction['transaction_text'] = $transaction['account_list'];
            }
            if (!empty($transaction['loan_id'])) {
                if(!empty($transaction['principal_payment'])) $transactions[$transaction['loan_id']]['transaction_text_main'] = $transaction['transaction_text'];
                $transactions[$transaction['loan_id']]['transaction_text'] = $transaction['transaction_text'];
                if(!empty($transaction['principal_payment'])) $transactions[$transaction['loan_id']]['transaction_text_main'] = $transaction['transaction_text'];
//                $transactions[$transaction['loan_id']]['transaction_text'] = $transaction['account_list'];
                if(!empty($transaction['period_count'])) $transactions[$transaction['loan_id']]['period_count'] = $transaction['period_count'];
                $transactions[$transaction['loan_id']]['principal_payment'] += $transaction['principal_payment'];
                $transactions[$transaction['loan_id']]['interest'] += $transaction['interest'];
                $transactions[$transaction['loan_id']]['loan_interest_remain'] += $transaction['loan_interest_remain'];
                if(!empty($transaction['loan_amount_balance'])) $transactions[$transaction['loan_id']]['loan_amount_balance'] = $transaction['loan_amount_balance'];
            } else if (!empty($transaction['loan_atm_id'])) {
                if(!empty($transaction['principal_payment'])) $transactions["atm_".$transaction['loan_atm_id']]['transaction_text_main'] = $transaction['transaction_text'];
                $transactions["atm_".$transaction['loan_atm_id']]['transaction_text'] = $transaction['transaction_text'];
                if(!empty($transaction['period_count'])) $transactions["atm_".$transaction['loan_atm_id']]['period_count'] = $transaction['period_count'];
                $transactions["atm_".$transaction['loan_atm_id']]['principal_payment'] += $transaction['principal_payment'];
                $transactions["atm_".$transaction['loan_atm_id']]['interest'] += $transaction['interest'];
                $transactions["atm_".$transaction['loan_atm_id']]['loan_interest_remain'] += $transaction['loan_interest_remain'];
                if(!empty($transaction['loan_amount_balance'])) $transactions["atm_".$transaction['loan_atm_id']]['loan_amount_balance'] = $transaction['loan_amount_balance'];
            } else {
                $transactions[] = $transaction;
            }
        }

        $data_arr['transaction_data'] = $transactions;

        //ลายเซ็นต์
        $date_signature = date('Y-m-d');
        $this->db->select(array('*'));
        $this->db->from('coop_signature');
        $this->db->where("start_date <= '{$date_signature}'");
        $this->db->order_by('start_date DESC');
        $this->db->limit(1);
        $row = $this->db->get()->result_array();
        //		echo"<pre>";print_r($row);exit;
        $data_arr['signature'] = @$row[0];

        $this->db->select('*');
        $this->db->from("coop_loan");
        $this->db->where("deduct_receipt_id = '".$receipt_id.$receipt_id2."'");
        $row = $this->db->get()->result_array();
        $data_arr['pay_for_loan']['contract_number'] = @$row[0]['contract_number'];

        $code_name = 'receipt_form';
        $this->db->select('switch_code');
        $this->db->from('format_setting_report');
        $this->db->where("code_name = '".$code_name."'");
        $setting_report = $this->db->get()->row_array();
        $data_arr['switch_code'] = $setting_report['switch_code'];

//        echo '<pre>';print_r($data_arr);exit;
        $this->load->view('admin/receipt_form_pdf_rev', $data_arr);
    }

    public function info(){
	    echo phpinfo();
    }


}
	
