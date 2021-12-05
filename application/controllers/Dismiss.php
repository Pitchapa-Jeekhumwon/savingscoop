<?php


class Dismiss extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Dismiss_model", "Dismiss");
        $this->load->model("Return_model", "Return");
    }


    public function keep(){
        $arr = array();
        //Load Information Model
        $this->load->model("information", "info");
        $member_id = $this->input->get('member_id');
        $year = $this->input->get('year');
        $year = empty($year) ? date("Y")+543 : $year;
        $month = $this->input->get('month');
        $month = empty($month) ? date('n') : $month;

        if( $this->input->get('member_id') != ''){
            $member_id = $this->input->get("member_id");

            //call member info
            $member = $this->info->member($member_id);
            $arr = $member->getMemberInfo();
            //echo "<pre>"; print_r($arr); exit;

            //profile
            $profileList = $this->Dismiss->getProfileList();
            $arr['profile_year'] = array_filter(array_unique(array_column($profileList, 'profile_year')));
            $arr['profile_month'] = self::month_filter($profileList, (string)date("Y")+543);
            //echo "<pre>"; print_r($arr['profile_month']); exit;
            $arr['keep_lists'] = $keepLists = $this->Dismiss->getKeepingList($member_id, $month, (string)$year);
            sort($arr['profile_year']);
            sort($arr['profile_month']);

            $arr['month_arr'] = $this->center_function->month_arr();
            $arr['member_id'] = $member_id;
            $arr['profile_id'] = $keepLists[0]["profile_id"];
        }
        unset($keepLists, $member);
        //echo "<pre>";print_r($arr);exit;
        $this->libraries->template("dismiss/keep/index", $arr);
    }

    function get_year(){
        $year = $_POST['profile_year'];

        $arr_data = array();
        $this->db->select(array('profile_month'));
        $this->db->from('coop_finance_month_profile');
        $this->db->where("profile_year = '".$year."'");
        $rs_year = $this->db->get()->result_array();
        $arr_data['rs_year'] = @$rs_year;

        echo json_encode($arr_data);
    }

    public function get_profile_year(){
        $year = $this->input->post("year", true);
        $html = "";
        $res = array();
        $arr_month = $this->center_function->month_arr();
        if(isset($year) && $year != ""){
            $res = self::month_filter($this->Dismiss->getProfileList($year));
            if(sizeof($res)){
                foreach ($res as $val){
                    $name_txt = $arr_month[$val];
                    $html .= `<option value="{$val}">{$name_txt}</option>`;
                }
            }
        }
        echo $this->output->set_content_type("text/html", "utf8")->_display();
        echo $html;
    }

    private function month_filter($arr_month, $year){

        foreach ($arr_month as $key => $val) $arr_year[$key] = $year;
        return array_filter(array_unique(array_map(function($arr, $year){
            return $arr['profile_year'] == $year ? $arr['profile_month'] : ""; }, $arr_month, $arr_year)));
    }

    public function get_keeping_month(){
        $html = "";
        $dataLists = $this->Dismiss->getKeepingList($member_id, $month, (string)$year);
        if(sizeof($dataLists)){
            foreach ($dataLists as $key => $item){

            }
        }
        echo $html;
    }

    public function get_keep_items(){
        $html = "";

        $member_id = $this->input->post('member_id');
        $profile_id = $this->input->post('profile_id');
        $deduct_code = $this->input->post('deduct_code');
        $ref_id = $this->input->post('ref_id');

        $dataLists = $this->Dismiss->getKeepItemList( $profile_id, $member_id, $deduct_code, $ref_id);

        if(sizeof($dataLists)){
            $num = 1;
            $total = 0;
            foreach ($dataLists as $key => $item){
                $total += $item['real_pay_amount'];
                $html .= $this->Dismiss->TemplateList($num, (object)$item);
                $num++;
            }
            $html .= $this->Dismiss->TemplateFooter(number_format($total, 2));
        }else{
            echo "empty datas";
        }

        $this->output->set_content_type("text/html", "utf8")->_display();
        echo $html;
    }

    public function get_keep_item_all(){
        $html = "";

        $member_id = $this->input->post("member_id");
        $profile_id = $this->input->post("profile_id");

        $dataLists = $this->Dismiss->getKeepItemAllList($profile_id, $member_id);
        if(sizeof($dataLists)){
            $num = 1; $total = 0;
            foreach ($dataLists as $key => $item){
                $total += $item['real_pay_amount'];
                $html .= $this->Dismiss->TemplateList($num, (object)$item);
                $num++;
            }
            $html .= $this->Dismiss->TemplateFooter(number_format($total, 2));
        }else{
            echo "empty data";
        }
        echo $html;
    }

    public function run_process(){

        $_month = $_POST['profile_month'];
        $_year = $_POST['profile_year']-543;

        $data = array();
        $data['member_id'] = $_POST['member_id'];
        $data['month'] = $_month;
        $data['year'] = $_year;
        $data['profile_id'] = $_POST['profile_id'];
        $data['process_date'] = $return_time = date("Y-m-t", strtotime($_year."-".sprintf("%02d",$_month)."-01"));
        $run_id = $_POST['run_id'];
        $return = $this->Dismiss->getFinanceMonthDetail($data, $run_id, 'in');

        $data['deduct_list'] = array_unique(array_column($return, "deduct_code"));
        $data['loan_list'] = array_filter(array_unique(array_column($return, "loan_id")));
        $data['atm_list'] = array_filter(array_unique(array_column($return, "loan_atm_id")));
        $data['deposit_list'] = array_filter(array_unique(array_column($return, "deposit_account_id")));
        $receipt_number = $this->Dismiss->add_receipt($data);

        if($receipt_number != "") {
            $bill_id = $this->Dismiss->getReturnBill($return_time);
            $dataSet = array();
            $i = 0;
            foreach ($return as $key => $item) {

                $dataSet[$i]['deduct_code'] = $item['deduct_code'];
                $dataSet[$i]['loan_id'] = $item['loan_id'];
                $dataSet[$i]['loan_atm_id'] = $item['loan_atm_id'];
                $dataSet[$i]['account_id'] = $item['deposit_account_id'];
                $dataSet[$i]['member_id'] = $item['member_id'];
                $dataSet[$i]['return_principal'] = $item['principal_payment'];
                $dataSet[$i]['return_interest'] = $item['interest'];
                $dataSet[$i]['return_amount'] = round($item['principal'] + $item['interest'], 2, 1);
                $dataSet[$i]['return_date'] = $return_time;
                $dataSet[$i]['receipt_id'] = $receipt_number;
                $dataSet[$i]['bill_id'] = $bill_id;
                $this->Return->executeReturn($dataSet[$i]);
                $i++;
            }

        }
        echo $data = "success";
        return $data;
    }


}
