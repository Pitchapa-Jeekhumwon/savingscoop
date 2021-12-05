<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

    $filename = $_SERVER["DOCUMENT_ROOT"]."/assets/document/loan/supportting-doc-collateral.pdf" ;
    //$filename = $_SERVER["DOCUMENT_ROOT"]."/fsccoop/assets/document/loan/supportting-doc-collateral.pdf" ;
    
	$pdf = new FPDI();

    $pageCount_1 = $pdf->setSourceFile($filename);
    $myImage = "assets/images/check-mark.png";
    //$data = $loan_fscoop;
    $location  = $profile_location; // location ของสถานที่ ex เขียนที่.....

    $age                = $this->center_function->diff_birthday($data['birthday']); //อายุ
    $monthtext          = $this->center_function->month_arr(); // function แปลงเดือนเป็นตัวอักษร
    $money_loan_amount_2text = $this->center_function->convert($data['loan_amount']); //จำนวนเงินกู็(ตัวอักษร)
    $money_salary_2text = $this->center_function->convert($data['salary']);//เงินเดือน(ตัวอักษร)
    $start_member_year  = $this->center_function->diff_year($data['approve_date'],date('Y-m-d H:i:s')); // ปีที่เริ่มทำงาน (จำนวนปี)
    $start_member_month      = $this->center_function->diff_month_interval($data['approve_date'],date('Y-m-d H:i:s')); // จำนวนเดือน
    if ($data['approve_date'] != ''){
        $date_to_year       = (substr($data['approve_date'], 0, 4))+543; // ปีที่เริ่มทำสัญญา
    }
    $date_to_text       = number_format(substr($data['approve_date'], 8, 2)); // วันที่เริ่มทำสัญญา
    $date_to_month      = number_format(substr($data['approve_date'], 5, 2)); // เดือนที่เริ้มทำสัญญา
    $month2text         = $monthtext[$date_to_month]; // เดือนที่เริ่มทำสัญญา (ตัวอักษร)
    $full_date          = $date_to_text."  ".$month2text."  ".$date_to_year; // วัน:เดือน:ปี ที่เริ่มทำสัญญา
    if ($data['createdatetime'] != ''){
        $create_year    = (substr($data['createdatetime'], 0, 4))+543; // // ปีที่บันทึกข้อมูล
    }
    $create_day = number_format(substr($data['createdatetime'], 8, 2)); // วันที่บันทึกข้อมูล
    $create_month = number_format(substr($data['createdatetime'], 5, 2)); // เดือนที่บันทึกข้อมูล
    $create_month2text = $monthtext[$create_month]; // เดือนที่บันทึกข้อมูล(ตัวอักษร)
    if ($data['createdatetime'] != ''){
        $create_year       = (substr($data['createdatetime'], 0, 4))+543; // // ปีที่บันทึกข้อมูล
    }
    $day_start_period       = number_format(substr($data['date_start_period'], 8, 2));// วันเริ่มจ่ายงวด(หุ้น)
    $month_start_period     = number_format(substr($data['date_start_period'], 5, 2)); // เดือนที่จ่ายค่างวด(หุ้น)
    $year_start_period      = (substr($data['approve_date'], 0, 4))+543; // ปีที่จ่ายค่างวด(หุ้น)
    $full_start_period      = $day_start_period."  ".$month_start_period."  ".$year_start_period; // วัน, เดือน, ปี ที่จ่ายค่างวด(หุ้น)
    $fullname_th            = $data['prename_full'].$data['firstname_th']."  ".$data['lastname_th']; // คำนำหน้าชื่อ , ชื่อ-สกุล (ผู้กู้)
    $contract_number_font   = substr($data['contract_number'], 0, -8); // ตัวอักษรหน้า เลขที่สัญญา ex. ฉฉ999999 = ฉฉ
    $contract_number_back   = substr($data['contract_number'], -9);   //ตัวอักษรหลัง เลขที่สัญญา ex. 999999 = ฉฉ
    $period_amount = $this->center_function->convert($data['period_amount']); //งวด(ตัวอักษร)

	for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {	
        $pdf->AddPage();
            $tplIdx = $pdf->importPage($pageNo);
            $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
            $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
            $pdf->SetFont('THSarabunNew', '', 14 );
            
            // $pdf->SetTitle(U2T('คำขอกู้เงินเพื่อการศึกษา'));
            $border = isset($_GET['show']) && $_GET['show'] == '1' ?  1 : 0;
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetAutoPageBreak(true,0);
            if($pageNo == '1'){
                $y_point = 36.6;
                $pdf->Image($myImage, 23.3, $y_point, 3);
                $y_point = 44.8;
                $pdf->Image($myImage, 23.3, $y_point, 3);
                $y_point = 52;
                $pdf->Image($myImage, 23.3, $y_point, 3);
                if(!empty($data['marry_name'])) {
                $y_point = 59.4;
                $pdf->Image($myImage, 23.3, $y_point, 3);//สำเน่ทะเบียนสมรสของผู้กู้
                }
                $y_point = 66.4;
                $pdf->Image($myImage, 23.3, $y_point, 3);
                $y_point = 74.8;
                $pdf->Image($myImage, 23.3, $y_point, 3);
                $y_point = 82.3;
                $pdf->Image($myImage, 23.3, $y_point, 3);
                $y_point = 89.3;
                $pdf->Image($myImage, 23.3, $y_point, 3);
                $y_point = 98;
                //$pdf->Image($myImage, 39.3, $y_point, 3);
                $y_point = 105;
                //$pdf->Image($myImage, 39.3, $y_point, 3);
                $y_point = 112.5;
                //$pdf->Image($myImage, 39.3, $y_point, 3);
                $y_point = 134.2;
                $pdf->SetXY(124.5, $y_point, 3);
                $pdf->MultiCell(58, 5, U2T($fullname_th), $border, "C");
            }else if($pageNo == '2'){
                    
            }else if($pageNo == '3'){
                $y_point = 31;
                    $pdf->SetXY(31, $y_point, 3);
                    $pdf->MultiCell(13, 5, U2T(''), $border, "C"); // รับที่
                    $pdf->SetXY(45, $y_point, 3);
                    $pdf->MultiCell(13, 5, U2T(''), $border, "C"); // รับที่
                $y_point = 38.7;
                    $pdf->SetXY(31, $y_point, 3);
                    $pdf->MultiCell(8, 5, U2T($date_to_text), $border, "C"); // วันที่(วัน)
                    $pdf->SetXY(40, $y_point, 3);
                    $pdf->MultiCell(10, 5, U2T($month_short_arr[$date_to_month]), $border, "C"); // วันที่(เดือน)
                    $pdf->SetXY(51.3, $y_point, 3);
                    $pdf->MultiCell(9.5, 5, U2T($date_to_year), $border, "C"); // วันที่(ปี)
                $y_point = 30;
                    $pdf->SetFont('THSarabunNew', '', 10 );
                    $pdf->SetXY(166.5, $y_point, 3);
                    $pdf->MultiCell(13, 5, U2T($contract_number_font), $border, "C"); // หนังสือกู้ที่
                    $pdf->SetXY(178, $y_point, 3);
                    $pdf->MultiCell(13, 5, U2T($contract_number_back), $border, "C"); // หนังสือกู้ที่
                    $pdf->SetFont('THSarabunNew', '', 14 );
                $y_point = 38;
                    $pdf->SetFont('THSarabunNew', '', 13 );
                    $pdf->SetXY(155, $y_point, 3);
                    $pdf->MultiCell(13, 5, U2T($date_to_text), $border, "C"); // วันที่(วัน)
                    $pdf->SetXY(167, $y_point, 3);
                    $pdf->MultiCell(14, 5, U2T($month_short_arr[$date_to_month]), $border, "C"); /// วันนที่(เดือน)
                    $pdf->SetXY(180, $y_point, 3);
                    $pdf->MultiCell(11, 5, U2T($date_to_year), $border, "C"); // วันที่(ปี)
                    $pdf->SetFont('THSarabunNew', '', 14 );
                $y_point = 52.0;
                    $pdf->SetXY(132, $y_point, 3);
                    $pdf->MultiCell(63.8, 5, U2T($location['profile_location']['0']['coop_name_th']), $border, "C"); // เขียนที่
                $y_point = 59.7;
                    $pdf->SetXY(132, $y_point, 3);
                    $pdf->MultiCell(63.8, 5, U2T($full_date), $border, "C"); // วันที่
                $y_point = 79;
                    $pdf->SetXY(45, $y_point, 3);
                    $pdf->MultiCell(86, 5, U2T($fullname_th), $border, "C"); // ชื่อผู้กู้
                    $pdf->SetXY(163, $y_point, 3);
                    $pdf->MultiCell(30.7, 5, U2T($data['member_id']), $border, "C"); // สมาชิกเลขที่ทะเบียนที่
                $y_point = 86.5;
                    $pdf->SetXY(57, $y_point, 3);
                    $pdf->MultiCell(42, 5, U2T(number_format($data['salary'])), $border, "C"); // เงินเดือน
                $y_point = 94.2;
                    $pdf->SetXY(78, $y_point, 3);
                    $pdf->MultiCell(28, 5, U2T(number_format($data['loan_amount'])), $border, "C"); // ขอกู้จำนวน
                    $pdf->SetXY(118, $y_point, 3);
                    $pdf->MultiCell(74, 5, U2T($money_loan_amount_2text), $border, "C"); // เงินกู้(ตัวอักษร)
                $y_point = 101.8;
                    $pdf->SetXY(123, $y_point, 3);
                    $pdf->MultiCell(70, 5, U2T($data['loan_reason']), $border, "C"); // โดยจะนำไปใช้เพื่อ...
                $y_point = 109.5;
                    $pdf->SetXY(115, $y_point, 3);
                    $pdf->MultiCell(77, 5, U2T($data['position_name']), $border, "C"); // ข้าพเจ้ารับราชการในตำแหน่ง
                $y_point = 117;
                    $pdf->SetXY(107, $y_point, 3);
                    $pdf->MultiCell(36, 5, U2T($data['id_card']), $border, "C"); // บัตรประจำตัวประชาชน
                    $pdf->SetXY(153, $y_point, 3);
                    $pdf->MultiCell(39, 5, U2T(($data['mem_group_name']=='')?'-':$data['mem_group_name']), $border, "C"); // สังกัด
                $y_point = 124.9;
                    $pdf->SetXY(54.5, $y_point, 3);
                    $pdf->MultiCell(27, 5, U2T($data['c_address_no']), $border, "C"); // บ้านเลขที่ (ปัจจุบัน)
                    $pdf->SetXY(90, $y_point, 3);
                    $pdf->MultiCell(37, 5, U2T(($data['c_address_road']=='')?'-':$data['address_road']), $border, "C"); // ถนน (ปัจจุบัน)
                    $pdf->SetXY(137, $y_point, 3);
                    $pdf->MultiCell(55, 5, U2T(($data['district_name']=='')?'-':$data['district_name']), $border, "C"); // ตำบล (ปัจจุบัน)
                $y_point = 132.4;
                    $pdf->SetXY(30.5, $y_point, 3);
                    $pdf->MultiCell(45, 5, U2T($data['amphur_name']), $border, "C"); // อำเภอ (ปัจจุบัน)
                    $pdf->SetXY(87, $y_point, 3);
                    $pdf->MultiCell(45, 5, U2T($data['province_name']), $border, "C"); // จังหวัด (ปัจจุบัน)
                    $pdf->SetXY(146.5, $y_point, 3);
                    $pdf->MultiCell(45.5, 5, U2T($data['mobile']), $border, "C"); // โทรศัพท์
                    $y_point = 151.9;
                    $pdf->SetXY( 38, $y_point );
                    $pdf->MultiCell(22, 5, U2T(''), $border, "C"); //โฉนดเลขที่
                    $pdf->SetXY( 78, $y_point );
                    $pdf->MultiCell(22, 5, U2T(''), $border, "C"); //ตำบล, แขวง
                $y_point = 158.9;
                    $pdf->SetXY( 36, $y_point );
                    $pdf->MultiCell(26.5, 5, U2T(''), $border, "C"); //อำเภอ, เขต
                    $pdf->SetXY( 74, $y_point );
                    $pdf->MultiCell(23, 5, U2T(''), $border, "C"); //จังหวัด
                $y_point = 165.9;
                    $pdf->SetXY( 46.5, $y_point );
                    $pdf->MultiCell(10.5, 5, U2T(''), $border, "C"); //เนื้อที่โดยประมาณ(ไร่)
                    $pdf->SetXY( 64, $y_point );
                    $pdf->MultiCell(10, 5, U2T(''), $border, "C"); //เนื้อที่โดยประมาณ (งาน)
                    $pdf->SetXY( 79, $y_point );
                    $pdf->MultiCell(12.3, 5, U2T(''), $border, "C"); //เนื้อที่โดยประมาณ (วา)
                $y_point = 173;
                    $pdf->SetXY( 35.5, $y_point );
                    $pdf->MultiCell(21.5, 5, U2T(''), $border, "C"); //อาคารแบบ
                    $pdf->SetXY( 66.5, $y_point );
                    $pdf->MultiCell(26, 5, U2T(''), $border, "C"); //เลขที่
                $y_point = 151.5;
                    $pdf->SetXY( 122.5, $y_point );
                    $pdf->MultiCell(15, 5, U2T(''), $border, "C"); //ห้องชุดเลขที่
                    $pdf->SetXY( 148.9, $y_point );
                    $pdf->MultiCell(10, 5, U2T(''), $border, "C"); //ชั้น
                    $pdf->SetXY( 165, $y_point );
                    $pdf->MultiCell(11.5, 5, U2T(''), $border, "C"); //เนื้อที่.....ตรม
                $y_point = 158.9;
                    $pdf->SetXY( 122.5, $y_point );
                    $pdf->MultiCell(61, 5, U2T(''), $border, "C"); //ชื่ออาคาร, ชุด
                $y_point = 165.9;
                    $pdf->SetXY( 122.5, $y_point );
                    $pdf->MultiCell(18.5, 5, U2T(''), $border, "C"); //ห้องชุดแบบ
                    $pdf->SetXY( 159, $y_point );
                    $pdf->MultiCell(26, 5, U2T(''), $border, "C"); //ตำบล, แขวง
                $y_point = 173;
                    $pdf->SetXY( 121, $y_point );
                    $pdf->MultiCell(25.5, 5, U2T(''), $border, "C"); //อำเภอ, เขต
                    $pdf->SetXY( 159.5, $y_point );
                    $pdf->MultiCell(26, 5, U2T(''), $border, "C"); //จังหวัด
                $y_point = 202.1;
                    //$pdf->Image($myImage, 42.2, $y_point, 3);
                $y_point = 199.7;
                    $pdf->SetXY( 87, $y_point );
                    $pdf->MultiCell(35, 5, U2T($data['money_per_period']), $border, "C"); // ต้นเงินเท่ากันทุกงวด งวดละ.....(บาท)
                    $pdf->SetXY( 164, $y_point );
                    $pdf->MultiCell(18, 5, U2T($data['period_amount']), $border, "C"); //พร้อมดอกเบี้ยจำนวน....... (งวด)
                $y_point = 207.5;
                    $pdf->SetXY( 104, $y_point );
                    $pdf->MultiCell(40, 5, U2T($data['money_per_period']), $border, "C"); //เงินต้นและดอกเบี้ยเท่ากันทุกงวด(งวดละ)
                    $pdf->SetXY( 164, $y_point );
                    $pdf->MultiCell(18, 5, U2T($data['period_amount']), $border, "C"); //จำนวน(งวด)
                $y_point = 208.9;
                    //$pdf->Image($myImage, 42.2, $y_point, 3);
                $y_point = 241;
                $pdf->SetXY(110, $y_point);
                $pdf->SetFont('THSarabunNew', '', 13);
                $pdf->MultiCell(100, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                $y_point = 276.5;
                $pdf->SetXY(107, $y_point);
                $pdf->MultiCell(100, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                    $y_point = 220;
            }else if($pageNo == '4'){
                $y_point = 21;
                    $pdf->SetXY(128, $y_point);
                    $pdf->MultiCell(43, 5, U2T($create_day." ".$create_month2text." ".$create_year), $border, "C"); //วันที่
                $y_point = 45.7;
                    //$pdf->Image($myImage, 64.9, $y_point, 3);//ความมุ่งหมายและเหตุผลแห่งเงินกู้(จริง)
                    //$pdf->Image($myImage, 103.2, $y_point, 3);//ความมุ่งหมายและเหตุผลแห่งเงินกู้(ไม่จริง)
                    $y_point = 60.2;
                    //$pdf->Image($myImage, 64.9, $y_point, 3); //ผู้ขอกู้มีพฤติการณ์อาจถูกออกจากงาน (มี)
                    //$pdf->Image($myImage, 103.2, $y_point, 3);//ผู้ขอกู้มีพฤติการณ์อาจถูกออกจากงาน(ไม่มี)
                $y_point = 76.2;
                    //$pdf->Image($myImage, 64.9, $y_point, 3); //ผู้ขอกู้มีหนี้สินภายนอกเป็นจำนวนมาก (มี)
                    //$pdf->Image($myImage, 103.8, $y_point, 3);//ผู้ขอกู้มีหนี้สินภายนอกเป็นจำนวนมาก(ไม่มี)
                $y_point = 125.5;
                    $pdf->SetXY(88.5, $y_point);
                    $pdf->MultiCell(63, 5, U2T(number_format($data['loan_amount'])), $border, "C"); //อำเภอ, เขต
                $y_point = 153;
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->SetXY( 19, $y_point );
                $pdf->MultiCell(15 , 5, U2T(number_format($data['salary'])), $border, "C");//เงินได้รายเดือน
                $pdf->SetFont('THSarabunNew', '', 9);
                $pdf->SetXY( 34, $y_point );
                $pdf->MultiCell(12.5 , 5, U2T(number_format($share_group['share_collect_value'])), $border, "C");//เงินค่าหุ้น(บาท)
                $pdf->SetFont('THSarabunNew', '', 9 );
                $pdf->SetXY( 46.5, $y_point );
                $pdf->MultiCell(16.5 , 5, U2T(number_format($loan['credit_limit'])), $border, "C");//จำกัดวงเงินกู้(บาท)
                $pdf->SetFont('THSarabunNew', '', 9 );
                $pdf->SetXY( 62.5, $y_point );
                $pdf->MultiCell(15.5 , 5, U2T($contract['data'][0]['contract_number']), $border, "C");// น/สกู้ (สามัญ)
                $pdf->SetFont('THSarabunNew', '', 9 );
                $pdf->SetXY( 78.5, $y_point );
                $pdf->MultiCell(16.5 , 5, U2T(number_format($contract['data'][0]['loan_amount_balance'])), $border, "C");// จำนวนเงิน (สามัญ)
                $pdf->SetFont('THSarabunNew', '', 9 );
                $pdf->SetXY( 95, $y_point );
                $pdf->MultiCell(15 , 5, U2T($loan['contract_number']), $border, "C");// น/สกู้(เพื่อเหตุฉุกเฉิน)
                $pdf->SetFont('THSarabunNew', '', 9 );
                $pdf->SetXY( 110, $y_point );
                $pdf->MultiCell(17.5 , 5, U2T(number_format($loan['loan_amount_balance'])), $border, "C");// จำนวนเงิน
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 128, $y_point );
                $pdf->MultiCell(14.5 , 5, U2T($data['']."*"), $border, "C");// จำนวนเงิน
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 143, $y_point );
                $pdf->MultiCell(17.5 , 5, U2T($data['']."*"), $border, "C");// จำนวนเงิน
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 159.5, $y_point );
                $pdf->MultiCell(12.8 , 5, U2T($data['']."*"), $border, "C");// จำนวนเงิน
                $pdf->SetFont('THSarabunNew', '', 8 );
                $pdf->SetXY( 173, $y_point );
                $pdf->MultiCell(24 , 5, U2T($data['']."*"), $border, "C");// จำนวนเงิน
                $y_point = 164;
                    //$pdf->Image($myImage, 156 , $y_point, 3); //เคยผิดนัดการส่งเงินชำระหนี้(เคย)
                    //$pdf->Image($myImage, 169 , $y_point, 3); //เคยผิดนัดการส่งเงินชำระหนี้
                $y_point = 171.3;
                    $pdf->SetXY(64.2, $y_point);
                    $pdf->MultiCell(130, 5, U2T(''), $border, "C"); //ข้อชี้แจงอื่นๆ
                $y_point = 178.8;
                    $pdf->SetXY(61, $y_point);
                    $pdf->MultiCell(130, 5, U2T(''), $border, "C"); //ข้อชี้แจงอื่นๆ

            }else if($pageNo == '5'){
                $y_point = 40;
                    $pdf->SetXY(158.8, $y_point);
                    $pdf->MultiCell(17, 5, U2T($contract_number_font), $border, "C"); //ที่
                    $pdf->SetXY(178.8, $y_point);
                    $pdf->MultiCell(17, 5, U2T($contract_number_back), $border, "C"); //ที่
                $y_point = 47.2;
                    $pdf->SetXY(156.8, $y_point);
                    $pdf->MultiCell(39, 5, U2T($create_day." ".$create_month2text." ".$create_year), $border, "C"); //วันที่
                $y_point = 55.1;
                    $pdf->SetXY(42.8, $y_point);
                    $pdf->MultiCell(150, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                $y_point = 62.3;
                    $pdf->SetXY(43.3, $y_point);
                    $pdf->MultiCell(150, 5, U2T($fullname_th), $border, "C"); //ชื่อค้ำ
                $y_point = 69.6;
                    $pdf->SetXY(154.8, $y_point);
                    $pdf->MultiCell(38, 5, U2T($data['member_id']), $border, "C"); //สมาชิก
                $y_point = 77.6;
                    $pdf->SetXY(80.8, $y_point);
                    $pdf->MultiCell(55, 5, U2T($data['position_name']), $border, "C"); //ตำแหน่ง
                $y_point = 85.5;
                    $pdf->SetXY(45, $y_point);
                    $pdf->MultiCell(35, 5, U2T($data['id_card']), $border, "C"); //รหัสบัตร ปชช
                    $pdf->SetXY(92, $y_point);
                    $pdf->MultiCell(42, 5, U2T($data['mem_group_name']), $border, "C"); //สังกัด
                    $pdf->SetXY(170, $y_point);
                    $pdf->MultiCell(23, 5, U2T($data['c_address_no']), $border, "C"); //บ้านเลขที่(ปัจจุบัน)
                $y_point = 93.2;
                    $pdf->SetXY(27.5, $y_point);
                    $pdf->MultiCell(33, 5, U2T(($data['c_address_road']=='')?'-':$data['address_road']), $border, "C"); // ถนน(ปัจจุบัน)
                    $pdf->SetXY(70, $y_point);
                    $pdf->MultiCell(35, 5, U2T(($data['district_name']=='')?'-':$data['district_name']), $border, "C"); //ตำบล(ปัจจุบัน)
                    $pdf->SetXY(114, $y_point);
                    $pdf->MultiCell(35, 5, U2T($data['amphur_name']), $border, "C"); //อำเภอ(ปัจจุบัน)
                    $pdf->SetXY(160, $y_point);
                    $pdf->MultiCell(33, 5, U2T($data['province_name']), $border, "C"); //จังหวัด(ปัจจุบัน)
                $y_point = 100.9;
                    $pdf->SetXY(34.5, $y_point);
                    $pdf->MultiCell(57.5, 5, U2T($data['mobile']), $border, "C"); //โทรศัพท์
                $y_point = 116;
                    $pdf->SetXY(101, $y_point);
                    $pdf->MultiCell(23, 5, U2T($data['loan_amount']), $border, "C"); //จำนวนเงินกู้
                    $pdf->SetXY(135, $y_point);
                    $pdf->MultiCell(55, 5, U2T($money_loan_amount_2text), $border, "C"); //จำนวนเงินกู้(อักษร)
                $y_point = 140;
                    //$pdf->Image($myImage, 41.9, $y_point, 3);
                $y_point = 139;
                    $pdf->SetXY(104, $y_point);
                    $pdf->MultiCell(63, 5, U2T($this->center_function->convert($data['money_per_period'])), $border, "C"); //ต้นเงินเท่ากันทุกงวดที่
                $y_point = 148;
                    //$pdf->Image($myImage, 41.9, $y_point, 3);
                $y_point = 146.9;
                    $pdf->SetXY(122, $y_point);
                    $pdf->MultiCell(64, 5, U2T($this->center_function->convert($data['money_per_period'])), $border, "C"); //ที่
                $y_point = 154.8;
                    $pdf->SetXY(48, $y_point);
                    $pdf->MultiCell(30, 5, U2T(substr($period_amount,0,-3*7)), $border, "C"); //จำนวน, งวด (ตัวอักษร)
                    $pdf->SetXY(154.5, $y_point);
                    $pdf->MultiCell(30, 5, U2T($this->center_function->convert($data['period_amount'])), $border, "C"); //อัตราร้อยละ
                $y_point = 162.5;
                    $pdf->SetXY(59, $y_point);
                    $pdf->MultiCell(31, 5, U2T($month2text), $border, "C"); //ตั้งแต่งวดประจำเดือน
            }else if ($pageNo == '6'){
                $y_point = 112.7;
                    $pdf->SetXY(110, $y_point);
                    $pdf->MultiCell(34, 5, U2T(''), $border, "C"); //ข้าพเจ้าตกลงนำอสังหาริมทรัพย์
                    $pdf->SetXY(165, $y_point);
                    $pdf->MultiCell(31, 5, U2T(''), $border, "C"); //ตำบล(อสังหา)
                $y_point = 120;
                    $pdf->SetXY(37, $y_point);
                    $pdf->MultiCell(26, 5, U2T(''), $border, "C"); //อำเภอ(อสังหา)
                    $pdf->SetXY(77, $y_point);
                    $pdf->MultiCell(26, 5, U2T(''), $border, "C"); //จังหวัด (อสังหา)
                $y_point = 227;
                    $pdf->SetXY(33, $y_point);
                    $pdf->MultiCell(80, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                    $pdf->SetXY(149, $y_point);
                    $pdf->MultiCell(38, 5, U2T(number_format($data['loan_amount'])), $border, "C"); //เงินกู้จำนวน..
                $y_point = 234.5;
                    $pdf->SetXY(21, $y_point);
                    $pdf->MultiCell(80, 5, U2T($this->center_function->convert($data['loan_amount'])), $border, "C"); //เงินกู้จำนวน(ตัวอักษร)
                $y_point = 187;
                $pdf->SetXY(39, $y_point);
                    $pdf->MultiCell(64, 5, U2T($fullname_th), $border, "C"); // ชื่อ(ลายเซ็น)
                    $y_point = 177.5;
                $pdf->SetXY(119, $y_point);
                $pdf->SetFont('THSarabunNew', '', 12 );
                    $pdf->MultiCell(60, 5, U2T($fullname_th), $border, "C"); // ชื่อ (ลายเซ็น)
                $pdf->SetFont('THSarabunNew', '', 12 );
                    $y_point = 254;
                    $pdf->SetXY(86, $y_point);
                    $pdf->MultiCell(60, 5, U2T($fullname_th), $border, "C"); //ชื่อ (ลายเซ็น)
            }else if($pageNo == '7'){

            }else if($pageNo == '8'){

            }else if($pageNo == '9' ){

            }else if($pageNo == '10'){
                $y_point = 47;
                $pdf->SetXY(134.5, $y_point);
                 $pdf->MultiCell(60, 5, U2T($location['profile_location']['0']['coop_name_th']), $border, "C"); // เขียนที่
                $y_point = 54.4;
                $pdf->SetXY(120, $y_point);
                $pdf->MultiCell(15, 5, U2T($create_day), $border, "C"); // วันที่(วัน)
                $pdf->SetXY(142.5, $y_point);
                $pdf->MultiCell(28, 5, U2T($create_month2text), $border, "C"); //วันที่(เดือน)
                $pdf->SetXY(176.5, $y_point);
                $pdf->MultiCell(18, 5, U2T($create_year), $border, "C"); // วันที่(ปี)
                $y_point = 61.8;
                $pdf->SetXY( 43.5, $y_point );
                $pdf->MultiCell(58, 5, U2T($fullname_th), $border, "C"); //ชื่อผู้กู้
                $pdf->SetXY( 135.5, $y_point );
                $pdf->MultiCell(40, 5, U2T($data['id_card']), $border, "C"); //หมายเลขบัตรประชาชน
                $pdf->SetXY( 181.5, $y_point );
                $pdf->MultiCell(10, 5, U2T($age), $border, "C"); //อายุ
                $y_point = 69.8;
                $pdf->SetXY( 50.5, $y_point );
                $pdf->MultiCell(15, 5, U2T($data['c_address_no']), $border, "C"); //
                $pdf->SetXY( 71.5, $y_point );
                $pdf->MultiCell(15, 5, U2T($data['c_address_moo']), $border, "C"); //
                $pdf->SetXY( 102.5, $y_point );
                $pdf->MultiCell(35, 5, U2T($data['c_address_soi']), $border, "C"); //
                $pdf->SetXY( 152.5, $y_point );
                $pdf->MultiCell(38, 5, U2T($data['c_address_road']), $border, "C"); //
                $y_point = 77.3;
                $pdf->SetXY( 39.5, $y_point );
                $pdf->MultiCell(44, 5, U2T($data['distric_name']), $border, "C"); //
                $pdf->SetXY( 98, $y_point );
                $pdf->MultiCell(38, 5, U2T($data['amphur_name']), $border, "C"); //
                $pdf->SetXY( 152, $y_point );
                $pdf->MultiCell(40, 5, U2T($data['province_name']), $border, "C"); //
                $y_point = 84.8;
                $pdf->SetXY( 47, $y_point );
                $pdf->MultiCell(38, 5, U2T($data['tel']), $border, "C"); //
                $pdf->SetXY( 96, $y_point );
                $pdf->MultiCell(38, 5, U2T($data['mobile']), $border, "C"); //
                $pdf->SetXY( 148, $y_point );
                $pdf->MultiCell(45, 5, U2T($data['position_name']), $border, "C"); //
                $y_point = 92.5;
                $pdf->SetXY( 29, $y_point );
                $pdf->MultiCell(33.5, 5, U2T(''), $border, "C"); //
                $pdf->SetXY( 101.5, $y_point );
                $pdf->MultiCell(21, 5, U2T(number_format($data['salary'])), $border, "C"); //
                $pdf->SetXY( 130.5, $y_point );
                $pdf->MultiCell(60, 5, U2T($this->center_function->convert($data['salary'])), $border, "C"); //
                $y_point = 100.2;
                $pdf->SetXY( 150.5, $y_point );
                $pdf->MultiCell(33, 5, U2T($data['member_id']), $border, "C"); //
                $y_point = 123.2;
                $pdf->SetXY( 73.5, $y_point );
                $pdf->MultiCell(30, 5, U2T($data['contract_number']), $border, "C"); //
                $pdf->SetXY( 140, $y_point );
                $pdf->MultiCell(30, 5, U2T($data['petition_number']), $border, "C"); //
                $y_point = 249.5;
                $pdf->SetXY( 77, $y_point );
                $pdf->SetFont('THSarabunNew', '', 13 );
                $pdf->MultiCell(60, 5, U2T($fullname_th), $border, "C"); //
                // $y_point = 78.5;
                // $pdf->SetXY( 49, $y_point );
                // $pdf->MultiCell(38, 5, U2T('044-313XXX'), $border, "C"); ///
                // $pdf->SetXY( 100, $y_point );
                // $pdf->MultiCell(38, 5, U2T($mobile), $border, "C"); //
                // $pdf->SetXY( 154, $y_point );
                // $pdf->MultiCell(38, 5, U2T($position), $border, "C"); //s
                    
            }else if($pageNo == '11'){

            }else if($pageNo == '12'){
                if(!empty($data['marry_name'])) {
                    $y_point = 76.9;
                    $pdf->SetXY(127, $y_point);
                    $pdf->MultiCell(11, 5, U2T($create_day), $border, "C"); //
                    $pdf->SetXY(145.5, $y_point);
                    $pdf->MultiCell(28, 5, U2T($create_month2text), $border, "C"); //
                    $pdf->SetXY(180.5, $y_point);
                    $pdf->MultiCell(15, 5, U2T($create_year), $border, "C"); //
                    $y_point = 92;
                    $pdf->SetXY(45, $y_point);
                    $pdf->MultiCell(105, 5, U2T($data['marry_name']), $border, "C"); //
                    $pdf->SetXY(157, $y_point);
                    $pdf->MultiCell(30, 5, U2T($age), $border, "C"); //
                    $y_point = 99.2;
                    $pdf->SetXY(39, $y_point);
                    $pdf->MultiCell(24.5, 5, U2T($data['c_address_no']), $border, "C"); //
                    $pdf->SetXY(70, $y_point);
                    $pdf->MultiCell(17, 5, U2T($data['c_address_moo']), $border, "C"); //
                    $pdf->SetXY(104, $y_point);
                    $pdf->MultiCell(37.5, 5, U2T($data['c_address_soi']), $border, "C"); //
                    $pdf->SetXY(150, $y_point);
                    $pdf->MultiCell(40, 5, U2T($data['c_address_road']), $border, "C"); //
                    $y_point = 107.3;
                    $pdf->SetXY(39, $y_point);
                    $pdf->MultiCell(48, 5, U2T($data['district_name']), $border, "C"); //
                    $pdf->SetXY(104, $y_point);
                    $pdf->MultiCell(37.5, 5, U2T($data['amphur_name']), $border, "C"); //
                    $pdf->SetXY(152.5, $y_point);
                    $pdf->MultiCell(38, 5, U2T($data['district_name']), $border, "C"); //
                    $y_point = 115;
                    $pdf->SetXY(53.5, $y_point);
                    $pdf->MultiCell(103, 5, U2T($fullname_th), $border, "C"); //
                    $y_point = 161;
                    $pdf->SetXY(85.5, $y_point);
                    $pdf->MultiCell(55, 5, U2T($data['marry_name']), $border, "C"); //
                    $y_point = 215.5;
                    $pdf->SetXY(44.5, $y_point);
                    $pdf->MultiCell(123, 5, U2T($fullname_th), $border, "C"); //
                    $y_point = 223;
                    $pdf->SetXY(47, $y_point);
                    $pdf->MultiCell(98, 5, U2T($data['marry_name']), $border, "C"); //
                }
            }
        
    }
	//exit;
    $pdf->Output();
    