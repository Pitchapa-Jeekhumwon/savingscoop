<div class="layout-content">
	<div class="layout-content-body">
		<h1 style="margin-bottom: 0">ระบบสรรหา</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<a class="link-line-none" href="?act=add">
					<button class="btn btn-primary btn-lg bt-add" type="button">
						<span class="icon icon-plus-circle"></span>
						เพิ่มกิจกรรม
					</button>
				</a>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">

					<div class="bs-example" data-example-id="striped-table">
						<table class="table table-striped">
							<thead>
									<tr>
									<th class="text-center" style="width: 80px;">ลำดับ</th>
									<th style="width: 110px;">วันที่จัดกิจกรรม</th>
									<th>ชื่อกิจกรรม</th>
									<th style="width: 110px;">จำนวนผู้เข้าร่วม</th>
									<th style="width: 120px;">สถานะ</th>
									<th style="width: 150px;">ผู้สร้างกิจกรรม</th>
									<th style="width: 250px;"></th>
									</tr>
							</thead>
							<tbody>
					<?php
					if(!empty($rs)){
						foreach(@$rs as $key => $row){ ?>
							<tr>
								<td class="text-center"><?php echo @$i++; ?></d>
								<td class="text-center"><?php echo $this->center_function->ConvertToThaiDate(@$row['meeting_date'], 1, 0); ?></td>
								<td><?php echo @$row['meeting_name']; ?></td>
								<td class="text-center"><?php echo number_format(@$row['regis_count']); ?></td>
								<td class="text-center"><?php echo @$row['meeting_status'] == 1 ? "เสร็จสิ้น" : "เปิดใช้งาน"; ?></td>
								<td class="text-center"><?php echo @$row['user_name']; ?></td>
								<td class="text-center">
									<a href="?act=add&id=<?php echo @$row["meeting_id"] ?>">แก้ไข</a> |
									<span class="text-del del"  onclick="del_coop_data('<?php echo @$row['meeting_id'] ?>')">ลบ</span> |
									<?php if(@$row['meeting_status'] == 0) { ?>
										<a href="<?php echo base_url(PROJECTPATH.'/meeting/register').'?id='.$row["meeting_id"]; ?>" target="_blank">ลงทะเบียน</a> |
									<?php } ?>
									<a href="?act=detail&id=<?php echo (int)$row["meeting_id"] ?>">รายละเอียด</a>
									<?php /*
									<a href="<?php echo base_url(PROJECTPATH.'/meeting/report_register').'?id='.$row["meeting_id"]; ?>" target="_blank">รายงาน</a> |
									<a href="?act=reward&id=<?php echo @$row["meeting_id"] ?>">รางวัล</a>
									*/ ?>
								</td>
							</tr>
					<?php
							}
						}
					?>

							</tbody>
						</table>
					</div>
				</div><!-- End panel panel-body  -->
				<?php echo @$paging; ?>
			</div>
		</div>
	</div>
</div>
<script>
  var base_url = $('#base_url').attr('class');

  function del_coop_data(id){
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
          url: base_url+'/meeting/delete',
          method: 'POST',
          data: {
            'table': 'coop_meeting',
            'id': id,
            'field': 'meeting_id'
          },
          success: function(msg){
            //console.log(msg); return false;
            if(msg == 1){
              document.location.href = base_url+'meeting';
            }else{

            }
          }
        });
      } else {

      }
    });
  }
</script>