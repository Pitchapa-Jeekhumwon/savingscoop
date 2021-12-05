<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Beneficiary extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		if($this->input->post()){

			$data = $this->input->post();

			$data['admin_id'] = $_SESSION['USER_ID'];
			if($data['gain_detail_id']!=''){

                $old = $this->db->get_where("coop_mem_gain_detail", array('member_id' => $data['member_id'], 'gain_detail_id' => $data['gain_detail_id']))->row_array();
                $history_gain_detail = array();
                $i = 0;
                foreach ($old as $key => $value){
                    if($data[$key] !== $value){
                        $history_gain_detail[$i]['member_id'] = $data['member_id'];
                        $history_gain_detail[$i]['input_name'] = $key;
                        $history_gain_detail[$i]['old_value'] = $value;
                        $history_gain_detail[$i]['new_value'] = $data[$key];
                        $history_gain_detail[$i]['gain_detail_id'] = $data['gain_detail_id'];
                        $history_gain_detail[$i]['user_id'] =  $_SESSION['USER_ID'];
                        $history_gain_detail[$i]['created_at'] =  date('Y-m-d H:i:s');
                        $history_gain_detail[$i]['table'] = 'coop_mem_gain_detail' ;
                        $i++;
                    }
                }
                unset($old);

				$this->db->where('gain_detail_id', $data['gain_detail_id']);
				unset($data['gain_detail_id']);
				$this->db->update('coop_mem_gain_detail', $data);

				if($this->db->affected_rows()){
				    $this->db->insert_batch("coop_beneficiary_history", $history_gain_detail);
                }

			}else{
				unset($data['gain_detail_id']);
				$data['g_create'] = date('Y-m-d H:i:s');
				$this->db->insert('coop_mem_gain_detail', $data);
                if($this->db->affected_rows()){
                    $i = 0;
                    foreach ($data as $key => $value){
                            $history_gain_detail[$i]['member_id'] = $data['member_id'];
                            $history_gain_detail[$i]['input_name'] = $key;
                            $history_gain_detail[$i]['new_value'] = $data[$key];
                            $history_gain_detail[$i]['gain_detail_id'] = $this->db->insert_id();
                            $history_gain_detail[$i]['user_id'] =  $_SESSION['USER_ID'];
                            $history_gain_detail[$i]['created_at'] =  date('Y-m-d H:i:s');
                            $history_gain_detail[$i]['table'] = 'coop_mem_gain_detail' ;
                            $i++;
                    }
                    $this->db->insert_batch("coop_beneficiary_history", $history_gain_detail);
                }

			}
			//echo"<pre>";print_r($data);
			echo "<script> document.location.href = '".PROJECTPATH."/beneficiary?member_id=".$data['member_id']."' </script>";
			exit;
		}
		if($this->input->get('member_id')!=''){
			$member_id = $this->input->get('member_id');
		}else{
			$member_id = '';
		}
		$arr_data = array();
		$arr_data['member_id'] = $member_id;
		if($member_id != ''){
			$this->db->select(array('t1.*',
							't2.mem_group_name AS department_name',
							't3.mem_group_name AS faction_name',
							't4.mem_group_name AS level_name'));
			$this->db->from('coop_mem_apply as t1');			
			$this->db->join("coop_mem_group AS t2","t1.department = t2.id","left");
			$this->db->join("coop_mem_group AS t3","t1.faction = t3.id","left");
			$this->db->join("coop_mem_group AS t4","t1.level = t4.id","left");
			$this->db->where("t1.member_id = '".$member_id."'");
			$rs = $this->db->get()->result_array();
			$row = @$rs[0];
			
			$department = "";
			$department .= @$row['department_name'];
			$department .= (@$row["faction_name"]== 'ไม่ระบุ')?"":"  ".@$row["faction_name"];
			$department .= "  ".@$row["level_name"];
			$row['mem_group_name'] = $department;
			$arr_data['row_member'] = $row;	
			
			//อายุเกษียณ
			$this->db->select(array('retire_age'));
			$this->db->from('coop_profile');
			$rs_retired = $this->db->get()->result_array();
			$arr_data['retire_age'] = $rs_retired[0]['retire_age'];	

			//ประเภทสมาชิก
			$this->db->select('mem_type_id, mem_type_name');
			$this->db->from('coop_mem_type');
			$rs_mem_type = $this->db->get()->result_array();
			$mem_type_list = array();
			foreach($rs_mem_type AS $key=>$row_mem_type){
				$mem_type_list[$row_mem_type['mem_type_id']] = $row_mem_type['mem_type_name'];
			}
			
			$arr_data['mem_type_list'] = $mem_type_list;
			
			$this->db->select(
				array(
					'coop_mem_gain_detail.*',
					'coop_prename.prename_short',
					'coop_district.district_name',
					'coop_amphur.amphur_name',
					'coop_province.province_name',
					'coop_mem_relation.relation_name',
					'coop_user.user_name'
				)
			);
			$this->db->from('coop_mem_gain_detail');
			$this->db->where("member_id = '".$member_id."'");
			$this->db->join('coop_prename', 'coop_prename.prename_id = coop_mem_gain_detail.g_prename_id', 'left');
			$this->db->join('coop_district', 'coop_district.district_id = coop_mem_gain_detail.g_district_id', 'left');
			$this->db->join('coop_amphur', 'coop_amphur.amphur_id = coop_mem_gain_detail.g_amphur_id', 'left');
			$this->db->join('coop_province', 'coop_province.province_id = coop_mem_gain_detail.g_province_id', 'left');
			$this->db->join('coop_mem_relation', 'coop_mem_relation.relation_id = coop_mem_gain_detail.g_relation_id', 'left');
			$this->db->join('coop_user', 'coop_user.user_id = coop_mem_gain_detail.admin_id', 'left');
			$row = $this->db->get()->result_array();
			$arr_data['data'] = $row;

           $arr_data['history'] = $this->beneficiaries->historyList($member_id);

           //echo "<pre>"; print_r($arr_data['history']); exit;

            $condition = $this->beneficiaries->condition($member_id);
            if( isset($condition) && sizeof($condition)){
                if($condition['howto_beneficiary'] === "1") {
                    $arr_data['check_choose_1'] = 'checked="checked"';
                }else if($condition['howto_beneficiary'] === "2") {
                    $arr_data['check_choose_2'] = 'checked="checked"';
                }else if($condition['howto_beneficiary'] === "3") {
                    $arr_data['check_choose_3'] = 'checked="checked"';
                    $arr_data['other'] = $condition['other'];
                }

                $arr_data['effective_date'] = $this->center_function->mydate2date(date('Y-m-d', strtotime($condition['effective_date'])));

            }else{
                //beneficiary condition
                $arr_data['check_choose_1'] = 'checked="checked"';
                $arr_data['effective_date'] = $this->center_function->mydate2date(date('Y-m-d'));
            }
		}else{
			$arr_data['row_member'] = array();
			$arr_data['data'] = array();

		}

		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->libraries->template('beneficiary/index',$arr_data);
	}

	function add_beneficiary(){
		$arr_data = array();
		$arr_data['gain_detail_id'] = $this->input->post('gain_detail_id');
		$arr_data['member_id'] = $this->input->post('member_id');

		if($arr_data['gain_detail_id']!=''){
			$this->db->select('*');
			$this->db->from('coop_mem_gain_detail');
			$this->db->where("gain_detail_id = '".$arr_data['gain_detail_id']."'");
			$row = $this->db->get()->result_array();
			$arr_data['data'] = $row[0];
		}else{
			$arr_data['data'] = array();
		}

		$this->db->select('relation_id, relation_name');
		$this->db->from('coop_mem_relation');
		$row = $this->db->get()->result_array();
		$arr_data['relation'] = $row;

		$this->db->select('prename_id, prename_full');
		$this->db->from('coop_prename');
		$row = $this->db->get()->result_array();
		$arr_data['prename'] = $row;

		$this->db->select('province_id, province_name');
		$this->db->from('coop_province');
		$this->db->order_by('province_name');
		$row = $this->db->get()->result_array();
		$arr_data['province'] = $row;

		if(@$arr_data['data']["g_province_id"]!=''){
			$this->db->select('amphur_id, amphur_name');
			$this->db->from('coop_amphur');
			$this->db->where("province_id = '".$arr_data['data']["g_province_id"]."'");
			$this->db->order_by('amphur_name');
			$row = $this->db->get()->result_array();
			$arr_data['amphur'] = $row;
		}else{
			$arr_data['amphur'] = array();
		}

		if(@$arr_data['data']["g_amphur_id"]!=''){
			$this->db->select('district_id, district_name');
			$this->db->from('coop_district');
			$this->db->where("amphur_id = '".@$arr_data['data']["g_amphur_id"]."'");
			$this->db->order_by('district_name');
			$row = $this->db->get()->result_array();
			$arr_data['district'] = $row;
		}else{
			$arr_data['district'] = array();
		}

		$this->load->view('beneficiary/add_beneficiary',$arr_data);
	}

	function show_beneficiary(){
		$arr_data = array();

		$this->db->select(
			array(
				'coop_mem_gain_detail.*',
				'coop_prename.prename_short',
				'coop_district.district_name',
				'coop_amphur.amphur_name',
				'coop_province.province_name',
				'coop_mem_relation.relation_name',
				'coop_user.user_name'
			)
		);
		$this->db->from('coop_mem_gain_detail');
		$this->db->where("gain_detail_id = '".$this->input->post('gain_detail_id')."'");
		$this->db->join('coop_prename', 'coop_prename.prename_id = coop_mem_gain_detail.g_prename_id', 'left');
		$this->db->join('coop_district', 'coop_district.district_id = coop_mem_gain_detail.g_district_id', 'left');
		$this->db->join('coop_amphur', 'coop_amphur.amphur_id = coop_mem_gain_detail.g_amphur_id', 'left');
		$this->db->join('coop_province', 'coop_province.province_id = coop_mem_gain_detail.g_province_id', 'left');
		$this->db->join('coop_mem_relation', 'coop_mem_relation.relation_id = coop_mem_gain_detail.g_relation_id', 'left');
		$this->db->join('coop_user', 'coop_user.user_id = coop_mem_gain_detail.admin_id', 'left');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row[0];

		$this->load->view('beneficiary/show_beneficiary',$arr_data);
	}

	function delete_beneficiary($gain_detail_id,$member_id){
		$this->db->where('gain_detail_id', $gain_detail_id);
		$this->db->delete('coop_mem_gain_detail');

		echo "<script> document.location.href = '".PROJECTPATH."/beneficiary?member_id=".$member_id."' </script>";
		exit;
	}

	function save_pdf(){
		$member_id = $this->uri->segment(3);
		if($_FILES['file']!='' && $member_id!=""){
			$config['upload_path']          = FCPATH.'assets/uploads/benefits_attach';
			$config['allowed_types']        = 'pdf';
			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload('file'))
            {
				$message = "อัพโหลดไม่สำเร็จ";
        	}
            else
            {	
				$data = array('upload_data' => $this->upload->data());
				// var_dump($data);
				$temp_date = explode("/", $_POST['benefits_attach_date']);
				$date = ($temp_date[2]-543)."-".$temp_date[1]."-".$temp_date[0];
				$this->db->set("benefits_attach", $data['upload_data']['file_name'] );
				$this->db->set("benefits_attach_date", $date );
				$this->db->where("member_id", $member_id);
				$this->db->update("coop_mem_apply");
				$message = "อัพโหลดสำเร็จ";


			}		
		}
		header("Location: ".base_url('beneficiary?member_id='.$member_id.'&upload='.$message) );		
		exit;
	}

	public function save_beneficiary(){
        if($this->input->post()) {
            $data = $this->input->post();
            $member_id = $data['member_id'];
            $row = $this->db->get_where('coop_beneficiary', array('member_id' => $data['member_id']))->result_array();
            if (sizeof($row)) {
                $data_insert = array();
                $data_insert['howto_beneficiary'] = $data['howto_beneficiary'];
                $data_insert['effective_date'] = $this->center_function->ConvertToSQLDate($data['effective_date']);
                $data_insert['other'] = $data['other'];
                $data_insert['user_update'] = $_SESSION['USER_ID'];

                $old = array();
                $i = 0;
                foreach ($row[0] as $key => $value){
                    if($row[0][$key] != $data_insert[$key]) {
                        $old[$i]['member_id'] = $data_insert['member_id'];
                        $old[$i]['input_name'] = $key;
                        $old[$i]['old_value'] = $value;
                        $old[$i]['new_value'] = $data_insert[$key];
                        $old[$i]['user_id'] =  $_SESSION['USER_ID'];
                        $old[$i]['created_at'] =  date('Y-m-d H:i:s');
                        $old[$i]['table'] = 'coop_beneficiary' ;
                        $i++;
                    }
                }
                if(sizeof($old)){
                    $this->db->insert_batch("coop_beneficiary_history", $old);
                }

                $this->db->update('coop_beneficiary', $data_insert, "member_id='{$member_id}'");
            } else {
                $data_insert = array();
                $data_insert['member_id'] = $data['member_id'];
                $data_insert['howto_beneficiary'] = $data['howto_beneficiary'];
                $data_insert['other'] = $data['other'];
                $data_insert['effective_date'] = $this->center_function->ConvertToSQLDate($data['effective_date']);
                $data_insert['user_create_id'] = $_SESSION['USER_ID'];
                $data_insert['createdatetime'] = date('Y-m-d H:i:s');

                $i = 0; $old = array();
                foreach ($data_insert as $key => $value){
                        $old[$i]['member_id'] = $data_insert['member_id'];
                        $old[$i]['input_name'] = $key;
                        $old[$i]['new_value'] = $data_insert[$key];
                        $old[$i]['user_id'] =  $_SESSION['USER_ID'];
                        $old[$i]['created_at'] =  date('Y-m-d H:i:s');
                        $old[$i]['table'] = 'coop_beneficiary' ;
                        $i++;
                }
                if(sizeof($old)){
                    $this->db->insert_batch("coop_beneficiary_history", $old);
                }
                $this->db->insert('coop_beneficiary', $data_insert);
            }
        }
        if($this->db->affected_rows()) {
            $this->center_function->toast("บันทึกเงื่อนไขสำเร็จ");
        }else{
            $this->center_function->toastDanger('บันทึกเงื่อนไขไม่สำเร็จ');
        }
        header("Location: ".base_url('beneficiary?member_id='.$member_id) );
        exit;
    }

	public function get_change_detail(){

	    $detail = $this->beneficiaries->getDetail($this->input->post('id'));
	    $label = $this->beneficiaries->historyLabel(array('coop_mem_gain_detail', 'coop_beneficiary'));

	    $data = array(
	        'name' => $label[$detail['input_name']],
            'new_value' => $detail['new_value'] == NULL ? "(NULL)" : $detail['new_value'] ,
            'old_value' => $detail['old_value'] == NULL ? "(NULL)" : $detail['new_value'] ,
        );

        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
