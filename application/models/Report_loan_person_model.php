<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Report_loan_person_model extends CI_Model {
	
	public function __construct()
	{
        parent::__construct();
	}

    public function coop_report_loan_person_model($data){
		$arr_data = array();

		if($data['member_id']!=''){
			$member_id = $data['member_id'];
		}else{
			$member_id = '';
		}
		$arr_data['member_id'] = $member_id;

		$member_name = '';
		if($member_id != '') {
			$member_info = $this->db->select(array('t1.member_id', 't1.firstname_th', 't1.lastname_th', 't2.prename_full'))
									->from('coop_mem_apply as t1')
									->join("coop_prename as t2","t2.prename_id = t1.prename_id","left")
									->where("t1.member_id = '{$member_id}'")
									->get()->row();
			$member_name = $member_info->prename_full.$member_info->firstname_th." ".$member_info->lastname_th;
		}

		$arr_data['member_name'] = $member_name;
        return $arr_data;
	}

    function coop_report_loan_person_pdf_model($data){
        $arr_data = array();

		if($data['member_id']!=''){
			$member_id = $data['member_id'];
		}else{
			$member_id = '';
		}
		
		$row_profile = $this->db->select(array(
            'coop_name_th',
        ))
        ->from('coop_profile')->limit(1)
        ->get()->result_array();

        $row_loan = $this->db->select(array(
            't1.member_id',
	        't4.prename_short',
	        't1.firstname_th',
	        't1.lastname_th',
	        't1.birthday',
	        't1.apply_date',
	        't1.marry_name',
	        't1.position',
	        't6.mem_group_name AS subunits',
	        't5.mem_group_name AS affiliation',
	        't1.marry_status',
	        't1.salary',
	        't2.share_early_value',
	        't2.share_collect_value',
	        't2.share_period',
	        't2.share_bill_date',
	        't3.loan_amount',
	        't3.loan_amount_balance'
        ))
        ->from('coop_mem_apply as t1')
        ->join('coop_mem_share AS t2','t1.member_id = t2.member_id','LEFT')
        ->join('coop_loan AS t3','t1.member_id = t3.member_id','LEFT')
        ->join('coop_prename AS t4','t4.prename_id = t1.prename_id','LEFT')
        ->join('coop_mem_group AS t5','t5.id = t1.level','LEFT')
        ->join('coop_mem_group AS t6','t6.id = t1.faction','LEFT')
        ->where("t1.member_id = '{$member_id}'")
        ->order_by('t2.share_date DESC , t3.createdatetime DESC')
        ->limit(1)
        ->get()->result_array();

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

        $loan_data = $this->db->select(array(
            'member_id',
            'petition_number',
            'createdatetime',
            'date_start_period',
            'loan_amount',
            'money_per_period',
            'period_amount',
	        'loan_amount_balance',
	        'date_last_interest',
            'period_now'
        ))
        ->from('coop_loan')
        ->where("member_id = '{$member_id}' AND coop_loan.loan_status IN ('1','2')")
        ->get()->result_array();

        $arr_data['sum_loan_balance'] = 0;
        $arr_data['sum_loan_amount'] = 0;
        foreach($loan_data AS $key =>$value){
            $arr_data['sum_loan_balance'] += $value['loan_amount_balance'];
            $arr_data['sum_loan_amount'] += $value['loan_amount'];
        }

        $arr_data['member_id'] = $member_id;
        $arr_data['row_profile'] = $row_profile[0];
        $arr_data['row_loan'] = $row_loan[0];
        $arr_data['loan_data'] = $loan_data;

        return $arr_data;
	}
}