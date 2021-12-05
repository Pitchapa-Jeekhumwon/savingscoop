<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}
    // print_r($user);
    // $filename = $_SERVER["DOCUMENT_ROOT"]."/assets/document/loan/education-loan.pdf" ;
    $filename    = $_SERVER["DOCUMENT_ROOT"]."/fsccoop/assets/document/loan/education-loan.pdf" ;
	$pdf         = new FPDI();
    $pageCount_1 = $pdf->setSourceFile($filename);
    $myImage     = "assets/images/check-mark.png";
    $full_data   = $loan_fscoop; // ข้อมูลจาก coop_loan/coop_mem_apply
    $location    = $profile_location; // location ของสถานที่ ex เขียนที่.....
    $share       = $share_group; // ข้อมูลหุ้น

    $age                     = $this->center_function->diff_birthday($full_data['birthday']); //อายุ
    $monthtext               = $this->center_function->month_arr(); // function แปลงเดือนเป็นตัวอักษร
    $money_loan_amount_2text = $this->center_function->convert($full_data['loan_amount']); //จำนวนเงินกู็(ตัวอักษร)
    $money_salary_2text      = $this->center_function->convert($full_data['salary']);//เงินเดือน(ตัวอักษร)
    $start_member_year       = $this->center_function->diff_year($full_data['approve_date'],date('Y-m-d H:i:s')); // ปีที่เริ่มทำงาน (จำนวนปี)
    $start_member_month      = $this->center_function->diff_month_interval($full_data['approve_date'],date('Y-m-d H:i:s')); // จำนวนเดือน

    if ($full_data['approve_date'] != ''){
        $date_to_year        = (substr($full_data['approve_date'], 0, 4))+543; // ปีที่เริ่มทำสัญญา
    }
    $date_to_text            = number_format(substr($full_data['approve_date'], 8, 2)); // วันที่เริ่มทำสัญญา
    $date_to_month           = number_format(substr($full_data['approve_date'], 5, 2)); // เดือนที่เริ้มทำสัญญา
    $month2text              = $monthtext[$date_to_month]; // เดือนที่เริ่มทำสัญญา (ตัวอักษร)
    $full_date               = $date_to_text."  ".$month2text."  ".$date_to_year; // วัน:เดือน:ปี ที่เริ่มทำสัญญา
    if ($full_data['createdatetime'] != ''){
        $create_year         = (substr($full_data['createdatetime'], 0, 4))+543; // // ปีที่บันทึกข้อมูล
    }

    $create_day = number_format(substr($full_data['createdatetime'], 8, 2)); // วันที่บันทึกข้อมูล
    $create_month = number_format(substr($full_data['createdatetime'], 5, 2)); // เดือนที่บันทึกข้อมูล
    $create_month2text = $monthtext[$create_month]; // เดือนที่บันทึกข้อมูล(ตัวอักษร)
    if ($full_data['createdatetime'] != ''){
        $create_year         = (substr($full_data['createdatetime'], 0, 4))+543; // // ปีที่บันทึกข้อมูล
    }

    $day_start_period       = number_format(substr($full_data['date_start_period'], 8, 2));// วันเริ่มจ่ายงวด(หุ้น)
    $month_start_period     = number_format(substr($full_data['date_start_period'], 5, 2)); // เดือนที่จ่ายค่างวด(หุ้น)
    $year_start_period      = (substr($full_data['approve_date'], 0, 4))+543; // ปีที่จ่ายค่างวด(หุ้น)
    $full_start_period      = $day_start_period."  ".$month_start_period."  ".$year_start_period; // วัน, เดือน, ปี ที่จ่ายค่างวด(หุ้น)
    $fullname_th            = $full_data['prename_full'].$full_data['firstname_th']."  ".$full_data['lastname_th']; // คำนำหน้าชื่อ , ชื่อ-สกุล (ผู้กู้)
    $contract_number_font   = substr($full_data['contract_number'], 0, -8); // ตัวอักษรหน้า เลขที่สัญญา ex. ฉฉ999999 = ฉฉ
    $contract_number_back   = substr($full_data['contract_number'], -9);   //ตัวอักษรหลัง เลขที่สัญญา ex. 999999 = ฉฉ
    $period_amount          = substr($this->center_function->convert($full_data['period_amount']),0,-3*7); //งวด(ตัวอักษร)
    // echo "<pre>";
    // print_r($share);exit;
	for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {	
        $pdf->AddPage();
                    
            $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
            $pdf->SetFont('THSarabunNew', '', 6 );
            $tplIdx = $pdf->importPage($pageNo);
            $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
            $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
            $pdf->SetFont('THSarabunNew', '', 14 );    
            $pdf->SetTitle(U2T('คำขอกู้เงินเพื่อการศึกษา'));
            $border = isset($_GET['show']) && $_GET['show'] == '1' ?  1 : 0;
            // $border = 1;
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetAutoPageBreak(true,0);
            if($pageNo == '1'){
                $pdf->Image($myImage, 35.5, 154.3, 3);
                $pdf->Image($myImage, 35.5, 162.5, 3);
                $pdf->Image($myImage, 35.5, 170.3, 3);
                $pdf->Image($myImage, 35.5, 185, 3);
                $y_point = 28.2;
                $pdf->SetXY( 183, $y_point );
                $pdf->SetFont('THSarabunNew', '', 10 );  
                $pdf->MultiCell(9, 5, U2T($contract_number_font), $border, "C"); //หนังสือกู้ที่
                $pdf->SetXY( 191.5, $y_point );
                $pdf->MultiCell(13, 5, U2T($contract_number_back), $border, "L"); //หนังสือกู้ที่
                $pdf->SetFont('THSarabunNew', '', 14 );  
                $y_point = 35;
                $pdf->SetXY( 176, $y_point );
                $pdf->MultiCell(6, 5, U2T($date_to_text), $border, "C"); //วัน
                $pdf->SetXY( 183, $y_point );
                $pdf->MultiCell(9, 5, U2T($month2text), $border, "C"); //เดือน
                $pdf->SetXY( 193, $y_point );
                $pdf->MultiCell(10, 5, U2T($date_to_year), $border, "C"); //ปี
                $y_point = 45.7;
                $pdf->SetXY( 144.5, $y_point );
                $pdf->MultiCell(53, 5, U2T($location['profile_location']['0']['coop_name_th']), $border, "C"); //เขียนที่
                $y_point = 53.5;
                $pdf->SetXY( 146, $y_point );
                $pdf->MultiCell(50, 5, U2T($create_day."   ".$create_month2text."   ".$create_year), $border, "C"); //วันที่

                $y_point = 68.7;
                $pdf->SetXY( 47, $y_point );
                $pdf->MultiCell(89, 5, U2T($fullname_th), $border, "C"); //ข้าพเจ้า.....
                $pdf->SetXY( 169, $y_point );
                $pdf->MultiCell(28, 5, U2T($full_data['member_id']), $border, "C"); //สมาชิกเลขทะเบียนที่
                $y_point = 76.3;
                $pdf->SetXY( 60, $y_point );
                $pdf->MultiCell(32, 5, U2T(number_format($full_data['salary'])), $border, "C"); //รายได้เดือนละ
                $y_point = 84;
                $pdf->SetXY( 80, $y_point );
                $pdf->MultiCell(26, 5, U2T(number_format($full_data['loan_amount'])), $border, "C"); //ขอกู้เงินจำนวน
                $pdf->SetXY( 116, $y_point );
                $pdf->MultiCell(77.5, 5, U2T($money_loan_amount_2text), $border, "C"); //ตัวอักษรจำนวนเงิน
                $pdf->Image($myImage, 76.107, 93.3, 3);
                $pdf->Image($myImage, 123, 93.3, 3);
                $y_point = 99.5;
                $pdf->SetXY( 44.5, $y_point );
                $pdf->MultiCell(70, 5, U2T('โรงเรียนปากช่อง**'), $border, "C"); //ชื่อสถานศึกษา
                $pdf->SetXY( 135, $y_point );
                $pdf->MultiCell(60, 5, U2T('หนองสาหร่าย**'), $border, "C"); //แขวง, ตำบล
                $y_point = 107;
                $pdf->SetXY( 38, $y_point );
                $pdf->MultiCell(76, 5, U2T($full_data['amphur_name']."*"), $border, "C"); //เขต, อำเภอ
                $pdf->SetXY( 127, $y_point );
                $pdf->MultiCell(67, 5, U2T($full_data['province_name']."*"), $border, "C"); //จังหวัด
                $y_point = 114.7;
                $pdf->SetXY( 116, $y_point );
                $pdf->MultiCell(78, 5, U2T(($position=='')? '-': $position), $border, "C"); //จังหวัด
                $y_point = 122.5;
                $pdf->SetXY( 108, $y_point );
                $pdf->MultiCell(37, 5, U2T($full_data['id_card']), $border, "C"); //จังหวัด
                $pdf->SetXY( 156, $y_point );
                $pdf->MultiCell(37, 5, U2T($full_data['mem_group_name']), $border, "C"); //สังกัด
                $y_point = 130;
                $pdf->SetXY( 57, $y_point );
                $pdf->MultiCell(37, 5, U2T($full_data['address_no']), $border, "C"); //บ้านเลขที่
                $pdf->SetXY( 105, $y_point );
                $pdf->MultiCell(39, 5, U2T(($full_data['address_road']=='')?'-':$full_data['address_road']), $border, "C"); //ถนน..
                $pdf->SetXY( 165, $y_point );
                $pdf->MultiCell(32, 5, U2T(($full_data['district_name']=='')?'-':$full_data['district_name']), $border, "C"); //แขวง, ตำบล..
                $y_point = 137.8;
                $pdf->SetXY( 38, $y_point );
                $pdf->MultiCell(56, 5, U2T($full_data['amphur_name']), $border, "C"); //เขต, อำเภอ
                $pdf->SetXY( 107, $y_point );
                $pdf->MultiCell(38, 5, U2T($full_data['province_name']), $border, "C"); //จังหวัด
                $pdf->SetXY( 162, $y_point );
                $pdf->MultiCell(33, 5, U2T($full_data['mobile']), $border, "C"); //โทรษัพท์
                $y_point = 199;
                $pdf->SetXY( 121.5, $y_point );
                $pdf->MultiCell(24, 5, U2T(number_format($full_data['money_per_period'])), $border, "C"); //ส่งงวดละ
                $pdf->SetXY( 165.5, $y_point );
                $pdf->MultiCell(20, 5, U2T(number_format($full_data['period_amount'])), $border, "C"); //งวด
                $y_point = 241.9;
                $pdf->SetXY( 131.3, $y_point );
                $pdf->MultiCell(46, 5, U2T($full_data['marry_name']), $border, "C"); //ชื่อผู้กู้
                $y_point = 284.8;
                $pdf->SetXY( 131.3, $y_point );
                $pdf->MultiCell(46, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
            }else if($pageNo == '2'){
                $y_point = 45.2;
                $pdf->SetXY( 131, $y_point );
                $pdf->MultiCell(47, 5, U2T($create_day."  ".$create_month2text."  ".$create_year), $border, "C"); //วันที่
                $pdf->Image($myImage, 67.3, 73.3, 3);
                $pdf->Image($myImage, 106, 73.3, 3);
                $pdf->Image($myImage, 67.3, 88.7, 3);
                $pdf->Image($myImage, 106, 88.7, 3);
                $pdf->Image($myImage, 67.3, 103.7, 3);
                $pdf->Image($myImage, 106, 103.7, 3);
                $y_point = 204;
                $pdf->SetFont('THSarabunNew', '', 9 );
                $pdf->SetXY( 20, $y_point );
                $pdf->MultiCell(15 , 5, U2T(number_format($full_data['salary'])), $border, "C");//เงินได้รายเดือน
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 35, $y_point );
                $pdf->MultiCell(12.5 , 5, U2T($share[0]['share_collect_value']), $border, "C");//เงินค่าหุ้น(บาท)
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 47.5, $y_point );
                $pdf->MultiCell(16.5 , 5, U2T($full_data['']."*"), $border, "C");//จำกัดวงเงินกู้(บาท)
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 63.5, $y_point );
                $pdf->MultiCell(15.5 , 5, U2T($full_data['']."*"), $border, "C");// น/สกู้ (สามัญ)
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 79.5, $y_point );
                $pdf->MultiCell(16.5 , 5, U2T($full_data['']."*"), $border, "C");// จำนวนเงิน (สามัญ)
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 96, $y_point );
                $pdf->MultiCell(15 , 5, U2T($full_data['']."*"), $border, "C");// น/สกู้(เพื่อเหตุฉุกเฉิน)
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 111, $y_point );
                $pdf->MultiCell(17.5 , 5, U2T($full_data['']."*"), $border, "C");// จำนวนเงิน
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 129, $y_point );
                $pdf->MultiCell(14.5 , 5, U2T($full_data['']."*"), $border, "C");// จำนวนเงิน
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 144, $y_point );
                $pdf->MultiCell(17.5 , 5, U2T($full_data['']."*"), $border, "C");// จำนวนเงิน
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 160.5, $y_point );
                $pdf->MultiCell(12.8 , 5, U2T($full_data['']."*"), $border, "C");// จำนวนเงิน
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 174, $y_point );
                $pdf->MultiCell(24 , 5, U2T($full_data['']."*"), $border, "C");// จำนวนเงิน
                $y_point = 157.2;
                $pdf->SetFont('THSarabunNew', '', 14 );
                $pdf->SetXY( 90, $y_point );
                $pdf->MultiCell(64, 5, U2T(number_format($full_data['loan_amount'])), $border, "C"); //วันที่
                $pdf->Image($myImage, 157, 220, 3); //เคยผิดนัดการส่งเงินงวดชำระหนี้(เคย) -- check box
                $pdf->Image($myImage, 170.5, 220, 3); //เคยผิดนัดการส่งเงินงวดชำระหนี้(ไม่เคย) check box
                $y_point = 226.3;
                $pdf->SetXY( 66.5, $y_point );
                $pdf->MultiCell(126, 5, U2T(number_format($full_data['loan_amount'])), $border, "C"); //วันที่
            }else if($pageNo == '3'){
                $y_point = 35.2;
                $pdf->SetXY( 165, $y_point );
                $pdf->MultiCell(15, 5, U2T($contract_number_font), $border, "C"); //ชื่อผู้กู้
                $pdf->SetXY( 182, $y_point );
                $pdf->MultiCell(15, 5, U2T($contract_number_back), $border, "C"); //ชื่อผู้กู้
                $y_point = 42.7;
                $pdf->SetXY( 45, $y_point );
                $pdf->MultiCell(116.5, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้ 
                $pdf->SetXY( 169, $y_point );
                $pdf->MultiCell(27, 5, U2T($date_to_text." ".$month2text." ".$date_to_year), $border, "L"); //วันที่
                $y_point = 50.6;
                $pdf->SetXY( 158, $y_point );
                $pdf->MultiCell(36.5, 5, U2T($full_data['member_id']), $border, "C"); //ตำแหน่ง
                $y_point = 58.2;
                $pdf->SetXY( 85, $y_point );
                $pdf->MultiCell(55, 5, U2T($full_data['position_name']), $border, "C"); //สมาชิก
                $y_point = 65.7;
                $pdf->SetXY( 48, $y_point );
                $pdf->MultiCell(37, 5, U2T($full_data['id_card']), $border, "C"); //ID
                $pdf->SetXY( 95, $y_point );
                $pdf->MultiCell(43, 5, U2T(' - '), $border, "C"); //สังกัด
                $pdf->SetXY( 172.5, $y_point );
                $pdf->MultiCell(21, 5, U2T($full_data['address_no']), $border, "C"); //บ้านเลขที่
                $y_point = 73.5;
                $pdf->SetXY( 30.5, $y_point );
                $pdf->MultiCell(47, 5, U2T(($full_data['address_road']=='')?'-':$full_data['address_road']), $border, "C"); //ถนน
                $pdf->SetXY( 97, $y_point );
                $pdf->MultiCell(40, 5, U2T($full_data['district_name']), $border, "C"); //ตำบล
                $pdf->SetXY( 155, $y_point );
                $pdf->MultiCell(38, 5, U2T($full_data['amphur_name']), $border, "C"); //อำเภอ
                $y_point = 81;
                $pdf->SetXY( 33, $y_point );
                $pdf->MultiCell(45, 5, U2T($full_data['province_name']), $border, "C"); //จังหวัด
                $pdf->SetXY( 93, $y_point );
                $pdf->MultiCell(58, 5, U2T($full_data['mobile']), $border, "C"); //เบอร์โทรศัพท์
                $y_point = 104;
                $pdf->SetXY( 102, $y_point );
                $pdf->MultiCell(27, 5, U2T(number_format($full_data['loan_amount'])), $border, "C"); //จำนวนเงิน
                $pdf->SetXY( 138, $y_point );
                $pdf->MultiCell(57, 5, U2T($money_loan_amount_2text), $border, "C"); //ตัวอักษร
                $y_point = 119.7;
                $pdf->SetXY( 149, $y_point );
                $pdf->MultiCell(40, 5, U2T($full_data['loan_amount_total']), $border, "C"); //จำนวนเงิน
                $y_point = 127;
                $pdf->SetXY( 50.5, $y_point );
                $pdf->MultiCell(31, 5, U2T($period_amount), $border, "C"); //ตัวอักษรจำนวนเงิน
                $y_point = 127;
                $pdf->SetXY( 157, $y_point );
                $pdf->MultiCell(30, 5, U2T($full_data['interest_per_year']), $border, "C"); ///จำนวนเงิน
                $y_point = 134.8;
                $pdf->SetXY( 62, $y_point );
                $pdf->MultiCell(27, 5, U2T($monthtext[$month_start_period]), $border, "C"); //ตั้งแต่เดือน
                
            }else if($pageNo == '4'){
                $y_point = 27;
                $pdf->SetXY( 120, $y_point );
                $pdf->MultiCell(12, 5, U2T($share['share_bill']), $border, "C"); //สหกรณ์เลขที่
                $pdf->SetXY( 145, $y_point );
                $pdf->MultiCell(14, 5, U2T($share['share_collect']), $border, "C"); //จำนวน
                $pdf->SetXY( 175, $y_point );
                $pdf->MultiCell(14, 5, U2T($share['share_payable_value']), $border, "C"); //เป็นจำนวนเงิน
                $y_point = 98.5;
                $pdf->SetXY( 31, $y_point );
                $pdf->MultiCell(38.5, 5, U2T('*'), $border, "C"); //ข้าฯ(ข้าราชการตำรวจระดับสารวัตเหนือตนขึ้นไป)
                $pdf->SetXY( 81, $y_point );
                $pdf->MultiCell(30, 5, U2T('*'), $border, "C"); //ตำแหน่ง
                $y_point = 105.0;
                $pdf->SetXY( 41.5, $y_point );
                $pdf->MultiCell(63, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                $y_point = 92;
                $pdf->SetXY( 125, $y_point );
                $pdf->MultiCell(50, 5, U2T($fullname_th), $border, "C"); //ลายเซ็นผู้กู้
                $y_point = 154.3;
                $pdf->SetXY( 156, $y_point );
                $pdf->MultiCell(40, 5, U2T($fullname_th), $border, "C"); //ลายเซ็นผู้กู้
                $y_point = 162;
                $pdf->SetXY( 158, $y_point );
                $pdf->MultiCell(34, 5, U2T($full_date), $border, "C"); //ลายเซ็นผู้กู้
                $y_point = 169.5;
                $pdf->SetXY( 46, $y_point );
                $pdf->MultiCell(60, 5, U2T($full_data['marry_name']), $border, "C"); //ลายเซ็นผู้กู้
                $pdf->SetXY( 131, $y_point );
                $pdf->MultiCell(65, 5, U2T($fullname_th), $border, "C"); //คู่สมรสของ
                $y_point = 177;
                $pdf->SetXY( 55, $y_point );
                $pdf->MultiCell(42, 5, U2T($fullname_th), $border, "C"); //ยินยอมให้
                $y_point = 200;
                $pdf->SetXY( 113, $y_point );
                $pdf->MultiCell(42, 5, U2T($full_data['marry_name']), $border, "C"); //ลายเซ็นคู่สมรส
                $y_point = 215.3;
                $pdf->SetXY( 113, $y_point );
                $pdf->MultiCell(42, 5, U2T($fullname_th), $border, "C"); //ลายเซ็นคู่สมรส
                $y_point = 227.3;
                $pdf->SetXY( 34, $y_point );
                $pdf->MultiCell(84.5, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                $pdf->SetXY( 149, $y_point );
                $pdf->MultiCell(38, 5, U2T(number_format($full_data['loan_amount'])), $border, "C"); //จำนวนเงินกู้
                $y_point = 234.8;
                $pdf->SetXY( 24, $y_point );
                $pdf->MultiCell(107, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                $y_point = 250;
                $pdf->SetXY( 94, $y_point );
                $pdf->MultiCell(51, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
            }else if($pageNo == '5'){
                $y_point = 50.5;
                $pdf->SetXY( 123, $y_point );
                $pdf->MultiCell(14, 5, U2T($date_to_text), $border, "C"); //วันที่
                $pdf->SetXY( 146, $y_point );
                $pdf->MultiCell(27, 5, U2T($date_to_month), $border, "C"); //วันที่
                $pdf->SetXY( 180.5, $y_point );
                $pdf->MultiCell(15, 5, U2T($date_to_year), $border, "C"); //วันที่
                $y_point = 82.5;
                $pdf->SetXY( 60, $y_point );
                $pdf->MultiCell(67, 5, U2T($fullname_th), $border, "C"); //ชื่อ...
                $y_point = 90.3;
                $pdf->SetXY( 73, $y_point );
                $pdf->MultiCell(34, 5, U2T(number_format($full_data['loan_amount'])), $border, "C"); //จำนวนเงิน
                $pdf->SetXY( 119, $y_point );
                $pdf->MultiCell(65, 5, U2T($this->center_function->convert($full_data['loan_amount'])), $border, "C"); //จำนวนเงินตัวเขียน
                $y_point = 113;
                $pdf->SetXY( 40, $y_point );
                $pdf->MultiCell(30, 5, U2T(number_format($full_data['loan_amount'])), $border, "C"); //จำนวนเงิน
                $pdf->SetXY( 85, $y_point );
                $pdf->MultiCell(72, 5, U2T($money_loan_amount_2text), $border, "C"); //จำนวนเงินตัวเขียน
                $pdf->Image($myImage, 35.5, 122.3, 3);
                $pdf->Image($myImage, 35.5, 129.7, 3);
                $pdf->Image($myImage, 35.5, 138.1, 3);
                $y_point = 143.7;
                $pdf->SetXY( 35, $y_point );
                $pdf->MultiCell(159, 5, U2T('*'), $border, "C"); //ส่งมอบแก่
                $pdf->Image($myImage, 35.5, 160.7, 3);
                $y_point = 166.5;
                $pdf->SetXY( 35, $y_point);
                $pdf->MultiCell(56, 5, U2T($full_data['transfer_type']), $border, "C"); //บัญชีเงินฝากประเภท
                $pdf->SetXY( 107, $y_point);
                $pdf->MultiCell(88, 5, U2T($full_data['transfer_account_id']), $border, "C"); //เลขที่บัญชี
                $y_point = 197;
                $pdf->SetXY( 87, $y_point);
                $pdf->MultiCell(33, 5, U2T(number_format($full_data['loan_amount'])), $border, "C"); //กู้เงินจากสหกรณ์จำนวน
                $pdf->SetXY( 142, $y_point);
                $pdf->MultiCell(27, 5, U2T('*'), $border, "C"); //คงเหลือ
                $y_point = 243;
                $pdf->SetXY( 82.5, $y_point);
                $pdf->MultiCell(55, 5, U2T('*'), $border, "C"); //คงเหลือ
            }else if($pageNo == '6'){

            }else if($pageNo == '7'){
                $y_point = 40.5;
                $pdf->SetXY( 137, $y_point );
                $pdf->MultiCell(60, 5, U2T($location['profile_location']['0']['coop_name_th']), $border, "C"); //เขียนที่
                $y_point = 48;
                $pdf->SetXY( 131.5, $y_point );
                $pdf->MultiCell(9, 5, U2T($create_day), $border, "C"); //วัน
                $pdf->SetXY( 149.5, $y_point );
                $pdf->MultiCell(25, 5, U2T($create_month2text), $border, "C"); //เดือน
                $pdf->SetXY( 182,   $y_point );
                $pdf->MultiCell(14, 5, U2T($create_year), $border, "C"); //ปี
                $y_point = 55.8;
                $pdf->SetXY( 47, $y_point );
                $pdf->MultiCell(60, 5, U2T($fullname_th), $border, "C"); //ชื่อ...
                $pdf->SetXY( 145, $y_point );
                $pdf->MultiCell(33, 5, U2T($full_data['id_card']), $border, "C"); // เลขประจำตัวประชาชน
                $pdf->SetXY( 185, $y_point );
                $pdf->MultiCell(9, 5, U2T($age), $border, "C"); //อายุ
                $y_point = 63.5;
                $pdf->SetXY( 52.5, $y_point );
                $pdf->MultiCell(15, 5, U2T($full_data['c_address_no']), $border, "C"); //บ้านเลขที่(ปัจจุบัน)
                $pdf->SetXY( 73.5, $y_point );
                $pdf->MultiCell(15, 5, U2T(($full_data['c_address_moo']=='')?'-':$full_data['c_address_moo']), $border, "C"); // หมู่(ปัจจุบัน)
                $pdf->SetXY( 106.5, $y_point );
                $pdf->MultiCell(42, 5, U2T(($full_data['c_address_soi']=='')?'-':$full_data['c_address_soi']), $border, "C"); //ตรอก,ซอย (ปัจจุบัน)
                $pdf->SetXY( 157.5, $y_point );
                $pdf->MultiCell(38, 5, U2T(($full_data['c_address_road']=='')?'-':$full_data['c_address_road']), $border, "C"); // ถนน (ปัจจุบัน)
                $y_point = 71;
                $pdf->SetXY( 40.5, $y_point );
                $pdf->MultiCell(48, 5, U2T($full_data['district_name']), $border, "C"); // ตำบล(ปัจจุบัน)
                $pdf->SetXY( 106, $y_point );
                $pdf->MultiCell(42, 5, U2T($full_data['amphur_name']), $border, "C"); // อำเภอ (ปัจจุบัน)
                $pdf->SetXY( 161, $y_point );
                $pdf->MultiCell(35, 5, U2T($full_data['province_name']), $border, "C"); //จังหวัด(ปัจจุบัน)
                $y_point = 78.5;
                $pdf->SetXY( 49, $y_point );
                $pdf->MultiCell(38, 5, U2T($full_data['tel']), $border, "C"); // เบอร์โทรศัพท์
                $pdf->SetXY( 100, $y_point );
                $pdf->MultiCell(38, 5, U2T($full_data['mobile']), $border, "C"); // เบอร์มือถือ
                $pdf->SetXY( 152, $y_point );
                $pdf->MultiCell(43.5, 5, U2T($full_data['position'].'*'), $border, "C"); // ตำแหน่ง
                $y_point = 86.2;
                $pdf->SetXY( 30.5, $y_point );
                $pdf->MultiCell(38, 5, U2T($full_data['mem_group_name_faction']), $border, "C"); // สังกัด
                $pdf->SetXY( 106.5, $y_point );
                $pdf->MultiCell(20, 5, U2T(number_format($full_data['salary'], '0', '.', ',')), $border, "C"); // เงินเดือน
                $pdf->SetXY( 135.5, $y_point );
                $pdf->MultiCell(60, 5, U2T($this->center_function->convert($full_data['salary'])), $border, "C"); // เงินเดือน(ตัวอักษร)
                $y_point = 93.8;
                $pdf->SetXY( 157.5, $y_point );
                $pdf->MultiCell(40, 5, U2T($full_data['member_id']), $border, "C"); // ทะเบียนสมาชิกเลขที่
                $y_point = 117.5;
                $pdf->SetXY( 64.5, $y_point );
                $pdf->MultiCell(14, 5, U2T('*'), $border, "C"); //สัญญากู้เลขที่..
                $pdf->SetXY( 106, $y_point );
                $pdf->MultiCell(7, 5, U2T('*'), $border, "C"); // คำขอกู้เลขที่
                $y_point = 243.5;
                $pdf->SetXY( 100, $y_point );
                $pdf->MultiCell(50, 5, U2T($fullname_th), $border, "C"); // ชื่อ
            }else if($pageNo == '8'){

            }else if($pageNo == '9'){
                $y_point = 53;
                $pdf->SetXY( 118, $y_point );
                $pdf->MultiCell(11, 5, U2T($date_to_text), $border, "C"); //วันที่
                $pdf->SetXY( 136, $y_point );
                $pdf->MultiCell(33, 5, U2T($date_to_month), $border, "C"); //วันที่
                $pdf->SetXY( 178.5, $y_point );
                $pdf->MultiCell(18, 5, U2T($date_to_year), $border, "C"); //วันที่
                $y_point = 88.5;
                $pdf->SetXY( 53, $y_point );
                $pdf->MultiCell(62, 5, U2T($fullname_th), $border, "C"); //ชื่อ...
                $pdf->SetXY( 129, $y_point );
                $pdf->MultiCell(64, 5, U2T($full_data['position']), $border, "C"); //ชื่อ...
                $y_point = 95.8;
                $pdf->SetXY( 41, $y_point );
                $pdf->MultiCell(64, 5, U2T($full_data['mem_group_name_faction']), $border, "C"); //ชื่อ...
                $pdf->SetXY(115, $y_point );
                $pdf->MultiCell(50, 5, U2T($full_data['mem_group_name_level']), $border, "C"); //ชื่อ...
                $y_point = 111;
                $pdf->SetXY( 35.5, $y_point );
                $pdf->MultiCell(23, 5, U2T(number_format($full_data['salary'])), $border, "C"); //เงินเดือน
                $pdf->SetXY( 69, $y_point );
                $pdf->MultiCell(66, 5, U2T($this->center_function->convert($full_data['salary'])), $border, "C"); //เงินเดือน(ตัวอักษร)
                $pdf->SetXY( 159, $y_point );
                $pdf->MultiCell(38, 5, U2T($month_arr[$full_data['month_start']]."*"), $border, "C"); //ในเดือน....มีเงินคงเหลือ
                $y_point = 118.7;
                $pdf->SetXY( 51, $y_point );
                $pdf->MultiCell(40, 5, U2T(number_format($full_data['salary']-$full_data['money_per_period'], 2)."*"), $border, "C"); //
                $pdf->SetXY( 102, $y_point );
                $pdf->MultiCell(90, 5, U2T($this->center_function->convert($full_data['salary']-$full_data['money_per_period'])), $border, "C"); //
            }
    }
	
	//exit;
	$pdf->Output();