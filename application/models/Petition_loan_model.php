<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Petition_loan_model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}

	public function get_petition_file($type_id,$date,$loan_name_id="",$type_doc=""){
		$result = array();
		$where = "";
		if($loan_name_id !=''){
			$where = " AND loan_name_id = '".$loan_name_id."'";
		}
		
		$where_type_doc = "";
		if($type_doc !=''){
			$where_type_doc = " AND type_doc = '".$type_doc."'";
		}
		
		$this->db->select(array('petition_file'));
		$this->db->from('coop_loan_petition');
		$this->db->where("loan_type_id = '{$type_id}' AND startdatetime <= '{$date}' AND status = 1 {$where} {$where_type_doc}");
		$this->db->order_by("startdatetime DESC");
		$this->db->limit(1);
		$row = $this->db->get()->row_array();
		
		if(!empty($row)){
			$result = $row;
		}else{
			$this->db->select(array('petition_file'));
			$this->db->from('coop_loan_petition');
			$this->db->where("loan_type_id = '{$type_id}' AND startdatetime <= '{$date}' AND status = 1  AND loan_name_id IS NULL  {$where_type_doc}");
			$this->db->order_by("startdatetime DESC");
			$this->db->limit(1);
			$row_main = $this->db->get()->row_array();
			if(!empty($row_main)){
				$result = $row_main;
			}else{
				$this->db->select(array('petition_file'));
				$this->db->from('coop_loan_type');
				$this->db->where("id = '{$type_id}'");
				$this->db->limit(1);
				$row = $this->db->get()->row_array();
				$result = $row;
			}
		}
		
		if(!empty($_GET['debug'])){
			echo $this->db->last_query(); echo '<br>';
			echo '<pre>'; print_r($result); echo '</pre>'; echo '<br>';
		}
		return $result['petition_file'];
	}
}