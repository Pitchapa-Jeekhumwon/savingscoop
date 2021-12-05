<style type="text/css">
    body, .paragraph span {
        font-family: 'THSarabunNew', sans-serif;
        font-size: 14px;
        line-height: 1.8531312em;
    }
    span {
        font-family: 'THSarabunNew', sans-serif;
        font-size: 14px;
        line-height: 1.8531312em;
    }
    .content-top, .content-middle, .content-bottom{
        display: flex;
        margin-bottom: 15px;
    }

    .columns-box {
        display:  flex;
        flex-direction: row;
        flex-grow: 5;
        align-items: flex-end;
    }

    .columns-box .start{
        flex: 2.75;

    }
    .columns-box .middle{
        flex: 2;
        margin-right: 5px;
    }
    .columns-box .end{
        flex: .25;
        text-align: start;
    }

    .content-top {
        width: 100%;
        font-size: 14px;
    }

    .content-middle {
        flex-direction: column;
        justify-content: start;
        text-align: start;
    }

    .content-bottom{
        width: 100%;
        flex-direction: row;
    }

    .column-left, .column-right{
        width: 100%;
    }

    .sign-box {
        display: flex;
        flex-direction: column;
        width: 100%;
        margin-bottom: 25px;
    }
    .sign-box .sign-item{
        display: flex;
        flex-grow: 4;
        line-height: 2em;
    }

    .sign-box .sign-item .start{
        text-align: end;
        flex: 0.45;
    }
    .sign-box .sign-item .end{
        text-align: start;
        flex: 2;
    }
    .sign-box .sign-item .middle{
        flex: 2.6;
        border-bottom: 2px dotted #000;
    }

    .column-left:first-child {
        padding-right: 25px;
    }

    .line-block{
        align-items: flex-end;
    }
    .line-block .txt-line-detail {
        display: flex;
        justify-content: flex-start;
        height: 25px;
    }
    .line-block .txt-line-data {
        display: flex;
        justify-content: flex-end;
        align-items: flex-end;
        border-bottom: 2px dotted #000;
        height: 25px;
    }
    .line-block .txt-line-unit {
        display: flex;
        justify-content: center;
        height: 25px;
    }

    .txt-line-detail, .txt-line-data, .txt-line-unit, .check-inline, .first-line, .second-line{
        margin-bottom: 0.66em;
    }

    .txt-line-header {
        font-weight: 700;
        text-decoration: underline solid #000;
    }

    .txt-line-data.txt-line-non-underline { border-bottom: none !important; }

    .block-checked {
        display: flex;
        flex-direction: column;
        width: 100%;
        justify-content: flex-start;
    }

    .block-checked .txt-line-detail { justify-content: flex-start; text-align: start; }

    .check-inline { display: flex; text-align: start; }
    .check-inline .checkbox-item { display: flex; flex-direction: column; width: 100%; justify-content: flex-start; }

    .flex-box {
        display: flex;
        flex-direction: column;
        padding: 80px 65px 30px 100px !important;
    }

    .list-condition-block {
        margin-left: 35px;
        line-height: 1.8531312em;
    }

    .title{
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 1.1em;
    }

    .paragraph{
        display: flex;
        justify-items: flex-start;
        flex-direction: column;
        width: 100%;
    }

    .first-line { display: flex; flex-grow: 100; margin-left: 70px;  margin-bottom: 1.1em;}
    .first-line .li-item-1 { display: flex ; flex: 1; justify-items: flex-end; align-items: flex-end; line-height: 1}
    .first-line .li-item-2 { display: flex ; flex: 32; border-bottom: 2px dotted #000; align-items: flex-end; line-height: 1}
    .first-line .li-item-3 { display: flex ; flex: 9; justify-content: center; align-items: flex-end; line-height: 1}
    .first-line .li-item-4 { display: flex ; flex: 20; border-bottom: 2px dotted #000; align-items: flex-end; line-height: 1}
    .first-line .li-item-5 { display: flex ; flex: 1; align-items: flex-end; line-height: 1}
    .first-line .li-item-1 { display: flex ; flex: 1; justify-items: flex-end; align-items: flex-end; line-height: 1}

    .second-line { display: flex; flex-grow: 100; align-items: flex-end; line-height: 1;  margin-bottom: 1.1em;}
    .second-line .li-item-1 { display: flex ; flex: 34; border-bottom: 2px dotted #000; align-items: flex-end; line-height: 1}
    .second-line .li-item-2 { display: flex ; flex: 17; align-items: flex-end; line-height: 1}
    .second-line .li-item-3 { display: flex ; flex: 28; border-bottom: 2px dotted #000; align-items: flex-end; line-height: 1}

    .third-line { display: flex; justify-items: flex-start;  margin-bottom: 1.1em;}

    .sign-receiver { display: flex; flex-direction: row; flex-grow: 2;}
    .sign-receiver .empty, .sign-receiver .receiver { flex: 1; width: 100%; }
    .receiver { display: flex; flex-direction: column}

    .dotted-underline { line-height: 1 }

    @media print {
        body, .paragraph span {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 14px;
            line-height: 1.8531312em;
        }
        span {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 14px;
            line-height: 1.8531312em;
        }
        .content-top, .content-middle, .content-bottom{
            display: flex;
            margin-bottom: 25px;
        }

        .columns-box {
            display:  flex;
            flex-direction: row;
            flex-grow: 5;
            align-items: flex-end;
        }

        .columns-box .start{
            flex: 2.75;

        }
        .columns-box .middle{
            flex: 2;
            margin-right: 5px;
        }
        .columns-box .end{
            flex: .25;
            text-align: start;
        }

        .content-top {
            width: 100%;
            font-size: 14px;
        }

        .content-middle {
            flex-direction: column;
            justify-content: start;
            text-align: start;
        }

        .content-bottom{
            width: 100%;
            flex-direction: row;
        }

        .column-left, .column-right{
            width: 100%;
        }

        .sign-box {
            display: flex;
            flex-direction: column;
            width: 100%;
            margin-bottom: 25px;
        }
        .sign-box .sign-item{
            display: flex;
            flex-grow: 4;
            line-height: 2em;
        }

        .sign-box .sign-item .start{
            text-align: end;
            flex: 0.45;
        }
        .sign-box .sign-item .end{
            text-align: start;
            flex: 2;
        }
        .sign-box .sign-item .middle{
            flex: 2.6;
            border-bottom: 2px dotted #000;
        }

        .column-left:first-child {
            padding-right: 25px;
        }

        .line-block{
            align-items: flex-end;
        }
        .line-block .txt-line-detail {
            display: flex;
            justify-content: flex-start;
            height: 25px;
        }
        .line-block .txt-line-data {
            display: flex;
            justify-content: flex-end;
            align-items: flex-end;
            border-bottom: 2px dotted #000;
            height: 25px;
        }
        .line-block .txt-line-unit {
            display: flex;
            justify-content: center;
            height: 25px;
        }

        .txt-line-detail, .txt-line-data, .txt-line-unit, .check-inline, .first-line, .second-line{
            margin-bottom: 0.87em;
        }

        .txt-line-header {
            font-weight: 700;
            text-decoration: underline solid #000;
        }

        .txt-line-data.txt-line-non-underline { border-bottom: none !important; }

        .block-checked {
            display: flex;
            flex-direction: column;
            width: 100%;
            justify-content: flex-start;
        }

        .block-checked .txt-line-detail { justify-content: flex-start; text-align: start; }

        .check-inline { display: flex; text-align: start; }
        .check-inline .checkbox-item { display: flex; flex-direction: column; width: 100%; justify-content: flex-start; }

        .flex-box {
            display: flex;
            flex-direction: column;
            padding: 80px 65px 30px 100px !important;
        }

        .list-condition-block {
            margin-left: 35px;
            line-height: 1.8531312em;
        }

        .title{
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 1.1em;
        }

        .paragraph{
            display: flex;
            justify-items: flex-start;
            flex-direction: column;
            width: 100%;
        }

        .first-line { display: flex; flex-grow: 100; margin-left: 70px;  margin-bottom: 1.1em;}
        .first-line .li-item-1 { display: flex ; flex: 1; justify-items: flex-end; align-items: flex-end; line-height: 1}
        .first-line .li-item-2 { display: flex ; flex: 32; border-bottom: 2px dotted #000; align-items: flex-end; line-height: 1}
        .first-line .li-item-3 { display: flex ; flex: 9; justify-content: center; align-items: flex-end; line-height: 1}
        .first-line .li-item-4 { display: flex ; flex: 20; border-bottom: 2px dotted #000; align-items: flex-end; line-height: 1}
        .first-line .li-item-5 { display: flex ; flex: 1; align-items: flex-end; line-height: 1}
        .first-line .li-item-1 { display: flex ; flex: 1; justify-items: flex-end; align-items: flex-end; line-height: 1}

        .second-line { display: flex; flex-grow: 100; align-items: flex-end; line-height: 1;  margin-bottom: 1.1em;}
        .second-line .li-item-1 { display: flex ; flex: 34; border-bottom: 2px dotted #000; align-items: flex-end; line-height: 1}
        .second-line .li-item-2 { display: flex ; flex: 17; align-items: flex-end; line-height: 1}
        .second-line .li-item-3 { display: flex ; flex: 28; border-bottom: 2px dotted #000; align-items: flex-end; line-height: 1}

        .third-line { display: flex; justify-items: flex-start;  margin-bottom: 1.1em;}

        .sign-receiver { display: flex; flex-direction: row; flex-grow: 2;}
        .sign-receiver .empty, .sign-receiver .receiver { flex: 1; width: 100%; }
        .receiver { display: flex; flex-direction: column}

        .dotted-underline { line-height: 1 }
    }
</style>
<div style="width: 1000px" class="page-break">
    <div class="panel panel-body flex-box" style="height: 1400px;">
        <div class="title">
            แบบรายงานรายละเอียดการยื่นกู้สามัญ (หุ้นค้ำประกัน)
        </div>
        <div class="content-top">
            <div class="column-left">
            </div>
            <div class="column-right">
                <div class="columns-box" style="margin-right: 30px;">
                    <div class="start line-block">
                    </div>
                    <div class="middle line-block" style="flex:1;">
                        <div class="txt-line-unit">วันที่</div>
                    </div>
                    <div class="middle line-block" style="flex:4;">
                        <div class="txt-line-data" style="justify-content: center"><?php echo $this->center_function->ConvertToThaiDate(@$row_loan['createdatetime'],0,0);?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-top">
            <div class="column-left">
                <div class="columns-box ">
                    <div class="start line-block">
                        <div class="txt-line-detail">ผู้กู้</div>
                        <div class="txt-line-detail">ขอกู้</div>
                        <div class="txt-line-detail">บวก : ค่าเบี้ยประกัน</div>
                        <div class="txt-line-detail">รวมเงินขอกู้</div>
                        <div class="txt-line-detail">หนี้คงเหลือ</div>
                        <div class="txt-line-detail">ดอกเบี้ย (*)</div>
                        <div class="txt-line-detail">เงินกู้ที่ได้รับ</div>
                    </div>
                    <div class="middle line-block">
                        <div class="txt-line-data"><?php echo @$row_member['prename_full'].@$row_member['firstname_th'].'  '.@$row_member['lastname_th']; ?></div>
                        <div class="txt-line-data"><?php echo (@$row_loan['loan_amount'] == 0)?"-":number_format(@$row_loan['loan_amount']-$deduct_insurance,2); ?></div>
                        <div class="txt-line-data"><?php echo number_format($deduct_insurance, 2); ?></div>
                        <div class="txt-line-data"><?php echo (@$row_loan['loan_amount'] == 0)?"-":number_format(@$row_loan['loan_amount'],2); ?></div>
                        <div class="txt-line-data"><?php echo number_format($principal_load, 2); ?></div>
                        <div class="txt-line-data"><?php echo number_format($interest_burden, 2); ?></div>
                        <div class="txt-line-data"><?php echo (@$row_loan['loan_amount'] == 0)?"-":number_format(@$row_loan['loan_amount']-$existing_loan,2); ?></div>
                    </div>
                    <div class="end line-block">
                        <div class="txt-line-unit"></div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">บาท</div>
                    </div>
                </div>
            </div>
            <div class="column-right">
                <div class="columns-box">
                    <div class="start line-block">
                        <div class="txt-line-detail" style="width: 150px">เริ่มหักชำระ</div>
                        <div class="txt-line-detail">ผ่อนชำระรายเดือน</div>
                        <div class="txt-line-detail">จำนวนงวดที่ขอผ่อน</div>
                        <div class="txt-line-detail">จำนวนงวดที่ชำระมาแล้ว</div>
                    </div>
                    <div class="middle line-block">
                        <div class="txt-line-data"><?php echo $this->center_function->ConvertToThaiDate(@$row_loan['date_start_period'],0,0);?></div>
                        <div class="txt-line-data"><?php echo number_format($total_paid_per_month, 2); ?></div>
                        <div class="txt-line-data"><?php echo number_format($row_loan['period_amount']); ?></div>
                        <div class="txt-line-data"><?php echo number_format($list_old_loan[0]['period_now']); ?></div>
                    </div>
                    <div class="end line-block">
                        <div class="txt-line-unit"></div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">งวด</div>
                        <div class="txt-line-unit">งวด</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-top">
            <div class="column-left">
                <div class="columns-box ">
                    <div class="start line-block">
                        <div class="txt-line-detail txt-line-header" style="width: 150px">ผลการคำนวนขอกู้</div>
                        <div class="txt-line-detail">เงินได้รายเดือน</div>
                        <div class="txt-line-detail">หัก : ค่าใช้จ่ายทั้งสิ้น</div>
                        <div class="txt-line-detail">คงเหลือ</div>
                        <div class="txt-line-detail">หัก : เงินงวด + ดอกเบี้ย</div>
                        <div class="txt-line-detail">เงินเดือนคงเหลือสุทธิ</div>
                    </div>
                    <?php
                    $salary_balance = $row_loan['salary']-$sum_cost_code-$total_paid_per_month-$interest_30_day;
                    ?>
                    <div class="middle line-block">
                        <div class="txt-line-data txt-line-non-underline">&nbsp;</div>
                        <div class="txt-line-data"><?php echo number_format($row_loan['salary'], 2); ?></div>
                        <div class="txt-line-data"><?php echo number_format($cost_all, 2); ?></div>
                        <div class="txt-line-data"><?php echo number_format($row_loan['salary']-$cost_all, 2); ?></div>
                        <div class="txt-line-data"><?php echo number_format($total_paid_per_month+$interest_30_day, 2); ?></div>
                        <div class="txt-line-data"><?php echo number_format($salary_balance-$cost_all, 2); ?></div>
                    </div>
                    <div class="end line-block">
                        <div class="txt-line-unit">&nbsp;</div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">บาท</div>
                    </div>
                </div>
            </div>
            <div class="column-right">
                <div class="columns-box">
                    <div class="start line-block">
                        <div class="txt-line-detail txt-line-header" style="width: 240px">รายการเงินกู้อื่นของผู้ขอกู้</div>
                        <?php
                        $total_old_loan_amount_balance = 0;
                        foreach ($loan_order as $loan_type => $loan_name) {
                            foreach ($loan_name as $loan_name_id => $detail) {
                                foreach ($detail as $key => $value) {
                                    if($loan_type == '1'){
                                        $value['loan_name'] = 'กู้ฉุกเฉิน';
                                    }else  if($loan_type == '3'){
                                        $value['loan_name'] = 'กู้พิเศษ';
                                    } ?>
                                    <div class="txt-line-detail"><?php echo 'เงิน' . $value['loan_name'].' '.$value['contract_number'] ; ?></div>
                                <?php }
                            }
                        } ?>
                        <div class="txt-line-detail">รวมเงินกู้เดิม</div>
                        <div class="txt-line-detail">รวมเงินกู้เดิมและที่ขอกู้ครั้งนี้</div>
                    </div>
                    <div class="end line-block">
                        <div class="txt-line-unit" style="width: 50px">&nbsp;</div>
                        <?php foreach ($loan_order as $loan_type => $loan_name) {
                            foreach ($loan_name as $loan_name_id => $detail) {
                                foreach ($detail as $key => $value) { ?>
                                    <div class="txt-line-unit">คงเหลือ</div>
                                <?php }
                            }
                        } ?>
                        <div class="txt-line-unit">คงเหลือ</div>
                        <div class="txt-line-unit">คงเหลือ</div>
                    </div>
                    <div class="middle line-block">
                        <div class="txt-line-data txt-line-non-underline">&nbsp;</div>
                        <?php foreach ($loan_order as $loan_type => $loan_name) {
                            foreach ($loan_name as $loan_name_id => $detail) {
                                foreach ($detail as $key => $value) {
                                    $total_old_loan_amount_balance += $value['loan_amount_balance']?>
                                    <div class="txt-line-data"><?php echo $value['loan_amount_balance'] == '0'? '-' : number_format($value['loan_amount_balance'], 2); ?></div>
                                <?php }
                            }
                        } ?>
                        <div class="txt-line-data"><?php echo $total_old_loan_amount_balance == '0'? '-' : number_format($total_old_loan_amount_balance, 2); ?></div>
                        <div class="txt-line-data"><?php echo number_format($total_old_loan_amount_balance+@$row_loan['loan_amount'], 2); ?></div>
                    </div>
                    <div class="end line-block">
                        <div class="txt-line-unit" style="width: 50px">&nbsp;</div>
                        <?php foreach ($loan_order as $loan_type => $loan_name) {
                            foreach ($loan_name as $loan_name_id => $detail) {
                                foreach ($detail as $key => $value) { ?>
                                    <div class="txt-line-unit">บาท</div>
                                <?php }
                            }
                        } ?>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">บาท</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-top" style="margin-bottom: 0px;">
            <div class="columns-box">
                <div class="txt-line-detail txt-line-header" style="width: 170px;">ข้อมูลการค้ำประกันรายอื่น</div>
            </div>
        </div>
        <?php
        $num = 0;
        foreach ($guarantors as $key => $value){ ?>
            <div class="content-top" style="margin-bottom: 0px;">
                <div class="column-left">
                    <div class="columns-box">
                        <div class="end line-block">
                            <div class="txt-line-detail" style="width: 10px"><?php echo ++$num;?>.</div>
                            <div class="txt-line-detail"></div>
                            <div class="txt-line-detail"></div>
                        </div>
                        <div class="start line-block">
                            <div class="txt-line-detail">ชื่อ - สกุล </div>
                            <div class="txt-line-detail">เลขที่สัญญา</div>
                            <div class="txt-line-detail">วงเงินค้ำประกัน</div>
                        </div>
                        <div class="middle line-block">
                            <div class="txt-line-data"><?php echo $value['prename_full'].$value['firstname_th'].' '.$value['lastname_th'] ;?></div>
                            <div class="txt-line-data"><?php echo $value['contract_number']; ?></div>
                            <div class="txt-line-data"><?php echo number_format($value['guarantee_person_amount'], 2); ?></div>
                        </div>
                        <div class="end line-block">
                            <div class="txt-line-unit"></div>
                            <div class="txt-line-unit"></div>
                            <div class="txt-line-unit">บาท</div>
                        </div>
                    </div>
                </div>
                <div class="column-right">
                    <div class="columns-box">
                        <div class="start line-block">
                            <div class="txt-line-detail" style="width: 200px">เลขที่สมาชิก</div>
                            <div class="txt-line-detail">วงเงินกู้</div>
                            <div class="txt-line-detail">วงเงินค้ำประกันคงเหลือ</div>
                        </div>
                        <div class="middle line-block">
                            <div class="txt-line-data"><?php echo $value['member_id']; ?></div>
                            <div class="txt-line-data"><?php echo number_format($value['loan_amount'], 2); ?></div>
                            <div class="txt-line-data"><?php echo number_format($value['loan_amount_balance'], 2); ?></div>
                        </div>
                        <div class="end line-block">
                            <div class="txt-line-unit"></div>
                            <div class="txt-line-unit">บาท</div>
                            <div class="txt-line-unit">บาท</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <br>
        <div class="content-middle">
            <div class="txt-line-detail txt-line-header">เสนอมูลค่าหุ้นค้ำประกันครั้งนี้</div>
            <?php $day_date = substr(@$row_loan['createdatetime'], 8,2); ?>
            <div class="list-condition-block">
                <div class="txt-line-detail" style="margin-left: 40px"> ใช้ใบหุ้นของสหกรณ์ เลขที่ <?php echo $row_member['member_id'] ;?>  จำนวน <?php echo number_format($g_share_amt/10, 0); ?> หุ้น เป็นเงิน <?php echo number_format($g_share_amt, 2); ?> บาท เพื่อเป็นหลักประกันเงินกู้ </div>
            </div>
            <div class="txt-line-detail txt-line-header">หลักเกณฑ์การกู้สามัญ (หุ้นค้ำประกัน)</div>
            <div class="list-condition-block">
                <!-- ลูกค้าจะเช็คข้อมูลเอง ไม่ต้อง recheck -->
                <div class="txt-line-detail"> [/<?php //echo @$member_month > 6 ? '/' : '&nbsp;'?>] เป็นสมาชิกตั้งแต่ <?php echo $this->center_function->ConvertToThaiDate($row_member['member_date'], 0, 0); ?> ไม่น้อยกว่า 6 เดือน</div>
                <div class="txt-line-detail"> [/<?php //echo @$day_date <= 7 ? '/' : '&nbsp;'?>] ยื่นเอกสารคำขอกู้ภายในวันที่ 7 ของเดือน </div>
                <div class="txt-line-detail"> [/<?php //echo @$cal_share >= $row_loan['loan_amount'] ? '/' : '&nbsp;'?>] ขอกู้ไม่เกิน มูลค่าหุ้นที่มีอยู่ มีหุ้น <?php echo number_format($cal_share, 2);?> บาท</div>
                <div class="txt-line-detail"> [/<?php //echo @$list_old_loan[0]['period_now'] > 6 ? '/' : '&nbsp;'?>] กู้เพิ่มเติม ผ่อนชำระแล้ว <?php echo @$list_old_loan[0]['period_now'];?> งวด ไม่น้อยกว่า 6 งวด</div>
                <div class="txt-line-detail"> [/<?php //cho @$row_loan['period_amount'] <= 156 ? '/' : '&nbsp;'?>] ขอผ่อนชำระไม่เกิน 156 งวด </div>
                <div class="txt-line-detail"> [/] เงินเดือนคงเหลือสุทธิ <?php echo number_format(@$net, 2); ?> บาท คิดเป็น <?php echo number_format(@$net_percent,2);?>% ของเงินได้รายเดือน </div>
            </div>
            <div class="line-end-page">
                <hr style="height:2px;color:black;background-color:black">
            </div>
        </div>
    </div>
</div>

<div style="width: 1000px" class="page-break">
    <div class="panel panel-body flex-box" style="padding-top: 10px !important; height: 1400px;">
        <div class="page-header">
        </div>
        <div class="content-bottom">
            <div class="column-left" style="padding-left: 200px;    padding-right: 30px;">
                <div class="sign-box">
                    <div class="sign-item">
                        <div class="start">ลงชื่อ</div>
                        <div class="middle"></div>
                        <div class="end" style="flex: 3">เจ้าหน้าที่ผู้ตรวจสอบ</div>
                    </div>
                    <div class="sign-item situation">
                        <div class="start">(</div>
                        <div class="middle"></div>
                        <div class="end" style="flex: 3">)</div>
                    </div>
                </div>
                <div class="sign-box">
                    <div class="sign-item">
                        <div class="start">ลงชื่อ</div>
                        <div class="middle"></div>
                        <div class="end" style="flex: 3">เจ้าหน้าที่ผู้ตรวจทาน พิจารณาเสนอเลขานุการฯ</div>
                    </div>
                    <div class="sign-item situation">
                        <div class="start">(</div>
                        <div class="middle"></div>
                        <div class="end" style="flex: 3">)</div>
                    </div>
                </div>
                <div class="sign-box">
                    <div class="sign-item">
                        <div class="start">ลงชื่อ</div>
                        <div class="middle"></div>
                        <div class="end" style="flex: 3">เลขานุการฯ พิจารณาเสนอคณะกรรมการเงินกู้ฯ</div>
                    </div>
                    <div class="sign-item situation">
                        <div class="start">(</div>
                        <div class="middle"></div>
                        <div class="end" style="flex: 3">)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
