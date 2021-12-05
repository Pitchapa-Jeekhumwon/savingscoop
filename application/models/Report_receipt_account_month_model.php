<?php


class Report_receipt_account_month_model extends CI_Model
{
    public function report_receipt_account_month_model()
    {
        $return_year = @$_GET['year'];
        $mount_receipt = $_GET['month'];
        $return_yearks = $return_year-543;

        $where = "1=1  ";
        $data_get = $this->input->get();
        if($data_get['choose_receipt'] == '2'){
            $where .= !empty($data_get['member_id']) ? "AND t1.member_id = '{$data_get['member_id']}' " : "";
        }else if($data_get['choose_receipt'] == '3'){
            $where .= !empty($data_get['member_id_begin']) ? "AND t1.member_id BETWEEN '{$data_get['member_id_begin']}' AND '{$data_get['member_id_end']}' " : "";
        }else{
            // condition filter on choose receipt 1 By t.tawatsak
            if(!empty($data_get['department'])){
                $where .="AND (";
                $where .= !empty($data_get['department']) ? "t3.department='{$data_get['department']}' AND t4.department_old is null " : "";
                $where .= !empty($data_get['faction']) ? "AND t3.faction='{$data_get['faction']}' AND t4.faction_old is null " : "";
                $where .= !empty($data_get['level']) ? "AND t3.level='{$data_get['level']}' AND t4.level_old is null " : "";
                $where .=") OR ";
                $where .="(";
                $where .= !empty($data_get['department']) ? " t4.department_old ='{$data_get['department']}' AND t4.department <> '{$data_get['department']}' " : "";
                $where .= !empty($data_get['faction']) ? "AND t4.faction_old = '{$data_get['faction']}' AND t4.faction <> '{$data_get['faction']}' " : "";
                $where .= !empty($data_get['level']) ? "AND t4.level_old ='{$data_get['level']}' AND t4.level <> '{$data_get['level']}' " : "";
                $where .=")  ";
            }
            $end_limit = $data_get['page_number'] * 100;
            $start_limit = $end_limit - 100;

            !empty($start_limit)?$start_limit = $start_limit : $start_limit = 0;
            !empty($end_limit)?$end_limit = $end_limit : $end_limit = 100;

        }
        $this->db->select('accm_month_ini');
        $this->db->from("coop_account_period_setting");
        $row_period_setting = $this->db->get()->result_array();
        $data_arr['row_period_setting'] = $row_period_setting;
        $years = $return_yearks;
        $mount_end = sprintf("%02d",$data_arr['row_period_setting'][0]['accm_month_ini']);
        if ($mount_receipt < $mount_end){
            $years_old = $years-1;
        }else{
            $years_old = $years;
            $years++;
        }
        $mount_old = $mount_end;
        $mount_old = sprintf("%02d",$mount_old);
        $mount_end = $mount_end-1;
        if($mount_end < 1){
            $mount_end = 12;
        }
        $mount_end = sprintf("%02d",$mount_end);
        $date_end  = cal_days_in_month(CAL_GREGORIAN, $mount_end, $years);

        $between_date_start = "".$years_old.'-'.$mount_old."-01";
        $between_date_end = "".$years.'-'.$mount_end."-".$date_end;
        $this->db->select(array("t1.receipt_id", "t3.member_id", "t1.receipt_datetime", "t1.pay_type", "t4.date_move", "t5.prename_short",
            "t3.firstname_th", "t3.lastname_th", "t6.mem_group_name", "t7.sum_interest_year",
            "CONCAT('[',t2.loan_id,']') as loan_id",
            "CONCAT('[',t2.period_count,']') as period_count",
            "CONCAT('[',t2.account_list_id,']') as account_list_id",
            "CONCAT('[',t2.account_list,']') as account_list",
            "CONCAT('[',t2.contract_number,']') as contract_number",
            "CONCAT('[',t2.principal_payment,']') as principal_payment",
            "CONCAT('[',t2.interest,']') as interest",
            "CONCAT('[',t2.loan_amount_balance,']') as loan_amount_balance",
            "CONCAT('[',t2.transaction_text,']') as transaction_text"));
        $this->db->from("coop_finance_month_detail as t0");
        $this->db->join("coop_receipt as t1", "t1.member_id = t0.member_id AND t1.year_receipt = ".$return_year." AND t1.month_receipt = ".$mount_receipt, "inner");
        $this->db->join("(SELECT `receipt_id`,
            GROUP_CONCAT(IFNULL(CONCAT('\"',loan_id,'\"'), '\"-\"') ORDER BY t2.finance_transaction_id ASC) as loan_id, 
            GROUP_CONCAT(IFNULL(CONCAT('\"',period_count,'\"'), '\"\"') ORDER BY t2.finance_transaction_id ASC) as period_count,
            GROUP_CONCAT(IFNULL(CONCAT('\"',account_list_id,'\"'), '\"\"') ORDER BY t2.finance_transaction_id ASC) as account_list_id,
            GROUP_CONCAT(IFNULL(CONCAT('\"',t4.account_list,'\"') , '\"\"') ORDER BY t2.finance_transaction_id ASC) as account_list ,
            GROUP_CONCAT(IFNULL(CONCAT('\"',t3.contract_number,'\"') , '\"\"') ORDER BY t2.finance_transaction_id ASC) as contract_number ,
            GROUP_CONCAT(IFNULL(CONCAT('\"',principal_payment,'\"') , '\"\"') ORDER BY t2.finance_transaction_id ASC) as principal_payment ,
            GROUP_CONCAT(IFNULL(CONCAT('\"',interest,'\"') , '\"\"') ORDER BY t2.finance_transaction_id ASC) as interest ,
            GROUP_CONCAT(IFNULL(CONCAT('\"',t2.transaction_text,'\"') , '\"\"') ORDER BY t2.finance_transaction_id ASC) as transaction_text,
            GROUP_CONCAT(IFNULL(CONCAT('\"',t2.loan_amount_balance,'\"') , '\"0\"') ORDER BY t2.finance_transaction_id ASC) as loan_amount_balance 
        FROM `coop_finance_transaction` as t2
        LEFT JOIN `coop_loan` as `t3` ON `t2`.`loan_id` = `t3`.`id`
        LEFT JOIN `coop_account_list` as `t4` ON `t2`.`account_list_id` = `t4`.`account_id`
        GROUP BY `receipt_id`) as t2", "t1.receipt_id = t2.receipt_id", "inner");
        $this->db->join("coop_mem_apply as t3", "t1.member_id = t3.member_id", "left");
        $this->db->join("coop_mem_group_move as t4", "t3.member_id = t4.member_id AND t4.date_move >= t1.receipt_datetime", "left");
        $this->db->join("coop_prename as t5", "t3.prename_id = t5.prename_id", "left");
        $this->db->join("coop_mem_group as t6", "t3.level = t6.id", "left");
        $this->db->join("(SELECT receipt_id, sum(interest) as sum_interest_year FROM coop_finance_transaction WHERE payment_date BETWEEN '".$between_date_start."' AND '".$between_date_end."' GROUP BY receipt_id) as t7", "t1.receipt_id = t7.receipt_id", "left");

        $this->db->where("t1.receipt_id NOT LIKE '%C%' AND (t1.receipt_status <> 2 OR t1.receipt_status is NULL)");
        $this->db->where($where);
        $this->db->group_by('t1.receipt_id');
        $this->db->order_by('t1.member_id', 'ASC');
       $this->db->limit($end_limit,$start_limit);
        $get_data = $this->db->get()->result_array();
//        echo '<pre>';print_r($get_data);exit;
        foreach ($get_data as $key => $value){
            $loan_id_decode = json_decode($value['loan_id'], TRUE);
            $period_count_decode = json_decode($value['period_count'], TRUE);
            $account_list_id_decode = json_decode($value['account_list_id'], TRUE);
            $account_list_decode = json_decode($value['account_list'], TRUE);
            $contract_number_decode = json_decode($value['contract_number'], TRUE);
            $principal_payment_decode = json_decode($value['principal_payment'], TRUE);
            $interest_decode = json_decode($value['interest'], TRUE);
            $loan_amount_balance_decode = json_decode($value['loan_amount_balance'], TRUE);
            $transaction_text_decode = json_decode($value['transaction_text'], TRUE);

//            echo '<pre>';print_r($value['account_list']);exit;
            if(!empty($loan_id_decode)) {
                $new_array = array();
                foreach (@$loan_id_decode as $json_key => $json_value){
                    if($json_value != '-'&&$json_value != '' ){
                        if (array_key_exists($json_value,$new_array)){
                            $new_array['loan'][$json_value]['principal_payment'] += $principal_payment_decode[$json_key];
                            $new_array['loan'][$json_value]['interest'] += $interest_decode[$json_key];
                            $new_array['loan'][$json_value]['loan_amount_balance'] = $loan_amount_balance_decode[$json_key];
                        }else{
                            $new_array['loan'][$json_value]['loan_id'] = $json_value;
                            $new_array['loan'][$json_value]['period_count'] = $period_count_decode[$json_key];
                            $new_array['loan'][$json_value]['account_list_id'] = $account_list_id_decode[$json_key];
                            $new_array['loan'][$json_value]['account_list'] = $account_list_decode[$json_key];
                            $new_array['loan'][$json_value]['contract_number'] = $contract_number_decode[$json_key];
                            $new_array['loan'][$json_value]['principal_payment'] += $principal_payment_decode[$json_key];
                            $new_array['loan'][$json_value]['interest'] += $interest_decode[$json_key];
                            $new_array['loan'][$json_value]['loan_amount_balance'] = $loan_amount_balance_decode[$json_key];
                            $new_array['loan'][$json_value]['transaction_text'] = $transaction_text_decode[$json_key];
                        }
                    }else{
                        $new_array['other'][$json_key]['loan_id'] = $json_value;
                        $new_array['other'][$json_key]['period_count'] = $period_count_decode[$json_key];
                        $new_array['other'][$json_key]['account_list_id'] = $account_list_id_decode[$json_key];
                        $new_array['other'][$json_key]['account_list'] = $account_list_decode[$json_key];
                        $new_array['other'][$json_key]['contract_number'] = $contract_number_decode[$json_key];
                        $new_array['other'][$json_key]['principal_payment'] = $principal_payment_decode[$json_key];
                        $new_array['other'][$json_key]['interest'] = $interest_decode[$json_key];
                        $new_array['other'][$json_key]['loan_amount_balance'] = $loan_amount_balance_decode[$json_key];
                        $new_array['other'][$json_key]['transaction_text'] = $transaction_text_decode[$json_key];
                    }
                }
            }
//            echo '<pre>';print_r($new_array);exit;
            $get_data[$key]['finance_month_detail'] = $new_array;
            $get_data[$key] = \array_diff_key($get_data[$key], ['loan_id' => "xy"]); // ลบ loan_id
            $get_data[$key] = \array_diff_key($get_data[$key], ['period_count' => "xy"]);
            $get_data[$key] = \array_diff_key($get_data[$key], ['account_list_id' => "xy"]);
            $get_data[$key] = \array_diff_key($get_data[$key], ['account_list' => "xy"]);
            $get_data[$key] = \array_diff_key($get_data[$key], ['contract_number' => "xy"]);
            $get_data[$key] = \array_diff_key($get_data[$key], ['principal_payment' => "xy"]);
            $get_data[$key] = \array_diff_key($get_data[$key], ['interest' => "xy"]);
            $get_data[$key] = \array_diff_key($get_data[$key], ['loan_amount_balance' => "xy"]);
        }
//        echo  $this->db->last_query();
//        echo '<pre>';print_r($get_data);exit;
        return $get_data;
    }


}