<?php
$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
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
$styleArray = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    ),
    'font'  => array(
        'bold'  => false,
        'size'  => 14,
        'name'  => 'Cordia New'
    )
);

$headerStyle = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 14,
        'name'  => 'Cordia New'
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        //'color' => array('rgb' => 'CCFFFF')
    ));
$objPHPExcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($headerStyle);

foreach(range('A','I') as $columnID) {
    $objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderTop);
    $objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderLeft);
    $objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderRight);
    $objPHPExcel->getActiveSheet()->getStyle($columnID.'4')->applyFromArray($borderLeft);
    $objPHPExcel->getActiveSheet()->getStyle($columnID.'4')->applyFromArray($borderRight);
    $objPHPExcel->getActiveSheet()->getStyle($columnID.'4')->applyFromArray($borderBottom);

    $objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($columnID.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7.43);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13.57);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(24);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13.57);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13.57);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12.71);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(13.71);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(13.71);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

$i = 3 ;
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "รหัสสมาชิก" ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "ชื่อสมาชิก" ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "เลขที่ใบเสร็จ" ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , "เงินต้น" ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , "ดอกเบี้ย" ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "รวม" ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "ประเภทจ่ายเงิน") ;
$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "วันที่คืนเงิน" ) ;

$j=1;
$sum_share = 0;
foreach($data as $key => $row){
    $i++;

    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++ ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $row['member_id']." " ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $row['prename_full'] . " " . $row['firstname_th'] . " " . $row['lastname_th'] ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , $row['receipt_id']." " ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , number_format($row['principal'], 2) ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($row['interest'], 2 ) );
    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($row['total_amount'], 2) ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , $row['pay_type_name'] ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , $this->center_function->ConvertToThaiDate($row['return_time'], '1', '0') ) ;

    $return_principal += $row['rprincipal'];
    $return_interest += $row['interest'];
    $return_sum += $row['total_amount'];
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
}
$i+=1;
$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , 'ยอดรวม' ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , number_format($return_principal,2) ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($return_principal,2) ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($return_principal,2) ) ;
$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$summaryStyle = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FF0000'),
        'size'  => 14,
        'name'  => 'Cordia New'
    ),
    'borders' => array(
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_MEDIUM
        )
    )
);
$objPHPExcel->getActiveSheet()->getStyle('E' . $i )->applyFromArray($summaryStyle);
$objPHPExcel->getActiveSheet()->getStyle('F' . $i )->applyFromArray($summaryStyle);
$objPHPExcel->getActiveSheet()->getStyle('G' . $i )->applyFromArray($summaryStyle);

$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
$objPHPExcel->getActiveSheet()->mergeCells('A2:I2');
$objPHPExcel->getActiveSheet()->SetCellValue('A1', "รายงานการคืนเงินรายเดือนตามการช่องทางการคืนเงิน".$_SESSION['COOP_NAME'] ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('A2', $title_date." จำนวน ".($j-1)."รายการ" ) ;

$titleStyle = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 14,
        'name'  => 'Cordia New'
    ));
$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//exit;
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานการคืนเงินรายเดือนตามการช่องทางการคืนเงิน_'.$title_date.'.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>