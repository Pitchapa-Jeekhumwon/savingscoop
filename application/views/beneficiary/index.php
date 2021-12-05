<div class="layout-content">
    <div class="layout-content-body">
        <style type="text/css">
            .form-group{
                margin-bottom: 5px;
            }
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            .toast-success {
                background-color: #42b546;
                color: #fff;
            }

            .toast-top-right {
                right: 12px;
                top: 65px;
            }

            .alert-success {
                background-color: #DBF6D3;
                border-color: #AED4A5;
                color: #569745;
                font-size:14px;
            }
            .alert {
                border-radius: 0;
                -webkit-border-radius: 0;
                box-shadow: 0 1px 2px rgba(0,0,0,0.11);
                display: table;
                width: 100%;
            }
            .text_indent {
                font-size:21px;
                font-family: 'DBHelvethaica';
            }
            a {
                text-decoration: none !important;
            }

            a:hover {
                color: #075580;
            }

            a:active {
                color: #757575;
            }

            .left_indent {
                margin-left : 1.5em;
            }

            .modal-header-delete {
                padding:9px 15px;
                border:1px solid #d50000;
                background-color: #d50000;
                color: #fff;
                -webkit-border-top-left-radius: 5px;
                -webkit-border-top-right-radius: 5px;
                -moz-border-radius-topleft: 5px;
                -moz-border-radius-topright: 5px;
                border-top-left-radius: 5px;
                border-top-right-radius: 5px;
            }

            .modal-dialog-info {
                margin:0 auto;
                width: 75%;
                margin-top: 5%;
            }
            .modal-dialog-add {
                margin:0 auto;
                width: 80%;
                margin-top: 5%;
            }

            .modal-dialog-delete {
                margin:0 auto;
                width: 350px;
                margin-top: 8%;
            }


        </style>
<h1 style="margin-bottom: 0">ผู้รับผลประโยชน์</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>

