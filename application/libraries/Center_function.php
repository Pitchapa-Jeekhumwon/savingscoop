<?php

class Center_function
{
	function toast($message) { setcookie("toast", $message , time() + 1); }

	function toastDanger($message) { setcookie("toast_e", $message , time() + 1); }

	function mydate2date($date, $time = false, $lang = "th") {
		if($date!='') {
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
		}else{
			return '';
		}
	}

	function ConvertToSQLDate($date) {
		if(!empty($date)) {
			if(strpos($date, "/")!==false) {
				$x = explode("/", $date);
				//$x[2] = ($x[2] > 2500 ? $x[2] - 543 : $x[2]);
				$x[2] = ($x[2] - 543);
				$x[1] = sprintf("%02d", (int)$x[1]);
				$return = "{$x[2]}-{$x[1]}-{$x[0]}";
				
			} elseif(strpos($date, "-")!==false) {
				$x = explode("-", $date);
				//$x[0] = ($x[0] > 2500 ? $x[0] - 543 : $x[0]);
				$x[0] = ($x[0] - 543);
				$x[1] = sprintf("%02d", (int)$x[1]);
				$return = "{$x[0]}-{$x[1]}-{$x[2]}";
			} else $return = "0000-00-00";
		} else $return = "";
		return $return;
	}

