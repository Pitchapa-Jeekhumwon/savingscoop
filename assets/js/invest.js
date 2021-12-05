$(document).ready(function() {
    invest_id = $("#default_invest_id").val();
    current_date_format = $("#current_date_format").val();
    $(".mydate").datepicker({
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
        autoclose: true,
    });

    if(invest_id) {
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
        display_detail(invest_id);
    }

    $("#add_invest").click(function() {
        $("#invest_id").val('');
        $("#invest_type_add_sub").val('');
        $('#invest_type_add').prop('disabled', false);
        $('.coop_sav_c_row').hide();
        $('.coop_share_c_row').hide();
        $('.bond_c_row').hide();
        $('.coop_share_s_row').hide();
        $('.status_edit_row').hide();
        $("#status_enable").prop("checked", true);
        $("#status_disable").prop("checked", false);
        $("#invest_modal").modal('show');
    });
    $("#invest_type_add").change(function() {
        type = $(this).val();
        if(type == 1) {
            $('.coop_sav_c_row').show();
            $('.coop_share_c_row').hide();
            $('.bond_c_row').hide();
            $('.coop_share_s_row').hide();
        } else if (type == 2) {
            $('.coop_sav_c_row').hide();
            $('.coop_share_c_row').show();
            $('.bond_c_row').hide();
            $('.coop_share_s_row').hide();
        } else if (type == 3 || type == 4) {
            $('.coop_sav_c_row').hide();
            $('.coop_share_c_row').hide();
            $('.bond_c_row').show();
            $('.coop_share_s_row').hide();
        } else if (type == 5) {
            $('.coop_sav_c_row').hide();
            $('.coop_share_c_row').hide();
            $('.bond_c_row').hide();
            $('.coop_share_s_row').show();
        }
    });
    $("#cancel_add").click(function() {
        $("#invest_modal").modal('hide');
        $("#invest_type_add_sub").val('');
        $("#invest_type_add").val('');
        $('#invest_type_add').prop('disabled', false);
        $("#sav_c_name").val('');
        $("#sav_c_amount").val(0);
        $("#sav_c_interest").val('');
        $("#sav_c_start_date").val(current_date_format);
        $("#sav_c_due_date").val(current_date_format);
        $("#sav_c_period").val('');
        $("#sav_c_source").val('');
        $("#share_c_name").val('');
        $("#share_c_period").val('');
        $("#share_c_source").val('');
        $("#bond_c_name").val('');
        $("#bond_c_department_name").val('');
        $("#bond_c_credit_rating").val('');
        $("#bond_c_unit").val(0);
        $("#bond_c_value_per_unit").val(0);
        $("#bond_c_aver_profit").val(0);
        $("#bond_c_invest_rate_text").val('');
        $("#bond_c_start_date").val(current_date_format);
        $("#bond_c_due_date").val(current_date_format);
        $("#bond_c_source").val('');
        $("#share_s_name").val('');
        $("#share_s_period").val('');
        $("#share_s_source").val('');
        $("#bond_c_payment_method_text").val('');
    });
    $("#interest_add_btn").click(function() {
        $("#interest_modal").modal('show');
    });
    $("#interest_cancel_add").click(function() {
        $("#interest_modal").modal("hide");
        $("#interest_date").val(current_date_format);
        $("#interest_rate").val(0);
        $("#interest_amount").val(0);
        $("#interest_note").val("");
        $("#add_interest_invest_id").val("");
        $("#add_interest_interest_id").val("");
    });
    $("#interest_submit_add").click(function() {
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
        invest_id = $("#add_interest_invest_id").val();
        $("#interest_rate").val(removeCommas($("#interest_rate").val()));
        $("#interest_amount").val(removeCommas($("#interest_amount").val()));
        $.post(base_url+"invest/add_interest",
        $("#add_interest_form").serialize(),
        function(result) {
            data = JSON.parse(result);
            $("#interest_modal").modal("hide");
            $("#interest_date").val(current_date_format);
            $("#interest_rate").val(0);
            $("#interest_amount").val(0);
            $("#interest_note").val("");
            $("#add_interest_invest_id").val("");
            $("#add_interest_interest_id").val("");
            display_detail(invest_id);
        });
    });
    $(document).on("click",".profit-edit-bth",function() {
        id = $(this).attr('data-id');
        $.get(base_url+"invest/get_profit_transaction?id="+id,
        function(result) {
            data = JSON.parse(result);
            transaction = data.data;
            $("#interest_date").val(transaction.date_calender);
            $("#interest_rate").val(transaction.rate_format);
            $("#interest_amount").val(transaction.amount_format);
            $("#interest_note").val(transaction.note);
            $("#add_interest_invest_id").val(transaction.invest_id);
            $("#add_interest_interest_id").val(transaction.id);
            $("#interest_modal").modal("show");
        });
    });
    $("#share_add_tran_btn").click(function() {
        $("#share_c_m_invest_id").val($(this).attr('data_id'));
        $("#share_c_m_tran_id").val("");
        $("#share_c_m_date").val(current_date_format);
        $("#share_c_m_amount").val(0);
        $("#share_c_m_unit").val(0);
        $("#share_c_m_note").val("");
        $("#share_c_modal").modal('show');
    });
    $("#share_c_m_cancel").click(function() {
        $("#share_c_m_invest_id").val($(this).attr('data_id'));
        $("#share_c_m_tran_id").val("");
        $("#share_c_m_date").val(current_date_format);
        $("#share_c_m_amount").val(0);
        $("#share_c_m_unit").val(0);
        $("#share_c_m_note").val("");
        $("#share_c_modal").modal('hide');
    });
    $("#share_c_m_submit").click(function() {
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
        invest_id = $("#share_c_m_invest_id").val();
        $(".num_input").each(function(index) {
            $(this).val(removeCommas($(this).val()));
        });
        $.post(base_url+"invest/add_transaction",
        $("#share_c_form").serialize(),
        function(result) {
            data = JSON.parse(result);
            $("#share_c_m_invest_id").val("");
            $("#share_c_m_tran_id").val("");
            $("#share_c_m_date").val(current_date_format);
            $("#share_c_m_amount").val(0);
            $("#share_c_m_unit").val(0);
            $("#share_c_m_note").val("");
            $("#share_c_modal").modal('hide');
            display_detail(invest_id);
        });
    });
    $(document).on("click",".s-t-c-edit-bth",function() {
        id = $(this).attr('data-id');
        $.get(base_url+"invest/get_transaction?id="+id,
        function(result) {
            data = JSON.parse(result);
            tran = data.data;
            $("#share_c_m_invest_id").val(tran.invest_id);
            $("#share_c_m_tran_id").val(tran.id);
            $("#share_c_m_date").val(tran.date_calender);
            $("#share_c_m_amount").val(tran.amount_format);
            $("#share_c_m_unit").val(tran.unit);
            $("#share_c_m_note").val(tran.note);
            $("#share_c_modal").modal("show");
        });
    });
    $("#dividend_add_btn").click(function() {
        $("#add_dividend_invest_id").val($(this).attr("data_id"));
        $("#dividend_modal").modal('show');
    });
    $("#dividend_cancel_add").click(function() {
        $("#dividend_modal").modal("hide");
        $("#dividend_date").val(current_date_format);
        $("#dividend_rate").val(0);
        $("#dividend_amount").val(0);
        $("#dividend_note").val("");
        $("#add_dividend_invest_id").val("");
        $("#add_dividend_interest_id").val("");
    });
    $("#dividend_submit_add").click(function() {
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
        $(".num_input").each(function(index) {
            $(this).val(removeCommas($(this).val()));
        });
        invest_id = $("#add_dividend_invest_id").val();
        $("#dividend_rate").val(removeCommas($("#dividend_rate").val()));
        $("#dividend_amount").val(removeCommas($("#dividend_amount").val()));
        $.post(base_url+"invest/add_interest",
        $("#add_dividend_form").serialize(),
        function(result) {
            data = JSON.parse(result);
            $("#dividend_modal").modal("hide");
            $("#dividend_date").val(current_date_format);
            $("#dividend_rate").val(0);
            $("#dividend_amount").val(0);
            $("#dividend_note").val("");
            $("#add_dividend_invest_id").val("");
            $("#add_dividend_interest_id").val("");
            display_detail(invest_id);
        });
    });
    $(document).on("click",".dividend-edit-bth",function() {
        id = $(this).attr('data-id');
        $.get(base_url+"invest/get_profit_transaction?id="+id,
        function(result) {
            data = JSON.parse(result);
            transaction = data.data;
            $("#dividend_date").val(transaction.date_calender);
            $("#dividend_rate").val(transaction.rate_format);
            $("#dividend_amount").val(transaction.amount_format);
            $("#dividend_note").val(transaction.note);
            $("#add_dividend_invest_id").val(transaction.invest_id);
            $("#add_dividend_interest_id").val(transaction.id);
            $("#dividend_modal").modal("show");
        });
    });
    $("#sav_c_edit_btn").click(function() {
        $('.coop_sav_c_row').show();
        $('.coop_share_c_row').hide();
        id = $(this).attr('data_id');
        $.get(base_url+"invest/get_invest_by_id?id="+id,
        function(result) {
            data = JSON.parse(result);
            invest = data.data;
            $("#invest_id").val(invest.id);
            $("#invest_type_add").val(invest.type);
            $('#invest_type_add').prop('disabled', true);
            $("#invest_type_add_sub").val(invest.type);
            $("#org_add").val(invest.org_id);
            $("#sav_c_name").val(invest.name);
            $("#sav_c_amount").val(invest.amount_format);
            $("#sav_c_interest").val(invest.detail.invest_rate_text);
            $("#sav_c_start_date").val(invest.detail.start_date_calender);
            $("#sav_c_due_date").val(invest.detail.end_date_calender);
            $("#sav_c_period").val(invest.detail.payment_method_text);
            $("#sav_c_source").val(invest.source);
            $("#share_c_name").val('');
            $("#share_c_period").val('');
            $("#share_c_source").val('');
            $("#bond_c_name").val('');
            $("#bond_c_department_name").val('');
            $("#bond_c_credit_rating").val('');
            $("#bond_c_unit").val(0);
            $("#bond_c_value_per_unit").val(0);
            $("#bond_c_aver_profit").val(0);
            $("#bond_c_invest_rate_text").val('');
            $("#bond_c_start_date").val(current_date_format);
            $("#bond_c_due_date").val(current_date_format);
            $("#bond_c_payment_method_text").val('');
            $("#bond_c_source").val('');
            $("#share_s_name").val('');
            $("#share_s_period").val('');
            $("#share_s_source").val('');
            $('.coop_sav_c_row').show();
            $('.coop_share_c_row').hide();
            $('.bond_c_row').hide();
            $('.coop_share_s_row').hide();
            if(invest.detail.invest_interval_left) {
                $("#status_enable_text").html(" ปกติ");
            } else {
                $("#status_enable_text").html(" ครบกำหนด");
            }
            if(invest.status == 2) {
                $("#status_enable").prop("checked", false);
                $("#status_disable").prop("checked", true);
            } else {
                $("#status_enable").prop("checked", true);
                $("#status_disable").prop("checked", false);
            }
            $(".status_edit_row").show();
            $("#invest_modal").modal("show");
        });
    });
    $("#bond_edit_btn").click(function() {
        id = $(this).attr('data_id');
        $.get(base_url+"invest/get_invest_by_id?id="+id,
        function(result) {
            data = JSON.parse(result);
            invest = data.data;
            $("#invest_id").val(invest.id);
            $("#invest_type_add").val(invest.type);
            $('#invest_type_add').prop('disabled', true);
            $("#invest_type_add_sub").val(invest.type);
            $("#org_add").val(invest.org_id);
            $("#sav_c_name").val('');
            $("#sav_c_amount").val(0);
            $("#sav_c_interest").val('');
            $("#sav_c_start_date").val(current_date_format);
            $("#sav_c_due_date").val(current_date_format);
            $("#sav_c_period").val('');
            $("#sav_c_source").val('');
            $("#share_c_name").val('');
            $("#share_c_period").val('');
            $("#share_c_source").val('');
            $("#bond_c_name").val(invest.detail.name);
            $("#bond_c_department_name").val(invest.name);
            $("#bond_c_credit_rating").val(invest.detail.credit_rating);
            $("#bond_c_unit").val(invest.detail.unit_format);
            $("#bond_c_value_per_unit").val(invest.detail.value_per_unit_format);
            $("#bond_c_aver_profit").val(invest.detail.aver_profit_format);
            $("#bond_c_invest_rate_text").val(invest.detail.invest_rate_text);
            $("#bond_c_start_date").val(invest.detail.start_date_calender);
            $("#bond_c_due_date").val(invest.detail.end_date_calender);
            $("#bond_c_payment_method_text").val(invest.detail.payment_method_text);
            $("#bond_c_source").val(invest.source);
            $("#share_s_name").val('');
            $("#share_s_period").val('');
            $("#share_s_source").val('');
            $('.coop_sav_c_row').hide();
            $('.coop_share_c_row').hide();
            $('.bond_c_row').show();
            $('.coop_share_s_row').hide();
            if(invest.detail.invest_interval_left) {
                $("#status_enable_text").html(" ปกติ");
            } else {
                $("#status_enable_text").html(" ครบกำหนด");
            }
            if(invest.status == 2) {
                $("#status_enable").prop("checked", false);
                $("#status_disable").prop("checked", true);
            } else {
                $("#status_enable").prop("checked", true);
                $("#status_disable").prop("checked", false);
            }
            $(".status_edit_row").show();
            $("#invest_modal").modal("show");
        });
    });
    $("#share_c_edit_btn").click(function() {
        id = $(this).attr('data_id');
        $.get(base_url+"invest/get_invest_by_id?id="+id,
        function(result) {
            data = JSON.parse(result);
            invest = data.data;
            $("#invest_id").val(invest.id);
            $("#invest_type_add").val(invest.type);
            $('#invest_type_add').prop('disabled', true);
            $("#invest_type_add_sub").val(invest.type);
            $("#org_add").val(invest.org_id);
            $("#sav_c_name").val('');
            $("#sav_c_amount").val(0);
            $("#sav_c_interest").val('');
            $("#sav_c_start_date").val(current_date_format);
            $("#sav_c_due_date").val(current_date_format);
            $("#sav_c_period").val('');
            $("#sav_c_source").val('');
            $("#share_c_name").val(invest.name);
            $("#share_c_period").val(invest.detail.payment_method_text);
            $("#share_c_source").val(invest.source);
            $("#bond_c_name").val('');
            $("#bond_c_department_name").val('');
            $("#bond_c_credit_rating").val('');
            $("#bond_c_unit").val(0);
            $("#bond_c_value_per_unit").val(0);
            $("#bond_c_aver_profit").val(0);
            $("#bond_c_invest_rate_text").val('');
            $("#bond_c_start_date").val(current_date_format);
            $("#bond_c_due_date").val(current_date_format);
            $("#bond_c_payment_method_text").val('');
            $("#bond_c_source").val('');
            $("#share_s_name").val('');
            $("#share_s_period").val('');
            $("#share_s_source").val('');
            $('.coop_sav_c_row').hide();
            $('.coop_share_c_row').show();
            $('.bond_c_row').hide();
            $('.coop_share_s_row').hide();
            if(invest.status == 2) {
                $("#status_enable").prop("checked", false);
                $("#status_disable").prop("checked", true);
            } else {
                $("#status_enable").prop("checked", true);
                $("#status_disable").prop("checked", false);
            }
            $(".status_edit_row").show();
            $("#invest_modal").modal("show");
        });
    });
    $("#share_s_edit_btn").click(function() {
        id = $(this).attr('data_id');
        $.get(base_url+"invest/get_invest_by_id?id="+id,
        function(result) {
            data = JSON.parse(result);
            invest = data.data;
            $("#invest_id").val(invest.id);
            $("#invest_type_add").val(invest.type);
            $('#invest_type_add').prop('disabled', true);
            $("#invest_type_add_sub").val(invest.type);
            $("#org_add").val(invest.org_id);
            $("#sav_c_name").val('');
            $("#sav_c_amount").val(0);
            $("#sav_c_interest").val('');
            $("#sav_c_start_date").val(current_date_format);
            $("#sav_c_due_date").val(current_date_format);
            $("#sav_c_period").val('');
            $("#sav_c_source").val('');
            $("#share_c_name").val(invest.name);
            $("#share_c_period").val(invest.detail.payment_method_text);
            $("#share_c_source").val('');
            $("#bond_c_name").val('');
            $("#bond_c_department_name").val('');
            $("#bond_c_credit_rating").val('');
            $("#bond_c_unit").val(0);
            $("#bond_c_value_per_unit").val(0);
            $("#bond_c_aver_profit").val(0);
            $("#bond_c_invest_rate_text").val('');
            $("#bond_c_start_date").val(current_date_format);
            $("#bond_c_due_date").val(current_date_format);
            $("#bond_c_payment_method_text").val('');
            $("#bond_c_source").val('');
            $("#share_s_name").val(invest.name);
            $("#share_s_period").val(invest.detail.name);
            $("#share_s_source").val(invest.source);
            $('.coop_sav_c_row').hide();
            $('.coop_share_c_row').hide();
            $('.bond_c_row').hide();
            $('.coop_share_s_row').show();
            $("#status_enable_text").html(" ปกติ");
            if(invest.status == 2) {
                $("#status_enable").prop("checked", false);
                $("#status_disable").prop("checked", true);
            } else {
                $("#status_enable").prop("checked", true);
                $("#status_disable").prop("checked", false);
            }
            $(".status_edit_row").show();
            $("#invest_modal").modal("show");
        });
    });

    $("#share_s_rate_edit_btn").click(function() {
        id = $(this).attr('data_id');
        $.get(base_url+"invest/get_invest_share_value?id="+id,
        function(result) {
            data = JSON.parse(result);
            date = current_date_format;
            share_val = 0;
            if(data.share_val && data.share_val.last) {
                share_val = data.share_val.last.value_format;
            }
            $("#share_val_invest_id").val(id);
            $("#share_val_date").val(date);
            $("#share_val_amount").val(share_val);
            $("#share_val_branch_name").html($("#share_s_t_name").text());
            $("#share_val_modal").modal('show');
        });
    });
    $("#share_val_cancel").click(function() {
        $("#share_val_date").val(current_date_format);
        $("#share_val_amount").val(0);
        $("#share_val_modal").modal('hide');
    });
    $("#share_val_submit").click(function() {
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
        $(".num_input").each(function(index) {
            $(this).val(removeCommas($(this).val()));
        });
        $.post(base_url+"invest/add_invest_share_value",
        $("#share_val_form").serialize(),
        function(result) {
            data = JSON.parse(result);
            invest_id = $("#share_val_invest_id").val();
            $("#share_val_modal").modal('hide');
            display_detail(invest_id);
        });
    });
    $("#share_s_add_tran_btn").click(function() {
        id = $(this).attr('data_id');
        $("#share_s_m_invest_id").val(id);
        $("#share_s_m_tran_id").val("");
        $("#share_s_m_date").val(current_date_format);
        $("#share_s_m_unit").val(0);
        $("#share_s_m_amount").val(0);
        $("#share_s_m_fee").val(0);
        $("#share_s_m_tax").val(0);
        $("#share_s_m_rate").html('0.00');
        $("#share_s_m_note").val("");
        $("#share_s_modal").modal('show');
    });
    $(document).on("click",".s-t-s-edit-bth",function() {
        id = $(this).attr('data-id');
        $.get(base_url+"invest/get_transaction?id="+id,
        function(result) {
            data = JSON.parse(result);
            tran = data.data;
            if(tran.tran_type == 1) {
                $("#tran_type_1").prop("checked", true);
            } else {
                $("#tran_type_2").prop("checked", true);
            }
            $("#share_s_m_invest_id").val(tran.invest_id);
            $("#share_s_m_tran_id").val(tran.id);
            $("#share_s_m_date").val(tran.date_calender);
            $("#share_s_m_unit").val(tran.unit_format);
            $("#share_s_m_fee").val(tran.fee_format);
            $("#share_s_m_tax").val(tran.tax_format);
            $("#share_s_m_amount").val(tran.amount_format);
            $("#share_s_m_rate").html(tran.value_per_unit_format);
            $("#share_s_m_note").val(tran.note);
            $("#share_s_modal").modal('show');
        });
    });
    $("#share_s_m_cancel").click(function() {
        $("#share_s_m_tran_id").val("");
        $("#share_s_m_date").val(current_date_format);
        $("#share_s_m_unit").val(0);
        $("#share_s_m_amount").val(0);
        $("#share_s_m_fee").val(0);
        $("#share_s_m_tax").val(0);
        $("#share_s_m_rate").html('0.00');
        $("#share_s_m_note").val("");
        $("#share_s_modal").modal('hide');
    });
    $("#share_s_m_submit").click(function() {
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
        $(".num_input").each(function(index) {
            $(this).val(removeCommas($(this).val()));
        });
        invest_id = $("#share_s_m_invest_id").val();
        $.post(base_url+"invest/add_transaction",
        $("#share_s_form").serialize(),
        function(result) {
            data = JSON.parse(result);
            $("#share_s_modal").modal('hide');
            display_detail(invest_id);
        });
    });
    $(".share_s_cal").keypress(function() {
        if(!$("#share_s_m_unit").val() || !$("#share_s_m_amount").val() || !$("#share_s_m_fee").val()) {
            $("#share_s_m_rate").html('0.00');
        } else {
            unit = parseFloat(removeCommas($("#share_s_m_unit").val()));
            val = parseFloat(removeCommas($("#share_s_m_amount").val()));
            val_per_unit = val / unit;
            val_per_unit = Math.round(val_per_unit*100)/100;
            val_per_unit = val_per_unit.toLocaleString('en');
            $("#share_s_m_rate").html(val_per_unit);
        }
    });
    $(".share_s_cal").change(function() {
        if(!$("#share_s_m_unit").val() || !$("#share_s_m_amount").val() || !$("#share_s_m_fee").val()) {
            $("#share_s_m_rate").html('0.00');
        } else {
            unit = parseFloat(removeCommas($("#share_s_m_unit").val()));
            val = parseFloat(removeCommas($("#share_s_m_amount").val()));
            val_per_unit = val / unit;
            val_per_unit = Math.round(val_per_unit*100)/100;
            val_per_unit = val_per_unit.toLocaleString('en');
            $("#share_s_m_rate").html(val_per_unit);
        }
    });

    $(document).on("click",".dividend-del-bth",function() {
        id = $(this).attr('data-id');
        swal({
            title: "คุณต้องการที่จะลบใช่หรือไม่",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ลบ',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
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
                $.post(base_url+"invest/remove_profit",
                {id:id},
                function(result) {
                    data = JSON.parse(result);
                    display_detail($("#dividend_add_btn").attr('data_id'));
                });
            }
        });
    });
    $(document).on("click",".profit-del-bth",function() {
        id = $(this).attr('data-id');
        swal({
            title: "คุณต้องการที่จะลบใช่หรือไม่",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ลบ',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
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
                $.post(base_url+"invest/remove_profit",
                {id:id},
                function(result) {
                    data = JSON.parse(result);
                    display_detail($("#interest_add_btn").attr('data_id'));
                });
            }
        });
    });
    $(document).on("click", ".s-t-c-del-bth", function() {
        id = $(this).attr('data-id');
        swal({
            title: "คุณต้องการที่จะลบใช่หรือไม่",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ลบ',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
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
                $.post(base_url+"invest/remove_transactrion",
                {id:id},
                function(result) {
                    data = JSON.parse(result);
                    display_detail($("#share_add_tran_btn").attr('data_id'));
                });
            }
        });
    });
    $(document).on("click", ".s-t-s-del-bth", function() {
        id = $(this).attr('data-id');
        swal({
            title: "คุณต้องการที่จะลบใช่หรือไม่",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ลบ',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
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
                $.post(base_url+"invest/remove_transactrion",
                {id:id},
                function(result) {
                    data = JSON.parse(result);
                    display_detail($("#share_s_add_tran_btn").attr('data_id'));
                });
            }
        });
    });

    $(".del_btn").click(function() {
        swal({
            title: "คุณต้องการที่จะลบใช่หรือไม่",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ลบ',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
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
                id = $(this).attr("data-id");
                $.post(base_url+"invest/delete",
                {id:id},
                function(result) {
                    window.location.href =  base_url+'invest';
                });
            }
        });
    });

    $(".account_edit_btn").click(function() {
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
        $.get(base_url+"invest/get_account_info_by_id?id="+invest_id,
        function(result) {
            data = JSON.parse(result);

            $(".s_c_m_r").hide();
            $(".sh_c_m_r").hide();
            $(".b_m_r").hide();
            $(".s_s_m_r").hide();
            $("#account_match_type").val(data.type);
            if(data.type == 4) {
                $("#account_match_type").val(3);
            }

            if(!data.gen_account_data || data.gen_account_data == 2) {
                $("#account_match_disable").prop("checked", true);
            } else {
                $("#account_match_enable").prop("checked", true);
            }

            if(data.type == 1) {
                // s_c_m_r
                if(data.gen_account_data && data.gen_account_data == 1) {
                    if(data.matchs){
                        match_datas = data.matchs;
                        $("#1_invest_increase_desc").val(match_datas.invest_increase.description);
                        $("#1_invest_increase_chart_credit").val(match_datas.invest_increase.credit);
                        $("#1_invest_increase_chart_credit").trigger('change');
                        $("#1_invest_increase_chart_debit").val(match_datas.invest_increase.debit);
                        $("#1_invest_increase_chart_debit").trigger('change');
                        $("#1_invest_decrease_desc").val(match_datas.invest_decrease.description);
                        $("#1_invest_decrease_chart_credit").val(match_datas.invest_decrease.credit);
                        $("#1_invest_decrease_chart_credit").trigger('change');
                        $("#1_invest_decrease_chart_debit").val(match_datas.invest_decrease.debit);
                        $("#1_invest_decrease_chart_debit").trigger('change');
                        $("#1_interest_desc").val(match_datas.interest.description);
                        $("#1_interest_chart_credit").val(match_datas.interest.credit);
                        $("#1_interest_chart_credit").trigger('change');
                        $("#1_interest_chart_debit").val(match_datas.interest.debit);
                        $("#1_interest_chart_debit").trigger('change');
                        $("#1_interest_decrease_desc").val(match_datas.interest_decrease.description);
                        $("#1_interest_decrease_credit").val(match_datas.interest_decrease.credit);
                        $("#1_interest_decrease_credit").trigger('change');
                        $("#1_interest_decrease_debit").val(match_datas.interest_decrease.debit);
                        $("#1_interest_decrease_debit").trigger('change');
                    }
                    $(".s_c_m_r").show();
                }
            } else if (data.type == 2) {
                // sh_c_m_r
                if(data.gen_account_data && data.gen_account_data == 1) {
                    if(data.matchs) {
                        match_datas = data.matchs;
                        $("#2_invest_increase_desc").val(match_datas.invest_increase.description);
                        $("#2_invest_increase_chart_credit").val(match_datas.invest_increase.credit);
                        $("#2_invest_increase_chart_credit").trigger('change');
                        $("#2_invest_increase_chart_debit").val(match_datas.invest_increase.debit);
                        $("#2_invest_increase_chart_debit").trigger('change');
                        $("#2_invest_decrease_desc").val(match_datas.invest_decrease.description);
                        $("#2_invest_decrease_chart_credit").val(match_datas.invest_decrease.credit);
                        $("#2_invest_decrease_chart_credit").trigger('change');
                        $("#2_invest_decrease_chart_debit").val(match_datas.invest_decrease.debit);
                        $("#2_invest_decrease_chart_debit").trigger('change');
                        $("#2_dividend_desc").val(match_datas.dividend.description);
                        $("#2_dividend_chart_credit").val(match_datas.dividend.credit);
                        $("#2_dividend_chart_credit").trigger('change');
                        $("#2_dividend_chart_debit").val(match_datas.dividend.debit);
                        $("#2_dividend_chart_debit").trigger('change');
                        $("#2_dividend_decrease_desc").val(match_datas.dividend_decrease.description);
                        $("#2_dividend_decrease_credit").val(match_datas.dividend_decrease.credit);
                        $("#2_dividend_decrease_credit").trigger('change');
                        $("#2_dividend_decrease_debit").val(match_datas.dividend_decrease.debit);
                        $("#2_dividend_decrease_debit").trigger('change');
                    }
                    $(".sh_c_m_r").show();
                }
            } else if(data.type == 3 || data.type == 4) {
                // b_m_r
                if(data.gen_account_data && data.gen_account_data == 1) {
                    if(data.matchs) {
                        match_datas = data.matchs;
                        $("#3_invest_increase_desc").val(match_datas.invest_increase.description);
                        $("#3_invest_increase_chart_credit").val(match_datas.invest_increase.credit);
                        $("#3_invest_increase_chart_credit").trigger('change');
                        $("#3_invest_increase_chart_debit").val(match_datas.invest_increase.debit);
                        $("#3_invest_increase_chart_debit").trigger('change');
                        $("#3_invest_decrease_desc").val(match_datas.invest_decrease.description);
                        $("#3_invest_decrease_chart_credit").val(match_datas.invest_decrease.credit);
                        $("#3_invest_decrease_chart_credit").trigger('change');
                        $("#3_invest_decrease_chart_debit").val(match_datas.invest_decrease.debit);
                        $("#3_invest_decrease_chart_debit").trigger('change');
                        $("#3_interest_desc").val(match_datas.interest.description);
                        $("#3_interest_chart_credit").val(match_datas.interest.credit);
                        $("#3_interest_chart_credit").trigger('change');
                        $("#3_interest_chart_debit").val(match_datas.interest.debit);
                        $("#3_interest_chart_debit").trigger('change');
                        $("#3_interest_decrease_desc").val(match_datas.interest_decrease.description);
                        $("#3_interest_decrease_credit").val(match_datas.interest_decrease.credit);
                        $("#3_interest_decrease_credit").trigger('change');
                        $("#3_interest_decrease_debit").val(match_datas.interest_decrease.debit);
                        $("#3_interest_decrease_debit").trigger('change');
                    }
                    $(".b_m_r").show();
                }
            } else if (data.type == 5) {
                // s_s_m_r
                console.log("here")
                if(data.gen_account_data && data.gen_account_data == 1) {
                    if(data.matchs) {
                        match_datas = data.matchs;
                        $("#5_invest_increase_desc").val(match_datas.invest_increase.description);
                        $("#5_invest_increase_chart_cash_credit").val(match_datas.invest_increase.cash_credit);
                        $("#5_invest_increase_chart_cash_credit").trigger('change');
                        $("#5_invest_increase_chart_tax_credit").val(match_datas.invest_increase.tax_credit);
                        $("#5_invest_increase_chart_tax_credit").trigger('change');
                        $("#5_invest_increase_share_debit").val(match_datas.invest_increase.share_debit);
                        $("#5_invest_increase_share_debit").trigger('change');
                        $("#5_invest_increase_fee_debit").val(match_datas.invest_increase.fee_debit);
                        $("#5_invest_increase_fee_debit").trigger('change');
                        $("#5_invest_decrease_desc").val(match_datas.invest_decrease.description);
                        $("#5_invest_decrease_share_credit").val(match_datas.invest_decrease.share_credit);
                        $("#5_invest_decrease_share_credit").trigger('change');
                        $("#5_invest_decrease_tax_credit").val(match_datas.invest_decrease.tax_credit);
                        $("#5_invest_decrease_tax_credit").trigger('change');
                        $("#5_invest_decrease_cash_debit").val(match_datas.invest_decrease.cash_debit);
                        $("#5_invest_decrease_cash_debit").trigger('change');
                        $("#5_invest_decrease_profit_debit").val(match_datas.invest_decrease.profit_debit);
                        $("#5_invest_decrease_profit_debit").trigger('change');
                        $("#5_invest_decrease_fee_debit").val(match_datas.invest_decrease.fee_debit);
                        $("#5_invest_decrease_fee_debit").trigger('change');
                        $("#5_dividend_desc").val(match_datas.dividend.description);
                        $("#5_dividend_chart_credit").val(match_datas.dividend.credit);
                        $("#5_dividend_chart_credit").trigger('change');
                        $("#5_dividend_chart_debit").val(match_datas.dividend.debit);
                        $("#5_dividend_chart_debit").trigger('change');
                        $("#5_dividend_decrease_desc").val(match_datas.dividend_decrease.description);
                        $("#5_dividend_decrease_credit").val(match_datas.dividend_decrease.credit);
                        $("#5_dividend_decrease_credit").trigger('change');
                        $("#5_dividend_decrease_debit").val(match_datas.dividend_decrease.debit);
                        $("#5_dividend_decrease_debit").trigger('change');
                    }
                    $(".s_s_m_r").show();
                }
            }
            $.unblockUI();
        });
        createSelect2("account_match_modal");
        $("#account_match_modal").modal("show");
    });
    $("#account_match_cancel").click(function() {
        $("#account_match_modal").modal("hide");
    });
    $(".match_status").click(function() {
        if($(this).val() == 2) {
            $(".s_c_m_r").hide();
            $(".sh_c_m_r").hide();
            $(".b_m_r").hide();
            $(".s_s_m_r").hide();
        } else {
            type = $("#account_match_type").val();
            if(type == 1) {
                $(".s_c_m_r").show();
            } else if (type == 2) {
                $(".sh_c_m_r").show();
            } else if (type == 3 || type == 4) {
                $(".b_m_r").show();
            } else if (type == 5) {
                $(".s_s_m_r").show();
            }
        }
    });
    $("#account_match_submit").click(function() {
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

        $.post(base_url+"invest/add_account_match",
        $("#account_match_form").serialize(),
        function(result) {
            $("#account_match_modal").modal("hide");
            $.unblockUI();
        });
    });

    $(".change_rate").click(function() {
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

        $("#share_val_div").html("");
        type_id = $(this).attr('data-id');
        $.post(base_url+"invest/json_get_invest_share_vals_by_type", { id: type_id },
        function(result) {
            data = JSON.parse(result);
            shares = data.share_values.data;
            for(i=0; i < shares.length; i++) {
                share = shares[i];
                val_text = `<div class="row">
                                <div class="form-group">
                                    <label class="col-sm-5 ub_label text-right">`+share.name+`</label>
                                    <div class="col-sm-2">
                                        <input id="share_val_`+share.id+`" name="amount[`+share.id+`]" class="form-control m-b-1 num_input" onKeyUp="format_the_number_decimal(this)" value="`+share.value+`">
                                    </div>
                                    <label class="col-sm-3 ub_label text-left"> บาท  </label>
                                </div>
                            </div>`;
                $("#share_val_div").append(val_text);
            }
            $.unblockUI();
        });
        $("#share_val_modal").modal("show");
    });

    $("#share_all_val_submit").click(function() {
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
        $.post(base_url+"invest/add_invest_share_value_list", $("#share_all_val_form").serialize(),
        function(result) {
            window.location.href =  base_url+'invest';
            $.unblockUI();
        });
    });
    $("#share_all_val_cancel").click(function() {
        $("#share_val_modal").modal("hide");
    });
});

