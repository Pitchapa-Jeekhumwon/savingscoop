var temp = new Array();
//transaction_time
function inline_transaction_time(transaction_id,transaction_time) {
    var arr_transaction_time = transaction_time.split(" ");
    var date_arr = arr_transaction_time[0].split('-');
    var data_date = date_arr[2]+'/'+date_arr[1]+'/'+(parseInt(date_arr[0])+543);
    var data_time = arr_transaction_time[1];
    temp['inline_transaction_time'] = $('.inline_transaction_time[data-inline_transaction_time="' + transaction_id + '"]').html();

    var html = `<div class="input-with-icon g24-col-sm-16">
                    <div class="form-group">
                        <input id="transaction_time_`+transaction_id+`" name="transaction_time" class="form-control m-b-1 datepicker_recurring_start" style="padding-left: 50px;" type="text" value="`+data_date+`" data-date-language="th-th" autocomplete="off">
                        <span class="icon icon-calendar input-icon m-f-1"></span>
                    </div>
                    <div class="input-with-icon">
                    <div class="form-group">
                        <input id="inline_time_`+transaction_id+`" name="inline_time" class="form-control m-b-1 timepicker_recurring_start" type="text" value="`+data_time+`">
                        <span class="icon icon-clock-o input-icon"></span>
                    </div>
                </div>
                </div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="save_inline_transaction_time(`+transaction_id+`)"><i class="fa fa-check" aria-hidden="true"></i></a></div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="dimiss_inline_transaction_time(`+transaction_id+`)"><i class="fa fa-times" aria-hidden="true" style="color: red !important;"></i></a></div>
        `;
    $('.inline_transaction_time[data-inline_transaction_time="' + transaction_id + '"]').html(html);
    $("#transaction_time_"+transaction_id).focus();
    $('html, body').animate({
        scrollTop: $(".row_id_"+transaction_id).offset().top
    }, 1000)

}

function save_inline_transaction_time(transaction_id){
    var inline_date = $("#transaction_time_"+transaction_id).val();
    var inline_time = $("#inline_time_"+transaction_id).val();
    var token = $('.inline_transaction_time[data-inline_transaction_time="' + transaction_id + '"]').data("token");
    $.ajax({
        url: base_url+'/Save_money/inline_update',
        method: 'POST',
        data: {
            method  : "transaction_time",
            transaction_id : transaction_id,
            inline_date : inline_date,
            inline_time : inline_time,
            token : token
        },
        async:false,
        success: function(res){
            console.log(res.result);
            if(res.result){
                $('.inline_transaction_time[data-inline_transaction_time="' + transaction_id + '"]').html(temp['inline_transaction_time']);
                $('.inline_date[data-inline_transaction_time="' + transaction_id + '"]').html(res.message);
            }else{

            }
        }
    });
}

function dimiss_inline_transaction_time(transaction_id){
    $('.inline_transaction_time[data-inline_transaction_time="' + transaction_id + '"]').html(temp['inline_transaction_time']);
}

//withdrawal
function inline_transaction_withdrawal(transaction_id) {
    temp['inline_transaction_withdrawal'] = $('.inline_transaction_withdrawal[data-inline_transaction_time="' + transaction_id + '"]').html();

    var html = `<div class="g24-col-sm-16">
                    <div class="form-group">
                        <input id="transaction_withdrawal" name="transaction_withdrawal" class="form-control m-b-1 numeral" type="text" autocomplete="off">
                    </div>
                </div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="save_inline_transaction_withdrawal(`+transaction_id+`)"><i class="fa fa-check" aria-hidden="true"></i></a></div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="dimiss_inline_transaction_withdrawal(`+transaction_id+`)"><i class="fa fa-times" aria-hidden="true" style="color: red !important;"></i></a></div>
        `;
    $('.inline_transaction_withdrawal[data-inline_transaction_time="' + transaction_id + '"]').html(html);
}

