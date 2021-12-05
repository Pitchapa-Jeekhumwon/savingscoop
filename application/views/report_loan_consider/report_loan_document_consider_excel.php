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

$titleStylel = array(
    'font'=>'Cordia New',
    'font-size'=>16,
    'font-style'=>'bold',
    'halign'=>'left',
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
$styleTl = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'left',
    'border-style' => 'thin',
    'border'=>'left,right,top'];
$styleTr = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'right',
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
$styleBr = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'right',
    'border-style' => 'thin',
    'border'=>'left,right,bottom'
];
$styleBl = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'left',
    'border-style' => 'thin',
    'border'=>'left,right,bottom'
];

$styleLR = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'center',
    'border-style' => 'thin',
    'border'=>'left,right'
];

$styleLRl = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'left',
    'border-style' => 'thin',
    'border'=>'left,right'
];

$styleLRr = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'right',
    'border-style' => 'thin',
    'border'=>'left,right'
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
$styleTBl = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'left',
    'border-style' => 'thin',
    'border'=>'left,right,top,bottom'
];
$styleTBr = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'right',
    'border-style' => 'thin',
    'border'=>'left,right,top,bottom'
];
$styleTBC = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'font-style'=>'bold',
    'fill'=>'#FFFFFF',
    'halign'=>'center',
    'border-style' => 'thin',
    'border'=>'left,right',
    'hidden' => true
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
    'font-size'=>14,
    'halign'=>'left'
];

$textRight = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'halign'=>'right'
];

$textCenter = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'halign'=>'center'
];

