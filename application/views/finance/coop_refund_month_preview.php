<style>
    .table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
        font-size: 14px;
    }
    .table-view-2>thead>tr>th{
        border-top: 1px solid #000 !important;
        border-bottom: 1px solid #000 !important;
        font-size: 14;
    }
    .table-view-2>tbody>tr>td{
        border: 0px !important;
        /*font-family: upbean;
        font-size: 16px;*/
        font-family: Tahoma;
        font-size: 12px;
    }
    .border-bottom{
        border-bottom: 1px solid #000 !important;
        font-weight: bold;
    }

    .foot-border{
        border-top: 1px solid #000 !important;
        border-bottom: double !important;
        font-weight: bold;
    }
    .table {
        color: #000;
    }
</style>
<?php
$title_date = "";
if($_GET['month']!='' && $_GET['year']!=''){
    $day = '';
    $month = $_GET['month'];
    $year = $_GET['year'];
    $title_date = " เดือน ".$month_arr[$month]." ปี ".($year);
}
$last_runno = 0;
$all_withdrawal = 0;
$all_deposit = 0;
$all_balance = 0;

$prev_member_id = 'x';
$total = array();
if(!empty($datas)){
    foreach($datas AS $page=>$data_row){
        ?>

        <div style="width: 1500px;"  class="page-break">
            <div class="panel panel-body" style="padding-top:10px !important;min-height: 950px;">
                <table style="width: 100%;">
                    <?php

                    if(@$page == 1){
                        ?>
                        <tr>
                            <td style="width:100px;vertical-align: top;">

                            </td>
                            <td class="text-center">
                                <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
                                <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                                <h3 class="title_view">รายการคืนเงินประจำเดือน<?php echo $title_date;?></h3>
                            </td>
                            <td style="width:100px;vertical-align: top;" class="text-right">
                                <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                                <?php
                                $get_param = '?';
                                foreach(@$_GET as $key => $value){
                                    //if($key != 'month' && $key != 'year' && $value != ''){
                                    $get_param .= $key.'='.$value.'&';
                                    //}
                                }
                                $get_param = substr($get_param,0,-1);
                                ?>
                                <a class="no_print"  target="_blank" href="<?php echo base_url('/finance/coop_refund_month_excel'.$get_param); ?>">
                                    <button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }else{
                        ?>
                        <tr>
                            <td colspan="3" style="text-align: left;">&nbsp;</td>
                        </tr>
                        <?php
                    }
                    ?>

                    <tr>
                        <td colspan="11" style="text-align: right;">
                            <span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>
                            <span class="title_view">   เวลา <?php echo date('H:i:s');?></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" style="text-align: right;">
                            <span class="title_view">หน้าที่ <?php echo $page.'/'.$page_all;?></span><br>
                        </td>
                    </tr>
                </table>

                <table class="table table-view table-center">
                    <thead>
                    <tr>
                        <th style="width: 40px;vertical-align: middle;" rowspan="2">ลำดับ</th>
                        <th style="width: 200px;vertical-align: middle;" rowspan="2">หน่วยงาน</th>
                        <th rowspan="2" style="width: 80px;vertical-align: middle; ">ทะเบียนสมาชิก</th>
                        <th rowspan="2" style="width: 160px;vertical-align: middle;">ชื่อสมาชิก</th>
                        <th colspan="4" style="width: 100px;vertical-align: middle;">จ่ายเงินระหว่างเดือน</th>
                        <th colspan="3"style="width: 160px;vertical-align: middle;">รายการเรียกเก็บ</th>
                        <th colspan="3"style="width: 80px;vertical-align: middle;">เงินรอจ่ายคืน</th>
                    </tr>
                    <tr>
                        <th style="width: 100px;vertical-align: middle;">รายการ</th>
                        <th style="width: 80px;vertical-align: middle;">เงินต้น</th>
                        <th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th>
                        <th style="width: 80px;vertical-align: middle;">รวม</th>
                        <th style="width: 80px;vertical-align: middle;">เงินต้น</th>
                        <th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th>
                        <th style="width: 80px;vertical-align: middle;">รวม</th>
                        <th style="width: 80px;vertical-align: middle;">เงินคืน</th>
                        <th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th>
                        <th style="width: 80px;vertical-align: middle;">รวม</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    $n = $last_runno;
                    if(!empty($data_row)){
                        //echo "<pre>";echo print_r($data_row);exit;
                        foreach($data_row as $key => $row){
                            if(!empty($row['member_id']) ) {

                                ?>
                                <tr>
                                    <td style="text-align: center;vertical-align: top;"><?php echo $i+1; ?></td>
                                    <td style="text-align: center;vertical-align: top;"><?php echo $row['department_name']; ?></td>
                                    <td style="text-align: center;vertical-align: top;"><?php echo $row['member_id']; ?></td>
                                    <td style="text-align: left;vertical-align: top;"><?php echo $row['member_name']; ?></td>
                                    <td style="text-align: center;vertical-align: top;"><?php echo $row['deduct_code']; ?></td>
                                    <td style="text-align: right;vertical-align: top;"><?php echo number_format($row['principal_month'],2); ?></td>
                                    <td style="text-align: right;vertical-align: top;"><?php echo number_format($row['interest_month'],2); ?></td>
                                    <td style="text-align: right;vertical-align: top;"><?php echo number_format(($row['interest_month']+$row['principal_month']),2); ?></td>
                                    <td style="text-align: right;vertical-align: top;"><?php echo number_format($row['principal'],2); ?></td>
                                    <td style="text-align: right;vertical-align: top;"><?php echo number_format($row['interest'],2); ?></td>
                                    <td style="text-align: right;vertical-align: top;"><?php echo number_format(($row['interest']+$row['principal']),2); ?></td>

                                    <?php
                                    if($row['loan_amount_balance']<=0){
                                        $money_refund =$row['principal'];
                                        $interest_refund =$row['interest'];
                                        $total_refund = $money_refund+$interest_refund;?>

                                  <?php  }else{
                                        $money_refund =$row['principal_month']-$row['principal'];
                                        $interest_refund =$row['interest_month']-$row['interest'];
                                        $total_refund = $money_refund+$interest_refund;
                                   } ?>
                                 <td style="text-align: right;vertical-align: top;"><?php echo number_format($money_refund,2); ?></td>
                                 <td style="text-align: right;vertical-align: top;"><?php echo number_format($interest_refund,2); ?></td>
                                 <td style="text-align: right;vertical-align: top;"><?php echo number_format($total_refund,2); ?></td>
<!--                             <td style="text-align: right;vertical-align: top;">--><?php //echo number_format($row['loan_amount_balance'],2); ?><!--</td>-->
                                    <?php  ?>
                                </tr>
                                <?php
                                $total['principal_month'] += $row['principal_month'];
                                $total['interest_month'] += $row['interest_month'];
                                $total['total_month'] += number_format(($row['interest_month']+$row['principal_month']),2);
                                $total['principal'] += $row['principal'];
                                $total['interest'] += $row['interest'];
                                $total['money_pay'] += number_format(($row['interest']+$row['principal']),2);
                                $money_refund += $money_refund;
                                $interest_refund += $interest_refund;
                                $total_refund+= $total_refund;
                                $prev_member_id = $row['member_id'];
                                //echo  $total_refund;exit;
                                $i++;
                            }
                        }
                    }
                    if($page == $page_all) {
                        ?>

                        <tr>
                            <td colspan="5" style="text-align: center;vertical-align: top;">ยอดรวม</td>
                            <td style="text-align: right;vertical-align: top;"><?php echo number_format( $total['principal_month'],2);?></td>
                            <td style="text-align: right;vertical-align: top;"><?php echo number_format($total['interest_month'],2);?></td>
                            <td style="text-align: right;vertical-align: top;"><?php echo number_format($total['total_month'] ,2);?></td>
                            <td style="text-align: right;vertical-align: top;"><?php echo number_format($total['principal'] ,2);?></td>
                            <td style="text-align: right;vertical-align: top;"><?php echo number_format($total['interest'] ,2);?></td>
                            <td style="text-align: right;vertical-align: top;"><?php echo number_format( $total['money_pay'] ,2);?></td>
                            <td style="text-align: right;vertical-align: top;"><?php echo  $money_refund;?></td>
                            <td style="text-align: right;vertical-align: top;"><?php echo  $interest_refund;?></td>
                            <td style="text-align: right;vertical-align: top;"><?php echo  $total_refund;?></td>

                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
}
?>