<div class="layout-content">
    <div class="layout-content-body">
        <style>
            input[type=number]::-webkit-inner-spin-button,
            input[type=number]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            th, td {
                text-align: center;
            }
            .modal-dialog-delete {
                margin:0 auto;
                width: 350px;
                margin-top: 8%;
            }
            .modal-dialog-account {
                margin:auto;
                width: 70%;
                margin-top:7%;
            }
            .control-label {
                text-align:right;
                padding-top:5px;
            }
            .text_left {
                text-align:left;
            }
            .text_right {
                text-align:right;
            }
            .modal {
                overflow-x: hidden;
                overflow-y: auto;
            }
        </style>
        <h1 style="margin-bottom: 0">นำเข้าข้อมูลไฟล์ข้อมูล</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="bs-example" data-example-id="striped-table">
                        <div class="form-group g24-col-sm-24">
                            <form data-toggle="validator" method="post" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_save">
                                <label class="g24-col control-label text-left"> แนบไฟล์นำเข้าจาก KTB &nbsp;&nbsp; &nbsp;&nbsp; </label>
                                <div class="g24-col">
                                    <div class="file-field">
                                        <div class="file-path-wrapper">
                                            <input class="form-control" type="text" placeholder="เลือกไฟล์นำเข้า" id="a_input_file" value="" readonly>
                                            <input type="file" id="import_file" name="import_file" style="display:none">
                                        </div>
                                    </div>
                                </div>
                                <div class="g24-col-sm-4">
                                    <input type="button" class="btn btn-primary" id="import_btn" value="นำเข้า">
                                </div>
                            </form>
						</div>
                        <table class="table table-bordered table-striped table-center">
                            <thead>
                                <tr class="bg-primary">
                                    <th class="font-normal" width="10%">วันที่นำเข้า</th>
                                    <th class="font-normal"> ข้อมูลจาก </th>
                                    <th class="font-normal" width="15%"> ฝากเงิน </th>
                                    <th class="font-normal" width="15%"> ชำระหนี้ </th>
                                    <th class="font-normal" width="15%"> ซื้อหุ้น </th>
                                    <th class="font-normal" width="15%"> ไม่พบข้อมูล </th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                if(!empty($datas)) {
                                    foreach($datas as $data) {
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $this->center_function->ConvertToThaiDate($data['import_at'],'1','0');?></td>
                                    <td class="text-left"><?php echo $data['bank_code'] == 6 ? 'KTB' : "";?></td>
                                    <td class="text-center">
                                    <?php
                                        if($data['dept_status'] == 0) {
                                    ?>
                                        ไม่มีข้อมูล
                                    <?php
                                        } else {
                                    ?>
                                        <a href="#" class="access_btn" id="dept_access_<?php echo $data['id'];?>" data-id="<?php echo $data['id'];?>" data-type="2"><?php echo $data['dept_status'] == 1 ? 'รอตรวจสอบ' : ($data['dept_status'] == 2 ? 'รอตรวจสอบ' : 'ดูข้อมูล');?></a>
                                    <?php
                                        }
                                    ?>
                                    </td>
                                    <td class="text-center">
                                    <?php
                                        if($data['loan_status'] == 0) {
                                    ?>
                                        ไม่มีข้อมูล
                                    <?php
                                        } else {
                                    ?>
                                        <a href="#" class="access_btn" id="loan_access_<?php echo $data['id'];?>" data-id="<?php echo $data['id'];?>" data-type="1">ดูข้อมูล</a>
                                    <?php
                                        }
                                    ?>
                                    </td>
                                    <td class="text-center">
                                    <?php
                                        if($data['share_status'] == 0) {
                                    ?>
                                        ไม่มีข้อมูล
                                    <?php
                                        } else {
                                    ?>
                                        <a href="#" class="access_btn" id="share_access_<?php echo $data['id'];?>" data-id="<?php echo $data['id'];?>" data-type="0"><?php echo $data['share_status'] == 1 ? 'รอตรวจสอบ' : ($data['share_status'] == 2 ? 'รอตรวจสอบ' : 'ดูข้อมูล');?></a>
                                    <?php
                                        }
                                    ?>
                                    </td>
                                    <td class="text-center">
                                    <?php
                                        if($data['unknow_status'] == 0) {
                                    ?>
                                        ไม่มีข้อมูล
                                    <?php
                                        } else {
                                    ?>
                                        <a href="#" class="access_btn" id="unknow_access_<?php echo $data['id'];?>" data-id="<?php echo $data['id'];?>" data-type="4">ดูข้อมูล</a>
                                    <?php
                                        }
                                    ?>
                                    </td>
                                </tr>
                            <?php
                                    }
                                } else {
                            ?>
                                <tr>
                                    <td class="text-center" colspan="6">ไม่พบข้อมูล</td>
                                </tr>
                            <?php
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
<form action="<?php echo base_url(PROJECTPATH.'/payin/details'); ?>" method="post" id="access_form">
    <input type="hidden" name="type" id="form_type" value="">
    <input type="hidden" name="id" id="form_id" value="">
</form>
<?php
$v = date('YmdHis');
?>
<script>
    $(document).ready(function() {
        $("#a_input_file").click(function() {
            $("#import_file").click();
        });
        $("#import_file").change(function (){
            var fileName = $(this).val();
            $("#a_input_file").attr('placeholder', fileName.split(/(\\|\/)/g).pop());
        });
        $("#import_btn").click(function() {
            if($('#import_file')[0].files.length > 0) {
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
                    baseZ: 6000,
                    bindEvents: false
                });
                var form_data = new FormData();
                var files = $('#import_file')[0].files[0];
                form_data.append('file', files);
                $.ajax({
                    url: base_url + "payin/save_import_data",
                    type: 'post',
                    data: form_data,
                    contentType: false,
                    processData: false,
                    success: function(result){
                        data = JSON.parse(result);
                        if(data.status == 'success') {
                            swal('ทำรายการเสร็จสมบูรณ์', "", 'success');
                            location.reload();
                            $.unblockUI();
                        } else {
                            $.unblockUI();
                            swal('เกิดขึ้นผิดพลาด', data.massage, 'warning');
                        }
                    },
                });
            } else {
                swal('เกิดขึ้นผิดพลาด',"กรุณาเลือกไฟล์ก่อนทำรายการ ด้วยการคลิกที่ช่อง 'เลือกไฟล์นำเข้า'", 'warning');
            }
        });
        $(".access_btn").click(function() {
            $("#form_id").val($(this).attr('data-id'));
            $("#form_type").val($(this).attr('data-type'));
            $("#access_form").submit();
        });
    });
</script>