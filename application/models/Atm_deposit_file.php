<?php
/**
 * Created by PhpStorm.
 * User: macmini2
 * Date: 2019-12-04
 * Time: 10:12
 */

class Atm_deposit_file extends Text_file
{

    public function __construct()
    {
        parent::__construct();
    }

    public function check_file_exists($file_name)
    {
        return $this->db->select('*')->from('coop_deposit_atm_file_upload')->where('file_name', $file_name)->get()->num_rows();
    }

    public function read_withdraw_file($file_name)
    {
		$setting_file = $this->get_setting_loan_atm_file();
		$bank_atm = $setting_file['bank_atm'];
        if($bank_atm == 'ktb'){
			$items = $this->read_withdraw_file_ktb($file_name);
		}else{
			$items = $this->read_withdraw_file_kma($file_name);
		}
        return $items;
    }
	
	public function read_withdraw_file_kma($file_name)
    {
        if (!file_exists($file_name)) {
            return;
        }
        $file = file_get_contents($file_name);
        $all = strlen($file);
        $data = array();
        for ($row = 0; $row < ceil($all / 160) - 1; $row++) {
            $line = mb_substr($file, $row * 162, 160);
            if (strlen($line)) {
                $data[$row] = $line;
            }
        }
        unset($data[0], $data[sizeof($data)]);
        $items = array();
        $index = 0;
        foreach ($data as $item) {
            $items[$index]['CARD-NUM'] = substr($item, 4, 19);
            $items[$index]['TERM-NUM'] = substr($item, 26, 7);
            $items[$index]['TERM-LOCATION'] = substr($item, 33, 15);
            $items[$index]['TERM-CITY'] = substr($item, 48, 2);
            $items[$index]['TERM-STATE'] = substr($item, 50, 2);
            $items[$index]['TERM-TXSEQ'] = substr($item, 52, 6);
            $items[$index]['TRANS-DATE'] = substr($item, 58, 8);
            $items[$index]['TRANS-TIME'] = substr($item, 66, 6);
            $items[$index]['TRANS-CODE'] = substr($item, 72, 6);
            $items[$index]['FROM-AC-NUM'] = substr($item, 78, 10);
            $items[$index]['TO-AC-NUM'] = substr($item, 88, 10);
            $items[$index]['COOF-CUST-ID'] = substr($item, 98, 19);
            $items[$index]['TRANS-AMOUNT'] = substr($item, 117, 11);
            $items[$index]['DISP-AMOUNT'] = substr($item, 128, 11);
            $items[$index]['TRANS-FEE'] = substr($item, 139, 9);
            $items[$index]['RESP-BY'] = substr($item, 148, 1);
            $items[$index]['RESP-CODE-1'] = substr($item, 149, 1);
            $items[$index]['RESP-CODE-2'] = substr($item, 150, 2);
            $items[$index]['REV-CODE'] = substr($item, 152, 2);
            $items[$index]['APPROVE-CODE'] = substr($item, 154, 6);
            $index++;
        }
        return $items;
    }
	
	public function read_withdraw_file_ktb($file_name)
    {		
		if (!file_exists($file_name)) {
            return;
        }

		$file = self::tis620_to_utf8(file_get_contents($file_name));

		$all =  mb_strlen($file, 'UTF-8');
        $data = array();

        for($row = 0; $row < ceil($all/ 84); $row++){
			$line = mb_substr($file, $row*85, 84, 'UTF-8');
            if(strlen($line)){
               $data[$row] = $line;
            }
        }

        $items = array();
        $index = 0;
		
        foreach ($data as $item) {
            $items[$index]['MEMBER_NO']   	= mb_substr($item,0, 6, 'UTF-8');
            $items[$index]['COOP_ID']       = mb_substr($item,6, 4, 'UTF-8');
            $items[$index]['OPERATE_DATE']  = mb_substr($item,10, 6, 'UTF-8');
            $items[$index]['OPERATE_TIME']  = mb_substr($item,16, 6, 'UTF-8');
            $items[$index]['OPERATE_CODE']  = mb_substr($item,22,3, 'UTF-8');
            $items[$index]['ITEM_AMT']      = mb_substr($item,25,15, 'UTF-8');
            $items[$index]['BANK_CODE']     = mb_substr($item,40,3, 'UTF-8');
            $items[$index]['BRANCH_CODE']   = mb_substr($item,43,3, 'UTF-8');
            $items[$index]['ATM_NO']       	= mb_substr($item,46,6, 'UTF-8');
            $items[$index]['ATM_SEQNO']     = mb_substr($item,52, 6, 'UTF-8');
            $items[$index]['SAVING_ACC']    = mb_substr($item,58, 15, 'UTF-8');
            $items[$index]['CONTRACT_NO']   = mb_substr($item, 73, 10, 'UTF-8');		
            $index++;
        }

        return $items;
    }