$textBorderBottomRight = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'halign'=>'center',
    'font-style'=>'bold',
    'border-style' => 'thin',
    'border'=>'bottom,right'
];
$textBorderBottom = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'halign'=>'center',
    'font-style'=>'bold',
    'border-style' => 'thin',
    'border'=>'bottom'
];
$textBorderBottom_l = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'halign'=>'left',
    'font-style'=>'bold',
    'border-style' => 'thin',
    'border'=>'bottom'
];
$textBorderBottom_r = [
    'font'=>'Cordia New',
    'font-size'=>14,
    'halign'=>'right',
    'font-style'=>'bold',
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


$bodyStyle0 = array(
    $styleTBC,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTBC
);
$bodyStyle1 = array(
    $styleT,
    $styleTl,
    $styleT,
    $styleTr,
    $styleTr,
    $styleTBr,
    $styleTr,
    $styleTr,
    $styleTr,
    $styleTl,
    $styleTr,
    $styleTr,
    $styleTl,
    $styleTr,
    $styleTr,
    $styleTl
);

$bodyStyle2 = array(
    $styleLR,
    $styleLRr,
    $styleLR,
    $styleLRr,
    $styleLRr,
    $styleLRr,
    $styleLRr,
    $styleLRr,
    $styleLRr,
    $styleLRr,
    $styleLRr,
    $styleLRr,
    $styleLRr,
    $styleLRr,
    $styleLRr,
    $styleLR
);

$bodyStyle3 = array(
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLRr,
    $styleLR,
    $styleLR,
    $styleTr,
    $styleBr,
    $styleLRr,
    $styleLRl,
    $styleLRr,
    $styleLRr,
    $styleLRl,
    $styleLRr,
    $styleLRr,
    $styleLR
);

$bodyStyle4 = array(
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLRr,
    $styleLR,
    $styleLR,
    $styleLRr,
    $styleLRr,
    $styleLRr,
    $styleLRl,
    $styleLRr,
    $styleLR,
    $styleLRl,
    $styleLRr,
    $styleLRr,
    $styleLR
);

$bodyStyle5 = array(
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLRr,
    $styleLR,
    $styleLR,
    $styleLRr,
    $styleLRr,
    $styleLRr,
    $styleLRl,
    $styleLRr,
    $styleLRr,
    $styleLRl,
    $styleLRr,
    $styleLRr,
    $styleLRl
);

$bodyStyle6 = array(
    $styleLR,
    $styleLR,
    $styleLRr,
    $styleLRr,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLRr,
    $styleLRr,
    $styleLRl,
    $styleLRr,
    $styleLRr,
    $styleLRl,
    $styleLRr,
    $styleLRr,
    $styleLRl
);

$bodyStyle7 = array(
    $styleLR,
    $styleLR,
    $styleLRr,
    $styleLRr,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLRr,
    $styleLRr,
    $styleLRl,
    $styleLRr,
    $styleLRr,
    $styleLRl,
    $styleLRr,
    $styleLRr,
    $styleLRl
);

$bodyStyle8 = array(
    $styleB,
    $styleBl,
    $styleB,
    $styleTBr,
    $styleBr,
    $styleTBr,
    $styleTBr,
    $styleTBr,
    $styleTBr,
    $styleBl,
    $styleTBr,
    $styleBr,
    $styleBl,
    $styleTBr,
    $styleB,
    $styleB,
    $styleB,
    $styleB
);

$text_topStyle1 = array(
    $styleT,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleT
);

$headerStyle1 = array(
    $styleLR,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleTB,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleT,
    $styleLR
);

$headerStyle2 = array(
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR
);

$headerStyle3 = array(
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleT,
    $styleB,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleLR,
    $styleT,
    $styleLR
);

$headerStyle8 = array(
    $styleB,
    $styleB,
    $styleB,
    $styleTB,
    $styleB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleTB,
    $styleB,
    $styleTB,
    $styleB,
    $styleB,
    $styleTB,
    $styleB,
    $styleB,
    $styleB,
    $styleB
);


$footerBorderbottom = array(
    $textBorderBottom,
    $textBorderBottom,
    $textBorderBottom_r,
    $textBorderBottom,
    $textBorderBottom_l,
    $textBorderBottom,
    $textBorderBottom_r,
    $textBorderBottom,
    $textBorderBottom,
    $textBorderBottom_r,
    $textBorderBottom,
    $textBorderBottom_l,
    $textBorderBottom_r,
    $textBorderBottom_r,
    $textBorderBottom,
    $textBorderBottomRight,
    $textBorderBottomRight,
    $textBorderBottomRight
);

$footerStyle = array(
    $textCenter,
    $textCenter,
    $textCenter,
    $textRight,
    $textCenter,
    $textCenter,
    $textCenter,
    $textCenter,
    $textCenter,
    $textNull,
    $textNull,
    $textNull,
    $textRight,
    $textCenter,
    $textCenter,
    $textLeft,
    $textNull,
    $textNull
);







$sheet1 = 'รายงานสรุป';
$title = array(''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string');
$header_top1 = array("","","","","","","","","","","","","","","รวมจำนวน : ".$num_presun." ราย","");
$title1 = array("เรื่อง เพื่อพิจารณาให้เงินกู้ประเภทสามัญ วงเงินกู้ 50 เท่าของเงินเดือน กรณีให้แล้วเสร็จภายใน 5 วันทำการ วงเงินไม่เกิน 1,000,000 บาท");
$title2 = array("","","","","","1. อายุการเป็นสมาชิก 3 ปี - 5 ปี","","", "2. ผ่อนชำระได้ไม่เกิน 140 งวด");
$title3 = array("","","","","","3. อายุไม่เกิน 60 ปี","","", "4. ต้องมีเงินคงเหลือ ไม่ต่ำกว่า 3,000.- บาท ต่อเดือน");
$title5 = array("ระหว่างวันที่ ".$_GET['date_start']." ถึง ".$_GET['date_end']);
$header = array("string","string","string","string","string","string","string","string","string","string","string","string","string","string","string","string","string");
$text_top1 = array("","สมาชิกขอกู้เงิน","","","","","","","","ผู้ค้ำประกัน","","","","","","");
$text_top2 = array("","ยศ - ชื่อ - ชื่อสกุล/","เลขที่/","รายได้"            ,"วงเงินกู้"    ,"ต้น"        ,"ทุนเรือนหุ้น"       ,"ยอดกู้เดิม (ส)"  ,"ยอดกู้ใหม่"       ,"ยศ - ชื่อ - ชื่อสกุล/","รายได้"           ,"เงินค้ำประกัน","ยศ - ชื่อ - ชื่อสกุล/","รายได้"           ,"เงินค้ำประกัน",'');
$text_top3 = array("","ชำระ (งวด)/"  ,"สมาชิก"   ,"(หัก) หนี้เคหะฯ"     ,"ขอกู้"       ,"ดอกเบี้ย"    ,"เงินฝาก","ชำระแล้ว (ส) " ,"(หัก)กลบหนี้เดิม"  ,"เลขที่สมาชิก/"      ,"(หัก) หนี้เคหะฯ"    ,"ใช้ค้ำแล้ว"   ,"เลขที่สมาชิก/"      ,"(หัก) หนี้เคหะฯ"    ,"ใช้ค้ำแล้ว"   ,'');
$text_top4 = array("","อายุสมาชิก/"    ,"ปี สช./"  ,"(หัก) ทุนเรือนหุ้น"    ,""          ,""          ,"ออมทรัพย์"               ,"หนี้ค้างชำระ"    ,"1. สามัญคงเหลือ" ,"ปีสมาชิก/"         ,"(หัก) ทุนเรือนหุ้น"   ,"คงเหลือค้ำ"  ,"ปีสมาชิก/"         ,"(หัก) ทุนเรือนหุ้น"   ,"คงเหลือค้ำ"  ,'');
$text_top5 = array("ลำดับ","         "    ,""        ,"(หัก) หนี้เงินกู้สอ.รพ" ,""          ,""          ,"หุ้น+เงินฝาก"      ,""             ,"1. ฉุกเฉินคงเหลือ","อายุสมาชิก/"       ,"(หัก) หนี้เงินกู้สอ.รพ",""          ,"อายุสมาชิก/"       ,"(หัก) หนี้เงินกู้สอ.รพ",""          ,'ผลการพิจารณา');
$text_top6 = array("","         "    ,""        ,"(หัก) หนี้ที่อื่น"       ,""          ,""          ,"15% ของยอด"     ,"เกณฑ์"         ,"(หัก) เงินฝากเพิ่ม",""                ,"(หัก) หนี้ที่อื่น"      ,""          ,""                ,"(หัก) หนี้ที่อื่น"      ,""          ,'');
$text_top7 = array("","วัน เดือน ปี เกิด","หน่วยงาน" ,"(หัก) ค่าใช้จ่ายอื่นๆ"  ,"เหตุผลขอกู้"  ,""          ,"ขอกู้"            ,"1/5 ของยอด"   ,"(หัก) ประกันชีวิต" ,""                ,"(หัก) ค่าใช้จ่ายอื่นๆ" ,""          ,""                ,"(หัก) ค่าใช้จ่ายอื่นๆ" ,""          ,'');
$text_top8 = array("","         "    ,""        ,"(หัก) เงินคงเหลือ"   ,""          ,""          ,""               ,"กู้เดิม"         ,"( % )"         ,""                ,""                ,""          ,""                ,""                ,""          ,'');
$text_top9 = array("","เบอร์โทร/"     ,""        ,"ยอดชำระได้"        ,""          ,"ยอดชำระใหม่","ต้องมีเงินฝากเพิ่ม"  ,"หรือชำระ 8 งวด","คงเหลือจ่ายจริง"  ,"เบอร์โทร"         ,"เงินคงเหลือ"       ,""          ,"เบอร์โทร"         ,"เงินคงเหลือ"       ,""          ,'');
$year = date("Y")+543;
$date = date('d/m/');
$time_now = date('H:i:s');
$date_now = $date.$year.' '.$time_now;

$writer->writeSheetHeader($sheet1, $title,$col_options = ['widths'=>[4.86,20,15,15,15,15,15,15,15,20,15,15,15,15,15,20,15,15,15], 'suppress_row' => 1]);
$writer->writeSheetRow($sheet1, $header_top1,$textRight);
$writer->writeSheetRow($sheet1, $title1,$titleStyle);
$writer->writeSheetRow($sheet1, $title2,$titleStylel);
$writer->writeSheetRow($sheet1, $title3,$titleStylel);
$writer->writeSheetRow($sheet1, $title5,$titleStyle);
$writer->writeSheetRow($sheet1, $text_top1,$text_topStyle1);
$writer->writeSheetRow($sheet1, $text_top2,$headerStyle1);
$writer->writeSheetRow($sheet1, $text_top3,$headerStyle2);
$writer->writeSheetRow($sheet1, $text_top4,$headerStyle2);
$writer->writeSheetRow($sheet1, $text_top5,$headerStyle3);
$writer->writeSheetRow($sheet1, $text_top6,$headerStyle2);
$writer->writeSheetRow($sheet1, $text_top7,$headerStyle2);
$writer->writeSheetRow($sheet1, $text_top8,$headerStyle2);
$writer->writeSheetRow($sheet1, $text_top9,$headerStyle8);

$row = 0;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=14, $end_row=$row, $end_col=15);
$row = 1;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=14);
$row = 2;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=5, $end_row=$row, $end_col=7);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=8, $end_row=$row, $end_col=10);
$row = 3;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=5, $end_row=$row, $end_col=7);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=8, $end_row=$row, $end_col=10);
$row = 4;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=15);
$row++;
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=1, $end_row=$row, $end_col=8);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=9, $end_row=$row, $end_col=14);
$row+=8;
$row++;

