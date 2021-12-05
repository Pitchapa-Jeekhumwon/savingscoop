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

$textBorderBottom = [
    'font'=>'Cordia New',
    'font-size'=>12,
    'border-style' => 'thin',
    'border'=>'bottom'
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


$textStyle = array(
    $textCenterBorder,
    $textLeftBorder,
    $textLeftBorder,
    $textLeftBorder,
    $textLeftBorder,
    $textCenterBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textLeftBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textRightBorder,
    $textCenterBorder,
    $textCenterBorder,
    $textCenterBorder
);

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


$footerBorderbottom = array(
    $textCenterBorder,
    $textCenterBorder,
    $textCenterBorder,
    $textCenterBorder,
    $textNull,
    $textBorderBottom,
    $textBorderBottom,
    $textNull,
    $textBorderBottom,
    $textBorderBottom,
    $textBorderBottom,
    $textNull,
    $textBorderBottom,
    $textBorderBottom,
    $textNull,
    $textBorderBottom,
    $textBorderBottom,
    $textNull
);

$footerStyle = array(
    $textCenterBorder,
    $textCenterBorder,
    $textCenterBorder,
    $textCenterBorder,
    $textNull,
    $textCenter,
    $textNull,
    $textNull,
    $textCenter,
    $textNull,
    $textNull,
    $textNull,
    $textCenter,
    $textNull,
    $textNull,
    $textCenter,
    $textNull,
    $textNull
);







$sheet1 = 'รายงานสรุป';
$title = array(''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string');
$title1 = array(@$_SESSION['COOP_NAME']);
$title2 = array("รายงานการจ่าย".$loan_type_name);
$title3 = array("ระหว่างวันที่ ".$_GET['date_start']." ถึง ".$_GET['date_end']);
$header = array("string","string","string","string","string","string","string","string","string","string","string","string","string","string","string","string","string");
$text_top1 = array("ลำดับ","เลขสัญญา","เลขทะเบียน","ชื่อ - นามสกุล","กลุ่มงาน","วิธีเก็บ","การชำระ","","อนุมัติเงินกู้","รายการหัก","","","","ซื้อหุ้น","อื่นๆ","จ่ายจริง","เลขที่บัญชี","ผู้รับเงิน");
$text_top2 = array("","","","","","","งวด","ต่องวด","","เลขที่ใบเสร็จ","","เงินต้น","ดอกเบี้ย","","","","","");
$year = date("Y")+543;
$date = date('d/m/');
$time_now = date('H:i:s');
$date_now = $date.$year.' '.$time_now;
//echo $date_now;exit;
$header_top1 = array("","","","","","","","","","","","","","","","","เวลาที่พิมพ์ : ".$date_now,"");

//$writer->writeSheetHeader($sheet1, $title, $col_options = ['suppress_row'=>true] );
$writer->writeSheetHeader($sheet1, $title,$col_options = ['widths'=>[4.86,10.43,10.43,27,40,10.43,14.43,10.57,11.29,10.43,10.43,11.29,9.43,9.57,9.57,16.57,16.71,8.86,10]]);
$writer->writeSheetRow($sheet1, $header_top1,$textRight);
$writer->writeSheetRow($sheet1, $title1,$titleStyle);
$writer->writeSheetRow($sheet1, $title2,$titleStyle);
$writer->writeSheetRow($sheet1, $title3,$titleStyle);
$writer->writeSheetRow($sheet1, $header_top2 = array("เงินกู้ : ".$loan_type_name),$textLeft);
$writer->writeSheetRow($sheet1, $header_top3 = array("จ่าย : ".$transfer_type = 'CSH - เงินสด'),$textLeft);
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

$num_person = 0;

$sum_deduct_emergency_principle = 0;
$sum_deduct_normal_principle = 0;
$sum_deduct_special_principle = 0;

$sum_deduct_emergency_interest = 0;
$sum_deduct_normal_interest = 0;
$sum_deduct_special_interest = 0;

$sum_deduct_emergency_per_period = 0;
$sum_deduct_normal_per_period = 0;
$sum_deduct_special_per_period = 0;

foreach ($datas as $key => $value){
    $num_person++;
    $loan_approve = $value['loan_amount'];
    if($value['pay_type'] == 1){
        $pay_type = 'คงต้น';
    }else if($value['pay_type'] == 2){
        $pay_type = 'คงยอด';
    }
    $r = 0;
    $bay_share = 0;
    $deduction = 0;
    $true_pay = $loan_approve - $bay_share - $deduction;
    $data[$r][0] = $key+1;
    $data[$r][1] = $value['contract_number'];
    $data[$r][2] = $value['member_id'];
    $data[$r][3] = $value['full_name'];
    $data[$r][4] = $value['mem_group_name'];
    $data[$r][5] = $pay_type;
    $data[$r][6] = @number_format($value['period_amount'], 2, '.', ',');
//    $data[$r][7] = @number_format($value['money_per_period'], 2, '.', ',');
    $data[$r][7] = @number_format($value['total_paid_per_month'], 2, '.', ',');
    $data[$r][8] = @number_format($value['loan_amount'], 2, '.', ',');
    $data[$r][9] = $value['deduct_receipt_id'];
    $data[$r][10] = '';
    $data[$r][11] = '';
    $data[$r][12] = '';
    $data[$r][13] = '';//ซื้อหุ้น
    $data[$r][14] = '';//อื่นๆ
    $data[$r][15] = @number_format($loan_approve, 2, '.', ',');;
    $data[$r][16] = $value['transfer_bank_account_id'];
    $data[$r][17] = '';



    if(!empty($value['prefix_code'])){
        $row_prefix_code = explode("&,", $value['prefix_code']);
        $row_pay_amount = explode("&,", $value['pay_amount']);
        $interest_amount = explode("&,", $value['interest_amount']);
        $deduct_loan_type_id = explode("&,", $value['deduct_loan_type_id']);
        $deduct_money_per_period = explode("&,", $value['deduct_money_per_period']);
        foreach ($row_prefix_code as $prefix_key => $prefix_value){
            if($r > 0){
                foreach ($data[$r-1] as $data_1_key => $data_1_value){
                    $data[$r][$data_1_key] = ''; // set $data_1 เป็น ค่าว่าง
                }
            }
            $data[$r][10] = $row_prefix_code[$prefix_key];
            $data[$r][11] = @number_format($row_pay_amount[$prefix_key], 2, '.', ',');
            $data[$r][12] = @number_format($interest_amount[$prefix_key], 2, '.', ',');


            if($deduct_loan_type_id[$prefix_key]=='1'){
                $sum_deduct_emergency_principle += $row_pay_amount[$prefix_key];
                $sum_deduct_emergency_interest += $interest_amount[$prefix_key];
                $sum_deduct_emergency_per_period += $deduct_money_per_period[$prefix_key];
            }else if($deduct_loan_type_id[$prefix_key]=='2'){
                $sum_deduct_normal_principle += $row_pay_amount[$prefix_key];
                $sum_deduct_normal_interest += $interest_amount[$prefix_key];
                $sum_deduct_normal_per_period += $deduct_money_per_period[$prefix_key];
            }else if($deduct_loan_type_id[$prefix_key]=='3'){
                $sum_deduct_special_principle += $row_pay_amount[$prefix_key];
                $sum_deduct_special_interest += $interest_amount[$prefix_key];
                $sum_deduct_special_per_period += $deduct_money_per_period[$prefix_key];
            }

            $sum_pay_amount += $row_pay_amount[$prefix_key];
            $sum_interest_amount +=  $interest_amount[$prefix_key];
            $r ++;
        }
    }
    $r = 0;
    foreach ($data as $key2 => $value2){
        $writer->writeSheetRow($sheet1, $data[$key2], $textStyle);
        $row++;
    }
    $data = array();
    $sum_loan_approve += $loan_approve;
    $sum_period_amount += $value['period_amount'];
//    $sum_money_per_period += $value['money_per_period'];
    $sum_money_per_period += $value['total_paid_per_month'];

    $sum_bay_share += $bay_share;
    $sum_deduction += $deduction;
    $sum_true_pay += $true_pay;
}
//exit;
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
$footer1 = array("รวมจ่ายเป็น $transfer_type จำนวน $num_person ราย เป็นเงิน","","","","","","","$sum_money_per_period",$sum_loan_approve,"","",$sum_pay_amount,$sum_interest_amount,"","",$sum_true_pay,"","");
$row++;
$writer->writeSheetRow($sheet1, $footer1,$textStyle);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=5);
$row++;
$writer->writeSheetRow($sheet1, array(''));
$row++;
$writer->writeSheetRow($sheet1, array('','สรุปรายการหักหนี้เก่า','',''  ),$headerStyle);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=1, $end_row=$row, $end_col=3);
$row++;



