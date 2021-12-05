<div class="layout-content">
    <div class="layout-content-body">
        <style>
            .modal-header-alert {
                padding: 9px 15px;
                border: 1px solid #FF0033;
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
                margin: auto;
                margin-top: 7%;
            }

            label {
                padding-top: 7px;
            }

            @media (min-width: 768px) {
                .modal-dialog {
                    width: 700px;
                }
            }

            .form-group {
                margin-bottom: 5px;
            }
        </style>

        <h1 style="margin-bottom: 0">เรียกเก็บประจำเดือนรายคน</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:50px !important; padding-bottom: 50px !important;">

                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-7 control-label right">รหัสสมาชิก</label>
                        <div class="g24-col-sm-3">
                            <div class="input-group">
                                <input id="form-control-2" class="form-control member_id" type="text" value="<?php echo $member_id; ?>" onkeypress="check_member_id();">
                                <span class="input-group-btn">
                                    <a data-toggle="modal" data-target="#myModal" id="test" class="fancybox_share fancybox.iframe" href="#">
                                        <button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-7 control-label right">ชื่อสกุล</label>
                        <div class="g24-col-sm-7">
                            <input id="form-control-2" class="form-control " style="width:100%" type="text" value="<?php echo $member_name; ?>" readonly>
                        </div>
                    </div>
                    <form id="submit_member" method="GET" action="<?php echo base_url(PROJECTPATH . '/finance/finance_all_money_report'); ?>" target="_blank">
                        <input id="member_id" type="hidden" name="member_id" value="<?php echo $member_id; ?>" />
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-7 control-label right"> เดือน </label>
                            <div class="g24-col-sm-3">
                                <select id="month" name="month" class="form-control">
                                    <?php foreach ($month_arr as $key => $value) { ?>
                                        <option value="<?php echo $key; ?>" <?php echo $key == ((int)date('m')) ? 'selected' : ''; ?>><?php echo $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label class="g24-col-sm-1 control-label left"> ปี </label>
                            <div class="g24-col-sm-3">
                                <select id="year" name="year" class="form-control">
                                    <?php for ($i = ((date('Y') + 543) - 5); $i <= ((date('Y') + 543) + 5); $i++) { ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i == (date('Y') + 543) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-7 control-label right"></label>
                            <div class="g24-col-sm-7">
                                <input type="button" class="btn btn-primary" style="width:100%" value="เรียกเก็บประจำเดือนรายคน" onclick="popup()">
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ข้อมูลสมาชิก</h4>
            </div>
            <div class="modal-body">
                <div class="input-with-icon">
                    <div class="row">
                        <div class="col">
                            <label class="col-sm-2 control-label">รูปแบบค้นหา</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select id="search_list" name="search_list" class="form-control m-b-1">
                                        <option value="">เลือกรูปแบบค้นหา</option>
                                        <option value="member_id">รหัสสมาชิก</option>
                                        <option value="id_card">หมายเลขบัตรประชาชน</option>
                                        <option value="firstname_th">ชื่อสมาชิก</option>
                                        <option value="lastname_th">นามสกุล</option>
                                    </select>
                                </div>
                            </div>
                            <label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input id="search_text" name="search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>">
                                        <span class="input-group-btn">
                                            <button type="button" id="member_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bs-example" data-example-id="striped-table">
                    <table class="table table-striped">
                        <tbody id="result_member">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>
<!-- modalแสดงทำรายการสำเร็จ-->
<div class="modal fade" id="alert_show" role="dialog">
    <div class="modal-dialog" style="width: 420px; ">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <br>
                    <span style="color: green;" class="icon icon-check icon-5x"></span>
                    <h3 style="font-size:22px;color: green; "> ทำรายการสำเร็จ </h3>
                    <p></p>
                    <div class="m-t-lg">
                        <button class="btn btn-primary" style="width: 150px;" data-dismiss="modal" type="button" onclick="get_finance_month_member_detail()">แสดงรายการเรียกเก็บ</button>
                        <button class="btn btn-default" data-dismiss="modal" type="button">ปิด</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ปิดmodalแสดงทำรายการสำเร็จ-->
<!-- modalแสดงรายการเรียกเก็บ -->
<div class="modal fade" id="get_finance_detail_modal" role="dialog">
    <div class="modal-dialog modal-dialog-file">
        <div class="modal-content data_modal">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">รายการเรียกเก็บ</h3>
            </div>
            <div class="modal-body">
                <div class="row m-b-1">
                    <div class="col-sm-12">
                        <h4 class="modal-title">รหัสสมาชิก <span id="member_id_show"></span> เดือน <span id="month_show"></span> ปี <span id="year_show"></span></h4>
                        <table class="table table-bordered table-striped table-center">
                            <thead>
                                <tr class="bg-primary">
                                    <th width="80">ลำดับ</th>
                                    <th>รายการเรียกเก็บ</th>
                                    <th width="150">ยอดเงินเรียกเก็บ</th>
                                </tr>
                            </thead>
                            <tbody id="table_data_debt">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ปิดmodalแสดงรายการเรียกเก็บ -->
<!-- modalทำรายการซ้ำ -->
<div class="modal fade" id="alert_show_repeatedly" role="dialog">
    <div class="modal-dialog" style="width: 400px;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <br>
                    <span class="text-danger icon icon-times-circle icon-5x"></span>
                    <h3 class="text-danger" style="font-size:22px;">ไม่สามารถเรียกเก็บรายเดือนได้</h3>
                    เนื่องจากมีการประมวลผลผ่านรายการรายเดือน </span> <br> เดือน <span id="month_show_swl"></span> ปี <span id="year_show_swl"></span> แล้ว
                    <p></p>
                    <div class="m-t-lg">
                        <button class="btn btn-primary" data-dismiss="modal" type="button">ตกลง</button>
                        <button class="btn btn-default" data-dismiss="modal" type="button">ยกเลิก</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ปิดmodalทำรายการซ้ำ -->
<script>
    $('#member_search').click(function() {
        if ($('#search_list').val() == '') {
            swal('กรุณาเลือกรูปแบบค้นหา', '', 'warning');
        } else if ($('#search_text').val() == '') {
            swal('กรุณากรอกข้อมูลที่ต้องการค้นหา', '', 'warning');
        } else {
            $.ajax({
                url: base_url + "ajax/search_member_by_type",
                method: "post",
                data: {
                    search_text: $('#search_text').val(),
                    search_list: $('#search_list').val()
                },
                dataType: "text",
                success: function(data) {
                    $('#result_member').html(data);
                },
                error: function(xhr) {
                    console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                }
            });
        }
    });

    function check_member_id() {
        var member_id = $('.member_id').first().val();
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            $.post(base_url + "save_money/check_member_id", {
                member_id: member_id
            }, function(result) {
                obj = JSON.parse(result);
                mem_id = obj.member_id;
                if (mem_id != undefined) {
                    document.location.href = '<?php echo base_url(uri_string()) ?>?member_id=' + mem_id
                } else {
                    swal('ไม่พบรหัสสมาชิกที่ท่านเลือก', '', 'warning');
                }
            });
        }
    }

    function popup() {
        if ($('#member_id').val() == '') {
            swal("กรุณาเลือกหมายเลขสมาชิก");
            return false;
        }
        swal({
                title: "ยืนยันการทำรายการเรียกเก็บประจำเดือนรายคน",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {

                if (isConfirm) {
                    return new Promise(resolve => {
                        var month_swl = $("#month option:selected").text()
                        var month = $("#month").val();
                        var year = $("#year").val();
                        var member_id = $("#member_id").val();
                        var limit = 50;
                        $.ajax({
                            method: 'GET',
                            url: base_url + 'finance/finance_all_money_report',
                            data: {
                                month: month,
                                year: year,
                                member_id: member_id,

                            },
                            success: function(msg) {
                                var data = JSON.parse(msg);

                                if (data["results"] == "true") {
                                    $('#alert_show').modal('show');
                                }
                                if (data["results"] == "false") {
                                    $('#alert_show_repeatedly').modal('show');
                                    $('#year_show_swl').html(year);
                                    $('#month_show_swl').html(month_swl);
                                }
                            },

                        });
                    });

                }
            });
    }

    function get_finance_month_member_detail() {
        //  var month = $("#month").val();
        var month = $("#month option:selected").text()
        //  console.log(month)
        var year = $("#year").val();
        var member_id = $("#member_id").val();
        var month_show = $("#month").val();
        $.ajax({
            url: base_url + "/finance/get_finance_month_member_detail",
            method: "post",
            data: {
                member_id: member_id,
                profile_month: month_show,
                profile_year: year,
            },
            dataType: "text",
            success: function(data) {
                console.log(data);
                $('#table_data_debt').html(data);
                $('#member_id_show').html(member_id);
                $('#year_show').html(year);
                $('#month_show').html(month);
                $('#get_finance_detail_modal').modal('show');
            }
        });
    }
</script>