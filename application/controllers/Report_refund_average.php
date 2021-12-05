<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_refund_average extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        $arr_data = array();
        // หาจำนวน ค.ศ. ที่ไม่ซ้ำใน coop_dividend_average
        $this->db->distinct('year');
        $this->db->select('year');
        $this->db->from('coop_dividend_average');
        $year_arr = $this->db->get()->result_array();
        $arr_data['year'] = $year_arr;
        foreach ($arr_data['year'] as $key => $value){ 
            $arr_data['year'][$key]['year'] = $value['year']+543; //  เปลี่ยน ค.ศ. เป็น พ.ศ
        }
        $this->libraries->template('report_refund_average/coop_report_refund_average',$arr_data);
    }

    public function coop_report_refund_average_excel(){   
        $arr_data = array();
        $arr = Array ();
        $year = @$_GET['year']-543; 
        $this->load->model('Report_average_model','average_model');
        $data = $this->average_model->get_report_interest_average($year);
        foreach($data as $key => $value){ //ปัดทศนิยมดอกเบี้ยเงินกู้ เงินเฉลี่ยคืน เป็น 0 25 50 75
            $data[$key]['interest_sum'] = $this->average_model->get_calculate_satang($value['interest_sum']);
            $data[$key]['average_return_value_sum'] = $this->average_model->get_calculate_satang($value['average_return_value_sum']);
        }
        //@start จัดข้อมูลที่จะแสดงในแต่ละหน้า
		$page = 1;
		$first_page_size = 12;
		$page_size = 40;
		$prev_level = "x";
		$index = 0;
		$first_page_level = 1;
        $page_index = 1;
        
        foreach($data as $member) {
			if ($index < $page_size){
                $index++;
                $datas[$page][] = $member;
            }else{
                $page++;
                $datas[$page][] = $member;
                $index = 0;
                $page_size = 43;
            }
		}
		$arr_data['datas'] = $datas;
        $arr_data['page_all'] = $page;
        //@end

        $this->preview_libraries->template_preview('report_refund_average/coop_report_refund_average_excel',$arr_data);
    }

    public function report_summarize_excel(){   
        $arr_data = array();
        $arr = Array ();
        $year = @$_GET['year']-543; 
        $this->load->model('Report_average_model','average_model');
        $dividend_arr = $this->average_model->get_report_dividend_sum($year);
        $data_average_return_arr = $this->average_model->get_report_average_return_sum($year);
        foreach($dividend_arr as $key => $value){ //ปัดทศนิยมดอกเบี้ยเงินกู้ เงินเฉลี่ยคืน เป็น 0 25 50 75
            $data[$key]['dividend_value_sum'] = $this->average_model->get_calculate_satang($value['dividend_value_sum']);
        }
        $arr_data['dividend_arr'] = $dividend_arr;
        $arr_data['data_average_return_arr'] = $data_average_return_arr;



        $this->preview_libraries->template_preview('report_refund_average/report_summarize_excel',$arr_data);
    }

    public function check_report_refund_average(){
        echo 'success';
    }
    public function check_report_summarize(){
        echo 'success';
    }
}