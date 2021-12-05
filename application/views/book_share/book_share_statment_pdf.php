<?php
	function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
	function num_format($text) { 
		if($text!=''){
			return number_format($text,2);
		}else{
			return '';
		}
	}
	function format_date($text) { 
		if($text!=''){
			$date = date('d/m/Y',strtotime($text));
			$date_arr = explode('/',$date);
			$date = (int)$date_arr[0]."/".(int)$date_arr[1]."/".$date_arr[2];
			return $date;
		}else{
			return '';
		}
	}
	function add_star($text){
		if($text!=''){
			$text_arr = explode('.',$text);
			$number = $text_arr[0];
			$number_without_commas = str_replace(',','',$number);
			$decimal = @$text_arr[1]!=''?$text_arr[1]:'00';
			$count_number = strlen($number_without_commas);
			$star = '';
			$count_star = 13-$count_number;
			for($i=0;$i<=$count_star;$i++){
				$star .= '*';
			}
			$number = number_format($number_without_commas.".".$decimal,2);
			$text_return = $star.$number;
			return $text_return;
		}else{
			return '';
		}
	}
	function cal_age($birthday,$type = 'y'){//รูปแบบการเก็บค่าข้อมูลวันเกิด
		$birthday = date("Y-m-d",strtotime($birthday)); 
		$today = date("Y-m-d");//จุดต้องเปลี่ยน
		list($byear, $bmonth, $bday)= explode("-",$birthday);//จุดต้องเปลี่ยน
		list($tyear, $tmonth, $tday)= explode("-",$today);//จุดต้องเปลี่ยน
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
    $this->db->select(array('*'));
	$this->db->from('coop_mem_share_print');
	$this->db->where("member_id = '".$this->input->get('member_id')."'");
	$row_share_print = $this->db->get()->result_array();
	if(empty($row_share_print[0]['member_id'])){
		$data_insert = array();
		$data_insert['member_id'] = $this->input->get('member_id');
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		// $data_insert['book_status'] = "1";
		// $data_insert['book_number'] = "1";
		$data_insert['date_print_book'] = date("Y-m-d H:i:s");
		$data_insert['print_number_point'] = "1";
        $data_insert['show_no'] = "1";
		$this->db->insert('coop_mem_share_print', $data_insert);
	}
	if(empty($row_share_print[0]['book_number'])){
		$insert_book_number = "1";
	}else{
		$insert_book_number = $row_share_print[0]['book_number'];
	}
	$pdf = new FPDI('P','mm', array($style['width_page'], $style['height_page']));
	$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
	$border = 0;
	$number_start = $this->input->get('number_start');

	$this->db->select(array('*'));
	$this->db->from('coop_mem_share_print');
	$this->db->where("member_id = '".$this->input->get('member_id')."'");
	$row_account = $this->db->get()->result_array();
	$print_number_point = $row_account[0]['print_number_point'];

	$data = array();
	$share_id = json_decode($_GET["share_id"]);
	if (!empty($share_id)) {
		$this->db->select(array('*'));
		$this->db->from('coop_mem_share');
		$this->db->where("member_id = '".$this->input->get('member_id')."' AND share_id IN ".str_replace("]",")",str_replace("[","(",$_GET["share_id"])));
		$this->db->order_by('share_date ASC, share_id ASC');
		$data = $this->db->get()->result_array();
	}
   
	$this->db->select(array('*'));
	$this->db->from('coop_user');
	$this->db->where("user_id = '".$_SESSION['USER_ID']."'");
	$data_user = $this->db->get()->result_array();
	$data_user = $data_user[0];
 
   


    $number_now = $number_start;

	$line_start = $_GET["line_start"];
    $shows_no=$_GET["show_no"];
	if(empty($line_start)) {
	
        $line_start = $this->db->get_where("coop_mem_share_print", array(
			"member_id" => $this->input->get('member_id')
		))->result_array()[0]['print_number_point'];
       
        if($line_start==""){
			$line_start = 1;
		}
	} 
    if(empty($shows_no)) {
        $shows_no = $this->db->get_where("coop_mem_share_print", array(
            "member_id" => $this->input->get('member_id')
        ))->result_array()[0]['show_no'];
        if($shows_no==""){
			$shows_no = 1;
		}
	}
	$number_start = $line_start;
    $show_no = $shows_no;
    
	// $this->db->limit($count, $line_start);
	// $row = $this->db->get_where("coop_book_bank_stagement_rowX", array(
	// 	"style_id" => "1"
	// ))->result_array();

	
  
	$total_row = $this->db->select("count('*') as total")->get_where("coop_book_bank_stagement_row", array(
		"style_id" => "1"
	))->result_array()[0]['total'];
    
	$number_end = $total_row;
    // echo "<pre>";	print_r($number_end);exit;
	$per_page = $total_row;
	// $current_line = $number_start;
    $current_line = $number_start <=20 ? $number_start : 1 ;
	$pdf->AddPage();
	$border = 0;
	foreach ($data as $key => $value) {
		$this->db->join("coop_book_bank_stagement_row_setting", "coop_book_bank_stagement_row_setting.row_id = coop_book_bank_stagement_row.row_id", "inner");
		$row_item = $this->db->get_where("coop_book_bank_stagement_row", array(
			"style_id" => "1",
			"no" => ($current_line)
		))->result_array();
		//start line.
 
		foreach ($row_item as $k => $item) {
			$x = $item['x'];
			$y = $item['y'];
			$font_size = $item['font_size'];

			$text = $item['style_value'];
			$width = $item['width'];
			$align = $item['align'];
			$lpad_width = 22;
			// echo $text;
			// echo " ";
			if($item['style_value']=="[no]"){
            //  $text=   $current_line <=26 ? $current_line%27 : ($current_line%27) + 1;
                $text = $show_no;
                 // ($current_line%26) == 0 ? 26 :
				$text_width =  round($pdf->GetStringWidth($text),2);
			}
			if($item['style_value']=="[date]"){
				$text = date("d/m/Y", strtotime('+543 year',strtotime($value['share_date'])));
                // @$value = date('d/m/Y H:i น.',strtotime('+543 year',strtotime(@$value)));
			}
			if($item['style_value']=="[code]"){
				$text = $value['share_type'];
			}
			if($item['style_value']=="[buy_share]"){
				$text = number_format($value['share_early_value'],2);
				$text_width =  round($pdf->GetStringWidth($text),2);
				$char_width = round($pdf->GetStringWidth("*"),2);
				for ($i=$text_width; $i < $lpad_width; $i+=$char_width) { 
                    if(number_format($value['share_early_value'],2) == 0){
                        $text ='';
                    }else{
                        $text = "*".$text;
                    }
                    // $text = "*".$text;
					
				}
			}
			if($item['style_value']=="[balance]"){
				$text = number_format($value['share_collect_value'],2);
				$text_width =  round($pdf->GetStringWidth($text),2);
			}
		

			$pdf->SetFont('THSarabunNew', '', $font_size );
			$pdf->SetXY( $x, $y );
			$pdf->cell($width, 6, U2T($text), $border, 0, $align);

			$data_insert = array();
			$data_insert['print_status'] = "1";
			$data_insert['date_print_statement'] = date("Y-m-d H:i:s");
			$data_insert['book_number'] = $insert_book_number;
			$this->db->where('member_id',$this->input->get('member_id'));
			$this->db->where('share_id',$value['share_id']);
			$this->db->update('coop_mem_share', $data_insert);

		}
		//end line.
      
     
		if($current_line > $per_page){
			$current_line = 1;
         
		}
// $per_page++;
		$current_line++;
        $show_no++;
	}
    // echo "<pre>";	print_r($per_page);
    // echo "<pre>";	print_r($current_line);
    // echo "<pre>";	print_r($test);exit;
    $data_insert = array();
    $data_insert['date_print_statement'] = date("Y-m-d H:i:s");
    $data_insert['print_number_point'] = $current_line;
    $data_insert['show_no'] = $show_no;
	$data_insert['admin_id'] = $_SESSION['USER_ID'];
    $this->db->where('member_id',$this->input->get('member_id'));
    $this->db->update('coop_mem_share_print', $data_insert);


	$pdf->Output();
	exit;

	// $number_end = (( sizeof($row) + $count)-1);
	// $per_page = sizeof($row);
	// $half_page = $per_page / 2;
	// $number_count = $number_start;
	// for($a=1;$a<=50;$a++){
	// 	$first_of_page = ($per_page*$a)-($per_page);
	// 	$last_of_page = $per_page*$a;
	// 	if($last_of_page<$number_start){
	// 		continue;
	// 	}
	// 	if($first_of_page>$number_end){
	// 		break;
	// 	}
	// 	$pdf->AddPage();
	// 	$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
	// 	$pdf->SetFont('THSarabunNew', '', 13 );
	// 	$pdf->SetMargins($style['left_margin'], $style['top_margin'], $style['right_margin']);
	// 	$border = 0;
	// 	$pdf->SetTextColor(0, 0, 0);
	// 	$pdf->SetAutoPageBreak(true,0);

	// 	$y_point = 3;
	// 	if($a=='1' && $number_start=='1'){
	// 		$this->db->select(array('*'));
	// 		$this->db->from('coop_account_transaction');
	// 		$this->db->where("account_id = '".$this->input->get('account_id')."' AND (print_status <> '0' AND print_status IS NOT NULL  AND print_status <> '')");
	// 		$this->db->order_by('transaction_id DESC');
	// 		$this->db->limit(1);
	// 		$row_prev = $this->db->get()->result_array();
	// 		$row_prev = @$row_prev[0];
	// 	}
	// 	$line_height = 5.25;

	// 	for($i=0;$i<=26;$i++){
	// 		$y_point += 5.25;
	// 		if(!empty($result[$a][$i])){
	// 			$data_insert = array();
	// 			$data_insert['print_status'] = '1';
	// 			$data_insert['print_number_point'] = $number_count;
	// 			$data_insert['book_number'] = $book_number;
	// 			$this->db->where('transaction_id', $result[$a][$i]['transaction_id']);
	// 			$this->db->update('coop_account_transaction', $data_insert);
	// 			$pdf->SetXY( 1, $y_point );
	// 			$pdf->MultiCell(23, $line_height, U2T(format_date($result[$a][$i]['transaction_time'])), $border, 'C');
	// 			$pdf->SetXY( 24, $y_point );
	// 			$pdf->MultiCell(14, $line_height, U2T($result[$a][$i]['transaction_list']), $border, 'C');
	// 			if($result[$a][$i]['transaction_withdrawal']=='0' && $result[$a][$i]['transaction_deposit']=='0' && $result[$a][$i]['transaction_balance']=='0'){
	// 				$pdf->SetXY( 38, $y_point );
	// 				$pdf->MultiCell(31, $line_height, U2T(add_star(num_format('0.00'))), $border, 'R');
	// 				$pdf->SetXY( 69, $y_point );
	// 				$pdf->MultiCell(31, $line_height, U2T(add_star(num_format('0.00'))), $border, 'R');
	// 				$pdf->SetXY( 100, $y_point );
	// 				$pdf->MultiCell(31, $line_height, U2T(add_star(num_format('0.00'))), $border, 'R');
	// 			}else{
	// 				if($result[$a][$i]['transaction_withdrawal']!='0'){
	// 					$pdf->SetXY( 38, $y_point );
	// 					$pdf->MultiCell(31, $line_height, U2T(add_star(num_format($result[$a][$i]['transaction_withdrawal']))), $border, 'R');
	// 				}
	// 				if($result[$a][$i]['transaction_deposit']!='0'){
	// 					$pdf->SetXY( 69, $y_point );
	// 					$pdf->MultiCell(31, $line_height, U2T(add_star(num_format($result[$a][$i]['transaction_deposit']))), $border, 'R');
	// 				}
	// 				if($result[$a][$i]['transaction_balance']!='0'){
	// 					$pdf->SetXY( 100, $y_point );
	// 					$pdf->MultiCell(31, $line_height, U2T(add_star(num_format($result[$a][$i]['transaction_balance']))), $border, 'R');
	// 				}
	// 			}
	// 			$pdf->SetXY( 131, $y_point );
	// 			$pdf->MultiCell(15, $line_height, U2T(substr($result[$a][$i]['user_name'],0,7)), $border, 'C');

	// 			$data_insert = array();
	// 			$data_insert['last_time_print'] = date('Y-m-d H:i:s');
	// 			$data_insert['print_number_point_now'] = $number_count;
	// 			$data_insert['book_number'] = $book_number;
	// 			$this->db->where('account_id', $this->input->get('account_id'));
	// 			$this->db->update('coop_maco_account', $data_insert);
	// 		}
	// 		if(($i)==$half_page){
	// 			$y_point += 5;
	// 		}
	// 	}
	// }
	// $pdf->Output();