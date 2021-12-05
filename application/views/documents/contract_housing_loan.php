<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}
     $filename = $_SERVER["DOCUMENT_ROOT"]."/assets/document/loan/housing-loan.pdf" ;
//   $filename = $_SERVER["DOCUMENT_ROOT"]."/fsccoop/assets/document/loan/housing-loan.pdf" ;
	$pdf = new FPDI();
    $pageCount_1 = $pdf->setSourceFile($filename);
    $myImage = "assets/images/check-mark.png";
    $location  = $profile_location;
    $grt        =$grt;
    $contract    = $contract;
    // location ของสถานที่ ex เขียนที่.....
    $grt_remark = (substr($grt['remark'],5,9));

    $age                = $this->center_function->diff_birthday($data['birthday']); //อายุ
    $monthtext          = $this->center_function->month_arr(); // function แปลงเดือนเป็นตัวอักษร
    $money_loan_amount_2text = $this->center_function->convert($data['loan_amount']); //จำนวนเงินกู็(ตัวอักษร)
    $money_salary_2text = $this->center_function->convert($data['salary']);//เงินเดือน(ตัวอักษร)
    $start_member_year  = $this->center_function->diff_year($data['approve_date'],date('Y-m-d H:i:s')); // ปีที่เริ่มทำงาน (จำนวนปี)
    $start_member_month       = ($this->center_function->diff_month_interval($data['approve_date'],date('Y-m-d H:i:s'))%12); // จำนวนเดือน
    if ($data['approve_date'] != ''){
        $date_to_year       = (substr($data['approve_date'], 0, 4))+543; // ปีที่เริ่มทำสัญญา
    }
    $date_to_text       = number_format(substr($data['approve_date'], 8, 2)); // วันที่เริ่มทำสัญญา
    $date_to_month      = number_format(substr($data['approve_date'], 5, 2)); // เดือนที่เริ้มทำสัญญา
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
    $day_start_period   = number_format(substr($data['date_start_period'], 8, 2));
    $month_start_period = number_format(substr($data['date_start_period'], 5, 2));
    if(!empty($data['approve_date'])){
        $year_start_period  = (substr($data['approve_date'], 0, 4))+543;
    }
    $full_start_period  = $day_start_period."  ".$month_start_period."  ".$year_start_period;
    $fullname_th        = $data['prename_full'].$data['firstname_th']."  ".$data['lastname_th'];
	for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {	
        $pdf->AddPage();
            $tplIdx = $pdf->importPage($pageNo);
            $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
            $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
            $pdf->SetFont('THSarabunNew', '', 14 );
            $border = isset($_GET['show']) && $_GET['show'] == '1' ?  1 : 0;
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetAutoPageBreak(true,0);
            if($pageNo == '1'){
                $y_point = 44;
                $pdf->Image($myImage, 29, $y_point, 3); // คำขอกู้เงินพิเศษเพื่อการเคหะ
                $y_point = 52;
                $pdf->Image($myImage, 29, $y_point, 3); // สำเนา่บัตรประจำตัวข้าราชการ/ประชาชน
                $y_point = 59;
                $pdf->Image($myImage, 29, $y_point, 3); // สำเนาทะเบียนบ้านของผู้กู้, สมรส
                if(!empty($data['marry_name'])) {
                $y_point = 66.9;
                $pdf->Image($myImage, 29, $y_point, 3); // สำเนาทะเบียนสมรสของผู้กู้
                   }
                $y_point = 73.7;
                $pdf->Image($myImage, 29, $y_point, 3); // สลิปเงินเดือนฉบับสุดท้ายของผู้กู้
                $y_point = 82.5;
                $pdf->Image($myImage, 29, $y_point, 3); // สัญญาจะซื้อจะขาย
                $y_point = 89.5;
                $pdf->Image($myImage, 29, $y_point, 3); //สำเนาโฉนดที่ดิน(ทุกหน้า)
                $pdf->Image($myImage, 95.5, $y_point, 3); //หนังสือแสดงกรรมสิทธิ์ในห้องชุด
                $y_point = 97.2;
                $pdf->Image($myImage, 29, $y_point, 3); // ผังโครงการ
                $y_point = 104.4;
                $pdf->Image($myImage, 29, $y_point, 3); // แผนที่สังเขป
                $y_point = 120.4;
                $pdf->Image($myImage, 29, $y_point, 3); // สัญญาเงินกู้จากสถาบันการเงิน
                $y_point = 128.4;
                $pdf->Image($myImage, 29, $y_point, 3); // หลักฐานการผ่อนชำระหนี้
                $y_point = 136;
                $pdf->Image($myImage, 29, $y_point, 3); //หลักฐานการเป็นเจ้าของกรรมสิทธิ์
                $y_point = 143.5;
                //$pdf->Image($myImage, 44.7, $y_point, 3); // กรณีหย่า
                $y_point = 150.8;
                //$pdf->Image($myImage, 44.7, $y_point, 3); // กรณีเสียชีวิต
                $y_point = 158.5;
                //$pdf->Image($myImage, 44.7, $y_point, 3); // กรณีเปลี่ยนชื่อ-สกุล
                $y_point = 172;
                $pdf->SetXY( 128, $y_point );
                $pdf->MultiCell(62, 5, U2T($fullname_th), $border, "C"); //ชื่อ
                $y_point = 284.5;
                $pdf->SetXY( 125.5, $y_point );
                $pdf->MultiCell(70, 5, U2T($fullname_th), $border, "C"); //ชื่อ
            }else if($pageNo == '2'){

            }else if($pageNo == '3'){
                $y_point = 10;
                $pdf->SetXY( 34.5, $y_point );
                $pdf->MultiCell(18, 5, U2T(''), $border, "C"); //เลขที่รับ
                $y_point = 17.5;
                $pdf->SetXY( 31.5, $y_point );
                $pdf->MultiCell(23, 5, U2T($date_to_text." /".$month_short_arr[$date_to_month]."/".$date_to_year), $border, "C"); //วันที่
                $y_point = 14;
                $pdf->SetXY( 179, $y_point );
                $pdf->MultiCell(18, 5, U2T('*'), $border, "C"); //หนังสือกู้พิเศษที่
                $y_point = 22;
                $pdf->SetXY( 160, $y_point );
                $pdf->MultiCell(37, 5, U2T($full_date), $border, "C"); //วันที่
                $y_point = 48.1;
                $y_point = 48.1;
                $pdf->SetXY( 135.3, $y_point );
                $pdf->MultiCell(61, 5, U2T($location['profile_location']['0']['coop_name_th']), $border, "C"); //เขียนที่
                $y_point = 55;
                $pdf->SetXY( 132, $y_point );
                $pdf->MultiCell(13, 5, U2T($date_to_text), $border, "C"); //วันที่(วัน)
                $pdf->SetXY( 153.5, $y_point );
                $pdf->MultiCell(23, 5, U2T($month2text), $border, "C"); //วันที่(เดือน)
                $pdf->SetXY( 184.5, $y_point );
                $pdf->MultiCell(15, 5, U2T($date_to_year), $border, "C"); //วันที่(ปี)
                $y_point = 74;
                $pdf->SetXY( 49, $y_point );
                $pdf->MultiCell(70, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                $pdf->SetXY( 150, $y_point );
                $pdf->MultiCell(20, 5, U2T($data['member_id']), $border, "C"); //สมาชิกเลขทะเบียนที่
                $pdf->SetXY( 177, $y_point );
                $pdf->MultiCell(12, 5, U2T($age), $border, "C"); //อายุ
                $y_point = 81.2;
                $pdf->SetXY( 79, $y_point );
                $pdf->MultiCell(57, 5, U2T(($data['position_name']=='')?'-':$data['position_name']), $border, "C"); //ตำแหน่ง
                $pdf->SetXY( 146, $y_point );
                $pdf->MultiCell(44, 5, U2T($data['mem_group_name']), $border, "C"); //สังกัด
                $y_point = 95.8;
                $pdf->SetXY( 93, $y_point );
                $pdf->MultiCell(27, 5, U2T(number_format($data['loan_amount'])), $border, "C"); //จำนวนเงินกู้
                $pdf->SetXY( 130, $y_point );
                $pdf->MultiCell(61, 5, U2T($money_loan_amount_2text), $border, "C"); //จำนวนเงินกู้ตัวอักษร
                $y_point = 102.8;
                $pdf->SetXY( 53, $y_point );
                $pdf->MultiCell(24, 5, U2T($data['period_amount']), $border, "C"); //ระยะเวลาผ่อนชำระ
                $pdf->SetXY( 144.3, $y_point );

                $pdf->MultiCell(35, 5, U2T(number_format($data['money_per_period'])), $border, "C"); //จำนวนที่ผ่อนชำระได้
                $y_point = 118.7;
                //$pdf->Image($myImage, 43.5, $y_point, 3); //Check วัตถุประสงค์ในการซื้อ
                $y_point = 131.5;
                $pdf->SetXY( 43.5, $y_point );
                $pdf->MultiCell(37, 5, U2T(''), $border, "C"); //ราคาซื้อขายบ้านพร้อมที่ดิน
                $pdf->SetXY( 102, $y_point );
                $pdf->MultiCell(28, 5, U2T($data['']), $border, "C"); //เงินมัดจำ
                $pdf->SetXY( 158.5, $y_point );
                $pdf->MultiCell(25.5, 5, U2T($data['']), $border, "C"); //เงินทุนส่วนตัว
                $y_point = 139;
                $pdf->SetXY( 93.5, $y_point );
                $pdf->MultiCell(36, 5, U2T(number_format($data['loan_amount_total_balance'])), $border, "C"); //เงินต้นจำนวน
                $pdf->SetXY( 148.8, $y_point );
                $pdf->MultiCell(35, 5, U2T(number_format($data['loan_interest_amount'])), $border, "C"); //ดอกเบี้ยจำนวน
                $y_point = 146.1;
                $pdf->SetXY( 76.5, $y_point );
                $pdf->MultiCell(52.8, 5, U2T($data['']), $border, "C"); //ภาระจำนองสถาบันการเงิน
                $pdf->SetXY( 136.5, $y_point );
                $pdf->MultiCell(52.8, 5, U2T($data['']), $border, "C"); //สาขา
                $y_point = 164.9;
                $pdf->SetXY( 42.5, $y_point );
                $pdf->MultiCell(22, 5, U2T($data['']), $border, "C"); //โฉนดเลขที่(หลักประกัน)
                $pdf->SetXY( 82.9, $y_point );
                $pdf->MultiCell(22, 5, U2T($data['']), $border, "C"); //ตำบล, แขวง (หลักประกัน)
                $y_point = 171.9;
                $pdf->SetXY( 40, $y_point );
                $pdf->MultiCell(26.5, 5, U2T($data['']), $border, "C"); //อำเภอ, เขต (หลักประกัน)
                $pdf->SetXY( 80, $y_point );
                $pdf->MultiCell(25, 5, U2T($data['']), $border, "C"); //จังหวัด (หลักประกัน)
                $y_point = 179.2;
                $pdf->SetXY( 52, $y_point );
                $pdf->MultiCell(10.5, 5, U2T($data['']), $border, "C"); //อำเภอ, เขต (หลักประกัน)
                $pdf->SetXY( 68, $y_point );
                $pdf->MultiCell(10, 5, U2T($data['']), $border, "C"); //จังหวัด (หลักประกัน)
                $pdf->SetXY( 84.5, $y_point );
                $pdf->MultiCell(12.3, 5, U2T($data['']), $border, "C"); //จังหวัด (หลักประกัน)
                $y_point = 186.3;
                $pdf->SetXY( 42, $y_point );
                $pdf->MultiCell(21.5, 5, U2T($data['']), $border, "C"); //อำเภอ, เขต (หลักประกัน)
                $pdf->SetXY( 72.5, $y_point );
                $pdf->MultiCell(26, 5, U2T($data['']), $border, "C"); //อำเภอ, เขต (หลักประกัน)
                $y_point = 164.9;
                $pdf->SetXY( 127.5, $y_point );
                $pdf->MultiCell(15, 5, U2T($data['']), $border, "C"); //ห้องชุดเลขที่ (หลักประกัน)
                $pdf->SetXY( 152.9, $y_point );
                $pdf->MultiCell(10, 5, U2T($data['']), $border, "C"); //ชั้น (หลักประกัน)
                $pdf->SetXY( 170, $y_point );
                $pdf->MultiCell(11.5, 5, U2T($data['']), $border, "C"); //เนื้อที่.....ตรม (หลักประกัน)
                $y_point = 171.9;
                $pdf->SetXY( 127.5, $y_point );
                $pdf->MultiCell(60, 5, U2T($data['']), $border, "C"); //ชื่ออาคาร, ชุด (หลักประกัน)
                $y_point = 179.2;
                $pdf->SetXY( 127.5, $y_point );
                $pdf->MultiCell(18.5, 5, U2T($data['']), $border, "C"); //ห้องชุดแบบ (หลักประกัน)
                $pdf->SetXY( 164, $y_point );
                $pdf->MultiCell(26, 5, U2T($data['']), $border, "C"); //ตำบล, แขวง (หลักประกัน)
                $y_point = 186.3;
                $pdf->SetXY( 126, $y_point );
                $pdf->MultiCell(25.5, 5, U2T($data['']), $border, "C"); //อำเภอ, เขต (หลักประกัน)
                $pdf->SetXY( 164.5, $y_point );
                $pdf->MultiCell(26, 5, U2T($data['']), $border, "C"); //จังหวัด (หลักประกัน)
                $y_point = 201;
                $pdf->SetXY( 70, $y_point );
                $pdf->MultiCell(43, 5, U2T($data['']), $border, "C"); //การศึกษาระดับ
                $pdf->SetXY( 120, $y_point );
                $pdf->MultiCell(70, 5, U2T($data['']), $border, "C"); //จาก...
                $y_point = 209.2;
                //$pdf->Image($myImage, 62, $y_point, 3); // ข้อมูลเพิ่มเติม อาชีพ(รับราชการตำรวจ)-Check
                //$pdf->Image($myImage, 109, $y_point, 3);// ข้อมูลเพิ่มเติม อาชีพ(ลูกจ้างประจำ)-Check
                //$pdf->Image($myImage, 143, $y_point, 3);// ข้อมูลเพิ่มเติม อาชีพ(อื่นๆ)-Check
                $y_point = 208.1;
                $pdf->SetXY( 155, $y_point );
                $pdf->MultiCell(20, 5, U2T(''), $border, "C"); //ข้อมูลเพิ่มเติม อาชีพ(อื่นๆ)-Check
                $y_point = 215;
                $pdf->SetXY( 37.5, $y_point );
                $pdf->MultiCell(37, 5, U2T($data['position_name']), $border, "C"); //ข้อมูลเพิ่มเติม ตำแหน่ง
                $pdf->SetXY( 90.5, $y_point );
                $pdf->MultiCell(30, 5, U2T($data['']), $border, "C"); //ข้อมูลเพิ่มเติม แผนก,ฝ่าย
                $pdf->SetXY( 130.7, $y_point );
                $pdf->MultiCell(27, 5, U2T($data['']), $border, "C"); //ข้อมูลเพิ่มเติม เบอร์โทรศัพท์
                $pdf->SetXY( 175, $y_point );
                $pdf->MultiCell(16, 5, U2T(''), $border, "C"); //ข้อมูลเพิ่มเติม อายุงาน
                $y_point = 222.3;
                $pdf->SetXY( 46, $y_point );
                $pdf->MultiCell(40, 5, U2T(number_format($data['salary'])), $border, "C"); //ข้อมูลเพิ่มเติม อัตตราเงินเดือน
                $pdf->SetXY( 116.5, $y_point );
                $pdf->MultiCell(32, 5, U2T(''), $border, "C"); //ข้อมูลเพิ่มเติม รายได้อื่นๆจาก
                $pdf->SetXY( 160.5, $y_point );
                $pdf->MultiCell(28, 5, U2T(''), $border, "C"); //ข้อมูลเพิ่มเติม จำนวนเงิน (รายได้อื่นๆ)
                $y_point = 231;
                //$pdf->Image($myImage, 67.5, $y_point, 3);
                //$pdf->Image($myImage, 82, $y_point, 3);
                //$pdf->Image($myImage, 99, $y_point, 3);
                //$pdf->Image($myImage, 135, $y_point, 3);
                //$pdf->Image($myImage, 151.5, $y_point, 3);
                $y_point = 236.5;
                $pdf->SetXY( 35, $y_point );
                $pdf->MultiCell(41.5, 5, U2T($data['marry_name']), $border, "C"); //ชื่อคู่สมรส
                $pdf->SetXY( 90, $y_point );
                $pdf->MultiCell(12, 5, U2T($data['']), $border, "C"); //อายุ(คู่สมรส)
                $pdf->SetXY( 117, $y_point );
                $pdf->MultiCell(35, 5, U2T($data['']), $border, "C"); //อาชีพ(คู่สมรส)
                $pdf->SetXY( 165, $y_point );
                $pdf->MultiCell(30, 5, U2T(''), $border, "C"); //ตำแหน่ง(คู่สมรส)
                $y_point = 243.5;
                $pdf->SetXY( 46, $y_point );
                $pdf->MultiCell(57, 5, U2T($data['']), $border, "C"); //สถานที่ทำงาน(คู่สมรส)
                $pdf->SetXY( 115.5, $y_point );
                $pdf->MultiCell(80, 5, U2T($data['']), $border, "C"); //สถานที่ทำงานตั้งอยู่ที่(คู่สมรส)
                $y_point = 250.8;
                $pdf->SetXY( 36.5, $y_point );
                $pdf->MultiCell(37, 5, U2T($data['']), $border, "C"); //รายได้ (คู่สมรส)
                $pdf->SetXY( 104, $y_point );
                $pdf->MultiCell(18, 5, U2T(''), $border, "C"); //จำนวนบุตร (คู่สมรส)
                $pdf->SetXY( 155, $y_point );
                $pdf->MultiCell(29, 5, U2T(''), $border, "C"); //ค่าใช้จ่ายต่อเดือน (คู่สมรส)
                $y_point = 258.1;
                $pdf->SetXY( 87.5, $y_point );
                $pdf->MultiCell(22, 5, U2T($data['address_no']), $border, "C"); //ที่อยู๋(ตามทะเบียนบ้าน)
                $pdf->SetXY( 117.2, $y_point );
                $pdf->MultiCell(28.5, 5, U2T($data['address_moo']), $border, "C"); //หมู่(ตามทะเบียนบ้าน)
                $pdf->SetXY( 153.2, $y_point );
                $pdf->MultiCell(42, 5, U2T($data['address_soi']), $border, "C"); //ซอย (ตามทะเบียนบ้าน)
                $y_point = 265.1;
                $pdf->SetXY( 32.5, $y_point );
                $pdf->MultiCell(38.5, 5, U2T($data['address_road']), $border, "C"); //ถนน (ตามทะเบียนบ้าน)
                $pdf->SetXY( 88.7, $y_point );
                $pdf->MultiCell(45, 5, U2T($data['district_name']), $border, "C"); // ตำบล (ตามทะเบียนบ้าน)
                $pdf->SetXY( 150, $y_point );
                $pdf->MultiCell(45, 5, U2T('amphur_name'), $border, "C"); // อำเภอ (ตามทะเบียนบ้าน)
                $y_point = 272.3;
                $pdf->SetXY( 36, $y_point );
                $pdf->MultiCell(49, 5, U2T($data['province_name']), $border, "C"); //จังหวัด (ตามทะเบียนบ้าน)
                $pdf->SetXY( 107, $y_point );
                $pdf->MultiCell(25, 5, U2T($data['zipcode']), $border, "C"); //รหัสไปรษณีย์ (ตามทะเบียนบ้าน)
                $pdf->SetXY( 142, $y_point );
                $pdf->MultiCell(55, 5, U2T($data['tel']), $border, "C"); //เบอร์โทรศัพท์
            }else if($pageNo == '4'){
                $y_point = 93.1;
                $pdf->SetXY( 43, $y_point );
                $pdf->MultiCell(62.5, 5, U2T($data['']), $border, "C"); //หากสหกรณ์มีเรื่องจำเป็นโปรดติดต่อกับ
                $pdf->SetXY( 127, $y_point );
                $pdf->MultiCell(65.5, 5, U2T($data['']), $border, "C"); //สถานที่ติดต่อ
                $y_point = 100.2;
                $pdf->SetXY( 140, $y_point );
                $pdf->MultiCell(56, 5, U2T($data['']), $border, "C"); //เบอร์โทรศัพท์
                $y_point = 128.9;
                $pdf->SetXY( 97, $y_point );
                $pdf->MultiCell(61.5, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้ขอกู้(ลายเซ็น)
                $y_point = 180;
                $pdf->SetXY( 108, $y_point );
                $pdf->MultiCell(63, 5, U2T($full_date), $border, "C"); //วันที่
                $y_point = 202;
                $pdf->SetXY( 72, $y_point );
                $pdf->MultiCell(46, 5, U2T($full_date), $border, "C"); //เป็นสมาชิกสหกรณ์ตั้งแต่วันที่
                $pdf->SetXY( 137, $y_point );
                $pdf->MultiCell(24, 5, U2T($start_member_year), $border, "C"); //รวมระยะเวลา(ปี)
                $pdf->SetXY( 165, $y_point );
                $pdf->MultiCell(24, 5, U2T($start_member_month), $border, "C"); //รวมระยะเวลา(เดือน)
                $y_point = 209;
                $pdf->SetXY( 87, $y_point );
                $pdf->MultiCell(47, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $pdf->SetXY( 150, $y_point );
                $pdf->MultiCell(32, 5, U2T(number_format($data['money_per_period'])), $border, "C"); //ส่งหุ้น บาท/งวด
                $y_point = 216.2;
                $pdf->SetXY( 54, $y_point );
                $pdf->MultiCell(36.5, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $pdf->SetXY( 105, $y_point );
                $pdf->MultiCell(40.5, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $pdf->SetXY( 156.5, $y_point );
                $pdf->MultiCell(34, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $y_point = 230.2;
                $pdf->SetXY( 62, $y_point );
                $pdf->MultiCell(42, 5,U2T($contract['data'][0]['contract_number']), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $pdf->SetXY( 112, $y_point );
                $pdf->MultiCell(33.5, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $pdf->SetXY( 161, $y_point );
                $pdf->MultiCell(28, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $y_point = 237.7;
                $pdf->SetXY( 40, $y_point );
                $pdf->MultiCell(29, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $pdf->SetXY( 87, $y_point );
                $pdf->MultiCell(24.5, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $pdf->SetXY( 143, $y_point );
                $pdf->MultiCell(23, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $y_point = 244.8;
                $pdf->SetXY( 62, $y_point );
                $pdf->MultiCell(43, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $pdf->SetXY( 112, $y_point );
                $pdf->MultiCell(33.5, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $pdf->SetXY( 160.5, $y_point );
                $pdf->MultiCell(28, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $y_point = 251.8;
                $pdf->SetXY( 40.5, $y_point );
                $pdf->MultiCell(27.5, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $pdf->SetXY( 85.5, $y_point );
                $pdf->MultiCell(25.5, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $pdf->SetXY( 143.5, $y_point );
                $pdf->MultiCell(23.5, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $y_point = 258.8;
                $pdf->SetXY( 85.5, $y_point );
                $pdf->MultiCell(25.5, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน
                $pdf->SetXY( 141.5, $y_point );
                $pdf->MultiCell(24, 5, U2T(""), $border, "C"); //ผู้กู้มีหุ้นสหกรณ์รวมเป็นเงิน

            }else if($pageNo == '5'){

            }else if($pageNo == '6'){
                    
            }else if($pageNo == '7'){

                $y_point = 59.6;
                $pdf->SetXY( 130.8, $y_point );
                $pdf->MultiCell(9.5, 5, U2T($date_to_text), $border, "C"); //วันที่
                $pdf->SetXY( 149, $y_point );
                $pdf->MultiCell(21, 5, U2T($month2text), $border, "C"); //วันที่
                $pdf->SetXY( 178, $y_point );
                $pdf->MultiCell(18, 5, U2T($date_to_year), $border, "C"); //วันที่
                $y_point = 71.5;
                $pdf->SetXY( 47, $y_point );
                $pdf->MultiCell(60, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                $pdf->SetXY( 139.5, $y_point );
                $pdf->MultiCell(35, 5, U2T($data['id_card']), $border, "C"); //ID Card
                $pdf->SetXY( 182.5, $y_point );
                $pdf->MultiCell(10, 5, U2T($age), $border, "C"); //อายุ
                $y_point = 79;
                $pdf->SetXY( 56.2, $y_point );
                $pdf->MultiCell(21, 5, U2T($data['address_no']), $border, "C"); //บ้านเลขที่
                $pdf->SetXY( 85.5, $y_point );
                $pdf->MultiCell(16, 5, U2T($data['address_moo']), $border, "C"); //หมู่
                $pdf->SetXY( 119, $y_point );
                $pdf->MultiCell(30, 5, U2T($data['address_soi']), $border, "C"); //ซอย
                $pdf->SetXY( 158.5, $y_point );
                $pdf->MultiCell(37, 5, U2T($data['address_road']), $border, "C"); //ถนน
                $y_point = 86.8;
                $pdf->SetXY( 44, $y_point );
                $pdf->MultiCell(34, 5, U2T($data['district_name']), $border, "C"); //ตำบล
                $pdf->SetXY( 95, $y_point );
                $pdf->MultiCell(46, 5, U2T($data['amphur_name']), $border, "C"); //อำเภอ
                $pdf->SetXY( 153, $y_point );
                $pdf->MultiCell(42.5, 5, U2T($data['province_name']), $border, "C"); //จังหวัด
                $y_point = 94.3;
                $pdf->SetXY( 52, $y_point );
                $pdf->MultiCell(38.5, 5, U2T($data['tel']), $border, "C"); //เบอร์โทรศัพท์บ้าน
                $pdf->SetXY( 103.5, $y_point );
                $pdf->MultiCell(35.5, 5, U2T($data['mobile']), $border, "C"); //เบอร์มือถือ
                $pdf->SetXY( 152, $y_point );
                $pdf->MultiCell(43, 5, U2T($data['position_name']), $border, "C"); //ตำแหน่ง
                $y_point = 101.8;
                $pdf->SetXY( 35, $y_point );
                $pdf->MultiCell(36, 5, U2T(''), $border, "C"); //สังกัต
                $pdf->SetXY( 109, $y_point );
                $pdf->MultiCell(16.5, 5, U2T(number_format($data['salary'])), $border, "C"); //เงินเดือน
                $pdf->SetXY( 136, $y_point );
                $pdf->MultiCell(57, 5, U2T($money_salary_2text), $border, "C"); //เงินเดือน(ตัวอักษร)
                $y_point = 109.8;
                $pdf->SetXY( 163.5, $y_point );
                $pdf->MultiCell(32, 5, U2T($data['member_id']), $border, "C"); //ทะเบียนสมาชิก
                $y_point = 132.8;
                $pdf->SetXY( 82.5, $y_point );
                $pdf->MultiCell(31, 5, U2T($data['contract_number']), $border, "C"); //สัญญากู้เลขที่
                $pdf->SetXY( 140.5, $y_point );
                $pdf->MultiCell(31, 5,U2T($data['petition_number']), $border, "C"); //คำขอกู้เลขที่
                $y_point = 248;
                $pdf->SetXY( 110.5, $y_point );
                $pdf->MultiCell(57, 5, U2T($fullname_th), $border, "C"); //คำขอกู้เลขที่
            }else if($pageNo == '8'){

            }else if($pageNo == '9'){
                if(!empty($data['marry_name'])) {
                    $y_point = 64.8;
                    $pdf->SetXY(130, $y_point);
                    $pdf->MultiCell(9.5, 5, U2T($date_to_text), $border, "C"); //วันที่
                    $pdf->SetXY(147, $y_point);
                    $pdf->MultiCell(30, 5, U2T($month2text), $border, "C"); //วันที่
                    $pdf->SetXY(183, $y_point);
                    $pdf->MultiCell(15, 5, U2T($date_to_year), $border, "C"); //วันที่
                    $y_point = 80.1;
                    $pdf->SetXY(45, $y_point);
                    $pdf->MultiCell(100, 5, U2T($data['marry_name']), $border, "C"); //ชื่อคู่สมรส
                    $pdf->SetXY(162.5, $y_point);
                    $pdf->MultiCell(30, 5, U2T(''), $border, "C"); //ชื่อผู้กู้
                    $y_point = 87.9;
                    $pdf->SetXY(45.2, $y_point);
                    $pdf->MultiCell(22.2, 5, U2T($data['address_no']), $border, "C"); //ชื่อผู้กู้
                    $pdf->SetXY(76.5, $y_point);
                    $pdf->MultiCell(15.7, 5, U2T($data['address_moo']), $border, "C"); //ชื่อผู้กู้
                    $pdf->SetXY(109.7, $y_point);
                    $pdf->MultiCell(36, 5, U2T($data['address_soi']), $border, "C"); //ชื่อผู้กู้
                    $pdf->SetXY(155.5, $y_point);
                    $pdf->MultiCell(40, 5, U2T($data['address_road']), $border, "C"); //ชื่อผู้กู้
                    $y_point = 95.1;
                    $pdf->SetXY(45.2, $y_point);
                    $pdf->MultiCell(47, 5, U2T($data['district_name']), $border, "C"); //ชื่อผู้กู้
                    $pdf->SetXY(110, $y_point);
                    $pdf->MultiCell(35, 5, U2T($data['amphur_name']), $border, "C"); //ชื่อผู้กู้
                    $pdf->SetXY(158, $y_point);
                    $pdf->MultiCell(35, 5, U2T($data['province_name']), $border, "C"); //ชื่อผู้กู้
                    $y_point = 102.9;
                    $pdf->SetXY(60, $y_point);
                    $pdf->MultiCell(98, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                    $y_point = 149;
                    $pdf->SetXY(88, $y_point);
                    $pdf->MultiCell(60, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                    $y_point = 203.3;
                    $pdf->SetXY(49, $y_point);
                    $pdf->MultiCell(121, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                    $y_point = 210.7;
                    $pdf->SetXY(40, $y_point);
                    $pdf->MultiCell(95, 5, U2T($data['marry_name']), $border, "C"); //ชื่อคู่สมรส
                    $y_point = 230.5;
                    $pdf->SetXY(48, $y_point);
                    $pdf->MultiCell(95, 5, U2T($data['marry_name']), $border, "C"); //ชื่อคู่สมรส
                }
            }else if($pageNo == '10'){

            }else if($pageNo == '11'){
                $y_point = 63.9;
                $pdf->SetXY( 127, $y_point );
                $pdf->MultiCell(9.5, 5, U2T($date_to_text), $border, "C"); //วันที่
                $pdf->SetXY( 145, $y_point );
                $pdf->MultiCell(30, 5, U2T($month2text), $border, "C"); //วันที่
                $pdf->SetXY( 180.5, $y_point );
                $pdf->MultiCell(15, 5, U2T($date_to_year), $border, "C"); //วันที่
                $y_point = 91.4;
                $pdf->SetXY( 61, $y_point );
                $pdf->MultiCell(134.5, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                $y_point = 99.1;
                $pdf->SetXY( 140, $y_point );
                $pdf->MultiCell(50, 5, U2T(number_format($data['loan_amount'])), $border, "C"); //เงินกู้
                $y_point = 106.8;
                $pdf->SetXY( 26, $y_point );
                $pdf->MultiCell(97, 5, U2T($money_loan_amount_2text), $border, "C"); //จำนวนเงิน(ตัวอักษร)
                $y_point = 114.5;
                $pdf->SetXY( 45, $y_point );
                $pdf->MultiCell(44, 5, U2T($full_date), $border, "C"); //วันที่กู้
                $y_point = 129.5;
                $pdf->SetXY( 69.5, $y_point );
                $pdf->MultiCell(37, 5, U2T($full_date), $border, "C"); //ในวันที่กู้
                $pdf->SetXY( 124, $y_point );
                $pdf->MultiCell(65, 5, U2T(number_format($data['loan_amount'])), $border, "C"); //เงินกู้
                $y_point = 137.3;
                $pdf->SetXY( 27, $y_point );
                $pdf->MultiCell(103, 5, U2T($money_loan_amount_2text), $border, "C"); //จำนวนเงิน(ตัวอักษร)
                $y_point = 160;
                $pdf->SetXY( 38, $y_point );
                $pdf->MultiCell(159, 5, U2T(''), $border, "C"); //จำนวนเงิน(ตัวอักษร
                $y_point = 146;
                //$pdf->Image($myImage, 39, $y_point, 3);
                $y_point = 175.5;
                $pdf->SetXY( 38, $y_point );
                $pdf->MultiCell(159, 5, U2T(''), $border, "C"); //จำนวนเงิน(ตัวอักษร
                $y_point = 154.8;
                //$pdf->Image($myImage, 39, $y_point, 3);
                $y_point = 190.5;
                $pdf->SetXY( 38, $y_point );
                //$pdf->MultiCell(57, 5, U2T("เงินฝากประเภท*"), $border, "C"); //เงินฝากประเภท
                $pdf->SetXY( 110.5, $y_point );
                //$pdf->MultiCell(85, 5, U2T("เลขที่บัญชี"), $border, "C"); //เลขที่บัญชี
                $y_point = 169.3;
                //$pdf->Image($myImage, 39, $y_point, 3);
                $y_point = 184.7;
                //$pdf->Image($myImage, 39, $y_point, 3);
                $y_point = 199.5;
                //$pdf->Image($myImage, 39, $y_point, 3);
                $y_point = 264;
                $pdf->SetXY( 81, $y_point );
                $pdf->MultiCell(60, 5, U2T($fullname_th), $border, "C"); //เงินฝากประเภท
            }
    }
	
	//exit;
	$pdf->Output();