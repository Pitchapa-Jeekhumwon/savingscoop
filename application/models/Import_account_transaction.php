<?php


class Import_account_transaction extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Finance_libraries", "Finance_libraries");
        $this->load->model("Cashier_loan_model", "cashier_loan");
        $this->load->model('Atm_calculator_model', 'ATMCalc');
        $this->load->model("Deposit_modal", "deposit_modal");
    }

    private $_date = null;

    public function execute($date = ""){
        $date_start= explode('/', $_POST['date']);
        $date = ($date_start[2]-543).'-'.$date_start[1].'-'.$date_start[0];

        $this->_date = $date." ".date("16:00:00");

        $data = self::loadData($_POST['id']);

        $res = array();
        $rcdata = array();
        foreach ($data as $key => $item){
            $item['date_cal'] = $this->_date;
            $res[] = self::prepare($item);
            $rcdata[] = self::preReceipt($item);
        }

        $this->db->insert_batch("temp_receipt",$rcdata);
        $this->db->insert_batch("coop_account_transaction", $res);
        $run_bal = self::Run_st($date);
        $run_int = self::Run_int($date);
        return $res;
    }

    private function loadData($file_id, $limit = null){

       return $this->db->get_where('temp_import_account_transaction',"file_id = '".$file_id."'", $limit)->result_array();

    }

    private function loadAccount($accountId){

        $this->db->select("DATE(t1.transaction_time) AS transaction_time,
            t1.old_acc_int,
            t1.accu_int_item,
            t1.transaction_balance,
            t2.type_id,
            t2.account_status"
        );
        $this->db->from("coop_account_transaction as t1");
        $this->db->join("coop_maco_account as t2", "t1.account_id=t2.account_id", "inner");
        $this->db->where("t1.account_id = '{$accountId}'  AND t2.account_status = '0'");
        $this->db->order_by("t1.transaction_time DESC,t1.transaction_id DESC ");
        $this->db->limit(1);

        return $this->db->get()->row_array();
    }

    private function last_transaction($account_id){
        return $this->db->select("DATE(t1.transaction_time) AS transaction_time,
                t1.old_acc_int,
                t1.accu_int_item,
                t1.transaction_balance,
                t2.type_id,
                t2.account_status")
            ->from("coop_account_transaction  AS t1")
            ->join("coop_maco_account AS t2","t1.account_id = t2.account_id	","inner")
            ->where("t1.account_id = '{$account_id}'  AND t2.account_status = '0'")
            ->order_by('t1.transaction_time DESC,t1.transaction_id DESC ')
            ->limit(1)
            ->get()->row_array();
    }

    private function prepare($data = array()){

        $acc_int_arr = $this->deposit_modal->cal_accu_int($data);

        $last = self::last_transaction($data['account_id']);

        $acc_id= explode('-', $data['account_id']);
        $account_id = $acc_id[0].$acc_id[1].$acc_id[2];
        $data_insert = array();
        $data_insert['transaction_time'] = $this->_date;
        $data_insert['transaction_list'] =  $data['transaction_withdrawal'] == 0 ? '040' : '150';
        $data_insert['transaction_withdrawal'] =  !empty($data['transaction_withdrawal']) ? number_format($data['transaction_withdrawal'], 2, '.', '') : "";
        $data_insert['transaction_deposit'] =  !empty($data['transaction_deposit'])?number_format($data['transaction_deposit'], 2, '.', ''): "";
        $data_insert['transaction_balance'] =  !empty(($last['transaction_balance'] + $data['transaction_deposit'] - $data['transaction_withdrawal']))?number_format($last['transaction_balance'] + $data['transaction_deposit'] - $data['transaction_withdrawal'], 2, '.', ''):"";
        $data_insert['user_id'] =  "IMP";
        $data_insert['account_id'] =  $account_id;
        $data_insert['old_acc_int'] =  $acc_int_arr['old_acc_int'];
        $data_insert['accu_int_item'] =  $acc_int_arr['accu_int_item'];


        return $data_insert;
    }

    private function preReceipt($data = array()){

        $data_insert1 = array();
        $data_insert1['member_id'] = $data['member_id'];
        $data_insert1['share_month'] =  $data['transaction_withdrawal'];
        $data_insert1['loan_normal_principal'] =  " ";
        $data_insert1['loan_normal_interest'] = " ";
        $data_insert1['file_id'] =  $data['file_id'];
        $data_insert1['status'] =  1 ;

        return $data_insert1;
    }
    private function upStatus($data =""){

        $data_insert1 = array();
        $data_insert1['status'] =  '1';

        return $data_insert1;
    }


    private function loadReceiptTemp(){
        return $this->db->select(array('member_id as member_id',
         'IFNULL(share_month, 0) as share_month',
         'IFNULL(loan_normal_principal, 0) as loan_normal_principal',
         'IFNULL(loan_normal_interest, 0) as loan_normal_interest',
         '(IFNULL(share_month, 0)+IFNULL(loan_normal_principal, 0)+IFNULL(loan_normal_interest, 0)) as total'))
            ->from('temp_receipt')
            ->get()->result_array();
    }

    private function getLoanType($typeId){
        return $this->db->select(array('loan_name_id', 'loan_type_id', 'loan_name'))
            ->from('coop_loan_name t1')
            ->join('coop_loan_type t2', 't1.loan_type_id=t2.id', 'inner')
            ->where(array('t2.id' => $typeId))->get()->result_array();
    }

    private function findNormalLoan($member){
        $_in = array_column(self::getLoanType(2),'loan_name_id');
        return $this->db->select(array('id', 'loan_type', 'contract_number', 'loan_amount_balance'))
            ->from("coop_loan")
            ->where(array('loan_status' => 1, 'member_id' => $member))
            ->where_in('loan_type', $_in)
            ->get()->row_array();
    }

    public function executeReceipt(){
        $date_start= explode('/', $_POST['date']);
        $date = ($date_start[2]-543).'-'.$date_start[1].'-'.$date_start[0];

        $this->_date = $date." ".date("H:i:s");
        $data = self::prepareReceipt();
        foreach ($data as $key => $item){
             $receipt = self::getReceiptNumber($date);
             self::addReceipt($item, $receipt, $date);
             foreach ($item['receipt'] as $index => $value){
                 self::addReceiptDetail($value, $receipt, $date);
                 self::addFinanceTransaction($value, $receipt, $date);
                 if($value['loan_id']){
                     self::addLoanTransaction($value, $receipt, $date);
                 }else{
                     self::addShare($value, $receipt, $date);
                 }

             }
            echo "\n\n";
        }
    }

    public function prepareReceipt(){
        $res = array();
        $data = self::loadReceiptTemp();
        $k = 0;
        foreach ($data as $key => $item){
            if($item['total'] > 0){
                $i=0;
                if($item['share_month'] > 0){

                    $st = array();
                    $st['member_id'] = $item['member_id'];
                    $st['amount'] = $item['share_month'];
                    $st['principal'] = $item['share_month'];
                    $st['interest'] = 0;
                    $st['account_list'] = 16;
                    $st['loan_amount_balance'] = self::findShareLast($item['member_id'])['share_collect_value']+ $item['share_month'];
                    $res[$k]['receipt'][$i] = $st;
                    $i++;
                }

                if($item['loan_normal_principal'] > 0){
                    $st = array();
                    $loan = self::findNormalLoan($item['member_id']);
                    $st['loan_id'] = $loan['id'];
                    $st['member_id'] = $item['member_id'];
                    $st['principal'] = $item['loan_normal_principal'];
                    $st['interest'] = $item['loan_normal_interest'];
                    $st['loan_amount_balance'] = $loan['loan_amount_balance']-$item['loan_normal_principal'];
                    $st['amount'] = $item['loan_normal_principal']+$item['loan_normal_interest'];
                    $st['account_list'] = 15;
                    $res[$k]['receipt'][$i] = $st;
                    $i++;
                }

                $res[$k]['total'] = $item['total'];
                $res[$k]['member_id'] = $item['member_id'];
                $k++;
            }
        }
        return $res;
    }

    /**
     * @param $date "format Y-m-d H:i:s"
     * @return string
     */
    private function getReceiptNumber($date = null){
        if($date == null){
          $date = date('Y-m-d H:i:s');
        }
        //get receipt setting data
        $receipt_format = 1;
        $receipt_finance_setting = $this->db->select("*")->from("coop_setting_finance")->where("name = 'receipt_cashier_format' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        if(!empty($receipt_finance_setting)) {
            $receipt_format = $receipt_finance_setting['value'];
        }

        if($receipt_format == 1) {
            $yymm = (date("Y") + 543) . date("m");
            $mm = date("m");
            $yy = (date("Y") + 543);
            $yy_full = (date("Y") + 543);
            $yy = substr($yy, 2);
            $this->db->select('*');
            $this->db->from('coop_receipt');
            $this->db->where("receipt_id LIKE '" . $yy_full . $mm . "%'");
            $this->db->order_by("receipt_id DESC");
            $this->db->limit(1);
            $row = $this->db->get()->result_array();

            if (!empty($row)) {
                $id = (int)substr($row[0]["receipt_id"], 6);
                $receipt_number = $yymm . sprintf("%06d", $id + 1);
            } else {
                $receipt_number = $yymm . "000001";
            }
        } else {
            $receipt_number = $this->Finance_libraries->generate_cashier_receipt_id($receipt_format, $date);
        }

        return $receipt_number;
    }

    private function addReceipt($data = array(), $receipt = "", $date = ""){
        $data_insert['sumcount'] = number_format($data['total'], 2, '.', '');

        $data_insert['member_id'] = $data['member_id'];
        $data_insert['receipt_id'] = $receipt;
        $data_insert['receipt_datetime'] = $date;
        $data_insert['admin_id'] = "IMP";
        $data_insert['pay_type'] = "1";
        $this->db->set($data_insert);
        //echo $this->db->get_compiled_insert('coop_receipt')."\n";
        $this->db->insert('coop_receipt');
    }

    private function addReceiptDetail($data = array(), $receipt = "", $date = ""){

        $data_insert = array();
        $data_insert['receipt_id'] = $receipt;
        $data_insert['receipt_list'] = $data['account_list'];
        $data_insert['receipt_count'] = number_format($data['amount'], 2, '.', '');
        $this->db->set($data_insert);
        //echo $this->db->get_compiled_insert('coop_receipt_detail')."\n";
        $this->db->insert('coop_receipt_detail');
    }

    private function addFinanceTransaction($data = array(), $receipt = "", $date = ""){
        if($data['loan_id']) {
            $transaction_text = $this->Finance_libraries->generate_loan_receipt_text_cashier($data['loan_id'], $data['account_list']);
        }else{
            $transaction_text = self::getAccountList($data['account_list']);
        }
        $data_insert = array();
        $data_insert['member_id'] = $data['member_id'];
        $data_insert['receipt_id'] = $receipt;
        $data_insert['loan_id'] = $data['loan_id'];
        $data_insert['deduct_type'] = 'all';
        $data_insert['account_list_id'] = $data['account_list'];
        $data_insert['principal_payment'] = number_format($data['principal'], 2, '.', '');
        $data_insert['interest'] = number_format($data['interest'], 2, '.', '');
        $data_insert['loan_interest_remain'] = number_format(0, 2, '.', '');
        $data_insert['total_amount'] = $data['amount'];
        $data_insert['loan_amount_balance'] = number_format($data['loan_amount_balance'], 2, '.', '');
        $data_insert['payment_date'] = $date;
        $data_insert['createdatetime'] = $date;
        $data_insert['transaction_text'] = $transaction_text;
        $this->db->set($data_insert);
        //$this->db->get_compiled_insert('coop_finance_transaction')."\n";
        $this->db->insert('coop_finance_transaction');

    }

    private function prepareLoanTransaction($data = array(), $receipt = "", $date = ""){
        $data = self::findLoanDataById($data['loan_id']);
    }

    private function findLoanDataById($id){
        $this->db->select(array('loan_amount_balance', 'contract_number'));
        $this->db->from('coop_loan');
        $this->db->where("id = '{$id}'");
        return $this->db->get()->row_array();
    }

    private function getSharePeriod($memberId){
        $this->db->select(array('share_period'));
        $this->db->from('coop_mem_share');
        $this->db->where("member_id = '".$memberId."' AND (share_status = '1' OR share_status = '5') AND (share_period IS NOT NULL AND share_period <> 0)");
        $this->db->order_by("share_date DESC, share_id DESC");
        $this->db->limit(1);
        return $this->db->get()->row_array()['share_period'];
    }

    private function findShareLast($memberId){
        $this->db->select(array('*'));
        $this->db->from('coop_mem_share');
        $this->db->where("member_id = '".$memberId."' AND (share_status = '1' OR share_status = '5')");
        $this->db->order_by("share_date DESC, share_id DESC");
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    private function addShare($data = array(), $receipt = "", $date = ""){

        $memberId = $data['member_id'];
        $payAmount = $data['amount'];
        $share_value = 10;

        $share_period_max = self::getSharePeriod($memberId);
        $row_share = self::findShareLast($memberId);

        $interest = '';
        $balance = $row_share['share_collect_value'] + $payAmount;
        $period_count = $share_period_max+1;
        $transaction_text = !empty($share_transaction_text) ? $share_transaction_text['value'] : 'ชำระเงินค่าหุ้นรายเดือน';

        $data_insert = array();
        $data_insert['member_id'] = $memberId;
        $data_insert['admin_id'] = "IMP";
        $data_insert['share_type'] = 'SB';
        $data_insert['share_date'] = $date;
        $data_insert['share_payable'] = @$row_share['share_collect'];
        $data_insert['share_payable_value'] = @$row_share['share_collect_value'];
        $data_insert['share_early'] = ($payAmount/$share_value);
        $data_insert['share_early_value'] = $payAmount;
        $data_insert['share_collect'] = ($balance/$share_value);
        $data_insert['share_collect_value'] = $balance;
        $data_insert['share_value'] = $share_value;
        $data_insert['share_status'] = '1';
        $data_insert['share_bill'] = $receipt;
        $data_insert['share_bill_date'] = $date;
        $data_insert['share_period'] = $period_count;
        $this->db->set($data_insert);
        //echo $this->db->insert('coop_mem_share') ."\n";
        $this->db->insert('coop_mem_share');
    }

    private function addLoanTransaction($data = array(), $receipt = "", $date = ""){

        $loan_transaction = array();
        $loan_transaction['loan_id'] = $data['loan_id'];
        $loan_transaction['loan_amount_balance'] = $data['loan_amount_balance'];
        $loan_transaction['transaction_datetime'] = $date;
        $loan_transaction['receipt_id'] = $receipt;
        $loan_transaction['interest'] =  $data['interest'];
        $this->loan_libraries->loan_transaction_arrears('PL',$loan_transaction);

        $data_insert = array();
        $data_insert['date_last_interest'] = $date;
        $data_insert['loan_amount_balance'] = $data['loan_amount_balance'];
        $this->db->where('id', $data['loan_id']);
        $this->db->set($data_insert);
       // echo $this->db->get_compiled_update('coop_loan')."\n";
        $this->db->update('coop_loan');
    }

    private function getAccountList($account_list){
        $this->db->select(array('account_list'));
        $this->db->from('coop_account_list');
        $this->db->where("account_id = '" . $account_list . "'");
        $this->db->limit(1);
        return $this->db->get()->row_array()['account_list'];
    }

    public function uploadTempTransaction($files){
        error_reporting(0);
        ini_set('display_errors', 0);
        if ($files['file']['name'] != '') {
            $output_dir = $_SERVER["DOCUMENT_ROOT"] . PROJECTPATH . "/assets/document/import_excel/";
            if (!@mkdir($output_dir, 0, true)) {
                @chmod($output_dir, 0777);
            } else {
                @chmod($output_dir, 0777);
            }

            $value_file = $files['file']['name'];
            $fileName = array();
            $list_dir = array();
            $cdir = scandir($output_dir);
            foreach ($cdir as $key => $value) {
                if (!in_array($value, array(".", ".."))) {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                        $list_dir[$value] = dirToArray(@$dir . DIRECTORY_SEPARATOR . $value);
                    } else {
                        if (substr($value, 0, 8) == date('Ymd')) {
                            $list_dir[] = $value;
                        }
                    }
                }
            }
            $explode_arr = array();
            foreach ($list_dir as $key => $value) {
                $task = explode('.', $value);
                $task2 = explode('_', $task[0]);
                $explode_arr[] = $task2[1];
            }
            $max_run_num = sprintf("%04d", count($explode_arr) + 1);
            $explode_old_file = explode('.', $files["file"]["name"]);
            $new_file_name = date('Ymd') . "_" . $max_run_num . "." . $explode_old_file[(count($explode_old_file) - 1)];
            if (!is_array($files["file"]["name"])) {
                $fileName['file_name'] = $new_file_name;
                $fileName['file_type'] = $files["file"]["type"];
                $fileName['file_old_name'] = $files["file"]["name"];
                $fileName['file_path'] = $output_dir . $fileName['file_name'];
                move_uploaded_file($files["file"]["tmp_name"], $output_dir . $fileName['file_name']);
                //unlink($fileName['file_path']);
                return $fileName;
            }
        }
        return null;
    }

    public function getSheetName($files){

        $types = array('Excel2007', 'Excel5', 'Excel2016');
        foreach ($types as $type) {
            $reader = PHPExcel_IOFactory::createReader($type);
            if ($reader->canRead($files['file_path'])) {
                $valid = true;
                break;
            }
        }

        if (!empty($valid)) {
            $objPHPExcel = PHPExcel_IOFactory::load($files['file_path']);
            return $objPHPExcel->getSheetNames()[0];
        }
        return null;
    }

    public function Run_st($date=""){
        $this->db->select(array('account_id'));
        $this->db->from('temp_import_account_transaction');
        $this->db->where("date_data LIKE '%".$date."%' ");
        $temp_import = $this->db->get()->result_array();
        foreach($temp_import as $key=>$value){
            $acc_id= explode('-', $value['account_id']);
            $account_id = $acc_id[0].$acc_id[1].$acc_id[2];
            $temp_import[$key]['account_id']= $account_id ;

            $date_b = explode('-', $date);
            $date_before = $date_b[0].$date_b[1].$date_b[2];

            $date_b = explode('-', $date);
            if($date_b[1]=='01'){
                $date_b0 =  ($date_b[0]-1);
                $date_before = $date_b0."-12-15";
            }
            else{$date_b[1]=$date_b[1]-1;
                $date_before = $date_b[0]."0".$date_b[1]."15";
            }

            $date_month_before = date('Y-m-t', strtotime($date_before));
            $this->db->select('account_id,transaction_time');
            $this->db->from('coop_account_transaction');
            $this->db->where("transaction_time LIKE  '%".$date_month_before."%' AND account_id ='".$account_id."'");
            $this->db->order_by("transaction_time DESC") ;
            $this->db->limit(1);
            $data[$key]= $this->db->get()->row_array();

            if(empty($data[$key])){
                $this->db->select('account_id,transaction_time');
                $this->db->from('coop_account_transaction');
                $this->db->where("transaction_time LIKE  '%".$date."%' AND account_id ='".$account_id."'");
                $data[$key]= $this->db->get()->row_array();
            }
        }
        foreach ($data as $key1 => $val1 ) {
            $account_id = $val1['account_id'];
            $transaction_time = $date_month_before;
            //echo $transaction_time;exit;
            $where = "1=1";
            if (@$account_id != '') {
                $where .= " AND account_id = '{$account_id}'";
            }
            $sql1 = "	SELECT
						account_id,
						mem_id,
						created,
						account_status,
						close_account_date
					FROM
						coop_maco_account 
					WHERE
						{$where}
					ORDER BY
						account_id ASC";
            $row1 = $this->db->query($sql1)->result_array();
            if (!empty($row1)) {
                foreach ($row1 AS $key1 => $val1) {
                    $account_id = $val1['account_id'];
                    //$transaction_time = $val1['created'];
                    //$account_id = '1A00025';
                    //$transaction_time = '2020-12-22';
                    $affected_rows = 0;
                    $sql2 = "	SELECT
								account_id,
								transaction_time,
								transaction_id,
								transaction_list,
								old_acc_int,
								accu_int_item
							FROM
								coop_account_transaction 
							WHERE
								transaction_time > '{$transaction_time}' 
								AND account_id = '{$account_id}'
							ORDER BY
								account_id ASC , transaction_time ASC";
                    $row2 = $this->db->query($sql2)->result_array();
                    $old_acc_int = 0;
                    if (!empty($row2)) {
                        foreach ($row2 AS $key2 => $val2) {
                            $data_update = array();
                            $data_update['date'] = $transaction_time;
                            $data_update['account_id'] = $account_id;
                            $this->update_st->Run_import_excel_update($data_update);
                            if ($this->db->affected_rows()) {
                                $affected_rows++;
                            }
                        }
                    }

                }
            }


        }
        return null;
    }

    public function Run_int($date="")
    {
        $this->db->select(array('account_id'));
        $this->db->from('temp_import_account_transaction');
        $this->db->where("date_data LIKE '%".$date."%' ");
        $temp_import = $this->db->get()->result_array();
        foreach($temp_import as $key=>$value){
            $acc_id= explode('-', $value['account_id']);
            $account_id = $acc_id[0].$acc_id[1].$acc_id[2];
            $temp_import[$key]['account_id']= $account_id ;

            $date_b = explode('-', $date);
            $date_before = $date_b[0].$date_b[1].$date_b[2];

            $date_b = explode('-', $date);
            if($date_b[1]=='01'){
                $date_b0 =  ($date_b[0]-1);
                $date_before = $date_b0."-12-15";
            }
            else{$date_b[1]=$date_b[1]-1;
                $date_before = $date_b[0]."0".$date_b[1]."15";
            }

            $date_month_before = date('Y-m-t', strtotime($date_before));
            $this->db->select('account_id,transaction_time');
            $this->db->from('coop_account_transaction');
            $this->db->where("transaction_time LIKE  '%".$date_month_before."%' AND account_id ='".$account_id."'");
            $this->db->order_by("transaction_time DESC") ;
            $this->db->limit(1);
            $data[$key]= $this->db->get()->row_array();

            if(empty($data[$key])){
                $this->db->select('account_id,transaction_time');
                $this->db->from('coop_account_transaction');
                $this->db->where("transaction_time LIKE  '%".$date."%' AND account_id ='".$account_id."'");
                $data[$key]= $this->db->get()->row_array();
            }
        }
//echo'<pre>';print_r($data);exit;
        foreach($data as $key => $val) {
            $sql1 = "	SELECT
						account_id,
						mem_id,
						created,
						account_status,
						close_account_date
					FROM
						coop_maco_account 
					WHERE
						account_id = '".$val['account_id']."'
					ORDER BY
						account_id ASC";
            $row1 = $this->db->query($sql1)->result_array();
            if (!empty($row1)) {
                foreach ($row1 AS $key1 => $val1) {
                    $account_id = $val1['account_id'];
                    //$transaction_time = $val1['created'];
                    //$account_id = '1A00025';
                    //$transaction_time = '2020-12-22';
                    $transaction_time = $date;

                    $affected_rows = 0;
                    $sql2 = "	SELECT
								account_id,
								transaction_time,
								transaction_id,
								transaction_list,
								old_acc_int,
								accu_int_item
							FROM
								coop_account_transaction 
							WHERE
								transaction_time > '{$transaction_time}' 
								AND account_id = '{$account_id}'
							ORDER BY
								account_id ASC , transaction_time ASC";
                    $row2 = $this->db->query($sql2)->result_array();
                    $old_acc_int = 0;
                    if (!empty($row2)) {
                        foreach ($row2 AS $key2 => $val2) {
                            //@start เรียกใช้ ข้อมูล ดอกเบี้ยสะสม
                            $data_cal = array();
                            $data_cal['account_id'] = $val2['account_id'];
                            $data_cal['date_cal'] = $val2['transaction_time'];
                            $data_cal['date_start_cal'] = $val2['transaction_time'];
                            $data_cal_accu_int = $this->deposit_modal->cal_accu_int($data_cal);
//echo '<pre>'; print_r($data_cal); echo '</pre>';

                            $transaction_id = $val2['transaction_id'];

                            //if($val2['transaction_list'] == 'INT'){
                            if (in_array($val2['transaction_list'], array('INT', 'IN'))) {
                                $old_acc_int = 0;
                                $data_cal_accu_int['old_acc_int'] = 0;
                                $data_cal_accu_int['accu_int_item'] = 0;
                                echo $val2['transaction_list'] . '<br>';
                            }
                            //echo '<pre>'; print_r($data_cal_accu_int); echo '</pre>';exit;
                            if ($key2 == 0) {
                                $old_acc_int = $data_cal_accu_int['old_acc_int'];
                            } else {
                                $old_acc_int += $data_cal_accu_int['accu_int_item'];
                            }
//echo '<pre>'; print_r($data_cal_accu_int); echo '</pre>';
                            $old_acc_int = number_format($old_acc_int, 2, '.', '');
                            $data_insert['old_acc_int'] = $old_acc_int;
                            $data_insert['accu_int_item'] = $data_cal_accu_int['accu_int_item'];
                            $this->db->where('transaction_id', $transaction_id);
                            $this->db->update('coop_account_transaction', $data_insert);
                            if ($this->db->affected_rows()) {
                                $affected_rows++;
                            }
                        }

                    }

                }
            }
        }
        return null;
    }

}