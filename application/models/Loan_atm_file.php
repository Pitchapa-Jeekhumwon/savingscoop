<?php
/**
 * Created by PhpStorm.
 * User: macmini2
 * Date: 2019-11-27
 * Time: 09:35
 */

class Loan_atm_file extends Text_file
{

    public $coop_type_id = "007083";

    public function __construct()
    {
        parent::__construct();
    }

    public function test(){

        $new = self::get_new_contract();
        foreach ($new as $key => $contract) {

            $key_options = array(
                'coop_type_id' => $this->coop_type_id,
                'coop_customer_id' => $contract['member_id']
            );
            $this->set_keys($key_options);
            $this->set_control(1, 'A');

            $info = self::find_account_info($contract['member_id']);

            $obj = array();
            $obj['account_name_th'] = self::get_prename($info['prename_id']).$info['firstname_th'] . " " . $info['lastname_th'];
            $obj['account_name_en'] = $info['firstname_en'] . " " . $info['lastname_en'];
            $obj['gender'] = $info['sex'] == "M" ? 1 : 2;
            $obj['birth_day'] = str_replace('-', '', $info['birthday']);
            $obj['card_type'] = 1;
            $obj['card_number'] = $info['id_card'];
            $obj['account_no'] = $contract['account_id'];
            echo $this->atm_new_account_record($obj);
        }

    }

    public function new_contract_info($date=''){
        $new = self::get_new_contract($date);
        $txt = "";
        foreach ($new as $key => $contract) {

            $key_options = array(
                'coop_type_id' => $this->coop_type_id,
                'coop_customer_id' => $contract['member_id']
            );
            $this->set_keys($key_options);
            $this->set_control(1, 'A');

            $info = self::find_account_info($contract['member_id']);
			$address = self::find_address($contract['member_id']);

            $obj = array();
            $obj['account_name_th'] =  self::get_prename($info['prename_id']).$info['firstname_th'] . " " . $info['lastname_th'];
            $obj['account_name_en'] = $info['firstname_en'] . " " . $info['lastname_en'];
            $obj['gender'] = $info['sex'] == "M" ? 1 : 2;
            $obj['birth_day'] = str_replace('-', '', $info['birthday']);
            $obj['card_type'] = 1;
            $obj['card_number'] = $info['id_card'];
            $obj['account_no'] = $contract['account_id'];
			$obj['address'] = $address;
            $txt .= $this->atm_new_account_record($obj);
        }
        return $txt;
    }

    public function change_contract_info($date=''){

    }

    public function delete_contract_info($date=''){
		$del = self::get_delete_contract($date);
        $txt = "";
        foreach ($del as $key => $contract) {

            $key_options = array(
                'coop_type_id' => $this->coop_type_id,
                'coop_customer_id' => $contract['member_id']
            );
            $this->set_keys($key_options);
            $this->set_control(1, 'D');

            $info = self::find_account_info($contract['member_id']);
			$address = self::find_address($contract['member_id']);

            $obj = array();
            $obj['account_name_th'] =  self::get_prename($info['prename_id']).$info['firstname_th'] . " " . $info['lastname_th'];
            $obj['account_name_en'] = $info['firstname_en'] . " " . $info['lastname_en'];
            $obj['gender'] = $info['sex'] == "M" ? 1 : 2;
            $obj['birth_day'] = str_replace('-', '', $info['birthday']);
            $obj['card_type'] = 1;
            $obj['card_number'] = $info['id_card'];
            $obj['account_no'] = $contract['account_id'];
            $obj['address'] = $address;
            $txt .= $this->atm_new_account_record($obj);
        }
        return $txt;
    }

    public function get_new_contract($date=''){
	
        $_where = "atm_file_write_datetime is null AND loan_atm_status = 1 AND activate_status= 0";
		if($date != ''){
			$_where .= " AND DATE(atm_file_last_access) = '{$date}'";
		}
        return $this->db->select("*")->from('coop_loan_atm')->where($_where)->order_by('loan_atm_id', 'asc')
        ->get()->result_array();
    }

    public function find_account_info($member_id){
        return $this->db->select('*')->from('coop_mem_apply')->where('member_id', $member_id)->get()->row_array();
    }