	function ConvertToThaiDate($value,$short='1',$need_time='1',$need_time_second='0',$short_year='0') {
		$date_arr = explode(' ', $value);
		$date = $date_arr[0];
		if(isset($date_arr[1])){
			$time = $date_arr[1];
		}else{
			$time = '';
		}

		$value = $date;
		if($value!="0000-00-00" && $value !='') {
			$x=explode("-",$value);
			if($short==false)
				$arrMM=array(1=>"มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
			else
				$arrMM=array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
			// return $x[2]." ".$arrMM[(int)$x[1]]." ".($x[0]>2500?$x[0]:$x[0]+543);
			if($need_time=='1'){
				if($need_time_second == '1'){
					$time_format = $time!=''?date('H:i:s น.',strtotime($time)):'';
				}else{
					$time_format = $time!=''?date('H:i น.',strtotime($time)):'';
				}
			}else{
				$time_format = '';
			}

			$y = $x[0]>2500?$x[0]:$x[0]+543;
			if($short_year == '1')
				$y = substr($y, 2, 2);
			
			return (int)$x[2]." ".$arrMM[(int)$x[1]]." ".$y." ".$time_format;
		} else
			return "";
	}

	function ConvertToThaiDate_sub($value,$short='1',$need_time='1',$need_time_second='0',$short_year='0') {
		$date_arr = explode(' ', $value);
		$date = $date_arr[0];
		if(isset($date_arr[1])){
			$time = $date_arr[1];
		}else{
			$time = '';
		}

		$value = $date;
		if($value!="0000-00-00" && $value !='') {
			$x=explode("-",$value);
			if($short==false)
				$arrMM=array(1=>"มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
			else
				$arrMM=array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
			// return $x[2]." ".$arrMM[(int)$x[1]]." ".($x[0]>2500?$x[0]:$x[0]+543);
			if($need_time=='1'){
				if($need_time_second == '1'){
					$time_format = $time!=''?date('H:i:s น.',strtotime($time)):'';
				}else{
					$time_format = $time!=''?date('H:i น.',strtotime($time)):'';
				}
			}else{
				$time_format = '';
			}

			$y = $x[0]>2500?$x[0]:$x[0]+543;
			if($short_year == '1')
				$y = substr($y, 2, 2);

			return (int)$x[2]." ".$arrMM[(int)$x[1]]." ".substr($y,2)." ".$time_format;
		} else
			return "";
	}
	function ConvertToLastOfMonth($value,$short='1',$need_date='1',$need_time='0') {
		$date_arr = explode(' ', $value);
		$date = $date_arr[0];
		$date = date('Y-m-t',strtotime($date));
		if(isset($date_arr[1])){
			$time = $date_arr[1];
		}else{
			$time = '';
		}

		$value = $date;
		if($value!="0000-00-00") {
			$x=explode("-",$value);
			if($short==false)
				$arrMM=array(1=>"มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
			else
				$arrMM=array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
			// return $x[2]." ".$arrMM[(int)$x[1]]." ".($x[0]>2500?$x[0]:$x[0]+543);
			if($need_time=='1'){
				$time_format = $time!=''?date('H:i น.',strtotime($time)):'';
			}else{
				$time_format = '';
			}

			return ($need_date=='1'?(int)$x[2]." ":'').$arrMM[(int)$x[1]]." ".($x[0]>2500?$x[0]:$x[0]+543)." ".$time_format;
		} else
			return "<font color=\"#FF0000\">ไม่ระบุ</font>";
	}

	/**
	 * แปลงตัวเลขเป็นอักษร
	 * @example convert('25.45') => ยี่สิบห้าบาทสี่สิบห้าสตรางค์
	 * @example convert('25.45', 'unit') => ยี่สิบห้าจุดสี่ห้า
	 * @param $number
	 * @param string $ignore
	 * @return string
	 */
	function convert($number, $ignore = "") {
		$txtnum1 = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ');
		$txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน');
		$number = str_replace(",","",$number);
		$number = str_replace(" ","",$number);
		$number = str_replace("บาท","",$number);
		$number = explode(".",$number);
		if(sizeof($number) > 2) {
			return 'ทศนิยมหลายตัวนะจ๊ะ';
			exit;
		}
		$strlen = strlen($number[0]);
		$convert = '';
		for($i=0;$i<$strlen;$i++){
			$n = substr($number[0], $i,1);
			if($n!=0){
				if($i != 0 && (($i==($strlen-1)) || ($i==($strlen-7)) ) AND $n==1){
					if($strlen > 7 && substr($number[0], $strlen-8, 1) == 0 && $i==($strlen-7)){
						$convert .= $txtnum1[$n];
					}else {
						$convert .= 'เอ็ด';
					}
				}
				elseif(($i==(($strlen)-2) || $i==($strlen-8)) AND $n==2){ $convert .= 'ยี่'; }
				elseif(($i==(($strlen)-2) || $i==($strlen-8)) AND $n==1){ $convert .= ''; }
				else{ $convert .= $txtnum1[$n]; }
				$convert .= $txtnum2[$strlen-$i-1];
			}
			//หลักล้าน
			if($n==0 && $i == $strlen-7){
				$convert .= $txtnum2[$strlen-$i-1];
			}
		}

		if(!isset($number[1])) $number[1] = 0;
		if(!empty($convert)) $convert .= $ignore == 'unit' ? 'จุด': 'บาท';
		if($number[1]=='0' || $number[1]=='00' || $number[1]==''){
			$convert .= 'ถ้วน';
		}else{
			$strlen = strlen($number[1]);
			if($strlen > 1) {
				for($i=0;$i<$strlen;$i++){
					$n = substr($number[1], $i,1);
					if($n!=0){
						if($ignore == '') {
                            if ((($i != 0 && ($i == ($strlen - 1))) || ($i == ($strlen - 7) && ($strlen - $i) + 1 >= 7)) && $n == 1 && substr($number[1], 0, 1) != 0) {
                                $convert .= 'เอ็ด';
                            } elseif (($i == (($strlen) - 2) || $i == ($strlen - 8)) AND $n == 2) {
                                $convert .= 'ยี่';
                            } elseif (($i == (($strlen) - 2) || $i == ($strlen - 8)) AND $n == 1) {
                                $convert .= '';
                            } else {
                                $convert .= $txtnum1[$n];
                            }
                            $convert .= $txtnum2[$strlen - $i - 1];
                        }else{
                            $convert .= $txtnum1[$n];
						}
					}
				}
			} else {
				$n = $number[1];
				if((($i != 0 && ( $i==($strlen-1))) || ( $i==($strlen-7) &&  ($strlen-$i)+1 >= 7 )) && $n==1 && substr($number[1], 0,1) != 0){ $convert .= 'เอ็ด';}
				elseif($n==2){ $convert .= 'ยี่'; }
				elseif($n==1){ $convert .= ''; }
				else{ $convert .= $txtnum1[$n];}
				$convert .= $txtnum2[1];
			}
			$convert .= $ignore == 'unit' ? '': 'สตางค์';
		}
		return $convert;
	}
	function create_file_name($output_dir,$file_name){
		$list_dir = array();
		$cdir = scandir($output_dir);
		foreach ($cdir as $key => $value) {
			if (!in_array($value,array(".",".."))) {
				if (@is_dir(@$dir . DIRECTORY_SEPARATOR . $value)){
					$list_dir[$value] = dirToArray(@$dir . DIRECTORY_SEPARATOR . $value);
				}else{
					if(substr($value,0,8) == date('Ymd')){
						$list_dir[] = $value;
					}
				}
			}
		}
		$explode_arr=array();
		foreach($list_dir as $key => $value){
			$task = explode('.',$value);
			$task2 = explode('_',$task[0]);
			$explode_arr[] = $task2[1];
		}
		$max_run_num = sprintf("%04d",count($explode_arr)+1);
		$explode_old_file = explode('.',$file_name);
		$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
		return $new_file_name;
	}

	
	function send_mj_mail($subject, $mail_detail, $to) {
		$key_api = "0a4aaa3552724ba4956e5c8c4a2e6a1f";
		$key_secret = "6d36928aec0420193222d7ecadded7d7";

		require('Mailjet/php-mailjet-v3-simple.class.php');

		$mj = new Mailjet($key_api, $key_secret);


		$x = array();
		$x = explode(",", $to);
		foreach($x as $key => $val) {
			$params = array(
					"method" => "POST",
					"from" => "freetradecoop <noreply@upbean.co.th>",
					"to" => $val,
					"subject" => $subject,
					"html" => $mail_detail
			);

		$result = $mj->sendEmail($params);
		}
		return $result;
	}
	
	function diff_year($date_start,$date_end) {		
		$diff = date_diff(date_create($date_start ),date_create($date_end));
		$result = floor($diff->format("%a")/365);
		return $result;
	}

	function send_sms($mobile, $msg) {
		$Username	= "upbean";	//
		$Password	= "up69Bean";	//
		$Sender		= "SPKTCOOP";	//
		$Message	=urlencode(iconv("UTF-8", "TIS-620", $msg));
		
		$Parameter	=	"User={$Username}&Password={$Password}&Msnlist={$mobile}&Msg={$Message}&Sender={$Sender}";
		$API_URL		=	"http://member.smsmkt.com/SMSLink/SendMsg/index.php";
		
		$ch = curl_init();   
		curl_setopt($ch,CURLOPT_URL,$API_URL);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch,CURLOPT_POST,1); 
		curl_setopt($ch,CURLOPT_POSTFIELDS,$Parameter);
		
		$result = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $result;
	}
	
	function diff_birthday($bithdayDate) {
        $date = new DateTime($bithdayDate);
        $now  = new DateTime();
        $interval = $now->diff($date);
        return $interval->y;
    }
	
	function cal_age($birthday,$type = 'y'){     //รูปแบบการเก็บค่าข้อมูลวันเกิด
		if($birthday=="" || $birthday=="0000-00-00")
			return "n/a";
		$birthday = date("Y-m-d",strtotime($birthday)); 
		$today = date("Y-m-d");   //จุดต้องเปลี่ยน
		list($byear, $bmonth, $bday)= explode("-",$birthday);       //จุดต้องเปลี่ยน
		list($tyear, $tmonth, $tday)= explode("-",$today);                //จุดต้องเปลี่ยน
		$mbirthday = mktime(0, 0, 0, $bmonth, $bday, $byear);
		$mnow = mktime(0, 0, 0, $tmonth, $tday, $tyear );
		$mage = ($mnow - $mbirthday);
		//echo "วันเกิด $birthday"."<br>\n";
		//echo "วันที่ปัจจุบัน $today"."<br>\n";
		//echo "รับค่า $mage"."<br>\n";
		$u_y=date("Y", $mage)-1970;
		$u_m=date("m",$mage)-1;
		$u_d=date("d",$mage)-1;
		if($type=='y'){
			return $u_y;
		}else if($type=='m'){
			return $u_m;
		}else{
			return $u_d;
		}
	}

	function cal_age_ext($birthday, $type = 'y', $target = ''){
		if($birthday=="" || $birthday=="0000-00-00")
			return "n/a";
		$birthday = date("Y-m-d", strtotime($birthday));
		if($target !== "") {
			$today = date("Y-m-d", strtotime($target));
		}else{
			$today = date("Y-m-d");   //จุดต้องเปลี่ยน
		}

		list($byear, $bmonth, $bday)= explode("-",$birthday);       //จุดต้องเปลี่ยน
		list($tyear, $tmonth, $tday)= explode("-",$today);                //จุดต้องเปลี่ยน
		$mbirthday = mktime(0, 0, 0, $bmonth, $bday, $byear);
		$mnow = mktime(0, 0, 0, $tmonth, $tday, $tyear );
		$mage = ($mnow - $mbirthday);

		$u_y=date("Y", $mage)-1970;
		$u_m=date("m",$mage)-1;
		$u_d=date("d",$mage)-1;
		if($type=='y'){
			return $u_y;
		}else if($type=='m'){
			return $u_m;
		}else{
			return $u_d;
		}
	}
	
	function diff_month_interval($start,$end){	
		$datetime1 = date_create($start);
		$datetime2 = date_create($end);		
		
		$diff =  $datetime1->diff($datetime2);
		$months = $diff->y * 12 + $diff->m + $diff->d / 30;
		return (int) round($months)+1;
	}
	
	function month_arr(){	
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		return $month_arr;
	}
	function month_short_arr(){	
		$month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
		return $month_short_arr;
	}

	//public function convert_account_id($account_id){
	public function convert_account_id($account_id){
		$str = substr($account_id, 0, 3) . "-" . substr($account_id, 3, 2) . "-" . substr($account_id, 5, 5) . "-" . substr($account_id, 10);
		return $str;
	}

	//$year = ค.ศ.
	function get_days_of_year($year) { return 365;}
	
	//$date_start = 2019-01-01  ,$date_end = 2019-01-31
	function diff_day($date_start,$date_end) {		
		$diff = date_diff(date_create($date_start ),date_create($date_end));
		$result = $diff->format('%a')+1;
		return $result;
	}
	
	function format_account_number($txt = "", $prefix = true){
		$CI =& get_instance();
		$CI->load->model('Format_account_modal');
		$format_account = $CI->Format_account_modal->get_format($txt);

		if($prefix){
		    $prefix = $CI->Format_account_modal->get_prefix($txt);
		    return $prefix.substr($txt, $this->digit_run_account()*(-1));
        }

		if(@$format_account != ''){
			$pattern = @$format_account;
		}else{
			$pattern = "##-#####";
		}
		
        return vsprintf(str_replace('#', '%s', $pattern), str_split($txt));
    }
	
	//จำนวนหลักในการรันเลขบัญชีเงินฝาก
	function digit_run_account(){
		$num_digit = 5;
		return $num_digit;
	}

	function withdraw_permission($account_id){
		$CI =& get_instance();
		$CI->load->model('Format_account_modal');
		return $CI->Format_account_modal->check_withdraw_permission($account_id);
	}

	public function complete_member_id($member_id, $format = "%06d",$prefix = "S"){
		//var_dump(strpos(strtoupper($member_id), $prefix) === ""); exit;
		if(strpos(strtoupper($member_id), $prefix) !== false){
			return $member_id;
		}else{
			return sprintf($format, $member_id);
		}
	}

	function operator($a, $b, $op){
		$val = false;
		switch ($op) {
			case '>': $val = ($a > $b) ? true : false;
				break;
			case '>=': $val = ($a >= $b) ? true : false;
				break;
			case '<': $val = ($a < $b) ? true : false;
				break;
			case '<=': $val = ($a <= $b) ? true : false;
				break;
			case '==': $val = ($a == $b) ? true : false;
				break;
			case '!=': $val = ($a != $b) ? true : false;
				break;
			case '+': $val = ($a + $b);
				break;
			case '-': $val = ($a - $b);
				break;
			case '*': $val = ($a * $b);
				break;
			case '/': $val = ($a / $b);
				break;
			case '^': $val = ($a ^ $b);
				break;
			default:
				$val = false;
				break;
		}

		return $val;
	}

	function format_signed_number($number = 0, $decimal = 0){
		if($number < 0){
			return sprintf("(%s)", number_format(abs($number), $decimal));
		}else{
			return number_format(abs($number),$decimal);
		}
	}
	function encrypt($pure_string) {
		$dirty = array("+", "/", "=");
		$clean = array("_PLUS_", "_SLASH_", "_EQUALS_");
		return base64_encode(str_replace($dirty, $clean, $pure_string));
	}

	function decrypt($encrypted_string) {
		$dirty = array("+", "/", "=");
		$clean = array("_PLUS_", "_SLASH_", "_EQUALS_");
		return str_replace($clean, $dirty, base64_decode($encrypted_string));
	}

	function encrypt_text($plaintext) {
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($plaintext, $cipher, ENCRYPT_KEY, $options=OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPT_KEY, $as_binary=true);
		$ciphertext = base64_encode($iv.$hmac.$ciphertext_raw);

		return $ciphertext;
	}

	function decrypt_text($ciphertext) {
		$c = base64_decode($ciphertext);
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, $sha2len=32);
		$ciphertext_raw = substr($c, $ivlen+$sha2len);
		$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, ENCRYPT_KEY, $options=OPENSSL_RAW_DATA, $iv);
		$calcmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPT_KEY, $as_binary=true);

