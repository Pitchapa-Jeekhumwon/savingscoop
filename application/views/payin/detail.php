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
            .bt-add {
                margin-left: 10px;
            }
        </style>
        <h1 style="margin-bottom: 0"><?php echo $type == 0 ? "ซื้อหุ้น" : ($type == 1 ? "ชำระหนี้" : ($type == 2 ? "ฝากเงิน" : "ไม่พบข้อมูล"))?></h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0 text-right">
            <?php
                if(($type == 0 && $share_status == 1) || ($type == 2 && $dept_status == 1)) {
            ?>
                <button class="btn btn-primary btn-lg bt-add" type="button" id="approve">
                    อนุมัติการนำเข้า
                </button>
            <?php
                } else {
            ?>
                <input id="pdf-btn" class="form-control m-b-1 btn btn-primary" type="button" value="Pdf">
                <input id="excel-btn" class="form-control m-b-1 btn btn-primary" type="button" value="Excel">
            <?php
                }
            ?>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="bs-example" data-example-id="striped-table">
                        <div class="form-group g24-col-sm-24 text-center">
                            <h3>KTB <?php echo $this->center_function->ConvertToThaiDate($import_at,'1','0');?></h3>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr class="bg-primary">
                                    <th class="font-normal" width="4%">#</th>
                                    <th class="font-normal" width="10%"> วันที่เวลา </th>
                                    <th class="font-normal" width="10%"> รหัสสมาชิก </th>
                                    <th class="font-normal" width="20%"> ชื่อสมาชิก </th>
                                    <th class="font-normal" width="10%"> REF2 </th>
                                    <th class="font-normal" width=""> รายละเอียด </th>
                                    <th class="font-normal" width="10%"> ยอดเงิน </th>
                                    <?php
                                        if($type != 1) {
                                    ?>
                                    <th class="font-normal" width="10%"> สถานะ </th>
                                    <?php
                                        }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $i = 1;
                                if(!empty($datas)) {
                                    foreach($datas as $data) {
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++;?></td>
                                    <td class="text-center">
                                        <?php
                                            if(!empty($data['payment_date'])) {
                                                $paydate = substr($data['payment_date'], 4, 4)."-".substr($data['payment_date'], 2, 2)."-".substr($data['payment_date'], 0, 2);
                                                if(!empty($data['payment_time'])) {
                                                    $paydate .= " ".substr($data['payment_time'], 0, 2).":".substr($data['payment_time'], 2, 2).":".substr($data['payment_time'], 4, 2);
                                                }
                                                echo $this->center_function->ConvertToThaiDate($paydate,'1','1');
                                            } else {
                                                echo "-";
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center"><?php echo !empty($data['member_id']) ? $data['member_id'] : "-";?></td>
                                    <td class="text-left"><?php echo !empty($data['firstname_th']) ? $data['prename_short'].$data['firstname_th']." ".$data['lastname_th'] : "-";?></td>
                                    <td class="text-center"><?php echo !empty($data['ref2']) ? $data['ref2'] : "-";?></td>
                                    <td class="text-left">
                                    <?php
                                        $detail = "-";
                                        if($type == 0) {
                                            $detail = "ซื้อหุ้น";
                                        } else if ($type == 1) {
                                            $txt = "";
                                            foreach($data['contract_number'] as $contract_number) {
                                                $txt .= $txt == "" ? $contract_number : ", ".$contract_number;
                                            }
                                            $detail = "ชำระหนี้ สัญญา ".$txt;
                                        } else if ($type == 2) {
                                            $txt = "";
                                            $acc_nos = "";
                                            foreach($data['deptaccount_no'] as $deptaccount_no) {
                                                $txt .= $txt == "" ? $deptaccount_no : ", ".$deptaccount_no;
                                                $acc_nos .= $acc_nos == "" ? $deptaccount_no : ",".$deptaccount_no;
                                            }
                                            $detail = "เงินฝาก ".$txt;
                                        }
                                        echo $detail;
                                    ?>
                                    </td>
                                    <td class="text-right"><?php echo !empty($data['amount']) ? number_format($data['amount'], 2) : "-";?></td>
                                    
                                    <?php
                                        if ($type == 1) {
                                    ?>
                                    <?php
                                        }
                                        else if($type == 4) {
                                    ?>
                                    <td class="text-center text-danger">
                                        ไม่พบข้อมูล
                                    </td>
                                    <?php
                                        } else if ($data['status'] == 1) {
                                    ?>
                                    <td class="text-center text-danger">
                                        รอดำเนินการ
                                    </td>
                                    <?php
                                        } else if ($data['status'] == 3) {
                                    ?>
                                    <td class="text-center text-danger">
                                    <?php
                                        if($type == 0) {
                                    ?>
                                        <a href="#" class="access_btn" id="btn_<?php echo $data['id'];?>" data-id="<?php echo $data['id'];?>" data-type="<?php echo $type;?>"
                                                data-value="<?php echo $data['ref_data'];?>" data-member-id="<?php echo $data['member_id']?>">ดำเนินการแล้ว</a>
                                    <?php
                                        } else if ($type == 2) {
                                    ?>
                                        <a href="#" class="access_btn" id="btn_<?php echo $data['id'];?>" data-id="<?php echo $data['id'];?>" data-type="<?php echo $type;?>"
                                            data-value="<?php echo $data['ref_data'];?>" data-member-id="<?php echo $data['member_id']?>" data-acc-no="<?php echo $acc_nos;?>">ดำเนินการแล้ว</a>
                                    <?php
                                        }
                                    ?>
                                    </td>
                                    <?php
                                        }
                                    ?>
                                </tr>
                            <?php
                                    }
                                } else {
                            ?>
                                <tr>
                                    <td class="text-center" colspan="8">ไม่พบข้อมูล</td>
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
<form action="" method="post" id="approve_form">
    <input type="hidden" name="type" id="form_type" value="<?php echo $type;?>">
    <input type="hidden" name="id" id="form_id" value="<?php echo $id;?>">
</form>
<form action="<?php echo base_url(PROJECTPATH.'/payin/details'); ?>" method="post" id="access_form">
    <input type="hidden" name="type" id="access_type" value="<?php echo $type;?>">
    <input type="hidden" name="id" id="access_id" value="<?php echo $id;?>">
</form>
<form action="<?php echo base_url(PROJECTPATH.'/payin/export_doc'); ?>" target="_blank" method="post" id="doc_form">
    <input type="hidden" name="type" id="doc_type" value="<?php echo $type;?>">
    <input type="hidden" name="id" id="doc_id" value="<?php echo $id;?>">
    <input type="hidden" name="doc_type" id="doc_doc_type" value="">
</form>
<script>
    $(document).ready(function() {
       $("#approve").click(function() {
            swal({
				title: "คุณต้องการอนุมัติ",
				text: "",
				type: "warning",
				showCancelButton: true,
				confirmButtonText: 'ยืนยัน',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: true,
				closeOnCancel: true
			},
			function (isConfirm) {
				if (isConfirm) {
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
                    $.ajax({
                        url: base_url + "payin/approve_import_data",
                        type: 'post',
                        data: $("#approve_form").serialize(),
                        dataType: "text",
                        success: function(result){
                            data = JSON.parse(result);
                            $.unblockUI();
                            if(data.status == 'success') {
                                swal('ทำรายการเสร็จสมบูรณ์', "", 'success');
                                $("#access_form").submit();
                                $.unblockUI();
                            } else {
                                $.unblockUI();
                                swal('เกิดขึ้นผิดพลาด', data.massage, 'warning');
                            }
                        },
                    });
				}
			});
        });

        $(".access_btn").click(function() {
            type = $(this).attr('data-type');
            if(type == 0) {
                member_id = $(this).attr('data-member-id');
                window.open(base_url+'buy_share?member_id='+member_id, '_blank');
            } else if (type == 2) {
                $(this).attr("data-acc-no").split(",").forEach(function(acc_no) {
                    window.open(base_url+'save_money/account_detail?account_id='+acc_no, '_blank');
                });
            }
        });
        $("#pdf-btn").click(function() {
            $("#doc_doc_type").val("pdf");
            $("#doc_form").submit();
        });
        $("#excel-btn").click(function() {
            $("#doc_doc_type").val("excel");
            $("#doc_form").submit();
        });
    });
</script>
