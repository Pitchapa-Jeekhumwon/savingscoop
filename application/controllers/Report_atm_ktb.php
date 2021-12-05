<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_atm_ktb extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function coop_report_ktb(){
		$arr_data = array();
		
		$this->libraries->template('report_atm_ktb/coop_report_ktb',$arr_data);
	}
	
	function coop_report_ktb_preview(){
		$arr_data = array();
		$arr_data['month_arr'] = $this->month_arr;
		$arr_data['month_short_arr'] = $this->month_short_arr;	

		if(@$_GET['report_date'] != ''){
			$date_arr = explode('/',@$_GET['report_date']);
			$day = @$date_arr[0];
			$month = @$date_arr[1];
			$year = @$date_arr[2];
			$year -= 543;
			$report_date = $day.'/'.$month.'/'.$year;
			$transaction_date = $year.$month.$day;
		}		
		$arr_data['report_date'] = @$report_date;	

		$this->db->select(array('*'));
		$this->db->from('message_request_atm_ktb');
		$this->db->where("transaction_date = '{$transaction_date}'");
		$rs_request = $this->db->get()->result_array();	

		$data_request = array();
		$chk_transaction_code = array('0100'=>'0110','0200'=>'0210','0400'=>'0410');
		foreach($rs_request AS $key=>$val){
			$transaction_code = $chk_transaction_code[$val['transaction_code']];
			$this->db->select(array('*'));
			$this->db->from('message_response_atm_ktb');
			//$this->db->where("transaction_date = '{$transaction_date}' AND bank_reference_number = '{$val['bank_reference_number']}'");
			$this->db->where("transaction_date = '{$val['transaction_date']}' AND transaction_time = '{$val['transaction_time']}' AND transaction_code = '{$transaction_code}' AND bank_reference_number = '{$val['bank_reference_number']}'");
			$this->db->limit(1);
			$rs_response = $this->db->get()->row_array();
			if($_GET['debug'] == 'dev'){
				echo $this->db->last_query(); echo ';<br>';
			}
		
			$data_request[$key] = $val;
			$data_request[$key]['transaction_amount_req'] = $this->text_to_decimal($val['transaction_amount']);
			$data_request[$key]['transaction_amount_act'] = $this->text_to_decimal($rs_response['transaction_amount']);
			$data_request[$key]['transaction_date'] = $this->convert_date($val['transaction_date']);
			$data_request[$key]['transaction_time'] = $this->convert_time($val['transaction_time']);
			$data_request[$key]['response_code'] = $rs_response['response_code'];
			
		}
		
		$arr_data['arr_transaction_code'] = array('0100'=>'Inquiry','0200'=>'Financial','0400'=>'Reverse Financial');
		$arr_data['arr_list_id'] = array('001'=>'Inquiry','002'=>'Withdraw','003'=>'Pay the loan / Deposit');
		$arr_data['arr_from_acct_type'] = array('01'=>'Loan','02'=>'Deposit');
		$arr_data['arr_response_code'] = array('0000'=>'Approve','0011'=>'Declined','0012'=>'Please Retry','0005'=>'Stop Sending','0095'=>'Invalid Authentication','0099'=>'System s Unavailable');
		$arr_data['data'] = $data_request;
		if(@$_GET['export']=="excel"){
			$this->load->view('report_atm_ktb/coop_report_ktb_preview',$arr_data);
		}else{
			$this->preview_libraries->template_preview('report_atm_ktb/coop_report_ktb_preview',$arr_data);
		}
	}
	
	//แปลงจำนวนเงิน เป็นตัวเลขมีทศนิยม
	public function text_to_decimal($data){
		$result = '';
		$data_integer = substr(@$data,0,13);//จำนวนเต็ม
		$data_decimal = substr(@$data,13,2);//ทศนิยม
		$result = (int)$data_integer.'.'.sprintf("%02d", $data_decimal);//ยอดเงินที่ถอน	
		return $result;
	}
	
	public function convert_date($transaction_date){
		if($transaction_date != ''){
			$transaction_yy = substr(@$transaction_date,0,4);//ปี
			$transaction_mm = substr(@$transaction_date,4,2);//เดือน
			$transaction_dd = substr(@$transaction_date,6,2);//วัน			
			$data_date = $transaction_dd.'/'.$transaction_mm.'/'.$transaction_yy;	
		}		
		return $data_date;
	}
	
	public function convert_time($transaction_time){
		if($transaction_time != ''){
			$transaction_h = substr(@$transaction_time,0,2);//ชั่วโมง
			$transaction_i = substr(@$transaction_time,2,2);//นาที
			$transaction_s = substr(@$transaction_time,4,2);//วินาที
			$data_time = $transaction_h.':'.$transaction_i.':'.$transaction_s;	
		}
		
		return $data_time;
	}
	
	public function coop_report_ktb_account(){
		$arr_data = array();

		$this->libraries->template('report_atm_ktb/coop_report_ktb_account',$arr_data);
	}
	
	function coop_report_ktb_account_preview(){
		$arr_data = array();
		$arr_data['month_arr'] = $this->month_arr;
		$arr_data['month_short_arr'] = $this->month_short_arr;	

		if(@$_GET['report_date'] != ''){
			$date_arr = explode('/',@$_GET['report_date']);
			$day = @$date_arr[0];
			$month = @$date_arr[1];
			$year = @$date_arr[2];
			$year -= 543;
			$report_date = $day.'/'.$month.'/'.$year;
			$date = $year.'-'.$month.'-'.$day;
		}		
		$arr_data['report_date'] = @$report_date;	
		
		$where = "";
		if(@$_GET['check_all'] == '1'){
			$where = "account_id_atm <> ''";
		}else{
			$where = "account_id_atm_update LIKE '{$date}%'";
		}

		$this->db->select(array('*'));
		$this->db->from('coop_maco_account');
		$this->db->where($where);
		$this->db->order_by("account_id_atm_update ASC");
		$rs= $this->db->get()->result_array();	
		//echo $this->db->last_query();
		
		$arr_data['data'] = $rs;
		$this->preview_libraries->template_preview('report_atm_ktb/coop_report_ktb_account_preview',$arr_data);
	}
	
	public function coop_report_ktb_import(){
		$arr_data = array();
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select("*");
		$this->paginater_all->main_table('coop_import_data_ktb_for_date');
		$this->paginater_all->where($where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->group_by('import_date_file');
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];
		
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;

		$this->libraries->template('report_atm_ktb/coop_report_ktb_import',$arr_data);
	}
	
	public function ktb_import_save() {
		$arr_data = array();
		if(!empty($_FILES)) {
			$datas = $this->read_csv($_FILES);	
			if(gettype($datas) == "string"){	
				$this->center_function->toastDanger($datas);
				echo "<script> document.location.href='".base_url(PROJECTPATH.'/report_atm_ktb/coop_report_ktb_import')."' </script>";
			} else {				
				$data_inserts = array();
				if(!empty($datas['data'])){
					foreach($datas['data'] as $key => $data) {
						$data_insert = array();
						$data_insert["import_ser"] = trim($data["import_ser"]);
						$data_insert["import_bank"] = trim($data["import_bank"]);
						$data_insert["import_acct"] = trim($data["import_acct"]);
						$data_insert["import_coop_id"] = trim($data["import_coop_id"]);
						$data_insert["import_mem_id"] = trim($data["import_mem_id"]);
						$data_insert["import_act"] = trim($data["import_act"]);
						$data_insert["import_result"] = trim($data["import_result"]);
						$data_insert["admin_id"] = $_SESSION['USER_ID'];
						$data_insert["import_datecreate"] = $datas['import_datecreate'];
						$data_insert["import_date_file"] = $datas['import_date_file'];
						$data_inserts[] = $data_insert;
					}			
					//echo '<pre>'; print_r($data_inserts); echo '</pre>';
					if (!empty($data_inserts)) {
						$this->db->insert_batch('coop_import_data_ktb', $data_inserts);
						$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
						echo "<script> document.location.href='".base_url(PROJECTPATH.'/report_atm_ktb/coop_report_ktb_import')."' </script>";
					}
				}else{
					$this->center_function->toastDanger('ไม่สามารถนำเข้าข้อมูลได้');
					echo "<script> document.location.href='".base_url(PROJECTPATH.'/report_atm_ktb/coop_report_ktb_import')."' </script>";
				}
			}			
		}
		exit;
	}
	
	public function read_csv($files){
		$explode_file = explode('.',$files["file"]["name"]);
		$type_file = $explode_file[1];//CSV
		if($type_file != 'CSV'){
			return "ไฟล์ไม่ถูกต้อง";
		}
		
		$arr_result = array();
		if(isset($files["file"])){			
			$date_gen_file = date('YmdHis');
			if($files["file"]["error"] > 0) {
				echo "Return Code: " . $files["file"]["error"] . "<br />";
			}else{
				//if file already exists
				$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/ktb_import/";
				if(file_exists($output_dir.$files["file"]["name"])) {
					echo $files["file"]["name"] . " already exists. ";
				}else {
					$storagename = "uploaded_file_tmp_".$date_gen_file.".txt";
					move_uploaded_file($files["file"]["tmp_name"], $output_dir.$storagename);
				}
			}
		}else{
			 echo "No file selected <br />";
		}
		
		if(isset($storagename) && $file = fopen($output_dir.$storagename , r )){
			$firstline = 0;
			$num = strlen($firstline);
			//save the different fields of the firstline in an array called fields
			$fields = array();
			$fields = explode( ";", $firstline, ($num+1) );

			$line = array();
			$i = 0;
			while ( $line[$i] = fgets ($file, 4096) ) {
				$dsatz[$i] = array();
				$dsatz[$i] = explode( ";", $line[$i], ($num+1) );
				$i++;
			}
			
			$arr_data = array();
			$arr_result['import_datecreate']  = date('Y-m-d H:i:s');		
			foreach($dsatz as $key => $number){
				if($key == 0){
					$arr_date = explode('/',substr($number[0],108, 10));
					$import_date = $arr_date[2].'-'.sprintf("%02d", $arr_date[1]).'-'.sprintf("%02d", $arr_date[0]);
					
				}
				if($key == 1){
					$import_time = substr($number[0],108,8);
				}
				if($key > 5){		
					foreach ($number as $k => $content) {
						$data = array();
						$data[] = $content;						
						foreach ($data as $item){							
							$arr_data[$key]['import_ser']	= substr($item,0, 6);
							$arr_data[$key]['import_bank']	= substr($item,6, 6);
							$arr_data[$key]['import_acct']	= substr($item,12, 12);
							$arr_data[$key]['import_coop_id']	= substr($item,24, 20);
							$arr_data[$key]['import_mem_id']	= substr($item,44,13);
							//$arr_data[$key]['import_coop_acct']= substr($item,57,48);
							//$arr_data[$key]['import_type']	= substr($item,57,48);
							$arr_data[$key]['import_act']     = substr($item,57,48);
							$arr_data[$key]['import_result']  = substr($item,105,30);
						}
					}
				}
			}
		}

		$arr_result['import_date_file'] = @$import_date.' '.$import_time;
		$this->db->select("*");
		$this->db->from('coop_import_data_ktb');
		$this->db->where("import_date_file = '".$arr_result['import_date_file']."'");
		$check_data = $this->db->get()->row_array();

		if($arr_result['import_date_file'] == $check_data['import_date_file']){
			//return "ไม่สามรถนำเข้า <br>วันที่ ".$this->center_function->ConvertToThaiDate($arr_result['import_date_file'],0,1)." ได้ <br>เนื่องมีการทำการนำเข้าข้อมูลแล้ว";
			$this->db->where("import_date_file = '".$check_data['import_date_file']."'");
			$this->db->delete("coop_import_data_ktb");
		}
		
		if(!empty($arr_data)){
			foreach ($arr_data as $key => $val) {
				if($val['import_bank'] == '0006'){
					$arr_result['data'][] = @$val;
				}
			}
		}
		return $arr_result;
	}
	
	
	public function get_data_ktb_import($data){
		$where = '1=1';
		$check_where = 0;
		if(@$data['member_id'] != ''){
			$where .= " AND t1.import_mem_id = '".@$data['member_id']."'";
			$check_where = 1;
		}
		
		if(@$data['start_date']){
			$start_date_arr = explode('/',@$data['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			$arr_data['start_date'] = @$start_date;
		}
		
		if(@$data['end_date']){
			$end_date_arr = explode('/',@$data['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
			$arr_data['end_date'] = @$end_date;
		}
		
		if(@$data['start_date'] != '' && @$data['end_date'] == ''){
			$where .= " AND t1.import_date_file BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
			$check_where = 1;
		}else if(@$data['start_date'] != '' && @$data['end_date'] != ''){
			$where .= " AND t1.import_date_file BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$check_where = 1;
		}
		
		if($check_where > 0){
			$this->db->select("t1.*,CONCAT(t2.firstname_th,' ',t2.lastname_th) AS member_name");
			$this->db->from('coop_import_data_ktb AS t1');
			$this->db->join("coop_mem_apply AS t2","t1.import_mem_id =  t2.member_id","left");
			$this->db->where($where);
			$this->db->order_by("t1.import_date_file ASC,t1.import_ser ASC");
			$datas = $this->db->get()->result_array();
		}else{
			$datas = array();
		}	
		return $datas;
	}
	
	public function coop_report_ktb_import_view(){
		$arr_data = array();		
		$datas = $this->get_data_ktb_import($_GET);
		//echo '<pre>'; print_r($datas); echo '</pre>'; exit;
		$arr_data['datas'] = @$datas;
		$this->libraries->template('report_atm_ktb/coop_report_ktb_import_view',$arr_data);
	}
	
	public function coop_report_ktb_import_excel(){
		$arr_data = array();		
		$datas = $this->get_data_ktb_import($_GET);
		$arr_data['datas'] = @$datas;
		$this->load->view('report_atm_ktb/coop_report_ktb_import_excel',$arr_data);
	}
	
	function coop_report_ktb_account_detail_preview(){
		$arr_data = array();
		$arr_data['month_arr'] = $this->month_arr;
		$arr_data['month_short_arr'] = $this->month_short_arr;	

		if(@$_GET['report_date'] != ''){
			$date_arr = explode('/',@$_GET['report_date']);
			$day = @$date_arr[0];
			$month = @$date_arr[1];
			$year = @$date_arr[2];
			$year -= 543;
			$report_date = $day.'/'.$month.'/'.$year;
			$date = $year.'-'.$month.'-'.$day;
		}		
		$arr_data['report_date'] = @$report_date;	
		
		$where = "1=1";
		if(@$_GET['account_id'] != ''){
			$where = "account_id = '{$_GET['account_id']}'";
		}

		$this->db->select(array('account_id','mem_id','account_name'));
		$this->db->from('coop_maco_account');
		$this->db->where($where);
		$this->db->order_by("account_id_atm_update ASC");
		$row = $this->db->get()->row_array();	
		
		$this->db->select(array('t1.account_id','t1.account_id_atm','t1.account_id_atm_update','t1.account_atm_status','t2.user_name'));
		$this->db->from('coop_account_atm_log t1');
		$this->db->join('coop_user t2','t1.admin_id = t2.user_id','left');
		$this->db->where($where);
		$this->db->order_by("t1.account_id_atm_update ASC");
		$row_detail = $this->db->get()->result_array();
		
		$arr_data['data'] = $row;
		$arr_data['data_detail'] = $row_detail;
		$arr_data['arr_atm_status'] = array('A'=>'เพิ่ม','U'=>'แก้ไข','D'=>'ลบ');

		if(@$_GET["export"]=="excel"){
			$this->load->view('report_atm_ktb/coop_report_ktb_account_detail_preview',$arr_data);
		}else{
			$this->preview_libraries->template_preview('report_atm_ktb/coop_report_ktb_account_detail_preview',$arr_data);
		}
	}

}
