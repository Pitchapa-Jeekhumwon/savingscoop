<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานสรุปดอกเบี้ยสะสมเงินฝาก.xls");
date_default_timezone_set('Asia/Bangkok');
?>
<pre>
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
					color: #000;
					text-align:center;
				}
				.table_title_right{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 16px;
					font-weight: bold;
					color: #000;
					text-align:right;
				}
				.table_header_top{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					color: #000;
					border-top: thin solid black;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_mid{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					color: #000;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_bot{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					color: #000;
					border-bottom: thin solid black;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_bot2{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					color: #000;
					font-weight: bold;
					text-align:center;
					border: thin solid black;
				}
				.table_body{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 21px;
					color: #000;
					border: thin solid black;
				}
				.table_body_right{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 21px;
					color: #000;
					border: thin solid black;
					text-align:right;
				}
			</style>
		</head>
		<body>
<?php 

if (@$_GET['start_date']) {
	$start_date_arr = explode('/', @$_GET['start_date']);
	$start_day = $start_date_arr[0];
	$start_month = $start_date_arr[1];
	$start_year = $start_date_arr[2];
	$start_year -= 543;
	$start_date = $start_year . '-' . $start_month . '-' . $start_day;
}
$title_date = "";
if($_GET['month']!='' && $_GET['year']!=''){
	$day = '';
	$month = $_GET['month'];
	$year = $_GET['year'];
	$title_date = " เดือน ".$month_arr[$month]." ปี ".($year);
}
?>
		<table class="table table-bordered">
						<tr>
							<th class="table_title" colspan="5"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="5">รายงานสรุปดอกเบี้ยสะสมเงินฝาก<?= ($monthly = true) ? 'ประจำเดือน' : '' ?> <?= $type_name;?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="5">ประเภทสมัครสมาชิก <?=(@$_GET['apply_type_id']==0)?"ทั้งหมด": @$mem_apply_type[@$_GET['apply_type_id']]; ?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="5">

							<?php if ($monthly =true) : ?>
										<h3 class="title_view">
										ประจำเดือน <?php echo $title_date;?>
									</h3>
									<?php else : ?>
								<h3 class="title_view">
									<?php 
										echo "วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
									?>
								</h3>
								<?php endif;?>
							</th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="5">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="5">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
						</tr>
				</table>
				<table class="table table-bordered">
					<thead> 
						<tr>
							<th class="table_header_top" style="width: 10%;vertical-align: middle;">ลำดับ</th>
							<th class="table_header_top" style="width: 20%;vertical-align: middle;">เลขที่บัญชี</th>
							<th class="table_header_top" style="width: 30%;vertical-align: middle;">ชื่อ-นามสกุล</th>
							<th class="table_header_top" style="width: 20%;vertical-align: middle;">รหัสสมาชิก</th>
							<th class="table_header_top" style="width: 20%;vertical-align: middle;">ดอกเบี้ยสะสม
                        </th>
						</tr>  
					</thead>
					<tbody>
						<?php
                        $total = 0;
                        ?>
                        <?php if (!empty($data)) : ?> 
                        <?php foreach (@$data as $key => $row) : ?>
							<tr> 
								<td class="table_body" style="text-align: center;vertical-align: top;"><?= $key + 1 ?></td>
								<td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'@';">
									<?php echo @$this->center_function->format_account_number($row['account_id']); ?>
								</td>					 
								<td class="table_body" style="text-align: left;vertical-align: top;"><?= $row['prename_short']." ".$row['firstname_th']." ".$row['lastname_th']; ?></td>
								<td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'@';"><?=$row['mem_id']; ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?= number_format($row['old_acc_int'], 2); ?></td>
							</tr>											
						
						<?php $total += $row['old_acc_int']; ?>
                        <?php endforeach; ?>
                        <?php endif; ?>
						<tr>
							<td colspan="4" class="table_body" style="text-align: center;vertical-align: top;">
									รวม
							</td>
							<td class="table_body" style="text-align: center;vertical-align: top;">
									<?php echo number_format($total, 2) ?>
							</td>
						</tr>
				</tbody>    
			</table>
		</body>
	</html>
</pre>