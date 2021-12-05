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
		
		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">ใบสำคัญจ่าย</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
                    <form action="" id="form1" method="GET" autocomplete="off">
                        <div class="form-group g24-col-sm-24">
                            <div class="g24-col-sm-5 right">
                                <h3></h3>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> วันที่ </label>
                            <div class="g24-col-sm-4">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="from_date" name="from_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo !empty($_GET["from_date"]) ? $_GET["from_date"] : ""; ?>" data-date-language="th-th">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                            <label class="g24-col-sm-1 control-label text-center"> ถึง </label>
                            <div class="g24-col-sm-4">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="thru_date" name="thru_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo !empty($_GET["thru_date"]) ? $_GET["thru_date"] : ""; ?>" data-date-language="th-th">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> เลขที่ </label>
                            <div class="g24-col-sm-4">
                                    <div class="form-group">
                                        <input id="voucher_no" name="voucher_no" class="form-control m-b-1" type="text" value="<?php echo !empty($_GET["voucher_no"]) ? $_GET["voucher_no"] : '';?>" data-date-language="th-th">
                                    </div>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"></label>
                            <div class="g24-col-sm-2">
                                <input class="btn btn-primary btn-after-input" type="submit" value="ค้นหา"></input>
                            </div>
                        </div>
                    </form>
                    <div class="bs-example" data-example-id="striped-table">
						<div id="tb_wrap">
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th>วันที่ทำรายการ</th>
										<th>รหัสสมาชิก</th>
										<th>ชื่อสกุล</th>
                                        <th style="width: 130px;"></th>										
									</tr>
								</thead>
								<tbody>
								<?php
									$i=1;
									if(!empty($datas)){
										foreach($datas as $key => $data){
									?>
										<tr>
											<td><?php echo $this->center_function->ConvertToThaiDate($data['date'],1,0);?></td>
											<td><?php echo $data['member_id'];?></td>
											<td><?php echo $data['prename_short'].$data["firstname_th"]." ".$data["lastname_th"];?></td>
                                            <td>
                                                <a class="" id="voucher_<?php echo @$data['id']; ?>" title="" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo base_url(PROJECTPATH . "/account/voucher_pdf?id=".$data['id']);?>" target="_blank">
													<?php echo @$data['voucher_no']; ?>
												</a>
                                            </td>
										</tr>
									<?php
											$i++;
										}
									}else{ ?>
										<tr><td colspan="5">ไม่พบข้อมูล</td></tr>
									<?php } ?>
								</tbody> 
							</table>
						</div>
					</div>
					<?php echo @$paging ?>
				</div>
			</div>
		</div>
	</div>
</div>
	
<script>
var base_url = $('#base_url').attr('class');

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
	createSelect2("form1");
});

</script>
