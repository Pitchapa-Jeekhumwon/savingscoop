<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}
function new_page($pdf, $page_index, $from_date_text, $thru_date_text, $y_point) {
	$line_height = 8;
	$col_x_1 = 10;
	$col_w_1 = 90;
	$col_x_2 = $col_w_1 + $col_x_1;
	$col_w_2 = 25;
	$col_x_3 = $col_w_2 + $col_x_2;
	$col_w_3 = 15;
	$col_x_4 = $col_w_3 + $col_x_3;
	$col_w_4 = 30;
	$col_x_5 = $col_w_4 + $col_x_4;
	$col_w_5 = 30;
	$full_w = $col_x_5 + $col_w_5 - $col_x_1;

	//draw bottom line of prev page.
	$pdf->SetFont('common', '', 12);
	$pdf->SetXY($col_x_1, $y_point);
	$pdf->MultiCell($full_w, $line_height, "", 'B', "C");

	$pdf->AddPage();

	$pdf->SetMargins(0, 0, 0);
	$border = 0;
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetAutoPageBreak(true,0);

	$y_point = 20;
	$pdf->SetFont('common', '', 12);
	$pdf->SetXY( 10, $y_point - 4 );
	$pdf->MultiCell(190, 10, U2T("หน้าที่ ".$page_index), 0, "R");

	$pdf->SetFont('bold', '', 20 );
	$pdf->SetXY( 0, $y_point );
	$pdf->MultiCell(210, 10, U2T($_SESSION['COOP_NAME']), 0, "C");

	$pdf->SetFont('bold', '', 16 );
	$y_point += 10;
	$pdf->SetXY( 0, $y_point );
	$pdf->MultiCell(210, 8, U2T("สรุปรายการรับจ่ายเงินสด"), 0, "C");

	$period = 'วันที่ '.$from_date_text;
	if($_GET["from_date"] != $_GET["thru_date"]) $period .= ' ถึง วันที่ '.$thru_date_text;
	$y_point += 8;
	$pdf->SetXY( 0, $y_point );
	$pdf->MultiCell(210, 8, U2T($period), 0, "C");

	$pdf->SetFont('bold', '', 12 );

	$y_point += $line_height+5;
	$pdf->SetXY($col_x_1, $y_point);
	$pdf->MultiCell($col_w_1 + $col_w_2, $line_height * 2, U2T("รายการ"), 1, "C");
	$pdf->SetXY($col_x_3, $y_point);
	$pdf->MultiCell($col_w_3, $line_height * 2, U2T("รหัสบัญชี"), 1, "C");
	$pdf->SetXY($col_x_4, $y_point);
	$pdf->MultiCell($col_w_4 + $col_w_5, $line_height, U2T("จำนวนเงิน"), 1, "C");

	$y_point += $line_height;
	$pdf->SetXY($col_x_4, $y_point);
	$pdf->MultiCell($col_w_4, $line_height, U2T("รายรับ"), 1, "C");
	$pdf->SetXY($col_x_5, $y_point);
	$pdf->MultiCell($col_w_5, $line_height, U2T("รายจ่าย"), 1, "C");
	return $y_point;
}

function add_col($pdf, $type, $description, $date, $chart_id, $amount, $y_point) {
	$line_height = 8;
	$col_x_1 = 10;
	$col_w_1 = 90;
	$col_x_2 = $col_w_1 + $col_x_1;
	$col_w_2 = 25;
	$col_x_3 = $col_w_2 + $col_x_2;
	$col_w_3 = 15;
	$col_x_4 = $col_w_3 + $col_x_3;
	$col_w_4 = 30;
	$col_x_5 = $col_w_4 + $col_x_4;
	$col_w_5 = 30;
	$pdf->SetFont('common', '', 12 );
	$y_point += $line_height;
	$pdf->SetXY($col_x_1, $y_point);
	$pdf->MultiCell($col_w_1, $line_height, U2T($description), 0, "L");
	$h = $pdf->GetY();
	$pdf->SetXY($col_x_2, $y_point);
	$pdf->MultiCell($col_w_2, $line_height, U2T($date), 0, "R");
	$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
	$pdf->SetXY($col_x_3, $y_point);
	$pdf->MultiCell($col_w_3, $line_height, $chart_id, 0, "C");
	$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
	$pdf->SetXY($col_x_4, $y_point);
	$pdf->MultiCell($col_w_4, $line_height, $type == 'RV' ? number_format($amount, 2) : "", 0, "R");
	$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
	$pdf->SetXY($col_x_5, $y_point);
	$pdf->MultiCell($col_w_5, $line_height, $type == 'PV' ? number_format($amount, 2) : "", 0, "R");
	$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;

	$row_height = $h - $y_point;
	$pdf->SetXY($col_x_1, $y_point);
	$pdf->MultiCell($col_w_1 + $col_w_2, $row_height, "", 'L', "C");
	$pdf->SetXY($col_x_3, $y_point);
	$pdf->MultiCell($col_w_3, $row_height, "", 'L', "C");
	$pdf->SetXY($col_x_4, $y_point);
	$pdf->MultiCell($col_w_4, $row_height, "", 'L', "C");
	$pdf->SetXY($col_x_5, $y_point);
	$pdf->MultiCell($col_w_5, $row_height, "", 'LR', "C");
	return $y_point + $row_height - $line_height;
}

