<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Report_withdrawal_data extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	public function report_summary_withdrawal()
	{
		$arr_data = array();
		$this->db->select(array('t1.type_id', 't1.type_name', 't1.type_code'));
		$this->db->from('coop_deposit_type_setting as t1');
		$row = $this->db->get()->result_array();
		$arr_data['type_id'] = $row;
		$this->libraries->template('report_withdrawal_data/report_summary_withdrawal', $arr_data);
	}

	function summary_withdrawal_check()
	{
		if (@$_POST['start_date']) {
			$start_date_arr = explode('/', @$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year . '-' . $start_month . '-' . $start_day;
		}
		if (@$_POST['end_date']) {
			$end_date_arr = explode('/', @$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year . '-' . $end_month . '-' . $end_day;
		}else{
			$end_date = $start_date;
		}
		$where = "t2.transaction_time BETWEEN '" . $start_date . " 00:00:00.000' AND '" . $end_date . " 23:59:59.000'";
		if (!empty($_POST['type_id'])) $where .= " AND t1.type_id = '" . $_POST['type_id'] . "'";

		$rs = $this->db->select(array('t1.account_id', 't1.account_name', 't1.mem_id', "t2.transaction_balance", "t2.transaction_deposit", "t2.transaction_withdrawal", "t2.transaction_time"))
			->from('coop_maco_account as t1')
			->join("(SELECT * FROM coop_account_transaction WHERE transaction_time BETWEEN '" . $start_date . " 00:00:00.000' AND '" . $end_date . " 23:59:59.000'  AND transaction_withdrawal > 0) as t2", "t1.account_id = t2.account_id", "inner")
			->order_by("t2.transaction_time")
			->where($where)->get()->result_array();
		if (!empty($rs)) {
			echo "success";
		} else {
			echo "";
		}
	}
	function report_summary_withdrawal_pdf()
	{
		$arr_data = array();
		$this->db->select(array('t1.type_id', 't1.type_name'));
		$this->db->from('coop_deposit_type_setting as t1');
		$rs_type = $this->db->get()->result_array();
		$arr_type_deposit = array();
		foreach ($rs_type as $key => $row_type) {
			$arr_type_deposit[$row_type['type_id']] = $row_type['type_name'];
		}
		$arr_data['type_deposit'] = $arr_type_deposit;
		$arr_data['month_arr'] = $this->center_function->month_arr();
		if (@$_GET['start_date']) {
			$start_date_arr = explode('/', @$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year . '-' . $start_month . '-' . $start_day;
		}
		if (@$_GET['end_date']) {
			$end_date_arr = explode('/', @$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year . '-' . $end_month . '-' . $end_day;
		}else{
			$end_date = $start_date;
		}
		$where = "1=1";
		if (!empty($_GET['type_id'])) $where .= " AND t1.type_id = '" . $_GET['type_id'] . "'";
		$x = 0;
		$join_arr = array();
		$join_arr[$x]['table'] = "(SELECT transaction_id,account_id,transaction_time,transaction_withdrawal FROM coop_account_transaction WHERE transaction_time BETWEEN '" . $start_date . " 00:00:00.000' AND '" . $end_date . " 23:59:59.000'  AND transaction_withdrawal > 0) as t2";
		$join_arr[$x]['condition'] = "t1.account_id = t2.account_id";
		$join_arr[$x]['type'] = 'inner';
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array(
			't1.account_id',
			't1.account_name',
			"t2.transaction_withdrawal",
			"t2.transaction_time"
		));
		$this->paginater_all->main_table('coop_maco_account as t1');
		$this->paginater_all->where($where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(100);
		$this->paginater_all->page_link_limit(36);
		$this->paginater_all->join_arr($join_arr);
		$this->paginater_all->order_by('t2.transaction_time, t2.transaction_id');
		$row = $this->paginater_all->paginater_process();
		$paging = $this->pagination_center->paginating(intval($row['page']), $row['num_rows'], $row['per_page'], $row['page_link_limit'], @$_GET); //$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$runno = (($row['page'] * 100) - 100) + 1;
		foreach ($row['data'] as $key2 => $value2) {
			$row['data'][$key2]['runno'] = $runno;
			$runno++;
		}
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = ceil($row['num_rows'] / $row['per_page']);
		$arr_data['page'] = $row['page'];
		
		$this->load->view('report_withdrawal_data/report_summary_withdrawal_pdf', $arr_data);
	}
}
