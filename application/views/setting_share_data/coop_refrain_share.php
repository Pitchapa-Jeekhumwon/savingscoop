<div class="layout-content">
    <div class="layout-content-body">
	<?php if (@$act != "add") { ?>
		<h1 style="margin-bottom: 0">การงดหุ้น</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
	<?php } ?>

		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
						<form id='form1' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_share_data/coop_refrain_share_save'); ?>" method="post">
							<input name="id"  type="hidden" value="<?php echo @$row['id']?>">
							<h3></h3>
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-4 control-label">&nbsp;</label>
									<label class="g24-col-sm-4 control-labell text-left"><h3 style="margin-bottom:0px;">งดหุ้นถาวร</h3></label>
								</div>
							</div>
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label right"> ส่งค่าหุ้นแล้วไม่น้อยกว่า </label>
									<div class="g24-col-sm-4">
										<input class="form-control m-b-1" type="number" name="min_share_month" id="min_share_month" value="<?php echo @$row['min_share_month']?>">
										<input  name="id" type="hidden" value="<?php echo @$row['id']?>">
									</div>
									<label class="g24-col-sm-8 control-label text-left">  เดือนและไม่มีหนี้ ไม่ติดค้ำประกัน</label>
								</div>
							</div>
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label right"> มีจำนวนเงินไม่น้อยกว่า </label>
									<div class="g24-col-sm-4">
										<input class="form-control m-b-1" type="number" name="min_share_collect" id="min_share_collect" value="<?php echo @$row['min_share_collect']?>">
										<input  name="id" type="hidden" value="<?php echo @$row['id']?>">
									</div>
									<label class="g24-col-sm-8 control-label text-left">  บาท </label>
								</div>
							</div>
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label right"> ตรวจสอบเงื่อนไข </label>
									<div class="g24-col-sm-4">
										<input type="checkbox" id="check_debt" name="check_debt"  <?php echo @$row['check_debt'] == '1' ? 'checked' : '';  ?>>
										<label for="vehicle1"> หนี้ </label><br>
									</div>
								</div>
							</div>
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label right">  </label>
									<div class="g24-col-sm-4">
										<input type="checkbox" id="ckeck_guarantee" name="ckeck_guarantee" <?php echo @$row['ckeck_guarantee'] == '1' ? 'checked' : '';  ?>>
										<label for="vehicle1"> ภาระค้ำประกัน </label><br>
									</div>
								</div>
							</div>
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-4 control-label">&nbsp;</label>
									<label class="g24-col-sm-4 control-label text-left"><h3 style="margin-bottom:0px;">งดหุ้นชั่วคราว</h3></label>
								</div>
							</div>
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label right"> ไม่เกิน </label>
									<div class="g24-col-sm-4">
										<input class="form-control m-b-1" type="number" name="max_refrain" id="max_refrain" value="<?php echo @$row['max_refrain']?>">
										<input  name="id" type="hidden" value="<?php echo @$row['id']?>">
									</div>
									<label class="g24-col-sm-8 control-label text-left"> ครั้งต่อปี</label>
								</div>
							</div>
							<div class="g24-col-sm-24">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label "></label>
									<div class="g24-col-sm-4" style="text-align:center;">
										<button class="btn btn-primary" type="button" onclick="submit_form()"><span class="icon icon-save"></span> บันทึก</button>
									</div>
								</div>
							</div>
						</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
 function submit_form(){
	var text_alert = '';
	if($.trim($('#min_share_month').val())== ''){
		text_alert += ' - จำนวนเดือนที่ส่งค่าหุ้น\n';
	}
	if($.trim($('#max_refrain').val())== ''){
		text_alert += ' - จำนวนการงดหุ้นชั่วคราว\n';
	}
	
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form1').submit();
	}
 }
</script>
