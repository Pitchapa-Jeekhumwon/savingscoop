<?php
$month_arr = array('01'=>'มกราคม','02'=>'กุมภาพันธ์','03'=>'มีนาคม','04'=>'เมษายน','05'=>'พฤษภาคม','06'=>'มิถุนายน','07'=>'กรกฎาคม','08'=>'สิงหาคม','09'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
$month_short_arr = array('01'=>'ม.ค.','02'=>'ก.พ.','03'=>'มี.ค.','04'=>'เม.ย.','05'=>'พ.ค.','06'=>'มิ.ย.','07'=>'ก.ค.','08'=>'ส.ค.','09'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

$objPHPExcel = new PHPExcel();

$borderRight = array(
  'borders' => array(
    'right' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);
$borderLeft = array(
  'borders' => array(
    'left' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);
$borderTop = array(
  'borders' => array(
    'top' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);
$borderBottom = array(
  'borders' => array(
    'bottom' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);
$borderBottomDouble = array(
  'borders' => array(
    'bottom' => array(
      'style' => PHPExcel_Style_Border::BORDER_DOUBLE
    )
  )
);
$styleArray = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  ),
  'font'  => array(
		'bold'  => false,
		'size'  => 13,
		'name'  => 'Cordia New'
	)
);
$textStyleArray = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 13,
		'name'  => 'CordiaUPC'
	)
);
$headerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 13,
		'name'  => 'Cordia New'
	)
);
$titleStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 14,
		'name'  => 'AngsanaUPC'
	)
);
$footerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 14,
		'name'  => 'AngsanaUPC'
	)
);
$headStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 16,
		'name'  => 'AngsanaUPC'
	)
);
if(@$_GET['start_date']){
	$start_date_arr = explode('/',@$_GET['start_date']);
	$start_day = $start_date_arr[0];
	$start_month = $start_date_arr[1];
	$start_year = $start_date_arr[2];
	$start_year -= 543;
	$start_date = $start_year.'-'.$start_month.'-'.$start_day;
}
if(@$_GET['end_date']){
	$end_date_arr = explode('/',@$_GET['end_date']);
	$end_day = $end_date_arr[0];
	$end_month = $end_date_arr[1];
	$end_year = $end_date_arr[2];
	$end_year -= 543;
	$end_date = $end_year.'-'.$end_month.'-'.$end_day;
}
$text_end_date = "  ถึง  ".$this->center_function->ConvertToThaiDate($end_date);
$sheet = 0;
foreach($data as $keys =>$dataa) {
		$i=0;
		$objPHPExcel->createSheet($sheet);
		$objPHPExcel->setActiveSheetIndex($sheet);
		$objPHPExcel->getActiveSheet()->setTitle(@$type_code[@$keys]);
		$i+=1;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':L'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "รายงานการทำรายการ".' '.@$type_deposit[@$keys]);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$i+=1;
		$i_title = $i;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':L'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i,' วันที่ '.$this->center_function->ConvertToThaiDate($start_date).$text_end_date) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		// $i+=1;
		// $i_title = $i;
		// $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
		// $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i,' วันที่ '.$this->center_function->ConvertToThaiDate($start_date).$text_end_date) ;
		// $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
      if($m != '12'){
            $next_month = sprintf("%02d",$m+1);
            $next_year = $year;
        }else{
            $next_month = '01';
            $next_year = $year+1;
        }
		// $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i,$this->center_function->ConvertToThaiDate(@$datas['row_head']['transaction_time'],0,0) ) ;
		$i+=1;
		$i_top = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "วันที่" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "เวลาที่" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':E'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "หมายเลขบัญชี" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , "ชื่อบัญชี" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':H'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "รายการ" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "ฝาก" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "ถอน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , "คงเหลือ" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('L'.$i.':L'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , "ผู้บันทึก" ) ;
		$i+=1;
		$i_bottom = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "ทำรายการ" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "ทำรายการ" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "(บาท)" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "(บาท)" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , "(บาท)" ) ;
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4.23);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(9.00);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(9.00);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(9);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(24);	
		foreach(range('A','L') as $columnID) {
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderTop);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderRight);
			
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderRight);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderBottom);
			
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':L'.$i_bottom)->applyFromArray($headerStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':L'.$i_bottom)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$j = 1;
		$sum_transaction_withdrawal = 0;
		$sum_transaction_deposit = 0;
		$sum_interest = 0;
		$sum_no = 0 ;
	  	if(!empty($dataa)) {
			foreach($dataa as $key => $row){
				@$sum_transaction_withdrawal+=@$row['transaction_withdrawal'];
				@$sum_transaction_deposit+=@$row['transaction_deposit'];	
				@$transaction_balance+=@$row['transaction_balance'];
				@$sum_no++;
				$i+=1;
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++);
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , (@$row['transaction_time'])?$this->center_function->ConvertToThaiDate(@$row['transaction_time'],1,0):"");
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , (@$row['transaction_time'])?date(" H:i" , strtotime(@$row['transaction_time'])):"");
				$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':E'.($i));
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$this->center_function->format_account_number(@$row['account_id']) );
				$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.($i));
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$row['account_name']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , @$row['transaction_list']);
				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , @number_format($row['transaction_deposit'],2));
				$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , @number_format($row['transaction_withdrawal'],2));
				$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , @number_format($row['transaction_balance'],2));
				$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , @$row['user_name']);		
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->applyFromArray($textStyleArray);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->applyFromArray($borderTop);
				
				foreach(range('A','L') as $columnID) {
					if(!in_array($columnID, array('D','E'))){
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderLeft);
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderRight);
					}
					if(!in_array($columnID, array('F','G'))){
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderLeft);
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderRight);
					}
					$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderBottom);
				}
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$count_register++;
			}
			$i+=1;
			$i_top = $i;
			$i_bottom = $i;
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':H'.($i));
			$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i ,'รวมทั้งหมด '.$sum_no.' รายการ');		
			$objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':I'.($i));
			$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i ,@number_format($sum_transaction_deposit));
			$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i ,@number_format($sum_transaction_withdrawal));
			$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i ,@number_format($transaction_balance));
			$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i ,"บาท");
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i).':L'.($i))->applyFromArray($borderBottom);
			foreach(range('A','L') as $columnID) {
				$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderTop);
				$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderLeft);
				$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderRight);
				
				$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderLeft);
				$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderRight);
				$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderBottom);
				if(in_array($columnID, array('I','K'))){
					$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				}else{
					$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}	
			}
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':L'.$i_bottom)->applyFromArray($footerStyle);
		$sheet++;
	}
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานการทำรายการ ประจำวัน'.$file_name_text.'.xlsx"');
header('Cache-Control: max-age=0');		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>