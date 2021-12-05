<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.form-group { margin-bottom: 0; }
			.border1 { border: solid 1px #ccc; padding: 0 15px; }
			.mem_pic { float: right; width: 150px; }
			.mem_pic img { width: 100%; border: solid 1px #ccc; }
			.mem_pic button { display: block; width: 100%; }

			.hide_error{color : inherit;border-color : inherit;}

			.has-error{color : #d50000;border-color : #d50000;}

			input::-webkit-outer-spin-button,
			input::-webkit-inner-spin-button {
				-webkit-appearance: none;
				margin: 0;
			}
			.alert-danger {
				background-color: #F2DEDE;
				border-color: #e0b1b8;
				color: #B94A48;
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
			.modal.fade {
				z-index: 10000000 !important;
			}
		</style>
		<h1 style="margin-bottom: 0">วิธีคิด ดบ. เงินฝากสะสม</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>

			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				
			</div>

		</div>

		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<div class="g24-col-sm-24">
						<form action="" method="POST" id="frm1">
							<div class="col-md-offset-3 col-md-6">
								<div class="form-group">
									<label for="" class="control-label g24-col-sm-8">เลขบัญชีเงินฝาก</label>
									<div class="g24-col-sm-13" style="margin-bottom: 5px;padding-top: 5px;">
										<input type="text" class="form-control" placeholder="ระบุเลขบัญชีเงินฝาก" id="account_id" name="account_id">
									</div>
								</div>
								
								<div class="form-group">
									<label for="" class="control-label g24-col-sm-8">วันที่คำนวณ</label>
									<div class="g24-col-sm-13" style="margin-bottom: 5px;padding-top: 5px;">
										<div class="input-with-icon">
											<div class="form-group">
												<input id="date_cal" name="date_cal" class="form-control m-b-1 date" style="padding-left: 50px;" type="text" data-date-language="th-th"  title="กรุณาป้อน วันที่">
												<span class="icon icon-calendar input-icon m-f-1"></span>
											</div>
										</div>
									</div>
								</div>
								
								<div class="row" style="margin-top: 15px;margin-bottom: 15px;">
									<div class="col-md-offset-4 col-md-4 text-center" >
										<button class="btn btn-primary" id="submit_button" type="button">คำนวณ</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					
					<div class="g24-col-sm-24">					
						<?php if(@$account_id != ''){ ?>
												
						<hr style="border-top: 1px dashed #067c3b;">
						
						<?php if(!empty($data['cal'])){ ?>						
						<label for="" class="control-label g24-col-sm-3">เลขบัญชีเงินฝาก : </label>
						<label for="" class="control-label g24-col-sm-2"><?php echo @$data['account_id'];?></label>
						<label for="" class="control-label g24-col-sm-3">วันที่คำนวณ : </label>
						<label for="" class="control-label g24-col-sm-3"><?php echo (@$data['date_end'] != '')?$this->center_function->mydate2date(@$data['date_end']):''; ?></label>
						<label for="" class="control-label g24-col-sm-3">ได้ดอกเบี้ยสะสม : </label>
						<label for="" class="control-label g24-col-sm-2"><?php echo empty($data['old_acc_int']) ? "" : number_format($data['old_acc_int'],2);; ?></label>
						
						<br><h3>วิธีคิด ดบ. เงินฝากสะสม</h3>
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-bordered table-striped table-center" id="table">	
								<thead>
									<tr class="bg-primary">
										<th class = "font-normal" style="width: 5%">ลำดับ</th>
										<th class = "font-normal" style="width: 12%">วันที่เริ่มคำนวณ</th>
										<th class = "font-normal" style="width: 12%">วันที่สิ้นสุดคำนวณ</th>
										<th class = "font-normal" style="width: 10%">อัตราดอกเบี้ย</th> 
										<th class = "font-normal" style="width: 10%">จำนวนวัน</th>
										<th class = "font-normal" style="width: 10%">จำนวนวันในปี</th>
										<th class = "font-normal" >เงินฝากคงเหลือ</th>
										<th class = "font-normal" >ดอกเบี้ย ณ วันคำนวณ</th>
									</tr> 
								</thead>
								<tbody>									
									<?php 
										$i=0;
										foreach($data['cal'] as $key => $row) { 
											$i++;
									?>
											<tr>
												<td><?php echo $i; ?></td>
												<td><?php echo $this->center_function->mydate2date($row['start_date']); ?></td>
												<td><?php echo $this->center_function->mydate2date($row['end_date']); ?></td>
												<td><?php echo $row['interest_rate']; ?></td>
												<td><?php echo $row['date_count']; ?></td>
												<td><?php echo $row['days_of_year']; ?></td>
												<td class="text-right"><?php echo empty($row['transaction_balance']) ? "" : number_format($row['transaction_balance'],2); ?></td>
												<td class="text-right"><?php echo empty($row['accu_int_item']) ? "" : number_format($row['accu_int_item'],2); ?></td>
											</tr>
										<?php } ?>									
								</tbody>
							</table>
							
						</div>							
						<?php }else{ ?>
							<h3 class="text-center">ไม่พบข้อมูล</h3>
						<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
    </div>
</div>

<script>
$(function(){
	$(".date").datepicker({
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
    $( "#submit_button" ).click(function() {
		$('#frm1').submit();
    });
</script>



