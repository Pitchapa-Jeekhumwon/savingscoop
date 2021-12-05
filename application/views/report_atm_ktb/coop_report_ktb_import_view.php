<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.modal.fade {
			  z-index: 10000000 !important;
			}
			.modal-backdrop.in{
				opacity: 0;
			}
			.modal-backdrop {
				position: relative;
				top: 0;
				right: 0;
				bottom: 0;
				left: 0;
				z-index: 1040;
				background-color: #000;
			}
			  
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
			
            .modal-footer {
                border-top:0;
            }	

		</style>
		
		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">รายงานนำเข้าข้อมูล แนบไฟล์ CSV ที่ได้จาก KTB</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<form action="<?php echo base_url(PROJECTPATH.'/report_atm_ktb/coop_report_ktb_import_view'); ?>" id="form1" method="GET">
						<h3></h3>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right">รหัสสมาชิก</label>
							<div class="g24-col-sm-4">
								<div class="input-group">
									<input id="member_id" name="member_id" class="form-control member_id" type="text" value="<?php echo @$_GET['member_id']; ?>" onkeypress="check_member_id();">
									<span class="input-group-btn">
										<a data-toggle="modal" data-target="#myModal" id="test" class="fancybox_share fancybox.iframe" href="#">
											<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
										</a>
									</span>	
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right">หรือแสดงข้อมูลวันที่ </label>
							<div class="g24-col-sm-4">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" autocomplete="off" type="text" value="<?php echo ($start_date != '')?$this->center_function->mydate2date(@$start_date):''; ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
							<label class="g24-col-sm-1 control-label right"> ถึงวันที่ </label>
							<div class="g24-col-sm-4">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="end_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" autocomplete="off" type="text" value="<?php echo ($end_date != '')?$this->center_function->mydate2date(@$end_date):''; ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"></label>
							<div class="g24-col-sm-7">
								<input type="button" class="btn btn-primary" style="width:100%" value="แสดงข้อมูล" onclick="check_empty()">
							</div>
							<div class="g24-col-sm-4">
								<?php 
									$param = '?member_id='.@$_GET['member_id'].'&start_date='.@$_GET['start_date'].'&end_date='.@$_GET['end_date'];
								?>
								<a id="btn_link_export" href="<?php echo base_url(PROJECTPATH.'/report_atm_ktb/coop_report_ktb_import_excel'.$param); ?>" target="_blank" class="btn btn-default" style="width:100%">พิมพ์รายงาน</a>
							</div>
						</div>
					</form>
					
					<div class="form-group g24-col-sm-24"></div>
					<table class="table table-bordered table-striped table-center">
                        <thead>
                            <tr class="bg-primary">
                                <th class="text-center" style="width:5%;">ลำดับ</th>
                                <th class="text-center" style="width:15%;">วันที่ทำรายการ</th>
                                <th class="text-center" style="width:10%;">รหัสสมาชิก</th>
                                <th class="text-center" style="width:50%;">ชื่อสกุล</th>
                                <th class="text-center" style="width:10%;">บัญชี KTB</th>
                                <th class="text-center" style="width:10%;">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody id="table_first">
                        <?php
							$runno = 0;
                            if(!empty($datas)) {
                                foreach($datas as $data) {
									$runno++;
                        ?>
                            <tr>
                                <td class="text-center"><?php echo @$runno; ?></td>
                                <td class="text-center"><?php echo (@$data['import_date_file'] != '')?$this->center_function->ConvertToThaiDate($data['import_date_file'],0,0,0):''; ?></td>
                                <td class="text-center"><?php echo @$data['import_mem_id']; ?></td>
                                <td class="text-left"><?php echo @$data['member_name']; ?></td>
                                <td class="text-center"><?php echo @$data['import_acct']; ?></td>
                                <td class="text-center"><?php echo @$data['import_result']; ?></td>
                            </tr>
                        <?php
                                }
                            } else {
                        ?>
                            <tr>
                                <td class="text-center" colspan="8">ไม่พบข้อมูล</td>
                            </tr>
                        <?php
                            }
                        ?>
                        </tbody>
                    </table>		
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('search_member_new_modal'); ?>
<script>
var base_url = $('#base_url').attr('class');
	
function check_member_id() {
	var member_id = $('.member_id').first().val();
	var keycode = (event.keyCode ? event.keyCode : event.which);
	if(keycode == '13'){
	  $.post(base_url+"save_money/check_member_id", 
	  {	
		member_id: member_id
	  }
	  , function(result){
		 obj = JSON.parse(result);
		 console.log(obj.member_id);
		 mem_id = obj.member_id;
		 if(mem_id != undefined){
		   document.location.href = '<?php echo base_url(uri_string())?>?member_id='+mem_id
		 }else{					
		   swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning');
		 }
	   });
	 }
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
	
});

function check_empty(){
	var start_date = $('#start_date').val();
	var end_date = $('#end_date').val();	
	$('#form1').submit();
}
</script>
