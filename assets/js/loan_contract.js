/***
 * Control Flow Loan Contract
 * @author by Adisak
 */

/**
 *
 * @author by unknown
 * @modify by Adisak
 */
(function ($) {
    $.fn.inputFilter = function (inputFilter) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function () {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                this.value = "";
            }
        });
    };
}(jQuery));

/**
 * เปลี่ยนค่า input ของ .input เป็น number format
 * @author by Adisak
 */
$('.number_format').inputFilter(function (value) {
    return /^-?\d*[.,]?\d{0,2}$/.test(value); //currency
});



var condition_garantor_id = "";                     //เงิื่อนไขสำหรับคนค้ำประกัน
var value_check = "";
var garantor_condition = [];
var not_condition = [];
var template_garantor = [];
var operator = {
    '>': function (x, y) {
        return x > y
    },
    '>=': function (x, y) {
        return x >= y
    },
    '<': function (x, y) {
        return x < y
    },
    '<=': function (x, y) {
        return x <= y
    },
    '=': function (x, y) {
        return x == y
    },
    '!=': function (x, y) {
        return x != y
    }
}

/**
 * ตั้งค่า Option สำหรับ LocaleString
 * @type {{maximumFractionDigits: number, minimumFractionDigits: number}}
 * @example
 *  let number = 1000;
 *  number.toLocaleString('en', format_number_option) //result 1000.00
 */
var format_number_option = {minimumFractionDigits: 2, maximumFractionDigits: 2} //


/**
 * เริ่มต้นการทำงาน loan_contract
 * @note ส่วนของตั้งค่า datepicker จำเป็นต้องวางไว้ใน ready
 */
