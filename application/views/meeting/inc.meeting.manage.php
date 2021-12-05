<div class="layout-content">
	<div class="layout-content-body">
		<div class="col-md-8 col-md-offset-2">
			<h1 class="text-center m-t-1 m-b-2"><?php echo  (!empty($row['meeting_id'])) ? "แก้ไขกิจกรรม" : "เพิ่มกิจกรรม" ; ?></h1>
			<form id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/meeting/save'); ?>" method="post">
				<?php if (!empty($row['meeting_id'])) { ?>
				<input name="type_add"  type="hidden" value="edit" required>
				<input name="id"  type="hidden" value="<?php echo $row['meeting_id']; ?>" required>
				<?php }else{ ?>
				<input name="type_add"  type="hidden" value="add" required>
				<?php } ?>

				<div class="row">
					<label class="col-sm-4 control-label" for="meeting_date">วันที่จัดกิจกรรม</label>
					<div class="col-sm-3">
						<div class="input-with-icon">
							<div class="form-group">
								<input id="meeting_date" name="meeting_date" class="form-control m-b-1 mydate" type="text" value="<?php echo @$row['meeting_date'] ?>" required title="กรุณากรอก วันที่จัดกิจกรรม" data-date-language="th-th">
								<span class="icon icon-calendar input-icon"></span>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 control-label" for="meeting_name">ชื่อกิจกรรม</label>
					<div class="col-sm-8">
						<div class="form-group">
							<input id="meeting_name" name="meeting_name" class="form-control m-b-1" type="text" value="<?php echo @$row['meeting_name'] ?>" required title="กรุณากรอก ชื่อกิจกรรม">
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 control-label" for="meeting_paytype">ค่าตอบแทน</label>
					<div class="col-sm-8">
						<div class="form-group">
							<label><input type="radio" name="meeting_paytype" value="1" class="chk_meeting_paytype"<?php if(@$row["meeting_paytype"] == 1) { ?> checked="checked"<?php } ?>> มี</label> &nbsp;
							<label><input type="radio" name="meeting_paytype" value="0" class="chk_meeting_paytype"<?php if(@$row["meeting_paytype"] == 0) { ?> checked="checked"<?php } ?>> ไม่มี</label>
						</div>
					</div>
				</div>
				<div id="meeting_pay_wrap" class="<?php if(@$row["meeting_paytype"] == 0) { ?>hidden<?php } ?>">
					<div class="row">
						<label class="col-sm-4 control-label" for="meeting_pay">เบี้ยประชุม</label>
						<div class="col-sm-3">
							<div class="form-group">
								<input id="meeting_pay" name="meeting_pay" class="form-control m-b-1" type="number" value="<?php echo @$row['meeting_pay'] ?>">
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 control-label" for="depreciation_percent">การรับเงินเบี้ยประชุม</label>
						<div class="col-sm-8">
							<div class="form-group">
								<label><input type="radio" name="meeting_recvtype" value="1" checked="checked"> โอนเข้าบัญชีสมาชิกทันที</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 control-label" for="meeting_status">หน่วยงานที่มีสิทธิ์เลือกตั้ง</label>
					<div class="col-sm-8">
						<div class="form-group">
							<?php
								$row['mem_group'] = explode(',', $row['mem_group']);
								foreach($mem_group as $k => $v) {
									$checked = in_array($v['id'], $row["mem_group"]) ? ' checked' : '';
									echo sprintf('<div><label><input type="checkbox" name="mem_group[]" value="%s"%s> %s</label></div>', $v['id'], $checked, $v['name']);
								}
							?>
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 control-label" for="meeting_status">ประเภทสมาชิก</label>
					<div class="col-sm-8">
						<div class="form-group">
							<?php
								$row['mem_type'] = explode(',', $row['mem_type']);
								foreach($mem_type as $k => $v) {
									$checked = in_array($v['id'], $row["mem_type"]) ? ' checked' : '';
									echo sprintf('<div><label><input type="checkbox" name="mem_type[]" value="%s"%s> %s</label></div>', $v['id'], $checked, $v['name']);
								}
							?>
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 control-label" for="meeting_status">สถานะ</label>
					<div class="col-sm-8">
						<div class="form-group">
							<label><input type="radio" name="meeting_status" value="0"<?php if(@$row["meeting_status"] == 0) { ?> checked="checked"<?php } ?>> เปิดใช้งาน</label> &nbsp;
							<label><input type="radio" name="meeting_status" value="1"<?php if(@$row["meeting_status"] == 1) { ?> checked="checked"<?php } ?>> เสร็จสิ้น</label>
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 control-label" for="use_photo_personcard">&nbsp;</label>
					<div class="col-sm-8">
						<div class="form-group">
							<label><input type="checkbox" name="use_photo_personcard" value="1"<?php echo $row["use_photo_personcard"] == 1 ? ' checked' : '' ; ?> /> อัปเดตรูปจากบัตรประชาชนลงในโปรไฟล์สมาชิก</label> &nbsp;
						</div>
					</div>
				</div>

				<div class="form-group text-center m-t-1">
					<button type="button"  onclick="check_form()" class="btn btn-primary min-width-100">ตกลง</button>
					<a href="?"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	let check_form = function() {
		$('#form_save').submit();
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

		$(".chk_meeting_paytype").change(function() {
			if($(this).val() == 1) {
				$("#meeting_pay_wrap").removeClass("hidden");
			}
			else {
				$("#meeting_pay_wrap").addClass("hidden");
			}
		});
	});
</script>