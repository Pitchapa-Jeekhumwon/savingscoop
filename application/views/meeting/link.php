<div class="layout-content">
	<div class="layout-content-body">
		<h1 style="margin-bottom: 0">ระบบสรรหา</h1>
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
					<h2 class="text-center">QR CODE ลงทะเบียน</h2>
					<div class="text-center">
						<img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=<?php echo WEB_BASE_URL."/facescan"; ?>&choe=UTF-8" alt="">
					</div>
					<div class="row">
						<label class="col-sm-3 control-label" for="meeting_name">หรือ ส่งลิงก์นี้ให้สมาชิก</label>
						<div class="col-sm-6">
							<div class="form-group">
								<input id="url" name="url" class="form-control m-b-1" type="text" value="<?php echo WEB_BASE_URL."/facescan"; ?>" readonly="readonly">
							</div>
						</div>
						<div class="col-sm-3">
							<button type="button" class="btn btn-primary btn-block" onclick="copy_url()">คัดลอกลิงก์</button>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-3 control-label" for="meeting_name">สถานะการลงทะเบียน</label>
						<div class="col-sm-9">
							<div class="form-group">
								<label style="padding-top: 7px;"><input type="radio" name="is_facescan_regis" value="1"<?php if($is_facescan_regis == 1) { ?> checked="checked"<?php } ?> class="is_facescan_regis"> เปิดให้ลงทะเบียน</label> &nbsp;
								<label style="padding-top: 7px;"><input type="radio" name="is_facescan_regis" value="0"<?php if($is_facescan_regis == 0) { ?> checked="checked"<?php } ?> class="is_facescan_regis"> ปิดการลงทะเบียน</label>
							</div>
						</div>
					</div>
				</div><!-- End panel panel-body  -->
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('search_member_modal_jquery'); ?>

<script src="/assets/js/elephant.min.js"></script>
<script src="/assets/js/application.min.js"></script>
<script src="/assets/js/jquery.blockUI.js"></script>
<script src="/assets/js/sweetalert.min.js"></script>

<script>
	var base_url = $('#base_url').attr('class');
	var web_base_url = "<?php echo WEB_BASE_URL; ?>";

	$(document).ready(function() {
		$(".is_facescan_regis").click(function() {
			$.ajax({
				url: base_url + "/meeting/link/set_facescan_regis",
				method: "POST",
				data: {
					"is_facescan_regis": $(".is_facescan_regis:checked").val()
				},
				success: function(msg){
					data = jQuery.parseJSON(msg);
					
				}
			});
		});
	});
	
	function copy_url() {
		var copyText = document.getElementById("url");
		copyText.select();
		copyText.setSelectionRange(0, 99999);
		document.execCommand("copy");
	} 
</script>