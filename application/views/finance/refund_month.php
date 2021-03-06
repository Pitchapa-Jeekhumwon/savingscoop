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
        </style>

        <style type="text/css">
            .form-group{
                margin-bottom: 5px;
            }
        </style>
        <h1 style="margin-bottom: 0">รายการคืนเงินประจำเดือน</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <form action="<?php echo base_url('/finance/coop_refund_month_preview'); ?>" id="form1" method="GET" target="_blank">
                        <h3></h3>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> เดือน </label>
                            <div class="g24-col-sm-4">
                                <select id="month" name="month" class="form-control">
                                    <?php foreach($month_arr as $key => $value){ ?>
                                        <option value="<?php echo $key; ?>" <?php echo $key==((int)date('m'))?'selected':''; ?>><?php echo $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label class="g24-col-sm-1 control-label right"> ปี </label>
                            <div class="g24-col-sm-4">
                                <select id="year" name="year" class="form-control">
                                    <?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
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
                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['mem_group_name']; ?></option>
                                        <?php
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-5 control-label right"></label>
                            <div class="g24-col-sm-6">
                                <input type="button" class="btn btn-primary" style="width:100%" value="รายการคืนเงินประจำเดือน" data-type="preview" onclick="check_empty('preview')">
                            </div>
                            <div class="g24-col-sm-4">
                                <input type="button" class="btn btn-default" style="width:100%" value="Export Excel" data-type="excel" onclick="check_empty('excel')">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $( document ).ready(function() {

    });
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
    function check_empty(type){
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
            baseZ: 2000,
            bindEvents: false
        });
        $.ajax({
            success:function(data){
                $.unblockUI();
                    console.log(type)
                    if(type == 'preview') {
                        $('#form1').attr('action', base_url+'finance/coop_refund_month_preview');
                        $('#form1').submit();
                    } else if(type == 'excel') {
                        $('#form1').attr('action', base_url+'finance/coop_refund_month_excel');
                        $('#form1').submit();
                    }

            }
        });
    }
</script>


