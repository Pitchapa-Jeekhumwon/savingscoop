<style>
	.btn-control {
		text-align: right;
	}
	.btn-control .btn.btn-lg {
		margin-bottom: 1em;
		margin-top: -1em;
		padding: 6px 12px !important;
		width: auto !important;
    height: auto !important;
	}
	.form-control-static {
    padding-top: 7px;
		line-height: 34px;
	}
</style>
<div class="layout-content">
	<div class="layout-content-body">
		<h1 style="margin-bottom: 0"><?php echo $page_title; ?></h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="row gutter-xs">
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<?php $this->load->view('breadcrumb'); ?>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<div class="btn-control">
						<button type="button" class="btn btn-primary btn-lg" id="btn-transfer">
							<span class="icon icon-money"></span>
							โอนเงิน
						</button>
						<a type="button" class="btn btn-primary btn-lg" href="/meeting/report_reward?id=<?php echo $meeting_id; ?>" target="_blank">
							<span class="icon icon-print"></span>
							พิมพ์รายงาน
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<h1 class="text-center m-t-1 m-b-2">รางวัล</h1>
							<form id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/meeting/reward_save'); ?>" method="post">
								<input id="meeting_id" name="meeting_id"  type="hidden" value="<?php echo $meeting_id; ?>" required>
								<div class="row">
									<label class="col-sm-2 control-label" for="member_id">รหัสสมาชิก</label>
									<div class="col-sm-6">
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control m-b-1" id="member_id" name="member_id" value="" required title="หรือ รหัสสมาชิก" autocomplete="off">
												<span class="input-group-btn">
													<button type="button" class="btn btn-info btn-search" data-toggle="modal" data-target="#myModal"><span class="icon icon-search"></span></button>
												</span>
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<label class="col-sm-2 control-label" for="fullname">ชื่อสมาชิก</label>
									<div class="col-sm-6">
										<div class="form-group">
											<input id="fullname" name="fullname" class="form-control form-r m-b-1" type="text" value="" readonly autocomplete="off">
										</div>
									</div>
									<div class="col-sm-4"><button type="button" class="btn btn-primary min-width-100" id="btn-reward-add">เพิ่มชื่อ</button></div>
								</div>
								<div class="row">
									<label class="col-sm-2 control-label" for="fullname">จำนวนเงิน</label>
									<div class="col-sm-3">
										<div class="form-group">
											<input type="text" class="form-control m-b-1" id="award_amount" name="award_amount" value="" autocomplete="off">
										</div>
									</div>
									<div class="col-sm-4"><span class="form-control-static">บาท</span></div>
								</div>

							</form>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<table class="table table-borderless table-striped m-t-3" id="table-reward">
								<thead>
									<tr>
										<th width="80">ลำดับ</th>
										<th width="100">รหัสสมาชิก</th>
										<th width="250">ชื่อสกุล</th>
										<th width="120">เลขบัญชี</th>
										<th>สถานะ</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>

						</div>
					</div>

				</div>
			</div>


		</div><!-- End panel panel-body  -->
		<div id="reward-paging"></div>
	</div>
