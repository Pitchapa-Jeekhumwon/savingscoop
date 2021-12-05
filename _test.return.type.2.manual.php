<?php
  //include "{$_SERVER["DOCUMENT_ROOT"]}/application/config/database.php";
  $db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'spktsys_com',
    'password' => 'x4zOmINFa',
    'database' => 'spktsys_com'
  );

	$mysqli = new mysqli( $db['default']['hostname'] , $db['default']['username'] , $db['default']['password'] );
	$mysqli->select_db($db['default']['database']);
  $mysqli->query("SET NAMES utf8");

  $date_s = '2019-01-01';
  $date_e = '2019-01-31';

  $tmp = cal_process_return_type_2($mysqli, $date_s, $date_e);
  $month_process = explode('-', $date_s)[1];
  $year_process = explode('-', $date_s)[0];
  $data_return = [];
  $sql = "SELECT *
  FROM coop_process_return
  WHERE return_type = 2
    AND return_year = {$year_process}
    AND return_month = {$month_process}";
  $rs_return = $mysqli->query($sql);
  while(( $row_return = $rs_return->fetch_assoc() )) {
    $data_return["{$row_return['member_id']}#{$row_return['loan_id']}"] = $row_return;
  }
  $no = 1;
  foreach( $tmp['return'] as $key => $row ) {
    $row['payment_date_year'] = $year_process;
    $row['payment_date_month'] = $month_process;
    if( $data_return["{$row['member_id']}#{$row['loan_id']}"]['return_amount'] != $row['return_amount'] ) {
      $return = $data_return["{$row['member_id']}#{$row['loan_id']}"]['return_amount'];
      echo "<div>{$no} - {$row['member_id']}#{$row['loan_id']} ||| {$return}|{$row['return_amount']}</div>";
      $no++;
    }
    /*
    $sql = "SELECT ret_id
            FROM coop_process_return
            WHERE return_type = 2
              AND member_id = '{$row['member_id']}'
              AND loan_id = '{$row['loan_id']}'
              AND return_year = {$row['payment_date_year']}
              AND return_month = {$row['payment_date_month']}
              AND receipt_id = '{$row['receipt_id']}'
              ";
    $rs_chk = $mysqli->query($sql);
    echo "<div>{$sql}</div>";
    if( $rs_chk->num_rows == 0 && $row['account_id'] && $row['return_amount'] ) {
      $sql = "SELECT transaction_balance
              FROM coop_account_transaction
              WHERE account_id = '{$row['account_id']}'
              ORDER BY transaction_time DESC, transaction_id DESC
              LIMIT 1";
      $rs_balance = $mysqli->query($sql);
      $row_balance = $rs_balance->fetch_assoc();
      $transaction_deposit = $row['return_amount'];
      $transaction_balance = $row['return_amount'] + $row_balance['transaction_balance'];
      $receipt_id = $row['receipt_id'];
      $bill_id = sprintf('R%s%s%05d', $year, $month, $_bill_id++);

      $sql = "INSERT INTO coop_account_transaction(transaction_time, transaction_list, transaction_withdrawal, transaction_deposit, transaction_balance, account_id, user_id)
              VALUES(NOW(), 'REVD', 0, {$transaction_deposit}, {$transaction_balance}, '{$row['account_id']}', 'process_interest')";
      echo "<div>{$sql}</div>";

      $sql = "INSERT INTO coop_process_return(member_id, loan_id, return_type, account_id, receipt_id, bill_id, return_amount, return_year, return_month, return_time)
              VALUES('{$row['member_id']}', '{$row['loan_id']}', 2, '{$row['account_id']}', '{$receipt_id}', '{$bill_id}', {$row['return_amount']}, {$row['payment_date_year']}, {$row['payment_date_month']}, NOW())";
      echo "<div>{$sql}</div><hr />";
    }
    */
  }

  function cal_process_return_type_2($mysqli, $date_s, $date_e) {
    $month_process = explode('-', $date_s)[1];
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
            AND tb2.member_id = '014103'
          ORDER BY tb2.member_id, tb2.loan_id, tb2.payment_date, tb2.receipt_id";
    $rs = $mysqli->query($sql);
    $data = [];
    //echo $sql;
    //foreach ($rs->result_array() as $row) {
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

      $sql = "SELECT *
              FROM coop_receipt
              WHERE receipt_status = 2
                AND receipt_id = '{$row['receipt_id']}'";
      $rs_chk_error = $mysqli->query($sql);
      if( $rs_chk->num_rows > 0 && !$rs_chk_error->num_rows ) {

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
        echo "<div>วันที่ {$row['payment_date']} ยอด {$row['principal_payment']} : คืน = {$return}</div>";
        $return_real = round($return);
        $sql = "SELECT *
                FROM coop_process_return
                WHERE return_year = {$year_process}
                  AND return_month = {$month_process}
                  AND return_type = 2
                  AND member_id = '{$row['member_id']}'
                  AND loan_id = '{$row['loan_id']}'";
        $rs_return = $mysqli->query($sql);
        $row_return = $rs_return->fetch_assoc();
        if( strpos($row_chk['receipt_id'], 'B') ) {
          $data['return']["{$row['member_id']}#{$row['loan_id']}"] = [
            'member_id' => $row['member_id'],
            'loan_id' => $row['loan_id'],
            'interest_rate' => $interest_rate,
            'receipt_id' => $row_chk['receipt_id'],
            'account_id' => $row['account_id'],
            'return_amount' => $return_real + $data['return']["{$row['member_id']}#{$row['loan_id']}"]['return_amount'],
            'ret_id' => $row_return['ret_id'],
            'return_time' => $row_return['return_time'],
            'is_return' => $rs_return->num_rows ? 1 : 0
          ];
        } else {
          $data['no_return']["{$row['member_id']}#{$row['loan_id']}"] = [
            'member_id' => $row['member_id'],
            'loan_id' => $row['loan_id'],
            'interest_rate' => $row['interest_rate'],
            'receipt_id' => $row_chk['receipt_id'],
            'account_id' => $row['account_id'],
            'return_amount' => $return_real + $data['return']["{$row['member_id']}#{$row['loan_id']}"]['return_amount'],
            'ret_id' => '',
            'return_time' => '',
            'is_return' => $rs_return->num_rows ? 1 : 0
          ];
        }
      }
    }
    return $data;
  }