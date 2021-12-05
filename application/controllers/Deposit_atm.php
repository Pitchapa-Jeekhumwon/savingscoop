<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deposit_atm extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        $arr_data = array();
        $where = "";

        $x=0;
        $join_arr = array();
        $join_arr[$x]['table'] = 'coop_maco_account as t2';
        $join_arr[$x]['condition'] = 't1.account_id = t2.account_id';
        $join_arr[$x]['type'] = 'inner';

        $x=1;
        $join_arr[$x]['table'] = 'coop_deposit_type_setting as t3';
        $join_arr[$x]['condition'] = 't2.type_id = t3.type_id';
        $join_arr[$x]['type'] = 'left';

        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select(array('t2.account_name', 't2.mem_id', 't1.*','t3.type_code'));
        $this->paginater_all->main_table('coop_deposit_atm_account as t1');
        $this->paginater_all->where($where);
        $this->paginater_all->page_now(@$_GET["page"]);
        $this->paginater_all->per_page(20);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->order_by('t2.account_status ASC,t2.created DESC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();

        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], @$_GET);
        $i = $row['page_start'];

        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['paging'] = $paging;
        $arr_data['data'] = $row['data'];
        $arr_data['i'] = $i;

        $this->libraries->template('deposit_atm/index',$arr_data);
    }

    public function atm_account(){
        $arr_data = array();

//Set page num if empty
        if (empty($_GET["page"])) $_GET["page"] = 1;

        $account_id = $this->input->get('account_id');
        $arr_data['account_id'] = $account_id;

        $this->db->select(array('min_first_deposit','month_conclude'));
        $this->db->from('coop_deposit_setting');
        $this->db->order_by('deposit_setting_id DESC');
        $row = $this->db->get()->result_array();

        $arr_data['min_first_deposit'] = $row[0]['min_first_deposit'];
        $arr_data['month_conclude'] = $row[0]['month_conclude'];

        $this->db->select(array('t1.*','t3.type_id','t3.type_name','t3.deduct_guarantee_id'));
        $this->db->from('coop_maco_account as t1');
        $this->db->join('coop_deposit_type_setting as t3','t1.type_id = t3.type_id','left');
        $this->db->where("account_id = '".$account_id."'");
        $row = $this->db->get()->result_array();
        $arr_data['row_memberall'] = @$row[0];

        $this->db->select('*');
        $this->db->from('coop_mem_apply');
        $this->db->where("member_id = '".$arr_data['row_memberall']['mem_id']."'");
        $row = $this->db->get()->result_array();
        $arr_data['row_member'] = @$row[0];

        $this->db->select(array('transaction_balance'));
        $this->db->from('coop_account_transaction');
        $this->db->where("account_id = '".$account_id."'");
        $this->db->order_by("transaction_time DESC,transaction_id DESC");
        $row = $this->db->get()->result_array();
        $arr_data['last_transaction'] = @$row[0];


        $this->db->where('account_id', $account_id);
        $arr_data['acc_atm'] = $this->db->get('coop_deposit_atm_account' )->row_array();

        $sql = "SELECT * FROM coop_deposit_atm_detail WHERE account_id = '".$account_id."' ORDER BY entry_date DESC LIMIT 1";
        //echo $sql; exit;
        $arr_data['atm_last'] = $this->db->query($sql)->row_array();

        $show_conclude_checkbox = '0';
        if(@$arr_data['row_memberall']['last_time_print']!=''){
            $diff_last_print = date('Y-m-d',strtotime('- '.$arr_data['month_conclude'].' month'));
            $last_print_date = explode(" ",$arr_data['row_memberall']['last_time_print']);
            $last_print_date = $last_print_date[0];
            $arr_data['last_print_date'] = $last_print_date;
            if($arr_data['row_memberall']['last_time_print'] < $diff_last_print){
                $show_conclude_checkbox = '1';
            }
        }
        $arr_data['show_conclude_checkbox'] = @$show_conclude_checkbox;

        //Count amount of transaction
        $this->db->select('seq_no');
        $this->db->from('coop_deposit_atm_detail');
        $this->db->where("account_id = '".$account_id."'");
        $transactionNum = count($this->db->get()->result_array());

        $maxPage = $transactionNum%26 > 0 ? floor(($transactionNum/26)) + 1 : $transactionNum/26;

        $x=0;
        $join_arr = array();
        $join_arr[$x]['table'] = 'coop_user';
        $join_arr[$x]['condition'] = 'coop_deposit_atm_detail.user_id = coop_user.user_id';
        $join_arr[$x]['type'] = 'left';

        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('coop_deposit_atm_detail.*, coop_user.user_name');
        $this->paginater_all->main_table('coop_deposit_atm_detail');
        $this->paginater_all->where("account_id = '".$account_id."'");

        //Set First Page is last page
        $this->paginater_all->page_now($maxPage - @$_GET["page"] + 1);
        $this->paginater_all->per_page(26);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->order_by('entry_date ASC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();

        $paging = $this->pagination_center->paginating(intval($_GET["page"]), $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

        $i = $row['page_start'];

        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['paging'] = $paging;
        $arr_data['data'] = $row['data'];
        $arr_data['i'] = $i;



        $this->db->select('*');
        $this->db->from('coop_account_transaction');
        $this->db->where("account_id = '".$account_id."'");
        $row = $this->db->get()->result_array();
        $num_arr = array();
        $i = 1;
        foreach($row as $key => $value){
            $num_arr[$value['transaction_id']] = $i++;
        }

        $arr_data['num_arr'] = $num_arr;

        $this->db->select('money_type_name_short');
        $this->db->from('coop_money_type');
        $this->db->where("id='1'");
        $row = $this->db->get()->result_array();
        $arr_data['row_deposit'] = $row[0];

        $this->db->select('money_type_name_short');
        $this->db->from('coop_money_type');
        $this->db->where("id='2'");
        $row = $this->db->get()->result_array();
        $arr_data['row_with'] = $row[0];

        $this->db->select('user_permission_id');
        $this->db->from('coop_user_permission');
        $this->db->where("user_id = '".$_SESSION['USER_ID']."' AND menu_id = '187'");
        $row = $this->db->get()->result_array();
        if($row[0]['user_permission_id']==''){
            $arr_data['cancel_transaction_display'] = "display:none;";
        }else{
            $arr_data['cancel_transaction_display'] = "";
        }

        $this->db->select(array('type_fee','pay_interest','num_month_before','percent_depositor','permission_type', 'staus_close_principal','is_withdrawal_specify'));
        $this->db->from('coop_deposit_type_setting_detail');
        $this->db->where("type_id = '".$arr_data['row_memberall']['type_id']."'");
        $this->db->order_by("type_detail_id DESC");
        $this->db->limit(1);
        $row_setting_detail = $this->db->get()->result_array();
        $row_setting_detail = $row_setting_detail[0];
        $arr_data['type_fee'] = $row_setting_detail['type_fee'];
        $arr_data['permission_type'] = $row_setting_detail['permission_type'];
        $arr_data['staus_close_principal'] = $row_setting_detail['staus_close_principal'];
        $arr_data['is_withdrawal_specify'] = $row_setting_detail['is_withdrawal_specify'];
        if($row_setting_detail['type_fee'] == '3'){
            if($row_setting_detail['pay_interest'] == '2'){ //ประเภทเงินฝากที่คิดดอกเบี้ย ตามวันที่ฝาก
                $arr_data['fix_withdrawal_amount'] = 0;
                $this->db->select(array('deposit_interest_balance'));
                $this->db->from('coop_account_transaction');
                $this->db->where("account_id = '".$account_id."' AND interest_period = '".$row_setting_detail['num_month_before']."' AND fixed_deposit_status = '0'");
                $row = $this->db->get()->result_array();
                if(!empty($row)){
                    foreach($row as $key => $value){
                        $arr_data['fix_withdrawal_amount'] += $value['deposit_interest_balance'];
                        $arr_data['fix_withdrawal_status'] = 'success';
                    }
                }else{
                    $this->db->select(array('deposit_balance','transaction_time'));
                    $this->db->from('coop_account_transaction');
                    $this->db->where("account_id = '".$account_id."' AND fixed_deposit_status = '0'");
                    $row2 = $this->db->get()->result_array();
                    if(!empty($row2)){
                        foreach($row2 as $key2 => $value2){
                            $interest_rate = $row_setting_detail['percent_depositor'];
                            $date_start = date('Y-m-d',strtotime($value2['transaction_time']));
                            $date_end = date('Y-m-d');
                            $diff = @date_diff(date_create($date_start),date_create($date_end));
                            $date_count = @$diff->format("%a");
                            $date_count = $date_count+1;

                            $interest = ((($value2['deposit_balance']*@$interest_rate)/100)*$date_count)/365;

                            $arr_data['fix_withdrawal_amount'] += ($value2['deposit_balance']+$interest);
                        }
                        $arr_data['fix_withdrawal_status'] = 'fail';
                    }
                }
            }else{
                $create_date = date('Y-m-d',strtotime($arr_data['row_memberall']['created']));
                $end_date = date('Y-m-d',strtotime('+ '.$row_setting_detail['num_month_before'].' month',strtotime($create_date)));
                $date_interest = date('Y-m-d');
                if($date_interest < $end_date){
                    $this->db->select(array('transaction_balance','transaction_no_in_balance'));
                    $this->db->from('coop_account_transaction');
                    $this->db->where("account_id = '".$account_id."'");
                    $this->db->order_by('transaction_time DESC, transaction_id DESC');
                    $this->db->limit(1);
                    $row_transaction = $this->db->get()->result_array();

                    $interest_rate = $row_setting_detail['percent_depositor'];
                    $date_start = $create_date;
                    $date_end = $date_interest;
                    $diff = @date_diff(date_create($date_start),date_create($date_end));
                    $date_count = @$diff->format("%a");
                    $date_count = $date_count+1;

                    $interest = ((($row_transaction[0]['transaction_no_in_balance']*@$interest_rate)/100)*$date_count)/365;

                    $arr_data['fix_withdrawal_amount'] = ($row_transaction[0]['transaction_no_in_balance']+$interest);
                    $arr_data['fix_withdrawal_status'] = 'fail';
                }
            }
        }

        $this->libraries->template("deposit_atm/atm_account", $arr_data);
    }

    public function add_account_atm(){
        $arr_data = array();
        $data = $this->input->post();
        if($data['account_id']!=''){
            $account_id = @$data['account_id'] ;
            $arr_data['account_id'] = $account_id;

            $this->db->select(array('t1.account_name', 't1.mem_id', 't2.*', 't3.type_name', "CONCAT('1|||', t2.account_id) AS id_transfer, t4.user_name"));
            $this->db->from('coop_maco_account as t1');
            $this->db->join('coop_deposit_atm_account as t2', 't1.account_id=t2.account_id', 'inner');
            $this->db->join('coop_deposit_type_setting as t3','t1.type_id = t3.type_id','inner');
            $this->db->join('coop_user as t4','t4.user_id = t1.sequester_by','left');
            $this->db->where("t1.account_id = '".$account_id."'");
            $row = $this->db->get()->result_array();
            $arr_data['auto_account_id'] = '';
            $btitle = "แก้ไขบัญชีเงินฝาก";
            $arr_data['row'] = $row[0];
        }else{
            $btitle = "เพิ่มบัญชีเงินฝาก";
            $arr_data['row'] = array();
            $arr_data['account_id'] = '';
        }
        $this->db->select(array('t1.type_id','t1.type_code','t1.type_name','t1.unique_account'));
        $this->db->from('coop_deposit_type_setting as t1');
        $row = $this->db->get()->result_array();
        $arr_data['type_id'] = $row;

        $arr_data['account_list_transfer'] = array();
        $arr_data['get_dividend_acc_num'] = array();
        if($data['member_id']!=""){
            $this->db->select(array("CONCAT('1|', '', '|', '', '|', account_id) AS id", "account_id ","CONCAT(account_id, '  ',account_name) AS text"));
            $this->db->from("coop_maco_account");
            $this->db->where("mem_id = '{$data['member_id']}' ");
            $maco_account = $this->db->get()->result_array();

            if($maco_account){
                foreach ($maco_account as $key => $value) {
                    array_push($arr_data['account_list_transfer'], $value);
                }
            }
            //echo "<pre>"; print_r($arr_data['account_list_transfer']); exit;
            $this->db->select("id,member_id,dividend_acc_num");
            $this->db->from('coop_mem_bank_account');
            $this->db->where("member_id = '{$data['member_id']}' ");
            $row2 = $this->db->get()->result_array();
            if($row2){
                foreach ($row2 as $key => $value2) {
                    array_push($arr_data['get_dividend_acc_num'], $value2);
                }
            }
        }

        $arr_data['btitle'] = $btitle;
        $this->load->view("deposit_atm/add_account_atm", $arr_data);
    }

    function get_member(){
        $where = '';
        if(@$_POST['member_id'] != ''){
            $member_id = isset($_POST['member_id']) ? trim($_POST['member_id']) : "";
            $member_id = sprintf("%06d",$member_id);
            $where .= " AND member_id = '".$member_id."' ";
        }
        if(@$_POST['id'] != ''){
            $where .= " AND coop_mem_apply.id = '".$_POST['id']."' ";
        }

        $this->db->select(array(
            'coop_mem_apply.*',
            'coop_mem_group.mem_group_name'
        ));
        $this->db->from('coop_mem_apply');
        $this->db->join('coop_mem_group','coop_mem_apply.mem_group_id = coop_mem_group.mem_group_id','left');
        $this->db->where("
			1=1 ".$where."
		");
        $row = $this->db->get()->result_array();
        $row = @$row[0];

        $data = array();
        $data = $row;
        $data['member_id'] = $row['member_id'];
        $data['member_name'] = $row['firstname_th']." ".$row['lastname_th'];
        $data['member_group_name'] = $row['mem_group_name'];
        $data['account_list_transfer'] = array();
        $this->db->select(array("CONCAT('1|', '', '|', '', '|', account_id) AS id", "CONCAT(account_id, '  ',account_name) AS text"));
        $this->db->from("coop_maco_account");
        $this->db->where("mem_id = '{$data['member_id']}' AND account_id NOT IN (SELECT account_id FROM coop_deposit_atm_account WHERE mem_id='{$data['member_id']}') ");
        $maco_account = $this->db->get()->result_array();

        if($maco_account){
            foreach ($maco_account as $key => $value) {
                array_push($data['account_list_transfer'], $value);
            }
        }

        if(@$_POST['for_loan']=='1'){

            $this->db->select(array(
                'guarantee_count'
            ));
            $this->db->from('coop_guarantee_setting');
            $this->db->where("
				salary_start <= '".$row['salary']."' AND salary_end >= '".$row['salary']."'
			");
            $this->db->limit(1);
            $row_term_of_loan = $this->db->get()->result_array();
            $row_term_of_loan = @$row_term_of_loan[0];
            $guarantee_count = $row_term_of_loan['guarantee_count'];
            $this->db->select(array(
                '*'
            ));
            $this->db->from('coop_loan_guarantee_person as t1');
            $this->db->join('coop_loan as t2','t1.loan_id = t2.id ','inner');
            $this->db->where("
				t1.guarantee_person_id = '".$row['member_id']."'
				AND t2.loan_status IN('1','2') AND t2.loan_amount_balance > 0
			");
            $rs_count_guarantee = $this->db->get()->result_array();
            $i=0;
            foreach($rs_count_guarantee as $key => $row_count_guarantee){
                $i++;
            }
            if($i>=$guarantee_count && $guarantee_count > 0){
                echo 'over_guarantee';
                exit;
            }else{
                echo $i;
                exit;
            }
        }
        $this->db->select("id,member_id,dividend_acc_num");
        $this->db->from('coop_mem_bank_account');
        $this->db->where("member_id = '{$data['member_id']}' ");
        $row2 = $this->db->get()->result_array();
        $data['get_dividend_acc_num'] = $row2;
        echo json_encode($data);
        exit;
    }


    public function check_account_atm(){
        header("content-type: application/json; charset=utf-8");

        $data['dupplicate_account_no'] = 'success';
        $data['atm_number'] = 'success';
        $data['unique_account'] = 'success';
        $data['error'] = '';
        echo json_encode($data);
        exit;
    }

    public function save_account_atm(){
        $data = array();

    //    $post = $this->input->post();
    //    echo "<pre>"; print_r($post); exit;

        $action_type = $this->input->post('action_type');

        $opn_date = $this->input->post("opn_date");
        $opn_date = str_replace("/", "-", $opn_date);
        $create_date = date(" Y-m-d H:i:s", strtotime($opn_date." ".date("H:i:s")." -543 year"));

        //get account id
        if($this->input->post('action_type') == "edit") {
            $tmp_account = $this->input->post("dummy_acc_id");
        }else{
            $tmp_account = $this->input->post("acc_id");
        }
        $account = explode("|", $tmp_account);
        $account_id = $account[3];

        $arr_approve_amount = explode(",", $this->input->post("approve_amount"));
        $approve_amount = join("", $arr_approve_amount);
        $arr_min_first_deposit_atm = explode(",", $this->input->post("min_first_deposit_atm"));
        $min_first_deposit_atm = join("", $arr_min_first_deposit_atm);

        if($this->input->post('action_type') == "add") {
            //insert atm account
            $data['account_id'] = $account_id;
            $data['bank_account_on'] = $this->input->post("bank_account");
            $data['approve_amount'] = $approve_amount;
            $data['member_id'] = $this->input->post("mem_id");
            $data['approve_date'] = $create_date;
            $data['create_date'] = $create_date;
            $data['account_status'] = 0;
            $data['sequester_status'] = $this->input->post("sequester_status");
            $data['sequester_amount'] = str_replace( ',','', $this->input->post("sequester_amount"));
            $data['sequester_status_atm'] = $this->input->post("sequester_status_atm");
            $data['loan_atm_activate'] = $this->input->post("loan_atm_activate");
            $data['min_first_deposit_atm'] = $min_first_deposit_atm;
            $this->db->insert('coop_deposit_atm_account', $data);

            $date = empty($create_date) ? date("Y-m-d H:i:s") : $create_date;

            $data = array();
            $data['account_id'] = $account_id;
            $data['seq_no'] = 1;
            $data['principal_amount'] = $min_first_deposit_atm;
            $data['fee_amount'] = 0;
            $data['operate_date'] = $date;
            $data['item_type'] = 'NEW';
            $data['approve_amount'] = $approve_amount;
            $data['banalce'] = $min_first_deposit_atm;
            $data['user_id'] = $_SESSION['user_id'];
            $data['entry_date'] = date("Y-m-d H:i:s");
            $data['last_access_user'] = $_SESSION['user_id'];
            $data['last_access_branch'] = '01';
            $this->db->insert('coop_deposit_atm_detail', $data);
        }

        if($action_type == "edit" && $account_id != ""){

            $data['sequester_status'] = $this->input->post("sequester_status");
            $data['sequester_amount'] = str_replace(',','',$this->input->post("sequester_amount"));
            $data['sequester_status_atm'] = $this->input->post("sequester_status_atm");
            $data['sequester_remark'] = $this->input->post("remark");
            $data['sequester_time'] = date('Y-m-d H:i:s');
            $data['sequester_by'] = $_SESSION['user_id'];
			$data['loan_atm_activate'] = $this->input->post("loan_atm_activate");

            $this->db->where('account_id', $account_id);
            $this->db->update('coop_deposit_atm_account', $data);
        }
		
		//@start บันทึกเลขบัญชีธาคารฝั่งเงินกู้ ATM
		$row_loan_atm = $this->db->select('*')->from("coop_loan_atm")
					->where(array("loan_atm_status" => "1", "member_id" => $this->input->post("mem_id")))
					->order_by("approve_date DESC")->limit(1)->get()->row_array();
		if(!empty($row_loan_atm)){
			$loan_atm_id = $row_loan_atm['loan_atm_id'];
			$data = array();
			if($this->input->post("loan_atm_activate") == '1'){
				$data['activate_status'] = 0;
			}else {
				$data['activate_status'] = 1;
			}
			$data['account_id'] = $this->input->post("bank_account");
			$this->db->where('loan_atm_id', $loan_atm_id);
			$this->db->update('coop_loan_atm', $data);
		}
		//@end บันทึกเลขบัญชีธาคารฝั่งเงินกู้ ATM
		
        $this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
        header( "location: ".base_url("deposit_atm/index") );
        exit;

    }

    public function save_chg_approve(){
        if($this->input->post()){
            $account_id = $this->input->post("account_id");
            $approve_amt = $this->input->post("approve_amount");
            $balanc_amt = $this->input->post("balance");
            $date = $this->input->post('date');
            //update deposit atm account approve amount
            $data = array();
            $result = array();

            $data['approve_amount'] = $approve_amt;
            $this->db->where('account_id', $account_id);
            $this->db->update('coop_deposit_atm_account', $data);
            if($this->db->affected_rows() > 0){
                $result['update_approve_amount'] = "success";
            }else{
                $result['update_approve_amount'] = "error";
            }

            $sql = "SELECT * FROM coop_deposit_atm_detail WHERE account_id = '".$account_id."' ORDER BY entry_date DESC LIMIT 1";
            $atm_last = $this->db->query($sql)->row_array();

            if($atm_last['banalce'] <> $balanc_amt ){

                $date = empty($date) ? date("Y-m-d H:i:s") : $date;

                $data = array();
                $data['account_id'] = $account_id;
                $data['seq_no'] = $atm_last['seq_no'] + 1;
                $data['principal_amount'] = $balanc_amt;
                $data['fee_amount'] = 0;
                $data['operate_date'] = $date;
                $data['item_type'] = 'CHG';
                $data['approve_amount'] = $approve_amt;
                $data['banalce'] = $balanc_amt;
                $data['user_id'] = $_SESSION['user_id'];
                $data['entry_date'] = date("Y-m-d H:i:s");
                $data['last_access_user'] = $_SESSION['user_id'];
                $data['last_access_branch'] = '01';

                $this->db->insert('coop_deposit_atm_detail', $data);
                if($this->db->affected_rows()){
                    $result['update_atm_status'] = "success";
                }else{
                    $result['update_atm_status'] = "error";
                }
            }
        }
        $result['status'] = 'done';
        header("content-type: application/json; charset=utf-8;");
        echo json_encode($result);
        exit;
    }

    public function save_change_balance(){
        $data = array();
        $result = array();
        if($this->input->post()) {

            $account_id = $this->input->post("account_id");
            $balanc_amt = $this->input->post("balance");
            $date = $this->input->post('date');

            $approve_amt = $this->db->select("approve_amount")->from("coop_deposit_atm_account")->where(array('account_id' => $account_id))->get()->row_array()['approve_amount'];

            $max_seq = $this->db->select("seq_no")->from("coop_deposit_atm_detail")->where('account_id', $account_id)->order_by('seq_no', 'desc')->limit(1)->get()->row_array()['seq_no'];

            if($approve_amt) {
                $date = empty($date) ? date("Y-m-d H:i:s") : $date;

                $data['account_id'] = $account_id;
                $data['seq_no'] = $max_seq+1;
                $data['principal_amount'] = $balanc_amt;
                $data['fee_amount'] = 0;
                $data['operate_date'] = $date;
                $data['item_type'] = 'MOD';
                $data['approve_amount'] = $approve_amt;
                $data['banalce'] = $balanc_amt;
                $data['user_id'] = $_SESSION['user_id'];
                $data['entry_date'] = date("Y-m-d H:i:s");
                $data['last_access_user'] = $_SESSION['user_id'];
                $data['last_access_branch'] = '01';

                $this->db->insert('coop_deposit_atm_detail', $data);
                if ($this->db->affected_rows()) {
                    $result['update_atm_transaction'] = "success";
                } else {
                    $result['update_atm_transaction'] = "error";
                }
            }else{
                $result['update_atm_transaction'] = "error";
            }
        }
        $result['status'] = 'done';
        header("content-type: application/json; charset=utf-8;");
        echo json_encode($result);
        exit;
    }

    public function download(){
        $arr_data = array();
        $this->libraries->template("deposit_atm/download", $arr_data);
    }

    public function display(){
		$arr_data = array();
        $str_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        $data = $this->atm_deposit->fetch($str_date);
        $txt = $this->atm_deposit->header($str_date);
        $i = 0; $balance = 0; $approve = 0; 
        foreach ($data as $key => $val){
            $txt .= $this->atm_deposit->detail($val);
            $balance += $val['banalce'];
            $approve += $val['approve_amount'];
            $i++;
        }

		$txt .= $this->atm_deposit->footer($i, $balance, $approve);
		$txt = iconv('UTF-8', 'TIS-620', $txt);
        $this->atm_deposit->create_file($txt,$str_date);
    }

    public function update_transaction(){
        $arr_data = array();

        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');

        $where =" YEAR(`file_date`)='{$year}' AND MONTH(`file_date`)='{$month}' ";
        $data = $this->db->select('*')->from('coop_deposit_atm_file_upload')->where($where)->get()->result_array();
        $list = array();
        foreach ($data as $key => $item){
            $list[date('Y-m-d', strtotime($item['file_date']))] = $item;
        }
        $arr_data['list'] = $list;

        $this->libraries->template("deposit_atm/update_transaction", $arr_data);
    }

    public function upload_file_withdraw(){
        $this->load->model('text_file');
        header('content-type: application/json; charset: utf8;');
        $dir_path = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/withdraw/";
		$full_path = $dir_path.$_FILES['file']['name'];
        $file_name = $_FILES['file']['name'];

        $data = ["status" => 0 , "msg" => "no response"];
		//echo '<pre>'; print_r($data); echo '</pre>';
		//ตั้งค่าข้อมูลการธนาคาร Offline
		$setting_atm_file = $this->atm_deposit_file->get_setting_loan_atm_file();
		$file_extension = $setting_atm_file['file_extension'];
		$file_type = $setting_atm_file['file_type'];
		$bank_atm = $setting_atm_file['bank_atm'];

		if($_FILES['file']['type'] !== $file_type){
			$data['msg'] = "กรุณาอัพโหลดไฟล์ที่เป็นนานสกุล ".$file_extension." เท่านั้น" ;
            $data['extension'] = $_FILES['file']['type'];
            echo json_encode($data);
            exit;
        }

        if(!file_exists($dir_path)){
            mkdir($dir_path, 0777);
        }

        if($this->file_name_exists($_FILES['file']['name'])){
            $data['msg'] = "เนื่องจากวันที่ของข้อมูลซ้ำกับข้อมูลในระบบ" ;
            echo json_encode($data);
            exit;
        }

        move_uploaded_file($_FILES['file']['tmp_name'], $full_path);
		
		if($this->file_previous_exists($_FILES['file']['name'])){
            $data['msg'] = "เนื่องจากวันที่ของข้อมูลย้อนหลังจากไฟล์ในระบบกรุณาอัพโหลดไฟล์ใหม่";
            echo json_encode($data);
            exit;
        }
        
		$file_date = $this->text_file->read_date_file_withdraw($file_name);

		if($this->atm_deposit_file->check_file_exists($file_name)){

			$data_insert = array();
			$data_insert['file_name'] = $file_name;
			$data_insert['file_path'] = $full_path;
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['file_date'] = $file_date;
			$data_insert['active_status'] = 0;
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$data_insert['status'] = 0;

			$this->db->update('coop_deposit_atm_file_upload', $data_insert);
			$data['status'] = 1;
			$data['msg'] = "ปรับปรุงข้อมูลไฟล์ ".$file_name." วันที่ ".$this->center_function->ConvertToThaiDate($file_date, 2, 1);
			echo json_encode($data);
			exit;

		}else {

			$data_insert = array();
			$data_insert['file_name'] = $file_name;
			$data_insert['file_path'] = $full_path;
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['file_date'] = $file_date;
			$data_insert['active_status'] = 0;
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$data_insert['status'] = 0;

			$this->db->insert('coop_deposit_atm_file_upload', $data_insert);

			$data['status'] = 1;
			$data['msg'] = "อัพโหลดไฟล์สำเร็จแล้ว";
			echo json_encode($data);
			exit;
		}
    }

    private function file_previous_exists($file){
        $this->load->model('text_file');
        $file_date = $this->text_file->read_date_file_withdraw($file);
        $date = date('Y-m-d', strtotime(str_replace('/','-', $file_date)));
        $year = date("Y", strtotime($date));
        $month = date("m", strtotime($date));
        $day = date("d", strtotime($date));
        $str = " YEAR(file_date) = '{$year}' AND MONTH(file_date) = '{$month}' AND  DAY(file_date) >= '{$day}' ";
        $res = $this->db->select("*")->from("coop_deposit_atm_file_upload")->where($str)->get()->result_array();
        return !!(sizeof($res) > 0);
    }

    private function file_name_exists($file_name){
        $this->db->select('file_name')->from('coop_deposit_atm_file_upload')->where("file_name = '{$file_name}'");
        return $this->db->get()->num_rows() ? true : false;
    }

    public function process_withdraw_file(){
        if(isset($_POST)){
            $this->db->select('*')->from("coop_deposit_atm_file_upload")->where(array("status" => "0", "active_status" => "0", "id" => $_POST['id']));
            $res = $this->db->get()->row();

            if(empty($res->file_path)){
                $data = (array) $res;
                header('content-type: application/json; charset: utf8');
                $result = array('result' => 0, 'msg' => 'file empty', 'status' => 'error', 'data' => $data );
                echo json_encode($result);
                exit;
            }else{
                header('content-type: application/json; charset: utf8');
                if(!file_exists($res->file_path)) {
                    $result = array('result' => 0, 'msg' => 'file not found', 'status' => 'error');
                }else{
                    $file = $this->atm_deposit_file->read_withdraw_file($res->file_path);
                    $rs = $this->atm_deposit_file->save_deposit_atm_receive_file($res->id, $file, $res->file_name);
                    $result = array('result' => $rs, 'msg' => 'success');
                }
                echo json_encode($result);
                exit;
            }
        }
    }

    public function test_datetime(){
        $this->load->model('text_file');
        echo $this->text_file->read_date_file_withdraw('160111_coa031_withdraw10.txt');
    }
	
	public function receive_file_transaction_data(){
	    header('content-type: application/json; charset: utf8;');
	    if(isset($_POST)){
			$this->atm_deposit_file->add_account_transaction($_POST['id']);

			$data_update['status'] = 2;
	        $data_update['submit_date'] = date('Y-m-d H:i:s');
	        $this->db->where(array('id'=> $_POST['id']));
	        $this->db->update('coop_deposit_atm_file_upload', $data_update);
			
	        echo json_encode(array('status' => 1, 'msg' => 'success'));
	        exit;
        }
        echo json_encode(array('status' => 0, 'msg' => 'error'));
        exit;
    }
	
	public function delete_file_update(){
	    $dir_path = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/withdraw/";
	    header('content-type: application/json; charset: utf8');
		if(isset($_POST)) {
            $res = $this->db->select(array('file_name', 'active_status', 'status'))->from("coop_deposit_atm_file_upload")->where(array("id" => $_POST['id']))->get()->row();
            if($res->status == '2' || $res->active_status == '1'){
                echo json_encode(array('status' => 0, 'msg' => 'unmount file.'));
                exit;
            }else{
				if(!unlink($dir_path.$res->file_name)) {
                    echo json_encode(array('status' => 0, 'msg' => 'unmount file.'));
					exit;
                }else{
                    $this->db->where(array('id' => $_POST['id']));
                    $this->db->delete('coop_deposit_atm_file_upload');
                    echo json_encode(array('status' => 1, 'msg' => 'removed file.'));
					exit;
                }
            }
        }
	    echo json_encode(array('status' => 0, 'msg' => 'error'));
	    exit;
    }
	
	public function check_receive_file(){
	    $get = $this->input->get();
	    if(isset($get['id'])) {
            $data = $this->atm_deposit_file->getDataReceiveById($get['id']);
            $html = "";
            $num = 0;
            foreach ($data as $key => $rows){
                $html .= "<tr>";
                $html .= "<td class=\"text-center\">".(++$num)."</td>";
                foreach ($rows as $key_2=>$item){
					if($key_2 == 'transaction_amount'){
						$html .= "<td class=\"text-right\">".number_format($item,2).'</td>';
					}else if($key_2 == 'transaction_date'){
						$html .= "<td class=\"text-center\">".$this->center_function->ConvertToThaiDate($item,1,1,0).'</td>';
					}else if($key_2 == 'member_id'){
						$html .= "<td class=\"text-center\">".sprintf("%06d", $item).'</td>';
					}else{
						$html .= "<td class=\"text-center\">".$item.'</td>';
					}
                }
                $html .= "</tr>";
            }
            echo $html;
            exit;
        }
	    echo "";
	    exit;

    }
	
	public function check_loan_atm(){
	    $member_id = $this->input->post("member_id");
		$res = $this->db->select('*')->from("coop_loan_atm")
					->where(array("loan_atm_status" => "1", "member_id" => $member_id))
					->get()->row();
		if(!empty($res)){
			echo json_encode(array('status' => 1, 'msg' => 'success'));
			exit;
		}else{
			echo json_encode(array('status' => 0, 'msg' => 'error'));
			exit;
		}
    }
	
}
