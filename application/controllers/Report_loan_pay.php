<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_loan_pay extends CI_Controller {
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

        $this->db->select('id, mem_group_name, mem_group_full_name');
        $this->db->from('coop_mem_group');
        $this->db->where('mem_group_parent_id = 2');
        $coop_mem_group = $this->db->get()->result_array();

//        echo '<pre>';print_r($coop_mem_group);exit;

        $arr_data['loan_type'] = $loan_type;
        $arr_data['coop_mem_group'] = $coop_mem_group;
        $this->libraries->template('report_loan_pay/index',$arr_data);
    }

    public function report_loan_pay_excel(){
        $arr_data = array();
        $loan_type = $_GET['loan_type'];
        $date_start = $_GET['date_start'];
        $date_end = $_GET['date_end'];
        $date_start_arr = explode("/", $date_start);
        $date_end_arr = explode("/", $date_end);
        $date_start = ($date_start_arr[2]-543).'-'.$date_start_arr[1].'-'.$date_start_arr[0].' 00:00:00';
        $date_end = ($date_end_arr[2]-543).'-'.$date_end_arr[1].'-'.$date_end_arr[0].' 23:59:59';
        $where = "t1.createdatetime BETWEEN '$date_start' AND '$date_end' AND t1.transfer_status IS NOT NULL";

        $this->db->select('id, mem_group_name, mem_group_full_name');
        $this->db->from('coop_mem_group');
        $this->db->where('mem_group_parent_id = 2');
        $coop_mem_group_arr = $this->db->get()->result_array();
        $coop_mem_group = array();
        foreach ($coop_mem_group_arr as $key => $value){
            $coop_mem_group[$value['id']] = $value['mem_group_full_name'];
        }

        $arr_data['coop_mem_group'] = $coop_mem_group;


        $this->db->select(array('t1.id as loan_id', "GROUP_CONCAT(t2.ref_id SEPARATOR '&,') as ref_id","GROUP_CONCAT(t4.prefix_code SEPARATOR '&,') as prefix_code",
            "GROUP_CONCAT((t2.pay_amount - t2.interest_amount) SEPARATOR '&,') as pay_amount", "GROUP_CONCAT(t2.interest_amount SEPARATOR '&,') as interest_amount",
            't1.createdatetime'));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_loan_prev_deduct as t2','t2.loan_id = t1.id', 'inner');
        $this->db->join('coop_loan as t3','t3.id = t2.ref_id', 'left');
        $this->db->join('(SELECT * FROM coop_term_of_loan GROUP BY type_id) as t4','t4.type_id = t3.loan_type', 'left');
        $this->db->where($where);
        $this->db->group_by('t1.id');
        $this->db->get();
        $outstanding = $this->db->last_query();

        $this->db->select(array('t1.id as loan_id',
            "GROUP_CONCAT(t2.guarantee_person_id SEPARATOR '&,') as guarantee_person_id",
            "GROUP_CONCAT(CONCAT(t3.member_id, ' : ', IF(t4.prename_short is null, '', t4.prename_short), t3.firstname_th, ' ', t3.lastname_th) SEPARATOR '&,') as guarantee_full_name",
            't1.createdatetime'));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_loan_guarantee_person as t2','t2.loan_id = t1.id', 'inner');
        $this->db->join('coop_mem_apply as t3','t3.member_id = t2.guarantee_person_id', 'left');
        $this->db->join('coop_prename as t4','t4.prename_id = t3.prename_id', 'left');
        $this->db->where($where);
        $this->db->group_by('t1.id');
        $this->db->get();
        $guarantee_person = $this->db->last_query();


        if($_GET['loan_type'] != ''){
            $where .= "AND t11.loan_type_id = '$loan_type'";
            $loan_type_data = $this->db->select('id, loan_type')->from('coop_loan_type')->where("id = $loan_type")->get()->row_array();
            $loan_type_name = $loan_type_data['loan_type'];
        }else{
            $loan_type_name = 'ทั้งหมด';
        }

        $where.="AND t3.id IN (".implode(',',$_GET['mem_group']).")";
        $arr_data['loan_type_name'] = $loan_type_name;
        $this->db->select(array('t1.id as loan_id', 't1.contract_number' ,'t2.member_id',
            "CONCAT(IF(t5.prename_short is null, '', t5.prename_short), t2.firstname_th, ' ', t2.lastname_th) as full_name",
            "t1.createdatetime", "t1.pay_type", "t1.money_per_period", "t1.period_amount", "t1.loan_amount",

            "t6.user_name",
            "t15.prefix_code",
            "t15.pay_amount",
            "t15.interest_amount",

            "t16.guarantee_person_id",
            "t16.guarantee_full_name",
            't11.loan_type_id',
            "t3.id as mem_group_id", "t3.mem_group_name",
            "t17.total_paid_per_month"
        ));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id', 'inner');
        $this->db->join('coop_mem_group as t3','t3.id = t2.level', 'left');
        $this->db->join('coop_prename as t5','t5.prename_id = t2.prename_id', 'left');
        $this->db->join('coop_user as t6','t1.admin_id = t6.user_name', 'left');


        $this->db->join('coop_loan_name as t11','t11.loan_name_id = t1.loan_type', 'LEFT');
        $this->db->join('('.$outstanding.') as t15','t15.loan_id = t1.id', 'left');
        $this->db->join('('.$guarantee_person.') as t16','t16.loan_id = t1.id', 'left');
        $this->db->join("(SELECT loan_id,`principal_payment`, `total_paid_per_month` FROM `coop_loan_period` WHERE  date_count = '31' GROUP BY loan_id) as t17",'t17.loan_id = t1.id', 'LEFT');
        $this->db->where($where);
        $this->db->group_by('t1.id');
        $this->db->order_by('t1.createdatetime');
        $data = $this->db->get()->result_array();

        $datas = array();
        foreach ($_GET['mem_group'] as $key => $value){
            $datas[$value] = array();
        }

        foreach ($data as $key => $value) {
            $datas[$value['mem_group_id']][] = $data[$key];
        }

//        print_r($_SESSION);exit;

//        echo '<pre>';print_r($datas);exit;
//        echo '<pre>';print_r($datas);exit;
        $arr_data['datas'] = $datas;
        $this->load->view('report_loan_pay/report_loan_pay_excel',$arr_data);

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

