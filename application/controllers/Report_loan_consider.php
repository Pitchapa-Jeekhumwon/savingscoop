<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_loan_consider extends CI_Controller {
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
        $this->libraries->template('report_loan_consider/index',$arr_data);
    }

    public function report_loan_document_consider_excel(){
        $arr_data = array();
        $month_arr = array('01'=>'มกราคม','02'=>'กุมภาพันธ์','03'=>'มีนาคม','04'=>'เมษายน','05'=>'พฤษภาคม','06'=>'มิถุนายน','07'=>'กรกฎาคม','08'=>'สิงหาคม','09'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

        $loan_type = $_GET['loan_type'];
        $date_start = $_GET['date_start'];
        $date_end = $_GET['date_end'];
        $date_start_arr = explode("/", $date_start);
        $date_end_arr = explode("/", $date_end);
        $date_start = ($date_start_arr[2]-543).'-'.$date_start_arr[1].'-'.$date_start_arr[0].' 00:00:00';
        $date_end = ($date_end_arr[2]-543).'-'.$date_end_arr[1].'-'.$date_end_arr[0].' 23:59:59';
        $where = "t1.createdatetime BETWEEN '$date_start' AND '$date_end' AND t1.transfer_status IS NOT NULL";

        $this->db->select(array('member_id', 'share_collect_value'));
        $this->db->from('( SELECT member_id, MAX(share_date) as max_share_date FROM coop_mem_share GROUP BY member_id ) as a ');
        $this->db->join('(SELECT  member_id as id, share_collect_value ,share_date FROM coop_mem_share)  as b','a.member_id = b.id AND a.max_share_date = b.share_date', 'inner');
        $this->db->where('b.share_collect_value > 0');
        $this->db->get();
        $pay_old_debts = $this->db->last_query();

        $this->db->select(array('t1.id as loan_id', "GROUP_CONCAT(t2.ref_id SEPARATOR '&,') as ref_id","GROUP_CONCAT(t4.prefix_code SEPARATOR '&,') as prefix_code",
            "GROUP_CONCAT(t3.loan_amount SEPARATOR '&,') as overdue_loan_amount",
            "GROUP_CONCAT((t2.pay_amount - t2.interest_amount) SEPARATOR '&,') as pay_amount", "GROUP_CONCAT(t2.interest_amount SEPARATOR '&,') as interest_amount",
            "GROUP_CONCAT(t5.loan_type_id SEPARATOR '&,') as loan_type_id", 't1.createdatetime'));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_loan_prev_deduct as t2','t2.loan_id = t1.id', 'inner');
        $this->db->join('coop_loan as t3','t3.id = t2.ref_id', 'left');
        $this->db->join('(SELECT * FROM coop_term_of_loan GROUP BY type_id) as t4','t4.type_id = t3.loan_type', 'left');
        $this->db->join('coop_loan_name as t5','t5.loan_name_id = t3.loan_type', 'left');
        $this->db->where($where);
        $this->db->group_by('t1.id');
        $this->db->get();
        $outstanding = $this->db->last_query();
//        echo $outstanding;exit;

        $this->db->select(array('t1.id as loan_id',
            "GROUP_CONCAT(t2.guarantee_person_id SEPARATOR '&,') as guarantee_person_id",
            "GROUP_CONCAT(CONCAT(IF(t4.prename_short is null, '', t4.prename_short), t3.firstname_th) SEPARATOR '&,') as guarantee_first_name",
            "GROUP_CONCAT(t3.lastname_th SEPARATOR '&,') as guarantee_last_name",
            "GROUP_CONCAT((YEAR(NOW()) - YEAR(t3.birthday)) SEPARATOR '&,') as guarantee_age",
            "GROUP_CONCAT(t3.salary SEPARATOR '&,') as guarantee_salary",
            "GROUP_CONCAT(t3.tel SEPARATOR '&,') as guarantee_tel",
            "GROUP_CONCAT(t3.apply_date SEPARATOR '&,') as guarantee_apply_date",
            't1.createdatetime'));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_loan_guarantee_person as t2','t2.loan_id = t1.id', 'inner');
        $this->db->join('coop_mem_apply as t3','t3.member_id = t2.guarantee_person_id', 'left');
        $this->db->join('coop_prename as t4','t4.prename_id = t3.prename_id', 'left');
        $this->db->where($where);
        $this->db->group_by('t1.id');
        $this->db->get();
        $guarantee_person = $this->db->last_query();
//        echo $guarantee_person;exit;

        if($_GET['loan_type'] != ''){
            $where .= "AND t11.loan_type_id = '$loan_type'";
            $loan_type_data = $this->db->select('id, loan_type')->from('coop_loan_type')->where("id = $loan_type")->get()->row_array();
            $loan_type_name = $loan_type_data['loan_type'];
        }else{
            $loan_type_name = 'ทั้งหมด';
        }

        $arr_data['loan_type_name'] = $loan_type_name;

        $this->db->select(array('t1.id as loan_id',
            "CONCAT(IF(t5.prename_short is null, '', t5.prename_short), t2.firstname_th) as firstname",
            "lastname_th as lastname",
            't1.period_amount','(YEAR(NOW()) - YEAR(t2.birthday)) as age, t2.birthday','t2.tel','t2.member_id','t2.apply_date','t13.short_mem_group_name','t2.salary',
            "t2.salary * 40 as 'วงเงิน'",'t1.loan_amount','t6.loan_reason','t1.money_per_period','t14.principal_payment','t14.interest','t3.share_collect_value','t1.loan_status',
            't15.ref_id','t15.prefix_code','t15.overdue_loan_amount','t15.pay_amount','t15.interest_amount','t15.loan_type_id','t16.guarantee_person_id',
            't16.guarantee_first_name','t16.guarantee_last_name','t16.guarantee_age',
            't16.guarantee_tel','t16.guarantee_salary','t16.guarantee_apply_date'));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_mem_apply as t2','t2.member_id = t1.member_id', 'inner');
        $this->db->join('('.$pay_old_debts.') as t3','t2.member_id = t3.member_id', 'left');
        $this->db->join('coop_prename as t5','t5.prename_id = t2.prename_id', 'left');
        $this->db->join('coop_loan_reason as t6','t5.prename_id = t1.loan_reason', 'left');
        $this->db->join('coop_loan_name as t11','t11.loan_name_id = t1.loan_type', 'left');
        $this->db->join('coop_mem_group as t13','t13.id = t2.level', 'left');
        $this->db->join('coop_loan_period as t14','t14.loan_id = t1.id', 'left');
        $this->db->join('('.$outstanding.') as t15','t15.loan_id = t1.id', 'left');
        $this->db->join('('.$guarantee_person.') as t16','t16.loan_id = t1.id', 'left');
        $this->db->where($where);
        $this->db->group_by('t1.id');
        $this->db->order_by('t1.createdatetime');
        $data = $this->db->get()->result_array();

        $num_presun = 0;
        foreach ($data as $key => $value){
            $num_presun++;
            if(!empty($value['apply_date'])) {
                $data[$key]['member_year'] = $this->cal_member_year($value['apply_date']);
            }

            if(!empty($value['guarantee_person_id'])){
                $guarantee_person_id = explode("&,", $value['guarantee_person_id']);
                $guarantee_first_name = explode("&,", $value['guarantee_first_name']);
                $guarantee_last_name = explode("&,", $value['guarantee_last_name']);
                $guarantee_age = explode("&,", $value['guarantee_age']);
                $guarantee_tel = explode("&,", $value['guarantee_tel']);
                $guarantee_salary = explode("&,", $value['guarantee_salary']);
                $guarantee_apply_date = explode("&,", $value['guarantee_apply_date']);
                $guarantee_arr = array();
                foreach ($guarantee_person_id as $guarantee_key => $guarantee_value){
                    $array_push = array(    'guarantee_person_id' => $guarantee_person_id[$guarantee_key],
                                            'guarantee_first_name' => $guarantee_first_name[$guarantee_key],
                                            'guarantee_last_name' => $guarantee_last_name[$guarantee_key],
                                            'guarantee_age' => $guarantee_age[$guarantee_key],
                                            'guarantee_tel' => $guarantee_tel[$guarantee_key],
                                            'guarantee_salary' => $guarantee_salary[$guarantee_key],
                                            'guarantee_apply_date' => $guarantee_apply_date[$guarantee_key],
                                            'guarantee_member_year' => $this->cal_member_year($guarantee_apply_date[$guarantee_key]));
                    array_push($guarantee_arr, $array_push);

                }
                $data[$key]['guarantee_persun'] = $guarantee_arr;
            }
        }


        $this->db->select(array('finance_name','signature_1','receive_name','signature_2','manager_name','signature_3'));
        $this->db->from('coop_signature');
        $signature = $this->db->get()->row_array();
//        echo '<pre>';print_r($signature);exit;
//        echo '<pre>';print_r($data);exit;
//        echo $this->db->last_query();exit;
        $arr_data['datas'] = $data;
        $date_now = date('Y-m-d');
        $arr_data['date_now'] = $date_now;
        $arr_data['signature'] = $signature;

        $arr_data['month_arr'] = $month_arr;
        $arr_data['num_presun'] = $num_presun;


        $this->load->view('report_loan_consider/report_loan_document_consider_excel',$arr_data);

    }
    public function cal_member_year ($apply_date){
        $date_now = date('Y-m-d');
        $date_now_arr = explode('-', $date_now);
        $date_now_day = $date_now_arr['2'];
        $date_now_month = $date_now_arr['1'];
        $date_now_year = $date_now_arr['0'];

        $apply_date_arr = explode('-', $apply_date);
        $apply_day = $apply_date_arr['2'];
        $apply_month = $apply_date_arr['1'];
        $apply_year = $apply_date_arr['0'];

        $year = $date_now_year - $apply_year;
        $month = $date_now_month - $apply_month;
        $day = $date_now_day - $apply_day;

        if($day < 0){
            $month--;
        }
        if($month < 0){
            $month+=12;
            $year--;
        }
        $member_year = $year.'/'.$month;

        return $member_year;
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

