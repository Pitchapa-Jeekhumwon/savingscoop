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
		$index = 0;
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
        $sum_share = 0;
        $sum_emergent = 0;
        $sum_normal = 0;
        $sum_special = 0;
		if(!empty($data)){
			foreach(@$data as $key => $row) {



					if (!empty($row['share_collect']) || !empty($row['loan_emergent_balance']) || !empty($row['loan_normal_balance']) || !empty($row['loan_special_balance'])) {



						if($member_id_past != $row['member_id']) {
							$runno = 0;
						}
						$runno++;
						if ($index == 0 || $index == 24 || ( $index > 24 && (($index-24) % 30) == 0 )) {


	?>	

	<div style="width: 1500px;"  class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 950px;">
				<table style="width: 100%;">
				<?php 
					if($index == 0){
				?>	
					<tr>
						<td style="width:100px;vertical-align: top;">
							
						</td>
						<td class="text-center">
						<!-- <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	 -->
						<img src="<?php echo base_url('/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">รายงานสรุปทุนเรือนหุ้น หนี้คงเหลือ</h3>
							 <h3 class="title_view">
								<?php 								
									$title_date = (@$_GET['type_date'] == '1')?'ณ วันที่':'ประจำวันที่';								
									echo @$title_date." ".$this->center_function->ConvertToThaiDate($start_date);
								?>
							</h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							<?php
							$get_param = '?';
							foreach(@$_GET as $key => $value){
								//if($key != 'month' && $key != 'year' && $value != ''){
									$get_param .= $key.'='.$value.'&';
								//}
							}
							$get_param = substr($get_param,0,-1);
							?>
							<a class="no_print"  target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_share_data/coop_report_share_loan_balance_person_excel'.$get_param); ?>">
								<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
							</a>
						</td>
					</tr> 
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view_small">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></span>				
						</td>
					</tr>  
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view_small">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
						</td>
					</tr>  
				<?php
					}
				?>
					<!-- <tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view_small">หน้าที่ <?php echo $index == 0 ? 1 : $index == 24 ? 2 : (($index-24) / 30) + 2?></span><br>						
						</td>
					</tr>  -->
				</table>
			
				<table class="table table-view table-center">
					<thead> 
						<tr>
                            <th rowspan="2" style="width: 150px;vertical-align: middle;">หน่วยงานย่อย</th> 
                            <th rowspan="2" style="width: 150px;vertical-align: middle;">จำนวนสมาชิก</th> 
							<th rowspan="2" style="width: 90px;vertical-align: middle;">ทุนเรือนหุ้น</th> 
							<?php 
							foreach($loan_type AS $key=>$row_loan_type){
							?>
							<th colspan="2" style="width: 80px;vertical-align: middle;"><?php echo str_replace('เงินกู้','',$row_loan_type['loan_type']);?></th> 
							<?php }?>
						</tr> 
						<tr>
							<?php 
							foreach($loan_type AS $key=>$row_loan_type){
							?>
							<th style="width: 80px;vertical-align: middle;">จำนวนสัญญา</th>
							<th style="width: 80px;vertical-align: middle;">เงินคงเหลือ</th>
							<?php }?>
						</tr> 
					</thead>
					<tbody>
							<?php
								}
                            if(!empty($tmp['mem_group_id']) && $tmp['mem_group_id'] <> $row['mem_group_id'] ) {
                                echo "<tr style='border-bottom: 2px double black;'><td colspan='5'>รวม</td><td colspan='2' style='text-align: right'>".number_format($member_count)."</td><td style='text-align: right'>".number_format($sum_share,2)."</td></td><td colspan='2'>รวม</td><td style='text-align: right'>".number_format($sum_emergent, 2)."</td><td colspan='2'>รวม</td><td style='text-align: right'>".number_format($sum_normal, 2)."</td><td colspan='2'>รวม</td><td style='text-align: right'>".number_format($sum_special, 2)."</td></tr>";
                                $sum_share = 0;
                                $sum_emergent = 0;
                                $sum_normal = 0;
                                $sum_special = 0;
                                $member_count = 0;
                                $index++;
                            }
                            $tmp['mem_group_id'] = $row['mem_group_id'];
                            if(empty($tmp['member_id']) || $tmp['member_id'] <> $row['member_id']){
                                $member_count += 1;
                            }
                            $sum_share += (int)$row['share_collect'];
                            $sum_emergent += (int)$row['loan_emergent_balance'];
                            $sum_normal += (int)$row['loan_normal_balance'];
                            $sum_special += (int)$row['loan_special_balance'];
                            $tmp['member_id'] = $row['member_id'];
							?>
							<tr> 
                              <td style="text-align: left;vertical-align: top;"><?php echo @$row['mem_group_name_level'];?></td>		
                              <td style="text-align: left;vertical-align: top;"><?php echo @$row['mem_group_count'];?></td>		
                              <td style="text-align: right;vertical-align: top;"><?php echo (@$row['share_collect'] !='')?number_format(@$row['share_collect'],2):''; ?></td> 					 
							  <td style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_emergent_count'] !='')?number_format(@$row['loan_emergent_count'],0):''; ?></td> 					 
							  <td style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_emergent_balance'] !='')?number_format(@$row['loan_emergent_balance'],2):''; ?></td> 					 
							  <td style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_normal_count'] !='')?number_format(@$row['loan_normal_count'],0):''; ?></td> 					 
							  <td style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_normal_balance'] !='')?number_format(@$row['loan_normal_balance'],2):''; ?></td> 					 
							  <td style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_special_count'] !='')?number_format(@$row['loan_special_count'],0):''; ?></td> 					 
							  <td style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_special_balance'] !='')?number_format(@$row['loan_special_balance'],2):''; ?></td> 					 
							</tr>										
					<?php
						if ($data_count == $all_index || $index == 23 || ( $index > 24 && (($index-24) % 30) == 29 )) {
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
		$last_runno = $runno;					
	?>
