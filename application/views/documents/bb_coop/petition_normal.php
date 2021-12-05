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
    $count_guarantee = count($loan_guarantee);
    if(empty($count_guarantee)){
        $count_guarantee = '1';
    }
    if($count_guarantee == '1'){
        $filename = $_SERVER["DOCUMENT_ROOT"]."/assets/document/loan/bb_coop/petition_normal_1.pdf";
    }else if($count_guarantee == '2'){
        $filename = $_SERVER["DOCUMENT_ROOT"]."/assets/document/loan/bb_coop/petition_normal_2.pdf";
    }else {
        $filename = $_SERVER["DOCUMENT_ROOT"]."/assets/document/loan/bb_coop/petition_normal_3.pdf";
    }
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
			$y_point = 14;
			$pdf->SetXY( 27, $y_point );
			$pdf->MultiCell(35, 5, U2T(""), $border, 1);

			$y_point = 20.5;
			$pdf->SetXY( 27, $y_point );
			$pdf->MultiCell(35, 5, U2T(""), $border, 1);

			$y_point = 21.5;
			$pdf->SetXY( 160, $y_point );
			$pdf->MultiCell(35, 5, U2T(""), $border, 1);

			$y_point = 28.5;
			$pdf->SetXY( 152, $y_point );
			$pdf->MultiCell(8, 5, U2T(""), $border, 'C');
			$pdf->SetXY( 162, $y_point );
			$pdf->MultiCell(10, 5, U2T(""), $border, 'C');
			$pdf->SetXY( 174, $y_point );
			$pdf->MultiCell(23, 5, U2T(""), $border, 'C');

			$y_point = 35;
			$pdf->SetXY(162, $y_point);
			$pdf->MultiCell(35, 5, U2T(""), $border, 1);

			$y_point = 46.7;
			$pdf->SetXY( 149, $y_point );
			$pdf->MultiCell(35, 5, U2T($write_at), $border, 'C');

			$y_point = 56;
			$pdf->SetXY( 148, $y_point );
			// ลูกค้าต้องการให้วันที่ได้ ถ้ายังทำไม่ได้ให้เว้นว่างไว้
//			$pdf->MultiCell(30, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 'C');

			$y_point = 79-2.4;
			$pdf->SetXY( 38, $y_point );
			$pdf->MultiCell(63, 5, U2T($full_name), $border, 1);

			$y_point = 89-3.2;
			$pdf->SetXY( 35.7, $y_point );
			$pdf->MultiCell(28, 5, U2T($member_id), $border, 'C');
			$pdf->SetXY( 121, $y_point );
			$pdf->MultiCell(70, 5, U2T($position), $border, 1);

			$y_point = 95.1;
			$pdf->SetXY( 80-7, $y_point );
			$pdf->MultiCell(30, 5, U2T($data['id_card']), $border, 1);
			$pdf->SetXY(137, $y_point );
			$pdf->MultiCell(55, 5, U2T($level), $border, 1);

			$y_point = 104.5;
			$pdf->SetXY( 50, $y_point );
			$pdf->MultiCell(38, 5, U2T($c_address_no), $border, 1);
			$pdf->SetXY( 97, $y_point );
			$pdf->MultiCell(82, 5, U2T($c_address_road), $border, 1);

			$y_point = 119-5.1;
			$pdf->SetXY( 34, $y_point );
			$pdf->MultiCell(40, 5, U2T($c_district_name), $border, 1);
			$pdf->SetXY( 90.5, $y_point );
			$pdf->MultiCell(50, 5, U2T($c_amphur_name), $border, 1);
			$pdf->SetXY( 150, $y_point );
			$pdf->MultiCell(35, 5, U2T($c_province_name), $border, 1);

			$y_point = 128.5-5.1;
			$pdf->SetXY( 36, $y_point );
			$pdf->MultiCell(40, 5, U2T($c_zipcode), $border, 1);
			$pdf->SetXY( 101, $y_point );
			$pdf->MultiCell(39, 5, U2T($data['tel']), $border, 1);
			$pdf->SetXY( 148, $y_point );
			$pdf->MultiCell(45, 5, U2T($data['mobile']), $border, 1);

			$y_point = 148-5.9;
			$pdf->SetXY( 90, $y_point );
			$pdf->MultiCell(49, 5, U2T(" = " .num_format($data['loan_amount'])." = "), $border, 'C');

			$y_point = 158.5-6.5;
			$pdf->SetXY( 17, $y_point );
			$pdf->MultiCell(110, 5, U2T(" = ".$this->center_function->convert($data['loan_amount'])." = "), $border, 'C');

			$y_point = 168-6.2;
			$pdf->SetXY( 68, $y_point );
			$pdf->MultiCell(131, 5, U2T($data['loan_reason']), $border, 1);

			$i=0;
