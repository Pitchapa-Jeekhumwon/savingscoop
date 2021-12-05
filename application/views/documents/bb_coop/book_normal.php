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
	//หนังสือเงินกู้สามัญ
	$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/loan/bb_coop/book_normal.pdf";
//	$filename = $_SERVER["DOCUMENT_ROOT"].'/fsccoop'."/assets/document/loan/bb_coop/book_normal.pdf";
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
		$y = 6.7;
		$arr_marry_status = array('1'=>'โสด', '2'=>'สมรส','3'=>'หย่า ','4'=>'หม้าย');
		if($pageNo == '1'){
			$y_point = 37.3;
			$pdf->SetXY( 53, $y_point );
			$pdf->MultiCell(49, 5, U2T($data['contract_number']), $border, 1);

			$y_point = 50.8;
			$pdf->SetXY( 160, $y_point );
			$pdf->MultiCell(30, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 'C');

			$y_point = 64.2;
			$pdf->SetXY( 47, $y_point );
			$pdf->MultiCell(142, 5, U2T($full_name), $border, 1);
            $y_point += $y;
			$pdf->SetXY( 135-15, $y_point );
			$pdf->MultiCell(30, 5, U2T($member_id), $border, 'C');

            $y_point += $y;
			$pdf->SetXY( 85-10, $y_point );
			$pdf->MultiCell(105, 5, U2T($position), $border, 1);

            $y_point += $y;
			$pdf->SetXY( 103-12, $y_point );
			$pdf->MultiCell(61, 5, U2T($data['id_card']), $border, 'C');

            $y_point += $y;
			$pdf->SetXY( 66-7, $y_point );
			$pdf->MultiCell(85, 5, U2T($c_address_no.$c_address_moo.$c_address_village.$c_address_soi), $border, 1);
			$pdf->SetXY( 160-14, $y_point );
			$pdf->MultiCell(40, 5, U2T($c_address_road), $border, 1);

            $y_point += $y;
			$pdf->SetXY( 30-2, $y_point );
			$pdf->MultiCell(45, 5, U2T($c_district_name), $border, 1);
			$pdf->SetXY( 87-1, $y_point );
			$pdf->MultiCell(65, 5, U2T($c_amphur_name), $border, 1);
			$pdf->SetXY( 166-16, $y_point );
			$pdf->MultiCell(35, 5, U2T($c_province_name), $border, 1);

            $y_point += $y;
			$pdf->SetXY( 41-3, $y_point );
			$pdf->MultiCell(27, 5, U2T($c_zipcode), $border, 1);
			$pdf->SetXY( 84-4, $y_point );
			$pdf->MultiCell(45, 5, U2T($data['mobile']), $border, 1);

			$y_point = 117.8;
			$pdf->SetXY( 92, $y_point );
			$pdf->MultiCell(40, 5, U2T(" = ".num_format($data['loan_amount'])." = "), $border, 'C');
			$pdf->SetXY( 135, $y_point );
			$pdf->MultiCell(45, 5, U2T(" = ".$this->center_function->convert($data['loan_amount'])." = "), $border, 'C');

			$y_point = 131;
			$pdf->SetXY( 150-15, $y_point );
			$pdf->MultiCell(55, 5, U2T(($paid_per_month != '')? " = ". $this->center_function->convert($paid_per_month)." = ":''), $border, 'C');

            $y_point += $y;
			$pdf->SetXY( 86-8, $y_point );
			$pdf->MultiCell(41, 5, U2T(" = ".$this->center_function->convert_number_to_text($data['interest_per_year'])." = "), $border, 'C');
			$pdf->SetXY( 157.5-3, $y_point );
			$pdf->MultiCell(39, 5, U2T( " = ". $this->center_function->convert_number_to_text($data['period_amount'])." = "), $border, 'C');

            $y_point += $y;
			$pdf->SetXY( 85-8, $y_point );
			$pdf->MultiCell(120, 5, U2T(($paid_per_month_last != '')? " = ".$this->center_function->convert($paid_per_month_last) ." = ":''), $border, 'C');

            $y_point += $y;
			$pdf->SetXY( 34, $y_point );
			$pdf->MultiCell(42, 5, U2T($this->center_function->ConvertToThaiDate($date_start_period,0,0)), $border, 'C');

		}else if($pageNo == '2'){
			$y_point = 16.4;
			$pdf->SetXY( 123-12, $y_point );
			$pdf->MultiCell(31.5, 5, U2T($member_id), $border, 'C');
			$pdf->SetXY( 165-14.5, $y_point );
			$pdf->MultiCell(25, 5, U2T(" = " . num_format($data_share['share_collect']) . " = "), $border, 'C');

            $y_point += $y;
			$pdf->SetXY( 32, $y_point );
			$pdf->MultiCell(44, 5, U2T(($data_share['share_collect_value'] != '')? " = " .num_format($data_share['share_collect_value']) ." = ":''), $border, 'C');

            //คู่สมรส
            $y2 = 74.8;
            if(@$data['marry_name'] != ''){
                $y_point = 127.5;
                $pdf->SetXY( 160+5, $y_point );
                $pdf->MultiCell(45, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 'C');

                $y_point = 136.3;
                $pdf->SetXY( 39, $y_point );
                $pdf->MultiCell(62, 5, U2T($data['marry_name']), $border, 1);
                $pdf->SetXY( 139, $y_point );
                $pdf->MultiCell(65, 5, U2T($full_name), $border, 1);

                $y_point = 143.8;
                $pdf->SetXY( 46, $y_point );
                $pdf->MultiCell(93, 5, U2T($full_name), $border, 1);
            }

            $y_point = 183.1;
            $pdf->SetXY( 33, $y_point );
            $pdf->MultiCell(58, 5, U2T($full_name), $border, 1);
            $pdf->SetXY( 130, $y_point );
            $pdf->MultiCell(45, 5, U2T(" = ". num_format($data['loan_amount']). " = "), $border, 'C');

            $y_point = 190;
            $pdf->SetXY( 20, $y_point );
            $pdf->MultiCell(106, 5, U2T(" = " . $this->center_function->convert($data['loan_amount'])." = "), $border, 'C');

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

