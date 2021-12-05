<div class="layout-content">
    <div class="layout-content-body">
	<style>
		label{
			padding-top:7px;
		}
		.control-label{
			padding-top:7px;
			text-align: right;
		}
		.control-label_2{
			padding-top:7px;
		}
		.indent{
				text-indent: 40px;
				.modal-dialog-data {
					width:90% !important;
					margin:auto;
					margin-top:1%;
					margin-bottom:1%;
				}
			}
			table>thead>tr>th{
				text-align: center;
			}
			table>tbody>tr>td{
				text-align: center;
			}

			.text-center{
				text-align:center;
			}
			.text-right{
				text-align:right;
			}
			.bt-add{
				float:none;
			}
			.modal-dialog{
				width:80%;
			}
			small{
				display: none !important;
			}
	</style>
		
		<?php
		$act = @$_GET['act'];
		$id  = @$_GET['id'];
		?> 
		<h1 style="margin-bottom: 0">ตั้งค่าการแสดงผลหน้าเงินกู้</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<?php if ($act != "add") { ?>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">	
					<a class="link-line-none" href="?act=add">
						<button class="btn btn-primary btn-lg bt-add" type="button"><span class="icon icon-plus-circle"></span> เพิ่มรายการ </button>
					</a>
				</div>
			<?php } ?>
		</div>

		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
                    <div class="g24-col-sm-24">
                        <div class="form-group g24-col-sm-24">
                            <label class="control-label g24-col-sm-9">ประเภทเงินกู้</label>
                            <div class="g24-col-sm-5">
                                <select id="loan_type" class="form-control" onchange="change_type()" >
                                    <option value="">เลือกประเภทการกู้เงิน</option>
                                    <?php foreach($rs_loan_type as $key => $value){ ?>
                                        <option value="<?php echo $value['id']; ?>"  <?php echo $value['id'] == @$default_loan ? 'selected="selected"' : ''; ?>><?php echo $value['loan_type']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-9 control-label">ชื่อเงินกู้</label>
                            <div class="g24-col-sm-5">
                                <select id="loan_name" class="form-control" name="loan_name">
                                    <option value="">เลือกชื่อเงินกู้</option>
                                    <?php foreach ($rs_loan_name as $key => $value){ ?>
                                        <option value="<?php echo $value['loan_name_id']?>" <?php echo $value['loan_name_id'] == @$default_loan_name ? 'selected="selected"' : '' ?>><?php echo $value['loan_name']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="g24-col-sm-23 text-center">
                        <div class="content-submit-btn">
                            <button type="button" class="btn btn-primary" onclick="confirm();"><span class="fa fa-save"></span>&nbsp;บันทึก</button>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

    function change_type() {
        $.ajax({
            url: base_url + 'setting_loan_default/change_loan_type',
            method: 'POST',
            data: {
                'type_id': $('#loan_type').val()
            },
            success: function (msg) {
                $('#loan_name').html(msg);
            }
        });
        $('#type_name').val($('#type_id :selected').text());
    }
	
	function confirm(){
        var loan_type = document.getElementById("loan_type").value;
        var loan_name = document.getElementById("loan_name").value;
        $.ajax({
            url: base_url+'/Setting_loan_default/save_setting_loan',
            method: 'POST',
            data: {
                'loan_type': loan_type,
                'loan_name': loan_name
            },
            success: function(msg){
                if(msg == 'success'){
                    location.reload();
                }
            }
        });
	}
</script>