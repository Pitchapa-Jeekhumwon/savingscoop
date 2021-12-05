<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invest_libraies extends CI_Model {
    public function __construct() {
        parent::__construct();

    }

    public function get_total_data() {
        $result = array();
        $invest_payment = $this->db->select("SUM(amount) as sum")->from("coop_invest")->where("status = 1")->get()->row_array();
        $result['invest_payment'] = !empty($invest_payment) ? $invest_payment['sum'] : 0;
        $result['invest_payment_format'] = number_format($result['invest_payment'], 2);

        $profit = $this->db->select("SUM(amount) as sum")->from("coop_invest_profit_transaction")->where("status = 1 AND date LIKE '".date('Y')."%'")->get()->row_array();
        $result['profit'] = !empty($profit) ?  $profit['sum'] : 0;
        $result['profit_format'] = number_format($result['profit'], 2);

        return $result;
    }

    /*
        for type = 1.
        id: if empty create / if not empty update
        amount: number 2 decimals
        due_date: dd/mm/yyyy
        interest: text
        name: text
        period: text
        start_date: dd/mm/yyyy
        source: text
        status default = 1.
    */
    public function edit_saving_coop($org_id, $id, $name, $amount, $due_date, $interest, $period, $start_date, $source, $status) {
        //Check current data first.
        $invest = $this->db->select("*")->from("coop_invest")->where("id = '".$id."'")->get()->row_array();
        if(!empty($invest)) {
            if($status == 2 && $invest['status'] == 1) {
                $amount = 0;
            }
            if($status == 1 && $invest['status'] == 2) {
                $balance_history = $this->db->select("amount")->from("coop_invest_balance_history")->where("invest_id = '".$id."' AND amount != 0")->order_by("created_at DESC")->get()->row_array();
                $amount = $balance_history['amount'];
            }
        }

        $process_timestamp = date('Y-m-d H:i:s');
        $data_insert = array();
        $data_insert['org_id'] = $org_id;
        $data_insert['name'] = $name;
        $data_insert['amount'] = $amount;
        $data_insert['status'] = !empty($status) ? $status : 1;
        $data_insert['start_date'] = $start_date;
        $data_insert['type'] = 1;
        $data_insert['source'] = $source;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        if(!empty($id)) {
            $data_insert['update_date'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->where('id', $id);
            $this->db->update('coop_invest', $data_insert);
        } else {
            $data_insert['update_date'] = $process_timestamp;
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_invest', $data_insert);
            $id = $this->db->insert_id();
        }

        //Update balance history if new amonut difference from lastest amount.
        $balance_history = $this->db->select("amount")->from("coop_invest_balance_history")->where("invest_id = '".$id."'")->order_by("created_at DESC")->get()->row_array();
        if(empty($balance_history) || $balance_history['amount'] != $amount) {
            $data_insert = array();
            $data_insert['invest_id'] = $id;
            $data_insert['amount'] = $amount;
            $this->db->insert('coop_invest_balance_history', $data_insert);
            $invest_balance_history_id = $this->db->insert_id();

            if($invest['gen_account_data'] == 1) {
                //Generate account data.
                if(empty($balance_history) || $balance_history['amount'] < $amount) {
                    $diff_amount = empty($balance_history) ? $amount : $amount - $balance_history['amount'];
                    $process = "invest_increase";
                } else {
                    $diff_amount = $balance_history['amount'] - $amount;
                    $process = "invest_decrease";
                }

                $budget_year_be = $this->account_transaction->get_budget_year($process_timestamp);

                $matchs = $this->db->select('t1.description, t2.chart_id, t2.type, t3.account_chart')
                                    ->from("coop_invest_account as t1")
                                    ->join("coop_invest_account_match as t2", "t1.id = t2.invest_account_id", "INNER")
                                    ->join("coop_account_chart as t3", "t2.chart_id = t3.account_chart_id", "LEFT")
                                    ->where("t1.invest_id = '".$id."' AND t1.process = '".$process."' AND t1.status = 1")
                                    ->get()->result_array();

                $journal_ref = "JV".(date('Y') + 543 - 2500).sprintf('%02d',date("m"));
                $last_journal_ref_account = $this->db->select("journal_ref")->from("coop_account")->where("journal_ref LIKE '%".$journal_ref."%'")->order_by("journal_ref desc")->get()->row();
                if(empty($last_journal_ref_account)) {
                    $journal_ref .= sprintf('%02d',date("d"))."001";
                } else {
                    $last_journal_ref = $last_journal_ref_account->journal_ref;
                    $journal_ref .= !empty($last_journal_ref) ? sprintf('%02d',date("d")).(sprintf('%03d', substr($last_journal_ref, -3) + 1)): sprintf('%02d',date("d"))."001";
                }
                $description = $matchs[0]['description'];

                $data_insert = array();
                $data_insert['account_description'] = $description;
                $data_insert['account_datetime'] = date('Y-m-d');
                $data_insert['account_status'] = 0;
                $data_insert['ref_id'] = $invest_balance_history_id;
                $data_insert['ref_type'] = "coop_invest_balance_history.id";
                $data_insert['process'] = "invest_1_".$process;
                $data_insert['journal_ref'] = $journal_ref;
                $data_insert['journal_type'] = "JV";
                $data_insert['user_id'] = $_SESSION['USER_ID'];
                $data_insert['budget_year'] = $budget_year_be;
                $data_insert['created_at'] = $process_timestamp;
                $data_insert['updated_at'] = $process_timestamp;
                $this->db->insert('coop_account', $data_insert);
                $account_id = $this->db->insert_id();

                $data_inserts = array();
                $seq_no = 1;
                foreach($matchs as $match) {
                    $data_insert = array();
                    $data_insert['account_id'] = $account_id;
                    $data_insert['account_type'] = $match['type'];
                    $data_insert['account_chart_id'] = $match['chart_id'];
                    $data_insert['account_amount'] = $diff_amount;
                    $data_insert['description'] = $match['account_chart'];
                    $data_insert['seq_no'] = $seq_no++;
                    $data_insert['created_at'] = $process_timestamp;
                    $data_insert['updated_at'] = $process_timestamp;
                    $data_inserts[] = $data_insert;
                    $this->account_transaction->increase_decrease_budget_year($match['chart_id'], $diff_amount, $match['type'], $budget_year_be, 1);
                }

                if (!empty($data_inserts)) {
                    $this->db->insert_batch('coop_account_detail', $data_inserts);
                }
            }
        }

        $detail = $this->db->select("id")->from("coop_invest_detail")->where("invest_id = '".$id."' AND status = 1")->get()->row_array();

        $data_insert = array();
        $data_insert['invest_id'] = $id;
        $data_insert['invest_rate_text'] = $interest;
        $data_insert['invest_date'] = $start_date;
        $data_insert['end_date'] = $due_date;
        $data_insert['payment_method_text'] = $period;
        $data_insert['status'] = 1;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        if(!empty($detail)) {
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->where('id', $detail['id']);
            $this->db->update('coop_invest_detail', $data_insert);
        } else {
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_invest_detail', $data_insert);
            $detail_id = $this->db->insert_id();
        }

        return $id;
    }

    public function delete($id) {
        $data_update = array();
        $data_update['status'] = 3;
        $this->db->where('id', $id);
        $this->db->update('coop_invest', $data_update);
        return 'success';
    }

    public function get_invest_data($id, $status, $detail_status, $profit_status, $tran_status) {
        $result = array();
        
        $where = "1=1";
        $where_detail = "1=1";
        $where_profit = "1=1";
        $where_prev_profit = "1=1";
        $where_transaction = "1=1";
        $where_prev_transaction = "1=1";

        if(!empty($status)) {
            $where = "t1.status = '".$status."'";
        }
        if(!empty($detail_status)) {
            $where_detail = "status = '".$detail_status."'";
        }
        if(!empty($profit_status)) {
            $where_profit = "status = '".$profit_status."'";
        }
        if(!empty($tran_status)) {
            $where_transaction = "status = '".$tran_status."'";
        }
        if(!empty($id)) {
            $where .= " AND t1.id = '".$id."'";
            $where_detail .= " AND invest_id = '".$id."'";
            $where_profit .= " AND invest_id = '".$id."'";
            $where_prev_profit .= " AND invest_id = '".$id."'";
            $where_transaction .= " AND invest_id = '".$id."'";
            $where_prev_transaction .= " AND invest_id = '".$id."'";
        }

        //transaction n profit show only last 5 years.
        $where_profit .= " AND date >= '".(((INT)date('Y'))-5)."-01-01' AND date <= '".date('Y')."-12-31'";
        $where_prev_profit .= " AND status = 1 AND date < '".(((INT)date('Y'))-5)."-01-01'";
        $where_transaction .= " AND date >= '".(((INT)date('Y'))-5)."-01-01' AND date <= '".date('Y')."-12-31'";
        $where_prev_transaction .= " AND status = 1 AND date < '".(((INT)date('Y'))-5)."-01-01'";

        $invest = $this->db->select("t1.*, t2.name as org_name")
                            ->from("coop_invest as t1")
                            ->join("coop_invest_organization as t2", "t1.org_id = t2.id", "left")
                            ->where($where)
                            ->get()->row_array();
        $result = $invest;
        $result['amount_format'] = number_format($invest['amount'],2);
        $result['update_date_thai'] = $this->center_function->ConvertToThaiDate($invest['update_date'],'1','0');

        $detail = $this->db->select("*")->from("coop_invest_detail")->where($where_detail)->get()->row_array();

        if(!empty($detail)) {
            $detail['value_per_unit_format'] = number_format($detail['value_per_unit'],2);
            $detail['aver_profit_format'] = number_format($detail['aver_profit'],2);
            $detail['unit_format'] = number_format($detail['unit']);
            if(!empty($detail['invest_date']) && !empty($detail['end_date'])) {
                $date1 = new DateTime($detail['invest_date']);
			    $date2 = new DateTime($detail['end_date']);
                $interval = date_diff($date1, $date2);
                $detail['invest_interval'] = $interval;
                $detail['start_date_thai'] = $this->center_function->ConvertToThaiDate($detail['invest_date'],'1','0');
                $detail['start_date_calender'] = $this->center_function->mydate2date($detail['invest_date']);

                $detail['end_date_thai'] = $this->center_function->ConvertToThaiDate($detail['end_date'],'1','0');
                $detail['end_date_calender'] = $this->center_function->mydate2date($detail['end_date']);

            }
            if(!empty($detail['end_date'])) {
                if(strtotime($detail['end_date']) >=  strtotime("now")) {
                    $date1 = new DateTime(date("Y-m-d"));
                    $date2 = new DateTime($detail['end_date']);
                    $interval = date_diff($date1, $date2);
                    $detail['invest_interval_left'] = $interval;
                }
            }
        }
        $result['detail'] = $detail;

        //Get perv profit for chart.
        $profits = array();
        $profit_perv_raw = $this->db->select("id, rate, note, amount, date")->from("coop_invest_profit_transaction")->where($where_prev_profit)->order_by("date ASC")->get()->result_array();
        $profit_perv_sum = 0;
        $profit_perv_lastest = 0;
        foreach($profit_perv_raw as $profit) {
            $profit['amount_format'] = number_format($profit['amount'],2);
            $profit['last_five_years'] = 0;
            $profit['date_format'] = $this->center_function->ConvertToThaiDate($profit['date'],'1','0');
            $profits[] = $profit;
            $profit_perv_sum += $profit['amount'];
            $profit_perv_lastest = $profit['amount'];
        }
        $result['profit_perv_sum'] = $profit_perv_sum;
        $result['profit_perv_lastest'] = $profit_perv_lastest;

        $profit_raw = $this->db->select("id, rate, note, amount, date")->from("coop_invest_profit_transaction")->where($where_profit)->order_by("date ASC")->get()->result_array();
        $sum_profit = 0;
        foreach($profit_raw as $profit) {
            $profit['amount_format'] = number_format($profit['amount'],2);
            $profit['last_five_years'] = 1;
            $profit['date_format'] = $this->center_function->ConvertToThaiDate($profit['date'],'1','0');
            $profits[] = $profit;
            $sum_profit += $profit['amount'];
        }
        $result['sum_profit'] = $sum_profit;
        $result['sum_profit_format'] = number_format($sum_profit,2);
        $result['total_balance'] = $sum_profit + $invest['amount'];
        $result['total_balance_format'] = number_format($result['total_balance'] ,2);
        $result['total_profit'] = $profit_perv_sum + $sum_profit;
        $result['total_profit_format'] = number_format($result['total_profit'], 2);
        $result['profits'] = $profits;

        $transaction_raw = $this->db->select("*")->from("coop_invest_transaction")->where($where_transaction)->order_by('date ASC')->get()->result_array();
        $transactions = array();
        $total_transaction_unit = 0;
        $total_transaction_payment = 0;
        $last_value_per_unit = 0;
        $total_buy_unit = 0;
        $total_buy_payment = 0;
        foreach($transaction_raw as $transaction) {
            $transaction['amount_format'] = number_format($transaction['amount'], 2);
            $transaction['fee_format'] = number_format($transaction['fee'], 2);
            $transaction['unit_format'] = number_format($transaction['unit']);
            $transaction['value_per_unit_format'] = number_format($transaction['value_per_unit'], 2);
            $transaction['payment'] = $transaction['amount'] * $transaction['value_per_unit'];
            $transaction['payment_format'] = number_format($transaction['payment'], 2);
            $transaction['date_format'] = $this->center_function->ConvertToThaiDate($transaction['date'],'1','0');
            $transactions[] = $transaction;
            if($transaction['tran_type'] == 1) {
                $total_transaction_unit += $transaction['unit'];
                $total_transaction_payment += $transaction['amount'];
                $total_buy_unit += $transaction['unit'];
                $total_buy_payment += $transaction['amount'];
            } else {
                $total_transaction_unit -= $transaction['unit'];
                $total_transaction_payment -= $transaction['amount'];
            }

            $last_value_per_unit = $transaction['value_per_unit'];
        }
        $result['average_share_value'] = !empty($total_buy_unit) ? $total_buy_payment/$total_buy_unit : 0;
        $result['transactions'] = $transactions;

        $result['tran_fif_total_payment'] = $total_transaction_payment;
        $result['tran_fif_total_payment_format'] = number_format($result['tran_fif_total_payment'],2);
        $result['tran_fif_total_unit'] = $total_transaction_unit;
        $result['tran_fif_total_unit_format'] = number_format($total_transaction_unit);
        $result['tran_fif_total_amount'] = $result['tran_fif_total_unit'] * $last_value_per_unit;
        $result['tran_fif_total_amount_format'] = number_format($result['tran_fif_total_amount'], 2);
        $result['tran_fif_total_amount_n_profit'] = $result['tran_fif_total_amount'] + $result['total_fif_profit'];
        $result['tran_fif_total_amount_n_profit_format'] = number_format($result['tran_fif_total_amount_n_profit'],2);

        $tran_prev_unit = 0;
        $tran_prev_payment = 0;
        $tran_prev_raw = $this->db->select("*")->from("coop_invest_transaction")->where($where_prev_transaction)->get()->result_array();
        foreach($tran_prev_raw as $tran_prev) {
            if($transaction['tran_type'] == 1) {
                $tran_prev_unit += $tran_prev['unit'];
                $tran_prev_payment += $tran_prev['amount'];
            } else {
                $tran_prev_unit += $tran_prev['unit'];
                $tran_prev_payment += $tran_prev['amount'];
            }
        }
        $result['tran_prev_unit'] = $tran_prev_unit;
        $result['tran_prev_unit_format'] = number_format($tran_prev_unit,2);
        $result['tran_prev_payment'] = $tran_prev_payment;
        $result['tran_prev_payment_format'] = number_format($tran_prev_payment,2);
        $result['tran_total_payment'] = $tran_prev_payment + $total_transaction_payment;
        $result['tran_total_payment_format'] = number_format($result['tran_total_payment'],2);
        $result['tran_total_unit'] = $tran_prev_unit + $total_transaction_unit;
        $result['tran_total_amount'] = $result['tran_total_unit'] * $last_value_per_unit;
        $result['tran_total_amount_format'] = number_format($result['tran_total_amount'], 2);
        $result['tran_total_amount_n_profit'] = $result['tran_total_amount'] + $result['total_profit'];
        $result['tran_total_amount_n_profit_format'] = number_format($result['tran_total_amount_n_profit'],2);
        
        return $result;
    }

    public function add_profit_transaction($id, $invest_id, $date, $rate, $amount, $note) {
        $result = array();
        $invest = $this->db->select("*")->from("coop_invest")->where("id = '".$invest_id."'")->get()->row_array();
        $process_timestamp = date('Y-m-d H:i:s');

        $result['invest_id'] = $invest_id;

        $data_insert = array();
        $data_insert['invest_id'] = $invest_id;
        $data_insert['date'] = $date;
        $data_insert['rate'] = $rate;
        $data_insert['amount'] = $amount;
        $data_insert['type'] = $invest['type'];
        $data_insert['note'] = $note;
        $data_insert['status'] = 1;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        $data_insert['updated_at'] = $process_timestamp;

        if(empty($id)) {
            $data_insert['created_at'] = $process_timestamp;
            $this->db->insert('coop_invest_profit_transaction', $data_insert);
            $id = $this->db->insert_id();

            $account_amount = $amount;
            $process = $invest['type'] == 2 || $invest['type'] == 5 ? "dividend" : "interest";
        } else {
            $prev_transaction = $this->db->select("*")->from("coop_invest_profit_transaction")->where("id = '".$id."'")->get()->row_array();
            if($prev_transaction['amount'] > $amount) {
                $account_amount = $prev_transaction['amount'] - $amount;
                $process = $invest['type'] == 2 || $invest['type'] == 5 ? "dividend_decrease" : "interest_decrease";
            } else if ($prev_transaction['amount'] < $amount) {
                $account_amount = $amount - $prev_transaction['amount'];
                $process = $invest['type'] == 2 || $invest['type'] == 5 ? "dividend" : "interest";
            }
            $this->db->where('id', $id);
            $this->db->update('coop_invest_profit_transaction', $data_insert);
        }

        if($invest['gen_account_data'] == 1) {
            $budget_year_be = $this->account_transaction->get_budget_year($process_timestamp);

            $matchs = $this->db->select('t1.description, t2.chart_id, t2.type, t3.account_chart')
                                ->from("coop_invest_account as t1")
                                ->join("coop_invest_account_match as t2", "t1.id = t2.invest_account_id", "INNER")
                                ->join("coop_account_chart as t3", "t2.chart_id = t3.account_chart_id", "LEFT")
                                ->where("t1.invest_id = '".$invest_id."' AND t1.process = '".$process."' AND t1.status = 1")
                                ->get()->result_array();

            $journal_ref = "JV".(date('Y') + 543 - 2500).sprintf('%02d',date("m"));
            $last_journal_ref_account = $this->db->select("journal_ref")->from("coop_account")->where("journal_ref LIKE '%".$journal_ref."%'")->order_by("journal_ref desc")->get()->row();
            if(empty($last_journal_ref_account)) {
                $journal_ref .= sprintf('%02d',date("d"))."001";
            } else {
                $last_journal_ref = $last_journal_ref_account->journal_ref;
                $journal_ref .= !empty($last_journal_ref) ? sprintf('%02d',date("d")).(sprintf('%03d', substr($last_journal_ref, -3) + 1)): sprintf('%02d',date("d"))."001";
            }
            $description = $matchs[0]['description'];

            $data_insert = array();
            $data_insert['account_description'] = $description;
            $data_insert['account_datetime'] = date('Y-m-d');
            $data_insert['account_status'] = 0;
            $data_insert['ref_id'] = $id;
            $data_insert['ref_type'] = "coop_invest_profit_transaction.id";
            $data_insert['process'] = "invest_".$invest['type']."_".$process;
            $data_insert['journal_ref'] = $journal_ref;
            $data_insert['journal_type'] = "JV";
            $data_insert['user_id'] = $_SESSION['USER_ID'];
            $data_insert['budget_year'] = $budget_year_be;
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_account', $data_insert);
            $account_id = $this->db->insert_id();

            $data_inserts = array();
            $seq_no = 1;
            foreach($matchs as $match) {
                $data_insert = array();
                $data_insert['account_id'] = $account_id;
                $data_insert['account_type'] = $match['type'];
                $data_insert['account_chart_id'] = $match['chart_id'];
                $data_insert['account_amount'] = $account_amount;
                $data_insert['description'] = $match['account_chart'];
                $data_insert['seq_no'] = $seq_no++;
                $data_insert['created_at'] = $process_timestamp;
                $data_insert['updated_at'] = $process_timestamp;
                $data_inserts[] = $data_insert;
                $this->account_transaction->increase_decrease_budget_year($match['chart_id'], $account_amount, $match['type'], $budget_year_be, 1);
            }

            if (!empty($data_inserts)) {
                $this->db->insert_batch('coop_account_detail', $data_inserts);
            }
        }

        $result['id'] = $id;

        $data_insert = array();
        $data_insert['update_date'] = $process_timestamp;
        $data_insert['updated_at'] = $process_timestamp;
        $this->db->where('id', $invest_id);
        $this->db->update('coop_invest', $data_insert);

        return $result;
    }

    public function get_profit_transaction_by_id($id) {
        $where = "1=1";

        if(!empty($id)) { $where .= " AND id = '".$id."'";}

        $transaction = $this->db->select("*")->from("coop_invest_profit_transaction")->where($where)->get()->row_array();
        $transaction['amount_format'] = number_format($transaction['amount'],2);
        $transaction['rate_format'] = number_format($transaction['rate']);
        $transaction['date_format'] = $this->center_function->ConvertToThaiDate($transaction['date'],'1','0');
        $transaction['date_calender'] = $this->center_function->mydate2date($transaction['date']);

        return $transaction;
    }

    /*
        status default : 1.
    */
    public function edit_share_coop($org_id, $id, $name, $period, $source, $status) {
        $process_timestamp = date('Y-m-d H:i:s');

        //Check current data first.
        $amount = NULL;
        $invest = $this->db->select("*")->from("coop_invest")->where("id = '".$id."'")->get()->row_array();
        if(!empty($invest)) {
            if($status == 2 && $invest['status'] == 1) {
                $amount = 0;
            }
            if($status == 1 && $invest['status'] == 2) {
                $balance_history = $this->db->select("amount")->from("coop_invest_balance_history")->where("invest_id = '".$id."'")->order_by("created_at DESC")->get()->row_array();
                $amount = $balance_history['amount'];
            }
        }

        $data_insert = array();
        $data_insert['org_id'] = $org_id;
        $data_insert['name'] = $name;
        $data_insert['amount'] = $amount;
        $data_insert['status'] = !empty($status) ? $status : 1;
        $data_insert['start_date'] = $process_timestamp;
        $data_insert['type'] = 2;
        $data_insert['source'] = $source;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        if(!empty($id)) {
            $data_insert['update_date'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->where('id', $id);
            $this->db->update('coop_invest', $data_insert);
        } else {
            $data_insert['update_date'] = $process_timestamp;
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_invest', $data_insert);
            $id = $this->db->insert_id();
        }

        //Update balance history if new amonut difference from lastest amount.
        $balance_history = $this->db->select("amount")->from("coop_invest_balance_history")->where("invest_id = '".$id."'")->order_by("created_at DESC")->get()->row_array();
        if((empty($balance_history) || $balance_history['amount'] != $amount) && $amount != NULL) {
            $data_insert = array();
            $data_insert['invest_id'] = $id;
            $data_insert['amount'] = $amount;
            $this->db->insert('coop_invest_balance_history', $data_insert);
        }

        $detail = $this->db->select("id")->from("coop_invest_detail")->where("invest_id = '".$id."' AND status = 1")->get()->row_array();

        $data_insert = array();
        $data_insert['invest_id'] = $id;
        $data_insert['payment_method_text'] = $period;
        $data_insert['status'] = 1;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        if(!empty($detail)) {
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->where('id', $detail['id']);
            $this->db->update('coop_invest_detail', $data_insert);
        } else {
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_invest_detail', $data_insert);
            $detail_id = $this->db->insert_id();
        }

        return $id;
    }

    /*
    $tran_type 1 = ซื้อ/ 2 = ขาย
    */
    public function add_transaction($id, $invest_id, $date, $unit, $rate, $amount, $note, $tran_type, $fee,$tax) {
        $result = array();
        $process_timestamp = date('Y-m-d H:i:s');
        $account_datas = array();

        $invest = $this->db->select("*")->from("coop_invest")->where("id = '".$invest_id."'")->get()->row_array();
        $transaction_cur = $this->db->select("*")->from("coop_invest_transaction")->where("id = '".$id."'")->get()->row_array();

        $average_share_value = 0;
        $profit = 0;
        if($tran_type == 2) {
            $prev_transactions = $this->db->select("SUM(unit) as unit, SUM(amount) as amount, tran_type")->from("coop_invest_transaction")->where("invest_id = '".$invest_id."' AND status = 1 AND tran_type = 1")->group_by("tran_type")->get()->row_array();
            if(!empty($prev_transactions)) {
                $average_share_value = $prev_transactions['amount']/$prev_transactions['unit'];
            }
            $profit = $amount - ($unit * $average_share_value);
        }

        if(empty($rate)) {
            $rate = ($amount + $fee) / $unit;
        }

        $data_insert = array();
        $data_insert['invest_id'] = $invest_id;
        $data_insert['date'] = $date;
        $data_insert['amount'] = $amount;
        $data_insert['value_per_unit'] = $rate;
        $data_insert['unit'] = $unit;
        $data_insert['fee'] = $fee;
        $data_insert['tax'] = $tax;
        $data_insert['profit'] = $profit;
        $data_insert['note'] = $note;
        $data_insert['status'] = 1;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        $data_insert['type'] = $invest['type'];
        $data_insert['tran_type'] = $tran_type;
        if(!empty($id)) {
            $data_del = array();
            $data_del['status'] = 3;
            $data_del['updated_at'] = $process_timestamp;
            $this->db->where('id', $id);
            $this->db->update('coop_invest_transaction', $data_del);
        }
        $data_insert['created_at'] = $process_timestamp;
        $data_insert['updated_at'] = $process_timestamp;
        $this->db->insert('coop_invest_transaction', $data_insert);
        $id = $this->db->insert_id();

        //Update invest amount
        $transactions = $this->db->select("*")->from("coop_invest_transaction")->where("invest_id = '".$invest_id."' AND status = 1")->get()->result_array();
        $total_unit = 0;
        $total_buy_unit = 0;
        $total_buy_payment = 0;
        foreach($transactions as $transaction) {
            if($transaction['tran_type'] == 1) {
                $total_unit += $transaction['unit'];
                $total_buy_unit += $transaction['unit'];
                $total_buy_payment += $transaction['amount'];
            } else {
                $total_unit -= $transaction['unit'];
            }
        }

        $total_payment = ($total_buy_payment/$total_buy_unit) * $total_unit;

        $data_update = array();
        $data_update['amount'] = $total_payment;
        $data_update['update_date'] = $process_timestamp;
        $data_update['updated_at'] = $process_timestamp;
        $this->db->where('id', $invest_id);
        $this->db->update('coop_invest', $data_update);

        if($tran_type == 1) {
            $account_datas['process'] = "invest_increase";
            $acc_cash_chart = "cash_credit";
        } else {
            $account_datas['process'] = "invest_decrease";
            $acc_cash_chart = "cash_debit";
        }

        //Update balance history if new amonut difference from lastest amount.
        $balance_history = $this->db->select("amount")->from("coop_invest_balance_history")->where("invest_id = '".$invest_id."'")->order_by("created_at DESC")->get()->row_array();

        if(empty($balance_history) || $balance_history['amount'] != $total_payment || (!empty($id) && $transaction_cur['amount'] != $amount)) {
            $data_insert = array();
            $data_insert['invest_id'] = $invest_id;
            $data_insert['amount'] = $total_payment;
            $this->db->insert('coop_invest_balance_history', $data_insert);
            $invest_balance_history_id = $this->db->insert_id();

            if($balance_history['amount'] < $total_payment) {
                $diff_amount = empty($balance_history) ? $total_payment : $total_payment - $balance_history['amount'];
                if($tran_type == 1 || $invest['type'] == 1) {
                    $account_datas['amount']['type'] = "increase";
                    $account_datas['amount']['amount'] = empty($id) ? $amount : $amount - $transaction_cur['amount'];
                } else {
                    if(empty($transaction_cur)) {
                        $account_datas['profit']['type'] = "increase";
                        $account_datas['profit']['amount'] = $profit;

                        $account_datas['amount']['type'] = "increase";
                        $account_datas['amount']['amount'] = $amount;
                    } else {
                        if($profit > $transaction_cur['profit']) {
                            $account_datas['profit']['type'] = "increase";
                            $account_datas['profit']['amount'] = $profit - $transaction_cur['profit'];
                        } else if ($profit < $transaction_cur['profit']) {
                            $account_datas['profit']['type'] = "decrease";
                            $account_datas['profit']['amount'] = $transaction_cur['profit'] - $profit;
                        }

                        if(($amount) > ($transaction_cur['amount'])) {
                            $account_datas['amount']['type'] = "increase";
                            $account_datas['amount']['amount'] = ($amount) - ($transaction_cur['amount']);
                        } else if (($amount) < ($transaction_cur['amount'])) {
                            $account_datas['amount']['type'] = "decrease";
                            $account_datas['amount']['amount'] = ($transaction_cur['amount']) - ($amount);
                        }
                    }
                }
            } else {
                $diff_amount = $balance_history['amount'] - $total_payment;
                if($tran_type == 1 || $invest['type'] == 1) {
                    $account_datas['amount']['type'] = "decrease";
                    $account_datas['amount']['amount'] = $amount;
                } else {
                    if(empty($transaction_cur)) {
                        $account_datas['profit']['type'] = $profit >= 0 ? "increase" : "decrease";
                        $account_datas['profit']['amount'] = $profit >= 0 ? $profit : $profit * (-1);

                        $account_datas['amount']['type'] = "increase";
                        $account_datas['amount']['amount'] = $amount;
                    } else {
                        if($profit > $transaction_cur['profit']) {
                            $account_datas['profit']['type'] = "increase";
                            $account_datas['profit']['amount'] = $profit - $transaction_cur['profit'];
                        } else if ($profit < $transaction_cur['profit']) {
                            $account_datas['profit']['type'] = "decrease";
                            $account_datas['profit']['amount'] = $transaction_cur['profit'] - $profit;
                        }

                        if(($amount) > ($transaction_cur['amount'])) {
                            $account_datas['amount']['type'] = "increase";
                            $account_datas['amount']['amount'] = ($amount) - ($transaction_cur['amount']);
                        } else if (($amount) < ($transaction_cur['amount'])) {
                            $account_datas['amount']['type'] = "decrease";
                            $account_datas['amount']['amount'] = ($transaction_cur['amount']) - ($amount);
                        }
                    }
                }
            }
        }

        //Check fee.
        if($fee > $transaction_cur['fee']) {
            if(empty($account_datas['process']))$account_datas['process'] = "invest_increase"; // process type does not need to change if exist. due to priority.
            $account_datas['fee']['type'] = "increase";
            $account_datas['fee']['amount'] = $fee - $transaction_cur['fee'];
        } else if ($fee < $transaction_cur['fee']) {
            if(empty($account_datas['process']))$account_datas['process'] = "invest_decrease"; // process type does not need to change if exist. due to priority.
            $account_datas['fee']['type'] = "decrease";
            $account_datas['fee']['amount'] = $transaction_cur['fee'] - $fee;
        }

        //Check tax.
        if($tax > $transaction_cur['tax']) {
            if(empty($account_datas['process']))$account_datas['process'] = "invest_increase"; // process type does not need to change if exist. due to priority.
            $account_datas['tax']['type'] = "increase";
            $account_datas['tax']['amount'] = $tax - $transaction_cur['tax'];

            if($tran_type == 1) {
                if(!empty($account_datas['amount'])) {
                    if($account_datas['amount']['type'] == "increase") {
                        $account_datas['amount']['amount'] += $tax;
                    } else {
                        $account_datas['amount']['amount'] -= $tax;
                    }
                } else {
                    $account_datas['amount']['type'] = "increase";
                    $account_datas['amount']['amount'] += $tax;
                }
            }
        } else if ($tax < $transaction_cur['tax']) {
            if(empty($account_datas['process']))$account_datas['process'] = "invest_decrease"; // process type does not need to change if exist. due to priority.
            $account_datas['tax']['type'] = "decrease";
            $account_datas['tax']['amount'] = $transaction_cur['tax'] - $tax;

            if($tran_type == 1) {
                if(!empty($account_datas['amount'])) {
                    if($account_datas['amount']['type'] == "increase") {
                        $account_datas['amount']['amount'] -= $tax;
                    } else {
                        $account_datas['amount']['amount'] += $tax;
                    }
                } else {
                    $account_datas['amount']['type'] = "decrease";
                    $account_datas['amount']['amount'] += $tax;
                }
            }
        }

        //Generate account data.
        if($invest['gen_account_data'] == 1 && !empty($account_datas)) {
            $budget_year_be = $this->account_transaction->get_budget_year($process_timestamp);

            $matchs = $this->db->select('t1.description, t2.chart_id, t2.type, t3.account_chart')
                                ->from("coop_invest_account as t1")
                                ->join("coop_invest_account_match as t2", "t1.id = t2.invest_account_id", "INNER")
                                ->join("coop_account_chart as t3", "t2.chart_id = t3.account_chart_id", "LEFT")
                                ->where("t1.invest_id = '".$invest_id."' AND t1.process = '".$account_datas['process']."' AND t1.status = 1")
                                ->get()->result_array();

            if(!empty($matchs)) {
                $journal_ref = "JV".(date('Y') + 543 - 2500).sprintf('%02d',date("m"));
                $last_journal_ref_account = $this->db->select("journal_ref")->from("coop_account")->where("journal_ref LIKE '%".$journal_ref."%'")->order_by("journal_ref desc")->get()->row();
                if(empty($last_journal_ref_account)) {
                    $journal_ref .= sprintf('%02d',date("d"))."001";
                } else {
                    $last_journal_ref = $last_journal_ref_account->journal_ref;
                    $journal_ref .= !empty($last_journal_ref) ? sprintf('%02d',date("d")).(sprintf('%03d', substr($last_journal_ref, -3) + 1)): sprintf('%02d',date("d"))."001";
                }
                $description = $matchs[0]['description'];

                $match_arr = array();
                foreach($matchs as $match) {
                    $match_arr[$match['type']]['chart_id'] = $match['chart_id'];
                    $match_arr[$match['type']]['desc'] = $match['account_chart'];
                }

                //Generate coop_account data.
                $data_insert = array();
                $data_insert['account_description'] = $description;
                $data_insert['account_datetime'] = date('Y-m-d');
                $data_insert['account_status'] = 0;
                $data_insert['ref_id'] = $id;
                $data_insert['ref_type'] = "coop_invest_transaction.id";
                $data_insert['process'] = "invest_tran_".$account_datas['process'];
                $data_insert['journal_ref'] = $journal_ref;
                $data_insert['journal_type'] = "JV";
                $data_insert['user_id'] = $_SESSION['USER_ID'];
                $data_insert['budget_year'] = $budget_year_be;
                $data_insert['created_at'] = $process_timestamp;
                $data_insert['updated_at'] = $process_timestamp;
                $this->db->insert('coop_account', $data_insert);
                $account_id = $this->db->insert_id();

                $credit = 0;
                $debit = 0;
                $data_inserts = array();
                $seq_no = 1;
                //Generate coop_account_detail data.
                foreach($account_datas as $key => $account_data) {
                    $type = null;
                    $chart_id = null;

                    if($key != "process" && $account_data['amount'] != 0) {
                        //If amount is minus value must be switch nature and make it to be plus value.
                        if($account_data['amount'] < 0) {
                            $account_data['amount'] = $account_data['amount'] * (-1);
                            $account_data['type'] = $account_data['type'] == "increase" ? "decrease" : "increase";
                        }
                        if($account_datas['process'] == "invest_increase") {
                            if($key == "amount") {
                                if($account_data['type'] == "increase") {
                                    $type = "debit";
                                } else {
                                    $type = "credit";
                                }
                                if(!empty($match_arr['share_debit'])) {
                                    $chart_id = $match_arr['share_debit']['chart_id'];
                                    $desc = $match_arr['share_debit']['desc'];
                                } else {
                                    $chart_id = $match_arr[$type]['chart_id'];
                                    $desc = $match_arr[$type]['desc'];
                                }
                            } else if ($key == "fee") {
                                if($account_data['type'] == "increase") {
                                    $type = "debit";
                                } else {
                                    $type = "credit";
                                }
                                $chart_id = $match_arr['fee_debit']['chart_id'];
                                $desc = $match_arr['fee_debit']['desc'];
                            } else if ($key == "tax") {
                                if($account_data['type'] == "increase") {
                                    $type = "credit";
                                } else {
                                    $type = "debit";
                                }
                                $chart_id = $match_arr['tax_credit']['chart_id'];
                                $desc = $match_arr['tax_credit']['desc'];
                            }
                        } else {
                            if($key == "amount") {
                                if($account_data['type'] == "increase") {
                                    $type = "credit";
                                } else {
                                    $type = "debit";
                                }
                                if(!empty($match_arr['share_credit'])) {
                                    $chart_id = $match_arr['share_credit']['chart_id'];
                                    $desc = $match_arr['share_credit']['desc'];
                                } else {
                                    $chart_id = $match_arr[$type]['chart_id'];
                                    $desc = $match_arr[$type]['desc'];
                                }
                            } else if ($key == "profit") {
                                if($account_data['type'] == "increase") {
                                    $type = "debit";
                                } else {
                                    $type = "credit";
                                }
                                $chart_id = $match_arr['profit_debit']['chart_id'];
                                $desc = $match_arr['profit_debit']['desc'];
                            } else if ($key == "fee") {
                                if($account_data['type'] == "increase") {
                                    $type = "debit";
                                } else {
                                    $type = "credit";
                                }
                                $chart_id = $match_arr['fee_debit']['chart_id'];
                                $desc = $match_arr['fee_debit']['desc'];
                            } else if ($key == "tax") {
                                if($account_data['type'] == "increase") {
                                    $type = "credit";
                                } else {
                                    $type = "debit";
                                }
                                $chart_id = $match_arr['tax_credit']['chart_id'];
                                $desc = $match_arr['tax_credit']['desc'];
                            }
                        }

                        if(!empty($type) && !empty($chart_id)) {
                            if($type == "credit") {
                                $credit += $account_data['amount'];
                            } else {
                                $debit += $account_data['amount'];
                            }

                            $data_insert = array();
                            $data_insert['account_id'] = $account_id;
                            $data_insert['account_type'] = $type;
                            $data_insert['account_chart_id'] = $chart_id;
                            $data_insert['account_amount'] = round($account_data['amount'],2);
                            $data_insert['description'] = $desc;
                            $data_insert['seq_no'] = $seq_no++;
                            $data_insert['created_at'] = $process_timestamp;
                            $data_insert['updated_at'] = $process_timestamp;
                            $data_inserts[] = $data_insert;
                            $this->account_transaction->increase_decrease_budget_year($chart_id, $account_data['amount'], $type, $budget_year_be, 1);
                        }
                    }
                }
                if($credit > $debit) {
                    $cash = $credit - $debit;
                    $chart_id = !empty($match_arr[$acc_cash_chart]['chart_id']) ? $match_arr[$acc_cash_chart]['chart_id'] : $match_arr["debit"]['chart_id'];
                    $desc = !empty($match_arr[$acc_cash_chart]['desc']) ? $match_arr[$acc_cash_chart]['desc'] : $match_arr["debit"]['desc'];
                    $data_insert = array();
                    $data_insert['account_id'] = $account_id;
                    $data_insert['account_type'] = "debit";
                    $data_insert['account_chart_id'] = $chart_id;
                    $data_insert['account_amount'] = round($cash,2);
                    $data_insert['description'] = $desc;
                    $data_insert['seq_no'] = $seq_no++;
                    $data_insert['created_at'] = $process_timestamp;
                    $data_insert['updated_at'] = $process_timestamp;
                    $data_inserts[] = $data_insert;
                    $this->account_transaction->increase_decrease_budget_year($chart_id, $cash, "debit", $budget_year_be, 1);
                } else if ($debit > $credit) {
                    $cash = $debit - $credit;
                    $chart_id = !empty($match_arr[$acc_cash_chart]['chart_id']) ? $match_arr[$acc_cash_chart]['chart_id'] : $match_arr["credit"]['chart_id'];
                    $desc = !empty($match_arr[$acc_cash_chart]['desc']) ? $match_arr[$acc_cash_chart]['desc'] : $match_arr["credit"]['desc'];
                    $data_insert = array();
                    $data_insert['account_id'] = $account_id;
                    $data_insert['account_type'] = "credit";
                    $data_insert['account_chart_id'] = $chart_id;
                    $data_insert['account_amount'] = round($cash,2);
                    $data_insert['description'] = $desc;
                    $data_insert['seq_no'] = $seq_no++;
                    $data_insert['created_at'] = $process_timestamp;
                    $data_insert['updated_at'] = $process_timestamp;
                    $data_inserts[] = $data_insert;
                    $this->account_transaction->increase_decrease_budget_year($chart_id, $cash, "credit", $budget_year_be, 1);
                }

                if (!empty($data_inserts)) {
                    $this->db->insert_batch('coop_account_detail', $data_inserts);
                }
            }
        }

        $result['id'] = $id;
        $result['invest_id'] = $invest_id;
        return $result;
    }

    public function get_transaction($id) {
        $result = array();
        $where = "1=1";
        if(!empty($id)) {
            $where .= " AND id = ".$id;
        }
        $transaction = $this->db->select("*")->from("coop_invest_transaction")->where($where)->get()->row_array();
        if(!empty($transaction)) {
            $transaction['amount_format'] = number_format($transaction['amount'],2);
            $transaction['unit_format'] = number_format($transaction['unit']);
            $transaction['fee_format'] = number_format($transaction['fee'],2);
            $transaction['tax_format'] = number_format($transaction['tax'],2);
            $transaction['value_per_unit_format'] = number_format($transaction['value_per_unit'],2);
            $transaction['date_format'] = $this->center_function->ConvertToThaiDate($transaction['date'],'1','0');
            $transaction['date_calender'] = $this->center_function->mydate2date($transaction['date']);
        }
        return $transaction;
    }

    public function edit_bond($org_id, $id, $aver_profit, $credit_rating, $start_date, $due_date, $invest_rate_text, $name, $department_name, $payment_method_text, $unit, $value_per_unit, $source, $status) {
        $process_timestamp = date('Y-m-d H:i:s');
        $amount = $unit * $value_per_unit;

        //Check current data first.
        $invest = $this->db->select("*")->from("coop_invest")->where("id = '".$id."'")->get()->row_array();
        if(!empty($invest)) {
            //IF status change to disable amount must be zero.
            if($status == 2 && $invest['status'] == 1) {
                $amount = 0;
            }
        }

        $data_insert = array();
        $data_insert['org_id'] = $org_id;
        $data_insert['name'] = $department_name;
        $data_insert['amount'] = $amount;
        $data_insert['status'] = !empty($status) ? $status : 1;
        $data_insert['start_date'] = $start_date;
        $data_insert['type'] = 3;
        $data_insert['source'] = $source;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        if(!empty($id)) {
            $data_insert['update_date'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->where('id', $id);
            $this->db->update('coop_invest', $data_insert);
        } else {
            $data_insert['update_date'] = $process_timestamp;
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_invest', $data_insert);
            $id = $this->db->insert_id();
        }

        //Update balance history if new amonut difference from lastest amount.
        $balance_history = $this->db->select("amount")->from("coop_invest_balance_history")->where("invest_id = '".$id."'")->order_by("created_at DESC")->get()->row_array();
        if((empty($balance_history) || $balance_history['amount'] != $amount) && $amount != NULL) {
            $data_insert = array();
            $data_insert['invest_id'] = $id;
            $data_insert['amount'] = $amount;
            $this->db->insert('coop_invest_balance_history', $data_insert);
            $invest_balance_history_id = $this->db->insert_id();

            if($invest['gen_account_data'] == 1) {
                //Generate account data.
                if(empty($balance_history) || $balance_history['amount'] < $amount) {
                    $diff_amount = empty($balance_history) ? $amount : $amount - $balance_history['amount'];
                    $process = "invest_increase";
                } else {
                    $diff_amount = $balance_history['amount'] - $amount;
                    $process = "invest_decrease";
                }

                $budget_year_be = $this->account_transaction->get_budget_year($process_timestamp);

                $matchs = $this->db->select('t1.description, t2.chart_id, t2.type, t3.account_chart')
                                    ->from("coop_invest_account as t1")
                                    ->join("coop_invest_account_match as t2", "t1.id = t2.invest_account_id", "INNER")
                                    ->join("coop_account_chart as t3", "t2.chart_id = t3.account_chart_id", "LEFT")
                                    ->where("t1.invest_id = '".$id."' AND t1.process = '".$process."' AND t1.status = 1")
                                    ->get()->result_array();

                $journal_ref = "JV".(date('Y') + 543 - 2500).sprintf('%02d',date("m"));
                $last_journal_ref_account = $this->db->select("journal_ref")->from("coop_account")->where("journal_ref LIKE '%".$journal_ref."%'")->order_by("journal_ref desc")->get()->row();
                if(empty($last_journal_ref_account)) {
                    $journal_ref .= sprintf('%02d',date("d"))."001";
                } else {
                    $last_journal_ref = $last_journal_ref_account->journal_ref;
                    $journal_ref .= !empty($last_journal_ref) ? sprintf('%02d',date("d")).(sprintf('%03d', substr($last_journal_ref, -3) + 1)): sprintf('%02d',date("d"))."001";
                }
                $description = $matchs[0]['description'];

                $data_insert = array();
                $data_insert['account_description'] = $description;
                $data_insert['account_datetime'] = date('Y-m-d');
                $data_insert['account_status'] = 0;
                $data_insert['ref_id'] = $invest_balance_history_id;
                $data_insert['ref_type'] = "coop_invest_balance_history.id";
                $data_insert['process'] = "invest_3_".$process;
                $data_insert['journal_ref'] = $journal_ref;
                $data_insert['journal_type'] = "JV";
                $data_insert['user_id'] = $_SESSION['USER_ID'];
                $data_insert['budget_year'] = $budget_year_be;
                $data_insert['created_at'] = $process_timestamp;
                $data_insert['updated_at'] = $process_timestamp;
                $this->db->insert('coop_account', $data_insert);
                $account_id = $this->db->insert_id();

                $data_inserts = array();
                $seq_no = 1;
                foreach($matchs as $match) {
                    $data_insert = array();
                    $data_insert['account_id'] = $account_id;
                    $data_insert['account_type'] = $match['type'];
                    $data_insert['account_chart_id'] = $match['chart_id'];
                    $data_insert['account_amount'] = $diff_amount;
                    $data_insert['description'] = $match['account_chart'];
                    $data_insert['seq_no'] = $seq_no++;
                    $data_insert['created_at'] = $process_timestamp;
                    $data_insert['updated_at'] = $process_timestamp;
                    $data_inserts[] = $data_insert;
                    $this->account_transaction->increase_decrease_budget_year($match['chart_id'], $diff_amount, $match['type'], $budget_year_be, 1);
                }

                if (!empty($data_inserts)) {
                    $this->db->insert_batch('coop_account_detail', $data_inserts);
                }
            }
        }

        $detail = $this->db->select("id")->from("coop_invest_detail")->where("invest_id = '".$id."' AND status = 1")->get()->row_array();

        $data_insert = array();
        $data_insert['invest_id'] = $id;
        $data_insert['invest_rate_text'] = $invest_rate_text;
        $data_insert['invest_date'] = $start_date;
        $data_insert['end_date'] = $due_date;
        $data_insert['payment_method_text'] = $payment_method_text;
        $data_insert['aver_profit'] = $aver_profit;
        $data_insert['credit_rating'] = $credit_rating;
        $data_insert['unit'] = $unit;
        $data_insert['value_per_unit'] = $value_per_unit;
        $data_insert['name'] = $name;
        $data_insert['status'] = 1;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        if(!empty($detail)) {
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->where('id', $detail['id']);
            $this->db->update('coop_invest_detail', $data_insert);
        } else {
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_invest_detail', $data_insert);
            $detail_id = $this->db->insert_id();
        }

        return $id;
    }

    public function edit_private_share($org_id, $id, $aver_profit, $credit_rating, $start_date, $due_date, $invest_rate_text, $name, $department_name, $payment_method_text, $unit, $value_per_unit, $source, $status) {
        $process_timestamp = date('Y-m-d H:i:s');
        $amount = $unit * $value_per_unit;

        //Check current data first.
        $invest = $this->db->select("*")->from("coop_invest")->where("id = '".$id."'")->get()->row_array();
        if(!empty($invest)) {
            if($status == 2 && $invest['status'] == 1) {
                $amount = 0;
            }
        }

        $data_insert = array();
        $data_insert['org_id'] = $org_id;
        $data_insert['name'] = $department_name;
        $data_insert['amount'] = $amount;
        $data_insert['status'] = !empty($status) ? $status : 1;
        $data_insert['start_date'] = $start_date;
        $data_insert['type'] = 4;
        $data_insert['source'] = $source;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        if(!empty($id)) {
            $data_insert['update_date'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->where('id', $id);
            $this->db->update('coop_invest', $data_insert);
        } else {
            $data_insert['update_date'] = $process_timestamp;
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_invest', $data_insert);
            $id = $this->db->insert_id();
        }

        //Update balance history if new amonut difference from lastest amount.
        $balance_history = $this->db->select("amount")->from("coop_invest_balance_history")->where("invest_id = '".$id."'")->order_by("created_at DESC")->get()->row_array();
        if((empty($balance_history) || $balance_history['amount'] != $amount) && ($amount != NULL || $amount == 0)) {
            $data_insert = array();
            $data_insert['invest_id'] = $id;
            $data_insert['amount'] = $amount;
            $this->db->insert('coop_invest_balance_history', $data_insert);
            $invest_balance_history_id = $this->db->insert_id();

            if($invest['gen_account_data'] == 1) {
                //Generate account data.
                if(empty($balance_history) || $balance_history['amount'] < $amount) {
                    $diff_amount = empty($balance_history) ? $amount : $amount - $balance_history['amount'];
                    $process = "invest_increase";
                } else {
                    $diff_amount = $balance_history['amount'] - $amount;
                    $process = "invest_decrease";
                }

                $budget_year_be = $this->account_transaction->get_budget_year($process_timestamp);

                $matchs = $this->db->select('t1.description, t2.chart_id, t2.type, t3.account_chart')
                                    ->from("coop_invest_account as t1")
                                    ->join("coop_invest_account_match as t2", "t1.id = t2.invest_account_id", "INNER")
                                    ->join("coop_account_chart as t3", "t2.chart_id = t3.account_chart_id", "LEFT")
                                    ->where("t1.invest_id = '".$id."' AND t1.process = '".$process."' AND t1.status = 1")
                                    ->get()->result_array();

                $journal_ref = "JV".(date('Y') + 543 - 2500).sprintf('%02d',date("m"));
                $last_journal_ref_account = $this->db->select("journal_ref")->from("coop_account")->where("journal_ref LIKE '%".$journal_ref."%'")->order_by("journal_ref desc")->get()->row();
                if(empty($last_journal_ref_account)) {
                    $journal_ref .= sprintf('%02d',date("d"))."001";
                } else {
                    $last_journal_ref = $last_journal_ref_account->journal_ref;
                    $journal_ref .= !empty($last_journal_ref) ? sprintf('%02d',date("d")).(sprintf('%03d', substr($last_journal_ref, -3) + 1)): sprintf('%02d',date("d"))."001";
                }
                $description = $matchs[0]['description'];

                $data_insert = array();
                $data_insert['account_description'] = $description;
                $data_insert['account_datetime'] = date('Y-m-d');
                $data_insert['account_status'] = 0;
                $data_insert['ref_id'] = $invest_balance_history_id;
                $data_insert['ref_type'] = "coop_invest_balance_history.id";
                $data_insert['process'] = "invest_4_".$process;
                $data_insert['journal_ref'] = $journal_ref;
                $data_insert['journal_type'] = "JV";
                $data_insert['user_id'] = $_SESSION['USER_ID'];
                $data_insert['budget_year'] = $budget_year_be;
                $data_insert['created_at'] = $process_timestamp;
                $data_insert['updated_at'] = $process_timestamp;
                $this->db->insert('coop_account', $data_insert);
                $account_id = $this->db->insert_id();

                $data_inserts = array();
                $seq_no = 1;
                foreach($matchs as $match) {
                    $data_insert = array();
                    $data_insert['account_id'] = $account_id;
                    $data_insert['account_type'] = $match['type'];
                    $data_insert['account_chart_id'] = $match['chart_id'];
                    $data_insert['account_amount'] = $diff_amount;
                    $data_insert['description'] = $match['account_chart'];
                    $data_insert['seq_no'] = $seq_no++;
                    $data_insert['created_at'] = $process_timestamp;
                    $data_insert['updated_at'] = $process_timestamp;
                    $data_inserts[] = $data_insert;
                    $this->account_transaction->increase_decrease_budget_year($match['chart_id'], $diff_amount, $match['type'], $budget_year_be, 1);
                }

                if (!empty($data_inserts)) {
                    $this->db->insert_batch('coop_account_detail', $data_inserts);
                }
            }
        }

        $detail = $this->db->select("id")->from("coop_invest_detail")->where("invest_id = '".$id."' AND status = 1")->get()->row_array();

        $data_insert = array();
        $data_insert['invest_id'] = $id;
        $data_insert['invest_rate_text'] = $invest_rate_text;
        $data_insert['invest_date'] = $start_date;
        $data_insert['end_date'] = $due_date;
        $data_insert['payment_method_text'] = $payment_method_text;
        $data_insert['aver_profit'] = $aver_profit;
        $data_insert['credit_rating'] = $credit_rating;
        $data_insert['unit'] = $unit;
        $data_insert['value_per_unit'] = $value_per_unit;
        $data_insert['name'] = $name;
        $data_insert['status'] = 1;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        if(!empty($detail)) {
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->where('id', $detail['id']);
            $this->db->update('coop_invest_detail', $data_insert);
        } else {
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_invest_detail', $data_insert);
            $detail_id = $this->db->insert_id();
        }

        return $id;
    }

    public function edit_set_share($org_id, $id, $name, $period, $source, $status) {
        //Check current data first.
        $amount = NULL;
        $invest = $this->db->select("*")->from("coop_invest")->where("id = '".$id."'")->get()->row_array();
        if(!empty($invest)) {
            if($status == 2 && $invest['status'] == 1) {
                $amount = 0;
            }
            if($status == 1 && $invest['status'] == 2) {
                $balance_history = $this->db->select("amount")->from("coop_invest_balance_history")->where("invest_id = '".$id."'")->order_by("created_at DESC")->get()->row_array();
                $amount = $balance_history['amount'];
            }
        }

        $process_timestamp = date('Y-m-d H:i:s');
        $data_insert = array();
        $data_insert['org_id'] = $org_id;
        $data_insert['name'] = $name;
        if($amount != NULL || $amount == 0) $data_insert['amount'] = $amount;
        $data_insert['status'] = !empty($status) ? $status : 1;
        $data_insert['start_date'] = $process_timestamp;
        $data_insert['type'] = 5;
        $data_insert['source'] = $source;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        if(!empty($id)) {
            $data_insert['update_date'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->where('id', $id);
            $this->db->update('coop_invest', $data_insert);
        } else {
            $data_insert['update_date'] = $process_timestamp;
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_invest', $data_insert);
            $id = $this->db->insert_id();
        }

        //Update balance history if new amonut difference from lastest amount.
        $balance_history = $this->db->select("amount")->from("coop_invest_balance_history")->where("invest_id = '".$id."'")->order_by("created_at DESC")->get()->row_array();
        if((empty($balance_history) || $balance_history['amount'] != $amount) && $amount != NULL) {
            $data_insert = array();
            $data_insert['invest_id'] = $id;
            $data_insert['amount'] = $amount;
            $this->db->insert('coop_invest_balance_history', $data_insert);
        }

        $detail = $this->db->select("id")->from("coop_invest_detail")->where("invest_id = '".$id."' AND status = 1")->get()->row_array();

        $data_insert = array();
        $data_insert['invest_id'] = $id;
        $data_insert['name'] = $period;
        $data_insert['status'] = 1;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        if(!empty($detail)) {
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->where('id', $detail['id']);
            $this->db->update('coop_invest_detail', $data_insert);
        } else {
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_invest_detail', $data_insert);
            $detail_id = $this->db->insert_id();
        }

        return $id;
    }

    public function add_invest_share_value($invest_id, $date, $value) {
        $data_insert = array();
        $data_insert['invest_id'] = $invest_id;
        $data_insert['status'] = 1;
        $data_insert['value'] = $value;
        $data_insert['date'] = $date;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        $data_insert['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('coop_invest_share_value', $data_insert);
        $id = $this->db->insert_id();
        return $id;
    }

    /*
        $date must be timestamp.
    */
    public function get_invest_share_value($invest_id, $date) {
        $result = array();
        if(empty($date)) $date = date('Y-m-d H:i:s');

        $first_interest = $this->db->select("*")->from("coop_invest_share_value")->where("invest_id = '".$invest_id."' AND DATE(date) <= '".$date."' AND status = 1")->order_by("date, id")->get()->row_array();
        $last_interest = $this->db->select("*")->from("coop_invest_share_value")->where("invest_id = '".$invest_id."' AND DATE(date) <= '".$date."' AND status = 1")->order_by("date DESC, id DESC")->get()->row_array();
        if(!empty($first_interest)) {
            $first_interest['date_thai'] = $this->center_function->ConvertToThaiDate($first_interest['date'],'1','0');
            $first_interest['date_calender'] = $this->center_function->mydate2date($first_interest['date']);
            $first_interest['value_format'] = number_format($first_interest['value'],2);
            $first_interest['value'] = $first_interest['value'];
        }
        if(!empty($last_interest)) {
            $last_interest['date_thai'] = $this->center_function->ConvertToThaiDate($last_interest['date'],'1','0');
            $last_interest['date_calender'] = $this->center_function->mydate2date($last_interest['date']);
            $last_interest['value_format'] = number_format($last_interest['value'],2);
            $last_interest['value'] = $last_interest['value'];
        }

        $result['first'] = $first_interest;
        $result['last'] = $last_interest;
        return $result;
    }

    public function get_invest_type_share_values($type_id, $date) {
        $result = array();
        if(empty($date)) $date = date('Y-m-d H:i:s');

        $invests = $this->db->select("id, name")->from("coop_invest")->where("type = '".$type_id."' AND status = 1")->get()->result_array();
        $data = array();
        foreach($invests as $invest) {
            $share_val = $this->db->select("value, date")->from("coop_invest_share_value")->where("invest_id = '".$invest['id']."' AND status = 1 AND date <= '".$date."'")->order_by("date DESC, id DESC")->get()->row_array();
            $inv = array();
            $inv['id'] = $invest['id'];
            $inv['name'] = $invest['name'];
            if(!empty($share_val)) {
                $inv['value'] = $share_val['value'];
                $inv['date'] = $share_val['date'];
            } else {
                $inv['value'] = 0;
            }
            $result['data'][] = $inv;
        }
        return $result;
    }

    public function remove_profit($id) {
        $result = array();

        $data_update = array();
        $data_update['status'] = 2;
        $data_update['cancel_user_id'] = $_SESSION['USER_ID'];
        $data_update['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('coop_invest_profit_transaction', $data_update);

        return $id;
    }

    public function remove_transactrion($id) {
        $result = array();

        $data_update = array();
        $data_update['status'] = 2;
        $data_update['cancel_user_id'] = $_SESSION['USER_ID'];
        $data_update['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('coop_invest_transaction', $data_update);

        return $id;
    }

    /*
        if type == NULL => get All.
        invest_type must be array / if invest_type = NULL then get all
     */
    public function get_profit($type = NULL, $org_id, $from_date, $thru_date, $invest_type = NULL) {
        $result = array();

        $where = "";
        //Check If $type not null where type = $type / if not type IN $invest_type For get all data of interest or dividend.
        if(!empty($_POST['type'])) {
            $where .= " AND t1.type = '".$type."'";
        } else if(!empty($invest_type)) {
            $where .= " AND t1.type IN (".implode(',',$invest_type).")";
        }
        if(!empty($org_id)) {
			$where .= " AND t1.org_id = '".$org_id."'";
		}
		if(!empty($from_date)) {
			$from_date = $this->center_function->ConvertToSQLDate($from_date);
			$where .= " AND DATE(t4.date) >= '".$from_date."'";
		}
		if(!empty($thru_date)) {
			$thru_date = $this->center_function->ConvertToSQLDate($thru_date);
			$where .= " AND DATE(t4.date) <= '".$thru_date."'";
		}

        $invests = $this->db->select("t1.id,
                                        t1.name,
                                        t1.amount,
                                        t1.start_date,
                                        t2.invest_rate_text,
                                        t2.invest_date,
                                        t2.end_date,
                                        t2.payment_method_text as period,
                                        t3.name as type_name,
                                        t4.date as interest_date,
                                        t4.rate,
                                        t4.amount as interest_amount,
                                        t4.note,
                                        t5.name as org_name")
                            ->from("coop_invest as t1")
                            ->join("coop_invest_detail as t2", "t1.id = t2.invest_id", "LEFT")
                            ->join("coop_invest_type as t3", "t1.type = t3.id", "INNER")
                            ->join("coop_invest_profit_transaction as t4", "t1.id = t4.invest_id", "INNER")
                            ->join("coop_invest_organization as t5", "t1.org_id = t5.id", "LEFT")
                            ->where("t1.status != 3".$where)
                            ->order_by("t1.org_id, t3.order, t1.start_date")
                            ->get()->result_array();
        return $invests;
    }

    //Check If $type not null where type = $type / if not Then where type IN $type_arr For get all data of interest or dividend.
    public function check_invest($type, $type_arr) {
        if(!empty($type)) {
            $where .= " AND t1.type = '".$type."'";
        } if(!empty($type_arr)) {
            $where .= " AND t1.type IN (".implode(',',$type_arr).")";
        }

        $invest = $this->db->select("t1.id")
							->from("coop_invest as t1")
							->where("t1.status != 3".$where)
                            ->get()->row();
        if(!empty($invest)) {
            return "success";
        } else {
            return "not-found-data";
        }
    }

     //Check If $type not null where type = $type / if not Then where type IN $type_arr For get all data of interest or dividend.
    public function check_invest_profit($type, $org_id, $from_date, $thru_date, $type_arr) {
        if(!empty($type)) {
            $where .= " AND t1.type = '".$type."'";
        } if(!empty($type_arr)) {
            $where .= " AND t1.type IN (".implode(',',$type_arr).")";
        }
        if(!empty($org_id)) {
			$where .= " AND t1.org_id = '".$org_id."'";
		}
		if(!empty($from_date)) {
			$from_date = $this->center_function->ConvertToSQLDate($from_date);
			$where .= " AND DATE(t2.date) >= '".$from_date."'";
		}
		if(!empty($thru_date)) {
			$thru_date = $this->center_function->ConvertToSQLDate($thru_date);
			$where .= " AND DATE(t2.date) <= '".$thru_date."'";
		}

        $invest = $this->db->select("t1.id")
							->from("coop_invest as t1")
							->join("coop_invest_profit_transaction as t2", "t1.id = t2.invest_id AND t2.status = 1", "INNER")
							->where("t1.status != 3".$where)
                            ->get()->row();
        if(!empty($invest)) {
            return "success";
        } else {
            return "not-found-data";
        }
    }

    public function get_share_transaction_report_data($from_date, $thru_date) {
        $result = array();

        //Get previous total transaction to set to be balance.
        $invest_balances = array();
        $prev_transactions = $this->db->select("t1.id, t2.amount, t2.fee, t2.tran_type")
                                        ->from("coop_invest as t1")
                                        ->join("coop_invest_transaction as t2", "t1.id = t2.invest_id AND t2.status = 1 AND DATE(t2.date) < DATE('".$from_date."')", "INNER")
                                        ->where("t1.start_date <= '".$thur_date."'")
                                        ->get()
                                        ->result_array();
        foreach($prev_transactions as $transcation) {
            if($transaction['tran_type'] == 1) {
                $invest_balances[$transaction['id']] += $transaction['amount'] + $transaction['fee'];
            } else {
                $invest_balances[$transaction['id']] -= $transaction['amount'] + $transaction['fee'];
            }
        }

        $invest_sells = array();
        $transactions = $this->db->select("t1.id, t1.name, t2.amount, t2.fee, t2.tran_type, t3.name as type_name, t4.name as org_name")
                                ->from("coop_invest as t1")
                                ->join("coop_invest_transaction as t2", "t1.id = t2.invest_id AND t2.status = 1 AND DATE(t2.date) >= DATE('".$from_date."') AND DATE(t2.date) <= DATE('".$thru_date."')", "INNER")
                                ->join("coop_invest_type as t3", "t1.type = t3.id", "INNER")
                                ->join("coop_invest_organization as t4", "t1.org_id = t4.id", "LEFT")
                                ->order_by("t1.org_id, t3.order, t1.id")
                                ->get()
                                ->result_array();
        foreach($transactions as $transaction) {
            if($transaction['tran_type'] == 1) {
                $invest_balances[$transaction['id']] += $transaction['amount'] + $transaction['fee'];
            } else {
                $invest_sells[$transaction['id']]['amount'] += $transaction['amount'] - $transaction['fee'];
                $invest_sells[$transaction['id']]['id'] = $transaction['id'];
                $invest_sells[$transaction['id']]['name'] = $transaction['name'];
                $invest_sells[$transaction['id']]['tran_type'] = $transaction['tran_type'];
                $invest_sells[$transaction['id']]['type_name'] = $transaction['type_name'];
                $invest_sells[$transaction['id']]['org_name'] = $transaction['org_name'];
            }
        }

        $result['invest_balances'] = $invest_balances;
        $result['invest_sells'] = $invest_sells;
        return $result;
    }

    //Check If $type not null where type = $type / if not Then where type IN $type_arr For get all data of interest or dividend.
    public function get_invest_balance($type, $type_arr) {
        $result = array();
        $where = "t1.status = 1";
        if(!empty($type)) {
            $where .= " AND t1.type = '".$type."'";
        } if(!empty($type_arr)) {
            $where .= " AND t1.type IN (".implode(',',$type_arr).")";
        }

        $invests = $this->db->select("t1.id, t1.amount, t1.name, t1.type, t2.name as type_name, t4.name as org_name")
                            ->from("coop_invest as t1")
                            ->join("coop_invest_type as t2", "t1.type = t2.id", "INNER")
                            ->join("coop_invest_organization as t4", "t1.org_id = t4.id", "LEFT")
                            ->where($where)
                            ->order_by("t1.org_id, t2.order, t1.id")
                            ->get()->result_array();
        foreach($invests as $invest) {
            //Get current balance.
            $shares = $this->db->select("amount, unit")->from("coop_invest_transaction")->where("invest_id = ".$invest['id']." AND status = 1")->order_by("date, id")->get()->result_array();
            $share_amount = 0;
            $value_per_unit = 0;
            foreach($shares as $share) {
                if($transaction['tran_type'] == 1) {
                    $share_amount += $share['amount'];
                } else {
                    $share_amount += $share['amount'];
                }
                $value_per_unit = $share['amount'] / $share['unit'];
            }

            $share_val = $this->db->select("value")->from("coop_invest_share_value")->where("invest_id = '".$invest['id']."' AND status = 1")->order_by("date DESC, id DESC")->get()->row_array();
            if(!empty($share_rate)) {
                $invest['balance'] = $share_val * $share_amount;
            } else {
                $invest['balance'] = $value_per_unit * $share_amount;
            }
            $result[] = $invest;
        }

        return $result;
    }

    //Check If $type not null where type = $type / if not Then where type IN $type_arr For get all data of interest or dividend.
    public function get_expect_profit($type, $type_arr) {
        $result = array();

        $where = "t1.status = 1 AND DATE(t3.end_date) >= DATE(NOW())";
        if(!empty($type)) {
            $where .= " AND t1.type = '".$type."'";
        } if(!empty($type_arr)) {
            $where .= " AND t1.type IN (".implode(',',$type_arr).")";
        }

        $invests = $this->db->select("t1.id, t1.amount, t1.name, t1.type, t2.name as type_name, t3.invest_rate_text as rate, t3.end_date, t4.name as org_name")
                            ->from("coop_invest as t1")
                            ->join("coop_invest_type as t2", "t1.type = t2.id", "INNER")
                            ->join("coop_invest_detail as t3", "t1.id = t3.invest_id AND t3.status = 1", "INNER")
                            ->join("coop_invest_organization as t4", "t1.org_id = t4.id", "LEFT")
                            ->where($where)
                            ->order_by("t1.org_id, t2.order, t1.id")
                            ->get()->result_array();
        return $invests;
    }

    public function get_account_info($id) {
        $result = array();
        $invest = $this->db->select("gen_account_data, type")->from("coop_invest")->where("id = '".$id."'")->get()->row_array();
        $result['id']= $id;
        $result['type']= $invest['type'];
        $result['gen_account_data']= $invest['gen_account_data'];

        $matchs = $this->db->select("t1.process, t1.description, t2.type, t2.chart_id")
                            ->from("coop_invest_account as t1")
                            ->join("coop_invest_account_match as t2", "t1.id = t2.invest_account_id", "left")
                            ->where("t1.invest_id = '".$id."' AND t1.status = 1")
                            ->get()->result_array();
        foreach($matchs as $match) {
            $result['matchs'][$match['process']][$match['type']] = $match['chart_id'];
            $result['matchs'][$match['process']]["description"] = $match['description'];
        }

        return $result;
    }

    public function add_account_match($data) {
        $result = array();
        $process_timestamp = date('Y-m-d H:i:s');

        $data_insert = array();
        $data_insert['gen_account_data'] = $data['status'];
        $data_insert['updated_at'] = $process_timestamp;
        $this->db->where('id', $data['invest_id']);
        $this->db->update('coop_invest', $data_insert);

        if($data['status'] == 1) {
            //Disable all exist data.
            $data_insert = array();
            $data_insert['status'] = 2;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->where('invest_id', $data['invest_id']);
            $this->db->update('coop_invest_account', $data_insert);

            //Generate new data.
            $matchs = $_POST['data'][$_POST['type']];
            foreach($matchs as $process => $match) {
                $data_insert = array();
                $data_insert['invest_id'] = $data['invest_id'];
                $data_insert['process'] = $process;
                $data_insert['user_id'] = $_SESSION['USER_ID'];
                $data_insert['description'] = $match['desc'];
                $data_insert['status'] = 1;
                $data_insert['created_at'] = $process_timestamp;
                $data_insert['updated_at'] = $process_timestamp;
                $this->db->insert('coop_invest_account', $data_insert);
                $invest_account_id = $this->db->insert_id();

                foreach($match as $key => $chart_id) {
                    if($key != "desc") {
                        $data_insert = array();
                        $data_insert['invest_account_id'] = $invest_account_id;
                        $data_insert['chart_id'] = $chart_id;
                        $data_insert['type'] = $key;
                        $data_insert['created_at'] = $process_timestamp;
                        $this->db->insert('coop_invest_account_match', $data_insert);
                    }
                }
            }
        }

        return "success";
    }

    public function get_organizations() {
        $result = $this->db->select("id, name, status")->from("coop_invest_organization")->where("status = 1")->get()->result_array();
        return $result;
    }

    public function get_organization($id) {
        $result = $this->db->select("id, name, status")->from("coop_invest_organization")->where("id = '".$id."'")->get()->row_array();
        return $result;
    }

    //If id is empty or null, system shall generate new data.
    public function edit_organization($id, $name, $status) {
        $process_timestamp = date('Y-m-d H:i:s');
        $data_insert = array();
        if(!empty($name)) $data_insert['name'] = $name;
        if(!empty($status)) $data_insert['status'] = $status;
        $data_insert['user_id'] = $_SESSION['USER_ID'];

        if(!empty($id)) {
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->where('id', $id);
            $this->db->update('coop_invest_organization', $data_insert);
        } else {
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_invest_organization', $data_insert);
            $id = $this->db->insert_id();
        }

        return $id;
    }
}