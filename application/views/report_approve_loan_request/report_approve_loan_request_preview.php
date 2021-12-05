<?php
function GETVAR($key, $default = null, $prefix = null, $suffix = null) {
    return isset($_GET[$key]) ? $prefix . $_GET[$key] . $suffix : $prefix . $default . $suffix;
}
$mFull = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
$mShort = array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
$str = "" ;
$datetime = date("Y-m-d H:i:s");

function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", trim($text)); }
$font = GETVAR('font','fontawesome-webfont1','','.php');

function getMBStrSplit($string, $split_length = 1){
    mb_internal_encoding('UTF-8');
    mb_regex_encoding('UTF-8');

    $split_length = ($split_length <= 0) ? 1 : $split_length;
    $mb_strlen = mb_strlen($string, 'utf-8');
    $array = array();
    $i = 0;

    while($i < $mb_strlen)
    {
        $array[] = mb_substr($string, $i, $split_length);
        $i = $i+$split_length;
    }

    return $array;
}

function getStrLenTH($string)
{
    $array = getMBStrSplit($string);
    $count = 0;

    foreach($array as $value)
    {
        $ascii = ord(iconv("UTF-8", "TIS-620", $value ));

        if( !( $ascii == 209 ||  ($ascii >= 212 && $ascii <= 218 ) || ($ascii >= 231 && $ascii <= 238 )) )
        {
            $count += 1;
        }
    }
    return $count;
}

