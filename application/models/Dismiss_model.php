<?php


class Dismiss_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Contract_modal", "Contract");
        self::setInterestRound();
    }

    public function dismiss(){

    }

    public function findKeepMonth(){

    }


    public function getProfileList($year = ""){

        if(isset($year) && $year != ""){
            $this->db->where(array("profile_year" => $year));
        }
        return $this->db->get("coop_finance_month_profile")->result_array();
    }

    public function keep_month(){

    }

    public function getKeepingList($member_id, $month, $year){
        $this->db->select(array(
            "t1.profile_id",
            "t1.member_id",
            "t1.deduct_code",
            "IF(`t1`.`deduct_code`= 'SHARE', `t3`.`deduct_detail`,IF( `t1`.`deduct_code` = 'LOAN', `t4`.`contract_number`, `t5`.`contract_number`)) as deduct_detail",
            "t1.deduct_id",
            "IF(`t1`.`loan_id` <> '', `t1`.`loan_id`, IF(`t1`.`loan_atm_id` <> '', `t1`.`loan_atm_id`, IF(`t1`.`deposit_account_id` <> '', `t1`.`deposit_account_id`, `t1`.`member_id`))) as ref_id",
            "sum(if(`t1`.`pay_type` = 'principal', t1.pay_amount, 0)) as `principal`",
            "sum(if(`t1`.`pay_type` = 'interest', t1.pay_amount, 0)) as `interest`",
            "sum(t1.run_status) as `run_status`"
        ));
        $this->db->from("coop_finance_month_detail as t1");
        $this->db->join("coop_finance_month_profile as t2", "t1.profile_id=t2.profile_id", "inner");
        $this->db->join("coop_deduct as t3", "t1.deduct_id=t3.deduct_id", "inner");
        $this->db->join("coop_loan as t4", "t1.loan_id=t4.id", "left");
        $this->db->join("coop_loan_atm as t5", "t1.loan_atm_id=t5.loan_atm_id", "left");
        $this->db->where(array("t1.member_id" => $member_id, "t2.profile_month" => $month, "t2.profile_year" => $year));
        $this->db->group_by("t1.loan_id, t1.loan_atm_id");
        return $this->db->get()->result_array();
    }

    public function getDescription($deduct_code, $ref_id, $deduct_id){
        $description = array_column(self::findDetail(), 'deduct_detail', 'deduct_id');
        if($deduct_code == "LOAN"){
            return $description[$deduct_id]." ";
        }else if($deduct_code == "ATM"){
            return $description[$deduct_id]." ".$this->contract->findContract();
        }else if($deduct_code == "DEPOSIT"){
            return $description[$deduct_id]." ".$ref_id;
        }else{
            return $description[$deduct_id];
        }
    }

    public function getKeepItemList($profile_id, $member_id, $deduct_code, $ref_id){

        $arr = array("t1.member_id" => $member_id,
            "t1.profile_id" => $profile_id,
            "t1.deduct_code" => $deduct_code
        );

        if($deduct_code == "LOAN"){
            $arr["t1.loan_id"] = $ref_id;
        }else if($deduct_code == "DEPOSIT"){
            $arr["t1.deposit_account_id"] = $ref_id;
        }else if($deduct_code == "ATM"){
            $arr["t1.loan_atm_id"] = $ref_id;
        }

        return $this->db->select(array(
            "t1.profile_id",
            "t1.member_id",
            "t1.deduct_code",
            "IF(`t1`.`deduct_code`= 'SHARE', `t3`.`deduct_detail`,IF( `t1`.`deduct_code` = 'LOAN', concat(`t3`.`deduct_detail`, ' ',`t4`.`contract_number`), concat(`t3`.`deduct_detail`, ' ',`t5`.`contract_number`))) as deduct_detail",
            "t1.deduct_id",
            "IF(`t1`.`loan_id` <> '', `t1`.`loan_id`, IF(`t1`.`loan_atm_id` <> '', `t1`.`loan_atm_id`, IF(`t1`.`deposit_account_id` <> '', `t1`.`deposit_account_id`, `t1`.`member_id`))) as ref_id",
            "t1.real_pay_amount",
            "t1.run_id",
            "`t1`.`run_status`"
        ))
        ->from("coop_finance_month_detail as t1")
        ->join("coop_finance_month_profile as t2", "t1.profile_id=t2.profile_id", "inner")
        ->join("coop_deduct as t3", "t1.deduct_id=t3.deduct_id", "inner")
        ->join("coop_loan as t4", "t1.loan_id=t4.id", "left")
        ->join("coop_loan_atm as t5", "t1.loan_atm_id=t5.loan_atm_id", "left")
        ->where($arr)
        ->get()->result_array();
    }

    public function getKeepItemAllList($profile_id, $member_id){

        $arr = array("t1.member_id" => $member_id,
            "t1.profile_id" => $profile_id,
            "t1.run_status" => 0,
        );

        return $this->db->select(array(
            "t1.profile_id",
            "t1.member_id",
            "t1.deduct_code",
            "IF(`t1`.`deduct_code`= 'SHARE', `t3`.`deduct_detail`,IF( `t1`.`deduct_code` = 'LOAN', concat(`t3`.`deduct_detail`, ' ',`t4`.`contract_number`), concat(`t3`.`deduct_detail`, ' ',`t5`.`contract_number`))) as deduct_detail",
            "t1.deduct_id",
            "IF(`t1`.`loan_id` <> '', `t1`.`loan_id`, IF(`t1`.`loan_atm_id` <> '', `t1`.`loan_atm_id`, IF(`t1`.`deposit_account_id` <> '', `t1`.`deposit_account_id`, `t1`.`member_id`))) as ref_id",
            "t1.real_pay_amount",
            "t1.run_id",
            "`t1`.`run_status`"
        ))
            ->from("coop_finance_month_detail as t1")
            ->join("coop_finance_month_profile as t2", "t1.profile_id=t2.profile_id", "inner")
            ->join("coop_deduct as t3", "t1.deduct_id=t3.deduct_id", "inner")
            ->join("coop_loan as t4", "t1.loan_id=t4.id", "left")
            ->join("coop_loan_atm as t5", "t1.loan_atm_id=t5.loan_atm_id", "left")
            ->where($arr)
            ->order_by("t1.deduct_code DESC, t1.loan_id ASC, t1.loan_atm_id ASC, IF(t1.pay_type='principal', 1, 2) ASC")
            ->get()->result_array();
    }



    public function findDetail(){
        return $this->db->get("coop_deduct")->result_array();
    }

    public function TemplateList($no, $data){
        return '<tr>
        <td class="text-center">
        <input class="larger" type="checkbox" checked="checked" value="'.$data->run_id.'"/>
    </td>
    <td class="text-left" >'.$data->deduct_detail.'</td>
    <td class="text-right" >'.number_format($data->real_pay_amount, 2).'</td>
</tr>';
    }


    public function TemplateFooter($total){
        return '<tr>
                    <td></td>
                    <td class="text-center">รวมทั้งหมด</td>
                    <td class="text-right">
                        <span class="text-underline-double">'.$total.'</span>
                    </td>
                    
                </tr>';
    }


    //TODO ลงใบเสร็จ
    function add_receipt($data){

        $process_date = $data['process_date'];
        $month = $data["month"];
        $year = $data["year"];
        $member_id  = $_sub_query_where["t1.member_id"]  = $data["member_id"];
        $profile_id = $_sub_query_where["t1.profile_id"] = $data["profile_id"];

        if(!empty($data["ref_id"]) && !empty($data["deduct_code"])) {
            $deduct_code = $_sub_query_where["t1.deduct_code"] = $data["deduct_code"];
            $ref_id = $data["ref_id"];
            if ($deduct_code == "LOAN" || $deduct_code == 'GUARANTEE') {
                $_sub_query_where["t1.loan_id"] = $ref_id;
            } else if ($deduct_code == "DEPOSIT") {
                $_sub_query_where["t1.deposit_account_id"] = $ref_id;
            } else if ($deduct_code == "ATM") {
                $_sub_query_where["t1.loan_atm_id"] = $ref_id;
            } else if ($deduct_code == "CREMATION") {
                $_sub_query_where["t1.cremation_type_id"] = $ref_id;
            } else {
                $_sub_query_where["t1.member_id"] = $ref_id;
            }
        }

        $_where_or_in = "";
        $where_or_in = array();
        if(isset($data['deduct_list']) && sizeof($data['deduct_list'])){
           $where_or_in[] = "t1.deduct_code IN (".implode(", ",array_map(function($v){ return sprintf("'%s'", $v); }, $data['deduct_list'])).")";
        }
        if(isset($data['loan_list']) && sizeof($data['loan_list'])){
            $where_or_in[] = "t1.loan_id IN (". implode(", ", $data['loan_list']).")";
        }
        if(isset($data['atm_list']) && sizeof($data['atm_list'])){
            $where_or_in[] = "t1.loan_atm_id IN (". implode(", ",$data['atm_list']).")";
        }
        if(isset($data['deposit_list']) && sizeof($data['deposit_list'])){
            $where_or_in[] = "t1.deposit_account_id IN (". implode(", ",$data['deposit_list']).")";
        }
        if(sizeof($where_or_in)) {
            $_where_or_in = implode(" OR ", $where_or_in);
        }

        $where = " 1=1 AND coop_mem_apply.member_id = '".$member_id."' AND (coop_mem_apply.member_status = '1' OR coop_mem_apply.member_status = '2')";

        $share_transaction_text = $this->db->select("value")->from("coop_setting_finance")->where("name = 'receipt_share_month_text'")->get()->row_array();
        $loan_transaction_text_format_setting = $this->db->select("value")->from("coop_setting_finance")->where("name = 'cashier_receipt_loan_text' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        $loan_transaction_text_format_type = !empty($loan_transaction_text_format_setting) ? $loan_transaction_text_format_setting['value'] : 1; // Default 1.

        $this->db->select('profile_id,profile_month,profile_year');
        $this->db->from('coop_finance_month_profile');
        $this->db->where("profile_id = '".$profile_id."'");
        $row = $this->db->get()->result_array();
        $row_profile = @$row[0];
        $profile_id = $row_profile['profile_id'];
        $profile_month = $row_profile['profile_month'];
        $profile_year = $row_profile['profile_year'];

        $this->db->select('setting_value');
        $this->db->from('coop_share_setting');
        $this->db->where("setting_id = '1'");
        $row = $this->db->get()->result_array();
        $row_share_value = $row[0];
        $share_value = $row_share_value['setting_value'];

        $this->db->select(array('coop_mem_apply.member_id'));
        $this->db->from('coop_mem_apply');
        $this->db->where($where);
        $this->db->order_by('member_id ASC');
        $row_member = $this->db->get()->result_array();

        $date_month_end = date('Y-m-t',strtotime(($year).'-'.sprintf("%02d",$month).'-01'));
        $cremation_2_details = $this->db->select("*")
            ->from("coop_setting_cremation_detail")
            ->where("start_date <= '".$date_month_end ."' AND cremation_id = '2'")
            ->order_by("start_date DESC")
            ->limit(1)
            ->get()->result_array();
        $cremation_2_detail = $cremation_2_details[0];

        //get receipt setting data
        $receipt_format = 1;
        $receipt_finance_setting = $this->db->select("*")->from("coop_setting_finance")->where("name = 'receipt_finance_month_format' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        if(!empty($receipt_finance_setting)) {
            $receipt_format = $receipt_finance_setting['value'];
        }
        $receipt_number = "";
        foreach($row_member as $key => $value){
            $this->db->select('*');
            $this->db->from('coop_non_pay');
            $this->db->where("member_id = '".@$value['member_id']."' AND non_pay_month = '".(int)$month."' AND non_pay_year = '".$year."' AND non_pay_status = '0'");
            $row_non_pay = $this->db->get()->result_array();
            $row_non_pay = @$row_non_pay[0];
            $text = '';

            if(!empty($row_non_pay)){
                $non_pay_balance = $row_non_pay['non_pay_amount'];

                $this->db->select(array('t1.*'));
                $this->db->from('coop_finance_month_detail AS t1');
                $this->db->join("coop_deduct AS t2","t1.deduct_id = t2.deduct_id","left");
                $this->db->join("coop_loan AS t3","t1.loan_id = t3.id","left");
                $this->db->join("coop_deduct_detail AS t4","t1.deduct_id = t4.deduct_id AND t4.ref_id = t3.loan_type","left");
                $this->db->where("t1.profile_id = '".@$profile_id."' AND t1.member_id = '".$value['member_id']."' AND t1.run_status = '0'");
                $this->db->order_by("t2.deduct_seq DESC,t4.deduct_detail_seq DESC");
                $row_detail = $this->db->get()->result_array();
                foreach($row_detail as $key_detail => $value_detail){
                    $pay_amount = $value_detail['pay_amount'];
                    $real_pay_amount = $pay_amount;
                    if($pay_amount > $non_pay_balance){
                        $real_pay_amount = $pay_amount - $non_pay_balance;
                        $non_pay_amount = $non_pay_balance;
                        $non_pay_balance = 0;
                    }else{
                        $non_pay_balance = $non_pay_balance - $pay_amount;
                        $non_pay_amount = $pay_amount;
                        $real_pay_amount = 0;
                    }
                    $data_insert = array();
                    $data_insert['deduct_code'] = $value_detail['deduct_code'];
                    $data_insert['non_pay_amount'] = $non_pay_amount;
                    $data_insert['non_pay_amount_balance'] = $non_pay_amount;
                    $data_insert['loan_id'] = $value_detail['loan_id'];
                    $data_insert['loan_atm_id'] = $value_detail['loan_atm_id'];
                    $data_insert['pay_type'] = $value_detail['pay_type'];
                    $data_insert['finance_month_profile_id'] = $value_detail['profile_id'];
                    $data_insert['finance_month_detail_id'] = $value_detail['run_id'];
                    $data_insert['member_id'] = $value_detail['member_id'];
                    $data_insert['non_pay_id'] = $row_non_pay['non_pay_id'];
                    $data_insert['cremation_type_id'] = $value_detail['cremation_type_id'];
                    $data_insert['deposit_account_id'] = $value_detail['deposit_account_id'];
                    $this->db->insert('coop_non_pay_detail',$data_insert);

                    $data_insert = array();
                    $data_insert['real_pay_amount'] = $real_pay_amount;
                    $this->db->where('run_id',$value_detail['run_id']);
                    $this->db->update('coop_finance_month_detail',$data_insert);
                    if($non_pay_balance == 0){
                        break;
                    }
                }

                $data_insert = array();
                $data_insert['non_pay_status'] = '1';
                $this->db->where('non_pay_id',$row_non_pay['non_pay_id']);
                $this->db->update('coop_non_pay',$data_insert);
                $text = 'F';
            }

            //เช็คบัญชีที่ถูกปิดไปแล้ว และมีการออกเรียกเก็บ ให้ ลงรายการ ใบเสร็จ F เป็นรายการชำระไม่ครบ
            $_sub_query_where['t1.run_status'] = 0;
            $this->db->select(array('t1.*'));
            $this->db->from('coop_finance_month_detail AS t1');
            $this->db->join("coop_deduct AS t2","t1.deduct_id = t2.deduct_id","left");
            $this->db->join("coop_loan AS t3","t1.loan_id = t3.id","left");
            $this->db->join("coop_deduct_detail AS t4","t1.deduct_id = t4.deduct_id AND t4.ref_id = t3.loan_type","left");
            $this->db->join("coop_maco_account AS t5","t1.deposit_account_id = t5.account_id AND `t5`.`account_status` = '1'","left");
            $this->db->where($_sub_query_where);
            if($_where_or_in){
                $this->db->where("(".$_where_or_in.")");
            }
            $this->db->order_by("t2.deduct_seq ASC,t4.deduct_detail_seq ASC");
            $subquery = $this->db->get_compiled_select();

            $row_process = $this->db->select("`member_id`,`deduct_code`,SUM(IF (pay_type='principal',real_pay_amount,0)) AS `principal_payment`,SUM(IF (pay_type='interest',real_pay_amount,0)) AS `interest`,`loan_id`,`deduct_id`,`cremation_type_id`,`deposit_account_id`,`loan_atm_id`,`finance_month_type`,`admin_id`,`create_datetime`,`update_datetime`,`department`,`faction`,`level`,`interest_from`,`interest_to`,`loan_amount_balance`,`interest_arrears`,`interest_calculate_arrears`,`interest_arrear_bal`,`interest_notpay`")
                ->from("(".$subquery.") as process")->group_by("process.deduct_code, process.loan_id, process.loan_atm_id, process.deposit_account_id")->get()->result_array();

            //echo $this->db->last_query(); exit;

            foreach($row_process as $key_process => $value_process){
                if($value_process['deduct_code'] == 'DEPOSIT'){
                    $this->db->select('account_id,account_status');
                    $this->db->from('coop_maco_account');
                    $this->db->where("account_id = '".$value_process['deposit_account_id']."' AND account_status = 1");
                    $this->db->limit(1);
                    $row_account = $this->db->get()->result_array();
                    $row_account = @$row_account[0];
                    if(@$row_account['account_id'] != ''){
                        $non_pay_amount = $value_process['pay_amount'];
                        $real_pay_amount = 0;

                        $data_insert = array();
                        $data_insert['non_pay_month'] = @$profile_month;
                        $data_insert['non_pay_year'] = @$profile_year;
                        $data_insert['member_id'] = @$value['member_id'];
                        $data_insert['non_pay_amount'] = $non_pay_amount;
                        $data_insert['non_pay_amount_balance'] = $non_pay_amount;
                        $data_insert['admin_id'] = @$_SESSION['USER_ID'];
                        $data_insert['non_pay_status'] = '0';
                        if($this->db->insert('coop_non_pay', $data_insert)){
                            $non_pay_id = $this->db->insert_id();

                            $data_insert = array();
                            $data_insert['deduct_code'] = $value_process['deduct_code'];
                            $data_insert['non_pay_amount'] = $non_pay_amount;
                            $data_insert['non_pay_amount_balance'] = $non_pay_amount;
                            $data_insert['loan_id'] = $value_process['loan_id'];
                            $data_insert['loan_atm_id'] = $value_process['loan_atm_id'];
                            $data_insert['pay_type'] = $value_process['pay_type'];
                            $data_insert['finance_month_profile_id'] = $value_process['profile_id'];
                            $data_insert['finance_month_detail_id'] = $value_process['run_id'];
                            $data_insert['member_id'] = $value_process['member_id'];
                            $data_insert['non_pay_id'] = $non_pay_id;
                            $data_insert['cremation_type_id'] = $value_process['cremation_type_id'];
                            $data_insert['deposit_account_id'] = $value_process['deposit_account_id'];
                            $this->db->insert('coop_non_pay_detail',$data_insert);

                            $data_insert = array();
                            $data_insert['real_pay_amount'] = $real_pay_amount;
                            $this->db->where('run_id',$value_process['run_id']);
                            $this->db->update('coop_finance_month_detail',$data_insert);

                            $text = 'F';
                        }
                    }
                }
            }

            if(empty($text)) {
                $text = 'B';
            }
            if($receipt_format == 1) {
                $yymm = date("m", strtotime($process_date));
                // $yymm = "11";//กลับมาแก้ด้วย
                $yy = (date("Y", strtotime($process_date))+543);
                $yy_full = (date("Y", strtotime($process_date))+543);
                $yy = substr($yy,2);
                $this->db->select(array('*'));
                $this->db->from('coop_receipt');
                $this->db->where("receipt_id LIKE '".$yymm.'B'.$yy."%' OR receipt_id LIKE '".$yymm.'F'.$yy."%'");
                $this->db->order_by("order_by DESC");
                $this->db->limit(1);

                $row_receipt = $this->db->get()->result_array();
                $row_receipt = @$row_receipt[0];

                if($row_receipt['receipt_id'] != '') {
                    $id = (int) substr($row_receipt["receipt_id"], 6);
                    $receipt_number = $yymm.''.$text.''.$yy.sprintf("%06d", $id + 1);

                }else {
                    $receipt_number = $yymm.''.$text.''.$yy."000001";
                }

                $order_by_id =  $row_receipt["order_by"]+1 ;

                $sql = "SELECT receipt_id
						FROM coop_receipt
						WHERE receipt_id = '".$receipt_number."'";
                $rs_chk_receipt = $this->db->query($sql);

                if($rs_chk_receipt->num_rows() == 1){
                    $this->db->select(array('*'));
                    $this->db->from('coop_receipt');
                    $this->db->where("receipt_id LIKE '".$yymm.'B'.$yy."%' OR receipt_id LIKE '".$yymm.'F'.$yy."%'");
                    $this->db->order_by("order_by DESC");
                    $this->db->limit(1);
                    $row_receipt = $this->db->get()->result_array();
                    $row_receipt = @$row_receipt[0];

                    if($row_receipt['receipt_id'] != '') {
                        $id = (int) substr($row_receipt["receipt_id"], 6);
                        $receipt_number = $yymm.''.$text.''.$yy.sprintf("%06d", $id + 1);
                    }else {
                        $receipt_number = $yymm.''.$text.''.$yy."000001";
                    }
                }
            } else {
                $receipt_number = $this->Finance_libraries->generate_finance_month_receipt_id($receipt_format, $text, $process_date);
            }

            $order_by_id =  $row_receipt["order_by"]+1;
            $sum_count = 0;

            //start บันทึกใบเสร็จ
            $this->db->select(array('SUM(t1.real_pay_amount) AS sum_count'));
            $this->db->from('coop_finance_month_detail AS t1');
            $this->db->where($_sub_query_where);
            $real_pay_amount = $this->db->get()->result_array();
            $sum_count = @$real_pay_amount[0]['sum_count'];

            $data_insert = array();
            $data_insert['receipt_id'] = @$receipt_number;
            $data_insert['member_id'] = @$value['member_id'];
            $data_insert['admin_id'] = @$_SESSION['USER_ID'];
            $data_insert['sumcount'] = number_format($sum_count, self::_getPrecision(), '.', '');
            $data_insert['receipt_datetime'] = $process_date." ".date('H:i:s');
            $data_insert['month_receipt'] = $month;
            $data_insert['year_receipt'] = $year;
            $data_insert['finance_month_profile_id'] = @$profile_id;
            $data_insert['pay_type'] = @$_POST['pay_type'];
            $data_insert['order_by'] = @$order_by_id;
            if($this->db->insert('coop_receipt', $data_insert)){

                //Prepare Arry for keep loan with occasional payment
                $occasional_loans = array();
                $occasional_loan_atms = array();

                $this->db->select(array('t1.*'));
                $this->db->from('coop_finance_month_detail AS t1');
                $this->db->join("coop_deduct AS t2","t1.deduct_id = t2.deduct_id","left");
                $this->db->join("coop_loan AS t3","t1.loan_id = t3.id","left");
                $this->db->join("coop_deduct_detail AS t4","t1.deduct_id = t4.deduct_id AND t4.ref_id = t3.loan_type","left");
                $this->db->join("coop_maco_account AS t5","t1.deposit_account_id = t5.account_id AND `t5`.`account_status` = '1'","left");
                $this->db->where($_sub_query_where);
                if($_where_or_in){
                    $this->db->where("(".$_where_or_in.")");
                }
                $this->db->order_by("t2.deduct_seq ASC,t4.deduct_detail_seq ASC");
                $subquery = $this->db->get_compiled_select();

                $row_process = $this->db->select("`member_id`, `run_id`, `deduct_code`, SUM(IF (pay_type='principal',real_pay_amount,0)) AS `principal_payment`,SUM(IF (pay_type='interest',real_pay_amount,0)) AS `interest`,`loan_id`,`deduct_id`,`cremation_type_id`,`deposit_account_id`,`loan_atm_id`,`finance_month_type`,`admin_id`,`create_datetime`,`update_datetime`,`department`,`faction`,`level`,`interest_from`,`interest_to`,`loan_amount_balance`,`interest_arrears`,`interest_calculate_arrears`,`interest_arrear_bal`,`interest_notpay`")
                    ->from("(".$subquery.") as process")->group_by("process.deduct_code, process.loan_id, process.loan_atm_id, process.deposit_account_id")->get()->result_array();

                foreach($row_process as $key_process => $value_process){

                    if(in_array($value_process['deduct_code'], array('ATM', 'LOAN'))) {
                        if($value_process['deduct_code'] == "ATM") {
                            $this->db->where('loan_atm_id', $value_process['loan_atm_id']);
                        }else{
                            $this->db->where('loan_id', $value_process['loan_id']);
                        }

                    }else{
                        $this->db->where('run_id', $value_process['run_id']);
                    }
                    $this->db->set('run_status', '1');
                    $this->db->update('coop_finance_month_detail');
                    if($value_process['principal_payment'] > 0 || $value_process['interest']>0 ){

                        $this->db->select(array('*'));
                        $this->db->from('coop_deduct');
                        $this->db->where("deduct_id = '".$value_process['deduct_id']."'");
                        $this->db->limit(1);
                        $row_deduct = $this->db->get()->result_array();
                        $row_deduct = @$row_deduct[0];

                        if($value_process['deduct_code'] == 'LOAN' || $value_process['deduct_code'] == 'GUARANTEE'){
                            $this->db->select(
                                array(
                                    'coop_loan.id',
                                    'coop_loan.loan_type',
                                    'coop_loan.contract_number',
                                    'coop_loan.loan_amount_balance',
                                    'coop_loan.interest_per_year',
                                    'coop_loan.period_now',
                                    'coop_loan_transfer.date_transfer',
                                    'coop_loan_name.loan_name',
                                    'coop_loan.createdatetime'
                                )
                            );

                            $this->db->from('coop_loan');
                            $this->db->join('coop_loan_transfer', 'coop_loan_transfer.loan_id = coop_loan.id', 'left');
                            $this->db->join('coop_loan_name', 'coop_loan_name.loan_name_id = coop_loan.loan_type', 'inner');
                            $this->db->where("
									coop_loan.id = '".$value_process['loan_id']."'
								");
                            $row_loan = $this->db->get()->result_array();
                            $row_loan = $row_loan[0];

                            if($row_loan['period_now']!=''){
                                $period_count = $row_loan['period_now']+1;
                            }else{
                                $period_count = 1;
                            }

                            $interest = $value_process['interest'] ;
                            $return_amount = 0;
                            if($value_process['principal_payment'] > $row_loan['loan_amount_balance']){
                                if($row_loan['loan_amount_balance'] <= 0){
                                    $return_amount = ($value_process['principal_payment'] *-1) + $row_loan['loan_amount_balance'];
                                }else{
                                    $return_amount = $value_process['principal_payment'] - $row_loan['loan_amount_balance'];
                                }
                                $pay_amount = $value_process['principal_payment'];
                                $balance = $return_amount;
                            }else{
                                $pay_amount = $value_process['principal_payment'];
                                $balance = $row_loan['loan_amount_balance'] - $pay_amount;
                            }
                            $transaction_text = $this->Finance_libraries->generate_loan_receipt_text_finance_month($row_loan['loan_id'], 15, "principal");

                            if($balance > 0){
                                $data_insert = array();
                                $data_insert['loan_amount_balance'] = $balance;
                                $data_insert['period_now'] = $period_count;
                                $data_insert['date_last_interest'] = $value_process['interest_to'];
                                $this->db->where('id', $value_process['loan_id']);
                                $this->db->update('coop_loan', $data_insert);
                            }else{
                                $data_insert = array();
                                $data_insert['loan_amount_balance'] = $balance;
                                $data_insert['period_now'] = $period_count;
                                $data_insert['date_last_interest'] = $value_process['interest_to'];
                                $data_insert['loan_status'] = '4';
                                $this->db->where('id', $value_process['loan_id']);
                                $this->db->update('coop_loan', $data_insert);
                            }

                            $loan_transaction = array();
                            $loan_transaction['loan_id'] = $value_process['loan_id'];
                            $loan_transaction['loan_amount_balance'] = $balance;
                            $loan_transaction['transaction_datetime'] = $process_date." ".date('H:i:s');
                            $loan_transaction['receipt_id'] = $receipt_number;
                            $loan_transaction['interest'] = $value_process['interest_arrear_bal'];
                            $this->loan_libraries->loan_transaction_arrears('PM', $loan_transaction);

                            //Calculate non_pay_amount_balance of relate loan_id
                            $non_pay_details = $this->db->select("run_id, non_pay_id")
                                ->from("coop_non_pay_detail")
                                ->where("loan_id = '".$value_process['loan_id']."' AND pay_type = 'principal' AND non_pay_amount_balance > '".$balance."'")
                                ->get()->result_array();

                            foreach($non_pay_details as $non_pay_detail) {
                                $data_insert = array();
                                $data_insert['non_pay_amount_balance'] = $balance;
                                $this->db->where('run_id', $non_pay_detail["run_id"]);
                                $this->db->update('coop_non_pay_detail', $data_insert);

                                $details = $this->db->select("sum(non_pay_amount_balance) as sum")
                                    ->from("coop_non_pay_detail")
                                    ->where("non_pay_id = '".$non_pay_detail["non_pay_id"]."'")
                                    ->get()->result_array();
                                $data_insert = array();
                                if($details[0]['sum'] == 0) {
                                    $data_insert["non_pay_status"] = 2;
                                }
                                $data_insert["non_pay_amount_balance"] = $details[0]['sum'];
                                $this->db->where('non_pay_id', $non_pay_detail["non_pay_id"]);
                                $this->db->update('coop_non_pay', $data_insert);
                            }

                        }else if($value_process['deduct_code'] == 'SHARE'){
                            $this->db->select(array('share_period'));
                            $this->db->from('coop_mem_share');
                            $this->db->where("member_id = '".$value_process['member_id']."' AND (share_status = '1' OR share_status = '5') AND (share_period IS NOT NULL AND share_period <> 0)");
                            $this->db->order_by("share_date DESC, share_id DESC");
                            $this->db->limit(1);
                            $row_shar_max = $this->db->get()->result_array();
                            $share_period_max = @$row_shar_max[0]['share_period'];

                            $this->db->select(array('*'));
                            $this->db->from('coop_mem_share');
                            $this->db->where("member_id = '".$value_process['member_id']."' AND (share_status = '1' OR share_status = '5')");
                            $this->db->order_by("share_date DESC, share_id DESC");
                            $this->db->limit(1);
                            $row_share = $this->db->get()->result_array();
                            $row_share = @$row_share[0];

                            $pay_amount = $value_process['principal_payment'];
                            $interest = '';
                            $balance = $row_share['share_collect_value'] + $value_process['principal_payment'];
                            $period_count = $share_period_max+1;
                            $transaction_text = !empty($share_transaction_text) ? $share_transaction_text['value'] : 'ชำระเงินค่าหุ้นรายเดือน';

                            $data_insert = array();
                            $data_insert['member_id'] = @$value_process['member_id'];
                            $data_insert['admin_id'] = @$_SESSION['USER_ID'];
                            $data_insert['share_type'] = 'SPM';
                            $data_insert['share_date'] = $process_date." ".date('H:i:s');
                            $data_insert['share_payable'] = @$row_share['share_collect'];
                            $data_insert['share_payable_value'] = @$row_share['share_collect_value'];
                            $data_insert['share_early'] = ($pay_amount/$share_value);
                            $data_insert['share_early_value'] = $pay_amount;
                            $data_insert['share_collect'] = ($balance/$share_value);
                            $data_insert['share_collect_value'] = $balance;
                            $data_insert['share_value'] = $share_value;
                            $data_insert['share_status'] = '1';
                            $data_insert['share_bill'] = $receipt_number;
                            $data_insert['share_bill_date'] = $process_date." ".date('H:i:s');
                            $data_insert['share_period'] = $period_count;
                            $this->db->insert('coop_mem_share', $data_insert);

                        }else if($value_process['deduct_code'] == 'CREMATION'){
                            $this->db->select(array('*'));
                            $this->db->from('coop_cremation_data');
                            $this->db->where("cremation_id = '".$value_process['cremation_type_id']."'");
                            $this->db->limit(1);
                            $row_cremation = $this->db->get()->result_array();
                            $row_cremation = @$row_cremation[0];

                            $pay_amount = $value_process['principal_payment'];
                            $interest = '';
                            $balance = '';
                            $period_count = '';
                            $transaction_text = $row_deduct['deduct_detail']." ".$row_cremation['cremation_name_short'];

                            if($value_process['cremation_type_id'] == 2) {
                                $process_timestamp = date('Y-m-d H:i:s');
                                $pay_amount = $value_process['principal_payment'];

                                $cremations = $this->db->select("t1.id as finance_month_id, t1.member_cremation_id, t1.pay_amount, t1.real_pay_amount, t2.adv_payment_balance, t2.adv_id")
                                    ->from("coop_cremation_finance_month as t1")
                                    ->join("coop_cremation_advance_payment as t2", "t1.member_cremation_id = t2.member_cremation_id", "left")
                                    ->where("t1.ref_member_id = '".$value_process['member_id']."' AND t1.profile_id = '".$profile_id."' AND t1.status = 1")
                                    ->get()->result_array();
                                $insert_datas = array();
                                foreach($cremations as $cremation) {
                                    if($cremation["adv_payment_balance"] < $cremation_2_detail["advance_pay"] && $pay_amount > 0) {
                                        $data_insert = array();
                                        $transaction_insert = array();
                                        $deduct_cremation = $cremation["pay_amount"];
                                        $transaction_insert["amount"] = $deduct_cremation;
                                        $real_pay_amount = $cremation["real_pay_amount"];
                                        if ($deduct_cremation <= $pay_amount) {
                                            $data_insert['adv_payment_balance'] = $cremation["adv_payment_balance"] + $deduct_cremation;
                                            $transaction_insert["total"] = $cremation["adv_payment_balance"] + $deduct_cremation;
                                            $real_pay_amount += $deduct_cremation;
                                        } else {
                                            $data_insert['adv_payment_balance'] = $cremation["adv_payment_balance"] + $pay_amount;
                                            $transaction_insert["total"] = $cremation["adv_payment_balance"] + $pay_amount;
                                            $real_pay_amount += $pay_amount;
                                        }

                                        $pay_amount = $pay_amount - $deduct_cremation;
                                        //Update advance month
                                        $data_insert["lastpayment"] = $process_timestamp;
                                        $data_insert["updatetime"] = $process_timestamp;
                                        $this->db->where('adv_id', $cremation['adv_id']);
                                        $this->db->update('coop_cremation_advance_payment', $data_insert);

                                        //Genarate data for advance month transaction
                                        $transaction_insert["finance_month_detail_id"] = $value_process['run_id'];
                                        $transaction_insert["cremation_finance_month_id"] = $cremation["id"];
                                        $transaction_insert["member_cremation_id"] = $cremation["member_cremation_id"];
                                        $transaction_insert["type"] = "FMP";
                                        $transaction_insert["status"] = 1;
                                        $transaction_insert["created_at"] = $process_timestamp;
                                        $transaction_insert["updated_at"] = $pr11B63000018ocess_timestamp;
                                        $insert_datas[]  = $transaction_insert;

                                        //Update cremation finance month
                                        $data_update = array();
                                        $data_update["real_pay_amount"] = $real_pay_amount;
                                        $data_update["updated_at"] = $process_timestamp;
                                        $this->db->where('id', $cremation['finance_month_id']);
                                        $this->db->update('coop_cremation_finance_month', $data_update);

                                        //Genarate cremation finance month receipt
                                        //Generate Receipt identification
                                        $yymm = (date("Y")+543).date("m");
                                        $this->db->select(array('*'));
                                        $this->db->from('coop_cremation_receipt');
                                        $this->db->where("receipt_id LIKE '".$yymm."%'");
                                        $this->db->order_by("receipt_id DESC");
                                        $this->db->limit(1);
                                        $row_receipt = $this->db->get()->result_array();
                                        $row_receipt = $row_receipt[0];

                                        if($row_receipt['receipt_id'] != '') {
                                            $id = (int) substr($row_receipt["receipt_id"], 6);
                                            $receipt_id = $yymm.sprintf("%06d", $id + 1);
                                        }else {
                                            $receipt_id = $yymm."000001";
                                        }
                                        $data_insert = array();
                                        $data_insert["receipt_id"] = $receipt_id;
                                        $data_insert["member_cremation_id"] = $cremation["member_cremation_id"];
                                        $data_insert["main_receipt_id"] = $receipt_number;
                                        $data_insert["amount"] = $real_pay_amount;
                                        $data_insert["detail"] = "ชำระเงินฌาปนกิจสงเคราะห์";
                                        $data_insert["status"] = 1;
                                        $data_insert["user_id"] = $_SESSION['USER_ID'];
                                        $data_insert["created_at"] = $process_timestamp;
                                        $data_insert["updated_at"] = $process_timestamp;
                                        $this->db->insert('coop_cremation_receipt', $data_insert);
                                        $id_receipt = $this->db->insert_id();

                                        //Relate receipt to finance month
                                        $data_insert = array();
                                        $data_insert["receipt_id"] = $id_receipt;
                                        $data_insert["finance_month_id"] = $cremation['finance_month_id'];
                                        $data_insert["created_at"] = $process_timestamp;
                                        $data_insert["updated_at"] = $process_timestamp;
                                        $this->db->insert('coop_cremation_finance_month_receipt', $data_insert);
                                    }
                                }
                                if (!empty($insert_datas)) {
                                    //Insert advance month transaction
                                    $this->db->insert_batch('coop_cremation_advance_payment_transaction', $insert_datas);
                                }
                            }
                        }else if($value_process['deduct_code'] == 'DEPOSIT'){
                            $DEPOSIT = $value_process['principal_payment'];

                            $this->db->select('*');
                            $this->db->from('coop_maco_account');
                            $this->db->where("account_id = '".$value_process['deposit_account_id']."'  AND account_status = 0");
                            $this->db->limit(1);
                            $row_account = $this->db->get()->result_array();
                            $row_account = @$row_account[0];
                            if(@$row_account['account_id'] != ''){
                                $this->db->select('*');
                                $this->db->from('coop_account_transaction');
                                $this->db->where("account_id = '".$value_process['deposit_account_id']."'");
                                $this->db->order_by('transaction_time DESC,transaction_id DESC');
                                $this->db->limit(1);
                                $row_transaction = $this->db->get()->result_array();
                                if(!empty($row_transaction)){
                                    $balance = @$row_transaction[0]['transaction_balance'];
                                    $balance_no_in = @$row_transaction[0]['transaction_no_in_balance'];
                                }else{
                                    $balance = 0;
                                    $balance_no_in = 0;
                                }
                                $sum = $balance + $DEPOSIT;
                                $sum_no_in = $balance_no_in + $DEPOSIT;

                                $data_insert = array();
                                $data_insert['transaction_time'] = $process_date." ".date('H:i:s');
                                $data_insert['transaction_list'] = 'DEPP';
                                $data_insert['transaction_withdrawal'] = '';
                                $data_insert['transaction_deposit'] = $DEPOSIT;
                                $data_insert['transaction_balance'] = $sum;
                                $data_insert['transaction_no_in_balance'] = $sum_no_in;
                                $data_insert['user_id'] = $_SESSION['USER_ID'];
                                $data_insert['account_id'] = $value_process['deposit_account_id'];
                                $data_insert['receipt_id'] = @$receipt_number;
                                $this->db->insert('coop_account_transaction', $data_insert);

                                $account_period = $row_account['account_period']!=''?($row_account['account_period']+1):1;

                                $data_insert = array();
                                $data_insert['account_period'] = $account_period;
                                $this->db->where('account_id', $value_process['deposit_account_id']);
                                $this->db->update('coop_maco_account', $data_insert);

                                $pay_amount = $value_process['principal_payment'];
                                $interest = '';
                                $balance = $sum;
                                $period_count = $account_period;
                                $transaction_text = $row_deduct['deduct_detail']." เลขที่บัญชี".$value_process['deposit_account_id'];
                            }
                        }else if($value_process['deduct_code'] == 'ATM'){
                            $this->db->select(
                                array(
                                    't1.loan_atm_id',
                                    't1.total_amount_approve',
                                    't1.total_amount_balance',
                                    't1.contract_number'
                                )
                            );
                            $this->db->from('coop_loan_atm as t1');
                            $this->db->where("
								t1.loan_atm_id = '".$value_process['loan_atm_id']."'
							");
                            $row_loan_atm = $this->db->get()->result_array();
                            $row_loan_atm = $row_loan_atm[0];
                            $this->db->select(
                                array(
                                    't1.loan_id',
                                    't1.loan_atm_id',
                                    't1.loan_amount_balance'
                                )
                            );
                            $this->db->from('coop_loan_atm_detail as t1');
                            $this->db->where("
								t1.loan_atm_id = '".$value_process['loan_atm_id']."'
								AND t1.loan_status = '0'
							");
                            $this->db->order_by('loan_id ASC');
                            $row_loan_atm_detail = $this->db->get()->result_array();

                            $interest = $value_process['interest'];
                            $pay_amount = $value_process['principal_payment'];
                            $return_amount = 0;
                            $principal_payment = $value_process['principal_payment'];
                            foreach($row_loan_atm_detail as $key_atm => $value_atm){
                                if($principal_payment > 0){
                                    if($principal_payment >= $value_atm['loan_amount_balance']){
                                        $data_insert = array();
                                        $data_insert['loan_amount_balance'] = 0;
                                        $data_insert['loan_status'] = '1';
                                        $data_insert['date_last_pay'] = $process_date;
                                        $this->db->where('loan_id', $value_atm['loan_id']);
                                        $this->db->update('coop_loan_atm_detail', $data_insert);
                                        $principal_payment = $principal_payment - $value_atm['loan_amount_balance'];
                                    }else{
                                        $data_insert = array();
                                        $data_insert['loan_amount_balance'] = $value_atm['loan_amount_balance']-$principal_payment;
                                        $data_insert['date_last_pay'] = $process_date;
                                        $this->db->where('loan_id', $value_atm['loan_id']);
                                        $this->db->update('coop_loan_atm_detail', $data_insert);
                                        $principal_payment = 0;
                                    }
                                }
                            }
                            $over_peyment = 0;
                            $total_amount_balance = $row_loan_atm['total_amount_balance'] + $value_process['principal_payment'];
                            $data_insert = array();
                            $data_insert['total_amount_balance'] = $total_amount_balance;
                            $this->db->where('loan_atm_id', $value_process['loan_atm_id']);
                            $this->db->update('coop_loan_atm', $data_insert);

                            $loan_amount_balance = $row_loan_atm['total_amount_approve'] - $total_amount_balance - $over_peyment;
                            $balance = $loan_amount_balance;

                            $atm_transaction = array();
                            $atm_transaction['loan_atm_id'] = $value_process['loan_atm_id'];
                            $atm_transaction['loan_amount_balance'] = $loan_amount_balance;
                            $atm_transaction['transaction_datetime'] = $process_date." ".date('H:i:s');
                            $atm_transaction['receipt_id'] = @$receipt_number;
                            $atm_transaction['interest'] = $value_process['interest_arrear_bal'];
                            $this->loan_libraries->atm_transaction_arrears('PM', $atm_transaction);

                            $transaction_text = $this->Finance_libraries->generate_loan_atm_receipt_text_finance_month($value_process['loan_atm_id'], 15, "principal");

                            //Calculate non_pay_amount_balance of relate loan_atm_id
                            $non_pay_details = $this->db->select("run_id, non_pay_id")
                                ->from("coop_non_pay_detail")
                                ->where("loan_atm_id = '".$value_process['loan_atm_id']."' AND pay_type = 'principal' AND non_pay_amount_balance > '".$loan_amount_balance."'")
                                ->get()->result_array();

                            foreach($non_pay_details as $non_pay_detail) {
                                $data_insert = array();
                                $data_insert['non_pay_amount_balance'] = $loan_amount_balance;
                                $this->db->where('run_id', $non_pay_detail["run_id"]);
                                $this->db->update('coop_non_pay_detail', $data_insert);

                                $details = $this->db->select("sum(non_pay_amount_balance) as sum")
                                    ->from("coop_non_pay_detail")
                                    ->where("non_pay_id = '".$non_pay_detail["non_pay_id"]."'")
                                    ->get()->result_array();
                                $data_insert = array();
                                if($details[0]['sum'] == 0) {
                                    $data_insert["non_pay_status"] = 2;
                                }
                                $data_insert["non_pay_amount_balance"] = $details[0]['sum'];
                                $this->db->where('non_pay_id', $non_pay_detail["non_pay_id"]);
                                $this->db->update('coop_non_pay', $data_insert);
                            }

                        }else{
                            $pay_amount = $value_process['principal_payment'];
                            $interest = '';
                            $balance = '';
                            $period_count = '';
                            $transaction_text = $row_deduct['deduct_detail'];
                        }

                        $data_insert = array();
                        $data_insert['receipt_id'] = $receipt_number;
                        $data_insert['receipt_list'] = $row_deduct['account_list_id'];
                        $data_insert['receipt_count'] = number_format((double)$pay_amount+(double)$interest,2,'.', '' );
                        $this->db->insert('coop_receipt_detail', $data_insert);

                        //บันทึกการชำระเงิน
                        if(empty($interest)) $interest = 0;
                        if(empty($pay_amount)) $pay_amount = 0;
                        $data_insert = array();
                        $data_insert['receipt_id'] = $receipt_number;
                        $data_insert['member_id'] = @$value_process['member_id'];
                        $data_insert['loan_id'] = @$value_process['loan_id'];
                        $data_insert['loan_atm_id'] = @$value_process['loan_atm_id'];
                        $data_insert['account_list_id'] = $row_deduct['account_list_id'];
                        $data_insert['principal_payment'] = @$pay_amount;
                        $data_insert['interest'] = @$interest;
                        $data_insert['total_amount'] = ($pay_amount+$interest);
                        $data_insert['payment_date'] = $process_date;
                        $data_insert['period_count'] = @$period_count;
                        $data_insert['loan_amount_balance'] = $balance;
                        $data_insert['createdatetime'] = $process_date." ".date('H:i:s');
                        $data_insert['transaction_text'] = $transaction_text;
                        $data_insert['deduct_type'] = 'all';
                        $this->db->insert('coop_finance_transaction', $data_insert);
                    }
                }
            }
            //end บันทึกใบเสร็จ
        }
        return $receipt_number;
    }

    public function getSubQueryWhere($data){
        $_sub_query_where["t1.member_id"]  = $data["member_id"];
        $_sub_query_where["t1.profile_id"] = $data["profile_id"];

        if(!empty($data["ref_id"]) && !empty($data["deduct_code"])) {
            $deduct_code = $_sub_query_where["t1.deduct_code"] = $data["deduct_code"];
            $ref_id = $data["ref_id"];
            if ($deduct_code == "LOAN" || $deduct_code == 'GUARANTEE') {
                $_sub_query_where["t1.loan_id"] = $ref_id;
            } else if ($deduct_code == "DEPOSIT") {
                $_sub_query_where["t1.deposit_account_id"] = $ref_id;
            } else if ($deduct_code == "ATM") {
                $_sub_query_where["t1.loan_atm_id"] = $ref_id;
            } else if ($deduct_code == "CREMATION") {
                $_sub_query_where["t1.cremation_type_id"] = $ref_id;
            } else {
                $_sub_query_where["t1.member_id"] = $ref_id;
            }
        }
        $_sub_query_where["t1.run_status"] = "0";
        return $_sub_query_where;
    }

    public function getFinanceMonthDetail($data, $run_id = array(), $type = "in"){
        $this->db->select(array('t1.*'));
        $this->db->from('coop_finance_month_detail AS t1');
        $this->db->join("coop_deduct AS t2","t1.deduct_id = t2.deduct_id","left");
        $this->db->join("coop_loan AS t3","t1.loan_id = t3.id","left");
        $this->db->join("coop_deduct_detail AS t4","t1.deduct_id = t4.deduct_id AND t4.ref_id = t3.loan_type","left");
        $this->db->join("coop_maco_account AS t5","t1.deposit_account_id = t5.account_id AND `t5`.`account_status` = '1'","left");
        $this->db->where(self::getSubQueryWhere($data));
        if(sizeof($run_id) && !empty($run_id) ){
            if($type == 'not_in') {
                $this->db->where_not_in("run_id", $run_id);
            }else{
                $this->db->where_in("run_id", $run_id);
            }
        }
        $this->db->order_by("t2.deduct_seq ASC,t4.deduct_detail_seq ASC");
        $subquery = $this->db->get_compiled_select();

        return $this->db->select("`member_id`,`deduct_code`,SUM(IF (pay_type='principal',real_pay_amount,0)) AS `principal_payment`,SUM(IF (pay_type='interest',real_pay_amount,0)) AS `interest`,`loan_id`,`deduct_id`,`cremation_type_id`,`deposit_account_id`,`loan_atm_id`,`finance_month_type`,`admin_id`,`create_datetime`,`update_datetime`,`department`,`faction`,`level`,`interest_from`,`interest_to`,`loan_amount_balance`,`interest_arrears`,`interest_calculate_arrears`,`interest_arrear_bal`,`interest_notpay`")
            ->from("(".$subquery.") as process")->group_by("process.deduct_code, process.loan_id, process.loan_atm_id, process.deposit_account_id")->get()->result_array();

    }

    private  $_precision = 0;

    private function setInterestRound($value = null){
        if($set != null){
            $this->_precision = $value;
        }else{
            $this->_precision = $this->Setting_model->get('round_interest');
        }
    }

    private function _getPrecision(){
        return $this->_precision;
    }


    public function getReturnBill($date){
        $datetime = strtotime($date);
        $year = date('Y',  $datetime) + 543 ;
        $month = date('n', $datetime);
        $row = self::getReturnBillLast($year, $month);
        if(sizeof($row)){
            $_bill_id = (int)substr($row['bill_id'], 7, 5) + 1;
        }else{
            $_bill_id = 1;
        }
        return sprintf('R%s%s%05d', $year, $month, $_bill_id);
    }


    private function getReturnBillLast($year, $month){
        return $this->db->select("bill_id")
                ->where("bill_id LIKE 'R{$year}{$month}%'")
                ->order_by("bill_id", "desc")
            ->get("coop_process_return", 1)->row_array();
    }
}
