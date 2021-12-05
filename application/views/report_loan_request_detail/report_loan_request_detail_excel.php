<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายชื่อสมาชิกที่ยื่นคำขอกู้.xls");
date_default_timezone_set('Asia/Bangkok');

function getMBStrSplit($string, $split_length = 1){
    mb_internal_encoding('UTF-8');
    mb_regex_encoding('UTF-8');

    $split_length = ($split_length <= 0) ? 1 : $split_length;
    $mb_strlen = mb_strlen($string, 'utf-8');
    $array = array();
    $i = 0;

    while($i < $mb_strlen)
    {
        $array[] = mb_substr($string, $i, $split_length);
        $i = $i+$split_length;
    }

    return $array;
}

function getStrLenTH($string)
{
    $array = getMBStrSplit($string);
    $count = 0;

    foreach($array as $value)
    {
        $ascii = ord(iconv("UTF-8", "TIS-620", $value ));

        if( !( $ascii == 209 ||  ($ascii >= 212 && $ascii <= 218 ) || ($ascii >= 231 && $ascii <= 238 )) )
        {
            $count += 1;
        }
    }
    return $count;
}
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
                .table_title_footer{
                    font-family: AngsanaUPC, MS Sans Serif;
                    font-size: 16px;
                    font-weight: bold;
                    text-align:center;
                }
                .table_title_footer_left{
                    font-family: AngsanaUPC, MS Sans Serif;
                    font-size: 16px;
                    font-weight: bold;
                    text-align:left;
                }
                .table_title_footer_right{
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
                    font-size: 16px;
                    border: thin solid black;
                }
                .table_body_right{
                    font-family: AngsanaUPC, MS Sans Serif;
                    font-size: 16px;
                    border: thin solid black;
                    text-align:right;
                }
			</style>
		</head>
		<body>
			<table class="table table-bordered">
				<tr>
					<th class="table_title" colspan="19"><?php echo @$_SESSION['COOP_NAME'];?></th>
                </tr>
                <tr>
					<th class="table_title" colspan="19"><?php echo "รายชื่อสมาชิกที่ยื่นคำขอกู้ในเดือน".$month_arr[$_GET['approve_month']].' '.($_GET['approve_year']+543);?></th>
				</tr>
                <?php
                if(empty($_GET['loan_name_all'])) {
                    $txts = array();
                    $Line = 0;
                    foreach ($loan_name as $key => $value) {
//                        echo $value['loan_name'];
                        if($key != '0'){
                            $txts[$Line] .= ', ';
                        }
                        if(getStrLenTH($txts[$Line].$value['loan_name']) > 110){
                            $Line++;
                        }
                        $txts[$Line] .= $value['loan_name'];
                    }
                    foreach ($txts as $Line => $txt) { ?>

                    <tr>
                        <th class="table_title" colspan="19"><?php echo $txt;?></th>
                    </tr>
                    <?php }
                }
                ?>
			</table>
			<table class="table table-bordered">
				<thead>
					<tr>
                        <th class="table_header_top" style="vertical-align: middle;" rowspan="2">ลำดับ<br>ที่</th>
                        <th class="table_header_top" style="vertical-align: middle;" rowspan="2">ชื่อ - นามสกุลผู้กู้</th>
                        <th class="table_header_top" style="vertical-align: middle;" rowspan="2">เลขที่สมาชิก</th>
                        <th class="table_header_top" style="vertical-align: middle;" rowspan="2">เงินเดือน/เงินเดือนคงเหลือ</th>
                        <th class="table_header_top" style="vertical-align: middle;" rowspan="2">จำนวนเงินกู้</th>
                        <th class="table_header_top" style="vertical-align: middle;" rowspan="2">ประกัน</th>
                        <th class="table_header_top" style="vertical-align: middle;" rowspan="2">จำนวนงวด</th>
                        <th class="table_header_top" style="vertical-align: middle;" colspan="6">สัญญากู้กับสหกรณ์</th>
                        <th class="table_header_top" style="vertical-align: middle;" rowspan="2">ดอกเบี้ย</th>
                        <th class="table_header_top" style="vertical-align: middle;" rowspan="2">จำนวนเงินกู้ที่ได้รับ</th>
                        <th class="table_header_top" style="vertical-align: middle;" colspan="2">หลักค้ำประกัน</th>
                        <th class="table_header_top" style="vertical-align: middle;" rowspan="2">วัตถุประสงค์การกู้</th>
                        <th class="table_header_top" style="vertical-align: middle;" rowspan="2">หมายเหตุ</th>
					</tr>
                    <tr>
                        <th class="table_header_top" style="vertical-align: middle;">สัญญากู้สามัญคงเหลือ</th>
                        <th class="table_header_top" style="vertical-align: middle;"> สัญญากู้หุ้นค้ำคงเหลือ </th>
                        <th class="table_header_top" style="vertical-align: middle;">สัญญากู้พิเศษคงเหลือ</th>
                        <th class="table_header_top" style="vertical-align: middle;">สัญญากู้ฉุกเฉินคงเหลือ</th>
                        <th class="table_header_top" style="vertical-align: middle;">รวมหนี้รวม</th>
                        <th class="table_header_top" style="vertical-align: middle;">รวมหนี้กู้ครั้งนี้</th>
                        <th class="table_header_top" style="vertical-align: middle;">ผู้กู้มีหุ้น</th>
                        <th class="table_header_top" style="vertical-align: middle;">ชื่อ - นามสกุล (ผู้ค้ำประกัน)</th>
                    </tr>
				</thead>
				<tbody>
                <?php
                $i = 0;
                foreach ($datas as $pase => $data) {
                    foreach ($data as $key => $value) {
                        $count_loan++;
                        $normal_person = @$value['loan_order']['normal']['200']['loan_amount_balance'];
                        $normal_share = @$value['loan_order']['normal']['201']['loan_amount_balance'];
                        $special = @$value['loan_order']['special']['300']['loan_amount_balance'];
                        $emergent = @$value['loan_order']['emergent']['100']['loan_amount_balance'];
                        $total_loan_order = $normal_person+$normal_share+$special+$emergent;
                        $full_name = @$value['prename_short'].@$value['firstname_th'].' '.@$value['lastname_th'];
                        $full_name_guarantee = $value['guarantee_person'][0]['prename_short'].$value['guarantee_person'][0]['firstname_th'].' '.$value['guarantee_person'][0]['lastname_th'];
                        $count_guarantee_person = count($value['guarantee_person']);
                        $total_amount = $value['loan_amount']-$value['pay_amount']-$value['interest_amount'];
                        if ($count_guarantee_person <= 1){
                            $row_span = 2;
                        }else{
                            $row_span = $count_guarantee_person;
                        }
                        ?>
                        <tr>
                            <td class="table_body" style="text-align: left;vertical-align: top;mso-number-format:'@';" rowspan="<?php echo $row_span;?>">
                                <?php echo @$count_loan; ?></td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>">
                                <?php echo $full_name; ?></td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>">
                                <?php echo @$value['member_id']; ?></td>
                            <td class="table_body" style="text-align: left;vertical-align: top;">
                                <?php echo number_format(@$value['salary'],2); ?></td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>">
                                <?php echo number_format(@$value['loan_amount'],2); ?>
                            </td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>">
                                <?php echo !empty(@$value['deduct']['deduct_insurance'])?number_format(@$value['deduct']['deduct_insurance'],2):''; ?>
                            </td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>">
                                <?php echo @$value['period_amount']; ?>
                            </td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>">
                                <?php echo !empty(@$normal_person)?number_format($normal_person, 2):''; ?>
                            </td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>">
                                <?php echo !empty(@$normal_share)?number_format($normal_share, 2):''; ?>
                            </td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>">
                                <?php echo !empty(@$special)?number_format($special, 2):''; ?>
                            </td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>">
                                <?php echo !empty(@$emergent)?number_format($emergent, 2):''; ?>
                            </td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>">
                                <?php echo !empty(@$total_loan_order)?number_format($total_loan_order,2):''; ?>
                            </td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>">
                                <?php echo !empty($total_loan_order+$value['loan_amount'])?number_format($total_loan_order+$value['loan_amount'],2):''; ?>
                            </td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>">
                                <?php echo !empty(@$value['interest_amount'])?number_format($value['interest_amount'],2):''; ?>
                            </td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>">
                                <?php echo !empty($total_amount)?number_format($total_amount,2):''; ?>
                            </td>
                            <?php if ($count_guarantee_person == 0){ ?>
                                <td class="table_body" style="text-align: left;vertical-align: top;"></td>
                                <td class="table_body" style="text-align: left;vertical-align: top;"></td>
                            <?php }else{?>
                                <td class="table_body" style="text-align: left;vertical-align: top;"><?php echo number_format($value['guarantee_person'][0]['share_collect_value'],2); ?></td>
                                <td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $full_name_guarantee; ?></td>
                            <?php } ?>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>"><?php echo $value['loan_reason']; ?></td>
                            <td class="table_body" style="text-align: left;vertical-align: top;" rowspan="<?php echo $row_span;?>"><?php echo ''; ?></td>
                        </tr>
                        <?php if($count_guarantee_person > 1): ?>
                                <?php for ($x =1; $x <$count_guarantee_person ; $x++):?>
                                    <tr>
                                    <?php if($x ==1 ):?>
                                        <td  class="table_body" style="text-align: right;<?php echo $border; ?>" rowspan="<?= $row_span -1 ;?>">
                                    <?php echo number_format(@$value['salary_balance'],2); ?>
                                    </td>
                                    <?php endif ;
                                      $full_name_guarantee = $value['guarantee_person'][$x]['prename_short'].$value['guarantee_person'][$x]['firstname_th'].' '.$value['guarantee_person'][$x]['lastname_th'];
                                    
                                    ?>
                                    <td class="table_body" style="text-align: right;<?php echo $border; ?>"><?php echo number_format($value['guarantee_person'][$x]['share_collect_value'],2); ?></td>
                                    <td class="table_body" style="text-align: left;<?php echo $border; ?>"><?php echo $full_name_guarantee; ?></td>
                                    </tr>
                                <?php endfor; ?>
                        <?php else : ?>
                                <tr>
                                    <td  class="table_body" style="text-align: right;<?php echo $border; ?>">
                                    <?php echo number_format(@$value['salary_balance'],2); ?>
                                    </td>
                                    <td class="table_body" colspan="2"></td>
                                </tr>
                        <?php endif?>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
            <table style="width: 100%;" class="m-t-2" border="0">
                <tr></tr>
                <tr>
                    <th></th>
                    <th class="table_title_footer_right" style="vertical-align: middle;" colspan = '3'><?php echo "________________________";?></th>
                    <th class="table_title_footer_left" style="vertical-align: left;" colspan = '2'><?php echo "เจ้าหน้าที่สหกรณ์ " ;?></th>
                    <th class="table_title_footer_right" style="vertical-align: middle;" colspan = '3'><?php echo "________________________";?></th>
                    <th class="table_title_footer_left" style="vertical-align: left;" colspan = '2'><?php echo "ผู้ช่วยผู้จัดการสหกรณ์ฯ ";?></th>
                    <th class="table_title_footer_right" style="vertical-align: middle;" colspan = '3'><?php echo "________________________" ;?></th>
                    <th class="table_title_footer_left" style="vertical-align: left;" colspan = '2'><?php echo "เลขานุการคณะกรรมการเงินกู้";?></th>
                    <th></th>
                </tr>
                <tr></tr>
                <tr>
                    <td></td>
                    <td class="table_title_footer_right" style="vertical-align: middle;" colspan = '3'><?php echo "(....................................................)";?></td>
                    <td class="table_title_footer_left" style="vertical-align: middle;" colspan = '2'><?php echo " " ;?></td>
                    <td class="table_title_footer_right" style="vertical-align: middle;" colspan = '3'><?php echo "(....................................................)";?></td>
                    <td class="table_title_footer_left" style="vertical-align: middle;" colspan = '2'><?php echo " ";?></td>
                    <td class="table_title_footer_right" style="vertical-align: middle;" colspan = '3'><?php echo "(....................................................)" ;?></td>
                    <td class="table_title_footer_left" style="vertical-align: middle;" colspan = '2'><?php echo "";?></td>
                    <td></td>
                </tr>
            </table>
        </body>
	</html>
</pre>