$sum_loan_approve = 0;
$sum_period_amount = 0;
$sum_money_per_period = 0;
$sum_pay_amount = 0;
$sum_interest_amount = 0;

$sum_bay_share = 0;
$sum_deduction = 0;
$sum_true_pay = 0;

$num_presun_approve = 0;

$sum_loan = 0;
$sum_loan_approve = 0;




foreach ($datas as $key => $value){
    $r = 0;

    $housing_debt = 0; // (หัก) หนี้เคหะฯ
    $share_capital = 0; // (หัก) ทุนเรือนหุ้น
    $loan_debt = 0; //(หัก) หนี้เงินกู้สอ.รพ
    $other_debt = 0; //(หัก) หนี้ที่อื่น

    $other_expenses = 0; //(หัก) ค่าใช้จ่ายอื่นๆ
    $less_balance = 0; //(หัก) เงินคงเหลือ

    $deposit = 0; //เงินฝาก
    $payment_amount = $value['salary'] - $housing_debt - $loan_debt - $other_debt - $other_expenses - $less_balance; //ยอดชำระได้

    $additional_deposit = ($value['loan_amount']*0.15)-($value['share_collect_value']+$deposit);//เงินฝากเพิ่ม
    if($additional_deposit < 0){
        $additional_deposit = 0;
    }
    $life_insurance = 0; // (หัก) ประกันชิวิต

    $sum_original_loan_amount = 0; //ยอดกู้เดิม
    $settle_normal = 0 ;//หักกลบหนี้ สามัญ
    $settle_emergency = 0;//หักกลบหนี้ ฉุกเฉิน
    $sum_overdue_loan_amount = 0; //ยอดกู้เดิม
    $max_guarantee_amount = $value['guarantee_salary']*50;

    $sum_loan += $value['loan_amount'];
    $checkbox_approve = '☐';
    $checkbox_not_approve = '☐';
    if ($value['loan_status'] == '1') {
        $checkbox_approve = '🗹';
        $num_presun_approve++;
        $sum_loan_approve += $value['loan_amount'];
    }else if ($value['loan_status'] == '5'){
        $checkbox_not_approve = '🗹';
    }

    if ($value['loan_reason'] == ''){
        $value['loan_reason'] = '-';
    }

    if($value['principal_payment'] == ''){
        $value['principal_payment'] = 0;
    }
    if($value['interest'] == ''){
        $value['interest'] = 0;
    }
    if (!empty($value['birthday'])){
        $birthday_arr = explode("-", $value['birthday']);
        $birthday = $birthday_arr[2].' '.$month_arr[intval($birthday_arr[1])].' '.($birthday_arr[0]+543);

    }
    if(!empty($value['ref_id'])) {
        $ref_id = explode("&,", $value['ref_id']);
        $pay_amount = explode("&,", $value['pay_amount']);
        $interest_amount = explode("&,", $value['interest_amount']);
        $loan_type_id = explode("&,", $value['loan_type_id']);
        $overdue_loan_amount = explode("&,", $value['overdue_loan_amount']);


        foreach ($ref_id as $ref_key => $ref_value){
//            echo  $loan_type_id[$ref_key];
            if($loan_type_id[$ref_key] == '1'){ //ฉุกเฉิน
                $settle_emergency += $pay_amount[$ref_key];
//                echo $pay_amount[$ref_key];
                $settle_emergency += $interest_amount[$ref_key];
            }else if($loan_type_id[$ref_key] == '2'){ //สามัญ
                $settle_normal += $pay_amount[$ref_key];
                $settle_normal += $interest_amount[$ref_key];
            }

            $sum_original_loan_amount += $pay_amount[$ref_key];
            $sum_original_loan_amount += $interest_amount[$ref_key];

            $sum_overdue_loan_amount += $overdue_loan_amount[$ref_key];
        }
    }
    $actually_pay = $value['loan_amount'] - $sum_original_loan_amount - $settle_normal - $settle_emergency - $additional_deposit - $life_insurance;//คงเหลือจ่ายจริง
    //    🗹  ☐  🗷 เช็คบล็อก
    $data[$r][0] = array(
        $key+1,
        $value['firstname'],
        $value['member_id']            ,
        @number_format($value['salary'], 2, '.', ','),
        @number_format($value['salary']*50, 2, '.', ','),
        @number_format($value['principal_payment'], 2, '.', ','),
        @number_format($value['share_collect_value'], 2, '.', ','),
        @number_format($sum_overdue_loan_amount, 2, '.', ','),
        @number_format($value['loan_amount'], 2, '.', ','),
        "",
        "",
        "",
        "",
        "",
        "",
        $checkbox_approve.' อนุมัติ'
    );
    $data[$r][1] = array(
        "",
        $value['lastname'],
        $value['member_year'],
        @number_format($housing_debt, 2, '.', ','),
        @number_format($value['loan_amount'], 2, '.', ','),
        @number_format($value['interest'], 2, '.', ','),
        @number_format($deposit, 2, '.', ','),
        @number_format(($sum_overdue_loan_amount-$sum_original_loan_amount), 2, '.', ','),
        @number_format($sum_original_loan_amount, 2, '.', ','),
        "",
        "",
        "",
        "",
        "",
        "",
        'จำนวน '.@number_format($value['loan_amount'], 2, '.', ',').' บาท'
    );
    $data[$r][2] = array(
        "",
        'ชำระ '.$value['period_amount'].' งวด',
        "",
        @number_format($share_capital, 2, '.', ','),
        "",
        "",
        @number_format(($value['share_collect_value']+$deposit), 2, '.', ','),
        @number_format($sum_original_loan_amount, 2, '.', ','),
        @number_format($settle_normal, 2, '.', ','),
        "",
        "",
        "",
        "",
        "",
        "",
        ''
    );
    $data[$r][3] = array(
        "",
        "",
        "",
        @number_format($loan_debt, 2, '.', ','),
        "",
        "",
        @number_format(($value['loan_amount']*0.15), 2, '.', ','),
        @number_format(($sum_overdue_loan_amount*0.2), 2, '.', ','),
        @number_format($settle_emergency, 2, '.', ','),
        "",
        "",
        "",
        "",
        "",
        "",
        ""
    );
    $data[$r][4] = array(
        "",
        'อายุ '.$value['age'],
        "",
        @number_format($other_debt, 2, '.', ','),
        "",
        "",
        "",
        "",
        @number_format($additional_deposit, 2, '.', ','),
        "",
        "",
        "",
        "",
        "",
        "",
        ''
    );
    $data[$r][5] = array(
        "",
        $birthday,
        $value['short_mem_group_name'] ,
        @number_format($other_expenses, 2, '.', ',')  ,
        $value['loan_reason'],
        "",
        "",
        "",
        @number_format($life_insurance, 2, '.', ','),
        "",
        "",
        "",
        "",
        "",
        "",
        $checkbox_not_approve.' ไม่อนุมัติ');
    $data[$r][6] = array(
        "",
        "",
        "",
        @number_format($less_balance, 2, '.', ','),
        "",
        "",
        "",
        "",
        "50%",
        "",
        "",
        "",
        "",
        "",
        "",
        'เนื่องจาก .................');
    $data[$r][7] = array(
        "",
        $value['tel'],
        "",
        @number_format($payment_amount, 2, '.', ','),
        "",
        @number_format($value['principal_payment']+$value['interest'], 2, '.', ','),
        @number_format($additional_deposit, 2, '.', ','),
        "ชำระ 0 งวด",
        @number_format($actually_pay, 2, '.', ','),
        "",
        "",
        "",
        "",
        "",
        "",
        '.........................');

    if(!empty($value['guarantee_person_id'])) {
        foreach ($value['guarantee_persun'] as $gua_key => $gua_value) {
            $max_guarantee_amount = @($gua_value['guarantee_salary']*50);

            $housing_debt = 0; // (หัก) หนี้เคหะฯ
            $share_capital = 0; // (หัก) ทุนเรือนหุ้น
            $loan_debt = 0; //(หัก) หนี้เงินกู้สอ.รพ
            $other_debt = 0; //(หัก) หนี้ที่อื่น
            $other_expenses = 0; //(หัก) ค่าใช้จ่ายอื่นๆ

            $less_balance = @($gua_value['guarantee_salary']-$housing_debt-$share_capital-$loan_debt-$other_debt-$other_expenses); // เงินคงเหลือ

            $guarantee_amount = 0;//ใช้เงินค้ำประกันไปแล้ว
            $nember = 9 + ($gua_key * 3);
            if ($nember > 12) {
                if ($number % 2 == 0) {
                    $nember = 9;
                    $r++;
                } else {
                    $nember = 9 + 3;
                }
                for ($i = 0; $i <= 7; $i++) {
                    $data[$r][$i] = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", '');
                }
            }

            $data[$r][0][$nember] = $gua_value['guarantee_first_name'];
            $data[$r][1][$nember] = $gua_value['guarantee_last_name'];

            $data[$r][2][$nember] = 'เลขที่สมาชิก '.$gua_value['guarantee_person_id'];
            $data[$r][3][$nember] = 'อายุสมาชิก '.$gua_value['guarantee_member_year'];
            $data[$r][4][$nember] = 'อายุ '.$gua_value['guarantee_age'];
            $data[$r][7][$nember] = $gua_value['guarantee_tel'];
            $data[$r][0][$nember + 1] = @number_format($gua_value['guarantee_salary'], 2, '.', ',');
            $data[$r][1][$nember + 1] = @number_format($housing_debt, 2, '.', ',');
            $data[$r][2][$nember + 1] = @number_format($share_capital, 2, '.', ',');
            $data[$r][3][$nember + 1] = @number_format($loan_debt, 2, '.', ',');
            $data[$r][4][$nember + 1] = @number_format($other_debt, 2, '.', ',');
            $data[$r][5][$nember + 1] = @number_format($other_expenses, 2, '.', ',');
            $data[$r][7][$nember + 1] = @number_format($less_balance, 2, '.', ',');
            $data[$r][0][$nember + 2] = @number_format($max_guarantee_amount, 2, '.', ',');
            $data[$r][1][$nember + 2] = @number_format($guarantee_amount, 2, '.', ',');
            $data[$r][2][$nember + 2] = @number_format(($max_guarantee_amount - $guarantee_amount), 2, '.', ',');

        }
    }else{
        $writer->markMergedCell($sheet1, $start_row=$row, $start_col=9, $end_row=$row+7, $end_col=14);
    }

    $data_style[0] = $bodyStyle1;
    $data_style[1] = $bodyStyle2;
    $data_style[2] = $bodyStyle3;
    $data_style[3] = $bodyStyle4;
    $data_style[4] = $bodyStyle5;
    $data_style[5] = $bodyStyle6;
    $data_style[6] = $bodyStyle7;
    $data_style[7] = $bodyStyle8;

    foreach ($data as $k => $v){
        if($k > 0){
            $writer->markMergedCell($sheet1, $start_row=$row, $start_col=1, $end_row=$row+7, $end_col=8);
        }
        foreach ($data[$k] as $key => $value){
            $writer->writeSheetRow($sheet1, $value, $data_style[$key]);
            $row++;
        }
    }
    $data = array();
}

