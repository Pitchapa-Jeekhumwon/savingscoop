<?php
$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
$writer = new XLSXWriter();
$writer->setAuthor('Some Author');

$titleStyle = array(
    'font'=>'Cordia New',
    'font-size'=>16,
    'font-style'=>'bold',
    'halign'=>'center',
);

$headerStyle = array(
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'center',
    'border-style' => 'thin',
    'border'=>'left,right,top,bottom'
);
$styleT = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'center',
    'border-style' => 'thin',
    'border'=>'left,right,top'];

$styleB = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'center',
    'border-style' => 'thin',
    'border'=>'left,right,bottom'
];

$styleTB = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'center',
    'border-style' => 'thin',
    'border'=>'left,right,top,bottom'
];
$textNull = [];
$textLeftBorder = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'left',
    'border-style' => 'thin',
    'border'=>'left,right,top,bottom'
];

$textRightBorder = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'right',
    'border-style' => 'thin',
    'border'=>'left,right,top,bottom'
];

$textCenterBorder = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'center',
    'border-style' => 'thin',
    'border'=>'left,right,top,bottom'
];
$textLeft = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'halign'=>'left'
];

$textRight = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'halign'=>'right'
];

$textCenter = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'halign'=>'center'
];


$textRightBorderBottom = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'halign'=>'right',
    'border-style' => 'thin',
    'border'=>'bottom'
];

$textRightBorderBottomRed = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'halign'=>'right',
    'border-style' => 'thin',
    'border'=>'bottom',
    'color'=>'#FF0000',
];
$textRightRed = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'halign'=>'right',
    'color'=>'#FF0000',
];

$textCenterBorderBottomPink = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'halign'=>'center',
    'border-style' => 'thin',
    'border'=>'bottom',
    'color'=>'#FF00FF',
];
$textCenterPink = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'halign'=>'center',
    'color'=>'#FF00FF',
];
$textCenterBorderBottomBlue = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'halign'=>'center',
    'border-style' => 'thin',
    'border'=>'bottom',
    'color'=>'#0000FF',
];
$textCenterBlue = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'halign'=>'center',
    'color'=>'#0000FF',
];
$textCenterBorderBottomGreen  = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'halign'=>'center',
    'border-style' => 'thin',
    'border'=>'bottom',
    'color'=>'#339966',
];
$textCenterGreen  = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'halign'=>'center',
    'color'=>'#339966',
];

$headerLeftBorder = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'halign'=>'left',
    'border-style' => 'thin',
    'border'=>'left,right,top,bottom'
];

$headerStyle1 = array(
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT
);

$headerStyle2 = array(
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT
);

$textStyle = array(
    $textCenterBorder,
    $textLeftBorder,
    $textLeftBorder,
    $textLeftBorder,
    $textCenterBorder,
    $textCenterBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textCenterBorder,
    $textLeftBorder,
    $textCenterBorder,
    $textCenterBorder,
    $textCenterBorder
);

$footerStyle = array(
    $textRightBorder,
    $textLeftBorder,
    $textLeftBorder,
    $textLeftBorder,
    $textCenterBorder,
    $textCenterBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textCenterBorder,
    $textLeftBorder,
    $textCenterBorder,
    $textCenterBorder,
    $textCenterBorder
);

