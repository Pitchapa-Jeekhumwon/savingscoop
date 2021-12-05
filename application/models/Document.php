<?php

class Document extends CI_Model
{
	public function fscoop_full($loan_id)
	{

		$arr_data = array();
		$this->db->select('
		    t1.member_id,
		    t1.loan_type,
		    t1.loan_amount,
		    t1.loan_interest_amount,
		    t1.loan_amount_total,
		    t1.loan_amount_balance,
		    t1.loan_amount_total_balance,
		    t1.interest_per_year,
		    t1.money_per_period,
		    t1.period_amount,
		    t1.createdatetime,
		    t1.approve_date,
		    t1.pay_type,
		    t1.contract_number,
		    t1.petition_number,
		    t1.salary,
		    t2.salary as member_salary,
		    t1.loan_reason,
		    t1.date_period_1,
		    t1.money_period_1,
		    t1.date_period_2,
		    t1.money_period_2,
		    t1.transfer_type,
		    t1.transfer_bank_account_id,
		    t1.date_start_period,
		    t2.firstname_th,
		    t2.lastname_th,
		    t2.birthday,
		    t2.id_card,
		    t2.tel,
		    t2.c_tel,
		    t2.office_tel,
		    t2.mobile,
		    t2.email,
		    t2.marry_status,
		    t2.address_no,
		    t2.address_moo,
		    t2.address_village,
		    t2.address_road,
		    t2.address_soi,
		    t2.zipcode,
		    t2.c_address_no,
		    t2.c_address_moo,
		    t2.c_address_village,
		    t2.c_address_road,
		    t2.c_address_soi,
		    t2.c_zipcode,
		    t2.m_address_no,
		    t2.m_address_moo,
		    t2.m_address_village,
		    t2.m_address_road,
		    t2.m_address_soi,
		    t2.m_zipcode,
		    t2.marry_name,
		    t2.m_id_card,
		    t2.m_work_id_card,
		    t2.m_tel,
		    t13.prename_full,
		    t13.prename_short,
		    t14.position_name,
		    t13.*,t14.*,
            t4.district_code as district_code,
            t4.district_name as district_name,
            t5.district_code as c_district_code,
            t5.district_name as c_district_name,
            t6.district_code as m_district_code,
            t6.district_name as m_district_name,
            t7.amphur_code as amphur_code , 
            t7.amphur_name as amphur_name , 
            t8.amphur_code as c_amphur_code , 
            t8.amphur_name as c_amphur_name , 
            t9.amphur_code as m_amphur_code , 
            t9.amphur_name as m_amphur_name , 
            t10.province_code as province_code , 
            t10.province_name as province_name , 
            t11.province_code as c_province_code , 
            t11.province_name as c_province_name , 
            t12.province_code as m_province_code , 
            t12.province_name as m_province_name , 
            t15.mem_group_name as mem_group_name_level ,
            t15.mem_group_full_name as mem_group_full_name_level ,
            t15.short_mem_group_name as short_mem_group_name_level,
            t16.mem_group_name as mem_group_name_faction ,
            t16.mem_group_full_name as mem_group_full_name_faction ,
            t16.short_mem_group_name as short_mem_group_name_faction,
            t21.mem_group_name as mem_group_name_department ,
            t21.mem_group_full_name as mem_group_full_name_department,
            t21.short_mem_group_name as short_mem_group_name_department,
            t17.date_period,
            t17.principal_payment as first_principal_payment,
            t17.total_paid_per_month as first_total_paid_per_month,
            t18.date_period as last_date_period,
            t18.principal_payment as last_principal_payment,
            t18.total_paid_per_month as last_total_paid_per_month,
            t19.loan_reason,
            t20.total_paid_per_month,
            m1.total_amount_approve,
            m1.total_amount,
            m2.interest,
            m3.credit_limit,
            t22.loan_name
		');
		$this->db->from('coop_loan as t1');
		$this->db->join("coop_mem_apply as t2","t1.member_id = t2.member_id","left");
		$this->db->join("coop_district as t4","t2.district_id = t4.district_id","left");
		$this->db->join("coop_district as t5","t2.c_district_id = t5.district_id","left");
		$this->db->join("coop_district as t6","t2.m_district_id = t6.district_id","left");
		$this->db->join("coop_amphur as t7","t2.amphur_id = t7.amphur_id","left");
		$this->db->join("coop_amphur as t8","t2.c_amphur_id = t8.amphur_id","left");
		$this->db->join("coop_amphur as t9","t2.m_amphur_id = t9.amphur_id","left");
		$this->db->join("coop_province as t10","t2.province_id = t10.province_id","left");
		$this->db->join("coop_province as t11","t2.c_province_id = t11.province_id","left");
		$this->db->join("coop_province as t12","t2.m_province_id = t12.province_id","left");
		$this->db->join("coop_prename as t13","t2.prename_id = t13.prename_id","left");
		$this->db->join("coop_mem_position as t14","t2.position_id = t14.position_id","left");
		$this->db->join("coop_mem_group as t15","t2.level = t15.id","left");
		$this->db->join("coop_mem_group as t16","t2.faction = t16.id","left");
        $this->db->join("coop_mem_group as t21","t2.department = t21.id","left");
        $this->db->join("coop_loan_atm as m1","t1.member_id = m1.member_id","left");
        $this->db->join("coop_loan_period as m2","t1.member_id = m2.loan_id","left");
        $this->db->join("coop_term_of_loan as m3","t1.loan_type = m3.type_id","left");
		$this->db->join("(SELECT `loan_id`, `date_period`, `principal_payment`, `total_paid_per_month` FROM `coop_loan_period` WHERE `loan_id` = '".$loan_id."' AND `period_count` = '1' limit 1) as t17", "t17.loan_id = t1.id", "left");
		$this->db->join("(SELECT `loan_id`, `date_period`, `principal_payment`, `total_paid_per_month` FROM `coop_loan_period` WHERE `loan_id` = '".$loan_id."' ORDER BY `id` DESC LIMIT 1) as t18", "t18.loan_id = t1.id", "left");
        $this->db->join("coop_loan_reason as t19","t19.loan_reason_id = t1.loan_reason","left");
        $this->db->join("(SELECT `loan_id`, `total_paid_per_month` FROM `coop_loan_period` WHERE `loan_id` = '".$loan_id."' AND `date_count` = '31' LIMIT 1) as t20", "t20.loan_id = t1.id", "left");
        $this->db->join("coop_loan_name as t22","t1.loan_type = t22.loan_name_id","left");
		$this->db->WHERE("t1.id = '$loan_id'");
		$row = $this->db->get()->result_array();
		$level = $row[0]['level'];
		$member_id = $row[0]['member_id'];
		$this->db->select('*');
		$this->db->from('coop_mem_share');
		$this->db->WHERE("member_id = '$member_id'");
		$coop_mem_share_row = $this->db->get()->result_array();

		$this->db->select('N1.loan_id,N1.member_id,N1.guarantee_type,N1.amount,N1.price,
				N2.firstname_th,
				N2.lastname_th,
				N2.marry_status,
				N2.marry_name,
				N2.salary,
				N10.position_name,
				N2.id_card,
				N2.mobile,
				N2.c_address_no,
                N2.c_address_moo,
                N2.c_address_village,
                N2.c_address_road,
                N2.c_address_soi,
                N2.c_zipcode,
				N3.prename_full,N3.prename_short,
				N4.mem_group_name,
				N4.short_mem_group_name as short_mem_group_name_level,
				N5.mem_group_name as mem_group_name_faction,
				N6.mem_group_name as mem_group_name_department,
				N7.district_name as c_district_name,
                N8.amphur_name as c_amphur_name,
                N9.province_name as c_province_name
        ');
		$this->db->from('coop_loan_guarantee as N1');
		$this->db->join("coop_mem_apply as N2","N1.member_id = N2.member_id","left");
		$this->db->join("coop_prename as N3","N2.prename_id = N3.prename_id","left");
		$this->db->join("coop_mem_group as N4","N2.level = N4.id","left");
		$this->db->join("coop_mem_group as N5","N2.faction = N5.id","left");
        $this->db->join("coop_mem_group as N6","N2.department = N6.id","left");
        $this->db->join("coop_district as N7","N2.c_district_id = N7.district_id","left");
        $this->db->join("coop_amphur as N8","N2.c_amphur_id = N8.amphur_id","left");
        $this->db->join("coop_province as N9","N2.c_province_id = N9.province_id","left");
        $this->db->join("coop_mem_position as N10","N2.position_id = N10.position_id","left");
		$this->db->WHERE("N1.loan_id = '$loan_id'");
		$coop_loan_guarantee_row = $this->db->get()->result_array();
		foreach($coop_loan_guarantee_row as $key => $value){
			$coop_loan_guarantee_row[$key]['full_name_th'] = $coop_loan_guarantee_row[$key]['prename_full'].$coop_loan_guarantee_row[$key]['firstname_th'].' '.$coop_loan_guarantee_row[$key]['lastname_th'];
			$coop_loan_guarantee_row[$key]['short_name_th'] = $coop_loan_guarantee_row[$key]['prename_short'].$coop_loan_guarantee_row[$key]['firstname_th'].' '.$coop_loan_guarantee_row[$key]['lastname_th'];
		}

		$arr_data['full'] =  $row[0];
		$arr_data['full']['coop_loan_guarantee'] = $coop_loan_guarantee_row;
		return $arr_data['full'];
	}
    public function coop_loan_atm($loan_id)
    {

        $arr_data = array();
        $this->db->select('
        
            m5.*,
		    t2.salary as member_salary,
		    t2.firstname_th,
		    t2.lastname_th,
		    t2.birthday,
		    t2.id_card,
		    t2.tel,
		    t2.c_tel,
		    t2.office_tel,
		    t2.mobile,
		    t2.email,
		    t2.marry_status,
		    t2.address_no,
		    t2.address_moo,
		    t2.address_village,
		    t2.address_road,
		    t2.address_soi,
		    t2.zipcode,
		    t2.c_address_no,
		    t2.c_address_moo,
		    t2.c_address_village,
		    t2.c_address_road,
		    t2.c_address_soi,
		    t2.c_zipcode,
		    t2.m_address_no,
		    t2.m_address_moo,
		    t2.m_address_village,
		    t2.m_address_road,
		    t2.m_address_soi,
		    t2.m_zipcode,
		    t2.marry_name,
		    t2.m_id_card,
		    t2.m_work_id_card,
		    t2.m_tel,
		    t13.prename_full,
		    t13.prename_short,
		    t14.position_name,
		    t13.*,t14.*,
            t4.district_code as district_code,
            t4.district_name as district_name,
            t5.district_code as c_district_code,
            t5.district_name as c_district_name,
            t6.district_code as m_district_code,
            t6.district_name as m_district_name,
            t7.amphur_code as amphur_code , 
            t7.amphur_name as amphur_name , 
            t8.amphur_code as c_amphur_code , 
            t8.amphur_name as c_amphur_name , 
            t9.amphur_code as m_amphur_code , 
            t9.amphur_name as m_amphur_name , 
            t10.province_code as province_code , 
            t10.province_name as province_name , 
            t11.province_code as c_province_code , 
            t11.province_name as c_province_name , 
            t12.province_code as m_province_code , 
            t12.province_name as m_province_name , 
            t15.mem_group_name as mem_group_name_level ,
            t15.mem_group_full_name as mem_group_full_name_level ,
            t15.short_mem_group_name as short_mem_group_name_level,
            t16.mem_group_name as mem_group_name_faction ,
            t16.mem_group_full_name as mem_group_full_name_faction ,
            t16.short_mem_group_name as short_mem_group_name_faction,
            t21.mem_group_name as mem_group_name_department ,
            t21.mem_group_full_name as mem_group_full_name_department,
            t21.short_mem_group_name as short_mem_group_name_department,
            t17.date_period,
            t17.principal_payment as first_principal_payment,
            t17.total_paid_per_month as first_total_paid_per_month,
            t18.date_period as last_date_period,
            t18.principal_payment as last_principal_payment,
            t18.total_paid_per_month as last_total_paid_per_month,
            t19.loan_reason,
            t20.total_paid_per_month,
            m3.interest_rate,
            m3.credit_limit,
            m4.min_month_share,
            m4.max_period,          
            m6.loan_type,
            m6.loan_amount_balance,
            m7.loan_type as type_loan,
            m7.loan_amount_balance as ordinary_loan

    
            
		');
        $this->db->from('coop_loan_atm as m5');
        //$this->db->join("coop_loan as t1","m5.member_id = t1.member_id","left");
        $this->db->join("coop_mem_apply as t2","m5.member_id = t2.member_id","left");
        $this->db->join("coop_district as t4","t2.district_id = t4.district_id","left");
        $this->db->join("coop_district as t5","t2.c_district_id = t5.district_id","left");
        $this->db->join("coop_district as t6","t2.m_district_id = t6.district_id","left");
        $this->db->join("coop_amphur as t7","t2.amphur_id = t7.amphur_id","left");
        $this->db->join("coop_amphur as t8","t2.c_amphur_id = t8.amphur_id","left");
        $this->db->join("coop_amphur as t9","t2.m_amphur_id = t9.amphur_id","left");
        $this->db->join("coop_province as t10","t2.province_id = t10.province_id","left");
        $this->db->join("coop_province as t11","t2.c_province_id = t11.province_id","left");
        $this->db->join("coop_province as t12","t2.m_province_id = t12.province_id","left");
        $this->db->join("coop_prename as t13","t2.prename_id = t13.prename_id","left");
        $this->db->join("coop_mem_position as t14","t2.position_id = t14.position_id","left");
        $this->db->join("coop_mem_group as t15","t2.level = t15.id","left");
        $this->db->join("coop_mem_group as t16","t2.faction = t16.id","left");
        $this->db->join("coop_mem_group as t21","t2.department = t21.id","left");
        $this->db->join("coop_loan_period as m2","m5.member_id = m2.loan_id","left");
        $this->db->join("coop_loan_atm_setting as m4","m4.run_id = 1","left");
        $this->db->join("coop_loan as m6","m6.member_id = m5.member_id and m6.loan_type = 4","left");
        $this->db->order_by("m6.date_start_period","DESC");
        $this->db->limit(1);
        $this->db->join("coop_loan as m7","m7.member_id = m5.member_id and m7.loan_type = 29","left");
        $this->db->order_by("m7.date_start_period","DESC");
        $this->db->limit(1);
        //$this->db->join("(SELECT `loan_amount_balance` FROM `coop_loan` ORDER BY `date_start_period` DESC LIMIT 1) as m6", "m6.member_id = m5.member_id ", "left");
        $this->db->join("coop_term_of_loan as m3","m3.id = 9999","left"); // กำหนดค่าให้id เนื่องจากในตาราง coop_term_of_loan ไม่มีการตั้งค่าให้เชื่อมต่อกับตาราง coop_loan_atm
        $this->db->join("(SELECT `loan_id`, `date_period`, `principal_payment`, `total_paid_per_month` FROM `coop_loan_period` WHERE `loan_id` = '".$loan_id."' AND `period_count` = '1' limit 1) as t17", "t17.loan_id = m5.loan_atm_id ", "left");
        $this->db->join("(SELECT `loan_id`, `date_period`, `principal_payment`, `total_paid_per_month` FROM `coop_loan_period` WHERE `loan_id` = '".$loan_id."' ORDER BY `id` DESC LIMIT 1) as t18", "t18.loan_id = m5.loan_atm_id ", "left");
        $this->db->join("coop_loan_reason as t19","t19.loan_reason_id = m5.loan_reason","left");
        $this->db->join("(SELECT `loan_id`, `total_paid_per_month` FROM `coop_loan_period` WHERE `loan_id` = '".$loan_id."' AND `date_count` = '31' LIMIT 1) as t20", "t20.loan_id = m5.loan_atm_id ", "left");
        $this->db->WHERE("m5.loan_atm_id = '$loan_id'");
        $row = $this->db->get()->result_array();
        $level = $row[0]['level'];
        $member_id = $row[0]['member_id'];

        $this->db->select('*');
        $this->db->from('coop_mem_share');
        $this->db->WHERE("member_id = '$member_id'");
        $coop_mem_share_row = $this->db->get()->result_array();

        $this->db->select('N1.loan_id,N1.member_id,N1.guarantee_type,N1.amount,N1.price,
				N2.firstname_th,
				N2.lastname_th,
				N2.marry_status,
				N2.marry_name,
				N2.salary,
				N10.position_name,
				N2.id_card,
				N2.mobile,
				N2.c_address_no,
                N2.c_address_moo,
                N2.c_address_village,
                N2.c_address_road,
                N2.c_address_soi,
                N2.c_zipcode,
				N3.prename_full,N3.prename_short,
				N4.mem_group_name,
				N4.short_mem_group_name as short_mem_group_name_level,
				N5.mem_group_name as mem_group_name_faction,
				N6.mem_group_name as mem_group_name_department,
				N7.district_name as c_district_name,
                N8.amphur_name as c_amphur_name,
                N9.province_name as c_province_name
        ');
        $this->db->from('coop_loan_guarantee as N1');
        $this->db->join("coop_mem_apply as N2","N1.member_id = N2.member_id","left");
        $this->db->join("coop_prename as N3","N2.prename_id = N3.prename_id","left");
        $this->db->join("coop_mem_group as N4","N2.level = N4.id","left");
        $this->db->join("coop_mem_group as N5","N2.faction = N5.id","left");
        $this->db->join("coop_mem_group as N6","N2.department = N6.id","left");
        $this->db->join("coop_district as N7","N2.c_district_id = N7.district_id","left");
        $this->db->join("coop_amphur as N8","N2.c_amphur_id = N8.amphur_id","left");
        $this->db->join("coop_province as N9","N2.c_province_id = N9.province_id","left");
        $this->db->join("coop_mem_position as N10","N2.position_id = N10.position_id","left");
        $this->db->WHERE("N1.loan_id = '$loan_id'");
        $coop_loan_guarantee_row = $this->db->get()->result_array();
        foreach($coop_loan_guarantee_row as $key => $value){
            $coop_loan_guarantee_row[$key]['full_name_th'] = $coop_loan_guarantee_row[$key]['prename_full'].$coop_loan_guarantee_row[$key]['firstname_th'].' '.$coop_loan_guarantee_row[$key]['lastname_th'];
            $coop_loan_guarantee_row[$key]['short_name_th'] = $coop_loan_guarantee_row[$key]['prename_short'].$coop_loan_guarantee_row[$key]['firstname_th'].' '.$coop_loan_guarantee_row[$key]['lastname_th'];
        }

        $arr_data['full'] =  $row[0];
        $arr_data['full']['coop_loan_guarantee'] = $coop_loan_guarantee_row;
        return $arr_data['full'];
    }

	/*จำนวนหุ้น*/
	public function share_group($loan_id){
		$date = date("Y-m-d 00:00:00");
		$this->db->select("t1.share_early, t1.share_early_value, t1.share_collect, t1.share_collect_value");
		$this->db->from("coop_mem_share as t1");
		$this->db->join("coop_loan as t2","t1.member_id = t2.member_id","left");
		$this->db->where("t2.id = '$loan_id' AND `share_date` <= '$date'");
		$this->db->order_by("share_date","DESC");
		$this->db->limit(1,1);
		$share_group = $this->db->get()->row_array();
		$arr_data['share_group'] = $share_group;
		return $arr_data['share_group'];
	}
/*
 *  โปรไฟล์ชื่อสหกรณ์ ex.เขียนจาก....
 */
	public function profile_location(){
		$this->db->select('*');
		$this->db->from('coop_profile');
		$profile_location = $this->db->get()->row_array();
		$arr_data['profile_location'] = $profile_location;
		return $arr_data;
	}
/*
ระยะเวลากู้
*/
	public function loan_period($loan_id){
		$this->db->select('t1.period_count,				
						   t1.outstanding_balance,
						   t1.date_period,
						   t1.date_count,
						   t1.interest,
						   t1.principal_payment,
						   t1.total_paid_per_month,t2.id
						  '); /*งวดที่, เงินต้นคงเหลือ, วันที่ชำระ, จำนวนวันในที่ชำระนั้นๆ, ดอกเบี้ย, เงินต้นที่ชำระ, รวมชำระต่องวด (ตามลำดับ) */
		$this->db->from("coop_loan_period as t1");
		$this->db->join("coop_loan as t2","t1.loan_id = t2.id", "left");
		$this->db->where("t2.id = '$loan_id'");
		$loan_period = $this->db->get()->result_array();
		$arr_data['loan_period'] = $loan_period ;
		return $arr_data['loan_period'];
	}

    public function setting_report($code_name)
    {
        $this->db->select(array('switch_code'));
        $this->db->from('format_setting_report');
        $this->db->where("code_name = '".$code_name."'");
        $row = $this->db->get()->row_array();
        return $row['switch_code'];
    }

    /* หายอดหนี้เกินกู้คงเหลือเดิม */
    public function contract_current($loan_id, $member_id, $createdatetime)
    {
        $contract = $this->contract->current($member_id)->get();
        $data = array();
        foreach ($contract as $key => $loan) {
            if($loan['id'] != $loan_id){
                $this->db->select(array(
                    'principal_payment',
                    'total_paid_per_month'
                ));
                $this->db->from('coop_loan_period');
                $this->db->where("loan_id='".$loan['id']."' AND date_count = '31'");
                $loan_period = $this->db->get()->row_array();

                $loan['principal_payment'] = $loan_period['principal_payment'];
                $loan['total_paid_per_month'] = $loan_period['total_paid_per_month'];
                $loan['loan_type_code'] = $this->get_loan_type_code($loan['loan_type']);
                $loan['loan_reason'] = $this->get_loan_reason($loan['id']);

                $data[$key] = $loan;
            }
        }
        foreach ($data as $item) {
            $loan_transaction = $this->loan_transaction($item['id'], $createdatetime);
            $cal_loan[$item['loan_type_id']]['loan_amount_balance'] += $loan_transaction['loan_amount_balance'];
            $cal_loan[$item['loan_type_id']]['num_transaction'] += $loan_transaction['num_transaction'];
            $cal_loan[$item['loan_type_id']]['principal_payment'] += $item['principal_payment'];
        }

        $get_data['data'] = $data;
        $get_data['cal_loan'] = $cal_loan;
//        echo '<pre>';print_r($get_data);exit;
        return $get_data;
    }

    /* ค่าใช้จ่ายรายเดือน */
    public function loan_cost($loan_id, $member_id){
        $arr_data['loan_cost_code'] = array();
        $this->db->select('outgoing_name, loan_cost_amount');
        $this->db->from("coop_outgoing");
        $this->db->join("coop_loan_cost_mod", "coop_outgoing.outgoing_code=coop_loan_cost_mod.loan_cost_code", "inner");
        $this->db->where("loan_id = '".$loan_id."' AND member_id = '".$member_id."'");
        $rs_cost['data'] = $this->db->get()->result_array();
        $total_cost = 0;
        foreach ($rs_cost['data'] as $item) {
            $total_cost += $item['loan_cost_amount'];
        }
        $rs_cost['total_cost'] = $total_cost;
        return $rs_cost;
    }

    /* หาเงินฝาก */
    public function deposit($member_id){
        $this->db->select('account_id');
        $this->db->from('coop_maco_account');
        $this->db->where("mem_id = '".$member_id."' AND account_status = '0'");
        $rs_account = $this->db->get()->result_array();
        $count_account = 0;
        $cal_account = 0;
        foreach($rs_account as $key => $row_account){
            $this->db->select('*');
            $this->db->from('coop_account_transaction');
            $this->db->where("account_id = '".$row_account['account_id']."'");
            $this->db->order_by('transaction_time DESC, transaction_id DESC');
            $this->db->limit(1);
            $row_account_trans = $this->db->get()->result_array();

            $cal_account += @$row_account_trans[0]['transaction_balance'];
            $count_account++;

            $rs_account[$key]['transaction_balance'] = @$row_account_trans[0]['transaction_balance'];
        }
        $arr_data['data_account'] = $rs_account;
        $arr_data['count_account'] = $count_account;
        $arr_data['cal_account'] = $cal_account;

        return $arr_data;
    }

    public function loan_transaction($loan_id, $createdatetime){
        $this->db->select('loan_id ,loan_amount_balance');
        $this->db->from('coop_loan_transaction');
        $this->db->where("loan_id = '".$loan_id."' AND transaction_datetime < '".$createdatetime."' AND loan_type_code = 'PL'");
        $this->db->order_by('transaction_datetime','DESC');
        $get_data = $this->db->get()->row_array();

        $this->db->select('COUNT(loan_transaction_id) as num_transaction');
        $this->db->from('coop_loan_transaction');
        $this->db->where("loan_id = '".$loan_id."' AND transaction_datetime < '".$createdatetime."' AND loan_type_code = 'PL'");
        $num_transaction = $this->db->get()->row_array();
        $get_data['num_transaction'] = $num_transaction['num_transaction'];

        return $get_data;
    }

    public function coop_signature($createdatetime){
        $this->db->select('t1.finance_name, t1.receive_name, t1.manager_name, t1.createdatetime');
        $this->db->from('coop_signature as t1');
        $this->db->order_by('t1.createdatetime', 'DESC');
        $re = $this->db->get()->result_array();
        $coop_signature = array();
        foreach ($re as $item) {
            if($item['createdatetime'] <= $createdatetime){
                $coop_signature = $item;
                break;
            }
        }
        if($coop_signature == array()){
            $coop_signature = $re[count($re)-1];
        }
        return $coop_signature;
    }
	
	public function get_loan_type_code($loan_type){
        $row = $this->db->select('t2.loan_type_code')
						->from('coop_loan_name AS t1')
						->join("coop_loan_type AS t2","t1.loan_type_id = t2.id","left")
						->where("t1.loan_name_id = '{$loan_type}'")
						->get()->row_array();
        if(!empty($row)){
			$data = $row['loan_type_code'];
		}
        return $data;
    }
	
	public function get_loan_reason($loan_id){
        $row = $this->db->select('t2.loan_reason')
						->from("coop_loan AS t1")
						->join("coop_loan_reason AS t2","t1.loan_reason = t2.loan_reason_id","left")
						->where("t1.id = '{$loan_id}'")
						->get()->row_array();
        if(!empty($row)){
			$data = $row['loan_reason'];
		}
        return $data;
    }
	
	//หายอดผ่อนชำระต่องวด งวดแรก
	public function loan_period_first($loan_id){
		$data = array();
		$row = $this->db->select(array('principal_payment','total_paid_per_month'))
						->from('coop_loan_period')
						->where("loan_id = '".$loan_id."' AND period_count = '2'")
						->get()->row_array();
		if(!empty($row)){
			$data = $row;
		}
		return $data;
	}	
	//หายอดผ่อนชำระต่องวด งวดสุดท้าย
	public function loan_period_last($loan_id){
		$data = array();
		$row = $this->db->select(array('principal_payment','total_paid_per_month'))
						->from('coop_loan_period')
						->where("loan_id = '".$loan_id."'")
						->order_by("period_count DESC")
						->limit(1)
						->get()->row_array();
		//echo $this->db->last_query(); exit;				
		if(!empty($row)){
			$data = $row;
		}
		return $data;
	}	
}
