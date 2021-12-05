<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/loan/ordinary-loan-agreement.pdf" ;
// $filename = $_SERVER["DOCUMENT_ROOT"]."/fsccoop/assets/document/loan/ordinary-loan-agreement.pdf" ;
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
if ($data['approve_date'] != '') {
    $year_start_period = (substr($data['approve_date'], 0, 4)) + 543; // ปีที่จ่ายค่างวด(หุ้น)
}
$full_start_period  = $day_start_period."  ".$month_start_period."  ".$year_start_period; // วัน, เดือน, ปี ที่จ่ายค่างวด(หุ้น)
$fullname_th        = $data['prename_full'].$data['firstname_th']."  ".$data['lastname_th']; // คำนำหน้าชื่อ , ชื่อ-สกุล (ผู้กู้)
$contract_number_font = substr($data['contract_number'], 0, -8); // ตัวอักษรหน้า เลขที่สัญญา ex. ฉฉ999999 = ฉฉ
$contract_number_back = substr($data['contract_number'], -9);   //ตัวอักษรหลัง เลขที่สัญญา ex. 999999 = ฉฉ

if ($data['createdatetime'] != ''){
    $create_year       = (substr($data['createdatetime'], 0, 4))+543; //  ปีที่บันทึกข้อมูลล
}
$day_start_period   = number_format(substr($data['date_start_period'], 8, 2));
$month_start_period = number_format(substr($data['date_start_period'], 5, 2));
$year_start_period  = (substr($data['date_start_period'], 0, 4))+543;
$full_start_period  = $day_start_period."  ".$month_start_period."  ".$year_start_period;
$fullname_th        = $data['prename_full'].$data['firstname_th']."  ".$data['lastname_th'];
$period_amount = substr($this->center_function->convert($data['period_amount']),0,-3*7); //งวด(ตัวอักษร)
$limit_loan_amount_balance = $data['salary']*50 > 1000000? 1000000:$data['salary']*50;

