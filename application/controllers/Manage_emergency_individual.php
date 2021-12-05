<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_emergency_individual extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function index(){
        {
            $arr_data = array();
            $month_arr = array('1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม');

            $where = '';

            $x = 0;
            $join_arr = array();
            $join_arr[$x]['table'] = 'coop_mem_apply as t2';
            $join_arr[$x]['condition'] = 't1.member_id = t2.member_id';
            $join_arr[$x]['type'] = 'inner';
            $x++;
            $join_arr[$x]['table'] = 'coop_prename as t3';
            $join_arr[$x]['condition'] = 't2.prename_id = t3.prename_id';
            $join_arr[$x]['type'] = 'left';

            $this->paginater_all->type(DB_TYPE);
            $this->paginater_all->select('
			t1.*, t1.id as setting_id, 
			t2.firstname_th, 
			t2.lastname_th,
			t3.prename_short
		');
            $this->paginater_all->main_table('coop_setting_member_loan_mobile as t1');
//            $this->paginater_all->where("non_pay_status = '0'" . $where);
            $this->paginater_all->page_now(@$_GET["page"]);
            $this->paginater_all->per_page(10);
            $this->paginater_all->page_link_limit(20);
            $this->paginater_all->order_by('member_id');
            $this->paginater_all->join_arr($join_arr);
            $row = $this->paginater_all->paginater_process();
            //echo"<pre>";print_r($row);exit;
//            echo $this->db->last_query();exit;
            $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], @$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
            $i = $row['page_start'];

            foreach ($row['data'] as $key => $value){
                $start_date = '';
                $start_time = '';
                $end_date = '';
                $end_time = '';
                if($value['start_date'] != ''){
                    $start_date = date("Y-m-d",strtotime($value['start_date']));
                    $start_time = date("H:i",strtotime($value['start_date']));
                }
                if($value['end_date'] != ''){
                    $end_date = date("Y-m-d",strtotime($value['end_date']));
                    $end_time = date("H:i",strtotime($value['end_date']));
                }
                $row['data'][$key]['start_date'] = $start_date;
                $row['data'][$key]['start_time'] = $start_time;
                $row['data'][$key]['end_date'] = $end_date;
                $row['data'][$key]['end_time'] = $end_time;
            }

            $arr_data['num_rows'] = $row['num_rows'];
            $arr_data['paging'] = $paging;
            $arr_data['data'] = $row['data'];
            $arr_data['i'] = $i;

            $arr_data['month_arr'] = $month_arr;
            $arr_data['setting_status'] = array('0'=>'ไม่ใช้งาน','1'=>'ใช้งาน');

//            exit;

            $this->libraries->template('manage_emergency_individual/index', $arr_data);
        }
	}

    function manage_emergency_individual_save()
    {
//        echo"<pre>";print_r($_POST);echo "<pre>";
//        exit;

        if(!empty($_POST['data']['list_data'])){
            foreach ($_POST['data']['list_data'] as $key => $value) {
                $this->db->select(array('id'));
                $this->db->from('coop_setting_member_loan_mobile');
                $this->db->where("member_id = '" . @$value['member_id']."'");
                $setting_member_loan_mobile = $this->db->get()->row_array();

                $data_insert = array();
                $data_insert['member_id'] = @$value['member_id'];
                if(!empty($value['date_transfer_start'])){
                    $date_start_arr = explode('/', $value['date_transfer_start']);
                    $data_insert['start_date'] = ($date_start_arr[2]-543).'-'.$date_start_arr[1].'-'.$date_start_arr[0];
                    if(!empty($value['time_transfer_start'])){
                        $data_insert['start_date'] .= ' '.$value['time_transfer_start'].':00';
                    }else{
                        $data_insert['start_date'] .= ' 00:00:00';
                    }
                }
                if(!empty($value['date_transfer_end'])){
                    $date_start_arr = explode('/', $value['date_transfer_end']);
                    $data_insert['end_date'] = ($date_start_arr[2]-543).'-'.$date_start_arr[1].'-'.$date_start_arr[0];
                    if(!empty($value['time_transfer_end'])){
                        $data_insert['end_date'] .= ' '.$value['time_transfer_end'].':00';
                    }else{
                        $data_insert['end_date'] .= ' 00:00:00';
                    }
                }
                if(@$value['status'] == 'on'){
                    $data_insert['status'] = '1';
                }else{
                    $data_insert['status'] = '0';
                }
//                echo '<pre>'; print_r($data_insert); echo '</pre>';
                if (@$setting_member_loan_mobile['id'] != '') {
                    $this->db->where('id', $setting_member_loan_mobile['id']);
                    $this->db->update('coop_setting_member_loan_mobile', $data_insert);
                } else {
                    $this->db->insert('coop_setting_member_loan_mobile', $data_insert);
                }
            }
        }
        if(!empty($_POST['update'])){
            foreach ($_POST['update'] as $key => $value){
                $data_update = array();
                if($value['loan_app'] == '1'){
                    $data_update['status'] = '1';
                }else{
                    $data_update['status'] = '0';
                }
                if(!empty($value['date_transfer_start'])){
                    $date_start_arr = explode('/', $value['date_transfer_start']);
                    $data_update['start_date'] = ($date_start_arr[2]-543).'-'.$date_start_arr[1].'-'.$date_start_arr[0];
                    if(!empty($value['time_transfer_start'])){
                        $data_update['start_date'] .= ' '.$value['time_transfer_start'].':00';
                    }else{
                        $data_update['start_date'] .= ' 00:00:00';
                    }
                }

                if(!empty($value['date_transfer_end'])){
                    $date_start_arr = explode('/', $value['date_transfer_end']);
                    $data_update['end_date'] = ($date_start_arr[2]-543).'-'.$date_start_arr[1].'-'.$date_start_arr[0];
                    if(!empty($value['time_transfer_end'])){
                        $data_update['end_date'] .= ' '.$value['time_transfer_end'].':00';
                    }else{
                        $data_update['end_date'] .= ' 00:00:00';
                    }
                }

                $this->db->where('id', $key);
                $this->db->update('coop_setting_member_loan_mobile', $data_update);
            }
        }
        $this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
        echo "<script>document.location.href='" . base_url(PROJECTPATH . '/manage_emergency_individual') . "'</script>";
    }
    function manage_emergency_individual_delete($id)
    {
//        echo"<pre>";print_r($id);exit;
        $this->db->where('id', $id);
        $this->db->delete('coop_setting_member_loan_mobile');
        $this->center_function->toast('ลบข้อมูลเรียบร้อยแล้ว');
        echo "<script>document.location.href='" . base_url(PROJECTPATH . '/manage_emergency_individual') . "'</script>";
    }
}
