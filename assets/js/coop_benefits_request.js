var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
	var member_id = $("#member_id").val();
	if(member_id == ''){
		$("#bt_view").prop("disabled", true);
		$("#bt_add").prop("disabled", true);
	}
	
	$('#myModal').on('shown.bs.modal', function () {
		$('#search_text').focus();
	})  

	$(document).on('click', '.scholarship_radio', function() {
		$(".benefits_approved_req").val(numeral($(this).attr("data-amount")).format('0,0.00'));
	});
	$(document).on('click', '.dismemberment_radio', function() {
		$(".benefits_approved_req").val(numeral($(this).attr("data-amount")).format('0,0.00'));
	});

	$("#submit_auth_confirm").on('click', function (){
		var confirm_user = $('#confirm_user').val();
		var confirm_pwd = $('#confirm_pwd').val();	
		$.ajax({
			method: 'POST',
			url: base_url+'benefits/auth_confirm_sp_req',
			data: {
				confirm_user : confirm_user,
				confirm_pwd : confirm_pwd
			},
			dataType: 'json',
			success: function(data){
				if(data.result=="true"){
					if(data.permission=="true"){
						$('#from_save').submit();
					}else{
						swal("ไม่มีสิทธิ์ทำรายการยกเลิก");
					}
				}else{
					swal("ตรวจสอบข้อมูลให้ถูกต้อง");
				}
			}
		});
	});

	$(document).on("change","#tre_i_day_count",function() {
		day_num = numeral($(this).val()).value();
		amount = numeral(removeCommas($("#selected_choice_tre_i_id").attr("data-amount"))).value();
		$("#benefits_approved_amount").val(numeral(day_num * amount).format('0,0.00'));
	});

	$(document).on("change", "#tre_i_day_count", function() {
		amount = numeral($("#selected_choice_tre_i_id").attr("data-amount")).value() * numeral($("#tre_i_day_count").val()).value();
		$("#benefits_approved_amount").val(numeral(amount).format('0,0.00'))
	});

	$(document).on("click", "#selected_choice_tre_i_id", function() {
		amount = numeral($("#selected_choice_tre_i_id").attr("data-amount")).value() * numeral($("#tre_i_day_count").val()).value();
		$("#benefits_approved_amount").val(numeral(amount).format('0,0.00'))
	});

	$(document).on("click", "#selected_choice_tre_o_id", function() {
		$(".benefits_approved_req").val(numeral($(this).attr("data-amount")).format('0,0.00'));
	})
});

function check_form(){
	if ($('#benefits_check_condition').is(':checked') && $("#benefits_type_id").val()) {
		var benefits_type_id = $("#benefits_type_id").val();
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
			baseZ: 5001,
			bindEvents: false
		});
		$.ajax({
			type: "POST",
			url: base_url+'benefits/ajax_check_benefits_req_conditions',
			data: {
				id : benefits_type_id,
				lastest_condition_created : $("#lastest_condition_created").val(),
				card_id : $("#card_id").val(),
				age_grester : $("#age_grester").val(),
				member_age_grester : $("#member_age_grester").val(),
				work_age_grester : $("#work_age_grester").val(),
				request_time : $("#request_time").val(),
				request_time_unit : $("#request_time_unit").val(),
				member_id : $("#member_id").val(),
				benefits_request_id : $("#benefits_request_id").val()
			},
			success: function(result) {
				data = $.parseJSON(result);

				$.unblockUI();
				if(data["status"] == "success") {
					$('#from_save').submit();
				} else {
					if(data["status"] == "not_success") {
						swal({
							title: "ท่านต้องการคำเนินการต่อหรือไม่",
							text: data["warning_text"],
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
								$('#confirm_sp_req').modal('show');
							} else {
							}
						});
					} else if (data["status"] == "not_member") {
						swal({
							title: "ท่านต้องการคำเนินการต่อหรือไม่",
							text: data["warning_text"],
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
								$('#confirm_sp_req').modal('show');
							} else {
							}
						});
					}
				}
			}
		});
	} else {
		textAlert = "";
		if(!$('#benefits_check_condition').is(':checked')) {
			textAlert += " - กรุณาเลือกตรวจสอบแล้วผ่านเกณฑ์เงื่อนไข\n";
		}
		if(!$("#benefits_type_id").val()) {
			textAlert += " - กรุณาเลือกสวัสดิการ\n";
		}

		swal('ไม่สามารถบันทึกข้อมูลได้',textAlert,'warning');
	}
}
	
function get_search_member(){
	$.ajax({
		type: "POST",
		url: base_url+'benefits/get_search_member',
		data: {
			search_text : $("#search_text").val(),
			form_target : 'add'
		},
		success: function(msg) {
			$("#table_data").html(msg);
		}
	});
}

function del_coop_data(id,member_id){	
	swal({
		title: "ท่านต้องการลบข้อมูลใช่หรือไม่",
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
				url: base_url+'/benefits/del_coop_data',
				method: 'POST',
				data: {
					'table': 'coop_benefits_request',
					'id': id,
					'field': 'benefits_request_id'
				},
				success: function(msg){
					if(msg == 1){
					  document.location.href = base_url+'benefits/benefits_request?id='+member_id;
					}else{

					}
				}
			});
		} else {
			
		}
	});		
}


