<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Credit_settings extends CI_Model
{
 	public function __construct()
	{
		parent::__construct();
	}

	public function  getLoanGroupName(){
 		return $this->db->get("coop_loan_group")->result_array();
	}

	public function findLoanGroupName($id){
 		return $this->db->get_where("coop_loan_name", "loan_group_id='{$id}'")->result_array();
	}

}
