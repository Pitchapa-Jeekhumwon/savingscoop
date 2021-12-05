<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_main_menu extends CI_Controller {
	
	function __construct() {
		parent::__construct();
	}

    public function index(){
        {
            $arr_data = array();
            if(!empty($_GET['menu_id'])){
                $where = "t1.menu_parent_id = '".$_GET['menu_id']."'";
                $this->db->select('menu_parent_id, menu_name, menu_img');
                $this->db->from('coop_menu');
                $this->db->where("menu_id = '".$_GET['menu_id']."'");
                $menu_parent  = $this->db->get()->row_array();
                $arr_data['menu_parent'] = $menu_parent;

                $menu_parent_before = '';
                while (!empty($menu_parent['menu_parent_id'])) {
                    $this->db->select('menu_name,menu_parent_id');
                    $this->db->from('coop_menu');
                    $this->db->where("menu_id = '" . $menu_parent['menu_parent_id'] . "'");
                    $menu_parent  = $this->db->get()->row_array();
                    $menu_parent_before = '/'.$menu_parent['menu_name'].$menu_parent_before;
                }
                $menu_parent_before = 'หน้าแรก'.$menu_parent_before;
                $arr_data['menu_parent_before'] = $menu_parent_before;

            }else{
                $where = "t1.menu_parent_id is null";
            }
//            echo $menu_parent_before;
//            exit;
            $this->db->select('*');
            $this->db->from('coop_menu as t1');
            $this->db->join('(SELECT menu_parent_id, count(menu_id) as count_menu_id FROM coop_menu GROUP BY menu_parent_id) as t2', 't2.menu_parent_id = t1.menu_id', 'LEFT' );
            $this->db->where($where);
            $menu_data = $this->db->get()->result_array();

            $arr_data['data'] = $menu_data;

            $dir = "./assets/images/icon_web";
            $icon_web = array();

            if (is_dir($dir)){
                if ($dh = opendir($dir)){
                    while (($file = readdir($dh)) !== false){
                        if(preg_match('/(.*).(jpg|png)/',$file)){
//                            echo $file.'<br>';
                            $icon_web[] = $file;
                        }
                    }
                    closedir($dh);
                }
            }
            $arr_data['icon_img'] = $icon_web;

            $this->db->select('menu_icon');
            $this->db->from('coop_menu');
            $this->db->group_by('menu_icon');
            $icon = $this->db->get()->result_array();

            $arr_data['icon'] = array('icon-archive', 'icon-bar-chart', 'icon-briefcase', 'icon-cloud-download', 'icon-cog', 'icon-comments', 'icon-credit-card', 'icon-edit', 'icon-folder-open' ,'icon-globe', 'icon-list', 'icon-pencil-square-o', 'icon-usd', 'icon-user');
            if($_GET['dev']=='dev'){
                echo '<pre>';print_r($arr_data);exit;
            }
            $this->libraries->template('setting_main_menu/index', $arr_data);
        }
    }

    function show_detail()
    {
        $arr_data = array();
        $this->db->select('*');
        $this->db->from('coop_menu');
        $this->db->where("menu_id = '".$_POST['menu_id']."'");
        $arr_data['main_menu'] = $this->db->get()->result_array();

        $arr_data = array();
        $this->db->select('*');
        $this->db->from('coop_menu');
        $this->db->where("menu_parent_id = '".$_POST['menu_id']."'");
        $arr_data['data'] = $this->db->get()->result_array();

//        echo json_encode($arr_data);
//        echo print_r($menu_data);
        $this->load->view('Setting_main_menu/edit_data',$arr_data);
    }


    function edit_data()
    {
        $data = array();
        $menu_id = @$_POST['menu_id'];
        $data_edit['menu_name'] = @$_POST['menu_name'];
        $data_edit['menu_url'] = @$_POST['menu_url'];
        $data_edit['menu_icon'] = @$_POST['menu_icon'];
        $data_edit['menu_img'] = @$_POST['menu_img'];
        $data_edit = $this->db->update('coop_menu', $data_edit, "menu_id = ".$menu_id);
        echo 'success';

    }

    function insert_data()
    {
        if(!empty(@$_POST['menu_parent_id'])){
            $where = "menu_parent_id = '".@$_POST['menu_parent_id']."'";
        }else{
            $where = "menu_parent_id is null";
        }
        $this->db->select('max(order_by) as max_order_by');
        $this->db->from('coop_menu');
        $this->db->where($where);
        $max_order_by = $this->db->get()->row_array()['max_order_by'];

        $data = array();
        $data_insert['menu_id'] = @$_POST['menu_id'];
        if(!empty(@$_POST['menu_parent_id'])){
            $data_insert['menu_parent_id'] = @$_POST['menu_parent_id'];
        }
        $data_insert['menu_name'] = @$_POST['menu_name'];
        $data_insert['menu_url'] = @$_POST['menu_url'];
        $data_insert['menu_icon'] = @$_POST['menu_icon'];
        $data_insert['menu_img'] = @$_POST['menu_img'];
        $data_insert['menu_active'] = '1';
        $data_insert['nav_hidden'] = '';
        $data_insert['order_by'] = $max_order_by;
        $data_insert['menu_type'] = '1';

        $insert = $this->db->insert('coop_menu', $data_insert);
        echo 'success';
//
//        print_r($data_insert);

    }

    function del_data()
    {
        $data = array();
        $menu_id = @$_POST['menu_id'];

        $this->db->delete('coop_menu', array('menu_id' => $menu_id));
        echo 'success';

    }

    function edit_menu_active()
    {
        $data_update = array();

//        echo '<pre>';print_r($_POST);
        if(!empty($_POST['menu_active'])){
            foreach ($_POST['menu_active'] as $key => $value) {
                $data_update[] = array(
                    'menu_id' => $key,
                    'menu_active' => $value,
                    'nav_hidden'=> $_POST['nav_hidden'][$key]
                );
            }
        }
//        echo '<pre>';print_r($data_update);
        $this->db->update_batch('coop_menu', $data_update, 'menu_id');

//        echo 'success';
        echo '<meta http-equiv= "refresh" content="0; url=/Setting_main_menu"/>';
    }
	
}