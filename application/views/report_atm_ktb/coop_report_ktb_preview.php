<?php
$i = '1';
$h_border = "";
$b_border = "";
	
if(@$_GET['export']=="excel"){
	header("Content-type: application/vnd.ms-excel;charset=utf-8;");
	header("Content-Disposition: attachment; filename=Report ATM ktb.xls");
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
</style>
<?php		
	}
?>
<style>

	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 16px;
	}		
	@page { 
		size: landscape;
		padding-top:10px;
		margin-top: 10px;
	}
	.table {
		color: #000;
	}
	</style>
		<div style="width: 1500px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1000px;">
				<table style="width: 100%;">
					<tr>
						<td colspan="11" class="text-center">
							 <h3 class="title_view">Report ATM ktb</h3>
						</td>
                        <?php if($i == '1'){?>
                        <td style="width:100px;vertical-align: top;" class="text-right">
								<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
								<a class="no_print" onclick="export_excel()"><button class="btn btn-perview btn-after-input" type="button"><span class="icon fa fa-file-excel-o" aria-hidden="true"></span></button></a>
						</td>
                        <?php } ?>
					</tr>
					<!--<tr>
						<td colspan="10" class="text-center"></td>
						<td class="text-left" style="width: 100px;">PAGE</td>
						<td class="text-left"><?php echo $i;?></td>
					</tr>-->
					<tr>
						<td colspan="10" class="text-center"></td>
						<td class="text-left" style="width: 100px;">RUN DATE</td>
						<td class="text-left"><?php echo date('d/m/Y');?></td>
					</tr>
					<tr>
						<td colspan="10" class="text-center"></td>
						<td class="text-left">RUN TIME</td>
						<td class="text-left"><?php echo date('H:i:s');?></td>
					</tr>
					<tr>
						<td colspan="11" class="text-center">AS AT <?php echo @$report_date;?></td>
						<td class="text-left">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="12" class="text-center">&nbsp;</td>
					</tr>
				</table>
				<table class="table table-view table-center">
					<thead> 
						<tr>
                            <th  style="width: 80px;vertical-align: middle; <?php echo $h_border?>" class="table_header_top">ITEM</th>
							<th  style="width: 100px;vertical-align: middle; <?php echo $h_border?>" class="table_header_top">TX-SEQ</th>
							<th  style="width: 100px;vertical-align: middle; <?php echo $h_border?>" class="table_header_top">DATE</th>
							<th  style="width: 100px;vertical-align: middle; <?php echo $h_border?>" class="table_header_top">TIME</th>
							<th  style="width: 150px;vertical-align: middle; <?php echo $h_border?>" class="table_header_top">CUSTOMER COOP ACCOUNT</th>
							<th  style="width: 100px;vertical-align: middle; <?php echo $h_border?>" class="table_header_top">MEMBER ID</th>
							<th  style="width: 90px;vertical-align: middle; <?php echo $h_border?>" class="table_header_top">TXN CODE</th>
							<th  style="width: 120px;vertical-align: middle; <?php echo $h_border?>" class="table_header_top">TXN ID</th>
							<th  style="width: 90px;vertical-align: middle; <?php echo $h_border?>" class="table_header_top">TXN TYPE</th>
							<th  style="width: 100px;vertical-align: middle; <?php echo $h_border?>" class="table_header_top">REQ ATM</th>
							<th  style="width: 100px;vertical-align: middle; <?php echo $h_border?>" class="table_header_top">ACT ATM</th>
							<th  style="width: 100px;vertical-align: middle; <?php echo $h_border?>" class="table_header_top">RESPONSE CODE</th>
						</tr>
					</thead>
					<tbody>
					  <?php 
						//echo '<pre>'; print_r(@$data); echo '</pre>';
						$runno = 0;
						if(!empty($data)){
							foreach($data as $key => $value){
								$runno++;
						?>
						  <tr>
                              <td style="text-align: center;<?php echo $border; ?>;" class="table_body"><?php echo @$runno; ?></td>
                              <td style="text-align: center;<?php echo $border; ?>" class="table_body"><?php echo @$value['bank_reference_number'];?></td>
                              <td style="text-align: center;<?php echo $border; ?>" class="table_body"><?php echo @$value['transaction_date'];?></td>
                              <td style="text-align: center;<?php echo $border; ?>" class="table_body"><?php echo @$value['transaction_time'];?></td>
                              <td style="text-align: center;<?php echo $border; ?>" class="table_body"><?php echo @$value['customer_coop_account'];?></td>
                              <td style="text-align: center;<?php echo $border; ?>" class="table_body"><?php echo @$value['coop_member_id'];?></td>
                              <td style="text-align: left;<?php echo $border; ?>" class="table_body"><?php echo $arr_transaction_code[@$value['transaction_code']];?></td>
                              <td style="text-align: left;<?php echo $border; ?>" class="table_body"><?php echo $arr_list_id[@$value['list_id']]; ?></td>
							  <td style="text-align: left;<?php echo $border; ?>" class="table_body"><?php echo $arr_from_acct_type[@$value['from_acct_type']];?></td>
                              <td style="text-align: right;<?php echo $border; ?>" class="table_body"><?php echo @$value['transaction_amount_req'];?></td>
							  <td style="text-align: right;<?php echo $border; ?>" class="table_body"><?php echo @$value['transaction_amount_act']; ?></td>
							  <td style="text-align: left;<?php echo $border; ?>" class="table_body"><?php echo $arr_response_code[@$value['response_code']]; ?></td>
						  </tr>
					<?php 
							}
						} 
					?>							
					</tbody>  
				</table>
			</div>
		</div>
<script>
	function export_excel(){
		var url = window.location.href+"&export=excel";
		window.location = url;
	}
</script>		