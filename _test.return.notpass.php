<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php
	set_time_limit(600);
	define("HOSTNAME","103.233.192.68") ;
	define("DBNAME","coop_spktcsys");
	define("USERNAME","admin_spktcsys");
	define("PASSWORD",'Qzij45#0');

	$date_s = '2018-11-01';
	$date_e = '2018-11-30';
	$return_type = 2;

	$mysqli = new mysqli( HOSTNAME , USERNAME , PASSWORD );
	$mysqli->select_db(DBNAME);
	$mysqli->query("SET NAMES utf8");
	$sql = "SELECT DISTINCT tb1.member_id, tb1.loan_id
					FROM coop_finance_month_detail tb1
					INNER JOIN coop_finance_month_profile tb2 ON tb1.profile_id = tb2.profile_id
					WHERE tb2.profile_year = 2561
						AND tb2.profile_month = 11
					AND tb1.run_status = 1
					AND tb1.loan_id IS NOT NULL
					ORDER BY tb1.member_id;";
	$rs = $mysqli->query($sql);
	$filter = [];
	while(( $row = $rs->fetch_assoc() )) {
		$filter[] = sprintf('%06d#%s', $row['member_id'], $row['loan_id']);
	}
	//var_dump($filter);

	$sql = "SELECT *
												FROM (
												SELECT DISTINCT tb2.member_id, tb2.receipt_id, tb2.loan_id, tb5.loan_type,
												SUM(tb2.principal_payment) principal_payment, SUM(tb2.interest) interest
												, (
														SELECT interest_rate
														FROM coop_term_of_loan
														WHERE start_date <= '{$date_s}'
														AND type_id = tb5.loan_type
														ORDER BY start_date DESC
														LIMIT 1
													) interest_rate
												, SUM(tb2.total_amount) total_amount
												, tb2.payment_date
												, DAYOFYEAR(DATE_FORMAT('{$date_s}', '%Y-12-31')) num_day_of_year
												, DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) num_datediff
												, CONCAT(tb3.firstname_th, ' ', tb3.lastname_th) member_name
												, tb4.account_id
												, tb5.contract_number
												, ROUND(
																SUM(tb2.principal_payment) *
																(
																	(
																		SELECT interest_rate
																		FROM coop_term_of_loan
																		WHERE start_date <= '{$date_s}'
																		AND type_id = tb5.loan_type
																		ORDER BY start_date DESC
																		LIMIT 1
																	)
																	/
																	100
																) *
																CAST(
																		DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) /
																		DAYOFYEAR(DATE_FORMAT('{$date_s}', '%Y-12-31'))
																AS DOUBLE )
												) return_interest
												, tb6.ret_id
												, tb6.return_time
												, YEAR(tb2.payment_date) payment_date_year
												, MONTH(tb2.payment_date) payment_date_month
												FROM coop_receipt AS tb1
												INNER JOIN coop_finance_transaction AS tb2 ON tb1.receipt_id = tb2.receipt_id
												INNER JOIN coop_mem_apply tb3 ON tb1.member_id = tb3.member_id
												LEFT OUTER JOIN ( SELECT * FROM coop_maco_account WHERE type_id = '2' AND account_status = '0' ) tb4 ON tb1.member_id = tb4.mem_id
												INNER JOIN coop_loan tb5 ON tb2.loan_id = tb5.id
												LEFT OUTER JOIN (SELECT * FROM coop_process_return WHERE return_type = {$return_type}) tb6 ON tb2.member_id = tb6.member_id
																AND tb2.loan_id = tb6.loan_id
																AND MONTH(tb2.payment_date) = tb6.return_month
																AND YEAR(tb2.payment_date) = tb6.return_year
												WHERE tb2.payment_date BETWEEN '{$date_s}' AND '{$date_e}'
																AND DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) > 0
																AND ( tb2.receipt_id NOT LIKE '%C%' OR tb2.receipt_id NOT LIKE '%c%' )

																AND tb1.finance_month_profile_id IS NULL
																AND ( tb6.ret_id IS NOT NULL OR (tb6.ret_id IS NOT NULL AND tb6.account_id = '' ) )
												GROUP BY tb2.member_id, tb2.receipt_id, tb5.contract_number
												ORDER BY tb2.member_id
												) tb
												WHERE return_interest > 0
												ORDER BY member_id";

	$rs = $mysqli->query($sql);
	echo "member_no : เลขที่สัญญา";
	while(( $row = $rs->fetch_assoc() )) {
		$chk = sprintf('%06d#%s', $row['member_id'], $row['loan_id']);
		//echo $chk.'<br />';
		if( !in_array( $chk , $filter) ) {
			echo "<div>{$row['member_id']} | {$row['loan_id']}</div>";
		}
	}
