<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_process_return_data extends CI_Controller
{
    public $month_arr = array('01'=>'มกราคม','02'=>'กุมภาพันธ์','03'=>'มีนาคม','04'=>'เมษายน','05'=>'พฤษภาคม','06'=>'มิถุนายน','07'=>'กรกฎาคม','08'=>'สิงหาคม','09'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
    public $month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
    function __construct()
    {
        parent::__construct();
        $this->month_arr = array('01'=>'มกราคม','02'=>'กุมภาพันธ์','03'=>'มีนาคม','04'=>'เมษายน','05'=>'พฤษภาคม','06'=>'มิถุนายน','07'=>'กรกฎาคม','08'=>'สิงหาคม','09'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
    }
    public function coop_report_return_pay_type(){
        $arr_data = array();

        $this->db->select(array('id','mem_group_name'));
        $this->db->from('coop_mem_group');
        $this->db->where("mem_group_type = '1'");
        $row = $this->db->get()->result_array();
        $arr_data['row_mem_group'] = $row;

        $this->db->select('mem_type_id, mem_type_name');
        $this->db->from('coop_mem_type');
        $row = $this->db->get()->result_array();
        $arr_data['mem_type'] = $row;

        $this->libraries->template('report_process_return_data/coop_report_return_pay_type',$arr_data);
    }
    function coop_report_return_pay_type_preview() {

        set_time_limit ( 180 );
        $title_date = "";
        if (!empty($_GET['start_date'])) {
            $start_date_arr = explode("/", $_GET['start_date']);
            $start_date = ($start_date_arr[2] - 543)."-".$start_date_arr[1]."-".$start_date_arr[0]." 00:00:00";
            //$receipt_con .= " AND t9.receipt_datetime >= '{$start_date}'";
            $title_date = "วันที่ ".$start_date_arr[0]." เดือน ".$this->month_arr[$start_date_arr[1]]." ปี ".(@$start_date_arr[2])." ถึง ";
        }
        if (!empty($_GET['end_date'])) {
            $end_date_arr = explode("/", $_GET['end_date']);
            $end_date = ($end_date_arr[2] - 543)."-".$end_date_arr[1]."-".$end_date_arr[0]." 23:59:59";
            //$receipt_con .= " AND t9.receipt_datetime <= '{$end_date}'";
            $title_date .=  " วันที่ ".$end_date_arr[0]." เดือน ".$this->month_arr[$end_date_arr[1]]." ปี ".(@$end_date_arr[2]);
        }

        $where ="t1.return_time  BETWEEN '".$start_date."' AND '".$end_date."'";
        if(@$_GET['level'] != ''&& @$_GET['level'] != 0) {
            $where .= " AND t2.level = '".$_GET['level']."'";
        }
        if(@$_GET['faction'] != '' && @$_GET['faction'] != 0 ) {
            $where .= " AND t2.faction = '".$_GET['faction']."'";
        }
        if(@$_GET['department'] != '') {
            $where .= " AND t2.department = '".$_GET['department']."'";
        }

        if(@empty($_GET['pay_type'])) {
            $where .= "";
        }else{
            $where .= " AND t1.pay_type = '".$_GET['pay_type']."'";
        }

//echo print_r($_GET);exit;
        $arr_data = array();
        $this->db->select(array('SUM(if(t1.receipt_id=t1.receipt_id,t1.return_principal, 0)) as principal',
            'SUM(if(t1.receipt_id=t1.receipt_id,t1.return_interest, 0)) as interest',
            'SUM(if(t1.receipt_id=t1.receipt_id,t1.return_amount, 0)) as total_amount','t2.level',
            't2.faction',
            't2.department',
            't2.firstname_th',
            't2.lastname_th',
            't2.prename_id ',
            't10.prename_full',
            't3.id ',
            't3.petition_number',
            't1.*'));
        $this->db->from('coop_process_return as t1 ');
        $this->db->join('coop_mem_apply as t2','t2.member_id = t1.member_id','left');
        $this->db->join("coop_prename as t10", "t2.prename_id= t10.prename_id", "left");
        $this->db->join("coop_loan as t3", "t1.loan_id= t3.id", "left");
        $this->db->where("$where");
        $this->db->group_by("t1.receipt_id,t1.member_id");
        $this->db->order_by("t1.bill_id ASC");
        $data = $this->db->get()->result_array();
        //echo $this->db->last_query();exit;

        foreach ($data as $key=>$value){
            if(empty($value['petition_number'])){
                $loan_id = $this->db->select("petition_number,loan_atm_id")
                    ->from("coop_loan_atm as t1")
                    ->where("t1.loan_atm_id = '".$value['id']."'")
                    ->get()->row();
                $data[$key]['petition_number']= $loan_id['petition_number'];
            }

           if($value['pay_type']=="0"){
               $data[$key]['pay_type_name']= "เงินสด";
           }else if($value['pay_type']=="1"){
               $data[$key]['pay_type_name']= "เงินโอน";
           }
           else if($value['pay_type']=="2"){
               $data[$key]['pay_type_name']= "ชำระเงินกู้อื่น";
           }
           else if($value['pay_type']=="3"){
               $data[$key]['pay_type_name']= "เก็บเงินไม่ได้";
           }
           else if(empty($value['pay_type'])){
               $data[$key]['pay_type_name']= "ไม่ระบุ";
           }
        }
        $num_rows = count($data);
        $page_num = 1;
        $all_page = ceil($num_rows/100);

        $page_get = !empty($_GET['page']) ? $_GET['page'] : 1;
        $paging = $this->pagination_center->paginating(intval($page_get), $all_page, 1, 20,@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
        foreach($data as $index => $datas) {
            if ($index == 35 || (($index - 35)%42) == 0) {
                $page_num++;
            }
            $row['data'][$page_num][] = $datas;
        }
        $num_rows = count($data);
        $arr_data["title_date"] = $title_date;
        $arr_data['num_rows'] = $num_rows;
        $arr_data['data'] = $row['data'];
        $arr_data['page_all'] = $page_num;
        //echo "<pre>";print_r($arr_data['data']);exit;
        $this->preview_libraries->template_preview('report_process_return_data/coop_report_return_pay_type_preview', $arr_data);
    }
    function coop_report_return_pay_type_excel() {

        set_time_limit ( 180 );
        $title_date = "";
        if (!empty($_GET['start_date'])) {
            $start_date_arr = explode("/", $_GET['start_date']);
            $start_date = ($start_date_arr[2] - 543)."-".$start_date_arr[1]."-".$start_date_arr[0]." 00:00:00";
            //$receipt_con .= " AND t9.receipt_datetime >= '{$start_date}'";
            $title_date = "วันที่ ".$start_date_arr[0]." เดือน ".$this->month_arr[$start_date_arr[1]]." ปี ".(@$start_date_arr[2])." ถึง ";
        }
        if (!empty($_GET['end_date'])) {
            $end_date_arr = explode("/", $_GET['end_date']);
            $end_date = ($end_date_arr[2] - 543)."-".$end_date_arr[1]."-".$end_date_arr[0]." 23:59:59";
            //$receipt_con .= " AND t9.receipt_datetime <= '{$end_date}'";
            $title_date .=  " วันที่ ".$end_date_arr[0]." เดือน ".$this->month_arr[$end_date_arr[1]]." ปี ".(@$end_date_arr[2]);
        }

        $where ="t1.return_time  BETWEEN '".$start_date."' AND '".$end_date."'";
        if(@$_GET['level'] != ''&& @$_GET['level'] != 0) {
            $where .= " AND t2.level = '".$_GET['level']."'";
        }
        if(@$_GET['faction'] != '' && @$_GET['faction'] != 0 ) {
            $where .= " AND t2.faction = '".$_GET['faction']."'";
        }
        if(@$_GET['department'] != '') {
            $where .= " AND t2.department = '".$_GET['department']."'";
        }

        if(@empty($_GET['pay_type'])) {
            $where .= "";
        }else{
            $where .= " AND t1.pay_type = '".$_GET['pay_type']."'";
        }

//echo print_r($_GET);exit;
        $arr_data = array();
        $this->db->select(array('SUM(if(t1.receipt_id=t1.receipt_id,t1.return_principal, 0)) as principal',
            'SUM(if(t1.receipt_id=t1.receipt_id,t1.return_interest, 0)) as interest',
            'SUM(if(t1.receipt_id=t1.receipt_id,t1.return_amount, 0)) as total_amount','t2.level',
            't2.faction',
            't2.department',
            't2.firstname_th',
            't2.lastname_th',
            't2.prename_id ',
            't10.prename_full',
            't3.id ',
            't3.petition_number',
            't1.*'));
        $this->db->from('coop_process_return as t1 ');
        $this->db->join('coop_mem_apply as t2','t2.member_id = t1.member_id','left');
        $this->db->join("coop_prename as t10", "t2.prename_id= t10.prename_id", "left");
        $this->db->join("coop_loan as t3", "t1.loan_id= t3.id", "left");
        $this->db->where("$where");
        $this->db->group_by("t1.receipt_id,t1.member_id");
        $this->db->order_by("t1.bill_id ASC");
        $data = $this->db->get()->result_array();
        //echo $this->db->last_query();exit;

        foreach ($data as $key=>$value){
            if(empty($value['petition_number'])){
                $loan_id = $this->db->select("petition_number,loan_atm_id")
                    ->from("coop_loan_atm as t1")
                    ->where("t1.loan_atm_id = '".$value['id']."'")
                    ->get()->row();
                $data[$key]['petition_number']= $loan_id['petition_number'];
            }

            if($value['pay_type']=="0"){
                $data[$key]['pay_type_name']= "เงินสด";
            }else if($value['pay_type']=="1"){
                $data[$key]['pay_type_name']= "เงินโอน";
            }
            else if($value['pay_type']=="2"){
                $data[$key]['pay_type_name']= "ชำระเงินกู้อื่น";
            }
            else if($value['pay_type']=="3"){
                $data[$key]['pay_type_name']= "เก็บเงินไม่ได้";
            }
            else if(empty($value['pay_type'])){
                $data[$key]['pay_type_name']= "ไม่ระบุ";
            }
        }
        $arr_data["title_date"] = $title_date;
        $arr_data['data'] = $data;

        //echo "<pre>";print_r($arr_data['data']);exit;
        $this->preview_libraries->template_preview('report_process_return_data/coop_report_return_pay_type_excel', $arr_data);
    }

    function check_coop_report_return_pay_type() {
        $title_date = "";
        if (!empty($_GET['start_date'])) {
            $start_date_arr = explode("/", $_GET['start_date']);
            $start_date = ($start_date_arr[2] - 543)."-".$start_date_arr[1]."-".$start_date_arr[0]." 00:00:00";
            //$receipt_con .= " AND t9.receipt_datetime >= '{$start_date}'";
            $title_date = "วันที่ ".$start_date_arr[0]." เดือน ".$this->month_arr[$start_date_arr[1]]." ปี ".(@$start_date_arr[2])." ถึง ";
        }
        if (!empty($_GET['end_date'])) {
            $end_date_arr = explode("/", $_GET['end_date']);
            $end_date = ($end_date_arr[2] - 543)."-".$end_date_arr[1]."-".$end_date_arr[0]." 23:59:59";
            //$receipt_con .= " AND t9.receipt_datetime <= '{$end_date}'";
            $title_date .=  " วันที่ ".$end_date_arr[0]." เดือน ".$this->month_arr[$end_date_arr[1]]." ปี ".(@$end_date_arr[2]);
        }

        $where ="t1.return_time  BETWEEN '".$start_date."' AND '".$end_date."'";
        if(@$_GET['level'] != ''&& @$_GET['level'] != 0) {
            $where .= " AND t2.level = '".$_GET['level']."'";
        }
        if(@$_GET['faction'] != '' && @$_GET['faction'] != 0 ) {
            $where .= " AND t2.faction = '".$_GET['faction']."'";
        }
        if(@$_GET['department'] != '') {
            $where .= " AND t2.department = '".$_GET['department']."'";
        }

        if(@empty($_GET['pay_type'])) {
            $where .= "";
        }else{
            $where .= " AND t1.pay_type = '".$_GET['pay_type']."'";
        }


        $this->db->select(array('t2.level',
            't2.faction',
            't2.department',
            't2.firstname_th',
            't2.lastname_th',
            't2.prename_id ',
            't10.prename_full',
            't3.id ',
            't3.petition_number',
            't1.*'));
        $this->db->from('coop_process_return as t1 ');
        $this->db->join('coop_mem_apply as t2','t2.member_id = t1.member_id','left');
        $this->db->join("coop_prename as t10", "t2.prename_id= t10.prename_id", "left");
        $this->db->join("coop_loan as t3", "t1.loan_id= t3.id", "left");
        $this->db->where("$where");
        $this->db->order_by("t1.pay_type ASC");
        $datas = $this->db->get()->result_array();
        $hasData = false;

            if(!empty($datas)) {
                $hasData = true;
            }

        if($hasData){
            echo "success";
        }else{
            echo "";
        }
    }
}