    public function save_deposit_atm_receive_file($file_id, $data = array(), $file_name = "")
    {
        $setting_file = $this->get_setting_loan_atm_file();
		$bank_atm = $setting_file['bank_atm'];
        if($bank_atm == 'ktb'){
			$result = $this->save_deposit_atm_receive_file_ktb($file_id,$data,$file_name);
		}else{
			$result = $this->save_deposit_atm_receive_file_kma($file_id,$data,$file_name);
		}
        return $result;
    }
	
	public function save_deposit_atm_receive_file_kma($file_id, $data = array(), $file_name = "")
    {
        $data_insert = array();
        $result = array('result' => 0, 'msg' => 'error');
        if (isset($data) && sizeof($data)) {
            foreach ($data as $index => $item) {
                $data_insert[$index]['member_id'] = $this->toMemberID($item['COOF-CUST-ID']);
                $data_insert[$index]['transaction_code'] = $this->removeSpace($item['TRANS-CODE']);
                $data_insert[$index]['transaction_date'] = $this->dateFormat($item['TRANS-DATE']) . " " . $this->timeFormat($item['TRANS-TIME']);
                $data_insert[$index]['transaction_amount'] = $this->toNumber($item['TRANS-AMOUNT']);
                $data_insert[$index]['terminal_city'] = $item['TERM-CITY'];
                $data_insert[$index]['terminal_location'] = $item['TERM-LOCATION'];
                $data_insert[$index]['terminal_number'] = $this->removeSpace($item['TERM-NUM']);
                $data_insert[$index]['terminal_seq'] = $item['TERM-TXSEQ'];
                $data_insert[$index]['terminal_state'] = $item['TERM-STATE'];
                $data_insert[$index]['form_ac_number'] = $item['FROM-AC-NUM'];
                $data_insert[$index]['to_ac_number'] = $item['TO-AC-NUM'];
                $data_insert[$index]['form_file_name'] = $file_name;
                $data_insert[$index]['approve_code'] = $item['APPROVE-CODE'];
                $data_insert[$index]['createdatetime'] = date("Y-m-d H:i:s");
                $data_insert[$index]['file_id'] = $file_id;
            }
            unset($data);

            if (sizeof($data_insert)) {
                $this->db->insert_batch('coop_deposit_atm_transaction_receive_file', $data_insert);
                $update = array();
                $update['active_status'] = 1;
                $this->db->where(array('id' => $file_id));
                $this->db->update('coop_deposit_atm_file_upload', $update);
                return $data_insert;
            } else {
                return 0;
            }
        }
        return 0;
    }
	
	public function save_deposit_atm_receive_file_ktb($file_id, $data = array(), $file_name = "")
    {       
		$data_insert = array();
        $result = array('result' => 0, 'msg' => 'error');
        if (isset($data) && sizeof($data)) {
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

            if (sizeof($data_insert)) {
                $this->db->insert_batch('coop_deposit_atm_transaction_receive_file_ktb', $data_insert);
                $update = array();
                $update['active_status'] = 1;
                $this->db->where(array('id' => $file_id));
                $this->db->update('coop_deposit_atm_file_upload', $update);
                return $data_insert;
            } else {
                return 0;
            }
        }
        return 0;
    }

    public function add_account_transaction($file_id)
    {
		$setting_file = $this->get_setting_loan_atm_file();
		$bank_atm = $setting_file['bank_atm'];
        if($bank_atm == 'ktb'){
			$where = array('file_id' => $file_id);
			$data = $this->db->select('*,saving_acc AS to_ac_number')->from("coop_deposit_atm_transaction_receive_file_ktb")
					->where($where)->get()->result_array();
		}else{
			$where = array('file_id' => $file_id);
			$data = $this->db->select('*')->from("coop_deposit_atm_transaction_receive_file")
					->where($where)->get()->result_array();
		}
        
        if (sizeof($data)) {
            foreach ($data as $key => $trans) {
                self::update_account_transaction($trans);
                self::update_deposit_account_detail($trans);
            }
        }
    }

