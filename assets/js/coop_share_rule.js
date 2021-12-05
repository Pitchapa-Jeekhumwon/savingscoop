var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
	$("#various1").fancybox({
	  'titlePosition'		: 'inside',
	  'transitionIn'		: 'none',
	  'transitionOut'		: 'none',
	});
	
	$("#filter").change(function() {
		document.location.href = base_url+'setting_share_data/coop_share_rule?filter='+ $(this).val();
	});
	
	change_status_salary();

});
	
function check_form(){
	var text_alert = '';
	if($.trim($('#mem_type_id').val())== ''){
		text_alert += ' - ประเภทสมาชิก\n';
	}
	
	if($('#status_salary').is(':checked')){	
		if($.trim($('#percent_salary').val())== ''){
			text_alert += ' - จำนวน % ของเงินเดือน\n';
		}
		if($.trim($('#share_monthly_min').val())== ''){
			text_alert += ' - หุ้นรายเดือนขั้นต่ำ\n';
		}
	}else{		
		if($.trim($('#salary_rule').val())== ''){
			text_alert += ' - เงินเดือนมากกว่า\n';
		}
		if($.trim($('#share_salary').val())== ''){
			text_alert += ' - หุ้นรายเดือน\n';
		}
	}	
	
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		//เช็คประเภทสมาชิก กับ  การเลือกแบบคิดตาม % เงินเดือน 
		var status_salary = '';
		if($('#status_salary').is(':checked')){	
			status_salary = $('#status_salary').val();
		}

		var mem_type_id = $('#mem_type_id').val();
		var share_rule_id = $('#share_rule_id').val();
		//alert('share_rule_id='+share_rule_id);
		$.ajax({
			url: base_url+'/setting_share_data/check_choose_percent_salary',
			method: 'POST',
			data: {
				'status_salary': status_salary,
				'mem_type_id': mem_type_id,
				'share_rule_id': share_rule_id
			},
			success: function(msg){
				console.log(msg);
				if(msg == 1){
					$('#form_save').submit();
				}else{
					swal('แจ้งเตือน','ไม่สามารถตั้งค่าประเภทสมาชิกนี้ได้','warning');
				}
			}
		});
		
	}
}	

function check_form_change(){
	var text_alert = '';
	if($.trim($('#share_cost').val())== ''){
		text_alert += ' - มูลค่าหุ้นใหม่\n';
	}
	
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form_change').submit();
	}
}

function del_coop_share_data(id){	
	swal({
		title: "ท่านต้องการลบข้อมูลนี้ใช่หรือไม่ ! ",
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
				url: base_url+'/setting_share_data/del_coop_share_data',
				method: 'POST',
				data: {
					'table': 'coop_share_rule',
					'id': id,
					'field': 'share_rule_id'
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_share_data/coop_share_rule';
					}else{

					}
				}
			});
		} else {
			
		}
	});
	
}

function change_status_salary(){
	if($('#status_salary').is(':checked')){	
		$('.type_1').show();
		$('.type_2').hide();
	}else{		
		$('.type_1').hide();
		$('.type_2').show();
	}
}