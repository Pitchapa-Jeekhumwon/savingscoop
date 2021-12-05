<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}
function SetDash($pdf, $black=null, $white=null) {
    if($black!==null)
        $s=sprintf('[%.3F %.3F] 0 d',$black*$pdf->k,$white*$pdf->k);
    else
        $s='[] 0 d';

    $pdf->_out($s);
}

$pdf = new FPDI('L','mm', array(177, 129));

$pdf->AddPage();
$pdf->AddFont('common', '', 'THSarabunNew.php');
$pdf->AddFont('bold', '', 'THSarabunNew-Bold.php');

$pdf->SetFont('common', '', 13 );
$pdf->SetMargins(0, 0, 0);
$border = 0;
$pdf->SetTextColor(0, 0, 0);
$pdf->SetAutoPageBreak(true,0);

//Set data
$full_w = 177;
$y_point = 5;
$font_size = 14;

//Title
$pdf->setFillColor(255,255,255);

$pdf->SetFont('common', '', $font_size);
$pdf->SetXY( 0, $y_point);
$pdf->MultiCell($full_w, 8, U2T($_SESSION['COOP_NAME']),0,'C',1);

$y_point += 10;
$pdf->SetFont('bold', 'U', $font_size);
$pdf->SetXY( 0, $y_point);
$pdf->MultiCell($full_w, 8, U2T("ใบสำคัญจ่าย"),0,'C',0);

$pdf->SetFont('common', '', $font_size);
$pdf->SetXY( 10, $y_point);
$pdf->MultiCell($full_w - 20, 8, U2T($voucher->no),0,'R',0);

$y_point +=8;
$pdf->SetXY( 10, $y_point);
$pdf->MultiCell($full_w - 20, 8, U2T("วันที่ ".$this->center_function->ConvertToThaiDate($voucher->voucher_timestamp, 0,0)),0,'R',0);
SetDash($pdf, 0.5, 0.5);
$pdf->Line(141, $y_point+6, 167, $y_point+6);

$y_point += 10;
$pdf->SetXY( 10, $y_point);
$pdf->MultiCell(16, 8, U2T("ข้าพเจ้า"),0,'L',0);
SetDash($pdf, 0.5, 0.5);
$pdf->Line(22, $y_point+6, 90, $y_point+6);
$pdf->SetXY( 23, $y_point);
$pdf->MultiCell(67, 8, U2T($voucher->prename_full.$voucher->firstname_th." ".$voucher->lastname_th),0,'C',0);
$pdf->SetXY( 90, $y_point);
$pdf->MultiCell(35, 8, U2T("สมาชิกเลขทะเบียนที่"),0,'L',0);
SetDash($pdf, 0.5, 0.5);
$pdf->Line(119, $y_point+6, 155, $y_point+6);
$pdf->SetXY( 119, $y_point);
$pdf->MultiCell(36, 8, U2T($voucher->member_id),0,'C',0);

$y_point += 10;
$pdf->SetXY( 10, $y_point);
$pdf->MultiCell(35, 8, U2T("ตำแหน่งหรือตำบลที่อยู่"),0,'L',0);
$pdf->SetXY( 42, $y_point);
$pdf->MultiCell(113, 8, U2T($department["department_name"]." ".$department["faction_name"]." ".$department["level_name"]),0,'C',0);
SetDash($pdf, 0.5, 0.5);
$pdf->Line(42, $y_point+6, 155, $y_point+6);

$y_point += 15;
$x1 = 10;
$w1 = 90;
$x2 = $w1+$x1;
$w2 = 35;
$x3 = $x2 + $w2;
$w3 = 20;
SetDash($pdf);
$pdf->SetLineWidth(0.1);
$pdf->Line(10, $y_point-0.8, 155, $y_point-0.8);
$pdf->SetXY( $x1, $y_point);
$pdf->MultiCell($w1, 8, U2T(""),"T",'C',0);
$pdf->SetXY( $x2, $y_point);
$pdf->MultiCell($w2, 8, U2T("บาท"),1,'C',0);
$pdf->SetXY( $x3, $y_point);
$pdf->MultiCell($w3, 8, U2T("สตางค์"),1,'C',0);

$y_point += 8;
$amount = number_format($detail["value"],2);
$amount_arr = explode('.', $amount);
$pdf->SetXY( $x1, $y_point);
$pdf->MultiCell($w1, 8, U2T("เป็นเงินค่า  ".$detail["description"]),0,'L',0);
$pdf->SetXY( $x2, $y_point);
$pdf->MultiCell($w2, 8, U2T($amount_arr[0]),1,'R',0);
$pdf->SetXY( $x3, $y_point);
$pdf->MultiCell($w3, 8, U2T($amount_arr[1]),1,'R',0);

$y_point += 8;

SetDash($pdf, 0.5, 0.5);
$pdf->Line($x1, $y_point, $x2, $y_point);
SetDash($pdf);

$pdf->SetXY( $x1, $y_point);
$pdf->MultiCell($w1, 8, U2T(""),0,'C',0);
$pdf->SetXY( $x2, $y_point);
$pdf->MultiCell($w2, 8, U2T(""),1,'C',0);
$pdf->SetXY( $x3, $y_point);
$pdf->MultiCell($w3, 8, U2T(""),1,'C',0);

$y_point += 8;

SetDash($pdf, 0.5, 0.5);
$pdf->Line($x1, $y_point, $x2, $y_point);
SetDash($pdf);

$pdf->SetXY( $x1, $y_point);
$pdf->MultiCell(10, 10, U2T("บาท"),0,'L',0);
$pdf->SetXY( $x1 + 9, $y_point);
if($pdf->GetStringWidth($this->center_function->convert($detail["value"])) > 177) {
    $text_w = $pdf->GetStringWidth($this->center_function->convert($detail["value"]));
    $ad_size = $font_size - ($text_w/177);
    $pdf->SetFont('common', '', $ad_size);
}
$pdf->MultiCell($w1 - 19, 10, U2T($this->center_function->convert($detail["value"])),0,'L',0);
$pdf->SetFont('common', '', $font_size);
SetDash($pdf, 0.5, 0.5);
$pdf->Line($x1+8, $y_point+8, $x2-8, $y_point+8);
SetDash($pdf);
$pdf->SetXY( $x2-10, $y_point);
$pdf->MultiCell(10, 10, U2T("รวม"),0,'R',0);
$pdf->SetXY( $x2, $y_point);
$pdf->MultiCell($w2, 10, U2T($amount_arr[0]),1,'R',0);
$pdf->SetXY( $x3, $y_point);
$pdf->MultiCell($w3, 10, U2T($amount_arr[1]),1,'R',0);

$y_point += 12;
$pdf->SetFont('common', 'U', $font_size);
$pdf->SetXY( 10, $y_point);
$pdf->MultiCell(20, 8, U2T("คำชี้แจง"),0,'L',0);
SetDash($pdf, 0.5, 0.5);
$pdf->Line(23, $y_point+5.8, 155, $y_point+5.8);
SetDash($pdf);

$y_point += 12;
$pdf->SetFont('common', '', $font_size);
SetDash($pdf, 0.5, 0.5);
$pdf->Line(50, $y_point+5.8, 110, $y_point+5.8);
SetDash($pdf);
$pdf->SetXY( 110, $y_point);
$pdf->MultiCell(20, 8, U2T("ผู้รับเงิน"),0,'L',0);

$pdf->Output();
