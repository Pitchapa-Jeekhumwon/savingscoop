<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Loan_libraries extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
		# Load libraries
		//$this->load->library('parser');
		$this->load->helper(array('html', 'url'));
        $this->load->model("Setting_model", 'settingModel');
        self::setPrecision();
	}

	private $_precision = null;

	public function setPrecision(){
        if($this->_precision == null){
            $this->_precision =  $this->settingModel->get('round_interest');
        }
    }
	private function _precision(){
	    return $this->_precision;
    }

	public function lastSeqContractNumber($year, $loan_type){

		//ปรับการ gen เลขให้ใช้กลุ่มเงินกู้	
		$loan_name = $this->db->select("*")->from("coop_loan_name")->where("loan_name_id='{$loan_type}'")->get()->row();

        $this->db->select('run_contract_number');
        $this->db->from("coop_loan_contract_number");
		$this->db->where("contract_year = '" . $year . "' AND loan_group_id = '" . $loan_name->loan_group_id . "'");
        $this->db->order_by("run_id DESC");
        $this->db->limit(1);

        $rs_contact_number = $this->db->get()->result_array();
        $row_contact_number = @$rs_contact_number[0];


        if(@$row_contact_number['run_contract_number']==''){
            $contact_number_now = '1';
        }else{
            $contact_number_now = $row_contact_number['run_contract_number'];
            (int)$contact_number_now++;
        }

        return ['loan_type_id'=>$loan_name->loan_type_id,'loan_group_id'=>$loan_name->loan_group_id,'contract_number'=>$contact_number_now];
    }

    public function get_contract_number($year, $date_approve, $loan_type = '', $debug = 0){
		$day 		= date("d", strtotime($date_approve));
		$month 		= date("m", strtotime($date_approve));
        //SET ATM type_id
        if($loan_type == ''){
            $loan_type = 999999;
		}

		$contact_now = self::lastSeqContractNumber($year, $loan_type);
		$contact_number_now = $contact_now['contract_number'];
		$contact_loan_group_id = $contact_now['loan_group_id'];
		$contact_loan_type_id = $contact_now['loan_type_id'];

		$this->db->select('*');
		$this->db->from("coop_term_of_loan");
		$this->db->where("type_id = '".@$loan_type."' AND start_date <= '".($year-543)."-".$month."-".$day."'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$rs_term_of_loan = $this->db->get()->result_array();
		$row_term_of_loan = @$rs_term_of_loan[0];
		// Y - A four digit representation of a year
		// y - A two digit representation of a year
		// YTH - A four digit representation of a year THAI
		// yth - A two digit representation of a year THAI
		// m - A numeric representation of a month (from 01 to 12)
		// n - A numeric representation of a month, without leading zeros (1 to 12)
		// RUNNO1 - เลขรันตามประเภทเงินกู้ 1
		// RUNNO2 - เลขรันตามประเภทเงินกู้ 01
		// RUNNO3 - เลขรันตามประเภทเงินกู้ 001
		// RUNNO4 - เลขรันตามประเภทเงินกู้ 0001
		// RUNNO5 - เลขรันตามประเภทเงินกู้ 00001
		// RUNNO6 - เลขรันตามประเภทเงินกู้ 000001
		
		//e.g. "YTH/RUNNO4" = 2563/0074
		//e.g. "YTHnRUNNO6" = 256309000027
		//e.g. "PREFIX.ythRUNNO5" = สห.6303066
		$pattern = $row_term_of_loan['contract_pattern'];
		$str = preg_replace('/PREFIX/u', $row_term_of_loan['prefix_code'], $pattern);
		$str = preg_replace('/YTH/u', ($year), $str);
		$str = preg_replace('/yth/u', substr(($year),2,4), $str);
		$str = preg_replace('/Y/u', ($year-543), $str);
		$str = preg_replace('/y/u', substr(($year-543),2,4), $str);
		$str = preg_replace('/m/u', sprintf("%02d",$month), $str);
		$str = preg_replace('/n/u', (int)$month, $str);
		$str = preg_replace('/RUNNO1/u', sprintf("%01d",$contact_number_now), $str);
		$str = preg_replace('/RUNNO2/u', sprintf("%02d",$contact_number_now), $str);
		$str = preg_replace('/RUNNO3/u', sprintf("%03d",$contact_number_now), $str);
		$str = preg_replace('/RUNNO4/u', sprintf("%04d",$contact_number_now), $str);
		$str = preg_replace('/RUNNO5/u', sprintf("%05d",$contact_number_now), $str);
		$str = preg_replace('/RUNNO6/u', sprintf("%06d",$contact_number_now), $str);

		if($loan_type == 999999){
		    $str = sprintf("%06d", $contact_number_now);
        }

		if($debug) {
		    return $str;
        }else{
            $data_insert = array();
       	 	$data_insert['contract_year'] = $year;
        	$data_insert['run_contract_number'] = $contact_number_now;
        	$data_insert['loan_type'] = $loan_type;
        	$data_insert['loan_name_id'] =$contact_loan_type_id; 
        	$data_insert['loan_group_id'] = $contact_loan_group_id;
        	$data_insert['createdatetime'] = date('Y-m-d H:i:s');
        	$this->db->insert('coop_loan_contract_number',$data_insert);
        }
        return $str;
    }

    public function getPetitionNumber($year, $loan_type, $str,$group = 0, $debug = 0){
        $number = 0;
        $petition_run_type = 1;//ค่าตั้งต้น
        $petition_run_setting = $this->db->select('setting_value')->from('coop_setting')->where('setting_name = "loan_petition_run"')->get()->row_array();

        if(!empty($petition_run_setting)) {
            $petition_run_type = $petition_run_setting['setting_value'];
        }

        if($petition_run_type == 2) {
            //run by loan type group.
            $loan_name = $this->db->select("loan_group_id, loan_type_id")->from("coop_loan_name")->where("loan_name_id='{$loan_type}'")->get()->row();

            $loan_petition_number = $this->db->select('run_number')
                                                ->from("coop_loan_petition_number")
                                                ->where("year='".$year."' AND loan_group_id='".$loan_name->loan_group_id."' and status = 1")
                                                ->order_by("id DESC")
                                                ->get()->row_array();

            if(!empty($loan_petition_number)) {
                $number = $loan_petition_number['run_number']+1;
            } else {
                $number = 1;
            }

            if($debug == 0) {
                $data_insert = array();
                $data_insert['run_number'] = $number;
                $data_insert['createdatetime'] = date('Y-m-d H:i:s');
                $data_insert['year'] = $year;
                $data_insert['loan_type'] = $loan_name->loan_type_id;
                $data_insert['loan_name_id'] = $loan_type;
                $data_insert['loan_group_id'] = $loan_name->loan_group_id;
                $data_insert['status'] = 1;
                $data_insert['user_id'] = @$_SESSION['USER_ID'];
                $data_insert['created_at'] = date('Y-m-d H:i:s');
                $data_insert['updated_at'] = date('Y-m-d H:i:s');
                $this->db->insert('coop_loan_petition_number',$data_insert);
            }
        } else {
            //run by prefix.
            $prefix = substr($str, 0, -6);
            $petition_number =  $this->getPetitionByPrefix($prefix,$group)['petition_number'];
            $number = (int)str_replace($prefix, '',  $petition_number);
            if($number){
                $number++;
            }else{
                $number = 1;
            }
        }
        return $number;
    }

    private function getPetitionByPrefix($str,$group = 0){
	    return $this->db->order_by("petition_number", "desc")->join('coop_loan_name','loan_type=loan_name_id','inner')
		->where('loan_group_id',$group)->get_where("coop_loan", "petition_number like '%".$str."%'", 1)->row_array();
    }

    public function createPetitionNumber($year, $date_approve, $loan_type = ''){
        $day 		= date("d", strtotime($date_approve));
        $month 		= date("m", strtotime($date_approve));
        //SET ATM type_id
        if($loan_type == ''){
            $loan_type = 999999;
        }


        $this->db->select('*');
        $this->db->from("coop_term_of_loan");
        $this->db->where("type_id = '".@$loan_type."' AND start_date <= '".($year-543)."-".$month."-".$day."'");
        $this->db->order_by('start_date DESC');
        $this->db->limit(1);
        $row_term_of_loan = $this->db->get()->row_array();
        $pattern = $row_term_of_loan['petition_pattern'];
		$group =  $this->db->select('loan_group_id')->get_where('coop_loan_name',"loan_name_id = '$loan_type' ",1)->row_array()['loan_group_id'];
		$rs_month_account = $this->db->select('accm_month_ini')->from("coop_account_period_setting")->get()->row_array();
		$month_account = $rs_month_account['accm_month_ini'];

		if((int)$month >= (int)$month_account && (int)$month_account != 1){
			$year = $year+1;
		}else{
			$year = $year;
		}

        // Y - A four digit representation of a year
        // y - A two digit representation of a year
        // YTH - A four digit representation of a year THAI
        // yth - A two digit representation of a year THAI
        // m - A numeric representation of a month (from 01 to 12)
        // n - A numeric representation of a month, without leading zeros (1 to 12)
        // RUNNO1 - เลขรันตามประเภทเงินกู้ 1
        // RUNNO2 - เลขรันตามประเภทเงินกู้ 01
        // RUNNO3 - เลขรันตามประเภทเงินกู้ 001
        // RUNNO4 - เลขรันตามประเภทเงินกู้ 0001
        // RUNNO5 - เลขรันตามประเภทเงินกู้ 00001
        // RUNNO6 - เลขรันตามประเภทเงินกู้ 000001

        //e.g. "YTH/RUNNO4" = 2563/0074
        //e.g. "YTHnRUNNO6" = 256309000027
        //e.g. "PREFIX.ythRUNNO5" = สห.6303066

        $str = preg_replace('/PREFIX/u', $row_term_of_loan['prefix_petition'], $pattern);
        $str = preg_replace('/YTH/u', ($year), $str);
        $str = preg_replace('/yth/u', substr(($year),2,4), $str);
        $str = preg_replace('/Y/u', ($year-543), $str);
        $str = preg_replace('/y/u', substr(($year-543),2,4), $str);
        $str = preg_replace('/m/u', sprintf("%02d",$month), $str);
        $str = preg_replace('/n/u', (int)$month, $str);

        $petition = self::getPetitionNumber($year, $loan_type, $str, $group, 0);
        $str = preg_replace('/RUNNO1/u', sprintf("%01d",$petition), $str);
        $str = preg_replace('/RUNNO2/u', sprintf("%02d",$petition), $str);
        $str = preg_replace('/RUNNO3/u', sprintf("%03d",$petition), $str);
        $str = preg_replace('/RUNNO4/u', sprintf("%04d",$petition), $str);
        $str = preg_replace('/RUNNO5/u', sprintf("%05d",$petition), $str);
        $str = preg_replace('/RUNNO6/u', sprintf("%06d",$petition), $str);

        if($loan_type == 999999){
            $str = sprintf("%06d", $petition);
        }
        return $str;
    }

    public function get_petition_atm_number(){
        $setting = $this->db->get('coop_loan_atm_setting', 1)->row_array();
        $prefix = substr(date('Y')+543, 2, 2);
        $prefix .= $setting['prefix_code'];

        $this->db->select(array('petition_number'));
        $this->db->from('coop_loan_atm');
        $this->db->order_by('petition_number DESC');
        $this->db->limit(1);
        $row_petition_number = $this->db->get()->row_array();
        if(!empty($row_petition_number)){
            $petition_number = ((int)mb_substr($row_petition_number['petition_number'], 4, 5))+1;
            $petition_number = sprintf('%05d',$petition_number);
        }else{
            $petition_number = sprintf('%05d',1);
        }
        return $prefix.$petition_number;
    }
	
	public function loan_transaction($data){
		$data_insert = array();
		$data_insert['loan_id'] = $data['loan_id'];
		$data_insert['loan_amount_balance'] = $data['loan_amount_balance'];
		$data_insert['transaction_datetime'] = $data['transaction_datetime'];
		if(@$data['receipt_id']!=''){
			$data_insert['receipt_id'] = $data['receipt_id'];
		}
		$this->db->insert('coop_loan_transaction',$data_insert);
		return 'success';
	}

    public function loan_transaction_arrears($action, $data){
        $this->load->model('Loan_calculator_model', 'LoanCalc');

        if($data['receipt_id'] != 'NEW'){
            $calc_arrears = array();
            $calc_arrears['loan_id'] = $data['loan_id'];
            $calc_arrears['entry_date']  = $data['transaction_datetime'];

            $calc_arrears['loan_type'] = $this->LoanCalc->get_loan_type($data['loan_id']);
            if($action == "RM") {

                $calc_arrears['principal'] = $data['principal'];
                $calc_arrears['interest'] = $data['interest'];
                $calc_arrears['action'] = 'return';
            }else if($action === "AD"){
                $calc_arrears['action'] = "add";
                $calc_arrears['add'] = $data['add'];
            }else if(in_array($action, array('PL', 'PM'))){

                $calc_arrears['interest'] = $data['interest'];
                $calc_arrears['action'] = 'payment';
            }else{
                $calc_arrears['interest']   = $data['interest'];
            }

            $arr_insert = $this->LoanCalc->calc($action, $calc_arrears);

            if(!empty($arr_insert)){
                foreach($arr_insert AS $key=>$val){
                    if($key != 'data_cal_int'){
                        $data_insert[$key] = $val;
                    }
                }
            }
        }

        if(isset($data['interest_over']) && $data['interest_over'] == 0){
            $data_insert['interest_arrear_bal'] = 0;
        }

        if(isset($data['interest_over_new']) && !empty($data['interest_over_new'])){
            $data_insert['interest_arrear_bal'] = $data['interest_over_new'];
        }
        $data_insert['loan_type_code'] = $action;
        $data_insert['loan_id'] = $data['loan_id'];
        if( $action == "RM" && !empty($arr_insert['loan_amount_balance'])) {
            $data_insert['loan_amount_balance'] = $arr_insert['loan_amount_balance'];
        }else if( $action == "AD" && !empty($arr_insert['loan_amount_balance'])) {
            $data_insert['loan_amount_balance'] = $arr_insert['loan_amount_balance'];
        }else{
            $data_insert['loan_amount_balance'] = $data['loan_amount_balance'];
        }
        if($action == "RM" &&
            date_create(date('Y-m-d', strtotime($data['transaction_datetime']))) ==
            date_create(date('Y-m-d', strtotime($arr_insert['transaction_datetime'])))
        ){
            $data_insert['transaction_datetime'] = date('Y-m-d', strtotime($data['transaction_datetime']))." ".
                date("H:i:s", strtotime($arr_insert['transaction_datetime']));
        }else{
            $data_insert['transaction_datetime'] = $data['transaction_datetime'];
        }
        if(@$data['receipt_id']!=''){
            $data_insert['receipt_id'] = $data['receipt_id'];
        }

        $this->db->insert('coop_loan_transaction',$data_insert);
        return 'success';
    }
	
	public function atm_transaction($data){
		$data_insert = array();
		$data_insert['loan_atm_id'] = $data['loan_atm_id'];
		$data_insert['loan_amount_balance'] = $data['loan_amount_balance'];
		$data_insert['transaction_datetime'] = $data['transaction_datetime'];
		if(@$data['receipt_id']!=''){
			$data_insert['receipt_id'] = $data['receipt_id'];
		}
		$this->db->insert('coop_loan_atm_transaction',$data_insert);
		return 'success';
	}

    public function atm_transaction_arrears($action, $data){

        $this->load->model('Atm_calculator_model', 'ATMCalc');

        $calc_arrears = array();
        $calc_arrears['loan_atm_id'] = $data['loan_atm_id'];
        $calc_arrears['entry_date']  = $data['transaction_datetime'];
        $calc_arrears['interest']    = $data['interest'];
        if(isset($data['receipt_id']) && !empty($data['receipt_id'])) {
            $calc_arrears['action'] = 'payment';
        }
        if($action == "RM"){
            $calc_arrears['principal']  = $data['principal'];
            $calc_arrears['interest']   = $data['interest'];
            $calc_arrears['action']     = 'return';
        }else{
            $calc_arrears['interest']   = $data['interest'];
        }
        //$data_insert = array();
        $data_insert = $this->ATMCalc->calc($action, $calc_arrears);

        if(isset($data['interest_over']) &&  $data['interest_over'] == 0){
            $data_insert['interest_arrear_bal'] = 0;
        }

        if(isset($data['interest_over']) && !empty($data['interest_over_new'])){
            $data_insert['interest_arrear_bal'] = $data['interest_over_new'];
        }
        if($action != "RM" && $data['loan_amount_balance'] != "") {
            $data_insert['loan_amount_balance'] = round($data['loan_amount_balance'], self::_precision());
        }
        $data_insert['loan_type_code'] = $action;
        $data_insert['loan_atm_id']    = $data['loan_atm_id'];

        if(@$data['receipt_id']!=''){
            $data_insert['receipt_id'] = $data['receipt_id'];
        }

        if($action == "RM" &&
            date_create(date('Y-m-d', strtotime($data['transaction_datetime']))) ==
            date_create(date('Y-m-d', strtotime($data_insert['transaction_datetime'])))
        ){
            $data_insert['transaction_datetime'] = date('Y-m-d', strtotime($data['transaction_datetime']))." ".
            date("H:i:s", strtotime($data_insert['transaction_datetime']));
        }else{
            $data_insert['transaction_datetime'] = $data['transaction_datetime'];
        }
        $this->db->insert('coop_loan_atm_transaction',$data_insert);
        return 'success';
    }
	
	public function cal_atm_interest($data,$return_type='echo'){
		$this->db->select('*');
		$this->db->from('coop_loan_atm_setting');
		$row = $this->db->get()->result_array();
		$loan_atm_setting = @$row[0];
		$this->db->select('date_last_interest');
		$this->db->from('coop_loan_atm');
		$this->db->where("
			loan_atm_id = '".$data['loan_atm_id']."'
		");
		$row = $this->db->get()->result_array();
		$row_last_transaction = @$row[0];
		if(@$row_last_transaction['date_last_interest']!=''){
			$last_payment_date = $row_last_transaction['payment_date'];
		}else{
			$this->db->select('loan_date');
			$this->db->from('coop_loan_atm_detail');
			$this->db->where("
				loan_atm_id = '".$data['loan_atm_id']."'
			");
			$this->db->order_by("loan_id ASC");
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			$last_payment_date = @$row[0]['loan_date'];
		}
		$this->db->select('*');
		$this->db->from('coop_loan_atm_transaction');
		$this->db->where("
			loan_atm_id = '".$data['loan_atm_id']."'
			AND transaction_datetime >= '".$last_payment_date."'
		");
		$this->db->order_by("loan_atm_transaction_id ASC");
		$row = $this->db->get()->result_array();
		$atm_transaction = array();
		$i=0;
		foreach($row as $key => $value){
			$atm_transaction[$i]['loan_amount_balance'] = $value['loan_amount_balance'];
			$date_start = explode(' ',$value['transaction_datetime']);
			$atm_transaction[$i]['date_start'] = $date_start[0];
			if(@$row[$key+1]['transaction_datetime']!=''){
				$date_end = explode(' ',$row[$key+1]['transaction_datetime']);
				$atm_transaction[$i]['date_end'] = $date_end[0];
			}else{
				$atm_transaction[$i]['date_end'] = $data['date_interesting'];
			}
			$diff = date_diff(date_create($atm_transaction[$i]['date_start']),date_create($atm_transaction[$i]['date_end']));
			$date_count = $diff->format("%a");
			if($date_count == 0){
				//$date_count = $date_count+1;
			}
			$atm_transaction[$i]['date_count'] = $date_count;
			$interest = ((($atm_transaction[$i]['loan_amount_balance']*$loan_atm_setting['interest_rate'])/100)/365)*$atm_transaction[$i]['date_count'];
			$atm_transaction[$i]['origin_interest'] = $interest;
			$interest = round($interest);
			$atm_transaction[$i]['interest_rate'] = $loan_atm_setting['interest_rate'];
			$atm_transaction[$i]['interest'] = $interest;
			$i++;
		}
		$atm_transaction_tmp = array();
		foreach($atm_transaction as $key => $value){
			$atm_transaction_tmp[$value['date_start']] = $value;
		}
		$atm_transaction = $atm_transaction_tmp;
		$interest_amount = 0;
		foreach($atm_transaction as $key => $value){
			$interest_amount += $value['interest'];
		}
		//echo "<pre>";print_r($atm_transaction);echo"</pre>";exit;
		if($return_type == 'echo'){
			return $interest_amount;
		}else{
			return $atm_transaction;
		}
	}
	/*-----------------------------------------------------------
	// $type_count_date is array("month" => XX, "year" => XX) จะเป็นตัวนับจำนวนวันในเดือนนั้นๆ ใช้ในการคำนวณดอกเบี้ยออกรายการเรียกเก็บประชำเดือน
	------------------------------------------------------------*/
	
	public function cal_atm_interest_report_test($data,$return_type='echo',$type_count_date="",$is_process=true, $is_counter=false){
		if(@$_GET['debug']){
			var_dump($data);
		}
		if(@$_GET['excel']){
			header('Content-Encoding: UTF-8');
			header('Content-type: text/csv; charset=UTF-8');
			header('Content-Disposition: attachment; filename="'.sprintf("%06d",$_GET['member_id']).' | createtime '.date("Y-m-d H:i:s").'.csv"');
			echo "\xEF\xBB\xBF";
			// $fp = fopen($data["loan_atm_id"].'.csv', 'w');
			$fp = fopen('php://output', 'wb+');
		}
		// $data['date_interesting'] = "2020-07-09";

		$coop_loan_atm = $this->db->get_where("coop_loan_atm", array(
			"loan_atm_id" => $data['loan_atm_id']
		))->result_array()[0];

		$date_interesting = date("Y", strtotime(
		$data['date_interesting'])).'-'.date("m", strtotime($data['date_interesting'])).'-01';
		// $date_calc_interest_counter = date("Y-m-", strtotime($data['date_interesting']))."01";

		$arr_is_atm 					= array();
		$arr_datetime_last_process 		= array();
		for ($i=0; $i < 3; $i++) { 
			$this->db->join("coop_finance_month_profile", 
			"coop_finance_month_profile.profile_id = coop_finance_month_detail.profile_id and coop_finance_month_profile.profile_year = ".(date("Y", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))) )+543).
			" and coop_finance_month_profile.profile_month = ". (date("m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))) )), 
			"inner");
			$last_process_month 			= @$this->db->get_where("coop_finance_month_detail", array(
				"loan_atm_id" => $data['loan_atm_id']
			))->result_array()[0];
			$arr_datetime_last_process[date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))]			= $last_process_month["create_datetime"];
			//เอาไว้ใช้เช็คการเริ่มนับ ดอกเบี้ยสะสม
			
			if($arr_datetime_last_process[date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))]!=""){
				$this->db->where("loan_date > '".$arr_datetime_last_process[date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))]."'");
				$this->db->where("loan_date <= '".date("Y-m-t", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))."'");
			}else{
				$this->db->where("loan_date like '".date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))."%'");
			}
			$this->db->where("loan_atm_id", $data['loan_atm_id']);
			$is_atm_transaction 			= @$this->db->get_where("coop_loan_atm_detail")->result_array()[0];
			$arr_is_atm[date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))] = (!empty($is_atm_transaction)) ? true : false;
			// echo "<br>";
			// echo $this->db->last_query().",<br> ";

			$this->db->join("coop_finance_month_profile", 
			"coop_finance_month_profile.profile_id = coop_finance_month_detail.profile_id and coop_finance_month_profile.profile_year = ".(date("Y", strtotime("-".($i-1)." month", strtotime($data['date_interesting'])) )+543).
			" and coop_finance_month_profile.profile_month = ". (date("m", strtotime("-".($i-1)." month", strtotime($data['date_interesting'])) )), 
			"inner");
			$last_process_month 			= @$this->db->get_where("coop_finance_month_detail", array(
				"loan_atm_id" => $data['loan_atm_id']
			))->result_array()[0];
			$arr_reset_datetime_last_process[date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))]			= $last_process_month["create_datetime"];
			//เอาไว้ใช้เช็คการเริ่ม reset การกดดอกเบี้ย atm สะสม
			$arr_is_reset[date("Y-m", strtotime("-".$i." month", strtotime($data['date_interesting'])))]	= true;

			$this->db->where("loan_atm_id", $data['loan_atm_id']);
			$this->db->where("loan_date like '".date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))."%'");
			$this->db->where("loan_date >= '".date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))."-01"."'");
			if($arr_reset_datetime_last_process[date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))] != ""){
				$this->db->where("loan_date <= '".$arr_reset_datetime_last_process[date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))]."'");
			}
			$is_atm_transaction_before_process 			= @$this->db->get_where("coop_loan_atm_detail")->result_array()[0];
			$arr_is_atm_before_process[date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))] = (!empty($is_atm_transaction_before_process)) ? true : false;
			// echo "<br>";
			// echo $this->db->last_query()." ".$arr_is_atm_before_process[date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))]."<br>";

			$is_atm_transaction_after_process = array();
			if($arr_reset_datetime_last_process[date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))] != ""){
				$this->db->where("loan_atm_id", $data['loan_atm_id']);
				$this->db->where("loan_date like '".date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))."%'");
				$this->db->where("loan_date > '".$arr_reset_datetime_last_process[date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))]."'");
				$this->db->where("loan_date <= '".date("Y-m-t", strtotime($arr_reset_datetime_last_process[date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))]))." 23:59:59"."'");
				$is_atm_transaction_after_process 			= @$this->db->get_where("coop_loan_atm_detail")->result_array()[0];
				// echo "<br>";
				// echo $this->db->last_query()." ".$is_atm_transaction_after_process[date("Y-m", strtotime("-".$i." month", strtotime($data['date_interesting'])))]."<br>";
			}
			
			
			$arr_is_atm_after_process[date("Y-m", strtotime("-".($i)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))] = (!empty($is_atm_transaction_after_process)) ? true : false;
		}

		// var_dump($arr_is_atm);exit;
		$date_calc_interest_counter = $arr_datetime_last_process[date("Y-m", strtotime("-".(1)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))];
		if($date_calc_interest_counter==""){
			$date_calc_interest_counter = $coop_loan_atm['approve_date'];
		}

		if(@$_GET['debug']!=""){
			echo "<br>IS ATM<pre>";
			var_dump($arr_is_atm);
			echo "</pre>";
			echo "<br>ATM BEFORE<pre>";
			var_dump($arr_is_atm_before_process);
			echo "</pre>";
			echo "<br>ATM AFTER<pre>";
			var_dump($arr_is_atm_after_process);
			echo "</pre>";
			echo "<br>ATM DATE LAST PROCESS<pre>";
			var_dump($arr_datetime_last_process);
			echo "</pre>";
		}
		// exit;
		// echo "<pre>";
		// var_dump($arr_datetime_last_process);exit;
		// echo $date_calc_interest_atm_after_payment;
		// exit;
		$month = date("m", strtotime("-1 month", strtotime($date_interesting)));
		if($month == "12")
			$year =	date("Y", strtotime("-1 month", strtotime($date_interesting))) + 543;
		else
			$year =	date("Y", strtotime("-0 month", strtotime($date_interesting))) + 543;

		$days_in_year = $this->center_function->get_days_of_year($year-543);
		
		// var_dump($data);
		$i_last = 0;
		//--------------------
		//หาดอกเบี้ยกดระหว่างเดือน
		//--------------------
		
		$arr_month = explode('-',$data['date_interesting']);
		$last_month = date("Y-m", strtotime("-1 month", strtotime($date_interesting)));
		$first_day_of_last_month = $last_month."-01";
		
		//เพิ่มการหาวันที่ประมลผลผ่านรายการของย้อนหลัง 2 เดือน
		$last_month_2 = date("Y-m", strtotime("-2 month", strtotime($date_interesting)));
		$last_date_month_2 = date("Y-m-t", strtotime("-2 month", strtotime($date_interesting)));
		//-----------------------------
		//หาวันที่ประมวลผลเดือนก่อนหน้า
		//-----------------------------
		$date_s_calc_atm = $arr_datetime_last_process[date("Y-m", strtotime("-".(1)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))];
		if($date_s_calc_atm==""){
			$date_s_calc_atm = $coop_loan_atm['approve_date'];
		}
		// var_dump($date_s_calc_atm);exit;
		//-----------------------------
		//หาวันที่ประมวลล่าสุด
		//-----------------------------
		$date_latest_process = $date_s_calc_atm;
		
		$this->db->select("t1.transaction_datetime");
		$this->db->from("coop_loan_atm_transaction AS t1");
		$this->db->join("coop_receipt AS t2","t1.receipt_id = t2.receipt_id","inner");
		$this->db->where("t1.transaction_datetime LIKE '$last_month_2%' AND t1.loan_atm_id = '".$data['loan_atm_id']."'");
		$this->db->order_by("t1.transaction_datetime ASC");
		$this->db->limit(1);
		$row_last_process = $this->db->get()->result_array();
		$date_last_process = $row_last_process[0]['transaction_datetime'];		
		if($date_last_process==""){
			$date_last_process = date("Y-m", strtotime("-2 month", strtotime(date("Y-m", strtotime($data['date_interesting']))."-05") ))."-01";
		}
		
		// echo date("Y-m", strtotime($data['date_interesting']));exit;

		if(!$is_process){
			$this->db->where("receipt_id like '%B%' and transaction_datetime like '".date("Y-m", strtotime($data['date_interesting']))."%'");
			$have_payment_this_month = $this->db->get_where("coop_loan_atm_transaction", array(
				"loan_atm_id" => $data['loan_atm_id']
			))->result_array();

			$this->db->where("loan_date >= '".$arr_datetime_last_process[date("Y-m", strtotime("-".(1)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))]."'");
			
			$atm_transaction_this_month = $this->db->get_where("coop_loan_atm_detail",
				array(
					"loan_atm_id" => $data['loan_atm_id']
				)
			)->result_array();
			
			if($have_payment_this_month && empty($atm_transaction_this_month)){//ถ้าไม่มีชำระตัว B || C และไม่มีรายการกดเงินในเดือนนี้
				$date_last_process = date("Y-m-d", strtotime($have_payment_this_month[0]['transaction_datetime']));
			}else{//ใช้วันประมวลออกเรียกเก็บล่าสุด
				if(@$arr_is_atm[date("Y-m", strtotime("-".(1)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))]){
					$date_last_process = $arr_datetime_last_process[date("Y-m", strtotime("-".(1)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))];
				}else{
					$date_last_process = $arr_datetime_last_process[date("Y-m", strtotime("-".(0)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))];
				}
			}

			if($date_last_process == ""){
				$date_last_process = $coop_loan_atm['date_last_interest'];
			}

			if($date_last_process == ""){
				$date_last_process = $coop_loan_atm['approve_date'];
			}
		}
		// echo $date_last_process;exit;
		if($is_process){
			$end_of_month = date("Y-m-t", strtotime("-1 month", strtotime($date_interesting)));
			$fix_date_end = "and transaction_datetime <= '".$data['date_interesting']." 23:59:59'";
		}else{
			$end_of_month = $data['date_interesting'];
			$fix_date_end = "AND transaction_datetime <= '".$end_of_month." 23:59:59'";
		}
		/*-------------------------------
		//ใช้สำหรับ fix ประมวลผลถึงวันที่ตามระบุ
		---------------------------------*/
		// $end_of_month = "2019-05-28";
		/*---------------------------------*/
		$_month_1 = date("Y-m-t", strtotime("-1 month", strtotime($date_interesting)))." 23:59:59";
		$_month_2 = date("Y-m-t", strtotime("-2 month", strtotime($date_interesting)))." 23:59:59";
		$query_union_2 = '';
		if(strtotime(date("Y-m-d", strtotime($coop_loan_atm['approve_date']))) <= strtotime($_month_2)){
			$check_first_atm = $this->db->get_where("coop_loan_atm_transaction", array(
				"loan_atm_id" => $data['loan_atm_id'],
				"transaction_datetime <= '".$coop_loan_atm["date_last_interest"]."'" => NULL
			))->row_array();

			if(empty($check_first_atm)){//ถ้ากู้ใหม่แล้วยังไม่เคยมีการกดเงินมาก่อน ใช้วันที่คิดดอกเบี้ยล่าสุด/วันที่อนุมัติ ในการหา Transaction
				$query_union_2 = "UNION ALL
				(
					SELECT null,null,(select loan_amount_balance from coop_loan_atm_transaction where loan_atm_id = ".$data['loan_atm_id']." AND transaction_datetime < '".date('Y-m-d 23:59:59', strtotime($coop_loan_atm["date_last_interest"]))."' ORDER BY transaction_datetime DESC, loan_atm_transaction_id DESC LIMIT 1),'".date('Y-m-d 23:59:59', strtotime($coop_loan_atm["date_last_interest"]))."',null
				)";
			}else{
				$query_union_2 = "UNION ALL
				(
					SELECT null,null,(select loan_amount_balance from coop_loan_atm_transaction where loan_atm_id = ".$data['loan_atm_id']." AND transaction_datetime < '$_month_2' ORDER BY transaction_datetime DESC, loan_atm_transaction_id DESC LIMIT 1),'$_month_2',null
				)";
			}
			
		}
		
		$query = $this->db->query("SELECT * from (SELECT
										loan_atm_transaction_id,	loan_atm_id,	loan_amount_balance,	transaction_datetime,	receipt_id 
									FROM
										`coop_loan_atm_transaction`
									WHERE
										`loan_atm_id` = ".$data['loan_atm_id']."
									AND `transaction_datetime` > '".$date_last_process."' $fix_date_end
									) as m
									UNION ALL
									(
										SELECT null,null,(select loan_amount_balance from coop_loan_atm_transaction where loan_atm_id = ".$data['loan_atm_id']." AND transaction_datetime < '$_month_1' ORDER BY transaction_datetime DESC, loan_atm_transaction_id DESC LIMIT 1),'$_month_1',null
									)
									".$query_union_2."
									UNION ALL (select null,null, (select loan_amount_balance from coop_loan_atm_transaction where loan_atm_id = ".$data['loan_atm_id']." and transaction_datetime <= '$end_of_month 00:00:00' order by transaction_datetime desc limit 1),'$end_of_month 00:00:00',NULL)
									ORDER BY transaction_datetime"
		)->result_array();
		if(@$_GET['debug']){
			echo "<hr>";
			echo $this->db->last_query();
			echo "<hr>";
		}

		$row_excel					= 1;								
		$temp_interest 				= 0;
		$sum_of_interest 			= 0;
		$collect_interest 			= 0;
		$remain_interest 			= 0;
		$subtract_after_payment		= 0;
		$after_payment				= false;
		$is_atm						= false;
		$sum_of_atm					= 0;
		$sum_of_interest_atm		= 0;//ดอกเบี้ยที่ต้องเรียกเก็บ ยอดที่กดระหว่างเดือน
		$last_receipt_id			= "";
		$start_at_transaction_id	= "";
		$is_transfer				= false;
		$sum_real_payment_interest	= 0;
		$sum_collect_interest		= 0;
		$is_settlement				= false;
		$sum_un_interest			= 0;
		$sum_of_atm_settlement		= 0;
		$sum_of_settlement_interest = 0;//เก็บดอกเบี้ยสำหรับปิดยอดเงินกู้
		$is_atm_after_process		= false;
		$is_atm_current_month		= false;
		$sum_of_atm_after_process	= 0;
		$last_bf_after_process 		= 0;
		$sum_of_minus				= 0;
		$total_real_pay_interest	= 0; // ดอกเบี้ย ชำระอื่นๆ ดอกปัจจุบัน
		$is_all_paid				= false;
		if($query){
			// echo "<br>";
			$this->db->where("loan_atm_id", $data['loan_atm_id']);
			$this->db->where("transaction_datetime < ", $query[0]['transaction_datetime']);
			$this->db->order_by("transaction_datetime DESC, loan_atm_transaction_id DESC");
			$this->db->limit(1);
			$query_last_transaction 			= $this->db->get("coop_loan_atm_transaction")->result_array()[0];
			// echo $this->db->last_query(); echo '<br>';
			if($query_last_transaction){
				$last_atm_transaction_date 		= date("Y-m-d", strtotime("-0 month", strtotime($query_last_transaction['transaction_datetime'])));
				$bf								= $query_last_transaction['loan_amount_balance'];
				$start_at_transaction_id		= $query_last_transaction['loan_atm_transaction_id'];
				// echo $bf;
				// exit;
			}else{
				// $last_atm_transaction_date 		= date("Y-m-t", strtotime("-2 month", strtotime($date_interesting)));
				$this->db->order_by("transaction_datetime asc");
				$this->db->limit(1);
				$last_atm_transaction_date = $this->db->get_where("coop_loan_atm_transaction", array("loan_atm_id" => $data['loan_atm_id']) )->result_array()[0]['transaction_datetime'];
				$this->db->order_by('transaction_datetime ASC, loan_atm_transaction_id ASC');
				$this->db->limit(1);
				$this->db->where("transaction_datetime < '$last_atm_transaction_date'");
				$this->db->where("loan_atm_id", $data['loan_atm_id']);
				$bf = $this->db->get("coop_loan_atm_transaction")->result()[0]->loan_amount_balance;
			}

			if(@$_GET['excel']){
				$str = "วันที่,คงเหลือ,รายการ/เลขที่ใบเสร็จ,เงินต้น,ดอกเบี้ย,เรทดอกเบี้ย,จำนวนวัน,ดอกเบี้ยสะสม,ดบ.กดระหว่างเดือน,ยอดกดสะสม";
				fputcsv($fp, explode(",", $str));
				$row_excel++;	
				$atm_transaction = @$this->db->get_where("coop_loan_atm_transaction", array(
					"loan_atm_transaction_id" => $start_at_transaction_id
				))->result_array()[0];
				
				$this->db->select(array(
					"sum(principal_payment) as principal", 
					"sum(interest) as interest"
				));
				$receipt_detail = @$this->db->get_where("coop_finance_transaction", array(
					"receipt_id" => $atm_transaction['receipt_id'],
					"loan_atm_id" => $data['loan_atm_id'],
				))->result_array();
				$str = explode(" ",$last_atm_transaction_date)[0].",".$bf.",".@$atm_transaction['receipt_id'].",".$receipt_detail[0]['principal'].",".$receipt_detail[0]['interest'].",,,";
				fputcsv($fp, explode(",", $str));	
				$row_excel++;	
			}

			foreach ($query as $key => $value) {
				$detail 							= "";
				$withdraw_atm						= 0;
				$temp_interest						= 0;
				$bf 								= ($key==0) ? ($bf==0 ? $value['loan_amount_balance'] : $bf) : $value['loan_amount_balance'];
				$last_bf							= ($key==0) ? $bf : (@$query[$key-1]['loan_amount_balance']);
				$sum_of_minus 						= $last_bf;

				if(!$is_all_paid && $last_bf<=0){
					$is_all_paid = true;
				}


				$rs_detail = @$this->db->get_where("coop_loan_atm_detail", array(
					"loan_atm_id" 	=> $data['loan_atm_id'],
					"loan_date" 	=> $value['transaction_datetime']
				))->result_array()[0];
				// echo $this->db->last_query();
				// echo "\r\n";
				if($rs_detail){
					$withdraw_atm						= $rs_detail['loan_amount'];
					$is_atm								= true;
				}else{
					$is_atm								= false;
				}
				
				//ต้องติดลบจาก การชำระจะทำการ skip ข้าม
				if($value['loan_amount_balance'] < 0 && empty($rs_detail)){
					
					if(@$_GET['excel']){
						$str = $value['transaction_datetime'].",".
						$bf.",".
						$value['receipt_id'].",";
						fputcsv($fp, explode(",", $str));
						$row_excel++;	
					}
					$sum_of_minus = $value['loan_amount_balance'];
					continue;
				}

				if($bf <= 0){
					$is_settlement = true;
				}else{
					$is_settlement = false;
				}
				
				// if( $is_counter==true && strtotime($value['transaction_datetime']) < strtotime($date_latest_process)){
				if( $is_counter==true && strtotime($value['transaction_datetime']) <= strtotime($date_calc_interest_counter) && strtotime($value['transaction_datetime']) <= strtotime($date_latest_process) ){
					$sum_of_interest_atm = 0;
					// exit;
				}


				if( date("m", strtotime( explode(" ", $value['transaction_datetime'])[0] ) ) != date("m", strtotime($last_atm_transaction_date) ) ){
					// $sum_of_atm 					= 0;//รีเซ็ตกด atm สะสม
					$count_i						= 0;
					// $last_balance					= $query[$key-$count_i]['loan_amount_balance'];
					$check_sum_duplicate			= 0; //นับจำนวน Transaction ในวันนั้นๆ ว่ามีกี่รายการ
					foreach ($query as $key_1 => $value_1) {
						if(date("Y-m-d", strtotime($value_1['transaction_datetime'])) == date("Y-m-d", strtotime($value['transaction_datetime']) )){
							$check_sum_duplicate++;
						}
					}
					if($arr_datetime_last_process[date("Y-m", strtotime($value['transaction_datetime']) )]!=""){
						$this->db->where("transaction_datetime <= '".$arr_datetime_last_process[date("Y-m", strtotime($value['transaction_datetime']) )]."'");
					}else{
						if($check_sum_duplicate>1){//ถ้ามีมากกว่า 1 จะใช้เงื่อนไข transaction_datetime ล่าสุดในวันนั้น
							$this->db->where("transaction_datetime <= '".date("Y-m-d 23:59:59", strtotime($value['transaction_datetime']))."'");
						}else{
							$this->db->where("transaction_datetime <= '".$value['transaction_datetime']."'");
						}
						
					}
					$this->db->order_by("transaction_datetime", "desc");
					$this->db->limit(1);
					$last_balance					= $this->db->get_where("coop_loan_atm_transaction", array(
						"loan_atm_id" 	=> $data['loan_atm_id']
					))->result_array()[0]['loan_amount_balance'];
					$last_bf_after_process 			= ($last_balance != "") ? $last_balance : $query[$key-$count_i]['loan_amount_balance'];
					// echo "SET LOCK: ".$last_bf_after_process ."<br>";
					$last_atm_transaction_date_part = date("Y-m-t", strtotime($last_atm_transaction_date) );
					$d1 							= date("Y-m-d", strtotime($last_atm_transaction_date));
					$d2 							= date("Y-m-d", strtotime($last_atm_transaction_date_part));
					$diff 							= date_diff(date_create($d1), date_create($d2));
					$date_count 					= $diff->format("%a");
					$days_in_year 					= Center_function::get_days_of_year(date("Y", strtotime($last_atm_transaction_date_part)));

					// exit;
					/*---------------------*/
					//หาดอกเบี้ย part_1 ส่วนที่คาบเกี่ยวระหว่างเดือน
					$this->db->select('interest_rate');
					$this->db->from('coop_loan_atm_setting_template');
					$this->db->where("start_date <= '".$last_atm_transaction_date_part."'");
					$this->db->order_by("start_date DESC,run_id DESC");
					$this->db->limit(1);
					$row_atm_setting = $this->db->get()->result_array();
					$interest_rate_atm = $row_atm_setting[0]['interest_rate'];
					
					// $temp_interest 					+= $bf * $interest_rate_atm / 100 * $date_count / $days_in_year;
					// $sum_of_interest 				+= $temp_interest;
					// $remain_interest 				+= $temp_interest;
					if(strtotime($last_atm_transaction_date_part) > strtotime($date_s_calc_atm)){
						$temp_interest 					+= $last_bf * $interest_rate_atm / 100 * $date_count / $days_in_year;
						$sum_of_interest 				+= $temp_interest;
						// echo "FFF ".$last_atm_transaction_date_part." ".($is_atm==true ? "is_atm " : "NOT  ");
						// echo "<br>";
						if($is_atm==true){
							$sum_of_interest_atm += round(($sum_of_atm) * $interest_rate_atm / 100 * $date_count / $days_in_year, 2);
							if( strtotime($last_atm_transaction_date_part) <= strtotime($_month_1) )
								$sum_of_settlement_interest += round(($sum_of_atm_settlement) * $interest_rate_atm / 100 * $date_count / $days_in_year, 2);
						}
							
					}
					// echo "SOSI ".$sum_of_atm;
					// echo "<br><br>";
					//หาดอกเบี้ย part_2
					$date_calc_diff_day = $value['transaction_datetime'];

					$this->db->select('interest_rate, start_date');
					$this->db->from('coop_loan_atm_setting_template');
					$this->db->where("start_date <= '".$date_calc_diff_day."'");
					$this->db->order_by("start_date DESC,run_id DESC");
					$this->db->limit(1);
					$row_atm_setting = $this->db->get()->result_array();
					$interest_rate_atm = $row_atm_setting[0]['interest_rate'];
					
					// $diff 							= date_diff(date_create($date_calc_diff_day), date_create($last_atm_transaction_date_part));
					// $date_count 					= $diff->format("%a");
					$d1 							= date("Y-m-d", strtotime($last_atm_transaction_date_part));
					$d2 							= date("Y-m-d", strtotime($value['transaction_datetime']));
					$diff 							= date_diff(date_create($d1), date_create($d2));
					$date_count 					= $diff->format("%a");
					$days_in_year 					= Center_function::get_days_of_year(date("Y", strtotime($value['transaction_datetime'])));
					$last_bf						= $query[$key-1]['loan_amount_balance'];
					$bf 							= $value['loan_amount_balance'];
					
					
					// $withdraw_atm				= $bf - $last_bf;
					$is_transfer					= true;
				}else{
					$d1 							= date("Y-m-d", strtotime($value['transaction_datetime']));
					$d2 							= date("Y-m-d", strtotime($last_atm_transaction_date));
					$diff 							= date_diff(date_create($d1), date_create($d2));
					$date_count 					= $diff->format("%a");
					$days_in_year 					= Center_function::get_days_of_year(date("Y", strtotime($last_atm_transaction_date)));
					//หาดอกเบี้ย
					$this->db->select('interest_rate, start_date');
					$this->db->from('coop_loan_atm_setting_template');
					$this->db->where("start_date <= '".$last_atm_transaction_date."'");
					$this->db->order_by("start_date DESC,run_id DESC");
					$this->db->limit(1);
					$row_atm_setting = $this->db->get()->result_array();
					$interest_rate_atm = $row_atm_setting[0]['interest_rate'];
				}
				//สำหรับหักกลบ และหน้าเค้าเตอร์
				if($is_counter && strtotime($value['transaction_datetime']) >= strtotime( date("Y-m-", strtotime($data['date_interesting']) )."01" ) ){
					$sum_of_atm_settlement = 0;
				}

				//รีเซ็ตค่า เมื่อมีการจ่ายหน้าเค้าเตอร์
				if($value['receipt_id']!="" && (strpos($value['receipt_id'],"B")==false && strpos($value['receipt_id'],"C")==false && strpos($value['receipt_id'],"F")==false) ){
					// $sum_of_atm = 0;
					$sum_of_atm_settlement = 0;
					$sum_of_interest = 0;
					$sum_of_interest_atm = 0;
					$sum_of_atm = 0;
				}

				// ล้างยอดกดเงินสะสม เมื่อขึ้นเดือนถัดไป
				if( date("m", strtotime(@$value['transaction_datetime'])) != date("m", strtotime($last_atm_transaction_date)) ){
					$sum_of_atm_settlement = 0;
				}
				//เซ็ตวันที่หาดอกเบี้ยล่าสุด transaction
				$last_atm_transaction_date 		= date("Y-m-d H:i:s", strtotime($value['transaction_datetime']));
				// $last_atm_transaction_date 		= date("Y-m-d", strtotime($value['transaction_datetime']));
				if($is_atm_after_process && strtotime($last_atm_transaction_date) >= strtotime($date_calc_interest_counter)){
					$last_bf = $last_bf_after_process;
					// echo "<span style='color: red;'>".$last_atm_transaction_date." ".$last_bf_after_process."</span><br>";
				}

				// echo $last_bf." * ".$interest_rate_atm." / 100 * ".$date_count." / ".$days_in_year." = ".($last_bf * $interest_rate_atm / 100 * $date_count / $days_in_year)."<br>";
				$temp_interest 					+= $last_bf * $interest_rate_atm / 100 * $date_count / $days_in_year;
				$sum_of_interest 				+= $temp_interest;
				$remain_interest 				+= $temp_interest;
				// echo $date_latest_process;exit;
				// if(strtotime($last_atm_transaction_date) > strtotime($date_calc_interest_counter)){
				if($is_process){//ถ้าเรียกเก็บประจำเดือน ใช้ตัวนี้
					$date_start_collect_interest = $date_s_calc_atm;
				}else{
					$date_start_collect_interest = $date_calc_interest_counter;
				}

				

				// echo $date_start_collect_interest;exit;
				
				if(@$_GET['debug']!=""){
					echo "<br>";
					echo (($arr_is_atm[date("Y-m", strtotime($last_atm_transaction_date))]) ? "<span style='color: green'>IS ATM" : "<span style='color: red'>NOT ATM")." TRANSACTION(A)".$last_atm_transaction_date." START COLLECT INTEREST(H) ".$date_start_collect_interest."</span><br>";
				}
				if($arr_is_atm[date("Y-m", strtotime($last_atm_transaction_date))] && strtotime($last_atm_transaction_date) >= strtotime($date_start_collect_interest)){
					if(@$_GET['debug']!=""){
						echo "ADD INTEREST: ";
						echo ($sum_of_atm)." * ".$interest_rate_atm." / 100 * ".$date_count." / ".$days_in_year." = ".round(($sum_of_atm) * $interest_rate_atm / 100 * $date_count / $days_in_year, 2)."<br>";
					}
					if($is_process && date("m", strtotime( explode(" ", $query[$key-1]['transaction_datetime'])[0] ) ) != date("m", strtotime($last_atm_transaction_date)) && !$is_atm_after_process){
						// echo "RESET ".$last_atm_transaction_date." ".$sum_of_atm."<br>";
						$sum_of_atm 					= 0;//รีเซ็ตกด atm สะสม
						$arr_is_reset[date("Y-m", strtotime($last_atm_transaction_date) )] = false;
					}

					$this->db->where("loan_date >= '$date_start_collect_interest' AND loan_date <= '$last_atm_transaction_date'");
					$this->db->where("loan_atm_id", $data['loan_atm_id']);
					$rs_is_atm = $this->db->get("coop_loan_atm_detail")->result_array();//หากด atm หลังผ่านประมวลเรียกเก็บ
					// echo "<br>".$this->db->last_query().";<br>";
					// echo date("Y-m", strtotime("+".(1)." month", strtotime(date("Y-m-05", strtotime($last_atm_transaction_date)))))."<br>";
					// echo $arr_datetime_last_process[date("Y-m", strtotime("+".(1)." month", strtotime(date("Y-m-05", strtotime($last_atm_transaction_date)))))];
					// var_dump($rs_is_atm);
					if(!empty($rs_is_atm )){
						$sum_of_interest_atm += round(($sum_of_atm) * $interest_rate_atm / 100 * $date_count / $days_in_year, 2);
					}
				}

				if($is_atm==true && strtotime($last_atm_transaction_date) > strtotime($date_start_collect_interest) && strtotime(date("Y-m", strtotime("-".(1)." month", strtotime(date("Y-m-05", strtotime($data['date_interesting'])))))."-01") > strtotime($coop_loan_atm['approve_date']) ){
					if($is_atm_after_process == false){
						$sum_collect_interest = 0;
					}
					$is_atm_after_process = true;
					// echo $last_atm_transaction_date." ".$date_calc_interest_atm_after_payment;exit;
				}


				if($is_atm==true && strtotime($last_atm_transaction_date) >= strtotime($date_latest_process))
					$is_atm_current_month = true;
				
				if(($is_counter && strtotime($value['transaction_datetime']) > strtotime( $_month_1 ))){
					$sum_of_settlement_interest += $temp_interest;
				}

				if(@$_GET['excel']){
					$this->db->select(array(
						"sum(principal_payment) as principal", 
						"sum(interest) as interest"
					));
					$receipt_detail = @$this->db->get_where("coop_finance_transaction", array(
						"receipt_id" => $value['receipt_id'],
						"loan_atm_id" => $data['loan_atm_id'],
						"payment_date" => date("Y-m-d", strtotime($value['transaction_datetime']))
					))->result_array();				

					if($is_transfer){
						$detail = @$this->db->get_where("coop_loan_atm_detail", array(
							"loan_atm_id" 	=> $data['loan_atm_id'],
							"loan_date" 	=> $value['transaction_datetime']
						))->result_array()[0]['loan_description'];

						if($value['receipt_id']!="" && $detail=="")
							$detail = $value['receipt_id'];
						else if($detail=="")
							$detail = "ยอดยกมา";
						
						
						$is_transfer = false;
					}else if(@$value['receipt_id']!="" ){

						$detail = @$value['receipt_id'];
					}else{
						// $detail = "ทำรายการกู้ATM";
						
						if($rs_detail){
							$detail = $rs_detail['loan_description'];
						}
						// echo $this->db->last_query();
						if($detail==""){
							$detail = "ยอดยกมา.";
						}
					}

					// $detail = (@$value['receipt_id']!="" || ($withdraw_atm) == 0 ? @$value['receipt_id'] : "ทำรายการกู้ATM");
					$last_bf_ext = 0;
					if($is_counter && strtotime($value['transaction_datetime']) <= strtotime($date_latest_process) ){
						$last_bf_ext = $last_bf;
					}
					$str = $value['transaction_datetime'].",".$bf.",".
						$detail.",".
						(@$value['receipt_id']!="" ? @$receipt_detail[0]['principal'] : $withdraw_atm).",".
						(@$value['receipt_id']!="" ? @$receipt_detail[0]['interest'] : "0").",".
						$interest_rate_atm."%".",".
						$date_count.",".
						"=".($last_bf-$last_bf_ext) ."*". $interest_rate_atm ."/ 100 * ". $date_count ."/". $days_in_year.",".
						"=".(true==true ? "J".$row_excel ."*". $interest_rate_atm ."/ 100 *". $date_count ."/". $days_in_year : "0" ).
						",".( ($sum_of_atm) ).",".$last_bf;
					fputcsv($fp, explode(",", $str));
					$row_excel++;	
				}

				// echo "{".$sum_collect_interest." | ".$value['transaction_datetime']."}";
				//echo 'temp_interest='.$temp_interest.'<br>';
				//echo '<pre>'; print_r($value); echo '</pre>';
				if($after_payment)
					$subtract_after_payment += $temp_interest;

				$collect_interest			+= $temp_interest;
				if($value['receipt_id']!=""){
					// $collect_interest			= 0;
					// $remain_interest 			= 0;
					$after_payment				= true;
				}else if(strtotime(date("Y-m-d", strtotime(@$value['transaction_datetime']))) <= strtotime(date("y-m-d", strtotime($last_date_month_2))) ){
					// $collect_interest			+= $temp_interest;
					$remain_interest 			= 0;
				}

				if(
					(
						!empty($arr_reset_datetime_last_process[date("Y-m", strtotime($last_atm_transaction_date))]) 
						&& $arr_is_reset[date("Y-m", strtotime($last_atm_transaction_date) )] 
						&& strtotime($last_atm_transaction_date) > strtotime(date("Y-m-d", strtotime($arr_reset_datetime_last_process[date("Y-m", strtotime($last_atm_transaction_date))])))
					)
				){
					// echo "RESET ".$last_atm_transaction_date." ".$sum_of_atm."<br>";
					$sum_of_atm 					= 0;//รีเซ็ตกด atm สะสม
					
					$arr_is_reset[date("Y-m", strtotime($last_atm_transaction_date) )] = false;
					// echo "<br>";
					// echo "<span style='color: red;'>RESET ON : ".$last_atm_transaction_date." ".$arr_reset_datetime_last_process[date("Y-m", strtotime($last_atm_transaction_date))]." ".$last_bf;
					// echo " || ".date("m", strtotime( explode(" ", $query[$key-1]['transaction_datetime'])[0] ) )." != ".date("m", strtotime($last_atm_transaction_date))."</span>";
					// echo "<br>";
				}
				
				//ทำรายการกู้ATM
				if($is_counter && $value['receipt_id']=="" && $withdraw_atm > 0 && !empty($arr_datetime_last_process[date("Y-m", strtotime($last_atm_transaction_date))]) && strtotime($last_atm_transaction_date) >= strtotime($arr_datetime_last_process[date("Y-m", strtotime($last_atm_transaction_date))])){
					$is_atm 			= true;
					$sum_of_atm 		+= $withdraw_atm;
					// echo "<br>";
					// echo $last_atm_transaction_date." VS ".$arr_datetime_last_process[date("Y-m", strtotime($last_atm_transaction_date))]."<br>";
					// echo "ADD : ".$last_atm_transaction_date." = ".$sum_of_atm."<br><hr>";
					if(($sum_of_atm + $sum_of_minus) < 0)
						$sum_of_atm = 0;
				}else if($withdraw_atm > 0 && strtotime($last_atm_transaction_date) >= strtotime($date_start_collect_interest)){
					$is_atm 			= true;
					$sum_of_atm 		+= $withdraw_atm;
					// echo "ADD ELSE : ".$last_atm_transaction_date." >= ".$arr_datetime_last_process[date("Y-m", strtotime($last_atm_transaction_date))]." = ".$sum_of_atm."<br><hr>";
					
					if(($sum_of_atm + $sum_of_minus) < 0)
						$sum_of_atm = 0;
				}
				
				// echo $date_s_calc_atm;exit;
				if($value['receipt_id']=="" && $withdraw_atm > 0 && strtotime($last_atm_transaction_date) >= strtotime($date_latest_process)){
					$is_atm 			= true;
					$sum_of_atm_settlement += $withdraw_atm;
					if(($sum_of_atm_settlement + $sum_of_minus) < 0)
						$sum_of_atm_settlement = 0;
				}			

				$last_receipt_id				= ($value['receipt_id'] != "") ? $value['receipt_id'] : $last_receipt_id;
				$last_bf						= $value['loan_amount_balance'];
				// echo "SET : ".$last_bf."<br><br>";
			}
			/*----------------
			end foreach
			----------------*/
		}else{
			// echo "<br>QUERY NOT FOUND!";
			$this->db->order_by('transaction_datetime DESC, loan_atm_transaction_id DESC');
			$this->db->limit(1);
			$this->db->where("loan_atm_id", $data['loan_atm_id']);
			$bf = $this->db->get("coop_loan_atm_transaction")->result()[0]->loan_amount_balance;
		}

		//หาดอกเบี้ย
		$this->db->select('interest_rate');
		$this->db->from('coop_loan_atm_setting_template');
		$this->db->where("start_date <= '". (date("Y-m-d", strtotime("+1 day", strtotime($last_atm_transaction_date)) ) ) ."'");
		$this->db->order_by("start_date DESC,run_id DESC");
		$this->db->limit(1);
		$row_atm_setting = $this->db->get()->result_array();
		$interest_rate_atm = $row_atm_setting[0]['interest_rate'];
		// echo 'date_interesting ='.$date_interesting .'<br>';
		// echo 'interest_rate_atm ='.$interest_rate_atm .'<br>';

		// echo "<br>";
		$interest = 0;
		//----------------หาดอกเบี้ยส่วนที่ยังไม่มีการคิดดอกเบี้ย
		if($last_atm_transaction_date != ''){
			//$diff = date_diff(date_create( date("Y-m-t", strtotime("-1 month", strtotime($data['date_interesting']) ) ) ), date_create($last_atm_transaction_date));
			if($is_process){
				//ใช้ประมวลผล
				$diff = date_diff(date_create( date("Y-m-t", strtotime("-1 month", strtotime($date_interesting) ) ) ), date_create($last_atm_transaction_date));
				$date_count = $diff->format("%a");
			}else{
				// ใช้หักกลบ
				$diff = date_diff(date_create( date("Y-m-d", strtotime("-0 month", strtotime($data['date_interesting']) ) ) ), date_create($last_atm_transaction_date));
				$date_count = $diff->format("%a");
			}


			//$tmp_interest = $bf * $date_count * $loan_atm_setting['interest_rate'] / 100 / $days_in_year;
			$tmp_interest = $bf * $date_count * $interest_rate_atm / 100 / 365;
			
			if($is_process)
				$interest += $tmp_interest;
		}
		//----------------หา ดบ. เรียกเก็บ
		//$diff = date_diff(date_create( date("Y-m-t", strtotime("-1 month", strtotime($data['date_interesting']) ) ) ), date_create( date('Y-m-t', strtotime($data['date_interesting']) ) ));
		if(!$is_process){
			//ใช้หักกลบ
			$this->db->order_by("transaction_datetime desc");
			$this->db->limit(1);
			$last_transacrion_atm = $this->db->get_where("coop_loan_atm_transaction", array("loan_atm_id" => $data['loan_atm_id']))->result()[0]->transaction_datetime;
			if( date('m', strtotime($date_interesting)) != date("m", strtotime($last_transacrion_atm)) ){
				$last_transacrion_atm = date("Y-m-t", strtotime($last_transacrion_atm));
			}
			$diff = date_diff(date_create( date("Y-m-d", strtotime("-0 month", strtotime($last_transacrion_atm) ) ) ), date_create( date('Y-m-d', strtotime($data['date_interesting']) ) ));
			$date_count = $diff->format("%a");
			$days_in_year = Center_function::get_days_of_year(date("Y", strtotime($last_transacrion_atm)));
		}else{
			//ใช้ออกเรียกเก็บประจำเดือน
			if($is_all_paid && $last_bf > 0){//หนี้หมดไปนานมากกว่า 1 เดือนแล้วกลับมาเป็นหนี้ใหม่
				$this->db->order_by("loan_date desc");
				$last_atm_date = $this->db->get_where("coop_loan_atm_detail", array(
					"loan_atm_id" => $data['loan_atm_id']
				))->row_array()['loan_date']; //วันที่กดเงินล่าสุด
				if(strtotime($last_atm_date) >= strtotime($coop_loan_atm['date_last_interest'])){
					$date_interesting = date("Y-m-d", strtotime($last_atm_date));
				}else{
					$date_interesting = date("Y-m-d", strtotime($coop_loan_atm['date_last_interest']));
				}
				$diff = date_diff(date_create( date("Y-m-d", strtotime($date_interesting) ) ), date_create( date('Y-m-t', strtotime($data['date_interesting']) ) ));
				$date_count = $diff->format("%a");
				$days_in_year = Center_function::get_days_of_year(date("Y", strtotime($date_interesting)));
			}else{
				$diff = date_diff(date_create( date("Y-m-t", strtotime("-1 month", strtotime($date_interesting) ) ) ), date_create( date('Y-m-t', strtotime($data['date_interesting']) ) ));
				$date_count = $diff->format("%a");
				$days_in_year = Center_function::get_days_of_year(date("Y", strtotime($date_interesting)));
			}

		}


		//หาดอกเบี้ย
		$this->db->select('interest_rate');
		$this->db->from('coop_loan_atm_setting_template');
		$this->db->where("start_date <= '".$date_interesting."'");
		$this->db->order_by("start_date DESC,run_id DESC");
		$this->db->limit(1);
		$row_atm_setting = $this->db->get()->result_array();
		$interest_rate_atm = $row_atm_setting[0]['interest_rate'];
		if($is_process){
			$tmp_interest = ($bf<=0 ? 0 : $bf) * $date_count * $interest_rate_atm / 100 / $days_in_year;

			/*------------------------*/
			//skip การเรียกเก็บดอกเบี้ยล่วงหน้า
			$interest = $tmp_interest;
			/*-----------------------*/

			if(@$_GET['excel']){		
				$str = explode(" ", $data['date_interesting'])[0].",".$bf.",".
					"-".",".
					"".",".
					"".",".
					$interest_rate_atm."%".",".
					$date_count.",".
					"=".($last_bf<=0 ? 0 : $last_bf) ."*". $interest_rate_atm ."/ 100 *". $date_count ."/". $days_in_year.",".
					"".",";
				fputcsv($fp, explode(",", $str));
				$row_excel++;	
			}
		}else{
			$total_real_pay_interest = $this->db->select("sum(interest) as interest")
												->from("coop_finance_transaction")
												->where("loan_atm_id", $data['loan_atm_id'])
												->where("payment_date >= '".$date_interesting."'")
												->where("receipt_id not like '%B%' AND receipt_id not like '%F%' AND receipt_id not like '%C%'")
												->get()->result_array()[0]['interest'];
		}
		

		$total_interest = ($interest);

		$this->db->where("(receipt_id like '%B%')");
		$this->db->where("transaction_datetime like '".date("Y-m", strtotime("-0 month", strtotime($data['date_interesting'])))."%'");
		$process_this_month = $this->db->get_where("coop_loan_atm_transaction", array(
			"loan_atm_id" => $data['loan_atm_id']
		))->result_array()[0];
		if(!empty($process_this_month)){
			$sum_of_settlement_interest = 0;
		}

		$sql_max_period = "select if( (select max_period from coop_loan_atm where coop_loan_atm.loan_atm_id = ".$data['loan_atm_id']."), (select max_period from coop_loan_atm where coop_loan_atm.loan_atm_id = ".$data['loan_atm_id']."), (select max_period from coop_loan_atm_setting LIMIT 1) ) as max_period";
		$max_period = $this->db->query($sql_max_period)->result()[0]->max_period;
		$sql = "SELECT
		IF (
				ISNULL(
					(
						SELECT
							loan_atm_id
						FROM
							coop_loan_atm_detail
						WHERE
							loan_date ".(strtotime($coop_loan_atm['approve_date']) >= strtotime($date_s_calc_atm) ? '>' : '>=' )." '".$date_s_calc_atm."'
						AND loan_atm_id = ".$data['loan_atm_id']." LIMIT 1
					)
				),
				(
					SELECT
						pay_amount
					FROM
						coop_finance_month_detail
					WHERE
						loan_atm_id = ".$data['loan_atm_id']."
					AND pay_type = 'principal'
					ORDER BY
						run_id DESC
					LIMIT 1
				),
				(
					SELECT

					IF (
						MOD (loan_amount_balance / $max_period, 100) > 0,
						loan_amount_balance / $max_period + (
							100 - MOD (loan_amount_balance / $max_period, 100)
						),
						loan_amount_balance / $max_period
					)
					FROM
						coop_loan_atm_transaction
					WHERE
						loan_atm_id = ".$data['loan_atm_id']."
					AND transaction_datetime <= '".date("Y-m-t", strtotime($last_month))." 23:59:59"."'
					ORDER BY
						transaction_datetime DESC
					LIMIT 1
				)
		)  AS principal";

		if($is_counter){
			$sum_of_settlement_interest -= $total_real_pay_interest;
		}
		
		$arr_loan_interest['principal_month'] = @$this->db->query($sql)->result()[0]->principal;
		$arr_loan_interest['sum_real_payment_interest'] = round($sum_real_payment_interest, 2);
		$arr_loan_interest['sum_collect_interest'] = round($sum_collect_interest, 2);
		$arr_loan_interest['settlement_interest'] = round($sum_of_settlement_interest, 2);
		if( ($arr_loan_interest['sum_collect_interest'] - $sum_un_interest) <= 1 ){
			$arr_loan_interest['sum_collect_interest'] = round($arr_loan_interest['sum_collect_interest'] - $sum_un_interest, 2);
		}
		//----
		$this->db->where("loan_atm_id", $data['loan_atm_id'] );
		$this->db->where("approve_date < ", $last_month.'-01');
		$rs_chk = $this->db->get("coop_loan_atm")->result_array();
		$used = "";
		if($rs_chk || $is_counter){
			//เคสกู้เก่า ดอกเบี้ยลวงหน้า + ดบ.กดสะสม
			// echo $sum_of_settlement_interest;exit;
			if($is_counter){
				if(($is_atm_after_process || $is_atm_current_month) && !$is_all_paid)//ถ้ามีการกดเงินหลังผ่านรายการ
					$arr_loan_interest['interest_month'] = round($total_interest + $sum_of_settlement_interest + $sum_of_atm_after_process + $sum_of_interest_atm,2);
				else if($is_all_paid && $last_bf > 0)
					$arr_loan_interest['interest_month'] = round($total_interest + $sum_of_atm_after_process + $sum_of_interest_atm,2);
				else
					$arr_loan_interest['interest_month'] = round($total_interest + $sum_of_settlement_interest + $sum_of_atm_after_process,2);
				
				if($arr_loan_interest['interest_month'] < 0)
					$arr_loan_interest['interest_month'] = 0;
			}else{
				$arr_loan_interest['interest_month'] = round($total_interest + $sum_of_interest_atm + $sum_of_atm_after_process,2);

			}
			// echo "<br>".$sum_of_interest_atm;exit;
			// exit;
			$used = "ดอกเบี้ยลวงหน้า + ดบ.กดสะสมระหว่างเดือน";
		}else{
			//เคสกู้เก่าหรือหักกลบ ดอกเบี้ยลวงหน้า + ดบ.สะสม
			$arr_loan_interest['interest_month'] = round($total_interest + $remain_interest);
			$used = "ดอกเบี้ยลวงหน้า + ดบ.สะสม";
			
		}
		
		//-------------
		$arr_loan_interest['interest_rate'] = $interest_rate_atm;
		$arr_loan_interest['start_date_cal'] = $date_last_process; //วันที่เริ่มคำนวณ
		$arr_loan_interest['end_date_cal'] = $data['date_interesting']; //วันที่คำนวณถึง 
		if(@$_GET['debug']){
			echo "<br>";
			echo $total_interest." | ".$sum_of_settlement_interest." | ".$sum_of_atm_after_process ." | ".$sum_of_interest_atm . " = ".($total_interest+$sum_of_settlement_interest+$sum_of_atm_after_process+$sum_of_interest_atm);

			echo "<pre>";var_dump($arr_loan_interest);echo "</pre><hr>";
			exit;
		}
		
		if(@$_GET['excel']){
			// $interest_return = 0;
			$date_ruturn_interest = date("Y-m-t", strtotime( "-1 months", strtotime((@$_GET['year']-543) . "-" . @$_GET['month'] . "-01") ) );
			$days_in_year = $this->center_function->get_days_of_year($_GET['year']-543);

			$str = ",";
			fputcsv($fp, explode(",", $str));
			$row_excel++;	

			$str = "ดบ., , , , , , ,".@$total_interest;
			fputcsv($fp, explode(",", $str));
			$row_excel++;	
			$str = "ดบ.ยอดที่กดระหว่างเดือน, , , , , , ,".(@$sum_of_interest_atm)." ";
			fputcsv($fp, explode(",", $str));
			$row_excel++;	
			$str = "ดบ.สะสม, , , , , , ,".@$remain_interest;
			fputcsv($fp, explode(",", $str));
			$row_excel++;	
			$str = "รวมดอกเบี้ย, , , , , , ,=".($arr_loan_interest['interest_month']).",".$used;
			fputcsv($fp, explode(",", $str));
			$row_excel++;	
			fclose($fp);
		}

		return $arr_loan_interest;
	}
	
	//-----------------------------------------------------
	// $type_count_date is array("month" => XX, "year" => XX) จะเป็นตัวนับจำนวนวันในเดือนนั้นๆ ใช้ในการคำนวณดอกเบี้ยออกรายการเรียกเก็บประชำเดือน
	
	public function cal_loan_interest($data,$return_type="echo",$type_count_date=""){
		$this->db->select(array('t1.loan_amount_balance','(select interest_rate from coop_term_of_loan where type_id = t1.loan_type and start_date <= CURDATE() ORDER BY start_date desc, id desc LIMIT 1) as interest_per_year','t1.createdatetime','t2.date_transfer','t1.date_last_interest'));
		$this->db->from('coop_loan as t1');
		$this->db->join('coop_loan_transfer as t2','t1.id = t2.loan_id','inner');
		$this->db->where("
			t1.id = '".$data['loan_id']."' AND t2.transfer_status = '0'
		");
		$row = $this->db->get()->result_array();
		$row_loan = @$row[0];
		/*$this->db->select('payment_date');
		$this->db->from('coop_finance_transaction');
		$this->db->where("
			loan_id = '".$data['loan_id']."'
			AND deduct_type = 'all'
		");
		$this->db->order_by("payment_date DESC");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$row_last_transaction = @$row[0];*/
		if($row_loan['date_last_interest']!=''){
			$last_payment_date = $row_loan['date_last_interest'];
		}else{
			$last_payment_date = @$row_loan['date_transfer'];
		}
		$this->db->select('*');
		$this->db->from('coop_loan_transaction');
		$this->db->where("loan_id = '".$data['loan_id']."'");
		$this->db->order_by("transaction_datetime", "desc");
		$this->db->order_by("loan_transaction_id", "desc");
		$this->db->limit(1);
		// $this->db->where("
		// 	loan_id = '".$data['loan_id']."'
		// 	AND transaction_datetime >= '".$last_payment_date."'
		// ");
		// $this->db->order_by("loan_transaction_id ASC");
		$row = $this->db->get()->result_array();
		$loan_transaction = array();
		$i=0;
		if(!empty($row)){
			foreach($row as $key => $value){
				$loan_transaction[$i]['loan_amount_balance'] = $value['loan_amount_balance'];
				$date_start = explode(' ',$value['transaction_datetime']);
				$loan_transaction[$i]['date_start'] = $date_start[0];
				if(@$row[$key+1]['transaction_datetime']!=''){
					$date_end = explode(' ',$row[$key+1]['transaction_datetime']);
					$loan_transaction[$i]['date_end'] = $date_end[0];
				}else{
					$loan_transaction[$i]['date_end'] = $data['date_interesting'];
				}
				//หาจำนวนวัน
				if($type_count_date==""){
					$diff = date_diff(date_create($loan_transaction[$i]['date_start']),date_create($loan_transaction[$i]['date_end']));
					$date_count = $diff->format("%a");
				}else{
					$date_count = cal_days_in_month(CAL_GREGORIAN, $type_count_date['month'], $type_count_date['year']);
				}
				$loan_transaction[$i]['date_count'] = $date_count;
				$interest = ((($loan_transaction[$i]['loan_amount_balance']*$row_loan['interest_per_year'])/100)/365)*$loan_transaction[$i]['date_count'];
				$loan_transaction[$i]['origin_interest'] = $interest;
				$interest = round($interest);
				$loan_transaction[$i]['interest'] = $interest;
				$loan_transaction[$i]['interest_rate'] = $row_loan['interest_per_year'];
				$i++;
			}
			$loan_transaction_tmp = array();
			foreach($loan_transaction as $key => $value){
				$loan_transaction_tmp[$value['date_start']] = $value;
			}
			$loan_transaction = $loan_transaction_tmp;
		}
		$interest_amount = 0;
		foreach(@$loan_transaction as $key => $value){
			$interest_amount += $value['interest'];
		}
		//echo "<pre>";print_r($loan_transaction);echo"</pre>";exit;
		if($return_type == 'echo'){
			return $interest_amount;
		}else{
			return $loan_transaction;
		}
	}
	
	public function cal_atm_after_process($data,$return_type='echo'){
		$loan_atm_id = @$data['loan_atm_id'];
		$date_interesting = @$data['date_interesting'];
		$arr_date = explode('-',$date_interesting);
		$mm_profile = (int)$arr_date[1];
		$yy_profile = $arr_date[0]+543;
		//หาโปรไฟล์ ก่อน
		$rs_profile = $this->db->select(array('profile_id'))
		->from('coop_finance_month_profile')
		->where("profile_month = '".$mm_profile."' AND profile_year = '".$yy_profile."'")
		->limit(1)
		->get()->result_array();
		$profile = @$rs_profile[0]['profile_id'];
		//เช็คการผ่านรายการ
		$finance_month = $this->db->select('
		coop_finance_month_detail.member_id,
		coop_finance_month_detail.loan_atm_id,
		coop_finance_month_detail.profile_id,
		coop_finance_month_detail.run_status,
		coop_finance_month_detail.pay_type,
		coop_finance_month_detail.pay_amount
		')
		->from('coop_finance_month_detail')
		->where("coop_finance_month_detail.loan_atm_id IS NOT NULL
				AND coop_finance_month_detail.loan_atm_id != ''
				AND coop_finance_month_detail.loan_atm_id = '".$loan_atm_id."'
				AND profile_id = '".$profile."'
				AND pay_type = 'principal'
				AND run_status = '1'")
		->get()->result_array();
		$atm_month_detail = array();
		$finance_month_amount = 0;
		$i=0;
		foreach($finance_month as $key => $value){
			$atm_month_detail[$i]['pay_amount'] = $value['pay_amount'];
			$finance_month_amount += $value['pay_amount'];
			$i++;
		}
		if($return_type == 'echo'){
			return $finance_month_amount;
		}else{
			return $atm_month_detail;
		}
	}
	
	public function cal_atm_interest_transaction($data,$return_type='echo'){
		$loan_atm_id = @$data['loan_atm_id'];
		$date_interesting = @$data['date_interesting'];
		$arr_date = explode('-',$date_interesting);
		$mm_en = (int)$arr_date[1];
		$yy_en = $arr_date[0];
		$this->db->select('*');
		$this->db->from('coop_loan_atm_setting');
		$row = $this->db->get()->result_array();
		$loan_atm_setting = @$row[0];
		//echo $date_interesting.'<br>';
		//$start_date = $yy_en.'-'.sprintf("%02d",@$mm_en).'-01'.' 00:00:00.000';
		//$end_date = date('Y-m-t',strtotime($start_date)).' 23:59:59.000';
		//echo '<pre>'; print_r($data); echo '</pre>';
		$rs_loan_atm = $this->db->select('member_id')
		->from('coop_loan_atm')
		->where("loan_atm_id = '".$loan_atm_id."'")
		->limit(1)
		->get()->result_array();
		$member_id = @$rs_loan_atm[0]['member_id'];
		$rs_receipt = $this->db->select('receipt_datetime')
		->from('coop_receipt')
		->where("member_id = '".$member_id."' AND month_receipt IS NOT NULL AND year_receipt IS NOT NULL  AND finance_month_profile_id IS NOT NULL ")
		->order_by("receipt_datetime DESC")
		->limit(1)
		->get()->result_array();
		$start_date = @$rs_receipt[0]['receipt_datetime'];
		$end_date = date('Y-m-t',strtotime($date_interesting)).' 23:59:59.000';
		$rs_atm_detail = $this->db->select('loan_date,loan_amount,loan_amount_balance')
		->from('coop_loan_atm_detail')
		->where("
			loan_atm_id = '".$loan_atm_id."'
			AND loan_date BETWEEN '".$start_date."' AND '".$end_date."'
		")
		->get()->result_array();
		$row_atm_detail = @$rs_atm_detail;
		//echo $this->db->last_query(); echo '<br>';
		//exit;
		//echo '<pre>'; print_r($row_atm_detail); echo '</pre>';
		$atm_transaction = array();
		$i=0;
		foreach($row_atm_detail as $key => $value){
			$atm_transaction[$i]['loan_amount_balance'] = $value['loan_amount_balance'];
			$date_start = explode(' ',$value['loan_date']);
			$atm_transaction[$i]['date_start'] = $date_start[0];
			if(@$row[$key+1]['loan_date']!=''){
				$date_end = explode(' ',$row[$key+1]['loan_date']);
				$atm_transaction[$i]['date_end'] = $date_end[0];
			}else{
				$atm_transaction[$i]['date_end'] = $data['date_interesting'];
			}
			$diff = date_diff(date_create($atm_transaction[$i]['date_start']),date_create($atm_transaction[$i]['date_end']));
			$date_count = $diff->format("%a");
			if($date_count == 0){
				//$date_count = $date_count+1;
			}
			$atm_transaction[$i]['date_count'] = $date_count;
			$interest = ((($atm_transaction[$i]['loan_amount_balance']*$loan_atm_setting['interest_rate'])/100)/365)*$atm_transaction[$i]['date_count'];
			$atm_transaction[$i]['origin_interest'] = $interest;
			$interest = number_format($interest, 2, '.', '');
			//echo 'interest='.$interest.'<br>';
			//$interest = round($interest);
			$atm_transaction[$i]['interest_rate'] = $loan_atm_setting['interest_rate'];
			$atm_transaction[$i]['interest'] = $interest;
			$i++;
		}
		//echo '<pre>'; print_r($atm_transaction); echo '</pre>';
		$interest_amount = 0;
		foreach($atm_transaction as $key => $value){
			$interest_amount += $value['interest'];
		}
		if($return_type == 'echo'){
			return number_format($interest_amount,0, '.', '');
		}else{
			return $atm_transaction;
		}
	}
	
	public function generate_period_loan($loan_id, $pay_type, $period_type, $period, $date_cal ,$interest){
		// var_dump($data);
		// init set
		$this->db->select(array("coop_loan.*", "DAY(date_start_period) AS d", "MONTH(date_start_period) AS m", "YEAR(date_start_period) AS y"));
		$loan_row = $this->db->get_where("coop_loan", array("id" => $loan_id) )->result()[0];
		if($loan_row!=""){
			$this->db->where("loan_id", $loan_id);
			$this->db->delete("coop_loan_period");
		}
		// $interest = $interest; // อัตราดอกเบี้ย
		$loan = $loan_row->loan_amount; // จำนวนเงินกู้
		// $pay_type = $_POST["pay_type"]; // ปรเภท ชำระเท่ากันทุกงวด,ต้นเท่ากันทุกงวด
		// $period = (double)$_POST["period"]; // จำนวน งวด  หรือ เงิน แล้วแต่ type
		$tmp_date = explode("-", $date_cal);
		$day = $tmp_date[2];
		$month = $tmp_date[1]-1;
		$year  = $tmp_date[0];
		// $period_type= 2; // ประเภท งวดหรือจำนวนเงิน
		//1 งวด
		//2 จำนวนเงิน
		if($period_type == '1' && $pay_type=='2'){
			$total_per_period = $loan/$period;
			$date_start = ($year-543)."-".$month."-".$day;
			$date_period_1 = date('Y-m-t',strtotime('+1 month',strtotime($date_start)));
			$diff = date_diff(date_create($date_start),date_create($date_period_1));
			$date_count = $diff->format("%a");
			$date_count = 31;
			$interest_period_1 = ((($loan*$interest)/100)/365)*$date_count;
			//if($interest_period_1 > $total_per_period){
				$per_period = ($loan * ( (6/100) / 12 ))/( 1-pow(1/(1+( (6/100) /12)),$period));
				//$per_period = ceil($interest_period_1/100)*100;
				$period = $per_period;
				$period_type = 2;
			//}
		}
		$pay_period = $loan / $period;
		$a = ceil($pay_period/10)*10;
		$daydiff = date('t') - $day;
		// ---------------------------
				$loan_remain = $loan;
				$is_last = FALSE;
				$total_loan_pri = 0;
				$total_loan_int = 0;
				$total_loan_pay = 0;
				$d = $period - 1;
				$peroid_row = array();
				for ($i=1; $i <= $period; $i++) {
					if($loan_remain <= 0 ){ break; }
					if($pay_type == 1) {
						if ($period_type == 1) {
									if ($month > 12) {
											$month = 1;
											$year += 1;
									}
									//$loan_pri = $a;
									$loan_pri = ceil($a/100)*100;
									$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
									$summonth = $nummonth;
									$daydiff = 31 - $day;
									if ($i == 1) {
										if ($daydiff >= 0) {
												/*if ($day <= 10) {
													$summonth -=  $day;
													$summonth += 1;
												} else if ($day >= 11 && $day <= 31) {*/
													$month += 1;
													if ($month > 12) {
															$month = 1;
															$year += 1;
													}
													$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
													$summonth = $nummonth;
													$summonth = $daydiff + 31;
												//}
										 }
									}
									$summonth = $this->force_summonth($summonth,$i);
									//$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
									$loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
									if($loan_pri < 0){
										$loan_pri = 0;
									}
									$loan_pay = $loan_pri + $loan_int;
									$loan_remain -= ceil($loan_pri/100)*100;
									//$loan_remain -= $loan_pri;
						} else if ($period_type == 2) {
							if ($month > 12) {
									$month = 1;
									$year += 1;
							}
							$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
							$summonth = $nummonth;
							$daydiff = 31 - $day;
							if ($i == 1) {
								if ($daydiff >= 0) {
										/*if ($day <= 10) {
											$summonth -=  $day;
											$summonth += 1;
										} else if ($day >= 11 && $day <= 31) {*/
											$month += 1;
											$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
											$summonth = $nummonth;
											$summonth = $daydiff + 31;
										//}
								 }
							}
							$summonth = $this->force_summonth($summonth,$i);
							//$loan_pri = $period;
							$loan_pri = ceil($period/100)*100;
							//$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
							$loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
							if($loan_pri < 0){
								$loan_pri = 0;
							}
							$loan_pay = $loan_pri + $loan_int;
							//$loan_remain -= $loan_pri;
							$loan_remain -= ceil($loan_pri/100)*100;
					}
				}else if($pay_type == 2) {
						if ($period_type == 1) {
									if ($month > 12) {
											$month = 1;
											$year += 1;
									}
									//$loan_pri = $a;
									$loan_pri = ceil($a/100)*100;
									$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
									$summonth = $nummonth;
									$daydiff = 31 - $day;
									if ($i == 1) {
										if ($daydiff >= 0) {
												/*if ($day <= 10) {
													$summonth -=  $day;
													$summonth += 1;
												} else if ($day >= 11 && $day <= 31) {*/
													$month += 1;
													if ($month > 12) {
															$month = 1;
															$year += 1;
													}
													$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
													$summonth = $nummonth;
													$summonth = $daydiff + 31;
												//}
										 }
									}
									$summonth = $this->force_summonth($summonth,$i);
									//$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
									$loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
									$loan_pri = $loan_pri - $loan_int;
									if($loan_pri < 0){
										$loan_pri = 0;
									}
									$loan_pay = $loan_pri + $loan_int;
									$loan_remain -= ceil($loan_pri/100)*100;
						} else if ($period_type == 2) {
							if ($month > 12) {
									$month = 1;
									$year += 1;
							}
							$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
							$summonth = $nummonth;
							$daydiff = 31 - $day;
							if ($i == 1) {
								if ($daydiff >= 0) {
										/*if ($day <= 10) {
											$summonth -=  $day;
											$summonth += 1;
										} else if ($day >= 11 && $day <= 31) {*/
											$month += 1;
											$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
											$summonth = $nummonth;
											$summonth = $daydiff + 31;
										//}
								 }
							}
							$summonth = $this->force_summonth($summonth,$i);
							//$loan_pri = $period;
							$loan_pri = ceil($period/100)*100;
							//$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
							$loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
							$loan_pri = $loan_pri - $loan_int;
							if($loan_pri < 0){
								$loan_pri = 0;
							}
							$loan_pay = $loan_pri + $loan_int;
							//$loan_remain -= $loan_pri;
							$loan_remain -= ceil($loan_pri/100)*100;
					}
				}
					if($loan_remain <= 0) {
						$loan_pri += $loan_remain;
						$loan_pay = $loan_pri + $loan_int;
						$loan_remain = 0;
						@$count = $count + 1;
					}
					$sumloan = $loan_remain + $loan_pri;
					$sumloanarr[] = $loan_remain + $loan_pri;
					$sumint[] = $loan_int;
					if ($i == $period) {
						$loan_pri = $sumloanarr[$d];
						$loan_pay = $loan_pri + $loan_int;
					}
					@$total_loan_int += $loan_int;
					//@$total_loan_pri += $loan_pri;
					@$total_loan_pri += ceil($loan_pri/100)*100;
					@$total_loan_pay += $loan_pay;
					//@$total_loan_pri_m += $loan_pri;
					@$total_loan_pri_m += ceil($loan_pri/100)*100;
					@$total_loan_int_m += $loan_int;
					@$total_loan_pay_m += $loan_pay;
					if((int)$month == '2'){
						$nummonth = '28';
					}
					$peroid_row['period_count']				= $i;
					$peroid_row['outstanding_balance']		= $sumloan;
					$peroid_row['date_period']				= ($year)."-".sprintf('%02d',$month)."-".$nummonth;
					$peroid_row['date_count']				= $summonth;
					$peroid_row['interest']					= $loan_int;
					$peroid_row['principal_payment']		= $loan_pri;
					$peroid_row['total_paid_per_month']		= $loan_pay;
					$peroid_row['loan_id']					= $loan_id;
					// echo "<pre>";
					// var_dump($peroid_row);
					// echo "</pre>";
					// $this->db->insert("coop_loan_period",$peroid_row);
					if(@$period_1==""){
							$period_1 = $peroid_row;
							return $period_1;
					}
					if($is_last) {
						break;
					}
					$month++;
				}
				$update_coop_loan = array();
				$update_coop_loan['money_per_period']	= $period_1['total_paid_per_month'];
				$update_coop_loan['period_amount']		= $i-1;
				$update_coop_loan['updatetimestamp']	= date("Y-m-d H:i:s");
				$update_coop_loan['pay_type']					= $pay_type;
				$update_coop_loan['period_type']			= $period_type;
				$update_coop_loan['money_period_1']		= $period_1['total_paid_per_month'];
				$update_coop_loan['date_period_1']		= $period_1['date_period'];
				$update_coop_loan['period_type']			= $period_type;
				// $this->db->where("id", $loan_id);
				// $this->db->update("coop_loan", $update_coop_loan);
				return $update_coop_loan;
		// var_dump($total_per_period);
				// exit;
	}
	private function force_summonth($summonth,$period){
		if($period=='1'){
			$summonth = $summonth-1;
		}else{
			$summonth = $summonth;
		}
		return $summonth;
	}
	
	public function cal_atm_interest_deduct($data,$return_type='echo',$type_count_date=""){
		$this->db->select('*');
		$this->db->from('coop_loan_atm_setting');
		$row = $this->db->get()->result_array();
		$loan_atm_setting = @$row[0];
		$year =	date("Y", strtotime("-0 month", strtotime($data['date_interesting']))) + 543;
		$month = date("m", strtotime("-1 month", strtotime($data['date_interesting'])));
		// var_dump($data);
		$i_last = 0;
		//--------------------
		//หาดอกเบี้ยกดระหว่างเดือน
		//--------------------
		$arr_month = explode('-',$data['date_interesting']);
		$last_month = date("Y-m", strtotime("-1 month", strtotime($data['date_interesting'])));
		$this->db->where("loan_atm_id", $data['loan_atm_id']);
		$this->db->where("transaction_datetime LIKE '$last_month%'");
		$query = $this->db->get("coop_loan_atm_transaction")->result_array();
		$temp_interest 				= 0;
		$sum_of_interest 			= 0;
		$collect_interest 			= 0;
		$remain_interest 			= 0;
		if($query){
			// echo "<br>";
			$this->db->where("loan_atm_id", $data['loan_atm_id']);
			$this->db->where("transaction_datetime < ", $query[0]['transaction_datetime']);
			$this->db->order_by("transaction_datetime DESC, loan_atm_transaction_id DESC");
			$this->db->limit(1);		
			$query_last_transaction 			= $this->db->get("coop_loan_atm_transaction")->result_array()[0];
			if($query_last_transaction){
				$last_atm_transaction_date 		= date("Y-m-d", strtotime("-0 month", strtotime($query_last_transaction['transaction_datetime'])));	
				$bf								= $query_last_transaction['loan_amount_balance'];
				// echo $bf;
				// exit;
			}else{
				$last_atm_transaction_date 		= date("Y-m-t", strtotime("-2 month", strtotime($data['date_interesting'])));
				$this->db->order_by('transaction_datetime ASC, loan_atm_transaction_id ASC');
				$this->db->limit(1);
				$this->db->where("transaction_datetime < '$last_atm_transaction_date'");
				$this->db->where("loan_atm_id", $data['loan_atm_id']);
				$bf = $this->db->get("coop_loan_atm_transaction")->result()[0]->loan_amount_balance;
			}
			foreach ($query as $key => $value) {
				// var_dump($value);
				$diff 							= date_diff(date_create($value['transaction_datetime']), date_create($last_atm_transaction_date));
				$date_count 					= $diff->format("%a");
				$last_atm_transaction_date 		= date("Y-m-d", strtotime($value['transaction_datetime']));
				$temp_interest 					= $bf * 6 / 100 * $date_count / 365;
				$sum_of_interest 				+= $temp_interest;
				$remain_interest 				+= $temp_interest; 
				$collect_interest				= $temp_interest;
				$bf 							= $value['loan_amount_balance'];					
			}
		}else{
			$this->db->order_by('transaction_datetime DESC, loan_atm_transaction_id DESC');
			$this->db->limit(1);
			$this->db->where("loan_atm_id", $data['loan_atm_id']);
			$bf = $this->db->get("coop_loan_atm_transaction")->result()[0]->loan_amount_balance;
		}
		//echo $this->db->last_query(); echo '<br>';
		// echo "<br>";
		$interest = 0;
		//----------------หาดอกเบี้ยส่วนที่ยังไม่มีการคิดดอกเบี้ย
		if($last_atm_transaction_date != ''){
			$diff = date_diff(date_create( date("Y-m-t", strtotime("-1 month", strtotime($data['date_interesting']) ) ) ), date_create($last_atm_transaction_date));
			$date_count = $diff->format("%a");
			$tmp_interest = $bf * $date_count * $loan_atm_setting['interest_rate'] / 100 / 365;
			// echo "<br>".$tmp_interest." หาดอกเบี้ยส่วนที่ยังไม่มีการคิดดอกเบี้ย<br>";
			$interest += $tmp_interest;
		}
		//----------------หา ดบ. เรียกเก็บ
		$diff = date_diff(date_create( date("Y-m-t", strtotime("-1 month", strtotime($data['date_interesting']) ) ) ), date_create( date('Y-m-t', strtotime($data['date_interesting']) ) ));
		$date_count = $diff->format("%a");
		// echo "<br>".date('Y-m-01', strtotime($data['date_interesting']) );
		$tmp_interest = $bf * $date_count * $loan_atm_setting['interest_rate'] / 100 / 365;
		// echo  $bf . " * ". $date_count . " * ". $loan_atm_setting['interest_rate'] . " / 100 / 365<br>";
		// echo $tmp_interest." -หา ดบ. เรียกเก็บ<br>";
		$interest += $tmp_interest;
		// echo "<br>remain_interest ".$remain_interest."<br>";
		//---------------หา ดบ. สะสม
		$this->db->join("coop_finance_month_detail", "coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id");
		$this->db->where("profile_month", $month);
		$this->db->where("profile_year", $year);
		$this->db->where("loan_atm_id", $data['loan_atm_id']);
		$this->db->where("pay_type", 'interest');
		$this->db->limit(1);
		$query_last_payment = $this->db->get("coop_finance_month_profile");
		//echo $this->db->last_query(); echo '<br>';
		$last_payment 		= 0;
		if($query_last_payment)
			foreach ($query_last_payment->result_array() as $key => $value) 
				$last_payment += $value['pay_amount'];
		$deduct = 0;
		$deduct = ($last_payment - $remain_interest);
		 $total_interest = ($interest + $remain_interest);
		$temp_interest 				= 0;
		$sum_of_interest 			= 0;
		$collect_interest 			= 0;
		$remain_interest 			= 0;
		$arr_month = explode('-',$data['date_interesting']);
		$last_month = date("Y-m", strtotime("-0 month", strtotime($data['date_interesting'])));
		$this->db->where("loan_atm_id", $data['loan_atm_id']);
		$this->db->where("transaction_datetime LIKE '$last_month%'");
		$query = $this->db->get("coop_loan_atm_transaction")->result_array();
		//echo $this->db->last_query(); echo '<br>';
		//echo '=============coop_loan_atm_transaction=============<br>';
		if($query){
			// echo "<br>";
			$this->db->where("loan_atm_id", $data['loan_atm_id']);
			$this->db->where("transaction_datetime < ", $query[0]['transaction_datetime']);
			$this->db->order_by("transaction_datetime DESC, loan_atm_transaction_id DESC");
			$this->db->limit(1);		
			$query_last_transaction 			= $this->db->get("coop_loan_atm_transaction")->result_array()[0];
			if($query_last_transaction){
				$last_atm_transaction_date 		= date("Y-m-d", strtotime("-0 month", strtotime($query_last_transaction['transaction_datetime'])));	
				$bf								= $query_last_transaction['loan_amount_balance'];
			}else{
				$last_atm_transaction_date 		= date("Y-m-t", strtotime("-2 month", strtotime($data['date_interesting'])));
				$this->db->order_by('transaction_datetime ASC, loan_atm_transaction_id ASC');
				$this->db->limit(1);
				$this->db->where("transaction_datetime < '$last_atm_transaction_date'");
				$this->db->where("loan_atm_id", $data['loan_atm_id']);
				$bf = $this->db->get("coop_loan_atm_transaction")->result()[0]->loan_amount_balance;
			}
			foreach ($query as $key => $value) {
				// var_dump($value);
				$diff 							= date_diff(date_create($value['transaction_datetime']), date_create($last_atm_transaction_date));
				$date_count 					= $diff->format("%a");
				$last_atm_transaction_date 		= date("Y-m-d", strtotime($value['transaction_datetime']));
				$temp_interest 					= $bf * 6 / 100 * $date_count / 365;
				$sum_of_interest 				+= $temp_interest;
				$remain_interest 				+= $temp_interest; 
				$collect_interest				= $temp_interest;
				$loan_amount_balance			= $value['loan_amount_balance'];				
			}			 
		}
		//หา ณ วันที่หักกลบ
		$diff 							= date_diff(date_create($data['date_interesting']), date_create($last_atm_transaction_date));
		$date_count 					= $diff->format("%a");
		$last_atm_transaction_date 		= date("Y-m-d", strtotime($data['date_interesting']));
		$temp_interest 					= $loan_amount_balance * 6 / 100 * $date_count / 365;
		$sum_of_interest 				+= $temp_interest;
		$remain_interest 				+= $temp_interest; 
		$collect_interest				= $temp_interest;
		// ดบ.ที่ต้องจ่าย เดือนปจจุบัน ลบ ดบ.สะสม ของเดือนก่อน
		$interest_now = $remain_interest - $deduct;
		$loan_interest = round($interest_now);
		//echo '===============interest_month===============<br>';
		//echo '<pre>'; print_r($arr_loan_interest); echo '</pre>';
		return $loan_interest;
	}
	
	public function update_loan_atm_setting_now() {
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm_setting_template');
		$this->db->where("start_date <= '".date('Y-m-d')."'");
		$this->db->order_by('start_date DESC, run_id DESC');
		if($row = $this->db->get()->row_array()) {
			$data_insert = array();
			$data_insert['prefix_code']  = $row["prefix_code"];
			$data_insert['max_loan_amount']  = $row["max_loan_amount"];
			$data_insert['interest_rate']  = $row["interest_rate"];
			$data_insert['use_atm_count']  = $row["use_atm_count"];
			$data_insert['use_atm_over_count_fee']	= $row["use_atm_over_count_fee"];
			$data_insert['min_loan_amount']  = $row["min_loan_amount"];
			$data_insert['min_month_share']  = $row["min_month_share"];
			$data_insert['max_period']  = $row["max_period"];
			$data_insert['max_withdraw_amount_day']  = $row["max_withdraw_amount_day"];
			$table = "coop_loan_atm_setting";
			$this->db->where('run_id', '1');
			$this->db->update($table, $data_insert);
		}
	}
	
	public function calc_interest_loan($loan_amount, $loan_id, $date1, $date2){
		$sql = "select loan_type from coop_loan where id = ".$loan_id." LIMIT 1";
		$loan_type = $this->db->query($sql)->result_array()[0]['loan_type'];
		$loan = @$this->db->get_where("coop_loan", array("id" => $loan_id))->result_array()[0];
		$arg = array("member_id" => $loan['member_id'], "loan_id" => $loan_id);
		$interest_rate = $this->Interest_modal->get_interest($loan_type, $date2, $arg);
		$dStart = new DateTime($date1);
		$dEnd  = new DateTime($date2);
		$dDiff = $dStart->diff($dEnd);
		$days = $dDiff->format('%r%a');
		$interest = $loan_amount * $days * $interest_rate / 100 / 365;
		return $interest;
	}

    public function calc_loan_repayment($loan_id, $s_date, $e_date){
        $sum = 0;
        $this->db->where("coop_loan_repayment.loan_id", $loan_id);
        $this->db->where("update_time >= '".$s_date."'");
        $this->db->where("update_time <= '".$e_date."'");
        $this->db->join("coop_loan_transaction", "coop_loan_repayment.loan_id = coop_loan_transaction.loan_id and coop_loan_repayment.update_time = coop_loan_transaction.transaction_datetime");
        $transction = $this->db->get("coop_loan_repayment")->result_array();
        foreach ($transction as $key => $value) {
            $diff = date_diff(date_create($value['transaction_datetime']),date_create($e_date));
            $date_count = $diff->format("%a");

            $this->db->select(array('*'));
            $this->db->from('coop_term_of_loan');
            $this->db->where("type_id = (select loan_type from coop_loan as l where l.id = '".$loan_id."' limit 1) AND start_date <= '".$e_date."'");
            $this->db->order_by("start_date DESC");
            $this->db->limit(1);
            $interest_rate = $this->db->get()->result_array()[0]['interest_rate'];

            $interest = $value['loan_request'] * $interest_rate / 100 * $date_count / $this->center_function->get_days_of_year( date("Y", strtotime($value['transaction_datetime'])) );
            $sum += $interest;
        }

        return $sum;
    }

    public function calc_interest_loan_type($loan_amount, $loan_type, $date1, $date2){
        $interest = 0;
        $d1 = date('Y', strtotime($date1));
        $d2 = date('Y',  strtotime($date2));
        $diff_y = $d2-$d1+1;

        $c_date1 = $date1;
        if($diff_y > 1){
            for ($i=0; $i < $diff_y; $i++) {

                if($diff_y-1 == $i){
                    $query = $this->db->query("select calc_loan_interest($loan_amount, $loan_type, '$c_date1', '$date2', ".$this->center_function->get_days_of_year(date('Y')).") as interest");
                }else{
                    $c_date2 = date("Y", strtotime($c_date1) ) . "-12-" . date("t", strtotime($c_date1) );
                    $query = $this->db->query("select calc_loan_interest($loan_amount, $loan_type, '$c_date1', '$c_date2' ,".$this->center_function->get_days_of_year(date('Y')).") as interest");
                }

                $c_date1 = $c_date2;
                $tmp_interest = $query->result_array();
                $interest += array_sum(array_column($tmp_interest, 'interest'));

            }
        }else{
            $query = $this->db->query("select calc_loan_interest($loan_amount, $loan_type, '$date1', '$date2' , ".$this->center_function->get_days_of_year(date('Y')).") as interest");
            $tmp_interest = $query->result_array();
            $interest += array_sum(array_column($tmp_interest, 'interest'));
        }
        return $interest;
    }

    public function calc_loan_multi_step_rate($type = '',$date_start = '', $date_end = '' ){


		$sql = "SELECT*FROM coop_term_of_loan WHERE type_id='{$type}' AND start_date<='{$date_end}' AND start_date>=(
				SELECT start_date FROM coop_term_of_loan WHERE type_id='{$type}' AND start_date<='{$date_start}' ORDER BY start_date DESC LIMIT 1) ORDER BY start_date desc";
        $term = $this->db->query($sql)->result_array();
        $interest_rate = array();
        if(sizeof($term) > 1) {
            foreach ($term as $key => $res) {
                if (date_create($res['start_date']) <= date_create($date_start) || date_create($res['start_date']) <= date_create($date_end)) {
                    $data = array();
                    $data['interest_rate'] = $res['interest_rate'];
                    $data['start_date'] = date('Y-m-d', strtotime($res['start_date'] . " -1 day"));

                    if ($key == 0) {
                        $data['end_date'] = $date_end;

                    } else if ($key == (sizeof($term) - 1)) {
                        $data['start_date'] = $date_start;
                        $data['end_date'] = $interest_rate[$key - 1]['start_date'];

                    } else {
                        $data['end_date'] = $interest_rate[$key - 1]['start_date'];

                    }
                    $interest_rate[] = $data;
                }
            }
        }else{
            foreach ($term as $key => $res) {
                $data['interest_rate'] = $res['interest_rate'];
                $data['start_date'] = $date_start;
                $data['end_date'] = $date_end;
                $interest_rate[] = $data;
            }
        }
        return $interest_rate;
    }

    public function calc_interest_loan_multi_rate($loan_amount, $loan_type, $start_date, $end_date){
        ini_set('precision', '16');
        $multiStep = self::calc_loan_multi_step_rate($loan_type, $start_date, $end_date);
        $arr = array();
        foreach ($multiStep as $key => $step){
            $step['balance'] = $loan_amount;
            $step['interest'] = round(self::calc_interest_loan_type($loan_amount, $loan_type, $step['start_date'], $step['end_date']), 2, PHP_ROUND_HALF_UP);
            $step['count'] = date_diff(date_create($step['start_date']), date_create($step['end_date']))->format('%a');
            $arr[] = $step;
        }
        if($_GET['dev'] == 1){
            echo "<pre>"; print_r($arr); exit;
        }
        return array_sum(array_column($arr, 'interest'));
    }

    public function calc_loan_atm_multi_step_rate($date_start = '', $date_end = '' ){

        $sql = "SELECT*FROM coop_loan_atm_setting_template WHERE start_date<='{$date_end}' AND start_date>=(
SELECT start_date FROM coop_loan_atm_setting_template WHERE start_date<='{$date_start}' ORDER BY start_date DESC LIMIT 1) ORDER BY start_date desc;";
        $term = $this->db->query($sql)->result_array();
        $interest_rate = array();
        if(sizeof($term) > 1) {
            foreach ($term as $key => $res) {
                if (date_create($res['start_date']) <= date_create($date_start) || date_create($res['start_date']) <= date_create($date_end)) {
                    $data = array();
                    $data['interest_rate'] = $res['interest_rate'];
                    $data['start_date'] = date('Y-m-d', strtotime($res['start_date'] . " -1 day"));

                    if ($key == 0) {
                        $data['end_date'] = $date_end;

                    } else if ($key == (sizeof($term) - 1)) {
                        $data['start_date'] = $date_start;
                        $data['end_date'] = $interest_rate[$key - 1]['start_date'];

                    } else {
                        $data['end_date'] = $interest_rate[$key - 1]['start_date'];

                    }
                    $interest_rate[] = $data;
                }
            }
        }else{
            foreach ($term as $key => $res) {
                $data['interest_rate'] = $res['interest_rate'];
                $data['start_date'] = $date_start;
                $data['end_date'] = $date_end;
                $interest_rate[] = $data;
            }
        }

        return $interest_rate;
    }

    public function calc_loan_atm_interest_multi_rate($loan_amount_balance, $start_date, $end_date){
	    ini_set('precision', '16');
        $multiStep = self::calc_loan_atm_multi_step_rate( $start_date, $end_date);
        $arr = array();
        foreach ($multiStep as $key => $step){
            $step['balance'] = $loan_amount_balance;
            $step['interest'] = number_format(self::calc_atm_loan_interest($loan_amount_balance, $step['interest'], $step['start_date'], $step['end_date']), 2, '.', '');
            $step['count'] = date_diff(date_create($step['start_date']), date_create($step['end_date']))->format('%a');
            $arr[] = $step;
        }
        return array_sum(array_column($arr, 'interest'));
    }

    public function calc_atm_loan_interest($loan_amount_balance, $rate, $start, $end){
	    ini_set('precision', '16');
        $interest = 0;
        $d1 = date('Y', strtotime($start));
        $d2 = date('Y',  strtotime($end));
        $diff_y = $d2-$d1+1;
        $c_date1 = $start;

        $dayOfyear = $this->center_function->get_days_of_year(date('Y'));

        if($diff_y > 1){
            for ($i=0; $i < $diff_y; $i++) {

                if($diff_y-1 == $i){
                    $interest += self::calc_atm_core_interest($loan_amount_balance, $c_date1, $end, $dayOfyear);
                }else{
                    $c_date2 = date("Y", strtotime($c_date1) ) . "-12-" . date("t", strtotime($c_date1) );
                    $interest += self::calc_atm_core_interest($loan_amount_balance, $c_date1, $c_date2, $dayOfyear);
                }

            }
        }else{
            $interest += self::calc_atm_core_interest($loan_amount_balance, $start, $end, $dayOfyear);
        }
        return $interest;
    }

    public function calc_atm_core_interest($loan_amount_balance, $start, $end, $day_in_year = 0){

	    if(empty($day_in_year)){
	        $day_in_year = $this->center_function->get_days_of_year(date('Y'));
        }

	    $srcYear = self::cal_days_in_year(date('Y', strtotime($start)));
	    $desYear = self::cal_days_in_year(date('Y', strtotime($end)));

	    if($srcYear == $desYear){
	        $count = date_diff(date_create($start), date_create($end))->format('%a');
	        $interest_rate =  self::calc_loan_atm_multi_step_rate($start, $end)[0]['interest_rate'];
	        return $loan_amount_balance * ($interest_rate/ 100) * $count / $day_in_year;
        }else{


	        $middle  = date('Y-12-31', strtotime($start));
	        $bf_diff = date_diff(date_create($start), date_create($middle))->format('%a');
	        $af_diff = date_diff(date_create($middle), date_create($end))->format('%a');

            $interest_rate =  self::calc_loan_atm_multi_step_rate($start, $middle)[0]['interest_rate'];
            $bf_calc_int = $loan_amount_balance * ($interest_rate/100) * $bf_diff / $day_in_year;

            $interest_rate =  self::calc_loan_atm_multi_step_rate($middle, $end)[0]['interest_rate'];
            $af_calc_int = $loan_amount_balance * ($interest_rate/100) * $af_diff / $day_in_year;

            return $bf_calc_int + $af_calc_int;
        }
    }


    function cal_days_in_year($year)
    {
        $days = 0;
        for ($month = 1; $month <= 12; $month++) {
            $days = $days + cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        return $days;
    }

}
