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
<?php
	$runno = 0;
	$prev_level = "x";
	$total = 0;
	foreach($datas AS $page=>$data){
?>
<form action="<?php echo base_url(PROJECTPATH.'/invest/report_invest_preview'); ?>" id="search_form" method="POST" target="_blank">
	<input type="hidden" name="doc_type" id="doc_type" value="excel"/>
	<input type="hidden" name="type" id="type" value="<?php echo $_POST['type'];?>"/>
	<input type="hidden" name="from_date" id="from_date" value="<?php echo $_POST['from_date'];?>"/>
	<input type="hidden" name="thru_date" id="thru_date" value="<?php echo $_POST['thru_date'];?>"/>
	<input type="hidden" name="org" id="org" value="<?php echo $_POST['org'];?>"/>
</form>
<div style="width: 11.7in;" class="page-break">
	<div class="panel panel-body" style="padding-top:10px !important;height: 8.3in;">
		<table style="width: 100%;">
		<?php 
			if($page == 1) {
		?>	
			<tr>
				<td style="width:100px;vertical-align: top;">

				</td>
				<td class="text-center">
					<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
					<h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
					<h3 class="title_view">รายงานสรุปการลงทุน</h3>
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
		<?php } ?>
			<tr>
				<td colspan="3" style="text-align: right;">
					<span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>
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

		<table class="table table-view table-center">
			<tbody>
			<?php
				$first_of_page = 1;
				$max = count($data)-1;
				foreach($data as $key => $row){
					if(!empty($first_of_page)) {
			?>
				<tr>
					<th style="width: 5%;vertical-align: middle;">ลำดับ</th>
					<th style="width: 15%;vertical-align: middle;">องค์กร</th>
					<th style="width: 15%;vertical-align: middle;">หมวดการลงทุน</th>
					<th style="vertical-align: middle;">หัวข้อการลงทุน</th>
					<th style="width: 10%;vertical-align: middle;">เงินลงทุน</th>
					<th style="width: 10%;vertical-align: middle;">วันที่ลงทุน</th>
					<th style="width: 10%;vertical-align: middle;">วันครบกำหนด</th>
				</tr>
			<?php
					}
			?>
				<tr>
					<td style="text-align: center;vertical-align: top;"><?php echo ++$runno; ?></td>
					<td style="text-align: left;vertical-align: top;"><?php echo $row['org_name'];?></td>
					<td style="text-align: left;vertical-align: top;"><?php echo $row['type_name'];?></td>
					<td style="text-align: left;vertical-align: top;"><?php echo $row['name'];?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo number_format($row['amount'], 2);?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo !empty($row['invest_date']) ? $this->center_function->ConvertToThaiDate($row['invest_date'], '1', '0')
																												 : $this->center_function->ConvertToThaiDate($row['start_date'], '1', '0');?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo !empty($row['end_date']) ? $this->center_function->ConvertToThaiDate($row['end_date'], '1', '0') : "";?></td>
				</tr>
			<?php
					$first_of_page = 0;
					$total += $row['amount'];
					if($max == $key) {
			?>
				<tr>
					<th colspan="4" class="table_body text-right">
						รวม&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</th>
					<th class="text-right">
						<?php echo number_format($total, 2);?>
					</tthd>
					<th colspan="3">
					</th>
				</tr>
			<?php
					}
				}
			?>
			</tbody>
		</table>
	</div>
</div>
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