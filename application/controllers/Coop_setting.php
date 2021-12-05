<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coop_setting extends CI_Controller {

    function __construct()
    {
        parent::__construct();
    }

    public function get_setting(){
        $setting = $this->uri->segment(3);

        // if(empty($setting)){
        //     $res = array();
        //     header("content-type: application/json; charset=utf-8;");
        //     echo json_encode($res);
        //     exit;
        // }

        $res = array();
        $res['status'] = 200;
        if(!empty($setting)){
            $this->db->where("setting_name", $setting);
        }
        $this->db->select(array("setting_name", "setting_value"));
        $rs = $this->db->get("coop_setting")->result_array();
        $res['setting'] = array();
        foreach($rs as $key=>$value) {
            $res['setting'][$value['setting_name']] = $value["setting_value"];
        }
        header("content-type: application/json; charset=utf-8;");
        echo json_encode($res);
        exit;
    }
}
