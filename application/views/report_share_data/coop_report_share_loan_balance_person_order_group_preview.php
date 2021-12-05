<?php
	if(@$_GET['export']=="excel"){
		header("Content-type: application/vnd.ms-excel;charset=utf-8;");
		header("Content-Disposition: attachment; filename=รายงานทุนเรือนหุ้นและเงินกู้คงเหลือ .xls");
		date_default_timezone_set('Asia/Bangkok');
?>
<style>
	.num {
	  mso-number-format:General;
	}
	.text{
	  mso-number-format:"\@";/*force text*/ 
	}
	.text-center{
		text-align: center;
	}
	.text-left{
		text-align: left;
	}
	.table_title{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 22px;
		font-weight: bold;
		text-align:center;
	}
	.table_title_right{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 16px;
		font-weight: bold;
		text-align:right;
	}
	.table_header_top{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 19px;
		font-weight: bold;
		text-align:center;
		border-top: thin solid black;
		border-left: thin solid black;
		border-right: thin solid black;
		color: #000000 !important;
	}
	.table_header_mid{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 19px;
		font-weight: bold;
		text-align:center;
		border-left: thin solid black;
		border-right: thin solid black;
	}
	.table_header_bot{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 19px;
		font-weight: bold;
		text-align:center;
		border-bottom: thin solid black;
		border-left: thin solid black;
		border-right: thin solid black;
	}
	.table_header_bot2{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 19px;
		font-weight: bold;
		text-align:center;
		border: thin solid black;
	}
	.table_body{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 21px;
		border: thin solid black;
		color: #000000 !important;
	}
	.table_body_right{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 21px;
		border: thin solid black;
		text-align:right;
	}
	
	h3{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 22px;
		color: #000000 !important;
	}
	
	.body-excel{
		background: #FFFFFF !important;
		width: 100%;
	}
	
	.title_view{
		font-family: AngsanaUPC, MS Sans Serif;
		color: #000000 !important;
	}
	
	.title_view_small{
		font-family: AngsanaUPC, MS Sans Serif;
		color: #000000 !important;
	}
</style>
<?php		
	}
?>
<style>
	.table {
		font-size: 10px;
		font-family: THSarabunNew;
		color: #000;
	}
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 10px;
		font-family: THSarabunNew;
		color: #000;
	}
	.title_view{
		font-size: 16px;
		font-family: THSarabunNew;	
		margin-bottom: 10px;
	    /*color: #000;	*/
	}
	.title_view_small{
		font-size: 10px;
		font-family: THSarabunNew;	
	    /*color: #000;*/
	}	
	@page { size: landscape; }
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}
	
