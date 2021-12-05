<?php
class Deposit_month_model extends CI_Model {
    
	public function get_deposit_month_due($date_now)
    {
		$arr_data = array();
		$deduction_month = date('n',strtotime($date_now));
		$deduction_year = date('Y',strtotime($date_now))+543;	
		
		$rs_deposit_month = $this->db->select(array('t1.member_id','t1.account_id','t1.total_amount','t1.deduction_type','DATE(t2.created) AS date_open'
													,'t1.deduction_month'
													,'t1.deduction_year'
													,'t1.admin_id'
													,"t2.type_id"
													,"(SELECT deduction_of_year FROM coop_deposit_type_setting_detail WHERE type_id = t2.type_id AND start_date <= '{$date_now}' ORDER BY start_date DESC LIMIT 1) AS deduction_of_year"
													,"(SELECT max_month FROM coop_deposit_type_setting_detail WHERE type_id = t2.type_id AND start_date <= '{$date_now}' ORDER BY start_date DESC LIMIT 1) AS max_month"
													))
													->from('coop_deposit_month_transaction AS t1')
													->join("coop_maco_account AS t2","t1.account_id = t2.account_id","inner")
													->join("(SELECT account_id,MAX(CONCAT(deduction_year,RIGHT(CONCAT('00', deduction_month), 2))) AS max_date,MAX(id) AS max_id FROM coop_deposit_month_transaction GROUP BY account_id) AS t3","t1.account_id = t3.account_id AND CONCAT(t1.deduction_year,RIGHT(CONCAT('00', t1.deduction_month), 2)) = t3.max_date AND t1.id = t3.max_id","inner")
													->where("t2.account_status = 0")
													->order_by('t1.deduction_year DESC, t1.deduction_month DESC')
													->get()->result_array();
		//echo '<pre>'; print_r($rs_deposit_month); echo '</pre>';		
		//echo $this->db->last_query(); echo '<br>'; //exit;
		$date_dep = $date_now;
		$i = 0;
		foreach($rs_deposit_month AS $key=>$val){
			if(@$val['deduction_of_year'] <= 0){
				continue;
			}
			
			if(@$deduction_month == $val['deduction_month'] && @$deduction_year == $val['deduction_year'] && $val['admin_id']=='SYSTEM'){
				continue;
			}
			
			$num_month_maturity = @$val['max_month'];
			$where_chk = " AND (";
				$where_chk .= "(YEAR(transaction_time) = YEAR(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))
					AND MONTH(transaction_time) = MONTH(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))
					AND DAY(transaction_time) = DAY(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH)))";
				
				$cur_day_count = date("t", strtotime($date_dep));
				$_row = $this->db->query("SELECT DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH) AS prev_date")->row_array();
				if(date("d", strtotime($date_dep)) == $cur_day_count){
					$prev_day_count = date("t", strtotime($_row["prev_date"]));
					$day_diff = $prev_day_count - $cur_day_count;
					for($i = 1; $i <= $day_diff; $i++) {
						$_row2 = $this->db->query("SELECT DATE_ADD('".$_row["prev_date"]."', INTERVAL ".$i." DAY) AS next_date")->row_array();
						$where_chk .= " OR (YEAR(transaction_time) = YEAR('".$_row2["next_date"]."')
							AND MONTH(transaction_time) = MONTH('".$_row2["next_date"]."')
							AND DAY(transaction_time) = DAY('".$_row2["next_date"]."'))";
					}
				}
				
				$where_chk .= ")";			
			$this->db->select("transaction_time, transaction_id, transaction_deposit, transaction_balance, transaction_list, TIMESTAMPDIFF(MONTH,transaction_time, '".$date_now." 23:59:59') AS period");
			$this->db->from("coop_account_transaction");
			$this->db->where("fixed_deposit_status <> '1' AND account_id = '".$val['account_id']."' AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD') ".$where_chk);
			$this->db->order_by("transaction_time, transaction_id");
			$rs_due = $this->db->get()->row_array();	
			if(!empty($rs_due)){
				$arr_data[$i]['member_id'] = @$val['member_id'];
				$arr_data[$i]['account_id'] = @$val['account_id'];
				$arr_data[$i]['deduction_type'] = 0; //การหักส่ง (0=หักส่งรายเดือน,1=งดส่ง)
				$arr_data[$i]['deduction_month'] = @$deduction_month;
				$arr_data[$i]['deduction_year'] = @$deduction_year;
				$arr_data[$i]['total_amount'] = @$val['total_amount']+@$val['deduction_of_year'];
				$i++;
			}else{
				$arr_data = array();
			}
		}
		return $arr_data;
	}
	
	public function save_deposit_month($data)
    {
		$affected_rows = 0;	
		foreach($data AS $key=>$val){
			$data_insert = array();
			$data_insert['member_id'] = @$val['member_id'];
			$data_insert['account_id'] = @$val['account_id'];
			$data_insert['deduction_type'] = @$val['deduction_type'];
			$data_insert['deduction_month'] = @$val['deduction_month'];
			$data_insert['deduction_year'] = @$val['deduction_year'];
			$data_insert['total_amount'] = str_replace(',','',@$val['total_amount']);
			$data_insert['admin_id'] = 'SYSTEM';		
			$data_insert['updatetime'] = date('Y-m-d H:i:s');
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert('coop_deposit_month_transaction', $data_insert);
			if($this->db->affected_rows()){
				$affected_rows++;
			}
		}
		
		if($affected_rows > 0){
			$result = 'sucess';
		}else{
			$result = 'error';
		}	
		return $result;
	}	
	
	public function insert_deposit_month($date_now='')
    {
		if($date_now == ''){
			$date_now = date('Y-m-d');
		}	
		$arr_data = array();		
		$get_date = $this->get_deposit_month_due($date_now);
		$result = $this->save_deposit_month($get_date);
		return $result;
	}
}