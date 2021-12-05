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
		<h1 style="margin-bottom: 0">รายงานทะเบียนคุมเลขที่ใบสำคัญจ่ายเงิน</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/account/coop_report_voucher_excel'); ?>" id="form1" method="POST">
					<div class="form-group g24-col-sm-24">
						<div class="g24-col-sm-5 right">
							<h3></h3>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> วันที่ </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="from_date" name="from_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date("Y-m-d")); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
						<label class="g24-col-sm-1 control-label text-center"> ถึง </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="thru_date" name="thru_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date("Y-m-d")); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"></label>
						<!-- <div class="g24-col-sm-2">
							<button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty('pdf')"><span> PDF</span></button>
						</div> -->
						<div class="g24-col-sm-2">
							<button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty('excel')"><span> EXCEL</span></button>
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
function check_empty(type){
	$.ajax({
		url:base_url+"account/check_report_voucher",
		method:"POST",
		data: $("#form1").serialize(),
		dataType:"text",
		success:function(data){
			if(data == 'success'){
				if(type == 'excel') {
					$('#form1').attr('action', '<?php echo base_url(PROJECTPATH.'/account/coop_report_voucher_excel'); ?>');
				} else {
					$('#form1').attr('action', '<?php echo base_url(PROJECTPATH.'/account/coop_report_voucher_pdf'); ?>');
				}
				$('#form1').submit();
			}else{
				$('#alertNotFindModal').appendTo("body").modal('show');
			}
		}
	});
}
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
	createSelect2("form1");
});

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
</script>
