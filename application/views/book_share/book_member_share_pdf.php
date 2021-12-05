<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
	if($text!=''){
		return number_format($text,2);
	}else{
		return '';
	}
}
function cal_age($birthday,$type = 'y'){     //รูปแบบการเก็บค่าข้อมูลวันเกิด
	$birthday = date("Y-m-d",strtotime($birthday));
	$today = date("Y-m-d");   //จุดต้องเปลี่ยน
	list($byear, $bmonth, $bday)= explode("-",$birthday);       //จุดต้องเปลี่ยน
	list($tyear, $tmonth, $tday)= explode("-",$today);                //จุดต้องเปลี่ยน
	$mbirthday = mktime(0, 0, 0, $bmonth, $bday, $byear);
	$mnow = mktime(0, 0, 0, $tmonth, $tday, $tyear );
	$mage = ($mnow - $mbirthday);
	//echo "วันเกิด $birthday"."<br>\n";
	//echo "วันที่ปัจจุบัน $today"."<br>\n";
	//echo "รับค่า $mage"."<br>\n";
	$u_y=date("Y", $mage)-1970;
	$u_m=date("m",$mage)-1;
	$u_d=date("d",$mage)-1;
	if($type=='y'){
		return $u_y;
	}else if($type=='m'){
		return $u_m;
	}else{
		return $u_d;
	}
}
function account_id_format($account_id){
	$account_id_arr = str_split($account_id,1);
	$account_id_format = '';
	foreach($account_id_arr as $key => $value){
		if($key == '1' || $key == '4' || $key == '5'){
			$account_id_format .= ' - '.$value;
		}else{
			$account_id_format .= $value;
		}

	}
	return $account_id_format;
}

$pdf = new FPDI('P','mm', array($style['width_page'], $style['height_page']));
$pdf->AddPage();

$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$pdf->SetMargins($style['left_margin'], $style['top_margin'], $style['right_margin']);

$border = 0;
$pdf->SetTextColor(0, 0, 0);
$pdf->SetAutoPageBreak(false);
foreach ($rows as $key => $value) {
	$pdf->SetXY( $value['x'], $value['y'] );
	$pdf->SetFont('THSarabunNew', '', $value['font_size'] );
	//$pdf->cell($value['width'], 8, U2T($value['text']), $border, 1, $value['align']);
	$pdf->MultiCell($value['width'], 6, U2T($value['text']), $border, $value['align']);
}
$data_insert = array();

////////***********************************************เงื่อนไขเปิดเล่มใหม่
	$this->db->select(array('*'));
	$this->db->from('coop_mem_share_print');
	$this->db->where("member_id = '".$this->input->get('member_id')."'");
	$row_share_print = $this->db->get()->result_array();
	if(empty($row_share_print)){
		$data_insert = array();
		$data_insert['member_id'] = $this->input->get('member_id');
		// $data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['book_status'] = "1";
		$data_insert['book_number'] = "1";
		$data_insert['date_print_book'] = date("Y-m-d H:i:s");
		// $data_insert['print_number_point'] = "1";
		// $data_insert['show_no'] = "1";
		$this->db->insert('coop_mem_share_print', $data_insert);
	}else{
		//ถ้ามีการเพิ่มเล่มต้องมาแก้ตรงนี้ก่อน
		$data_insert = array();
		$data_insert['date_print_book'] = date("Y-m-d H:i:s");
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['book_status'] = "1";
		$data_insert['book_number'] = "1";
		$this->db->where('member_id',$this->input->get('member_id'));
		$this->db->update('coop_mem_share_print', $data_insert);
	}
$pdf->Output();