<?php
function U2T($text)
{
    return @iconv("UTF-8", "TIS-620//IGNORE", ($text));
}
function add_star($text)
{
    if ($text != '') {
        $text_arr = explode('.', $text);
        $number = $text_arr[0];
        $number_without_commas = str_replace(',', '', $number);
        $decimal = @$text_arr[1] != '' ? $text_arr[1] : '00';
        $count_number = strlen($number_without_commas);
        $star = '';
        $count_star = 11 - $count_number;
        for ($i = 0; $i <= $count_star; $i++) {
            $star .= '+';
        }
        $number = number_format($number_without_commas . "." . $decimal, 2);
        $text_return = $star . $number;
        return $text_return;
    } else {
        return '';
    }
}
function datedmy($date, $time = false, $lang = "th")
{
    if ($date != '') {
        if ($lang == "th") {
            $tmp = explode(" ", $date);
            if ($tmp[0] != "" && $tmp[0] != "0000-00-00") {
                $d = explode("-", $tmp[0]);
                $str = $d[2] . "/" . $d[1] . "/" . ($d[0] > 2500 ? $d[0] : $d[0] + 543);
                if ($time) {
                    $t = strtotime($date);
                    $str .= " " . date("H:i", $t);
                }
            }
        } else {
            $str = empty($date) || $date == "0000-00-00 00:00:00" || $date == "0000-00-00" ? "" : date("d/m/Y" . ($time ? " H:i" : ""), strtotime($date));
        }
        return $str;
    } else {
        return '';
    }
}
function add_st($text)
{
    if ($text != '') {
        $text_arr = explode('.', $text);
        $number = $text_arr[0];
        $number_without_commas = str_replace(',', '', $number);
        $decimal = @$text_arr[1] != '' ? $text_arr[1] : '00';
        $count_number = strlen($number_without_commas);
        $star = '';
        $count_star = 17 - $count_number;
        for ($i = 0; $i <= $count_star; $i++) {
            $star .= '-';
        }
        $number = number_format($number_without_commas . "." . $decimal, 2);
        $text_return = $star . $number;
        return $text_return;
    } else {
        return '';
    }
}
function ThaiDate($value, $short = '1', $need_time = '1', $need_time_second = '0', $short_year = '0')
{
    $date_arr = explode(' ', $value);
    $date = $date_arr[0];
    if (isset($date_arr[1])) {
        $time = $date_arr[1];
    } else {
        $time = '';
    }

    $value = $date;
    if ($value != "0000-00-00" && $value != '') {
        $x = explode("-", $value);
        if ($short == false)
            $arrMM = array(1 => "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
        else

            $arrMM = array(1 => "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
        // return $x[2]." ".$arrMM[(int)$x[1]]." ".($x[0]>2500?$x[0]:$x[0]+543);
        if ($need_time == '1') {
            if ($need_time_second == '1') {
                $time_format = $time != '' ? date('H:i:s น.', strtotime($time)) : '';
            } else {
                $time_format = $time != '' ? date('H:i น.', strtotime($time)) : '';
            }
        } else {
            $time_format = '';
        }

        $y = $x[0] > 2500 ? $x[0] : $x[0] + 543;
        if ($short_year == '1')
            $y = substr($y, 2, 2);

        return (int)$x[2] . " " . $arrMM[(int)$x[1]] . " " . $y . " " . $time_format;
    } else
        return "";
}
function convertn($number)
{
    $txtnum1 = array('ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า', 'สิบ');
    $txtnum2 = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
    $number = str_replace(",", "", $number);
    $number = str_replace(" ", "", $number);
    $number = str_replace("บาท", "", $number);
    $number = explode(".", $number);
    if (sizeof($number) > 2) {
        return '';
        exit;
    }
    $strlen = strlen($number[0]);
    $convert = '';
    for ($i = 0; $i < $strlen; $i++) {
        $n = substr($number[0], $i, 1);
        if ($n != 0) {
            if ($i == ($strlen - 1) and $n == 1) {
                $convert .= 'เอ็ด';
            } elseif ($i == ($strlen - 2) and $n == 2) {
                $convert .= 'ยี่';
            } elseif ($i == ($strlen - 2) and $n == 1) {
                $convert .= '';
            } else {
                $convert .= $txtnum1[$n];
            }
            $convert .= $txtnum2[$strlen - $i - 1];
        }
    }

    $convert .= 'บาท';
    if (
        $number[1] == '0' or $number[1] == '00' or
        $number[1] == ''
    ) {
        $convert .= 'ถ้วน';
    }
    return $convert;
}



$pdf = new FPDI('P', 'mm', array(180, 155));
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AddFont('THSarabunNew', '', 'angsa.php');
$pdf->AddFont('THSarabunNewB', '', 'angsab.php');
$pdf->SetFont('THSarabunNew', '', 13);
$border = 0;
$pdf->SetTextColor(0, 0, 0);
//header
$pdf->SetXY(35, 1);
$pdf->Image('./assets/images/coop_profile/' . $_SESSION['COOP_IMG'], 7, 5, 19);
$pdf->SetXY(30, 7);
$pdf->SetFont('THSarabunNewB', '', 16);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', $_SESSION['COOP_NAME']));
//$pdf->SetXY(5, 17);
$pdf->SetXY(5, 23);
$pdf->SetFont('THSarabunNewB', 'u', 13);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "เรื่อง "));
// $pdf->SetXY(14, 17);
$pdf->SetXY(14, 23);
$pdf->SetFont('THSarabunNew', '', 13);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', (@$_GET['type_id'] != 'all') ? "ลงนามถอนเงินจาก" . @$type_deposit[@$_GET['type_id']] : ""));
$pdf->SetXY(5, 23);
$pdf->SetFont('THSarabunNewB', '', 13);
//$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "วันที่ "));
//$pdf->SetXY(14, 23);
//$pdf->SetFont('THSarabunNew', '', 13);
//$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', ThaiDate(@date('Y-m-d'), 1, 0)));
//เส้นขอบบน
$pdf->line(5, 31, 135, 31);
//เส้นกึ่งกลาง
$pdf->line(89, 31, 89, 58);
//เส้นขอบล่าง
$pdf->line(5, 58, 135, 58);
//radiobutton
$pdf->SetXY(5, 28.5);
$pdf->SetFont('THSarabunNewB', 'u', 13);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "เรียน  "));
$pdf->Image('assets/images/radio.png', 12, 38.45, -4500);
$pdf->Image('assets/images/radio.png', 12, 43.45, -4500);
$pdf->Image('assets/images/radio.png', 12, 48.45, -4500);
$pdf->Image('assets/images/radio.png', 12, 53.45, -4500);
$pdf->Image('assets/images/radio.png', 90, 38.45, -4500);
$pdf->Image('assets/images/radio.png', 90, 43.45, -4500);
$pdf->Image('assets/images/radio.png', 90, 48.45, -4500);
$pdf->Image('assets/images/radio.png', 90, 53.45, -4500);
$pdf->SetXY(16, 35);
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "ประธาน".$_SESSION['COOP_NAME'].""));
$pdf->SetXY(95, 35);
$pdf->SetFont('THSarabunNew', '', 11.5);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "เพื่อโปรดทราบ"));
$pdf->SetXY(16, 40);
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "รองประธาน".$_SESSION['COOP_NAME'].""));
$pdf->SetXY(95, 40);
$pdf->SetFont('THSarabunNew', '', 11.5);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "เพื่อโปรดพิจารณาสั่งการ"));
$pdf->SetXY(16, 45);
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "กรรมการ".$_SESSION['COOP_NAME'].""));
$pdf->SetXY(95, 45);
$pdf->SetFont('THSarabunNew', '', 11.5);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "เพื่อโปรดอนุมัติ"));
$pdf->SetXY(16, 50);
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "กรรมการ".$_SESSION['COOP_NAME'].""));
$pdf->SetXY(95, 50);
$pdf->SetFont('THSarabunNew', '', 11.5);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "เพื่อโปรดลงนาม"));
//รายการถอนเงิน
$pdf->SetXY(10, 58.5);
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->MultiCell(110, 6, iconv('UTF-8', 'cp874', (@$_GET['type_id'] != 'all') ? " ตามที่สมาชิกสหกรณ์ฯ ขอถอน" . @$type_deposit[@$_GET['type_id']] : ""));


