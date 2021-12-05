<style>
	
	#pay_type_transfer {
        display: none;
    }
	#pay_type_transfer_note {
        display: none;
    }
  
	.pay_type_transfer_content{
		border: 1px solid #cccccc;
		border-radius: 4px;
		padding: 8px 20px 8px 9px;
	}
</style>
<div class="col-md-12 ">
	<form data-toggle="validator" novalidate="novalidate" id="frm1" action="<?php echo base_url(PROJECTPATH.'/save_money/save_add_save_money'); ?>" method="post">
		<?php
			if($account_id!=''){
				$action_type = 'edit';
			}else{
				$action_type = 'add';
			}
		?>
		<input type="hidden" id="action_type" name="action_type" value="<?php echo $action_type; ?>">
		<input type="hidden" id="main_type_code" name="main_type_code" value="<?php echo $main_type_code; ?>">
		<input type="hidden" id="atm_online_status" name="atm_online_status" value="<?php echo $atm_online_status; ?>">
		<div class="form-group">
			<?php if($row['account_id']!=''){ ?>
			<div class="g24-col-sm-24">
			<label class="col-sm-3 control-label" for="form-control-2"> เลขที่บัญชี </label>
				<div class="col-sm-9">
					<div id="form_acc_id" class="form-group input-group">
						<input type="hidden" id="old_account_no" name="old_account_no">
						<input class="form-control m-b-1 has-success" type="text" id="acc_id" name="acc_id" value="<?php echo empty($row['account_id']) ? '' : $row['account_id']; ?>" required readonly>
						<span class="input-group-btn">
							<a class="" href="#">
								<button id="edit_account_no" type="button" class="btn btn-info btn-search"><span class="icon icon-edit"></span></button>
							</a>
						</span>	
					</div>
					<!-- <input class="form-control m-b-1" type="text" id="acc_id" name="acc_id" value="<?php echo empty($row['account_id']) ? '' : $row['account_id']; ?>" required readonly> -->
				</div>	
			</div>
			<br><br>
			<?php }?>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2">รหัสสมาชิก</label>
				<div class="col-sm-9 m-b-1">
				<!-- กดแก้ไขreadonlyรหัสสมาชิกไว้ -->
					<?php if($action_type=='edit'){?>
								<input value="<?php echo empty($row['account_id']) ? '' : $row['mem_id'] ?>" class="form-control m-b-1" type="text" name="mem_id" id="member_id_add"  required readonly>
					<?php }else{ ?>
						<div class="input-group"> 
						<input value="<?php echo empty($row['account_id']) ? '' : $row['mem_id'] ?>" class="form-control m-b-1" type="text" name="mem_id" id="member_id_add"  required onkeypress="check_member_id();">
						<span class="input-group-btn">
							<a class="" data-toggle="modal" data-target="#search_member_add_modal" href="#">
								<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
							</a>
						</span>	
						</div>
					<?php }	?>
					</div>
				</div>

			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2">ชื่อ - นามสกุล</label>
				<?php
					if($action_type=='add'){
						?>
							<div class="col-sm-6">
								<input value="<?php echo empty($row['account_id']) ? '' : $row['member_name'] ?>" class="form-control m-b-1" type="text" name = "member_name" id="member_name_add"   required readonly>
							</div>
							<div class="col-sm-2">				
								<button id="bt_yourself" name="bt_yourself" type="button" class="btn btn-primary" style="width: auto;" onclick="click_bt_yourself();">กำหนดเลขบัญชีเอง</button>
							</div>		
						<?php
					}else{
						?>
							<div class="col-sm-9">
								<input value="<?php echo empty($row['account_id']) ? '' : $row['member_name'] ?>" class="form-control m-b-1" type="text" name = "member_name" id="member_name_add"   required readonly>
							</div>
								
						<?php
					}
				?>
									
			</div>
			
			<div class="g24-col-sm-24 show_acc_id_yourself" style="display:none;">
				<label class="col-sm-3 control-label" for="form-control-2"> เลขที่บัญชี </label>
				<div class="col-sm-9">
