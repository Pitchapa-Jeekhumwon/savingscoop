$( document ).ready(function() {
	$("#account_datetime").datepicker({
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
	$(".form_date_picker").datepicker({
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
	$("#btn-add-account-detail").click(function() {
	});
	$(document).on("change",".acc_input",function() {
		cal_acc_input();
	});

	$("#submit_auth_confirm").click(function() {
		var confirm_user = $('#confirm_user').val();
		var confirm_pwd = $('#confirm_pwd').val();
		if($("#confirm_sp_action").val() == 'cancel' || $("#confirm_sp_action").val() == 'edit_tran' || $("#confirm_sp_action").val() == 'edit_cash' || $("#confirm_sp_action").val() == 'edit_sp') {
			var account_id = "";
			if($("#confirm_sp_action").val() == 'edit_tran') {
				account_id = $("#account_id_tran").val();
			} else if ($("#confirm_sp_action").val() == 'edit_cash') {
				account_id = $("#account_id_cash").val();
			} else if ($("#confirm_sp_action").val() == 'edit_sp') {
				account_id = $("#account_id_sp").val();
			}
			$.ajax({
				method: 'POST',
				url: base_url+'account/check_add_edit_auth',
				data: {
					username : confirm_user,
					password : confirm_pwd,
					account_id : account_id,
				},
				dataType: 'json',
				success: function(data){
					if(data.result=="true") {
						if($("#confirm_sp_action").val() == 'cancel') {
							$("#form1_cancel").submit();
						} else if ($("#confirm_sp_action").val() == 'edit_tran') {
							$(".debit_input").each(function() {
								$(this).val(removeCommas($(this).val()));
							});
							$(".credit_input").each(function() {
								$(this).val(removeCommas($(this).val()));
							});
							$('#form1').submit();
						} else if ($("#confirm_sp_action").val() == 'edit_cash') {
							$(".acc_input").each(function( index ) {
								$(this).val(removeCommas($(this).val()));
							});
							$('#form1_cash').submit();
						} else if ($("#confirm_sp_action").val() == 'edit_sp') {
							$(".debit_input").each(function() {
								$(this).val(removeCommas($(this).val()));
							});
							$(".credit_input").each(function() {
								$(this).val(removeCommas($(this).val()));
							});
							$('#form_sp').submit();
						}
					} else {
						swal(data.message);
					}
				}
			});
		} else if($("#confirm_sp_action").val() == 'close_account' || $("#confirm_sp_action").val() == 'open_account') {
			$.ajax({
				method: 'POST',
				url: base_url+'account/check_close_year_auth',
				data: {
					username : confirm_user,
					password : confirm_pwd,
				},
				dataType: 'json',
				success: function(data){
					if(data.result=="true") {
						if($("#confirm_sp_action").val() == 'close_account') {
							$("#form_close_budget_year").submit();
						} else if($("#confirm_sp_action").val() == 'open_account') {
							$("#form_open_budget_year").submit();
						}
					} else {
						swal(data.message);
					}
				}
			});
		} else if ($("#confirm_sp_action").val() == 'open_daily') {
			$.ajax({
				method: 'POST',
				url: base_url+'account/check_close_daily_auth',
				data: {
					username : confirm_user,
					password : confirm_pwd,
				},
				dataType: 'json',
				success: function(data){
					if(data.result=="true") {
						run_open_daily();
					} else {
						swal(data.message);
					}
				}
			});
		} else if ($("#confirm_sp_action").val() == 'close_daily') {
			$.ajax({
				method: 'POST',
				url: base_url+'account/check_close_daily_auth',
				data: {
					username : confirm_user,
					password : confirm_pwd,
				},
				dataType: 'json',
				success: function(data){
					if(data.result=="true") {
						run_close_daily();
					} else {
						swal(data.message);
					}
				}
			});
		}
	});
});

function open_modal(id){
	$('#'+id).modal('show');
}

function close_modal(id){
	$('#'+id).modal('hide');
}

function clear_modal(id){
	$('#account_description').val('');
	$('#account_data').html('');
	$('#account_data_cash').html('')
	$('#account_data_sp').html('');
	$('#add_account_cash').modal('hide');
	$('#add_account_tran').modal('hide');
	$('#add_account_type').modal('hide');
	$('#add_account_sp').modal('hide');

	$("#modal_check").val(0)
	$("#nature_check").val('debit');
}

function add_account(){
	open_modal('add_account_type');
}

function tran_modal(type){
	$('#add_account_type').modal('hide');
	var date = new Date();
	var day = date.getDate() < 10 ? "0"+date.getDate() : date.getDate();
	var month = date.getMonth() < 10 ? "0"+(date.getMonth() + 1) : date.getMonth() + 1;
	var year = date.getFullYear() + 543;

	$("#modal_check").val(type)

	if(type == 1) {
		$(".add-tr").remove();
		$("#account_id_cash").val('');
		$("#account_datetime_cash").val(day+"/"+month+"/"+year);
		$("#account_description_cash").val('');
		$("#account_chart_id_cash_0").val("");
		$("#acc_desc_0").val("");
		$("#acc_0").val("");
		$("#sum_cash").val(0);
		$("#user_cash_row").css("display","none");
		createSelect2("add_account_cash");
		$("#add_account_cash").modal({
			backdrop: 'static',
			keyboard: false  // to prevent closing with Esc button (if you want this too)
		});
		$("#add_account_cash").data('bs.modal').options.backdrop = 'static';
	} else if (type == 2) {
		$(".add-tr").remove();
		$("#account_id_tran").val('');
		$("#account_datetime").val(day+"/"+month+"/"+year);
		$("#account_description").val('');
		$("#sum_debit").val(0);
		$("#sum_credit").val(0);
		$("#journal_type_tran").val("JV");
		$("#user_tran_row").css("display","none");
		createSelect2("add_account_tran");
		$("#add_account_tran").modal({
			backdrop: 'static',
			keyboard: false  // to prevent closing with Esc button (if you want this too)
		});
		$("#add_account_tran").data('bs.modal').options.backdrop = 'static';
	} else if (type == 3) {
		$(".add-tr").remove();
		$("#account_id_tran").val('');
		$("#account_datetime").val(day+"/"+month+"/"+year);
		$("#account_description").val('');
		$("#sum_debit").val(0);
		$("#sum_credit").val(0);
		$("#journal_type_tran").val("SV");
		$("#user_tran_row").css("display","none");
		createSelect2("add_account_tran");
		$("#add_account_tran").modal({
			backdrop: 'static',
			keyboard: false  // to prevent closing with Esc button (if you want this too)
		});
		$("#add_account_tran").data('bs.modal').options.backdrop = 'static';
	} else if (type == 4) {
		$(".add-tr").remove();
		$("#account_id_sp").val('');
		$("#account_datetime_sp").val(day+"/"+month+"/"+year);
		$("#account_description_sp").val('');
		$("#sum_debit_sp").val(0);
		$("#sum_credit_sp").val(0);
		$("#journal_type_sp").val("SV");
		$("#user_sp_row").css("display","none");
		createSelect2("add_account_sp");
		$("#add_account_sp").modal({
			backdrop: 'static',
			keyboard: false  // to prevent closing with Esc button (if you want this too)
		});
		$("#add_account_sp").data('bs.modal').options.backdrop = 'static';
	}
}

function call_sum_credit_debit(number,type) {
	var debit_input_now = 0;
	var credit_input_now = 0;
	var i = 0;

	$(".debit_input").each(function() {
		if(parseFloat(removeCommas($(this).val())) == NaN || $(this).val() == ''){
		}else{
			debit_input_now += parseFloat(removeCommas($(this).val()));
		}
	});
	$(".credit_input").each(function() {
		if(parseFloat(removeCommas($(this).val())) == NaN || $(this).val() == ''){
		}else{
			credit_input_now += parseFloat(removeCommas($(this).val()));
		}
	});

	credit_input_now = credit_input_now.toFixed(2);
	debit_input_now = debit_input_now.toFixed(2);
	//แสดงผลรวมของบัญชีฝั่งเคดิต และเดบิต
	$('#sum_debit').val(debit_input_now);
	$('#sum_credit').val(credit_input_now);
	$('#sum_diff').val((debit_input_now - credit_input_now).toFixed(2));
	format_the_number_decimal(document.getElementById("sum_debit"));
	format_the_number_decimal(document.getElementById("sum_credit"));
	format_the_number_decimal(document.getElementById("sum_diff"));
	$('#sum_debit_sp').val(debit_input_now);
	$('#sum_credit_sp').val(credit_input_now);
	$('#sum_diff_sp').val((debit_input_now - credit_input_now).toFixed(2));
	format_the_number_decimal(document.getElementById("sum_debit_sp"));
	format_the_number_decimal(document.getElementById("sum_credit_sp"));
	format_the_number_decimal(document.getElementById("sum_diff_sp"));
}

function form_submit(){
	var text_alert = '';
	var void_input = 0;
	var debit_input = 0;
	var credit_input = 0;
	var has_none_select_chart = 0;
	if($('#account_datetime').val()==''){
		text_alert += ' - กรุณาระบุวันที่ของรายการ\n';
	}
	if($('#account_description').val()==''){
		text_alert += ' - กรุณาระบุรายละเอียดของรายการ\n';
	}

	$('[id^=sel_input_]').each(function(){
		if($(this).val()==''){
			has_none_select_chart = 1;
		}
	});
	if(has_none_select_chart>0){
		text_alert += ' - กรุณาระบุเลือก ผังบัญชี ให้ครบถ้วน\n';
	}

	$('.account_detail').each(function(){
		if($(this).val()==''){
			void_input++;
		}
	});
	$(".account_detail_sel").each(function() {
		if($(this).val()==''){
			void_input++;
		}
	});
	if(void_input>0){
		text_alert += ' - กรุณาระบุข้อมูล เดบิต เครดิต ให้ครบถ้วน\n';
	}
	$('.debit_input').each(function(){
		debit_input = parseFloat(debit_input) + parseFloat(removeCommas($(this).val()));
	});
	$('.credit_input').each(function(){
		credit_input = parseFloat(credit_input) + parseFloat(removeCommas($(this).val()));
	});
	if($("#sum_credit").val() != $("#sum_debit").val()){
		text_alert += ' - กรุณาลงรายการ เดบิต และ เครดิตให้เท่ากัน\n';
	}

	if(text_alert!=''){
		swal('เกิดข้อผิดพลาด',text_alert,'warning');
	}else{
		$.ajax({
            url:base_url+"account/check_submit_date",
            method:"POST",
            data: {'date':$("#account_datetime").val()},
            dataType:"text",
            success:function(result){
				data = JSON.parse(result);
                if(data.status == 'success'){
					if($("#account_id_tran").val() && $("#enable_edit_delete_permission").val() == 1) {
						$("#confirm_sp_action").val("edit_tran");
						$("#confirm_sp_req").modal("show");
					} else {
						$(".debit_input").each(function() {
							$(this).val(removeCommas($(this).val()));
						});
						$(".credit_input").each(function() {
							$(this).val(removeCommas($(this).val()));
						});
						$('#form1').submit();
					}
                } else {
					swal('เกิดข้อผิดพลาด',data.message,'warning');
				}
            }
        });
	}
}

function form_submit_sp(){
	var text_alert = '';
	var void_input = 0;
	var debit_input = 0;
	var credit_input = 0;
	var has_none_select_chart = 0;
	if($('#account_datetime_sp').val()==''){
		text_alert += ' - กรุณาระบุวันที่ของรายการ\n';
	}
	if($('#account_description_sp').val()==''){
		text_alert += ' - กรุณาระบุรายละเอียดของรายการ\n';
	}
	$('.account_detail').each(function(){
		if($(this).val()==''){
			void_input++;
		}
	});
	$(".account_detail_sel").each(function() {
		if($(this).val()==''){
			void_input++;
		}
	});

	$('[id^=sel_input_]').each(function(){
		if($(this).val()==''){
			has_none_select_chart = 1;
		}
	});
	if(has_none_select_chart>0){
		text_alert += ' - กรุณาระบุเลือก ผังบัญชี ให้ครบถ้วน\n';
	}

	if(void_input>0){
		text_alert += ' - กรุณาระบุข้อมูล เดบิต เครดิต ให้ครบถ้วน\n';
	}
	$('.debit_input').each(function(){
		debit_input = parseFloat(debit_input) + parseFloat(removeCommas($(this).val()));
	});
	$('.credit_input').each(function(){
		credit_input = parseFloat(credit_input) + parseFloat(removeCommas($(this).val()));
	});
	if($("#sum_credit").val() != $("#sum_debit").val()){
		text_alert += ' - กรุณาลงรายการ เดบิต และ เครดิตให้เท่ากัน\n';
	}

	if(text_alert!='') {
		swal('เกิดข้อผิดพลาด',text_alert,'warning');
	} else {
		$.ajax({
            url:base_url+"account/check_submit_date",
            method:"POST",
            data: {'date':$("#account_datetime_sp").val()},
            dataType:"text",
            success:function(result){
				data = JSON.parse(result);
                if(data.status == 'success'){
					if($("#account_id_sp").val() && $("#enable_edit_delete_permission").val() == 1) {
						$("#confirm_sp_action").val("edit_sp");
						$("#confirm_sp_req").modal("show");
					} else {
						$(".debit_input").each(function() {
							$(this).val(removeCommas($(this).val()));
						});
						$(".credit_input").each(function() {
							$(this).val(removeCommas($(this).val()));
						});
						$('#form_sp').submit();
					}
                } else {
					swal('เกิดข้อผิดพลาด',data.message,'warning');
				}
            }
        });
	}
}

function form_cash_submit() {
	var text_alert = '';
	var void_input = 0;
	var has_none_select_chart = 0;

	if(!$('#pay_type_0').is(':checked') && !$('#pay_type_1').is(':checked')) {
		text_alert += ' - กรุณาเลือกประเภทการชำระเงิน\n';
	}
	if($('#account_datetime_cash').val()==''){
		text_alert += ' - กรุณาระบุวันที่ของรายการ\n';
	}
	if($('#account_description_cash').val()==''){
		text_alert += ' - กรุณาระบุรายละเอียดของรายการ\n';
	}
	$('.acc_input').each(function(){
		if($(this).val()==''){
			void_input++;
		}
	});
	if(void_input>0){
		text_alert += ' - กรุณาระบุจำนวนให้ครบถ้วน\n';
	}

	$('[id^=account_chart_id_cash_]').each(function(){
		if($(this).val()==''){
			has_none_select_chart = 1;
		}
	});
	if(has_none_select_chart>0){
		text_alert += ' - กรุณาระบุเลือก ผังบัญชี ให้ครบถ้วน\n';
	}

	if(text_alert!=''){
		swal('เกิดข้อผิดพลาด',text_alert,'warning');
	}else{
		$.ajax({
            url:base_url+"account/check_submit_date",
            method:"POST",
            data: {'date':$("#account_datetime_cash").val()},
            dataType:"text",
            success:function(result){
				data = JSON.parse(result);
                if(data.status == 'success'){
                    if($("#account_id_cash").val() && $("#enable_edit_delete_permission").val() == 1) {
						$("#confirm_sp_action").val("edit_cash");
						$("#confirm_sp_req").modal("show");
					} else {
						$(".acc_input").each(function( index ) {
							$(this).val(removeCommas($(this).val()));
						});
						$('#form1_cash').submit();
					}
                } else {
					swal('เกิดข้อผิดพลาด',data.message,'warning');
				}
            }
        });
	}
}

function account_excel_tranction_voucher(detail,date,account_detail_id){
	$('#detail').val(detail);
	$('#date').val(date);
	$('#account_detail_id').val(account_detail_id);
	$('#from_excel_day').submit();
}

function account_pdf_tranction_voucher(detail,date,account_detail_id){
	$('#detail_pdf').val(detail);
	$('#date_pdf').val(date);
	$('#account_detail_id_pdf').val(account_detail_id);
	$('#from_pdf_day').submit();
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
			$('#'+ele.id).val('');
		}else{
			value = (num[0] == '')?0:parseInt(num[0]);
			value = value.toLocaleString()+decimal;
			$('#'+ele.id).val(value);
		}
	}else{
		$('#'+ele.id).val('');
	}
}

function createSelect2(id){
	$('.js-data-example-ajax').select2({
		dropdownParent: $("#"+id),
		matcher: matchStart
	});
}

function cal_acc_input() {
	total = 0;
	$('.acc_input').each(function(){
		total += !isNaN(parseFloat(removeCommas($(this).val()))) ? parseFloat(removeCommas($(this).val())) : 0;
	});

	$("#sum_cash").val(total.toFixed(2));
	format_the_number_decimal(document.getElementById("sum_cash"));
}

function removeCommas(str) {
	return(str.replace(/,/g,''));
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

function close_account() {
	year = $("#budget_year_for_close").val();
	swal({
		title: "ท่านต้องการปิดปีบัญชีปี " + year + " ใช่หรือไม่?",
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
			$("#confirm_sp_action").val("close_account");
			$("#confirm_sp_req").modal("show");
		} else {
		}
	});
}

function open_account() {
	year = $("#budget_year_for_open").val();
	swal({
		title: "ท่านต้องการเปิดปีบัญชีปี " + year + " ใช่หรือไม่?",
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
			$("#confirm_sp_action").val("open_account");
			$("#confirm_sp_req").modal("show");
		} else {
		}
	});
}