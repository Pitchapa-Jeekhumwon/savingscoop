<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.modal-header-alert {
				padding:9px 15px;
				border:1px solid #FF0033;
				background-color: #FF0033;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}
			.center {
				text-align: center;
			}
			.right {
				text-align: right;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			label{
				padding-top:7px;
			}
		</style>

		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">รายงานสรุปทุนเรือนหุ้น-เงินกู้คงเหลือ</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/report_share_data/coop_report_share_loan_balance_preview'); ?>" id="form1" method="GET" target="_blank">
		  			<input type="hidden" name="sms_file_type" id="sms_file_type" value="excel"/>
					<h3></h3>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> รูปแบบการค้นหา </label>
						<div class="g24-col-sm-4">
							<select name="type_date" id="type_date" onchange="" class="form-control">
							    <option value="">เลือกรูปแบบการค้นหา</option>
								<option value="1">ทั้งหมดถึงวันที่เลือก</option>
								<option value="2">เฉพาะวันที่เลือก</option>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> วันที่ </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" autocomplete="off" >
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> รูปแบบ </label>
						<div class="g24-col-sm-4">
							<select name="type_department" id="type_department" onchange="" class="form-control">
							    <option value="">เลือกรูปแบบ</option>
								<option value="1">หน่วยงานหลัก</option>
								<option value="2">หน่วยงานย่อย</option>
								<option value="3">รายบุคคล</option>
							</select>
						</div>
					</div>

					<div class="form-group g24-col-sm-24 show_group_name">
						<label class="g24-col-sm-6 control-label right"> เลือกหน่วยงาน</label>
                        <input type="hidden" id="group_id" name="level" value="">
						<div class="g24-col-sm-12 mem_type_list">	
							<label class="custom-control custom-control-primary custom-checkbox g24-col-sm-8" style="padding-top: 9px;margin-left: 15px;">
								<input type="checkbox" class="custom-control-input type_item" id="group_name_all" value="all">
								<span class="custom-control-indicator" style="margin-top: 9px;"></span>
								<span class="custom-control-label">ทั้งหมด</span>
							</label>
							<?php
								if(!empty($row_mem_group)){
									foreach($row_mem_group AS $key=>$value_group){
							?>
										<label class="custom-control custom-control-primary custom-checkbox g24-col-sm-8" style="padding-top: 9px;">
											<input type="checkbox" class="custom-control-input type_item group_item" value="<?php echo @$value_group['id'];?>">
											<span class="custom-control-indicator" style="margin-top: 9px;"></span>
											<span class="custom-control-label"><?php echo @$value_group['mem_group_name'];?></span>
										</label>
							<?php
									}
								}
							?>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"></label>
						<div class="g24-col-sm-3">
							<input type="button" class="btn btn-primary" style="width:100%" value="แสดงรายงาน" onclick="change_type()">
						</div>
						<div class="g24-col-sm-3">
							<input type="button" class="btn btn-default" style="width:100%" value="Export Excel" onclick="export_excel()">
						</div>
						<div class="g24-col-sm-3">
							<input type="button" class="btn btn-default" style="width:100%" value="Export SMS Excel" onclick="export_sms_excel()">
						</div>
						<div class="g24-col-sm-3">
							<input type="button" class="btn btn-default" style="width:100%" value="Export SMS CSV" onclick="export_sms_csv()">
						</div>
					</div>
				</form>				
				</div>
			</div>
		</div>
	</div>
</div>
<script>	
	$( document ).ready(function() {
		$(".show_department").hide();
		$(".show_level").hide();
		$(".show_group_name").hide();
		$("#type_department").change(function() {
			var type_department = $(this).val();
			if(type_department == '2'){
				$(".show_department").hide();
				$(".show_level").hide();
				$(".show_group_name").hide();
			}else if(type_department == '3'){
				$(".show_department").hide();
				$(".show_level").hide();
				$(".show_group_name").show();
			}else{
				$(".show_department").hide();
				$(".show_level").hide();
				$(".show_group_name").hide();
			}
		});
		
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
		
		$("#group_name_all").change(function() {
            if($("#group_name_all").attr('checked') == "checked"){
                $('.type_item').prop('checked', true)
            } else {
                $('.type_item').prop('checked', false)
            }
        });
        $(".type_item").change(function() {
            if($(this).attr('checked') != "checked"){
                $('#group_name_all').prop('checked', false)
            }
        });
	});	
	function change_mem_group(id, id_to){
		var mem_group_id = $('#'+id).val();
		$('#level').html('<option value="">เลือกข้อมูล</option>');
		$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/get_mem_group_list',
			data: {
				mem_group_id : mem_group_id
			},
			success: function(msg){
				$('#'+id_to).html(msg);
			}
		});
	}
	
	function change_type(id){
		var link_to = '';
		
		if($('#type_date').val() == ''){
			swal("กรุณาเลือกรูปแบบวันที่การค้นหาข้อมูล");
		}else if($('#type_department').val() == ''){
			swal("กรุณาเลือกรูปแบบการค้นหาข้อมูล");
		}else{		
			if($('#type_department').val() == '1' || $('#type_department').val() == '2'){
				link_to =  base_url+'report_share_data/coop_report_share_loan_balance_preview';
			}else{
				link_to =  base_url+'report_share_data/coop_report_share_loan_balance_person_preview';
			}
			$('#form1').attr('action', link_to);
			$('#form1').submit();
		}
	}

	function export_excel(){
		var link_to = '';
		
		if($('#type_date').val() == ''){
			swal("กรุณาเลือกรูปแบบวันที่การค้นหาข้อมูล");
		}else if($('#type_department').val() == ''){
			swal("กรุณาเลือกรูปแบบการค้นหาข้อมูล");
		}else{		
			if($('#type_department').val() == '1' || $('#type_department').val() == '2'){
				link_to =  base_url+'report_share_data/coop_report_share_loan_balance_excel';
			}else{
				link_to =  base_url+'report_share_data/coop_report_share_loan_balance_person_excel';
			}
			$('#form1').attr('action', link_to);
			$('#form1').submit();
		}
	}

	function export_sms_excel(){
		var link_to = '';

		if($('#type_date').val() == '') {
			swal("กรุณาเลือกรูปแบบวันที่การค้นหาข้อมูล");
		} else{
			link_to =  base_url+'report_share_data/coop_report_share_loan_balance_sms_person';
			// link_to =  '/spktcoop/system.spktcoop.com/report_share_data/coop_report_share_loan_balance_sms_person';
			$("#sms_file_type").val("excel");
			$('#form1').attr('action', link_to);
			$('#form1').submit();
		}
	}

	function export_sms_csv(){
		var link_to = '';

		if($('#type_date').val() == '') {
			swal("กรุณาเลือกรูปแบบวันที่การค้นหาข้อมูล");
		} else{
			link_to =  base_url+'report_share_data/coop_report_share_loan_balance_sms_person';
			$("#sms_file_type").val("csv");
			// link_to =  '/spktcoop/system.spktcoop.com/report_share_data/coop_report_share_loan_balance_sms_person';
			$('#form1').attr('action', link_to);
			$('#form1').submit();
		}
	}

	$(document).on('change', '.group_item, #group_name_all', function(){
	    var list = "";
	    var isfirst = 1;
	    var comma = ',';
	    $('.group_item').each(function(){
	        if($(this).is(':checked')) {
	            if(isfirst) {
                    isfirst = 0;
                }else{
	                list+=comma;
                }
                list += $(this).val()
            }
        });
	    console.log(list);
        $('#group_id').val(list);
    })

</script>


