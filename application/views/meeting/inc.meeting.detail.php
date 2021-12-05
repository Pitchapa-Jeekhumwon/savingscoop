<style>
  .btn.btn-block {
    width: 100%;
  }
</style>
<div class="layout-content">
	<div class="layout-content-body">
    <div class="panel panel-body">
      <h3 class="text-center"><?php echo $row['meeting_name']; ?></h3>
      <div class="row gutter-xs m-t-3 m-b-5">
        <div class="col-xs-12 col-sm-2 col-sm-offset-5">
		  <button class="btn btn-block btn-primary" id="btn-label" data-id="<?php echo $row["meeting_id"]; ?>">ลาเบล</button>
          <a class="btn btn-block btn-primary" href="<?php echo base_url(PROJECTPATH.'/meeting/register').'?id='.$row["meeting_id"]; ?>" target="_blank">ลงทะเบียน</a>
          <a class="btn btn-block btn-primary" href="<?php echo base_url(PROJECTPATH.'/meeting/register_form').'?id='.$row["meeting_id"]; ?>" target="_blank">แบบฟอร์มลงทะเบียน</a>
          <a class="btn btn-block btn-primary" href="<?php echo base_url(PROJECTPATH.'/meeting/register_info').'?id='.$row["meeting_id"]; ?>" target="_blank">ข้อมูลผู้เข้าร่วมงาน</a>
          <a class="btn btn-block btn-primary" href="<?php echo base_url(PROJECTPATH.'/meeting/register_graph').'?id='.$row["meeting_id"]; ?>" target="_blank">กราฟสถิต Realtime</a>
          <a class="btn btn-block btn-primary" href="<?php echo base_url(PROJECTPATH.'/meeting').'?act=reward&id='.$row["meeting_id"]; ?>" target="_blank">รางวัล</a>
          <a class="btn btn-block btn-primary" href="<?php echo base_url(PROJECTPATH.'/meeting/register_facescan').'?id='.$row["meeting_id"]; ?>" target="_blank">การสแกนใบหน้า</a>
          <button class="btn btn-block btn-primary" id="btn-del-meeting" data-id="<?php echo $row["meeting_id"]; ?>">ลบการลงทะเบียนทั้งหมด</button>
		  <?php /*<a class="btn btn-block btn-primary" href="<?php echo base_url(PROJECTPATH.'/meeting/register_facescan_dup').'?id='.$row["meeting_id"]; ?>" target="_blank">แจ้งเตือนลงทะเบียนซ้ำ</a>*/ ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="modal_label" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-add" style="width:40% !important;">
		<div class="modal-content">
			<div class="modal-header modal-header-info">
				<h2 class="modal-title">พิมพ์ลาเบล</h2>
			</div>
			<div class="modal-body">
				<div class="g24-col-sm-24 ">
					<form data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/meeting/label'); ?>" method="get" id="frm_label" target="_blank">
						<label class="control-label g24-col-sm-24 m-b-1" style="text-align: center;">เลขที่ต้องการพิมพ์</label>
						<div class="row m-b-1">
							<div class="form-group">
								<div class="g24-col-sm-4 g24-col-sm-offset-7">
									<input type="text" class="form-control" name="start_num" id="start_num" value="" required title="&nbsp;">
								</div>
								<label class="control-label g24-col-sm-2 m-b-1" style="text-align: center;">ถึง</label>
								<div class="g24-col-sm-4">
									<input type="text" class="form-control" name="end_num" id="end_num" value="" required title="&nbsp;">
								</div>
							</div>
						</div>
						<label class="control-label g24-col-sm-offset-7 m-b-1">เช่น 1 - 1000</label>
						
						<div class="row m-b-1">
							<div class="form-group">
								<div class="g24-col-sm-24" style="text-align:center">
									<button type="submit" class="btn btn-primary min-width-100 mct_btn_print">พิมพ์ลาเบล</button>
									<button class="btn btn-danger min-width-100" type="button" data-dismiss="modal">ยกเลิก</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				&nbsp;
			</div>
		</div>
	</div>
</div>

<script>
  var base_url = $('#base_url').attr('class');


  $('body').on('click', '#btn-del-meeting', function() {
    $meeting_id = $(this).data('id');
    swal({
      title: "ท่านต้องการลบข้อมูลใช่หรือไม่",
      text: "",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: '#DD6B55',
      confirmButtonText: 'ลบ',
      cancelButtonText: "ยกเลิก",
      closeOnConfirm: false,
      closeOnCancel: true
    },
    function(isConfirm) {
      if (isConfirm) {
        $.ajax({
						url: base_url+'/meeting/meeting_detail_del',
						method: 'POST',
						data: {
							'id': $meeting_id
						},
						async: true,
						success: function(msg){ console.log( msg );
							swal({
              title: "",
              text: '',
              type: "success",
              confirmButtonColor: '#3a6336',
              confirmButtonText: 'ปิดหน้าต่าง',
              closeOnConfirm: true
            });
						}
					});
      } else {

      }
    });

  });
  
  $("#btn-label").click(function() {
	$("#modal_label").modal('show');
  });
</script>