<?php


class Loan_contract extends  CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if(base64_decode($_SERVER['QUERY_STRING'], true)){
            parse_str($this->center_function->decrypt($_SERVER['QUERY_STRING']), $_GET);
        }
        $this->load->model("Condition_loan_model", "condition_model");
        $this->load->model("Loan_model", "loan_model");
    }

    public function index(){

        $arr_data = array();

        $this->db->select('*');
        $this->db->from('setting_loan_page');
        $default_loan = $this->db->get()->row_array();


        $arr_data['default_loan'] = $default_loan['loan_type_id'];
        $arr_data['default_loan_name'] = $default_loan['loan_name_id'];
        $arr_data['buy_list_enable'] = 0;
        $arr_data['cost_list_enable'] = 1;
        $arr_data['deduct_list_enable'] = 1;

        $arr_data['rs_rule'] = $this->contract->getTermOfLoanUseCurrent();
        $arr_data['rs_loan_type'] = $this->contract->getLoanType();
        $arr_data['rs_loan_name'] = $this->contract->getLoanNameByTypeId($arr_data['default_loan']);
        $arr_data['rs_loan_reason'] = $this->contract->getLoanReason();

        if($this->input->get('member_id')!=''){
            $member_id = $this->input->get('member_id');
        }else{
            $member_id = '';
        }

        if($this->input->get('fixed_date')){
            $fixed_date = $this->input->get('fixed_date');
        }else{
            $fixed_date = date('Y-m-d');
        }

        if($member_id != ""){

            //member info
            $info = $this->info->member($member_id);
            $member = $info->getInfo();
            $arr_data['mem_group_name'] = $this->info->getGroup('level');
            $arr_data['mem_type'] = $member->mem_type;
            $arr_data['member_id'] = $member->member_id;
            $arr_data['apply_date'] = $member->apply_date;
            $arr_data['apply_type_id'] = $member->apply_type_id;
            $arr_data['salary'] = $member->salary;
            $arr_data['share_month'] = $member->share_month;
            $arr_data['position'] = $info->getPosition();
            $arr_data['fullname'] =  $info->getFullName();
            $arr_data['birthday'] = $member->birthday;
            $arr_data['age'] = $this->center_function->cal_age($member->birthday)." ปี ";
            $arr_data['member_age'] = $this->center_function->cal_age(@$member->member_date) . " ปี " . $this->center_function->cal_age(@$member->member_date, 'm')." เดือน";
            $arr_data['income'] = $info->getIncoming();
            $arr_data['income_per_month'] = array_sum(array_column($arr_data['income'], 'income_value'))+$member->salary;
            $date_arr = explode('-',@$arr_data['birthday']);
            $sday = $date_arr[0];
            $smonth = $date_arr[1];
            $syear = $date_arr[2];
            $syear -= 543;
            $age_month_retire = 10 - $smonth;//เศษเดือน
            if($age_month_retire<=0){
                $age_month_retire=0;
            }
            $age_retire =  59 - intval($this->center_function->cal_age($arr_data['birthday']));//ปีที่เหลือนำไปคำนวณเกษียณ
            $this->db->select('year_retire')->from('coop_term_of_loan')->limit(1);
            $year_re = $this->db->get()->result_array();
            $year_retire=$year_re[0]['year_retire'];
            $arr_data['period_person'] = ($age_retire* 12)+ $age_month_retire +($year_retire*12);//สูตรคำนวณงวด
            //จ่ายต่องวด
            $arr_data['per_period_person'] = $arr_data['salary']-$arr_data['salary']*(30/100);

            if($member->mem_type == 2){
                $arr_data['st_status'] = $info->getStStatus();
            }
            //share
            $currentShare = $this->share->current($member_id, $fixed_date);
            $arr_data['cal_share'] = $currentShare->getCollectVal();
            $arr_data['count_share'] = $currentShare->getCollect();
            $arr_data['share_period'] = $this->share->getShareMonth($member_id);
            $arr_data['share_early_value'] = $this->share->getShareEarly();

            //สถานะงดหุ้น
            $arr_data['share_month_status'] = 'N/A';

            //deposit
            $deposit = $this->deposit->current($member_id, $fixed_date);
            $arr_data['data_account'] = $deposit->getAccount();
            $arr_data['count_account'] = $deposit->getAccountCount();
            $arr_data['cal_account'] = $deposit->getBalanceAll();

            //previous loan
            $contract = $this->contract->current($member_id);
            $arr_data['contract'] = $contract->get();
            $arr_data['atm_contract'] = $contract->getATM();
          
            //guarantee
            $guaruntee = $this->guarantee->current($member_id);
            $arr_data['count_contract'] = 0;
            $arr_data['sum_guarantee_balance'] = 0;
            $arr_data['rs_guarantee'] = $guaruntee->get();
            $arr_data['sum_balance'] = $guaruntee->getBalance();
            $arr_data['count_contract'] = $guaruntee->itemCount();

            //payment
            $arr_data['rs_bank'] = $this->db->get("coop_bank")->result_array();
            $arr_data['content_bank'] = $this->db->get_where("coop_mem_bank_account", array(
                "member_id" => $member_id
            ))->row_array();

            //keeping
            $payment_other = $payment_deposit = $payment_share = $payment_loan = 0;                 //initial

            $keep = $this->keep->find(date('Y-m-d H:i:s'), $member_id);
            $keeping = $keep->getSummary();
            if(sizeof($keeping)) {
                foreach ($keeping as $key => $item) {
                    if ($item['deduct_code'] == "DEPOSIT") {
                        $payment_deposit += $item['sum_amount'];
                        $arr_data['payment_deposit'] += $item['sum_amount'];
                        continue;
                    }
                    if ($item['deduct_code'] == "LOAN") {

                        if($item['pay_type'] == "principal"){
                            $arr_data['payment_principal'] = $item['sum_amount'];
                        }else{
                            $arr_data['payment_interest'] = $item['sum_amount'];
                        }
                        $payment_loan += $item['sum_amount'];
                        $arr_data['payment_loan'] = $item['sum_amount'];
                        continue;
                    }
                    if ($item['deduct_code'] == "SHARE") {
                        $payment_share = $item['sum_amount'];
                        $arr_data['payment_share'] = $item['sum_amount'];
                        continue;
                    }
                    $payment_other += $item['sum_amount'];
                    $arr_data['payment_other'] += $item['sum_amount'];
                }
            }else{

                $loan_arr = $this->loan_model->payment_month($member_id);
                $i=0;
                $sum_money_period=0;
                $sum_interest=0;
                foreach($loan_arr as $key => $value){
                    $loan_amount = $value['loan_amount_balance'];
                    $loan_id = $value['id'];
                    $date1 = $value['transaction_datetime'] == ''? date('Y-m-d',strtotime($value['approve_date'])) : date('Y-m-d',strtotime($value['transaction_datetime']));//วันคิดดอกเบี้ยล่าสุด
                    $date2 = date('Y-m-t',strtotime('+1 month',strtotime($date1)));
                    $interest_loan = 0;
                    $interest_loan = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_id, $date1, $date2);
                    $sum_money_period += $value['money_period_1'];     
                    $sum_interest +=  ROUND($interest_loan,2)   ;
                    $prev_loan_active_arr[$i]['money_period_1'] = $value['money_period_1'];
                    $i++;   
                }
                $arr_data['payment_deposit']  = $keep->getPaymentDeposit()[0]['total_amount'];
                $arr_data['sum_interest'] = $sum_interest;
                $arr_data['sum_loan_amount'] = $sum_money_period;
                $arr_data['payment_month'] = $arr_data['sum_loan_amount']+  $arr_data['payment_deposit'];

            }
            $arr_data['net_balance'] = $arr_data['income_per_month'] - ($payment_loan + $payment_deposit + $payment_share+$payment_other);

            //Get coop saving account.
            $saving_accounts = $this->db->select('account_id')->from('coop_maco_account')->where('mem_id = "'.$member_id.'" AND account_status = 0')->get()->result_array();
            $arr_data['saving_accounts'] = $saving_accounts;
        }

        if(isset($_GET['loan_id']) && $_GET['loan_id'] != ''){
            $contract = $curr_contract =  $this->contract->findContract($_GET['loan_id']);
            $arr_data['payment_per_period']= $curr_contract->money_period_1;
            $arr_data['default_loan'] = $this->contract->getLoanTypeByLoanNameId($contract->loan_type);
            $arr_data['default_loan_name'] = $contract->loan_type;
            $arr_data['rs_loan_name'] = $this->contract->getLoanNameByTypeId($arr_data['default_loan'] );
            $arr_data['petition_number'] = $contract->petition_number;
            $arr_data['transfer_account_id'] = $contract->transfer_account_id;

            //รายการหักภายนอก
            $external_deducts = $this->contract->getExternalDeduct($_GET['loan_id']);
            $arr_data['external_deducts'] = $external_deducts;

            $deduct_coop_saving = $this->db->select("account_id, amount")
                                            ->from('coop_loan_deduct_coop_saving')
                                            ->where("loan_id = '".$_GET['loan_id']."' and status = 1")
                                            ->get()->result_array();
            $arr_data['deduct_coop_savings'] = $deduct_coop_saving;
        }

        //รายการซื้อ
        $buyList = $this->contract->getLoanBuyList();
        $arr_data = array_merge($arr_data, $buyList);

        //รายการหัก
        $deductList = $this->contract->getLoanDeductList();
        $arr_data = array_merge($arr_data, $deductList);

        //รายการค่าใข้จ่าย
        $this->db->select('*')->from('coop_outgoing')->where('outgoing_status = 1')->order_by('outgoing_no asc');
        $arr_data['outgoings'] = $this->db->get()->result_array();
