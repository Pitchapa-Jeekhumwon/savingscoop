<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Report_average_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    //ดึงข้อมูลสมาชิก และหาผลรวม จำนวนเงินปันผล และ จำนวนเงินเฉลี่ยคืน หน้า รายงานสรุปยอดเงินปันผลและเฉลี่ยคืน @Fame
    public function get_report_dividend_average($year, $type_report=''){
        $this->db->select('t1.member_id,t3.bank_name,
                            sum(t2.dividend_value) as sum_dividend_value,
                            sum(t2.average_return_value) as sum_average_return_value,
                            t1.firstname_th,t1.lastname_th,t4.prename_short,t2.pay_type');
        $this->db->from('coop_mem_apply as t1');
        $this->db->join('coop_dividend_average as t2','t1.member_id = t2.member_id','INNER');
        $this->db->join('coop_bank as t3','t2.bank_code = t3.bank_id','LEFT');
        $this->db->join('coop_prename as t4','t1.prename_id = t4.prename_id','LEFT');
        $this->db->where("t2.year = '$year'");
        $this->db->group_by('t1.member_id');
        if ($type_report == 1){
            //เรียงตามธนาคาร
            $this->db->order_by('t1.member_id', 'ASC');
        }else if ($type_report == 2){
            //เรียงตามวิธีรับเงิน
            $this->db->order_by('t2.pay_type', 'ASC');
            $this->db->order_by('t1.member_id', 'ASC');
        }else {
            $this->db->order_by('t1.member_id', 'ASC');
        }
        $row = $this->db->get()->result_array();

        return $row;
    }

    //ดึงข้อมูลสมาชิก และหาผลรวม ดอกเบี้ย และ จำนวนเงินเฉลี่ยคืน @Fame
    public function get_report_interest_average($year, $type_report=''){
        $this->db->select('t1.member_id,
                            sum(t2.interest_total) as interest_sum,
                            sum(t2.average_return_value) as average_return_value_sum,
                            t1.firstname_th,t1.lastname_th,t3.prename_short,t4.mem_group_name');
        $this->db->from('coop_mem_apply as t1');
        $this->db->join('coop_dividend_average as t2','t1.member_id = t2.member_id','INNER');
        $this->db->join('coop_prename as t3','t1.prename_id = t3.prename_id','LEFT');
        $this->db->join('coop_mem_group as t4','t1.mem_group_id = t4.mem_group_id','LEFT');
        $this->db->where("t2.year = '$year'");
        $this->db->group_by('t1.member_id');
        $row = $this->db->get()->result_array();

        return $row;
    }

    public function get_report_dividend_sum($year){ //ดึงข้อมูลสรุปยอด จำนวนเงิน จำนวนราย ดอกเบี้ยเงินกู้และเงินเฉลี่ยคืน หน้า สรุปยอด(รวม) @Fame
        $this->db->select('t1.pay_type,t1.dividend_drop,
                            count(t1.member_id) as count_member_id,
                            sum(t1.share_value) as share_value_sum,
                            sum(t1.dividend_value) as dividend_value_sum');
        $this->db->from('coop_dividend_average as t1');
        $this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','INNER');
        $this->db->where("t1.year = '$year'");
        $this->db->group_by('t1.dividend_drop');
        $row = $this->db->get()->result_array();
        for ($i = 0;$i <= 3;$i++){
            if ($row[$i] == ''){ //เพิ่มข้อมูลที่จะแสดง กรณีที่ไม่มี dividend_drop
                $add_data = array('pay_type'=>'1',
                                'dividend_drop' => $i,
                                'count_member_id' => 0,
                                'share_value_sum' => 0,
                                'dividend_value_sum' => 0);
                $row[$i] = $add_data;
            }
        }
        return $row;
    }

    public function get_report_average_return_sum($year){ //ดึงข้อมูลสรุปยอด จำนวนเงิน จำนวนราย ดอกเบี้ยเงินกู้และเงินเฉลี่ยคืน หน้า สรุปยอด(รวม) @Fame
        $this->db->select('t1.pay_type,t1.average_drop,
                            count(t1.member_id) as count_member_id,
                            sum(t1.share_value) as share_value_sum,
                            t1.average_return_value,
                            sum(t1.average_return_value) as average_return_value_sum');
        $this->db->from('coop_dividend_average as t1');
        $this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','INNER');
        $this->db->where("t1.year = '$year'");
        $this->db->group_by('t1.average_drop');
        $row = $this->db->get()->result_array();
        for ($i = 0;$i <= 3;$i++){
            if ($row[$i] == ''){ //เพิ่มข้อมูลที่จะแสดง กรณีที่ไม่มี dividend_drop
                $add_data = array('pay_type'=>'1',
                                'average_drop' => $i,
                                'count_member_id' => 0,
                                'share_value_sum' => 0,
                                'average_return_value_sum' => 0);
                $row[$i] = $add_data;
            }
        }
        return $row;
    }

    public function get_calculate_satang($money){ // ปัดทศนิยมเงินปันผล เงินเฉลี่ยคืน เป็น 0 25 50 75 @Fame
        $number_input = number_format($money, 2, '.', '');
        $number_input = explode(".", $number_input);
        if($number_input[1]>50 and $number_input[1]<75){
            $number_input[1] = 75;
            }else if($number_input[1]>25 and $number_input[1]<50){
            $number_input[1] = 50;
            }else if($number_input[1]>0 and $number_input[1]<25){
            $number_input[1] = 25;
        }
        $number_input = $number_input[0].'.'.$number_input[1];
        return $number_input;
    }
}