function save_inline_transaction_withdrawal(transaction_id){
    var transaction_withdrawal = $("#transaction_withdrawal").val();
    var token = $('.inline_transaction_withdrawal[data-inline_transaction_time="' + transaction_id + '"]').data("token");
    $.ajax({
        url: base_url+'/Save_money/inline_update',
        method: 'POST',
        data: {
            method  : "transaction_withdrawal",
            transaction_id : transaction_id,
            inline_withdrawal : transaction_withdrawal,
            token : token
        },
        async:false,
        success: function(res){
            console.log(res.result);
            if(res.result){
                $('.inline_transaction_withdrawal[data-inline_transaction_time="' + transaction_id + '"]').html(temp['inline_transaction_withdrawal']);
                $('.inline_withdrawal[data-inline_transaction_time="' + transaction_id + '"]').html(res.message);
            }else{

            }
        }
    });
}

function dimiss_inline_transaction_withdrawal(transaction_id){
    $('.inline_transaction_withdrawal[data-inline_transaction_time="' + transaction_id + '"]').html(temp['inline_transaction_withdrawal']);
}

//deposit
function inline_transaction_deposit(transaction_id) {
    temp['inline_transaction_deposit'] = $('.inline_transaction_deposit[data-inline_transaction_time="' + transaction_id + '"]').html();

    var html = `<div class="g24-col-sm-16">
                    <div class="form-group">
                        <input id="transaction_deposit" name="transaction_deposit" class="form-control m-b-1 numeral" type="text" autocomplete="off">
                    </div>
                </div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="save_inline_transaction_deposit(`+transaction_id+`)"><i class="fa fa-check" aria-hidden="true"></i></a></div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="dimiss_inline_transaction_deposit(`+transaction_id+`)"><i class="fa fa-times" aria-hidden="true" style="color: red !important;"></i></a></div>
        `;
    $('.inline_transaction_deposit[data-inline_transaction_time="' + transaction_id + '"]').html(html);
}

function save_inline_transaction_deposit(transaction_id){
    var transaction_deposit = $("#transaction_deposit").val();
    var token = $('.inline_transaction_deposit[data-inline_transaction_time="' + transaction_id + '"]').data("token");
    $.ajax({
        url: base_url+'/Save_money/inline_update',
        method: 'POST',
        data: {
            method  : "transaction_deposit",
            transaction_id : transaction_id,
            inline_deposit : transaction_deposit,
            token : token
        },
        async:false,
        success: function(res){
            console.log(res.result);
            if(res.result){
                $('.inline_transaction_deposit[data-inline_transaction_time="' + transaction_id + '"]').html(temp['inline_transaction_deposit']);
                $('.inline_deposit[data-inline_transaction_time="' + transaction_id + '"]').html(res.message);
            }else{

            }
        }
    });
}

function dimiss_inline_transaction_deposit(transaction_id){
    $('.inline_transaction_deposit[data-inline_transaction_time="' + transaction_id + '"]').html(temp['inline_transaction_deposit']);
}

//balance
function inline_transaction_balance(transaction_id) {
    temp['inline_transaction_balance'] = $('.inline_transaction_balance[data-inline_transaction_time="' + transaction_id + '"]').html();

    var html = `<div class="g24-col-sm-16">
                    <div class="form-group">
                        <input id="transaction_balance" name="transaction_balance" class="form-control m-b-1 numeral" type="text" autocomplete="off">
                    </div>
                </div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="save_inline_transaction_balance(`+transaction_id+`)"><i class="fa fa-check" aria-hidden="true"></i></a></div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="dimiss_inline_transaction_balance(`+transaction_id+`)"><i class="fa fa-times" aria-hidden="true" style="color: red !important;"></i></a></div>
        `;
    $('.inline_transaction_balance[data-inline_transaction_time="' + transaction_id + '"]').html(html);
}

function save_inline_transaction_balance(transaction_id){
    var transaction_balance = $("#transaction_balance").val();
    var token = $('.inline_transaction_balance[data-inline_transaction_time="' + transaction_id + '"]').data("token");
    $.ajax({
        url: base_url+'/Save_money/inline_update',
        method: 'POST',
        data: {
            method  : "transaction_balance",
            transaction_id : transaction_id,
            inline_balance : transaction_balance,
            token : token
        },
        async:false,
        success: function(res){
            console.log(res.result);
            if(res.result){
                $('.inline_transaction_balance[data-inline_transaction_time="' + transaction_id + '"]').html(temp['inline_transaction_balance']);
                $('.inline_balance[data-inline_transaction_time="' + transaction_id + '"]').html(res.message);
            }else{

            }
        }
    });
}

