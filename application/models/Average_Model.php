<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Average_Model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ตั้งค่ารอบบัญชี
     * @return String
     */
    private function getIntiAccount(){
        $this->db->order_by('accm_id', 'desc');
        return $this->db->get('coop_account_period_setting', 1)->row_array()['accm_month_ini'];
    }

    private $_query = null;
    
    public function _query($year = 0){

        if($year == 0){
            $year = date('Y');
        }

        $month = self::getIntiAccount();
        if($year.sprintf("%02d", $month) <= date('Ym')){
            $year += 1;
        }
        $start_date = date('Y-m-d', strtotime("".($year-1)."-10-01"));
        $end_date = date('Y-m-d', strtotime("{$year}-09-30s"));

        $this->_query = "SELECT T.member_id ,
            T.loan_id , T.loan_atm_id 
            ,if(T.loan_id is null, 
                (SUM(T.return_amount) + coalesce(T7.interest_notpay, 0)), 
                (SUM(T.return_amount) - coalesce(T2.interest_arrear_bal, 0) - coalesce(T3.interest_calculate_arrears, 0) + coalesce(T4.interest_notpay, 0)- coalesce(T8.interest, 0))
            ) as return_amount 
            FROM (
                SELECT member_id,interest AS `return_amount`,payment_date AS `date`, loan_id,loan_atm_id
                FROM coop_finance_transaction 
                WHERE payment_date BETWEEN '{$start_date}' AND '{$end_date}' AND account_list_id IN ('15','31') AND interest<> 0 
                UNION ALL SELECT member_id,(return_principal-return_amount) AS return_amount,return_time AS `date`, loan_id ,loan_atm_id
                FROM coop_process_return WHERE return_time 
                BETWEEN '{$start_date}' AND '{$end_date}'
            ) T 
            LEFT JOIN (
                SELECT t1.loan_id,  
                sum(t1.interest_calculate_arrears) as interest_calculate_arrears, 
                sum(t1.interest_arrear_bal) as interest_arrear_bal 
                FROM coop_loan_transaction as t1 
                WHERE t1.transaction_datetime BETWEEN '{$start_date}  00:00:00' AND '{$end_date} 23:59:59' AND t1.loan_amount_balance < 0
                AND t1.loan_type_code IN ('PM') AND t1.loan_id IS NOT NULL 
                GROUP BY t1.loan_id
            ) as T2 ON T.loan_id = T2.loan_id 
            LEFT JOIN (
                SELECT t1.loan_id, 
                sum(t1.interest_calculate_arrears) as interest_calculate_arrears 
                FROM coop_loan_transaction as t1 
                WHERE t1.transaction_datetime BETWEEN '{$start_date}  00:00:00'  AND '{$end_date} 23:59:59' 
                AND t1.loan_type_code IN ('RC') AND t1.loan_id IS NOT NULL 
                GROUP BY t1.loan_id
            ) as T3 ON T.loan_id = T3.loan_id   
            LEFT JOIN (
                SELECT t1.loan_id, sum(t1.interest_notpay) as interest_notpay 
                FROM coop_loan_transaction as t1 
                WHERE t1.transaction_datetime BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59' 
                AND t1.loan_type_code IN ('MR', 'PM', 'PL') AND t1.loan_id IS NOT NULL 
                GROUP BY t1.loan_id) as T4 ON T.loan_id = T4.loan_id
            LEFT JOIN (
                SELECT t1.loan_atm_id, 
                sum(t1.interest_calculate_arrears) as interest_calculate_arrears, 
                sum(t1.interest_arrear_bal) as interest_arrear_bal 
                FROM coop_loan_atm_transaction as t1 
                WHERE t1.transaction_datetime BETWEEN '{$start_date}  00:00:00' AND '{$end_date} 23:59:59' AND t1.loan_amount_balance < 0
                AND t1.loan_type_code IN ('PM') AND t1.loan_atm_id IS NOT NULL 
                GROUP BY t1.loan_atm_id
            ) as T5 ON T.loan_atm_id = T5.loan_atm_id 
            LEFT JOIN (
                SELECT t1.loan_atm_id, 
                sum(t1.interest_calculate_arrears) as interest_calculate_arrears 
                FROM coop_loan_atm_transaction as t1 
                WHERE t1.transaction_datetime BETWEEN '{$start_date}  00:00:00'  AND '{$end_date} 23:59:59' 
                AND t1.loan_type_code IN ('RC', 'CL') AND t1.loan_atm_id IS NOT NULL 
                GROUP BY t1.loan_atm_id
            ) as T6 ON T.loan_atm_id = T6.loan_atm_id   
            LEFT JOIN (
                SELECT t1.loan_atm_id, sum(t1.interest_notpay) as interest_notpay 
                FROM coop_loan_atm_transaction as t1 
                WHERE t1.transaction_datetime BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59' 
                AND t1.loan_type_code IN ('MR') AND t1.loan_atm_id IS NOT NULL 
                GROUP BY t1.loan_atm_id) as T7 ON T.loan_atm_id = T7.loan_atm_id
            LEFT JOIN (
                SELECT t1.member_id,t2.loan_id, sum(t2.interest) as interest FROM (
                    SELECT member_id FROM coop_finance_transaction WHERE payment_date BETWEEN '{$start_date}' AND '{$end_date}'
                    GROUP BY member_id
                )as t1
                INNER JOIN (
                    SELECT t1.member_id, t1.loan_id
                    ,t1.interest as interest 
                    FROM coop_finance_transaction as t1
                    INNER JOIN coop_loan_transaction as t2 ON t1.receipt_id = t2.receipt_id AND t1.loan_id = t2.loan_id
                    WHERE t2.loan_type_code = 'RM' AND t1.loan_id IS NOT NULL
                    AND t1.payment_date BETWEEN '{$start_date}' AND '{$end_date}'  AND t2.interest_notpay > 0
                    GROUP BY member_id, loan_id,t2.receipt_id
                ) as t2 ON t1.member_id = t2.member_id
                GROUP BY member_id, loan_id
            ) as T8 ON T8.member_id = T.member_id AND T8.loan_id = T.loan_id
            LEFT JOIN (
                SELECT t1.member_id,t2.loan_atm_id, sum(t2.interest) as interest FROM (
                    SELECT member_id FROM coop_finance_transaction WHERE payment_date BETWEEN '{$start_date}' AND '{$end_date}'
                    GROUP BY member_id
                )as t1
                INNER JOIN (
                    SELECT t1.member_id, t1.loan_atm_id
                    ,t1.interest as interest 
                    FROM coop_finance_transaction as t1
                    INNER JOIN coop_loan_atm_transaction as t2 ON t1.receipt_id = t2.receipt_id AND t1.loan_atm_id = t2.loan_atm_id
                    WHERE t2.loan_type_code = 'RM' AND t1.loan_id IS NOT NULL
                    AND t1.payment_date BETWEEN '{$start_date}' AND '{$end_date}'  AND t2.interest_notpay > 0
                    GROUP BY member_id, loan_atm_id,t2.receipt_id
                ) as t2 ON t1.member_id = t2.member_id
                GROUP BY member_id, loan_atm_id
            ) as T9 ON T9.member_id = T.member_id AND T9.loan_atm_id = T.loan_atm_id
            GROUP BY member_id, loan_id, loan_atm_id";
        return $this;
    }

    public function getMember($member_id, $year = 0){
        self::_query($year);
        $this->_query = "SELECT tt.* FROM (".$this->_query.") tt WHERE member_id = '".sprintf("%06d",$member_id)."'";
        return $this;
    }
    
    public function get(){
        return $this->db->query($this->_query)->result_array();
    }
}