<?php
$i = '1';
$h_border = "";
$b_border = "";
if(isset($_GET['excel'])){
    $i = '0';
    $file_name = " รายงานการผูกบัญชีกับกรุงไทย ของระบบเงินฝาก";
	//echo '<pre>'; print_r($_GET); echo '</pre>'; exit;
    header("Content-Disposition: attachment; filename=".$file_name.".xls");
    header("Content-type: application/vnd.ms-excel; charset=UTF-8");

    $h_border = ' border: thin solid black; ';
    $b_border = ' border-left: thin solid black;';
}
?>
<style>

	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 16px;
	}		
	@page { 
		padding-top:10px;
		margin-top: 10px;
	}
	.table {
		color: #000;
	}
	 <?php if(isset($_GET['excel'])) {?>

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
			}
			.table_body_right{
				font-family: AngsanaUPC, MS Sans Serif;
				font-size: 21px;
				border: thin solid black;
				text-align:right;
			}
			.title_view{
				font-family: AngsanaUPC, MS Sans Serif;
				border: thin solid black;
				text-align:right;
			}

    <?php } ?>
	</style>
		<div style="width: 900px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1000px;">
				<table style="width: 100%;">
					<tr>
						<td class="text-center" style="width:100px;">&nbsp;</td>
						<td colspan="4" class="text-center">
							 <h3 class="title_view">รายงานการผูกบัญชีกับกรุงไทย ของระบบเงินฝาก</h3>
						</td>
                        <?php if($i == '1'){?>
                        <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							<a class="no_print" onclick="export_excel('<?php echo @$_GET['account_id']?>');"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>
						</td>
                        <?php } ?>						
					</tr>
					<tr>
						<td colspan="6" class="text-center">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="6" class="text-center">
							<?php $tab = "&nbsp;&nbsp;&nbsp;&nbsp;";?>
							<h4 class="title_view">
								เลขที่บัญชี: <?php echo @$data['account_id'].$tab;?>
								รหัสสมาชิก :  <?php echo @$data['mem_id'].$tab;?>
								ชื่อ - นามสกุล : <?php echo @$data['account_name'].$tab;?>
							</h4>		
						</td>
					</tr>
				</table>
				<table class="table table-view table-center">
					<thead> 
						<tr>
                            <th class="table_header_top" style="width: 10%;vertical-align: middle; <?php echo $h_border?>">ลำดับ</th>
							<th class="table_header_top" style="width: 20%;vertical-align: middle; <?php echo $h_border?>">บัญชีกรุงไทย</th>
							<th class="table_header_top" style="width: 20%;vertical-align: middle; <?php echo $h_border?>">วันที่ผูกบัญชีกรุงไทย</th>
							<th class="table_header_top" style="width: 20%;vertical-align: middle; <?php echo $h_border?>">ผู้ทำรายการ</th>
							<th class="table_header_top" style="width: 10%;vertical-align: middle; <?php echo $h_border?>">สถานะ</th>
						</tr>
					</thead>
					<tbody>
					  <?php 
						//echo '<pre>'; print_r(@$data); echo '</pre>';
						$runno = 0;
						if(!empty($data_detail)){
							foreach($data_detail as $key => $value){
								$runno++;
						?>
						  <tr>
							  <td class="table_body" style="text-align: center;<?php echo $border; ?>;"><?php echo @$runno; ?></td>
                              <td class="table_body" style="text-align: center;<?php echo $border; ?>"><?php echo @$value['account_id_atm'];?></td>
                              <td class="table_body" style="text-align: center;<?php echo $border; ?>"><?php echo @$this->center_function->ConvertToThaiDate(@$value['account_id_atm_update'],1,1);?></td>
                              <td class="table_body" style="text-align: left;<?php echo $border; ?>"><?php echo @$value['user_name'];?></td>
							  <td class="table_body" style="text-align: center;<?php echo $border; ?>"><?php echo (@$value['account_atm_status'] == '')?@$value['account_atm_status']:$arr_atm_status[@$value['account_atm_status']];?></td>		
						  </tr>
					<?php 
							}
						} 
					?>							
					</tbody>  
				</table>
			</div>
		</div>
<?php if(!isset($_GET['excel'])) { ?>
<script>
    function export_excel(account_id) {
        window.open('coop_report_ktb_account_detail_preview?excel=excel&account_id=' + account_id , '_blank');
    }
</script>
<?php } ?>