<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}		
	.table {
		color: #000;
	}
	@page { size: landscape; }
</style>		
	<?php
		//echo '<pre>'; print_r($_GET); echo '</pre>';
		if(@$_GET['month']!='' && @$_GET['year']!=''){
			$day = '';
			$month = $_GET['month'];
			$year = $_GET['year'];
			$title_date = " เดือน ".$month_arr[$month]." ปี ".($year);
		}else{
			$day = '';
			$month = '';
			$year = (@$_GET['year']);
			$title_date = " ปี ".(@$year);
		}
		$runno = 0;
		$index = 0;
		$data_count = count($datas);
		$all_index = 1;
        $tmp_group = "";
        $tmp_lv = 0;
		foreach($datas as $member_id => $data){
			if (!empty($data['total'])) {
				$runno++;
				$depositCount = !empty($data['DEPOSIT']) ? count($data['DEPOSIT']) : 1;
				$normalCount = !empty($data['normal']) ? count($data['normal']) : 1;
				$emergentCount = !empty($data['emergent']) ? count($data['emergent']) : 1;
				$specialCount = !empty($data['special']) ? count($data['special']) : 1;
				$max_index = max(array($depositCount, $normalCount, $emergentCount, $specialCount));
				for($i = 0; $i < $max_index; $i++) {
					if ($index == 0 || $index == 24 || ( $index > 24 && (($index-24) % 30) == 0 )) {
	?>
		<div style="min-width: 1500px;"  class="page-break">
			<div class="panel panel-body " style="padding-top:10px !important;min-height: 950px;display: table;">
				<table style="width: 100%;">
				<?php 
					if($index == 0){
				?>	
					<tr>
						<td style="width:100px;vertical-align: top;">
							
						</td>
						<td class="text-center">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">สรุปรายการเรียกเก็บ รายบุคคล</h3>
							 <h3 class="title_view">
								<?php echo " ประจำ ".@$title_date;?>
							</h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							<?php
								$get_param = '?';
								foreach(@$_GET as $key => $value){
									$get_param .= $key.'='.$value.'&';
								}
								$get_param = substr($get_param,0,-1);
								
							?>
							<a class="no_print"  target="_blank" href="<?php echo base_url('/report_processor_data/coop_report_charged_person_excel'.$get_param); ?>">
								<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
							</a>
						</td>
					</tr> 
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></span>				
						</td>
					</tr>  
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
						</td>
					</tr>  
				<?php
					}
				?>
					<!-- <tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>						
						</td>
					</tr>  -->
				</table>
			
				<table class="table table-view table-center">
					<thead>
						<tr>
                            <th rowspan="2" style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th rowspan="2" style="width: 262px; text-align: left; vertical-align: middle;">สังกัด</th>
							<th rowspan="2" style="width: 262px; text-align: left; vertical-align: middle;">หน่วยงานย่อย</th>
                            <th rowspan="2" style="width: 262px; text-align: left; vertical-align: middle;">ประเภท</th>
							<th rowspan="2" style="width: 66px;vertical-align: middle;">รหัสสมาชิก</th>
							<th rowspan="2" style="width: 200px;vertical-align: middle;">ชื่อ-นามสกุล</th>
							<th rowspan="2" style="width: 64px;vertical-align: middle;">หุ้น</th>
							<?php 
								foreach($loan_type AS $key=>$row_loan_type){
							?>
							<th colspan="3" style="width: 200px;vertical-align: middle;"><?php echo str_replace('เงินกู้','',$row_loan_type['loan_type']);?></th>
							<?php
								}
							?>
							<th colspan="2" style="width: 160px;vertical-align: middle;">เงินฝาก</th>
							<th rowspan="2" style="width: 60px;vertical-align: middle;">ฌสอ สป</th>
							<th rowspan="2" style="width: 100px;vertical-align: middle;">ค่าธรรมเนียมแรกเข้า</th>
							<th rowspan="2" style="width: 34px;vertical-align: middle;">อื่นๆ</th>
							<th rowspan="2" style="width: 100px;vertical-align: middle;">ชำระหนี้ค้ำประกัน</th>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">รวม</th>
						</tr>
						<tr>
							<th style="vertical-align: middle;">เลขที่สัญญา</th> 
							<th style="vertical-align: middle;">เงินต้น</th> 
							<th style="vertical-align: middle;">ดอกเบี้ย</th> 
							<th style="vertical-align: middle;">เลขที่สัญญา</th> 
							<th style="vertical-align: middle;">เงินต้น</th> 
							<th style="vertical-align: middle;">ดอกเบี้ย</th> 
							<th style="vertical-align: middle;">เลขที่สัญญา</th> 
							<th style="vertical-align: middle;">เงินต้น</th> 
							<th style="vertical-align: middle;">ดอกเบี้ย</th> 
							<th stype="vertical-align: middle;">เลขบัญชี</th>
							<th stype="vertical-align: middle;">จำนวนเงิน</th>
						</tr>
					</thead>
					<tbody>
					<?php
					}

					if($tmp_group == ""){
					    $tmp_group = $data['mem_group_name2'];
                        $tmp_lv = $data['lv'];
                    }else if($tmp_group != "" && $tmp_group != $data['mem_group_name2']){
						$runno = 1;
                    ?>
                        <tr>
                            <td style="text-align: center;" colspan="6">รวม</td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_SHARE']) ? number_format($group_total_data[$tmp_lv.'_SHARE'], 2) : '';?></td>
                            <!-- ฉุกเฉิน -->
                            <td style="text-align: center;"></td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_emergent_principal']) ? number_format($group_total_data[$tmp_lv.'_emergent_principal'],2) : '';?></td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_emergent_interest']) ? number_format($group_total_data[$tmp_lv.'_emergent_interest'],2) : '';?></td>
                            <!-- สามัญ -->
                            <td style="text-align: center;"></td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_normal_principal']) ? number_format($group_total_data[$tmp_lv.'_normal_principal'],2) : '';?></td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_normal_interest']) ? number_format($group_total_data[$tmp_lv.'_normal_interest'],2) : '';?></td>
                            <!-- พิเศษ -->
                            <td style="text-align: center;"></td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_special_principal']) ? number_format($group_total_data[$tmp_lv.'_special_principal'],2) : '';?></td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_special_interest']) ? number_format($group_total_data[$tmp_lv.'_special_interest'],2) : '';?></td>
                            <td style="text-align: right;" ></td>
                            <td style="text-align: right;" ><?php echo !empty($group_total_data[$tmp_lv.'_DEPOSIT']) ? number_format($group_total_data[$tmp_lv.'_DEPOSIT'],2) : '';?></td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_CREMATION']) ? number_format($group_total_data[$tmp_lv.'_CREMATION'],2) : '';?></td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_REGISTER_FEE']) ? number_format($group_total_data[$tmp_lv.'_REGISTER_FEE'],2) : '';?></td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_OTHER']) ? number_format($group_total_data[$tmp_lv.'_OTHER'],2) : '';?></td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_GUARANTEE_AMOUNT']) ? number_format($group_total_data[$tmp_lv.'_GUARANTEE_AMOUNT'],2) : '';?></td>
                            <td style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_total_amount']) ? number_format($group_total_data[$tmp_lv.'_total_amount'],2) : '';?></td>
                        </tr>
                    <?php
                        $tmp_lv = $data['lv'];
                        $tmp_group = $data['mem_group_name2'];
                    }
					?>
							<tr> 
                                <td style="text-align: center;"><?php echo $runno;?></td>
								<td style="width: 262px; text-align: left;"><?php echo @$data['mem_group_name2']; ?></td>
								<td style="width: 262px; text-align: left;"><?php echo @$data['mem_group_name']; ?></td>
                                <td style="width: 262px; text-align: left;"><?php echo @$data['mem_type_name']; ?></td>
								<td style="text-align: center;"><?php echo $data['member_id'];?></td>
								<td style="text-align: left;"><?php echo $data['member_name'];?></td>
								<td style="text-align: right;"><?php echo $i == 0 && !empty($data['SHARE']) ? number_format($data['SHARE'],2) : ""; ?></td> 
								<!-- ฉุกเฉิน -->
								<td style="text-align: center;"><?php echo $data['emergent'][$data['emergent_ids'][$i]]['contract_number'];?></td> 					 
								<td style="text-align: right;"><?php echo !empty($data['emergent'][$data['emergent_ids'][$i]]['principal']) ? number_format($data['emergent'][$data['emergent_ids'][$i]]['principal'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo !empty($data['emergent'][$data['emergent_ids'][$i]]['interest']) ? number_format($data['emergent'][$data['emergent_ids'][$i]]['interest'],2) : '';?></td> 
								<!-- สามัญ -->
								<td style="text-align: center;"><?php echo $data['normal'][$data['normal_ids'][$i]]['contract_number'];?></td> 					 
								<td style="text-align: right;"><?php echo !empty($data['normal'][$data['normal_ids'][$i]]['principal']) ? number_format($data['normal'][$data['normal_ids'][$i]]['principal'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo !empty($data['normal'][$data['normal_ids'][$i]]['interest']) ? number_format($data['normal'][$data['normal_ids'][$i]]['interest'],2) : '';?></td> 
								<!-- พิเศษ -->
								<td style="text-align: center;"><?php echo $data['special'][$data['special_ids'][$i]]['contract_number'];?></td> 					 
								<td style="text-align: right;"><?php echo !empty($data['special'][$data['special_ids'][$i]]['principal']) ? number_format($data['special'][$data['special_ids'][$i]]['principal'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo !empty($data['special'][$data['special_ids'][$i]]['interest']) ? number_format($data['special'][$data['special_ids'][$i]]['interest'],2) : '';?></td> 

								<td style="text-align: right;"><?php echo $data['deposit_account_id'][$i];?></td> 					 
								<td style="text-align: right;"><?php echo !empty($data['DEPOSIT'][$i]) ? number_format($data['DEPOSIT'][$i],2) : '';?></td>
								<td style="text-align: right;"><?php echo $i == 0 && !empty($data['CREMATION']) ? number_format($data['CREMATION'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo $i == 0 && !empty($data['REGISTER_FEE']) ? number_format($data['REGISTER_FEE'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo $i == 0 && !empty($data['OTHER']) ? number_format($data['OTHER'],2) : '';?></td> 				 
								<td style="text-align: right;"><?php echo $i == 0 && !empty($data['GUARANTEE_AMOUNT']) ? number_format($data['GUARANTEE_AMOUNT'],2): '';?></td> 				 
								<td style="text-align: right;"><?php echo $i == 0 ? number_format($data['total'],2) : "";?></td> 							 
						  	</tr>						
					
					<?php

						
						// if($page == $page_all){	
					?>

					<?php
						// }
						if($data_count == $all_index) {
					?>

				   			<tr>
								<td style="text-align: center;" colspan="5">รวมทั้งสิ้น</td>				 
								<td style="text-align: right;"><?php echo !empty($total_data['SHARE']) ? number_format($total_data['SHARE']) : '';?></td> 
								<!-- Atm -->
								<td style="text-align: center;"></td> 					 
								<td style="text-align: right;"><?php echo !empty($total_data['atm_principal']) ? number_format($total_data['atm_principal'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo !empty($total_data['atm_interest']) ? number_format($total_data['atm_interest'],2) : '';?></td> 
								<!-- ฉุกเฉิน -->
								<td style="text-align: center;"></td> 					 
								<td style="text-align: right;"><?php echo !empty($total_data['emergent_principal']) ? number_format($total_data['emergent_principal'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo !empty($total_data['emergent_interest']) ? number_format($total_data['emergent_interest'],2) : '';?></td> 
								<!-- สามัญ -->
								<td style="text-align: center;"></td> 					 
								<td style="text-align: right;"><?php echo !empty($total_data['normal_principal']) ? number_format($total_data['normal_principal'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo !empty($total_data['normal_interest']) ? number_format($total_data['normal_interest'],2) : '';?></td> 
								<!-- พิเศษ -->
								<td style="text-align: center;"></td> 					 
								<td style="text-align: right;"><?php echo !empty($total_data['special_principal']) ? number_format($total_data['special_principal'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo !empty($total_data['special_interest']) ? number_format($total_data['special_interest'],2) : '';?></td> 
								<td style="text-align: right;" ></td> 					 
								<td style="text-align: right;" ><?php echo !empty($total_data['DEPOSIT']) ? number_format($total_data['DEPOSIT'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo !empty($total_data['CREMATION']) ? number_format($total_data['CREMATION'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo !empty($total_data['REGISTER_FEE']) ? number_format($total_data['REGISTER_FEE'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo !empty($total_data['OTHER']) ? number_format($total_data['OTHER'],2) : '';?></td> 					 
								<td style="text-align: right;"><?php echo !empty($total_data['GUARANTEE_AMOUNT']) ? number_format($total_data['GUARANTEE_AMOUNT'],2) : '';?></td> 				 
								<td style="text-align: right;"><?php echo !empty($total_data['total_amount']) ? number_format($total_data['total_amount'],2) : '';?></td> 	 						 
						  	</tr>

					<?php
						}
						if ($data_count == $all_index || $index == 23 || ( $index > 24 && (($index-24) % 30) == 29 )) {
					?>	  
					</tbody>    
				</table>
			</div>
		</div>
<?php
						}
						$index++;
				}
			}
			$all_index++;
		}
?>
