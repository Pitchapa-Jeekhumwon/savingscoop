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
	#table_process_return tbody tr td:first-child {
		text-align: left;
	}
</style>
<?php
$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

$this->db->select(array('id','mem_group_name'));
$this->db->from('coop_mem_group');
$this->db->where("mem_group_type = '1'");
$row = $this->db->get()->result_array();

?>
<div class="layout-content">
	<div class="layout-content-body">
		<h1 style="margin-bottom: 0"> ประมวลผลเงินคืน </h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0" id="breadcrumb">
					<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <a class="btn btn-primary btn-lg bt-add" onclick="open_process_modal()">
                    <span></span> รายการคืนเงิน
                </a>
				<a class="btn btn-primary btn-lg bt-add" id="btn-show-return-manual" style="margin-right:10px">
					<span class="icon icon-hand-paper-o"></span> คืนเงินด้วยตัวเอง
				</a>
				<a class="btn btn-primary btn-lg bt-add" id="btn-show-statement-edit" style="margin-right:10px">
					<span class="icon icon-pencil"></span> แก้ไข statement
				</a>
			</div>
		</div>
		<div class="panel panel-body col-xs-12 col-sm-12 col-md-12 col-lg-12 " >

			<div class="form-inline text-center">
				<div class="form-group">
					<label class="control-label">วันที่&nbsp;</label>
					<input type="text" class="form-control datepick" id="date_s" name="date_s" value="<?php echo $this->center_function->mydate2date(date("Y-m-1")); ?>" data-date-language="th-th" />
					<label class="control-label">&nbsp;ถึงวันที่&nbsp;</label>
					<input type="text" class="form-control datepick" id="date_e" name="date_e" class="form-control datepick" value="<?php echo $this->center_function->mydate2date(date("Y-m-t")); ?>" data-date-language="th-th" />
					<button type="button" class="btn btn-primary" id="btn_get_data" name="btn_get_data">แสดงข้อมูล</button>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-sm-offset-2">
					<table class="table" id="table_process_return">
						<thead>
							<tr>
								<th>รายการคืนเงิน</th>
								<th>ทั้งหมด</th>
								<th>ไม่คืนเงิน</th>
								<th>คืนเงินแล้ว</th>
								<th>เก็บเพิ่ม</th>
								<th>ค้างโอน</th>
								<th>การดำเนินการ</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="process_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
    <div class="modal-dialog modal-dialog-data">
        <div class="modal-content data_modal">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h2 class="modal-title" id="type_name">รายการคืนเงิน</h2>
            </div>
            <form action="<?php echo base_url(PROJECTPATH.'/finance/coop_refund_month_preview'); ?>" id="form1" method="GET" target="_blank">
                <h3></h3>
            <div class="form-group g24-col-sm-24">
                <label class="g24-col-sm-8 control-label right"> เดือน </label>
                <div class="g24-col-sm-5">
                    <select id="month" name="month" class="form-control">
                        <?php foreach($month_arr as $key => $value){ ?>
                            <option value="<?php echo $key; ?>" <?php echo $key==((int)date('m'))?'selected':''; ?>><?php echo $value; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <label class="g24-col-sm-1 control-label right"> ปี </label>
                <div class="g24-col-sm-4">
                    <select id="year" name="year" class="form-control">
                        <?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
                            <option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group g24-col-sm-24">
                <label class="g24-col-sm-8 control-label right"> สังกัดหน่วยงาน </label>
                <div class="g24-col-sm-9">
                    <select name="department" id="department" onchange="change_mem_group('department', 'faction')" class="form-control">
                        <option value="">เลือกข้อมูล</option>
                        <?php
                        foreach($row as $key => $value){
                            ?>
                            <option value="<?php echo $value['id']; ?>"><?php echo $value['mem_group_name']; ?></option>
                            <?php
                        } ?>
                    </select>
                </div>
            </div>
            <br>
            <div class="text-center">
                <input type="button" class="btn btn-primary" style="width:50%" value="รายการคืนเงินประจำเดือน" data-type="preview" onclick="check_empty('preview')">
            </div>
           <br>
            </form>
        </div>
     </div>
</div>
<?php
	$link = [
		'src' => PROJECTJSPATH.'assets/js/process_return.js?v='.date("Ymdhi"),
		'type' => 'text/javascript'
	];
	echo script_tag($link);
?>
<script>
    function open_process_modal(){
        $('#process_modal').modal('show');
    }
    function change_mem_group(id, id_to){
        var mem_group_id = $('#'+id).val();
        $('#level').html('<option value="">เลือกข้อมูล</option>');
        $.ajax({
            method: 'POST',
            url: base_url+'manage_member_share/get_mem_group_list',
            data: {
                mem_group_id : mem_group_id
            },
            success: function(msg){
                $('#'+id_to).html(msg);
            }
        });
    }
    function check_empty(type){
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
            success:function(data){
                $.unblockUI();
                console.log(type)
                if(type == 'preview') {
                    $('#form1').attr('action', base_url+'finance/coop_refund_month_preview');
                    $('#form1').submit();
                } else if(type == 'excel') {
                    $('#form1').attr('action', base_url+'finance/coop_refund_month_excel');
                    $('#form1').submit();
                }

            }
        });
    }
</script>
