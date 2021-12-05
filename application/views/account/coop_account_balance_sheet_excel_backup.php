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
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $_SESSION['COOP_NAME'] ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "งบดุล" ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ณ วันที่ 31 ธันวาคม ".($year+543)." และ ".($year+542) ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=2;
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , ($year+543) ) ;
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , ($year+542) ) ;
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , 'หมายเหตุ' ) ;
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , 'บาท' ) ;
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , 'บาท' ) ;
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
foreach($account_charts as $chart) {
    $i+=1;
    $amount = $year_budgets[$chart["account_chart_id"]]["budget_amount"];
    $prev_amount = $prev_year_budgets[$chart["account_chart_id"]]["budget_amount"];

    $lead_space = "";
    for($j = 1; $j < $chart["level"]; $j++) {
        $lead_space .= "        ";
    }

    $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $lead_space.$chart["account_chart"]) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($amount) ? number_format($amount) : "0.00" ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($prev_amount) ? number_format($prev_amount) : "0.00" ) ;
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($textStyleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}

$i+=3;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "หมายเหตุประกอบงบการเงินเป็นส่วนหนึ่งของงบการเงินนี้" ) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($headerStyle);

$i+=2;
$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , "วันที่ ".$this->center_function->ConvertToThaiDate(date('Y-m-d'),'0','0') ) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3.57);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(3);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(39);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(7.57);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(2.43);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16.86);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(1.86);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(17);

$objPHPExcel->getActiveSheet()->setTitle('sheet',2,2);
$sheet++;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานงบดุล.xlsx"');
header('Cache-Control: max-age=0');
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>