$pdf = new FPDF('P','mm','A4');
//$pdf->SetTitle(@$row_receipt['receipt_id']);
$pase_loan_amount = 0;
$pase_pay_amount = 0;
$pase_interest_amount = 0;
$pase_deduct_amount = 0;
$pase_total = 0;
foreach ($datas as $pase => $data) {
    $y = 0;
    $pdf->AddPage();
    $pdf->AddFont('H', '', 'angsa.php');
    $pdf->AddFont('FA', '', $font);
    $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
    $pdf->AddFont('THSarabunNewB', '', 'THSarabunNew-Bold.php');
    $pdf->SetAutoPageBreak('true', 0);

    ////////////////////--------------------------------
//    $spacing = 5;
//    $pdf->SetDrawColor(204, 255, 255);
//    $pdf->SetLineWidth(0.35);
//    for ($i = 0; $i < $pdf->w; $i += $spacing) {
//        $pdf->Line($i, 0, $i, $pdf->h);
//    }
//    for ($i = 0; $i < $pdf->h; $i += $spacing) {
//        $pdf->Line(0, $i, $pdf->w, $i);
//    }
//    $pdf->SetDrawColor(0, 0, 0);
//
//    $x = $pdf->GetX();
//    $y = $pdf->GetY();
//
//    $pdf->SetFont('THSarabunNew', '', 8);
//    $pdf->SetTextColor(204, 204, 204);
//    $pdf->SetTextColor(0, 0, 0);
//    for ($i = 5; $i < ($pdf->h); $i += 5) {
//        $pdf->SetXY(1, $i - 3);
//        $pdf->Write(4, $i);
//    }
//    for ($i = 5; $i < (($pdf->w) - ($pdf->rMargin) - 5); $i += 5) {
//        $pdf->SetXY($i - 1, 1);
//        $pdf->Write(4, $i);
//    }
//    $border = 1;
//    $pdf->SetXY($x, $y);
    ////////////////////--------------------------------
    $pdf->SetFont('THSarabunNewB', '', 14);
    $y_point = 0;
    if($pase == 1) {
        $y_point += 10;
        $pdf->SetXY(45, $y_point);
        $pdf->Cell(120, 5, U2T('รายชื่อสมาชิกที่อนุมัติคำขอกู้ในเดือน' . $mFull[$_GET['approve_month']] . ' ' . ($_GET['approve_year'] + 543)), $border, 0, 'C');
        if(!empty($_GET['loan_name_all'])) {
            $y_point += 10;
            $pdf->SetXY(45, $y_point);
            $pdf->Cell(120, 5, U2T('เงินกู้ทั้งหมด'), $border, 0, 'C');
        }else{
            $txts = array();
            $Line = 0;
            foreach ($loan_name as $key => $value) {
                if($key != '0'){
                    $txts[$Line] .= ', ';
                }
                if(getStrLenTH($txts[$Line].$value['loan_name']) > 110){
                    $Line++;
                }
                $txts[$Line] .= $value['loan_name'];
            }
            foreach ($txts as $Line => $txt) {
                $y_point += 10;
                $pdf->SetXY(45, $y_point);
                $pdf->Cell(120, 5, U2T($txt), $border, 0, 'C');
            }
        }

        $y_point += 10;
        $pdf->SetXY(185, $y_point);
        $pdf->Cell(20, 5, U2T("หน่วย : บาท"), $border, 0, 'L');
    }
    $y_point += 5;
    $m = 8;
    $column1 = 10;
    $column2 = 50;
    $column3 = 23.33;
    $pdf->SetXY(5, $y_point);
    $pdf->Cell($column1, $m, U2T("ลำดับ"), 0, 0, 'C');
    $pdf->Cell($column2, $m, U2T("รายชื่อ"), 0, 0, 'C');
    $pdf->Cell($column3 * 2, $m, U2T("จำนวนเงิน"), 0, 0, 'C');
    $pdf->Cell($column3, $m, U2T("หักหนี้"), 0, 0, 'C');
    $pdf->Cell($column3, $m, U2T("ดอกเบี้ย"), 0, 0, 'C');
    $pdf->Cell($column3, $m, U2T("ประกัน"), 0, 0, 'C');
    $pdf->Cell($column3, $m, U2T("จำนวนเงินกู้"), 0, 0, 'C');
    $y_point += $m;
    $pdf->SetXY(5, $y_point);
    $pdf->Cell($column1, $m, U2T(""), 0, 0, 'C');
    $pdf->Cell($column2, $m, U2T(""), 0, 0, 'C');
    $pdf->Cell($column3, $m, U2T("ที่ขอกู้"), 1, 0, 'C');
    $pdf->Cell($column3, $m, U2T("ที่อนุมัติ"), 1, 0, 'C');
    $pdf->Cell($column3, $m, U2T("คงเหลือ"), 0, 0, 'C');
    $pdf->Cell($column3, $m, U2T("(*)"), 0, 0, 'C');
    $pdf->Cell($column3, $m, U2T(""), 0, 0, 'C');
    $pdf->Cell($column3, $m, U2T("ที่ได้รับ"), 0, 0, 'C');

    $pdf->SetXY(5, $y_point - $m);
    $pdf->Cell($column1, $m * 2, '', 1, 0, 'C');
    $pdf->Cell($column2, $m * 2, '', 1, 0, 'C');
    $pdf->Cell($column3 * 2, $m * 2, '', 1, 0, 'C');
    $pdf->Cell($column3, $m * 2, '', 1, 0, 'C');
    $pdf->Cell($column3, $m * 2, '', 1, 0, 'C');
    $pdf->Cell($column3, $m * 2, '', 1, 0, 'C');
    $pdf->Cell($column3, $m * 2, '', 1, 0, 'C');

    $pdf->SetFont('THSarabunNew', '', 14);
    $count_data = count($data);

    // ตีกรอบ
    $pdf->SetXY(5, $y_point+$m);
    $pdf->Cell($column1, $m*$count_data, '', 1, 0, 'C');
    $pdf->Cell($column2, $m*$count_data, U2T(''), 1, 0, 'L');
    $pdf->Cell($column3, $m*$count_data, U2T(''), 1, 0, 'R');
    $pdf->Cell($column3, $m*$count_data, U2T(''), 1, 0, 'R');
    $pdf->Cell($column3, $m*$count_data, U2T(''), 1, 0, 'R');
    $pdf->Cell($column3, $m*$count_data, U2T(''), 1, 0, 'R');
    $pdf->Cell($column3, $m*$count_data, U2T(''), 1, 0, 'R');
    $pdf->Cell($column3, $m*$count_data, U2T(''), 1, 0, 'R');

    foreach ($data as $key => $value) {
        $y_point += $m;
        $full_name = $value['prename_short'].$value['firstname_th'].' '.$value['lastname_th'];
        if(empty($value['loan_amount'])) { $value['loan_amount'] = 0; }
        if(empty($value['pay_amount'])) { $value['pay_amount'] = 0; }
        if(empty($value['interest_amount'])) { $value['interest_amount'] = 0; }
        if(empty($value['loan_deduct_amount'])) { $value['loan_deduct_amount'] = 0; }
        $total = $value['loan_amount'] - $value['pay_amount'] - $value['interest_amount'];
        $pase_loan_amount += $value['loan_amount'];
        $pase_pay_amount += $value['pay_amount'];
        $pase_interest_amount += $value['interest_amount'];
        $pase_deduct_amount += $value['loan_deduct_amount'];
        $pase_total += $total;

        if(empty($value['loan_amount'])) {
            $value['loan_amount'] = '-';
        }else{
            $value['loan_amount'] = number_format($value['loan_amount'],2);
        }
        if(empty($value['pay_amount'])) {
            $value['pay_amount'] = '-';
        }else{
            $value['pay_amount'] = number_format($value['pay_amount'],2);
        }
        if(empty($value['interest_amount'])) {
            $value['interest_amount'] = '-';
        }else{
            $value['interest_amount'] = number_format($value['interest_amount'],2);
        }
        if(empty($value['loan_deduct_amount'])) {
            $value['loan_deduct_amount'] = '-';
        }else{
            $value['loan_deduct_amount'] = number_format($value['loan_deduct_amount'],2);
        }

        $pdf->SetXY(5, $y_point);
        $pdf->Cell($column1, $m, $key + 1, 0, 0, 'C');
        $pdf->Cell($column2, $m, U2T($full_name.' '.$value['loan_type']), 0, 0, 'L');
        $pdf->Cell($column3, $m, U2T($value['loan_amount']), 0, 0, 'R');
        $pdf->Cell($column3, $m, U2T($value['loan_amount']), 0, 0, 'R');
        $pdf->Cell($column3, $m, U2T($value['pay_amount']), 0, 0, 'R');
        $pdf->Cell($column3, $m, U2T($value['interest_amount']), 0, 0, 'R');
        $pdf->Cell($column3, $m, U2T($value['loan_deduct_amount']), 0, 0, 'R');
        $pdf->Cell($column3, $m, U2T(number_format($total,2)), 0, 0, 'R');
    }
    if($pase == $last_pase){
        $pdf->SetFont('THSarabunNewB', '', 14);
        $y_point += $m;
        $pdf->SetXY(5, $y_point);
        $pdf->Cell($column1, $m, '', 1, 0, 'C');
        $pdf->Cell($column2, $m, U2T(''), 1, 0, 'L');
        $pdf->Cell($column3, $m, U2T(number_format($pase_loan_amount, 2)), 1, 0, 'R');
        $pdf->Cell($column3, $m, U2T(number_format($pase_loan_amount, 2)), 1, 0, 'R');
        $pdf->Cell($column3, $m, U2T(number_format($pase_pay_amount, 2)), 1, 0, 'R');
        $pdf->Cell($column3, $m, U2T(number_format($pase_interest_amount, 2)), 1, 0, 'R');
        $pdf->Cell($column3, $m, U2T(number_format($pase_deduct_amount,2)), 1, 0, 'R');
        $pdf->Cell($column3, $m, U2T(number_format($pase_total, 2)), 1, 0, 'R');
    }

}
//exit;

$pdf->Output();
?>
