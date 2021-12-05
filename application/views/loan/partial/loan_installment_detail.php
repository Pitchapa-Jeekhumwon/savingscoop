<?php
$total = 0;
$total_of_principal = 0;
$total_of_interest = 0;
?>
<style>
    th{
        border-color: black;
        background-color: #eee ;
        position: sticky;
        top: 0; 
    }
</style>

<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12 ">
        <div class="panel panel-body">
            <div style="max-height:450px; overflow:auto; margin-left:10%;margin-right:10%">
                <table class="table table-view table-center">
                    <thead>
                        <tr>
                            <th style="vertical-align: middle;">งวดที่</th>
                            <th style="vertical-align: middle;">รายเดือน</th>
                            <th style="vertical-align: middle;">ดอกเบี้ย</th>
                            <th style="vertical-align: middle;">เงินต้น</th>
                            <th style="vertical-align: middle;">คงเหลือ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['coop_loan_period'] as $k => $val) :
                            $total +=$val['total_paid_per_month'];
                            $total_of_principal +=$val['principal_payment'];
                            $total_of_interest +=$val['interest'];
                            ?>
                            <tr>
                                <td style="text-align: center;vertical-align: top;"><?= $val['period_count'] ?></td>
                                <td style="text-align: right;vertical-align: top;"><?= number_format($val['total_paid_per_month'], 2) ?></td>
                                <td style="text-align: right;vertical-align: top;"><?= number_format($val['interest'], 2) ?></td>
                                <td style="text-align: right;vertical-align: top;"><?= number_format($val['principal_payment'], 2) ?></td>
                                <td style="text-align: right;vertical-align: top;"><?= number_format($val['outstanding_balance'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding: 1rem;">
                <div class="g24-col-xs-24 g24-col-sm-6 g24-col-md-12 g24-col-lg-8">
                    <label class="g24-col-lg-10 control-label " for="form-control-2">รวมทั้งหมด</label>
                    <div class="g24-col-lg-10">
                        <div class="form-group">
                            <input class="form-control " type="text" value="<?= number_format($total, 2) ?>" readonly="">
                        </div>
                    </div>
                </div>
                <div class="g24-col-xs-24 g24-col-sm-6 g24-col-md-12 g24-col-lg-8">
                    <label class="g24-col-lg-10 control-label" for="form-control-2">รวมดอกเบี้ยทั้งหมด</label>
                    <div class="g24-col-lg-10">
                        <div class="form-group">
                            <input class="form-control " type="text" value="<?= number_format($total_of_interest, 2) ?>" readonly="">
                        </div>
                    </div>
                </div>
                <div class="g24-col-xs-24 g24-col-sm-6 g24-col-md-12 g24-col-lg-8">
                    <label class="g24-col-lg-10 control-label " for="form-control-2">รวมจำนวนเงินกู้ทั้งหมด</label>
                    <div class="g24-col-lg-10">
                        <div class="form-group">
                            <input class="form-control " type="text" value="<?= number_format($total_of_principal, 2) ?>" readonly="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>