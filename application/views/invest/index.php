<!-- Css color of this screen not belong to main css. -->
<?php
unset($_POST);
$link = array(
    'href' => PROJECTJSPATH.'assets/css/Chart.min.css',
    'rel' => 'stylesheet',
    'type' => 'text/css'
);
echo link_tag($link);
?>
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
        width: 50%;
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
    @media (min-width: 768px) {
        .card_col {
            width:50%;
        }
    }
    @media (max-width: 768px) {
        .card_col {
            width:100%;
        }
    }
    .card_col {
        float: left;
        padding-left: 5px;
        padding-right: 5px;
    }
    .card_col > .card {
        padding-top: 15px;
        padding-bottom: 15px;
    }
    .second_bg {
        background-color : #137de0;
        color :#FFFFFF;
    }
    .second_text {
        color : #137de0;
    }
    .selected_invest {
        background-color : #137de0 !important;
        color :#FFFFFF !important;
    }
    .card {
        padding: 15px;
    }
    .card_list {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    .topic_row {
        border-top-color: #000 !important;
    }
    .topic_row th {
        color: #4c4b4b;
        font-size: 20px;
        padding-top: 2px!important;
        padding-bottom: 2px!important;
    }
    .table_invest_list {
        border: 0 !important;
        background-color: #fff !important;
        margin-top: 20px;
    }
    /* .topic_col {
        border-top: 2px solid #eceff1 !important;
    } */
    .table_sub {
        background-color: transparent !important;
    }
    .table_sub td {
        border-right: 2px solid #fff !important;
    }
    .text_right {
        text-align: right !important;
    }
    .table_sub tr:hover {
        background-color: #c7c3c3 !important;
    }
    .card_list .invest-title {
        margin-top: 10px !important;
        margin-bottom: 10px !important;
    }
    .detail_top {
        padding-bottom: 20px;
        border-bottom: 1px solid #eceff1;
    }
    .row_detail {
        border-bottom: 1px solid #eceff1;
        padding-bottom: 10px;
    }
    .col_detail_right {
        /* border-left: 1px solid #eceff1; */
    }
    .col_detail_left {
        border-right: 1px solid #eceff1;
    }
    .helpblock_plus {
        display: inline;
        margin-top: 5px;
        margin-bottom: 10px;
        /* color: #137de0; */
    }
    .helpblock_minus {
        display: inline;
        margin-top: 5px;
        margin-bottom: 10px;
        color: #d50000;
    }
    .helpblock_dark {
        display: inline;
        margin-top: 5px;
        margin-bottom: 10px;
        color: #000000;
    }
    .helpbtn {
        border-radius: 50px !important;
        font-size: 12px;
        padding-top: 2px;
        padding-bottom: 2px;
        width: unset;
        height: unset;
    }
    .no_b_space {
        margin-bottom: 0px;
    }
    .no_t_space {
        margin-top: 0px;
    }
    .title_font {
        font-size: 28px;
    }
    .dark_font {
        color: #000000;
    }
    .grey_font {
        color: #6b6b6b;
    }
    .grey_icon {
        font-size: 12px;
        color: #b5b5b5;
    }
    .no_size_padding {
        padding-left:0 !important;
        padding-right:0  !important;
    }
    .invest_row td {
        padding-top: 4px !important;
        padding-bottom: 4px !important;
    }
    .card-footer {
        border-top: unset;
    }
    .table_invest_list .table-bordered {
        border: unset;
    }
    .invest_type_total_row th {
        padding-bottom: 40px;
    }
    .ub_label {
        font-family: upbean;
        font-size: 18px;
        font-weight: unset;
    }
</style>
<div class="layout-content">
    <div class="layout-content-body">
        <h1 class="title_top">ระบบการลงทุน</h1>
        <p style="font-family: upbean; font-size: 20px; margin-bottom:5px;"><?php $this->load->view('breadcrumb'); ?></p>
        <div class="row gutter-xs">
            <div class="card_col">
                <div class="card bg-primary">
                    <div class="card-values">
                        <div class="p-x text-right">
                            <h3 class="card-title fw-l title_font" id="card_title_invest_payment"><?php echo $total_data['invest_payment_format']; ?></h3>
                            <small>จำนวนเงินลงทุน</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card_col">
                <div class="card bg-primary">
                    <div class="card-values">
                        <div class="p-x text-right">
                            <h3 class="card-title fw-l title_font" id="card_title_profit"><?php echo $total_data['profit_format'];?></h3>
                            <small>ผลตอบแทนระหว่างปี <?php echo (date('Y') + 543);?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="card">
                    <div class="card-body card_list">
                        <div class="col-xs-12 col-md-12 text-right">
                            <button type="button" id="add_invest" class="btn second_bg" data-dismiss="modal"><span class="icon icon-plus"></span> เพิ่มการลงทุน</button>
                            <a href="<?php echo base_url()."invest/organization"; ?>"><button type="button" id="edit_org" class="btn second_bg" data-dismiss="modal"><span class="icon icon-gear"></span> ตั้งค่าองค์กร</button></a>
                        </div>
                        <div class="col-xs-12 col-md-12 text-center">
                            <br><br>
                            <table class="table table-bordered table-center table_invest_list" style="width:90%; margin: auto;">
                                <tbody>
                                    <?php
                                        foreach($invest_types as $invest_type) {
                                            if(!empty($invests[$invest_type['id']])) {
                                                $total_invest_type = 0;
                                    ?>
                                    <tr class="topic_row">
                                        <th class="text-left topic_col" colspan="5">
                                            <?php echo $invest_type["name"];?>
                                        </th>
                                        <th class="text-left topic_col" colspan="1">
                                        <?php
                                            if($invest_type['able_change_share_value'] == 1) {
                                        ?>
                                            <button type="button" id="change_rate_<?php echo $invest_type['id'];?>" class="btn second_bg change_rate" data-id="<?php echo $invest_type['id'];?>" data-dismiss="modal"><span class="icon icon-gear"></span> แก้ไขมูลค่าหุ้น</button>
                                        <?php
                                            }
                                        ?>
                                        </th>
                                    </tr>
                                    <tr style="border-bottom: 3px solid #bdbdbd;">
                                        <th width="5%">ลำดับ</th>
                                        <th width="">หัวข้อการลงทุน</th>
                                        <th width="20%">จำนวนเงินลงทุน</th>
                                        <th width="20%">อัปเดต</th>
                                        <th width="10%">สถานะ</th>
                                        <th width="10%"></th>
                                    </tr>
                                    <tr>
                                        <td colspan="6" style="padding: 0;">
                                            <table class="table table-bordered table-striped table-center table_sub">
                                                <tbody class="tbody_<?php echo $invest_type['id']?>">
                                                    <?php
                                                        $invest_index = 1;
                                                        foreach($invests[$invest_type['id']] as $invest) {
                                                            $total_invest_type += $invest['amount'];
                                                    ?>
                                                    <tr class="invest_row" id="invest_row_<?php echo $invest['id'];?>" data-id="<?php echo $invest['id'];?>">
                                                        <td class="border_td" width="5%"><?php echo $invest_index++;?></td>
                                                        <td class="border_td text_left" id="invest_name_<?php echo $invest['id'];?>" width="">
                                                            <a class="detail_btn second_text" style="cursor: pointer;" id="detail_<?php echo $invest['id'];?>" data-id="<?php echo $invest['id'];?>"><?php echo $invest['name'];?></a>
                                                        </td>
                                                        <td id="invest_amount_<?php echo $invest['id'];?>" width="20%" class="text_right"><?php echo number_format($invest['amount'],2);?></td>
                                                        <td id="invest_update_date_<?php echo $invest['id'];?>" width="20%"><?php echo $this->center_function->ConvertToThaiDate($invest['update_date'],'1','0');?></td>
                                                        <td id="invest_status_<?php echo $invest['id'];?>" width="10%"><?php echo $invest['status'] == 2 ? "ไม่ใช้งาน" : (empty($invest['end_date']) || strtotime($invest['end_date']) >=  strtotime("now") ? "ปกติ" : "ครบกำหนด"); ?></td>
                                                        <td width="10%">
                                                            <a class="edit_btn " style="cursor: pointer;" class="text-default" id="edit_<?php echo $invest['id'];?>" data-id="<?php echo $invest['id'];?>">แก้ไข</a>
                                                            |
                                                            <a class="del_btn text-danger" style="cursor: pointer;" class="text-default" id="del_<?php echo $invest['id'];?>" data-id="<?php echo $invest['id'];?>">ลบ</a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                        }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="invest_type_total_row">
                                        <th width="5%"></th>
                                        <th width="" class="text-right" style="padding-bottom: 40px;">รวม</th>
                                        <th width="20%" class="text-right" style="padding-bottom: 40px;"><?php echo number_format($total_invest_type, 2);?></th>
                                        <th width="20%"></th>
                                        <th width="10%"></th>
                                        <th width="10%"></th>
                                    </tr>
                                    <?php
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
</div>
<div id="invest_modal" tabindex="-1" role="dialog" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">เพิ่มการลงทุน</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(PROJECTPATH.'/invest/edit'); ?>" method="post" id="add_form">
                    <input id="invest_id" name="invest_id" type="hidden" value="">
                    <input id="invest_type_add_sub" name="invest_type_sub" type="hidden" value=""><!-- For edit process. -->
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">องค์กร</label>
                            <div class="col-sm-6">
                                <select id="org_add" name="org_id" class="form-control m-b-1">
                                    <option value=""></option>
                                    <?php
                                        foreach($orgs as $key => $org) {
                                    ?>
                                    <option value="<?php echo $org['id']; ?>"><?php echo $org['name']; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">หมวดการลงทุน</label>
                            <div class="col-sm-6">
                                <select id="invest_type_add" name="invest_type" class="form-control m-b-1">
                                    <option value=""></option>
                                    <?php 
                                        foreach($invest_types as $key => $invest_type) {
                                    ?>
                                    <option value="<?php echo $invest_type['id']; ?>"><?php echo $invest_type['name']; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row coop_sav_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ชื่อผู้รับฝาก</label>
                            <div class="col-sm-6">
                                <input id="sav_c_name" name="sav_c[name]" class="form-control m-b-1 coop_sav_c_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row coop_sav_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">จำนวนเงิน</label>
                            <div class="col-sm-6">
                                <input type="text" id="sav_c_amount" name="sav_c[amount]" class="form-control m-b-1 num_input" onKeyUp="format_the_number_decimal(this)" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row coop_sav_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">อัตราดอกเบี้ย</label>
                            <div class="col-sm-6">
                                <input id="sav_c_interest" name="sav_c[interest]" class="form-control m-b-1 coop_sav_c_input num_input" onKeyUp="format_the_number_decimal(this)" type="number" value="">
                            </div>
                            <label class="col-sm-3 control-label text-left" style="padding-left: 0;">%</label>
                        </div>
                    </div>
                    <div class="row coop_sav_c_row">
                        <label class="col-sm-3 control-label right">วันที่ลงทุน</label>
                        <div class="col-sm-6">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="sav_c_start_date" name="sav_c[start_date]" class="form-control m-b-1 mydate coop_sav_c_input" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row coop_sav_c_row">
                        <label class="col-sm-3 control-label right">วันที่ครบกำหนด</label>
                        <div class="col-sm-6">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="sav_c_due_date" name="sav_c[due_date]" class="form-control m-b-1 mydate coop_sav_c_input" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row coop_sav_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รอบการจ่ายเงิน</label>
                            <div class="col-sm-6">
                                <input id="sav_c_period" name="sav_c[period]" class="form-control m-b-1 coop_sav_c_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row coop_sav_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ที่มาของเงินลงทุน</label>
                            <div class="col-sm-6">
                                <input id="sav_c_source" name="sav_c[source]" class="form-control m-b-1 coop_sav_c_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row coop_share_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ชื่อชุมนุม</label>
                            <div class="col-sm-6">
                                <input id="share_c_name" name="share_c[name]" class="form-control m-b-1 coop_share_c_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row coop_share_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รอบบัญชี</label>
                            <div class="col-sm-6">
                                <input id="share_c_period" name="share_c[period]" class="form-control m-b-1 coop_share_c_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row coop_share_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ที่มาของเงินลงทุน</label>
                            <div class="col-sm-6">
                                <input id="share_c_source" name="share_c[source]" class="form-control m-b-1 coop_sav_c_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row bond_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ชื่อรุ่น</label>
                            <div class="col-sm-6">
                                <input id="bond_c_name" name="bond[name]" class="form-control m-b-1 bond_c_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row bond_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ชื่อหน่วยงานที่ออก</label>
                            <div class="col-sm-6">
                                <input id="bond_c_department_name" name="bond[department_name]" class="form-control m-b-1 bond_c_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row bond_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Credit Rating</label>
                            <div class="col-sm-6">
                                <input id="bond_c_credit_rating" name="bond[credit_rating]" class="form-control m-b-1 bond_c_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row bond_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">จำนวนหน่วย</label>
                            <div class="col-sm-6">
                                <input id="bond_c_unit" name="bond[unit]" class="form-control m-b-1 bond_c_input num_input" onKeyUp="format_the_number_decimal(this)" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row bond_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">มูลค่าเฉลี่ย</label>
                            <div class="col-sm-6">
                                <input id="bond_c_value_per_unit" name="bond[value_per_unit]" class="form-control m-b-1 bond_c_input num_input" onKeyUp="format_the_number_decimal(this)" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row bond_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ผลตอบแทนเฉลี่ย</label>
                            <div class="col-sm-6">
                                <input id="bond_c_aver_profit" name="bond[aver_profit]" class="form-control m-b-1 bond_c_input num_input" onKeyUp="format_the_number_decimal(this)" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row bond_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ดอกเบี้ย</label>
                            <div class="col-sm-6">
                                <input id="bond_c_invest_rate_text" name="bond[invest_rate_text]" class="form-control m-b-1 bond_c_input num_input" onKeyUp="format_the_number_decimal(this)" type="number" value="">
                            </div>
                            <label class="col-sm-3 control-label text-left" style="padding-left: 0;">%</label>
                        </div>
                    </div>
                    <div class="row bond_c_row">
                        <label class="col-sm-3 control-label right">วันที่ซื้อ</label>
                        <div class="col-sm-6">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="bond_c_start_date" name="bond[start_date]" class="form-control m-b-1 mydate bond_c_input" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row bond_c_row">
                        <label class="col-sm-3 control-label right">วันที่ครบกำหนด</label>
                        <div class="col-sm-6">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="bond_c_due_date" name="bond[due_date]" class="form-control m-b-1 mydate bond_c_input" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row bond_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รอบการจ่ายดอกเบี้ย</label>
                            <div class="col-sm-6">
                                <input id="bond_c_payment_method_text" name="bond[payment_method_text]" class="form-control m-b-1 bond_c_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row bond_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ที่มาของเงินลงทุน</label>
                            <div class="col-sm-6">
                                <input id="bond_c_source" name="bond[source]" class="form-control m-b-1 coop_sav_c_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row coop_share_s_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ชื่อหุ้น</label>
                            <div class="col-sm-6">
                                <input id="share_s_name" name="share_s[name]" class="form-control m-b-1 coop_share_s_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row coop_share_s_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ตัวย่อ</label>
                            <div class="col-sm-6">
                                <input id="share_s_period" name="share_s[period]" class="form-control m-b-1 coop_share_sinput" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row coop_share_s_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ที่มาของเงินลงทุน</label>
                            <div class="col-sm-6">
                                <input id="share_s_source" name="share_s[source]" class="form-control m-b-1 coop_sav_c_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row status_edit_row" style="display:none;">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">สถานะ</label>
                            <div class="col-sm-6" style="margin-top:5px;">
                                <input type="radio" name="status" id="status_enable" value="1"/><span id="status_enable_text"> ปกติ </span>&nbsp;&nbsp;
                                <input type="radio" name="status" id="status_disable" value="2"/><span> ไม่ใช้งาน </span>&nbsp;&nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" id="submit_add">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" id="cancel_add">ยกเลิก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="share_val_modal" tabindex="-1" role="dialog" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">แก้ไขมูลค่าหุ้น</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(PROJECTPATH.'/invest/add_invest_interest_value'); ?>" method="post" id="share_all_val_form">
                    <div class="row">
                        <label class="col-sm-5 ub_label text-right"> วันที่แก้ไข </label>
                        <div class="col-sm-3">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="share_val_date" name="date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-t-2 m-b-2">
                        <label class="col-sm-5 control-label text-right"> ชื่อหุ้น </label>
                        <label class="col-sm-3 control-label text-center" style="text-align: center;">มูลค่าหุ้น</label>
                    </div>
                    <div id="share_val_div">
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" id="share_all_val_submit">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" id="share_all_val_cancel">ยกเลิก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<form action="<?php echo base_url(PROJECTPATH.'/invest/detail'); ?>" method="post" id="access_form">
    <input type="hidden" name="id" id="form_id" value="">
</form>
<input type="hidden" id="default_invest_id" value="<?php echo $invest_id;?>"/>
<input type="hidden" id="current_date_format" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>"/>
<form action="<?php echo base_url(PROJECTPATH.'/invest'); ?>" method="post" id="reload_form">
    <input type="hidden" id="reload_invest_id" name="invest_id" value="">;
</form>
<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/Chart.min.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
$link = array(
    'src' => PROJECTJSPATH.'assets/js/invest.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
<script>
    $(document).ready(function() {
        $("#submit_add").click(function() {
            $(".num_input").each(function(index) {
                $(this).val(removeCommas($(this).val()));
            });
            $.post(base_url+"invest/edit",
            $("#add_form").serialize(),
            function(result) {
                data = JSON.parse(result);
                invest_id = data.invest_id;
                $("#reload_invest_id").val(invest_id);
                $("#reload_form").submit();
            });
        });
        $(".detail_btn").click(function() {
            id = $(this).attr("data-id");
            $("#form_id").val(id);
            $("#access_form").submit();
        });
        $(".edit_btn").click(function() {
            id = $(this).attr("data-id");

            $.get(base_url+"invest/get_invest_by_id?id="+id,
            function(result) {
                data = JSON.parse(result);
                invest = data.data;
                if(invest.type == 1) {
                    $("#invest_id").val(invest.id);
                    $("#invest_type_add").val(invest.type);
                    $('#invest_type_add').prop('disabled', true);
                    $("#invest_type_add_sub").val(invest.type);
                    $("#org_add").val(invest.org_id);
                    $("#sav_c_name").val(invest.name);
                    $("#sav_c_amount").val(invest.amount_format);
                    $("#sav_c_interest").val(invest.detail.invest_rate_text);
                    $("#sav_c_start_date").val(invest.detail.start_date_calender);
                    $("#sav_c_due_date").val(invest.detail.end_date_calender);
                    $("#sav_c_period").val(invest.detail.payment_method_text);
                    $("#sav_c_source").val(invest.source);
                    $("#share_c_name").val('');
                    $("#share_c_period").val('');
                    $("#share_c_source").val('');
                    $("#bond_c_name").val('');
                    $("#bond_c_department_name").val('');
                    $("#bond_c_credit_rating").val('');
                    $("#bond_c_unit").val(0);
                    $("#bond_c_value_per_unit").val(0);
                    $("#bond_c_aver_profit").val(0);
                    $("#bond_c_invest_rate_text").val('');
                    $("#bond_c_start_date").val(current_date_format);
                    $("#bond_c_due_date").val(current_date_format);
                    $("#bond_c_payment_method_text").val('');
                    $("#bond_c_source").val('');
                    $("#share_s_name").val('');
                    $("#share_s_period").val('');
                    $("#share_s_source").val('');
                    $('.coop_sav_c_row').show();
                    $('.coop_share_c_row').hide();
                    $('.bond_c_row').hide();
                    $('.coop_share_s_row').hide();
                    if(invest.detail.invest_interval_left) {
                        $("#status_enable_text").html(" ปกติ");
                    } else {
                        $("#status_enable_text").html(" ครบกำหนด");
                    }
                    if(invest.status == 2) {
                        $("#status_enable").prop("checked", false);
                        $("#status_disable").prop("checked", true);
                    } else {
                        $("#status_enable").prop("checked", true);
                        $("#status_disable").prop("checked", false);
                    }
                    $(".status_edit_row").show();
                    $("#invest_modal").modal("show");
                } else if (invest.type == 2) {
                    $("#invest_id").val(invest.id);
                    $("#invest_type_add").val(invest.type);
                    $('#invest_type_add').prop('disabled', true);
                    $("#invest_type_add_sub").val(invest.type);
                    $("#org_add").val(invest.org_id);
                    $("#sav_c_name").val('');
                    $("#sav_c_amount").val(0);
                    $("#sav_c_interest").val('');
                    $("#sav_c_start_date").val(current_date_format);
                    $("#sav_c_due_date").val(current_date_format);
                    $("#sav_c_period").val('');
                    $("#sav_c_source").val('');
                    $("#share_c_name").val(invest.name);
                    $("#share_c_period").val(invest.detail.payment_method_text);
                    $("#share_c_source").val(invest.source);
                    $("#bond_c_name").val('');
                    $("#bond_c_department_name").val('');
                    $("#bond_c_credit_rating").val('');
                    $("#bond_c_unit").val(0);
                    $("#bond_c_value_per_unit").val(0);
                    $("#bond_c_aver_profit").val(0);
                    $("#bond_c_invest_rate_text").val('');
                    $("#bond_c_start_date").val(current_date_format);
                    $("#bond_c_due_date").val(current_date_format);
                    $("#bond_c_payment_method_text").val('');
                    $("#bond_c_source").val('');
                    $("#share_s_name").val('');
                    $("#share_s_period").val('');
                    $("#share_s_source").val('');
                    $('.coop_sav_c_row').hide();
                    $('.coop_share_c_row').show();
                    $('.bond_c_row').hide();
                    $('.coop_share_s_row').hide();
                    $("#status_enable_text").html(" ปกติ");
                    if(invest.status == 2) {
                        $("#status_enable").prop("checked", false);
                        $("#status_disable").prop("checked", true);
                    } else {
                        $("#status_enable").prop("checked", true);
                        $("#status_disable").prop("checked", false);
                    }
                    $(".status_edit_row").show();
                    $("#invest_modal").modal("show");
                } else if (invest.type == 3 || invest.type == 4) {
                    $("#invest_id").val(invest.id);
                    $("#invest_type_add").val(invest.type);
                    $('#invest_type_add').prop('disabled', true);
                    $("#invest_type_add_sub").val(invest.type);
                    $("#org_add").val(invest.org_id);
                    $("#sav_c_name").val('');
                    $("#sav_c_amount").val(0);
                    $("#sav_c_interest").val('');
                    $("#sav_c_start_date").val(current_date_format);
                    $("#sav_c_due_date").val(current_date_format);
                    $("#sav_c_period").val('');
                    $("#sav_c_source").val('');
                    $("#share_c_name").val('');
                    $("#share_c_period").val('');
                    $("#share_c_source").val('');
                    $("#bond_c_name").val(invest.detail.name);
                    $("#bond_c_department_name").val(invest.name);
                    $("#bond_c_credit_rating").val(invest.detail.credit_rating);
                    $("#bond_c_unit").val(invest.detail.unit_format);
                    $("#bond_c_value_per_unit").val(invest.detail.value_per_unit_format);
                    $("#bond_c_aver_profit").val(invest.detail.aver_profit_format);
                    $("#bond_c_invest_rate_text").val(invest.detail.invest_rate_text);
                    $("#bond_c_start_date").val(invest.detail.start_date_calender);
                    $("#bond_c_due_date").val(invest.detail.end_date_calender);
                    $("#bond_c_payment_method_text").val(invest.detail.payment_method_text);
                    $("#bond_c_source").val(invest.source);
                    $("#share_s_name").val('');
                    $("#share_s_period").val('');
                    $("#share_s_source").val('');
                    $('.coop_sav_c_row').hide();
                    $('.coop_share_c_row').hide();
                    $('.bond_c_row').show();
                    if(invest.detail.invest_interval_left) {
                        $("#status_enable_text").html(" ปกติ");
                    } else {
                        $("#status_enable_text").html(" ครบกำหนด");
                    }
                    if(invest.status == 2) {
                        $("#status_enable").prop("checked", false);
                        $("#status_disable").prop("checked", true);
                    } else {
                        $("#status_enable").prop("checked", true);
                        $("#status_disable").prop("checked", false);
                    }
                    $(".status_edit_row").show();
                    $('.coop_share_s_row').hide();
                    $("#invest_modal").modal("show");
                } else if (invest.type == 5) {
                    $("#invest_id").val(invest.id);
                    $("#invest_type_add").val(invest.type);
                    $('#invest_type_add').prop('disabled', true);
                    $("#invest_type_add_sub").val(invest.type);
                    $("#org_add").val(invest.org_id);
                    $("#sav_c_name").val('');
                    $("#sav_c_amount").val(0);
                    $("#sav_c_interest").val('');
                    $("#sav_c_start_date").val(current_date_format);
                    $("#sav_c_due_date").val(current_date_format);
                    $("#sav_c_period").val('');
                    $("#sav_c_source").val('');
                    $("#share_c_name").val(invest.name);
                    $("#share_c_period").val(invest.detail.payment_method_text);
                    $("#share_c_source").val('');
                    $("#bond_c_name").val('');
                    $("#bond_c_department_name").val('');
                    $("#bond_c_credit_rating").val('');
                    $("#bond_c_unit").val(0);
                    $("#bond_c_value_per_unit").val(0);
                    $("#bond_c_aver_profit").val(0);
                    $("#bond_c_invest_rate_text").val('');
                    $("#bond_c_start_date").val(current_date_format);
                    $("#bond_c_due_date").val(current_date_format);
                    $("#bond_c_payment_method_text").val('');
                    $("#bond_c_source").val('');
                    $("#share_s_name").val(invest.name);
                    $("#share_s_period").val(invest.detail.name);
                    $("#share_s_source").val(invest.source);
                    $('.coop_sav_c_row').hide();
                    $('.coop_share_c_row').hide();
                    $('.bond_c_row').hide();
                    $('.coop_share_s_row').show();
                    $("#status_enable_text").html(" ปกติ");
                    if(invest.status == 2) {
                        $("#status_enable").prop("checked", false);
                        $("#status_disable").prop("checked", true);
                    } else {
                        $("#status_enable").prop("checked", true);
                        $("#status_disable").prop("checked", false);
                    }
                    $(".status_edit_row").show();
                    $("#invest_modal").modal("show");
                }
            });
        });
    });
</script>