    public function get_prename($id){
        if(empty($id)){
            return '';
        }
        return $this->db->select('prename_short')->from('coop_prename')->where('prename_id', $id)->get()->row()->prename_short;
    }

    public function read_file_DCSBAD60($file_name){
        if(file_exists($file_name)) {
            $data = self::reader_DCSBAD60($file_name);
            unset($data[0], $data[1], $data[2], $data[3]);
            $items = array();
            $index = 0;
            foreach ($data as $item) {
                $item = strtolower($item);
                $items[$index]['status'] = trim(substr($item, 1, 9));
                $items[$index]['member'] = substr(substr($item, 16, 10), 4, 6);
                $items[$index]['type'] = trim(substr($item, 30, 31));
                $items[$index]['action'] = trim(substr($item, 61, 31));
                $index++;
            }
            return $items;
        }
        return [];
    }

    public function get_date_file_DCSBAD60($file){
        if(file_exists($file)) {
            $data = self::reader_DCSBAD60($file);
            $str_date = str_replace('/', '-', mb_substr($data[2], 112, 10));
            $str_time = str_replace(".", ":", mb_substr($data[2], 128, 5)).":00";
            return date('Y-m-d H:i:s', strtotime($str_date." ".$str_time));
        }else{
            return 'undefined';
        }
    }

    public function reader_DCSBAD60($file_name){

        $file = self::tis620_to_utf8(file_get_contents($file_name));

        $all =  mb_strlen($file, 'UTF-8');
        $data = array();
        for($row = 0; $row < ceil($all/ 133)-1; $row++){
            $line = mb_substr($file, $row*135, 133, 'UTF-8');
            if(strlen($line)){
                $data[$row] = $line;
            }
        }
        return $data;
    }

    public function get_data_verify_bank($file_name){
        return $this->db->select('*')->from('coop_loan_verify_bank_upload')->where('file_name', $file_name)->get()->row_array();
    }

    public function check_file_exists($file_name){
        return $this->db->select('*')->from('coop_loan_verify_bank_upload')->where('file_name', $file_name)->get()->num_rows();
    }

    public function getDataById($id){
        $this->db->where('id', $id);
        $file = $this->db->get('coop_loan_verify_bank_upload')->row_array();
        if(file_exists($file['file_path'])) {
            return self::read_file_DCSBAD60($file['file_path']);
        }else{
            return [];
        }
    }

    public function update_atm_file_status($post, $file_date){
        $where['member_id'] = $post['member'];
        $where['loan_atm_status'] = '1';
        $where['activate_status'] = '0';
        //$where['atm_file_status'] = 'A';
        $contract = $this->db->select('*')->from('coop_loan_atm')->where($where)->get()->row();
        $data = [];
        if($contract){
            $action_mode = strtoupper(substr($post['action'], 0, 1));
            if('A' == $action_mode  && 'COMPLETE' === strtoupper($post['status']) && ( empty($contract->atm_file_write_datetime) || date_create($contract->atm_file_write_datetime) <= date_create($file_date)) ){

                $data['atm_file_status'] = 'C';
                //$data['atm_file_last_access'] = date('Y-m-d H:i:s');
                $data['atm_file_last_access'] = date('Y-m-d H:i:s', strtotime($file_date));
                $data['atm_file_write_datetime'] = date('Y-m-d H:i:s', strtotime($file_date));
                $data['activate_status'] = self::activated_status($action_mode);
                $this->db->where('loan_atm_id', $contract->loan_atm_id);
                $this->db->update('coop_loan_atm', $data);

                return 'change new account success';
            }else if('C' == $action_mode  && 'COMPLETE' === strtoupper($post['status']) && date_create($contract->atm_file_write_datetime) <= date_create($file_date) ){

                $data['atm_file_status'] = 'C';
                //$data['atm_file_last_access'] = date('Y-m-d H:i:s');
                $data['atm_file_last_access'] = date('Y-m-d H:i:s', strtotime($file_date));
                $data['activate_status'] = self::activated_status($action_mode);
                $this->db->where('loan_atm_id', $contract->loan_atm_id);
                $this->db->update('coop_loan_atm', $data);
                return 'change current account success';

            }else if('D' == $action_mode  && 'COMPLETE' === strtoupper($post['status']) && date_create($contract->atm_file_write_datetime) <= date_create($file_date) ){

                $data['atm_file_status'] = 'D';
                //$data['atm_file_last_access'] = date('Y-m-d H:i:s');
                $data['atm_file_last_access'] = date('Y-m-d H:i:s', strtotime($file_date));
                $data['activate_status'] = self::activated_status($action_mode);
                $this->db->where('loan_atm_id', $contract->loan_atm_id);
                $this->db->update('coop_loan_atm', $data);
                return 'delete current account success';

            }else{

                return 'do nothing';
            }
        }
        return 'fail';
    }

