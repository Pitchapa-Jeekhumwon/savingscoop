<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php
	set_time_limit(600);

	define("HOSTNAME","localhost") ;
	define("DBNAME","spktsys_com");
	define("USERNAME","spktsys_com");
	define("PASSWORD",'x4zOmINFa');

	$mysqli = new mysqli( HOSTNAME , USERNAME , PASSWORD );
	$mysqli->select_db(DBNAME);
	$mysqli->query("SET NAMES utf8");

	$date_s = '2019-01-01';
	$date_e = '2019-01-31';
	$year_process = explode('-', $date_s)[0];
	$days_of_month = date_format(date_create($date_s), 't');
	$days_of_year = date_format(date_create("{$year_process}-12-31"), 'z') + 1 ;

	$sql = "SELECT tb2.member_id, tb2.receipt_id, tb2.loan_id
					, tb2.principal_payment
					, tb2.interest
					, tb2.total_amount
					, tb2.payment_date
					, tb3.loan_type
					, tb4.account_id
					FROM coop_receipt AS tb1
					INNER JOIN coop_finance_transaction AS tb2 ON tb1.receipt_id = tb2.receipt_id
					INNER JOIN coop_loan tb3 ON tb2.loan_id = tb3.id
					LEFT OUTER JOIN ( SELECT * FROM coop_maco_account WHERE type_id = '2' AND account_status = '0' ) tb4 ON tb1.member_id = tb4.mem_id
					WHERE tb2.payment_date BETWEEN '{$date_s}' AND '{$date_e}'
						AND DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) > 0
						AND ( tb2.receipt_id NOT LIKE '%C%' OR tb2.receipt_id NOT LIKE '%c%' )
						AND tb1.finance_month_profile_id IS NULL
						AND tb2.loan_id IS NOT NULL
						AND tb4.account_id IS NOT NULL
					ORDER BY tb2.member_id, tb2.loan_id, tb2.payment_date, tb2.receipt_id";
	$rs = $mysqli->query($sql);
	$data = [];
	$data_false = [];
	$num = [
		'no_cal' => 0,
		'cal' => 0
	];
	while(( $row = $rs->fetch_assoc() )) {
		/* เช็คว่ามีการผ่านรายการม้ย */
		$sql = "SELECT tb1.member_id, tb1.loan_id, tb1.receipt_id, tb1.payment_date,
			SUM(tb1.principal_payment) principal_payment,
			SUM(tb1.interest) interest,
			SUM(tb1.total_amount) total_amount
			FROM coop_finance_transaction tb1
			INNER JOIN coop_receipt tb2 ON tb1.receipt_id = tb2.receipt_id
			WHERE ( tb1.receipt_id NOT LIKE '%C%' OR tb1.receipt_id NOT LIKE '%c%' )
				AND tb1.payment_date BETWEEN '{$date_s}' AND '{$date_e}'
				AND tb1.loan_id IS NOT NULL
				AND tb2.finance_month_profile_id IS NOT NULL
				AND ( tb1.member_id = '{$row['member_id']}' AND tb1.loan_id = '{$row['loan_id']}' )
			GROUP BY tb1.member_id, tb1.loan_id, tb1.receipt_id, tb1.payment_date
		";
		$rs_chk = $mysqli->query($sql);
		if( $rs_chk->num_rows > 0 ) {

			$row_chk = $rs_chk->fetch_assoc();
			$date_payment = DateTime::createFromFormat('Y-m-d', $row['payment_date']);
			$date_of_month = DateTime::createFromFormat('Y-m-d', date('Y-m-t', strtotime($date_s)));
			$date_diff = $date_payment->diff($date_of_month);

			$sql = "SELECT interest_rate
							FROM coop_term_of_loan
							WHERE start_date <= '{$date_s}'
								AND type_id = {$row['loan_type']}
							ORDER BY start_date DESC
							LIMIT 1";
			$rs_interest = $mysqli->query($sql);
			$interest_rate = $rs_interest->fetch_assoc()['interest_rate'];
			$return = $row['principal_payment'] * ( $interest_rate / 100 ) * ( $date_diff->format('%a') / $days_of_year ) ;
			$return_real = round($return);
			$sql = "SELECT *
							FROM coop_process_return
							WHERE return_year = 2018
								AND return_month = 11
								AND return_type = 2
								AND member_id = '{$row['member_id']}'
								AND loan_id = '{$row['loan_id']}'";
			$rs_return = $mysqli->query($sql);
			if( strpos($row_chk['receipt_id'], 'B') ) {
				$data['return'][] = [
					'member_id' => $row['member_id'],
					'loan_id' => $row['loan_id'],
					'receipt_id' => $row_chk['receipt_id'],
					'account_id' => $row['account_id'],
					'return_amount' => $return_real,
					'is_return' => $rs_return->num_rows ? 1 : 0
				];
			} else {
				$data['no_return'][] = [
					'member_id' => $row['member_id'],
					'loan_id' => $row['loan_id'],
					'receipt_id' => $row_chk['receipt_id'],
					'account_id' => $row['account_id'],
					'return_amount' => $return_real,
					'is_return' => $rs_return->num_rows ? 1 : 0
				];
			}
			/*
			echo "
				member_id = {$row['member_id']}<br />
				loan_id = {$row['loan_id']}<br />
				payment_date = {$row['payment_date']}<br />
				total_amount = {$row['total_amount']}<br />
				interest_rate = {$interest_rate}<br />
				day of month = {$days_of_month}<br />
				day of year = {$days_of_year}<br />
				return = {$return}<br />
				return_real = {$return_real}<br />
				";
			echo sprintf('date diff = %s%s<br />', $date_diff->format('%R'), $date_diff->format('%a'));
			echo $date_of_month->format('Y-m-d');
			echo '<hr />';
			*/
		} else {
			$num['no_cal']++;
			/* */
			$sql = "SELECT *
							FROM coop_process_return
							WHERE return_year = 2018
								AND return_month = 11
								AND return_type = 2
								AND member_id = '{$row['member_id']}'
								AND loan_id = '{$row['loan_id']}'";
			$rs_return = $mysqli->query($sql);
			$row_return = $rs_return->fetch_assoc();
			$data_false[] = [
				'member_id' => $row['member_id'],
				'loan_id' => $row['loan_id'],
				'receipt_id' => $row['receipt_id'],
				'account_id' => $row['account_id'],
				'return' => $row_return['return_amount']
			];
		}
	}
	echo '
	<h3>ผ่านรายการ - คืนเงิน</h3>
	<table border="1" cellpadding="3"	cellspacing="0">
		<thead>
			<tr>
				<th>#</th>
				<th>member_id</th>
				<th>loan_id</th>
				<th>เลขที่ใบเสร็จ</th>
				<th>เลขที่บัญชร</th>
				<th>เงืนคืน</th>
				<th>สถานะ</th>
			</tr>
		</thead>
		<tbody>
	';
	$no = 1;
	foreach( $data['return'] as $key => $row) {
		echo sprintf('
			<tr>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
			</tr>
			',
			$no++,
			$row['member_id'],
			$row['loan_id'],
			$row['receipt_id'],
			$row['account_id'],
			number_format($row['return_amount'], 0),
			($row['is_return']) ? 'คืนแล้ว' : 'ยังไม่คืน'
			);
	}
	echo '
		</tbody>
	</table>
	';

	echo '
	<h3>ผ่านรายการ - ไม่คืนเงิน</h3>
	<table border="1" cellpadding="3"	cellspacing="0">
		<thead>
			<tr>
				<th>#</th>
				<th>member_id</th>
				<th>loan_id</th>
				<th>เลขที่ใบเสร็จ</th>
				<th>เลขที่บัญชร</th>
				<th>เงืนคืน</th>
				<th>สถานะ</th>
			</tr>
		</thead>
		<tbody>
	';
	$no = 1;
	foreach( $data['no_return'] as $key => $row) {
		echo sprintf('
			<tr>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
			</tr>
			',
			$no++,
			$row['member_id'],
			$row['loan_id'],
			$row['receipt_id'],
			$row['account_id'],
			number_format($row['return_amount'], 0),
			($row['is_return']) ? 'คืนแล้ว' : 'ยังไม่คืน'
			);
	}
	echo '
		</tbody>
	</table>
	';

	echo sprintf('ทั้งหมด %s รายการ<br />', count($data['return']) + $num['no_cal']);
	echo sprintf('มีการเรียกเก็บ %s รายการ<br />', count($data['return']));
	echo sprintf('ไม่มีการเรียกเก็บ %s รายการ<br />', $num['no_cal']);