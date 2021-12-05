<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Non_pay_model extends CI_Model
{

    private $_non_pay_data = null;

     public function __construct()
     {
         parent::__construct();
         $this->_non_pay_data = null;
     }


     public function PaymentNonPay($id, $principal, $interest, $date, $receipt_id, $type = 'loan'){

         //todo หายอดที่จะหัก non_pay_detail
         $non_pay = self::findNonPay($id, $date, $type);

         if(sizeof($non_pay)){
             foreach ($non_pay as $key => $detail){
                 $set_detail = array();
                 $where_detail = array();
                 if($detail['pay_type'] == 'principal') {
                     if($detail['non_pay_amount_balance'] < $principal){
                         $set_detail['non_pay_amount_balance'] = 0;
                         $principal -= $detail['non_pay_amount_balance'];
                     }else if($detail['non_pay_amount_balance'] >= $principal) {
                         $set_detail['non_pay_amount_balance'] = $detail['non_pay_amount_balance'] - $principal;
                         $principal = 0;
                     }
                 }
                 if($detail['pay_type'] == 'interest') {
                     if($detail['non_pay_amount_balance'] < $interest){
                         $set_detail['non_pay_amount_balance'] = 0;
                         $interest -= $detail['non_pay_amount_balance'];
                     }else if($detail['non_pay_amount_balance'] >= $interest) {
                         $set_detail['non_pay_amount_balance'] = $detail['non_pay_amount_balance'] - $interest;
                         $interest = 0;
                     }
                 }
                 $where_detail['run_id'] = $detail['run_id'];
                 //todo อัพเดทยอด non_pay_detail
                 self::updateStNonPay($set_detail, $where_detail);
                 //todo อัพเดทยอด non_pay
                 self::updateStatusNonPay($detail['non_pay_id']);
                 self::insertNonPayReceipt($detail['non_pay_id'], $receipt_id, $date);
             }
         }
     }

    public function findNonPay($id, $date, $type = 'loan'){
         $date = date('Y-m-t', strtotime($date." -".date('d', strtotime($date))." day"));
         $month = date("n", strtotime($date));
         $year = date("Y", strtotime($date))+543;
         return $this->db->select(array(
             't1.run_id',
             't1.non_pay_amount_balance',
             't1.pay_type',
             't1.non_pay_id',
             't1.finance_month_profile_id',
             't2.non_pay_month',
             't2.non_pay_year'))->from('coop_non_pay_detail as t1')
             ->join('coop_non_pay as t2', "t1.non_pay_id = t2.non_pay_id", 'inner')
             ->where(array('t1.'.self::check_type($type) => $id,
                 't1.non_pay_amount_balance >' => '0',
                 't2.non_pay_month >' => $month,
                 't2.non_pay_year >=' => $year))
             ->order_by("t1.finance_month_profile_id", "asc")
             ->get()->result_array();
    }

    private function check_type($type){
         switch (strtoupper($type)){
             case 'LOAN' : return 'loan_id';
             case 'ATM' : return 'loan_atm_id';
             case 'SHARE' : return 'member_id';
             case 'DEPOSIT' : return 'deposit_account_id';
             default : return null;
         }
    }

    private function updateStNonPay($set, $where){
         $this->db->set($set);
         $this->db->where($where);
         $this->db->update("coop_non_pay_detail");
    }

    private function updateStatusNonPay($non_pay_id){
        $non_pay_details = $this->db->select("sum(non_pay_amount_balance) as sum_balance")
            ->from("coop_non_pay_detail")
            ->where("non_pay_id = '" . $non_pay_id . "'")
            ->get()->row();

        $data_insert = array();
        $data_insert['non_pay_amount_balance'] = $non_pay_details->sum_balance;
        if ($non_pay_details->sum_balance <= 0) {
            $data_insert['non_pay_status'] = 2;
        }
        $this->db->where('non_pay_id', $non_pay_id);
        $this->db->update('coop_non_pay', $data_insert);
    }

    private function insertNonPayReceipt($non_pay_id, $receipt_number, $date){
        $data_insert = array();
        $data_insert['member_id'] = self::getNonPay($non_pay_id)->member_id;
        $data_insert['non_pay_id'] = $non_pay_id;
        $data_insert['receipt_id'] = $receipt_number;
        $data_insert['createdatetime'] = $date;
        $this->db->replace('coop_non_pay_receipt', $data_insert);
    }

    private function getNonPay($non_pay_id){
         if(!empty($this->_non_pay_data)){
             return $this->_non_pay_data;
         }
         return $this->_non_pay_data = $this->db->get_where("coop_non_pay",
             array("non_pay_id" => $non_pay_id))->row();
    }

}
