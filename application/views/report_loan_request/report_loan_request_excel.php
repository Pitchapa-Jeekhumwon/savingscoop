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
    'font-size'=>12,
    'halign'=>'left',
    'border-style' => 'thin',
    'border'=>'left,right,top,bottom'
];

$textRightBorder = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'halign'=>'right',
    'border-style' => 'thin',
    'border'=>'left,right,top,bottom'
];

$textCenterBorder = [
    'font'=>'Cordia New',
    'font-size'=>12,
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
    $textCenterBorder,
    $textCenterBorder,
    $textLeftBorder,
    $textCenterBorder,
    $textRightBorder,
    $textRightBorder,
    $textCenterBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textCenterBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textLeftBorder
);

$textStyle3 = array(
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textRight,
    $textRight,
    $textLeft,
    $textRight,
    $textRight,
    $textRight,
    $textRight,
    $textLeft,
    $textRight,
    $textRight,
    $textRight,
    $textRight,
    $textNull
);

$textStyle4 = array(
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textRightBorderBottom,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textRightBorderBottom,
    $textNull,
    $textNull,
    $textNull
);

$textStyle6 = array(
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textCenter,
    $textRightBorderBottomRed,
    $textNull,
    $textNull,
    $textCenter,
    $textRightBorderBottomRed,
    $textNull,
    $textNull,
    $textNull,
    $textCenter,
    $textRightBorderBottomRed,
    $textNull,
    $textCenter,
    $textRight
);

$textStyle8 = array(
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textCenter,
    $textNull,
    $textNull,
    $textCenter,
    $textNull
);

$textStyle9 = array(
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textCenterPink,
    $textRightRed,
    $textNull,
    $textNull,
    $textNull,
    $textCenterPink,
    $textRightRed,
    $textNull,
    $textNull,
    $textNull
);

$textStyle10 = array(
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textCenterBorderBottomPink,
    $textRightBorderBottomRed,
    $textNull,
    $textNull,
    $textNull,
    $textCenterBorderBottomPink,
    $textRightBorderBottomRed,
    $textNull,
    $textNull,
    $textNull
);

$textStyle11 = array(
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textCenterBlue,
    $textRightRed,
    $textNull,
    $textNull,
    $textNull,
    $textCenterGreen,
    $textRightRed,
    $textNull,
    $textNull,
    $textNull
);

$textStyle12 = array(
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textNull,
    $textCenterBlue,
    $textRightRed,
    $textNull,
    $textNull,
    $textNull,
    $textCenterGreen,
    $textRightRed,
    $textNull,
    $textNull,
    $textNull
);

$sheet1 = 'รายงานสรุป';
$title = array(''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string');
$title1 = array(@$_SESSION['COOP_NAME']);
$title2 = array("รายงานการรับคำขอกู้");
$title3 = array("วันกู้ระหว่าง ".$_GET['date_start']." ถึง ".$_GET['date_end']." ".$loan_type_name);
$header = array("string","string","string","string","string","string","string","string","string","string","string","string","string","string","string","string","string");
$text_top1 = array("ลำดับ","ทะเบียน","ชื่อ - นามสกุล","อายุ","เงินเดือน","ค่าหุ้น","ทุนเรือนหุ้น","สิทธิกู้","ขอกู้","รายละเอียดเงินกู้","","","หักชำระหนี้เก่า","","","ซื้อหุ้น","หักประกัน","จ่ายจริง","เลขบัญชี");

$text_top2 = array("","","","","","","","","","อนุมัติ","งวด","ต่องวด","","ต้นเงิน","ดอกเบี้ย","","","","");
$year = date("Y")+543;
$date = date('d/m/');
$time_now = date('H:i:s');
$date_now = $date.$year.' '.$time_now;
//echo $date_now;exit;
$header_top1 = array("","","","","","","","","","","","","","","","","เวลาที่พิมพ์ : ".$date_now,"");

//$writer->writeSheetHeader($sheet1, $title, $col_options = ['suppress_row'=>true] );
$writer->writeSheetHeader($sheet1, $title,$col_options = ['widths'=>[4.86,10.43,22.14,8.43,10.57,10.43,14.43,10.57,11.29,10.43,10.43,11.29,9.43,9.57,9,7.71,8.86,15,10.67]]);
$writer->writeSheetRow($sheet1, $header_top1,$textRight);
$writer->writeSheetRow($sheet1, $title1,$titleStyle);
$writer->writeSheetRow($sheet1, $title2,$titleStyle);
$writer->writeSheetRow($sheet1, $title3,$titleStyle);
$writer->writeSheetRow($sheet1, $header_top2 = array('สถานะ : <ไม่ระบุ>'),$textLeft);
$writer->writeSheetRow($sheet1, $header_top2 = array("เงินกู้ : ".$loan_type_name),$textLeft);
$writer->writeSheetRow($sheet1, $header_top2 = array('จ่าย : GSH-เงินสด'),$textLeft);
$writer->writeSheetRow($sheet1, $text_top1,$headerStyle);
$writer->writeSheetRow($sheet1, $text_top2,$headerStyle);

$row = 1;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=16, $end_row=$row, $end_col=18);
$row = 2;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=18);
$row = 3;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=18);
$row = 4;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=18);
$row++;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=1);
$row++;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=1);
$row++;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=1);
$row++;
for ($i = 0;$i<=8;$i++){
    $writer->markMergedCell($sheet1, $start_row=$row, $start_col=$i, $end_row=$row+1, $end_col=$i);
}
for ($i = 15;$i<=18;$i++){
    $writer->markMergedCell($sheet1, $start_row=$row, $start_col=$i, $end_row=$row+1, $end_col=$i);
}
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=9, $end_row=$row, $end_col=11);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=12, $end_row=$row, $end_col=14);
$row++;

