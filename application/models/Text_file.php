<?php
/**
 * Created by PhpStorm.
 * User: macmini2
 * Date: 2019-03-25
 * Time: 15:07
 */

class Text_file extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public $rec_type = 1;

    private $key = null;

    private $function = null;

    private $control = null;

    private $customer_status = 0;

    private $cus_expired_date = null;

    //Authorization Information (O/D Limit)
    //Authorization Information (O/D Limit)
    private $auth_info = null;

    //Debt Information
    private $dept_info = null;

    //Bank Information
    private $bank_info = null;

    public function coop_loan_atm_file(){

        $rec_number = 1;
        $maximuam_amount = 50000;
        $principle_amount = 10000;

        $txt = "";
        $txt .= $this->atm_header('083', date('Y-m-d'), "coop083");

        $txt .= $this->atm_record_detail();

        $txt .= $this->atm_footer();

        return $txt;
    }

    /**
     * @param string $id
     * @param string $date
     * @param int $rec_type
     * @return string
     */
    public function atm_header($id = '', $date = '', $file_name = "",$rec_type = 0){            
        $file_name = strtoupper($file_name);
        $file_name = $file_name.$this->add_space(35, strlen($file_name));
		$setting_file = $this->get_setting_loan_atm_file();
		$bank_atm = $setting_file['bank_atm'];
		if($bank_atm == 'ktb'){
			$get_coop_profile = $this->get_coop_profile();
			$date = date("dmy", strtotime(str_replace("-", "/", $date)));
			$bank_code = @$setting_file['bank_code'];	//BANK-CODE
			$company_code = str_pad($id, 4, '0', STR_PAD_LEFT); //COMPANY-CODE
			$company_account_no = @$setting_file['company_account_no']; //COMPANY-ACCOUNT-NO  = 9805879720 เลขทีบัญชีธนาคารของสหกรณ์
			$coop_name_th = $get_coop_profile['coop_name_th'];
			$company_name = $coop_name_th.$this->add_space(80, mb_strlen($get_coop_profile['coop_name_th'], 'UTF-8')); //COMPANY-NAME
			$post_date = date("dmy", strtotime(str_replace("-", "/", $date))); //POST-DATE			
			return sprintf("%s%s%s%s%s\r\n",$bank_code, $company_code, $company_account_no, $company_name,$post_date);
		}else{
			$date = date("d/m/Y", strtotime(str_replace("-", "/", $date)));
			$id = str_pad($id, 3, '0', STR_PAD_LEFT);
			return sprintf("%s%s%s%s%s%s\r\n",$rec_type, $id, $date, $file_name,$this->add_space(6,0),$this->add_space(300, 56));       
		}		
    }

    /**
     * @param int $rec_number
     * @param int $limit_amt
     * @param int $principle_amt
     * @param int $rec_type
     * @param string $end
     * @return string
     */
    //public function atm_footer($rec_number = 0, $limit_amt = 0, $principle_amt = 0, $rec_type = 9, $end = "END"){
    public function atm_footer($arr_data){
		return $this->layout_atm_footer($arr_data);
    }

    public function atm_record_detail($arr_data = ''){
		return $this->layout_atm_record_detail($arr_data);
    }

    public function atm_new_account_record($obj){
        if(is_array($obj)){
            $obj = (object) $obj;
        }
        $rec_type = $this->rec_type;
        $key = $this->key;
        $control = self::str_format($this->control, 2);
        $account_name_th = self::str_format($obj->account_name_th, 50, 1);
        $account_name_en = self::str_format($obj->account_name_en, 50, 1);
        $gender = self::str_format($obj->gender,1);
        $birth_day = self::str_format($obj->birth_day, 8);
        $card_type = self::str_format($obj->card_type, 2, 0, 0);
        $card_number = self::str_format($obj->card_number, 13);
        $reserve_2 = self::str_format("", 26);
        $address = self::str_format($obj->address, 80);
        $account_no = self::str_format($obj->account_no, 10);
        $other = self::str_format("", 38);
        return sprintf("%s%s%s%s%s%s%s%s%s%s%s%s%s\r\n", $rec_type, $key, $control, $account_name_th, $account_name_en, $gender, $birth_day, $card_type, $card_number, $reserve_2, $address, $account_no, $other);
    }

    function str_format($text, $lenght = 0, $str_pad = STR_PAD_LEFT, $default = " "){
        if($str_pad == STR_PAD_LEFT){
            return sprintf("%s%s", self::add_space($lenght, mb_strlen($text), $default), $text);
        }else if($str_pad == STR_PAD_RIGHT){
            return sprintf("%s%s", $text, self::add_space($lenght, mb_strlen($text), $default));
        }else{
            return sprintf("%s%s", self::add_space($lenght, mb_strlen($text), $default), $text);
        }
    }

    /**
     * @param array $keys
     * required :
     *      coop_type_id
     *      coop_customer_id
     * @return $this
     */
    public function set_keys($keys = array()){

        $coop_type_id = str_pad($keys['coop_type_id'], 6, '0', STR_PAD_LEFT);
        $coop_customer_id = str_pad($keys['coop_customer_id'], 10, '0', STR_PAD_LEFT);
        $reserve =$this->add_space(3);
        $this->key = sprintf("%s%s%s", $coop_type_id, $coop_customer_id, $reserve);
        return $this;
    }

    public function set_control($data_type = 2, $function = "C"){
        $this->control = sprintf('%s%s', $data_type, $function);
        return $this;
    }

    //Customer Status
    public function set_cus_status($status = 0){
        $this->customer_status = $status;
        return $this;
    }

    public function set_cus_expired($expired = ""){
        $this->cus_expired_date = sprintf("%s", $this->conv_datetotime(!empty($expired) ? $expired : date('Y-m-d')));
        return $this;
    }

    /**
     * @param array $params
     * @return Atm_text_file
     */
    public function set_auth_info($params = array()){

        $tran_min_limit_amt = str_pad($params['tran_min_limit_amt'], 11, '0', STR_PAD_LEFT);
        $tran_max_limit_amt = str_pad($params['tran_max_limit_amt'], 11, '0', STR_PAD_LEFT);
        $daily_limit_trans = str_pad($params['daily_limit_trans'], 5, '0', STR_PAD_LEFT);
        $daily_limit_amt = str_pad($params['daily_limit_amt'], 11, '0', STR_PAD_LEFT);
        $weekly_limit_trans = str_pad($params['weekly_limit_trans'], 5, '0', STR_PAD_LEFT);
        $weekly_limit_amt = str_pad($params['weekly_limit_amt'], 11, '0', STR_PAD_LEFT);
        $monthly_limit_trans = str_pad($params['monthly_limit_trans'], 5, '0', STR_PAD_LEFT);
        $monthly_limit_amt = str_pad($params['monthly_limit_amt'], 11, '0', STR_PAD_LEFT);
        $cus_limit_trans = str_pad($params['cus_limit_trans'], 5, '0', STR_PAD_LEFT);
        $cus_limit_amt = str_pad($params['cus_limit_amt'], 11, '0', STR_PAD_LEFT);

        $this->auth_info = sprintf("%s%s%s%s%s%s%s%s%s%s", $tran_min_limit_amt, $tran_max_limit_amt,
            $daily_limit_trans, $daily_limit_amt, $weekly_limit_trans, $weekly_limit_amt, $monthly_limit_trans,
            $monthly_limit_amt, $cus_limit_trans, $cus_limit_amt);
        return $this;
    }

    /**
     * @param array $params
     * @return Atm_text_file
     */
    public function set_dept_info($params = array()){

        //$od_principal, $od_interest, $due_date, $last_int_date, $last_pay_date, $min_pay_time,
        //$min_pay_amt, $max_pay_time, $max_pay_amt

        $od_principal = str_pad($params['od_principal'], 11,'0', STR_PAD_LEFT);
        $od_interest = str_pad($params['od_interest'], 11,'0', STR_PAD_LEFT);
        $due_date = $this->conv_datetotime(!empty($params['due_date'])  ? $params['due_date'] : date('Y-m-d')) ;
        $last_int_date = $this->conv_datetotime(!empty($params['last_int_date'])  ? $params['last_int_date'] : date('Y-m-d'));
        $last_pay_date = $this->conv_datetotime(!empty($params['last_pay_date'])  ? $params['last_pay_date'] : date('Y-m-d')) ;
        $min_pay_time = str_pad($params['min_pay_time'], 5,'0', STR_PAD_LEFT);
        $min_pay_amt = str_pad($params['min_pay_amt'], 11,'0', STR_PAD_LEFT);
        $max_pay_time = str_pad($params['max_pay_time'], 5,'0', STR_PAD_LEFT);
        $max_pay_amt = str_pad($params['max_pay_amt'], 11,'0', STR_PAD_LEFT);

        $this->dept_info = sprintf("%s%s%s%s%s%s%s%s%s", $od_principal, $od_interest, $due_date, $last_int_date, $last_pay_date,
            $min_pay_time, $min_pay_amt, $max_pay_time, $max_pay_amt);
        return $this;
    }

    public function set_bank_info($bank_account = ""){
        $bank_account = str_pad($bank_account, 10, '0', STR_PAD_LEFT);
        $this->bank_info = sprintf("%s%s\r\n", $bank_account, $this->add_space(300, 206));
        return $this;
    }
	
    /**
     * @param int $number
     * @param int $start
     * @return string
     */
    private function add_space($number = 0, $start = 0, $char = " "){
        $txt = "";
        for($i=$start; $i < $number; $i++){
            $txt .= $char;
        }
        return $txt;
    }

    public function conv_datetotime($date){
        return date('Ymd', strtotime(str_replace('-', '/', $date)));
    }

    public function test_conv_datetotime($date){
        return $this->conv_datetotime($date);
    }

    public function read_date_file_DCSDCOT($file_name,$bank_atm=""){
		$path_file = $_SERVER["DOCUMENT_ROOT"].'/assets/uploads/tmp/';
        $path_file_name = $path_file.$file_name;
        $file = file_get_contents($path_file_name);
		if($bank_atm == 'ktb'){
			$date_file = date("Y-m-d", strtotime("-1 day", strtotime(date('Y').'-'.mb_substr($file_name, 2, 2).'-'.mb_substr($file_name, 4, 2))));
			//$result = $this->dateFormat_y(mb_substr($file, 10, 6));
			$result = $date_file;
		}else{
			$result = $this->dateFormat_dmy(mb_substr($file, 5, 10));
		}
        return $result;
    }

    public function read_date_file_withdraw($file_name){
        $path_file = $_SERVER["DOCUMENT_ROOT"].'/assets/uploads/withdraw/';
		$path_file_name = $path_file.$file_name;

        $file = file_get_contents($path_file_name);

		$setting_file = $this->get_setting_loan_atm_file();
		$bank_atm = $setting_file['bank_atm'];
		if($bank_atm == 'ktb'){
			$date_file = date("Y-m-d", strtotime("-1 day", strtotime(date('Y').'-'.mb_substr($file_name, 2, 2).'-'.mb_substr($file_name, 4, 2))));
			//$result = $this->dateFormat_y(mb_substr($file, 10, 6));
			$result = $date_file;
		}else{
			$result = $this->dateFormat_dmy(mb_substr($file, 5, 10));
		}
		return $result;
    }

    public function read_file_DCSDCOT($file_name,$bank_atm="kma"){		
		$items = array();
		if($bank_atm == 'ktb'){
			$items = $this->read_file_DCSDCOT_ktb($file_name);
		}else{
			$items = $this->read_file_DCSDCOT_kma($file_name);
		}
        return $items;
    }
	
	//kma=ธนาคารกรุงศรีอยธุยา
	public function read_file_DCSDCOT_kma($file_name){
        $path_file = './assets/uploads/tmp/';
        $file_name = $path_file.$file_name;
        $file = file_get_contents($file_name);
        $all =  strlen($file);
        $data = array();
        for($row = 0; $row < ceil($all/ 160)-1; $row++){
            $line = mb_substr($file, $row*162, 160);
            if(strlen($line)){
                $data[$row] = $line;
            }
        }
        unset($data[0], $data[sizeof($data)]);
        $items = array();
        $index = 0;
        foreach ($data as $item){
            $items[$index]['CARD-NUM']         = substr($item,4, 19);
            $items[$index]['TERM-NUM']         = substr($item,26, 7);
            $items[$index]['TERM-LOCATION']    = substr($item,33, 15);
            $items[$index]['TERM-CITY']        = substr($item,48, 2);
            $items[$index]['TERM-STATE']       = substr($item,50,2);
            $items[$index]['TERM-TXSEQ']       = substr($item,52,6);
            $items[$index]['TRANS-DATE']       = substr($item,58,8);
            $items[$index]['TRANS-TIME']       = substr($item,66,6);
            $items[$index]['TRANS-CODE']       = substr($item,72,6);
            $items[$index]['FROM-AC-NUM']      = substr($item,78, 10);
            $items[$index]['TO-AC-NUM']        = substr($item,88, 10);
            $items[$index]['COOF-CUST-ID']     = substr($item,98,19);
            $items[$index]['TRANS-AMOUNT']     = substr($item,117,11);
            $items[$index]['DISP-AMOUNT']      = substr($item,128,11);
            $items[$index]['TRANS-FEE']        = substr($item,139,9);
            $items[$index]['RESP-BY']          = substr($item,148,1);
            $items[$index]['RESP-CODE-1']      = substr($item,149,1);
            $items[$index]['RESP-CODE-2']      = substr($item,150,2);
            $items[$index]['REV-CODE']         = substr($item,152,2);
            $items[$index]['APPROVE-CODE']     = substr($item,154,6);
            $index++;
        }
        return $items;
    }
	
	//ktb=ธนาคารกรุงไทย
	public function read_file_DCSDCOT_ktb($file_name){
        $path_file = './assets/uploads/tmp/';
        $file_name = $path_file.$file_name;
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
        foreach ($data as $item){			
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

    /***
     * Withdraw
     */

    public function set_withdraw_info($object){
        if(is_array($object)){
            $object = (object) $object;
        }
        $this->set_data_type($object->data_type);
        $this->set_wd_info_account_th($object->account_name_th);
        $this->set_wd_info_account_en($object->account_name_en);
        $this->set_wd_info_cust_sex($object->cust_sex);
        $this->set_wd_info_birthday($object->cust_birthday);
        if(!empty($object->card_type)) {
            $this->set_wd_info_card_type($object->card_type);
        }
        $this->set_wd_info_card_number($object->card_number);
        $this->set_wd_info_contact_address($object->cust_addr);
        $this->set_wd_info_account_no($object->account_no);
    }

    private $_withdraw_info_txt = "";
    public function print_withdraw_info(){
        $this->_withdraw_info_txt .= $this->key;
        $this->_withdraw_info_txt .= $this->_data_type;
        $this->_withdraw_info_txt .= $this->_wd_account_th;
        $this->_withdraw_info_txt .= $this->_wd_account_en;
        $this->_withdraw_info_txt .= $this->_wd_cust_sex;
        $this->_withdraw_info_txt .= $this->_wd_birthday;
        $this->_withdraw_info_txt .= $this->_wd_card_type;
        $this->_withdraw_info_txt .= $this->_wd_card_number;
        $this->_withdraw_info_txt .= $this->add_space(26);
        $this->_withdraw_info_txt .= $this->_wd_cust_addr;
        $this->_withdraw_info_txt .= $this->_wd_account_no;
        $this->_withdraw_info_txt .= $this->add_space(38);

        return sprintf("%s\r\n", $this->_withdraw_info_txt);
    }

    //file 1coop083
    public function set_wd_info_body(){

    }

    public function set_wd_del_body(){

    }

    public function set_wd_limit_body(){

    }

    /**
     * File ID : DCSDCOT
     * @param int $code :
     * 0 is no data
     * 1 is information
     * 3 is withdraw limit
     * 5 is deposit type
     * @param string $type :
     * D is delete data
     * A is Add new data
     * C is Change data
     * @return string ex:
     * 0D is delete user account no data
     * 1A is add new record information data
     * 1C is change record information data
     * 1D is delete record information data
     * 3A is add new record withdraw limit data
     * 3C is change record withdraw limit data
     * 3D is delete record withdraw limit data
     */
    private $_data_type;
    public function set_data_type($type = ""){
        $this->_data_type =  sprintf("%s", $type);
    }

    private $_wd_account_th;
    public function set_wd_info_account_th($txt = "", $length = 50){
        $this->_wd_account_th =  sprintf("%s%s",$txt, $this->add_space($length, mb_strlen($txt, 'utf-8')));
    }

    private $_wd_account_en;
    public function set_wd_info_account_en($txt = "", $length = 50){
        $this->_wd_account_en =  sprintf("%s%s",$txt, $this->add_space($length, strlen($txt)));
    }

    /**
     * set sex customer
     * @param string $txt :
     *  1 is Male,
     *  2 is Female
     * @return string
     */
    private $_wd_cust_sex = " ";
    public function set_wd_info_cust_sex($txt = "1"){
        $this->_wd_cust_sex =  sprintf("%s",$txt);
    }

    private $_wd_birthday;
    public function set_wd_info_birthday($txt = "", $length = 8){
        $this->_wd_birthday =  sprintf("%s%s",$txt, $this->add_space($length, strlen($txt), 0));
    }

    private $_wd_card_type = "01";
    public function set_wd_info_card_type($txt = '01'){
        $this->_wd_card_type =  sprintf("%s",$txt);
    }

    private $_wd_card_number;
    public function set_wd_info_card_number($txt = "", $length = 13){
        $this->_wd_card_number =  sprintf("%s%s",$txt, $this->add_space($length, strlen($txt)));
    }

    private $_wd_cust_addr;
    public function set_wd_info_contact_address($txt = "", $length = 80){
        $this->_wd_cust_addr =  sprintf("%s%s", $txt, $this->add_space($length, strlen($txt)));
    }

    private $_wd_account_no;
    public function set_wd_info_account_no($txt = "", $length = 10){
        $this->_wd_account_no = sprintf("%s%s", $txt, $this->add_space($length, strlen($txt)));
    }

    private $_wd_other;
    public function set_wd_info_other($txt = "", $length = 13){
        $this->_wd_other =  sprintf("%s%s", $txt, $this->add_space($length, strlen($txt)));
    }

    /********************************************
     * WithDraw Limit
     * */

    public function set_withdraw_limit($object){
        if(is_array($object)){
            $object = (object) $object;
        }
        $this->set_data_type($object->data_type);
        $this->set_wl_cust_status($object->cust_status);
        $this->set_wl_cust_exp_date($object->cust_exp_date);
        $this->set_wl_min_limit_amt_tran($object->min_limit_amt_tran);
        $this->set_wl_max_limit_amt_tran($object->max_limit_amt_tran);
        $this->set_wl_daily_limit_tran($object->daily_limit_tran);
        $this->set_wl_daily_limit_amt($object->daily_limit_amt);
        $this->set_wl_weekly_limit_tran($object->weekly_limit_tran);
        $this->set_wl_weekly_limit_amt($object->weekly_limit_amt);
        $this->set_wl_monthly_limit_tran($object->monthly_limit_tran);
        $this->set_wl_monthly_limit_amt($object->monthly_limit_amt);
        $this->set_wl_cust_limit_tran($object->cust_limit_tran);
        $this->set_wl_cust_limit_amt($object->cust_limit_amt);
        $this->set_wl_withdraw($object->withdraw);
        $this->set_wl_coop_account($object->coop_account);
    }

    private $_print_withdraw_limit = "";
    public function print_withdraw_limit(){
        $this->_print_withdraw_limit = $this->key;
        $this->_print_withdraw_limit .= $this->_data_type;
        $this->_print_withdraw_limit .= $this->_wl_cust_status;
        $this->_print_withdraw_limit .= $this->_wl_cust_exp_date;
        $this->_print_withdraw_limit .= $this->_wl_min_limit_amt_tran;
        $this->_print_withdraw_limit .= $this->_wl_max_limit_amt_tran;
        $this->_print_withdraw_limit .= $this->_wl_daily_limit_tran;
        $this->_print_withdraw_limit .= $this->_wl_daily_limit_amt;
        $this->_print_withdraw_limit .= $this->_wl_weekly_limit_tran;
        $this->_print_withdraw_limit .= $this->_wl_weekly_limit_amt;
        $this->_print_withdraw_limit .= $this->_wl_monthly_limit_tran;
        $this->_print_withdraw_limit .= $this->_wl_monthly_limit_amt;
        $this->_print_withdraw_limit .= $this->_wl_cust_limit_tran;
        $this->_print_withdraw_limit .= $this->_wl_cust_limit_amt;
        $this->_print_withdraw_limit .= $this->_wl_withdraw;
        $this->_print_withdraw_limit .= $this->add_space(67);
        $this->_print_withdraw_limit .= $this->_wl_coop_account;
        $this->_print_withdraw_limit .= $this->add_space(95);
        return sprintf("%s\r\n", $this->_print_withdraw_limit);
    }

    private $_wl_cust_status;
    public function set_wl_cust_status($txt = 0, $length = 1){
        $this->_wl_cust_status = sprintf("%s%s", $txt, $this->add_space($length, strlen($txt), 0));
    }

    private $_wl_cust_exp_date;
    public function set_wl_cust_exp_date($txt = "", $length = 8){
        $this->_wl_cust_exp_date = sprintf("%s%s", $txt, $this->add_space($length, strlen($txt)));
    }

    /**
     * Minimum amount per transaction
     */
    private $_wl_min_limit_amt_tran;
    public function set_wl_min_limit_amt_tran($txt = "", $length = 11){
        $this->_wl_min_limit_amt_tran = sprintf("%s%s", $this->add_space($length, strlen($txt), 0),  $txt);
    }

    /***
     * Maximum amount per transaction
     */
    private $_wl_max_limit_amt_tran;
    public function set_wl_max_limit_amt_tran($txt = "", $length = 11){
        $this->_wl_max_limit_amt_tran = sprintf("%s%s", $this->add_space($length, strlen($txt), 0),  $txt);;
    }

    /**
     * Maximum transaction per day
     */
    private  $_wl_daily_limit_tran;
    public function set_wl_daily_limit_tran($txt = "", $length = 5){
        $this->_wl_daily_limit_tran = sprintf("%s%s", $this->add_space($length, strlen($txt), 0),  $txt);
    }

    /**
     * Maximum amount per day
     */
    private $_wl_daily_limit_amt;
    public function set_wl_daily_limit_amt($txt = "", $length = 11){
        $this->_wl_daily_limit_amt = sprintf("%s%s", $this->add_space($length, strlen($txt), 0),  $txt);
    }

    /**
     * Maximum transaction per week
     */
    private $_wl_weekly_limit_tran;
    public function set_wl_weekly_limit_tran($txt = "", $length = 5){
        $this->_wl_weekly_limit_tran = sprintf("%s%s", $this->add_space($length, strlen($txt), 0),  $txt);
    }

    /**
     * Maximum amount per week
     */
    private $_wl_weekly_limit_amt;
    public function set_wl_weekly_limit_amt($txt = "", $length = 11){
        $this->_wl_weekly_limit_amt = sprintf("%s%s", $this->add_space($length, strlen($txt), 0),  $txt);
    }

    /**
     * Maximum transaction per month
     */
    private $_wl_monthly_limit_tran;
    public function set_wl_monthly_limit_tran($txt = "", $length = 5){
        $this->_wl_monthly_limit_tran =  sprintf("%s%s", $this->add_space($length, strlen($txt), 0),  $txt);
    }

    /**
     * Maximum amount per month
     */
    private $_wl_monthly_limit_amt;
    public function set_wl_monthly_limit_amt($txt = "", $length = 11){
        $this->_wl_monthly_limit_amt = sprintf("%s%s", $this->add_space($length, strlen($txt), 0),  $txt);
    }

    /**
     * Maximum transaction per customer (customer limit)
     */
    private $_wl_cust_limit_tran;
    public function set_wl_cust_limit_tran($txt = "", $length = 5){
        $this->_wl_cust_limit_tran = sprintf("%s%s", $this->add_space($length, strlen($txt), 0),  $txt);
    }

    /**
     * Maximum amount per customer (customer limit)
     */
    private $_wl_cust_limit_amt;
    public function set_wl_cust_limit_amt($txt = "", $length = 11){
        $this->_wl_cust_limit_amt = sprintf("%s%s", $this->add_space($length, strlen($txt), 0),  $txt);
    }

    /****************************************************
     * COOP Information
     */

    /***
     * Withdraw Available
     */
    private $_wl_withdraw;
    public function set_wl_withdraw($txt = "", $length = 11){
        $this->_wl_withdraw = sprintf("%s%s", $this->add_space($length, strlen($txt), 0),  $txt);
    }

    /***
     *COOP customer account in BAY
     */
    private $_wl_coop_account;
    public function set_wl_coop_account($txt = "", $length = 10){
        $this->_wl_coop_account = sprintf("%s%s", $txt, $this->add_space($length, strlen($txt)));
    }

    /******************************************************
     * Deposit data
     ******************************************************/
    public function set_deposit_info($object){
        if(is_array($object)){
            $object = (object) $object;
        }
        $this->set_data_type($object->data_type);
        $this->setDepositNameAccout($object->account_name);
        $this->setDepositCoopAccount($object->account_id);
    }

    public $_dp_info_txt = "";
    public function print_deposit_info_st(){
        //echo " length : ".strlen($this->_dp_name_acc.$this->add_space(180));
        $this->_dp_info_txt .= $this->key;
        $this->_dp_info_txt .= $this->_data_type;
        $this->_dp_info_txt .= $this->_dp_name_acc;
        $this->_dp_info_txt .= $this->add_space(180);
        $this->_dp_info_txt .= $this->_dp_coop_acc;
        $this->_dp_info_txt .= $this->add_space(38);
        return sprintf("%s\r\n", $this->_dp_info_txt);
    }

    public $_dp_name_acc = "";
    private function setDepositNameAccout($text, $length = 50){
        $this->_dp_name_acc = sprintf("%s%s", $text, $this->add_space($length, mb_strlen($text, 'utf-8')));
    }

    public $_dp_coop_acc = "";
    private function setDepositCoopAccount($text, $length = 10){
        $this->_dp_coop_acc =  sprintf("%s%s", $this->add_space($length, strlen($text), 0), $text);
    }


    /**
     * @param string $id
     * @param string $date
     * @param int $rec_type
     * @return string
     */
    public function atm_deposit_header($id = '', $date = '', $file_name = "",$rec_type = 0){
		$file_name = strtoupper($file_name);
        $file_name = $file_name.$this->add_space(35, strlen($file_name));
		$setting_file = $this->get_setting_loan_atm_file();
		$bank_atm = $setting_file['bank_atm'];
		if($bank_atm == 'ktb'){
			$get_coop_profile = $this->get_coop_profile();
			$date = date("dmy", strtotime(str_replace("-", "/", $date)));
			$bank_code = @$setting_file['bank_code'];	//BANK-CODE
			$company_code = str_pad($id, 4, '0', STR_PAD_LEFT); //COMPANY-CODE
			$company_account_no = @$setting_file['company_account_no']; //COMPANY-ACCOUNT-NO  = 9805879720 เลขทีบัญชีธนาคารของสหกรณ์
			$coop_name_th = $get_coop_profile['coop_name_th'];
			$company_name = $coop_name_th.$this->add_space(80, mb_strlen($get_coop_profile['coop_name_th'], 'UTF-8')); //COMPANY-NAME
			$post_date = date("dmy", strtotime(str_replace("-", "/", $date))); //POST-DATE			
			return sprintf("%s%s%s%s%s\r\n",$bank_code, $company_code, $company_account_no, $company_name,$post_date);
		}else{
			$date = date("d/m/Y", strtotime(str_replace("-", "/", $date)));
			$id = str_pad($id, 3, '0', STR_PAD_LEFT);
			return sprintf("%s%s%s%s%s%s\r\n",$rec_type, $id, $date, $file_name,$this->add_space(6,0),$this->add_space(245));
		}
	}

    /**
     * @param int $rec_number
     * @param int $limit_amt
     * @param int $principle_amt
     * @param int $rec_type
     * @param string $end
     * @return string
     */
    public function atm_deposit_footer($rec_number = 0, $limit_amt = 0, $principle_amt = 0, $rec_type = 9, $end = "END"){
		$setting_file = $this->get_setting_loan_atm_file();
		$bank_atm = $setting_file['bank_atm'];		
		if($bank_atm == 'ktb'){
			$file_type = 'D';//FILE-TYPE ประเภทไฟล์ L=เงินกู้,D=เงินฝาก
			$total_amount = $this->str_format($this->toString($principle_amt),15,0,'0');//TOTAL-AMOUNT ยอดเงินรวมทั้งสิ้น  "000000005000000" = 50000.00
			$total_count = $this->str_format($rec_number,6,0,'0');//TOTAL-COUNT  จํานวนรายการ
			return sprintf("%s%s%s\r\n",$file_type, $total_amount, $total_count);
		}else{
			$rec = str_pad($rec_number, 9, '0', STR_PAD_LEFT);
			$limit = str_pad($this->toString($limit_amt), 13, '0', STR_PAD_LEFT);
			$pricipal = str_pad($this->toString($principle_amt), 13, '0', STR_PAD_LEFT);
			return sprintf("%s%s%s%s%s%s\r\n",$rec_type, $end, $rec, $limit, $pricipal, $this->add_space(261));
		}
    }
	
	function tis620_to_utf8($tis) {
        $utf8 = "";
        for( $i=0 ; $i< strlen($tis) ; $i++ ){
            $s = substr($tis, $i, 1);
            $val = ord($s);
            if( $val < 0x80 ){
                $utf8 .= $s;
            } elseif ((0xA1 <= $val and $val <= 0xDA)
                or (0xDF <= $val and $val <= 0xFB))  {
                $unicode = 0x0E00 + $val - 0xA0;
                $utf8 .= chr( 0xE0 | ($unicode >> 12) );
                $utf8 .= chr( 0x80 | (($unicode >> 6) & 0x3F) );
                $utf8 .= chr( 0x80 | ($unicode & 0x3F) );
            }
        }
        return $utf8;
    }
	
	public  function removeSpace($string = ""){
        return preg_replace('/\s+/', '', $string);
    }

	//convert date format Ymd to Y-m-d
    public function dateFormat($date){	   
	    return substr($date, 0, 4)."-".substr($date,4,2)."-".substr($date,6,2);
    }
	
	//convert date format ymd to Y-m-d
    public function dateFormat_y($date){
		$date_y = substr($date, 4, 2)."-".substr($date,2,2)."-".substr($date,0,2);
		$date_yy = date('Y-m-d', strtotime($date_y));
		return $date_yy;
    }
	
	//convert date format d/m/Y to Y-m-d
    public function dateFormat_dmy($date){	   
	    return date('Y-m-d', strtotime(str_replace('/','-', $date)));
    }

    public function timeFormat($time){
	    return substr($time, 0, 2).":".substr($time, 2, 2).":".substr($time, 4, 2);
    }

    public  function toNumber($number)
    {
        return round((double) $number / 100, 2);
    }

    public function toMemberID($member){
	    return str_pad(substr($this->removeSpace($member), -6, 6), 6, '0');
    }

    public function toString($amt){
        return str_replace('.', '', number_format($amt, 2, '.', ''));
    }	
	
	//ตั้งค่าข้อมูลการธนาคาร Offline
	public function get_setting_loan_atm_file(){
		$result = array();
		$setting_atm_file = $this->db->select(array("*"))->from('coop_setting_loan_atm_file')->limit(1)->get()->row_array();
		if(!empty($setting_atm_file)){
			$file_extension = $setting_atm_file['file_extension'];
			$file_type = $setting_atm_file['file_type'];
			$bank_atm = $setting_atm_file['bank_atm'];
		}else{
			$file_extension = 'txt';
			$file_type = 'text/plain';
			$bank_atm = 'kma';			
		}
		$coop_code = $setting_atm_file['coop_code'];
		$company_account_no = $setting_atm_file['company_account_no'];
		$bank_code = $setting_atm_file['bank_code'];
		
		$result['file_extension'] = $file_extension;
		$result['file_type'] = $file_type;
		$result['bank_atm'] = $bank_atm;
		$result['coop_code'] = $coop_code;
		$result['company_account_no'] = $company_account_no;
		$result['bank_code'] = $bank_code;
		return $result;
	}
	
	//ข้อมูลสหกรณ์
	public function get_coop_profile(){
		$result = $this->db->select(array("*"))->from('coop_profile')->limit(1)->get()->row_array();		
		return $result;
	}
	
	//set layout file
	public function layout_atm_header($arr_data){            
		
    }
	
	public function layout_atm_record_detail($arr_data){
		$setting_file = $this->get_setting_loan_atm_file();
		$bank_atm = $setting_file['bank_atm'];
		if($bank_atm == 'ktb'){
			$acc_no = $this->str_format($arr_data['acc_no'],10,0,'0');//ACC-NO	เลขที่บัญชีธนาคารของสมาชิก 8021300039
			$item_type = $arr_data['item_type'];//ITEM-TYPE	ประเภทธุรกรรม 	"R" ใช้เลขนี้เสมอ
			$item_amount = $this->str_format($this->toString($arr_data['item_amount']),15,0,'0'); //ITEM-AMOUNT	จํานวนเงิน(วงเงิน)
			$operate_date = date('dmy', strtotime($arr_data['operate_date']));//OPERATE-DATE	วันที่ทํารายการ  DDMMYY (ค.ศ.)
			$operate_time = date('His', strtotime($arr_data['operate_time']));//OPERATE-TIME	เวลาที่ทํารายการ 	HHMMSS
 			$member_no = $this->str_format($arr_data['member_no'],6,0,'0');//MEMBER-NO 	เลขที่สมาชกสหกรณ์ 	"024646"
			$balance_amount = $this->str_format($this->toString($arr_data['balance_amount']),15,0,'0');//BALANCE-AMOUNT 	หนี้คงเหลือ/เงินฝากคงเหลือ
			$expire_date = date('dmy', strtotime($arr_data['expire_date'])).'000000';//EXPIRE-DATE 	วันที่หมดอายุวงเงิน DDMMYYhhmmss (ค.ศ.)
			$reference_key = $this->str_format($arr_data['reference_key'],10,1);//REFERENCE-KEY 	Referrence Key ฝั่งสหกรณ์ เป็นเลขสัญญาเงินกู้ เช่น ฉก.490001 	 หรือ เลขบัญชีเงินฝาก เช่น 0110000001
			$member_name = $this->str_format($arr_data['member_name'],30,1);//MEMBER-NAME	ชื่อตัวสมาชิก
			$memeber_surname = $this->str_format($arr_data['memeber_surname'],30,1);//MEMBER-SURNAME	ชื่อสกุลสมาชิก(นามสกุล)
			$person_id = $this->str_format($arr_data['person_id'],13,1);//PERSON-ID 	เลขประจำตัวประชาชน
			
			return sprintf("%s%s%s%s%s%s%s%s%s%s%s%s\r\n",
				$acc_no, $item_type, $item_amount, $operate_date, $operate_time, $member_no, 
				$balance_amount, $expire_date,$reference_key,$member_name,$memeber_surname,$person_id);
		}else{
			return sprintf("%s%s%s%s%s%s%s%s",
            $this->rec_type, $this->key, $this->control, $this->customer_status,
            $this->cus_expired_date, $this->auth_info, $this->dept_info, $this->bank_info);
		}
    }
	
	public function layout_atm_footer($arr_data){  
		$setting_file = $this->get_setting_loan_atm_file();
		$bank_atm = $setting_file['bank_atm'];		
		if($bank_atm == 'ktb'){
			$file_type = 'L';//FILE-TYPE ประเภทไฟล์ L=เงินกู้,D=เงินฝาก
			$total_amount = $this->str_format($this->toString($arr_data['total_amount']),15,0,'0');//TOTAL-AMOUNT ยอดเงินรวมทั้งสิ้น  "000000005000000" = 50000.00
			$total_count = $this->str_format($arr_data['rec_num'],6,0,'0');//TOTAL-COUNT  จํานวนรายการ
			return sprintf("%s%s%s\r\n",$file_type, $total_amount, $total_count);
		}else{	
			$rec_type = $arr_data['rec_type'];
			$end = $arr_data['end'];
			$rec = str_pad($arr_data['rec_number'], 9, '0', STR_PAD_LEFT);
			$limit = str_pad($arr_data['limit_amt'], 13, '0', STR_PAD_LEFT);
			$pricipal = str_pad($arr_data['principle_amt'], 13, '0', STR_PAD_LEFT);		
			return sprintf("%s%s%s%s%s%s\r\n",$rec_type, $end, $rec, $limit, $pricipal, $this->add_space(300, 40));
		}		
    }
}
