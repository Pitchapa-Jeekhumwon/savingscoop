<div class="layout-content">
    <div class="layout-content-body">
		<?php
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		?>
		<style>
			.center {
				text-align: center;
			}
			.right {
				text-align: right;
			}
			label{
				padding-top:7px;
			}		

		</style>
		
		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">รายงานอนุมัติคำขอกู้</h1>
		<?php $this->load->view('breadcrumb');
        $month = date("m");
        $year = date("Y");
        ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:50px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/report_approve_loan_request/report_approve_loan_request_preview'); ?>" id="form1" method="GET" target="_blank">
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> เดือน </label>
						<div class="g24-col-sm-9">
							<div class="input-with-icon">
								<div class="form-group">
                                    <select name="approve_month" id="approve_month" class="form-control">
                                        <?php
                                        if(!empty($month_arr)){
                                            foreach($month_arr as $key => $value){
                                                ?>
                                                <option value="<?php echo $key; ?>" <?php echo $month == $key ? 'selected':''; ?>><?php echo @$value; ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
								</div>
							</div>
						</div>
                    </div>
                    <div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right" style = 'min-width : 60px'> ปี </label>
						<div class="g24-col-sm-9">
                            <div class="form-group">
                                <select name="approve_year" id="approve_year" class="form-control">
                                    <?php
                                    if(!empty($year_arr)){
                                        foreach($year_arr as $key => $value){
                                            ?>
                                            <option value="<?php echo @$value['YEAR']; ?>" <?php echo $year == @$value['YEAR'] ? 'selected':''; ?>><?php echo @$value['YEAR']+543; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
						</div>
					</div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label right"> ประเภทเงินกู้ </label>
                        <div class="g24-col-sm-12 mem_type_list">
                            <label class="custom-control custom-control-primary custom-checkbox g24-col-sm-8" style="padding-top: 9px;margin-left: 15px;">
                                <input type="checkbox" class="custom-control-input type_item" id="loan_name_all" name="loan_name_all" value="all" onclick="select_checkbox('all')" checked>
                                <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                <span class="custom-control-label">ทั้งหมด</span>
                            </label>
                            <?php
                            if(!empty($loan_name)){
                                foreach($loan_name AS $key=>$value){
                                    ?>
                                    <label class="custom-control custom-control-primary custom-checkbox g24-col-sm-8" style="padding-top: 9px;">
                                        <input type="checkbox" class="custom-control-input type_item" id="loan_name[<?php echo @$value['loan_name_id'];?>]" name="loan_name[]" value="<?php echo @$value['loan_name_id'];?>" onclick="select_checkbox(<?php echo @$value['loan_name_id'];?>)" checked>
                                        <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                        <span class="custom-control-label"><?php echo @$value['loan_name'];?></span>
                                    </label>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
					<div class="form-group g24-col-sm-24">
						<div class="g24-col-sm-6">
						</div>
						<div class="g24-col-sm-9">
							<button class="btn btn-primary btn-after-input" type="button" style='width:100%' onclick="check_empty()"><span> รายงานอนุมัติคำขอกู้ </span></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
    function check_empty() {
        $.blockUI({
            message: 'กรุณารอสักครู่...',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .5,
                color: '#fff'
            },
            baseZ: 2000,
            bindEvents: false
        })

        $.ajax({
            url: base_url+'/report_approve_loan_request/check_loan_document',
            method:"post",
            data: $("#form1").serializeArray(),
            dataType:"text",
            success:function(data){
                $.unblockUI();
                if(data == 'success'){
                    $('#form1').submit()
                }else{
                    $('#alertNotFindModal').appendTo("body").modal('show')
                }
            }
        })
    }
    $( document ).ready(function() {
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
    });
    function select_checkbox(data) {
        var checkBox_all = document.getElementById("loan_name_all");
        if(data == 'all') {
            <?php
            if(!empty($loan_name)){
                foreach($loan_name AS $key=>$value){ ?>
                    var checkBox = document.getElementById("loan_name[<?php echo @$value['loan_name_id'];?>]");
                    checkBox.checked = checkBox_all.checked;
            <?php }
            }?>
        }else{
            var checkBox = document.getElementById("loan_name["+data+"]");
            if(checkBox.checked){
                var check_loan_name = true;
                <?php
                if(!empty($loan_name)){
                    foreach($loan_name AS $key=>$value){ ?>
                        var checkBox = document.getElementById("loan_name[<?php echo @$value['loan_name_id'];?>]");
                        if(!checkBox.checked){
                            check_loan_name = false;
                        }
                <?php }
                }?>
                if(check_loan_name){
                    checkBox_all.checked = true;
                }
            }else{
                checkBox_all.checked = false;
            }
        }
    }
</script>

