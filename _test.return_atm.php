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

  $date_s = '2018-10-01';
  $date_e = '2018-10-31';

  function check_month_payment($mysqli, $member_id, $date_s, $date_e) {
    $sql = "SELECT *
    FROM (

    SELECT
        t7.member_id,
        DATE(t1.transaction_datetime) transaction_date,
       SUM(
        IF (
         t2.loan_amount <> '',
         `t2`.`loan_amount`,
         t3.principal_payment
        )
       ) AS principal,
       SUM(t3.interest) AS interest,
       SUM(t3.total_amount) AS total_amount,

      IF (
       ! ISNULL(
        (
         SELECT
          ret_id
         FROM
          coop_process_return
         WHERE
          coop_process_return.return_month = t6.month_receipt
         AND coop_process_return.return_year = (t6.year_receipt - 543)
         AND coop_process_return.loan_atm_id = t1.loan_atm_id
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
      ) AS loan_amount_balance
    FROM `coop_loan_atm_transaction` AS `t1`
    LEFT JOIN `coop_loan_atm_detail` AS `t2` ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id`
    AND `t1`.`transaction_datetime` = `t2`.`loan_date`
    LEFT JOIN `coop_finance_transaction` AS `t3` ON `t1`.`receipt_id` = `t3`.`receipt_id`
    AND `t1`.`loan_atm_id` = `t3`.`loan_atm_id`
    LEFT JOIN `coop_receipt` AS `t4` ON `t3`.`receipt_id` = `t4`.`receipt_id`
    LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
    INNER JOIN coop_loan_atm t7 ON t1.loan_atm_id = t7.loan_atm_id
    WHERE DATE(t1.transaction_datetime) BETWEEN '2018-10-01' AND '2018-10-31'
    AND ( t2.loan_description IS NULL AND t4.finance_month_profile_id IS NULL )
     GROUP BY
      `t1`.`transaction_datetime`
    ) tb
    WHERE loan_amount_balance = 0
    AND member_id = {$member_id}
    ORDER BY member_id";
  }

$sql = "SELECT *
FROM (

SELECT
    t7.member_id,
    DATE(t1.transaction_datetime) transaction_date,
   SUM(
    IF (
     t2.loan_amount <> '',
     `t2`.`loan_amount`,
     t3.principal_payment
    )
   ) AS principal,
   SUM(t3.interest) AS interest,
   SUM(t3.total_amount) AS total_amount,

  IF (
   ! ISNULL(
    (
     SELECT
      ret_id
     FROM
      coop_process_return
     WHERE
      coop_process_return.return_month = t6.month_receipt
     AND coop_process_return.return_year = (t6.year_receipt - 543)
     AND coop_process_return.loan_atm_id = t1.loan_atm_id
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
  ) AS loan_amount_balance
FROM `coop_loan_atm_transaction` AS `t1`
LEFT JOIN `coop_loan_atm_detail` AS `t2` ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id`
AND `t1`.`transaction_datetime` = `t2`.`loan_date`
LEFT JOIN `coop_finance_transaction` AS `t3` ON `t1`.`receipt_id` = `t3`.`receipt_id`
AND `t1`.`loan_atm_id` = `t3`.`loan_atm_id`
LEFT JOIN `coop_receipt` AS `t4` ON `t3`.`receipt_id` = `t4`.`receipt_id`
LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
INNER JOIN coop_loan_atm t7 ON t1.loan_atm_id = t7.loan_atm_id
WHERE DATE(t1.transaction_datetime) BETWEEN '{$date_s}' AND '{$date_e}'
AND ( t2.loan_description IS NULL AND t4.finance_month_profile_id IS NULL )
 GROUP BY
  `t1`.`transaction_datetime`
) tb
WHERE loan_amount_balance = 0
AND (principal + interest) <> 0
AND member_id IS NOT NULL
ORDER BY member_id";

$rs = $mysqli->query($sql);
echo '<table cellpadding="3" cellspacing="0" border="1">
        <thead>
          <tr>
            <th>#</th>
            <th>รหัสสมาชิก</th>
            <th>วันที่</th>
            <th>เงินต้น</th>
            <th>ดอกเบี้ย</th>
            <th>ยอดคงเหลือ</th>
          </tr>
        </thead>
        <tbody>';
$no = 1;
while( ( $row = $rs->fetch_assoc()) ) {
  echo sprintf('
  <tr>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
  </tr>',
  $no++,
  $row['member_id'],
  $row['transaction_date'],
  $row['principal'],
  $row['interest'],
  $row['loan_amount_balance']
);
}

echo '</tbody>
  </table>
';