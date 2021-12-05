<?php
class Setting_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
    }

    public function get($setting_name=''){
        if($setting_name!="") $this->db->where("setting_name", $setting_name);
        $rs = $this->db->get("coop_setting");
        $value = ($setting_name!="") ? $rs->row_array()['setting_value'] : $rs->result_array();
        return $value;
    }

    public function set($setting_name, $setting_value){
        $this->db->set("setting_name", $setting_name);
        $this->db->set("setting_value", $setting_value);
        $this->db->insert("coop_setting");
    }

}