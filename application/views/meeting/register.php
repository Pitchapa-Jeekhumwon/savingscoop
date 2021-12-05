<style>
	body { background: #eceff1; font-size: 13px; }
	h2 { margin: 0; }
	.sweet-alert h2 { margin: 0 0 10px 0; }
	.has-success .form-control { border-color: #e0e0e0; }
	.has-success .form-control:focus { border-color: #ff7534; }
	.form-group { margin-bottom: 10px; }
	.form-r { background-color: #e0e0e0; }
	#server_time { display: inline; }
	#server_time .time { display: inline-block; width: 20px; text-align: center; }
	#server_time .sep { display: inline-block; width: 20px; text-align: center; }
</style>

<nav style="font-size: 28px;font-family: upbean;padding-top: 3px;padding-bottom: 3px; background: #067c3b; color: #fff;"><?php echo $name_coop['title_name']; ?></nav>

<div class="container">
	<div class="row" style="margin-top: 30px;">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-body">
				<h2><?php echo $row["meeting_name"]; ?></h2>
				<h2>วันที่ <?php echo $this->center_function->ConvertToThaiDate($row["meeting_date"], 0, 0); ?></h2>
				<h2>เวลา <div id="server_time" class="text-danger"></div></h2>
				<input type="hidden" id="meeting_paytype" value="<?php echo $row["meeting_paytype"]; ?>">

				<?php if($row["meeting_status"] == 1) { ?>
					<h3 class="center" style="margin-top: 30px;">กิจกรรมเสร็จสิ้นแล้ว</h3>
				<?php } else { ?>
					<form id="frm_regis" method="post" action="" data-toggle="validator" novalidate="novalidate">
						<input id="id" name="id" type="hidden" value="<?php echo $id; ?>">
						<input id="type" name="type" type="hidden" value="">

						<div class="row" style="margin-top: 30px;">
							<div class="col-md-3 col-md-push-9">
								<canvas id="canvas_id_card" class="img-thumbnail" style="width: 150px; min-height: 180px;">
							</div>
							<div class="col-md-9 col-md-pull-3">
								<div class="row">
									<label class="col-sm-3 control-label" for="id_card">เลขบัตรประชาชน</label>
									<div class="col-sm-5">
										<div class="form-group">
											<div class="input-group">
												<input id="id_card" name="id_card" class="form-control m-b-1" type="text" value="" required title="กรุณากรอก เลขบัตรประชาชน" autocomplete="off">
												<span class="input-group-btn">
													<button type="button" class="btn btn-info btn-search" tabindex="-1" data-toggle="modal" data-target="#myModal"><span class="icon icon-search"></span></button>
												</span>
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<label class="col-sm-3 control-label" for="member_id">รหัสสมาชิก</label>
									<div class="col-sm-5">
										<div class="form-group">
											<div class="input-group">
												<input id="member_id" name="member_id" class="form-control m-b-1" type="text" value="" readonly="readonly" required title="หรือ รหัสสมาชิก" autocomplete="off">
												<span class="input-group-btn">
													<button type="button" class="btn btn-info btn-search" tabindex="-1" data-toggle="modal" data-target="#myModal"><span class="icon icon-search"></span></button>
												</span>
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<label class="col-sm-3 control-label" for="fullname">ชื่อ - นามสกุล</label>
									<div class="col-sm-9">
										<div class="form-group">
											<input id="fullname" name="fullname" class="form-control form-r m-b-1" type="text" value="" autocomplete="off">
										</div>
									</div>
								</div>

								<div class="row">
									<label class="col-sm-3 control-label" for="address">ที่อยู่</label>
									<div class="col-sm-9">
										<div class="form-group">
											<input id="address" name="address" class="form-control form-r m-b-1" type="text" value="" autocomplete="off">
										</div>
									</div>
								</div>

								<div class="row">
									<label class="col-sm-3 control-label" for="birthday">วันเดือนปีเกิด</label>
									<div class="col-sm-5">
										<div class="form-group">
											<input id="birthday" name="birthday" class="form-control form-r m-b-1" type="text" value="" autocomplete="off">
										</div>
									</div>
									<label class="col-sm-2 control-label" for="age">อายุ</label>
									<div class="col-sm-2">
										<div class="form-group">
											<input id="age" name="age" class="form-control form-r m-b-1" type="text" value="" autocomplete="off">
										</div>
									</div>
								</div>



								<div class="row">
									<label class="col-sm-3 control-label" for="address">เบอร์โทร</label>
									<div class="col-sm-9">
										<div class="form-group">
											<input id="tel" name="tel" class="form-control m-b-1" type="text" value="" autocomplete="off">
										</div>
									</div>
								</div>

							</div>
						</div>

						<div style="margin-top: 30px;">
							<button type="button" id="btn_save" class="btn btn-primary btn-lg">ตกลง</button>
						</div>
					</form>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('search_member_modal_jquery'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/assets/js/elephant.min.js"></script>
<script src="/assets/js/application.min.js"></script>
<script src="/assets/js/jquery.blockUI.js"></script>
<script src="/assets/js/sweetalert.min.js"></script>

<script>
	var base_url = $('#base_url').attr('class');
	var get_data;
	var is_show_modal = false;
	var is_tel_focus = false;

	$(document).ready(function() {
		$("#id_card").focus();

		$('body').on('focus', '#tel', function() { is_tel_focus = true; });
		$('body').on('blur', '#tel', function() { is_tel_focus = false; });

		$(document).click(function() {
			if(!is_show_modal && !is_tel_focus) {
				$("#id_card").focus();
			}
		});

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

		function load_member_img(src) {
			var canvas = document.getElementById("canvas_id_card");
			var ctx = canvas.getContext('2d');
			var img = new Image();
			img.onload = function () {
				canvas.width = this.width;
				canvas.height = this.height;
				ctx.drawImage(img, 0, 0);
			};
			img.src = src;
		}

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
						load_member_img(data["member_pic"]);
					}
				}
			});
		}

		function clear_form_regis() {
			$("#frm_regis").trigger("reset");
			var canvas = document.getElementById("canvas_id_card");
			var ctx = canvas.getContext('2d');
			ctx.clearRect(0, 0, canvas.width, canvas.height);
			$("#id_card").focus();
		}

		$("#frm_regis").submit(function(e) {
			e.preventDefault();

			swal({
				title: "ลงทะเบียน",
				text: "บันทึกการลงทะเบียน\nสมาชิกรหัส " + $("#member_id").val() + " ชื่อสกุล " + $("#fullname").val() + ($("#meeting_paytype").val() == 1 ? "\nระบบจะทำการโอนเงินให้สมาชิกอัตโนมัติ" : ""),
				//type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#3a6336',
				confirmButtonText: 'ตกลง',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: true,
				closeOnCancel: true
			},
			function(isConfirm) {
				if(isConfirm) {
					var canvas_id_card = document.getElementById("canvas_id_card");
					var params = new FormData($("#frm_regis")[0]);
					params.append("id_card_img64", canvas_id_card.toDataURL());

					$.ajax({
						type: "POST",
						url: base_url + "/meeting/register_save",
						data: params,
						contentType: false,
						cache: false,
						processData: false,
						success: function(msg) {
							data = jQuery.parseJSON(msg);

							if(data["status"] == "1") {
								swal({
									title: "ลงทะเบียน",
									text: data["msg"],
									type: "success",
									confirmButtonColor: '#3a6336',
									confirmButtonText: 'ปิดหน้าต่าง',
									closeOnConfirm: true
								},
								function(isConfirm) {
									clear_form_regis();
								});
							}
							else if(data["status"] == "0") {
								swal({
									title: "ลงทะเบียน",
									text: data["msg"],
									type: "warning",
									confirmButtonColor: '#3a6336',
									confirmButtonText: 'ปิดหน้าต่าง',
									closeOnConfirm: true
								},
								function(isConfirm) {
									clear_form_regis();
								});
							}
							else if(data["status"] == "-1") {
								swal({
									title: "ลงทะเบียน",
									text: "ท่านไม่มีสิทธิ์ลงทะเบียน",
									type: "warning",
									confirmButtonColor: '#3a6336',
									confirmButtonText: 'ปิดหน้าต่าง',
									closeOnConfirm: true
								},
								function(isConfirm) {
									clear_form_regis();
								});
							}
							else if(data["status"] == "-2") {
								location.reload(true);
							}
						}
					});
				}
			});
		});

		$("#btn_save").click(function() {
			var validator = $("#frm_regis").validate();
			if(validator.form()) {
				$("#frm_regis").submit();
			}
			$("#id_card").focus();
		});

        $('.modal').on('shown.bs.modal', function() {
			is_show_modal = true;
			$("#search_text").focus();
        });
        $('.modal').on('hidden.bs.modal', function () {
			is_show_modal = false;
			$("#search_text").val("");
			$("#result_member").html("");

			setTimeout(function() { $("#id_card").focus(); }, 1);
        });

		$("#id_card").keydown(function(e){
			if(e.which == 13 && $(this).val() != "") {
				 load_member_data(1);
			}
		});

		get_data = function(member_id, fullname, ref) {
			$("#member_id").val(member_id);
			load_member_data(2);
			$("#myModal").modal("hide");
		}
	});
</script>

<script>
	function retrieveImageFromClipboardAsBlob(pasteEvent, callback){
		if(pasteEvent.clipboardData == false){
			if(typeof(callback) == "function"){
				callback(undefined);
			}
		};

		var items = pasteEvent.clipboardData.items;

		if(items == undefined){
			if(typeof(callback) == "function"){
				callback(undefined);
			}
		};

		for (var i = 0; i < items.length; i++) {
			// Skip content if not image
			if (items[i].type.indexOf("image") == -1) continue;
			// Retrieve image on clipboard as blob
			var blob = items[i].getAsFile();

			if(typeof(callback) == "function"){
				callback(blob);
			}
		}
	}

	window.addEventListener("paste", function(e){

		// Handle the event
		retrieveImageFromClipboardAsBlob(e, function(imageBlob){
			// If there's an image, display it in the canvas
			if(imageBlob){
				var canvas = document.getElementById("canvas_id_card");
				var ctx = canvas.getContext('2d');

				// Create an image to render the blob on the canvas
				var img = new Image();

				// Once the image loads, render the img on the canvas
				img.onload = function(){
					// Update dimensions of the canvas with the dimensions of the image
					canvas.width = this.width;
					canvas.height = this.height;

					// Draw the image
					ctx.drawImage(img, 0, 0);
				};

				// Crossbrowser support for URL
				var URLObj = window.URL || window.webkitURL;

				// Creates a DOMString containing a URL representing the object given in the parameter
				// namely the original Blob
				img.src = URLObj.createObjectURL(imageBlob);
			}
		});
	}, false);
</script>