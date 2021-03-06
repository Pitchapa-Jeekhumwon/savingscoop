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
		'size'  => 16,
		'name'  => 'Cordia New'
	)
);
$textStyleArray = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 16,
		'name'  => 'Angsana New'
	)
);
$headerStyle = array(
	'font'  => array(
		'bold'  => false,
		'size'  => 16,
		'name'  => 'Angsana New'
	)
);
$titleStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 18,
		'name'  => 'TH Sarabun New'
	)
);
$footerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 14,
		'name'  => 'AngsanaUPC'
	)
);
$table = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    ),
);

$sheet = 0;
$i=0;
$objPHPExcel->createSheet($sheet);
$objPHPExcel->setActiveSheetIndex($sheet);
$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $_SESSION['COOP_NAME']);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "??????????????????????????????????????????????????????");
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0)) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "????????????????????????????????? ".$_SESSION['USER_NAME']) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$i+=1;

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);

$total = 0;
$prev_id = "x";
foreach($datas as $index => $data) {
    if($prev_id != $data['id']) {
		$total_id = 0;
		$run_no = 0;
		$prev_id = $data['id'];
		$i+=2;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i ,$data['type_name']." : ".$data['name']);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($headerStyle);

		$i++;
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, "??????????????????????????? : ".number_format($data['amount'],2));
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, "??????????????????????????????????????? : ".$data['invest_rate_text']);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, "????????????????????????????????? : ".$this->center_function->ConvertToThaiDate($data['invest_date'], '1', '0'));
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, "?????????????????????????????????????????? : ".$this->center_function->ConvertToThaiDate($data['end_date'], '1', '0'));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($headerStyle);

		$i++;
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, "?????????????????????????????????????????? : ".$data['period']);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, "??????????????? : ".($data['status'] == 1 && strtotime($data['end_date']) >= strtotime(date("Y-m-d")) ? "????????????"
																			: (strtotime($data['end_date']) < strtotime(date("Y-m-d")) ? "????????????????????????" : "???????????????????????????????????????")));
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, "?????????????????? : ".$data['org_name']);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":D".$i)->applyFromArray($headerStyle);

		$i++;
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "???????????????");
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, "????????????????????????????????????????????????????????????");
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, "???????????????????????????????????????");
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, "????????????????????????");
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, "????????????????????????");
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($table);
	}
    $i++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, ++$run_no);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $this->center_function->ConvertToThaiDate($data['interest_date'], '1', '0'));
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $data['rate']."%");
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, number_format($data['interest_amount'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $data['note']);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($table);
	$total += $data['interest_amount'];
	$total_id += $data['interest_amount'];
	if(empty($datas[($index + 1)]) || $datas[($index + 1)]['id'] != $data['id']) {
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "");
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, "");
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, "?????????");
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, number_format($total_id, 2));
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, "");
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($table);
	}
}

$i++;
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, "??????????????????????????????");
// $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, number_format($total,2));
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, "");
$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':F'.$i);

$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($headerStyle);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="????????????????????????????????????.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>