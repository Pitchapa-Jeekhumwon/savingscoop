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
            .form-group{
                margin-bottom: 5px;
            }
        </style>
        <h1 style="margin-bottom: 0">แก้ไขรายการเรียกเก็บประจำเดือน</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <h3></h3>
                    <form method="POST" id="search_form" action="<?php echo base_url(PROJECTPATH.'/finance/edit_finance_month'); ?>">
                        <div class="form-group g24-col-sm-24">
                            <input type="hidden" name="yymm_now" id="yymm_now" value="<?php echo (date('Y')+543).date('m');?>">
                            <label class="g24-col-sm-6 control-label right"> เดือน </label>
                            <div class="g24-col-sm-4">
                                <select id="month" name="month" class="form-control">
                                    <?php foreach($month_arr as $key => $value){ ?>
                                        <?php
                                        if($key < ((int)date('m'))){
                                            //$check_disabled = "disabled";
                                            $check_disabled = "";
                                        }else{
                                            $check_disabled = "";
                                        }
                                        ?>
                                        <option value="<?php echo $key; ?>" <?php echo (empty($_POST['month']) && $key==((int)date('m'))) || (!empty($_POST['month']) && $_POST['month']==$key)?'selected':''; ?> <?php echo $check_disabled;?>><?php echo $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label class="g24-col-sm-1 control-label right"> ปี </label>
                            <div class="g24-col-sm-4">
                                <select id="year" name="year" class="form-control">
                                    <?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
                                        <option value="<?php echo $i; ?>" <?php echo (empty($_POST['year']) && $i==(date('Y')+543)) || (!empty($_POST['year']) && $i == $_POST['year'])?'selected':''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> สังกัดหน่วยงาน </label>
                            <div class="g24-col-sm-9">
                                <select name="department" id="department" onchange="change_mem_group('department', 'faction')" class="form-control">
                                    <option value="">เลือกข้อมูล</option>
                                    <?php
                                    foreach($row_mem_group as $key => $value){
                                        ?>
                                        <option value="<?php echo $value['id']; ?>" <?php echo @$_POST['department']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
                                        <?php
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> สังกัดย่อย  </label>
                            <div class="g24-col-sm-9">
                                <select name="faction" id="faction" onchange="change_mem_group('faction','level')" class="form-control">
                                    <option value="">เลือกข้อมูล</option>
                                    <?php
                                        foreach($faction as $key => $value){
                                            ?>
                                            <option value="<?php echo $value['id']; ?>" <?php echo @$_POST['faction']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> หน่วยงานย่อย </label>
                            <div class="g24-col-sm-9">
                                <select name="level" id="level" class="form-control">
                                    <option value="">เลือกข้อมูล</option>
                                    <?php
                                        foreach($level as $key => $value){
                                            ?>
                                            <option value="<?php echo $value['id']; ?>" <?php echo @$_POST['level']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> รหัสสมาชิก </label>
                            <div class="g24-col-sm-4">
                                <div class="input-group">
                                    <input id="member_id" name="member_id" class="form-control" style="text-align:left;" type="number" value="<?php echo $_GET['member_id'] ?>" onkeypress="check_member_id();" required title="กรุณาป้อน รหัสสมาชิก" />
                                    <span class="input-group-btn">
                                        <a data-toggle="modal" data-target="#myModal" id="modal-search" class="fancybox_share fancybox.iframe" href="#">
                                            <button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span>
                                            </button>
                                        </a>
                                    </span>    
                                </div>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"></label>
                            <div class="g24-col-sm-9">
                                <input type="button" id="sumbit_search" class="btn btn-primary" style="width:100%" value="ค้นหารายการเรียกเก็บรายเดือน">
                            </div>
                        </div>
                    </form>
                    <h3></h3>
                    <div class="result_div">
                        <table class="table table-bordered table-striped table-center">
                            <thead> 
                                <tr class="bg-primary">
                                    <th style="width:60%">รายละเอียด</th>
                                    <th style="width:20%">จำนวนเงิน</th>
                                    <th style="width:20%"></th> 
                                </tr> 
                            </thead>
                            <tbody id="table_first">

                            <?php
                                foreach($finance_datas as $member_id => $finance_data) {
                            ?>
                                <tr>
                                    <td colspan="3" class="text-left"><?php echo "[".$member_id."]  ".$finance_data['name'];?></td>
                                </tr>
                            <?php
                                    if(!empty($finance_data['details'])) {
                                        foreach($finance_data['details'] as $run_id => $data) {
                            ?>
                                <tr id="tr_<?php echo $data['id'];?>">
                                    <td class="text-left">
                                        <?php echo $data['detail'];?>
                                    </td>
                                    <td class="">
                                        <input id="amount_<?php echo $data['id'];?>" name="amount[]" class="form-control m-b-1" type="text" value="<?php echo number_format($data['amount'],2); ?>" onKeyUp="format_the_number_decimal(this)">
                                    </td>
                                    <td class="text-right">
                                        <input type="button" id="sumbit_<?php echo $data['id'];?>" class="btn btn-primary btn_submit" data_id="<?php echo $data['id'];?>" value="แก้ไข"/>
                                        <input type="button" id="del_<?php echo $data['id'];?>" class="btn btn-danger btn_del" data_id="<?php echo $data['id'];?>" value="ลบ"/>
                                    </td>
                                </tr>
                            <?php
                                        }
                                    }
                                }
                            ?>
                            </tbody>
                        </table>
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
                                        <input id="search_text" name="search_text" class="form-control m-b-1" type="text" value="<?php echo @$_GET['member_id']; ?>">
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
$(document).ready(function() {    
    $('#myModal').on('shown.bs.modal', function () {
        $('#search_text').focus();
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
                    console.log(data)
                    $('#result_member').html(data.replace(`href="?member_id=`, `class="member_bth" data_id="`));  
                }  ,
                error: function(xhr){
                    console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                }
            });
        }
    });
    $(document).on('click', '.member_bth', function() {
        $('#member_id').val($(this).attr('data_id'))
        $('#myModal').modal('hide');
    });
    $("#sumbit_search").click(function() {
        $('#search_form').submit();
    });

    $(".btn_submit").click(function() {
        id = $(this).attr('data_id');
        amount = $('#amount_'+id).val().replace(/,/g,'');
        swal({
            title: "ยืนยันการแก้ไขข้อมูล",
            confirmButtonText: "ยืนยัน",
            showCancelButton: true,
            closeOnConfirm: true,
            showLoaderOnConfirm: true
        }, function (isConfirm) {
            if(isConfirm) {
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
                $.post(base_url+"/finance/ajax_edit_finance_month", {id: id, amount:amount}, function (res) {
                    result = JSON.parse(JSON.stringify(res));
                    $.unblockUI();
                });
            }
        });
    });

    $(".btn_del").click(function() {
        id = $(this).attr('data_id');
        swal({
            title: "ยืนยันการลบข้อมูล",
            confirmButtonText: "ยืนยัน",
            showCancelButton: true,
            closeOnConfirm: true,
            showLoaderOnConfirm: true
        }, function (isConfirm) {
            if(isConfirm) {
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
                $.post(base_url+"/finance/ajax_delete_finance_month", {id: id}, function (res) {
                    result = JSON.parse(JSON.stringify(res));
                    $("#tr_"+id).remove();
                    $.unblockUI();
                });
            }
        });
    });
});
function format_the_number_decimal(ele){
    var value = $('#'+ele.id).val();
    value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    var num = value.split(".");
    var decimal = '';
    var num_decimal = '';
    if(typeof num[1] !== 'undefined'){
        if(num[1].length > 2){
            num_decimal = num[1].substring(0, 2);
        }else{
            num_decimal =  num[1];
        }
        decimal = "."+num_decimal;
        
    }

    if(value!=''){
        if(value == 'NaN'){
            $('#'+ele.id).val('');
        }else{        
            value = parseInt(num[0]);
            value = value.toLocaleString()+decimal;
            $('#'+ele.id).val(value);
        }
    }else{
        $('#'+ele.id).val('');
    }
}

function change_mem_group(id, id_to){
        var mem_group_id = $('#'+id).val();
        $('#level').html('<option value="">เลือกข้อมูล</option>');
        $.ajax({
            method: 'POST',
            url: base_url+'manage_member_share/get_mem_group_list',
            data: {
                mem_group_id : mem_group_id
            },
            success: function(msg){
                $('#'+id_to).html(msg);
            }
        });
    }
</script>
