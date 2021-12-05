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
$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$pdf->AddFont('THSarabunNew-Bold', '', 'THSarabunNew-Bold.php');

foreach($datas as $account_id => $account) {
    $pdf->AddPage();

    $pdf->SetFont('THSarabunNew', '', 13 );
    $pdf->SetMargins(0, 0, 0);
    $border = 0;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetAutoPageBreak(true,0);

    //Set data
    $full_w = 178;
    $y_point = 16;

    //Title
    $pdf->SetXY( 16, $y_point);
    $pdf->SetTextColor(255, 26, 26);
    $pdf->SetFont('THSarabunNew-Bold', '', 20 );
    $pdf->MultiCell($full_w, 8, U2T("ใบสำคัญบันทึกบัญชี"), 0, 1);

    //Change font style to common
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('THSarabunNew', '', 14 );

    //Top Title
    $y_point += 12;
    $pdf->SetXY( 16, $y_point);
    $pdf->setFillColor(255, 221, 153);
    $pdf->MultiCell(35.6, 8, U2T("วันที่"),1,1,'L',1);
    $pdf->SetXY( 51.6, $y_point);
    $pdf->setFillColor(230, 242, 255);
    $pdf->MultiCell(142.4, 8, U2T($this->center_function->ConvertToThaiDate($account['datetime'],'1','0')),1,1,'L',1);
    $y_point += 8;
    $pdf->SetXY( 16, $y_point);
    $pdf->setFillColor(255, 221, 153);
    $pdf->MultiCell(35.6, 8, U2T("เลขที่รายการ"),1,1,'L',1);
    $pdf->SetXY( 51.6, $y_point);
    $pdf->setFillColor(230, 242, 255);
    $pdf->MultiCell(142.4, 8, $account['journal_ref'],1,1,'L',1);
    // $y_point += 8;
    // $pdf->SetXY( 16, $y_point);
    // $pdf->setFillColor(255, 221, 153);
    // $pdf->MultiCell(35.6, 8, U2T("อ้างอิง"),1,1,'L',1);
    // $pdf->SetXY( 51.6, $y_point);
    // $pdf->setFillColor(230, 242, 255);
    // $pdf->MultiCell(142.4, 8, "",1,1,'L',1);
    $y_point += 8;
    $pdf->SetXY(300, $y_point);
    $pdf->MultiCell(142.4, 8, U2T(''),1,1,'L',1);
    $y = $pdf->GetY();
    $h = $y-$y_point;
    $pdf->SetXY( 16, $y_point);
    $pdf->setFillColor(255, 221, 153);
    $pdf->MultiCell(35.6, $h, "",1,1,'L',1);
    $pdf->SetXY( 16, $y_point);
    $pdf->Cell(35.6,8, U2T("คำอธิบาย"),0,0,'L');
    $pdf->SetXY( 51.6, $y_point);
    $pdf->setFillColor(230, 242, 255);
    $pdf->MultiCell(142.4, 8, U2T($account['description']),1,1,'L',1);

    $y = $pdf->GetY();
    //Detail table
    //table header
    $x_1 = 16;
    $w_1 = 20;
    $x_2 = $x_1 + $w_1;
    $w_2 = 86.8;
    $x_3 = $x_2 + $w_2;
    $w_3 = 35.6;
    $x_4 = $x_3 + $w_3;
    $w_4 = 35.6;
    $y_point += $h+4;
    $pdf->setFillColor(255, 221, 153);
    $pdf->SetXY( $x_1, $y_point);
    $pdf->MultiCell($w_1, 8, U2T("เลขที่บัญชี"),'TLB','C',1,1);
    $pdf->SetXY( $x_2, $y_point);
    $pdf->MultiCell($w_2, 8, U2T("ชื่อบัญชี"),'TLB','C',1,1);
    $pdf->SetXY( $x_3, $y_point);
    $pdf->MultiCell($w_3, 8, U2T("DR."),'TLB','C',1,1);
    $pdf->SetXY( $x_4, $y_point);
    $pdf->MultiCell($w_4, 8, U2T("CR."),'TLRB','C',1,1);
    //Detail Row
    $h = 8;
    $pdf->setFillColor(255, 255, 255);
    $total = 0;
    foreach($account['datas'] as $value) {
        $y_point += $h;
        $pdf->SetXY($x_2, $y_point);
        $pdf->MultiCell($w_2, 8, U2T($value["account_chart"]), 0,'L',1,1);
        $y = $pdf->GetY();
        $h = $y-$y_point;

        $pdf->SetXY( $x_1, $y_point);
        $pdf->MultiCell($w_1, $h, "", 'L','L',1,0);
        $pdf->SetXY( $x_1, $y_point);
        $pdf->Cell($w_1,8,U2T($value["account_chart_id"]),0,0,'C');
        $pdf->SetXY( $x_2, $y_point);
        $pdf->MultiCell($w_2, 8, "", 'L','L',0,0);
        $pdf->SetXY( $x_3, $y_point);
        $pdf->MultiCell($w_3, $h, "", 'L','R',1,1);
        $pdf->SetXY( $x_3, $y_point);
        $pdf->Cell($w_3,8, $value["account_type"] == "debit" ? number_format($value["account_amount"],2) : "",0,0,'R');
        $pdf->SetXY( $x_4, $y_point);
        $pdf->MultiCell($w_4, $h, "", 'LR','R',1,1);
        $pdf->SetXY( $x_4, $y_point);
        $pdf->Cell($w_4,8, $value["account_type"] == "credit" ? number_format($value["account_amount"],2) : "",0,0,'R');
        $total += $value["account_type"] == "credit" ? $value["account_amount"] : 0;
    }
    //Space before table footer
    $y_point += $h;
    $space_size = 240-$y_point;
    $pdf->SetXY( $x_1, $y_point);
    $pdf->MultiCell($w_1, $space_size, "", 'LB','L',1,1);
    $pdf->SetXY( $x_2, $y_point);
    $pdf->MultiCell($w_2, $space_size, "", 'LB','L',1,1);
    $pdf->SetXY( $x_3, $y_point);
    $pdf->MultiCell($w_3, $space_size, "", 'LB','R',1,1);
    $pdf->SetXY( $x_4, $y_point);
    $pdf->MultiCell($w_4, $space_size,  "", 'LRB','R',1,1);
    //table footer
    $y_point += $space_size;
    $pdf->SetXY(300, $y_point);
    $pdf->MultiCell(71.2, 8, U2T($this->center_function->convert(number_format($total,2,'.',''))),1,'L',1,1);
    $y = $pdf->GetY();
    $h = $y-$y_point;
    $pdf->SetXY( $x_1, $y_point);
    $pdf->MultiCell($w_1, $h, U2T("รวมยอด"), 1,'C',1,0);
    $pdf->SetFont('THSarabunNew', '', 13);
    $pdf->SetXY( $x_2, $y_point);
    $pdf->MultiCell($w_2, $h, U2T($this->center_function->convert(number_format($total,2,'.',''))), 1,'C',1,1);
    $pdf->SetFont('THSarabunNew', '', 14);
    $pdf->SetXY( $x_3, $y_point);
    $pdf->MultiCell($w_3, $h, !empty($total) ? number_format($total,2) : "-", 1,'R',1,1);
    $pdf->SetXY( $x_4, $y_point);
    $pdf->MultiCell($w_4, $h, !empty($total) ? number_format($total,2) : "-", 1,'R',1,1);

    //Signature
    $y_point += $h+8;
    $pdf->SetXY( 16, $y_point);
    $pdf->MultiCell($full_w, 8, U2T("ลงชื่อ..............................................ผู้ทำบัญชี  ลงชื่อ..............................................ตรวจสอบ  ลงชื่อ..............................................ผู้อนุมัติ"), 0,'C',1,0);
    $pdf->SetFont('THSarabunNew', '', 12.5 );
    $pdf->SetXY( 27, $y_point-0.5);
    $pdf->Cell(36, 8, U2T($account['user_name']), 0, 0, 'C');
    $pdf->SetFont('THSarabunNew', '', 13 );
    $y_point += 8;
    $pdf->SetXY( 16, $y_point);
    $pdf->MultiCell($full_w, 8, U2T("    วันที่..................................................          วันที่..................................................            วันที่..................................................       "), 0,'C',1,0);
    $pdf->SetFont('THSarabunNew', '', 12.5 );
    $pdf->SetXY( 35, $y_point-0.5);
    $pdf->Cell(36, 8, U2T($this->center_function->ConvertToThaiDate($account['datetime'],'1','0')), 0, 0, 'C');
    $pdf->SetFont('THSarabunNew', '', 13 );
}
$pdf->Output();
