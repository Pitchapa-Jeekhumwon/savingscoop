<div class="head">
    <div class="g24-col-xs-24 g24-col-sm-12 g24-col-md-12 g24-col-lg-12">
        <label class="g24-col-lg-8 control-label " for="form-control-2">เลขที่บัญชี</label>
        <div class="g24-col-lg-8">
            <div class="form-group">
                <input class="form-control " type="text" value="<?=$head['member_id']?>" readonly="">
            </div>
        </div>
    </div>
    <div class="g24-col-xs-24 g24-col-sm-12 g24-col-md-12 g24-col-lg-12">
        <label class="g24-col-lg-8 control-label " for="form-control-2">อัตราดอกเบี้ย</label>
        <div class="g24-col-lg-8">
            <div class="form-group">
                <input class="form-control " type="text" value="<?=number_format($head['interest_per_year'],2)?> %" readonly="">
            </div>
        </div>
    </div>
    <div class="g24-col-xs-24 g24-col-sm-12 g24-col-md-12 g24-col-lg-12">
        <label class="g24-col-lg-8 control-label " for="form-control-2">จำนวนงวด</label>
        <div class="g24-col-lg-8">
            <div class="form-group">
                <input class="form-control " type="text" value="<?=$head['period_amount']?>" readonly="">
            </div>
        </div>
    </div>
    <div class="g24-col-xs-24 g24-col-sm-12 g24-col-md-8 g24-col-lg-12">
        <label class="g24-col-lg-8 control-label " for="form-control-2">จำนวนเงินกู้</label>
        <div class="g24-col-lg-8">
            <div class="form-group">
                <input class="form-control " type="text" value="<?=number_format($head['loan_amount'],2)?>" readonly="">
            </div>
        </div>
    </div>
    <div class="g24-col-xs-24 g24-col-sm-12 g24-col-md-12 g24-col-lg-12">
        <label class="g24-col-lg-12 control-label " for="form-control-2">วันที่เริ่มชำระงวดแรก</label>
        <div class="g24-col-lg-8">
            <div class="form-group">
                <input class="form-control " type="text" value="<?=$head['createdatetime']?>" readonly="">
            </div>
        </div>
    </div>
</div>
<div>
    <table class="table table-view table-center">
        <thead>
            <tr>
                <th style="vertical-align: middle;">งวดที่</th>
                <th style="vertical-align: middle;">รายเดือน</th>
                <th style="vertical-align: middle;">ดอกเบี้ย</th>
                <th style="vertical-align: middle;">เงินต้น</th>
                <th style="vertical-align: middle;">คงเหลือ</th>
                <th style="vertical-align: middle;">หุ้นสะสม</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['coop_loan_period'] as $k => $val) : ?>
                <tr>
                    <td style="text-align: center;vertical-align: top;"><?= $val['period_count'] ?></td>
                    <td style="text-align: right;vertical-align: top;"><?= number_format($val['total_paid_per_month'],2) ?></td>
                    <td style="text-align: right;vertical-align: top;"><?= number_format($val['interest'],2) ?></td>
                    <td style="text-align: right;vertical-align: top;"><?= number_format($val['principal_payment'],2) ?></td>
                    <td style="text-align: right;vertical-align: top;"><?= number_format($val['outstanding_balance'],2) ?></td>
                    <td style="text-align: right;vertical-align: top;"></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>