</div>
<script>
	var base_url = $('#base_url').attr('class');
	var get_data = function($member_id, $fullname, $ref) {
		$("#member_id").val($member_id);
		load_member_info($member_id);
		$("#myModal").modal("hide");
	}

	var load_member_info = function($member_id) {
		$.ajax({
			url: base_url + "/meeting/get_member",
			method: "POST",
			dataType: "json",
			data: {
				"member_id": $("#member_id").val()
			},
			success: function( $json ){ console.log($json);
				$("#fullname").val($json.fullname);
			}
		});
	}

	var get_table = function() {
		let $remain_transfer = 0;
		$.ajax({
				url: base_url + "/meeting/reward_table",
				method: "POST",
				dataType: "json",
				data: {
					"meeting_id": $("#meeting_id").val()
				},
				success: function( $json ){ console.log( $json );
					$('#table-reward tbody').empty();
					$no = 1;
					$.each($json.data, function($key, $row) {
						$award_status = {
							0: '<span class="text-warning">รอโอนเงิน</span>',
							1: `<span class="text-primary">โอนเงินแล้วเมื่อ ${$row.transfer_time_text}</span>`,
							2: `<span class="text-danger">ไม่พบบัญชีเงินฝาก</span>`
						};
						if( $row.award_status == 0 ) {
							$remain_transfer++;
						}
						$('#table-reward tbody').append(`
							<tr>
								<td class="text-center">${$no++}.</td>
								<td class="text-center">${$row.member_id}</td>
								<td>${$row.firstname_th} ${$row.lastname_th}</td>
								<td class="text-center">${$row.account_no}</td>
								<td class="text-center">${ $award_status[ $row.award_status] }</td>
							</tr>
						`);
					});
					$('#reward-paging').html($json.paging);
					if( $remain_transfer == 0 ) {
						$('#btn-transfer').addClass('disabled');
					} else {
						$('#btn-transfer').removeClass('disabled');
					}
				}
			});
	}

	$(function() {
		get_table();
		$("#member_id").keydown(function(e){
			if($(this).val() != "" && e.keyCode == 13) {
				load_member_info($.trim($(this).val()));
			} else {
				$("#fullname").val( '' );
			}
		});

		$('body').on('click', '#btn-transfer:not(.disabled)', function() {
			$.ajax({
				url: base_url + "/meeting/reward_transfer",
				method: "POST",
				dataType: "json",
				data: {
					"meeting_id": $("#meeting_id").val()
				},
				success: function( $json ) { console.log( $json );
					if( $json.err_code.length == 0 ) {
						swal({
							title: "รางวัล",
							text: 'ทำการโอนเงินเรียบร้อย',
							type: "success",
							confirmButtonColor: '#3a6336',
							confirmButtonText: 'ปิดหน้าต่าง',
							closeOnConfirm: true
						},
						function(isConfirm) {

						});
						get_table();
					} else {
						swal({
									title: "รางวัล",
									text: data.msg,
									type: "warning",
									confirmButtonColor: '#3a6336',
									confirmButtonText: 'ปิดหน้าต่าง',
									closeOnConfirm: true
								},
								function(isConfirm) {

								});
					}
				}
			});
		});
		$('body').on('click', '#btn-reward-add', function() {
			let $is_error = 0;

			if( $.trim($("#member_id").val()).length == 0 ) {
				swal({
					title: "รางวัล",
					text: 'กรุณากรอกรหัสสมาชิก',
					type: "warning",
					confirmButtonColor: '#3a6336',
					confirmButtonText: 'ปิดหน้าต่าง',
					closeOnConfirm: true
				});
			} else if( $.trim($("#award_amount").val()).length == 0 ) {
				swal({
					title: "รางวัล",
					text: 'กรุณากรอกจำนวนเงิน',
					type: "warning",
					confirmButtonColor: '#3a6336',
					confirmButtonText: 'ปิดหน้าต่าง',
					closeOnConfirm: true
				});
			} else {
				$.ajax({
					url: base_url + "/meeting/reward_add",
					method: "POST",
					dataType: "json",
					data: {
						"meeting_id": $("#meeting_id").val(),
						"member_id": $("#member_id").val(),
						"award_amount": $("#award_amount").val(),
					},
					success: function( $json ){
						if( $json.err_code.length == 0 ) {
							$("#member_id").val('');
							$("#fullname").val('');
							get_table();
						} else {
							swal({
										title: "รางวัล",
										text: $json.msg,
										type: "warning",
										confirmButtonColor: '#3a6336',
										confirmButtonText: 'ปิดหน้าต่าง',
										closeOnConfirm: true
									},
									function(isConfirm) {

									});
						}
					}
				});
			}
		});

	});
</script>