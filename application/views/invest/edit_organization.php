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
            width:33.33%;
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
</style>
<div class="layout-content">
    <div class="layout-content-body">
        <h1 class="title_top">ตั้งค่าองค์กร</h1>
        <p style="font-family: upbean; font-size: 20px; margin-bottom:5px;"><?php $this->load->view('breadcrumb'); ?></p>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="card-body card_list">
                        <div class="col-xs-12 col-md-12 text-right">
                            <button type="button" id="add_org" class="btn second_bg" data-dismiss="modal"><span class="icon icon-plus"></span> เพิ่มองค์กร</button>
                        </div>
                        <div class="col-xs-12 col-md-12 text-center">
                            <br><br>
                            <table class="table table-striped table-bordered table-center table_invest_list" style="width:60%; margin: auto;">
                                <thead>
                                    <tr style="border-bottom: 3px solid #bdbdbd;">
                                        <th width="10%">ลำดับ</th>
                                        <th width="">ชื่อองค์กร</th>
                                        <th width="15%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach($orgs as $org) {
                                    ?>
                                    <tr>
                                        <td class="border_td" width="5%"><?php echo ++$index;?></td>
                                        <td width="" class="text_left"><?php echo $org['name'];?></td>
                                        <td width="">
                                            <a class="edit_btn " style="cursor: pointer;" class="text-default" id="edit_<?php echo $org['id'];?>" data-id="<?php echo $org['id'];?>">แก้ไข</a>
                                            |
                                            <a class="del_btn text-danger" style="cursor: pointer;" class="text-default" id="del_<?php echo $org['id'];?>" data-id="<?php echo $org['id'];?>">ลบ</a>
                                        </td>
                                    </tr>
                                    <?php
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
<div id="edit_modal" tabindex="-1" role="dialog" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">เพิ่มการลงทุน</h2>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="add_form">
                <input id="modal_id" name="id" type="hidden" value="">
                    <div class="row coop_sav_c_row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ชื่อองค์กร</label>
                            <div class="col-sm-6">
                                <input id="add_name" name="name" class="form-control m-b-1" type="text" value="">
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

<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/invest_org.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
<script>
    $(document).ready(function() {

    });
</script>