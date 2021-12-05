<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_loan_pay_normal extends CI_Controller {
	function __construct()
	{
        parent::__construct();
    }

    public function index(){
        $arr_data = array();
        $this->db->select('id, loan_type');
        $this->db->from('coop_loan_type');
        $this->db->order_by('order_by');
        $loan_type = $this->db->get()->result_array();
        $arr_data['loan_type'] = $loan_type;
        $this->libraries->template('report_loan_pay_normal/index',$arr_data);
    }

    public function report_loan_pay_normal_excel(){
        $arr_data = array();
        $loan_type = $_GET['loan_type'];
        $date_start = $_GET['date_start'];
        $date_end = $_GET['date_end'];
        $date_start_arr = explode("/", $date_start);
        $date_end_arr = explode("/", $date_end);
        $date_start = ($date_start_arr[2]-543).'-'.$date_start_arr[1].'-'.$date_start_arr[0].' 00:00:00';
        $date_end = ($date_end_arr[2]-543).'-'.$date_end_arr[1].'-'.$date_end_arr[0].' 23:59:59';
        $where = "t1.createdatetime BETWEEN '$date_start' AND '$date_end' AND t1.transfer_status IS NOT NULL";
        if($_GET['loan_type'] != ''){
            $where .= "AND t11.loan_type_id = '$loan_type'";
            $loan_type_data = $this->db->select('id, loan_type')->from('coop_loan_type')->where("id = $loan_type")->get()->row_array();
            $loan_type_name = $loan_type_data['loan_type'];
        }else{
            $loan_type_name = 'เงินกู้ทั้งหมด';
        }
        $arr_data['loan_type_name'] = $loan_type_name;
        $this->db->select(array('t1.id as loan_id', 't1.contract_number' ,'t2.member_id',
            "CONCAT(IF(t5.prename_short is null, '', t5.prename_short), t2.firstname_th, ' ', t2.lastname_th) as full_name",
            "t3.id as mem_group_id", "t3.mem_group_name", "t1.pay_type","t1.period_amount",  "t1.money_per_period", "t1.loan_amount",
            "t1.deduct_receipt_id",
            "GROUP_CONCAT(t6.prefix_code SEPARATOR '&,') as prefix_code",
            "GROUP_CONCAT((t4.pay_amount - t4.interest_amount) SEPARATOR '&,') as pay_amount",
            "GROUP_CONCAT(t4.interest_amount SEPARATOR '&,') as interest_amount",
            "GROUP_CONCAT(t12.money_per_period SEPARATOR '&,') as deduct_money_per_period",
            "GROUP_CONCAT(t13.loan_type_id SEPARATOR '&,') as deduct_loan_type_id",
            "t1.transfer_bank_account_id",
            "t1.createdatetime",
            "t14.total_paid_per_month"
        ));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id', 'inner');
        $this->db->join('coop_mem_group as t3','t3.id = t2.level', 'left');
        $this->db->join('coop_loan_prev_deduct as t4','t4.loan_id = t1.id', 'left');
        $this->db->join('coop_prename as t5','t5.prename_id = t2.prename_id', 'left');
        $this->db->join('coop_loan as t12','t12.id = t4.ref_id', 'LEFT');
        $this->db->join('(SELECT * FROM coop_term_of_loan GROUP BY type_id) as t6','t6.type_id = t12.loan_type', 'left');
        $this->db->join('coop_loan_name as t11','t11.loan_name_id = t1.loan_type', 'LEFT');
        $this->db->join('coop_loan_name as t13','t13.loan_name_id = t12.loan_type', 'LEFT');
        $this->db->join("(SELECT loan_id,`principal_payment`, `total_paid_per_month` FROM `coop_loan_period` WHERE  date_count = '31' GROUP BY loan_id) as t14",'t14.loan_id = t1.id', 'LEFT');
        $this->db->where($where);
        $this->db->group_by('t1.id');
        $this->db->order_by('t1.createdatetime');
        $data = $this->db->get()->result_array();

//        echo $this->db->last_query();exit;
//        echo '<pre>'; print_r($data);exit;
        $arr_data['datas'] = $data;
        $this->load->view('report_loan_pay_normal/report_loan_pay_normal_excel',$arr_data);

    }

    public function  check_loan_document(){

        $loan_type = $_POST['loan_type'];

        $date_start = $_POST['date_start'];
        $date_end = $_POST['date_end'];
        $date_start_arr = explode("/", $date_start);
        $date_end_arr = explode("/", $date_end);
        $date_start = ($date_start_arr[2]-543).'-'.$date_start_arr[1].'-'.$date_start_arr[0].' 00:00:00';
        $date_end = ($date_end_arr[2]-543).'-'.$date_end_arr[1].'-'.$date_end_arr[0].' 23:59:59';

        $where = '';
        if($_POST['loan_type'] != '') {
            $where .= "AND t2.loan_type_id = '$loan_type'";
        }
        $this->db->select(array('count(*) as count'));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_loan_name as t2','t2.loan_name_id = t1.loan_type', 'left');
        $this->db->where("t1.createdatetime BETWEEN '$date_start' AND '$date_end' AND t1.transfer_status IS NOT NULL $where");
        $data = $this->db->get()->row_array();

        if ($data['count'] > 0){
            echo 'success';
        }

    }

}

