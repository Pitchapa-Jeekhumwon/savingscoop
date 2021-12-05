<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	/* ************************************
		? private function section
	************************************ */
	private function _base64ToImage($base64_string, $output_file) {
		$file = fopen($output_file, "wb");

		$data = explode(',', $base64_string);

		fwrite($file, base64_decode($data[1]));
		fclose($file);

		return $output_file;
	}

	private function _member_meeting_count($member_id, $meeting_type) {
		$this->db
			->select("COUNT(coop_meeting_regis.meeting_regis_id) meeting_count")
			->from("coop_meeting_regis")
			->join("coop_meeting", "coop_meeting_regis.meeting_id  = coop_meeting.meeting_id", "inner")
			->where("coop_meeting_regis.member_id = '{$member_id}' AND coop_meeting_regis.is_delete = 0 AND coop_meeting.meeting_type = {$meeting_type} AND coop_meeting.is_delete = 0");
		return (int)$this->db->get()->row_array()["meeting_count"];
	}

	private function _get_account($member_id) {
		$this->db->select("coop_maco_account.*, coop_deposit_type_setting.type_name")
			->from("coop_maco_account")
			->join("coop_deposit_type_setting", "coop_maco_account.type_id  = coop_deposit_type_setting.type_id", "inner")
			->where("coop_maco_account.mem_id = '{$member_id}' AND coop_maco_account.account_status = '0' AND coop_deposit_type_setting.main_account = 1");
		$row_acc = $this->db->get()->row_array();
		return $row_acc;
	}

	private function _transfer_deposit($account_no, $amount, $ref_no) {
		$this->db->select(array(
			'transaction_balance',
			'transaction_no_in_balance'
		));
		$this->db->from("coop_account_transaction");
		$this->db->where("account_id = '{$account_no}'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$row_balance = $this->db->get()->row_array();
		$balance = $row_balance["transaction_balance"];

		$sum = $balance + $amount;

		$transaction_time = date('Y-m-d H:i:s');

		$data_insert = array();
		$data_insert['transaction_time'] = $transaction_time;
		$data_insert['transaction_list'] = 'LUN';
		$data_insert['transaction_withdrawal'] = '';
		$data_insert['transaction_deposit'] = $amount;
		$data_insert['transaction_balance'] = number_format($sum,2,'.','');
		$data_insert['transaction_no_in_balance'] = 0;
		$data_insert['user_id'] = $_SESSION["USER_ID"];
		$data_insert['account_id'] = $account_no;
		$data_insert['ref_no'] = $ref_no;
		$this->db->insert("coop_account_transaction", $data_insert);
	}

	private function _meeting_get($meeting_type, $get) {
		$join_arr = [];
		$join_arr[] = [
			'table' => 'coop_user',
			'condition' => 'coop_meeting.user_id = coop_user.user_id',
			'type' => 'left'
		];
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select("coop_meeting.*, coop_user.user_name");
		$this->paginater_all->main_table("coop_meeting");
		$this->paginater_all->where("is_delete = 0 AND meeting_type = {$meeting_type}");
		$this->paginater_all->page_now(@$get["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by("meeting_date DESC, meeting_id DESC");
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo $this->db->last_query();exit;
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $get);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

		foreach($row['data'] as $_key => $_row) { // จำนวนผู้เข้าร่วม
			$this->db->select("COUNT(meeting_regis_id) AS regis_count");
			$this->db->from("coop_meeting_regis");
			$this->db->where("meeting_id = '{$_row['meeting_id']}' AND is_delete = 0");
			$_row_regis = $this->db->get()->row_array();
			$row['data'][$_key]['regis_count'] = $_row_regis['regis_count'];
		}

		return [
			'row' => $row,
			'paging' => $paging
		];
	}

	function _url_exists($url){
		$headers=get_headers($url);
		return stripos($headers[0],"200 OK")?true:false;
	}
	/* ************************************
		? private function section - E N D
	************************************ */

	public function index() {
		$arr_data = array();
		$id = @$_GET['id'];

		$this->db->select("id, mem_group_name name")
			->from("coop_mem_group")
			->where("mem_group_type = 3")
			->order_by("mem_group_name ASC");
		$arr_data['mem_group'] = $this->db->get()->result_array();

		$this->db->select("mem_type_id id, mem_type_name name")
			->from("coop_mem_type")
			->where("mem_type_status = 1")
			->order_by("mem_type_name ASC");
		$arr_data['mem_type'] = $this->db->get()->result_array();

		if(!empty($id)) {
			$this->db->select("*");
			$this->db->from("coop_meeting");
			$this->db->where("meeting_id = '{$id}'");
			$row = $this->db->get()->row_array();

			$tmp = explode(" ", $row["meeting_date"]);
			$tmp = explode("-", $tmp[0]);
			$row["meeting_date"] = $tmp[2]."/".$tmp[1]."/".($tmp[0] + 543);

			$arr_data['row'] = $row;
		} else {
			$x = 0;
			$join_arr = array();
			$join_arr[$x]['table'] = "coop_user";
			$join_arr[$x]['condition'] = "coop_meeting.user_id = coop_user.user_id";
			$join_arr[$x]['type'] = "left";

			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select("coop_meeting.*, coop_user.user_name");
			$this->paginater_all->main_table("coop_meeting");
			$this->paginater_all->where("is_delete = 0 AND meeting_type = 1");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by("meeting_date DESC, meeting_id DESC");
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

			foreach($row['data'] as $_key => $_row) {
				$this->db->select("COUNT(meeting_regis_id) AS regis_count");
				$this->db->from("coop_meeting_regis");
				$this->db->where("meeting_id = '{$_row['meeting_id']}' AND is_delete = 0");
				$_row_regis = $this->db->get()->row_array();

				$row['data'][$_key]['regis_count'] = $_row_regis['regis_count'];
			}

			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}

		$this->libraries->template('meeting/index', $arr_data);
	}

	public function save() {
		$tmp = explode("/", $_POST["meeting_date"]);
		$meeting_date = ($tmp[2] - 543)."-".$tmp[1]."-".$tmp[0];

		$data_insert = array();
		$data_insert['meeting_type'] = 1;
		$data_insert['meeting_name'] = @$_POST["meeting_name"];
		$data_insert['meeting_date'] = $meeting_date;
		$data_insert['meeting_paytype'] = @$_POST["meeting_paytype"];
		$data_insert['meeting_pay'] = @$_POST["meeting_pay"];
		$data_insert['meeting_recvtype'] = @$_POST["meeting_recvtype"];
		$data_insert['meeting_status'] = @$_POST["meeting_status"];
		$data_insert['use_photo_personcard'] = (int)$_POST["use_photo_personcard"];
		$data_insert['mem_group'] = $_POST["mem_group"] ? implode(',', array_filter($_POST["mem_group"])) : '';
		$data_insert['mem_type'] = $_POST["mem_type"] ? implode(',', array_filter($_POST["mem_type"])) : '';
		$data_insert['update_time'] = date('Y-m-d H:i:s');

		$type_add = @$_POST["type_add"];
		$id_edit = @$_POST["id"];

		$table = "coop_meeting";

		if ($type_add == 'add') {
		// add
			$data_insert['user_id'] = $_SESSION["USER_ID"];
			$data_insert['create_time'] = $data_insert['update_time'];
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}
		else {
		// edit
			$data_insert['edit_user_id'] = $_SESSION["USER_ID"];
			$this->db->where('meeting_id', $id_edit);
			$this->db->update($table, $data_insert);
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}

		echo"<script> document.location.href='".PROJECTPATH."/meeting' </script>";
	}

	function delete() {
		$table = @$_POST['table'];
		$table_sub = @$_POST['table_sub'];
		$id = @$_POST['id'];
		$field = @$_POST['field'];

		if (!empty($table_sub)) {
			$this->db->where($field, $id );
			$this->db->delete($table_sub);
        }

		$this->db->where($field, $id );
		$data_insert = array();
		$data_insert['is_delete'] = 1;
		$data_insert['update_time'] = date('Y-m-d H:i:s');
		$data_insert['edit_user_id'] = $_SESSION["USER_ID"];

		$this->db->update($table, $data_insert);
		//$this->db->delete($table);
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
	}

	function gettime() {
		echo date("D M d Y H:i:s O");
		exit;
	}

	function get_member() {
		$id_card = $_POST["id_card"];
		$member_id = $_POST["member_id"];
		$type = (int)$_POST["type"];
		if($type == 3) {
			$id_card = '';
			$member_id = sprintf('%06d', $_POST["id_card"]);
		}
		$this->db->select("coop_mem_apply.*, coop_district.district_name, coop_amphur.amphur_name, coop_province.province_name");
		$this->db->from("coop_mem_apply");
		$this->db->join("coop_district", "coop_district.district_id = coop_mem_apply.district_id", "left");
		$this->db->join("coop_amphur", "coop_amphur.amphur_id = coop_mem_apply.amphur_id", "left");
		$this->db->join("coop_province", "coop_province.province_id = coop_mem_apply.province_id", "left");
		$this->db->where($_POST["type"] == 1 ? "coop_mem_apply.id_card = '{$id_card}'" : "coop_mem_apply.member_id = '{$member_id}'");
		$row = $this->db->get()->row_array();
		$member_id = $row["member_id"];
		// ? Age *******************************************************************************
		$d1 = new DateTime();
		$d2 = new DateTime($row["birthday"]);
		$interval = $d1->diff($d2);
		$age = $interval->format("%y");

		// ? Address *******************************************************************************
		$address = $row["address_no"];
		$address .= empty($row["address_moo"]) ? "" : " ".trim($row["address_moo"]);
		$address .= empty($row["address_village"]) ? "" : " ".trim($row["address_village"]);
		$address .= empty($row["address_road"]) ? "" : " ".trim($row["address_road"]);
		$address .= empty($row["address_soi"]) ? "" : " ".trim($row["address_soi"]);
		$address .= empty($row["district_name"]) ? "" : " ".trim($row["district_name"]);
		$address .= empty($row["amphur_name"]) ? "" : " ".trim($row["amphur_name"]);
		$address .= empty($row["province_name"]) ? "" : " ".trim($row["province_name"]);
		$address .= empty($row["zipcode"]) ? "" : " ".trim($row["zipcode"]);

		$member_pic = empty($row["member_pic"]) ? "/assets/uploads/members/default.png" : "/assets/uploads/members/".$row["member_pic"];

		// ? สังกัด *******************************************************************************
		$level = (int)$row["level"];
		$this->db
					->select("mem_group_full_name")
					->from("coop_mem_group")
					->where("id = {$level}");
		$group_name = $this->db->get()->row_array()["mem_group_full_name"];

		// ? ครั้งที่เข้าร่วม *******************************************************************************
		$meeting_type = (int)$_POST['meeting_type'];
		$this->db
					->select("COUNT(coop_meeting_regis.meeting_regis_id) meeting_total")
					->from("coop_meeting")
					->join("coop_meeting_regis", "coop_meeting.meeting_id = coop_meeting_regis.meeting_id", "inner")
					->where("coop_meeting_regis.member_id = '{$member_id}' AND coop_meeting_regis.is_delete = 0 AND coop_meeting.meeting_type = {$meeting_type}");
		$meeting_total = (int)$this->db->get()->row_array()["meeting_total"];

		echo json_encode([
			"id_card" => $row["id_card"],
			"member_id" => $row["member_id"],
			"fullname" => $row["firstname_th"]." ".$row["lastname_th"],
			"address" => $address,
			"birthday" => $this->center_function->mydate2date($row["birthday"]),
			"age" => $age,
			"member_pic" => $member_pic,
			"tel" => $row['tel'],
			"group_name" => $group_name,
			"meeting_total" => $meeting_total,
			"reg_recruit" => $row['reg_recruit'],
		]);
		exit;
	}

	public function register() {
		$arr_data = array();
		$id = @$_GET['id'];

		if(!empty($id)) {
			$this->db->select("*");
			$this->db->from("coop_meeting");
			$this->db->where("meeting_id = '{$id}'");
			$row = $this->db->get()->row_array();

			$arr_data['id'] = $id;
			$arr_data['row'] = $row;

			$this->preview_libraries->template_preview('meeting/register', $arr_data);
		}
	}

	public function register_save() {
		$msg = "";

		$this->db->select("*");
		$this->db->from("coop_meeting");
		$this->db->where("meeting_id = '{$_POST["id"]}'");
		$row_meeting = $this->db->get()->row_array();
		$member_id = $_POST['member_id'];
		$row_acc = $this->_get_account($member_id);

		if($row_meeting["meeting_status"] == 1) {
			$status = "-2";
		}
		/* elseif(empty($row_acc)) {
			$status = "-1";
		} */
		else {
			$data_insert = array();
			$data_insert['meeting_id'] = @$_POST["id"];
			$data_insert['member_id'] = @$_POST["member_id"];
			$data_insert['id_card'] = @$_POST["id_card"];
			$data_insert['facescan_id'] = (int)@$_POST["facescan_id"];
			$data_insert['mach_id'] = (int)@$_POST["mach_id"];
			$data_insert['create_time'] = empty($_POST["create_time"]) ? date('Y-m-d H:i:s') : $_POST["create_time"];
			$data_insert['user_id'] = empty($_POST["user_id"]) ? $_SESSION["USER_ID"] : $_POST["user_id"];
			$data_insert['ip'] = $_SERVER["REMOTE_ADDR"];

			$data_insert['id_card_data'] = "";
			if($_POST["type"] == 1) {
				$id_card_data = [
					"fullname" => $_POST["fullname"],
					"address" => $_POST["address"],
					"birthday" => $_POST["birthday"],
					"age" => $_POST["age"]
				];
				$data_insert['id_card_data'] = json_encode($id_card_data);
				if( $row_meeting['use_photo_personcard'] ) {
					$base64_string = $_POST["id_card_img64"];
					if(strlen($base64_string) > 500) {
						$filename = $data_insert['id_card'].".png";
						$output_file = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/members/".$filename;
						$this->_base64ToImage($base64_string, $output_file);

						$_data = array();
						$_data['member_pic'] = $filename;
						$this->db->where("member_id", $data_insert['member_id']);
						$this->db->update("coop_mem_apply", $_data);
					}
				}
			}

			/* Update tel @ coop_mem_apply */
			if(!empty($_POST["tel"])) {
				$data_update = array();
				$data_update['tel'] = $_POST["tel"];
				$data_update['update_time'] = date('Y-m-d H:i:s');
				$data_update['update_ip'] = $_SERVER["REMOTE_ADDR"];
				$this->db->where("member_id", $data_insert['member_id']);
				$this->db->update("coop_mem_apply", $data_update);
			}

			$this->db->select("*")
							->from("coop_mem_apply")
							->where("member_id = '{$data_insert['member_id']}'");
			$member_info = $this->db->get()->row_array();

			$this->db->select("*")
							->from("coop_mem_group")
							->where("mem_group_id = '{$member_info['mem_group_id']}'");
			$coop_mem_group = $this->db->get()->row_array();
			$member_info['mem_group'] = $coop_mem_group['id'];

			$err_no = 0;
			$mem_group = explode(',', $row_meeting['mem_group']);
			$mem_type = explode(',', $row_meeting['mem_type']);
			if(!empty($row_meeting['mem_group']) && !in_array($member_info['mem_group'], $mem_group)) {
				$err_no = 1;
			} elseif(!empty($row_meeting['mem_type']) && !in_array($member_info['mem_type_id'], $mem_type)) {
				$err_no = 2;
			}

			$this->db->select("*");
			$this->db->from("coop_meeting_regis");
			$this->db->where("meeting_id = '{$data_insert['meeting_id']}' AND member_id = '{$data_insert['member_id']}' AND is_delete = 0");
			$_row = $this->db->get()->row_array();

			if( $err_no == 1 ) {
				$status = 0;
				$msg = sprintf('ไม่มีสิทธิ์ลงทะเบียน เนื่องจากลงได้เฉพาะกลุ่มที่กำหนด');
			} elseif( $err_no == 2 ) {
				$status = 0;
				$msg = sprintf('ไม่มีสิทธิ์ลงทะเบียน เนื่องจากลงได้เฉพาะสมาชิกประเภทที่กำหนด');
			} elseif(!empty($_row)) {
				$status = 0;
				$login_time = $this->center_function->ConvertToThaiDate($_row['create_time']);
				$login_type = '';
				if( $_row['facescan_id'] ) $login_type = 'ใบหน้า';
				elseif( $_row['id_card_data'] ) $login_type = 'บัตรประชาชน';
				else $login_type = 'เลขสมาชิก';
				$fullname = "{$member_info['firstname_th']} {$member_info['lastname_th']}";
				$msg = sprintf("สมาชิกรหัส %s ชื่อสกุล %s\r\nลงทะเบียนแล้วเมื่อ %s (%s)", $member_id, $fullname, $login_time, $login_type);
			} else {
				$table = "coop_meeting_regis";
				$this->db->insert($table, $data_insert);
				$meeting_regis_id = $this->db->insert_id();

				if($row_meeting["meeting_paytype"] == 1) {
					$row_acc = $this->_get_account($member_id);
					if(!empty($row_acc)) {
						$this->db->select(array(
							'transaction_balance',
							'transaction_no_in_balance'
						));
						$this->db->from("coop_account_transaction");
						$this->db->where("account_id = '".$row_acc["account_id"]."'");
						$this->db->order_by('transaction_time DESC, transaction_id DESC');
						$this->db->limit(1);
						$row_balance = $this->db->get()->row_array();
						$balance = $row_balance["transaction_balance"];

						$sum = $balance + $row_meeting["meeting_pay"];

						$transaction_time = date('Y-m-d H:i:s');

						$data_insert = array();
						$data_insert['transaction_time'] = $transaction_time;
						$data_insert['transaction_list'] = 'LUN';
						$data_insert['transaction_withdrawal'] = '';
						$data_insert['transaction_deposit'] = $row_meeting["meeting_pay"];
						$data_insert['transaction_balance'] = number_format($sum,2,'.','');
						$data_insert['transaction_no_in_balance'] = 0;
						$data_insert['user_id'] = $_SESSION["USER_ID"];
						$data_insert['account_id'] = $row_acc["account_id"];
						$data_insert['ref_no'] = $meeting_regis_id;
						$this->db->insert("coop_account_transaction", $data_insert);

						$data_insert = array();
						$data_insert['transfer_account_id'] = $row_acc["account_id"];
						$data_insert['transfer_amt'] = $row_meeting["meeting_pay"];
						$data_insert['transfer_time'] = $transaction_time;
						$data_insert['user_id'] = $_SESSION["USER_ID"];
						$data_insert['ip'] = $_SERVER["REMOTE_ADDR"];
						$this->db->where("meeting_regis_id", $meeting_regis_id);
						$this->db->update("coop_meeting_regis", $data_insert);

						$msg = "ระบบทำการโอนเงินไปยังสมาชิกรหัส {$_POST['member_id']}\nชื่อบัญชี {$row_acc["account_name"]} เลขที่ {$row_acc["account_id"]}\nจำนวน ".number_format($row_meeting["meeting_pay"])." บาท เรียบร้อยแล้ว";
					}
				}

				$status = "1";
			}
		}

		echo json_encode([
			"err_no" => $err_no,
			"status" => $status,
			"msg" => $msg,
			'login_time' => $login_time,
			'login_type' => $login_type
		]);
		exit;
	}

	public function transfer() {
		$this->db->select("*");
		$this->db->from("coop_meeting_regis");
		$this->db->where("meeting_id = '{$_POST['id']}' AND member_id = '{$_POST['member_id']}' AND is_delete = 0");
		$row_regis = $this->db->get()->row_array();
		if(!empty($row_regis)) {
			$this->db->select("*");
			$this->db->from("coop_meeting");
			$this->db->where("meeting_id = '{$_POST['id']}'");
			$row_meeting = $this->db->get()->row_array();
			if($row_meeting["meeting_paytype"] == 1) {
				$row_acc = $this->_get_account($_POST['member_id']);
				if(!empty($row_acc)) {
					if(empty($row_regis["transfer_account_id"])) {
						$this->db->select(array(
							'transaction_balance',
							'transaction_no_in_balance'
						));
						$this->db->from("coop_account_transaction");
						$this->db->where("account_id = '".$row_acc["account_id"]."'");
						$this->db->order_by('transaction_time DESC, transaction_id DESC');
						$this->db->limit(1);
						$row_balance = $this->db->get()->row_array();
						$balance = $row_balance["transaction_balance"];

						$sum = $balance + $row_meeting["meeting_pay"];

						$transaction_time = date('Y-m-d H:i:s');

						$data_insert = array();
						$data_insert['transaction_time'] = $transaction_time;
						$data_insert['transaction_list'] = 'LUN';
						$data_insert['transaction_withdrawal'] = '';
						$data_insert['transaction_deposit'] = $row_meeting["meeting_pay"];
						$data_insert['transaction_balance'] = number_format($sum,2,'.','');
						$data_insert['transaction_no_in_balance'] = 0;
						$data_insert['user_id'] = $_SESSION["USER_ID"];
						$data_insert['account_id'] = $row_acc["account_id"];
						$data_insert['ref_no'] = $row_regis["meeting_regis_id"];
						$this->db->insert("coop_account_transaction", $data_insert);

						$data_insert = array();
						$data_insert['transfer_account_id'] = $row_acc["account_id"];
						$data_insert['transfer_amt'] = $row_meeting["meeting_pay"];
						$data_insert['transfer_time'] = $transaction_time;
						$data_insert['user_id'] = $_SESSION["USER_ID"];
						$data_insert['ip'] = $_SERVER["REMOTE_ADDR"];
						$this->db->where("meeting_regis_id", $row_regis["meeting_regis_id"]);
						$this->db->update("coop_meeting_regis", $data_insert);
					}

					$msg = "ระบบทำการโอนเงินไปยังสมาชิกรหัส {$_POST['member_id']}\nชื่อบัญชี {$row_acc["account_name"]} เลขที่ {$row_acc["account_id"]}\nจำนวน ".number_format($row_meeting["meeting_pay"])." บาท เรียบร้อยแล้ว";
				}
			}
		}

		echo json_encode([
			"status" => "1",
			"msg" => $msg
		]);
		exit;
	}

	public function report_register() {
		$arr_data = array();

		$this->db->select("*");
		$this->db->from("coop_meeting");
		$this->db->where("meeting_id = '{$_GET['id']}'");
		$row = $this->db->get()->row_array();
		$arr_data['row_meeting'] = $row;

		$where = "coop_meeting_regis.meeting_id = '{$_GET["id"]}' AND coop_meeting_regis.is_delete = 0";

		$this->db->select("coop_meeting_regis.*, coop_meeting.meeting_pay, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th");
		$this->db->from("coop_meeting_regis");
		$this->db->join("coop_meeting", "coop_meeting_regis.meeting_id = coop_meeting.meeting_id", "left");
		$this->db->join("coop_mem_apply", "coop_meeting_regis.member_id = coop_mem_apply.member_id", "left");
		$this->db->where("{$where}");
		$this->db->order_by("coop_meeting_regis.create_time");
		$rs = $this->db->get()->result_array();

		foreach($rs as $k => $v) {
			$this->db->select("COUNT(coop_meeting_regis.meeting_regis_id) register_num");
			$this->db->from("coop_meeting_regis");
			$this->db->join("coop_meeting", "coop_meeting_regis.meeting_id = coop_meeting.meeting_id", "inner");
			$this->db->where("coop_meeting.is_delete = 0 AND coop_meeting_regis.member_id = '{$v["member_id"]}' AND coop_meeting_regis.is_delete = 0");
			$register_num = $this->db->get()->row_array()["register_num"];
			$rs[$k]["register_num"] = $register_num;
		}

		$arr_data['data'] = $rs;

		if(@$_GET['type'] == 'excel'){
			$this->load->view('meeting/report_register_excel',$arr_data);
		}
		elseif(@$_GET['type'] == 'pdf'){
			$this->load->view('meeting/report_register_pdf',$arr_data);
		}
		else{
			$this->preview_libraries->template_preview('meeting/report_register',$arr_data);
		}
	}

	public function register_form() {
		$arr_data = array();

		$this->db->select("*");
		$this->db->from("coop_meeting");
		$this->db->where("meeting_id = '{$_GET['id']}'");
		$row = $this->db->get()->row_array();
		$arr_data['row_meeting'] = $row;
		$mem_type_id = (int)$_GET["type"];


		$this->db->select("coop_mem_apply.member_id, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_mem_apply.tel, coop_mem_group.mem_group_full_name mem_group");
		$this->db->from("coop_mem_apply");
		$this->db->join("coop_mem_group", "coop_mem_apply.level = coop_mem_group.id", "left");
		if( $mem_type_id ) {
			$this->db->where("coop_mem_apply.mem_type_id = {$mem_type_id}");
		}
		$this->db->order_by("coop_mem_apply.firstname_th ASC, coop_mem_apply.lastname_th ASC");
		$rs = $this->db->get()->result_array();



		$arr_data['data'] = $rs;

		if(@$_GET['type'] == 'pdf'){
			$this->load->view('meeting/register_form_pdf',$arr_data);
		} else{
			$this->preview_libraries->template_preview('meeting/register_form',$arr_data);
		}
	}

	public function register_graph() {
		$arr_data = [];

		$meeting_id = (int)$_GET['id'];
		$this->db->select("*");
		$this->db->from("coop_meeting");
		$this->db->where("meeting_id = '{$meeting_id}'");
		$row = $this->db->get()->row_array();
		$arr_data['meeting_info'] = $row;

		$this->db->select('coop_mem_group.id, coop_mem_group.mem_group_name name, COUNT(coop_meeting_regis.member_id) num_register')
		->from('coop_mem_group')
		->join("coop_mem_apply", "coop_mem_group.id = coop_mem_apply.level", "left")
		->join("coop_meeting_regis", "coop_mem_apply.member_id = coop_meeting_regis.member_id", "left")
		->where('mem_group_type = 3')
		->group_by('coop_mem_group.id, coop_mem_group.mem_group_name')
		->order_by('COUNT(coop_meeting_regis.member_id) DESC, mem_group_name ASC');
		$arr_data['group_info'] = $this->db->get()->result_array();

		$this->preview_libraries->template_preview('meeting/register_graph',$arr_data);
	}

	function register_graph_info() {
    $json['data'] = [];
		$meeting_id = (int)$_POST["id"];
		$this->db->select('coop_mem_apply.level, COUNT(coop_meeting_regis.member_id) num_register')
		->from('coop_meeting_regis')
		->join("coop_mem_apply", "coop_meeting_regis.member_id = coop_mem_apply.member_id", "inner")
		->where("coop_meeting_regis.meeting_id = {$meeting_id} AND coop_meeting_regis.is_delete = 0")
		->group_by('coop_mem_apply.level');
		$data = $this->db->get()->result_array();
		foreach($data as $k => $row) {
			$json['data'][$row['level']] = (int)$row['num_register'];
		}


    $json['meeting_id'] = $meeting_id;
    $json['total'] = array_sum( $json['data'] );
		echo json_encode($json);
		exit();
	}
		/* ************************************
		? รางวัล section
	************************************ */
	function reward_table() {
		$json = [
			'err_code' => "",
			'msg' => ''
		];
		$meeting_id = (int)$_POST['meeting_id'];

		$x = 0;
		$join_arr = [];
		$join_arr[] = [
			'table' => 'coop_mem_apply',
			'condition' => 'coop_meeting_award.member_id = coop_mem_apply.member_id',
			'type' => 'inner'
		];

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select("coop_meeting_award.member_id, coop_meeting_award.award_status, coop_meeting_award.account_no, coop_meeting_award.transfer_time, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th");
		$this->paginater_all->main_table("coop_meeting_award");
		$this->paginater_all->where("is_delete = 0 AND meeting_id = {$meeting_id}");
		$this->paginater_all->page_now((int)$_POST["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by("create_time ASC");
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();

		foreach($row['data'] as $k => $v) {
			$row['data'][$k]['award_status'] = (int)$v['award_status'];
			$row['data'][$k]['transfer_time_text'] = $this->center_function->ConvertToThaiDate($v['transfer_time']);
		}
		//echo $this->db->last_query();exit;
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_POST);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$json['data'] = $row['data'];
		$json['paging'] = $paging;
		echo json_encode($json);
		exit;
	}

	function reward_add() {
		$json = [
			'err_code' => "",
			'msg' => ''
		];
		$meeting_id = (int)$_POST['meeting_id'];
		$member_id = $_POST['member_id'];
		$award_amount = (double)$_POST['award_amount'];

		$row_acc = $this->_get_account($member_id);

		$this->db->select("member_id")
			->from("coop_meeting_award")
			->where("meeting_id = {$meeting_id} AND member_id = {$member_id} AND is_delete = 0");
		$row_award = $this->db->get()->row_array();
		if( !empty($row_award) ) {
			$json['err_code'] = 'ERR001';
			$json['msg'] = 'ได้ทำการเพิ่มชื่อไปแล้ว';
		} else {
			/*
			if(empty($row_acc)) {
				$json['err_code'] = 'ERR002';
				$json['msg'] = 'เกิดข้อผิดพลาด เนื่องจากไม่มีบัญชีเงินฝาก';
			} else {
				$data_insert = array();
				$data_insert['meeting_id'] = $meeting_id;
				$data_insert['member_id'] = $member_id;
				$data_insert['award_amount'] = $award_amount;
				$data_insert['account_no'] = trim($row_acc['account_id']);
				$this->db->insert('coop_meeting_award', $data_insert);
				$award_id = $this->db->insert_id();
			}
			*/
			if(!empty($row_acc)) {
				$data_insert = array();
				$data_insert['meeting_id'] = $meeting_id;
				$data_insert['member_id'] = $member_id;
				$data_insert['award_amount'] = $award_amount;
				$data_insert['account_no'] = trim($row_acc['account_id']);
				$this->db->insert('coop_meeting_award', $data_insert);
				$award_id = $this->db->insert_id();
			}
		}

		$json['meeting_id'] = $meeting_id;
		$json['member_id'] = $member_id;
		$json['award_id'] = (int)$award_id;
		echo json_encode($json);
		exit;
	}

	function reward_transfer() {
		$json = [
			'err_code' => "",
			'msg' => ''
		];
		$meeting_id = (int)$_POST['meeting_id'];

		$this->db->select("award_id, member_id, account_no, award_amount")
			->from("coop_meeting_award")
			->where("meeting_id = {$meeting_id} AND is_delete = 0 AND award_status = 0");
		$rs = $this->db->get()->result_array();
		$json['rs'] = $rs;
		foreach( $rs as $k => $row) {
			$member_id = $row['member_id'];
			$row_acc = $this->_get_account($member_id);
			if(empty($row_acc)) { //กรณีไม่พบบัญชีเงินฝาก
				$data_update = [];
				$data_update['account_no'] = '';
				$data_update['award_status'] = 2;
				$data_update['update_time'] = date('Y-m-d H:i:s');
				$data_update['update_ip'] = $_SERVER["REMOTE_ADDR"];
				$this->db->where("award_id", $row['award_id']);
				$this->db->update("coop_meeting_award", $data_update);
			} else {
				$this->_transfer_deposit(trim($row_acc['account_id']), $row['award_amount'], $row['award_id']);
				$data_update = [];
				$data_update['account_no'] = trim($row_acc['account_id']);
				$data_update['award_status'] = 1;
				$data_update['transfer_amount'] = $row['award_amount'];
				$data_update['transfer_time'] = date('Y-m-d H:i:s');
				$data_update['update_time'] = date('Y-m-d H:i:s');
				$data_update['update_ip'] = $_SERVER["REMOTE_ADDR"];
				$this->db->where("award_id", $row['award_id']);
				$this->db->update("coop_meeting_award", $data_update);
			}
		}
		$json['meeting_id'] = $meeting_id;
		echo json_encode($json);
		exit;
	}

	public function report_reward() {
		$arr_data = array();

		$this->db->select("*");
		$this->db->from("coop_meeting");
		$this->db->where("meeting_id = '{$_GET['id']}'");
		$row = $this->db->get()->row_array();
		$arr_data['row_meeting'] = $row;

		$where = "coop_meeting_award.meeting_id = '{$_GET["id"]}'";

		$this->db->select("coop_meeting_award.*, coop_meeting.meeting_pay, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th");
		$this->db->from("coop_meeting_award");
		$this->db->join("coop_meeting", "coop_meeting_award.meeting_id = coop_meeting.meeting_id", "left");
		$this->db->join("coop_mem_apply", "coop_meeting_award.member_id = coop_mem_apply.member_id", "left");
		$this->db->where("{$where}");
		$this->db->order_by("coop_meeting_award.create_time");
		$rs = $this->db->get()->result_array();

		$arr_data['data'] = $rs;

		if(@$_GET['type'] == 'excel'){
			$this->load->view('meeting/report_reward_excel',$arr_data);
		}
		elseif(@$_GET['type'] == 'pdf'){
			$this->load->view('meeting/report_reward_pdf',$arr_data);
		}
		else{
			$this->preview_libraries->template_preview('meeting/report_reward',$arr_data);
		}
	}

	public function register_facescan() {
		$arr_data = array();
		$id = @$_GET['id'];

		if(!empty($id)) {
			$_res = $this->center_function->post_url(WEB_BASE_URL."/APIs/facescan.get.machine.php", []);
			$_json = json_decode($_res, true);
			$machines = $_json["data"];
			$arr_data['machines'] = $machines;
			
			$this->db->select("*");
			$this->db->from("coop_meeting");
			$this->db->where("meeting_id = '{$id}'");
			$row = $this->db->get()->row_array();

			$arr_data['id'] = $id;
			$arr_data['row'] = $row;

			$this->preview_libraries->template_preview('meeting/register_facescan3', $arr_data);
		}
	}

	function get_facescan() {
		$id = $_POST["id"];

		$this->db->select("member_id, create_time");
		$this->db->from("coop_meeting_regis");
		$this->db->where("meeting_id = '{$id}' AND facescan_id <> 0 AND is_delete = 0");
		$this->db->order_by("create_time DESC, meeting_regis_id DESC");
		$this->db->limit(1);
		$row = $this->db->get()->row_array();

		echo json_encode([
			"member_id" => $row["member_id"] == null ? "" : $row["member_id"],
			"time" => date("D M d Y H:i:s O", strtotime($row["create_time"]))
		]);
		exit;
	}

	function get_facescan2() {
		$id = $_POST["id"];
		$start = (int)$_POST["start"];
		$length = (int)$_POST["length"];

		$where = "coop_meeting_regis.meeting_id = '{$id}' AND coop_meeting_regis.facescan_id <> 0 AND coop_meeting_regis.is_delete = 0";

		$this->db->select("COUNT(coop_meeting_regis.meeting_regis_id) AS c");
		$this->db->from("coop_meeting_regis");
		$this->db->join("coop_mem_apply", "coop_meeting_regis.member_id = coop_mem_apply.member_id", "inner");
		$this->db->where($where);
		$_row_count = $this->db->get()->row_array();
		$count = $_row_count["c"];

		$this->db->select("coop_meeting_regis.*, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_mem_apply.member_pic");
		$this->db->from("coop_meeting_regis");
		$this->db->join("coop_mem_apply", "coop_meeting_regis.member_id = coop_mem_apply.member_id", "inner");
		$this->db->where($where);
		$this->db->order_by("coop_meeting_regis.create_time, coop_meeting_regis.meeting_regis_id");
		$this->db->limit($length, $start);
		$_rs = $this->db->get()->result_array();
		$data = [];
		foreach($_rs as $key => $_row) {
			$_file = WEB_BASE_URL."/uploads/facescan/".((int)$_row["member_id"]).".jpg";
			$member_pic = empty($_row["member_pic"]) ? "/assets/uploads/members/default.png" : "/assets/uploads/members/".$_row["member_pic"];
			$member_pic = $_row['facescan_id'] ? $_file : $member_pic;

			$regis_type = '';
			if( $_row['facescan_id'] ) $regis_type = 'ใบหน้า';
			elseif( $_row['id_card_data'] ) $regis_type = 'บัตรประชาชน';
			else $regis_type = 'เลขสมาชิก';

			$data[] = [
				"id" => $_row["meeting_regis_id"],
				"member_id" => $_row["member_id"],
				"fullname" => $_row["firstname_th"]." ".$_row["lastname_th"],
				"create_time" =>  $this->center_function->ConvertToThaiDate($_row["create_time"], '1', '1', '0', '1'),
				"regis_type" => $regis_type,
				"member_pic" => $member_pic,
				"status" => 1,
				"status_text" => "เสร็จสิ้น",
				"card_tail" => $_row["card_tail_number"]
			];
		}

		echo json_encode([
			"data" => $data,
			"count" => $count
		]);
		exit;
	}

	function get_facescan3() {
		$id = $_POST["id"];
		$mach_id = $_POST["mach_id"];
		$keyword = $_POST["keyword"];
		$start = (int)$_POST["start"];
		$length = (int)$_POST["length"];

		$where = "coop_meeting_regis.meeting_id = '{$id}' AND coop_meeting_regis.facescan_id <> 0 AND coop_meeting_regis.is_delete = 0";
		if(!empty($mach_id)) {
			$where .= " AND coop_meeting_regis.mach_id = '{$mach_id}'";
		}
		if($keyword != "") {
			$where .= " AND (coop_meeting_regis.member_id LIKE '{$keyword}%' OR coop_mem_apply.firstname_th LIKE '%{$keyword}%' OR coop_mem_apply.lastname_th LIKE '%{$keyword}%')";
		}

		$this->db->select("COUNT(coop_meeting_regis.meeting_regis_id) AS c");
		$this->db->from("coop_meeting_regis");
		$this->db->join("coop_mem_apply", "coop_meeting_regis.member_id = coop_mem_apply.member_id", "inner");
		$this->db->where($where);
		$_row_count = $this->db->get()->row_array();
		$count = $_row_count["c"];

		$this->db->select("coop_meeting_regis.*, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_mem_apply.member_pic");
		$this->db->from("coop_meeting_regis");
		$this->db->join("coop_mem_apply", "coop_meeting_regis.member_id = coop_mem_apply.member_id", "inner");
		$this->db->where($where);
		$this->db->order_by("coop_meeting_regis.create_time, coop_meeting_regis.meeting_regis_id");
		$this->db->limit($length, $start);
		$_rs = $this->db->get()->result_array();
		$data = [];
		foreach($_rs as $key => $_row) {
			$this->db->select("meeting_regis_id");
			$this->db->from("coop_meeting_regis");
			$this->db->where("meeting_id = '{$id}' AND card_tail_number = '{$_row["card_tail"]}' AND card_tail_number <> '' AND meeting_regis_id <> '{$_row["meeting_regis_id"]}'");
			$_row_dup = $this->db->get()->row_array();
			$is_dup = !empty($_row_dup["meeting_regis_id"]);
			
			$_file = WEB_BASE_URL."/uploads/facescan/".((int)$_row["member_id"]).".jpg";
			$member_pic = empty($_row["member_pic"]) ? "/assets/uploads/members/default.png" : "/assets/uploads/members/".$_row["member_pic"];
			$member_pic = $_row['facescan_id'] ? $_file : $member_pic;

			$regis_type = '';
			if( $_row['facescan_id'] ) $regis_type = 'ใบหน้า';
			elseif( $_row['id_card_data'] ) $regis_type = 'บัตรประชาชน';
			else $regis_type = 'เลขสมาชิก';

			$data[] = [
				"id" => $_row["meeting_regis_id"],
				"member_id" => $_row["member_id"],
				"fullname" => $_row["firstname_th"]." ".$_row["lastname_th"],
				"create_time" =>  $this->center_function->ConvertToThaiDate($_row["create_time"], '1', '1', '0', '1'),
				"regis_type" => $regis_type,
				"member_pic" => $member_pic,
				"card_tail" => $_row["card_tail_number"],
				"num" => ($is_dup > 0 ? "ซ้ำ " : "").(empty($_row["card_tail_number"]) ? "?" : $_row["card_tail_number"]),
				"num_status" => $is_dup > 0 ? "error" : (empty($_row["card_tail_number"]) ? "active" : ""),
				"meeting_payment" => $_row["meeting_payment"],
				"receive_gift" => $_row["is_gift"],
				"is_dup" => $is_dup
			];
		}

		echo json_encode([
			"data" => $data,
			"count" => $count
		]);
		exit;
	}
	
	public function facescan_register() {
		$arr_data = array();

		$search_text = $_GET["search_text"];
		$page = empty($_GET["page"]) ? 1 : $_GET["page"];
		$per_page = 100;

		$_res = $this->center_function->post_url(WEB_BASE_URL."/APIs/facescan.get.register.php", [
			"search_text" => $search_text,
			"page" => $page,
			"per_page" => $per_page
		]);
		$_data = json_decode($_res, true);

		$paging = $this->pagination_center->paginating(intval($page), $_data['page']['row_count'], $per_page, 20, $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

		foreach($_data['data'] as $_key => $_row) {
			$this->db->select("firstname_th, lastname_th");
			$this->db->from("coop_mem_apply");
			$this->db->where("member_id = '{$_row['member_id']}'");
			$_row_member = $this->db->get()->row_array();

			$_data['data'][$_key]['fullname'] = $_row_member['firstname_th']." ".$_row_member['lastname_th'];
		}

		$i = $_data['page']['index'];

		$arr_data['num_rows'] = $_data['page']['row_count'];
		$arr_data['paging'] = $paging;
		$arr_data['rs'] = $_data['data'];
		$arr_data['i'] = $i;

		$this->libraries->template('meeting/facescan_register', $arr_data);
	}

	public function register_info() {
		$arr_data = array();

		$arr_data['meeting_id'] = (int)$_GET['id'];


		$this->db->select("*");
		$this->db->from("coop_meeting");
		$this->db->where("meeting_id = '{$_GET['id']}'");
		$row = $this->db->get()->row_array();
		$arr_data['row_meeting'] = $row;

		$where = "coop_meeting_regis.meeting_id = '{$_GET["id"]}' AND coop_meeting_regis.is_delete = 0";

		$this->db->select("coop_meeting_regis.*, coop_meeting.meeting_pay, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_mem_apply.member_pic");
		$this->db->from("coop_meeting_regis");
		$this->db->join("coop_meeting", "coop_meeting_regis.meeting_id = coop_meeting.meeting_id", "left");
		$this->db->join("coop_mem_apply", "coop_meeting_regis.member_id = coop_mem_apply.member_id", "left");
		$this->db->where("{$where}");
		$this->db->order_by("coop_meeting_regis.create_time");
		$rs = $this->db->get()->result_array();

		$arr_data['rs'] = $rs;

		$this->libraries->template('meeting/register_info', $arr_data);
	}

	public function register_info_get() {
		$arr_data = array();

		$meeting_id = (int)$_POST['meeting_id'];
		$filter = $_POST['filter'];


		$this->db->select("*");
		$this->db->from("coop_meeting");
		$this->db->where("meeting_id = {$meeting_id}");
		$row = $this->db->get()->row_array();
		$arr_data['row_meeting'] = $row;

		$where = "coop_meeting_regis.meeting_id = {$meeting_id}
							AND coop_meeting_regis.is_delete = 0
							AND (coop_mem_apply.firstname_th LIKE '%{$filter}%' OR coop_mem_apply.lastname_th LIKE '%{$filter}%' OR coop_mem_apply.member_id LIKE '%{$filter}%')";

		$this->db->select("coop_meeting_regis.*, coop_meeting.meeting_pay, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_mem_apply.member_pic");
		$this->db->from("coop_meeting_regis");
		$this->db->join("coop_meeting", "coop_meeting_regis.meeting_id = coop_meeting.meeting_id", "left");
		$this->db->join("coop_mem_apply", "coop_meeting_regis.member_id = coop_mem_apply.member_id", "left");
		$this->db->where("{$where}");
		$this->db->order_by("coop_meeting_regis.create_time");
		$json['data'] = $this->db->get()->result_array();

		echo json_encode($json);
		exit();
	}

	public function register_info_save() {
		$json = [

		];
		foreach($_POST['card_tail_number'] as $k => $v) {
			if(!empty($v)) {
				$data_update = [];
				$data_update['card_tail_number'] = trim($v);
				$data_update['update_time'] = date('Y-m-d H:i:s');
				$data_update['update_ip'] = $_SERVER["REMOTE_ADDR"];
				$data_insert['user_id'] = $_SESSION["USER_ID"];
				$this->db->where("meeting_regis_id", $k);
				$this->db->update("coop_meeting_regis", $data_update);
			}
		}
		foreach($_POST['is_gift'] as $k => $v) {
			if(!empty($v)) {
				$data_update = [];
				$data_update['is_gift'] = trim($v);
				$data_update['update_time'] = date('Y-m-d H:i:s');
				$data_update['update_ip'] = $_SERVER["REMOTE_ADDR"];
				$data_update['user_id'] = $_SESSION["USER_ID"];
				$this->db->where("meeting_regis_id", $k);
				$this->db->update("coop_meeting_regis", $data_update);
			}
		}
		echo json_encode($json);
	}


	public function register_info_del() {
		$meeting_regis_id = explode(',', $_POST['meeting_regis_id']);
		foreach($meeting_regis_id as $k => $v) {
			$this->db->select("*");
			$this->db->from("coop_meeting_regis");
			$this->db->where("meeting_regis_id = '{$v}'");
			$row = $this->db->get()->row_array();
			$facescan_id = $row['facescan_id'];
			if( $facescan_id > 0 ) {
				$this->center_function->post_url(WEB_BASE_URL."/APIs/facescan.delete.time.php", ['id' => $facescan_id]);
			}
			$data_update['is_delete'] = 1;
			$data_update['update_time'] = date('Y-m-d H:i:s');
			$data_update['update_ip'] = $_SERVER["REMOTE_ADDR"];
			$data_update['user_id'] = $_SESSION["USER_ID"];
			$this->db->where("meeting_regis_id", $v);
			$this->db->update("coop_meeting_regis", $data_update);
		}

	}

	public function meeting_detail_del() {
		$meeting_id = $_POST['id'];
		$this->db->select("*");
		$this->db->from("coop_meeting_regis");
		$this->db->where("meeting_id = '{$meeting_id}'");
		$rs = $this->db->get()->result_array();
		foreach($rs as $k => $row) {
			$facescan_id = $row['facescan_id'];
			if( $facescan_id > 0 ) {
				$this->center_function->post_url(WEB_BASE_URL."/APIs/facescan.delete.time.php", ['id' => $facescan_id]);
			}
			$data_update['is_delete'] = 1;
			$data_update['update_time'] = date('Y-m-d H:i:s');
			$data_update['update_ip'] = $_SERVER["REMOTE_ADDR"];
			$data_update['user_id'] = $_SESSION["USER_ID"];
			$this->db->where("meeting_regis_id", $row['meeting_regis_id']);
			$this->db->update("coop_meeting_regis", $data_update);
		}
	}

	public function set_card_tail() {
		$json = [
			'err_code' => '',
			'msg' => ''
		];

		$this->db->select("meeting_regis_id, member_id");
		$this->db->from("coop_meeting_regis");
		$this->db->where("meeting_id = '{$_POST["meeting_id"]}' AND card_tail_number = '{$_POST["card_tail"]}' AND card_tail_number <> '' AND meeting_regis_id <> '{$_POST["id"]}'");
		$row = $this->db->get()->row_array();
		if(empty($row["meeting_regis_id"])) {
			$data_update = [];
			$data_update['card_tail_number'] = $_POST["card_tail"];
			$data_update['update_time'] = date('Y-m-d H:i:s');
			$data_update['update_ip'] = $_SERVER["REMOTE_ADDR"];
			$data_insert['user_id'] = $_SESSION["USER_ID"];
			$this->db->where("meeting_regis_id", $_POST["id"]);
			$this->db->update("coop_meeting_regis", $data_update);
		}
		else {
			$json['err_code'] = '1';
			$json['msg'] = 'เลขหางบัตรซ้ำกับสมาชิกรหัส '.$row["member_id"];
		}

		echo json_encode($json);
		exit;
	}

	public function set_receive_money() {
		$json = [

		];

		$data_update = [];
		$data_update['meeting_payment'] = 1;
		$data_update['payment_date'] = date('Y-m-d');
		$data_update['update_time'] = date('Y-m-d H:i:s');
		$data_update['update_ip'] = $_SERVER["REMOTE_ADDR"];
		$data_insert['user_id'] = $_SESSION["USER_ID"];
		$this->db->where("meeting_regis_id", $_POST["id"]);
		$this->db->update("coop_meeting_regis", $data_update);

		echo json_encode($json);
		exit;
	}
	
	public function set_receive_gift() {
		$json = [

		];

		$data_update = [];
		$data_update['is_gift'] = 1;
		$data_update['gift_date'] = date('Y-m-d');
		$data_update['update_time'] = date('Y-m-d H:i:s');
		$data_update['update_ip'] = $_SERVER["REMOTE_ADDR"];
		$data_insert['user_id'] = $_SESSION["USER_ID"];
		$this->db->where("meeting_regis_id", $_POST["id"]);
		$this->db->update("coop_meeting_regis", $data_update);

		echo json_encode($json);
		exit;
	}
	
}