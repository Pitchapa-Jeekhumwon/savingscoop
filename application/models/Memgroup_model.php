<?php
class Memgroup_model extends CI_Model {
    
    public function get_department_all()
    {
        $query = $this->db->select('id, mem_group_name')
        ->from('coop_mem_group')
        ->where('mem_group_type = 1')
        ->get();
        return $query->result_array();
    }

	public function get_department_member($member_id,$date='')
    {
		if($date == ""){
			$date = date("Y-m-d");
		}
	
        $row = $this->db->select("
					IF
						(
							( SELECT level_old FROM coop_mem_group_move WHERE date_move >= '{$date}' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1 ),
							( SELECT level_old FROM coop_mem_group_move WHERE date_move >= '{$date}' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1 ),
							coop_mem_apply.LEVEL 
						) AS LEVEL,
					IF
						(
							( SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '{$date}' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1 ),
							( SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '{$date}' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1 ),
							coop_mem_apply.faction 
						) AS faction,
					IF
						(
							( SELECT department_old FROM coop_mem_group_move WHERE date_move >= '{$date}' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1 ),
							( SELECT department_old FROM coop_mem_group_move WHERE date_move >= '{$date}' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1 ),
							coop_mem_apply.department 
						) AS department,
						member_id
				")
				->from("coop_mem_apply")
				->where("member_id = '{$member_id}'")
				->get()->row_array();
				
		//echo '<pre>'; print_r($row); echo '</pre>';		
        $result = $row;
        $result['level_name'] = $this->get_name_department($row['LEVEL']);
        $result['faction_name'] = $this->get_name_department($row['faction']);
        $result['department_name'] = $this->get_name_department($row['department']);
		return $result;
    }
	
	public function get_name_department($id)
    {
        $result = array();
		$row = $this->db->select("mem_group_name")
        ->from("coop_mem_group")
        ->where("id = '{$id}'")
        ->get()->row_array()['mem_group_name'];	
        if(!empty($row)){
			$result = $row;
		}		
		return $result;
    }
}