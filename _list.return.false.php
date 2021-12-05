<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php
	set_time_limit(600);

	define("HOSTNAME","103.233.192.68") ;
	define("DBNAME","coop_spktcsys");
	define("USERNAME","admin_spktcsys");
	define("PASSWORD",'Qzij45#0');

	$mysqli = new mysqli( HOSTNAME , USERNAME , PASSWORD );
	$mysqli->select_db(DBNAME);
	$mysqli->query("SET NAMES utf8");

	$year = 2018;
	$month = 11;
	$data_0 = [];
	$data_1 = [];
	$data_2 = [];
	$num = [
		'has' => 0,
		'total' => 0
	];
	$sql = "SELECT *, DATE(return_time) return_date
					FROM coop_process_return
					WHERE return_year = {$year}
					AND return_month = {$month}
					AND return_type = 2";
	$rs = $mysqli->query($sql);
	while(( $row = $rs->fetch_assoc() )) {
		$num['total']++;
		$member_id = $row['member_id'];
		$loan_id = $row['loan_id'];
		$return_date = $row['return_date'];
		$sql = "SELECT tb1.member_id, tb1.loan_id, tb1.receipt_id, tb1.payment_date,
						SUM(tb1.principal_payment) principal_payment,
						SUM(tb1.interest) interest,
						SUM(tb1.total_amount) total_amount,
						DATEDIFF('{$return_date}', tb1.payment_date) _diff_
						FROM coop_finance_transaction tb1
						INNER JOIN coop_receipt tb2 ON tb1.receipt_id = tb2.receipt_id
						WHERE ( tb1.receipt_id NOT LIKE '%C%' OR tb1.receipt_id NOT LIKE '%c%' )
							AND ( YEAR(tb1.payment_date) = {$year} AND MONTH(tb1.payment_date) = {$month})
							AND tb1.loan_id IS NOT NULL
							AND tb2.finance_month_profile_id IS NOT NULL
							AND ( tb1.member_id = '{$member_id}' AND tb1.loan_id = '{$loan_id}' )
						GROUP BY tb1.member_id, tb1.loan_id, tb1.receipt_id, tb1.payment_date;";
		//echo $sql.'<hr />';
		$rs_chk = $mysqli->query($sql);
		if( $rs_chk->num_rows ) { // มีการชำระรายเดือน
			$row_chk = $rs_chk->fetch_assoc() ;
			if( $row_chk['_diff_'] < 0 ) { // ชำระก่อน รายเดือน
				$num['no']++;
				$data_0[] = [
					'member_id' => $member_id,
					'loan_id' => $loan_id,
					'return_date' => $return_date,
					'receipt_id' => $row_chk['receipt_id'],
					'account_id' => $row['account_id'],
					'return' => $row['return_amount'],
					'payment_date' => $row_chk['payment_date']
				];
			} else { // ชำระพร้อม หรือหลัง รายเดือน
				$data_1[] = [
					'member_id' => $member_id,
					'loan_id' => $loan_id,
					'return_date' => $return_date,
					'receipt_id' => $row_chk['receipt_id'],
					'account_id' => $row['account_id'],
					'return' => $row['return_amount'],
					'payment_date' => $row_chk['payment_date']
				];
			}
		} else { // ยังไม่มีการชำระรายเดือน
			$data_2[] = [
				'member_id' => $member_id,
				'loan_id' => $loan_id,
				'return_date' => $return_date,
				'receipt_id' => $row['receipt_id'],
				'account_id' => $row['account_id'],
				'return' => $row['return_amount']
			];
		}
	}

	echo '<h3>คืนเงินก่อนชำระรายเดือน</h3>';
	echo '
	<table border="1" cellpadding="3"	cellspacing="0">
		<thead>
			<tr>
				<th>#</th>
				<th>member_id</th>
				<th>loan_id</th>
				<th>เลขที่ใบเสร็จ</th>
				<th>เลขที่บัญชร</th>
				<th>คืนวันที่</th>
				<th>เงืนคืน</th>
				<th>วันที่ชำระรายเดือน</th>
			</tr>
		</thead>
		<tbody>
	';
	$no = 1;
	foreach( $data_0 as $key => $row) {
		if( strpos($row['receipt_id'], 'B') === false ) {
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
			</tr>
			',
			$no++,
			$row['member_id'],
			$row['loan_id'],
			$row['receipt_id'],
			$row['account_id'],
			$row['return_date'],
			number_format($row['return'], 0),
			$row['payment_date']
			);
		}

	}
	echo '
		</tbody>
	</table>
	';
	echo '<h3>คืนเงินพร้อม หรือหลังชำระรายเดือน</h3>';
	echo '
	<table border="1" cellpadding="3"	cellspacing="0">
		<thead>
			<tr>
				<th>#</th>
				<th>member_id</th>
				<th>loan_id</th>
				<th>เลขที่ใบเสร็จ</th>
				<th>เลขที่บัญชร</th>
				<th>คืนวันที่</th>
				<th>เงืนคืน</th>
				<th>วันที่ชำระรายเดือน</th>
			</tr>
		</thead>
		<tbody>
	';
	$no = 1;
	foreach( $data_1 as $key => $row) {
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
			</tr>
			',
			$no++,
			$row['member_id'],
			$row['loan_id'],
			$row['receipt_id'],
			$row['account_id'],
			$row['return_date'],
			number_format($row['return'], 0),
			$row['payment_date']
			);
	}
	echo '
		</tbody>
	</table>
	';

	echo '<h3>ไม่มีการชำระรายเดือน</h3>';
	echo '
	<table border="1" cellpadding="3"	cellspacing="0">
		<thead>
			<tr>
				<th>#</th>
				<th>member_id</th>
				<th>loan_id</th>
				<th>เลขที่บัญชร</th>
				<th>คืนวันที่</th>
				<th>เงืนคืน</th>
				<th>วันที่ชำระรายเดือน</th>
			</tr>
		</thead>
		<tbody>
	';
	$no = 1;
	foreach( $data_2 as $key => $row) {
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
			$row['account_id'],
			$row['return_date'],
			number_format($row['return'], 0),
			$row['payment_date']
			);
	}
	echo '
		</tbody>
	</table>
	';
	echo sprintf('ทั้งหมด %s รายการ<br />', $num['total']);