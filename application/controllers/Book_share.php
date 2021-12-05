<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Book_share extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model("Finance_libraries", "Finance_libraries");
	}
	public function index()
	{
		if(empty($_GET["page"])){
			$_GET["page"]="1";
		}
		// print_r($_GET);exit;
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
			
			//งวดที่
			$this->db->select('share_period');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2') AND share_period IS NOT NULL");
			$this->db->order_by('share_date DESC,share_id DESC');
			$this->db->limit(1);
			$row_share_month = $this->db->get()->result_array();
			$row_share_month = @$row_share_month[0];
			$arr_data['share_period'] = @$row_share_month['share_period'];

			$this->db->select('share_id');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."'");
			$transactionNum = count($this->db->get()->result_array());		
			$maxPage = $transactionNum%20 > 0 ? floor(($transactionNum/20)) + 1 : $transactionNum/20;
// echo"<pre>";print_r($maxPage);exit;
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_user';
			$join_arr[$x]['condition'] = 'coop_mem_share.admin_id = coop_user.user_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_mem_share');
			$this->paginater_all->where("member_id = '".$member_id."' ");
			$this->paginater_all->page_now($maxPage - @$_GET["page"] + 1);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('share_date ASC, share_id ASC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
		// echo"<pre>";print_r($paging);exit;
			$paging = $this->pagination_center->paginating(intval($_GET["page"]), $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		// echo"<pre>";print_r($paging);exit;
			$i = $row['page_start'];
			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			$arr_data['i'] = $i;
		}else{
			$arr_data['data'] = array();
			$arr_data['paging'] = '';
		}
	//ประเภทหุ้น
	$this->db->select('share_type_code, share_type_name');
	$this->db->from('coop_share_type');
	// $this->db->where("share_type_status ='1'");
	$rs_share_type = $this->db->get()->result_array();
	$arr_share_type = array();
	foreach($rs_share_type AS $key=>$row_share_type){
		$arr_share_type[$row_share_type['share_type_code']] = $row_share_type['share_type_name'];
	}
	$arr_data['share_type'] = @$arr_share_type;
		
		//Get receipt type.
		// $arr_data['receipt_same'] = $this->db->select("same_share_format")->from("coop_setting_receipt")->get()->row_array();
// echo "<pre>";	print_r($arr_data);exit;
		$this->libraries->template('book_share/index',$arr_data);
	}
	
	function book_member_share_pdf(){
		$arr_data = array();
		$member_id = $this->input->get('member_id');
		$arr_data['member_id'] = $member_id;
		
		$this->db->select('*');
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id ='" . $member_id . "'");
		$this->db->join('coop_prename', 'coop_prename.prename_id = coop_mem_apply.prename_id', 'left');
		$row1= $this->db->get()->result_array();
		
		$style = $this->db->get_where("coop_book_bank_style", array(
			"style_id" => 1
		))->result_array()[0];
		$arr_data['style'] = $style;

		$rows = $this->db->get_where("coop_book_bank_style_setting", array(
			"style_id" => 1
		))->result_array();
		$book_date=date('Y-m-d');
		foreach ($rows as $key => $value) {
			// var_dump($value);
			// echo "<br>";
			$meta = $value['style_value'];
			$text = $meta;
			if($meta == "[member_id]"){
				$text = $member_id;
			}
			if($meta == "[member_name]"){
				$text = $row1[0]['prename_full']." ".$row1[0]['firstname_th']." ".$row1[0]['lastname_th'];
			}
			if($meta == "[date_now]"){
				$text = $this->center_function->ConvertToThaiDate(@$book_date,false);
			}

			$rows[$key]['text'] = $text;
			
		}
		// var_dump($rows);
		// exit;
		$arr_data['rows'] = $rows;
		// echo "<pre>";	print_r($member_name);exit;
		$this->load->view('book_share/book_member_share_pdf',$arr_data);
	}
	function book_share_statment_pdf(){
		$member_id = $this->input->get('member_id');
		$arr_data['member_id'] = $member_id;
		// echo "<pre>";	print_r($_GET);exit;
		$this->db->select('*');
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id ='" . $member_id . "'");
		$this->db->join('coop_prename', 'coop_prename.prename_id = coop_mem_apply.prename_id', 'left');
		$row1= $this->db->get()->result_array();
		
		$style = $this->db->get_where("coop_book_bank_style", array(
			"style_id" => 1
		))->result_array()[0];
		$arr_data['style'] = $style;

		// $rows = $this->db->get_where("coop_book_bank_style_setting", array(
		// 	"style_id" => 1
		// ))->result_array();
		// foreach ($rows as $key => $value) {
		// 	// var_dump($value);
		// 	// echo "<br>";
		// 	$meta = $value['style_value'];
		// 	$text = $meta;
		// 	if($meta == "[member_id]"){
		// 		$text = $member_id;
		// 	}
		// 	if($meta == "[member_name]"){
		// 		$text = $row1[0]['prename_full']." ".$row1[0]['firstname_th']." ".$row1[0]['lastname_th'];
		// 	}
		// 	if($meta == "[date_now]"){
		// 		$text = date("d/m/").(date("Y")+543);
		// 	}

		// 	$rows[$key]['text'] = $text;
			
		// }
		// // var_dump($rows);
		// // exit;
		// $arr_data['rows'] = $rows;
		$this->load->view('book_share/book_share_statment_pdf',$arr_data);
	}
	function check_show_no(){
		
		$shows_no = $this->db->get_where("coop_mem_share_print", array(
            "member_id" => $this->input->post('member_id')
        ))->result_array()[0]['show_no'];
		
		if(!empty($shows_no) > 0){
			echo json_encode(array("result" => "success","show_no" =>$shows_no ));
			exit;
		}else{
			echo json_encode(["result" => "error"]);
			exit;
		}
	}
}