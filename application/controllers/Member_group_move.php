<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_group_move extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function index() {
		$arr_data = array();

		$sql = "SELECT id, mem_group_name
					FROM coop_mem_group_new
					WHERE mem_group_type = '1'";
		$_rs = $this->db->query($sql)->result_array();
		$arr_data['rs_dep'] = $_rs;

		$sql = "SELECT coop_mem_group.id AS department, coop_mem_group.mem_group_name AS department_name,
						coop_mem_group2.id AS faction, coop_mem_group2.mem_group_name AS faction_name,
						COUNT(coop_mem_apply.member_id) AS c,
						group_new.department AS department_new, group_new.faction AS faction_new, group_new.level AS level_new, group_new.c AS c_new
					FROM coop_mem_group
						INNER JOIN coop_mem_group coop_mem_group2 ON coop_mem_group.id = coop_mem_group2.mem_group_parent_id AND coop_mem_group2.mem_group_type = '2'
						INNER JOIN coop_mem_apply ON coop_mem_group2.id = coop_mem_apply.faction
						LEFT OUTER JOIN (
							SELECT department_old, faction_old, department, faction, level, COUNT(member_id) AS c
							FROM coop_mem_apply_group_new
							GROUP BY department_old, faction_old, department, faction, level
						) group_new ON coop_mem_group.id = group_new.department_old AND coop_mem_group2.id = group_new.faction_old
					WHERE coop_mem_group.mem_group_type = '1'
					GROUP BY coop_mem_group.id, coop_mem_group.mem_group_name,
						coop_mem_group2.id, coop_mem_group.mem_group_name,
						group_new.department, group_new.faction, group_new.level, group_new.c";
		$rs = $this->db->query($sql)->result_array();
		$arr_data['rs'] = $rs;
		$arr_data['i'] = 1;

		$this->libraries->template('setting_member_data/member_group_move',$arr_data);
	}
	
	public function get_faction_list() {
		session_write_close();

		$id = $_POST["id"];

		$sql = "SELECT id, mem_group_name AS name
					FROM coop_mem_group_new
					WHERE mem_group_type = '2' AND mem_group_parent_id = '{$id}'";
		$_rs = $this->db->query($sql)->result_array();
		echo json_encode([
			"status" => 1,
			"data" => $_rs
		]);
		exit;
	}

	public function get_level_list() {
		session_write_close();

		$id = $_POST["id"];

		$sql = "SELECT id, mem_group_name AS name
					FROM coop_mem_group_new
					WHERE mem_group_type = '3' AND mem_group_parent_id = '{$id}'";
		$_rs = $this->db->query($sql)->result_array();
		echo json_encode([
			"status" => 1,
			"data" => $_rs
		]);
		exit;
	}

	public function save() {
		$this->db->where("department_old = '{$_POST["department_old"]}' AND faction_old = '{$_POST["faction_old"]}'");
        $this->db->delete("coop_mem_apply_group_new");

		$sql = "SELECT member_id
					FROM coop_mem_apply
					WHERE department = '{$_POST["department_old"]}' AND faction = '{$_POST["faction_old"]}'";
		$_rs = $this->db->query($sql)->result_array();
		foreach($_rs as $_row) {
			$data_insert = array();
			$data_insert['member_id'] = $_row['member_id'];
			$data_insert['department_old'] = $_POST['department_old'];
			$data_insert['faction_old'] = $_POST['faction_old'];
			$data_insert['department'] = $_POST['department'];
			$data_insert['faction'] = $_POST['faction'];
			$data_insert['level'] = $_POST['level'];
			$this->db->insert('coop_mem_apply_group_new', $data_insert);
		}

		echo json_encode([
			"status" => 1,
			"c" => count($_rs)
		]);
		exit;
	}

}