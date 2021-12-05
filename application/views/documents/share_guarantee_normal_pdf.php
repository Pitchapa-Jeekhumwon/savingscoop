<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/loan/loan-coop.pdf" ;
// $filename = $_SERVER["DOCUMENT_ROOT"]."/fsccoop/assets/document/loan/loan-coop.pdf" ;
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
    $create_year       = (substr($data['createdatetime'], 0, 4))+543; // // ปีที่บันทึกข้อมูล
}
$day_start_period   = number_format(substr($data['date_start_period'], 8, 2));// วันเริ่มจ่ายงวด(หุ้น)
$month_start_period = number_format(substr($data['date_start_period'], 5, 2)); // เดือนที่จ่ายค่างวด(หุ้น)
$year_start_period  = (substr($data['date_start_period'], 0, 4))+543; // ปีที่จ่ายค่างวด(หุ้น)
$full_start_period  = $day_start_period."  ".$month_start_period."  ".$year_start_period; // วัน, เดือน, ปี ที่จ่ายค่างวด(หุ้น)
$fullname_th        = $data['prename_full'].$data['firstname_th']."  ".$data['lastname_th']; // คำนำหน้าชื่อ , ชื่อ-สกุล (ผู้กู้)
$contract_number_font = substr($data['contract_number'], 0, -8); // ตัวอักษรหน้า เลขที่สัญญา ex. ฉฉ999999 = ฉฉ
$contract_number_back = substr($data['contract_number'], -9);   //ตัวอักษรหลัง เลขที่สัญญา ex. 999999 = ฉฉ
$period_amount = substr($this->center_function->convert($data['period_amount']),0,-3*7); //งวด(ตัวอักษร)

