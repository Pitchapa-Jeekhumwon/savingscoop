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

$styleArray = array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	),
	'font' => array(
		'bold' => false,
		'size'  => 14,
		'name'  => 'AngsanaUPC'
	)
);
$styleBordArray = array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	),
	'font' => array(
		'bold' => true,
		'size'  => 14,
		'name'  => 'AngsanaUPC'
	)
);

$sheet = 0;
$i=0;
$objPHPExcel->createSheet($sheet);
$objPHPExcel->setActiveSheetIndex($sheet);
$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i , "แบบฟอร์มข้อมูลทะเบียนสมาชิกสหกรณ์และการถือหุ้น");
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i , "สหกรณ์ ".$_SESSION['COOP_NAME']);
$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i , "เลขทะเบียนสหกรณ์ ".$profile['coop_member_id']);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':I'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i , "จังหวัด ".$profile['province_name']);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i , "หมายเลขประจำตัวประชาชน");
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i , "คำนำหน้าชื่อ(นาย/นาง/นางสาว)");
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i , "ชื่อ");
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i , "สกุล");
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i , "สัญชาติ");
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i , "วันที่เป็นสมาชิก\n(dd/mm/yyyy)");
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i , "จำนวนหุ้นที่\nถือ(หุ้น)");
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i , "มูลค่าหุ้น\n(...บาทต่อ ๑ หุ้น)");
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$i , "ประเภทสมาชิก\nสามัญ = ๑\nสมทบ = ๒");
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($styleBordArray);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

foreach($datas as $data) {
    $i+=1;
    $member_date = "";
    if(!empty($data["member_date"])) {
        $date_arr = explode( '-', $data["member_date"]);
        $member_date = $date_arr[2]."/".$date_arr["1"]."/".($date_arr["0"]+543);
    }

    $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$i, $data["id_card"], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i , $data["prename_full"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i , $data["firstname_th"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i , $data["lastname_th"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i , !empty($data["nationality"]) ? $data["nationality"] : "ไทย");
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i , $member_date);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i , $data["share"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i , $data["share_value"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i , !empty($data["mem_type_code"]) ? 2 : 1);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($styleArray);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="แบบฟอร์มข้อมูลทะเบียนสมาชิกสหกรณ์และการถือหุ้น.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>