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
            width:20%;
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
    .topic_col {
        border-top: 2px solid #eceff1 !important;
    }
    .table_sub {
        background-color: transparent !important;
    }
    .table_sub td {
        border: 0 !important;
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
    #share_s_detail .table_invest_list thead th {
        border: unset;
    }
    .row {
        min-height: 27px;
    }
</style>
<div class="layout-content">
    <div class="layout-content-body">
        <h1 class="title_top">ระบบการลงทุน</h1>
        <p style="font-family: upbean; font-size: 20px; margin-bottom:5px;"><?php $this->load->view('breadcrumb'); ?></p>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="card-body card_list">
                        <div class="row">
                            <div id="sav_c_detail" class="col-md-12" style="display:none;">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h3 class="invest-title dark_font" id="sav_c_t_name"></h3>
                                        <span id="type_1_span" class="help-block">เงินฝากในระบบสหกรณ์</span>
                                    </div>
                                    <div class="col-md-3 text-right invest-title">
                                    <button type="button" id="sav_c_account_edit_btn" class="btn btn-primary account_edit_btn" data_id=""><span class="icon icon-cog"></span> ตั้งค่าบัญชี</button>
                                    <button type="button" id="sav_c_edit_btn" class="btn btn-primary" data_id=""><span class="icon icon-pencil"></span> แก้ไข</button>
                                    </div>
                                </div>
                                <div class="row row_detail">
                                    <div class="col-md-4 col_detail_left">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3 id="sav_c_t_amount" class="no_b_space title_font dark_font"></h3>
                                                <span  class="help-block no_t_space no_b_space">เงินฝาก</span>
                                            </div>
                                            <div class="col-md-12">
                                                <span id="sav_c_d_source" class="help-block no_t_space"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col_detail_left">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3 id="sav_c_t_time_left" class="no_b_space title_font dark_font"></h3>
                                                <span  class="help-block no_t_space no_b_space">อายุคงเหลือ</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row" style="display:none;">
                                            <div class="col-md-12 col_detail_right">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h3 id="sav_c_t_balance" class="second_text no_b_space title_font"></h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <span  class="help-block no_t_space">รวมทั้งหมด</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <span id="sav_c_diff_percent" class="helpblock_plus text-edit"></span>
                                                        <span id="sav_c_diff_percent_arrow" class="icon icon-arrow-up text-edit"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 col_detail_right">
                                                <h3 id="sav_c_t_profit" class="second_text no_b_space title_font"></h3>
                                                <span  class="help-block no_t_space">รวมดอกเบี้ยที่ได้รับ</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>ข้อมูลการลงทุน</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>จำนวนเงิน</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="sav_c_d_amount"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>อัตราดอกเบี้ย</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="sav_c_d_interest"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>วันที่ลงทุน</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="sav_c_d_start_date"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>วันที่ครบกำหนด</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="sav_c_d_due_date"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>รอบการจ่ายเงิน</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="sav_c_d_period"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>สถานะ</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="sav_c_d_status"></span>
                                    </div>
                                </div>
                            </div>
                            <div id="share_c_detail" class="col-md-12" style="display:none;">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h3 class="invest-title dark_font" id="share_c_t_name"></h3>
                                        <span id="type_2_span" class="help-block">หุ้นชุมนุม</span>
                                    </div>
                                    <div class="col-md-3 text-right invest-title">
                                        <button type="button" id="share_c_account_edit_btn" class="btn btn-primary account_edit_btn" data_id=""><span class="icon icon-cog"></span> ตั้งค่าบัญชี</button>
                                        <button type="button" id="share_c_edit_btn" class="btn btn-primary" data_id=""><span class="icon icon-pencil"></span> แก้ไข</button>
                                    </div>
                                </div>
                                <div class="row row_detail">
                                    <div class="col-md-6 col_detail_left">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3 id="share_c_t_amount" class="no_b_space title_font dark_font"></h3>
                                                <span  class="help-block no_t_space no_b_space">เงินลงทุน</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span id="share_c_d_soucre" class="help-block no_t_space"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12 col_detail_right">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h3 id="share_c_t_balance" class="second_text no_b_space title_font"></h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span class="help-block no_t_space">รวมปันผลที่ได้รับ</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-9">
                                        <label class="invest-title">ประวัติซื้อหุ้นย้อนหลัง</label>
                                    </div>
                                    <div class="col-md-3 text-right invest-title">
                                        <button type="button" id="share_add_tran_btn" class="btn second_bg" data_id=""><span class="icon icon-plus"></span> เพิ่ม</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <table class="table table-bordered table-center table_invest_list table-striped no_t_space table_sub">
                                        <thead> 
                                            <tr style="border-bottom: 3px solid #eceff1;">
                                                <th width="17%">วันที่</th>
                                                <th width="15%">จำนวนหุ้น</th>
                                                <th width="20%">มูลค่า</th>
                                                <th>หมายเหตุ</th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="share-tran-tbody">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <span id="share-t-c-total"></span>
                                    </div>
                                </div>
                            </div>
                            <div id="bond_c_detail" class="col-md-12" style="display:none;">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h3 class="invest-title dark_font" id="bond_t_name"></h3>
                                        <span class="help-block" id='bond_help_title'>พันธบัตรรัฐบาล</span>
                                        <span class="help-block" id='share_p_help_title'>หุ้นกู้เอกชน</span>
                                    </div>
                                    <div class="col-md-3 text-right invest-title">
                                        <button type="button" id="bond_account_edit_btn" class="btn btn-primary account_edit_btn" data_id=""><span class="icon icon-cog"></span> ตั้งค่าบัญชี</button>
                                        <button type="button" id="bond_edit_btn" class="btn btn-primary" data_id=""><span class="icon icon-pencil"></span> แก้ไข</button>
                                    </div>
                                </div>
                                <div class="row row_detail">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4 col_detail_left">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h3 id="bond_t_amount" class="no_b_space title_font dark_font"></h3>
                                                        <span  class="help-block no_t_space no_b_space">มูลค่าเฉลีย</span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span id="bond_d_soucre" class="help-block no_t_space"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col_detail_left">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h3 id="bond_t_time_left" class="no_b_space title_font dark_font"></h3>
                                                        <span class="help-block no_t_space no_b_space">อายุคงเหลือ</span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span id="" class="help-block no_t_space">&nbsp</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row" style="display:none;">
                                                    <div class="col-md-12 col_detail_right">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <h3 id="bond_t_balance" class="second_text no_b_space title_font"></h3>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <span class="help-block no_t_space">รวมทั้งหมด</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <span id="bond_diff_percent" class="helpblock_plus text-edit"></span>
                                                                <span id="bond_diff_percent_arrow" class="icon icon-arrow-up text-edit"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 col_detail_right">
                                                        <h3 id="bond_t_profit" class="second_text no_b_space title_font"></h3>
                                                        <span  class="help-block no_t_space">รวมดอกเบี้ยที่ได้รับ</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>ข้อมูลการลงทุน</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>ชื่อรุ่น</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="bond_d_name"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>Credit Rating</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="bond_d_credit_rating"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>จำนวนหน่วย</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="bond_d_unit"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>มูลค่าเฉลี่ย</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="bond_d_value_per_unit"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>ผลตอบแทนเฉลี่ย</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="bond_d_aver_profit"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>ดอกเบี้ย</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="bond_d_interest"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>วันที่ซื้อ</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="bond_d_start_date"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>วันที่ครบกำหนด</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="bond_d_due_date"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-md-offset-1">
                                        <span>รอบการจ่ายดอกเบี้ย</span>
                                    </div>
                                    <div class="col-md-9">
                                        <span id="bond_d_payment_method_text"></span>
                                    </div>
                                </div>
                            </div>
                            <div id="share_s_detail" class="col-md-12" style="display:none;">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h3 class="invest-title dark_font" id="share_s_t_name"></h3>
                                        <span id="type_5_span" class="help-block">หุ้นทุนในตลาดหลักทรัพย์</span>
                                    </div>
                                    <div class="col-md-3 text-right invest-title">
                                        <button type="button" id="share_s_account_edit_btn" class="btn btn-primary account_edit_btn" data_id=""><span class="icon icon-cog"></span> ตั้งค่าบัญชี</button>
                                        <button type="button" id="share_s_edit_btn" class="btn btn-primary" data_id=""><span class="icon icon-pencil"></span> แก้ไข</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 col_detail_left">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h3 id="share_s_t_amount" class="no_b_space title_font dark_font"></h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span  class="help-block no_t_space" style="display: inline;">เงินลงทุน </span>
                                                        <span id="share_s_d_info" class="second_text no_t_space no_b_space"></span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span id="share_s_d_soucre" class="help-block no_t_space no_b_space"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-12 col_detail_left">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h3 id="share_s_t_balance" class="second_text no_b_space title_font"></h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span id="share_s_rate_date" class="helpblock_dark no_b_space no_t_space"></span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <span class="helpblock_dark">มูลค่าต่อหุ้น </span>
                                                        <span id="share_s_t_rate" class="second_text no_b_space no_t_space"></span>
                                                        <span class="helpblock_dark"> บาท </span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <button type="button" id="share_s_rate_edit_btn" class="btn second_bg helpbtn" data_id=""><span class="icon icon-pencil"></span> แก้ไขมูลค่า</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col_detail_left">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3 id="share_s_t_diff" class="no_b_space title_font second_text" style="display: inline-block;"></h3>
                                                <span id="share_s_rate_diff" class="helpblock_plus text-edit no_b_space no_t_space"></span>
                                            </div>
                                            <div class="col-md-6" style="margin-top: 20px;margin-bottom: 0px;">
                                               
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="no_b_space no_t_space">ผลต่าง</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-12 col_detail_right">
                                                <div class="row">
                                                    <div class="col-md-12" style="margin-top: 20px;margin-bottom: 0px;">
                                                        <h3 id="share_s_t_profit" class="second_text no_b_space no_t_space title_font"></h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span  class="help-block no_t_space">รวมปันผลที่ได้รับ</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-9">
                                        <label class="invest-title">ประวัติซื้อหุ้นย้อนหลัง</label>
                                    </div>
                                    <div class="col-md-3 text-right invest-title">
                                        <button type="button" id="share_s_add_tran_btn" class="btn second_bg" data_id=""><span class="icon icon-plus"></span> เพิ่ม</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-9">
                                        <label class="invest-title">ซื้อ</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <table class="table table-center table-bordered table-striped table_invest_list no_t_space" style="margin-left: 10%; width: 80%;">
                                        <thead> 
                                            <tr style="border-bottom: 0;">
                                                <th width="16.66%">วันที่</th>
                                                <th width="16.66%">หุ้น</th>
                                                <th width="16.66%">มูลค่า</th>
                                                <th width="16.66%">มูลค่าเฉลี่ยต่อหุ้น</th>
                                                <th width="16.66%">ค่าธรรมเนียม</th>
                                                <th width="16.66%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="share-s-tran-tbody-buy">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    &nbsp;
                                </div>
                                <div class="row">
                                    <div class="col-md-9">
                                        <label class="invest-title">ขาย</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <table class="table table-center table-bordered table-striped table_invest_list no_t_space" style="margin-left: 10%; width: 80%;">
                                        <thead> 
                                            <tr style="border-bottom: 0;">
                                                <th width="16.66%">วันที่</th>
                                                <th width="16.66%">หุ้น</th>
                                                <th width="16.66%">มูลค่า</th>
                                                <th width="16.66%">กำไร/ขาดทุน</th>
                                                <th width="16.66%">ค่าธรรมเนียม</th>
                                                <th width="16.66%"></th>

                                            </tr>
                                        </thead>
                                        <tbody id="share-s-tran-tbody-sell" class="common_font">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <span id="share-t-c-total"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-body">
                    <div class="card-body card_list">
                        <div class="row">
                            <div id="interest_card" class="col-md-12" style="display: none;">
                                <div class="row">
                                    <div class="col-md-9">
                                        <label class="invest-title">ประวัติรับดอกเบี้ยย้อนหลัง</label>
                                    </div>
                                    <div class="col-md-3 text-right invest-title">
                                        <button type="button" id="interest_add_btn" class="btn btn-primary" data_id=""><span class="icon icon-plus"></span> เพิ่ม</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card" style="display: block; max-width: 60%;  margin: auto;">
                                            <div class="card-chart chart-container"  style="display: block; max-width: 100%; ">
                                                <canvas id="myChart" style="max-width: 100%; max-height: 300px;"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <br>
                                <div class="row">
                                    <table class="table table-bordered table-center table_invest_list table-striped no_t_space" style="max-width: 60%; margin: auto;">
                                        <thead> 
                                            <tr style="border-bottom: 3px solid #eceff1;">
                                                <th width="17%">วันที่</th>
                                                <th width="15%">อัตราดอกเบี้ย</th>
                                                <th width="17%">ดอกเบี้ย</th>
                                                <th >หมายเหตุ</th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="profit-tbody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="dividend_card" class="col-md-12" style="display: none;">
                                <div class="row">
                                    <div class="col-md-9">
                                        <label class="invest-title">ประวัติเงินปันผลย้อนหลัง</label>
                                    </div>
                                    <div class="col-md-3 text-right invest-title">
                                        <button type="button" id="dividend_add_btn" class="btn second_bg" data_id=""><span class="icon icon-plus"></span> เพิ่ม</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card" style="display: block; max-width: 60%;margin: auto;">
                                            <div class="card-chart"  style="display: block; max-width: 100%;">
                                                <canvas id="dividendChart" style="max-width: 100%; max-height: 300px;"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <table class="table table-bordered table-center table_invest_list table-striped no_t_space" style="max-width: 60%; margin: auto;">
                                        <thead> 
                                            <tr style="border-bottom: 3px solid #eceff1;">
                                                <th>วันที่</th>
                                                <th>ปันผล</th>
                                                <th>จำนวนเงิน</th>
                                                <th>หมายเหตุ</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="dividend-tbody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
                    <div class="row status_edit_row">
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
<div id="interest_modal" tabindex="-1" role="dialog" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">เพิ่มประวัติการรับดอกเบี้ย</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(PROJECTPATH.'/invest/add_interest'); ?>" method="post" id="add_interest_form">
                    <input id="add_interest_invest_id" name="invest_id" type="hidden" value="">
                    <input id="add_interest_interest_id" name="id" type="hidden" value="">
                    <div class="row">
                        <label class="col-sm-3 control-label right"> ชื่อชุมนุม </label>
                        <label class="col-sm-9 control-label text-left" id="interest-add-branch-name"></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-3 control-label right"> วันที่ได้รับดอกเบี้ย </label>
                        <div class="col-sm-6">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="interest_date" name="date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> อัตราดอกเบี้ย </label>
                            <div class="col-sm-6">
                                <input id="interest_rate" name="rate" class="form-control m-b-1 num_input" onKeyUp="format_the_number_decimal(this)" value="0">
                            </div>
                            <label class="col-sm-3 control-label text-left p-l-0">%</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> ดอกเบี้ยที่ได้รับ </label>
                            <div class="col-sm-6">
                                <input id="interest_amount" name="amount" class="form-control m-b-1 num_input" onKeyUp="format_the_number_decimal(this)" value="0">
                            </div>
                            <label class="col-sm-3 control-label text-left p-l-0">บาท</label>
                        </div>
                    </div>
                    <div class="row coop_sav_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">หมายเหตุ</label>
                            <div class="col-sm-6">
                                <input id="interest_note" name="note" class="form-control m-b-1" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" id="interest_submit_add">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" id="interest_cancel_add">ยกเลิก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="dividend_modal" tabindex="-1" role="dialog" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">เพิ่มประวัติการรับเงินปันผล</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(PROJECTPATH.'/invest/add_interest'); ?>" method="post" id="add_dividend_form">
                    <input id="add_dividend_invest_id" name="invest_id" type="hidden" value="">
                    <input id="add_dividend_interest_id" name="id" type="hidden" value="">
                    <div class="row">
                        <label class="col-sm-3 control-label right" id="dividend_branch_name_t_label"> ชื่อชุมนุม </label>
                        <label class="col-sm-9 control-label text-left" id="dividend-add-branch-name"></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-3 control-label right"> วันที่ได้รับปันผล </label>
                        <div class="col-sm-6">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="dividend_date" name="date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> % ปันผล </label>
                            <div class="col-sm-6">
                                <input id="dividend_rate" name="rate" class="form-control m-b-1 num_input" onKeyUp="format_the_number_decimal(this)" value="0">
                            </div>
                            <label class="col-sm-3 control-label text-left p-l-0">%</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> จำนวนเงิน </label>
                            <div class="col-sm-6">
                                <input id="dividend_amount" name="amount" class="form-control m-b-1 num_input" onKeyUp="format_the_number_decimal(this)" value="0">
                            </div>
                            <label class="col-sm-3 control-label text-left p-l-0">บาท</label>
                        </div>
                    </div>
                    <div class="row coop_sav_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">หมายเหตุ</label>
                            <div class="col-sm-6">
                                <input id="dividend_note" name="note" class="form-control m-b-1" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" id="dividend_submit_add">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" id="dividend_cancel_add">ยกเลิก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="share_c_modal" tabindex="-1" role="dialog" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">เพิ่มประวัติการซื้อหุ้น</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(PROJECTPATH.'/invest/add_transaction'); ?>" method="post" id="share_c_form">
                    <input id="share_c_m_invest_id" name="invest_id" type="hidden" value="">
                    <input id="share_c_m_tran_id" name="id" type="hidden" value="">
                    <input id="share_c_m_tran_type" name="tran_type" type="hidden" value="1">
                    <div class="row">
                        <label class="col-sm-3 control-label right"> ชื่อชุมนุม </label>
                        <label class="col-sm-3 control-label text-left" id="share_c_m_branch_name"></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-3 control-label right"> วันที่ลงทุน </label>
                        <div class="col-sm-6">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="share_c_m_date" name="date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> จำนวนหุ้น </label>
                            <div class="col-sm-6">
                                <input id="share_c_m_unit" name="unit" class="form-control m-b-1 num_input" onKeyUp="format_the_number_decimal(this)" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> มูลค่าหุ้น </label>
                            <div class="col-sm-6">
                                <input id="share_c_m_amount" name="amount" class="form-control m-b-1 num_input" onKeyUp="format_the_number_decimal(this)" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row coop_sav_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">หมายเหตุ</label>
                            <div class="col-sm-6">
                                <input id="share_c_m_note" name="note" class="form-control m-b-1" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" id="share_c_m_submit">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" id="share_c_m_cancel">ยกเลิก</button>
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
                <form action="<?php echo base_url(PROJECTPATH.'/invest/add_invest_interest_value'); ?>" method="post" id="share_val_form">
                    <input id="share_val_invest_id" name="invest_id" type="hidden" value="">
                    <div class="row">
                        <label class="col-sm-3 control-label right"> ชื่อหุ้น </label>
                        <label class="col-sm-3 control-label text-left" id="share_val_branch_name"></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-3 control-label right"> ข้อมูล ณ วันที่ </label>
                        <div class="col-sm-6">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="share_val_date" name="date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> มูลค่าหุ้น </label>
                            <div class="col-sm-6">
                                <input id="share_val_amount" name="amount" class="form-control m-b-1 num_input" onKeyUp="format_the_number_decimal(this)" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" id="share_val_submit">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" id="share_val_cancel">ยกเลิก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="share_s_modal" tabindex="-1" role="dialog" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">เพิ่มประวัติการซื้อหุ้น</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(PROJECTPATH.'/invest/add_transaction'); ?>" method="post" id="share_s_form">
                    <input id="share_s_m_invest_id" name="invest_id" type="hidden" value="">
                    <input id="share_s_m_tran_id" name="id" type="hidden" value="">
                    <div class="row">
                        <label class="col-sm-3 control-label right"> ชื่อหุ้น </label>
                        <label class="col-sm-3 control-label text-left" id="share_s_m_branch_name"></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-3 control-label right"> วันที่ลงทุน </label>
                        <div class="col-sm-6">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="share_s_m_date" name="date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> ซื้อ/ขาย </label>
                            <div class="col-sm-6">
                                <span style="">
                                    <input type="radio" name="tran_type" id="tran_type_1" value="1" checked="checked"> ซื้อ &nbsp;&nbsp;
                                    <input type="radio" name="tran_type" id="tran_type_2" value="2"> ขาย &nbsp;&nbsp;
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> จำนวนหุ้น </label>
                            <div class="col-sm-6">
                                <input id="share_s_m_unit" name="unit" class="form-control m-b-1 num_input share_s_cal" onKeyUp="format_the_number_decimal(this)" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> มูลค่าหุ้น </label>
                            <div class="col-sm-6">
                                <input id="share_s_m_amount" name="amount" class="form-control m-b-1 num_input share_s_cal" onKeyUp="format_the_number_decimal(this)" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> ค่าธรรมเนียม </label>
                            <div class="col-sm-6">
                                <input id="share_s_m_fee" name="fee" class="form-control m-b-1 num_input share_s_cal" onKeyUp="format_the_number_decimal(this)" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> ภาษี </label>
                            <div class="col-sm-6">
                                <input id="share_s_m_tax" name="tax" class="form-control m-b-1 num_input share_s_cal" onKeyUp="format_the_number_decimal(this)" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"> มูลค่าหุ้นต่อหน่วย </label>
                            <div class="col-sm-6">
                                <label class="col-sm-3 control-label text-left" id="share_s_m_rate">  </label>
                            </div>
                        </div>
                    </div>
                    <div class="row coop_sav_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">หมายเหตุ</label>
                            <div class="col-sm-6">
                                <input id="share_s_m_note" name="note" class="form-control m-b-1" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" id="share_s_m_submit">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" id="share_s_m_cancel">ยกเลิก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="account_match_modal" tabindex="-1" role="dialog" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">การสร้างข้อมูลอัตโนมัติ</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(PROJECTPATH.'/invest/add_account_match'); ?>" method="post" id="account_match_form">
                <input id="account_match_invest_id" name="invest_id" type="hidden" value="">
                <input id="account_match_type" name="type" type="hidden" value="">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-6" style="margin-top:5px;">
                                <input type="radio" class="match_status" name="status" id="account_match_enable" value="1"/><span id="status_enable_text"> เปิดใช้งาน </span>&nbsp;&nbsp;
                                <input type="radio" class="match_status" name="status" id="account_match_disable" value="2" checked><span> ไม่ใช้งาน </span>&nbsp;&nbsp;
                            </div>
                        </div>
                    </div>
                    <!-- s_c_m_r : saving coop modal row -->
                    <div class="row s_c_m_r m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">เพิ่มเงินลงทุน</label>
                        </div>
                    </div>
                    <div class="row s_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="1_invest_increase_desc" name="data[1][invest_increase][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row s_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="1_invest_increase_chart_credit" name="data[1][invest_increase][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row s_c_m_r m-b-2">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="1_invest_increase_chart_debit" name="data[1][invest_increase][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row s_c_m_r m-b-1 m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">ลดเงินลงทุน</label>
                        </div>
                    </div>
                    <div class="row s_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="1_invest_decrease_desc" name="data[1][invest_decrease][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row s_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="1_invest_decrease_chart_credit" name="data[1][invest_decrease][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row s_c_m_r m-b-2">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="1_invest_decrease_chart_debit" name="data[1][invest_decrease][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row s_c_m_r m-b-1 m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">รับดอกเบี้ย</label>
                        </div>
                    </div>
                    <div class="row s_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="1_interest_desc" name="data[1][interest][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row s_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="1_interest_chart_credit" name="data[1][interest][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row s_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="1_interest_chart_debit" name="data[1][interest][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row s_c_m_r m-b-1 m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">ลดดอกเบี้ย(กรณีแก้ไขข้อมูล)</label>
                        </div>
                    </div>
                    <div class="row s_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="1_interest_decrease_desc" name="data[1][interest_decrease][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row s_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="1_interest_decrease_credit" name="data[1][interest_decrease][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row s_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="1_interest_decrease_debit" name="data[1][interest_decrease][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- sh_c_m_r : share coop modal row -->
                    <div class="row sh_c_m_r m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">ซื้อหุ้น</label>
                        </div>
                    </div>
                    <div class="row sh_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="2_invest_increase_desc" name="data[2][invest_increase][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row sh_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="2_invest_increase_chart_credit" name="data[2][invest_increase][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row sh_c_m_r m-b-2">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="2_invest_increase_chart_debit" name="data[2][invest_increase][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row sh_c_m_r m-b-1 m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">ขายหุ้น</label>
                        </div>
                    </div>
                    <div class="row sh_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="2_invest_decrease_desc" name="data[2][invest_decrease][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row sh_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="2_invest_decrease_chart_credit" name="data[2][invest_decrease][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row sh_c_m_r m-b-2">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="2_invest_decrease_chart_debit" name="data[2][invest_decrease][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row sh_c_m_r m-b-1 m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">รับปันผล</label>
                        </div>
                    </div>
                    <div class="row sh_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="2_dividend_desc" name="data[2][dividend][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row sh_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="2_dividend_chart_credit" name="data[2][dividend][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row sh_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="2_dividend_chart_debit" name="data[2][dividend][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row sh_c_m_r m-b-1 m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">ลดปันผล(กรณีแก้ไขข้อมูล)</label>
                        </div>
                    </div>
                    <div class="row sh_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="2_dividend_decrease_desc" name="data[2][dividend_decrease][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row sh_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="2_dividend_decrease_credit" name="data[2][dividend_decrease][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row sh_c_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="2_dividend_decrease_debit" name="data[2][dividend_decrease][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- b_m_r : bond modal row -->
                    <div class="row b_m_r m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">เพิ่มเงินลงทุน</label>
                        </div>
                    </div>
                    <div class="row b_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="3_invest_increase_desc" name="data[3][invest_increase][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row b_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="3_invest_increase_chart_credit" name="data[3][invest_increase][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row b_m_r m-b-2">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="3_invest_increase_chart_debit" name="data[3][invest_increase][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row b_m_r m-b-1 m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">ลดเงินลงทุน</label>
                        </div>
                    </div>
                    <div class="row b_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="3_invest_decrease_desc" name="data[3][invest_decrease][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row b_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="3_invest_decrease_chart_credit" name="data[3][invest_decrease][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row b_m_r m-b-2">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="3_invest_decrease_chart_debit" name="data[3][invest_decrease][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row b_m_r m-b-1 m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">รับดอกเบี้ย</label>
                        </div>
                    </div>
                    <div class="row b_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="3_interest_desc" name="data[3][interest][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row b_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="3_interest_chart_credit" name="data[3][interest][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row b_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="3_interest_chart_debit" name="data[3][interest][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row b_m_r m-b-1 m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">ลดดอกเบี้ย(กรณีแก้ไขข้อมูล)</label>
                        </div>
                    </div>
                    <div class="row b_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="3_interest_decrease_desc" name="data[3][interest_decrease][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row b_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="3_interest_decrease_credit" name="data[3][interest_decrease][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row b_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="3_interest_decrease_debit" name="data[3][interest_decrease][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                     <!-- s_s_m_r : share set modal row -->
                     <div class="row s_s_m_r m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">ซื้อหุ้น</label>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="5_invest_increase_desc" name="data[5][invest_increase][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">เงินจ่ายรับ(เครดิต)</label>
                            <div class="col-sm-6">
                                <select id="5_invest_increase_chart_cash_credit" name="data[5][invest_increase][cash_credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ภาษี(เครดิต)</label>
                            <div class="col-sm-6">
                                <select id="5_invest_increase_chart_tax_credit" name="data[5][invest_increase][tax_credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-2">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">มูลค่าหุ้น(เดบิต)</label>
                            <div class="col-sm-6">
                                <select id="5_invest_increase_share_debit" name="data[5][invest_increase][share_debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-2">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">มูลค่าหุ้น(เดบิต)</label>
                            <div class="col-sm-6">
                                <select id="5_invest_increase_fee_debit" name="data[5][invest_increase][fee_debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row s_s_m_r m-b-1 m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">ขายหุ้น</label>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="5_invest_decrease_desc" name="data[5][invest_decrease][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">มูลค่าหุ้น(เครดิต)</label>
                            <div class="col-sm-6">
                                <select id="5_invest_decrease_share_credit" name="data[5][invest_decrease][share_credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ภาษี(เครดิต)</label>
                            <div class="col-sm-6">
                                <select id="5_invest_decrease_tax_credit" name="data[5][invest_decrease][tax_credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-2">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">เงินด้านรับ(เดบิต)</label>
                            <div class="col-sm-6">
                                <select id="5_invest_decrease_cash_debit" name="data[5][invest_decrease][cash_debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-2">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายได้(เดบิต)</label>
                            <div class="col-sm-6">
                                <select id="5_invest_decrease_profit_debit" name="data[5][invest_decrease][profit_debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-2">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ค่าธรรมเนียม(เดบิต)</label>
                            <div class="col-sm-6">
                                <select id="5_invest_decrease_fee_debit" name="data[5][invest_decrease][fee_debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row s_s_m_r m-b-1 m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">รับปันผล</label>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="5_dividend_desc" name="data[5][dividend][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="5_dividend_chart_credit" name="data[5][dividend][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row s_s_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="5_dividend_chart_debit" name="data[5][dividend][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-1 m-f-1">
                        <div class="form-group">
                            <label class="col-sm-12 control-label text-left">ลดปันผล(กรณีแก้ไขข้อมูล)</label>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียด</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control account_desc" id="5_dividend_decrease_desc" name="data[5][dividend_decrease][desc]">
                            </div>
                        </div>
                    </div>
                    <div class="row s_s_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเครดิต</label>
                            <div class="col-sm-6">
                                <select id="5_dividend_decrease_credit" name="data[5][dividend_decrease][credit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div><div class="row s_s_m_r m-b-1">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">บัญชีฝั่งเดบิต</label>
                            <div class="col-sm-6">
                                <select id="5_dividend_decrease_debit" name="data[5][dividend_decrease][debit]" class="form-control m-b-1 js-data-example-ajax">
                                    <option value="">เลือกรหัสผังบัญชี</option>
                                    <?php 
                                        foreach($account_charts as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="row">&nbsp;</div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" id="account_match_submit">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" id="account_match_cancel">ยกเลิก</button>
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
<form action="<?php echo base_url(PROJECTPATH.'/invest/detail'); ?>" method="post" id="reload_form">
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
$link = array(
    'src' => PROJECTJSPATH.'assets/js/select2.full.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
<script>
    $(document).ready(function() {
        $("#submit_add").click(function() {
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
            $(".num_input").each(function(index) {
                $(this).val(removeCommas($(this).val()));
            });
            $.post(base_url+"invest/edit",
            $("#add_form").serialize(),
            function(result) {
                data = JSON.parse(result);
                invest_id = data.invest_id;
                $("#invest_modal").modal('hide');
                display_detail(invest_id);
            });
        });
    });
</script>