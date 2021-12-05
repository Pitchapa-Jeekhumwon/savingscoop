<?php
$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
$month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

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
		'size'  => 14,
		'name'  => 'Cordia New'
	)
);
$textStyleArray = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 14,
		'name'  => 'CordiaUPC'
	)
);
$headerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 16,
		'name'  => 'Cordia New'
	)
);
$titleStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 24,
		'name'  => 'AngsanaUPC'
	)
);
$footerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 22,
		'name'  => 'AngsanaUPC'
	)
);
if(@$_GET['report_date'] != ''){
	$date_arr = explode('/',@$_GET['report_date']);
	$day = (int)@$date_arr[0];
	$month = (int)@$date_arr[1];
	$year = (int)@$date_arr[2];
	$year -= 543;
	$file_name_text = $day."_".$month_arr[$month]."_".($year+543);
}else{
	if(@$_GET['month']!='' && $_GET['year']!=''){
		$day = '';
		$month = @$_GET['month'];
		$year = (@$_GET['year']-543);
		$file_name_text = $month_arr[$month]."_".($year+543);
	}else{
		$day = '';
		$month = '';
		$year = (@$_GET['year']-543);
		$file_name_text = ($year+543);
	}
}

if($month!=''){
	$month_start = $month;
	$month_end = $month;
}else{
	$month_start = 1;
	$month_end = 12;
}
$sheet = 0;
for($m = $month_start; $m <= $month_end; $m++){
		$s_date = $year.'-'.sprintf("%02d",@$m).'-01'.' 00:00:00.000';
		$e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
		$where_check = " AND t1.createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
		$this->db->select(array('t1.id as loan_id'));
		$this->db->from('coop_loan as t1');
		$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
		$this->db->join("coop_prename as t3 ", "t2.prename_id = t3.prename_id", "left");
		$this->db->join("coop_loan_reason as t4 ", "t1.loan_reason = t4.loan_reason_id", "inner");
		$this->db->join("coop_loan_name as t5", "t1.loan_type = t5.loan_name_id", "left");
		$this->db->join("coop_loan_type as t6", "t5.loan_type_id = t6.id", "left");
		$this->db->where("t6.id = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where_check}");
		//$this->db->where("t1.loan_type = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where_check}");
		$this->db->order_by('t1.createdatetime ASC');
		$rs_check = $this->db->get()->result_array();
		$row_check = @$rs_check[0];
	
		if(@$row_check['loan_id']=='' && @$_GET['report_date']==''){
			continue;
		}
		$i=0;
		$objPHPExcel->createSheet($sheet);
		$objPHPExcel->setActiveSheetIndex($sheet);
		$i+=1;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':S'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "ทะเบียน".@$loan_type[@$_GET['loan_type']]."  เดือน  ".@$month_arr[$m]." ".(@$year+543) ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i+=1;
		$i_top = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "หนังสือกู้สำหรับ" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);	
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "ผู้กู้" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':H'.$i);	
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "จำนวนเงินกู้" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':I'.($i+2));	
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "การส่งเงินงวดชำระหนี้" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('J'.$i.':L'.$i);	
		$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , "เหตุผล" ) ; 
		
		$i+=1;
		$i_middle = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $loan_type[@$_GET['loan_type']] ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "ทะเบียน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "รหัส" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , "ชื่อ -สกุล" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.($i+1));	
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "หน่วย" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "งวดละ" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('J'.$i.':J'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , "ตั้งแต่" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':K'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , "ถึง" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('L'.$i.':L'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , "ในการขอกู้" ) ; 
		
		$i+=1;
		$i_bottom = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "วันที่" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "สมาชิก" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "พนักงาน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "งาน" ) ;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(17.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(11.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(6.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(13.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(13.29);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(11.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(19.86);
		
		foreach(range('A','M') as $columnID) {
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderTop);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderRight);
			
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_middle)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_middle)->applyFromArray($borderRight);
			
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderRight);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderBottom);
			
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_middle)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i_top.':H'.$i_top)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i_top.':L'.$i_top)->applyFromArray($borderBottom);
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_middle.':B'.$i_middle)->applyFromArray($borderBottom);
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':M'.$i_bottom)->applyFromArray($headerStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':M'.$i_bottom)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$where = '';
		if($day != ''){
			$s_date = $year.'-'.sprintf("%02d",@$m).'-'.sprintf("%02d",@$day).' 00:00:00.000';
			$e_date = $year.'-'.sprintf("%02d",@$m).'-'.sprintf("%02d",@$day).' 23:59:59.000';
			$where .= " AND t1.createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
		}else{
			$s_date = $year.'-'.sprintf("%02d",@$m).'-01'.' 00:00:00.000';
			$e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
			$where .= " AND t1.createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
		}
		$this->db->select(array('t1.id as loan_id',
								't1.contract_number',
								't1.createdatetime',
								't2.member_id',
								't2.employee_id',
								't3.prename_short',
								't2.firstname_th',
								't2.lastname_th',
								't2.level',
								't1.period_amount',
								't1.loan_amount',
								't1.money_period_1',
								't4.loan_reason'));
		$this->db->from('coop_loan as t1');
		$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
		$this->db->join("coop_prename as t3 ", "t2.prename_id = t3.prename_id", "left");
		$this->db->join("coop_loan_reason as t4 ", "t1.loan_reason = t4.loan_reason_id", "inner");
		$this->db->join("coop_loan_name as t5", "t1.loan_type = t5.loan_name_id", "left");
		$this->db->join("coop_loan_type as t6", "t5.loan_type_id = t6.id", "left");
		$this->db->where("t6.id = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where}");
		//$this->db->where("t1.loan_type = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where}");
		$this->db->order_by('t1.createdatetime ASC');
		$rs = $this->db->get()->result_array();
		$count_loan = 0;
		$loan_amount=0;
		//print_r($this->db->last_query());
		if(!empty($rs)){
			foreach($rs as $key => $row){		
				$i+=1;
				$this->db->select(array('period_count','date_period'));
				$this->db->from('coop_loan_period');
				$this->db->where("loan_id = '".@$row['loan_id']."'");
				$this->db->order_by('period_count ASC');
				$rs_period = $this->db->get()->result_array();;
				
				$first_period = '';
				$last_period = '';
				if(!empty($rs_period)){
					foreach($rs_period as $key => $row_period){	
						if(@$row_period['period_count'] == '1'){
							$first_period = @$row_period['date_period'];
						}
						$last_period = @$row_period['date_period'];
					}
				}

				$loan_amount += @$row['loan_amount'];
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , @$row['contract_number'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $this->center_function->mydate2date(@$row['createdatetime']));
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , @$row['member_id']."'" );
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$row['employee_id']."'" );
				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , @$row['prename_short'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$row['firstname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , @$row['lastname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , @$mem_group_arr[@$row['level']] );
				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format(@$row['loan_amount'],2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format(@$row['money_period_1'],2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , ($first_period)?@$month_short_arr[(int)date('m',strtotime($first_period))]." ".substr((date('Y',strtotime($first_period))+543),2,2):'' );
				$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , ($last_period)?@$month_short_arr[(int)date('m',strtotime($last_period))]." ".substr((date('Y',strtotime($last_period))+543),2,2):'' );
				$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , @$row['loan_reason'] );
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':M'.$i)->applyFromArray($textStyleArray);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':M'.$i)->applyFromArray($borderTop);
				
				foreach(range('A','M') as $columnID) {
					if(!in_array($columnID, array('E','F','G'))){
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderLeft);
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderRight);
					}
					$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderBottom);
				}
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('K'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$count_loan++;
			}
		}
		$i+=2;
		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.($i));	
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "เดือน ".$month_arr[$m] );
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , "รวม " );
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($count_loan) );
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "สัญญา " );
		$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':I'.($i));
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "เป็นเงินจำนวน " );
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format($loan_amount) );
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , "บาท " );
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':M'.$i)->applyFromArray($footerStyle);
	//$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
	//}
	$objPHPExcel->getActiveSheet()->setTitle($month_short_arr[$m].substr(($year+543),2,2));
	$sheet++;
}
//exit;	
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานเงินกู้แยกประเภท_'.$loan_type[@$_GET['loan_type']].'_'.$file_name_text.'.xlsx"');
header('Cache-Control: max-age=0');
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>