function display_detail(invest_id) {
    $(".selected_invest").removeClass("selected_invest");
    $("#invest_row_"+invest_id).addClass("selected_invest");
    
    $(".interest_add_btn").attr("data_id", invest_id);

    $.get(base_url+"invest/get_invest_by_id?id="+invest_id,
    function(result) {
        data = JSON.parse(result);
        invest = data.data;

        $("#invest_name_"+invest_id).html(invest.name);
        $("#invest_amount_"+invest_id).html(invest.amount_format);
        $("#invest_update_date_"+invest_id).html(invest.update_date_thai);
        $("#invest_status_"+invest_id).html(invest.status == 1 ? "Active" : "Inactive");
        $("#account_edit_btn").attr("data_id", invest_id);
        $("#account_match_invest_id").val(invest_id);
        $("#add_interest_invest_id").val(invest_id);
        if(invest.type == 1) {
            $("#sav_c_edit_btn").attr("data_id", invest_id);
            $("#sav_c_t_name").html(invest.name);
            $("#interest-add-branch-name").html(invest.name);
            $("#sav_c_t_amount").html(invest.amount_format);
            if(invest.org_name) {
                type_detail = $("#type_1_span").html();
                $("#type_1_span").html(type_detail + " : " + invest.org_name);
            }
            interval = "";
            if(invest.detail.invest_interval_left) {
                if(invest.detail.invest_interval_left.y) {
                    interval += invest.detail.invest_interval_left.y + " ปี ";
                }
                if(invest.detail.invest_interval_left.m) {
                    interval += invest.detail.invest_interval_left.m + " เดือน ";
                }
                if(invest.detail.invest_interval_left.d) {
                    interval += invest.detail.invest_interval_left.d + " วัน ";
                }
            } else {
                interval = "ครบกำหนดแล้ว";
            }
            $("#sav_c_t_time_left").html(interval);
            $("#sav_c_t_balance").html(invest.total_balance_format);
            $("#sav_c_t_profit").html(invest.sum_profit_format);
            $("#sav_c_d_amount").html(invest.amount_format);
            if(invest.source) {
                $("#sav_c_d_source").html("("+invest.source+")");
            } else {
                $("#sav_c_d_source").html("");
            }
            if(invest.detail) {
                detail = invest.detail;
                $("#sav_c_d_interest").html(detail.invest_rate_text+" %");
                $("#sav_c_d_start_date").html(detail.start_date_thai);
                $("#sav_c_d_due_date").html(detail.end_date_thai);
                $("#sav_c_d_period").html(detail.payment_method_text);
            } else {
                $("#sav_c_d_interest").html("");
                $("#sav_c_d_start_date").html("");
                $("#sav_c_d_due_date").html("");
                $("#sav_c_d_period").html("");
            }
            status = invest.status == 1 ? 'Active' : 'Inactive';
            $("#sav_c_d_status").html(status);

            profit_ele = $("#profit-tbody");
            profit_ele.html("");
            chart_labels = [''];
            total_profit = invest.profit_perv_sum ? invest.profit_perv_sum : 0;
            chart_values = [total_profit];
            cur_year = new Date().getFullYear();
            if(invest.profits) {
                for(i=1; i < invest.profits.length + 1; i++) {
                    profit = invest.profits[invest.profits.length - i];
                    if(cur_year == profit.date.substring(0, 4)) {
                        td = $(`<tr>
                                    <td class="text-center ">`+profit.date_format+`</td>
                                    <td class="text-center ">`+profit.rate+` % </td>
                                    <td class="text-right ">`+profit.amount_format+`</td>
                                    <td class="text-left">`+profit.note+`</td>
                                    <td class="">
                                        <a class="profit-edit-bth" id="profit-edit-bth-`+profit.id+`" data-id="`+profit.id+`" href="javascript:void(0)">แก้ไข</a>
                                        <a class="profit-del-bth text-danger" id="profit-del-bth-`+profit.id+`" data-id="`+profit.id+`" href="javascript:void(0)">ลบ</a>
                                    </td>
                                </tr>`);
                        profit_ele.append(td);
                    }
                }
                for(i=0; i < invest.profits.length; i++) {
                    profit = invest.profits[i];
                    if(profit.last_five_years == 1) {
                        chart_labels.push(profit.date_format);
                        total_profit += parseFloat(profit.amount);
                        chart_values.push(profit.amount);    
                    } else {
                        chart_values = [profit.amount];
                    }
                }
                if(invest.profits.length == 0) {
                    td = $(`<tr>
                                <td colspan='5' class="text-center ">ไม่พบรายการ</td>
                            </tr>`);
                    profit_ele.append(td);
                }
            }
            chart_labels.push('');

            diff_percent = Math.round(((total_profit*100)/invest.amount)*100)/100;
            if(diff_percent > 0) {
                $("#sav_c_diff_percent").html("+"+diff_percent+"%");
                $("#sav_c_diff_percent_arrow").show();
            } else {
                $("#sav_c_diff_percent").html("");
                $("#sav_c_diff_percent_arrow").hide();
            }
            var ctx = document.getElementById('myChart');
            var ctx1 = ctx.getContext('2d');
            const gradient = ctx1.createLinearGradient(0, 0, 0, 270);
            gradient.addColorStop(0, 'rgba(250,0,0,1)');   
            gradient.addColorStop(1, 'rgba(250,0,0,0)');
            var myChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: chart_labels,
                                        datasets: [{
                                            data: chart_values,
                                            backgroundColor : gradient,
                                            borderColor: [
                                                'rgba(255, 99, 132, 1)'
                                            ],
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        scales: {
                                            yAxes: [{
                                                ticks: {
                                                    beginAtZero: true,
                                                    callback: function (value) {
                                                        return numeral(value).format('0,0.00')
                                                    }
                                                }
                                            }],
                                            xAxes: [{
                                                gridLines : {
                                                    display : false
                                                }
                                            }]
                                        },
                                        responsive: true,
                                        aspectRatio: 3,
                                        legend: {
                                            display: false
                                        }
                                    }
                                });
            $("#sav_c_detail").show();
            $("#share_c_detail").hide();
            $("#bond_c_detail").hide();
            $("#share_s_detail").hide();

            $("#interest_card").show();
            $("#dividend_card").hide();
        } else if (invest.type == 2) {
            $("#share_c_t_name").html(invest.name);
            $("#share_c_m_branch_name").html(invest.name);
            $("#share_c_t_amount").html(invest.amount_format);
            $("#share_c_t_balance").html(invest.total_profit_format);
            $("#share_add_tran_btn").attr("data_id", invest_id);
            $("#share_c_t_amount").html(invest.amount_format);
            $("#share_c_t_balance").html(invest.total_profit_format);
            $("#dividend-add-branch-name").html(invest.name);
            $("#dividend_add_btn").attr("data_id", invest_id);
            $("#share_c_edit_btn").attr("data_id", invest_id);
            $("#dividend_branch_name_t_label").html('ชื่อชุมนุม');
            if(invest.source) {
                $("#share_c_d_soucre").html("("+invest.source+")");
            } else {
                $("#share_c_d_soucre").html("");
            }

            if(invest.org_name) {
                type_detail = $("#type_2_span").html();
                $("#type_2_span").html(type_detail + " : " + invest.org_name);
            }

            tran_ele = $("#share-tran-tbody");
            tran_ele.html("");
            value_per_unit = 0;
            if(invest.transactions) {
                for(i=0; i < invest.transactions.length; i++) {
                    tran = invest.transactions[i];
                    td = $(`<tr>
                                <td class="text-center no_size_padding">`+tran.date_format+`</td>
                                <td class="text-right no_size_padding">`+tran.unit_format+`</td>
                                <td class="text-right no_size_padding">`+tran.amount_format+`</td>
                                <td class="text-left">`+tran.note+`</td>
                                <td class="no_size_padding">
                                    <a class="s-t-c-edit-bth" id="s-t-c-edit-bth-`+tran.id+`" data-id="`+tran.id+`" href="javascript:void(0)">แก้ไข</a>
                                    <a class="s-t-c-del-bth text-danger" id="s-t-c-del-bth-`+tran.id+`" data-id="`+tran.id+`" href="javascript:void(0)">ลบ</a>
                                </td>
                            </tr>`);
                    tran_ele.append(td);
                    value_per_unit = tran.value_per_unit;
                }
            }
            td = $(`<tr>
                        <td class="text-center no_size_padding"></td>
                        <td class="text-center no_size_padding"><label>รวม</label></td>
                        <td class="text-right no_size_padding"><label>`+invest.tran_total_amount_format+`</label></td>
                        <td class="text-left"></td>
                        <td class="no_size_padding"></td>
                    </tr>`);
            tran_ele.append(td);

            profit_ele = $("#dividend-tbody");
            profit_ele.html("");
            chart_labels = [''];
            total_profit = invest.profit_perv_sum ? invest.profit_perv_sum : 0;
            profit_perv_lastest = invest.profit_perv_lastest ? invest.profit_perv_lastest : 0;
            chart_values = [profit_perv_lastest];
            cur_year = new Date().getFullYear();
            if(invest.profits) {
                for(i=1; i < invest.profits.length + 1; i++) {
                    profit = invest.profits[invest.profits.length - i];
                    if(cur_year == profit.date.substring(0, 4)) {
                        td = $(`<tr>
                                    <td class="text-center no_size_padding">`+profit.date_format+`</td>
                                    <td class="text-center no_size_padding">`+profit.rate+` % </td>
                                    <td class="text-right no_size_padding">`+profit.amount_format+`</td>
                                    <td class="text-left">`+profit.note+`</td>
                                    <td class="no_size_padding">
                                        <a class="dividend-edit-bth" id="dividend-edit-bth-`+profit.id+`" data-id="`+profit.id+`" href="javascript:void(0)">แก้ไข</a>
                                        <a class="dividend-del-bth text-danger" id="dividend-del-bth-`+profit.id+`" data-id="`+profit.id+`" href="javascript:void(0)">ลบ</a>
                                    </td>
                                </tr>`);
                        profit_ele.append(td);
                    }
                }
                for(i=0; i < invest.profits.length; i++) {
                    profit = invest.profits[i];
                    if(profit.last_five_years == 1) {
                        chart_labels.push(profit.date_format);
                        total_profit += parseFloat(profit.amount);
                        chart_values.push(profit.amount);    
                    } else {
                        chart_values = [profit.amount];
                    }
                }
                if(invest.profits.length == 0) {
                    td = $(`<tr>
                                <td colspan='5' class="text-center no_size_padding">ไม่พบรายการ</td>
                            </tr>`);
                    profit_ele.append(td);
                }
            }
            chart_labels.push("");
            var ctx = document.getElementById('dividendChart');
            var ctx1 = ctx.getContext('2d');
            const gradient = ctx1.createLinearGradient(0, 0, 0, 270);
            gradient.addColorStop(0, 'rgba(250,0,0,1)');   
            gradient.addColorStop(1, 'rgba(250,0,0,0)');
            var myChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: chart_labels,
                                        datasets: [{
                                            data: chart_values,
                                            backgroundColor : gradient,
                                            borderColor: [
                                                'rgba(255, 99, 132, 1)'
                                            ],
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        scales: {
                                            yAxes: [{
                                                ticks: {
                                                    beginAtZero: true,
                                                    callback: function (value) {
                                                        return numeral(value).format('0,0.00')
                                                    }
                                                }
                                            }],
                                            xAxes: [{
                                                gridLines : {
                                                    display : false
                                                }
                                            }]
                                        },
                                        responsive: true,
                                        aspectRatio: 3,
                                        legend: {
                                            display: false
                                        }
                                    },
                                    responsive: true
                                });

            $("#sav_c_detail").hide();
            $("#share_c_detail").show();
            $("#bond_c_detail").hide();
            $("#share_s_detail").hide();

            $("#interest_card").hide();
            $("#dividend_card").show();
        } else if (invest.type == 3 || invest.type == 4) {
            $("#bond_edit_btn").attr("data_id", invest_id);
            $("#bond_t_name").html(invest.name);
            $("#interest-add-branch-name").html(invest.name);
            $("#bond_t_amount").html(invest.amount_format);
            if(invest.source) {
                $("#bond_d_soucre").html("("+invest.source+")");
            } else {
                $("#bond_d_soucre").html("");
            }

            if(invest.type == 3) {
                if(invest.org_name) {
                    type_detail = $("#bond_help_title").html();
                    $("#bond_help_title").html(type_detail + " : " + invest.org_name);
                }
                $("#bond_help_title").show();
                $("#share_p_help_title").hide();
            } else {
                if(invest.org_name) {
                    type_detail = $("#share_p_help_title").html();
                    $("#share_p_help_title").html(type_detail + " : " + invest.org_name);
                }
                $("#bond_help_title").hide();
                $("#share_p_help_title").show();
            }
            interval = "";
            if(invest.detail.invest_interval_left) {
                if(invest.detail.invest_interval_left.y) {
                    interval += invest.detail.invest_interval_left.y + " ปี ";
                }
                if(invest.detail.invest_interval_left.m) {
                    interval += invest.detail.invest_interval_left.m + " เดือน ";
                }
                if(invest.detail.invest_interval_left.d) {
                    interval += invest.detail.invest_interval_left.d + " วัน ";
                }
            }
            $("#bond_t_time_left").html(interval);
            $("#bond_t_balance").html(invest.total_balance_format);
            $("#bond_t_profit").html(invest.sum_profit_format);
            $("#bond_d_name").html(invest.amount_format);
            if(invest.detail) {
                detail = invest.detail;
                $("#bond_d_name").html(detail.name);
                $("#bond_d_credit_rating").html(detail.credit_rating);
                $("#bond_d_unit").html(detail.unit);
                $("#bond_d_value_per_unit").html(detail.value_per_unit_format);
                $("#bond_d_aver_profit").html(detail.aver_profit_format);
                $("#bond_d_interest").html(detail.invest_rate_text+" %");
                $("#bond_d_start_date").html(detail.start_date_thai);
                $("#bond_d_due_date").html(detail.end_date_thai);
                $("#bond_d_payment_method_text").html(detail.payment_method_text);
            } else {
                $("#bond_d_name").html("");
                $("#bond_d_credit_rating").html("");
                $("#bond_d_unit").html("");
                $("#bond_d_value_per_unit").html("");
                $("#bond_d_aver_profit").html("");
                $("#bond_d_interest").html("");
                $("#bond_d_start_date").html("");
                $("#bond_d_due_date").html("");
                $("#bond_d_payment_method_text").html("");
            }

            profit_ele = $("#profit-tbody");
            profit_ele.html("");
            chart_labels = [''];
            total_profit = invest.profit_perv_sum ? invest.profit_perv_sum : 0;
            chart_values = [total_profit];
            cur_year = new Date().getFullYear();
            if(invest.profits) {
                for(i=1; i < invest.profits.length + 1; i++) {
                    profit = invest.profits[invest.profits.length - i];
                    if(cur_year == profit.date.substring(0, 4)) {
                        td = $(`<tr>
                                    <td class="text-center no_size_padding">`+profit.date_format+`</td>
                                    <td class="text-center no_size_padding">`+profit.rate+` % </td>
                                    <td class="text-right no_size_padding">`+profit.amount_format+`</td>
                                    <td class="text-left">`+profit.note+`</td>
                                    <td class="no_size_padding">
                                        <a class="profit-edit-bth" id="profit-edit-bth-`+profit.id+`" data-id="`+profit.id+`" href="javascript:void(0)">แก้ไข</a>
                                        <a class="profit-del-bth text-danger" id="profit-del-bth-`+profit.id+`" data-id="`+profit.id+`" href="javascript:void(0)">ลบ</a>
                                    </td>
                                </tr>`);
                        profit_ele.append(td);
                    }
                }
                for(i=0; i < invest.profits.length; i++) {
                    profit = invest.profits[i];
                    if(profit.last_five_years == 1) {
                        chart_labels.push(profit.date_format);
                        total_profit += parseFloat(profit.amount);
                        chart_values.push(profit.amount);    
                    } else {
                        chart_values = [profit.amount];
                    }
                }
                if(invest.profits.length == 0) {
                    td = $(`<tr>
                                <td colspan='5' class="text-center no_size_padding">ไม่พบรายการ</td>
                            </tr>`);
                    profit_ele.append(td);
                }
            }
            chart_labels.push('');

            diff_percent = Math.round(((total_profit*100)/invest.amount)*100)/100;
            if(diff_percent > 0) {
                $("#bond_diff_percent").html("+"+diff_percent+"%");
                $("#bond_diff_percent_arrow").show();
            } else {
                $("#bond_diff_percent").html("");
                $("#bond_diff_percent_arrow").hide();
            }
            
            var ctx = document.getElementById('myChart');
            var ctx1 = ctx.getContext('2d');
            const gradient = ctx1.createLinearGradient(0, 0, 0, 270);
            gradient.addColorStop(0, 'rgba(250,0,0,1)');   
            gradient.addColorStop(1, 'rgba(250,0,0,0)');
            var myChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: chart_labels,
                                        datasets: [{
                                            data: chart_values,
                                            backgroundColor : gradient,
                                            borderColor: [
                                                'rgba(255, 99, 132, 1)'
                                            ],
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        scales: {
                                            yAxes: [{
                                                ticks: {
                                                    beginAtZero: true,
                                                    callback: function (value) {
                                                        return numeral(value).format('0,0.00')
                                                    }
                                                }
                                            }],
                                            xAxes: [{
                                                gridLines : {
                                                    display : false
                                                }
                                            }]
                                        },
                                        responsive: true,
                                        aspectRatio: 3,
                                        legend: {
                                            display: false
                                        }
                                    }
                                });

            $("#sav_c_detail").hide();
            $("#share_c_detail").hide();
            $("#bond_c_detail").show();
            $("#share_s_detail").hide();

            $("#interest_card").show();
            $("#dividend_card").hide();
        } else if (invest.type == 5) {
            current_amount = data.share_value.last ? numeral(invest.tran_fif_total_unit).value() * numeral(data.share_value.last.value).value() : 0;
            $("#share_s_t_name").html(invest.name);
            $("#share_s_t_amount").html(invest.amount_format);
            $("#share_s_t_balance").html(numeral(current_amount).format('0,0.00'));
            $("#share_s_t_diff").html(numeral(current_amount - invest.amount).format('0,0.00'))
            $("#share_s_rate_edit_btn").attr("data_id", invest.id);
            $("#share_s_m_branch_name").html(invest.name);
            $("#share_s_edit_btn").attr("data_id", invest.id);
            $("#share_s_add_tran_btn").attr("data_id", invest.id);
            $("#dividend_add_btn").attr("data_id", invest.id);
            $("#dividend-add-branch-name").html(invest.name);
            $("#dividend_branch_name_t_label").html('ชื่อหุ้น');
            $("#share_s_d_info").html("("+numeral(invest.tran_fif_total_unit).format('0,0')+" หุ้น ต้นทุนเฉลี่ย "+numeral(invest.average_share_value).format('0,0.00')+")")
            if(invest.source) {
                $("#share_s_d_soucre").html("("+invest.source+")");
            } else {
                $("#share_s_d_soucre").html("");
            }

            if(invest.org_name) {
                type_detail = $("#type_5_span").html();
                $("#type_5_span").html(type_detail + " : " + invest.org_name);
            }

            share_value = data.share_value;
            if(share_value && share_value.last) {
                diff = current_amount - invest.amount;
                if(diff > 0) {
                    var  diff_percent = diff * 100 / invest.amount;
                    $("#share_s_rate_diff").html("(+"+diff_percent.toFixed(2)+"%)");
                    $("#share_s_rate_diff").addClass("helpblock_plus");
                    $("#share_s_rate_diff").removeClass("helpblock_minus");
                } else if (diff < 0) {
                    diff = diff * (-1);
                    diff_percent = diff * 100 / invest.amount;
                    $("#share_s_rate_diff").html("(-"+diff_percent.toFixed(2)+"%)");
                    $("#share_s_rate_diff").removeClass("helpblock_plus");
                    $("#share_s_rate_diff").addClass("helpblock_minus");
                } else {
                    $("#share_s_rate_diff").html("(0)");
                    $("#share_s_rate_diff").removeClass("helpblock_plus");
                    $("#share_s_rate_diff").removeClass("helpblock_minus");
                }
                $("#share_s_t_rate").html(share_value.last.value_format);
                $("#share_s_rate_date").html("มูลค่าหุ้น ณ "+share_value.last.date_thai);
            } else {
                $("#share_s_t_rate").html("-");
                $("#share_s_rate_date").html("มูลค่าหุ้น ณ -");
            }

            diff = invest.tran_fif_total_amount - invest.amount;
            if(diff > 0) {
                $("#share_s_diff_percent").html("+"+diff+"%");
                $("#share_s_diff_percent_arrow_plus").show();
                $("#share_s_diff_percent_arrow_minus").hide();
                $("#share_s_diff_percent").addClass("helpblock_plus");
                $("#share_s_diff_percent").removeClass("helpblock_minus");
            } else if (diff < 0) {
                diff = diff * (-1);
                $("#share_s_diff_percent").html("+"+diff+"%");
                $("#share_s_diff_percent_arrow_plus").hide();
                $("#share_s_diff_percent_arrow_minus").show();
                $("#share_s_diff_percent").removeClass("helpblock_plus");
                $("#share_s_diff_percent").addClass("helpblock_minus");
            } else {
                $("#share_s_diff_percent").html("");
                $("#share_s_diff_percent_arrow_plus").hide();
                $("#share_s_diff_percent_arrow_minus").hide();
                $("#share_s_diff_percent").removeClass("helpblock_plus");
                $("#share_s_diff_percent").removeClass("helpblock_minus");
            }

            $("#share_s_t_profit").html(invest.sum_profit_format);

            tran_buy_ele = $("#share-s-tran-tbody-buy");
            tran_sell_ele = $("#share-s-tran-tbody-sell");
            tran_buy_ele.html("");
            tran_sell_ele.html("");
            value_per_unit = 0;
            has_buy = 0;
            has_sell = 0;
            if(invest.transactions) {
                if(invest.transactions.length > 0) {
                    share_buy_unit = 0;
                    share_buy_amount = 0;
                    share_buy_fee = 0;
                    share_sell_unit = 0;
                    share_sell_amount = 0;
                    share_sell_fee = 0;
                    average_share_value = 0;
                    total_profit = 0;
                    for(i=0; i < invest.transactions.length; i++) {
                        tran = invest.transactions[i];
                        tran_type_text = tran.tran_type == 1 ? "ซื้อ" : "ขาย";
                        if(tran.tran_type == 1) {
                            td = $(`<tr>
                                        <td class="text-center">`+tran.date_format+`</td>
                                        <td class="text-right">`+tran.unit_format+`</td>
                                        <td class="text-right">`+tran.amount_format+`</td>
                                        <td class="text-right">`+tran.value_per_unit_format+`</td>
                                        <td class="text-right">`+tran.fee_format+`</td>
                                        <td class="">
                                            <a class="s-t-s-edit-bth" id="s-t-s-edit-bth-`+tran.id+`" data-id="`+tran.id+`" href="javascript:void(0)">แก้ไข</a>
                                            <a class="s-t-s-del-bth text-danger" id="s-t-s-del-bth-`+tran.id+`" data-id="`+tran.id+`" href="javascript:void(0)">ลบ</a>
                                        </td>
                                    </tr>`);
                            tran_buy_ele.append(td);
                            value_per_unit = tran.value_per_unit_format;
                            has_buy = 1;
                            share_buy_unit += numeral(tran.unit).value();
                            share_buy_amount += numeral(tran.amount).value();
                            share_buy_fee += numeral(tran.fee).value();
                        } else {
                            profit = numeral(tran.amount_format).value() - (numeral(tran.unit).value() * numeral(invest.average_share_value).value())
                            td = $(`<tr>
                                        <td class="text-center">`+tran.date_format+`</td>
                                        <td class="text-right">`+tran.unit_format+`</td>
                                        <td class="text-right">`+tran.amount_format+`</td>
                                        <td class="text-right">`+numeral(profit).format('0,0.00')+`</td>
                                        <td class="text-right">`+tran.fee_format+`</td>
                                        <td class="">
                                            <a class="s-t-s-edit-bth" id="s-t-s-edit-bth-`+tran.id+`" data-id="`+tran.id+`" href="javascript:void(0)">แก้ไข</a>
                                            <a class="s-t-s-del-bth text-danger" id="s-t-s-del-bth-`+tran.id+`" data-id="`+tran.id+`" href="javascript:void(0)">ลบ</a>
                                        </td>
                                    </tr>`);
                            tran_sell_ele.append(td);
                            value_per_unit = tran.value_per_unit_format;
                            has_sell = 1;
                            share_sell_unit += numeral(tran.unit).value();
                            share_sell_amount += numeral(tran.amount).value();
                            share_sell_fee += numeral(tran.fee).value();
                            total_profit += profit;
                        }
                    }
                    if(has_buy == 1) {
                        average_share_value = share_buy_amount/share_buy_unit;
                        td = $(`<tr>
                                    <td class="text-center"><label>รวม</label></td>
                                    <td class="text-right"><label>`+numeral(share_buy_unit).format('0,0.00')+`</label></td>
                                    <td class="text-right"><label>`+numeral(share_buy_amount).format('0,0.00')+`</label></td>
                                    <td class="text-right"><label>`+numeral(average_share_value).format('0,0.00')+`</label></td>
                                    <td class="text-right">`+numeral(share_buy_fee).format('0,0.00')+`</td>
                                    <td class="text-center"></td>
                                </tr>`);
                        tran_buy_ele.append(td);
                    } else {
                        td = $(`<tr>
                                    <td colspan="6" class="text-center no_size_padding">ไม่พบรายการ</td>
                                </tr>`);
                        tran_buy_ele.append(td);
                    }

                    if(has_sell == 1) {
                        td = $(`<tr>
                                    <td class="text-center"><label>รวม</label></td>
                                    <td class="text-right"><label>`+numeral(share_sell_unit).format('0,0.00')+`</label></td>
                                    <td class="text-right"><label>`+numeral(share_sell_amount).format('0,0.00')+`</label></td>
                                    <td class="text-right"><label>`+numeral(total_profit).format('0,0.00')+`</label></td>
                                    <td class="text-right">`+numeral(share_sell_fee).format('0,0.00')+`</td>
                                    <td class="text-center"></td>
                                </tr>`);
                        tran_sell_ele.append(td);
                    } else {
                        td = $(`<tr>
                                    <td colspan="6" class="text-center no_size_padding">ไม่พบรายการ</td>
                                </tr>`);
                        tran_sell_ele.append(td);
                    }
                } else {
                    td = $(`<tr>
                                <td colspan="6" class="text-center no_size_padding">ไม่พบรายการ</td>
                            </tr>`);
                    tran_buy_ele.append(td);
                    td = $(`<tr>
                                <td colspan="6" class="text-center no_size_padding">ไม่พบรายการ</td>
                            </tr>`);
                    tran_sell_ele.append(td);
                }
            }

            profit_ele = $("#dividend-tbody");
            profit_ele.html("");
            chart_labels = [''];
            total_profit = invest.profit_perv_sum ? invest.profit_perv_sum : 0;
            chart_values = [total_profit];
            cur_year = new Date().getFullYear();
            if(invest.profits) {
                for(i=1; i < invest.profits.length + 1; i++) {
                    profit = invest.profits[invest.profits.length - i];
                    if(cur_year == profit.date.substring(0, 4)) {
                        td = $(`<tr>
                                    <td class="text-center">`+profit.date_format+`</td>
                                    <td class="text-center ">`+profit.rate+` % </td>
                                    <td class="text-right">`+profit.amount_format+`</td>
                                    <td class="text-left">`+profit.note+`</td>
                                    <td class="">
                                        <a class="profit-edit-bth" id="profit-edit-bth-`+profit.id+`" data-id="`+profit.id+`" href="javascript:void(0)">แก้ไข</a>
                                        <a class="profit-del-bth text-danger" id="profit-del-bth-`+profit.id+`" data-id="`+profit.id+`" href="javascript:void(0)">ลบ</a>
                                    </td>
                                </tr>`);
                        profit_ele.append(td);
                    }
                }
                for(i=0; i < invest.profits.length; i++) {
                    profit = invest.profits[i];
                    if(profit.last_five_years == 1) {
                        chart_labels.push(profit.date_format);
                        total_profit += parseFloat(profit.amount);
                        chart_values.push(profit.amount);    
                    } else {
                        chart_values = [profit.amount];
                    }
                }
                if(invest.profits.length == 0) {
                    td = $(`<tr>
                                <td colspan='5' class="text-center">ไม่พบรายการ</td>
                            </tr>`);
                    profit_ele.append(td);
                }
            }
            chart_labels.push('');
            var ctx = document.getElementById('dividendChart');
            var ctx1 = ctx.getContext('2d');
            const gradient = ctx1.createLinearGradient(0, 0, 0, 270);
            gradient.addColorStop(0, 'rgba(250,0,0,1)');   
            gradient.addColorStop(1, 'rgba(250,0,0,0)');
            var myChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: chart_labels,
                                        datasets: [{
                                            data: chart_values,
                                            backgroundColor : gradient,
                                            borderColor: [
                                                'rgba(255, 99, 132, 1)'
                                            ],
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        scales: {
                                            yAxes: [{
                                                ticks: {
                                                    beginAtZero: true,
                                                    callback: function (value) {
                                                        return numeral(value).format('0,0.00')
                                                    }
                                                },
                                                afterFit: function(scale) {
                                                    scale.height = 10 //<-- set value as you wish 
                                                }
                                            }],
                                            xAxes: [{
                                                gridLines : {
                                                    display : false
                                                }
                                            }]
                                        },
                                        responsive: true,
                                        aspectRatio: 3,
                                        legend: {
                                            display: false
                                        }
                                    },
                                });

            $("#sav_c_detail").hide();
            $("#share_c_detail").hide();
            $("#bond_c_detail").hide();
            $("#share_s_detail").show();
            $("#interest_card").hide();
            $("#dividend_card").show();
        }

        $("#card_title_invest_payment").html(data.total_data.invest_payment_format);
        $("#card_title_profit").html(data.total_data.profit_format);
        $.unblockUI();
    });
}

function format_the_number_decimal(ele){
    var value = $('#'+ele.id).val();
    value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    var num = value.split(".");
    var decimal = '';
    var num_decimal = '';
    if(typeof num[1] !== 'undefined'){
        if(num[1].length > 2){
            num_decimal = num[1].substring(0, 2);
        }else{
            num_decimal =  num[1];
        }
        decimal =  "."+num_decimal;
    }
    if(value!=''){
        if(value == 'NaN'){
            $('#'+ele.id).val(0);
        }else{
            value = (num[0] == '')?0:parseInt(num[0]);
            value = value.toLocaleString()+decimal;
            $('#'+ele.id).val(value);
        }
    }else{
        $('#'+ele.id).val(0);
    }
}

function removeCommas(str) {
    return(str.replace(/,/g,''));
}

function createSelect2(id){
	$('.js-data-example-ajax').select2({
		dropdownParent: $("#"+id),
		matcher: matchStart
	});
}

function matchStart(params, data) {
	// If there are no search terms, return all of the data
	if ($.trim(params.term) === '') {
	  return data;
	}

	// Display only term macth with text begin chars
	if(data.text.indexOf(params.term) == 0) {
		return data;
	}

	// Return `null` if the term should not be displayed
	return null;
}