</style>		
	<?php
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
	?>

	<?php
		//$index = 0;
		$all_index = 1;
		$page_num = 0;
		$runno = $last_runno;						
		$all_share_person = 0;
		$all_share_collect = 0;
		$all_loan_emergent_person = 0;
		$all_loan_emergent_balance = 0;
		$all_loan_normal_person = 0;
		$all_loan_normal_balance = 0;
		$all_loan_special_person = 0;
		$all_loan_special_balance = 0;
		$all_total_loan_balance = 0;
		$all_total_loan_balance = 0;
		$all_share_balance_subdivision = 0;
		$all_loan_balance_subdivision = 0;
		$member_id_past = "xx";
		//echo '<pre>'; print_r($data); echo '</pre>';
        $tmp['mem_group_id'] = array();
        $tmp['member_id'] = array();
        $member_count = 0;
		
		$all_member_count = 0;
		$all_sum_share = 0;
		$all_emergent_count = 0;
		$all_sum_emergent = 0;
		$all_normal_count = 0;
		$all_sum_normal = 0;
		$all_special_count = 0;
		$all_sum_special = 0;
        foreach ($set_loan_column as $loan_key => $set_loan_type){
            $all_count[$set_loan_type] = 0;
            $all_sum[$set_loan_type] = 0;
        }

		$page_now = 0;
		//echo $data_count; exit;
		if(!empty($data)){
			$data_count_group = count(@$data); 
			foreach(@$data  as $key_d=>$row_d) {
				$mem_group_name = $arr_mem_group[$key_d];
				$mem_group_id = $arr_mem_group_id[$key_d];
				$runno_group = 0;	
				$data_count_member_group = count(@$row_d);		
				$index = 0;
				
				$sum_share = 0;
				$sum_emergent = 0;
				$sum_normal = 0;
				$sum_special = 0;
				
				$emergent_count  = 0;
				$normal_count  = 0;
				$special_count = 0;

                foreach ($set_loan_column as $loan_key => $set_loan_type){
                    $count[$set_loan_type] = 0;
                    $sum[$set_loan_type] = 0;
                }

				foreach(@$row_d AS $da) {	
					$runno_group++;
					foreach(@$da as $key => $row){
                        $check_data = false;
                        foreach ($set_loan_column as $loan_key => $set_loan_type){
                            if($row['loan_balance'][$set_loan_type]){
                                $check_data = true;
                            }
                        }
						if (!empty($row['share_collect']) || $check_data || !empty($row['loan_atm_id'])) {
							if($member_id_past != $row['member_id']) {
								$runno = 0;
							}
							$runno++;
							//echo $index .'== 0 || '.$index.' == 24 || ( '.$index.' > 24 && '.(($index-24) % 30).' == 0 )<br>';
							//if ($index == 0 || $index == 24 || ( $index > 24 && (($index-24) % 30) == 0 )) {
							if ($index == 0 || $index == 24 || ( $index > 24 && (($index-24) % 24) == 0 ) || $runno_group == 1) {
								//$page_now = (($index-24) / 24)+2;
								$page_now++;


	?>

	<div style="width: 1500px;"  class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 950px;">
				<table style="width: 100%;">
				<?php
					//if($index == 0){
				?>
					<tr>
						<td style="width:100px;vertical-align: top;">

						</td>
						<td class="text-center" colspan="9">
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">รายงานทุนเรือนหุ้นและเงินกู้คงเหลือ
							 	<?php
									$title_date = (@$_GET['type_date'] == '1')?'ณ วันที่':'ประจำวันที่';
									echo @$title_date." ".$this->center_function->ConvertToThaiDate($start_date,0,0);
								?>
							</h3>
							<h3 class="title_view">สังกัด <?php echo @$mem_group_name;?></h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right" colspan="1">
							<?php
								if($index == 0){
							?>
								<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
								<?php
								$get_param = '?export=excel&';
								foreach(@$_GET as $key => $value){
									if(is_array($value)){
										if(!empty($value)){
											foreach($value as $key_a => $value_a){
												$get_param .= $key.'[]='.$value_a.'&';
												//%5B%5D
											}
										}
									}else{
										$get_param .= $key.'='.$value.'&';
									}
								}
								$get_param = substr($get_param,0,-1);
								//echo '<pre>'; print_r($get_param); echo '</pre>';
								?>
								<!--<a class="no_print"  target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_share_data/coop_report_share_loan_balance_person_excel'.$get_param); ?>">-->
								<a class="no_print"  target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_share_data/coop_report_share_loan_balance_person_preview'.$get_param); ?>">
									<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
								</a>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td colspan="11" style="text-align: right;">
							<span class="title_view_small">ณ วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></span>
						</td>
					</tr>
					<tr>
						<td colspan="11" style="text-align: right;">
							<span class="title_view_small">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
						</td>
					</tr>
				<?php
					//}
				?>
					<tr>
						<td colspan="11" style="text-align: right;">
							<span class="title_view_small">หน้าที่ <?php echo $page_now."/".$page_all;?></span><br>
						</td>
					</tr>
				</table>

				<table class="table table-view table-center">
					<thead>
						<tr>
							<th rowspan="2" style="width: 40px;vertical-align: middle;" class="table_header_top">ลำดับ</th>
							<th rowspan="2" style="width: 40px;vertical-align: middle;" class="table_header_top">ทะเบียนสมาชิก</th>
							<!--<th rowspan="2" style="width: 40px;vertical-align: middle;" class="table_header_top">รหัสพนักงาน</th>-->
							<th rowspan="2" style="width: 160px;vertical-align: middle;" class="table_header_top">ชื่อ - นามสกุล</th>
							<th rowspan="2" style="width: 90px;vertical-align: middle;" class="table_header_top">ทุนเรือนหุ้น</th>
							<?php
							foreach($loan_type AS $key=>$row_loan_type){
							?>
							<th colspan="2" style="width: 80px;vertical-align: middle;" class="table_header_top"><?php echo $row_loan_type['loan_type'];?></th>
							<?php }?>
						</tr>
						<tr>
							<?php
							foreach($loan_type AS $key=>$row_loan_type){
							?>
							<th style="width: 80px;vertical-align: middle;" class="table_header_top">เลขที่สัญญา</th>
							<th style="width: 80px;vertical-align: middle;" class="table_header_top">คงเหลือ</th>
							<?php }?>
						</tr>
					</thead>
					<tbody>
							<?php
								}

                            $tmp['mem_group_id'] = $row['mem_group_id'];
                            if(empty($tmp['member_id']) || $tmp['member_id'] <> $row['member_id']){
                                $member_count += 1;
                            }
                            if(!empty($row['loan_atm_contract_number'])){
                                $atm_count += 1;
                            }

                            foreach ($set_loan_column as $loan_key => $set_loan_type){
                                if(!empty($row['loan_contract_number'][$set_loan_type])){
                                    $count[$set_loan_type] +=1;
                                    if(empty($row['loan_balance'][$set_loan_type])){
                                        $row['loan_balance'][$set_loan_type] = 0;

                                    }
                                    $sum[$set_loan_type] += $row['loan_balance'][$set_loan_type];
                                }

                            }
							if(!empty($row['loan_emergent_contract_number'])){
                                $emergent_count += 1;
                            }

							if(!empty($row['loan_normal_contract_number'])){
                                $normal_count += 1;
                            }

							if(!empty($row['loan_special_contract_number'])){
                                $special_count += 1;
                            }
                            if(empty($row['share_collect'])){
                                $row['share_collect'] = 0;
                            }
                            if(empty($row['loan_atm_balance'])){
                                $row['loan_atm_balance'] = 0;
                            }
                            if(empty($row['loan_emergent_balance'])){
                                $row['loan_emergent_balance'] = 0;
                            }
                            if(empty($row['loan_normal_balance'])){
                                $row['loan_normal_balance'] = 0;
                            }
                            if(empty($row['loan_special_balance'])){
                                $row['loan_special_balance'] = 0;
                            }

                            $sum_share += $row['share_collect'];
                            $sum_atm += $row['loan_atm_balance'];
                            $sum_emergent += $row['loan_emergent_balance'];
                            $sum_normal += $row['loan_normal_balance'];
                            $sum_special += $row['loan_special_balance'];
                            $tmp['member_id'] = $row['member_id'];
							?>
							<tr>
							  <?php
							  	if($runno == '1') {
							  ?>
								<td style="text-align: center;vertical-align: top;" class="table_body"><?php echo $runno_group;?></td>
							<?php }else{?>
								<td style="text-align: center;vertical-align: top;" class="table_body"></td>
							<?php }?>
							  <td style="text-align: center;vertical-align: top;mso-number-format:'\@'" class="table_body"><?php echo @$row['member_id'];?></td>
							  <!--<td style="text-align: center;vertical-align: top;mso-number-format:'\@'" class="table_body"><?php /*echo @$row['employee_id'];*/?></td>-->
							  <td style="text-align: left;vertical-align: top;" class="table_body"><?php echo @$row['prename_full'].@$row['firstname_th']."  ".@$row['lastname_th'];?></td>
							  <?php
							  	if($runno == '1') {
							  ?>
							  <td style="text-align: right;vertical-align: top;" class="table_body"><?php echo (@$row['share_collect'] !='')?number_format(@$row['share_collect'],2):''; ?></td>
							  <?php
								  } else {
							  ?>
							  <td style="text-align: right;vertical-align: top;" class="table_body"></td>
							  <?php
								  }
                                   if(array_search('atm', $set_loan_column)=='atm') {
							  ?>
                                <td style="text-align: right;vertical-align: top;" class="table_body"><?php echo @$row['loan_atm_contract_number']; ?></td>
                                <td style="text-align: right;vertical-align: top;" class="table_body"><?php echo (@$row['loan_atm_balance'] !='')?number_format(@$row['loan_atm_balance'],2):''; ?></td>
                              <?php
                                  }
                                    foreach ($set_loan_column as $loan_key => $set_loan_type){
                                        if($set_loan_type != 'atm'){
//                                    if(array_search('emergent', $set_loan_column)=='emergent') {
                              ?>
                                <td style="text-align: right;vertical-align: top;" class="table_body"><?php echo @$row['loan_contract_number'][$set_loan_type]; ?></td>
							  <td style="text-align: right;vertical-align: top;" class="table_body"><?php echo (@$row['loan_balance'][$set_loan_type] !='')?number_format(@$row['loan_balance'][$set_loan_type],2):''; ?></td>
                              <?php
							            }
                                    }
							  ?>
							</tr>										
					<?php
						if ($runno_group == $data_count_member_group){
							echo "<tr>";
								echo "<td colspan='3' class='table_body'>รวมสังกัด ".@$mem_group_id." ".number_format($data_count_member_group,0)." ราย </td>";

								echo "<td style='text-align: right' class='table_body'>".number_format($sum_share,2)."</td>";
                            if(array_search('atm', $set_loan_column)=='atm') {
                                echo "<td style='text-align: center' class='table_body'>" . number_format($atm_count, 0) . "</td>";
                                echo "<td style='text-align: right' class='table_body'>" . number_format($sum_atm, 2) . "</td>";
                            }
                            foreach ($set_loan_column as $loan_key => $set_loan_type) {
                                if ($set_loan_type != 'atm') {
                                    echo "<td style='text-align: center' class='table_body'>" . number_format($count[$set_loan_type], 0) . "</td>";
                                    echo "<td style='text-align: right' class='table_body'>" . number_format($sum[$set_loan_type], 2) . "</td>";
                                }
                            }
							echo "</tr>";
							$all_member_count += $data_count_member_group;
							$all_sum_share += $sum_share;
                            $all_atm_count += $atm_count;
                            $all_sum_atm += $sum_atm;
							$all_emergent_count += $emergent_count;
							$all_sum_emergent += $sum_emergent;
							$all_normal_count += $normal_count;
							$all_sum_normal += $sum_normal;
							$all_special_count += $special_count;
							$all_sum_special += $sum_special;
                            foreach ($set_loan_column as $loan_key => $set_loan_type){
                                $all_count[$set_loan_type] += $count[$set_loan_type];
                                $all_sum[$set_loan_type] += $sum[$set_loan_type];
                            }
                        }

						if ($data_count == $all_index){

							echo "<tr>";
								echo "<td colspan='3' class='table_body'>รวมสังกัดทั้งหมด ".number_format($data_count_group,0)." สังกัด  จำนวนสมาชิก ".number_format($all_member_count,0)." ราย</td>";
								echo "<td style='text-align: right' class='table_body'>".number_format($all_sum_share,2)."</td>";
                                if(array_search('atm', $set_loan_column)=='atm') {
                                    echo "<td style='text-align: center' class='table_body'>" . number_format($atm_count, 0) . "</td>";
                                    echo "<td style='text-align: right' class='table_body'>" . number_format($sum_atm, 2) . "</td>";
                                }
                                foreach ($set_loan_column as $loan_key => $set_loan_type) {
                                    if ($set_loan_type != 'atm') {
                                        echo "<td style='text-align: center' class='table_body'>" . number_format($all_count[$set_loan_type], 0) . "</td>";
                                        echo "<td style='text-align: right' class='table_body'>" . number_format($all_sum[$set_loan_type], 2) . "</td>";
                                    }
                                }
							echo "</tr>";
						}	
					
						//if ($data_count == $all_index || $index == 23 || ( $index > 24 && (($index-24) % 30) == 29 )) {
						if ($data_count == $all_index || $index == 23 || ( $index > 24 && (($index-24) % 24) == 23 ) || $runno_group == $data_count_member_group) {
	
					?>

					</tbody>    
				</table>
			</div>			
		</div>
	<?php	
							}
							$member_id_past = $row['member_id'];
							$index++;
						}
						$all_index++;
					}
				}
			}
		}
		$last_runno = $runno;					
	?>
