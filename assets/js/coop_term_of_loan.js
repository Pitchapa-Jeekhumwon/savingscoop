var base_url = $('#base_url').attr('class');
$(document).ready(function() {
    $("#start_date").datepicker({
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
        //startDate: '+0d',
        autoclose: true,
    });
    // change_type('type_id');
    // console.log($('#type_id').val());
    /*-----------------------------หนังสือคำขอกู้------------------------------------- */
    $.ajax({
        url: base_url + 'setting_credit_data/pdf_type',
        method: 'POST',
        dataType: 'json',
        data: {
            'type_id': $('#type_id').val()
        },
        success: function(msg) {
            for (let index = 0; index < msg.length; index++) {
                $('#type_pdf_id').append("<option value='" + msg[index]['id'] + "'>ฉบับที่ " + (index + 1) + " " + msg[index]['details'] + "</option>");
            }
        }
    });

    /*-----------------------------หนังสือสัญญา------------------------------------- */
    $.ajax({
        url: base_url + 'setting_credit_data/pdf_type',
        method: 'POST',
        dataType: 'json',
        data: {
            'type_id': '11'
        },
        success: function(msg) {
            $('#transfer_pdf_id').append("<option value='' >ไม่ต้องการ</option>");
            for (let index = 0; index < msg.length; index++) {
                $('#transfer_pdf_id').append("<option value='" + msg[index]['id'] + "'>ฉบับที่ " + (index + 1) + " " + msg[index]['details'] + "</option>");
            }
        }
    });
    /*-----------------------------หนังสือค้ำประกัน------------------------------------- */
    $.ajax({
        url: base_url + 'setting_credit_data/pdf_type',
        method: 'POST',
        dataType: 'json',
        data: {
            'type_id': '12'
        },
        success: function(msg) {
            $('#surety_pdf_id').append("<option value='' >ไม่ต้องการ</option>");
            for (let index = 0; index < msg.length; index++) {
                $('#surety_pdf_id').append("<option value='" + msg[index]['id'] + "'>ฉบับที่ " + (index + 1) + " " + msg[index]['details'] + "</option>");
            }
        }
    });
    /*----------------------------หนังสือรับรองเงินเดือน-------------------------------------- */
    $.ajax({
        url: base_url + 'setting_credit_data/pdf_type',
        method: 'POST',
        dataType: 'json',
        data: {
            'type_id': '13'
        },
        success: function(msg) {
            $('#append_pdf_id').append("<option value='' >ไม่ต้องการ</option>");
            for (let index = 0; index < msg.length; index++) {
                $('#append_pdf_id').append("<option value='" + msg[index]['id'] + "'>ฉบับที่ " + (index + 1) + " " + msg[index]['details'] + "</option>");
            }
        }
    });
    /*------------------------------------------------------------------ */

});

function check_form() {
    var text_alert = '';
    if ($.trim($('#type_name').val()) == '') {
        text_alert += ' - ประเภทการกู้เงิน\n';
    }
    if ($.trim($('#interest_rate').val()) == '') {
        text_alert += ' - อัตราดอกเบี้ย\n';
    }
    if ($.trim($('#prefix_code').val()) == '') {
        text_alert += ' - รหัสนำหน้าสัญญา\n';
    }

    if (text_alert != '') {
        swal('กรุณากรอกข้อมูลต่อไปนี้', text_alert, 'warning');
    } else {
        $('#form_save').submit();
    }

}

function del_coop_credit_data(id) {
    swal({
            title: "ท่านต้องการลบข้อมูลนี้ใช่หรือไม่",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ลบ',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: base_url + '/setting_credit_data/del_coop_credit_data',
                    method: 'POST',
                    data: {
                        'table': 'coop_term_of_loan',
                        'id': id,
                        'field': 'id'
                    },
                    success: function(msg) {
                        //console.log(msg); return false;
                        if (msg == 1) {
                            document.location.href = base_url + 'setting_credit_data/coop_term_of_loan';
                        } else {

                        }
                    }
                });
            } else {

            }
        });

}

function add_type() {
    $('#loan_type_modal').modal('show');
}

function save_type() {
    $('#form1').submit();
}

function edit_type(id, type_name, loan_type_status) {
    $('#loan_type_id').val(id);
    $('#loan_type').val(type_name);
    if (loan_type_status == '1') {
        $('#loan_type_status').attr('checked', true);
    } else {
        $('#loan_type_status').removeAttr('checked');
    }
}

