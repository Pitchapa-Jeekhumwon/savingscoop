<div class="col-md-12 ">
	<form data-toggle="validator" novalidate="novalidate" id="frm1" action="<?php echo base_url(PROJECTPATH.'/deposit_atm/save_account_atm'); ?>" method="post">
		<?php
			if($account_id!=''){
				$action_type = 'edit';
			}else{
				$action_type = 'add';
			}
		?>
		<input type="hidden" id="action_type" name="action_type" value="<?php echo $action_type; ?>">
		<div class="form-group">
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2">รหัสสมาชิก</label>
				<div class="col-sm-9 m-b-1">
					<div class="input-group">
						<input value="<?php echo empty($row['account_id']) ? '' : $row['mem_id'] ?>" class="form-control m-b-1" type="text" name="mem_id" id="member_id_add" required onkeypress="check_member_id();">
						<span class="input-group-btn">
							<a class="" data-toggle="modal" data-target="#search_member_add_modal" href="#">
								<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
							</a>
						</span>	
					</div>
				</div>
			</div>

			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2">ชื่อ - นามสกุล</label>
				<?php
					if($action_type=='add'){
                ?>
							<div class="col-sm-9">
								<input value="<?php echo empty($row['account_id']) ? '' : $row['member_name'] ?>" class="form-control m-b-1" type="text" name = "member_name" id="member_name_add"   required readonly>
							</div>
						<?php
					}else{
						?>
							<div class="col-sm-9">
								<input value="<?php echo empty($row['account_id']) ? '' : $row['member_name'] ?>" class="form-control m-b-1" type="text" name = "member_name" id="member_name_add" required readonly>
							</div>
								
                <?php
					}
				?>
									
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
				<label class="col-sm-3 control-label" for="form-control-2" require>ระบุยอดเงินอนุมัติ</label>
				<div class="col-sm-9">
					<input name="approve_amount" class="form-control m-b-1" type="text" id="approve_amount" value="<?php echo number_format($row['approve_amount'], 2);?>" <?php echo $action_type=='edit'? 'readonly':'';?> required>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2" require>ระบุยอดเงินเปิดบัญชี</label>
				<div class="col-sm-9">
					<input name="min_first_deposit_atm" class="form-control m-b-1" type="text" id="min_first_deposit_atm" value="<?php echo number_format($row['approve_amount'], 2);?>" <?php echo $action_type=='edit'? 'readonly':'';?> required>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2" require>วันที่เปิดบัญชี ATM</label>
				<div class="col-sm-9">
					<div class="input-with-icon">
						<div class="form-group">
							<?php
								$opn_date = date('d/m/').(date('Y')+543);
								if(@$row['approve_date']!=''){
									$tmp_opn_date = explode('-', explode(' ', $row['approve_date'] )[0]);
									$opn_date = $tmp_opn_date[2]."/".$tmp_opn_date[1]."/".($tmp_opn_date[0] + 543);
								}
							?>
							<div id="form_acc_id" class="form-group input-group">
								<!-- <input type="hidden" id="old_account_no" name="old_account_no" value="00101017822"> -->
								<input id="opn_date" name="opn_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" data-date-language="th-th" value="<?=$opn_date?>" <?=($action_type=='edit') ? " readonly disabled " : ""?>>
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
			<div class="g24-col-sm-24" style="margin: 6px 0px 6px 0px;">
				<label class="col-sm-3 control-label" for="form-control-2" require>บัญชี ATM</label>
				<div class="col-sm-9">
					<select name="acc_id" id="acc_id" class="form-control" <?php echo $row['account_id']=="" ? '' : 'readonly disabled'; ?>>
						<option value="">เลือกบัญชี ATM</option>
						<?php
							if($account_list_transfer){
								foreach ($account_list_transfer as $key => $value_account_list) {
									if($row['id_transfer']==$value_account_list['id']){
										echo "<option selected='selected' value='".$value_account_list['id']."' >".$value_account_list['text']."</option>";
									}else{
										echo "<option value='".$value_account_list['id']."'>".$value_account_list['text']."</option>";
									}
								}
							}
						?>
					</select>
                    <input type="hidden" name="dummy_acc_id" value="<?php echo $row['id_transfer']; ?>">
				</div>
			</div>
            <div class="g24-col-sm-24" style="margin: margin: 6px 0px 6px 0px;">
                <label class="col-sm-3 control-label" for="form-control-2" require>เลขบัญชีธานาคาร</label>
                <div class="col-sm-9">
				<select name="bank_account" id="dividend_acc_num" class="form-control" <?php echo $row['account_id']=="" ? '' : 'readonly disabled'; ?>>
						<option value="">เลือกเลขบัญชีธนาคาร</option>
						<?php
							if($get_dividend_acc_num){
								foreach ($get_dividend_acc_num as $key2 => $value_dividend_acc_num) {
									if($row['bank_account_on']==$value_dividend_acc_num['dividend_acc_num']){
										echo "<option selected='selected' value='".$value_dividend_acc_num['id']."' >".$value_dividend_acc_num['dividend_acc_num']."</option>";
									}else{
										echo "<option value='".$value_dividend_acc_num['id']."'>".$value_dividend_acc_num['dividend_acc_num']."</option>";
									}
								}
							}
						?>
					</select>

                </div>
            </div>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2">สถานะการเปิดใช้งาน ATM ของเงินกู้</label>
				<div class="col-sm-9">
					<div style="margin-top: 4px;">
						<input type="radio" name="loan_atm_activate" id="loan_atm_activate_0" value="0" onclick="check_loan_atm()" <?php echo (@$row['loan_atm_activate']=='0' || @$row['loan_atm_activate']=='')?'checked':''; ?>><label>&nbsp;ไม่เปิดใช้งาน &nbsp;&nbsp;</label>
						<input type="radio" name="loan_atm_activate" id="loan_atm_activate_1" value="1" onclick="check_loan_atm()" <?php echo (@$row['loan_atm_activate']=='1')?'checked':''; ?>><label>&nbsp;เปิดใช้งาน &nbsp;&nbsp;</label>
					</div>
				</div>
			</div>
			<?php if($action_type=='edit'){ ?>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2">อายัดบัญชี</label>
				<div class="col-sm-9">
					<div style="margin-top: 4px;">
						<input type="radio" name="sequester_status" id="sequester_status_0" value="0" onclick="change_type()" <?php echo (@$row['sequester_status']=='0' || @$row['sequester_status']=='')?'checked':''; ?>><label>&nbsp;ไม่อายัด &nbsp;&nbsp;</label>
						<input type="radio" name="sequester_status" id="sequester_status_1" value="1" onclick="change_type()" <?php echo (@$row['sequester_status']=='1')?'checked':''; ?>><label>&nbsp;อายัดทั้งหมด &nbsp;&nbsp;</label>
						<input type="radio" name="sequester_status" id="sequester_status_2" value="2" onclick="change_type()" <?php echo (@$row['sequester_status']=='2')?'checked':''; ?>><label>&nbsp; อายัดบางส่วน &nbsp;&nbsp;</label>
					</div>
				</div>
			</div>
			<div class="g24-col-sm-24 show_sequester_amount" style="display:none;">
				<label class="col-sm-3 control-label">จำนวนเงินอายัด</label>
				<div class="col-sm-4">
					<input name="sequester_amount" class="form-control m-b-1" type="text" id="sequester_amount" value="<?php echo number_format(@$row['sequester_amount'],0) ?>" onkeyup="format_the_number(this);">
				</div>
				<label class="col-sm-1 control-label text-left">บาท</label>
			</div>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2">อายัด ATM</label>
				<div class="col-sm-9">
					<div style="margin-top: 4px;">
						<?php $sequester_status_atm_disabled = (@$row['sequester_status']=='1')?'disabled':''; ?>
						<input type="radio" class="sequester_status_atm" name="sequester_status_atm" id="sequester_status_atm_0" value="0" onclick="check_remark()" <?php echo (@$row['sequester_status_atm']=='0' || @$row['sequester_status_atm']=='')?'checked':''; ?> <?php echo $sequester_status_atm_disabled; ?>><label>&nbsp;ไม่อายัด &nbsp;&nbsp;</label>
						<input type="radio" class="sequester_status_atm" name="sequester_status_atm" id="sequester_status_atm_1" value="1" onclick="check_remark()" <?php echo (@$row['sequester_status_atm']=='1')?'checked':''; ?> <?php echo $sequester_status_atm_disabled; ?>><label>&nbsp;อายัด &nbsp;&nbsp;</label>
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
								<h4>สาเหตุการอายัด : <?=$row['sequester_remark']?> โดย <?=$row['user_name']?> เวลา <?=$this->center_function->ConvertToThaiDate($row['sequester_time']);?></h4>
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
        var acc_id = $('#acc_id');
		if(acc_id.prop('readOnly') === true){
            acc_id.prop('readOnly', false);
            var value = acc_id.val();
            var format = format_account_number(value);
            acc_id.val(format);
        }

	});

	$('#edit_opn_date').click(() => {
		$('#opn_date').prop('readOnly', false);


	});


	$('input[name=acc_id_yourself]').keyup(function() {
		var value = $(this).val();

		var format = format_account_number(value);

		$(this).val(format);
		// console.log(value_real);
	});

	$('input[name=acc_id]').keyup(function() {
		var value = $(this).val();

		var format = format_account_number(value);

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
			if(i==2){
				add_symbol = '-';
			}else if(i==4){
				add_symbol = '-';
			}else if(i==9){
				add_symbol = '-';
			}
			if(i>=11){
				continue;
			}
			str += element + add_symbol;
		}
		return str;
	}

    function format_account_number(txt, pattern){
	    if(typeof txt === "undefined") return txt;
        pattern = typeof pattern === "undefined" ? "##-#####" : pattern;
	    var str = "";
	    var num = 0;
        pattern.split("").forEach(function(i, k){
            if(i === "#"){
                str += '{'+num+'}';
                num++;
            }else {
                str += i;
            }
        });
        txt.split("").forEach(function(i, k){
            str = str.replace("{"+k+"}", i);
        });
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
			var format = format_account_number(value);

			$("#acc_id").val(format);
			$("#old_account_no").val(format.replace(/-/g, ''));
		}
		
	});



$('#approve_amount').keyup(function(evt, obj) {
	var value = $(this).val();
	var dotcontains = value.indexOf(".") != -1;
	if(dotcontains){
		return;
	}
	var number_format = numeral(value).format('0,0');
	$(this).val(number_format);
});

$('#approve_amount').change(function(evt, obj) {
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

</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
