<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Set_css_color extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
		$this->load->model('Set_css_model','set_css');
    }

    public function index()
    {     
		$css_color_main = $this->set_css->css_main();
		$css_color_login = $this->set_css->css_login();
		exit;
	}
}
