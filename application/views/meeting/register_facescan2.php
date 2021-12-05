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
	
	.btn_img, .btn_status {cursor: pointer; }
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
					
					<h3 class="text-danger text-left">รายการลงทะเบียนซ้ำ (<span class="dup_count">0</span> คน)</h3>
					<table id="tb_dup" class="table table-striped">
						<thead>
							<tr>
								<th class="text-center" style="width: 80px;">ลำดับ</th>
								<th class="text-center" style="width: 50px;">รูป</th>
								<th class="text-center" style="width: 120px;">รหัสสมาชิก</th>
								<th>ชื่อสกุล</th>
								<th class="text-center" style="width: 150px;">เวลาลงทะเบียน</th>
								<th class="text-center" style="width: 120px;">การลงทะเบียน</th>
								<th class="text-center" style="width: 240px;">สถานะ</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
					
					<h3 class="text-left" style="color: #067c3b;">รายการลงทะเบียนสำเร็จ (<span class="reg_count">0</span> คน)</h3>
					<table id="tb_reg" class="table table-striped">
						<thead>
							<tr>
								<th class="text-center" style="width: 80px;">ลำดับ</th>
								<th class="text-center" style="width: 50px;">รูป</th>
								<th class="text-center" style="width: 120px;">รหัสสมาชิก</th>
								<th>ชื่อสกุล</th>
								<th class="text-center" style="width: 150px;">เวลาลงทะเบียน</th>
								<th class="text-center" style="width: 120px;">การลงทะเบียน</th>
								<th class="text-center" style="width: 120px;">สถานะ</th>
								<th class="text-center" style="width: 120px;">หางบัตร</th>
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

