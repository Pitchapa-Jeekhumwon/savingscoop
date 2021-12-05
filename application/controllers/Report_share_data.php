<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_share_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
        $this->load->model('report_share_data_model');
	}
	
	public function coop_report_change_share(){
		$arr_data = array();
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$this->db->order_by('order_by ASC');
		$rs_loan_type = $this->db->get()->result_array();
		//print_r($this->db->last_query()); exit;
		$loan_type = array();
		if(!empty($rs_loan_type)){
			foreach($rs_loan_type as $key => $row_loan_type){
				$loan_type[$row_loan_type['id']] = @$row_loan_type['loan_type'];
			}
		}
		$arr_data['loan_type'] = $loan_type;
		$this->libraries->template('report_share_data/coop_report_change_share',$arr_data);
	}
	
	function check_report_change_share(){
		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$where_check = "";
		$where_check2 = "";

		if(@$_POST['start_date'] != '' AND @$_POST['end_date'] == ''){
			$where_check = " AND t1.create_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
			$where_check2 = " AND t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_POST['start_date'] != '' AND @$_POST['end_date'] != ''){
			$where_check = " AND t1.create_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_check2 = " AND t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}
		
		$this->db->select('t2.member_id');
		$this->db->from('coop_change_share as t1');
		$this->db->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner");
		$this->db->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left");
		$this->db->where("t1.change_share_status IN ('1', '2') {$where_check}");
		$rs_check = $this->db->get()->result_array();		
		$row_check = @$rs_check[0];
		//print_r($this->db->last_query()); exit;
		
		$this->db->select('t2.member_id');
		$this->db->from('coop_mem_share as t1');
		$this->db->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner");
		$this->db->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left");
		$this->db->where("t1.share_status IN ('1', '2') AND t1.share_type = 'SPA'  {$where_check2}");
		$rs_check2 = $this->db->get()->result_array();
		$row_check2 = @$rs_check2[0];
		
		if(@$row_check['member_id'] != '' || @$row_check2['member_id'] != ''){
			echo "success";
		}
	}
	
	function coop_report_change_share_preview(){
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$loan_type = array();
		foreach($rs_loan_type as $key => $row_loan_type){
			$loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		
		$this->preview_libraries->template_preview('report_share_data/coop_report_change_share_preview',$arr_data);
	}
	
	function coop_report_change_share_excel(){
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$loan_type = array();
		foreach($rs_loan_type as $key => $row_loan_type){
			$loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		
		$this->load->view('report_share_data/coop_report_change_share_excel',$arr_data);
	}
	
	public function coop_report_share_loan_balance(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '3'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		$this->libraries->template('report_share_data/coop_report_share_loan_balance',$arr_data);
	}
	
	function coop_report_share_loan_balance_preview(){
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

                $this->preview_libraries->template_preview('report_share_data/coop_report_share_loan_balance_preview', $arr_data);
            } else if (@$_GET['type_department'] == '2') {
                $this->preview_libraries->template_preview('report_share_data/coop_report_share_loan_balance_subdivision_preview', $arr_data);
            }
        }
	}

	function coop_report_share_loan_balance_loan_type_preview(){
        set_time_limit(-1);
        ini_set("memory_limit", -1);
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

        $this->db->select(array('coop_loan_name.loan_name_id','coop_loan_name.loan_name','coop_loan_type.loan_type_code'));
        $this->db->from('coop_loan_name');
        $this->db->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left');
        $this->db->where(" coop_loan_name.loan_name_id IN (SELECT loan_type FROM coop_loan WHERE loan_status = 1 GROUP BY loan_type) ");
        $this->db->order_by("coop_loan_name.loan_name_id asc");
        $rs_type_code = $this->db->get()->result_array();
        $arr_loan_type_code = array();

        foreach($rs_type_code AS $key_type_code=>$row_type_code){
            $arr_loan_type_code[@$row_type_code['loan_name_id']] = @$row_type_code['loan_type_code'];
        }

        $sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`, `coop_mem_apply`.`department`, `coop_mem_apply`.`faction`, `coop_mem_apply`.`level`, `coop_mem_apply`.`sex`
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
							) AS department, member_id, prename_id, firstname_th, lastname_th,member_status, if(sex = 'M', 2, 1) as `sex`  FROM coop_mem_apply) AS coop_mem_apply	  
				WHERE coop_mem_apply.member_status = 1 
				ORDER BY coop_mem_apply.member_id ASC";
        if($_GET['debug'] == "on") {
            echo $sql.'<hr>'; //exit;
        }
        $result = $this->db->query($sql)->result_array();

        $member_ids = array_column($result, 'member_id');

        //Get Lastest Loan Information
        $loan_ids = array_column($result, 'loan_id');
        $where_loan = " 1=1 ";
        if(sizeof(array_filter($member_ids))){
            $where_loan = " t3.loan_status = '1' AND t3.member_id IN  (".implode(',', array_map(function($v){ return sprintf("'%s'", $v); }, array_filter($member_ids))).") ";
        }

        $loans = $this->db->query("SELECT T.*FROM (SELECT `t3`.`member_id`,`t3`.`contract_number`,`t3`.`period_now`,`t3`.`loan_type`,`t1`.`loan_transaction_id`,`t1`.`loan_id`,`t1`.`loan_amount_balance`,`t1`.`transaction_datetime` FROM `coop_loan_transaction` AS `t1` INNER JOIN (SELECT loan_id,MAX(cast(transaction_datetime AS Datetime)) AS max FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' GROUP BY loan_id) AS t2 ON `t1`.`loan_id`=`t2`.`loan_id` AND `t1`.`transaction_datetime`=`t2`.`max` INNER JOIN coop_loan AS t3 ON t2.loan_id=t3.id WHERE {$where_loan}  ORDER BY `t1`.`transaction_datetime`,`t1`.`loan_transaction_id` DESC) T  ORDER BY member_id")->result_array();
        if($_GET['debug'] == "print_loans") {
            echo $this->db->last_query(); exit;
        }
        $loan_members = array_column($loans, 'loan_id');
        $lona_const_type = array();
        foreach ($rs_type_code as $index => $value){
            $lona_const_type[$value['loan_type_code']][] = $value;
        }
        if($_GET['debug'] == "loan_type"){
            echo "<pre>"; print_r($lona_const_type); exit;
        }
        $_loans = array();
        $_loan_type_chk = array();
        foreach ($loans as $key => $loan){

            $loan_type_code = $arr_loan_type_code[$loan['loan_type']];
            if(empty($lona_const_type[$loan_type_code])){
                echo  "loan_type_code : ".$loan_type_code ." , loan_type : ".$loan['loan_type']; exit;
            }
            foreach ($lona_const_type[$loan_type_code] as $key => $item){
                if($item['loan_name_id'] == $loan["loan_type"]){
                    $_loans[$loan['member_id']][$key][$arr_loan_type_code[$loan['loan_type']]] = $loan;
                }
            }
        }
        $loans = $_loans;
        unset($_loans);
        if($_GET['debug'] == "print_loans") {

            echo "<pre>"; print_r($lona_const_type);
            echo "<pre>"; print_r($loans); exit;
        }
        $run_index = 0;
        $row = array();
        $check_row = "xx";
        $index = 0;
        $sql_max_loan_type = "SELECT max(amount) as `max_type` FROM (SELECT coop_loan_type.loan_type_code, COUNT(coop_loan_type.loan_type_code) as `amount` FROM coop_loan_name LEFT JOIN coop_loan_type ON coop_loan_name.loan_type_id=coop_loan_type.id GROUP BY coop_loan_type.loan_type_code) T ";
        $max_type = $this->db->query($sql_max_loan_type)->row_array()['max_type'];

        $where_share = " 1=1 ";
        if(sizeof(array_filter($member_ids))){
            $where_share = " member_id IN  (".implode(',', array_map(function($v){ return sprintf("'%s'", $v); }, array_filter($member_ids))).") ";
        }
        $sql_shares = "SELECT t1.member_id,t1.share_period,t1.share_collect_value,t1.share_status,t1.share_payable_value,'1' AS check_share,t1.share_date FROM coop_mem_share AS t1 INNER JOIN (
SELECT inner_a.member_id,max(inner_a.share_id) AS share_id FROM coop_mem_share AS inner_a INNER JOIN (
SELECT member_id,max(share_date) AS share_date FROM coop_mem_share WHERE {$where_share} AND share_date BETWEEN '" . $start_date . " 00:00:00.000' AND '" . $end_date . " 23:59:59.000' GROUP BY member_id) inner_b ON inner_a.member_id=inner_b.member_id AND inner_a.share_date=inner_b.share_date GROUP BY member_id) t2 ON t1.member_id=t2.member_id AND t1.share_id=t2.share_id WHERE t1.share_status<> 0 ORDER BY t1.member_id ASC";

        $shares = $this->db->query($sql_shares)->result_array();
        $_shares = array();
        foreach ($shares as $key => $share){

            $_shares[$share['member_id']] = $share;
        }
        $shares = $_shares;
        unset($_shares);

        //Generate Fund support Information
        $where_fund = "1=1";
        $where_fund_t1 = $_GET["type_date"] == 1 ? "payment_date <= '".$end_date." 23:59:59.000'" : "payment_date BETWEEN '".$end_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        $funds = $this->db->select("SUM(t2.principal) as loan_amount_balance, t5.member_id, t5.prename_id, t5.firstname_th, t5.lastname_th, t5.level, t5.sex, t7.id as faction, t8.id as department, t9.prename_full,
									t6.mem_group_id as id, t6.mem_group_name as name, t7.mem_group_name as sub_name, t8.mem_group_name as main_name, t4.id as loan_id, t4.contract_number, t4.loan_type, t4.period_now")
            ->from("(SELECT *, MAX(payment_date) as max_date FROM coop_loan_fund_balance_transaction WHERE ".$where_fund_t1." GROUP BY sub_compromise_id) as t1")
            ->join("coop_loan_fund_balance_transaction as t2", "t1.sub_compromise_id = t2.sub_compromise_id AND t1.max_date = t2.payment_date", "inner")
            ->join("coop_loan_compromise as t3", "t2.compromise_id = t3.id", "inner")
            ->join("coop_loan as t4", "t3.loan_id = t4.id", "inner")
            ->join("(SELECT IF (
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										coop_mem_apply. level
									) AS level, member_id, prename_id, firstname_th, lastname_th,member_status, IF(sex = 'M', 2, 1) as sex  FROM coop_mem_apply) as t5", "t3.member_id = t5.member_id", "inner")
            ->join("coop_mem_group as t6", "t5.level = t6.id", "left")
            ->join("coop_mem_group as t7", "t7.id = t6.mem_group_parent_id", "left")
            ->join("coop_mem_group as t8", "t8.id = t7.mem_group_parent_id", "left")
            ->join("coop_prename as t9", "t5.prename_id = t9.prename_id", "left")
            ->where($where_fund)
            ->group_by("t2.compromise_id")
            ->get()->result_array();

        //echo '<pre>'; print_r($row); echo '</pre>'; exit;

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

        $arr_data = array();
        $x=0;
        $join_arr = array();

        $where .= " AND coop_mem_group.mem_group_type = '2'";
        $field_department = "faction";

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
        $this->paginater_all_preview->page_link_limit(24);
        $this->paginater_all_preview->page_limit_first(16);
        $this->paginater_all_preview->order_by('coop_mem_group.mem_group_id');
        $this->paginater_all_preview->join_arr($join_arr);
        $row = $this->paginater_all_preview->paginater_process();

        //echo "<pre>"; print_r($row); exit;
        $_tmp_check_member = array();
        $_tmp_check_loan = array();
        $_mng_rows_pointer = array();
        $rows = 0;

        //echo "<pre>"; var_dump($result); echo "</pre>"; exit;

        $_data_dumper = [];
        foreach ($row['data'] as $key => $data) {

            foreach ($data as $index => $val) {

                foreach ($result as $num => $member) {

                    if ($member['faction'] == $val['id']) {

                        $comn += 1;

                        if (!empty($loans[$member['member_id']])) {

                            foreach ($loans[$member['member_id']] as $ii => $loan) {

                                if($ii == 0) {
                                    //mem ground label
                                    $_data_dumper[$key][$index][$ii]['id'] = $val['id'];
                                    $_data_dumper[$key][$index][$ii]['mem_group_id'] = $val['mem_group_id'];
                                    $_data_dumper[$key][$index][$ii]['mem_group_parent_id'] = $val['mem_group_parent_id'];
                                    $_data_dumper[$key][$index][$ii]['mem_group_name'] = $val['mem_group_name'];


                                    if (!in_array($member['member_id'], $_tmp_check_member)) {
                                        $nnonoo +=1;
                                        //หุ้น
                                        $_data_dumper[$key][$index][$ii]['share_person'] = (int)$_data_dumper[$key][$index][$ii]['share_person'] + 1;
                                        $_data_dumper[$key][$index][$ii]['mem_all_amt'] += 1;
                                        $_data_dumper[$key][$index][$ii]['mem_has_debt'] += 1;
                                        $_data_dumper[$key][$index][$ii]['mem_men'] += $member['sex'] == 2 ? 1 : 0;
                                        $_data_dumper[$key][$index][$ii]['mem_wemen'] += $member['sex'] == 1 ? 1 : 0;
                                        $_tmp_check_member[] = $member['member_id'];

                                    }

                                    if (@$shares[$member['member_id']]['share_status'] == '3') {
                                        $share_collect_value = (!empty($shares[$member['member_id']]['share_payable_value'])) ? @$shares[$member['member_id']]['share_payable_value'] : 0;
                                    } else {
                                        $share_collect_value = !empty($shares[$member['member_id']]['share_collect_value']) ? $shares[$member['member_id']]['share_collect_value'] : 0;
                                    }
                                    $_data_dumper[$key][$index][$ii]['share_collect'] = (int)$_data_dumper[$key][$index][$ii]['share_collect'] + $share_collect_value;
                                    //echo "<pre>"; print_r($loans[$member['member_id']]);
                                }

                                foreach($loan as $name => $item_loan) {
                                    $_data_dumper[$key][$index][$ii]['loan_' . $name . '_person'] = (int)$_data_dumper[$key][$index][$ii]['loan_' . $name . '_person'] + 1;
                                    $_data_dumper[$key][$index][$ii]['loan_' . $name . '_balance'] = (int)$_data_dumper[$key][$index][$ii]['loan_' . $name . '_balance'] + $item_loan['loan_amount_balance'];

                                    $_data_dumper[$key][$index][$ii]['total_loan_balance'] = (int)$_data_dumper[$key][$index][$ii]['total_loan_balance'] + $item_loan['loan_amount_balance'];
                                }
                            }
                        }else{

                            $_data_dumper[$key][$index][0]['id'] = $val['id'];
                            $_data_dumper[$key][$index][0]['mem_group_id'] = $val['mem_group_id'];
                            $_data_dumper[$key][$index][0]['mem_group_parent_id'] = $val['mem_group_parent_id'];
                            $_data_dumper[$key][$index][0]['mem_group_name'] = $val['mem_group_name'];

                            if (!in_array($member['member_id'], $_tmp_check_member)) {
                                $los += 1;
                                //หุ้น
                                $_data_dumper[$key][$index][0]['share_person'] = (int)$_data_dumper[$key][$index][0]['share_person'] + 1;
                                $_tmp_check_member[] = $member['member_id'];
                                $_data_dumper[$key][$index][0]['mem_all_amt'] += 1;
                                $_data_dumper[$key][$index][0]['mem_men'] += $member['sex'] == 2 ? 1 : 0;
                                $_data_dumper[$key][$index][0]['mem_wemen'] += $member['sex'] == 1 ? 1 : 0;
                            }
                            if (@$shares[$member['member_id']]['share_status'] == '3') {
                                $share_collect_value = (!empty($shares[$member['member_id']]['share_payable_value'])) ? @$shares[$member['member_id']]['share_payable_value'] : 0;
                            } else {
                                $share_collect_value = !empty($shares[$member['member_id']]['share_collect_value']) ? $shares[$member['member_id']]['share_collect_value'] : 0;
                            }
                            $_data_dumper[$key][$index][0]['share_collect'] = (int) $_data_dumper[$key][$index][0]['share_collect'] + $share_collect_value;
                        }
                    }
                }
            }
        }

        $row['data'] = $_data_dumper;
        if($_GET['debug'] == "print_data") {

            $summary_key =  array(
                'loan_emergent_balance',
                'loan_normal_balance',
                'loan_special_balance'
            );

            $_summary= self::sum_total_array($row['data'],$summary_key);
            echo "<pre>"; print_r($_summary);
            exit;
        }

        $summary_key =  array(
            'loan_emergent_balance',
            'loan_normal_balance',
            'loan_special_balance'
        );

        $arr_data['summary'] = self::sum_total_array($row['data'],$summary_key);

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

        $this->preview_libraries->template_preview('report_share_data/coop_report_share_loan_balance_subdivision_custom_preview.php',$arr_data);

    }

    function coop_report_share_loan_balance_loan_type_excel(){
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

        $this->db->select(array('coop_loan_name.loan_name_id','coop_loan_name.loan_name','coop_loan_type.loan_type_code'));
        $this->db->from('coop_loan_name');
        $this->db->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left');
        $this->db->where(" coop_loan_name.loan_name_id IN (SELECT loan_type FROM coop_loan WHERE loan_status = 1 GROUP BY loan_type) ");
        $this->db->order_by("coop_loan_name.loan_name_id asc");
        $rs_type_code = $this->db->get()->result_array();
        $arr_loan_type_code = array();

        foreach($rs_type_code AS $key_type_code=>$row_type_code){
            $arr_loan_type_code[@$row_type_code['loan_name_id']] = @$row_type_code['loan_type_code'];
        }

        $sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`, `coop_mem_apply`.`department`, `coop_mem_apply`.`faction`, `coop_mem_apply`.`level`, `coop_mem_apply`.`sex`
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
							) AS department, member_id, prename_id, firstname_th, lastname_th,member_status, if(sex = 'M', 2, 1) as `sex`  FROM coop_mem_apply) AS coop_mem_apply	  
				WHERE coop_mem_apply.member_status = 1 
				ORDER BY coop_mem_apply.member_id ASC";
        if($_GET['debug'] == "on") {
            echo $sql.'<hr>'; //exit;
        }
        $result = $this->db->query($sql)->result_array();

        $member_ids = array_column($result, 'member_id');

        //Get Lastest Loan Information
        $loan_ids = array_column($result, 'loan_id');
        $where_loan = " 1=1 ";
        if(sizeof(array_filter($member_ids))){
            $where_loan = " t3.loan_status = '1' AND t3.member_id IN  (".implode(',', array_map(function($v){ return sprintf("'%s'", $v); }, array_filter($member_ids))).") ";
        }

        $loans = $this->db->query("SELECT T.*FROM (SELECT `t3`.`member_id`,`t3`.`contract_number`,`t3`.`period_now`,`t3`.`loan_type`,`t1`.`loan_transaction_id`,`t1`.`loan_id`,`t1`.`loan_amount_balance`,`t1`.`transaction_datetime` FROM `coop_loan_transaction` AS `t1` INNER JOIN (SELECT loan_id,MAX(cast(transaction_datetime AS Datetime)) AS max FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' GROUP BY loan_id) AS t2 ON `t1`.`loan_id`=`t2`.`loan_id` AND `t1`.`transaction_datetime`=`t2`.`max` INNER JOIN coop_loan AS t3 ON t2.loan_id=t3.id WHERE {$where_loan} ORDER BY `t1`.`transaction_datetime`,`t1`.`loan_transaction_id` DESC) T ORDER BY member_id")->result_array();
