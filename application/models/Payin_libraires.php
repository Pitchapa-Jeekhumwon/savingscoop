<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Content-Type: text/html; charset=UTF-8');

class Payin_libraires extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('html', 'url'));
        $this->load->model("Finance_libraries", "Finance_libraries");
    }

    /*
        ซื้อหุ้น = 0 = 7342
        ชำระหนี้ = 1 = 7343
        เงินฝาก = 2 = 7369

        สถานะ paid
        0 = ยังไม่ได้จ่าย
        1 = จ่ายแล้ว

        need csv file.
    */
    public function import_bank_data($file) {
        $result = array();
        $payin_bank_id = NULL;
        $payin_bank_head_id = NULL;

        //For ktb.
        //Check First.
        if (($handle = fopen($file["file"]["tmp_name"], "r")) !== FALSE) {
            $duplicate_ref_2 = "[";
            while(($data = fgetcsv($handle, 10000, ",")) != FALSE) {
                if($data[0] == "D") {
                    $detail_check = $this->db->select("*")->from("coop_payin_bank_detail")->where("ref2 = '".$data[8]."' AND status != 2 AND type != 4")->get()->row_array();
                    if(!empty($detail_check)) {
                        $duplicate_ref_2 .= $duplicate_ref_2 == "[" ? $data[8] : ",".$data[8];
                    }
                }
            }

            if($duplicate_ref_2 != "[") {
                $result['status'] = 'fail';
                $result['massage'] = 'มีข้อมูลซ้ำ Ref_2 :: '.$duplicate_ref_2."]";
                return $result;
            }
        }
        if (($handle = fopen($file["file"]["tmp_name"], "r")) !== FALSE) {
            $is_run = false;

            $share_status = 0;
            $loan_status = 0;
            $dept_status = 0;
            $unknow_status = 0;
            
            $details = array();
            $type = 0;
            $data = fgetcsv($handle, 10000, ",");
            while(($data = fgetcsv($handle, 10000, ",")) != FALSE) {
                if ($data[0] != "Record Type" && $data[0] != "H" && $data[0] != "D" && $data[0] != "T") {
                    break; 
                } else if($data[0] == "H") {
                    $payin_bank = $this->db->select("id")->from("coop_payin_bank")->where("DATE(import_at) = '".date('Y-m-d')."'")->get()->row_array();
                    if(empty($payin_bank)) {
                        $data_insert = array();
                        $data_insert['bank_code'] = $data[2];
                        // $data_insert['company_account'] = $data[3];
                        // $data_insert['company_name'] = $data[4];
                        // $data_insert['effective_date'] = $data[5];
                        // $data_insert['service_code'] = $data[6];
                        $data_insert['status'] = 1;
                        $data_insert['share_status'] = 0;
                        $data_insert['loan_status'] = 0;
                        $data_insert['dept_status'] = 0;
                        $data_insert['unknow_status'] = 0;
                        $data_insert['import_at'] = date('Y-m-d H:i:s');
                        $data_insert['created_at'] = date('Y-m-d H:i:s');
                        $this->db->insert('coop_payin_bank', $data_insert);
                        $payin_bank_id = $this->db->insert_id();
                    } else {
                        $payin_bank_id = $payin_bank['id'];
                    }

                    $data_insert = array();
                    $data_insert['payin_bank_id'] = $payin_bank_id;
                    $data_insert['bank_code'] = $data[2];
                    $data_insert['company_account'] = $data[3];
                    $data_insert['company_name'] = $data[4];
                    $data_insert['effective_date'] = $data[5];
                    $data_insert['service_code'] = $data[6];
                    $data_insert['created_at'] = date('Y-m-d H:i:s');
                    $this->db->insert('coop_payin_bank_header', $data_insert);
                    $payin_bank_head_id =  $this->db->insert_id();

                    if($data[6] == 7342){
                        $type = 0;// ซื้อหุ้น
                    }elseif($data[6] == 7343){
                        $type = 1;// ชำระหนี้
                    }elseif($data[6] == 7369){
                        $type = 2;// เงินฝาก
                    }
                    $is_run = true;
                } else if($data[0] == "D") {
                    $c_type = 4;
                    $unknow_desc = NULL;
                    $payin = $this->db->select("*")->from("coop_payin")->where("ref_2 = '".$data[8]."'")->get()->row_array();

                    if(!empty($payin) && !empty($data[8]) && !empty($data[15])) {
                        $type = $payin['type'];
                        if($type == 0) {
                            $share_status = 1;
                        } else if ($type == 1) {
                            $loan_status = 1;
                        } else if ($type ==2) {
                            $dept_status = 1;
                        }
                        $c_type = $type;
                    } else {
                        $unknow_status = 1;
                        $unknow_desc = "ข้อมูลไม่ครบถ้วน";
                        if(empty($payin)) {
                            $unknow_desc = "ไม่มีข้อมูลอ้างอิงในระบบ";
                        }
                    }

                    $data_insert = array();
                    $data_insert['payin_bank_id'] = $payin_bank_id;
                    $data_insert['head_id'] = $payin_bank_head_id;
                    $data_insert['seq_no'] = $data[1];
                    $data_insert['bank_code'] = $data[2];
                    $data_insert['company_account'] = $data[3];
                    $data_insert['payment_date'] = $data[4];
                    $data_insert['payment_time'] = $data[5];
                    $data_insert['customer_name'] = $data[6];
                    $data_insert['ref1'] = $data[7];
                    $data_insert['ref2'] = $data[8];
                    $data_insert['reg3'] = $data[9];
                    $data_insert['branch_no'] = $data[10];
                    $data_insert['teller_no'] = $data[11];
                    $data_insert['kind_of_transaction'] = $data[12];
                    $data_insert['transaction_code'] = $data[13];
                    $data_insert['cheque_no'] = $data[14];
                    $data_insert['amount'] = $data[15];
                    $data_insert['cheque_bank_code'] = $data[16];
                    $data_insert['last_col'] = $data[17];
                    $data_insert['status'] = 1;
                    $data_insert['type'] = $c_type;
                    $data_insert['unknow_desc'] = $unknow_desc;
                    $data_insert['created_at'] = date('Y-m-d H:i:s');
                    $data_insert['updated_at'] = date('Y-m-d H:i:s');
                    $details[] = $data_insert;

                    if(!empty($payin) && !empty($data[7]) && !empty($data[8]) && !empty($data[15])) {
                        if($type == 0) {
                            $share_status = 1;
                        } else if ($type == 1) {
                            $loan_status = 1;
                        } else if ($type ==2) {
                            $dept_status = 1;
                        }
                    } else {
                        $unknow_status = 1;
                    }
                }
            }

            if(!empty($details)) {
                $this->db->insert_batch('coop_payin_bank_detail', $details);
            }

            $data_update = array();
            if(!empty($share_status)) $data_update['share_status'] = $share_status;
            if(!empty($loan_status)) $data_update['loan_status'] = $loan_status;
            if(!empty($dept_status)) $data_update['dept_status'] = $dept_status;
            if(!empty($unknow_status)) $data_update['unknow_status'] = $unknow_status;
            $this->db->where('id', $payin_bank_id);
            $this->db->update('coop_payin_bank', $data_update);
        }

        if(!$is_run) {
            $result['status'] = 'fail';
            $result['massage'] = 'ไฟล์ไม่ถูกต้อง';
        } else {
            $result['status'] = 'success';
            $result['massage'] = 'ทำรายการสำเร็จ';
            $result['payin_bank_id'] = $payin_bank_id;
        }

        return $result;
    }

    public function approve_import_data($id, $type) {
        $result = array();
        $timestamp = date('Y-m-d H:i:s');

        //Check if data not miss.
        if(empty($id) || (empty($type) && $type != 0)) {
            $result['status'] = 'fail';
            $result['massage'] = 'ข้อมูลไม่ครบถ้วน';
        } else {
            //Check if data already approve.
            $where = " id = '".$id."'";
            if ($type == 0) {
                $where .= " AND share_status = 3";
            } else if ($type == 2) {
                $where .= " AND dept_status = 3";
            }
            $approved_payin = $this->db->select("id")->from("coop_payin_bank")->where($where)->get()->row();
            if(!empty($approved_payin)) {
                $result['status'] = 'fail';
                $result['massage'] = 'รายการนี้ได้ดำเนินการอนุมัติไปแล้ว';
            } else if ($type == 0) {
                //Share process.
                $where = "t1.payin_bank_id = '".$id."' AND t1.type = '".$type."' AND t1.status = 1";
                $details = $this->db->select("t1.id, t1.amount, t1.payin_bank_id, t2.member_id")
                                    ->from("coop_payin_bank_detail as t1")
                                    ->join("coop_payin as t2", "t1.ref2 = t2.ref_2", "INNER")
                                    ->where($where)
                                    ->get()->result_array();
                foreach($details as $detail) {
                    $share_val_setting = $this->db->select("*")->from("coop_share_setting")->get()->row_array();
                    $share_val = !empty($share_val_setting) && !empty($share_val['setting_value']) ? $share_val['setting_value'] : 10;
                    $prev_share = $this->db->select("share_collect_value, share_collect")->from("coop_mem_share")->where("member_id = '".$detail['member_id']."' AND share_status = '1'")->order_by("share_date DESC, share_id DESC")->get()->row_array();
                    $balance = $prev_share['share_collect_value'];
                    $collect_val = $balance + $detail['amount'];
                    $collect = $collect_val / $share_val;

                    //get receipt setting data
                    $receipt_format = 1;
                    $receipt_finance_setting = $this->db->select("*")->from("coop_setting_finance")->where("name = 'receipt_cashier_format' AND status = 1")->order_by("created_at DESC")->get()->row_array();
                    if(!empty($receipt_finance_setting)) {
                        $receipt_format = $receipt_finance_setting['value'];
                    }
                    if($receipt_format == 1) {
                        $yymm = (date("Y")+543).date("m");
                        $this->db->select('*');
                        $this->db->from("coop_receipt");
                        $this->db->where("receipt_id LIKE '".$yymm."%'");
                        $this->db->order_by("receipt_id DESC");
                        $this->db->limit(1);
                        $row = $this->db->get()->result_array();

                        if(!empty($row)) {
                            $id = (int) substr($row[0]["receipt_id"], 6);
                            $receipt_number = $yymm.sprintf("%06d", $id + 1);
                        }else {
                            $receipt_number = $yymm."000001";
                        }
                    } else {
                        $receipt_number = $this->Finance_libraries->generate_cashier_receipt_id($receipt_format, NULL);
                    }

                    $data_insert = array();
                    $data_insert['receipt_id'] = $receipt_number;
                    $data_insert['member_id'] = $detail['member_id'];
                    $data_insert['sumcount'] = $detail['amount'];
                    $data_insert['admin_id'] = $_SESSION['USER_ID'];
                    $data_insert['receipt_datetime'] = $timestamp;
                    $data_insert['receipt_status'] = '0';
                    $data_insert['pay_type'] = 1;
                    $this->db->insert('coop_receipt', $data_insert);

                    $data_insert = array();
                    $data_insert['receipt_id'] = $receipt_number;
                    $data_insert['receipt_list'] = '14';
                    $data_insert['receipt_count'] = $detail['amount'];
                    $data_insert['receipt_count_item'] = $detail['amount'] / $share_val;
                    $this->db->insert('coop_receipt_detail', $data_insert);

                    $data_insert = array();
                    $data_insert['member_id'] = $detail['member_id'];
                    $data_insert['receipt_id'] = $receipt_number;
                    $data_insert['account_list_id'] = '14';
                    $data_insert['principal_payment'] = number_format($detail['amount'],2,'.','');
                    $data_insert['interest'] = '0';
                    $data_insert['transaction_text'] = 'หุ้น';
                    $data_insert['deduct_type'] = 'all';
                    $data_insert['total_amount'] = number_format($detail['amount'],2,'.','');
                    $data_insert['payment_date'] = $timestamp;
                    $data_insert['createdatetime'] = $timestamp;
                    $this->db->insert('coop_finance_transaction', $data_insert);

                    $data_insert = array();
                    $data_insert['member_id'] = $detail['member_id'];
                    $data_insert['admin_id'] = $_SESSION['USER_ID'];
                    $data_insert['share_type'] = "SPA";
                    $data_insert['share_date'] = $timestamp;
                    $data_insert['share_status'] = '1';
                    $data_insert['share_payable'] = $prev_share['share_collect'];
                    $data_insert['share_payable_value'] = $prev_share['share_collect_value'];
                    $data_insert['share_early'] = $detail['amount'] / $share_val;
                    $data_insert['share_early_value'] = $detail['amount'];
                    $data_insert['share_collect'] = $collect;
                    $data_insert['share_collect_value'] = $collect_val;
                    $data_insert['share_bill'] = $receipt_number;
                    $data_insert['share_bill_date'] = $timestamp;
                    $data_insert['share_value'] = $share_val;
                    $data_insert['share_period'] = 1;
                    $data_insert['pay_type'] = 1;
                    $this->db->insert('coop_mem_share', $data_insert);
                    $share_id = $this->db->insert_id();

                    $data_update = array();
                    $data_update['coop_ref_type'] = 'share_id';
                    $data_update['coop_ref_data'] = $share_id;
                    $data_update['approved_date'] = $timestamp;
                    $data_update['status'] = 3;
                    $data_update['updated_at'] = $timestamp;
                    $this->db->where('id', $detail['id']);
                    $this->db->update('coop_payin_bank_detail', $data_update);

                    $data_update = array();
                    $data_update['share_status'] = 3;
                    $this->db->where('id', $detail['payin_bank_id']);
                    $this->db->update('coop_payin_bank', $data_update);
                }

                $result['status'] = 'success';
                $result['massage'] = 'ทำรายการสำเร็จ';
            } else if ($type == 2) {
                //Saving process
                $where = "t1.payin_bank_id = '".$id."' AND t1.type = '".$type."' AND t1.status = 1";
                $details = $this->db->select("t1.id, t1.payin_bank_id, t1.amount, t2.member_id, t2.deptaccount_no, t2.amount as payin_amount")
                                    ->from("coop_payin_bank_detail as t1")
                                    ->join("coop_payin as t2", "t1.ref2 = t2.ref_2", "INNER")
                                    ->where($where)
                                    ->get()->result_array();
                foreach($details as $detail) {
                    $prev_acc_transaction = $this->db->select("transaction_balance, transaction_no_in_balance")
                                                        ->from("coop_account_transaction")
                                                        ->where("account_id = '".$detail['deptaccount_no']."' AND (cancel_status IS NULL OR cancel_status = 0)")
                                                        ->order_by("transaction_time DESC, transaction_id DESC")
                                                        ->get()->row_array();
                    $data_insert = array();
                    $data_insert['transaction_time'] = $timestamp;
                    $data_insert['transaction_list'] = "XD";
                    $data_insert['transaction_withdrawal'] = '';
                    $data_insert['transaction_deposit'] = $detail['payin_amount'];
                    $data_insert['transaction_balance'] = $detail['payin_amount'] + $prev_acc_transaction['transaction_balance'];
                    $data_insert['transaction_no_in_balance'] = $detail['payin_amount'] + $prev_acc_transaction['transaction_no_in_balance'];
                    $data_insert['user_id'] = $_SESSION['USER_ID'];
                    $data_insert['account_id'] = $detail['deptaccount_no'];
                    $data_insert['createtime'] = date("Y-m-d H:i:s");
                    $this->db->insert('coop_account_transaction', $data_insert);
                    $transaction_id = $this->db->insert_id();

                    $bank_detail = $this->db->select("*")->from("coop_payin_bank_detail")->where("id = '".$detail['id']."'")->get()->row_array();
                    $coop_ref_data = !empty($bank_detail['coop_ref_data']) ? $bank_detail['coop_ref_data'].",".$transaction_id : $transaction_id;
                    $data_update = array();
                    $data_update['coop_ref_type'] = 'acc_tran_id';
                    $data_update['coop_ref_data'] = $coop_ref_data;
                    $data_update['approved_date'] = $timestamp;
                    $data_update['status'] = 3;
                    $data_update['updated_at'] = $timestamp;
                    $this->db->where('id', $detail['id']);
                    $this->db->update('coop_payin_bank_detail', $data_update);

                    $data_update = array();
                    $data_update['dept_status'] = 3;
                    $this->db->where('id', $detail['payin_bank_id']);
                    $this->db->update('coop_payin_bank', $data_update);
                }

                $result['status'] = 'success';
                $result['massage'] = 'ทำรายการสำเร็จ';
            }
        }
        return $result;
    }

    /*
        for get detail of imported bank payin.
        id :: need parameter.
        $type :: must be array if empty get all.
     */
    public function get_payin_bank_detail($id, $type) {
        $result = array();

        $where = "t1.status != 2";
        if(!empty($id)) {
            $where .= " AND t1.payin_bank_id = '".$id."'";
        }
        if(!empty($type) || $type == 0) {
            $where .= " AND t1.type = '".$type."'";
        }

        $payin = $this->db->select("*")->from("coop_payin_bank")->where("id = '".$id."'")->get()->row_array();
        $result = $payin;

        $details = $this->db->select("t1.id,
                                        t1.bank_code,
                                        t1.payment_date,
                                        t1.payment_time,
                                        t1.ref2,
                                        t1.status,
                                        t1.amount,
                                        t1.approved_date,
                                        t1.coop_ref_type as ref_type,
                                        t1.coop_ref_data as ref_data,
                                        t2.type,
                                        t2.member_id,
                                        t2.deptaccount_no,
                                        t2.loancontract_no,
                                        t3.contract_number,
                                        t4.firstname_th,
                                        t4.lastname_th,
                                        t5.prename_short")
                            ->from("coop_payin_bank_detail as t1")
                            ->join("coop_payin as t2", "t1.ref2 = t2.ref_2", "left")
                            ->join("coop_loan as t3", "t2.loancontract_no = t3.id", "left")
                            ->join("coop_mem_apply as t4", "t2.member_id = t4.member_id", "left")
                            ->join("coop_prename as t5", "t4.prename_id = t5.prename_id", "left")
                            ->where($where)
                            ->get()->result_array();
        $datas = array();
        foreach($details as $detail) {
            $data[$detail['id']]['id'] = $detail['id'];
            $data[$detail['id']]['bank_code'] = $detail['bank_code'];
            $data[$detail['id']]['payment_date'] = $detail['payment_date'];
            $data[$detail['id']]['payment_time'] = $detail['payment_time'];
            $data[$detail['id']]['ref2'] = $detail['ref2'];
            $data[$detail['id']]['amount'] = $detail['amount'];
            $data[$detail['id']]['status'] = $detail['status'];
            $data[$detail['id']]['approved_date'] = $detail['approved_date'];
            $data[$detail['id']]['ref_type'] = $detail['ref_type'];
            $data[$detail['id']]['ref_data'] = $detail['ref_data'];
            $data[$detail['id']]['type'] = $detail['type'];
            $data[$detail['id']]['member_id'] = $detail['member_id'];
            $data[$detail['id']]['firstname_th'] = $detail['firstname_th'];
            $data[$detail['id']]['lastname_th'] = $detail['lastname_th'];
            $data[$detail['id']]['prename_short'] = $detail['prename_short'];
            if(!empty($detail['contract_number'])) $data[$detail['id']]['contract_number'][] = $detail['contract_number'];
            if(!empty($detail['loancontract_no'])) $data[$detail['id']]['loancontract_no'][] = $detail['loancontract_no'];
            if(!empty($detail['deptaccount_no'])) $data[$detail['id']]['deptaccount_no'][] = $detail['deptaccount_no'];
        }
        $result['data'] = $data;

        return $result;
    }
}