<?php
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
		'size'  => 15,
		'name'  => 'Angsana New'
	)
);
$headerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 15,
		'name'  => 'Angsana New'
	)
);
$titleStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 15,
		'name'  => 'Angsana New'
	)
);
$footerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 14,
		'name'  => 'AngsanaUPC'
	)
);

$sheet = 0;
$i=0;
$objPHPExcel->createSheet($sheet);
$objPHPExcel->setActiveSheetIndex($sheet);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':J'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $_SESSION['COOP_NAME'] ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':J'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "งบกำไรขาดทุน" ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':J'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ณ วันที่ 31 ธันวาคม ".($year+543)." และ ".($year+542) ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=2;
$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , ($year+543) ) ;
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':J'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , ($year+542) ) ;
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , 'บาท');
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , '%');
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , 'บาท');
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , '%');
$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(2.71);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(2.57);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(36.43);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14.71);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(1.14);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(7);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(1);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14.14);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(1);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(7.29);

$account_chart = array();
if(!empty($rs)){
	foreach(@$rs as $key => $row){ 
		$account_chart[@$row['account_chart_id']] = @$row['account_chart'];
	}
}

$data = array();
if(!empty($rs_detail)){
	foreach(@$rs_detail as $key => $row_detail){ 
		@$year_account = date('Y',strtotime(@$row_detail['account_datetime']));
		@$data[@$year_account][@$row_detail['account_chart_id']]['account_amount'] += @$row_detail['account_amount'];
		@$data[@$year_account]['sum_all'] += @$row_detail['account_amount'];
	}
}

$count_account_chart = count($account_chart);
$j=1;
$percent = array();
foreach($account_chart as $key => $value){
	$i+=1;
	if(@$data[$year]['sum_all']>0){
		@$percent[$year][$key] = $data[$year][$key]['account_amount'] * 100 / $data[$year]['sum_all'];
		@$percent[$year]['sum_all'] += $percent[$year][$key];
	}
	if(@$data[($year-1)]['sum_all']>0){
		@$percent[($year-1)][$key] = $data[$year][$key]['account_amount'] * 100 / $data[($year-1)]['sum_all'];
		@$percent[($year-1)]['sum_all'] += $percent[($year-1)][$key];
	}

	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i,$value ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i,@$data[$year][$key]['account_amount']>0?" ".number_format(@$data[$year][$key]['account_amount'],2):'-');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i,@$data[$year][$key]['account_amount']>0?" ".number_format(@$percent[$year][$key],2):'-');
	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i,@$data[($year-1)][$key]['account_amount']>0?" ".number_format(@$data[($year-1)][$key]['account_amount'],2):'-');
	$objPHPExcel->getActiveSheet()->SetCellValue('J'.$i,@$data[($year-1)][$key]['account_amount']>0?" ".number_format(@$percent[($year-1)][$key],2):'-');
	if($j==$count_account_chart){
		$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($borderBottom);
	}
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($textStyleArray);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$j++;
}

$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, 'กำไร (ขาดทุน)สุทธิ' ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i,@$data[$year]['sum_all']>0?" ".number_format(@$data[$year]['sum_all'],2):'-');
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i,@$data[$year]['sum_all']>0?" ".number_format(@$percent[$year]['sum_all'],2):'-');
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i,@$data[($year-1)]['sum_all']>0?" ".number_format(@$data[$year]['sum_all'],2):'-');
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$i,@$data[($year-1)]['sum_all']>0?" ".number_format(@$percent[($year-1)]['sum_all'],2):'-');
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray($borderBottomDouble);
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($borderBottomDouble);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray($borderBottomDouble);
$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($borderBottomDouble);

$objPHPExcel->getActiveSheet()->setTitle('sheet',2,2);
$sheet++;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานงบกำไรขาดทุน.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>