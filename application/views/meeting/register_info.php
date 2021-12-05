<div class="layout-content">
	<div class="layout-content-body">
		<h1 style="margin-bottom: 0">ข้อมูลผู้เข้าร่วมงาน</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>

			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0 text-right m-b-3">
				<button type="button" class="link-line-none btn btn-danger btn-lg" id="btn-meeting_del">
						ลบ
        </button>
				<a class="link-line-none btn btn-primary btn-lg" href="report_register?id=<?php echo $meeting_id; ?>">
						รายงาน
				</a>
				<button type="button" class="link-line-none btn btn-primary btn-lg" id="info-set">
						บันทึก
        </button>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<div class="row gutter-xs">
						<div class="col-xs-12 col-sm-6">


								<input type="text" class="form-control" id="filter" placeholder="ค้นหาจากรหัสสมาชิก หรือชื่อสกุล" />
<?php /*<div class="input-group">
								<div class="input-group-btn" style="display: none;">
									<button type="button" class="btn btn-default" id="btn-filter">ค้นหา</button>
								</div>
								</div>
								*/ ?>

						</div>
					</div>
					<div class="bs-example" data-example-id="striped-table">
            <form id="frm_info">
						<table class="table table-striped" id="table-data" data-id="<?php echo $meeting_id; ?>">
							<thead>
									<tr>
                    <th width="60" class="text-center"><input type="checkbox" id="meeting_del-all" /></th>
                    <th width="80" class="text-center">ลำดับ</th>
                    <th width="120" class="text-center hide">รูป</th>
                    <th width="80" class="text-center">รหัสสมาชิก</th>
                    <th class="text-center">ชื่อสกุล</th>
                    <th width="150" class="text-center">เวลาลงทะเบียน</th>
                    <th width="120" class="text-center">การลงทะเบียน</th>
                    <th width="80" class="text-center">เลขหางบัตร</th>
                    <th width="80" class="text-center">ของทีระลึก</th>
									</tr>
							</thead>
							<tbody>
					<?php
					if(!empty($rs)) {
            $i = 1;
						foreach(@$rs as $key => $row){
              $regis_type = '';
              if( $row['facescan_id'] ) $regis_type = 'ใบหน้า';
              elseif( $row['id_card_data'] ) $regis_type = 'บัตรประชาชน';
              else $regis_type = 'เลขสมาชิก';
              $fullname = "{$row['firstname_th']} {$row['lastname_th']}";
          ?>
							<tr>
								<td class="text-center"><input type="checkbox" class="meeting_del" name="meeting_del[]" data-face="<?php echo $row['facescan_id']; ?>" value="<?php echo $row['meeting_regis_id']; ?>" /></d>
								<td class="text-center"><?php echo @$i++; ?></d>
								<td class="text-center hide"><img src="/assets/uploads/members/<?php echo empty($row['member_pic']) ? 'default.png' : $row['member_pic'] ; ?>" class="img-responsive" alt=""></td>
								<td class="text-center"><?php echo @$row['member_id']; ?></td>
								<td><?php echo @$fullname; ?></td>
								<td class="text-center"><?php echo $this->center_function->ConvertToThaiDate(@$row['create_time'], 1, 1); ?></td>
								<td class="text-center"><?php echo $regis_type; ?></td>
								<td class="text-center"><input type="text" class="form-control" name="card_tail_number[<?php echo $row['meeting_regis_id']; ?>]" value="<?php echo $row['card_tail_number']; ?>" /></td>
								<td class="text-center"><input type="checkbox" name="is_gift[<?php echo $row['meeting_regis_id']; ?>]" value="1"<?php echo (int)$row['is_gift'] ? ' checked' : '' ; ?> /></td>
							</tr>
					<?php
							}
						}
					?>

							</tbody>
            </table>
            </form>
					</div>
				</div><!-- End panel panel-body  -->
				<?php echo @$paging; ?>
			</div>
		</div>
	</div>
</div>
<script>
  var base_url = $('#base_url').attr('class');
	var WEB_BASE_URL = '<?php echo WEB_BASE_URL; ?>';

	const isEmpty = (val) => {
    return (val === undefined || val == null || val.length <= 0) ? true : false;
	}

	const get_data = () => {
		$.ajax({
			url: base_url+'/meeting/register_info_get',
			method: 'POST',
			dataType: 'json',
			data: {
				'meeting_id': $('#table-data').data('id'),
				'filter': $.trim($('#filter').val())
			},
			async: true,
			success: function( $json ){ console.log( $json );
				$('#table-data tbody').empty();
				$no = 1;
				$.each( $json.data, function($key, $row) {
					$regis_type = '';
					if( $row.facescan_id ) $regis_type = 'ใบหน้า';
					else if( $row.id_card_data ) $regis_type = 'บัตรประชาชน';
					else $regis_type = 'เลขสมาชิก';
					$fullname = `${$row.firstname_th} ${ $row.lastname_th }`;
					$('#table-data tbody').append(`
						<tr>
							<td class="text-center"><input type="checkbox" class="meeting_del" name="meeting_del[]" data-face="${ $row.facescan_id }" value="${ $row.meeting_regis_id }" /></d>
							<td class="text-center">${ $no++ }</d>
							<td class="text-center hide"><img src="/assets/uploads/members/${ isEmpty($row.member_pic) ? 'default.png' : $row.member_pic }" class="img-responsive" alt=""></td>
							<td class="text-center">${ $row.member_id }</td>
							<td>${ $fullname }</td>
							<td class="text-center">${ $row.create_time }</td>
							<td class="text-center"><?php echo $regis_type; ?></td>
							<td class="text-center"><input type="text" class="form-control" name="card_tail_number[${ $row.meeting_regis_id }]" value="${ $row.card_tail_number }" /></td>
							<td class="text-center"><input type="checkbox" name="is_gift[${ $row.meeting_regis_id }]" value="1"${ $row.is_gift == 1 ? ' checked' : '' } /></td>
						</tr>
					`);
				});
			}
		});
	}
  $(function() {
		$('body').on('click', '#btn-filter', function() {
			get_data();
		});

		$('body').on('keyup', '#filter', function() {
			get_data();
		});
		$('body').on('click', '#meeting_del-all', function() {
			$('.meeting_del').prop('checked', $('#meeting_del-all').prop('checked'))
		});

    $('body').on('click', '#btn-meeting_del', function() {
			if( $('.meeting_del:checked').length > 0 ) {
				$meeting_regis_id = [];
				$('.meeting_del:checked').each(function() {
					$meeting_regis_id.push( $(this).val() );
				});
				$.ajax({
						url: base_url+'/meeting/register_info_del',
						method: 'POST',
						data: {
							'meeting_regis_id': $meeting_regis_id.join(',')
						},
						async: true,
						success: function(msg){ console.log( msg );
							location.reload();
						}
					});
				console.log( $meeting_regis_id.join(',') );
			}
		});
    $('body').on('click', '#info-set', function() {
      $data = $('#frm_info').serializeArray();
      $.ajax({
        url: base_url+'/meeting/register_info_save',
        method: 'POST',
        data: $data,
				async: true,
        success: function(msg){
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
    });
  });
</script>