<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานยอดจ่ายเงินกู้สุทธิ ของผู้ยื่นกู้.xls"); 
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
			</style>
		</head>
		<body>
<?php
if(@$_GET['start_date']){
	$start_date_arr = explode('/',@$_GET['start_date']);
	$start_day = $start_date_arr[0];
	$start_month = $start_date_arr[1];
	$start_year = $start_date_arr[2];
	$start_year -= 543;
	$start_date = $start_year.'-'.$start_month.'-'.$start_day;
}

if(@$_GET['end_date']){
	$end_date_arr = explode('/',@$_GET['end_date']);
	$end_day = $end_date_arr[0];
	$end_month = $end_date_arr[1];
	$end_year = $end_date_arr[2];
	$end_year -= 543;
	$end_date = $end_year.'-'.$end_month.'-'.$end_day;
}
if(@$_GET['month']!='' && @$_GET['year']!=''){
	$day = '';
	$month = @$_GET['month'];
	$year = @$_GET['year'];
	$title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year);
}else{
	$day = '';
	$month = '';
	$year = @$_GET['year'];
	$title_date = " ปี ".(@$year);
}
$sum_loan_amount = 0;
$sum_buy_share =0;
$sum_debet = 0;
$sum_depet_int = 0;
$sum_insulance = 0;
$sum_deposit = 0;
$sum_other =0;
$sum_duty = 0;
$sum_balance =0;
	?>
				<table class="table table-bordered">
					<tr>
						<tr>
							<th class="table_title" colspan="15"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="15">รายงานยอดจ่ายเงินกู้สุทธิ ของผู้ยื่นกู้ </th>
						</tr>
						<tr>
							<th class="table_title" colspan="15">
								<h3 class="title_view">
									<?php 
										echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"ตั้งแต่";
										echo "วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
										echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึงวันที่  ".$this->center_function->ConvertToThaiDate($end_date);
									?>
								</h3>
							</th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="15">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="15">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
						</tr>
					</tr> 
				</table>
				<table class="table table-bordered">
					<thead> 
						<tr>
							<th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
							<th class="table_header_top" style="vertical-align: middle;">เลขที่</th>
							<th class="table_header_top" style="vertical-align: middle;">รหัสสมาชิก</th>
							<th class="table_header_top" style="vertical-align: middle;">ชื่อสมาชิก</th>
							<th class="table_header_top" style="vertical-align: middle;">บัญชีธนาคาร</th>
							<th class="table_header_top" style="vertical-align: middle;">ให้กู้</th>
                            <th class="table_header_top" style="vertical-align: middle;">ซื้อหุ้น</th>
							<th class="table_header_top" style="vertical-align: middle;">หนี้ค้าง</th>
							<th class="table_header_top" style="vertical-align: middle;">ดอกเบี้ย</th>
                            <th class="table_header_top" style="vertical-align: middle;">ประกัน</th>
							<th class="table_header_top" style="vertical-align: middle;">เงินฝาก</th>
							<th class="table_header_top" style="vertical-align: middle;">หนี้อื่นๆ</th>
                            <th class="table_header_top" style="vertical-align: middle;">อากร</th>
							<th class="table_header_top" style="vertical-align: middle;">เหลือสุทธิ</th>
							<th class="table_header_top" style="vertical-align: middle;">เชคเลขที่</th>
						</tr>  
					</thead>
					<tbody>
						<?php foreach ($data as $k=>$val) :    
                        $balance = ($val['loan_amount'] - $deduct[$val['loan_id']]['deduct_share'] -$val['debet'] -$val['debet_int']  -$deduct[$val['loan_id']]['deduct_insurance'] -$deduct[$val['loan_id']]['deduct_blue_deposit']- $deduct[$val['loan_id']]['deduct_other'] -$deduct[$val['loan_id']]['']);
                        $sum_loan_amount +=$val['loan_amount'] ;
                        $sum_buy_share +=$deduct[$val['loan_id']]['deduct_share'];
                        $sum_debet +=$val['debet'];
                        $sum_depet_int +=$val['debet_int'];
                        $sum_insulance +=$deduct[$val['loan_id']]['deduct_insurance'];
                        $sum_deposit +=$deduct[$val['loan_id']]['deduct_blue_deposit'];
                        $sum_other +=$deduct[$val['loan_id']]['deduct_other'];
                        $sum_duty +=$deduct[$val['loan_id']][''];
                        $sum_balance +=$balance;
                            ?>
							<tr> 
							  <td class="table_body" style="text-align: center;vertical-align: top;"> <?=$k+1?></td>
                              <td class="table_body" style="text-align: center;vertical-align: top;"><?=$val['petition_number']?></td>
                              <td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'@'"><?=$val['member_id']?></td>
                              <td class="table_body" style="text-align: center;vertical-align: top;"><?=$val['member_name']?></td>
                              <td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'@'"><?=$val['account_number']?> </td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($val['loan_amount'],2)?> </td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($deduct[$val['loan_id']]['deduct_share'],2)?></td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($val['debet'],2)?></td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($val['debet_int'],2)?></td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($deduct[$val['loan_id']]['deduct_insurance'],2)?></td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($deduct[$val['loan_id']]['deduct_blue_deposit'],2)?> </td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($deduct[$val['loan_id']]['deduct_other'],2)?></td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($deduct[$val['loan_id']][''],2)?> </td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($balance,2)?></td>
                              <td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'@'"> <?=$val['ceche']?> </td>
							</tr>											
						
                        <?php endforeach; ?>
							<tr>
                              <td class="table_body" style="text-align: center;vertical-align: top; " colspan="5"> รวมทั้งสิ้น </td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($sum_loan_amount,2)?></td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($sum_buy_share,2)?></td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"> <?=number_format($sum_debet,2)?></td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"> <?=number_format($sum_depet_int,2)?></td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($sum_insulance,2)?></td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($sum_deposit,2)?> </td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($sum_other,2)?></td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($sum_duty,2)?></td>
                              <td class="table_body" style="text-align: right;vertical-align: top;"><?=number_format($sum_balance,2)?></td>
                              <td class="table_body" style="text-align: center;vertical-align: top;"> </td>
							</tr>
					</tbody>    
				</table>
		</body>
	</html>
</pre>