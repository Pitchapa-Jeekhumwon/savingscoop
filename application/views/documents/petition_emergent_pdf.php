<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

 $filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/loan/collateral-loan.pdf" ;
//$filename = $_SERVER["DOCUMENT_ROOT"]."/fsccoop/assets/document/loan/collateral-loan.pdf" ;
$pdf = new FPDI();
$pageCount_1 = $pdf->setSourceFile($filename);
$myImage = "assets/images/check-mark.png";
$location  = $profile_location;

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
    $create_day = number_format(substr($data['createdatetime'], 8, 2)); // วันที่บันทึกข้อมูล
    $create_month = number_format(substr($data['createdatetime'], 5, 2)); // เดือนที่บันทึกข้อมูล
}
$create_month2text = $monthtext[$create_month]; // เดือนที่บันทึกข้อมูล(ตัวอักษร)
if ($data['createdatetime'] != ''){
    $create_year       = (substr($data['createdatetime'], 0, 4))+543; // // ปีที่บันทึกข้อมูล
}
$day_start_period   = number_format(substr($data['date_start_period'], 8, 2));// วันเริ่มจ่ายงวด(หุ้น)
$month_start_period = number_format(substr($data['date_start_period'], 5, 2)); // เดือนที่จ่ายค่างวด(หุ้น)
$fullname_th        = $data['prename_full'].$data['firstname_th']."  ".$data['lastname_th']; // คำนำหน้าชื่อ , ชื่อ-สกุล (ผู้กู้)
$contract_number_font = substr($data['contract_number'], 0, -8); // ตัวอักษรหน้า เลขที่สัญญา ex. ฉฉ999999 = ฉฉ
$contract_number_back = substr($data['contract_number'], -9);   //ตัวอักษรหลัง เลขที่สัญญา ex. 999999 = ฉฉ
$period_amount = substr($this->center_function->convert($data['period_amount']),0,-3*7); //งวด(ตัวอักษร)
for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {
    $pdf->AddPage();
    $tplIdx = $pdf->importPage($pageNo);
    $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
    $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
    $pdf->SetFont('THSarabunNew', '', 12 );
            
    // $pdf->SetTitle(U2T('คำขอกู้เงินเพื่อการศึกษา'));
    $border = isset($_GET['show']) && $_GET['show'] == '1' ?  1 : 0;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetAutoPageBreak(true,0);
    if($pageNo == '1'){
        $pdf->SetFont('THSarabunNew', '', 11 );
        $y_point = 24.3;
        $pdf->SetXY( 115, $y_point );
        $pdf->MultiCell(48, 5, U2T($location['profile_location']['coop_name_th']), $border, "C"); // เขียนที่
        $pdf->SetFont('THSarabunNew', '', 12 );
        $y_point = 23;
        $pdf->SetXY( 181.5, $y_point );
        $pdf->MultiCell(6, 5, U2T($contract_number_font), $border, "C"); // ฉฉ
        $pdf->SetXY( 187.2, $y_point );
        $pdf->MultiCell(15, 5, U2T($contract_number_back), $border, "C"); // หนังสือกู้ที่
        $y_point = 30;
        $pdf->SetXY( 112, $y_point );
        $pdf->MultiCell(8, 5, U2T($date_to_text), $border, "C"); // วันที่(วัน)
        $pdf->SetXY( 125.8, $y_point );
        $pdf->MultiCell(17, 5, U2T($month_short_arr[$date_to_month]), $border, "C"); // วันที่ (เดือน)
        $pdf->SetXY( 149, $y_point );
        $pdf->MultiCell(12, 5, U2T($date_to_year), $border, "C"); // วันที่ (ปี)
        $y_point = 29.3;
        $pdf->SetXY( 169.5, $y_point );
        $pdf->MultiCell(9, 5, U2T($date_to_text), $border, "C"); // วันที่(วัน)
        $pdf->SetXY( 179.5, $y_point );
        $pdf->MultiCell(9, 5, U2T($month_short_arr[$date_to_month]), $border, "C"); // วันที่ (เดือน)
        $pdf->SetXY( 189.5, $y_point );
        $pdf->MultiCell(9, 5, U2T($date_to_year), $border, "C"); /// วันที่ (ปี)
        $y_point = 50;
        $pdf->SetXY( 45, $y_point );
        $pdf->MultiCell(77.5, 5, U2T($fullname_th), $border, "C"); // ชื่อผู้กู้
        $pdf->SetXY( 146.5, $y_point );
        $pdf->MultiCell(50, 5, U2T($data['member_id']), $border, "C"); // เลขทะเบียนสมาชิกที่...
        $y_point = 56;
        $pdf->SetXY( 56, $y_point );
        $pdf->MultiCell(42, 5, U2T($data['position_name']), $border, "C"); // ตำแหน่งในงานประจำ
        $pdf->SetFont('THSarabunNew', '', 11 );
        $pdf->SetXY( 105, $y_point );
        $pdf->MultiCell(22, 5, U2T($data['short_mem_group_name_level']), $border, "C"); // สังกัด
        $pdf->SetFont('THSarabunNew', '', 12 );
        $pdf->SetXY( 140, $y_point );
        $pdf->MultiCell(25, 5, U2T(($data['office_tel']=='')?'-':$data['office_tel']), $border, "C"); // เบอร์โทรศัพท์ภายใน
        $pdf->SetXY( 172.5, $y_point );
        $pdf->MultiCell(25.5, 5, U2T(($data['mobile']=='')?'-':$data['mobile']), $border, "C"); // เบอร์โทรศัพท์มือถือ
        $y_point = 61.7;
        $pdf->SetXY( 33.5, $y_point );
        $pdf->MultiCell(25.5, 5, U2T(number_format($data['salary'])), $border, "C"); // เงินเดือนที่ได้รับ
        $pdf->SetXY( 98.5, $y_point );
        $pdf->MultiCell(24, 5, U2T(number_format($data['salary'] - $loan_cost_code['total_cost'])), $border, "C"); // เงินเดือนที่คงเหลือ
        $pdf->SetXY( 134.5, $y_point );
        $pdf->MultiCell(18, 5, U2T($month_short_arr[$create_month]), $border, "C"); // เดือน..... ขำคำขอเงินกู้เพื่อเหตุฉุกเฉิน...
        $pdf->SetXY( 158.5, $y_point );
        $pdf->MultiCell(18, 5, U2T($date_to_year), $border, "C"); // ปี..... ขำคำขอเงินกู้เพื่อเหตุฉุกเฉิน...
        $y_point = 73.6;
        $pdf->SetXY( 134.5, $y_point );
        $pdf->MultiCell(24, 5, U2T(number_format($data['loan_amount'])), $border, "C"); //จำนวนเงินกู้
        $pdf->SetXY( 166, $y_point );
        $pdf->SetFont('THSarabunNew', '', 8 );
        $pdf->MultiCell(35, 5, U2T($money_loan_amount_2text), $border, "C"); // จำนวนเงินกู้(ตัวอักษร)
        $pdf->SetFont('THSarabunNew', '', 12 );
        $y_point = 79.5;
        $pdf->SetXY( 23.7, $y_point );
        $pdf->MultiCell(82, 5, U2T($data['loan_reason']), $border, "C"); // ขอกู้เงินสหกรณ์เพื่อนำไปใช้
        if(false){ /*สิทธิการกู้*/
            $y_point = 92;
            $pdf->Image($myImage, 55, $y_point, 3);
        }else{
            $y_point = 98;
            $pdf->Image($myImage, 55, $y_point, 3);
        }

        if(empty($contract['cal_loan']['1']['loan_amount_balance'])){ /*สภาพหนี้*/
            $y_point = 115;
            $pdf->Image($myImage, 55, $y_point, 3);
        }else{
            $y_point = 121;
            $pdf->Image($myImage, 55, $y_point, 3);
            $y_point = 120.2;
            $pdf->SetXY(83.5, $y_point, 3);
            $pdf->MultiCell(27.2, 5, U2T( number_format($contract['cal_loan']['1']['loan_amount_balance']) ), $border, "C"); // หนี้ฉุกเฉินคงเหลือ
            $pdf->SetXY(137, $y_point, 3);
            $pdf->MultiCell(12, 5, U2T( number_format($contract['cal_loan']['1']['num_transaction']) ), $border, "C"); // ผ่อนชำระมาแล้ว (งวด)
            $pdf->SetXY(170, $y_point, 3);
            $pdf->MultiCell(26, 5, U2T( number_format($contract['cal_loan']['1']['principal_payment']) ), $border, "C"); // เงินต้นงวดละ (บาท)
        }
        $y_point = 125.8;
        $pdf->SetXY(123, $y_point, 3);
        $pdf->MultiCell(12, 5, U2T($data['period_amount']), $border, "C"); // ชำระคืนเงินต้นและดอกเบี้ยแก่สหกรณ์ รวม(งวด)
        $pdf->SetXY(179.5, $y_point, 3);
        $pdf->MultiCell(21.5, 5, U2T($month_arr[(int)substr($data['date_period_1'],5,2)]), $border, "C"); // เริ่มชำรำงวดแรก(เดือน)
        $y_point = 131.5;
        $pdf->SetXY(15, $y_point, 3);
        $pdf->MultiCell(15, 5, U2T(substr($data['date_period_1'], 0, 4) + 543), $border, "C"); // เริ่มชำรำงวดแรก(ปี)
        $pdf->SetXY(40, $y_point, 3);
        $pdf->MultiCell(26.2, 5, U2T($month_arr[(int)substr($data['date_period_2'],5,2)]), $border, "C"); // ชำระถึง  (เดือน)
        $pdf->SetXY(71, $y_point, 3);
        $pdf->MultiCell(12, 5, U2T(substr($data['date_period_2'],0,4)+543), $border, "C"); // ชำระถึง (ปี)
        $pdf->SetXY(99, $y_point, 3);
        $pdf->MultiCell(22, 5, U2T(number_format($data['first_principal_payment'])), $border, "C"); // เงินต้น
        $pdf->SetXY(186.5, $y_point, 3);
        $pdf->MultiCell(16, 5, U2T($month_arr[(int)substr($data['last_date_period'],5,2)]), $border, "C"); // และงวดสุดท้ายใน (เดือน)
        $y_point = 137.3;
        $pdf->SetXY(15, $y_point, 3);
        $pdf->MultiCell(15, 5, U2T(substr($data['last_date_period'],0,4)+543), $border, "C"); // และงวดสุดท้าย (ปี)
        $pdf->SetXY(47, $y_point, 3);
        $pdf->MultiCell(19.5, 5, U2T(number_format($data['last_principal_payment'])), $border, "C"); // เงินต้นงวดละinterest_per_year
        $y_point = 154.3;
        $pdf->SetXY(150, $y_point, 3);
        $pdf->MultiCell(15, 5, U2T($data['interest_per_year']), $border, "C"); // อัตราดอกเบี้ย
        $y_point = 251.2;
        $pdf->SetXY(77, $y_point, 3);
        $pdf->MultiCell(53, 5, U2T($fullname_th), $border, "C"); // ชื่อ (ลายเซ็น)

    }else if($pageNo == '2'){
        $y_point = 24.5;
        $pdf->SetXY(95.5, $y_point, 3);
        $pdf->MultiCell(29.5, 5, U2T(number_format($data['loan_amount'])), $border, "C"); // จำนวนเงินกู้
        $y_point = 63.5;
        $pdf->SetXY(10, $y_point, 3);
        $pdf->MultiCell(35, 5, U2T(number_format($data['salary'])), $border, "C"); // เงินได้รายเดือน
        $total_emergency = 0;
        $total_normal = 0;
        foreach ($contract['data'] as $item) {
            if($item['loan_type_id'] == '1'){
                $total_emergency += $item['total_paid_per_month'];
            }else if($item['loan_type_id'] == '2'){
                $total_normal += $item['total_paid_per_month'];
            }
        }
        $pdf->SetXY(48.5, $y_point, 3);
        $pdf->MultiCell(35, 5, U2T(''), $border, "C"); // ต้นเงินกู้สามัญคงเหลือ
        $pdf->SetXY(87.5, $y_point, 3);
        $pdf->MultiCell(36, 5, U2T(''), $border, "C"); // ต้นเงินกู้เพื่อเหตุฉุกเฉิน
        $pdf->SetXY(127.5, $y_point, 3);
        $pdf->MultiCell(32.5, 5, U2T(''), $border, "C"); // จำกัดวงเงินกู้
        $pdf->SetXY(163.5, $y_point, 3);
//        $pdf->MultiCell(35.5, 5, U2T(number_format(number_format(($data['salary'] - $total_normal - $total_emergency), 2))), $border, "C"); // จำกัดวงเงินกู้คงเหลือ
        $y_point = 85;
//        $pdf->Image($myImage, 75, $y_point, 3); //เคยผิดนัดการส่งเงินงวดชำระหนี้ (เคย)
        $y_point = 84.5;
        $pdf->Image($myImage, 118.5, $y_point, 3); //เคยผิดนัดการส่งเงินงวดชำระหนี้ (ไม่เคย)
        $y_point = 91;
        $pdf->SetXY(57.5, $y_point, 3);
        $pdf->MultiCell(107, 5, U2T('ข้อชี้แจงอื่นๆ'), $border, "C"); // ข้อชี้แจงอื่นๆ
        $y_point = 158.5;
        $pdf->SetXY(97.5, $y_point, 3);
        $pdf->MultiCell(41, 5, U2T($data['prename_short'].$data['firstname_th']."  ".$data['lastname_th']), $border, "C"); // ชื่อผู้กู้
        $y_point = 200.5;
        $pdf->SetXY(37, $y_point, 3);
        $pdf->MultiCell(30, 5, U2T('ประชาชน'), $border, "C"); // บัตรประจำตัว
        $y_point = 207.5;
        $pdf->SetXY(26, $y_point, 3);
        $pdf->MultiCell(40, 5, U2T($data['id_card']), $border, "C"); //บัตรประจำตัวเลขที่
        $y_point = 220;
        $pdf->SetXY(48, $y_point, 3);
        $pdf->MultiCell(72, 5, U2T($fullname_th), $border, "C"); // ชื่อผู้กู้
        $pdf->SetXY(149, $y_point, 3);
        $pdf->MultiCell(41.5, 5, U2T($data['loan_amount']), $border, "C"); // จำนวนเงินกู้
        $y_point = 227.8;
        $pdf->SetXY(12, $y_point, 3);
        $pdf->MultiCell(71.5, 5, U2T($money_loan_amount_2text), $border, "C"); // จำนวนเงินกู้(ตัวอักษร)
        $pdf->SetXY(135, $y_point, 3);
        $pdf->MultiCell(20, 5, U2T($date_to_text), $border, "C"); // ได้รับเงินกู้ ณ วันที่ (วัน)
        $pdf->SetXY(158, $y_point, 3);
        $pdf->MultiCell(17, 5, U2T($month_short_arr[$date_to_month]), $border, "C"); // ได้รับเงินกู้ ณ วันที่ (เดือน)
        $pdf->SetXY(177, $y_point, 3);
        $pdf->MultiCell(17, 5, U2T($date_to_year), $border, "C"); // ได้รับเงินกู้ ณ วันที่ (ปี)
        $y_point = 264.3;
        $pdf->SetXY(90.5, $y_point, 3);
        $pdf->MultiCell(57, 5, U2T($fullname_th), $border, "C"); // ชื่อ(ผู้รับเงิน)
    }else if($pageNo == '3'){
        $pdf->SetFont('THSarabunNew', '', 11 );
        $y_point = 21.5;
        $pdf->SetXY( 150.8, $y_point );
        $pdf->MultiCell(47, 5, U2T($location['profile_location']['coop_name_th']), $border, "C"); // เขียนที่
        $pdf->SetFont('THSarabunNew', '', 12 );
        $y_point = 30.5;
        $pdf->SetXY( 148.2, $y_point );
        $pdf->MultiCell(40, 5, U2T($full_date), $border, "C"); // วันที่
        $y_point = 49.5;
        $pdf->SetXY( 54.2, $y_point );
        $pdf->MultiCell(83.5, 5, U2T($fullname_th), $border, "C"); // ชื่อผู้กู้
        $y_point = 57.5;
        $pdf->SetXY( 35.2, $y_point );
        $pdf->MultiCell(30.5, 5, U2T($data['member_id']), $border, "C"); // สมาชิกเลขที่ทะเบียนที่
        $pdf->SetXY( 83.2, $y_point );
        $pdf->MultiCell(53.5, 5, U2T($data['mobile']), $border, "C"); // โทรศัพท์มือถือ
        $y_point = 73.5;
        $max_loan_amount = 100000;
        if($data['salary']*5 < $max_loan_amount) {
            $max_loan_amount = $data['salary']*5;
        }
        $pdf->SetXY( 65.2, $y_point );
        $pdf->MultiCell(52.5, 5, U2T(number_format($max_loan_amount)), $border, "C"); // วงเงินกู้สูงสุด(ตามสิทธิ)
        $pdf->SetXY( 141.7, $y_point );
        $pdf->MultiCell(15, 5, U2T(number_format($max_loan_amount /$data['period_amount'],2)), $border, "C"); // ชำระ(เดือน)
        $pdf->SetXY( 183.2, $y_point );
        $pdf->MultiCell(10, 5, U2T($data['period_amount']), $border, "C"); // จำนวน,(งวด)
        $y_point = 81.5;
        $pdf->SetXY( 65.2, $y_point );
        $pdf->MultiCell(52.5, 5, U2T(number_format($data['loan_amount'])), $border, "C"); // วงเงินขอกู้ที่ต้องการ
        $pdf->SetXY( 141.7, $y_point );
        $pdf->MultiCell(15, 5, U2T(number_format($data['total_paid_per_month'],2)), $border, "C"); // ชำระ(เดือน)
        $pdf->SetXY( 183.2, $y_point );
        $pdf->MultiCell(10, 5, U2T($data['period_amount']), $border, "C"); //จำนวน(งวด)

        $y_point = 89.5;
        $pdf->SetXY( 65.2, $y_point );
        $pdf->MultiCell(52.5, 5, U2T(number_format($max_loan_amount)), $border, "C"); //คิดเป็นทุนหรือหุ้น(บาท)
        $pdf->SetXY( 141.7, $y_point );
        $pdf->MultiCell(15, 5, U2T(number_format($max_loan_amount / $data['period_amount'],2)), $border, "C"); // เป็นเงินฝาก (บาท)
        $pdf->SetXY( 183.2, $y_point );
        $pdf->MultiCell(10, 5, U2T($data['period_amount']), $border, "C"); // รวมเป็น (บาท)
        $y_point = 105;
        $pdf->SetXY( 47.5, $y_point );
        $pdf->MultiCell(32, 5, U2T(number_format($share_group['share_collect_value'], 2)), $border, "C"); // คิดเป็น ทุนเรือนหุ้น
        $pdf->SetXY( 99, $y_point );
        $pdf->MultiCell(26, 5, U2T(number_format($deposit['cal_account'], 2)), $border, "C"); // เป็นเงินฝาก(บาท)
        $pdf->SetXY( 142.5, $y_point );
        $pdf->MultiCell(33, 5, U2T(number_format($share_group['share_collect_value'] + $deposit['cal_account'], 2)), $border, "C");// รวมเป็น(บาท)
        $y_point = 112.8;
        $pdf->SetXY( 66, $y_point );
        $pdf->MultiCell(37, 5, U2T(number_format($data['loan_amount'],2)), $border, "C"); //ยอดหนี้รวมทั้งหมด
        $pdf->SetXY( 147.5, $y_point );
        $pdf->MultiCell(28, 5, U2T(number_format($data['loan_amount']*0.15,2)), $border, "C"); // คิดหลักประกัน 15% เป็นจำนวนเงิน
        $y_point = 120.5;
        $pdf->SetXY( 133, $y_point );
        $sum_deposit_more = ($data['loan_amount_balance']*0.15)-($share_group['share_collect_value'] + $deposit['cal_account']);
        if($sum_deposit_more < 0){
            $sum_deposit_more = 0;
        }
        $pdf->MultiCell(41.5, 5, U2T(number_format($sum_deposit_more)), $border, "C");
        if(true) {
            $y_point = 152;
            $pdf->SetXY(22, $y_point);
            $pdf->MultiCell(34, 5, U2T($full_date), $border, "C");// ณ วันที่.... มียอดเงินกู้ฉุกเฉินเหลือเป็น
            $pdf->SetXY(130.5, $y_point);
            $pdf->MultiCell(53, 5, U2T(number_format($contract['cal_loan']['1']['loan_amount_balance'])), $border, "C"); //จำนวนเงิน(บาท)
            $y_point = 183.3;
            $pdf->SetXY(135.5, $y_point);
            $pdf->MultiCell(47.5, 5, U2T(number_format($data['loan_amount'])), $border, "C"); //นำวงเงินที่ผู้ขอกู้หักยอดเงินกู้ฉุกเฉินเดิม ข้อ 1,2,3, จากด้านบน
            $y_point = 191.4;
            $pdf->SetXY(135.5, $y_point);
            $pdf->MultiCell(47.5, 5, U2T(number_format($contract['cal_loan']['1']['loan_amount_balance'])), $border, "C"); //หักลบกับข้อ 5.
            $y_point = 205.5;
            $pdf->SetXY(135.5, $y_point);
            $pdf->MultiCell(47.5, 5, U2T(number_format($data['loan_amount'] - $contract['cal_loan']['1']['loan_amount_balance'])), $border, "C"); // เงินกู้ที่ผู้ขอกู้จะได้รับเป็นจำนวนเงิน
            $y_point = 227.5;
            $pdf->SetXY(26.5, $y_point);
            $pdf->MultiCell(60.5, 5, U2T($fullname_th), $border, "C"); // ชื่อ (ลายเซ็น)
            $y_point = 235.5;
            $pdf->SetXY(26.5, $y_point);
            $pdf->MultiCell(60.5, 5, U2T($full_date), $border, "C"); /// วันที่
            $pdf->SetXY(125.5, $y_point);
            $pdf->MultiCell(59, 5, U2T($full_date), $border, "C"); /// วันที่
        }
    }
}
//exit;
$pdf->Output();