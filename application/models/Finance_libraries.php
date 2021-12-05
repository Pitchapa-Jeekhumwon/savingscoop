<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Finance_libraries extends CI_Model {
	public function __construct() {
		parent::__construct();
    }

    public function generate_cashier_receipt_id($receipt_format, $date) {
        if(empty($date)) {
            $date = date('Y-m-d H:i:s');
        }

        $mm = date('m',strtotime($date));
        $yy = (date('Y',strtotime($date)) + 543);
        $yymm = $receipt_format == 2 || $receipt_format == 3 ? $yy : $yy . $mm;
        $this->db->select('*');
        $this->db->from('coop_receipt');
        if($receipt_format == 3) {
            $this->db->where("receipt_id LIKE '" . $yy . "02%'");
        } else {
            $this->db->where("receipt_id LIKE '" . $yymm . "%'");
        }
        $this->db->order_by("receipt_id DESC");
        $this->db->limit(1);
        $row = $this->db->get()->result_array();
        if($receipt_format == 2) {
            if (!empty($row)) {
                $id = (int)substr($row[0]["receipt_id"], 4);
                $receipt_number = $yymm . sprintf("%07d", $id + 1);
            } else {
                $receipt_number = $yymm . "0000001";
            }
        } else if($receipt_format == 3) {
            if (!empty($row)) {
                $id = (int)substr($row[0]["receipt_id"], -5);
                $receipt_number = $yymm . "02" . sprintf("%05d", $id + 1);
            } else {
                $receipt_number = $yymm . "0200001";
            }
        } else {
            if (!empty($row)) {
                $id = (int)substr($row[0]["receipt_id"], 6);
                $receipt_number = $yymm . sprintf("%06d", $id + 1);
            } else {
                $receipt_number = $yymm . "000001";
            }
        }

        return $receipt_number;
    }

    public function generate_non_pay_receipt_id($receipt_format, $date, $non_pay_month, $non_pay_year) {
        if(empty($date)) {
            $date = date('Y-m-d H:i:s');
        }

        if($receipt_format == 1) {		
            $yymm_check = (date('Y',strtotime($date))+543).date('m',strtotime($date));
            $yymm_check = date('m',strtotime($date));
            $yy_check = (date('Y',strtotime($date))+543);

            $yymm = sprintf("%02d", $non_pay_month);
            $yy_full = (date('Y',strtotime($date))+543);
            $yy = substr($non_pay_year,2);
            $text = 'C';
            $this->db->select('*');
            $this->db->from('coop_receipt');
            $this->db->where("receipt_id LIKE '".'%__C__'.$yy_check."%'");
            $this->db->order_by("order_by DESC");
            $this->db->limit(1);
            $row = $this->db->get()->result_array();
            if(!empty($row)) {
                $id = (int) substr($row[0]["receipt_id"], 9);
                $receipt_number = $yymm.''.$text.''.$yy.$yy_check.sprintf("%06d", $id + 1);
            } else {
                $receipt_number = $yymm.''.$text.''.$yy.$yy_check."000001";
            }
        } else {
            $mm = date('m',strtotime($date));
            $yy = (date('Y',strtotime($date)) + 543);
            $yymm = $receipt_format == 2 || $receipt_format == 3 ? $yy : $yy . $mm;
            $this->db->select('*');
            $this->db->from('coop_receipt');
            if($receipt_format == 3) {
                $this->db->where("receipt_id LIKE '" . $yy . "02%'");
            } else {
                $this->db->where("receipt_id LIKE '" . $yymm . "%'");
            }
            $this->db->order_by("receipt_id DESC");
            $this->db->limit(1);
            $row = $this->db->get()->result_array();
            if($receipt_format == 2) {
                if (!empty($row)) {
                    $id = (int)substr($row[0]["receipt_id"], 4);
                    $receipt_number = $yymm . sprintf("%07d", $id + 1);
                } else {
                    $receipt_number = $yymm . "0000001";
                }
            } else if($receipt_format == 3) {
                if (!empty($row)) {
                    $id = (int)substr($row[0]["receipt_id"], -5);
                    $receipt_number = $yymm . "02" . sprintf("%05d", $id + 1);
                } else {
                    $receipt_number = $yymm . "0200001";
                }
            } else {
                if (!empty($row)) {
                    $id = (int)substr($row[0]["receipt_id"], 6);
                    $receipt_number = $yymm . sprintf("%06d", $id + 1);
                } else {
                    $receipt_number = $yymm . "000001";
                }
            }
        }

        return $receipt_number;
    }

    public function generate_finance_month_receipt_id($receipt_format, $text, $date) {
        if(empty($date)) {
            $date = date('Y-m-d H:i:s');
        }

        if($receipt_format == 1) {
            $yymm = date("m", strtotime($date));
            $yy = (date("Y", strtotime($date))+543);
            $yy_full = (date("Y", strtotime($date))+543);
            $yy = substr($yy,2);

            if(empty($text)) {
                $text = 'B';
            }

            $this->db->select(array('*'));
            $this->db->from('coop_receipt');
            $this->db->where("receipt_id LIKE '".$yymm.'B'.$yy."%' OR receipt_id LIKE '".$yymm.'F'.$yy."%'");
            $this->db->order_by("order_by DESC");
            $this->db->limit(1);

            $row_receipt = $this->db->get()->result_array();
            $row_receipt = @$row_receipt[0];

            if($row_receipt['receipt_id'] != '') {
                $id = (int) substr($row_receipt["receipt_id"], 6);
                $receipt_number = $yymm.''.$text.''.$yy.sprintf("%06d", $id + 1);
            }else {
                $receipt_number = $yymm.''.$text.''.$yy."000001";
            }

            $order_by_id =  $row_receipt["order_by"]+1 ;

			$sql = "SELECT receipt_id
					FROM coop_receipt
					WHERE receipt_id = '".$receipt_number."'";
			$rs_chk_receipt = $this->db->query($sql);
			if($rs_chk_receipt->num_rows() == 1){
				$this->db->select(array('*'));
				$this->db->from('coop_receipt');
				$this->db->where("receipt_id LIKE '".$yymm.'B'.$yy."%' OR receipt_id LIKE '".$yymm.'F'.$yy."%'");
				$this->db->order_by("order_by DESC");
				$this->db->limit(1);
				$row_receipt = $this->db->get()->result_array();
				$row_receipt = @$row_receipt[0];
				if($row_receipt['receipt_id'] != '') {
					$id = (int) substr($row_receipt["receipt_id"], 6);
					$receipt_number = $yymm.''.$text.''.$yy.sprintf("%06d", $id + 1);
				}else {
					$receipt_number = $yymm.''.$text.''.$yy."000001";
				}
				$order_by_id =  $row_receipt["order_by"]+1 ;
			}
        } else {
            $mm = date('m',strtotime($date));
            $yy = (date('Y',strtotime($date)) + 543);
            $yymm = $receipt_format == 2 || $receipt_format == 3 ? $yy : $yy . $mm;
            $this->db->select('*');
            $this->db->from('coop_receipt');
            if($receipt_format == 3) {
                $this->db->where("receipt_id LIKE '" . $yy . "01%'");
            } else {
                $this->db->where("receipt_id LIKE '" . $yymm . "%'");
            }
            $this->db->order_by("receipt_id DESC");
            $this->db->limit(1);
            $row = $this->db->get()->result_array();
            if($receipt_format == 2) {
                if (!empty($row)) {
                    $id = (int)substr($row[0]["receipt_id"], 4);
                    $receipt_number = $yymm . sprintf("%07d", $id + 1);
                } else {
                    $receipt_number = $yymm . "0000001";
                }
            } else if($receipt_format == 3) {
                if (!empty($row)) {
                    $id = (int)substr($row[0]["receipt_id"],-5);
                    $receipt_number = $yymm . "01" . sprintf("%05d", $id + 1);
                } else {
                    $receipt_number = $yymm . "0119657";//For bbcoop please remove after run financemonth 2020-10.
                }
            } else {
                if (!empty($row)) {
                    $id = (int)substr($row[0]["receipt_id"], 6);
                    $receipt_number = $yymm . sprintf("%06d", $id + 1);
                } else {
                    $receipt_number = $yymm . "000001";
                }
            }
        }

        return $receipt_number;
    }

    public function check_receipt_id($receipt_id) {
        $result = array();
        $receipt = $this->db->select("receipt_status, receipt_id")->from("coop_receipt")->where("receipt_id = '".$receipt_id."'")->get()->row_array();
        if(!empty($receipt)) {
            $result['receipt_id'] = $receipt['receipt_id'];
            $result['status'] = empty($receipt['receipt_status']) ? 0 : $receipt['receipt_status'];
            $result['status_name'] = empty($receipt['receipt_status']) ? "ปกติ" : ($receipt['receipt_status'] == 1 ? "รอการยืนยัน" : "ถูกยกเลิกแล้ว");
            $result['encode_receipt_id'] = jwt::urlsafeB64Encode($this->center_function->encrypt_text($receipt['receipt_id']));
        } else {
            $result['receipt_id'] = NULL;
        }

        return $result;
    }
    public function check_contract_number($contract_number,$member_id) {
        $result = array();
        $contract_number = $this->db->select("contract_number")->from("coop_loan")->where("contract_number = '".$contract_number."' AND member_id = '".str_pad($member_id, 6, 0, STR_PAD_LEFT) ."'")->get()->row_array();

        if(!empty($contract_number)) {
            $result['contract_number'] = $contract_number['contract_number'];
        } else{
            $result['contract_number'] = NULL;
        }
        return $result;
    }
    public function check_contract_atm_number($contract_number,$member_id) {
        $result = array();
        $contract_number = $this->db->select("contract_number")->from("coop_loan_atm")->where("contract_number = '".$contract_number."'  AND member_id = '".str_pad($member_id, 6, 0, STR_PAD_LEFT) ."'")->get()->row_array();

        if(!empty($contract_number)) {
            $result['contract_number'] = $contract_number['contract_number'];
        } else{
            $result['contract_number'] = NULL;
        }
        return $result;
    }
    public function check_petition_number($petition_number,$member_id) {
        $result = array();
        $petition_number = $this->db->select("petition_number")->from("coop_loan")->where("petition_number = '".$petition_number."'  AND member_id = '".str_pad($member_id, 6, 0, STR_PAD_LEFT) ."'")->get()->row_array();
        if(!empty($petition_number)) {
            $result['petition_number'] = $petition_number['petition_number'];
        } else{
            $result['petition_number'] = NULL;
        }
        return $result;
    }
    public function check_petition_atm_number($petition_number,$member_id) {
        $result = array();
        $petition_number = $this->db->select("petition_number")->from("coop_loan_atm")->where("petition_number = '".$petition_number."'  AND member_id = '".str_pad($member_id, 6, 0, STR_PAD_LEFT) ."'")->get()->row_array();
        if(!empty($petition_number)) {
            $result['petition_number'] = $petition_number['petition_number'];
        } else{
            $result['petition_number'] = NULL;
        }
        return $result;
    }
    public function change_contract_number($contract_number, $new_contract_number,$member_id) {
        $data_insert = array();
        $id = $this->db->select('id')->from('coop_loan')
            ->where("contract_number = '".$contract_number."' AND member_id = '".str_pad($member_id, 6, 0, STR_PAD_LEFT) ."'")
            ->get()->row_array();
        $data_insert['contract_number'] = $new_contract_number;
        $this->db->where("id = '".$id['id']."'");
        $this->db->update('coop_loan', $data_insert);

        $result['status'] = 1;
        $result['message'] = "ทำรายการสำเร็จ";
        return $result;
    }
    public function change_contract_atm_number($contract_number, $new_contract_number,$member_id) {
        $data_insert = array();
        $id = $this->db->select('loan_atm_id')->from('coop_loan_atm')
            ->where("contract_number = '".$contract_number."' AND member_id = '".str_pad($member_id, 6, 0, STR_PAD_LEFT) ."'")
            ->get()->row_array();
        $data_insert['contract_number'] = $new_contract_number;
        $this->db->where("loan_atm_id = '".$id['loan_atm_id']."'");
        $this->db->update('coop_loan_atm', $data_insert);

        $result['status'] = 1;
        $result['message'] = "ทำรายการสำเร็จ";
        return $result;
    }
    public function change_petition_number($petition_number, $new_petition_number,$member_id) {
        $data_insert = array();
        $id = $this->db->select('id')->from('coop_loan')
            ->where("petition_number = '".$petition_number."' AND member_id = '".str_pad($member_id, 6, 0, STR_PAD_LEFT) ."'")
            ->get()->row_array();
        $data_insert['petition_number'] = $new_petition_number;
        $this->db->where("id = '".$id['id']."'");
        $this->db->update('coop_loan', $data_insert);

        $result['status'] = 1;
        $result['message'] = "ทำรายการสำเร็จ";
        return $result;
    }
    public function change_petition_atm_number($petition_number, $new_petition_number,$member_id) {
        $data_insert = array();
        $id = $this->db->select('loan_atm_id')->from('coop_loan_atm')
            ->where("petition_number = '".$petition_number."' AND member_id = '".str_pad($member_id, 6, 0, STR_PAD_LEFT) ."'")
            ->get()->row_array();
        $data_insert['petition_number'] = $new_petition_number;
        $this->db->where("loan_atm_id = '".$id['id']."'");
        $this->db->update('coop_loan_atm', $data_insert);

        $result['status'] = 1;
        $result['message'] = "ทำรายการสำเร็จ";
        return $result;
    }

    public function change_receipt_id($receipt_id, $new_receipt_id) {
        $result = array();
        $check_receipt_id = $this->db->select("receipt_status, receipt_id")->from("coop_receipt")->where("receipt_id = '".$receipt_id."'")->get()->row_array();
        $check_new_receipt_id = $this->db->select("receipt_id")->from("coop_receipt")->where("receipt_id = '".$new_receipt_id."'")->get()->row_array();
        if (empty($check_receipt_id)) {
            $result['status'] = 0;
            $result['message'] = "ไม่พบใบเสร็จเดิมในระบบ";
        } else if(!empty($check_new_receipt_id)) {
            $result['status'] = 0;
            $result['message'] = "ไม่สามารถเปลี่ยนเป็นเลขที่ใบเสร็จที่มีอยู่ในระบบได้";
        } else {
            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_receipt', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_receipt_detail', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_finance_transaction', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_loan_transaction', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_loan_atm_transaction', $data_insert);

            $data_insert = array();
            $data_insert['share_bill'] = $new_receipt_id;
            $this->db->where('share_bill', $receipt_id);
            $this->db->update('coop_mem_share', $data_insert);

            $data_insert = array();
            $data_insert['deduct_receipt_id'] = $new_receipt_id;
            $this->db->where('deduct_receipt_id', $receipt_id);
            $this->db->update('coop_loan', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_process_return', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_dividend_average_receipt', $data_insert);

            $data_insert = array();
            $data_insert['ref_id'] = $new_receipt_id;
            $this->db->where('ref_id', $receipt_id);
            $this->db->update('coop_account', $data_insert);

            $data_insert = array();
            $data_insert['old_receipt_id'] = $receipt_id;
            $data_insert['new_receipt_id'] = $new_receipt_id;
            $data_insert['user_id'] = $_SESSION['USER_ID'];
            $data_insert['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('coop_receipt_id_change_history', $data_insert);

            $result['status'] = 1;
            $result['message'] = "ทำรายการสำเร็จ";
            $result['encode_receipt_id'] = jwt::urlsafeB64Encode($this->center_function->encrypt_text($new_receipt_id));
        }
        return $result;
    }

    public function generate_loan_receipt_text_cashier($loan_id, $account_list_id) {
        $result = "";
        //Get receipt format setting.
        $format_setting = $this->db->select("value")->from("coop_setting_finance")->where("name = 'cashier_receipt_loan_text' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        $format_type = !empty($format_setting) ? $format_setting['value'] : 1; // Default 1.

        if($format_type == 2) {
            // รูปแบบสำนักงบ ex [coop_loan_name.type_name] [coop_loan.contract_number]
            $loan = $this->db->select("t1.contract_number, t2.loan_name")
                                ->from("coop_loan as t1")
                                ->join("coop_loan_name as t2", "t1.loan_type = t2.loan_name_id", "left")
                                ->where("t1.id = '".$loan_id."'")
                                ->get()->row_array();
            $result = $loan['loan_name']." ".$loan['contract_number'];
        } else {
            //For format_type == 1 or empty.
            //รูปแบบตั้งต้น ex. [coop_account_list.account_list]เลขที่สัญญา [coop_loan.contract_number]
            $loan = $this->db->select("contract_number")->from("coop_loan")->where("id = '".$loan_id."'")->get()->row_array();
            $account_list = $this->db->select("account_list")->from("coop_account_list")->where("account_id = '".$account_list_id."'")->get()->row_array();
            $result = $account_list['account_list']."เลขที่สัญญา ".$loan['contract_number'];
        }

        return $result;
    }

    public function generate_loan_receipt_text_finance_month($loan_id, $account_list_id, $deduct_type) {
        $result = "";
        //Get receipt format setting.
        $format_setting = $this->db->select("value")->from("coop_setting_finance")->where("name = 'cashier_receipt_loan_text' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        $format_type = !empty($format_setting) ? $format_setting['value'] : 1; // Default 1.

        if($format_type == 2) {
            // รูปแบบสำนักงบ ex [coop_loan_name.type_name] [coop_loan.contract_number]
            $loan = $this->db->select("t1.contract_number, t2.loan_name")
                                ->from("coop_loan as t1")
                                ->join("coop_loan_name as t2", "t1.loan_type = t2.loan_name_id", "left")
                                ->where("t1.id = '".$loan_id."'")
                                ->get()->row_array();
            $result = $loan['loan_name']." ".$loan['contract_number'];
        } else {
            //For format_type == 1 or empty.
            //รูปแบบตั้งต้น ex. [coop_account_list.account_list]เลขที่สัญญา [coop_loan.contract_number]
            $loan = $this->db->select("contract_number")->from("coop_loan")->where("id = '".$loan_id."'")->get()->row_array();
            $result = $deduct_type == "interest" ? "ดอกเบี้ยเงินกู้เลขที่สัญญา ".$loan['contract_number'] : "ต้นเงินกู้เลขที่สัญญา ".$loan['contract_number'];
        }

        return $result;
    }

    public function generate_loan_atm_receipt_text_finance_month($loan_atm_id, $account_list_id, $deduct_type) {
        $result = "";
        //Get receipt format setting.
        $format_setting = $this->db->select("value")->from("coop_setting_finance")->where("name = 'cashier_receipt_loan_text' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        $format_type = !empty($format_setting) ? $format_setting['value'] : 1; // Default 1.

        if($format_type == 2) {
            // รูปแบบสำนักงบ ex [coop_loan_name.type_name] [coop_loan.contract_number]
            $loan = $this->db->select("t1.contract_number, 'เงินกู้ฉุกเฉิน ATM' as loan_name")
                ->from("coop_loan_atm as t1")
                ->where("t1.loan_atm_id = '".$loan_atm_id."'")
                ->get()->row_array();
            $result = $loan['loan_name']." ".$loan['contract_number'];
        } else {
            //For format_type == 1 or empty.
            //รูปแบบตั้งต้น ex. [coop_account_list.account_list]เลขที่สัญญา [coop_loan.contract_number]
            $loan = $this->db->select("contract_number")->from("coop_loan_atm")->where("loan_atm_id = '".$loan_atm_id."'")->get()->row_array();
            $result = "เงินกู้เลขที่สัญญา ".$loan['contract_number'];
        }

        return $result;
    }

    public function generate_loan_receipt_text_deduct($loan_id) {
        $result = "";
        //Get receipt format setting.
        $format_setting = $this->db->select("value")->from("coop_setting_finance")->where("name = 'cashier_receipt_loan_text' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        $format_type = !empty($format_setting) ? $format_setting['value'] : 1; // Default 1.

        if($format_type == 2) {
            // รูปแบบสำนักงบ ex [coop_loan_name.type_name] [coop_loan.contract_number]
            $loan = $this->db->select("t1.contract_number, t2.loan_name")
                                ->from("coop_loan as t1")
                                ->join("coop_loan_name as t2", "t1.loan_type = t2.loan_name_id", "left")
                                ->where("t1.id = '".$loan_id."'")
                                ->get()->row_array();
            $result = $loan['loan_name']." ".$loan['contract_number'];
        } else {
            //For format_type == 1 or empty.
            //รูปแบบตั้งต้น ex. [coop_account_list.account_list]เลขที่สัญญา [coop_loan.contract_number]
            $loan = $this->db->select("contract_number")->from("coop_loan")->where("id = '".$loan_id."'")->get()->row_array();
            $result = 'หักกลบเงินกู้เลขที่สัญญา '.$loan['contract_number'];
        }

        return $result;
    }

    public function get_finance_month_data($month, $year, $member_id, $level, $faction, $department, $run_status) {
        $result = array();

        $profile_month = $this->db->select("profile_id")->from('coop_finance_month_profile')->where('profile_month = '.$month." AND profile_year = ".$year)->get()->row_array();

        if(!empty($profile_month) && !empty($profile_month['profile_id'])) {
            $profile_id = $profile_month['profile_id'];
            $where = "t1.profile_id = ".$profile_id." AND t1.run_status = ".$run_status;
            if(!empty($member_id)) {
                $where .= " AND t1.member_id = '".$member_id."'";
            } else if (!empty($level)) {
                $where .= " AND t1.level = '".$level."'";
            } else if (!empty($faction)) {
                $where .= " AND t1.faction = '".$faction."'";
            } else if (!empty($department)) {
                $where .= " AND t1.department = '".$department."'";
            }

            $finance_months = $this->db->select('t1.run_id,
                                                t1.member_id,
                                                t1.deduct_code,
                                                t1.pay_amount,
                                                t1.pay_type,
                                                t1.loan_id,
                                                t1.deduct_id,
                                                t1.deposit_account_id,
                                                t2.petition_number,
                                                t2.contract_number,
                                                t3.firstname_th,
                                                t3.lastname_th,
                                                t4.prename_short,
                                                t5.deduct_detail')
                                        ->from("coop_finance_month_detail as t1")
                                        ->join("coop_loan as t2", "t1.loan_id = t2.id", "left")
                                        ->join("coop_mem_apply as t3", "t1.member_id = t3.member_id", "left")
                                        ->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
                                        ->join("coop_deduct as t5", "t1.deduct_Id = t5.deduct_id", "left")
                                        ->where($where)
                                        ->order_by('t1.member_id', 't1.run_id', "left")
                                        ->get()->result_array();
            $result = $finance_months;
        }

        return $result;
    }

    public function edit_finance_month($id, $amount) {
        $result = array();
        $finance_month = $this->db->select('*')->from("coop_finance_month_detail")->where('run_id = '.$id)->get()->row_array();

        //Not allow to update if already run finance month( status = 1).
        if(!empty($finance_month) && $finance_month['run_status'] == 0) {
            $finance_month['edit_by'] = $_SESSION['USER_ID'];
            $finance_month['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('coop_finance_month_detail_history', $finance_month);

            unset($finance_month['edit_by']);
            unset($finance_month['created_at']);
            $finance_month['pay_amount'] = $amount;
            $finance_month['real_pay_amount'] = $amount;
            $this->db->where("run_id = '".$id."'");
            $this->db->update('coop_finance_month_detail', $finance_month);
            $result['status'] = true;
        } else {
            $result['status'] = false;
            $result['message'] = 'ไม่พบข้อมูลหรือไม่สามารถแก้ไขข้อมูลที่ผ่านรายการแล้วได้';
        }

        return $result;
    }

    public function delete_finance_month($id) {
        $result = array();
        $finance_month = $this->db->select('*')->from("coop_finance_month_detail")->where('run_id = '.$id)->get()->row_array();

        //Not allow to update if already run finance month( status = 1).
        if(!empty($finance_month) && $finance_month['run_status'] == 0) {
            $finance_month['edit_by'] = $_SESSION['USER_ID'];
            $finance_month['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('coop_finance_month_detail_history', $finance_month);

            if(!empty($finance_month['loan_id'])) {
                $fin_m_b_cal_int = $this->db->select("id")->from("coop_setting_finance")->where("name = 'fin_m_b_cal_int' AND value = 2 AND status = 1")->order_by("created_at DESC")->get()->row_array();

                if(!empty($fin_m_b_cal_int)) {
                    $this->db->query("DELETE FROM coop_finance_month_detail WHERE profile_id = '".$finance_month['profile_id']."' AND loan_id = '".$finance_month['loan_id']."'");
                } else {
                    $this->db->query("DELETE FROM coop_finance_month_detail WHERE run_id = '".$id."'");
                }
            } else {
                $this->db->query("DELETE FROM coop_finance_month_detail WHERE run_id = '".$id."'");
            }
            $result['status'] = true;
        } else {
            $result['status'] = false;
            $result['message'] = 'ไม่พบข้อมูลหรือไม่สามารถแก้ไขข้อมูลที่ผ่านรายการแล้วได้';
        }

        return $result;
    }
}