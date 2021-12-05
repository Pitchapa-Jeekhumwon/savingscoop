<div class="layout-content">
    <div class="layout-content-body">
        <?php
        $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        ?>
        <style>
            .modal-header-alert {
                padding:9px 15px;
                border:1px solid #FF0033;
                background-color: #FF0033;
                color: #fff;
                -webkit-border-top-left-radius: 5px;
                -webkit-border-top-right-radius: 5px;
                -moz-border-radius-topleft: 5px;
                -moz-border-radius-topright: 5px;
                border-top-left-radius: 5px;
                border-top-right-radius: 5px;
            }
            .center {
                text-align: center;
            }
            .right {
                text-align: right;
            }
            .modal-dialog-account {
                margin:auto;
                margin-top:7%;
            }
            label{
                padding-top:7px;
            }
        </style>

        <style type="text/css">
            .form-group{
                margin-bottom: 5px;
            }
        </style>
        <h1 style="margin-bottom: 0">รายงานการคืนเงินรายเดือนตามการข่องทางการคืนเงิน</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <form action="<?php echo base_url(PROJECTPATH.'/report_process_return_data/coop_report_return_pay_type_preview'); ?>" id="form1" method="GET" target="_blank">
                        <h3></h3>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> วันที่ </label>
                            <div class="g24-col-sm-4">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                            <label class="g24-col-sm-1 control-label right"> ถึง </label>
                            <div class="g24-col-sm-4">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="end_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> รูปแบบหน่วยงาน </label>
                            <div class="g24-col-sm-4">
                                <select name="type_department" id="type_department" onchange="" class="form-control">
                                    <option value="">เลือกรูปแบบหน่วยงาน</option>
                                    <option value="1">หน่วยงานหลัก</option>
                                    <option value="2">หน่วยงานย่อย</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right show_department"> สังกัดหน่วยงาน </label>
                            <div class="g24-col-sm-4 show_department">
                                <select name="department" id="department" onchange="change_mem_group('department', 'faction')" class="form-control">
                                    <option value="">เลือกข้อมูล</option>
                                    <?php
                                    foreach($row_mem_group as $key => $value){
                                        ?>
                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['mem_group_name']; ?></option>
                                        <?php
                                    } ?>
                                </select>
                            </div>
                            <label class="g24-col-sm-1 control-label right show_level"> หน่วยงานรอง </label>
                            <div class="g24-col-sm-4 show_level">
                                <select name="faction" id="faction" onchange="change_mem_group('faction','level')" class="form-control">
                                    <option value="">เลือกข้อมูล</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24 show_level">
                            <label class="g24-col-sm-6 control-label right"> หน่วยงานย่อย </label>
                            <div class="g24-col-sm-4">
                                <select name="level" id="level" class="form-control">
                                    <option value="">เลือกข้อมูล</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right">ช่องทางชำระเงิน</label>
                            <div class="col-xs-6 col-sm-3">
                                <label class="radio-inline">
                                    <input type="radio" id="pay_type_non_pay" name="pay_type" value=""> ทั้งหมด
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="pay_type_cash" name="pay_type" checked="" value="0"> เงินสด
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="pay_type_hold" name="pay_type" value="2"> ชำระเงินกู้อื่น
                                </label>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right">&nbsp;&nbsp;</label>
                            <div class="col-xs-6 col-sm-3">
                                <label class="radio-inline">
                                    <input type="radio" id="pay_type_transfer" name="pay_type" value="1"> เงินโอน
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="pay_type_non_pay" name="pay_type" value="3"> เก็บเงินไม่ได้
                                </label>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-5 control-label right"></label>
                            <div class="g24-col-sm-6">
                                <input type="button" class="btn btn-primary" style="width:100%" value="รายงานการคืนเงินรายเดือนตามการข่องทางการคืนเงิน" data-type="preview"  onclick="check_empty('preview')">
                            </div>
                            <div class="g24-col-sm-4">
                                <input type="button" class="btn btn-default" style="width:100%" value="Export Excel" data-type="excel"  onclick="check_empty('excel')">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $( document ).ready(function() {
        $(".show_department").hide();
        $(".show_level").hide();
        $("#type_department").change(function() {
            var type_department = $(this).val();
            if(type_department == '1'){
                $(".show_department").show();
                $(".show_level").hide();
            }else if(type_department == '2'){
                $(".show_department").show();
                $(".show_level").show();
            }

            //alert($(this).val());
        });


        $(".mydate").datepicker({
            prevText : "ก่อนหน้า",
            nextText: "ถัดไป",
            currentText: "Today",
            changeMonth: true,
            changeYear: true,
            isBuddhist: true,
            monthNamesShort: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
            dayNamesMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
            constrainInput: true,
            dateFormat: "dd/mm/yy",
            yearRange: "c-50:c+10",
            autoclose: true,
        });


        $(".type_item").click(function() {
            $(this).parents("div").children(".type_item").prop("checked", true);
            $(this).parent("div").find(".type_item").prop("checked", $(this).prop("checked"));
        });

        $(".type_item").click(function() {
            $(this).parents("div").children(".type_item").prop("checked", true);
            $(this).parent("div").find(".type_item").prop("checked", $(this).prop("checked"));
        });

        $("#mem_type_all").change(function() {
            if($("#mem_type_all").attr('checked') == "checked"){
                $('.type_item').prop('checked', true)
            } else {
                $('.type_item').prop('checked', false)
            }
        });
        $(".type_item").change(function() {
            if($(this).attr('checked') != "checked"){
                $('#mem_type_all').prop('checked', false)
            }
        });
    });
    function change_mem_group(id, id_to){
        var mem_group_id = $('#'+id).val();
        $('#level').html('<option value="">เลือกข้อมูล</option>');
        $.ajax({
            method: 'POST',
            url: base_url+'manage_member_share/get_mem_group_list',
            data: {
                mem_group_id : mem_group_id
            },
            success: function(msg){
                $('#'+id_to).html(msg);
            }
        });
    }
    function check_empty(type){
        var type_id = $('#type_department').val();
        if(type_id == ''){
            swal("กรุณาเลือกรูปแบบหน่วยงาน");
            return false;
        }
        var type_item_check = false;
        $(".type_item").each(function( index ) {
            if($(this).attr('checked') == "checked"){
                type_item_check = true;
            }
        });

        $.blockUI({
            message: 'กรุณารอสักครู่...',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .5,
                color: '#fff'
            },
            baseZ: 2000,
            bindEvents: false
        });
        $.ajax({
            success:function(data){
                $.unblockUI();
                console.log(type)
                if(type == 'preview') {
                    $('#form1').attr('action', base_url+'report_process_return_data/coop_report_return_pay_type_preview');
                    $('#form1').submit();
                } else if(type == 'excel') {
                    $('#form1').attr('action', base_url+'report_process_return_data/coop_report_return_pay_type_excel');
                    $('#form1').submit();
                }

            }
        });

    }


</script>


