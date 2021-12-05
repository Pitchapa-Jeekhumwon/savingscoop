<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
    Receipt_type
    1=ใบเสร็จทั่วไป admin/receipt_form_pdf
    2=ใบเสร็จการผ่านรายการรายเดือน report_receipt_account_month/report_receipt_account_month_encode
*/
class Setting_receipt extends CI_Controller {
	
	function __construct() {
		parent::__construct();
	}

    //other monthly receipt.
    public function index(){
        $arr_data = array();
        $arr_data['type'] = 1;
        $arr_data['receipt_size'] = $this->db->select('*')->from('receipt_size')->where("`nav_hidden` = '0' or `nav_hidden` is null")->get()->result_array();
        $arr_data['setting_receipt'] = $this->db->select('*')->from('coop_setting_receipt')->where("type = 1 AND status = 1")->order_by("id DESC")->get()->row_array();
        $this->libraries->template('setting_receipt/index', $arr_data);
    }

    //Monthly receipt.
    public function monthly(){
        $arr_data = array();
        $arr_data['type'] = 2;
        $arr_data['receipt_size'] = $this->db->select('*')->from('receipt_size')->where("`nav_hidden` = '0' or `nav_hidden` is null")->get()->result_array();
        $arr_data['setting_receipt'] = $this->db->select('*')->from('coop_setting_receipt')->where("type = 2 AND status = 1")->order_by("id DESC")->get()->row_array();
        $this->libraries->template('setting_receipt/index', $arr_data);
    }

    function save_setting_receipt() {
        $type = $_POST["type"];

        $data_insert = array();
        $data_insert['status']= 2;
        $this->db->where('type', $type);
        $this->db->update('coop_setting_receipt', $data_insert);

        $data_insert = array();
        $data_insert['status'] = 1;
        $data_insert['type'] = $type;
        $data_insert['receipt_size_id'] = $_POST['approval_id'][0];
        $data_insert['copy_status'] = $_POST['copy_receipt'];
        $data_insert['sign_manager'] = $_POST['sign_manager'];
        $data_insert['header_status'] = $_POST['header_status'];
        $data_insert['loan_int_debt'] = $_POST['loan_int_debt'];
        $data_insert['alpha'] = $_POST['alpha'];
        $data_insert['sign_1'] = !empty($_POST['sign_1_check']) ? $_POST['sign_1'] : 0;
        $data_insert['sign_2'] = !empty($_POST['sign_2_check']) ? $_POST['sign_2'] : 0;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        $data_insert['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('coop_setting_receipt', $data_insert);

        echo '<meta http-equiv= "refresh" content="0; url=/setting_receipt"/>';
    }
}