$pdf = new FPDI('P','mm', "A4");
$pdf->AddFont('common', '', 'THSarabunNew.php');
$pdf->AddFont('bold','','THSarabunNew-Bold.php');

$page_index = 0;
$line_height = 8;
$col_x_1 = 10;
$col_w_1 = 90;
$col_x_2 = $col_w_1 + $col_x_1;
$col_w_2 = 25;
$col_x_3 = $col_w_2 + $col_x_2;
$col_w_3 = 15;
$col_x_4 = $col_w_3 + $col_x_3;
$col_w_4 = 30;
$col_x_5 = $col_w_4 + $col_x_4;
$col_w_5 = 30;
$full_w = $col_x_5 + $col_w_5 - $col_x_1;
$index = 0;
$rv_total = 0;
foreach($data['RV'] as $key => $value){
    if($y_point > 250 || $y_point == 0) {
		$page_index++;
		$y_point = new_page($pdf, $page_index, $from_date_text, $thru_date_text, $y_point);
	}

	//Type Header.
	if($index == 0) {
		$pdf->SetFont('bold', '', 12 );
		$y_point += $line_height;
		$pdf->SetXY($col_x_1, $y_point);
		$pdf->MultiCell($col_w_1, $line_height, U2T("รายรับ"), 0, "L");

		$pdf->SetXY($col_x_1, $y_point);
		$pdf->MultiCell($col_w_1 + $col_w_2, $line_height, "", 'L', "C");
		$pdf->SetXY($col_x_3, $y_point);
		$pdf->MultiCell($col_w_3, $line_height, "", 'L', "C");
		$pdf->SetXY($col_x_4, $y_point);
		$pdf->MultiCell($col_w_4, $line_height, "", 'L', "C");
		$pdf->SetXY($col_x_5, $y_point);
		$pdf->MultiCell($col_w_5, $line_height, "", 'LR', "C");
	}

	$index++;
	$description = $value['journal_ref']."-".$value['account_chart'];
	$date = $this->center_function->ConvertToThaiDate($value['account_datetime'],'1','0');
	$y_point = add_col($pdf, "RV", $description, $date, $value['account_chart_id'], $value['account_amount'], $y_point);

	$rv_total += $value['account_amount'];
}
$index = 0;
$pv_total = 0;
foreach($data['PV'] as $key => $value){
    if($y_point > 250 || $y_point == 0) {
		$page_index++;
		$y_point = new_page($pdf, $page_index, $from_date_text, $thru_date_text, $y_point);
	}

	//Type Header.
	if($index == 0) {
		$pdf->SetFont('bold', '', 12 );
		$y_point += $line_height;
		$pdf->SetXY($col_x_1, $y_point);
		$pdf->MultiCell($col_w_1, $line_height, U2T("รายจ่าย"), 0, "L");

		$pdf->SetXY($col_x_1, $y_point);
		$pdf->MultiCell($col_w_1 + $col_w_2, $line_height, "", 'L', "C");
		$pdf->SetXY($col_x_3, $y_point);
		$pdf->MultiCell($col_w_3, $line_height, "", 'L', "C");
		$pdf->SetXY($col_x_4, $y_point);
		$pdf->MultiCell($col_w_4, $line_height, "", 'L', "C");
		$pdf->SetXY($col_x_5, $y_point);
		$pdf->MultiCell($col_w_5, $line_height, "", 'LR', "C");
	}

	$index++;
	$description = $value['journal_ref']."-".$value['account_chart'];
	$date = $this->center_function->ConvertToThaiDate($value['account_datetime'],'1','0');
	$y_point = add_col($pdf, "PV", $description, $date, $value['account_chart_id'], $value['account_amount'], $y_point);
	$pv_total += $value['account_amount'];
}