    public function update_contract_by_file_loan($id){
        $this->db->where('id', $id);
        $file = $this->db->get('coop_loan_verify_bank_upload')->row_array();
        $sql = $this->db->last_query();
        if(file_exists($file['file_path'])) {
            $data = self::read_file_DCSBAD60($file['file_path']);
            $tmp = $res = [];
            $row = 0;
            foreach ($data as $key => $item){
                if(in_array($item['member'], $tmp)) {
                    continue;
                }else{
                    array_push($tmp, $item['member']);
                    $res[$row]['member'] = $item['member'];
                    $res[$row]['data'] = self::update_atm_file_status($item, $file['file_date']);
                    $row++;
                }
            }

            if(sizeof($res)) {
                $data_update['active_status'] = '1';
                $data_update['submit_date'] = date('Y-m-d H:i:s');
                $this->db->where('id', $file['id']);
                $this->db->update('coop_loan_verify_bank_upload', $data_update);
            }
            return $res;
        }
        return $sql;
    }

    private function activated_status($action_mode){
        switch ($action_mode){
            case 'A' : return 0;
            case 'C' : return 0;
            case 'D' : return 1;
            default : return 0;
        }
    }
	
	public function del_verify_bank_upload_file($id){
		$this->db->where('id', $id);
        $file = $this->db->get('coop_loan_verify_bank_upload')->row_array();
        $sql = $this->db->last_query();
        if(file_exists($file['file_path'])) {
			$this->db->where('id',$id);
			if($this->db->delete('coop_loan_verify_bank_upload')){		
				$res['id'] = $id;
				return $res;
			}
        }
        return $sql;
	}
	
	public function get_delete_contract($date=''){
	
        $_where = "loan_atm_status = 1 AND atm_file_status = 'D'";
		if($date != ''){
			$_where .= " AND DATE(atm_file_last_access) = '{$date}'";
		}
		$result = $this->db->select("*")->from('coop_loan_atm')->where($_where)->order_by('loan_atm_id', 'asc')
        ->get()->result_array();
        //echo $this->db->last_query();
		return $result;
    }
	
