<?php
function U2T($text)
{
    return @iconv("UTF-8", "TIS-620//IGNORE", ($text));
}

function num_format($text)
{
    if ($text != '') {
        return number_format($text, 2);
    } else {
        return '';
    }
}

// print_r($user);
$filename = $_SERVER["DOCUMENT_ROOT"] . "/assets/document/loan/vehicle-loan.pdf";
//$filename = $_SERVER["DOCUMENT_ROOT"]."/fsccoop/assets/document/loan/vehicle-loan.pdf" ;
//echo $filename;exit;

$pdf = new FPDI();
// echo $filename;exit;
$pageCount_1 = $pdf->setSourceFile($filename);
$myImage = "assets/images/check-mark.png";
//$data = $loan_fscoop;
$location = $profile_location; // location ของสถานที่ ex เขียนที่.....

$age = $this->center_function->diff_birthday($data['birthday']); //อายุ
$monthtext = $this->center_function->month_arr(); // function แปลงเดือนเป็นตัวอักษร
$money_loan_amount_2text = $this->center_function->convert($data['loan_amount']); //จำนวนเงินกู็(ตัวอักษร)
$money_salary_2text = $this->center_function->convert($data['salary']);//เงินเดือน(ตัวอักษร)
$start_member_year = $this->center_function->diff_year($data['approve_date'], date('Y-m-d H:i:s')); // ปีที่เริ่มทำงาน (จำนวนปี)
$start_member_month = $this->center_function->diff_month_interval($data['approve_date'], date('Y-m-d H:i:s')); // จำนวนเดือน
if ($data['approve_date'] != '') {
    $date_to_year = (substr($data['approve_date'], 0, 4)) + 543; // ปีที่เริ่มทำสัญญา
}
$date_to_text = number_format(substr($data['approve_date'], 8, 2)); // วันที่เริ่มทำสัญญา
$date_to_month = number_format(substr($data['approve_date'], 5, 2)); // เดือนที่เริ้มทำสัญญา
$month2text = $monthtext[$date_to_month]; // เดือนที่เริ่มทำสัญญา (ตัวอักษร)
$full_date = $date_to_text . "  " . $month2text . "  " . $date_to_year; // วัน:เดือน:ปี ที่เริ่มทำสัญญา
if ($data['createdatetime'] != '') {
    $create_year = (substr($data['createdatetime'], 0, 4)) + 543; // // ปีที่บันทึกข้อมูล
}
$create_day = number_format(substr($data['createdatetime'], 8, 2)); // วันที่บันทึกข้อมูล
$create_month = number_format(substr($data['createdatetime'], 5, 2)); // เดือนที่บันทึกข้อมูล
$create_month2text = $monthtext[$create_month]; // เดือนที่บันทึกข้อมูล(ตัวอักษร)
if ($data['createdatetime'] != '') {
    $create_year = (substr($data['createdatetime'], 0, 4)) + 543; // // ปีที่บันทึกข้อมูล
}
$day_start_period = number_format(substr($data['date_start_period'], 8, 2));// วันเริ่มจ่ายงวด(หุ้น)
$month_start_period = number_format(substr($data['date_start_period'], 5, 2)); // เดือนที่จ่ายค่างวด(หุ้น)
$year_start_period = (substr($data['approve_date'], 0, 4)) + 543; // ปีที่จ่ายค่างวด(หุ้น)
$full_start_period = $day_start_period . "  " . $month_start_period . "  " . $year_start_period; // วัน, เดือน, ปี ที่จ่ายค่างวด(หุ้น)
$fullname_th = $data['prename_full'] . $data['firstname_th'] . "  " . $data['lastname_th']; // คำนำหน้าชื่อ , ชื่อ-สกุล (ผู้กู้)
$contract_number_font = substr($data['contract_number'], 0, -8); // ตัวอักษรหน้า เลขที่สัญญา ex. ฉฉ999999 = ฉฉ
$contract_number_back = substr($data['contract_number'], -9);   //ตัวอักษรหลัง เลขที่สัญญา ex. 999999 = ฉฉ
$period_amount = substr($this->center_function->convert($data['period_amount']), 0, -3 * 7); //งวด(ตัวอักษร)
for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {
    $pdf->AddPage();
    $tplIdx = $pdf->importPage($pageNo);
    $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
    $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
    $pdf->SetFont('THSarabunNew', '', 14);
    $border = isset($_GET['show']) && $_GET['show'] == '1' ? 1 : 0;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetAutoPageBreak(true, 0);
    if ($pageNo == '1') {
        $y_point = 86.2;
        $pdf->Image($myImage, 29, $y_point, 3);
        $y_point = 94;
        $pdf->Image($myImage, 29, $y_point, 3);
        $y_point = 101.4;
        $pdf->Image($myImage, 29, $y_point, 3);
        if(!empty($data['marry_name'])) {
        $y_point = 109;
        $pdf->Image($myImage, 29, $y_point, 3);
        }
        $y_point = 116.0;
        $pdf->Image($myImage, 29, $y_point, 3);
        $y_point = 124.2;
        $pdf->Image($myImage, 29, $y_point, 3);
        $y_point = 132.2;
        //$pdf->Image($myImage, 45, $y_point, 3);
        $y_point = 139.2;
        //$pdf->Image($myImage, 45, $y_point, 3);
        $y_point = 147;
        //$pdf->Image($myImage, 45, $y_point, 3);
        $y_point = 166.3;
        $pdf->SetXY(123, $y_point);
        $pdf->MultiCell(58, 5, U2T($fullname_th), $border, "C"); //เขียนที่

    } else if ($pageNo == '2') {

    } else if ($pageNo == '3') {
        $y_point = 23;
        $pdf->SetXY(34.5, $y_point);
        $pdf->MultiCell(18, 5, U2T(''), $border, "C"); //เลขที่รับ
        $y_point = 30.5;
        $pdf->SetXY(29.5, $y_point);//วันที่่รับ
        $pdf->MultiCell(27, 5, U2T($date_to_text . " /" . $month_short_arr[$date_to_month] . " /" . $date_to_year), $border, "C"); //เขียนที่
        $y_point = 24.5;
        $pdf->SetXY(178.5, $y_point);
        $pdf->MultiCell(18, 5, U2T(''), $border, "C"); //หนังสือกู้พิเศษที่
        $y_point = 32.5;
        $pdf->SetXY(161.5, $y_point);
        $pdf->MultiCell(35, 5, U2T($full_date), $border, "C"); //วันที่
        $y_point = 65.5;
        $pdf->SetXY(135, $y_point);
        $pdf->MultiCell(62.5, 5, U2T($location['profile_location']['0']['coop_name_th']), $border, "C"); //เขียนที่
        $y_point = 72.8;
        $pdf->SetXY(132, $y_point);
        $pdf->MultiCell(13, 5, U2T($create_day), $border, "C"); //วันที่
        $pdf->SetXY(153.5, $y_point);
        $pdf->MultiCell(23, 5, U2T($create_month2text), $border, "C"); //เดือนที่
        $pdf->SetXY(184.2, $y_point);
        $pdf->MultiCell(13, 5, U2T($create_year), $border, "C"); //ปี
        $y_point = 91.5;
        $pdf->SetXY(49, $y_point);
        $pdf->MultiCell(70, 5, U2T($fullname_th), $border, "C"); //ชื่อ
        $pdf->SetXY(150, $y_point);
        $pdf->MultiCell(20, 5, U2T($data['member_id']), $border, "C"); //สมาชิกทะเบียนที่
        $pdf->SetXY(176.5, $y_point);
        $pdf->MultiCell(17.5, 5, U2T($age), $border, "C"); //อายุ
        $y_point = 98.7;
        $pdf->SetXY(78.5, $y_point);
        $pdf->MultiCell(58, 5, U2T($data['position_name']), $border, "C"); //ตำแหน่ง
        $pdf->SetXY(146.2, $y_point);
        $pdf->MultiCell(50, 5, U2T($data['mem_group_name']), $border, "C"); //สังกัด
        $y_point = 106;
        $pdf->SetXY(57.5, $y_point);
        $pdf->MultiCell(25, 5, U2T($data['address_no']), $border, "C"); //บ้านเลขที่
        $pdf->SetXY(90.5, $y_point);
        $pdf->MultiCell(40, 5, U2T(($data['address_road'] == '') ? '-' : $data['address_road']), $border, "C"); //ถนน
        $pdf->SetXY(140.5, $y_point);
        $pdf->MultiCell(55, 5, U2T(($data['address_soi'] == '') ? '-' : $data['address_soi']), $border, "C"); //ซอย
        $y_point = 113;
        $pdf->SetXY(33.5, $y_point);
        $pdf->MultiCell(47, 5, U2T(($data['district_name'] == '') ? '-' : $data['district_name']), $border, "C"); //ตำบล
        $pdf->SetXY(93.5, $y_point);
        $pdf->MultiCell(40, 5, U2T($data['amphur_name']), $border, "C"); //ปากช่อง
        $pdf->SetXY(145.5, $y_point);
        $pdf->MultiCell(52, 5, U2T($data['province_name']), $border, "C"); //นครราชสีมา
        $y_point = 120.2;
        $pdf->SetXY(59, $y_point);
        $pdf->MultiCell(29, 5, U2T(($data['office_tel'] == '') ? '-' : $data['office_tel']), $border, "C"); //โทรศัพท์ติดต่อที่ทำงาน
        $pdf->SetXY(107.5, $y_point);
        $pdf->MultiCell(32, 5, U2T(($data['tel'] == '') ? '-' : $data['tel']), $border, "C"); //โทรศัพท์บ้าน
        $pdf->SetXY(159, $y_point);
        $pdf->MultiCell(37, 5, U2T(($data['mobile'] == '') ? '-' : $data['mobile']), $border, "C"); //โทรศัพท์มือถือ
        $y_point = 134.4;
        $pdf->SetXY(92, $y_point);
        $pdf->MultiCell(29, 5, U2T($data['loan_amount']), $border, "C"); //จำนวนเงินกู้
        $pdf->SetXY(130.2, $y_point);
        $pdf->MultiCell(62, 5, U2T($money_loan_amount_2text), $border, "C"); //จำนวนเงินกู้
        $y_point = 141.6;
        $pdf->SetXY(97, $y_point);
        $pdf->MultiCell(25, 5, U2T($data['money_per_period']), $border, "C"); //เงินต้นและดอกเบี้ย
        $pdf->SetXY(135.2, $y_point);
        $pdf->MultiCell(23, 5, U2T($data['period_amount']), $border, "C"); //งวด
        $y_point = 148.8;
        //$pdf->Image($myImage, 45, 151, 3);
        $pdf->SetXY(95, $y_point);
        //$pdf->MultiCell(25, 5, U2T('FORD**'), $border, "C"); //ยี่ห้อรถ
        $pdf->SetXY(127.5, $y_point);
        //$pdf->MultiCell(29, 5, U2T('MUSTANG**'), $border, "C"); //รุ่นรถ
        $pdf->SetXY(164.9, $y_point);
        //$pdf->MultiCell(19.8, 5, U2T('1,500,000**'), $border, "C"); //ราคา
        $y_point = 156;
        //$pdf->Image($myImage, 45, 157.3, 3);
        $pdf->SetXY(95, $y_point);
        //$pdf->MultiCell(25, 5, U2T('FORD**'), $border, "C"); //ยี่ห้อรถ
        $pdf->SetXY(127.5, $y_point);
        //$pdf->MultiCell(29, 5, U2T('MUSTANG**'), $border, "C"); //รุ่นรถ
        $pdf->SetXY(164.9, $y_point);
        //$pdf->MultiCell(19.8, 5, U2T('1,500,000**'), $border, "C"); //ราคา
        $y_point = 185.0;
        $pdf->SetXY(35, $y_point);
        //$pdf->MultiCell(80, 5, U2T('American International Assurance*'), $border, "C"); //ประกันบริษัท
        $y_point = 235;
        $pdf->SetXY(97, $y_point);
        $pdf->MultiCell(62, 5, U2T($fullname_th), $border, "C"); //ชื่อ(ลายเซ็น)

    } else if ($pageNo == '4') {
        $y_point = 54.3;
        $pdf->SetXY(111.5, $y_point);
        $pdf->SetFont('THSarabunNew', '', 13.5);
        $pdf->MultiCell(60, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
        $y_point = 61.5;
        $pdf->SetXY(114, $y_point);
        $pdf->MultiCell(57, 5, U2T($data['position_name']), $border, "C"); //วันที่
        $y_point = 68.5;
        $pdf->SetXY(127.5, $y_point);
        $pdf->MultiCell(34, 5, U2T($full_date), $border, "C"); //วันที่
        $y_point = 90.8;
        $pdf->SetXY(73, $y_point);
        //$pdf->MultiCell(44.5, 5, U2T($full_date), $border, "C"); //เป็นผู้กู้ตั้งแต่วันที่
        $pdf->SetXY(138, $y_point);
        $pdf->MultiCell(23, 5, U2T($diff_year), $border, "C"); //รวมระยะ(ปี)
        $pdf->SetXY(165, $y_point);
        $pdf->MultiCell(20, 5, U2T(''), $border, "C"); //รวมระยะ(เดือน)
        $y_point = 98;
        $pdf->SetXY(87.5, $y_point);
        $pdf->MultiCell(44.5, 5, U2T(''), $border, "C"); //หุ้นในสหกรณ์
        $pdf->SetXY(151.5, $y_point);
        $pdf->MultiCell(30, 5, U2T(''), $border, "C"); //ส่งหุ้นละ(งวด)
        $y_point = 105.1;
        $pdf->SetXY(54.5, $y_point);
        $pdf->MultiCell(35.5, 5, U2T(''), $border, "C"); //เงินฝากประเภท
        $pdf->SetXY(105, $y_point);
        $pdf->MultiCell(40, 5, U2T(''), $border, "C"); //เลขที่บัญชี
        $pdf->SetXY(157, $y_point);
        $pdf->MultiCell(33, 5, U2T(''), $border, "C"); //จำนวนเงินฝาก
        $y_point = 119.5;
        $pdf->SetXY(62.5, $y_point);
        $pdf->MultiCell(43, 5, U2T(''), $border, "C"); //หนังสือกู้ฉุกเฉินที่
        $pdf->SetXY(113, $y_point);
        $pdf->MultiCell(32, 5, U2T(''), $border, "C"); //วันที่
        $pdf->SetXY(162.5, $y_point);
        //$pdf->MultiCell(26, 5, U2T($data['period_amount']), $border, "C"); //ระยะเวลากู้
        $y_point = 126.6;
        $pdf->SetXY(40.8, $y_point);
        //$pdf->MultiCell(27, 5, U2T($data['money_per_period']), $border, "C"); //จำนวนเงิน
        $pdf->SetXY(87, $y_point);
        $pdf->MultiCell(23.4, 5, U2T(''), $border, "C"); //คงเหลือ
        $pdf->SetXY(144, $y_point);
        $pdf->MultiCell(22.7, 5, U2T(''), $border, "C"); //จำนวนผ่อนชำระ
        $y_point = 133.6;
        $pdf->SetXY(62, $y_point);
        $pdf->MultiCell(42, 5, U2T(''), $border, "C"); //หนังสือเงินกู้สามัญที่
        $pdf->SetXY(113, $y_point);
        $pdf->MultiCell(32, 5, U2T(''), $border, "C"); //วันที่
        $pdf->SetXY(161, $y_point);
        $pdf->MultiCell(27, 5, U2T(''), $border, "C"); //ระยะเวลากู้
        $y_point = 140.6;
        $pdf->SetXY(40, $y_point);
        $pdf->MultiCell(27, 5, U2T(''), $border, "C"); //จำนวนเงิน
        $pdf->SetXY(86, $y_point);
        $pdf->MultiCell(26, 5, U2T(''), $border, "C"); //คงเหลือ
        $pdf->SetXY(143, $y_point);
        $pdf->MultiCell(24, 5, U2T(''), $border, "C"); //จำนวนผ่อนชำระ บาท/งวด
        $y_point = 147.8;
        $pdf->SetXY(79, $y_point);
        $pdf->MultiCell(27, 5, U2T(''), $border, "C"); //หนังสือเพื่อการเคหะที่
        $pdf->SetXY(113, $y_point);
        $pdf->MultiCell(31, 5, U2T(''), $border, "C"); //คงเหลือ
        $pdf->SetXY(162.5, $y_point);
        $pdf->MultiCell(26, 5, U2T(''), $border, "C"); //จำนวนผ่อนชำระ/งวด
        $y_point = 154.8;
        $pdf->SetXY(40, $y_point);
        $pdf->MultiCell(27, 5, U2T(''), $border, "C"); //จำนวนเงินกู้
        $pdf->SetXY(86.5, $y_point);
        $pdf->MultiCell(24, 5, U2T(''), $border, "C"); //คงเหลือ
        $pdf->SetXY(144, $y_point);
        $pdf->MultiCell(24, 5, U2T(''), $border, "C"); //จำนวนผ่อนชำระ/งวด
        $y_point = 162;
        $pdf->SetXY(84, $y_point);
        $pdf->MultiCell(27, 5, U2T(''), $border, "C"); //เคยผิดนัดการส่งงวดชำระที่
        $pdf->SetXY(142.2, $y_point);
        $pdf->MultiCell(24, 5, U2T(''), $border, "C"); //จำนวนหุ้นที่ขาดรำระ
        $y_point = 169.2;
        $pdf->SetXY(58, $y_point);
        $pdf->MultiCell(135, 5, U2T(''), $border, "C"); //ข้อชี้แจงอย่างอื่น

    } else if ($pageNo == '5') {
        $y_point = 65.;
        $pdf->SetXY(130.2, $y_point);
        $pdf->MultiCell(11, 5, U2T($create_day), $border, "C"); //วันที่
        $pdf->SetXY(148.9, $y_point);
        $pdf->MultiCell(22.9, 5, U2T($create_month2text), $border, "C"); //เดือนที่
        $pdf->SetXY(178.5, $y_point);
        $pdf->MultiCell(17, 5, U2T($create_year), $border, "C"); //ปี
        $y_point = 77;
        $pdf->SetXY(49, $y_point);
        $pdf->MultiCell(53, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
        $pdf->SetXY(140, $y_point);
        $pdf->MultiCell(35, 5, U2T($data['id_card']), $border, "C"); //สมาชิกเลขทะเบียนที่
        $pdf->SetXY(181, $y_point);
        $pdf->MultiCell(12, 5, U2T($age), $border, "C"); //อายุ
        $y_point = 84.8;
        $pdf->SetXY(56.2, $y_point);
        $pdf->MultiCell(19, 5, U2T($data['address_no']), $border, "C"); //บ้านเลขที่
        $pdf->SetXY(85.5, $y_point);
        $pdf->MultiCell(16, 5, U2T(($data['address_moo'] == '') ? '-' : $data['address_moo']), $border, "C"); //หมู่
        $pdf->SetXY(119, $y_point);
        $pdf->MultiCell(30, 5, U2T(($data['address_soi'] == '') ? '-' : $data['address_soi']), $border, "C"); //ซอย
        $pdf->SetXY(158.5, $y_point);
        $pdf->MultiCell(37, 5, U2T(($data['address_road'] == '') ? '-' : $data['address_road']), $border, "C"); //ถนน
        $y_point = 92.6;
        $pdf->SetXY(44, $y_point);
        $pdf->MultiCell(30, 5, U2T($data['district_name']), $border, "C"); //ตำบล
        $pdf->SetXY(95, $y_point);
        $pdf->MultiCell(46, 5, U2T($data['amphur_name']), $border, "C"); //อำเภอ
        $pdf->SetXY(153, $y_point);
        $pdf->MultiCell(42.5, 5, U2T($data['province_name']), $border, "C"); //จังหวัด
        $y_point = 100;
        $pdf->SetXY(52, $y_point);
        $pdf->MultiCell(38.5, 5, U2T($data['tel']), $border, "C"); //เบอร์โทรศัพท์บ้าน
        $pdf->SetXY(103.5, $y_point);
        $pdf->MultiCell(35.5, 5, U2T($data['mobile']), $border, "C"); //เบอร์มือถือ
        $pdf->SetXY(152, $y_point);
        $pdf->MultiCell(43, 5, U2T($data['position_name']), $border, "C"); //ตำแหน่ง
        $y_point = 107.5;
        $pdf->SetXY(35, $y_point);
        $pdf->MultiCell(36, 5, U2T($data['mem_group_name']), $border, "C"); //สังกัต
        $pdf->SetXY(109, $y_point);
        $pdf->MultiCell(16.5, 5, U2T($data['salary']), $border, "C"); //เงินเดือน
        $pdf->SetXY(136, $y_point);
        $pdf->MultiCell(57, 5, U2T($money_salary_2text), $border, "C"); //เงินเดือน(ตัวอักษร)
        $y_point = 115.3;
        $pdf->SetXY(165.5, $y_point);
        $pdf->MultiCell(31, 5, U2T($data['member_id']), $border, "C"); //สัญญากู้เลขที่
        $y_point = 138.8;
        $pdf->SetXY(82.5, $y_point);
        $pdf->MultiCell(31, 5, U2T($data['contract_number']), $border, "C"); //สัญญากู้เลขที่
        $pdf->SetXY(140.5, $y_point);
        $pdf->MultiCell(31, 5, U2T($data['petition_number']), $border, "C"); //คำขอกู้เลขที่
        $y_point = 253.8;
        $pdf->SetXY(110, $y_point);
        $pdf->MultiCell(60, 5, U2T($fullname_th), $border, "C"); //ชื่อ-นามสกุล
    } else if ($pageNo == '6') {

    } else if ($pageNo == '7') {
        if(!empty($data['marry_name'])) {
            $y_point = 80.5;
            $pdf->SetXY(130.2, $y_point);
            $pdf->MultiCell(11, 5, U2T($create_day), $border, "C"); //วันที่
            $pdf->SetXY(148.9, $y_point);
            $pdf->MultiCell(22.9, 5, U2T($create_month2text), $border, "C"); //เดือนที่
            $pdf->SetXY(178.5, $y_point);
            $pdf->MultiCell(17, 5, U2T($create_year), $border, "C"); //ปี
            $y_point = 95.8;
            $pdf->SetXY(49, $y_point);
            $pdf->MultiCell(106, 5, U2T($data['marry_name']), $border, "C"); //ชื่อผู้กู้
            $pdf->SetXY(162.5, $y_point);
            $pdf->MultiCell(31, 5, U2T(''), $border, "C"); //อายุคู่สมรส
            $y_point = 103.6;
            $pdf->SetXY(45.2, $y_point);
            $pdf->MultiCell(22.2, 5, U2T($data['m_address_no']), $border, "C"); //ชื่อผู้กู้
            $pdf->SetXY(76.5, $y_point);
            $pdf->MultiCell(15.7, 5, U2T(($data['m_address_moo'] == '') ? '-' : $data['address_moo']), $border, "C"); //ชื่อผู้กู้
            $pdf->SetXY(109.7, $y_point);
            $pdf->MultiCell(36, 5, U2T(($data['m_address_soi'] == '') ? '-' : $data['address_soi']), $border, "C"); //ชื่อผู้กู้
            $pdf->SetXY(155.5, $y_point);
            $pdf->MultiCell(40, 5, U2T(($data['m_address_road'] == '') ? '-' : $data['address_road']), $border, "C"); //ชื่อผู้กู้
            $y_point = 111;
            $pdf->SetXY(45.2, $y_point);
            $pdf->MultiCell(47, 5, U2T($data['district_name']), $border, "C"); //ชื่อผู้กู้
            $pdf->SetXY(110, $y_point);
            $pdf->MultiCell(35, 5, U2T($data['amphur_name']), $border, "C"); //ชื่อผู้กู้
            $pdf->SetXY(158, $y_point);
            $pdf->MultiCell(35, 5, U2T($data['province_name']), $border, "C"); //ชื่อผู้กู้
            $y_point = 118.5;
            $pdf->SetXY(59.5, $y_point);
            $pdf->MultiCell(102, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
            $y_point = 164.5;
            $pdf->SetXY(90.5, $y_point);
            $pdf->MultiCell(55, 5, U2T($data['marry_name']), $border, "C"); //ชื่อผู้กู้
            $y_point = 219;
            $pdf->SetXY(50.5, $y_point);
            $pdf->MultiCell(118, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
            $y_point = 226.8;
            $pdf->SetXY(53.5, $y_point);
            $pdf->MultiCell(95, 5, U2T($data['marry_name']), $border, "C"); //ชื่อคู่สมรส
            $y_point = 246.4;
            $pdf->SetXY(51, $y_point);
            $pdf->MultiCell(95, 5, U2T($data['marry_name']), $border, "C"); //ชื่อคู่สมรส
        }
    }
}

//exit;
$pdf->Output();