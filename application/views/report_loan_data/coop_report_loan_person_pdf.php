<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) { 
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

if(@$row_loan['marry_status']==1){
    $marry_status = "โสด";
}else if(@$row_loan['marry_status']==2){
    $marry_status = "สมรส";
}else{
    $marry_status = "ไม่ระบุ";
}

if(@$row_loan['position']!=""){
    $position = @$row_loan['position'];
}else{
    $position = "ไม่ระบุ";
}

if(@$bank_account['dividend_acc_num']!=""){
    $account_bank = @$bank_account['dividend_acc_num'];
}else{
    $account_bank = "ไม่ระบุ";
}

$pdf = new FPDI('P','mm','A4');

$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$pdf->AddFont('THSarabunNew-Bold', '', 'THSarabunNew-Bold.php');

$pdf->AddPage();
$pdf->SetMargins(0, 0, 0);
$border = 0;
$pdf->SetTextColor(0, 0, 0);
$pdf->SetAutoPageBreak(false);

$pdf->SetFont('THSarabunNew-Bold', '', 18 );
    $pdf->SetXY( 0, 10 );
    $pdf->MultiCell(210, 0, U2T(@$row_profile['coop_name_th']),0, "C");
$pdf->SetFont('THSarabunNew', '', 16 );
    $pdf->SetXY( 0, 20 );
    $pdf->MultiCell(210, 0, U2T("[  รายละเอียดต่างๆ ของสมาชิก  ]"),0, "C");
	$pdf->SetXY( 5, 20 );
    $pdf->MultiCell(70, 0, U2T(""),1, "L");
	$pdf->SetXY( 135, 20 );
    $pdf->MultiCell(70, 0, U2T(""),1, "L");
	$pdf->SetXY( 5, 27 );
    $pdf->MultiCell(100, 0, U2T("รหัสสมาชิก"),0, "L");
	$pdf->SetXY( 35, 27 );
    $pdf->MultiCell(100, 0, U2T(": ".@$row_loan['member_id']),0, "L");
	$pdf->SetXY( 5, 34 );
    $pdf->MultiCell(100, 0, U2T("ชื่อสมาชิก"),0, "L");
	$pdf->SetXY( 35, 34 );
    $pdf->MultiCell(100, 0, U2T(": ".@$row_loan['prename_short']." ".@$row_loan['firstname_th']." ".@$row_loan['lastname_th']),0, "L");
	$pdf->SetXY( 110, 34 );
    $pdf->MultiCell(100, 0, U2T("เป็นสมาชิก :"),0, "L");
	$pdf->SetXY( 100, 34 );
    $pdf->MultiCell(100, 0, U2T(@$marry_status),0, "L");
	$pdf->SetXY( 5, 41 );
    $pdf->MultiCell(100, 0, U2T("สังกัด"),0, "L");
	$pdf->SetXY( 35, 41 );
    $pdf->MultiCell(100, 0, U2T(": ".@$row_loan['subunits']),0, "L");
	$pdf->SetXY( 5, 48 );
    $pdf->MultiCell(100, 0, U2T("หน่วยย่อย"),0, "L");
	$pdf->SetXY( 35, 48 );
    $pdf->MultiCell(100, 0, U2T(": ".@$row_loan['affiliation']),0, "L");
	$pdf->SetXY( 5, 55 );
    $pdf->MultiCell(100, 0, U2T("ตำแหน่ง"),0, "L");
	$pdf->SetXY( 35, 55 );
    $pdf->MultiCell(100, 0, U2T(": ".$position),0, "L");
	$pdf->SetXY( 5, 62 );
    $pdf->MultiCell(100, 0, U2T("ชื่อคู่สมรส"),0, "L");
	$pdf->SetXY( 35, 62 );
    $pdf->MultiCell(100, 0, U2T(": ".@$row_loan['marry_name']),0, "L");

	$pdf->SetXY( 120, 39 );
    $pdf->MultiCell(0.01, 33, U2T(""),1, "L");

	$pdf->SetXY( 125, 41 );
    $pdf->MultiCell(100, 0, U2T("วันที่เกิด"),0, "L");
	$pdf->SetXY( 155, 41 );
    $pdf->MultiCell(100, 0, U2T(": ".$this->center_function->mydate2date(@$row_loan['birthday'])),0, "L");
	$pdf->SetXY( 125, 48 );
    $pdf->MultiCell(100, 0, U2T("เข้าเป็นสมาชิก"),0, "L");
	$pdf->SetXY( 155, 48 );
    $pdf->MultiCell(100, 0, U2T(": ".$this->center_function->mydate2date(@$row_loan['apply_date'])),0, "L");
    $pdf->SetXY( 125, 55 );
    $pdf->MultiCell(100, 0, U2T("อายุ ของสมาชิก"),0, "L");
	$pdf->SetXY( 155, 55 );
    $pdf->MultiCell(100, 0, U2T(": ".$this->center_function->cal_age(@$row_loan['birthday'])." ปี"),0, "L");
	$pdf->SetXY( 125, 62 );
    $pdf->MultiCell(100, 0, U2T("อายุ เป็นสมาชิก"),0, "L");
	$pdf->SetXY( 155, 62 );
    $pdf->MultiCell(100, 0, U2T(": ".$this->center_function->cal_age(@$row_loan['apply_date'])." ปี ".$this->center_function->cal_age(@$row_loan['apply_date'],'m')." เดือน ".$this->center_function->cal_age(@$row_loan['apply_date'],'d')." วัน"),0, "L");

    $pdf->SetXY( 5, 72 );
    $pdf->MultiCell(25, 0, U2T(""),1, "L");
    $pdf->SetXY( 5, 72 );
    $pdf->MultiCell(0.01, 30, U2T(""),1, "L");
    $pdf->SetXY( 33, 72 );
    $pdf->MultiCell(100, 0, U2T("ข้อมูลหุ้น"),0, "L");
    $pdf->SetXY( 53, 72 );
    $pdf->MultiCell( 103, 0, U2T(""),1, "L");
    $pdf->SetXY( 160, 72 );
    $pdf->MultiCell(100, 0, U2T("รวมยอด"),0, "L");
    $pdf->SetXY( 180, 72 );
    $pdf->MultiCell(25, 0, U2T(""),1, "L");

    $pdf->SetXY( 5, 82 );
    $pdf->MultiCell(100, 0, U2T("ชำระถึงงวดที่"),0, "L");
    $pdf->SetXY( 35, 82 );
    $pdf->MultiCell(100, 0, U2T(@$row_loan['share_period']."  ".$this->center_function->mydate2date(@$row_loan['share_bill_date'])),0,"L");
    $pdf->SetXY( 5, 89 );
    $pdf->MultiCell(100, 0, U2T("หุ้นรายเดือน"),0, "L");
    $pdf->SetXY( 35, 89 );
    $pdf->MultiCell(100, 0, U2T(num_format(@$row_loan['share_early_value'])." บาท"),0, "L");
    $pdf->SetXY( 5, 96 );
    $pdf->MultiCell(100, 0, U2T("หุ้นสะสม"),0, "L");
    $pdf->SetXY( 35, 96 );
    $pdf->MultiCell(100, 0, U2T(num_format(@$row_loan['share_collect_value'])." บาท"),0, "L");
    $pdf->SetXY( 75, 72 );
    $pdf->MultiCell(0.01, 30, U2T(""),1, "L");
    $pdf->SetXY( 76, 82 );
    $pdf->MultiCell(100, 0, U2T("เงินเดือน"),0, "L");
    $pdf->SetXY( 100, 82 );
    $pdf->MultiCell(100, 0, U2T(": ".num_format(@$row_loan['salary'])),0, "L");
    $pdf->SetXY( 76, 89 );
    $pdf->MultiCell(100, 0, U2T("บ/ช ธนาคาร"),0, "L");
    $pdf->SetXY( 100, 89 );
    $pdf->MultiCell(100, 0, U2T(": ".$account_bank),0, "L");
    $pdf->SetXY( 135, 72 );
    $pdf->MultiCell(0.01, 30, U2T(""),1, "L");
    $pdf->SetXY( 136, 82 );
    $pdf->MultiCell(100, 0, U2T("ยอดกู้"),0, "L");
    $pdf->SetXY( 153, 82 );
    $pdf->MultiCell(100, 0, U2T(": ".num_format(@$sum_loan_amount)." บาท"),0, "L");
    $pdf->SetXY( 136, 89 );
    $pdf->MultiCell(100, 0, U2T("คงเหลือ"),0, "L");
    $pdf->SetXY( 153, 89 );
    $pdf->MultiCell(100, 0, U2T(": ".num_format(@$sum_loan_balance)." บาท"),0, "L");
    $pdf->SetXY( 205, 72 );
    $pdf->MultiCell(0.01, 30, U2T(""),1, "L");

    $pdf->SetXY( 5, 102 );
    $pdf->MultiCell(12, 10, U2T("ลำดับ"),1, "C");
    $pdf->SetXY( 17, 102 );
    $pdf->MultiCell(25, 10, U2T("สัญญากู้"),1, "C");
    $pdf->SetXY( 42, 102 );
    $pdf->MultiCell(22, 10, U2T("วันกู้"),1, "C");
    $pdf->SetXY( 64, 102 );
    $pdf->MultiCell(23, 10, U2T("เริ่มชำระ"),1, "C");
    $pdf->SetXY( 87, 102 );
    $pdf->MultiCell(28, 10, U2T("ยอดกู้"),1, "C");
    $pdf->SetXY( 115, 102 );
    $pdf->MultiCell(25, 10, U2T("ค่างวด/จน."),1, "C");
    $pdf->SetXY( 140, 102 );
    $pdf->MultiCell(30, 10, U2T("คงเหลือ"),1, "C");
    $pdf->SetXY( 170, 102 );
    $pdf->MultiCell(23, 10, U2T("คิด ด/บ ถึง"),1, "C");
    $pdf->SetXY( 193, 102 );
    $pdf->MultiCell(12, 10, U2T("งวด"),1, "C");
    $pdf->SetFont('THSarabunNew', '', 16 );
    $y_point = 112;
    $pdf->SetXY( 0, $y_point );
    $key=1;

    foreach(@$loan_data AS $key => $value){
    $y_point += $h;
    $key++;
    $pdf->SetXY( 5, $y_point );
    $pdf->MultiCell(12, 10, U2T(@$key),0, "C");
    $H = $pdf->GetY();
    $pdf->SetXY( 17, $y_point );
    $pdf->MultiCell(0.1, 10, U2T (""),1, "L");
    $pdf->SetXY( 17, $y_point );
    $pdf->MultiCell(25, 10, U2T($value['petition_number']),0, "R");
    $pdf->SetXY( 42, $y_point );
    $pdf->MultiCell(0.1, 10, U2T (""),1, "L");
    $pdf->SetXY( 42, $y_point );
    $pdf->MultiCell(22, 10, U2T($this->center_function->mydate2date($value['createdatetime'])),0, "L");
    $pdf->SetXY( 64, $y_point );
    $pdf->MultiCell(0.1, 10, U2T (""),1, "L");
    $pdf->SetXY( 64, $y_point );
    $pdf->MultiCell(23, 10, U2T($this->center_function->mydate2date($value['date_start_period'])),0, "L");
    $pdf->SetXY( 87, $y_point );
    $pdf->MultiCell(0.1, 10, U2T (""),1, "L");
    $pdf->SetXY( 87, $y_point );
    $pdf->MultiCell(28, 10, U2T(num_format($value['loan_amount'])),0, "R");
    $pdf->SetXY( 115, $y_point );
    $pdf->MultiCell(0.1, 10, U2T (""),1, "L");
    $pdf->SetXY( 115, $y_point );
    $pdf->MultiCell(25, 10, U2T(number_format($value['money_per_period'],2)."/".$value['period_amount']),0, "R");
    $pdf->SetXY( 140, $y_point );
    $pdf->MultiCell(0.1, 10, U2T (""),1, "L");
    $pdf->SetXY( 140, $y_point );
    $pdf->MultiCell(30, 10, U2T(num_format($value['loan_amount_balance'])),0, "R");
    $pdf->SetXY( 170, $y_point );
    $pdf->MultiCell(0.1, 10, U2T (""),1, "L");
    $pdf->SetXY( 170, $y_point );
    $pdf->MultiCell(23, 10, U2T($this->center_function->mydate2date($value['date_last_interest'])),0, "R");
    $pdf->SetXY( 193, $y_point );
    $pdf->MultiCell(0.1, 10, U2T (""),1, "L");
    $pdf->SetXY( 193, $y_point );
    $pdf->MultiCell(12, 10, U2T($value['period_now']),0, "C");
    $pdf->SetXY( 5, $y_point );
    $pdf->MultiCell(0.1, 10, U2T (""),1, "L");
    $pdf->SetXY( 205, $y_point );
    $pdf->MultiCell(0.1, 10, U2T (""),1, "L");
    $h = $H-$y_point;
    }
    $pdf->SetXY( 5, $y_point+10 );
    $pdf->MultiCell(200, 0, U2T (""),1, "L");
    $pdf->SetXY( 5, $y_point+11 );
    $pdf->MultiCell(100, 10, U2T("ผู้พิมพ์ : ".$_SESSION['USER_NAME']),0, "L");
    $pdf->SetXY( 130, $y_point+11 );
    $pdf->MultiCell(100, 10, U2T("วันที่ : ".$this->center_function->mydate2date(@date('Y-m-d'),0,0)),0, "L");
    $pdf->SetXY( 165, $y_point+11 );
    $pdf->MultiCell(130, 10, U2T("เวลา : ".date('H:i:s')),0, "L");
    $y_point = $H;

$pdf->Output();