$(function() {

	"use strict";
	Number.prototype.format = function(n, x) {
			var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
			return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
	};

	$(document).ready((e) => {
		$("#date_return").datepicker({
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
	});

  $('body').on('click', '#check_all', function() {
    if( $('#check_all').attr('checked') == 'checked' )
      $('.check_return').attr('checked', 'checked');
    else $('.check_return').removeAttr('checked');
  });

  $('body').on('click', '#btn-save-return', function() {
  	dialogReturn();
  });

  $(document).on("click", "#submit_return", () => {
  	dateInputReturn();
  });

  if(('#table_process_return').length > 0) get_data();
  $('body').on('click', '#btn_get_data', function() {
    get_data();
	});
	
	$('body').on('click', '#btn-previous', function() {
		var page = $('#table_process_return').data('page') - 1;
		var limit = $("#limit").val();
		console.log(page);
		if(page<1){
			return;
		}
		var url = window.location.href;
		if(url.search("&page") > -1){
			url = url.substring(0 , url.search("&page") );
		}
		window.location = url+"&page="+page+"&limit="+limit;
	});

	$('body').on('click', '#btn-next', function() {
		var page = $('#table_process_return').data('page') + 1;
		var url = window.location.href;
		var limit = $("#limit").val();
		if(url.search("&page") > -1){
			url = url.substring(0 , url.search("&page") );
		}
		window.location = url+"&page="+page+"&limit="+limit;
	});


});

const btnConfirmSave = () => {
	dialogReturn();
};

const dialogReturn = () => {
	$("#modal_return_date").modal("show");
};

const dateInputReturn = () => {
 new Promise(resolve => {
	 $("#table_process_return").data( "fixed-date", $("#date_return").val());
	 $("#table_process_return").data( "pay-type", $("input[name='pay_type']:checked").val());
	 $("#modal_return_date").modal("hide");
 	setTimeout(() => {
		return resolve();
	}, 500);})
	 .then(processReturn);
};

const processReturn = () => {

	if( $('.check_return:checked').length == 0 ) {
		swal('กรุณาเลือกรายการ','','warning');
	} else {
		$.blockUI({
			message: '<h4 class="text-center"><i class="fa fa-refresh fa-spin"></i>&nbsp;กรุณารอสักครู่</h4>',
			css: {
				border: 'none',
				padding: '15px',
				backgroundColor: '#000',
				'-webkit-border-radius': '10px',
				'-moz-border-radius': '10px',
				opacity: .5,
				color: '#fff'
			},
			baseZ: 2000,
			bindEvents: false
		});

		var search = location.search.substring(1);
		var queryStr = JSON.parse('{"' + decodeURI(search).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');

		if(queryStr.type === '4'){
			$("input[name^=surcharge]").prop("disabled", "disabled");
			$("input[name^=return_principal]").prop("disabled", "disabled");
			$("input[name^=receipt_id]").prop("disabled", "disabled");
		}

		var tmp = [];
		var data_post = $('#frm-return-interest').serializeArray();
		var serialized = $('input:checkbox').map(function() {
			if(this.name.search(/check_return/i) >= 0 && this.checked == true){

				var key = this.name.substring(13, this.name.search("]") ) ;
				if(key != undefined ){
				tmp.push({ name: this.name, value: this.value });
				}

				var member_id = $("input[name='member_id["+key+"]']").val();
				if(member_id != undefined ){
				tmp.push({ name: "member_id["+key+"]", value: member_id.substring(10, member_id.search("]") ) });
				}

				var deduct_code = $("input[name='deduct_code["+key+"]']").val();
				if(deduct_code != undefined ){
				tmp.push({ name: "deduct_code["+key+"]", value: deduct_code });
				}

				var loan_id = $("input[name='loan_id["+key+"]']").val();
				if(loan_id != undefined ){
				tmp.push({ name: "loan_id["+key+"]", value: loan_id.substring(8, loan_id.search("]") ) });
				}
				
				var loan_atm_id = $("input[name='loan_atm_id["+key+"]']").val();
				if(loan_atm_id != undefined ){
				tmp.push({ name: "loan_atm_id["+key+"]", value: loan_atm_id });
				}
				
				var receipt_id = $("input[name='receipt_id["+key+"]']").val();
				if(receipt_id != undefined ){
				tmp.push({ name: "receipt_id["+key+"]", value: receipt_id.substring(11, receipt_id.search("]") ) });
				}
				
				var return_principal = $("input[name='return_principal["+key+"]']").val();
				if(loan_id != undefined ){
				tmp.push({ name: "return_principal["+key+"]", value: return_principal.substring(17, return_principal.search("]") ) });
				}
		
				var return_interest = $("input[name='return_interest["+key+"]']").val();
				if(return_interest != undefined ){
				tmp.push({ name: "return_interest["+key+"]", value: return_interest.substring(16, return_interest.search("]") ) });
				}

				var surcharge = $("input[name='surcharge["+key+"]']").val();
				if(surcharge != undefined ){
				tmp.push({ name: "surcharge["+key+"]", value: surcharge.substring(10, surcharge.search("]") ) });
				}
				
				var account_id = $("input[name='account_id["+key+"]']").val();
				if(account_id != undefined ){
				tmp.push({ name: "account_id["+key+"]", value: account_id.substring(10, account_id.search("]") ) });
				}

				var transaction_balance = $("input[name='transaction_balance["+key+"]']").val();
				if(transaction_balance != undefined ){
				tmp.push({ name: "transaction_balance["+key+"]", value: transaction_balance.substring(17, transaction_balance.search("]") ) });
				}principal_payment

				var principal_payment = $("input[name='principal_payment["+key+"]']").val();
				if(principal_payment != undefined ){
				tmp.push({ name: "principal_payment["+key+"]", value: principal_payment.substring(17, principal_payment.search("]") ) });
				}
				
			}
			return;
		});

		data_post = tmp;
		//console.log($('#table_process_return').data('ds'));
		//console.log($('#table_process_return').data('de'));
		data_post.push({ name: "ret_type", value: $('#table_process_return').data('type') });
		data_post.push({ name: "date_s", value: $('#table_process_return').data('ds') });
		data_post.push({ name: "date_e", value: $('#table_process_return').data('de') });
		data_post.push({ name: "fix_date", value: $('#table_process_return').data('fixed-date') });
		data_post.push({ name: "pay_type", value: $('#table_process_return').data('pay-type') });
		data_post.push({ name: "_t", value: Math.random() });
		data_post = jQuery.param(data_post)

		$.ajax({
			url: base_url+"ajax/process_return_edit_exec"
			, type: "POST"
			, dataType: "json"
			, data: data_post
			, async: true
			, success: function($json) {
				$.unblockUI();
				console.log($json);
				get_data();
			}
			, error: function(jqXHR, error_text, error_thrown) {
				console.log(jqXHR.status + ' : ' + error_text + ' : ' + error_thrown)
			}
		});
	}
};

const get_data = () => {
	$('#table_process_return tbody').empty().append('<tr><td colspan="8"><h4 class="text-center"><i class="fa fa-refresh fa-spin"></i>&nbsp;กรุณารอสักครู่</h4></td></tr>');
	$.ajax({
		url: base_url+"ajax/process_return_edit",
		method:"post",
		data: {
			ret_type : $('#table_process_return').data('type'),
			date_s : $('#table_process_return').data('ds'),
			date_e : $('#table_process_return').data('de'),
			page : $('#table_process_return').data('page'),
			limit : $('#table_process_return').data('limit'),
		},
		dataType:"json",
		success:function($json) {
			// console.log($('#table_process_return').data('type'));
			// console.log($json);
			$('#table_process_return tbody').empty();
			var $row_no = 1;
			$json.data.forEach(function($row) {
				//console.log($row);
				if( $row.return_principal === null || typeof $row.return_principal === 'undefined' ) $row.return_principal = 0;
				if( $row.surcharge === null || typeof $row.surcharge === 'undefined' ) $row.surcharge = 0;

				if($('#table_process_return').data('type') == 5){
					var text_detail = '';
					var text_return_principal = '';
					var text_return_interest = '';
					$row.data_detail.forEach(function($row_detail) {
						console.log($row_detail);
						text_return_principal = ($row_detail.return_principal > 0)?Number($row_detail.return_principal).format(2, 3)+' บาท ':'';
						text_return_interest = ($row_detail.return_interest > 0)?Number($row_detail.return_interest).format(2, 3)+' บาท ':'';
						text_detail += $row_detail.transaction_text+'  ';
						text_detail += text_return_principal;
						text_detail += text_return_interest;
						text_detail += '<br>';
					});

					$('#table_process_return tbody').append('\
					<tr>\
						<td>\
						<label class="custom-control custom-control-primary custom-checkbox">\
							<input type="checkbox" name="check_return[' + $row_no + ']" value="1" class="custom-control-input check_return" />\
							<span class="custom-control-indicator"></span>\
							<span class="custom-control-label"></span>\
						</label>\
						<input type="hidden" name="member_id[' + $row_no + ']" value="' + $row.member_id + '" />\
						<input type="hidden" name="receipt_id[' + $row_no + ']" value="' + $row.receipt_id + '" />\
						<input type="hidden" name="surcharge[' + $row_no + ']" value="' + $row.surcharge + '" />\
						</td>\
						<td>' + Number($row_no++).format(0, 3) + '</td>\
						<td>' + $row.member_id + '</td>\
						<td>' + $row.member_name + '</td>\
						<td>' + $row.receipt_id + '</td>\
						<td colspan="7" style="text-align: left;">'+text_detail+'</td>\
					</tr>\
					');
				}else if($('#table_process_return').data('type') == 7){
					$('#table_process_return tbody').append('\
					<tr>\
						<td>\
						<label class="custom-control custom-control-primary custom-checkbox">\
							<input type="checkbox" name="check_return[' + $row_no + ']" value="1" class="custom-control-input check_return" />\
							<span class="custom-control-indicator"></span>\
							<span class="custom-control-label"></span>\
						</label>\
						<input type="hidden" name="member_id[' + $row_no + ']" value="' + $row.member_id + '" />\
						<input type="hidden" name="receipt_id[' + $row_no + ']" value="' + $row.receipt_id + '" />\
						<input type="hidden" name="account_id[' + $row_no + ']" value="' + $row.account_id + '" />\
						<input type="hidden" name="transaction_balance[' + $row_no + ']" value="' + $row.transaction_balance + '" />\
						<input type="hidden" name="principal_payment[' + $row_no + ']" value="' + $row.principal_payment + '" />\
						</td>\
						<td>' + Number($row_no++).format(0, 3) + '</td>\
						<td>' + $row.member_id + '</td>\
						<td>' + $row.account_name + '</td>\
						<td>' + $row.account_id + '</td>\
						<td>' + $row.receipt_id + '</td>\
						<td  style="text-align: center;">' + Number($row.principal_payment).format(2, 3) + '</td>\
						<td>' + $row.is_return + '</td>\
					</tr>\
					');
				}else{
					$('#table_process_return tbody').append('\
					<tr>\
						<td>\
						<label class="custom-control custom-control-primary custom-checkbox">\
							<input type="checkbox" name="check_return[' + $row_no + ']" value="1" class="custom-control-input check_return" />\
							<span class="custom-control-indicator"></span>\
							<span class="custom-control-label"></span>\
						</label>\
						<input type="hidden" name="member_id[' + $row_no + ']" value="' + $row.member_id + '" />\
						<input type="hidden" name="deduct_code[' + $row_no + ']" value="' + $row.deduct_code + '" />\
						<input type="hidden" name="loan_id[' + $row_no + ']" value="' + $row.loan_id + '" />\
						<input type="hidden" name="loan_atm_id[' + $row_no + ']" value="' + $row.loan_atm_id + '" />\
						<input type="hidden" name="receipt_id[' + $row_no + ']" value="' + $row.receipt_id + '" />\
						<input type="hidden" name="return_principal[' + $row_no + ']" value="' + $row.return_principal + '" />\
						<input type="hidden" name="return_interest[' + $row_no + ']" value="' + $row.return_interest + '" />\
						<input type="hidden" name="surcharge[' + $row_no + ']" value="' + $row.surcharge + '" />\
						</td>\
						<td>' + Number($row_no++).format(0, 3) + '</td>\
						<td>' + $row.member_id + '</td>\
						<td>' + $row.member_name + '</td>\
						<td>' + $row.contract_number + '</td>\
						<td class="text-center">' + Number($row.interest_rate).format(2, 3) + '</td>\
						<td>' + $row.receipt_id + '</td>\
						<td>' + Number($row.return_principal).format(2, 3) + '</td>\
						<td>' + Number($row.return_interest).format(2, 3) + '</td>\
						<td>' + Number($row.surcharge).format(2, 3) + '</td>\
						<td>' + $row.return_status + '</td>\
					</tr>\
					');
				}
			});
		},
		error: function(xhr){
			console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
		}
	});
};
