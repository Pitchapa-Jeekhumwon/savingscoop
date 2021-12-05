<div class="layout-content">
    <div class="layout-content-body">
        <?php
        $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        ?>
        <style>
            .modal.fade {
                z-index: 10000000 !important;
            }
            .modal-backdrop.in{
                opacity: 0;
            }
            .modal-backdrop {
                position: relative;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: 1040;
                background-color: #000;
            }

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
        <h1 style="margin-bottom: 0">ไฟล์ส่งยอดเรียกเก็บ</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <form action="<?php echo base_url(PROJECTPATH.'/text_file_deduction/processor_data_txt'); ?>" id="form4" method="GET" target="_blank">
                        <div class="form-group g24-col-sm-24">
                            <div class="g24-col-sm-5 right">
                                <h3>เรียกเก็บรายเดือน</h3>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24" style="display: none;">
                            <label class="g24-col-sm-6 control-label right"> ประเภทพนักงาน </label>
                            <div class="g24-col-sm-4">
                                <select name="processor_mem_type" id="processor_mem_type" class="form-control" onchange="check_total_share('PROCESSOR')">
                                    <option value="1">พนักงาน</option>
                                    <option value="2">ลูกจ้าง</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> เดือน </label>
                            <div class="g24-col-sm-4">
                                <select id="processor_month" name="processor_month" class="form-control" onchange="check_total_share('PROCESSOR')">
                                    <?php foreach($month_arr as $key => $value){ ?>
                                        <option value="<?php echo $key; ?>" <?php echo $key==date('m')?'selected':''; ?>><?php echo $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ปี </label>
                            <div class="g24-col-sm-4">
                                <select id="processor_year" name="processor_year" class="form-control" onchange="check_total_share('PROCESSOR')">
                                    <?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> รูปแบบสมาชิก </label>
                            <div class="g24-col-sm-12 mem_type_list">
                                <label class="custom-control custom-control-primary custom-checkbox g24-col-sm-8" style="padding-top: 9px;margin-left: 15px;">
                                    <input type="checkbox" class="custom-control-input type_item" id="mem_type_all" name="mem_type_all[]" value="all" onchange="check_all()">
                                    <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                    <span class="custom-control-label">ทั้งหมด</span>
                                </label>
                                <?php
                                if(!empty($mem_type)){
                                    foreach($mem_type AS $key=>$type_value){
                                        ?>
                                        <label class="custom-control custom-control-primary custom-checkbox g24-col-sm-8" style="padding-top: 9px;">
                                            <input type="checkbox" class="custom-control-input type_item" id="mem_type[<?php echo @$type_value['mem_type_id'];?>]" name="mem_type[]" value="<?php echo @$type_value['mem_type_id'];?>" onchange="check_total_share('PROCESSOR')">
                                            <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                            <span class="custom-control-label"><?php echo @$type_value['mem_type_name'];?></span>
                                        </label>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ยอดรวม </label>
                            <label class="g24-col-sm-4" id="total_share"></label>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <div class="g24-col-sm-6"></div>
                            <div class="g24-col-sm-12">
                                <button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty('PROCESSOR')"><span> แสดงผล</span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var mem_type = [];
    <?php
    if(!empty($mem_type)){
    foreach($mem_type AS $key=>$value){ ?>
    mem_type.push('<?php echo $value['mem_type_id'];?>');
    <?php }
    }
    ?>
</script>
<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_text_files.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);

?>


