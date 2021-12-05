<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_loan_default extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function index(){
		$arr_data = array();

        $this->db->select('*');
        $this->db->from('setting_loan_page');
        $default_loan = $this->db->get()->row_array();
        $arr_data['default_loan'] = $default_loan['loan_type_id'];
        $arr_data['default_loan_name'] = $default_loan['loan_name_id'];
        $arr_data['rs_loan_type'] = $this->contract->getLoanType();
        $arr_data['rs_loan_name'] = $this->contract->getLoanNameByTypeId($arr_data['default_loan']);

//        echo '<pre>';print_r($arr_data);exit;

		$this->libraries->template('setting_loan_default/index',$arr_data);
	}

    function change_loan_type(){
        $row = $this->contract->getLoanNameByTypeId($_POST['type_id']);
        $text_return = "<option value=''>เลือกทั้งหมด</option>";
        foreach($row as $key => $value){
            $text_return .= "<option value='".$value['loan_name_id']."'>".$value['loan_name']." ".$value['loan_name_description']."</option>";
        }
        echo $text_return;
        exit;
    }

    public function save_setting_loan(){
        $this->db->set(array(
            'loan_type_id'=>$_POST['loan_type'],
            'loan_name_id'=>$_POST['loan_name'])
        );
        $this->db->where('id', 1);
        $this->db->update('setting_loan_page');
//        $this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");
        echo 'success';
    }


}
