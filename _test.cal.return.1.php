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
	$month_process = explode('-', $date_s)[1];
	$days_of_month = date_format(date_create($date_s), 't');
  $days_of_year = date_format(date_create("{$year_process}-12-31"), 'z') + 1 ;

  $interest_rate = [];
  $sql = "SELECT type_id, interest_rate
          FROM coop_term_of_loan
          WHERE start_date <= '{$date_s}'
          ORDER BY type_id, start_date DESC
          ";
  $rs = $mysqli->query($sql);
  while(( $row = $rs->fetch_assoc() )) {
    if( !isset($interest_rate[$row['type_id']]) ) $interest_rate[$row['type_id']] = $row['interest_rate'];
  }
  unset($sql, $rs, $row);


  $data_return = [];
  $sql = "SELECT *
          FROM coop_process_return
          WHERE return_year = {$year_process}
            AND return_month = {$month_process}
            AND return_type = 1";
            /*
            AND member_id = '{$row['member_id']}'
            AND loan_id = '{$row['loan_id']}'
            */
  $rs = $mysqli->query($sql);
  while(( $row = $rs->fetch_assoc() )) {
    $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"] = $row['ret_id'];
  }

  $sql = "SELECT DISTINCT tb2.member_id, tb2.receipt_id, tb2.loan_id,
          SUM(tb2.principal_payment) principal_payment, SUM(tb2.interest) interest
          , SUM(tb2.total_amount) total_amount
          , SUM(tb2.loan_amount_balance) loan_amount_balance
          , tb2.payment_date
          , DAYOFYEAR(DATE_FORMAT(NOW(), '%Y-12-31')) num_day_of_year
          , DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) num_datediff
          , CONCAT(tb3.firstname_th, ' ', tb3.lastname_th) member_name
          , tb4.account_id
          , tb5.loan_type
          , tb5.contract_number
          , YEAR(tb2.payment_date) payment_date_year
          , MONTH(tb2.payment_date) payment_date_month
          FROM coop_receipt AS tb1
          INNER JOIN coop_finance_transaction AS tb2 ON tb1.receipt_id = tb2.receipt_id
          INNER JOIN coop_mem_apply tb3 ON tb1.member_id = tb3.member_id
          LEFT OUTER JOIN (
            SELECT mem_id, account_id
            FROM coop_maco_account
            WHERE type_id = '2'
              AND account_status = '0'
          ) tb4 ON tb1.member_id = tb4.mem_id
          INNER JOIN coop_loan tb5 ON tb2.loan_id = tb5.id
          WHERE tb2.payment_date BETWEEN '{$date_s}' AND '{$date_e}'
            AND (
                ( tb2.receipt_id LIKE '%C%' OR tb2.receipt_id LIKE '%c%' )
                OR
                ( tb2.receipt_id LIKE '%B%' OR tb2.receipt_id LIKE '%b%' )
            )
            AND tb1.finance_month_profile_id IS NOT NULL
          GROUP BY tb2.member_id, tb2.receipt_id, tb5.contract_number
          ORDER BY tb2.member_id, tb2.loan_id, tb2.payment_date";
  // AND DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) > 0
  //
	$rs = $mysqli->query($sql);
	$data = [];
	$data_false = [];
	$num = [
		'no_cal' => 0,
		'cal' => 0
  ];

	while(( $row = $rs->fetch_assoc() )) {
      $return_real = 0;
      if(strpos( strtolower($row['receipt_id']), 'b') !== false) { // ผ่านรายการ

          if( $row['loan_amount_balance'] >= 0 ) {
            // คืนเงิน
            $date_payment = DateTime::createFromFormat('Y-m-d', $row['payment_date']);
            $date_of_month = DateTime::createFromFormat('Y-m-d', date('Y-m-t', strtotime($date_s)));
            $date_diff = $date_payment->diff($date_of_month);
            $return = $row['principal_payment'] * ( $interest_rate[$row['loan_type']] / 100 ) * ( $date_diff->format('%a') / $days_of_year ) ;
            $return_real = round($return);
            if( $return_real > 0 ) {
              $data['return'][] = [
                'member_id' => $row['member_id'],
                'loan_id' => $row['loan_id'],
                'contract_number' => $row['contract_number'],
                'receipt_id' => $row['receipt_id'],
                'account_id' => $row['account_id'],
                'return_principal' => 0,
                'return_interest' => $return_real,
                'surcharge' => 0,
                'is_return' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? 1 : 0,
                'interest_rate' => $interest_rate[$row['loan_type']],
                'principal_payment' => $row['principal_payment'],
                'interest' => $row['interest'],
                'loan_amount_balance' => $row['loan_amount_balance']
              ];
            }
          } else {
            // ยอดคงเหลือติดลบ ไม่คืนเงิน
            $data['no_return'][] = [
              'member_id' => $row['member_id'],
              'loan_id' => $row['loan_id'],
              'contract_number' => $row['contract_number'],
              'receipt_id' => $row['receipt_id'],
              'account_id' => $row['account_id'],
              'return_principal' => $row['principal_payment'],
              'return_interest' => $row['interest'],
              'surcharge' => 0,
              'is_return' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? 1 : 0,
              'interest_rate' => $interest_rate[$row['loan_type']],
              'principal_payment' => $row['principal_payment'],
              'interest' => $row['interest'],
              'loan_amount_balance' => $row['loan_amount_balance']
            ];
          }



      } else {
        // จ่ายล่าช้า เก็บเพิ่ม
        $surcharge = $row['principal_payment'] * ( $interest_rate[$row['loan_type']] / 100 ) * ( explode('-', $row['payment_date'])[2] / $days_of_year ) ;
        $surcharge_real = round($surcharge);

        if( ($row['principal_payment'] + $row['loan_amount_balance']) == 0 && $row['loan_amount_balance'] < 0 && $surcharge_real ) {
          $data['no_return'][] = [
            'member_id' => $row['member_id'],
            'loan_id' => $row['loan_id'],
            'contract_number' => $row['contract_number'],
            'receipt_id' => $row['receipt_id'],
            'account_id' => $row['account_id'],
            'return_principal' => $row['principal_payment'],
            'return_interest' => $row['interest'],
            'surcharge' => 0,
            'is_return' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? 1 : 0,
            'interest_rate' => $interest_rate[$row['loan_type']],
            'principal_payment' => $row['principal_payment'],
            'interest' => $row['interest'],
            'loan_amount_balance' => $row['loan_amount_balance']
          ];
        } elseif( $row['loan_amount_balance'] == 0 && $surcharge_real ) {
            $data['surcharge'][] = [
              'member_id' => $row['member_id'],
              'loan_id' => $row['loan_id'],
              'contract_number' => $row['contract_number'],
              'receipt_id' => $row['receipt_id'],
              'account_id' => $row['account_id'],
              'return_principal' => 0,
              'return_interest' => 0,
              'surcharge' => $surcharge_real,
              'is_return' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? 1 : 0,
              'interest_rate' => $interest_rate[$row['loan_type']],
              'principal_payment' => $row['principal_payment'],
              'interest' => $row['interest'],
              'loan_amount_balance' => $row['loan_amount_balance']
            ];
        } elseif( $row['loan_amount_balance'] < 0 ) {
          $return_principal = $row['principal_payment'] + $row['loan_amount_balance'];
          $surcharge = $return_principal * ( $interest_rate[$row['loan_type']] / 100 ) * ( explode('-', $row['payment_date'])[2] / $days_of_year ) ;
          $surcharge_real = round($surcharge);
          if( $surcharge_real > 0 ) {
            $data['surcharge'][] = [
              'member_id' => $row['member_id'],
              'loan_id' => $row['loan_id'],
              'contract_number' => $row['contract_number'],
              'receipt_id' => $row['receipt_id'],
              'account_id' => $row['account_id'],
              'return_principal' => $return_principal,
              'return_interest' => $row['interest'],
              'surcharge' => $surcharge_real,
              'is_return' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? 1 : 0,
              'interest_rate' => $interest_rate[$row['loan_type']],
              'principal_payment' => $row['principal_payment'],
              'interest' => $row['interest'],
              'loan_amount_balance' => $row['loan_amount_balance']
            ];
          }
        }


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
  }

	echo '
	<h3>ผ่านรายการ - คืนเงินเคสปกติ</h3>
		<table border="1" cellpadding="3"	cellspacing="0">
		<thead>
			<tr>
				<th>#</th>
				<th>member_id</th>
				<th>loan_id</th>
				<th>เลขที่ใบเสร็จ</th>
				<th>เลขที่บัญชร</th>
				<th>คืนต้น</th>
				<th>คืนดอก</th>
				<th>เก็บเพิ่ม</th>
				<th>สถานะ</th>
				<th>เงินต้น</th>
				<th>ดอกเบี้ย</th>
				<th>ยอดคงเหลือ</th>
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
			number_format($row['return_principal'], 0),
			number_format($row['return_interest'], 0),
			number_format($row['surcharge'], 0),
			($row['is_return']) ? 'คืนแล้ว' : 'ยังไม่คืน',
			$row['principal_payment'],
			$row['interest'],
			$row['loan_amount_balance']
			);
	}
	echo '
		</tbody>
	</table>
  ';

  $sum = 0;
  $count = 0;
  foreach( $data['return'] as $key => $row) {
    $count++;
    $sum += $row['return_interest'];
  }
  echo sprintf('<div>sum => %s</div><div>count => %s</div>', number_format($sum, 0), $count);
	echo '
  <h3 style="display: none;">ผ่านรายการ - ไม่คืนเงินเพราะยอดคงเหลือต่ำกว่า 0</h3>
  <h3>ผ่านรายการ - คืนหมดเพราะยอดคงเหลือต่ำกว่า 0</h3>
	<table border="1" cellpadding="3"	cellspacing="0">
		<thead>
			<tr>
				<th>#</th>
				<th>member_id</th>
				<th>loan_id</th>
				<th>เลขที่ใบเสร็จ</th>
				<th>เลขที่บัญชร</th>
				<th>คืนต้น</th>
				<th>คืนดอก</th>
				<th>เก็บเพิ่ม</th>
				<th>สถานะ</th>
				<th>เงินต้น</th>
				<th>ดอกเบี้ย</th>
				<th>ยอดคงเหลือ</th>
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
			number_format($row['return_principal'], 0),
			number_format($row['return_interest'], 0),
			number_format($row['surcharge'], 0),
			($row['is_return']) ? 'คืนแล้ว' : 'ยังไม่คืน',
			$row['principal_payment'],
			$row['interest'],
			$row['loan_amount_balance']
			);
	}
	echo '
		</tbody>
	</table>
  ';
  echo '
	<h3>ผ่านรายการ - จ่ายล่าช้าเก็บเพิ่ม</h3>
	<table border="1" cellpadding="3"	cellspacing="0">
		<thead>
			<tr>
				<th>#</th>
				<th>member_id</th>
				<th>loan_id</th>
				<th>เลขที่ใบเสร็จ</th>
				<th>เลขที่บัญชร</th>
				<th>คืนต้น</th>
				<th>คืนดอก</th>
				<th>เก็บเพิ่ม</th>
				<th>สถานะ</th>
				<th>เงินต้น</th>
				<th>ดอกเบี้ย</th>
				<th>ยอดคงเหลือ</th>
			</tr>
		</thead>
		<tbody>
	';
	$no = 1;
	foreach( $data['surcharge'] as $key => $row) {
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
      number_format($row['return_principal'], 0),
			number_format($row['return_interest'], 0),
			number_format($row['surcharge'], 0),
			($row['is_return']) ? 'คืนแล้ว' : 'ยังไม่คืน',
			$row['principal_payment'],
			$row['interest'],
			$row['loan_amount_balance']
			);
	}
	echo '
		</tbody>
	</table>
	';
	echo sprintf('ทั้งหมด %s รายการ<br />', count($data['return']));
	echo sprintf('มีการเรียกเก็บ %s รายการ<br />', count($data['return']));