$sum_loan_approve = 0;
$sum_period_amount = 0;
$sum_money_per_period = 0;
$sum_pay_amount = 0;
$sum_interest_amount = 0;

$sum_bay_share = 0;
$sum_deduction = 0;
$sum_true_pay = 0;
$num_person = 0;
foreach ($datas as $key => $value){
    $loan_approve = $value['loan_amount'];
    if($value['pay_type'] == 1){
        $pay_type = 'คงต้น';
    }else if($value['pay_type'] == 2){
        $pay_type = 'คงยอด';
    }


    $num_person++;
    $bay_share = 0;
    $deduction = 0;
    $true_pay = $loan_approve - $bay_share - $value['deduct'];
    $data_1[0] = $key+1; //A
    $data_1[1] = $value['member_id']; //B
    $data_1[2] = $value['full_name']; //C
    $data_1[3] = $value['age']; //D
    $data_1[4] = @number_format($value['salary'], 2, '.', ','); //E
    $data_1[5] = @number_format($value['share_month'], 2, '.', ','); //F
    $data_1[6] = @number_format($value['share_collect_value'], 2, '.', ','); //G
    $data_1[7] = @number_format($value['salary']*40, 2, '.', ','); //H
    $data_1[8] = @number_format($value['loan_amount'], 2, '.', ','); //I
    $data_1[9] = @number_format($loan_approve, 2, '.', ','); //J
    $data_1[10] = $value['period_amount']; //K
//    $data_1[11] = @number_format($value['money_per_period'], 2, '.', ','); //L
    $data_1[11] = @number_format($value['total_paid_per_month'], 2, '.', ','); //L
    $data_1[12] = $value['prefix_code']; //M
    $data_1[13] = @number_format($value['pay_amount'], 2, '.', ','); //N
    $data_1[14] = @number_format($value['interest_amount'], 2, '.', ','); //O
    $data_1[15] = @number_format($bay_share, 2, '.', ','); //P
    $data_1[16] = @number_format($value['deduct'], 2, '.', ','); //Q
    $data_1[17] = @number_format($true_pay-($value['pay_amount']+$value['interest_amount']), 2, '.', ','); //R
    $data_1[18] = ''; //S


    if(!empty($value['ref_id'])){
        $row_ref_id = explode("&,", $value['ref_id']);
        $row_prefix_code = explode("&,", $value['prefix_code']);
        $row_pay_amount = explode("&,", $value['pay_amount']);
        $interest_amount = explode("&,", $value['interest_amount']);
        foreach ($row_prefix_code as $prefix_key => $prefix_value){
            $data_1[12] = $row_prefix_code[$prefix_key];
            $data_1[13] = @number_format($row_pay_amount[$prefix_key], 2, '.', ',');
            $data_1[14] = @number_format($interest_amount[$prefix_key], 2, '.', ',');

            $writer->writeSheetRow($sheet1, $data_1, $styleTB);
            $row++;

            $sum_pay_amount += $row_pay_amount[$prefix_key];
            $sum_interest_amount +=  $interest_amount[$prefix_key];
            foreach ($data_1 as $data_1_key => $data_1_value){
                $data_1[$data_1_key] = ''; // set $data_1 เป็น ค่าว่าง
            }
        }
    }else{
        $writer->writeSheetRow($sheet1, $data_1, $styleTB);
        $row++;
    }

    if(!empty($value['guarantee_person_id'])){
        $row_person_id = explode("&,", $value['guarantee_person_id']);
        $row_full_name = explode("&,", $value['guarantee_full_name']);
        $count_person = count($row_person_id);

        for($i = 0;$i<$count_person;$i+=2){
            $data_2[0] = ''; //A
            $data_2[1] = ''; //B
            $data_2[2] = '[ '.@$row_full_name[$i+0].' ]'; //C
            $data_2[3] = ''; //D
            $data_2[4] = ''; //E
            $data_2[5] = ''; //F
            $data_2[6] = @$row_full_name[$i+1]!=''?'[ '.$row_full_name[$i+1].' ]':''; //G
            $data_2[7] = ''; //H
            $data_2[8] = ''; //I
            $data_2[9] = ''; //J
            $data_2[10] = ''; //K
            $data_2[11] = @$i == 0? $pay_type:''; //L
            $data_2[12] = @$i == 0? $value['loan_reason']:''; //M
            $data_2[13] = ''; //N
            $data_2[14] = ''; //O
            $data_2[15] = ''; //P
            $data_2[16] = ''; //Q
            $data_2[17] = ''; //R
            $data_2[18] = @$i == 0? $value['contract_number']:''; //S

            $writer->writeSheetRow($sheet1, $data_2, $styleTB);
            $row++;
            $writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=1);
            $writer->markMergedCell($sheet1, $start_row=$row, $start_col=2, $end_row=$row, $end_col=4);
            $writer->markMergedCell($sheet1, $start_row=$row, $start_col=6, $end_row=$row, $end_col=8);
            $writer->markMergedCell($sheet1, $start_row=$row, $start_col=9, $end_row=$row, $end_col=10);
            $writer->markMergedCell($sheet1, $start_row=$row, $start_col=12, $end_row=$row, $end_col=17);
        }

    }else{
        $data_2[0] = ''; //A
        $data_2[1] = ''; //B
        $data_2[2] = ''; //C
        $data_2[3] = ''; //D
        $data_2[4] = ''; //E
        $data_2[5] = ''; //F
        $data_2[6] = ''; //G
        $data_2[7] = ''; //H
        $data_2[8] = ''; //I
        $data_2[9] = ''; //J
        $data_2[10] = ''; //K
        $data_2[11] = $pay_type; //L
        $data_2[12] = $value['loan_reason']; //M
        $data_2[13] = ''; //N
        $data_2[14] = ''; //O
        $data_2[15] = ''; //P
        $data_2[16] = ''; //Q
        $data_2[17] = ''; //R
        $data_2[18] = $value['contract_number']; //S

        $writer->writeSheetRow($sheet1, $data_2, $styleTB);
        $row++;
        $writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=10);
        $writer->markMergedCell($sheet1, $start_row=$row, $start_col=12, $end_row=$row, $end_col=17);
    }
    $sum_loan_approve += $loan_approve;
    $sum_period_amount += $value['period_amount'];
