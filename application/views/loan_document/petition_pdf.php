<?php
function U2T($text)
{
	return @iconv("UTF-8", "TIS-620//IGNORE", ($text));
}
function num_format($text)
{
	if ($text != '') {
		return number_format($text, 2);
	} else {
		return '';
	}
}

$arrMM = array(1 => "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
$arrMM_short = array(1 => "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");

$pdf = new FPDI();
foreach ($path_data as $data) {
	if ($data['data_in_path'] != '') {
		if ($data['type_loan'] == "12") {
			for ($i = 0; $i < count($guarateedata);) {
				$filename = $_SERVER["DOCUMENT_ROOT"] . PROJECTPATH . "/assets/document/loan_request/" . $data['path_data'];
				$pageCount_1 = $pdf->setSourceFile($filename);
				for ($pageNo = 1; $pageNo <= $pageCount_1;) {
					$pdf->AddPage();
					$tplIdx = $pdf->importPage($pageNo);
					$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

					$pdf->SetTextColor(0, 0, 0);
					$pdf->SetAutoPageBreak(true, 0);
					$pay_type = array('cash' => 'เงินสด', 'cheque' => 'เช็คธนาคาร', 'transfer' => 'เงินโอน');
					foreach ($data['data_in_path_' . $i] as $datas) {
						if ($datas['page_no'] == $pageNo) {
							$pdf->SetXY($datas['x_point'],  $datas['y_point']);
							$pdf->AddFont($datas['fonts'], '', $datas['fonts'] . '.php');
							$pdf->SetFont($datas['fonts'], '', $datas['fonts_size']);
							if ($datas['detail'] == 'check_mark' && $datas['detail'] != '') {
								$pdf->Image('assets/images/check_mark.png', 1, 1, $datas['x_point'],  $datas['y_point']);
							} else {
								$pdf->MultiCell($datas['text_width'], $datas['text_height'], U2T($datas['data_name']), $border, $datas['text_point']);
							}
						}
					}
					$pageNo++;
				}
				$i++;
			}
		} else {
			$filename = $_SERVER["DOCUMENT_ROOT"] . PROJECTPATH . "/assets/document/loan_request/" . $data['path_data'];
			$pageCount_1 = $pdf->setSourceFile($filename);
			for ($pageNo = 1; $pageNo <= $pageCount_1;) {
				$pdf->AddPage();
				$tplIdx = $pdf->importPage($pageNo);
				$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetAutoPageBreak(true, 0);
				foreach ($data['data_in_path'] as $page => $value) {
					if(!empty($value)){
						foreach ($value as $key => $datas) {
							if (@$datas['page_no'] == $pageNo) {
							$pdf->SetXY(@$datas['x_point'],  @$datas['y_point']);
							$pdf->AddFont(@$datas['fonts'], '', @$datas['fonts'] . '.php');
							$pdf->SetFont(@$datas['fonts'], '', @$datas['fonts_size']);
								if (@$datas['detail'] == 'check_mark') {
									$pdf->Image('assets/images/check_mark.png', @$datas['x_point'],  @$datas['y_point'], 5, 5);
								} else {
									$pdf->MultiCell(@$datas['text_width'], @$datas['text_height'], U2T(@$datas['data_name']), $border, @$datas['text_point']);
								}
							}
						}
					}
				}
				$pageNo++;
			}
		}
	}
}
$pdf->Output();
