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
            .control-label{
                text-align:right;
                padding-top:5px;
            }
            .text_left{
                text-align:left;
            }
            .text_right{
                text-align:right;
            }
        </style>
        <h1 style="margin-bottom: 0">ทะเบียนย่อย</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <h3>ทะเบียนย่อย</h3>
                    <form method="POST" action="<?php echo base_url(PROJECTPATH.'/account/sub_registration_pdf'); ?>" id="form1">
                        <div class="g24-col-sm-24">
                            <label class="g24-col-sm-3 control-label datepicker1" for="approve_date">เลือกวันที่บันทึกบัญชี</label>
                            <div class="input-with-icon g24-col-sm-3">
                                <div class="form-group">
                                    <input id="approve_date" name="approve_date" class="form-control m-b-1 form_date_picker" type="text" value="<?php echo !empty($_GET['approve_date']) ? $_GET['approve_date'] : $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" autocomplete="off">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                        <div class="g24-col-sm-24 form-group">
                            <label class="g24-col-sm-3 control-label datepicker1" for="submit-btn"></label>
                            <div class="input-with-icon g24-col-sm-1">
                                <input id="submit-btn" type="button" class="btn btn-primary" value="PDF">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".form_date_picker").datepicker({
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

        $("#submit-btn").click(function() {
            $.ajax({
                url: base_url+'/account/check_sub_registration',	
                method:"post",
                data:$("#form1").serialize(),
                dataType:"text",
                success:function(result){
                    data = JSON.parse(result);
                    if(data.status == 'success'){
                        $('#form1').submit();
                    }else{
                        $('#alertNotFindModal').appendTo("body").modal('show');
                    }
                }
            });
        });
    });
</script>