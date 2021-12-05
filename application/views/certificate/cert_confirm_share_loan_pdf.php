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

$pdf = new FPDI();

$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$pdf->AddFont('THSarabunNew-Bold', '', 'THSarabunNew-Bold.php');

foreach ($datas as $member_id => $member_info) {
    $pdf->AddPage();
    $pdf->SetMargins(0, 0, 0);
    $border = 0;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetAutoPageBreak(true,15);
    $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_profile/'.@$row_profile['coop_img'],10,15,-300);
    // $pdf->Image('assets/images/coop_profile/'.@$row_profile['coop_img'],10,15,-300);

    $y = 15;
    $pdf->SetFont('THSarabunNew-Bold', '', 14 );
    $pdf->SetXY(70, $y);
    $pdf->Cell(140, 8, U2T("สหกรณ์ออมทรัพย์"), 0, 'C');
    $y += 8;
    $pdf->SetXY(70, $y);
    $pdf->Cell(140, 8, U2T("หนังสือยืนยันยอดลูกหนี้ เงินฝาก และทุนเรือนหุ้น"), 0, 'C');

    $pdf->SetFont('THSarabunNew', '', 14 );
    $y += 8;
    $pdf->SetXY( 0, $y );
    $pdf->MultiCell(205, 8, U2T("วันที่ ".$this->center_function->ConvertToThaiDate($date,'','0')), 0, 'R');
    $y += 8;
    $pdf->SetXY( 0, $y );
    $pdf->MultiCell(210, 8, U2T("เรียน ".$member_info["prename_full"].$member_info["firstname_th"]." ".$member_info["lastname_th"]." สมาชิกเลขทะเบียน ".$member_info["member_id"]." สังกัด ".$member_info["name"]), 0, 'C');
    $y += 8;
    $pdf->SetXY( 0, $y );
    $pdf->MultiCell(210, 8, U2T("สหกรณ์ออมทรัพย์ ขอเรียนว่า ณ วันที่ ".$this->center_function->ConvertToThaiDate($date,'','0')), 0, 'C');

    $pdf->SetFont('THSarabunNew', '', 12 );
    $count = 0;
    $y = 47;
    $line_height = 6;
    //Loan
    if (!empty($member_info["loan"])) {
        $y += 8;
        $count++;
        $pdf->SetXY( 20, $y );
        $pdf->MultiCell(105, $line_height, U2T($count.". ท่านเป็นหนี้ต่อสหกรณ์ฯ เป็นจำนวนเงินทั้งสิ้น"), 0, 'L');
        $pdf->SetXY( 125, $y );
        $pdf->MultiCell(30, $line_height, number_format($total_data[$member_id]["loan"],2).U2T(" บาท"), 0, 'R');
        $pdf->SetXY( 155, $y );
        $pdf->MultiCell(40, $line_height, U2T(" ตามรายการดังต่อไปนี้"), 0, 'R');
        $sub_count = 0;
        foreach($member_info["loan"] as $id => $loan) {
            $sub_count++;
            $y += 6;
            $pdf->SetXY( 20, $y );
            $pdf->MultiCell(105, $line_height, U2T($count.".".$sub_count." ".$loan['contract_number']), 0, 'L');
            $pdf->SetXY( 125, $y );
            $pdf->MultiCell(30, $line_height, U2T("หนี้คงเหลือ"), 0, 'R');
            $pdf->SetXY( 155, $y );
            $pdf->MultiCell(40, $line_height, number_format(!empty($loan["total"]) ? $loan["total"] : 0,2).U2T(" บาท"), 0, 'R');
        }
    }

    //Account
    if (!empty($member_info["account"])) {
        $count++;
        $y += 8;
        $pdf->SetXY( 20, $y );
        $pdf->MultiCell(105, $line_height, U2T($count.". มีเงินฝากไว้กับสหกรณ์ฯ เป็นจำนวนเงินทั้งสิ้น"), 0, 'L');
        $pdf->SetXY( 125, $y );
        $pdf->MultiCell(30, $line_height, number_format($total_data[$member_id]["account"],2).U2T(" บาท"), 0, 'R');
        $pdf->SetXY( 155, $y );
        $pdf->MultiCell(40, $line_height, U2T(" ตามรายการดังต่อไปนี้"), 0, 'R');
        $sub_count = 0;
        foreach($member_info["account"] as $id => $account) {
            $sub_count++;
            $y += 6;
            $pdf->SetXY( 20, $y );
            $pdf->MultiCell(105, $line_height, U2T($count.".".$sub_count." ".$account['type_name']." ".$account['account_id']), 0, 'L');
            $H = $pdf->GetY()-$y;
            $pdf->SetXY( 125, $y );
            $pdf->MultiCell(30, $line_height, U2T("จำนวนเงิน"),0, 'R');
            $pdf->SetXY( 155, $y );
            $pdf->MultiCell(40, $line_height, number_format($account["balance"],2).U2T(" บาท"), 0, 'R');
            $y = $H+$y-7;
        }
    }
    
    //Share
    if (!empty($member_info["share_collect_value"])) {
        $count++;
        $y += 8;
        $pdf->SetXY( 20, $y );
        $pdf->MultiCell(105, $line_height, U2T($count.". ทุนเรือนหุ้น เป็นจำนวน"), 0, 'L');
        $pdf->SetXY( 125, $y );
        $pdf->MultiCell(30, $line_height, number_format($member_info["share_collect_value"],2).U2T(" บาท"), 0, 'R');
        $pdf->SetXY( 155, $y );
        $pdf->MultiCell(40, $line_height, "", 0, 'R');
    }

    // $y += 8;
    $y = 175;
    $pdf->SetFont('THSarabunNew', '', 12 );
    $pdf->SetXY( 140, $y);
    $pdf->MultiCell(0, 8, U2T("ขอแสดงความนับถือ"), 0, 'C');
    $y += 8;
    $pdf->SetXY( 140, $y);
    $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_signature/'.$signature->signature_3,164,$y,20);
    // $pdf->Image('assets/images/coop_signature/'.$signature->signature_3,164,$y,-250);
    $y += 8;
    $pdf->SetXY( 140, $y-0.5);
    $pdf->MultiCell(0, 8, U2T("( ".$signature->manager_name." )"), 0, 'C');
    SetDash($pdf, 0.5, 0.5);
    $pdf->Line(162, $y+5.5, 188, $y+5.5);
    $y += 6;
    $pdf->SetXY( 140, $y);
    $pdf->MultiCell(0, 6, U2T("ผู้จัดการ"), 0, 'C');
    $y += 6;
    // $y = 190;
    $pdf->SetXY( 140, $y);
    $pdf->MultiCell(0, 6, U2T("สหกรณ์ออมทรัพย์"), 0, 'C');

    // if ($y >= 190) {
    //     $pdf->AddPage();
    //     $pdf->SetMargins(0, 0, 0);
    //     $border = 0;
    //     $pdf->SetTextColor(0, 0, 0);
    //     $pdf->SetAutoPageBreak(true,15);
    //     $y = 20;
    // }

    $y += 8;
    $pdf->SetFont('THSarabunNew-Bold', '', 12 );
    $pdf->SetXY( 0, $y);
    $pdf->MultiCell(0, 6, U2T("หมายเหตุ ถ้าตรวจสอบแล้วโปรดส่งกลับคืนหาผู้สอบบัญชีของสหกรณ์ภายใน 7 วันหลังได้รับหนังสือนี้ ทั้งฉบับเพื่อประโยชน์ในการตรวจสอบต่อไป"), 0, 'C');
    $y += 8;
    SetDash($pdf, 0, 0);
    $pdf->Line(5, $y, 200, $y);

    $y += 5;
    $pdf->SetXY( 0, $y);
    $pdf->MultiCell(0, 6, U2T("หนังสือตอบยืนยันยอด"), 0, 'C');
    $y += 8;
    $pdf->SetFont('THSarabunNew', '', 12 );
    $pdf->SetXY( 20, $y);
    $pdf->MultiCell(0, 6, U2T("เรียน ".$_GET['account_name']." ผู้สอบบัญชีสหกรณ์ออมทรัพย์"), 0, 'L');
    $y += 6;
    $pdf->SetXY( 30, $y);
    $pdf->MultiCell(0, 6, U2T("ข้าพเจ้าขอแจ้งให้ทราบว่า ยอดหนี้ เงินฝาก และทุนเรือนหุ้น ที่ส่งไปตรวจสอบนั้น                    (  ) ถูกต้อง (  ) ไม่ถูกต้อง"), 0, 'L');
    $y += 6;
    $pdf->SetXY( 30, $y);
    $pdf->MultiCell(40, 6, U2T("(ถ้าไม่ถูกต้อง) สาเหตุเพราะ"), 0, 'L');
    $pdf->Line(70, $y+5, 190, $y+5);
    $y += 6;
    $pdf->Line(70, $y+5, 190, $y+5);
    $y += 6;
    $pdf->SetXY( 30, $y);
    $pdf->MultiCell(40, 6, U2T("จึงเรียนมาเพื่อทราบ"), 0, 'L');
    $y += 6;
    $pdf->SetXY( 130, $y);
    $pdf->MultiCell(10, 6, U2T("ลงชื่อ"), 0, 'L');
    SetDash($pdf, 0.5, 0.5);
    $pdf->Line(140, $y+5, 188, $y+5);
    $y += 6;
    $pdf->SetXY( 137, $y);
    $pdf->MultiCell(1, 6, U2T("("), 0, 'L');
    $pdf->SetXY( 139, $y);
    $pdf->MultiCell(50,6, U2T($member_info["prename_full"].$member_info["firstname_th"]." ".$member_info["lastname_th"]), 0, "C");
    $pdf->SetXY( 188, $y);
    $pdf->MultiCell(1, 6, U2T(")"), 0, 'L');
    $pdf->Line(140, $y+6, 188, $y+6);
    $y += 6;
    $pdf->SetXY( 139, $y);
    $pdf->MultiCell(50,6, U2T("เลขทะเบียนสมาชิก ".$member_info["member_id"]), 0, "C");
}

$pdf->Output();