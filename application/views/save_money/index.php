<div class="layout-content">
	<div class="layout-content-body">
		<style>
			.form-group {
				margin-bottom: 1em;
			}

			.border1 {
				border: solid 1px #ccc;
				padding: 0 15px;
			}

			.mem_pic {
				margin-top: -1em;
				float: right;
				width: 150px;
			}

			.mem_pic img {
				width: 100%;
				border: solid 1px #ccc;
			}

			.mem_pic button {
				display: block;
				width: 100%;
			}

			.modal-backdrop.in {
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

			.font-normal {
				font-weight: normal;
			}

			.font-normal2 {
				font-weight: bold;
				font-size: 20px;
			}

			.font-normal3 {
				font-weight: bold;
				font-size: 16px;
			}

			input::-webkit-outer-spin-button,
			input::-webkit-inner-spin-button {
				-webkit-appearance: none;
				margin: 0;
			}

			.btn_deposit {
				margin-right: 5px;
			}

			.alert-success {
				background-color: #DBF6D3;
				border-color: #AED4A5;
				color: #569745;
				font-size: 14px;
			}

			.alert-danger {
				background-color: #F2DEDE;
				border-color: #e0b1b8;
				color: #B94A48;
			}

			.alert {
				border-radius: 0;
				-webkit-border-radius: 0;
				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.11);
				display: table;
				width: 100%;
			}

			.modal-header-deposit {
				padding: 9px 15px;
				border: 1px solid #ff7534;
				background-color: #ff7534;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}

			.modal-header-withdrawal {
				padding: 9px 15px;
				border: 1px solid #d50000;
				background-color: #d50000;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}

			.modal-dialog-account {
				margin: 0 auto;
				margin-top: 10%;
			}

			.modal-dialog-print {
				margin: 0 auto;
				margin-top: 15%;
				width: 350px;
			}

			.center {
				text-align: center;
			}

			th, td {
				text-align: center;
			}

			a {
				text-decoration: none !important;
			}

			a:hover {
				color: #075580;
			}

			a:active {
				color: #757575;
			}

			.bg-table {
				background-color: #ff7534;
				border-color: #ff7534;
				color: #fff;
			}

			.modal-dialog-delete {
				margin: 0 auto;
				width: 350px;
				margin-top: 8%;
			}

			.modal-dialog-add {
				margin: 0 auto;
				width: 60%;
				margin-top: 5%;
			}

			#add_account {
				z-index: 5100 !important;
			}

			#search_member_add_modal {
				z-index: 5200 !important;
			}

			@media (min-width: 768px) {
				.modal-dialog {
					width: 700px;
				}
			}
			#show_wd_list {
       			display: none;
  		 	}
			#show_wd_list.active{
      			display: inherit;
   			 }

			.pay_type_transfer_content{
				border: 1px solid #cccccc;
				border-radius: 4px;
				padding: 8px 16px 8px 9px;
			}

		</style>
		<h1 style="margin-bottom: 0">จัดการบัญชี</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 padding-l-r-0">
				<a class="link-line-none" onclick="add_account('','')">
					<button class="btn btn-primary btn-lg bt-add" type="button">
						<span class="icon icon-plus-circle"></span>
						เปิดบัญชีใหม่
					</button>
				</a>
				<!--a class="link-line-none" href="?act=account">
                    <button class="btn btn-primary btn-lg bt-add" type="button" style="margin-right:20px;">
                    <i class="fa fa-money" aria-hidden="true"></i>
                        บัญชีเงินฝาก
                    </button>
                </a-->
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">

					<div class="row">
						<div class="col-sm-8">
							<!--<div class="input-with-icon">
							<input class="form-control input-thick pill m-b-2" type="text" placeholder="ค้นหา" name="search_text" id="search_text">
							<span class="icon icon-search input-icon"></span>
							</div>
							-->
							<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select id="search_list" name="search_list" class="form-control m-b-1">
										<option value="">เลือกรูปแบบค้นหา</option>
										<option value="member_id" selected>รหัสสมาชิก</option>
										<option value="id_card">หมายเลขบัตรประชาชน</option>
										<option value="firstname_th">ชื่อสมาชิก</option>
										<option value="lastname_th">นามสกุล</option>
										<option value="account_id">หมายเลขบัญชี</option>
									</select>
								</div>
							</div>

							<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
							<div class="col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="search_text" name="search_text" class="form-control m-b-1"
											   type="text" value="<?php echo @$data['id_card']; ?>">
										<span class="input-group-btn">
											<button type="button" onclick="check_search();"
													class="btn btn-info btn-search"><span
													class="icon icon-search"></span></button>
										</span>
									</div>
								</div>
							</div>
						</div>

						<div class="col-sm-4 text-right">
							<p>จำนวนบัญชีเงินฝากทั้งหมด <?php echo number_format($num_rows); ?> บัญชี</p>
						</div>
					</div>

					<div class="bs-example" data-example-id="striped-table">
						<div id="tb_wrap">
							<table class="table table-bordered table-striped table-center">
								<thead>
								<tr class='bg-primary'>
									<th>ลำดับ</th>
									<th>ประเภทบัญชี</th>
									<th>เลขบัญชี</th>
									<th>ชื่อบัญชี</th>
									<th>รหัสสมาชิก</th>
									<th>ชื่อ - นามสกุล</th>
									<th>วันที่เปิดบัญชี</th>
									<th>สถานะ</th>
									<th>จัดการ</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$account_status = array('0' => 'ใช้งาน', '1' => 'ไม่ใช้งาน');
								foreach ($data as $key => $row) { ?>
									<tr>
										<td><?php echo $i++; ?></td>
										<td><?php echo $row['type_code']; ?></td>
										<td>
											<a href="<?php echo base_url(PROJECTPATH . '/save_money/account_detail?account_id=' . $row['account_id']); ?>">
												<?php echo $this->center_function->format_account_number($row['account_id']); ?>
											</a></td>
										<td style="text-align:left"><?php echo $row['account_name']; ?></td>
										<td><?php echo $row['mem_id']; ?></td>
										<td style="text-align:left"><?php echo $row['member_name']; ?></td>
										<td><?php echo $this->center_function->ConvertToThaiDate($row['created']); ?></td>
										<td><?php echo $account_status[$row['account_status']]; ?></td>
										<td>
											<?php if ($row['account_status'] == '0') { ?>
												<a onclick="add_account('<?php echo @$row["account_id"]; ?>','<?php echo $row['mem_id']; ?>','<?php echo $row['type_id']; ?>')"
												   style="cursor:pointer;"> แก้ไข </a> |
												<a class="text-del"
												   onclick="close_account('<?php echo @$row["account_id"]; ?>')">ปิดบัญชี</a>
											<?php } ?>
										</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div id="page_wrap">
					<?php echo $paging ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="add_account" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-add">
		<div class="modal-content">
			<div class="modal-header modal-header-info">
				<h2 class="modal-title">บัญชีเงินฝาก</h2>
			</div>
			<div class="modal-body" id="add_account_space">

			</div>
		</div>
	</div>
</div>
<div class="modal modal_in_modal fade" id="search_member_add_modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">ข้อมูลสมาชิก</h4>
			</div>
			<div class="modal-body">
				<div class="input-with-icon">
					<div class="row">
						<div class="col">
							<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select id="member_search_list" name="member_search_list"
											class="form-control m-b-1">
										<option value="">เลือกรูปแบบค้นหา</option>
										<option value="member_id">รหัสสมาชิก</option>
										<option value="id_card">หมายเลขบัตรประชาชน</option>
										<option value="firstname_th">ชื่อสมาชิก</option>
										<option value="lastname_th">นามสกุล</option>
									</select>
								</div>
							</div>
							<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
							<div class="col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="member_search_text" name="member_search_text"
											   class="form-control m-b-1" type="text"
											   value="<?php echo @$data['id_card']; ?>" onkeypress="check_search_add();">
										<span class="input-group-btn">
									<button type="button" id="member_search" class="btn btn-info btn-search"><span
											class="icon icon-search"></span></button>
								</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="bs-example" data-example-id="striped-table">
					<table class="table table-striped">
						<tbody id="result_add">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
			</div>
		</div>
	</div>
</div>
<div id="close_account" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-add" style="width:40% !important;">
		<div class="modal-content">
			<div class="modal-header modal-header-info">
				<h2 class="modal-title">ปิดบัญชี</h2>
			</div>
			<div class="modal-body">
				<div class="g24-col-sm-24 ">
					<form data-toggle="validator" novalidate="novalidate"
						  action="<?php echo base_url(PROJECTPATH . '/save_money/close_account'); ?>" method="post"  id="frm_close">
						<input type="hidden" name="account_date_open" id="account_date_open">
						<input type="hidden" name="account_id" id="close_account_id">
						<input type="hidden" name="interest_rate" id="close_interest_rate">	
						<input type="hidden" name="cal_type_id" id="cal_type_id">
						<!-- <div class="form-group">
							<?php foreach ($data as $key => $row) { ?>
								<?php if ($key == ''){?>
							<div class="g24-col-sm-24 m-b-1" style="text-align:center !important;">
								<label class="control-label">
										<?php echo substr($row['account_id'], 0, 3) . "-" . substr($row['account_id'], 3, 2) . "-" . substr($row['account_id'], 5, 5) . "-" . substr($row['account_id'], 10); ?>
									</label>
							</div>
							<?php } ?>
							<?php } ?>
						</div> -->
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> เลขบัญชี </label>
								<div class="g24-col-sm-11">
									<p id="close_account_id_view" class="form-control-static"></p>
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> ชื่อบัญชี </label>
								<div class="g24-col-sm-11">
									<p id="close_account_name" class="form-control-static"></p>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="g24-col-sm-24 m-b-1" style="text-align:center !important;">
								<label class="control-label"> จำนวนเงินที่จะได้รับ </label>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> ยอดปัจจุบัน </label>
								<div class="g24-col-sm-11">
									<input type="text" class="form-control" name="close_account_principal"
										   id="close_account_principal" value="" readonly>
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1">&nbsp;</label>
								<div class="g24-col-sm-11">
									<input type="checkbox" name="is_ignore_interest_return" id="is_ignore_interest_return" value="1"> คิดดอกเบี้ยเต็ม ไม่หักส่วนสหกรณ์
									<input type="hidden" id="tmp_close_account_interest_return" value="">
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label for="money" class="control-label g24-col-sm-7"></label>
								<div class="g24-col-sm-4" style="margin-bottom: 5px;padding-top: 5px;">
									<input type="checkbox" name="is_custom_date_close" id="is_custom_date_close">
									ระบุวันที่
								</div>
								<div class="g24-col-sm-7" style="margin-bottom: 5px;padding-top: 5px;">
									<div class="input-with-icon g24-col-sm-24">
										<div class="form-group">
											<input id="date_close_tmp" name="date_close_tmp" class="form-control m-b-1" style="padding-left: 50px;" type="text" data-date-language="th-th"  title="กรุณาป้อน วันที่" disabled>
											<span class="icon icon-calendar input-icon m-f-1"></span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> ดอกเบี้ย </label>
								<div class="g24-col-sm-11">
									<input type="text" class="form-control cal_close_account_total" name="close_account_interest" id="close_account_interest" value="">
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> คืนดอกเบี้ย </label>
								<div class="g24-col-sm-11">
									<input type="text" class="form-control cal_close_account_total" name="close_account_interest_return" id="close_account_interest_return" value="">
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> ยอดปิดบัญชี </label>
								<div class="g24-col-sm-11">
									<p id="close_account_total" class="form-control-static"></p>
								</div>
							</div>
						</div>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label g24-col-sm-7 m-b-1"> เลือกการรับเงิน </label>
								<div class="g24-col-sm-11">
									<input type="radio" name="pay_type" value="0" checked> เงินสด 
									<input type="radio" name="pay_type" value="1"> โอนเงิน
								</div>
							</div>
						</div>
						
						<div class="g24-col-sm-24"  id="show_wd_list" >
							<div class="form-group" >
								<label class="control-label g24-col-sm-6"></label>
								<div class="g24-col-sm-18 pay_type_transfer_content" style="margin-bottom: 5px;padding-top: 5px;">
									<div class="row m-b-1">
										<div class="form-group">
											<label class="control-label g24-col-sm-8" for="c_transfer_bank_id">ธนาคาร :</label>
											<select class="form-control g24-col-sm-16" id="c_transfer_bank_id" name="transfer_bank_id" onchange="change_bank('c_transfer_bank_id','c_transfer_bank_branch_id','c_transfer_acc_num','c_transfer_bank_branch_name')">
												<option value="">-- เลือกธนาคาร --</option>
												<?php
													foreach( $bank_info  as $k => $v) {
														echo sprintf('<option value="%s">%s (%s)</option>', $v['bank_id'], $v['bank_name'], $v['bank_code']);
													}
												?>
											</select>
										</div>
									</div>
									<div class="row m-b-1">
										<div class="form-group">
											<label class="control-label g24-col-sm-8" for="c_transfer_bank_branch_name">สาขา :</label>
											<select class="form-control g24-col-sm-16" id="c_transfer_bank_branch_id" name="transfer_bank_branch_id" onchange="change_bank_branch('c_transfer_bank_branch_id','c_transfer_acc_num','c_transfer_bank_branch_name')">
													<option value="">-- เลือกสาขาธนาคาร --</option>
												</select>
											<input type="hidden" class="form-control g24-col-sm-16" id="c_transfer_bank_branch_name" name="transfer_bank_branch_name" value="" placeholder="ระบุสาขาธนาคาร" />
										</div>
									</div>
									<div class="row m-b-1">
										<div class="form-group">
											<label class="control-label g24-col-sm-8" for="c_transfer_acc_num">เลขที่บัญชี :</label>
											<input class="form-control g24-col-sm-16" name="transfer_acc_num" id="c_transfer_acc_num" placeholder="ระบุเลขที่บัญชี"/>
										</div>
									</div>	
								</div>
                            </div>
							<label class="control-label g24-col-sm-4">&nbsp;</label>
						</div>

						<div class="row m-b-1">
							<div class="form-group">
								<div class="g24-col-sm-24" style="text-align:center">
									<!--<button type="submit" class="btn btn-primary min-width-100">ยืนยัน</button>-->
									<button type="button" id="bt_save_close" class="btn btn-primary min-width-100" onclick="check_close()">ยืนยัน</button>
									<button class="btn btn-danger min-width-100" type="button"
											onclick="close_modal('close_account');"> ยกเลิก
									</button>
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

<!--  MODAL custom_date_close_modal-->
<div id="custom_date_close_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-sm" style="width:300px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">ยืนยันสิทธิ์การทำรายการปิดบัญชีแบบกำหนดวันที่</h4>
			</div>
			<div class="modal-body">
				<p>ชื่อผู้มีสิทธิ์อนุมัติ</p>
				<input type="text" class="form-control" id="confirm_user_close">
				<p>รหัสผ่าน</p>
				<input type="password" class="form-control" id="confirm_pwd_close">
				<br>
				<!-- <input type="hidden" id="transaction_id_err" value=""> -->
				<div class="row">
					<div class="col-sm-12 text-center">
						<button class="btn btn-info" id="submit_confirm_close">ยืนยัน</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--  MODAL custom_date_close_modal-->

<script>
	function removeCommas(str) {
		return(str.replace(/,/g,''));
	}
	
	function add_account(account_id, member_id,type_id) {
		$.ajax({
			url: base_url + "/save_money/add_save_money",
			method: "post",
			data: {account_id: account_id, member_id: member_id,type_id:type_id},
			dataType: "text",
			success: function (data) {
				$('#add_account_space').html(data);
				if ($('#sequester_status_2').is(':checked')) {
					$('.show_sequester_amount').show();
				}
				$('#add_account').modal('show');
				change_account_type();
			}
		});

	}

	function change_type() {
		if ($('#sequester_status_2').is(':checked')) {
			$('.show_sequester_amount').show();
		} else {
			$('#sequester_amount').val('0');
			$('.show_sequester_amount').hide();
		}
		check_remark();
	}

	function check_remark(){
		var sequester_status = $('input[name=sequester_status]:checked', '#frm1').val();
		var sequester_status_atm = $('input[name=sequester_status_atm]:checked', '#frm1').val();
		if((sequester_status != 0 || sequester_status_atm != 0) && !$("input[name='sequester_status_atm']").is(':disabled')){
			$('#div_remark').show();
		}else{
			$('#div_remark').hide();
		}
	}

	function get_data(member_id, member_name) {
		$('#member_id_add').val(member_id);
		$('#member_name_add').val(member_name);
		$('#acc_name_add').val(member_name);
		$('#acc_name_add').removeAttr('readonly');
		$('#account_name_eng').val("");
		$.post(base_url + "ajax/get_member",
			{
				member_id: member_id
			}
			, function (result) {
				obj = JSON.parse(result);
				create_option_account_transfer(obj.account_list_transfer);
				console.log(obj);
				if (obj.firstname_en) {
					$('#account_name_eng').val(obj.firstname_en + ' ' + obj.lastname_en);
				} else {
					$('#account_name_eng').val("");
				}
			});
		$('#account_name_eng').removeAttr('readonly');
		$('#type_id').removeAttr('readonly');
		$('#search_member_add_modal').modal('hide');
	}

	function delete_account(account_id) {
		swal({
				title: "ท่านต้องการลบบัญชีใช่หรือไม่?",
				text: "",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'ยืนยัน',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: false,
				closeOnCancel: true
			},
			function (isConfirm) {
				if (isConfirm) {
					$.ajax({
						type: "POST",
						url: base_url + "/save_money/check_account_delete",
						data: {account_id: account_id},
						success: function (msg) {
							if (msg == 'success') {
								document.location.href = base_url + '/save_money/delete_account/' + account_id;
							} else {
								swal('ไม่สามารถลบข้อมูลบัญชีได้', 'เนื่องจากมียอดเงินคงเหลือในบัญชี', 'warning');
							}
						}
					});
				} else {

				}
			});
	}

	function close_account(account_id,date_close='') {
		if(date_close==''){
			$('#is_custom_date_close').prop('checked', false);
			$("#date_close_tmp").prop('disabled', true);
			$("#date_close_tmp").val('');
		}
		/*swal({
            title: "ท่านต้องการปิดบัญชีใช่หรือไม่?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                document.location.href = base_url+'/save_money/close_account/'+account_id;
            } else {

            }
        });*/
		$.ajax({
			type: "POST",
			url: base_url + "save_money/close_account_calculate",
			data: {account_id: account_id,date_close:date_close},
			success: function (msg) {
				var obj = JSON.parse(msg);
				if (obj.text_alert != '') {
					swal('', obj.text_alert, 'warning');
				} else {
					$('#account_date_open').val(obj.account_date_open);
					$('#close_account_interest').val(obj.interest);
					$('#close_interest_rate').val(obj.interest_rate);
					$('#cal_type_id').val(obj.cal_type_id);
					$('#close_account_interest_return').val(obj.interest_return);
					$('#tmp_close_account_interest_return').val(obj.interest_return);
					$('#close_account_principal').val(obj.principal);
					$('#close_account_id').val(account_id);
					$('#close_account_id_view').html(account_id);
					$('#close_account_name').html(obj.close_account_name);
					$("#close_account_interest").trigger("keyup");
					$('#close_account').modal('show');
				}
			}
		});

	}

	$(function () {
		$("#search_member_add").keyup(function () {
			$.ajax({
				type: "POST",
				url: base_url + "/ajax/search_member_jquery",
				data: {search: $("#search_member_add").val()},
				success: function (msg) {
					$("#result_add").html(msg);
				}
			});
		});

		
        $("#search_text").keyup(function(e) {
			var code = e.which; // recommended to use e.which, it's normalized across browsers
			if(code==13)e.preventDefault();
			if(code==32||code==13||code==188||code==186){
				check_search();
				// $.ajax({
                //     type: "POST",
                //     url: base_url+"/ajax/search_account",
                //     data: "search_text=" + $("#search_text").val(),
                //     success: function(msg) {
                //         $("#tb_wrap").html(msg);
                //         $("#page_wrap").css("display", $("#search_text").val() == "" ? "block" : "none");
                //     }
                // });
			} 
                // $.ajax({
                //     type: "POST",
                //     url: base_url+"/ajax/search_account",
                //     data: "search_text=" + $("#search_text").val(),
                //     success: function(msg) {
                //         $("#tb_wrap").html(msg);
                //         $("#page_wrap").css("display", $("#search_text").val() == "" ? "block" : "none");
                //     }
                // });
            });
        
		$("#is_ignore_interest_return").click(function() {
			if($(this).prop("checked")) {
				$("#close_account_interest_return").val("");
				$("#close_account_interest_return").prop("readonly", true);
			}
			else {
				$("#close_account_interest_return").val($("#tmp_close_account_interest_return").val());
				$("#close_account_interest_return").prop("readonly", false);
			}
			
			$(".cal_close_account_total").trigger("keyup");
		});
		
		$(".cal_close_account_total").keyup(function(e) {
			var close_account_principal = isNaN(parseFloat($("#close_account_principal").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_principal").val().replace(/,/g, ""));
			var close_account_interest = isNaN(parseFloat($("#close_account_interest").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_interest").val().replace(/,/g, ""));
			var close_account_interest_return = isNaN(parseFloat($("#close_account_interest_return").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_interest_return").val().replace(/,/g, ""));
			$("#close_account_total").html($().number_format(close_account_principal + close_account_interest - close_account_interest_return, { numberOfDecimals: 2, decimalSeparator: '.', thousandSeparator: ',' }));
		});
	});

	function check_submit() {
		var text_alert = '';
		if ($('#member_id_add').val() == '') {
			text_alert += '- รหัสสมาชิก\n';
		}
		if ($('#acc_name_add').val() == '') {
			text_alert += '- ชื่อบัญชี\n';
		}
		if ($('#type_id').val() == '') {
			text_alert += '- ประเภทบัญชี\n';
		}

		if($('#min_first_deposit').val()==''){
			if($('#min_first_deposit').is('[readonly]')==false){
				text_alert += '- ระบุยอดเงินเปิดบัญชี\n';
			}	
		}
		var sequester_status = $('input[name=sequester_status]:checked', '#frm1').val();
		var sequester_status_atm = $('input[name=sequester_status_atm]:checked', '#frm1').val();
		console.log("11 ",$("#remark").val());
		console.log($("input[name='sequester_status_atm']").is(':disabled'));
		if((sequester_status != 0 || sequester_status_atm != 0) 
			&& $("#remark").val()==""
			&& !$("input[name='sequester_status_atm']").is(':disabled')
		){
			text_alert += '- ระบุสาเหตุการอายัดบัญชีด้วย\n';
		}

		if($('#acc_id').val()!=undefined){
			var tmp = $('#acc_id').val();
			acc_id = tmp.replace(/-/g, '');
		}else{
			var tmp = $('#acc_id_yourself').val();
			acc_id = tmp.replace(/-/g, '');
		}
		$.ajax({
			type: "POST",
			url: base_url + "/save_money/check_account_save",
			data: {
				atm_number: $('#atm_number').val(),
				member_id: $('#member_id_add').val(),
				account_id: acc_id,
				old_account_no: ($("#old_account_no").val()!=undefined) ? $("#old_account_no").val() : "",
				type_id: $('#type_id').val(),
				unique_account: $('#type_id :selected').attr('unique_account'),
				min_first_deposit: removeCommas($('#min_first_deposit').val()),
				action_type: $('#action_type').val()
			},
			success: function (msg) {
				var obj = JSON.parse(msg);
				console.log(obj);
				if (obj.acc_number == 'dupplicate_account_no' && ($("#acc_id").val()=="" || $("#acc_id").val()==undefined) ) {
					text_alert += '- มีเลขที่บัญชี ซ้ำในระบบ\n';
				}
				if (obj.atm_number == 'dupplicate') {
					text_alert += '- มีเลขบัตร ATM ซ้ำในระบบ\n';
				}
				if (obj.unique_account == 'dupplicate') {
					if(obj.account_status == '0'){
						text_alert += '- ประเภทบัญชีที่ท่านเลือกมีได้เพียงบัญชีเดียว\n';
					}
				}

				if (obj.error != '') {
					text_alert += obj.error+'\n';
				}
				
				if (text_alert != '') {
					swal('กรุณากรอกข้อมูลต่อไปนี้', text_alert, 'warning');
				} else {
					if($('#acc_id_yourself').val()!=undefined){
						var tmp = $('#acc_id_yourself').val();
						acc_id = tmp.replace(/-/g, '');
						$('#acc_id_yourself').val(acc_id);
					}
					$('#frm1').submit();
				}
			}
		});
	}

	function change_account_type() {
		if ($('#type_id :selected').attr('type_code') == $('#main_type_code').val() || $('#type_code').val() == $('#main_type_code').val() && $('#atm_online_status').val() == true) {	
			$('#atm_space').show();
			$('.check_account_atm').show();
		} else {
			$('#atm_number').val('');
			$('#atm_space').hide();
			$('.check_account_atm').hide();
		}
	}

	function check_search() {
		if ($('#search_list').val() == '') {
			swal('กรุณาเลือกรูปแบบค้นหา', '', 'warning');
		} else if ($('#search_text').val() == '') {
			swal('กรุณากรอกข้อมูลที่ต้องการค้นหา', '', 'warning');
		} else {
			var tmp = $('#search_text').val().split('-');
			var search_text = tmp.join('');
			$.ajax({
				type: "POST",
				url: base_url + "/ajax/search_account",
				data: {
					search_text: search_text,
					search_list: $('#search_list').val()
				},
				success: function (msg) {
					$("#tb_wrap").html(msg);
					$("#page_wrap").css("display", $("#search_text").val() == "" ? "block" : "none");
				}
			});
		}
	}

	function check_member_id() {
		var member_id = $('#member_id_add').val();
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if (keycode == '13') {
			$.post(base_url + "ajax/get_member",
				{
					member_id: member_id
				}
				, function (result) {
					obj = JSON.parse(result);
					if (obj.member_id && obj.member_name) {
						get_data(obj.member_id, obj.member_name)
						if (obj.firstname_en) {
							$('#account_name_eng').val(obj.firstname_en + ' ' + obj.lastname_en);
						} else {
							$('#account_name_eng').val("");
						}
					} else {
						swal('ไม่พบรหัสสมาชิกที่ท่านเลือก', '', 'warning');
					}
				});
		}
	}
	//หน้าเปิดบัญชีกดค้นหาโดยใช้enter
	function check_search_add() {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if (keycode == '13') {	
			if ($('#member_search_list').val() == '') {
			swal('กรุณาเลือกรูปแบบค้นหา', '', 'warning');
		} else if ($('#member_search_text').val() == '') {
			swal('กรุณากรอกข้อมูลที่ต้องการค้นหา', '', 'warning');
		} else {
			$.ajax({
				url: base_url + "ajax/search_member_by_type_jquery",
				method: "post",
				data: {
					search_text: $('#member_search_text').val(),
					search_list: $('#member_search_list').val()
				},
				dataType: "text",
				success: function (data) {
					$('#result_add').html(data);
				},
				error: function (xhr) {
					console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
				}
			});
		}
		}
	}
	//หน้าเปิดบัญชีกดค้นหาโดยใช้การคลิก
	$('#member_search').click(function () {
		if ($('#member_search_list').val() == '') {
			swal('กรุณาเลือกรูปแบบค้นหา', '', 'warning');
		} else if ($('#member_search_text').val() == '') {
			swal('กรุณากรอกข้อมูลที่ต้องการค้นหา', '', 'warning');
		} else {
			$.ajax({
				url: base_url + "ajax/search_member_by_type_jquery",
				method: "post",
				data: {
					search_text: $('#member_search_text').val(),
					search_list: $('#member_search_list').val()
				},
				dataType: "text",
				success: function (data) {
					$('#result_add').html(data);
				},
				error: function (xhr) {
					console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
				}
			});
		}
	});

	function close_modal(id) {
		$('#' + id).modal('hide');
	}

	function click_bt_yourself() {
		$('.show_acc_id_yourself').show();
	}

	function create_option_account_transfer(data){
		console.log(data);
		$('#account_transfer')
			.find('option')
			.remove()
			.end()
			.append('<option value="">เลือกบัญชีคู่โอน</option>')
			.val('')
		;
		// account_transfer
		$.each(data, function(key, value) {   
			$('#account_transfer')
				.append($("<option></option>")
							.attr("value",value.id)
							.text(value.text)); 
		});
	}
	$(function(){
		$("#date_close_tmp").datepicker({
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

		$("#is_custom_date_close" ).change(function () {
			console.log("hi", $(this).is(":checked"));
			if($(this).is(":checked")){
				$('#custom_date_close_modal').modal("show");
			}else{
				$('#custom_date_close_modal').modal("hide");
				$("#date_close_tmp").prop('disabled', true);
			}
		});

		$("#date_close_tmp" ).change(function () {
			console.log("date_close_tmp", $(this).val());
			$("#date_close").val($(this).val());
			var date_close = $(this).val();
			var account_id = $("#close_account_id_view").html();
			var account_date_open = $("#account_date_open").val();
			var date_close_eng_arr = date_close.split('/');
			var date_close_eng = (parseInt(date_close_eng_arr[2])-543)+'-'+date_close_eng_arr[1]+'-'+date_close_eng_arr[0];
			
			if(date_close_eng <= account_date_open){
				swal('สามารถปิดบัญชีได้ในวันพรุ่งนี้');
				$('#close_account').modal('hide');
				return false;
			}
			
			close_account(account_id,date_close);
		});
		
		$("#submit_confirm_close").on('click', function (){
			var confirm_user = $('#confirm_user_close').val();
			var confirm_pwd = $('#confirm_pwd_close').val();	
			$.ajax({
					method: 'POST',
					url: base_url+'save_money/authen_confirm_user',
					data: {
						confirm_user : confirm_user,
						confirm_pwd : confirm_pwd,
						permission_id : 231
					},
					dataType: 'json',
					success: function(data){
						console.log(data);
						if(data.result=="true"){
							
							if(data.permission=="true"){
								$("#date_close_tmp").prop('disabled', false);
								$('#custom_date_close_modal').modal('hide');
							}else{
								swal("ไม่มีสิทธิ์ทำรายการ");
								$("#date_close_tmp").prop('disabled', true);
							}
						}else{
							swal("ตรวจสอบข้อมูลให้ถูกต้อง");
						}
					}
			});
		});
	});

	function chkNumber(ele){
		var value = $('#'+ele.id).val();
		value = value.replace(/[^0-9]/g, '');	
		if(value!=''){
			if(value == 'NaN'){
				$('#'+ele.id).val('');
			}else{		
				value = value.toLocaleString();
				$('#'+ele.id).val(value);
			}			
		}else{
			$('#'+ele.id).val('');
		}
	}
	
	function check_close() {
		
		var date_close =$("#date_close_tmp").val();
		var account_id = $("#close_account_id_view").html();
		var account_date_open = $("#account_date_open").val();
		if(date_close!=''){
			var date_close_eng_arr = date_close.split('/');
			var date_close_eng = (parseInt(date_close_eng_arr[2])-543)+'-'+date_close_eng_arr[1]+'-'+date_close_eng_arr[0];
		}else{
			var date_close_eng = moment().format('YYYY-MM-DD');
		}
		//เช็คการรับเงินหลังจากการกดปิดบัญชี
		var pay_type_check = $('input[name=pay_type]:checked', '#frm_close').val();
		var close_account_principal = isNaN(parseFloat($("#close_account_principal").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_principal").val().replace(/,/g, ""));
		var close_account_interest = isNaN(parseFloat($("#close_account_interest").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_interest").val().replace(/,/g, ""));
		var close_account_interest_return = isNaN(parseFloat($("#close_account_interest_return").val().replace(/,/g, ""))) ? 0 : parseFloat($("#close_account_interest_return").val().replace(/,/g, ""));
		var close_total=close_account_principal+close_account_interest+close_account_interest_return;
		// var wd_list = isNaN(parseFloat($("#wd_list").val().replace(/,/g, ""))) ? 0 : parseFloat($("#wd_list").val().replace(/,/g, ""));
		// 	if(pay_type_check == 1){
		// 		if(close_total != wd_list){
		// 			swal("กรุณากรอกจำนวนเงินแยกรายการโอนให้ถูกต้อง");
		// 		}else{
		// 			if(date_close_eng <= account_date_open){
		// 				swal('สามารถปิดบัญชีได้ในวันพรุ่งนี้');
		// 				$('#close_account').modal('hide');
		// 			}else{
		// 				$('#frm_close').submit();
		// 			}
		// 		}
		// 	}else{
		// 		if(date_close_eng <= account_date_open){
		// 				swal('สามารถปิดบัญชีได้ในวันพรุ่งนี้');
		// 				$('#close_account').modal('hide');
		// 			}else{
		// 				$('#frm_close').submit();
		// 			}	
		// 	}
		if(date_close_eng <= account_date_open){
			swal('สามารถปิดบัญชีได้ในวันพรุ่งนี้');
			$('#close_account').modal('hide');
		}else{
			$('#frm_close').submit();
		}	
	
	}
	//เลือกการรับเงินแบบโอน
	$(document).on("change", "input[name='pay_type']", function(e){
     
		
			if($(this).val() === "1"){;
				$("#show_wd_list").addClass("active");		
			}else if($(this).val() === "0"){;
				$("#show_wd_list").removeClass("active");
			}	
    })
	//เพิ่มรายการโอนออก
	$('#addrow').click(function(e) {
		var tr=$('#myTable tbody tr:last').clone();
		var t_cell1 = parseInt($(".cell1:last").text());	
		var i=	document.getElementsByClassName('withdraw_list_wd').length;
		var y=	document.getElementsByClassName('alert_wd').length;
		var t_cell3 =('withdraw_list'+ ++i); //เปลี่ยนattr_id
		var alert =('alert'+ ++y);
		tr.find('.cell1').html(t_cell1+1);
		tr.find('.withdraw_list_wd').attr('id',t_cell3);
		tr.find('.alert_wd').attr('id',alert);
		var clonelast='<tr>'+$(tr).html()+'</tr>';
		$('#myTable tbody').append(clonelast);
	});
	//ลบรายการโอนออก
	$('#removerow').click(function() {
		$("#myTable tbody tr:last").remove()
		$("#wd_list").val(0);
	});
		//เช็คตัวเลขจำนวนเงินโอนออกว่าถูกต้องไหม
	$(document).ready(function() {  
  		$("#myTable").delegate("input","keyup",function() { 
		var wd_id=this.id;
		var al = wd_id.substring(13, 14);
		var total_list = 0;    
			$('.withdraw_list_wd').each(function(){   
				var input_list=removeCommas($(this).val());
				if($.isNumeric(input_list)){
					total_list += parseFloat(input_list);
				}	
					$("#wd_list").val( total_list);
				});
				if($.trim($('#'+wd_id).val()) == '') {
					$('#close_account').find('#alert'+al).show();
				} else {
					$('#close_account').find('#alert'+al).hide();
				}
 		 }); 
		});

	function format_the_number(ele){
		var value_ele = $('#'+ele.id).val();
		console.log(value_ele);	
		value_ele = value_ele.split('.');
		value = value_ele[0].replace(/[^0-9]/g, '');
		console.log(value_ele);	
		console.log(value);	
		if(value!=''){
			if(value == 'NaN'){
				$('#'+ele.id).val('');
			}else{		
				value = parseInt(value);
				value = value.toLocaleString();
				if(value_ele[1] != null){
					value = value+"."+value_ele[1]
				}else{
					value = value;
				}
				$('#'+ele.id).val(value);
			}			
		}else{
			$('#'+ele.id).val('');
		}
	}
	function addCommas(x){
	 	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}	
	function removeCommas(str) {
	return(str.replace(/,/g,''));
	}

	$('#close_account').on('hidden.bs.modal', function (e) {
		$("input[name=pay_type][value='0']").attr("checked",true);
		$("#show_wd_list").removeClass("active");
		$("#c_transfer_bank_id").val('');
		$("#c_transfer_bank_branch_name").val('');
		$("#c_transfer_acc_num").val('');
	})

	function change_bank(bank,branch,acc_num,branch_text){
    	var bank_id = $('#'+bank).val();
		$('#'+acc_num).val('');
		$('#'+branch_text).val('');
		$('#'+branch)
		.empty()
		.append('<option selected="selected" value="">-- เลือกสาขาธนาคาร --</option>');

		$.post( base_url+"/save_money/ajax_get_bank_branch",
			{	
				bank_id: bank_id
			}
			, function(result){
				var obj = JSON.parse(result);
				for(var key in obj){
					$('#'+branch).append($('<option>',{
						value: obj[key].branch_id,
						text: obj[key].branch_name,
						account_bank_number: obj[key].account_bank_number,
						branch_name: obj[key].branch_name
					}));
				}
			});		
	}

	function change_bank_branch(branch,acc_num,branch_text){
		var account_bank_number = $('#'+branch+' :selected').attr('account_bank_number');
		var branch_name = $('#'+branch+' :selected').attr('branch_name');
		$('#'+acc_num).val(account_bank_number);
		$('#'+branch_text).val(branch_name);
	}
</script>