<!--					<input class="form-control m-b-1" type="text" id="acc_id" name="acc_id" value="--><?php //echo $row['account_id'] ? '' : $row['account_id']; ?><!--" required readonly>-->
					<input class="form-control m-b-1" type="text" id="acc_id_yourself" name="acc_id_yourself" value="<?php echo @$row['account_id'] ?>" <?php echo $action_type=='edit'?'':'readonly';?>" required>
				</div>
			</div>
			
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2" require>ชื่อบัญชี</label>
				<div class="col-sm-9">
					<input name="acc_name" class="form-control m-b-1" type="text" id="acc_name_add" value="<?php echo @$row['account_name'] ?>" <?php echo $action_type=='edit'?'':'readonly';?> autofocus>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2" require>ชื่อบัญชีภาษาอังกฤษ</label>
				<div class="col-sm-9">
					<input name="account_name_eng" class="form-control m-b-1" type="text" id="account_name_eng" value="<?php echo @$row['account_name_eng'] ?>" <?php echo $action_type=='edit'?'':'readonly';?> autofocus>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2">ประเภทบัญชี</label>
				<div class="col-sm-9">
					<?php if($action_type=='edit'){ ?>
						<input class="form-control m-b-1" type="text" value="<?php echo @$row['type_name'] ?>" readonly>
						<input type="hidden" id="type_id" name="type_id" value="<?php echo @$row['type_id'] ?>">
						<input type="hidden" id="type_code" name="type_code" value="<?php echo @$row['type_code'] ?>">
					<?php } else{ ?>
					<select class="form-control type_select m-b-1" id="type_id"  name="type_id" <?php echo $action_type=='edit'?'':'readonly';?>  require>
						<option value="">เลือกประเภทบัญชี</option>
						<?php foreach($type_id as $key => $value){ ?>
							<option value="<?php echo $value['type_id']; ?>" unique_account="<?php echo $value['unique_account']; ?>" type_code="<?php echo $value['type_code']; ?>" <?php echo $value['type_id']==@$row['type_id']?'selected':''; ?>><?php echo $value['type_code']." : ".$value['type_name']; ?></option>
						<?php } ?>
					</select>
					<?php } ?>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2">บาร์โค้ดสมุดคู่ฝาก</label>
				<div class="col-sm-9">
					<input name="barcode" class="form-control m-b-1" type="text" id="barcode" value="<?php echo @$row['barcode'] ?>">
				</div>
			</div>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2" require>ระบุยอดเงินเปิดบัญชี</label>
				<div class="col-sm-9">
					<input name="min_first_deposit" class="form-control m-b-1" type="text" id="min_first_deposit" value="" <?php echo $action_type=='edit'? 'readonly':'';?> required>
				</div>
			</div>
			<div class="border_transfer show_pay_type">
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2" require>วิธีการชำระเงิน</label>
				<div class="col-sm-9">
					<input type="radio" name="pay_type_tmp" value="0" checked=""> เงินสด <i class="fa fa-money"></i> &nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="pay_type_tmp" value="1"> โอนเงิน <i class="fa fa-credit-card"></i>
				</div>              
			</div>
			<!-- <div class="g24-col-sm-24" id="pay_type_transfer">   
				<label class="col-sm-3 control-label" for="form-control-2"></label>              
                 <div class="col-sm-9">
						<select name="save_account_type_transfer" class="form-control m-b-1" id="pay_type_transfer_list_1">													
							<option value="">เลือกรายการโอนเงิน</option>
							<?php foreach ($row_money_type as $key => $value) {?>	
							<option data-id="<?php echo $value["money_type_name_th"]?>" value="<?php echo $value["money_type_name_short"]?>" ><?php echo  $value["money_type_name_short"].' '.$value["money_type_name_th"]?></option>
							<?php } ?>				
						</select>		
				</div>
          </div>
		  <div class="g24-col-sm-24" id="pay_type_transfer_note">   
				<label class="col-sm-3 control-label" for="form-control-2"></label>              
                 <div class="col-sm-9">
						<input class="form-control m-b-1" name="save_account_note" id="save_account_note" placeholder="ระบุหมายเหตุ"/>
				</div>
			</div> -->
		</div>
		<div class="g24-col-sm-24"  id="pay_type_transfer" >
			<div class="form-group" >
				<label class="control-label g24-col-sm-6"></label>
				<div class="g24-col-sm-18 pay_type_transfer_content" style="margin-bottom: 5px;padding-top: 5px;">
					<div class="row m-b-1">
						<div class="form-group">
							<label class="control-label g24-col-sm-8" for="o_transfer_bank_id">ธนาคาร :</label>
							<select class="form-control g24-col-sm-16" id="o_transfer_bank_id" name="transfer_bank_id" onchange="change_bank('o_transfer_bank_id','o_transfer_bank_branch_id','o_transfer_acc_num','o_transfer_bank_branch_name')">
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
							<label class="control-label g24-col-sm-8" for="o_transfer_bank_branch_name">สาขา :</label>
							<select class="form-control g24-col-sm-16 m-b-1" id="o_transfer_bank_branch_id" name="transfer_bank_branch_id" onchange="change_bank_branch('o_transfer_bank_branch_id','o_transfer_acc_num','o_transfer_bank_branch_name')">
								<option value="">-- เลือกสาขาธนาคาร --</option>
							</select>
							<input type="hidden" class="form-control g24-col-sm-16" id="o_transfer_bank_branch_name" name="transfer_bank_branch_name" value="" placeholder="ระบุสาขาธนาคาร" />
						</div>
					</div>
					<div class="row m-b-1">
						<div class="form-group">
							<label class="control-label g24-col-sm-8" for="o_transfer_acc_num">เลขที่บัญชี :</label>
							<input class="form-control g24-col-sm-16" name="transfer_acc_num" id="o_transfer_acc_num" placeholder="ระบุเลขที่บัญชี"/>
						</div>
					</div>	
				</div>
			</div>
			<label class="control-label g24-col-sm-4">&nbsp;</label>
		</div>
	
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2" require>วันที่เปิดบัญชี</label>
				<div class="col-sm-9">
					<div class="input-with-icon">
						<div class="form-group">
							<?php
								$opn_date = date('d/m/').(date('Y')+543);
								if(@$row['created']!=''){
									$tmp_opn_date = explode('-', explode(' ', $row['created'] )[0]);
									$opn_date = $tmp_opn_date[2]."/".$tmp_opn_date[1]."/".($tmp_opn_date[0] + 543);
								}
							?>
							<div id="form_acc_id" class="form-group input-group">
								<input id="opn_date" name="opn_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" data-date-language="th-th" value="<?=$opn_date?>" <?=($action_type=='edit') ? "readonly" : ""?>>
								<span class="icon icon-calendar input-icon m-f-1"></span>
								<span class="input-group-btn">
									<a class="" href="#">
										<button id="edit_opn_date" type="button" class="btn btn-info btn-search"><span class="icon icon-edit"></span></button>
									</a>
								</span>	
							</div>

							
						</div>
					</div>
				</div>
			</div>
			<div class="g24-col-sm-24" style="margin: 6px 0px 6px 0px;" >
				<label class="col-sm-3 control-label" for="form-control-2" require>บัญชีคู่โอน</label>
				<div class="col-sm-9">
				<?php
					// var_dump($row);
				?>
					<?php
					if($action_type=='add'){
						?>
							<select name="account_transfer" id="account_transfer" class="form-control select_type">
						<option value="">เลือกบัญชีคู่โอน</option>
					
					</select>	
						<?php
					}else{
						?>
						<select name="account_transfer" id="account_transfer" class="form-control">
						<option value="">เลือกบัญชีคู่โอน</option>
						<?php
							if($account_list_transfer){
								foreach ($account_list_transfer as $key => $value_account_list) {
									if($row['id_transfer']==$value_account_list['id']){
										echo "<option value='".$value_account_list['id']."' selected>".$value_account_list['text']."</option>";
									}else{
										echo "<option value='".$value_account_list['id']."'>".$value_account_list['text']."</option>";
									}
								}
							}
						?>
					</select>
								
						<?php
					}
				?>
				
				</div>
			</div>
			<div class="g24-col-sm-24 check_account_atm">
				<label class="col-sm-3 control-label" for="form-control-2">สถานะการผูกบัญชีกรุงไทย ATM</label>
				<div class="col-sm-6 show_bt_atm_status">
					<div style="margin-top: 4px;">
						<button id="bt_show_a_atm_status" name="bt_show_a_atm_status" type="button" class="btn btn-primary" style="width: auto;" onclick="click_a_atm_status();">แก้ไขสถานะการผูกบัญชีกรุงไทย ATM</button>
					</div>
				</div>
				<div class="col-sm-6 show_radio_atm_status" style="display:none;">
					<div style="margin-top: 4px;">
						<input type="radio" name="account_atm_status" id="account_atm_status_0" value="A"><label>&nbsp;เพิ่ม&nbsp;</label>
						<input type="radio" name="account_atm_status" id="account_atm_status_1" value="D"><label>&nbsp;ลบ&nbsp;</label>
						<input type="radio" name="account_atm_status" id="account_atm_status_2" value="U"><label>&nbsp; แก้ไข&nbsp;</label>
						<input type="radio" name="account_atm_status" id="account_atm_status_3" value="N"><label>&nbsp; ลบและเพิ่ม&nbsp;</label>
						<i class="fa fa-times text-del del" onclick="cancel_a_atm_status()" title="ยกเลิกการเลือกสถานะการผูกบัญชีกรุงไทย ATM"></i>
					</div>
				</div>				
				<div class="col-sm-3 text-right">
					<a href="<?php echo base_url(PROJECTPATH.'/report_atm_ktb/coop_report_ktb_account_detail_preview?account_id='.$row['account_id']); ?>" target="_blank">
						<button id="bt_show_detail_atm_status" name="bt_show_detail_atm_status" type="button" class="btn btn-primary" style="width: auto;" title="แสดงรายการส่งค่าการผูกบัญชีกรุงไทย ATM ของระบบเงินฝาก">
							<i class="fa fa-file"></i>
						</button>
					</a>
					<a href="<?php echo base_url(PROJECTPATH.'/report_atm_ktb/coop_report_ktb_import_view?member_id='.$row['mem_id']); ?>" target="_blank">
						<button id="bt_show_detail_atm_status" name="bt_show_detail_atm_status" type="button" class="btn btn-primary" style="width: auto;" title="แสดงรายงานนำเข้าข้อมูล แนบไฟล์ CSV ที่ได้จาก KTB">
							<i class="fa fa-file"></i>
						</button>
					</a>
				</div>
			</div>
			<div class="g24-col-sm-24 check_account_atm" style="display:none;">
				<label class="col-sm-3 control-label" for="form-control-2">บัญชีกรุงไทย ATM</label>
				<div class="col-sm-9">
					<input name="account_id_atm" class="form-control m-b-1" type="text" id="account_id_atm" value="<?php echo @$row['account_id_atm'] ?>" maxlength="10" onkeyup="chkNumber(this);" readonly>
				</div>
			</div>
			<?php if($action_type=='edit'){ ?>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2">อายัดบัญชี</label>
				<div class="col-sm-9">
					<div style="margin-top: 4px;">
						<?php
							$lock = ((@$row['sequester_status']=='1' || $row['sequester_status_atm']) && @$row['sequester_by']!=$_SESSION['USER_ID'] && $row['sequester_by']!="") ?'disabled' : '';
						?>
						<input type="radio" name="sequester_status" id="sequester_status_0" value="0" onclick="change_type()" <?php echo (@$row['sequester_status']=='0' || @$row['sequester_status']=='')?'checked':''; ?> <?=$lock?>><label>&nbsp;ไม่อายัด &nbsp;&nbsp;</label>
						<input type="radio" name="sequester_status" id="sequester_status_1" value="1" onclick="change_type()" <?php echo (@$row['sequester_status']=='1')?'checked':''; ?> <?=$lock?>><label>&nbsp;อายัดทั้งหมด &nbsp;&nbsp;</label>
						<input type="radio" name="sequester_status" id="sequester_status_2" value="2" onclick="change_type()" <?php echo (@$row['sequester_status']=='2')?'checked':''; ?> <?=$lock?>><label>&nbsp; อายัดบางส่วน &nbsp;&nbsp;</label>
					</div>
				</div>
			</div>
			<div class="g24-col-sm-24 show_sequester_amount" style="display:none;">
				<label class="col-sm-3 control-label">จำนวนเงินอายัด</label>
				<div class="col-sm-4">
					<input name="sequester_amount" class="form-control m-b-1" type="text" id="sequester_amount" value="<?php echo number_format(@$row['sequester_amount'],0) ?>" onkeyup="format_the_number(this);" <?php echo $lock;?>>
				</div>
				<label class="col-sm-1 control-label text-left">บาท</label>
			</div>
			<div class="g24-col-sm-24 check_account_atm">
				<label class="col-sm-3 control-label" for="form-control-2">อายัด ATM <?=$row['user_id']?></label>
				<div class="col-sm-9">
					<div style="margin-top: 4px;">
						<?php 
							$sequester_status_atm_disabled = (@$row['sequester_status']=='1')?'disabled':'';
							$lock_atm = ((@$row['sequester_status']=='1' || $row['sequester_status_atm']) && @$row['sequester_by']!=$_SESSION['USER_ID'] && $row['sequester_by']!="") ?'disabled' : '';
						?>
						<input type="radio" class="sequester_status_atm" name="sequester_status_atm" id="sequester_status_atm_0" value="0" onclick="check_remark()" <?php echo (@$row['sequester_status_atm']=='0' || @$row['sequester_status_atm']=='')?'checked':''; ?> <?php echo $sequester_status_atm_disabled; ?> <?=$lock_atm?>><label>&nbsp;ไม่อายัด &nbsp;&nbsp;</label>
						<input type="radio" class="sequester_status_atm" name="sequester_status_atm" id="sequester_status_atm_1" value="1" onclick="check_remark()" <?php echo (@$row['sequester_status_atm']=='1')?'checked':''; ?> <?php echo $sequester_status_atm_disabled; ?> <?=$lock_atm?>><label>&nbsp;อายัด &nbsp;&nbsp;</label>
					</div>
				</div>
			</div>	
			<?php
				if($row['sequester_status'] || @$row['sequester_status_atm']){
					?>
					<div class="g24-col-sm-24">
						<label class="col-sm-3 control-label" for="form-control-2"></label>
						<div class="col-sm-9">
							<div style="margin-top: 4px;">
								<?php
									if($row['user_name']!=""){
										?>
											<h4>สาเหตุการอายัด : <?=$row['sequester_remark']?> <br>โดย <?=$row['user_name']?> เวลา <?=$this->center_function->ConvertToThaiDate($row['sequester_time']);?></h4>
										<?php
									}
								?>
							</div>
						</div>
					</div>
					
					<?php
				}
			?>
			<div class="g24-col-sm-24" id="div_remark" style="display:none;">
				<label class="col-sm-3 control-label" for="form-control-2">สาเหตุการอายัด</label>
				<div class="col-sm-9">
					<div style="margin-top: 4px;">
						<input name="remark" class="form-control m-b-1" type="text" id="remark" value=""  required placeholder="โปรดระบุสาเหตุการอายัด">
					</div>
				</div>
			</div>		
			<?php }else{ ?>
				<input type="hidden" name="sequester_status" value='0'>
				<input type="hidden" name="sequester_status_atm" value='0'>
				<input type="hidden" name="sequester_amount" value='0'>
			<?php } ?>			
			<!--div class="g24-col-sm-24" id="atm_space" style="display:none;">
				<label class="col-sm-3 control-label" for="form-control-2">เลขบัตร ATM</label>
				<div class="col-sm-9">
					<input name="atm_number" class="form-control m-b-1" type="text" id="atm_number" value="<?php echo @$row['atm_number'] ?>" <?php echo @$row['atm_number']==''?'':'readonly';?>>
				</div>
			</div-->
			<?php //if(@$row['atm_number']!=''){ ?>
			<!--div class="g24-col-sm-24" id="cancel_atm_space">
				<label class="col-sm-3 control-label" for="form-control-2"></label>
				<div class="col-sm-9">
					<input name="cancel_atm_number" class="m-b-1" type="checkbox" id="cancel_atm_number" value="1"> อาญัติบัตร ATM
				</div>
			</div-->
			<?php //} ?>

		</div>
				
		<div></div>
		<div class="g24-col-sm-24">
			<div class="col-sm-9 col-sm-offset-4">
				<button type="button" class="btn btn-primary min-width-100" style="margin-left:20px;" onclick="check_submit()">ตกลง</button>
				<button class="btn btn-danger min-width-100" type="button" onclick="window.parent.parent.location.reload();"> ยกเลิก</button>
			</div>
		</div>
	</form>
	</div>
	<table><tr><td>&nbsp;</td></tr></table>
	