//    $sum_money_per_period += $value['money_per_period'];
    $sum_money_per_period += $value['total_paid_per_month'];

    $sum_bay_share += $bay_share;
    $sum_deduction += $deduction;
    $sum_true_pay += $true_pay;
}
$sum_loan_approve = @number_format($sum_loan_approve, 2, '.', ',');
$sum_period_amount = @number_format($sum_period_amount, 2, '.', ',');
$sum_money_per_period = @number_format($sum_money_per_period, 2, '.', ',');
$sum_pay_amount = @number_format($sum_pay_amount, 2, '.', ',');
$sum_interest_amount = @number_format($sum_interest_amount, 2, '.', ',');
$sum_bay_share = @number_format($sum_bay_share, 2, '.', ',');
$sum_deduction = @number_format($sum_deduction, 2, '.', ',');
$sum_true_pay = @number_format($sum_true_pay, 2, '.', ',');
$footer1 = array("รวมจ่าย CSH - เงิดสด จำนวน $num_person ราย เป็นเงิน","","","","","","","","",$sum_loan_approve,"","$sum_money_per_period","",$sum_pay_amount,$sum_interest_amount,"$sum_bay_share","$sum_deduction","$sum_true_pay","");
$footer2 = array("รวม 00 - <ไม่ระบุ> จำนวน $num_person ราย เป็นเงิน","","","","","","","","",$sum_loan_approve,"","$sum_money_per_period","",$sum_pay_amount,$sum_interest_amount,"$sum_bay_share","$sum_deduction","$sum_true_pay","");
$footer3 = array("รวมคำขอกู้ ทั้งหมด จำนวน $num_person ราย เป็นเงิน","","","","","","","","",$sum_loan_approve,"","$sum_money_per_period","",$sum_pay_amount,$sum_interest_amount,"$sum_bay_share","$sum_deduction","$sum_true_pay","");

$row++;
$writer->writeSheetRow($sheet1, $footer1,$headerStyle);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=8);
$row++;
$writer->writeSheetRow($sheet1, $footer2,$headerStyle);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=8);
$row++;
$writer->writeSheetRow($sheet1, $footer3,$headerStyle);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=8);

//exit;


$filename = "รายงานการรับคำขอกู้.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();