function add_request(){
	$('#myModalRequest').modal('show');
	var user_name = $("#user_name_session").val();
	$("#user_name").html(user_name);
	$("#benefits_request_id").val('');
	$("#benefits_type_id").val('');
	$("#benefits_approved_amount").val('');
	$("#benefits_request_detail").html('');
	$('#benefits_check_condition').prop('checked', true);
}

function view_request(){
	$('#viewRequest').modal('show');
}

function change_type(){
	var benefits_type_id = $("#benefits_type_id").val();
	var member_id = $('#member_id').first().val();
	$.ajax({
		type: "POST",
		url: base_url+'benefits/get_benefits_type',
		data: {
			id : benefits_type_id,
			member_id : member_id
		},
		success: function(msg) {
			response = $.parseJSON(msg);
			if(response){
				$("#benefits_request_detail").html(response.benefits_detail);
				if(response.conditions.conditions_text && response.conditions.conditions_text != "") {
					$("#conditions").html(response.conditions.conditions_text);
					$("#lastest_condition_created").val(response.updatetime);
				} else {
					$("#conditions").html('<label class="g24-col-sm-24 control-label text-left"> - </label>');
					$("#lastest_condition_created").val("");
				}
				$(".benefits_approved_req").val(response.benefit_total_text);
			}else{
				$("#benefits_request_detail").html('');
				$("#conditions").html('<label class="g24-col-sm-24 control-label text-left"> - </label>');
				$("#lastest_condition_created").val("");
			}
		}
	});
	
}

function change_type_view(){
	var benefits_type_id = $("#benefits_type_id_view").val();
	$.ajax({
		type: "POST",
		url: base_url+'benefits/get_benefits_type',
		data: {
			id : benefits_type_id
		},
		success: function(msg) {
			response = $.parseJSON(msg);
			if(response){
				$("#benefits_request_detail_view").html(response.benefits_detail);
				$("#start_date_view").val(response.start_date);
			}else{
				$("#benefits_request_detail_view").html('');
				$("#start_date_view").val('');
			}
		}
	});
	
}

function close_modal(id){
	$('#'+id).modal('hide');
	$("#cremation_request_detail_view").html('');
	$("#start_date_view").val('');
	$("#cremation_type_id_view").val('');
}

function show_file(){
	 $('#show_file_attach').modal('show');
}

function edit_request(benefits_request_id,member_id){
	$('#btn_show_file').hide();
	$.ajax({
		type: "POST",
		url: base_url+'benefits/get_benefits_request',
		data: {
			id : benefits_request_id
		},
		success: function(msg) {
			response = $.parseJSON(msg);
			$("#benefits_request_id").val(response.benefits_request_id);
			$("#benefits_type_id").val(response.benefits_type_id);
			$("#benefits_approved_amount").val(response.benefits_approved_amount);
			$("#benefits_request_detail").html(response.benefits_detail);
			$("#user_name").html(response.user_name);
			$("#req_note").html(response.req_note);
			
			if(response.benefits_check_condition == '1'){
				$('#benefits_check_condition').prop('checked', true);
			}else{
				$('#benefits_check_condition').prop('checked', false);
			}	
			
			var txt_file_attach = '<table width="100%">';
			var i=1;
			for(var key in response.coop_file_attach){
				txt_file_attach += '<tr class="file_row" id="file_'+response.coop_file_attach[key].id+'">\n';
				txt_file_attach += '<td><a href="'+base_url+'/assets/uploads/benefits_request/'+response.coop_file_attach[key].file_name+'" target="_blank">'+response.coop_file_attach[key].file_old_name+'</a></td>\n';
				txt_file_attach += '<td style="color:red;font-size: 20px;cursor:pointer;" align="center" width="10%"><span class="icon icon-ban" onclick="del_file(\''+response.coop_file_attach[key].id+'\')"></span></td>\n';
				txt_file_attach += '</tr>\n';
				i++;
			}
			txt_file_attach += '</table>';
			$('#show_file_space').html(txt_file_attach);
			if(i>1){
				$('#btn_show_file').show();
			}
			if(response.conditions.conditions_text) {
				$("#conditions").html(response.conditions.conditions_text);
				$("#lastest_condition_created").val(response.updatetime);
			} else {
				$("#conditions").html('-');
				$("#lastest_condition_created").val("");
			}
		}
	});
	$('#myModalRequest').modal('show');
}

function del_file(id){
	swal({
        title: "ท่านต้องการลบไฟล์ใช่หรือไม่?",
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
			$.post( base_url+"/benefits/ajax_delete_file_attach", 
			{	
				id: id
			}
			, function(result){
				if(result=='success'){
					$('#file_'+id).remove();
					
					var i=0;
					$('.file_row').each(function(index){
						i++;
					});
					
					if(i<=0){
						$('#show_file_attach').modal('hide');
						$('#btn_show_file').hide();
					}
				}else{
					swal('ไม่สามารถลบไฟล์ได้');
				}
			});
			
		}else{
			
		}
	});
}

