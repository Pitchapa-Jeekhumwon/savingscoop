<style>
	body { background: #eceff1; font-size: 13px; }
	h2 { margin: 0; }
	.sweet-alert h2 { margin: 0 0 10px 0; }
	.has-success .form-control { border-color: #e0e0e0; }
	.has-success .form-control:focus { border-color: #ff7534; }
	.form-group { margin-bottom: 10px; }
	.form-r { background-color: #e0e0e0; }
	.time1 { display: inline; }
	.time1 .time { display: inline-block; width: 20px; text-align: center; }
	.time1 .sep { display: inline-block; width: 20px; text-align: center; }
	.img-thumbnail { background: #9f9f9f; border-color: #9f9f9f; }
	.label { font-size: 12px; font-weight: normal; }
	
	.btn_img, .btn_status {cursor: pointer; }
</style>

<nav style="font-size: 28px;font-family: upbean;padding-top: 3px;padding-bottom: 3px; background: #067c3b; color: #fff;"><?php echo $name_coop['title_name']; ?></nav>

<div class="container">
	<div class="row" style="margin-top: 30px;">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-body">
				<?php if($row["meeting_status"] == 1) { ?>
					<h3 class="center" style="margin-top: 30px;">กิจกรรมเสร็จสิ้นแล้ว</h3>
				<?php } else { ?>
					<div>
						<input id="id" name="id" type="hidden" value="<?php echo $id; ?>">
						<input id="type" name="type" type="hidden" value="">

						<div class="row" style="margin-top: 30px;">
							<div class="col-md-3">
								<img id="member_pic" src="/assets/images/blank.png" class="img-thumbnail" style="width: 150px; min-height: 150px;">
							</div>
							<div class="col-md-9">
								<div class="row">
									<label class="col-sm-3 control-label" for="member_id">รหัสสมาชิก</label>
									<div class="col-sm-5">
										<div class="form-group">
											<input id="member_id" name="member_id" class="form-control m-b-1" type="text" value="" readonly="readonly" autocomplete="off">
										</div>
									</div>
								</div>

								<div class="row">
									<label class="col-sm-3 control-label" for="fullname">ชื่อ - นามสกุล</label>
									<div class="col-sm-9">
										<div class="form-group">
											<input id="fullname" name="fullname" class="form-control form-r m-b-1" type="text" value="" readonly="readonly" autocomplete="off">
										</div>
									</div>
								</div>
								
								<div style="margin-top: 30px;">
									<h2 style="color: #067c3b;">ลงทะเบียนแล้วเมื่อเวลา <div id="facesacn_time" class="text-danger time1"></div></h2>
								</div>
							</div>
						</div>
						
					</div>
					<br>
					
					<table id="tb_data" class="table table-striped">
						<thead>
								<tr>
								<th class="text-center" style="width: 80px;">ลำดับ</th>
								<th class="text-center" style="width: 50px;">รูป</th>
								<th class="text-center" style="width: 120px;">รหัสสมาชิก</th>
								<th>ชื่อสกุล</th>
								<th class="text-center" style="width: 150px;">เวลาลงทะเบียน</th>
								<th class="text-center" style="width: 120px;">การลงทะเบียน</th>
								<th class="text-center" style="width: 120px;">สถานะ</th>
								</tr>
						</thead>
						<tbody></tbody>
					</table>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('search_member_modal_jquery'); ?>

<div id="modal_img" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog">
	  <div class="modal-content">
		<div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal">
			<div aria-hidden="true">×</div>
			<div class="sr-only">ปิด</div>
		  </button>
		</div>
		<div class="modal-body">
		  <img src="" class="img_preview img-responsive" alt="">
		</div>
		<div class="modal-footer"></div>
	  </div>
	</div>
</div>

<script src="/assets/js/elephant.min.js"></script>
<script src="/assets/js/application.min.js"></script>
<script src="/assets/js/jquery.blockUI.js"></script>
<script src="/assets/js/sweetalert.min.js"></script>

<script>
	var base_url = $('#base_url').attr('class');
	var web_base_url = "<?php echo WEB_BASE_URL; ?>";
	var server = "<?php echo API_SERVER; ?>";

	$(document).ready(function() {
		
		function load_member_data(type) {
			$("#type").val(type);

			$.ajax({
				url: base_url + "/meeting/get_member",
				method: "POST",
				data: {
					"id_card": $("#id_card").val(),
					"member_id": $("#member_id").val(),
					"type": type
				},
				success: function(msg){
					data = jQuery.parseJSON(msg);
					$("#id_card").val(data["id_card"]);
					$("#member_id").val(data["member_id"]);

					$("#fullname").val(data["fullname"]);
					$("#address").val(data["address"]);
					$("#birthday").val(data["birthday"]);
					$("#age").val(data["age"]);
					$("#tel").val(data["tel"]);
					if(type == 2) {
						$("#member_pic").prop("src", data["member_pic"]);
					}
				}
			});
		}
		
		function load_facescan_dup() {
			$.ajax({
				url: web_base_url + "/APIs/facescan.get.meeting.register.dup.php",
				method: "POST",
				data: {
					"id": $("#id").val(),
					"server": server
				},
				success: function(data){
					var index = data.count;
					$('#tb_data tbody').empty();
					$.each(data.data, function($key, $row) {
						if($key == 0) {
							$("#member_id").val($row["member_id"]);
							$("#fullname").val($row["fullname"]);
							$("#member_pic").prop("src", $row["member_pic"]);
							$("#facesacn_time").html($row.create_time + ' (' + $row.regis_type + ')');
						}
						
						$('#tb_data tbody').append(`
						<tr>
							<td class="text-center">${index}</td>
							<td class="text-center"><img src="${$row.member_pic}" class="btn_img img-responsive" alt=""></td>
							<td class="text-center">${$row.member_id}</td>
							<td>${$row.fullname}</td>
							<td class="text-center">${$row.create_time}</td>
							<td class="text-center">${$row.regis_type}</td>
							<td class="text-center"><div class="btn_status label ` + ($row.dup_status == 1 ? 'label-success' : 'label-danger') + `" data-id="${$row.id}" data-status="${$row.dup_status}">${$row.dup_status_text}</div></td>
						</tr>
						`);
						
						index--;
					});
				}
			});
		}
		setInterval(function() { load_facescan_dup(); }, 1000);
		load_facescan_dup();
		
		$("body").on("click", ".btn_status", function() {
			var self = $(this);
			
			$.ajax({
				url: base_url + "/meeting/register_facescan_dup/update_status",
				method: "POST",
				data: {
					"id": self.data("id"),
					"dup_status": (self.data("status") == 1 ? 0 : 1)
				},
				success: function(msg){
					data = jQuery.parseJSON(msg);
					load_facescan_dup();
				}
			});
		});
		
		$("body").on("click", ".btn_img", function() {
			$("#modal_img .img_preview").prop("src", $(this).prop("src"));
			$('#modal_img').modal('show');
		});
		
	});
</script>