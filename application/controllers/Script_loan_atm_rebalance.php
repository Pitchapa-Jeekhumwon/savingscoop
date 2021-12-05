<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Script_loan_atm_rebalance extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function atm_detail_detail(){

        if(@$_GET['loan_atm_id'] != ''){
            if($_GET['loan_atm_id'] != 'all') {
                $this->db->where(array('loan_atm_id' => $_GET['loan_atm_id']));
            }
        }else{
            echo 'please enter loan_atm_id : ';
            exit;
        }

        //$this->db->where(array('loan_atm_status' => 0));
        $res =$this->db->get('coop_loan_atm')->result_array();

        //echo $this->db->last_query(); exit;

        foreach($res as $key => $val) {

            $last_trans = $this->atm_transaction_by_id($val['loan_atm_id']);

            $detail = $this->atm_detail_by_id($val['loan_atm_id']);

            if(sizeof($detail) == 0){
                continue;
            }

            $loan_balance = round($last_trans['loan_amount_balance'], 2);
            if($_GET['dev'] == 'data') {
                echo "balance : " . number_format($loan_balance, 2)."\n";
            }

            //$counter = sizeof($detail);
            $arr = array();
            $num = 0;
            foreach ($detail as $index => $item) {
                //--$counter;
                if ($loan_balance > 0) {

                    if ($loan_balance > $item['loan_amount'] && $item['loan_amount'] !=  0) {
                        $arr[$num]['loan_amount_balance'] =  round($item['loan_amount'] , 2);
                    }else{
                        $arr[$num]['loan_amount_balance'] = round($loan_balance , 2);
                        $loan_balance = 0;
                    }
                    $loan_balance = round($loan_balance - $item['loan_amount'], 2);
                    $arr[$num]['loan_status'] = 0;
                    $arr[$num]['loan_id'] = $item['loan_id'];
                    $num++;
                } else {
                    $arr[$num]['loan_id'] = $item['loan_id'];
                    $arr[$num]['loan_status'] = 1;
                    $arr[$num]['loan_amount_balance'] = 0;
                    $num++;
                }
            }

            $this->db->update_batch('coop_loan_atm_detail', $arr, 'loan_id');
            if($_GET['dev'] == 'data') {

                echo "<pre>";
                foreach ($detail as $x => $y){
                    echo implode(" ,", array_map(function($k, $v){
                        return sprintf("'%s'=>'%s'", $k, $v);
                    }, array_keys($y), $y))."\n";
                }
                echo "</pre>";
                exit;
            }
                $affected = $this->db->affected_rows();
            if($affected) {
                self::rebalance_atm_total($val['loan_atm_id']);
                echo "<pre>";
                echo "loan_atm_id {$val['loan_atm_id']} success affected rows : ".number_format($affected);
                echo "\n";
                echo "</pre>";
                //exit;
            }
        }

    }

    public function rebalance_atm_total($id){
        $sql = "UPDATE coop_loan_atm SET total_amount_balance =
                (total_amount_approve - (select sum(loan_amount_balance) from coop_loan_atm_detail where loan_atm_id='{$id}' and loan_status='0'))
                WHERE loan_atm_id='{$id}'  LIMIT 1";
        $this->db->query($sql);
    }

    public function loan_atm_balance(){
        ini_set("precision", 12);
        $this->db->where(array('loan_atm_status' => 1, 'activate_status' => 0));
        $res =$this->db->get('coop_loan_atm')->result_array();
        $num = 0;
        $data = [];
        foreach($res as $key => $val) {
            $last_trans = $this->atm_transaction_by_id($val['loan_atm_id']);
            $loan_balance = round($last_trans['loan_amount_balance'], 2);
            $atm_loan_balance =  round($val['total_amount_approve'] - $loan_balance, 2);
            //if($last_trans['loan_amount_balance'] == 0 && self::atm_account_lasted($res['member_id']) != $val['loan_atm_id']){
            //    $data[$num]['loan_atm_status'] = 4;
            //}
            $data[$num]['total_amount_balance'] = $atm_loan_balance;
            $data[$num]['loan_atm_id'] = $val['loan_atm_id'];
            $num++;
        }
        if(sizeof($data)){
            $this->db->update_batch('coop_loan_atm', $data, 'loan_atm_id');
        }else{
            echo "<pre>"; echo "empty"; echo "</pre>";
        }
    }

    private function atm_detail_by_id($id){
        $sql = "SELECT * FROM coop_loan_atm_detail WHERE loan_atm_id = ? ORDER BY loan_date DESC, loan_id DESC;";
        return $this->db->query($sql, $id)->result_array();
    }

    private function atm_transaction_by_id($id){
        $sql = "SELECT * FROM coop_loan_atm_transaction WHERE loan_atm_id = ? ORDER BY transaction_datetime DESC, loan_atm_transaction_id DESC LIMIT 1;";
        return $this->db->query($sql, $id)->row_array();
    }

    private function loan_transaction_by_id($id){
        $sql = "SELECT * FROM coop_loan_transaction WHERE loan_id = ? ORDER BY loan_transaction_id DESC LIMIT 1;";
        return $this->db->query($sql, $id)->row_array();
    }

    private function atm_account_lasted($member_id = ''){

        return $this->db->select('loan_atm_id')->order_by('loan_atm_id', 'desc')->get_where('coop_loan_atm', array(
            'member_id' => $member_id
        ), 1)->row()->loan_atm_id;
    }

    public function loan_balance(){
        ini_set("precision", 12);
        $this->db->where(array('loan_status' => 1));
        $res =$this->db->get('coop_loan')->result_array();
        $num = 0;
        $data = [];
        foreach($res as $key => $val) {
            $last_trans = $this->loan_transaction_by_id($val['id']);
            $loan_balance = round($last_trans['loan_amount_balance'], 2);

            $data[$num]['loan_amount_balance'] = $loan_balance;
            $data[$num]['date_last_interest'] = $last_trans['transaction_datetime'];
            $data[$num]['id'] = $val['loan_id'];
            $num++;
        }
        if(sizeof($data)){
            $this->db->update_batch('coop_loan', $data, 'id');
        }else{
            echo "<pre>"; echo "empty"; echo "</pre>";
        }
    }

}
