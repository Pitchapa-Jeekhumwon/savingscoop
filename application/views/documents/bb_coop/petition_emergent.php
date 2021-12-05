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
	//คำขอกู้เงินฉุกเฉิน
	$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/loan/bb_coop/petition_emergent.pdf";
	$pageCount = $pdf->setSourceFile($filename);
	for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {	
	$pdf->AddPage();
		$tplIdx = $pdf->importPage($pageNo); 
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
		
		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->SetFont('THSarabunNew', '', 13 );
		
		$border = 0;
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);
		
		$arr_marry_status = array('1'=>'โสด', '2'=>'สมรส','3'=>'หย่า ','4'=>'หม้าย');
		if($pageNo == '1'){	
			$y_point = 17;
			$pdf->SetXY( 37, $y_point );
			$pdf->MultiCell(37, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 'C');	
			$pdf->SetXY( 85, $y_point );
			$pdf->MultiCell(40, 5, U2T(date('H:i น.',strtotime($data['createdatetime']))), $border, 'C');	
			
			$y_point = 57;
			$pdf->SetXY( 58, $y_point );
			$pdf->MultiCell(70, 5, U2T($full_name), $border, 1);
			$pdf->SetXY( 150, $y_point );
			$pdf->MultiCell(40, 5, U2T($member_id), $border, 'C');
			
			
			$y_point = 64.5;
			$pdf->SetXY( 38, $y_point );
			$pdf->MultiCell(65, 5, U2T($position), $border, 1);	
			$pdf->SetXY(139, $y_point );
			$pdf->MultiCell(70, 5, U2T($level), $border, 1);			
			
			$y_point = 71.5;
			$pdf->SetXY( 71, $y_point );
			$pdf->MultiCell(30, 5, U2T($data['id_card']), $border, 1);
			$pdf->SetXY( 121, $y_point );
			$pdf->MultiCell(88, 5, U2T($c_address_no.$c_address_moo.$c_address_village.$c_address_soi), $border, 1);
			
			$y_point = 78.5;
			$pdf->SetXY( 30, $y_point );
			$pdf->MultiCell(45, 5, U2T($c_address_road), $border, 1);
			$pdf->SetXY( 86, $y_point );
			$pdf->MultiCell(41, 5, U2T($c_district_name), $border, 1);
			$pdf->SetXY( 138, $y_point );
			$pdf->MultiCell(71, 5, U2T($c_amphur_name), $border, 1);
			
			$y_point = 85.5;
			$pdf->SetXY( 33, $y_point );
			$pdf->MultiCell(43, 5, U2T($c_province_name), $border, 1);
			$pdf->SetXY( 98, $y_point );
			$pdf->MultiCell(16, 5, U2T($c_zipcode), $border, 1);
			$pdf->SetXY( 138, $y_point );
			$pdf->MultiCell(25, 5, U2T($data['tel']), $border, 1);
			$pdf->SetXY( 175, $y_point );
			$pdf->MultiCell(34, 5, U2T($data['mobile']), $border, 1);
			
			$y_point = 93;
			$pdf->SetXY( 74, $y_point );
			$pdf->MultiCell(40, 5, U2T(num_format($member_income)), $border, 'R');

			$y_point = 100;
			$pdf->SetXY( 98, $y_point );
			$pdf->MultiCell(55, 5, U2T(" = ".num_format($data['loan_amount'])." = "), $border, 'C');

			$y_point = 107;
			$pdf->SetXY( 22, $y_point );
			$pdf->MultiCell(81, 5, U2T(" = ".$this->center_function->convert($data['loan_amount'])." = "), $border, 'C');
			$pdf->SetXY( 124, $y_point );
			$pdf->MultiCell(85, 5, U2T($data['loan_reason']), $border, 1);

			$y_point = 114;
			$pdf->SetXY( 110, $y_point );
			$pdf->MultiCell(32, 5, U2T(($paid_per_month != '')? " = ".num_format($paid_per_month)." = ":''), $border, 'C');
			$pdf->SetXY( 162, $y_point );
			$pdf->MultiCell(16, 5, U2T( $data['period_amount']), $border, 'C');

			$y = 0;

			if(!empty($data_old_loan)){
				foreach($data_old_loan AS $key_old=>$val_old){
					$y_point = 137+$y;
					$pdf->Image('assets/images/check-box-mark.png', 46, $y_point, 5, '', '', '', '', false, 35);
					$pdf->SetXY(52, $y_point );
					$pdf->MultiCell(71, 5, U2T("".$val_old['loan_name']), $border, 'L');
					$pdf->SetXY(125, $y_point );
					$pdf->MultiCell(80, 5, U2T('จำนวน.............................................................................บาท'), $border, 'L');
					$pdf->SetXY( 135, ($y_point-1) );
					$pdf->MultiCell(55, 5, U2T(($val_old['loan_amount_balance'] != '')? " = ".num_format($val_old['loan_amount_balance'])." = ":''), $border, 'R');
					$y +=7;

				}
			}
            else{
                foreach($loan_select AS $key_loan=>$val_loan){
                    $y_point = 137+$y;
                    $pdf->Image('assets/images/check-mark1.png', 46, $y_point, 5, '', '', '', '', false, 35);

                    $pdf->SetXY(52, $y_point );
                    $pdf->MultiCell(71, 5, U2T("".$val_loan['loan_name']), $border, 'L');
                    $pdf->SetXY(125, $y_point );
                    $pdf->MultiCell(80, 5, U2T('จำนวน.............................................................................บาท'), $border, 'L');
                    $y +=7;
                }

            }
			
			$y_point = 264.5;
			$pdf->SetXY( 118, $y_point );
			$pdf->MultiCell(60, 5, U2T($full_name), $border, 'C');
		}
	 
	 }

	$pdf->Output();