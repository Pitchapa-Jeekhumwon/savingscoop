<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}
	.table-view>tbody>tr>th{
	    border-top: 1px solid #000 !important;
		border-bottom: 1px solid #000 !important;
		font-size: 12px;
		background-color: #eee;
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
	.table-view-2>tbody>tr>td>span{
		font-family: Tahoma;
		font-size: 12px !important;
	}
	.foot-border{
	    border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}
	.table {
		color: #000;
	}
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		padding:6px;
	}
</style>
<form action="<?php echo base_url(PROJECTPATH.'/invest/report_profit_dividend_preview'); ?>" id="search_form" method="POST" target="_blank">
	<input type="hidden" name="doc_type" id="doc_type" value="excel"/>
	<input type="hidden" name="type" id="type" value="<?php echo $_POST['type'];?>"/>
	<input type="hidden" name="from_date" id="from_date" value="<?php echo $_POST['from_date'];?>"/>
	<input type="hidden" name="thru_date" id="thru_date" value="<?php echo $_POST['thru_date'];?>"/>
	<input type="hidden" name="org" id="org" value="<?php echo $_POST['org'];?>"/>
</form>
<?php
	$runno = 0;
	$total = 0;
	$id_total = 0;//total for each id.
	// $index = 0;
	$line_count = 0;
	$prev_id = "x";
	$page = 1;
	foreach($datas AS $index => $data){
?>
<?php
	if($index == 0) {
?>	
<div style="width: 8.3in;" class="page-break">
	<div class="panel panel-body" style="padding-top:10px !important;height: 11.7in;">
		<table style="width: 100%;">
			<tr>
				<td style="width:100px;vertical-align: top;">

				</td>
				<td class="text-center">
					<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
					<h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
					<h3 class="title_view">รายงานสรุปปันผล</h3>
					<h3 class="title_view">
					</h3>
				</td>
				<td style="width:100px;vertical-align: top;" class="text-right">
					<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
					<a class="no_print"  target="_blank" id="excel-submit">
						<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
					</a>
				</td>
			</tr>
	<?php
			$line_count += 150;
		}
		if($index == 0 || $line_count > 900 || ($line_count > 800  && $prev_id != $data['id'])) {
			if($index != 0) {
				$line_count = 0;
	?>
<div style="width: 8.3in;" class="page-break">
	<div class="panel panel-body" style="padding-top:10px !important;height: 11.7in;">
		<table style="width: 100%;">
	<?php
			}
	?>
			<tr>
				<td colspan="3" style="text-align: right;">
					<span class="title_view">หน้าที่ <?php echo @$page++;?></span><br>
				</td>
			</tr> 
			<tr>
				<td colspan="3" style="text-align: right;">
					<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>
				</td>
			</tr> 
			<tr>
				<td colspan="3" style="text-align: right;">
					<span class="title_view">เวลา <?php echo date('H:i:s');?></span>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: right;">
					<span class="title_view">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
				</td>
			</tr>
		</table>
	<?php
			$line_count += 80;
			$str_f = 1;
		} else {
			$str_f = 0;
		}

		if($prev_id != $data['id']) {
			$prev_id = $data['id'];
			$runno = 0;
			
	?>
		<table class="table" style="<?php echo $index != 0 && $str_f == 0 ? 'margin-top: 80px;' : '';?> width:90%; margin-bottom: 10px;">
			<tbody>
				<tr>
					<th colspan="4" style="border: unset;" class="text-left">
					<?php echo $data['type_name']." : ".$data['name']?>
					</th>
				</tr>
				<tr>
					<th style="border: unset;width: 50%;" class="text-left">
						จำนวนเงิน : <?php echo number_format($data['amount'],2);?>
					</th>
					<th style="border: unset;width: 50%;" class="text-left">
						วันที่ลงทุน : <?php echo $this->center_function->ConvertToThaiDate($data['start_date'], '1', '0');?>
					</th>
				</tr>
				<tr>
					<th style="border: unset;" class="text-left">
						รอบบัญชี : <?php echo $data['period'];?>
					</th>
					<th style="border: unset;" class="text-left">
						องค์กร : <?php echo $data['org_name'];?>
					</th>
				</tr>
				<tr>
					<th style="border: unset;" class="text-left">
						สถานะ : <?php echo $data['status'] == 1 ? "ปกติ" : "ไม่เปิดใช้งาน";?>
					</th>
					<th style="border: unset;" class="text-left">
					</th>
				</tr>
			</tbody>
		</table>
	<?php
			$str_f = 1;
			$line_count += 190;
		}

		if($str_f == 1) {
	?>

		<table class="table table-view table-center" style="width:80%">
			<tbody>
				<tr>
					<th style="width: 11%;vertical-align: middle;">ลำดับ</th>
					<th style="width: 20%;vertical-align: middle;">วันที่ได้รับปันผล</th>
					<th style="width: 12%;vertical-align: middle;">ปันผล</th>
					<th style="width: 15%;vertical-align: middle;">จำนวนเงิน</th>
					<th style="vertical-align: middle;">หมายเหตุ</th>
				</tr>
	<?php
			$line_count += 20;
		}
	?>
				<tr>
					<td style="text-align: center;vertical-align: top;"><?php echo ++$runno; ?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo $this->center_function->ConvertToThaiDate($data['interest_date'], '1', '0');?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo $data['rate']."%";?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo number_format($data['interest_amount'], 2);?></td>
					<td style="text-align: left;vertical-align: top;"><?php echo $data['note'];?></td>
				</tr>
			<?php
					$line_count += 20;
					$first_of_page = 0;
					$total += $data['interest_amount'];
					$id_total += $data['interest_amount'];
					if(empty($datas[($index+1)]) || $datas[($index+1)]['id'] != $data['id']) {
			?>
				<tr>
					<th colspan="3" class="table_body text-right">
						รวม&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</th>
					<th class="text-right">
						<?php echo number_format($id_total, 2);?>
					</tthd>
					<th colspan="1">
					</th>
				</tr>
			<?php
						$id_total = 0;
					}
	if($line_count > 900 || $index == $max || ($line_count > 800  && $datas[($index+1)]['id'] != $data['id'])) {
?>
			</tbody>
		</table>
<?php
		if($index == $max) {
?>
		<table class="table" style="width:80%; margin-top: 10px;">
			<tbody>
				<tr>
					<th style="width: 11%;vertical-align: middle;"></th>
					<th style="width: 20%;vertical-align: middle;"></th>
					<th style="width: 12%;vertical-align: middle;" class="text-right">รวมทั้งสิ้น</th>
					<th style="width: 15%;vertical-align: middle;" class="text-right"><?php echo number_format($total, 2);?></th>
					<th style="vertical-align: middle;"></th>
				</tr>
			</tbody>
		</table>
<?php
		}
?>
	</div>
</div>
<?php
	} 
?>
<?php
	}
?>
<script>
	$(document).ready(function() {
		$("#excel-submit").click(function() {
			$("#search_form").submit();
		});
	});
</script>