<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register_facescan_dup extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	public function index() {
		$arr_data = array();
		$id = @$_GET['id'];

		if(!empty($id)) {
			$this->db->select("*");
			$this->db->from("coop_meeting");
			$this->db->where("meeting_id = '{$id}'");
			$row = $this->db->get()->row_array();

			$arr_data['id'] = $id;
			$arr_data['row'] = $row;

			$this->preview_libraries->template_preview('meeting/register_facescan_dup', $arr_data);
		}
	}
	
	public function update_status() {
		$msg = "";
		$err_no = 0;
		
		$data_update = array();
		$data_update['dup_status'] = $_POST["dup_status"];
		$this->db->where("meeting_regis_id", $_POST["id"]);
		$this->db->update("coop_meeting_regis", $data_update);
		
		$status = "1";
		
		echo json_encode([
			"err_no" => $err_no,
			"status" => $status,
			"msg" => $msg
		]);
		exit;
	}
	
}