<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php
	set_time_limit(600);
	error_reporting(E_ALL);

	define("HOSTNAME","103.233.192.68") ;
	define("DBNAME","coop_spktcsys");
	define("USERNAME","admin_spktcsys");
	define("PASSWORD",'Qzij45#0');

	$mysqli = new mysqli( HOSTNAME , USERNAME , PASSWORD );
	$mysqli->select_db(DBNAME);
	$mysqli->query("SET NAMES utf8");

	echo '<h3>คำนวนเงินคืน ฉATM </h3>';

	$member_id = '013379'; //เคส 1 เคสที่ ไม่มีการชำระระหว่างเดือน
	$member_id = '006351'; //เคส 4 เคสที่ มีการชำระ บางส่วน ก่อนผ่านรายการ
	$member_id = '011503'; //เคส 2 เคสที่ มีการชำระหนี้ทั้งหมด ระหว่างเดือน
	$member_id = sprintf('%06d', $_GET['member_id']);
	$year = isset($_GET['y']) ? $_GET['y'] : 2018 ;
	$month = isset($_GET['m']) ? $_GET['m'] : 10 ;

	cal_return_atm($mysqli, $member_id, $year, $month);

	function cal_return_atm($mysqli, $member_id, $year, $month) {
		$sql = "SELECT loan_atm_id, contract_number
						FROM coop_loan_atm
						WHERE member_id = '{$member_id}'
							";//AND loan_atm_status = 1
		$rs_loan_atm = $mysqli->query($sql);
		$total_day_of_month = date_format(date_create("{$year}-{$month}-01"), 't') ;
		while(( $row_loan_atm = $rs_loan_atm->fetch_assoc() )) {
			$loan_atm_id = $row_loan_atm['loan_atm_id'];
			$contract_number = $row_loan_atm['contract_number'];
			echo "<div>สัญญาเลขที่ {$contract_number} ( {$loan_atm_id} ) end date @ {$total_day_of_month}</div>";
			$sql = "SELECT *
							FROM (
							SELECT 1 payment_type,
							tb1.loan_description,
							tb1.loan_amount,
							0 interest,
							tb2.loan_amount_balance,
							tb1.loan_date
							FROM coop_loan_atm_detail tb1
							LEFT JOIN coop_loan_atm_transaction tb2 ON tb1.loan_atm_id = tb2.loan_atm_id
								AND tb1.loan_date = tb2.transaction_datetime
							WHERE tb1.member_id = '{$member_id}'
								AND tb1.loan_description NOT LIKE '%ยกมา%'
							UNION ALL
							SELECT CASE WHEN tb1.finance_month_profile_id IS NULL THEN 2 ELSE 3 END payment_type
							, CASE WHEN tb1.finance_month_profile_id IS NULL THEN 'ชำระอื่น ๆ' ELSE 'ชำระรายเดือน' END loan_description
							, SUM(tb2.principal_payment) loan_amount
							, SUM(tb2.interest) interest
							, SUM(tb2.loan_amount_balance) loan_amount_balance
							, tb2.createdatetime loan_date
							FROM coop_receipt tb1
							INNER JOIN coop_finance_transaction tb2 ON tb1.receipt_id = tb2.receipt_id
							WHERE tb2.member_id = '{$member_id}'
									AND tb2.loan_atm_id = '{$loan_atm_id}'
							GROUP BY tb2.member_id, tb2.receipt_id
							) tb
							WHERE loan_date BETWEEN DATE_FORMAT(DATE_ADD('{$year}-{$month}-01', INTERVAL -1 DAY), '%Y-%m-01') AND '{$year}-{$month}-{$total_day_of_month}'
							ORDER BY loan_date ASC";

			$sql = "SELECT *
							FROM (

							SELECT CASE WHEN t4.finance_month_profile_id IS NULL THEN 2 ELSE 3 END payment_type
							, IF (
						t2.loan_description != '',
						`t2`.`loan_description`,

					IF (
						t4.finance_month_profile_id != '',
						'ชำระเงินรายเดือน',
						'ชำระเงินอื่นๆ'
					)
					) loan_description
							, SUM(							IF (
								t2.loan_amount <> '',
								`t2`.`loan_amount`,
								t3.principal_payment
							)) loan_amount
							, SUM(t3.interest) interest
							, IF (
								! ISNULL(
								(
								SELECT
									ret_id
								FROM
									coop_process_return
								WHERE
									coop_process_return.return_month = t6.month_receipt AND coop_process_return.return_year = (t6.year_receipt-543) AND coop_process_return.loan_atm_id = t1.loan_atm_id
								LIMIT 1
								)
								),
								SUM(

								IF (
								t2.loan_amount <> '',
								`t2`.`loan_amount`,
								t3.principal_payment
								)
								) * - 1,
								`t1`.`loan_amount_balance`
								) loan_amount_balance
							, DATE(t1.transaction_datetime) loan_date
							FROM
								`coop_loan_atm_transaction` AS `t1`
							LEFT JOIN `coop_loan_atm_detail` AS `t2` ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id`
							AND `t1`.`transaction_datetime` = `t2`.`loan_date`
							LEFT JOIN `coop_finance_transaction` AS `t3` ON `t1`.`receipt_id` = `t3`.`receipt_id`
							AND `t1`.`loan_atm_id` = `t3`.`loan_atm_id`
							LEFT JOIN `coop_receipt` AS `t4` ON `t3`.`receipt_id` = `t4`.`receipt_id`
							LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
							WHERE
								`t1`.`loan_atm_id` = '{$loan_atm_id}'
							GROUP BY
								`t1`.`transaction_datetime`
							) tb
							WHERE loan_date BETWEEN DATE_FORMAT(DATE_ADD('{$year}-{$month}-01', INTERVAL -1 DAY), '%Y-%m-01') AND '{$year}-{$month}-{$total_day_of_month}'
							ORDER BY loan_date ASC";
			$rs = $mysqli->query($sql);
			echo '
				<table border="1" cellspacing="0" cellpadding="3">
					<thead>
						<tr>
							<th>วันที่</th>
							<th>รายการ</th>
							<th>เงินต้น</th>
							<th>ดอกเบี้ย</th>
							<th>คงเหลือ</th>
							<th>ดบ.ที่ต้องจ่าย</th>
							<th>ดบ.จ่ายสะสม</th>
							<th>จำนวนวันคิดดอก</th>
							<th>ดบ.คืน</th>
						</tr>
					</thead>
					<tbody>
			';
			$date_s = null;
			$date_e = null;

			$total_day_of_year = date_format(date_create('2018-12-31'), 'z') + 1;
			$is_end = false;
			$loan_amount_balance = 0;
			$interest_acc = [];
			$interest = 0;
			$index = 0;
			$is_close = 0;
			$return = 0;
			$return_arr = [];

			$sum = [];

			while(( $row = $rs->fetch_assoc() )) {
				if( !$is_end ) {
					$return = 0;
					if( !$date_s && !$date_e ) {
						$num_of_days = '';
					} elseif( $date_s != null ) {
						$date_e = date_create(explode(' ', $row['loan_date'])[0]);
						$num_of_days = date_diff($date_s, $date_e)->format('%a');
						$date_s = date_create(explode(' ', $row['loan_date'])[0]);
					}

					if( (int)$num_of_days > 0 ) {
						//echo "<div>{$loan_amount_balance} | {$num_of_days} | {$total_day_of_year}</div>";
						$interest_acc[$index] = $loan_amount_balance * (6 / 100) * ($num_of_days/$total_day_of_year);
					} else {
						$interest_acc[$index] = 0;
					}

					if( $row['payment_type'] == 2 ) { // กรณีมีการชำระอื่น ๆ เข้ามา
						//  && $row['loan_amount_balance'] ยังไม่ปิด
						//  && !$row['loan_amount_balance'] ปิด

						//echo "<div>{$row['interest']} ::: ".(array_sum($interest_acc))."</div>";
						$interest_acc[] = $row['interest'] ;
						//$return = abs(round($row['interest'] - array_sum($interest_acc) + $row['interest'], 2));
						$return = 0;
						$return_arr[explode(' ', $row['loan_date'])[0]] = $return;
						$interest_acc = [];
					}

					if( $row['loan_amount_balance'] == 0 || $is_close ) {
						$interest_acc = [];
						$is_close = 1;
					}
					if( $is_close && $row['payment_type'] == 3 ) { // กรณีมีปิด และมีการเรียกเก็บรายเดือน
						$interest_acc[] = $row['loan_amount'] + $row['interest'] ;
						$return = abs(round(array_sum($interest_acc), 2));
						$return_arr[explode(' ', $row['loan_date'])[0]] = $return;
					}

					if( !$is_close && $row['payment_type'] == 3 && $num_of_days ) { // กรณีมียังไม่ปิด และมีการเรียกเก็บรายเดือน
						$return = abs(round($row['interest'] - array_sum($interest_acc), 2));
						$return_arr[explode(' ', $row['loan_date'])[0]] = $return;
					}



					echo sprintf('
							<tr>
								<td>%s</td>
								<td>%s</td>
								<td>%s</td>
								<td>%s</td>
								<td>%s</td>
								<td>%s</td>
								<td>%s</td>
								<td>%s</td>
								<td>%s</td>
							</tr>
					',
					$row['loan_date'],
					$row['loan_description'],
					$row['loan_amount'],
					$row['interest'],
					$row['loan_amount_balance'],
					'',
					round($interest_acc[$index], 2),
					$num_of_days,
					$return
					);
					$sum['return'] += $return;
					if( $row['payment_type'] == 3 ) {
						$date_s = date_create(explode(' ', $row['loan_date'])[0]);
						//$date_s = DateTime::createFromFormat('Y-m-d H:i:s', $row['loan_date']);
					}
					$index++;
					$loan_amount_balance = $row['loan_amount_balance'];
				}

				if( date_format(date_create(explode(' ', $row['loan_date'])[0]), 'Y-m') == "{$year}-{$month}" && $row['payment_type'] == 3 ) {
					//$is_end = true;
					if( !$is_close ) $interest = $row['interest'];
					/*
					echo sprintf('
					<tr>
						<td colspan="6">คืน</td>
						<td>%s</td>
						<td>%s - %s</td>
					</tr>
					', abs(round($interest - array_sum($interest_acc), 2))
					, $interest
					, array_sum($interest_acc)
					);
					*/
				}

			}
			echo sprintf('
			<tr>
				<td colspan="8" align="right">รวม</td>
				<td>%s</td>
			</tr>',
			number_format($sum['return'], 2)
			);
			echo '
					</tbody>
				</table>
			';
			var_dump($return_arr);
		}




		return 1;
	}