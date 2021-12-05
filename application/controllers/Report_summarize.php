<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_summarize extends CI_Controller
{
    public function index(){
        echo 'asd';
    }

    public function coop_report_refund_average_excel(){   
        $arr_data = array();
        $arr = Array ();
        
        $this->load->model('Report_average_model','average_model');
        $data = $this->average_model->get_report_interest_average($year, $type_report);
        foreach($data as $key => $value){ //ปัดทศนิยมดอกเบี้ยเงินกู้ เงินเฉลี่ยคืน เป็น 0 25 50 75
            $data[$key]['interest_sum'] = $this->average_model->get_calculate_satang($value['interest_sum']);
            $data[$key]['average_return_value_sum'] = $this->average_model->get_calculate_satang($value['average_return_value_sum']);
        }
        //@start จัดข้อมูลที่จะแสดงในแต่ละหน้า
		$page = 1;
		$first_page_size = 12;
		$page_size = 40;
		$index = 0;
        $page_index = 1;
        
        foreach($data as $member) {
			if ($index < $page_size){
                $index++;
                $datas[$page][] = $member;
            }else{
                $page++;
                $datas[$page][] = $member;
                $index = 0;
                $page_size = 45;
            }
		}
		$arr_data['datas'] = $datas;
        $arr_data['page_all'] = $page;
        //@end

        $this->preview_libraries->template_preview('report_refund_average/coop_report_refund_average_excel',$arr_data);
    }

    public function check_report_refund_average(){
        echo 'success';
    }
}