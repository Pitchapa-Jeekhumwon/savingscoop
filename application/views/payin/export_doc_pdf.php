<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

$pdf = new FPDI('P','mm', "A4");
$pdf->AddFont('common', '', 'UpBean Regular Ver 1.00.php');
$pdf->AddFont('bold','','UpBean Bold Ver 1.00.php');
$font_size = 10;
$line_height = 8;
$row_height = 10;

if($type == 1) {
    $col_x_1 = 10;
    $col_w_1 = 10;
    $col_x_2 = $col_w_1 + $col_x_1;
    $col_w_2 = 30;
    $col_x_3 = $col_w_2 + $col_x_2;
    $col_w_3 = 20;
    $col_x_4 = $col_w_3 + $col_x_3;
    $col_w_4 = 50;
    $col_x_5 = $col_w_4 + $col_x_4;
    $col_w_5 = 18;
    $col_x_6 = $col_w_5 + $col_x_5;
    $col_w_6 = 37;
    $col_x_7 = $col_w_6 + $col_x_6;
    $col_w_7 = 25;
} else {
    $col_x_1 = 10;
    $col_w_1 = 10;
    $col_x_2 = $col_w_1 + $col_x_1;
    $col_w_2 = 30;
    $col_x_3 = $col_w_2 + $col_x_2;
    $col_w_3 = 20;
    $col_x_4 = $col_w_3 + $col_x_3;
    $col_w_4 = 45;
    $col_x_5 = $col_w_4 + $col_x_4;
    $col_w_5 = 18;
    $col_x_6 = $col_w_5 + $col_x_5;
    $col_w_6 = 25;
    $col_x_7 = $col_w_6 + $col_x_6;
    $col_w_7 = 20;
    $col_x_8 = $col_w_7 + $col_x_7;
    $col_w_8 = 22;
}


