function removeCommas(str) {
    if(typeof str === "undefined") return;
    return(str.replace(/,/g,''));
}

function add_account(account_id, member_id) {
    $.ajax({
        url: base_url + "/deposit_atm/add_account_atm",
        method: "post",
        data: {account_id: account_id, member_id: member_id},
        dataType: "text",
        success: function (data) {
            $('#add_account_space').html(data);
            if ($('#sequester_status_2').is(':checked')) {
                $('.show_sequester_amount').show();
            }
            $('#add_account').modal('show');
            trigger_account_choose();
            change_account_type();
        }
    });

}
function trigger_account_choose(){
    setTimeout(function(){
        var dummy = $("input[name='dummy_acc_id']");
        if(typeof dummy.val() !== "undefined" || dummy.val() === "") {
            $("#acc_id").val(dummy.val());
        }
    }, 900);
}
function change_type() {
    if ($('#sequester_status_2').is(':checked')) {
        $('.show_sequester_amount').show();
    } else {
        $('#sequester_amount').val('0');
        $('.show_sequester_amount').hide();
    }
    check_remark();
}

function check_remark(){
    var sequester_status = $('input[name=sequester_status]:checked', '#frm1').val();
    var sequester_status_atm = $('input[name=sequester_status_atm]:checked', '#frm1').val();
    if((sequester_status != 0 || sequester_status_atm != 0) && !$("input[name='sequester_status_atm']").is(':disabled')){
        $('#div_remark').show();
    }else{
        $('#div_remark').hide();
    }
}

function get_data(member_id, member_name) {
    $('#member_id_add').val(member_id);
    $('#member_name_add').val(member_name);
    $('#acc_name_add').val(member_name);
    $('#acc_name_add').removeAttr('readonly');
    $('#account_name_eng').val("");
    $.post(base_url + "deposit_atm/get_member",
        {
            member_id: member_id
        }
        , function (result) {
            obj = JSON.parse(result);
            create_option_account_transfer(obj.account_list_transfer);
            create_option_get_dividend_acc_num(obj.get_dividend_acc_num);
            console.log(obj);
            if (obj.firstname_en) {
                $('#account_name_eng').val(obj.firstname_en + ' ' + obj.lastname_en);
            } else {
                $('#account_name_eng').val("");
            }
        });
    $('#account_name_eng').removeAttr('readonly');
    $('#type_id').removeAttr('readonly');
    $('#search_member_add_modal').modal('hide');
}

function delete_account(account_id) {
    swal({
            title: "ท่านต้องการลบบัญชีใช่หรือไม่?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: base_url + "/save_money/check_account_delete",
                    data: {account_id: account_id},
                    success: function (msg) {
                        if (msg == 'success') {
                            document.location.href = base_url + '/save_money/delete_account/' + account_id;
                        } else {
                            swal('ไม่สามารถลบข้อมูลบัญชีได้', 'เนื่องจากมียอดเงินคงเหลือในบัญชี', 'warning');
                        }
                    }
                });
            } else {

            }
        });
}

function close_account(account_id) {

    $.ajax({
        type: "POST",
        url: base_url + "save_money/close_account_calculate",
        data: {account_id: account_id},
        success: function (msg) {
            var obj = JSON.parse(msg);
            if (obj.text_alert != '') {
                swal('', obj.text_alert, 'warning');
            } else {
                $('#close_account_interest').val(obj.interest);
                $('#close_account_interest_return').val(obj.interest_return);
                $('#close_account_tax_return').val(obj.tax_return);
                $('#tmp_close_account_interest_return').val(obj.interest_return);
                $('#close_account_principal').val(obj.principal);
                $('#close_account_id').val(account_id);
                $('#close_account_id_view').html(account_id);
                $('#close_account_name').html(obj.close_account_name);
                $("#close_account_interest").trigger("keyup");
                $('#close_account').modal('show');
            }
        }
    });

}

