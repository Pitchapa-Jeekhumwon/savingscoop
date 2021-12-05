<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.modal.fade {
			  z-index: 10000000 !important;
			}
			.modal-backdrop.in{
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
			
            .modal-footer {
                border-top:0;
            }	

		</style>
		
		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">นำเข้าข้อมูล แนบไฟล์ CSV ที่ได้จาก KTB</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<div class="form-group g24-col-sm-24">
						<h3></h3>
						<label class="g24-col-sm-6 control-label right"> นำเข้าข้อมูล แนบไฟล์ CSV ที่ได้จาก KTB </label>
						<div class="g24-col-sm-6">
							<button class="btn btn-primary btn-after-input" type="button" onclick="" id="bt_attach_file"><span> แนบไฟล์</span></button>	
						</div>
						<label class="g24-col-sm-6 control-label right"></label>
						<div class="g24-col-sm-6 right">
							<a href="<?php echo base_url(PROJECTPATH.'/report_atm_ktb/coop_report_ktb_import_view'); ?>">
								<button class="btn btn-primary btn-after-input" type="button" onclick=""><span> รายงานนำเข้า</span></button>
							</a>
						</div>
					</div>
					
					<div class="form-group g24-col-sm-24"></div>
					<table class="table table-bordered table-striped table-center">
                        <thead>
                            <tr class="bg-primary">
                                <th class="text-center">วันที่</th>
                                <th class="text-center">จำนวนรายการ</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="table_first">
                        <?php
                            if(!empty($data)) {
                                foreach($data as $val) {
									$arr_data = explode("-",$val['date_file']);
									$date_file = $arr_data[2].'/'.$arr_data[1].'/'.($arr_data[0]+543);
                        ?>
                            <tr>                                
                                <td class="text-center"><?php echo $this->center_function->ConvertToThaiDate($val['import_date_file'],0,0,0); ?></td>
                                <td class="text-center"><?php echo number_format(@$val['import_count_data'],0); ?></td>
                                <td class="text-center"><a href="<?php echo base_url(PROJECTPATH.'/report_atm_ktb/coop_report_ktb_import_excel?start_date='.$date_file); ?>" target="_blank">พิมพ์รายงาน</a></td>
                            </tr>
                        <?php
                                }
                            } else {
                        ?>
                            <tr>
                                <td class="text-center" colspan="8">ไม่พบข้อมูล</td>
                            </tr>
                        <?php
                            }
                        ?>
                        </tbody>
                    </table>		
				</div>
			</div>
		</div>
		<?php echo $paging; ?>
	</div>
</div>

<form action="<?php echo base_url(PROJECTPATH.'/report_atm_ktb/ktb_import_save'); ?>" id="form1" method="POST" enctype="multipart/form-data">
<div id="import-modal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-info">
        <div class="modal-content">
            <div class="modal-header modal-header-info">
                <h2 class="modal-title">นำเข้าข้อมูล แนบไฟล์ CSV ที่ได้จาก KTB</h2>
            </div>
            <div class="modal-body" style="height: 120px;">
                <div class="g24-col-xs-24 g24-col-sm-24 g24-col-md-24 g24-col-lg-24 padding-l-r-0">&nbsp;</div>
                <div class="g24-col-xs-24 g24-col-sm-24 g24-col-md-24 g24-col-lg-24 padding-l-r-0">
                    <label class="g24-col-xs-2 g24-col-sm-2 g24-col-md-2 g24-col-lg-2"></label>
                    <label class="g24-col-sm-7 control-label">แนบไฟล์ CSV</label>
                    <div class="g24-col-sm-6 req-file">
                        <div class="form-group">
                            <label class="fileContainer btn btn-info ">
                                <span class="icon icon-paperclip"></span> 
									แนบไฟล์
                                <input id="file" name="file" class="form-control m-b-1" type="file" value="" style="height: auto;">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center" style="padding-top:0;">
				<button type="submit" id="submit-btn" class="btn btn-primary" >นำเข้า</button>
			</div>
        </div>
    </div>
</div>
</form>

<script>
$(document).ready(function(){
	$("#bt_attach_file").click(function(){
		$("#import-modal").modal('toggle');
	});
});
</script>
