$('#search_member_loan').keyup(function(){
   var txt = $(this).val();
   if(txt != ''){
		$.ajax({
			 url:base_url+"/ajax/search_member_jquery",
			 method:"post",
			 data:{search:txt, member_id_not_allow: $('#member_id').val()},
			 dataType:"text",
			 success:function(data)
			 {
			 //console.log(data);
			  $('#result_member_search').html(data);
			 }
		});
   }else{
	   
   }
});
function addCommas(x){
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function open_modal(id,loan_atm_id=''){
	$('#'+id).modal('show');
	if(id == 'loan_contract_modal'){
		$('.loan_atm_id').val(loan_atm_id);	
		$.post( base_url+"/loan_atm/ajax_get_loan_atm_prev_deduct",
			{	
				loan_atm_id: loan_atm_id
			}
			, function(result){
				if(result != 'not_found'){
					var obj = JSON.parse(result);
					var interest = 0;
					//console.log(obj);	
					if(obj.coop_loan_atm != null){	
						$('#loan_contract_modal input[name=loan_atm_id]').val(obj.coop_loan_atm.loan_atm_id);
						$('#petition_number').val(obj.coop_loan_atm.petition_number);
						$('#member_id').val(obj.coop_loan_atm.member_id);
						$('#total_amount').val(obj.coop_loan_atm.total_amount);
						
						
						$('.prev_loan_checkbox').attr('checked',false);
						$('.prev_loan_amount').val('');

						for(var key in obj.coop_loan_atm_prev_deduct){

							$('.prev_loan_checkbox').each(function(){

								if(obj.coop_loan_atm_prev_deduct[key].ref_id == $(this).attr('ref_id')
									&& obj.coop_loan_atm_prev_deduct[key].data_type == $(this).attr('data_type')
								){

									var index = $(this).attr('attr_index');
									$(this).attr('checked',true);
									if(obj.coop_loan_atm_prev_deduct[key].pay_type == 'principal'){
										$('#prev_loan_pay_type_1_'+index).attr('checked',true);
										$('#prev_loan_pay_type_2_'+index).attr('checked',false);
									}else if(obj.coop_loan_atm_prev_deduct[key].pay_type == 'all'){
										$('#prev_loan_pay_type_1_'+index).attr('checked',false);
										$('#prev_loan_pay_type_2_'+index).attr('checked',true);
										interest += parseFloat(obj.coop_loan_atm_prev_deduct[key].interest_amount);
									}
									$('#prev_loan_amount_'+index).val(addCommas(obj.coop_loan_atm_prev_deduct[key].pay_amount));
								}
							});
						}
						console.log('log interest:', interest);
						$("#form_contract input[name='interest_amount']").val(interest);
						change_prev_loan_pay_type('loan_contract_modal');
					}
				}
				
			});				
	}
}

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

function check_submit(){
	var text_alert = '';
	var min_loan_amount = removeCommas($('#min_loan_amount').val());
	var loan_amount = removeCommas($('#loan_amount').val());
	var total_amount_balance = removeCommas($('#total_amount_balance').val());
		
	if($('#loan_amount').val() == ''){
		text_alert += '- ??????????????????????????????????????????????????????\n';
	}
	else if(parseInt(loan_amount) > parseInt(total_amount_balance)){
		text_alert += '- ?????????????????????????????????????????????????????????????????????????????????????????????????????????????????? '+addCommas(total_amount_balance)+' ?????????\n';
	}
	if($('#pay_type_3').is(':checked')){
		if($('#account_id').val()==''){
			text_alert += '- ?????????????????????????????????????????????\n';
		}
	}
	if($('#pay_type_1').is(':checked')){
		if($('#bank_id').val()==''){
			text_alert += '- ????????????????????????????????????????????????\n';
		}
		if($('#bank_account_id').val()==''){
			text_alert += '- ???????????????????????????????????????????????????\n';
		}
	}
	/*
	else if(parseInt(min_loan_amount) > parseInt(loan_amount)){
		text_alert += '- ?????????????????????????????????????????????????????????????????????????????????????????????????????????????????? '+addCommas(min_loan_amount)+' ?????????\n';
	}*/
	
	if(text_alert != ''){
		swal('??????????????????????????????????????????',text_alert,'warning');
	}else{
		loan_amount = Math.ceil(loan_amount/100)*100;
		swal({
			title: "???????????????????????????????????????????????????????????????????????????????????????????????? "+addCommas(loan_amount)+" ?????????",
			text: "",
			type: "warning",
			showCancelButton: true,
			//confirmButtonColor: '#DD6B55',
			confirmButtonColor: '#d50000',
			confirmButtonText: '??????????????????',
			cancelButtonText: "??????????????????",
			closeOnConfirm: true,
			closeOnCancel: true
		},
		function(isConfirm) {
			if (isConfirm) {
				$('#loan_amount').val(loan_amount);
				$('#form_normal_loan').submit();				
			}else{
				
			}
		});
	}
}
function check_submit_contract(){ 
	var text_alert = '';	
	
	if($('#total_amount').val() == ''){
		text_alert += '- ????????????????????????????????????????????????????????????\n';
	// }else if(parseInt(removeCommas($('#total_amount').val())) > parseInt(removeCommas($('#max_loan_amount').val()))){
	// 	//text_alert += '- ???????????????????????????????????????????????????????????? '+$('#max_loan_amount').val()+' ?????????\n';
	// 	text_alert += '- ?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????\n';
	}else if(parseInt(removeCommas($('#total_amount').val())) < parseInt($('#total_amount_prev_deduct').val())){
		text_alert += '- ???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????\n';
	}
	if(text_alert != ''){
		swal('??????????????????????????????????????????',text_alert,'warning');
	}else{
		$('#form_contract').submit();
	}
}
function removeCommas(str) {
	if (typeof str === "undefined" || str === "" || str === null) return 0;
	if (typeof str === "number") return str;
	return Math.round(parseFloat(str.split(',').join('')) * 100)/100;
}

function del_file(id){
	swal({
        title: "??????????????????????????????????????????????????????????????????????????????????",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: '??????????????????',
        cancelButtonText: "??????????????????",
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {
			$.post( base_url+"/loan_atm/ajax_delete_loan_file_attach", 
			{	
				id: id
			}
			, function(result){
				if(result=='success'){
					$('#file_'+id).remove();
					
					var i=0;
					$('.file_row').each(function(index){
						i++;
						//console.log(i);
					});
					
					if(i<=0){
						$('#show_file_attach').modal('hide');
						$('#btn_show_file').hide();
					}
				}else{
					swal('??????????????????????????????????????????????????????');
				}
			});
			
		}else{
			
		}
	});
}
function close_modal(id){
	$('#'+id).modal('hide');
}
function cancel_contract(principal_amount){
	if(principal_amount=='0'){
		swal({
        title: "?????????????????????????????????????????????????????????????????????????????????????????????????",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: '??????????????????',
        cancelButtonText: "??????????????????",
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {
			$('#form_cancel_contract').submit();
		}else{
			
		}
	});
	}else{
		swal('','???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????','warning');
	}
}
function check_submit_change_amount(){ 
	var text_alert = '';
	if($('#total_amount_c').val() == ''){
		text_alert += '- ????????????????????????????????????????????????????????????\n';
	}else if(parseInt(removeCommas($('#total_amount_c').val())) > parseInt(removeCommas($('#max_loan_amount_c').val()))){
		text_alert += '- ???????????????????????????????????????????????????????????? '+$('#max_loan_amount_c').val()+' ?????????\n';
	}
	if(parseInt(removeCommas($('#total_amount_c').val())) < parseInt(removeCommas($('#deduct_amount').val()))){
		text_alert += '- ??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????\n';
	}
	if(text_alert != ''){
		swal('??????????????????????????????????????????',text_alert,'warning');
	}else{
		$('#form_change_amount').submit();
	}
}

function cal_prev_loan(){
	var deduct_pay_prev_loan = 0;
	$('.prev_loan_amount').each(function(){
		var index = $(this).attr('attr_index');
		if($('#prev_loan_checkbox_'+index).is(':checked') && $(this).val()!=''){
			deduct_pay_prev_loan += parseFloat(removeCommas($(this).val()));
		}
	});
	deduct_pay_prev_loan = addCommas(deduct_pay_prev_loan);
	$('#deduct_pay_prev_loan').val(deduct_pay_prev_loan);
	cal_estimate_money();
}
$( document ).ready(function() {
	$(".modal").on("hidden.bs.modal", function(){
		var total_amount_val = $('#total_amount_val').val();
		var loan_reason_val = $('#loan_reason_val').val();
		$('#total_amount').val(total_amount_val);
		$('#loan_reason').val(loan_reason_val);
	});
});

function loan_atm_lock(loan_atm_id,member_id){
	swal({
		title: "??????????????????????????????????????????????????????????????????????????????????????????????",
		text: "??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: '??????????????????',
		cancelButtonText: "??????????????????",
		closeOnConfirm: true,
		closeOnCancel: true
	},
	function(isConfirm) {
		if (isConfirm) {
			document.location.href = base_url+'loan_atm/loan_atm_lock/'+loan_atm_id+'/'+member_id;
		}else{
			
		}
	});
}
function loan_atm_unlock(loan_atm_id,member_id){
	swal({
		title: "???????????????????????????????????????????????????????????????????????????????????????????????????????",
		text: "",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: '??????????????????',
		cancelButtonText: "??????????????????",
		closeOnConfirm: true,
		closeOnCancel: true
	},
	function(isConfirm) {
		if (isConfirm) {
			document.location.href = base_url+'loan_atm/loan_atm_unlock/'+loan_atm_id+'/'+member_id;
		}else{
			
		}
	});
}


function check_modal(id){
	var activate_status = $('#activate_status').val();
	if(activate_status == '1'){
		swal('???????????????????????????????????????????????????????????? ??????????????????????????????????????????????????????????????????','??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????','warning');
	}else{
		open_modal(id);
	}
}
function change_pay_type(){
	$('#account_choose_space').hide();
	$('.coop_account').hide();
	$('.bank_account').hide();
	if($('#pay_type_3').is(':checked')){
		$('#account_choose_space').show();
		$('.coop_account').show();
	}else if($('#pay_type_1').is(':checked')){
		$('#account_choose_space').show();
		$('.bank_account').show();
	}
}

function change_prev_loan_pay_type(loan_modal){
	var total_amount = removeCommas($("#total_amount").val());
	var prev_loan_amount_all = 0;
	var prev_principal = 0;
	var prev_interest = 0;
	var prev_loan_total = 0;
	var deduct_amount = 0;
	var net = 0 ;
	$('#'+loan_modal+' .prev_loan_checkbox').each(function(){
		var index = $(this).attr('attr_index');
		if($(this).is(':checked')){
			var prev_loan_total_c  = '';
			var _principal = 0;
			var _interest = 0;
			if($('#'+loan_modal+' #prev_loan_pay_type_1_'+index).is(':checked')){
				$('#'+loan_modal+' #prev_loan_amount_'+index).val($('#'+loan_modal+' #principal_without_finance_month_'+index).val());
				prev_loan_total_c = _principal =removeCommas($("#"+loan_modal).find('.modal-body #principal_without_finance_month_'+index).val());
				$('#'+loan_modal+' #prev_loan_interest_'+index).val(0)
			}else if($('#'+loan_modal+' #prev_loan_pay_type_2_'+index).is(':checked')){
				$('#'+loan_modal+' #prev_loan_amount_'+index).val($('#'+loan_modal+' #prev_loan_total_'+index).val());
				$('#'+loan_modal+' #prev_loan_interest_'+index).val($('#'+loan_modal+' #prev_interest_'+index).val());
				prev_loan_total_c  = _principal = removeCommas($("#"+loan_modal).find('.modal-body #prev_loan_total_'+index).val());
				prev_loan_total_c += _interest = removeCommas($("#"+loan_modal).find('.modal-body #prev_loan_interest_'+index).val());

			}
			prev_loan_total = parseFloat(removeCommas(prev_loan_total_c));
			prev_principal += _principal;
			prev_interest += _interest;
			prev_loan_amount_all += prev_loan_total;

			if(prev_loan_amount_all > total_amount){
				prev_loan_amount_all -= prev_loan_total;
				$("#"+loan_modal).find('.modal-body #prev_loan_checkbox_'+index).removeAttr('checked');
				swal("","?????????????????????????????????????????????????????????????????????????????? ?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????","warning");
				$('#'+loan_modal+' #prev_loan_checkbox_'+index).prop('checked', false);
				var chk = 1;
				setTimeout(()=> {
					change_deduct(loan_modal);
				}, 500);
			}else if(prev_loan_amount_all == 0 || prev_loan_amount_all == undefined){
				swal("","??????????????????????????????????????????????????????????????????????????????","warning");
				$('#'+loan_modal+' #prev_loan_checkbox_'+index).prop('checked', false);
				setTimeout(()=> {
					change_deduct(loan_modal);
				}, 500);
				net = round(total_amount, 2);
			}else{
				deduct_amount += prev_loan_total;
				 var chk = 2;
				//$('#deduct_amount').val(deduct_amount);
				$("#"+loan_modal).find('.modal-body #net_amount').val(net);
				$("#"+loan_modal).find('.modal-body #deduct_amount').val(deduct_amount);
			}
		}else{
			var chk = 1 ;
			//$('#deduct_amount').val('');
			$("#"+loan_modal).find('.modal-body #deduct_amount').val();
			$('#'+loan_modal+' #prev_loan_amount_'+index).val('');
			$('#'+loan_modal+' #prev_loan_interest_'+index).val('');
			$("#"+loan_modal).find('#net_amount').val();
		}
		if(chk == 1){
			net = round(total_amount , 2);
		}if(chk == 2){
			net = round(total_amount - prev_loan_total, 2);
		}

		$("#"+loan_modal+" #principal_amount").val(prev_principal);
		$("#"+loan_modal+" #interest_amount").val(prev_interest);
		$("#"+loan_modal+" input[name='interest_amount']").val(prev_interest);
		$("#"+loan_modal+" input[name='pay_amount']").val(prev_principal);
		$("#"+loan_modal+" input[name='deduct_amount']").val(prev_loan_total);

		$("#"+loan_modal+" #net_amount").val(net);
		$("#"+loan_modal+" input[name='net_amount']").val(net);
	});


}

function change_deduct(loan_modal) {
	var total_amount = removeCommas($("#total_amount_c").val());
	var prev_loan_amount_all = 0;
	var prev_principal = 0;
	var prev_interest = 0;
	var prev_loan_total = 0;
	var deduct_amount = 0;
	$('#'+loan_modal+' .prev_loan_checkbox').each(function(){
		var index = $(this).attr('attr_index');
		if($(this).is(':checked')){
			var prev_loan_total_c  = '';
			var _principal = 0;
			var _interest = 0;
			if($('#'+loan_modal+' #prev_loan_pay_type_1_'+index).is(':checked')){
				$('#'+loan_modal+' #prev_loan_amount_'+index).val($('#'+loan_modal+' #principal_without_finance_month_'+index).val());
				prev_loan_total_c = _principal =removeCommas($("#"+loan_modal).find('.modal-body #principal_without_finance_month_'+index).val());
				$('#'+loan_modal+' #prev_loan_interest_'+index).val(0)
			}else if($('#'+loan_modal+' #prev_loan_pay_type_2_'+index).is(':checked')){
				$('#'+loan_modal+' #prev_loan_amount_'+index).val($('#'+loan_modal+' #prev_loan_total_'+index).val());
				$('#'+loan_modal+' #prev_loan_interest_'+index).val($('#'+loan_modal+' #prev_interest_'+index).val());
				prev_loan_total_c  = _principal = removeCommas($("#"+loan_modal).find('.modal-body #prev_loan_total_'+index).val());
				prev_loan_total_c += _interest = removeCommas($("#"+loan_modal).find('.modal-body #prev_loan_interest_'+index).val());
			}
			prev_loan_total = parseFloat(removeCommas(prev_loan_total_c));
			prev_principal += _principal;
			prev_interest += _interest;
			prev_loan_amount_all += prev_loan_total;

			if(prev_loan_amount_all > total_amount){
				prev_loan_amount_all -= prev_loan_total;
				$("#"+loan_modal).find('.modal-body #prev_loan_checkbox_'+index).removeAttr('checked');
				swal("","?????????????????????????????????????????????????????????????????????????????? ?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????","warning");
				$('#'+loan_modal+' #prev_loan_checkbox_'+index).prop('checked', false);
				setTimeout(()=> {
					change_deduct(loan_modal);
				}, 500);
			}else if(prev_loan_amount_all == 0 || prev_loan_amount_all == undefined){
				swal("","??????????????????????????????????????????????????????????????????????????????","warning");
				$('#'+loan_modal+' #prev_loan_checkbox_'+index).prop('checked', false);
				setTimeout(()=> {
					change_deduct(loan_modal);
				}, 500);
			}else{
				deduct_amount += prev_loan_total;
				//$('#deduct_amount').val(deduct_amount);
				$("#"+loan_modal).find('.modal-body #deduct_amount').val(deduct_amount);
			}
		}else{
			//$('#deduct_amount').val('');
			$("#"+loan_modal).find('.modal-body #deduct_amount').val();
			$('#'+loan_modal+' #prev_loan_amount_'+index).val('');
			$('#'+loan_modal+' #prev_loan_interest_'+index).val('');
		}
		$("#"+loan_modal+" #principal_amount").val(prev_principal);
		$("#"+loan_modal+" #interest_amount").val(prev_interest);
		$("#"+loan_modal+" input[name='interest_amount']").val(prev_interest);
		$("#"+loan_modal+" input[name='pay_amount']").val(prev_principal);
		$("#"+loan_modal+" #principal_amount_show").val(prev_principal);
		$("#"+loan_modal+" #interest_amount_show").val(prev_interest);
		$("#"+loan_modal+" input[name='deduct_amount']").val(prev_loan_total);
	});
}

$(document).ready(function(){
	$("#createdatetime").datepicker({
		prevText : "????????????????????????",
		nextText: "???????????????",
		currentText: "Today",
		changeMonth: true,
		changeYear: true,
		isBuddhist: true,
		monthNamesShort: ['???.???.', '???.???.', '??????.???.', '??????.???.', '???.???.', '??????.???.', '???.???.', '???.???.', '???.???.', '???.???.', '???.???.', '???.???.'],
		dayNamesMin: ['??????', '???', '???', '???', '??????', '???', '???'],
		constrainInput: true,
		dateFormat: "dd/mm/yy",
		yearRange: "c-50:c+10",
		autoclose: true,
	});
});

/*$(document).on('change', '#createdatetime', function(){
	let data = {};
	data.create = $("#createdatetime_c").val();
	data.loan_atm_id = $("#loan_atm_id").val();
	$.post(base_url+"loan_atm/get_prev_loan_atm", data, function(res){

		const principal = parseFloat(res.loan_amount_balance);
		const interest = parseFloat(res.interest_arrear_bal);
		const total = principal+interest;

		$("#form_change_amount #principal_amount").val(principal);
		$("#form_change_amount #interest_amount").val(interest);
		$("#form_change_amount input[name='pay_amount']").val(total);
		$("#principal_amount_show").val(principal);
		$("#interest_amount_show").val(interest);
		$("#form_change_amount input[name='deduct_amount']").val(total);
	});
});*/

$(document).on('change', '#createdatetime, #createdatetime_c', function(){
	let deduct = 0;
	let selector_modal = '';
	if($(this).attr('id') === "createdatetime") {
		selector_modal = '#loan_contract_modal'
	}else{
		selector_modal = '#loan_change_amount_modal';
	}
	const previousDeduct = $(selector_modal+" .prev_loan_checkbox");
	new Promise((resolve, reject) => {
		let data = {};
		data.member_id = $("#member_id").val();

		if($(this).attr('id') === "createdatetime"){
			data.createdatetime = $('#createdatetime').val();
		}else{
			data.createdatetime = $('#createdatetime_c').val();
		}
		$.post(base_url + '/loan_contract/get_check_prev_loan', data, function (res) {
			if (res.status === 200) {
				resolve(res.data);
			} else {
				reject(`something wrong`);
			}
		});
	}).then((data) => {
		previousDeduct.each(function (idx) {
			let _ref_id = $(this).val();
			let num = 0;
			if ($(this).is(':checked') === true) {
				data.forEach(function (item, index) {
					if (_ref_id === item.ref_id) {
						let prevLoan = removeCommas(item.prev_loan_total);
						let interest = removeCommas(item.interest);
						let result = parseFloat(prevLoan + interest);
						let without = removeCommas(item.principal_without_finance_month);

						$(selector_modal+" #prev_loan_total_" + idx).val(prevLoan);
						$(selector_modal+" #prev_interest_" + idx).val(interest);
						$(selector_modal+" input[name='prev_loan[" + idx + "][interest]'").val(interest);
						$(selector_modal+" input[name='prev_loan[" + idx + "][principal]'").val(prevLoan);
						$(selector_modal+" input[name='prev_loan[" + idx + "][amount]'").val(result);
						$(selector_modal+" #principal_without_finance_month_" + idx).val(without);

						deduct += result;
						num++;
					}
				});
			}
		});
		return;
	}).then(() => {
		if($(this).attr('id') === "createdatetime") {
			change_prev_loan_pay_type('loan_contract_modal');
		}else {
			change_deduct("loan_change_amount_modal");
		}
	}).catch((err) => {
		console.log(" error: " + err);
	});
});

$(document).on("change blur", "#total_amount_c, #max_period_c", () => {
	const amount = removeCommas($("#total_amount_c").val());
	const period = removeCommas($("#max_period_c").val());
	$("#period_payment_amount_c").val(addCommas(calculatePayment(amount, period)));
});

$(document).on("change blur", "#total_amount, #max_period", () => {
	const amount = removeCommas($("#loan_contract_modal #total_amount").val());
	const period = removeCommas($("#loan_contract_modal #max_period").val());
	$("#period_payment_amount").val(addCommas(calculatePayment(amount, period)));
});

const calculatePayment = (amount, period) => {
	return Math.ceil( (amount/period)/100)*100;
};