$y_point = 0;
$page_index = 0;
$index = 0;
foreach($datas as $data) {
    if($y_point > 260 || $y_point == 0) {
        $pdf->AddPage();
        $page_index++;

        $pdf->SetMargins(0, 0, 0);
        $border = 0;
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetAutoPageBreak(true,0);

        $y_point = 35;
        $pdf->SetFont('common', '', 12);
        $pdf->SetXY( 10, $y_point - 4 );
        $pdf->MultiCell(190, 10, U2T("หน้าที่ ".$page_index), 0, "R");

        $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_profile/'.$_SESSION['COOP_IMG'],92,10,-4000);

        $pdf->SetFont('bold', '', 18 );
        $pdf->SetXY( 0, $y_point );
        $pdf->MultiCell(210, 10, U2T($_SESSION['COOP_NAME']), 0, "C");

        $title =  $type == 0 ? "ซื้อหุ้น" : ($type == 1 ? "ชำระหนี้" : ($type == 2 ? "ฝากเงิน" : "ไม่พบข้อมูล"));
        $pdf->SetFont('bold', '', 12 );
        $y_point += 10;
        $pdf->SetXY( 0, $y_point );
        $pdf->MultiCell(210, 8, U2T("รายงาน".$title), 0, "C");

        $y_point += 8;
        $pdf->SetXY( 0, $y_point );
        $pdf->MultiCell(210, 8, U2T("KTB ".$this->center_function->ConvertToThaiDate($import_at,'1','0')), 0, "C");

        $y_point += 10;
        $pdf->setFillColor(238,238,238); 
        $pdf->SetXY($col_x_1, $y_point);
        $pdf->MultiCell($col_w_1, $row_height, U2T("#"), 1, "C",1);
        $pdf->SetXY($col_x_2, $y_point);
        $pdf->MultiCell($col_w_2, $row_height, U2T("วันที่เวลา"), 1, "C",1);
        $pdf->SetXY($col_x_3, $y_point);
        $pdf->MultiCell($col_w_3, $row_height, U2T("รหัสสมาชิก"), 1, "C",1);
        $pdf->SetXY($col_x_4, $y_point);
        $pdf->MultiCell($col_w_4, $row_height, U2T("ชื่อสมาชิก"), 1, "C",1);
        $pdf->SetXY($col_x_5, $y_point);
        $pdf->MultiCell($col_w_5, $row_height, U2T("REF2"), 1, "C",1);
        $pdf->SetXY($col_x_6, $y_point);
        $pdf->MultiCell($col_w_6, $row_height, U2T("รายละเอียด"), 1, "C",1);
        $pdf->SetXY($col_x_7, $y_point);
        $pdf->MultiCell($col_w_7, $row_height, U2T("ยอดเงิน"), 1, "C",1);
        if($type != 1) {
            $pdf->SetXY($col_x_8, $y_point);
            $pdf->MultiCell($col_w_8, $row_height, U2T("สถานะ"), 1, "C",1);
        }

        $y_point += $row_height;
    }

    $pdf->SetFont('common', '', $font_size);

    $pdf->SetXY($col_x_1, $y_point);
    $pdf->MultiCell($col_w_1, $line_height, ++$index, 0, "C");
    $h = $pdf->GetY();

    $paydateText = "-";
    $pdf->SetXY($col_x_2, $y_point);
    if(!empty($data['payment_date'])) {
        $paydate = substr($data['payment_date'], 4, 4)."-".substr($data['payment_date'], 2, 2)."-".substr($data['payment_date'], 0, 2);
        if(!empty($data['payment_time'])) {
            $paydate .= " ".substr($data['payment_time'], 0, 2).":".substr($data['payment_time'], 2, 2).":".substr($data['payment_time'], 4, 2);
        }
        $paydateText =  $this->center_function->ConvertToThaiDate($paydate,'1','1');
    }
    $pdf->MultiCell($col_w_2, $line_height, U2T($paydateText), 0, "C");

    $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
    $pdf->SetXY($col_x_3, $y_point);
    $pdf->MultiCell($col_w_3, $line_height, U2T(!empty($data['member_id']) ? $data['member_id'] : "-"), 0, "C");
    $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
    $pdf->SetXY($col_x_4, $y_point);
    $pdf->MultiCell($col_w_4, $line_height, U2T(!empty($data['firstname_th']) ? $data['prename_short'].$data['firstname_th']." ".$data['lastname_th'] : "-"), 0, "L");
    $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
    $pdf->SetXY($col_x_5, $y_point);
    $pdf->MultiCell($col_w_5, $line_height, U2T(!empty($data['ref2']) ? $data['ref2'] : "-"), 0, "C");
    $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;

    $detail = "-";
    if($type == 0) {
        $detail = "ซื้อหุ้น";
    } else if ($type == 1) {
        $txt = "";
        foreach($data['contract_number'] as $contract_number) {
            $txt .= $txt == "" ? $contract_number : ", ".$contract_number;
        }
        $detail = "ชำระหนี้ สัญญา ".$txt;
    } else if ($type == 2) {
        $txt = "";
        $acc_nos = "";
        foreach($data['deptaccount_no'] as $deptaccount_no) {
            $txt .= $txt == "" ? $deptaccount_no : ", ".$deptaccount_no;
            $acc_nos .= $acc_nos == "" ? $deptaccount_no : ",".$deptaccount_no;
        }
        $detail = "เงินฝาก ".$txt;
    }
    $pdf->SetXY($col_x_6, $y_point);
    $pdf->MultiCell($col_w_6, $line_height, U2T($detail), 0, "L");
    $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
    $pdf->SetXY($col_x_7, $y_point);
    $pdf->MultiCell($col_w_7, $line_height, !empty($data['amount']) ? number_format($data['amount'], 2) : "-", 0, "R");
    $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;

    if ($type != 1) {
        $pdf->SetXY($col_x_8, $y_point);
        $pdf->MultiCell($col_w_8, $line_height, U2T($type == 4 ? "ไม่พบข้อมูล" : ($data['status'] == 1 ? "รอดำเนินการ" : "ดำเนินการแล้ว")), 0, "C");
        $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
    }

    $row_height = $h - $y_point;
    $pdf->SetXY($col_x_1, $y_point);
    $pdf->MultiCell($col_w_1, $row_height, "", 1, "C");
    $pdf->SetXY($col_x_2, $y_point);
    $pdf->MultiCell($col_w_2, $row_height, "", 1, "C");
    $pdf->SetXY($col_x_3, $y_point);
    $pdf->MultiCell($col_w_3, $row_height, "", 1, "L");
    $pdf->SetXY($col_x_4, $y_point);
    $pdf->MultiCell($col_w_4, $row_height, "", 1, "R");
    $pdf->SetXY($col_x_5, $y_point);
    $pdf->MultiCell($col_w_5, $row_height, "", 1, "R");
    $pdf->SetXY($col_x_6, $y_point);
    $pdf->MultiCell($col_w_6, $row_height, "", 1, "R");
    $pdf->SetXY($col_x_7, $y_point);
    $pdf->MultiCell($col_w_7, $row_height, "", 1, "R");
    if ($type != 1) {
        $pdf->SetXY($col_x_8, $y_point);
        $pdf->MultiCell($col_w_8, $row_height, "", 1, "R");
    }
    $y_point = $h;
}

$pdf->Output();