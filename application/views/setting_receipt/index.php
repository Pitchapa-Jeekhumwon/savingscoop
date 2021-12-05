<?php
if($_GET['dev'] == 'dev'){
    echo '<pre>';print_r($side_menus);exit;
}
?>

<div class="layout-content">
    <div class="layout-content-body">
        <style>
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
            th {
                text-align: center;
            }
            .modal-dialog-cal {
                width:80% !important;
                margin:auto;
                margin-top:1%;
                margin-bottom:1%;
            }

            .modal-dialog-search {
                width: 700px;
            }
            .sidenav-heading {
                color: #9e9e9e;
                font-size: 12px;
                font-weight: 500;
                line-height: 1;
                margin-bottom: 0;
                margin-top: 15px;
                overflow: hidden;
                padding: 0px 0px;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-family: upbean;
                font-size: 18px;
            }
            .border_icon_web{
                border: 6px outset #067c3b;
                box-shadow: 3px 3px 5px #888888;
            }
            .border_icon_select{
                border: 4px outset #067c3b;
                box-shadow: 3px 3px 5px #888888;
            }
            .border_icon{
                border: 4px outset #067c3b;
                box-shadow: 3px 3px 5px #888888;
            }
        </style>
        <h1 style="margin-bottom: 0">ตั้งค่าใบเสร็จรับเงิน<?php echo $type == 1 ? "ระหว่างเดือน" : "รายเดือน"; ?></h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
        </div>

        <div class="row gutter-xs">
            <div class="col-md-12">
                <div class="panel panel-body" style="padding-top:15px !important;">
                    <form action="<?php echo base_url(PROJECTPATH.'/Setting_receipt/save_setting_receipt'); ?>" id="form1" method="POST">
                        <input type="hidden" name="type" value="<?php echo $type; ?>"/>
                        <div class="row">
                            <label class="col-sm-5 control-label" for="form-control-1">ตั่งค่าหน้ากระดาษ</label>
                            <div class="col-sm-2">
                                <select name="approval_id[<?php echo @$row['id']; ?>]" class="form-control">
                                    <?php foreach ($receipt_size as $key => $value){ ?>
                                        <option value="<?php echo $value['id']; ?>" <?php echo (@$value['id']==$setting_receipt['receipt_size_id'])?'selected':''; ?>><?php echo $value['name']; ?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <label class="col-sm-5 control-label"> ชื่อ-ที่อยู่สหกรณ์ </label>
                            <div class="g-24 col-sm-1">
                                <label class="radio-inline">
                                    <input type="radio" name="header_status" value="1" id="select_all_contract" <?php echo ($setting_receipt['header_status']=='1')?'checked':'';?>> มี
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="radio-inline">
                                    <input type="radio" name="header_status" value="0" id="select_contract" <?php echo ($setting_receipt['header_status']=='0')?'checked':'';?>> ไม่มี
                                </label>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <label class="col-sm-5 control-label"> สำเนา </label>
                            <div class="g-24 col-sm-1">
                                <label class="radio-inline">
                                    <input type="radio" name="copy_receipt" value="1" id="select_all_contract" <?php echo ($setting_receipt['copy_status']=='1')?'checked':'';?>> มี
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="radio-inline">
                                    <input type="radio" name="copy_receipt" value="0" id="select_contract" <?php echo ($setting_receipt['copy_status']=='0')?'checked':'';?>> ไม่มี
                                </label>
                            </div>
                        </div>
                        <!-- <div class="row" style="margin-top: 10px;">
                            <label class="col-sm-5 control-label"> แสดงชื่อเจ้าหน้าที่ผู้รับเงิน/ผู้จัดการ </label>
                            <div class="g-24 col-sm-1">
                                <label class="radio-inline">
                                    <input type="radio" name="sign_manager" value="1" id="select_all_contract" <?php echo ($setting_receipt['sign_manager']=='1')?'checked':'';?>> มี
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="radio-inline">
                                    <input type="radio" name="sign_manager" value="0" id="select_contract" <?php echo ($setting_receipt['sign_manager']=='0')?'checked':'';?>> ไม่มี
                                </label>
                            </div>
                        </div> -->
                        <div class="row" style="margin-top: 10px;">
                            <label class="col-sm-5 control-label"> ลายเซ็นล่างซ้าย </label>
                            <div class="g-24 col-sm-1">
                                <label class="radio-inline">
                                    <input type="radio" name="sign_1_check" value="1" id="sign_1_check_1" class="sign_1_radio" <?php echo (!empty($setting_receipt['sign_1']))?'checked':'';?>> มี
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="radio-inline">
                                    <input type="radio" name="sign_1_check" value="0" id="sign_1_check_2" class="sign_1_radio" <?php echo (empty($setting_receipt['sign_1']))?'checked':'';?>> ไม่มี
                                </label>
                            </div>
                        </div>
                        <div class="row" id="sign_1_div" style="margin-top: 10px; <?php echo !empty($setting_receipt['sign_1']) ? "" : "display:none";?>">
                            <label class="col-sm-5 control-label">เลือกเจ้าของลายเซ็นล่างซ้าย</label>
                            <div class="g-24 col-sm-5">
                                <select id="sign_1_select" name="sign_1" class="form-control">
                                    <option <?php echo $setting_receipt['sign_1'] == 1 ? "selected" : "";?> value='1'>เจ้าหน้าที่การเงิน</option>
                                    <option <?php echo $setting_receipt['sign_1'] == 4 ? "selected" : "";?> value='4'>เจ้าหน้าที่การเงิน 2</option>
                                    <option <?php echo $setting_receipt['sign_1'] == 2 ? "selected" : "";?> value='2'>หัวหน้าสินเชื่อ</option>
                                    <option <?php echo $setting_receipt['sign_1'] == 3 ? "selected" : "";?> value='3'>ผู้จัดการ</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <label class="col-sm-5 control-label"> ลายเซ็นล่างขวา </label>
                            <div class="g-24 col-sm-1">
                                <label class="radio-inline">
                                    <input type="radio" name="sign_2_check" value="1" id="sign_2_check_1" class="sign_2_radio" <?php echo (!empty($setting_receipt['sign_2']))?'checked':'';?>> มี
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="radio-inline">
                                    <input type="radio" name="sign_2_check" value="0" id="sign_2_check_2" class="sign_2_radio" <?php echo (empty($setting_receipt['sign_2']))?'checked':'';?>> ไม่มี
                                </label>
                            </div>
                        </div>
                        <div class="row" id="sign_2_div" style="margin-top: 10px; <?php echo !empty($setting_receipt['sign_2']) ? "" : "display:none";?>">
                            <label class="col-sm-5 control-label">เลือกเจ้าของลายเซ็นล่างขวา</label>
                            <div class="g-24 col-sm-5">
                                <select id="sign_2_select" name="sign_2" class="form-control">
                                    <option <?php echo $setting_receipt['sign_2'] == 1 ? "selected" : "";?> value='1'>เจ้าหน้าที่การเงิน</option>
                                    <option <?php echo $setting_receipt['sign_2'] == 4 ? "selected" : "";?> value='4'>เจ้าหน้าที่การเงิน 2</option>
                                    <option <?php echo $setting_receipt['sign_2'] == 2 ? "selected" : "";?> value='2'>หัวหน้าสินเชื่อ</option>
                                    <option <?php echo $setting_receipt['sign_2'] == 3 ? "selected" : "";?> value='3'>ผู้จัดการ</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <label class="col-sm-5 control-label"> ลายน้ำ </label>
                            <div class="g-24 col-sm-1">
                                <label class="radio-inline">
                                    <input type="radio" name="alpha" value="1" id="select_all_contract" <?php echo ($setting_receipt['alpha']=='1')?'checked':'';?>> มี
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="radio-inline">
                                    <input type="radio" name="alpha" value="0" id="select_contract" <?php echo ($setting_receipt['alpha']=='0')?'checked':'';?>> ไม่มี
                                </label>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <label class="col-sm-5 control-label"> ดอกเบี้ยคงค้าง </label>
                            <div class="g-24 col-sm-1">
                                <label class="radio-inline">
                                    <input type="radio" name="loan_int_debt" value="1" id="show_loan_int_debt" <?php echo ($setting_receipt['loan_int_debt']=='1')?'checked':'';?>> แสดง
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="radio-inline">
                                    <input type="radio" name="loan_int_debt" value="0" id="loan_int_debt" <?php echo ($setting_receipt['loan_int_debt']=='0')?'checked':'';?>> ไม่แสดง
                                </label>
                            </div>
                        </div>
                        <div class="form-group m-t-1">
                            <label class="col-sm-5 control-label" for="form-control-1"></label>
                            <div class="col-sm-5">
                                <button type="submit" class="btn btn-primary min-width-100">บันทึก</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    function open_modal(id){
        $('#'+id).modal('show');
    }
    $( document ).ready(function() {
        $('#search_member_modal').on('shown.bs.modal', function() {
            $('#search_member').focus();
        });
        $('#search_member').keyup(function(){
            var txt = $(this).val();
            if(txt != ''){
                $.ajax({
                    url:base_url+"/ajax/search_member_jquery",
                    method:"post",
                    data:{search:txt},
                    dataType:"text",
                    success:function(data)
                    {
                        //console.log(data);
                        $('#result_member_search').html(data);
                    }
                });
            }else{

            }
        });
        $(".sign_1_radio").click(function() {
            sign_1_status = $('input[name=sign_1_check]:checked', '#form1').val();
            console.log(sign_1_status)
            if(sign_1_status == 1) {
                $("#sign_1_div").show();
            } else {
                $("#sign_1_div").hide();
            }
        });
        $(".sign_2_radio").click(function() {
            sign_status = $('input[name=sign_2_check]:checked', '#form1').val();
            console.log(sign_status)
            if(sign_status == 1) {
                $("#sign_2_div").show();
            } else {
                $("#sign_2_div").hide();
            }
        });
    });
    function del_data(menu_id){
        swal({
                title: 'ท่านต้องการลบข้อมูลใช่หรือไม่',
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    // document.location.href = base_url+'Setting_main_menu/manage_emergency_individual_delete/'+non_pay_id;
                    $.ajax({
                        url:base_url+"/Setting_main_menu/del_data",
                        method:"post",
                        data:{
                            menu_id: menu_id
                        },
                        dataType:"text",
                        success:function(data)
                        {
                            if(data == 'success'){
                                swal("ลบ!", "ทำรายการสำเร็จแล้ว", "success");
                                location.reload()
                            }
                        }
                    });
                } else {

                }
            });
    }

</script>