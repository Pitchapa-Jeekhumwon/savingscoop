<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Condition_loan_model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}

	public function get_meta_id($txt){
		$id = "";
		$this->db->where("detail_text", $txt);
		$result = $this->db->get("coop_meta_condition")->result_array()[0];
		if(!empty($result)){
			$id = $result['id'];
		}
		return $id;
	}

	//opt['member'] ต้องส่งมา ***
	function get_value_condition_of_loan($type_id, $key_field, $opt, $debug=0){
		// var_dump($opt);
		// echo $key_field."<hr>";
		$this->db->where("start_date <=", "'".date("Y-m-d")."'", false);
		$this->db->order_by("start_date desc");
		$this->db->limit(1);
		$term = $this->db->get_where("coop_term_of_loan", array(
			"type_id" => $type_id
		))->result_array()[0];
		
		if(empty($term)){
			exit;
		}
		
		$used_value 	= "";
		$global_value 	= "";
		$message 		= "";
		$global_value 	= $term[$key_field];
		
		$condition_of_loan = $this->db->get_where("coop_condition_of_loan", array(
			"result_type" 				=> $key_field,
			"term_of_loan_id"			=> $term['id']
		))->result_array();

        if($debug == 2){
            echo "<pre>"; print_r($condition_of_loan);
        }

		foreach ($condition_of_loan as $key => $row) {

		    $rs_result_list = $this->db->get_where("coop_condition_list", array("col_id" => $row['col_id'] ))->row_array();

            if($debug == 2){
                echo "<pre>"; print_r($rs_result_list);
            }

            if(!empty($rs_result_list)){
                $a = self::getCCDVal($rs_result_list['ccd_id_a'], $opt);
                $b = self::getCCDVal($rs_result_list['ccd_id_b'], $opt);
                $op = @$rs_result_list['operation'];
                if($this->center_function->operator($a, $b, $op) === false){
                    continue;
                }
                if($debug == 2){
                    echo $a." |".$op."| ".$b."<br>";
                }
            }

			$rs_result_value = $this->db->get_where("coop_condition_detail", array(
				"ccd_id" => $row['result_value']
			))->result_array()[0];

            if($debug == 2){
                echo "1: <pre>"; print_r($rs_result_value);
            }

            if(@$rs_result_value['a_is_meta'] == 1){

                $raw_data = $this->db->select('coop_meta_condition.fieldname as value, coop_meta_condition.req_field, coop_meta_condition.params')
                    ->from('coop_meta_condition')
                    ->join('coop_condition_detail', 'coop_meta_condition.id = coop_condition_detail.a ','left')
                    ->where('coop_condition_detail.ccd_id = ' . $row['result_value'])->get()->row_array();

                if($debug == 2){
                    echo "<pre> a : "; print_r($raw_data);
                }

                if(!empty($raw_data['value'])){
                    @$rs_result_value['a'] = $raw_data['value'];
                    $require_a = $raw_data['req_field'];
                    $params_a = $raw_data['params'];
                }
                else{
                    @$rs_result_value['a'] = $opt[$raw_data[0]['req_field']];
                }
            }

            if(@$rs_result_value['b_is_meta'] == 1){
                $raw_data = $this->db->select('coop_meta_condition.fieldname as value, coop_meta_condition.req_field, coop_meta_condition.params')
                    ->from('coop_meta_condition')
                    ->join('coop_condition_detail', 'coop_meta_condition.id = coop_condition_detail.b','left')
                    ->where('coop_condition_detail.ccd_id = ' . $row['result_value'])->get()->result_array();

                if($debug == 2){
                    echo "<pre> b : "; print_r($raw_data);
                }
                if(!empty($raw_data[0]['value'])){
                    @$rs_result_value['b'] = $raw_data[0]['value'];
                    $require_b = $raw_data[0]['req_field'];
                }
                else{
                    @$rs_result_value['b'] = $opt[$raw_data[0]['req_field']];
                }
            }


			if($this->is_query(@$rs_result_value['a'])){
				$sql = @$rs_result_value['a'];
				if(isset($params_a)) {
                    $rs_check = $this->db->query($sql, [$opt[$params_a], $opt[$require_a]])->result_array()[0]['value'];
                }else{
                    $rs_check = $this->db->query($sql, $opt[$require_a])->result_array()[0]['value'];
                }
				$a = $rs_check;
			}else {
                $a = $rs_result_value['a'];
            }

			if($this->is_query(@$rs_result_value['b'])){
				$sql = @$rs_result_value['b'];
                if(isset($params_b)) {
                    $rs_check = $this->db->query($sql,[$opt[$params_b], $opt[$require_b]])->result_array()[0]['value'];
                }else{
                    $rs_check = $this->db->query($sql, $opt[$require_b])->result_array()[0]['value'];
                }
				$b = $rs_check;
			}else {
                $b = $rs_result_value['b'];
            }

			$op = @$rs_result_value['op'];


			//echo $a." |".$op."|- ".$b."<br>";
			if( $this->center_function->operator($a, $b, $op) ){
				$result_value = $this->center_function->operator($a, $b, $op);
			}else{
				$result_value = $a;
			}

            if($debug == 2){
                echo "result: ". $result_value." |". $op."| ".$b."<br>";
            }

			// var_dump($condition);
			$status = true;

            $condition_of_loan[$key]['result_value'] = $result_value;
            $condition = $this->db->get_where("coop_condition_list", array(
                "col_id" => $row['col_id']
            ))->result_array();

			/*
            if($debug == 2){
                echo "<pre>"; print_r($condition);
            }

			foreach ($condition as $i => $value) {
				// หาค่า A
				$a = $this->condition_model->get_op_val($value["ccd_id_a"], $opt);
				if($a==""){
					$a = $opt['force_a'];
				}
				// หาค่า B
				$b = $this->condition_model->get_op_val($value["ccd_id_b"], $opt);
				if($b==""){
					$b = $opt['force_b'];
				}
				$op = @$value['operation'];

                if($debug == 2){
                    echo "in loop: ". $a." | ".$op." | ".$b."<br>";
                }

                if( $this->center_function->operator($a, $b, $op) ){
					$return_text_garantor = "";
					$result = $value["conn_garantor_id"];
					// echo "TRUEEEE";
				}else{
					$status = false;
					break;
				}
			}*/

			// if($opt['required']==""){
			// 	$a = $opt['force_a'];
			// }else

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

		// var_dump($condition_of_loan);

		if(sizeof($condition_of_loan) <= 0){
			$used_value = $global_value;
		}

		// echo $used_value;
		// var_dump($rs_condition);
		return $used_value;
	}

	function get_op_val($ccd_id, $opt){
		$rs_ccd_id = $this->db->get_where("coop_condition_detail", array(
			"ccd_id" => $ccd_id
		))->result_array()[0];
		
		if($this->is_query($rs_ccd_id['a'])){
			$sql = $rs_ccd_id['a'];
			$rs = $this->db->query($sql, $opt['member_id'])->result_array()[0]['value'];
			$tmp_a = $rs;
		}else if($rs_ccd_id['a_is_meta']=="1"){
			$tmp_sql_query = $this->db->get_where("coop_meta_condition", array(
				"id" => $rs_ccd_id['a']
			))->result_array()[0];
			$sql_query = $tmp_sql_query['fieldname'];
			$req_field = $tmp_sql_query['req_field'];
			$params = $tmp_sql_query['params'];

			if(!empty($sql_query)){
			    if(!empty($params)){
                    $rs = @$this->db->query($sql_query, [$opt[$params] ,$opt[$req_field]])->result_array()[0]['value'];
                }else{
                    $rs = @$this->db->query($sql_query, $opt[$req_field])->result_array()[0]['value'];
                }
			}else{
				$rs = $opt[$req_field];
			}
			$tmp_a = $rs;
		}else{
			$tmp_a = $rs_ccd_id['a'];
		}

		if($this->is_query($rs_ccd_id['b'])){
			$sql = $rs_ccd_id['b'];
			$rs = @$this->db->query($sql, $opt['member_id'])->result_array()[0]['value'];
			$tmp_b = $rs;
		}else if($rs_ccd_id['b_is_meta']=="1"){
            $tmp_sql_query = $this->db->get_where("coop_meta_condition", array(
                "id" => $rs_ccd_id['b']
            ))->row_array();
            $sql_query = $tmp_sql_query['fieldname'];
            $req_field = $tmp_sql_query['req_field'];
            $params = $tmp_sql_query['params'];
            if(!empty($sql_query)){
                if(!empty($params)){
                    $rs = @$this->db->query($sql_query, [$opt[$params] ,$opt[$req_field]])->row_array()['value'];
                }else{
                    $rs = @$this->db->query($sql_query, $opt[$req_field])->row_array()['value'];
                }
			}else{
				$rs = $opt['optional'];
			}
			$tmp_b = $rs;
		}else{
			$tmp_b = $rs_ccd_id['b'];
		}
		
		// echo $tmp_a." tmp ".$tmp_b."<hr>";
		$tmp_op = $rs_ccd_id['op'];

		$val = $tmp_a;
		if(!empty($tmp_op)){
			$val = $this->operator($tmp_a, $tmp_b, $tmp_op);
		}

		return $val;
	}

	function is_query($str){
		return strpos($str, "?") <= -1 ? false : true;
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
			case '=!': $val = ($a =! $b) ? true : false;
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

	private function getCCDId($ccd_id){
	    return $this->db->get_where("coop_condition_detail", array('ccd_id' => $ccd_id))->row_array();
    }

    private function getCCDVal($ccd_id, $opt = array()){
	    $ccd = self::getCCDId($ccd_id);
        $result_value = 0;
	    if($ccd['a_is_meta']  === '1'){
            $meta = self::getMetaCCD($ccd["a"]);
            $result_value = self::getFormulaMeta($meta, $opt);
        }
	    if($ccd['a_is_meta'] === '0' || empty($result_value)){
            return $ccd["a"];
        }else{
	        return $result_value;
        }
    }

    private function getFormulaMeta($meta, $opt = array()){
	    if(!empty($meta['fieldname']) && (!empty($opt[$meta['req_field']]) || !empty($opt[$meta['params']]))){
	        if(!empty($meta['params'])) {
                return $this->db->query($meta['fieldname'], [self::validate($opt[$meta['params']]), self::validate($opt[$meta['req_field']])])->row_array()['value'];
            }else{
                return $this->db->query($meta['fieldname'], $opt[$meta['req_field']])->row_array()['value'];
            }
        } else if(empty($meta['fieldname']) && (!empty($opt[$meta['req_field']]) || !empty($opt[$meta['params']]))) {
			return $opt[$meta['params']];
		}
	    return null;
    }

    private function getMetaCCD($id){
	    return $this->db->get_where("coop_meta_condition", array("id" => $id))->row_array();
    }

    private function validate($params = null){
	    return !empty($params) ? $params : 0;
    }

}
