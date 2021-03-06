<style>
	.datepick {
		text-align: center;
		width: 150px !important;
	}
	#table_process_return {
		margin-top: 30px;
	}
	#table_process_return thead tr th {
		text-align: center !important;
	}
	#table_process_return tbody tr td {
		text-align: center;
	}
	#table_process_return tbody tr td:nth-child(4) {
		text-align: left;
	}
	#table_process_return tbody tr td:nth-child(7) {
		text-align: right;
	}
    .custom-form-group-radio-inline{
        padding-top: 6px;
    }
</style>
<div class="layout-content">
	<div class="layout-content-body">
		<h1 style="margin-bottom: 0"> ประมวลผลเงินคืน </h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0" id="breadcrumb">
					<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
		<div class="panel panel-body col-xs-12 col-sm-12 col-md-12 col-lg-12 " >

			<div class="row">
				<div class="col-xs-12 col-sm-12 col-sm-offset-0">
          <div class="text-right"><button type="button" class="btn btn-primary" id="btn-save-return">บันทึก</button></div>
					<br>
					<div class="col-xs-4 col-sm-4 col-sm-offset-0">
						<button type="button" class="btn" id="btn-previous">ก่อนหน้า</button>
					</div>
					<div class="col-xs-4 col-sm-4 col-sm-offset-0 text-center">
						<div class="col-md-4 text-right">
							<label for="">แสดงทีละ</label>
						</div>
						<div class="col-md-6">
							<select name="" id="limit" class="form-control">
								<option value="100" <?=(@$_GET['limit']=="100") ? "selected" : "" ?>>100 รายการ</option>
								<option value="200" <?=(@$_GET['limit']=="200") ? "selected" : "" ?>>200 รายการ</option>
								<option value="300" <?=(@$_GET['limit']=="300") ? "selected" : "" ?>>300 รายการ</option>
								<option value="400" <?=(@$_GET['limit']=="400") ? "selected" : "" ?>>400 รายการ</option>
								<option value="500" <?=(@$_GET['limit']=="500" || $_GET['limit']=="" ) ? "selected" : "" ?>>500 รายการ</option>
							</select>
						</div>
					</div>
					<div class="col-xs-4 col-sm-4 col-sm-offset-0 text-right">
						<button type="button" class="btn" id="btn-next">หน้าถัดไป</button>
					</div>

          <form id="frm-return-interest">
						<table class="table" id="table_process_return" data-type="<?php echo $_GET['type']; ?>" data-ds="<?php echo $_GET['ds']; ?>" data-de="<?php echo $_GET['de']; ?>" data-page="<?=(@$_GET['page']=="" ? "1" : @$_GET['page'])?>" data-limit="<?=(@$_GET['limit']=="" ? "100" : @$_GET['limit'] )?>">
              <thead>
                <tr>
                  <th width="30">
                    <label class="custom-control custom-control-primary custom-checkbox">
                      <input type="checkbox" id="check_all" value="1" class="custom-control-input" />
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-label"></span>
                    </label>
                  </th>
                  <th width="60">#</th>
                  <th width="100">รหัสสมาชิก</th>
				  <?php if(@$_GET['type'] == 7){?> 
					<th>ชื่อบัญชี</th>         
				  <?php }else{ ?>
					<th>ชื่อสมาชิก</th>
				    <?php }?>
				  <?php if(@$_GET['type'] == 5){?>
						<th width="120">เลขที่ใบเสร็จ</th>	
					   <th colspan="7">รายละเอียด</th>
				  <?php }elseif(@$_GET['type'] == 7){ ?>		
					   <th width="120">เลขที่บัญชี</th>	
					   <th width="130">เลขที่ใบเสร็จ</th>
					   <th width="120">เงินคืน</th>	
					   <th width="150">สถานะ</th>
				  <?php }else{ ?>
					<th width="120">เลขที่สัญญา</th>
					  <th width="60">อัตราดอกเบี้ย</th>
					  <th width="120">เลขที่ใบเสร็จ</th>
					  <th width="100">เงินคืนต้น</th>
					  <th width="100">เงินคืนดอก</th>
					  <th width="100">เก็บเพิ่ม</th>
					  <th width="150">สถานะ</th>

				    <?php }?>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </form>
				</div>
			</div>

		</div>
	</div>
</div>
<div class="modal fade" id="modal_return_date" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">กำหนดวันที่คืนเงิน</h4>
            </div>
            <div class="modal-body">
                <div class="form-group g24-col-sm-24">
                    <label class="g24-col-sm-8 control-label" for="form-control-2">วันโอนเงิน</label>
                    <div class="g24-col-sm-9" >
                        <div class="input-with-icon g24-col-sm-24" style="margin-left: -8px;">
                            <div class="form-group">
                                <input id="date_return" name="date_return" class="form-control m-b-1" style="padding-left: 50px;" type="text" data-date-language="th-th" value="<?php echo $this->center_function->mydate2date(date('Y-m-d'));?>" title="กรุณาป้อน วันที่">
                                <span class="icon icon-calendar input-icon m-f-1"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="g24-col-sm-24">
                    <label class="g24-col-sm-8 control-label">ช่องทางคืนเงิน</label>
                    <div class="g24-col-sm-16 custom-form-group-radio-inline">
                        <label class="radio-inline">
                                <input type="radio" name="pay_type" id="cash" value="0" checked> เงินสด
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="pay_type" id="transfer" value="1"> เงินโอน
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="pay_type" id="non_pay" value="3"> เก็บเงินไม่ได้
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-info" id="submit_return">บันทึก</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<?php
	$v = date('YmdHis');
	$link = [
		'src' => PROJECTJSPATH.'assets/js/process_return_edit.js?v='.$v,
		'type' => 'text/javascript'
	];
	echo script_tag($link);
?>
