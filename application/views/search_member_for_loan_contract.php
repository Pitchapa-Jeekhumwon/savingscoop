<?php
function getAge($birthday)
{
    $then = strtotime($birthday);
    return (floor((time() - $then) / 31556926));
}
function getRetiredYears($birthday, $retire_age)
{
    $arr_birthday = explode('-', @$birthday);
    $birth_year = @$arr_birthday[0];
    $retired_years = (@$retire_age + @$birth_year) + 543;
    return $retired_years;
}
?>
<style>
    .form-group {
        margin-bottom: 5px;
    }

    label {
        padding-top: 6px;
        text-align: right;
    }

    .modal-content {
        margin: auto;
        margin-top: 7%;
    }

    .hide_error {
        color: inherit;
        border-color: inherit;
    }

</style>
<div class="row">
    <div class="g24-col-xs-24 g24-col-sm-8 g24-col-md-6 g24-col-lg-8 ">
        <label class="g24-col-lg-7 control-label" for="form-control-2">รหัสสมาชิก</label>
        <div class="g24-col-lg-17">
            <div class="form-group">
                <div class="input-group" >
                    <input id="form-control-2" class="form-control member_id" type="text"  value="<?php echo @$member_id ?>" onkeypress="check_member_id();">
                    <span class="input-group-btn">
                        <a data-toggle="modal" data-target="#myModal" id="test" class="fancybox_share fancybox.iframe" href="#">
                            <button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="g24-col-xs-24 g24-col-sm-8 g24-col-md-6 g24-col-lg-8">
        <label class="g24-col-lg-7 control-label " for="form-control-2">ชื่อ-สกุล</label>
        <div class="g24-col-lg-17">
            <div class="form-group">
                <input class="form-control full_name" type="text" value="<?php echo @$fullname ?>" readonly>
            </div>
        </div>
    </div>
    <div class="g24-col-xs-24 g24-col-sm-8 g24-col-md-6 g24-col-lg-8">
        <label class="g24-col-lg-7 control-label" for="form-control-2">ตำแหน่ง</label>
        <div class="g24-col-lg-17">
            <div class="form-group">
                <input id="position_name" class="form-control " type="text" value="<?php echo @$position ?>" readonly>
            </div>
        </div>
    </div>
    <div class="g24-col-xs-24 g24-col-sm-8 g24-col-md-6 g24-col-lg-8">
        <label class="g24-col-lg-7 control-label " for="form-control-2">ประเภทสมาชิก</label>
        <div class="g24-col-lg-17">
            <div class="form-group">
                <?php if (@$apply_type_id == 2) { ?>
                    <input class="form-control " type="text" value="สมทบ" readonly>
                <?php } else { ?>
                    <input class="form-control " type="text" value="ปกติ" readonly>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="g24-col-xs-24 g24-col-sm-8 g24-col-md-6 g24-col-lg-8">
        <label class="g24-col-lg-7 control-label " for="form-control-2">วันที่เป็นสมาชิก</label>
        <?php $apply_yy_mm = "(" . $this->center_function->cal_age(@$apply_date) . " ปี " . $this->center_function->cal_age(@$apply_date, 'm') . " เดือน)"; ?>
        <div class="g24-col-lg-17">
            <div class="form-group">
                <?php if (!empty($apply_date)) { ?>
                    <input class="form-control " type="text" value="<?php echo $this->center_function->mydate2date(empty($apply_date) ? date("Y-m-d") : $apply_date); ?>  <?php echo @$apply_yy_mm; ?>" readonly>
                <?php } else { ?>
                    <input class="form-control " type="text" value="" readonly>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="g24-col-xs-24 g24-col-sm-8 g24-col-md-6 g24-col-lg-8">
        <label class="g24-col-lg-7 control-label">หน่วยงาน</label>
        <div class="g24-col-lg-17">
            <div class="form-group">
                <input class="form-control" type="text" value="<?php echo @$mem_group_name; ?>" readonly>
            </div>
        </div>
    </div>
    <div class="g24-col-xs-24 g24-col-sm-8 g24-col-md-6 g24-col-lg-8" style="/*padding-right: 0px !important;*/">
        <label class="g24-col-lg-7 control-label " for="form-control-2">สถานะ</label>
        <div class="g24-col-lg-17">
            <div class="form-group">
                <?php if (@$mem_type == 1) { ?>
                    <input class="form-control " type="text" value="ปกติ" readonly>
                <?php } else if (@$mem_type == 2) {
                ?>
                    <input style="color: red" class="form-control " type="text" value="ลาออก <?= $this->center_function->mydate2date(@$str_status) ?>" readonly>
                <?php } else if (@$mem_type == 3) { ?>
                    <input class="form-control " type="text" value="รออนุมัติ" readonly>
                <?php } else if (@$mem_type == 4) { ?>
                    <input class="form-control " type="text" value="ประนอมหนี้" readonly>
                <?php } else if (@$mem_type == 5) { ?>
                    <input class="form-control " type="text" value="โอนหุ้นตัดหนี้" readonly>
                <?php } else if (@$mem_type == 7) { ?>
                    <input class="form-control " type="text" value="รอโอนย้าย" readonly>
                <?php } else if (@$mem_type == 8) { ?>
                    <input class="form-control " type="text" value="รอส่งบำนาญ" readonly>
                <?php } else if (@$mem_type == 9) { ?>
                    <input class="form-control " type="text" value="ไม่หักไปที่เงินเดือน" readonly>
                <?php } else { ?>
                    <input class="form-control " type="text" value="" readonly>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="g24-col-xs-24 g24-col-sm-8 g24-col-md-6 g24-col-lg-8">
        <label class="g24-col-lg-7 control-label">หุ้น</label>
        <div class="g24-col-lg-17">
            <div class="form-group">
                <input type="text" class="form-control share-collect" id="share-collect-search" value="<?php echo number_format($cal_share) ?>" readonly>
            </div>
        </div>
    </div>
</div>
<script>
    function check_member_id() {
        var member_id = $('.member_id').first().val();
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            $.post(base_url + "save_money/check_member_id",
                {
                    member_id: member_id
                }
                , function (result) {
                    obj = JSON.parse(result);
                    console.log(obj.member_id);
                    mem_id = obj.member_id;
                    if (mem_id != undefined) {
                        document.location.href = '<?php echo base_url(uri_string())?>?'+btoa('member_id=' + mem_id)
                    } else {
                        swal('ไม่พบรหัสสมาชิกที่ท่านเลือก', '', 'warning');
                    }
                });
        }
    }
</script>