//ข้อมูลจากดาต้าเบส

$run_no_share = 0;
$total_amount = 0;
$x = $pdf->GetX();
$y = $pdf->GetY();

foreach ($data as  $val) {
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $amount = $val['transaction_withdrawal'];
    if ($val['transaction_withdrawal'] > 0) {
        $run_no_share++;
        $pdf->SetXY($x + 3.5, $y);
        $pdf->MultiCell(10, 6, iconv('UTF-8', 'cp874', $run_no_share . "."));
        $pdf->SetXY($x + 7, $y);
        $pdf->MultiCell(90, 6, iconv('UTF-8', 'cp874', " " . $val['account_name']));
        $pdf->SetXY($x + 98, $y);
        $pdf->MultiCell(15, 6, iconv('UTF-8', 'cp874', "จำนวน"));
        $pdf->SetXY($x + 105, $y);
        $pdf->MultiCell(25, 6, iconv('UTF-8', 'cp874', number_format($amount)), 0, 'R');
        $pdf->SetXY($x + 131, $y);
        $pdf->MultiCell(10, 6, iconv('UTF-8', 'cp874', "บาท"));
        $total_amount += $amount;

        if ($y > 85) {

            $pdf->SetXY($x + 93, $y + 8);
            $pdf->SetFont('THSarabunNewB', '', 12);
            $pdf->MultiCell(50, 6, iconv('UTF-8', 'cp874', "รวมเป็นเงิน"));
            $pdf->SetXY($x + 105, $y + 8);
            $pdf->SetFont('THSarabunNewB', '', 12);
            $pdf->MultiCell(25, 6, iconv('UTF-8', 'cp874', number_format($total_amount)), 0, 'R');
            $pdf->SetXY($x + 130.95, $y + 8);
            $pdf->SetFont('THSarabunNewB', '', 12);
            $pdf->MultiCell(10, 6, iconv('UTF-8', 'cp874', "บาท"));
            $pdf->SetXY($x + 38, $y + 13);
            $pdf->SetFont('THSarabunNewB', '', 12);
            $pdf->MultiCell(100, 6, iconv('UTF-8', 'cp874', "(  " . convertn($total_amount)) . "  )", 0, 'R');

            //ลายเซ็นเจ้าหน้าที่สหกรณ์ฯ
            $pdf->SetXY(4.5, $y + 20);
            $pdf->SetFont('THSarabunNew', '', 12);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "จึงเรียนมาเพื่อโปรดลงนามในเช็คที่แนบมาพร้อมนี้"));
            $pdf->SetXY($x+2, $y + 25);
            $pdf->SetFont('THSarabunNewB', '', 20);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "__________________"));
            $pdf->SetXY($x, $y + 31.5);
            $pdf->SetFont('THSarabunNew', '', 20);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "(.....................................)"));
            $pdf->SetXY($x + 10, $y + 43);
            $pdf->SetFont('THSarabunNew', '', 12);
            $pdf->MultiCell(40, 0, iconv('UTF-8', 'cp874', "ผู้ช่วยผู้จัดการสหกรณ์ฯ"));
            $pdf->SetXY($x + 16, $y + 50);
            $pdf->SetFont('THSarabunNew', '', 12);
            $pdf->MultiCell(40, 0, iconv('UTF-8', 'cp874', "ลงนามแล้ว"));
            //ลายเซ็นผู้ช่วยผู้จัดการสหกรณ์ฯและลายเซ็นผู้จัดการสหกรณ์ฯ
            $pdf->SetXY($x+85, $y + 25);
            $pdf->SetFont('THSarabunNewB', '', 20);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "__________________"));
            $pdf->SetXY($x + 83, $y + 31.5);
            $pdf->SetFont('THSarabunNew', '', 20);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "(.....................................)"));
            $pdf->SetXY($x + 93, $y + 43);
            $pdf->SetFont('THSarabunNew', '', 12);
            $pdf->MultiCell(40, 0, iconv('UTF-8', 'cp874', "ผู้ช่วยผู้จัดการสหกรณ์ฯ"));
            $pdf->SetXY($x + 99, $y + 50);
            $pdf->SetFont('THSarabunNew', '', 12);
            $pdf->MultiCell(40, 0, iconv('UTF-8', 'cp874', "ลงนามแล้ว"));
            $pdf->SetXY($x+85, $y + 52.5);
            $pdf->SetFont('THSarabunNewB', '', 20);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "__________________"));
            $pdf->SetXY($x + 83, $y + 64);
            $pdf->SetFont('THSarabunNew', '', 20);
            $pdf->MultiCell(90, 0, iconv('UTF-8', 'cp874', "(.....................................)"));
            $pdf->SetXY($x + 95, $y + 70);
            $pdf->SetFont('THSarabunNew', '', 12);
            $pdf->MultiCell(40, 0, iconv('UTF-8', 'cp874', "ผู้จัดการสหกรณ์ฯ"));
        }
        if ($y > 85) {

            $pdf->AddPage();
            //header
            $pdf->SetXY(35, 1);
			$pdf->Image('./assets/images/coop_profile/' . $_SESSION['COOP_IMG'], 7, 5, 19);
			$pdf->SetXY(30, 7);
			$pdf->SetFont('THSarabunNewB', '', 16);
			$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', $_SESSION['COOP_NAME']));
			//$pdf->SetXY(5, 17);
			$pdf->SetXY(5, 23);
			$pdf->SetFont('THSarabunNewB', 'u', 13);
			$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "เรื่อง "));
			// $pdf->SetXY(14, 17);
			$pdf->SetXY(14, 23);
			$pdf->SetFont('THSarabunNew', '', 13);
			$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', (@$_GET['type_id'] != 'all') ? "ลงนามถอนเงินจาก" . @$type_deposit[@$_GET['type_id']] : ""));
			$pdf->SetXY(5, 23);
			$pdf->SetFont('THSarabunNewB', '', 13);
            //$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "วันที่ "));
            //$pdf->SetXY(14, 23);
            //$pdf->SetFont('THSarabunNew', '', 13);
            //$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', ThaiDate(@date('Y-m-d'), 1, 0)));
            //เส้นขอบบน
            $pdf->line(5, 31, 135, 31);
            //เส้นกึ่งกลาง
            $pdf->line(89, 31, 89, 58);
            //เส้นขอบล่าง
            $pdf->line(5, 58, 135, 58);
            //radiobutton
            $pdf->SetXY(5, 28.5);
            $pdf->SetFont('THSarabunNewB', 'u', 13);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "เรียน  "));
            $pdf->Image('assets/images/radio.png', 12, 38.45, -4500);
            $pdf->Image('assets/images/radio.png', 12, 43.45, -4500);
            $pdf->Image('assets/images/radio.png', 12, 48.45, -4500);
            $pdf->Image('assets/images/radio.png', 12, 53.45, -4500);
            $pdf->Image('assets/images/radio.png', 90, 38.45, -4500);
            $pdf->Image('assets/images/radio.png', 90, 43.45, -4500);
            $pdf->Image('assets/images/radio.png', 90, 48.45, -4500);
            $pdf->Image('assets/images/radio.png', 90, 53.45, -4500);
            $pdf->SetXY(16, 35);
            $pdf->SetFont('THSarabunNew', '', 12);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "ประธาน".$_SESSION['COOP_NAME'].""));
            $pdf->SetXY(95, 35);
            $pdf->SetFont('THSarabunNew', '', 11.5);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "เพื่อโปรดทราบ"));
            $pdf->SetXY(16, 40);
            $pdf->SetFont('THSarabunNew', '', 12);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "รองประธาน".$_SESSION['COOP_NAME'].""));
            $pdf->SetXY(95, 40);
            $pdf->SetFont('THSarabunNew', '', 11.5);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "เพื่อโปรดพิจารณาสั่งการ"));
            $pdf->SetXY(16, 45);
            $pdf->SetFont('THSarabunNew', '', 12);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "กรรมการ".$_SESSION['COOP_NAME'].""));
            $pdf->SetXY(95, 45);
            $pdf->SetFont('THSarabunNew', '', 11.5);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "เพื่อโปรดอนุมัติ"));
            $pdf->SetXY(16, 50);
            $pdf->SetFont('THSarabunNew', '', 12);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "กรรมการ".$_SESSION['COOP_NAME'].""));
            $pdf->SetXY(95, 50);
            $pdf->SetFont('THSarabunNew', '', 11.5);
            $pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "เพื่อโปรดลงนาม"));
            //รายการถอนเงิน
            $pdf->SetXY(10, 58.5);
            $pdf->SetFont('THSarabunNew', '', 12);
            $pdf->MultiCell(110, 6, iconv('UTF-8', 'cp874', (@$_GET['type_id'] != 'all') ? " ตามที่สมาชิกสหกรณ์ฯ ขอถอน" . @$type_deposit[@$_GET['type_id']] : ""));
        }
    }
}

