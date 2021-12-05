<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Atm_calc extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Loan_calculator_model", "LoanCalc");
        $this->load->model("Atm_calculator_model", "ATMCalc");
    }

    public function ex_entry(){

        $data = array();
        $data['loan_atm_id'] = 1202;
        $data['entry_date'] = '2020-11-27 00:00:00';
        $result =  $this->ATMCalc->calc('AT', $data);
        echo "<pre>"; print_r($result); exit;

    }

    public function atm_detail(){

        $loan_amt= (5000);
        $this->ATMCalc->balanceAtmDetail(1279, $loan_amt);

        $data = $this->db->select(array('loan_date', 'loan_amount', 'loan_amount_balance'))->from('coop_loan_atm_detail')
            ->where(array('loan_atm_id' => 1279))->order_by("loan_date", "asc")
            ->get()->result_array();

        header("context-type: application/json; charset=utf8");
        echo json_encode($data);
        exit;
    }

    public function ex_payment(){

        $result = $this->db->get_where('coop_finance_month_detail',
            "profile_id='3' AND loan_atm_id='953' AND pay_type='interest'")->row_array();

        $data = array();
        $data['loan_atm_id'] = 899;
        $data['entry_date'] = '2021-12-31 00:00:00';
        $data['interest'] = $result['pay_amount'];
        $data['action'] = 'payment';
        $result = $this->ATMCalc->calc('PM', $data);
        echo "<pre>"; print_r($result); exit;
    }

    public function ex_cashier(){

        $data = array();
        $data['loan_atm_id'] = 1182;
        $data['entry_date'] = "2021-01-06 00:00:00";
        $data['limit'] = array('transaction_datetime <=' => $data['entry_date']);
        $result = $this->ATMCalc->calc('PL', $data);
        echo "<pre>"; print_r($result); exit;
    }

    public function ex_return(){
        $data = array();
        $data['loan_id'] = 6037;
        $data['transaction_datetime'] = '2021-01-31 00:00:00';
        $data['interest'] = 415.08;
        $data['principal'] = 3400;
        $data['receipt_id'] = 'R25640200001';
        $this->loan_libraries->loan_transaction_arrears("RM", $data);

    }

    public function ex_process(){
        $data = array();
        $data['loan_atm_id'] = 1203;
        $data['entry_date'] = '2020-08-31 00:00:00';
        $result = $this->ATMCalc->calc('RR', $data);
        echo "<pre>"; print_r($result); exit;
    }

    public function ex_normal(){
        $data = array();
        $data['loan_id'] = $_GET['id'];
        $data['entry_date'] = $_GET['date'].' 00:00:00';
        $data['loan_type'] = $this->LoanCalc->get_loan_type($data['loan_id']);
        $data['interest'] = $_GET['interest']; //657.86
        $data['limit'] = array('transaction_datetime <=' => $data['entry_date']);
        $data['action'] = $_GET['action']; //payment
        $result = $this->LoanCalc->calc('PL', $data);
        header('Content-Type: application/json; charset=utf8');
        echo json_encode($result);
    }

    public function add(){
        $data = array();
        $data['loan_id'] = 16410;
        $data['entry_date'] = '2021-03-03 00:00:00';
        $data['loan_type'] = $this->LoanCalc->get_loan_type($data['loan_id']);
        $data['add'] = 3000000;
        $data['action'] = "add";
        $result = $this->LoanCalc->calc('AD', $data);
        header('Content-Type: application/json; charset=utf8');
        echo json_encode($result);
    }

    public function atm_balance(){
        $loan_atm_id = 1191;
        //$loan_amount_balance = 0; //real value -9810.75-9810.75
        $loan_amount_balance = -9810.75; //real value -9810.75-9810.75
        echo "<pre>";
        $this->ATMCalc->balanceAtmDetail($loan_atm_id, $loan_amount_balance);
        echo "</pre>";
    }

    public function atm_return(){

        $loan_atm_transaction = array();
        $loan_atm_transaction['loan_atm_id'] = 1191;
        $loan_atm_transaction['transaction_datetime'] = '2021-02-01 '.date("H:i:s");
        $loan_atm_transaction['receipt_id'] = "TEST RETURN";
        $loan_atm_transaction['interest'] = 489.25;
        $loan_atm_transaction['principal'] = 9810.75;

        $this->loan_libraries->atm_transaction_arrears("RM", $loan_atm_transaction);
    }

    private function find_atm_incomplete(){
        return $this->db->select('*')->from('coop_loan_atm_transaction')
            ->where(" transaction_datetime >= '2020-12-31 00:00:00' AND loan_atm_id ='".$_GET['id']."'")
            ->order_by("transaction_datetime asc")
            ->limit(1)
            ->get()->result_array();
    }

    private function find_atm_press($id, $date){
        return (double)$this->db->select('loan_amount')->from("coop_loan_atm_detail")
            ->where("loan_atm_id='{$id}' AND loan_date='{$date}'")->limit(1)->get()->row_array()['loan_amount'];
    }

    private function find_receipt($loan_atm_id, $receipt){
        return $this->db->select('sum(interest) as `interest`')->from('coop_finance_transaction')
            ->where("loan_atm_id='{$loan_atm_id}' and receipt_id='{$receipt}'")
            ->get()->row_array();
    }

    private function update_st($data = array(), $transaction_id = ''){
        $this->db->update('coop_loan_atm_transaction', $data, "loan_atm_transaction_id='{$transaction_id}'");
//        echo "<pre>";
//        $this->db->set($data);
//        $this->db->where("loan_atm_transaction_id='{$transaction_id}'");
//        echo $this->db->get_compiled_update('coop_loan_atm_transaction')."<br>";
//        echo "</pre>";
    }

    public function repair_atm_transaction(){
        $trx = self::find_atm_incomplete();
        foreach ($trx as $key => $val){
                $calc_interest['loan_atm_id'] = $val['loan_atm_id'];
                $calc_interest['entry_date'] = date( 'Y-m-d 00:00:00', strtotime($val['transaction_datetime']));
                $calc_interest['limit'] = array('loan_atm_transaction_id < ' => $val['loan_atm_transaction_id']);
            if($val['receipt_id'] == "") {
                $trx = $this->ATMCalc->calc('AT', $calc_interest);
            }else if($val['receipt_id']!= "" &&  strpos($val['receipt_id'], 'B') == false){
                $receipt = self::find_receipt($val['loan_atm_id'], $val['receipt_id']);
                $calc_interest['interest'] = $receipt['interest'];
                $trx = $this->ATMCalc->calc('PL', $calc_interest);
            }else{
                $receipt = self::find_receipt($val['loan_atm_id'], $val['receipt_id']);
                $calc_interest['interest'] = $receipt['interest'];
                $trx = $this->ATMCalc->calc('PM', $calc_interest);
            }
            $trx['loan_amount_balance'] = $trx['loan_amount_balance']+self::find_atm_press($val['loan_atm_id'], $val['transaction_datetime']);
            self::update_st($trx, $val['loan_atm_transaction_id']);$this->db->last_query();
        }
    }

    public function repair_atm_pm(){
        $trx = self::find_atm_incomplete();
        echo "<pre>"; print_r($trx);
        foreach ($trx as $key => $val) {

            foreach (self::find_problem($val) as $index => $item) {
                $calc_interest = array();
                $calc_interest['loan_atm_id'] = $item['loan_atm_id'];
                $calc_interest['entry_date'] = date('Y-m-d 00:00:00', strtotime($item['transaction_datetime']));
                $calc_interest['limit'] = array('loan_atm_transaction_id < ' => $item['loan_atm_transaction_id']);

                if ($item['loan_type_code'] == "PM" || $item['loan_type_code'] == "PL") {

                    $receipt = self::find_receipt($val['loan_atm_id'], $item['receipt_id']);
                    $calc_interest['interest'] = $receipt['interest'];
                    $calc_interest['action'] = 'payment';
                    $result = $this->ATMCalc->calc($item['loan_type_code'], $calc_interest);
                    $result['loan_amount_balance'] = self::format($item['loan_amount_balance']-$receipt['principal_payment'], 2);
                }else if($item['loan_type_code'] == "RM"){

                    $return = self::getProcessReturn($item['receipt_id']);
                    $data['interest'] = $return['return_interest'];
                    $data['principal'] = $return['return_principal'];
                    $data['receipt_id'] = $return['bill_id'];
                    $result = $this->ATMCalc->calc($item['loan_type_code'], $calc_interest);
                    $result['loan_amount_balance'] = self::format($return['return_principal']+$item['loan_amount_balance'], 2);
                }else{

                    $result = $this->ATMCalc->calc($item['loan_type_code'], $calc_interest);
                    unset($result['loan_amount_balance']);
                }


                //unset($result['loan_amount_balance']);
                //echo "<pre>"; print_r($result); echo "</pre>";
                self::update_st($result,  $item['loan_atm_transaction_id']);
            }
        }
    }

    public function find_problem($data = array()){
        return $this->db->where(array(
                'transaction_datetime >=' => date('Y-m-d 00:00:00', strtotime($data['transaction_datetime'])),
                'loan_atm_id' => $data['loan_atm_id']
            )
        )->order_by("transaction_datetime asc")
        ->get('coop_loan_atm_transaction')->result_array();
    }

    public function getProcessReturn($return_bill){
        return $this->db
            ->select(array('return_principal',	'return_interest'))
            ->where(array('bill_id' => $return_bill))
            ->get('coop_process_return')
            ->row_array();
    }

    private function format($amount, $precision = 0){ return number_format($amount, $precision, '.', '');}
}