	public function find_address($member_id){
        $data = '';
		$row = $this->db->select("t1.*,
								t2.district_name AS district_name,
								t3.amphur_name AS amphur_name,
								t4.province_name AS province_name")
							->from('coop_mem_apply AS t1')
							->join("coop_district AS t2","t1.district_id = t2.district_id","left")
							->join("coop_amphur AS t3","t1.amphur_id = t3.amphur_id","left")
							->join("coop_province AS t4","t1.province_id = t4.province_id","left")
							->where('member_id', $member_id)->get()->row_array();
		
		$data .= (@$row['address_no'] != '')?$row['address_no']:'';
		$data .= (@$row['address_moo'] != '')?' '.$row['address_moo']:'';
		$data .= (@$row['address_village'] != '')?' '.$row['address_village']:'';
		$data .= (@$row['address_soi'] != '')?' '.$row['address_soi']:'';
		$data .= (@$row['address_road'] != '')?' '.$row['address_road']:'';
		$data .= (@$row['district_name'] != '')?' '.$row['district_name']:'';
		$data .= (@$row['amphur_name'] != '')?' '.$row['amphur_name']:'';
		$data .= (@$row['province_name'] != '')?' '.$row['province_name']:'';
		return $data;
    }

	public function getDataReceiveById($id,$bank_atm){		
		if($bank_atm == 'ktb'){
			$table = "coop_loan_atm_transaction_receive_file_ktb";
		}else{
			$table = "coop_loan_atm_transaction_receive_file";
		}
		$result = $this->db->select("t1.member_id, t2.contract_number, t1.transaction_amount, t1.transaction_date")
                ->from("{$table} as t1")
				->join('coop_loan_atm as t2', 't1.contract_no=t2.contract_number AND t1.member_id=t2.member_id', 'left')
				->where("t1.file_id='{$id}'")
				->get()->result_array();

		return $result;
    }
	
	//บันทึกรายการกู้เงินฉุกเฉิน ATM
	public function loan_atm_receive_file($file_id, $data = array(), $file_name , $bank_atm){
		if($bank_atm == 'ktb'){
			$result = $this->loan_atm_receive_file_ktb($file_id, $data, $file_name);
		}else{
			$result = $this->loan_atm_receive_file_kma($file_id, $data, $file_name);
		}
		return $result;
	}
	
	//kma=ธนาคารกรุงศรีอยธุยา
	public function loan_atm_receive_file_kma($file_id, $data = array(), $file_name = ""){
        $data_insert = array();
        $result = array('result' => 0, 'msg' => 'error');
	    if(isset($data) && sizeof($data)){
	        foreach ($data as $index => $item){
	            $data_insert[$index]['member_id']  = $this->toMemberID($item['COOF-CUST-ID']);
	            $data_insert[$index]['transaction_code']  = $this->removeSpace($item['TRANS-CODE']);
	            $data_insert[$index]['transaction_date']  = $this->dateFormat($item['TRANS-DATE'])." ".$this->timeFormat($item['TRANS-TIME']);
	            $data_insert[$index]['transaction_amount']  = $this->toNumber($item['TRANS-AMOUNT']);
	            $data_insert[$index]['terminal_city']  = $item['TERM-CITY'];
	            $data_insert[$index]['terminal_location']  = $item['TERM-LOCATION'];
	            $data_insert[$index]['terminal_number']  = $this->removeSpace($item['TERM-NUM']);
	            $data_insert[$index]['terminal_seq']  = $item['TERM-TXSEQ'];
	            $data_insert[$index]['terminal_state']  = $item['TERM-STATE'];
	            $data_insert[$index]['form_ac_number']  = $item['FROM-AC-NUM'];
	            $data_insert[$index]['to_ac_number']  = $item['TO-AC-NUM'];
	            $data_insert[$index]['form_file_name']  = $file_name;
	            $data_insert[$index]['approve_code']  = $item['APPROVE-CODE'];
	            $data_insert[$index]['createdatetime']  = date("Y-m-d H:i:s");
	            $data_insert[$index]['file_id']  = $file_id;
            }
            unset($data);

	        if(sizeof($data_insert)){
                $this->db->insert_batch('coop_loan_atm_transaction_receive_file', $data_insert);
                $update = array();
                $update['active_status'] = 1;
                $this->db->where(array('id' => $file_id));
                $this->db->update('coop_loan_atm_file_upload', $update);
                return $data_insert;
            }else{
                return 0;
            }
        }
	    return 0;
    }
	
	//ktb=ธนาคารกรุงไทย
	public function loan_atm_receive_file_ktb($file_id, $data = array(), $file_name = ""){
        $data_insert = array();
        $result = array('result' => 0, 'msg' => 'error');
	    if(isset($data) && sizeof($data)){
	        foreach ($data as $index => $item){
	            $data_insert[$index]['member_id']  = $this->toMemberID($item['MEMBER_NO']);
	            $data_insert[$index]['coop_id']  = $item['COOP_ID'];
	            $data_insert[$index]['operate_date']  = $item['OPERATE_DATE'];
	            $data_insert[$index]['operate_time']  = $item['OPERATE_TIME'];
	            $data_insert[$index]['operate_code']  = $item['OPERATE_CODE'];
	            $data_insert[$index]['transaction_amount']  = $this->toNumber($item['ITEM_AMT']);
	            $data_insert[$index]['bank_code']  = $item['BANK_CODE'];
	            $data_insert[$index]['branch_code']  = $item['BRANCH_CODE'];
	            $data_insert[$index]['atm_no']  = $item['ATM_NO'];
	            $data_insert[$index]['atm_seqno']  = $item['ATM_SEQNO'];
	            $data_insert[$index]['saving_acc']  = $item['SAVING_ACC'];
	            $data_insert[$index]['contract_no']  = $this->removeSpace($item['CONTRACT_NO']);
				$data_insert[$index]['transaction_date']  = $this->dateFormat_y($item['OPERATE_DATE'])." ".$this->timeFormat($item['OPERATE_TIME']);
				$data_insert[$index]['createdatetime']  = date("Y-m-d H:i:s");
	            $data_insert[$index]['file_id']  = $file_id;
            }
            unset($data);

	        if(sizeof($data_insert)){
                $this->db->insert_batch('coop_loan_atm_transaction_receive_file_ktb', $data_insert);
                $update = array();
                $update['active_status'] = 1;
                $this->db->where(array('id' => $file_id));
                $this->db->update('coop_loan_atm_file_upload', $update);
                return $data_insert;
            }else{
                return 0;
            }
        }
	    return 0;
    }

	public function get_transaction_receive_file($file_id, $bank_atm){
		if($bank_atm == 'ktb'){
			$table = "coop_loan_atm_transaction_receive_file_ktb";
			$condition_join = " AND t1.contract_no = t2.contract_number";
            $_where = "";
		}else{
			$table = "coop_loan_atm_transaction_receive_file";
			$condition_join = "";
			$_where = " AND t2.loan_atm_status=1";
		}
		$res = $this->db->select("t1.*, t2.loan_atm_id")->from("{$table} as t1")
				->join("coop_loan_atm as t2", "t1.member_id=t2.member_id {$condition_join}", "inner")
				->where("t1.file_id = '{$file_id}'".$_where)
				->get()->result_array();	
		return $res;		
	}

	public function get_transaction_receive_file_range($bank_atm, $option = array()){
        if($bank_atm == 'ktb'){
            $table = "coop_loan_atm_transaction_receive_file_ktb";
            $condition_join = " AND t1.contract_no = t2.contract_number";
        }else{
            $table = "coop_loan_atm_transaction_receive_file";
            $condition_join = "";
            $this->db->where("t2.loan_atm_status='1'");
        }
        if(!empty($option)){
           $this->db->where($option);
        }
        $res = $this->db->select("t1.*, t2.loan_atm_id")->from("{$table} as t1")
            ->join("coop_loan_atm as t2", "t1.member_id=t2.member_id {$condition_join}", "inner")
            ->get()->result_array();

        return $res;
    }

    public function atm_detail_add($trans, $setting){
        ini_set("precision", 12);
        $data_insert = array();
        $data_insert['loan_atm_id'] = $trans['loan_atm_id'];
        $data_insert['member_id'] = str_pad($trans['member_id'],6, "0", STR_PAD_LEFT);
        $data_insert['member_id_atm'] = str_pad($trans['member_id'], 6, "0", STR_PAD_LEFT);
        $data_insert['loan_amount'] = str_replace(',','',$trans['transaction_amount']);
        $data_insert['loan_amount_balance'] = $trans['transaction_amount'];
        $data_insert['loan_date'] = $trans['transaction_date'];
        $data_insert['loan_status'] = '0';
        $data_insert['loan_description'] = 'ทำรายการกู้ATM';
        $data_insert['date_start_period'] = date('Y-m-t',strtotime($data_insert['loan_date'].' +1 month'));
        $data_insert['transaction_at'] = '1';
        $data_insert['transfer_status'] = '1';
        $data_insert['admin_id'] = $_SESSION['USER_ID'];

        $principal_per_month = $data_insert['loan_amount']/$setting->max_period;
        $data_insert['principal_per_month'] = ceil($principal_per_month);
        //echo"<pre>";print_r($data_insert);exit;

        $this->db->select(array('petition_number'));
        $this->db->from('coop_loan_atm_detail');
        $this->db->order_by('petition_number DESC');
        $this->db->limit(1);
        $row_petition_number = $this->db->get()->result_array();
        if(!empty($row_petition_number)){
            $petition_number = (int)$row_petition_number[0]['petition_number']+1;
            $petition_number = sprintf('%06d',$petition_number);
        }else{
            $petition_number = sprintf('%06d',1);
        }
        $data_insert['petition_number'] = $petition_number;
        $data_insert['pay_type'] = '1';
        $data_insert['account_id'] = $trans['form_ac_number'];

        $this->db->insert('coop_loan_atm_detail',$data_insert);

        $this->db->select(array('total_amount_approve','total_amount_balance'));
        $this->db->from('coop_loan_atm');
        $this->db->where("loan_atm_id = '".$trans['loan_atm_id']."'");
        $row_loan_atm = $this->db->get()->result_array();
        $total_amount_balance = $row_loan_atm[0]['total_amount_balance'] - str_replace(',','',$trans['transaction_amount']);

        $loan_amount_balance = $this->find_balance_detail($trans['loan_atm_id'], $trans['transaction_date'], 0);

        $atm_transaction = array();
        $atm_transaction['loan_atm_id'] = $trans['loan_atm_id'];
        $atm_transaction['loan_amount_balance'] = $loan_amount_balance;
        $atm_transaction['transaction_datetime'] = $trans['transaction_date'];
        $atm_transaction['receipt_id'] = $trans['atm_no'];
        //$this->loan_libraries->atm_transaction($atm_transaction);
        $this->loan_libraries->atm_transaction_arrears('AT', $atm_transaction);
        $loan_atm = $this->get_loan_atm_balance($trans['loan_atm_id']);

        $data_insert = array();
        $data_insert['loan_atm_status'] = 1;
        $data_insert['total_amount_balance'] = round($loan_atm->total_amount_approve - $loan_amount_balance, 2);
        $this->db->where('loan_atm_id',$trans['loan_atm_id']);
        $this->db->update('coop_loan_atm',$data_insert);
    }

	private function get_loan_atm_balance($atm_id){
	    $this->db->select('total_amount_balance, total_amount_approve');
	    $this->db->where(array('loan_atm_id' => $atm_id));
	    return $this->db->get('coop_loan_atm', 'loan_amount_balance')->row();
    }

    private function find_balance_detail($loan_atm_id, $trans_date, $approve_amount = 0){
        $balance = 0;
	    $res = $this->db->select("SUM(loan_amount_balance) as loan_balance_real")->from("coop_loan_atm_detail")
            ->where(array("loan_atm_id" => $loan_atm_id, "loan_date <=" => $trans_date, "loan_status" => 0))->group_by('loan_atm_id')->get()->row();
	    if($res) {
           $balance = ROUND($res->loan_balance_real, 2);
        }
	    return $balance;
    }
	
	/***down_load_file ข้อมูลยอดสรุปกู้เงินฉุกเฉิน ATM ***/
	public function get_data_load_file($bank_atm){		
		if($bank_atm == 'ktb'){
			$result = $this->get_data_load_file_ktb();
		}else{
			$result = $this->get_data_load_file_kma();
		}
		return $result;
	}
	
	public function get_data_load_file_kma(){
		$result = array();
		$date_now = date('Y-m-d');
		
		$setting_file = $this->get_setting_loan_atm_file();
        $file_name = "COOP".$setting_file['coop_code'];
        $path_file = './assets/document/'.$file_name.".txt";

        $text = $this->atm_header((int)$setting_file['coop_code'], $date_now, "coopf".$setting_file['coop_code']);

        //echo strlen($text); exit;

        $info_row_amt = sizeof($this->loan_atm_file->get_new_contract());	
        $text .= $this->loan_atm_file->new_contract_info($date_now);
        $text .= $this->loan_atm_file->delete_contract_info($date_now);
        $res = $this->loan_atm_file_last($date_now);
        $rec_num = $info_row_amt;
        $total_principal = 0;
        $limit_mat = 0;
        $sum_max_amount = 0;

        foreach($res as $key => $val){

            $coop_type_id = 7083;
            $member_id = $val['member_id'];
            $min_limit_amt = 100.00;
            $principal = $val['total_amount_balance'];
            $total_principal += round($principal, 2);
            $principal_x2  = round($val['total_amount_balance'] * 2, 2);
            $limit_mat += $principal_x2;
            $expired   = date('Y-m-d', strtotime(str_replace('-','/',$val['approve_date']) . " +20 year"));
            $account_id  = $val['account_id'];
            $interest = round($this->loan_libraries->cal_atm_interest_transaction($val), 2);
            $status = $val['loan_atm_status'];
            $max_amount = $val['max_amount'];

            $sum_max_amount += $max_amount;
            //set option keys
            $key_options = array(
                'coop_type_id' => $coop_type_id,
                'coop_customer_id' => $member_id
            );
            $this->set_keys($key_options);
            $this->set_control(2, $val['atm_file_status']);
            $this->set_cus_status($status);
            $this->set_cus_expired($expired);

            $auth_option = array(
                'tran_min_limit_amt' => $this->toString($min_limit_amt),
                'tran_max_limit_amt' => $this->toString($principal),
                'daily_limit_trans' => '999',
                'daily_limit_amt' => $this->toString($principal),
                'weekly_limit_trans' => '999',
                'weekly_limit_amt' => $this->toString($principal_x2),
                'monthly_limit_trans' => '999',
                'monthly_limit_amt' => $this->toString($principal_x2),
                'cus_limit_trans' => '999',
                'cus_limit_amt' => $this->toString($principal),
            );
            $this->set_auth_info($auth_option);

            $dept_option = array(
                'od_principal' => $this->toString($max_amount),
                'od_interest' => $this->toString($interest),
                'due_date' => '2099-12-31',
                'last_int_date' => '2099-12-31',
                'last_pay_date' => '2099-12-31',
                'min_pay_time' => '',
                'min_pay_amt' => '',
                'max_pay_time' => '',
                'max_pay_amt' => ''
            );
            $this->set_dept_info($dept_option);
            $this->set_bank_info($account_id);
            $text .= $this->atm_record_detail();
            $rec_num++;
        }
		
        $arr_data_footer = array(
								'rec_number' => $rec_num,
								'limit_amt' => $this->toString($total_principal),
								'principle_amt' => $this->toString($sum_max_amount),
								'rec_type' => '9',
								'end' => 'END'
							);
        $text .= $this->atm_footer($arr_data_footer);
		
		$result['file_name'] = $file_name;
		$result['path_file'] = $path_file;
		$result['text'] = $text;
		return $result;
	}
	
	public function get_data_load_file_ktb(){
		$result = array();
		$date_now = date('Y-m-d');
		
		$setting_file = $this->get_setting_loan_atm_file();
		$dd = date("d", strtotime($date_now)); //วันที่
		$coop_code = $setting_file['coop_code'];//รหัสสหกรณ์
		//$round = '01'; // รอบที่ (01,02)	 สหกรณ์ upload ให้ธนาคาร ก่อนเวลา 12.00 น.  และก่อน 17.00 น.ของทุกวัน 
		$time_hour = date('H');
		if($time_hour < 12){
			$round = '01';
		}else{
			$round = '02';
		}

		$file_name = "L".$dd.$coop_code.$round;
        $path_file = './assets/document/'.$file_name.".DAT";
		
        $text = $this->atm_header((int)$setting_file['coop_code'], $date_now, "coopf".$setting_file['coop_code']);

        $res = $this->loan_atm_file_last($date_now);

        $rec_num = 0;
        $total_principal = 0;
        $limit_mat = 0;
        $sum_max_amount = 0;
		$total_amount = 0;

        foreach($res as $key => $val){
            $coop_type_id = 7083;
            $min_limit_amt = 100.00;
            $principal = $val['total_amount_balance'];
            $total_principal += round($principal, 2);
            $principal_x2  = round($val['total_amount_balance'] * 2, 2);
            $limit_mat += $principal_x2;
            $interest = round($this->loan_libraries->cal_atm_interest_transaction($val), 2);
            $status = $val['loan_atm_status'];
            
			$member_id = $val['member_id'];
			$account_id  = trim($val['account_id']);
			$max_amount = $val['max_amount'];
			$total_amount_balance = $val['total_amount_balance'];
			$debt_balance_amount = round($val['max_amount']-$val['total_amount_balance'],2);
			$expired   = date('Y-m-d', strtotime(str_replace('-','/',$val['approve_date']) . " +20 year"));
			$contract_number = $val['contract_number'];
			$member_name = $val['firstname_th'];
			$memeber_surname = $val['lastname_th'];
			$person_id = $val['id_card'];
			
			//$transaction_date_last = '2020-06-21 13:20:45';
			$transaction_date_last = $val['transaction_date_last'];
			$operate_date = date("Y-m-d", strtotime($transaction_date_last));
			$operate_time = date("H:i:s", strtotime($transaction_date_last));
			
            $sum_max_amount += $max_amount;
            $total_amount += $total_amount_balance; //ยอดเงินรวมทั้งสิ้น
			$arr_data_detail = array(
									'acc_no' => $account_id,
									'item_type' => 'R',
									'item_amount' => $total_amount_balance,
									'operate_date' => $operate_date,
									'operate_time' => $operate_time,
									'member_no' => $member_id,
									'balance_amount' => $debt_balance_amount,
									'expire_date' => $expired,
									'reference_key' => $contract_number,
									'member_name' => $member_name,
									'memeber_surname' => $memeber_surname,
									'person_id' => $person_id
									);	
            $text .= $this->atm_record_detail($arr_data_detail);
			//echo $text.'<hr>';
            $rec_num++;
        }
		$arr_data_footer = array(
								'total_amount' => $total_amount,
								'rec_num' => $rec_num
							);
        $text .= $this->atm_footer($arr_data_footer);

		$result['file_name'] = $file_name;
		$result['path_file'] = $path_file;
		$result['text'] = $text;
		return $result;
	}
	
	public function loan_atm_file_last($date = ''){	
		$setting_file = $this->get_setting_loan_atm_file();
		$bank_atm = $setting_file['bank_atm'];		
		if($bank_atm == 'ktb'){			
			$where  = "";
				if($date != ''){
					$where = " AND (DATE( atm_file_last_access ) = '{$date}'  OR DATE(t3.transaction_datetime) = '{$date}')";
				}
				
				$res = $this->db->select(array('t1.member_id', 't1.total_amount_balance', 't1.loan_atm_status', 't1.approve_date', 't1.total_amount_approve as max_amount'
										,'t1.account_id', 'now() as date_interesting', 't1.atm_file_status' ,'t1.contract_number','t2.firstname_th','t2.lastname_th','t2.id_card'
										,'IF(t3.transaction_datetime > t1.atm_file_last_access,t3.transaction_datetime,t1.atm_file_last_access) AS transaction_date_last'))
				->from('coop_loan_atm t1')
				->join('coop_mem_apply t2', 't1.member_id=t2.member_id', 'inner')
				->join('(SELECT loan_atm_id, MAX(cast(transaction_datetime as Datetime)) as transaction_datetime FROM coop_loan_atm_transaction group by loan_atm_id) AS t3', 't1.loan_atm_id = t3.loan_atm_id', 'left')
				->where("t1.loan_atm_status = '1' AND t2.member_status = '1' {$where}")->order_by('`t1`.`atm_file_status`,
			`t1`.`approve_date`,CAST(`t1`.`account_id` AS UNSIGNED)', 'asc')->get()->result_array();
				//echo $this->db->last_query(); exit;
		}else{
			$where  = "";
			if($date != ''){
				$where = " AND DATE(atm_file_last_access) = '{$date}'";
			}
			//AND t1.activate_status = '0'
			
			$res = $this->db->select(array('t1.member_id', 't1.total_amount_balance', 't1.loan_atm_status', 't1.approve_date', 't1.total_amount_approve as max_amount'
									,'t1.account_id', 'now() as date_interesting', 't1.atm_file_status' ,'t1.contract_number','t2.firstname_th','t2.lastname_th','t2.id_card'))
			->from('coop_loan_atm t1')
			->join('coop_mem_apply t2', 't1.member_id=t2.member_id', 'inner')
			->where("t1.loan_atm_status = '1' AND t2.member_status = '1' {$where}")->order_by('`t1`.`atm_file_status`,
		`t1`.`approve_date`,CAST(`t1`.`account_id` AS UNSIGNED)', 'asc')->get()->result_array();
			//echo $this->db->last_query(); exit;
		}
        return $res;
    }

    public function checkContractByFileId($id){
	    $file_data = self::getFileData($id);
	    $result = array();
	    foreach ($file_data as $key => $val){
	        if(self::checkAtmContract($val['contract_no'], $val['member_id'])){
	            continue;
            }else{
	            $result[] = array("member_id" => $val['member_id'], "contract_number" => $val['contract_no']);
            }
        }
	    return $result;
    }

    private function getFileData($id){
	    return $this->db->where(array('file_id' => $id))->get("coop_loan_atm_transaction_receive_file_ktb")->result_array();
    }

    private function checkAtmContract($contract_number, $member_id){
	    return $this->db->where(array("contract_number" => $contract_number, "member_id" => $member_id))
            ->count_all_results("coop_loan_atm");
    }
	
}