//        if($_GET['debug'] == "print_loans") {
//            echo $this->db->last_query(); exit;
//        }
        $loan_members = array_column($loans, 'loan_id');
        $lona_const_type = array();
        foreach ($rs_type_code as $index => $value){
            $lona_const_type[$value['loan_type_code']][] = $value;
        }
        if($_GET['debug'] == "loan_type"){
            echo "<pre>"; print_r($lona_const_type); exit;
        }
        $_loans = array();
        $_loan_type_chk = array();
        foreach ($loans as $key => $loan){

            $loan_type_code = $arr_loan_type_code[$loan['loan_type']];
            foreach ($lona_const_type[$loan_type_code] as $key => $item){
                if($item['loan_name_id'] == $loan["loan_type"]){
                    $_loans[$loan['member_id']][$key][$arr_loan_type_code[$loan['loan_type']]] = $loan;
                }
            }
        }
        $loans = $_loans;
        unset($_loans);
        if($_GET['debug'] == "print_loans") {

            echo "<pre>"; print_r($lona_const_type);
            echo "<pre>"; print_r($loans); exit;
        }
        $run_index = 0;
        $row = array();
        $check_row = "xx";
        $index = 0;
        $sql_max_loan_type = "SELECT max(amount) as `max_type` FROM (SELECT coop_loan_type.loan_type_code, COUNT(coop_loan_type.loan_type_code) as `amount` FROM coop_loan_name LEFT JOIN coop_loan_type ON coop_loan_name.loan_type_id=coop_loan_type.id GROUP BY coop_loan_type.loan_type_code) T ";
        $max_type = $this->db->query($sql_max_loan_type)->row_array()['max_type'];

        $where_share = " 1=1 ";
        if(sizeof(array_filter($member_ids))){
            $where_share = " member_id IN  (".implode(',', array_map(function($v){ return sprintf("'%s'", $v); }, array_filter($member_ids))).") ";
        }
        $sql_shares = "SELECT t1.member_id,t1.share_period,t1.share_collect_value,t1.share_status,t1.share_payable_value,'1' AS check_share,t1.share_date FROM coop_mem_share AS t1 INNER JOIN (
