<style>
	label { padding-top: 7px; }
	.form-r { background-color: #e0e0e0; }
	.table thead tr th { text-align: center; }
	.form-group { margin-bottom: 1em; }
</style>

<div class="layout-content">
	<div class="layout-content-body">
		<h1 style="margin-bottom: 0">รายชื่อลงทะเบียนใบหน้า</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">

					<div class="row">
						<div class="col-sm-12">
							<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
							<div class="col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="search_text" name="search_text" class="form-control m-b-1" placeholder="รหัสสมาชิก/ชื่อสกุล"
											   type="text" value="<?php echo $_GET["search_text"]; ?>">
										<span class="input-group-btn">
											<button type="button" onclick="check_search();"
													class="btn btn-info btn-search"><span
													class="icon icon-search"></span></button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="bs-example" data-example-id="striped-table">
						<table class="table table-striped">
							<thead>
									<tr>
									<th class="text-center" style="width: 80px;">ลำดับ</th>
									<th class="text-center" style="width: 120px;">รหัสสมาชิก</th>
									<th>ชื่อสกุล</th>
									<th class="text-center" style="width: 150px;">เวลาลงทะเบียน</th>
									<th class="text-center" style="width: 120px;">ใบหน้า</th>
									</tr>
							</thead>
							<tbody>
					<?php
					if(!empty($rs)){
						foreach(@$rs as $key => $row){ ?>
							<tr>
								<td class="text-center"><?php echo @$i--; ?></d>
								<td class="text-center"><?php echo @$row['member_id']; ?></td>
								<td><?php echo @$row['fullname']; ?></td>
								<td class="text-center"><?php echo $this->center_function->ConvertToThaiDate(@$row['createtime'], 1, 1); ?></td>
								<td class="text-center"><img src="<?php echo @$row['faceimg']; ?>" class="img-responsive" alt=""></td>
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

	function check_search() {
		location.href = "?search_text=" + encodeURI($('#search_text').val());
	}
	
	$(function () {
		$("#search_text").keyup(function(e) {
			var code = e.which; // recommended to use e.which, it's normalized across browsers
			if(code==13)e.preventDefault();
			if(code==32||code==13||code==188||code==186){
				check_search();
			}
		});

	});
</script>