<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');


class Report_loan_data_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_report_loan_data(){
        $member_id = @$_GET['member_id'];
		$loan_id = @$_GET['loan_id'];
		if($member_id != '') {
			//@start ดึงข้อมูลในตารางเก็บข้อมูลรายละเอียดการขอกู้เงิน เพื่อใช้ดูข้อมูลย้อนหลัง
			$this->db->select('*');
			$this->db->from('coop_loan_report_detail');
			$this->db->where("loan_id = '".$loan_id."'");
			$rs_report_detail = $this->db->get()->result_array();
			$row_report_detail = $rs_report_detail[0];
			$arr_data['row_report_detail'] = $row_report_detail;			
			//@end ดึงข้อมูลในตารางเก็บข้อมูลรายละเอียดการขอกู้เงิน เพื่อใช้ดูข้อมูลย้อนหลัง
			
			$this->db->select('coop_mem_apply.*,
							coop_mem_type.mem_type_name,
							department.mem_group_full_name AS department_name,
							faction.mem_group_full_name AS faction_name,
							level.mem_group_full_name AS level_name,
							prename.prename_full'
							);
			$this->db->from('coop_mem_apply');
			$this->db->join("coop_mem_type","coop_mem_apply.mem_type_id = coop_mem_type.mem_type_id","left");
			$this->db->join("coop_mem_group as department","coop_mem_apply.department = department.id","left");
			$this->db->join("coop_mem_group as faction","coop_mem_apply.faction = faction.id","left");
			$this->db->join("coop_mem_group as level","coop_mem_apply.level = level.id ","left");
			$this->db->join("coop_prename as prename","coop_mem_apply.prename_id = prename.prename_id ","left");
			$this->db->where("coop_mem_apply.member_id = '".$member_id."'");
			$rs_member = $this->db->get()->result_array();
			$row_member = $rs_member[0];
			$arr_data['row_member'] = $row_member;
			
			$this->db->select(array(
				't1.*',
				't3.loan_name as loan_type_detail',
				't3.loan_type_id',
				't4.id',
				't5.bank_name',
				't6.account_name',
				't7.user_name AS admin_name'
			));
			$this->db->from('coop_loan as t1');			
			$this->db->join('coop_loan_name as t3','t1.loan_type = t3.loan_name_id','inner');
			$this->db->join("coop_loan_type as t4",'t3.loan_type_id = t4.id','inner');
			$this->db->join("coop_bank as t5",'t1.transfer_bank_id = t5.bank_id','left');
			$this->db->join("coop_maco_account as t6",'t1.transfer_account_id = t6.account_id','left');
			$this->db->join("coop_user AS t7",'t1.admin_id = t7.user_id','left');
			$this->db->where("t1.member_id = '".$member_id."' AND t1.id='".$loan_id."'");
			$this->db->order_by("t1.id DESC");
			$rs_loan = $this->db->get()->result_array();
			$row_loan =  @$rs_loan[0];
			$arr_data['row_loan'] = @$row_loan;
			$createdate_loan = date("Y-m-d", strtotime($row_loan['createdatetime']));
			if(@$_GET['dev'] == 'dev'){
				echo $this->db->last_query(); 
				echo '<hr>';
			}
			$this->db->select(array(
				//' MAX(total_paid_per_month) AS total_paid_per_month'
				'principal_payment',
				'total_paid_per_month'
			));
			$this->db->from('coop_loan_period');
			$this->db->where("loan_id='".$loan_id."' AND date_count = '31'");
			$this->db->limit(1);
			$per_month = $this->db->get()->result_array();
			//echo $this->db->last_query(); 
			if(@$row_loan['pay_type'] == '1'){
				$total_paid_per_month = @round(@$per_month[0]['principal_payment'],2);
				$pay_type_name = "แบบคงต้น";
				
				//ดอกเบี้ย 30 วัน ของจากยอดกู้เต็ม
				$date_count = 31;
				$interest_30_day = (((@$row_loan['loan_amount']*@$row_loan['interest_per_year'])/100)/365)*@$date_count;
				if(@$_GET['dev'] == 'dev'){
					echo '((('.@$row_loan['loan_amount'].'*'.@$row_loan['interest_per_year'].')/100)/365)*'.@$date_count.'<br>';
				}	
				$interest_30_day = round(@$interest_30_day, 2);
			}else{
				$total_paid_per_month = @round(@$per_month[0]['total_paid_per_month'],2);
				$pay_type_name = "แบบคงยอด";
				$interest_30_day  = 0;
			}
			//$total_paid_per_month = round(@$per_month[0]['total_paid_per_month'],-2);
			$arr_data['total_paid_per_month'] = @$total_paid_per_month;
			$arr_data['pay_type'] = @$pay_type_name;			
			$arr_data['interest_30_day'] = @$interest_30_day;
			$arr_data['pay_type_id'] = @$row_loan['pay_type'];
			
			$this->db->select('*');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2')");
			$this->db->order_by('share_date DESC');
			$this->db->limit(1);
			$row_prev_share = $this->db->get()->result_array();
			$row_prev_share = @$row_prev_share[0];
			
			$arr_data['count_share'] = $row_prev_share['share_collect'];
			$arr_data['cal_share'] = $row_prev_share['share_collect_value']; //หุ้นที่มี่
			$arr_data['share_period'] = $row_prev_share['share_period'];
			$arr_data['rules_share'] = $row_loan['loan_amount']*20/100; //หุ้นตามหลักเกณฑ์
			$arr_data['old_share'] = 0; //เดิม
			$arr_data['deposit_account_in'] = 0; //เข้าบัญชีเงินฝาก
			
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
				$account_blue_balance = @$rs_account_blue_balance[0]['transaction_balance'];
				
			}
			$arr_data['account_blue_deposit'] = @$account_blue_balance; //เงินฝากสีน้ำเงิน
			
			//////////////////////////////////////
			//รายการผ่อนชำระสหกรณ์ปัจจุบัน/เดือน
			//$month_now = date('n');
			//$year_now = date('Y')+543;
			$month_now = date('n',strtotime(@$row_loan['createdatetime']));
			$year_now = date('Y',strtotime(@$row_loan['createdatetime']))+543;
			$date_month_end = date('Y-m-t',strtotime((@$year_now-543).'-'.sprintf("%02d",@$month_now).'-01'));
			
