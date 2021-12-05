<?php
$i = '1';
$h_border = "";
$b_border = "";
if(isset($_GET['excel'])){
    $i = '0';
    $file_name = " รายงานการผูกบัญชีกับกรุงไทย";
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

    * {
        all: unset;
    }

    body{
        background: #fff !important;
    }

    table {
        background: white !important;
    }

    table tbody tr td{
        border-collapse: collapse;
        border-top: none;
        border-bottom: none;
        border-left: thin solid black;
        border-right: thin solid black;
    }

    <?php } ?>
	</style>
		<div style="width: 900px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1000px;">
				<table style="width: 100%;">
					<tr>
						<td class="text-center" style="width:100px;">&nbsp;</td>
						<td colspan="4" class="text-center">
							 <h3 class="title_view">รายงานการผูกบัญชีกับกรุงไทย</h3>
							 <?php if(@$_GET['report_date'] != ''){?>
							 <h4 class="title_view">วันที่  <?php echo @$report_date;?></h4>		
							 <?php } ?>
						</td>
                        <?php if($i == '1'){?>
                        <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							<a class="no_print" onclick="export_excel('<?php echo @$_GET['report_date']?>', '<?php echo @$_GET['check_all']?>');"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>
						</td>
                        <?php } ?>						
					</tr>
					<tr>
						<td colspan="6" class="text-center">&nbsp;</td>
					</tr>
				</table>
				<table class="table table-view table-center">
					<thead> 
						<tr>
                            <th  style="width: 60px;vertical-align: middle; <?php echo $h_border?>">ลำดับ</th>
							<th  style="width: 100px;vertical-align: middle; <?php echo $h_border?>">รหัสสมาชิก</th>
							<th  style="width: 200px;vertical-align: middle; <?php echo $h_border?>">ชื่อ - นามสกุล</th>
							<th  style="width: 100px;vertical-align: middle; <?php echo $h_border?>">เลขที่บัญชี</th>
							<th  style="width: 100px;vertical-align: middle; <?php echo $h_border?>">บัญชีกรุงไทย</th>
							<th  style="width: 100px;vertical-align: middle; <?php echo $h_border?>">วันที่ผูกบัญชีกรุงไทย</th>
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
                              <td style="text-align: center;<?php echo $border; ?>;"><?php echo @$runno; ?></td>
                              <td style="text-align: center;<?php echo $border; ?>"><?php echo @$value['mem_id'];?></td>
                              <td style="text-align: left;<?php echo $border; ?>"><?php echo @$value['account_name'];?></td>
                              <td style="text-align: center;<?php echo $border; ?>"><?php echo @$value['account_id'];?></td>
                              <td style="text-align: center;<?php echo $border; ?>"><?php echo @$value['account_id_atm'];?></td>
                              <td style="text-align: center;<?php echo $border; ?>"><?php echo @$this->center_function->ConvertToThaiDate(@$value['account_id_atm_update'],1,1);?></td>
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
    function export_excel(report_date, check_all) {
        window.open('coop_report_ktb_account_preview?excel=&report_date=' + report_date + '&check_all=' + check_all, '_blank');
    }
</script>
<?php } ?>