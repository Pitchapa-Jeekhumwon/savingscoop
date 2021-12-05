<style>
    .modal-dialog {
        width: 700px;
    }
</style>
<div class="layout-content">
    <div class="layout-content-body">
        <?php
        $month_arr = array('1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม');
        ?>
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
        </style>

        <style type="text/css">
            .form-group {
                margin-bottom: 5px;
            }
        </style>
        <h1 style="margin-bottom: 0">คำนวนยอดชำระเงินกู้</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <form id="cal_form" method="POST">
                        <br>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-5 control-label">จำนวนเงินขอกู้</label>
                            <div class="g24-col-sm-5">
                                <div class="form-group">
                                    <input type="text" id="loan_amount" title="กรุณากรอกจำนวนเงินขอกู้" name="loan_amount" data-meta="credit_limit" data-optional="loan" value="0.00" class="form-control validation text-right" onchange=""aria-required="true" >
                                </div>
                            </div>
                            <label class="g24-col-sm-2 control-label">ดอกเบี้ย</label>
                            <div class="g24-col-sm-5">
                                <input type="number" id="interest" name="interest" value="0.00" min='0.00' max='100'  step='0.01'class="form-control validation text-right" >
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-5 control-label">จำนวนงวด</label>
                            <div class="g24-col-sm-5">
                                <div class="form-group">
                                    <input type="text" id="period_amount" value="0" name="period_amount" class="form-control validation text-right"  aria-required="true" >
                                </div>
                            </div>
                            <label class="g24-col-sm-2 control-label">ผ่อนต่องวด</label>
                            <div class="g24-col-sm-5">
                                <div class="form-group">
                                    <input type="text" id="pay_amount" name="pay_amount" value="0.00" class="form-control validation text-right" onchange=""aria-required="true" >
                                </div>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-5 control-label">การชำระ</label>
                            <div class="g24-col-sm-5">
                                <div class="form-group">
                                    <div class="radio-inline">
                                        <label>
                                            <input type="radio" id="pay_type_1" name="pay_type" value="1" onclick="">คงต้น
                                        </label>
                                    </div>
                                    <div class="radio-inline">
                                        <label>
                                            <input type="radio" id="pay_type_2" name="pay_type" value="2" onclick="" checked> คงยอด
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-5 control-label right"></label>
                            <div class="g24-col-sm-12">
                                <button id="btn_link_preview" type="submit" name="view" value="preview" class="btn btn-primary" style="width:100%">
                                    คำนวน
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id='loan_install_detail'>

        </div>
    </div>
</div>
</div>

<script>
    var base_url = $('#base_url').attr('class');
    $(document).ready(function() {
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
        $('#cal_form').submit(cal_loan_installment);
        let arr_input = ['input#loan_amount','input#interest','input#period_amount','input#pay_amount'];
        var txtval = [];
        arr_input.forEach(item=>{
             txtval[item] = $(item).val();
          $(item).focus(()=>{
            txtval[item] = $(item).val();
                $(item).val('')
          });
          $(item).blur(()=>{
            if($(item).val() == ''){
                $(item).val(txtval[item]);
            }
            cal_payamount()
            number_format(item)
          });
        });
        $('input[type=radio][name=bedStatus]').change(function() {
            cal_payamount()
        });
    });

    function cal_loan_installment(e) {
        e.preventDefault();
        let data = {
            loan_amount: removeCommas($('#loan_amount').val()),
            pay_amount:removeCommas($('#pay_amount').val()),
            period_amount: removeCommas($('#period_amount').val()),
            pay_type: $('input[name="pay_type"]:checked').val(),
            interest: $('#interest').val()
        };
        $.post(base_url + 'loan/loan_installment_detail', data).done(e => {
            $('#loan_install_detail').html(e);
        }).fail(e => {
            console.log(e);
        });
    }
    function cal_payamount() {
        let period = removeCommas($('#period_amount').val());
        let interest = $('#interest').val();
        let loan_amount = removeCommas($('#loan_amount').val());
        let pay_type =  $('input[name="pay_type"]:checked').val();
        let fn_ER =(l,i,p)=>{
            return  round_nearest((l * ((i / 100) / 12)) / (1 - Math.pow(1 / (1 + ((i / 100) / 12)), p)), undefined, 'ceil');
        }
        let fn_FR=(l,p)=>{
            return  round_nearest((l/p), undefined, 'ceil');
        }
        let pay_amount = 0.0;
        switch (pay_type) {
            case '1': pay_amount =fn_FR(loan_amount,interest)
                break;
            case '2': pay_amount =fn_ER(loan_amount,interest,period)
                break;
        }
        if(!pay_amount || pay_amount === Infinity){
            pay_amount = 0;
        }
        if(pay_amount !=0){
            if((pay_amount %50) !=0 ){
                pay_amount += (50 -(pay_amount %50))
            }
        }
        $('#pay_amount').val(addCommas(pay_amount))
    }
    function change_type() {
        $.ajax({
            url: base_url + 'loan/change_loan_type',
            method: 'POST',
            data: {
                'type_id': $('#loan_type').val()
            },
            success: function(msg) {
                $('#loan_name').html(msg);
            }
        });
        $('#type_name').val($('#type_id :selected').text());
    }

    function select_type(e) {
        $.post(base_url + 'loan/select_loan_type', {
            loan_name_id: $(e).val()
        }).done(e => {
            let result = JSON.parse(e);
            $('#interest').val(result.interest_rate);
        }).fail(e => {
            console.log(e);
        });
    }
    var format_number_option = {minimumFractionDigits: 2, maximumFractionDigits: 2} //
    function number_format(e) {
        let amt = $(e).val();
        amt = typeof amt === 'string' ? parseFloat(removeCommas(amt)) : amt;
        amt = amt === 'NaN' ? 0 : amt;
        $(e).val(addCommas(amt));
    }
    /**
     * แปลงค่า number format เป็็น float
     * @param str
     * @returns {number}
     */
    function removeCommas(str) {
        if (typeof str === "undefined" || str === "" || str === null) return 0;
        if (typeof str === "number") return str;
        return parseFloat(str.split(',').join(''));
    }
    /**
     * แปลงค่า numeric เป็น number format
     * @param str
     * @returns {number}
     */
    function addCommas(amt) {
        if (typeof amt === 'undefined' || amt === null) {
            return 0;
        }
        if (typeof amt === 'string') {
            amt = parseFloat(removeCommas(amt));
        }
        return amt.toLocaleString('en', format_number_option);
    }
</script>