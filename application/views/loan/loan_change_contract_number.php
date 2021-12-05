<div class="layout-content">
    <div class="layout-content-body">
        <style>
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
            th {
                text-align: center;
            }
            .modal-dialog-cal {
                width:80% !important;
                margin:auto;
                margin-top:1%;
                margin-bottom:1%;
            }
            .modal-dialog-search {
                width: 700px;
            }
        </style>
        <h1 style="margin-bottom: 0">เปลี่ยนแปลงเลขที่สัญญาเงินกู้</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">

            </div>

        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <form action="" method="POST">
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label">กรอกเลขสมาชิก</label>
                                <div class="g24-col-sm-3">
                                    <input class="form-control" id="member_id" name="member_id" type="text" value="">
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <h3>เลขที่สัญญาเงินกู้</h3>
                    <form action="" method="POST">
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"> เลขทีสัญญาเดิม</label>
                                <div class="g24-col-sm-3">
                                    <input class="form-control" id="contract_number" name="contract_number" type="text" value="">
                                </div>
                            </div>
                            <div class="g24-col-sm-2">
                                <input type="button" id="contract_number_check" class="btn btn-primary" value="ตรวจสอบ">
                            </div>
                        </div>
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"> เลขทีสัญญาที่ต้องการเปลี่ยนให้เป็น </label>
                                <div class="g24-col-sm-3">
                                    <input class="form-control" id="new_contract_number" name="new_contract_number" type="text" value="">
                                </div>
                            </div>
                        </div>
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"></label>
                                <div class="g24-col-sm-2">
                                    <input type="button" id="submit_btn" class="btn btn-primary" value="ดำเนินการ">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <h3>เลขที่สัญญาเงินกู้ ATM</h3>
                    <form action="" method="POST">
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"> เลขทีสัญญา ATM เดิม</label>
                                <div class="g24-col-sm-3">
                                    <input class="form-control" id="contract_atm_number" name="contract_atm_number" type="text" value="">
                                </div>
                            </div>
                            <div class="g24-col-sm-2">
                                <input type="button" id="contract_atm_number_check" class="btn btn-primary" value="ตรวจสอบ">
                            </div>
                        </div>
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"> เลขทีสัญญา ATM ที่ต้องการเปลี่ยนให้เป็น </label>
                                <div class="g24-col-sm-3">
                                    <input class="form-control" id="new_contract_atm_number" name="new_contract_atm_number" type="text" value="">
                                </div>
                            </div>
                        </div>
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"></label>
                                <div class="g24-col-sm-2">
                                    <input type="button" id="submit_atm_btn" class="btn btn-primary" value="ดำเนินการ">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <h3>เลขที่คำร้องเงินกู้ </h3>
                    <form action="" method="POST">
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"> เลขทีคำร้องเดิม</label>
                                <div class="g24-col-sm-3">
                                    <input class="form-control" id="petition_number" name="petition_number" type="text" value="">
                                </div>
                            </div>
                            <div class="g24-col-sm-2">
                                <input type="button" id="petition_number_check" class="btn btn-primary" value="ตรวจสอบ">
                            </div>
                        </div>
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"> เลขทีคำร้องที่ต้องการเปลี่ยนให้เป็น </label>
                                <div class="g24-col-sm-3">
                                    <input class="form-control" id="new_petition_number" name="new_petition_number" type="text" value="">
                                </div>
                            </div>
                        </div>
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"></label>
                                <div class="g24-col-sm-2">
                                    <input type="button" id="submit_pt_btn" class="btn btn-primary" value="ดำเนินการ">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <h3>เลขที่คำร้องเงินกู้ ATM</h3>
                    <form action="" method="POST">
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"> เลขทีคำร้อง ATM เดิม</label>
                                <div class="g24-col-sm-3">
                                    <input class="form-control" id="petition_number_atm" name="petition_number_atm" type="text" value="">
                                </div>
                            </div>
                            <div class="g24-col-sm-2">
                                <input type="button" id="petition_number_atm_check" class="btn btn-primary" value="ตรวจสอบ">
                            </div>
                        </div>
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"> เลขทีคำร้อง ATM ที่ต้องการเปลี่ยนให้เป็น </label>
                                <div class="g24-col-sm-3">
                                    <input class="form-control" id="new_petition_atm_number" name="new_petition_atm_number" type="text" value="">
                                </div>
                            </div>
                        </div>
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"></label>
                                <div class="g24-col-sm-2">
                                    <input type="button" id="submit_pt_atm_btn" class="btn btn-primary" value="ดำเนินการ">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function() {
        $("#contract_number_check").click(function() {
            if (!$('#member_id').val()) {
                swal('กรุณากรอกเลขสมาชิก','', 'warning');
            }else {
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
                    baseZ: 6000,
                    bindEvents: false
                });
                $.ajax({
                    url: base_url + "loan_contract/check_contract_number",
                    method: "post",
                    data: {
                        contract_number: $('#contract_number').val()
                        ,member_id :$('#member_id').val()
                    },
                    dataType: "json",
                    success: function (result) {
                        data = JSON.parse(JSON.stringify(result));
                        $.unblockUI();
                        if (data.contract_number == null) {
                            swal("ไม่พบเลขที่สัญญานี้");
                        } else {
                            swal("พบเลขที่สัญญานี้ระบบ");
                        }
                    }
                });
            }
        });

        $("#contract_atm_number_check").click(function() {
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
                baseZ: 6000,
                bindEvents: false
            });
            $.ajax({
                url: base_url+"loan_contract/check_contract_atm_number",
                method:"post",
                data: {
                    contract_number : $('#contract_atm_number').val()
                    ,member_id :$('#member_id').val()
                },
                dataType:"json",
                success:function(result) {
                    data = JSON.parse(JSON.stringify(result));
                    $.unblockUI();
                    if(data.contract_number == null) {
                        swal("ไม่พบเลขที่สัญญานี้");
                    }
                    else{
                        swal("พบเลขที่สัญญานี้ระบบ");
                    }
                }
            });
        });

        $("#petition_number_check").click(function() {
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
                baseZ: 6000,
                bindEvents: false
            });
            $.ajax({
                url: base_url+"loan_contract/check_petition_number",
                method:"post",
                data: {
                    petition_number : $('#petition_number').val()
                    ,member_id :$('#member_id').val()
                },
                dataType:"json",
                success:function(result) {
                    data = JSON.parse(JSON.stringify(result));
                    $.unblockUI();
                    if(data.petition_number == null) {
                        swal("ไม่พบเลขที่คำร้องนี้");
                    }
                    else{
                        swal("พบเลขที่คำร้องนี้ระบบ");
                    }
                }
            });
        });
        $("#petition_number_atm_check").click(function() {
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
                baseZ: 6000,
                bindEvents: false
            });
            $.ajax({
                url: base_url+"loan_contract/check_petition_number_atm",
                method:"post",
                data: {
                    petition_number : $('#petition_number_atm').val()
                    ,member_id :$('#member_id').val()
                },
                dataType:"json",
                success:function(result) {
                    data = JSON.parse(JSON.stringify(result));
                    $.unblockUI();
                    if(data.petition_number == null) {
                        swal("ไม่พบเลขที่คำร้องนี้");
                    }
                    else{
                        swal("พบเลขที่คำร้องนี้ระบบ");
                    }
                }
            });
        });

        $("#submit_btn").click(function() {
            if (!$('#member_id').val()) {
                swal('กรุณากรอกเลขสมาชิก','', 'warning');
            }else{

            if (!$('#new_contract_number').val()) {
                swal('ไม่สามารถทำรายการได้','', 'warning');
            } else {
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
                    baseZ: 6000,
                    bindEvents: false
                });
                $.ajax({
                    url: base_url + "loan_contract/change_contract_number",
                    method: "post",
                    data: {
                        contract_number: $('#contract_number').val(),
                        new_contract_number: $('#new_contract_number').val()
                        ,member_id :$('#member_id').val()
                    },
                    dataType: "json",
                    success: function (result) {
                        data = JSON.parse(JSON.stringify(result));
                        $.unblockUI();
                        if (data.status == 1) {
                            swal({
                                title: "ทำรายการสำเร็จ",
                                type: "success",
                                showCancelButton: true,
                                confirmButtonColor: '#DD6B55',
                                cancelButtonText: "ปิดหน้าต่าง",
                                closeOnConfirm: true,
                                closeOnCancel: true

                            });
                            location.reload();
                        } else {
                            swal('ไม่สามารถทำรายการได้','', 'warning');
                            location.reload();
                        }
                    }
                });
            }
        }
        });

        $("#submit_atm_btn").click(function() {
            if (!$('#member_id').val()) {
                swal('กรุณากรอกเลขสมาชิก','', 'warning');
            }else {
                if (!$('#new_contract_atm_number').val()) {
                    swal('ไม่สามารถทำรายการได้', '', 'warning');
                } else {
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
                        baseZ: 6000,
                        bindEvents: false
                    });
                    $.ajax({
                        url: base_url + "loan_contract/change_contract_atm_number",
                        method: "post",
                        data: {
                            contract_number: $('#contract_atm_number').val(),
                            new_contract_number: $('#new_contract_atm_number').val()
                            , member_id: $('#member_id').val()
                        },
                        dataType: "json",
                        success: function (result) {
                            data = JSON.parse(JSON.stringify(result));
                            $.unblockUI();
                            if (data.status == 1) {
                                swal({
                                    title: "ทำรายการสำเร็จ",
                                    type: "success",
                                    showCancelButton: true,
                                    confirmButtonColor: '#DD6B55',
                                    cancelButtonText: "ปิดหน้าต่าง",
                                    closeOnConfirm: true,
                                    closeOnCancel: true

                                });
                                location.reload();
                            } else {
                                swal('ไม่สามารถทำรายการได้', '', 'warning');
                                location.reload();
                            }
                        }
                    });
                }
            }
        });

        $("#submit_pt_btn").click(function() {
            if (!$('#member_id').val()) {
                swal('กรุณากรอกเลขสมาชิก','', 'warning');
            }else {
                if (!$('#new_petition_number').val()) {
                    swal('ไม่สามารถทำรายการได้', '', 'warning');
                } else {
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
                        baseZ: 6000,
                        bindEvents: false
                    });
                    $.ajax({
                        url: base_url + "loan_contract/change_petition_number",
                        method: "post",
                        data: {
                            petition_number: $('#petition_number').val(),
                            new_petition_number: $('#new_petition_number').val()
                            , member_id: $('#member_id').val()
                        },
                        dataType: "json",
                        success: function (result) {
                            data = JSON.parse(JSON.stringify(result));
                            $.unblockUI();
                            if (data.status == 1) {
                                swal({
                                    title: "ทำรายการสำเร็จ",
                                    type: "success",
                                    showCancelButton: true,
                                    confirmButtonColor: '#DD6B55',
                                    cancelButtonText: "ปิดหน้าต่าง",
                                    closeOnConfirm: true,
                                    closeOnCancel: true

                                });
                                location.reload();
                            } else {
                                swal('ไม่สามารถทำรายการได้', '', 'warning');
                                location.reload();
                            }
                        }
                    });
                }
            }
        });

        $("#submit_pt_atm_btn").click(function() {
            if (!$('#member_id').val()) {
                swal('กรุณากรอกเลขสมาชิก','', 'warning');
            }else {
                if (!$('#new_petition_atm_number').val()) {
                    swal('ไม่สามารถทำรายการได้', '', 'warning');
                } else {
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
                        baseZ: 6000,
                        bindEvents: false
                    });
                    $.ajax({
                        url: base_url + "loan_contract/change_petition_atm_number",
                        method: "post",
                        data: {
                            petition_number: $('#petition_number_atm').val(),
                            new_petition_number: $('#new_petition_atm_number').val()
                            , member_id: $('#member_id').val()
                        },
                        dataType: "json",
                        success: function (result) {
                            data = JSON.parse(JSON.stringify(result));
                            $.unblockUI();
                            if (data.status == 1) {
                                swal({
                                    title: "ทำรายการสำเร็จ",
                                    type: "success",
                                    showCancelButton: true,
                                    confirmButtonColor: '#DD6B55',
                                    cancelButtonText: "ปิดหน้าต่าง",
                                    closeOnConfirm: true,
                                    closeOnCancel: true

                                });
                                location.reload();
                            } else {
                                swal('ไม่สามารถทำรายการได้', '', 'warning');
                                location.reload();
                            }
                        }
                    });
                }
            }
        });
    });
</script>
