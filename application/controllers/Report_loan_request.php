<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_loan_request extends CI_Controller {
	function __construct()
	{
        parent::__construct();
    }

    public function index(){
        $arr_data = array();

        $this->db->select('*');
        $this->db->from('setting_loan_page');
        $default_loan = $this->db->get()->row_array();

        $arr_data['default_loan'] = $default_loan['loan_type_id'];
        $arr_data['default_loan_name'] = $default_loan['loan_name_id'];
        $arr_data['loan_type'] = $this->contract->getLoanType();
        $arr_data['rs_loan_name'] = $this->contract->getLoanNameByTypeId($arr_data['default_loan']);

        $this->libraries->template('report_loan_request/index',$arr_data);
    }

    function change_loan_type(){
        $row = $this->contract->getLoanNameByTypeId($_POST['type_id']);
        $text_return = "<option value=''>เลือกทั้งหมด</option>";
        foreach($row as $key => $value){
            $text_return .= "<option value='".$value['loan_name_id']."'>".$value['loan_name']." ".$value['loan_name_description']."</option>";
        }
        echo $text_return;
        exit;
    }
    public function report_loan_request_excel(){
        $arr_data = array();
        $loan_type = $_GET['loan_type'];
        $loan_name = $_GET['loan_name'];
        $date_start = $_GET['date_start'];
        $date_end = $_GET['date_end'];
        $date_start_arr = explode("/", $date_start);
        $date_end_arr = explode("/", $date_end);
        $date_start = ($date_start_arr[2]-543).'-'.$date_start_arr[1].'-'.$date_start_arr[0].' 00:00:00';
        $date_end = ($date_end_arr[2]-543).'-'.$date_end_arr[1].'-'.$date_end_arr[0].' 23:59:59';
        $where = "t1.createdatetime BETWEEN '$date_start' AND '$date_end'";
        $where1 = "t3.loan_name_id = '".$loan_name."' ";
       //echo  $loan_name_id."-". $loan_type;exit;
        $this->db->select(array('member_id', 'share_collect_value'));
        $this->db->from('( SELECT member_id, MAX(share_date) as max_share_date FROM coop_mem_share GROUP BY member_id ) as a ');
        $this->db->join('(SELECT  member_id as id, share_collect_value ,share_date FROM coop_mem_share)  as b','a.member_id = b.id AND a.max_share_date = b.share_date', 'inner');
        $this->db->where('b.share_collect_value > 0');
        $this->db->get();
        $pay_old_debts = $this->db->last_query();


        $this->db->select(array('t1.id as loan_id', "GROUP_CONCAT(t2.ref_id SEPARATOR '&,') as ref_id","GROUP_CONCAT(t4.prefix_code SEPARATOR '&,') as prefix_code",
            "GROUP_CONCAT((t2.pay_amount - t2.interest_amount) SEPARATOR '&,') as pay_amount", "GROUP_CONCAT(t2.interest_amount SEPARATOR '&,') as interest_amount",
            't1.createdatetime'));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_loan_prev_deduct as t2','t2.loan_id = t1.id', 'inner');
        $this->db->join('coop_loan as t3',"t3.id = t2.ref_id", 'left');
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
        if($_GET['loan_name'] != ''){
            $where .= "AND t11.loan_name_id = '$loan_name'";
        }

        $arr_data['loan_type_name'] = $loan_type_name;

        $this->db->select(array('t1.createdatetime', 't1.id as loan_id','t2.member_id',
            "CONCAT(IF(t5.prename_short is null, '', t5.prename_short), t2.firstname_th, ' ', t2.lastname_th) as full_name",  "(YEAR(NOW()) - YEAR(t2.birthday)) as age",
            "t2.salary", "t2.share_month", "t3.share_collect_value","t1.loan_amount","t1.period_amount","t1.money_per_period",
            "t15.ref_id",
            "t15.prefix_code",
            "t15.pay_amount",
            "t15.interest_amount",
            't1.contract_number',
            "t16.guarantee_person_id",
            "t16.guarantee_full_name",
            't1.pay_type','t10.loan_reason','t11.loan_type_id','t11.loan_name_id',
            "t17.total_paid_per_month"
        ));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id', 'inner');
        $this->db->join('('.$pay_old_debts.') as t3','t2.member_id = t3.member_id', 'left');
        $this->db->join('coop_prename as t5','t5.prename_id = t2.prename_id', 'left');
        $this->db->join('coop_loan_reason as t10','t10.loan_reason_id = t1.loan_reason', 'left');
        $this->db->join('coop_loan_name as t11','t11.loan_name_id = t1.loan_type', 'LEFT');
        $this->db->join('('.$outstanding.') as t15','t15.loan_id = t1.id', 'left');
        $this->db->join('('.$guarantee_person.') as t16','t16.loan_id = t1.id', 'left');
        $this->db->join("(SELECT loan_id,`principal_payment`, `total_paid_per_month` FROM `coop_loan_period` WHERE  date_count = '31' GROUP BY loan_id) as t17",'t17.loan_id = t1.id', 'LEFT');
        //$this->db->join('('.$deduct_insurance.') as m5','m5.loan_id = t1.id', 'left');
        $this->db->where($where);//t2.member_id = '000025' AND
        $this->db->group_by('t1.id');
        $this->db->order_by('t1.createdatetime');
        $data = $this->db->get()->result_array();


        $this->db->select(array('loan_id','loan_deduct_list_code','loan_deduct_amount'));
        $this->db->from("coop_loan_deduct");
        $this->db->where("loan_deduct_list_code = 'deduct_insurance'");
        $deduct_insurance= $this->db->get()->result_array();

        foreach($data as $key=>$val){
            foreach($deduct_insurance as $k => $v){
                if($data[$key]['loan_id']==$deduct_insurance[$k]['loan_id']) {
                    $data[$key]['deduct'] = $deduct_insurance[$k]['loan_deduct_amount'];
                }
            }
        }

        $arr_data['datas'] = $data;
        //echo "<pre>";print_r($arr_data['datas']);exit;
        $this->load->view('report_loan_request/report_loan_request_excel',$arr_data);
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

