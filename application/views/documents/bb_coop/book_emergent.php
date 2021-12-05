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
	
	//หนังสือเงินฉุกเฉิน
	$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/loan/bb_coop/book_emergent.pdf";
//	$filename = $_SERVER["DOCUMENT_ROOT"].'/fsccoop'."/assets/document/loan/bb_coop/book_emergent.pdf";
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
			$y_point = 20.5;
			$pdf->SetXY( 32, $y_point );
			$pdf->MultiCell(35, 5, U2T(""), $border, 1);	
			
			$y_point = 27;
			$pdf->SetXY( 32, $y_point );
			$pdf->MultiCell(35, 5, U2T(""), $border, 1);	
			
			$y_point = 23;
			$pdf->SetXY( 168, $y_point );
			$pdf->MultiCell(30, 5, U2T($data['contract_number']), $border, 1);
			
			$y_point = 30;
			$pdf->SetXY( 159, $y_point );
			$pdf->MultiCell(31, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 'C');			
			
			$y_point = 68;
			$pdf->SetXY( 155, $y_point );
			$pdf->MultiCell(39, 5, U2T($write_at), $border, 'C');
			
			$y_point = 76;
			$pdf->SetXY( 155, $y_point );
			$pdf->MultiCell(39, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 'C');			
			
			$y_point = 91;
			$pdf->SetXY( 48, $y_point );
			$pdf->MultiCell(145, 5, U2T($full_name), $border, 1);
			
			$y_point = 99.2;
			$pdf->SetXY( 40, $y_point );
			$pdf->MultiCell(49, 5, U2T($member_id), $border, 'C');
			$pdf->SetXY( 145, $y_point );
			$pdf->MultiCell(50, 5, U2T($position), $border, 1);
			
			$y_point = 107.5;
			$pdf->SetXY( 93.5, $y_point );
			$pdf->MultiCell(46, 5, U2T($data['id_card']), $border, 1);
			$pdf->SetXY(149, $y_point );
			$pdf->MultiCell(60, 5, U2T($level), $border, 1);
			
			$y_point = 115.5;
			$pdf->SetXY( 50.5, $y_point );
//			$pdf->MultiCell(13, 5, U2T($c_address_no.$c_address_moo.$c_address_village.$c_address_soi.$c_address_road), $border, 1);
			$pdf->MultiCell(13, 5, U2T($c_address_no), $border, 1);
			$pdf->SetXY( 70, $y_point );
			$pdf->MultiCell(35, 5, U2T($c_district_name), $border, 1);
			$pdf->SetXY( 110, $y_point );
			$pdf->MultiCell(35, 5, U2T($c_amphur_name), $border, 1);
			$pdf->SetXY( 146, $y_point );
			$pdf->MultiCell(60, 5, U2T($c_province_name), $border, 1);

            $y_point = 124;
            $pdf->SetXY( 39, $y_point );
            $pdf->MultiCell(27, 5, U2T($c_zipcode), $border, 1);
			$pdf->SetXY( 87, $y_point );
			$pdf->MultiCell(29, 5, U2T($data['tel']), $border, 1);
			$pdf->SetXY( 131, $y_point );
			$pdf->MultiCell(34, 5, U2T($data['mobile']), $border, 1);
			
			$y_point = 140.5;
			$pdf->SetXY( 87.5, $y_point );
			$pdf->MultiCell(30, 5, U2T(" = ".num_format($data['loan_amount'])." = "), $border, 'C');
			$pdf->SetXY( 122, $y_point );
			$pdf->MultiCell(70, 5, U2T(" = ".$this->center_function->convert($data['loan_amount'])." = "), $border, 'C');

			$y_point = 156.7;
			$pdf->SetXY( 22, $y_point );
			$pdf->MultiCell(160, 5, U2T($data['loan_reason']), $border, 1);

            $y_point = 165;
            $pdf->SetXY( 145.5, $y_point );
			$pdf->MultiCell(25, 5, U2T(($paid_per_month != '')? " = ". num_format($paid_per_month)." =":''), $border, 'C');

            $y_point = 173.4;
            $pdf->SetXY( 22, $y_point );
			$pdf->MultiCell(132, 5, U2T(($paid_per_month != '')? " = ". $this->center_function->convert($paid_per_month)." = ":''), $border, 'C');
						
			$y_point = 181.5;
			$pdf->SetXY( 40, $y_point );
			$pdf->MultiCell(25, 5, U2T($data['period_amount']), $border, 'C');	
			$pdf->SetXY( 90, $y_point );
			$pdf->MultiCell(36, 5, U2T($this->center_function->ConvertToThaiDate($data['date_start_period'],0,0)), $border, 'C');
		}else if($pageNo == '2'){
			$y_point = 114.5;
			$pdf->SetXY( 130, $y_point );
			$pdf->MultiCell(48, 5, U2T($full_name), $border, 'C');
		 }
	 }

$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/loan/bb_coop/payment_voucher.pdf";
//$filename = $_SERVER["DOCUMENT_ROOT"].'/fsccoop'."/assets/document/loan/bb_coop/payment_voucher.pdf";
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
        $y_point = 33.4;
        $pdf->SetXY( 150, $y_point );
        $pdf->MultiCell(42, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 'C');

        $y_point = 42.2;
        $pdf->SetXY( 40, $y_point );
        $pdf->MultiCell(85, 5, U2T($full_name), $border, 1);

        $pdf->SetXY( 155, $y_point );
        $pdf->MultiCell(30, 5, U2T($member_id), $border, 'C');

        $y_point = 51.2;
        $pdf->SetXY( 30, $y_point );
        $pdf->MultiCell(142, 5, U2T($position), $border, 1);

        $y_point = 60;
        $pdf->SetXY( 33, $y_point );
        $pdf->MultiCell(85, 5, U2T($profile_location['coop_name_th']), $border, 'C');

        $y_point = 69;
        $pdf->SetXY( 46, $y_point );
        $pdf->MultiCell(85, 5, U2T($data['loan_name']), $border, 'C');
        $pdf->SetXY( 142, $y_point );
        $pdf->MultiCell(40, 5, U2T(" = ".num_format($data['loan_amount'])." = "), $border, 'C');

        $y_point = 78.1;
        $pdf->SetXY( 30, $y_point );
        $pdf->MultiCell(110, 5, U2T(" = ".$this->center_function->convert($data['loan_amount'])." = "), $border, 'C');

        $y_point = 87.3;
        $pdf->SetXY( 30, $y_point );
        $pdf->MultiCell(160, 5, U2T(''), $border, 'C');

        $y_point = 113.5;
        $pdf->SetXY( 107, $y_point );
        $pdf->MultiCell(60, 5, U2T($full_name), $border, 'C');
    }
}
$pdf->Output();