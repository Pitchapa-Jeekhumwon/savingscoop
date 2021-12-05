<div class="layout-content">
    <div class="layout-content-body">
		<style>
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
		<h1 style="margin-bottom: 0">ตั้งค่าการเรียงลำดับเลขสัญญาเงินกู้</h1>
		
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
						<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_1">
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th>#</th>
										<th>ประเภทเงินกู้</th>
                                        <th>ชื่อเงินกู้</th>
                                        <th>ตัวนับปัจจุบัน</th>
                                        <th>จัดการ</th>
									</tr> 
								</thead>
								<tbody>
								<?php  
									if(!empty($loan_types)){
                                        $i=1;
										foreach($loan_types as $key => $row){ 
								?>
									<tr> 
										<td><?php echo $i++; ?></td>
                                        <td style="text-align:left;"><?php echo @$row['loan_type']; ?></td>
                                        <td style="text-align:left;"><?php echo @$row['loan_name']; ?></td>
                                        <td><?=(@$row['current_contract_number']=="") ? "0" : @$row['current_contract_number']?></td>
										<td>
											<a title="แก้ไข" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="edit_number('<?php echo @$row['loan_name_id']?>', '<?php echo @$row['loan_name']?>')"><span style="cursor: pointer;" class="icon icon-edit"></span>
											</a>
										</td> 
									</tr>
								<?php 
										}
									} 
								?>
								</tbody> 
							</table> 
						</div>
						<?php echo @$paging ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- MODAL -->
<div class="modal fade" id="edit_number" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">แก้ไขการเรียงลำดับเลขสัญญาเงินกู้</h4>
        </div>
        <div class="modal-body">
            <h3 id="loan_named" style="margin-top: 0px;"></h3>
            <form action="edit_number_increment" method="POST" id="frm">
                <input type="hidden" name="loan_name_id" id="loan_name_id" value="">
                <div class="row">
                    <div class="col-sm-4">
                        <label for="">ตัวนับ</label>
                    </div>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" name="run_contract_number" id="run_contract_number">
                    </div>
                </div>
                <div class="row" style="margin-top: 15px;margin-bottom: 15px;">
                    <div class="col-sm-4">
                        <label for="">เลขสัญญาต่อไปจะได้หมายเลข</label>
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="next_contract_number" readonly>
                    </div>
                </div>


            </form>
        </div>
        <div class="modal-footer text-center">
			<button class="btn btn-info" id="submit_edit">ตกลง</button>
			<button class="btn btn-default" id="" data-dismiss="modal">ยกเลิก</button>
        </div>
      </div>
    </div>
</div>

<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/setting_number_increment.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
