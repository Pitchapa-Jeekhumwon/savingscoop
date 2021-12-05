<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=".$row_meeting['meeting_name'].".xls");
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
					font-size: 16pt;
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
					font-size: 16pt;
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
					font-size: 16pt;
					border: thin solid black;
					vertical-align: top;
				}
				.table_body_right{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 21px;
					border: thin solid black;
					text-align:right;
				}
			</style>
		</head>
		<body>
<?php
$last_runno = 0;

	?>
				<table style="width: 100%;" style="background-color: #FFF !important;">
                    <tr>
						<th class="table_title" colspan="7"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="7"><?php echo $row_meeting['meeting_name']; ?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="7"><?php echo "วันที่ ".$this->center_function->ConvertToThaiDate($row_meeting['meeting_date'],1,0); ?></th>
					</tr>
				</table>

				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">ลำดับ</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">รหัสสมาชิก</th>
							<th class="table_header_top" style="width: 300px;vertical-align: middle;">ชื่อ - นามสกุล</th>
							<th class="table_header_top" style="width: 300px;vertical-align: middle;">วันที่/เวลาลงทะเบียน</th>
							<th class="table_header_top" style="width: 300px;vertical-align: middle;">การลงทะเบียน</th>
							<th class="table_header_top" style="width: 300px;vertical-align: middle;">เลขหางบัตร</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">ของทีระลึก</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$runno = $last_runno;
							$total = 0;
							if(!empty($data)){
								foreach(@$data as $key => $row){
									$regis_type = '';
									if( $row['facescan_id'] ) $regis_type = 'ใบหน้า';
									elseif( $row['id_card_data'] ) $regis_type = 'บัตรประชาชน';
									else $regis_type = 'เลขสมาชิก';
									$runno++;
						?>
							<tr>
								<td class="table_body" style="text-align: center;"><?php echo number_format($key + 1);?></td>
								<td class="table_body" style="text-align: center;"><?php echo $row['member_id'];?></td>
								<td class="table_body" style="text-align: left;"><?php echo $row['firstname_th'].' '.$row['lastname_th'];?></td>
								<td class="table_body" style="text-align: center;"><?php echo $this->center_function->ConvertToThaiDate($row['create_time']);?></td>
								<td class="table_body" style="text-align: center;"><?php echo $regis_type; ?></td>
								<td class="table_body" style="text-align: center;"><?php echo $row['card_tail_number']; ?></td>
								<td class="table_body" style="text-align: center;"><?php echo $row['is_gift'] ? '/' : ''; ?></td>
							</tr>

						<?php
								//$total += $row['transaction_balance'];
								}
							}
							$last_runno = $runno;
						?>
							<?php /*<tr>
								<td colspan="4" class="table_body" style="text-align: center;vertical-align: top;">
									รวม
								</td>
								<td class="table_body" style="text-align: center;vertical-align: top;">
									<?php echo number_format($total,2)?>
								</td>
							</tr>*/ ?>
					</tbody>
				</table>
		</body>
	</html>
</pre>