			$this->db->select(array('coop_finance_month_detail.*','coop_finance_month_profile.*','coop_loan_name.loan_type_id'));
			$this->db->from('coop_finance_month_detail');
			$this->db->join('coop_finance_month_profile', 'coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id', 'left');
			$this->db->join('coop_loan', 'coop_finance_month_detail.loan_id = coop_loan.id', 'left');
			$this->db->join('coop_loan_name', 'coop_loan.loan_type = coop_loan_name.loan_name_id', 'left');
			$this->db->where("
						coop_finance_month_detail.member_id = '".@$member_id."'
						AND coop_finance_month_profile.profile_month = '".$month_now."'
						AND coop_finance_month_profile.profile_year = '".$year_now."'
					");
			$row_finance_month = $this->db->get()->result_array();
            //			echo $this->db->last_query()."<br>";
            //			echo $row_finance_month.'<br>';
			//
			if(!empty($row_finance_month)){
				//ออกรายการเรียกเก็บประจำเดือนแล้ว
				//echo '<pre>'; print_r($row_finance_month); echo '</pre>';
				$arr_list_loan = array();
				foreach($row_finance_month AS $key_month=>$value_month){
					//echo @$value_month['deduct_code'].'<br>';
					if(@$value_month['deduct_code'] == 'SHARE'){
						//หุ้นหักรายเดือน
						$share_month = @$value_month['pay_amount'];
						$share_month_interest = 0;
						$arr_data['share_month'] = @$share_month; //หุ้นหักรายเดือน(เงินต้น)
						$arr_data['share_month_interest'] = @$share_month_interest; //หุ้นหักรายเดือน(ดอกเบี้ย)
						
					}						
					
					if(@$value_month['deduct_code'] == 'DEPOSIT'){
						//เงินฝากหักรายเดือน
						$deposit_month =  @$value_month['pay_amount'];
						$deposit_month_interest = 0;	
						$arr_data['deposit_month'] = @$deposit_month; //เงินฝากหักรายเดือน(เงินต้น)
						$arr_data['deposit_month_interest'] = @$deposit_month_interest; //เงินฝากหักรายเดือน(ดอกเบี้ย)	
											
					}
					
					if(@$value_month['deduct_code'] == 'LOAN'){
						//echo '<pre>'; print_r($value_month); echo '</pre>';
						if(@$value_month['pay_type'] == 'principal'){
							$arr_list_loan[@$value_month['loan_type_id']]['loan_principle'] = @$value_month['pay_amount'];//ยอดที่ชำระต่อเดือน เงินต้น
						}
						
						if(@$value_month['pay_type'] == 'interest'){
							$arr_list_loan[@$value_month['loan_type_id']]['loan_interest'] = @$value_month['pay_amount'];//(ดอกเบี้ย)
						}
						$arr_list_loan[@$value_month['loan_type_id']]['loan_id'] = @$value_month['loan_id'];//loan_id
						
					}
					
					if(@$value_month['deduct_code'] == 'ATM'){
						if(@$value_month['pay_type'] == 'principal'){
							@$arr_list_loan[7]['loan_principle'] += @$value_month['pay_amount'];//ยอดที่ชำระต่อเดือน เงินต้น
						}
						
						if(@$value_month['pay_type'] == 'interest'){
							@$arr_list_loan[7]['loan_interest'] += @$value_month['pay_amount'];//(ดอกเบี้ย)
						}
						$arr_list_loan[7]['loan_id'] = @$value_month['loan_atm_id'];//loan_id
					}
					
					
				}

				
				$this->db->select(array('*'));
				$this->db->from('coop_loan_type');
				$loan_type = $this->db->get()->result_array();
				$list_loan = array();
				$loan_principle_total = 0;
				$loan_interest_total = 0;
				if(!empty($loan_type)){
					foreach($loan_type AS $key=>$value){						
						$list_loan[$value['id']]['loan_name']= $value['loan_type'];//ชื่อเงินกู้หลัก
						$list_loan[$value['id']]['loan_principle']= @$arr_list_loan[$value['id']]['loan_principle'];//ยอดที่ชำระต่อเดือน
						$list_loan[$value['id']]['loan_interest'] = @$arr_list_loan[$value['id']]['loan_interest'];//(ดอกเบี้ย)
						$loan_principle_total += @$arr_list_loan[$value['id']]['loan_principle'];
						$loan_interest_total += @$arr_list_loan[$value['id']]['loan_interest'];
					}
				}
				$arr_data['list_loan'] = @$list_loan;	
				
			}else{
				
				//ยังไม่ได้ออกรายการเรียกเก็บประจำเดือน
				$arr_list_loan = array();
				$this->db->select('setting_value');
				$this->db->from('coop_share_setting');
				$this->db->where("setting_id = '1'");
				$row = $this->db->get()->result_array();
				$row_share_value = $row[0];
				$share_value = $row_share_value['setting_value'];
			
				$this->db->select(array('deduct_id','deduct_code','deduct_detail','deduct_type','deduct_format','deposit_type_id','deposit_amount'));
				$this->db->from('coop_deduct');
				$this->db->order_by('deduct_seq ASC');
				$deduct_list = $this->db->get()->result_array();
				//echo '<pre>'; print_r($deduct_list); echo '</pre>';	
				foreach($deduct_list as $key2 => $value2){
					
					//หุ้นหักรายเดือน
					if($value2['deduct_code']=='SHARE'){
						//งดหุ้นชั่วคราว
						$check_refrain_share = 0;
						$this->db->select('*');
						$this->db->from('coop_refrain_share');
						$this->db->where("member_id = '".$row_member['member_id']."' AND type_refrain = '2' AND month_refrain = '".@$month_now."' AND year_refrain = '".@$year_now."'");
						$this->db->order_by('refrain_id DESC');			
						$rs_refrain_temporary = $this->db->get()->result_array();
						if(!empty($rs_refrain_temporary)){
							foreach($rs_refrain_temporary AS $key=>$value){
								$check_refrain_share = 1;
							}
						}
						
						//งดหุ้นถาวร
						$this->db->select('*');
						$this->db->from('coop_refrain_share');
						$this->db->where("member_id = '".$row_member['member_id']."' AND type_refrain = '1'");
						$this->db->order_by('refrain_id DESC');			
						$rs_refrain_permanent = $this->db->get()->result_array();
						if(!empty($rs_refrain_permanent)){
							foreach($rs_refrain_permanent AS $key=>$value){
								$check_refrain_share = 1;
							}
						}				
						
						//ทุนเรือนหุ้น
						if(@$row_member['apply_type_id'] == '1' && $check_refrain_share == 0){															
							$share = @$row_member['share_month'];
							$share_month = @$share;
							$share_month_interest = 0;
							$arr_data['share_month'] = @$share_month; //หุ้นหักรายเดือน(เงินต้น)
							$arr_data['share_month_interest'] = @$share_month_interest; //หุ้นหักรายเดือน(ดอกเบี้ย)
						}
						//echo $row_member['apply_type_id'].'<br>';
						/*if(@$row_member['apply_type_id'] == '1'){	
							$this->db->select(array('change_value'));
							$this->db->from('coop_change_share');
							$this->db->where("member_id = '".$row_member['member_id']."' AND change_share_status IN ('1','2')");
							$this->db->order_by("change_share_id DESC");
							$this->db->limit(1);
							$row_change_share = $this->db->get()->result_array();
							$row_change_share = @$row_change_share[0];
							$sum = 0;
							if(@$row_change_share['change_value'] != ''){
								$num_share = @$row_change_share['change_value'];
							}else{
								$this->db->select(array('share_salary'));
								$this->db->from('coop_share_rule');
								$this->db->where("salary_rule <= '".$row_member['salary']."'");
								$this->db->order_by("salary_rule DESC");
								$this->db->limit(1);
								$row_share_rule = $this->db->get()->result_array();
								$row_share_rule = @$row_share_rule[0];
								
								$num_share = @$row_share_rule['share_salary'];
							}
							$share = @$num_share*@$share_value;

							$share_month = @$share;
							$share_month_interest = 0;
							$arr_data['share_month'] = @$share_month; //หุ้นหักรายเดือน(เงินต้น)
							$arr_data['share_month_interest'] = @$share_month_interest; //หุ้นหักรายเดือน(ดอกเบี้ย)
						}
						*/
					}
					
					//เงินฝากหักรายเดือน
					if($value2['deduct_code']=='DEPOSIT'){							
						//เงินฝาก	
						$sum_deposit = 0;
						$DEPOSIT = 0;						
						$deposit_type_id = @$value2['deposit_type_id'];
						$DEPOSIT = @$value2['deposit_amount'];
						//echo $deposit_type_id.'<hr>';
						$deposit_period_count = 1;
						$deposit_balance = $DEPOSIT;
						
						$this->db->select('*');
						$this->db->from('coop_maco_account');
						$this->db->where("mem_id = '".@$row_member['member_id']."' AND type_id = '".@$deposit_type_id."'");
						$this->db->limit(1);
						$rs_account = $this->db->get()->result_array();
						$account_id = @$rs_account[0]['account_id'];
						if(!empty($account_id)){												
							if($DEPOSIT > 0){						
								$sum_deposit += @$DEPOSIT;
							}
						}

						$deposit_month =  @$sum_deposit;
						$deposit_month_interest = 0;	
						$arr_data['deposit_month'] = @$deposit_month; //เงินฝากหักรายเดือน(เงินต้น)
						$arr_data['deposit_month_interest'] = @$deposit_month_interest; //เงินฝากหักรายเดือน(ดอกเบี้ย)							
					}
				
					
					if(@$value2['deduct_code'] == 'LOAN'){										
						$LOAN = array();
						$where = '';
						$where .= " AND (coop_loan.guarantee_for_id = '' OR coop_loan.guarantee_for_id IS NULL) ";
		
						$this->db->select(
							array(
								'coop_loan.id',
								'coop_loan.loan_type',
								'coop_loan.contract_number',
								'coop_loan.loan_amount_balance',
								'coop_loan.interest_per_year',
								'coop_loan_transfer.date_transfer',
								'coop_loan_name.loan_name',
								'coop_loan.pay_type',
								'coop_loan.money_period_1',
								'coop_loan.createdatetime',
								'coop_loan.guarantee_for_id',
								'coop_loan_name.loan_type_id'
							)
						);
						$this->db->from('coop_loan');
						$this->db->join('coop_loan_transfer', 'coop_loan_transfer.loan_id = coop_loan.id', 'left');
						$this->db->join('coop_loan_name', 'coop_loan_name.loan_name_id = coop_loan.loan_type', 'inner');
						$this->db->where("
							coop_loan.loan_amount_balance > 0
							AND coop_loan.member_id = '".$row_member['member_id']."'
							AND coop_loan.loan_status = '1'
							AND coop_loan_transfer.transfer_status = '0'
							AND coop_loan.date_start_period <= '".($year_now-543)."-".sprintf("%02d",$month_now)."-".date('t',strtotime(($year_now-543)."-".$month_now."-01"))."' 
						".$where);
						$row_loan_month = $this->db->get()->result_array();
						//echo $this->db->last_query()."<br>";
						$j=0;
						
						foreach($row_loan_month as $key => $row_normal_loan){
							$this->db->select(array('deduct_id','ref_id'));
							$this->db->from('coop_deduct_detail');
							$this->db->where("deduct_id = '{$value2['deduct_id']}' AND ref_id = '{$row_normal_loan['loan_type']}'");
							$rs_deduct_detail = $this->db->get()->result_array();
							$ref_id = @$rs_deduct_detail[0]['ref_id'];
							if(!empty($ref_id)){
								$deduct_format = @$value2['deduct_format'];
								
								if($row_normal_loan['guarantee_for_id']!=''){
									$for_loan_id = $row_normal_loan['guarantee_for_id'];
								}else{
									$for_loan_id = $row_normal_loan['id'];
								}
								
								$this->db->select(
									array(
										'outstanding_balance',
										'principal_payment',
										'total_paid_per_month'
									)
								);
								$this->db->from('coop_loan_period');
								$this->db->where("loan_id = '".$for_loan_id."'");
								$this->db->limit(1);
								$row_loan_period = $this->db->get()->result_array();
								$row_principal_payment = $row_loan_period[0];
								
								$date_interesting = $date_month_end;
								$cal_loan_interest = array();
								$cal_loan_interest['loan_id'] = $row_normal_loan['id'];
								$cal_loan_interest['date_interesting'] = $date_interesting;
								$interest = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
								
								if($row_principal_payment['principal_payment'] > $row_normal_loan['loan_amount_balance']){
									$principal_payment = @$row_normal_loan['loan_amount_balance'];
									$balance = 0;
								}else{
									$principal_payment = @$row_principal_payment['principal_payment'];
									$balance = @$row_normal_loan['loan_amount_balance']-@$row_principal_payment['principal_payment'];
								}
								
								$LOAN[$j]['loan_id'] = $row_normal_loan['id'];
								$LOAN[$j]['loan_type'] = $row_normal_loan['loan_name'];
								//$LOAN[$j]['loan_type_id'] = $row_normal_loan['loan_type'];
								$LOAN[$j]['loan_type_id'] = $row_normal_loan['loan_type_id'];
								$LOAN[$j]['contract_number'] = $row_normal_loan['contract_number'];
								$LOAN[$j]['money_period_1'] = $row_normal_loan['money_period_1'];
								$LOAN[$j]['pay_loan_type'] = $row_normal_loan['pay_type'];
								if($deduct_format == '2'){
									$LOAN[$j]['text_title'] = 'ต้นเงินกู้เลขที่สัญญา';
									$LOAN[$j]['principal_payment'] = $principal_payment;
									$LOAN[$j]['interest'] = 0;
									$LOAN[$j]['total'] = $principal_payment;
									$LOAN[$j]['pay_type'] = 'principal';
								}else if($deduct_format == '1'){
									$LOAN[$j]['text_title'] = 'ดอกเบี้ยเงินกู้เลขที่สัญญา';
									$LOAN[$j]['principal_payment'] = 0;
									$LOAN[$j]['interest'] = $interest;
									$LOAN[$j]['total'] = $interest;
									$LOAN[$j]['pay_type'] = 'interest';
									$interest_arr[$row_normal_loan['id']] = $interest;
								}	
								$balance = @$row_normal_loan['loan_amount_balance']-$principal_payment-$interest;
								$LOAN[$j]['balance'] = $balance;
								
							}
							$j++;
						}
						//echo"<pre>";print_r($LOAN);echo"</pre>";
						if(!empty($LOAN)){
							foreach($LOAN as $key3 => $value3){
								$arr_list_loan[@$value3['loan_type_id']]['loan_principle'] = @$value3['principal_payment'];//ยอดที่ชำระต่อเดือน เงินต้น							
								$arr_list_loan[@$value3['loan_type_id']]['loan_interest'] = @$value3['interest'];//(ดอกเบี้ย)
								$arr_list_loan[@$value3['loan_type_id']]['loan_id'] = @$value3['loan_id'];//loan_id
							}
						}
						
						//echo '<pre>'; print_r($arr_list_loan); echo '</pre>';
					}
					
					if(@$value2['deduct_code'] == 'ATM'){					
						$ATM = 0;
						$this->db->select(array(
							't1.loan_amount_balance',
							't1.principal_per_month',
							't2.contract_number',
							't2.loan_atm_id',
							't1.date_last_pay',
							't1.loan_date'
						));
						$this->db->from('coop_loan_atm_detail as t1');
						$this->db->join('coop_loan_atm as t2', 't1.loan_atm_id = t2.loan_atm_id', 'inner');
						$this->db->where("
							t2.member_id = '".$row_member['member_id']."'
							AND t2.loan_atm_status = '1'
							AND t1.date_start_period <= '".$date_month_end."'
							AND t1.loan_status = '0'
						");
						$row_atm = $this->db->get()->result_array();
						$principal_per_month = 0;
						$loan_amount_balance = 0;
						if(!empty($row_atm)){
							foreach($row_atm as $key_atm => $value_atm){
								$loan_atm_id = @$value_atm['loan_atm_id'];
								$principal_per_month += @$value_atm['principal_per_month'];
								$loan_amount_balance += @$value_atm['loan_amount_balance'];
							}
							if(@$principal_per_month < @$loan_atm_setting['min_principal_pay_per_month']){
								$principal_per_month = @$loan_atm_setting['min_principal_pay_per_month'];
							}
							if(@$principal_per_month > @$loan_amount_balance){
								$principal_per_month = @$loan_amount_balance;
							}
							
							$cal_loan_interest = array();
							$cal_loan_interest['loan_atm_id'] = @$loan_atm_id;
							$cal_loan_interest['date_interesting'] = @$date_month_end;
							$interest = $this->loan_libraries->cal_atm_interest(@$cal_loan_interest);
							
							
							$deduct_format = @$value2['deduct_format'];
							if($deduct_format == '2'){
								@$arr_list_loan[7]['loan_principle'] += @$principal_per_month;//ยอดที่ชำระต่อเดือน เงินต้น
							}else{
								@$arr_list_loan[7]['loan_interest'] += @$interest;//(ดอกเบี้ย)
							}
							$arr_list_loan[7]['loan_id'] = @$loan_atm_id;//loan_id
						}
					}
				}
				
					
				$this->db->select(array('*'));
				$this->db->from('coop_loan_type');
				$loan_type = $this->db->get()->result_array();
				$list_loan = array();
				$loan_principle_total = 0;
				$loan_interest_total = 0;
				if(!empty($loan_type)){
					foreach($loan_type AS $key=>$value){						
						$list_loan[$value['id']]['loan_name']= $value['loan_type'];//ชื่อเงินกู้หลัก
						$list_loan[$value['id']]['loan_principle']= @$arr_list_loan[$value['id']]['loan_principle'];//ยอดที่ชำระต่อเดือน
						$list_loan[$value['id']]['loan_interest'] = @$arr_list_loan[$value['id']]['loan_interest'];//(ดอกเบี้ย)
						$loan_principle_total += @$arr_list_loan[$value['id']]['loan_principle'];
						$loan_interest_total += @$arr_list_loan[$value['id']]['loan_interest'];
					}
				}
				$arr_data['list_loan'] = @$list_loan;	
			}
			$arr_data['arr_list_loan'] = @$arr_list_loan;	
			///////////////////////////////////////////////
			
			//เงินฝาก
			$this->db->select(array('coop_maco_account.account_id','coop_maco_account.mem_id','coop_maco_account.account_name','coop_deposit_type_setting.type_name'));
			$this->db->from('coop_maco_account');
			$this->db->join("coop_deposit_type_setting","coop_maco_account.type_id = coop_deposit_type_setting.type_id AND deduct_loan = '1'","inner");
			$this->db->where("coop_maco_account.mem_id = '".@$member_id."'");
			$row_account= $this->db->get()->result_array();
			$account_list = array();
			if(!empty($row_account)){
				foreach($row_account AS $key=>$value){
					$this->db->select(array('transaction_balance','transaction_deposit'));
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$value['account_id']."'");
					$this->db->order_by('transaction_id DESC');
					$this->db->limit(1);
					$row_balance = $this->db->get()->result_array();
					//$account_balance  = @$row_balance[0]['transaction_balance'];
					$account_balance  = @$row_balance[0]['transaction_deposit'];
					
					$account_list[$value['account_id']]['account_id'] =  @$value['account_id'];
					$account_list[$value['account_id']]['account_name'] =  @$value['account_name'];
					$account_list[$value['account_id']]['account_balance'] =  @$account_balance;
					//print_r($this->db->last_query());
				}
			}
			$arr_data['account_list'] = @$account_list;			
			
			//ปิดสัญญาเดิม
			//
			/*
			$this->db->select(array('t1.*','t3.loan_type_code'));
			$this->db->from("coop_loan as t1");
			$this->db->join("coop_loan_name as t2",'t1.loan_type = t2.loan_name_id','inner');
			$this->db->join("coop_loan_type as t3",'t2.loan_type_id = t3.id','inner');
			$this->db->where("t1.id = '".@$loan_id."'");
			$rs_loan = $this->db->get()->result_array();
			$rs_loan = $rs_loan[0];	
			*/			
			
			$date_interesting = date('Y-m-d');
			$list_old_loan = array();
			
			$this->db->select(array('t1.*',
									't2.contract_number',
									't2.loan_amount_balance',
									't2.id'
									));
			$this->db->from("coop_loan_prev_deduct t1");
			$this->db->join("coop_loan t2",'t1.ref_id = t2.id','inner');
			$this->db->where("t1.loan_id = '".@$loan_id."' AND t1.data_type = 'loan'");
			$row = $this->db->get()->result_array();
			$index = 0;
			if(@$_GET['dev'] == 'dev'){
				echo 'coop_loan_prev_deduct<br>';
				print_r($this->db->last_query()); //exit;
			}
			$extra_debt = array();
			foreach($row as $key => $value){
				$list_old_loan[$index]['contract_number'] = @$value['contract_number'];
				
				$extra_debt_amount	= 0;//หนี้ห้อย
				/*if(date("Y-m", strtotime($rs_loan[0]['createdatetime']) ) != date("Y-m") ){
					$month = date("m", strtotime("+1 months", strtotime($rs_loan[0]['createdatetime'])) );
					if($month=="01"){
						$year = date("Y", strtotime($rs_loan[0]['createdatetime']) ) + 543 + 1;
					}else{
						$year = date("Y", strtotime($rs_loan[0]['createdatetime']) ) + 543;
					}
					
					$this->db->select('profile_id');
					$this->db->from('coop_finance_month_profile');
					$this->db->where("profile_month = '".(int)$month."' AND profile_year = '".$year."' ");
					$profile_id = $this->db->get()->result_array()[0]['profile_id'];
					if(@$_GET['dev'] == 'dev'){
						echo '<hr>';
						print_r($this->db->last_query());
					}
					$this->db->select("sum(pay_amount) as sum_of_pay_amount");
					$finance_month_detail = $this->db->get_where("coop_finance_month_detail", array(
						"profile_id" => $profile_id,
						"member_id" => $rs_loan[0]['member_id'],
						"loan_id" => $value['id'],
						"pay_type" => "principal",
						"run_status" => 0
					))->result_array()[0];
					if(@$_GET['dev'] == 'dev'){
						echo '<hr>';
						print_r($this->db->last_query());
						echo '<hr>'.date("Y-m", strtotime($rs_loan[0]['createdatetime']) ).' !='. date("Y-m").'<br>';
					}
					if($finance_month_detail && $rs_loan[0]['loan_status']==0){
						$extra_debt['total_princical'] += $finance_month_detail['sum_of_pay_amount'];
						$extra_debt_amount	= $finance_month_detail['sum_of_pay_amount'];
					}
						
				}
				*/

				if($value['pay_type'] == 'principal'){	
					if(@$_GET['dev'] == 'dev'){
						echo 'pay_amount='.@$value['pay_amount'].'<br>';
						echo 'extra_debt_amount='.@$extra_debt_amount.'<br>';
					}				
					$list_old_loan[$index]['loan_id'] = @$value['id'];
					$list_old_loan[$index]['loan_amount_balance'] = @$value['pay_amount'] - $extra_debt_amount;
					$list_old_loan[$index]['loan_interest_amount'] = 0;
				}else{
					$list_old_loan[$index]['loan_id'] = @$value['id'];
					$list_old_loan[$index]['loan_amount_balance'] = @$value['pay_amount'] - $extra_debt_amount - @$value['interest_amount'];
					$list_old_loan[$index]['loan_interest_amount'] = @$value['interest_amount'];
					/*$list_old_loan[$index]['loan_amount_balance'] = @$value['loan_amount_balance'];
					$cal_loan_interest = array();
					$cal_loan_interest['loan_id'] = @$value['ref_id'];
					$cal_loan_interest['date_interesting'] = $this->center_function->ConvertToSQLDate(@$date_interesting);
					$interest_data = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
					$list_old_loan[$index]['loan_interest_amount'] = @$interest_data;
					*/
				}
				$index++;
			}
			
			//ปิดปัญชีกู้ฉุกเฉิน ATM
			$this->db->select(array('t1.*',
									't2.contract_number',
									't2.total_amount_approve',
									't2.total_amount_balance',
									't2.loan_atm_id'
									));
			$this->db->from("coop_loan_prev_deduct t1");
			$this->db->join("coop_loan_atm t2",'t1.ref_id = t2.loan_atm_id','inner');
			$this->db->where("t1.loan_id = '".@$loan_id."' AND t1.data_type = 'atm'");
			$row = $this->db->get()->result_array();
			if(@$_GET['dev'] == 'dev'){
				print_r($this->db->last_query()); //exit;
				echo '<pre>'; print_r(@$row); echo '</pre>';
			}
			
			foreach($row as $key => $value){
				$list_old_loan[$index]['contract_number'] = @$value['contract_number'];
				if($value['pay_type'] == 'principal'){					
					$list_old_loan[$index]['loan_id'] = @$value['loan_atm_id'];
					//$list_old_loan[$index]['loan_amount_balance'] = @$value['total_amount_approve'] - @$value['total_amount_balance'];
					$list_old_loan[$index]['loan_amount_balance'] = @$value['pay_amount'] - @$value['interest_amount'];
					$list_old_loan[$index]['loan_interest_amount'] = 0;
				}else{
					$list_old_loan[$index]['loan_id'] = @$value['loan_atm_id'];
					//$list_old_loan[$index]['loan_amount_balance'] = @$value['total_amount_approve'] - @$value['total_amount_balance'];
					$list_old_loan[$index]['loan_amount_balance'] = @$value['pay_amount'] - @$value['interest_amount'];
					/*$cal_loan_interest = array();
					//$cal_loan_interest['loan_id'] = @$value['ref_id'];
					$cal_loan_interest['loan_atm_id'] = @$value['ref_id'];
					$cal_loan_interest['date_interesting'] = $this->center_function->ConvertToSQLDate(@$date_interesting);
					$interest_atm = $this->loan_libraries->cal_atm_interest_deduct($cal_loan_interest);
					$list_old_loan[$index]['loan_interest_amount'] = @$interest_atm;
					*/
					$list_old_loan[$index]['loan_interest_amount'] = @$value['interest_amount'];
				}
				$index++;
			}	
			
			if(@$_GET['dev'] == 'dev'){
					echo '<pre>'; print_r(@$list_old_loan); echo '</pre>';
			}
			
			$arr_data['list_old_loan'] = @$list_old_loan;
			$total_principal = 0;
			$total_interest = 0;
			$total_loan_balance = 0;
			if(!empty($list_old_loan)){
				foreach($list_old_loan AS $key => $value){
					$total_principal += @$value['loan_amount_balance'];
					$total_interest += @$value['loan_interest_amount'];
					$total_loan_balance += @$value['loan_amount_balance']+@$value['loan_interest_amount'];
				}
			}
			
			$arr_data['existing_loan'] = @$total_loan_balance; //หักเงินกู้เดิม
			$arr_data['principal_load'] = @$total_principal;//ภาระเงินต้น
			$arr_data['interest_burden'] = @$total_interest;//ภาระดอกเบี้ย
			$arr_data['extra_debt'] = @$extra_debt;//หนี้ห้อย
			
			$loan_amount = @$row_loan['loan_amount']; //วงเงินที่ขออนุมัติ		
			
			$month_receipt = date("m", strtotime($row_loan['createdatetime']));
			$year_receipt = date("Y", strtotime($row_loan['createdatetime']));
			
			$this->db->select(array('deduct_id','deduct_code','deduct_detail','deduct_type','deduct_format','deposit_type_id','deposit_amount'));
			$this->db->from('coop_deduct');
			$this->db->order_by('deduct_seq ASC');
			$deduct_list = $this->db->get()->result_array();
			$data_arr['deduct_list'] = @$deduct_list;
			//
			
			$deductible =  0 ;			
			//ค่าธรรมเนียม 
			$this->db->select(array('*'));
			$this->db->from('coop_loan_deduct');
			$this->db->where("loan_id = '{$loan_id}'");
			$rs_deduct = $this->db->get()->result_array();
			//echo $loan_id.'<hr>';
			//echo '<pre>'; print_r($rs_deduct); echo '</pre>';
			if(!empty($rs_deduct)){
				foreach($rs_deduct AS $key=>$row_deduct){
					if(!in_array($row_deduct['loan_deduct_list_code'], array( 'deduct_pay_prev_loan', 'deduct_cheque'))){
						$deductible += @$row_deduct['loan_deduct_amount'];
					}
					
					if($row_deduct['loan_deduct_list_code'] == 'deduct_loan_fee'){
						$arr_data['deduct_loan_fee'] = @$row_deduct['loan_deduct_amount'];
					}
					
					if($row_deduct['loan_deduct_list_code'] == 'deduct_person_guarantee'){
						//มีการกำหนดกรณีไม่ผ่านเกณฑ์
						$arr_data['deduct_person_guarantee'] = @$row_deduct['loan_deduct_amount'];
					}

					if($row_deduct['loan_deduct_list_code'] == 'deduct_insurance'){
						//เบี้ยประกัน
						$arr_data['deduct_insurance'] = @$row_deduct['loan_deduct_amount'];
					}

					if($row_deduct['loan_deduct_list_code'] == 'deduct_cheque'){
                        $arr_data['deduct_cheque'] = @$row_deduct['loan_deduct_amount'];
                    }
				}
			}
			//exit;			
			
			$arr_data['deductible'] =  @$deductible; //รายการหัก
			$total_amount = @$loan_amount - @$total_loan_balance - @$deductible - @$arr_data['deduct_cheque'];
			$arr_data['total_amount'] = @$total_amount;
			
			$arr_data['total_paid_per_month'] =  @$total_paid_per_month; //รวมชำระต่องวด
			
			//รายการรับเงิน	
			$arr_receiving_money = array();
			//ชำระหนี้สถาบันการเงิน
			$this->db->select(array('*'));
			$this->db->from('coop_loan_financial_institutions');
			$this->db->where("loan_id = '".@$loan_id."'");
			$this->db->order_by("order_by ASC");
			$rs_financial_institutions = $this->db->get()->result_array();
			
			$i=0;
			$financial_amount = 0;
			foreach($rs_financial_institutions AS $key=>$row_financial_institutions){
				$arr_receiving_money[$i]['transfer_type'] = 'จ่ายธนาคาร'.@$row_financial_institutions['financial_institutions_name'];
				$arr_receiving_money[$i]['total_received'] = @$row_financial_institutions['financial_institutions_amount'];
				$financial_amount += @$row_financial_institutions['financial_institutions_amount'];
				$i++;
			}
			
			//การจ่ายเงินกู้
			$transfer_type = array('0'=>'เงินสด','1'=>'โอนเงินบัญชีสหกรณ์','2'=>'โอนเงินบัญชี','3' => 'พร้อมเพย์', '4' => 'เช็คเงินสด');
			if(@$row_loan['transfer_type'] == '2'){
				$transfer_type_description = '';
				$transfer_type_description .= @$row_loan['bank_name'];
				$transfer_type_description .= '<br> เลขที่บัญชี '.@$row_loan['transfer_bank_account_id'];
			}else if(@$row_loan['transfer_type'] == '1'){
				$transfer_type_description = ' '.@$row_loan['transfer_account_id'].':'.@$row_loan['account_name'];
			}else{
				$transfer_type_description = '';
			}
			$text_transfer_type = @$transfer_type[@$row_loan['transfer_type']].@$transfer_type_description;
			
			//ยอดเงินที่จะได้รับโดยประมาณ
			$this->db->select(array('estimate_receive_money'));
			$this->db->from('coop_loan_deduct_profile');
			$this->db->where("loan_id = '".@$loan_id."'");
			$loan_deduct_profile = $this->db->get()->result_array();
			$estimate_receive_money = @$loan_deduct_profile[0]['estimate_receive_money'];			
			
			$arr_receiving_money[$i]['transfer_type'] = @$text_transfer_type;
			$arr_receiving_money[$i]['total_received'] = @$estimate_receive_money-@$financial_amount;
			
			$arr_data['receiving_money'] = $arr_receiving_money;		
					
			$arr_data['total_month'] = @$share_month+@$deposit_month+@$loan_principle_total;//รวมทั้งสิ้น รายการผ่อนชำระสหกรณ์ปัจจุบัน/เดือน
			$arr_data['total_month_interest'] = @$share_month_interest+@$deposit_month_interest+@$loan_interest_total;//รวมทั้งสิ้น รายการผ่อนชำระสหกรณ์ปัจจุบัน/เดือน(ดอกเบี้ย)
			
			//บุคคลค้ำประกัน
			$this->db->select(array(
				't1.*',
				't3.member_id',
				't3.firstname_th',
				't3.lastname_th',
				't3.mem_group_id',
				't3.salary',
				't3.birthday',
				't3.member_date',
				't3.share_month',
				't4.mem_group_name',
				't5.prename_full'
			));
			$this->db->from('coop_loan_guarantee_person as t1');
			$this->db->join('coop_mem_apply as t3','t1.guarantee_person_id = t3.member_id','inner');
			$this->db->join("coop_mem_group as t4", "t3.level = t4.id", "left");
			$this->db->join("coop_prename as t5", "t3.prename_id = t5.prename_id", "left");
			$this->db->where("t1.loan_id = '{$loan_id}' AND t3.member_status <> '3'");
			$this->db->order_by("t1.id ASC");
			$rs_guarantee = $this->db->get()->result_array();
			foreach($rs_guarantee AS $key=>$row_guarantee){
				$this->db->from('coop_loan_guarantee_person as t1');
				$this->db->join('coop_loan as t2','t1.loan_id = t2.id ','inner');
				$this->db->where("
					t1.guarantee_person_id = '".@$row_guarantee['guarantee_person_id']."' 
					AND t2.loan_status IN('1','2')
				");
				$rs_count_guarantee = $this->db->get()->result_array();
				$n=0;
				foreach($rs_count_guarantee as $key_count => $row_count_guarantee){
					$n++;
				}
				@$rs_guarantee[$key]['count_guarantee'] = @$n;

				$share = $this->db->select("share_period, share_collect_value")->from("coop_mem_share")->where("share_date <= '".$row_loan["createdatetime"]."' AND member_id = '".$row_guarantee['guarantee_person_id']."' AND share_status IN (1,5,6)")->order_by("share_date DESC")->get()->row();
				$rs_guarantee[$key]['share_balance'] = $share->share_collect_value;
				$rs_guarantee[$key]['share_period'] = $share->share_period;

				//ข้อมูลค้ำประกันเงินกู้
				$guarantors_raw = $this->db->select("t3.member_id, t2.contract_number, t3.firstname_th, t3.lastname_th, t4.prename_full, t1.loan_id")
							->from("coop_loan_guarantee_person as t1")
							->join("coop_loan as t2", "t1.loan_id = t2.id", "INNER")
							->join("coop_mem_apply as t3", "t2.member_id = t3.member_id", "LEFT")
							->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "LEFT")
							->where("t1.guarantee_person_id = '".$row_guarantee['guarantee_person_id']."'")
							->get()->result_array();
				$guarantors = array();
				foreach($guarantors_raw as $data) {
					$transaction = $this->db->select("*")->from("coop_loan_transaction")->where("loan_id = '".$data["loan_id"]."' AND transaction_datetime <= '".$row_loan["createdatetime"]."'")->order_by("transaction_datetime DESC")->get()->row();
					if($transaction->loan_amount_balance > 0) {
						$guarantors[] = $data;
					}
				}

				$rs_guarantee[$key]["guarantors"] = $guarantors;

                //หุ้นค้ำประกัน
                $res_guarantee =  $this->db->get_where('coop_loan_guarantee', array('loan_id' => $loan_id))->row_array();
                $arr_data['g_share_amt'] = $res_guarantee['amount'];


                $loans = array();
				$loan_raw = $this->db->select("t1.id as loan_id, t1.approve_date, t1.contract_number, t1.date_last_interest, t1.loan_amount, t1.money_per_period, t1.period_amount, t1.period_now")
							->from("coop_loan as t1")
							->where("t1.member_id = '".$row_guarantee['guarantee_person_id']."' AND t1.loan_status IN (1,4,6,7,8) AND t1.id != '".$_GET["loan_id"]."'")
							->get()->result_array();
				foreach($loan_raw as $loan) {
					$transaction = $this->db->select("*")->from("coop_loan_transaction")->where("loan_id = '".$loan["loan_id"]."' AND transaction_datetime <= '".$row_loan["createdatetime"]."'")->order_by("transaction_datetime DESC")->get()->row();
					if($transaction->loan_amount_balance > 0) {
						$loan["balance"] = $transaction->loan_amount_balance;
						$loans[] = $loan;
					}
				}
				$rs_guarantee[$key]["loans"] = $loans;

			}	
			$arr_data['row_guarantee'] = @$rs_guarantee;	
			
			//รายการซื้อ	
			$this->db->select(array('*'));
			$this->db->from('coop_loan_deduct_list');
			$this->db->where("loan_deduct_list_code != 'deduct_pay_prev_loan' AND loan_deduct_status = 1");
			$this->db->order_by('run_id ASC');
			$row = $this->db->get()->result_array();
			$arr_data['loan_deduct_list'] = $row;	
			
			$this->db->select(array(
				'loan_deduct_list_code',
				'loan_deduct_amount'
			));
			$this->db->from('coop_loan_deduct');
			$this->db->where("loan_id = '".$loan_id."'");
			$row = $this->db->get()->result_array();
			$loan_deduct = array();
			foreach($row as $key => $value){
				$loan_deduct[$value['loan_deduct_list_code']] = $value['loan_deduct_amount'];
			}
			$arr_data['loan_deduct'] = $loan_deduct;
			
			//ค่าใช้จ่าย
            //			$this->db->from('coop_loan_cost');
            //			$this->db->where("loan_id = '".$loan_id."' AND member_id = '".$member_id."'");
            //			$this->db->limit(1);
            //			$rs_cost = $this->db->get()->result_array();
            //			$row_cost = @$rs_cost[0];
            //			$arr_data['school_benefits'] = $row_cost['school_benefits'];
            //			$arr_data['saving'] = @$row_cost['saving'];
            //			$arr_data['ch_p_k'] = @$row_cost['ch_p_k'];
            //			$arr_data['pension'] = @$row_cost['pension'];
            //			$arr_data['k_b_k'] = @$row_cost['k_b_k'];
            //			$arr_data['other'] = @$row_cost['other'];

            //รายได้
            $sql = "SELECT
				*, 
				IFNULL((select coop_income_loan_detail.income_value from coop_income_loan_detail where coop_income_loan_detail.loan_id = '".$loan_id."' and coop_income_loan_detail.income_id = coop_income.id), 0) as income_value
			FROM
				`coop_income`
			ORDER BY
				`seq` ASC";

            if($_GET['sql'] == 'show'){
                echo $sql; exit;
            }
            $arr_data['income'] = $this->db->query($sql)->result_array();

            //ค่าใช้จ่าย
            $arr_data['loan_cost_code'] = array();
            $this->db->select('outgoing_code, outgoing_name, loan_cost_amount');
            $this->db->from("coop_outgoing");
            $this->db->join("coop_loan_cost_mod", "coop_outgoing.outgoing_code=coop_loan_cost_mod.loan_cost_code", "inner");
            $this->db->where("loan_id = '".$loan_id."' AND member_id = '".$member_id."'");
            $rs_cost = $this->db->get()->result_array();
            $arr_data['loan_cost_code'] = $rs_cost;
            //            foreach ($rs_cost as $key => $val){
            //                $arr_data['loan_cost_code'] = $val['loan_cost_amount'];
            //            }

			//อสังหาทรัพย์ค้ำประกัน
			$this->db->select(array('t1.*',
									't2.province_name',
									't3.amphur_name',
									't4.district_name'
								));
			$this->db->from("coop_loan_guarantee_real_estate t1");
			$this->db->join("coop_province t2","t1.province_id = t2.province_id","left");
			$this->db->join("coop_amphur t3","t1.amphur_id = t3.amphur_id","left");
			$this->db->join("coop_district t4","t1.district_id = t4.district_id","left");
			$this->db->where("t1.loan_id = '".$loan_id."'");
			$rs_real_estate = $this->db->get()->result_array();
			$arr_data['row_real_estate'] = @$rs_real_estate[0];
			
			//$member_id = @$_GET['member_id'];
			//$loan_id = @$_GET['loan_id'];
			//@start ข้อมูลประกันชีวิต
			$rs_cremation_type = $this->db->select('import_cremation_type,import_amount_balance')
			->from('coop_life_insurance_cremation')
			->where("member_id = '".$member_id."' AND loan_id = '".$loan_id."'")
			->get()->result_array();
			$cremation_type_1 = 0;
			$cremation_type_2 = 0;
			foreach($rs_cremation_type AS $key=>$row_cremation_type){
				//ชสอ
				if($row_cremation_type['import_cremation_type'] == '1'){
					$cremation_type_1 = @$row_cremation_type['import_amount_balance'];
				}
				
				//สสอค
				if($row_cremation_type['import_cremation_type'] == '2'){
					$cremation_type_2 = @$row_cremation_type['import_amount_balance'];
				}
			}
			$arr_data['cremation_type_1'] = @$cremation_type_1;
			$arr_data['cremation_type_2'] = @$cremation_type_2;			
				
			$row_life_insurance = $this->db->select('insurance_year,insurance_date,insurance_amount,insurance_premium,insurance_new,insurance_old')
			->from('coop_life_insurance')
			->where("member_id = '".$member_id."' AND loan_id = '".$loan_id."'")
			->limit(1)
			->get()->result_array();
			
			$arr_data['life_insurance_4'] = @$row_life_insurance[0]['insurance_old'];
			$arr_data['life_insurance_5'] = @$row_life_insurance[0]['insurance_new'];	
			$arr_data['life_insurance_6'] = @$row_life_insurance[0]['insurance_amount'];	
			//@end ข้อมูลประกันชีวิต

			//ข้อมูลค้ำประกันเงินกู้
			$guarantors_raw = $this->db->select("t3.member_id, t2.contract_number, t3.firstname_th, t3.lastname_th, t4.prename_full, t1.loan_id")
									->from("coop_loan_guarantee_person as t1")
									->join("coop_loan as t2", "t1.loan_id = t2.id", "INNER")
									->join("coop_mem_apply as t3", "t2.member_id = t3.member_id", "LEFT")
									->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "LEFT")
									->where("t1.guarantee_person_id = '".$member_id."'")
									->get()->result_array();
			$guarantors = array();
			foreach($guarantors_raw as $data) {
				$transaction = $this->db->select("*")->from("coop_loan_transaction")->where("loan_id = '".$data["loan_id"]."' AND transaction_datetime <= '".$row_loan["createdatetime"]."'")->order_by("transaction_datetime DESC")->get()->row();
				if($transaction->loan_amount_balance > 0) {
					$guarantors[] = $data;
				}
			}

			$arr_data["guarantors"] = $guarantors;

			$loans = array();
			$loan_raw = $this->db->select("t1.id as loan_id, t1.approve_date, t1.contract_number, t1.date_last_interest, t1.loan_amount, t1.money_per_period, t1.period_amount, t1.period_now")
									->from("coop_loan as t1")
									->where("t1.member_id = '".$member_id."' AND t1.loan_status IN (1,4,6,7,8) AND t1.id != '".$_GET["loan_id"]."'")
									->get()->result_array();
			foreach($loan_raw as $loan) {
				// 2019-12-31 00:00:00
				$transaction = $this->db->select("*")->from("coop_loan_transaction")->where("loan_id = '".$loan["loan_id"]."' AND transaction_datetime <= '".$row_loan["createdatetime"]."'")->order_by("transaction_datetime DESC")->get()->row();
				if($transaction->loan_amount_balance > 0) {
					$loan["balance"] = $transaction->loan_amount_balance;
					$loans[] = $loan;
				}
			}
			$arr_data["loans"] = $loans;

			$board_raw = $this->db->select("*")->from("coop_loan_board")->get()->result_array();
			$boards = array();
			foreach($board_raw as $board) {
				if($board["level"] != "board") {
					$boards[$board["level"]] = $board;
				} else {
					$boards["boards"][] = $board;
				}
			}
			$arr_data["boards"] = $boards;
		}

