<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}
function cal_age($birthday,$type = 'y'){     //รูปแบบการเก็บค่าข้อมูลวันเกิด
		$birthday = date("Y-m-d",strtotime($birthday)); 
		$today = date("Y-m-d");   //จุดต้องเปลี่ยน
		list($byear, $bmonth, $bday)= explode("-",$birthday);       //จุดต้องเปลี่ยน
		list($tyear, $tmonth, $tday)= explode("-",$today);                //จุดต้องเปลี่ยน
		$mbirthday = mktime(0, 0, 0, $bmonth, $bday, $byear);
		$mnow = mktime(0, 0, 0, $tmonth, $tday, $tyear );
		$mage = ($mnow - $mbirthday);
		//echo "วันเกิด $birthday"."<br>\n";
		//echo "วันที่ปัจจุบัน $today"."<br>\n";
		//echo "รับค่า $mage"."<br>\n";
		$u_y=date("Y", $mage)-1970;
		$u_m=date("m",$mage)-1;
		$u_d=date("d",$mage)-1;
		if($type=='y'){
			return $u_y;
		}else if($type=='m'){
			return $u_m;
		}else{
			return $u_d;
		}
	}

//$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/payment.pdf" ;
	//echo $filename;exit;
	
	$pdf = new FPDI('L','mm', array(140,227));
	
	//$pageCount_1 = $pdf->setSourceFile($filename);
	//for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {	
	$pdf->AddPage();
		//$tplIdx = $pdf->importPage($pageNo); 
		//$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
		
		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->SetFont('THSarabunNew', '', 13 );
		
		$border = 0;
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);
		
		$pay_type = array('cash'=>'เงินสด', 'cheque'=>'เช็คธนาคาร', 'transfer'=>'เงินโอน');
		//if($pageNo == '1'){
			$y_point = 15.5;
			$pdf->SetXY( 182, $y_point );
			$pdf->MultiCell(40, 5, U2T($row['account_buy_number']), $border, 1);
			
			$y_point = 22.8;
			$pdf->SetXY( 182, $y_point );
			$pdf->MultiCell(40, 5, U2T($this->center_function->mydate2date($row['buy_date'])), $border, 1);
			
			$y_point = 29.7;
			$pdf->SetXY( 29, $y_point );
			$pdf->MultiCell(90, 5, U2T($row['pay_for']), $border, 1);
			
			$y_point = 30;
			$pdf->SetXY( 127, $y_point );
			$pdf->MultiCell(40, 5, U2T($pay_type[$row['pay_type']] ), $border, 1);
			
			$y_point = 35.7;
			$pdf->SetXY( 33, $y_point );
			$pdf->MultiCell(90, 5, U2T(number_format($row['total_amount'],2)." บาท"), $border, 1);
			
			
			$y_point = 46;
			foreach($rs_detail as $key => $row_detail){
				$y_point += 7;
				$pdf->SetXY( 17, $y_point );
				$pdf->MultiCell(24, 5, U2T( $row_detail['account_chart_id'] ), $border, 'C');
				$pdf->SetXY( 41, $y_point );
				$pdf->MultiCell(31, 5, U2T( $row_detail['bill_number'] ), $border, 'C');
				$pdf->SetXY( 72, $y_point );
				$pdf->MultiCell(109, 5, U2T( $row_detail['pay_description'] ), $border, 1);
				$pdf->SetXY( 181, $y_point );
				$pdf->MultiCell(30, 5, U2T( number_format($row_detail['pay_amount'],2) ), $border, 'R');
			}
			
			$y_point = 109;
			$pdf->SetXY( 17, $y_point );
			$pdf->MultiCell( 164, 5, U2T( $this->center_function->convert($row['total_amount']) ), $border, 'C');
			$pdf->SetXY( 181, $y_point );
			$pdf->MultiCell( 30, 5, U2T( number_format($row['total_amount'],2) ), $border, 'R');
			
			$pdf->Image(base_url().PROJECTPATH.'/assets/images/coop_signature/'.$signature['signature_1'],25,125,25,'','','');
			$pdf->Image(base_url().PROJECTPATH.'/assets/images/coop_signature/'.$signature['signature_2'],120,125,25,'','','');
		//}
		
	//}
	
	$pdf->Output();