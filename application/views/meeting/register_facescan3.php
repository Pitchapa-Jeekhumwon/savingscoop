<style>
	body { background: #eceff1; font-size: 13px; }
	h2 { margin: 0; }
	span { font-family: inherit; font-size: inherit; }
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
	.table { margin-bottom: 0; }
	.dataTable { margin-top: 0; }
	.dataTables_length, .dataTables_paginate { display: none; }
	.label-success { background-color: #067c3b; }
	
	.btn_img, .btn_status {cursor: pointer; }
	
	#reg_list_scroll { height: 550px; overflow-y: auto; overflow-x: hidden; position: relative; }
	#reg_list_scroll_load { position: relative; width: 1px; }
	#reg_list { position: absolute; top: 0; left: 0; right: 0; }
	#reg_list .cell { height: 117px; }
	.reg_list .col1 { float: left; width: 80px; }
	.reg_list .col2 { margin-left: 90px; text-align: left; }
	.reg_list .num { background: #999; color: #fff; font-size: 24px; }
	.reg_list .num.active { background: #067c3b; }
	.reg_list .num.error { background: #d50000; }
	.reg_list .sep { clear: both; border-bottom: solid 1px #e0e0e0; padding-top: 10px; margin-bottom: 10px; }
	.btn_num, .btn_money, .btn_gift { cursor: pointer; }
	.btn_money, .btn_gift { width: 90px;  }
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
					<input id="id" name="id" type="hidden" value="<?php echo $id; ?>">
					
					<div class="row m-t-2">
						<label class="col-sm-1 control-label">ช่องที่</label>
						<div class="col-sm-2">
							<div class="form-group">
								<select id="mach_id" name="mach_id" class="form-control">
									<option value="">ทั้งหมด</option>
									<?php foreach($machines as $machine) { ?>
										<option value="<?php echo $machine["id"] ?>"><?php echo $machine["no"] ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<label class="col-sm-1 control-label">ค้นหา</label>
						<div class="col-sm-4">
							<div class="form-group">
								<input id="keyword" name="keyword" class="form-control" type="text" value="" placeholder="รหัสสมาชิก หรือ ชื่อสกุล">
							</div>
						</div>
						<div class="col-sm-4 text-right">
							<button class="btn btn-primary" id="btn-all">แสดงทั้งหมด</button>
						</div>
					</div>
					
					<h3 class="text-left" style="color: #067c3b;">รายการลงทะเบียนสำเร็จ (<span class="reg_count">0</span> คน)</h3>
					<div id="reg_list_scroll">
						<div id="reg_list" class="row reg_list"></div>
						<div id="reg_list_scroll_load"></div>
					</div>
					
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('search_member_modal_jquery'); ?>

<div id="modal_dialog" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-add">
		<div class="modal-content">
			<div class="modal-header modal-header-info">
				<h2 class="modal-title text-left"></h2>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer" style="border: none;"></div>
		</div>
	</div>
</div>

<div id="modal_card_tail" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-add">
		<div class="modal-content">
			<div class="modal-header modal-header-info">
				<h2 class="modal-title text-left">เลขหางบัตร</h2>
			</div>
			<div class="modal-body">
				<div class="g24-col-sm-24 ">
					<form data-toggle="validator" novalidate="novalidate" action="" method="post" id="frm_card_tail">
						<input type="hidden" name="id" id="mct_meeting_regis_id">
						
						<div class="row m-b-1">
							<div class="col-sm-6">
								<div class="reg_list">
									<div class="col1">
										<img src="" class="btn_img img-responsive" alt="">
										<div class="num active">?</div>
									</div>
									<div class="col2">
										<div class="member_id"></div>
										<div class="fullname"></div>
										<div class="create_time"></div>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label">สแกนบาร์โค็ด เพื่อกำหนดเลขหางบัตร</label>
									<div class="">
										<input type="text" class="form-control text-center" name="card_tail" id="mct_card_tail" value="" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');">
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
				&nbsp;
			</div>
		</div>
	</div>
</div>

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
<script src="https://cdn.datatables.net/scroller/2.0.3/js/dataTables.scroller.min.js"></script>

<script>
	var base_url = $('#base_url').attr('class');
	var web_base_url = "<?php echo WEB_BASE_URL; ?>";
	var server = "<?php echo API_SERVER; ?>";

	var is_reg_list = false;
	
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

		var reg_list_offset = 0;
		var reg_list_length = 90;
		var old_scroll = $("#reg_list_scroll").scrollTop();
		function show_reg_list(callback) {
			if(is_reg_list) {
				return;
			}
			is_reg_list = true;
			
			$.ajax({
				url: base_url + "/meeting/get_facescan3",
				method: "POST",
				data: {
					"id": $("#id").val(),
					"mach_id": $("#mach_id").val(),
					"keyword": $("#keyword").val(),
					"start": reg_list_offset,
					"length": reg_list_length
				},
				success: function(msg){
					$data = jQuery.parseJSON(msg);
					
					$(".reg_count").html($data.count);
					
					$('#reg_list').empty();
					$.each($data.data, function($key, $row) {
						if($key % 3 == 0 && $key > 0) {
							$('#reg_list').append(`<div class="sep"></div>`);
						}
						
						$('#reg_list').append(
							`<div class="col-sm-4 cell">
								<div class="col1">
									<img src="${$row.member_pic}" class="btn_img img-responsive" alt="">
									<div class="` + ($row.is_dup ? `` : `btn_num `) + `num ${$row.num_status}" data-id="${$row.id}">${$row.num}</div>
								</div>
								<div class="col2">
									<div class="member_id">${$row.member_id}</div>
									<div class="fullname">${$row.fullname}</div>
									<div class="create_time">${$row.create_time}</div>` +
									($row.card_tail == "" ? "" :
										`<div>` + ($row.meeting_payment == 1 ?  `<i class="fa fa-check-square-o" aria-hidden="true"></i> รับเงิน` : `<div class="label label-success btn_money" data-id="${$row.id}">รับเงิน</div>`) + `</div>
										<div class="m-t-1">` + ($row.receive_gift == 1 ? `<i class="fa fa-check-square-o" aria-hidden="true"></i> รับของที่ระลึก`: `<div class="label label-success btn_gift" data-id="${$row.id}">รับของที่ระลึก</div>`) + `</div>`
									) + `
								</div>
							</div>`
						);
					});
					
					var row_pre_load = 10;
					var reg_list_cell_h = 117 + 11 /*$('#reg_list .cell:first').height() + 11*/;
					var h =  Math.ceil($data.count * reg_list_cell_h / 3) - (reg_list_cell_h * row_pre_load);
					$("#reg_list_scroll_load").height(h);
					
					var delay;
					$("#reg_list_scroll").scroll(function() {
						var self = $(this);
						
						if(delay) {
							clearTimeout(delay);
						}
						delay = setTimeout(function() {
							var reg_list_top = parseInt($('#reg_list').css("top").replace(/px/g, ""));
							var reg_list_h = $('#reg_list').height();
							var diff_scroll = self.scrollTop() - old_scroll;
							
							//console.log(self.scrollTop() + "/" + h + "/" + reg_list_top + "/" + reg_list_h + "/" + diff_scroll);
							
							if(self.scrollTop() - old_scroll > (reg_list_cell_h * row_pre_load) || self.scrollTop() - old_scroll < (reg_list_cell_h * (row_pre_load / 1) * -1) || self.scrollTop() == 0) {
								console.log(self.scrollTop() + "/" + h + "/" + reg_list_top + "/" + reg_list_h);
								reg_list_offset = Math.ceil(self.scrollTop() / reg_list_cell_h) - (row_pre_load / 2);
								reg_list_offset = (reg_list_offset < 0 ? 0 : reg_list_offset) * 3;
								var adj_top = self.scrollTop() % reg_list_cell_h;
								var top = self.scrollTop() - (reg_list_cell_h * (row_pre_load / 1 - 2)) + adj_top;
								top = top < 0 ? 0 : top;
								reg_list_offset = self.scrollTop() == 0 ? 0 : reg_list_offset;
								console.log("load data:" + reg_list_offset);
								setTimeout(function() {
									show_reg_list(function() {
										$('#reg_list').css("top", top + "px");
										old_scroll = self.scrollTop();
									});
								}, 50);
							}
						}, 100);
					});
					
					is_reg_list = false;
					
					if(callback != null) {
						callback();
					}
				}
			});
		}
		show_reg_list();
		
		var is_update_facescan = false;
		function update_facescan() {
			if(is_update_facescan) {
				return;
			}
			is_update_facescan = true;
			
			$.ajax({
				url: web_base_url + "/APIs/facescan.meeting.register.php",
				method: "POST",
				data: {
					"id": $("#id").val()
				},
				success: function(data){
					show_reg_list();
					is_update_facescan = false;
				}
			});
		}
		setInterval(function() { update_facescan(); }, 1000);
		update_facescan();
		
		$("body").on("click", ".btn_num", function() {
			var cell = $(this).parents(".cell");
			$("#mct_meeting_regis_id").val($(this).data("id"));
			$("#mct_card_tail").val("");
			$("#frm_card_tail").find(".btn_img").prop("src", cell.find(".btn_img").prop("src"));
			$("#frm_card_tail").find(".num").html(cell.find(".num").html());
			$("#frm_card_tail").find(".num").prop("class", cell.find(".num").prop("class"));
			$("#frm_card_tail").find(".member_id").html(cell.find(".member_id").html());
			$("#frm_card_tail").find(".fullname").html(cell.find(".fullname").html());
			$("#frm_card_tail").find(".create_time").html(cell.find(".create_time").html());
			$("#modal_card_tail").on('shown.bs.modal', function () {
				$("#mct_card_tail").focus();
			});
			$("#modal_card_tail").modal('show');
			return false;
		});
		
		$("body").on("keydown", "#mct_card_tail", function(e) {
			$('.form-group').removeClass('has-error').find('.help-block').remove();
		});
		
		$("body").on("keypress", "#mct_card_tail", function(e) {
			$('.form-group').removeClass('has-error').find('.help-block').remove();
			
			if(e.which == 13) {
				e.preventDefault();
				
				var self = $(this);
				
				$.ajax({
					url: base_url + "/meeting/set_card_tail",
					method: "POST",
					data: {
						"id": $("#mct_meeting_regis_id").val(),
						"meeting_id": $("#id").val(),
						"card_tail": $("#mct_card_tail").val()
					},
					success: function(msg){
						data = jQuery.parseJSON(msg);
						if(data.err_code == "") {
							show_reg_list();
							$("#modal_card_tail").modal('hide');
						}
						else {
							self.parents('.form-group').addClass('has-error');
							self.parent().append(`<p class="help-block">${data.msg}</p>`);
						}
					}
				});
			}
		});
		
		var btn_money;
		$("body").on("click", ".btn_money", function() {
			btn_money = $(this);
			
			$("#modal_dialog .modal-title").html("รับเงิน");
			$("#modal_dialog .modal-body").html(
				`<div class="row">
					<div class="col-sm-6 col-sm-offset-3 reg_list">
						<div class="col1">
							<img src="" class="btn_img img-responsive" alt="">
							<div class="num"></div>
						</div>
						<div class="col2">
							<div class="member_id"></div>
							<div class="fullname"></div>
							<div class="create_time"></div>
						</div>
					</div>
				</div>`
			);
			$("#modal_dialog .modal-footer").html(
				`<h3 class="text-center m-b-3">ยืนยันการรับเงิน</h3>
				<div class="row m-b-1">
					<div class="form-group">
						<div class="g24-col-sm-24" style="text-align:center">
							<button type="button" class="btn btn-primary min-width-100 btn_money_ok" data-dismiss="modal">ตกลง</button>
							<button class="btn btn-danger min-width-100" type="button" data-dismiss="modal">ยกเลิก</button>
						</div>
					</div>
				</div>`
			);
			
			var cell = $(this).parents(".cell");
			$("#modal_dialog").find(".btn_img").prop("src", cell.find(".btn_img").prop("src"));
			$("#modal_dialog").find(".num").html(cell.find(".num").html());
			$("#modal_dialog").find(".num").prop("class", cell.find(".num").prop("class"));
			$("#modal_dialog").find(".member_id").html(cell.find(".member_id").html());
			$("#modal_dialog").find(".fullname").html(cell.find(".fullname").html());
			$("#modal_dialog").find(".create_time").html(cell.find(".create_time").html());
			
			$("#modal_dialog").modal('show');
		});
		
		$("body").on("click", ".btn_money_ok", function() {
			$.ajax({
				url: base_url + "/meeting/set_receive_money",
				method: "POST",
				data: {
					"id": btn_money.data("id")
				},
				success: function(msg){
					data = jQuery.parseJSON(msg);
					show_reg_list();
				}
			});
		});
		
		var btn_gift;
		$("body").on("click", ".btn_gift", function() {
			btn_gift = $(this);
			
			$("#modal_dialog .modal-title").html("รับของที่ระลึก");
			$("#modal_dialog .modal-body").html(
				`<div class="row">
					<div class="col-sm-6 col-sm-offset-3 reg_list">
						<div class="col1">
							<img src="" class="btn_img img-responsive" alt="">
							<div class="num"></div>
						</div>
						<div class="col2">
							<div class="member_id"></div>
							<div class="fullname"></div>
							<div class="create_time"></div>
						</div>
					</div>
				</div>`
			);
			$("#modal_dialog .modal-footer").html(
				`<h3 class="text-center m-b-3">ยืนยันการรับของที่ระลึก</h3>
				<div class="row m-b-1">
					<div class="form-group">
						<div class="g24-col-sm-24" style="text-align:center">
							<button type="button" class="btn btn-primary min-width-100 btn_gift_ok" data-dismiss="modal">ตกลง</button>
							<button class="btn btn-danger min-width-100" type="button" data-dismiss="modal">ยกเลิก</button>
						</div>
					</div>
				</div>`
			);
			
			var cell = $(this).parents(".cell");
			$("#modal_dialog").find(".btn_img").prop("src", cell.find(".btn_img").prop("src"));
			$("#modal_dialog").find(".num").html(cell.find(".num").html());
			$("#modal_dialog").find(".num").prop("class", cell.find(".num").prop("class"));
			$("#modal_dialog").find(".member_id").html(cell.find(".member_id").html());
			$("#modal_dialog").find(".fullname").html(cell.find(".fullname").html());
			$("#modal_dialog").find(".create_time").html(cell.find(".create_time").html());
			
			$("#modal_dialog").modal('show');
		});
		
		$("body").on("click", ".btn_gift_ok", function() {
			$.ajax({
				url: base_url + "/meeting/set_receive_gift",
				method: "POST",
				data: {
					"id": btn_gift.data("id")
				},
				success: function(msg){
					data = jQuery.parseJSON(msg);
					show_reg_list();
				}
			});
		});
		
		$("body").on("click", ".btn_img", function() {
			$("#modal_img .img_preview").prop("src", $(this).prop("src"));
			$('#modal_img').modal('show');
		});
		
		$("#mach_id").change(function() {
			show_reg_list();
		});
		
		$("#keyword").on("input", function() {
			show_reg_list();
		});
		
		$("#btn-all").click(function() {
			$("#keyword").val("");
			show_reg_list();
		});
		
	});
</script>