		$arr_data['person_guarantee'] = $this->db->select('person_guarantee')->from('coop_term_of_loan')->where('type_id', $row_loan['loan_type'])->order_by('start_date', 'desc')->limit(1)->get()->row_array()['person_guarantee'];
        return $arr_data;
    }

    public function get_report_loan_data_1(){
        $member_id = @$_GET['member_id'];
		$loan_id = @$_GET['loan_id'];
		if($member_id != '') {
			//@start ดึงข้อมูลในตารางเก็บข้อมูลรายละเอียดการขอกู้เงิน เพื่อใช้ดูข้อมูลย้อนหลัง
			$this->db->select('*');
			$this->db->from('coop_loan_report_detail');
			$this->db->where("loan_id = '".$loan_id."'");
			$rs_report_detail = $this->db->get()->result_array();
			$row_report_detail = $rs_report_detail[0];
			$arr_data['row_report_detail'] = $row_report_detail;			
			//@end ดึงข้อมูลในตารางเก็บข้อมูลรายละเอียดการขอกู้เงิน เพื่อใช้ดูข้อมูลย้อนหลัง
			
			$this->db->select('coop_mem_apply.*,
							coop_mem_type.mem_type_name,
							department.mem_group_full_name AS department_name,
							faction.mem_group_full_name AS faction_name,
							level.mem_group_full_name AS level_name,
							 prename.prename_full'
							);
			$this->db->from('coop_mem_apply');
			$this->db->join("coop_mem_type","coop_mem_apply.mem_type_id = coop_mem_type.mem_type_id","left");
			$this->db->join("coop_mem_group as department","coop_mem_apply.department = department.id","left");
			$this->db->join("coop_mem_group as faction","coop_mem_apply.faction = faction.id","left");
			$this->db->join("coop_mem_group as level","coop_mem_apply.level = level.id ","left");
			$this->db->join("coop_prename as prename","coop_mem_apply.prename_id = prename.prename_id ","left");
			$this->db->where("coop_mem_apply.member_id = '".$member_id."'");
			$rs_member = $this->db->get()->result_array();
			$row_member = $rs_member[0];
			$arr_data['row_member'] = $row_member;
			
			$this->db->select(array(
				't1.*',
				't1.id as loan_id',
				't3.loan_name as loan_type_detail',
				't3.loan_type_id',
				't4.id',
				't5.bank_name',
				't6.account_name',
				't7.user_name AS admin_name'
			));
			$this->db->from('coop_loan as t1');			
			$this->db->join('coop_loan_name as t3','t1.loan_type = t3.loan_name_id','inner');
			$this->db->join("coop_loan_type as t4",'t3.loan_type_id = t4.id','inner');
			$this->db->join("coop_bank as t5",'t1.transfer_bank_id = t5.bank_id','left');
			$this->db->join("coop_maco_account as t6",'t1.transfer_account_id = t6.account_id','left');
			$this->db->join("coop_user AS t7",'t1.admin_id = t7.user_id','left');
			$this->db->where("t1.member_id = '".$member_id."' AND t1.id='".$loan_id."'");
			$this->db->order_by("t1.id DESC");
			$rs_loan = $this->db->get()->result_array();
			$row_loan =  @$rs_loan[0];
			$arr_data['row_loan'] = @$row_loan;
			$createdate_loan = date("Y-m-d", strtotime($row_loan['createdatetime']));
			if(@$_GET['dev'] == 'dev'){
				echo $this->db->last_query(); 
				echo '<hr>';
			}
			$this->db->select(array(
				//' MAX(total_paid_per_month) AS total_paid_per_month'
				'principal_payment',
				'total_paid_per_month'
			));
			$this->db->from('coop_loan_period');
			$this->db->where("loan_id='".$loan_id."' AND date_count = '31'");
			$this->db->limit(1);
			$per_month = $this->db->get()->result_array();
			//echo $this->db->last_query(); 
			if(@$row_loan['pay_type'] == '1'){
				$total_paid_per_month = @round(@$per_month[0]['principal_payment'],2);
				$pay_type_name = "แบบคงต้น";
				
				//ดอกเบี้ย 30 วัน ของจากยอดกู้เต็ม
				$date_count = date_diff(date_create($row_loan['createdatetime']),date_create($row_loan['date_start_period']))->format("%a");
				$interest_30_day = (((@$row_loan['loan_amount']*@$row_loan['interest_per_year'])/100)/365)*@$date_count;
				if(@$_GET['dev'] == 'dev'){
					echo '((('.@$row_loan['loan_amount'].'*'.@$row_loan['interest_per_year'].')/100)/365)*'.@$date_count.'<br>';
				}	
				$interest_30_day = round(@$interest_30_day, 2);
			}else{
				$total_paid_per_month = @round(@$per_month[0]['total_paid_per_month'],2);
				$pay_type_name = "แบบคงยอด";
				$interest_30_day  = 0;
			}
			//$total_paid_per_month = round(@$per_month[0]['total_paid_per_month'],-2);
			$arr_data['total_paid_per_month'] = @$total_paid_per_month;
			$arr_data['pay_type'] = @$pay_type_name;			
			$arr_data['interest_30_day'] = @$interest_30_day;
			$arr_data['pay_type_id'] = @$row_loan['pay_type'];
			
			$this->db->select('*');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2')");
			$this->db->order_by('share_date DESC');
			$this->db->limit(1);
			$row_prev_share = $this->db->get()->result_array();
			$row_prev_share = @$row_prev_share[0];
			
			$arr_data['count_share'] = $row_prev_share['share_collect'];
			$arr_data['cal_share'] = $row_prev_share['share_collect_value']; //หุ้นที่มี่
			$arr_data['share_period'] = $row_prev_share['share_period'];
			$arr_data['rules_share'] = $row_loan['loan_amount']*20/100; //หุ้นตามหลักเกณฑ์
			$arr_data['old_share'] = 0; //เดิม
			$arr_data['deposit_account_in'] = 0; //เข้าบัญชีเงินฝาก
			
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
				$account_blue_balance = @$rs_account_blue_balance[0]['transaction_balance'];
				
			}
			$arr_data['account_blue_deposit'] = @$account_blue_balance; //เงินฝากสีน้ำเงิน
			
			//////////////////////////////////////
			//รายการผ่อนชำระสหกรณ์ปัจจุบัน/เดือน
			//$month_now = date('n');
			//$year_now = date('Y')+543;
			$month_now = date('n',strtotime(@$row_loan['createdatetime']));
			$year_now = date('Y',strtotime(@$row_loan['createdatetime']))+543;
			$date_month_end = date('Y-m-t',strtotime((@$year_now-543).'-'.sprintf("%02d",@$month_now).'-01'));
			
			$this->db->select(array('coop_finance_month_detail.*','coop_finance_month_profile.*','coop_loan_name.loan_type_id'));
			$this->db->from('coop_finance_month_detail');
			$this->db->join('coop_finance_month_profile', 'coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id', 'left');
			$this->db->join('coop_loan', 'coop_finance_month_detail.loan_id = coop_loan.id', 'left');
			$this->db->join('coop_loan_name', 'coop_loan.loan_type = coop_loan_name.loan_name_id', 'left');
			$this->db->where("
						coop_finance_month_detail.member_id = '".@$member_id."'
						AND coop_finance_month_profile.profile_month = '".$month_now."'
						AND coop_finance_month_profile.profile_year = '".$year_now."'
					");
			$row_finance_month = $this->db->get()->result_array();
			//			echo $this->db->last_query()."<br>";
			//			echo $row_finance_month.'<br>';
			//
			if($this->Setting_model->get("display_deduct_in_coop_report_loan_detail_preview")==1){
				//share
				$share_month 						= @$arr_data['row_member']['share_month'];
				$share_month_interest 				= 0;
				$arr_data['share_month'] 			= @$share_month; //หุ้นหักรายเดือน(เงินต้น)
				$arr_data['share_month_interest'] 	= @$share_month_interest; //หุ้นหักรายเดือน(ดอกเบี้ย)
				$this->db->order_by("share_date", "DESC");
				$this->db->limit(1);
				$arr_data['share_month_balance']	= $this->db->get_where("coop_mem_share", array("member_id" => $row_member['member_id'], "share_date <= " => $row_loan['createdatetime']))->result_array()[0]['share_collect_value'];

				//เงินฝากหักรายเดือน
				$sum_deposit = 0;
				$deposit_month =  0;
				$deposit_month_interest = 0;
				$maco_account = $this->db->get_where("coop_maco_account", array(
					"mem_id" => $row_member['member_id']
				));
				foreach ($maco_account->result_array() as $key => $value) {
					$this->db->select("SUM(pay_amount) as deposit_month");
					$this->db->join("coop_finance_month_profile", "coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id", "inner");
					$deposit_month_transaction = $this->db->get_where("coop_finance_month_detail", array(
						"member_id" 												=> $row_member['member_id'],
						"deduct_code"												=> "DEPOSIT",
						"deposit_account_id"										=> $value['account_id'],
						"coop_finance_month_profile.profile_year" 					=> (date("Y", strtotime($row_loan['createdatetime'])) + 543),
						"coop_finance_month_profile.profile_month" 					=> (date("n", strtotime($row_loan['createdatetime'])))
					))->row_array()['deposit_month'];
					if(empty($deposit_month_transaction)){
						$this->db->order_by("deduction_year desc, deduction_month desc");
						$deposit_month_transaction = $this->db->get_where("coop_deposit_month_transaction", array(
							"account_id" => $value['account_id']
						))->row_array()['total_amount'];
					}
					if(empty($deposit_month_transaction)){
						$this->db->select("SUM(pay_amount) as deposit_month");
						$this->db->join("coop_finance_month_profile", "coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id", "inner");
						$deposit_month_transaction = $this->db->get_where("coop_finance_month_detail", array(
							"member_id" 												=> $row_member['member_id'],
							"deduct_code"												=> "DEPOSIT",
							"deposit_account_id"										=> $value['account_id'],
							"coop_finance_month_profile.profile_year" 					=> (date("Y", strtotime("-1 month", strtotime($row_loan['createdatetime']))) + 543),
							"coop_finance_month_profile.profile_month" 					=> (date("n", strtotime("-1 month", strtotime($row_loan['createdatetime']))))
						))->row_array()['deposit_month'];
					}

					if($deposit_month_transaction>0){
						$deposit_month += $deposit_month_transaction;
					}

					$this->db->order_by("transaction_time desc, transaction_id desc");
					$this->db->limit(1);
					$transaction_balance = $this->db->get_where("coop_account_transaction", array(
						"account_id" => $value['account_id'],
						"transaction_time" => $row_loan['createdatetime']
					))->row_array()['transaction_balance'];
					$sum_deposit += $transaction_balance;
				}
				$arr_data['deposit_month'] = @$deposit_month; //เงินฝากหักรายเดือน(เงินต้น)
				$arr_data['deposit_month_interest'] = @$deposit_month_interest; //เงินฝากหักรายเดือน(ดอกเบี้ย)
				$arr_data['deposit_balance'] = $sum_deposit;

				//loan
				$loan_principle_total = 0;
				$loan_interest_total = 0;
				$this->db->join("coop_loan_name", "coop_loan_name.loan_name_id = coop_loan.loan_type", "inner");
				$loans = $this->db->get_where("coop_loan", array(
					"member_id" => $row_member['member_id'],
					"id <> " => $loan_id,
					"loan_status <> " => "3"
				))->result_array();
				foreach ($loans as $key => $value) {

					$loan_prev_deduct = $this->db->get_where("coop_loan_prev_deduct", array(
						"loan_id" => $loan_id,
						"ref_id" => $value['id']
					))->row_array();
					$is_loan_dedct = (!empty($loan_prev_deduct)) ? true : false;
					if($is_loan_dedct){
						continue;
					}
					//update money_per_period
					// $sql_update = "update coop_loan set money_per_period = (select principal_payment from coop_loan_period where loan_id = coop_loan.id limit 1) where loan_status = 1 and id = ".$value['id'];
					// $this->db->query($sql_update);
					$value['money_per_period'] = $this->db->get_where("coop_loan", array("id"=>$value['id']))->row_array()['money_per_period'];
					if($value['pay_type']==2){
						$finance_month_detail = $this->db->get_where("coop_finance_month_detail", array("loan_id"=>$value['id'], "pay_type" => "principal"))->row_array()['pay_amount'];
					}
					if($value['money_per_period'] == ''){
						$this->db->select(array(
							'principal_payment',
							'total_paid_per_month'
						));
						$this->db->from('coop_loan_period');
						$this->db->where("loan_id='".$value['id']."' AND date_count = '31'");
						$this->db->limit(1);
						$per_month_transaction = $this->db->get()->row_array();
						$value['money_per_period'] = $per_month_transaction['principal_payment'];
					}
					//
//					$this->db->order_by("transaction_datetime desc, loan_transaction_id desc");
//					$this->db->limit(1);
//					$loan_transaction = $this->db->get_where("coop_loan_transaction", array(
//						"loan_id" => $value['id'],
//						"transaction_datetime <= " => $row_loan['createdatetime'],
//						"loan_amount_balance >= " => 0
//					))->row_array();

					$this->db->select('t1.*, t2.date_start_period');
					$this->db->from('coop_loan_transaction as t1');
					$this->db->join('coop_loan as t2', 't1.loan_id = t2.id', 'inner');
					$this->db->where("t1.loan_id = '".$value['id']."'");
					$this->db->where("t1.transaction_datetime <= '".$row_loan['createdatetime']."'");
					$this->db->where("t1.loan_amount_balance >= 0");
                    $this->db->order_by(" t1.loan_transaction_id desc, t1.transaction_datetime desc");
                    $this->db->limit(1);
                    $loan_transaction = $this->db->get()->row_array();

                    if($_GET['dev'] == 'cost'){
                        echo "<pre>"; echo $this->db->last_query(); echo "</pre>";
                    }

					if(($loan_transaction['loan_amount_balance']-($value['pay_type']==2 ? $finance_month_detail : $value['money_per_period'])) <= 0){
						continue;
					}

					if($loan_transaction['loan_amount_balance'] > 0){
						if(!empty($row_loan['date_start_period'])) {
							$interest_per_year = $this->db->get_where("coop_term_of_loan", array(
								"type_id" => $value['loan_type'],
								"start_date <=" => $row_loan['date_start_period']
							))->row_array()['interest_rate'];
						} else {
							$interest_per_year = $this->db->get_where("coop_term_of_loan", array("type_id" => $value['loan_type']))->row_array()['interest_rate'];
						}
//                        if($value['period_now'] == '' || $value['period_now'] == '0') {
                        if($row_loan['date_start_period'] == $loan_transaction['date_start_period']) {
                            $count_day = $this->center_function->diff_day($value['date_period_1'], $value['approve_date']);
                            $temp_interest_31_day = (((@$value['loan_amount_balance'] * $interest_per_year) / 100) / 365) * $count_day;
                        }else{
                            $count_day = date('t', strtotime($row_loan['date_start_period']));
                            $temp_interest_31_day = ((((@$loan_transaction['loan_amount_balance']-($value['pay_type']==2 ? $finance_month_detail : $value['money_per_period'])) * $interest_per_year) / 100) / 365) * $count_day;
                        }
						$temp_interest_31_day = round($temp_interest_31_day, 2);

						$arr_list_loan[@$value['id']]['loan_principle'] = ($value['pay_type']==2) ? $value['money_per_period']-$temp_interest_31_day : $value['money_per_period']; //ยอดที่ชำระต่อเดือน เงินต้น
						if(strpos(@$value['contract_number'], 'G')) {
						    /**
                             * Todo
                             * รอการแก้ไขให้ระบบแยกเงินกู้ประเภท G
                             */
						    if(in_array($value['member_id'], array('000730', '000711'))){
                                $arr_list_loan[@$value['id']]['loan_principle'] = 0;
                                $arr_list_loan[@$value['id']]['loan_interest'] = 0;
                            }else{
                                $arr_list_loan[@$value['id']]['loan_interest'] = 0;
                            }

                        }else{
                            $arr_list_loan[@$value['id']]['loan_interest'] = @$temp_interest_31_day; //(ดอกเบี้ย)
                        }
						$arr_list_loan[@$value['id']]['loan_amount_balance'] = $loan_transaction['loan_amount_balance']-$value['money_per_period']; //(balance)
						$arr_list_loan[@$value['id']]['loan_id'] = @$value['id']; //loan_id
						$arr_list_loan[@$value['id']]['contract_number'] = @$value['contract_number']; //contract_number
						$loan_principle_total += $arr_list_loan[@$value['id']]['loan_principle'];
						$loan_interest_total += $arr_list_loan[@$value['id']]['loan_interest'];
						if(@$_GET['debug']){
							echo "loan_id ". $value['id']." : (".$loan_transaction['loan_amount_balance']."-".($value['pay_type']==2 ? $finance_month_detail : $value['money_per_period']).") * ".$interest_per_year." / 100 / 365 * ".$count_day." = ".$temp_interest_31_day."<br>";
							echo $value['money_per_period']." - ".$temp_interest_31_day." = ".($arr_list_loan[@$value['id']]['loan_principle'])."<br>";
						}
					}else{

						if($value['loan_status']!="0"){
							if($loan_transaction['loan_transaction_id']!=""){
								$this->db->order_by("transaction_datetime desc, loan_transaction_id desc");
								$this->db->limit(1);
								$tmp_loan_transaction = $this->db->get_where("coop_loan_transaction", array(
									"loan_id" => $value['id'],
									"transaction_datetime <= " => $row_loan['createdatetime'],
									"loan_amount_balance >= " => 0,
									"loan_transaction_id < " => $loan_transaction['loan_transaction_id']
								))->row_array();
								if($value['loan_status']!=4){
									$loan_transaction = $tmp_loan_transaction;
									$arr_list_loan[@$value['id']]['loan_principle'] = $value['money_per_period']; //ยอดที่ชำระต่อเดือน เงินต้น
									$temp_interest_31_day = (((@$loan_transaction['loan_amount_balance'] * @$value['interest_per_year']) / 100) / 365) * 31;
									$temp_interest_31_day = @$temp_interest_31_day;
									$arr_list_loan[@$value['id']]['loan_interest'] = @$temp_interest_31_day; //(ดอกเบี้ย)
									$arr_list_loan[@$value['id']]['loan_amount_balance'] = $loan_transaction['loan_amount_balance']; //(balance)
									$arr_list_loan[@$value['id']]['loan_id'] = @$value['id']; //loan_id
									$arr_list_loan[@$value['id']]['contract_number'] = @$value['contract_number']; //contract_number
									$loan_principle_total += $value['money_per_period'];
									$loan_interest_total += $temp_interest_31_day;
								}
							}else{
								$loan_transaction = $value['loan_amount_balance'];
								$arr_list_loan[@$value['id']]['loan_principle'] = $value['money_per_period']; //ยอดที่ชำระต่อเดือน เงินต้น
								$temp_interest_31_day = (((@$loan_transaction * @$value['interest_per_year']) / 100) / 365) * 31;
								$temp_interest_31_day = @$temp_interest_31_day;
								$arr_list_loan[@$value['id']]['loan_interest'] = @$temp_interest_31_day; //(ดอกเบี้ย)
								$arr_list_loan[@$value['id']]['loan_amount_balance'] = $loan_transaction; //(balance)
								$arr_list_loan[@$value['id']]['loan_id'] = @$value['id']; //loan_id
								$arr_list_loan[@$value['id']]['contract_number'] = @$value['contract_number']; //contract_number
								$loan_principle_total += $value['money_per_period'];
								$loan_interest_total += $temp_interest_31_day;
							}

						}
					}
				}

				$this->db->select(array('*'));
				$this->db->from('coop_loan_type');
				$loan_type = $this->db->get()->result_array();
				$list_loan = array();
				$arr_data['list_loan'] = @$list_loan;

				$loan_principle_total += $total_paid_per_month;
				$loan_interest_total += $interest_30_day;
			}else{
				if(!empty($row_finance_month)){
					//ออกรายการเรียกเก็บประจำเดือนแล้ว
					//echo '<pre>'; print_r($row_finance_month); echo '</pre>';
					$arr_list_loan = array();
					foreach($row_finance_month AS $key_month=>$value_month){
						//echo @$value_month['deduct_code'].'<br>';
						if(@$value_month['deduct_code'] == 'SHARE'){
							//หุ้นหักรายเดือน
							$share_month = @$value_month['pay_amount'];
							$share_month_interest = 0;
							$arr_data['share_month'] = @$share_month; //หุ้นหักรายเดือน(เงินต้น)
							$arr_data['share_month_interest'] = @$share_month_interest; //หุ้นหักรายเดือน(ดอกเบี้ย)
							
						}						
						
						if(@$value_month['deduct_code'] == 'DEPOSIT'){
							//เงินฝากหักรายเดือน
							$deposit_month =  @$value_month['pay_amount'];
							$deposit_month_interest = 0;	
							$arr_data['deposit_month'] = @$deposit_month; //เงินฝากหักรายเดือน(เงินต้น)
							$arr_data['deposit_month_interest'] = @$deposit_month_interest; //เงินฝากหักรายเดือน(ดอกเบี้ย)	
												
						}
						
						if(@$value_month['deduct_code'] == 'LOAN'){
							//echo '<pre>'; print_r($value_month); echo '</pre>';
							if(@$value_month['pay_type'] == 'principal'){
								$arr_list_loan[@$value_month['loan_type_id']]['loan_principle'] = @$value_month['pay_amount'];//ยอดที่ชำระต่อเดือน เงินต้น
							}
							
							if(@$value_month['pay_type'] == 'interest'){
								$arr_list_loan[@$value_month['loan_type_id']]['loan_interest'] = @$value_month['pay_amount'];//(ดอกเบี้ย)
							}
							$arr_list_loan[@$value_month['loan_type_id']]['loan_id'] = @$value_month['loan_id'];//loan_id
							
						}
						
						if(@$value_month['deduct_code'] == 'ATM'){
							if(@$value_month['pay_type'] == 'principal'){
								@$arr_list_loan[7]['loan_principle'] += @$value_month['pay_amount'];//ยอดที่ชำระต่อเดือน เงินต้น
							}
							
							if(@$value_month['pay_type'] == 'interest'){
								@$arr_list_loan[7]['loan_interest'] += @$value_month['pay_amount'];//(ดอกเบี้ย)
							}
							$arr_list_loan[7]['loan_id'] = @$value_month['loan_atm_id'];//loan_id
						}
						
						
					}
					
					$this->db->select(array('*'));
					$this->db->from('coop_loan_type');
					$loan_type = $this->db->get()->result_array();
					$list_loan = array();
					$loan_principle_total = 0;
					$loan_interest_total = 0;
					if(!empty($loan_type)){
						foreach($loan_type AS $key=>$value){						
							$list_loan[$value['id']]['loan_name']= $value['loan_type'];//ชื่อเงินกู้หลัก
							$list_loan[$value['id']]['loan_principle']= @$arr_list_loan[$value['id']]['loan_principle'];//ยอดที่ชำระต่อเดือน
							$list_loan[$value['id']]['loan_interest'] = @$arr_list_loan[$value['id']]['loan_interest'];//(ดอกเบี้ย)
							$loan_principle_total += @$arr_list_loan[$value['id']]['loan_principle'];
							$loan_interest_total += @$arr_list_loan[$value['id']]['loan_interest'];
						}
					}
					$arr_data['list_loan'] = @$list_loan;	
					
				}else{
					
					//ยังไม่ได้ออกรายการเรียกเก็บประจำเดือน
					$arr_list_loan = array();
					$this->db->select('setting_value');
					$this->db->from('coop_share_setting');
					$this->db->where("setting_id = '1'");
					$row = $this->db->get()->result_array();
					$row_share_value = $row[0];
					$share_value = $row_share_value['setting_value'];
				
					$this->db->select(array('deduct_id','deduct_code','deduct_detail','deduct_type','deduct_format','deposit_type_id','deposit_amount'));
					$this->db->from('coop_deduct');
					$this->db->order_by('deduct_seq ASC');
					$deduct_list = $this->db->get()->result_array();
					//echo '<pre>'; print_r($deduct_list); echo '</pre>';	
					foreach($deduct_list as $key2 => $value2){
						
						//หุ้นหักรายเดือน
						if($value2['deduct_code']=='SHARE'){
							//งดหุ้นชั่วคราว
							$check_refrain_share = 0;
							$this->db->select('*');
							$this->db->from('coop_refrain_share');
							$this->db->where("member_id = '".$row_member['member_id']."' AND type_refrain = '2' AND month_refrain = '".@$month_now."' AND year_refrain = '".@$year_now."'");
							$this->db->order_by('refrain_id DESC');			
							$rs_refrain_temporary = $this->db->get()->result_array();
							if(!empty($rs_refrain_temporary)){
								foreach($rs_refrain_temporary AS $key=>$value){
									$check_refrain_share = 1;
								}
							}
							
							//งดหุ้นถาวร
							$this->db->select('*');
							$this->db->from('coop_refrain_share');
							$this->db->where("member_id = '".$row_member['member_id']."' AND type_refrain = '1'");
							$this->db->order_by('refrain_id DESC');			
							$rs_refrain_permanent = $this->db->get()->result_array();
							if(!empty($rs_refrain_permanent)){
								foreach($rs_refrain_permanent AS $key=>$value){
									$check_refrain_share = 1;
								}
							}				
							
							//ทุนเรือนหุ้น
							if(@$row_member['apply_type_id'] == '1' && $check_refrain_share == 0){															
								$share = @$row_member['share_month'];
								$share_month = @$share;
								$share_month_interest = 0;
								$arr_data['share_month'] = @$share_month; //หุ้นหักรายเดือน(เงินต้น)
								$arr_data['share_month_interest'] = @$share_month_interest; //หุ้นหักรายเดือน(ดอกเบี้ย)
							}
							//echo $row_member['apply_type_id'].'<br>';
							/*if(@$row_member['apply_type_id'] == '1'){	
								$this->db->select(array('change_value'));
								$this->db->from('coop_change_share');
								$this->db->where("member_id = '".$row_member['member_id']."' AND change_share_status IN ('1','2')");
								$this->db->order_by("change_share_id DESC");
								$this->db->limit(1);
								$row_change_share = $this->db->get()->result_array();
								$row_change_share = @$row_change_share[0];
								$sum = 0;
								if(@$row_change_share['change_value'] != ''){
									$num_share = @$row_change_share['change_value'];
								}else{
									$this->db->select(array('share_salary'));
									$this->db->from('coop_share_rule');
									$this->db->where("salary_rule <= '".$row_member['salary']."'");
									$this->db->order_by("salary_rule DESC");
									$this->db->limit(1);
									$row_share_rule = $this->db->get()->result_array();
									$row_share_rule = @$row_share_rule[0];
									
									$num_share = @$row_share_rule['share_salary'];
								}
								$share = @$num_share*@$share_value;
	
								$share_month = @$share;
								$share_month_interest = 0;
								$arr_data['share_month'] = @$share_month; //หุ้นหักรายเดือน(เงินต้น)
								$arr_data['share_month_interest'] = @$share_month_interest; //หุ้นหักรายเดือน(ดอกเบี้ย)
							}
							*/
						}
						
						//เงินฝากหักรายเดือน
						if($value2['deduct_code']=='DEPOSIT'){							
							//เงินฝาก	
							$sum_deposit = 0;
							$DEPOSIT = 0;						
							$deposit_type_id = @$value2['deposit_type_id'];
							$DEPOSIT = @$value2['deposit_amount'];
							//echo $deposit_type_id.'<hr>';
							$deposit_period_count = 1;
							$deposit_balance = $DEPOSIT;
							
							$this->db->select('*');
							$this->db->from('coop_maco_account');
							$this->db->where("mem_id = '".@$row_member['member_id']."' AND type_id = '".@$deposit_type_id."'");
							$this->db->limit(1);
							$rs_account = $this->db->get()->result_array();
							$account_id = @$rs_account[0]['account_id'];
							if(!empty($account_id)){												
								if($DEPOSIT > 0){						
									$sum_deposit += @$DEPOSIT;
								}
							}
	
							$deposit_month =  @$sum_deposit;
							$deposit_month_interest = 0;	
							$arr_data['deposit_month'] = @$deposit_month; //เงินฝากหักรายเดือน(เงินต้น)
							$arr_data['deposit_month_interest'] = @$deposit_month_interest; //เงินฝากหักรายเดือน(ดอกเบี้ย)							
						}
					
						
						if(@$value2['deduct_code'] == 'LOAN'){										
							$LOAN = array();
							$where = '';
							$where .= " AND (coop_loan.guarantee_for_id = '' OR coop_loan.guarantee_for_id IS NULL) ";
			
							$this->db->select(
								array(
									'coop_loan.id',
									'coop_loan.loan_type',
									'coop_loan.contract_number',
									'coop_loan.loan_amount_balance',
									'coop_loan.interest_per_year',
									'coop_loan_transfer.date_transfer',
									'coop_loan_name.loan_name',
									'coop_loan.pay_type',
									'coop_loan.money_period_1',
									'coop_loan.createdatetime',
									'coop_loan.guarantee_for_id',
									'coop_loan_name.loan_type_id'
								)
							);
							$this->db->from('coop_loan');
							$this->db->join('coop_loan_transfer', 'coop_loan_transfer.loan_id = coop_loan.id', 'left');
							$this->db->join('coop_loan_name', 'coop_loan_name.loan_name_id = coop_loan.loan_type', 'inner');
							$this->db->where("
								coop_loan.loan_amount_balance > 0
								AND coop_loan.member_id = '".$row_member['member_id']."'
								AND coop_loan.loan_status = '1'
								AND coop_loan_transfer.transfer_status = '0'
								AND coop_loan.date_start_period <= '".($year_now-543)."-".sprintf("%02d",$month_now)."-".date('t',strtotime(($year_now-543)."-".$month_now."-01"))."' 
							".$where);
							$row_loan_month = $this->db->get()->result_array();
							//echo $this->db->last_query()."<br>";
							$j=0;
							
							foreach($row_loan_month as $key => $row_normal_loan){
								$this->db->select(array('deduct_id','ref_id'));
								$this->db->from('coop_deduct_detail');
								$this->db->where("deduct_id = '{$value2['deduct_id']}' AND ref_id = '{$row_normal_loan['loan_type']}'");
								$rs_deduct_detail = $this->db->get()->result_array();
								$ref_id = @$rs_deduct_detail[0]['ref_id'];
								if(!empty($ref_id)){
									$deduct_format = @$value2['deduct_format'];
									
									if($row_normal_loan['guarantee_for_id']!=''){
										$for_loan_id = $row_normal_loan['guarantee_for_id'];
									}else{
										$for_loan_id = $row_normal_loan['id'];
									}
									
									$this->db->select(
										array(
											'outstanding_balance',
											'principal_payment',
											'total_paid_per_month'
										)
									);
									$this->db->from('coop_loan_period');
									$this->db->where("loan_id = '".$for_loan_id."'");
									$this->db->limit(1);
									$row_loan_period = $this->db->get()->result_array();
									$row_principal_payment = $row_loan_period[0];
									
									$date_interesting = $date_month_end;
									$cal_loan_interest = array();
									$cal_loan_interest['loan_id'] = $row_normal_loan['id'];
									$cal_loan_interest['date_interesting'] = $date_interesting;
									$interest = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
									
									if($row_principal_payment['principal_payment'] > $row_normal_loan['loan_amount_balance']){
										$principal_payment = @$row_normal_loan['loan_amount_balance'];
										$balance = 0;
									}else{
										$principal_payment = @$row_principal_payment['principal_payment'];
										$balance = @$row_normal_loan['loan_amount_balance']-@$row_principal_payment['principal_payment'];
									}
									
									$LOAN[$j]['loan_id'] = $row_normal_loan['id'];
									$LOAN[$j]['loan_type'] = $row_normal_loan['loan_name'];
									//$LOAN[$j]['loan_type_id'] = $row_normal_loan['loan_type'];
									$LOAN[$j]['loan_type_id'] = $row_normal_loan['loan_type_id'];
									$LOAN[$j]['contract_number'] = $row_normal_loan['contract_number'];
									$LOAN[$j]['money_period_1'] = $row_normal_loan['money_period_1'];
									$LOAN[$j]['pay_loan_type'] = $row_normal_loan['pay_type'];
									if($deduct_format == '2'){
										$LOAN[$j]['text_title'] = 'ต้นเงินกู้เลขที่สัญญา';
										$LOAN[$j]['principal_payment'] = $principal_payment;
										$LOAN[$j]['interest'] = 0;
										$LOAN[$j]['total'] = $principal_payment;
										$LOAN[$j]['pay_type'] = 'principal';
									}else if($deduct_format == '1'){
										$LOAN[$j]['text_title'] = 'ดอกเบี้ยเงินกู้เลขที่สัญญา';
										$LOAN[$j]['principal_payment'] = 0;
										$LOAN[$j]['interest'] = $interest;
										$LOAN[$j]['total'] = $interest;
										$LOAN[$j]['pay_type'] = 'interest';
										$interest_arr[$row_normal_loan['id']] = $interest;
									}	
									$balance = @$row_normal_loan['loan_amount_balance']-$principal_payment-$interest;
									$LOAN[$j]['balance'] = $balance;
									
								}
								$j++;
							}
							//echo"<pre>";print_r($LOAN);echo"</pre>";
							if(!empty($LOAN)){
								foreach($LOAN as $key3 => $value3){
									$arr_list_loan[@$value3['loan_type_id']]['loan_principle'] = @$value3['principal_payment'];//ยอดที่ชำระต่อเดือน เงินต้น							
									$arr_list_loan[@$value3['loan_type_id']]['loan_interest'] = @$value3['interest'];//(ดอกเบี้ย)
									$arr_list_loan[@$value3['loan_type_id']]['loan_id'] = @$value3['loan_id'];//loan_id
								}
							}

							//echo '<pre>'; print_r($arr_list_loan); echo '</pre>';
						}

						if(@$value2['deduct_code'] == 'ATM'){					
							$ATM = 0;
							$this->db->select(array(
								't1.loan_amount_balance',
								't1.principal_per_month',
								't2.contract_number',
								't2.loan_atm_id',
								't1.date_last_pay',
								't1.loan_date'
							));
							$this->db->from('coop_loan_atm_detail as t1');
							$this->db->join('coop_loan_atm as t2', 't1.loan_atm_id = t2.loan_atm_id', 'inner');
							$this->db->where("
								t2.member_id = '".$row_member['member_id']."'
								AND t2.loan_atm_status = '1'
								AND t1.date_start_period <= '".$date_month_end."'
								AND t1.loan_status = '0'
							");
							$row_atm = $this->db->get()->result_array();
							$principal_per_month = 0;
							$loan_amount_balance = 0;
							if(!empty($row_atm)){
								foreach($row_atm as $key_atm => $value_atm){
									$loan_atm_id = @$value_atm['loan_atm_id'];
									$principal_per_month += @$value_atm['principal_per_month'];
									$loan_amount_balance += @$value_atm['loan_amount_balance'];
								}
								if(@$principal_per_month < @$loan_atm_setting['min_principal_pay_per_month']){
									$principal_per_month = @$loan_atm_setting['min_principal_pay_per_month'];
								}
								if(@$principal_per_month > @$loan_amount_balance){
									$principal_per_month = @$loan_amount_balance;
								}
								
								$cal_loan_interest = array();
								$cal_loan_interest['loan_atm_id'] = @$loan_atm_id;
								$cal_loan_interest['date_interesting'] = @$date_month_end;
								$interest = $this->loan_libraries->cal_atm_interest(@$cal_loan_interest);
								
								
								$deduct_format = @$value2['deduct_format'];
								if($deduct_format == '2'){
									@$arr_list_loan[7]['loan_principle'] += @$principal_per_month;//ยอดที่ชำระต่อเดือน เงินต้น
								}else{
									@$arr_list_loan[7]['loan_interest'] += @$interest;//(ดอกเบี้ย)
								}
								$arr_list_loan[7]['loan_id'] = @$loan_atm_id;//loan_id
							}
						}
					}
					
						
					$this->db->select(array('*'));
					$this->db->from('coop_loan_type');
					$loan_type = $this->db->get()->result_array();
					$list_loan = array();
					$loan_principle_total = 0;
					$loan_interest_total = 0;
					if(!empty($loan_type)){
						foreach($loan_type AS $key=>$value){						
							$list_loan[$value['id']]['loan_name']= $value['loan_type'];//ชื่อเงินกู้หลัก
							$list_loan[$value['id']]['loan_principle']= @$arr_list_loan[$value['id']]['loan_principle'];//ยอดที่ชำระต่อเดือน
							$list_loan[$value['id']]['loan_interest'] = @$arr_list_loan[$value['id']]['loan_interest'];//(ดอกเบี้ย)
							$loan_principle_total += @$arr_list_loan[$value['id']]['loan_principle'];
							$loan_interest_total += @$arr_list_loan[$value['id']]['loan_interest'];
						}
					}
					$arr_data['list_loan'] = @$list_loan;	
				}
			}
			// var_dump($arr_list_loan);exit;
			$arr_data['arr_list_loan'] = @$arr_list_loan;
			if(!empty($arr_list_loan)) {
                $keep_month = array_sum(array_column($arr_list_loan, 'loan_principle')) + array_sum(array_column($arr_list_loan, 'loan_interest'));
            }else{
			    $keep_month = 0;
            }
			///////////////////////////////////////////////
			
			//เงินฝาก
			$this->db->select(array('coop_maco_account.account_id','coop_maco_account.mem_id','coop_maco_account.account_name','coop_deposit_type_setting.type_name'));
			$this->db->from('coop_maco_account');
			$this->db->join("coop_deposit_type_setting","coop_maco_account.type_id = coop_deposit_type_setting.type_id AND deduct_loan = '1'","inner");
			$this->db->where("coop_maco_account.mem_id = '".@$member_id."'");
			$row_account= $this->db->get()->result_array();
			$account_list = array();
			if(!empty($row_account)){
				foreach($row_account AS $key=>$value){
					$this->db->select(array('transaction_balance','transaction_deposit'));
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$value['account_id']."'");
					$this->db->order_by('transaction_id DESC');
					$this->db->limit(1);
					$row_balance = $this->db->get()->result_array();
					//$account_balance  = @$row_balance[0]['transaction_balance'];
					$account_balance  = @$row_balance[0]['transaction_deposit'];
					
					$account_list[$value['account_id']]['account_id'] =  @$value['account_id'];
					$account_list[$value['account_id']]['account_name'] =  @$value['account_name'];
					$account_list[$value['account_id']]['account_balance'] =  @$account_balance;
					//print_r($this->db->last_query());
				}
			}
			$arr_data['account_list'] = @$account_list;			
			
			//ปิดสัญญาเดิม
			//
			/*
			$this->db->select(array('t1.*','t3.loan_type_code'));
			$this->db->from("coop_loan as t1");
			$this->db->join("coop_loan_name as t2",'t1.loan_type = t2.loan_name_id','inner');
			$this->db->join("coop_loan_type as t3",'t2.loan_type_id = t3.id','inner');
			$this->db->where("t1.id = '".@$loan_id."'");
			$rs_loan = $this->db->get()->result_array();
			$rs_loan = $rs_loan[0];	
			*/			
			
			$date_interesting = date('Y-m-d');
			$list_old_loan = array();
			
			$this->db->select(array('t1.*',
									't2.contract_number',
									't2.loan_amount_balance',
									't2.id',
                                    't2.period_now'
									));
			$this->db->from("coop_loan_prev_deduct t1");
			$this->db->join("coop_loan t2",'t1.ref_id = t2.id','inner');
			$this->db->where("t1.loan_id = '".@$loan_id."' AND t1.data_type = 'loan'");
			$row = $this->db->get()->result_array();
			$index = 0;
			if(@$_GET['dev'] == 'dev'){
				echo 'coop_loan_prev_deduct<br>';
				print_r($this->db->last_query()); //exit;
			}
			$extra_debt = array();
			foreach($row as $key => $value){
				$list_old_loan[$index]['contract_number'] = @$value['contract_number'];

				$extra_debt_amount	= 0;//หนี้ห้อย
				/*if(date("Y-m", strtotime($rs_loan[0]['createdatetime']) ) != date("Y-m") ){
					$month = date("m", strtotime("+1 months", strtotime($rs_loan[0]['createdatetime'])) );
					if($month=="01"){
						$year = date("Y", strtotime($rs_loan[0]['createdatetime']) ) + 543 + 1;
					}else{
						$year = date("Y", strtotime($rs_loan[0]['createdatetime']) ) + 543;
					}

					$this->db->select('profile_id');
					$this->db->from('coop_finance_month_profile');
					$this->db->where("profile_month = '".(int)$month."' AND profile_year = '".$year."' ");
					$profile_id = $this->db->get()->result_array()[0]['profile_id'];
					if(@$_GET['dev'] == 'dev'){
						echo '<hr>';
						print_r($this->db->last_query());
					}
					$this->db->select("sum(pay_amount) as sum_of_pay_amount");
					$finance_month_detail = $this->db->get_where("coop_finance_month_detail", array(
						"profile_id" => $profile_id,
						"member_id" => $rs_loan[0]['member_id'],
						"loan_id" => $value['id'],
						"pay_type" => "principal",
						"run_status" => 0
					))->result_array()[0];
					if(@$_GET['dev'] == 'dev'){
						echo '<hr>';
						print_r($this->db->last_query());
						echo '<hr>'.date("Y-m", strtotime($rs_loan[0]['createdatetime']) ).' !='. date("Y-m").'<br>';
					}
					if($finance_month_detail && $rs_loan[0]['loan_status']==0){
						$extra_debt['total_princical'] += $finance_month_detail['sum_of_pay_amount'];
						$extra_debt_amount	= $finance_month_detail['sum_of_pay_amount'];
					}

				}
				*/

				if($value['pay_type'] == 'principal'){
					if(@$_GET['dev'] == 'dev'){
						echo 'pay_amount='.@$value['pay_amount'].'<br>';
						echo 'extra_debt_amount='.@$extra_debt_amount.'<br>';
					}
					$list_old_loan[$index]['loan_id'] = @$value['id'];
					$list_old_loan[$index]['loan_amount_balance'] = @$value['pay_amount'] - $extra_debt_amount;
					$list_old_loan[$index]['loan_interest_amount'] = 0;
					$list_old_loan[$index]['period_now'] = $value['period_now'];
				}else{
					$list_old_loan[$index]['loan_id'] = @$value['id'];
					$list_old_loan[$index]['loan_amount_balance'] = @$value['pay_amount'] - $extra_debt_amount - @$value['interest_amount'];
					$list_old_loan[$index]['loan_interest_amount'] = @$value['interest_amount'];
                    $list_old_loan[$index]['period_now'] = $value['period_now'];
					/*$list_old_loan[$index]['loan_amount_balance'] = @$value['loan_amount_balance'];
					$cal_loan_interest = array();
					$cal_loan_interest['loan_id'] = @$value['ref_id'];
					$cal_loan_interest['date_interesting'] = $this->center_function->ConvertToSQLDate(@$date_interesting);
					$interest_data = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
					$list_old_loan[$index]['loan_interest_amount'] = @$interest_data;
					*/
				}
				$index++;
			}
			
			//ปิดปัญชีกู้ฉุกเฉิน ATM
			$this->db->select(array('t1.*',
									't2.contract_number',
									't2.total_amount_approve',
									't2.total_amount_balance',
									't2.loan_atm_id'
									));
			$this->db->from("coop_loan_prev_deduct t1");
			$this->db->join("coop_loan_atm t2",'t1.ref_id = t2.loan_atm_id','inner');
			$this->db->where("t1.loan_id = '".@$loan_id."' AND t1.data_type = 'atm'");
			$row = $this->db->get()->result_array();
			if(@$_GET['dev'] == 'dev'){
				print_r($this->db->last_query()); //exit;
				echo '<pre>'; print_r(@$row); echo '</pre>';
			}
			
			foreach($row as $key => $value){
				$list_old_loan[$index]['contract_number'] = @$value['contract_number'];
				if($value['pay_type'] == 'principal'){					
					$list_old_loan[$index]['loan_id'] = @$value['loan_atm_id'];
					//$list_old_loan[$index]['loan_amount_balance'] = @$value['total_amount_approve'] - @$value['total_amount_balance'];
					$list_old_loan[$index]['loan_amount_balance'] = @$value['pay_amount'] - @$value['interest_amount'];
					$list_old_loan[$index]['loan_interest_amount'] = 0;
				}else{
					$list_old_loan[$index]['loan_id'] = @$value['loan_atm_id'];
					//$list_old_loan[$index]['loan_amount_balance'] = @$value['total_amount_approve'] - @$value['total_amount_balance'];
					$list_old_loan[$index]['loan_amount_balance'] = @$value['pay_amount'] - @$value['interest_amount'];
					/*$cal_loan_interest = array();
					//$cal_loan_interest['loan_id'] = @$value['ref_id'];
					$cal_loan_interest['loan_atm_id'] = @$value['ref_id'];
					$cal_loan_interest['date_interesting'] = $this->center_function->ConvertToSQLDate(@$date_interesting);
					$interest_atm = $this->loan_libraries->cal_atm_interest_deduct($cal_loan_interest);
					$list_old_loan[$index]['loan_interest_amount'] = @$interest_atm;
					*/
					$list_old_loan[$index]['loan_interest_amount'] = @$value['interest_amount'];
				}
				$index++;
			}	
			
			if(@$_GET['dev'] == 'dev'){
					echo '<pre>'; print_r(@$list_old_loan); echo '</pre>';
			}
			
			$arr_data['list_old_loan'] = @$list_old_loan;
			$total_principal = 0;
			$total_interest = 0;
			$total_loan_balance = 0;
			if(!empty($list_old_loan)){
				foreach($list_old_loan AS $key => $value){
					$total_principal += @$value['loan_amount_balance'];
					$total_interest += @$value['loan_interest_amount'];
					$total_loan_balance += @$value['loan_amount_balance']+@$value['loan_interest_amount'];
				}
			}
			
			$arr_data['existing_loan'] = @$total_loan_balance; //หักเงินกู้เดิม
			$arr_data['principal_load'] = @$total_principal;//ภาระเงินต้น
			$arr_data['interest_burden'] = @$total_interest;//ภาระดอกเบี้ย
			$arr_data['extra_debt'] = @$extra_debt;//หนี้ห้อย
			
			$loan_amount = @$row_loan['loan_amount']; //วงเงินที่ขออนุมัติ		
			
			$month_receipt = date("m", strtotime($row_loan['createdatetime']));
			$year_receipt = date("Y", strtotime($row_loan['createdatetime']));
			
			$this->db->select(array('deduct_id','deduct_code','deduct_detail','deduct_type','deduct_format','deposit_type_id','deposit_amount'));
			$this->db->from('coop_deduct');
			$this->db->order_by('deduct_seq ASC');
			$deduct_list = $this->db->get()->result_array();
			$data_arr['deduct_list'] = @$deduct_list;
			//
			
			$deductible =  0 ;			
			//ค่าธรรมเนียม 
			$this->db->select(array('*'));
			$this->db->from('coop_loan_deduct');
			$this->db->where("loan_id = '{$loan_id}'");
			$rs_deduct = $this->db->get()->result_array();
			//echo $loan_id.'<hr>';
			//echo '<pre>'; print_r($rs_deduct); echo '</pre>';
			if(!empty($rs_deduct)){
				foreach($rs_deduct AS $key=>$row_deduct){
					if(!in_array($row_deduct['loan_deduct_list_code'], array( 'deduct_pay_prev_loan', 'deduct_cheque'))){
						$deductible += @$row_deduct['loan_deduct_amount'];
					}
					
					if($row_deduct['loan_deduct_list_code'] == 'deduct_loan_fee'){
						$arr_data['deduct_loan_fee'] = @$row_deduct['loan_deduct_amount'];
					}

					if($row_deduct['loan_deduct_list_code'] == 'deduct_before_interest'){
                        $arr_data['deduct_before_interest'] = @$row_deduct['loan_deduct_amount'];
                    }
					
					if($row_deduct['loan_deduct_list_code'] == 'deduct_person_guarantee'){
						//มีการกำหนดกรณีไม่ผ่านเกณฑ์
						$arr_data['deduct_person_guarantee'] = @$row_deduct['loan_deduct_amount'];
					}

					if($row_deduct['loan_deduct_list_code'] == 'deduct_insurance'){
						//เบี้ยประกัน
						$arr_data['deduct_insurance'] = @$row_deduct['loan_deduct_amount'];
					}

					if($row_deduct['loan_deduct_list_code'] == 'deduct_cheque'){
                        $arr_data['deduct_cheque'] = @$row_deduct['loan_deduct_amount'];
                    }
				}
			}
			//exit;			
			
			$arr_data['deductible'] =  @$deductible; //รายการหัก
			$total_amount = @$loan_amount - @$total_loan_balance - @$deductible - @$arr_data['deduct_cheque'];
			$arr_data['total_amount'] = @$total_amount;
			
			$arr_data['total_paid_per_month'] =  @$total_paid_per_month; //รวมชำระต่องวด
			
			//รายการรับเงิน	
			$arr_receiving_money = array();
			//ชำระหนี้สถาบันการเงิน
			$this->db->select(array('*'));
			$this->db->from('coop_loan_financial_institutions');
			$this->db->where("loan_id = '".@$loan_id."'");
			$this->db->order_by("order_by ASC");
			$rs_financial_institutions = $this->db->get()->result_array();
			
			$i=0;
			$financial_amount = 0;
			foreach($rs_financial_institutions AS $key=>$row_financial_institutions){
				$arr_receiving_money[$i]['transfer_type'] = 'จ่ายธนาคาร'.@$row_financial_institutions['financial_institutions_name'];
				$arr_receiving_money[$i]['total_received'] = @$row_financial_institutions['financial_institutions_amount'];
				$financial_amount += @$row_financial_institutions['financial_institutions_amount'];
				$i++;
			}
			
			//การจ่ายเงินกู้
			$transfer_type = array('0'=>'เงินสด','1'=>'โอนเงินบัญชีสหกรณ์','2'=>'โอนเงินบัญชี','3' => 'พร้อมเพย์', '4' => 'เช็คเงินสด');
			if(@$row_loan['transfer_type'] == '2'){
				$transfer_type_description = '';
				$transfer_type_description .= @$row_loan['bank_name'];
				$transfer_type_description .= '<br> เลขที่บัญชี '.@$row_loan['transfer_bank_account_id'];
			}else if(@$row_loan['transfer_type'] == '1'){
				$transfer_type_description = ' '.@$row_loan['transfer_account_id'].':'.@$row_loan['account_name'];
			}else{
				$transfer_type_description = '';
			}
			$text_transfer_type = @$transfer_type[@$row_loan['transfer_type']].@$transfer_type_description;
			
			//ยอดเงินที่จะได้รับโดยประมาณ
			$this->db->select(array('estimate_receive_money'));
			$this->db->from('coop_loan_deduct_profile');
			$this->db->where("loan_id = '".@$loan_id."'");
			$loan_deduct_profile = $this->db->get()->result_array();
			$estimate_receive_money = @$loan_deduct_profile[0]['estimate_receive_money'];			
			
			$arr_receiving_money[$i]['transfer_type'] = @$text_transfer_type;
			$arr_receiving_money[$i]['total_received'] = @$estimate_receive_money-@$financial_amount;
			
			$arr_data['receiving_money'] = $arr_receiving_money;		
					
			$arr_data['total_month'] = @$share_month+@$deposit_month+@$loan_principle_total;//รวมทั้งสิ้น รายการผ่อนชำระสหกรณ์ปัจจุบัน/เดือน
			$arr_data['total_month_interest'] = @$share_month_interest+@$deposit_month_interest+@$loan_interest_total;//รวมทั้งสิ้น รายการผ่อนชำระสหกรณ์ปัจจุบัน/เดือน(ดอกเบี้ย)
			
			//บุคคลค้ำประกัน
			$this->db->select(array(
				't1.*',
				't3.member_id',
				't3.firstname_th',
				't3.lastname_th',
				't3.mem_group_id',
				't3.salary',
				't3.birthday',
				't3.member_date',
				't3.share_month',
				't4.mem_group_name',
				't5.prename_full'
			));
			$this->db->from('coop_loan_guarantee_person as t1');
			$this->db->join('coop_mem_apply as t3','t1.guarantee_person_id = t3.member_id','inner');
			$this->db->join("coop_mem_group as t4", "t3.level = t4.id", "left");
			$this->db->join("coop_prename as t5", "t3.prename_id = t5.prename_id", "left");
			$this->db->where("t1.loan_id = '{$loan_id}' AND t3.member_status <> '3'");
			$this->db->order_by("t1.id ASC");
			$rs_guarantee = $this->db->get()->result_array();

            //หุ้นค้ำประกัน
            $res_guarantee =  $this->db->get_where('coop_loan_guarantee', array('loan_id' => $loan_id))->row_array();
            $arr_data['g_share_amt'] = $res_guarantee['amount'];

			foreach($rs_guarantee AS $key=>$row_guarantee){
				$this->db->from('coop_loan_guarantee_person as t1');
				$this->db->join('coop_loan as t2','t1.loan_id = t2.id ','inner');
				$this->db->where("
					t1.guarantee_person_id = '".@$row_guarantee['guarantee_person_id']."' 
					AND t2.loan_status IN('1','2')
				");
				$rs_count_guarantee = $this->db->get()->result_array();
				$n=0;
				foreach($rs_count_guarantee as $key_count => $row_count_guarantee){
					$n++;
				}
				@$rs_guarantee[$key]['count_guarantee'] = @$n;

				$share = $this->db->select("share_period, share_collect_value")->from("coop_mem_share")->where("share_date <= '".$row_loan["createdatetime"]."' AND member_id = '".$row_guarantee['guarantee_person_id']."' AND share_status IN (1,5,6)")->order_by("share_date DESC")->get()->row();
				$rs_guarantee[$key]['share_balance'] = $share->share_collect_value;
				$rs_guarantee[$key]['share_period'] = $share->share_period;

				//ข้อมูลค้ำประกันเงินกู้
				$guarantors_raw = $this->db->select("t3.member_id, t3.salary, t2.contract_number, t3.firstname_th, t3.lastname_th, t4.prename_full, t1.loan_id, t2.loan_amount, t1.guarantee_person_amount, (SELECT count(*) FROM coop_loan_guarantee_person WHERE loan_id=t2.id) as person")
							->from("coop_loan_guarantee_person as t1")
							->join("coop_loan as t2", "t1.loan_id = t2.id", "INNER")
							->join("coop_mem_apply as t3", "t2.member_id = t3.member_id", "LEFT")
							->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "LEFT")
							->where("t1.guarantee_person_id = '".$row_guarantee['guarantee_person_id']."' AND t1.loan_id != '".$loan_id."'")
							->get()->result_array();
				$guarantors = array();

				if(isset($_GET['dev']) && $_GET['dev'] == 'guarantee'){
                    echo "<pre>";
				    echo $this->db->last_query();
				    echo "</pre>";
                }
				$check_dupp = array();
				foreach($guarantors_raw as $data) {
					$transaction = $this->db->select("loan_amount_balance, transaction_datetime")->from("coop_loan_transaction")->where("loan_id = '".$data["loan_id"]."' AND transaction_datetime <= '".$row_loan["createdatetime"]."'")->order_by("transaction_datetime DESC, loan_transaction_id DESC")->get()->row();
					if($transaction->loan_amount_balance > 0) {
                            //$data['guarantee_person_amount'] = $this->get_guarantee_person_amount_used($data['member_id']);

                            $_tempFin = self::getFinanceMonth($data["loan_id"], $createdate_loan);
                            $data['guarantee_person_amount'] = $data['guarantee_person_amount'];
                            $data['loan_amount_balance'] = $transaction->loan_amount_balance-$_tempFin[0]['real_pay_amount'];
                            $check_dupp[] = $data['member_id'];
                            $guarantors[$data['member_id']] = $data;
                    }
				}


				$rs_guarantee[$key]["guarantors"] = $guarantors;

//                //หุ้นค้ำประกัน
//                $res_guarantee =  $this->db->get_where('coop_loan_guarantee', array('loan_id' => $loan_id))->row_array();
//                $arr_data['g_share_amt'] = $res_guarantee['amount'];


                $loans = array();
				$loan_raw = $this->db->select("t1.id as loan_id, t1.approve_date, t1.contract_number, t1.date_last_interest, t1.loan_amount, t1.money_per_period, t1.period_amount, t1.period_now")
							->from("coop_loan as t1")
							->where("t1.member_id = '".$row_guarantee['guarantee_person_id']."' AND t1.loan_status IN (1,4,6,7,8) AND t1.id != '".$_GET["loan_id"]."'")
							->get()->result_array();
				foreach($loan_raw as $loan) {
					$transaction = $this->db->select("*")->from("coop_loan_transaction")->where("loan_id = '".$loan["loan_id"]."' AND transaction_datetime <= '".$row_loan["createdatetime"]."'")->order_by("transaction_datetime DESC")->get()->row();
					if($transaction->loan_amount_balance > 0) {
						$loan["balance"] = $transaction->loan_amount_balance;
						$loans[] = $loan;
					}
				}
				$rs_guarantee[$key]["loans"] = $loans;

			}

			$arr_data['row_guarantee'] = @$rs_guarantee;	
			
			//รายการซื้อ	
			$this->db->select(array('*'));
			$this->db->from('coop_loan_deduct_list');
			$this->db->where("loan_deduct_list_code != 'deduct_pay_prev_loan' AND loan_deduct_status = 1");
			$this->db->order_by('run_id ASC');
			$row = $this->db->get()->result_array();
			$arr_data['loan_deduct_list'] = $row;	
			
			$this->db->select(array(
				'loan_deduct_list_code',
				'loan_deduct_amount'
			));
			$this->db->from('coop_loan_deduct');
			$this->db->where("loan_id = '".$loan_id."'");
			$row = $this->db->get()->result_array();
			$loan_deduct = array();
			foreach($row as $key => $value){
				$loan_deduct[$value['loan_deduct_list_code']] = $value['loan_deduct_amount'];
			}
			$arr_data['loan_deduct'] = $loan_deduct;
			
			//ค่าใช้จ่าย
			//			$this->db->from('coop_loan_cost');
			//			$this->db->where("loan_id = '".$loan_id."' AND member_id = '".$member_id."'");
			//			$this->db->limit(1);
			//			$rs_cost = $this->db->get()->result_array();
			//			$row_cost = @$rs_cost[0];
			//			$arr_data['school_benefits'] = $row_cost['school_benefits'];
			//			$arr_data['saving'] = @$row_cost['saving'];
			//			$arr_data['ch_p_k'] = @$row_cost['ch_p_k'];
			//			$arr_data['pension'] = @$row_cost['pension'];
			//			$arr_data['k_b_k'] = @$row_cost['k_b_k'];
			//			$arr_data['other'] = @$row_cost['other'];

            //รายได้
            $sql = "SELECT
				*, 
				IFNULL((select coop_income_loan_detail.income_value from coop_income_loan_detail where coop_income_loan_detail.loan_id = '".$loan_id."' and coop_income_loan_detail.income_id = coop_income.id), 0) as income_value
			FROM
				`coop_income`
			ORDER BY
				`seq` ASC";

            if($_GET['sql'] == 'show'){
                echo $sql; exit;
            }
            $arr_data['income'] = $this->db->query($sql)->result_array();

            //ค่าใช้จ่าย
            $arr_data['loan_cost_code'] = array();
            $this->db->select('outgoing_code, outgoing_name, loan_cost_amount');
            $this->db->from("coop_outgoing");
            $this->db->join("coop_loan_cost_mod", "coop_outgoing.outgoing_code=coop_loan_cost_mod.loan_cost_code", "inner");
            $this->db->where("loan_id = '".$loan_id."' AND member_id = '".$member_id."'");
            $rs_cost = $this->db->get()->result_array();
            $arr_data['loan_cost_code'] = $rs_cost;
			//            foreach ($rs_cost as $key => $val){
			//                $arr_data['loan_cost_code'] = $val['loan_cost_amount'];
			//            }

			//อสังหาทรัพย์ค้ำประกัน
			$this->db->select(array('t1.*',
									't2.province_name',
									't3.amphur_name',
									't4.district_name'
								));
			$this->db->from("coop_loan_guarantee_real_estate t1");
			$this->db->join("coop_province t2","t1.province_id = t2.province_id","left");
			$this->db->join("coop_amphur t3","t1.amphur_id = t3.amphur_id","left");
			$this->db->join("coop_district t4","t1.district_id = t4.district_id","left");
			$this->db->where("t1.loan_id = '".$loan_id."'");
			$rs_real_estate = $this->db->get()->result_array();
			$arr_data['row_real_estate'] = @$rs_real_estate[0];
			
			//$member_id = @$_GET['member_id'];
			//$loan_id = @$_GET['loan_id'];
			//@start ข้อมูลประกันชีวิต
			$rs_cremation_type = $this->db->select('import_cremation_type,import_amount_balance')
			->from('coop_life_insurance_cremation')
			->where("member_id = '".$member_id."' AND loan_id = '".$loan_id."'")
			->get()->result_array();
			$cremation_type_1 = 0;
			$cremation_type_2 = 0;
			foreach($rs_cremation_type AS $key=>$row_cremation_type){
				//ชสอ
				if($row_cremation_type['import_cremation_type'] == '1'){
					$cremation_type_1 = @$row_cremation_type['import_amount_balance'];
				}
				
				//สสอค
				if($row_cremation_type['import_cremation_type'] == '2'){
					$cremation_type_2 = @$row_cremation_type['import_amount_balance'];
				}
			}
			$arr_data['cremation_type_1'] = @$cremation_type_1;
			$arr_data['cremation_type_2'] = @$cremation_type_2;			
				
			$row_life_insurance = $this->db->select('insurance_year,insurance_date,insurance_amount,insurance_premium,insurance_new,insurance_old')
			->from('coop_life_insurance')
			->where("member_id = '".$member_id."' AND loan_id = '".$loan_id."'")
			->limit(1)
			->get()->result_array();
			
			$arr_data['life_insurance_4'] = @$row_life_insurance[0]['insurance_old'];
			$arr_data['life_insurance_5'] = @$row_life_insurance[0]['insurance_new'];	
			$arr_data['life_insurance_6'] = @$row_life_insurance[0]['insurance_amount'];	
			//@end ข้อมูลประกันชีวิต

			//ข้อมูลค้ำประกันเงินกู้
			$guarantors_raw = $this->db->select("t3.member_id, t3.salary, t2.contract_number, t3.firstname_th, t3.lastname_th, t4.prename_full, t1.loan_id, t1.guarantee_person_amount, t2.loan_amount, (SELECT count(*) FROM coop_loan_guarantee_person WHERE loan_id=t2.id) as person")
									->from("coop_loan_guarantee_person as t1")
									->join("coop_loan as t2", "t1.loan_id = t2.id", "INNER")
									->join("coop_mem_apply as t3", "t2.member_id = t3.member_id", "LEFT")
									->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "LEFT")
									->where("t1.guarantee_person_id = '".$member_id."' AND t2.loan_status = 1")
									->get()->result_array();
			$guarantors = array();

			foreach($guarantors_raw as $data) {
				$transaction = $this->db->select("*")->from("coop_loan_transaction")->where("loan_id = '".$data["loan_id"]."' AND transaction_datetime <= '".$row_loan["createdatetime"]."'")->order_by("transaction_datetime DESC")->get()->row();
				if($transaction->loan_amount_balance > 0) {
                    //$data['guarantee_person_amount_used'] = $this->get_guarantee_person_amount_used($data['member_id']);

                    $_tempFin = self::getFinanceMonth($data['loan_id'], $createdate_loan);
					if($data['loan_amount'] == $data['guarantee_person_amount']){
                        $data['loan_amount_balance'] = ($transaction->loan_amount_balance - $_tempFin[0]['real_pay_amount']);
                    }else{
                        $loan_amount_balance = ($transaction->loan_amount_balance - $_tempFin[0]['real_pay_amount']);
                        $percen_cal = $data['guarantee_person_amount']/$data['loan_amount']*100;
                        $percen_cal = ceil($percen_cal * 100) / 100; //ปัดทศนิยมตำแหน่งที่ 2 ขึ้น
                        $data['loan_amount_balance'] = $loan_amount_balance / 100 * $percen_cal;
                    }
                    $data['guarantee_person_amount'] = $data['guarantee_person_amount'];
                    $guarantors[$data['member_id']] = $data;
				}
			}

			if(isset($_GET['dev']) && $_GET['dev'] == "loan"){
			    echo "<pre>"; print_r($guarantors); echo "</pre>"; exit;
            }
			$arr_data["guarantors"] = $guarantors;

			$loans = array();
			$loan_raw = $this->db->select("t1.id as loan_id, t1.loan_type, t1.approve_date, t1.contract_number, 't1.guarantee_person_amount',t1.date_last_interest, t1.loan_amount, t1.money_per_period, t1.period_amount, t1.period_now")
									->from("coop_loan as t1")
									->where("t1.member_id = '".$member_id."' AND t1.loan_status IN (1,4,6,7,8) AND t1.id != '".$_GET["loan_id"]."'")
									->get()->result_array();
			foreach($loan_raw as $loan) {
				// 2019-12-31 00:00:00
				$transaction = $this->db->select("*")->from("coop_loan_transaction")->where("loan_id = '".$loan["loan_id"]."' AND transaction_datetime <= '".$row_loan["createdatetime"]."'")->order_by("transaction_datetime DESC")->get()->row();
				if($transaction->loan_amount_balance > 0) {
					$loan["balance"] = $transaction->loan_amount_balance;
					$loans[] = $loan;
				}
			}
			$arr_data["loans"] = $loans;

			$board_raw = $this->db->select("*")->from("coop_loan_board")->get()->result_array();
			$boards = array();
			foreach($board_raw as $board) {
				if($board["level"] != "board") {
					$boards[$board["level"]] = $board;
				} else {
					$boards["boards"][] = $board;
				}
			}
			$arr_data["boards"] = $boards;
		}

        $cost_all = array_sum(array_column($rs_cost, 'loan_cost_amount'));

		$arr_data['income_all'] = $income = $row_member['salary']+array_sum(array_column($arr_data['income'], 'income_value'));
		$arr_data['keep_loan'] = ($arr_data['total_paid_per_month'] +$arr_data['interest_30_day']);

        if($_GET['dev'] == 'cost'){
//            exit;
            echo "<pre>";
            print_r($arr_data['arr_list_loan']);
            print_r($arr_data['income']);  echo "\n";
            print_r($arr_data['share_month']);  echo "\n";
			print_r($rs_cost); echo "\n";

            echo 'cost_all => '.$cost_all.'<br>';
            echo 'share_month => '.$arr_data['share_month'].'<br>';
            echo 'keep_month => '.$keep_month.'<br>';
            echo '<br>';
            echo 'income => '.($cost_all + $arr_data['share_month'] + $keep_month)."\n";

            $income_data = 12980.14;

            echo 'ส่วนต่าง => '.$income_data.' - '.($cost_all + $arr_data['share_month'] + $keep_month).' = '.($income_data - ($cost_all + $arr_data['share_month'] + $keep_month));
//            echo $arr_data['keep_loan']."\n";
            exit;
        }

		if($keep_month > 0) {
            $arr_data['cost_all'] = ($cost_all + $arr_data['share_month'] + $arr_data['share_month_interest'] + $arr_data['deposit_month'] + $arr_data['deposit_month_interest'] + $keep_month);
        }else{
            $arr_data['cost_all'] = $cost_all + $arr_data['share_month'] + $arr_data['share_month_interest'] + $arr_data['deposit_month'] + $arr_data['deposit_month_interest'];
        }


		$member_start = date_create($row_member['member_date']);
		$pasent = date_create($createdate_loan);
		$year_member =  date_diff($member_start, $pasent)->format('%Y');
		$month_member =  date_diff($member_start, $pasent)->format('%m');
        $arr_data['member_month'] = ($year_member)*12+$month_member;
        $arr_data['check_salary'] = $row_loan['loan_amount'] <= ($row_member['salary'] * 3) && $row_loan['loan_amount'] <= 150000 ? 1 : 0;
        $arr_data['check_period_first'] = ($arr_data['list_old_loan'][0]['period_now']) >= 1 ? 1 : 0;

        if($arr_data['list_old_loan'][0]['period_now'] >= 1 && $arr_data['list_old_loan'][0]['period_now'] < 3){
            if($arr_data['deduct_loan_fee'] == 100){
                $arr_data['check_fee'] = 1;
            }else{
                $arr_data['check_fee'] = 0;
            }
        }else {
                $arr_data['check_fee'] = 0;
        }

        $incomeX2 = ($arr_data['income_all'])*2;
        $arr_data['incomeX3'] = $incomeX3 = $row_member['salary']*3;
        $period_limit = 0;
        if($row_loan['loan_amount'] <= $incomeX2){
            $arr_data['period_amount'] = $period_limit = 10;
        }else{
            $arr_data['period_amount'] = $period_limit = 12;
        }

        if($row_loan['loan_amount'] >= $incomeX2){
            if($row_loan['period_amount'] <= $period_limit){
                $arr_data['check_period'] = 1;
            }else{
                $arr_data['check_period'] = 0;
            }
        }

        $retire = $this->db->get("coop_profile", 1)->row_array();
        $arr_data['net'] =  $arr_data['income_all']-($arr_data['keep_loan']+$arr_data['cost_all']);
        if(!empty($arr_data['net']) && !empty($arr_data['income_all'])){
            $arr_data['net_percent'] = ($arr_data['net']/$arr_data['income_all']) * 100;
        }else{
            $arr_data['net_percent'] = 0;
        }
        $arr_data['check_net'] = ($arr_data['net'] > 1000 && $arr_data['net_percent'] > 8 ) ? 1 : 0;

        $arr_data['retire_date'] = $retire_date = date( 'Y-09-30', strtotime($row_member['birthday'].( date('m', strtotime($row_member['birthday'])) <= $retire['retire_month'] ? " +".$retire['retire_age']." YEAR" : " +".($retire['retire_age']+1)." YEAR")));

        $diff = date_diff(date_create($row_loan['createdatetime']), date_create($retire_date));
        $diff_year = $diff->format('%y');
        $diff_month = $diff->format('%m');
        $arr_data['work_month_amount'] = ($diff_year*12)+$diff_month;
        $arr_data['member_retire_month'] = $arr_data['work_month_amount'] >=  $row_loan['period_amount'] && $row_member['mem_type_id']  == 1 ? 1 : 0;

		$term_of_loan = $this->db->select('person_guarantee, max_contract_total, max_period')->from('coop_term_of_loan')->where('type_id', $row_loan['loan_type'])->order_by('start_date', 'desc')->limit(1)->get()->row_array();
		$arr_data['person_guarantee'] = $term_of_loan['person_guarantee'];
		$arr_data['max_contract_total'] = $term_of_loan['max_contract_total'];
		$arr_data['max_period'] = $term_of_loan['max_period'];
        return $arr_data;
    }

    public function get_report_loan_order($createdatetime){
        $member_id = @$_GET['member_id'];
        $loan_id = @$_GET['loan_id'];
        $this->db->select(array('t1.id', 't1.loan_amount', 't1.loan_amount_balance', 't1.loan_type', 't1.petition_number', 't1.contract_number', 't1.createdatetime', 't2.loan_type_id', 't2.loan_name'));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_loan_name as t2', 't2.loan_name_id = t1.loan_type', 'left');
        $this->db->join("(SELECT * FROM coop_loan_prev_deduct WHERE loan_id = '".$loan_id."') as t3", 't1.id = t3.ref_id', 'left');
        $this->db->where("t1.member_id = '".$member_id."' AND loan_amount_balance > '0' AND id != '".$loan_id."' AND t1.loan_status in (1,2,6) AND t3.ref_id is NULL");
        $this->db->order_by("t1.loan_type");
        $row = $this->db->get()->result_array();
        $loan_order = array();

        $loan = $this->db->get_where('coop_loan', "id ='".$loan_id."'")->row_array();

        $time = strtotime($loan['createdatetime']);
        $profile_month = date("m", $time);
        $profile_year = date("Y", $time);

        $finance_month= $this->db->select('*')->from("coop_finance_month_detail as t1")
            ->join("coop_finance_month_profile as t2", "t1.profile_id=t2.profile_id", 'inner')
            ->where("loan_id is not null")
            ->where(array('t2.profile_year' => (int)$profile_year+543,
                "t2.profile_month" => $profile_month,
                "t1.member_id" => $member_id,
                "t1.pay_type" => "principal",
                "t1.run_status" => "0"))
            ->get()
            ->result_array();

        $_finance = array_column($finance_month, 'real_pay_amount', 'loan_id');

        if(isset($_GET['dev']) && $_GET['dev'] == 'loan'){
            echo "<pre>"; print_r($_finance);
        }

        foreach ($row as $key => $value){
            $loan_transaction = $this->db->select('loan_amount_balance')
                ->from('coop_loan_transaction as t1')
                ->where("t1.transaction_datetime < '".$createdatetime."' AND t1.loan_id = '".$value['id']."'")
                ->order_by("t1.transaction_datetime DESC, t1.loan_transaction_id DESC")
                ->get()->row_array();
//            $value['loan_amount_balance'] = round($value['loan_amount_balance'] - (double)$_finance[$value['id']], 2);
            $value['loan_amount_balance'] = round($loan_transaction['loan_amount_balance'] - (double)$_finance[$value['id']], 2);
            $loan_order[$value['loan_type_id']][$value['loan_type']][] = $value;
        }


        if(isset($_GET['dev']) && $_GET['dev'] == 'loan'){
            echo "<pre>"; print_r($loan_order); exit;
        }

        $row = $this->db->select('loan_name_id ,loan_name')
            ->from('coop_loan_name')->get()->result_array();
        $loan_name = array();
        foreach ($row as $key => $value){
            $loan_name[$value['loan_name_id']]['loan_name'] = $value['loan_name'];
        }
        if(empty($loan_order['2']['200'])){ // กู้สามัญ (บุคคลค้ำ)
            $loan_order['2']['200']['0']['loan_name'] =  $loan_name['200']['loan_name'];
            $loan_order['2']['200']['0']['loan_amount'] = '0';
            $loan_order['2']['200']['0']['contract_number'] = '';
        }
        if(empty($loan_order['2']['201'])){ // กู้สามัญ (บุคคลค้ำ)
            $loan_order['2']['201']['0']['loan_name'] =  $loan_name['201']['loan_name'];
            $loan_order['2']['201']['0']['loan_amount'] = '0';
            $loan_order['2']['201']['0']['contract_number'] = '';
        }
        if(empty($loan_order['3'])){ // กู้พิเศษ
            $loan_order['3']['300']['0']['loan_name'] =  'กู้พิเศษ';
            $loan_order['3']['300']['0']['loan_amount'] = '0';
            $loan_order['3']['300']['0']['contract_number'] = '';
        }
        if(empty($loan_order['1'])){ // ฉฉ
            $loan_order['1']['100']['0']['loan_name'] = $loan_name['100']['loan_name'];
            $loan_order['1']['100']['0']['loan_amount'] = '0';
            $loan_order['1']['100']['0']['contract_number'] = '';
        }

        // เรียงลำดับ
        $new_loan_order = array();
        $new_loan_order['2'] = $loan_order['2'];
        $new_loan_order['3'] = $loan_order['3'];
        $new_loan_order['1'] = $loan_order['1'];
        $loan_order = $new_loan_order;

        if($_GET['dev'] == "loan") {
            echo "<pre>";
            print_r($loan_order);
            exit;
        }

        return $loan_order;
    }

    public function get_guarantee_person_amount_used($search_text){
        $this->db->select(array(
            "loan_id",
            "coop_loan.member_id"
        ));
        $this->db->join("coop_loan", "coop_loan.id = coop_loan_guarantee_person.loan_id and coop_loan.loan_status in (1,2,6)");
        $row2 = $this->db->get_where("coop_loan_guarantee_person", array(
            "guarantee_person_id" => $search_text,
        ))->result_array();

        $total_guarantee = 0;
        foreach ($row2 as $key => $value) {
            $guarantee_person_id = $value['member_id'];
            $share = $this->db->query("select share_collect_value from coop_mem_share where coop_mem_share.member_id = '$guarantee_person_id' and share_status not in (0,3) order by share_date desc limit 1 ")->result_array()[0]['share_collect_value'];
            $share = ($share == "") ? 0 : $share;
            $deposit_balance = @$this->db->query("select transaction_balance from coop_account_transaction where account_id = (select coop_maco_account.account_id from coop_maco_account where account_status = '0' AND mem_id = '$guarantee_person_id' and coop_maco_account.type_id = (select coop_deposit_type_setting.type_id from coop_deposit_type_setting where deduct_loan = 1 limit 1) )")->result_array()[0]['transaction_balance'];
            $deposit_balance = ($deposit_balance == "") ? 0 : $deposit_balance;
            $this->db->select(array(
                "FORMAT(ABS((loan_amount_balance - $share + $deposit_balance)) / (select count(*) from coop_loan_guarantee_person where loan_id = coop_loan.id group by loan_id), 0) as guarantee_person_amount"
            ));
            $this->db->join("coop_mem_apply", "coop_loan.member_id = coop_mem_apply.member_id");
            $guarantee = $this->db->get_where("coop_loan", array(
                "coop_loan.id" => $value['loan_id'],
            ))->result_array();
            $row2[$key]['guarantee'] = $guarantee;
            foreach ($guarantee as $key_1 => $value_1) {
                $total_guarantee += implode("", explode(",", $value_1['guarantee_person_amount']));
            }
        }
        return $total_guarantee;
    }

    /**
     * bbcoop only
     *
     **/
    public function get_data_loan_all_report_personal($loan_id){

        $data = array();
        /***
         * Loan Data
         */
        $loan_data = $this->db->get_where('coop_loan', array('id' => $loan_id), 1)->row_array();

        $data['row_member'] = $this->db->select("t1.member_id, concat(`t2`.`prename_full`, `t1`.`firstname_th`, ' ',`t1`.`lastname_th`) as fullname_th, `share_month`")->from('coop_mem_apply as t1')
            ->join('coop_prename as t2', 't1.prename_id=t2.prename_id', 'left')
            ->where(array('member_id' => $loan_data['member_id']))
            ->get()->row_array();

        $loan =  $this->db->select('*')->from('coop_loan as t1')->where(array('member_id' => $loan_data['member_id'], 'id <> '  => $loan_id, 'loan_status' => '1' ))->get()->result_array();

        $data_receipt = self::findReceiptData($loan_data['member_id'], $loan_data['createdatetime']);
        $data_laon = self::findLoanData($loan_data['member_id'], $loan_data['createdatetime']);

        $data['container'] = self::prepare($data_receipt, $data_laon);


        $data['loan_name'] = $this->db->select(array('loan_name_id', 'loan_name'))->from('coop_loan_name')->get()->result_array();
        $data['loan_type'] = $this->db->get('coop_loan_type')->result_array();

        $loan_name = array();
        foreach ($data['loan_name'] as $key => $value){
            $loan_name[$value['loan_name_id']] = $value;
        }
        $data['loan_name'] = $loan_name;

        return $data;
    }

    public function getFinanceMonth($loan_id, $createdatetime){

        $time = strtotime($createdatetime);
        $profile_month = date("m", $time);
        $profile_year = date("Y", $time);

        return $this->db->select('deduct_code, loan_id, real_pay_amount, pay_type')->from("coop_finance_month_detail as t1")
            ->join("coop_finance_month_profile as t2", "t1.profile_id=t2.profile_id", 'inner')
            ->where("loan_id is not null")
            ->where(array('t2.profile_year' => (int)$profile_year+543,
                "t2.profile_month" => $profile_month,
                "t1.loan_id" => $loan_id,
                "t1.deduct_code" => "LOAN",
                "t1.pay_type" => "principal",
                "t1.run_status" => "1"))
            ->get()
            ->result_array();
    }

    public function findReceiptData($member_id, $date){
        $start = date('Y-m-01', strtotime($date." -12 MONTH"));
        return $this->db->select(
            "t1.payment_date, t2.sumcount, t3.loan_type, t1.member_id, t3.contract_number, t1.loan_id, period_count, sum(principal_payment) principal_payment, sum(interest) interest, sum(total_amount) as total_amount")
            ->from("coop_finance_transaction t1")
            ->join("coop_receipt t2", "t1.receipt_id=t2.receipt_id", "inner")
            ->join("coop_loan t3", "t1.loan_id=t3.id", "left")
            ->where("t1.member_id ='{$member_id}' AND t1.payment_date BETWEEN '{$start}' AND '{$date}' AND t1.total_amount > 0")
            ->group_by('t1.receipt_id, t1.loan_id')
            ->order_by('t1.payment_date', 'ASC')
            ->get()->result_array();
    }

    public function findLoanData($member_id, $date){
        $start = date('Y-m-01', strtotime($date." -12 MONTH"));
        return $this->db->select("createdatetime, id as loan_id, loan_type, loan_amount, loan_amount as loan_amount_balance, contract_number")->from("coop_loan")
            ->where("member_id='{$member_id}' AND createdatetime BETWEEN '{$start}' AND '{$date}'")
            ->get()->result_array();
    }

    private function prepare($receipt, $loan){
        $res = array();
        $loan_type = array_column(self::getLoanType(),'loan_type_id','loan_name_id');
        foreach ($receipt as $key => $value){
            $laon_type_key = $loan_type[$value['loan_type']] == "" ? 0 : $loan_type[$value['loan_type']];
            $share = self::getShare($value['member_id'], $value['payment_date']." 23:59:59");
            $value['share_collect'] = $share['share_collect'];
            $value['share_collect_value'] = $share['share_collect_value'];
            $res[$value['payment_date']][0][$laon_type_key] = $value;
            $res[$value['payment_date']][0]['sumcount'] = $value['sumcount'];
        }

        foreach ($loan as $key => $value){
            $laon_type_key = $loan_type[$value['loan_type']] == "" ? 0 : $loan_type[$value['loan_type']];
            $res[date('Y-m-d', strtotime($value['createdatetime']))][1][$laon_type_key] = $value;
        }
        ksort($res);
        return $res;
    }

    private function getLoanType(){
        return $this->db->get('coop_loan_name')->result_array();
    }


    private function getShare($member_id, $date){
        $str_share = "SELECT tt1.* FROM coop_mem_share tt1 
                    INNER JOIN (
                    SELECT t1.member_id, t1.share_date, max(t1.share_id) as share_id FROM coop_mem_share t1  INNER JOIN (
                        SELECT max(share_date) as `share_date`, share_id, member_id FROM coop_mem_share WHERE member_id = ? AND share_date <= ? GROUP BY member_id
                        ) t2 ON t1.share_date=t2.share_date AND t1.member_id=t2.member_id
                    ) tt2
                    ON tt1.member_id=tt2.member_id and tt1.share_date=tt2.share_date AND tt1.share_id=tt2.share_id";

        return $this->db->query($str_share, array($member_id, $date))->row_array();
    }
	public function get_coop_loan_type($s='*')
	{
		return $this->db->select($s)->from('coop_loan_type')->order_by('order_by ASC')->get()->result_array();
	}

	public function get_coop_report_loan_request($where_date ="1=1")
	{
		$q ="select g1.*,g2.pay_P,pay_int,(g1.loan_amount - pay_P - pay_int) as balance from (SELECT t1.id as loan_id, @mem := t1.member_id as member_id,concat(t3.prename_short,t2.firstname_th,' ',t2.lastname_th) as member_name,t1.petition_number,t1.loan_amount,@date := date(createdatetime) as createdatetime  FROM `coop_loan` as t1  
		INNER JOIN coop_mem_apply t2 on t1.member_id = t2.member_id
		INNER JOIN coop_prename t3 on t2.prename_id = t3.prename_id
		where $where_date) g1
		 left JOIN 
		(select t2.member_id,principal  as pay_P, interest as pay_int from coop_loan_transaction t1 INNER JOIN  coop_loan t2 on t1.loan_id = t2.id
		 where loan_id in(select t1.id from coop_loan t1 where t1.member_id = @mem) and transaction_datetime >=CONCAT(@date,' 00:00:00') and  transaction_datetime <= CONCAT(@date,' 23:59:59')GROUP BY loan_id) g2 on g1.member_id = g2.member_id ;
		";
		return $this->db->query($q)->result_array();
	}
	public function coop_report_loan_request_temp_base_data($where ='where 1=1')
	{
		$this->db->query("DROP TEMPORARY TABLE IF EXISTS temp_base_data;");
		$q = "CREATE TEMPORARY TABLE temp_base_data
		SELECT t1.member_id,CONCAT(IFNULL(t5.prename_short,''),t4.firstname_th,' ',t4.lastname_th) as member_name,(select dividend_acc_num from coop_mem_bank_account where member_id = t1.member_id limit 1) as account_number,t1.id as loan_id,t1.petition_number,t1.loan_amount,t1.createdatetime,t1.loan_type as loan_type,t3.id as group_loan_type , sum(t6.pay_amount) as debet,sum(t6.interest_amount) as debet_int,GROUP_CONCAT(t7.petition_number, ',') as debet_petition_number
		FROM `coop_loan` `t1` LEFT JOIN `coop_loan_name` `t2` ON `t1`.`loan_type`=`t2`.`loan_name_id` LEFT JOIN `coop_loan_type` `t3` ON `t3`.`id` = `t2`.`loan_type_id` INNER JOIN coop_mem_apply t4 on t1.member_id = t4.member_id LEFT JOIN coop_prename t5 on t4.prename_id = t5.prename_id 
		Left join coop_loan_prev_deduct t6 on t1.id = t6.loan_id
		Left join coop_loan t7 on t6.ref_id = t7.id
		$where  
		GROUP BY t1.id
		";
		$this->db->query($q);
	}
	public function get_coop_report_loan_request_data($s='*')
	{
		return $this->db->select($s)->from('temp_base_data')->get()->result_array();
	}
	public function get_coop_report_loan_request_deduct_data($s='*')
	{
		return $this->db->select($s)->from('coop_loan_deduct')->where_in('loan_id','select loan_id from temp_base_data',false)->get()->result_array();
	}
	public function check_coop_report_loan_request($where='')
	{
		if($where != ''){
			$this->db->where($where);
		}
		return $this->db->select('count(*) as Count ')->from('coop_loan t1')->join('coop_loan_name t2','t1.loan_type=t2.loan_name_id','left')->join('coop_loan_type t3','t3.id = t2.loan_type_id','left')->get()->result_array();
	}
	
}