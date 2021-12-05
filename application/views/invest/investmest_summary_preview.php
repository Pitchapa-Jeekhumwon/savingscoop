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
	.title_view {
		font-size: 32px;
    	font-weight: bold;
	}
	.topic_view {
		color: #000;
		font-weight: bold;
	}
	@media print { body { -webkit-print-color-adjust: exact; } }
</style>
<div style="width: 11.7in;" class="page-break">
	<div class="panel panel-body" style="padding-top:30px; !important;height: 8.3in;">
		<table style="width: 100%;">
			<tr>
				<td style="width:100px;vertical-align: top;">
				</td>
				<td class="text-center">
					<h3 class="title_view">Investment Summary</h3>
				</td>
				<td style="width:100px;vertical-align: top;" class="text-right">
					<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
				</td>
			</tr>
		</table>
		&nbsp;
		<table style="width: 100%;">
			<tr>
				<td style="width:32%;">
					<h3 class="topic_view">Credit Company</h3>
				</td>
				<td>
					<h3 class="topic_view">Asset Allocation</h3>
				</td>
			</tr>
			<tr>
				<td>
					<div class="card" style="display: block; max-width: 100%;  margin: auto;border:0;">
						<div class="card-chart chart-container"  style="display: block; max-width: 100%; ">
							<canvas id="summaryChart" style="max-width: 90%; margin-left: auto; margin-right: auto; display: block;" width="90" height="90"></canvas>
						</div>
					</div>
				</td>
				<td style=" vertical-align:top; padding-top:20px;">
					<table class="table table-view table-center">
						<thead>
							<tr>
								<th>องค์กร</th>
								<th>ประเภทการลงทุน</th>
								<th>การลงทุน</th>
								<th>เงินลงทุน</th>
								<th>รวม</th>
							</tr>
						</thead>
						<tbody>
						<?php
							$org_prev = 'xx';
							foreach($orgs as $org_key => $org) {
								foreach($org['invests'] as $invest) {
						?>
							<tr>
								<?php
									if($org_prev != $org_key) {
								?>
								<td rowspan="<?php echo count($org['invests']);?>" class="text-left">
									<div style="display:flex;">
										<div id="color_box_<?php echo $org_key;?>" class="color_box" style="background-color: #cfc; padding: 10px;">
										</div>
										&nbsp;
										<?php echo $org['name'];?>
									</div>
								</td>
								<?php
									}
								?>
								<td class="text-left"><?php echo $invest['type_name'];?></td>
								<td class="text-left"><?php echo $invest['name'];?></td>
								<td class="text-right"><?php echo number_format($invest['amount'],2);?></td>
								<?php
									if($org_prev != $org_key) {
								?>
								<td rowspan="<?php echo count($org['invests']);?>" class="text-right"><?php echo number_format($org['amount'],2)."<br>(".(number_format(($org['amount'] * 100 / $total_invest),2))."%)";?></td>
								<?php
									}
								?>
							</tr>
						<?php
									$org_prev = $org_key;
								}
							}
						?>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/Chart.min.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
$link = array(
    'src' => PROJECTJSPATH.'assets/js/chartjs_plugin/chartjs-plugin-labels.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
<script media="screen">
    $(document).ready(function() {

		//Generate summary chart.
		var ctx = document.getElementById('summaryChart');
		color_default_arr = ['#fc7f23','#1f78b4','#d52728','#2e9f2e','#2e9f9a','#9d2e9f','#452e9f'];
		var randomScalingFactor = function() {
			return Math.round(Math.random() * 100);
		};

		var dynamicColors = function() {
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            return "rgb(" + r + "," + g + "," + b + ")";
         };
		var ctx = document.getElementById("summaryChart").getContext("2d");

		chart_labels = [];
		chart_values = [];
		chart_colors = [];
		org_index = 0;
		<?php
			foreach($orgs as $org_key => $org) {
		?>
			chart_labels.push('<?php echo $org['name'];?>');
			chart_values.push(<?php echo $org['amount'];?>);
			color_code = color_default_arr[org_index] ? color_default_arr[org_index] : dynamicColors();
			chart_colors.push(color_code);
			$("#color_box_<?php echo $org_key;?>").attr('style', ' padding: 10px; background-color: ' + color_code + " !important;");
			org_index++;
		<?php
			}
		?>
		var myDoughnut = new Chart(ctx, {
			type: 'pie',
			data: {
				labels: chart_labels,
				datasets: [{
					data: chart_values,
					backgroundColor: chart_colors,
					borderColor: 'white',
					borderWidth: 0,
				}]
			},
			showDatapoints: true,
			options: {
				tooltips: {
					enabled: false
				},
				pieceLabel: {
					mode: 'value',
					render: 'label',
					fontColor: '#000',
					position: 'outside',
				},
				responsive: true,
				legend: {
					display: false,
				},
				plugins: {
      				labels: {
						outsidePadding: 5,
						textMargin: 10,
						position: 'outside'
					}
				}
			}
		});
	});
</script>