function del_type(id) {
    swal({
            title: "คุณต้องการที่จะลบ",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ลบ',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {

                $.ajax({
                    url: base_url + '/setting_credit_data/check_use_type',
                    method: 'POST',
                    data: {
                        'id': id
                    },
                    success: function(msg) {
                        //console.log(msg); return false;
                        if (msg == 'success') {
                            document.location.href = base_url + 'setting_credit_data/del_loan_type?id=' + id;
                        } else {
                            swal("ไม่สามารถลบประเภทนี้ได้ \nเนื่องจากได้ตั้งค่าชื่อสินเชื่อสำหรับประเภทนี้แล้ว");
                        }
                    }
                });


            } else {

            }
        });
}

function add_loan_name() {
    $('#loan_name_modal').modal('show');
}

function save_loan_name() {
    if (document.getElementById("loan_app").checked) {
        swal({
            title: "ต้องการเปลี่ยนประเภทเงินกู้ในแอพใช้หรือไม่",
            text: "",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function() {
            $('#form2').submit();
        });
    } else {
        $('#form2').submit();
    }
    // $('#form2').submit();

}

function edit_loan_name(loan_name_id, loan_type_id, loan_name, loan_group_id, loan_name_description, loan_name_status, loan_app, check_contract_main) {
    $('#choose_loan_type_id').val(loan_type_id);
    $('#loan_name_id').val(loan_name_id);
    $('#loan_name').val(loan_name);
    $('#loan_name_description').val(loan_name_description);
    $('#choose_loan_group_id').val(loan_group_id);

    if (loan_name_status == '1') {
        $('#loan_name_status').attr('checked', true);
    } else {
        $('#loan_name_status').removeAttr('checked');
    }
    if (loan_app == '1') {
        $('#loan_app').attr('checked', true);
    } else {
        $('#loan_app').removeAttr('checked');
    }
    if (check_contract_main == '1') {
        $('#check_contract_main').attr('checked', true);
    } else {
        $('#check_contract_main').removeAttr('checked');
    }
}

function del_loan_name(id) {
    swal({
            title: "คุณต้องการที่จะลบ",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ลบ',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {

                $.ajax({
                    url: base_url + '/setting_credit_data/check_use_name',
                    method: 'POST',
                    data: {
                        'id': id
                    },
                    success: function(msg) {
                        //console.log(msg); return false;
                        if (msg == 'success') {
                            document.location.href = base_url + 'setting_credit_data/del_loan_name?id=' + id;
                        } else {
                            swal("ไม่สามารถลบประเภทนี้ได้ \nเนื่องจากได้ตั้งค่าเงื่อนไขการกู้เงินสำหรับชื่อสินเชื่อนี้แล้ว");
                        }
                    }
                });


            } else {

            }
        });
}

function change_type(data) {
    $("#type_pdf_id option[value]").remove();
    $("#transfer_pdf_id option[value]").remove();
    $("#append_pdf_id option[value]").remove();
    $("#surety_pdf_id option[value]").remove();
    $("#transfer_surety_pdf_id option[value]").remove();
    $("#type_pdf_condition_id option[value]").remove();
    // console.log($('#type_id').val());
    $.ajax({
        url: base_url + 'setting_credit_data/change_loan_type',
        method: 'POST',
        data: {
            'type_id': $('#type_id').val()
        },
        success: function(msg) {
            $('#loan_name_id').html(msg);
        }
    });
    if (data == 'type_id') {
        /*-----------------------------หนังสือคำขอกู้------------------------------------- */
        $.ajax({
            url: base_url + 'setting_credit_data/pdf_type',
            method: 'POST',
            dataType: 'json',
            data: {
                'type_id': $('#type_id').val()
            },
            success: function(msg) {
                $('#type_pdf_id').append("<option value='' selected disabled>กรุณาเลือกหนังสือคำขอกู้</option>");
                for (let index = 0; index < msg.length; index++) {
                    $('#type_pdf_id').append("<option value='" + msg[index]['id'] + "'>ฉบับที่ " + (index + 1) + " " + msg[index]['details'] + "</option>");
                }
            }
        });
    }
    /*-----------------------------หนังสือสัญญา------------------------------------- */
    $.ajax({
        url: base_url + 'setting_credit_data/pdf_type',
        method: 'POST',
        dataType: 'json',
        data: {
            'type_id': '11'
        },
        success: function(msg) {
            $('#transfer_pdf_id').append("<option value='' selected disabled>กรุณาเลือกหนังสือสัญญา</option>");
            $('#transfer_pdf_id').append("<option value='' >ไม่ต้องการ</option>");
            for (let index = 0; index < msg.length; index++) {
                $('#transfer_pdf_id').append("<option value='" + msg[index]['id'] + "'>ฉบับที่ " + (index + 1) + " " + msg[index]['details'] + "</option>");
            }
        }
    });
    /*-----------------------------หนังสือค้ำประกัน------------------------------------- */
    $.ajax({
        url: base_url + 'setting_credit_data/pdf_type',
        method: 'POST',
        dataType: 'json',
        data: {
            'type_id': '12'
        },
        success: function(msg) {
            $('#surety_pdf_id').append("<option value='' selected disabled>กรุณาเลือกหนังสือค้ำประกัน</option>");
            $('#surety_pdf_id').append("<option value='' >ไม่ต้องการ</option>");
            for (let index = 0; index < msg.length; index++) {
                $('#surety_pdf_id').append("<option value='" + msg[index]['id'] + "'>ฉบับที่ " + (index + 1) + " " + msg[index]['details'] + "</option>");
            }
        }
    });
    /*----------------------------หนังสือรับรองเงินเดือน-------------------------------------- */
    $.ajax({
        url: base_url + 'setting_credit_data/pdf_type',
        method: 'POST',
        dataType: 'json',
        data: {
            'type_id': '13'
        },
        success: function(msg) {
            $('#append_pdf_id').append("<option value='' selected disabled>กรุณาเลือกหนังสือรับรองเงินเดือน</option>");
            $('#append_pdf_id').append("<option value='' >ไม่ต้องการ</option>");
            for (let index = 0; index < msg.length; index++) {
                $('#append_pdf_id').append("<option value='" + msg[index]['id'] + "'>ฉบับที่ " + (index + 1) + " " + msg[index]['details'] + "</option>");
            }
        }
    });
    /*------------------------------------------------------------------ */
    $('#type_name').val($('#type_id :selected').text());
}