$writer->writeSheetRow($sheet1, array("", "", "ขอกู้", $num_presun, "ราย", "เป็นเงิน", @number_format($sum_loan, 2, '.', ','), "บาท", "", "อนุมัติ", $num_presun_approve, "ราย", "เป็นเงิน", @number_format($sum_loan_approve, 2, '.', ','), 'บาท',""), $footerBorderbottom);
$finance_name = $signature['finance_name'];
$manager_name = $signature['manager_name'];

$row++;
$writer->writeSheetRow($sheet1, array(""));
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=0, $end_row=$row, $end_col=15);
$row++;
$writer->writeSheetRow($sheet1, array("", "", "ตรวจแล้วถูกต้อง", "", "", "", "", "", "", "", "", "", "", "", "", ''));
$row++;
$writer->writeSheetRow($sheet1, array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ''));
$row++;
$writer->writeSheetRow($sheet1, array("", "", "", '', "...........................................................................................................................",
                                    "", "", "", "", 'เลขานุกรม/กรรมการคณะกรรมการเงินกู้ สอ.รพ.', "", "", "", "", "", ''),$footerStyle);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=4, $end_row=$row, $end_col=8);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=9, $end_row=$row, $end_col=11);
$row++;
$writer->writeSheetRow($sheet1, array("", "", "", '', "( $finance_name )", "", "", "", "", "", "", "", "", "", "", ''),$footerStyle);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=4, $end_row=$row, $end_col=8);
$row++;
$writer->writeSheetRow($sheet1, array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ''));
$row++;
$writer->writeSheetRow($sheet1, array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ''));
$row++;
$writer->writeSheetRow($sheet1, array("", "", "", '',"...........................................................................................................................",
                                    "", "", "", "", 'กรรมการคณะกรรมการเงินกู้ สอ.รพ.', "", "", "", "", "",  ''),$footerStyle);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=4, $end_row=$row, $end_col=8);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=9, $end_row=$row, $end_col=11);