function dimiss_inline_transaction_balance(transaction_id){
    $('.inline_transaction_balance[data-inline_transaction_time="' + transaction_id + '"]').html(temp['inline_transaction_balance']);
}

//accu_int_item 
function inline_accu_int_item(transaction_id) {
    temp['inline_accu_int_item'] = $('.inline_accu_int_item[data-inline_transaction_time="' + transaction_id + '"]').html();

    var html = `<div class="g24-col-sm-16">
                    <div class="form-group">
                        <input id="accu_int_item" name="accu_int_item" class="form-control m-b-1 numeral" type="text" autocomplete="off">
                    </div>
                </div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="save_inline_accu_int_item(`+transaction_id+`)"><i class="fa fa-check" aria-hidden="true"></i></a></div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="dimiss_inline_accu_int_item(`+transaction_id+`)"><i class="fa fa-times" aria-hidden="true" style="color: red !important;"></i></a></div>
        `;
    $('.inline_accu_int_item[data-inline_transaction_time="' + transaction_id + '"]').html(html);
}

function save_inline_accu_int_item(transaction_id){
    var accu_int_item = $("#accu_int_item").val();
    var token = $('.inline_accu_int_item[data-inline_transaction_time="' + transaction_id + '"]').data("token");
    $.ajax({
        url: base_url+'/Save_money/inline_update',
        method: 'POST',
        data: {
            method  : "accu_int_item",
            transaction_id : transaction_id,
            inline_accu_int : accu_int_item,
            token : token
        },
        async:false,
        success: function(res){
            console.log(res.result);
            if(res.result){
                $('.inline_accu_int_item[data-inline_transaction_time="' + transaction_id + '"]').html(temp['inline_accu_int_item']);
                $('.inline_accu_int[data-inline_transaction_time="' + transaction_id + '"]').html(res.message);
            }else{

            }
        }
    });
}

function dimiss_inline_accu_int_item(transaction_id){
    $('.inline_accu_int_item[data-inline_transaction_time="' + transaction_id + '"]').html(temp['inline_accu_int_item']);
}

//old_acc_int 
function inline_old_acc_int(transaction_id) {
    temp['inline_old_acc_int'] = $('.inline_old_acc_int[data-inline_transaction_time="' + transaction_id + '"]').html();

    var html = `<div class="g24-col-sm-16">
                    <div class="form-group">
                        <input id="old_acc_int" name="old_acc_int" class="form-control m-b-1 numeral" type="text" autocomplete="off">
                    </div>
                </div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="save_inline_old_acc_int(`+transaction_id+`)"><i class="fa fa-check" aria-hidden="true"></i></a></div>
                <div class="g24-col-sm-4" style="padding-top: 8px;"><a href="#" onclick="dimiss_inline_old_acc_int(`+transaction_id+`)"><i class="fa fa-times" aria-hidden="true" style="color: red !important;"></i></a></div>
        `;
    $('.inline_old_acc_int[data-inline_transaction_time="' + transaction_id + '"]').html(html);
}

function save_inline_old_acc_int(transaction_id){
    var old_acc_int = $("#old_acc_int").val();
    var token = $('.inline_old_acc_int[data-inline_transaction_time="' + transaction_id + '"]').data("token");
    $.ajax({
        url: base_url+'/Save_money/inline_update',
        method: 'POST',
        data: {
            method  : "old_acc_int",
            transaction_id : transaction_id,
            inline_old_acc_int : old_acc_int,
            token : token
        },
        async:false,
        success: function(res){
            console.log(res.result);
            if(res.result){
                $('.inline_old_acc_int[data-inline_transaction_time="' + transaction_id + '"]').html(temp['inline_old_acc_int']);
                $('.inline_old_acc[data-inline_transaction_time="' + transaction_id + '"]').html(res.message);
            }else{

            }
        }
    });
}

function dimiss_inline_old_acc_int(transaction_id){
    $('.inline_old_acc_int[data-inline_transaction_time="' + transaction_id + '"]').html(temp['inline_old_acc_int']);
}

$(document).ready(function () {
    $('body').on('focus', ".datepicker_recurring_start", function () {
        $(this).datepicker();
    });

    $("body").on("change", ".numeral", function () {
        console.log("numeral", $(this).val());
        var number = numeral(numeral($(this).val()).value()).format('0,0.00');
        $(this).val(number);
    });
});