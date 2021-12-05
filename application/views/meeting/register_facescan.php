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
</style>

<nav style="font-size: 28px;font-family: upbean;padding-top: 3px;padding-bottom: 3px; background: #067c3b; color: #fff;"><?php echo $name_coop['title_name']; ?></nav>

<div class="container">
	<div class="row" style="margin-top: 30px;">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-body">
				<h2><?php echo $row["meeting_name"]; ?></h2>
				<h2>วันที่ <?php echo $this->center_function->ConvertToThaiDate($row["meeting_date"], 0, 0); ?></h2>
				<h2>เวลา <div id="server_time" class="text-danger time1"></div></h2>
				<input type="hidden" id="meeting_paytype" value="<?php echo $row["meeting_paytype"]; ?>">

				<?php if($row["meeting_status"] == 1) { ?>
					<h3 class="center" style="margin-top: 30px;">กิจกรรมเสร็จสิ้นแล้ว</h3>
				<?php } else { ?>
					<form id="frm_regis" method="post" action="" data-toggle="validator" novalidate="novalidate">
						<input id="id" name="id" type="hidden" value="<?php echo $id; ?>">
						<input id="type" name="type" type="hidden" value="">

						<div class="row" style="margin-top: 30px;">
							<div class="col-md-3 col-md-push-9">
								<img id="member_pic" src="/assets/images/blank.png" class="img-thumbnail" style="width: 150px; min-height: 180px;">
							</div>
							<div class="col-md-9 col-md-pull-3">
								<div class="row">
									<label class="col-sm-3 control-label" for="id_card">เลขบัตรประชาชน</label>
									<div class="col-sm-5">
										<div class="form-group">
											<input id="id_card" name="id_card" class="form-control m-b-1" type="text" value="" readonly="readonly" autocomplete="off">
										</div>
									</div>
								</div>

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

								<div class="row">
									<label class="col-sm-3 control-label" for="address">ที่อยู่</label>
									<div class="col-sm-9">
										<div class="form-group">
											<input id="address" name="address" class="form-control form-r m-b-1" type="text" value="" readonly="readonly" autocomplete="off">
										</div>
									</div>
								</div>

								<div class="row">
									<label class="col-sm-3 control-label" for="birthday">วันเดือนปีเกิด</label>
									<div class="col-sm-5">
										<div class="form-group">
											<input id="birthday" name="birthday" class="form-control form-r m-b-1" type="text" value="" readonly="readonly" autocomplete="off">
										</div>
									</div>
									<label class="col-sm-2 control-label" for="age">อายุ</label>
									<div class="col-sm-2">
										<div class="form-group">
											<input id="age" name="age" class="form-control form-r m-b-1" type="text" value="" readonly="readonly" autocomplete="off">
										</div>
									</div>
								</div>

							</div>
						</div>

						<div style="margin-top: 30px;">
							<h2 style="color: #067c3b;">ลงทะเบียนแล้วเมื่อเวลา <div id="facesacn_time" class="text-danger time1"></div></h2>
						</div>
					</form>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('search_member_modal_jquery'); ?>

<script src="/assets/js/elephant.min.js"></script>
<script src="/assets/js/application.min.js"></script>
<script src="/assets/js/jquery.blockUI.js"></script>
<script src="/assets/js/sweetalert.min.js"></script>

<script>
	var base_url = $('#base_url').attr('class');
	var web_base_url = "<?php echo WEB_BASE_URL; ?>";

	$(document).ready(function() {
		var time_diff = 0;
		function showServerTime() {
			var d = new Date(new Date().getTime() + time_diff);
			$("#server_time").html('<div class="time">' + ("00" + d.getHours()).slice(-2) + '</div><div class="sep">:</div><div class="time">' + ("00" + d.getMinutes()).slice(-2) + '</div><div class="sep">:</div><div class="time">' + ("00" + d.getSeconds()).slice(-2) + '</div>');
		}

		function loadServerTime() {
			$.ajax({
				url: base_url + "/meeting/gettime",
				method: "POST",
				data: {

				},
				success: function(msg){
					var d_server = new Date(msg);
					var d_local = new Date();
					time_diff = d_server.getTime() - d_local.getTime();

					showServerTime();

					setInterval(function() {
						showServerTime();
					}, 1000);
				}
			});
		}
		loadServerTime();

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
		
		function update_facescan() {
			$.ajax({
				url: web_base_url + "/APIs/facescan.meeting.register.php",
				method: "POST",
				data: {
					"id": $("#id").val()
				},
				success: function(data){
					load_facescan();
				}
			});
		}
		setInterval(function() { update_facescan(); }, 1000);
		update_facescan();
		
		function load_facescan() {
			$.ajax({
				url: base_url + "/meeting/get_facescan",
				method: "POST",
				data: {
					"id": $("#id").val()
				},
				success: function(msg){
					data = jQuery.parseJSON(msg);
					if(data["member_id"] != "") {
						$("#member_id").val(data["member_id"]);
						var d = new Date(data["time"]);
						$("#facesacn_time").html('<div class="time">' + ("00" + d.getHours()).slice(-2) + '</div><div class="sep">:</div><div class="time">' + ("00" + d.getMinutes()).slice(-2) + '</div><div class="sep">:</div><div class="time">' + ("00" + d.getSeconds()).slice(-2) + '</div>');
						load_member_data(2);
					}
				}
			});
		}
		
	});
</script>