<?php
$i = '1';
$h_border = "";
$b_border = "";
if(isset($_GET['excel'])){
    $i = '0';
    $file_name = " รายงาน".($_GET['loan_type'] ? $loan_type[$_GET['loan_type']] : 'เงินกู้')."คงเหลือ ".$date_title;
    //echo '<pre>'; print_r($_GET); echo '</pre>'; exit;
    header("Content-Disposition: attachment; filename=".$file_name.".xls");
    header("Content-type: application/vnd.ms-excel; charset=UTF-8");

    $h_border = ' border: thin solid black; ';
    $b_border = ' border-left: thin solid black;';
}

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
    <style>

        .table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
            font-size: 16px;
        }
        @page {
            size: landscape;
        }
        .table {
            color: #000;
        }
        <?php if(isset($_GET['excel'])) {?>

        * {
            all: unset;
        }

        body{
            background: #fff !important;
        }

        table {
            background: white !important;
        }

        table tbody tr td{
            border-collapse: collapse;
            border-top: none;
            border-bottom: none;
            border-left: thin solid black;
            border-right: thin solid black;
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
        <?php } ?>
    </style>
<?php
$get_param = '?excel=&';
foreach(@$_GET as $key => $value){
    if($key != 'loan_name'){
        $get_param .= $key.'='.$value.'&';
    }

    if($key == 'loan_name'){
        foreach($value as $key2 => $value2){
            $get_param .= $key.'[]='.$value2.'&';
        }
    }
}
$get_param = substr($get_param,0,-1);
?>
<?php if(!isset($_GET['excel'])){ ?>
    <div style="width: 1500px;" class="page-break">
        <div class="panel panel-body" style="padding-top:10px !important;min-height: 1000px;">
<?php } ?>
            <table style="width: 100%;">
                <tr>
                    <?php if(!isset($_GET['excel'])){ ?>
                        <td style="width:100px;vertical-align: top;">
                            <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
                        </td>
                    <?php } ?>
                    <td colspan="11" class="text-center">
                        <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                        <h3 class="title_view"><?php echo "รายชื่อสมาชิกที่ยื่นคำขอกู้ในเดือน".$month_arr[$_GET['approve_month']].' '.($_GET['approve_year']+543);?></h3>
                        <?php
                        if(empty($_GET['loan_name_all'])) {
                            $txts = array();
                            $Line = 0;
                            foreach ($loan_name as $key => $value) {
                                if($key != '0'){
                                    $txts[$Line] .= ', ';
                                }
                                if(getStrLenTH($txts[$Line].$value['loan_name']) > 110){
                                    $Line++;
                                }
                                $txts[$Line] .= $value['loan_name'];
                            }
                            foreach ($txts as $Line => $txt) {?>
                                <h3 class="title_view"><?php echo $txt;?></h3>
                            <?php }
                        }
                        ?>

                    </td>
                    <?php if($i == '1'){?>
                        <td style="width:100px;vertical-align: top;" class="text-right">
                            <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                            <a class="no_print" onclick="export_excel('<?php echo $get_param?>')"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>
                        </td>
                    <?php } ?>
                </tr>
            </table>
            <table class="table table-view table-center">
                <thead>
                <tr>
                    <th  style="width: 0px;vertical-align: middle; <?php echo $h_border?>" rowspan="2">ลำดับ<br>ที่</th>
                    <th  style="width: 0px;vertical-align: middle; <?php echo $h_border?>" rowspan="2">ชื่อ - นามสกุลผู้กู้</th>
                    <th  style="width: 0px;vertical-align: middle; <?php echo $h_border?>" rowspan="2">เลขที่สมาชิก</th>
                    <th  style="width: 0px;vertical-align: middle; <?php echo $h_border?>" rowspan="2">เงินเดือน/เงินเดือนคงเหลือ</th>
                    <th  style="width: 0px;vertical-align: middle; <?php echo $h_border?>" rowspan="2">จำนวนเงินกู้</th>
                    <th  style="width: 0px;vertical-align: middle; <?php echo $h_border?>" rowspan="2">ประกัน</th>
                    <th  style="width: 0px;vertical-align: middle; <?php echo $h_border?>" rowspan="2">จำนวนงวด</th>
                    <th  style="width: 0px;vertical-align: middle; <?php echo $h_border?>" colspan="6">สัญญากู้กับสหกรณ์</th>
                    <th  style="width: 0px;vertical-align: middle; <?php echo $h_border?>" rowspan="2">ดอกเบี้ย</th>
                    <th  style="width: 0px;vertical-align: middle; <?php echo $h_border?>" rowspan="2">จำนวนเงินกู้ที่ได้รับ</th>
                    <th  style="width: 0px;vertical-align: middle; <?php echo $h_border?>" colspan="2">หลักค้ำประกัน</th>
                    <th  style="min-width: 95px;vertical-align: middle; <?php echo $h_border?>" rowspan="2">วัตถุประสงค์การกู้</th>
                    <th  style="width: 0px;vertical-align: middle; <?php echo $h_border?>" rowspan="2">หมายเหตุ</th>
                </tr>
                <tr>
                    <th  style="vertical-align: middle; <?php echo $h_border?>">สัญญากู้สามัญคงเหลือ</th>
                    <th  style="vertical-align: middle; <?php echo $h_border?>"> สัญญากู้หุ้นค้ำคงเหลือ </th>
                    <th  style="vertical-align: middle; <?php echo $h_border?>">สัญญากู้พิเศษคงเหลือ</th>
                    <th  style="vertical-align: middle; <?php echo $h_border?>">สัญญากู้ฉุกเฉินคงเหลือ</th>
                    <th  style="vertical-align: middle; <?php echo $h_border?>">รวมหนี้รวม</th>
                    <th  style="vertical-align: middle; <?php echo $h_border?>">รวมหนี้กู้ครั้งนี้</th>
                    <th  style="vertical-align: middle; <?php echo $h_border?>">ผู้กู้มีหุ้น</th>
                    <th  style="vertical-align: middle; <?php echo $h_border?>">ชื่อ - นามสกุล (ผู้ค้ำประกัน)</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $count_loan = 0;
                $loan_amount=0;
                //echo '<pre>'; print_r(@$data); echo '</pre>';
                if(!empty($datas)){
                    foreach($datas as $pase => $data){
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
                                <td style="text-align: center;<?php echo $border; ?>;" rowspan="<?php echo $row_span;?>">
                                    <?php echo @$count_loan;
                                    if($_GET['dev'] == 'loan_id'){
                                        echo '<br>'.$value['loan_id'];
                                    }?>
                                </td>
                                <td style="text-align: left;<?php echo $border; ?>;" rowspan="<?php echo $row_span;?>">
                                    <?php echo @$full_name; ?>
                                </td>
                                <td style="text-align: center;<?php echo $border; ?>;" rowspan="<?php echo $row_span;?>">
                                    <?php echo @$value['member_id']; ?>
                                </td>
                                <td style="text-align: right;<?php echo $border; ?>">
                                    <?php echo number_format(@$value['salary'],2); ?>
                                </td>
                                <td style="text-align: right;<?php echo $border; ?>" rowspan="<?php echo $row_span;?>">
                                    <?php echo number_format(@$value['loan_amount'],2); ?>
                                </td>
                                <td style="text-align: right;<?php echo $border; ?>" rowspan="<?php echo $row_span;?>">
                                    <?php echo !empty(@$value['deduct']['deduct_insurance'])?number_format(@$value['deduct']['deduct_insurance'],2):''; ?>
                                </td>
                                <td style="text-align: right;<?php echo $border; ?>" rowspan="<?php echo $row_span;?>">
                                    <?php echo @$value['period_amount']; ?>
                                </td>
                                <td style="text-align: right;<?php echo $border; ?>" rowspan="<?php echo $row_span;?>">
                                    <?php echo !empty(@$normal_person)?number_format($normal_person, 2):''; ?>
                                </td>
                                <td style="text-align: right;<?php echo $border; ?>" rowspan="<?php echo $row_span;?>">
                                    <?php echo !empty(@$normal_share)?number_format($normal_share, 2):''; ?>
                                </td>
                                <td style="text-align: right;<?php echo $border; ?>" rowspan="<?php echo $row_span;?>">
                                    <?php echo !empty(@$special)?number_format($special, 2):''; ?>
                                </td>
                                <td style="text-align: right;<?php echo $border; ?>" rowspan="<?php echo $row_span;?>">
                                    <?php echo !empty(@$emergent)?number_format($emergent, 2):''; ?>
                                </td>
                                <td style="text-align: right;<?php echo $border; ?>" rowspan="<?php echo $row_span;?>">
                                    <?php echo !empty(@$total_loan_order)?number_format($total_loan_order,2):''; ?>
                                </td>
                                <td style="text-align: right;<?php echo $border; ?>" rowspan="<?php echo $row_span;?>">
                                    <?php echo !empty($total_loan_order+$value['loan_amount'])?number_format($total_loan_order+$value['loan_amount'],2):''; ?>
                                </td>
                                <td style="text-align: right;<?php echo $border; ?>" rowspan="<?php echo $row_span;?>">
                                    <?php echo !empty(@$value['interest_amount'])?number_format($value['interest_amount'],2):''; ?>
                                </td>
                                <td style="text-align: right;<?php echo $border; ?>" rowspan="<?php echo $row_span;?>">
                                    <?php echo !empty($total_amount)?number_format($total_amount,2):''; ?>
                                </td>
                                <?php if ($count_guarantee_person == 0){ ?>
                                    <td style="text-align: right;<?php echo $border; ?>"></td>
                                    <td style="text-align: left;<?php echo $border; ?>"></td>
                                <?php }else{?>
                                    <td style="text-align: right;<?php echo $border; ?>"><?php echo number_format($value['guarantee_person'][0]['share_collect_value'],2); ?></td>
                                    <td style="text-align: left;<?php echo $border; ?>"><?php echo $full_name_guarantee; ?></td>
                                <?php } ?>
                                <td style="text-align: right;<?php echo $border; ?>" rowspan="<?php echo $row_span;?>"><?php echo $value['loan_reason']; ?></td>
                                <td style="text-align: right;<?php echo $border; ?>" rowspan="<?php echo $row_span;?>"><?php echo ''; ?></td>
                            </tr>
                        <?php if($count_guarantee_person > 1): ?>
                                <?php for ($x =1; $x <$count_guarantee_person ; $x++):?>
                                    <tr>
                                    <?php if($x ==1 ):?>
                                        <td style="text-align: right;<?php echo $border; ?>" rowspan="<?= $row_span -1 ;?>">
                                    <?php echo number_format(@$value['salary_balance'],2); ?>
                                    </td>
                                    <?php endif ;
                                      $full_name_guarantee = $value['guarantee_person'][$x]['prename_short'].$value['guarantee_person'][$x]['firstname_th'].' '.$value['guarantee_person'][$x]['lastname_th'];
                                    
                                    ?>
                                    <td style="text-align: right;<?php echo $border; ?>"><?php echo number_format($value['guarantee_person'][$x]['share_collect_value'],2); ?></td>
                                    <td style="text-align: left;<?php echo $border; ?>"><?php echo $full_name_guarantee; ?></td>
                                    </tr>
                                <?php endfor; ?>
                        <?php else : ?>
                                <tr>
                                    <td style="text-align: right;<?php echo $border; ?>">
                                    <?php echo number_format(@$value['salary_balance'],2); ?>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                        <?php endif?>
                            <?php
                        }
                    }
                }
                ?>
                </tbody>
            </table>
				
            <table style="width: 100%;" class="m-t-2" border="0">
                <tr>
                    <th></th>
                    <th style="width: 150px; font-size: 16px;"><?php echo "____________________";?></th>
                    <th style="width: 200px; font-size: 16px;"><?php echo "เจ้าหน้าที่สหกรณ์ " ;?></th>
                    <th style="width: 150px; font-size: 16px;"><?php echo "____________________";?></th>
                    <th style="width: 200px; font-size: 16px;"><?php echo "ผู้ช่วยผู้จัดการสหกรณ์ฯ ";?></th>
                    <th style="width: 150px; font-size: 16px;"><?php echo "____________________" ;?></th>
                    <th style="width: 220px; font-size: 16px;"><?php echo "เลขานุการคณะกรรมการเงินกู้";?></th>
                    <th></th>
                </tr>
                <tr>
                    <td></td>
                    <td style="font-size: 16px;"><?php echo "(.......................................)".$month_arr[$m];?></td>
                    <td style="font-size: 16px;"><?php echo " " ;?></td>
                    <td style="font-size: 16px;"><?php echo "(.......................................)";?></td>
                    <td style="font-size: 16px;"><?php echo " ";?></td>
                    <td style="font-size: 16px;"><?php echo "(.......................................)" ;?></td>
                    <td style="font-size: 16px;"><?php echo "";?></td>
                    <td></td>
                </tr>
            </table>
<?php if(!isset($_GET['excel'])){ ?>
        </div>
    </div>
<?php } ?>
<?php if(!isset($_GET['excel'])) { ?>
    <script>
        function export_excel(get_param) {
            window.open('report_loan_request_detail_preview'+get_param, '_blank');
            //window.open('coop_report_loan_normal_excel?loan_type='+loan_type+'&year='+year+'&second_half=1','_blank');
        }
    </script>
<?php } ?>