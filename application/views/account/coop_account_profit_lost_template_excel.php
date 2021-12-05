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
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $_SESSION['COOP_NAME'] ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "งบกำไรขาดทุน" ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ณ วันที่ 31 ธันวาคม ".($year+543)." และ ".($year+542) ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=2;
$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , ($year+543) ) ;
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , ($year+542) ) ;
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , 'บาท');
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , '%');
$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , 'บาท');
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , '%');
$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//For new template
foreach($doc_groups as $doc) {
    $i++;
    if($doc["level"] == 0) {
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $doc['name']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setUnderline(true);
    } else if ($doc["level"] == 1) {
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $doc['name']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    } else if ($doc["level"] == 2) {
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $doc['name']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setUnderline(true);
    } else if ($doc["level"] == 3) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $doc['name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , number_format($current_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , number_format(100,2));
        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($prev_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format(100,2));
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setUnderline(true);
    } else if ($doc["level"] == 4) {
        $c_percent = !empty($current_datas[$doc['parent_id']]) ? $current_datas[$doc['id']]*100/$current_datas[$doc['parent_id']] : 100;
		$p_percent = !empty($prev_datas[$doc['parent_id']]) ? $prev_datas[$doc['id']]*100/$prev_datas[$doc['parent_id']] : 100;
		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $doc['name']);
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , number_format($current_datas[$doc['id']],2));
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , number_format($c_percent,2));
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($prev_datas[$doc['id']],2));
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($p_percent,2));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($textStyleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    } else if ($doc["level"] == 5) {
        $c_percent = !empty($current_datas[$doc['parent_id']]) ? $current_datas[$doc['id']]*100/$current_datas[$doc['parent_id']] : 100;
		$p_percent = !empty($prev_datas[$doc['parent_id']]) ? $prev_datas[$doc['id']]*100/$prev_datas[$doc['parent_id']] : 100;
		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $doc['name']);
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , number_format($current_datas[$doc['id']],2));
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , number_format($c_percent,2));
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($prev_datas[$doc['id']],2));
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($p_percent,2));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($textStyleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setUnderline(true);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setUnderline(true);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setUnderline(true);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setUnderline(true);
    } else if ($doc["level"] == 6) {
        $c_percent = !empty($current_datas[$doc['parent_id']]) ? $current_datas[$doc['id']]*100/$current_datas[$doc['parent_id']] : 100;
		$p_percent = !empty($prev_datas[$doc['parent_id']]) ? $prev_datas[$doc['id']]*100/$prev_datas[$doc['parent_id']] : 100;
        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $doc['name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , number_format($current_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , number_format($c_percent,2));
        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($prev_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($p_percent,2));
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($textStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setUnderline(true);
    } else if ($doc['level'] == 7) {
        $c_percent = !empty($current_datas[$doc['parent_id']]) ? $current_datas[$doc['id']]*100/$current_datas[$doc['parent_id']] : 100;
		$p_percent = !empty($prev_datas[$doc['parent_id']]) ? $prev_datas[$doc['id']]*100/$prev_datas[$doc['parent_id']] : 100;
        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $doc['name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , number_format($current_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , number_format($c_percent,2));
        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($prev_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($p_percent,2));
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':G'.$i)->applyFromArray($textStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setUnderline(true);
    }
}

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

$objPHPExcel->getActiveSheet()->setTitle('sheet',2,2);
$sheet++;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานงบกำไรขาดทุน.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>