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
		font-size: 11px;
	}	
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}
	
	.table-view-2>tbody>tr>td>span{
		font-family: Tahoma;
		font-size: 11px;
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
	$start_date_arr = explode('/',@$_GET['start_date']);
	$start_day = $start_date_arr[0];
	$start_month = $start_date_arr[1];
	$start_year = $start_date_arr[2];
	$start_year -= 543;
	$start_date = $start_year.'-'.$start_month.'-'.$start_day;
}

if(@$_GET['end_date']){
	$end_date_arr = explode('/',@$_GET['end_date']);
	$end_day = $end_date_arr[0];
	$end_month = $end_date_arr[1];
	$end_year = $end_date_arr[2];
	$end_year -= 543;
	$end_date = $end_year.'-'.$end_month.'-'.$end_day;
}		
		
//class="page-break"
//
$all_withdrawal = 0; 
$all_deposit = 0; 
$all_balance = 0;
$all_runno = 0;
$page_no =0;	//หน้าที่
$max_page =0;	//หน้าทั้งหมด	
// echo '553';exit;
if(!empty($data)){
	foreach(@$data AS $key_max=>$max){ //นับจำนวนหน้าทั้งหมด
		foreach(@$max AS $key_max2=>$max2){
			
			$max_page++;
			
		}
	}
	foreach(@$data AS $page=>$dataa){
		$count_page=count($dataa); //นับจำนวนแต่ละประเภทบัญชี
		$sum_type = 0;	//หน้าที่ ของแต่ละประเภทบัญชี
		$total_transaction_withdrawal = 0;//ถอนรวมแต่ละประเภทบัญชี
		$total_transaction_deposit = 0;//ฝากรวมแต่ละประเภทบัญชี
		$total_transaction_balance = 0;//คงเหลือรวมแต่ละประเภทบัญชี
		$runno = 0;
		foreach(@$dataa AS $key_type=>$datass){
			$sum_type++; 
			$page_no++;
		
	?>
		<div style="width: 1000px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
				<table style="width: 100%;">
				<?php 		
					// if(@$key_type == 1){
				?>	
					<tr>
						<td style="width:100px;vertical-align: top;">	
						</td>
						<td class="text-center">
							 <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">รายงานการทำรายการ</h3>
							 <h3 class="title_view">
								<?php echo (@$_GET['type_id']!='all') ? " ประเภทบัญชี ".@$type_deposit[@$_GET['type_id']] : "ประเภทบัญชี ทั้งหมด"?>
							</h3>
							 <h3 class="title_view">
								<?php 
									echo " ประจำวันที่ ".date('d/m/Y',strtotime('+543 year',strtotime($start_date)));
									echo "  ถึง  ".date('d/m/Y',strtotime('+543 year',strtotime($end_date)));
								?>
							</h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
						 <a class="no_print" onclick="export_excel()"><button class="btn btn-perview btn-after-input" type="button"><span class="fa fa-file-excel-o" aria-hidden="true"></span></button></a>
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>						 
							<br><br>
							<span class="title_view">วันที่พิมพ์ <?php echo @date('Y-m-d');?></span>		<br>	
							<span class="title_view">หน้าที่ <?php echo @$page_no.'/'.@$max_page;?></span>	
						</td>
					</tr>  					
				<?php 
			// }
			 ?>
					<?php 	
					// if(@$key_type == 1){
					?>
					<tr>
						<td colspan="3" >
							<h3 class="title_view"><?php echo  "ประเภทบัญชีเงินฝาก "; echo @$page=='' ? "ทั้งหมด": @$type_deposit[$page] ?></h3>
						</td>
					</tr>
					<?php
				//  }
				  ?>
				</table>
				<table class="table table-view-2 table-center">
					<thead> 
						<tr>
							<th style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th style="width: 100px;vertical-align: middle;">วันที่ทำรายการ</th>
							<th style="width: 60px;vertical-align: middle;">เวลาที่ทำรายการ</th>
							<th style="width: 100px;vertical-align: middle;">หมายเลขบัญชี</th>
							<th style="width: 180px;vertical-align: middle;">ชื่อบัญชี</th>
							<th style="width: 70px;vertical-align: middle;">รายการ</th>
							<th style="width: 80px;vertical-align: middle;">ฝาก</th>
							<th style="width: 80px;vertical-align: middle;">ถอน</th>
							<th style="width: 80px;vertical-align: middle;">คงเหลือ</th>
							<th style="vertical-align: middle;">ผู้บันทึก</th>
						</tr>  
					</thead>
					<tbody>			
					<?php			
						if(!empty($datass)){		
							foreach(@$datass as $key => $row){
								$runno++;
								$total_transaction_withdrawal += @$row['transaction_withdrawal'];
								$total_transaction_deposit += @$row['transaction_deposit'];
								$total_transaction_balance += @$row['transaction_balance'];
								$all_runno++;
								$all_withdrawal +=  @$row['transaction_withdrawal'];
								$all_deposit +=  @$row['transaction_deposit'];
								$all_balance +=  @$row['transaction_balance'];
					?>
							<tr> 
							  <td style="text-align: center;vertical-align: top;"><?php echo @$runno; ?></td>
							  <td style="text-align: center;vertical-align: top;"><?php echo (@$row['transaction_time'])?$this->center_function->ConvertToThaiDate(@$row['transaction_time'],1,0):"";?></td>
							  <td style="text-align: center;vertical-align: top;"><?php echo (@$row['transaction_time'])?date(" H:i" , strtotime(@$row['transaction_time'])):"";?></td>						 
							  <td style="text-align: center;vertical-align: top;"><?php echo @$row['account_id'];?></td>						 
							  <td style="text-align: left;vertical-align: top;"><?php echo @$row['account_name'];?></td>	
							  <td style="text-align: center;vertical-align: top;"><?php echo @$row['transaction_list'];?></td> 					 
							  <td style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_deposit'],2); ?></td> 					 
							  <td style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_withdrawal'],2); ?></td> 					 
							  <td style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_balance'],2); ?></td> 						 
							  <td style="text-align: center;vertical-align: top;"><?php echo @$row['user_name'];?> </td> 
							
							 						 
							</tr>											
					<?php													
							}
						}	
					?>
					<?php	
						if(@$count_page == @$sum_type && @$_GET['type_id']=='all'){							
					?>
					 <tr class="border-bottom">  </tr>
						   <tr class="border-bottom"> 
							  <td style="text-align: center;" colspan="4"><?php echo (@$_GET['type_id']=='all') ? " รวม" : "รวมทั้งหมด "?> <?php echo @$runno;?> รายการ</td>					 
							  <td style="text-align: center;" colspan="2"><?php echo (@$_GET['type_id']=='all') ? "จำนวนเงิน" : "จำนวนเงินทั้งหมด"?></td>						 
							  <td style="text-align: right;"><span style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_transaction_deposit,2); ?></span></td> 					 
							  <td style="text-align: right;"><span style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_transaction_withdrawal,2); ?></span></td> 					 
							  <td style="text-align: right;"><span style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_transaction_balance,2); ?></span></td> 							 
							  <td style="text-align: center;">บาท</td> 						 
						  </tr>
					<?php } ?>
					<?php 
						if(@$page_no == @$max_page)
					{
						?>	 
						<tr class="foot-border"> 
							  <td style="text-align: center;" colspan="4">รวมทั้งหมด <?php echo @$all_runno;?> รายการ</td>					 
							  <td style="text-align: center;" colspan="2">จำนวนเงินทั้งหมด</td>						 
							  <td style="text-align: right;"><?php echo number_format(@$all_deposit,2); ?></td> 						 
							  <td style="text-align: right;"><?php echo number_format(@$all_withdrawal,2); ?></td> 						 
							  <td style="text-align: right;"><?php echo number_format(@$all_balance,2); ?></td> 						 
							  <td style="text-align: center;">บาท</td> 						 
						  </tr>
					<?php } ?> 
						
					</tbody>    
				</table>
			</div>
		</div>
<?php 
		}
	}	 
}
?>
<script>
    function export_excel(){
        var url = window.location.href+"&excel=export";
        window.location  = url;
    }
</script>