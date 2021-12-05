<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Atm_deposit_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
	
	public function _bank_atm(){
        $setting_file = $this->atm_deposit_file->get_setting_loan_atm_file();
		$result = $setting_file['bank_atm'];
        return $result;
    }
	
	public function _file_extension(){
        $setting_file = $this->atm_deposit_file->get_setting_loan_atm_file();
		$result = $setting_file['file_extension'];
        return $result;
    }
	
	public function _file_type(){
        $setting_file = $this->atm_deposit_file->get_setting_loan_atm_file();
		$result = $setting_file['file_type'];
        return $result;
    }
	
	public function _coop_code(){
        $setting_file = $this->atm_deposit_file->get_setting_loan_atm_file();
		$result = $setting_file['coop_code'];
        return $result;
    }
	
	public function _company_account_no(){
        $setting_file = $this->atm_deposit_file->get_setting_loan_atm_file();
		$result = $setting_file['company_account_no'];
        return $result;
    }
	
	public function _bank_code(){
        $setting_file = $this->atm_deposit_file->get_setting_loan_atm_file();
		$result = $setting_file['bank_code'];
        return $result;
    }
	
    public function create($data = array(), $type = "A"){
		if($this->_bank_atm() == 'ktb'){
			$result = $this->layout_record_detail_ktb($data,$type);
		}else{
			$result = $this->layout_record_detail_kma($data,$type);
		}
        return $result;
	}
	
    public function layout_record_detail_kma($data = array(), $type = "A"){		
        $wd_info = $dp_info = $wd_limit = [];
        $coop_type_id = '1007'.$this->_coop_code();

        $set_key = array(
            'coop_type_id' => $coop_type_id,
            'coop_customer_id' => $data['member_id']
        );
        $this->text->set_keys($set_key);

        $info = self::deposit_account_info($data['account_id']);
        $member = self::member_info($data['member_id']);
        $atm_info = self::atm_deposit_account_info($data['account_id']);
        $birthday = empty($member['birthday']) ? "" : str_replace("-", "", $member['birthday']);
        $gender = $member['id_card'] == "F" ? 2 : 1;


        $exp_date = "20991231";

        if($atm_info['close_account_date'] != "" && $atm_info['sequester_status'] != "0" ) {
            if(date_create($data['operate_date']) >= date_create($atm_info['close_account_date'])) {
                $cust_status = "2";
            }else{
                $cust_status = "1";
            }
        }else{
            $cust_status = "1";
        }

        $wd_info = array(
            'data_type' => '1'.$type,
            'account_name_th' => $info['account_name'],
            'account_name_en' => $info['account_name_eng'],
            'cust_sex' => $gender,
            'cust_birthday' => $birthday,
            'card_type' =>  '01',
            'card_number' => $member['id_card'],
            'cust_addr' => '',
            'account_no' => $atm_info['bank_account_on']
        );

        $dp_info = array(
            'data_type' => '5'.$type,
            'account_name' => $info['account_name'],
            'account_id' => $data['account_id']
        );

        $wd_limit = array(
            'data_type' => '3'.$type,
            'cust_status' => $cust_status,
            'cust_exp_date' => $exp_date,
            'min_limit_amt_tran' => self::toString(100),
            'max_limit_amt_tran' => self::toString($data['banalce']),
            'daily_limit_tran' => 999,
            'daily_limit_amt' => self::toString($data['banalce']),
            'weekly_limit_tran' => 999,
            'weekly_limit_amt' => self::toString($data['banalce']*2),
            'monthly_limit_tran' => 999,
            'monthly_limit_amt' => self::toString($data['banalce']*2),
            'cust_limit_tran' => 999,
            'cust_limit_amt' => self::toString($data['banalce']),
            'withdraw' => self::toString($data['banalce']),
            'coop_account' => $atm_info['bank_account_on']
        );

       return $this->print_statements($wd_info, $dp_info, $wd_limit);
    }
	
	public function layout_record_detail_ktb($data = array(), $type = "A"){		
		$info = self::deposit_account_info($data['account_id']);
        $member = self::member_info($info['mem_id']);
        $atm_info = self::atm_deposit_account_info($data['account_id']);

		$member_id = $info['mem_id'];
		$acc_no  = trim($atm_info['bank_account_on']);
		$account_id  = trim($data['account_id']);
		$total_amount_balance = $data['approve_amount'];
		$debt_balance_amount = round($data['banalce'],2);
		$expired   = date('Y-m-d', strtotime($atm_info['approve_date']." +10 year"));

		$member_name = $member['firstname_th'];
		$memeber_surname = $member['lastname_th'];
		$person_id = $member['id_card'];
		
		//$transaction_date_last = '2020-06-21 13:20:45';
		$transaction_date_last = $data['last_access_datetime'];
		$operate_date = date("Y-m-d", strtotime($transaction_date_last));
		$operate_time = date("H:i:s", strtotime($transaction_date_last));
			
		$arr_data_detail = array(
								'acc_no' => $acc_no,
								'item_type' => 'R',
								'item_amount' => $total_amount_balance,
								'operate_date' => $operate_date,
								'operate_time' => $operate_time,
								'member_no' => $member_id,
								'balance_amount' => $debt_balance_amount,
								'expire_date' => $expired,
								'reference_key' => $account_id,
								'member_name' => $member_name,
								'memeber_surname' => $memeber_surname,
								'person_id' => $person_id
								);
		$result = $this->text->layout_atm_record_detail($arr_data_detail);
		return $result;
    }

    private function toString($amt)
    {
        return str_replace(',', '', str_replace('.', '', number_format($amt, 2)));
    }


    public function deposit_account_info($account_id){
        return $this->db->where('account_id', $account_id)->get('coop_maco_account')->row_array();
    }

    public function member_info($member_id){
        return $this->db->select('*')->from('coop_mem_apply')->where('member_id', $member_id)->get()->row_array();
    }

    public function atm_deposit_account_info($account_id){
        return $this->db->select('*')->from('coop_deposit_atm_account')->where('account_id', $account_id)->get()->row_array();
    }

    public function print_statements($wd_info, $dp_info, $wd_limit){
        $txt = "";
        $this->text->set_withdraw_info($wd_info);
        $txt .= $this->text->print_withdraw_info();
        $this->text->set_deposit_info($dp_info);
        $txt .= $this->text->print_deposit_info_st();
        $this->text->set_withdraw_limit($wd_limit);
        $txt .= $this->text->print_withdraw_limit();
        return $txt;
    }

    public function fetch($date){
        $date_start = date('Y-m-d H:i:s', strtotime($date." 00:00:00"));
        $date_end = date('Y-m-d H:i:s', strtotime($date." 23:59:59"));
        return $this->db->select("*")->from('coop_deposit_atm_detail')
            //->where(array('entry_date >=' => $date_start, 'entry_date <=' => $date_end ))
            ->where(array('operate_date >=' => $date_start, 'operate_date <=' => $date_end ))
            ->where_in('item_type', array('CHG', 'MOD', 'NEW', 'DEL'))
            ->order_by('entry_date', 'asc')
            ->get()->result_array();
    }

    public function accountInfo($condition){
        $this->db->where($condition);
        return $this->db->get('coop_maco_account')->row_array();
    }

    public function findStatementByAccount($acc){
        $sql = "SELECT R.* FROM coop_account_transaction AS R INNER JOIN (
                  SELECT A.account_id, MAX(A.transaction_id) as transaction_id FROM coop_account_transaction AS A INNER JOIN (
                  SELECT account_id, MAX(transaction_time) as transaction_time
                  FROM coop_account_transaction
                  WHERE account_id = '{$acc}'
                  GROUP BY account_id
                ) AS B ON A.transaction_time=B.transaction_time AND A.account_id=B.account_id GROUP BY A.account_id
                ) AS T ON R.transaction_id=T.transaction_id AND R.account_id=T.account_id";
        return $this->db->query($sql)->result_array();
    }

    public function detail($item = array()){
        if($item['item_type'] == "NEW"){
            return $this->create($item, 'A');
        }else if($item['item_type'] == "MOD"){
            return $this->create($item, 'C');
        }else if($item['item_type'] == "CHG"){
            return $this->create($item, 'C');
        }else if($item['item_type'] == "DEL"){
            return $this->create($item, 'D');
        }else{
            return;
        }
    }
      
    public function create_file($txt,$str_date){        
		$arr_file = $this->get_file_name($str_date);
		$file_extension = $arr_file['file_extension'];
		$file_name = $arr_file['file_name'];
		$path_file = $arr_file['path_file'];

        $file = fopen($path_file, "w") or die("Unable to open file");
        fwrite($file, $txt);
        fclose($file);
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="' . $file_name .'.'.$file_extension. '"');
        readfile($path_file);
    }

    public function header($date = ""){
        $date = $date === "" ? date('Y-m-d') : $date;
        $file_name = "coopf".$this->_coop_code();
		return $this->text->atm_deposit_header((int)$this->_coop_code(), $date, $file_name);
    }

    public function footer($row_number = 0, $limit_amt = 0, $principle_amt = 0){
		return $this->text->atm_deposit_footer($row_number, $limit_amt, $principle_amt);
    }
	
	public function get_file_name($str_date){
		$return = array();
		if($this->_bank_atm() == 'ktb'){
			$dd = date("d", strtotime($str_date)); //วันที่
			//$round = '01'; // รอบที่ (01,02)	 สหกรณ์ upload ให้ธนาคาร ก่อนเวลา 12.00 น.  และก่อน 17.00 น.ของทุกวัน 
			$time_hour = date('H');
			if($time_hour < 12){
				$round = '01';
			}else{
				$round = '02';
			}
			$file_name = "D".$dd.$this->_coop_code().$round;
		}else{
			$file_name = '1coopf'.$this->_coop_code();
		}
			
		$return['file_name'] = $file_name;	
		$return['file_extension'] = $this->_file_extension();	
		$return['path_file'] = './assets/document/'.$file_name.".".$this->_file_extension();

        return $return;
    }
	
	public function insert_deposit_atm_detail($data){
		$return = array();
		$deposit_acc = $this->db->select('*')->from('coop_deposit_atm_account')
				->where("account_id = '{$data['account_id']}' AND member_id = '{$data['member_id']}'")
				->limit(1)->get()->row_array();		
		
		if(!empty($deposit_acc)){
		   $detail = $this->db->select('*')->from('coop_deposit_atm_detail')
					->where("account_id = '{$deposit_acc['account_id']}' AND operate_date <= '{$data['transaction_date']}'")
					->order_by('seq_no', 'desc')->limit(1)->get()->row_array();
					
			$_seq_no = (int)$detail['seq_no'];
			$seq_no = (int)$detail['seq_no']+1;

			$_banalce = $detail['banalce'];
			$balance = round(round($detail['banalce'], 2)-round($data['transaction_amount'], 2), 2);

			$insert_data = [];
			$insert_data['account_id'] = $deposit_acc['account_id'];
			$insert_data['seq_no'] = $seq_no;
			$insert_data['principal_amount'] = round($data['transaction_amount'], 2);
			$insert_data['fee_amount'] = 0;
			$insert_data['operate_date'] = $data['transaction_date'];
			
			if($data['operate_code'] == '022'){
				$insert_data['item_type'] = 'WD'; //ถอน
			}else if($data['operate_code'] == '023'){
				$insert_data['item_type'] = 'CHG';	///ฝาก
				$balance = round(round($detail['banalce'], 2)+round($data['transaction_amount'], 2), 2);
			}		
		   
			$insert_data['approve_amount'] = $detail['approve_amount'];
			$insert_data['banalce'] = round($balance, 2);
			$insert_data['user_id'] = $_SESSION['USER_ID'];
			$insert_data['entry_date'] = date('Y-m-d H:i:s');
			$insert_data['dep_transaction'] = $data['transaction_code'];
			$insert_data['entry_client'] = $_SESSION['USER_ID'];
			$insert_data['last_access_datetime'] = date('Y-m-d H:i:s');
			$insert_data['last_access_user'] = $_SESSION['USER_ID'];
			$insert_data['last_access_branch'] = '01';
			
			self::update_detail_seq($deposit_acc['account_id'], $seq_no, round($balance, 2));

			$this->db->insert('coop_deposit_atm_detail', $insert_data);
			if($this->db->affected_rows() == 0){
				self::update_detail_seq($deposit_acc['account_id'], $_seq_no, round($_banalce, 2));
			}
		}
        return $return;
    }
	
	public function update_detail_seq($account, $seq_no, $balnace){
        $where = array('account_id' => $account, 'seq_no >=' => $seq_no);
        $list = $this->db->select('*')->from('coop_deposit_atm_detail')
            ->where($where)->order_by('seq_no', 'asc')->get()->result_array();
        $data_insert = [];
        $i = 0;
    }

}
