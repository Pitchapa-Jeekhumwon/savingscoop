<?php


class Share_modal extends CI_Model
{

    private $_current = null;
    private $_share_collect_val = null;
    private $_setting = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function setting(){
        $this->db->order_by('setting_id','DESC');
        $this->_setting = $this->db->get('coop_share_setting',1)->row_array();
        return $this;
    }

    public function getSetting($key = ''){
        self::setting();
        if($key != "") {
            return $this->_setting[$key];
        }else{
            return $this->_setting;
        }
    }

    public function current($member_id, $date = ''){

        $where =  "1=1";
        $where .= " and member_id='{$member_id}'";

        if($date != '') {
            $date = date("Y-m-d H:i:s", strtotime($date));
        }else{
            $date = date("Y-m-d H:i:s");
        }
        $where .= " and share_bill_date <= '{$date}' ";
        $this->_current = $this->db->order_by("share_date, share_id", "desc")->get_where('coop_mem_share', $where, 1)->row();

        return $this;
    }

    public function getShareMonth($member_id){
        return $this->db->select("share_month")->get_where('coop_mem_apply', "member_id='{$member_id}'")->row()->share_month;
    }

    public function getCollect(){


        return $this->_current->share_collect;
    }

    public function getCollectVal(){
        return $this->_current->share_collect_value;
    }

    public function getShareEarly(){
        return $this->_current->share_early_value;
    }
    
    public function findRefrainShare($member_id = ""){

        $txt = "";
        //งดหุ้นภาวร
        $refrain_share =  $this->db->get_where('coop_refrain_share', array(
            'member_id' => $member_id,
            'type_refrain' => 1,
        ))->result_array();
        if(isset($refrain_share) && sizeof($refrain_share)){
            return "งดหุ้นภาวร";
        }

        //งดหุ้นชั่วคราว
        $refrain_share =  $this->db->order_by("year_refrain asc, convert(month_refrain , decimal) asc ")->get_where('coop_refrain_share', array(
            'member_id' => $member_id,
            'type_refrain' => 2,
            'year_refrain >=' => (int)date("Y")+543,
            'month_refrain >=' => (int)date("n")
        ))->result_array();

        //echo $this->db->last_query(); exit;
        if(isset($refrain_share) && sizeof($refrain_share)){

            $txt = " งดหุ้นชั่วคราวเดือน ";
            foreach ($refrain_share as $key => $value){

                if($key == 0){
                    $txt .= $this->center_function->month_short_arr()[$value['month_refrain']] . " ". $value['year_refrain'];
                }

                if($key == sizeof($refrain_share)-1 && $key != 0){
                   $txt .= " ถึง " .$this->center_function->month_short_arr()[$value['month_refrain']] . " ". $value['year_refrain'];
                }
            }
            return $txt;
        }

        $txt = "ไม่ระบุ";
        return $txt;

    }


}