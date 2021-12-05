<?php
class Loan_save_model extends Contract_modal
{

    public function __construct()
    {
		parent::__construct();
		$this->load->model("Finance_libraries", "Finance_libraries");
    }
	
    public function get_loan_save($data_post,$data){
		$arr_data = array();
		$member_id = $data_post['data']['coop_loan']['member_id'];
		$loan_amount = $data_post['data']['coop_loan']['loan_amount'];
		$check_loan_save = $this->loan_save($data_post);
		//echo '<pre>'; print_r($check_loan_save); echo '</pre>'; //exit;
		if($check_loan_save['status'] == 'success'){
			$arr_data_approve = array();
			$loan_id = $check_loan_save['loan_id'];
			$arr_data_approve['loan_id'] = $check_loan_save['loan_id'];
			$arr_data_approve['status_to'] = 1;
			$arr_data_approve['date_approve'] = $this->center_function->mydate2date(date('Y-m-d'));
			$check_loan_approve_save = $this->loan_approve_save($arr_data_approve);
			if($check_loan_approve_save['status'] == 'success'){
				$main_account = $this->getMainAccount($member_id);
				$amount_transfer = $this->getAmountTransfer($loan_id);
				
				$arr_data_transfer = array();
				$arr_data_transfer['loan_id'] = $loan_id;
				$arr_data_transfer['time_transfer'] = date('H:i:s');
				$arr_data_transfer['date_transfer'] = $this->center_function->mydate2date(date('Y-m-d'));
				$arr_data_transfer['loan_amount'] = $loan_amount;
				$arr_data_transfer['amount_transfer'] = $amount_transfer;
				$arr_data_transfer['pay_type'] = '1';
				$arr_data_transfer['account_id'] = $main_account['account_id'];
				$check_loan_transfer = $this->loan_transfer_save($arr_data_transfer,$data);
				if($check_loan_transfer['status'] == 'success'){
					$data_arr['account_id'] = $main_account['account_id'];
					$data_arr['status'] = 'success';
					$data_arr['msg'] = 'เรียบร้อยแล้ว';
				}else{
					$data_arr['status'] = 'error';
					$data_arr['msg'] = 'บันทึกข้อมูลไม่สำเร็จ';
				}
			}else{
				$data_arr['status'] = 'error';
				$data_arr['msg'] = 'บันทึกข้อมูลไม่สำเร็จ';
			}	
		}else{
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'บันทึกข้อมูลไม่สำเร็จ';
		}
		return $data_arr;
	}
	
