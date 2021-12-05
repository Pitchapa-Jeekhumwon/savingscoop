<style>
    .table-view>thead,
    .table-view>thead>tr>td,
    .table-view>thead>tr>th {
        font-size: 14px;
    }

    .table-view-2>thead>tr>th {
        border-top: 1px solid #000 !important;
        border-bottom: 1px solid #000 !important;
        font-size: 16px;
    }

    .table-view-2>tbody>tr>td {
        border: 0px !important;
        /*font-family: upbean;
		font-size: 16px;*/
        font-family: Tahoma;
        font-size: 12px;
    }

    .border-bottom {
        border-bottom: 1px solid #000 !important;
        font-weight: bold;
    }

    .foot-border {
        border-top: 1px solid #000 !important;
        border-bottom: double !important;
        font-weight: bold;
    }

    .table {
        color: #000;
    }

    @media print {
        .pagination {
            display: none;
        }
    }
</style>
<?php

if (@$_GET['start_date']) {
    $start_date_arr = explode('/', @$_GET['start_date']);
    $start_day = $start_date_arr[0];
    $start_month = $start_date_arr[1];
    $start_year = $start_date_arr[2];
    $start_year -= 543;
    $start_date = $start_year . '-' . $start_month . '-' . $start_day;
}

if (@$_GET['end_date']) {
    $end_date_arr = explode('/', @$_GET['end_date']);
    $end_day = $end_date_arr[0];
    $end_month = $end_date_arr[1];
    $end_year = $end_date_arr[2];
    $end_year -= 543;
    $end_date = $end_year . '-' . $end_month . '-' . $end_day;
}
$line =0;
$page = 1;

$sum_loan_amount = 0;
$sum_buy_share =0;
$sum_debet = 0;
$sum_depet_int = 0;
$sum_insulance = 0;
$sum_deposit = 0;
$sum_other =0;
$sum_duty = 0;
$sum_balance =0;
?>
<?php if($data):?>
    <?php foreach($data as $k=>$val):?>
    <?php if($line == 0):?>
