<?php


class Loan_close_fiscal_year_end extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ลงรายการปิดปีงบประมาณเงินกู้
     */
    public function close_loan_transaction(){
        $loanActive = self::findLoanActive();

    }

    /**
     * ลงรายการปิดปีงบประมาณเงินกู้
     */
    public function close_loan_atm_transaction(){
        $atmActive = self::findLoanATMActive();
    }

    private function findLoanActive(){
        return $this->db->get_where('coop_loan', array('loan_status' => '1'))->result_array();
    }

    private function findLoanATMActive(){
        return $this->db->get_where('coop_loan_atm', array('loan_status' => '1'))->result_array();
    }
}
