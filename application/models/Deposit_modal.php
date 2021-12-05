<?php


class Deposit_modal extends CI_Model
{

    public function __construct()
    {
        ini_set('precision', '16');
        parent::__construct();
        $this->load->model("Deposit_libraries", "Deposit_libraries");
    }

    private $_account = null;
    private $_isCall = false;

    public function current($member_id, $date = ''){
        $this->_isCall = true;
        $where = " 1=1 and mem_id = '{$member_id}' ";

        if($date){
            $date = date('Y-m-d');
            $where .= " AND close_account_date <= '{$date}' ";
            $where .= " AND account_status = 0 ";
        }
        $this->db->where_in("type_id", array_column(self::getGuaranteeDepositType(), "type_id"));
        $this->_account = $this->db->get_where('coop_maco_account', $where)->result_array();

        //echo $this->db->last_query(); exit;
        return $this;
    }

    public function getGuaranteeDepositType(){
        return $this->db->get_where("coop_deposit_type_setting", array("using_guarantee_loan" => 1))->result_array();
    }

    public function getAccount(){
        self::exceptionAccount();
        return $this->_account;
    }

    private function exceptionAccount(){
        try{

            if($this->_account == null && !$this->_isCall) throw new Exception("Must be Call account() Function");

        }catch (Exception $e){
            show_error($e->getTraceAsString(), $e->getCode(), $e->getMessage());
        }
    }

    public function nonInterest(){
        $this->db->where_not_in('transaction_list' , array('INT'));
        return $this;
    }