	//บันทึกคำขอ/สัญญาเงินกู้ 
    public function loan_save($data_post){
		//echo '<pre>'; print_r($data_post); //exit;
		$affected_rows = 0 ;
		
        $arr_createdatetime = explode('/',@$data_post['createdatetime']);
        $data_createdatetime = ($arr_createdatetime[2]-543)."-".$arr_createdatetime[1]."-".$arr_createdatetime[0];
        $createdatetime = (@$data_post['createdatetime'] != '')?@$data_createdatetime." ".date('H:i:s'):date('Y-m-d H:i:s');

        if(isset($data_post['data']['coop_loan'])){
            $data_post['data']['coop_loan'] = $this->calc_period($data_post['data']['coop_loan']);
            $data_post['data']['coop_loan_period'] = $data_post['data']['coop_loan']['coop_loan_period'];
        }

        if(@$data_post['loan_id']==''){
            $data_insert = array();
            $data_insert['admin_id'] = @$_SESSION['USER_ID'];
            $data_insert['createdatetime'] = @$createdatetime;
            $data_insert['updatetimestamp'] = date('Y-m-d H:i:s');
            //$data_insert['contract_number'] = @$new_contact_number;
            $data_insert['contract_number'] = '';

            foreach(@$data_post['data']['coop_loan'] as $key => $value){
				if($key == 'createdatetime'){
                    if(!empty($value)){
                        $value = $createdatetime;
                    }
                }
				
                if($key == 'date_period_1' || $key == 'date_period_2'){
                    if(!empty($value)){
                        $date_arr = explode('/',$value);
                        $value = ($date_arr[2])."-".$date_arr[1]."-".$date_arr[0];
                    }
                }
                if($key == 'loan_amount' || $key == 'money_period_1' || $key == 'money_period_2' || $key == 'salary'){
                    $value = str_replace(',','',@$value);
                }

                //generate petition number
                if($key == 'petition_number'){
                    $value  =  $this->generatePetitionNumber($data_post['data']['coop_loan']['loan_type']);
                }

                if($key != 'summonth_period_1' && $key != 'summonth_period_2'){
                    $data_insert[$key] = @$value;
					//echo $key.'='.$value.'<br>';
                }

                if($key == 'loan_amount'){
                    $data_insert['loan_amount_balance'] = @$value;
                }
                $data_insert['loan_status'] = '0';

                if(in_array($key, array('coop_loan_period', 'date_receive_money', 'first_interest',
                    'last_period', 'total_loan_pri', 'max_period', 'interest_current_value'))){
                    unset($data_insert[$key]);
                }
            }

            //echo "<pre>"; print_r($data_insert); exit;
            //add
            $this->db->insert('coop_loan', $data_insert);
            $loan_id = $this->db->insert_id();
			if($this->db->affected_rows()){
				$affected_rows++;
			}	

            if(isset($data_post['data']['coop_loan_guarantee']) && sizeof($data_post['data']['coop_loan_guarantee']) >= 1) {
                foreach (@$data_post['data']['coop_loan_guarantee'] as $key => $value) {
                    if($value['type'] == 1){
                        $this->personalGuarantee($loan_id, $value);
                    }else if($value['type'] == 2) {
                        $this->shareGuarantee($loan_id, $value);
                    }else if($value['type'] == 3){
                        $this->depositGuarantee($loan_id, $value);
                    }else if($value['type'] == 4){
                        $this->realEstateGuarantee($loan_id, $value);
                    }

                }
            }
            //echo "_________________________<br>";
            foreach(@$data_post['data']['coop_loan_period'] as $key => $value){
                $data_insert = array();
                $data_insert['loan_id'] = @$loan_id;
                foreach($value as $key2 => $value2){
                    //$sql .= " ".$key2." = '".$value2."',";
                    $data_insert[$key2] = @$value2;
                }
                //add coop_loan_period
                $this->db->insert('coop_loan_period', $data_insert);
            }

        }else{

            $data_insert = array();
            $data_insert['admin_id'] = @$_SESSION['USER_ID'];
            $data_insert['createdatetime'] = @$createdatetime." ".date("H:i:s");
            if(@$data_post['updatetimestamp'] != '') {
                $data_insert['updatetimestamp'] = @$data_post['updatetimestamp'];
            }

            foreach(@$data_post['data']['coop_loan'] as $key => $value){
                if($key == 'date_period_1' || $key == 'date_period_2'){
                    if(!empty($value)){
                        $date_arr = explode('/',$value);
                        $value = ($date_arr[2])."-".$date_arr[1]."-".$date_arr[0];
                    }
                }

                if($key == 'loan_amount' || $key == 'money_period_1' || $key == 'money_period_2' || $key == 'salary'){
                    $value = str_replace(',','',@$value);
                }

                //generate petition number
                if($key == 'petition_number'){
                    $value  =  $this->generatePetitionNumber($data_post['data']['coop_loan']['loan_type']);
                }

                if($key != 'summonth_period_1' && $key != 'summonth_period_2'){
                    $data_insert[$key] = @$value;
                }

                if($key == 'loan_amount'){
                    $data_insert['loan_amount_balance'] = @$value;
                }
                $data_insert['loan_status'] = '0';

                if(in_array($key, array('coop_loan_period', 'date_receive_money', 'first_interest',
                    'last_period', 'total_loan_pri', 'max_period', 'interest_current_value'))){
                    unset($data_insert[$key]);
                }
            }

            //edit coop_loan
            $this->db->where('id', @$data_post['loan_id']);
            $this->db->update('coop_loan', $data_insert);
            $loan_id = @$data_post['loan_id'];

            $this->db->where("loan_id", $loan_id );
            $this->db->delete("coop_loan_guarantee");

            $this->db->where("loan_id", $loan_id );
            $this->db->delete("coop_loan_guarantee_person");

            $this->db->where("loan_id", $loan_id );
            $this->db->delete("coop_loan_guarantee_real_estate");

            if(isset($data_post['data']['coop_loan_guarantee']) && sizeof($data_post['data']['coop_loan_guarantee']) >= 1) {
                foreach (@$data_post['data']['coop_loan_guarantee'] as $key => $value) {
                    if($value['type'] == 1){
                        $this->personalGuarantee($loan_id, $value);
                    }else if($value['type'] == 2){
                        $this->shareGuarantee($loan_id, $value);
                    }else if($value['type'] == 3){
                        $this->depositGuarantee($loan_id, $value);
                    }else if($value['type'] == 4){
                        $this->realEstateGuarantee($loan_id, $value);
                    }

                }
            }

            $this->db->where("loan_id", $loan_id );
            $this->db->delete("coop_loan_period");
            foreach(@$data_post['data']['coop_loan_period'] as $key => $value){
                $data_insert = array();
                $data_insert['loan_id'] = @$loan_id;
                foreach($value as $key2 => $value2){
                    $data_insert[$key2] = @$value2;
                }
                //add coop_loan_period
                $this->db->insert('coop_loan_period', $data_insert);
            }
        }

        // Loan Cost
        $this->db->where("loan_id", $loan_id );
        $this->db->delete("coop_loan_cost_mod");
        $data_insert = array();
        if(@$data_post['data']['coop_loan_cost']) {
            $index = 0;
            foreach (@$data_post['data']['coop_loan_cost'] as $key => $val){
                $data_insert[$index]['loan_id'] = @$loan_id;
                $data_insert[$index]['member_id'] = @$data_post['data']['coop_loan']['member_id'];
                $data_insert[$index]['loan_cost_code'] = $key;
                $data_insert[$index]['loan_cost_amount'] = str_replace(',','',$val);
                $index++;
            }
            unset($index);
            $this->db->insert_batch('coop_loan_cost_mod', $data_insert);
        }

        //Loan Deduct
        //clear deduct profile
        $this->db->where("loan_id", $loan_id )->delete("coop_loan_deduct_profile");

        //clear deduct
        $this->db->where("loan_id", $loan_id )->delete("coop_loan_deduct");

        //clear loan deduct status
        $this->db->where("id", $loan_id );
        $this->db->update("coop_loan", array('deduct_status'=>'0'));

        //add deduct profile
        $data_insert = array();
        $data_insert['loan_id'] = @$loan_id;
        $data_insert['estimate_receive_money'] = str_replace(',', '', $data_post['data']['loan_deduct_profile']['estimate_receive_money']);
        $data_insert['pay_per_month'] = (@$data_post['data']['coop_loan']['summonth_period_2'] == '31') ? str_replace(',', '', $data_post['data']['coop_loan']['money_period_2']) : str_replace(',', '', $data_post['data']['coop_loan']['money_period_1']);
 
		if(trim(@$data_post['data']['loan_deduct_profile']['date_receive_money'])!=''){
			$date_receive_money = explode('/', @$data_post['data']['loan_deduct_profile']['date_receive_money']);
			$date_receive_money = (@$date_receive_money[2] - 543) . "-" . @$date_receive_money[1] . "-" . @$date_receive_money[0];
        }else{
			$date_receive_money = $data_post['data']['coop_loan']['date_start_period'];
		}
		
		$data_insert['date_receive_money'] = $date_receive_money;
        $data_insert['date_first_period'] = $data_post['data']['loan_deduct_profile']['date_first_period'];
        $data_insert['first_interest'] = str_replace(',', '', $data_post['data']['loan_deduct_profile']['first_interest']);
      
		$this->db->insert('coop_loan_deduct_profile', $data_insert);
        $loan_deduct_id = $this->db->insert_id();

        if(isset($data_post['data']['loan_deduct'] )) {

            $deduct_amount = 0;
            foreach ($data_post['data']['loan_deduct'] as $key => $value) {
                $data_insert = array();
                $data_insert['loan_id'] = @$loan_id;
                $data_insert['loan_deduct_list_code'] = $key;
                $data_insert['loan_deduct_amount'] = str_replace(',', '', $value);
                $data_insert['loan_deduct_id'] = @$loan_deduct_id;
                $this->db->insert('coop_loan_deduct', $data_insert);

                $deduct_amount += str_replace(',', '', $value);
            }
        }

        if($deduct_amount>0) {
            $this->db->where("id", $loan_id);
            $this->db->update("coop_loan", array('deduct_status' => '1'));
        }

        $this->db->where('loan_id',$loan_id);
        $this->db->delete('coop_loan_prev_deduct');
        if(!empty($data_post['prev_loan'])){
            foreach($data_post['prev_loan'] as $key => $value){
                if(@$value['id']!=''){
                    $data_insert = array();
                    $data_insert['loan_id'] = @$loan_id;
                    $data_insert['ref_id'] = $value['id'];
                    $data_insert['data_type'] = $value['type'];
                    $data_insert['pay_type'] = $value['pay_type'];
                    $data_insert['pay_amount'] = str_replace(',','',$value['amount']);
                    $data_insert['interest_amount'] = $value['interest'];
                    $this->db->insert('coop_loan_prev_deduct', $data_insert);
                }
            }
        }


        //$member_id = $this->contract->findContract($loan_id)->member_id;
        $member_id = $this->findContract($loan_id)->member_id;
        $this->db->where("loan_id = '".$loan_id."' AND member_id='".$member_id."'");
        $this->db->delete("coop_life_insurance");

        $insurance_amount = str_replace(',','',$data_post['data']['coop_left_insurance']);
        $insurance_premium = str_replace(',', '', $data_post['data']['loan_deduct']['deduct_insurance']);
        if($insurance_amount > 0){
            $data_insert = array();
            $data_insert['loan_id'] = @$loan_id;
            $data_insert['member_id'] = @$member_id;
            $data_insert['insurance_year'] = date("Y")+543;
            //$data_insert['insurance_date'] = @$data_post['insurance_date'];
            $data_insert['contract_number'] = '';
            $data_insert['insurance_amount'] = $insurance_amount;
            $data_insert['insurance_premium'] = $insurance_premium;//การกู้
            $data_insert['admin_id'] = @$_SESSION['USER_ID'];
            $data_insert['createdatetime'] = @$createdatetime;
            $data_insert['insurance_status'] = 0;
            $this->db->insert('coop_life_insurance', $data_insert);
        }
		
		
		if($affected_rows > 0){
			$result['status'] = 'success';
			$result['loan_id'] = $loan_id;
			//บันทึกสำเร็จ
		}else{
			$result['status'] = 'error';
			//บันทึกไม่สำเร็จ
		}
		return $result;
    }	
	
