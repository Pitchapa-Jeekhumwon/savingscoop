<?php
function chkBrowser($nameBroser)
{
    return preg_match("/" . $nameBroser . "/", $_SERVER['HTTP_USER_AGENT']);
}
?>
<div class="layout-content">
    <div class="layout-content-body">
        <style>
            
            .font-normal{
		font-weight:normal;
	}
	.font-normal2{
		font-weight:bold;
		font-size:20px;
	}
	.font-normal3{
		font-weight:bold;
		font-size:16px;
	}
    
            .center {
                text-align: center;
            }

            .modal-dialog-account {
                margin: auto;
                margin-top: 7%;
            }

            .form-group {
                margin-bottom: 5px;
            }

            input[type=checkbox],
            input[type=radio] {
                margin: 11px 0 0;
            } 
	        th, td {
	    	text-align:center;
            }
            .modal-content.print{
	        border: 0px;
	        border-radius: 10px;
	        width:40%;
            }
        </style>
   
        <h1 style="margin-bottom: 0">ข้อมูลหุ้น</h1>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <form id="form1" method="POST" action="<?php echo base_url(PROJECTPATH . '/buy_share/save_share'); ?>">
                        <input type="hidden" id="member_id" name="member_id" value="<?php echo $member_id; ?>">
                        <?php $this->load->view('search_member_new'); ?>
                        <div class="" style="padding-top:0;">
                            <h3>ข้อมูลหุ้น</h3>
                            <div class="g24-col-sm-24">
                                <div class="form-group g24-col-sm-8">
                                    <label class="g24-col-sm-10 control-label ">ทุนเรือนหุ้นสะสม</label>
                                    <div class="g24-col-sm-14">
                                        <input class="form-control" type="text" name="share_payable" value="<?php echo $cal_share; ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group g24-col-sm-8">
                                    <label class="g24-col-sm-10 control-label ">ส่งหุ้นงวดละ</label>
                                    <div class="g24-col-sm-14">
                                        <input class="form-control" type="text" value="<?php echo number_format(@$row_member['share_month']); ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group g24-col-sm-8">
                                    <label class="g24-col-sm-10 control-label ">งวดที่</label>
                                    <div class="g24-col-sm-14">
                                        <input class="form-control" type="text" value="<?php echo number_format(@$share_period); ?>" readonly>
                                    </div>
                                </div>
                            </div>


                            <span style="display:none;"><a class="link-line-none" data-toggle="modal" data-target="#confirmSave" id="confirmSaveModal" class="fancybox_share fancybox.iframe" href="#"></a></span>

                            <span style="display:none;"><a class="link-line-none" data-toggle="modal" data-target="#alert" id="alertModal" class="fancybox_share fancybox.iframe" href="#"></a></span>
                          
                    </form><?php if($member_id != '')  {?>
                    	<?php if($_SESSION['USER_ID']==1){?>
                           
									<div class="g24-col-sm-24 text-right">
                                    <a class="link-line-none" href="book_share/book_member_share_pdf?member_id=<?php echo $member_id ?>" target="_blank">
					                <button  class="btn btn-info" style="width: 11%;" type="button" >
						                <span class="icon icon-print"></span>
						                    พิมพ์หน้าปกสมุดบัญชี
					                        </button>
				                        </a>
										<button type="button" class="btn btn-info btn_deposit" data-toggle="modal" data-target="#update_transaction" data-account="<?php echo $row_memberall['account_id'] ?>"> <span class="icon icon-arrow-circle-down"></span> อัพเดท ST </button>
									</div>
									<?php }?>
                    <div class="g24-col-sm-24 m-t-1">
                        <div class="bs-example" data-example-id="striped-table">
                            <table class="table table-bordered table-striped table-center">
                                <thead>
                                    <tr class="bg-primary">
                                        <th class = "font-normal">ลำดับ</th>
                                        <th class = "font-normal">วันที่</th>
                                        <th class = "font-normal">ประเภท</th>
                                        <th class = "font-normal">จำนวนเงิน</th>
                                        <th class = "font-normal">จำนวนเงินหุ้นคงเหลือ</th>
                                        <th class = "font-normal">หุ้นคงเหลือ</th>
                                        <th class = "font-normal">เลขที่เอกสาร</th>
                                        <th class = "font-normal">สถานะ</th>
                                        <th class = "font-normal">ผู้ทำรายการ</th>
                                        <th class="font-normal r_hidden" >
                                            <label class="custom-control custom-control-primary custom-checkbox " >
                                                <input type="checkbox" class="custom-control-input" id="share_check_all" name="" value="">
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-label">เลือกพิมพ์</span>
                                            </label>
                                        </th>
                                        
                                    </tr>
                                </thead>
                                <tbody >
                                    <?php
                                    $run_no_share = 0;
                                    if (!empty($data)) {
                                        foreach ($data as $key => $row_mem_share) {
                                            $run_no_share++;
                                    ?>
                                            <tr>
                                                <td><?php echo @$run_no_share; ?></td>
                                                <td><?php echo @$this->center_function->ConvertToThaiDate(@$row_mem_share['share_date']); ?></td>
                                                <td><?php echo @$row_mem_share['share_type']; ?></td>
                                                <td class="text-right"><?php echo number_format(@$row_mem_share['share_early_value'], 0); ?></td>
                                                <td class="text-right"><?php echo number_format(@$row_mem_share['share_collect_value'], 0); ?></td>
                                                <td class="text-right"><?php echo number_format(@$row_mem_share['share_collect'], 0); ?></td>
                                                <td class="text-center"><?php echo @$row_mem_share['share_bill']; ?></td>
                                                <td class="text-center"><?php echo @$share_type[@$row_mem_share['share_type']]; ?></td>
                                                <td class="text-center"><?php echo @$row_mem_share['share_id']; ?></td>
                                                <td class="r_hidden">
                                                    <label class="custom-control custom-control-primary custom-checkbox " style="">
                                                    <input type="checkbox" class="custom-control-input share_id_item select_print_slip" data-line="<?php echo $run_no_share; ?>" id="" name="share_id[]" value="<?php echo $row_mem_share['share_id'];?>">
                                                        <span class="custom-control-indicator" style="height: 20px; width: 20px;"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="10">ไม่พบข้อมูล</td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="page_wrap" style="text-align:center;">
                        <?php echo $paging ?>
                        		
			
                        <div class="row m-t-1 center">
		
					<button class="btn btn-primary btn-width-auto" type="button" onclick="check_show_no('<?php echo $member_id ?>')" >
						<span class="icon icon-print"></span>
                        <!-- data-toggle="modal" data-target="#modal_line_start" -->
						พิมพ์สมุดหุ้น	
					</button>		
			</div>
           
                    </div>
                    <?php   }?>
                </div>

            </div>
        </div>
    </div>
  
</div>
<!-- MODAL CONFIRM ERR TRANSACTION-->
<div class="modal fade" id="modal_line_start" role="dialog">
	<input type="hidden" name="line_start" id="line_start" value=""/>
    <div class="modal-dialog modal-sm">
      <div class="modal-content print">
        <!-- <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">กำหนดบรรทัดและเลขลำดับเริ่มต้นพิมพ์</h4>
        </div> -->
        <div class="modal-body">
        <h4 >กำหนดบรรทัดเริ่มต้นพิมพ์</h4>
			<select name="select_line_start" id="select_line_start" class="form-control" required>
				<option value="">พิมพ์ตามลำดับ</option>
				<?php
					for ($i=1; $i <= 20; $i++) {
						echo "<option value='".$i."'>".$i."</option>";
					}
				?>
			</select>
            <div  id="check_show_no" style="display: none;">
            <h4 >เลขลำดับเริ่มต้นพิมพ์</h4>
        <input id="input_show_no" name="input_show_no" class="form-control m-b-1" type="text" value="" placeholder="กรุณากรอกลำดับในสมุดทุนเรือนหุ้น" >
             </div>
        </div>
        <!-- <div class="modal-body" id="check_show_no" style="display: none;">
        <h4 >เลขลำดับเริ่มต้นพิมพ์</h4>
        <input id="input_show_no" name="input_show_no" class="form-control m-b-1" type="text" value="" placeholder="กรุณากรอกลำดับในสมุดทุนเรือนหุ้น" >
        </div> -->
        <div class="modal-footer text-center">
			<button class="btn btn-info" id="submit_select_line">ตกลง</button>
			<button class="btn btn-default" id="modal_line_start_close_btn">ยกเลิก</button>
        </div>
      </div>
    </div>
</div>
<!-- update_transaction -->
<div id="update_transaction" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-account">
		<div class="modal-content">
			<div class="modal-header modal-header-deposit">
				<h2 class="modal-title">อัพเดทยอดคงเหลือ</h2>
			</div>
			<div class="modal-body">
				<form action="?" method="POST">
					<input type="hidden" name="update_account_id"  value="<?=@$row_memberall['account_id']?>" id="update_account_id">
					<div class="g24-col-sm-24">
						<div class="form-group">
							<label for="money" class="control-label g24-col-sm-7">เลือกวันที่เริ่มการอัพเดท</label>
							<div class="g24-col-sm-5">
								<select name="update_day" id="update_day" class="form-control" required>
								<option value="">เลือกวันที่</option>
									<?php
										for ($i=1; $i <= 31; $i++) { 
											echo "<option value='".sprintf('%02d', $i)."'>".sprintf('%02d', $i)."</option>";
										}
									?>
								</select>
							</div>
							<div class="g24-col-sm-5">
								<select name="update_day" id="update_month" class="form-control" required>
								<option value="">เลือกเดือน</option>
									<?php
										for ($i=1; $i <= 12; $i++) { 
											echo "<option value='".sprintf('%02d', $i)."'>".sprintf('%02d', $i)."</option>";
										}
									?>
								</select>
							</div>
							<div class="g24-col-sm-5">
								<select name="update_day" id="update_year" class="form-control" required>
								<option value="">เลือกปี</option>
									<?php
										for ($i=(date('Y')+543); $i >= (date('Y')+543-10); $i--) { 
											echo "<option value='$i'>$i</option>";
										}
									?>
								</select>
							</div>
							<label class="control-label g24-col-sm-4">&nbsp;</label>
							
						</div>

						<label class="g24-col-sm-24"><i class="fa fa-info"></i> วิธีอัพเดท ให้เลือกวันที่ก่อนหน้า รายการที่ยอดคงเหลือผิด 1 รายการ</label>

						<div class="form-group">
							<div class="g24-col-sm-24 text-center m-t-2">
								<button class="btn btn-primary"  type="button" id="update_confirm">อัพเดท</button>
								<button class="btn btn-default bt_close" data-dismiss="modal" type="button">ยกเลิก </button>								
							</div>
						</div>
					</div>
				</form>
				<div>&nbsp;</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('search_member_new_modal'); ?>

<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH . 'assets/js/Buy_share.js?v=' . $v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
<script>
  
    // function get_share_id(share_id) {
    //     $('#share_id').val(share_id);
    // }
    function check_show_no(member_id) {
       
        
        $.ajax({
			url: base_url + "/book_share/check_show_no",
			method: "post",
			data: {member_id: member_id},
            dataType: 'json',
			success: function(data){
                console.log(data.result);
					if(data.result=="success"){		
                     $('#check_show_no').show();
                     $('#input_show_no').val(data.show_no);	
                    //  $('#input_show_no').attr('placeholder','ลำดับในสมุดทุนเรือนหุ้น '+data.show_no)
					}else{
                     $('#check_show_no').show();
                     $('#input_show_no').val('');	
					}

				}
		});
        $('#modal_line_start').modal('toggle');
        // $('#check_show_no').show();
    }
    $("#share_check_all").change(function() {
            if($("#share_check_all").attr('checked') == "checked"){
                $('.share_id_item').prop('checked', true)
            } else {
                $('.share_id_item').prop('checked', false)
            }
        });
        $(".share_id_item").change(function() {
            if($(this).attr('checked') != "checked"){
                $('#share_check_all').prop('checked', false)
            }
        });
        $("#modal_line_start_close_btn").on('click', function (){
			$('#modal_line_start').modal('toggle');
		});
        $("#submit_select_line").on('click', function (){
			$("#line_start").val($("#select_line_start").val())
            $("#show_no").val($("#input_show_no").val())
			$('#modal_line_start').modal('toggle');
            // $('#check_show_no').hide();	
           
			print_transaction()
		})

        function print_transaction() {
		var share_id = [];
		$(".share_id_item").each(function( index ) {
			if ($(this).attr('checked') == "checked"){
				share_id[$(this).attr('data-line')] = $(this).val()
			}
		});
		window.open(base_url+"book_share/book_share_statment_pdf?member_id=<?php echo $member_id?>&share_id="+JSON.stringify(share_id)+"&line_start="+$("#line_start").val()+"&show_no="+$("#input_show_no").val(), "_blank");
	}
    $("#update_confirm" ).click(function(){
	var d = $("#update_day").val();
	var m = $("#update_month").val();
	var y = $("#update_year").val();

	if(d=="" || m=="" || y==""){
		swal("เลือกวันที่ถูกต้อง", "warming");
		return;
	}

	$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/update_transaction_share',
			data: {
				date : (y-543) + '-' + m + '-' + d,
				member_id : $(".member_id").val()
			},
			success: function(data){
				console.log(data);
				if(data=="success"){
					
					swal("อัพเดทสำเร็จ", "อัพเดทข้อมูลเรียบร้อย", "success");
					setTimeout(() => {
						location.reload();
					}, 500);
					
				}else if(data=="fail"){
					swal("ไม่สามารถอัพเดทได้ ตรวจสอบวันที่ให้ถูกต้อง");
				}else{
					swal("ไม่สามารถอัพเดทได้ ตรวจสอบวันที่ให้ถูกต้อง");
				}

			}
	});	


});
</script>