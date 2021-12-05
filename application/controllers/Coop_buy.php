<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coop_buy extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$arr_data = array();
		$data_get = $this->input->get();
		$arr_data['data_get'] = @$data_get;
		if(@$data_get['account_buy_id']!=''){
			
			$this->db->select('*');
			$this->db->from('coop_account_buy');
			$this->db->where("account_buy_id = '".$data_get['account_buy_id']."'");
			$row = $this->db->get()->result_array();
			$arr_data['row'] = @$row[0];
			
			$this->db->select('*');
			$this->db->from('coop_account_buy_detail');
			$this->db->where("account_buy_id = '".$data_get['account_buy_id']."'");
			$this->db->order_by('account_buy_detail_id ASC');
			$row = $this->db->get()->result_array();
			$arr_data['rs_detail'] = @$row;
		}
		
		$this->db->select(array('account_id','account_list','amount'));
		$this->db->from('coop_account_buy_list');
		$account_buy_list = $this->db->get()->result_array();
		$arr_data['account_buy_list'] = @$account_buy_list; 
		
		$this->db->select(array('account_bank_id','account_bank_name'));
		$this->db->from('coop_account_bank');
		$rs_bank = $this->db->get()->result_array();
		$arr_data['rs_bank'] = @$rs_bank; 
		
		$this->db->select(array('*'));
		$this->db->from('coop_account_buy');
		$this->db->order_by('account_buy_number DESC');
		$this->db->limit(5);
		$rs_search = $this->db->get()->result_array();
		$arr_data['rs_search'] = @$rs_search; 
		
		$this->libraries->template('coop_buy/index',$arr_data);
	}
	function save_buy(){
			$data_post = $this->input->post();
			//echo"<pre>";print_r($data_post);echo"</pre>";exit;
			$this->db->select(array('account_buy_number'));
			$this->db->from('coop_account_buy');
			$this->db->where("account_buy_number LIKE '".date("Ym")."%'");
			$this->db->order_by('account_buy_number DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			$row = @$row[0];
			
			if(@$row['account_buy_number'] != ''){
				$id = (int) substr($row["account_buy_number"], 6);
				$account_buy_number = date("Ym").sprintf("%06d", $id + 1);
			}
			else {
				$account_buy_number = date("Ym")."000001";
			}
			
			$data = $data_post['data'];
			$buy_date_arr = explode('/',$data['coop_account_buy']['buy_date']);
			$buy_date = ($buy_date_arr[2]-543)."-".$buy_date_arr[1]."-".$buy_date_arr[0];
			
			$data_insert = array();
			$data_insert['buy_date'] = $buy_date;
			$data_insert['account_buy_number'] = $account_buy_number;
			$data_insert['pay_for'] = $data['coop_account_buy']['pay_for'];
			$data_insert['pay_type'] = $data['coop_account_buy']['pay_type'];
			if($data['coop_account_buy']['pay_type'] == 'cheque'){
				$cheque_date_arr = explode('/',$data['coop_account_buy']['cheque_date']);
				$cheque_date = ($cheque_date_arr[2]-543)."-".$cheque_date_arr[1]."-".$cheque_date_arr[0];
				$data_insert['cheque_number'] = $data['coop_account_buy']['cheque_number'];
				$data_insert['cheque_date'] = $cheque_date;
			}else if($data['coop_account_buy']['pay_type'] == 'transfer'){
				$data_insert['account_bank_id'] = $data['coop_account_buy']['account_bank_id'];
			}
			$data_insert['account_buy_status'] = '0';
			
			$this->db->insert('coop_account_buy', $data_insert);
			
			$account_buy_id = $this->db->insert_id();
			
			
			$total_amount = 0;
			$i=0;
			foreach($data['coop_account_buy_detail'] as $key => $value){
				
				$data_insert = array();
				$data_insert['account_buy_id'] = $account_buy_id;
				$data_insert['account_id'] = $value['account_id'];
				$data_insert['pay_amount'] = $value['pay_amount'];
				$data_insert['pay_description'] = $value['pay_description'];
				$data_insert['bill_number'] = $value['bill_number'];
				$this->db->insert('coop_account_buy_detail', $data_insert);
				
				$total_amount += $value['pay_amount'];
				
				$this->db->select(array('account_chart_id'));
				$this->db->from('coop_account_match');
				$this->db->where("match_type = 'account_buy_list' AND match_id = '".$value['account_id']."'");
				$row = $this->db->get()->result_array();
				$row_match = $row[0];
				
				$data_account['coop_account_detail'][$i]['account_type'] = 'debit';
				$data_account['coop_account_detail'][$i]['account_amount'] = $value['pay_amount'];
				$data_account['coop_account_detail'][$i]['account_chart_id'] = $row_match['account_chart_id'];
				$i++;
			}
			if($data['coop_account_buy']['pay_type'] == 'transfer'){
				$this->db->select(array('account_chart_id'));
				$this->db->from('coop_account_bank');
				$this->db->where("account_bank_id = '".$data['coop_account_buy']['account_bank_id']."'");
				$row = $this->db->get()->result_array();
				$row_credit_id = $row[0];
				
				$credit_chart_id = $row_credit_id['account_chart_id'];
			}else{
				$credit_chart_id = '10100';
			}
			$data_account['coop_account_detail'][$i]['account_type'] = 'credit';
			$data_account['coop_account_detail'][$i]['account_amount'] = $total_amount;
			$data_account['coop_account_detail'][$i]['account_chart_id'] = $credit_chart_id;
			
			$data_account['coop_account']['account_description'] = "รายการซื้อ จ่ายให้ ".$data['coop_account_buy']['pay_for'];
			$data_account['coop_account']['account_datetime'] = $buy_date;
			
			$account_book_id = $this->account_transaction->account_process($data_account);
			
			$data_insert = array();
			$data_insert['total_amount'] = @$total_amount;
			$data_insert['account_book_id'] = @$account_book_id;
			$this->db->where('account_buy_id', @$account_buy_id);
			$this->db->update('coop_account_buy', @$data_insert);
			
			echo"<script> document.location.href='".base_url(PROJECTPATH.'/coop_buy/coop_buy_pdf?account_buy_id='.$account_buy_id)."' </script>";
			exit;
	}
	function coop_buy_pdf(){
		$arr_data = array();
		$data_get = $this->input->get();
		$this->db->select(array(
			'account_buy_id',
			'total_amount',
			'buy_date',
			'account_buy_number',
			'pay_type',
			'cheque_number',
			'cheque_date',
			'pay_for')
		);
		$this->db->from('coop_account_buy');
		$this->db->where("account_buy_id = '".$data_get['account_buy_id']."'");
		$row = $this->db->get()->result_array();
		$arr_data['row'] = @$row[0];
		
		$this->db->select(array(
			't1.account_id',
			't1.pay_amount',
			't1.pay_description',
			't1.bill_number',
			't2.account_chart_id'
			)
		);
		$this->db->from('coop_account_buy_detail as t1');
		$this->db->join('coop_account_match t2', "t1.account_id = t2.match_id AND match_type = 'account_buy_list'", 'left');
		$this->db->where("account_buy_id = '".$data_get['account_buy_id']."'");
		$row = $this->db->get()->result_array();
		$arr_data['rs_detail'] = @$row;
		
		//ลายเซ็นต์
		//$date_signature = $arr_data['row']['buy_date'];
		$date_signature = date("Y-m-d", strtotime($arr_data['row']['buy_date']));
		//echo $date_signature ;exit;
		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("start_date <= '{$date_signature}'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$arr_data['signature'] = @$row[0];
		//print_r($this->db->last_query());exit;
		$this->load->view('coop_buy/coop_buy_pdf',$arr_data);
	}
	function search_account_buy(){
			$this->db->select(array('*'));
			$this->db->from('coop_account_buy');
			$this->db->where("account_buy_number LIKE '%".$this->input->post("search")."%'
			OR pay_for LIKE '%".$this->input->post("search")."%'
			");
			$this->db->order_by('account_buy_number DESC');
			$this->db->limit(5);
			$rs = $this->db->get()->result_array();
			$output = '';
           if(!empty($rs)){  
				$i= 1; 
				foreach($rs as $key => $row){
                     $output .= '
						<tr> 
							<th scope="row">'.$row['account_buy_number'].'</th>
							<td>'.$this->center_function->ConvertToThaiDate($row['buy_date'],'1','0').'</td> 
							<td>'.$row['pay_for'].'</td> 
							<td align="right">
								<a href="?account_buy_id='.$row['account_buy_id'].'">
									<button style="padding: 2px 12px;"  id="'.$row['account_buy_id'].'" type="button" class="btn btn-info">เลือก</button>
								</a>
							</td>
						</tr>
                     ';  
               $i++; 
			   }
                echo $output;  
           }else{
                $output .= '  

                          <tr>  
                               <td align="center" colspan="4"><h1>ไม่พบผลการค้นหา!</h1></td>  
                          </tr> 
                     ';
                echo $output;  
           }  
		   exit;
	}
	function cancel_account_buy(){
		$data_get = $this->input->get();
		if($data_get['account_buy_id']!='' && $data_get['action_cancel']!=''){
			$data_insert = array();
			$data_insert['account_buy_status'] = $data_get['action_cancel'];
			$data_insert['cancel_date'] = date('Y-m-d H:i:s');
			$this->db->where('account_buy_id', $data_get['account_buy_id']);
			$this->db->update('coop_account_buy', $data_insert);
			
			echo"<script> document.location.href='".base_url(PROJECTPATH.'/coop_buy?account_buy_id='.$data_get['account_buy_id'])."' </script>";
			exit;
		}
	}
	function coop_buy_cancel(){
		$arr_data = array();
		
		if (@$this->input->get('account_buy_id') != '') {
			$data_insert = array();
			$data_insert['account_buy_status'] = $this->input->get('status_to');
			$this->db->where('account_buy_id', $this->input->get('account_buy_id'));
			$this->db->update('coop_account_buy', $data_insert);
			
			$this->db->select(array('account_book_id'));
			$this->db->from('coop_account_buy');
			$this->db->where("account_buy_id = '".$this->input->get('account_buy_id')."'");
			$row = $this->db->get()->result_array();
			
			$data_insert = array();
			$data_insert['account_status'] = '2';
			$this->db->where('account_id', $row[0]['account_book_id']);
			$this->db->update('coop_account', $data_insert);
			
			echo"<script> document.location.href='".base_url(PROJECTPATH.'/coop_buy/coop_buy_cancel')."'; </script>";
			exit;
		}
		$x=0;
		$join_arr = array();
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('*');
		$this->paginater_all->main_table('coop_account_buy');
		$this->paginater_all->where("account_buy_status IN('1','2')");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(10);
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
		
		$this->libraries->template('coop_buy/coop_buy_cancel',$arr_data);
	}
}
