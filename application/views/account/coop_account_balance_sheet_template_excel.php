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
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $_SESSION['COOP_NAME'] ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "งบแสดงฐานะการเงิน" ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ณ วันที่ 31 ธันวาคม ".($year+543)." และ ".($year+542) ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//For new template
foreach($doc_groups as $doc) {
    $i++;
    if($doc["level"] == 0) {
        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $doc['name']);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    } else if ($doc["level"] == 1) {
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $doc['name']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    } else if ($doc["level"] == 2) {
        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $doc['name']);
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($current_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format($prev_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($textStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    } else if ($doc["level"] == 3) {
        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $doc['name']);
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($current_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format($prev_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($textStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->setUnderline(true);
    } else if ($doc["level"] == 4) {
        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $doc['name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($current_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format($prev_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($textStyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    } else if ($doc["level"] == 5) {
        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $doc['name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($current_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format($prev_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->setUnderline(true);
    } else if ($doc["level"] == 6) {
        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $doc['name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($current_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format($prev_datas[$doc['id']],2));
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_DOUBLE);
        $objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_DOUBLE);
    } else if ($doc['level'] == 7) {
        $i+=2;
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , ($year+543) ) ;
        $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , ($year+542) ) ;
        $objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $i+=1;
        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , 'หมายเหตุ' ) ;
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , 'บาท' ) ;
        $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , 'บาท' ) ;
        $objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->setUnderline(true);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }
}

$i+=3;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "หมายเหตุประกอบงบการเงินเป็นส่วนหนึ่งของงบการเงินนี้" ) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($headerStyle);

$i+=2;
$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , "วันที่ ".$this->center_function->ConvertToThaiDate(date('Y-m-d'),'0','0') ) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(7.57);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(2);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(2);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(2);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(17);
$objPHPExcel->getActiveSheet()->setTitle('sheet',2,2);
$sheet++;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานงบดุล.xlsx"');
header('Cache-Control: max-age=0');
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>