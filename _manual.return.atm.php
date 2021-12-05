<?php
	define("HOSTNAME","localhost") ;
	define("DBNAME","spktsys_com");
	define("USERNAME","spktsys_com");
	define("PASSWORD",'x4zOmINFa');

	$mysqli = new mysqli( HOSTNAME , USERNAME , PASSWORD );
	$mysqli->select_db(DBNAME);
	$mysqli->query("SET NAMES utf8");

	$ret_type = 2;
	$data_return = [];
	/*
	$data_return[] = ['member_id'=>'008819', 'account_id'=>'00121025312','loan_id'=>'2064','principal'=>349,'interest'=>0, 'return_amount' =>349 ];
	$data_return[] = ['member_id'=>'008819', 'account_id'=>'00121025312','loan_id'=>'2065','principal'=>6543,'interest'=>0, 'return_amount' =>6543 ];
	$data_return[] = ['member_id'=>'011485', 'account_id'=>'00121047794','loan_id'=>'3621','principal'=>2500,'interest'=>0, 'return_amount' =>2500 ];
	$data_return[] = ['member_id'=>'010632', 'account_id'=>'00121031310','loan_id'=>'3842','principal'=>2362,'interest'=>0, 'return_amount' =>2362 ];
	$data_return[] = ['member_id'=>'013498', 'account_id'=>'00121067845','loan_id'=>'6570','principal'=>2500,'interest'=>0, 'return_amount' =>2500 ];
	$data_return[] = ['member_id'=>'013508', 'account_id'=>'00121067947','loan_id'=>'6574','principal'=>600,'interest'=>0, 'return_amount' =>600 ];
	$data_return[] = ['member_id'=>'014297', 'account_id'=>'00121075787','loan_id'=>'7583','principal'=>689,'interest'=>0, 'return_amount' =>689 ];
	$data_return[] = ['member_id'=>'006490', 'account_id'=>'00121033152','loan_id'=>'7674','principal'=>500,'interest'=>0, 'return_amount' =>500 ];
	$data_return[] = ['member_id'=>'013798', 'account_id'=>'00121070760','loan_id'=>'7805','principal'=>986,'interest'=>0, 'return_amount' =>986 ];
	$data_return[] = ['member_id'=>'014550', 'account_id'=>'00121078315','loan_id'=>'8113','principal'=>343,'interest'=>0, 'return_amount' =>343 ];
	$data_return[] = ['member_id'=>'014549', 'account_id'=>'00121078304','loan_id'=>'8112','principal'=>2200,'interest'=>0, 'return_amount' =>2200 ];
	*/
	$data_return[] = ['member_id'=>'007494', 'account_id'=>'00121002633','loan_id'=>'740','principal'=>6000,'interest'=>645, 'return_amount' =>6645 ];
	$data_return[] = ['member_id'=>'008178', 'account_id'=>'00121028291','loan_id'=>'1313','principal'=>1700,'interest'=>186, 'return_amount' =>1886 ];
	$data_return[] = ['member_id'=>'009239', 'account_id'=>'00121015036','loan_id'=>'416','principal'=>5000,'interest'=>580, 'return_amount' =>5580 ];
	$data_return[] = ['member_id'=>'010330', 'account_id'=>'00121046953','loan_id'=>'934','principal'=>2400,'interest'=>294, 'return_amount' =>2694 ];
	$data_return[] = ['member_id'=>'010776', 'account_id'=>'00121034937','loan_id'=>'326','principal'=>1500,'interest'=>100, 'return_amount' =>1600 ];
	$data_return[] = ['member_id'=>'012407', 'account_id'=>'00121056933','loan_id'=>'872','principal'=>1500,'interest'=>124, 'return_amount' =>1624 ];
	$data_return[] = ['member_id'=>'012562', 'account_id'=>'00121058480','loan_id'=>'824','principal'=>3900,'interest'=>477, 'return_amount' =>4377 ];
	$data_return[] = ['member_id'=>'014167', 'account_id'=>'00121074479','loan_id'=>'1235','principal'=>4400,'interest'=>531, 'return_amount' =>4931 ];
	$data_return[] = ['member_id'=>'014253', 'account_id'=>'00121075334','loan_id'=>'158','principal'=>6300,'interest'=>370, 'return_amount' =>6670 ];
	$data_return[] = ['member_id'=>'014438', 'account_id'=>'00121077198','loan_id'=>'155','principal'=>3900,'interest'=>407, 'return_amount' =>4307 ];
	$data_return[] = ['member_id'=>'014648', 'account_id'=>'00121079214','loan_id'=>'328','principal'=>2000,'interest'=>119, 'return_amount' =>2119 ];
	$data_return[] = ['member_id'=>'015900', 'account_id'=>'00121091652','loan_id'=>'1232','principal'=>3400,'interest'=>0, 'return_amount' =>3400 ];
	$data_return[] = ['member_id'=>'012443', 'account_id'=>'00121057296','loan_id'=>'986','principal'=>3583,'interest'=>462, 'return_amount' =>4045 ];

	/*
	$data_return[] = ['member_id'=>'016677', 'account_id'=>'00121099478','loan_id'=>'4154','principal'=>5400,'interest'=>26, 'return_amount' => 5426];
	$data_return[] = ['member_id'=>'010799', 'account_id'=>'00121034971','loan_id'=>'7382','principal'=>1500,'interest'=>784, 'return_amount' => 2284];
	$data_return[] = ['member_id'=>'008732', 'account_id'=>'00121033696','loan_id'=>'8378','principal'=>1400,'interest'=>7, 'return_amount' => 1407];
	$data_return[] = ['member_id'=>'016024', 'account_id'=>'00121092904','loan_id'=>'7635','principal'=>800,'interest'=>4, 'return_amount' => 804];
	*/
	/*
$data_return[] = ['member_id'=>'009470', 'account_id'=>'00121008742','loan_id'=>'6863','principal'=>0,'interest'=>0, 'return_amount' => 144];
$data_return[] = ['member_id'=>'009751', 'account_id'=>'00121008855','loan_id'=>'6872','principal'=>0,'interest'=>0, 'return_amount' => 144];
$data_return[] = ['member_id'=>'010119', 'account_id'=>'00121008968','loan_id'=>'6877','principal'=>0,'interest'=>0, 'return_amount' => 144];
$data_return[] = ['member_id'=>'011470', 'account_id'=>'00121047749','loan_id'=>'6902','principal'=>0,'interest'=>0, 'return_amount' => 144];
$data_return[] = ['member_id'=>'012112', 'account_id'=>'00121054066','loan_id'=>'6907','principal'=>0,'interest'=>0, 'return_amount' => 144];
*/
	/*
$data_return[] = ['member_id'=>'004902', 'account_id'=>'00121004431','loan_id'=>'699','principal'=>2800,'interest'=>600];
$data_return[] = ['member_id'=>'004921', 'account_id'=>'00121030706','loan_id'=>'259','principal'=>3000,'interest'=>507];
$data_return[] = ['member_id'=>'005490', 'account_id'=>'00121030411','loan_id'=>'546','principal'=>4000,'interest'=>963];
$data_return[] = ['member_id'=>'006550', 'account_id'=>'00121004544','loan_id'=>'259','principal'=>3200,'interest'=>606];
$data_return[] = ['member_id'=>'008813', 'account_id'=>'00121041447','loan_id'=>'1290','principal'=>500,'interest'=>1206];
$data_return[] = ['member_id'=>'008922', 'account_id'=>'00121011430','loan_id'=>'978','principal'=>5200,'interest'=>1037];
$data_return[] = ['member_id'=>'008944', 'account_id'=>'00121029076','loan_id'=>'1290','principal'=>1900,'interest'=>75];
$data_return[] = ['member_id'=>'010109', 'account_id'=>'00121012806','loan_id'=>'978','principal'=>6300,'interest'=>788];
$data_return[] = ['member_id'=>'010638', 'account_id'=>'00121030171','loan_id'=>'546','principal'=>3000,'interest'=>1072];
$data_return[] = ['member_id'=>'011503', 'account_id'=>'00121047998','loan_id'=>'477','principal'=>6000,'interest'=>1430];
$data_return[] = ['member_id'=>'011563', 'account_id'=>'00121048637','loan_id'=>'1290','principal'=>5200,'interest'=>1099];
$data_return[] = ['member_id'=>'011883', 'account_id'=>'00121051790','loan_id'=>'978','principal'=>3000,'interest'=>683];
$data_return[] = ['member_id'=>'012148', 'account_id'=>'00121054395','loan_id'=>'606','principal'=>1500,'interest'=>302];
$data_return[] = ['member_id'=>'012330', 'account_id'=>'00121056160','loan_id'=>'979','principal'=>5500,'interest'=>1195];
$data_return[] = ['member_id'=>'012414', 'account_id'=>'00121057003','loan_id'=>'259','principal'=>4400,'interest'=>1032];
$data_return[] = ['member_id'=>'014313', 'account_id'=>'00121075947','loan_id'=>'546','principal'=>3200,'interest'=>715];
$data_return[] = ['member_id'=>'014850', 'account_id'=>'00121081207','loan_id'=>'978','principal'=>2100,'interest'=>3];
$data_return[] = ['member_id'=>'014861', 'account_id'=>'00121081321','loan_id'=>'978','principal'=>1700,'interest'=>257];
$data_return[] = ['member_id'=>'015479', 'account_id'=>'00121087496','loan_id'=>'514','principal'=>3400,'interest'=>801];
$data_return[] = ['member_id'=>'015502', 'account_id'=>'00121087689','loan_id'=>'546','principal'=>3400,'interest'=>430];
$data_return[] = ['member_id'=>'015900', 'account_id'=>'00121091652','loan_id'=>'1290','principal'=>3200,'interest'=>702];
$data_return[] = ['member_id'=>'016482', 'account_id'=>'00121098295','loan_id'=>'259','principal'=>1200,'interest'=>28];
$data_return[] = ['member_id'=>'016689', 'account_id'=>'00121099581','loan_id'=>'1290','principal'=>2500,'interest'=>574];
$data_return[] = ['member_id'=>'016923', 'account_id'=>'00121101791','loan_id'=>'978','principal'=>2500,'interest'=>597];
exit();
*/