$sheet1 = 'รายงานสรุป';
$title = array(''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string');
$title1 = array(@$_SESSION['COOP_NAME']);
$title2 = array("รายงานการจ่ายเงินกู้");
$title3 = array("วันที่เริ่มสัญญา ระหว่าง ".$_GET['date_start']." ถึง ".$_GET['date_end']."รหัสหน่วย 00000 ถึง 00007 ".$loan_type_name."สถานะการจ่าย จ่าย");
$header = array("string","string","string","string","string","string","string","string","string","string","string","string","string","string","string","string","string");
$text_top1 = array("ลำดับ","เลขสัญญา","เลขทะเบียน","ชื่อ - นามสกุล","วันเริ่มเก็บ","วิธีเก็บ","การชำระ","","อนุมัติเงินกู้","รายการหัก","","","","จ่ายจริง","เลขที่บัญชี","หลักประกัน","ผู้บันทึก","ผู้อนุมัติ");
$text_top2 = array("","","","","","","ต่องวด","งวด","","","เงินต้น","ดอกเบี้ย","รวม","","","","","");
$year = date("Y")+543;
$date = date('d/m/');
$time_now = date('H:i:s');
$date_now = $date.$year.' '.$time_now;
//echo $date_now;exit;
$header_top1 = array("","","","","","","","","","","","","","","","","เวลาที่พิมพ์ : ".$date_now,"");

//$writer->writeSheetHeader($sheet1, $title, $col_options = ['suppress_row'=>true] );
$writer->writeSheetHeader($sheet1, $title,$col_options = ['widths'=>[4.86,10.43,10.43,30,16.57,10.43,14.43,10.57,15.29,10.43,10.43,11.29,9.43,16,9.57,35.57,7.71,8.86,10]]);
$writer->writeSheetRow($sheet1, $header_top1,$textRight);
$writer->writeSheetRow($sheet1, $title1,$titleStyle);
$writer->writeSheetRow($sheet1, $title2,$titleStyle);
$writer->writeSheetRow($sheet1, $title3,$titleStyle);
$writer->writeSheetRow($sheet1, $header_top2 = array("เงินกู้ : ".$loan_type_name),$textLeft);
$writer->writeSheetRow($sheet1, $text_top1,$headerStyle);
$writer->writeSheetRow($sheet1, $text_top2,$headerStyle);

$row = 1;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=16, $end_row=$row, $end_col=17);
$row = 2;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=17);
$row = 3;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=17);
$row = 4;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=17);
$row++;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=1);
$row++;
for ($i = 0;$i<=5;$i++){
    $writer->markMergedCell($sheet1, $start_row=$row, $start_col=$i, $end_row=$row+1, $end_col=$i);
}
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=8, $end_row=$row+1, $end_col=8);
for ($i = 13;$i<=17;$i++){
    $writer->markMergedCell($sheet1, $start_row=$row, $start_col=$i, $end_row=$row+1, $end_col=$i);
}
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=6, $end_row=$row, $end_col=7);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=9, $end_row=$row, $end_col=12);
$row++;

$sum_loan_approve = 0;
$sum_period_amount = 0;
$sum_money_per_period = 0;
$sum_pay_amount = 0;
$sum_interest_amount = 0;

