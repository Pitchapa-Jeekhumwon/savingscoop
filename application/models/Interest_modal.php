<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Interest_modal extends CI_Model {

	public function __construct()
	{
        parent::__construct();
        $this->load->model("Condition_loan_model", "condition_model");
    }

    public function get_interest($loan_type_id, $start_date, $optional = null){
        $this->db->where('start_date <=', $start_date);
        $this->db->where("type_id", $loan_type_id);
        $this->db->order_by("start_date desc, id desc");
        $term_of_loan = $this->db->get("coop_term_of_loan")->result_array()[0];
        $term_of_loan_id = $term_of_loan['id'];

        if($optional==null){// display
            return $term_of_loan['interest_rate'];
        }else{
            $this->db->where("(result_type = 'interest' or result_type = 'interest_rate')");
            $main_condition_rate_interest = $this->db->get_where("coop_condition_of_loan", array(
                "term_of_loan_id" => $term_of_loan_id
            ))->result_array();
            foreach ($main_condition_rate_interest as $key => $value) {
                $condition = $this->db->get_where("coop_condition_list", array(
                    "col_id" => $value['col_id']
                ))->result_array();
                $check = true;

                foreach ($condition as $i => $value1) {
                    /** หาค่า A */
                    $a = $this->condition_model->get_op_val($value1["ccd_id_a"], $optional);
                    /** หาค่า B */
                    $b = $this->condition_model->get_op_val($value1["ccd_id_b"], $optional);
                    $op = @$value1['operation'];
                    if( $this->center_function->operator($a, $b, $op) ){

                    }else{
                        $check = false;
                        break;
                    }
                }

                if($check){
                    $result = $this->condition_model->get_op_val($value['result_value'], $optional);
                    return $result;
                }
            }
        }

        return $term_of_loan['interest_rate'];
        
    }

    public function first_interest_payment($loan_id){
	    return $this->db->select("*")->from("coop_finance_transaction")->where("loan_id" , $loan_id)->get()->num_rows() ? true : false;
    }

    function is_query($str){
		return strpos($str, "?") <= -1 ? false : true;
	}

    public function operator($a, $b, $operator){
        switch ($operator) {
            case '>':
                return ($a > $b);
            case '>=':
                return ($a >= $b);
            case '<':
                return ($a < $b);
            case '<=':
                return ($a <= $b);
            case '=':
                return ($a = $b);
            case '!=':
                return ($a != $b);
            
            default:
                die("no operator()");
                break;
        }
    }
    
}
