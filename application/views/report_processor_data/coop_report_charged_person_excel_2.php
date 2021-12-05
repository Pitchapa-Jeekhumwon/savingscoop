<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=สรุปรายการเรียกเก็บ รายบุคคล.xls");
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
	if(@$_GET['month']!='' && @$_GET['year']!=''){
		$day = '';
		$month = @$_GET['month'];
		$year = (@$_GET['year']);
		$title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year);
	}else{
		$day = '';
		$month = '';
		$year = (@$_GET['year']);
		$title_date = " ปี ".(@$year);
	}
    $runno = 0;
	?>
			<table class="table table-bordered">
				<tr>
					<tr>
						<th class="table_title" colspan="21"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="21">สรุปรายการเรียกเก็บ รายบุคคล</th>
					</tr>
					<tr>
						<th class="table_title" colspan="21"><?php echo " ประจำ ".$title_date;?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="21">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="21">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>
			<table class="table table-bordered">
				<thead>
					<tr>
                        <th class="table_header_top" rowspan="2" style="vertical-align: middle;">ลำดับ</th>
						<th class="table_header_top" rowspan="2" style="vertical-align: middle;">สังกัด</th>
						<th class="table_header_top" rowspan="2" style="vertical-align: middle;">หน่วยงานย่อย</th>
						<th class="table_header_top" rowspan="2" style="vertical-align: middle;">ประเภท</th>
						<th class="table_header_top" rowspan="2" style="vertical-align: middle;">เลขพนักงาน</th>
						<th class="table_header_top" rowspan="2" style="vertical-align: middle;">ชื่อ-นามสกุล</th>
						<th class="table_header_top" rowspan="2" style="vertical-align: middle;">หุ้น</th>
						<?php
							foreach($loan_type AS $key=>$row_loan_type){
						?>
						<th class="table_header_top" colspan="3" style="vertical-align: middle;"><?php echo str_replace('เงินกู้','',$row_loan_type['loan_type']);?></th>
						<?php
							}
						?>
						<th class="table_header_top" colspan="2" style="vertical-align: middle;">เงินฝาก</th>
						<th class="table_header_top" rowspan="2" style="vertical-align: middle;">ฌสอ สป</th>
						<th class="table_header_top" rowspan="2" style="vertical-align: middle;">ค่าธรรมเนียมแรกเข้า</th>
						<th class="table_header_top" rowspan="2" style="vertical-align: middle;">อื่นๆ</th>
						<th class="table_header_top" rowspan="2" style="vertical-align: middle;">ชำระหนี้ค้ำประกัน</th>
						<th class="table_header_top" rowspan="2" style="vertical-align: middle;">รวม</th>
					</tr>
					<tr>
						<th class="table_header_top" style="vertical-align: middle;">เลขที่สัญญา</th>
						<th class="table_header_top" style="vertical-align: middle;">เงินต้น</th>
						<th class="table_header_top" style="vertical-align: middle;">ดอกเบี้ย</th>
						<th class="table_header_top" style="vertical-align: middle;">เลขที่สัญญา</th>
						<th class="table_header_top" style="vertical-align: middle;">เงินต้น</th>
						<th class="table_header_top" style="vertical-align: middle;">ดอกเบี้ย</th>
						<th class="table_header_top" style="vertical-align: middle;">เลขที่สัญญา</th>
						<th class="table_header_top" style="vertical-align: middle;">เงินต้น</th>
						<th class="table_header_top" style="vertical-align: middle;">ดอกเบี้ย</th>
						<th class="table_header_top" stype="vertical-align: middle;">เลขบัญชี</th>
						<th class="table_header_top" stype="vertical-align: middle;">จำนวนเงิน</th>
					</tr>
				</thead>
				<tbody>
				<?php

                $tmp_group = "";
                $tmp_lv = 0;
						foreach($datas as $member_id => $data){
							if (!empty($data['total'])) {
								$runno++;
								$depositCount = !empty($data['DEPOSIT']) ? count($data['DEPOSIT']) : 1;
								$normalCount = !empty($data['normal']) ? count($data['normal']) : 1;
								$emergentCount = !empty($data['emergent']) ? count($data['emergent']) : 1;
								$specialCount = !empty($data['special']) ? count($data['special']) : 1;
								$max_index = max(array($depositCount, $normalCount, $emergentCount, $specialCount));
								for($i = 0; $i < $max_index; $i++) {
                                    if($tmp_group == ""){
                                        $tmp_group = $data['mem_group_name'];
                                        $tmp_lv = $data['lv'];
                                    }else if($tmp_group != "" && $tmp_group != $data['mem_group_name']){
										$runno = 1;
                                        ?>
                                        <tr>
                                            <td class="table_body" style="text-align: center;" colspan="5">รวม</td>
                                            <td class="table_body" style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_SHARE']) ? number_format($group_total_data[$tmp_lv.'_SHARE'], 2) : '';?></td>
                                                            <!-- ฉุกเฉิน -->
                                            <td class="table_body" style="text-align: center;"></td>
                                            <td class="table_body" style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_emergent_principal']) ? number_format($group_total_data[$tmp_lv.'_emergent_principal'],2) : '';?></td>
                                            <td class="table_body" style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_emergent_interest']) ? number_format($group_total_data[$tmp_lv.'_emergent_interest'],2) : '';?></td>
                                                            <!-- สามัญ -->
                                            <td class="table_body" style="text-align: center;"></td>
                                            <td class="table_body" style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_normal_principal']) ? number_format($group_total_data[$tmp_lv.'_normal_principal'],2) : '';?></td>
                                            <td class="table_body" style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_normal_interest']) ? number_format($group_total_data[$tmp_lv.'_normal_interest'],2) : '';?></td>
                                                            <!-- พิเศษ -->
                                            <td class="table_body" style="text-align: center;"></td>
                                            <td class="table_body" style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_special_principal']) ? number_format($group_total_data[$tmp_lv.'_special_principal'],2) : '';?></td>
                                            <td class="table_body" style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_special_interest']) ? number_format($group_total_data[$tmp_lv.'_special_interest'],2) : '';?></td>
                                            <td class="table_body" style="text-align: right;" ></td>
                                            <td class="table_body" style="text-align: right;" ><?php echo !empty($group_total_data[$tmp_lv.'_DEPOSIT']) ? number_format($group_total_data[$tmp_lv.'_DEPOSIT'],2) : '';?></td>
                                            <td class="table_body" style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_CREMATION']) ? number_format($group_total_data[$tmp_lv.'_CREMATION'],2) : '';?></td>
                                            <td class="table_body" style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_REGISTER_FEE']) ? number_format($group_total_data[$tmp_lv.'_REGISTER_FEE'],2) : '';?></td>
                                            <td class="table_body" style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_OTHER']) ? number_format($group_total_data[$tmp_lv.'_OTHER'],2) : '';?></td>
                                            <td class="table_body" style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_GUARANTEE_AMOUNT']) ? number_format($group_total_data[$tmp_lv.'_GUARANTEE_AMOUNT'],2) : '';?></td>
                                            <td class="table_body" style="text-align: right;"><?php echo !empty($group_total_data[$tmp_lv.'_total_amount']) ? number_format($group_total_data[$tmp_lv.'_total_amount'],2) : '';?></td>
                                        </tr>
                        <?php
                                $tmp_lv = $data['lv'];
                                $tmp_group = $data['mem_group_name'];
                            }
                        ?>
							<tr>
                                <td class="table_body" style="text-align: center;"><?php echo $runno;?></td>
								<td class="table_body" style="text-align: center;"><?php echo @$data['mem_group_name2']; ?></td>
								<td class="table_body" style="text-align: center;"><?php echo @$data['mem_group_name']; ?></td>
                                <td class="table_body" style="text-align: center;"><?php echo @$data['mem_type_name']; ?></td>
								<td class="table_body" style="text-align: center;"><?php echo $data['member_id']; ?></td>
								<td class="table_body" style="text-align: left;"><?php echo $data['member_name'];?></td>
								<td class="table_body" style="text-align: right;"><?php echo $i == 0  && !empty($data['SHARE']) ? number_format($data['SHARE'],2) : ""; ?></td>
								<!-- ฉุกเฉิน -->
								<td class="table_body" style="text-align: center;"><?php echo $data['emergent'][$data['emergent_ids'][$i]]['contract_number'];?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($data['emergent'][$data['emergent_ids'][$i]]['principal']) ? number_format($data['emergent'][$data['emergent_ids'][$i]]['principal'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($data['emergent'][$data['emergent_ids'][$i]]['interest']) ? number_format($data['emergent'][$data['emergent_ids'][$i]]['interest'],2) : '';?></td>
								<!-- สามัญ -->
								<td class="table_body" style="text-align: center;"><?php echo $data['normal'][$data['normal_ids'][$i]]['contract_number'];?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($data['normal'][$data['normal_ids'][$i]]['principal']) ? number_format($data['normal'][$data['normal_ids'][$i]]['principal'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($data['normal'][$data['normal_ids'][$i]]['interest']) ? number_format($data['normal'][$data['normal_ids'][$i]]['interest'],2) : '';?></td>
								<!-- พิเศษ -->
								<td class="table_body" style="text-align: center;"><?php echo $data['special'][$data['special_ids'][$i]]['contract_number'];?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($data['special'][$data['special_ids'][$i]]['principal']) ? number_format($data['special'][$data['special_ids'][$i]]['principal'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($data['special'][$data['special_ids'][$i]]['interest']) ? number_format($data['special'][$data['special_ids'][$i]]['interest'],2) : '';?></td>

								<td class="table_body" style="text-align: right;"><?php echo $data['deposit_account_id'][$i];?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($data['DEPOSIT'][$i]) ? number_format($data['DEPOSIT'][$i],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo $i == 0 && !empty($data['CREMATION']) ? number_format($data['CREMATION'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo $i == 0 && !empty($data['REGISTER_FEE']) ? number_format($data['REGISTER_FEE'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo $i == 0 && !empty($data['OTHER']) ? number_format($data['OTHER'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo $i == 0 && !empty($data['GUARANTEE_AMOUNT']) ? number_format($data['GUARANTEE_AMOUNT'],2): '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo $i == 0 ? number_format($data['total'],2) : "";?></td>
                      </tr>

					<?php
								}
							}
						}
					?>
						   <tr>
								<td class="table_body" style="text-align: center;" colspan="4">รวมทั้งสิ้น</td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($total_data['SHARE']) ? number_format($total_data['SHARE']) : '';?></td>
								<!-- ฉุกเฉิน -->
								<td class="table_body" style="text-align: center;"></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($total_data['emergent_principal']) ? number_format($total_data['emergent_principal'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($total_data['emergent_interest']) ? number_format($total_data['emergent_interest'],2) : '';?></td>
								<!-- สามัญ -->
								<td class="table_body" style="text-align: center;"></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($total_data['normal_principal']) ? number_format($total_data['normal_principal'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($total_data['normal_interest']) ? number_format($total_data['normal_interest'],2) : '';?></td>
								<!-- พิเศษ -->
								<td class="table_body" style="text-align: center;"></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($total_data['special_principal']) ? number_format($total_data['special_principal'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($total_data['special_interest']) ? number_format($total_data['special_interest'],2) : '';?></td>
								<td class="table_body" style="text-align: right;" ></td>
								<td class="table_body" style="text-align: right;" ><?php echo !empty($total_data['DEPOSIT']) ? number_format($total_data['DEPOSIT'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($total_data['CREMATION']) ? number_format($total_data['CREMATION'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($total_data['REGISTER_FEE']) ? number_format($total_data['REGISTER_FEE'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($total_data['OTHER']) ? number_format($total_data['OTHER'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($total_data['GUARANTEE_AMOUNT']) ? number_format($total_data['GUARANTEE_AMOUNT'],2) : '';?></td>
								<td class="table_body" style="text-align: right;"><?php echo !empty($total_data['total_amount']) ? number_format($total_data['total_amount'],2) : '';?></td>
						  </tr>
                </tbody>
			</table>
		</body>
	</html>
</pre>
