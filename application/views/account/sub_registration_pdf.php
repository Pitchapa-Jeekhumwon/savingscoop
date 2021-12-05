<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

$pdf = new FPDI('L','mm', array(177, 129));

foreach($charts as $chart) {
    if(!empty($chart_balances[$chart["account_chart_id"]])) {
        foreach($chart_balances[$chart["account_chart_id"]] as $type => $amount) {
            $pdf->AddPage();
            $data = 
            $pdf->AddFont('common', '', 'THSarabunNew.php');
            $pdf->AddFont('bold', '', 'THSarabunNew-Bold.php');
        
            $pdf->SetFont('common', '', 13 );
            $pdf->SetMargins(0, 0, 0);
            $border = 0;
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetAutoPageBreak(true,0);
        
            //Set data
            $full_w = 177;
            $y_point = 10;
        
            //Title
            $pdf->setFillColor(255,255,255);
            if($type == "PV") {
                $pdf->SetTextColor(255, 26, 26);
            } else {
                $pdf->SetTextColor(18, 127, 5);
            }
        
            $pdf->SetFont('common', '', 16);
            $pdf->SetXY( 0, $y_point);
            $pdf->MultiCell($full_w, 8, U2T($_SESSION['COOP_NAME']),0,'C',1);

            $y_point += 10;
            $pdf->SetXY( 10, $y_point);
            $pdf->MultiCell($full_w - 20, 8, U2T("วันที่ ".$date." ".$this->month_arr[$month]." ".$year),0,'R',1);

            $y_point += 10;
            if($type == "PV") {
                $pdf->SetXY( 10, $y_point);
                $pdf->MultiCell($full_w - 20, 8, U2T("ลูกหนี้"),0,'L',1);

                $pdf->SetFont('common', 'U', 16);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY( 19.5, $y_point);
                $pdf->MultiCell($full_w - 20, 8, U2T("    ".$chart["account_chart"]),0,'L',0);
            } else {
                $pdf->SetXY( 10, $y_point);
                $pdf->MultiCell($full_w - 20, 8, U2T("เจ้าหนี้"),0,'L',1);

                $pdf->SetFont('common', 'U', 16);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY( 20, $y_point);
                $pdf->MultiCell($full_w - 20, 8, U2T("    ".$chart["account_chart"]),0,'L',0);
            }

            $y_point += 10;
            if($type == "PV") {
                $pdf->SetTextColor(255, 26, 26);
                $pdf->SetDrawColor(255, 26, 26);
            } else {
                $pdf->SetTextColor(18, 127, 5);
                $pdf->SetDrawColor(18, 127, 5);
            }
            $pdf->Line(10, $y_point, 167, $y_point);

            $y_point += 1;
            $pdf->SetXY( 10, $y_point);
            $pdf->MultiCell(90, 8, U2T(""),'TR','L',0);
            $pdf->SetXY( 101, $y_point);
            $pdf->MultiCell(45, 8, U2T(""),'TLR','L',0);
            $pdf->SetXY( 146, $y_point);
            $pdf->MultiCell(21, 8, U2T(""),'T','L',0);

            $pdf->SetFont('common', '', 16);
            $pdf->SetTextColor(0, 0, 0);
            $number = explode('.', number_format($amount,2));
            $pdf->SetXY( 101, $y_point);
            $pdf->MultiCell(45, 8, $number[0], 0,'R',0);
            $pdf->SetXY( 146, $y_point);
            $pdf->MultiCell(21, 8, $number[1], 0,'R',0);

            if($type == "PV") {
                $pdf->SetTextColor(255, 26, 26);
                $pdf->SetDrawColor(255, 26, 26);
            } else {
                $pdf->SetTextColor(18, 127, 5);
                $pdf->SetDrawColor(18, 127, 5);
            }

            $y_point += 8;
            $pdf->SetXY( 10, $y_point);
            $pdf->MultiCell(90, 8, U2T(""),'TR','L',0);
            $pdf->SetXY( 101, $y_point);
            $pdf->MultiCell(45, 8, U2T(""),'TLR','L',0);
            $pdf->SetXY( 146, $y_point);
            $pdf->MultiCell(21, 8, U2T(""),'T','L',0);
            $y_point += 8;
            $pdf->SetXY( 10, $y_point);
            $pdf->MultiCell(90, 8, U2T(""),'TR','L',0);
            $pdf->SetXY( 101, $y_point);
            $pdf->MultiCell(45, 8, U2T(""),'TLR','L',0);
            $pdf->SetXY( 146, $y_point);
            $pdf->MultiCell(21, 8, U2T(""),'T','L',0);
            $y_point += 8;
            $pdf->SetXY( 10, $y_point);
            $pdf->MultiCell(90, 8, U2T(""),'TR','L',0);
            $pdf->SetXY( 101, $y_point);
            $pdf->MultiCell(45, 8, U2T(""),'TLR','L',0);
            $pdf->SetXY( 146, $y_point);
            $pdf->MultiCell(21, 8, U2T(""),'T','L',0);
            $y_point += 8;

            $pdf->SetXY( 10, $y_point);
            $pdf->MultiCell(90, 8, U2T("บาท"), 0,'L',0);
            $pdf->SetXY( 10, $y_point);
            $pdf->MultiCell(90, 8, U2T("รวม"), 0,'R',0);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY( 20, $y_point);
            $pdf->MultiCell(70, 8, U2T($this->center_function->convert($amount)), 0,'L',0);
            $h = $pdf->GetY();
            $pdf->SetXY( 101, $y_point);
            $pdf->MultiCell(45, 8, $number[0], 0,'R',0);
            $pdf->SetXY( 146, $y_point);
            $pdf->MultiCell(21, 8, $number[1], 0,'R',0);

            $pdf->SetXY( 10, $y_point);
            $pdf->MultiCell(90, $h - $y_point, U2T(""),'TBR','L',0);
            $pdf->SetXY( 101, $y_point);
            $pdf->MultiCell(45, $h - $y_point, U2T(""),'TBLR','L',0);
            $pdf->SetXY( 146, $y_point);
            $pdf->MultiCell(21, $h - $y_point, U2T(""),'TB','L',0);

            $y_point += 35;
            if($type == "PV") {
                $pdf->SetTextColor(255, 26, 26);
            } else {
                $pdf->SetTextColor(18, 127, 5);
            }
            $pdf->SetXY(20, $y_point+1);
            $pdf->MultiCell(80, 8, U2T("ผู้จัดทำ  .................................................."),0,"L",0);
            $pdf->SetXY(20, $y_point);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Multicell(51, 8, U2T($data['user_name']), 0, "C", 0);

            $pdf->SetXY(90, $y_point+1);
            if($type == "PV") {
                $pdf->SetTextColor(255, 26, 26);
            } else {
                $pdf->SetTextColor(18, 127, 5);
            }
            $pdf->MultiCell(80, 8, U2T("ผู้ตรวจ  .................................................."),0,"L",0);
            $pdf->SetXY(100, $y_point);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Multicell(51, 8, "", 0, "C", 0);
        }
    }
}
$pdf->Output();