$(document).ready(function () {

    //ตั้งค่า datepicker
    $("#createdatetime, #date_receive_money, #approve_date").datepicker({
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

    //TODO: Set contract default data

    if (typeof $('.member_id').val() !== "undefined" && $('.member_id').val() !== "") {             // ตรวจหาว member id ตั้งค่าการกู้เงิน
        termCondition($('#loan_type_select').val());
    }

    $(".loan_balance_input").change(function() {
        loan_id = $(this).attr('data_id');
        total = numeral(removeCommas($(this).val())).value() + numeral(removeCommas($('#contract_interest_input_'+loan_id).val())).value()
        $('#prev_contract_balance_'+loan_id).val(total)
        calculate_loan_deduct()
        calcDeduct()
        calcEstimateReceive()
    });
    $(".loan_interest_input").change(function() {
        loan_id = $(this).attr('data_id');
        total = numeral(removeCommas($(this).val())).value() + numeral(removeCommas($('#contract_balance_input_'+loan_id).val())).value()
        $('#prev_contract_balance_'+loan_id).val(total)
        $('#prev_contract_interest_'+loan_id).val(removeCommas($(this).val()))
        calculate_loan_deduct()
        calcDeduct()
        calcEstimateReceive()
    });
    $("#form_add_loan_reason").submit(add_loan_reason);
    $("#btn_add_loan_reason").on('click',()=>{
        $("#form_add_loan_reason").submit();
    })
    $("#modal_add_loan_reason").on('hidden.bs.modal',reset_modal)
});

/**
 *
 * @param ele
 */
function format_the_number_decimal(ele) {
    var value = $('#' + ele.id).val();
    value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    var num = value.split(".");
    var decimal = '';
    var num_decimal = '';
    if (typeof num[1] !== 'undefined') {
        if (num[1].length > 2) {
            num_decimal = num[1].substring(0, 2);
        } else {
            num_decimal = num[1];
        }
        decimal = "." + num_decimal;

    }

    if (value != '') {
        if (value == 'NaN') {
            $('#' + ele.id).val('');
        } else {
            value = (num[0] == '') ? 0 : parseInt(num[0]);
            value = value.toLocaleString() + decimal;
            $('#' + ele.id).val(value);
        }
    } else {
        $('#' + ele.id).val('');
    }
}

/**
 * update ตั้งค่า
 * @author Adisak
 */
$(document).on('change', '#loan_type_select', function () {
    termCondition($(this).val());
    $('#deduct_loan_fee').val(0.00);
    $('#period_amount').trigger('change');
});

/**
 * กำหนดวงเงินกู้สูงสุด
 * @param amt
 * @param
 * @author Adisak
 */
function setMaxLoanLimit(amt, ignore) {
    ignore  = typeof ignore === "undefined" ? false : ignore;
    // ToDo คำนวนสิทธิ์กู้สูงสุด
    // const salary = removeCommas($("input[name='data[coop_loan][salary]']").val());
    // const maxLimit = removeCommas(preferences.credit_limit);
    // const multipleSalary = removeCommas(preferences.less_than_multiple_salary);
    // const estimateAmt = salary * multipleSalary;
    //
    // if(multipleSalary > 0) { //กุ้ได้ x เท่าของเงินเดือน
    //     amt = estimateAmt >= maxLimit ? maxLimit : estimateAmt;
    // }

    creditLimit = typeof amt === "string" ? parseFloat(removeCommas(amt)) : amt;

    $('#max_loan_limit').val(addCommas(amt));

    if(!ignore) {
        loanAmount = creditLimit;
        $('#loan_amount').val(addCommas(amt));
    }
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

function change_type() {
    $.ajax({
        url: base_url + 'loan_contract/change_loan_type',
        method: 'POST',
        data: {
            'type_id': $('#loan_type_choose').val()
        },
        success: function (msg) {
            $('#loan_type_select').html(msg);
        }
    });
    $('#type_name').val($('#type_id :selected').text());
}

var condition = {};
var periodAmt = 0;
var periodMaxAmt = 0;
var creditLimit = 0;
var loanAmount = 0;
var interest = 0;
var estimate = 0;
var deduct = 0;
var memberID = $('.member_id');

var preferences = {};
var payment_loan_down = $('#payment_loan_down').val();  //ภาระอื่นๆ ที่ต้องชำระ  *****เงินกู้****** input->hidden->ที่เก็บค่าไว้
var principal_down = $('#principal_down').val(); //รายการชำระ ***** เงินต้น ********* input->hidden->ที่เก็บค่าไว้
var interest_down = $('#interest_down').val(); //รายการชำระ ***** ดอกเบี้ย ********* input->hidden->ที่เก็บค่าไว้
var payment_loan_down_cal = $('#payment_loan_down').val(); //ภาระอื่นๆ ที่ต้องชำระ      *****เงินกู้****** ค่าเริ่มต้น
var principal_down_cal = $('#principal_down').val();   //รายการชำระ ***** เงินต้น ********* ค่าเริ่มต้น
var interest_down_cal = $('#interest_down').val();//รายการชำระ ***** ดอกเบี้ย ********* ค่าเริ่มต้น
function setInterest(amt) {

    amt = typeof amt === "string" ? parseFloat(amt) : amt;
    $('#interest_per_year').val(amt);
    interest = amt;
}

/**
 * เริ่มตั้งค่าเงื่อนไขเงินกู้ประเภทต่างๆ
 * @param type_id ประเภทเงินกู้ย่อย จำเป็นในการดึกเงื่อนไข
 */
function termCondition(type_id) {
    if (typeof memberID.val() !== "undefined" && memberID.val() !== "" && $('#loan_id').val() !== "undefined" &&
        $('#loan_id').val() !== "") {
        immune(); //ล๊อกค่าที่ไม่ต้องการให้แก้ไข
        new Promise(getInit).then(initial).then(getContractData).then(setContract).then(shareAndDeposit).then(calcProcessing).then(function(){
            firstSet = true;
            $("#pay_amount").val(addCommas(coopLoan ? coopLoan.period_amount_bath : 0));
        }).catch((err) => {
            console.error("Error:", err);
        })
    } else if (typeof memberID.val() !== "undefined") {
        new Promise(getInit).then(initial).then(shareAndDeposit).then(function(){
            firstSet = true;
            $("#pay_amount").val(addCommas(coopLoan ? coopLoan.period_amount_bath : 0));
        }).catch(err => {
            console.error("Error:", err);
        });

    } else {
        /* TODO: Initial contract crete */
    }
}

function getInit(resolve, reject) {
    $.post(base_url + '/loan_contract/term_condition',
    {
        type_id: $('#loan_type_select').val(),
        member_id: memberID.val(),
        net_balance: $('#net_balance').val()
    },
    function (res) {
        setPreferences(res);
        resolve(res);
        return;
    }).error(function (xhr) {
        reject(xhr);
        return;
    });
    return;
}

function initial(res) {
    firstSet = false;
    deductReset();
    setMaxLoanLimit(addCommas(res ? res.credit_limit : 0));
    setMinimum(res);
    setMaxPeriod(res ? res.max_period : 0);
    setInterest(res ? res.interest_rate : 0);
    calcProcessing();
    setGuaranteePersonal();
}

const setMinimum = (obj) => {
    return new Promise((resolve, reject) => {
        let data = {};
        data.member_id = memberID.val();
        $.post(base_url+"/loan_contract/get_total_income", data, (res, status, xhr) => {
            if(res.status === "success"){
                return resolve(res);
            }
            return reject(xhr);
        });
    }).then((res) => {
        let amount = 0;
        if(obj && obj.credit_minimum_percentage !== "" && obj.credit_minimum_percentage !== null && obj.credit_minimum_percentage !== "0.00"){
            const percentage = obj.credit_minimum_percentage;
            const total = res.total_income;
            amount = (total*percentage/100);
        }
        if(obj && obj.credit_minimum_baht !== "" &&  obj.credit_minimum_baht !== null && obj.credit_minimum_baht !== "0.00"){
            if(amount > obj.credit_minimum_baht) amount = obj.credit_minimum_baht;
        }
        if(obj && obj.percent_share_guarantee && obj.percent_share_guarantee != 0) {
            limit_by_share = removeCommas($("#share-collect-search").val()) * removeCommas(obj.percent_share_guarantee) /100;
            if(obj.credit_limit) {
                amount = limit_by_share <= obj.credit_limit ? limit_by_share : obj.credit_limit;
            } else {
                amount = limit_by_share;
            }
        } else if (amount == 0){
            amount = obj ? obj.credit_limit : 0
        }
        setLoanAmount(amount);
        setMaxLoanLimit(amount);
    }).catch((err) => {
        console.log(err);
    });
}

/**
 * ตรวจเงื่อนไขหลักเกณการกู้
 */
let requireShareOrDeposit = 0;
function shareAndDeposit() {
    const deductShareDeposit = $('#deduct_blue_deposit');
    if (preferences && preferences.share_and_deposit_guarantee === "1") {
        const reqShareDepositPer = removeCommas(preferences.least_share_or_blue_acc_percent);
        requireShareOrDeposit = Math.round(reqShareDepositPer * loanAmount)/100;
        new Promise(resolve => {
            $.post(base_url+'loan_contract/check_share_and_deposit', { member_id: $('.member_id').val() }, function(res){
                if(res.status === 200){
                    const require = removeCommas(deductShareDeposit.val());
                    const amount = requireShareOrDeposit - (removeCommas(res.total)+require);
                    resolve(amount);
                    return;
                }
            })
        }).then((amount) => {
            const current = removeCommas(deductShareDeposit.val());
            if((current+amount) > current) {
                deductShareDeposit.val(addCommas((current+amount))).trigger('blur');
            }else{
                deductShareDeposit.trigger('blur');
            }
        });
    }
}

/**
 * ล๊อก input ที่ไม่ต้องการให้แก้ไข
 */
function immune() {
    $('#loan_type_choose').attr('readonly', 'readonly').attr('disabled', 'disabled');
    $('#loan_type_select').attr('readonly', 'readonly').attr('disabled', 'disabled');
    $('.member_id').attr('readonly', 'readonly');
    $('#test').attr('disabled', 'disabled').removeAttr('data-toggle').removeAttr('href');
}

/**
 * ปลดล๊อก input จาก method immune()
 */
function release() {
    $('#loan_type_choose').removeAttr('readonly').removeAttr('disabled');
    $('#loan_type_select').removeAttr('readonly').removeAttr('disabled');
    $('.member_id').removeAttr('readonly');
    $('#test').removeAttr('disabled').attr('data-toggle', 'modal').attr('href', '#');
}


function checkForm() {
    var text_alert = '';
    var id_to_focus = [];
    var i = 0;

    if ($('.member_id').val() == "") {
        text_alert += 'กรุณาระบุเลขสมาชิก\n';
        id_to_focus[i] = 'member_id';
        i++;
    }

    if (text_alert !== '') {
        $('#' + id_to_focus[0]).focus();
        swal('กรุณากรอกข้อมูลให้ครบ', text_alert, 'error');
    } else {
        setTimeout(function() {
            $('#form_contract').submit();
        }, 200);
    }
}

/**
 *
 */


// ---------- กู้ฉุกเฉิน ไม่เกิน 12 งวด ----------
function check_installment(event){
    let meta = 'max_period';
    let thisValue = parseInt(removeCommas($('#period_amount').val()));
    let optional = removeCommas($('#period_amount').val());
    let ele = new Validation(event);
    ele.meta = meta;
    ele.loanTypeId = $("#loan_type_select").val();
    ele.memberId = $("#member_id").val();
    ele.loan = removeCommas($("#loan_amount").val());

    let value = ele.rule(optional);

    if(thisValue > value.valueMax){
        if(ele.is_numeric(value.valueMax)){
            $('#period_amount').val(addCommas(value.valueMax));
            $('#period_amount').trigger('change');
        }
        else{
            $('#period_amount').val(value.valueMax);
            $('#period_amount').trigger('change');
        }
    }
    if(value.message){
        console.error( ( parseInt(value.valueMax) != "" ? "" : "เงื่อนไขไม่ถูกต้อง \n" ) + "\t\t" + value.message );
    }
}
// ---------- กู้ฉุกเฉิน ไม่เกิน 12 งวด ----------

//---------- เงื่อนไข meta ----------
$(document).on('change', '#loan_amount', function(e) {
    if($('#loan_type_choose').val() == 2 && $('#loan_type_select').val() == 200){ //เงื่อนไข ไม่เกิน ของกู้สามัญ บุคคลค้ำ
        let thisValue = parseInt(removeCommas(this.value));
        let meta = $(this).data("meta");
        let key_optional = $(this).data("optional");
        let optional = removeCommas($("#"+key_optional).val());
        let ele = new Validation(e);
        ele.meta = meta;
        ele.loanTypeId = $("#loan_type_select").val();
        ele.memberId = $("#member_id").val();
        ele.loan = removeCommas($("#loan").val());
        let value = ele.rule(optional);

        $(this).prop('max', value.valueMax);

        if(thisValue > value.valueMax){
            swal("ระบบแจ้งเตือน", "ไม่สามารถกรอกค่า ได้มากกว่า "+ (ele.is_numeric( value.valueMax ) ? addCommas(value.valueMax) : "" ) );
            if(ele.is_numeric(value.valueMax)){
                $(this).val(addCommas(value.valueMax));
                $(this).trigger('change');
            }
            else{
                $(this).val(value.valueMax);
                $(this).trigger('change');
            }
        }
        if(value.message){
            swal("ระบบแจ้งเตือน", ( value.valueMax != "" ? "" : "เงื่อนไขไม่ถูกต้อง \n" ) + "\t\t" + value.message);
        }
    } else if($('#loan_type_choose').val() == 2 && $('#loan_type_select').val() == 201){ // หุ้นค้ำ

        var thisValue = parseInt(removeCommas(this.value));
        var meta = $(this).data("meta");
        var ele = new Validation(e);
        ele.meta = meta;
        ele.loanTypeId = $("#loan_type_select").val();
        ele.memberId = $("#member_id").val();
        ele.loan = removeCommas($("#loan_amount").val());
        var value = ele.rule(optional);

        $(this).prop('Max', value.valueMax);

        if(thisValue > value.valueMax){
            swal("ระบบแจ้งเตือน", "ไม่สามารถกรอกค่า ได้มากกว่า "+ ( ele.is_numeric( value.valueMax ) ? addCommas(value.valueMax) : "" ) );
            if(ele.is_numeric(value.valueMax)){
                $(this).val(addCommas(value.valueMax));
                $(this).trigger('change');
            }
            else{
                $(this).val(value.valueMax);
                $(this).trigger('change');
            }
        }
        if(value.message){
            swal("ระบบแจ้งเตือน", ( value.valueMax != "" ? "" : "เงื่อนไขไม่ถูกต้อง \n" ) + "\t\t" + value.message);
        }
    } else if($('#loan_type_choose').val() == 1 && $('#loan_type_select').val() == 100){ // กู้ฉุกเฉิน ค่าธรรมเนียม
        check_installment(e); // เช็คงวด
        var thisValue = parseInt(removeCommas(this.value));
        var meta = 'loan_fee_baht';
        var ele = new Validation(e);
        ele.meta = meta;
        ele.loanTypeId = $("#loan_type_select").val();
        ele.memberId = $("#member_id").val();
        ele.loan = removeCommas($("#loan_amount").val());
        var optional = '';

        var value = ele.rule(optional);

        $(this).prop('Max', value.valueMax);

        if(thisValue > value.valueMax){
            // swal("ระบบแจ้งเตือน", "ค่าธรรมเนียม " + ( ele.is_numeric( value.valueMax ) ? addCommas(value.valueMax) : "" ) );
            if(ele.is_numeric(value.valueMax)){
                $('#deduct_loan_fee').val(addCommas(value.valueMax));
                $('#deduct_loan_fee').trigger('change');
            }
            else{
                $('#deduct_loan_fee').val(value.valueMax);
                $('#deduct_loan_fee').trigger('change');
            }
        }
        if(value.message){
            swal("ระบบแจ้งเตือน", ( value.valueMax != "" ? "" : "เงื่อนไขไม่ถูกต้อง \n" ) + "\t\t" + value.message);
        }
    } else{

        var thisValue = parseInt(removeCommas(this.value));
        if(thisValue > creditLimit){
            // $(this).val(addCommas(creditLimit)).trigger("change");
            // swal("ระบบแจ้งเตือน", "จำนวนเงินขอกู้ต้องไม่เกินกว่า "+addCommas(creditLimit)+" บาท\t\t\n ต้องการทำรายการต่อใช่หรือไม่");
            swal({
				title: "ระบบแจ้งเตือน",
				text: "จำนวนเงินขอกู้ต้องไม่เกินกว่า "+addCommas(creditLimit)+" บาท\t\t\n ต้องการทำรายการต่อใช่หรือไม่",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'ตกลง',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: true,
				closeOnCancel: true
			},
			function (isConfirm) {
				if (!isConfirm) {
					$(this).val(addCommas(creditLimit)).trigger("change");
				}
			});
        }
    }
});

$(document).on('change', '#period_amount', function(e) {
    if($('#loan_type_choose').val() == 1 && $('#loan_type_select').val() == 100){
        let thisValue = parseInt(removeCommas(this.value));
        let meta = $(this).data("meta");
        let optional = removeCommas($('#period_amount').val());
        let ele = new Validation(e);
        ele.meta = meta;
        ele.loanTypeId = $("#loan_type_select").val();
        ele.memberId = $("#member_id").val();
        ele.loan = removeCommas($("#loan_amount").val());
        let value = ele.rule(optional);

        $(this).prop('max', value.valueMax);

        if(thisValue > value.valueMax){
            // swal("ระบบแจ้งเตือน", "ไม่สามารถกรอกค่า ได้มากกว่า "+ (ele.is_numeric( value.valueMax ) ? addCommas(value.valueMax) : "" ) );
            if(ele.is_numeric(value.valueMax)){
                $(this).val(addCommas(value.valueMax));
                $(this).trigger('change');
            }
            else{
                $(this).val(value.valueMax);
                $(this).trigger('change');
            }
        }

        if(value.message){
            console.error( ( parseInt(value.valueMax) != "" ? "" : "เงื่อนไขไม่ถูกต้อง \n" ) + "\t\t" + value.message );
        }
    }
});

//---------- เงื่อนไข meta ----------
 function calcAge(date, month, year) {
    month = month - 1;
    year = year - 543;

    today = new Date();
    dateStr = today.getDate();
    monthStr = today.getMonth();
    yearStr = today.getFullYear();

    theYear = yearStr - year;
    theMonth = monthStr - month;
    theDate = dateStr - date;

    var days = "";
    if (monthStr == 0 || monthStr == 2 || monthStr == 4 || monthStr == 6 || monthStr == 7 || monthStr == 9 || monthStr == 11) days = 31;
    if (monthStr == 3 || monthStr == 5 || monthStr == 8 || monthStr == 10) days = 30;
    if (monthStr == 1) days = 28;

    inYears = theYear;

    if (month < monthStr && date > dateStr) {
        inYears = parseInt(inYears) + 1;
        inMonths = theMonth - 1;
    };

    if (month < monthStr && date <= dateStr) {
        inMonths = theMonth;
    } else if (month == monthStr && (date < dateStr || date == dateStr)) {
        inMonths = 0;
    } else if (month == monthStr && date > dateStr) {
        inMonths = 11;
    } else if (month > monthStr && date <= dateStr) {
        inYears = inYears - 1;
        inMonths = ((12 - -(theMonth)) + 1);
    } else if (month > monthStr && date > dateStr) {
        inMonths = ((12 - -(theMonth)));
    };

    if (date < dateStr) {
        inDays = theDate;
    } else if (date == dateStr) {
        inDays = 0;
    } else {
        inYears = inYears - 1;
        inDays = days - (-(theDate));
    };

    var result = ['day', 'month', 'year'];
    result.day = inDays;
    result.month = inMonths;
    result.year = inYears;

    return result;
};

$(document).on('change', '#date_receive_money', function() {
    conditionProcess();
});

function conditionProcess() {
    if($('#loan_type_choose').val() == '1' && $('#loan_type_select').val() == '100'){  // 1 กู้ฉุกเฉิน 100 กู้ฉุกเฉิน
        $.ajax({
            url: base_url+"ajax/get_information_member",
            method:"post",
            data: {
                member_id : $('.member_id').val(),
                loan_type : $('#loan_type_select').val()
            },
            dataType:"json",
            success:function(data) {
                if(parseFloat(removeCommas($('#loan_amount').val())) > parseFloat(removeCommas($('#max_loan_limit').val()))){ // เพิ่มเงื่อนไขกรณีกรอกเงินเกิน Limit
                    $('#loan_amount').val(addCommas($('#max_loan_limit').val()));
                    // $('#loan_amount').trigger('change');
                }

                let loan_list = data.period_now.loan_list //รายการกู้
                let period_now = data.period_now.period_now //งวดของคนกู้
                let min_installment = data.term_of_loan.min_installment //ชำระงวดขั้นต่ำ

                if(parseInt(loan_list) > 0){ // เช็คว่ามีรายการกู้ไหม
                    if( parseInt(period_now) < parseInt(min_installment) || period_now == null){ // ชำระขั้นต่ำจำนวน 1 งวด จึงจะสามารถกู้ใหม่ได้
                        swal('ระบบแจ้งเตือน', 'กรุณาชำระขั้นต่ำจำนวน ' + min_installment + ' จึงจะสามารถกู้ฉุกเฉินใหม่ได้');
                    }
                }

                let approve_date =  $('#approve_date').val().split('/');
                let receive_money = $('#date_receive_money').val().split('/');
                let birthday = new Array();
                let today = new Date();
                birthday[2] = today.getDate();
                birthday[1] = today.getMonth();
                birthday[0] = today.getFullYear();

                if(data.data.birthday == null){
                    swal('ระบบแจ้งเตือน', 'ไม่พบวันเกิด กรุณาเพิ่มข้อมูลวันเกิด');
                }
                else{
                    birthday = data.data.birthday.split('-');
                }

                let age = calcAge(birthday[2],birthday[1],parseInt(birthday[0])+543);
                let period_amount = 0;

                //---------- เงือนไข อายุไม่เกิน 60 ----------
                if(parseInt(age.year) == 60){
                    period_amount = (10 - parseInt(receive_money[1])) < 0 ? 0 : 10 - parseInt(receive_money[1]); // หาจำนวนงวดทั้งหมด โดย เอาเดือน กันยายน ตั้ง ลบ กับเดือน เริ่มสัญญา
                    if(parseInt(birthday[1]) < 9 && parseInt($('#period_amount').val()) > period_amount){
                        // swal("ระบบแจ้งเตือน", "จำกัดงวดชำระ " + period_amount + " สำหรับคนเกิดก่อน กันยายน");
                        $('#period_amount').val(period_amount);
                        $('#period_amount').trigger('change');
                    }
                    else if(parseInt(birthday[1] > 9)){// เช็คเดือนว่าเกิดหลัง เดือนกันยายน หรือไม่
                        period_amount = (12 - parseInt(receive_money[1])) + 10; // หาจำนวนงวดทั้งหมด โดย เอา 12(เดือน) ลบ เดือนเกิด จากนั้น บวก กับ 9(เดือนกันยายน)
                        if(parseInt($('#period_amount').val()) >  period_amount ){
                            // swal("ระบบแจ้งเตือน", "จำกัดงวดสำหรับคนเกิดหลัง กันยายน");
                            $('#period_amount').val(period_amount);
                            $('#period_amount').trigger('change');
                        }
                    }
                }
                else if(parseInt(age.year) == 59){ // อายุ 59
                    if(parseInt(birthday[1]) < 9){
                        if(parseInt(receive_money[1]) >= 10 && parseInt(receive_money[1]) <= 12 && parseInt(receive_money[2])-543 == today.getFullYear()){
                            period_amount = ( 12 - parseInt(receive_money[1]) + 10 ) < 0 ? 0 : ( 12 - parseInt(receive_money[1]) + 10 );
                            if(parseInt($('#period_amount').val()) > period_amount ){
                                $('#period_amount').val(period_amount);
                                $('#period_amount').trigger('change');
                            }
                        }
                        else if(parseInt(receive_money[1]) >= 1 && parseInt(receive_money[1]) <= 9 && parseInt(receive_money[2])-543 != today.getFullYear()){
                            period_amount = (10 - parseInt(receive_money[1])) < 0 ? 0 : (10 - parseInt(receive_money[1]));
                            if(parseInt($('#period_amount').val()) > period_amount ){
                                $('#period_amount').val(period_amount);
                                $('#period_amount').trigger('change');
                            }
                        }
                        else if(parseInt(receive_money[1]) >= 10 && parseInt(receive_money[2])-543 != today.getFullYear()){
                            $('#period_amount').val(0);
                            $('#period_amount').trigger('change');
                        }
                    }
                }
                //---------- เงือนไข อายุไม่เกิน 60 ----------
            }  ,
            error: function(xhr){
                console.error('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
            }
        });
    }
    else if($('#loan_type_choose').val() == '1' && $('#loan_type_select').val() == '102'){
        if(parseFloat(removeCommas($('#loan_amount').val())) > parseFloat(removeCommas($('#max_loan_limit').val()))){ // เพิ่มเงื่อนไขกรณีกรอกเงินเกิน Limit
            $('#loan_amount').val(addCommas($('#max_loan_limit').val()));
            // $('#loan_amount').trigger('change');
        }
    }
    else if($('#loan_type_choose').val() == '1' && $('#loan_type_select').val() == '103'){
        if(parseFloat(removeCommas($('#loan_amount').val())) > parseFloat(removeCommas($('#max_loan_limit').val()))){ // เพิ่มเงื่อนไขกรณีกรอกเงินเกิน Limit
            $('#loan_amount').val(addCommas($('#max_loan_limit').val()));
            // $('#loan_amount').trigger('change');
        }
    }
    else if($('#loan_type_choose').val() == '2' && $('#loan_type_select').val() == '101'){
        if(parseFloat(removeCommas($('#loan_amount').val())) > parseFloat(removeCommas($('#max_loan_limit').val()))){ // เพิ่มเงื่อนไขกรณีกรอกเงินเกิน Limit
            $('#loan_amount').val(addCommas($('#max_loan_limit').val()));
            // $('#loan_amount').trigger('change');
        }
    }
    else if($('#loan_type_choose').val() == '2' && $('#loan_type_select').val() == '200'){
        let optional = '';
        let ele = new Validation();
        ele.meta = 'credit_limit';
        ele.loanTypeId = $("#loan_type_select").val();
        ele.memberId = $("#member_id").val();
        ele.loan = removeCommas($("#loan_amount").val());
        let value = ele.rule(optional);

        if(ele.is_numeric(value.valueMax)){
            $('#max_loan_limit').val(addCommas(value.valueMax));
            $('#max_loan_limit').trigger('change');
        }
        else{
            $('#max_loan_limit').val(value.valueMax);
            $('#max_loan_limit').trigger('change');
        }
        if(value.message){
            swal("ระบบแจ้งเตือน", ( value.valueMax != "" ? "" : "เงื่อนไขไม่ถูกต้อง \n" ) + "\t\t" + value.message);
        }

        $.ajax({
            url: base_url+"ajax/get_information_member",
            method:"post",
            data: {
                member_id : $('.member_id').val(),
                loan_type : $('#loan_type_select').val()
            },
            dataType:"json",
            success:function(data) {

                let loan_list = data.period_now.loan_list; //รายการกู้
                let period_now = data.period_now.period_now; //งวดของคนกู้
                let min_installment = data.term_of_loan.min_installment; //ชำระงวดขั้นต่ำ
                let max_period = data.term_of_loan.max_period; //งวดการกู้สูงสุด

                if(parseInt(loan_list) > 0){ // เช็คว่ามีรายการกู้ไหม
                    if( parseInt(period_now) < parseInt(min_installment) || period_now == null){ // ชำระขั้นต่ำจำนวน 12 งวด จึงจะสามารถกู้ใหม่ได้
                        swal('แจ้งเตือน', 'กรุณาชำระขั้นต่ำจำนวน ' + min_installment + ' งวดจึงจะสามารถกู้สามัญบุคคลค้ำใหม่ได้');
                    }
                }

                if(parseInt($('#period_amount').val()) > parseInt(max_period)){ // งวดเกินที่ตั้งไว้ x งวด ตอนนี้ 150 งวด
                    $('#period_amount').val(parseInt(max_period));
                    // $('#period_amount').trigger('change');
                }

                if(parseFloat(removeCommas($('#loan_amount').val())) > parseFloat(removeCommas($('#max_loan_limit').val()))){ // เพิ่มเงื่อนไขกรณีกรอกเงินเกิน Limit
                    $('#loan_amount').val(addCommas($('#max_loan_limit').val()));
                    // $('#loan_amount').trigger('change');
                }
            }  ,
            error: function(xhr){
                console.error('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
            }
        });
    }
    else if($('#loan_type_choose').val() == '2' && $('#loan_type_select').val() == '201'){
        //---------- ถ้ามีการกู้ ให้ชำระเงินที่กู้ก่อน ----------
        $.ajax({
            url: base_url+"ajax/get_information_member",
            method:"post",
            data: {
                member_id : $('.member_id').val(),
                loan_type : $('#loan_type_select').val()
            },
            dataType:"json",
            success:function(data) {
                let loan_list = data.period_now.loan_list //รายการกู้
                let min_installment = data.term_of_loan.min_installment //ชำระงวดขั้นต่ำ
                var period_now = data.period_now.period_now; //งวดของคนกู้

                if(parseInt(loan_list) > 0){ // เช็คว่ามีรายการกู้ไหม
                    if( parseInt(period_now) < parseInt(min_installment) || period_now == null){ // ชำระขั้นต่ำจำนวน x งวด จึงจะสามารถกู้ใหม่ได้
                        swal('แจ้งเตือน', 'กรุณาชำระขั้นต่ำจำนวน ' + min_installment + ' งวดจึงจะสามารถกู้สามัญ (หุ้นค้ำ) ใหม่ได้');
                    }
                }
            }  ,
            error: function(xhr){
                console.error('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
            }
        });
        //---------- ถ้ามีการกู้ ให้ชำระเงินที่กุ้ก่อน ----------
    }
    else if($('#loan_type_choose').val() == '2' && $('#loan_type_select').val() == '300'){
        if(removeCommas($('#loan_amount').val()) > removeCommas($('#max_loan_limit').val())){
            $('#loan_amount').val($('#max_loan_limit').val());
        }
    }
    else if($('#loan_type_choose').val() == '2' && $('#loan_type_select').val() == '400'){
        if(parseFloat(removeCommas($('#loan_amount').val())) > parseFloat(removeCommas($('#max_loan_limit').val()))){ // เพิ่มเงื่อนไขกรณีกรอกเงินเกิน Limit
            $('#loan_amount').val(addCommas($('#max_loan_limit').val()));
            // $('#loan_amount').trigger('change');
        }
    }
    else if($('#loan_type_choose').val() == '3' && $('#loan_type_select').val() == '301'){
        if(parseFloat(removeCommas($('#loan_amount').val())) > parseFloat(removeCommas($('#max_loan_limit').val()))){ // เพิ่มเงื่อนไขกรณีกรอกเงินเกิน Limit
            $('#loan_amount').val(addCommas($('#max_loan_limit').val()));
            // $('#loan_amount').trigger('change');
        }
    }
}

function calcProcessing() {

    //conditionProcess(); // เงื่อนไขใหม่

    calcDeduct();
    calcEstimateReceive();
    if(!$('#loan_id').val()) {
        deductDefault();
    }
    calcCost();
    calcEstimateReceive();

    setTimeout(function(){
        if(!$('#loan_id').val()) {
            calcPeriod();
        }
        prevDeduct();
        cal_estimate_money();
        setTimeout(function(){
            update_net();
            if(!$('#loan_id').val()) {
                setDefaultGuarantee();
            }
        }, 300);
    }, 800);

    //shareAndDeposit());
}

function setPreferences(obj) {
    preferences = obj
}

function setLoanAmount(amt) {
    loanAmount = typeof amt === 'string' ? parseFloat(removeCommas(amt)) : amt;
    $("#loan_amount").val(addCommas(loanAmount));
}

function setMaxPeriod(amt) {
    let _amt = periodMaxAmt = typeof amt === "string" ? parseInt(amt) : amt;
    setPeriodAMT(_amt);
}

function setPeriodAMT(amt){
    let _amt = periodAmt = typeof amt === "string" ? parseInt(amt) : amt;
    $('#period_amount').val(_amt);
}

function number_format(e) {
    let amt = $(e).val();
    amt = typeof amt === 'string' ? parseFloat(removeCommas(amt)) : amt;
    amt = amt === 'NaN' ? 0 : amt;
    $(e).val(addCommas(amt));
}

function cal_period(){ //คำนวณงวดจากการชำระต่องวด
    if(removeCommas($('#loan_amount').val()) > 0){
        var period =  removeCommas($('#loan_amount').val()) / removeCommas($('#pay_amount').val());
        // swal('จำนวนที่สามารถผ่อนได้ : ' + String(period) + ' แปลงเป็น Int ' + parseInt(period), '', 'warning');
        $('#period_amount').val(parseInt(period));
        $('#period_amount').trigger('change');

        calcProcessing();
    }
}
function check_period() {
    var period = $('#period_amount').val();
    var  pay_amount = $('#pay_amount').val();
    var period_person =$('#period_person').val();
    var per_period_person =$('#per_period_person').val();
    let total = 0;
    $("input[name^='data[coop_loan_cost]']").each(function(){
        total += removeCommas($(this).val());
    });
    pay_amount =removeCommas(pay_amount);
    per_period_person = per_period_person-total;
    if (period > 300) {
        swal('จำนวนงวดต้องไม่เกิน 300 งวด', '', 'warning');
    }
    if (parseFloat(period) > parseFloat(period_person) ) {
        swal('จำนวนงวดต้องไม่เกิน ' + period_person + 'งวดและยอดผ่อนต่องวดต้องไม่เกิน' + per_period_person + ' บาท', '', 'warning');
    } else if (parseFloat(period) < parseFloat(period_person) && parseFloat(pay_amount) > parseFloat(per_period_person)) {
            swal('ยอดผ่อนต่องวดต้องไม่เกิน ' + addCommas(per_period_person, 2) + ' บาท', '', 'warning');
    } else if (parseFloat(pay_amount) > parseFloat(per_period_person)){
        swal('ยอดผ่อนต่องวดต้องไม่เกิน' + per_period_person + ' บาท', '', 'warning');
    }
}


function calcPeriod() {
    let payType = $('input[name="data[coop_loan][pay_type]"]:checked').val();
    let period = 0;
    if (payType === '2') {
        period = effectiveRate();
    } else {
        period = flatRate();
    }
    if(period !=0){
        if((period %50) !=0 ){
            period += (50 -(period %50))
        }
    }
    $('#pay_amount').val(addCommas(period));
}

function flatRate() {
    return  round_nearest((loanAmount/periodAmt), undefined, 'ceil');
}

function effectiveRate() {
    return round_nearest((loanAmount * ((interest / 100) / 12)) / (1 - Math.pow(1 / (1 + ((interest / 100) / 12)), periodAmt)), undefined, 'ceil');
}

function calcDeduct() {
    $('#deduct_amount').val(addCommas(deduct));
    if(payment_loan_down_cal == '0'){
        $('#payment_loan_down_cal').val(('0.00'));
    }else{
        $('#payment_loan_down_cal').val(addCommas(payment_loan_down_cal));
    }
    if(interest_down_cal == '0'){
        $('#interest_down_cal').val(('0.00'));
    }else{
        $('#interest_down_cal').val(addCommas(interest_down_cal));
    }
    $('#principal_down_cal').val(addCommas(principal_down_cal)); 

}

function calcEstimateReceive() {
    if(!firstSet) return;
    estimate = round((loanAmount - deduct));
    $('#deduct_pay_prev_loan').val(deduct);
    $('#estimate_money').val(addCommas(estimate));
}

function cal_estimate_money(){
    let total = 0;
    $("input[name^='data[loan_deduct]']").each(function(){
        total += removeCommas($(this).val());
    });
    $(".external_deduct_amount").each(function(){
        total += removeCommas($(this).val());
    });
    $(".coop_saving_deduct").each(function(){
        total += removeCommas($(this).val());
    });
    estimate = round(loanAmount - total);
    $('#estimate_money').val(addCommas(estimate));
}

/***
 * Guarantee
 */
function setInputDisplay(obj) {
    guaranteeName.val(obj.name);
    guaranteeEstimate.val(addCommas(obj.estimate));
    guaranteeAmount.val(addCommas(obj.amount));
}

function changeInput(x) {

    switch (x) {
        case "1" :
            callGuarantee();
            break;
        case "2" :
            callShare();
            break;
        case "3" :
            callAccount();
            break;
        case "4" :
            callState();
            break;
        default  :
            callDefault();
            break
    }
    //calc_personal_guarantee();
}

var guaranteeType = $('.edit-guarantee-type');
var guarantee = $('.edit-guarantee');
var guaranteeName = $('.edit-guarantee-name');
var guaranteeEstimate = $('.edit-guarantee-estimate');
var guaranteeAmount = $('.edit-guarantee-amount');
var guaranteeRemark = $('.edit-guarantee-remark');
var guaranteeSalary = $('.edit-guarantee-salary');
var clsList = ['guarantee', 'account'];

function callShare() {

    let member_id = memberID.val();
    let memberName = $('.full_name').val();
    let memberGuaranteeMax = removeCommas($('.share-collect').val()) * 10;
    let memberGuaranteeVal = (memberGuaranteeMax * 90 / 100);

    let shareElement = [
        {name: 'number', val: member_id, display: 'readOnly', type: 'default'},
        {name: 'name', val: memberName, display: 'readOnly'},
        {name: 'estimate', val: memberGuaranteeMax, display: 'readOnly'},
        {name: 'value', val: memberGuaranteeVal, event: [{'onblur': "number_format(this)"}]},
        {name: 'remark'},
        {name: 'salary', display: 'readOnly'},
    ];
    setEditor(shareElement)

}

function callGuarantee() {
    let guaranteeEle = [
        {name: 'number', type: 'member', display: 'readOnly'},
        {name: 'name', display: 'readOnly'},
        {name: 'estimate', event: [{'onblur': "number_format(this); check_loan_amount(this);"}]},
        {name: 'value', event: [{'onblur': "number_format(this); check_loan_amount(this);"}]},
        {name: 'remark'},
        {name: 'salary', display: 'readOnly'},
    ];
    setEditor(guaranteeEle)
}

function check_loan_amount(){
    //---------- เช็ค เงินคนค้ำประกันว่าเท่ากับวงเงิน ไหม ---------
    if($('#loan_type_choose').val() == 2 && $('#loan_type_select').val() == 200){ //เงินกู้สามัญ กู้สามัญบุคคลค้ำ
        if(removeCommas(guaranteeAmount.val()) > removeCommas(guaranteeEstimate.val())){
            guaranteeAmount.val(guaranteeEstimate.val());
            guaranteeAmount.trigger('change');
        }
        else if(removeCommas(guaranteeAmount.val()) > removeCommas($('.edit-guarantee-salary').val()) * multiple_guarantee_estimate){
            let limit_guarantee = removeCommas($('.edit-guarantee-salary').val()) * multiple_guarantee_estimate;
            guaranteeAmount.val(addCommas(limit_guarantee));
            guaranteeAmount.trigger('change');
        }
    }
    //---------- เช็ค เงินคนค้ำประกันว่าเท่ากับวงเงิน ไหม ---------
}

function callAccount() {
    let account = [
        {name: 'number', type: 'account', event: [{'onchange': 'setGuaranteeDeposit(this)'}]},
        {name: 'name', display: 'readOnly'},
        {name: 'estimate', display: 'readOnly'},
        {name: 'value'},
        {name: 'remark'},
        {name: 'salary', display: 'readOnly'},
    ];
    setEditor(account);
}

function callDefault() {
    let defaults = [
        {name: 'number', type: 'default', display: 'readOnly'},
        {name: 'name', display: 'readOnly'},
        {name: 'estimate', display: 'readOnly'},
        {name: 'value', display: 'readOnly'},
        {name: 'remark', display: 'readOnly'},
        {name: 'salary', display: 'readOnly'},
    ]
    setEditor(defaults)
}

function callState() {
    let state = [
        {name: 'number', type: 'default'},
        {name: 'name', display: ''},
        {name: 'estimate', display: '', event: [{'onblur': 'number_format(this)'}], addClasses: 'number_format'},
        {name: 'value', display: 'readOnly'},
        {name: 'remark'},
        {name: 'salary', display: 'readOnly'},
    ];
    setEditor(state)
}

function setEditor(obj) {
    obj.forEach(function (item, index) {
        if (typeof item.type !== "undefined") {
            setClassDisplay(guaranteeType, item.type);
        }
        if (item.name === 'number') {
            setEditorDisplay(guarantee, item);
        }
        if (item.name === 'name') {
            setEditorDisplay(guaranteeName, item);
        }
        if (item.name === 'estimate') {
            setEditorDisplay(guaranteeEstimate, item);
        }
        if (item.name === 'value') {
            setEditorDisplay(guaranteeAmount, item);
        }
        if (item.name === 'remark') {
            setEditorDisplay(guaranteeRemark, item);
        }
        if (item.name === 'salary') {
            setEditorDisplay(guaranteeSalary, item);
        }
    })
}

function setGuaranteeDeposit(ele) {
    new Promise((resolve, reject) => {
        $('.data .content-guarantee-type').each(function(index){
            if(parseInt($(this).val()) === 3 && parseInt($(ele).val()) === parseInt($(this).closest('.data').find('.content-guarantee-number').val())){
                reject('deposit');
                return;
            }
        });
        resolve();
        return;
    }).then(initDeposit).catch(alertGuaranteeDuplication);

}

function initDeposit() {
    new Promise(findAccountDeposit).then(setInputDisplay).then().catch((err) => {
        console.error('setGuaranteeDeposit Err =>', err)
    });
}

const depositPerGuarantee = 90;

function findAccountDeposit(resolve, reject) {
    try {
        let account_id = $('.edit-guarantee').val();
        accountList.forEach(function (account, index) {
            if (account_id === account.account_id) {
                let data = {};
                data.name = account.account_name;
                data.estimate = account.balance;
                data.amount = (parseFloat(account.balance) * depositPerGuarantee / 100);
                resolve(data);
            }
        });
        return;
    } catch (e) {
        reject(e);
        return;
    }
    return;
}


const events = ['onblur', 'onchange', 'onkeypress', 'onkeyup', 'onkeydown'];// set Event Element
const addClasses = ['number_format']; //set addClass Element
const number_format_config = ['estimate', 'value'];

function setEditorDisplay(ele, obj) {

    if (typeof obj.val !== "undefined" || obj.val !== "") {
        if (number_format_config.indexOf(obj.name) !== -1) {
            ele.val(addCommas(obj.val));
        } else {
            ele.val(obj.val)
        }
    }
    if (obj.display === 'readOnly') {
        ele.attr('readOnly', true);
    } else {
        ele.prop('readOnly', false);
    }

    if (typeof obj.event !== "undefined" && obj.event !== "") {

        obj.event.forEach(function (item, index) {
            ele.attr(Object.keys(item)[0], Object.values(item)[0]);
        });

    } else {
        events.forEach(function (event, index) {
            ele.removeAttr(event);
        })
    }

    if (typeof obj.addClasses !== "undefined" && obj.addClasses !== "") {
        if (typeof obj.addClasses === "string") {
            ele.addClass(obj.addClasses);
        }
        if (typeof obj.addClasses === 'object') {
            obj.addClasses.forEach(function (item, index) {
                ele.addClass(Object.keys(item)[0], Object.values(item)[0]);
            });
        }
    } else {
        addClasses.forEach(function (className, index) {
            ele.removeClass(className);
        });
    }
}

function guaranteeModify() {
    $('tr.data').find(':eq(1)').addClass('modify');
}

var contact = $('.content-guarantee');
var salary = $('.content-guarantee-salary');

function setClassDisplay(ele, typeName) {
    typeName = typeof typeName === "undefined" ? 'default' : typeName;
    if (typeName === 'member') {
        contact.replaceWith(displayGuaranteeMemberSearch);
        salary.replaceWith(displayGuaranteeSalary);
    } else if (typeName === 'account') {
        new Promise(getMemberAccount).then(createList, errorAccount);
    } else {
        contact.replaceWith(displayGuaranteeDefault);
        salary.replaceWith(displayGuaranteeSalaryDefault);
    }
    contact = $('.content-guarantee');
    guarantee = ele.closest('tr').find('.edit-guarantee');
}

function getMemberAccount(resolve, reject) {
    try {
        $.post(base_url + "loan_contract/getAccountList", {member_id: memberID.val()}, function (res) {
            if (res.status === 200) {
                resolve(res.data);
            } else {
                reject(`Error Ajax status: ${res.status}`);
            }
        })
    } catch (err) {
        reject(`Error during setup: ${err}`);
    }
    return;
}

var accountList = [];


function createList(accList) {

    let accountListUse = [];
    new Promise(((resolve, reject) => {
        const length = $('.data').length;
        $('.data').each(function(index){
           const type =  $(this).closest('tr').find('.content-guarantee-type').val();
           const account = $(this).closest('tr').find('.content-guarantee-number').val();
           if( parseInt(type) === 3 ){
               accountListUse.push(account)
           }

        });
        resolve(accountListUse);

    })).then((a) => {
        accountList = accList;
        let html = '<select class="form-control content-guarantee edit-guarantee" onchange="setGuaranteeDeposit(this)">';
        html += `<option value=""> เลือกบัญชีเงินฝากสหกรณ์ </option>`;
        accList.forEach(function (item, index) {
            let disabled = "";
            if(accountListUse.indexOf(item.account_id) >= 0){
                disabled = " disabled=disabled ";
            }
            html += '<option value="' + item.account_id + '" '+disabled+' >' + item.account_id + '</option>';
        });
        html += '</select>';
        contact.replaceWith(html);
        contact = $('.content-guarantee');
        guarantee = $('.edit-guarantee');
        return;
    });
}

function errorAccount(err) {
    contact.replaceWith(displayGuaranteeDefault);
    return;
}

var displayGuaranteeDefault = '<input class="form-control content-guarantee edit-guarantee" type="text" >';
var displayGuaranteeSalaryDefault = '<input class="form-control content-guarantee-salary edit-guarantee-salary" type="text" readonly>';

var displayGuaranteeMemberSearch = '\
    <div class="input-group content-guarantee">\
    <div class="row">\
        <div class="col-sm-10 content-guarantee-member-search">\
            <input class="form-control guarantee_ guarantee_person_id edit-guarantee" type="text" value="" readonly>\
        </div>\
        <div class="col-sm-2 input-group-btn btn_search_member icon-guarantee-member-search">\
            <button type="button" class="btn btn-info btn-search" onclick="search_member_modal(\'1\')">\
                <span class="icon icon-search"></span>\
            </button>\
        </div>\
    </div>\
</div>';

var displayGuaranteeSalary = '<div class="input-group content-guarantee-salary">\
<input class="form-control edit-guarantee-salary" type="text" value="" readonly>\
<span class="input-group-btn btn_search_member">\
<button type="button" class="btn btn-info btn-search" onclick="open_modal(\'update_guarantee_salary_modal\')"><span class="icon icon-pencil"></span></button>\
</span>\
</div>';

/**
 * Add List Table
 */
var guaranteeTable = $('.guarantee-table');

function getEditValue(resolve, reject) {
    try {

        let result = {};
        let editor = guaranteeTable.find('.editor');

        if (typeof editor.find('.edit-guarantee').val() === "undefined" || editor.find('.edit-guarantee').val() === "") {
            const err = {code: 400, msg: `กรุณาระบุข้อมูลหลักประกัน`};
            reject(err);
            return;
        }

        if($('#loan_type_choose').val() == 2 && $('#loan_type_select').val() == 200){ // กู้ สามัญ บุคคลค้ำ
            var $items = $('.data');
            var surety = 3 // จำนวนคนค้ำประกัน
            if($items.length >= surety){
                var count_type = 0;
                for ( var i = 0, l = $items.length; i < l; i++ ) {
                    if($("input[name='data[coop_loan_guarantee][" + i + "][type]']").val() == "1"){ // 1 คือหลักค้ำประกัน
                        count_type += 1;
                    }
                }
                if(count_type >= surety && editor.find('.edit-guarantee-type').val() == 1){
                    swal('แจ้งเตือน', 'จำนวนสมาชิกค้ำประกันเกิน ' + String(surety) + ' คน');
                    // const err = {code: 400, msg: `จำนวนสมาชิกค้ำประกันเกิน `+String(surety)+` คน`};
                    // reject(err);
                    // return;
                }
            }
        }
        result.typeName = editor.find('.edit-guarantee-type option:selected').text();
        result.typeId = editor.find('.edit-guarantee-type').val();
        result.number = editor.find('.edit-guarantee').val();
        result.name = editor.find('.edit-guarantee-name').val();
        result.estimate = editor.find('.edit-guarantee-estimate').val();
        result.amount = editor.find('.edit-guarantee-amount').val();
        result.remark = editor.find('.edit-guarantee-remark').val();
        result.salary = editor.find('.edit-guarantee-salary').val();
        resolve(result);
        return;
    } catch (e) {
        reject(`Error during :${e}`);
    }
    return;
}

function troubleMessage(reason) {

    if (typeof reason.code === "undefined") {
        console.error(`Error : ${reason}`);
    } else if (reason.code === 400) {
        swal(reason.msg, '', 'warning');
    }
}

function createRow(obj) {

    let counter = guaranteeTable.find('.editor').index();
    let editor = guaranteeTable.find('.editor');

    let row = '<tr class="data">';
    row += `<td class="text-center"><span class="text-label">${counter + 1}</span><input class="data-value" type="hidden" name="data[coop_loan_guarantee][${counter}][counter]" value="${counter}" ></td>`;
    row += `<td><span class="text-label">${obj.typeName}</span><input class="data-value content-guarantee-type" type="hidden" name="data[coop_loan_guarantee][${counter}][type]" value="${obj.typeId}" ></td>`;
    row += `<td><span class="text-label">${obj.number}</span><input class="data-value content-guarantee-number" type="hidden" name="data[coop_loan_guarantee][${counter}][number]" value="${obj.number}" ></td>`;
    row += `<td><span class="text-label">${obj.name}</span><input class="data-value content-guarantee-name" type="hidden" name="data[coop_loan_guarantee][${counter}][name]" value="${obj.name}" ></td>`;
    row += `<td><span class="text-label col-sm-8" style="padding:0px;">${addCommas(obj.estimate)}</span><input class="data-value content-guarantee-estimate" type="hidden" name="data[coop_loan_guarantee][${counter}][estimate]" value="${removeCommas(obj.estimate)}" ><label class="control-label col-sm-2" style="padding:0px; margin:0px;"> บาท</label></td>`;
    row += `<td><span class="text-label col-sm-8" style="padding:0px;">${addCommas(obj.amount)}</span><input class="data-value content-guarantee-amount" type="hidden" name="data[coop_loan_guarantee][${counter}][amount]" value="${removeCommas(obj.amount)}" ><label class="control-label col-sm-2" style="padding:0px; margin:0px;"> บาท</label></td>`;
    row += `<td><span class="text-label">${obj.remark}</span><input class="data-value content-guarantee-remark" type="hidden" name="data[coop_loan_guarantee][${counter}][remark]" value="${obj.remark}" ></td>`;
    row += `<td><span class="text-label">${addCommas(obj.salary)}</span><input class="data-value content-guarantee-salary" type="hidden" name="data[coop_loan_guarantee][${counter}][salary]" value="${removeCommas(obj.salary)}" onclick="open_modal(\'update_guarantee_salary_modal\')"></td>`;
    row += `<td>\
<button class="btn btn-info btn-smaller" type="button" onclick="move(this)"><span class="icon"><i class="fa fa-pencil"></i></span></button>\
<button class="btn btn-danger btn-smaller" type="button" onclick="remove(this)"><span class="icon"><i class="fa fa-trash"></i></span></button>\
</td>`;
    row += "</tr>";
    editor.before(row);
    //editor.find('td:first-child').text(counter+2);

    if (editor.index() !== $('.guarantee-table tbody tr').length) {
        clone = editor;
        editor.remove();
        $('.guarantee-table tbody tr:last').after(clone);
    }
    $('.editor td:first').text('#');
    $('.editor .icon .fa').removeClass('fa-save').addClass('fa-plus');

    if (parseInt(obj.typeId) === 2) {
        disableType(obj.typeId);
    }

}

/**
 * Clear Input Editor Guarantee
 * @param ignore object type
 *        support array object ex. ['apple', 'banana', 'foo', 'bar']
 */
function clearData(ignore) {
    ignore = typeof ignore  === "object" ? ignore : [];
    new Promise((resolve, reject) => {
        try {
            $('[class*="edit-guarantee"]').each(function () {
                if(ignore.length > 0){
                    new Promise((resolve) => {
                        $(this).attr("class").split(" ").forEach(function(item, index){
                            if(item.search('edit-guarantee') !== -1){
                                $(this).val('');
                            }
                        });
                    }).then((key) => {
                        if(ignore.indexOf(key) === -1){
                            $(this).val('');
                        }
                    });
                }else{
                    $(this).val('');
                }
            });
            resolve(true);
            return;
        } catch (e) {
            reject(e);
            return;
        }
    }).then(callDefault).catch((err) => {
        // console.error(err);
    })
}

function addGuaranteeType() {
    isModify = true;
    new Promise(getEditValue)
        .then(createRow)
        .then(clearData)
        .catch(troubleMessage)
        .finally(() => {
        })
}

var previousDeduct = $('.previous-deduct');

function deductDefault() {
    if(PrevLoan === false) return;
    previousDeduct.each(function () {
        if (preferences && $(this).data('type') === parseInt(preferences.type_id)) {
            $(this).prop('checked', true);
        }
    });
}

function deductReset(){
    previousDeduct.each(function(){
        $(this).prop('checked', false);
    });
}

/**
 *  Modify Guarantee
 **/

/**
 *
 * @type {null}
 */
var clone = null;


var isModify = true;
var _current = null;

function move(element) {
    const selector = $(element).closest('tr.data');
    let number = selector.index();
    const typeId = selector.find('.content-guarantee-type').val();

    new Promise((resolve => {
        if (isModify === false) {
            addGuaranteeType();
        }
        isModify = false;
        setTimeout(function () {
            resolve(isModify);
            return;
        }, 300);
    })).then((aBoolen) => {
        new Promise(resolve => {
            changeInput(typeId)
            setTimeout(function () {
                resolve();
                return;
            }, 300);
        }).then(() => {
            clone = $('tr.editor').clone();
            $('tr.editor').remove();
            let selector = $('tr.data:eq(' + number + ')');
            selector.after(clone);
            copy(number);
            selector.remove();
        })

    })
}

function copy(index) {

    let selector = $('tr.data:eq(' + index + ')');
    let typeId = selector.find('.content-guarantee-type').val();
    let number = selector.find('.content-guarantee-number').val();
    let name = selector.find('.content-guarantee-name').val();
    let estimate = selector.find('.content-guarantee-estimate').val();
    let amount = selector.find('.content-guarantee-amount').val();
    let remark = selector.find('.content-guarantee-remark').val();
    let salary = selector.find('.content-guarantee-salary').val();

    let editor = $('.editor');

    guaranteeType = $('.edit-guarantee-type');
    guarantee = $('.edit-guarantee');
    guaranteeName = $('.edit-guarantee-name');
    guaranteeEstimate = $('.edit-guarantee-estimate');
    guaranteeAmount = $('.edit-guarantee-amount');
    guaranteeRemark = $('.edit-guarantee-remark');
    guaranteeSalary = $('.edit-guarantee-salary');
    guaranteeSalaryUpdate = $('.update-guarantee-salary');


    editor.find('td:first').text(index + 1);
    editor.find('.edit-guarantee-type').val(typeId);
    editor.find('.edit-guarantee').val(number);
    editor.find('.edit-guarantee-name').val(name);
    editor.find('.edit-guarantee-estimate').val(estimate);
    editor.find('.edit-guarantee-amount').val(amount);
    editor.find('.edit-guarantee-remark').val(remark);
    editor.find('.edit-guarantee-salary').val(salary);
    editor.find('.icon .fa').addClass('fa-save').removeClass('fa-plus');

    if (typeId === '2') {
        enableType(typeId);
    }

}

function remove(ele) {
    let index = $(ele).closest('tr.data').index();
    new Promise(resolve => {
        if ($('.guarantee-table tbody tr:eq(' + index + ') .content-guarantee-type').val() === '2') {
            enableType(2);
        }
        $('.guarantee-table tbody tr:eq(' + index + ')').remove();
        calc_personal_guarantee();
        setTimeout(function () {
            resolve();
        }, 150);
    }).then(triggerEditor).then(sortTableGuarantee)
}

function triggerEditor() {
    $('.edit-guarantee-type').trigger('change');
}

function change() {

}

function add() {

}

function addDeductInput(index, obj, dataType) {

    let amount = removeCommas(obj.interest) + removeCommas(obj.prev_loan_total);

    $('#form_contract').append(`<input class="prev_contract prev_contract_interest_hidden" type="hidden" id="prev_contract_interest_`+obj.ref_id+`" name="prev_loan[${index}][interest]" value="${obj.interest}" >`);
    $('#form_contract').append(`<input class="prev_contract prev_contract_balance_hidden" type="hidden" id="prev_contract_balance_`+obj.ref_id+`" name="prev_loan[${index}][amount]" value="${amount}" >`);
    $('#form_contract').append(`<input class="prev_contract" type="hidden" name="prev_loan[${index}][id]" value="${obj.ref_id}" >`);
    $('#form_contract').append(`<input class="prev_contract" type="hidden" name="prev_loan[${index}][pay_type]" value="all" >`);
    $('#form_contract').append(`<input class="prev_contract" type="hidden" name="prev_loan[${index}][type]" value="${dataType}" >`);

}

var deductList = [];

function prevDeduct() {
    deduct = 0;
    payment_loan_down_cal = removeCommas(payment_loan_down);
    principal_down_cal = removeCommas(principal_down);   
    interest_down_cal = removeCommas(interest_down);
    new Promise((resolve, reject) => {
        let data = {};
        data.member_id = memberID.val();
        data.createdatetime = $('#createdatetime').val();
        if($('#loan_id').val()) {
            console.log($('#loan_id').val())
            data.loan_id = $('#loan_id').val()
        }
        $.post(base_url + '/loan_contract/get_check_prev_loan', data, function (res) {
            if (res.status === 200) {
                resolve(res.data);
            } else {
                reject(`something wrong`);
            }
        });
    }).then((data) => {
        $('.prev_contract').remove();
        deduct = 0;

        previousDeduct.each(function (idx) {
            let _ref_id = $(this).val();
            let num = 0;
            const dataType = $(this).attr('data-type') === '99' ? 'atm' : 'loan';
            if ($(this).is(':checked') === true) {
                data.forEach(function (item, index) {
                    if (_ref_id === item.ref_id) {
                        let prevLoan = removeCommas(item.prev_loan_total);
                        let interest = removeCommas(item.interest);
                        let result = parseFloat(prevLoan + interest);
                        let money_per_period = removeCommas(item.money_per_period);
                        let interest_cal_loan = removeCommas(item.interest_cal_loan);
                        $("#contract_balance_input_"+item.ref_id).val(addCommas(item.loan_amount_balance));
                        $("#contract_interest_input_"+item.ref_id).val(addCommas(interest));
                        deduct += result;
                        // payment_loan_down_cal -= parseFloat(money_per_period + interest_cal_loan);
                        payment_loan_down_cal -= parseFloat(money_per_period);
                        payment_loan_down_cal = round(payment_loan_down_cal,2);
                        principal_down_cal -= parseFloat(removeCommas(item.money_per_period));
                        interest_down_cal -= parseFloat(removeCommas(item.interest_cal_loan));
                        interest_down_cal = round(interest_down_cal,2);
                        addDeductInput(idx, item, dataType);                 
                        num++;
                    }
                });
            }
        });
        return;
    }).then(() => {
        calcDeduct();
        calcEstimateReceive();
		cal_estimate_money();
        update_sum_cost_balance();
        update_cost();
    }).catch((err) => {
        // console.error(err);
    });
}

$(document).on('change', '.previous-deduct', function () {
    prevDeduct();
});

$(document).on('change', '#loan_amount, #approve_date', function () {
    calcProcessing();
});

function search_member_modal(id) {
    $('#input_id').val(id);
    $('#search_member_loan_modal').modal('show');
}

$('#member_loan_search').click(function () {
    if ($('#member_search_list').val() == '') {
        swal('กรุณาเลือกรูปแบบค้นหา', '', 'warning');
    } else if ($('#member_search_text').val() == '') {
        swal('กรุณากรอกข้อมูลที่ต้องการค้นหา', '', 'warning');
    } else {
        $.ajax({
            url: base_url + "ajax/search_member_by_type_jquery",
            method: "post",
            data: {
                search_text: $('#member_search_text').val(),
                search_list: $('#member_search_list').val()
            },
            dataType: "text",
            success: function (data) {
                $('#result_member_search').html(data);
            },
            error: function (xhr) {
                console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
            }
        });
    }
});

$('#search_member_loan_modal').on('hide.bs.modal', function () {
    $(".blockUI.blockOverlay").remove();
});


function get_data(member_id, member_name, member_group) {
    const guarantor = $('.member_id').val();
    new Promise(((resolve, reject) => {
        $.post(base_url + "/ajax/get_member",
            {
                member_id: member_id,
                guarantor: guarantor,
                for_loan: '1',
                loan_type: $('#loan_type_select').val(),
                loan_member_id: $('.member_id').val(),
                value_check: member_id,
                not_condition: () => {
                    var arr = [];
                    not_condition.forEach(element => {
                        arr.push(element.value);
                    });
                    return arr.join(",");
                },
                condition_garantor_id: condition_garantor_id
            }
            , function (result, status, xhr) {

                var obj = JSON.parse(result);
                if (obj.message.text === "") {
                    obj.member_id = member_id;
                    obj.member_name = member_name;
                    resolve(obj);
                    return;
                } else {
                    reject(obj);
                    return;
                }
            }); 

    })).then(guaranteePersonalDuplication)
        .then(guaranteePersonalSet) // Set ค่า
        .then(alertGuaranteeDuplication)
        .catch(alertGuaranteeDuplication);
}

var multiple_guarantee_estimate = 0;

function guaranteePersonalSet(obj) {
    if (obj === false) {
        return false;
    } else {

        if (typeof obj === 'object' && obj !== null) {
            if (obj.check_id != null) {
                not_condition.push({key: obj.member_id, value: obj.check_id});
            }
            result = obj.message;
        }

        if($('#loan_type_choose').val() == 2 && $('#loan_type_select').val() == 2){ // กู้สามัญ บุคคลค้ำ
            if(parseInt(obj.check_surety) >= parseInt(obj.term_of_loan['num_guarantee'])){ // ภาระค้ำเกิน
                swal('แจ้งเตือน', 'ภาระค้ำประกันเกิน ' + obj.term_of_loan['num_guarantee'], 'warning');
                return;
            }
            // else if(obj.data['loan_guarantee_person'] == 0){
            //     swal('แจ้งเตือน', 'สิทธิค้ำ/ประเมิน ครบกำหนด', 'warning');
            // }
        }

        multiple_guarantee_estimate = obj.data['multiple_guarantee_estimate'];
        guaranteeEstimate.val(addCommas(obj.data['loan_guarantee_person'] < 0 ? 0 : obj.data['loan_guarantee_person']));
        guaranteeEstimate.trigger("change");
        guaranteeAmount.val(addCommas(0.00));
        guaranteeAmount.trigger("change");

        guarantee.val(obj.member_id);
        guaranteeName.val(obj.member_name);
        guaranteeSalary = $('.edit-guarantee-salary');
        guaranteeSalary.val(obj.data['salary_start']);

        $('#search_member_loan_modal').modal('hide');

        calc_personal_guarantee(1);

        return;
    }
}

function guaranteePersonalDuplication(data) {
    var checkPersonalDup = function (resolve, reject) {
        let status = false;
        const count = $('.content-guarantee-type').length;
        if (count === 0) resolve(data);
        $('.content-guarantee-type').each(function (i) {
            if ($(this).val() === "1") {
                if ($(this).closest('tr').find('.content-guarantee-number').val() === data.member_id) {
                    status = true;
                }
            }
            if (i + 1 === count) {
                if (status) {
                    resolve(false);
                } else {
                    resolve(data);
                }
            }
        });
        reject(data);
        return;
    }
    return new Promise(checkPersonalDup);
}

function alertGuaranteeDuplication(err) {

    const ignore = ['edit-guarantee-type', 'edit-guarantee'];
    if(typeof err === "object" && err.message.text !== ""){
        swal( err.message.title, err.message.text, 'error');
        clearData(ignore);
    }
    if (err === false) {
        swal("ไม่สามารถเลือกผู้ค้ำประกันคนเดิมได้", "", 'error');
        clearData(ignore);
    }
    if(err === 'deposit'){
        // swal("ไม่สามารถเลือกบัญชีค้ำประกันเดิมได้", "", 'error');
        clearData(ignore);
    }

    return;
}

function calc_personal_guarantee(flag) {
    flag = typeof flag === "undefined" ? 0 : flag;
    new Promise((resolve, reject) => {
        const count = $('.content-guarantee-type').length;
        let person = 0;
        if (count >= 1) {
            $('.content-guarantee-type').each(function (i) {
                if ($(this).val() === "1") {
                    person += 1;
                }
                if (i + 1 === count) {
                    resolve(person + flag);
                }
            });
        } else {
            resolve(person + flag);
        }
    }).then(updateEstimateGuaranteePerson);
}

function updateEstimateGuaranteePerson(person) {
    return;
    let calEstimate = (loanAmount - (loanAmount % person)) / person;
    let fraction = (loanAmount % person);

    const personCount = $('.content-guarantee-type').length;
    if (personCount > 0) {
        $('.content-guarantee-type').each(function (i) {
            if ($(this).val() === "1") {
                let estimate = $(this).closest('tr').find('.content-guarantee-estimate');
                let estimateLabel = estimate.closest('td').find('.text-label');
                let amount = $(this).closest('tr').find('.content-guarantee-amount');
                let amountLabel = amount.closest('td').find('.text-label');
                if (fraction > 0) {
                    estimateLabel.text(addCommas(calEstimate + 1));
                    estimate.val(calEstimate + 1);
                    amountLabel.text(addCommas(calEstimate + 1));
                    amount.val(calEstimate + 1);
                    --fraction;
                } else {
                    estimateLabel.text(addCommas(calEstimate));
                    estimate.val(calEstimate);
                    amountLabel.text(addCommas(calEstimate));
                    amount.val(calEstimate);
                }

            }
        });
    }
    if ($('.edit-guarantee-type').val() === "1" && $('.edit-guarantee').val() !== "") {
        if (fraction > 0) {
            $('.edit-guarantee-estimate').val(addCommas(calEstimate + 1));
            $('.edit-guarantee-amount').val(addCommas(calEstimate + 1));
            --fraction;
        } else {
            $('.edit-guarantee-estimate').val(addCommas(calEstimate));
            $('.edit-guarantee-amount').val(addCommas(calEstimate));
        }
    }
}

/**
 *  Transfer Type
 */
$(document).on('change', '#transfer_type', function () {
    if ($(this).val() === '0') {
        $('.content-deposit').hide();
        $('.content-bank').hide();
    } else if ($(this).val() === '1') {
        $('.content-deposit').show();
        $('.content-bank').hide();
    } else if ($(this).val() === '2') {
        $('.content-deposit').hide();
        $('.content-bank').show();
    } else if ($(this).val() === '3') {
        $('.content-deposit').hide();
        $('.content-bank').hide();
    } else {
        $('.content-deposit').hide();
        $('.content-bank').hide();
    }
});

function confirm() {
    release();
    if(!checkForm()){
        return false;
    }else {
        setTimeout(function () {
            $('#form_contract').submit();
        }, 200);
    }
}

function getContractData() {
    const loadDataContract = function (resolve, reject) {
        $.post(base_url + "/loan/ajax_get_loan_data", {loan_id: $('#loan_id').val()}, function (result) {
            let obj = JSON.parse(result);
            resolve(obj);
            return;
        }).error(function (xhr) {
            reject(xhr.error());
            return;
        });
    };
    return new Promise(loadDataContract);
}

var PrevLoan = true;
var coopLoan = {};
function setContract(result) {
    new Promise((resolve) => {
        resolve(result)
    }).then((data) => {
        coopLoan = data.coop_loan;
        setContractDetail(data.coop_loan);
        return data;
    }).then((data) => {
        setPrevLoan(data.coop_loan_prev_deduct);
        return data;
    }).then((data) => {
        setCost(data.coop_loan_cost);
        return data;
    }).then((data) => {
        setDeduct(data.coop_loan_deduct);
        return data;
    }).then((data) => {
        setInsurance(data.coop_life_insurance);
        return data;
    });
}

function setContractDetail(obj) {
    $('#loan_id').val(obj.id);
    $('#petition_number').val(obj.petition_number);
    $('#loan_amount').val(addCommas(obj.loan_amount));
    $('#loan').val(obj.loan_amount);
    $('#salary').val(obj.salary);
    $('#loan_reason').val(obj.loan_reason);
    $('#interest_per_year').val(obj.interest_per_year);
    $('#period_amount').val(obj.period_amount);
    $('#interest').val(obj.interest_per_year);
    $('#createdatetime').val(obj.createdatetime);

    $('#approve_date').val(format_date(obj.date_last_interest));
    $('#transfer_type').val(obj.transfer_type);
    $('#transfer_bank_id').val(obj.transfer_bank_id);
    $('#transfer_bank_account_id').val(obj.transfer_bank_account_id);
    $('#transfer_type').trigger("change");
    setPeriodAMT(obj.period_amount);
    setPayType(obj.pay_type);
    setLoanAmount(obj.loan_amount);
    //calcProcessing();
}

function setPrevLoan(obj) {
    PrevLoan = false;
    obj.forEach(function(object, num){
        $('.previous-deduct').each(function(index, item){
            if($(this).val() === object.ref_id && $(this).prop('checked')  !== true){
                $(this).prop('checked', true);
            }
        });
    });
}

function setGuaranteePersonal() {
    const id = $('#loan_id').val();
    if (typeof id !== "undefined" || id !== "") {
        new Promise(((resolve, reject) => {
            $.post(base_url + "loan_contract/getloanguarantee", {loan_id: id}, function (res, status, xhr) {
                if (res.status == 200) {
                    resolve(res.data)
                } else {
                    reject(xhr.responseText);
                }
            })
        })).then((res) => {
            for (let i in res) {
                createRow(res[i]);
            }
        }).catch(err => {
            console.log(err);
        });
    }
}


function setCost(obj) {
    Object.keys(obj).map((key, indx) => {
        $("input[name='data[coop_loan_cost]["+key+"]']").val(addCommas(obj[key]));
    });

}

function selected_loan_type(id) {
    $.get(base_url + "loan/get_loan_type", {id: id}, function (res) {
        $('#loan_type_choose').val(res.ref_id).trigger('change');
        setTimeout(function () {
            $('#loan_type_select').val(res.id);
        }, 800);
    });
}

function open_modal(id) {
    document.getElementById("update_guarantee_member").value = $('.edit-guarantee').val();
    document.getElementById("update_guarantee_salary").value = $('.edit-guarantee-salary').val();
    $('#' + id).modal('show');
}

function close_modal(id) {
    $('#' + id).modal('hide');
}

function sortTableGuarantee() {
    $(".guarantee-table .data").each(function (index) {
        $(this).find('td:first').text(index + 1);
    });
}

function disableType(type) {
    $('.edit-guarantee-type option[value="' + type + '"]').attr('disabled', 'disabled');
}

function enableType(type) {
    $('.edit-guarantee-type option[value="' + type + '"]').removeAttr('disabled');
}
var firstSet = true;
function calcCost(){
    if(!firstSet) return;
    let costTotal = 0;
    let netBalance = $('.net_balance');
    let money_use_balance_baht = removeCommas(preferences.money_use_balance_baht);
    let result = 0;
    $('.loan_cost').each(function(index){
        costTotal += removeCommas($(this).val());
    });
    result = removeCommas($("input[name='data[coop_loan][salary]']").val()) - (costTotal + money_use_balance_baht);
    netBalance.val(addCommas(result));
}
function setDeduct(obj){
    if(typeof obj === "undefined" || obj.length === 0){
        return;
    }
    obj.forEach((item, index) => {
        $("input[name='data[loan_deduct]["+item.loan_deduct_list_code+"]']").val(addCommas(item.loan_deduct_amount));
    });
}

function setPayType(payType){
    payType = typeof payType === "undefined" ? 2 : payType;
    payType = typeof payType === "string" ? parseInt(payType) : payType;
    $("input[name='data[coop_loan][pay_type]'][value='"+payType+"']").prop('checked', true);
}

function setInsurance(obj) {
    if( obj === null) return;
    if(obj.insurance_amount !==  "" && obj.insurance_amount !== null){
        $("input[name='data[coop_left_insurance]']").val(addCommas(obj.insurance_amount));
    }
}

function update_salary() {
    update_income();
    let income = 0;
    $('.income').each(function(){
         income += parseFloat(removeCommas($(this).val()));
    });
    let salary = parseFloat(removeCommas($('#update_salary').val()));
    $('input[name="data[coop_loan][salary]"]').val(addCommas(salary+income));
    $.post(base_url + "/loan/update_salary",
        {
            member_id: $('.member_id').val(),
            salary: $('#update_salary').val(),
            other_income: $('#update_other_income').val()
        }
        , function (result) {
            close_modal('update_salary_modal');
            update_net();
        });
}

function update_guarantee_salary() {
    guaranteeSalary = $('.edit-guarantee-salary');
    guaranteeSalary.val($('#update_guarantee_salary').val());
    $.post(base_url + "/loan/update_salary",
        {
            member_id: $('#update_guarantee_member').val(),
            salary: $('#update_guarantee_salary').val()
        }
        , function (result) {
            close_modal('update_guarantee_salary_modal');
            get_data(String(guarantee.val()), String(guaranteeName.val()), '');// เรียกใช้ฟังก์ชั่น กรณีไม่มีเงินเดือน
        });
}

function update_net(){
    let net = 0;
    $('.payment_per_month').each(function(){
        net += numeral($(this).val()).value();
    });
    let balance = numeral($("input[name='data[coop_loan][salary]']").val()).value();
    balance -= net;
    $('.net_balance').val(numeral(balance).format('0,0.00'));
    update_sum_cost_balance();
    //$("#loan_amount").trigger("blur");
}

function update_income(){
    $('.income').each(function(){
        var income_id = $(this).data("key");
        var income_value = numeral($(this).val()).value();
        $.post(base_url+"/loan_contract/update_income",
            {
                member_id: $('.member_id').val(),
                income_id: income_id,
                income_value: income_value
            }
            , function(result){
            });
    });

}

$("body").on("change", ".income", function() {
    var key = $(this).data("key");
    var number = numeral($(this).val()).value();
    $("input[name='income["+key+"]']").val(number);
});

function format_date(str){
    let _rev = str.split('-').reverse();
    _rev[2] = parseInt(_rev[2])+543;
    return _rev.join("/");
}
$("body").on("change", ".loan_cost", function() {
    var key = $(this).data("key");
    var number = numeral($(this).val()).value();
    $("input[name='data[coop_loan_cost]["+key+"]']").val(number);
});
function update_cost() {
    let loan_cost = 0;
    $('.loan_cost').each(function(){
        loan_cost += parseFloat(removeCommas($(this).val()));
    });
    $('input[name="data[coop_loan_cost][OTH]"]').val(addCommas(loan_cost));
    update_sum_cost_balance();
    update_net();
    close_modal('payment_cost');
}
function update_sum_cost_balance(){
    let net = 0;
    $('.payment_per_month').each(function(){
        net += numeral($(this).val()).value();
    });
    $('.sum_cost_balance').val(numeral(net).format('0,0.00'));
}
function installment_schedule(print){
    let type = ($('#pay_type_1').is(":checked"))?1:2;
    let data ={
        member_id:$('.member_id').val(),
        interest:interest,
        loan_amount:removeCommas($('#loan_amount').val()),
        pay_amount:$('#pay_amount').val(),
        date_start:$('#date_receive_money').val(),
        period:$('#period_amount').val(),
        pat_type:type
    }
    if(print){
        location.replace(base_url+'loan_contract/installment_schedule?'+$.param(data)+'&print=true');
    }else{
        $.get(base_url+'loan_contract/installment_schedule?'+$.param(data)).done(e=>{
            $('#installment_schedule_modal_body').html(e);
            open_modal('installment_schedule');
        })
    }
}

function calculate_loan_deduct() {
    deduct = 0;
    $('.prev_contract_balance_hidden').each(function(){
        deduct += numeral($(this).val()).value();
    });
}

function add_loan_reason(e){
    e.preventDefault();
    let reason = $('#tv_loan_reason').val();
    
    if(reason == ""){
        $("#tv_loan_reason").css('border-color','red');
        $("#show_alert").show();
        return false;
    }
    $.post(base_url+'loan_contract/add_loan_reason',$('#form_add_loan_reason').serialize()).done(e=>{
        let R = JSON.parse(e);
        let mkOP = (k,val)=>{    
            return `<option value='${k}'>${val} </option>`;
        }
        if(R.result =='true'){
            $('#loan_reason').append(mkOP(R.ID,R.text));
            $('#modal_add_loan_reason').modal('hide');
            toastNotifications('เพิ่ม เหตุผลการกู้เงิน สำเร็จ');
            $('#loan_reason').val(R.ID);
            $('#loan_reason').focus();
        }
        $('#')
    })
}
function reset_modal() {
    $("#tv_loan_reason").css('border-color','black');
    $("#show_alert").hide();
    $('#tv_loan_reason').val('');
}
function change_page_to(url) {
    window.location.href = base_url+url; 
} 
function openInNewTab(url) {
    window.open(baseurl+url, '_blank').focus();
}  