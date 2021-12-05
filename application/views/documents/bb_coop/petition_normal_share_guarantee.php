<?php
	function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
	function num_format($text) {
		if($text!=''){
			return number_format($text,2);
		}else{
			return '';
		}
	}
	//$pdf->grid = 10;
	//คำขอกู้เงินสามัญ
	$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/loan/bb_coop/petition_normal_share_guarantee.pdf";
//	$filename = $_SERVER["DOCUMENT_ROOT"]."/fsccoop/assets/document/loan/bb_coop/petition_normal_share_guarantee.pdf";
	$pageCount = $pdf->setSourceFile($filename);
	for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {	
	$pdf->AddPage();
		$tplIdx = $pdf->importPage($pageNo); 
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
		
		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->SetFont('THSarabunNew', '', 14 );
		
		$border = 0;
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);
		
		$arr_marry_status = array('1'=>'โสด', '2'=>'สมรส','3'=>'หย่า ','4'=>'หม้าย');
		if($pageNo == '1'){	
			$y_point = 16.2;
			$pdf->SetXY( 35, $y_point );
			$pdf->MultiCell(35, 5, U2T(""), $border, 1);	
			
			$y_point += 7.6;
			$pdf->SetXY( 35, $y_point );
			$pdf->MultiCell(35, 5, U2T(""), $border, 1);	
			
			$y_point = 25.2;
			$pdf->SetXY( 160, $y_point );
			$pdf->MultiCell(35, 5, U2T(""), $border, 1);

            $y_point += 7.6;
			$pdf->SetXY( 152, $y_point );
			$pdf->MultiCell(8, 5, U2T(""), $border, 'C');
			$pdf->SetXY( 162, $y_point );
			$pdf->MultiCell(10, 5, U2T(""), $border, 'C');
			$pdf->SetXY( 174, $y_point );
			$pdf->MultiCell(23, 5, U2T(""), $border, 'C');

            $y_point += 7.6;
			$pdf->SetXY(162, $y_point);
			$pdf->MultiCell(35, 5, U2T(""), $border, 1);

			$y_point = 78.5;
			$pdf->SetXY( 153, $y_point );
			$pdf->MultiCell(35, 5, U2T($write_at), $border, 'C');

            $y_point += 6.8;
			$pdf->SetXY( 153, $y_point );
			$pdf->MultiCell(35, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 'C');

            $y_point += 13.2;
			$pdf->SetXY( 43, $y_point );
			$pdf->MultiCell(85, 5, U2T($full_name), $border, 1);

            $y_point += 6.6;
			$pdf->SetXY( 38, $y_point );
			$pdf->MultiCell(37, 5, U2T($member_id), $border, 'C');
			$pdf->SetXY( 137, $y_point );
			$pdf->MultiCell(65, 5, U2T($position), $border, 1);

            $y_point += 6.6;
			$pdf->SetXY( 75, $y_point );
			$pdf->MultiCell(35, 5, U2T($data['id_card']), $border, 1);
			$pdf->SetXY(145, $y_point );
			$pdf->MultiCell(65, 5, U2T($level), $border, 1);

            $y_point += 6.6;
			$pdf->SetXY( 50, $y_point );
			$pdf->MultiCell(51, 5, U2T($c_address_no), $border, 1);
			$pdf->SetXY( 112, $y_point );
			$pdf->MultiCell(90, 5, U2T($c_address_road), $border, 1);

            $y_point += 6.6;
			$pdf->SetXY( 34.5, $y_point );
			$pdf->MultiCell(49, 5, U2T($c_district_name), $border, 1);
			$pdf->SetXY( 102, $y_point );
			$pdf->MultiCell(53, 5, U2T($c_amphur_name), $border, 1);
			$pdf->SetXY( 165, $y_point );
			$pdf->MultiCell(35, 5, U2T($c_province_name), $border, 1);

            $y_point += 6.6;
			$pdf->SetXY( 38, $y_point );
			$pdf->MultiCell(38, 5, U2T($c_zipcode), $border, 1);
			$pdf->SetXY( 95, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['tel']), $border, 1);
			$pdf->SetXY( 145, $y_point );
			$pdf->MultiCell(45, 5, U2T($data['mobile']), $border, 1);

            $y_point += 13.4;
			$pdf->SetXY( 92, $y_point );
			$pdf->MultiCell(49, 5, U2T(" = " .num_format($data['loan_amount'])." = "), $border, 'C');

            $y_point += 6.6;
			$pdf->SetXY( 22, $y_point );
			$pdf->MultiCell(120, 5, U2T(" = ".$this->center_function->convert($data['loan_amount'])." = "), $border, 'C');

            $y_point += 6.6;
			$pdf->SetXY( 68, $y_point );
			$pdf->MultiCell(131, 5, U2T($data['loan_reason']), $border, 1);

            $y_point += 20.3;
            $pdf->SetXY( 78, $y_point );
            $pdf->MultiCell(37, 5, U2T(($loan_guarantee['0']['amount'] != '')? " = " .num_format($loan_guarantee['0']['amount']/10) ." = " :''), $border, 'C');
            $pdf->SetXY( 133, $y_point );
            $pdf->MultiCell(40, 5, U2T(($loan_guarantee['0']['amount'] != '')? " = " .num_format($loan_guarantee['0']['amount']) ." = " :''), $border, 'C');

