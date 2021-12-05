	<?php
	//define('FPDF_FONTPATH', base_url("fpdf/font/"));
	//echo base_url("fpdf/1.8.1/fpdf.php");exit;
	//include base_url("fpdf/1.8.1/fpdf.php");
	
    function GETVAR($key, $default = null, $prefix = null, $suffix = null) {
        return isset($_GET[$key]) ? $prefix . $_GET[$key] . $suffix : $prefix . $default . $suffix;
    }
	
	$mShort = array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
	$str = "" ;
	//$datetime = date("Y-m-d H:i:s");
	$datetime = $share_date;
		
	$tmp = explode(" ",$datetime);
	if( $tmp[0] != "0000-00-00" ) {
		$d = explode( "-" , $tmp[0]);
		$month = array() ;
		
		$month = $mShort ;
		
		$str = $d[2] . " " . $month[(int)$d[1]].  " ".($d[0]>2500?$d[0]:$d[0]+543);
		
		$t = strtotime($datetime);
		$str  = $str. " ".date("H:i" , $t ) . " น." ;	
	}
	
	function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", trim($text)); }
	
    $font = GETVAR('font','fontawesome-webfont1','','.php');
    
	//$pdf = new FPDF('L','mm',array(228.60,139.7));
	//$pdf = new FPDF('P','mm',array(210,148.5));
    if($setting_receipt['receipt_size_id'] == '1'){
        $pdf = new FPDF('P','mm','A4');
    }else if($setting_receipt['receipt_size_id'] == '2' ||$setting_receipt['receipt_size_id'] == '3'){
        $pdf = new FPDF('L','mm','A5');
    }else if($setting_receipt['receipt_size_id'] == '3'){
        //  $pdf = new FPDI('P','mm',array(129,141));
    }
	//$part = 0;
    $y = 0;
    $y2 = 0;
    $y3 = 0;
    $border = 0;
    if ($setting_receipt['copy_status'] == 1){
        $max_part = 2;
    }else{
        $max_part = 1;
    }
    for($part=0;$part<$max_part;$part++){
        if($setting_receipt['receipt_size_id'] == '2' || $setting_receipt['receipt_size_id'] == '3'){
            $pdf->AddPage();
        }else if($setting_receipt['receipt_size_id'] == '1'){
            if($part==0){
                $pdf->AddPage();
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
        if($setting_receipt['alpha'] == '1') {
            if (!empty($profile['coop_img'])) {
                $coop_img_alpha = explode(".", $profile['coop_img']);
                $pdf->Image('./assets/images/coop_profile/' . $coop_img_alpha[0] . '_alpha.' . $coop_img_alpha[1], 70, 45 + $y, 75, 0, 'PNG', '0');
            }
        }

        $pdf->SetFont('THSarabunNew','',14);
        $pdf->Text( 162 , 28+$y , U2T("วันที่"),'R');
        $pdf->Text( 172 , 28+$y , U2T($this->center_function->mydate2date($share_date)));
        $pdf->Text( 152 , 35+$y , U2T("เลขที่ใบเสร็จ "),'R');
        $pdf->Text( 172 , 35+$y , U2T($receipt_id));

        $pdf->SetFont('THSarabunNewB','',20);
        $pdf->Text( 95,29+$y,U2T("ใบเสร็จรับเงิน"),0,1,'C');
        $line = "______________________________________________________________________________________________________________";
        $pdf->SetFont('THSarabunNew','',14);
        $pdf->Text( 10 , 35+$y , U2T("ได้รับเงินจาก ")." ".U2T($prename_full.$name));
        $pdf->Text( 149 , 42+$y , U2T("ทะเบียนสมาชิก"),'R');
        $pdf->Text( 172 , 42+$y , U2T($member_id));
        $pdf->Text( 10 , 42+$y , U2T("สังกัด")." ".U2T(@$mem_group_name));
        $pdf->Text( 10,45+$y, U2T("$line"));

        if($part == 1 && $setting_receipt['receipt_size_id'] == '1'){
            $pdf->Cell(0, 38+29, U2T(""),0,1,'C');
        }else{
            $pdf->Cell(0, 38, U2T(""),0,1,'C');
        }

//		$pdf->Cell(0, 38+$y2, U2T(""),0,1,'C');
        $pdf->Cell(70, 5, U2T("รายการชำระ"),$border,0,'C');
        $pdf->Cell(25, 5, U2T("งวดที่"),$border,0,'C');
        $pdf->Cell(25, 5, U2T("เงินต้น"),$border,0,'C');
        $pdf->Cell(25, 5, U2T("ดอกเบี้ย"),$border,0,'C');
        $pdf->Cell(25, 5, U2T("จำนวนเงิน"),$border,0,'C');
        $pdf->Cell(25, 5, U2T("คงเหลือ"),$border,1,'C');
        $pdf->Cell(0, 0, U2T("$line"),0,1,'C');
        $pdf->Cell(0, 1, U2T("$line"),0,1,'C');
        $pdf->Cell(0, 3, U2T(""),0,1,'C');

        $i = 0;
        $sum = 0;

//        $save = "ซื้อหุ้นเพิ่มพิเศษ จำนวนหุ้น ".number_format($num_share,0)." หุ้น";
        $save = "ค่าหุ้นปกติ";
        $count = $value;

        $pdf->Cell(70, 5, U2T($save),$border,0,'L');//8
        $pdf->Cell(25, 5, U2T(""),$border,0,'C');
        $pdf->Cell(25, 5, U2T(number_format($count,2)),$border,0,'C');
        $pdf->Cell(25, 5, U2T(""),$border,0,'C');
        $pdf->Cell(25, 5, U2T(number_format($count,2)),$border,0,'R');
        $pdf->Cell(25, 5, U2T(number_format($share_collect_value,2)),$border,1,'C');
        //$pdf->Text(15,$i, U2T($save));
        //$pdf->Text(175,$i, U2T($count));
        $sum = $sum + $count;
        $i++;
        //}
        $num = 60-(($i*5)+7);
        $pdf->Cell(0, $num, U2T(""),0,1,'C');
        //$use = 135;
        $pdf->Text(10,100, U2T("$line"));
        $pdf->Cell(135, 7, U2T($this->center_function->convert($sum)),1,0,'C');
        $pdf->Cell(25, 7, U2T("รวมเงิน"),0,0,'C');
        $pdf->Cell(25, 7, U2T(number_format($sum,2)),0,0,'R');
        $pdf->Cell(25, 7, U2T(" บาท"),0,1,'L');

        $pdf->Text(30-15, 124+$y, U2T("ลงชื่อ........................................................เจ้าหน้าที่ผู้รับเงิน"));
        $pdf->Text(130, 124+$y, U2T("ลงชื่อ........................................................ผู้จัดการ/เหรัญญิก"));
        $pdf->SetXY(25-15,128+$y );
        if($setting_receipt['sign_manager'] == '1'){
            $pdf->Cell(70,0, U2T("( ".$prename_full.$name." )"),0,0,'C');
        }else{
            $pdf->Cell(70,0, U2T("(                                          )"),0,0,'C');
        }

        $pdf->SetXY( 130,128+$y3 );
        $pdf->Cell(61,0,  U2T("(                                          )"),0,0,'C');

        $pdf->SetFont('THSarabunNew','',30);
        $pdf->SetTextColor(0, 0, 204);
        $pdf->Text(97, 132+$y, U2T(@$pay_type));

        $pdf->SetTextColor(0, 0, 0);

        if($part == 1){
            $pdf->SetFont('THSarabunNew','',12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Text( 180 , 136+$y , U2T("สำเนา"),'R');
        }
        if($setting_receipt['receipt_size_id'] == '1'){
            $y += 148;
            $y2 += 148;
            $y3 += 148;
            if($part == 0){
                $pdf->SetFont('THSarabunNew','',30);
                $pdf->Text(3, $y, U2T(" - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -"),1,0,'C');
                $pdf->SetFont('THSarabunNew','',12);
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