<div style="width: 1500px;" class="page-break">
    <div class="panel panel-body" style="padding-top:10px !important;min-height: 1000px;">
        <?php if($page ==1):?>
        <table style="width: 100%;">
            <tr>
                <td style="width:100px;vertical-align: top;">
                </td>
                <td class="text-center">
                    <img src="<?php echo base_url(PROJECTPATH . '/assets/images/coop_profile/' . $_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
                    <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME']; ?></h3>
                    <h3 class="title_view">รายงานยอดจ่ายเงินกู้สุทธิ ของผู้ยื่นกู้</h3>
                    <?php if (!empty($type_name)) { ?><h3 class="title_view">ประเภท <?= $type_name; ?></h3><?php } ?>

                    <h3 class="title_view">
                        <?php
                        echo (@$_GET['start_date'] == @$_GET['end_date']) ? "" : "ตั้งแต่";
                        echo "วันที่ " . $this->center_function->ConvertToThaiDate($start_date);
                        echo (@$_GET['start_date'] == @$_GET['end_date']) ? "" : "  ถึงวันที่  " . $this->center_function->ConvertToThaiDate($end_date);
                        ?>
                    </h3>
                </td>
                <td style="width:100px;vertical-align: top;" class="text-right">
                    <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                    <?php
                    $get_param = '?';
                    foreach (@$_GET as $key => $value) {
                        if($key == 'export'){
                            $get_param .= $key . '=excel&';
                        }else{
                            $get_param .= $key . '=' . $value . '&';
                        }
                    }
                    $get_param = substr($get_param, 0, -1);
                    ?>
                    <a class="no_print" target="_blank" href="<?php echo base_url(PROJECTPATH . '/report_loan_data/coop_report_loan_request_preview' . $get_param); ?>">
                        <button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
                    </a>
                </td>
            </tr>
        </table>
        <?php $line +=$head_line; ?>
        <?php endif ;?>
 <table  style="width: 100%;">
 <tr>
                <td colspan="3" style="text-align: left;">
                    <span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'), 1, 0); ?></span>
                    <span class="title_view"> เวลา <?php echo date('H:i:s'); ?></span>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left;">
                    <span class="title_view">หน้าที่ <?php echo @$page . '/' . @$max_page; ?></span><br>
                </td>
            </tr>
 </table>
        <table class="table table-view table-center">
            <thead>
                <tr>
                    <th style="vertical-align: middle;width:3%">ลำดับ</th>
                    <th style="vertical-align: middle;width:5%">เลขที่</th>
                    <th style="vertical-align: middle;width:5%">รหัสสมาชิก</th>
                    <th style="vertical-align: middle;width:10%">ชื่อสมาชิก</th>
                    <th style="vertical-align: middle;width:5%">บัญชีธนาคาร</th>
                    <th style="vertical-align: middle;width:5%">ให้กู้</th>
                    <th style="vertical-align: middle;width:5%">ซื้อหุ้น</th>
                    <th style="vertical-align: middle;width:5%">หนี้ค้าง</th>
                    <th style="vertical-align: middle;width:5%">ดอกเบี้ย</th>
                    <th style="vertical-align: middle;width:5%">ประกัน</th>
                    <th style="vertical-align: middle;width:5%">เงินฝาก</th>
                    <th style="vertical-align: middle;width:5%">หนี้อื่นๆ</th>
                    <th style="vertical-align: middle;width:5%">อากร</th>
                    <th style="vertical-align: middle;width:5%">เหลือสุทธิ</th>
                    <th style="vertical-align: middle;width:5%">เชคเลขที่</th>
                </tr>
            </thead>
            <tbody>
            <?php $line ++; ?>
            <?php endif; 
            
            $balance = ($val['loan_amount'] - $deduct[$val['loan_id']]['deduct_share'] -$val['debet'] -$val['debet_int']  -$deduct[$val['loan_id']]['deduct_insurance'] -$deduct[$val['loan_id']]['deduct_blue_deposit']- $deduct[$val['loan_id']]['deduct_other'] -$deduct[$val['loan_id']]['']);
            $sum_loan_amount +=$val['loan_amount'] ;
            $sum_buy_share +=$deduct[$val['loan_id']]['deduct_share'];
            $sum_debet +=$val['debet'];
            $sum_depet_int +=$val['debet_int'];
            $sum_insulance +=$deduct[$val['loan_id']]['deduct_insurance'];
            $sum_deposit +=$deduct[$val['loan_id']]['deduct_blue_deposit'];
            $sum_other +=$deduct[$val['loan_id']]['deduct_other'];
            $sum_duty +=$deduct[$val['loan_id']][''];
            $sum_balance +=$balance;
        ?>
               
                    <tr>
                    <td style="vertical-align: middle;"> <?=$k+1?> </td>
                    <td style="vertical-align: middle;"> <?=$val['petition_number']?></td>
                    <td style="vertical-align: middle;"> <?=$val['member_id']?></td>
                    <td style="vertical-align: middle; text-align :left;"> <?=$val['member_name']?> </td>
                    <td style="vertical-align: middle;text-align :center;"> <?=$val['account_number']?> </td>
                    <td style="vertical-align: middle;text-align :right;"> <?=number_format($val['loan_amount'],2)?> </td>
                    <td style="vertical-align: middle;text-align :right;"> <?=number_format($deduct[$val['loan_id']]['deduct_share'],2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($val['debet'],2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($val['debet_int'],2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($deduct[$val['loan_id']]['deduct_insurance'],2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($deduct[$val['loan_id']]['deduct_blue_deposit'],2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($deduct[$val['loan_id']]['deduct_other'],2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($deduct[$val['loan_id']][''],2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($balance,2)?> </td>
                    <td style="vertical-align: middle;text-align :left"> <?=$val['ceche']?> </td>
                </tr>
                <?php $line ++; ?>
            <?php if($line >= $max_line -1 || $k+1 == count($data)) :?>
                <?php  if($k+1 == count($data)) :?>
                    <tr>
                    <td style="vertical-align: middle;text-align :center;" colspan="5"> รวมทั้งสิ้น </td>
                    <td style="vertical-align: middle;text-align :right;"> <?=number_format($sum_loan_amount,2)?> </td>
                    <td style="vertical-align: middle;text-align :right;"> <?=number_format($sum_buy_share,2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($sum_debet,2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($sum_depet_int,2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($sum_insulance,2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($sum_deposit,2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($sum_other,2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($sum_duty,2)?> </td>
                    <td style="vertical-align: middle;text-align :right"> <?=number_format($sum_balance,2)?> </td>
                    <td style="vertical-align: middle;text-align :left"></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $line = 0;
    $page ++;
 endif; ?>
<?php endforeach; ?>
<?php endif; ?>