//            $pdf->SetFont('THSarabunNew', '', 13 );
            $y_point += 6.8;
            $pdf->SetXY( 141, $y_point );
            $pdf->MultiCell(32, 5, U2T(($paid_per_month != '')? " = " .num_format($paid_per_month) ." = " :''), $border, 'C');
            $y_point += 6.6;
            $pdf->SetXY( 110, $y_point );
            $pdf->MultiCell(42, 5, U2T($data['period_amount']), $border, 'C');
		}else if($pageNo == '2'){

//			$pdf->SetFont('THSarabunNew', '', 12 );
			$j= -25.5;

			if(!empty($data_old_loan['emergent'])){
				foreach($data_old_loan['emergent'] AS $key_n=>$val_n){
					if($key_n < 2){
						//ก. เงินกู้สามัญ
						$y_point = 71+$j;
						$pdf->SetXY( 65, $y_point );
						$pdf->MultiCell(36, 5, U2T($val_n['contract_number']), $border, 'C');
						$pdf->SetXY( 105, $y_point );
						$pdf->MultiCell(30, 5, U2T($this->center_function->ConvertToThaiDate($val_n['approve_date'],0,0)), $border, 'C');
						
						$y_point = 77.5+$j;
						$pdf->SetXY( 42, $y_point );
						$pdf->MultiCell(100, 5, U2T($val_n['loan_reason']), $border, 1);
//                        $pdf->SetFont('THSarabunNew', '', 12 );
						$pdf->SetXY( 167, $y_point );
						$pdf->MultiCell(27, 5, U2T("=".num_format($val_n['loan_amount_balance']) ."=" ), $border, 'C');
//                        $pdf->SetFont('THSarabunNew', '', 13 );
						$j += 13.5;
					}
				}
			}

			if(!empty($data_old_loan['emergent'])){
				foreach($data_old_loan['emergent'] AS $key_e=>$val_e){
					if($key_e < 1){
						//ข.เงินกู้ฉุกเฉิน
						$y_point = 83.3-4.5;
						$pdf->SetXY( 65, $y_point );
						$pdf->MultiCell(36, 5, U2T($val_e['contract_number']), $border, 'C');
						$pdf->SetXY( 105, $y_point );
						$pdf->MultiCell(30, 5, U2T($this->center_function->ConvertToThaiDate($val_e['approve_date'],0,0)), $border, 'C');

						$y_point = 89.8-4.5;
						$pdf->SetXY( 42, $y_point );
						$pdf->MultiCell(100, 5, U2T($val_e['loan_reason']), $border, 1);
						$pdf->SetXY( 167, $y_point );
//                        $pdf->SetFont('THSarabunNew', '', 9.5 );
						$pdf->MultiCell(27, 5, U2T(" = " . num_format($val_e['loan_amount_balance']) . " = "), $border, 'C');
//                        $pdf->SetFont('THSarabunNew', '', 13 );
					}
				}
			}

			if(!empty($data_old_loan['emergent'])){
				foreach($data_old_loan['emergent'] AS $key_s=>$val_s){
					if($key_s < 1){
						//ค.เงินกู้พิเศษ
						$y_point = 103.5-4.5;
						$pdf->SetXY( 65, $y_point );
						$pdf->MultiCell(36, 5, U2T($val_s['contract_number']), $border, 'C');
						$pdf->SetXY( 105, $y_point );
						$pdf->MultiCell(30, 5, U2T($this->center_function->ConvertToThaiDate($val_s['approve_date'],0,0)), $border, 'C');

						$y_point = 110-4.5;
						$pdf->SetXY( 42, $y_point );
						$pdf->MultiCell(100, 5, U2T($val_s['loan_reason']), $border, 1);
						$pdf->SetXY( 167, $y_point );
//                        $pdf->SetFont('THSarabunNew', '', 9.5 );
						$pdf->MultiCell(27, 5, U2T($val_s['loan_amount_balance'] ? " = " . num_format($val_s['loan_amount_balance']) . " = " : " - "), $border, 'C');
//                        $pdf->SetFont('THSarabunNew', '', 13 );
					}
				}
			}
//            $pdf->SetFont('THSarabunNew', '', 13 );
            $y_point = 201;
            $pdf->SetXY( 114, $y_point );
            $pdf->MultiCell(55, 5, U2T($full_name), $border, 'C');
		 }
	 }

	$pdf->Output();