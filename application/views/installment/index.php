<style type="text/css">

    .title_header {
        font-family: upbean;
        font-size: 24px !important;
        margin-bottom: 8px;
    }

    .padding-line {
        padding-bottom: 8px;
    }
</style>
<div class="layout-content">
    <div class="layout-content-body">
        <h1 class="title_top">อนุมัติการโอนเงินกู้</h1>
        <p style="font-family: upbean; font-size: 20px; margin-bottom:5px;"><?php $this->load->view('breadcrumb'); ?></p>
        <div class="row gutter-xs">
            <div class="panel panel-body" style="padding-top:0px !important;">
                <p class="g24-col-lg-24 title_header">รายละเอียดการกู้</p>
                <div class="g24-col-sm-24 padding-line">
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">รหัสสมาชิก</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="member_id" type="text"
                                   value="<?php echo $member['member_id'] ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">ชื่อ-สกุล</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="full_name_th" type="text"
                                   value="<?php echo $member['full_name_th'] ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">วงเงินที่อนุมัติ</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="loan_amount_balance" type="text"
                                   value="<?php echo number_format($contract['loan_amount'], 2); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="g24-col-sm-24 padding-line">
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">เลขที่สัญญา</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="contract_number" type="text"
                                   value="<?php echo $contract['contract_number']; ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">จำนวนงวดที่ขอกู้</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="loan_amount" type="text"
                                   value="<?php echo number_format($contract['loan_amount'], 2); ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">ชำระต่องวด</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="payment_per_period" type="text"
                                   value="<?php echo number_format($contract['money_period_1'], 2); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="g24-col-sm-24 padding-line">
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">ประเภทการส่งหัก</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="pay_type" type="text"
                                   value="<?php echo $contract['pay_type'] == 1 ? "คงต้น" : "คงยอด"; ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">วันที่เริ่มชำระ</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="date_start_period" type="text"
                                   value="<?php echo $this->center_function->mydate2date($contract['date_start_period']); ?>"
                                   readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">วันที่อนุมัติ</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="approve_date" type="text"
                                   value="<?php echo $this->center_function->mydate2date($contract['approve_date']); ?>"
                                   readonly>
                        </div>
                    </div>
                </div>
                <div class="g24-col-sm-24 padding-line">
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">จำนวนเงินรับจริง</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="real_pay_amount" type="text"
                                   value="<?php echo number_format(array_sum(array_column($installment, "amount")), 2); ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">ยอดเงินคงเหลือ</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="loan_receipt_balance" type="text"
                                   value="<?php echo number_format($contract['loan_amount_balance'] , 2); ?>" readonly>
                        </div>
                    </div>
                    <div class="g24-col-lg-8">
                        <label class="g24-col-sm-10 control-label ">วงเงินกู้คงเหลือ</label>
                        <div class="g24-col-sm-14">
                            <input class="form-control" id="loan_amount_balance_approve" type="text"
                                   value="<?php echo number_format($contract['loan_amount']- array_sum(array_column($installment, "amount")), 2) ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="g24-col-sm-24 padding-line">
                    <div class="g24-col-lg-16">
                        <label class="g24-col-sm-5 control-label ">เหตุผลการกู้</label>
                        <div class="g24-col-sm-19">
                            <input class="form-control" id="reason_loan" type="text"
                                   value="<?php echo $contract['loan_reason'] ?>" readonly>
                        </div>
                    </div>
                </div>
                <p class="g24-col-lg-24 title_header">จัดการงวดจ่ายเงินกู้</p>
                <div class="g24-col-sm-24 padding-line">
                    <div class="g24-col-lg-18">
                        <label class="g24-col-sm-4 control-label">จำนวนงวดสูงสุดที่แบ่งจ่าย</label>
                        <div class="g24-col-sm-4">
                            <div class="input-group" id="group-installing">
                                <input id="installment" type="text" class="form-control m-b-1 text-right" <?php echo  $contract['installment_amount'] == "" ? "" : 'readonly="readonly"';?> value="<?php echo $contract['installment_amount']; ?>">
                                <span class="input-group-btn">
                                <?php if($contract['installment_amount'] == ""){?>
                                    <button id="" type="button" class="btn btn-info btn-search" onclick="btnSave()"><span class="icon icon-save"></span></button>
                                <?php }else{ ?>
                                    <button id="" type="button" class="btn btn-info btn-search" onclick="btnEdit()"><span class="icon icon-pencil"></span></button>
                                <?php } ?>
                                </span>
                            </div>
                        </div>
                        <label class="g24-col-sm-10 control-label text-left" for="installment">
                            <span>งวด</span>
                            <span class="" style="color: red">(**กรุณากรอกจำนวนงวดและบันทึกก่อนทำรายการอนุมัติเงินกู้)</span>
                        </label>
                    </div>
                </div>
                <div class="g24-col-sm-24">
                    <table id="installing" class="g24-col-sm-offset-1 g24-col-sm-21 table table-bordered table-striped table-center">
                        <thead class="bg-primary">
                        <tr>
                            <th style="width: 5%;">ลำดับ</th>
                            <th style="width: 15%;">วันที่</th>
                            <th style="width: 15%;">วงเงินกู้คงเหลือ</th>
                            <th style="width: 25%;">เงินโอนจ่าย</th>
                            <th style="width: 10%;">สถานะ</th>
                            <th style="width: 10%;">ผู้อนุมัติ</th>
                            <th style="width: 20%;">จัดการ</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; if(sizeof(@$installment)){?>
                            <?php foreach ($installment as $key => $value ){ ?>
                                <tr>
                                    <td><span class="line-number"><?php echo $i; ?></span></td>
                                    <td>
                                        <div class="input-with-icon g24-col-sm-24">
                                            <div class="form-group">
                                                <input id="approve_date_<?php echo $i; ?>" name="data[<?php echo $i; ?>]['approve_date']"
                                                       class="form-control m-b-1 datepicker required" style="padding-left: 50px;"
                                                       type="text" value="<?php echo $this->center_function->mydate2date($value['transaction_datetime']); ?>"
                                                       data-date-language="th-th" required title="" readonly="readonly" disabled="disabled">
                                                <span class="icon icon-calendar input-icon m-f-1"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="loan_amount_balance"><? echo number_format($value['balance'], 2); ?></span></td>
                                    <td>
                                        <input type="text" class="form-control m-b-1 text-right calc required" readonly="readonly"
                                               name="data[]['loan_amount_receiver']"
                                               value="<?php echo number_format($value['amount'], 2); ?>">
                                    </td>
                                    <td><?php echo $status[$value['transfer_status']]; ?></td>
                                    <td>
                                        <?php echo $value['user_name'];?>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary required btn-app" type="button" disabled="disabled">
                                            <i class="fa fa-check-circle-o "></i><span> อนุมัติ</span></button>
                                    </td>
                                </tr>
                            <?php $i++; } ?>
                        <?php } ?>
                        <?php if($contract['installment_amount'] >= $i || $contract['installment_amount'] == 0){ ?>
                            <tr>
                                <td><span class="line-number"><?php echo $i++; ?></span></td>
                                <td>
                                    <div class="input-with-icon g24-col-sm-24">
                                        <div class="form-group">
                                            <input id="approve_date_0" name="data[]['approve_date']"
                                                   class="form-control m-b-1 datepicker required" style="padding-left: 50px;"
                                                   type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>"
                                                   data-date-language="th-th" required title="" >
                                            <span class="icon icon-calendar input-icon m-f-1"></span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="loan_amount_balance"><? echo number_format($contract['loan_amount']- array_sum(array_column($installment, "amount")), 2); ?></span></td>
                                <td>

                                    <input type="text" class="form-control m-b-1 text-right calc required"
                                           name="data[]['loan_amount_receiver']" onblur="calc(this)"
                                           value="<?php echo number_format(0, 2); ?>">
                                </td>
                                <td>N/A</td>
                                <td>
                                    N/A
                                </td>
                                <td>
                                    <button class="btn btn-primary required btn-app" type="button" onclick="btnOnClickListener(this)">
                                        <i class="fa fa-check-circle-o "></i><span> อนุมัติ</span></button>
                                    <button class="btn btn-primary btn-add-row required" type="button" onclick="add(this)" >
                                        <i class="fa fa-plus"></i><span> เพิ่มงวด</span>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" id="loan_id" value="<?php echo $contract['id']?>">
            </div>
        </div>
    </div>
</div>
<!-- MODAL CONFIRM USER-->
<div class="modal fade" id="modal_confirm_user" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ยืนยันสิทธิ์การใช้งาน</h4>
            </div>
            <div class="modal-body">
                <p>ชื่อผู้มีสิทธิ์อนุมัติ</p>
                <input type="text" class="form-control" id="confirm_user">
                <p>รหัสผ่าน</p>
                <input type="password" class="form-control" id="confirm_pwd">
                <br>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button type="button" class="btn btn-info" id="submit_confirm_user">บันทึก</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<!-- MODAL CONFIRM USER-->

<script type="application/javascript">
    $(function () {
        $('.datepicker').datepicker({
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
        })


    });

    $(document).ready(function(){
        if($("#installment").val() === ""){
            disabledTable();
        }
    });

    const btn_add = `<button class="btn btn-primary btn-add-row" type="button" onclick="add(this)">
                        <i class="fa fa-plus"></i><span> เพิ่มงวด</span>
                    </button>`;

    const removeCommas = (number) => {
        return parseFloat(number.split(',').join(''));
    };

    const addComma = (number) => {
        return number.toLocaleString('en', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    };

    const calc = (_element) => {
        const _target = $(_element).closest("tr");
        const _number = removeCommas(_target.find('.line-number').html());
        const _bal = removeCommas(_target.find('.loan_amount_balance').html());
        const _n = removeCommas($(_element).val());
        const balance = _bal - _n;
        let t_balance = 0;
        //_target.find('.loan_amount_balance').html(addComma(balance));
        console.log("element: ", _n);
        $(_element).val(addComma(_n));
        let _cal = 0;
        let _balance = 0;
        _target.closest('tbody').find('tr').each((i, item) => {
            _balance = removeCommas($(item).find(".loan_amount_balance").html());
            _cal = removeCommas($(item).find(".calc").val());
            if(i === 0){
                t_balance = removeCommas($("#loan_amount_balance").val());
                if(_cal === 0){
                    $(item).find('.loan_amount_balance').html(addComma(t_balance));
                }
            }
            if(i >= _number) {
                t_balance -= _cal;
                console.log("find 1: ", t_balance);
                $(item).find('.loan_amount_balance').html(addComma(t_balance));
            }else{
                console.log("find 2: ", t_balance);
                if((t_balance - _cal) < 0){
                    $(item).find('.loan_amount_balance').html(addComma(0.00));
                    $(_element).val(addComma(t_balance));
                    $(".btn-add-row").remove();
                }else {
                    t_balance -= _cal;
                    $(item).find('.loan_amount_balance').html(addComma(t_balance));
                    addBtn(_element);
                }
            }
        });

    };


    const addBtn = (_element) => {
        const _target = $(_element).closest("tr");
        const count = $(_element).closest("tbody").find("tr").length-1;
        const _max_amount = $("#installment").val()-1;
        if(_target.find("button").hasClass("btn-add-row") === false && count === _target.index() && _max_amount < _target.index()) {
            _target.find(".btn-app").after(btn_add);
        }
    };

    const add = (_element) => {
        const _target = $(_element).closest('tr');
        const _limiter =  parseInt($('#installment').val());
        const number = parseInt(_target.find('.line-number').html())+1;
        const amount = _target.find('.loan_amount_balance').html();

        if(_limiter < number){

            return false;
        }

        const template =
            `<tr>
                <td>
                    <span class="line-number">${number}</span>
                </td>
                <td>
                    <div class="input-with-icon g24-col-sm-24">
                        <div class="form-group">
                            <input id="approve_date_${number}" name="data[]['approve_date']" class="form-control m-b-1 datepicker" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" required title="" >
                            <span class="icon icon-calendar input-icon m-f-1"></span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="loan_amount_balance">${amount}</span>
                </td>
                <td>
                    <input type="text" class="form-control m-b-1 text-right calc" value="0.00" onblur="calc(this)" >
                </td>
                <td>
                    N/A
                </td>
                <td>
                    N/A
                </td>
                <td>
                     <button class="btn btn-primary btn-app" type="button" onclick="b">
                                <i class="fa fa-check-circle-o "></i><span> อนุมัติ</span>
                     </button>
                    <button class="btn btn-primary btn-add-row" type="button" onclick="add(this)">
                        <i class="fa fa-plus"></i><span> เพิ่มงวด</span>
                    </button>
                </td>
            </tr>`;


        const table  = $('#installing tbody');

        $(".btn-add-row").remove();

        table.append(template);

        if(_limiter === number ){
            $(".btn-add-row").remove();
        }

        $('.datepicker').datepicker({
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
        })

    }

    const btnEdit = () => {
        installmentEnabled();
    };

    const btnSave = () => {
        const installment = $('#installment');
        if(installment.val() === ""){
            swal('กรุณากรอกจำนวนงวดในช่องแบ่งจ่าย ! ', "", "warning");
            installment.focus();
            return false;
        }

        swal({
            title: "ท่านต้องการบันทึกข้อมูลนี้ใช่หรือไม่ ! ",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },(isConfirm) => {
            if(isConfirm){
                sendInstallment().then((res) => {
                    if(res.status === 200 && res.status_code === "success"){
                        swal("บันทึกสำเร็จ", "", "success");
                        installmentDisable();
                        enableTable();
                    }else{
                        swal("บันทึกไม่สำเร็จ", "", "error");
                        installmentEnabled();
                    }
                }).catch((err) => {
                    swal("บันทึกไม่สำเร็จ", "", "error");
                    installmentEnabled();
                    console.log(err);
                })
            }
        });
    };

    const sendInstallment = () => {
        let data = {loan_id: $('#loan_id').val(), amount: removeCommas($('#installment').val())};
        return new Promise((resolve, reject) => {
            $.post(base_url+"/installment/update_amt", data, (res, status, xhr) => {
                console.log(res, status, xhr);
                resolve(res);
            }).error((err) => {
                reject(err);
            })
        })
    };

    const installmentDisable = () => {
        $('#installment').attr('readonly', 'readonly');
        $('#group-installing .icon').removeClass('fa-save').addClass('fa-pencil');
        $('#group-installing button').attr('onclick', 'btnEdit()');
    };

    const installmentEnabled = () => {
        $('#installment').removeAttr('readonly');
        $('#group-installing .icon').removeClass('fa-pencil').addClass('fa-save');
        $('#group-installing button').attr('onclick', 'btnSave()');
    };

    const enableTable = () => {
        $(".required").removeAttr('disabled').removeAttr('readonly');
    };

    const disabledTable = () => {
        $(".required").attr('disabled', 'disabled').attr('readonly', 'readonly');
    };

    const btnOnClickListener = (_element) => {
        const _target = $(_element).closest("tr");
        let data = {};
        data.loan_id    = $("#loan_id").val();
        data.datetime   = _target.find(".datepicker").val();
        data.balance    = removeCommas(_target.find(".loan_amount_balance").text());
        data.amount     = removeCommas(_target.find(".calc").val());
        data.seq        = _target.index()+1;

        let data2 = {};
        data2.loan_id    = $("#loan_id").val();
        data2.status_to  = 1;
        data2.amount     = removeCommas(_target.find(".calc").val());
        data2.date_approve   = _target.find(".datepicker").val();
        let request = {installment: data, loan: data2};

        console.log(request);

       if( data.amount === 0){
            swal("กรุณากรอกข้อมูลจำนวนเงินที่ต้องการอนุมัติ","", "warning");
            _target.find(".calc").focus();
        }else {
            confirmSummit(request);
        }
    };


    const confirmSummit = (data) => {
        swal({
            title: "ท่านต้องการบันทึกข้อมูลนี้ใช่หรือไม่ ! ",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },(isConfirm) => {
            if(isConfirm) {
                approveCallback(data).then((res) => {
                    if (res.status === 200 && res.status_code === "success") {
                        swal("บันทึกสำเร็จ", "", "success");

                    } else {
                        swal("บันทึกไม่สำเร็จ", "", "error");
                    }
                    return;
                }).then(() => {
                    $.get(base_url+"installment/index/"+data.loan.loan_id, (res) => {
                        $(".gutter-xs").replaceWith($(res).find(".gutter-xs"));
                        $('.datepicker').datepicker({
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
                    })
                }).catch((err) => {
                    swal("บันทึกไม่สำเร็จ", "", "error");
                    console.log(err);
                })
            }
        });
    };

    const btnOnSubmitListener = () => {

    };

    const permissionCallback  = () => {

    };

    const requestCallback = () => {
        return new Promise((resolve, reject) => {
            $.post(base_url+'', data, (res, status, xhr) => {
                if(res.status === 200){
                    resolve(res);
                }else{
                    reject(res);
                }
            })
        });
    };

    const approveCallback = (data) => {
        return new Promise((resolve, reject) => {
            $.post(base_url+"installment/approve", data, (res, status, xhr) => {
                if(res.status === 200){
                    resolve(res);
                }else{
                    reject(res);
                }
            })
        })
    };

    const setStorage = (token) => {
        sessionStorage.setItem("_token", token);
    };

    const clearStorage = () => {
        sessionStorage.clear();
    };

    $(document).on("click", "#submit_confirm_user", (e) => {
        btnOnClickListener();
    });


</script>
