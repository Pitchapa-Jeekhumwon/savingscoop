<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}
	.table-view-2>thead>tr>th{
	    border-top: 1px solid #000 !important;
		border-bottom: 1px solid #000 !important;
		font-size: 16px;
	}
	.table-view-2>tbody>tr>td{
	    border: 0px !important;
		/*font-family: upbean;
		font-size: 16px;*/
		font-family: Tahoma;
		font-size: 12px;
	}	
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}
	
	.foot-border{
	    border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}
	.table {
		color: #000;
	}
</style>		
<?php

if(@$_GET['start_date']){
	$start_date = $this->center_function->ConvertToSQLDate($_GET['start_date']);
}

if(@$_GET['end_date']){
	$end_date = $this->center_function->ConvertToSQLDate($_GET['end_date']);
}		

if(!empty($_GET['start_date']) && empty($_GET['end_date'])) {
	$date_last = $start_date;
}else if(!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
	$date_last = $end_date;
}		
//class="page-break"
$last_runno = 0;
$all_loan_amount  = 0;	
	
if(!empty($data)){
	foreach(@$data AS $page=>$data_row){
	?>
		
		<div style="width: 1000px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
				<table style="width: 100%;">	
					<tr>
						<td style="width:150px;vertical-align: top;">
							
						</td>
						<td class="text-center" colspan="3">
							 <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
							</h3>
						 </td>
						 <td style="width:150px;vertical-align: top;" class="text-right">
							<?php if(@$page == 1){ ?>
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
							<!--<a class="no_print"  target="_blank" href="<?php echo base_url('/report_deposit_data/coop_report_transaction_emergent_atm_excel'.$get_param); ?>">
								<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
							</a>-->
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td style="width:100px;vertical-align: top;">
							
						</td>
						<td class="text-center" colspan="3">
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
						 </td>
						 <td class="text-right">
							<span class="title_view">วันที่ <?php echo $this->center_function->mydate2date(@date('Y-m-d'));?></span>				
							<span class="title_view">   เวลา <?php echo date('H:i:s');?></span>	
						</td>
					</tr>
					<tr>
						<td style="width:100px;vertical-align: top;">
							
						</td>
						<td class="text-center" colspan="3">
							 <h3 class="title_view">รายงาน statement เงินฝาก (ดอกเบี้ยสะสม)</h3>							 
						 </td>
						 <td class="text-right">
							<span class="title_view">หน้าที่ : <?php echo @$page.'/'.@$page_all;?></span><br>	
						</td>
					</tr>
					<tr>
						<td style="width:100px;vertical-align: top;">
							
						</td>
						<td class="text-center" colspan="3">
							<h3 class="title_view">
								<?php 
									echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"ระหว่าง";
									echo "วันที่ ".$this->center_function->mydate2date($start_date);
									echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึง  ".$this->center_function->mydate2date($end_date);
								?>
							</h3>
						 </td>
						 <td class="text-right">
							<span class="title_view">ผู้ที่พิมพ์  : <?php echo $_SESSION['USER_NAME'];?>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<h4 class="title_view">เลขที่บัญชี : <?php echo $data_row['account']['account_id'];?></h4>
						</td>
						<td>	
							<h4 class="title_view">ชื่อบัญชี : <?php echo $data_row['account']['account_name'];?></h4>
						</td>
						<td>	
							<h4 class="title_view">วันที่เปิดบัญชี : <?php echo $this->center_function->mydate2date($data_row['account']['created']);?></h4>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">
							<h4 class="title_view">สังกัด : <?php echo $data_row['account']['level_name'];?></h4>
						</td>
						<td>&nbsp;</td>
						<td>	
							<h4 class="title_view">เลขทะเบียนสมาชิก : <?php echo $data_row['account']['member_id'];?></h4>
						</td>
						<td>&nbsp;</td>
					</tr>					
					<tr>
						<td style="width:150px;">&nbsp;</td>
						<td style="width:150px;">&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th style="width: 100px;vertical-align: middle;">วันที่</th>
							<th style="width: 80px;vertical-align: middle;">รายการ</th>
							<th style="width: 80px;vertical-align: middle;">ถอน</th>
							<th style="width: 80px;vertical-align: middle;">ฝาก</th>
							<th style="width: 80px;vertical-align: middle;">คงเหลือ</th>
							<th style="width: 80px;vertical-align: middle;">ดบ.สะสม</th>
							<th style="width: 100px;vertical-align: middle;">พนักงาน</th>
						</tr>  
					</thead>
					<tbody>
					
					<?php	
						$runno = $last_runno;
						$total_loan_amount = 0;	
						$run_row = 0;
						$transaction_balance_last = 0;
						if(!empty($data_row['transaction'])){
							foreach(@$data_row['transaction'] as $key => $row){
								$runno++;
								$run_row++;
								$total_loan_amount += $row['loan_amount'];
					?>
							<tr> 
							  <td style="text-align: center;vertical-align: top;"><?php echo @$run_row; ?></td>
							  <td style="text-align: center;vertical-align: top;"><?php echo (@$row['transaction_time'])?$this->center_function->mydate2date(@$row['transaction_time']):"";?></td>
							  <td style="text-align: center;vertical-align: top;"><?php echo @$row['transaction_list'];?></td>
							  <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['transaction_withdrawal'],2); ?></td>
							  <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['transaction_deposit'],2); ?></td>
							  <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['transaction_balance'],2); ?></td>
							  <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['old_acc_int'],2); ?></td>
							  <td style="text-align: left;vertical-align: top;"><?php echo @$row['user_name'];?></td>
							</tr>
					<?php		
								$transaction_balance_last = @$row['transaction_balance'];
							}
						}
						$last_runno = $runno;
						$all_loan_amount += $total_loan_amount;
						
					?> 		
						<tr> 
						  <td style="text-align: left;vertical-align: top;border: 0px;" colspan="8"><h4>*** จำนวนรายการติดต่อ ***  <?php echo $run_row;?> รายการ</h4></td>					 
						</tr>
						<tr> 
						  <td style="text-align: left;vertical-align: top;border: 0px;" colspan="8"><h4>จำนวนเงินคงเหลือ ณ วันที่ <?php echo $this->center_function->mydate2date($date_last);?> : <?php echo number_format(@$transaction_balance_last,2); ?> บาท</h4></td>					 
						</tr>					
					</tbody>    
				</table>
			</div>
		</div>
<?php 
	}
} 
?>