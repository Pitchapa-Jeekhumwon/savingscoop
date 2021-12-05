<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_dividend_average extends CI_Controller
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
        $this->libraries->template('report_dividend_average/coop_report_dividend_average',$arr_data);
    }

    public function coop_report_dividend_average_excel(){   
        $arr_data = array();
        $arr = Array ();
        $type_report = @$_GET['type_report'];
        $year = @$_GET['year']-543;
        $this->load->model('Report_average_model','average_model');
        $data = $this->average_model->get_report_dividend_average($year, $type_report);
        foreach($data as $key => $value){ //ปัดทศนิยมเงินปันผล เงินเฉลี่ยคืน เป็น 0 25 50 75
            $data[$key]['sum_dividend_value'] = $this->average_model->get_calculate_satang($value['sum_dividend_value']);
            $data[$key]['sum_average_return_value'] = $this->average_model->get_calculate_satang($value['sum_average_return_value']);
        }
		$page = 1;
		$page_size = 40;
		$prev_level = "x";
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
                $page_size = 42;
            }
		}

		$arr_data['datas'] = $datas;
		$arr_data['page_all'] = $page;
		
        $this->preview_libraries->template_preview('report_dividend_average/coop_report_dividend_average_excel',$arr_data);
    }

    public function check_report_dividend_average(){
        echo 'success';
    }

}