for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {
    $pdf->AddPage();
    $tplIdx = $pdf->importPage($pageNo);
    $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

    //       $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
    // $pdf->SetFont('THSarabunNew', '', 6 );
    // $border = 0;
    // for ($i = 10; $i<=270;$i+= 5){
    //     for ($j = 10; $j<200;$j+= 10){
    //         $pdf->SetXY( $j, $i );
    //         $pdf->MultiCell(7, 5, U2T($j.','.$i), $border, "L");//หนังสือกู้ที่
    //     }

    // }
    $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
    $pdf->SetFont('THSarabunNew', '', 14 );
    $border = isset($_GET['show']) && $_GET['show'] == '1' ?  1 : 0;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetAutoPageBreak(true,0);
    if($pageNo == '1'){
        $y_point = 31.2;
        $pdf->SetXY( 30, $y_point );
//        $pdf->MultiCell(15, 5, U2T('*'), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY( 46, $y_point );
//        $pdf->MultiCell(12, 5, U2T('*'), $border, "C");//หนังสือกู้ที่
        $y_point = 38.5;
        $pdf->SetXY( 30, $y_point );
        $pdf->MultiCell(10, 5, U2T($date_to_text), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY( 40, $y_point );
        $pdf->MultiCell(10, 5, U2T($month_short_arr[$date_to_month]), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY( 51, $y_point );
        $pdf->MultiCell(10, 5, U2T($date_to_year), $border, "C");//หนังสือกู้ที่
        $y = 7.63;
        $y_point = 30;
        $pdf->SetFont('THSarabunNew', '', 10 );
        $pdf->SetXY( 165, $y_point );
        $pdf->MultiCell(13, 5, U2T($contract_number_font), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY( 180, $y_point );
        $pdf->MultiCell(13, 5, U2T($contract_number_back), $border, "C");//หนังสือกู้ที่
        $pdf->SetFont('THSarabunNew', '', 14 );
        $y_point = 37.63;
        $pdf->SetXY( 155, $y_point );
        $pdf->MultiCell(12, 5, U2T($date_to_text), $border, "C");///วันนที่
        $pdf->SetXY( 168.5, $y_point );
        $pdf->MultiCell(10, 5, U2T($month_short_arr[$date_to_month]), $border, "C");//วันที่
        $pdf->SetXY( 180.5, $y_point );
        $pdf->MultiCell(10, 5, U2T($date_to_year), $border, "C");//วันที่
        $y_point = 51.5;
        $pdf->SetXY( 133, $y_point );
        $pdf->MultiCell(62.5, 5, U2T($location['profile_location']['coop_name_th']), $border, "C");//เขียนที่
        $y_point = 59.5;
        $pdf->SetXY( 128, $y_point );
        $pdf->MultiCell(65.5, 5, U2T($full_date), $border, "C"); //วันที่

        $y_point = 78.6;
        $pdf->SetXY( 44.5, $y_point );
        $pdf->MultiCell(85, 5, U2T($fullname_th), $border, "C"); //ชื่อ
        $pdf->SetXY( 163, $y_point );
        $pdf->MultiCell(30, 5, U2T($data['member_id']), $border, "C"); //สมาชิกเลขทะเบียนที่
        $y_point += $y;
        $pdf->SetXY( 60, $y_point );
        $pdf->MultiCell(40, 5, U2T(number_format($data['salary'])), $border, "C"); //เงินเดือน
        $y_point += $y;
        $pdf->SetXY( 78, $y_point );
        // print_r ($data['loan_amount']); exit;
        $pdf->MultiCell(30, 5, U2T(number_format($data['loan_amount'])), $border, "C"); //จำนวนเงินที่ขอกู้
        $pdf->SetXY( 117, $y_point );
        $number_text = $this->center_function->convert($data['loan_amount']);
        $pdf->MultiCell(75, 5, U2T($number_text), $border, "C"); // ข้อความจำนวนเงินมี่ขอกู้
        $y_point += $y;
        $pdf->SetXY( 125, $y_point );
        $pdf->MultiCell(65, 5, U2T(($data['loan_reason']=='') ? '-' : $data['loan_reason']), $border, "C"); //เหตุผล
        $y_point += $y;
        $pdf->SetXY( 118, $y_point );
        $pdf->MultiCell(65, 5, U2T($data['position_name']), $border, "C"); //ทางานในตำแหน่ง
        $y_point += $y;
        $pdf->SetXY( 105, $y_point );
        $pdf->MultiCell(35, 5, U2T(($data['id_card']=='') ? '-' : $data['id_card']), $border, "C"); //ประจำตํวประชาชน
        $pdf->SetXY( 152, $y_point );
        $pdf->MultiCell(42, 5, U2T(($data['mem_group_name_level']=='') ? '-' : $data['mem_group_name_level']), $border, "C"); //สังกัด
        $y_point += $y;
        $pdf->SetXY( 55, $y_point );
        $pdf->MultiCell(25, 5, U2T(($data['c_address_no']=='') ? '-' : $data['c_address_no']), $border, "C"); //บ้านเลขที่
        $pdf->SetXY( 90, $y_point );
        $pdf->MultiCell(35, 5, U2T(($data['c_address_road']=='') ? '-' : $data['c_address_road']), $border, "C"); //ถนน
        $pdf->SetXY( 140, $y_point );
        $pdf->MultiCell(50, 5, U2T(($data['c_district_name']=='') ? '-' : $data['c_district_name']), $border, "C"); //ตำบล

        $y_point += $y;
        $pdf->SetXY( 32, $y_point );
        $pdf->MultiCell(40, 5, U2T(($data['c_amphur_name']=='') ? '-' : $data['c_amphur_name']), $border, "C"); //อำเภอ
        $pdf->SetXY( 90, $y_point );
        $pdf->MultiCell(40, 5, U2T(($data['c_province_name']=='') ? '-' : $data['c_province_name']), $border, "C"); //จังหวัด
        $pdf->SetXY( 150, $y_point );
        $pdf->MultiCell(40, 5, U2T(($data['mobile']=='') ? '-' : $data['mobile']), $border, "C"); //โทรศัพท์

        // if ($data['pay_type']==1){

        // }
        $y_point = 198.5;
        if ($data['pay_type']==1){
            $pdf->Image($myImage, 42.5, $y_point+1.3, 3);
            $pdf->SetXY( 86, $y_point );
            $pdf->MultiCell(36, 5, U2T(number_format($data['total_paid_per_month'],2)), $border, "C"); //ต้นเงินเท่ากันทุกงวดๆ ละ.
            $pdf->SetXY( 164, $y_point );
            $pdf->MultiCell(18, 5, U2T($data['period_amount']), $border, "C"); //จำนวนงวด
        }else if($data['pay_type']==2) {
            $y_point += $y;
            $pdf->Image($myImage, 42.5, $y_point+1.3, 3);
            $pdf->SetXY( 105, $y_point );
            $pdf->MultiCell(36, 5, U2T(number_format($data['total_paid_per_month'], 2)), $border, "C"); //ต้นเงินและดอกเบ้ียเท่ากันทุกงวดๆละ
            $pdf->SetXY( 164, $y_point );
            $pdf->MultiCell(18, 5, U2T($data['period_amount']), $border, "C"); //จำนวนงวด
        }

        $y_point = 233;
        $pdf->SetXY( 131, $y_point );
        $pdf->MultiCell(55, 5, U2T($name), $border, "C"); //ลงชื่อ
        $y_point += $y;
        $pdf->SetXY( 125, $y_point );
        $pdf->MultiCell(67, 5, U2T($fullname_th), $border, "C"); //ลายเซ็น
        $y_point = 276;
        $pdf->SetXY( 131, $y_point );
        $pdf->MultiCell(54.5, 5, U2T($name), $border, "C"); //ลงชื่อ
        $y_point += $y;
        $pdf->SetXY( 125, $y_point );
        $pdf->MultiCell(67, 5, U2T($fullname_th), $border, "C"); //ลายเซ็น

    }else if($pageNo == '2'){
//         $y_point = 125.3;
//         $pdf->SetXY( 90, $y_point );
//         $pdf->MultiCell(60 , 5, U2T(number_format($data['loan_amount_balance'])), $border, "C");//จำนวนเงินกู้
//         $y_point = 164.5;
//         $pdf->Image($myImage, 155.5, $y_point, 3);
//         $pdf->Image($myImage, 168.7, $y_point, 3);
//         $y_point = 170.5;
//         $pdf->SetXY( 70, $y_point );
//         $pdf->MultiCell(120 , 5, U2T('เจ้าหน้าที่กรอกเอง'), $border, "C");//ข้อชี้แจงอื่นๆ
//         $y_point = 186.9;
//         $pdf->SetXY( 107, $y_point );
//         $pdf->MultiCell(42 , 5, U2T('เจ้าหน้าที่กรอกเอง'), $border, "C");//ต้นเงินค้ำ
//         $y_point = 257;
//         $pdf->Image($myImage, 155.5, $y_point, 3);
//         $pdf->Image($myImage, 168.7, $y_point, 3);
//         $y_point += $y-1.3;
//         $pdf->SetXY( 68, $y_point );
//         $pdf->MultiCell(125 , 5, U2T('เจ้าหน้าที่กรอกเอง'), $border, "C"); //ขอช้้ีแจงอื่น ๆ
//         $y_point += $y;
//         $pdf->SetXY( 135.3, $y_point );
//         $pdf->MultiCell(45 , 5, U2T('เจ้าหน้าที่กรอกเอง'), $border, "C"); //เจ้าหน้าที่
//         $y_point += $y;
//         $pdf->SetXY( 135.3, $y_point );
//         $pdf->MultiCell(45 , 5, U2T('เจ้าหน้าที่กรอกเอง'), $border, "C"); //ลายเซ็น
//         $y_point += $y;
//         $pdf->SetXY( 143.3, $y_point );
//         $pdf->MultiCell(30 , 5, U2T('เจ้าหน้าที่กรอกเอง'), $border, "C"); //วันที่เซ็น
    }else if($pageNo == '3'){
        $y_point = 39.3;
        $pdf->SetXY( 160, $y_point );
        $pdf->MultiCell(35 , 5, U2T($data['contract_number']), $border, "C");//ที่
        $y_point += $y;
        $pdf->SetXY( 160, $y_point );
        $pdf->MultiCell(35 , 5, U2T($create_day." ".$create_month2text." ".$create_year), $border, "C");//วันที่
        $y_point += $y;
        $pdf->SetXY( 45, $y_point );
        $pdf->MultiCell(135 , 5, U2T($fullname_th), $border, "C");//ชื่อผู้กู๋
        $y_point += $y;
        $pdf->SetXY( 45, $y_point );
        $pdf->MultiCell(135 , 5, U2T($fullname_th), $border, "C");//ข้าพเจ้า
        $y_point += $y;
        $pdf->SetXY( 157, $y_point );
        $pdf->MultiCell(35 , 5, U2T($data['member_id']), $border, "C");//รหัสสมาชิก
        $y_point += $y;
        $pdf->SetXY( 85, $y_point );
        $pdf->MultiCell(45 , 5, U2T($data['position_name']), $border, "C");//ตำแหน่ง
        $y_point += $y;
        $pdf->SetXY( 47, $y_point );
        $pdf->MultiCell(35 , 5, U2T($data['id_card']), $border, "C");//รัฐวิสากิลเลขที่
        $pdf->SetXY( 92, $y_point );
        $pdf->MultiCell(40 , 5, U2T($data['short_mem_group_name_level']), $border, "C");//สังกัด
        $pdf->SetXY( 172, $y_point );
        $pdf->MultiCell(22 , 5, U2T($data['c_address_no']." ".$data['c_address_no']), $border, "C");//บ้านเลขที่
        $y_point += $y;
        $pdf->SetXY( 30, $y_point );
        $pdf->MultiCell(30 , 5, U2T($data['c_address_road']), $border, "C");//ถนน
        $pdf->SetXY( 70, $y_point );
        $pdf->MultiCell(33 , 5, U2T($data['c_amphur_name']), $border, "C");//ตำบล
        $pdf->SetXY( 115, $y_point );
        $pdf->MultiCell(33 , 5, U2T($data['c_district_name']), $border, "C");//อำเภอ
        $pdf->SetXY( 162, $y_point );
        $pdf->MultiCell(33 , 5, U2T($data['c_province_name']), $border, "C");//จังหวัด
        $y_point += $y;
        $pdf->SetXY( 35, $y_point );
        $pdf->MultiCell(57 , 5, U2T($data['mobile']), $border, "C");//โทรศัพท์
        $y_point += $y+$y;
        $pdf->SetXY( 102, $y_point );
        $pdf->MultiCell(23 , 5, U2T(number_format($data['loan_amount'])), $border, "C");//จำนวนเงิน
        $pdf->SetXY( 135, $y_point );
        $number_text = $this->center_function->convert($data['loan_amount']);
        $pdf->MultiCell(55 , 5, U2T($number_text), $border, "C");//จำนวนเงินตัวอักษร
        $y_point = 138.4;
        if ($data['pay_type']==1) {
            $pdf->Image($myImage, 42.3, $y_point + 1.3, 3);
            $pdf->SetXY(105, $y_point);
            $pdf->MultiCell(60, 5, U2T(number_format($data['total_paid_per_month'], 2)), $border, "C");//จำนวนเงินตัวอักษร
        }else if($data['pay_type'] == 2){
            $pdf->Image($myImage, 42.3, $y_point+1.5+$y, 3);
            $pdf->SetXY( 123, $y_point+0.2+$y );
            $pdf->MultiCell(60 , 5, U2T(number_format($data['total_paid_per_month'], 2)), $border, "C");//จำนวนเงินตัวอักษร
        }
        $y = $y + 0.2;
        $y_point += $y;
        $y_point += $y;
        $pdf->SetXY( 48, $y_point );
        $pdf->MultiCell(29 , 5, U2T(str_replace("บาทถ้วน","",$this->center_function->convert($data['period_amount']))), $border, "C");//จำนวนงวดตัวอักษร
        $pdf->SetXY( 153, $y_point );
        $pdf->MultiCell(33 , 5, U2T($this->center_function->convert($data['interest_per_year'], 'unit')), $border, "C");//จำนวนอัตราดอกเบี้ยตัวอักษร
        $y_point += $y;
        $pdf->SetXY( 60 , $y_point );

        $pdf->MultiCell(29 , 5, U2T($month_arr[(int)substr($data['date_period_1'], 5,2)]), $border, "C");//จำนวนงวดประจำเดือนตัวอักษร
    }else if($pageNo == '4'){
        if(!empty($data['coop_loan_guarantee'])) {
            $y_point = 36.3;
            $pdf->SetXY( 118.3, $y_point );
            $pdf->MultiCell(30 , 5, U2T(''), $border, "C");//เลขที่สหกรณ์
            $pdf->SetXY( 161, $y_point );
            $pdf->MultiCell(25 , 5, U2T(number_format($data['coop_loan_guarantee'][0]['amount']/10)), $border, "C");//จำนวนหุ้น
            $y -= 0.2;
            $y_point+=$y;
            $pdf->SetXY( 33, $y_point );
            $pdf->MultiCell(35, 5, U2T(number_format($data['coop_loan_guarantee'][0]['amount'])), $border, "C");//จำนวนเงิน
        }
        $y_point = 93.9;
        $pdf->SetXY( 124, $y_point );
        $pdf->MultiCell(50 , 5, U2T($name), $border, "C");//ชื่อผู้กู้
        $y_point += $y;
        $pdf->SetXY( 122, $y_point );
        $pdf->SetFont('THSarabunNew', '', 12 );
        $pdf->MultiCell(53 , 5, U2T($fullname_th), $border, "C");//ลายเซ็นผู้กู้
        $pdf->SetFont('THSarabunNew', '', 14 );
        // $y_point += $y;
        // $pdf->SetXY( 124, $y_point );
        // $pdf->MultiCell(50 , 5, U2T('t13'), $border, "C");//ชื่อพยาน
        // $y_point += $y;
        // $pdf->SetXY( 124, $y_point );
        // $pdf->MultiCell(50 , 5, U2T('t14'), $border, "C");//ลายเซ็นพยาน
        // $y_point += $y;
        // $pdf->SetXY( 124, $y_point );
        // $pdf->MultiCell(50 , 5, U2T('t15'), $border, "C");//ชื่อพยาน
        // $y_point += $y;
        // $pdf->SetXY( 124, $y_point );
        // $pdf->MultiCell(50 , 5, U2T('t16'), $border, "C");//ลายเซ็นพยาน


        $y_point = 229;
        $pdf->SetXY( 33, $y_point );
        $pdf->MultiCell(85 , 5, U2T($fullname_th), $border, "C");//ชื่อ
        $pdf->SetXY( 150, $y_point );
        $pdf->MultiCell(35 , 5, U2T(number_format($data['loan_amount'])), $border, "C");//จำนวนหุ้น
        $y_point += $y;
        $pdf->SetXY( 22, $y_point );
        $number_text = $this->center_function->convert($data['loan_amount']);
        $pdf->MultiCell(77 , 5, U2T($number_text), $border, "C");//จำนวนหุ้นตัวอักษร

        $y_point = 248.3;
        $pdf->SetXY( 92, $y_point );
        $pdf->MultiCell(50 , 5, U2T($name), $border, "C");//ชื่อผู้รับเงิน
        $y_point += $y;
        $pdf->SetXY( 91, $y_point );
        $pdf->SetFont('THSarabunNew', '', 12 );
        $pdf->MultiCell(52 , 5, U2T($fullname_th), $border, "C");//ลายเซ็นผู้รับเงิน
        $pdf->SetFont('THSarabunNew', '', 14 );
        // $y_point += 12;
        // $pdf->SetXY( 92, $y_point );
        // $pdf->MultiCell(50 , 5, U2T('t22'), $border, "C");//เจ้าหน้าที่ผู้จ่ายเงิน
        // $y_point += 12;
        // $pdf->SetXY( 92, $y_point );
        // $pdf->MultiCell(50 , 5, U2T('t22'), $border, "C");//เจ้าหน้าที่ผู้ตรวจสัญญากู้
    }else if($pageNo == '5'){
        $y = $y-0.2;
        $y_point = 46.3;
        $pdf->SetXY( 135, $y_point );
        $pdf->MultiCell(60 , 5, U2T('สหกรณ์ออมทรัพย์โรงบาลตำรวจ'), $border, "C");//เขียนที่/
        $y_point += $y;
        $pdf->SetXY( 120, $y_point );
        $pdf->MultiCell(12 , 5, U2T($date_to_text), $border, "C");//วันที่
        $pdf->SetXY( 143, $y_point );
        $pdf->MultiCell(25 , 5, U2T($month_short_arr[$date_to_month]), $border, "C");//เดือน
        $pdf->SetXY( 177, $y_point );
        $pdf->MultiCell(15 , 5, U2T($date_to_year), $border, "C");//ปี
        $y_point += $y;
        $pdf->SetXY( 45, $y_point );
        $pdf->MultiCell(55 , 5, U2T($fullname_th), $border, "C");//ชื่อ
        $pdf->SetXY( 138, $y_point );
        $pdf->MultiCell(35 , 5, U2T($data['id_card']), $border, "C");///เลขบัตรประชาชน
        $pdf->SetXY( 183, $y_point );
        function getAge($birthday) {
            $then = strtotime($birthday);
            return(floor((time()-$then)/31556926));
        }
        $dateB="1990-02-14"; // ตัวแปรเก็บวันเกิด
        $pdf->MultiCell(8 , 5, U2T(getAge($data['birthday'])), $border, "C");//อายุ

        $y_point += $y;
        $pdf->SetXY( 52, $y_point );
        $pdf->MultiCell(13   , 5, U2T(($data['c_address']=='') ? '-' : $data['c_address']), $border, "C");//บ้านเลขที่
        $pdf->SetXY( 73, $y_point );
        $pdf->MultiCell(12 , 5, U2T(($data['c_address_moo']=='') ? '-' : $data['c_address_moo']), $border, "C");//หมู่
        $pdf->SetXY( 103, $y_point );
        $pdf->MultiCell(37 , 5, U2T(($data['c_address_soi']=='') ? '-' : $data['c_address_soi']), $border, "C");//ตรอก/ซอย
        $pdf->SetXY( 150, $y_point );
        $pdf->MultiCell(45 , 5, U2T(($data['c_address_road']=='') ? '-' : $data['c_address_road']), $border, "C");//ถนน

        $y_point += $y;
        $pdf->SetXY( 39, $y_point );
        $pdf->MultiCell(43 , 5, U2T(($data['c_amphur_name']=='') ? '-' : $data['c_amphur_name']), $border, "C");//แขวง/ตำบล
        $pdf->SetXY( 100, $y_point );
        $pdf->MultiCell(37 , 5, U2T(($data['amphur_name']=='') ? '-' : $data['amphur_name']), $border, "C");//เขต/อำเภอ
        $pdf->SetXY( 152, $y_point );
        $pdf->MultiCell(40 , 5, U2T(($data['province_name']=='') ? '-' : $data['province_name']), $border, "C");//จำหวัด

        $y_point += $y;
        $pdf->SetXY( 47, $y_point );
        $pdf->MultiCell(33 , 5, U2T($data['tel']), $border, "C");//โทรศัพท์บ้าน
        $pdf->SetXY( 93, $y_point );
        $pdf->MultiCell(40 , 5, U2T($data['mobile']), $border, "C");//มือถือ
        $pdf->SetXY( 149, $y_point );
        $pdf->MultiCell(40 , 5, U2T($data['position_name']), $border, "C");//ตำแหน่ง
        $y_point += $y;
        $pdf->SetXY( 29, $y_point );
        $pdf->MultiCell(34 , 5, U2T(($data['short_mem_group_name_level']=='') ? '-' : $data['short_mem_group_name_level']), $border, "C");//สังกัด
        $pdf->SetXY( 102, $y_point );
        $pdf->MultiCell(19 , 5, U2T(number_format($data['salary'])), $border, "C");//เงินเดือน
        $pdf->SetXY( 132, $y_point );
        $number_text = $this->center_function->convert($data['salary']);
        $pdf->MultiCell(58 , 5, U2T($number_text), $border, "C");//เงินเดือนตัวอักษร
        $y_point += $y;
        $pdf->SetXY( 152, $y_point );
        $pdf->MultiCell(30 , 5, U2T($data['member_id']), $border, "C");//ทะเบียนสมาชิก

        $y_point += 23.3;
        $pdf->SetXY( 74, $y_point );
        // $pdf->MultiCell(13 , 5, U2T($data['petition_number']), $border, "L");//สัญญากู้เลขที่
        $pdf->SetXY( 116, $y_point );
        // $pdf->MultiCell(6 , 5, U2T($data['petition_number']), $border, "L");//คำขอกู้เลขที่

        $y_point = 241.8;
        $pdf->SetXY( 80, $y_point );
        $pdf->MultiCell(54 , 5, U2T($name), $border, "C");//ลงชื่อ ผู้ให้คำยินยอม
        $y_point += $y;
        $pdf->SetXY( 79, $y_point );
        $pdf->MultiCell(54 , 5, U2T($fullname_th), $border, "C");//ลายเซ็น ผู้ให้คำยินยอม
        // $y_point += $y;
        // $pdf->SetXY( 81, $y_point );
        // $pdf->MultiCell(50 , 5, U2T('t45'), $border, "L");//ลงชื่อ พยาน
        // $y_point += $y;
        // $pdf->SetXY( 81, $y_point );
        // $pdf->MultiCell(50 , 5, U2T('t46'), $border, "L");//ลายเซ็น พยาน
        // $y_point += $y;
        // $pdf->SetXY( 81, $y_point );
        // $pdf->MultiCell(50 , 5, U2T('t47'), $border, "L");//ลงชื่อ พยาน
        // $y_point += $y;
        // $pdf->SetXY( 81, $y_point );
        // $pdf->MultiCell(50 , 5, U2T('t48'), $border, "L");//ลายเซ็น พยาน
    }
}
$pdf->Output();