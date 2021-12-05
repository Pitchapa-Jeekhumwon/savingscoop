<?php
$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
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

$pdf = new FPDI('P','mm', array(180,155));
$pdf->AddPage();

$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$pdf->SetFont('THSarabunNew', '', 18 );
$pdf->SetMargins(0, 0, 0);
$border = 0;
$pdf->SetTextColor(0, 0, 0);
$pdf->SetAutoPageBreak(false);

$y_point = 128;
$pdf->SetXY( 40, $y_point );
$pdf->MultiCell(80, 5, U2T($this->center_function->format_account_number($account_id)), $border, 1);
$pdf->SetXY( 115, $y_point );
$pdf->MultiCell(40, 5, U2T($row['book_number']), $border, 1);

$y_point = 138;
$pdf->SetXY( 20, $y_point );
$pdf->MultiCell(100, 5, U2T($row['account_name']), $border, 1);

$y_point = 149;
$pdf->SetXY( 40, $y_point );
$pdf->MultiCell(45, 5, U2T($row['mem_id']), $border, 1);
$pdf->SetXY( 115, $y_point );
$pdf->MultiCell(35, 5, U2T($row_gname['mem_group_name']), $border, 1);

$y_point = 160;
$pdf->SetXY( 25, $y_point );
$pdf->MultiCell(60, 5, U2T(date("d")." ".($month_arr[date('n')])." ".(date("Y") +543)), $border, 1);

$pdf->Output();