</div>
<div class="panel panel-body col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <?php $this->load->view('search_member_new'); ?>

    <div class="form-group g24-col-sm-24">
        <form action="<?=base_url('beneficiary/save_pdf/'.@$_GET['member_id'])?>" method="post" enctype="multipart/form-data">
        <div class="form-group g24-col-sm-8">
        
            <div class="form-group">
                <label class="g24-col-sm-10 control-label ">แนบไฟล์</label>
                <div class=" g24-col-sm-14">
                    <input id="file" name="file" class="form-control " type="file" require>
                </div>
            </div>

            
        
        </div>
        <div class="form-group g24-col-sm-8">
            <div class="form-group">
                <label class="g24-col-sm-10 control-label ">วันที่รับเอกสาร</label>
                <div class=" g24-col-sm-14">
                    <div class="form-group has-success">
                        <input id="benefits_attach_date" name="benefits_attach_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?=date('d/m/').(date("Y")+543)?>" data-date-language="th-th" required="" title="กรุณาป้อน วันที่" aria-required="true" aria-invalid="false">
                        <span class="icon icon-calendar input-icon m-f-1"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group g24-col-sm-8">
            <div class="form-group">
                <label class="g24-col-sm-10 control-label "></label>
                <div class=" g24-col-sm-14">
                    <button class="btn btn-primary btn-md" type="submit">
                            <span class="fa fa-save"></span>
                            บันทึก
                    </button>
                </div>
            </div>
        </div>
        </form>
    </div>

    <div class="form-group g24-col-sm-24">
        <div class="form-group g24-col-sm-8">
            <div class="form-group">
                <label class="g24-col-sm-10 control-label ">ไฟล์เอกสาร</label>
                <div class=" g24-col-sm-14" style="padding-top: 7px;">
                    <?php
                    if($row_member['benefits_attach']!=""){
                        ?>
                        <a href="<?=base_url('assets/uploads/benefits_attach/'.@$row_member['benefits_attach'])?>" target="_blank"><span><?=$row_member['benefits_attach']?></span></a>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <form id="beneficiary" action="<?php echo base_url('/beneficiary/save_beneficiary')?>" method="post" enctype="multipart/form-data">
    <div class="g24-col-sm-24">
        <div class="row">
            <h3>เงื่อนไขการจ่ายเงินให้ผู้รับผลประโยชน์</h3>
        </div>
    </div>
    <div class="row">
        <div class="form-group g24-col-sm-24">
            <label class="radio-inline control-label col-sm-offset-2" for="choose_1">
                <input class="chooser" type="radio" name="howto_beneficiary" id="choose_1" value="1" <?php echo @$check_choose_1; ?> > 1.ให้จ่ายเงินทั้งหมดแก้ผู้รับโอนประโยชน์ที่ยังมีชีวิตในลำดับต้นก่อน
            </label>
        </div>
        <div class="form-group g24-col-sm-24">
            <label class="radio-inline control-label col-sm-offset-2" for="choose_2">
                <input class="chooser" type="radio" name="howto_beneficiary" id="choose_2" value="2" <?php echo @$check_choose_2; ?>> 2.ให้จ่ายเงินแก่ผู้รับผลประโยชน์ที่ยังมีชีวิตในลำดับต้นก่อน
            </label>
        </div>
        <div class="form-inline g24-col-sm-24">
            <label class="radio-inline control-label col-sm-offset-2" for="choose_3">
                <input class="chooser" type="radio" name="howto_beneficiary" id="choose_3" value="3" <?php echo @$check_choose_3; ?> > 3.อื่นๆ
            </label>
            <label class="control-label" for="other"> ระบุ </label>
            <input type="text" class="form-control" name="other" readonly="readonly" disabled="disabled" id="other" value="<?php echo @$other; ?>" >
        </div>
    </div>
    <div class="form-group g24-col-sm-8" style="padding-top: 12px;">
        <div class="form-group">
            <label class="g24-col-sm-10 control-label ">วันที่มีผล</label>
            <div class=" g24-col-sm-14">
                <div class="form-group has-success">
                    <input id="effective_date" name="effective_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo @$effective_date; ?>" data-date-language="th-th" required="" title="กรุณาป้อน วันที่" aria-required="true" aria-invalid="false">
                    <span class="icon icon-calendar input-icon m-f-1"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="g24-col-sm-16" style="padding-top: 12px;">
        <input type="hidden" name="member_id" value="<?php echo $_GET['member_id']; ?>">
        <button class="btn btn-primary" type="submit"><span class="fa fa-save"></span> บันทึกเงื่อนไข</button>
    </div>
    </form>

    <div class="col-sm-6" style="margin-top:20px;">
        <div class="input-with-icon">
            <!-- <input name="search_text" id="search_text" class="form-control input-thick pill" type="text" placeholder="Search…">
            <span class="icon icon-search input-icon"></span> -->
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top:30px;padding-right: 0px !important;margin-right: 0px !important;">
        <a class="link-line-none" onclick="add_benefit('','<?php echo $member_id?>')" style="cursor:pointer;">
            <button <?php echo  (empty($member_id)) ? 'disabled="disabled"' : '' ; ?>  class="btn btn-primary btn-lg bt-add" type="button">
                <span class="icon icon-plus-circle"></span>
                เพิ่มผู้รับประโยชน์
            </button>
        </a>
    </div>
        <div class="bs-example" data-example-id="striped-table">
            <table class="table table-bordered table-striped table-center">

                <thead>
                <tr class="bg-primary">
                    <th width="5%">ลำดับ</th>
                    <th width="15%">วันที่เพิ่ม</th>
                    <th width="15%">ชื่อ-สกุล</th>
                    <th>สัดส่วน</th>
                    <th width="20%">ที่อยู่ผู้รับโอน</th>
                    <th >ความสัมพันธ์</th>
                    <th width="15%">ผู้ทำรายการ</th>
                    <th>จัดการ</th>
                </tr>
                </thead>
                <tbody id="table_first">
                <?php
                $i=1;
                foreach($data as $key => $row){ ?>
                    <tr>
                        <td scope="row"><?php echo $i++; ?></td>
                        <td scope="row"><?php echo $row['g_create']!=''?$this->center_function->ConvertToThaiDate($row['g_create'],'1'):'';  ?></td>
                        <td class="set_left">

                            <?php echo $row['prename_short'].' '.$row['g_firstname'].' '. $row['g_lastname']; ?>

                        </td>
                        <th><?php echo $row['g_share_rate']." % "; ?></th>
                        <td class="set_left">

                            <?php
                            if ($row['g_address_no']) {
                                echo " บ้านเลขที่ ".$row['g_address_no'];
                            }
                            if ($row['g_address_moo']) {
                                echo " หมู่ ".$row['g_address_moo'];
                            }
                            if ($row['g_address_village']) {
                                echo " หมู่บ้าน ".$row['g_address_village'];
                            }
                            if ($row['g_address_road']) {
                                echo " ถนน ".$row['g_address_road'];
                            }
                            if ($row['g_address_soi']) {
                                echo " ซอย ".$row['g_address_soi'];
                            }
                            if ($row['g_district_id']) {
                                echo " ต. ".$row['district_name'];
                            }
                            if ($row['g_amphur_id']) {
                                echo " อ. ".$row['amphur_name'];
                            }
                            if ($row['g_province_id']) {
                                echo " จ. ".$row['province_name'];
                            }
                            if ($row['g_zipcode']) {
                                echo " รหัสไปรษณีย์ ".$row['g_zipcode'];
                            }
                            ?>

                        </td>
                        <td>
                            <?php
                            echo $row['relation_name'];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $row['user_name'];
                            ?>
                        </td>
                        <td>
                            <a onclick="show_detail('<?php echo $row['gain_detail_id'];?>')" style="cursor:pointer;" title="คลิกเพื่อดูรายละเอียด">  ดูข้อมูล </a>
                            | <a onclick="add_benefit('<?php echo $row['gain_detail_id'];?>','<?php echo $member_id?>')" style="cursor:pointer;">แก้ไข</a>
                            | <a class="text-del" onclick="delete_benefit('<?php echo $row['gain_detail_id'];?>','<?php echo $row['member_id'];?>')"> ลบ </a>
                        </td>

                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php include "beneficiary_history.php"; ?>

</div>
    </div>
</div>
<div id="add_benefit" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-add">
        <div class="modal-content">
            <div class="modal-header modal-header-info">
                <h2 class="modal-title">เพิ่มข้อมูลผู้รับผลประโยชน์</h2>
            </div>
            <div class="modal-body" id="add_benefit_space">
            </div>
        </div>
    </div>
</div>
<div id="show_detail" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-info">
        <div class="modal-content">
            <div class="modal-header modal-header-info">
                <h2 class="modal-title">รายละเอียดผู้รับผลประโยชน์</h2>
            </div>
            <div class="modal-body" id="show_detail_space">
            </div>
        </div>
    </div>
</div>
<div class="modal fade in" tabindex="-1" id="changeModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">ข้อมูลการแก้ไข</h4>
            </div>
            <div class="modal-body">
                <div class="">
                    <table class="table table-striped">
                        <thead>
                        <th></th>
                        <th class="text-center">ข้อมูลเดิม</th>
                        <th class="text-center">ข้อมูลใหม่</th>
                        </thead>
                        <tbody id="changeModal_tbody">
                        <td id="changeModal_td_name" class="text-center"></td>
                        <td id="changeModal_td_old_val" class="text-center"></td>
                        <td id="changeModal_td_new_val" class="text-center"></td>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer text-center">
                <button type="button" id="changeModal_close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('search_member_new_modal'); ?>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/beneficiary.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>

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

    $('.chooser').on('click', function(){
        const other = $("#other");
        if($(this).val() === '3'){
            other.val('');
            other.prop({
                disabled: false,
                readonly: false
            });
        }else{
            other.val('');
            other.prop({
                disabled: true,
                readonly: true
            });
        }
    })
});
</script>
