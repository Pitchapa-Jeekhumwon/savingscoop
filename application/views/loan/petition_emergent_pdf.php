<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

	$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/petition_emergent_pdf.pdf" ;
	//echo $filename;exit;
	
	$pdf = new FPDI();
	
	$pageCount_1 = $pdf->setSourceFile($filename);
	for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {	
	$pdf->AddPage();
		$tplIdx = $pdf->importPage($pageNo); 
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
		
		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->SetFont('THSarabunNew', '', 13 );
		
		$border = 0;
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);

	$borrower_name = "ชื่อผู้กู้";
	$date = "วันที่";
	$borrower_member_id = "รหัสสมาชิกของผู้กู้";
	$peper_number = "เลขที่หนังสือกู้";
	$borrower_tel = "เบอร์โทรศัพท์บ้าน";
	$borrower_mobile = "เบอร์โทรศัพท์มือถือของผู้กู้";
	$borrower_salary = "เงินเดือนของผู้กู้";
	$borrower_age = "อายุของผู้กู้";
	$borrower_id_card = $data['id_card'];//เลขประจำตัวบัตรประชาชน
	$borrow_money = "จำนวนเงินกู้";
	$borrow_moneyth = "จำนวนเงินกู้ภาษาเขียน";
	$borrow_because = "เหตุผลในการกู้ยืม";
	$borrower_district = "-";
	$borrower_amphur = "-";
	$borrower_province = "-";
	$borrower_address_no = "-";
	$borrower_address_village = "-";
	$borrower_address_road = "-";
	$borrower_address_soi = "-";
	$borrower_address_moo = "-";
	$borrower_rank = "-";
	$borrower_group = "-";
	$borrower_department = "-";
	$borrower_zipcode = "60000";
	$borrower_birthday_day = "07";
	$borrower_birthday_month = "กรกฎาคม";
	$borrower_birthday_years = "2477";
	$borrower_period_balance = "4";
	$borrower_division = "-";
	$borrower_faction = "-";
	$borrower_member_agey = "00";//อายุของทะเบียนสมาชิก(ปี)
	$borrower_member_agem = "00";//อายุของทะเบียนสมาชิก(เดือน)
	$borrower_deposit_type = "ประเภทเงินฝาก";//ประเภทเงินฝาก 
	$borrower_bank_acc_num = "-";//เลขบัญชีธนาคาร
	$borrower_other_income =  "-";//รายได้จากที่อื่น
	$borrower_email = "-";//อีเมล์
	$borrower_baby = "บุตร";//จำนวนบุตร
	$borrower_spouse = "ชื่อ-นามสกุล คู่สมรส";
	$borrower_spouse_age = "อายุ";
	$borrower_career = "อาชีพ";
	$borrower_rate_salary = "อัตราเงินเดือน";
	$percen_y = "00.00";//ดอกเบี้ยร้อยละ
	$amount_m = "000";//จำนวนงวด
	$installment_m = "ค่างวด";//ค่างวดรายเดือน
	$installment_m_th ="ค่างวดภาษาเขียน";
	$lastinstallment_m ="ค่างวดสุดท้าย";
	$lastinstallment_m_th ="ค่างวดสุดท้ายภาษาเขียน";
	$begin_pay = "เริ่มจ่ายวันที่";
	$agent_name = "ชื่อตัวแทนรับเงิน";	
	$agent_member_id = "000000";
	$agent_position = "--- ตำแหน่ง ---";
	$agent_under = "--- สังกัด ---";
	$witness_name = "---- ชื่อพยาน ----";

    $data['tel'] = str_replace('-', '', $data['tel']);
    $dmp_tel = explode(' ',$data['tel']);
    $data['tel'] = $dmp_tel[0];

	// -------------------------------------------------------
	

	
		
		$pay_type = array('cash'=>'เงินสด', 'cheque'=>'เช็คธนาคาร', 'transfer'=>'เงินโอน');
		if($pageNo == '1'){
			$y_point = 19;
			$pdf->SetXY( 172, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['contract_number']), $border, 1);
			
			$y_point = 26;
			$pdf->SetXY( 160, $y_point );
			$pdf->MultiCell(40, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 1);
			
			$y_point = 52;
			$pdf->SetXY( 152, $y_point );
			$pdf->MultiCell(40, 5, U2T(''), $border, 1);
			
			
			$y_point = 84;
			$pdf->SetXY( 50, $y_point );
			$pdf->MultiCell(85, 5, U2T($data['prename_short'].$data['firstname_th']." ".$data['lastname_th']), $border, 1);
			
			$y_point = 84;
			$pdf->SetXY( 139, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['member_id']), $border, 1);
			$y_point = 91;
			$pdf->SetXY( 25, $y_point );
			$pdf->MultiCell(40, 5, U2T($borrower_id_card), $border, 1);
			$y_point = 91;
			$pdf->SetXY( 74, $y_point );
			$pdf->MultiCell(11, 5, U2T($this->center_function->cal_age($data['birthday'])), $border, 'C');
			
			$y_point = 91;
			$pdf->SetXY( 100, $y_point );
			$pdf->MultiCell(40, 5, U2T($borrower_birthday_day), $border, 1);
			$pdf->SetXY( 120, $y_point );
			$pdf->MultiCell(40, 5, U2T($borrower_birthday_month), $border, 1);
			$pdf->SetXY( 146, $y_point );
			$pdf->MultiCell(40, 5, U2T($borrower_birthday_years), $border, 1);

			// $y_point = 97;
			// $pdf->SetXY( 78, $y_point );
			// $pdf->MultiCell(52, 5, U2T($data['position']), $border, 1);
			$y_point = 98;
			$pdf->SetXY( 76, $y_point );
			$pdf->MultiCell(60, 5, U2T($data['mem_group_name']), $border, 1);
			
			$y_point = 98;
			$pdf->SetXY( 9, $y_point );
			$pdf->MultiCell(42, 5, U2T(num_format($data['salary'])), $border, 'R');
			
			$y_point = 98;
			$pdf->SetXY( 95, $y_point );
			$pdf->MultiCell(42, 5, U2T($borrower_group), $border, 'R');

			$y_point = 98;
			$pdf->SetXY( 145, $y_point );
			$pdf->MultiCell(42, 5, U2T($borrower_department), $border, 'R');

			$y_point = 104;
			$pdf->SetXY( 1, $y_point );
			$pdf->MultiCell(42, 5, U2T($borrower_division), $border, 'R');

			$y_point = 104;
			$pdf->SetXY( 55, $y_point );
			$pdf->MultiCell(42, 5, U2T($borrower_faction), $border, 'R');
			
			$y_point = 118;
			$pdf->SetXY( 41, $y_point );
			$pdf->MultiCell(20, 5, U2T($borrower_address_no), $border, 1);
			$pdf->SetXY( 59, $y_point );
			$pdf->MultiCell(12, 5, U2T($borrower_address_moo), $border, 1);
			$pdf->SetXY( 82, $y_point );
			$pdf->MultiCell(12, 5, U2T($borrower_address_soi), $border, 1);
			$pdf->SetXY( 120, $y_point );
			$pdf->MultiCell(12, 5, U2T($borrower_address_road), $border, 1);
			$pdf->SetXY( 171, $y_point );
			$pdf->MultiCell(12, 5, U2T($borrower_district), $border, 1);
			$y_point = 125;
			$pdf->SetXY( 30, $y_point );
			$pdf->MultiCell(12, 5, U2T($borrower_amphur), $border, 1);
			$pdf->SetXY( 74, $y_point );
			$pdf->MultiCell(12, 5, U2T($borrower_province), $border, 1);
			$pdf->SetXY( 127, $y_point );
			$pdf->MultiCell(22, 5, U2T($borrower_zipcode), $border, 1);
			$pdf->SetXY( 162, $y_point );
			$pdf->MultiCell(40, 5, U2T($borrower_tel), $border, 1);
			// $pdf->SetXY( 96, $y_point );
			// $pdf->MultiCell(43, 5, U2T($data['district_name']), $border, 1);
			// $pdf->SetXY( 148, $y_point );
			// $pdf->MultiCell(38, 5, U2T($data['amphur_name']), $border, 1);

			$y_point = 138;
			$pdf->SetXY( 41, $y_point );
			$pdf->MultiCell(20, 5, U2T($borrower_address_no), $border, 1);
			$pdf->SetXY( 59, $y_point );
			$pdf->MultiCell(12, 5, U2T($borrower_address_moo), $border, 1);
			$pdf->SetXY( 82, $y_point );
			$pdf->MultiCell(12, 5, U2T($borrower_address_soi), $border, 1);
			$pdf->SetXY( 120, $y_point );
			$pdf->MultiCell(12, 5, U2T($borrower_address_road), $border, 1);
			$pdf->SetXY( 171, $y_point );
			$pdf->MultiCell(12, 5, U2T($borrower_district), $border, 1);
			$y_point = 145;
			$pdf->SetXY( 30, $y_point );
			$pdf->MultiCell(12, 5, U2T($borrower_amphur), $border, 1);
			$pdf->SetXY( 74, $y_point );
			$pdf->MultiCell(12, 5, U2T($borrower_province), $border, 1);
			$pdf->SetXY( 120, $y_point );
			$pdf->MultiCell(22, 5, U2T($borrower_zipcode), $border, 1);
			$pdf->SetXY( 162, $y_point );
			$pdf->MultiCell(40, 5, U2T($borrower_tel), $border, 1);

			$y_point = 152;
			$pdf->SetXY( 98, $y_point );
			$pdf->MultiCell(20, 5, U2T($borrower_baby), $border, 1);
			$pdf->SetXY( 126, $y_point );
			$pdf->MultiCell(30, 5, U2T($borrower_spouse), $border, 1);
			$pdf->SetXY( 186, $y_point );
			$pdf->MultiCell(20, 5, U2T($borrower_spouse_age), $border, 1);
			
			$y_point = 158;
			$pdf->SetXY( 25, $y_point );
			$pdf->MultiCell(20, 5, U2T($borrower_career), $border, 1);
			$pdf->SetXY( 74, $y_point );
			$pdf->MultiCell(20, 5, U2T($borrower_rate_salary), $border, 1);
			$pdf->SetXY( 170, $y_point );
			$pdf->MultiCell(40, 5, U2T($borrower_tel), $border, 1);
			
			// $y_point = 104;
			// $pdf->SetXY( 25, $y_point );
			// $pdf->MultiCell(38, 5, U2T($data['province_name']), $border, 1);
			// $pdf->SetXY( 80, $y_point );
			// $pdf->MultiCell(20, 5, U2T($data['c_zipcode']), $border, 1);
			$y_point = 104;
			$pdf->SetXY( 148, $y_point );
			$pdf->MultiCell(30, 5, U2T($data['tel']), $border, 1);
			// $pdf->SetXY( 161, $y_point );
			// $pdf->MultiCell(30, 5, U2T($data['mobile']), $border, 1);
			
			
			
			$y_point = 178.5;
			$pdf->SetXY( 76, $y_point );
			$pdf->MultiCell(44, 5, U2T(num_format($data['loan_amount'])), $border, 'R');
			$pdf->SetXY( 130, $y_point );
			$pdf->MultiCell(58, 5, U2T($this->center_function->convert($data['loan_amount'])), $border, 'C');
			
			$y_point = 185.3;
			$pdf->SetXY( 50, $y_point );
			$pdf->MultiCell(80, 5, U2T($borrow_because), $border, 1);
			
			$y_point = 198.8;
			$pdf->SetXY(95, $y_point );
			$pdf->MultiCell(80, 5, U2T($percen_y), $border, 1);
			
			$y_point = 212.5;
			$pdf->SetXY(148, $y_point );
			$pdf->MultiCell(80, 5, U2T($amount_m), $border, 1);

			$y_point = 212.5;
			$pdf->SetXY(173, $y_point );
			$pdf->MultiCell(80, 5, U2T($installment_m ), $border, 1);

			$y_point = 219;
			$pdf->SetXY(30, $y_point );
			$pdf->MultiCell(80, 5, U2T($installment_m_th), $border, 1);
			$pdf->SetXY(100 ,$y_point );
			$pdf->MultiCell(80, 5, U2T($lastinstallment_m), $border, 1);
			$pdf->SetXY(140,$y_point );
			$pdf->MultiCell(80, 5, U2T($lastinstallment_m_th), $border, 1);
			

			$y_point = 225.7;
			$pdf->SetXY(55, $y_point );
			$pdf->MultiCell(80, 5, U2T($begin_pay), $border, 1);
			//ปี
			// $y_point = 140;
			// $pdf->SetXY( 71, $y_point );
			// $pdf->MultiCell(16, 5, U2T($this->center_function->ConvertToLastOfMonth($data_period_1['date_period'],1,0,0)), $border, 1);
			// $pdf->SetXY( 91, $y_point );
			// $pdf->MultiCell(16, 5, U2T($this->center_function->ConvertToLastOfMonth($data_period_last['date_period'],1,0,0)), $border, 1);
			// $y_point = 150;
			// $pdf->SetXY( 115, $y_point );
			// $pdf->MultiCell(18, 5, U2T(num_format($data_period_1['principal_payment'])), $border, 'R');
			// $y_point = 160;
			// $pdf->SetXY( 150, $y_point );
			// $pdf->MultiCell(14, 5, U2T($data['period_amount']), $border, 'R');
			
		}else if($pageNo == '2'){
			$y_point =69;
			$pdf->SetXY( 30, $y_point );
			$pdf->MultiCell(49, 5, U2T($data['firstname_th']." ".$data['lastname_th']), $border, 'C');
			
			$y_point = 82;
			$pdf->SetXY( 40, $y_point );
			$pdf->MultiCell(70, 5, U2T($witness_name), $border, 1);
			$pdf->SetXY( 130, $y_point );
			$pdf->MultiCell(42, 5, U2T($witness_name), $border, 1);

			$y_point = 93;
			$pdf->SetXY( 150, $y_point );
			$pdf->MultiCell(70, 5, U2T($agent_name), $border, 1);

			$y_point = 99.5;
			$pdf->SetXY( 50, $y_point );
			$pdf->MultiCell(70, 5, U2T($agent_member_id), $border, 1);
			$pdf->SetXY( 90, $y_point );
			$pdf->MultiCell(70, 5, U2T($agent_position), $border, 1);
			$pdf->SetXY( 150, $y_point );
			$pdf->MultiCell(70, 5, U2T($agent_under), $border, 1);

			$y_point = 129;
			$pdf->SetXY( 50, $y_point );
			$pdf->MultiCell(70, 5, U2T($borrower_name), $border, 1);
			$pdf->SetXY( 130, $y_point );
			$pdf->MultiCell(70, 5, U2T($agent_name), $border, 1);

			$y_point = 142.5;
			$pdf->SetXY( 40, $y_point );
			$pdf->MultiCell(70, 5, U2T($witness_name), $border, 1);
			$pdf->SetXY( 130, $y_point );
			$pdf->MultiCell(70, 5, U2T($witness_name), $border, 1);

			$y_point = 151.5;
			$pdf->SetXY( 45, $y_point );
			$pdf->MultiCell(70, 5, U2T($agent_name), $border, 1);
			$pdf->SetXY( 145, $y_point );
			$pdf->MultiCell(70, 5, U2T(num_format($data['loan_amount'])), $border, 1);


			$y_point = 158.5;
			$pdf->SetXY( 40, $y_point );
			$pdf->MultiCell(70, 5, U2T($this->center_function->convert($data['loan_amount'])), $border, 1);
			$pdf->SetXY( 155, $y_point );
			$pdf->MultiCell(40, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 1);

			$y_point = 177;
			$pdf->SetXY( 135, $y_point );
			$pdf->MultiCell(70, 5, U2T($agent_name), $border, 1);
		}
	}
	//exit;
	$pdf->Output();