function transfer_benefits(id, action){
	$('#show_transfer').modal('show');
	$(".id-card-div").hide();
	$("#choice-div").hide();

	$.ajax({
		type: "POST",
		url: base_url+'benefits/get_benefits_transfer',
		data: {
			id : id
		},
		success: function(msg) {
			response = $.parseJSON(msg);
			$(".benefits_request_id").val(id);
			$(".member_id").val(response.member_id);
			$(".member_name").val(response.firstname_th+' '+response.lastname_th);
			$(".admin_request").val(response.admin_request);
			$(".benefits_no").val(response.benefits_no);
			$(".benefits_type_name").val(response.benefits_type_name);
			$(".benefits_approved_amount").val(response.benefits_approved_amount);
			$(".admin_transfer").val(response.admin_transfer);
			$(".mobile").val(response.mobile);			
			$("#createdatetime").val(response.createdatetime);
			$("#date_transfer_picker").val(response.date_transfer);
			$("#time_transfer").val(response.time_transfer);
			$("#bank_choose_1").prop("checked", false);
			$("#bank_choose_2").prop("checked", false);
			$("#bank_choose_"+response.bank_type).attr('checked','checked');
			$("#bank_id_show").val(response.bank_id);
			$("#dividend_bank_id").val(response.bank_id);
			change_bank(response.bank_branch_id);
			$("#branch_id_show").val(response.bank_branch_id);
			$("#bank_account_no").val(response.bank_account_no);
			$('#file_show').hide();
			if(response.file_name != '' && response.file_name != null){
				$("#file_transfer").attr('src',base_url+'assets/uploads/benefits_transfer/'+response.file_name);
				$('#file_show').show()
			}
			
			list_account();	
			change_bank_type();
			$("#action").val(action);
			if(action == 'view'){
				$('#bt_save').hide();
				$('#account_id').attr("disabled", true);
				$('#dividend_bank_id').attr("disabled", true);
				$('#dividend_bank_branch_id').attr("disabled", true);
				$('#bank_account_no').attr("disabled", true);
				$('#date_transfer_picker').attr("disabled", true);
				$('#time_transfer').attr("disabled", true);
				$('#file_name').hide();
				$('input[name="bank_type"]').attr('disabled', true);
			}else{
				$('#bt_save').show();
				$('#account_id').attr("disabled", false);
				$('#dividend_bank_id').attr("disabled", false);
				$('#dividend_bank_branch_id').attr("disabled", false);
				$('#bank_account_no').attr("disabled", false);
				$('#date_transfer_picker').attr("disabled", false);
				$('#time_transfer').attr("disabled", false);
				$('#file_name').show();
				$('input[name="bank_type"]').attr('disabled', false);
			}

			if(response.request_detail_card_id) {
				$("#id_card").val(response.request_detail_card_id);
				$(".id-card-div").show();
			} else {
				$("#id_card").val("");
			}

			if(response.selected_choice_title) {
				$("#choice-div").show();
				$("#selected_choice_title").html(response.selected_choice_title);
				$("#selected_choice_text").val(response.selected_choice_text);
			} else {
				$("#selected_choice_title").html("");
				$("#selected_choice_text").val("");
			}
		}
	});	
}

function change_bank_type(){
	if($('#bank_choose_1').is(':checked')){
		$('#bank_type_1').show();
		$('#bank_type_2').hide();
	}else if($('#bank_choose_2').is(':checked')){
		$('#bank_type_1').hide();
		$('#bank_type_2').show();
	}
}

function change_bank(bank_branch_id = ''){
	var bank_id = $('#dividend_bank_id').val();
	$('#bank_id_show').val(bank_id);
	$('#branch_id_show').val('');
	$.ajax({
		method: 'POST',
		url: base_url+'manage_member_share/get_bank_branch_list',
		data: {
			bank_id : bank_id
		},
		success: function(msg){
			$('#bank_branch').html(msg);
			if(bank_branch_id != ''){
				$("#dividend_bank_branch_id").val(bank_branch_id);
				if($("#action").val() == 'view'){
					$('#dividend_bank_branch_id').attr("disabled", true);
				}else{
					$('#dividend_bank_branch_id').attr("disabled", false);
				}
			}
		}
	});
}	

function list_account(){
	var member_id = $(".member_id").val();
	var benefits_request_id = $(".benefits_request_id").val();
	$.ajax({
		method: 'POST',
		url: base_url+'benefits/get_account_list',
		data: {
			member_id : member_id,
			benefits_request_id : benefits_request_id
		},
		success: function(msg){
			$('#account_id').html(msg);
		}
	});	
}
	
function alert_note_remark(){
	// var note = $("#note_remark").val();
	// if($.trim(note)!=''){
	// 	swal('หมายเหตุ', note);
	// }
}

function removeCommas(str) {
	return(str.replace(/,/g,''));
}