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
		'bold'  => true,
		'size'  => 18,
		'name'  => 'Angsana New'
	)
);
$subheaderStyle = array(
	'font'  => array(
		'bold'  => true,
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
$end_col = $type == 1 ? "G" : "H";

$i+=1;
$title = $type == 0 ? "ซื้อหุ้น" : ($type == 1 ? "ชำระหนี้" : ($type == 2 ? "ฝากเงิน" : "ไม่พบข้อมูล"));
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':'.$end_col.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รายงาน".$title) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$end_col.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':'.$end_col.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "KTB ".$this->center_function->ConvertToThaiDate($import_at,'1','0'));
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($subheaderStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$end_col.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$i_top = $i;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "#");
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, "วันที่เวลา");
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, "รหัสสมาชิก");
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, "ชื่อสมาชิก");
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, "REF2");
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, "รายละเอียด");
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, "ยอดเงิน");
if($type != 1) $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, "สถานะ");
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":".$end_col.$i)->applyFromArray($table);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
if($type != 1) $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);

$index = 0;
foreach($datas as $data) {
    $i+=1;
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, ++$index);
    $paydateText = "-";
    if(!empty($data['payment_date'])) {
        $paydate = substr($data['payment_date'], 4, 4)."-".substr($data['payment_date'], 2, 2)."-".substr($data['payment_date'], 0, 2);
        if(!empty($data['payment_time'])) {
            $paydate .= " ".substr($data['payment_time'], 0, 2).":".substr($data['payment_time'], 2, 2).":".substr($data['payment_time'], 4, 2);
        }
        $paydateText =  $this->center_function->ConvertToThaiDate($paydate,'1','1');
    }
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $paydateText);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, !empty($data['member_id']) ? $data['member_id'] : "-");
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, !empty($data['firstname_th']) ? $data['prename_short'].$data['firstname_th']." ".$data['lastname_th'] : "-");
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, !empty($data['ref2']) ? $data['ref2'] : "-");

    $detail = "-";
    if($type == 0) {
        $detail = "ซื้อหุ้น";
    } else if ($type == 1) {
        $txt = "";
        foreach($data['contract_number'] as $contract_number) {
            $txt .= $txt == "" ? $contract_number : ", ".$contract_number;
        }
        $detail = "ชำระหนี้ สัญญา ".$txt;
    } else if ($type == 2) {
        $txt = "";
        $acc_nos = "";
        foreach($data['deptaccount_no'] as $deptaccount_no) {
            $txt .= $txt == "" ? $deptaccount_no : ", ".$deptaccount_no;
            $acc_nos .= $acc_nos == "" ? $deptaccount_no : ",".$deptaccount_no;
        }
        $detail = "เงินฝาก ".$txt;
    }
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, $detail);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, !empty($data['amount']) ? number_format($data['amount'], 2) : "-");
    if($type != 1) $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, $type == 4 ? "ไม่พบข้อมูล" : ($data['status'] == 1 ? "รอดำเนินการ" : "ดำเนินการแล้ว"));
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.":".$end_col.$i)->applyFromArray($table);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="นำเข้าข้อมูลไฟล์ข้อมูล '."KTB ".$this->center_function->ConvertToThaiDate($import_at,'1','0').'.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>