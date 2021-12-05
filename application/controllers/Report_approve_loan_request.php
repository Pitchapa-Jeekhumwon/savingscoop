<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_approve_loan_request extends CI_Controller {
	function __construct()
	{
        parent::__construct();
    }

    public function index(){
        $arr_data = array();
        $arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

        $arr_data['year_arr'] = $this->db->select("(YEAR(approve_date)) as YEAR")
            ->from('coop_loan')
            ->where("approve_date is not null AND approve_date != '0'")
            ->group_by('YEAR(approve_date)')
            ->order_by('YEAR(approve_date) DESC')
            ->get()->result_array();

        $row = $this->db->select('loan_name, loan_name_id')
            ->from('coop_loan_name')
            ->where("loan_name_status = '1'")
            ->get()->result_array();
        $arr_data['loan_name'] = $row;

        $this->libraries->template('report_approve_loan_request/index',$arr_data);
    }

  
    public function report_approve_loan_request_preview(){
        $arr_data = array();
        if($_GET['dev'] == 'dev') {
            echo '<pre>';
            print_r($_GET);
            print_r($row);
            exit;
        }

        $approve_year = $_GET['approve_year'];
        $approve_month = $_GET['approve_month'];
        $where = "MONTH(t1.approve_date) = '".$approve_month."' AND YEAR(t1.approve_date) = '".$approve_year."'";

        if(empty($_GET['loan_name_all'])){
            $row = $this->db->select('loan_name, loan_name_id')
                ->from('coop_loan_name')
                ->where("loan_name_id IN  (".implode(',',array_filter($_GET['loan_name'])).")")
                ->get()->result_array();
            $arr_data['loan_name'] = $row;
            $where.= " AND loan_type IN  (".implode(',',array_filter($_GET['loan_name'])).")";
        }

        $this->db->select(array('t1.id as loan_id',
            "GROUP_CONCAT((t2.pay_amount - t2.interest_amount) SEPARATOR '&,') as pay_amount",
            "GROUP_CONCAT(t2.interest_amount SEPARATOR '&,') as interest_amount"))
            ->from('coop_loan as t1')
            ->join('coop_loan_prev_deduct as t2','t2.loan_id = t1.id', 'inner')
            ->where($where)
            ->group_by('t1.id')
            ->get();
        $outstanding = $this->db->last_query();

        $this->db->select(array('loan_id', 'sum(loan_deduct_amount) as loan_deduct_amount'))
            ->from('coop_loan_deduct')
            ->where("loan_deduct_list_code = 'deduct_insurance'")
            ->group_by('loan_id')
            ->get();
        $loan_deduct = $this->db->last_query();

        $row = $this->db->select(array('t1.loan_type', 't2.member_id', 't3.prename_short', 't2.firstname_th', 't2.lastname_th', 't1.loan_amount', 't4.pay_amount', 't4.interest_amount', 't5.loan_deduct_amount'))
            ->from('coop_loan as t1')
            ->join('coop_mem_apply as t2', 't1.member_id = t2.member_id', 'inner')
            ->join('coop_prename as t3', 't3.prename_id = t2.prename_id', 'left')
            ->join('('.$outstanding.') as t4','t1.id = t4.loan_id', 'left')
            ->join('('.$loan_deduct.') as t5','t1.id = t5.loan_id', 'left')
            ->where($where . " AND t1.loan_status not in ('0', '3', '5')")
            ->get()->result_array();

//        echo $this->db->last_query();

        $pase = 1;
        $limit_pase = 31;
        $last_pase = 0;
        $i = 0;
        $datas = array();
        foreach ($row as $key => $value){
            if($pase == 1){
                $check_limit_pase = $limit_pase-3;
            }else{
                $check_limit_pase = $limit_pase;
            }
            $i++;
            $datas[$pase][$key] = $value;
            $last_pase = $pase;
            if($i == $check_limit_pase){
                $i=0;
                $pase++;
            }

        }
        $arr_data['datas'] = $datas;
        $arr_data['last_pase'] = $last_pase;

//        exit;
        if($_GET['dev'] == 'dev'){
            echo '<pre>';print_r($arr_data);exit;
        }
        $this->load->view('report_approve_loan_request/report_approve_loan_request_preview',$arr_data);
    }
    
    public function  check_loan_document(){
        $approve_year = $_POST['approve_year'];
        $approve_month = $_POST['approve_month'];

        $where = "MONTH(t1.approve_date) = '".$approve_month."' AND YEAR(t1.approve_date) = '".$approve_year."' AND t1.loan_status not in ('0', '3', '5')";
        if(empty($_POST['loan_name_all'])){
            $where.= " AND loan_type IN  (".implode(',',array_filter($_POST['loan_name'])).")";
        }
	    $count_data = $this->db->select('count(t1.id) as count')
            ->from("coop_loan as t1")
            ->where($where)
            ->get()->row_array();
	    if($count_data['count'] > 0){
            echo 'success';
        }
    }

}