$sum_deduct_principle = $sum_deduct_emergency_principle + $sum_deduct_normal_principle + $sum_deduct_special_principle;
$sum_deduct_interest = $sum_deduct_emergency_interest + $sum_deduct_normal_interest + $sum_deduct_special_interest;
$sum_deduct_per_period = $sum_deduct_emergency_per_period + $sum_deduct_normal_per_period + $sum_deduct_special_per_period;
$sum_deduct_emergency_principle = @number_format($sum_deduct_emergency_principle, 2, '.', ',');
$sum_deduct_emergency_interest  = @number_format($sum_deduct_emergency_interest, 2, '.', ',');
$sum_deduct_emergency_per_period = @number_format($sum_deduct_emergency_per_period, 2, '.', ',');
$sum_deduct_normal_principle = @number_format($sum_deduct_normal_principle, 2, '.', ',');
$sum_deduct_normal_interest = @number_format($sum_deduct_normal_interest, 2, '.', ',');
$sum_deduct_normal_per_period = @number_format($sum_deduct_normal_per_period, 2, '.', ',');
$sum_deduct_special_principle = @number_format($sum_deduct_special_principle, 2, '.', ',');
$sum_deduct_special_interest = @number_format($sum_deduct_special_interest, 2, '.', ',');
$sum_deduct_special_per_period = @number_format($sum_deduct_special_per_period, 2, '.', ',');
$sum_deduct_principle = @number_format($sum_deduct_principle, 2, '.', ',');
$sum_deduct_interest = @number_format($sum_deduct_interest, 2, '.', ',');
$sum_deduct_per_period = @number_format($sum_deduct_per_period, 2, '.', ',');

