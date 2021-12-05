<div class="layout-content">
    <div class="layout-content-body">
        <h1 style="margin-bottom: 0;margin-top: 0">ไฟล์ข้อมูลวงเงินฝาก ATM</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <h3 style="margin-top: 0px !important;">ดาวน์โหลดข้อมูลวงเงินฝาก ATM</h3>
                    <div class="col-sm-12">
                        <div class="col-sm-12 form-group text-center">
                            <div class="col-sm-4"></div>
                            <div class="col-sm-5">
                            <label class="control-label col-sm-2">เลือกวันที่:</label>
                            <div class="col-sm-6">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <div id="form_acc_id" class="form-group input-group">
                                            <input id="file_date" name="file_date" class="form-control m-b-1 mydate"
                                                   style="padding-left: 50px;" type="text" data-date-language="th-th"
                                                   value="<?php echo $this->center_function->mydate2date(date("Y-m-d")); ?>"/>
                                            <span class="icon icon-calendar input-icon m-f-1"></span>
                                            <span class="input-group-btn">
                                            <a class="" href="#">
                                                <button id="edit_opn_date" type="button"class="btn btn-info btn-search"><span
                                                            class="icon icon-edit"></span></button>
                                            </a>
								        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-12 form-group text-center">
                            <button class="btn btn-primary" id="btn_download_transaction">download</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">

    $(document).ready(function () {
        $(".mydate").datepicker({
            prevText: "ก่อนหน้า",
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
    });

    $(document).on("click", '#btn_download_transaction', function(){
        let date = $("#file_date");
        if(typeof date.val() === "undefined" || date.val() === ""){
            swal("กรุณาเลือกวันที่ทำรายการ");
            return false;
        }
        let arr_date = date.val().split("/");
        arr_date[2] = parseInt(arr_date[2]) - 543;
        let _date = arr_date.reverse().join('-');
        window.location.href = base_url+"/deposit_atm/display?date="+_date;
    });
</script>
