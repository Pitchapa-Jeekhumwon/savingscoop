<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class MetaRest extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function rule(){
		$this->load->model("Condition_loan_model", "condition_model");
		$used_value 	= "";
		$global_value 	= "";
		$message 		= "";
		$data 			= $this->input->post();
		$loan_amount	= $data['loan'];
		$term 			= $this->db->get_where("coop_term_of_loan", array("type_id" => $data['term_of_loan_id'], "start_date >= " => "'".date("Y-m-d")."'"))->result_array()[0];
		$global_value 	= $term[$data['result_type']];

		$condition_of_loan = $this->db->get_where("coop_condition_of_loan", array(
			"result_type" 				=> $data['result_type'],
			"term_of_loan_id"			=> $term['id']
		))->result_array();

		$sql = $this->db->last_query();
		foreach ($condition_of_loan as $key => $row) {
			$rs_result_value = $this->db->get_where("coop_condition_detail", array(
				"ccd_id" => $row['result_value']
			))->result_array()[0];

			if(@$rs_result_value['a_is_meta'] == 1){
				$raw_data = $this->db->select('coop_meta_condition.fieldname as value, coop_meta_condition.req_field')
					->from('coop_meta_condition')
					->join('coop_condition_detail', 'coop_meta_condition.id = coop_condition_detail.a ','left')
					->where('coop_condition_detail.ccd_id = ' . $row['result_value'])->get()->result_array();
				if(!empty($raw_data[0]['value'])){
					@$rs_result_value['a'] = $raw_data[0]['value'];
				}
				else{
					@$rs_result_value['a'] = $data[$raw_data[0]['req_field']];
				}
			}
			if(@$rs_result_value['b_is_meta'] == 1){
				$raw_data = $this->db->select('coop_meta_condition.fieldname as value')
					->from('coop_meta_condition')
					->join('coop_condition_detail', 'coop_meta_condition.id = coop_condition_detail.b','left')
					->where('coop_condition_detail.ccd_id = ' . $row['result_value'])->get()->result_array();
				if(!empty($raw_data[0]['value'])){
					@$rs_result_value['b'] = $raw_data[0]['value'];
				}
				else{
					@$rs_result_value['b'] = $data[$raw_data[0]['req_field']];
				}
			}

			if($this->is_query(@$rs_result_value['a'])){
				$sql = @$rs_result_value['a'];
				$rs_check = $this->db->query($sql, $data['member_id'])->result_array()[0]['value'];
				$a = $rs_check;
			}else
				$a = $rs_result_value['a'];

			if($this->is_query(@$rs_result_value['b'])){
				$sql = @$rs_result_value['b'];
				$rs_check = $this->db->query($sql, $data['member_id'])->result_array()[0]['value'];
				$b = $rs_check;
			}else
				$b = $rs_result_value['b'];

			$op = @$rs_result_value['op'];
			if( $this->center_function->operator($a, $b, $op) ){
				$result_value = $this->center_function->operator($a, $b, $op);
			}else{
				$result_value = $a;
			}

			$condition_of_loan[$key]['result_value'] = $result_value;
			$condition = $this->db->get_where("coop_condition_list", array(
				"col_id" => $row['col_id']
			))->result_array();

			$status = true;
			foreach ($condition as $i => $value) {
				/** หาค่า A */
				$a = $this->condition_model->get_op_val($value["ccd_id_a"], array("member_id" => $data['member_id'], "optional" => $data['optional'], "loan" => $loan_amount));
				/** หาค่า B */
				$b = $this->condition_model->get_op_val($value["ccd_id_b"], array("member_id" => $data['member_id'], "optional" => $data['optional'], "loan" => $loan_amount));
				$op = @$value['operation'];

				$get_op[$key] = '[ A : ' . $a . ' ] [ B : ' . $b . ' ] [ Operation : ' . $op .' ]' . ' [ ccd_id_a : ' . $value["ccd_id_a"] . ' ][ ccd_id_b : ' . $value["ccd_id_b"] . ' ] '; //debug
				$check_operator[$key] = $this->center_function->operator($a, $b, $op);

				if( $this->center_function->operator($a, $b, $op) ){
					$return_text_garantor = "";
					$result = $value["conn_garantor_id"];
				}else{
					$status = false;
					break;
				}
			}

			$condition_of_loan[$key]['condition'] = $condition;
			if($status){
				$used_value = $result_value;
				$operator = $row['operator'];
				$message = "";
				break;
			}else{
				$message .= $row['detail_text']."\n";
			}

		}



		if(sizeof($condition_of_loan) <= 0){
			$used_value = $global_value;
		}

		header('Content-Type: application/json');
		echo json_encode(array("items" => $data, "condition_of_loan" => $condition_of_loan, "global_value" => $global_value, "value" => $used_value, "message" => $message, "operator" => $operator));
	}

	function operator($a, $b, $op){
		$val = false;
		switch ($op) {
			case '>': $val = ($a > $b) ? true : false;
				break;
			case '>=': $val = ($a >= $b) ? true : false;
				break;
			case '<': $val = ($a < $b) ? true : false;
				break;
			case '<=': $val = ($a <= $b) ? true : false;
				break;
			case '==': $val = ($a == $b) ? true : false;
				break;
			case '!=': $val = ($a != $b) ? true : false;
				break;
			case '+': $val = ($a + $b);
				break;
			case '-': $val = ($a - $b);
				break;
			case '*': $val = ($a * $b);
				break;
			case '/': $val = ($a / $b);
				break;
			case '^': $val = ($a ^ $b);
				break;
			default:
				$val = false;
				break;
		}

		return $val;
	}

	function is_query($str){
		return strpos($str, "?") <= -1 ? false : true;
	}

}