    public function update_account_transaction($trans)
    {
        $maco_acc = self::get_deposit_atm_account(sprintf('%06d',$trans['member_id']), $trans['to_ac_number']);
        $account_id = $maco_acc['account_id'];
        $datetime = $trans['transaction_date'];
        $transaction_amount = $trans['transaction_amount'];
        $acc_trans = self::get_max_balance($account_id, $datetime);
		
		$setting_file = $this->get_setting_loan_atm_file();
		$bank_atm = $setting_file['bank_atm'];
		if($bank_atm == 'ktb'){
			if($trans['operate_code'] == '023'){
				$transaction_list = 'AXW';	///ฝาก
				$balance = round(round($acc_trans['transaction_balance'], 2)+round($transaction_amount, 2), 2);
				$withdrawal_amount = 0;
				$deposit_amount = $transaction_amount;
			}else{
				$transaction_list = 'WAT';	///ถอน
				$balance = round(round($acc_trans['transaction_balance'], 2)-round($transaction_amount, 2), 2);
				$withdrawal_amount = $transaction_amount;
				$deposit_amount = 0;
			}
		}else{
			$transaction_list = 'WAT';
			$balance = round(round($acc_trans['transaction_balance'], 2)-round($transaction_amount, 2), 2);
			$withdrawal_amount = $transaction_amount;
			$deposit_amount = 0;
		}
	
        $insert_data = array();
        $insert_data['transaction_list'] = $transaction_list;
		
		$insert_data['transaction_withdrawal'] = $withdrawal_amount;
        $insert_data['transaction_deposit'] = $deposit_amount;
        $insert_data['balance_deposit'] = round($balance, 2);
        $insert_data['balance_deposit_int'] = 0;
        $insert_data['transaction_balance'] = round($balance, 2);
        $insert_data['transaction_time'] = date('Y-m-d H:i:s', strtotime($datetime));
        $insert_data['user_id'] = "SYSTEM";
        $insert_data['account_id'] = $account_id;
        $insert_data['ref_account_no'] = $acc_trans['ref_account_no'];
		//echo '<pre>'; print_r($insert_data); echo '</pre>';
		if($this->db->insert('coop_account_transaction', $insert_data)){		
			$this->update_st->update_transaction_balance($account_id, $datetime);
		}
		//exit;
    }

    public function update_deposit_account_detail($trans)
    {
		//echo '<pre>'; print_r($trans); echo '</pre>'; exit;
		$setting_file = $this->get_setting_loan_atm_file();
		$bank_atm = $setting_file['bank_atm'];	
	
        $deposit_acc = self::get_deposit_atm_account(sprintf('%06d',$trans['member_id']), $trans['to_ac_number']);

        $detail = self::get_max_transaction($deposit_acc['account_id'], $trans['transaction_date']);		

        $_seq_no = (int)$detail['seq_no'];
        $seq_no = (int)$detail['seq_no']+1;

        $_banalce = $detail['banalce'];
        $balance = round(round($detail['banalce'], 2)-round($trans['transaction_amount'], 2), 2);

        $insert_data = [];
        $insert_data['account_id'] = $deposit_acc['account_id'];
        $insert_data['seq_no'] = $seq_no;
        $insert_data['principal_amount'] = round($trans['transaction_amount'], 2);
        $insert_data['fee_amount'] =round(0.0, 2);
        $insert_data['operate_date'] = $trans['transaction_date'];
		if($bank_atm == 'ktb'){
			if($trans['operate_code'] == '022'){
				$insert_data['item_type'] = 'WD'; //ถอน
			}else if($trans['operate_code'] == '023'){
				$insert_data['item_type'] = 'CHG';	///ฝาก
				$balance = round(round($detail['banalce'], 2)+round($trans['transaction_amount'], 2), 2);
			}
		}else{	
			$insert_data['item_type'] = preg_replace( '/0/', '',$trans['terminal_lacation']) ? 'LRQ' : '10';
		}
        $insert_data['transaction_no'] = $trans['terminal_state'].$trans['terminal_seq'];
        $insert_data['terminal_number'] = $trans['terminal_number'];
        $insert_data['terminal_lacation'] = $trans['terminal_location'];
        $insert_data['approve_amount'] = $detail['approve_amount'];
        $insert_data['banalce'] = round($balance, 2);
        $insert_data['user_id'] = $_SESSION['USER_ID'];
        $insert_data['entry_date'] = date('Y-m-d H:i:s');
        $insert_data['dep_transaction'] = $trans['transaction_code'];
        $insert_data['entry_client'] = $_SESSION['USER_ID'];
        $insert_data['last_access_datetime'] = date('Y-m-d H:i:s');
        $insert_data['last_access_user'] = $_SESSION['USER_ID'];
        $insert_data['last_access_branch'] = '01';

        self::update_seq($deposit_acc['account_id'], $seq_no, round($balance, 2));

        $this->db->insert('coop_deposit_atm_detail', $insert_data);
        if($this->db->affected_rows() == 0){
            self::update_seq($deposit_acc['account_id'], $_seq_no, round($_banalce, 2));
        }

    }

