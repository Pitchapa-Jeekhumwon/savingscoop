<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
//header("Content-Type:text/json;charset=utf-8");
//header("Access-Control-Allow-Origin: *");
//header('Access-Control-Allow-Headers: X-Requested-With, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');
class Report_receipt_account_month extends CI_Controller {

    public $CI;

	function __construct()
	{
		parent::__construct();
        $this->load->library(array('libTFPDF'));
        $this->CI =&get_instance();

	}

    public function report_receipt_account_month_encode(){
        $text2 = '';
        foreach ($_GET as $key => $value){
            $text2 .= $key.'='.$value.'&';
        }
        $text2 =  base64_encode($text2);
//        echo PROJECTPATH."/report_receipt_account_month/report_receipt_account_month_preview?code=".$text2;
        header( "location: ".PROJECTPATH."/report_receipt_account_month/report_receipt_account_month_preview?code=".$text2 );


    }

	public function report_receipt_account_month_preview(){
        if(@$_SESSION['USER_ID']=='') {
            header( "location: ".PROJECTPATH."/auth?return_url=%2Fcashier%2Fcashier_month" );
            exit;
        }
        parse_str($this->center_function->decrypt($_GET['code']), $_GET);
        $this->load->model('Report_receipt_account_month_model');

		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$data_arr = array();

        $data_arr['datas'] = $this->Report_receipt_account_month_model->report_receipt_account_month_model();

        //ลายเซ็นต์
        $date_signature = date('Y-m-d');
        $this->db->select(array('*'));
        $this->db->from('coop_signature');
        $this->db->where("start_date <= '{$date_signature}'");
        $this->db->order_by('start_date DESC');
        $this->db->limit(1);
        $row = $this->db->get()->result_array();
        $data_arr['signature'] = @$row[0];

        $data_arr['profile'] = $this->db->select('*')->from('coop_profile')->limit(1)->get()->row_array();

        $data_arr['setting_receipt'] = $this->db->select('*')->from('coop_setting_receipt')->where("type = 1 AND status = 1")->order_by("id DESC")->get()->row_array();
//        $data_arr['setting_receipt']['copy_status'] = '0'; //ไม่มีสำเนา

        $arr_pay_type = array('0'=>'เงินสด','1'=>'โอนเงิน');
        $data_arr['pay_type'] =  $arr_pay_type;

        if ($_GET['dev'] == 'dev') {
            echo '<pre>';
            print_r($data_arr['datas']);
            exit;
        }

		 $this->load->view('report_receipt_account_month/report_receipt_account_month_preview',$data_arr);
	}

}
	