$footer2[0] = array( '','เงินต้น','ดอกเบี้ย', 'ต่องวด' );
$footer2[1] = array( 'ฉุกเฉิน', $sum_deduct_emergency_principle, $sum_deduct_emergency_interest, $sum_deduct_emergency_per_period );
$footer2[2] = array( 'สามัญ', $sum_deduct_normal_principle, $sum_deduct_normal_interest, $sum_deduct_normal_per_period );
$footer2[3] = array( 'พิเศษ', $sum_deduct_special_principle, $sum_deduct_special_interest, $sum_deduct_special_per_period);
$footer2[4] = array( '', $sum_deduct_principle, $sum_deduct_interest, $sum_deduct_per_period );

array_push($footer2[1],' ','','','','','','','','','','','','');
array_push($footer2[2],' ','ประธานกรรมการ','','','รองประธานด้านอำนวยการ','','','','ผู้เบิกจ่าย','','','ผู้ตรวจจ่าย','');
array_push($footer2[3],' ','รองประธานด้านธุรกิจ/เลขานุการ','','','รองประธานด้านสวัสดิการ/เหรัญญิก','','','','','','','','');
array_push($footer2[4],' ','(ผู้จัดการ)','','','(ผู้จัดการ)','','','','','','','','');

$writer->markMergedCell($sheet1, $start_row=$row, $start_col=5, $end_row=$row, $end_col=6);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=8, $end_row=$row, $end_col=10);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=12, $end_row=$row, $end_col=13);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=15, $end_row=$row, $end_col=16);
$writer->writeSheetRow($sheet1, $footer2[0], $footerBorderbottom);
$row++;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=5, $end_row=$row, $end_col=6);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=8, $end_row=$row, $end_col=10);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=12, $end_row=$row, $end_col=13);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=15, $end_row=$row, $end_col=16);
$writer->writeSheetRow($sheet1, $footer2[1], $footerBorderbottom);
$row++;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=5, $end_row=$row, $end_col=6);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=8, $end_row=$row, $end_col=10);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=12, $end_row=$row, $end_col=13);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=15, $end_row=$row, $end_col=16);
$writer->writeSheetRow($sheet1, $footer2[2], $footerStyle);
$row++;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=5, $end_row=$row, $end_col=6);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=8, $end_row=$row, $end_col=10);
$writer->writeSheetRow($sheet1, $footer2[3], $footerStyle);
$row++;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=5, $end_row=$row, $end_col=6);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=8, $end_row=$row, $end_col=10);
$writer->writeSheetRow($sheet1, $footer2[4], $footerStyle);
//exit;


$filename = "รายงานการจ่ายเงินกู้.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();