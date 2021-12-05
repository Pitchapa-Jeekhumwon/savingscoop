<div class="layout-content">
    <div class="layout-content-body">
	<style>
		label{
			padding-top:7px;
		}
		.control-label{
			padding-top:7px;
			text-align:right;
		}
		.indent{
			text-indent: 40px;
			.modal-dialog-data {
				width:90% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
		}
		.bt-add{
			float:none;
		}
		.modal-dialog{
			width:80%;
		}
		small{
			display: none !important;
		}
		.cke_contents{
			height: 500px !important;
		}
		th{
			text-align:center;
		}
		.money {
			width: 250% !important;
			left: 0 !important;
    		margin-left: -80% !important;
			}
			.month{

			}
		.popup {
  				position: relative;
  				display: inline-block;
  				cursor: pointer;
			}

			/* The actual popup (appears on top) */
			.popup .popuptext {
  				visibility: hidden;
 				width: 160px;
  				background-color: #555;
  				color: #fff;
  				text-align: center;
  				border-radius: 6px;
  				padding: 8px 0;
  				position: absolute;
  				z-index: 1;
  				bottom: 125%;
  				left: 50%;
  				margin-left: -80px;
			}

			/* Popup arrow */
			.popup .popuptext::after {
  				content: "";
  				position: absolute;
  				top: 100%;
  				left: 50%;
  				margin-left: -5px;
  				border-width: 5px;
  				border-style: solid;
  				border-color: #555 transparent transparent transparent;
			}

			/* Toggle this class when clicking on the popup container (hide and show the popup) */
			.popup .show {
  				visibility: visible;
  				-webkit-animation: fadeIn 1s;
 				 animation: fadeIn 1s
			}

			/* Add animation (fade in the popup) */
			@-webkit-keyframes fadeIn {
  				from {opacity: 0;}
  				to {opacity: 1;}
			}
			@keyframes fadeIn {
  				from {opacity: 0;}
  				to {opacity:1 ;}
			}
			.interest_tb{
				width: 100%;
    			max-width: 100%;
				margin: 1%;
			}
	</style>
		<h1 style="margin-bottom: 0">ประเภทเงินฝาก</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">	
				<a href="<?php echo base_url(PROJECTPATH.'/setting_deposit_data/coop_deposit_type_setting_detail_add?type_id='.@$_GET['type_id']); ?>">
					<button class="btn btn-primary btn-lg bt-add" type="button"><span class="icon icon-plus-circle"></span> เพิ่มรายการ</button> 
				</a>
				<a href="<?php echo base_url(PROJECTPATH.'/setting_deposit_data/coop_deposit_type_setting'); ?>">
					<button class="btn btn-primary btn-lg bt-add" type="button"><span class="icon icon-chevron-left"></span> กลับหน้าหลัก </button> 
				</a>
			</div>
		</div>	
		<div class="row gutter-xs">
			  <div class="col-xs-12 col-md-12">
					<div class="panel panel-body">
						<h1 class="text-left m-t-1 m-b-1"><?php echo @$row['type_code'].'  '.@$row['type_name']; ?></h1>
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-striped"> 
								<thead> 
									 <tr>
										<th class = "font-normal" width="5%">ลำดับ</th>
										<th class = "font-normal"> วันที่เพิ่ม </th>
										<th class = "font-normal"> วันที่มีผล </th>
										<th class = "font-normal"> อัตราดอกเบี้ย </th>
										<th class = "font-normal"> สถานะ </th>
										<th class = "font-normal" style="width: 150px;"> จัดการ </th>
									</tr> 
								</thead>
								<tbody>
							 <?php  
								$i = 1;
								if(!empty($rs_detail)){
									foreach(@$rs_detail as $key => $row_detail){ 
							?>
									<tr> 
									  <td scope="row" align="center"><?php echo $i++; ?></td>
									  <td align="center"><?php echo $this->center_function->ConvertToThaiDate(@$row_detail['createdatetime']); ?></td> 
									  <td align="center"><?php echo $this->center_function->ConvertToThaiDate(@$row_detail['start_date'],1,0); ?></td> 
									  <?php if(@$row_detail['condition_interest'] == '1'):?>
										<td align="center"><?=@$row_detail['percent_interest'] ?></td> 
										<?php else :?>
											<td align="center" >
												<div class="popup" onclick="interest_popup(this)" style="color:#c04b13;"> <?=@$text_interest[@$row_detail['condition_interest']]?>
													<div class="popuptext <?=(@$row_detail['condition_interest'] == 2 )? "month":"money" ?>">
														<table class="interest_tb">
														<?php foreach(@$row_detail['percent_interest'] as $k => $v): ?>
														<tr>
															<?php if(@$row_detail['condition_interest']  == 2 ): ?>
																<td style="width: 25%;">เดือนที่</td> <td style="width: 10%;"><?=$v['num_month']?></td>
															<?php elseif(@$row_detail['condition_interest']  == 3 ): ?>
																<td style="width: 25%;">จำนวนเงิน</td> <td style="width: 30%;text-align:right;"><?=number_format($v['amount_deposit'],2)?></td>
															<?php else: ?>
																<td colspan="2"></td> 
															<?php endif;?>
															<td style="width: auto;">ดอกเบี้ย  <?=number_format($v['percent_interest'],2)?>%</td>
														<?php endforeach ;?>
														</table>
													</div>
												</div>
											</td>
										<?php endif ?>
									  <td align="center"><?php echo @$row_status['type_detail_id']==@$row_detail['type_detail_id']?'<span style="color:green">ใช้งาน</span>':'ไม่ใช้งาน'; ?></td> 
									  <td align="center">
										  <a href="<?php echo base_url(PROJECTPATH.'/setting_deposit_data/coop_deposit_type_setting_detail_add?type_id='.@$row_detail['type_id']."&type_detail_id=".@$row_detail['type_detail_id']."&act=copy"); ?>">คัดลอก</a> |
										  <a href="<?php echo base_url(PROJECTPATH.'/setting_deposit_data/coop_deposit_type_setting_detail_add?type_id='.@$row_detail['type_id']."&type_detail_id=".@$row_detail['type_detail_id']); ?>">แก้ไข</a> |
										  <a href="#" onclick="del_coop_detail_data('<?php echo @$row_detail["type_id"]; ?>','<?php echo @$row_detail["type_detail_id"]; ?>')" class="text-del"> ลบ </a> 
									  </td> 
									</tr>
							<?php 
									}
								} 
							?>
								 </tbody> 
							  </table> 
						</div>
					</div>
					<?php echo @$paging ?>
				 </div>
		  </div>
	</div>
</div>  
<script>
var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
    $('body').click(e=>{
		var target = $( e.target );
		if(!target.is('div.popup')){
			if($('.popuptext').hasClass('show')){
				$('.popuptext').removeClass('show');
			}
		}
	});
});
	function del_coop_detail_data(type_id,type_detail_id){
		swal({
			title: "",
			text: "ท่านต้องการลบข้อมูลใช่หรือไม่?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ยืนยัน',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: false,
			closeOnCancel: true
		 },
		 function(isConfirm){
		   if (isConfirm){
				document.location.href = base_url+'setting_deposit_data/coop_deposit_type_setting_detail_delete?type_id='+type_id+'&type_detail_id='+type_detail_id;
			}
		 });
	}
	function interest_popup(elmnt){
		if($('.popuptext').hasClass('show')){
			$('.popuptext').removeClass('show');
		}else{
			$(elmnt).children('div').toggleClass('show');
		}
}
</script> 