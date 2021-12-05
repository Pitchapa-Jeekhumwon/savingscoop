<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Loan_request_form_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        
    }

    public function loan_request_form_pdf_model($member_id,$loan_id)
    {
            $monthtext = $this->center_function->month_arr();
            $arrMM=array(1=>"มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
            $arrMM_short=array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
            $arr_data = array();
            $date = date("Y-m-d 00:00:00");

            //ข้อมูลสหกรณ์ -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            $this->db->select('*');
            $this->db->from('coop_profile');
            $profile_location = $this->db->get()->row_array();
            $arr_data['profile_location'] = $profile_location;

            //ประวัติการผิดนัดชำระ -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            $this->db->select(array(
                'coop_non_pay.non_pay_month', 'coop_non_pay.non_pay_year', 'coop_non_pay.non_pay_status', 'coop_non_pay.member_id', 'coop_finance_month_profile.profile_id', 'SUM(coop_finance_month_detail.pay_amount) AS pay_amount', 'coop_finance_month_detail.loan_id', 'coop_finance_month_detail.deduct_code', 'coop_loan.contract_number'
            ));
            $this->db->from('coop_non_pay');
            $this->db->join("coop_finance_month_profile", "coop_non_pay.non_pay_month = coop_finance_month_profile.profile_month
                            AND coop_non_pay.non_pay_year = coop_finance_month_profile.profile_year ", "inner");
            $this->db->join("coop_finance_month_detail", "coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id
                            AND coop_finance_month_detail.member_id = coop_non_pay.member_id", "inner");
            $this->db->join("coop_loan", "coop_finance_month_detail.loan_id = coop_loan.id", "inner");
            $this->db->where("coop_non_pay.non_pay_status NOT IN ('0')
                            AND coop_non_pay.member_id = '{$member_id}'
                            AND coop_finance_month_detail.deduct_code = 'LOAN'");
            $rs_debt = $this->db->get()->result_array();
            $arr_data['rs_debt'] =     @$rs_debt;
            $arr_data['count_debt'] = 0;
            if (!empty($rs_debt)) {
                foreach ($rs_debt as $key => $row_count_debt) {
                    if ($row_count_debt['profile_id'] != '') {
                        @$arr_data['sum_debt_balance'] += $row_count_debt['pay_amount'];
                        $arr_data['count_debt']++;
                    }
                }
            }

            //ข้อมูลเงินฝาก---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                $this->db->select(array(
                    "t3.account_id",
                    "t3.member_name",
                    "t3.mem_id",
                    "t3.type_id",
                    "t4.type_name",
                    "t3.account_status",
                    "IF(t3.account_status = 0,'ปกติ','ปิดบัญชี') AS account_status_text",
                    "t3.close_account_date",
                    "t1.transaction_time",
                    "t2.transaction_balance"
                ));
                $this->db->from('coop_maco_account AS t3');
                $this->db->join('(SELECT account_id, MAX(transaction_time) AS transaction_time,MAX(transaction_id) AS transaction_id FROM coop_account_transaction GROUP BY account_id) AS t1', 't1.account_id = t3.account_id', 'LEFT');
                $this->db->join('coop_account_transaction AS t2', 't1.account_id = t2.account_id AND t1.transaction_id = t2.transaction_id AND t1.transaction_time = t2.transaction_time', 'LEFT');
                $this->db->join('coop_deposit_type_setting as t4', 't3.type_id = t4.type_id', 'LEFT');
                $this->db->where("t3.mem_id = '{$member_id}' AND t3.account_status = 0");
                $deposit_data = $this->db->get()->result_array();
                $arr_data['deposit_data'] = $deposit_data;

            //บัญชีธนาคาร---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            $bank_account = $this->db->select(array(
                't1.*', 
                't2.branch_name'
            ))
            ->from('coop_mem_bank_account AS t1')
            ->join('coop_bank_branch AS t2','t1.dividend_bank_id = t2.bank_id AND t1.dividend_bank_branch_id = t2.branch_code','LEFT')
            ->where("t1.member_id = '{$member_id}' AND t1.dividend_bank_id = 006")
            ->order_by('t1.id DESC')
            ->limit(1)
            ->get()->result_array();
            $arr_data['bank_account'] = $bank_account[0];

            //ข้อมูลผู้กู้ ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            $this->db->select(array(
                't1.*',
                't1.approve_date as date_approve',
                't2.*',
                't3.prename_short',
                't3.prename_full',
                't4.district_name',
                't5.amphur_name',
                't6.province_name',
                't7.mem_group_name AS level_name',
                't8.mem_group_name AS faction_name',
                't9.mem_group_name AS department_name',
                't10.loan_reason',
                't13.account_name',
                't15.district_name AS m_district',
	            't16.amphur_name AS m_amphur',
	            't17.province_name AS m_province',
                '(select total_paid_per_month from coop_loan_period where coop_loan_period.loan_id = t1.id limit 1) as start_money_per_period',
                '(select date_period from coop_loan_period where coop_loan_period.loan_id = t1.id limit 1) as start_date_period',
                '(select total_paid_per_month from coop_loan_period where coop_loan_period.loan_id = t1.id order by date_period desc limit 1) as last_money_per_period',
                '(select date_period from coop_loan_period where coop_loan_period.loan_id = t1.id order by date_period desc limit 1) as last_date_period',
                '(select share_collect_value from coop_mem_share where coop_mem_share.member_id = t1.member_id and share_date <= t1.createdatetime order by share_date desc, share_id desc limit 1) as share_collect_value'
            ));
            $this->db->from('coop_loan as t1');
            $this->db->join("coop_mem_apply as t2","t2.member_id = t1.member_id","inner");
            $this->db->join("coop_prename as t3","t3.prename_id = t2.prename_id","left");
            $this->db->join("coop_district as t4","t2.c_district_id = t4.district_id","left");
            $this->db->join("coop_amphur as t5","t2.c_amphur_id = t5.amphur_id","left");
            $this->db->join("coop_province as t6","t2.c_province_id = t6.province_id","left");
            $this->db->join("coop_mem_group as t7","t2.level = t7.id","left");
            $this->db->join("coop_mem_group as t8","t2.faction = t8.id","left");
            $this->db->join("coop_mem_group as t9","t2.department = t9.id","left");
            $this->db->join("coop_loan_reason as t10","t1.loan_reason = t10.loan_reason_id","left");
            $this->db->join("coop_maco_account as t13","t1.member_id = t13.mem_id","left");
            $this->db->join("coop_district as t15","t2.m_district_id = t15.district_id","left");
            $this->db->join("coop_amphur as t16","t2.m_amphur_id = t16.amphur_id","left");
            $this->db->join("coop_province as t17","t2.m_province_id = t17.province_id","left");
            $this->db->where("t1.id = '".$loan_id."'");
            $this->db->limit(1);
            $row = $this->db->get()->row_array();
            $arr_data['data'] = $row;

            //เงินต้นคงเหลือ--------------------------------------------------------------------------------------------------------------------------------
            $remaining_principal = $this->db->select(array(
                't1.id',
                't2.loan_type_id',
                't3.loan_type',
                't1.loan_type AS loan_type_name',
                't1.petition_number',
                't1.loan_amount',
                't1.loan_amount_balance'
            ))
                ->from('coop_loan as t1')
                ->join('coop_loan_name as t2', 't1.loan_type = t2.loan_name_id', 'LEFT')
                ->join('coop_loan_type as t3', 't2.loan_type_id = t3.id', 'LEFT')
                ->where("member_id = '{$member_id}' AND loan_status = '1'")
                ->get()->result_array();

            $sum_remaining_principal = 0; //เงินต้นคงเหลือทั้งหมด
            $remaining_principal_emergency = 0; //เงินต้นคงเหลือฉุกเฉิน อิงจาก(loan_type_id)
            $remaining_principal_normal = 0; //เงินต้นคงเหลือสามัญ อิงจาก(loan_type_id)
            $remaining_principal_special = 0; //เงินต้นคงเหลือพิเศษ อิงจาก(loan_type_id)
            $remaining_housing_assistance = 0; //เงินต้นคงเหลือพิเศษ อิงจาก(loan_type_id)

            $remaining_principal_emergency_em = 0; //เงินต้นคงเหลือฉุกเฉิน-ฉุกเฉิน อิงจาก(loan_type)
            $remaining_principal_normal_nm = 0; //เงินต้นคงเหลือสามัญ-สามัญ อิงจาก(loan_type)
            $remaining_principal_normal_ql = 0; //เงินต้นคงเหลือสามัญ-สามัญเพื่อส่งเสริมและพัฒนาคุณภาพชีวิต อิงจาก(loan_type)
            $remaining_principal_normal_lp = 0; //เงินต้นคงเหลือสามัญ-เงินกู้โครงการสวัสดิการช่วยเหลือดำรงชีพ อิงจาก(loan_type)
            $remaining_principal_special_gun = 0; //เงินต้นคงเหลือพิเศษ-อาวุธปืน อิงจาก(loan_type)
            $remaining_principal_special_com = 0; //เงินต้นคงเหลือพิเศษ-คอมพิวเตอร์ อิงจาก(loan_type)
            $remaining_principal_special_motorcycle = 0; //เงินต้นคงเหลือพิเศษ-รถจักรยานยนต์ อิงจาก(loan_type)
            $remaining_principal_special_electrical = 0; //เงินต้นคงเหลือพิเศษ-เครื่องใช้ไฟฟ้า อิงจาก(loan_type)

            $credit_normal_nm = 0; //วงเงินกู้สามัญ-สามัญ
            $credit_normal_ql = 0; //วงเงินกู้สามัญ-สามัญเพื่อส่งเสริมและพัฒนาคุณภาพชีวิต
            $credit_emergency_em = 0; //วงเงินกู้ฉุกเฉิน-ฉุกเฉิน
            $credit_special = 0; //วงเงินกู้โครงการพิเศษ
            $credit_emergency = 0; //วงเงินกู้ฉุกเฉิน
            $credit_normal = 0; //วงเงินกู้สามัญ
            $remaining_housing_assistance_ha = 0;
            foreach ($remaining_principal as $value) {
                $sum_remaining_principal += $value['loan_amount_balance'];
                if ($value['loan_type_id'] == '1') {
                    $remaining_principal_emergency += $value['loan_amount_balance'];
                    $credit_emergency += $value['loan_amount'];
                }
                if ($value['loan_type_id'] == '2') {
                    $remaining_principal_normal += $value['loan_amount_balance'];
                    $credit_normal += $value['loan_amount'];
                }
                if ($value['loan_type_id'] == '3') {
                    $remaining_principal_special += $value['loan_amount_balance'];
                    $credit_special += $value['loan_amount'];
                }
                if ($value['loan_type_id'] == '5') {
                    $remaining_housing_assistance += $value['loan_amount_balance'];
                }
                if ($value['loan_type_name'] == '1') {
                    $remaining_principal_emergency_em += $value['loan_amount_balance'];
                    $credit_emergency_em += $value['loan_amount'];
                }
                if ($value['loan_type_name'] == '2') {
                    $remaining_principal_normal_nm += $value['loan_amount_balance'];
                    $credit_normal_nm += $value['loan_amount'];
                }
                if ($value['loan_type_name'] == '3') {
                    $remaining_principal_normal_ql += $value['loan_amount_balance'];
                    $credit_normal_ql += $value['loan_amount'];
                }
                if ($value['loan_type_name'] == '4') {
                    $remaining_principal_special_gun += $value['loan_amount_balance'];
                }
                if ($value['loan_type_name'] == '5') {
                    $remaining_principal_special_com += $value['loan_amount_balance'];
                }
                if ($value['loan_type_name'] == '6') {
                    $remaining_principal_special_motorcycle += $value['loan_amount_balance'];
                }
                if ($value['loan_type_name'] == '7') {
                    $remaining_principal_special_electrical += $value['loan_amount_balance'];
                }
                if ($value['loan_type_name'] == '8') {
                    $remaining_principal_normal_lp += $value['loan_amount_balance'];
                }
                if ($value['loan_type_name'] == '11') {
                    $remaining_housing_assistance_ha += $value['loan_amount_balance'];
                }
            }
            $arr_data['remaining_principal']=$remaining_principal;

            //วงเงินกู้คงเหลือ-----------------------------------------------------------------------------------------------------------------
            $credit_limit_remaining = $this->db->select(array(
                'estimate_receive_money'
            ))
            ->from("coop_loan_deduct_profile")
            ->where("loan_id = '{$loan_id}'")
            ->get()->result_array();
            $arr_data['credit_limit_remaining']=$credit_limit_remaining[0];

            //หุ้นค้ำประกัน ----------------------------------------------------------------------------------
            $share_guarantee = $this->db->select(array('*'))
            ->from("coop_loan_guarantee")
            ->where("loan_id = '{$loan_id}' AND guarantee_type = '2'")
            ->get()->result_array();
            $arr_data['share_guarantee']=$share_guarantee[0];

            //เงินฝากค้ำประกัน -------------------------------------------------------------------------------
            $deposit_guarantee = $this->db->select(array('*'))
            ->from("coop_loan_guarantee")
            ->where("loan_id = '{$loan_id}' AND guarantee_type = '3'")
            ->get()->result_array();
            $arr_data['deposit_guarantee']=$deposit_guarantee[0];

            //ข้อมูลผู้ค้ำ ------------------------------------------------------------------------------------
                $this->db->select(array('principal_payment','total_paid_per_month','date_period'));
                $this->db->from('coop_loan_period');
                $this->db->where("loan_id = '".$loan_id."' AND period_count = '1'");
                $row = $this->db->get()->row_array();
                $arr_data['data_period_1'] = $row;
                $arr_guarantee_person = array();
                $this->db->select(array(
                    't1.*',
                    't2.*',
                    't3.*',
                    't7.mem_group_id','t7.mem_group_parent_id','t7.mem_group_type','t7.mem_group_name AS affiliation',
                    't8.mem_group_id','t8.mem_group_parent_id','t8.mem_group_type','t8.mem_group_name',
                    't9.mem_group_id','t9.mem_group_parent_id','t9.mem_group_type','t9.mem_group_name',
                    't10.*',
                    't4.district_name AS c_district',
	                't5.amphur_name AS c_amphur',
	                't6.province_name AS c_province',
	                't11.district_name AS m_district',
	                't12.amphur_name AS m_amphur',
	                't13.province_name AS m_province'
                ));
                $this->db->from('coop_loan_guarantee_person as t1');
                $this->db->join("coop_mem_apply as t2","t2.member_id = t1.guarantee_person_id","left");
                $this->db->join("coop_prename as t3","t3.prename_id = t2.prename_id","left");
                $this->db->join("coop_district as t4","t2.c_district_id = t4.district_id","left");
                $this->db->join("coop_amphur as t5","t2.c_amphur_id = t5.amphur_id","left");
                $this->db->join("coop_province as t6","t2.c_province_id = t6.province_id","left");
                $this->db->join("coop_mem_group as t7","t2.level = t7.id","left");
                $this->db->join("coop_mem_group as t8","t2.faction = t8.id","left");
                $this->db->join("coop_mem_group as t9","t2.department = t9.id","left");
                $this->db->join("coop_mem_type AS t10","t2.mem_type_id = t10.mem_type_id","left");
                $this->db->join("coop_district as t11","t2.m_district_id = t11.district_id","left");
                $this->db->join("coop_amphur as t12","t2.m_amphur_id = t12.amphur_id","left");
                $this->db->join("coop_province as t13","t2.m_province_id = t13.province_id","left");
                $this->db->where("t1.loan_id = '".$loan_id."'");
                $rs_guarantee_person = $this->db->get()->result_array();
                $arr_data['data_guarantee']=$rs_guarantee_person;
                $count_data = 0;
                foreach ($arr_data['data_guarantee'] as $data => $count) {
                    $count_data = $data;
                    $count_data++;
                }

                    if (!empty($rs_guarantee_person)) {
                    foreach ($rs_guarantee_person AS $key => $val) {
                        $arr_guarantee_person[$key]['full_name'] = @$val['prename_full'] . @$val['firstname_th'] . " " . @$val['lastname_th'];
                        $arr_guarantee_person[$key]['member_id'] = @$val['member_id'];
                        $arr_guarantee_person[$key]['id_card'] = @$val['id_card'];
                        $arr_guarantee_person[$key]['salary'] = number_format(@$val['salary'], 2);
                        $arr_guarantee_person[$key]['guarantee_amount'] = number_format(@$val['guarantee_person_amount'], 2);
                        $arr_guarantee_person[$key]['guarantee_amount_text'] = $this->center_function->convert(number_format((@$val['guarantee_person_amount']), 2));
                        $arr_guarantee_person[$key]['user_code'] = @$val['id_card'];
                        $arr_guarantee_person[$key]['rank'] = @$val['position'];
                        $arr_guarantee_person[$key]['department'] = @$val['department_name'];
                        $arr_guarantee_person[$key]['faction'] = @$val['faction_name'];
                        $arr_guarantee_person[$key]['level'] = @$val['affiliation'];
                        $arr_guarantee_person[$key]['tel'] = @$val['tel'];
                        $arr_guarantee_person[$key]['mobile'] = @$val['mobile'];
                        $arr_guarantee_person[$key]['address_no'] = @$val['c_address_no'];
                        $arr_guarantee_person[$key]['address_moo'] = @$val['c_address_moo'];
                        $arr_guarantee_person[$key]['address_soi'] = @$val['c_address_soi'];
                        $arr_guarantee_person[$key]['address_road'] = @$val['c_address_road'];
                        $arr_guarantee_person[$key]['district_name'] = @$val['c_district'];
                        $arr_guarantee_person[$key]['amphur_name'] = @$val['c_amphur'];
                        $arr_guarantee_person[$key]['province_name'] = @$val['c_province'];
                        $arr_guarantee_person[$key]['zipcode'] = @$val['c_zipcode'];
                        $arr_guarantee_person[$key]['email'] = @$val['email'];
                        $arr_guarantee_person[$key]['guarantee_member_agey'] = @$val['guarantee_member_agey'];
                        $arr_guarantee_person[$key]['guarantee_member_agem'] = @$val['guarantee_member_agem'];
                        $arr_guarantee_person[$key]['spouse_name'] = @$val['spouse_name'];
                        $arr_guarantee_person[$key]['birthday'] = @$val['birthday'];
                        $arr_guarantee_person[$key]['marry_status'] = @$val['marry_status'];
                        $arr_guarantee_person[$key]['marry_name'] = @$val['marry_name'];
                        $arr_guarantee_person[$key]['m_id_card'] = @$val['m_id_card'];
                        $arr_guarantee_person[$key]['m_address_no'] = @$val['m_address_no'];
                        $arr_guarantee_person[$key]['m_address_moo'] = @$val['m_address_moo'];
                        $arr_guarantee_person[$key]['m_address_village'] = @$val['m_address_village'];
                        $arr_guarantee_person[$key]['m_address_road'] = @$val['m_address_road'];
                        $arr_guarantee_person[$key]['m_address_soi'] = @$val['m_address_soi'];
                        $arr_guarantee_person[$key]['m_district_name'] = @$val['m_district'];
                        $arr_guarantee_person[$key]['m_amphur_name'] = @$val['m_amphur'];
                        $arr_guarantee_person[$key]['m_province_name'] = @$val['m_province'];
                        $arr_guarantee_person[$key]['m_zipcode'] = @$val['m_zipcode'];
                        $arr_guarantee_person[$key]['m_tel'] = @$val['m_tel'];
                        $arr_guarantee_person[$key]['affiliation'] = @$val['affiliation'];
                        $arr_guarantee_person[$key]['position'] = @$val['position'];
                        }
                    }
                    $arr_data['guarateedata'] = $arr_guarantee_person;

                    //--------------------------------------------------------------------------------------------------------------------------------------------
                    $this->db->select(array('loan_deduct_amount'));
                    $this->db->from('coop_loan_deduct');
                    $this->db->where("loan_id = '" . $loan_id . "' AND loan_deduct_list_code = 'deduct_pay_prev_loan'");
                    $row = $this->db->get()->row_array();
                    $arr_data['prev_loan_amount'] = $row['loan_deduct_amount'];
                    $arr_data['balance_loan'] = @$arr_data['data']['loan_amount'] - @$row['loan_deduct_amount'];

                    $this->db->select(array('loan_deduct_amount'));
                    $this->db->from('coop_loan_deduct');
                    $this->db->where("loan_id = '" . $loan_id . "' AND loan_deduct_list_code = 'deduct_person_guarantee'");
                    $row = $this->db->get()->row_array();
                    $arr_data['data_guarantee_cash'] = $row['loan_deduct_amount'];

                    //เรียงลำดับเอกสาร -----------------------------------------------------------------------------------------------
                    $this->db->select(array('type_pdf_id','transfer_pdf_id','surety_pdf_id','append_pdf_id'));
                        $this->db->from('coop_term_of_loan');
                        $this->db->where("type_id = '" . @$arr_data['data']['loan_type'] . "' AND start_date <= '" . date('Y-m-d') . "'");
                        $this->db->order_by('start_date DESC');
                        $this->db->limit(1);
                        $path2 = $this->db->get()->result_array();
                        
                    $path2[0]['type_pdf_id']=$path2[0]['type_pdf_id']==''?$path2[0]['type_pdf_id']='0':$path2[0]['type_pdf_id']; 
                        $get_id = $path2[0]['type_pdf_id']				!=""	?	$path2[0]['type_pdf_id']							:	"";
                        $get_id = $path2[0]['transfer_pdf_id']			!=""	?	$get_id ." , ". $path2[0]['transfer_pdf_id']		:	$get_id;
                        $get_id = $path2[0]['surety_pdf_id']			!=""	?	$get_id ." , ". $path2[0]['surety_pdf_id']			:	$get_id;
                        $get_id = $path2[0]['append_pdf_id']			!=""	?	$get_id ." , ". $path2[0]['append_pdf_id']			:	$get_id;
                        
                        $order =array(
                        $path2[0]['type_pdf_id']!=''? "ORDER BY CASE WHEN id=".$path2[0]['type_pdf_id']." THEN 1":'',
                        $path2[0]['transfer_pdf_id']!=''? "WHEN id=".$path2[0]['transfer_pdf_id']." THEN 2":'',
                        $path2[0]['surety_pdf_id']!=''? "WHEN id=".$path2[0]['surety_pdf_id']." THEN 3":'',
                        $path2[0]['append_pdf_id']!=''? "WHEN id=".$path2[0]['append_pdf_id']." THEN 4":'');
                    #-----------------------------------------------------------------------------------------------
                    $pathresult = $this->db->query("SELECT id,path_data,type_loan FROM coop_type_pdf as t1 WHERE t1.id IN (".$get_id.") ".$order[0]." ".$order[1]." ".$order[2]." ".$order[3]." END")
                    ->result_array();
                    $arr_data['path_data'] = $pathresult;
                    #-----------------------------------------------------------------------------------------------
                foreach ($pathresult as $key1 => $value) {
                        if ($value['type_loan']=="12") {
                            for ($i=0; $i <count($arr_data['guarateedata']) ; $i++) { 
                                $data_in_path = $this->db->query("SELECT * FROM `coop_type_pdf_details` WHERE type_pdf_id = '".$value['id']."'")->result_array();
                                $arr_data['path_data'][$key1]['data_in_path_'.$i] = $data_in_path;
                            }
                        }

                        $data_in_path = $this->db->query("SELECT * FROM `coop_type_pdf_details` WHERE type_pdf_id = '".$value['id']."'")->result_array();
                        $page_arr_path= array();

                        foreach($data_in_path as $key => $value){
                            $page = $value['page_no'];
                            $page_arr_path[$page][] = $value;
                        }
                        
                        //ใส่ลำดับต่อท้ายcode ข้อมูลผู้ค้ำในตาราง
                        foreach($page_arr_path AS $page => $list){
                            $i=0;$g=0;$s=0;$n=1;$pa=0;$dt=0;$da=0;$lt=0;$lta=0;$ag=0;$pg=0;
                            foreach($list AS $data => $value){
                                if($value['data_name']=="[guarantee_fullname]"){
                                    $page_arr_path[$page][$data]['data_name']="[guarantee_fullname_$i]";
                                $i++;
                                }
                                if($value['data_name']=="[mem_guarantor_id]"){
                                    $page_arr_path[$page][$data]['data_name']="[mem_guarantor_id_$g]";
                                $g++;
                                }
                                if($value['data_name']=="[salary_guarantor_no]"){
                                    $page_arr_path[$page][$data]['data_name']="[salary_guarantor_no_$s]";
                                $s++;
                                }
                                if($value['data_name']=="[guarantor_no]"){
                                    $page_arr_path[$page][$data]['data_name']="[guarantor_no_$n]";
                                $n++;
                                }
                                if($value['data_name']=="[position_affiliation_guarantor]"){
                                    $page_arr_path[$page][$data]['data_name']="[position_affiliation_guarantor_$pa]";
                                $pa++;
                                }
                                if($value['data_name']=="[deposit_type]"){
                                    $page_arr_path[$page][$data]['data_name']="[deposit_type_$dt]";
                                $dt++;
                                }
                                if($value['data_name']=="[deposit_amount]"){
                                    $page_arr_path[$page][$data]['data_name']="[deposit_amount_$da]";
                                $da++;
                                }
                                if($value['data_name']=="[loan_type]"){
                                    $page_arr_path[$page][$data]['data_name']="[loan_type_$lt]";
                                $lt++;
                                }
                                if($value['data_name']=="[loan_type_amount]"){
                                    $page_arr_path[$page][$data]['data_name']="[loan_type_amount_$lta]";
                                $lta++;
                                }
                                if($value['data_name']=="[affiliation_g]"){
                                    $page_arr_path[$page][$data]['data_name']="[affiliation_g_$ag]";
                                $ag++;
                                }
                                if($value['data_name']=="[position_g]"){
                                    $page_arr_path[$page][$data]['data_name']="[position_g_$pg]";
                                $pg++;
                                }
                            }
                        }
                        $arr_data['path_data'][$key1]['data_in_path'] = $page_arr_path;

                                $total_month = $this->db->query("SELECT SUM(loan_cost_amount) as loan_cost_amount FROM `coop_outgoing` INNER JOIN `coop_loan_cost_mod` ON `coop_outgoing`.`outgoing_code`=`coop_loan_cost_mod`.`loan_cost_code` WHERE `loan_id` = '".$loan_id."' AND `member_id` = '".$arr_data['data']['member_id']."' ORDER BY `coop_outgoing`.`outgoing_no`")->result_array();
                                $sum_total = $this->db->query("SELECT `principal_payment` as sum_total FROM `coop_loan_period` WHERE `loan_id` = '".$loan_id."' AND `date_count` = '31' LIMIT 1")->result_array();		
                                $loan_principle_total = 0;
                                    $loan_interest_total = 0;
                                    $this->db->join("coop_loan_name", "coop_loan_name.loan_name_id = coop_loan.loan_type", "inner");
                                    $loans = $this->db->get_where("coop_loan", array(
                                        "member_id" => "{$arr_data['data']['member_id']}",
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
                                        $value['money_per_period'] = $this->db->get_where("coop_loan", array("id"=>$value['id']))->row_array()['money_per_period'];
                                        $this->db->order_by("transaction_datetime desc, loan_transaction_id desc");
                                        $this->db->limit(1);
                                        $loan_transaction = $this->db->get_where("coop_loan_transaction", array(
                                            "loan_id" => $value['id'],
                                            "transaction_datetime <= " => $arr_data['data']['createdatetime'],
                                            "loan_amount_balance >= " => 0
                                        ))->row_array();
                                        
                                        $this->db->order_by("start_date desc");
                                        $this->db->where("type_id", $value['loan_type']);
                                        $this->db->where("start_date <= '".$arr_data['data']['createdatetime']."'");
                                        $term = $this->db->get("coop_term_of_loan")->row_array();
                                        
                                        if($loan_transaction['loan_amount_balance'] > 0){
                                            $arr_list_loan[$value['id']]['loan_principle'] = $value['money_per_period']; //ยอดที่ชำระต่อเดือน เงินต้น
                                            $temp_interest_31_day = (((@$loan_transaction['loan_amount_balance'] * $term['interest_rate']) / 100) / 365) * 31;
                                            $temp_interest_31_day = $this->center_function->round_satang(@$temp_interest_31_day, 0.25);
                                            $arr_list_loan[$value['id']]['loan_interest'] = @$temp_interest_31_day; //(ดอกเบี้ย)
                                            $arr_list_loan[$value['id']]['loan_amount_balance'] = $loan_transaction['loan_amount_balance']; //(balance)
                                            $arr_list_loan[$value['id']]['loan_id'] = $value['id']; //loan_id
                                            $arr_list_loan[$value['id']]['contract_number'] = $value['contract_number']; //contract_number
                                            $loan_principle_total += $value['money_per_period'];
                                            $loan_interest_total += $temp_interest_31_day;
                                        }else{
                                            if($value['loan_status']!="0"){
                                                if($loan_transaction['loan_transaction_id']!=""){
                                                    $this->db->order_by("transaction_datetime desc, loan_transaction_id desc");
                                                    $this->db->limit(1);
                                                    $tmp_loan_transaction = $this->db->get_where("coop_loan_transaction", array(
                                                        "loan_id" => $value['id'],
                                                        "transaction_datetime <= " => $arr_data['data']['createdatetime'],
                                                        "loan_amount_balance >= " => 0,
                                                        "loan_transaction_id < " => $loan_transaction['loan_transaction_id']
                                                    ))->row_array();
                                                    if($value['loan_status']!=4){
                                                        $loan_transaction = $tmp_loan_transaction;
                                                        $arr_list_loan[$value['id']]['loan_principle'] = $value['money_per_period']; //ยอดที่ชำระต่อเดือน เงินต้น
                                                        $temp_interest_31_day = (((@$loan_transaction['loan_amount_balance'] * $term['interest_rate']) / 100) / 365) * 31;
                                                        $temp_interest_31_day = $this->center_function->round_satang(@$temp_interest_31_day, 0.25);
                                                        $arr_list_loan[$value['id']]['loan_interest'] = @$temp_interest_31_day; //(ดอกเบี้ย)
                                                        $arr_list_loan[$value['id']]['loan_amount_balance'] = $loan_transaction['loan_amount_balance']; //(balance)
                                                        $arr_list_loan[$value['id']]['loan_id'] = $value['id']; //loan_id
                                                        $arr_list_loan[$value['id']]['contract_number'] = $value['contract_number']; //contract_number
                                                        $loan_principle_total += $value['money_per_period'];
                                                        $loan_interest_total += $temp_interest_31_day;
                                                    }
                                                }else{
                                                    $loan_transaction = $value['loan_amount_balance'];
                                                    $arr_list_loan[$value['id']]['loan_principle'] = $value['money_per_period']; //ยอดที่ชำระต่อเดือน เงินต้น
                                                    $temp_interest_31_day = (((@$loan_transaction * $term['interest_rate']) / 100) / 365) * 31;
                                                    $temp_interest_31_day = $this->center_function->round_satang(@$temp_interest_31_day, 0.25);
                                                    $arr_list_loan[$value['id']]['loan_interest'] = @$temp_interest_31_day; //(ดอกเบี้ย)
                                                    $arr_list_loan[$value['id']]['loan_amount_balance'] = $loan_transaction; //(balance)
                                                    $arr_list_loan[$value['id']]['loan_id'] = $value['id']; //loan_id
                                                    $arr_list_loan[$value['id']]['contract_number'] = $value['contract_number']; //contract_number
                                                    $loan_principle_total += $value['money_per_period'];
                                                    $loan_interest_total += $temp_interest_31_day;
                                                }
                                            }
                                        }
                                    }
                        // แสดงข้อมูลเอกสารค้ำประกัน -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        foreach ($arr_data['path_data'] AS $path_detail =>$val_detail){
                        if ($val_detail['type_loan']=='12'){
                            for ($is=0; $is < count($arr_data['guarateedata']);) { 
                                foreach ($data_in_path as $key2 => $v) {
                                    $array_data = $arr_data['data'];
                                    $data = $v['data_name'];
                                    $array_list_data = $arr_data['guarateedata'][$is];
                                    if($data== "[approve_date]"){ //วัน เดือน ปี ที่อนุมัติ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = ($array_data['date_approve']!='')?(int)date("d", strtotime($array_data['date_approve']))." ". $arrMM[date("n", strtotime($array_data['date_approve']))]." ". (date("Y", strtotime($array_data['date_approve']))+543):'';
                                    }
                                    if($data == "[period_amount]"){ //จำนวนงวด
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_data['period_amount'];
                                    }
                                    if($data== "[location]"){ //สถานที่เขียน
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name']= $arr_data['profile_location']['coop_name_th'];
                                    }
                                    if($data == "[contract_number]"){ //รหัสสัญญา
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_data['contract_number'];
                                    }
                                    if($data == "[createdatetime]"){ //วันที่ขอกู้ (วันที่/เดือน/ปี)
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = (int)date("d", strtotime($array_data['createdatetime']))." ". $arrMM[date("n", strtotime($array_data['createdatetime']))]." ". (date("Y", strtotime($array_data['createdatetime']))+543);
                                    }
                                    if($data == "[createdatetime_top_day]"){ //วันที่ขอกู้ (วันที่)
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = (int)date("d", strtotime($array_data['createdatetime']));
                                    }
                                    if($data == "[createdatetime_top_month]"){ //วันที่ขอกู้ (เดือน แบบย่อ)
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $arrMM_short[date("n", strtotime($array_data['createdatetime']))];
                                    }
                                    if($data == "[createdatetime_top_month_full]"){ //วันที่ขอกู้ (เดือน แบบเต็ม)
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $arrMM[date("n", strtotime($array_data['createdatetime']))];
                                    }
                                    if($data == "[createdatetime_top_year]"){ //วันที่ขอกู้ (ปี)
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = date("Y", strtotime($array_data['createdatetime']))+543;
                                    }
                                    if($data == "[fullname]"){ //ชื่อผู้กู้
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_data['prename_full'].$array_data['firstname_th']." ".$array_data['lastname_th'];
                                    }
                                    if($data == "[member_id]"){ //รหัสมาชิกผู้กู้
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_data['member_id'];
                                    }
                                    if($data == "[full_name_guarantor]"){ //ชื่อผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['full_name']!=""? $array_list_data['full_name']:" ";
                                    }
                                    if($data == "[age_guarantor]"){ //อายุผู้ค้ำ ปี
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $this->center_function->cal_age($array_list_data['birthday']);
                                    }
                                    if($data == "[id_card_guarantor]"){ //เลข ปชช.ผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['id_card'];
                                    }
                                    if($data == "[position_guarantor]"){ //ตำแหน่งงานผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = ($array_list_data['rank'] == '')?'':$array_list_data['rank'];
                                    }
                                    if($data == "[affiliation_guarantor]"){ //สังกัดผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = ($array_list_data['level'] == '')?'':$array_list_data['level'];
                                    }
                                    if($data == "[address_guarantor_no]"){ //บ้านเลขที่ ผู้ค้ำ 
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['address_no'];
                                    }
                                    if($data == "[address_guarantor_moo]"){ //หมู่บ้าน ผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['address_moo'];
                                    }
                                    if($data == "[address_guarantor_soi]"){ //ซอย ผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['address_soi'];
                                    }
                                    if($data == "[address_guarantor_road]"){ //ถนน ผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['address_road'];
                                    }
                                    if($data == "[district_guarantor_name]"){ //ตำบล ผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['district_name'];
                                    }
                                    if($data == "[amphur_guarantor_name]"){ //อำเภอ ผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['amphur_name'];
                                    }
                                    if($data == "[province_guarantor_name]"){ //จังหวัด ผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['province_name'];
                                    }
                                    if($data == "[zipcode_guarantor]"){ //รหัสไปรษณีย์ ผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['zipcode'];
                                    }
                                    if($data == "[tel_guarantor]"){ //เบอร์บ้าน ผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = ($array_list_data['tel']=='')?'':$array_list_data['tel'];
                                    }
                                    if($data == "[mobile_guarantor]"){ //เบอร์มือถือ ผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = ($array_list_data['mobile']=='')?'':$array_list_data['mobile'];
                                    }
                                    if($data == "[loan_amount]"){ //จำนวนเงินกู้
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = number_format($array_data['loan_amount'],2);
                                    }
                                    if($data == "[loan_amount_text]"){ //จำนวนเงินกู้ ข้อความ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $this->center_function->convert(number_format($array_data['loan_amount'],2));
                                    }
                                    if($data == "[money_per_period]"){ //จำนวนเงินชำระต่องวด
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = number_format($array_data['money_per_period'],2);
                                    }
                                    if($data == "[money_per_period_text]"){ //จำนวนเงินชำระต่องวด เป็นข้อความ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $this->center_function->convert($array_data['money_per_period']);
                                    }
                                    if($data == "[last_money_per_period]"){ //จำวนเงินชำระงวดสุดท้าย
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = number_format($array_data['last_money_per_period'],2);
                                    }
                                    if($data == "[last_money_per_period_text]"){ //จำวนเงินชำระงวดสุดท้าย เป็นข้อความ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $this->center_function->convert($array_data['last_money_per_period']);
                                    }
                                    if($data == "[email]"){ //อีเมลผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = ($array_list_data['email']==''||$array_list_data['email']=' ')?'':$array_data['email'];
                                    }
                                    if($data == "[member_id_guarantor]"){ //รหัสสมาชิกผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['member_id'];
                                    }
                                    if($data == "[salary_guarantor]"){ //เงินเดือนผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = ($array_list_data['salary']=='0'||$array_list_data['salary']=='')?'':$array_list_data['salary'];
                                    }
                                    if($data == "[position_affiliation_g]"){ //ตำแหน่ง/สังกัด ผู้ค้ำ
                                        $position = $array_list_data['rank'];
                                        $affiliation = $array_list_data['level'];
                                        if ($position && $affiliation != '') {
                                            $half = "/";
                                        }else{
                                            $half = "";
                                        }
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $position . $half . $affiliation;
                                    }

                                    if($data == "[g_marry_name]"){ //ชื่อคู่สมรสผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['marry_status']=='2'?$array_list_data['marry_name']: "";
                                    }
                                    if($data == "[g_address_no]"){ //บ้านเลขที่ คู่สมรสผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['marry_status']=='2'?$array_list_data['m_address_no']: "";
                                    }
                                    if($data == "[g_address_moo]"){ //หมู่ที่ คู่สมรสผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['marry_status']=='2'?$array_list_data['m_address_moo']: "";
                                    }
                                    if($data == "[g_address_soi]"){ //ซอย คู่สมรสผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['marry_status']=='2'?$array_list_data['m_address_soi']: "";
                                    }
                                    if($data == "[g_address_road]"){ //ถนน คู่สมรสผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['marry_status']=='2'?$array_list_data['m_address_road']: "";
                                    }
                                    if($data == "[g_district_name]"){ //ตำบล คู่สมรสผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['marry_status']=='2'?$array_list_data['m_district_name']: "";
                                    }
                                    if($data == "[g_amphur_name]"){ //อำเภอ คู่สมรสผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['marry_status']=='2'?$array_list_data['m_amphur_name']: "";
                                    }
                                    if($data == "[g_province_name]"){ //จังหวัด คู่สมรสผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['marry_status']=='2'?$array_list_data['m_province_name']: "";
                                    }
                                    if($data == "[g_zipcode]"){ //รหัสไปรษณีย์ คู่สมรสผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['marry_status']=='2'?$array_list_data['m_zipcode']: "";
                                    }
                                    if($data == "[g_id_card]"){ //เลขปชช คู่สมรสผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['marry_status']=='2'?$array_list_data['m_id_card']: "";
                                    }
                                    if($data == "[g_tel]"){ //หมายเลขโทรศัพท์ คู่สมรสผู้ค้ำ
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['marry_status']=='2'?$array_list_data['m_tel']: "";
                                    }
                                    if($data == "[g_fullname]"){ //ชื่อผู้ค้ำ กรณีเอกสารคำยินยอมคู่สมรส
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['marry_status']=='2'?$array_list_data['full_name']: "";
                                    }
                                    if($data == "[g_location]"){ //สถานที่เขียน กรณีเอกสารคำยินยอมคู่สมรส
                                        $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] = $array_list_data['marry_status']=='2'?$arr_data['profile_location']['coop_name_th']: "";
                                    }

                                    for ($i=0; $i < strlen($arr_data['data']['id_card']); ) { 
                                        if($data == "[id_card_".$i."]"){
                                            $arr_data['path_data'][$key1]['data_in_path_'.$is][$key2]['data_name'] =  $array_list_data['id_card'][$i];
                                        }
                                        $i++;
                                    }
                                    
                                }
                                $is++;
                            }
                        }else{
                        // ข้อมูลการกู้เงินบนเอกสารอื่นๆที่ไม่ใช่เอกสารค้ำประกัน -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        foreach ($page_arr_path as $key2 => $y) {
                            foreach ($y as $key3 => $v) {
                            $loan_type = $arr_data['loan_type'];
                            $array_data = $arr_data['data'];
                            $pay = 	($loan_principle_total + $loan_interest_total+$array_data['share_month']+$total_month[0]['loan_cost_amount'])+($this->center_function->round_satang((((($array_data['loan_amount']*$array_data['interest_per_year'])/100)/365)*31), 0.25)+$sum_total[0]['sum_total']);
                            $date_start_period=$arr_data['data']['date_start_period'];
                            $period_amount=$arr_data['data']['period_amount'];
                            $date_laast_period = date('Y-m-d', strtotime($date_start_period . " +$period_amount month"));
                            $position_loan = $array_data['position'];
                            $affiliation_loan = $array_data['level_name'];
                            if ($position_loan && $affiliation_loan != '') {
                                $half_loan = "/";
                            }
                            $position_affiliation = $position_loan.$half_loan.$affiliation_loan;
                        
                            $data = $v['data_name'];
                            if($data== "[approve_date]"){ //วัน เดือน ปี ที่อนุมัติ
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = ($array_data['date_approve']!='')?(int)date("d", strtotime($array_data['date_approve']))." ". $arrMM[date("n", strtotime($array_data['date_approve']))]." ". (date("Y", strtotime($array_data['date_approve']))+543):'';
                            }
                            if($data== "[location]"){ //สถานที่เขียน
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name']= $arr_data['profile_location']['coop_name_th'];
                            }
                            if($data == "[contract_number]"){ //รหัสสัญญา
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['contract_number'];
                            }
                            if($data == "[createdatetime_top_day]"){ //วันที่ขอกู้ (วันที่)
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = (int)date("d", strtotime($array_data['createdatetime']));
                            }
                            if($data == "[createdatetime_top_month]"){ //วันที่ขอกู้ (เดือน แบบย่อ)
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $arrMM_short[date("n", strtotime($array_data['createdatetime']))];
                            }
                            if($data == "[createdatetime_top_month_full]"){ //วันที่ขอกู้ (เดือน แบบเต็ม)
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $arrMM[date("n", strtotime($array_data['createdatetime']))];
                            }
                            if($data == "[createdatetime_top_year]"){ //วันที่ขอกู้ (ปี)
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = date("Y", strtotime($array_data['createdatetime']))+543;
                            }
                            if ($data == "[createdatetime]") { //วันที่ขอกู้ (วัน/เดือน/ปี)
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $this->center_function->ConvertToThaiDate($array_data['createdatetime'], 0, 0);
                            }
                            if($data == "[fullname]"){ //ชื่อผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['prename_full'].$array_data['firstname_th']." ".$array_data['lastname_th'];
                            }
                            if($data == "[member_id]"){ //รหัสสมาชิกผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['member_id'];
                            }
                            if($data == "[age_year]"){ //อายุผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $this->center_function->cal_age($array_data['birthday']);
                            }
                            if($data == "[id_card]"){ //เลข ปชช. ผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['id_card'];
                            }
                            if($data == "[position]"){ //ตำแหน่งของผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = ($array_data['position'] == '')?'':$array_data['position'];
                            }
                            if($data == "[affiliation]"){ //สังกัดของผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = ($array_data['level_name'] == '')?'':$arr_data['data']['level_name'];
                            }
                            if ($data == "[position_affiliation]") { //ตำแหน่ง/สังกัดของผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $position_affiliation;
                            }
                            if($data == "[salary]"){ //เงินเดือนผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format(($array_data['salary']),2);
                            }
                            if($data == "[address_no]"){ //บ้านเลขที่
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['c_address_no'];
                            }
                            if($data == "[address_moo]"){ //หมู่ที่ ผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = ($array_data['c_address_moo']==" "||$array_data['c_address_moo']=="")?'':$array_data['c_address_moo'];
                            }
                            if($data == "[address_soi]"){ //ซอย ผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] =  ($array_data['c_address_soi']==" "||$array_data['c_address_soi']=="")?'':$array_data['c_address_soi'];
                            }
                            if($data == "[address_road]"){ //ถนน ผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] =  ($array_data['c_address_road']==" "||$array_data['c_address_road']=="")?'':$array_data['c_address_road'];
                            }
                            if($data == "[district_name]"){ //ตำบล ผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] =  ($array_data['district_name']==" "||$array_data['district_name']=="")?'':$array_data['district_name'];
                            }
                            if($data == "[amphur_name]"){ //อำเภอ ผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] =  ($array_data['amphur_name']==" "||$array_data['amphur_name']=="")?'':$array_data['amphur_name'];
                            }
                            if($data == "[province_name]"){ //จังหวัด ผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] =  ($array_data['province_name']==" "||$array_data['province_name']=="")?'':$array_data['province_name'];
                            }
                            if($data == "[zipcode]"){ //รหัสไปรษณีย์ ผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] =  ($array_data['zipcode']==" "||$array_data['zipcode']=="")?'':$array_data['zipcode'];
                            }
                            if($data == "[tel]"){ //เบอร์บ้าน ผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = ($array_data['tel'] ==''||$array_data['tel'] ==' ')?'':$array_data['tel'];
                            }
                            if($data == "[mobile]"){ //เบอร์มือถือ ผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = ($array_data['mobile'] ==''||$array_data['mobile'] ==' ')?'':$array_data['mobile'];
                            }
                            if($data == "[loan_amount]"){ //จำนวนเงินกู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format($array_data['loan_amount'],2);
                            }
                            if($data == "[loan_amount_text]"){ //จำนวนเงินกู้ เป็นข้อความ
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $this->center_function->convert(number_format($array_data['loan_amount'],2));
                            }
                            if($data == "[loan_reason]"){ // เหตุผลการกู้เงิน
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['loan_reason'];
                            }
                            if($data == "[interest_per_year]"){ //ดอกเบี้ยต่อปี
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['interest_per_year'];
                            }
                            if($data == "[period_amount]"){ //จำนวนงวด
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['period_amount'];
                            }
                            if($data == "[money_per_period]"){ //จำนวนเงินชำระต่องวด
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format($array_data['money_per_period'],2);
                            }
                            if($data == "[money_per_period_text]"){ //จำนวนเงินชำระต่องวด เป็นข้อความ
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $this->center_function->convert($array_data['money_per_period']);
                            }
                            if($data == "[last_money_per_period]"){ //จำวนเงินชำระงวดสุดท้าย
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format($array_data['last_money_per_period'],2);
                            }
                            if($data == "[last_money_per_period_text]"){ //จำวนเงินชำระงวดสุดท้าย เป็นข้อความ
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $this->center_function->convert($array_data['last_money_per_period']);
                            }
                            if($data == "[share_collect_value]"){ //ยอดหุ้น
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format($array_data['share_collect_value'], 2);
                            }
                            if($data == "[share_collect_value_text]"){ //ยอดหุ้น เป็นข้อความ
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $this->center_function->convert(number_format($array_data['share_collect_value'],2));
                            }
                            if ($data == "[date_full_period_1]") { //เริ่มต้นชำระ วัน/เดือน/ปี งวดแรก
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $this->center_function->ConvertToThaiDate($array_data['date_start_period'],0,0,0);
                            }
                            if ($data == "[date_period_1]") { //เริ่มต้นชำระ(เดือน) งวดแรก
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $monthtext[date("n", strtotime($array_data['date_start_period']))];
                            }
                            if ($data == "[date_year_period_1]") { //เริ่มต้นชำระ(ปี) งวดรก
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = date("Y", strtotime($array_data['date_start_period']+543));
                            }
                            if ($data == "[date_date_period_2]") { //ชำระถึง(วันที่) งวดสุดท้าย
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = (int)date("d", strtotime($array_data['last_date_period']));
                            }
                            if ($data == "[date_period_2]") { //ชำระถึง(เดือน) งวดสุดท้าย
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $monthtext[date("n", strtotime($array_data['last_date_period']))];
                            }
                            if ($data == "[date_year_period_2]") { //ชำระถึง(ปี) วงดสุดท้าย
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = date("Y", strtotime($array_data['last_date_period']+543));;
                            }
                            if ($data == "[loan_amount_balance_nm]") { //เงินต้นกู้คงเหลือ(สามัญ)ทุกประเภท
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format(($remaining_principal_normal), 2);
                            }
                            if ($data == "[loan_amount_balance_em]") { //เงินต้นกู้คงเหลือ(ฉุกเฉิน)ทุกประเภท
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format(($remaining_principal_emergency), 2);
                            }
                            if ($data == "[loan_amount_balance_sp]") { //เงินต้นกู้คงเหลือ(พิเศษ)ทุกประเภท
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format(($remaining_principal_special), 2);
                            }
                            if ($data == "[loan_amount_normal]") { //เงินต้นกู้คงเหลือ(สามัญ-สามัญ)
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format(($remaining_principal_normal_nm), 2);
                            }
                            if ($data == "[loan_amount_emergency]") { //เงินต้นกู้คงเหลือ(ฉุกเฉิน-ฉุกเฉิน)
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format(($remaining_principal_emergency_em), 2);
                            }
                            if ($data == "[loan_amount_normal_life]") { //เงินต้นกู้คงเหลือ(สามัญ-เพื่อพัฒนาคุณถาพชีวิต)
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format(($remaining_principal_normal_ql), 2);
                            }
                            if ($data == "[loan_amount_balance_result]") { //รวมยอดเงินต้นกู้คงเหลือทั้งหมด
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format(($sum_remaining_principal), 2);
                            }
                            if ($data == "[credit_limit_normal]") { //วงเงินกู้ สามัญ-สามัญ
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format(($credit_normal_nm), 2);
                            }
                            if ($data == "[credit_limit_emergency]") { //วงเงินกู้ ฉุกเฉิน-ฉุกเฉิน
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format(($credit_emergency_em), 2);
                            }
                            if ($data == "[credit_limit_balance_sp]") { //วงเงินกู้ โครงการพิเศษ
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format(($credit_special), 2);
                            }
                            if ($data == "[credit_limit_normal_life]") { //วงเงินกู้ สามัญ-พัฒนาคุณภาพชีวิต
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format(($credit_normal_ql), 2);
                            }
                            if ($data == "[credit_limit_remaining]") { //วงเงินกู้คงเหลือ
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = number_format(($arr_data['credit_limit_remaining']['estimate_receive_money']), 2);
                            }
                            if($data == "[email]"){ //อีเมลผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = ($array_data['email'] ==''||$array_data['email'] ==' ')?'':$array_data['email'];
                            }
                            if ($data == "[branch_name]") { //สาขาธนาคาร
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $arr_data['bank_account']['branch_name'];
                            }
                            if ($data == "[transfer_bank_account_id]") { //หมายเลขบัญชี
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $arr_data['bank_account']['dividend_acc_num'];
                            }
                            if ($data == "[bank_account_name]") { //ชื่อบัญชี
                                $bank_account_name=$array_data['prename_short'].$array_data['firstname_th']." ".$array_data['lastname_th'];
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = ($arr_data['bank_account']['dividend_acc_num'] == '')?"":$bank_account_name;
                            }
                            if($data == "[marry_name]"){ //ชื่อภรรยาผู้กู้
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_name'];
                            }
                            for ($i = 1; $i <= 5; $i++) { //ลำดับผู้ค้ำประกัน
                                if ($data == "[guarantor_no_$i]") {
                                    $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $i <= $count_data?"$i":"";
                                }
                            }

                            for ($i = 0; $i <= 4; $i++) { //ชื่อนามสกุลผู้ค้ำประกัน
                                if ($data == "[guarantee_fullname_$i]") {
                                    $name_guarantee_[$i] = $rs_guarantee_person[$i]['prename_short'].$rs_guarantee_person[$i]['firstname_th'] . "  " . $rs_guarantee_person[$i]['lastname_th'];
                                    $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $name_guarantee_[$i];
                                }
                            }

                            for ($i = 0; $i <= 4; $i++) { // รหัสสมาชิกผู้ค้ำประกัน
                                if ($data == "[mem_guarantor_id_$i]") {
                                    $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $rs_guarantee_person[$i]['guarantee_person_id'];
                                }
                            }

                            for ($i = 0; $i <= 4; $i++) { // สังกัดผู้ค้ำประกัน
                                if ($data == "[affiliation_g_$i]") {
                                    $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $rs_guarantee_person[$i]['affiliation'];
                                }
                            }

                            for ($i = 0; $i <= 4; $i++) { // ตำแหน่งผู้ค้ำประกัน
                                if ($data == "[position_g_$i]") {
                                    $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $rs_guarantee_person[$i]['position'];
                                }
                            }

                            for ($i = 0; $i <= 5; $i++) { // ประเภทเงินฝาก
                                if ($data == "[deposit_type_$i]") {
                                    $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $arr_data['deposit_data'][$i]['type_name'];
                                }
                            }

                            for ($i = 0; $i <= 5; $i++) { // จำนวนเงินฝาก
                                if ($data == "[deposit_amount_$i]") {
                                    $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = ($arr_data['deposit_data'][$i]['transaction_balance']==0)?'':number_format($arr_data['deposit_data'][$i]['transaction_balance'],2);
                                }
                            }

                            if ($data == "[sum_deposit_amount]") { // รวมเงินฝาก
                                $sum_deposit=0;
                                foreach($arr_data['deposit_data'] AS $key =>$value){
                                    $sum_deposit += $value['transaction_balance'];
                                    $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = ($sum_deposit==0)?'':number_format($sum_deposit,2);
                                }
                            }

                            for ($i = 0; $i <= 5; $i++) { // ประเภทเงินกู้
                                if ($data == "[loan_type_$i]") {
                                    $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $arr_data['remaining_principal'][$i]['loan_type'];
                                }
                            }

                            for ($i = 0; $i <= 5; $i++) { // จำนวนเงินกู้คงเหลือ
                                if ($data == "[loan_type_amount_$i]") {
                                    $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = ($arr_data['remaining_principal'][$i]['loan_amount_balance']==0)?'':number_format($arr_data['remaining_principal'][$i]['loan_amount_balance'],2);
                                }
                            }

                            if ($data == "[sum_loan_amount]") { // รวมจำนวนเงินกู้คงเหลือ
                                $sum_loan=0;
                                foreach($arr_data['remaining_principal'] AS $key =>$value){
                                    $sum_loan += $value['loan_amount_balance'];
                                    $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = ($sum_loan==0)?'':number_format($sum_loan,2);
                                }
                            }

                            for ($i = 0; $i <= 4; $i++) { //สังกัด/ตำแหน่ง ผู้ค้ำ
                                if ($data == "[position_affiliation_guarantor_$i]") {
                                    $position[$i] = $rs_guarantee_person[$i]['position'];
                                    $affiliation[$i] = $rs_guarantee_person[$i]['affiliation'];
                                    if ($position[$i] && $affiliation[$i] != '') {
                                        $half[$i] = "/";
                                    }
                                    $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $position[$i] . $half[$i] . $affiliation[$i];
                                }
                            }

                            for ($i = 0; $i <= 4; $i++) { //เงินเดือนผู้ค้ำประกัน
                                if ($data == "[salary_guarantor_no_$i]") {
                                        $salaly_g=number_format(($rs_guarantee_person[$i]['salary']),2);
                                    if (($rs_guarantee_person[$i]['salary'] != '')&&($rs_guarantee_person[$i]['salary'] != '0')) {
                                        $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $salaly_g;
                                    }
                                    if (($rs_guarantee_person[$i]['salary'] == '')||($rs_guarantee_person[$i]['salary'] == '0')) {
                                        $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = '';
                                    }
                                }
                            }
                            if ($data == "[no]") { //เคยผิดชำระหนี้หรือไม่ (ไม่เคย)
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['detail'] = $arr_data['count_debt']=='0'?'check_mark':"";
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = '';
                            }
                            if ($data == "[yes]") { //เคยผิดชำระหนี้หรือไม่ (เคย)
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['detail'] = $arr_data['count_debt']!='0'?'check_mark':"";
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = '';
                            }

                            if ($data == "[deposit_guarantee]") { //กู้เงินโดยใช้เงินฝากเป็นหลักประกัน
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['detail'] = $array_data['loan_type']=='9'?'check_mark': "";
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = '';
                            }

                            if ($data == "[share_guarantee]") { //กู้เงินโดยใช้เงินกู้เป็นหลักประกัน
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['detail'] = $array_data['loan_type']=='10'?'check_mark': "";
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = '';
                            }

                            if ($data == "[paytype1]") { //ชำระเงินแบบคงต้น
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['detail'] = $array_data['pay_type']=='1'?'check_mark': "";
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = '';
                            }
                            if ($data == "[paytype2]") { //ชำระเงินแบบคงยอด
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['detail'] = $array_data['pay_type']=='2'?'check_mark': "";
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = '';
                            }

                            //ข้อมูลคู่สมรสผู้กู้
                            $m_fullname=$array_data['prename_full'].$array_data['firstname_th']."  ".$array_data['lastname_th'];
                            if($data == "[marry_name]"){
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_status']=='2'?$array_data['marry_name']: "";
                            }
                            if($data == "[m_address_no]"){
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_status']=='2'?$array_data['m_address_no']: "";
                            }
                            if($data == "[m_address_moo]"){
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_status']=='2'?$array_data['m_address_moo']: "";
                            }
                            if($data == "[m_address_road]"){
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_status']=='2'?$array_data['m_address_road']: "";
                            }
                            if($data == "[m_address_soi]"){
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_status']=='2'?$array_data['m_address_soi']: "";
                            }
                            if($data == "[m_province_name]"){
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_status']=='2'?$array_data['m_province']: "";
                            }
                            if($data == "[m_amphur_name]"){
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_status']=='2'?$array_data['m_amphur']: "";
                            }
                            if($data == "[m_district_name]"){
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_status']=='2'?$array_data['m_district']: "";
                            }
                            if($data == "[m_zipcode]"){
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_status']=='2'?$array_data['m_zipcode']: "";
                            }
                            if($data == "[m_tel]"){
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_status']=='2'?$array_data['m_tel']: "";
                            }
                            if($data == "[m_fullname]"){
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_status']=='2'?$m_fullname: "";
                            }
                            if($data == "[m_location]"){
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_status']=='2'?$arr_data['profile_location']['coop_name_th']: "";
                            }
                            if($data == "[m_id_card]"){
                                $arr_data['path_data'][$key1]['data_in_path'][$key2][$key3]['data_name'] = $array_data['marry_status']=='2'?$array_data['m_id_card']: "";
                            }
                        }
                    }
                }
            }
        }
        return $arr_data;
    }
}