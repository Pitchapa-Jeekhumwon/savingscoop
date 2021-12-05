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
    'borders' => array(
        'allborders' => array(
        'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    ),
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

$i++;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':J'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $_SESSION['COOP_NAME'] ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i++;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':J'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รายงานทะเบียนคุมเลขที่ใบเสร็จรับเงิน" ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i++;
$i_title = $i;
$period = 'วันที่ '.$this->center_function->ConvertToThaiDate($from_date,'1','0');
if($_POST["from_date"] != $_POST["thru_date"]) $period .= ' ถึง วันที่ '.$this->center_function->ConvertToThaiDate($thru_date,'1','0');
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':J'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $period) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i++;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':A'.($i+1));
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "วัน / เดือน / ปี");
$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':B'.($i+1));
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, "ทะเบียนสมาชิก");
$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':C'.($i+1));
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, "รหัสพนักงาน");
$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':D'.($i+1));
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, 'ชื่อ - สกุล');
$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':E'.($i+1));
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, 'หน่วยงาน');
$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':I'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, 'รายการรับเงิน');
$objPHPExcel->getActiveSheet()->mergeCells('J'.$i.':J'.($i+1));
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$i, 'เลขที่ใบเสร็จรับเงิน');

$i++;
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, 'หนังสือสัญญาเงินกู้ เลขที่');
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, 'เงินต้นชำระ');
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, 'ดอกเบี้ย');
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$i, 'คงเหลือ');

$objPHPExcel->getActiveSheet()->getStyle('A'.($i-1).":J".$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.($i-1).":J".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i_str = $i;
foreach($datas as $data) {
    $i++;
    $datetimes = explode(' ',$data['receipt_datetime']);
    $date_arr = explode('-',$datetimes[0]);
    $date = sprintf("%02d",$date_arr[2]).'-'.sprintf("%02d",$date_arr[1]).'-'.($date_arr[0] + 543);

    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $date);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$i, $data["member_id"], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$i, $data["employee_id"], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $data["prename_short"].$data["firstname_th"]." ".$data["lastname_th"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $data["mem_group_name"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, empty($data["loan_id"]) ? $data["transaction_text"] : $data["contract_number"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, !empty($data["principal_payment"]) ? number_format($data["principal_payment"],2) : "-");
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, !empty($data["interest"]) ? number_format($data["interest"],2) : "-");
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i, !empty($data["loan_amount_balance"]) ? number_format($data["loan_amount_balance"],2)
                                                                                             : (!empty($data["transaction_loan_amount_balance"]) ? number_format($data["transaction_loan_amount_balance"], 2)
                                                                                             : (!empty($data['share_collect_value']) ? number_format($data['share_collect_value'],2) :"-")));
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('J'.$i, $data["receipt_id"], PHPExcel_Cell_DataType::TYPE_STRING);
}
$objPHPExcel->getActiveSheet()->getStyle('A'.($i_str+1).":J".$i)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A'.($i_str+1).":C".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('D'.($i_str+1).":F".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('G'.($i_str+1).":I".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('J'.($i_str+1).":J".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPHPExcel->getActiveSheet()->setTitle('sheet',2,2);
$sheet++;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานทะเบียนคุมเลขที่ใบเสร็จรับเงิน.xlsx"');
header('Cache-Control: max-age=0');
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>