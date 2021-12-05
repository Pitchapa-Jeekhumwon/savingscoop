<?php
//set date
$year = @$_GET['year'] ? $_GET['year'] : date('Y');
$month = @$_GET['month'] ? $_GET['month'] : date('n');
$full_date = $year . "-" . str_pad($month, 2, '0', 0) . "-01";
?>
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

            .btnp {
                font-family: upbean;
                font-size: 18px;
                width: 40px;
                height: 34px;
                padding-top: 2px;
                font-weight: normal !important;
                border-radius: 3px !important;
            }

            label {
                padding-top: 7px;
            }
            .pd{
                padding-bottom: 10px;
            }

        </style>
        <style type="text/css">
            .form-group {
                margin-bottom: 5px;
            }
        </style>
        <h1 style="margin-bottom: 0">นำเข้าข้อมูลหักบัญชีเงินฝากเพื่อซื้อหุ้น</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <form action="<?php echo base_url(PROJECTPATH . '/import_deposit_monthly/file_save'); ?>" id="import_excel" method="POST" enctype="multipart/form-data">
                        <div class="form-group g24-col-sm-24">
                            <div class="g24-col-sm-5 right">
                                <h3>แนบไฟล์ EXCEL</h3>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-9 control-label text-right">เลือกไฟล์</label>
                            <div class="g24-col-sm-4">
                                <input id="file" name="file" class="form-control" type="file" value="">
                            </div>
                            <div class="g24-col-md-3">
                            <button class="btn btn-primary btn-after-input" type="button" onclick="check_submit_import()" id="submit-btn">
                                <span><i class="fa fa-cloud-upload"></i> อัพโหลด</span>
                            </button>
                        </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-12">
        <div class="panel panel-body" style="padding-top:0px !important;">
            <h3>ไฟล์อัพโหลดข้อมูล ATM</h3>
            <table class="table table-bordered table-striped table-center">
                <thead class="bg-primary">
                <tr>
                    <th width="5%">ลำดับ</th>
                    <th width="25%">ชื่อไฟล์</th>
                    <th width="10%">สถานะ</th>
                    <th width="20%">วันที่อัพโหลด</th>
                    <th width="20%">วันที่ทำรายการ</th>
                    <th width="30%">เครื่องมือ</th>
                </tr>
                </thead>
                <tbody>
                <?php if (isset($data) && sizeof($data)) {
                    $no = 0;
                    foreach ($data as $key => $val){
                        ?>
                        <tr>
                            <td><?php echo ++$no;?></td>
                            <td><?php echo $val['file_name']; ?></td>
                            <td><?php echo $val['status'] == 1 ? 'ประมวลผลแล้ว' : 'รอประมวลผล' ?></td>
                            <td><?php echo $this->center_function->ConvertToThaiDate( $val['date_data'], 1); ?></td>
                            <td><?php echo $this->center_function->ConvertToThaiDate( $val['submit_date'], 1); ?></td>
                            <td>
                                <button class="btn btn-circle btn-primary btnp" <?php echo !empty($val['date_data']) ? '' : 'disabled="disabled"' ?> onclick="view_receive_file(<?php echo $val['id']; ?>)" title="แสดงข้อมูล">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>
                                <button class="btn btn-circle btn-primary btnp" <?php echo $val['status'] == 1 ? 'disabled="disabled"' : '' ?> onclick="show_form_return_manual1()" title="ประมวลผลข้อมูล">
                                    <i class="fa fa-play" aria-hidden="true"></i>
                                </button>
                                <button class="btn btn-circle btn-primary btnp" <?php echo $val['receipt_status'] == 1 ? 'disabled="disabled"' : '' ?>  onclick="show_form_receipt()" title="ออกใบเสร็จ">
                                    <i class="icon icon-print" aria-hidden="true"></i>
                                </button>
                                <button class="btn btn-circle btn-primary btnp" <?php echo !empty($val['date_data']) ? '' : 'disabled="disabled"' ?>  onclick="del_file(<?php echo $val['id']; ?>)" title="ลบไฟล์">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    <?php }} else { ?>
                    <tr>
                        <td colspan="6" class="text-center"> ไม่พบข้อมูล</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_receive_check_list" role="dialog">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header modal-header-info">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">ตรวจสอบข้อมูลหักบัญชีเงินฝากเพื่อซื้อหุ้น</h3>
            </div>
            <div class="modal-body">
                <table class="table" id="verify_list">
                    <thead>
                        <tr>
                            <th class="text-center">ลำดับ</th>
                            <th class="text-center">เลขที่สมาชิก</th>
                            <th class="text-center">เลขที่บัญชีเงินฝาก</th>
                            <th class="text-center">จำนวนเงิน(ซื้อหุ้น)</th>
                            <th class="text-center">ข้อมูลวันที่</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4">ไม่พบรายการ</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer text-center">
                <button class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_select_date" role="dialog">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header modal-header-info">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">ตรวจสอบข้อมูลหักบัญชีเงินฝากเพื่อซื้อหุ้น</h3>
            </div>
            <div class="modal-body">
                <table class="table" id="verify_list">
                    <thead>
                    <tr>
                        <th class="text-center">ลำดับ</th>
                        <th class="text-center">เลขที่สมาชิก</th>
                        <th class="text-center">เลขที่บัญชีเงินฝาก</th>
                        <th class="text-center">จำนวนเงิน(ซื้อหุ้น)</th>
                        <th class="text-center">ข้อมูลวันที่</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="4">ไม่พบรายการ</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer text-center">
                <button class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-form-return-manual1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content pd">
            <div class="modal-header modal-header-info"><h4 class="modal-title">กรุณาเลือกวันที่ประมวลผล</h4></div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <label class="control-label col-xs-6 col-sm-5 text-right">วันที่</label>
                    <div class="col-xs-6 col-sm-3">
                        <div class="input-with-icon col-sm-12" style="padding: 0px !important;margin-left: 0px !important;margin-right: 0px !important;">
                            <div class="form-group" style="padding: 0px !important;margin-left: 0px !important;margin-right: 0px !important;">
                                <input id="pc_date" name="pc_date" class="form-control" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" autocomplete="off">
                                <span class="icon icon-calendar input-icon m-f-1"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <div class="m-t-lg">
                    <input type="hidden" id="id" value="<?php echo $val['id']; ?>">
                    <button type="button" class="btn btn-primary " id="process" onclick="process()" title="ประมวลผล">ประมวลผล</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-form-receipt" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content pd">
            <div class="modal-header modal-header-info"><h4 class="modal-title">กรุณาเลือกวันที่ออกใบเสร็จ</h4></div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <label class="control-label col-xs-6 col-sm-5 text-right">วันที่</label>
                    <div class="col-xs-6 col-sm-3">
                        <div class="input-with-icon col-sm-12" style="padding: 0px !important;margin-left: 0px !important;margin-right: 0px !important;">
                            <div class="form-group" style="padding: 0px !important;margin-left: 0px !important;margin-right: 0px !important;">
                                <input id="rc_date" name="rc_date" class="form-control" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" autocomplete="off">
                                <span class="icon icon-calendar input-icon m-f-1"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <div class="m-t-lg">
                    <input type="hidden" id="id" value="<?php echo $val['id']; ?>">
                    <button type="button" class="btn btn-primary " id="print_receipt" onclick="print_receipt()" title="ออกใบเสร็จ">ออกใบเสร็จ</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function show_form_return_manual1() {
        $('#modal-form-return-manual1').modal('show');
    }
    $(document).ready(function() {
        $("#pc_date").datepicker({
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
            autoclose: true
        })
    });

    function show_form_receipt() {
        $('#modal-form-receipt').modal('show');
    }
    $(document).ready(function() {
        $("#rc_date").datepicker({
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
            autoclose: true
        })
    });

    var base_url = $('#base_url').attr('class');
 
    function check_submit_import() {
        var filename = $("#file").val();
        if (filename == '') {
            swal('กรุณาแนบไฟล์ EXCEL', '', 'warning');
            return false;
        }
        swal({
                title: "ยืนยันการนำเข้าข้อมูล",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: false,
                closeOnCancel: true
            },

            function(isConfirm) {
                if (isConfirm) {
                    var params = new FormData($("#import_excel")[0]);
                    $.ajax({
                        type: "POST",
                        url: $("#import_excel").prop("action"),
                        data: params,
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(msg) {
                            data = jQuery.parseJSON(msg);
                            if (data["result"] == "true") {
                                swal({
                                    title: 'นำเข้าข้อมูลสำเร็จ',
                                    type: "success",
                                    showConfirmButton: false,
                                    timer: 1500,
                                }, function() {
                                    location.reload();

                                });
                            } else
                            if (data["result"] == "null") {
                                swal({
                                    title: 'ไฟล์ที่แนบเข้ามาซ้ำ',
                                    type: "error",
                                    showConfirmButton: true,

                                });
                            }
                        }
                    });
                }
            });
    }
    $(document).on('change', "#month, #year", function() {
        blockUI();
        $("#form1").submit();
    });

    function view_receive_file(id) {
        const table = $("#verify_list");
        const body = table.find('tbody');
        blockUI();
        $.get(base_url + 'import_deposit_monthly/show_data_file', {
            file_id: id
        }, function(xhr) {
            unblockUI();
            body.html(xhr);
            $('#modal_receive_check_list').modal('toggle');
        });
    }
    function process(){
        swal({
                title: "",
                text: "คุณต้องการประมวลผลไฟล์นี้ใช่หรือไม่",
                type: "warning",
                confirmButtonClass: "btn-danger",
                confirmButtonText: "ตกลง",
                cancelButtonText: "ยกเลิก",
                showCancelButton: true,
                closeOnConfirm: true,
                showLoaderOnConfirm: true
    }, function(isConfirm) {
        var id = $('#id').val();
        var date = $('#pc_date').val();

        console.log(id);
        console.log(date);



            if(isConfirm) {
                    $.post(base_url+"import_account/run_script", {id : id,date :date},function (res) {
                        console.log(res);
                        swal("เสร็จสิ้น!", "", "success");
                        window.location.reload();
                    });
                }else{
                    swal("ยกเลิกแล้ว", "", "success");
                }
            });
    };
    function print_receipt(){
        swal({
            title: "",
            text: "คุณต้องการออกใบเสร็จไฟล์นี้ใช่หรือไม่",
            type: "warning",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "ตกลง",
            cancelButtonText: "ยกเลิก",
            showCancelButton: true,
            closeOnConfirm: true,
            showLoaderOnConfirm: true
        }, function(isConfirm) {
            var id = $('#id').val();
            var date = $('#rc_date').val();

            console.log(id);
            console.log(date);

            if(isConfirm) {
                $.post(base_url+"import_account/run_receipt_script", {id : id,date :date},function (res) {
                    console.log(res);
                    swal("เสร็จสิ้น!", "", "success");

                    //window.location.reload();
                });
            }else{
                swal("ยกเลิกแล้ว", "", "success");
            }
        });
    };

    // function del_file(){
    //     swal({
    //         title: "",
    //         text: "คุณต้องการลบไฟล์นี้ใช่หรือไม่",
    //         type: "warning",
    //         confirmButtonClass: "btn-danger",
    //         confirmButtonText: "ตกลง",
    //         cancelButtonText: "ยกเลิก",
    //         showCancelButton: true,
    //         closeOnConfirm: true,
    //         showLoaderOnConfirm: true
    //     }, function(isConfirm) {
    //         var id = $('#id').val();
    //
    //         console.log(id);
    //
    //         if(isConfirm) {
    //             $.post(base_url+"import_account/delete_file", {id : id},function (res) {
    //                 console.log(res);
    //                 swal("เสร็จสิ้น!", "", "success");
    //
    //                 //window.location.reload();
    //             });
    //         }else{
    //             swal("ยกเลิกแล้ว", "", "success");
    //         }
    //     });
    // };

    function del_file(id){
        swal({
                title: 'ท่านต้องการลบข้อมูลใช่หรือไม่',
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    document.location.href = base_url+'import_account/delete_file/'+id;
                } else {

                }
            });
    }



</script>