SELECT inner_a.member_id,max(inner_a.share_id) AS share_id FROM coop_mem_share AS inner_a INNER JOIN (
SELECT member_id,max(share_date) AS share_date FROM coop_mem_share WHERE {$where_share} AND share_date BETWEEN '" . $start_date . " 00:00:00.000' AND '" . $end_date . " 23:59:59.000' GROUP BY member_id) inner_b ON inner_a.member_id=inner_b.member_id AND inner_a.share_date=inner_b.share_date GROUP BY member_id) t2 ON t1.member_id=t2.member_id AND t1.share_id=t2.share_id WHERE t1.share_status<> 0 ORDER BY t1.member_id ASC";

        $shares = $this->db->query($sql_shares)->result_array();
        $_shares = array();
        foreach ($shares as $key => $share){

            $_shares[$share['member_id']] = $share;
        }
        $shares = $_shares;
        unset($_shares);

        //Generate Fund support Information
        $where_fund = "1=1";
        $where_fund_t1 = $_GET["type_date"] == 1 ? "payment_date <= '".$end_date." 23:59:59.000'" : "payment_date BETWEEN '".$end_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        $funds = $this->db->select("SUM(t2.principal) as loan_amount_balance, t5.member_id, t5.prename_id, t5.firstname_th, t5.lastname_th, t5.level, t5.sex, t7.id as faction, t8.id as department, t9.prename_full,
									t6.mem_group_id as id, t6.mem_group_name as name, t7.mem_group_name as sub_name, t8.mem_group_name as main_name, t4.id as loan_id, t4.contract_number, t4.loan_type, t4.period_now")
            ->from("(SELECT *, MAX(payment_date) as max_date FROM coop_loan_fund_balance_transaction WHERE ".$where_fund_t1." GROUP BY sub_compromise_id) as t1")
            ->join("coop_loan_fund_balance_transaction as t2", "t1.sub_compromise_id = t2.sub_compromise_id AND t1.max_date = t2.payment_date", "inner")
            ->join("coop_loan_compromise as t3", "t2.compromise_id = t3.id", "inner")
            ->join("coop_loan as t4", "t3.loan_id = t4.id", "inner")
            ->join("(SELECT IF (
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										coop_mem_apply. level
									) AS level, member_id, prename_id, firstname_th, lastname_th,member_status, IF(sex = 'M', 2, 1) as sex  FROM coop_mem_apply) as t5", "t3.member_id = t5.member_id", "inner")
            ->join("coop_mem_group as t6", "t5.level = t6.id", "left")
            ->join("coop_mem_group as t7", "t7.id = t6.mem_group_parent_id", "left")
            ->join("coop_mem_group as t8", "t8.id = t7.mem_group_parent_id", "left")
            ->join("coop_prename as t9", "t5.prename_id = t9.prename_id", "left")
            ->where($where_fund)
            ->group_by("t2.compromise_id")
            ->get()->result_array();

        //echo '<pre>'; print_r($row); echo '</pre>'; exit;

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

        $arr_data = array();
        $x=0;
        $join_arr = array();

        $where .= " AND coop_mem_group.mem_group_type = '2'";
        $field_department = "faction";

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
        $this->paginater_all_preview->per_page(200);
        $this->paginater_all_preview->page_link_limit(200);
        $this->paginater_all_preview->page_limit_first(200);
        $this->paginater_all_preview->order_by('coop_mem_group.mem_group_id');
        $this->paginater_all_preview->join_arr($join_arr);
        $row = $this->paginater_all_preview->paginater_process();

        //echo "<pre>"; print_r($row); exit;
        $_tmp_check_member = array();
        $_tmp_check_loan = array();
        $_mng_rows_pointer = array();
        $rows = 0;

        //echo "<pre>"; var_dump($result); echo "</pre>"; exit;

        $_data_dumper = [];
        foreach ($row['data'] as $key => $data) {

            foreach ($data as $index => $val) {

                foreach ($result as $num => $member) {

                    if ($member['faction'] == $val['id']) {

                        $comn += 1;

                        if (!empty($loans[$member['member_id']])) {

                            foreach ($loans[$member['member_id']] as $ii => $loan) {

                                if($ii == 0) {
                                    //mem ground label
                                    $_data_dumper[$key][$index][$ii]['id'] = $val['id'];
                                    $_data_dumper[$key][$index][$ii]['mem_group_id'] = $val['mem_group_id'];
                                    $_data_dumper[$key][$index][$ii]['mem_group_parent_id'] = $val['mem_group_parent_id'];
                                    $_data_dumper[$key][$index][$ii]['mem_group_name'] = $val['mem_group_name'];


                                    if (!in_array($member['member_id'], $_tmp_check_member)) {
                                        $nnonoo +=1;
                                        //หุ้น
                                        $_data_dumper[$key][$index][$ii]['share_person'] = (int)$_data_dumper[$key][$index][$ii]['share_person'] + 1;
                                        $_data_dumper[$key][$index][$ii]['mem_all_amt'] += 1;
                                        $_data_dumper[$key][$index][$ii]['mem_has_debt'] += 1;
                                        $_data_dumper[$key][$index][$ii]['mem_men'] += $member['sex'] == 2 ? 1 : 0;
                                        $_data_dumper[$key][$index][$ii]['mem_wemen'] += $member['sex'] == 1 ? 1 : 0;
                                        $_tmp_check_member[] = $member['member_id'];

                                    }

                                    if (@$shares[$member['member_id']]['share_status'] == '3') {
                                        $share_collect_value = (!empty($shares[$member['member_id']]['share_payable_value'])) ? @$shares[$member['member_id']]['share_payable_value'] : 0;
                                    } else {
                                        $share_collect_value = !empty($shares[$member['member_id']]['share_collect_value']) ? $shares[$member['member_id']]['share_collect_value'] : 0;
                                    }
                                    $_data_dumper[$key][$index][$ii]['share_collect'] = (int)$_data_dumper[$key][$index][$ii]['share_collect'] + $share_collect_value;
                                    //echo "<pre>"; print_r($loans[$member['member_id']]);
                                }

                                foreach($loan as $name => $item_loan) {
                                    $_data_dumper[$key][$index][$ii]['loan_' . $name . '_person'] = (int)$_data_dumper[$key][$index][$ii]['loan_' . $name . '_person'] + 1;
                                    $_data_dumper[$key][$index][$ii]['loan_' . $name . '_balance'] = (int)$_data_dumper[$key][$index][$ii]['loan_' . $name . '_balance'] + $item_loan['loan_amount_balance'];

                                    $_data_dumper[$key][$index][$ii]['total_loan_balance'] = (int)$_data_dumper[$key][$index][$ii]['total_loan_balance'] + $item_loan['loan_amount_balance'];
                                }
                            }
                        }else{

                            $_data_dumper[$key][$index][0]['id'] = $val['id'];
                            $_data_dumper[$key][$index][0]['mem_group_id'] = $val['mem_group_id'];
                            $_data_dumper[$key][$index][0]['mem_group_parent_id'] = $val['mem_group_parent_id'];
                            $_data_dumper[$key][$index][0]['mem_group_name'] = $val['mem_group_name'];

                            if (!in_array($member['member_id'], $_tmp_check_member)) {
                                $los += 1;
                                //หุ้น
                                $_data_dumper[$key][$index][0]['share_person'] = (int)$_data_dumper[$key][$index][0]['share_person'] + 1;
                                $_tmp_check_member[] = $member['member_id'];
                                $_data_dumper[$key][$index][0]['mem_all_amt'] += 1;
                                $_data_dumper[$key][$index][0]['mem_men'] += $member['sex'] == 2 ? 1 : 0;
                                $_data_dumper[$key][$index][0]['mem_wemen'] += $member['sex'] == 1 ? 1 : 0;
                            }
                            if (@$shares[$member['member_id']]['share_status'] == '3') {
                                $share_collect_value = (!empty($shares[$member['member_id']]['share_payable_value'])) ? @$shares[$member['member_id']]['share_payable_value'] : 0;
                            } else {
                                $share_collect_value = !empty($shares[$member['member_id']]['share_collect_value']) ? $shares[$member['member_id']]['share_collect_value'] : 0;
                            }
                            $_data_dumper[$key][$index][0]['share_collect'] = (int) $_data_dumper[$key][$index][0]['share_collect'] + $share_collect_value;
                        }
                    }
                }
            }
        }

        $row['data'] = $_data_dumper;
        if($_GET['debug'] == "print_data") {
            $summary_key =  array(
                            'loan_emergent_balance',
                            'loan_normal_balance',
                            'loan_special_balance'
                        );

            $_summary = array();
            $_summary = self::sum_total_array($row['data'],$summary_key);
            echo "<pre>"; print_r($_summary);
            exit;
        }
        $summary_key =  array(
            'loan_emergent_balance',
            'loan_normal_balance',
            'loan_special_balance'
        );

        $_summary = array();
        $arr_data['summary'] = self::sum_total_array($row['data'],$summary_key);

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

        $this->preview_libraries->template_preview('report_share_data/coop_report_share_loan_balance_subdivision_custom_excel.php',$arr_data);

    }

    function sum_total_array($arr = array(), $keys, $index_num = 0){
	    foreach ($arr as $value){
	        foreach ($value as $index => $item){
	            if(is_array($keys)) {
	                foreach($keys as $in => $key) {
	                    for($i=0;$i <= 2; $i++){
                            $amt[$i][$key] += $item[$i][$key];
                        }
                    }
                }else{
	                $amt += (int)$item[$index_num][$keys];
                }
            }
        }
	    return $amt;
    }

    function coop_report_share_loan_balance_person_preview(){
        $arr_data = array();
        $code_name = 'report_share_loan_balance_person';

        $this->db->select('*');
        $this->db->from('format_setting_report');
        $this->db->where('code_name', $code_name);
        $switch_report = $this->db->get()->row_array();

        if($switch_report['switch_code'] == '1') {
            $arr_data = $this->report_share_data_model->get_data_share_loan_balance_person();
        }

        if($switch_report['switch_code'] == '1') {
            if (@$_GET['export'] == "excel") {
                $this->load->view('report_share_data/coop_report_share_loan_balance_person_order_group_preview', $arr_data);
            } else {
                $this->preview_libraries->template_preview('report_share_data/coop_report_share_loan_balance_person_order_group_preview', $arr_data);
            }
        }

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
                $this->load->view('report_share_data/coop_report_share_loan_balance_excel', $arr_data);
            } else if (@$_GET['type_department'] == '2') {
                $this->load->view('report_share_data/coop_report_share_loan_balance_subdivision_excel', $arr_data);
            }
        }
		
	}

	function coop_report_share_loan_balance_person_excel(){
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

		//echo '<pre>'; print_r($row['data']); echo '</pre>'; exit;
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
		
		$this->load->view('report_share_data/coop_report_share_loan_balance_person_excel',$arr_data);	
	}

	public function coop_report_share_loan_balance_sms_person() {
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
		
		$this->db->select(array('max_period'));
		$this->db->from('coop_loan_atm_setting');					
		$rs_atm_setting = $this->db->get()->result_array();
		$row_atm_setting = @$rs_atm_setting[0];
		$max_period_atm = $row_atm_setting['max_period'];

		$arr_data = array();
		
		$where = "";
	
		//Get All data
		$rs = $this->db->select(array(
			'coop_mem_apply.member_id',
			'coop_mem_apply.mobile',
			'coop_mem_apply.department',
			'coop_mem_apply.faction',
			'coop_mem_apply.level',
			't1.mem_group_id as id',
			't1.mem_group_name as name',
			't2.mem_group_name as sub_name',
			't3.mem_group_name as main_name',
			't4.share_id',
			't5.loan_id',
			't5.loan_amount_balance',
			't5.contract_number',
			't5.loan_type',
			't6.loan_atm_id',
			't6.contract_number AS contract_number_atm',
			't6.loan_amount_balance_atm'
		))
		->from("(SELECT member_id, mobile, department, faction, level FROM coop_mem_apply) as coop_mem_apply")
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
		->join("(SELECT
						t3.member_id
						,t3.contract_number
						,t3.period_now
						,t3.loan_type
						,t1.loan_transaction_id
						,t1.loan_id
						,t1.loan_amount_balance
						,t1.transaction_datetime
					FROM
						coop_loan_transaction AS t1
					LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id 
					WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59'
					GROUP BY t1.loan_id
					ORDER BY t1.loan_id DESC ,t1.loan_transaction_id DESC
					) AS t5", "coop_mem_apply.member_id = t5.member_id", "left")
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
		->where("1=1  AND (t5.loan_id != '' OR t4.share_id != '' OR t6.loan_atm_id != '')")
		->order_by('coop_mem_apply.member_id ASC')
		->get()->result_array();

		//Get Lastest Share Information
		$member_ids = array_column($rs, 'member_id');
		$shares = $this->db->query("SELECT `t1`.`member_id`, `t1`.`share_date`, `t1`.`share_period`, `t1`.`share_collect_value`, `t1`.`share_collect`
									FROM `coop_mem_share` as `t1`
									INNER JOIN (SELECT member_id, share_date, MAX(cast(share_date as Datetime)) as max FROM coop_mem_share WHERE share_date BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by member_id)
											as t2 ON `t1`.`member_id` = `t2`.`member_id` AND `t1`.`share_date` = `t2`.`max`
									WHERE `t1`.`share_collect_value` > 0 AND t1.member_id IN  (".implode(',',$member_ids).")
									")->result_array();
		$share_members = array_column($shares, 'member_id');

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
									WHERE `t1`.`loan_amount_balance` > 0 AND {$where_loan}
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
									")->result_array();
		$loan_members = array_column($loans, 'loan_id');

		//Get Lastest Loan ATM Information
		$loan_atm_ids = array_column($rs, 'loan_atm_id');

        $where_atm = " AND  1=1 ";
        if(sizeof(array_filter($loan_atm_ids))){
            $where_atm = " AND t1.loan_atm_id IN  (".implode(',',array_filter($loan_atm_ids)).") ";
        }

		$loan_atms = $this->db->query("SELECT t1.loan_atm_transaction_id, `t1`.`loan_atm_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
									FROM `coop_loan_atm_transaction` as `t1`
									INNER JOIN (SELECT loan_atm_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_atm_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_atm_id)
											as t2 ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id` AND `t1`.`transaction_datetime` = `t2`.`max`
											WHERE `t1`.`loan_amount_balance` > 0 {$where_atm}
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_atm_transaction_id` DESC
									")->result_array();
		$loan_atm_members = array_column($loan_atms, 'loan_atm_id');

		$run_index = 0;
		$row = array();
		
		$check_row = "xx";
		$index = 0;
		foreach($rs AS $key2=>$value2){
			if($check_row != @$value2['member_id']){				
				$check_row = @$value2['member_id'];
				$share_period = in_array($value2['member_id'],$share_members) ? $shares[array_search($value2['member_id'],$share_members)]['share_period'] : "";
				$share_collect_value = in_array($value2['member_id'],$share_members) ? $shares[array_search($value2['member_id'],$share_members)]['share_collect_value'] : "";
			}

			$row['data'][$value2['member_id']]['member_id'] = $value2['member_id'];
			$row['data'][$value2['member_id']]['mobile'] = $value2['mobile'];

			//หุ้น
			$row['data'][$value2['member_id']]['share_period'] = $share_period;
			$row['data'][$value2['member_id']]['share_collect'] = $share_collect_value;
			
			$loan_type_code = $arr_loan_type_code[$value2['loan_type']];
			if($loan_type_code == 'emergent' &&  in_array($value2['loan_id'],$loan_members)){
				$loan = array();
				$loan['loan_emergent_period_now'] = $value2['period_now'];
				$loan['loan_emergent_contract_number'] = $value2['contract_number'];
				$loan['loan_emergent_balance'] = in_array($value2['loan_id'],$loan_members) ? $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'] : "";
				$row['data'][$value2['member_id']]['emergent'][$value2['contract_number']] = $loan;
				$run_emergent++;
			}

			if($loan_type_code == 'normal' && in_array($value2['loan_id'],$loan_members)){
				$loan = array();
				$loan['loan_emergent_period_now'] = $value2['period_now'];
				$loan['loan_emergent_contract_number'] = $value2['contract_number'];
				$loan['loan_emergent_balance'] = in_array($value2['loan_id'],$loan_members) ? $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'] : "";
				$row['data'][$value2['member_id']]['normal'][$value2['contract_number']] = $loan;
				$run_normal++;
			}
			
			if($loan_type_code == 'special' && in_array($value2['loan_id'],$loan_members)){
				$loan = array();
				$loan['loan_emergent_period_now'] = $value2['period_now'];
				$loan['loan_emergent_contract_number'] = $value2['contract_number'];
				$loan['loan_emergent_balance'] = in_array($value2['loan_id'],$loan_members) ? $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'] : "";
				$row['data'][$value2['member_id']]['special'][$value2['contract_number']] = $loan;
				$run_special++;
			}
			//เงินกู้ฉุกเฉิน ATM
			if(@$value2['loan_amount_balance_atm'] != '' && in_array($value2['loan_atm_id'],$loan_atm_members)){
				$loan = array();
				$loan['loan_emergent_period_now'] = '';
				$loan['loan_emergent_contract_number'] = $value2['contract_number_atm'];
				$loan['loan_emergent_balance'] = in_array($value2['loan_atm_id'],$loan_atm_members) ? $loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'] : "";
				$row['data'][$value2['member_id']]['emergent'][$value2['contract_number_atm']] = $loan;
			}

			$run_index++;		

		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row['data'];
		$arr_data['i'] = $i;
		
		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;
		
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		if($_GET['sms_file_type'] == "csv") {
			$this->load->view('report_share_data/coop_report_share_loan_balance_sms_person_csv',$arr_data);
		} else {
			$this->load->view('report_share_data/coop_report_share_loan_balance_sms_person_excel',$arr_data);
		}
	}

	///รายงานซื้อหุ้น 
	public function coop_report_buy_share(){
		$arr_data = array();
		
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;
		
		$this->libraries->template('report_share_data/coop_report_buy_share',$arr_data);
	}
	
	function coop_report_buy_share_preview(){
		$arr_data = array();	
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
			
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}	

		$where = " AND t1.share_status IN ('1','2') ";		
		if(@$get_start_date != ''){
			$where .= " AND t1.share_date BETWEEN '".$start_date." 00:00:00' AND '".$start_date." 23:59:59'";
		}
		
		if(@$_GET['level'] != ''){
			$where .= " AND t4.level = '".@$_GET['level']."'";
		}else if(@$_GET['faction'] != ''){
			$where .= " AND t4.faction = '".@$_GET['faction']."'";
		}else if(@$_GET['department'] != ''){
			$where .= " AND t4.department = '".@$_GET['department']."'";
		}
		
		$x=0;
		$join_arr = array();
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_apply AS t4';
		$join_arr[$x]['condition'] = 't4.member_id = t1.member_id';
		$join_arr[$x]['type'] = 'inner';	
		
		$x++;
		$join_arr[$x]['table'] = 'coop_prename';
		$join_arr[$x]['condition'] = 'coop_prename.prename_id = t4.prename_id';
		$join_arr[$x]['type'] = 'left';
		
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_group as t6';
		$join_arr[$x]['condition'] = 't6.id = t4.level';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_group as t7';
		$join_arr[$x]['condition'] = 't7.id = t6.mem_group_parent_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_group as t8';
		$join_arr[$x]['condition'] = 't8.id = t7.mem_group_parent_id';
		$join_arr[$x]['type'] = 'left';
		
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_apply_type AS t9';
		$join_arr[$x]['condition'] = 't9.apply_type_id = t4.apply_type_id';
		$join_arr[$x]['type'] = 'left';
		
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_type AS t10';
		$join_arr[$x]['condition'] = 't10.mem_type_id = t4.mem_type_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array(
												
												't1.member_id'
												,'t1.share_date'
												,'t1.share_early'
												,'t1.share_early_value'
												,'t1.share_bill'
												,'t1.pay_type'
												,'t4.firstname_th'
											    ,'t4.lastname_th'
												,'t4.department'
												,'t4.faction'
												,'t4.level'
												,'t9.apply_type_name'
												,'t10.mem_type_name'
												,'coop_prename.prename_short'
												,'t6.mem_group_id as mem_group_id'
												,'t6.mem_group_name as mem_group_name'
												,'t7.mem_group_name as sub_name'
												,'t8.mem_group_name as main_name'
												));
		$this->paginater_all_preview->main_table('coop_mem_share AS t1');
		$this->paginater_all_preview->where(" 1=1 {$where}");
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(26);
		$this->paginater_all_preview->page_limit_first(20);
		$this->paginater_all_preview->group_by('t1.member_id');
		$this->paginater_all_preview->order_by('t1.member_id,t1.share_date DESC ,t1.share_id DESC');
		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();
		
		//if(@$_GET['dev']=='dev'){
		//	print_r($this->db->last_query()); exit;
		//}
		
		foreach($row['data'] AS $key=>$value){
			foreach($value AS $key2=>$value2){
				if($value2['sub_name'] == '' || $value2['sub_name']=='ไม่ระบุ'){					
					$row['data'][$key][$key2]['sub_name'] = $value2['main_name'];
				}else{					
					$row['data'][$key][$key2]['sub_name'] = $value2['sub_name'];
				}
			}
		}
		//exit;
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];
		
		$this->preview_libraries->template_preview('report_share_data/coop_report_buy_share_preview',$arr_data);
		
	}	

	function check_report_buy_share(){
		if(@$_POST['start_date']){
				$start_date_arr = explode('/',@$_POST['start_date']);
				$start_day = $start_date_arr[0];
				$start_month = $start_date_arr[1];
				$start_year = $start_date_arr[2];
				$start_year -= 543;
				$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
				
				$start_date = $get_start_date;
				$end_date = $get_start_date;
				

			$where = " AND t1.share_status IN ('1','2') ";		
			if(@$get_start_date != ''){
				$where .= " AND t1.share_date BETWEEN '".$start_date." 00:00:00' AND '".$start_date." 23:59:59'";
			}
			
			if(@$_POST['level'] != ''){
				$where .= " AND t4.level = '".@$_POST['level']."'";
			}else if(@$_POST['faction'] != ''){
				$where .= " AND t4.faction = '".@$_POST['faction']."'";
			}else if(@$_POST['department'] != ''){
				$where .= " AND t4.department = '".@$_POST['department']."'";
			}	
				
			$rs_count = $this->db->select(array(
								't1.member_id'
								))
			->from("coop_mem_share AS t1")		
			->join("coop_mem_apply AS t4","t4.member_id = t1.member_id","inner")			
			->where("1=1 {$where}")
			->group_by('t1.member_id')
			->order_by("t1.member_id,t1.share_date DESC ,t1.share_id DESC")
			->get()->result_array();
		}

		if(!empty($rs_count)){
			echo "success";
		}else{
			echo "";
		}
	}	
	
	function coop_report_buy_share_excel(){
		$arr_data = array();
		set_time_limit (180);
		$this->db->save_queries = FALSE;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
			
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}	

		$where = " AND t1.share_status IN ('1','2') ";		
		if(@$get_start_date != ''){
			$where .= " AND t1.share_date BETWEEN '".$start_date." 00:00:00' AND '".$start_date." 23:59:59'";
		}
		
		if(@$_GET['level'] != ''){
			$where .= " AND t4.level = '".@$_GET['level']."'";
		}else if(@$_GET['faction'] != ''){
			$where .= " AND t4.faction = '".@$_GET['faction']."'";
		}else if(@$_GET['department'] != ''){
			$where .= " AND t4.department = '".@$_GET['department']."'";
		}	
			
		$row['data'] = $this->db->select(array(
							't1.member_id'
							,'t1.share_date'
							,'t1.share_date'
							,'t1.share_early'
							,'t1.share_early_value'
							,'t1.share_bill'
							,'t1.pay_type'
							,'t4.firstname_th'
							,'t4.lastname_th'
							,'t4.department'
							,'t4.faction'
							,'t4.level'
							,'t9.apply_type_name'
							,'t10.mem_type_name'
							,'coop_prename.prename_short'
							,'t6.mem_group_id as mem_group_id'
							,'t6.mem_group_name as mem_group_name'
							,'t7.mem_group_name as sub_name'
							,'t8.mem_group_name as main_name'
							))
		->from("coop_mem_share AS t1")		
		->join("coop_mem_apply AS t4","t4.member_id = t1.member_id","inner")		
		->join("coop_prename","coop_prename.prename_id = t4.prename_id","left")
		->join("coop_mem_group as t6","t6.id = t4.level","inner")
		->join("coop_mem_group as t7","t7.id = t6.mem_group_parent_id","left")
		->join("coop_mem_group as t8","t8.id = t7.mem_group_parent_id","left")		
		->join("coop_mem_apply_type AS t9","t9.apply_type_id = t4.apply_type_id","left")		
		->join("coop_mem_type AS t10","t10.mem_type_id = t4.mem_type_id","left")			
		->where("1=1 {$where}")
		->group_by('t1.member_id')
		->order_by("t1.member_id,t1.share_date DESC ,t1.share_id DESC")
		->get()->result_array();
		
		// echo '<pre>'; print_r($row['data']); echo '</pre>'; exit;
		foreach($row['data'] AS $key=>$value){
			if($value['sub_name'] == '' || $value['sub_name']=='ไม่ระบุ'){					
				$row['data'][$key]['sub_name'] = $value['main_name'];
			}else{					
				$row['data'][$key]['sub_name'] = $value['sub_name'];
			}
		}
		
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];	
		
		$this->load->view('report_share_data/coop_report_buy_share_excel',$arr_data);
		
	}

	public function coop_report_share_transaction(){
		$arr_data = array();

		if($_GET['member_id']!=''){
			$member_id = $_GET['member_id'];
		}else{
			$member_id = '';
		}
		$arr_data['member_id'] = $member_id;

		$member_name = '';
		if($member_id != '') {
			$member_info = $this->db->select(array('t1.member_id', 't1.firstname_th', 't1.lastname_th', 't2.prename_full'))
									->from('coop_mem_apply as t1')
									->join("coop_prename as t2","t2.prename_id = t1.prename_id","left")
									->where("t1.member_id = '{$member_id}'")
									->get()->row();
			$member_name = $member_info->prename_full.$member_info->firstname_th." ".$member_info->lastname_th;
		}

		$arr_data['member_name'] = $member_name;

		$this->libraries->template('report_share_data/coop_report_share_transaction',$arr_data);
	}

	public function coop_report_share_transaction_preview() {
		$arr_data = array();

		$dateCon = "";
		if(@$_GET['start_date'] && $_GET['type'] == '2'){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			$dateCon .= "share_date >= '".$start_date." 00:00:00' AND ";
		}

		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
			$dateCon .= "share_date <= '".$end_date." 23:59:59'";
		}

		$memberInfo = $this->db->select("firstname_th, lastname_th, prename_full")
								->from("coop_mem_apply")
								->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id","left")
								->where("member_id = '".$_GET['member_id']."'")
								->get()->row();

		$member_name = " - ";
		if (!empty($memberInfo)) {
			$member_name = $memberInfo->prename_full.$memberInfo->firstname_th." ".$memberInfo->lastname_th;
		}

		$shares = $this->db->select("t1.member_id, t1.admin_id, t1.share_type, t1.share_status, t1.share_bill, t1.share_date, t1.share_payable, t1.share_payable_value,
										t1.share_early, t1.share_early_value, t1.share_collect, t1.share_collect_value, t2.user_name")
							->from('coop_mem_share as t1')
							->where("member_id = '".$_GET['member_id']."' AND ".$dateCon)
							->join("coop_user as t2","t1.admin_id = t2.user_id","left")
							->order_by('share_date ASC, share_id ASC')
							->get()->result_array();
		if(@$_GET['dev'] == 'dev'){
			echo $this->db->last_query(); 
			echo '<hr>';
		}
		$shareCount = count($shares);
		$firstPage = 20;
		$perPage = 30;
		
		$total = 0;
		$row = array();
		$page = 0;
		$duplicate_index = 0;
		foreach($shares as $index => $share) {
			if (($index + $duplicate_index - 1) <= $firstPage) {
				$page = 1;
			} else {
				$page = ceil(($index + $duplicate_index - $firstPage -1)/$perPage)+1;
			}
			$data = $share;
			$data['operation'] = "add";
			if ($share['share_type'] == 'SPM' && ($share['share_status'] == '1' || $share['share_status'] == '2')) {
				$data['status'] = "หักส่งรายเดือน";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SPM' && $share['share_status'] == '3') {
				$data['status'] = "หักส่งรายเดือน";
				$data['share_collect'] = $data['share_early'] + $data['share_payable'];
				$data['share_collect_value'] = $data['share_early_value'] + $data['share_payable_value'];
				$row['data'][$page][] = $data;
				$cancelData = $share;
				$cancelData['status'] = "ยกเลิกรายการ";
				$cancelData['operation'] = "subtract";
				$cancelData['share_collect'] = $data['share_collect'] - $data['share_early'];
				$cancelData['share_collect_value'] = $data['share_collect_value'] - $data['share_early_value'];
				$row['data'][$page][] = $cancelData;
				$duplicate_index++;
			} else if ($share['share_type'] == 'SPA' && ($share['share_status'] == '1' || $share['share_status'] == '2')) {
				$data['status'] = "ซื้อพิเศษ";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SPA' && $share['share_status'] == '3') {
				$data['status'] = "ซื้อพิเศษ";
				$data['share_collect'] = $data['share_early'] + $data['share_payable'];
				$data['share_collect_value'] = $data['share_early_value'] + $data['share_payable_value'];
				$row['data'][$page][] = $data;
				$cancelData = $share;
				$cancelData['status'] = "ยกเลิกรายการ";
				$cancelData['operation'] = "subtract";
				$cancelData['share_collect'] = $data['share_collect'] - $data['share_early'];
				$cancelData['share_collect_value'] = $data['share_collect_value'] - $data['share_early_value'];
				$row['data'][$page][] = $cancelData;
				$duplicate_index++;
			} else if ($share['share_type'] == 'SPL' && ($share['share_status'] == '1' || $share['share_status'] == '2')) {
				$data['status'] = "ซื้อจากการกู้";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SPL' && $share['share_status'] == '3') {
				$data['status'] = "ซื้อจากการกู้";
				$data['share_collect'] = $data['share_early'] + $data['share_payable'];
				$data['share_collect_value'] = $data['share_early_value'] + $data['share_payable_value'];
				$row['data'][$page][] = $data;
				$cancelData = $share;
				$cancelData['status'] = "ยกเลิกรายการ";
				$cancelData['operation'] = "subtract";
				$cancelData['share_collect'] = $data['share_collect'] - $data['share_early'];
				$cancelData['share_collect_value'] = $data['share_collect_value'] - $data['share_early_value'];
				$row['data'][$page][] = $cancelData;
				$duplicate_index++;
			} else if ($share['share_type'] == 'SRF') {
				$data['status'] = "คืนเงิน";
				$data['operation'] = "subtract";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SRP') {
				$data['status'] = "ถอนหุ้น";
				$data['operation'] = "subtract";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SPD') {
				$data['status'] = "ยกเลิกรายการ";
				$data['operation'] = "subtract";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SDP') {
				$data['status'] = "ชำระหนี้คงค้าง";
				$row['data'][$page][] = $data;
			}

			if (($shareCount-1) == $index) {
				$total = $share['share_collect_value'];
			}			
		}

		$page_all = $shareCount <= $firstPage ? 1 : ceil(($shareCount - $firstPage)/$perPage)+1;

		$arr_data['data'] = $row['data'];
		$arr_data['start_date'] = $start_date;
		$arr_data['end_date'] = $end_date;
		$arr_data['page_all'] = $page_all;
		$arr_data['total'] = $total;
		$arr_data['member_name'] = $member_name;
		$this->preview_libraries->template_preview('report_share_data/coop_report_share_transaction_preview',$arr_data);
	}

	public function coop_report_share_transaction_excel() {
		$arr_data = array();

		$dateCon = "";
		if(@$_GET['start_date'] && $_GET['type'] == '2'){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			$dateCon .= "share_date >= '".$start_date." 00:00:00' AND ";
		}

		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
			$dateCon .= "share_date <= '".$end_date." 23:59:59'";
		}

		$memberInfo = $this->db->select("firstname_th, lastname_th, prename_full")
								->from("coop_mem_apply")
								->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id","left")
								->where("member_id = '".$_GET['member_id']."'")
								->get()->row();

		$member_name = " - ";
		if (!empty($memberInfo)) {
			$member_name = $memberInfo->prename_full.$memberInfo->firstname_th." ".$memberInfo->lastname_th;
		}

		$shares = $this->db->select("t1.member_id, t1.admin_id, t1.share_type, t1.share_status, t1.share_bill, t1.share_date, t1.share_payable, t1.share_payable_value,
										t1.share_early, t1.share_early_value, t1.share_collect, t1.share_collect_value, t2.user_name")
							->from('coop_mem_share as t1')
							->where("member_id = '".$_GET['member_id']."' AND ".$dateCon)
							->join("coop_user as t2","t1.admin_id = t2.user_id","left")
							->order_by('share_date, share_id')
							->get()->result_array();

		$shareCount = count($shares);
		$firstPage = 20;
		$perPage = 30;
		
		$total = 0;
		$row = array();
		$page = 0;
		$duplicate_index = 0;
		foreach($shares as $index => $share) {
			if (($index + $duplicate_index - 1) <= $firstPage) {
				$page = 1;
			} else {
				$page = ceil(($index + $duplicate_index - $firstPage -1)/$perPage)+1;
			}
			$data = $share;
			$data['operation'] = "add";
			if ($share['share_type'] == 'SPM' && ($share['share_status'] == '1' || $share['share_status'] == '2')) {
				$data['status'] = "หักส่งรายเดือน";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SPM' && $share['share_status'] == '3') {
				$data['status'] = "หักส่งรายเดือน";
				$data['share_collect'] = $data['share_early'] + $data['share_payable'];
				$data['share_collect_value'] = $data['share_early_value'] + $data['share_payable_value'];
				$row['data'][$page][] = $data;
				$cancelData = $share;
				$cancelData['status'] = "ยกเลิกรายการ";
				$cancelData['operation'] = "subtract";
				$cancelData['share_collect'] = $data['share_collect'] - $data['share_early'];
				$cancelData['share_collect_value'] = $data['share_collect_value'] - $data['share_early_value'];
				$row['data'][$page][] = $cancelData;
				$duplicate_index++;
			} else if ($share['share_type'] == 'SPA' && ($share['share_status'] == '1' || $share['share_status'] == '2')) {
				$data['status'] = "ซื้อพิเศษ";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SPA' && $share['share_status'] == '3') {
				$data['status'] = "ซื้อพิเศษ";
				$data['share_collect'] = $data['share_early'] + $data['share_payable'];
				$data['share_collect_value'] = $data['share_early_value'] + $data['share_payable_value'];
				$row['data'][$page][] = $data;
				$cancelData = $share;
				$cancelData['status'] = "ยกเลิกรายการ";
				$cancelData['operation'] = "subtract";
				$cancelData['share_collect'] = $data['share_collect'] - $data['share_early'];
				$cancelData['share_collect_value'] = $data['share_collect_value'] - $data['share_early_value'];
				$row['data'][$page][] = $cancelData;
				$duplicate_index++;
			} else if ($share['share_type'] == 'SPL' && ($share['share_status'] == '1' || $share['share_status'] == '2')) {
				$data['status'] = "ซื้อจากการกู้";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SPL' && $share['share_status'] == '3') {
				$data['status'] = "ซื้อจากการกู้";
				$data['share_collect'] = $data['share_early'] + $data['share_payable'];
				$data['share_collect_value'] = $data['share_early_value'] + $data['share_payable_value'];
				$row['data'][$page][] = $data;
				$cancelData = $share;
				$cancelData['status'] = "ยกเลิกรายการ";
				$cancelData['operation'] = "subtract";
				$cancelData['share_collect'] = $data['share_collect'] - $data['share_early'];
				$cancelData['share_collect_value'] = $data['share_collect_value'] - $data['share_early_value'];
				$row['data'][$page][] = $cancelData;
				$duplicate_index++;
			} else if ($share['share_type'] == 'SRF') {
				$data['status'] = "คืนเงิน";
				$data['operation'] = "subtract";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SRP') {
				$data['status'] = "ถอนหุ้น";
				$data['operation'] = "subtract";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SPD') {
				$data['status'] = "ยกเลิกรายการ";
				$data['operation'] = "subtract";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SDP') {
				$data['status'] = "ชำระหนี้คงค้าง";
				$row['data'][$page][] = $data;
			}

			if (($shareCount-1) == $index) {
				$total = $share['share_collect_value'];
			}			
		}

		$page_all = $shareCount <= $firstPage ? 1 : ceil(($shareCount - $firstPage)/$perPage)+1;

		$arr_data['data'] = $row['data'];
		$arr_data['start_date'] = $start_date;
		$arr_data['end_date'] = $end_date;
		$arr_data['page_all'] = $page_all;
		$arr_data['total'] = $total;
		$arr_data['member_name'] = $member_name;
		
		$this->load->view('report_share_data/coop_report_share_transaction_excel',$arr_data);
	}

	public function check_coop_report_share_transaction() {
		$dateCon = "";
		if(@$_GET['start_date'] && $_GET['type'] == '2'){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			$dateCon .= "share_date >= '".$start_date." 00:00:00' AND ";
		}

		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
			$dateCon .= "share_date <= '".$end_date." 23:59:59'";
		}

		$shares = $this->db->select("t1.member_id, t1.admin_id, t1.share_type, t1.share_status, t1.share_bill, t1.share_date, t1.share_payable, t1.share_payable_value,
										t1.share_early, t1.share_early_value, t1.share_collect, t1.share_collect_value, t2.user_name")
							->from('coop_mem_share as t1')
							->where("member_id = '".$_GET['member_id']."' AND ".$dateCon)
							->join("coop_user as t2","t1.admin_id = t2.user_id","left")
							->order_by('share_date, share_id')
							->get()->result_array();
		if(!empty($shares)){
			echo "success";
		}else{
			echo "";
		}	
	}

}