    public function getBalance($account_id){
        $this->db->select('*');
        $this->db->from('coop_account_transaction');
        $this->db->where("account_id = '".$account_id."'");
        $this->db->order_by('transaction_time DESC, transaction_id DESC');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function getBalanceAll(){
        self::exceptionAccount();
        $result = 0;
        foreach ($this->_account as $key => $account){
            $result += self::getBalance($account['account_id'])->transaction_balance;
        }
        return $result;

    }

    public function getBalanceList(){
        self::exceptionAccount();
        $result = [];
        $num = 0;
        foreach ($this->_account as $key => $acc){
            $result[$num] = $acc;
            $result[$num]['balance'] = self::nonInterest()->getBalance($acc['account_id'])->transaction_balance;
            $num++;
        }

        return $result;
    }

    public function getAccountCount(){
        return sizeof($this->_account);
    }

    public function getDeposit(){

        return $this;
    }

    /**
     * ยอดเงินคงเหลือในบัญชีที่ต้องใช้ในการประเมิณ
     * @param $member_id
     * @return mixed
     */
    public function guaranteeAccount($member_id){
        $acc_type = '10'; //Todo เพิ่มตารางตั้งค่าการบัญชีหลักเกณการกู้
        $act = $this->db->get_where('coop_maco_account',
            "mem_id='{$member_id}' and account_id like '{$acc_type}%' and account_status=0", 1)->row();

        return self::nonInterest()->getBalance($act->account_id)->transaction_balance;

    }
	
	/*	จำนวนวันในปีที่ใช้ในการคำนวณ
		1=365
		2=366
		3=depend of calendar
	*/
	function get_day_of_year_by_type($type, $date) {
		if($type == 2) {
			return 366;
		} else if ($type == 3) {
			return date("z", strtotime(substr($date, 0, 4)."-12-31")) + 1;
		} else {
			return 365;
		}
	}
	
	function check_interest_step_year($date_start,$date_end){
		$arr_data = array();
		$src_year = date("z", strtotime(substr($date_start, 0, 4)."-12-31")) + 1;
		$des_year = date("z", strtotime(substr($date_end, 0, 4)."-12-31")) + 1;

		$_date_start = date("Y-m-d", strtotime($date_start));
		if($date_start <= $date_end){
			if($src_year == $des_year){
				$arr_data[0]['date_start'] = $date_start;
				$arr_data[0]['date_end'] = $date_end;			
			}else{
				$date_middle = date("Y", strtotime($date_end)).'-01-01';

				$arr_data[0]['date_start'] = $date_start;
				$arr_data[0]['date_end'] = $date_middle;
				
				$arr_data[1]['date_start'] = $date_middle;
				$arr_data[1]['date_end'] = $date_end;
			}
		}
		return $arr_data;
	}	
	
	function check_interest_step($date_start,$date_end, $type_id){
		$this->db->select(array(
			't1.type_detail_id',
			't1.type_id',
			't2.num_month',
			't1.percent_depositor',
			't2.percent_interest as interest_rate',					
			'DATE(t1.start_date) AS start_date'
		));
		$this->db->from('coop_deposit_type_setting_detail as t1');
		$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
		$this->db->where("t1.type_id = '{$type_id}'");
		$this->db->order_by("start_date ASC");
		$row_interest_rate = $this->db->get()->result_array();
		
		$arr_interest_rate = array();
		$i=0;
		foreach($row_interest_rate as $key2 => $value2){
			//$start_date_interest = $value2['start_date'];
			$start_date_interest = ($date_start <= $value2['start_date'])?$value2['start_date']:$date_start;
			//echo @$row_interest_rate[($key2+1)]['start_date'].'<br>';
			//if(@$row_interest_rate[($key2+1)]['start_date'] != ''){
			if(@$row_interest_rate[($key2+1)]['start_date'] != '' && $date_end >= @$row_interest_rate[($key2+1)]['start_date']){
				$end_date_interest = date('Y-m-d',strtotime($row_interest_rate[($key2+1)]['start_date']));
			}else{
				$end_date_interest = $date_end;
			}
	
			if(strtotime($date_start) >= strtotime($start_date_interest) && strtotime($date_start) <= strtotime($end_date_interest) || strtotime($date_end) <= strtotime($end_date_interest)){	
				//echo $start_date_interest.','.$end_date_interest.'<br>';
				$arr_new = $this->check_interest_step_year($start_date_interest,$end_date_interest);
				//echo '<pre>'; print_r($arr_new); echo '</pre>';
				if(!empty($arr_new)){
					foreach($arr_new AS $val_new){						
						$arr_interest_rate[$i] = $value2;
						$arr_interest_rate[$i]['start_date'] = $val_new['date_start'];
						$arr_interest_rate[$i]['end_date'] = $val_new['date_end'];
						$i++;
					}					
				}
			}
		}
		return $arr_interest_rate;
	}

	public function cal_accu_int($data){
		//$this->load->model("Deposit_modal", "deposit_modal");
		$account_id = $data['account_id'];
		$date_end = $data['date_cal'];
		$where = "";
		if($data['date_start_cal'] != ''){
			//if($data['transaction_id'] != ''){
			//$date_start_cal = $data['date_start_cal'];
			//$where .= " AND DATE(t1.transaction_time) < '{$data['date_start_cal']}' ";
			$where .= " AND t1.transaction_time < '{$data['date_start_cal']}' ";
			//$where .= " AND t1.transaction_id < '{$data['transaction_id']}' ";
		}

		$data_last = $this->db->select("DATE(t1.transaction_time) AS transaction_time,
													t1.old_acc_int,
													t1.accu_int_item,
													t1.transaction_balance,
													t2.type_id,
													t2.account_status")
										->from("coop_account_transaction  AS t1")
										->join("coop_maco_account AS t2","t1.account_id = t2.account_id	","inner")
										//->where("t1.account_id = '{$account_id}'  AND t2.account_status = '0' {$where}")
										//เปลี่ยนมาเช็ควันที่ด้วย เนื่องจากหากมีการทำรายการในวันเดียวกัน จะไม่คิดดอกเบี้ย
										->where("t1.account_id = '{$account_id}'  AND t2.account_status = '0' AND DATE(t1.transaction_time) <= '{$date_end}' {$where}")
										->order_by('t1.transaction_time DESC,t1.transaction_id DESC ')
										->limit(1)
										->get()->row_array();
										//echo $this->db->last_query();
		$check_amount_min_no_interest = $this->db->select("amount_min_no_interest")
							->from("coop_deposit_type_setting_detail")
							->where("type_id = '{$data_last['type_id']}' ")
							->order_by('start_date DESC')
							->limit(1)
							->get()->row_array();
		$amount_min_no_interest=$check_amount_min_no_interest['amount_min_no_interest'];

		if(!empty($data_last)){								
			$last_old_acc_int = $data_last['old_acc_int'];
			$transaction_balance = $data_last['transaction_balance'];
			$date_start = $data_last['transaction_time'];		
			$type_id = $data_last['type_id'];

			$arr_interest_step = $this->check_interest_step($date_start, $date_end, $type_id);

			$arr_data = array();
			$accu_int_item = 0;
			
			$interest_rate = $val['interest_rate'];
			$formula = '1';
			$accu_int_arr =  $this->deposit_libraries->_cal_interest($transaction_balance, $interest_rate, $date_start, $date_end, $type_id ,$formula);
			//echo '<pre>'; print_r($accu_int_arr); echo '</pre>';
			$arr_data = $accu_int_arr;
			if(!empty($accu_int_arr['cal'])){
				foreach($accu_int_arr['cal'] AS $key=>$val){	
					$accu_int_item += $val['accu_int_item'];
				}
			}
			
			//เช็คตั้งค่าเงินฝาก type_interest=5 เช็คbalanceก่อนว่าน้อบกว่าamount_min_no_interestหรือไม่
			if(!empty($amount_min_no_interest)){
				$accu_int_item = ($transaction_balance < $amount_min_no_interest)?$accu_int_item="0":$accu_int_item;
			}

			$old_acc_int = $last_old_acc_int+$accu_int_item;
			$arr_data['account_id'] = $account_id;
			$arr_data['accu_int_item'] = number_format($accu_int_item, 2, '.', '');
			$arr_data['old_acc_int'] = number_format($old_acc_int, 2, '.', '');
			$arr_data['date_start'] = $date_start;
			$arr_data['date_end'] = $date_end;
		}else{
			$arr_data = array();
		}
		//echo '<pre>'; print_r($arr_data); echo '</pre>';
		return $arr_data;
		exit;
    }

    private function getSettingDeposit($type_detail_id){
	    return $this->db->get_where("coop_deposit_type_setting_detail", "type_detail_id={$type_detail_id}")->row_array();
    }

    private function getSettingDepositInterest($type_detail_id, $condition_interest){
	    $this->db->order_by("amount_deposit", "desc");
        return $this->db->get_where("coop_deposit_type_setting_interest", "type_detail_id={$type_detail_id} AND condition_interest='{$condition_interest}' AND percent_interest > 0")->result_array();
    }

    private function calc_interest_step_with_balance($data = array(), $range = array(), $setting = array()){

	    $rate = self::getSettingDepositInterest($setting['type_detail_id'], $setting['condition_interest']);

        $accu_int_item = 0;
        $res = array();

        foreach ($rate as $key => $item){

            if($data['transaction_balance'] >= $item['amount_deposit']) {

                $days_of_year = $this->get_day_of_year_by_type($item['type_id'], $range['start_date']);
                $diff = date_diff(date_create($range['start_date']), date_create($range['end_date']));
                $date_count = $diff->format("%a");
                $interest_rate = $item['percent_interest'];
                $accu_int_item_rate = ROUND(((($data['transaction_balance'] * $item['percent_interest']) / 100) / $days_of_year) * $date_count, 2);

                $res['start_date'] = $range['start_date'];
                $res['end_date'] = $range['end_date'];
                $res['interest_rate'] = $interest_rate;
                $res['days_of_year'] = $days_of_year;
                $res['date_count'] = $date_count;
                $res['transaction_balance'] = $data['transaction_balance'];
                $res['accu_int_item'] = number_format($accu_int_item_rate, 2, '.', '');
                $res['formula'] = 'ROUND((((' . $data['transaction_balance'] . '*' . $interest_rate . ')/100)/' . $days_of_year . ')*' . $date_count . ',2)';

                break;
            }

        }

	    return $res;

    }
	
	public function get_time_transaction($account_id,$date_transaction){        
		$data = $this->db->select('DATE(transaction_time) AS date_last,TIME(transaction_time) AS time_last')
			->from('coop_account_transaction')
			->where("account_id = '{$account_id}' AND DATE(transaction_time) = '{$date_transaction}'")
			->order_by('transaction_time DESC, transaction_id DESC')
			->limit(1)->get()->row_array();
		//echo $this->db->last_query();	
        return $data;
    }

	public function get_coop_bank(){ 
		//เช็คข้อมูลบัญชีธนาคารจากข้อมูลหลัก
		$data_main = $this->db->select('bank_id,bank_code,bank_name')
			->from('coop_bank_main')
			->order_by('bank_name ASC')
			->group_by('bank_id')
			->get()->result_array();

		if(!empty($data_main)){
			$data = $data_main;
		}else{
			$data_default = $this->db->select('*')
				->from('coop_bank')
				->order_by('bank_name ASC')
				->get()->result_array();
			//echo $this->db->last_query();	
			$data = $data_default;
		}
        return $data;
    }

	public function get_coop_bank_branch($bank_id){ 
		//เช็คข้อมูลบัญชีธนาคารจากข้อมูลหลัก
		$data_main = $this->db->select('t1.* ,t2.branch_name')
			->from('coop_bank_main AS t1')
			->join("coop_bank_branch AS t2","t1.branch_id = t2.branch_id","left")
			->where("t1.bank_id = '{$bank_id}'")
			->order_by('t2.branch_id ASC')
			->get()->result_array();
		if(!empty($data_main)){
			$data = $data_main;
		}else{
			$data_default = $this->db->select('*')
				->from('coop_bank_branch')
				->where("bank_id = '{$bank_id}'")
				->order_by('branch_id ASC')
				->get()->result_array();
			//echo $this->db->last_query();	
			$data = $data_default;
		}
        return $data;
    }
}
