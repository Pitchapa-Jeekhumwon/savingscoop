<style>
	.modal-dialog {
        width: 700px;
    }
</style>
<div class="layout-content">
    <div class="layout-content-body">
		<?php
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		?>
		<style>
			.modal-header-alert {
				padding:9px 15px;
				border:1px solid #FF0033;
				background-color: #FF0033;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}
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
		<h1 style="margin-bottom: 0">รายงาน statement เงินฝาก (ดอกเบี้ยสะสม)</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/report_account_accu_interest/coop_report_accu_interest_preview'); ?>" id="form1" method="GET" target="_blank">
					<h3></h3>
					<div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-7 control-label right">รหัสสมาชิก</label>
                        <div class="g24-col-sm-4">
                            <div class="input-group">
                                <input id="form-control-2"  class="form-control member_id" name="member_id" type="text" value="<?php echo $member_id; ?>" onkeypress="check_member_id();">
                                <span class="input-group-btn">
                                    <a data-toggle="modal" data-target="#myModal" id="test" class="fancybox_share fancybox.iframe" href="#">
                                        <button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                                    </a>
                                </span>
                            </div>
                        </div>
					</div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-7 control-label right">ชื่อสกุล</label>
                        <div class="g24-col-sm-9">
                                <input id="form-control-2" class="form-control " style="width:100%" type="text" value="<?php echo $member_name; ?>"  readonly>
                        </div>
					</div>
					
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-7 control-label right"> วันที่ </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
						<label class="g24-col-sm-1 control-label right"> ถึง </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="end_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
					</div>
					
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-7 control-label right"> เลขที่บัญชีเงินฝาก </label>
						<div class="g24-col-sm-4">
							<input id="start_account_id" name="start_account_id" class="form-control " type="text" value="">
						</div>
						<label class="g24-col-sm-1 control-label right"> ถึง </label>
						<div class="g24-col-sm-4">
							<input id="end_account_id" name="end_account_id" class="form-control " type="text" value="">
						</div>
					</div>
					
					
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-7 control-label right"></label>
						<div class="g24-col-sm-9">
							<input type="button" class="btn btn-primary" style="width:100%" value="แสดงข้อมูล" onclick="check_empty()">
						</div>
					</div>
				</form>				
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('search_member_new_modal'); ?>  
<script>	
	var base_url = $('#base_url').attr('class');
	$( document ).ready(function() {
		$(".mydate").datepicker({
			prevText : "ก่อนหน้า",
			nextText: "ถัดไป",
			currentText: "Today",
			changeMonth: true,
			changeYear: true,
			isBuddhist: true,
			monthNamesShort: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
			dayNamesMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
			constrainInput: true,
			dateFormat: "dd/mm/yy",
			yearRange: "c-50:c+10",
			autoclose: true,
		});
		
	});

	function check_empty(){
		var member_id = $('.member_id').first().val();
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();		
		var start_account_id = $('#start_account_id').val();
		var end_account_id = $('#end_account_id').val();
		
		var alert_text = '';
		if(member_id.trim()==''){
			alert_text += '- รหัสสมาชิก\n';
		}
		if(start_date.trim()==''){
			alert_text += '- วันที่เริ่มต้น\n';
		}
		if(end_date.trim()==''){
			alert_text += '- วันที่สิ้นสุด\n';
		}

		var d1 = start_date.split("/");
		var d2 = end_date.split("/");
		
		var start_date_eng = new Date(((d1[2]-543)+"-"+d1[1]+"-"+d1[0]));
		var end_date_eng = new Date(((d2[2]-543)+"-"+d2[1]+"-"+d2[0]));

		if ((Date.parse(end_date_eng) < Date.parse(start_date_eng))) {	
			alert_text += '- วันที่สิ้นสุดต้องมากกว่าวันที่เริ่มต้น\n';
		}
		
		if(alert_text!=''){
			swal('กรุณากรอกข้อมูลต่อไปนี้' , alert_text , 'warning');
		}else{
			$.ajax({
				url: base_url+'/report_account_accu_interest/check_report_accu_interest',	
				 method:"post",
				 data:{ 
					 member_id: member_id, 
					 start_date: start_date, 
					 end_date: end_date,
					 start_account_id: start_account_id,
					 end_account_id: end_account_id,
				 },
				 dataType:"text",
				 success:function(data){
					//console.log(data);
					if(data == 'success'){
						$('#form1').submit();
					}else{
						$('#alertNotFindModal').appendTo("body").modal('show');
					}
				 }
			});
		}
	}

	$('#member_search').click(function(){
        if($('#search_list').val() == '') {
            swal('กรุณาเลือกรูปแบบค้นหา','','warning');
        } else if ($('#search_text').val() == ''){
            swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
        } else {
            $.ajax({
                url: base_url+"ajax/search_member_by_type",
              method:"post",
              data: {
                search_text : $('#search_text').val(),
                search_list : $('#search_list').val()
              },  
              dataType:"text",  
              success:function(data) {
                $('#result_member').html(data);
              }  ,
              error: function(xhr){
                  console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
              }
          });  
      }
    });

    function check_member_id() {
        var member_id = $('.member_id').first().val();
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            $.post(base_url+"save_money/check_member_id",
            {
                member_id: member_id
            }
            , function(result){
                obj = JSON.parse(result);
                mem_id = obj.member_id;
                if(mem_id != undefined){
                    document.location.href = '<?php echo base_url(uri_string())?>?member_id='+mem_id
                }else{
                    swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning');
                }
            });
        }
    }	
</script>


