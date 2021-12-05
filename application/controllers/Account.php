<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $this->month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
    }

    public function index() {
        $arr_data['open_account_year_user_active'] =  0;//set default data.
        if(!empty($_POST) && (!empty($_POST['from_date']) || !empty($_POST['thru_date'])|| !empty($_POST['journal_ref']) || !empty($_POST['journal_type']))) {
            $where = "(account_status != 2 OR account_status is null)";
            if(!empty($_POST['from_date'])) {
                $where .= " AND t1.account_datetime >= '".$this->center_function->ConvertToSQLDate($_POST['from_date'])."'";
            }
            if(!empty($_POST['thru_date'])) {
                $where .= " AND t1.account_datetime <= '".$this->center_function->ConvertToSQLDate($_POST['thru_date'])."'";
            }
            if(!empty($_POST['journal_ref'])) {
                $where .= " AND t1.journal_ref  LIKE '%".$_POST['journal_ref']."%'";
            }
            if(!empty($_POST['journal_type'])) {
                $where .= is_array($_POST['journal_type']) ? " AND t1.journal_type  IN ('".implode("','", $_POST['journal_type'])."')" : "";
            }

            $join_arr = array();
            $this->paginater_all->type(DB_TYPE);
            $this->paginater_all->select('t1.account_id');
            $this->paginater_all->main_table('coop_account t1');
            $this->paginater_all->where($where);
            $this->paginater_all->page_now(@$_POST["page"]);
            $this->paginater_all->per_page(100);
            $this->paginater_all->page_link_limit(20);
            $this->paginater_all->order_by('t1.account_datetime, t1.account_id DESC');
            $this->paginater_all->join_arr($join_arr);
            $row = $this->paginater_all->paginater_process();

            $account_ids = array_column($row['data'], 'account_id');

            $data_account_detail = array();
            if(!empty($account_ids)) {
                $accounts = $this->db->select("t1.account_id, t1.account_datetime, t1.account_description, t1.run_status, t1.journal_type, t1.journal_ref,
                                                t2.account_detail_id, t2.account_chart_id, t2.account_type, t2.account_amount,
                                                t3.account_chart,
                                                t4.is_close")
                                        ->from("coop_account as t1")
                                        ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "left")
                                        ->join("coop_account_chart as t3", "t2.account_chart_id = t3.account_chart_id", "left")
                                        ->join("coop_account_year as t4", "t1.budget_year = t4.account_year", "left")
                                        ->where("t1.account_id  IN ('".implode("','", $account_ids)."')")
                                        ->order_by("t2.account_type desc, t1.account_id")
                                        ->get()->result_array();
                foreach($accounts as $key => $row_all) {
                    $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_chart_id'] = $row_all['account_chart_id'];
                    $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_chart'] = $row_all['account_chart'];
                    $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_type'] = $row_all['account_type'];
                    $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_amount'] += $row_all['account_amount'];
                    $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_datetime'] = $row_all['account_datetime'];
                    $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_id'] = $row_all['account_id'];
                    $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_description'] = $row_all['account_description'];
                    $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['run_status'] = $row_all['run_status'];
                    $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['journal_type'] = $row_all['journal_type'];
                    $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['journal_ref'] = $row_all['journal_ref'];
                    $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['is_close'] = $row_all['is_close'];
                    $count = $this->db->select("COUNT(*) as count")->from("coop_account")->where("journal_ref = '".$row_all['journal_ref']."' AND account_status = 2")->get()->row_array();
                    $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['count'] = $row_all['count'];
                }
            }

            $arr_data['data_account_detail'] = $data_account_detail;
            $arr_data['paging'] = $this->pagination_center->paginating_post($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_POST);
            $arr_data['daily_status'] = 0;
        } else {
            //Get setting.
            $allow_flexible = $this->db->select("*")->from("coop_account_setting")->where("type = 'allow_flexible_generate' AND value = 1")->get()->row_array();
            $arr_data['allow_flexible'] = $allow_flexible;

            $account_dates = $this->db->select("account_datetime")->from("coop_account")->where("(account_status != 2 OR account_status is null)")->order_by("account_datetime desc")->group_by("account_datetime")->get()->result_array();
            $page_names = array();
            $index = 0;
            foreach($account_dates as $account_date) {
                $page_names[$index++] = $this->center_function->ConvertToThaiDate($account_date["account_datetime"], 1, 0, 0);
            }

            $page_index = !empty($_GET["page"]) ? (int) $_GET["page"] : 1;

            $paging = $this->pagination_center->paginating_with_name($page_index, count($account_dates), 1, 20, $_GET, $page_names);

            $data_account_detail = array();
            $accounts = $this->db->select("t1.account_id, t1.account_datetime, t1.account_description, t1.run_status, t1.journal_type, t1.journal_ref,
                                            t2.account_detail_id, t2.account_chart_id, t2.account_type, t2.account_amount,
                                            t3.account_chart,
                                            t4.is_close")
                                    ->from("coop_account as t1")
                                    ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "left")
                                    ->join("coop_account_chart as t3", "t2.account_chart_id = t3.account_chart_id", "left")
                                    ->join("coop_account_year as t4", "t1.budget_year = t4.account_year", "left")
                                    ->where("t1.account_datetime = '".$account_dates[$page_index-1]["account_datetime"]."' AND (t1.account_status != 2 OR t1.account_status is null)")
                                    ->order_by("t2.account_type desc, t1.account_id")
                                    ->get()->result_array();

            foreach($accounts as $key => $row_all) {
                $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_chart_id'] = $row_all['account_chart_id'];
                $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_chart'] = $row_all['account_chart'];
                $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_type'] = $row_all['account_type'];
                $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_amount'] += $row_all['account_amount'];
                $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_datetime'] = $row_all['account_datetime'];
                $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_id'] = $row_all['account_id'];
                $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_description'] = $row_all['account_description'];
                $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['run_status'] = $row_all['run_status'];
                $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['journal_type'] = $row_all['journal_type'];
                $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['journal_ref'] = $row_all['journal_ref'];
                $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['is_close'] = $row_all['is_close'];
                $count = $this->db->select("COUNT(*) as count")->from("coop_account")->where("journal_ref = '".$row_all['journal_ref']."' AND account_status = 2 AND cancel_date IS NULL")->get()->row_array();
                $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['count'] = $count['count'];
            }
            $arr_data['data_account_detail'] = $data_account_detail;
            $arr_data['paging'] = $paging;

            $daily_status = $this->db->select("*")->from("coop_account_date")->where("date = '".$account_dates[$page_index-1]["account_datetime"]."'")->get()->row_array();
            $arr_data['daily_status'] = !empty($daily_status) ? $daily_status['is_close'] : 0;
            $arr_data['account_date'] = $account_dates[$page_index-1]["account_datetime"];

            $setting = $this->db->select("value")->from("coop_account_setting")->where("type = 'open_account_year_user_active'")->get()->row_array();
            if(!empty($setting)) {
                $open_account_year_user_active = json_decode($setting['value']);
                if(in_array($_SESSION['USER_ID'], $open_account_year_user_active)) {
                    $arr_data['open_account_year_user_active'] =  1;
                }
            }

            $account_year = $this->db->select("is_close")->from("coop_account_year")->where("start_date <= '".$account_dates[$page_index-1]["account_datetime"]."' AND end_date >= '".$account_dates[$page_index-1]["account_datetime"]."'")->get()->row_array();
            $arr_data['year_is_close'] =  $account_year['is_close'];
        }
        $account_chart = $this->db->select("account_chart_id, account_chart")->from("coop_account_chart")->where("type = 3 AND cancel_status IS NULL")->get()->result_array();
        $arr_data['account_chart'] = $account_chart;
        $arr_data['space'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

        //For close budget_year.
        $first_open_budget_year = $this->db->select("account_year")->from("coop_account_year")->where("is_close = 0")->order_by('start_date')->get()->row();
        if(!empty($first_open_budget_year)) {
            $arr_data['first_open_budget_year'] = $first_open_budget_year->account_year;
        }
        $last_close_budget_year = $this->db->select("account_year")->from("coop_account_year")->where("is_close = 1")->order_by('end_date DESC')->get()->row();
        if(!empty($last_close_budget_year)) {
            $arr_data['last_close_budget_year'] = $last_close_budget_year->account_year;
        }

        $enable_edit_delete_permission_setting = $this->db->select("value")->from("coop_account_setting")->where("type = 'enable_edit_delete_permission'")->get()->row();
        $arr_data['enable_edit_delete_permission'] = !empty($enable_edit_delete_permission_setting) ? $enable_edit_delete_permission_setting->value : 0;

        $this->libraries->template('account/index',$arr_data);
    }

    public function index_tmp_auto() {
        $arr_data = array();

        $x=0;
        $join_arr = array();
        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('CAST(account_datetime AS date) AS Converted');
        $this->paginater_all->main_table('coop_account_tmp_auto');
        $this->paginater_all->where("status_audit is null");
        $this->paginater_all->page_now(@$_GET["page"]);
        $this->paginater_all->per_page(10);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->group_by('Converted,account_description');
        $this->paginater_all->order_by('account_id DESC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();

        $b = 0;
        $max_data_val = 0;
        $max_data_val = $b+1;

        //  กำหนดจำนวนหน้าใน page นั้นๆ
        $this->db->select(array('*'));
        $this->db->from('coop_account_tmp_auto AS t1');
        $this->db->join('(SELECT CAST(account_datetime AS date) AS Converted FROM coop_account_tmp_auto GROUP BY Converted, account_description) AS t2','CAST(t1.account_datetime AS date) = t2.Converted','inner');
        $this->db->where("t1.status_audit is null AND t1.account_status is null  ");
        $this->db->group_by('Converted,account_description');

        $row_count = $this->db->get()->result_array();
        foreach ($row_count as $key=>$val){ $b ++; }

        $max_data_val =$b+1 ;
        $paging = $this->pagination_center->paginating($row['page'], $max_data_val, $row['per_page'], $row['page_link_limit']);

        $i =  $row['page_start'];
        $arr_data['num_rows'] = $max_data_val;
        $arr_data['paging'] = $paging;
        $arr_data['i'] = $i;

        $row = '';
        $this->db->select(array('t0.*','t1.*','t2.account_chart'));
        $this->db->from('coop_account_tmp_auto as t0 ');
        $this->db->join('coop_account_detail_tmp_auto as t1','t0.account_id = t1.account_id','inner');
        $this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','inner');
        $this->db->where("t0.status_audit is null AND t0.account_status is null  ");
        $this->db->order_by("account_type DESC ,account_detail_id DESC");
        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;
        $data_account_detail = array();

        foreach($row['data'] as $key => $row_all) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row_all['account_datetime']);
            foreach($row_all as $key2 => $row_detail_all){
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail_all['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_description']][$row_detail_all['account_chart_id']]['account_chart_id'] = $row_detail_all['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_description']][$row_detail_all['account_chart_id']]['account_chart'] = $row_detail_all['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_description']][$row_detail_all['account_chart_id']]['account_type'] = $row_detail_all['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_description']][$row_detail_all['account_chart_id']]['account_amount'] += $row_detail_all['account_amount'];
            }
        }
        $sort_array = array_map(function($array_account_datetime){
            return array_map(function($var){
                ksort($var);
                return $var;
            },$array_account_datetime);

        },$data_account_detail);

        //ตั้งค่าว่ารายการในแต่ละหน้าจะมีกี่รายการ ตั้งแต่รายการที่เท่าไรถึงเท่าไร
        if(empty($_GET["page"])){
            $firest_p = 1;
        }else{
            $firest_p =$_GET["page"];
        }
        $max_list = (10 *(@$firest_p));
        $min_list = (10 *(@$firest_p-1));

        $arr_data['max_list']  = $max_list;
        $arr_data['min_list']  = $min_list;
        //ตั้งค่าว่ารายการในแต่ละหน้าจะมีกี่รายการ ตั้งแต่รายการที่เท่าไรถึงเท่าไร

        $arr_data['data'] = $row['data'];
        $arr_data['space'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

        $this->libraries->template('account/index_tmp_auto',$arr_data);
    }

    public function index_status_audit() {
        $arr_data = array();

        $x=0;
        $join_arr = array();
        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('CAST(account_datetime AS date) AS Converted');
        $this->paginater_all->main_table('coop_account');
        $this->paginater_all->where("status_audit  = 1 ");
        $this->paginater_all->page_now(@$_GET["page"]);
        $this->paginater_all->per_page(10);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->group_by('Converted,account_description');
        $this->paginater_all->order_by('account_id DESC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();

        $b = 0;
        $max_data_val = 0;
        $max_data_val = $b+1;
        //กำหนดจำนวนหน้าใน page นั้นๆ
        $this->db->select(array('*'));
        $this->db->from('coop_account AS t1');
        $this->db->join('(SELECT CAST(account_datetime AS date) AS Converted FROM coop_account GROUP BY Converted, account_description) AS t2','CAST(t1.account_datetime AS date) = t2.Converted','inner');
        $this->db->where("t1.status_audit = 1  AND t1.account_status is null  ");
        $this->db->group_by('Converted,account_description');

        $row_count = $this->db->get()->result_array();
        foreach ($row_count as $key=>$val){ $b ++; }

        $max_data_val =$b+1 ;
        $paging = $this->pagination_center->paginating($row['page'], $max_data_val, $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
        $i =  $row['page_start'];
        $arr_data['num_rows'] = $max_data_val;
        $arr_data['paging'] = $paging;
        $arr_data['i'] = $i;

        $row = '';
        $this->db->select(array('t0.*','t1.*','t2.account_chart'));
        $this->db->from('coop_account as t0 ');
        $this->db->join('coop_account_detail as t1','t0.account_id = t1.account_id','inner');
        $this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','inner');
        $this->db->where("t0.status_audit = 1  AND t0.account_status is null  ");
        $this->db->order_by("account_type DESC ,account_detail_id DESC");
        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;
        $data_account_detail = array();

        foreach($row['data'] as $key => $row_all) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row_all['account_datetime']);
            foreach($row_all as $key2 => $row_detail_all){
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail_all['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_description']][$row_detail_all['account_chart_id']]['account_chart_id'] = $row_detail_all['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_description']][$row_detail_all['account_chart_id']]['account_chart'] = $row_detail_all['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_description']][$row_detail_all['account_chart_id']]['account_type'] = $row_detail_all['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_description']][$row_detail_all['account_chart_id']]['account_amount'] += $row_detail_all['account_amount'];
            }
        }
        $sort_array = array_map(function($array_account_datetime){
            return array_map(function($var){
                ksort($var);
                return $var;
            },$array_account_datetime);

        },$data_account_detail);

        //ตั้งค่าว่ารายการในแต่ละหน้าจะมีกี่รายการ ตั้งแต่รายการที่เท่าไรถึงเท่าไร
        if(empty($_GET["page"])){
            $firest_p = 1;
        }else{
            $firest_p =$_GET["page"];
        }
        $max_list = (10 *(@$firest_p));
        $min_list = (10 *(@$firest_p-1));

        $arr_data['max_list']  = $max_list;
        $arr_data['min_list']  = $min_list;
        //ตั้งค่าว่ารายการในแต่ละหน้าจะมีกี่รายการ ตั้งแต่รายการที่เท่าไรถึงเท่าไร
        $arr_data['data'] = $row['data'];
        $arr_data['space'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

        $this->libraries->template('account/index_status_audit',$arr_data);
    }

    public function account_save_status_audit() {
        $data = $_POST['data'];
        $account_datetime_arr = explode('/',$data['coop_account']['account_datetime']);
            if($account_datetime_arr[1] == '12'){
                $account_datetime_arr[2] = $account_datetime_arr[2]-1;
            }
        $data['coop_account']['account_datetime'] = ($account_datetime_arr[2]-543)."-".sprintf('%02d',11)."-".sprintf('%02d',30);

        $data_insert = array();
        $data_insert['account_description'] = $data['coop_account']['account_description'];
        $data_insert['account_datetime'] = $data['coop_account']['account_datetime'];
        $data_insert['status_audit'] = '1';
        $data_insert['process'] = 'add_manual';
        $this->db->insert('coop_account', $data_insert);

        $account_id = $this->db->insert_id();

        foreach($data['coop_account_detail'] as $key => $value){

            $data_insert = array();
            $data_insert['account_id'] = $account_id;
            $data_insert['account_type'] = $value['account_type'];
            $data_insert['account_amount'] = $value['account_amount'];
            $data_insert['account_chart_id'] = $value['account_chart_id'];
            $this->db->insert('coop_account_detail', $data_insert);

        }
        echo"<script> document.location.href='".base_url(PROJECTPATH.'/account/index_status_audit')."'; </script>";
    }

    public function account_save() {
        $data = $_POST['data'];
        $process_timestamp = date('Y-m-d H:i:s');
        $account_datetime_arr = explode('/',$data['coop_account']['account_datetime']);
        $year = $account_datetime_arr[2];
        $month = $account_datetime_arr[1];
        $data['coop_account']['account_datetime'] = ($account_datetime_arr[2]-543)."-".sprintf('%02d',$account_datetime_arr[1])."-".sprintf('%02d',$account_datetime_arr[0]);

        $journal_ref = $_POST["journal_type"].($account_datetime_arr[2]-2500).sprintf('%02d',$account_datetime_arr[1]).sprintf('%02d',$account_datetime_arr[0]);
        $last_journal_ref_account = $this->db->select("journal_ref")->from("coop_account")->where("journal_ref LIKE '%".$journal_ref."%' AND (account_status != 2 OR account_status is null)")->order_by("journal_ref desc")->get()->row();
        if(empty($last_journal_ref_account)) {
            $journal_ref .= "001";
        } else {
            $last_journal_ref = $last_journal_ref_account->journal_ref;
            $journal_ref .= !empty($last_journal_ref) ? (sprintf('%03d', substr($last_journal_ref, -3) + 1)): "001";
        }

        $data_insert = array();
        $data_insert['account_description'] = $data['coop_account']['account_description'];
        $data_insert['account_datetime'] = $data['coop_account']['account_datetime'];
        $data_insert['process'] = 'add_manual';
        $data_insert['journal_type'] = $_POST["journal_type"];
        $data_insert["journal_ref"] = $journal_ref;
        $data_insert["run_status"] = 0;
        $data_insert['user_id'] = $_SESSION['USER_ID'];

        $account_id = $_POST["account_id"];
        $budget_year_be = $this->account_transaction->get_budget_year($data['coop_account']['account_datetime']);
        if(!empty($_POST["account_id"])) {
            $data_insert = array();
            $data_insert['account_status'] = 2;
            $this->db->where('account_id', $_POST["account_id"]);
            $this->db->update('coop_account', $data_insert);

            //Calculate year budget(Remove previous data)
            $account_details = $this->db->select("YEAR(t1.account_datetime) AS year, MONTH(t1.account_datetime) AS month, t2.account_chart_id, t2.account_amount, t2.account_type, t3.entry_type")
                                        ->from("coop_account as t1")
                                        ->join("coop_account_detail as t2", "t1.account_id = t2.account_id")
                                        ->join("coop_account_chart as t3", "t2.account_chart_id = t3.account_chart_id", "left")
                                        ->where('t1.account_id = "'.$_POST["account_id"].'"')
                                        ->get()->result_array();
            foreach($account_details as $detail) {
                $month_acc = $detail["month"];
                $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();
                if(empty($month_acc)) $month_acc = $account_period->accm_month_ini;
                $year_be = $account_period->accm_month_ini <= $month_acc ? $detail["year"] + 543 + 1 : $detail["year"] + 543;
                $this->account_transaction->increase_decrease_budget_year($detail["account_chart_id"], $detail["account_amount"], $detail["account_type"], $year_be, 2);
            }

            $account = $this->db->select("journal_ref")->from("coop_account")->where("account_id = '".$_POST["account_id"]."'")->get()->row_array();
            $journal_ref = $account['journal_ref'];
        }
        //Generate account data.
        $data_insert = array();
        $data_insert['account_description'] = $data['coop_account']['account_description'];
        $data_insert['account_datetime'] = $data['coop_account']['account_datetime'];
        $data_insert["account_status"] = 0;
        $data_insert['process'] = 'add_manual';
        $data_insert['journal_type'] = $_POST["journal_type"];
        $data_insert["journal_ref"] = $journal_ref;
        $data_insert["run_status"] = 0;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        $data_insert['budget_year'] = $budget_year_be;
        $data_insert['created_at'] = $process_timestamp;
        $data_insert['updated_at'] = $process_timestamp;
        $this->db->insert('coop_account', $data_insert);
        $account_id = $this->db->insert_id();

        if($_POST["journal_type"] == "JV" || $_POST["journal_type"] == "SV") {
            foreach($data['coop_account_detail'] as $key => $value){
                $data_insert = array();
                $data_insert['account_id'] = $account_id;
                $data_insert['account_type'] = $value['account_type'];
                $data_insert['account_amount'] = $value['account_amount'];
                $data_insert['account_chart_id'] = $value['account_chart_id'];
                $data_insert['description'] = $value['account_description'];
                $data_insert['created_at'] = $process_timestamp;
                $this->db->insert('coop_account_detail', $data_insert);

                $this->account_transaction->increase_decrease_budget_year($value["account_chart_id"], $value["account_amount"], $value["account_type"], $budget_year_be, 1);
            }
        } else {
            $account_type = $_POST["journal_type"] == "RV" ? "credit" : "debit";
            $account_cash_type = $account_type == "credit" ? "debit" : "credit";
            $total_amount = 0;
            $has_credit = 0;
            $has_debit = 0;
            foreach($data['coop_account_detail'] as $key => $value){
                $data_insert = array();
                $data_insert['account_id'] = $account_id;
                $data_insert['account_type'] = !empty($value['account_type']) ? $value['account_type'] : $account_type;
                $data_insert['account_amount'] =  $value['account_amount'];
                $data_insert['account_chart_id'] = $value['account_chart_id'];
                $data_insert['description'] = $value['account_description'];
                $data_insert['created_at'] = $process_timestamp;
                $this->db->insert('coop_account_detail', $data_insert);
                $total_amount += $value['account_amount'];

                if($value['account_type'] == "credit") {
                    $has_credit = 1;
                } else if ($value['account_type'] == "debit") {
                    $has_debit = 1;
                }

                $this->account_transaction->increase_decrease_budget_year($value["account_chart_id"], $value["account_amount"], (!empty($value['account_type']) ? $value['account_type'] : $account_type), $budget_year_be, 1);
            }

            //Generate cash if do not have two side of data.
            if($has_credit == 0 || $has_debit == 0) {
                //Get account for cash.
                $cash_account = $this->db->select("*")->from("coop_account_setting")->where("type = 'cash_chart_id'")->get()->row();
                $cash_id = $cash_account->value;

                $data_insert = array();
                $data_insert['account_id'] = $account_id;
                $data_insert['account_type'] = $account_cash_type;
                $data_insert['account_amount'] = $total_amount;
                $data_insert['account_chart_id'] = $cash_id;
                $data_insert['created_at'] = $process_timestamp;
                $this->db->insert('coop_account_detail', $data_insert);

                $this->account_transaction->increase_decrease_budget_year($cash_id, $value["account_amount"], $account_cash_type, $budget_year_be, 1);
            }
        }

        $account_dates = $this->db->select("account_datetime")->from("coop_account")->where("(account_status != 2 OR account_status is null)")->order_by("account_datetime desc")->group_by("account_datetime")->get()->result_array();
        $page_names = array();
        $index = 1;
        foreach($account_dates as $account_date) {
            $page_names[$index++] = $account_date["account_datetime"];
        }
        $page_index = !empty($_GET["page"]) ? (int) $_GET["page"] : 1;
        echo"<script> document.location.href='".base_url(PROJECTPATH.'/account')."?page=".array_keys($page_names, $data['coop_account']['account_datetime']." 00:00:00")[0]."'; </script>";
    }

    function ajax_add_account_detail(){
        $arr_data = array();
        $arr_data['type'] = $_POST['type'];
        $arr_data['input_number'] = $_POST['input_number'];

        $this->db->select(array('*'));
        $this->db->from('coop_account_chart');
        $this->db->where("type = 3 AND cancel_status IS NULL");
        $this->db->order_by("account_chart_id ASC");
        $row_account_chart = $this->db->get()->result_array();

        $arr_data['row_account_chart'] = $row_account_chart;

        $this->load->view('account/ajax_add_account_detail',$arr_data);
    }

    function account_day_book(){
        $arr_data = array();
        $arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

        $this->libraries->template('account/account_day_book',$arr_data);
    }

    function ajax_check_day_book(){
        if(@$_POST['report_date'] != '' ){
            $date_arr = explode('/',$_POST['report_date']);
            $day = (int)@$date_arr[0];
            $month = (int)@$date_arr[1];
            $year = (int)@$date_arr[2];
            $year = $year - 543;

            $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
            $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
            $where = " AND account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        }else{
            if(@$_POST['month']!='' && @$_POST['year']!=''){
                $day = '';
                $month = @$_POST['month'];
                $year = (@$_POST['year']-543);
                $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
                $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
                $where = " AND account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            }else{
                $day = '';
                $month = '';
                $year = (@$_POST['year']-543);
                $where = " AND budget_year = ".$_POST['year'];
            }
        }

        $this->db->select(array('*'));
        $this->db->from('coop_account');
        $this->db->where("(account_status != '2' OR account_status IS NULL ) AND (status_audit <> '1' OR status_audit is null )  ".$where);
        $this->db->order_by("account_datetime ASC");
        $this->db->limit(1);
        $row = $this->db->get()->result_array();
        if(@$row[0]['account_id'] != ''){
            echo "success";
        }
        exit;
    }

    public function account_day_book_pdf() {
        if(empty($_POST)) { exit; }
        $arr_data = array();

        $data_account_detail = array();

        $merge_pv_rv = $this->db->select("value")->from("coop_account_setting")->where('type = "day_book_merge_pv_rv"')->get()->row();
        if(!empty($merge_pv_rv) && !empty($merge_pv_rv->value)) {
            $result = $this->get_account_day_book($_POST, "'RV','PV'");

            foreach($result["data_account_detail"] as $key => $data) {
                $data_account_detail[$key][] = $data;
            }
        } else {
            $result = $this->get_account_day_book($_POST, "'RV'");
            foreach($result["data_account_detail"] as $key => $data) {
                $data_account_detail[$key][] = $data;
            }
            $result = $this->get_account_day_book($_POST, "'PV'");
            foreach($result["data_account_detail"] as $key => $data) {
                $data_account_detail[$key][] = $data;
            }
        }
        $result = $this->get_account_day_book($_POST, "'JV'");
        foreach($result["data_account_detail"] as $key => $data) {
            $data_account_detail[$key][] = $data;
        }
        $result = $this->get_account_day_book($_POST, "'SV'");
        foreach($result["data_account_detail"] as $key => $data) {
            $data_account_detail[$key][] = $data;
        }

        $desc_show = $this->db->select("value")->from("coop_account_setting")->where('type = "day_book_show_desc"')->get()->row();

        $arr_data["month_arr"] = $this->month_arr;
        $arr_data['data'] = $data_account_detail;
        $arr_data['day'] = $result["day"];
        $arr_data['month'] = $result["month"];
        $arr_data['year_be'] = $result["year_be"];
        $arr_data['desc_show'] = !empty($desc_show) ? $desc_show->value : 0;
        $arr_data['merge_pv_rv'] = !empty($merge_pv_rv) ? $merge_pv_rv->value : 0;

        if(!empty($desc_show) && $desc_show->value == 1) {
            $this->load->view('account/account_day_book_with_desc_pdf',$arr_data);
        } else {
            $this->load->view('account/account_day_book_pdf',$arr_data);
        }
    }

    public function account_day_book_excel(){
        if(empty($_POST)) { exit; }
        $arr_data = array();

        $data_account_detail = array();

        $merge_pv_rv = $this->db->select("value")->from("coop_account_setting")->where('type = "day_book_merge_pv_rv"')->get()->row();
        if(!empty($merge_pv_rv) && !empty($merge_pv_rv->value)) {
            $result = $this->get_account_day_book($_POST, "'RV','PV'");
            foreach($result["data_account_detail"] as $key => $data) {
                $data_account_detail[$key][] = $data;
            }
        } else {
            $result = $this->get_account_day_book($_POST, "'RV'");
            foreach($result["data_account_detail"] as $key => $data) {
                $data_account_detail[$key][] = $data;
            }
            $result = $this->get_account_day_book($_POST, "'PV'");
            foreach($result["data_account_detail"] as $key => $data) {
                $data_account_detail[$key][] = $data;
            }
        }
        $result = $this->get_account_day_book($_POST, "'JV'");
        foreach($result["data_account_detail"] as $key => $data) {
            $data_account_detail[$key][] = $data;
        }
        $result = $this->get_account_day_book($_POST, "'SV'");
        foreach($result["data_account_detail"] as $key => $data) {
            $data_account_detail[$key][] = $data;
        }

        $desc_show = $this->db->select("value")->from("coop_account_setting")->where('type = "day_book_show_desc"')->get()->row();

        $arr_data["month_arr"] = $this->month_arr;
        $arr_data['data'] = $data_account_detail;
        $arr_data['day'] = $result["day"];
        $arr_data['month'] = $result["month"];
        $arr_data['year_be'] = $result["year_be"];
        $arr_data['desc_show'] = !empty($desc_show) ? $desc_show->value : 0;
        $arr_data['merge_pv_rv'] = !empty($merge_pv_rv) ? $merge_pv_rv->value : 0;
        $this->load->view('account/account_day_book_excel',$arr_data);
    }

    public function get_account_day_book($data, $type = null) {
        $result = array();
        $where = " AND t0.journal_type IN ({$type})";
        if(@$data['report_date'] != ''){
            $date_arr = explode('/',@$data['report_date']);
            $day = (int)@$date_arr[0];
            $month = (int)@$date_arr[1];
            $year = (int)@$date_arr[2];
            $year -= 543;
            $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
            $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
            $where .= " AND account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        }else{
            if(@$data['month']!='' && @$data['year']!=''){
                $day = '';
                $month = @$data['month'];
                $year = (@$data['year']-543);

                $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
                $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
                $where .= " AND account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            }else{
                $day = '';
                $month = '';
                $year = (@$data['year']-543);
                $where .= " AND budget_year = ".$data['year'];
            }
        }
        $result['day'] = $day;
        $result['month'] = $month;
        $result['year'] = $year;
        $result['year_be'] = $year + 543;

        $data = array();
        $sort_array = array();
        $data_account_detail = array();

        $row = array();
        $this->db->select(array('*'));
        $this->db->from("(SELECT
                            `t0`.account_number,
                            `t0`.account_description,
                            `t0`.account_page_number,
                            `t0`.account_status,
                            `t0`.ref_id,
                            `t0`.ref_type,
                            `t0`.process,
                            `t0`.status_audit,
                            `t0`.journal_ref,
                            `t0`.journal_type,
                            LEFT(`t0`.account_datetime, 10) AS account_datetime,
                            `t1`.*,
                            `t2`.`account_chart` 
                        FROM
                            `coop_account` AS `t0`
                            INNER JOIN `coop_account_detail` AS `t1` ON t0.account_id = t1.account_id
                            INNER JOIN `coop_account_chart` AS `t2` ON `t1`.`account_chart_id` = `t2`.`account_chart_id` 
                        WHERE
                            1 = 1 
                            AND (`account_status` != '2' OR `account_status` IS NULL )
                            AND( `t0`.`status_audit` <> '1' 
                            OR `t0`.`status_audit` IS NULL )
                            {$where}
                        ) as t_all");
        $this->db->order_by("t_all.account_datetime ASC,
                            t_all.account_type DESC,
                            t_all.account_chart_id ASC");

        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;

        foreach($row['data']  as $key => $row) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row['account_datetime']);
            foreach($row as $key2 => $row_detail){
                $i_d = 0;
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail['account_id']][$row_detail['account_chart_id'].$row_detail['account_type'].$key2]['account_chart_id'] = $row_detail['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_id']][$row_detail['account_chart_id'].$row_detail['account_type'].$key2]['account_chart'] = $row_detail['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_id']][$row_detail['account_chart_id'].$row_detail['account_type'].$key2]['account_type'] = $row_detail['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_id']][$row_detail['account_chart_id'].$row_detail['account_type'].$key2]['account_amount'] += $row_detail['account_amount'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_id']][$row_detail['account_chart_id'].$row_detail['account_type'].$key2]['journal_type'] = $row_detail['journal_type'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_id']][$row_detail['account_chart_id'].$row_detail['account_type'].$key2]['description'] = $row_detail['description'];
            }
        }

        $result["data_account_detail"] = $data_account_detail;
        return $result;
    }

    function account_chart_report(){
        $arr_data = array();
        $arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

        $account_charts = $this->db->select("*")->from("coop_account_chart")->where("type = 3")->get()->result_array();
        $arr_data["account_charts"] = $account_charts;

        $this->libraries->template('account/account_chart_report',$arr_data);
    }

    function account_subsidiary_ledge(){
        $arr_data = array();
        $arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $account_chart_main = array();
        $this->db->select(array('*'));
        $this->db->from('coop_account_chart');
        $this->db->order_by("account_chart_id ASC");
        $row = $this->db->get()->result_array();
        foreach($row as $key => $row_detail) {
            $account_chart_main[$row_detail['account_chart_id']] = $row_detail['account_chart'];
        }
        $arr_data['account_chart_main']  = $account_chart_main;
        $this->libraries->template('account/account_subsidiary_ledge',$arr_data);
    }
    function account_subsidiary_ledge_excel(){
        $arr_data = array();
        if(@$_GET['report_date_start'] != ''){
            $date_arr_start = explode('/',@$_GET['report_date_start']);
            $day_s = (int)@$date_arr_start[0];
            $month_s = (int)@$date_arr_start[1];
            $year_s = (int)@$date_arr_start[2];
            $year_s -= 543;
            $date_arr_end = explode('/',@$_GET['report_date_end']);
            $day_e = (int)@$date_arr_end[0];
            $month_e = (int)@$date_arr_end[1];
            $year_e = (int)@$date_arr_end[2];
            $year_e -= 543;
            $s_date = $year_s.'-'.sprintf("%02d",@$month_s).'-'.sprintf("%02d",@$day_s).' 00:00:00.000';
            $e_date = $year_e.'-'.sprintf("%02d",@$month_e).'-'.sprintf("%02d",@$day_e).' 23:59:59.000';
            $where = " AND account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        }
        if(@$_GET['account_chart_main'] != '') {
            $where_account  = " AND t1.account_chart_id ='".$_GET['account_chart_main']."' ";
        }

        $this->db->select(array('*'));
        $this->db->from("(SELECT
                            `t0`.account_number,
                            `t0`.account_description,
                            `t0`.account_page_number,
                            `t0`.account_status,
                            `t0`.ref_id,
                            `t0`.ref_type,
                            `t0`.process,
                            `t0`.status_audit,
                            `t0`.journal_ref,
                            LEFT(`t0`.account_datetime, 10) AS account_datetime,
                            `t1`.*,
                            `t2`.`account_chart`
                        FROM
                            `coop_account` AS `t0`
                            INNER JOIN `coop_account_detail` AS `t1` ON t0.account_id = t1.account_id
                            INNER JOIN `coop_account_chart` AS `t2` ON `t1`.`account_chart_id` = `t2`.`account_chart_id`
                        WHERE
                            1 = 1
                            AND (`account_status` != '2' OR `account_status` IS NULL )
                            AND( `t0`.`status_audit` <> '1'
                            OR `t0`.`status_audit` IS NULL )
                            $where  $where_account
                        ) as t_all");
        $this->db->order_by("t_all.account_chart_id ASC,t_all.account_datetime ASC");
        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;

        foreach($row['data']  as $key => $row) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row['account_datetime']);
            foreach($row as $key2 => $row_detail){
                $i_d = 0;
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_chart_id'] = $row_detail['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_chart'] = $row_detail['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_type'] = $row_detail['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_amount'] += $row_detail['account_amount'];
            }
        }

        $sort_array = array_map(function($array_account_datetime){
            return array_map(function($var){
                ksort($var);
                return $var;
            },$array_account_datetime);

        },$data_account_detail);

        $number_count = 0;
        $sum_debit = array();
        $sum_credit = array();
        foreach($sort_array as $key => $value_main){
            foreach($value_main as $key_type => $value_data) {
                foreach($value_data as $key_type_dc => $value_data_dc) {
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description']  = $key_type;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount']  += $value_data_dc['account_amount'] ;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id']  = $value_data_dc['account_chart_id'] ;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart']  = $value_data_dc['account_chart'] ;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type']  = $value_data_dc['account_type'] ;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;

                    $number_count++;
                    if($value_data_dc['account_type'] == 'debit'){
                        $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'] ;
                    }else{
                        $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'] ;
                    }

                }
            }
        }

        $account_chart_main = array();
        $this->db->select(array('*'));
        $this->db->from('coop_account_chart');
        $this->db->order_by("account_chart_id ASC");
        $row = $this->db->get()->result_array();
        foreach($row as $key => $row_detail) {
            $account_chart_main[$row_detail['account_chart_id']] = $row_detail['account_chart'];
        }

        if($_GET['month'] == 1 ){
            $old_mount = 12;
            $old_year =  $year_s-1;
        }else{
            $old_mount = $_GET['month'] -1 ;
            $old_year =  $year_s;
        }
        $this->db->select(array(
            '*'
        ));
        $this->db->from('coop_account_budget');
        $this->db->where("mount = '".$old_mount."' AND year = '".$old_year."'");

        $row_budget = $this->db->get()->result_array();
        $budget = array();
        foreach($row_budget as $key => $data_budget){
            $budget[$data_budget['account_chart_id']] = $data_budget;
        }

        $arr_data['row_budget'] = $budget;
        $arr_data['account_chart_main'] = $account_chart_main;
        $arr_data['data'] = $data_filter;
        $arr_data['sum_debit'] = $sum_debit;
        $arr_data['sum_credit'] = $sum_credit;
        $this->load->view('account/account_chart_report_excel',$arr_data);
    }

    function ajax_account_subsidiary_ledge(){
        $arr_data = array();
        if(@$_POST['report_date_start'] != ''){
            $date_arr_start = explode('/',@$_POST['report_date_start']);
            $day_s = (int)@$date_arr_start[0];
            $month_s = (int)@$date_arr_start[1];
            $year_s = (int)@$date_arr_start[2];
            $year_s -= 543;
            $date_arr_end = explode('/',@$_POST['report_date_end']);
            $day_e = (int)@$date_arr_end[0];
            $month_e = (int)@$date_arr_end[1];
            $year_e = (int)@$date_arr_end[2];
            $year_e -= 543;
            $s_date = $year_s.'-'.sprintf("%02d",@$month_s).'-'.sprintf("%02d",@$day_s).' 00:00:00.000';
            $e_date = $year_e.'-'.sprintf("%02d",@$month_e).'-'.sprintf("%02d",@$day_e).' 23:59:59.000';
            $where = " AND account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        }
        if(@$_POST['account_chart_main'] != '') {
            $where_account  = " AND t1.account_chart_id ='".$_POST['account_chart_main']."' ";
        }

        $this->db->select(array('*'));
        $this->db->from("(SELECT
                            `t0`.account_number,
                            `t0`.account_description,
                            `t0`.account_page_number,
                            `t0`.account_status,
                            `t0`.ref_id,
                            `t0`.ref_type,
                            `t0`.process,
                            `t0`.status_audit,
                            `t0`.journal_ref,
                            LEFT(`t0`.account_datetime, 10) AS account_datetime,
                            `t1`.*,
                            `t2`.`account_chart`
                        FROM
                            `coop_account` AS `t0`
                            INNER JOIN `coop_account_detail` AS `t1` ON t0.account_id = t1.account_id
                            INNER JOIN `coop_account_chart` AS `t2` ON `t1`.`account_chart_id` = `t2`.`account_chart_id`
                        WHERE
                            1 = 1
                            AND (`account_status` != '2' OR `account_status` IS NULL )
                            AND( `t0`.`status_audit` <> '1'
                            OR `t0`.`status_audit` IS NULL )
                            $where  $where_account
                        ) as t_all");
        $this->db->order_by("t_all.account_chart_id ASC,t_all.account_datetime ASC");
        $row = $this->db->get()->result_array();
        if(@$row[0]['account_id'] != ''){
            echo "success";
        }
        exit;
    }

    function ajax_check_account_chart_report() {
        $where = "";
        if ($_POST['from_date'] != '' && !empty($_POST['thru_date'])) {
            $date_arr = explode('/',$_POST['from_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $year = (int)$date_arr[2];
            $year -= 543;
            $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
            $date_arr = explode('/',$_POST['thru_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $year = (int)$date_arr[2];
            $year -= 543;
            $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
            $where = " AND t1.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        } else {
            if($_POST['month']!='' && $_POST['year']!=''){
                $day = '';
                $month = $_POST['month'];
                $year = ($_POST['year']-543);
                $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
                $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
                $where = " AND t1.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            }else{
                $day = '';
                $month = '';
                $year = ($_POST['year']-543);
                $where = " AND t1.budget_year = ".$_POST["year"];
            }
        }

        if(!empty($_POST["account_chart_id"])) {
            $where .= " AND t2.account_chart_id = '".$_POST["account_chart_id"]."'";
        }

        $this->db->select(array(
            't1.account_id',
            't1.account_datetime',
            't2.account_type'
        ));
        $this->db->from('coop_account as t1');
        $this->db->join('coop_account_detail as t2','t1.account_id = t2.account_id','inner');
        $this->db->where(" 1=1 ".$where." AND (t1.status_audit <> '1' OR t1.status_audit is null)" );
        $this->db->order_by("t1.account_datetime ASC");
        $this->db->limit(1);
        $row = $this->db->get()->result_array();
        if(@$row[0]['account_id'] != ''){
            echo "success";
        }
        exit;
    }

    function account_chart_report_pdf() {
        if(empty($_POST)) { exit; }

        $account_chart_report_disable_nature_group_setting = $this->db->select("value")->from("coop_account_setting")->where('type = "account_chart_report_disable_nature_group"')->get()->row();
        $account_chart_report_disable_nature_group = !empty($account_chart_report_disable_nature_group_setting) ? $account_chart_report_disable_nature_group_setting->value : 0;
        $arr_data = array();
        $from_year_be = 0;
        $from_month = 0;
        $balance_thru_date = "";
        $s_date = "";
        $e_date = "";
        if($_POST['from_date'] != '' && !empty($_POST['thru_date'])) {
            $date_arr = explode('/',$_POST['from_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $from_month = $month;
            $year = (int)$date_arr[2];
            $year -= 543;
            $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
            $date_arr = explode('/',$_POST['thru_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $year = (int)$date_arr[2];
            $year -= 543;
            $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
            $where = " AND t0.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            $from_year_be = $this->account_transaction->get_budget_year($s_date);
        } else if (!empty($_POST['month']) && !empty($_POST['year'])) {
            $month = $_POST['month'];
            $year = ($_POST['year']-543);
            $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
            $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
            $where = " AND t0.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            $from_year_be = $this->account_transaction->get_budget_year($s_date);
        } else if (!empty($_POST['year'])) {
            $year = ($_POST['year']-543);
            $budget_year_be = $this->account_transaction->get_year_period($_POST['year']);
            $account_year = $budget_year_be;
            $s_date = $budget_year_be['start'];
            $e_date = $budget_year_be['end']; 
            $where = " AND t0.budget_year = ".$_POST['year'];
            $from_year_be = $year + 543;
        }

        $arr_data['s_date'] = $s_date;
        $arr_data['e_date'] = $e_date;
        $balance_thru_date = $s_date;

        if(!empty($_POST["account_chart_id"])) {
            $where .= " AND t1.account_chart_id = '".$_POST["account_chart_id"]."'";
        }

        $data = array();

        $sort_array = array();
        $data_account_detail = array();

        $row  = array();

        $this->db->select(array('*'));
        $this->db->from("(SELECT
                            `t0`.account_number,
                            `t0`.account_page_number,
                            `t0`.account_status,
                            `t0`.ref_id,
                            `t0`.ref_type,
                            `t0`.process,
                            `t0`.status_audit,
                            `t0`.journal_ref,
                            `t0`.account_description,
                            LEFT(`t0`.account_datetime, 10) AS account_datetime,
                            `t1`.*,
                            `t2`.`account_chart`
                        FROM
                            `coop_account` AS `t0`
                            INNER JOIN `coop_account_detail` AS `t1` ON t0.account_id = t1.account_id
                            INNER JOIN `coop_account_chart` AS `t2` ON `t1`.`account_chart_id` = `t2`.`account_chart_id`
                        WHERE
                            1 = 1
                            AND (`account_status` != '2' OR `account_status` IS NULL )
                            AND( `t0`.`status_audit` <> '1'
                            OR `t0`.`status_audit` IS NULL )
                            $where
                        ) as t_all");
        $this->db->order_by("t_all.account_datetime ASC, t_all.account_chart_id ASC, t_all.journal_ref, t_all.account_detail_id");
        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;

        foreach($row['data']  as $key => $row) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row['account_datetime']);
            foreach($row as $key2 => $row_detail){
                $i_d = 0;
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail['account_datetime']);
                if(!empty($account_chart_report_disable_nature_group)) {
                    //Set all nature key to debit due to disable nature for police hospital 26/30/2021
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']]['debit'][$key2]['account_chart_id'] = $row_detail['account_chart_id'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']]['debit'][$key2]['account_chart'] = $row_detail['account_chart'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']]['debit'][$key2]['account_type'] = $row_detail['account_type'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']]['debit'][$key2]['account_amount'] += $row_detail['account_amount'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']]['debit'][$key2]['account_description'] = $row_detail['account_description'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']]['debit'][$key2]['journal_ref'] = $row_detail['journal_ref'];
                } else {
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_chart_id'] = $row_detail['account_chart_id'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_chart'] = $row_detail['account_chart'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_type'] = $row_detail['account_type'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_amount'] += $row_detail['account_amount'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_description'] = $row_detail['account_description'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['journal_ref'] = $row_detail['journal_ref'];
                }
            }
        }

        $sort_array = array_map(function($array_account_datetime){
            return array_map(function($var){
                ksort($var);
                return $var;
            },$array_account_datetime);

        },$data_account_detail);

        $number_count = 0;
        $sum_debit = array();
        $sum_credit = array();
        foreach($sort_array as $key => $value_main){
            foreach($value_main as $key_type_dc => $value_data_dc_r) {
                $group_id = substr($key_type_dc,0,1);
                if($group_id == 1 || $group_id == 5) {
                    if(!empty($value_data_dc_r["debit"])) {
                        foreach($value_data_dc_r["debit"] as $value_data_dc) {
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description'] = $value_data_dc['account_description'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount'] += $value_data_dc['account_amount'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id'] = $value_data_dc['account_chart_id'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart'] = $value_data_dc['account_chart'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type'] = $value_data_dc['account_type'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['journal_ref'] = $value_data_dc['journal_ref'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;

                            $number_count++;
                            if($value_data_dc['account_type'] == 'debit'){
                                $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }else{
                                $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }
                        }
                    }
                    if(!empty($value_data_dc_r["credit"])) {
                        foreach($value_data_dc_r["credit"] as $value_data_dc) {
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description'] = $value_data_dc['account_description'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount'] += $value_data_dc['account_amount'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id'] = $value_data_dc['account_chart_id'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart'] = $value_data_dc['account_chart'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type'] = $value_data_dc['account_type'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['journal_ref'] = $value_data_dc['journal_ref'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;

                            $number_count++;
                            if($value_data_dc['account_type'] == 'debit'){
                                $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }else{
                                $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }
                        }
                    }
                } else {
                    if(!empty($value_data_dc_r["credit"])) {
                        foreach($value_data_dc_r["credit"] as $value_data_dc) {
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description'] = $value_data_dc['account_description'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount'] += $value_data_dc['account_amount'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id'] = $value_data_dc['account_chart_id'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart'] = $value_data_dc['account_chart'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type'] = $value_data_dc['account_type'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['journal_ref'] = $value_data_dc['journal_ref'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;

                            $number_count++;
                            if($value_data_dc['account_type'] == 'debit'){
                                $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }else{
                                $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }
                        }
                    }
                    if(!empty($value_data_dc_r["debit"])) {
                        foreach($value_data_dc_r["debit"] as $value_data_dc) {
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description'] = $value_data_dc['account_description'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount'] += $value_data_dc['account_amount'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id'] = $value_data_dc['account_chart_id'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart'] = $value_data_dc['account_chart'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type'] = $value_data_dc['account_type'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['journal_ref'] = $value_data_dc['journal_ref'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;

                            $number_count++;
                            if($value_data_dc['account_type'] == 'debit'){
                                $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }else{
                                $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }
                        }
                    }
                }
            }
        }

        $account_chart_main = array();
        $this->db->select(array('*'));
        $this->db->from('coop_account_chart');
        $this->db->order_by("account_chart_id ASC");
        $row = $this->db->get()->result_array();
        foreach($row as $key => $row_detail) {
            $account_chart_main[$row_detail['account_chart_id']] = $row_detail['account_chart'];
        }

        //Get account balance
        $account_balances = array();
        $account_prev_year = $from_year_be - 1;
        $account_budget_years = $this->db->select("t1.account_chart_id, t1.entry_type, t2.budget_amount, t2.budget_type")
                                            ->from("coop_account_chart as t1")
                                            ->join("coop_account_budget_year as t2", "t1.account_chart_id = t2.account_chart_id AND year = '".$account_prev_year."'", "left")
                                            ->get()->result_array();

        foreach($account_budget_years as $budget) {
            if(!empty($budget["budget_type"])) {
                $account_balances[$budget["account_chart_id"]]["type"] = $budget["budget_type"];
                $account_balances[$budget["account_chart_id"]]["amount"] = $budget["budget_amount"];
            } else {
                $account_balances[$budget["account_chart_id"]]["type"] = $budget["entry_type"] == 1 ? "debit" : "credit";
                $account_balances[$budget["account_chart_id"]]["amount"] = 0;
            }
        }

        $where_balance = " AND t1.budget_year = '".$from_year_be."' AND t1.account_datetime < '".$balance_thru_date."'";
        $account_details = $this->db->select("SUM(t2.account_amount) as amount, t2.account_type, t2.account_chart_id")
                                    ->from("coop_account as t1")
                                    ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                                    ->where("(t1.account_status is null OR t1.account_status != 2)".$where_balance)
                                    ->group_by("t2.account_chart_id, t2.account_type")
                                    ->get()->result_array();

        foreach($account_details as $detail) {
            if($account_balances[$detail["account_chart_id"]]["type"] == $detail["account_type"]) {
                $account_balances[$detail["account_chart_id"]]["amount"] += $detail["amount"];
            } else {
                $account_balances[$detail["account_chart_id"]]["amount"] -= $detail["amount"];
            }
        }

        $arr_data['row_budget'] = $budget;
        $arr_data['account_chart_main'] = $account_chart_main;
        $arr_data['data'] = $data_filter;
        $arr_data['sum_debit'] = $sum_debit;
        $arr_data['sum_credit'] = $sum_credit;
        $arr_data['account_balances'] = $account_balances;
        $this->load->view('account/account_chart_report_pdf',$arr_data);
    }

    function account_chart_report_excel() {
        if(empty($_POST)) { exit; }

        $account_chart_report_disable_nature_group_setting = $this->db->select("value")->from("coop_account_setting")->where('type = "account_chart_report_disable_nature_group"')->get()->row();
        $account_chart_report_disable_nature_group = !empty($account_chart_report_disable_nature_group_setting) ? $account_chart_report_disable_nature_group_setting->value : 0;
        $arr_data = array();
        $from_year_be = 0;
        $from_month = 0;
        $balance_thru_date = "";
        $s_date = "";
        $e_date = "";
        if($_POST['from_date'] != '' && !empty($_POST['thru_date'])) {
            $date_arr = explode('/',$_POST['from_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $from_month = $month;
            $year = (int)$date_arr[2];
            $year -= 543;
            $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
            $date_arr = explode('/',$_POST['thru_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $year = (int)$date_arr[2];
            $year -= 543;
            $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
            $where = " AND t0.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            $from_year_be = $this->account_transaction->get_budget_year($s_date);
        } else if (!empty($_POST['month']) && !empty($_POST['year'])) {
            $month = $_POST['month'];
            $year = ($_POST['year']-543);
            $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
            $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
            $where = " AND t0.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            $from_year_be = $this->account_transaction->get_budget_year($s_date);
        } else if (!empty($_POST['year'])) {
            $year = ($_POST['year']-543);
            $budget_year_be = $this->account_transaction->get_year_period($_POST['year']);
            $account_year = $budget_year_be;
            $s_date = $budget_year_be['start'];
            $e_date = $budget_year_be['end']; 
            $where = " AND t0.budget_year = ".$_POST['year'];
            $from_year_be = $year + 543;
        }

        $arr_data['s_date'] = $s_date;
        $arr_data['e_date'] = $e_date;
        $balance_thru_date = $s_date;

        if(!empty($_POST["account_chart_id"])) {
            $where .= " AND t1.account_chart_id = '".$_POST["account_chart_id"]."'";
        }

        $data = array();

        $sort_array = array();
        $data_account_detail = array();

        $row  = array();

        $this->db->select(array('*'));
        $this->db->from("(SELECT
                            `t0`.account_number,
                            `t0`.account_page_number,
                            `t0`.account_status,
                            `t0`.ref_id,
                            `t0`.ref_type,
                            `t0`.process,
                            `t0`.status_audit,
                            `t0`.journal_ref,
                            `t0`.account_description,
                            LEFT(`t0`.account_datetime, 10) AS account_datetime,
                            `t1`.*,
                            `t2`.`account_chart`
                        FROM
                            `coop_account` AS `t0`
                            INNER JOIN `coop_account_detail` AS `t1` ON t0.account_id = t1.account_id
                            INNER JOIN `coop_account_chart` AS `t2` ON `t1`.`account_chart_id` = `t2`.`account_chart_id`
                        WHERE
                            1 = 1
                            AND (`account_status` != '2' OR `account_status` IS NULL )
                            AND( `t0`.`status_audit` <> '1'
                            OR `t0`.`status_audit` IS NULL )
                            $where
                        ) as t_all");
        $this->db->order_by("t_all.account_datetime ASC, t_all.account_chart_id ASC, t_all.journal_ref, t_all.account_detail_id");
        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;

        foreach($row['data']  as $key => $row) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row['account_datetime']);
            foreach($row as $key2 => $row_detail){
                $i_d = 0;
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail['account_datetime']);
                if(!empty($account_chart_report_disable_nature_group)) {
                    //Set all nature key to debit due to disable nature for police hospital 26/30/2021
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']]['debit'][$key2]['account_chart_id'] = $row_detail['account_chart_id'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']]['debit'][$key2]['account_chart'] = $row_detail['account_chart'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']]['debit'][$key2]['account_type'] = $row_detail['account_type'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']]['debit'][$key2]['account_amount'] += $row_detail['account_amount'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']]['debit'][$key2]['account_description'] = $row_detail['account_description'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']]['debit'][$key2]['journal_ref'] = $row_detail['journal_ref'];
                } else {
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_chart_id'] = $row_detail['account_chart_id'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_chart'] = $row_detail['account_chart'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_type'] = $row_detail['account_type'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_amount'] += $row_detail['account_amount'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_description'] = $row_detail['account_description'];
                    $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['journal_ref'] = $row_detail['journal_ref'];
                }
            }
        }

        $sort_array = array_map(function($array_account_datetime){
            return array_map(function($var){
                ksort($var);
                return $var;
            },$array_account_datetime);

        },$data_account_detail);

        $number_count = 0;
        $sum_debit = array();
        $sum_credit = array();
        foreach($sort_array as $key => $value_main){
            foreach($value_main as $key_type_dc => $value_data_dc_r) {
                $group_id = substr($key_type_dc,0,1);
                if($group_id == 1 || $group_id == 5) {
                    if(!empty($value_data_dc_r["debit"])) {
                        foreach($value_data_dc_r["debit"] as $value_data_dc) {
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description'] = $value_data_dc['account_description'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount'] += $value_data_dc['account_amount'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id'] = $value_data_dc['account_chart_id'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart'] = $value_data_dc['account_chart'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type'] = $value_data_dc['account_type'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['journal_ref'] = $value_data_dc['journal_ref'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;

                            $number_count++;
                            if($value_data_dc['account_type'] == 'debit'){
                                $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }else{
                                $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }
                        }
                    }
                    if(!empty($value_data_dc_r["credit"])) {
                        foreach($value_data_dc_r["credit"] as $value_data_dc) {
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description'] = $value_data_dc['account_description'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount'] += $value_data_dc['account_amount'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id'] = $value_data_dc['account_chart_id'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart'] = $value_data_dc['account_chart'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type'] = $value_data_dc['account_type'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['journal_ref'] = $value_data_dc['journal_ref'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;

                            $number_count++;
                            if($value_data_dc['account_type'] == 'debit'){
                                $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }else{
                                $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }
                        }
                    }
                } else {
                    if(!empty($value_data_dc_r["credit"])) {
                        foreach($value_data_dc_r["credit"] as $value_data_dc) {
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description'] = $value_data_dc['account_description'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount'] += $value_data_dc['account_amount'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id'] = $value_data_dc['account_chart_id'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart'] = $value_data_dc['account_chart'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type'] = $value_data_dc['account_type'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['journal_ref'] = $value_data_dc['journal_ref'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;

                            $number_count++;
                            if($value_data_dc['account_type'] == 'debit'){
                                $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }else{
                                $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }
                        }
                    }
                    if(!empty($value_data_dc_r["debit"])) {
                        foreach($value_data_dc_r["debit"] as $value_data_dc) {
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description'] = $value_data_dc['account_description'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount'] += $value_data_dc['account_amount'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id'] = $value_data_dc['account_chart_id'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart'] = $value_data_dc['account_chart'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type'] = $value_data_dc['account_type'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['journal_ref'] = $value_data_dc['journal_ref'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;

                            $number_count++;
                            if($value_data_dc['account_type'] == 'debit'){
                                $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }else{
                                $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }
                        }
                    }
                }
            }
        }

        $account_chart_main = array();
        $this->db->select(array('*'));
        $this->db->from('coop_account_chart');
        $this->db->order_by("account_chart_id ASC");
        $row = $this->db->get()->result_array();
        foreach($row as $key => $row_detail) {
            $account_chart_main[$row_detail['account_chart_id']] = $row_detail['account_chart'];
        }

        //Get account balance
        $account_balances = array();
        $account_prev_year = $from_year_be - 1;
        $account_budget_years = $this->db->select("t1.account_chart_id, t1.entry_type, t2.budget_amount, t2.budget_type")
                                            ->from("coop_account_chart as t1")
                                            ->join("coop_account_budget_year as t2", "t1.account_chart_id = t2.account_chart_id AND year = '".$account_prev_year."'", "left")
                                            ->get()->result_array();

        foreach($account_budget_years as $budget) {
            if(!empty($budget["budget_type"])) {
                $account_balances[$budget["account_chart_id"]]["type"] = $budget["budget_type"];
                $account_balances[$budget["account_chart_id"]]["amount"] = $budget["budget_amount"];
            } else {
                $account_balances[$budget["account_chart_id"]]["type"] = $budget["entry_type"] == 1 ? "debit" : "credit";
                $account_balances[$budget["account_chart_id"]]["amount"] = 0;
            }
        }

        $where_balance = " AND t1.budget_year = '".$from_year_be."' AND t1.account_datetime < '".$balance_thru_date."'";
        $account_details = $this->db->select("SUM(t2.account_amount) as amount, t2.account_type, t2.account_chart_id")
                                    ->from("coop_account as t1")
                                    ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                                    ->where("(t1.account_status is null OR t1.account_status != 2)".$where_balance)
                                    ->group_by("t2.account_chart_id, t2.account_type")
                                    ->get()->result_array();

        foreach($account_details as $detail) {
            if($account_balances[$detail["account_chart_id"]]["type"] == $detail["account_type"]) {
                $account_balances[$detail["account_chart_id"]]["amount"] += $detail["amount"];
            } else {
                $account_balances[$detail["account_chart_id"]]["amount"] -= $detail["amount"];
            }
        }

        $arr_data['row_budget'] = $budget;
        $arr_data['account_chart_main'] = $account_chart_main;
        $arr_data['data'] = $data_filter;
        $arr_data['sum_debit'] = $sum_debit;
        $arr_data['sum_credit'] = $sum_credit;
        $arr_data['account_balances'] = $account_balances;
        $this->load->view('account/account_chart_report_excel',$arr_data);
    }

    function coop_account_experimental_budget(){
        $arr_data = array();
        $arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $this->libraries->template('account/coop_account_experimental_budget',$arr_data);
    }

    function ajax_check_account_experimental_budget(){
        if(@$_POST['report_date'] != ''){
            $date_arr = explode('/',@$_POST['report_date']);
            $day = (int)@$date_arr[0];
            $month = (int)@$date_arr[1];
            $year = (int)@$date_arr[2];
            $year -= 543;
            $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
            $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
            $where = " AND t1.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        }else{
            if(@$_POST['month']!='' && @$_POST['year']!=''){
                $day = '';
                $month = @$_POST['month'];
                $year = (@$_POST['year']-543);

                $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
                $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
                $where = " AND t1.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            }else{
                $day = '';
                $month = '';
                $year = (@$_POST['year']-543);
                $where = " AND t1.budget_year = ".$_POST['year'];
            }
        }

        $this->db->select(array('t1.account_id'));
        $this->db->from('coop_account as t1');
        $this->db->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "inner");
        $this->db->join("coop_account_chart as t3", "t2.account_chart_id = t3.account_chart_id", "inner");
        $this->db->where("1=1 {$where}   AND (t1.status_audit <> '1' OR t1.status_audit is null)");
        $rs = $this->db->get()->row_array();
        if(@$rs['account_id'] != ''){
            echo "success";
        }
    }

    function coop_account_experimental_budget_excel() {
        $arr_data = array();
        $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
        $arr_data['month_arr'] = $month_arr;
        $arr_data['month_short_arr'] = $month_short_arr;

        $datas = $this->get_account_experimental_budget_data($_POST);

        $arr_data["prev_budgets"] = $datas["prev_budgets"];
        $arr_data["rs"] = $datas["rs"];
        $arr_data["data_chart"] = $datas["data_chart"];
        $arr_data['textTitle'] = $datas['textTitle'];

        $this->load->view('account/coop_account_experimental_budget_excel',$arr_data);
    }

    function coop_account_experimental_budget_pdf() {
        $arr_data = array();
        $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
        $arr_data['month_arr'] = $month_arr;
        $arr_data['month_short_arr'] = $month_short_arr;

        $datas = $this->get_account_experimental_budget_data($_POST);

        $arr_data["prev_budgets"] = $datas["prev_budgets"];
        $arr_data["rs"] = $datas["rs"];
        $arr_data["data_chart"] = $datas["data_chart"];
        $arr_data['textTitle'] = $datas['textTitle'];


        $charts = array();
        $page = 0;
        $first_page_size = 16;
        $page_size = 16;
        foreach($datas["data_chart"] as $index => $chart) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $charts[$page][] = $chart;
        }

        $arr_data["page_all"] = $page;
        $arr_data["data_charts"] = $datas["data_chart"];
        // $arr_data["data_charts"] = $charts;

        $this->load->view('account/coop_account_experimental_budget_pdf',$arr_data);
    }

    function get_account_experimental_budget_data($data) {
        $results = array();

        $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $where_acc = "";
        $where_prev_budget = "(t1.account_status = 0 OR t1.account_status IS NULL)";
        if(@$data['report_date'] != '') {
            $date_arr = explode('/',@$data['report_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $year = (int)$date_arr[2];
            $year_be = $year;
            $year -= 543;
            $textTitle = "ณ วันที่ ".$day." ".$month_arr[$month]." ".($year+543);
            $where = " AND mount = '".$month."'  AND year = '".$year."'   ";
            $account_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day);
            $budget_year_be = $this->account_transaction->get_budget_year($account_date." 00:00:00.000");
            $where_acc = " AND account_datetime between '".$account_date." 00:00:00.000'
                                                    AND '".$account_date." 23:59:59.000'";
            $where_prev_budget .= " AND t1.account_datetime < '".$account_date." 00:00:00.000' AND budget_year = ".$budget_year_be;
        } else {
            if(!empty($data['month']) && !empty($data['year'])) {
                // $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
                // $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
                // $where = " AND t0.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
                // $from_year_be = $this->account_transaction->get_budget_year($s_date);
                $day = '';
                $month = @$data['month'];
                $year_be = $data['year'];
                $year = (@$data['year']-543);
                $last_day_of_month = date("t", strtotime($year."-".sprintf("%02d",$month)."-01"));
                $textTitle = "ระหว่างวันที่ 01 ".$month_arr[$month]." ".($year+543);
                $textTitle .= " ถึงวันที่ ".$last_day_of_month." ".$month_arr[$month]." ".($year+543);
                $where = " AND mount = '".$month."'  AND year = '".$year."'   ";
                $s_date = $year.'-'.sprintf("%02d",@$month).'-01';
                $budget_year_be = $this->account_transaction->get_budget_year($s_date." 00:00:00.000");
                $where_acc = " AND MONTH(account_datetime) = ".$month." AND YEAR(account_datetime) = ".$year;
                $where_prev_budget .= " AND t1.account_datetime < '".$s_date." 00:00:00.000' AND budget_year = ".$budget_year_be;
            } else {
                $day = '';
                $month = '';
                $year_be = $data['year'];
                $year = (@$data['year']-543);
                $acc_year = $this->db->select("*")->from("coop_account_year")->where("account_year = '".$data["year"]."'")->get()->row_array();
                if(!empty($acc_year)) {
                    $textTitle = "ระหว่างวันที่ ".$this->center_function->ConvertToThaiDate($acc_year['start_date'], 0)." ถึง วันที่ ".$this->center_function->ConvertToThaiDate($acc_year['end_date'], 0);
                } else {
                    $textTitle = "ระหว่างวันที่ ".$this->center_function->ConvertToThaiDate($year."-01-01", 0)." ถึง วันที่ ".$this->center_function->ConvertToThaiDate($year."-12-31", 0);
                }
                $where = " AND mount = '".$month."'  AND year = '".$year."'   ";
                $where_acc = " AND budget_year = ".$year_be;
                $where_prev_budget .= " AND 1=2";//Shall not have any perv detail for current year
            }
        }

        $results['day'] = $day;
        $results['month'] = $month;
        $results['year'] = $year;
        $results['textTitle'] = $textTitle;

        if($data['month'] == 1 ) {
            $old_mount = 12;
            $old_year =  $year-1;
        } else {
            $old_mount = $data['month'] -1 ;
            $old_year =  $year;
        }

        //ยอดยกมาเงินงบทดลอง
        //Get budget of last year
        $prev_budgets = array();
        $prev_budget_year_raw = $this->db->select("*")->from("coop_account_budget_year")->where("year = '".($year_be - 1)."' AND account_chart_id NOT LIKE '4%' AND account_chart_id NOT LIKE '5%'")->get()->result_array();
        foreach($prev_budget_year_raw as $budget) {
            if(!array_key_exists($budget["account_chart_id"], $prev_budgets)) {
                $prev_budgets[$budget["account_chart_id"]] = $budget;
            }
        }
        $prev_detail_raw = $this->db->select("t2.account_chart_id, t2.account_type, SUM(t2.account_amount) as amount, t3.entry_type")
                                    ->from("coop_account as t1")
                                    ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                                    ->join("coop_account_chart as t3", "t2.account_chart_id = t3.account_chart_id", "inner")
                                    ->where($where_prev_budget)
                                    ->group_by("t2.account_chart_id, t2.account_type")
                                    ->get()->result_array();
        foreach($prev_detail_raw as $detail) {
            if(!empty($prev_budgets[$detail["account_chart_id"]])) {
                $prev_budgets[$detail["account_chart_id"]]["budget_amount"] += $detail["entry_type"] == 1 && $detail["account_type"] == "debit" ? $detail["amount"]
                                                                                : ($detail["entry_type"] == 1 && $detail["account_type"] == "credit" ? ($detail["amount"] * (-1))
                                                                                : ($detail["entry_type"] == 2 && $detail["account_type"] == "credit" ? $detail["amount"]
                                                                                : ($detail["amount"] * (-1))));
            } else {
                $prev_budgets[$detail["account_chart_id"]]["budget_amount"] += $detail["entry_type"] == 1 && $detail["account_type"] == "debit" ? $detail["amount"]
                                                                                : ($detail["entry_type"] == 1 && $detail["account_type"] == "credit" ? ($detail["amount"] * (-1))
                                                                                : ($detail["entry_type"] == 2 && $detail["account_type"] == "credit" ? $detail["amount"]
                                                                                : ($detail["amount"] * (-1))));
                $prev_budgets[$detail["account_chart_id"]]["budget_type"] = $detail["entry_type"] == 1 ? "debit" : "credit";
            }
        }
        $results["prev_budgets"] = $prev_budgets;

        //ยอดยกมาเงินยอดระหว่างเดือน
        $rs_chart_ledger = array();
        $details = $this->db->select("t2.*")
                        ->from("coop_account as t1")
                        ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                        ->where("1=1 AND (t1.account_status != 2 OR t1.account_status IS null)".$where_acc)
                        ->get()->result_array();
        foreach($details as $detail) {
            $rs_chart_ledger[$detail['account_chart_id']][$detail["account_type"]] += $detail["account_amount"];
        }
        $results['rs'] = $rs_chart_ledger;

        //ยอดยกมารายการทั้งหมด
        $this->db->select(array(
            '*'
        ));
        $this->db->from('coop_account_chart');
        $this->db->order_by("account_chart_id ASC");
        $data_chart = $this->db->get()->result_array();

        $results['data_chart'] = @$data_chart;

        return $results;
    }

    function coop_account_experimental_budget_excel_year() {
        $arr_data = array();
        $year = $_GET["year"];
        $prev_year = $_GET["year"] - 1;
        //Get Prev Year budget
        $prev_year_budget_raw = $this->db->select("*")->from("coop_account_budget_year")->where("year <= '".$prev_year."'")->order_by("year DESC")->get()->result_array();
        $prev_year_budgets = array();
        foreach($prev_year_budget_raw as $budget) {
            if(!array_key_exists($budget["account_chart_id"], $prev_year_budgets)) {
                $prev_year_budgets[$budget["account_chart_id"]] = $budget;
            }
        }
        $arr_data["prev_budgets"] = $prev_year_budgets;

         //Get Current Year budget
         $year_budget_raw = $this->db->select("*")->from("coop_account_budget_year")->where("year <= '".$year."'")->order_by("year DESC")->get()->result_array();
         $year_budgets = array();
         foreach($year_budget_raw as $budget) {
            if(!array_key_exists($budget["account_chart_id"], $year_budgets)) {
                $year_budgets[$budget["account_chart_id"]] = $budget;
            }
         }
         $arr_data["year_budgets"] = $year_budgets;

        //Get Current year transaction
        $account_detail_raw = $this->db->select("t2.account_type, t2.account_chart_id, SUM(t2.account_amount) as amount")
                                        ->from("coop_account as t1")
                                        ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                                        ->where("(t1.account_status IS NULL OR t1.account_status != 2) AND YEAR(t1.account_datetime) = '".($year - 543)."'")
                                        ->group_by("t2.account_type, t2.account_chart_id")
                                        ->get()->result_array();
        $account_details = array();
        foreach($account_detail_raw as $detail) {
            $account_details[$detail["account_chart_id"]][$detail["account_type"]] = $detail["amount"];
        }
        $arr_data["rs"] = $account_details;

        //Get all account chart
        $account_charts = $this->db->select("*")->from("coop_account_chart")->get()->result_array();
        $arr_data["data_chart"] = $account_charts;

        $this->load->view('account/coop_account_experimental_budget_excel_year', $arr_data);
    }
    

    function coop_account_balance_sheet(){
        $arr_data = array();
        $arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $this->libraries->template('account/coop_account_balance_sheet',$arr_data);
    }

    function ajax_check_account_balance_sheet(){
        echo "success";
    }

    function coop_account_balance_sheet_excel(){
        if(empty($_POST)) { exit; }

        $arr_data = array();
        $data = $this->get_account_balance_data($_POST);

        $arr_data["from_date"] = $data["from_date"];
        $arr_data["thur_date"] = $data["thur_date"];
        $arr_data["prev_date"] = $data["prev_date"];
        $arr_data["from_date_header"] = $data["from_date_header"];
        $arr_data["thur_date_header"] = $data["thur_date_header"];
        $arr_data["prev_date_header"] = $data["prev_date_header"];
        $arr_data["account_charts"] = $data["account_charts"];
        $arr_data["prev_year_budgets"] = $data["prev_year_budgets"];
        $arr_data["year_budgets"] = $data["year_budgets"];

        $this->load->view('account/coop_account_balance_sheet_excel',$arr_data);
    }

    function coop_account_balance_sheet_pdf(){
        if(empty($_POST)) { exit; }

        $arr_data = array();

        $data = $this->get_account_balance_data($_POST);
    
        $arr_data["from_date"] = $data["from_date"];
        $arr_data["thur_date"] = $data["thur_date"];
        $arr_data["prev_date"] = $data["prev_date"];
        $arr_data["from_date_header"] = $data["from_date_header"];
        $arr_data["thur_date_header"] = $data["thur_date_header"];
        $arr_data["prev_date_header"] = $data["prev_date_header"];
        $arr_data["account_charts"] = $data["account_charts"];
        $arr_data["prev_year_budgets"] = $data["prev_year_budgets"];
        $arr_data["year_budgets"] = $data["year_budgets"];

        $this->load->view('account/coop_account_balance_sheet_pdf',$arr_data);
    }

    function get_account_balance_data($data) {
        $result = array();
        $month_arr = $this->month_arr;
        $month_short_arr = $this->month_short_arr;

        $result['month_arr'] = $month_arr;
        $result['month_short_arr'] = $month_short_arr;

        $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();

        $where_prev_year = "1=1"; // Condition for prev year budget
        $where_prev = "1=1"; // condition for prev account data for current year
        $where_period = "1=1"; // condition for account data for current period
        if(!empty($data["from_date"]) && !empty($data["thru_date"])) {
            $from_date_arr = explode('/',$data['from_date']);
            $from_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",$from_date_arr[0]).' 00:00:00.000';
            $thur_date_arr = explode('/',$data['thru_date']);
            $thur_date = ($thur_date_arr[2] - 543).'-'.sprintf("%02d",$thur_date_arr[1]).'-'.sprintf("%02d",$thur_date_arr[0]).' 23:59:59.000';
            $prev_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",($from_date_arr[0] -1)).' 00:00:00.000';

            $from_date_header = $this->center_function->ConvertToThaiDate($from_date,'1','0');
            $thur_date_header = $this->center_function->ConvertToThaiDate($thur_date,'1','0');
            $prev_date_header = $this->center_function->ConvertToThaiDate($prev_date,'1','0');

            $from_year_be = $from_date_arr[2];
            $from_month = $from_date_arr[1];

            $budget_year_be = $this->account_transaction->get_budget_year($from_date);

            if(empty($from_month)) $from_month = $account_period->accm_month_ini;

            $where_prev_year = "year <= '".($budget_year_be - 1)."'";
            $where_prev = "t1.budget_year = '".$budget_year_be."' AND t1.account_datetime < '".$from_date."'";
            $where_profit_prev = "t1.budget_year = '".$budget_year_be."' AND t1.account_datetime < '".$from_date."'";
            $where_period = "t1.account_datetime BETWEEN '".$from_date."' AND '".$thur_date."'";
        } else if (!empty($data["month"]) && !empty($data["year"])) {
            $from_date = ($data["year"] - 543).'-'.sprintf("%02d",$data["month"]).'-01 00:00:00.000';
            $thur_date = date('Y-m-t',strtotime(($data["year"] - 543).'-'.sprintf("%02d",$data["month"]).'-01')).' 23:59:59.000';
            if($data["month"] == 1) {
                $prev_date  = ($data["year"] - 543 - 1).'-12-31 00:00:00.000';
            } else {
                $prev_date  = date('Y-m-t',strtotime(($data["year"] - 543).'-'.sprintf("%02d",($data["month"] - 1)).'-01')).' 23:59:59.000';
            }

            $thur_month = $data["month"];
            $prev_month = (int) $data["month"] != 1 ? $data["month"] - 1 : 12;
            $from_date_header = $this->center_function->ConvertToThaiDate($from_date,'1','0');
            $thur_date_header = $this->center_function->ConvertToThaiDate($thur_date,'1','0');
            $prev_date_header = $this->center_function->ConvertToThaiDate($prev_date,'1','0');

            $budget_year_be = $this->account_transaction->get_budget_year($from_date);

            $where_prev_year = "year <= '".($budget_year_be - 1)."'";
            $where_prev = "t1.budget_year = '".$budget_year_be."' AND t1.account_datetime < '".$from_date."'";
            $where_profit_prev = "t1.budget_year = '".$budget_year_be."' AND t1.account_datetime < '".$from_date."'";
            $where_period = "t1.account_datetime BETWEEN '".$from_date."' AND '".$thur_date."'";
        } else if (!empty($data["year"])) {
            $thru_month = $account_period->accm_month_ini == 1 ? 12 : $account_period->accm_month_ini - 1;
            $thru_year = $account_period->accm_month_ini == 1 ? $data["year"] : $data["year"] + 1;
            $from_date = ($data["year"] - 543).'-'.sprintf("%02d",$account_period->accm_month_ini).'-01 00:00:00.000';
            $thur_date = date('Y-m-t',strtotime(($thru_year - 543).'-'.sprintf("%02d",$thru_month).'-01')).' 23:59:59.000';
            $prev_date  =  date('Y-m-t',strtotime(($thru_year - 543 - 1).'-'.sprintf("%02d",$thru_month).'-01')).' 23:59:59.000';

            $from_date_header = $this->month_arr[12];
            $thur_date_header = $this->month_arr[12]." ".$thru_year;
            $prev_date_header = $this->month_arr[12]." ".($thru_year - 1);

            $where_prev_year = "year <= '".($data["year"] - 1)."'";
            $where_prev = "1=2";//Should not have any data
            $where_profit_prev = "t1.budget_year = '".($data["year"]-1)."'";
            $where_period = "t1.budget_year = '".$data["year"]."'";
        }

        $result["from_date"] = $from_date;
        $result["thur_date"] = $thur_date;
        $result["prev_date"] = $prev_date;

        $result["from_date_header"] = $from_date_header;
        $result["thur_date_header"] = $thur_date_header;
        $result["prev_date_header"] = $prev_date_header;

        //Get Account Chart
        $account_charts = $this->db->select("*")->from("coop_account_chart")->where("account_chart_id NOT LIKE '4%' AND account_chart_id NOT LIKE '5%' AND type IN (1,3)")->order_by("account_chart_id")->get()->result_array();
        $result["account_charts"] = $account_charts;

        //Get previous year balance
        $prev_year_budgets = array();
        $prev_year_budget_raw = $this->db->select("account_chart_id, budget_amount, budget_type")->from("coop_account_budget_year")->where($where_prev_year." AND account_chart_id NOT LIKE '4%' AND account_chart_id NOT LIKE '5%'")->order_by("year desc")->get()->result_array();
        foreach($prev_year_budget_raw as $budget) {
            if(!array_key_exists($budget["account_chart_id"], $prev_year_budgets)) {
                $prev_year_budgets[$budget["account_chart_id"]] = $budget["budget_amount"];
            }
        }

        $perv_account_raw = $this->db->select("t2.account_chart_id, t2.account_type, t2.account_amount, t3.entry_type")
                                        ->from("coop_account as t1")
                                        ->join("coop_account_detail as t2", "t1.account_id = t2.account_id AND t2.account_chart_id != '3005'", "INNER")
                                        ->join("coop_account_chart as t3", "t2.account_chart_id = t3.account_chart_id", "LEFT")
                                        ->where($where_prev." AND t1.account_status = 0")
                                        ->get()->result_array();
        foreach($perv_account_raw as $account) {
            $nature = $account["entry_type"] == 1 ? "debit" : "credit";
            if($nature == $account["account_type"]) {
                $prev_year_budgets[$account["account_chart_id"]] += $account["account_amount"];
            } else {
                $prev_year_budgets[$account["account_chart_id"]] -= $account["account_amount"];
            }
        }

        //Get period account
        $year_budgets = $prev_year_budgets;
        $account_raw = $this->db->select("t2.account_chart_id, t2.account_type, t2.account_amount, t3.entry_type")
                                ->from("coop_account as t1")
                                ->join("coop_account_detail as t2", "t1.account_id = t2.account_id AND t2.account_chart_id != '3005'", "INNER")
                                ->join("coop_account_chart as t3", "t2.account_chart_id = t3.account_chart_id", "LEFT")
                                ->where($where_period." AND (t1.account_status = 0 OR t1.account_status IS NULL)")
                                ->get()->result_array();
        foreach($account_raw as $account) {
            $nature = $account["entry_type"] == 1 ? "debit" : "credit";
            if($nature == $account["account_type"]) {
                $year_budgets[$account["account_chart_id"]] += $account["account_amount"];
            } else {
                $year_budgets[$account["account_chart_id"]] -= $account["account_amount"];
            }
        }

        //Special chart id
        //3005 profit - loss
        //Prev
        $profit_loss = $this->db->select("t2.account_type, SUM(t2.account_amount) as amount")
                                    ->from("coop_account as t1")
                                    ->join("coop_account_detail as t2", "t1.account_id = t2.account_id AND (t2.account_chart_id like '4%' OR t2.account_chart_id like '5%')")
                                    ->where($where_profit_prev." AND (t1.account_status = 0 OR t1.account_status IS NULL)")
                                    ->group_by("t2.account_type")
                                    ->get()->result_array();
        $profit_total = 0;
        foreach($profit_loss as $detail) {
            if($detail["account_type"] == 'credit') {
                $profit_total += $detail["amount"];
            } else if ($detail["account_type"] == 'debit') {
                $profit_total -= $detail["amount"];
            }
        }
        $prev_year_budgets[3005] += $profit_total;

        //Current
        $profit_total = 0;
        $profit_loss = $this->db->select("t2.account_type, SUM(t2.account_amount) as amount")
                                ->from("coop_account as t1")
                                ->join("coop_account_detail as t2", "t1.account_id = t2.account_id AND (t2.account_chart_id like '4%' OR t2.account_chart_id like '5%')")
                                ->where($where_period." AND (t1.account_status = 0 OR t1.account_status IS NULL)")
                                ->group_by("t2.account_type")
                                ->get()->result_array();
        $profit_total = 0;
        foreach($profit_loss as $detail) {
            if($detail["account_type"] == 'credit') {
                $profit_total += $detail["amount"];
            } else if ($detail["account_type"] == 'debit') {
                $profit_total -= $detail["amount"];
            }
        }
        $year_budgets[3005] = $prev_year_budgets[3005] + $profit_total;

        $result["year_budgets"] = $year_budgets;
        $result["prev_year_budgets"] = $prev_year_budgets;

        return $result;
    }

    function coop_account_profit_lost_statement(){
        $arr_data = array();
        $arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $this->libraries->template('account/coop_account_profit_lost_statement',$arr_data);
    }

    function ajax_check_account_profit_lost_statement(){
        if(@$_POST['report_date'] != ''){
            $date_arr = explode('/',@$_POST['report_date']);
            $day = (int)@$date_arr[0];
            $month = (int)@$date_arr[1];
            $year = (int)@$date_arr[2];
            $year -= 543;
            $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
            $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
            $where = " AND t1.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        }else{
            if(@$_POST['month']!='' && @$_POST['year']!=''){
                $day = '';
                $month = @$_POST['month'];
                $year = (@$_POST['year']-543);
                $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
                $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
                $where = " AND t1.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            }else{
                $day = '';
                $month = '';
                $year = (@$_POST['year']-543);
                $where = " AND t1.account_datetime BETWEEN '".$year."-01-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";
            }
        }

        $this->db->select(array('t1.account_id',
            't1.account_datetime',
            't2.account_type'
        ));
        $this->db->from('coop_account as t1');
        $this->db->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "inner");
        $this->db->where("(t1.account_status <> '2' OR t1.account_status IS NULL) {$where}");

        $rs = $this->db->get()->result_array();
        $row = @$rs[0];
        if(@$row['account_id'] != ''){
            echo "success";
        }
    }

    function coop_account_profit_lost_statement_excel(){
        if(empty($_POST)) { exit; }
        $arr_data = array();

        $data = $this->get_account_profit_lost_data($_POST);

        $arr_data["from_date"] = $data["from_date"];
        $arr_data["thur_date"] = $data["thur_date"];
        $arr_data["prev_date"] = $data["prev_date"];
        $arr_data["from_date_header"] = $data["from_date_header"];
        $arr_data["thur_date_header"] = $data["thur_date_header"];
        $arr_data["prev_date_header"] = $data["prev_date_header"];
        $arr_data["account_charts"] = $data["account_charts"];
        $arr_data["prev_year_budgets"] = $data["prev_year_budgets"];
        $arr_data["year_budgets"] = $data["year_budgets"];

        $this->load->view('account/coop_account_profit_lost_statement_excel',$arr_data);
    }

    function coop_account_profit_lost_statement_pdf(){
        if(empty($_POST)) { exit; }
        $arr_data = array();

        $data = $this->get_account_profit_lost_data($_POST);

        $arr_data["from_date"] = $data["from_date"];
        $arr_data["thur_date"] = $data["thur_date"];
        $arr_data["prev_date"] = $data["prev_date"];
        $arr_data["from_date_header"] = $data["from_date_header"];
        $arr_data["thur_date_header"] = $data["thur_date_header"];
        $arr_data["prev_date_header"] = $data["prev_date_header"];
        $arr_data["account_charts"] = $data["account_charts"];
        $arr_data["prev_year_budgets"] = $data["prev_year_budgets"];
        $arr_data["year_budgets"] = $data["year_budgets"];

        $this->load->view('account/coop_account_profit_lost_statement_pdf',$arr_data);
    }

    function get_account_profit_lost_data($data) {
        $result = array();
        $month_arr = $this->month_arr;
        $month_short_arr = $this->month_short_arr;

        $result['month_arr'] = $month_arr;
        $result['month_short_arr'] = $month_short_arr;

        $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();

        $where_prev = "1=1"; // condition for prev account data for current year
        $where_period = "1=1"; // condition for account data for current period
        if(!empty($data["from_date"]) && !empty($data["thru_date"])) {
            $from_date_arr = explode('/',$data['from_date']);
            $from_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",$from_date_arr[0]).' 00:00:00.000';
            $thur_date_arr = explode('/',$data['thru_date']);
            $thur_date = ($thur_date_arr[2] - 543).'-'.sprintf("%02d",$thur_date_arr[1]).'-'.sprintf("%02d",$thur_date_arr[0]).' 23:59:59.000';
            $prev_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",($from_date_arr[0] -1)).' 00:00:00.000';

            $from_date_header = $this->center_function->ConvertToThaiDate($from_date,'1','0');
            $thur_date_header = $this->center_function->ConvertToThaiDate($thur_date,'1','0');
            $prev_date_header = $this->center_function->ConvertToThaiDate($prev_date,'1','0');

            $from_year_be = $from_date_arr[2];
            $from_month = $from_date_arr[1];

            if(empty($from_month)) $from_month = $account_period->accm_month_ini;
            $year_acc = $account_period->accm_month_ini <= $from_month ? $from_year_be : $from_year_be - 1;

            $from_prev_date = ($year_acc - 543)."-".sprintf("%02d",$account_period->accm_month_ini)."-01 00:00:00.000";
            $where_prev = "t1.account_datetime >= '".$from_prev_date."' AND t1.account_datetime < '".$from_date."'";

            $thur_year_be = $thur_date_arr[2];
            $thur_month = $thur_date_arr[1];

            if(empty($thur_month)) $thur_month = $account_period->accm_month_ini;
            $thur_year_acc = $account_period->accm_month_ini <= $thur_month ? $thur_year_be : $thur_year_be - 1;
            $from_date_str = ($thur_year_acc - 543)."-".sprintf("%02d",$account_period->accm_month_ini)."-01 00:00:00.000";
            $where_period = "t1.account_datetime BETWEEN '".$from_date_str."' AND '".$thur_date."'";
        } else if (!empty($data["month"]) && !empty($data["year"])) {
            $from_date = ($data["year"] - 543).'-'.sprintf("%02d",$data["month"]).'-01 00:00:00.000';
            $thur_date = date('Y-m-t',strtotime(($data["year"] - 543).'-'.sprintf("%02d",$data["month"]).'-01')).' 23:59:59.000';
            if($data["month"] == 1) {
                $prev_date  = ($data["year"] - 543 - 1).'-12-31 00:00:00.000';
                $year_acc = $account_period->accm_month_ini <= $data["month"] ? $data["year"] - 1 : $data["year"] - 2;
                $thur_year_acc = $account_period->accm_month_ini <= $data["month"] ? $data["year"] : $data["year"] - 1;
            } else {
                $prev_date  = date('Y-m-t',strtotime(($data["year"] - 543).'-'.sprintf("%02d",($data["month"] - 1)).'-01')).' 23:59:59.000';
                $year_acc = $account_period->accm_month_ini <= $data["month"] ? $data["year"] : $data["year"] - 1;
                $thur_year_acc = $account_period->accm_month_ini <= $data["month"] ? $data["year"] : $data["year"] - 1;
            }

            $thur_month = $data["month"];
            $prev_month = (int) $data["month"] != 1 ? $data["month"] - 1 : 12;
            $from_date_header = $this->month_arr[(int) $data["month"]];
            $thur_date_header = $this->month_arr[(int) $thur_month];
            $prev_date_header = $this->month_arr[(int) $prev_month];

            $from_prev_date = ($year_acc - 543)."-".sprintf("%02d",$account_period->accm_month_ini)."-01 00:00:00.000";
            $where_prev = "t1.account_datetime >= '".$from_prev_date."' AND t1.account_datetime < '".$from_date."'";
            $from_date_str = ($thur_year_acc - 543)."-".sprintf("%02d",$account_period->accm_month_ini)."-01 00:00:00.000";
            $where_period = "t1.account_datetime BETWEEN '".$from_date_str."' AND '".$thur_date."'";
        } else if (!empty($data["year"])) {
            $thru_month = $account_period->accm_month_ini == 1 ? 12 : $account_period->accm_month_ini - 1;
            $thru_year = $account_period->accm_month_ini == 1 ? $data["year"] : $data["year"] + 1;
            $from_date = ($data["year"] - 543).'-'.sprintf("%02d",$account_period->accm_month_ini).'-01 00:00:00.000';
            $thur_date = date('Y-m-t',strtotime(($thru_year - 543).'-'.sprintf("%02d",$thru_month).'-01')).' 23:59:59.000';
            $prev_date  =  date('Y-m-t',strtotime(($thru_year - 543 - 1).'-'.sprintf("%02d",$thru_month).'-01')).' 23:59:59.000';

            $from_date_header = $this->month_arr[12];
            $thur_date_header = $this->month_arr[12]." ".$thru_year;
            $prev_date_header = $this->month_arr[12]." ".($thru_year - 1);

            $from_prev_month = $account_period->accm_month_ini;
            $from_prev_date = ($data["year"] - 543 - 1)."-".sprintf("%02d",$from_prev_month)."-01 00:00:00.000";
            $where_prev = "t1.account_datetime >= '".$from_prev_date."' AND t1.account_datetime < '".$from_date."'";

            $from_date_str = ($data["year"] - 543)."-".sprintf("%02d",$from_prev_month)."-01 00:00:00.000";
            $where_period = "t1.account_datetime BETWEEN '".$from_date."' AND '".$thur_date."'";
        }

        $result["from_date"] = $from_date;
        $result["thur_date"] = $thur_date;
        $result["prev_date"] = $prev_date;
        $result["from_date_header"] = $from_date_header;
        $result["thur_date_header"] = $thur_date_header;
        $result["prev_date_header"] = $prev_date_header;

        //Get Account Chart
        $account_charts = $this->db->select("*")->from("coop_account_chart")->where("account_chart_id NOT LIKE '1%' AND account_chart_id NOT LIKE '2%' AND account_chart_id NOT LIKE '3%' AND type IN (1,3)")->order_by("account_chart_id")->get()->result_array();
        $result["account_charts"] = $account_charts;

        //Get previous year balance
        $prev_year_budgets = array();
        $perv_account_raw = $this->db->select("t2.account_chart_id, t2.account_type, t2.account_amount, t3.entry_type")
                                        ->from("coop_account as t1")
                                        ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "INNER")
                                        ->join("coop_account_chart as t3", "t2.account_chart_id = t3.account_chart_id", "LEFT")
                                        ->where($where_prev." AND t1.account_status = 0")
                                        ->get()->result_array();
        foreach($perv_account_raw as $account) {
            $nature = $account["entry_type"] == 1 ? "debit" : "credit";
            if($nature == $account["account_type"]) {
                $prev_year_budgets[$account["account_chart_id"]] += $account["account_amount"];
            } else {
                $prev_year_budgets[$account["account_chart_id"]] -= $account["account_amount"];
            }
        }

        $result["prev_year_budgets"] = $prev_year_budgets;

        //Get period account
        $year_budgets = array();
        $account_raw = $this->db->select("t2.account_chart_id, t2.account_type, t2.account_amount, t3.entry_type")
                                ->from("coop_account as t1")
                                ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "INNER")
                                ->join("coop_account_chart as t3", "t2.account_chart_id = t3.account_chart_id", "LEFT")
                                ->where($where_period." AND t1.account_status = 0")
                                ->get()->result_array();
        foreach($account_raw as $account) {
            $nature = $account["entry_type"] == 1 ? "debit" : "credit";
            if($nature == $account["account_type"]) {
                $year_budgets[$account["account_chart_id"]] += $account["account_amount"];
            } else {
                $year_budgets[$account["account_chart_id"]] -= $account["account_amount"];
            }
        }
        $result["year_budgets"] = $year_budgets;

        return $result;
    }

    public function coop_adjusted_entry() {
        $arr_data = array();

        $x=0;
        $join_arr = array();
        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('CAST(account_datetime AS date) AS Converted');
        $this->paginater_all->main_table('coop_account');
        $this->paginater_all->where("status_audit = '1'");
        $this->paginater_all->page_now(@$_GET["page"]);
        $this->paginater_all->per_page(10);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->group_by('Converted,account_description');
        $this->paginater_all->order_by('account_id DESC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();

        $b = 0;
        $max_data_val = 0;
        $max_data_val = $b+1;

        //กำหนดจำนวนหน้าใน page นั้นๆ
        $this->db->select(array('*'));
        $this->db->from('coop_account AS t1');
        $this->db->join('(SELECT CAST(account_datetime AS date) AS Converted FROM coop_account GROUP BY Converted, account_description) AS t2','CAST(t1.account_datetime AS date) = t2.Converted','inner');
        $this->db->where("status_audit = '1'");
        $this->db->group_by('Converted,account_description');

        $row_count = $this->db->get()->result_array();
        foreach ($row_count as $key=>$val){ $b ++; }

        $max_data_val =$b+1 ;
        $paging = $this->pagination_center->paginating($row['page'], $max_data_val, $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
        $i =  $row['page_start'];
        $arr_data['num_rows'] = $max_data_val;
        $arr_data['paging'] = $paging;
        $arr_data['i'] = $i;

        $row = array();
        $this->db->select(array('t0.*','t1.*','t2.account_chart'));
        $this->db->from('coop_account as t0 ');
        $this->db->join('coop_account_detail as t1','t0.account_id = t1.account_id','inner');
        $this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','inner');
        $this->db->where("t0.status_audit = '1'");
        $this->db->order_by("account_detail_id DESC");
        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;
        $data_account_detail = array();

        foreach($row['data'] as $key => $row_all) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row_all['account_datetime']);
            foreach($row_all as $key2 => $row_detail_all){
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail_all['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_description']][$row_detail_all['account_chart_id']]['account_chart_id'] = $row_detail_all['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_description']][$row_detail_all['account_chart_id']]['account_chart'] = $row_detail_all['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_description']][$row_detail_all['account_chart_id']]['account_type'] = $row_detail_all['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_description']][$row_detail_all['account_chart_id']]['account_amount'] += $row_detail_all['account_amount'];
            }
        }
        $sort_array = array_map(function($array_account_datetime){
            return array_map(function($var){
                ksort($var);
                return $var;
            },$array_account_datetime);

        },$data_account_detail);

        //ตั้งค่าว่ารายการในแต่ละหน้าจะมีกี่รายการ ตั้งแต่รายการที่เท่าไรถึงเท่าไร
        if(empty($_GET["page"])){
            $firest_p = 1;
        }else{
            $firest_p =$_GET["page"];
        }
        $max_list = (10 *(@$firest_p));
        $min_list = (10 *(@$firest_p-1));

        $arr_data['max_list']  = $max_list;
        $arr_data['min_list']  = $min_list;
        //ตั้งค่าว่ารายการในแต่ละหน้าจะมีกี่รายการ ตั้งแต่รายการที่เท่าไรถึงเท่าไร

        $arr_data['data'] = $row['data'];
        $arr_data['space'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

        $this->libraries->template('account/coop_adjusted_entry',$arr_data);
    }
    public function account_save_adjusted() {
        $data = $_POST['data'];
        $account_datetime_arr = explode('/',$data['coop_account']['account_datetime']);
        $data['coop_account']['account_datetime'] = ($account_datetime_arr[2]-543)."-".sprintf('%02d',$account_datetime_arr[1])."-".sprintf('%02d',$account_datetime_arr[0]);

        $data_insert = array();
        $data_insert['account_description'] = $data['coop_account']['account_description'];
        $data_insert['account_datetime'] = $data['coop_account']['account_datetime'];
        $data_insert['account_status'] = '0';
        $data_insert['status_audit'] = '1';
        $this->db->insert('coop_account', $data_insert);
        $account_id = $this->db->insert_id();

        foreach($data['coop_account_detail'] as $key => $value){
            $data_insert = array();
            $data_insert['account_id'] = $account_id;
            $data_insert['account_type'] = $value['account_type'];
            $data_insert['account_amount'] = $value['account_amount'];
            $data_insert['account_chart_id'] = $value['account_chart_id'];
            $this->db->insert('coop_account_detail', $data_insert);
        }
        echo"<script> document.location.href='".base_url(PROJECTPATH.'/coop_adjusted_entry')."'; </script>";
    }
    public function account_excel_tranction_voucher() {
        $detail = $_POST['detail'];
        $date = $_POST['date'];
        $arr_data = array();

        if(@$date != ''){
            $date_arr = explode('/',@$date);
            $day = (int)@$date_arr[0];
            $month = (int)@$date_arr[1];
            $year = (int)@$date_arr[2];
            $year -= 543;
            $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
            $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
        }
        $arr_data['day'] = $day;
        $arr_data['month'] = $month;
        $arr_data['year'] = $year;

        $row = array();
        $this->db->select(array('t0.*','t1.*','t2.account_chart'));
        $this->db->from('coop_account as t0 ');
        $this->db->join('coop_account_detail as t1','t0.account_id = t1.account_id','inner');
        $this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','inner');
        $this->db->where(" 1=1 AND t0.status_audit <> '1'  OR t0.status_audit is null AND t0.account_description ='{$detail}' AND t0.account_datetime LIKE '{$date}%' " );
        $this->db->order_by("t0.account_datetime ASC,account_type DESC,account_chart_id DESC");

        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;

        foreach($row['data']  as $key => $row) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row['account_datetime']);
            foreach($row as $key2 => $row_detail){
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_chart_id'] = $row_detail['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_chart'] = $row_detail['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_type'] = $row_detail['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_amount'] += $row_detail['account_amount'];
            }
        }

        $sort_array = array_map(function($array_account_datetime){
            return array_map(function($var){
                ksort($var);
                return $var;
            },$array_account_datetime);

        },$data_account_detail);

        foreach($sort_array  as $key => $sort_date) {
            foreach($sort_date as $key2 => $row_detail) {
                $this->db->select(array(
                    'account_id','journal_ref'
                ));
                $this->db->from('coop_account');
                $this->db->where("account_description ='{$key2}' AND account_datetime LIKE '{$key}%' " );
                $rs = $this->db->get()->result_array();
                $journal_ref['journal_ref'] = $rs[0]['journal_ref'];
                $journal_ref['account_datetime'] = $key;
                $journal_ref['account_description'] = $key2;
            }
        }

        $arr_data['journal_ref'] = $journal_ref;
        $arr_data['data'] = $data_account_detail;
        $this->load->view('account/account_excel_tranction_voucher',$arr_data);
    }

    public function account_pdf_tranction_voucher() {
        //Get format of voucher.
        $setting = $this->db->select("*")->from("coop_account_setting")->where("type = 'voucher_type'")->get()->row();
        $voucher_type = !empty($setting) ? $setting->value : 0;

        $account_id = $_POST['detail'];
        $date = $_POST['date'];
        $account_detail_ids = $_POST['account_detail_ids'];
        $arr_data = array();
        if(@$date != ''){
            $date_arr = explode('-',@$date);
            $day = (int) $date_arr[2];
            $month = (int) $date_arr[1];
            $year = (int) $date_arr[0];
            $s_date = $year.'-'.sprintf("%02d", $month).'-'.sprintf("%02d", $day).' 00:00:00.000';
            $e_date = $year.'-'.sprintf("%02d", $month).'-'.sprintf("%02d", $day).' 23:59:59.000';
        }
        $arr_data['day'] = $day;
        $arr_data['month'] = $month;
        $arr_data['year'] = $year;

        if($voucher_type == 1) {
            if(!empty($_POST['account_detail_ids'])) {
                $accounts = $this->db->select("t1.journal_ref,
                                                t1.journal_type,
                                                t1.account_datetime,
                                                t1.account_description,
                                                t1.user_id,
                                                t1.account_id,
                                                t2.user_name")
                                    ->from("coop_account t1")
                                    ->join("coop_user t2", "t1.user_id = t2.user_id", "left")
                                    ->where("t1.account_id in (".implode(',',$account_detail_ids).")")
                                    ->get()->result_array();
                $account_details = array();
                foreach($accounts as $account) {
                    $datas = array();
                    $account_id = $account['account_id'];
                    if($account->journal_type == "PV" || $account->journal_type == "P") {
                        $details = $this->account_transaction->get_account_details($account_id, "credit");
                        foreach($details as $detail) {
                            $datas[] = $detail;
                        }
                        $details = $this->account_transaction->get_account_details($account_id, "debit");
                        foreach($details as $detail) {
                            $datas[] = $detail;
                        }
                    } else if($account->journal_type == "RV" || $account->journal_type == "R") {
                        $details = $this->account_transaction->get_account_details($account_id, "debit");
                        foreach($details as $detail) {
                            $datas[] = $detail;
                        }
                        $details = $this->account_transaction->get_account_details($account_id, "credit");
                        foreach($details as $detail) {
                            $datas[] = $detail;
                        }
                    } else {
                        $details = $this->account_transaction->get_account_details($account_id, NULL);
                        foreach($details as $detail) {
                            $datas[] = $detail;
                        }
                    }

                    $account_details[$account_id]["journal_ref"] = $account['journal_ref'];
                    $account_details[$account_id]["datetime"] = $account['account_datetime'];
                    $account_details[$account_id]["description"] = $account['account_description'];
                    $account_details[$account_id]['datas'] = $datas;
                    if(!empty($account['user_id']) && $account['user_id'] != 1) $account_details[$account_id]['user_name'] = $account['user_name'];
                }
            } else {
                $account_details = array();
            }
            $arr_data['datas'] = $account_details;
            $this->load->view('account/account_pdf_tranction_voucher_format_1',$arr_data);
        } else if ($voucher_type == 2) {
            if(!empty($_POST['account_detail_ids'])) {
                $account_details = $this->db->select("t1.account_id, t1.journal_ref, t1.journal_type, t1.account_datetime, t2.account_amount, t2.account_chart_id, t2.description, t2.account_type, t3.user_name, t4.account_chart")
                                            ->from("coop_account_detail as t2")
                                            ->join("coop_account as t1", "t1.account_id = t2.account_id", "left")
                                            ->join("coop_user as t3", "t1.user_id = t3.user_id", "left")
                                            ->join("coop_account_chart as t4", "t2.account_chart_id = t4.account_chart_id", "left")
                                            ->where("t2.account_detail_id in (".implode(',',$account_detail_ids).")")
                                            ->get()->result_array();
            } else {
                $account_details = array();
            }
            $arr_data['datas'] = $account_details;
            $this->load->view('account/account_pdf_tranction_voucher',$arr_data);
        } else if ($voucher_type == 3) {
            if(!empty($_POST['account_detail_ids'])) {
                $account_details = $this->db->select("t1.account_id, t1.journal_ref, t1.journal_type, t1.account_datetime, t2.account_amount, t2.account_chart_id, t2.description, t2.account_type, t3.user_name, t4.account_chart")
                                            ->from("coop_account_detail as t2")
                                            ->join("coop_account as t1", "t1.account_id = t2.account_id", "left")
                                            ->join("coop_user as t3", "t1.user_id = t3.user_id", "left")
                                            ->join("coop_account_chart as t4", "t2.account_chart_id = t4.account_chart_id", "left")
                                            ->where("t2.account_detail_id in (".implode(',',$account_detail_ids).")")
                                            ->get()->result_array();
            } else {
                $account_details = array();
            }
            $arr_data['datas'] = $account_details;
            $this->load->view('account/account_pdf_tranction_voucher_A5',$arr_data);
        } else if ($voucher_type == 4) {
            if(!empty($_POST['account_detail_ids'])) {
                $account_details = $this->db->select("t1.account_id, t1.journal_ref, t1.journal_type, t1.account_datetime, t2.description as detail_desc, t2.account_amount, t2.account_chart_id, t2.description, t2.account_type, t3.user_name, t4.account_chart")
                                            ->from("coop_account_detail as t2")
                                            ->join("coop_account as t1", "t1.account_id = t2.account_id", "left")
                                            ->join("coop_user as t3", "t1.user_id = t3.user_id", "left")
                                            ->join("coop_account_chart as t4", "t2.account_chart_id = t4.account_chart_id", "left")
                                            ->where("t2.account_detail_id in (".implode(',',$account_detail_ids).")")
                                            ->get()->result_array();
            } else {
                $account_details = array();
            }
            $arr_data['datas'] = $account_details;
            $this->load->view('account/account_pdf_tranction_voucher_1',$arr_data);
        }
    }

    public function account_excel_tranction_voucher_coop_buy() {
        $row = array();
        $this->db->select(array('t0.*','t1.*','t2.account_chart'));
        $this->db->from('coop_account_tmp_auto as t0 ');
        $this->db->join('coop_account_detail_tmp_auto as t1','t0.account_id = t1.account_id','inner');
        $this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','inner');
        $this->db->where(" ref_id = '{$_GET['account_buy_id']}' " );
        $this->db->order_by("t0.account_datetime ASC,account_type DESC,account_chart_id DESC");

        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;

        foreach($row['data']  as $key => $row) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row['account_datetime']);
            foreach($row as $key2 => $row_detail){
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_chart_id'] = $row_detail['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_chart'] = $row_detail['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_type'] = $row_detail['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_amount'] += $row_detail['account_amount'];
            }
        }

        $sort_array = array_map(function($array_account_datetime){
            return array_map(function($var){
                ksort($var);
                return $var;
            },$array_account_datetime);

        },$data_account_detail);

        foreach($sort_array  as $key => $sort_date) {
            foreach($sort_date as $key2 => $row_detail) {
                $this->db->select(array(
                    'account_id','journal_ref'
                ));
                $this->db->from('coop_account_tmp_auto');
                $this->db->where("account_description ='{$key2}' AND account_datetime LIKE '{$key}%' " );
                $rs = $this->db->get()->result_array();
                $journal_ref['journal_ref'] = $rs[0]['journal_ref'];
                $journal_ref['account_datetime'] = $key;
                $journal_ref['account_description'] = $key2;
            }
        }
        $arr_data['journal_ref'] = $journal_ref;
        $arr_data['data'] = $data_account_detail;

        $this->load->view('account/account_excel_tranction_voucher',$arr_data);
    }

    public function tranction_voucher() {
        $where_approve_date = '';
        $where_journal_type = '';
        if($_GET['approve_date']!=''){
            $approve_date_arr = explode('/',$_GET['approve_date']);
            $approve_day = stripslashes($approve_date_arr[0]);
            $approve_month = stripslashes($approve_date_arr[1]);
            $approve_year = stripslashes($approve_date_arr[2]);
            $_GET['approve_date'] = $approve_day."/".$approve_month."/".$approve_year;
            $approve_year -= 543;
            $approve_date = $approve_year.'-'.$approve_month.'-'.$approve_day;
            $where_approve_date = "AND t0.account_datetime like '{$approve_date}%' ";
        }else{
            $approve_date_arr = explode(' ',date('Y-m-d H:i:s'));
            $approve_date_arr = explode('-',$approve_date_arr[0]);
            $approve_day = stripslashes($approve_date_arr[2]);
            $approve_month = stripslashes($approve_date_arr[1]);
            $approve_year = stripslashes($approve_date_arr[0]);
            $_GET['approve_date'] = $approve_day."/".$approve_month."/".($approve_year + 543);
            $approve_date = $approve_year.'-'.$approve_month.'-'.$approve_day;
            $where_approve_date = "AND t0.account_datetime like '{$approve_date}%' ";
        }

        if(!empty($_GET["journal_type"])) {
            $where_journal_type = " AND t0.journal_type = '".$_GET["journal_type"]."'";
        }

        $arr_data = array();

        //Get account for cash
        $cash_account = $this->db->select("*")->from("coop_account_setting")->where("type = 'cash_chart_id'")->get()->row();
        $arr_data["cash_id"] = $cash_account->value;

        //Get type of account
        $account_types = $this->db->select("*")->from("coop_account_setting")->where("type = 'account_type'")->get()->row();
        $arr_data["account_types"] = json_decode($account_types->value);

        $row = array();
        $this->db->select(array('t0.*','t1.*','t2.account_chart'));
        $this->db->from('coop_account as t0 ');
        $this->db->join('coop_account_detail as t1','t0.account_id = t1.account_id','inner');
        $this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','inner');
        $this->db->where("t0.status_audit is null AND (t0.account_status is null OR t0.account_status = 0) {$where_approve_date} {$where_journal_type}");
        $this->db->order_by("account_type DESC ,account_detail_id DESC");
        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;
        $data_account_detail = array();

        foreach($row['data'] as $key => $row_all) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row_all['account_datetime']);
            foreach($row_all as $key2 => $row_detail_all){
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail_all['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['account_chart_id'] = $row_detail_all['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['account_chart'] = $row_detail_all['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['account_type'] = $row_detail_all['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['account_amount'] += $row_detail_all['account_amount'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['account_detail_id'] = $row_detail_all['account_detail_id'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['account_description'] = $row_detail_all['account_description'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['journal_ref'] = $row_detail_all['journal_ref'];
            }
        }
        $sort_array = array_map(function($array_account_datetime){
            return array_map(function($var){
                rsort($var);
                return $var;
            },$array_account_datetime);

        },$data_account_detail);

        //ตั้งค่าว่ารายการในแต่ละหน้าจะมีกี่รายการ ตั้งแต่รายการที่เท่าไรถึงเท่าไร
        if(empty($_GET["page"])){
            $firest_p = 1;
        }else{
            $firest_p =$_GET["page"];
        }
        $max_list = (10 *(@$firest_p));
        $min_list = (10 *(@$firest_p-1));
        $arr_data['max_list']  = $max_list;
        $arr_data['min_list']  = $min_list;
        //ตั้งค่าว่ารายการในแต่ละหน้าจะมีกี่รายการ ตั้งแต่รายการที่เท่าไรถึงเท่าไร

        $arr_data['approve_date'] = $row['approve_date'];
        $arr_data['data'] = $row['data'];
        $arr_data['data_account_detail'] = $data_account_detail;
        $arr_data['space'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

        //Get format of voucher.
        $setting = $this->db->select("*")->from("coop_account_setting")->where("type = 'voucher_type'")->get()->row();
        $voucher_type = !empty($setting) ? $setting->value : 0;
 
        if($voucher_type == 1) {
            $this->libraries->template('account/tranction_voucher_format_1',$arr_data);
        } else {
            $this->libraries->template('account/tranction_voucher',$arr_data);
        }
    }

    public function get_account_detail() {
        $result = array();
        $account_id = $_GET["account_id"];
        $account = $this->db->select("t1.account_id,
                                        t1.account_description,
                                        t1.account_datetime,
                                        t1.journal_ref,
                                        t1.journal_type,
                                        t1.user_id,
                                        t2.user_name")
                                    ->from("coop_account as t1")
                                    ->join("coop_user as t2", "t1.user_id = t2.user_id", "left")
                                    ->where("account_id = '{$account_id}'")
                            ->get()->row();
        $result["account_id"] = $account_id;
        $result["account_description"] = $account->account_description;
        $result["account_datetime"] = $account->account_datetime;
        $result["account_datetime_be"] = date("d", strtotime($account->account_datetime))."/".sprintf('%02d',date("m", strtotime($account->account_datetime)))."/".(date("Y", strtotime($account->account_datetime))+543);
        $result["journal_ref"] = $account->journal_ref;
        $result["journal_type"] = $account->journal_type;
        $result["user_name"] = $account->user_name;

        $details = $this->db->select("t1.account_id,
                                        t1.account_detail_id,
                                        t1.account_type,
                                        t1.account_amount,
                                        t1.account_chart_id,
                                        t1.description,
                                        t2.account_chart")
                            ->from("coop_account_detail as t1")
                            ->join("coop_account_chart as t2", "t1.account_chart_id = t2.account_chart_id", "left")
                            ->where("t1.account_id = '{$account_id}'")
                            ->get()->result_array();
        $result["details"] = $details;

        //Check if compound.
        $cash_account = $this->db->select("*")->from("coop_account_setting")->where("type = 'cash_chart_id'")->get()->row();
        $cash_chart_id = $cash_account->value;
        $result['is_compound'] = 0;
        foreach($details as $detail) {
            if(($account->journal_type == "PV" || $account->journal_type == "P") && $detail['account_type'] == "credit" && $detail['account_chart_id'] != $cash_chart_id) {
                $result['is_compound'] = 1;
            } else if (($account->journal_type == "RV" || $account->journal_type == "R") && $detail['account_type'] == "credit" && $detail['account_chart_id'] != $cash_chart_id) {
                $result['is_compound'] = 1;
            }
        }

        echo json_encode($result);
        exit;
    }

    public function cancel_account_transaction() {
        $result = array();
        $process_timestamp = date('Y-m-d H:i:s');
        $journal_ref = null;
        $accounts = $this->db->select("t1.budget_year, t1.journal_ref, t2.account_type, t2.account_amount, t2.account_chart_id")
                            ->from("coop_account as t1")
                            ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                            ->where("t1.account_id = '".$_POST["account_id"]."'")
                            ->get()->result_array();

        foreach($accounts as $account) {
            $this->account_transaction->increase_decrease_budget_year($account["account_chart_id"], $account["account_amount"], $account["account_type"], $account['budget_year'], 2);
            $journal_ref = $account['journal_ref'];
        }

        $data_insert = array();
        $data_insert['account_status'] = 2;
        $data_insert['cancel_user_id'] = $_SESSION['USER_ID'];
        $data_insert['cancel_date'] = $process_timestamp;
        $data_insert['updated_at'] = $process_timestamp;
        $this->db->where('account_id', $_POST["account_id"]);
        $this->db->update('coop_account', $data_insert);

        $this->db->query('UPDATE coop_account SET cancel_date = "'.$process_timestamp.'", cancel_user_id = "'.$_SESSION['USER_ID'].'" WHERE journal_ref = "'.$journal_ref.'" AND cancel_date is null');

        echo"<script> document.location.href='".base_url(PROJECTPATH.'/account')."'; </script>";
    }

    public function general_journal_report() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;
        $this->libraries->template('account/general_journal_report',$arr_data);
    }

    public function check_general_journal() {
        $from_date_arr = explode('/',$_GET['from_date']);
        $from_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",$from_date_arr[0]).' 00:00:00.000';
        $thur_date_arr = explode('/',$_GET['thur_date']);
        $thur_date = ($thur_date_arr[2] - 543).'-'.sprintf("%02d",$thur_date_arr[1]).'-'.sprintf("%02d",$thur_date_arr[0]).' 23:59:59.000';

        $account = $this->db->select("account_id")->from("coop_account")
                                ->where("(account_status != 2 OR account_status IS NULL) AND journal_type = 'JV' AND account_datetime BETWEEN '".$from_date."' AND '".$thur_date."'")
                                ->get()->row();
        if(!empty($account)) {
            echo "success";
        }
        exit;
    }

    public function general_journal_preview() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;

        $accounts = $this->get_general_journal($_GET);
        $datas = array();
        $page = 0;
        $first_page_size = 20;
        $page_size = 20;
        $index = 0;
        foreach($accounts as $account) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $account;
            $index++;
        }
        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $this->preview_libraries->template_preview('account/general_journal_preview', $arr_data);
    }

    public function general_journal_excel() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;

        $accounts = $this->get_general_journal($_GET);
        $arr_data["datas"] = $accounts;

        $this->load->view('account/general_journal_excel',$arr_data);
    }

    public function get_general_journal($data) {
        $from_date_arr = explode('/',$data['from_date']);
        $from_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",$from_date_arr[0]).' 00:00:00.000';
        $thur_date_arr = explode('/',$data['thur_date']);
        $thur_date = ($thur_date_arr[2] - 543).'-'.sprintf("%02d",$thur_date_arr[1]).'-'.sprintf("%02d",$thur_date_arr[0]).' 23:59:59.000';
        $where = " AND t1.account_datetime BETWEEN '".$from_date."' AND '".$thur_date."'";

        //Get Account
        $accounts = $this->db->select("t1.journal_ref,
                                        t2.account_chart_id,
                                        t2.account_type,
                                        SUM(t2.account_amount) as amount,
                                        t3.account_chart,
                                        t3.entry_type
                                        ")
                                ->from("coop_account as t1")
                                ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                                ->join("coop_account_chart as t3", "t2.account_chart_id = t3.account_chart_id", "inner")
                                ->where("(t1.account_status != 2 OR t1.account_status IS NULL) AND t1.journal_type = 'JV' ".$where)
                                ->group_by("t1.account_id, t2.account_chart_id, t2.account_type")
                                ->get()->result_array();
        $result = array();
        foreach($accounts as $account) {
            $result[$account["account_chart_id"]]["account_chart_id"] = $account["account_chart_id"];
            $result[$account["account_chart_id"]]["account_chart"] = $account["account_chart"];
            $result[$account["account_chart_id"]][$account["account_type"]] += $account["amount"];
            $result[$account["account_chart_id"]]["journal_ref"] = $account["journal_ref"];
        }

        return $result;
    }

    public function cash_journal_report() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;
        $this->libraries->template('account/cash_journal_report',$arr_data);
    }

    public function check_cash_journal() {
        $from_date_arr = explode('/',$_GET['from_date']);
        $from_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",$from_date_arr[0]).' 00:00:00.000';
        $thur_date_arr = explode('/',$_GET['from_date']);
        $thur_date = ($thur_date_arr[2] - 543).'-'.sprintf("%02d",$thur_date_arr[1]).'-'.sprintf("%02d",$thur_date_arr[0]).' 23:59:59.000';

        $account = $this->db->select("account_id")->from("coop_account")
                                ->where("(account_status != 2 OR account_status IS NULL) AND (journal_type = 'PV' OR journal_type = 'RV') AND account_datetime BETWEEN '".$from_date."' AND '".$thur_date."'")
                                ->get()->row();
        if(!empty($account)) {
            echo "success";
        }
        exit;
    }

    public function cash_journal_preview() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;

        $result = $this->get_cash_journal($_GET);
        $datas = array();
        $page = 0;
        $first_page_size = 20;
        $page_size = 20;
        $index = 0;
        foreach($result["accounts"] as $account) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $account;
            $index++;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $arr_data["cash_debit"] = $result["cash_debit"];
        $arr_data["cash_credit"] = $result["cash_credit"];
        $arr_data["cash_balance"] = $result["cash_balance"];
        $arr_data["diff_cash"] = $result["diff_cash"];

        $this->preview_libraries->template_preview('account/cash_journal_preview', $arr_data);
    }

    public function cash_journal_excel() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;

        $accounts = $this->get_cash_journal($_GET);
        $arr_data["datas"] = $accounts["accounts"];

        $arr_data["cash_debit"] = $accounts["cash_debit"];
        $arr_data["cash_credit"] = $accounts["cash_credit"];
        $arr_data["cash_balance"] = $accounts["cash_balance"];
        $arr_data["diff_cash"] = $accounts["diff_cash"];

        $this->load->view('account/cash_journal_excel',$arr_data);
    }

    public function get_cash_journal($data) {
        $from_date_arr = explode('/',$data['from_date']);
        $from_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",$from_date_arr[0]).' 00:00:00.000';
        $thur_date_arr = explode('/',$data['from_date']);
        $thur_date = ($thur_date_arr[2] - 543).'-'.sprintf("%02d",$thur_date_arr[1]).'-'.sprintf("%02d",$thur_date_arr[0]).' 23:59:59.000';
        $where = " AND t1.account_datetime BETWEEN '".$from_date."' AND '".$thur_date."'";
        $where_prev = " AND t1.account_datetime < '".$from_date."'";
        $year_be = $from_date_arr[2];
        $year = $from_date_arr[2] - 543;

        //GET cash chart_id
        $cash_chart_id = $this->db->select("*")->from("coop_account_setting")->where("type = 'cash_chart_id'")->get()->row()->value;

        //Get Account
        $result = array();
        $accounts = $this->db->select("
                                        t1.account_id,
                                        t1.journal_ref,
                                        t1.journal_type,
                                        t2.account_chart_id,
                                        t2.account_type,
                                        t2.account_amount as amount,
                                        t3.account_chart,
                                        t3.entry_type
                                        ")
                                ->from("coop_account as t1")
                                ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                                ->join("coop_account_chart as t3", "t2.account_chart_id = t3.account_chart_id AND t2.account_chart_id != '{$cash_chart_id}'", "inner")
                                ->where("(t1.account_status != 2 OR t1.account_status IS NULL) AND (t1.journal_type = 'RV' OR t1.journal_type = 'PV') ".$where)
                                ->order_by("t1.journal_type DESC, t2.account_chart_id")
                                ->get()->result_array();
        $result["accounts"] = $accounts;

        $cashs = $this->db->select("t2.account_type, SUM(t2.account_amount) as amount")
                                ->from("coop_account as t1")
                                ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                                ->join("coop_account_chart as t3", "t2.account_chart_id = t3.account_chart_id AND t2.account_chart_id = '{$cash_chart_id}'", "inner")
                                ->where("(t1.account_status != 2 OR t1.account_status IS NULL) AND (t1.journal_type = 'RV' OR t1.journal_type = 'PV') ".$where)
                                ->group_by("t2.account_type")
                                ->get()->result_array();
        $cash_debit = 0;
        $cash_credit = 0;
        foreach($cashs as $cash) {
            if($cash["account_type"] == "debit") $cash_debit += $cash["amount"];
            if($cash["account_type"] == "credit") $cash_credit += $cash["amount"];
        }
        $result["cash_debit"] = $cash_debit;
        $result["cash_credit"] = $cash_credit;

        //Get Cash balance
        $cash_budget_year = $this->db->select("budget_amount")->from("coop_account_budget_year")->where("account_chart_id = '{$cash_chart_id}' AND year = '".($year_be - 1)."'")->order_by("year DESC")->get()->row();
        $balance = $cash_budget_year->budget_amount;
        $result["cash_balance"] = $balance;
        $cash_transactions = $this->db->select("t2.account_type, SUM(t2.account_amount) as amount")
                                        ->from("coop_account as t1")
                                        ->join("coop_account_detail as t2", "t1.account_id = t2.account_id AND t2.account_chart_id = '{$cash_chart_id}'", "inner")
                                        ->where("1=1 ".$where_prev." AND YEAR(t1.account_datetime) = '{$year}' AND (t1.account_status != 2 OR t1.account_status IS NULL)")
                                        ->group_by("t2.account_type")
                                        ->get()->result_array();
        $diff_cash = 0;
        foreach($cash_transactions as $tran) {
            if($tran["account_type"] == "debit") $diff_cash += $tran["amount"];
            if($tran["account_type"] == "credit") $diff_cash -= $tran["amount"];
        }

        $result["diff_cash"] = $diff_cash;

        return $result;
    }

    public function update_account_year_status() {
        $data_insert = array();
        $data_insert['is_close'] = $_POST['status'];
        $this->db->where('account_year', $_POST["year"]);
        $this->db->update('coop_account_year', $data_insert);
        echo"<script> document.location.href='".base_url(PROJECTPATH.'/account')."'; </script>";
    }

    public function check_add_edit_auth() {
        $account_id = $this->input->post("account_id");
        $username = $this->input->post("username");
        $password = $this->input->post("password");
        $result = $this->account_transaction->check_auth_with_account_id($username, $password, 'add_edit_permission_id', $account_id);
        echo json_encode($result);
    }

    public function check_close_year_auth() {
        $username = $this->input->post("username");
        $password = $this->input->post("password");
        $result = $this->account_transaction->check_auth($username, $password, 'close_account_year_permission_id');
        echo json_encode($result);
    }

    public function check_close_daily_auth() {
        $username = $this->input->post("username");
        $password = $this->input->post("password");
        $result = $this->account_transaction->check_auth($username, $password, 'close_daily_account_permission_id');
        echo json_encode($result);
    }

    public function open_daily() {
        $data_insert = array();
        $data_insert['is_close'] = 0;
        $this->db->where('date', $_POST["date"]);
        $this->db->update('coop_account_date', $data_insert);
        echo "success";
    }

    public function close_daily() {
        $process_timestamp = date('Y-m-d H:i:s');
        $account_date = $this->db->select("id")->from("coop_account_date")->where('date = "'.$_POST['date'].'"')->get()->row_array();
        if(!empty($account_date)) {
            $data_insert = array();
            $data_insert['is_close'] = 1;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->where('date', $_POST["date"]);
            $this->db->update('coop_account_date', $data_insert);
        } else {
            $data_insert = array();
            $data_insert['date'] = $_POST['date'];
            $data_insert['is_close'] = 1;
            $data_insert['close_by'] = $_SESSION['USER_ID'];
            $data_insert['close_at'] = $process_timestamp;
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_account_date', $data_insert);
        }
        echo "success";
    }

    public function check_submit_date() {
        $result = array();
        $date = $this->center_function->ConvertToSQLDate($_POST['date']);
        $result['status'] = 'success';
        $result['message'] = '';
        $account_year = $this->db->select("is_close")->from("coop_account_year")->where("start_date <= '".$date."' AND end_date >= '".$date."'")->get()->row_array();
        if(!empty($account_year) && !empty($account_year['is_close'])) {
            $result['status'] = 'error';
            $result['message'] = 'ปีบัญชีนี้ถูกปิดแล้ว';
        } else {
            $account_date = $this->db->select("is_close")->from("coop_account_date")->where("date = '".$date."'")->get()->row_array();
            if(!empty($account_date) && !empty($account_date['is_close'])) {
                $result['status'] = 'error';
                $result['message'] = 'บัญชีรายวันถูกปิดแล้ว';
            }
        }
        echo json_encode($result);
    }

    public function json_get_account_history() {
        $account_id = $_POST['account_id'];
        $data = $this->account_transaction->get_account_history($account_id);
        echo json_encode($data);
    }

    public function report_cash_account() {
        $arr_data = array();
        $arr_data['month_arr'] = $this->month_arr;
        $this->libraries->template('account/report_cash_account',$arr_data);
    }

    public function report_cash_account_pdf() {
        $arr_data = array();

        if(!empty($_POST["from_date"])) {
            $from_date_arr = explode('/',$_POST['from_date']);
            $from_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",$from_date_arr[0]);
            $arr_data["from_date_text"] = ((int)$from_date_arr[0])." ".$this->month_arr[((int)$from_date_arr[1])]." ".$from_date_arr[2];
        }
        if(!empty($_POST["thru_date"])) {
            $thru_date_arr = explode('/',$_POST['thru_date']);
            $thru_date = ($thru_date_arr[2] - 543).'-'.sprintf("%02d",$thru_date_arr[1]).'-'.sprintf("%02d",$thru_date_arr[0]);
            $arr_data["thru_date_text"] = ((int)$thru_date_arr[0])." ".$this->month_arr[((int)$thru_date_arr[1])]." ".$thru_date_arr[2];
        }

        $data = $this->account_transaction->get_cash_account($from_date, $thru_date);
        $arr_data["data"] = $data['data'];
        $arr_data["cash_balance"] = $data['cash_balance'];

        $this->load->view('account/report_cash_account_pdf',$arr_data);
    }

}
