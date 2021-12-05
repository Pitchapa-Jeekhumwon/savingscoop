<style>
	.modal-dialog {
        width: 700px;
    }
</style>
<div class="layout-content">
    <div class="layout-content-body">
		<?php
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		?>
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
		<h1 style="margin-bottom: 0">รายงานสรุปดอกเบี้ยสะสมเงินฝากประจำเดือน</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/report_deposit_data/coop_report_conclude_interest_monthly_preview'); ?>" id="form1" method="GET" target="_blank">
					<h3></h3>
					<div id="type_wrap" class="form-group g24-col-sm-24 type_wrap">
						<label class="g24-col-sm-6 control-label right">ประเภทบัญชี</label>
						<div class="g24-col-sm-11">
							<select name="type_id" id="type_id" onchange="" class="form-control">
								<option value="0" >ทั้งหมด</option>
							    <?php
									$type_id = "";
							    	foreach ($type_ids as $key => $type) {
										if(empty($type_id)) $type_id = $type["type_id"];
							    ?>
									<option value="<?php echo $type["type_id"]?>"><?php echo $type["type_code"]?> <?php echo $type["type_name"]?></option>
								<?php
									}
								?>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24 type_wrap">
						<label class="g24-col-sm-6 control-label right">ประเภทสมัครสมาชิก</label>
						<div class="g24-col-sm-11">
							<select name="apply_type_id" id="apply_type_id" onchange="" class="form-control">
								<option value="0">ทั้งหมด</option>
							    <?php
									$apply_type_id = "";
							    	foreach ($row_mem_apply_type as $key_apply_type => $value_apply_type) {
										if(empty($apply_type_id)) $apply_type_id = $value_apply_type["apply_type_id"];
				
							    ?>
									<option value="<?php echo $value_apply_type["apply_type_id"]?>"><?php echo $value_apply_type["apply_type_name"]?></option>
								<?php
									}
								?>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> เดือน </label>
						<div class="g24-col-sm-4">
							<select id="month" name="month" class="form-control">
								<?php foreach($month_arr as $key => $value){ ?>
									<option value="<?php echo $key; ?>" <?php echo $key==((int)date('m'))?'selected':''; ?>><?php echo $value; ?></option>
								<?php } ?>
							</select>
						</div>
						<label class="g24-col-sm-1 control-label right"> ปี </label>
						<div class="g24-col-sm-4">
							<select id="year" name="year" class="form-control">
								<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
									<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-5 control-label right"></label>
						<div class="g24-col-sm-7">
							<button type="button" class="btn btn-primary" style="width:100%" onclick="check_data()">รายงานสรุปดอกเบี้ยสะสมเงินฝากประจำเดือน</button>
						</div>
						<div class="g24-col-sm-3">
							<a id="btn_link_export" class="btn btn-default" style="width:100%" onclick="to_excel_export()">Export Excel</a>
						</div>
					</div>
				</form>				
				</div>
			</div>
		</div>
	</div>
</div>
  
<script>	
	var base_url = $('#base_url').attr('class');
	$( document ).ready(function() {
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
		$("#type_date").change(function() {
			if($(this).val() == 1) {
				$(".end-date-label").hide();
			} else {
				$(".end-date-label").show();
			}
		});
		$(".chk_report_type").click(function() {
			if($(this).val() == 1) {
				$(".type_wrap").addClass("hidden");
			}
			else {
				$(".type_wrap").removeClass("hidden");
			}
			link_export();
		});
	});
	function to_excel_export() {
		$.get(base_url+"/report_deposit_data/check_coop_report_conclude_interest_monthly?apply_type_id="+$("#apply_type_id").val() + "&type_id=" + $("#type_id").val()+'&month='+$("#month").val()+'&year='+$('#year').val(),r=>{
				if(r.result==0){
					window.location = base_url+"/report_deposit_data/coop_report_conclude_interest_monthly_preview?apply_type_id="+$("#apply_type_id").val() + "&type_id=" + $("#type_id").val()+'&month='+$("#month").val()+'&year='+$('#year').val()+"&Excel=true";
				}else{
					swal('ไม่พบข้อมูล','','warning'); 
				}
			},'json');

			
		}
		function check_data() {
			$.get(base_url+"/report_deposit_data/check_coop_report_conclude_interest_monthly?apply_type_id="+$("#apply_type_id").val() + "&type_id=" + $("#type_id").val()+'&month='+$("#month").val()+'&year='+$('#year').val(),r=>{
				if(r.result==0){
					$("#form1").submit();
				}else{
					swal('ไม่พบข้อมูล','','warning'); 
				}
			},'json');
		}
</script>


