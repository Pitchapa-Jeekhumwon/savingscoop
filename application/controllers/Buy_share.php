<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buy_share extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model("Finance_libraries", "Finance_libraries");
	}
	public function index()
	{

		if($this->input->get('member_id')!=''){
			$member_id = $this->input->get('member_id');
		}else{
			$member_id = '';
		}
		$arr_data = array();
		$arr_data['member_id'] = $member_id;

		$this->db->select('*');
		$this->db->from('coop_share_setting');
		$this->db->order_by('setting_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['share_value'] = $row[0]['setting_value'];

		$arr_data['count_share'] = 0;
		$arr_data['cal_share'] = 0;

		if($member_id != '') {
			$this->db->select(array('t1.*',
							't2.mem_group_name AS department_name',
							't3.mem_group_name AS faction_name',
							't4.mem_group_name AS level_name'));
			$this->db->from('coop_mem_apply as t1');			
			$this->db->join("coop_mem_group AS t2","t1.department = t2.id","left");
			$this->db->join("coop_mem_group AS t3","t1.faction = t3.id","left");
			$this->db->join("coop_mem_group AS t4","t1.level = t4.id","left");
			$this->db->where("t1.member_id = '".$member_id."'");
			$rs = $this->db->get()->result_array();
			$row = @$rs[0];
			
			$department = "";
			$department .= @$row["department_name"];
			$department .= (@$row["faction_name"]== 'ไม่ระบุ')?"":"  ".str_replace(@$row["department_name"],"",@$row["faction_name"]);
			$department .= (@$row["level_name"]== 'ไม่ระบุ')?"":"  ".str_replace(@$row["department_name"],"",@$row["level_name"]);
			$row['mem_group_name'] = $department;
			$arr_data['row_member'] = $row;
			
			//อายุเกษียณ
			$this->db->select(array('retire_age'));
			$this->db->from('coop_profile');
			$rs_retired = $this->db->get()->result_array();
			$arr_data['retire_age'] = $rs_retired[0]['retire_age'];	
			
			//ประเภทสมาชิก
			$this->db->select('mem_type_id, mem_type_name');
			$this->db->from('coop_mem_type');
			$rs_mem_type = $this->db->get()->result_array();
			$mem_type_list = array();
			foreach($rs_mem_type AS $key=>$row_mem_type){
				$mem_type_list[$row_mem_type['mem_type_id']] = $row_mem_type['mem_type_name'];
			}
			
			$arr_data['mem_type_list'] = $mem_type_list;

			$this->db->select('*');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '" . $member_id . "' AND share_status IN('1','2')");
			$this->db->order_by("share_date DESC, share_id DESC");
			$this->db->limit(1);
			$row = $this->db->get()->result_array()[0];
			$arr_data['count_share']	= $row['share_collect'];
			$arr_data['cal_share']		= $row['share_collect_value'];

			$arr_data['count_share'] = number_format($arr_data['count_share']);
			$arr_data['cal_share'] = number_format($arr_data['cal_share']);

			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_user';
			$join_arr[$x]['condition'] = 'coop_mem_share.admin_id = coop_user.user_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_mem_share');
			$this->paginater_all->where("member_id = '".$member_id."' AND share_type = 'SPA'");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('share_date DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];


			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			$arr_data['i'] = $i;
		}else{
			$arr_data['data'] = array();
			$arr_data['paging'] = '';
		}

		//Get receipt type.
		$arr_data['receipt_same'] = $this->db->select("same_share_format")->from("coop_setting_receipt")->get()->row_array();

		//แสดงตัวเลือกชำระเงินแบบโอน
		$this->db->select(array('*'));
		$this->db->from('coop_money_type');
		$this->db->where("money_type = 'DT'");
		$this->db->order_by("id DESC");
		$row_money_type = $this->db->get()->result_array();
		foreach($row_money_type as $key_type =>$value_type){
			$cut_word=array();
			$money_type_name_th = str_replace('ฝากโดยการ',"ชำระเงินโดยการ",$value_type['money_type_name_th']);
			$cut_word['id'] = str_replace('ฝาก',"",$money_type_name_th);
			$cut_word['money_type_name_th'] = str_replace('ฝาก',"ชำระเงินโดยการ",$money_type_name_th);
			$cut_word['money_type_name_short'] = $value_type['money_type_name_short'];
			$row_money_type_get[]=$cut_word;
		}
		$arr_data['row_money_type'] = $row_money_type_get;

		$this->libraries->template('buy_share/index',$arr_data);
	}
	function save_share(){
		// echo"<pre>";print_r($this->input->post());exit;
		if($this->input->post()){
			$date = date("Y-m-d H:i:s");
			if(isset($_POST['fix_date'])){
				$tmp = explode("/", $_POST['fix_date']);
				$date = ($tmp[2]-543)."-".$tmp[1]."-".$tmp[0] . " " .date("H:i:s");
			}
			$data = $this->input->post();
			if(@$data['delete']=='1'){
				$this->db->where('share_id', @$data['share_id']);
				$this->db->delete('coop_mem_share');
				$this->center_function->toast("ลบข้อมูลเรียบร้อยแล้ว");
			}else if(@$data['cancel_receipt']=='1'){
				$this->db->select('*');
				$this->db->from("coop_mem_share");
				$this->db->where("share_id = '".@$data['share_id']."'");
				$row = $this->db->get()->result_array();

				$data_insert = array();
				$data_insert['receipt_status'] = '1';
				$data_insert['admin_id'] = $_SESSION['USER_ID'];
				$data_insert['cancel_date'] = $date;

				$this->db->where('receipt_id', $row[0]['share_bill']);
				$this->db->update('coop_receipt', $data_insert);

				$data_insert = array();
				$data_insert['share_status'] = '2';

				$this->db->where('share_id', @$data['share_id']);
				$this->db->update('coop_mem_share', $data_insert);

				echo "success";exit;
			}else{
				unset($data['fix_date']);
				unset($data['delete']);
				unset($data['share_id']);
				$data['admin_id'] = $_SESSION['USER_ID'];
				$data['share_type'] = 'SPA';
				$data['share_date'] = $date;
				$data['share_payable'] = str_replace( ',', '', $data['share_payable']);
				$data['share_payable_value'] = str_replace( ',', '', $data['share_payable_value']);
				$data['share_collect'] = $data['share_early']+str_replace( ',', '', $data['share_payable']);
				$data['share_collect_value'] = $data['share_early_value']+str_replace( ',', '', $data['share_payable_value']);
				$data['share_status'] = '1';
				$data['pay_type'] = @$data['pay_type'];
				if(@$data['pay_type'] == '1'){
					$data['pay_type_transfer_bank'] = $data['pay_type_transfer_bank'];
					$data['pay_type_transfer_bank_number'] = $data['pay_type_transfer_bank_number'];
					$data['pay_type_transfer_note'] = $data['pay_type_transfer_note'];
				}
				$this->db->insert('coop_mem_share', $data);
				$share_id = $this->db->insert_id();

				$transaction_text = $this->db->select("value")->from("coop_setting_finance")->where("name = 'receipt_share_spec_text' AND status = 1")->get()->row_array();
				//get receipt setting data
				$receipt_format = 1;
				$receipt_finance_setting = $this->db->select("*")->from("coop_setting_finance")->where("name = 'receipt_cashier_format' AND status = 1")->order_by("created_at DESC")->get()->row_array();
				if(!empty($receipt_finance_setting)) {
					$receipt_format = $receipt_finance_setting['value'];
				}
				if($receipt_format == 1) {
					$yymm = (date("Y", strtotime($date))+543).date("m", strtotime($date));

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
					$receipt_number = $this->Finance_libraries->generate_cashier_receipt_id($receipt_format, $date);
				}

				$data_insert = array();
				$data_insert['receipt_id'] = $receipt_number;
				$data_insert['member_id'] = $data['member_id'];
				$data_insert['sumcount'] = number_format($data['share_early_value'],2,'.','');
				$data_insert['admin_id'] = $_SESSION['USER_ID'];
				$data_insert['receipt_datetime'] = $date;
				$data_insert['receipt_status'] = '0';
				$data_insert['pay_type'] = $data['pay_type'];

				$this->db->insert('coop_receipt', $data_insert);

				$data_insert = array();
				$data_insert['receipt_id'] = $receipt_number;
				$data_insert['receipt_list'] = '14';
				$data_insert['receipt_count'] = number_format($data['share_early_value'],2,'.','');
				$data_insert['receipt_count_item'] = number_format($data['share_early'],2,'.','');

				$this->db->insert('coop_receipt_detail', $data_insert);

				$data_insert = array();
				$data_insert['member_id'] = $data['member_id'];
				$data_insert['receipt_id'] = $receipt_number;
				$data_insert['account_list_id'] = '14';
				$data_insert['principal_payment'] = number_format($data['share_early_value'],2,'.','');
				$data_insert['interest'] = '0';
				$data_insert['transaction_text'] = !empty($transaction_text) ? $transaction_text["value"] : 'หุ้น';
				$data_insert['deduct_type'] = 'all';
				$data_insert['total_amount'] = number_format($data['share_early_value'],2,'.','');
				$data_insert['payment_date'] = $date;
				$data_insert['loan_amount_balance'] = number_format($data["share_payable_value"],2,'.','')+number_format($data['share_early_value'],2,'.','');
				$data_insert['createdatetime'] = date('Y-m-d H:i:s');
				$this->db->insert('coop_finance_transaction', $data_insert);

				$data_insert = array();
				$data_insert['share_status'] = '1';
				$data_insert['share_bill'] = $receipt_number;
				$data_insert['share_bill_date'] = date('Y-m-d H:i:s');

				$this->db->where('share_id', $share_id);
				$this->db->update('coop_mem_share', $data_insert);

				$lastdate = self::getShareLast($data['member_id'])->share_date;
				$currentdate = date("Y-m-d", strtotime($date));
				if(date_create($currentdate) <= date_create($lastdate)){
					$target = self::getShareLast($data['member_id'], $date)->share_date;
					$this->update_st->update_share_transaction($data['member_id'], $target);
				}

				$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			}
			echo "<script> document.location.href = '".PROJECTPATH."/buy_share?member_id=".$data['member_id']."' </script>";
			//echo"<pre>";print_r($data);
			exit;
		}
	}

	function receipt_buy_share_temp(){
		$arr_data = array();

		$arr_data['member_id'] = $this->input->get('member_id');

		$this->db->select(array('t1.*','t2.mem_group_name','t3.prename_full'));
		$this->db->from("coop_mem_apply as t1");
		$this->db->join('coop_mem_group as t2','t1.level = t2.id','left');
		$this->db->join("coop_prename as t3",'t1.prename_id = t3.prename_id','left');
		$this->db->where("t1.member_id = '".$arr_data['member_id']."'");
		$row = $this->db->get()->result_array();
		//echo '<pre>'; print_r($row); echo '</pre>'; exit;
		$arr_data['prename_full'] = @$row[0]['prename_full'];
		$arr_data['name'] = $row[0]['firstname_th'].' '.$row[0]['lastname_th'];

		
		$arr_data['num_share'] = $this->input->get('num_share');
		$arr_data['value'] = $this->input->get('value');
		$arr_data['mem_group_name'] = $row[0]['mem_group_name'];
		
		//ลายเซ็นต์
		$date_signature = date('Y-m-d');
		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("start_date <= '{$date_signature}'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$arr_data['signature'] = @$row[0];
		
		
		$share_id = @$this->input->get('share_id');
		$this->db->select(array('*'));
		$this->db->from('coop_mem_share');
		$this->db->where("share_id = '{$share_id}'");
		$this->db->limit(1);
		$row_mem_share = $this->db->get()->result_array();
		$arr_data['pay_type'] = (@$row_mem_share[0]['pay_type'] == 0)?"เงินสด":"เงินโอน";
		$arr_data['receipt_id'] = @$row_mem_share[0]['share_bill'];
		$arr_data['share_date'] = @$row_mem_share[0]['share_date'];
        $arr_data['share_payable_value'] = @$row_mem_share[0]['share_payable_value'];
        $arr_data['share_collect_value'] = @$row_mem_share[0]['share_collect_value'];

        $arr_data['profile'] = $this->db->select('*')->from('coop_profile')->limit(1)->get()->row_array();

        $arr_data['setting_receipt'] = $this->db->select('*')->from('coop_setting_receipt')->get()->row_array();

        if($_GET['dev'] == 'dev'){
            echo '<pre>';print_r($arr_data);exit;
        }

		$this->load->view('buy_share/receipt_buy_share_temp',$arr_data);
	}

	function receipt_process(){
		$result = array();
		$data = $this->input->post();

		$this->db->select('*');
		$this->db->from("coop_mem_share");
		$this->db->where("share_id = '".$data['share_id']."'");
		$row = $this->db->get()->result_array();
		$mem_share = $row[0];

		if($mem_share['share_bill'] == ''){
			$transaction_text = $this->db->select("value")->from("coop_setting_finance")->where("name = 'receipt_share_spec_text' AND status = 1")->get()->row_array();
			//get receipt setting data
			$receipt_format = 1;
			$receipt_finance_setting = $this->db->select("*")->from("coop_setting_finance")->where("name = 'receipt_cashier_format' AND status = 1")->order_by("created_at DESC")->get()->row_array();
			if(!empty($receipt_finance_setting)) {
				$receipt_format = $receipt_finance_setting['value'];
			}
			if($receipt_format == 1) {
				$yymm = (date("Y", strtotime($mem_share["share_date"]))+543).date("m", strtotime($mem_share["share_date"]));

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
				$receipt_number = $this->Finance_libraries->generate_cashier_receipt_id($receipt_format, $mem_share["share_date"]);
			}

			$data_insert = array();
			$data_insert['receipt_id'] = $receipt_number;
			$data_insert['member_id'] = $mem_share['member_id'];
			$data_insert['sumcount'] = $mem_share['share_early_value'];
			$data_insert['admin_id'] = $_SESSION['USER_ID'];
			$data_insert['receipt_datetime'] = $mem_share["share_date"];
			$data_insert['receipt_status'] = '0';
			$data_insert['pay_type'] = $mem_share['pay_type'];

			$this->db->insert('coop_receipt', $data_insert);

			$data_insert = array();
			$data_insert['receipt_id'] = $receipt_number;
			$data_insert['receipt_list'] = '14';
			$data_insert['receipt_count'] = $mem_share['share_early_value'];
			$data_insert['receipt_count_item'] = $mem_share['share_early'];

			$this->db->insert('coop_receipt_detail', $data_insert);

			$data_insert = array();
			$data_insert['member_id'] = $mem_share['member_id'];
			$data_insert['receipt_id'] = $receipt_number;
			$data_insert['account_list_id'] = '14';
			$data_insert['principal_payment'] = number_format($mem_share['share_early_value'],2,'.','');
			$data_insert['interest'] = '0';
			$data_insert['transaction_text'] = !empty($transaction_text) ? $transaction_text["value"] : 'หุ้น';
			$data_insert['deduct_type'] = 'all';
			$data_insert['total_amount'] = number_format($mem_share['share_early_value'],2,'.','');
			$data_insert['payment_date'] = $mem_share["share_date"];
			$data_insert['loan_amount_balance'] = $mem_share["share_collect_value"];
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert('coop_finance_transaction', $data_insert);

			$data_insert = array();
			$data_insert['share_status'] = '1';
			$data_insert['share_bill'] = $receipt_number;
			$data_insert['share_bill_date'] = date('Y-m-d H:i:s');

			$this->db->where('share_id', $data['share_id']);
			$this->db->update('coop_mem_share', $data_insert);

			$lastdate = self::getShareLast($mem_share['member_id'])->share_date;
			$currentdate = date("Y-m-d", strtotime($mem_share["share_date"]));
			if(date_create($currentdate) <= date_create($lastdate)){
			    $target = self::getShareLast($mem_share['member_id'], $mem_share["share_date"])->share_date;
                $this->update_st->update_share_transaction($mem_share['member_id'], $target);
            }

            $process = 'buy_share';
            $money = $mem_share['share_early_value'];
            $ref = $receipt_number;
            $match_type =  'account_list';
            $match_id =  '14';
            if($mem_share['pay_type']=='0') {
                $statement = 'credit';
            }else{
                $statement = 'debit';
            }

            $data_process[] =   $this->account_transaction->set_data_account_trancetion_detail($match_id,$statement,$match_type,$ref,$money,$process);

            $process = 'buy_share';
            $money = $mem_share['share_early_value'];
            $ref = $receipt_number;
            $match_type = 'main';
            $match_id = '1';
            if($mem_share['pay_type']=='0') {
                $statement = 'debit';
            }else{
                $statement = 'credit';
            }

            $data_process[] =   $this->account_transaction->set_data_account_trancetion_detail($match_id,$statement,$match_type,$ref,$money,$process);
//            echo"<pre>";print_r($data_process);exit;

            $this->account_transaction->add_account_trancetion_detail($data_process);

		}else{
			$receipt_number = $mem_share['share_bill'];
		}
		$result['receipt_number'] = $receipt_number;
		$result['receipt_encode'] = jwt::urlsafeB64Encode($this->center_function->encrypt_text($receipt_number));
		echo json_encode($result);exit;
	}

	private function getShareLast($member_id, $date = ""){
	    if(!empty($date)){
	        $this->db->where( array("date_format(share_date, '%Y-%m-%d') <" => $date));
        }
	    $this->db->where(array("member_id" => $member_id));
	    return $this->db->order_by("date_format(share_date, '%Y-%m-%d') desc, share_id desc")
            ->select(array("date_format(share_date, '%Y-%m-%d') as share_date", "share_id",
                "share_collect_value", "share_collect"))
            ->get("coop_mem_share", 1)
            ->row();
    }

	function cancel_receipt(){
		
		if ($this->input->post()) {
		  if($this->input->post('cancel_receipt')=='1'){
				if($this->input->post('status_to')=='2'){
					$receipt_status = '2';
					$share_status = '3';
				}else{
					$receipt_status = '1';
					$share_status = '2';
				}
					
					$data_insert = array();
					$data_insert['receipt_status'] = $receipt_status;

					$this->db->where('receipt_id', $this->input->post('receipt_id'));
					$this->db->update('coop_receipt', $data_insert);
					
					$this->db->select('*');
					$this->db->from("coop_receipt_detail");
					$this->db->where("receipt_id = '".$this->input->post('receipt_id')."'");
					$row = $this->db->get()->result_array();
					
					foreach($row as $key => $value){
						if($value['receipt_list']=='14'){
							$data_insert = array();
							$data_insert['share_status'] = $share_status;

							$this->db->where('share_bill', $this->input->post('receipt_id'));
							$this->db->update('coop_mem_share', $data_insert);
						}
					}
					
				
				echo "success";exit;
			}
		}
		$arr_data = array();

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_receipt.admin_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('*');
		$this->paginater_all->main_table('coop_receipt');
		$this->paginater_all->where("receipt_status = '1' OR receipt_status = '2'");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('cancel_date DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		foreach($arr_data['data'] as $key => $value){
			$this->db->select('*');
			$this->db->from('coop_receipt_detail');
			$this->db->where("receipt_id = '".$value['receipt_id']."'");
			$row = $this->db->get()->result_array();
			$arr_data['data'][$key]['receipt_detail'] = $row;
		}
		
		
		$account_list = array();
		
		$this->db->select('*');
		$this->db->from('coop_account_list');
		$row = $this->db->get()->result_array();
		foreach($row as $key => $value){
			$account_list[$value['account_id']] = $value['account_list'];
		}
		$arr_data['account_list'] = $account_list;

		$this->libraries->template('buy_share/cancel_receipt',$arr_data);
	}
	
	function receipt_buy_share(){
		$arr_data = array();
		$this->db->select('*');
		$this->db->from("coop_receipt");
		$this->db->where("receipt_id = '".$this->input->get('receipt_id')."'");
		$row = $this->db->get()->result_array();

		$arr_data['receipt_id'] = $row[0]['receipt_id'];
		$arr_data['member_id'] = $row[0]['member_id'];

		$this->db->select(array('t1.*','t2.mem_group_name','t3.prename_full'));
		$this->db->from("coop_mem_apply as t1");
		$this->db->join('coop_mem_group as t2','t1.level = t2.id','left');
		$this->db->join("coop_prename as t3",'t1.prename_id = t3.prename_id','left');
		$this->db->where("t1.member_id = '".$arr_data['member_id']."'");
		$row = $this->db->get()->result_array();

		$arr_data['prename_full'] = @$row[0]['prename_full'];
		$arr_data['name'] = $row[0]['firstname_th'].' '.$row[0]['lastname_th'];
		$arr_data['mem_group_name'] = $row[0]['mem_group_name'];
		
		/*$this->db->select('setting_value');
		$this->db->from("coop_share_setting");
		$this->db->where("setting_id = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['num_share'] = $row[0]['setting_value'];
		*/
		
		$this->db->select(array(
			'coop_finance_transaction.*',
			'coop_loan.contract_number',
			'coop_loan.loan_amount_balance',
			'coop_loan.interest_per_year',
			'coop_loan_type.loan_type',
			'coop_account_list.account_list'));
		$this->db->from("coop_finance_transaction");
		$this->db->join('coop_loan', 'coop_finance_transaction.loan_id = coop_loan.id', 'left');
		$this->db->join('coop_loan_type', 'coop_loan.loan_type = coop_loan_type.id', 'left');
		$this->db->join('coop_account_list', 'coop_finance_transaction.account_list_id = coop_account_list.account_id', 'left');
		$this->db->where("coop_finance_transaction.receipt_id = '".$arr_data['receipt_id']."'");
		$row = $this->db->get()->result_array();
		$arr_data['value'] = $row[0]['total_amount'];	
		

		$share_bill = @$this->input->get('receipt_id');

		$this->db->select(array('*'));
		$this->db->from('coop_mem_share');
		$this->db->where("share_bill = '{$share_bill}'");
		$this->db->limit(1);
		$row_mem_share = $this->db->get()->result_array();
		$arr_data['pay_type'] = (@$row_mem_share[0]['pay_type'] == 0)?"เงินสด":"เงินโอน";
		$arr_data['share_date'] = @$row_mem_share[0]['share_date'];
		$arr_data['num_share'] = @$row_mem_share[0]['share_early'];
        $arr_data['share_collect_value'] = @$row_mem_share[0]['share_collect_value'];
		$arr_data['is_download'] = (@$this->input->get('is_download') !== null) ? 1 : null;
		//share_early

        $date_signature = date('Y-m-d');
        $this->db->select(array('*'));
        $this->db->from('coop_signature');
        $this->db->where("start_date <= '{$date_signature}'");
        $this->db->order_by('start_date DESC');
        $this->db->limit(1);
        $row = $this->db->get()->result_array();
        $arr_data['signature'] = @$row[0];

        $arr_data['profile'] = $this->db->select('*')->from('coop_profile')->limit(1)->get()->row_array();

        $arr_data['setting_receipt'] = $this->db->select('*')->from('coop_setting_receipt')->where("type = 1 AND status = 1")->order_by("id DESC")->get()->row_array();

        if($_GET['dev'] == 'dev'){
            echo '<pre>';print_r($arr_data);exit;
        }

		$this->load->view('buy_share/receipt_buy_share',$arr_data);
	}
}