    public function update_seq($account, $seq_no, $balnace){
        $where = array('account_id' => $account, 'seq_no >=' => $seq_no);
        $list = $this->db->select('*')->from('coop_deposit_atm_detail')
            ->where($where)->order_by('seq_no', 'asc')->get()->result_array();
        $data_insert = [];
        $i = 0;
		//echo '<pre>'; print_r($list); echo '</pre>';		exit;
        /*if(sizeof($list)) {
            foreach ($list as $key => $item) {
                $balance = round(round($balnace, 2) - round($item['banalce']), 2);
                $data_insert[$i]['id'] = $item['id'];
                $data_insert[$i]['seq_no'] = $seq_no + 1;
                $data_insert[$i]['banalce'] = $balance;
                $i++;
            }
            $this->db->update_batch('coop_deposit_atm_detail', $data_insert, 'id');
        }
		*/
    }

    private function get_max_transaction($account_id, $datetime){
        $where['account_id'] = $account_id;
        $where['operate_date <='] = $datetime;
        return $this->db->select('*')->from('coop_deposit_atm_detail')->where($where)->order_by('seq_no', 'desc')->limit(1)->get()->row_array();
    }

    private function get_max_balance($acc, $date){
        $sql = "SELECT transaction_balance FROM coop_account_transaction a INNER JOIN (
SELECT t1.account_id, max(t1.transaction_id) as transaction_id FROM coop_account_transaction t1 
INNER JOIN (SELECT account_id, max(transaction_time) as transaction_time FROM coop_account_transaction WHERE account_id = ? AND cast(transaction_time as date) <= ? ) t2 ON t1.account_id=t2.account_id AND t1.transaction_time=t2.transaction_time
) b ON a.account_id=b.account_id AND a.transaction_id=b.transaction_id";
        return $this->db->query($sql, array($acc, $date))->row_array();
    }

    private function get_deposit_atm_account($member_id, $to_account){
        $where = ['bank_account_on' => $to_account, 'member_id' => $member_id];
        return $this->db->select('*')->from('coop_deposit_atm_account')
            ->where($where)->limit('1')->get()->row_array();
    }
	
	public function getDataReceiveById($id){
		$setting_file = $this->get_setting_loan_atm_file();
		$bank_atm = $setting_file['bank_atm'];	
			
		if($bank_atm == 'ktb'){
			$result = $this->db->select("t1.member_id,t1.saving_acc AS to_ac_number,t2.account_id,t1.transaction_amount,t1.transaction_date")->from("coop_deposit_atm_transaction_receive_file_ktb as t1")
				->join('coop_deposit_atm_account AS t2', 't1.member_id = t2.member_id AND t1.saving_acc = t2.bank_account_on', 'inner')
				->where(array("t1.file_id" => $id,"t2.account_status"=>"0"))
				->get()->result_array();
		}else{
			$result = $this->db->select("t1.member_id,t1.to_ac_number,t2.account_id,t1.transaction_amount,t1.transaction_date")->from("coop_deposit_atm_transaction_receive_file as t1")
				->join('coop_deposit_atm_account AS t2', 't1.member_id = t2.member_id AND t1.to_ac_number = t2.bank_account_on', 'inner')
				->where(array("t1.file_id" => $id,"t2.account_status"=>"0"))
				->get()->result_array();
		}
		//echo $this->db->last_query(); echo '<br>';
		//echo '<pre>'; print_r($result); echo '</pre>';	
		return $result;
    }
}


