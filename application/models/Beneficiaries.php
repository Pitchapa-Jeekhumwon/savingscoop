<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Beneficiaries extends  CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private $_memberId = null;
    private $_limit = 10;
    private function counter(){
        $this->db->select("count(*) as `amount`");
        $this->db->from("coop_beneficiary_history");
        $this->db->where(array('member_id' =>  $this->_memberId));
        $this->db->group_by("created_at, gain_detail_id");
        $this->db->order_by("created_at asc");
        $counter = $this->db->get()->row()->amount;
        return $counter - $this->_limit > 1 ? $counter - $this->_limit : 0;
    }

    private function history(){

        $offset = self::counter();

        $this->db->select(array("t1.created_at","t2.user_name","t1.gain_detail_id"));
        $this->db->from("coop_beneficiary_history t1");
        $this->db->join("coop_user as t2", "t2.user_id=t1.user_id");
        $this->db->where(array('member_id' =>  $this->_memberId));
        $this->db->group_by("t1.created_at, t1.gain_detail_id");
        $this->db->order_by("t1.created_at asc");
        $this->db->limit($this->_limit, $offset);
        return $this->db->get()->result_array();
    }

    private function historyDetail(){
        $this->db->where(array('member_id' => $this->_memberId));
        return $this->db->get("coop_beneficiary_history")->result_array();
    }

    public function getDetail($Id = ""){
        $this->db->where(array('id' => $Id));
        return $this->db->get("coop_beneficiary_history")->row_array();
    }

    public function historyList($member_id, $limit = 10){
        $result = [];
        $this->_memberId = $member_id;
        $this->_limit = $limit;
        $_history = self::history();
        $label = self::historyLabel(array('coop_mem_gain_detail', 'coop_beneficiary'));
        $i = 0;
        if(sizeof($_history)){
            $list = self::historyDetail();

            foreach ($_history as $index => $item){
                $result[$i]['user_name'] = $item['user_name'];
                $result[$i]['gain_detail_id'] = $item['gain_detail_id'];
                $result[$i]['created_at'] = $item['created_at'];
                $k = 0;
                foreach ($list as $key => $value){
                    if($item['created_at'] == $value['created_at'] &&  $label[$value['input_name']] !== "") {
                        $value['name'] = $label[$value['table']][$value['input_name']];
                        $result[$i]['detail'][$k] = $value;
                        $k++;
                    }
                }
                $i++;
            }
        }
        return $result;
    }

    public function historyLabel($table_name, $ignore = array()){
        $result = array();
        foreach ($table_name as $item => $value) {
            $this->db->select(array("column_name", "column_comment"))->from("information_schema.columns");
            $this->db->where(array("table_schema" => $this->db->database, 'table_name' => $value));
            $rs = $this->db->get()->result_array();
            foreach ($rs as $key => $val) {
                $result[$value][$val['column_name']] = $val['column_comment'];
            }
        }
        return $result;
    }

    public function condition($member_id = ''){
      return $this->db->get_where('coop_beneficiary', array('member_id' => $member_id))->row_array();
    }


}