$(function () {
    $("#search_member_add").keyup(function () {
        $.ajax({
            type: "POST",
            url: base_url + "/ajax/search_member_jquery",
            data: {search: $("#search_member_add").val()},
            success: function (msg) {
                $("#result_add").html(msg);
            }
        });
    });


    $("#search_text").keyup(function(e) {
        var code = e.which; // recommended to use e.which, it's normalized across browsers
        if(code==13)e.preventDefault();
        if(code==32||code==13||code==188||code==186){
            check_search();
        }
    });

    $("#is_ignore_interest_return").click(function() {
        if($(this).prop("checked")) {
            $("#close_account_interest_return").val("");
            $("#close_account_interest_return").prop("readonly", true);
        }
        else {
            $("#close_account_interest_return").val($("#tmp_close_account_interest_return").val());
            $("#close_account_interest_return").prop("readonly", false);
        }

        $(".cal_close_account_total").trigger("keyup");
    });

    $(".cal_close_account_total").keyup(function(e) {
        var close_account_principal = isNaN(parseFloat($("#close_account_principal").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_principal").val().replace(/,/g, ""));
        var close_account_interest = isNaN(parseFloat($("#close_account_interest").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_interest").val().replace(/,/g, ""));
        var close_account_interest_return = isNaN(parseFloat($("#close_account_interest_return").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_interest_return").val().replace(/,/g, ""));
        var close_account_tax_return = isNaN(parseFloat($("#close_account_tax_return").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_tax_return").val().replace(/,/g, ""));
        $("#close_account_total").html($().number_format(close_account_principal + close_account_interest - close_account_interest_return - close_account_tax_return, { numberOfDecimals: 2, decimalSeparator: '.', thousandSeparator: ',' }));
    });
});

function check_submit() {
    var text_alert = '';
    if ($('#member_id_add').val() == '') {
        text_alert += '- รหัสสมาชิก\n';
    }
    if ($('#acc_name_add').val() == '') {
        text_alert += '- ชื่อบัญชี\n';
    }
    if ($('#type_id').val() == '') {
        text_alert += '- ประเภทบัญชี\n';
    }
    if($('#bank_account').val() == ''){
        text_alert += '- เลขบัญชีธนาคาร\n';
    }
    if($('#approve_amount').val() == '' || parseFloat($('#approve_amount').val()) == 0){
        text_alert += '- ยอดเงินอนุมัติ\n';
    }

    if($('#min_first_deposit').val()==''){
        if($('#min_first_deposit').is('[readonly]')==false){
            text_alert += '- ระบุยอดเงินเปิดบัญชี\n';
        }
    }
    var sequester_status = $('input[name=sequester_status]:checked', '#frm1').val();
    var sequester_status_atm = $('input[name=sequester_status_atm]:checked', '#frm1').val();
    console.log("11 ",$("#remark").val());
    console.log($("input[name='sequester_status_atm']").is(':disabled'));
    if((sequester_status != 0 || sequester_status_atm != 0)
        && $("#remark").val()==""
        && !$("input[name='sequester_status_atm']").is(':disabled')
    ){
        text_alert += '- ระบุสาเหตุการอายัดบัญชีด้วย\n';
    }

    if($('#acc_id').val()!=undefined){
        var tmp = $('#acc_id').val();
        acc_id = tmp.replace(/-/g, '');
    }else{
        var tmp = $('#acc_id_yourself').val();
        acc_id = tmp.replace(/-/g, '');
    }
    $.ajax({
        type: "POST",
        url: base_url + "/deposit_atm/check_account_atm",
        data: {
            atm_number: $('#atm_number').val(),
            member_id: $('#member_id_add').val(),
            account_id: acc_id,
            old_account_no: $("#old_account_no").val(),
            type_id: $('#type_id').val(),
            unique_account: $('#type_id :selected').attr('unique_account'),
            min_first_deposit: removeCommas($('#min_first_deposit').val())
        },
        success: function (obj) {
            if (obj.acc_number == 'dupplicate_account_no' && ($("#acc_id").val()=="" || $("#acc_id").val()==undefined) ) {
                text_alert += '- มีเลขที่บัญชี ซ้ำในระบบ\n';
            }
            if (obj.atm_number == 'dupplicate') {
                text_alert += '- มีเลขบัตร ATM ซ้ำในระบบ\n';
            }
            if (obj.unique_account == 'dupplicate') {
                text_alert += '- ประเภทบัญชีที่ท่านเลือกมีได้เพียงบัญชีเดียว\n';
            }
            if (obj.error != '' && old_account_no=="") {
                text_alert += '- ' + obj.error + '\n';
            }

            if (text_alert != '') {
                swal('กรุณากรอกข้อมูลต่อไปนี้', text_alert, 'warning');
            } else {
                if($('#acc_id_yourself').val()!=undefined){
                    var tmp = $('#acc_id_yourself').val();
                    acc_id = tmp.replace(/-/g, '');
                    $('#acc_id_yourself').val(acc_id);
                }
                $('#frm1').submit();
            }
        }
    });
}

function change_account_type() {
    if ($('#type_id :selected').attr('type_code') == '21') {
        $('#atm_space').show();
    } else {
        $('#atm_number').val('');
        $('#atm_space').hide();
    }
}

function check_search() {
    if ($('#search_list').val() == '') {
        swal('กรุณาเลือกรูปแบบค้นหา', '', 'warning');
    } else if ($('#search_text').val() == '') {
        swal('กรุณากรอกข้อมูลที่ต้องการค้นหา', '', 'warning');
    } else {
        var tmp = $('#search_text').val().split('-');
        var search_text = tmp.join('');
        $.ajax({
            type: "POST",
            url: base_url + "/ajax/search_account_deposit",
            data: {
                search_text: search_text,
                search_list: $('#search_list').val()
            },
            success: function (msg) {
                $("#tb_wrap").html(msg);
                $("#page_wrap").css("display", $("#search_text").val() == "" ? "block" : "none");
            }
        });
    }
}

function check_member_id() {
    var member_id = $('#member_id_add').val();
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if (keycode == '13') {
        $.post(base_url + "ajax/get_member",
            {
                member_id: member_id
            }
            , function (result) {
                obj = JSON.parse(result);
                if (obj.member_id && obj.member_name) {
                    get_data(obj.member_id, obj.member_name)
                    if (obj.firstname_en) {
                        $('#account_name_eng').val(obj.firstname_en + ' ' + obj.lastname_en);
                    } else {
                        $('#account_name_eng').val("");
                    }
                } else {
                    swal('ไม่พบรหัสสมาชิกที่ท่านเลือก', '', 'warning');
                }
            });
    }
}

$('#member_search').click(function () {
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
                $('#result_add').html(data);
            },
            error: function (xhr) {
                console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
            }
        });
    }
});

function close_modal(id) {
    $('#' + id).modal('hide');
}

function click_bt_yourself() {
    $('.show_acc_id_yourself').show();
}

function create_option_account_transfer(data){
    console.log(data);
    $('#acc_id')
        .find('option')
        .remove()
        .end()
        .append('<option value="">เลือกบัญชี ATM</option>')
        .val('')
    ;
    // account_transfer
    $.each(data, function(key, value) {
        $('#acc_id')
            .append($("<option></option>")
                .attr("value",value.id)
                .text(value.text));
    });
}
//optionเลือกเลขบัญชี
function create_option_get_dividend_acc_num(data){
    console.log(data);
    $('#dividend_acc_num')
        .find('option')
        .remove()
        .end()
        .append('<option value="">เลือกเลขบัญชีธนาคาร</option>')
        .val('')
    ;
    // account_transfer
    $.each(data, function(key, value) {
        $('#dividend_acc_num')
            .append($("<option></option>")
                .attr("value",value.dividend_acc_num)
                .text(value.dividend_acc_num));
    });
}
$(document).on('keyup', '#bank_account', function(e){

    const keycode = window.event ? event.keyCode : event.which;
    let char = $(this).val().split('-').join('');
    let _char= '';

    if(char.length >= 10){
        _char = $(this).val().substr(0, 12);
        $(this).val(_char);
        e.preventDefault();
    }
    if(e.keyCode === 8 || e.keyCode === 46) {
        return true;
    }else if(!(e.shiftKey == false && (e.keyCode == 46 || e.keyCode == 8 || e.keyCode == 37 || e.keyCode == 39 || (e.keyCode >= 48 && e.keyCode <= 57)))){
        e.preventDefault();
    }else{
        if (char.length === 3 || char.length === 4) {
            _char = $(this).val() + '-';
            $(this).val(_char);
        }
    }
});

function format_the_number(ele){
    var value = $('#'+ele.id).val();
    if(value!=''){
        value = value.replace(',','');
        value = parseInt(value);
        value = value.toLocaleString();
        if(value == 'NaN'){
            $('#'+ele.id).val('');
        }else{
            $('#'+ele.id).val(value);
        }
    }else{
        $('#'+ele.id).val('');
    }
}

function check_loan_atm() {
	if ($('#loan_atm_activate_1').is(':checked')) {		
		$.ajax({
			url: base_url + "/deposit_atm/check_loan_atm",
			method: "post",
			data: {member_id : $('#member_id_add').val()},
			dataType: "text",
			success: function (data) {
				//console.log(data);
				obj = JSON.parse(data);
				if(obj.status === 0){
					swal("ไม่พบข้อมูลเงินกู้ฉุกเฉิน ATM", "กรุณาสร้างสัญญาเงินกู้ฉุกเฉิน ATM", "error");
					$('#loan_atm_activate_0').attr('checked',true);
				}
			}
		});
    }
}
