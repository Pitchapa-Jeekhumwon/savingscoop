<?php defined('BASEPATH') OR exit('No direct script access allowed');

 class Atm_calculator_model extends CI_Model {

    private $_setting = null;
    private $_item_type = null;
    private $_calc = null;
    private $_interest_round = null;

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Setting_model", 'settingModel');
        self::init();
    }

    public function init(){
        self::setting();
    }

     private function _getPrecision(){
         if($this->_interest_round != null){
             return $this->_interest_round;
         }
         return $this->_interest_round = $this->settingModel->get('round_interest');
     }

    public function setting(){
        $this->_setting = $this->db->get('coop_atm_type_setting')->result_array();
        $num = 0;
        foreach ($this->_setting as $key => $setting){
            $this->_item_type[$setting['atm_type_code']] = $setting['atm_sign_mode'];
            $num++;
        }
    }

    public function calc($type_code, $entry_data = array()){

        if( $this->_item_type[$type_code] == "-1"){
            return self::calcPayment($type_code, $entry_data);
        }else if($this->_item_type[$type_code] == "1"){
            return self::calcEntry($type_code, $entry_data);
        }else {
            return self::calcNoop($type_code, $entry_data);
        }
    }

    private function calcPayment($type_code, $entry_data = array()){

        ini_set("precision", '16');
        $last_stm = self::getStmBeforePayment($entry_data['loan_atm_id'], $entry_data['limit']);
        if($_GET['debug'] == "on"){
            echo $this->db->last_query(); exit;
        }
        $start =  date('Y-m-d', strtotime($last_stm['transaction_datetime']));
        $end = date('Y-m-d', strtotime($entry_data['entry_date']));
        $cal_atm_interest = array();
        $cal_atm_interest['loan_atm_id'] = $entry_data['loan_atm_id'];
        $cal_atm_interest['date_interesting'] = $end;
        $interest = round($this->loan_libraries->calc_loan_atm_interest_multi_rate($last_stm['loan_amount_balance'], $start, $end), self::_getPrecision());

        //calc interest arrears total
        $arrears_total = round($last_stm['interest_arrear_bal'] + $interest, self::_getPrecision());
        $interest_not_pay = 0;

        if(in_array($type_code, array('PM'))) {
            //calc not pay
            if (isset($entry_data['action']) && $entry_data['action'] == 'payment') {
                if ($entry_data['interest']) {
                    $arrears_balance = $arrears_total - $entry_data['interest'];
                    $interest_not_pay =  ($entry_data['interest']) - $arrears_total;
                }
                //calc arrears balances
                $start = $entry_data['start_date'] ? $entry_data['start_date'] : self::getStmtPaymentLast($entry_data['loan_atm_id'], $entry_data['entry_date'])['transaction_datetime'];
            }else{
                $arrears_balance = $arrears_total;
            }

        }else{

            if (isset($entry_data['action']) && $entry_data['action'] == 'payment') {
                if(isset($entry_data['interest']) && $entry_data['interest']){
                    $interest_not_pay = $entry_data['interest'] - $arrears_total;
                    $arrears_balance = $arrears_total - $entry_data['interest'];
                }else {
                    $arrears_balance = $interest_not_pay;
                }
            }else{
                $arrears_balance = $arrears_total;
            }
        }

        $result = array();
        $result['loan_amount_balance'] = $last_stm['loan_amount_balance'];
        $result['loan_type_code'] = $type_code;
        $result['interest_from'] = $start;
        $result['interest_to'] = $end . ' 00:00:00';
        $result['interest_arrears'] = self::mb_round($last_stm['interest_arrear_bal'], self::_getPrecision());
        $result['interest_calculate_arrears'] = self::mb_round($interest, self::_getPrecision());
        $result['interest_arrear_bal'] = self::mb_round($arrears_balance, self::_getPrecision());
        $result['interest_notpay'] = self::mb_round((float)$interest_not_pay, self::_getPrecision());
        return $result;
    }

    private function calcEntry( $type_code, $entry_data = array()){
        $last_stm = self::getStmBeforePayment($entry_data['loan_atm_id'], $entry_data['limit']);
        $start =  date('Y-m-d', strtotime($last_stm['transaction_datetime']));
        $end = date('Y-m-d', strtotime($entry_data['entry_date']));
        $interest = $this->loan_libraries->calc_loan_atm_interest_multi_rate($last_stm['loan_amount_balance'], $start, $end);
        //$int_arrears_bal = $last_stm['interest_arrear_bal']+$interest;

        $x = 0;
        if($type_code == "RM" && !empty($entry_data['principal'])) {
            $int_arrears_bal = $last_stm['interest_arrear_bal'] + $entry_data['interest'] + $interest;
            $notpay = $x - $int_arrears_bal; //$x ยังไม่ทราบค่าที่แน่ชัด
        }else if($type_code == "RR"){
            $int_arrears_bal = $entry_data['interest'];
            $notpay = $entry_data['interest']*(-1);
        }else if($type_code == "AT"){
            $int_arrears_bal = self::mb_round((($last_stm['interest_arrear_bal'] - $entry_data['interest']) + $interest), self::_getPrecision());
            $notpay = $entry_data['interest']-$int_arrears_bal;
        }else{
            $int_arrears_bal = self::mb_round((($last_stm['interest_arrear_bal'] + $entry_data['interest']) + $interest), self::_getPrecision());
            $notpay = $entry_data['interest']-$int_arrears_bal;
        }
        if($type_code == "RM" && !empty($entry_data['principal'])){
            $last_stm['loan_amount_balance'] = self::mb_round($last_stm['loan_amount_balance']+$entry_data['principal'], self::_getPrecision());
        }
        $result = array();
        $result['loan_amount_balance'] = $last_stm['loan_amount_balance'];
        $result['loan_type_code'] = $type_code;
        $result['interest_from'] = $start . ' 00:00:00'; ;
        $result['interest_to'] = $end . ' 00:00:00';;
        $result['interest_arrears'] = self::mb_round($last_stm['interest_arrear_bal'], self::_getPrecision());
        $result['interest_calculate_arrears'] = self::mb_round($interest, self::_getPrecision());
        $result['interest_arrear_bal'] = self::mb_round($int_arrears_bal, self::_getPrecision());
        $result['interest_notpay'] = self::mb_round($notpay, self::_getPrecision());
        if($type_code == "RM") {
            $result['transaction_datetime'] = $last_stm['transaction_datetime'];
        }
        return $result;
    }

    private function calcNoop($type_code, $entry_data = array()){

        $last_stm = self::getStmBeforePayment($entry_data['loan_atm_id']);
        if(sizeof($last_stm)){
            $result['interest_arrear_bal'] = $last_stm['interest_arrear_bal'];
        }else{
            $result['interest_arrear_bal'] = "";
        }

        $result = array();
        $result['loan_amount_balance'] = "";
        $result['loan_type_code'] = $type_code;
        $result['interest_from'] = "";
        $result['interest_to'] = "";
        $result['interest_arrears'] = "";
        $result['interest_calculate_arrears'] = "";
        $result['interest_notpay'] = "";
        return $result;
    }

    private function calcSpecial($type_code, $entry_data = array()){
        return array();
    }

    public function getStmBeforePayment($loan_id, $limit = array()){
        if(!empty($limit)){
            $this->db->where($limit);
        }
        $this->db->order_by('transaction_datetime, loan_atm_transaction_id', 'desc');
        $this->db->where(array('loan_atm_id' => $loan_id));
        return $this->db->get('coop_loan_atm_transaction', 1)->row_array();
    }

    public function getStmPreventDate($loan_id, $entry_date){
        $this->db->order_by('loan_atm_transaction_id', 'desc');
        $this->db->where(array('loan_atm_id' => $loan_id, 'transaction_datetime <=' => $entry_date ));
        return $this->db->get('coop_loan_atm_transaction', 1)->row_array();
    }

    private function getPaymentCode(){
        if($this->_setting) {
            $arr = array();
            foreach ($this->_setting as $key => $val){
                if($val['atm_sign_mode'] == '-1'){
                    $arr[] = $val['atm_type_code'];
                }
            }
            return $arr;
        }
        return array();
    }

     public function getStmtPaymentLast($loan_id, $entry_date){
         $this->db->order_by('loan_atm_transaction_id', 'desc');
         $this->db->where(array('loan_atm_id' => $loan_id, 'transaction_datetime <=' => $entry_date));
         $this->db->where_in('loan_type_code', self::getPaymentCode());
         return $this->db->get('coop_loan_atm_transaction', 1)->row_array();
     }

     public function  getStmScopeTarget($loan_id, $prev_date){
        $this->db->order_by('loan_atm_transaction_id', 'desc');
        $this->db->where(array('loan_atm_id' => $loan_id, 'transaction_datetime >=' => $prev_date ));
        return $this->db->get('coop_loan_atm_transaction', 1)->row_array();
    }

     public function mb_round($x, $f = 0){
         return number_format($x, $f, '.', '');
     }

     public function balanceAtmDetail($loan_atm_id = "", $principal = 0){

        if(empty($loan_atm_id)){
            return;
        }

         $atmDetail = self::getAtmDetail($loan_atm_id);
         $_loan_amount_balance = self::mb_round(array_sum(array_column($atmDetail, 'loan_amount_balance')), 2);
         $loan_amount_balance = self::mb_round($_loan_amount_balance - $principal, 2);

         if(sizeof($atmDetail)){
             $arr = array();

             if(!empty($loan_amount_balance)){
                 $this->db->set('total_amount_balance', "(total_amount_approve-({$loan_amount_balance}))", false);
             }else if($loan_amount_balance == 0){
                 $this->db->set('total_amount_balance', "0");
             }
             $this->db->where(array('loan_atm_id' => $loan_atm_id));
             $this->db->update("coop_loan_atm");

             foreach ($atmDetail as $key => $val){

                 if($loan_amount_balance <= $val['loan_amount_balance'] || $loan_amount_balance == 0){


                     $arr['loan_amount_balance'] = $loan_amount_balance;
                     if($loan_amount_balance == 0){
                         $arr['loan_status'] = 1;
                     }
                     $this->db->set($arr);
                     $this->db->where(array('loan_id' =>  $val['loan_id']));
                     $this->db->update("coop_loan_atm_detail");
                     //echo $this->db->get_compiled_update('coop_loan_atm_detail') ."<br>";
                 }
                 $loan_amount_balance = self::mb_round($loan_amount_balance-$val['loan_amount_balance'], 2);
             }
         }else if($loan_amount_balance < 0){
             $val = self::getAtmDetailLast($loan_atm_id);
             $arr['loan_amount_balance'] = $loan_amount_balance;
             $this->db->set($arr);
             $this->db->where(array('loan_id' =>  $val['loan_id']));
             $this->db->update('coop_loan_atm_detail');

             $this->db->set('total_amount_balance', "(total_amount_approve-({$loan_amount_balance}))", false);
             $this->db->where(array('loan_atm_id' => $loan_atm_id));
             $this->db->update("coop_loan_atm");
         }
     }

     public function getAtmDetail($loan_atm_id){
         return $this->db->order_by("loan_date desc")
             ->get_where("coop_loan_atm_detail", array('loan_atm_id' => $loan_atm_id, 'loan_status' => 0))
             ->result_array();
     }

     public function getAtmDetailLast($loan_atm_id){
         return $this->db->order_by("loan_date desc")
             ->get_where("coop_loan_atm_detail", array('loan_atm_id' => $loan_atm_id), 1)
             ->row_array();
     }

     public function getBalanceDetail($loan_atm_id){
        $stmt = "SELECT sum(loan_amount_balance) as loan_amount_balance 
        FROM coop_loan_atm_detail WHERE loan_atm_id='{$loan_atm_id}' AND loan_status='0';";
        return $this->db->query($stmt)->row()->loan_amount_balance;
     }

     public function getLoanAtm($loan_atm_id){
        return $this->db->where(array('loan_atm_id' => $loan_atm_id))->get("coop_loan_atm")->row_array();
     }

     public function getContractByMember($member_id){
        return $this->db->where(array("member_id" => $member_id))
            ->get("coop_loan_atm")->result_array();
     }

     public function getLoanAtmTransaction($loan_atm_id, $year = ""){
         return $this->db->where(array("loan_atm_id" => $loan_atm_id))
             ->get("coop_loan_atm_transaction")->result_array();
     }

     public function getLoanAtmFinance($loan_atm_id, $year = ""){
         return $this->db->where(array("loan_atm_id" => $loan_atm_id))
             ->get("coop_finance_transaction")->result_array();
     }

     public function getReason($reason_id){
        return $this->db->get_where("coop_loan_reason", array("loan_reason_id" => $reason_id))->row()->loan_reason;
     }

     private function range(){

     }



}
