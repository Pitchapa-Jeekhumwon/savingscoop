<style>
	.table-view>thead,
	.table-view>thead>tr>td,
	.table-view>thead>tr>th {
		font-size: 14px;
	}

	.table-view-2>thead>tr>th {
		border-top: 1px solid #000 !important;
		border-bottom: 1px solid #000 !important;
		font-size: 16px;
	}

	.table-view-2>tbody>tr>td {
		border: 0px !important;
		/*font-family: upbean;
		font-size: 16px;*/
		font-family: Tahoma;
		font-size: 12px;
	}

	.border-bottom {
		border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}

	.foot-border {
		border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}

	.table {
		color: #000;
	}

	@media print {
		.pagination {
			display: none;
		}
	}
</style>
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
$line_per_page = 0;
$page = 1;
$sum_interest = 0;
?>

<?php if ($data) : ?>
	<?php foreach ($data as $k => $row) : ?>
		<?php if ($line_per_page == 0) : ?>
			<div style="width: 1000px;" class="page-break">
				<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
					<?php if ($k == 0) : ?>
						<table style="width: 100%;">
							<tr>
								<td style="width:100px;vertical-align: top;">
								</td>
								<td class="text-center">
									<img src="<?php echo base_url(PROJECTPATH . '/assets/images/coop_profile/' . $_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
									<h3 class="title_view"><?php echo @$_SESSION['COOP_NAME']; ?></h3>
									<h3 class="title_view">รายงานสรุปดอกเบี้ยสะสมเงินฝาก<?= ($monthly = true) ? 'ประจำเดือน' : '' ?> <?= @$type_name; ?></h3>
									<?php if (@$_GET['apply_type_id'] != '') { ?>
										<h3 class="title_view">ประเภทสมัครสมาชิก <?= (@$_GET['apply_type_id'] == 0) ? "ทั้งหมด" : @$mem_apply_type[@$_GET['apply_type_id']]; ?></h3>
									<?php } ?>
									<?php if ($monthly =true) : ?>
										<h3 class="title_view">
										ประจำเดือน <?php echo $title_date;?>
									</h3>
									<?php else : ?>
										<h3 class="title_view">
											<?= "วันที่ " . $this->center_function->ConvertToThaiDate($start_date); ?>
										</h3>
									<?php endif ?>

								</td>
								<td style="width:100px;vertical-align: top;" class="text-right">
									<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
									<?php
									$get_param = '?';
									foreach (@$_GET as $key => $value) {
										//if($key != 'month' && $key != 'year' && $value != ''){
										$get_param .= $key . '=' . $value . '&';
										//}
									}
									$get_param = substr($get_param, 0, -1);
									?>
									<a class="no_print" target="_blank" href="<?= ($monthly==true)? base_url('/report_deposit_data/coop_report_conclude_interest_monthly_preview' . $get_param . "&Excel=true") : base_url('/report_deposit_data/coop_report_conclude_interest_preview' . $get_param . "&Excel=true"); ?>">
										<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
									</a>
								</td>
							</tr>
							<tr>
								<td colspan="3" style="text-align: left;">
									<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'), 1, 0); ?></span>
									<span class="title_view"> เวลา <?php echo date('H:i:s'); ?></span>
								</td>
							</tr>
						</table>
						<?php $line_per_page += 8 ?>
					<?php endif; ?>
					<br>
					<div class="title_view" style="text-align: left;">หน้าที่ <?= $page ?> / <?= $page_all ?></div><br>
					<?php $line_per_page++ ?>
					<table class="table table-view table-center">
						<thead>
							<tr>
								<th style="width: 10%;vertical-align: middle;">ลำดับ</th>
								<th style="width: 15%;vertical-align: middle;">เลขที่บัญชี</th>
								<th style="width: 40%;vertical-align: middle;">ชื่อ-นามสกุล</th>
								<th style="width: 20%;vertical-align: middle;">รหัสสมาชิก</th>
								<th style="width: 15%;vertical-align: middle;">ดอกเบี้ยสะสม</th>
							</tr>
						</thead>
						<tbody>
							<?php $line_per_page++ ?>
						<?php endif; ?>
						<tr>
							<td style="text-align: center;vertical-align: top;"><?= $k + 1 ?></td>
							<td style="text-align: center;vertical-align: top;">
								<?= @$this->center_function->format_account_number($row['account_id']); ?>
							</td>
							<td style="text-align: left;vertical-align: top;"><?= $row['prename_short'] . " " . $row['firstname_th'] . " " . $row['lastname_th']; ?></td>
							<td style="text-align: center;vertical-align: top;"><?= $row['mem_id']; ?></td>
							<td style="text-align: right;vertical-align: top;"><?= number_format($row['old_acc_int'], 2); ?></td>
						</tr>
						<?php $sum_interest += $row['old_acc_int'] ?>
						<?php $line_per_page++ ?>
						<?php if ($line_per_page >= $max_line - 1 || ($k + 1) == $num_rows) : ?>
							<?php if (($k + 1) == $num_rows) : ?>
								<tr>
									<td colspan="4" style="text-align: center;vertical-align: top;">รวม</td>
									<td style="text-align: right;vertical-align: top;"><?= number_format($sum_interest, 2); ?></td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			<?php $line_per_page = 0;
							$page++;
			?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php else : ?>
<?php endif; ?>