<script>	
	$('#edit_account_no').click(() => {
		$('#acc_id').prop('readOnly', false);

		var value = $("#acc_id").val();

		var format = format_account_no(value);

		$("#acc_id").val(format);

	});

	$('#edit_opn_date').click(() => {
		$('#opn_date').prop('readOnly', false);


	});


	$('input[name=acc_id_yourself]').keyup(function() {
		var value = $(this).val();

		var format = format_account_no(value);

		$(this).val(format);
		// console.log(value_real);
	});

	$('input[name=acc_id]').keyup(function() {
		var value = $(this).val();

		var format = format_account_no(value);

		$(this).val(format);

		if(format.replace(/-/g, '').length == 11){
			$.ajax({
				url: base_url + "ajax/search_account_no",
				method: "post",
				data: {
					search: $(this).val().replace(/-/g, '')
				},
				dataType: "text",
				success: function (data) {
					// $('#result_add').html(data);
					if(data==0){
						$("#form_acc_id").addClass("has-success has-feedback");
						$("#form_acc_id").removeClass("has-error has-feedback");
					}else if(data>=1){
						$("#form_acc_id").addClass("has-error has-feedback");
						$("#form_acc_id").removeClass("has-success has-feedback");
						swal('เลขที่บัญชีนี้ซ้ำกับข้อมูลในระบบ', '', 'warning');
					}
					console.log("result", data);
				},
				error: function (xhr) {
					console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
				}
			});

			
		}else{
			$("#form_acc_id").addClass("has-error has-feedback");
			$("#form_acc_id").removeClass("has-success has-feedback");
		}
		// console.log(value_real);
	});


	function format_account_no(value){
		var value_real = value.replace(/-/g, '');
		var add_symbol = '';
		var str = "";
		var arr_number = value_real.split('');
		for (let i = 0; i < arr_number.length; i++) {
			const element = arr_number[i];
			var add_symbol = '';
			/*if(i==2){
				add_symbol = '-';
			}else if(i==4){
				add_symbol = '-';
			}else if(i==9){
				add_symbol = '-';
			}
			if(i>=11){
				continue;
			}
			*/
			add_symbol = '';
			if(i>=10){
				continue;
			}
			
			str += element + add_symbol;
		}
		return str;
	}

	$( document ).ready(function() {
		var value = $("#acc_id").val();

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
		
		if($("#acc_id").val()!=undefined){
			var format = format_account_no(value);

			$("#acc_id").val(format);
			$("#old_account_no").val(format.replace(/-/g, ''));
		}
		if($("#action_type").val() == 'edit'){
			$(".show_pay_type").hide();
		}else{
			$(".show_pay_type").show();
		}
	});



