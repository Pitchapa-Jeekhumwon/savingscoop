<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Report_deposit_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_coop_mem_apply_type($array ='*')
    {
        return $this->db->select($array)->from('coop_mem_apply_type')->get()->result_array();
    }
    public function get_coop_deposit_type_setting($array ='*',$where_type_id = 0)
    {
        if($where_type_id != 0){
            $this->db->where('type_id',$where_type_id);
        }
        return $this->db->select($array)->from('coop_deposit_type_setting')->order_by("type_seq")->get()->result_array();
    }
    public function tem_acc_interest($date)
    {
        $this->db->query("DROP TEMPORARY TABLE IF EXISTS tem_acc_interest;");
        $query = "CREATE TEMPORARY TABLE tem_acc_interest SELECT t1.account_id,t1.old_acc_int,t1.transaction_time,t3.mem_id,t3.close_account_date,t3.type_id 
            FROM coop_account_transaction t1	INNER JOIN (SELECT t1.account_id, MAX( t1.transaction_id ) AS transaction_id FROM coop_account_transaction t1 INNER JOIN ( SELECT account_id, MAX( transaction_time ) AS transaction_time 
            FROM coop_account_transaction WHERE transaction_time <= '$date 23:59:59' GROUP BY account_id ) t2 ON t1.account_id = t2.account_id 
		    AND t1.transaction_time = t2.transaction_time GROUP BY	account_id ) t2 ON t1.transaction_id = t2.transaction_id INNER JOIN coop_maco_account t3 ON t1.account_id = t3.account_id WHERE
            IF	( t3.close_account_date IS NULL, DATE_ADD( t1.transaction_time, INTERVAL 1 DAY ), t3.close_account_date ) > t1.transaction_time;";
        $this->db->query($query); 
    }
    public function coop_report_conclude_interest($acc_type = 0,$mem_type = 0)
    {
       $this->db->select('t3.prename_short,t2.firstname_th,t2.lastname_th,t2.mem_type_id,t1.*')->from('tem_acc_interest t1')
       ->join('coop_mem_apply t2','t1.mem_id = t2.member_id','left')->join('coop_prename t3','t2.prename_id = t3.prename_id','left');
        if ($acc_type != 0) {
            $this->db->where('type_id', $acc_type);
        } else {}
        if ($mem_type != 0) {
            $this->db->where('mem_type', $mem_type);
        } else {}
       return $this->db->order_by('account_id asc')->get()->result_array();
    }
    public function get_account_opened_on_time($date ='',$memtype = 0 ,$acc_type = 0,$array='*')
    {
            $where_array =array();

        if($date !='' ){
            // $where_array['created'] = " => $date 00:00:00";
            $this->db->where('created <=',"$date 00:00:00");
        }

        if($memtype !=0 ||$memtype){
            $where_array['mem_type'] = $memtype;
        }
        if($acc_type !=0 ||$acc_type){
            $where_array['type_id'] = $acc_type;
        }
        $this->db->where($where_array);
        return $this->db->select($array)->from('coop_maco_account z1')->join('coop_mem_apply z2','z1.mem_id =z2.member_id ','left')
       ->where("(account_status = 0 or if(close_account_date is null,DATE_ADD('$date 00:00:00', interval 1 day),close_account_date) > '$date :23:59:59')")
        ->get()->result_array();
        
        
    }
    public function insert_coop_deposit_acc_interest_month($data)
    {
        $select_id = $this->db->select('id')->from('coop_deposit_acc_interest_month')->where('create_date',$data['create_date'])->where('account_id',$data['account_id'])->get()->result_array()[0]['id'];
        if($select_id) {
            $this->db->where('id',$select_id)->update('coop_deposit_acc_interest_month',["acc_interest"=>$data['acc_interest']]);
        }else{
          $this->db->insert('coop_deposit_acc_interest_month',$data);  
        }
        return $this->db->affected_rows();
    }
    public function coop_report_conclude_interest_monthly_preview($acc_type = 0,$mem_type = 0,$date='')
    {
        if ($acc_type != 0) {
            $this->db->where('type_id', $acc_type);
        } 
        if ($mem_type != 0) {
            $this->db->where('mem_type', $mem_type);
        } 
        if($date != ''){
            $this->db->where('create_date >=',$date." 00:00:00")->where('create_date <=',$date." 00:00:00");
        }
       return $this->db->select('t3.prename_short,t2.firstname_th,t2.lastname_th,t2.mem_type,t1.mem_id,t0.account_id,acc_interest as old_acc_int')->from('coop_deposit_acc_interest_month t0')->join('coop_maco_account t1','t0.account_id = t1.account_id','inner')
        ->join('coop_mem_apply t2','t1.mem_id = t2.member_id','left')->join('coop_prename t3','t2.prename_id = t3.prename_id','left')->order_by('account_id asc')->get()->result_array();
    }
    public function data_coop_report_transaction($get_data)
    {
        $arr_data = array();
        if(@$get_data['start_date']){
			$start_date_arr = explode('/',@$get_data['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$get_data['end_date']){
			$end_date_arr = explode('/',@$get_data['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "";
		
		if(@$get_data['transaction_lists'] && empty($get_data['transaction_list_all'])){
			$where .= " AND t1.transaction_list IN (".implode(",", $get_data['transaction_lists']).")";
		}
		
		if(@$get_data['type_id'] && @$get_data['type_id']!="all"){
			$where .= " AND t3.type_id = '".@$get_data['type_id']."'";
		}
		
        if(@$get_data['type_id'] && @$get_data['type_id'] != "all"){
            $where .= " AND coop_maco_account.type_id = '".@$get_data['type_id']."'";	
        }
        
		if(@$get_data['user_id']){
			$where .= " AND t1.user_id = '".@$get_data['user_id']."'";
		}
			
		if(@$get_data['start_date'] != '' AND @$get_data['end_date'] == ''){
			$where .= " AND t1.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$get_data['start_date'] != '' AND @$get_data['end_date'] != ''){
			$where .= " AND t1.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

        $order_by = $this->deposit_libraries->order_by_report_deposit($get_data['show_by']);
        $row_detail=$this->db->select("	t1.account_id,
									t1.transaction_list,
									t1.transaction_time,								
									t1.transaction_deposit,
									t1.transaction_withdrawal,
									t1.transaction_balance,							
									t1.member_id_atm,							
									t2.user_name,
									t3.mem_id,
									t3.account_name,
									t4.type_id,
                                    t4.type_name
												")
		->from('coop_account_transaction AS t1')
		->join("coop_user AS t2","t1.user_id = t2.user_id","left")
		->join("coop_maco_account AS t3","t1.account_id = t3.account_id","inner")
		->join("coop_deposit_type_setting AS t4","t3.type_id = t4.type_id","inner")
		->where("1=1 {$where}")
        ->order_by($order_by)
		->get()->result_array();
		if(!empty($row_detail)){
            $i=0;
            foreach($row_detail AS $key_detail=>$val_detail){  
                    $arr_data[$val_detail['type_id']][$key_detail]['account_id']=$val_detail['account_id'];
                    $arr_data[$val_detail['type_id']][$key_detail]['transaction_list']=$val_detail['transaction_list'];
                    $arr_data[$val_detail['type_id']][$key_detail]['transaction_time']=$val_detail['transaction_time'];
                    $arr_data[$val_detail['type_id']][$key_detail]['transaction_deposit']=$val_detail['transaction_deposit'];
                    $arr_data[$val_detail['type_id']][$key_detail]['transaction_withdrawal']=$val_detail['transaction_withdrawal'];
                    $arr_data[$val_detail['type_id']][$key_detail]['transaction_balance']=$val_detail['transaction_balance'];
                    $arr_data[$val_detail['type_id']][$key_detail]['member_id_atm']=$val_detail['member_id_atm'];
                    $arr_data[$val_detail['type_id']][$key_detail]['user_name']=$val_detail['user_name'];
                    $arr_data[$val_detail['type_id']][$key_detail]['mem_id']=$val_detail['mem_id'];
                    $arr_data[$val_detail['type_id']][$key_detail]['account_name']=$val_detail['account_name'];
                    $arr_data[$val_detail['type_id']][$key_detail]['type_id']=$val_detail['type_id'];
                    $arr_data[$val_detail['type_id']][$key_detail]['type_name']=$val_detail['type_name'];
                 
            }
        }
        $new_arr_data=array(); //จัดหมวดหมู่ Array ใหม่
        foreach($arr_data AS $key0=>$arr_datas){ 
            $page=1;
            $i=0;
            foreach($arr_datas AS $key=>$value){
                $i++;
                if($i>=25){
                    $page++;
                    $i=0;
                }
                if($get_data['excel']){
                    $new_arr_data[$key0][$i] = $value;
                }else{
                    $new_arr_data[$key0][$page][$i] = $value;
                }    
            } 
        }
        return $new_arr_data ;
        // echo "<pre>";print_r($new_arr_data);exit;
    }

}