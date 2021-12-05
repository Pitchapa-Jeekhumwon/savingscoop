<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Label extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	public function index() {
		$arr_data = array();
		
		$arr_data["start_num"] = $_GET["start_num"];
		$arr_data["end_num"] = $_GET["end_num"];
		
		$this->preview_libraries->template_preview('meeting/label', $arr_data);
	}
	
	public function barcode() {
		$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
		echo $generator->getBarcode($_GET["c"], $generator::TYPE_CODE_128, 3, 50);
		header('Content-Type: image/png');
	}
	
}