//			$pdf->SetFont('THSarabunNew', '', 12 );
            $y_point = 199;
            if(!empty($loan_guarantee)){
				foreach($loan_guarantee AS $key=>$val){
					$i++;
					$guarantee_name = @$val['prename_full'].@$val['firstname_th']." ".@$val['lastname_th'];
					//บุคคลค้ำประกัน
					$pdf->SetXY( 10.5, $y_point );
					$pdf->MultiCell(10, 5, U2T($i), $border, 'C');
					$pdf->SetXY( 22, $y_point );
					$pdf->MultiCell(46, 5, U2T($guarantee_name." / ".@$val['member_id']), $border, 'L');
					$pdf->SetXY( 69, $y_point );
					$pdf->MultiCell(79, 5, U2T(@$val['mem_group_name']), $border, 'L');
					$pdf->SetXY( 148, $y_point );
					$pdf->MultiCell(18, 5, U2T(num_format(@$val['salary'])), $border, 'R');
					$pdf->SetXY( 184.8, $y_point );
					$pdf->MultiCell(22, 5, U2T(num_format(@$val['amount'])), $border, 'R');

					$y_point += 6;
					$pdf->SetXY( 69, $y_point );
					$pdf->MultiCell(79, 5, U2T(@$val['mem_group_name_faction']), $border, 'L');
                    $y_point += 10;
				}
			}
            if($count_guarantee == '1'){
                $y_point = 243.5;
                $pdf->SetXY( 146, $y_point );
                $pdf->MultiCell(32, 5, U2T(($paid_per_month != '')? " = " .num_format($paid_per_month) ." = " :''), $border, 'C');
                $y_point += 9.8;
                $pdf->SetXY( 110, $y_point );
                $pdf->MultiCell(42, 5, U2T($data['period_amount']), $border, 'C');
            }

		}else if($pageNo == '2'){
            if($count_guarantee != '1') {
                if($count_guarantee == '2'){
                    $y_point = 14;
                }else{
                    $y_point = 34;
                }
                $pdf->SetXY(146, $y_point);
                $pdf->MultiCell(32, 5, U2T(($paid_per_month != '') ? " = " . num_format($paid_per_month) . " = " : ''), $border, 'C');
                $y_point += 9.8;
                $pdf->SetXY(110, $y_point);
                $pdf->MultiCell(42, 5, U2T($data['period_amount']), $border, 'C');
            }

//			$pdf->SetFont('THSarabunNew', '', 12 );
            if($count_guarantee == '1'){
                $j= -40;
                $j2= -70;
            }else if($count_guarantee == '2'){
                $j= -10;
                $j2= -40;
            }else if($count_guarantee == '3'){
                $j= 10;
                $j2= -20;
            }
			if(!empty($data_old_loan['normal'])){
				foreach($data_old_loan['normal'] AS $key_n=>$val_n){
					if($key_n < 2){
						//ก. เงินกู้สามัญ
						$y_point = 71+$j;
						$pdf->SetXY( 65, $y_point );
						$pdf->MultiCell(36, 5, U2T($val_n['contract_number']), $border, 'C');
						$pdf->SetXY( 114, $y_point );
						$pdf->MultiCell(52, 5, U2T($this->center_function->ConvertToThaiDate($val_n['approve_date'],0,0)), $border, 'C');
						
						$y_point = 79+$j;
						$pdf->SetXY( 39, $y_point );
						$pdf->MultiCell(88, 5, U2T($val_n['loan_reason']), $border, 1);
						$pdf->SetXY( 148.5, $y_point );
						$pdf->MultiCell(30, 5, U2T("=".num_format($val_n['loan_amount_balance']) ."=" ), $border, 'C');
						$j += 15;
					}
				}
			}
			if(!empty($data_old_loan['emergent'])){
				foreach($data_old_loan['emergent'] AS $key_e=>$val_e){
					if($key_e < 1){
						//ข.เงินกู้ฉุกเฉิน
						$y_point = 139.3+$j2;
						$pdf->SetXY( 65, $y_point );
						$pdf->MultiCell(36, 5, U2T($val_e['contract_number']), $border, 'C');
						$pdf->SetXY( 114, $y_point );
						$pdf->MultiCell(52, 5, U2T($this->center_function->ConvertToThaiDate($val_e['approve_date'],0,0)), $border, 'C');

						$y_point = 146.8+$j2;
						$pdf->SetXY( 39, $y_point );
						$pdf->MultiCell(88, 5, U2T($val_e['loan_reason']), $border, 1);
						$pdf->SetXY( 149, $y_point );
						$pdf->MultiCell(29, 5, U2T(" = " . num_format($val_e['loan_amount_balance']) . " = "), $border, 'C');
					}
				}
			}
			if(!empty($data_old_loan['special'])){
				foreach($data_old_loan['special'] AS $key_s=>$val_s){
					if($key_s < 1){
						//ค.เงินกู้พิเศษ
						$y_point = 161.8+$j2;
						$pdf->SetXY( 65, $y_point );
						$pdf->MultiCell(36, 5, U2T($val_s['contract_number']), $border, 'C');
						$pdf->SetXY( 114, $y_point );
						$pdf->MultiCell(52, 5, U2T($this->center_function->ConvertToThaiDate($val_s['approve_date'],0,0)), $border, 'C');

						$y_point = 169.3+$j2;
						$pdf->SetXY( 39, $y_point );
						$pdf->MultiCell(88, 5, U2T($val_s['loan_reason']), $border, 1);
						$pdf->SetXY( 149, $y_point );
//                        $pdf->SetFont('THSarabunNew', '', 9.5 );
						$pdf->MultiCell(29, 5, U2T($val_s['loan_amount_balance'] ? " = " . num_format($val_s['loan_amount_balance']) . " = " : " - "), $border, 'C');
					}
				}
			}
            if($count_guarantee == '1'){
                $y_point = 203;
            }else if($count_guarantee == '2'){
                $y_point = 233;
            }else{
                $y_point = 252;
            }

            $pdf->SetXY( 111, $y_point );
            $pdf->MultiCell(55, 5, U2T($full_name), $border, 'C');
		 }
	 }

	$pdf->Output();