$('#min_first_deposit').keyup(function(evt, obj) {
	var value = $(this).val();
	var dotcontains = value.indexOf(".") != -1;
	if(dotcontains){
		return;
	}
	var number_format = numeral(value).format('0,0');
	$(this).val(number_format);
});

$('#min_first_deposit').change(function(evt, obj) {
	var value = $(this).val();
	var number_format = numeral(value).format('0,0.00');
	$(this).val(number_format);
});
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

function click_a_atm_status(){
	$('.show_bt_atm_status').hide();
	$('.show_radio_atm_status').show();	
	//$("#account_id_atm").prop("readonly",false);
}

function cancel_a_atm_status(){
	$('.show_bt_atm_status').show();
	$('.show_radio_atm_status').hide();
	$("input[name='account_atm_status']").attr("checked", false);
	$("#account_id_atm").prop("readonly",true);
}

$('#account_atm_status_1').click(() => {
	$("#account_id_atm").val("");
});

$("input[name='account_atm_status']").click(() => {
	$("#account_id_atm").prop("readonly",false);
});
$('.type_select').change(function(){
	var type_id = $(this).val();
	var mem_id = $("#member_id_add").val();
	
	$.ajax({
		method: 'POST',
		url: base_url + "/save_money/dual_list_transfer",
		data: { "type_id": type_id, "mem_id": mem_id },
		success: function(msg){
		  // console.log(msg); return false;
		   $(".select_type").html(msg)
	}
	});
});

$(document).on("change", "input[name='pay_type_tmp']", function(e){
       
    	if($(this).val() === "1"){;
			$("#pay_type_transfer").show();
           	$("#pay_type_transfer_note").show();
			   $("#pay_type_transfer_list_1").change(function() {
				var pay_type_transfer_list_1 = $(this).val();
				var open_account =($( "#pay_type_transfer_list_1 option:selected" ).attr('data-id'));
					$("#Deposit input[name=transaction_list]").val( pay_type_transfer_list_1 );
					$("#save_account_note").val("เปิดบัญชีโดยการ"+open_account);
				});
		}else{		
			$("#pay_type_transfer").hide();
			$("#pay_type_transfer_note").hide();
        }	
    })

</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>