$row++;
$writer->writeSheetRow($sheet1, array("", "", "", '', "( $manager_name )", "", "", "", "", "", "", "", "", "", "", ''),$footerStyle);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=4, $end_row=$row, $end_col=8);
$row++;
$writer->writeSheetRow($sheet1, array("", "", "", '', "", "", "", "", "", "", "", "", "ผู้จัดทำ", ".............................................", "", 'เจ้าหน้าที่สินเชื่อ'),$footerStyle);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=13, $end_row=$row, $end_col=14);
$row++;
$writer->writeSheetRow($sheet1, array(""));
$row++;
$writer->writeSheetRow($sheet1, array("", "", "", "", "", "", "", "", "", "", "", "", "ผู้ตรวจสอบ", ".............................................", "", 'หัวหน้าฝ่ายสินเชื่อ'),$footerStyle);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=13, $end_row=$row, $end_col=14);
$row++;
$writer->writeSheetRow($sheet1, array(""));
$row++;
$writer->writeSheetRow($sheet1, array("", "", "", "", "", "", "", "", "", "", "", "", "ผู้ตรวจสอบ", ".............................................", "", 'รองผู้จัดการ สอ.รพ.'),$footerStyle);
$writer->markMergedCell($sheet1, $start_row=$row, $start_col=13, $end_row=$row, $end_col=14);
$row++;


$filename = "รายงานเอกสารพิจารณาเงินกู้.xlsx";
//exit;
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();