//For total.
if($y_point > 250 || $y_point == 0) {
	$page_index++;
	$y_point = new_page($pdf, $page_index, $from_date_text, $thru_date_text, $y_point);
}
$pdf->SetFont('common', '', 12 );
$y_point += $line_height;
$pdf->SetXY($col_x_1, $y_point);
$pdf->MultiCell($col_w_1 + $col_w_2, $line_height, U2T("รวม รายรับ-รายจ่าย"), "TL", "L");
$pdf->SetXY($col_x_3, $y_point);
$pdf->MultiCell($col_w_3, $line_height, $chart_id, "TL", "C");
$pdf->SetXY($col_x_4, $y_point);
$pdf->MultiCell($col_w_4, $line_height, !empty($rv_total) ? number_format($rv_total, 2) : "", "TL", "R");
$pdf->SetXY($col_x_5, $y_point);
$pdf->MultiCell($col_w_5, $line_height, !empty($pv_total) ? number_format($pv_total, 2) : "", "TLR", "R");

$y_point += $line_height;
$pdf->SetXY($col_x_1, $y_point);
$pdf->MultiCell($col_w_1 + $col_w_2, $line_height, "             ".U2T("ยอดคงเหลือยกมา"), "L", "L");
$pdf->SetXY($col_x_3, $y_point);
$pdf->MultiCell($col_w_3, $line_height, $chart_id, "L", "C");
$pdf->SetXY($col_x_4, $y_point);
$pdf->MultiCell($col_w_4, $line_height, $cash_balance >= 0 ? number_format($cash_balance, 2) : "(".number_format($cash_balance, 2).")", "L", "R");
$pdf->SetXY($col_x_5, $y_point);
$pdf->MultiCell($col_w_5, $line_height, "", "LR", "R");

$balance = $cash_balance + $rv_total - $pv_total;
$y_point += $line_height;
$pdf->SetXY($col_x_1, $y_point);
$pdf->MultiCell($col_w_1 + $col_w_2, $line_height, "             ".U2T("ยอดคงเหลือยกไป"), "L", "L");
$pdf->SetXY($col_x_3, $y_point);
$pdf->MultiCell($col_w_3, $line_height, $chart_id, "L", "C");
$pdf->SetXY($col_x_4, $y_point);
$pdf->MultiCell($col_w_4, $line_height, "", "L", "R");
$pdf->SetXY($col_x_5, $y_point);
$pdf->MultiCell($col_w_5, $line_height, $balance >= 0 ? number_format($balance, 2) : "(".number_format($balance, 2).")", "LR", "R");

$debit_balance = $rv_total + $cash_balance;
$credit_balance = $pv_total + $balance;
$y_point += $line_height;
$pdf->SetXY($col_x_1, $y_point);
$pdf->MultiCell($col_w_1 + $col_w_2, $line_height, U2T("รวม"), "LB", "L");
$pdf->SetXY($col_x_3, $y_point);
$pdf->MultiCell($col_w_3, $line_height, $chart_id, "LB", "C");
$pdf->SetXY($col_x_4, $y_point);
$pdf->MultiCell($col_w_4, $line_height, number_format($debit_balance, 2), "LB", "R");
$pdf->SetXY($col_x_5, $y_point);
$pdf->MultiCell($col_w_5, $line_height, number_format($credit_balance, 2), "LRB", "R");


//For signature
if($y_point > 250 || $y_point == 0) {
	$page_index++;
	$y_point = new_page($pdf, $page_index, $from_date_text, $thru_date_text, $y_point);
}
$y_point += $line_height;
$pdf->SetXY($col_x_1, $y_point);
$pdf->MultiCell($full_w, 35, "", "RLB", "L");

$y_point += $line_height + 4;
$col_size = $full_w/3;
$col_x_2 = $col_x_1 + $col_size;
$col_x_3 = $col_x_2 + $col_size;
$pdf->SetXY($col_x_1, $y_point);
$pdf->MultiCell($col_size, $line_height, "........................................................", 0, "C");
$pdf->SetXY($col_x_2, $y_point);
$pdf->MultiCell($col_size, $line_height, "........................................................", 0, "C");
$pdf->SetXY($col_x_3, $y_point);
$pdf->MultiCell($col_size, $line_height, "........................................................", 0, "C");

$y_point += $line_height;
$pdf->SetXY($col_x_1, $y_point);
$pdf->MultiCell($col_size, $line_height, U2T("พนักงานการเงิน"), 0, "C");
$pdf->SetXY($col_x_2, $y_point);
$pdf->MultiCell($col_size, $line_height, U2T("พนักงานบัญชี"), 0, "C");
$pdf->SetXY($col_x_3, $y_point);
$pdf->MultiCell($col_size, $line_height, U2T("ผู้จัดการ"), 0, "C");

$pdf->Output();