for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {
    $pdf->AddPage();
    $tplIdx = $pdf->importPage($pageNo);
    $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
    $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
    $pdf->SetFont('THSarabunNew', '', 14);
    $border = isset($_GET['show']) && $_GET['show'] == '1' ? 1 : 0;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetAutoPageBreak(true, 0);
    $day = date('d');
    $mount = date('m');
    $int_mount = (int)$mount;
    $date = date('d/m/');
    $year = date('Y') + 543;
    $date = $date . $year;
    if ($pageNo == '1') {
        $y = 8.7;

        $y_point = 16.2;
        $pdf->SetXY(26, $y_point);
        $pdf->MultiCell(77, 5, U2T($fullname_th), $border, "C");//ชื่อผู้ขออกู้
        $pdf->SetXY(127, $y_point);
        $pdf->MultiCell(17, 5, U2T($data['member_id']), $border, "C");//เลขที่สมาชิก
        $pdf->SetXY(163, $y_point);
        $pdf->MultiCell(40, 5, U2T($data['mem_group_name_department']), $border, "C");//หน่วยงาน
        $y_point += $y;
        $pdf->SetXY(47, $y_point);
        $pdf->MultiCell(57, 5, U2T($data['coop_loan_guarantee'][0]['full_name_th']), $border, "C");//ชื่อผู้ค้ำ1
        $pdf->SetXY(127, $y_point);
        $pdf->MultiCell(17, 5, U2T($data['coop_loan_guarantee'][0]['member_id']), $border, "C");//เลขที่สมาชิก1
        $pdf->SetXY(163, $y_point);
        $pdf->MultiCell(40, 5, U2T($data['coop_loan_guarantee'][0]['mem_group_name_department']), $border, "C");//หน่อบงาน1
        $y_point += $y;
        $pdf->SetXY(47, $y_point);
        $pdf->MultiCell(57, 5, U2T($data['coop_loan_guarantee'][1]['full_name_th']), $border, "C");//ชื่อผู้ค้ำ2
        $pdf->SetXY(127, $y_point);
        $pdf->MultiCell(17, 5, U2T($data['coop_loan_guarantee'][1]['member_id']), $border, "C");//เลขที่สมาชิก2
        $pdf->SetXY(163, $y_point);
        $pdf->MultiCell(40, 5, U2T($data['coop_loan_guarantee'][1]['mem_group_name_department']), $border, "C");//หน่อบงาน2
        $y_point += $y;
        $pdf->SetXY(47, $y_point);
        $pdf->MultiCell(57, 5, U2T($data['coop_loan_guarantee'][2]['full_name_th']), $border, "C");//ชื่อผู้ค้ำ2
        $pdf->SetXY(127, $y_point);
        $pdf->MultiCell(17, 5, U2T($data['coop_loan_guarantee'][2]['member_id']), $border, "C");//เลขที่สมาชิก3
        $pdf->SetXY(163, $y_point);
        $pdf->MultiCell(40, 5, U2T($data['coop_loan_guarantee'][2]['mem_group_name_department']), $border, "C");//หน่อบงาน3

        $y_point = 71.5;
        if ($data['marry_status'] == 1 || $data['marry_status'] == 0) {
            $pdf->Image($myImage, 12.6, $y_point, 3);
        } else if ($data['marry_status'] == 2) {
            $pdf->Image($myImage, 31.6, $y_point, 3);
        } else if ($data['marry_status'] == 3) {
            $pdf->Image($myImage, 51.6, $y_point, 3);
        } else if ($data['marry_status'] == 4) {
            $pdf->Image($myImage, 67.6, $y_point, 3);
        }

        if (!empty($data['coop_loan_guarantee'][0])) {
            if ($data['coop_loan_guarantee'][0]['marry_status'] == 1 || $data['coop_loan_guarantee'][0]['marry_status'] == 0) {
                $pdf->Image($myImage, 110.6, $y_point, 3);
            } else if ($data['coop_loan_guarantee'][0]['marry_status'] == 2) {
                $pdf->Image($myImage, 133.1, $y_point, 3);
            } else if ($data['coop_loan_guarantee'][0]['marry_status'] == 3) {
                $pdf->Image($myImage, 156.1, $y_point, 3);
            } else if ($data['coop_loan_guarantee'][0]['marry_status'] == 4) {
                $pdf->Image($myImage, 177.6, $y_point, 3);
            }
        }
        $y_point += 6.8;
        if (!empty($data['coop_loan_guarantee'][1])) {
            if ($data['coop_loan_guarantee'][0]['marry_status'] == 1 || $data['coop_loan_guarantee'][0]['marry_status'] == 0) {
                $pdf->Image($myImage, 110.6, $y_point, 3);
            } else if ($data['coop_loan_guarantee'][0]['marry_status'] == 2) {
                $pdf->Image($myImage, 133.1, $y_point, 3);
            } else if ($data['coop_loan_guarantee'][0]['marry_status'] == 3) {
                $pdf->Image($myImage, 156.1, $y_point, 3);
            } else if ($data['coop_loan_guarantee'][0]['marry_status'] == 4) {
                $pdf->Image($myImage, 177.6, $y_point, 3);
            }
        }
        $y_point = 97;
//        $pdf->Image($myImage, 8.6, $y_point, 3);
//        $y_point =104;
//        $pdf->Image($myImage, 8.6, $y_point, 3);
//        $y_point =111.3;
//        $pdf->Image($myImage, 8.6, $y_point, 3);
//        $y_point =118.4;
//        $pdf->Image($myImage, 8.6, $y_point, 3);
//        $y_point =125.5;
//        $pdf->Image($myImage, 8.6, $y_point, 3);
//        $y_point =133.2;
//        $pdf->Image($myImage, 8.6, $y_point, 3);
//
//        $y_point = 97.5;
//        $pdf->Image($myImage, 170, $y_point, 3);
//        $y_point =104.5;
//        $pdf->Image($myImage, 170, $y_point, 3);
//        $y_point =111.8;
//        $pdf->Image($myImage, 170, $y_point, 3);
//        $y_point =118.9;
//        $pdf->Image($myImage, 170, $y_point, 3);
//        $y_point =126;
//        $pdf->Image($myImage, 170, $y_point, 3);
//
//        $y_point = 97.5;
//        $pdf->Image($myImage, 187, $y_point, 3);
//        $y_point =104.5;
//        $pdf->Image($myImage, 187, $y_point, 3);
//        $y_point =111.8;
//        $pdf->Image($myImage, 187, $y_point, 3);
//        $y_point =118.9;
//        $pdf->Image($myImage, 187, $y_point, 3);
//        $y_point =126;
//        $pdf->Image($myImage, 187, $y_point, 3);
//
//        $y_point = 157;
//        $pdf->Image($myImage, 8.6, $y_point, 3);
//        $y_point = 180.5;
//        $pdf->Image($myImage, 8.6, $y_point, 3);
//        $y_point = 187.7;
//        $pdf->Image($myImage, 8.6, $y_point, 3);
//        $y_point = 194.9;
//        $pdf->Image($myImage, 8.6, $y_point, 3);
//        $y_point = 208.9;
//        $pdf->Image($myImage, 8.6, $y_point, 3);
//        $y_point = 224.5;
//        $pdf->Image($myImage, 8.6, $y_point, 3);
//        $y_point = 240;
//        $pdf->Image($myImage, 8.6, $y_point, 3);
//
//        $y_point =157.2;
//        $pdf->Image($myImage, 170, $y_point, 3);
//        $pdf->Image($myImage, 187, $y_point, 3);
//
//        $y_point = 180.5;
//        $pdf->Image($myImage, 170, $y_point, 3);
//        $y_point = 187.7;
//        $pdf->Image($myImage, 170, $y_point, 3);
//        $y_point = 194.9;
//        $pdf->Image($myImage, 170, $y_point, 3);
//        $y_point = 208.9;
//        $pdf->Image($myImage, 172, $y_point, 3);
//        $y_point = 180.5;
//        $pdf->Image($myImage, 187, $y_point, 3);
//        $y_point = 187.7;
//        $pdf->Image($myImage, 187, $y_point, 3);
//        $y_point = 194.9;
//        $pdf->Image($myImage, 187, $y_point, 3);
//        $y_point = 208.9;
//        $pdf->Image($myImage, 189, $y_point, 3);
//        $y_point = 224.5;
//        $pdf->Image($myImage, 170, $y_point, 3);
//        $y_point = 240;
//        $pdf->Image($myImage, 170, $y_point, 3);
//        $y_point = 224.5;
//        $pdf->Image($myImage, 188, $y_point, 3);
//        $y_point = 240;
//        $pdf->Image($myImage, 188, $y_point, 3);

        $y = 7.63;
        $y_point = 257.8;
        $pdf->SetXY(80, $y_point);
        $pdf->MultiCell(65, 5, U2T($coop_signature['manager_name']), $border, "C");//รองผู้จัดการ
        $pdf->SetXY(161, $y_point);
        $pdf->MultiCell(10, 5, U2T($date_to_text), $border, "C");//วัน
        $pdf->SetXY(172, $y_point);
        $pdf->MultiCell(10, 5, U2T($month_short_arr[$date_to_month]), $border, "C");//เดือน
        $y_point += $y;
        $pdf->SetXY(80, $y_point);
        $pdf->MultiCell(65, 5, U2T($coop_signature['receive_name']), $border, "C");//หัวหน้าผ่ายสินเชื่อ
        $pdf->SetXY(161, $y_point);
        $pdf->MultiCell(10, 5, U2T($date_to_text), $border, "C");//วัน
        $pdf->SetXY(172, $y_point);
        $pdf->MultiCell(10, 5, U2T($month_short_arr[$date_to_month]), $border, "C");//เดือน
        $y_point += $y;
        $pdf->SetXY(80, $y_point);
        $pdf->MultiCell(65, 5, U2T($coop_signature['finance_name']), $border, "C");//เจ้าหน้าที่ สอ.รพ.
        $pdf->SetXY(161, $y_point);
        $pdf->MultiCell(10, 5, U2T($date_to_text), $border, "C");//วัน
        $pdf->SetXY(172, $y_point);
        $pdf->MultiCell(10, 5, U2T($month_short_arr[$date_to_month]), $border, "C");//เดือน

    } else if ($pageNo == '2') {

    } else if ($pageNo == '3') {
        $y = 7.67;
        $y_point = 34;
        $pdf->SetXY(32.5, $y_point);
        $pdf->MultiCell(14, 5, U2T('***'), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(166.5, $y_point - 1.5);
        $pdf->MultiCell(14, 5, U2T($contract_number_font), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(181.5, $y_point - 1.5);
        $pdf->SetFont('THSarabunNew', '', 10);
        $pdf->MultiCell(14, 5, U2T($contract_number_back), $border, "C");//หนังสือกู้ที่
        $pdf->SetFont('THSarabunNew', '', 14);
        $y_point += $y;
        $pdf->SetXY(32.5, $y_point);
        $pdf->MultiCell(9, 5, U2T($date_to_text), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(42.5, $y_point);
        $pdf->MultiCell(9, 5, U2T($date_to_month), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(52.5, $y_point);
        $pdf->MultiCell(10, 5, U2T($date_to_year), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(160, $y_point - 1.5);
        $pdf->MultiCell(9, 5, U2T($date_to_text), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(170, $y_point - 1.5);
        $pdf->MultiCell(9, 5, U2T($date_to_month), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(181.5, $y_point - 1.5);
        $pdf->MultiCell(11, 5, U2T($date_to_year), $border, "C");//หนังสือกู้ที่

        $y_point = 58;
        $pdf->SetXY(133, $y_point);
        $pdf->MultiCell(60, 5, U2T($location['profile_location']['coop_name_th']), $border, "C");//หนังสือกู้ที่
        $y_point += $y;
        $pdf->SetXY(133, $y_point);
        $pdf->MultiCell(60, 5, U2T($full_date), $border, "C");//หนังสือกู้ที่

        $y_point = 85.5;
        $pdf->SetXY(45, $y_point);
        $pdf->MultiCell(85, 5, U2T($fullname_th), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(165, $y_point);
        $pdf->MultiCell(30, 5, U2T($data['member_id']), $border, "C");//หนังสือกู้ที่
        $y_point += $y;
        $pdf->SetXY(58, $y_point);
        $pdf->MultiCell(40, 5, U2T(number_format($data['salary'])), $border, "C");//หนังสือกู้ที่
        $y_point += $y;
        $pdf->SetXY(79, $y_point);
        $pdf->MultiCell(30, 5, U2T(number_format($data['loan_amount'])), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(120, $y_point);
        $pdf->MultiCell(71, 5, U2T($money_loan_amount_2text), $border, "C");//หนังสือกู้ที่
        $y_point += $y;
        $pdf->SetXY(124, $y_point);
        $pdf->MultiCell(71, 5, U2T($data['loan_reason']), $border, "C");//หนังสือกู้ที่
        $y_point += $y;
        $pdf->SetXY(119, $y_point);
        $pdf->MultiCell(71, 5, U2T(($data['position_name'] == '') ? '-' : $data['position_name']), $border, "C");//หนังสือกู้ที่
        $y_point += $y;
        $pdf->SetXY(105, $y_point);
        $pdf->MultiCell(35, 5, U2T(''), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(154, $y_point);
        $pdf->MultiCell(40, 5, U2T($data['short_mem_group_name_level']), $border, "C");//หนังสือกู้ที่
        $y_point += $y;
        $pdf->SetXY(56, $y_point);
        $pdf->MultiCell(25, 5, U2T($data['c_address_no']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(90, $y_point);
        $pdf->MultiCell(30, 5, U2T($data['c_address_road']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(132, $y_point);
        $pdf->MultiCell(25, 5, U2T($data['c_district_name']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(168, $y_point);
        $pdf->MultiCell(27, 5, U2T($data['c_amphur_name']), $border, "C");//หนังสือกู้ที่
        $y_point += $y;
        $pdf->SetXY(32.5, $y_point);
        $pdf->MultiCell(39, 5, U2T($data['c_province_name']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(94, $y_point);
        $pdf->MultiCell(30, 5, U2T($data['zipcode']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(140, $y_point);
        $pdf->MultiCell(55, 5, U2T($data['mobile']), $border, "C");//หนังสือกู้ที่
        $y_point = 177.3;
        $pdf->SetXY(35, $y_point);
        $pdf->SetFont('THSarabunNew', '', 10);
        $pdf->MultiCell(46, 5, U2T($data['coop_loan_guarantee'][0]['full_name_th']), $border, "C");//หนังสือกู้ที่
        $pdf->SetFont('THSarabunNew', '', 14);
        $pdf->SetXY(82, $y_point);
        $pdf->MultiCell(18, 5, U2T($data['coop_loan_guarantee'][0]['member_id']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(101, $y_point);
        $pdf->MultiCell(33, 5, U2T($data['coop_loan_guarantee'][0]['short_mem_group_name_level']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(135, $y_point);
        if (!empty($data['coop_loan_guarantee'][0])) {
            $pdf->MultiCell(20, 5, U2T(number_format($data['coop_loan_guarantee'][0]['salary'])), $border, "C");//หนังสือกู้ที่
        }
        $pdf->SetXY(156, $y_point);
        $pdf->MultiCell(37, 5, U2T(''), $border, "C");//หนังสือกู้ที่
        $y_point = 185;
        $pdf->SetXY(35, $y_point);
        $pdf->SetFont('THSarabunNew', '', 10);
        $pdf->MultiCell(46, 5, U2T($data['coop_loan_guarantee'][1]['full_name_th']), $border, "C");//หนังสือกู้ที่
        $pdf->SetFont('THSarabunNew', '', 14);
        $pdf->SetXY(82, $y_point);
        $pdf->MultiCell(18, 5, U2T($data['coop_loan_guarantee'][1]['member_id']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(101, $y_point);
        $pdf->MultiCell(33, 5, U2T($data['coop_loan_guarantee'][1]['short_mem_group_name_level']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(135, $y_point);
        if (!empty($data['coop_loan_guarantee'][1])) {
            $pdf->MultiCell(20, 5, U2T(number_format($data['coop_loan_guarantee'][1]['salary'])), $border, "C");//หนังสือกู้ที่
        }
        $pdf->SetXY(156, $y_point);
        $pdf->MultiCell(37, 5, U2T(''), $border, "C");//หนังสือกู้ที่
        $y_point = 192.7;
        $pdf->SetXY(35, $y_point);
        $pdf->SetFont('THSarabunNew', '', 10);
        $pdf->MultiCell(46, 5, U2T($data['coop_loan_guarantee'][2]['full_name_th']), $border, "C");//หนังสือกู้ที่
        $pdf->SetFont('THSarabunNew', '', 14);
        $pdf->SetXY(82, $y_point);
        $pdf->MultiCell(18, 5, U2T($data['coop_loan_guarantee'][2]['member_id']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(101, $y_point);
        $pdf->MultiCell(33, 5, U2T($data['coop_loan_guarantee'][2]['short_mem_group_name_level']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(135, $y_point);
        if (!empty($data['coop_loan_guarantee'][2])) {
            $pdf->MultiCell(20, 5, U2T(number_format($data['coop_loan_guarantee'][2]['salary'])), $border, "C");//หนังสือกู้ที่
        }
        $pdf->SetXY(156, $y_point);
        $pdf->MultiCell(37, 5, U2T(''), $border, "C");//หนังสือกู้ที่
        $y_point = 207.3;
        if ($data['pay_type'] == 1) {
            $pdf->Image($myImage, 42.5, $y_point + 1.3, 3);
            $pdf->SetXY(86, $y_point);
            $pdf->MultiCell(36, 5, U2T(number_format($data['total_paid_per_month'], 2)), $border, "C"); //ต้นเงินเท่ากันทุกงวดๆ ละ.
            $pdf->SetXY(170, $y_point);
            $pdf->MultiCell(18, 5, U2T($data['period_amount']), $border, "C"); //จำนวนงวด
        } else if ($data['pay_type'] == 2) {
            $y_point += $y;
            $pdf->Image($myImage, 42.5, $y_point + 1.3, 3);
            $pdf->SetXY(105, $y_point);
            $pdf->MultiCell(36, 5, U2T(number_format($data['total_paid_per_month'], 2)), $border, "C"); //ต้นเงินและดอกเบ้ียเท่ากันทุกงวดๆละ
            $pdf->SetXY(170, $y_point);
            $pdf->MultiCell(18, 5, U2T($data['period_amount']), $border, "C"); //จำนวนงวด
        }
        $y_point = 238;
        $pdf->SetXY(135, $y_point);
        $pdf->MultiCell(50, 5, U2T(''), $border, "C");//หนังสือกู้ที่
        $y_point += $y;
        $pdf->SetXY(127, $y_point);
        $pdf->MultiCell(67, 5, U2T($fullname_th), $border, "C");//หนังสือกู้ที่
        $y_point = 276.5;
        $pdf->SetXY(135, $y_point);
        $pdf->MultiCell(50, 5, U2T(''), $border, "C");//หนังสือกู้ที่
        $y_point += $y;
        $pdf->SetXY(125, $y_point);
        $pdf->MultiCell(68, 5, U2T($fullname_th), $border, "C");//หนังสือกู้ที่


    } else if ($pageNo == '4') {
        $y_point = 21.3;
        $pdf->SetXY(128, $y_point);
        $pdf->MultiCell(46.5, 5, U2T($full_date), $border, "C");//หนังสือกู้ที่
        $y_point = 45;
//        $pdf->Image($myImage, 65.5, $y_point, 3);
//        $pdf->Image($myImage, 104, $y_point, 3);
        $y_point = 60.7;
//        $pdf->Image($myImage, 66, $y_point, 3);
//        $pdf->Image($myImage, 104, $y_point, 3);
        $y_point = 75.7;
//        $pdf->Image($myImage, 66, $y_point, 3);
//        $pdf->Image($myImage, 104, $y_point, 3);
        $y_point = 117;
        $pdf->SetXY(88, $y_point);
        $pdf->MultiCell(63, 5, U2T(number_format($data['loan_amount'])), $border, "C");//หนังสือกู้ที่
        $y_point = 145;
        $pdf->SetXY(18, $y_point);
        $pdf->MultiCell(15, 5, U2T(number_format($data['salary'])), $border, "C");//เงินเดือน
        $pdf->SetXY(33, $y_point);
        $pdf->MultiCell(17, 5, U2T(number_format($share_group['share_collect_value'])), $border, "C");//เงินค่าหุ้น(บาท)
        $pdf->SetXY(50, $y_point);
//        $pdf->MultiCell(16 , 5, U2T(number_format($limit_loan_amount_balance)), $border, "C");//จำกัดวงเงินกู้(บาท)
        $pdf->SetXY(66.3, $y_point);
        $pdf->MultiCell(15.5, 5, U2T(''), $border, "C");// น/สกู้ (สามัญ)
        $pdf->SetXY(82, $y_point);
        $pdf->MultiCell(16.5, 5, U2T(''), $border, "C");// จำนวนเงิน
        $pdf->SetXY(98.5, $y_point);
        $pdf->MultiCell(15, 5, U2T(''), $border, "C");// น/สกู้(ฉุกเฉิน)
        $pdf->SetXY(113.5, $y_point);
        $pdf->MultiCell(17.5, 5, U2T(''), $border, "C");// จำนวนเงิน
        $pdf->SetXY(131.5, $y_point);
        $pdf->MultiCell(14.5, 5, U2T(''), $border, "C");// จำนวนเงิน
        $pdf->SetXY(146, $y_point);
        $pdf->MultiCell(17.5, 5, U2T(''), $border, "C");// จำนวนเงิน
        $pdf->SetXY(163.5, $y_point);
        $pdf->MultiCell(12.8, 5, U2T(''), $border, "C");// จำนวนเงิน
        $pdf->SetXY(176.5, $y_point);
        $pdf->MultiCell(24, 5, U2T(''), $border, "C");// จำนวนเงิน
        $y_point = 159.7;
        if (false) {
            $pdf->Image($myImage, 155.5, $y_point, 3);
        } else {
            $pdf->Image($myImage, 168.5, $y_point, 3);
        }
        $y_point = 182.5;
        $pdf->SetXY(105, $y_point);
        $pdf->MultiCell(47, 5, U2T(number_format($data['coop_loan_guarantee'][0]['amount'], 2)), $border, "C");//เงินเดือน
        $pdf->SetFont('THSarabunNew', '', 10);
        if (!empty($data['coop_loan_guarantee'][0])) {
            $limit_guarantee_amount_balance = $data['coop_loan_guarantee'][0]['salary'] * 50 > 1000000 ? 1000000 : $data['coop_loan_guarantee'][0]['salary'] * 50;
            $y_point = 221;
            $pdf->SetXY(27, $y_point);
            $pdf->MultiCell(48, 5, U2T($data['coop_loan_guarantee'][0]['full_name_th']), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(75.5, $y_point);
            $pdf->MultiCell(16, 5, U2T(number_format($data['coop_loan_guarantee'][0]['salary'], 2)), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(92.5, $y_point);
            $pdf->MultiCell(16, 5, U2T(number_format($limit_guarantee_amount_balance, 2)), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(109.5, $y_point);
            $pdf->MultiCell(50, 5, U2T($fullname_th), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(160.2, $y_point);
            $pdf->MultiCell(16, 5, U2T(''), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(177.2, $y_point);
            $pdf->MultiCell(19.5, 5, U2T(number_format($limit_guarantee_amount_balance - $data['coop_loan_guarantee'][0]['amount'], 2)), $border, "C");//หนังสือกู้ที่
        }
        if (!empty($data['coop_loan_guarantee'][1])) {
            $y_point = 227.5;
            $pdf->SetXY(27, $y_point);
            $pdf->MultiCell(48, 5, U2T($data['coop_loan_guarantee'][1]['full_name_th']), $border, "L");//หนังสือกู้ที่
            $pdf->SetXY(75.5, $y_point);
            $pdf->MultiCell(16, 5, U2T(number_format($data['coop_loan_guarantee'][1]['salary'], 2)), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(92.5, $y_point);
            $pdf->MultiCell(16, 5, U2T(number_format($limit_guarantee_amount_balance, 2)), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(109.5, $y_point);
            $pdf->MultiCell(50, 5, U2T($fullname_th), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(160.2, $y_point);
            $pdf->MultiCell(16, 5, U2T(''), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(177.2, $y_point);
            $pdf->MultiCell(19.5, 5, U2T(number_format($limit_guarantee_amount_balance - $data['coop_loan_guarantee'][1]['amount'], 2)), $border, "C");//หนังสือกู้ที่
        }
        if (!empty($data['coop_loan_guarantee'][2])) {
            $y_point = 234;
            $pdf->SetXY(27, $y_point);
            $pdf->MultiCell(48, 5, U2T($data['coop_loan_guarantee'][2]['full_name_th']), $border, "L");//หนังสือกู้ที่
            $pdf->SetXY(75.5, $y_point);
            $pdf->MultiCell(16, 5, U2T(number_format($data['coop_loan_guarantee'][2]['salary'], 2)), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(92.5, $y_point);
            $pdf->MultiCell(16, 5, U2T(number_format($limit_guarantee_amount_balance, 2)), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(109.5, $y_point);
            $pdf->MultiCell(50, 5, U2T($fullname_th), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(160.2, $y_point);
            $pdf->MultiCell(16, 5, U2T(''), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(177.2, $y_point);
            $pdf->MultiCell(19.5, 5, U2T(number_format($limit_guarantee_amount_balance - $data['coop_loan_guarantee'][2]['amount'], 2)), $border, "C");//หนังสือกู้ที่
        }
        if (!empty($data['coop_loan_guarantee'][3])) {
            $y_point = 240.5;
            $pdf->SetXY(27, $y_point);
            $pdf->MultiCell(48, 5, U2T($data['coop_loan_guarantee'][3]['full_name_th']), $border, "L");//หนังสือกู้ที่
            $pdf->SetXY(75.5, $y_point);
            $pdf->MultiCell(16, 5, U2T(number_format($data['coop_loan_guarantee'][3]['salary'], 2)), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(92.5, $y_point);
            $pdf->MultiCell(16, 5, U2T(number_format($limit_guarantee_amount_balance, 2)), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(109.5, $y_point);
            $pdf->MultiCell(50, 5, U2T($fullname_th), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(160.2, $y_point);
            $pdf->MultiCell(16, 5, U2T(''), $border, "C");//หนังสือกู้ที่
            $pdf->SetXY(177.2, $y_point);
            $pdf->MultiCell(19.5, 5, U2T(number_format($limit_guarantee_amount_balance - $data['coop_loan_guarantee'][3]['amount'], 2)), $border, "C");//หนังสือกู้ที่
        }
        $pdf->SetFont('THSarabunNew', '', 14);

    } else if ($pageNo == '5') {
        $y = 7.65;
        $y_point = 38;
        $pdf->SetXY(161, $y_point);
        $pdf->MultiCell(15, 5, U2T($contract_number_font), $border, "C");//ที่
        $pdf->SetXY(178, $y_point);
        $pdf->SetFont('THSarabunNew', '', 12);
        $pdf->MultiCell(15, 5, U2T($contract_number_back), $border, "C");//ที่
        $pdf->SetFont('THSarabunNew', '', 14);

        $y_point += $y;
        $pdf->SetXY(157, $y_point);
        $pdf->MultiCell(34, 5, U2T($full_date), $border, "C");//วันที่

        $y_point += $y;
        $pdf->SetXY(45, $y_point);
        $pdf->MultiCell(150, 5, U2T($fullname_th), $border, "C");//ชื่อ
        $y_point += $y;
        $pdf->SetXY(45, $y_point);
        $pdf->MultiCell(150, 5, U2T($fullname_th), $border, "C");//ชื่อ
        $y_point += $y;
        $pdf->SetXY(157, $y_point);
        $pdf->MultiCell(35, 5, U2T($data['member_id']), $border, "C");//หนังสือกู้ที่
        $y_point += $y;
        $pdf->SetXY(80, $y_point);
        $pdf->MultiCell(58, 5, U2T($data['position_name']), $border, "C");//หนังสือกู้ที่
        $y_point += $y;
        $pdf->SetXY(46, $y_point);
        $pdf->MultiCell(35, 5, U2T($data['id_card']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(92, $y_point);
        $pdf->MultiCell(45, 5, U2T($data['mem_group_full_name_level']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(170, $y_point);
        $pdf->MultiCell(25, 5, U2T($data['c_address_no']), $border, "C");//หนังสือกู้ที่

        $y_point += $y;
        $pdf->SetXY(29, $y_point);
        $pdf->MultiCell(30, 5, U2T(($data['c_address_road'] == '') ? '-' : $data['c_address_road']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(70, $y_point);
        $pdf->MultiCell(33, 5, U2T(($data['c_district_name'] == '') ? '-' : $data['district_name']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(115, $y_point);
        $pdf->MultiCell(33, 5, U2T($data['c_amphur_name']), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(161, $y_point);
        $pdf->MultiCell(33, 5, U2T($data['c_province_name']), $border, "C");//หนังสือกู้ที่

        $y_point += $y;
        $pdf->SetXY(35, $y_point);
        $pdf->MultiCell(55, 5, U2T($data['mobile']), $border, "C");//หนังสือกู้ที่
        $y_point += $y + $y;
        $pdf->SetXY(102, $y_point);
        $pdf->MultiCell(23, 5, U2T(number_format($data['loan_amount'])), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(134, $y_point);
        $pdf->MultiCell(56, 5, U2T($money_salary_2text), $border, "C");//หนังสือกู้ที่

        $y_point += $y + $y + $y;
        if ($data['pay_type'] == 1) {
            $pdf->Image($myImage, 42.5, $y_point + 1.3, 3);
            $pdf->SetXY(105, $y_point);
            $pdf->MultiCell(60, 5, U2T($this->center_function->convert($data['total_paid_per_month'])), $border, "C");//หนังสือกู้ที่
        } else {
            $y_point += $y;
            $pdf->Image($myImage, 42.5, $y_point + 1.3, 3);
            $pdf->SetXY(122, $y_point);
            $pdf->MultiCell(65, 5, U2T($this->center_function->convert($data['total_paid_per_month'])), $border, "C");//หนังสือกู้ที่
        }
        $y_point = 152.75;
        $pdf->SetXY(48, $y_point);
        $pdf->MultiCell(28, 5, U2T(str_replace("บาทถ้วน", "", $this->center_function->convert($data['period_amount']))), $border, "C");//หนังสือกู้ที่
        $pdf->SetXY(155, $y_point);
        $pdf->MultiCell(30, 5, U2T($this->center_function->convert($data['interest_per_year'], 'unit')), $border, "C");//หนังสือกู้ที่
        $y_point += $y;
        $pdf->SetXY(60, $y_point);
        $pdf->MultiCell(30, 5, U2T($month_arr[(int)substr($data['date_period_1'], 5, 2)]), $border, "C");//หนังสือกู้ที่

    } else if ($pageNo == '6') {
        $y = 7.63;
        $y_point = 43;
        $pdf->SetXY(119, $y_point);
        $pdf->MultiCell(30, 5, U2T(''), $border, "C");//สหกรณ์เลขที่
        $pdf->SetXY(162, $y_point);
        $pdf->MultiCell(25, 5, U2T(number_format($share_group['share_collect'])), $border, "C");//หุ้น
        $y_point += $y;
        $pdf->SetXY(32, $y_point);
        $pdf->MultiCell(40, 5, U2T(number_format($share_group['share_collect_value'])), $border, "C");//จำนวนเงิน
        $y_point = 100.6;
        $pdf->SetXY(124, $y_point);
        $pdf->MultiCell(50, 5, U2T(''), $border, "C");//ผู้กู้
        $y_point += $y;
        $pdf->SetXY(122.5, $y_point);
        $pdf->SetFont('THSarabunNew', '', 12);
        $pdf->MultiCell(53, 5, U2T($fullname_th), $border, "C");//ผู้กู้
        $pdf->SetFont('THSarabunNew', '', 14);
        $y_point += $y;
        $pdf->SetXY(124, $y_point);
        $pdf->MultiCell(50, 5, U2T(''), $border, "C");//พยาน
        $y_point += $y;
        $pdf->SetXY(124, $y_point);
        $pdf->MultiCell(50, 5, U2T(''), $border, "C");//พยาน
        $y_point += $y;
        $pdf->SetXY(124, $y_point);
        $pdf->MultiCell(50, 5, U2T(''), $border, "C");//พยาน
        $y_point += $y;
        $pdf->SetXY(124, $y_point);
        $pdf->MultiCell(50, 5, U2T(''), $border, "C");//พยาน

        $y_point = 162.8;
        if (!empty($data['marry_name'])) {
            $pdf->SetXY(148, $y_point);
            $pdf->MultiCell(40, 5, U2T(''), $border, "C");//ผู้กู้
            $y_point += $y;
            $pdf->SetXY(148, $y_point);
            $pdf->MultiCell(40, 5, U2T($full_date), $border, "C");//ผู้กู้
            $y_point += $y;
            $pdf->SetXY(45, $y_point);
            $pdf->MultiCell(55, 5, U2T($data['marry_name']), $border, "C");//ผู้กู้
            $pdf->SetXY(126, $y_point);
            $pdf->MultiCell(65, 5, U2T($fullname_th), $border, "C");//ผู้กู้
            $y_point += $y;
            $pdf->SetXY(52.5, $y_point);
            $pdf->MultiCell(55, 5, U2T($fullname_th), $border, "C");//ผู้กู้
            $y_point += $y + $y;
            $pdf->SetXY(110, $y_point);
            $pdf->MultiCell(43, 5, U2T(''), $border, "C");//ผู้กู้
            $y_point += $y;
            $pdf->SetXY(110, $y_point);
            $pdf->SetFont('THSarabunNew', '', 10);
            $pdf->MultiCell(43, 5, U2T($data['marry_name']), $border, "C");//ผู้กู้
            $pdf->SetFont('THSarabunNew', '', 14);
            $y_point += $y;
            $pdf->SetXY(110, $y_point);
            $pdf->MultiCell(43, 5, U2T(''), $border, "C");//ผู้กู้
            $y_point += $y;
            $pdf->SetXY(110, $y_point);
            $pdf->SetFont('THSarabunNew', '', 10);
            $pdf->MultiCell(43, 5, U2T($fullname_th), $border, "C");//ผู้กู้
            $pdf->SetFont('THSarabunNew', '', 14);
        }

        $y_point = 235.5;
        $pdf->SetXY(32, $y_point);
        $pdf->MultiCell(85, 5, U2T($fullname_th), $border, "C");//ผู้กู้
        $pdf->SetXY(150, $y_point);
        $pdf->MultiCell(35, 5, U2T(number_format($data['loan_amount_balance'])), $border, "C");//ผู้กู้

        $y_point += $y;
        $pdf->SetXY(23, $y_point);
        $pdf->MultiCell(75, 5, U2T($this->center_function->convert($data['loan_amount_balance'])), $border, "C");//ผู้กู้

        $y_point = 255;
        $pdf->SetXY(92, $y_point);
        $pdf->MultiCell(50, 5, U2T(''), $border, "C");//ผู้กู้
        $y_point += $y;
        $pdf->SetXY(92, $y_point);
        $pdf->MultiCell(50, 5, U2T(''), $border, "C");//ผู้กู้
        $y_point = 274.3;
        $pdf->SetXY(92, $y_point);
        $pdf->MultiCell(50, 5, U2T(''), $border, "C");//ผู้กู้
        $y_point = 286;
        $pdf->SetXY(92, $y_point);
        $pdf->MultiCell(50, 5, U2T(''), $border, "C");//ผู้กู้
    } else if ($pageNo == '7') {
        $y_point = 43.7;
        $pdf->SetXY(135, $y_point);
        $pdf->MultiCell(60, 5, U2T($data['mem_group_name_faction']), $border, "C");//ผู้กู้
        $y_point += $y;
        $pdf->SetXY(120, $y_point);
        $pdf->MultiCell(13, 5, U2T($date_to_text), $border, "C");//ผู้กู้
        $pdf->SetXY(143, $y_point);
        $pdf->MultiCell(28, 5, U2T($month_arr[$mount]), $border, "C");//ผู้กู้
        $pdf->SetXY(178.5, $y_point);
        $pdf->MultiCell(13, 5, U2T($date_to_year), $border, "C");//ผู้กู้
        $y_point += $y;
        $pdf->SetXY(45, $y_point);
        $pdf->MultiCell(55, 5, U2T($fullname_th), $border, "C");//ผู้กู้
        $pdf->SetXY(138, $y_point);
        $pdf->MultiCell(37, 5, U2T($data['id_card']), $border, "C");//ผู้กู้
        $pdf->SetXY(182, $y_point);
        $pdf->MultiCell(8, 5, U2T($age), $border, "C");//ผู้กู้


        $y_point += $y;
        $pdf->SetXY(52, $y_point);
        $pdf->MultiCell(15, 5, U2T($data['c_address_no']), $border, "C");//ผู้กู้
        $pdf->SetXY(75, $y_point);
        $pdf->MultiCell(15, 5, U2T($data['c_address_moo']), $border, "C");//ผู้กู้
        $pdf->SetXY(107, $y_point);
        $pdf->MultiCell(35, 5, U2T($data['c_address_soi']), $border, "C");//ผู้กู้
        $pdf->SetXY(150, $y_point);
        $pdf->MultiCell(40, 5, U2T($data['c_address_road']), $border, "C");//ผู้กู้

        $y_point += $y;
        $pdf->SetXY(39, $y_point);
        $pdf->MultiCell(40, 5, U2T($data['c_district_name']), $border, "C");//ผู้กู้
        $pdf->SetXY(98, $y_point);
        $pdf->MultiCell(35, 5, U2T($data['c_amphur_name']), $border, "C");//ผู้กู้
        $pdf->SetXY(150, $y_point);
        $pdf->MultiCell(40, 5, U2T($data['c_province_name']), $border, "C");//ผู้กู้

        $y_point += $y;
        $pdf->SetXY(47, $y_point);
        $pdf->MultiCell(35, 5, U2T($data['tel']), $border, "C");//ผู้กู้
        $pdf->SetXY(95, $y_point);
        $pdf->MultiCell(38, 5, U2T($data['mobile']), $border, "C");//ผู้กู้
        $pdf->SetXY(149, $y_point);
        $pdf->MultiCell(40, 5, U2T($data['position']), $border, "C");//ผู้กู้
        $y_point += $y;
        $pdf->SetXY(30, $y_point);
        $pdf->MultiCell(30, 5, U2T($data['short_mem_group_name_level']), $border, "C");//ผู้กู้
        $pdf->SetXY(100, $y_point);
        $pdf->MultiCell(20, 5, U2T(number_format($data['salary'])), $border, "C");//ผู้กู้
        $pdf->SetXY(132, $y_point);
        $pdf->MultiCell(58, 5, U2T($money_salary_2text), $border, "C");//ผู้กู้
        $y_point += $y;
        $pdf->SetXY(152, $y_point);
        $pdf->MultiCell(40, 5, U2T($data['member_id']), $border, "C");//ผู้กู้
        $y_point += $y + $y + $y + 0.6;
        $pdf->SetFont('THSarabunNew', '', 10);
        $pdf->SetXY(68, $y_point);
        $pdf->MultiCell(14.5, 5, U2T($data['petition_number']), $border, "C");//ผู้กู้
        $pdf->SetFont('THSarabunNew', '', 5);
        $pdf->SetXY(110, $y_point);
//        $pdf->MultiCell(8 , 5, U2T($data['contract_number']), $border, "C");//ผู้กู้
        $pdf->SetFont('THSarabunNew', '', 12);
        $y_point = 246.5;
        $pdf->SetXY(96, $y_point);
        $pdf->MultiCell(55, 5, U2T($fullname_th), $border, "C");
        $pdf->SetFont('THSarabunNew', '', 14);

    }
}

$filename2 = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/loan/ordinary-loan-agreement-guarantee.pdf" ;
//$filename2 = $_SERVER["DOCUMENT_ROOT"]."/fsccoop/assets/document/loan/ordinary-loan-agreement-guarantee.pdf" ;
$pageCount_2 = $pdf->setSourceFile($filename2);
if(!empty($data['coop_loan_guarantee'])){
    foreach ($data['coop_loan_guarantee'] as $key => $coop_loan_guarantee) {
        for ($pageNo = 1; $pageNo <= $pageCount_2; $pageNo++) {
            $pdf->AddPage();
            $tplIdx = $pdf->importPage($pageNo);
            $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
            $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
            $pdf->SetFont('THSarabunNew', '', 14);
            $border = isset($_GET['show']) && $_GET['show'] == '1' ? 1 : 0;
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetAutoPageBreak(true, 0);
            $day = date('d');
            $mount = date('m');
            $int_mount = (int)$mount;
            $date = date('d/m/');
            $year = date('Y') + 543;
            $date = $date . $year;
            if($pageNo == '1'){
                $y = 7.63;
                $y_point = 40;
                $pdf->SetXY( 38, $y_point-1.7);
                $pdf->MultiCell(7.8, 5, U2T(''), $border, "C");
                $pdf->SetXY( 46.8, $y_point-1.7);
                $pdf->MultiCell(10, 5, U2T(''), $border, "C");
                $pdf->SetXY( 165, $y_point );
                $pdf->MultiCell(15 , 5, U2T($contract_number_font), $border, "C");
                $pdf->SetXY( 181, $y_point );
                $pdf->SetFont('THSarabunNew', '', 12);
                $pdf->MultiCell(15 , 5, U2T($contract_number_back), $border, "C");
                $pdf->SetFont('THSarabunNew', '', 14);

                $y_point += $y;
                $pdf->SetXY( 19, $y_point-1.7);
                $pdf->SetFont('THSarabunNew', '', 12);
                $pdf->MultiCell(54, 5, U2T($fullname_th), $border, "C");
                $pdf->SetFont('THSarabunNew', '', 14);
                $y_point = 59.4;
                $pdf->SetXY( 82, $y_point);
                $pdf->MultiCell(85, 5, U2T($coop_loan_guarantee['full_name_th']), $border, "C");//ผู้กู้
                $y_point = 71.5;
                $pdf->SetXY( 143, $y_point);
                $pdf->MultiCell(50, 5, U2T($full_date), $border, "C");
                $y_point = 83.3;
                $pdf->SetXY( 45, $y_point);
                $pdf->MultiCell(150, 5, U2T($coop_loan_guarantee['full_name_th']), $border, "C");//ผู้กู้


                $y_point += $y;
                $pdf->SetXY( 157, $y_point);
                $pdf->MultiCell(35, 5, U2T(U2T($coop_loan_guarantee['member_id'])), $border, "C");//ผู้กู้

                $y_point += $y;
                $pdf->SetXY( 88, $y_point);
                $pdf->MultiCell(45, 5, U2T($coop_loan_guarantee['position_name']), $border, "C");//ผู้กู้

                $y_point += $y;
                $pdf->SetXY( 49, $y_point);
                $pdf->MultiCell(35, 5, U2T($coop_loan_guarantee['id_card']), $border, "C");//ผู้กู้
                $pdf->SetXY( 95, $y_point);
                $pdf->MultiCell(35, 5, U2T($coop_loan_guarantee['short_mem_group_name_level']), $border, "C");//ผู้กู้
                $pdf->SetXY( 169, $y_point);
                $pdf->MultiCell(25, 5, U2T($coop_loan_guarantee['c_address_no']), $border, "C");//ผู้กู้

                $y_point += $y;
                $pdf->SetXY( 28, $y_point);
                $pdf->MultiCell(30, 5, U2T($coop_loan_guarantee['c_address_road']), $border, "C");//ผู้กู้
                $pdf->SetXY( 68, $y_point);
                $pdf->MultiCell(30, 5, U2T($coop_loan_guarantee['c_district_name']), $border, "C");//ผู้กู้
                $pdf->SetXY( 110, $y_point);
                $pdf->MultiCell(33  , 5, U2T($coop_loan_guarantee['c_amphur_name']), $border, "C");//ผู้กู้
                $pdf->SetXY( 155, $y_point);
                $pdf->MultiCell(40, 5, U2T($coop_loan_guarantee['c_province_name']), $border, "C");//ผู้กู้

                $y_point += $y;
                $pdf->SetXY( 38, $y_point);
                $pdf->MultiCell(40, 5, U2T($coop_loan_guarantee['mobile']), $border, "C");//ผู้กู้
                $y_point += $y+$y;
                $pdf->SetXY( 77, $y_point);
                $pdf->MultiCell(65, 5, U2T($fullname_th), $border, "C");//ผู้กู้
                $pdf->SetXY( 177, $y_point);
                $pdf->MultiCell(16, 5, U2T($coop_loan_guarantee['member_id']), $border, "C");//ผู้กู้
                $y_point += $y;
                $pdf->SetXY( 41, $y_point);
                $pdf->MultiCell(30, 5, U2T($data['loan_amount']), $border, "C");//ผู้กู้
                $pdf->SetXY( 83, $y_point);
                $pdf->MultiCell(70, 5, U2T($money_loan_amount_2text), $border, "C");//ผู้กู้

                $y_point += $y;
                $pdf->SetXY( 45, $y_point);
                $pdf->MultiCell(12, 5, U2T($contract_number_font), $border, "C");//ผู้กู้
                $pdf->SetXY( 58.5, $y_point);
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->MultiCell(12.5, 5, U2T($contract_number_back), $border, "C");//ผู้กู้
                $pdf->SetFont('THSarabunNew', '', 14);
                $pdf->SetXY( 83, $y_point);
                $pdf->MultiCell(38, 5, U2T($full_date), $border, "C");//ผู้กู้
            }
            else if($pageNo == '2'){
                $y_point = 70;
                $pdf->SetXY( 122.4, $y_point);
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->MultiCell(55, 5, U2T($coop_loan_guarantee['full_name_th']), $border, "C");//ผู้กู้
                $pdf->SetFont('THSarabunNew', '', 14);
                $y_point = 76.3;
                $pdf->SetXY( 28, $y_point);
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->MultiCell(41, 5, U2T($coop_loan_guarantee['full_name_th']), $border, "C");//ผู้กู้
                $pdf->SetFont('THSarabunNew', '', 14);
                $pdf->SetXY( 80, $y_point);
                $pdf->MultiCell(33, 5, U2T($coop_loan_guarantee['position_name']), $border, "C");//ผู้กู้
                $y_point = 82.5;
                $pdf->SetXY( 37.5, $y_point);
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->MultiCell(57, 5, U2T($fullname_th), $border, "C");//ผู้กู้
                $pdf->SetFont('THSarabunNew', '', 14);
                if(!empty($data['coop_loan_guarantee'][0]['mary_name'])) {
                    $y_point = 138.8;
                    $pdf->SetXY( 134.8, $y_point);
                    $pdf->MultiCell(57, 5, U2T(''), $border, "C");
                    $y_point = 146.5;
                    $pdf->SetXY( 132.7, $y_point);
                    $pdf->MultiCell(57, 5, U2T($full_date), $border, "C");
                    $y_point = 154;
                    $pdf->SetXY(38, $y_point);
                    $pdf->MultiCell(60, 5, U2T($coop_loan_guarantee['mary_name']), $border, "C");//ผู้กู้
                    $pdf->SetXY(123, $y_point);
                    $pdf->MultiCell(70, 5, U2T($coop_loan_guarantee['full_name_th']), $border, "C");//ผู้กู้
                    $y_point = 161.2;
                    $pdf->SetXY(45.5, $y_point);
                    $pdf->SetFont('THSarabunNew', '', 11);
                    $pdf->MultiCell(45, 5, U2T($coop_loan_guarantee['full_name_th']), $border, "C");
                    $y_point = 184;
                    $pdf->SetXY(109, $y_point);
                    $pdf->MultiCell(45, 5, U2T($coop_loan_guarantee['mary_name']), $border, "C");
                    $y_point = 199.7;
                    $pdf->SetXY(109, $y_point);
                    $pdf->MultiCell(45, 5, U2T($coop_loan_guarantee['full_name_th']), $border, "C");
                    $pdf->SetFont('THSarabunNew', '', 14);
                }
            }else if($pageNo == '3'){
                $y_point = 79.5;
                $pdf->SetXY( 136.5, $y_point);
                $pdf->MultiCell(57, 5, U2T($data['petition_number']), $border, "C");//ผู้กู้
                $y_point = 87;
                $pdf->SetXY( 45, $y_point);
                $pdf->MultiCell(40, 5, U2T($full_date), $border, "C");//ผู้กู้
                $y_point = 94;
                $pdf->SetXY( 38, $y_point);
                $pdf->MultiCell(88, 5, U2T($fullname_th), $border, "C");//ผู้กู้
                $y_point = 112;
//                $pdf->Image($myImage, 59, $y_point, 3);
                $y_point = 120;
//                $pdf->Image($myImage, 59, $y_point, 3);
                $y_point = 135;
//                $pdf->Image($myImage, 59, $y_point, 3);
                $y_point = 143.2;
//                $pdf->Image($myImage, 59, $y_point, 3);
                $y_point = 149.5;
//                $pdf->Image($myImage, 59, $y_point, 3);
                $y_point = 165;
//                $pdf->Image($myImage, 59, $y_point, 3);
                $y_point = 181;
//                $pdf->Image($myImage, 59, $y_point, 3);
                $y_point = 231.3;
                $pdf->SetXY( 105, $y_point);
                $pdf->MultiCell(54, 5, U2T($fullname_th), $border, "C");//ลายเซ็นผู้ค้ำ
            }else if($pageNo == '4'){

            }
        }
        
    }
}

$pdf->Output();