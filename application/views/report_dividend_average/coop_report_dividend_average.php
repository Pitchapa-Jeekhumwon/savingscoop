<div class="layout-content">
    <div class="layout-content-body">
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
          .panel-body{
            padding: 35px;
          }
		</style>
		<h1 style="margin-bottom: 0">รายงานสรุปยอดปันผลและเฉลี่ยคืน (ยอดสุทธิ)</h1>
		<?php 
		$this->load->view('breadcrumb'); 
		
		?>
        <div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
                    <div class="g24-col-sm-24">
					<form action="<?php echo base_url(PROJECTPATH.'/report_refund_average/report_refund_average_calculate_preview'); ?>" id="form1" method="GET" target="_blank">
						<div class="row row m-t-5">
                            <div class="form-group g24-col-sm-24">
								<label class="g24-col-sm-9 control-label" for="form-control-2">ปี</label>
								<div class="input-with-icon g24-col-sm-6">
									<div class="form-group">
										<select name="year" id="year"  class="form-control m-b-1">
		  								<?php foreach($year as $key => $value){ ?>
											<option value="<?php echo $value['year']; ?>"><?php echo $value['year']; ?></option>
										<?php } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
                        <?php 
                            $arr_data['type'] = array('1'=>'เรียงตามธนาคาร', '2'=>'เรียงตามวิธีรับเงิน')
                        ?>
                        <div class="row">
							<div class="form-group g24-col-sm-24">
								<label class="g24-col-sm-9 control-label" for="form-control-2"> เรียงลำดับตาม</label>
                                <div class="g24-col-sm-6">
                                    <select id="type_report" name="type_report" class="form-control">
                                        <?php foreach($arr_data['type'] as $key => $value){ ?>
                                            <option value="<?php echo $key; ?>" <?php echo $key=='1'?'selected':''; ?>><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>	
							</div>
						</div>
                        <div class="row justify-content-center">
							<div class="g24-col-sm-9">
							</div>
                            <div class="g24-col-sm-5">
                                <button type="button" id="submit_btn" class="btn-primary form-control" onclick="check_empty()" ><span class="icon icon-file-text-o" style="margin-top: 1px;"></span><span> แสดงรายงาน </span></button>
                            </div>
                        </div>
					</div>
					</form>	

                </div>
            </div>
        </div>
    </div>
</div>


<script>
	$('document').ready(function() {
		$("#date_interest").datepicker({
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
	function check_empty(){		
		
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
		});
		
		$.ajax({
			url: base_url+'/report_dividend_average/check_report_dividend_average',	
			method:"post",
			data: $("#form1").serializeArray(),
			dataType:"text",
			success:function(data){
			console.log(data);
			$.unblockUI();	
				if(data == 'success'){
					link_to =  base_url+'report_dividend_average/coop_report_dividend_average_excel';
					$('#form1').attr('action', link_to);
					$('#form1').submit();
				}else{
					$('#alertNotFindModal').appendTo("body").modal('show');
				}
			}
		});
	}
</script>