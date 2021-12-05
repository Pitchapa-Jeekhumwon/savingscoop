<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.modal-header-alert {
				padding:9px 15px;
				border:1px solid #FF0033;
				background-color: #FF0033;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}
			.center {
				text-align: center;
			}
			.right {
				text-align: right;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			label{
				padding-top:7px;
			}
		</style>
		<h1 style="margin-bottom: 0">ระบบการลงทุน</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;" id="search-div">
					<form action="<?php echo base_url(PROJECTPATH.'/invest/report_profit_interest_preview'); ?>" id="search_form" method="POST" target="_blank">
						<input type="hidden" name="doc_type" id="doc_type" value="html"/>
						<div class="form-group g24-col-sm-24 text-center">
                            <h3>รายงานสรุปดอกเบี้ย</h3>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-7 control-label right"> วันที่ </label>
							<div class="g24-col-sm-4">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="from_date" name="from_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
							<label class="g24-col-sm-2 control-label right"> ถึงวันที่ </label>
							<div class="g24-col-sm-4">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="thru_date" name="thru_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-7 control-label right"> องค์กร </label>
							<div class="g24-col-sm-4">
								<div class="input-with-icon">
									<div class="form-group">
										<select id="org" name="org" class="form-control m-b-1 js-data-example-ajax">
                                            <option value="">ทั้งหมด</option>
                                            <?php 
                                                foreach($orgs as $key => $org) {
                                            ?>
                                            <option value="<?php echo $org['id']; ?>"><?php echo $org['name'];?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-7 control-label right"> หมวดการลงทุน </label>
							<div class="g24-col-sm-4">
								<div class="input-with-icon">
									<div class="form-group">
										<select id="type" name="type" class="form-control m-b-1 js-data-example-ajax">
                                            <option value="">ทั้งหมด</option>
                                            <?php 
                                                foreach($invest_types as $key => $type) {
                                            ?>
                                            <option value="<?php echo $type['id']; ?>"><?php echo $type['name'];?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24 text-center">
                            <button class="btn btn-primary btn-after-input" type="button" onclick="check_empty(1)"><span> แสดงรายงาน</span></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	$v = date('YmdHis');
	$link = array(
		'src' => PROJECTJSPATH.'assets/js/invest_report.js?v='.$v,
		'type' => 'text/javascript'
	);
	echo script_tag($link);
?>
<script>
	function check_empty(type){
		$.ajax({
			url:base_url+"invest/check_report_profit_interest", 
			method:"post",
			data:$("#search_form").serialize(),
			dataType:"text",
			success:function(data){
				if(data == 'success'){
					if(type == 1) {
						$("#doc_type").val('html');
					} else {
						$("#doc_type").val('excel');
					}
					$("#search_form").submit();
				} else if (data == "no-data") {
					swal('ไม่พบข้อมูล', '', 'warning');
				}else{
					$('#alertNotFindModal').appendTo("body").modal('show');
				}
			}
		});
	}
</script>
