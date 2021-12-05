<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');


class Report_share_data_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

    }

    public function get_data_share_loan_balance_person(){
        ini_set('memory_limit', -1);
        set_time_limit(-1);
        //$this->db->save_queries = FALSE;
        if(@$_GET['start_date']){
            $start_date_arr = explode('/',urldecode(@$_GET['start_date']));
            $start_day = $start_date_arr[0];
            $start_month = $start_date_arr[1];
            $start_year = $start_date_arr[2];
            $start_year -= 543;
            $get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
        }

        if(@$_GET['type_date'] == '1'){
            $sql = "SELECT MIN(transaction_datetime) AS transaction_datetime FROM (
					SELECT transaction_datetime FROM (
							SELECT DATE(share_date) AS transaction_datetime FROM coop_mem_share WHERE share_type = 'BF' ORDER BY share_date DESC LIMIT 1
					) as t1
					UNION ALL
					(
							SELECT DATE(transaction_datetime) AS transaction_datetime FROM coop_loan_atm_transaction WHERE loan_type_code = 'BF' ORDER BY transaction_datetime DESC LIMIT 1
					)
					UNION ALL
					(
							SELECT DATE(transaction_datetime) AS transaction_datetime FROM coop_loan_transaction WHERE loan_type_code = 'BF' ORDER BY transaction_datetime DESC LIMIT 1
					)
				) AS t2";
            $date_min = $this->db->query($sql)->row_array();
            $start_date = $date_min['transaction_datetime'];
            $end_date = $get_start_date;
        }else{
            $start_date = $get_start_date;
            $end_date = $get_start_date;
        }

        $where_department = "";
        if(!empty($_GET['group_name'])){
            if(!in_array('all',$_GET['group_name'])){
                $where_department = " AND coop_mem_apply.department IN  (".implode(',',array_filter($_GET['group_name'])).")";
            }
        }

        $where_level = "";
        if(!empty($_GET['level'])){
            $where_level = "WHERE 1=1 AND coop_mem_apply.level in ({$_GET['level']}) ";
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

        $sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`, `coop_mem_apply`.`department`, `coop_mem_apply`.`faction`, `coop_mem_apply`.`level`,
				`coop_prename`.`prename_full`,
				`t2`.`mem_group_id` as `id`, `t1`.`mem_group_name` as `name`,
				`t2`.`mem_group_name` as `sub_name`,
				`t3`.`mem_group_name` as `main_name`,
				`t4`.`share_collect`, `t4`.`share_collect_value`, `t4`.`share_id`, `t4`.`share_period`, `t4`.`share_date`,
				`t5`.`loan_id`, `t5`.`loan_amount_balance`, `t5`.`contract_number`, `t5`.`loan_type`,t5.period_now,
				`t6`.`loan_atm_id`, `t6`.`contract_number` AS `contract_number_atm`, `t6`.`loan_amount_balance_atm`
				,coop_mem_apply.employee_id
				FROM (SELECT IF (
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
							) AS department, member_id, prename_id, firstname_th, lastname_th,member_status, retry_date,employee_id FROM coop_mem_apply {$where_level}) AS coop_mem_apply
				LEFT JOIN `coop_prename` ON `coop_prename`.`prename_id` = `coop_mem_apply`.`prename_id`
				LEFT JOIN `coop_mem_group` as `t1` ON `t1`.`id` = `coop_mem_apply`.`level`
				LEFT JOIN `coop_mem_group` as `t2` ON `t2`.`id` = `t1`.`mem_group_parent_id`
				LEFT JOIN `coop_mem_group` as `t3` ON `t3`.`id` = `t2`.`mem_group_parent_id`
				LEFT JOIN (SELECT t1.share_id,t1.share_collect,t1.share_collect_value,t1.member_id,t1.share_period,t1.share_date FROM coop_mem_share AS t1 INNER JOIN (SELECT member_id,max(share_id) share_id FROM coop_mem_share WHERE share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY member_id) t2 ON t1.member_id=t2.member_id AND t1.share_id=t2.share_id) AS t4 ON `coop_mem_apply`.`member_id` = `t4`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number ,t3.period_now ,t3.loan_type 
								,t1.loan_transaction_id
								,t1.loan_id
								,t1.loan_amount_balance
								,t1.transaction_datetime FROM (SELECT t1.loan_transaction_id,t1.loan_id,t1.loan_amount_balance,t1.transaction_datetime FROM coop_loan_transaction t1 INNER JOIN (
SELECT max(t1.loan_transaction_id) loan_transaction_id,t1.loan_id FROM coop_loan_transaction t1 INNER JOIN (
SELECT loan_id,max(transaction_datetime) transaction_datetime FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY loan_id) t2 ON t1.loan_id=t2.loan_id AND t1.transaction_datetime=t2.transaction_datetime GROUP BY t1.loan_id) t2 ON t1.loan_transaction_id=t2.loan_transaction_id AND t1.loan_id=t2.loan_id
) AS t1 LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND t1.loan_amount_balance > 0 GROUP BY t1.loan_id ORDER BY t1.loan_id DESC ,t1.loan_transaction_id DESC )
								AS t5 ON `coop_mem_apply`.`member_id` = `t5`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number
								,t1.loan_atm_transaction_id
								,t1.loan_atm_id
								,t1.loan_amount_balance as loan_amount_balance_atm
								FROM 
								(SELECT loan_atm_id,MAX(cast(transaction_datetime AS Datetime)) AS max
									FROM
										coop_loan_atm_transaction
									WHERE
										transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
									GROUP BY
										loan_atm_id
								)AS t12
								LEFT JOIN coop_loan_atm_transaction AS t1 ON t12.loan_atm_id = t1.loan_atm_id AND t12.max = t1.transaction_datetime								
								LEFT JOIN coop_loan_atm AS t3 ON t1.loan_atm_id = t3.loan_atm_id WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
								GROUP BY t1.loan_atm_id ORDER BY t1.loan_atm_id DESC ,t1.loan_atm_transaction_id DESC ) AS t6
								ON `coop_mem_apply`.`member_id` = `t6`.`member_id`
				WHERE (`t5`.`loan_id` != '' OR (`t4`.`share_id` != ''  AND t4.share_collect != 0) OR (`t6`.`loan_atm_id` != '' AND t6.loan_amount_balance_atm != 0)) AND ( coop_mem_apply.member_status = 1 OR (coop_mem_apply.member_status <> 3 AND  coop_mem_apply.retry_date > '".$end_date." 23:59:59.000')) {$where_department}
				ORDER BY t2.mem_group_id ASC , coop_mem_apply.member_id ASC";
        $result = $this->db->query($sql)->result_array();

        $member_ids = array_column($result, 'member_id');
        if(@$_GET['dev']=='dev2'){
            print_r($this->db->last_query()); exit;
        }
        if(@$_GET['dev']=='dev'){
            print_r($this->db->last_query()); exit;
        }

        //Get Lastest Loan Information
        $loan_ids = array_column($result, 'loan_id');
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
        //Get Lastest Loan ATM Information
        $loan_atm_ids = array_column($result, 'loan_atm_id');
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
        $run_emergent = 0;
        $run_normal = 0;
        $run_special = 0;
        $check_row = "xx";
        $index = 0;
        $row['data'] = array();
        $allCount = 0;
        //$data_count_all = 0;

        $sql_shares = "SELECT t1.share_id,t1.share_collect,t1.share_collect_value,t1.member_id,t1.share_period,t1.share_date FROM coop_mem_share AS t1 INNER JOIN (
SELECT t1.member_id,max(t1.share_id) share_id FROM coop_mem_share t1 INNER JOIN (SELECT member_id,max(share_date) share_date FROM coop_mem_share WHERE share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY member_id) t2 ON t1.member_id=t2.member_id AND t1.share_date=t2.share_date GROUP BY t1.member_id) t2 ON t1.member_id=t2.member_id AND t1.share_id=t2.share_id";
        $shares = $this->db->query($sql_shares)->result_array();
        $_shares = array();
        //echo $this->db->last_query(); exit;
        foreach ($shares as $key => $share){
            $_shares[$share['member_id']] = $share;
        }
        unset($shares);

        if(@$_GET['dev'] == "share"){
            echo "<pre>";
            print_r($_shares);
            exit;
        }

        foreach($result AS $key2=>$value2){
            $department = $value2['level'];
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

                $allCount += $runno;
                //$data_count_all += $runno;
                $runno = 1;
            }else{
                $runno++;
            }
            $row['data'][$department][$value2['member_id']][$runno] = $value2;

            $row['data'][$department][$value2['member_id']][$runno]['mem_group_id'] = $value2['id'];
            $row['data'][$department][$value2['member_id']][$runno]['mem_group_name_level'] = $value2['name'];
            if($value2->sub_name == 'ไม่ระบุ'){
                $row['data'][$department][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['main_name'];
            }else{
                $row['data'][$department][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['sub_name'];
            }

            $row['data'][$department][$value2['member_id']][$runno]['mem_group_name_main'] = $value2['main_name'];

            //หุ้น
            if ($runno == 1) {
                $row['data'][$department][$value2['member_id']][$runno]['share_period'] = $share_period;
                $row['data'][$department][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
            } else {
                $row['data'][$department][$value2['member_id']][$runno]['share_period'] = "";
                $row['data'][$department][$value2['member_id']][$runno]['share_collect'] = "";
            }

            $row['data'][$department][$value2['member_id']][$runno]['runno'] = @$runno;


            $loan_type_code = @$arr_loan_type_code[$value2['loan_type']];


            if(/*@$loan_type_code == 'emergent' &&*/ @$value2['loan_amount_balance'] != ''
                && in_array($value2['loan_id'],$loan_members) && !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance']) ){
                //เงินกู้ฉุกเฉิน
                if ($runno == 1) {
                    $row['data'][$department][$value2['member_id']][$runno]['loan_period_now'][@$loan_type_code] = @$value2['period_now'];
                    $row['data'][$department][$value2['member_id']][$runno]['loan_contract_number'][@$loan_type_code] = @$value2['contract_number'];
                    $row['data'][$department][$value2['member_id']][$runno]['loan_balance'][@$loan_type_code] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
                } else {
                    for($no_count = 1; $no_count <= $runno; $no_count++) {
                        if (empty($row['data'][$department][$value2['member_id']][$no_count]['contract_number'][@$loan_type_code])) {
                            $row['data'][$department][$value2['member_id']][$no_count]['loan_period_now'][@$loan_type_code] = @$value2['period_now'];
                            $row['data'][$department][$value2['member_id']][$no_count]['loan_contract_number'][@$loan_type_code] = @$value2['contract_number'];
                            $row['data'][$department][$value2['member_id']][$no_count]['loan_balance'][@$loan_type_code] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
                            break;
                        } else if ($row['data'][$department][$value2['member_id']][$no_count]['contract_number'][@$loan_type_code] == $value2['contract_number']) {
                            break;
                        }
                    }
                }
                $run_emergent++;
            }

//            if(@$loan_type_code == 'normal' && @$value2['loan_amount_balance'] != ''
//                && in_array($value2['loan_id'],$loan_members) && !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
//
//
//                if ($runno == 1) {
//                    $row['data'][$department][$value2['member_id']][$runno]['loan_normal_period_now'][@$loan_type_code] = $value2['period_now'];
//                    $row['data'][$department][$value2['member_id']][$runno]['loan_normal_contract_number'][@$loan_type_code] = $value2['contract_number'];
//                    $row['data'][$department][$value2['member_id']][$runno]['loan_normal_balance'][@$loan_type_code] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
//                } else {
//                    for($no_count = 1; $no_count <= $runno; $no_count++) {
//                        if ($row['data'][$department][$value2['member_id']][$no_count]['loan_normal_contract_number'][@$loan_type_code] == $value2['contract_number']) {
//                            break;
//                        } else if (empty($row['data'][$department][$value2['member_id']][$no_count]['loan_normal_contract_number'])) {
//                            $row['data'][$department][$value2['member_id']][$no_count]['loan_normal_period_now'][@$loan_type_code] = @$value2['period_now'];
//                            $row['data'][$department][$value2['member_id']][$no_count]['loan_normal_contract_number'][@$loan_type_code] = @$value2['contract_number'];
//                            $row['data'][$department][$value2['member_id']][$no_count]['loan_normal_balance'][@$loan_type_code] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
//                            break;
//                        }
//                    }
//                }
//                $run_normal++;
//            }
//
//            if(@$loan_type_code == 'special' && @$value2['loan_amount_balance'] != ''
//                && in_array($value2['loan_id'],$loan_members) && !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
//                if ($runno == 1) {
//                    $row['data'][$department][$value2['member_id']][$runno]['loan_special_period_now'][@$loan_type_code] = @$value2['period_now'];
//                    $row['data'][$department][$value2['member_id']][$runno]['loan_special_contract_number'][@$loan_type_code] = @$value2['contract_number'];
//                    $row['data'][$department][$value2['member_id']][$runno]['loan_special_balance'][@$loan_type_code] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
//                } else {
//                    for($no_count = 1; $no_count <= $runno; $no_count++) {
//                        if (empty($row['data'][$department][$value2['member_id']][$no_count]['loan_special_contract_number'])) {
//                            $row['data'][$department][$value2['member_id']][$no_count]['loan_special_period_now'][@$loan_type_code] = @$value2['period_now'];
//                            $row['data'][$department][$value2['member_id']][$no_count]['loan_special_contract_number'][@$loan_type_code] = @$value2['contract_number'];
//                            $row['data'][$department][$value2['member_id']][$no_count]['loan_special_balance'][@$loan_type_code] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
//                            break;
//                        } else if ($row['data'][$department][$value2['member_id']][$no_count]['loan_special_contract_number'][@$loan_type_code] == $value2['contract_number']) {
//                            break;
//                        }
//                    }
//                }
//
//                $run_special++;
//            }

            if(@$value2['loan_amount_balance_atm'] != ''
                && in_array($value2['loan_atm_id'],$loan_atm_members) && !empty($loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'])){
                //เงินกู้ฉุกเฉิน ATM
                $atm_index_count = $runno;
                if(!empty($row['data'][$department][$value2['member_id']][$runno]['loan_atm_contract_number'])) {
                    $atm_index_count = $runno+1;
                }
                for($no_count = 1; $no_count <= $atm_index_count; $no_count++) {
                    if (empty($row['data'][$department][$value2['member_id']][$no_count]['loan_atm_contract_number'])) {

                        if ($no_count > $runno ) {
                            $row['data'][$department][$value2['member_id']][$no_count] = $value2;
                        }
                        $row['data'][$department][$value2['member_id']][$no_count]['mem_group_id'] = $value2['id'];
                        $row['data'][$department][$value2['member_id']][$no_count]['mem_group_name_level'] = $value2['name'];
                        if($value2->sub_name == '' || $value2->sub_name=='ไม่ระบุ'){
                            $row['data'][$department][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['main_name'];
                        }else{
                            $row['data'][$department][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['sub_name'];
                        }

                        $row['data'][$department][$value2['member_id']][$no_count]['mem_group_name_main'] = $value2['main_name'];

                        if ($runno == 1) {
                            $row['data'][$department][$value2['member_id']][$runno]['share_period'] = $share_period;
                            $row['data'][$department][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
                        } else {
                            $row['data'][$department][$value2['member_id']][$runno]['share_period'] = "";
                            $row['data'][$department][$value2['member_id']][$runno]['share_collect'] = "";
                        }

                        $row['data'][$department][$value2['member_id']][$no_count]['runno'] = $no_count;
                        $row['data'][$department][$value2['member_id']][$no_count]['loan_atm_period_now'] = '';
                        $row['data'][$department][$value2['member_id']][$no_count]['loan_atm_contract_number'] = @$value2['contract_number_atm'];
                        $row['data'][$department][$value2['member_id']][$no_count]['loan_atm_balance'] = $loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'];
                        break;
                    } else if ($row['data'][$department][$value2['member_id']][$no_count]['loan_atm_contract_number'] == $value2['contract_number_atm']) {
                        break;
                    }
                }
            }
            $run_index++;
        }
        unset($result);
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
        //$allCount = count($row['data']);

        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['data'] = $row['data'];
        $arr_data['data_count'] = $allCount+1;
        $arr_data['i'] = $i;

        $this->db->select(array('id','loan_type','loan_type_code'));
        $this->db->from('coop_loan_type');
        $this->db->where("loan_type_status = '1'");
        $this->db->order_by("order_by");
        $row = $this->db->get()->result_array();
        $arr_data['loan_type'] = $row;

        $arr_data['month_arr'] = $this->center_function->month_arr();
        $arr_data['month_short_arr'] = $this->center_function->month_short_arr();

        $this->db->select(array('id','mem_group_id','mem_group_name'));
        $this->db->from('coop_mem_group');
        $this->db->where("mem_group_type = '3'");
        $row_mem_group  = $this->db->get()->result_array();
        $arr_mem_group = array();
        $arr_mem_group_id = array();
        foreach($row_mem_group AS $key_group=>$val_group){
            $arr_mem_group[$val_group['id']] = $val_group['mem_group_name'];
            $arr_mem_group_id[$val_group['id']] = $val_group['mem_group_id'];
        }
        $arr_data['arr_mem_group'] = $arr_mem_group;
        $arr_data['arr_mem_group_id'] = $arr_mem_group_id;

        //หาหน้าทั้งหมดในรายงาน
        $runno_group = 0;
        $page_all = 0;
        foreach($arr_data['data']  as $key_d=>$row_d) {
            $index = 0;
            foreach(@$row_d AS $da) {
                $runno_group++;
                foreach(@$da as $key => $row){
                    if (!empty($row['share_collect']) || !empty($row['loan_emergent_balance']) || !empty($row['loan_normal_balance']) || !empty($row['loan_special_balance'])) {
                        if($index == 0 || $index == 24 || ( $index > 24 && (($index-24) % 24) == 0 ) || $runno_group == 1) {
                            $page_all++;
                        }
                        $index++;
                    }
                }
            }
        }
        $arr_data['page_all'] = $page_all;

        //echo $arr_data['data_count'].'<br>'; exit;
        $set_loan_column = array();
        foreach ($arr_data['loan_type'] as $key => $value){
//            if($value['loan_type_code'] != 'atm'){
            $set_loan_column[$value['loan_type_code']] = $value['loan_type_code'];
//            }
        }
        $arr_data['set_loan_column'] = $set_loan_column;

        if($_GET['dev'] == 'arr'){
            echo '<pre>';
            print_r($arr_data);
            exit;
        }
        return $arr_data;
    }


    public function get_data_share_loan_balance(){
        $this->db->select(array('id','loan_type','loan_type_code'));
        $this->db->from('coop_loan_type');
        $this->db->where("loan_type_status = '1'");
        $this->db->order_by("order_by");
        $row = $this->db->get()->result_array();
        $set_loan_type = $row;
        $arr_data['set_loan_type'] = $set_loan_type;
        set_time_limit(-1);
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
            $this->db->from('coop_loan_atm_transaction');
            $this->db->order_by("transaction_datetime ASC");
            $this->db->limit(1);
            $rs_date_loan_atm = $this->db->get()->result_array();
            $date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));

            if($date_share_min < $date_loan_min){
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

        $sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`, `coop_mem_apply`.`department`, `coop_mem_apply`.`faction`, `coop_mem_apply`.`level`,
				
				`t4`.`share_collect`, `t4`.`share_collect_value`, `t4`.`share_id`, `t4`.`share_period`, `t4`.`share_date`,
				`t5`.`loan_id`, `t5`.`loan_amount_balance`, `t5`.`contract_number`, `t5`.`loan_type`,
				`t6`.`loan_atm_id`, `t6`.`contract_number` AS `contract_number_atm`, `t6`.`loan_amount_balance_atm`
				FROM (SELECT IF (
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
							) AS department, member_id, prename_id, firstname_th, lastname_th,member_status FROM coop_mem_apply) AS coop_mem_apply
		
				LEFT JOIN (SELECT coop_mem_share.share_id, coop_mem_share.share_collect, coop_mem_share.share_collect_value, coop_mem_share.member_id, coop_mem_share.share_period, coop_mem_share.share_date FROM (select MAX(share_id) as share_id, member_id from coop_mem_share WHERE coop_mem_share.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY member_id) as temp_share INNER JOIN coop_mem_share ON temp_share.share_id = coop_mem_share.share_id) AS t4 ON `coop_mem_apply`.`member_id` = `t4`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number ,t3.period_now ,t3.loan_type 
								,t1.loan_transaction_id
								,t1.loan_id
								,t1.loan_amount_balance
								,t1.transaction_datetime FROM coop_loan_transaction AS t1 LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY t1.loan_id ORDER BY t1.loan_id DESC ,t1.loan_transaction_id DESC )
								AS t5 ON `coop_mem_apply`.`member_id` = `t5`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number
								,t1.loan_atm_transaction_id
								,t1.loan_atm_id
								,t1.loan_amount_balance as loan_amount_balance_atm
								FROM coop_loan_atm_transaction AS t1 LEFT JOIN coop_loan_atm AS t3 ON t1.loan_atm_id = t3.loan_atm_id WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
								GROUP BY t1.loan_atm_id ORDER BY t1.loan_atm_id DESC ,t1.loan_atm_transaction_id DESC ) AS t6
								ON `coop_mem_apply`.`member_id` = `t6`.`member_id`
				WHERE (`t5`.`loan_id` != '' OR `t4`.`share_id` != '' OR `t6`.`loan_atm_id` != '') AND coop_mem_apply.member_status = 1
				ORDER BY coop_mem_apply.member_id ASC";
        if($_GET['debug'] == "on") {
            echo $sql.'<hr>'; exit;
        }
        $result = $this->db->query($sql)->result_array();

        $member_ids = array_column($result, 'member_id');
        //echo '<pre>'; print_r($member_ids); echo '</pre>'; exit;

        //Get Lastest Loan Information
        $loan_ids = array_unique(array_column($result, 'loan_id'));
        sort($loan_ids);
        $where_loan = " 1=1 ";
        if(sizeof(array_filter($loan_ids))){
            $where_loan = " t1.loan_id IN  (".implode(',',array_filter($loan_ids)).") ";
        }
        $loans = $this->db->query("SELECT `t1`.`loan_transaction_id`, `t1`.`loan_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
									FROM `coop_loan_transaction` as `t1`
									INNER JOIN (SELECT loan_id, MAX(loan_transaction_id) as max FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_id)
											as t2 ON `t1`.`loan_id` = `t2`.`loan_id` AND `t1`.`loan_transaction_id` = `t2`.`max`
									WHERE {$where_loan}
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
									")->result_array();

        if($_GET['dev'] == 'loan'){
            echo "<pre>"; echo $this->db->last_query(); echo "</pre>"; exit;
        }
        $loan_members = array_column($loans, 'loan_id');
        //Get Lastest Loan ATM Information
        $loan_atm_ids = array_unique(array_column($result, 'loan_atm_id'));
        sort($loan_atm_ids);
        $where_atm = " 1=1 ";
        if(sizeof(array_filter($loan_atm_ids))){
            $where_atm = " t1.loan_atm_id IN  (".implode(',',array_filter($loan_atm_ids)).") ";
        }

        if($this->Setting_model->get('report_share_loan_data_setting_loan_atm') == '1'){
            $atm_end_date = date('Y-m-d', strtotime($end_date. ' -1 DAY'));
        }else{
            $atm_end_date = $end_date;
        }

        $atm_sql = "SELECT t1.loan_atm_transaction_id, `t1`.`loan_atm_id`, `t1`.`transaction_datetime`, t1.loan_amount_balance AS loan_amount_balance
        FROM `coop_loan_atm_transaction` as `t1`
        INNER JOIN (SELECT max(loan_atm_transaction_id) loan_atm_transaction_id, loan_atm_id FROM coop_loan_atm_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$atm_end_date." 23:59:59' GROUP BY loan_atm_id ) as t2 ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id` AND `t1`.`loan_atm_transaction_id` = `t2`.`loan_atm_transaction_id`
        LEFT JOIN `coop_loan_atm_detail` AS `t3` ON `t1`.`loan_atm_id` = `t3`.`loan_atm_id`	AND `t1`.`transaction_datetime` = `t3`.`loan_date`
        LEFT JOIN `coop_finance_transaction` AS `t4` ON `t1`.`receipt_id` = `t4`.`receipt_id`	AND `t1`.`loan_atm_id` = `t4`.`loan_atm_id`
        LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
        WHERE {$where_atm}
        GROUP BY `t1`.`loan_atm_id`
        ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_atm_transaction_id` DESC";

        $loan_atms = $this->db->query($atm_sql)->result_array();

        if($_GET['dev'] == "atm"){
            echo "<pre>".$atm_sql; exit;
        }

        $atm_today = "SELECT * FROM coop_loan_atm_transaction WHERE transaction_datetime between '{$end_date} 00:00:00' AND '{$end_date} 23:59:59' AND loan_type_code <> 'AT'";
        $loan_atm_today = $this->db->query($atm_today)->result_array();
        $payment_atm_today = array_column($loan_atm_today, 'loan_amount_balance', 'loan_atm_id');

        $loan_atm_members = array_column($loan_atms, 'loan_atm_id');
        //echo '<pre>'; print_r($loan_atms); echo '</pre>'; exit;
        $run_index = 0;
        $row = array();

        $check_row = "xx";
        $index = 0;

        foreach($result AS $key2=>$value2){

            if($check_row != @$value2['member_id']){
                $check_row = @$value2['member_id'];
                $sql_shares = "SELECT 
									t1.member_id
									,t1.share_period
									,t1.share_collect_value
									,t1.share_status
									,t1.share_payable_value
									,'1' AS check_share
								FROM coop_mem_share AS t1
								WHERE t1.member_id = '".$value2['member_id']."'
								AND t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
								AND t1.share_status <> 0
								ORDER BY t1.share_date DESC ,t1.share_id DESC
								LIMIT 1";
                //echo $sql_shares.'<br>';
                $shares = $this->db->query($sql_shares)->row_array();
                $share_period = (!empty($shares['share_period']))?@$shares['share_period']: "";

                //echo $check_share .'<br>';
                if(@$shares['share_status'] == 3){
                    $share_collect_value = (!empty($shares['share_payable_value']))?@$shares['share_payable_value']: "";
                }else{
                    $share_collect_value = (!empty($shares['share_collect_value']))?@$shares['share_collect_value']: "";
                }
                $check_share = (!empty($shares['check_share']) && !empty($share_collect_value))?@$shares['check_share']: "";

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
            $row['data'][$value2['member_id']][$runno]['department'] = $value2['department'];
            $row['data'][$value2['member_id']][$runno]['faction'] = $value2['faction'];
            $row['data'][$value2['member_id']][$runno]['level'] = $value2['level'];
            $row['data'][$value2['member_id']][$runno]['loan_type'] = $value2['loan_type'];
            if($row_mem_group_level->sub_name == '' || $value2->sub_name=='ไม่ระบุ'){
                $row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['main_name'];
            }else{
                $row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['sub_name'];
            }

            $row['data'][$value2['member_id']][$runno]['mem_group_name_main'] = $value2['main_name'];

            //หุ้น
            if ($runno == 1) {
                $row['data'][$value2['member_id']][$runno]['share_period'] = $share_period;
                $row['data'][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
                $row['data'][$value2['member_id']][$runno]['check_share'] = $check_share;
            } else {
                $row['data'][$value2['member_id']][$runno]['share_period'] = "";
                $row['data'][$value2['member_id']][$runno]['share_collect'] = "";
                $row['data'][$value2['member_id']][$runno]['check_share'] = "";
            }
            $row['data'][$value2['member_id']][$runno]['runno'] = $runno;

            $loan_type_code = @$arr_loan_type_code[$value2['loan_type']];

            if(@$value2['loan_amount_balance'] != '' && in_array($value2['loan_id'],$loan_members)
                && $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance']){
                //เงินกู้ฉุกเฉิน
                if ($runno == 1) {
                    $row['data'][$value2['member_id']][$runno]['loan_period_now'][$loan_type_code] = @$value2['period_now'];
                    $row['data'][$value2['member_id']][$runno]['loan_contract_number'][$loan_type_code] = @$value2['contract_number'];
                    $row['data'][$value2['member_id']][$runno]['loan_balance'][$loan_type_code] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
                    $row['data'][$value2['member_id']][$runno]['check'][$loan_type_code] = 1;
                    // if($value2['loan_type']==16 && @$_GET['debug']=="ken"){
                    //     echo "<tr><td>".@$value2['contract_number']. "</td><td> ".$row['data'][$value2['member_id']][$runno]['loan_balance'][$loan_type_code]."</td></tr><br>";
                    // }
                } else {
                    for($no_count = 1; $no_count <= $runno; $no_count++) {
                        if (empty($row['data'][$value2['member_id']][$no_count]['loan_contract_number'][$loan_type_code])) {
                            $row['data'][$value2['member_id']][$no_count]['loan_period_now'][$loan_type_code] = @$value2['period_now'];
                            $row['data'][$value2['member_id']][$no_count]['loan_contract_number'][$loan_type_code] = @$value2['contract_number'];
                            $row['data'][$value2['member_id']][$no_count]['loan_balance'][$loan_type_code] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
                            $row['data'][$value2['member_id']][$no_count]['check'][$loan_type_code] = 1;
                            // if($value2['loan_type']==16 && @$_GET['debug']=="ken"){
                            //     echo "<tr><td>".@$value2['contract_number']."</td><td> ".$row['data'][$value2['member_id']][$no_count]['loan_balance'][$loan_type_code]."</td></tr><br>";
                            // }
                            break;
                        } else if ($row['data'][$value2['member_id']][$no_count]['loan_contract_number'][$loan_type_code] == $value2['contract_number']) {
                            break;
                        }
                    }
                }
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
                        if($row_mem_group_level->sub_name == '' || $value2->sub_name=='ไม่ระบุ'){
                            $row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['main_name'];
                        }else{
                            $row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['sub_name'];
                        }

                        $row['data'][$value2['member_id']][$no_count]['mem_group_name_main'] = $value2['main_name'];

                        $row['data'][$value2['member_id']][$runno]['department'] = $value2['department'];
                        $row['data'][$value2['member_id']][$runno]['faction'] = $value2['faction'];
                        $row['data'][$value2['member_id']][$runno]['level'] = $value2['level'];
                        $row['data'][$value2['member_id']][$runno]['loan_type'] = $value2['loan_type'];

                        //หุ้น
                        if ($runno == 1) {
                            $row['data'][$value2['member_id']][$runno]['share_period'] = $share_period;
                            $row['data'][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
                            $row['data'][$value2['member_id']][$runno]['check_share'] = $check_share;
                        } else {
                            $row['data'][$value2['member_id']][$runno]['share_period'] = "";
                            $row['data'][$value2['member_id']][$runno]['share_collect'] = "";
                            $row['data'][$value2['member_id']][$runno]['check_share'] = "";
                        }

                        $row['data'][$value2['member_id']][$no_count]['runno'] = $runno;
                        $row['data'][$value2['member_id']][$no_count]['loan_atm_period_now'] = '';
                        $row['data'][$value2['member_id']][$no_count]['loan_atm_contract_number'] = @$value2['contract_number_atm'];
                        $row['data'][$value2['member_id']][$no_count]['loan_atm_balance'] = isset($payment_atm_today[$value2['loan_atm_id']]) ? $payment_atm_today[$value2['loan_atm_id']] : $loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'];
                        $row['data'][$value2['member_id']][$no_count]['check_atm'] = 1;
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
                $data_arr["check_normal"] = 1;
                $row['data'][$fund["member_id"]][] = $data_arr;
            }
        }

        $data_tmp = $row['data'];

        $data_tmp_sum = array();
        foreach($data_tmp AS $key=>$value){

            foreach($value AS $key2=>$value2){
                if(@$_GET['type_department'] == '1'){
                    $data_tmp_sum[@$value2['department']]['share_person'] += !empty($value2['check_share']) ? $value2['check_share'] : 0;
                    $data_tmp_sum[@$value2['department']]['share_collect'] += !empty($value2['share_collect']) ? $value2['share_collect'] : 0;

                    $data_tmp_sum[@$value2['department']]['loan_atm_person'] += !empty($value2['check_atm']) ? $value2['check_atm'] : 0;
                    $data_tmp_sum[@$value2['department']]['loan_atm_balance'] += !empty($value2['loan_atm_balance']) ? $value2['loan_atm_balance'] : 0;

                    foreach ($set_loan_type as $key_loan_type => $loan_type) {
                        $data_tmp_sum[@$value2['department']]['loan_person'][$loan_type['loan_type_code']] += !empty($value2['check'][$loan_type['loan_type_code']]) ? $value2['check'][$loan_type['loan_type_code']] : 0;
                        $data_tmp_sum[@$value2['department']]['loan_balance'][$loan_type['loan_type_code']] += !empty($value2['loan_balance'][$loan_type['loan_type_code']]) ? $value2['loan_balance'][$loan_type['loan_type_code']] : 0;

                    }
                }else if(@$_GET['type_department'] == '2' ){
                    $data_tmp_sum[@$value2['level']]['share_person'] += !empty($value2['check_share']) ? $value2['check_share'] : 0;
                    $data_tmp_sum[@$value2['level']]['share_collect'] += !empty($value2['share_collect']) ? $value2['share_collect'] : 0;

                    $data_tmp_sum[@$value2['level']]['loan_atm_person'] += !empty($value2['check_atm']) ? $value2['check_atm'] : 0;
                    $data_tmp_sum[@$value2['level']]['loan_atm_balance'] += !empty($value2['loan_atm_balance']) ? $value2['loan_atm_balance'] : 0;

                    foreach (@$set_loan_type as $key_loan_type => $loan_type) {
                        $data_tmp_sum[@$value2['level']]['loan_person'][$loan_type['loan_type_code']] += !empty($value2['check'][$loan_type['loan_type_code']]) ? $value2['check'][$loan_type['loan_type_code']] : 0;
                        $data_tmp_sum[@$value2['level']]['loan_balance'][$loan_type['loan_type_code']] += !empty($value2['loan_balance'][$loan_type['loan_type_code']]) ? $value2['loan_balance'][$loan_type['loan_type_code']] : 0;

                        $data_tmp_sum[@$value2['level']]['loan_balance_subdivision'] += !empty($value2['loan_balance'][$loan_type['loan_type_code']]) ? @$value2['loan_balance'][$loan_type['loan_type_code']] : 0;
                    }
                }
            }
        }

        $x=0;
        $join_arr = array();

        $where = "";
        $field_department = "department";
        if(@$_GET['type_department'] == '1'){
            $where .= " AND coop_mem_group.mem_group_type = '1'";

        }else if(@$_GET['type_department'] == '2'){
            $where .= " AND coop_mem_group.mem_group_type = '3'";
            $field_department = "level";
        }

        $this->paginater_all_preview->type(DB_TYPE);
        $this->paginater_all_preview->select(array(
            'coop_mem_group.id',
            'coop_mem_group.mem_group_id',
            'coop_mem_group.mem_group_parent_id',
            'coop_mem_group.mem_group_name'
        ));
        $this->paginater_all_preview->main_table('coop_mem_group');
        $this->paginater_all_preview->where("{$where}");
        $this->paginater_all_preview->page_now(@$_GET["page"]);
        $this->paginater_all_preview->per_page(20);
        $this->paginater_all_preview->page_link_limit(28);
        $this->paginater_all_preview->page_limit_first(22);
        $this->paginater_all_preview->order_by('coop_mem_group.mem_group_id');
        $this->paginater_all_preview->join_arr($join_arr);

        $row = $this->paginater_all_preview->paginater_process();
        foreach($row['data'] AS $key=>$value){
            $check_group_id_sub = 'xx';
            foreach($value AS $key2=>$value2){
//				echo '<pre>'; print_r($row); echo '</pre>';
                //หุ้น
                $row['data'][$key][$key2]['share_person'] = @$data_tmp_sum[$value2['id']]['share_person'];
                $row['data'][$key][$key2]['share_collect'] = @$data_tmp_sum[$value2['id']]['share_collect'];
                //เงินกู้ฉุกเฉิน
                $row['data'][$key][$key2]['loan_atm_person'] = @$data_tmp_sum[$value2['id']]['loan_atm_person'];
                $row['data'][$key][$key2]['loan_atm_balance'] = @$data_tmp_sum[$value2['id']]['loan_atm_balance'];

                foreach (@$set_loan_type as $key_loan_type => $loan_type) {
                    $row['data'][$key][$key2]['loan_person'][$loan_type['loan_type_code']] = @$data_tmp_sum[$value2['id']]['loan_person'][$loan_type['loan_type_code']];
                    $row['data'][$key][$key2]['loan_balance'][$loan_type['loan_type_code']] = @$data_tmp_sum[$value2['id']]['loan_balance'][$loan_type['loan_type_code']];
                }

                //รวมเงินกู้คงเหลือ
                $row['data'][$key][$key2]['total_loan_balance'] = 0;
                foreach (@$set_loan_type as $key_loan_type => $loan_type) {
                    if($loan_type['loan_type_code'] != 'atm'){
                        $row['data'][$key][$key2]['total_loan_balance'] += @$data_tmp_sum[$value2['id']]['loan_balance'][$loan_type['loan_type_code']];
                    }
                }

                $this->db->select(array('coop_mem_group.id','coop_mem_group.mem_group_id','coop_mem_group.mem_group_parent_id','coop_mem_group.mem_group_name'));
                $this->db->from('coop_mem_group');
                $this->db->where("coop_mem_group.id = '".@$value2['mem_group_parent_id']."'");
                $this->db->limit(1);
                $rs_mem_group_sub = $this->db->get()->result_array();
                $row_mem_group_sub = @$rs_mem_group_sub[0];
                if(@$row_mem_group_sub['mem_group_name'] == '' || @$row_mem_group_sub['mem_group_name']=='ไม่ระบุ'){
                    $this->db->select(array('coop_mem_group.id','coop_mem_group.mem_group_id','coop_mem_group.mem_group_parent_id','coop_mem_group.mem_group_name'));
                    $this->db->from('coop_mem_group');
                    $this->db->where("coop_mem_group.id = '".@$row_mem_group_sub['mem_group_parent_id']."'");
                    $this->db->limit(1);
                    $rs_mem_group_main = $this->db->get()->result_array();

                    $row['data'][$key][$key2]['mem_group_name_main'] = @$rs_mem_group_main[0]['mem_group_name'];
                    $mem_group_id_sub = @$rs_mem_group_main[0]['id'];
                    $row['data'][$key][$key2]['share_balance_subdivision'] = $row['data'][$key][$key2]['share_collect'];
                    $row['data'][$key][$key2]['loan_balance_subdivision'] = $row['data'][$key][$key2]['total_loan_balance'];
                }else{
                    $mem_group_id_sub = @$value2['mem_group_parent_id'];
                    $row['data'][$key][$key2]['mem_group_name_main'] = @$row_mem_group_sub['mem_group_name'];
                    $row['data'][$key][$key2]['share_balance_subdivision'] = @$data_tmp_sum[$mem_group_id_sub]['share_balance_subdivision'];
                    $row['data'][$key][$key2]['loan_balance_subdivision'] = @$data_tmp_sum[$mem_group_id_sub]['loan_balance_subdivision'];
                }

                if($mem_group_id_sub == $check_group_id_sub){
                    $run_sub++;
                }else{
                    $check_group_id_sub = $mem_group_id_sub;
                }

                $arr_data['arr_run_sub'][@$mem_group_id_sub] = @$run_sub;

                $row['data'][$key][$key2]['run_sub'] = @$run_sub;
                $row['data'][$key][$key2]['mem_group_id_sub'] = @$mem_group_id_sub;
            }
        }

        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['paging'] = $paging;
        $arr_data['data'] = $row['data'];
        $arr_data['page_all'] = $row['page_all'];

        $this->db->select(array('id','loan_type','loan_type_code'));
        $this->db->from('coop_loan_type');
        $this->db->order_by("order_by");
        $row = $this->db->get()->result_array();
        $arr_data['loan_type'] = $row;

        $arr_data['month_arr'] = $this->center_function->month_arr();
        $arr_data['month_short_arr'] = $this->center_function->month_short_arr();
        return $arr_data;
    }

    function get_report_share_loan_balance_level_preview(){

        ini_set('memory_limit', -1);
        set_time_limit(-1);
        //$this->db->save_queries = FALSE;
        if(@$_GET['start_date']){
            $start_date_arr = explode('/',urldecode(@$_GET['start_date']));
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
            //echo $this->db->last_query(); exit;
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
                //echo "1";exit;
                $start_date = $date_loan_transaction_min;
            }else if($date_share_min < $date_loan_min){
                //echo "2";exit;
                $start_date = $date_share_min;
            }else if($date_loan_min < $date_loan_atm_min){
                //echo "3";exit;
                $start_date = $date_loan_min;
            }else if($date_loan_atm_min < $date_share_min){
                //echo "4";exit;
                $start_date = $date_loan_atm_min;
            }else{
                //echo "5";exit;
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

        $sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`, `coop_mem_apply`.`department`, `coop_mem_apply`.`faction`, `coop_mem_apply`.`level`,
				`coop_prename`.`prename_full`,
				`t2`.`mem_group_id` as `id`, `t1`.`mem_group_name` as `name`,
				`t2`.`mem_group_name` as `sub_name`,
				`t3`.`mem_group_name` as `main_name`,
				`t4`.`share_collect`, `t4`.`share_collect_value`, `t4`.`share_id`, `t4`.`share_period`, `t4`.`share_date`,
				`t5`.`loan_id`, `t5`.`loan_amount_balance`, `t5`.`contract_number`, `t5`.`loan_type`,t5.period_now,
				`t6`.`loan_atm_id`, `t6`.`contract_number` AS `contract_number_atm`, `t6`.`loan_amount_balance_atm`
				FROM (SELECT IF (
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
							) AS department, member_id, prename_id, firstname_th, lastname_th,member_status, retry_date FROM coop_mem_apply) AS coop_mem_apply
				LEFT JOIN `coop_prename` ON `coop_prename`.`prename_id` = `coop_mem_apply`.`prename_id`
				LEFT JOIN `coop_mem_group` as `t1` ON `t1`.`id` = `coop_mem_apply`.`level`
				LEFT JOIN `coop_mem_group` as `t2` ON `t2`.`id` = `t1`.`mem_group_parent_id`
				LEFT JOIN `coop_mem_group` as `t3` ON `t3`.`id` = `t2`.`mem_group_parent_id`
				LEFT JOIN (SELECT t1.share_id,t1.share_collect,t1.share_collect_value,t1.member_id,t1.share_period,t1.share_date FROM coop_mem_share AS t1 INNER JOIN (SELECT member_id,max(share_id) share_id FROM coop_mem_share WHERE share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY member_id) t2 ON t1.member_id=t2.member_id AND t1.share_id=t2.share_id) AS t4 ON `coop_mem_apply`.`member_id` = `t4`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number ,t3.period_now ,t3.loan_type 
								,t1.loan_transaction_id
								,t1.loan_id
								,t1.loan_amount_balance
								,t1.transaction_datetime FROM (SELECT t1.loan_transaction_id,t1.loan_id,t1.loan_amount_balance,t1.transaction_datetime FROM coop_loan_transaction t1 INNER JOIN (
				SELECT max(t1.loan_transaction_id) loan_transaction_id,t1.loan_id FROM coop_loan_transaction t1 INNER JOIN (
				SELECT loan_id,max(transaction_datetime) transaction_datetime FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY loan_id) t2 ON t1.loan_id=t2.loan_id AND t1.transaction_datetime=t2.transaction_datetime GROUP BY t1.loan_id) t2 ON t1.loan_transaction_id=t2.loan_transaction_id AND t1.loan_id=t2.loan_id
				) AS t1 LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND t1.loan_amount_balance > 0 GROUP BY t1.loan_id ORDER BY t1.loan_id DESC ,t1.loan_transaction_id DESC )
												AS t5 ON `coop_mem_apply`.`member_id` = `t5`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number
								,t1.loan_atm_transaction_id
								,t1.loan_atm_id
								,t1.loan_amount_balance as loan_amount_balance_atm
								FROM coop_loan_atm_transaction AS t1 LEFT JOIN coop_loan_atm AS t3 ON t1.loan_atm_id = t3.loan_atm_id WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
								GROUP BY t1.loan_atm_id ORDER BY t1.loan_atm_id DESC ,t1.loan_atm_transaction_id DESC ) AS t6
								ON `coop_mem_apply`.`member_id` = `t6`.`member_id`
				WHERE (`t5`.`loan_id` != '' OR `t4`.`share_id` != '' OR `t6`.`loan_atm_id` != '') AND ( coop_mem_apply.member_status = 1 OR (coop_mem_apply.member_status <> 3 AND  coop_mem_apply.retry_date > '".$end_date." 23:59:59.000'))
				ORDER BY t2.mem_group_id ASC , coop_mem_apply.member_id ASC";
        $result = $this->db->query($sql)->result_array();

        $member_ids = array_column($result, 'member_id');
        if(@$_GET['dev']=='dev2'){
            print_r($this->db->last_query()); exit;
        }
        if(@$_GET['dev']=='dev'){
            print_r($this->db->last_query()); exit;
        }

        //Get Lastest Loan Information
        $loan_ids = array_column($result, 'loan_id');
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
        //echo $this->db->last_query();exit;
        //Get Lastest Loan ATM Information
        $loan_atm_ids = array_column($result, 'loan_atm_id');
        $where_atm = " 1=1 ";
        if(sizeof(array_filter($loan_atm_ids))){
            $where_atm = " t1.loan_atm_id IN  (".implode(',',array_filter($loan_atm_ids)).") ";
        }

        $sql_atm = "SELECT t1.loan_atm_transaction_id, `t1`.`loan_atm_id`, `t1`.`transaction_datetime`,
        t1.loan_amount_balance AS loan_amount_balance
        FROM `coop_loan_atm_transaction` as `t1`
        INNER JOIN (SELECT loan_atm_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_atm_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_atm_id)
                as t2 ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id` AND `t1`.`transaction_datetime` = `t2`.`max`
        LEFT JOIN `coop_loan_atm_detail` AS `t3` ON `t1`.`loan_atm_id` = `t3`.`loan_atm_id`	AND `t1`.`transaction_datetime` = `t3`.`loan_date`
        LEFT JOIN `coop_finance_transaction` AS `t4` ON `t1`.`receipt_id` = `t4`.`receipt_id`	AND `t1`.`loan_atm_id` = `t4`.`loan_atm_id`
        LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
        WHERE ".$where_atm."
        GROUP BY `t1`.`loan_atm_id`
        ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_atm_transaction_id` DESC";

        $loan_atms = $this->db->query($sql_atm)->result_array();

        $loan_atm_members = array_column($loan_atms, 'loan_atm_id');
        if($_GET['dev'] == 'atm') {
            echo $sql_atm; exit;
        }
        $run_index = 0;

        $check_row = "xx";
        $index = 0;
        $row['data'] = array();
        $allCount = 0;

        $sql_shares = "SELECT t1.share_id,t1.share_collect,t1.share_collect_value,t1.member_id,t1.share_period,t1.share_date FROM coop_mem_share AS t1 INNER JOIN (
		SELECT t1.member_id,max(t1.share_id) share_id FROM coop_mem_share t1 INNER JOIN (SELECT member_id,max(share_date) share_date FROM coop_mem_share WHERE share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY member_id) t2 ON t1.member_id=t2.member_id AND t1.share_date=t2.share_date GROUP BY t1.member_id) t2 ON t1.member_id=t2.member_id AND t1.share_id=t2.share_id";
        $shares = $this->db->query($sql_shares)->result_array();
        $_shares = array();
        //echo $this->db->last_query(); exit;
        foreach ($shares as $key => $share){
            $_shares[$share['member_id']] = $share;
        }
        unset($shares);

        if(@$_GET['dev'] == "share"){
            echo "<pre>";
            print_r($_shares);
            exit;
        }

        foreach($result AS $key2=>$value2){
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

                $allCount += $runno;
                $runno = 1;
            }else{
                $runno++;
            }
            $row['data'][$value2['member_id']][$runno] = $value2;

            $row['data'][$value2['member_id']][$runno]['mem_group_id'] = $value2['id'];
            $row['data'][$value2['member_id']][$runno]['mem_group_name_level'] = $value2['name'];
            if($value2->sub_name == 'ไม่ระบุ'){
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

            $row['data'][$value2['member_id']][$runno]['runno'] = @$runno;

            $loan_type_code = @$arr_loan_type_code[$value2['loan_type']];

            if(@$loan_type_code == 'emergent' && @$value2['loan_amount_balance'] != ''
                && in_array($value2['loan_id'],$loan_members) && !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance']) ){
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
            }

            if(@$loan_type_code == 'normal' && @$value2['loan_amount_balance'] != ''
                && in_array($value2['loan_id'],$loan_members) && !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
                if ($runno == 1) {
                    $row['data'][$value2['member_id']][$runno]['loan_normal_period_now'] = $value2['period_now'];
                    $row['data'][$value2['member_id']][$runno]['loan_normal_contract_number'] = $value2['contract_number'];
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

            if(@$loan_type_code == 'special' && @$value2['loan_amount_balance'] != ''
                && in_array($value2['loan_id'],$loan_members) && !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
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

            if(@$value2['loan_amount_balance_atm'] != ''
                && in_array($value2['loan_atm_id'],$loan_atm_members) && !empty($loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'])){
                //เงินกู้ฉุกเฉิน ATM
                $atm_index_count = $runno;
                if(!empty($row['data'][$value2['member_id']][$runno]['loan_emergent_atm_contract_number'])) {
                    $atm_index_count = $runno+1;
                }
                for($no_count = 1; $no_count <= $atm_index_count; $no_count++) {
                    if (empty($row['data'][$value2['member_id']][$no_count]['loan_emergent_atm_contract_number'])) {

                        if ($no_count > $runno ) {
                            $row['data'][$value2['member_id']][$no_count] = $value2;
                        }
                        $row['data'][$value2['member_id']][$no_count]['mem_group_id'] = $value2['id'];
                        $row['data'][$value2['member_id']][$no_count]['mem_group_name_level'] = $value2['name'];
                        if($value2->sub_name == '' || $value2->sub_name=='ไม่ระบุ'){
                            $row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['main_name'];
                        }else{
                            $row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['sub_name'];
                        }

                        $row['data'][$value2['member_id']][$no_count]['mem_group_name_main'] = $value2['main_name'];

                        if ($runno == 1) {
                            $row['data'][$value2['member_id']][$runno]['share_period'] = $share_period;
                            $row['data'][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
                        } else {
                            $row['data'][$value2['member_id']][$runno]['share_period'] = "";
                            $row['data'][$value2['member_id']][$runno]['share_collect'] = "";
                        }

                        $row['data'][$value2['member_id']][$no_count]['runno'] = $no_count;
                        $row['data'][$value2['member_id']][$no_count]['loan_emergent_atm_period_now'] = '';
                        $row['data'][$value2['member_id']][$no_count]['loan_emergent_atm_contract_number'] = @$value2['contract_number_atm'];
                        $row['data'][$value2['member_id']][$no_count]['loan_emergent_atm_balance'] = $loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'];
                        break;
                    } else if ($row['data'][$value2['member_id']][$no_count]['loan_emergent_atm_contract_number'] == $value2['contract_number_atm']) {
                        break;
                    }
                }
            }
            $run_index++;
        }
        unset($result);

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
        //$allCount = count($row['data']);
        $new_data = array();
        $tmp_row = $row['data'];

        foreach($tmp_row as $key => $value){
            foreach ($value as $k => $v) {
                if($v['level']==128){
                    // echo $v['level']." ".$v['name']." ".$v['member_id']. " + ".$v['share_collect_value'] ."<br>";
                }

                $new_data[$v['level']]['mem_group_name_level'] 	= $v['mem_group_name_level'];
                $new_data[$v['level']]['mem_group_count'] 		+= @(!in_array($v['member_id'], $new_data[$v['level']]['member_list'])) ? 1 : 0;
                $new_data[$v['level']]['member_list']			= $v['member_id'];
                $new_data[$v['level']]['sub_name'] 				= $v['sub_name'];
                $new_data[$v['level']]['main_name'] 			= $v['main_name'];
                $new_data[$v['level']]['share_collect'] 		+= $v['share_collect'];
                $new_data[$v['level']]['share_collect_value'] 	+= $v['share_collect_value'];
                $new_data[$v['level']]['loan_emergent_count']	+= ($v['loan_emergent_balance']>0) ? 1 : 0;
                $new_data[$v['level']]['loan_emergent_balance'] += $v['loan_emergent_balance'];
                $new_data[$v['level']]['loan_normal_count']		+= ($v['loan_normal_balance']>0) ? 1 : 0;
                $new_data[$v['level']]['loan_normal_balance'] 	+= $v['loan_normal_balance'];
                $new_data[$v['level']]['loan_special_count']	+= ($v['loan_special_balance']>0) ? 1 : 0;
                $new_data[$v['level']]['loan_special_balance'] 	+= $v['loan_special_balance'];
            }

        }


        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['data'] = $new_data;
        $arr_data['data_count'] = $allCount+1;
        $arr_data['i'] = $i;
        // echo "<pre>";
        // var_dump($arr_data);
        // exit;
        $this->db->select(array('id','loan_type','loan_type_code'));
        $this->db->from('coop_loan_type');
        $this->db->order_by("order_by");
        $row = $this->db->get()->result_array();
        $arr_data['loan_type'] = $row;

        $arr_data['month_arr'] = $this->center_function->month_arr();
        $arr_data['month_short_arr'] = $this->center_function->month_short_arr();

        return $arr_data;

    }

}