<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');


class Report_processor_data_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_coop_report_charged_department_preview(){
        if (!empty($_GET["mem_type"]) && in_array("all", $_GET["mem_type"])){
			$_GET['mem_type'] = '';
		}
		if (empty($_GET['department'])) $_GET['department'] = '';
		if (empty($_GET['faction'])) $_GET['faction'] = '';
		if (empty($_GET['level'])) $_GET['level'] = '';

		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;

		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;

		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
        return $arr_data;
    }

    public function get_coop_report_charged_department_preview_2(){
        if (!empty($_GET["mem_type"]) && in_array("all", $_GET["mem_type"])){
			$_GET['mem_type'] = '';
		}
		if (empty($_GET['department'])) $_GET['department'] = '';
		if (empty($_GET['faction'])) $_GET['faction'] = '';
		if (empty($_GET['level'])) $_GET['level'] = '';

		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;

		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;

		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
        return $arr_data;
    }

    public function get_coop_report_charged_person_preview($arr_data) {
		//Set condition from URL
		$member_where = "";
		if (!empty($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
			$member_where .= " AND mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
		}
		if(@$_GET['department']!=''){
			$member_where .= " AND IF (
				t4.department_old IS NULL,
				t3.department,
				t4.department_old
			) = '".$_GET['department']."'";
		}
		if(@$_GET['faction']!=''){
			$member_where .= " AND faction = '".$_GET['faction']."'";
		}
		if(@$_GET['level']!=''){
			$member_where .= " AND level = '".$_GET['level']."'";
		}

		$loan_where = "1=1";
		if ($_GET['term_of_loan']) {
			$loan_where .= " AND t1.loan_type = ".$_GET['term_of_loan'];
		}

		$month = $_GET['month'];
		$year = $_GET['year'];

		//Get Loan type info
		$loan_types = $this->db->select("coop_loan_type.id, coop_loan_type.loan_type_code, coop_loan_name.loan_name_id")
								->from("coop_loan_name")
								->join("coop_loan_type", "coop_loan_name.loan_type_id = coop_loan_type.id", "inner")
								->get()->result_array();
		$loanNameIds = array_column($loan_types, 'loan_name_id');

		$date_start = date( 'Y-m-t',strtotime(($year-543)."-".sprintf("%02d",@$month)."-01"));
		$member_groups = $this->db->select(array('t1.mem_group_id'
										,'t1.mem_group_name'
										,'t1.id'
                                        ,'t1.lv'
										,'t2.member_id'
										,'t2.firstname_th'
										,'t2.lastname_th'
										,'t3.prename_full'
                                        ,'t2.employee_id'
									))
					->from('(SELECT t2.id as `lv`, t1.id , t1.mem_group_id, t2.mem_group_name as mem_group_name FROM coop_mem_group t1
							inner join coop_mem_group t2 ON t1.mem_group_parent_id=t2.id
							inner join coop_mem_group t3 ON t2.mem_group_parent_id=t3.id) as t1')
					->join("(SELECT
					                t3.employee_id,
									t3.member_id,
									IF(t4.level_old IS NULL, t3.LEVEL, t4.level_old) AS LEVEL,
									t3.firstname_th,
									t3.lastname_th,
									t3.prename_id,
									IF (
										t4.department_old IS NULL,
										t3.department,
										t4.department_old
									) AS department
								FROM
									coop_mem_apply AS t3
							LEFT JOIN (
								SELECT
									member_id,
									department_old,
									faction_old,
									level_old,
									date_move
								FROM
									coop_mem_group_move
								WHERE date_move >= '".$date_start."'
								GROUP BY member_id
								ORDER BY date_move ASC
							) AS t4 ON t3.member_id = t4.member_id
							WHERE 1=1 AND member_status <> 3 ".$member_where.") as t2", "t1.id = t2.level", "inner")
					->join('coop_prename as t3', 't2.prename_id = t3.prename_id', "left")
					->order_by('t1.lv, t2.employee_id')
					->get()->result_array();

		//echo $this->db->last_query(); exit;
		$member_ids = array_column($member_groups, 'member_id');

        $where_member = " 1=1 ";
        if(sizeof(array_filter($member_ids))){
            $where_member = " member_id IN (".implode(',', array_map(function($val){ return sprintf("'%s'", $val); }, $member_ids)).")";
        }

		//echo $this->db->last_query(); exit;
		$infoDatas = $this->db->select(array(
												't1.loan_type',
												't1.contract_number',
												't3.member_id',
												't3.deduct_code',
												't3.loan_id',
												't3.loan_atm_id',
												't3.pay_type',
												't3.deposit_account_id',
												't4.contract_number as contract_number_atm',
												"t3.pay_amount as sum_pay_amount"
											))
								->from("(SELECT * FROM coop_finance_month_detail WHERE {$where_member} ) as t3")
								->join("coop_finance_month_profile as t2","t2.profile_month = '".$month."' AND t2.profile_year = '".$year."' AND t3.profile_id = t2.profile_id","inner")
								->join("(SELECT * FROM coop_loan WHERE ".$loan_where.") as t1","t1.id = t3.loan_id", "left")
								->join("coop_loan_atm as t4","t4.loan_atm_id = t3.loan_atm_id", "left")
								->group_by("t3.deduct_code, t3.member_id, t3.pay_type, t3.loan_id, t3.loan_atm_id, t3.deposit_account_id")
								->order_by("t3.member_id")
								->get()->result_array();
								//"SUM(t3.pay_amount) as sum_pay_amount"

		$info_members = array_column($infoDatas, 'member_id');
		//echo $this->db->last_query(); exit;
		$total_data = array();
        $group_total_data = array();
		$datas = array();

		foreach($member_groups as $key => $member_group){
			$member_indexs = array_keys($info_members,$member_group['member_id']);
			//echo $member_group['member_id']." <br>";
			foreach($member_indexs AS $member_index){
                //echo "<pre>"; print_r($member_index);
                //exit;
				$datas[$member_group['member_id']]['member_name'] = $member_group['prename_full'].$member_group['firstname_th']." ".$member_group['lastname_th'];
				$datas[$member_group['member_id']]['mem_group_name'] = $member_group['mem_group_name'];
				$infoData = $infoDatas[$member_index];
				$datas[$member_group['member_id']]['employee_id'] = $member_group['employee_id'];
                $datas[$member_group['member_id']]['lv'] = $member_group['lv'];

				if($infoData['deduct_code']=='LOAN'){
					$loan_type_code = $loan_types[array_search($infoData['loan_type'],$loanNameIds)]['loan_type_code'];
					if (empty($datas[$member_group['member_id']][$loan_type_code."_ids"]) || !in_array($infoData['loan_id'], $datas[$member_group['member_id']][$loan_type_code."_ids"])) {
						$datas[$member_group['member_id']][$loan_type_code."_ids"][] = $infoData['loan_id'];
					}
					$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['contract_number'] = $infoData['contract_number'];
					if($infoData['pay_type']=='principal'){
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['principal'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
					} else {
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['interest'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
					}
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				}else if($infoData['deduct_code']=='ATM' && empty($_GET['term_of_loan'])){
					$loan_type_code = "atm";
					if (empty($datas[$member_group['member_id']][$loan_type_code."_ids"]) || !in_array($infoData['loan_atm_id'], $datas[$member_group['member_id']][$loan_type_code."_ids"])) {
						$datas[$member_group['member_id']][$loan_type_code."_ids"][] = $infoData['loan_atm_id'];
					}
					$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['contract_number'] = $infoData['contract_number_atm'];
					if($infoData['pay_type']=='principal'){
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['principal'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
					} else {
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['interest'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
					}
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				} else if ($infoData['deduct_code']=='SHARE' && empty($_GET['term_of_loan'])) {
					$datas[$member_group['member_id']][$infoData['deduct_code']] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				} else if ($infoData['deduct_code']=='DEPOSIT' && empty($_GET['term_of_loan'])) {
					$datas[$member_group['member_id']][$infoData['deduct_code']][] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['deposit_account_id'][] = $infoData['deposit_account_id'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				} else if(empty($_GET['term_of_loan'])){
					$datas[$member_group['member_id']][$infoData['deduct_code']] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				}
			}
		}

		$arr_data['datas'] = $datas;
		$arr_data['total_data'] = $total_data;
		$arr_data['group_total_data'] = $group_total_data;
		return $arr_data;
    }
    
    public function get_coop_report_charged_person_preview_2($arr_data) {
		//Set condition from URL
		$member_where = "";
		if (!empty($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
			$member_where .= " AND t3.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
		}
		if(@$_GET['department']!=''){
			$member_where .= " AND IF (
				t4.department_old IS NULL,
				t3.department,
				t4.department_old
			) = '".$_GET['department']."'";
		}
		if(@$_GET['faction']!=''){
			$member_where .= " AND faction = '".$_GET['faction']."'";
		}
		if(@$_GET['level']!=''){
			$member_where .= " AND level = '".$_GET['level']."'";
		}

		$loan_where = "1=1";
		if ($_GET['term_of_loan']) {
			$loan_where .= " AND t1.loan_type = ".$_GET['term_of_loan'];
		}

		$month = $_GET['month'];
		$year = $_GET['year'];

		//Get Loan type info
		$loan_types = $this->db->select("coop_loan_type.id, coop_loan_type.loan_type_code, coop_loan_name.loan_name_id")
								->from("coop_loan_name")
								->join("coop_loan_type", "coop_loan_name.loan_type_id = coop_loan_type.id", "inner")
								->get()->result_array();
		$loanNameIds = array_column($loan_types, 'loan_name_id');

		$date_start = date( 'Y-m-t',strtotime(($year-543)."-".sprintf("%02d",@$month)."-01"));
		$member_groups = $this->db->select(array('t1.mem_group_id'
										,'t1.mem_group_name'
										,'t1.id'
                                        ,'t1.lv'
										,'t2.member_id'
										,'t2.firstname_th'
										,'t2.lastname_th'
										,'t3.prename_full'
                                        ,'t2.employee_id'
										,'t2.mem_type_name'
										,'t1.mem_group_name2'
									))
					->from('(SELECT t2.id as `lv`, t1.id , t1.mem_group_id, t2.mem_group_name as mem_group_name, t1.mem_group_name as mem_group_name2 FROM coop_mem_group t1
							inner join coop_mem_group t2 ON t1.mem_group_parent_id=t2.id
							inner join coop_mem_group t3 ON t2.mem_group_parent_id=t3.id) as t1')
					->join("(SELECT
					                t3.employee_id,
									t3.member_id,
									IF(t4.level_old IS NULL, t3.LEVEL, t4.level_old) AS LEVEL,
									t3.firstname_th,
									t3.lastname_th,
									t3.prename_id,
									IF (
										t4.department_old IS NULL,
										t3.department,
										t4.department_old
									) AS department,
									IF (
										t4.faction_old IS NULL,
										t3.faction,
										t4.faction_old
                                    ) AS faction,
                                    coop_mem_type.mem_type_name
								FROM
                                    coop_mem_apply AS t3
                                    LEFT JOIN coop_mem_type ON t3.mem_type_id = coop_mem_type.mem_type_id
							LEFT JOIN (
								SELECT
									member_id,
									department_old,
									faction_old,
									level_old,
									date_move
								FROM
									coop_mem_group_move
								WHERE date_move >= '".$date_start."'
								GROUP BY member_id
								ORDER BY date_move ASC
							) AS t4 ON t3.member_id = t4.member_id
							WHERE 1=1 AND member_status <> 3 ".$member_where.") as t2", "t1.id = t2.level", "inner")
                    ->join('coop_prename as t3', 't2.prename_id = t3.prename_id', "left")
					->order_by('t1.id, t2.member_id')
					->get()->result_array();
		if(@$_GET['dev']!=""){
			echo $this->db->last_query();
		}
		$member_ids = array_column($member_groups, 'member_id');

        $where_member = " 1=1 ";
        if(sizeof(array_filter($member_ids))){
            $where_member = " member_id IN (".implode(',', array_map(function($val){ return sprintf("'%s'", $val); }, $member_ids)).")";
        }

		//echo $this->db->last_query(); exit;
		$infoDatas = $this->db->select(array(
												't1.loan_type',
												't1.contract_number',
												't3.member_id',
												't3.deduct_code',
												't3.loan_id',
												't3.loan_atm_id',
												't3.pay_type',
												't3.deposit_account_id',
												't4.contract_number as contract_number_atm',
												"t3.pay_amount as sum_pay_amount"
											))
								->from("(SELECT * FROM coop_finance_month_detail WHERE {$where_member} ) as t3")
								->join("coop_finance_month_profile as t2","t2.profile_month = '".$month."' AND t2.profile_year = '".$year."' AND t3.profile_id = t2.profile_id","inner")
								->join("(SELECT * FROM coop_loan WHERE ".$loan_where.") as t1","t1.id = t3.loan_id", "left")
								->join("coop_loan_atm as t4","t4.loan_atm_id = t3.loan_atm_id", "left")
								->group_by("t3.deduct_code, t3.member_id, t3.pay_type, t3.loan_id, t3.loan_atm_id, t3.deposit_account_id")
								->order_by("t3.member_id")
								->get()->result_array();
								//"SUM(t3.pay_amount) as sum_pay_amount"

		$info_members = array_column($infoDatas, 'member_id');
		//echo $this->db->last_query(); exit;
		$total_data = array();
        $group_total_data = array();
		$datas = array();

		foreach($member_groups as $key => $member_group){
			$member_indexs = array_keys($info_members,$member_group['member_id']);
			//echo $member_group['member_id']." <br>";
			foreach($member_indexs AS $member_index){
                //echo "<pre>"; print_r($member_index);
				//exit;
				$datas[$member_group['member_id']]['member_name'] = $member_group['prename_full'].$member_group['firstname_th']." ".$member_group['lastname_th'];
				$datas[$member_group['member_id']]['mem_group_name'] = $member_group['mem_group_name'];
				$datas[$member_group['member_id']]['mem_group_name2'] = $member_group['mem_group_name2'];
				$infoData = $infoDatas[$member_index];
                $datas[$member_group['member_id']]['member_id'] = $member_group['member_id'];
                $datas[$member_group['member_id']]['mem_type_name'] = $member_group['mem_type_name'];
                $datas[$member_group['member_id']]['lv'] = $member_group['id'];
				if($infoData['deduct_code']=='LOAN'){
					$loan_type_code = $loan_types[array_search($infoData['loan_type'],$loanNameIds)]['loan_type_code'];
					if (empty($datas[$member_group['member_id']][$loan_type_code."_ids"]) || !in_array($infoData['loan_id'], $datas[$member_group['member_id']][$loan_type_code."_ids"])) {
						$datas[$member_group['member_id']][$loan_type_code."_ids"][] = $infoData['loan_id'];
					}
					$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['contract_number'] = $infoData['contract_number'];
					if($infoData['pay_type']=='principal'){
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['principal'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
					} else {
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['interest'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
                    }
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				}else if($infoData['deduct_code']=='ATM' && empty($_GET['term_of_loan'])){
					$loan_type_code = "atm";
					if (empty($datas[$member_group['member_id']][$loan_type_code."_ids"]) || !in_array($infoData['loan_atm_id'], $datas[$member_group['member_id']][$loan_type_code."_ids"])) {
						$datas[$member_group['member_id']][$loan_type_code."_ids"][] = $infoData['loan_atm_id'];
					}
					$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['contract_number'] = $infoData['contract_number_atm'];
					if($infoData['pay_type']=='principal'){
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['principal'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
					} else {
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['interest'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
					}
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				} else if ($infoData['deduct_code']=='SHARE' && empty($_GET['term_of_loan'])) {
					$datas[$member_group['member_id']][$infoData['deduct_code']] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				} else if ($infoData['deduct_code']=='DEPOSIT' && empty($_GET['term_of_loan'])) {
					$datas[$member_group['member_id']][$infoData['deduct_code']][] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['deposit_account_id'][] = $infoData['deposit_account_id'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				} else if(empty($_GET['term_of_loan'])){
					$datas[$member_group['member_id']][$infoData['deduct_code']] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				}

			}
		}

		$arr_data['datas'] = $datas;
		$arr_data['total_data'] = $total_data;
		$arr_data['group_total_data'] = $group_total_data;
		return $arr_data;
    }
    
    public function get_coop_report_charged_person_excel(){
        $arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;

		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;

		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		//Set condition from URL
		$member_where = "";
		if (!empty($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
			$member_where .= " AND mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
		}
		if(@$_GET['department']!=''){
			$member_where .= " AND IF (
				t4.department_old IS NULL,
				t3.department,
				t4.department_old
			) = '".$_GET['department']."'";
		}
		if(@$_GET['faction']!=''){
			$member_where .= " AND faction = '".$_GET['faction']."'";
		}
		if(@$_GET['level']!=''){
			$member_where .= " AND level = '".$_GET['level']."'";
		}
		$loan_where = "1=1";
		if ($_GET['term_of_loan']) {
			$loan_where .= " AND t1.loan_type = ".$_GET['term_of_loan'];
		}

		$month = $_GET['month'];
		$year = $_GET['year'];

		//Get Loan type info
		$loan_types = $this->db->select("coop_loan_type.id, coop_loan_type.loan_type_code, coop_loan_name.loan_name_id")
								->from("coop_loan_name")
								->join("coop_loan_type", "coop_loan_name.loan_type_id = coop_loan_type.id", "inner")
								->get()->result_array();
		$loanNameIds = array_column($loan_types, 'loan_name_id');

		$date_start = date( 'Y-m-t',strtotime(($year-543)."-".sprintf("%02d",@$month)."-01"));
		$member_groups = $this->db->select(array('t1.mem_group_id'
										,'t1.mem_group_name'
										,'t1.id'
                                        ,'t1.lv'
										,'t2.member_id'
										,'t2.firstname_th'
										,'t2.lastname_th'
										,'t3.prename_full'
                                        ,'t2.employee_id'
									))
					->from('(SELECT t2.id as `lv`, t1.id , t1.mem_group_id, t2.mem_group_name as mem_group_name FROM coop_mem_group t1
                        inner join coop_mem_group t2 ON t1.mem_group_parent_id=t2.id
                        inner join coop_mem_group t3 ON t2.mem_group_parent_id=t3.id) as t1')
					->join("(SELECT
					                t3.employee_id,
									t3.member_id,
									IF(t4.level_old IS NULL, t3.LEVEL, t4.level_old) AS LEVEL,
									t3.firstname_th,
									t3.lastname_th,
									t3.prename_id,
									IF (
										t4.department_old IS NULL,
										t3.department,
										t4.department_old
									) AS department
								FROM
									coop_mem_apply AS t3
							LEFT JOIN (
								SELECT
									member_id,
									department_old,
									faction_old,
									level_old,
									date_move
								FROM
									coop_mem_group_move
								WHERE date_move >= '".$date_start."'
								GROUP BY member_id
								ORDER BY date_move ASC
							) AS t4 ON t3.member_id = t4.member_id
							WHERE 1=1 AND member_status <> 3 ".$member_where.") as t2", "t1.id = t2.level", "inner")
					->join('coop_prename as t3', 't2.prename_id = t3.prename_id', "left")
					->order_by('t1.lv, t2.employee_id')
					->get()->result_array();

		//echo $this->db->last_query(); exit;
		$member_ids = array_column($member_groups, 'member_id');
        $where_member = " 1=1 ";
        if(sizeof(array_filter($member_ids))){
            $where_member = " member_id IN (".implode(',', array_map(function($val){ return sprintf("'%s'", $val); }, $member_ids)).")";
        }

		//echo $this->db->last_query(); exit;
		$infoDatas = $this->db->select(array(
												't1.loan_type',
												't1.contract_number',
												't3.member_id',
												't3.deduct_code',
												't3.loan_id',
												't3.loan_atm_id',
												't3.pay_type',
												't3.deposit_account_id',
												't4.contract_number as contract_number_atm',
												"t3.pay_amount as sum_pay_amount"
											))
								->from("(SELECT * FROM coop_finance_month_detail WHERE {$where_member}) as t3")
								->join("coop_finance_month_profile as t2","t2.profile_month = '".$month."' AND t2.profile_year = '".$year."' AND t3.profile_id = t2.profile_id","inner")
								->join("(SELECT * FROM coop_loan WHERE ".$loan_where.") as t1","t1.id = t3.loan_id", "left")
								->join("coop_loan_atm as t4","t4.loan_atm_id = t3.loan_atm_id", "left")
								->group_by("t3.deduct_code, t3.member_id, t3.pay_type, t3.loan_id, t3.loan_atm_id, t3.deposit_account_id")
								->order_by("t3.member_id")
								->get()->result_array();
								//"SUM(t3.pay_amount) as sum_pay_amount"
		//echo $this->db->last_query();exit;
		$info_members = array_column($infoDatas, 'member_id');

		$total_data = array();
        $group_total_data = array();
		$datas = array();
		foreach($member_groups as $key => $member_group){
			$member_indexs = array_keys($info_members,$member_group['member_id']);
			foreach($member_indexs AS $member_index){
				$datas[$member_group['member_id']]['member_name'] = $member_group['prename_full'].$member_group['firstname_th']." ".$member_group['lastname_th'];
				$datas[$member_group['member_id']]['mem_group_name'] = $member_group['mem_group_name'];
				$infoData = $infoDatas[$member_index];
				$datas[$member_group['member_id']]['employee_id'] = $member_group['employee_id'];
                $datas[$member_group['member_id']]['lv'] = $member_group['lv'];

				if($infoData['deduct_code']=='LOAN'){
					$loan_type_code = $loan_types[array_search($infoData['loan_type'],$loanNameIds)]['loan_type_code'];
					if (empty($datas[$member_group['member_id']][$loan_type_code."_ids"]) || !in_array($infoData['loan_id'], $datas[$member_group['member_id']][$loan_type_code."_ids"])) {
						$datas[$member_group['member_id']][$loan_type_code."_ids"][] = $infoData['loan_id'];
					}
					$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['contract_number'] = $infoData['contract_number'];
					if($infoData['pay_type']=='principal'){
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['principal'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
					} else {
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['interest'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
					}
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				}else if($infoData['deduct_code']=='ATM' && empty($_GET['term_of_loan'])){
					$loan_type_code = "emergent";
					if (empty($datas[$member_group['member_id']][$loan_type_code."_ids"]) || !in_array($infoData['loan_atm_id'], $datas[$member_group['member_id']][$loan_type_code."_ids"])) {
						$datas[$member_group['member_id']][$loan_type_code."_ids"][] = $infoData['loan_atm_id'];
					}
					$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['contract_number'] = $infoData['contract_number_atm'];
					if($infoData['pay_type']=='principal'){
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['principal'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
					} else {
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['interest'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
					}
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				} else if ($infoData['deduct_code']=='SHARE' && empty($_GET['term_of_loan'])) {
					$datas[$member_group['member_id']][$infoData['deduct_code']] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				} else if ($infoData['deduct_code']=='DEPOSIT' && empty($_GET['term_of_loan'])) {
					$datas[$member_group['member_id']][$infoData['deduct_code']][] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['deposit_account_id'][] = $infoData['deposit_account_id'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				} else if(empty($_GET['term_of_loan'])){
					$datas[$member_group['member_id']][$infoData['deduct_code']] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				}
			}
		}
		//exit;
		$arr_data['datas'] = $datas;
		$arr_data['total_data'] = $total_data;
        $arr_data['group_total_data'] = $group_total_data;
        return $arr_data;
    }

    public function get_coop_report_charged_person_excel_2(){
        $arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;

		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;

		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		//Set condition from URL
		$member_where = "";
		if (!empty($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
			$member_where .= " AND mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
		}
		if(@$_GET['department']!=''){
			$member_where .= " AND IF (
				t4.department_old IS NULL,
				t3.department,
				t4.department_old
			) = '".$_GET['department']."'";
		}
		if(@$_GET['faction']!=''){
			$member_where .= " AND faction = '".$_GET['faction']."'";
		}
		if(@$_GET['level']!=''){
			$member_where .= " AND level = '".$_GET['level']."'";
		}
		$loan_where = "1=1";
		if ($_GET['term_of_loan']) {
			$loan_where .= " AND t1.loan_type = ".$_GET['term_of_loan'];
		}

		$month = $_GET['month'];
		$year = $_GET['year'];

		//Get Loan type info
		$loan_types = $this->db->select("coop_loan_type.id, coop_loan_type.loan_type_code, coop_loan_name.loan_name_id")
								->from("coop_loan_name")
								->join("coop_loan_type", "coop_loan_name.loan_type_id = coop_loan_type.id", "inner")
								->get()->result_array();
		$loanNameIds = array_column($loan_types, 'loan_name_id');

		$date_start = date( 'Y-m-t',strtotime(($year-543)."-".sprintf("%02d",@$month)."-01"));
		$member_groups = $this->db->select(array('t1.mem_group_id'
										,'t1.mem_group_name'
										,'t1.id'
                                        ,'t1.lv'
										,'t2.member_id'
										,'t2.firstname_th'
										,'t2.lastname_th'
										,'t3.prename_full'
                                        ,'t2.employee_id'
										,'t2.mem_type_name'
										,'t1.mem_group_name2'
									))
					->from('(SELECT t2.id as `lv`, t1.id , t1.mem_group_id, t2.mem_group_name as mem_group_name, t1.mem_group_name as mem_group_name2 FROM coop_mem_group t1
                        inner join coop_mem_group t2 ON t1.mem_group_parent_id=t2.id
                        inner join coop_mem_group t3 ON t2.mem_group_parent_id=t3.id) as t1')
					->join("(SELECT
					                t3.employee_id,
									t3.member_id,
									IF(t4.level_old IS NULL, t3.LEVEL, t4.level_old) AS LEVEL,
									t3.firstname_th,
									t3.lastname_th,
									t3.prename_id,
									IF (
										t4.department_old IS NULL,
										t3.department,
										t4.department_old
                                    ) AS department,
                                    coop_mem_type.mem_type_name
								FROM
                                    coop_mem_apply AS t3
                                    LEFT JOIN coop_mem_type ON t3.mem_type_id = coop_mem_type.mem_type_id
							LEFT JOIN (
								SELECT
									member_id,
									department_old,
									faction_old,
									level_old,
									date_move
								FROM
									coop_mem_group_move
								WHERE date_move >= '".$date_start."'
								GROUP BY member_id
								ORDER BY date_move ASC
							) AS t4 ON t3.member_id = t4.member_id
							WHERE 1=1 AND member_status <> 3 ".$member_where.") as t2", "t1.id = t2.level", "inner")
					->join('coop_prename as t3', 't2.prename_id = t3.prename_id', "left")
					->order_by('t1.id, t2.member_id')
					->get()->result_array();
		if(@$_GET['dev']!=""){
			echo $this->db->last_query();
		}
		
		$member_ids = array_column($member_groups, 'member_id');
        $where_member = " 1=1 ";
        if(sizeof(array_filter($member_ids))){
            $where_member = " member_id IN (".implode(',', array_map(function($val){ return sprintf("'%s'", $val); }, $member_ids)).")";
        }

		//echo $this->db->last_query(); exit;
		$infoDatas = $this->db->select(array(
												't1.loan_type',
												't1.contract_number',
												't3.member_id',
												't3.deduct_code',
												't3.loan_id',
												't3.loan_atm_id',
												't3.pay_type',
												't3.deposit_account_id',
												't4.contract_number as contract_number_atm',
												"t3.pay_amount as sum_pay_amount"
											))
								->from("(SELECT * FROM coop_finance_month_detail WHERE {$where_member}) as t3")
								->join("coop_finance_month_profile as t2","t2.profile_month = '".$month."' AND t2.profile_year = '".$year."' AND t3.profile_id = t2.profile_id","inner")
								->join("(SELECT * FROM coop_loan WHERE ".$loan_where.") as t1","t1.id = t3.loan_id", "left")
								->join("coop_loan_atm as t4","t4.loan_atm_id = t3.loan_atm_id", "left")
								->group_by("t3.deduct_code, t3.member_id, t3.pay_type, t3.loan_id, t3.loan_atm_id, t3.deposit_account_id")
								->order_by("t3.member_id")
								->get()->result_array();
								//"SUM(t3.pay_amount) as sum_pay_amount"
		//echo $this->db->last_query();exit;
		$info_members = array_column($infoDatas, 'member_id');

		$total_data = array();
        $group_total_data = array();
		$datas = array();
		foreach($member_groups as $key => $member_group){
			$member_indexs = array_keys($info_members,$member_group['member_id']);
			foreach($member_indexs AS $member_index){
				$datas[$member_group['member_id']]['member_name'] = $member_group['prename_full'].$member_group['firstname_th']." ".$member_group['lastname_th'];
				$datas[$member_group['member_id']]['mem_group_name'] = $member_group['mem_group_name'];
				$datas[$member_group['member_id']]['mem_group_name2'] = $member_group['mem_group_name2'];
				$infoData = $infoDatas[$member_index];
                $datas[$member_group['member_id']]['member_id'] = $member_group['member_id'];
                $datas[$member_group['member_id']]['mem_type_name'] = $member_group['mem_type_name'];
                $datas[$member_group['member_id']]['lv'] = $member_group['id'];

				if($infoData['deduct_code']=='LOAN'){
					$loan_type_code = $loan_types[array_search($infoData['loan_type'],$loanNameIds)]['loan_type_code'];
					if (empty($datas[$member_group['member_id']][$loan_type_code."_ids"]) || !in_array($infoData['loan_id'], $datas[$member_group['member_id']][$loan_type_code."_ids"])) {
						$datas[$member_group['member_id']][$loan_type_code."_ids"][] = $infoData['loan_id'];
					}
					$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['contract_number'] = $infoData['contract_number'];
					if($infoData['pay_type']=='principal'){
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['principal'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
					} else {
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['interest'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
					}
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				}else if($infoData['deduct_code']=='ATM' && empty($_GET['term_of_loan'])){
					$loan_type_code = "emergent";
					if (empty($datas[$member_group['member_id']][$loan_type_code."_ids"]) || !in_array($infoData['loan_atm_id'], $datas[$member_group['member_id']][$loan_type_code."_ids"])) {
						$datas[$member_group['member_id']][$loan_type_code."_ids"][] = $infoData['loan_atm_id'];
					}
					$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['contract_number'] = $infoData['contract_number_atm'];
					if($infoData['pay_type']=='principal'){
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['principal'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
					} else {
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['interest'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
                        $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
					}
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				} else if ($infoData['deduct_code']=='SHARE' && empty($_GET['term_of_loan'])) {
					$datas[$member_group['member_id']][$infoData['deduct_code']] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				} else if ($infoData['deduct_code']=='DEPOSIT' && empty($_GET['term_of_loan'])) {
					$datas[$member_group['member_id']][$infoData['deduct_code']][] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['deposit_account_id'][] = $infoData['deposit_account_id'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				} else if(empty($_GET['term_of_loan'])){
					$datas[$member_group['member_id']][$infoData['deduct_code']] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_'.$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
                    $group_total_data[$datas[$member_group['member_id']]['lv'].'_total_amount'] += $infoData['sum_pay_amount'];
				}
			}
		}
		//exit;
		$arr_data['datas'] = $datas;
		$arr_data['total_data'] = $total_data;
        $arr_data['group_total_data'] = $group_total_data;
        return $arr_data;
    }

}