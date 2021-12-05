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
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $_SESSION['COOP_NAME']);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รายงานยอดคงเหลือ");
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0)) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ผู้ทำรายการ ".$_SESSION['USER_NAME']) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$i+=1;
$i_top = $i;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "ลำดับ");
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, "องค์กร");
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, "หมวดการลงทุน");
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, "หัวข้อการลงทุน");
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, "เงินลงทุน");
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, "เงินคงเหลือ");
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":F".$i)->applyFromArray($table);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

$total = 0;
$total_balance = 0;
$index = 0;
foreach($datas as $data) {
    $i++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, ++$index);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $data["org_name"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $data["type_name"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $data['name']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, number_format($data['amount'], 2));
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, number_format($data['balance'], 2));
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.":F".$i)->applyFromArray($table);
    $total += $data['amount'];
    $total_balance += $data['balance'];
}

$i++;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "รวม");
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, number_format($total,2));
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, number_format($total_balance,2));

$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":F".$i)->applyFromArray($table);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ยอดคงเหลือ.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>