//echo '<pre>';print_r($arr_data['outgoings'] );exit;
        $setting['cut_off'] = 5;
        if(!empty($curr_contract->date_start_period)) {

            $date_first_receive = $curr_contract->date_start_period;
        }else{
            $date_first_receive = date('j') > $setting['cut_off'] ? date('Y-m-t', strtotime('+1 MONTH')) : date('Y-m-t');
        }
        $arr_data['first_receive_date'] = $date_first_receive;

         $this->libraries->template('loan_contract/index', $arr_data);
    }

    function change_loan_type(){
        $row = $this->contract->getLoanNameByTypeId($_POST['type_id']);
        $text_return = "<option value=''>เลือกทั้งหมด</option>";
        foreach($row as $key => $value){
            $text_return .= "<option value='".$value['loan_name_id']."'>".$value['loan_name']." ".$value['loan_name_description']."</option>";
        }
        echo $text_return;
        exit;
    }

    /**
     * ข้อมูลเงื่อนไขการกู้เงิน
     * @author adisak.sununtha@gmail.com
     */
    function term_condition(){
        header('Content-type: application/json; charset=utf-8');
        $res = $this->db->order_by('start_date', 'desc')->get_where('coop_term_of_loan', array('type_id'  => $_POST['type_id']), 1)->row_array();
        $res['credit_limit_org'] = $res['credit_limit'];

        $opt['member_id'] = $_POST['member_id'];
        $opt['net_balance'] = $_POST['net_balance'];
        $credit_limit = $this->condition_model->get_value_condition_of_loan($_POST['type_id'], 'credit_limit', $opt);
        if($credit_limit){
            $res['credit_limit'] = $credit_limit;
        }

        echo json_encode($res);
        exit;

    }

    /**
     * ข้อมูลสมาชิก
     * @author adisak.sununtha@gmail.com
     */
    function getShareValue(){
        if($this->input->post('member_id')){
            $member_id = $this->input->post('member_id');
            $info = $this->info->member($member_id);;
        }
    }

    /**
     * ข้อมูลบัญชีเงินฝาก
     * @author adisak.sununtha@gmail.com
     */
    public function getAccountList(){
        $member_id = $this->input->post('member_id');
        $result = array();
        $deposit =  $this->deposit->current($member_id);
        $result = $deposit->getBalanceList();
        $data = array('status' => 500, 'data' => array());
        if(sizeof($result)){
            $data['status'] = 200;
            $data['data'] = $result;
        }
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($data);
    }

    /**
     * ข้อมูลเงินกู้
     * @author adisak.sununtha@gmail.com
     */
    public function get_check_prev_loan(){
        $member_id = $this->input->post('member_id');
        $date = $this->input->post('createdatetime');
        $loan_id = $_POST['loan_id'];
        $result = array('status' => 500, 'data' => array());
        //$arr_createdatetime = explode('/',@$date);
        //$data_createdatetime = ($arr_createdatetime[2]-543)."-".$arr_createdatetime[1]."-".$arr_createdatetime[0];
        $data_createdatetime = $this->center_function->ConvertToSQLDate($date);
        $createdatetime = (@$_POST['createdatetime'] != '') ? @$data_createdatetime:date('Y-m-d');
        $data = $this->contract->getPrevLoan($member_id, $createdatetime, $loan_id);
        header('Content-type: application/json; charset=utf-8');
        if(sizeof($data)){
            $result['data'] = $data;
            $result['status'] = 200;
        }
        echo json_encode($result);
        exit;

    }

    /**
     * บันทึกคำขอ/สัญญาเงินกู้
     * @todo ต้องปรับให้เป็นอยู่ในโมเดล
     * @author adisak.sununtha@gmail.com
     */
    public function save_contract(){
        $income = $_POST['income'];

        $arr_createdatetime = explode('/',@$_POST['data']['coop_loan']['createdatetime']);
        $data_createdatetime = ($arr_createdatetime[2]-543)."-".$arr_createdatetime[1]."-".$arr_createdatetime[0];
        $createdatetime = (@$_POST['data']['coop_loan']['createdatetime'] != '')?@$data_createdatetime." ".date('H:i:s'):date('Y-m-d H:i:s');

        if(isset($_POST['data']['coop_loan'])){
            $_POST['data']['coop_loan'] = $this->contract->calc_period($_POST['data']['coop_loan']);
            $_POST['data']['coop_loan_period'] = $_POST['data']['coop_loan']['coop_loan_period'];
        }

        if(@$_POST['loan_id']==''){
            $data_insert = array();
            $data_insert['admin_id'] = @$_SESSION['USER_ID'];
            $data_insert['createdatetime'] = @$createdatetime;
            $data_insert['updatetimestamp'] = date('Y-m-d H:i:s');
            //$data_insert['contract_number'] = @$new_contact_number;
            $data_insert['contract_number'] = '';

            foreach(@$_POST['data']['coop_loan'] as $key => $value){
                if($key == 'date_period_1' || $key == 'date_period_2'){
                    if(!empty($value)){
                        $date_arr = explode('/',$value);
                        $value = ($date_arr[2])."-".$date_arr[1]."-".$date_arr[0];
                    }
                }
                if($key == 'loan_amount' || $key == 'money_period_1' || $key == 'money_period_2' || $key == 'salary'){
                    $value = str_replace(',','',@$value);
                }
                if($key == 'money_period_1'){
                    $data_insert['money_per_period'] = str_replace(',','',@$value);
                }

                //generate petition number
                if($key == 'petition_number'){
                    //$value  =  $this->contract->generatePetitionNumber($_POST['data']['coop_loan']['loan_type']);
                    $year = date("Y", strtotime($createdatetime))+543;
                    $value = $this->loan_libraries->createPetitionNumber($year, $createdatetime, $_POST['data']['coop_loan']['loan_type'] );
                }

                if($key != 'summonth_period_1' && $key != 'summonth_period_2'){
                    $data_insert[$key] = @$value;
                }

                if($key == 'loan_amount'){
                    $data_insert['loan_amount_balance'] = @$value;
                }
                $data_insert['loan_status'] = '0';

                if(in_array($key, array('coop_loan_period', 'date_receive_money', 'first_interest',
                    'last_period', 'total_loan_pri', 'max_period', 'interest_current_value'))){
                    unset($data_insert[$key]);
                }
                if($key == 'date_last_interest'){
                    $data_insert['date_last_interest'] = $this->center_function->ConvertToSQLDate($value);
                }
            }

            //echo "<pre>"; print_r($data_insert); exit;
            //add
            $this->db->insert('coop_loan', $data_insert);
            $loan_id = $this->db->insert_id();

            if(isset($_POST['data']['coop_loan_guarantee']) && sizeof($_POST['data']['coop_loan_guarantee']) >= 1) {
                foreach (@$_POST['data']['coop_loan_guarantee'] as $key => $value) {
                    if($value['type'] == 1){
                        $this->contract->personalGuarantee($loan_id, $value);
                    }else if($value['type'] == 2) {
                        $this->contract->shareGuarantee($loan_id, $value);
                    }else if($value['type'] == 3){
                        $this->contract->depositGuarantee($loan_id, $value);
                    }else if($value['type'] == 4){
                        $this->contract->realEstateGuarantee($loan_id, $value);
                    }

                }
            }
            //echo "_________________________<br>";
            foreach(@$_POST['data']['coop_loan_period'] as $key => $value){
                $data_insert = array();
                $data_insert['loan_id'] = @$loan_id;
                foreach($value as $key2 => $value2){
                    //$sql .= " ".$key2." = '".$value2."',";
                    $data_insert[$key2] = @$value2;
                }
                //add coop_loan_period
                $this->db->insert('coop_loan_period', $data_insert);
            }

        }else{

            $data_insert = array();
            $data_insert['admin_id'] = @$_SESSION['USER_ID'];
            $data_insert['createdatetime'] = @$createdatetime." ".date("H:i:s");
            if(@$_POST['updatetimestamp'] != '') {
                $data_insert['updatetimestamp'] = @$_POST['updatetimestamp'];
            }

            foreach(@$_POST['data']['coop_loan'] as $key => $value){

                if($key == 'date_period_1' || $key == 'date_period_2'){
                    if(!empty($value)){
                        $date_arr = explode('/',$value);
                        $value = ($date_arr[2])."-".$date_arr[1]."-".$date_arr[0];
                    }
                }

                if($key == 'loan_amount' || $key == 'money_period_1' || $key == 'money_period_2' || $key == 'salary'){
                    $value = str_replace(',','',@$value);
                }
                if($key == 'money_period_1'){
                    $data_insert['money_per_period'] = str_replace(',','',@$value);
                }

                //generate petition number
                if($key == 'petition_number'){
                    if(empty($value)) {
                        //$value =  $this->contract->generatePetitionNumber($_POST['data']['coop_loan']['loan_type']);
                        $year = date("Y", strtotime($createdatetime))+543;
                        $value = $this->loan_libraries->createPetitionNumber($year, $createdatetime, $_POST['data']['coop_loan']['loan_type']);
                    }
                }

                if($key != 'summonth_period_1' && $key != 'summonth_period_2'){
                    $data_insert[$key] = @$value;
                }

                if($key == 'loan_amount'){
                    $data_insert['loan_amount_balance'] = @$value;
                }
                $data_insert['loan_status'] = '0';

                if(in_array($key, array('coop_loan_period', 'date_receive_money', 'first_interest',
                    'last_period', 'total_loan_pri', 'max_period', 'interest_current_value'))){
                    unset($data_insert[$key]);
                }

                if($key == 'date_last_interest'){
                    $data_insert['date_last_interest'] = $this->center_function->ConvertToSQLDate($value);
                }
            }

            //edit coop_loan
            $this->db->where('id', @$_POST['loan_id']);
            $this->db->update('coop_loan', $data_insert);
            $loan_id = @$_POST['loan_id'];

            $this->db->where("loan_id", $loan_id );
            $this->db->delete("coop_loan_guarantee");

            $this->db->where("loan_id", $loan_id );
            $this->db->delete("coop_loan_guarantee_person");

            $this->db->where("loan_id", $loan_id );
            $this->db->delete("coop_loan_guarantee_real_estate");

            if(isset($_POST['data']['coop_loan_guarantee']) && sizeof($_POST['data']['coop_loan_guarantee']) >= 1) {
                foreach (@$_POST['data']['coop_loan_guarantee'] as $key => $value) {
                    if($value['type'] == 1){
                        $this->contract->personalGuarantee($loan_id, $value);
                    }else if($value['type'] == 2){
                        $this->contract->shareGuarantee($loan_id, $value);
                    }else if($value['type'] == 3){
                        $this->contract->depositGuarantee($loan_id, $value);
                    }else if($value['type'] == 4){
                        $this->contract->realEstateGuarantee($loan_id, $value);
                    }

                }
            }

            $this->db->where("loan_id", $loan_id );
            $this->db->delete("coop_loan_period");
            foreach(@$_POST['data']['coop_loan_period'] as $key => $value){
                $data_insert = array();
                $data_insert['loan_id'] = @$loan_id;
                foreach($value as $key2 => $value2){
                    $data_insert[$key2] = @$value2;
                }
                //add coop_loan_period
                $this->db->insert('coop_loan_period', $data_insert);
            }
        }

        //รายได้
        $this->db->where("loan_id", $loan_id);
        $this->db->delete("coop_income_loan_detail");
        if(!empty($income)) {
            foreach ($income as $key => $value) {
                $this->db->set("income_id", $key);
                $this->db->set("income_value", $value);
                $this->db->set("loan_id", $loan_id);
                $this->db->insert("coop_income_loan_detail");
            }
        }
        $this->db->where("loan_id", $loan_id );
        $this->db->delete("coop_loan_cost");

        $this->db->where("loan_id", $loan_id );
        $this->db->delete("coop_loan_cost_mod");

        // Loan Cost
        $this->db->where("loan_id", $loan_id );
        $this->db->delete("coop_loan_cost_mod");
        $data_insert = array();
        if(@$_POST['data']['coop_loan_cost']) {
            $index = 0;
            foreach (@$_POST['data']['coop_loan_cost'] as $key => $val){                
                if($key != 'OTH'){
                    $data_insert[$index]['loan_id'] = @$loan_id;
                    $data_insert[$index]['member_id'] = @$_POST['data']['coop_loan']['member_id'];
                    $data_insert[$index]['loan_cost_code'] = $key;
                    $data_insert[$index]['loan_cost_amount'] = str_replace(',','',$val);
                    $index++;
                } 
            }
            unset($index);
            $this->db->insert_batch('coop_loan_cost_mod', $data_insert);
        }

        //Loan Deduct
        //clear deduct profile
        $this->db->where("loan_id", $loan_id )->delete("coop_loan_deduct_profile");

        //clear deduct
        $this->db->where("loan_id", $loan_id )->delete("coop_loan_deduct");

        //clear loan deduct status
        $this->db->where("id", $loan_id );
        $this->db->update("coop_loan", array('deduct_status'=>'0'));

        //add deduct profile
        $data_insert = array();
        $data_insert['loan_id'] = @$loan_id;
        $data_insert['estimate_receive_money'] = str_replace(',', '', $_POST['data']['loan_deduct_profile']['estimate_receive_money']);
        $data_insert['pay_per_month'] = (@$_POST['data']['coop_loan']['summonth_period_2'] == '31') ? str_replace(',', '', $_POST['data']['coop_loan']['money_period_2']) : str_replace(',', '', $_POST['data']['coop_loan']['money_period_1']);
        $date_receive_money = explode('/', @$_POST['createdatetime']);
        $date_receive_money = (@$date_receive_money[2] - 543) . "-" . @$date_receive_money[1] . "-" . @$date_receive_money[0];
        $data_insert['date_receive_money'] = $date_receive_money;
        $data_insert['date_first_period'] = $_POST['data']['loan_deduct_profile']['date_first_period'];
        $data_insert['first_interest'] = str_replace(',', '', $_POST['data']['loan_deduct_profile']['first_interest']);
        $this->db->insert('coop_loan_deduct_profile', $data_insert);
        $loan_deduct_id = $this->db->insert_id();

        if(isset($_POST['data']['loan_deduct'] )) {

            $deduct_amount = 0;
            foreach ($_POST['data']['loan_deduct'] as $key => $value) {
                $data_insert = array();
                $data_insert['loan_id'] = @$loan_id;
                $data_insert['loan_deduct_list_code'] = $key;
                $data_insert['loan_deduct_amount'] = str_replace(',', '', $value);
                $data_insert['loan_deduct_id'] = @$loan_deduct_id;
                $this->db->insert('coop_loan_deduct', $data_insert);

                $deduct_amount += str_replace(',', '', $value);
            }
        }

        if($deduct_amount>0) {
            $this->db->where("id", $loan_id);
            $this->db->update("coop_loan", array('deduct_status' => '1'));
        }

        $this->db->where('loan_id',$loan_id);
        $this->db->delete('coop_loan_prev_deduct');
        if(!empty($_POST['prev_loan'])){
            foreach($_POST['prev_loan'] as $key => $value){
                if(@$value['id']!=''){
                    $data_insert = array();
                    $data_insert['loan_id'] = @$loan_id;
                    $data_insert['ref_id'] = $value['id'];
                    $data_insert['data_type'] = $value['type'];
                    $data_insert['pay_type'] = $value['pay_type'];
                    $data_insert['pay_amount'] = str_replace(',','',$value['amount']);
                    $data_insert['interest_amount'] = $value['interest'];
                    $this->db->insert('coop_loan_prev_deduct', $data_insert);
                }
            }
        }


        $member_id = $this->contract->findContract($loan_id)->member_id;
        $this->db->where("loan_id = '".$loan_id."' AND member_id='".$member_id."'");
        $this->db->delete("coop_life_insurance");

        $insurance_amount = str_replace(',','',$_POST['data']['coop_left_insurance']);
        $insurance_premium = str_replace(',', '', $_POST['data']['loan_deduct']['deduct_insurance']);
        if($insurance_amount > 0){
            $data_insert = array();
            $data_insert['loan_id'] = @$loan_id;
            $data_insert['member_id'] = @$member_id;
            $data_insert['insurance_year'] = date("Y")+543;
            //$data_insert['insurance_date'] = @$_POST['insurance_date'];
            $data_insert['contract_number'] = '';
            $data_insert['insurance_amount'] = $insurance_amount;
            $data_insert['insurance_premium'] = $insurance_premium;//การกู้
            $data_insert['admin_id'] = @$_SESSION['USER_ID'];
            $data_insert['createdatetime'] = @$createdatetime;
            $data_insert['insurance_status'] = 0;
            $this->db->insert('coop_life_insurance', $data_insert);
        }

        //External debt deduct.
        if(!empty($_POST['external_deduct_amount'])) {
            foreach($_POST['external_deduct_amount'] as $ex_index => $external_deduct_amount) {
                if(!empty($external_deduct_amount) && !empty($_POST['external_deduct_desc'][$ex_index])) {
                    $data_insert = array();
                    $data_insert['loan_id'] = $loan_id;
                    $data_insert['amount'] = str_replace(',','',$external_deduct_amount);
                    $data_insert['description'] = $_POST['external_deduct_desc'][$ex_index];
                    $data_insert['status'] = 1;
                    $data_insert['created_at'] = date('Y-m-d H:i:s');
                    $data_insert['updated_at'] = date('Y-m-d H:i:s');
                    $this->db->insert('coop_loan_deduct_external', $data_insert);
                }
            }
        }

        if(!empty($_POST['coop_saving_deduct_acc'])) {
            $data_insert = array();
            $data_insert['status'] = '2';
            $this->db->where('loan_id', $_POST['loan_id']);
            $this->db->update('coop_loan_deduct_coop_saving', $data_insert);

            $loan_id = @$_POST['loan_id'];
            $data_inserts = array();
            foreach($_POST['coop_saving_deduct_acc'] as $index => $account_id) {
                $amount = $_POST['coop_saving_deduct_amt'][$index];
                if(!empty($amount) && !empty($account_id)) {
                    $data_insert = array();
                    $data_insert['loan_id'] = $loan_id;
                    $data_insert['account_id'] = $account_id;
                    $data_insert['amount'] = str_replace(',','',$amount);
                    $data_insert['status'] = 1;
                    $data_insert['created_at'] = date('Y-m-d H:i:s');
                    $data_insert['updated_at'] = date('Y-m-d H:i:s');
                    $data_inserts[] = $data_insert;
                }
            }
            if(!empty($data_inserts)) {
                $this->db->insert_batch('coop_loan_deduct_coop_saving', $data_inserts);
            }
        }

        $this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
        header('location: '.base_url().'loan?member_id='.@$_POST['data']['coop_loan']['member_id']);
        exit;
    }

    /**
     * ข้อมูลการค้ำประกัน
     * @note ใช้ในการจัดการข้อมูลการค้ำประกัน
     * @author adisak.sununtha@gmail.com
     */
    public function getLoanGuarantee(){

        $loan_id = $this->input->post('loan_id');

        $data = array('status' => 500, 'msg' => '', 'data' => []);
        $guarantee =  $this->db->get_where('coop_loan_guarantee', array('loan_id' => $loan_id))->result_array();

        if(sizeof($guarantee)) {
            foreach ($guarantee as $key => $val) {
                if ($val['guarantee_type'] == "1") {
                    $data['data'][] = $this->contract->getGuaranteePerson($loan_id, $val['member_id']);
                } else if ($val['guarantee_type'] == "2") {
                    $data['data'][] = $this->contract->getGuaranteeShare($loan_id);
                } else if ($val['guarantee_type'] == "3") {
                    $data['data'][] = $this->contract->getGuaranteeDeposit($loan_id);
                } else if ($val['guarantee_type'] == "4") {
                    $data['data'][] = $this->contract->getGuaranteeRealEstate($loan_id);
                }
            }
            if(sizeof($data['data'])){
                $data['status'] = 200;
                $data['msg'] = 'success';
            }

        }else{
            $data['status'] = 400;
            $data['msg'] = 'empty';
        }

        header('Content-Type: application/json; Charset=utf-8;');
        echo json_encode($data);
        exit;
    }

    public function check_share_and_deposit(){
        $member_id = $_POST['member_id'];

        $deposit = $this->deposit->guaranteeAccount($member_id);
        $share = $this->share->current($member_id)->getCollectVal();

        header('content-type: application/json; charset: utf-8;');
        echo json_encode(array(
            'status' => 200,
            'total' => $share+(floor($deposit/100)*100)
        ));
    }

    public function update_income(){
        $data = $this->input->post();
        $this->info->saveInComing($data);
        echo json_encode($data);
        exit;

    }

    public function loan_change_contract_number() {

        $this->libraries->template('loan/loan_change_contract_number');
    }
    public function check_contract_number() {
        $contract_number = $_POST['contract_number'];
        $member_id = $_POST['member_id'];
        //echo $contract_number ;exit;
        $result = $this->Finance_libraries->check_contract_number($contract_number,$member_id);
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    public function check_contract_atm_number() {
        $contract_number = $_POST['contract_number'];
        $member_id = $_POST['member_id'];
        $result = $this->Finance_libraries->check_contract_atm_number($contract_number,$member_id);
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    public function check_petition_number() {
        $petition_number = $_POST['petition_number'];
        $member_id = $_POST['member_id'];
        $result = $this->Finance_libraries->check_petition_number($petition_number,$member_id);
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    public function check_petition_number_atm() {
        $petition_number = $_POST['petition_number'];
        $member_id = $_POST['member_id'];
        $result = $this->Finance_libraries->check_petition_atm_number($petition_number,$member_id);
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    public function change_contract_number() {
        $contract_number = $_POST['contract_number'];
        $new_contract_number = $_POST['new_contract_number'];
        $member_id = $_POST['member_id'];
        $result = $this->Finance_libraries->change_contract_number($contract_number,$new_contract_number,$member_id);
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    public function change_contract_atm_number() {
        $contract_number = $_POST['contract_number'];
        $new_contract_number = $_POST['new_contract_number'];
        $member_id = $_POST['member_id'];
        $result = $this->Finance_libraries->change_contract_atm_number($contract_number,$new_contract_number,$member_id);
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    public function change_petition_number() {
        $petition_number = $_POST['petition_number'];
        $new_petition_number = $_POST['new_petition_number'];
        $member_id = $_POST['member_id'];
        $result = $this->Finance_libraries->change_petition_number($petition_number,$new_petition_number,$member_id);
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    public function change_petition_atm_number() {
        $petition_number = $_POST['petition_number'];
        $new_petition_number = $_POST['new_petition_number'];
        $member_id = $_POST['member_id'];
        $result = $this->Finance_libraries->change_petition_atm_number($petition_number,$new_petition_number,$member_id);
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function check_condition(){
        $this->load->model("Guarantee", "guarantee");
        $this->load->library("unit_test");
        $x = 0;

        $x++;
        $amount = 100000;
        $date = date('Y-m-md');
        $loan_type = 2;
        $test = $this->guarantee->getGuarantee($amount, $date, $loan_type);
        $expected_result = 2;
        $test_name = 'Test-'.$x.': '.number_format($amount,2)." Test: ".$test." Except: ".$expected_result;
        echo $this->unit->run($test, $expected_result, $test_name);

        $x++;
        $amount = 200000;
        $date = date('Y-m-md');
        $loan_type = 2;
        $test = $this->guarantee->getGuarantee($amount, $date, $loan_type);
        $expected_result = 2;
        $test_name = 'Test-'.$x.': '.number_format($amount,2)." Test: ".$test." Except: ".$expected_result;
        echo $this->unit->run($test, $expected_result, $test_name);

        $x++;
        $amount = 3000000;
        $date = date('Y-m-md');
        $loan_type = 2;
        $test = $this->guarantee->getGuarantee($amount, $date, $loan_type);
        $expected_result = 4;
        $test_name = 'Test-'.$x.': '.number_format($amount,2)." Test: ".$test." Except: ".$expected_result;
        echo $this->unit->run($test, $expected_result, $test_name);

        $x++;
        $amount = 3500000;
        $date = date('Y-m-md');
        $loan_type = 2;
        $test = $this->guarantee->getGuarantee($amount, $date, $loan_type);
        $expected_result = 4;
        $test_name = 'Test-'.$x.': '.number_format($amount,2)." Test: ".$test." Except: ".$expected_result;
        echo $this->unit->run($test, $expected_result, $test_name);

        $x++;
        $amount = 4000000;
        $date = date('Y-m-md');
        $loan_type = 2;
        $test = $this->guarantee->getGuarantee($amount, $date, $loan_type);
        $expected_result = 5;
        $test_name = 'Test-'.$x.': '.number_format($amount,2)." Test: ".$test." Except: ".$expected_result;
        echo $this->unit->run($test, $expected_result, $test_name);

        $x++;
        $amount = 5000000;
        $date = date('Y-m-md');
        $loan_type = 2;
        $test = $this->guarantee->getGuarantee($amount, $date, $loan_type);
        $expected_result = 5;
        $test_name = 'Test-'.$x.': '.number_format($amount,2)." Test: ".$test." Except: ".$expected_result;
        echo $this->unit->run($test, $expected_result, $test_name);

    }

    public function get_guarantee_amount(){
        $this->load->model("Guarantee", "guarantee");
        $data = array('status' => 200);
        $amount = $this->input->post("amount");
        $_date = $this->input->post("createdatetime");
        $date = $this->center_function->ConvertToSQLDate($_date);
        $loan_type = $this->input->post("loan_type");
        $amount = $this->guarantee->getGuaranteeAmount($amount, $date, $loan_type);
        $data['amount'] = $amount;
        $this->output->set_content_type("application/json")->_display();
        echo json_encode($data);
        exit;
    }

    public function get_credit_limit(){

        $opt = array();
        $opt['member_id'] = $this->input->post('member_id');
        $opt['add_share'] = $this->input->post('deduct_share');
        try {
            $res['credit_limit'] = $this->condition_model->get_value_condition_of_loan(2, 'credit_limit', $opt);
            $res['status'] = "success";
        }catch (Exception $e){
            $res['status'] = "error";
            $res['msg'] = $e->getMessage();
        }
        $this->output->set_content_type("application/json")->_display();
        echo json_encode($res);
        exit;
    }


    public function test(){

        $opt = array();
        $opt['member_id'] = $this->input->get('member_id');
        $condition = $this->input->get('condition');
        $type = $this->input->get('type');
        try {
            $res[$condition] = $this->condition_model->get_value_condition_of_loan($type, $condition, $opt);
            $res['status'] = "success";
        }catch (Exception $e){
            $res['status'] = "error";
            $res['msg'] = $e->getMessage();
        }
        $this->output->set_content_type("application/json")->_display();
        echo json_encode($res);
        exit;
    }

    public function get_total_income(){
        $member_id =  $this->input->post("member_id");

        $res['status'] = "success";
        $res['total_income'] = $this->info->getIncomeById($member_id)['income'];
        $res['sql']  = $this->db->last_query();

        $this->output->set_content_type("application/json")->_display();
        echo json_encode($res);
        exit;
    }

    public function check_net_income()
    {
        $opt = array();

        $type_id = $this->input->post("loan_type", true);
        $opt['amount'] = $this->input->post("amount", true);
        $opt['member_id'] = $this->input->post("member_id", true);

        $res = array();
        $res['require_net'] = $this->condition_model->get_value_condition_of_loan($type_id, 'money_use_balance_baht', $opt);
        $res['status'] = "success";

        $this->output->set_content_type("application/json")->_display();
        echo json_encode($res);
        exit;
    }

    public function get_petition_number(){
       echo $this->loan_libraries->createPetitionNumber('2564', date('Y-m-d'), 1);
       echo "<br>";
       echo $this->loan_libraries->get_contract_number('2564', date('Y-m-d'), 1, 1);
    }

    public function installment_schedule()
    {
        $arr_data = array();
        $contract = [
            'loan_amount'=>$_GET['loan_amount'],
            'interest_per_year'=>$_GET['interest'],
            'createdatetime'=>$_GET['date_start'],
            'pay_amount'=>$_GET['pay_amount'],
            'period_amount'=>$_GET['period'],
            'money_period_1'=>$_GET['pay_amount'],
            'pay_type'=>$_GET['pat_type'],
            'member_id'=>$_GET['member_id']
        ];
        $sq = $this->contract->calc_period_by_period_per_year($contract);
        $arr_data['head'] = $contract;
        $arr_data['data'] = $sq;
        if($_GET['print']=='true'){
            $this->preview_libraries->template_preview('loan_contract/installment_schedule', $arr_data);
            echo "<script>window.print()</script>";
            return true;
        }
        echo  $this->load->view('loan_contract/installment_schedule_modal',$arr_data,true);
        return true;
    }

    public function get_json_expect_contract_number() {
        $result = array();
        $loan = $this->db->select('loan_type')->from("coop_loan")->where('id ="'.$_GET['id'].'"')->get()->row_array();

        $arr_date_approve = explode('/',@$_GET['date_approve']);
        $date_approve = ($arr_date_approve[2]-543)."-".$arr_date_approve[1]."-".$arr_date_approve[0];
        $date_approve_time = (@$_GET['date_approve'] != '')?@$date_approve." ".date('H:i:s'):date('Y-m-d H:i:s');
        $date_approve = (@$_GET['date_approve'] != '')?@$date_approve:date('Y-m-d');
        $year_approve = (@$_GET['date_approve'] != '')?($arr_date_approve[2]-543):date('Y');
        $month_approve = (@$_GET['date_approve'] != '')?($arr_date_approve[1]):date('m');

        $rs_month_account = $this->db->select('accm_month_ini')->from("coop_account_period_setting")->get()->row_array();
        $month_account = $rs_month_account['accm_month_ini'];

        if((int)$month_approve > (int)$month_account && (int)$month_account != 1){
            $year = ($year_approve+543)+1;
        }else{
            $year = ($year_approve+543);
        }
        $expect_contract_number = $this->loan_libraries->get_contract_number($year, $date_approve, $loan['loan_type'], 1);
        $result['contract_number'] = $expect_contract_number;

        echo json_encode($result);
    }
    public function add_loan_reason()
    {
        if($_POST){
            $loan_reason = $_POST['reason'];
            $this->db->insert('coop_loan_reason',['loan_reason'=>$loan_reason]);
            $insert_id = $this->db->insert_id();
            $R= [
                'result'=>'true',
                'text'=>$loan_reason,
                'ID'=>$insert_id
            ];
            echo json_encode($R);
        }
    }
}
