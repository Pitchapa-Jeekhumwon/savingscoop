<?php
$month_arr = array('1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม');
$title_date = "";
if($_GET['month']!='' && $_GET['year']!=''){
    $day = '';
    $month = $_GET['month'];
    $year = $_GET['year'];
    $title_date = " เดือน ".$month_arr[$month]." ปี ".($year);
}
$last_runno = 0;
$all_withdrawal = 0;
$all_deposit = 0;
$all_balance = 0;

$prev_member_id = 'x';
$total = array();
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
    'font' => array(
        'bold' => false,
        'size' => 16,
        'name' => 'Cordia New'
    )
);
$textStyle = array(
    'font' => array(
        'bold' => false,
        'size' => 16,
        'name' => 'Cordia New'
    )
);
$textStyleBold = array(
    'font' => array(
        'bold' => true,
        'size' => 16,
        'name' => 'Cordia New'
    )
);
$textStyleRed = array(
    'font' => array(
        'bold' => false,
        'size' => 16,
        'color' => array('rgb' => 'FF0000'),
        'name' => 'Cordia New'
    )
);
$textStyleGreen = array(
    'font' => array(
        'bold' => false,
        'size' => 16,
        'color' => array('rgb' => '339966'),
        'name' => 'Cordia New'
    )
);
$textStyleResult = array(
    'font' => array(
        'bold' => false,
        'size' => 16,
        'color' => array('rgb' => '3366FF'),
        'name' => 'Cordia New'
    )
);
$headerStyle = array(
    'font' => array(
        'bold' => true,
        'size' => 16,
        'name' => 'Cordia New',
        'margin'=> 10
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
    ));
$runno = 1;
$row_2 = 0;

$objPHPExcel->getActiveSheet()->getStyle('A1:M3')->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
$objPHPExcel->getActiveSheet()->SetCellValue('A1',$_SESSION['COOP_NAME']);
$objPHPExcel->getActiveSheet()->SetCellValue('A2',"วันที่พิมพ์ :".date("Y/m/d"));
$objPHPExcel->getActiveSheet()->SetCellValue('A3',"เวลาพิมพ์ :".date("h:i:s"));
$objPHPExcel->getActiveSheet()->mergeCells('C2:J2');
$objPHPExcel->getActiveSheet()->SetCellValue('C2',"รายการคืนเงินประจำเดือน เดือน".$title_date);
$objPHPExcel->getActiveSheet()->SetCellValue('M3',"ผู้พิมพ์".$_SESSION['USER_NAME']);
$i = 6;
$objPHPExcel->getActiveSheet()->mergeCells('A5:A6');
$objPHPExcel->getActiveSheet()->SetCellValue('A5' , "ลำดับ");
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(17.57);
$objPHPExcel->getActiveSheet()->mergeCells('B5:D6');
$objPHPExcel->getActiveSheet()->SetCellValue('B5', "หน่วยงาน");
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(21.71);
$objPHPExcel->getActiveSheet()->mergeCells('E5:E6');
$objPHPExcel->getActiveSheet()->SetCellValue('E5', "ทะเบียนสมาชิก");
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13.86);
$objPHPExcel->getActiveSheet()->mergeCells('F5:F6');
$objPHPExcel->getActiveSheet()->SetCellValue('F5', "ชื่อสมาชิก");
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17.29);
$objPHPExcel->getActiveSheet()->mergeCells('G5:I5');
$objPHPExcel->getActiveSheet()->SetCellValue('G5', "จ่ายเงินระหว่างเดือน");
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(17.29);

$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, "รายการ");
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14.43);
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, "รายการเรียกเก็บ");
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18.29);
$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, "เงินรอจ่ายคืน");
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18.29);

$objPHPExcel->getActiveSheet()->mergeCells('J5:K5');
$objPHPExcel->getActiveSheet()->SetCellValue('J5', "รายการเรียกเก็บ");
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(17.29);

$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, "เงินต้น");
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(14.43);
$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, "ดอกเบี้ย");
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(18.29);

$objPHPExcel->getActiveSheet()->mergeCells('L5:M5');
$objPHPExcel->getActiveSheet()->SetCellValue('L5', "เงินรอจ่ายคืน");
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(17.29);

$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i, "เงินคืน");
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(14.43);
$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i, "ดอกเบี้ย");
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(18.29);



foreach (range('A', 'M') as $columnID) {
    $objPHPExcel->getActiveSheet()->getStyle($columnID . '5')->applyFromArray($borderTop);
    $objPHPExcel->getActiveSheet()->getStyle($columnID . '5')->applyFromArray($borderLeft);
    $objPHPExcel->getActiveSheet()->getStyle($columnID . '5')->applyFromArray($borderRight);
    $objPHPExcel->getActiveSheet()->getStyle($columnID . '6')->applyFromArray($borderLeft);
    $objPHPExcel->getActiveSheet()->getStyle($columnID . '6')->applyFromArray($borderRight);
    $objPHPExcel->getActiveSheet()->getStyle($columnID . '5')->applyFromArray($borderBottom);

    $objPHPExcel->getActiveSheet()->getStyle($columnID . "5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($columnID . "6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle($columnID . "9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}
$i = 7;

$runno = $last_runno;
if (!empty($datas)) {
    foreach (@$datas as $key => $value) {
        //echo "<pre>";echo print_r($datas);exit;
        $runno++;
        $groups = substr($value['account_chart_id'],0,1);

        $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':A' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':B' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':D' . ($i ));
        $objPHPExcel->getActiveSheet()->getStyle('E' . $i . ':E' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $i . ':F' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G' . $i . ':G' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H' . $i . ':H' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('I' . $i . ':I' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J' . $i . ':J' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('K' . $i . ':K' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('L' . $i . ':L' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':M' . $i)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i,$runno);//รหัสบัญชี
        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i,$value['department_name']);//ชื่อบัญชี
        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i,$value['member_id']);//หมวดบัญชี
        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i,$value['member_name']);//ดุลบัญชี
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i,$value['deduct_code']);
        $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i,$value['principal_month']);//ประเภทบัญชี
        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i,$value['interest_month']);
        $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i,$value['principal']);
        $objPHPExcel->getActiveSheet()->SetCellValue('K' . $i,$value['interest']);
        $objPHPExcel->getActiveSheet()->SetCellValue('L' . $i,$value['principal_return']);
        $objPHPExcel->getActiveSheet()->SetCellValue('M' . $i,$value['interest_return']);

        $i++;

    }
}

$titleStyle = array(
    'font' => array(
        'bold' => true,
        'size' => 16,
        'name' => 'Cordia New'
    ));
$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A2:M2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:M3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A4:M4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานรายการผังบัญชี.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>