$row_num = 0;
foreach( $data_return as $key => $row) {
$row_num++;
//echo "<div>{$row_num}</div>";
$return_amount = $row['return_amount'];
					$sql = "SELECT transaction_balance
									FROM coop_account_transaction
									WHERE account_id = '{$row['account_id']}'
									ORDER BY transaction_time DESC, transaction_id DESC
									LIMIT 1";
					$rs_balance = $mysqli->query($sql);
					$row_balance = $rs_balance->fetch_assoc();
					$transaction_deposit = $return_amount;
					$transaction_balance = $return_amount + $row_balance['transaction_balance'];

					$sql = "INSERT INTO coop_account_transaction(transaction_time, transaction_list, transaction_withdrawal, transaction_deposit, transaction_balance, account_id, user_id)
									VALUES(NOW(), 'REVD', 0, {$transaction_deposit}, {$transaction_balance}, '{$row['account_id']}', 'm_return');";
					echo "<div>{$sql}</div>";


					//คืนเงินหักกลบ
					/*
					$sql = "INSERT INTO coop_process_return(member_id, loan_id, return_type, account_id, return_principal, return_interest, return_amount, return_year, return_month, return_time)
									VALUES('{$row['member_id']}', '{$row['loan_id']}', 2, '{$row['account_id']}', {$row['principal']}, {$row['interest']}, {$return_amount}, 2018, 12, NOW());";
					echo "<div>{$sql}</div>";
					*/


					// ATM

					$sql = "INSERT INTO coop_process_return(member_id, loan_atm_id, return_type, account_id, return_principal, return_interest, return_amount, return_year, return_month, return_time)
									VALUES('{$row['member_id']}', '{$row['loan_id']}', 3, '{$row['account_id']}', {$row['principal']}, {$row['interest']}, {$return_amount}, 2019, 01, NOW());";
					echo "<div>{$sql}</div>";



//echo '<hr />';
}