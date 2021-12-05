<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

$pdf = new FPDI('P','mm', array(210, 297));
$fort_size = 18;
//Set data
$full_w = 178;
$y_point = 10;
$l_height = 9;
$index = 0;
foreach($datas as $data) {
    $index++;

    if($index % 2 == 1) {
        $pdf->AddPage();
        $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
        $pdf->AddFont('THSarabunNew-Bold', '', 'THSarabunNew-Bold.php');

        $pdf->SetFont('THSarabunNew', '', 13 );
        $pdf->SetMargins(0, 0, 0);
        $border = 0;
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetAutoPageBreak(true,0);

        $y_point = 8;
    } else {
        $y_point = 150;
    }

    //Title
    $pdf->setFillColor(255,255,255);
    if($data["journal_type"] == "PV" || ($data["journal_type"] == "JV" && $data["account_type"] == "debit")) {
        $pdf->SetTextColor(255, 26, 26);
    } else {
        $pdf->SetTextColor(18, 127, 5);
    }

    $pdf->SetFont('THSarabunNew-Bold', '', $fort_size);
    $pdf->SetXY( 16, $y_point);
    $pdf->MultiCell($full_w, 8, U2T($_SESSION['COOP_NAME']),0,'C',1);
    $pdf->SetFont('THSarabunNew-Bold', '', $fort_size);

    $y_point += 8;
    $pdf->SetXY( 16, $y_point);
    $pdf->MultiCell($full_w, 8, U2T("ใบสำคัญการบันทึกบัญชี"),0,'C',1);

    $pdf->SetFont('THSarabunNew', '', $fort_size);
    $pdf->SetXY(146, $y_point);
    $pdf->Cell(35.6,8, U2T("เลขที่ "),0,0,'L');
    $pdf->SetXY(153, $y_point+1);
    $pdf->Cell(35.6,8, "...........................",0,0,'L');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(155, $y_point);
    $pdf->Cell(35.6,8, U2T($data["journal_ref"]),0,0,'L');

    $y_point += 10;
    if($data["journal_type"] == "PV" || ($data["journal_type"] == "JV" && $data["account_type"] == "debit")) {
        $pdf->SetTextColor(255, 26, 26);
    } else {
        $pdf->SetTextColor(18, 127, 5);
    }
    $pdf->SetXY(110, $y_point);
    $pdf->MultiCell($full_w, 8, U2T("วันที่ ................................................................"),0,'L',1);
    $pdf->SetXY(120, $y_point-1);
    $pdf->SetTextColor(0, 0, 0);
    $be_year = $year + 543;
    $pdf->MultiCell($full_w, 8, U2T($this->center_function->ConvertToThaiDate($data["account_datetime"],'1','0')),0,'L',0);

    $y_point += 10;
    $pdf->SetFont('THSarabunNew-Bold', '', $fort_size);
    if($data["journal_type"] == "PV" || ($data["journal_type"] == "JV" && $data["account_type"] == "debit")) {
        $pdf->SetTextColor(255, 26, 26);
    } else {
        $pdf->SetTextColor(18, 127, 5);
    }
    $pdf->SetXY(35, $y_point-1);
    if($data["journal_type"] == "PV" || ($data["journal_type"] == "JV" && $data["account_type"] == "debit")) {
        $identText = "ลูกหนี้";
        if($data["journal_type"] == "JV") $identText .= "-รายการโอน";
        $pdf->MultiCell($full_w, 8, U2T($identText),0,'L',1);
    } else {
        $identText = "เจ้าหนี้";
        if($data["journal_type"] == "JV") $identText .= "-รายการโอน";
        $pdf->MultiCell($full_w, 8, U2T($identText),0,'L',1);
    }
    $pdf->SetFont('THSarabunNew', '', $fort_size);
    if($data["journal_type"] == "JV") {
        $pdf->SetXY(68, $y_point);
        $pdf->Cell(50,8, ".................................................................................................................",0,0,'L');
        $pdf->SetXY(68, $y_point-1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(50,8, U2T($data["account_chart_id"]." ".$data["account_chart"]),0,0,'L');
    } else {
        $pdf->SetXY(48, $y_point);
        $pdf->Cell(40,8, "....................................................................................................................................",0,0,'L');
        $pdf->SetXY(48, $y_point-1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(40,8, U2T($data["account_chart_id"]." ".$data["account_chart"]),0,0,'L');
    }

    if($data["journal_type"] == "PV" || ($data["journal_type"] == "JV" && $data["account_type"] == "debit")) {
        $pdf->SetDrawColor(255, 26, 26);
    } else {
        $pdf->SetDrawColor(18, 127, 5);
    }
    $y_point += $l_height;
    $col_1_x = 20;
    $col_1_w = 125;
    $col_2_x = 145;
    $col_2_w = 30;
    $col_3_x = 175;
    $col_3_w = 10;
    $pdf->SetXY($col_1_x, $y_point);
    // $pdf->MultiCell(90, 8, U2T($data["description"]),1,'L',0);
    $pdf->MultiCell($col_1_w, $l_height, U2T(""),1,'L',0);
    $pdf->SetXY($col_2_x, $y_point);
    $account_amount_arr = explode( '.', $data['account_amount']);
    // $amount_num = $account_amount_arr[0];
    $amount_num = !empty($account_amount_arr[0]) ? $account_amount_arr[0] : 0;
    $decimal = !empty($account_amount_arr[1]) ? $account_amount_arr[1] : 0;
    $pdf->MultiCell($col_2_w, $l_height, number_format($amount_num),1,'R',0);
    $pdf->SetXY($col_3_x, $y_point);
    $pdf->MultiCell($col_3_w, $l_height, !empty($decimal) ? $decimal : "00",1,'R',0);

    $y_point += $l_height;
    $pdf->SetXY($col_1_x, $y_point);
    $pdf->MultiCell($col_1_w, $l_height, "",1,'L',0);
    $pdf->SetXY($col_2_x, $y_point);
    $pdf->MultiCell($col_2_w, $l_height, "",1,'L',0);
    $pdf->SetXY($col_3_x, $y_point);
    $pdf->MultiCell($col_3_w, $l_height, "",1,'L',0);

    $y_point += $l_height;
    $pdf->SetXY($col_1_x, $y_point);
    $pdf->MultiCell($col_1_w, $l_height, "",1,'L',0);
    $pdf->SetXY($col_2_x, $y_point);
    $pdf->MultiCell($col_2_w, $l_height, "",1,'L',0);
    $pdf->SetXY($col_3_x, $y_point);
    $pdf->MultiCell($col_3_w, $l_height, "",1,'L',0);

    $y_point += $l_height;
    $pdf->SetXY($col_1_x, $y_point);
    $pdf->MultiCell($col_1_w, $l_height, "",1,'L',0);
    $pdf->SetXY($col_2_x, $y_point);
    $pdf->MultiCell($col_2_w, $l_height, "",1,'L',0);
    $pdf->SetXY($col_3_x, $y_point);
    $pdf->MultiCell($col_3_w, $l_height, "",1,'L',0);

    $y_point += $l_height;
    $pdf->SetXY($col_1_x, $y_point);
    $pdf->MultiCell($col_1_w, $l_height, "",1,'L',0);
    $pdf->SetXY($col_2_x, $y_point);
    $pdf->MultiCell($col_2_w, $l_height, "",1,'L',0);
    $pdf->SetXY($col_3_x, $y_point);
    $pdf->MultiCell($col_3_w, $l_height, "",1,'L',0);

    $y_point += $l_height;
    $pdf->SetXY($col_1_x, $y_point);
    $pdf->MultiCell($col_1_w, $l_height, "",1,'L',0);
    $pdf->SetXY($col_2_x, $y_point);
    $pdf->MultiCell($col_2_w, $l_height, "",1,'L',0);
    $pdf->SetXY($col_3_x, $y_point);
    $pdf->MultiCell($col_3_w, $l_height, "",1,'L',0);

    $y_point += $l_height;
    $pdf->SetFont('THSarabunNew-Bold', '', $fort_size);
    $pdf->SetXY($col_1_x, $y_point);
    if($data["journal_type"] == "PV" || ($data["journal_type"] == "JV" && $data["account_type"] == "debit")) {
        $pdf->SetTextColor(255, 26, 26);
    } else {
        $pdf->SetTextColor(18, 127, 5);
    }
    $pdf->MultiCell($col_1_w, $l_height, U2T("รวมทั้งสิ้น"),0,'R',0);
    $pdf->SetFont('THSarabunNew', '', $fort_size);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY($col_2_x, $y_point);
    $pdf->MultiCell($col_2_w, $l_height, number_format($amount_num),1,'R',0);
    $pdf->SetXY($col_3_x, $y_point);
    $pdf->MultiCell($col_3_w, $l_height, !empty($decimal) ? $decimal : "00",1,'R',0);

    $y_point += $l_height;
    $pdf->SetFont('THSarabunNew', '', $fort_size);
    if($data["journal_type"] == "PV" || ($data["journal_type"] == "JV" && $data["account_type"] == "debit")) {
        $pdf->SetTextColor(255, 26, 26);
    } else {
        $pdf->SetTextColor(18, 127, 5);
    }
    $pdf->SetXY(20, $y_point+1);
    $pdf->MultiCell($full_w  , $l_height, "..............................................................................................................................................................",0,'L',0);

    $pdf->SetXY(20, $y_point);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Multicell($full_w - 30, $l_height, U2T("( ".$this->center_function->convert($data['account_amount'])." )"), 0, "C", 0);

    $y_point += $l_height;
    if($data["journal_type"] == "PV" || ($data["journal_type"] == "JV" && $data["account_type"] == "debit")) {
        $pdf->SetTextColor(255, 26, 26);
    } else {
        $pdf->SetTextColor(18, 127, 5);
    }
    $pdf->SetXY(20, $y_point+1);
    $pdf->MultiCell(80, $l_height, "...................................................",0,"L",0);
    $pdf->SetXY(20, $y_point);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Multicell(54, $l_height, U2T($data['user_name']), 0, "C", 0);

    if($data["journal_type"] == "PV" || ($data["journal_type"] == "JV" && $data["account_type"] == "debit")) {
        $pdf->SetTextColor(255, 26, 26);
    } else {
        $pdf->SetTextColor(18, 127, 5);
    }
    $pdf->SetXY(75, $y_point+1);
    $pdf->MultiCell(80, $l_height, "...................................................",0,"L",0);
    $pdf->SetXY(100, $y_point);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Multicell(51, $l_height, "", 0, "C", 0);

    if($data["journal_type"] == "PV" || ($data["journal_type"] == "JV" && $data["account_type"] == "debit")) {
        $pdf->SetTextColor(255, 26, 26);
    } else {
        $pdf->SetTextColor(18, 127, 5);
    }
    $pdf->SetXY(130, $y_point+1);
    $pdf->MultiCell(80, $l_height, "...................................................",0,"L",0);
    $pdf->SetXY(100, $y_point);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Multicell(51, $l_height, "", 0, "C", 0);

    $y_point += $l_height;
    if($data["journal_type"] == "PV" || ($data["journal_type"] == "JV" && $data["account_type"] == "debit")) {
        $pdf->SetTextColor(255, 26, 26);
    } else {
        $pdf->SetTextColor(18, 127, 5);
    }
    $pdf->SetFont('THSarabunNew-Bold', '', $fort_size);
    $pdf->SetXY(35, $y_point);
    $pdf->MultiCell(80, $l_height, U2T("ผู้บันทึก"),0,"L",0);
    $pdf->SetXY(95, $y_point);
    $pdf->MultiCell(80, $l_height, U2T("ผู้ทำ"),0,"L",0);
    $pdf->SetXY(150, $y_point);
    $pdf->MultiCell(80, $l_height, U2T("ผู้ตรวจ"),0,"L",0);
}
$pdf->Output();