		return hash_equals($hmac, $calcmac) ? $original_plaintext : "";
	}

	function post_url($url, $data) {
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'timeout' => 30,
				'content' => http_build_query($data)
			),
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
			)
		);
		$context = stream_context_create($options);
		$return = file_get_contents($url, false, $context);
		return is_scalar($return) ? $return : json_encode(['error' => 'ไม่สามารถเชื่อมต่อกับระบบได้']) ;
	}

	public function rounding_calc_period_interest(){
		$CI =& get_instance();
		$CI->load->model('Setting_model');
		return $CI->Setting_model->get("rounding_calc_period_interest");
	}
	
	function convert_number_to_text($number) {
		$txtnum1 = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ');
		$txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน');
		$number = str_replace(",","",$number);
		$number = str_replace(" ","",$number);

		$number = explode(".",$number);
		if(sizeof($number) > 2) {
			return 'ทศนิยมหลายตำแหน่ง';
			exit;
		}
		$strlen = strlen($number[0]);
		$convert = '';
		for($i=0;$i<$strlen;$i++){
			$n = substr($number[0], $i,1);
			if($n!=0){
				if($i==($strlen-1) AND $n==1){ $convert .= 'เอ็ด'; }
				elseif($i==($strlen-2) AND $n==2){ $convert .= 'ยี่'; }
				elseif($i==($strlen-2) AND $n==1){ $convert .= ''; }
				else{ $convert .= $txtnum1[$n]; }
				$convert .= $txtnum2[$strlen-$i-1];
			}
		}
		if(!isset($number[1])) $number[1] = 0;

		if($number[1]=='0' || $number[1]=='00' || $number[1]==''){

		}else{
			$convert .= 'จุด';
			$strlen = strlen($number[1]);
			for($i=0;$i<$strlen;$i++){
				$n = substr($number[1], $i,1);
				if($n!=0){
					if($i==($strlen-1) AND $n==1){$convert .= 'เอ็ด';}
					elseif($i==($strlen-2) AND $n==2){$convert .= 'ยี่';}
					elseif($i==($strlen-2) AND $n==1){$convert .= '';}
					else{ $convert .= $txtnum1[$n];}
					$convert .= $txtnum2[$strlen-$i-1];
				}
			}

		}
		return $convert;
	}

    //แสดงเดือน ปี
    function ConvertToThaiDateMMYY($value,$short_m='1',$short_y='1') {
        $date_arr = explode(' ', $value);
        $date = $date_arr[0];

        $value = $date;
        if($value!="0000-00-00" && $value !='') {
            $x=explode("-",$value);
            if($short_m==false){
                $arrMM=array(1=>"มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
            }else{
                $arrMM=array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
            }

            $yyyy = ($x[0]>2500?$x[0]:$x[0]+543);
            if($short_y=='1'){
                $data_y = substr($yyyy,2,2);
            }else{
                $data_y = $yyyy;
            }

            return $arrMM[(int)$x[1]]." ".$data_y;
        } else{
            return "";
        }
    }

    function bbcoop_format_contract($str){
		$year = substr($str, 2, 2);
		$number = (int)substr(preg_replace( '(\/)' , '',$str), 4, -1);
		return $number."/".$year;
	}

	function round_satang($number, $significance = 1){
		return ( is_numeric($number) && is_numeric($significance) ) ? (ceil(round($number,2)/$significance)*$significance) : false;
	}
	
}
?>
