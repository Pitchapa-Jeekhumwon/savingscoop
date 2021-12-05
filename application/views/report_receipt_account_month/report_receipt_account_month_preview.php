<?php
//require('fpdf.php');

function GETVAR($key, $default = null, $prefix = null, $suffix = null) {
    return isset($_GET[$key]) ? $prefix . $_GET[$key] . $suffix : $prefix . $default . $suffix;
}

$mShort = array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
$str = "" ;
$datetime = date("Y-m-d H:i:s");
$datetime = $share_date;


function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", trim($text)); }

$font = GETVAR('font','fontawesome-webfont1','','.php');

if($setting_receipt['receipt_size_id'] == '1'){
    $pdf = new FPDF('P','mm','A4');
}else if($setting_receipt['receipt_size_id'] == '2'){
    $pdf = new FPDF('L','mm','A5');
}else if($setting_receipt['receipt_size_id'] == '3'){
//    $setting_receipt['copy_status'] = 0;
    if ($setting_receipt['copy_status'] == 1){
        $max_part = 2;
        $pdf = new FPDI('L','mm',array(258,141));
        $r_length = 127;
    }else{
        $max_part = 1;
        $pdf = new FPDI('P','mm',array(129,141));
    }
}

$border = 0;

$y = 0;
$y2 = 0;
$y3 = 0;
$new_page = 148;
foreach ($datas as $part => $data){
    if($setting_receipt['receipt_size_id'] == 1 || $setting_receipt['receipt_size_id'] == 2){
        if ($setting_receipt['copy_status'] == 1){
            $max_part = 2;
        }else{
            $max_part = 1;
        }
        for($part=0;$part<$max_part;$part++){
            if($setting_receipt['receipt_size_id'] == '2'){
                $pdf->AddPage();
            }else if($setting_receipt['receipt_size_id'] == '1'){
                if($part==0){
                    if($y == 0) {
                        $pdf->AddPage();
                    }
                }
            }
            $pdf->AddFont('H','','angsa.php');
            $pdf->AddFont('FA','',$font);
            $pdf->AddFont('THSarabunNew','','THSarabunNew.php');
            $pdf->AddFont('THSarabunNewB','','THSarabunNew-Bold.php');
            $pdf->SetAutoPageBreak('true',0);

            if($setting_receipt['header_status'] == '1') {
                $pdf->SetFont('THSarabunNew','',18);
                $pdf->Image('./assets/images/coop_profile/' . @$profile['coop_img'], 20, 4 + $y, 25);
                $pdf->Text(49, 10 + $y, U2T(@$profile['coop_name_th']), 'R');

                $pdf->SetFont('THSarabunNew', '', 12);
                if (!empty(@$profile['fax'])) {
                    $tel = 'โทรศัพท์ ' . @$profile['tel'] . ' โทรสาร ' . @$profile['fax'];
                } else {
                    $tel = 'โทรศัพท์ ' . @$profile['tel'];
                }
                $pdf->Text(49, 15 + $y, U2T(@$profile['address1'] . @$profile['address2']), 'R');
                $pdf->Text(49, 19 + $y, U2T('โทรศัพท์ ' . @$profile['tel'] . ' โทรสาร ' . @$profile['fax']), 'R');
                $pdf->Text(49, 23 + $y, U2T('E-mail : ' . @$profile['email']), 'R');
            }
            if($setting_receipt['copy_top_right'] == '1' && $part == 1) {
				$pdf->SetFont('THSarabunNewB','',20);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->Text( 180 , 15+$y , U2T("สำเนา"),'R');
			}
            if($setting_receipt['alpha'] == '1') {
                if (!empty($profile['coop_img'])) {
                    $coop_img_alpha = explode(".", $profile['coop_img']);
                    $pdf->Image('./assets/images/coop_profile/' . $coop_img_alpha[0] . '_alpha.' . $coop_img_alpha[1], 70, 45 + $y, 75, 0, 'PNG', '0');
                }
            }
            $pdf->SetFont('THSarabunNew','',14);

            $pdf->Text( 162 , 28+$y , U2T("วันที่"),'R');
            $pdf->Text( 172 , 28+$y , U2T($this->center_function->mydate2date($data['receipt_datetime'])));
            $pdf->Text( 152 , 35+$y , U2T("เลขที่ใบเสร็จ "),'R');
            $pdf->Text( 172 , 35+$y , U2T(@$data['receipt_id']));

            $pdf->SetFont('THSarabunNewB','',20);
            if($account_list == "12") {
                $pdf->Text( 95,29+$y,U2T("ใบสำคัญจ่าย"),0,1,'C');
            } else {
                $pdf->Text( 95,29+$y,U2T("ใบเสร็จรับเงิน"),0,1,'C');
            }
            $line = "______________________________________________________________________________________________________________";
            $pdf->SetFont('THSarabunNew','',14);
            $pdf->Text( 10 , 35+$y , U2T("ได้รับเงินจาก  ")." ".U2T($data['prename_short'].$data['firstname_th'].' '.$data['lastname_th']));
            $pdf->Text( 10 , 42+$y , U2T("สังกัด")." ".U2T(@$data['mem_group_name']));

            $pdf->Text( 149 , 42+$y , U2T("ทะเบียนสมาชิก"),'R');
            $pdf->Text( 172 , 42+$y , U2T($data['member_id']));

            /*if(@$pay_for_loan['contract_number']!=''){
                $pdf->Text( 80 , 40+$y , U2T("สัญญาที่ชำระ")." ".U2T(@$pay_for_loan['contract_number']));
            }*/


            $pdf->Text( 10,45+$y, U2T($line));
            if($y != 0){
                $pdf->Cell(0, 38+29, U2T(""),0,1,'C');
            }else{
                $pdf->Cell(0, 38, U2T(""),0,1,'C');
            }
            //resize if disable some col.
            if($setting_receipt['loan_int_debt'] == '1') {
                $pdf->Cell(65, 5, U2T("รายการชำระ"),0,0,'C');
            } else {
                $pdf->Cell(88, 5, U2T("รายการชำระ"),0,0,'C');
            }
            $pdf->Cell(10, 5, U2T("งวดที่"),0,0,'C');
            $pdf->Cell(23, 5, U2T("เงินต้น"),0,0,'C');
            $pdf->Cell(23, 5, U2T("ดอกเบี้ย"),0,0,'C');
            if($setting_receipt['loan_int_debt'] == '1') {
                $pdf->Cell(23, 5, U2T("ดอกคงค้าง"),0,0,'C');
            }
            $pdf->Cell(23, 5, U2T("จำนวนเงิน"),0,0,'C');
            $pdf->Cell(23, 5, U2T("คงเหลือ"),0,1,'C');
            $pdf->Cell(0, 0, U2T($line),0,1,'C');
            $pdf->Cell(0, 1, U2T($line),0,1,'C');
            $pdf->Cell(0, 3, U2T(""),0,1,'C');

            $i = 0;
            $sum = 0;
            foreach ($data['finance_month_detail'] as $type => $datas) {
                foreach ($datas as $key => $value) {
                    $transaction_text = $value['transaction_text'];
                    $total_amount = $value['principal_payment'] + $value['interest'];
                    //resize if disable some col.
                    if($setting_receipt['loan_int_debt'] == '1') {
                        $pdf->Cell(65, 5, U2T($transaction_text), 0, 0, 'L');//8
                    } else {
                        $pdf->Cell(88, 5, U2T($transaction_text), 0, 0, 'L');//8
                    }
                    $pdf->Cell(10, 5, U2T(($value['period_count'] == '') ? '' : number_format($value['period_count'], 0)), 0, 0, 'R');
                    $pdf->Cell(23, 5, U2T(number_format($value['principal_payment'], 2)), 0, 0, 'R');
                    $pdf->Cell(23, 5, U2T(number_format($value['interest'], 2)), 0, 0, 'R');
                    if($setting_receipt['loan_int_debt'] == '1') {
                        $pdf->Cell(23, 5, U2T(number_format($value['loan_interest_remain'], 2)), 0, 0, 'R');
                    }
                    $pdf->Cell(23, 5, U2T(number_format($total_amount, 2)), 0, 0, 'R');
                    $pdf->Cell(23, 5, U2T(number_format($value['loan_amount_balance'], 2)), 0, 1, 'R');
                    //$pdf->Text(15,$i, U2T($save));
                    //$pdf->Text(175,$i, U2T($count));
                    $sum = $sum + $total_amount;
                    $i++;
                }
            }
            $num = 60-(($i*5)+7);
            $pdf->Cell(0, $num, U2T(""),0,1,'C');
            $sum_convert = number_format($sum,2);
            $pdf->Text(10,100+$y, U2T("$line"));
            $pdf->SetXY(25-15,110+$y );
            $pdf->Cell(135, 7, U2T($this->center_function->convert($sum_convert)),1,0,'C');
            $pdf->Cell(25, 7, U2T("รวมเงิน"),0,0,'C');
            $pdf->Cell(25, 7, U2T(number_format($sum,2)),0,0,'R');
            $pdf->Cell(25, 7, U2T(" บาท"),0,1,'L');

            $pdf->Text(30-15, 127+$y, U2T("ลงชื่อ........................................................เจ้าหน้าที่ผู้รับเงิน"));
            $pdf->Text(130, 127+$y, U2T("ลงชื่อ........................................................ผู้จัดการ/เหรัญญิก"));
            $pdf->SetXY(25-15,134+$y );
            if(!empty($setting_receipt['sign_1'])){
                $receive_param_name = $setting_receipt['sign_1'] == 1 ? "finance_name" : ($setting_receipt['sign_1'] == 2 ? "receive_name" : ($setting_receipt['sign_1'] == 3 ? "manager_name" : ($setting_receipt['sign_1'] == 4 ? "finance_name_2" : "")));
				$sign_param_name = $setting_receipt['sign_1'] == 1 ? "signature_1" : ($setting_receipt['sign_1'] == 2 ? "signature_2" : ($setting_receipt['sign_1'] == 3 ? "signature_3" : ($setting_receipt['sign_1'] == 4 ? "signature_4" : "")));
				if (!empty($signature[$receive_param_name])) {
					$pdf->Cell(70,0, U2T("( ".$signature[$receive_param_name]." )"),0,0,'C');
				} else {
                    $pdf->Cell(70,0, U2T("(                                          )"),0,0,'C');
                }
				if(!empty($signature[$sign_param_name])) {
					$pdf->Image(base_url().PROJECTPATH.'assets/images/coop_signature/'.$signature[$sign_param_name],30,115+$y,25,'','','');
				}
			}else{
				$pdf->Cell(70,0, U2T("(                                          )"),0,0,'C');
			}

            $pdf->SetXY( 130,133+$y );
            if(!empty($setting_receipt['sign_2'])){
                $receive_param_name = $setting_receipt['sign_2'] == 1 ? "finance_name" : ($setting_receipt['sign_2'] == 2 ? "receive_name" : ($setting_receipt['sign_2'] == 3 ? "manager_name" : ($setting_receipt['sign_2'] == 4 ? "finance_name_2" : "")));
				$sign_param_name = $setting_receipt['sign_2'] == 1 ? "signature_1" : ($setting_receipt['sign_2'] == 2 ? "signature_2" : ($setting_receipt['sign_2'] == 3 ? "signature_3" : ($setting_receipt['sign_2'] == 4 ? "signature_4" : "")));
				if(!empty($signature[$receive_param_name])) {
					$pdf->Cell(70,0, U2T("( ".$signature[$receive_param_name]." )"),0,0,'C');
				} else {
                    $pdf->Cell(61,0,  U2T("(                                          )"),0,0,'C');
                }
				if(!empty($signature[$sign_param_name])) {
					$pdf->Image(base_url().PROJECTPATH.'assets/images/coop_signature/'.$signature[$sign_param_name],145,115+$y,25,'','','');
				}
			} else {
				$pdf->Cell(61,0,  U2T("(                                          )"),0,0,'C');
            }

            $pdf->SetFont('THSarabunNew','',12);
            $pdf->SetFont('THSarabunNew','',30);
            $pdf->SetTextColor(0, 0, 204);
            $pdf->Text(97, 132+$y, U2T(@$pay_type[$data['pay_type']]));

            $pdf->SetTextColor(0, 0, 0);
			if ($setting_receipt['copy_bottom_right'] == '1' && $part == 1) {
				$pdf->SetFont('THSarabunNew','',12);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->Text( 180 , 139+$y , U2T("สำเนา"),'R');
			}
            if($setting_receipt['receipt_size_id'] == '1'){
                if($y == 0){
                    $y += $new_page;
                    $y2 += $new_page;
                    $y3 += $new_page;
                    if($part == 0){
                        $pdf->SetFont('THSarabunNew','',30);
                        $pdf->Text(1, $y, U2T(" - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -"),1,0,'C');
                        $pdf->SetFont('THSarabunNew','',12);
                    }
                }else{
                    $y = 0;
                    $y2 = 0;
                    $y3 = 0;
                }
            }
        }

    }else {
        $pdf->AddPage();

        $pdf->SetFillColor(0, 0, 0);

        $pdf->AddFont('H', '', 'angsa.php');
        $pdf->AddFont('FA', '', $font);
        $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
        $pdf->AddFont('THSarabunNewB', '', 'THSarabunNew-Bold.php');
        $pdf->SetAutoPageBreak('true', 0);

        $pdf->SetTextColor(0, 0, 0);
        $y = 4;
        $x = 0;
        $x2 = 0;

        $full_name = $data['prename_short'] . ' ' . $data['firstname_th'] . ' ' . $data['lastname_th'];
        if (!empty($data['receipt_datetime'])) {
            $receipt_datetime = explode(" ", $data['receipt_datetime']);
            $receipt_datetime = explode("-", $receipt_datetime[0]);
            $receipt_datetime = $receipt_datetime[2] . '/' . $receipt_datetime[1] . '/' . $receipt_datetime[0];
        }
        for($part=0;$part<$max_part;$part++) {
            $pdf->SetFont('THSarabunNew', '', 14);
            $y = 29.5;
            $pdf->Text(33 + $x, $y, U2T(@$data["receipt_id"]), 'L');
            $pdf->Text(104 + $x, $y, U2T(@$receipt_datetime), 'L');
            $y += 6;
            $pdf->Text(33 + $x, $y, U2T($full_name), 'L');
            $pdf->Text(104 + $x, $y, U2T($data['member_id']), 'L');
            $y += 6;
            $pdf->Text(33 + $x, $y, U2T($data["mem_group_name"]), 'L');
            $pdf->Text(104 + $x, $y, U2T(number_format(@$data["sum_interest_year"], 2, '.', ',')), 'L');

            $x += $r_length;
            $x2 += $r_length;
        }

        $y_point = 60 - 2;
        $sum = 0;
        $pdf->SetFont('THSarabunNew', '', 12);
        if (!empty($data['finance_month_detail'])) {
            foreach ($data['finance_month_detail'] as $type => $datas) {
                foreach ($datas as $key => $value) {
                    if ($setting_receipt['copy_status'] == 1){
                        $x3 = 0;
                        for ($j = 0; $j < 2; $j += 1) {
                            $transaction_text = !empty($value['contract_number']) ? $value['contract_number'] : $value['transaction_text'];
                            $total = @$value["principal_payment"] + @$value["interest"];
                            $h = $pdf->GetY();
                            $pdf->SetXY(9 + $x3, $y_point);
                            if ($value['account_list_id'] == '16') {
                                $pdf->MultiCell(30 - 10, 6, U2T('ค่าหุ้นรายเดือน'), $border, 'L');
                            } else {
                                $pdf->MultiCell(30 - 10, 6, U2T($transaction_text), $border, 'L');
                            }
                            $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
                            $pdf->SetXY(29 + $x3, $y_point);
                            $pdf->MultiCell(13, 6, U2T($value["period_count"]), $border, 'R');
                            $pdf->SetXY(42 + $x3, $y_point);
                            $pdf->MultiCell(22, 6, U2T(number_format($value["principal_payment"], 2, '.', ',')), $border, 'R');
                            $pdf->SetXY(64 + $x3, $y_point);
                            $pdf->MultiCell(19, 6, U2T(number_format($value["interest"], 2, '.', ',')), $border, 'R');
                            $pdf->SetXY(83 + $x3, $y_point);
                            $pdf->MultiCell(22, 6, U2T(number_format($total, 2, '.', ',')), $border, 'R');
                            $pdf->SetXY(103 + 2 + $x3, $y_point);
                            $pdf->MultiCell(22, 6, U2T(@number_format($value['loan_amount_balance'], 2, '.', ',')), $border, 'R');
                            $x3 += $r_length;
                        }
                    }else{
                        $transaction_text = !empty($value['contract_number']) ? $value['contract_number'] : $value['transaction_text'];
                        $total = @$value["principal_payment"] + @$value["interest"];
                        $h = $pdf->GetY();
                        $pdf->SetXY(4, $y_point);
                        if ($value['account_list_id'] == '16') {
                            $pdf->MultiCell(30 - 5, 6, U2T('ค่าหุ้นรายเดือน'), $border, 'L');
                        } else {
                            $pdf->MultiCell(30 - 5, 6, U2T($transaction_text), $border, 'L');
                        }
                        $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
                        $pdf->SetXY(29, $y_point);
                        $pdf->MultiCell(13, 6, U2T($value["period_count"]), $border, 'R');
                        $pdf->SetXY(42, $y_point);
                        $pdf->MultiCell(22, 6, U2T(number_format($value["principal_payment"], 2, '.', ',')), $border, 'R');
                        $pdf->SetXY(64, $y_point);
                        $pdf->MultiCell(19, 6, U2T(number_format($value["interest"], 2, '.', ',')), $border, 'R');
                        $pdf->SetXY(83, $y_point);
                        $pdf->MultiCell(20, 6, U2T(number_format($total, 2, '.', ',')), $border, 'R');
                        $pdf->SetXY(103, $y_point);
                        $pdf->MultiCell(22, 6, U2T(@number_format($value['loan_amount_balance'], 2, '.', ',')), $border, 'R');
                    }
                    $sum += $total;
                    $y_point = $h;
                }
            }
        }

        $y_point = 95;
        if ($setting_receipt['copy_status'] == 1) {
            $x3 = 0;
            for ($j = 0; $j < 2; $j += 1) {
                $pdf->SetXY(5 + 5 + $x3, $y_point - 4);
                $pdf->MultiCell(75 - 2, 8, U2T($this->center_function->convert(sprintf("%.2f", $sum))), $border, 'L');
                $pdf->SetXY(78 + 2 + $x3, $y_point - 4);
                $pdf->MultiCell(25, 8, U2T(number_format($sum, 2, '.', ',')), $border, 'R');
                $x3 += $r_length;
            }
        }else{
            $pdf->SetXY(5, $y_point - 4);
            $pdf->MultiCell(80 - 2, 8, U2T($this->center_function->convert(sprintf("%.2f", $sum))), $border, 'L');
            $pdf->SetXY(90 - 7, $y_point - 4);
            $pdf->MultiCell(30, 8, U2T(number_format($sum, 2, '.', ',')), $border, 'L');
        }
    }
}


$pdf->Output();

if ( $is_downloan ) {
    $pdf->Output("{$member_id}{$receipt_id}.pdf", "D");
} else {
    $pdf->Output();
}

?>