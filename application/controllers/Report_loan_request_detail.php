<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_loan_request_detail extends CI_Controller {
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
//            ->order_by('YEAR(approve_date) ASC')
            ->get()->result_array();

        $row = $this->db->select('loan_name, loan_name_id')
            ->from('coop_loan_name')
            ->where("loan_name_status = '1'")
            ->get()->result_array();
        $arr_data['loan_name'] = $row;

        $this->libraries->template('report_loan_request_detail/index',$arr_data);
    }

    public function report_loan_request_detail_preview(){
        $arr_data = array();
     
        $approve_year = $_GET['approve_year'];
        $approve_month = $_GET['approve_month'];
        $where = "MONTH(t1.createdatetime) = '".$approve_month."' AND YEAR(t1.createdatetime) = '".$approve_year."'";

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
        if(!empty($_GET['member_id'])){
            $member_id = $_GET['member_id'];
        }
        if(!empty($_GET['loan_id'])){
            $loan_id = $_GET['loan_id'];
        }

        $row = $this->db->select(array(
            't2.member_id',
            't3.prename_short',
            't2.firstname_th',
            't2.lastname_th',
            't2.share_month',
//            "t1.salary",
            "IF(t1.salary = '0' or t1.salary IS NULL, t2.salary, t1.salary) as salary",
            't1.id as loan_id',
            't1.loan_amount',
            't1.period_amount',
            't1.date_start_period',
            't1.pay_type',
            't1.interest_per_year',
            't4.pay_amount',
            't4.interest_amount',
            't5.loan_deduct_amount',
            't6.loan_reason',
            't1.createdatetime'))
            ->from('coop_loan as t1')
            ->join('coop_mem_apply as t2', 't1.member_id = t2.member_id', 'inner')
            ->join('coop_prename as t3', 't3.prename_id = t2.prename_id', 'left')
            ->join('('.$outstanding.') as t4','t1.id = t4.loan_id', 'left')
            ->join('('.$loan_deduct.') as t5','t1.id = t5.loan_id', 'left')
            ->join('coop_loan_reason as t6','t1.loan_reason = t6.loan_reason_id', 'left')
            ->where($where)
//            ->where("t1.member_id = '".$member_id."' AND t1.id = '".$loan_id."'")
            ->get()->result_array();
//        echo $this->db->last_query();

        foreach ($row as $key => $value){
            $member_id = $value['member_id'];
            $loan_id = $value['loan_id'];
            $createdatetime = $value['createdatetime'];
            $date_start_period = $value['date_start_period'];
            $pay_type = $value['pay_type'];
            $loan_amount = $value['loan_amount'];
            $interest_per_year = $value['interest_per_year'];
            $row[$key]['loan_order'] = $this->get_report_loan_order($member_id, $loan_id, $createdatetime);
            $row[$key]['guarantee_person'] = $this->get_report_guarantee_person($member_id, $loan_id, $createdatetime);
            $row[$key]['deduct'] = $this->get_report_deduct($loan_id);
            $row[$key]['keep_month'] = $this->get_keep_month($member_id, $loan_id, $createdatetime, $date_start_period);
            $get_total_paid_per_month = $this->get_total_paid_per_month($loan_id, $pay_type, $loan_amount, $interest_per_year, $createdatetime, $date_start_period);
            $row[$key]['total_paid_per_month'] = $get_total_paid_per_month['total_paid_per_month'];
            $row[$key]['interest_30_day'] = $get_total_paid_per_month['interest_30_day'];
            $this->db->select('outgoing_code, outgoing_name, loan_cost_amount');
            $this->db->from("coop_outgoing");
            $this->db->join("coop_loan_cost_mod", "coop_outgoing.outgoing_code=coop_loan_cost_mod.loan_cost_code", "inner");
            $this->db->where("loan_id = '".$loan_id."' AND member_id = '".$member_id."'");
            $rs_cost = $this->db->get()->result_array();
            $cost_all = array_sum(array_column($rs_cost, 'loan_cost_amount'));
            if(empty($value['salary'])){
                $value['salary'] = 0;
            }
            $salary_balance = $value['salary'] - $cost_all - $value['share_month'] - $row[$key]['keep_month'] - $row[$key]['total_paid_per_month'] - $row[$key]['interest_30_day'];
            $row[$key]['salary_balance'] = $salary_balance;

        }

//        echo '<pre>';print_r($row);
//        exit;


        $pase = 1;
        $limit_pase = 32;
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


        $arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

        if($_GET['dev'] == 'dev'){
            echo '<pre>';print_r($arr_data);exit;
        }
        if(!isset($_GET['excel'])){
            $this->preview_libraries->template_preview('report_loan_request_detail/report_loan_request_detail_preview',$arr_data);
        }else{
            $this->load->view('report_loan_request_detail/report_loan_request_detail_excel',$arr_data);
        }
    }
    public function get_total_paid_per_month($loan_id, $pay_type, $loan_amount, $interest_per_year, $createdatetime, $date_start_period){
        $arr_data = array();
        $this->db->select(array(
            'principal_payment',
            'total_paid_per_month'
        ));
        $this->db->from('coop_loan_period');
        $this->db->where("loan_id='".$loan_id."' AND date_count = '31'");
        $this->db->limit(1);
        $per_month = $this->db->get()->result_array();
        if($pay_type == '1'){
            $total_paid_per_month = @round(@$per_month[0]['principal_payment'],2);
            $pay_type_name = "แบบคงต้น";

            //ดอกเบี้ย 30 วัน ของจากยอดกู้เต็ม
            $date_count = date_diff(date_create($createdatetime),date_create($date_start_period))->format("%a");
            $interest_30_day = (((@$loan_amount*@$interest_per_year)/100)/365)*@$date_count;
            $interest_30_day = round(@$interest_30_day, 2);
        }else{
            $total_paid_per_month = @round(@$per_month[0]['total_paid_per_month'],2);
            $pay_type_name = "แบบคงยอด";
            $interest_30_day  = 0;
        }
        //$total_paid_per_month = round(@$per_month[0]['total_paid_per_month'],-2);
        $arr_data['total_paid_per_month'] = @$total_paid_per_month;
        $arr_data['interest_30_day'] = @$interest_30_day;
        return $arr_data;
    }

    public function get_keep_month($member_id, $loan_id, $createdatetime, $date_start_period){
        $loan_principle_total = 0;
        $loan_interest_total = 0;
        $this->db->join("coop_loan_name", "coop_loan_name.loan_name_id = coop_loan.loan_type", "inner");
        $loans = $this->db->get_where("coop_loan", array(
            "member_id" => $member_id,
            "id <> " => $loan_id,
            "loan_status <> " => "3"
        ))->result_array();
        $keep_month = 0;
        foreach ($loans as $key => $value) {
            $loan_prev_deduct = $this->db->get_where("coop_loan_prev_deduct", array(
                "loan_id" => $loan_id,
                "ref_id" => $value['id']
            ))->row_array();
            $is_loan_dedct = (!empty($loan_prev_deduct)) ? true : false;
            if($is_loan_dedct){
                continue;
            }
            $value['money_per_period'] = $this->db->get_where("coop_loan", array("id"=>$value['id']))->row_array()['money_per_period'];
            if($value['pay_type']==2){
                $finance_month_detail = $this->db->get_where("coop_finance_month_detail", array("loan_id"=>$value['id'], "pay_type" => "principal"))->row_array()['pay_amount'];
            }
            if($value['money_per_period'] == ''){
                $this->db->select(array(
                    'principal_payment',
                    'total_paid_per_month'
                ));
                $this->db->from('coop_loan_period');
                $this->db->where("loan_id='".$value['id']."' AND date_count = '31'");
                $this->db->limit(1);
                $per_month_transaction = $this->db->get()->row_array();
                $value['money_per_period'] = $per_month_transaction['principal_payment'];
            }
            $this->db->select('t1.*, t2.date_start_period');
            $this->db->from('coop_loan_transaction as t1');
            $this->db->join('coop_loan as t2', 't1.loan_id = t2.id', 'inner');
            $this->db->where("t1.loan_id = '".$value['id']."'");
            $this->db->where("t1.transaction_datetime <= '".$createdatetime."'");
            $this->db->where("t1.loan_amount_balance >= 0");
            $this->db->order_by(" t1.loan_transaction_id desc, t1.transaction_datetime desc");
            $this->db->limit(1);
            $loan_transaction = $this->db->get()->row_array();

//            echo $loan_transaction['loan_id'].' '.$loan_transaction['loan_amount_balance'].'<br>';

            if(($loan_transaction['loan_amount_balance']-($value['pay_type']==2 ? $finance_month_detail : $value['money_per_period'])) <= 0){
                continue;
            }
            if($loan_transaction['loan_amount_balance'] > 0){
                if(!empty($date_start_period)) {
                    $interest_per_year = $this->db->get_where("coop_term_of_loan", array(
                        "type_id" => $value['loan_type'],
                        "start_date <=" => $date_start_period
                    ))->row_array()['interest_rate'];
                } else {
                    $interest_per_year = $this->db->get_where("coop_term_of_loan", array("type_id" => $value['loan_type']))->row_array()['interest_rate'];
                }
                if($date_start_period == $loan_transaction['date_start_period']) {
                    $count_day = $this->center_function->diff_day($value['date_period_1'], $value['approve_date']);
                    $temp_interest_31_day = (((@$value['loan_amount_balance'] * $interest_per_year) / 100) / 365) * $count_day;
                }else{
                    $count_day = date('t', strtotime($date_start_period));
                    $temp_interest_31_day = ((((@$loan_transaction['loan_amount_balance']-($value['pay_type']==2 ? $finance_month_detail : $value['money_per_period'])) * $interest_per_year) / 100) / 365) * $count_day;
                }
                $temp_interest_31_day = round($temp_interest_31_day, 2);

                $arr_list_loan[@$value['id']]['loan_principle'] = ($value['pay_type']==2) ? $value['money_per_period']-$temp_interest_31_day : $value['money_per_period']; //ยอดที่ชำระต่อเดือน เงินต้น
                if(strpos(@$value['contract_number'], 'G')) {
                    /**
                     * Todo
                     * รอการแก้ไขให้ระบบแยกเงินกู้ประเภท G
                     */
                    if(in_array($value['member_id'], array('000730', '000711'))){
                        $arr_list_loan[@$value['id']]['loan_principle'] = 0;
                        $arr_list_loan[@$value['id']]['loan_interest'] = 0;
                    }else{
                        $arr_list_loan[@$value['id']]['loan_interest'] = 0;
                    }

                }else{
                    $arr_list_loan[@$value['id']]['loan_interest'] = @$temp_interest_31_day; //(ดอกเบี้ย)
                }
                $arr_list_loan[@$value['id']]['loan_amount_balance'] = $loan_transaction['loan_amount_balance']-$value['money_per_period']; //(balance)
                $arr_list_loan[@$value['id']]['loan_id'] = @$value['id']; //loan_id
                $arr_list_loan[@$value['id']]['contract_number'] = @$value['contract_number']; //contract_number
                $loan_principle_total += $arr_list_loan[@$value['id']]['loan_principle'];
                $loan_interest_total += $arr_list_loan[@$value['id']]['loan_interest'];
            }

            if(!empty($arr_list_loan)) {
                $keep_month = array_sum(array_column($arr_list_loan, 'loan_principle')) + array_sum(array_column($arr_list_loan, 'loan_interest'));
            }
            if(empty($keep_month)){
                $keep_month = 0;
            }
        }
        return $keep_month;
    }
    public function get_report_loan_order($member_id, $loan_id, $createdatetime){
        $createdatetime = substr($createdatetime, 0, 10);
        $createdatetime.= ' 23:59:59';
        $this->db->select(array('t1.id as loan_id', 't1.loan_amount_balance', 't1.loan_type', 't2.loan_type_id', 't2.loan_name'));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_loan_name as t2', 't2.loan_name_id = t1.loan_type', 'left');
        $this->db->where("t1.member_id = '".$member_id."' AND id != '".$loan_id."'");
        $this->db->order_by("t1.loan_type");
        $row = $this->db->get()->result_array();
        $loan_order = array();
        foreach ($row as $key => $value){
            $loan_transaction = $this->db->select('t1.loan_amount_balance')
                ->from('coop_loan_transaction as t1')
                ->where("t1.loan_id = '".$value['loan_id']."' AND t1.transaction_datetime <= '".$createdatetime."'")
                ->order_by('t1.transaction_datetime DESC')
                ->order_by('t1.loan_transaction_id DESC')
                ->get()->row_array();
            if(!empty($loan_transaction['loan_amount_balance'])){
//                $value['loan_amount_balance'] += $loan_transaction['loan_amount_balance'];
//                $loan_order[$value['loan_type_id']][$value['loan_type']][] = $value;
                if(empty($loan_order[$value['loan_type_id']][$value['loan_type']])){
                    $value['loan_amount_balance'] = $loan_transaction['loan_amount_balance'];
                    $loan_order[$value['loan_type_id']][$value['loan_type']]['loan_amount_balance'] = $value['loan_amount_balance'];
                    $loan_order[$value['loan_type_id']][$value['loan_type']]['loan_name'] = $value['loan_name'];
                }else{
                    $value['loan_amount_balance'] = $loan_transaction['loan_amount_balance'];
                    $loan_order[$value['loan_type_id']][$value['loan_type']]['loan_amount_balance'] += $value['loan_amount_balance'];
                }
            }
        }

        // เรียงลำดับ
        $new_loan_order = array();
        $new_loan_order['normal'] = $loan_order['2'];
        $new_loan_order['special'] = $loan_order['3'];
        $new_loan_order['emergent'] = $loan_order['1'];
        $loan_order = $new_loan_order;

        return $loan_order;
    }

    public function get_report_guarantee_person($member_id, $loan_id, $createdatetime){
        $createdatetime = substr($createdatetime, 0, 10);
        $createdatetime.= ' 23:59:59';
        $guarantee_person = $this->db->select(array(
                't1.id as loan_id',
                't3.member_id',
                't4.prename_short',
                't3.firstname_th',
                't3.lastname_th',
                't1.createdatetime'))
            ->from('coop_loan as t1')
            ->join('coop_loan_guarantee_person as t2','t2.loan_id = t1.id', 'inner')
            ->join('coop_mem_apply as t3','t3.member_id = t2.guarantee_person_id', 'left')
            ->join('coop_prename as t4','t4.prename_id = t3.prename_id', 'left')
            ->where("t1.id = '".$loan_id."'")
            ->get()->result_array();
//        $guarantee_person = $this->db->last_query();

        foreach ($guarantee_person as $key => $value){
            if(!empty($value['member_id'])){
                $share = $this->db->select(array('share_collect_value'))
                    ->from('coop_mem_share')
                    ->where("member_id = '".$value['member_id']."' AND share_date < '".$createdatetime."'")
                    ->order_by('share_date DESC, share_id DESC')
                    ->get()->row_array();
                $guarantee_person[$key]['share_collect_value'] = $share['share_collect_value'];
            }
        }

        return $guarantee_person;
    }

    public function get_report_deduct($loan_id)
    {
        $arr_data = array();
        $this->db->select(array('*'));
        $this->db->from('coop_loan_deduct');
        $this->db->where("loan_id = '{$loan_id}'");
        $rs_deduct = $this->db->get()->result_array();
        if (!empty($rs_deduct)) {
            foreach ($rs_deduct AS $key => $row_deduct) {
                if ($row_deduct['loan_deduct_list_code'] == 'deduct_loan_fee') {
                    $arr_data['deduct_loan_fee'] = @$row_deduct['loan_deduct_amount'];
                }

                if ($row_deduct['loan_deduct_list_code'] == 'deduct_before_interest') {
                    $arr_data['deduct_before_interest'] = @$row_deduct['loan_deduct_amount'];
                }

                if ($row_deduct['loan_deduct_list_code'] == 'deduct_person_guarantee') {
                    //มีการกำหนดกรณีไม่ผ่านเกณฑ์
                    $arr_data['deduct_person_guarantee'] = @$row_deduct['loan_deduct_amount'];
                }

                if ($row_deduct['loan_deduct_list_code'] == 'deduct_insurance') {
                    //เบี้ยประกัน
                    $arr_data['deduct_insurance'] = @$row_deduct['loan_deduct_amount'];
                }

                if ($row_deduct['loan_deduct_list_code'] == 'deduct_cheque') {
                    $arr_data['deduct_cheque'] = @$row_deduct['loan_deduct_amount'];
                }
            }
        }
        return $arr_data;
    }

    public function  check_loan_document(){
        $approve_year = $_POST['approve_year'];
        $approve_month = $_POST['approve_month'];

        $where = "MONTH(t1.createdatetime) = '".$approve_month."' AND YEAR(t1.createdatetime) = '".$approve_year."'";
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

