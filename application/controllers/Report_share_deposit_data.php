<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_share_deposit_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
        $this->load->model('report_share_data_model');
	}
	
	public function coop_report_balance(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '3'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		$this->libraries->template('report_share_deposit_data/coop_report_balance',$arr_data);
	}

	function coop_report_share_loan_balance_excel(){
        $arr_data = array();
        $code_name = 'report_share_loan_balance';

        $this->db->select('*');
        $this->db->from('format_setting_report');
        $this->db->where('code_name', $code_name);
        $switch_report = $this->db->get()->row_array();
        if($switch_report['switch_code'] == '1'){
            $arr_data = $this->report_share_data_model->get_data_share_loan_balance();
        }

        if($switch_report['switch_code'] == '1') {
            if (@$_GET['type_department'] == '1') {
                $this->load->view('report_share_deposit_data/coop_report_balance_excel', $arr_data);
            } else if (@$_GET['type_department'] == '2') {
                $this->load->view('report_share_deposit_data/coop_report_share_loan_balance_subdivision_excel', $arr_data);
            }
        }
		
	}

	function coop_report_balance_person_excel(){
		ini_set('memory_limit', -1);
		set_time_limit (180);
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}

		if(@$_GET['type_date'] == '1'){
			$this->db->select(array('share_date'));
			$this->db->from('coop_mem_share');
			$this->db->where("share_status IN ('1', '2')");
			$this->db->order_by("share_date ASC");
			$this->db->limit(1);
			$rs_date_share = $this->db->get()->result_array();
			$date_share_min  =  date("Y-m-d", strtotime(@$rs_date_share[0]['share_date']));
			
			
			$this->db->select(array('createdatetime'));
			$this->db->from('coop_loan');
			$this->db->where("loan_status = '1'");
			$this->db->order_by("createdatetime ASC");
			$this->db->limit(1);
			$rs_date_loan = $this->db->get()->result_array();
			$date_loan_min  =  date("Y-m-d", strtotime(@$rs_date_loan[0]['createdatetime']));
			
			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_transaction = $this->db->get()->result_array();
			$date_loan_transaction_min  =  date("Y-m-d", strtotime(@$rs_date_loan_transaction[0]['transaction_datetime']));
			
			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_atm_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_atm = $this->db->get()->result_array();
			$date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));

			if($date_loan_transaction_min < $date_share_min){
				$start_date = $date_loan_transaction_min;
			}else if($date_share_min < $date_loan_min){
				$start_date = $date_share_min;
			}else if($date_loan_min < $date_loan_atm_min){
				$start_date = $date_loan_min;
			}else if($date_loan_atm_min < $date_share_min){
				$start_date = $date_loan_atm_min;
			}else{
				$start_date = $date_share_min;
			}
			$end_date = $get_start_date;
		}else{		
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}
		
		
		$where_date = "";		
		$where_date_loan = "";		
		$where_date_loan_atm = "";		
		$where_date_loan_atm_transaction = "";		
		$where_date_loan_transaction = "";		
		if(@$_GET['start_date'] != ''){
			$where_date .= " AND coop_mem_share.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan .= " AND coop_loan.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm .= " AND coop_loan_atm.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm_transaction .= " AND coop_loan_atm_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_transaction .= " AND coop_loan_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}		
		
		
		$this->db->select(array('coop_loan_name.loan_name_id','coop_loan_type.loan_type_code'));
		$this->db->from('coop_loan_name');
		$this->db->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left');
		$rs_type_code = $this->db->get()->result_array();
		$arr_loan_type_code = array();
		foreach($rs_type_code AS $key_type_code=>$row_type_code){
			$arr_loan_type_code[@$row_type_code['loan_name_id']] = @$row_type_code['loan_type_code'];
		}
		
		$this->db->select(array('max_period'));
		$this->db->from('coop_loan_atm_setting');					
		$rs_atm_setting = $this->db->get()->result_array();
		$row_atm_setting = @$rs_atm_setting[0];
		$max_period_atm = $row_atm_setting['max_period'];
		//echo '<pre>'; print_r($arr_loan_type_code); echo '</pre>'; exit;		

		$arr_data = array();
		
		$where = "";
	
		//Get All data
		$rs = $this->db->select(array(
			'coop_mem_apply.member_id',
			'coop_mem_apply.prename_id',
			'coop_mem_apply.firstname_th',
			'coop_mem_apply.lastname_th',
			'coop_mem_apply.department',
			'coop_mem_apply.faction',
			'coop_mem_apply.level',
			'coop_prename.prename_full',
			't2.mem_group_id as id',
			't1.mem_group_name as name',
			't2.mem_group_name as sub_name',
			't3.mem_group_name as main_name',
			't4.share_id',
			't5.loan_id',
			't5.loan_amount_balance',
			't5.contract_number',
			't5.loan_type',
			't5.period_now',
			't6.loan_atm_id',
			't6.contract_number AS contract_number_atm',
			't6.loan_amount_balance_atm'
		))
		->from("(SELECT IF (
								(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply. level
							) AS level,
							IF (
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.faction
							) AS faction,
							IF (
								(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.department
							) AS department, member_id, prename_id, firstname_th, lastname_th,member_status, retry_date FROM coop_mem_apply) AS coop_mem_apply")
		->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left")
		->join("coop_mem_group as t1","t1.id = coop_mem_apply.level","left")
		->join("coop_mem_group as t2", "t2.id = t1.mem_group_parent_id", "left")
		->join("coop_mem_group as t3", "t3.id = t2.mem_group_parent_id", "left")
		->join("(SELECT 	 
						coop_mem_share.share_id,
						coop_mem_share.member_id
				 	FROM  
						coop_mem_share WHERE coop_mem_share.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY member_id) AS t4",
					"coop_mem_apply.member_id = t4.member_id",
					"left"
				)
		->join("(SELECT t3.member_id ,t3.contract_number ,t3.period_now ,t3.loan_type ,t1.loan_transaction_id,t1.loan_id,t1.loan_amount_balance,t1.transaction_datetime FROM (SELECT t1.loan_transaction_id,t1.loan_id,t1.loan_amount_balance,t1.transaction_datetime FROM coop_loan_transaction t1 INNER JOIN (
SELECT max(t1.loan_transaction_id) loan_transaction_id,t1.loan_id FROM coop_loan_transaction t1 INNER JOIN (
SELECT loan_id,max(transaction_datetime) transaction_datetime FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY loan_id) t2 ON t1.loan_id=t2.loan_id AND t1.transaction_datetime=t2.transaction_datetime GROUP BY t1.loan_id) t2 ON t1.loan_transaction_id=t2.loan_transaction_id AND t1.loan_id=t2.loan_id
) AS t1 LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND t1.loan_amount_balance > 0 GROUP BY t1.loan_id ORDER BY t1.loan_id DESC ,t1.loan_transaction_id DESC ) AS t5", "coop_mem_apply.member_id = t5.member_id", "left")
		->join("(SELECT
						t3.member_id
						,t3.contract_number
						,t1.loan_atm_transaction_id
						,t1.loan_atm_id
						,t1.loan_amount_balance as loan_amount_balance_atm
					FROM
						coop_loan_atm_transaction AS t1
					LEFT JOIN coop_loan_atm AS t3 ON t1.loan_atm_id = t3.loan_atm_id 
					WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59'
					GROUP BY t1.loan_atm_id
					ORDER BY t1.loan_atm_id DESC ,t1.loan_atm_transaction_id DESC
					) AS t6", "coop_mem_apply.member_id = t6.member_id", "left")
		->where("1=1  AND (t5.loan_id != '' OR t4.share_id != '' OR t6.loan_atm_id != '')  AND ( coop_mem_apply.member_status = 1 OR (coop_mem_apply.member_status <> 3 AND  coop_mem_apply.retry_date > '".$end_date." 23:59:59.000'))")
		->order_by('t2.mem_group_id ASC , coop_mem_apply.member_id ASC')
		->get()->result_array();

		//Get Lastest Share Information
		$member_ids = array_column($rs, 'member_id');

		//Get Lastest Loan Information
		$loan_ids = array_column($rs, 'loan_id');
        $where_loan = " 1=1 ";
        if(sizeof(array_filter($loan_ids))){
            $where_loan = " t1.loan_id IN  (".implode(',',array_filter($loan_ids)).") ";
        }
		$loans = $this->db->query("SELECT `t1`.`loan_transaction_id`, `t1`.`loan_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
									FROM `coop_loan_transaction` as `t1`
									INNER JOIN (SELECT loan_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_id)
											as t2 ON `t1`.`loan_id` = `t2`.`loan_id` AND `t1`.`transaction_datetime` = `t2`.`max`
									WHERE {$where_loan}
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
									")->result_array();
		$loan_members = array_column($loans, 'loan_id');
		// echo $this->db->last_query(); exit;

        $loan_atm_ids = array_unique(array_column($result, 'loan_atm_id'));
        sort($loan_atm_ids);
        $where_atm = " 1=1 ";
        if(sizeof(array_filter($loan_atm_ids))){
            $where_atm = " t1.loan_atm_id IN  (".implode(',',array_filter($loan_atm_ids)).") ";
        }

        $loan_atms = $this->db->query("SELECT t1.loan_atm_transaction_id, `t1`.`loan_atm_id`, `t1`.`transaction_datetime`,
									t1.loan_amount_balance AS loan_amount_balance
		
									FROM `coop_loan_atm_transaction` as `t1`
									INNER JOIN (
										SELECT 
											t23.loan_atm_id
											,MAX(t23.loan_atm_transaction_id) AS loan_atm_transaction_id
										FROM (
											SELECT 
												t22.loan_atm_id
												,t22.loan_atm_transaction_id
											FROM (
												SELECT
													loan_atm_id
													, MAX( cast( transaction_datetime AS Datetime ) ) AS max
												FROM
													coop_loan_atm_transaction 
												WHERE
													transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59'
												GROUP BY loan_atm_id
											) AS t21
											LEFT JOIN coop_loan_atm_transaction AS t22 ON t21.loan_atm_id = t22.loan_atm_id AND t21.max = t22.transaction_datetime
										) AS t23
										GROUP BY t23.loan_atm_id
									) as t2 ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id` AND t1.loan_atm_transaction_id = t2.loan_atm_transaction_id
									LEFT JOIN `coop_loan_atm_detail` AS `t3` ON `t1`.`loan_atm_id` = `t3`.`loan_atm_id`	AND `t1`.`transaction_datetime` = `t3`.`loan_date`
									LEFT JOIN `coop_finance_transaction` AS `t4` ON `t1`.`receipt_id` = `t4`.`receipt_id`	AND `t1`.`loan_atm_id` = `t4`.`loan_atm_id`
									LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
									WHERE ".$where_atm."
									GROUP BY `t1`.`loan_atm_id`
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_atm_transaction_id` DESC
									")->result_array();	
		$loan_atm_members = array_column($loan_atms, 'loan_atm_id');

		$run_index = 0;
		$row = array();
		
		$check_row = "xx";
		$index = 0;

        $sql_shares = "SELECT t1.share_id,t1.share_collect,t1.share_collect_value,t1.member_id,t1.share_period,t1.share_date FROM coop_mem_share AS t1 INNER JOIN (
SELECT t1.member_id,max(t1.share_id) share_id FROM coop_mem_share t1 INNER JOIN (SELECT member_id,max(share_date) share_date FROM coop_mem_share WHERE share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY member_id) t2 ON t1.member_id=t2.member_id AND t1.share_date=t2.share_date GROUP BY t1.member_id) t2 ON t1.member_id=t2.member_id AND t1.share_id=t2.share_id";
        $shares = $this->db->query($sql_shares)->result_array();
        $_shares = array();
        //echo $this->db->last_query(); exit;
        foreach ($shares as $key => $share){
            $_shares[$share['member_id']] = $share;
        }
        unset($shares);

		//เงินฝาก
		$sql_deposit = "SELECT 
			t1.account_id,
			t1.mem_id AS member_id,
			t3.transaction_time,
			t3.transaction_balance
			FROM
				coop_maco_account AS t1
				INNER JOIN ( SELECT account_id, MAX( cast( transaction_time AS Datetime )) AS transaction_time,MAX(transaction_id) AS transaction_id  FROM coop_account_transaction WHERE transaction_time BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' GROUP BY account_id ) AS t2 ON t1.account_id = t2.account_id 
				INNER JOIN coop_account_transaction AS t3 ON t2.account_id = t3.account_id AND t2.transaction_time = t3.transaction_time AND t2.transaction_id = t3.transaction_id 
			WHERE
				1 = 1 
				AND IF(t1.account_status = 1 ,(t1.account_status = 1 AND  t1.close_account_date > '".$end_date."'),(t1.account_status = 0))
			ORDER BY
				t1.mem_id ASC,
				t1.account_id ASC";							
		// echo $this->db->last_query(); exit;
		$deposit = $this->db->query($sql_deposit)->result_array();
		$arr_deposit = array();
		$i=0;
		$chk_member_id = '';
		if(!empty($deposit)){
			foreach ($deposit as $key_d => $val_d){
				if($val_d['member_id'] != $chk_member_id){
					$chk_member_id = $val_d['member_id'];
					$i = 0;
				}else{
					$i++;
				}
				$arr_deposit[$val_d['member_id']][$i]['member_id'] = $val_d['member_id'];
				$arr_deposit[$val_d['member_id']][$i]['account_id'] = $val_d['account_id'];
				$arr_deposit[$val_d['member_id']][$i]['transaction_time'] = $val_d['transaction_time'];
				$arr_deposit[$val_d['member_id']][$i]['transaction_balance'] = $val_d['transaction_balance'];
			}
		}
		// echo '<pre>'; print_r($deposit); echo '</pre>'; exit;
		// echo '<pre>'; print_r($arr_deposit); echo '</pre>'; exit;
		// $deposit_members = array_column($deposit, 'loan_id');
		// exit;
		foreach($rs AS $key2=>$value2){
			if($check_row != @$value2['member_id']){				
				$check_row = @$value2['member_id'];


                $shares = $_shares[$value2['member_id']];
				$share_period = (!empty($shares['share_period']))?@$shares['share_period']: "";		
				$check_share = (!empty($shares['check_share']))?@$shares['check_share']: "";		
				if(@$shares['share_status'] == 3){
					$share_collect_value = (!empty($shares['share_payable_value']))?@$shares['share_payable_value']: "";
				}else{
					$share_collect_value = (!empty($shares['share_collect_value']))?@$shares['share_collect_value']: "";
				}

				$runno = 1;
			}else{	
				$runno++;
			}

			$row['data'][$value2['member_id']][$runno]['member_id'] = $value2['member_id'];
			$row['data'][$value2['member_id']][$runno]['prename_full'] = $value2['prename_full'];
			$row['data'][$value2['member_id']][$runno]['firstname_th'] = $value2['firstname_th'];
			$row['data'][$value2['member_id']][$runno]['lastname_th'] = $value2['lastname_th'];
			$row['data'][$value2['member_id']][$runno]['mem_group_name_main'] = $value2['mem_group_name_main'];
			$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['mem_group_name_sub'];
			$row['data'][$value2['member_id']][$runno]['mem_group_name_level'] = $value2['name'];
			$row['data'][$value2['member_id']][$runno]['mem_group_id'] = $value2['id'];
			if($value2->sub_name=='ไม่ระบุ'){
				$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['main_name'];
			}else{					
				$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['sub_name'];
			}
				
			$row['data'][$value2['member_id']][$runno]['mem_group_name_main'] = $value2['main_name'];
			
			//หุ้น
			if ($runno == 1) {
				$row['data'][$value2['member_id']][$runno]['share_period'] = $share_period;
				$row['data'][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
			} else {
				$row['data'][$value2['member_id']][$runno]['share_period'] = "";
				$row['data'][$value2['member_id']][$runno]['share_collect'] = "";
			}
			$row['data'][$value2['member_id']][$runno]['runno'] = $runno;

			//เงินฝาก
			if(!empty($arr_deposit[$value2['member_id']])){
				foreach($arr_deposit[$value2['member_id']] AS $key_deposit=>$val_deposit){
					$row['data'][$value2['member_id']][($key_deposit+1)]['account_id'] = $val_deposit['account_id'];
					$row['data'][$value2['member_id']][($key_deposit+1)]['transaction_balance'] = $val_deposit['transaction_balance'];
					
					if($key_deposit >= 0){
						$row['data'][$value2['member_id']][($key_deposit+1)]['member_id'] = $val_deposit['member_id'];
					}
				}
			}
			
			$loan_type_code = @$arr_loan_type_code[$value2['loan_type']];
			if(@$loan_type_code == 'emergent' && @$value2['loan_amount_balance'] != '' && in_array($value2['loan_id'],$loan_members)
					&& $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance']){
				//เงินกู้ฉุกเฉิน
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['loan_emergent_period_now'] = @$value2['period_now'];
					$row['data'][$value2['member_id']][$runno]['loan_emergent_contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']][$runno]['loan_emergent_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
				} else {
					for($no_count = 1; $no_count <= $runno; $no_count++) {
						if (empty($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'])) {
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_period_now'] = @$value2['period_now'];
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] = @$value2['contract_number'];
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
							break;
						} else if ($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] == $value2['contract_number']) {
							break;
						}
					}
				}
				$run_emergent++;
				if($run_emergent > 1){
					//$runno++;
				}
			}

			if(@$loan_type_code == 'normal' && @$value2['loan_amount_balance'] != '' && in_array($value2['loan_id'],$loan_members)
					&& !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['loan_normal_period_now'] = @$value2['period_now'];
					$row['data'][$value2['member_id']][$runno]['loan_normal_contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']][$runno]['loan_normal_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
				} else {
					for($no_count = 1; $no_count <= $runno; $no_count++) {
						if ($row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'] == $value2['contract_number']) {
							break;
						} else if (empty($row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'])) {
							$row['data'][$value2['member_id']][$no_count]['loan_normal_period_now'] = @$value2['period_now'];
							$row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'] = @$value2['contract_number'];
							$row['data'][$value2['member_id']][$no_count]['loan_normal_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
							break;
						}
					}
				}
				$run_normal++;
			}
			
			if(@$loan_type_code == 'special' && @$value2['loan_amount_balance'] != '' && in_array($value2['loan_id'],$loan_members)
					&& !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['loan_special_period_now'] = @$value2['period_now'];
					$row['data'][$value2['member_id']][$runno]['loan_special_contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']][$runno]['loan_special_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
				} else {
					for($no_count = 1; $no_count <= $runno; $no_count++) {
						if (empty($row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'])) {
							$row['data'][$value2['member_id']][$no_count]['loan_special_period_now'] = @$value2['period_now'];
							$row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'] = @$value2['contract_number'];
							$row['data'][$value2['member_id']][$no_count]['loan_special_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
							break;
						} else if ($row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'] == $value2['contract_number']) {
							break;
						}
					}
				}

				$run_special++;
			}

			if(@$value2['loan_amount_balance_atm'] != '' && in_array($value2['loan_atm_id'],$loan_atm_members)
						&& !empty($loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'])){
				//เงินกู้ฉุกเฉิน ATM
				$atm_index_count = $runno;
				if(!empty($row['data'][$value2['member_id']][$runno]['loan_atm_contract_number'])) {
					$atm_index_count = $runno+1;
				}
				for($no_count = 1; $no_count <= $atm_index_count; $no_count++) {
					if (empty($row['data'][$value2['member_id']][$no_count]['loan_atm_contract_number'])) {
						$row['data'][$value2['member_id']][$no_count]['member_id'] = $value2['member_id'];
						$row['data'][$value2['member_id']][$no_count]['prename_full'] = $value2['prename_full'];
						$row['data'][$value2['member_id']][$no_count]['firstname_th'] = $value2['firstname_th'];
						$row['data'][$value2['member_id']][$no_count]['lastname_th'] = $value2['lastname_th'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_main'] = $value2['mem_group_name_main'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['mem_group_name_sub'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_level'] = $value2['name'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_id'] = $value2['id'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_id'] = $value2['id'];
						$row['data'][$value2['member_id']][$runno]['mem_group_name_level'] = $value2['name'];
						if($value2->sub_name=='ไม่ระบุ'){
							$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['main_name'];
						}else{					
							$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['sub_name'];
						}
	
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_main'] = $value2['main_name'];

						//หุ้น
						if ($runno == 1) {
							$row['data'][$value2['member_id']][$runno]['share_period'] = $share_period;
							$row['data'][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
						} else {
							$row['data'][$value2['member_id']][$runno]['share_period'] = "";
							$row['data'][$value2['member_id']][$runno]['share_collect'] = "";
						}

						$row['data'][$value2['member_id']][$no_count]['runno'] = $runno;
						$row['data'][$value2['member_id']][$no_count]['loan_atm_period_now'] = '';
						$row['data'][$value2['member_id']][$no_count]['loan_atm_contract_number'] = @$value2['contract_number_atm'];
						$row['data'][$value2['member_id']][$no_count]['loan_atm_balance'] = $loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'];
						break;
					} else if ($row['data'][$value2['member_id']][$no_count]['loan_atm_contract_number'] == $value2['contract_number_atm']) {
						break;
					}
				}	
			}

			$run_index++;		

		}

		//Generate Fund support Information
		$where_fund = "1=1";
		$where_fund_t1 = $_GET["type_date"] == 1 ? "payment_date <= '".$end_date." 23:59:59.000'" : "payment_date BETWEEN '".$end_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		$funds = $this->db->select("SUM(t2.principal) as loan_amount_balance, t5.member_id, t5.prename_id, t5.firstname_th, t5.lastname_th, t5.level, t7.id as faction, t8.id as department, t9.prename_full,
									t6.mem_group_id as id, t6.mem_group_name as name, t7.mem_group_name as sub_name, t8.mem_group_name as main_name, t4.id as loan_id, t4.contract_number, t4.loan_type, t4.period_now")
							->from("(SELECT *, MAX(payment_date) as max_date FROM coop_loan_fund_balance_transaction WHERE ".$where_fund_t1." GROUP BY sub_compromise_id) as t1")
							->join("coop_loan_fund_balance_transaction as t2", "t1.sub_compromise_id = t2.sub_compromise_id AND t1.max_date = t2.payment_date", "inner")
							->join("coop_loan_compromise as t3", "t2.compromise_id = t3.id", "inner")
							->join("coop_loan as t4", "t3.loan_id = t4.id", "inner")
							->join("(SELECT IF (
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										coop_mem_apply. level
									) AS level, member_id, prename_id, firstname_th, lastname_th,member_status FROM coop_mem_apply) as t5", "t3.member_id = t5.member_id", "inner")
							->join("coop_mem_group as t6", "t5.level = t6.id", "left")
							->join("coop_mem_group as t7", "t7.id = t6.mem_group_parent_id", "left")
							->join("coop_mem_group as t8", "t8.id = t7.mem_group_parent_id", "left")
							->join("coop_prename as t9", "t5.prename_id = t9.prename_id", "left")
							->where($where_fund)
							->group_by("t2.compromise_id")
							->get()->result_array();

		foreach($funds as $fund) {
			if($fund["loan_amount_balance"] > 0) {
				$data_arr = array();
				$data_arr["member_id"] = $fund["member_id"];
				$data_arr["prename_id"] = $fund["prename_id"];
				$data_arr["firstname_th"] = $fund["firstname_th"];
				$data_arr["lastname_th"] = $fund["lastname_th"];
				$data_arr["department"] = $fund["department"];
				$data_arr["faction"] = $fund["faction"];
				$data_arr["level"] = $fund["level"];
				$data_arr["prename_full"] = $fund["prename_full"];
				$data_arr["id"] = $fund["id"];
				$data_arr["name"] = $fund["name"];
				$data_arr["sub_name"] = $fund["sub_name"];
				$data_arr["main_name"] = $fund["main_name"];
				$data_arr["loan_id"] = $fund["loan_id"];
				$data_arr['loan_amount_balance'] = $fund["loan_amount_balance"];
				$data_arr["contract_number"] = $fund["contract_number"];
				$data_arr["loan_type"] = $fund["loan_type"];
				$data_arr["period_now"] = $fund["period_now"];
				$data_arr['mem_group_id'] = $fund["id"];
				$data_arr['mem_group_name_level'] = $fund["level"];
				$data_arr["mem_group_name_sub"] = $fund["faction"];
				$data_arr["mem_group_name_main"] = $fund["department"];
				$data_arr["loan_normal_period_now"] = $fund["period_now"];
				$data_arr["loan_normal_contract_number"] = $fund["contract_number"];
				$data_arr["loan_normal_balance"] = $fund["loan_amount_balance"];
				$row['data'][$fund["member_id"]][] = $data_arr;
			}
		}

		// echo '<pre>'; print_r($row['data']); echo '</pre>'; exit;
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();	
		// echo '<pre>'; print_r($arr_data['data']); echo '</pre>'; exit;	
		
		$this->load->view('report_share_deposit_data/coop_report_balance_person_excel',$arr_data);	
	}

}
