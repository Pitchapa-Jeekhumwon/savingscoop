<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Link extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	public function index() {
		$arr_data = array();
		
		$this->db->select("*");
		$this->db->from("coop_data");
		$this->db->where("data_id = 'is_facescan_regis'");
		$_row = $this->db->get()->row_array();
		$arr_data["is_facescan_regis"] = $_row["data_value"];
		
		$this->libraries->template('meeting/link', $arr_data);
	}
	
	public function set_facescan_regis() {
		$json = [

		];
		
		$data_update = [];
		$data_update['data_value'] = $_POST["is_facescan_regis"];
		$this->db->where("data_id", "is_facescan_regis");
		$this->db->update("coop_data", $data_update);
		
		echo json_encode($json);
		exit;
	}
	
}