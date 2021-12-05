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

    hr {
        display: block;
        height: 1px;
        background: transparent;
        width: 100%;
        border: none;
        border-top: solid 2px #000;
        margin-top: 0;
    }

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

        hr {
            display: block;
            height: 1px;
            background: transparent;
            width: 100%;
            border: none;
            border-top: solid 2px #000;
            margin-top: 0;
        }
    }
</style>
<div style="width: 1000px" class="page-break">
    <div class="panel panel-body flex-box" style="!important; height: 1400px;">
        <div class="title">
            แบบรายงานรายละเอียดการยื่นกู้ฉุกเฉิน
        </div>
        <div class="content-top">
            <div class="column-left">
                <div class="columns-box ">
                    <div class="start line-block">
                        <div class="txt-line-detail">วงเงินกู้</div>
                        <div class="txt-line-detail">ผ่อนชำระรายเดือน</div>
                        <div class="txt-line-detail">จำนวนงวดที่ขอผ่อน</div>
                        <div class="txt-line-detail">ดอกเบี้ย(*)</div>
                        <div class="txt-line-detail">คงเหลือ</div>
                        <div class="txt-line-detail txt-line-header">ผลการคำนวนขอกู้</div>

                        <div class="txt-line-detail">เงินได้รายเดือน</div>
                        <div class="txt-line-detail">หัก ค่าใช้จ่ายทั้งสิ้น</div>
                        <div class="txt-line-detail">คงเหลือ</div>
                        <div class="txt-line-detail">หัก เงินงวด + ดอกเบี้ย</div>
                        <div class="txt-line-detail">เงินเดือนคงเหลือสุทธิ</div>
                    </div>
                    <div class="middle line-block">
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo number_format($row_loan['loan_amount'], 2); ?></div></div>
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo number_format($total_paid_per_month, 2); ?></div></div>
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo number_format($row_loan['period_amount']); ?></div></div>
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo number_format(array_sum(array_column($list_old_loan, 'loan_interest_amount')), 2); ?></div></div>
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo number_format($total_amount, 2); ?></div></div>
                        <div class="txt-line-data txt-line-non-underline">&nbsp;</div>
                        <?php $salary = (!empty($row_report_detail))?@$row_report_detail['salary']:@$row_member['salary']; ?>
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo number_format($salary, 2); ?></div></div>
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo number_format($cost_all, 2); ?></div></div>
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo number_format($salary-$cost_all, 2); ?></div></div>
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo number_format($keep_loan, 2); ?></div></div>
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo number_format(($salary-($cost_all+$keep_loan)), 2); ?></div></div>
                    </div>
                    <div class="end line-block">
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">งวด</div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">บาท</div>
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
                        <div class="txt-line-detail">หนี้คงเหลือ</div>
                        <div class="txt-line-detail">จำนวนงวดที่ชำระมาแล้ว</div>
                        <div class="txt-line-detail">ค่าธรรมเนียม</div>
                        <div class="txt-line-detail">ดอกเบี้ย(จนท.สหกรณ์)</div>
                        <div class="txt-line-detail">เริ่มหักชำระ</div>

                    </div>
                    <div class="middle line-block">
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo number_format(array_sum(array_column($list_old_loan, 'loan_amount_balance')), 2); ?></div></div>
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo number_format($list_old_loan[0]['period_now']); ?></div></div>
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo number_format($deduct_loan_fee, 2); ?></div></div>
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo (@$deduct_before_interest > 0) ? number_format($deduct_before_interest, 2) : '-'; ?></div></div>
                        <div class="txt-line-data"><div class="dotted-underline"><?php echo $this->center_function->ConvertToThaiDate($row_loan['date_start_period'], 1, 0); ?></div></div>

                    </div>
                    <div class="end line-block">
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">งวด</div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">บาท</div>
                        <div class="txt-line-unit">&nbsp;</div>
                    </div>
                </div>
                <div class="block-checked">
                    <div class="txt-line-detail txt-line-header">เอกสารถูกต้องครงถ้วน</div>
                    <div class="check-inline">
                        <div class="checkbox-item">[&nbsp;]&nbsp;ครบ</div>
                        <div class="checkbox-item">[&nbsp;]&nbsp;ไม่ครบ</div>
                    </div>
                    <div class="txt-line-detail txt-line-header">
                        ผู้กู้เคยผิดนัดการส่งเงินหรือขาดส่งค่าหุ้นรายเดือนหรือไม่
                    </div>
                    <div class="check-inline">
                        <div class="checkbox-item">[&nbsp;]&nbsp;เคย</div>
                        <div class="checkbox-item">[&nbsp;]&nbsp;ไม่เคย</div>
                    </div>
                    <div class="txt-line-detail txt-line-header">เอกสารถูกต้องครงถ้วน</div>
                    <div class="check-inline">
                        <div class="checkbox-item">[&nbsp;]&nbsp;อนุมัติ</div>
                        <div class="checkbox-item">[&nbsp;]&nbsp;ไม่อนุมัติ</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-middle">
            <div class="txt-line-detail txt-line-header">หลักเกณฑ์การกู้ฉุกเฉิน</div>
            <div class="list-condition-block">
                <?php
                    $net = ($salary-($cost_all+$keep_loan));
                    $net_percent = $salary > 0 ? ($net/$salary)*100 : 0;
                ?>
                <div class="txt-line-detail"> [<?php echo @$member_month > 1 ? '/' : '&nbsp;'?>] เป็นสมาชิกตั้งแต่ <?php echo $this->center_function->ConvertToThaiDate($row_member['member_date'], 0, 0); ?> ไม่น้อยกว่า 1 เดือน</div>
                <div class="txt-line-detail"> [<?php echo @$check_salary == 1 ? '/' : '&nbsp;'?>] วงเงินกู้ไม่เกิน <?php echo number_format($incomeX3,2)?> บาท (3 เท่าของเงินได้รายเดือน), ไม่เกิน <?php echo number_format(150000, 2)?></div>
                <div class="txt-line-detail"> [<?php echo @$check_period_first == 1 ? '/' : '&nbsp;'?>] กู้ได้ ชำระงวดแรกแล้ว</div>
                <div class="txt-line-detail"> [<?php echo @$check_fee == 1 ? '/' : '&nbsp;'?>] กู้เพิ่มเติม ผ่อนชำระแล้ว 1 งวด น้อยกว่า 3 งวด ต้องเสียค่าธรรมเนียม 100 บาท</div>
                <div class="txt-line-detail"> [<?php echo @$check_period == 1 ? '/' : '&nbsp;'?>] ขอผ่อนชำระ <?php echo $row_loan['period_amount'] ?> งวด ไม่เกิน <?php echo $period_amount; ?> งวด กรณีกู้ไม่น้อยกว่า 2 เท่าของเงินได้รายได้</div>
                <div class="txt-line-detail"> [<?php echo @$member_retire_month == 1 ? '/' : '&nbsp;'?>] เป็นข้าราชการ ขอผ่อน <?php echo @$row_loan['period_amount'] ?> งวด ไม่เกินอายุราชการที่เหลือคิดเป็น <?php echo $work_month_amount; ?> เดือน</div>
                <div class="txt-line-detail"> [<?php echo @$check_net == 1 ? '/' : '&nbsp;'?>] เงินเดือนคงเหลือสุทธิ <?php echo number_format(@$net, 2); ?> บาท คิดเป็น <?php echo number_format(@$net_percent, 2)?>% ของเงินได้รายเดือน</div>
            </div>
        </div>
        <div class="content-bottom">
            <div class="column-left">
                <div class="sign-box">
                    <div class="sign-item">
                        <div class="start">ลงชื่อ</div>
                        <div class="middle"></div>
                        <div class="end">เจ้าหน้าที่ผู้ตรวจสอบ</div>
                    </div>
                    <div class="sign-item situation">
                        <div class="start">(</div>
                        <div class="middle"></div>
                        <div class="end">)</div>
                    </div>
                </div>
            </div>
            <div class="column-right">
                <div class="sign-box">
                    <div class="sign-item">
                        <div class="start">ลงชื่อ</div>
                        <div class="middle"></div>
                        <div class="end">ผู้ช่วยผู้จักการสหกรณ์ฯ</div>
                    </div>
                    <div class="sign-item situation">
                        <div class="start">(</div>
                        <div class="middle"></div>
                        <div class="end">)และพิจารณาเสนอผู้จัดการฯ</div>
                    </div>
                </div>
                <div class="sign-box">
                    <div class="sign-item">
                        <div class="start">ลงชื่อ</div>
                        <div class="middle"></div>
                        <div class="end">ผู้จัดการสหกรณ์ฯ</div>
                    </div>
                    <div class="sign-item situation">
                        <div class="start">(</div>
                        <div class="middle"></div>
                        <div class="end">)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="line-end-page">
            <hr>
        </div>
        <div class="content-top">
            <div class="paragraph">
                <div class="first-line">
                    <span class="li-item-1">ข้าพเจ้า</span>
                    <span class="li-item-2">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_member['prename_full'].$row_member['firstname_th']." ".$row_member['lastname_th'];?></span>
                    <span class="li-item-3">ได้รับเงินกู้จำนวน</span>
                    <span class="li-item-4">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo " = " .number_format($row_loan['loan_amount'])." = "; ?></span>
                    <span class="li-item-5">บาท</span>
                </div>
                <div class="second-line">
                    <span class="li-item-1">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo " = ".$this->center_function->convert($row_loan['loan_amount']) ." = ";?></span>
                    <span class="li-item-2">ไปเป็นการถูกต้องแล้ว ณ วันที่ </span>
                    <span class="li-item-3">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->center_function->ConvertToThaiDate($row_loan['createdatetime'], 1, 0);?></span>
                </div>
                <div class="third-line">
                    <span>และได้ลงลายมือชื่อในการรับเงินต่อหน้าเจ้าหน้าที่ของสหกรณ์</span>
                </div>
            </div>
        </div>
        <div class="content-bottom">
            <div class="sign-receiver">
                <div class="empty">&nbsp;</div>
                <div class="receiver">
                    <div class="sign-box">
                        <div class="sign-item">
                            <div class="start">ลงชื่อ</div>
                            <div class="middle"></div>
                            <div class="end">ผู้รับเงิน</div>
                        </div>
                        <div class="sign-item situation">
                            <div class="start">(</div>
                            <div class="middle"></div>
                            <div class="end">)</div>
                        </div>
                    </div>
                    <div class="sign-box">
                        <div class="sign-item">
                            <div class="start">ลงชื่อ</div>
                            <div class="middle"></div>
                            <div class="end">ผู้จ่ายเงิน</div>
                        </div>
                        <div class="sign-item situation">
                            <div class="start">(</div>
                            <div class="middle"></div>
                            <div class="end">)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