	//อนุมัติเงินกู้
    public function loan_approve_save($data_post){
	    ini_set('precision', 16);
		$affected_rows = 0;
		//echo '<pre>'; print_r($data_post); exit;
		// $this->db->trans_start();
		$arr_date_approve = explode('/',@$data_post['date_approve']);
		$date_approve = ($arr_date_approve[2]-543)."-".$arr_date_approve[1]."-".$arr_date_approve[0];
		$date_approve_time = (@$data_post['date_approve'] != '')?@$date_approve." ".date('H:i:s'):date('Y-m-d H:i:s');
		$date_approve = (@$data_post['date_approve'] != '')?@$date_approve:date('Y-m-d');
		$year_approve = (@$data_post['date_approve'] != '')?($arr_date_approve[2]-543):date('Y');
		$month_approve = (@$data_post['date_approve'] != '')?($arr_date_approve[1]):date('m');
		//echo 'date_approve='.$date_approve.'<br>';
		// echo"<pre>";print_r($data_post);exit;
		// var_dump(@$_POST);
		// exit;
		$this->db->select(array('t1.*','t3.loan_type_code'));
		$this->db->from("coop_loan as t1");
		$this->db->join("coop_loan_name as t2",'t1.loan_type = t2.loan_name_id','inner');
		$this->db->join("coop_loan_type as t3",'t2.loan_type_id = t3.id','inner');
		$this->db->where("t1.id = '".@$data_post['loan_id']."'");
		$rs_loan = $this->db->get()->result_array();
		$rs_loan = $rs_loan[0];
		$member_id = @$rs_loan['member_id'];
		$loan_amount = $rs_loan['loan_amount'];
		
		//@start บันทึกข้อมูลในตารางเก็บข้อมูลรายละเอียดการขอกู้เงิน เพื่อใช้ดูข้อมูลย้อนหลัง
		$this->db->select('salary,other_income');
		$this->db->from('coop_mem_apply');
		$this->db->where("coop_mem_apply.member_id = '".$member_id."'");
		$rs_member = $this->db->get()->result_array();
		$row_member = $rs_member[0];
		
		$salary = $row_member['salary']; //เงินเดือน
		$other_income = $row_member['other_income']; //รายได้อื่นๆ
		
		$this->db->select('share_collect_value');
		$this->db->from('coop_mem_share');
		$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2')");
		$this->db->order_by('share_date DESC');
		$this->db->limit(1);
		$row_prev_share = $this->db->get()->result_array();
		$row_prev_share = @$row_prev_share[0];
		$now_share = $row_prev_share['share_collect_value']; //หุ้นที่มี่
		$rules_share = $loan_amount*20/100; //หุ้นตามหลักเกณฑ์
		
		//เช็คสมุดเงินฝากสีน้ำเงิน
		$this->db->select(array('coop_maco_account.account_id'));
		$this->db->from('coop_maco_account');
		$this->db->join("coop_deposit_type_setting","coop_maco_account.type_id = coop_deposit_type_setting.type_id","inner");
		$this->db->where("
			coop_maco_account.mem_id = '".$member_id."' 
			 AND coop_maco_account.account_status = '0'
			AND coop_deposit_type_setting.deduct_loan = '1'
		");
		$this->db->limit(1);
		$rs_account_blue = $this->db->get()->result_array();
		$account_id_blue =  @$rs_account_blue[0]['account_id'];
		if($account_id_blue != ''){
			$this->db->select(array('transaction_balance'));
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '".$account_id_blue."'");
			$this->db->order_by('transaction_id DESC');
			$this->db->limit(1);
			$rs_account_blue_balance = $this->db->get()->result_array();
			$account_blue_deposit = @$rs_account_blue_balance[0]['transaction_balance'];
			
		}
		
		$data_insert = array();
		$data_insert['member_id'] = $member_id;
		$data_insert['loan_id'] = @$data_post['loan_id'];
		$data_insert['salary'] = $salary;
		$data_insert['other_income'] = $other_income;
		$data_insert['rules_share'] = $rules_share;
		$data_insert['now_share'] = $now_share;
		$data_insert['account_blue_deposit'] = $account_blue_deposit;
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['createdatetime'] = date('Y-m-d H:i:s');
		$data_insert['updatetime'] = date('Y-m-d H:i:s');
		$this->db->insert('coop_loan_report_detail', $data_insert);
		//@end บันทึกข้อมูลในตารางเก็บข้อมูลรายละเอียดการขอกู้เงิน เพื่อใช้ดูข้อมูลย้อนหลัง
		
		if($data_post['status_to']=='1'){
			//get receipt setting data
			$receipt_format = 1;
			$receipt_finance_setting = $this->db->select("*")->from("coop_setting_finance")->where("name = 'receipt_cashier_format' AND status = 1")->order_by("created_at DESC")->get()->row_array();
			if(!empty($receipt_finance_setting)) {
				$receipt_format = $receipt_finance_setting['value'];
			}
			if($receipt_format == 1) {
				//$date_approve_time
				$yymm = ($year_approve+543).$month_approve;

				$this->db->select(array('*'));
				$this->db->from('coop_receipt');
				$this->db->where("receipt_id LIKE '".$yymm."%'");
				$this->db->order_by("receipt_id DESC");
				$this->db->limit(1);
				$row_receipt = $this->db->get()->result_array();
				$row_receipt = @$row_receipt[0];

				if(@$row_receipt['receipt_id'] != '') {
					$id = (int) substr($row_receipt["receipt_id"], 6);
					$receipt_id = $yymm.sprintf("%06d", $id + 1);
				}else {
					$receipt_id = $yymm."000001";
				}
			} else {
				$receipt_id = $this->Finance_libraries->generate_cashier_receipt_id($receipt_format, $date_approve);
			}

			$receipt_arr = array();
			$this->db->select(array('t1.*'));
			$this->db->from("coop_loan_prev_deduct as t1");
			$this->db->where("t1.loan_id = '".@$data_post['loan_id']."'");
			$row = $this->db->get()->result_array();
			//echo"<pre>";print_r($row);exit;
			$r=0;
			foreach($row as $key => $value){
				//update หนี้ห้อย-------------------
				$extra_debt_amount	= 0;//หนี้ห้อย

				if($extra_debt_amount){
					$this->db->where("loan_id", $rs_loan['id']);
					$this->db->where("run_id", $value['run_id']);
					$this->db->set("pay_amount", "pay_amount - ".$extra_debt_amount, false);
					$this->db->update("coop_loan_prev_deduct");

					$this->db->where("loan_id", $rs_loan['id']);
					$this->db->set("estimate_receive_money", "estimate_receive_money + ".$extra_debt_amount, false);
					$this->db->update("coop_loan_deduct_profile");

					$this->db->where("loan_id", $rs_loan['id']);
					$this->db->where("loan_deduct_list_code", "deduct_pay_prev_loan");
					$this->db->set("loan_deduct_amount", "loan_deduct_amount - ".$extra_debt_amount, false);
					$this->db->update("coop_loan_deduct");

					// $this->db->where("id", $value['ref_id']);
					// $this->db->set("loan_amount_balance", "loan_amount_balance - ".$extra_debt_amount, false);
					// $this->db->update("coop_loan");
				}
				//--------------------------------

				if($value['pay_type'] == 'all'){
					if($value['data_type'] == 'loan'){
						$this->db->select(array('t1.*'));
						$this->db->from("coop_loan as t1");
						$this->db->where("t1.id = '".@$value['ref_id']."'");
						$ref_loan = $this->db->get()->result_array();
						$ref_loan = $ref_loan[0];

						$loan_amount = $ref_loan['loan_amount_balance'];//เงินกู้
						$loan_type = $ref_loan['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย
						$loan_id = $value['ref_id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562

						$date1 = date("Y-m-d", strtotime($ref_loan['date_last_interest']));
						
						//$date2 = date("Y-m-d");//วันที่คิดดอกเบี้ย now
						$date2 = $date_approve;//วันที่คิดดอกเบี้ย now
						$interest_loan = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_id, $date1, $date2);
						$interest_loan = round($interest_loan);

						$receipt_arr[$r]['receipt_id'] = $receipt_id;
						$receipt_arr[$r]['member_id'] = $member_id;
						$receipt_arr[$r]['loan_id'] = $value['ref_id'];
						$receipt_arr[$r]['account_list_id'] = '15';
						$receipt_arr[$r]['principal_payment'] = $ref_loan['loan_amount_balance'] - @$extra_debt_amount;
						$receipt_arr[$r]['interest'] = $interest_loan;
						$receipt_arr[$r]['total_amount'] = $ref_loan['loan_amount_balance'] - @$extra_debt_amount + $interest_loan;
						$receipt_arr[$r]['payment_date'] = $date_approve;
						$receipt_arr[$r]['createdatetime'] = $date_approve_time;
						$receipt_arr[$r]['loan_amount_balance'] = '0';
						$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$ref_loan['contract_number'];
						$receipt_arr[$r]['deduct_type'] = 'all';
						$r++;
						$data_insert = array();
						if(@$extra_debt_amount>=1){
							$data_insert['loan_status'] = '1';
							$data_insert['loan_amount_balance'] = $extra_debt_amount;//คงค้างหนี้ห้อยไว้ รอการผ่านรายการ
						}else{
							$data_insert['loan_status'] = '4';
							$data_insert['loan_amount_balance'] = '0';
						}
						$this->db->where('id',$value['ref_id']);
						$this->db->update('coop_loan',$data_insert);

						$loan_transaction = array();
						$loan_transaction['loan_id'] = $value['ref_id'];
						if(@$extra_debt_amount>=1)
							$loan_transaction['loan_amount_balance'] = @$extra_debt_amount;
						else
							$loan_transaction['loan_amount_balance'] = '0';
						$loan_transaction['transaction_datetime'] = $date_approve_time;
						$loan_transaction['receipt_id'] = $receipt_id;
						$this->loan_libraries->loan_transaction($loan_transaction);

						$data_insert = array();
						$data_insert['date_last_interest'] = $date_approve_time;
						$this->db->where('id',$value['ref_id']);
						$this->db->update('coop_loan',$data_insert);

					}else if($value['data_type'] == 'atm'){
						$this->db->select(array('t1.*'));
						$this->db->from("coop_loan_atm as t1");
						$this->db->where("
							t1.loan_atm_id = '".$value['ref_id']."'
						");
						$row_atm = $this->db->get()->result_array();
						$row_atm = @$row_atm[0];

						$loan_amount_balance = $row_atm['total_amount_approve'] - $row_atm['total_amount_balance'];

						$cal_atm_interest = array();
						$cal_atm_interest['loan_atm_id'] = $value['ref_id'];
						$cal_atm_interest['date_interesting'] = date('Y-m-d', strtotime($row_atm['date_last_interest']));
						//อันเดิม
						//$interest_loan = $this->loan_libraries->cal_atm_interest($cal_atm_interest);

						//ดอกเบี้ยเงินกู้ตามช่วงเวลาที่มีการทำรายการ
						// $interest_loan = $this->loan_libraries->cal_atm_interest_transaction($cal_atm_interest);
						// $interest_amount = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
						$interest_loan = $this->loan_libraries->cal_atm_interest_report_test($cal_atm_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false, true )['interest_month'];


						//รายการที่มีการผ่านรายการแล้ว
						// $total_atm_after_process = $this->loan_libraries->cal_atm_after_process($cal_atm_interest);

						$receipt_arr[$r]['receipt_id'] = $receipt_id;
						$receipt_arr[$r]['member_id'] = $member_id;
						$receipt_arr[$r]['loan_atm_id'] = $value['ref_id'];
						$receipt_arr[$r]['account_list_id'] = '31';
						$receipt_arr[$r]['principal_payment'] = $loan_amount_balance;
						$receipt_arr[$r]['interest'] = $interest_loan;
						$receipt_arr[$r]['interest_debt'] = @$value['interest_debt'];
						//$receipt_arr[$r]['total_amount'] = $loan_amount_balance+$interest_loan;
						$receipt_arr[$r]['total_amount'] = (@$loan_amount_balance-@$total_atm_after_process)+@$interest_loan + @$value['interest_debt'];
						$receipt_arr[$r]['payment_date'] = date('Y-m-d');
						$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
						$receipt_arr[$r]['loan_amount_balance'] = '0';
						$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$row_atm['contract_number'];
						$receipt_arr[$r]['deduct_type'] = 'all';
						$r++;
						$data_insert = array();
						$data_insert['loan_status'] = '1';
						$data_insert['loan_amount_balance'] = '0';
						$this->db->where('loan_atm_id',$value['ref_id']);
						$this->db->update('coop_loan_atm_detail',$data_insert);

						$data_insert = array();
						$data_insert['loan_atm_status'] = '3';
						$data_insert['total_amount_balance'] = $row_atm['total_amount_approve'];
						$this->db->where('loan_atm_id',$value['ref_id']);
						$this->db->update('coop_loan_atm',$data_insert);

						$atm_transaction = array();
						$atm_transaction['loan_atm_id'] = $value['ref_id'];
						$atm_transaction['loan_amount_balance'] = '0';
						$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
						$atm_transaction['receipt_id'] = $receipt_id;
						$this->loan_libraries->atm_transaction($atm_transaction);

						$data_insert = array();
						$data_insert['date_last_interest'] = date('Y-m-d H:i:s');
						$this->db->where('loan_atm_id',$value['ref_id']);
						$this->db->update('coop_loan_atm',$data_insert);
					}
				}else if($value['pay_type'] == 'principal'){
					if($value['data_type'] == 'loan'){
						$this->db->select(array('t1.*'));
						$this->db->from("coop_loan as t1");
						$this->db->where("
							t1.id = '".$value['ref_id']."'
						");
						$row_loan = $this->db->get()->result_array();
						$row_loan = @$row_loan[0];

						$loan_amount_balance = ($row_loan['loan_amount_balance'] - $value['pay_amount']) + @$extra_debt_amount;

						$data_insert = array();
						$data_insert['loan_amount_balance'] = $loan_amount_balance;
						$this->db->where('id',$value['ref_id']);
						$this->db->update('coop_loan',$data_insert);

						$receipt_arr[$r]['receipt_id'] = $receipt_id;
						$receipt_arr[$r]['member_id'] = $member_id;
						$receipt_arr[$r]['loan_id'] = $value['ref_id'];
						$receipt_arr[$r]['account_list_id'] = '15';
						$receipt_arr[$r]['principal_payment'] = $value['pay_amount'] - @$extra_debt_amount;
						$receipt_arr[$r]['total_amount'] = $value['pay_amount'] - @$extra_debt_amount;
						$receipt_arr[$r]['payment_date'] = $date_approve;
						$receipt_arr[$r]['createdatetime'] = $date_approve_time;
						$receipt_arr[$r]['loan_amount_balance'] = $loan_amount_balance;
						$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$row_loan['contract_number'];
						$receipt_arr[$r]['deduct_type'] = 'principal';
						$r++;
						$loan_transaction = array();
						$loan_transaction['loan_id'] = $value['ref_id'];
						$loan_transaction['loan_amount_balance'] = $loan_amount_balance;
						$loan_transaction['transaction_datetime'] = $date_approve_time;
						$loan_transaction['receipt_id'] = $receipt_id;
						$this->loan_libraries->loan_transaction($loan_transaction);
					}else if($value['data_type'] == 'atm'){
						$this->db->select(array('t1.*'));

						$this->db->from("coop_loan_atm_detail as t1");
						$this->db->where("
							t1.loan_atm_id = '".$value['ref_id']."'
							AND loan_status = '0'
						");
						$this->db->order_by('loan_id ASC');
						$row_loan = $this->db->get()->result_array();
						$pay_amount = $value['pay_amount'];
						foreach($row_loan as $key2 => $value2){
							if($pay_amount > $value2['loan_amount_balance']){
								$data_insert = array();
								$data_insert['loan_amount_balance'] = 0;
								$data_insert['loan_status'] = '1';
								$this->db->where('loan_id',$value2['loan_id']);
								$this->db->update('coop_loan_atm_detail',$data_insert);
								$pay_amount = $pay_amount - $value2['loan_amount_balance'];
							}else{
								$data_insert = array();
								$data_insert['loan_amount_balance'] = $value2['loan_amount_balance'] - $pay_amount;
								$this->db->where('loan_id',$value2['loan_id']);
								$this->db->update('coop_loan_atm_detail',$data_insert);
								$pay_amount = 0;
							}
							if($pay_amount == 0){
								break;
							}
						}
						$this->db->select(array('t1.*'));
						$this->db->from("coop_loan_atm as t1");
						$this->db->where("
							t1.loan_atm_id = '".$value['ref_id']."'
						");
						$row_loan = $this->db->get()->result_array();

						$data_insert = array();
						$data_insert['total_amount_balance'] = $row_loan[0]['total_amount_balance'] + $value['pay_amount'];
						$data_insert['loan_atm_status'] = '4';
						$this->db->where('loan_atm_id',$value['ref_id']);
						$this->db->update('coop_loan_atm',$data_insert);

						$loan_amount_balance = $row_loan[0]['total_amount_approve']-($row_loan[0]['total_amount_balance'] + $value['pay_amount']);

						$atm_transaction = array();
						$atm_transaction['loan_atm_id'] = $value['ref_id'];
						$atm_transaction['loan_amount_balance'] = $loan_amount_balance;
						$atm_transaction['transaction_datetime'] = $date_approve_time;
						$atm_transaction['receipt_id'] = $receipt_id;
						$this->loan_libraries->atm_transaction($atm_transaction);

						$receipt_arr[$r]['receipt_id'] = $receipt_id;
						$receipt_arr[$r]['member_id'] = $member_id;
						$receipt_arr[$r]['loan_atm_id'] = $value['ref_id'];
						$receipt_arr[$r]['account_list_id'] = '31';
						$receipt_arr[$r]['principal_payment'] = $value['pay_amount'];
						$receipt_arr[$r]['total_amount'] = $value['pay_amount'];
						$receipt_arr[$r]['payment_date'] = $date_approve;
						$receipt_arr[$r]['createdatetime'] = $date_approve_time;
						$receipt_arr[$r]['loan_amount_balance'] = $loan_amount_balance;
						$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$row_loan[0]['contract_number'];
						$receipt_arr[$r]['deduct_type'] = 'principal';
						$r++;
					}
				}
			}
		///////////////////////////////////////////////////////////
			$this->db->select(array('t1.*','t2.account_list_id','t3.account_list'));
			$this->db->from("coop_loan_deduct as t1");
			$this->db->join("coop_loan_deduct_list as t2",'t1.loan_deduct_list_code = t2.loan_deduct_list_code','inner');
			$this->db->join("coop_account_list as t3",'t2.account_list_id = t3.account_id','left');
			$this->db->where("
				t1.loan_id = '".$data_post['loan_id']."' AND t1.loan_deduct_list_code != 'deduct_pay_prev_loan'
			");
			$row_deduct = $this->db->get()->result_array();
			//echo $this->db->last_query();
			//echo "<pre>";print_r($row_deduct);exit;
			foreach($row_deduct as $key => $value) {
				if ($value['loan_deduct_list_code'] == 'deduct_blue_deposit' && $value['loan_deduct_amount'] > 0) {
					//เช็คสมุดเงินฝากสีน้ำเงิน
					$this->db->select(array('coop_maco_account.account_id', 'coop_maco_account.mem_id', 'coop_deposit_type_setting.type_name'));
					$this->db->from('coop_maco_account');
					$this->db->join("coop_deposit_type_setting", "coop_maco_account.type_id = coop_deposit_type_setting.type_id", "inner");
					$this->db->where("
						coop_maco_account.mem_id = '" . $member_id . "'
						 AND coop_maco_account.account_status = '0'
						AND coop_deposit_type_setting.deduct_loan = '1'
					");
					$this->db->limit(1);
					$rs_account = $this->db->get()->result_array();
					$row_account = @$rs_account[0];
					if (empty($rs_account)) {
						$this->db->select(array('type_id', 'type_name', 'type_code'));
						$this->db->from('coop_deposit_type_setting');
						$this->db->where("deduct_loan = '1'");
						$this->db->limit(1);
						$rs_deduct_loan = $this->db->get()->result_array();
						$row_deduct_loan = @$rs_deduct_loan[0];

						$this->db->select('account_id');
						$this->db->from('coop_maco_account');
						$this->db->where("type_id = '" . $row_deduct_loan['type_id'] . "' AND account_status = '0'");
						$this->db->order_by("account_id DESC");
						$this->db->limit(1);
						$row = $this->db->get()->result_array();
						if (!empty($row)) {
							$auto_account_id = str_replace("001" . $row_deduct_loan['type_code'], '', $row[0]['account_id']);
							$auto_account_id = (int)$auto_account_id;
							$auto_account_id = $auto_account_id + 1;
						} else {
							$auto_account_id = 1;
						}
						$acc_id = "001" . $row_deduct_loan['type_code'] . sprintf("%06d", @$auto_account_id);
						$account_id = @$acc_id;
						//echo '<pre>'; print_r($row_deduct_loan); echo '</pre>';
						$this->db->select('*');
						$this->db->from('coop_mem_apply');
						$this->db->where("member_id = '" . $member_id . "'");
						$rs_member = $this->db->get()->result_array();
						$row_member = @$rs_member[0];

						//start เช็คบัญชีในตาราง coop_account_transaction
						$this->db->select('account_id');
						$this->db->from('coop_account_transaction');
						$this->db->where("account_id = '" . $account_id . "'");
						$this->db->order_by("transaction_time DESC, transaction_id DESC");
						$this->db->limit(1);
						$check_account_transaction = $this->db->get()->result_array();
						if (!empty($check_account_transaction)) {
							//หา account_id อีกรอบ
							$this->db->select('account_id');
							$this->db->from('coop_account_transaction');
							$this->db->where("account_id LIKE '001" . $row_deduct_loan['type_code'] . "%'");
							$this->db->order_by("transaction_time DESC, transaction_id DESC");
							$this->db->limit(1);
							$last_account_id = $this->db->get()->result_array();
							$auto_account_id = str_replace("001" . $row_deduct_loan['type_code'], '', $last_account_id[0]['account_id']);
							$auto_account_id = (int)$auto_account_id;
							$auto_account_id = $auto_account_id + 1;
							$account_id = "001" . $row_deduct_loan['type_code'] . sprintf("%06d", @$auto_account_id);
						}
						//end เช็คบัญชีในตาราง coop_account_transaction

						$data_insert = array();
						$data_insert['account_id'] = @$account_id;
						$data_insert['mem_id'] = $member_id;
						$data_insert['member_name'] = $row_member['firstname_th'] . " " . $row_member['lastname_th'];
						$data_insert['account_name'] = $row_member['firstname_th'] . " " . $row_member['lastname_th'];
						$data_insert['created'] = date('Y-m-d H:i:s');
						$data_insert['account_amount'] = '0';
						$data_insert['book_number'] = '1';
						$data_insert['type_id'] = $row_deduct_loan['type_id'];
						$data_insert['account_status'] = '0';
						$this->db->insert('coop_maco_account', $data_insert);
						//$account_id = $this->db->insert_id();
						//$account_id = @$acc_id;
					} else {
						$account_id = $row_account['account_id'];
					}

					$this->db->where("account_id = '" . $account_id . "' AND loan_id ='" . $data_post['loan_id'] . "'");
					$this->db->delete("coop_account_transaction");

					$transaction_list = 'XD';
					$transaction_deposit = $value['loan_deduct_amount'];

					$this->db->select('*');
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '" . $account_id . "'");
					$this->db->order_by('transaction_time DESC, transaction_id DESC');
					$this->db->limit(1);
					$row = $this->db->get()->result_array();
					if (!empty($row)) {
						$balance = @$row[0]['transaction_balance'];
						$balance_no_in = @$row[0]['transaction_no_in_balance'];
					} else {
						$balance = 0;
						$balance_no_in = 0;
					}

					$sum = @$balance + @$transaction_deposit;
					$sum_no_in = @$balance_no_in + @$transaction_deposit;

					$data_insert = array();
					$data_insert['transaction_time'] = date('Y-m-d H:i:s');
					$data_insert['transaction_list'] = @$transaction_list;
					$data_insert['transaction_withdrawal'] = '';
					$data_insert['transaction_deposit'] = @$transaction_deposit;
					$data_insert['transaction_balance'] = @$sum;
					$data_insert['transaction_no_in_balance'] = @$sum_no_in;
					$data_insert['user_id'] = @$_SESSION['USER_ID'];
					$data_insert['account_id'] = @$account_id;
					$data_insert['loan_id'] = $data_post['loan_id'];
					$data_insert['receipt_id'] = $receipt_id;

					if ($this->db->insert('coop_account_transaction', $data_insert)) {
						/*$this->center_function->toast("ทำการฝากเงินเรียบร้อยแล้ว");

						$data_acc['coop_account']['account_description'] = "สมาชิกฝากเงินเข้าบัญชี";
						$data_acc['coop_account']['account_datetime'] = date('Y-m-d H:i:s');

						$i=0;
						$data_acc['coop_account_detail'][$i]['account_type'] = 'debit';
						$data_acc['coop_account_detail'][$i]['account_amount'] = @$transaction_deposit];
						$data_acc['coop_account_detail'][$i]['account_chart_id'] = '10100';
						$i++;
						$data_acc['coop_account_detail'][$i]['account_type'] = 'credit';
						$data_acc['coop_account_detail'][$i]['account_amount'] = @$transaction_deposit;
						$data_acc['coop_account_detail'][$i]['account_chart_id'] = '20100';
						$this->account_transaction->account_process($data_acc);
						*/
						$receipt_arr[$r]['receipt_id'] = $receipt_id;
						$receipt_arr[$r]['member_id'] = $member_id;
						$receipt_arr[$r]['account_list_id'] = '30';
						$receipt_arr[$r]['principal_payment'] = $value['loan_deduct_amount'];
						$receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
						$receipt_arr[$r]['payment_date'] = $date_approve;
						$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
						$receipt_arr[$r]['loan_amount_balance'] = $balance;
						$receipt_arr[$r]['transaction_text'] = 'ฝากเงินเลขที่บัญชี ' . $account_id;
						$receipt_arr[$r]['deduct_type'] = 'all';
						$r++;
					}

				} else if ($value['loan_deduct_list_code'] == 'deduct_share' && $value['loan_deduct_amount'] > 0) {
					$this->db->select('*');
					$this->db->from('coop_share_setting');
					$this->db->order_by('setting_id DESC');
					$row = $this->db->get()->result_array();
					$share_setting = $row[0];

					$this->db->select('*');
					$this->db->from('coop_mem_share');
					$this->db->where("member_id = '" . $member_id . "' AND share_status = '1'");
					$this->db->order_by('share_date DESC, share_id DESC');
					$this->db->limit(1);
					$row_share = $this->db->get()->result_array();
					$row_share = @$row_share[0];
					$data_insert = array();
					$data_insert['member_id'] = $member_id;
					$data_insert['admin_id'] = $_SESSION['USER_ID'];
					$data_insert['share_type'] = 'SPL';
					$data_insert['share_date'] = $date_approve_time;
					$data_insert['share_payable'] = @$row_share['share_collect'];
					$data_insert['share_payable_value'] = @$row_share['share_collect_value'];
					$data_insert['share_early'] = $value['loan_deduct_amount'] / $share_setting['setting_value'];
					$data_insert['share_early_value'] = $value['loan_deduct_amount'];
					$data_insert['share_collect'] = @$row_share['share_collect'] + ($value['loan_deduct_amount'] / $share_setting['setting_value']);
					$data_insert['share_collect_value'] = @$row_share['share_collect_value'] + @$value['loan_deduct_amount'];
					$data_insert['share_value'] = $share_setting['setting_value'];
					$data_insert['share_status'] = '1';
					$data_insert['pay_type'] = @$data['pay_type'];
					$data_insert['share_bill'] = @$receipt_id;

					$this->db->insert('coop_mem_share', $data_insert);

					$receipt_arr[$r]['receipt_id'] = $receipt_id;
					$receipt_arr[$r]['member_id'] = $member_id;
					$receipt_arr[$r]['account_list_id'] = '14';
					$receipt_arr[$r]['principal_payment'] = $value['loan_deduct_amount'];
					$receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
					$receipt_arr[$r]['payment_date'] = $date_approve;
					$receipt_arr[$r]['createdatetime'] = $date_approve_time;
					$receipt_arr[$r]['loan_amount_balance'] = @$row_share['share_collect_value'] + @$value['loan_deduct_amount'];
					$receipt_arr[$r]['transaction_text'] = 'หุ้น';
					$receipt_arr[$r]['deduct_type'] = 'all';
					$r++;
				} else if ($value['loan_deduct_list_code'] == 'buy_share' && $value['loan_deduct_amount'] > 0) {
					$this->db->select('*');
					$this->db->from('coop_share_setting');
					$this->db->order_by('setting_id DESC');
					$row = $this->db->get()->result_array();
					$share_setting = $row[0];

					$this->db->select('*');
					$this->db->from('coop_mem_share');
					$this->db->where("member_id = '" . $member_id . "' AND share_status = '1'");
					$this->db->order_by('share_date DESC, share_id DESC');
					$this->db->limit(1);
					$row_share = $this->db->get()->result_array();
					$row_share = @$row_share[0];
					$data_insert = array();
					$data_insert['member_id'] = $member_id;
					$data_insert['admin_id'] = $_SESSION['USER_ID'];
					$data_insert['share_type'] = 'SPL';
					$data_insert['share_date'] = $date_approve;
					$data_insert['share_payable'] = @$row_share['share_collect'];
					$data_insert['share_payable_value'] = @$row_share['share_collect_value'];
					$data_insert['share_early'] = $value['loan_deduct_amount'] / $share_setting['setting_value'];
					$data_insert['share_early_value'] = $value['loan_deduct_amount'];
					$data_insert['share_collect'] = @$row_share['share_collect'] + ($value['loan_deduct_amount'] / $share_setting['setting_value']);
					$data_insert['share_collect_value'] = @$row_share['share_collect_value'] + @$value['loan_deduct_amount'];
					$data_insert['share_value'] = $share_setting['setting_value'];
					$data_insert['share_status'] = '1';
					$data_insert['pay_type'] = @$data['pay_type'];
					$data_insert['share_bill'] = @$receipt_id;

					$this->db->insert('coop_mem_share', $data_insert);

					$receipt_arr[$r]['receipt_id'] = $receipt_id;
					$receipt_arr[$r]['member_id'] = $member_id;
					$receipt_arr[$r]['account_list_id'] = '37';
					$receipt_arr[$r]['principal_payment'] = $value['loan_deduct_amount'];
					$receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
					$receipt_arr[$r]['payment_date'] = $date_approve;
					$receipt_arr[$r]['createdatetime'] = $date_approve_time;
					$receipt_arr[$r]['loan_amount_balance'] = @$row_share['share_collect_value'] + @$value['loan_deduct_amount'];
					$receipt_arr[$r]['transaction_text'] = 'ซื้อหุ้นจากการกู้';
					$receipt_arr[$r]['deduct_type'] = 'all';
					$r++;
				}else if($value['loan_deduct_list_code'] == 'deduct_before_interest' && $value['loan_deduct_amount'] > 0){
					$receipt_arr[$r]['receipt_id'] = $receipt_id;
					$receipt_arr[$r]['member_id'] = $member_id;
					$receipt_arr[$r]['loan_id'] = $data_post['loan_id'];
					$receipt_arr[$r]['account_list_id'] = $value['account_list_id'];
					$receipt_arr[$r]['interest'] = $value['loan_deduct_amount'];
					$receipt_arr[$r]['principal_payment'] = 0;
					$receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
					$receipt_arr[$r]['payment_date'] = $date_approve;
					$receipt_arr[$r]['createdatetime'] = $date_approve_time;
					$receipt_arr[$r]['loan_amount_balance'] = $loan_amount;
					$receipt_arr[$r]['transaction_text'] = 'ชำระดอกเบี้ยเงินกู้';
					$receipt_arr[$r]['deduct_type'] = 'interest';
					$r++;
				}else{
					if($value['loan_deduct_amount']>0){
						$receipt_arr[$r]['receipt_id'] = $receipt_id;
						$receipt_arr[$r]['member_id'] = $member_id;
						$receipt_arr[$r]['account_list_id'] = $value['account_list_id'];
						$receipt_arr[$r]['principal_payment'] = $value['loan_deduct_amount'];
						$receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
						$receipt_arr[$r]['payment_date'] = $date_approve;
						$receipt_arr[$r]['createdatetime'] = $date_approve_time;
						$receipt_arr[$r]['loan_amount_balance'] = '';
						$receipt_arr[$r]['transaction_text'] = $value['account_list'];
						$receipt_arr[$r]['deduct_type'] = 'all';
						$r++;
					}
				}
			}
			//echo"<pre>";print_r($receipt_arr);exit;
			$data = array();
			$data['coop_account']['account_description'] = "รายการรับชำระเงิน";
			$data['coop_account']['account_datetime'] = $date_approve_time;
			$data['coop_account']['ref'] = @$data_post['loan_id'];
			$data['coop_account']['ref_type'] = 'loan_refinance';
			$data['coop_account']['process'] = 'refinance';

			$sum_count = 0;
			foreach($receipt_arr as $key => $value){
				$data_insert = array();
				$data_insert['receipt_id'] = $value['receipt_id'];
				$data_insert['receipt_list'] = $value['account_list_id'];
				$data_insert['receipt_count'] = $value['total_amount'];
				$this->db->insert('coop_receipt_detail', $data_insert);

				//บันทึกการชำระเงิน
				$data_insert = array();
				$data_insert['receipt_id'] = $value['receipt_id'];
				$data_insert['member_id'] = @$value['member_id'];
				$data_insert['loan_id'] = @$value['loan_id'];
				$data_insert['loan_atm_id'] = @$value['loan_atm_id'];
				$data_insert['account_list_id'] = $value['account_list_id'];
				$data_insert['principal_payment'] = @$value['principal_payment'];
				$data_insert['interest'] = @$value['interest'];
				$data_insert['total_amount'] = @$value['total_amount'];
				$data_insert['payment_date'] = @$value['payment_date'];
				$data_insert['loan_amount_balance'] = @$value['loan_amount_balance'];
				$data_insert['createdatetime'] = @$value['createdatetime'];
				$data_insert['transaction_text'] = @$value['transaction_text'];
				$data_insert['deduct_type'] = @$value['deduct_type'];
				$this->db->insert('coop_finance_transaction', $data_insert);
				$sum_count += @$value['total_amount'];
				$sum_interest += @$value['interest'];

				$this->db->select(array('t1.*'));
				$this->db->from("coop_loan as t1");
				$this->db->where("
					t1.id = '".$value['loan_id']."'
				");
				$row_loan = $this->db->get()->result_array();
				$row_loan = @$row_loan[0];

				if(!empty($row_loan['loan_type']) && $value['account_list_id'] != '15' ){
					$match_id = $value['account_list_id'];
					$match_type = 'loan';

				}else{
					$match_id = $value['account_list_id'];
					$match_type = 'account_list';
				}

				$this->db->select(array(
					't1.account_chart_id',
					't2.account_chart'
				   ));
				   $this->db->from('coop_account_match as t1');
				   $this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','left');
				   $this->db->where("
					t1.match_type = '{$match_type}'
					AND t1.match_id = '{$match_id}'
				   ");
				// echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";

				   $row_account_match = $this->db->get()->result_array();
				   $row_account_match = @$row_account_match[0];
				   $account_chart_id = @$row_account_match['account_chart_id'];

				$data['coop_account_detail'][$key]['account_type'] = 'credit';
				$data['coop_account_detail'][$key]['account_amount'] = @$value['total_amount'];
				$data['coop_account_detail'][$key]['account_chart_id'] = $account_chart_id;



			}

			
			$data['coop_account_detail'][10101001]['account_type'] = 'debit';
			$data['coop_account_detail'][10101001]['account_amount'] = $sum_count;
			$data['coop_account_detail'][10101001]['account_chart_id'] = '10101001';

			$data['coop_account_detail'][40101001]['account_type'] = 'credit';
			$data['coop_account_detail'][40101001]['account_amount'] = $sum_interest;
			$data['coop_account_detail'][40101001]['account_chart_id'] = '40101001';
			
			$this->account_transaction->account_process($data);
			
			if($sum_count>0){
				$data_insert = array();
				$data_insert['receipt_id'] = $receipt_id;
				$data_insert['member_id'] = @$member_id;
				$data_insert['admin_id'] = @$_SESSION['USER_ID'];
				$data_insert['sumcount'] = $sum_count;
				$data_insert['receipt_datetime'] = $date_approve_time;
				$data_insert['pay_type'] = '1'; //0=เงินสด 1=โอนเงิน
				$this->db->insert('coop_receipt', $data_insert);

				$data_insert = array();
				$data_insert['deduct_receipt_id'] = $receipt_id;
				$this->db->where('id',$data_post['loan_id']);
				$this->db->update('coop_loan',$data_insert);
			}


		}

		$data_insert = array();
		if($data_post['status_to']=='1'){
			//ปีในการ gen เลขสัญญา
			$rs_month_account = $this->db->select('accm_month_ini')
			->from("coop_account_period_setting")
			->limit(1)
			->get()->result_array();
			$month_account = $rs_month_account[0]['accm_month_ini'];
			$month_now = date('m');

			if((int)$month_now >= (int)$month_account && (int)$month_account != 1){
				$year = (date('Y')+543)+1;
			}else{
				$year = (date('Y')+543);
			}
			$year_short = substr($year,2,2);
			$new_contact_number = '';

			$this->db->select('*');
			$this->db->from("coop_term_of_loan");
			$this->db->where("type_id = '".@$rs_loan['loan_type']."' AND start_date <= '".$date_approve."'");
			$this->db->order_by('start_date DESC');
			$this->db->limit(1);
			$rs_term_of_loan = $this->db->get()->result_array();
			$row_term_of_loan = @$rs_term_of_loan[0];

			$new_contact_number = $row_term_of_loan['prefix_code'];
			$new_contact_number .= substr($year, 2,2);

			$contact_number_now = $this->loan_libraries->get_contract_number($year, $rs_loan['loan_type']);

			$new_contact_number .= sprintf("% 06d",$contact_number_now);
			//$new_contact_number = $new_contact_number."/".(date('Y')+543);
			if(empty($rs_loan["contract_number"])) {
				$data_insert['contract_number'] = @$new_contact_number;
			}
			//echo $new_contact_number;exit;

		}

		//Check if compromise loan change status_to to 8 if loanee got responsibility
		$compormise_detail = $this->db->select("*")
										->from("coop_loan_guarantee_compromise")
										->where("loan_id = '".$data_post['loan_id']."' AND type in (3,4)")
										->get()->row();
		if(!empty($compormise_detail)) {
			$data_insert['loan_status'] = 8;
		} else {
			$data_insert['loan_status'] = @$data_post['status_to'];
		}

		$data_insert['approve_date'] = $date_approve_time;
		$data_insert['date_last_interest'] = date("Y-m-d",strtotime($date_approve_time));
		$this->db->where('id', @$data_post['loan_id']);
		$this->db->update('coop_loan', $data_insert);
		if($this->db->affected_rows()){
			$affected_rows++;
		}
		
		//อัพเดตเลขที่สัญญาในระบบประกัน
		$life_insurance = $this->db->select("*")
										->from("coop_life_insurance")
										->where("loan_id = '".$data_post['loan_id']."' AND insurance_status = 0")
										->get()->row();
		if(!empty($life_insurance)) {
			$data_insert = array();
			$data_insert['insurance_date'] = $date_approve_time;
			$data_insert['contract_number'] = @$new_contact_number;
			$data_insert['insurance_status'] = 1;
			$this->db->where("member_id = '".@$member_id."' AND loan_id = '".$data_post['loan_id']."'");
			$this->db->update('coop_life_insurance',$data_insert);
			
			if($data_post['status_to']=='5'){
				//ลบข้อมูลระบบประกันชีวิต เมื่อไม่อนุมัติ
				$this->db->where("member_id = '".@$member_id."' AND loan_id = '".$data_post['loan_id']."'");
				$this->db->delete("coop_life_insurance");
			}
		}	
		
		if($affected_rows > 0){
			$result['status'] = 'success';
			$result['loan_id'] = $data_post['loan_id'];
			//บันทึกสำเร็จ
		}else{
			$result['status'] = 'error';
			//บันทึกไม่สำเร็จ
		}
		return $result;
	}

	//โอนเงินกู้
    public function loan_transfer_save($data_post,$data){
		$check_loan_transfer = $this->get_loan_transfer($data_post['loan_id']);
		if(!empty($check_loan_transfer)){
			//มีการโอนโอนเงินกู้แล้ว	
			$result['status'] = 'error';
			return $result;	
		}
		
		$affected_rows = 0;
		$amount_transfer = @str_replace(',','',@$data_post['amount_transfer']);
		$loan_amount_transfer = @str_replace(',','',@$data_post['loan_amount']);

		$this->db->select(array(
		'coop_loan.loan_amount',
		'coop_loan.loan_type',
		'coop_loan.member_id',
		'coop_loan_name.loan_type_id',
		'coop_loan.loan_application_id'
		));
		$this->db->from('coop_loan');
		$this->db->join('coop_loan_name','coop_loan.loan_type = coop_loan_name.loan_name_id','inner');
		$this->db->where("coop_loan.id = '".@$data_post['loan_id']."'");
		$row_loan = $this->db->get()->result_array();
		$row_loan = $row_loan[0];
		$loan_amount = @$row_loan['loan_amount'];
		$member_id = @$row_loan['member_id'];

		$date_arr = explode('/',@$data_post['date_transfer']);
		$date_transfer = ($date_arr[2]-543)."-".$date_arr[1]."-".$date_arr[0]." ".@$data_post['time_transfer'];
		
		//โอนเงินกู้เข้าบัญชีสหกรณ์
		if(@$data_post['pay_type'] == '1'){
			$this->save_account_transaction($data_post['account_id'],$amount_transfer,$data);
		}

		$data_insert = array();
		$data_insert['loan_id'] = $data_post['loan_id'];
		$data_insert['account_id'] = $data_post['account_id'];
		$data_insert['date_transfer'] = $date_transfer;
		$data_insert['createdatetime'] = date('Y-m-d H:i:s');
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['transfer_status'] = '0';
		$data_insert['amount_transfer'] = @$amount_transfer;
		$data_insert['pay_type'] = @$data_post['pay_type'];
		if(@$data_post['pay_type'] == '2'){
			$data_insert['dividend_bank_id'] = @$data_post['dividend_bank_id'];
			//$data_insert['dividend_bank_branch_id'] = @$data_post['dividend_bank_branch_id'];
			$data_insert['dividend_acc_num'] = @$data_post['dividend_acc_num'];
		}
		$this->db->insert('coop_loan_transfer', $data_insert);	

		$loan_transaction = array();
		$loan_transaction['loan_id'] = $data_post['loan_id'];
		$loan_transaction['loan_amount_balance'] = $loan_amount;
		$loan_transaction['transaction_datetime'] = date('Y-m-t', strtotime($date_transfer));
		$this->loan_libraries->loan_transaction($loan_transaction);

		$last_id = $this->db->insert_id();
		if(@$_FILES){
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/loan_transfer_attach/";

			if($_FILES['file_attach']['name']!=''){
				$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES['file_attach']['name']);
				@copy($_FILES["file_attach"]["tmp_name"],$output_dir.$new_file_name);

				$data_insert = array();
				$data_insert['file_name'] = $new_file_name;
				$this->db->where('id', $last_id);
				$this->db->update('coop_loan_transfer', $data_insert);
			}
		}

		$this->db->select(array(
			't1.account_chart_id',
			't2.account_chart'
		));
		$this->db->from('coop_account_match as t1');
		$this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','left');
		$this->db->where("
						t1.match_type = 'loan'
						AND t1.match_id = '".$row_loan['loan_type']."'
						");
		$row_account_match = $this->db->get()->result_array();
		$row_account_match = @$row_account_match[0];

		$account_id_transfer = '';
		if(!empty($data_post['account_id'])){
			$account_id_transfer = $data_post['account_id'];
		}else if(!empty($data_post['dividend_acc_num'])){
			$account_id_transfer = $data_post['dividend_acc_num'];
		}else{
			$account_id_transfer = '';
		}


		$data = array();
		$data['coop_account']['account_description'] = "โอนเงินให้".$row_account_match['account_chart'];
		$data['coop_account']['account_datetime'] = $date_transfer;
		$data['coop_account']['account_number'] = $account_id_transfer;
		$data['coop_account']['ref'] = $data_post['loan_id'];
		$data['coop_account']['ref_type'] = 'loan';
		$data['coop_account']['process'] = 'loan_transfer';

		$i=0;
		if(@$data_post['pay_type'] == '0'){
		$data['coop_account_detail'][$i]['account_type'] = 'credit';
		$data['coop_account_detail'][$i]['account_amount'] = @$loan_amount_transfer;
		$data['coop_account_detail'][$i]['account_chart_id'] = '10101001';
		}else{
		$data['coop_account_detail'][$i]['account_type'] = 'debit';
		$data['coop_account_detail'][$i]['account_amount'] = @$loan_amount_transfer;
		$data['coop_account_detail'][$i]['account_chart_id'] = '20105014';
		}

		if(@$data_post['pay_type'] == '0'){
			$i++;
			$data['coop_account_detail'][$i]['account_type'] = 'debit';
			$data['coop_account_detail'][$i]['account_amount'] = @$loan_amount_transfer;
			$data['coop_account_detail'][$i]['account_chart_id'] = $row_account_match['account_chart_id'];
		}else{
			$i++;
			$data['coop_account_detail'][$i]['account_type'] = 'credit';
			$data['coop_account_detail'][$i]['account_amount'] = @$loan_amount_transfer;
			$data['coop_account_detail'][$i]['account_chart_id'] = $row_account_match['account_chart_id'];
		}

		$this->account_transaction->account_process($data);

		$data_insert = array();
		$data_insert['transfer_status'] = '0';
		$this->db->where('id', @$data_post['loan_id']);
		$this->db->update('coop_loan', $data_insert);
		if($this->db->affected_rows()){
			$affected_rows++;
		}
		//exit;
		//อัพเดตสถานะ การโอนเงิน ระบบกู้เงินออนไลน์
		$loan_application_id = @$row_loan['loan_application_id'];
		if($loan_application_id != ''){
			$this->load->Model('Loan_online_transfer');
			$result_online_transfer = $this->Loan_online_transfer->update_confirm_transfer($loan_application_id);
		}
			
		if($affected_rows > 0){
			$result['status'] = 'success';
			$result['loan_id'] = $data_post['loan_id'];
			//บันทึกสำเร็จ
		}else{
			$result['status'] = 'error';
			//บันทึกไม่สำเร็จ
		}
		return $result;		
	}

	public function save_account_transaction($account_id,$pay_amount,$data){	
		$affected_rows = 0 ;
		$transaction_balance = $this->get_account_transaction_balance($account_id);
		$sum = $transaction_balance + $pay_amount;
		$data_insert = array();
		$data_insert['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".date('H:i:s') : date('Y-m-d H:i:s');
		
		if($data['uid'] != ''){
			$data_insert['transaction_list'] = 'XDM';			
			$data_insert['status_process'] = $data['platform'];
			$data_insert['mobile_uid'] = $data['uid'];
		}else{			
			$data_insert['transaction_list'] = 'XD';
			$data_insert['user_id'] = $_SESSION['USER_ID'];
		}
				
		$data_insert['transaction_withdrawal'] = '0';
		$data_insert['transaction_deposit'] = $pay_amount;
		$data_insert['transaction_balance'] = $sum;
		$data_insert['transaction_no_in_balance'] = $sum;
		$data_insert['account_id'] = $account_id;
		$this->db->insert('coop_account_transaction', $data_insert);
		$transaction_id = $this->db->insert_id();
		if($this->db->affected_rows()){
			$this->save_transaction_mobile($data,$transaction_id);
			$affected_rows++;
		}
		return $affected_rows;
    }
	
	public function save_transaction_mobile($data,$transaction_id){
		$affected_rows = 0 ;
		$data_insert = array();
		$data_insert['uid'] = $data['uid'];
		$data_insert['platform'] = $data['platform'];
		$data_insert['widthdraw_id'] = $transaction_id;
		$data_insert['name_page'] = $data['page_name'];
		$this->db->insert('transaction_mobile', $data_insert);
		if($this->db->affected_rows()){
			$affected_rows++;
		}
		return $affected_rows;
    }	
	
	public function get_loan_transfer($loan_id){		
		$result = $this->db->select(array('transfer_status','date_transfer'))
					->from('coop_loan_transfer')
					->where("loan_id = '{$loan_id}'")
					->limit(1)
					->get()->row_array();
		return $result;
    }
	
}