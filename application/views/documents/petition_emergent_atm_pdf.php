<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

 $filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/loan/emc-loan-book1.pdf" ;
//$filename = $_SERVER["DOCUMENT_ROOT"]."/fsccoop/assets/document/loan/emc-loan-book1.pdf" ;
$pdf = new FPDI();

$pageCount_1 = $pdf->setSourceFile($filename);
$myImage = "assets/images/check-mark.png";
$location  = $profile_location; // location ของสถานที่ ex เขียนที่.....

$age                = $this->center_function->diff_birthday($data['birthday']); //อายุ
$monthtext          = $this->center_function->month_arr(); // function แปลงเดือนเป็นตัวอักษร
$money_loan_amount_2text = $this->center_function->convert($data['loan_amount']); //จำนวนเงินกู็(ตัวอักษร)
$money_salary_2text = $this->center_function->convert($data['salary']);//เงินเดือน(ตัวอักษร)
$start_member_year  = $this->center_function->diff_year($data['approve_date'],date('Y-m-d H:i:s')); // ปีที่เริ่มทำงาน (จำนวนปี)
$start_member_month       = $this->center_function->diff_month_interval($data['approve_date'],date('Y-m-d H:i:s')); // จำนวนเดือน
$total_amount_approve =  $data['total_amount_approve'];
//echo $data['total_amount'] ;exit;
$per_month = $data['total_amount']/$data['min_month_share'];
$salary =  $data['salary'];
if(empty($data['salary'])){
    $salary = $data['member_salary'];
}
if ($data['approve_date'] != ''){
    $date_to_year       = (substr($data['approve_date'], 0, 4))+543; // ปีที่เริ่มทำสัญญา
    $date_to_text       = number_format(substr($data['approve_date'], 8, 2)); // วันที่เริ่มทำสัญญา
    $date_to_month      = number_format(substr($data['approve_date'], 5, 2)); // เดือนที่เริ้มทำสัญญา
}
$month2text         = $monthtext[$date_to_month]; // เดือนที่เริ่มทำสัญญา (ตัวอักษร)
$full_date          = $date_to_text."  ".$month2text."  ".$date_to_year; // วัน:เดือน:ปี ที่เริ่มทำสัญญา
if ($data['createdatetime'] != ''){
    $create_year       = (substr($data['createdatetime'], 0, 4))+543; // // ปีที่บันทึกข้อมูล
}
$create_day = number_format(substr($data['createdatetime'], 8, 2)); // วันที่บันทึกข้อมูล
$create_month = number_format(substr($data['createdatetime'], 5, 2)); // เดือนที่บันทึกข้อมูล
$create_month2text = $monthtext[$create_month]; // เดือนที่บันทึกข้อมูล(ตัวอักษร)
if ($data['createdatetime'] != ''){
    $create_year       = (substr($data['createdatetime'], 0, 4))+543;  // ปีที่บันทึกข้อมูล
}
if(!empty($data['date_start_period'])) {
    $day_start_period = number_format(substr($data['date_start_period'], 8, 2));// วันเริ่มจ่ายงวด(หุ้น)
    $month_start_period = number_format(substr($data['date_start_period'], 5, 2)); // เดือนที่จ่ายค่างวด(หุ้น)
    $year_start_period = (substr($data['date_start_period'], 0, 4)) + 543; // ปีที่จ่ายค่างวด(หุ้น)
}
$full_start_period  = $day_start_period."  ".$month_start_period."  ".$year_start_period; // วัน, เดือน, ปี ที่จ่ายค่างวด(หุ้น)
$fullname_th        = $data['prename_full'].$data['firstname_th']."  ".$data['lastname_th']; // คำนำหน้าชื่อ , ชื่อ-สกุล (ผู้กู้)
$contract_number_font = substr($data['contract_number'], 0, -8); // ตัวอักษรหน้า เลขที่สัญญา ex. ฉฉ999999 = ฉฉ
$contract_number_back = substr($data['contract_number'], -9);   //ตัวอักษรหลัง เลขที่สัญญา ex. 999999 = ฉฉ
$period_amount = substr($this->center_function->convert($data['period_amount']),0,-3*7); //งวด(ตัวอักษร)/
$max_loan_amount = 100000;
for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {
    $pdf->AddPage();
    $tplIdx = $pdf->importPage($pageNo);
    $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
    $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
    $pdf->SetFont('THSarabunNew', '', 12 );
    $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');

//    $pdf->SetTitle(U2T('คำขอกู้เงินเพื่อการศึกษา'));
    $border = isset($_GET['show']) && $_GET['show'] == '1' ?  1 : 0;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetAutoPageBreak(true,0);
    if($pageNo == '1'){
        // $pdf->Image($myImage, 35.8, 162.3, 3);
        // $y_point = 55.7;
        $y_point = 23;
        $pdf->SetXY( 180.5, $y_point );
        $pdf->MultiCell(10, 5, U2T($contract_number_font), $border, "C"); //หนังสือกู้ที่
        $pdf->SetXY( 190.5, $y_point );
        $pdf->SetFont('THSarabunNew', '', 10 );
        $pdf->MultiCell(13, 5, U2T($contract_number_back), $border, "C"); //หนังสือกู้ที่
        $pdf->SetFont('THSarabunNew', '', 12 );
        $y_point = 29.8;
        $pdf->SetXY( 172.5, $y_point );
        $pdf->MultiCell(9, 5, U2T($date_to_text), $border, "C"); //วันที่(วัน)
        $pdf->SetXY( 182.5, $y_point );
        $pdf->MultiCell(9, 5, U2T($month_short_arr[$date_to_month]), $border, "C"); //วันที่(เดือน)
        $pdf->SetXY( 192.5, $y_point );
        $pdf->MultiCell(9, 5, U2T($date_to_year), $border, "C"); //วันที่(ปี)
        $y_point = 26.5;
        $pdf->SetXY( 118.5, $y_point );
        $pdf->SetFont('THSarabunNew', '', 10 );
        $pdf->MultiCell(45, 5, U2T($location['profile_location']['coop_name_th']), $border, "C"); //เขียนที่
        $pdf->SetFont('THSarabunNew', '', 12 );
        $y_point = 32.9;
        $pdf->SetXY( 115.5, $y_point );
        $pdf->MultiCell(9, 5, U2T($date_to_text), $border, "C"); //วัน
        $pdf->SetXY( 130.5, $y_point );
        $pdf->MultiCell(13, 5, U2T($month_short_arr[$date_to_month]), $border, "C"); //เดือน
        $pdf->SetXY( 148.5, $y_point );
        $pdf->MultiCell(13, 5, U2T($date_to_year), $border, "C"); //เดือน
        $y_point = 54.5;
        $pdf->SetXY( 44.5, $y_point );
        $pdf->MultiCell(80, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
        $pdf->SetXY( 148, $y_point );
        $pdf->MultiCell(50, 5, U2T($data['member_id']), $border, "C"); //สมาชิกเลขทะเบียนที่
        $y_point = 60.3;
        $pdf->SetXY( 57, $y_point );
        $pdf->MultiCell(41, 5, U2T($data['position_name']), $border, "C"); // ตำแหน่ง
        $pdf->SetXY( 105.5, $y_point );
        $pdf->MultiCell(25, 5, U2T($data['mem_group_name']), $border, "C"); // สังกัด
        $pdf->SetXY( 143, $y_point );
        $pdf->MultiCell(25, 5, U2T(($data['office_tel']=='')?'-':$data['office_tel']), $border, "C"); // โทรภายใน
        $pdf->SetXY( 175, $y_point );
        $pdf->MultiCell(22, 5, U2T($data['mobile']), $border, "C"); //
        $y_point = 66;
        $pdf->SetXY( 33.5, $y_point );
        $pdf->MultiCell(26.5, 5, U2T(number_format($salary)), $border, "C"); //เงินเดือน
        $pdf->SetXY( 97.5, $y_point );
        $pdf->MultiCell(22, 5, U2T(number_format($salary)), $border, "C"); //
        $pdf->SetXY( 132.5, $y_point );
        $pdf->MultiCell(20, 5, U2T($month2text), $border, "C"); ///
        $pdf->SetXY( 158, $y_point );
        $pdf->MultiCell(13, 5, U2T($date_to_year), $border, "C"); ///
        $y_point = 78;
        $pdf->SetXY( 128, $y_point );
        $pdf->MultiCell(63, 5, U2T(number_format($data['total_amount'])), $border, "C"); //
        $y_point = 83.8;
        $pdf->SetXY( 11.8, $y_point );
        $pdf->MultiCell(64.5, 5, U2T($this->center_function->convert($data['total_amount'])), $border, "C"); //
        $y_point = 119;
        $pdf->SetXY( 100, $y_point );
        $pdf->MultiCell(105, 5, U2T($data['interest_rate']), $border, "C"); //
        $y_point = 224;
        $pdf->SetXY( 87, $y_point );
        $pdf->SetFont('THSarabunNew', '', 11 );
        $pdf->MultiCell(46, 5, U2T($fullname_th), $border, "C"); //
        $pdf->SetFont('THSarabunNew', '', 12 );
    }else if($pageNo == '2'){
//        $y_point = 36.2;
//        $pdf->Image($myImage, 17.2, $y_point, 3);
//        $pdf->Image($myImage, 100, $y_point, 3);
//        $y_point = 43.3;
//        $pdf->Image($myImage, 17.2, $y_point, 3);
//        $pdf->Image($myImage, 100, $y_point, 3);
        $y_point = 50.5;
        $pdf->SetXY( 71.5, $y_point );
        $pdf->MultiCell(27.5, 5, U2T(number_format($data['total_amount'])), $border, "C"); //
        $y_point = 85;
        $pdf->SetXY( 10, $y_point );
        $pdf->MultiCell(35, 5, U2T(number_format($salary, 2)), $border, "C"); //
        $total_emergency = 0;
        $total_normal = 0;
        $limit = $data['credit_limit'];
        foreach ($contract['data'] as $item) {
            if($item['loan_type_id'] == '1'){
                $total_emergency += $item['total_paid_per_month'];
            }else if($item['loan_type_id'] == '2'){
                $total_normal += $item['total_paid_per_month'];
            }
        }
        $pdf->SetXY( 48, $y_point );
        $pdf->MultiCell(35, 5, U2T(number_format($data['ordinary_loan'], 2)), $border, "C"); //
        $pdf->SetXY( 87, $y_point );
        $pdf->MultiCell(37.5, 5,U2T(number_format(($data['loan_amount_balance']), 2)), $border, "C"); //
        $pdf->SetXY( 128, $y_point );
        $pdf->MultiCell(32, 5, U2T(number_format($limit)), $border, "C"); //
        $pdf->SetXY( 164, $y_point );
        $pdf->MultiCell(34.5, 5,U2T(number_format(($limit-$data['total_amount']), 2)), $border, "C"); //
        $y_point = 102;//
        $pdf->SetXY( 65, $y_point );
        $pdf->MultiCell(30, 5,U2T(''), $border, "C"); //,
        $pdf->SetXY( 129.5, $y_point );
        $pdf->MultiCell(13, 5, U2T(''), $border, "C"); //
        $pdf->SetXY( 170, $y_point );
        $pdf->MultiCell(20, 5, U2T(''), $border, "C"); //
        $y_point = 109.1;
        $pdf->SetXY( 65, $y_point );
        $pdf->MultiCell(30, 5, U2T(''), $border, "C"); //
        $pdf->SetXY( 171.5, $y_point );
        $pdf->MultiCell(20, 5, U2T(''), $border, "C"); //
        if(!empty($data['date_start_period'])) {
            $y_point = 115.4;
            $pdf->SetXY(80.5, $y_point);
            $pdf->MultiCell(28, 5, U2T($month_arr[(int)substr($data['date_start_period'], 5, 2)]), $border, "C"); //
            $pdf->SetXY(114.5, $y_point);
            $pdf->MultiCell(16, 5, U2T(substr($data['date_start_period'], 0, 4) + 543), $border, "C"); //
        }
        $pdf->SetXY( 162.5, $y_point );
        $pdf->MultiCell(20.5, 5, U2T(number_format($data['total_paid_per_month'])), $border, "C"); //
        $y_point = 134.5;
        if(false){
            $pdf->Image($myImage, 48.5, $y_point, 3);
        }else{
            $pdf->Image($myImage, 91.3, $y_point, 3);
        }
        $y_point = 141;
        $pdf->SetXY( 52, $y_point );
        //$pdf->MultiCell(83.5, 5, U2T('ข้อแจ้งอื่นๆ'), $border, "C"); //
    }else if($pageNo == '3'){
        $y_point = 21.5;
        $pdf->SetXY( 150.5, $y_point );
        $pdf->SetFont('THSarabunNew', '', 11 );
        $pdf->MultiCell(46, 5, U2T($location['profile_location']['coop_name_th']), $border, "C"); //เขียนที่
        $pdf->SetFont('THSarabunNew', '', 12 );
        $y_point = 30.5;
        $pdf->SetXY( 148.5, $y_point );
        $pdf->MultiCell(40, 5, U2T($full_date), $border, "C"); //วันที่ทำรายการ
        $y_point = 48.5;
        $pdf->SetXY( 54.5, $y_point );
        $pdf->MultiCell(80, 5, U2T($fullname_th), $border, "C");
        $y_point = 57.5;
        $pdf->SetXY( 35.5, $y_point );
        $pdf->MultiCell(30, 5, U2T($data['member_id']), $border, "C");
        $pdf->SetXY( 84.5, $y_point );
        $pdf->MultiCell(52.5, 5, U2T($data['mobile']), $border, "C");
        $y_point = 73.5;
        $max_loan_amount = $data['credit_limit'];

        if($data['member_salary']*5 < $max_loan_amount) {
            $max_loan_amount = $data['member_salary']*5;
        }
        $pdf->SetXY( 65.5, $y_point );
        $pdf->MultiCell(50, 5, U2T(number_format($max_loan_amount)), $border, "C");
        $pdf->SetXY( 140.5, $y_point );
        $pdf->MultiCell(15, 5, U2T(number_format($max_loan_amount / $data['min_month_share'],2)), $border, "C");
        $pdf->SetXY( 183.5, $y_point );
        $pdf->MultiCell(12, 5, U2T($data['min_month_share']), $border, "C");
        $y_point = 81.5;///เงินกู้ที่ต้องการ
        $pdf->SetXY( 65.5, $y_point );
        $pdf->MultiCell(50, 5, U2T(number_format($data['total_amount'])), $border, "C");
        $pdf->SetXY( 140.5, $y_point );
        $pdf->MultiCell(15, 5, U2T(number_format($per_month,2)), $border, "C");
        $pdf->SetXY( 183.5, $y_point );
        $pdf->MultiCell(12, 5, U2T($data['min_month_share']), $border, "C");
        $y_point = 89;//สูงสุดที่กู้ได้
        $pdf->SetXY( 65.5, $y_point );
        $pdf->MultiCell(50, 5, number_format($max_loan_amount), $border, "C");
        $pdf->SetXY( 140.5, $y_point );
        $pdf->MultiCell(15, 5, U2T(number_format($max_loan_amount / $data['min_month_share'],2)), $border, "C");
        $pdf->SetXY( 183.5, $y_point );
        $pdf->MultiCell(12, 5, U2T($data['min_month_share']), $border, "C");
        $y_point = 105;
        $pdf->SetXY( 47.5, $y_point );
        $pdf->MultiCell(32, 5, U2T(number_format($share_group['share_collect_value'])), $border, "C");
        $pdf->SetXY( 98.5, $y_point );
        $pdf->MultiCell(28, 5, U2T(number_format($deposit['cal_account'])), $border, "C");
        $pdf->SetXY( 142.5, $y_point );
        $pdf->MultiCell(34, 5, U2T(number_format($share_group['share_collect_value'] + $deposit['cal_account'])), $border, "C");
        $y_point = 112.5;
        $pdf->SetXY( 66.5, $y_point );
        $pdf->MultiCell(36, 5,U2T(number_format(($data['loan_amount_balance']), 2)), $border, "C");
        $pdf->SetXY( 146, $y_point );
        $pdf->MultiCell(31, 5, U2T(number_format(($data['loan_amount_balance']*0.15),2)), $border, "C");
        $y_point = 120.5;
        $pdf->SetXY( 133.5, $y_point );
        $sum_deposit_more = ($data['loan_amount_balance']*0.15)-($share_group['share_collect_value'] + $deposit['cal_account']);
        if($sum_deposit_more < 0){
            $sum_deposit_more = 0;
        }
        $pdf->MultiCell(41.5, 5, U2T(number_format($sum_deposit_more)), $border, "C");
        if(true) {
            $y_point = 152;
            $pdf->SetXY(22, $y_point);
            $pdf->MultiCell(35, 5, U2T($full_date), $border, "C");
            $pdf->SetXY(130, $y_point);
            $pdf->MultiCell(53, 5, $data['loan_amount_balance'], $border, "C");
            //$pdf->MultiCell(53, 5, U2T(number_format($contract['cal_loan']['1']['loan_amount_balance'])), $border, "C");
            $y_point = 183;
            $pdf->SetXY(135, $y_point);
            $pdf->MultiCell(53, 5, U2T(number_format(($data['total_amount']),2)), $border, "C");
            $y_point = 191;
            $pdf->SetXY(135, $y_point);
            $pdf->MultiCell(53, 5, U2T(number_format(($data['loan_amount_balance']), 2)), $border, "C");
            //$pdf->MultiCell(48.5, 5, U2T(number_format($contract['cal_loan']['1']['loan_amount_balance'])), $border, "C");
            $cal_sum =$data['total_amount']-$data['loan_amount_balance'] ;
            $y_point = 205.5;
            $pdf->SetXY(135, $y_point);
            $pdf->MultiCell(53, 5,U2T(number_format(($cal_sum), 2)), $border, "C");
            //$pdf->MultiCell(48, 5, U2T(number_format($data['total_amount'] - $contract['cal_loan']['1']['loan_amount_balance'])), $border, "C");
            $y_point = 227.3;
            $pdf->SetXY(27, $y_point);
            $pdf->MultiCell(60, 5, U2T($fullname_th), $border, "C");
            $y_point = 235.3;
            $pdf->SetXY(27, $y_point);
            $pdf->MultiCell(58, 5, U2T($full_date), $border, "C");
            $y_point = 235.3;
            $pdf->SetXY(127, $y_point);
            $pdf->MultiCell(55, 5, U2T($full_date), $border, "C");
        }
    }
}
//exit;
$pdf->Output();