$sum_bay_share = 0;
$sum_deduction = 0;
$sum_true_pay = 0;
$num_presun = 0;
foreach ($datas as $mem_group_id => $loan_data) {
    if ($loan_data != array()){
        $row++;
        $writer->writeSheetRow($sheet1, array($coop_mem_group[$mem_group_id], "", "", "", "", "", "", "", "", "", "", "", "", "", "", '', "", ''), $headerLeftBorder);
        $writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=17);
    }
    foreach ($loan_data as $key => $value) {
        $num_presun++;
        $loan_approve = $value['loan_amount'];
        if ($value['pay_type'] == 1) {
            $pay_type = 'คงต้น';
        } else if ($value['pay_type'] == 2) {
            $pay_type = 'คงยอด';
        }
        $r = 0;
        $bay_share = 0;
        $deduction = 0;
        $true_pay = $loan_approve - $bay_share - $deduction;
        if (!empty($value['createdatetime'])) {
            $createdatetime = substr($value['createdatetime'], 0, 10);
            $createdatetime = explode("-", $createdatetime);
            $createdatetime = $createdatetime['2'] . '/' . $createdatetime['1'] . '/' . ($createdatetime['0'] + 543);
        } else {
            $createdatetime = '-';
        }
        $data[$r][0] = $num_presun;
        $data[$r][1] = $value['contract_number'];
        $data[$r][2] = $value['member_id'];
        $data[$r][3] = $value['full_name'];
        $data[$r][4] = $createdatetime;
        $data[$r][5] = $pay_type; //C
//        $data[$r][6] = @number_format($value['money_per_period'], 2, '.', ',');
        $data[$r][6] = @number_format($value['total_paid_per_month'], 2, '.', ',');
        $data[$r][7] = @number_format($value['period_amount'], 2, '.', ',');
        $data[$r][8] = @number_format($value['loan_amount'], 2, '.', ',');
        $data[$r][9] = '';
        $data[$r][10] = '';
        $data[$r][11] = '';
        $data[$r][12] = '';
        $data[$r][13] = @number_format($loan_approve, 2, '.', ',');
        $data[$r][14] = '';
        $data[$r][15] = '';
        $data[$r][16] = $value['user_name'];
        $data[$r][17] = '';


        if (!empty($value['prefix_code'])) {
            $row_prefix_code = explode("&,", $value['prefix_code']);
            $row_pay_amount = explode("&,", $value['pay_amount']);
            $interest_amount = explode("&,", $value['interest_amount']);
            foreach ($row_prefix_code as $prefix_key => $prefix_value) {
                if ($r > 0) {
                    foreach ($data[$r - 1] as $data_1_key => $data_1_value) {
                        $data[$r][$data_1_key] = ''; // set $data_1 เป็น ค่าว่าง
                    }
                }
                $data[$r][9] = $row_prefix_code[$prefix_key];
                $data[$r][10] = @number_format($row_pay_amount[$prefix_key], 2, '.', ',');
                $data[$r][11] = @number_format($interest_amount[$prefix_key], 2, '.', ',');
                $data[$r][12] = @number_format($row_pay_amount[$prefix_key] + $interest_amount[$prefix_key], 2, '.', ',');


                $sum_pay_amount += $row_pay_amount[$prefix_key];
                $sum_interest_amount += $interest_amount[$prefix_key];
                $r++;
            }
        } else {
//        $writer->writeSheetRow($sheet1, $data[$r], $styleTB);
//        $row++;
        }
        $r = 0;
        if (!empty($value['guarantee_person_id'])) {
            $row_person_id = explode("&,", $value['guarantee_person_id']);
            $row_full_name = explode("&,", $value['guarantee_full_name']);
            $count_person = count($row_person_id);
//        echo ' '.$count_person.'<br>';

            for ($i = 0; $i < $count_person; $i += 1) {
//            echo $row_full_name[$i];
                if (empty($data[$r])) {
                    $data[$r][0] = ''; //A
                    $data[$r][1] = ''; //B
                    $data[$r][2] = ''; //C
                    $data[$r][3] = ''; //D
                    $data[$r][4] = ''; //E
                    $data[$r][5] = ''; //F
                    $data[$r][6] = ''; //G
                    $data[$r][7] = ''; //H
                    $data[$r][8] = ''; //I
                    $data[$r][9] = ''; //J
                    $data[$r][10] = ''; //K
                    $data[$r][11] = ''; //L
                    $data[$r][12] = ''; //M
                    $data[$r][13] = ''; //N
                    $data[$r][14] = ''; //O
                    $data[$r][15] = $row_full_name[$i]; //P
                    $data[$r][16] = ''; //Q
                    $data[$r][17] = ''; //R
                } else {
                    $data[$r][15] = $row_full_name[$i]; //P
                }

                $r++;
            }

        }
        foreach ($data as $key2 => $value2) {
            $writer->writeSheetRow($sheet1, $data[$key2], $textStyle);
            $row++;
        }
        $data = array();
        $sum_loan_approve += $loan_approve;
        $sum_period_amount += $value['period_amount'];
//        $sum_money_per_period += $value['money_per_period'];
        $sum_money_per_period += $value['total_paid_per_month'];

        $sum_bay_share += $bay_share;
        $sum_deduction += $deduction;
        $sum_true_pay += $true_pay;
    }
}
$sum_amount = $sum_pay_amount + $sum_interest_amount;
$sum_amount = @number_format($sum_amount, 2, '.', ',');
$sum_loan_approve = @number_format($sum_loan_approve, 2, '.', ',');
$sum_period_amount = @number_format($sum_period_amount, 2, '.', ',');
$sum_money_per_period = @number_format($sum_money_per_period, 2, '.', ',');
$sum_pay_amount = @number_format($sum_pay_amount, 2, '.', ',');
$sum_interest_amount = @number_format($sum_interest_amount, 2, '.', ',');
$sum_bay_share = @number_format($sum_bay_share, 2, '.', ',');
$sum_deduction = @number_format($sum_deduction, 2, '.', ',');
$sum_true_pay = @number_format($sum_true_pay, 2, '.', ',');
$footer1 = array("รวมทั้งหมดจำนวน $num_presun ราย เป็นเงิน","","","","","","$sum_money_per_period","",$sum_loan_approve,"",$sum_pay_amount,$sum_interest_amount,$sum_amount,$sum_true_pay,"","","","");
$row++;
$writer->writeSheetRow($sheet1, $footer1,$footerStyle);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=5);

//exit;


$filename = "รายงานการจ่ายเงินกู้แยกหน่วย.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();