$pdf->SetXY($x + 93, $y + 8);
$pdf->SetFont('THSarabunNewB', '', 12);
$pdf->MultiCell(50, 6, iconv('UTF-8', 'cp874', "รวมเป็นเงิน"));
$pdf->SetXY($x + 105, $y + 8);
$pdf->SetFont('THSarabunNewB', '', 12);
$pdf->MultiCell(25, 6, iconv('UTF-8', 'cp874', number_format($total_amount)), 0, 'R');
$pdf->SetXY($x + 130.95, $y + 8);
$pdf->SetFont('THSarabunNewB', '', 12);
$pdf->MultiCell(10, 6, iconv('UTF-8', 'cp874', "บาท"));
$pdf->SetXY($x + 38, $y + 13);
$pdf->SetFont('THSarabunNewB', '', 12);
$pdf->MultiCell(100, 6, iconv('UTF-8', 'cp874', "(  " . convertn($total_amount)) . "  )", 0, 'R');

//ลายเซ็นเจ้าหน้าที่สหกรณ์ฯ
$pdf->SetXY(4.5, $y + 20);
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "จึงเรียนมาเพื่อโปรดลงนามในเช็คที่แนบมาพร้อมนี้"));
$pdf->SetXY($x+2, $y + 25);
$pdf->SetFont('THSarabunNewB', '', 20);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "__________________"));
$pdf->SetXY($x, $y + 31.5);
$pdf->SetFont('THSarabunNew', '', 20);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "(.....................................)"));
$pdf->SetXY($x + 10, $y + 43);
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->MultiCell(40, 0, iconv('UTF-8', 'cp874', "ผู้ช่วยผู้จัดการสหกรณ์ฯ"));
$pdf->SetXY($x + 16, $y + 50);
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->MultiCell(40, 0, iconv('UTF-8', 'cp874', "ลงนามแล้ว"));
//ลายเซ็นผู้ช่วยผู้จัดการสหกรณ์ฯและลายเซ็นผู้จัดการสหกรณ์ฯ
$pdf->SetXY($x+85, $y + 25);
$pdf->SetFont('THSarabunNewB', '', 20);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "__________________"));
$pdf->SetXY($x + 83, $y + 31.5);
$pdf->SetFont('THSarabunNew', '', 20);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "(.....................................)"));
$pdf->SetXY($x + 93, $y + 43);
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->MultiCell(40, 0, iconv('UTF-8', 'cp874', "ผู้ช่วยผู้จัดการสหกรณ์ฯ"));
$pdf->SetXY($x + 99, $y + 50);
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->MultiCell(40, 0, iconv('UTF-8', 'cp874', "ลงนามแล้ว"));
$pdf->SetXY($x+85, $y + 52.5);
$pdf->SetFont('THSarabunNewB', '', 20);
$pdf->MultiCell(90, 10, iconv('UTF-8', 'cp874', "__________________"));
$pdf->SetXY($x + 83, $y + 64);
$pdf->SetFont('THSarabunNew', '', 20);
$pdf->MultiCell(90, 0, iconv('UTF-8', 'cp874', "(.....................................)"));
$pdf->SetXY($x + 95, $y + 70);
$pdf->SetFont('THSarabunNew', '', 12);
$pdf->MultiCell(40, 0, iconv('UTF-8', 'cp874', "ผู้จัดการสหกรณ์ฯ"));
$pdf->Output();
