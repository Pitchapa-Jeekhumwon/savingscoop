<div class="layout-content">
    <div class="layout-content-body">
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

            @media (min-width: 768px) {
                .modal-dialog {
                    width: 700px;
                }
            }
            .form-group{
                margin-bottom: 5px;
            }
		</style>

		<h1 style="margin-bottom: 0">รายงานข้อมูลคำขอกู้</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="g24col-xs-24 g24col-md-24">
				<div class="panel panel-body" style="padding-top:50px !important; padding-bottom: 50px !important;">
                <form id="form" method="GET">
					<div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-7 control-label right">รหัสสมาชิก</label>
                        <div class="g24-col-sm-8">
                            <div class="input-group">
                                <input id="form-control-2"  class="form-control member_id" type="text" value="<?php echo $member_id; ?>" onkeypress="check_member_id();">
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
                        <div class="g24-col-sm-8">
                                <input id="form-control-2" class="form-control " style="width:100%" type="text" value="<?php echo $member_name; ?>"  readonly>
                        </div>
					</div>
                    </from>
				
                    <div class="form-group g24-col-sm-24">
                    <div class="g24-col-sm-12 control-label right" onclick="print_pdf('<?php echo $data['member_id']; ?>')">
                        <a class="link-line-none" href="coop_report_loan_person_pdf?member_id=<?php echo $member_id ?>" target="_blank">
				            <button class="btn btn-primary btn-lg bt-add" type="button" style="margin-top: 5px;">
					        <span class="icon icon-print"></span>
					            พิมพ์เอกสาร PDF
				            </button>
				        </a>
                    </div>
                    </div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">ข้อมูลสมาชิก</h4>
        </div>
        <div class="modal-body">
       		<div class="input-with-icon">
                <div class="row">
                    <div class="col">
                        <label class="col-sm-2 control-label">รูปแบบค้นหา</label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select id="search_list" name="search_list" class="form-control m-b-1">
                                    <option value="">เลือกรูปแบบค้นหา</option>
                                    <option value="member_id">รหัสสมาชิก</option>
                                    <option value="id_card">หมายเลขบัตรประชาชน</option>
                                    <option value="firstname_th">ชื่อสมาชิก</option>
                                    <option value="lastname_th">นามสกุล</option>
                                </select>
                            </div>
                        </div>
                        <label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="input-group">
                                    <input id="search_text" name="search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>">
                                    <span class="input-group-btn">
                                        <button type="button" id="member_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div class="bs-example" data-example-id="striped-table">
				<table class="table table-striped">
                    <tbody id="result_member">
                    </tbody>
				</table>
			</div>
        </div>
        <div class="modal-footer">
            <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
        </div>
      </div>
    </div>
  </div>

<script>
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
            },
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


