<style type="text/css">
    .information{
        padding-top: 25px;
    }
</style>
<div class="information">
    <div class="g24-col-sm-24">
        <div class="form-group g24-col-sm-8">
            <label class="g24-col-sm-8 control-label" for="age">อายุสมาชิก</label>
            <div class="g24-col-sm-16">
                <input class="form-control text-right" value="<?php echo @$age; ?>" id="age" disabled="disabled">
            </div>
        </div>
        <div class="form-group g24-col-sm-8">
            <label class="g24-col-sm-8 control-label" for="share_month">หุ้นรายเดือน</label>
            <div class="g24-col-sm-16">
                <input class="form-control text-right" value="<?php echo number_format(@$share_month, 2);?>" id="share_month" disabled="disabled">
            </div>
        </div>
    </div>
    <div class="g24-col-sm-24">
        <div class="form-group g24-col-sm-8">
            <label class="g24-col-sm-8 control-label" for="member_age">อายุการเป็นสมาชิก</label>
            <div class="g24-col-sm-16">
                <input class="form-control text-right" value="<?php echo @$member_age; ?>" id="member_age" disabled="disabled">
            </div>
        </div>
        <div class="form-group g24-col-sm-8">
            <label class="g24-col-sm-8 control-label" for="share_month_status">สถานะงดหุ้น</label>
            <div class="g24-col-sm-16">
                <input class="form-control text-right" value="<?php echo $share_month_status ?>" id="share_month_status" disabled="disabled">
            </div>
        </div>
    </div>
    <div class="g24-col-sm-24">
        <div class="form-group g24-col-sm-offset-8 g24-col-sm-8">
            <label class="g24-col-sm-8 control-label" for="share_collect_value">หุนเรือนหุ้น</label>
            <div class="g24-col-sm-16">
                <input class="form-control text-right" value="<?php echo number_format(@$cal_share, 2) ?>" id="share_collect_value" disabled="disabled">
            </div>
        </div>
    </div>
</div>