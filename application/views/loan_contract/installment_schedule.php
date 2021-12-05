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
		font-size: 11px;
	}

	.border-bottom {
		border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}

	.table-view-2>tbody>tr>td>span {
		font-family: Tahoma;
		font-size: 11px;
	}

	.foot-border {
		border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}
	.title_view>h2{
	margin:0px;
	}
	.table {
		color: #000;
	}
	@media print {
	
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

if (@$_GET['end_date']) {
	$end_date_arr = explode('/', @$_GET['end_date']);
	$end_day = $end_date_arr[0];
	$end_month = $end_date_arr[1];
	$end_year = $end_date_arr[2];
	$end_year -= 543;
	$end_date = $end_year . '-' . $end_month . '-' . $end_day;
}
if (!empty($data)) {
?>

	<div style="width: 1000px;" class="page-break">
		<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
			<table style="width: 100%;">
				<tr>
					<td style="width:100px;vertical-align: top;">
					</td>
					<td class="text-center">
						<img src="<?php echo base_url(PROJECTPATH . '/assets/images/coop_profile/' . $_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
						<h3 class="title_view"><?php echo @$_SESSION['COOP_NAME']; ?></h3>
						<h3 class="title_view">ตารางคำนวณการชำระเงิน</h3>

					</td>
					<td style="width:100px;vertical-align: top;" class="text-right">
						<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<table style="width: 100%;">
							<tr>
								<td class="title_view"><h2>รหัสสมาชิก : <?= $head['member_id'] ?></h2></td>
								<td class="title_view"><h2>อัตราดอกเบี้ย : <?= number_format($head['interest_per_year'], 2) ?> %</h2></td>
							</tr>
							<tr>
								<td class="title_view"><h2>จำนวนงวด : <?= $head['period_amount'] ?> งวด</h2></td>
								<td class="title_view"><h2>จำนวนเงินกู้ :<?=number_format($head['loan_amount'],2)?>  บาท</h2></td>
							</tr>
							<tr>
								<td  class="title_view"><h2>วันที่เริ่มชำระงวดแรก : <?= $head['createdatetime'] ?></h2></td>
							</tr>
					</table>
				</td>
			</tr>
			</table>
			<table class="table table-view table-center">
				<thead>
					<tr>
						<th style="vertical-align: middle;">งวดที่</th>
						<th style="vertical-align: middle;">รายเดือน</th>
						<th style="vertical-align: middle;">ดอกเบี้ย</th>
						<th style="vertical-align: middle;">เงินต้น</th>
						<th style="vertical-align: middle;">คงเหลือ</th>
						<th style="vertical-align: middle;">หุ้นสะสม</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($data['coop_loan_period'] as $k => $val) : ?>
						<tr>
							<td style="text-align: center;vertical-align: top;"><?= $val['period_count'] ?></td>
							<td style="text-align: right;vertical-align: top;"><?= number_format($val['total_paid_per_month'], 2) ?></td>
							<td style="text-align: right;vertical-align: top;"><?= number_format($val['interest'], 2) ?></td>
							<td style="text-align: right;vertical-align: top;"><?= number_format($val['principal_payment'], 2) ?></td>
							<td style="text-align: right;vertical-align: top;"><?= number_format($val['outstanding_balance'], 2) ?></td>
							<td style="text-align: right;vertical-align: top;"></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
<?php
}
?>