<div id="modal_card_tail" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-add" style="width:40% !important;">
		<div class="modal-content">
			<div class="modal-header modal-header-info">
				<h2 class="modal-title">หางบัตร</h2>
			</div>
			<div class="modal-body">
				<div class="g24-col-sm-24 ">
					<form data-toggle="validator" novalidate="novalidate" action="" method="post" id="frm_card_tail">
						<input type="hidden" name="id" id="mct_meeting_regis_id">
						
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1">หางบัตร</label>
								<div class="g24-col-sm-11">
									<input type="text" class="form-control" name="card_tail" id="mct_card_tail" value="">
								</div>
							</div>
						</div>
						
						<div class="row m-b-1">
							<div class="form-group">
								<div class="g24-col-sm-24" style="text-align:center">
									<button type="button" class="btn btn-primary min-width-100 mct_btn_save" data-dismiss="modal">บันทึก</button>
									<button class="btn btn-danger min-width-100" type="button" data-dismiss="modal">ยกเลิก</button>
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

	var tb_reg;
	var tb_dup;
	
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

		function tb_reg_scroll() {
			tb_reg = $('#tb_reg');
			if (tb_reg.length) {
				tb_reg = tb_reg.DataTable({
					serverSide: true,
					ajax: function(data, callback, settings) {
						if(is_tb_reg_scroll) {
							return;
						}
						is_tb_reg_scroll = true;
						
						console.log(data);
						var out = [];
						
						$.ajax({
							url: base_url + "/meeting/get_facescan2",
							method: "POST",
							data: {
								"id": $("#id").val(),
								"start": data.start,
								"length": data.length
							},
							success: function(msg){
								$data = jQuery.parseJSON(msg);
								
								$(".reg_count").html($data.count);
								
								var index = data.start + 1;
								$.each($data.data, function($key, $row) {
									out.push([
										`<div class="text-center">${index}</div>`,
										`<img src="${$row.member_pic}" class="btn_img img-responsive" alt="">`,
										`<div class="text-center">${$row.member_id}</div>`,
										$row.fullname,
										`<div class="text-center">${$row.create_time}</div>`,
										`<div class="text-center">${$row.regis_type}</div>`,
										`<div class="text-center"><div class="label ` + ($row.status == 1 ? 'label-success' : 'label-danger') + `">${$row.status_text}</div></div>`,
										`<div class="text-right">${$row.card_tail}&nbsp; <a href="#" class="btn_card_tail" data-id="${$row.id}" data-val="${$row.card_tail}"><i class="icon icon-pencil"></i></a></div>`
									]);
									index++;
								});
								
								setTimeout( function () {
									callback( {
										draw: data.draw,
										data: out,
										recordsTotal: $data.count,
										recordsFiltered: $data.count
									} );
									
									is_tb_reg_scroll = false;
								}, 50 );
							}
						});
					},
					deferRender: true,
					responsive: true,
					scroller: true,
					scrollY: 430,
					scrollCollapse: true,
					paging: true,
					info: false,
					ordering: false,
					searching: false
				});
			}
		}
		tb_reg_scroll();
		
		var is_update_facescan = false;
		var is_tb_reg_scroll = false;
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
					tb_reg.ajax.reload(null, false);
					is_update_facescan = false;
				}
			});
		}
		setInterval(function() { update_facescan(); }, 1000);
		//update_facescan();
		
		var is_tb_dup_scroll = false;
		function tb_dup_scroll() {
			tb_dup = $('#tb_dup');
			if (tb_dup.length) {
				tb_dup = tb_dup.DataTable({
					serverSide: true,
					ajax: function(data, callback, settings) {
						if(is_tb_dup_scroll) {
							return;
						}
						is_tb_dup_scroll = true;
						
						//console.log(data);
						var out = [];
						
						$.ajax({
							url: web_base_url + "/APIs/facescan.get.meeting.register.dup.php",
							method: "POST",
							data: {
								"id": $("#id").val(),
								"server": server,
								"start": data.start,
								"length": data.length
							},
							success: function($data){
								$(".dup_count").html($data.count);
								
								var index = data.start + 1;
								$.each($data.data, function($key, $row) {
									out.push([
										`<div class="text-center">${index}</div>`,
										`<img src="${$row.member_pic}" class="btn_img img-responsive" alt="">`,
										`<div class="text-center">${$row.member_id}</div>`,
										$row.fullname,
										`<div class="text-center">${$row.create_time}</div>`,
										`<div class="text-center">${$row.regis_type}</div>`,
										`<div class="text-center"><div class="btn_status label ` + ($row.dup_status == 1 ? 'label-success' : 'label-danger') + `" data-id="${$row.id}" data-status="${$row.dup_status}">${$row.dup_status_text}</div></div>`
									]);
									index++;
								});
								
								setTimeout( function () {
									callback( {
										draw: data.draw,
										data: out,
										recordsTotal: $data.count,
										recordsFiltered: $data.count
									} );
									
									is_tb_dup_scroll = false;
								}, 50 );
							}
						});
					},
					deferRender: true,
					responsive: true,
					scroller: true,
					scrollY: 80,
					scrollCollapse: true,
					paging: true,
					info: false,
					ordering: false,
					searching: false
				});
			}
		}
		tb_dup_scroll();
		setInterval(function() { tb_dup.ajax.reload(null, false); }, 1000);
		
		$("body").on("click", ".btn_card_tail", function() {
			$("#mct_meeting_regis_id").val($(this).data("id"));
			$("#mct_card_tail").val($(this).data("val"));
			$("#modal_card_tail").modal('show');
			return false;
		});
		
		$("body").on("click", ".mct_btn_save", function() {
			$.ajax({
				url: base_url + "/meeting/set_card_tail",
				method: "POST",
				data: {
					"id": $("#mct_meeting_regis_id").val(),
					"card_tail": $("#mct_card_tail").val()
				},
				success: function(msg){
					data = jQuery.parseJSON(msg);
					tb_reg.ajax.reload(null, false);
				}
			});
		});
		
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
					tb_dup.ajax.reload(null, false);
				}
			});
		});
		
		$("body").on("click", ".btn_img", function() {
			$("#modal_img .img_preview").prop("src", $(this).prop("src"));
			$('#modal_img').modal('show');
		});
		
	});
</script>