function change_loan_name() {
    $('#loan_name').val($('#loan_name_id :selected').text());
}
$(document).ready(function() {
    $("#various1").fancybox({
        'titlePosition': 'inside',
        'transitionIn': 'none',
        'transitionOut': 'none',
    });
    //class for check input number
    $('.check_number').on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    });
});

function change_type_id() {
    const type_id = $('#type_id').val();
    const param = new URLSearchParams(location.search);
    param.set('type_id', type_id);
    param.set('loan_name_id', '');
    document.location.href = `?${param.toString()}`;
}

function type_status() {
    const type_status = $('#type_status').val();
    const param = new URLSearchParams(location.search);
    param.set('type_status', type_status);
    document.location.href = `?${param.toString()}`;
}

function change_loan_name() {

    const loan_name_id = $('#loan_name_id').val();
    const params = new URLSearchParams(location.search);
    params.set('loan_name_id', loan_name_id);
    params.set('type_status', '');
    // document.location.href = `?${params.toString()}`;
}

function change_close_loan() {
    $.ajax({
        url: base_url + 'setting_credit_data/change_loan_type',
        method: 'POST',
        data: {
            'type_id': $('#close_loan_type_id').val()
        },
        success: function(msg) {
            $('#close_loan_name_id').html(msg);
        }
    });
    $('#close_type_name').val($('#close_loan_type_id :selected').text());
}

function change_close_loan_name() {
    $('#close_loan_name').val($('#close_loan_name_id :selected').text());
}

function add_loan_group() {
    $('#loan_group_modal').modal('show');
}

function edit_group_name(loan_group_id, loan_group_name, description, status) {
    console.log("descript: " + description);
    $("#loan_group_id").val(loan_group_id);
    $("#loan_group_name").val(loan_group_name);
    $("#description").val(description);
    $("#status").val(status);
}

function save_gruop_name() {
    const groupName = $("#loan_group_name");
    let err = "";
    if (typeof groupName.val() === "undefined" || groupName.val() === "") {
        err = "กรุณาระบุกลุ่มเงินกู้";
    }

    if (err) {
        swal(err, "", "warning");
    } else {
        $("#form_group_name").submit();
    }
}

function del_group_name(id) {
    swal({
        title: "คุณต้องการที่จะลบ",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'ลบ',
        cancelButtonText: "ยกเลิก",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        if (isConfirm) {
            $.post(base_url + "setting_credit_data/coop_loan_group_delete", { id: id }, function(data) {
                if (data.status === 400) {
                    swal("ลบข้อมูลไม่สำเร็จ", data.msg, "warning");
                    return false;
                } else {
                    swal("ลบข้อมูลสำเร็จ", "", "warning");
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                }
            });
        }
    });
}

function contract_pattern_help() {
    $('#contract_pattern_help').modal('show');
}