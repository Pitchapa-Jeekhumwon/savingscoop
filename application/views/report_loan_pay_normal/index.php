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
		<h1 style="margin-bottom: 0">รายงานการจ่ายเงินกู้</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:50px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/report_loan_pay_normal/report_loan_pay_normal_excel'); ?>" id="form1" method="GET" target="_blank">
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> ตั้งแต่วันที่ </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="date_start" name="date_start" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
						<label class="g24-col-sm-1 control-label right" style = 'min-width : 60px'> ถึงวันที่ </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="date_end" name="date_end" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
					</div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label right"> ประเภทรายงาน </label>
                        <div class="g24-col-sm-9">
                            <select name="loan_type" id="loan_type" class="form-control">
                                <option value="">ทั้งหมด</option>
                                <?php
                                if(!empty($loan_type)){
                                    foreach($loan_type as $key => $value){
                                        ?>
                                        <option value="<?php echo @$value['id']; ?>"><?php echo @$value['loan_type']; ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
					<div class="form-group g24-col-sm-24">
						<div class="g24-col-sm-6">
						</div>
						<div class="g24-col-sm-9">
							<button class="btn btn-primary btn-after-input" type="button" style='width:100%' onclick="check_empty()"><span> รายงานการจ่ายเงินกู้ </span></button>
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
            url: base_url+'/report_loan_pay_normal/check_loan_document',
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
</script>

