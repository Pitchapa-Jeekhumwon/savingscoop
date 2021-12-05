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
            #account_data_cash_table, #account_data_table, #account_data_sp_table {
                display: block;
                max-height: 500px;
                overflow-y:scroll;
            }
            .bt-default, .bt-default:hover, .bt-default:focus, .bt-default:active {
                background-color: #757575;
                border-color: #757575;
                color: #fff;
            }
        </style>
        <h1 style="margin-bottom: 0">รายการชำระ</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_account()">
                    <span class="icon icon-plus-circle"></span>
                    เพิ่มรายการ
                </button>
                <?php if(!empty($first_open_budget_year)) { ?>
                <button class="btn btn-primary btn-lg bt-add" style="margin-right: 10px;" type="button" onclick="close_account()">
                    ปิดปีบัญชี
                </button>
                <?php } ?>
                <?php if(!empty($last_close_budget_year) && $open_account_year_user_active) { ?>
                <button class="btn btn-primary btn-lg bt-add" style="margin-right: 10px;" type="button" onclick="open_account()">
                    เปิดปีบัญชีเก่า
                </button>
                <?php } ?>
                <?php
                    if(empty($_POST)) {
                        if(!empty($daily_status)) {
                ?>
                <button class="btn btn-lg bt-add <?php echo !empty($year_is_close) ? 'bt-default' : 'btn-primary';?>" style="margin-right: 10px;" type="button" <?php if(empty($year_is_close)) { ?>onclick="open_daily('<?php echo $account_date;?>')" <?php } ?>>
                    เปิดบัญชีรายวัน
                </button>
                <?php } else { ?>
                <button class="btn btn-lg bt-add <?php echo !empty($year_is_close) ? 'bt-default' : 'btn-primary';?>" style="margin-right: 10px;" type="button" <?php if(empty($year_is_close)) { ?>onclick="close_daily('<?php echo $account_date;?>')" <?php } ?>>
                    ปิดบัญชีรายวัน
                </button>
                <?php }
                    }
                ?>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <!-- Search Section. -->
                    <div class="row">
                        <div class="form-group">
                            <!-- <label class="col-sm-6 control-label"></label> -->
                            <div class="col-sm-10">
                                <div class="input-with-icon">
                                <form action="<?php echo base_url(PROJECTPATH.'/account'); ?>" method="post" id="search_form">
                                    <div class="form-group row">
                                        <label class="g24-col-sm-5 control-label" for="form-control-2">วันที่</label>
                                        <div class="g24-col-sm-5" >
                                            <div class="form-group">
                                                <input id="search_from_date" name="from_date" class="form-control m-b-1 type_input form_date_picker" type="text" value="<?php echo !empty($_POST['from_date']) ? $_POST['from_date'] : ""; ?>" data-date-language="th-th" style="padding-left:38px;" autocomplete="off">
                                                <span class="icon icon-calendar input-icon m-f-1"></span>
                                            </div>
                                        </div>
                                        <label class="g24-col-sm-1 control-label text-center" for="form-control-2">ถึง</label>
                                        <div class="g24-col-sm-5" >
                                            <div class="form-group">
                                                <input id="search_thru_date" name="thru_date" class="form-control m-b-1 type_input form_date_picker" type="text" value="<?php echo !empty($_POST['thru_date']) ? $_POST['thru_date'] : ""; ?>" data-date-language="th-th" style="padding-left:38px;" autocomplete="off">
                                                <span class="icon icon-calendar input-icon m-f-1"></span>
                                            </div>
                                        </div>
                                        <label class="g24-col-sm-3 control-label text-center" for="form-control-2">เลขที่ใบสำคัญ</label>
                                        <div class="g24-col-sm-5" >
                                            <input id="search_voucher" name="journal_ref" class="form-control m-b-1 type_input" type="text" value="<?php echo !empty($_POST['journal_ref']) ? $_POST['journal_ref'] : '';?>" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="g24-col-sm-5 control-label" for="form-control-2">ประเภท</label>
                                        <label class="control-label">
                                            <input type="checkbox" class="search_journal_type" id="search_chk_all" name="" value="">
                                            ทั้งหมด &nbsp; &nbsp;
                                            <input type="checkbox" class="search_journal_type" id="search_chk_rv" name="journal_type[]" value="RV" <?php echo !empty($_POST['journal_type']) && in_array("RV", $_POST['journal_type']) ?  "checked" : ""?>>
                                            ด้านรับ &nbsp; &nbsp;
                                            <input type="checkbox" class="search_journal_type" id="search_chk_pv" name="journal_type[]" value="PV" <?php echo !empty($_POST['journal_type']) && in_array("PV", $_POST['journal_type']) ?  "checked" : ""?>>
                                            ด้านจ่าย &nbsp; &nbsp;
                                            <input type="checkbox" class="search_journal_type" id="search_chk_jv" name="journal_type[]" value="JV" <?php echo !empty($_POST['journal_type']) && in_array("JV", $_POST['journal_type']) ?  "checked" : ""?>>
                                            ด้านโอน &nbsp; &nbsp;
                                            <input type="checkbox" class="search_journal_type" id="search_chk_sv" name="journal_type[]" value="SV" <?php echo !empty($_POST['journal_type']) && in_array("SV", $_POST['journal_type']) ?  "checked" : ""?>>
                                            ปรับปรุง
                                        </label>
                                    </div>
                                </form>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group text-left">
                                    <button class="btn btn-primary btn-lg" type="button" id="search_btn">
                                        ค้นหา
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="font-normal" width="20%">วันที่</th>
                                    <th class="font-normal"> รายการ </th>
                                    <th class="font-normal" width="15%"> รหัสบัญชี </th>
                                    <th class="font-normal" width="15%"> เดบิต </th>
                                    <th class="font-normal" width="15%"> เครดิต </th>
                                </tr>
                                <tr>
                                    <th class="font-normal" width="20%"></th>
                                    <th class="font-normal">รายละเอียด</th>
                                    <th class="font-normal" width="15%">เลขที่ใบสำคัญ</th>
                                    <th class="font-normal" width="15%"></th>
                                    <th class="font-normal" width="15%"></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $k_count=1;
                            $firest_p = $firest_p-1;
                            $i=1;
                            foreach($data_account_detail as $key_main => $row) {
                                foreach($row as $key => $row1) {
                                    $i=1;
                                    $description = "";
                                    $status = "";
                                    $account_id = "";
                                    $total_debit = 0;
                                    $total_credit = 0;
                                    $is_close = 0;
                                    foreach($row1 as $key2 => $row_detail){
                            ?>
                                            <tr>
                                                <td><?php echo $i=='1'?$this->center_function->ConvertToThaiDate($key_main,'1','0'):''; ?></td>
                                                <td width="35%" class="text_left">
                                                    <?php echo $row_detail['account_type']=='debit'?$row_detail['account_chart']:$space.$row_detail['account_chart']; ?>
                                                </td>
                                                <td><?php echo $row_detail['account_chart_id']; ?></td>
                                                <td class="text_right"><?php echo $row_detail['account_type']=='debit'?number_format($row_detail['account_amount'],2):''; ?></td>
                                                <td class="text_right"><?php echo $row_detail['account_type']=='credit'?number_format($row_detail['account_amount'],2):''; ?></td>
                                                <td class="text_right">
                                                </td>
                                            </tr>
                            <?php
                                        $description = $row_detail['account_description'];
                                        $account_id  = $row_detail['account_id'];
                                        if ($row_detail["run_status"] == 1) {
                                            $status = "ผ่านรายการแล้ว";
                                        } else if ($row_detail["run_status"] == 2) {
                                            $status = "ยกเลิก";
                                        }

                                        if($row_detail['account_type']=='debit') {
                                            $total_debit += round($row_detail['account_amount'], 2);
                                        } else if ($row_detail['account_type']=='credit') {
                                            $total_credit += round($row_detail['account_amount'], 2);
                                        }

                                        $is_close = $row_detail['is_close'];

                                        $i++;
                                    }
                            ?>
                                        <tr>
                                            <td class="text-center" colspan="3">รวม</td>
                                            <td class="text-right"><?php echo number_format($total_debit,2); ?></td>
                                            <td class="text-right"><?php echo number_format($total_credit,2); ?></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td class="text_left"><?php echo $description; ?></td>
                                            <td class="text-center"><?php echo $row_detail['journal_ref'];?></td>
                                            <td class="text_right" colspan="2">
                                                <?php if($row_detail['count'] > 0) { ?>
                                                    <button id="bt_his_<?php echo $key;?>" type="button" class="btn btn-primary m-b-1 btn_his" style="width:unset;" data-id="<?php echo $key;?>" >
                                                        <span>ประวัติ</span>
                                                    </button>
                                                <?php } ?>
                                                <?php if(!(!empty($is_close) || !empty($daily_status))) { ?>
                                                <input id="cancel-btn-<?php echo $account_id;?>" class="form-control m-b-1 btn btn-danger cancel-acc-btn" type="button" value="ลบ" data-account-id="<?php echo $account_id;?>">
                                                <input id="edit-btn-<?php echo $account_id;?>" class="form-control m-b-1 btn btn-primary edit-acc-btn" type="button" value="แก้ไข" data-account-id="<?php echo $account_id;?>">
                                                <?php } ?>
                                            </td>
                                        </tr>
                            <?php
                                    $i++;
                                    $k_count++;
                                }
                                $k_count++;
                                $i++;
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
<div id="add_account_type" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">เลือกประเภท</h2>
            </div>
            <div class="modal-body">
                <div class="form-group text-center">
                    <button type="button" class="btn btn-primary min-width-100" onclick="tran_modal(1)">เงินสด</button>
                    <button class="btn btn-danger min-width-100" type="button" onclick="tran_modal(2)">เงินโอน</button>
                    <button class="btn btn-primary min-width-100" type="button" onclick="tran_modal(3)">ปรัปปรุง</button>
                <?php
                    if(!empty($allow_flexible)) {
                ?>
                    <button class="btn btn-danger min-width-100" type="button" onclick="tran_modal(4)">พิเศษ</button>
                <?php
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="add_account_tran" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account" style="width: 100%; height: 100%; margin: 0; padding: 0;">
        <div class="modal-content" style="width: 100%; height: 100%; margin: 0; padding: 0;">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">บันทึกรายการบัญชี</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(PROJECTPATH.'/account/account_save'); ?>" method="post" id="form1">
                    <input id="input_number" type="hidden" value="0">
                    <input id="journal_type_tran" name="journal_type" type="hidden" value="JV">
                    <input id="account_id_tran" name="account_id" type="hidden" value="">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">วันที่</label>
                            <div class="col-sm-3">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="account_datetime" name="data[coop_account][account_datetime]" class="form-control m-b-1 type_input" type="text"
                                            value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" style="padding-left:38px;">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียดรายการบัญชี</label>
                            <div class="col-sm-6">
                                <input id="account_description" name="data[coop_account][account_description]" class="form-control m-b-1 type_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row" id="user_tran_row" style="display:none;">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ผู้ทำรายการ</label>
                            <label class="col-sm-6 control-label text-left" id="user_tran"></label>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="button" id="btn_debit" class="btn btn-primary min-width-100 btn-width-auto" onclick="add_account_detail('debit')">เพิ่มรายการเดบิต</button>
                        <button type="button" id="btn_credit" class="btn btn-primary min-width-100 btn-width-auto" onclick="add_account_detail('credit')">เพิ่มรายการเครดิต</button>
                    </div>
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-striped" id="account_data_cash_table">
                            <thead>
                                <tr>
                                    <th class="font-normal" width="30%"> รหัสบัญชี </th>
                                    <th class="font-normal" width="40%"> รายละเอียด </th>
                                    <th class="font-normal" width="15%"> เดบิต </th>
                                    <th class="font-normal" width="15%"> เครดิต </th>
                                    <th style="width:150px"></th>
                                </tr>
                            </thead>
                            <tbody id="account_data">
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="col-sm-3 control-label">รวมเดบิต</label>
                                <div class="col-sm-6">
                                    <input id="sum_debit" name="sum_debit" class="form-control m-b-1 type_input" type="text" value="" readonly>
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="col-sm-3 control-label">รวมเครดิต</label>
                                <div class="col-sm-6">
                                    <input id="sum_credit" name="sum_credit" class="form-control m-b-1 type_input" type="text" value="" readonly>
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="col-sm-3 control-label">ผลต่าง</label>
                                <div class="col-sm-6">
                                    <input id="sum_diff" name="sum_diff" class="form-control m-b-1 type_input" type="text" value="" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" onclick="form_submit()">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" onclick="clear_modal()">ยกเลิก</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<div id="add_account_cash" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account" style="width: 100%; height: 100%; margin: 0; padding: 0;">
        <div class="modal-content" style="width: 100%; height: 100%; margin: 0; padding: 0;">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">บันทึกรายการบัญชี</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(PROJECTPATH.'/account/account_save'); ?>" method="post" id="form1_cash">
                    <input id="input_number_cash" type="hidden" value="0">
                    <input id="account_id_cash" name="account_id" type="hidden" value="">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label right"> การชำระเงิน</label>
                            <div class="col-sm-3">
                                <span id="show_pay_type2" style="">
                                    <input type="radio" name="journal_type" id="pay_type_0" value="RV" checked> ด้านรับ &nbsp;&nbsp;
                                    <input type="radio" name="journal_type" id="pay_type_1" value="PV"> ด้านจ่าย &nbsp;&nbsp;
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">วันที่</label>
                            <div class="col-sm-3">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="account_datetime_cash" name="data[coop_account][account_datetime]" class="form-control m-b-1 type_input form_date_picker" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" style="padding-left:38px;">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียดรายการบัญชี</label>
                            <div class="col-sm-6">
                                <input id="account_description_cash" name="data[coop_account][account_description]" class="form-control m-b-1 type_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row" id="user_cash_row" style="display:none;">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ผู้ทำรายการ</label>
                            <label class="col-sm-6 control-label text-left" id="user_cash"></label>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="button" id="btn-add-account-detail" class="btn btn-primary min-width-100 btn-width-auto">เพิ่มรายการ</button>
                    </div>
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-striped" id="account_data_table">
                            <thead>
                                <tr>
                                    <th class="font-normal" style="width:40%"> รหัสบัญชี </th>
                                    <th class="font-normal" style="width:40%">รายละเอียด</th>
                                    <th class="font-normal" style="width:15%">จำนวนเงิน</th>
                                    <th class="font-normal" style="width:150px"></th>
                                </tr>
                            </thead>
                            <tbody id="account_data_cash" index="0">
                                <tr id="tr_acc_0" data-index="0" class="org-tr">
                                    <td>
                                        <select id="account_chart_id_cash_0" name="data[coop_account_detail][0][account_chart_id]" class="form-control m-b-1 js-data-example-ajax select_chart">
                                            <option value="">เลือกรหัสผังบัญชี</option>
                                            <?php 
                                                foreach($account_chart as $key => $row) {
                                            ?>
                                            <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                        <input type="hidden" name="data[coop_account_detail][0][account_type]" value="<?php echo $type; ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control account_desc" id="acc_desc_0" name="data[coop_account_detail][0][account_description]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control acc_input" id="acc_0" name="data[coop_account_detail][0][account_amount]" onKeyUp="format_the_number_decimal(this)">
                                    </td>
                                    <td id="remove_0" class="" data-index="0"></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group col-sm-12">
                            <label class="col-sm-3 control-label">ยอดรวม</label>
                            <div class="col-sm-6">
                                <input id="sum_cash" name="sum_cash" class="form-control m-b-1 type_input" type="text" value="0" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" onclick="form_cash_submit()">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" onclick="clear_modal()">ยกเลิก</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<div id="add_account_sp" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account" style="width: 100%; height: 100%; margin: 0; padding: 0;">
        <div class="modal-content" style="width: 100%; height: 100%; margin: 0; padding: 0;">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">บันทึกรายการบัญชี</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(PROJECTPATH.'/account/account_save'); ?>" method="post" id="form_sp">
                    <input id="input_number_sp" type="hidden" value="0">
                    <input id="account_id_sp" name="account_id" type="hidden" value="">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label right"> การชำระเงิน</label>
                            <div class="col-sm-3">
                                <span id="show_pay_type2" style="">
                                    <input type="radio" name="journal_type" id="pay_type_sp_0" value="RV"> ด้านรับ &nbsp;&nbsp;
                                    <input type="radio" name="journal_type" id="pay_type_sp_1" value="PV"> ด้านจ่าย &nbsp;&nbsp;
                                    <input type="radio" name="journal_type" id="pay_type_sp_2" value="JV" checked> ด้านโอน &nbsp;&nbsp;
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">วันที่</label>
                            <div class="col-sm-3">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="account_datetime_sp" name="data[coop_account][account_datetime]" class="form-control m-b-1 type_input" type="text"
                                            value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" style="padding-left:38px;">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียดรายการบัญชี</label>
                            <div class="col-sm-6">
                                <input id="account_description_sp" name="data[coop_account][account_description]" class="form-control m-b-1 type_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row" id="user_sp_row" style="display:none;">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">ผู้ทำรายการ</label>
                            <label class="col-sm-6 control-label text-left" id="user_sp"></label>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="button" id="btn_debit_sp" class="btn btn-primary min-width-100 btn-width-auto" onclick="add_account_detail_sp('debit'); $(this).blur();">เพิ่มรายการเดบิต</button>
                        <button type="button" id="btn_credit_sp" class="btn btn-primary min-width-100 btn-width-auto" onclick="add_account_detail_sp('credit'); $(this).blur();">เพิ่มรายการเครดิต</button>
                    </div>
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-striped" id="account_data_sp_table">
                            <thead>
                                <tr>
                                    <th class="font-normal" width="30%"> รหัสบัญชี </th>
                                    <th class="font-normal" width="40%"> รายละเอียด </th>
                                    <th class="font-normal" width="15%"> เดบิต </th>
                                    <th class="font-normal" width="15%"> เครดิต </th>
                                    <th style="width:150px"></th>
                                </tr>
                            </thead>
                            <tbody id="account_data_sp">
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="col-sm-3 control-label">รวมเดบิต</label>
                                <div class="col-sm-6">
                                    <input id="sum_debit_sp" name="sum_debit" class="form-control m-b-1 type_input" type="text" value="" readonly>
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="col-sm-3 control-label">รวมเครดิต</label>
                                <div class="col-sm-6">
                                    <input id="sum_credit_sp" name="sum_credit" class="form-control m-b-1 type_input" type="text" value="" readonly>
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="col-sm-3 control-label">ผลต่าง</label>
                                <div class="col-sm-6">
                                    <input id="sum_diff_sp" name="sum_diff" class="form-control m-b-1 type_input" type="text" value="" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" onclick="form_submit_sp()">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" onclick="clear_modal()">ยกเลิก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<form action="<?php echo base_url(PROJECTPATH.'/account/cancel_account_transaction'); ?>" method="post" id="form1_cancel">
    <input id="cancel_account_id" name="account_id" value=""/>
</form>

<!-- pagination from -->
<form action="<?php echo base_url(PROJECTPATH.'/account'); ?>" method="post" id="form_pagination">
    <input id="hidden_from_date" type="hidden" name="from_date" value=""/>
    <input id="hidden_thru_date" type="hidden" name="thru_date" value=""/>
    <input id="hidden_journal_ref" type="hidden" name="journal_ref" value=""/>
    <input id="hidden_page" type="hidden" name="page" value=""/>
</form>

<!-- for close budget year -->
<form action="<?php echo base_url(PROJECTPATH.'/account/update_account_year_status'); ?>" method="post" id="form_close_budget_year">
    <input id="budget_year_for_close" name="year" type="hidden" value="<?php echo $first_open_budget_year;?>"/>
    <input name="status" value="1" type="hidden"/>
</form>
<form action="<?php echo base_url(PROJECTPATH.'/account/update_account_year_status'); ?>" method="post" id="form_open_budget_year">
    <input id="budget_year_for_open" name="year" type="hidden" value="<?php echo $last_close_budget_year;?>"/>
    <input name="status" value="0" type="hidden"/>
</form>

<input id="confirm_sp_action" type="hidden" value="">
<div class="modal fade" id="confirm_sp_req" role="dialog">
    <div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">ยืนยันตัวตน</h4>
			</div>
			<div class="modal-body">
				<p>ชื่อผู้ใช้งาน</p>
				<input type="text" class="form-control" id="confirm_user">
				<p>รหัสผ่าน</p>
				<input type="password" class="form-control" id="confirm_pwd">
				<br>
				<input type="hidden" id="transaction_id_err">
				<div class="row">
					<div class="col-sm-12 text-center">
						<button class="btn btn-info" id="submit_auth_confirm">ตกลง</button>
					</div>
				</div>
			</div>
			<div class="modal-footer">
			</div>
		</div>
    </div>
</div>

<div id="account_history_modal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">บันทึกรายการบัญชี</h2>
            </div>
            <div class="modal-body" id="account_history_body">
            </div>
            <div class="modal-body text-center">
                <button class="btn btn-danger min-width-100" type="button" id="account_history_modal_close">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- enable_edit_delete_permission -->
<input type="hidden" id="enable_edit_delete_permission" value="<?php echo $enable_edit_delete_permission?>">

<!-- for hotkey -->
<input type="hidden" id="modal_check" value="0">
<input type="hidden" id="nature_check" value="debit">

<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/account.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);

$link = array(
    'src' => PROJECTJSPATH.'assets/js/zepto.min.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
$link = array(
    'src' => PROJECTJSPATH.'assets/js/jquery.mask.js',
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
        //Pagination
        $(".pagination_a").click(function() {
            $("#hidden_from_date").val($(this).attr('data_from_date'));
            $("#hidden_thru_date").val($(this).attr('data_thru_date'));
            $("#hidden_journal_ref").val($(this).attr('data_journal_ref'));
            if($(this).attr('data_journal_type')) {
                journal_types = JSON.parse($(this).attr('data_journal_type'));
                console.log(journal_types.length)
                for (i=0; i < journal_types.length; i++) {
                    journal_type = journal_types[i];
                    $("#form_pagination").append(`<input id="hidden_journal_type`+i+`" type="hidden" name="journal_type[]" value="`+journal_type+`"/>`);
                }
            }
            $("#hidden_page").val($(this).attr('data-pagenumber'));
            $("#form_pagination").submit();
        });

        $("#btn-add-account-detail").click(function() {
            index = parseInt($("#account_data_cash").attr("index")) + 1;
            html = `<tr id="tr_acc_`+index+`" data-index="`+index+`" class="add-tr">
                        <td>
                            <select id="account_chart_id_cash_`+index+`" class="form-control js-data-example-ajax select_chart" name="data[coop_account_detail][`+index+`][account_chart_id]">
                                <option value="">เลือกรหัสผังบัญชี</option>
                                <?php 
                                    foreach($account_chart as $key => $row) {
                                ?>
                                    <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                <?php
                                    }
                                ?>
                            </select>
                            <input type="hidden" name="data[coop_account_detail][`+index+`][account_type]" value="<?php echo $type; ?>">
                        </td>
                        <td><input type="text" class="form-control account_desc" id="acc_desc_`+index+`" name="data[coop_account_detail][`+index+`][account_description]"></td>
                        <td><input type="text" class="form-control acc_input" id="acc_`+index+`" name="data[coop_account_detail][`+index+`][account_amount]" onKeyUp="format_the_number_decimal(this)"></td>
                        <td id="remove_`+index+`" class="remove-cash-tr" data-index="`+index+`"><a href="#">ลบ</a></td>
                    </tr>`;
            $("#account_data_cash").append(html);
            $("#account_data_cash").attr("index", index);
            createSelect2("add_account_cash");
        });
        $(document).on("click",".remove-cash-tr",function() {
            index = $(this).attr("data-index");
            $("#tr_acc_"+index).remove();
        });
        $(".edit-acc-btn").click(function() {
            account_id = $(this).attr("data-account-id");
            $.get(base_url+"account/get_account_detail?account_id="+account_id
			, function(result) {
                data = JSON.parse(result);
                if(data.journal_type == "JV" || data.journal_type == "J" || data.journal_type == "SV" || data.journal_type == "S" || data.journal_type == "AV" || data.journal_type == "A") {
                    $("#account_id_tran").val(data.account_id);
                    $("#account_datetime").val(data.account_datetime_be);
                    $("#account_description").val(data.account_description);
                    $("#journal_type_tran").val(data.journal_type);
                    $(".add-tr").remove();
                    $("#input_number").val(0);
                    if(data.user_name) {
                        $("#user_tran_row").css("display","");
                        $("#user_tran").html(data.user_name);
                    }

                    $.ajaxSetup({async: false});
                    for (i = 0; i < data.details.length; i++) {
                        detail = data.details[i];
                        input_number = add_account_detail(detail.account_type);
                        $("#sel_input_"+input_number).val(detail.account_chart_id);
                        $("#desc_input_"+input_number).val(detail.description);
                        if(detail.account_type == "debit") {
                            $("#debit_input"+input_number).val(detail.account_amount);
                            format_the_number_decimal(document.getElementById("debit_input"+input_number));
                        } else if (detail.account_type == "credit") {
                            $("#credit_input"+input_number).val(detail.account_amount);
                            format_the_number_decimal(document.getElementById("credit_input"+input_number));
                        }
                    }

                    if(data.journal_type == "JV" || data.journal_type == "J") {
                        $("#modal_check").val(2)
                    } else {
                        $("#modal_check").val(2)
                    }

                    call_sum_credit_debit(0,0);
                    createSelect2("add_account_tran");
                    call_sum_credit_debit(null,null)
                    $("#add_account_tran").modal("show");
                } else if(data.is_compound == 1) {
                    $("#account_id_sp").val(data.account_id);
                    $("#account_datetime_sp").val(data.account_datetime_be);
                    $("#account_description_sp").val(data.account_description);
                    $("#journal_type_tran").val(data.journal_type);
                    if(data.journal_type == "RV" || data.journal_type == "R") {
                        $("#pay_type_sp_0").prop("checked", true);
                    } else if (data.journal_type == "PV" || data.journal_type == "P") {
                        $("#pay_type_sp_1").prop("checked", true);
                    } else {
                        $("#pay_type_sp_2").prop("checked", true);
                    }
                    $(".add-tr").remove();
                    $("#input_number_sp").val(0);
                    if(data.user_name) {
                        $("#user_sp_row").css("display","");
                        $("#user_sp").html(data.user_name);
                    }

                    $.ajaxSetup({async: false});
                    for (i = 0; i < data.details.length; i++) {
                        detail = data.details[i];
                        input_number = add_account_detail_sp(detail.account_type);
                        $("#sel_input_"+input_number).val(detail.account_chart_id);
                        $("#desc_input_"+input_number).val(detail.description);
                        if(detail.account_type == "debit") {
                            $("#debit_input"+input_number).val(detail.account_amount);
                            format_the_number_decimal(document.getElementById("debit_input"+input_number));
                        } else if (detail.account_type == "credit") {
                            $("#credit_input"+input_number).val(detail.account_amount);
                            format_the_number_decimal(document.getElementById("credit_input"+input_number));
                        }
                    }

                    call_sum_credit_debit(0,0);
                    createSelect2("add_account_sp");
                    $("#modal_check").val(4)
                    $("#add_account_sp").modal("show");
                } else {
                    if(data.journal_type == "PV") {
                        $("#pay_type_1").prop("checked", true);
                    } else {
                        $("#pay_type_0").prop("checked", true);
                    }

                    $("#account_id_cash").val(data.account_id);
                    $("#account_datetime_cash").val(data.account_datetime_be);
                    $("#account_description_cash").val(data.account_description);
                    if(data.user_name) {
                        $("#user_cash_row").css("display","");
                        $("#user_cash").html(data.user_name);
                    }
                    $(".add-tr").remove();

                    $("#account_data_cash").attr("index", 0);

                    for (i = 0; i < data.details.length; i++) {
                        detail = data.details[i];
                        if((data.journal_type == "PV" && detail.account_type == "debit") || (data.journal_type == "RV" && detail.account_type == "credit")) {
                            index = 0;
                            if(i > 0) {
                                index = parseInt($("#account_data_cash").attr("index")) + 1;
                                html = `<tr id="tr_acc_`+index+`" data-index="`+index+`" class="add-tr">
                                            <td>
                                                <select id="account_chart_id_cash_`+index+`" class="form-control js-data-example-ajax select_chart" name="data[coop_account_detail][`+index+`][account_chart_id]">
                                                    <option value="">เลือกรหัสผังบัญชี</option>
                                                    <?php 
                                                        foreach($account_chart as $key => $row) {
                                                    ?>
                                                        <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                                <input type="hidden" name="data[coop_account_detail][`+index+`][account_type]" value="<?php echo $type; ?>">
                                            </td>
                                            <td><input type="text" class="form-control account_desc" id="acc_desc_`+index+`" name="data[coop_account_detail][`+index+`][account_description]"></td>
                                            <td><input type="text" class="form-control acc_input" id="acc_`+index+`" name="data[coop_account_detail][`+index+`][account_amount]" onKeyUp="format_the_number_decimal(this)"></td>
                                            <td id="remove_`+index+`" class="remove-cash-tr" data-index="`+index+`"><a href="#">ลบ</a></td>
                                        </tr>`;
                                $("#account_data_cash").append(html);
                                $("#account_data_cash").attr("index", index);   
                            }
                            $("#account_chart_id_cash_"+index).val(detail.account_chart_id);
                            $("#acc_desc_"+index).val(detail.description);
                            $("#acc_"+index).val(detail.account_amount);
                            format_the_number_decimal(document.getElementById("acc_"+index));
                            createSelect2("add_account_cash");
                        }
                    }
                    cal_acc_input();
                    $("#modal_check").val(1)
                    call_sum_credit_debit(null,null)
                    $("#add_account_cash").modal("show");
                }
			});
        });
        $(".cancel-acc-btn").click(function() {
            account_id = $(this).attr("data-account-id");
            swal({
                title: "ท่านต้องการลบข้อมูลใช่หรือไม่?",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    if($("#enable_edit_delete_permission").val() == 1) {
                        $("#cancel_account_id").val(account_id);
                        $("#confirm_sp_action").val("cancel");
                        $("#confirm_sp_req").modal("show");
                    } else {
                        $("#cancel_account_id").val(account_id);
                        $("#form1_cancel").submit();
                    }
                } else {
                }
            });
        });

        $("#search_chk_all").change(function() {
            $('.search_journal_type').not(this).prop('checked', $('#search_chk_all').is(":checked"));
        });

        $(".search_journal_type").change(function() {
            if(!$(this).is(":checked")) {
                $('#search_chk_all').not(this).prop('checked', false);
            }
        });

        $("#search_btn").click(function() {
            $("#search_form").submit();
        });

        $(".btn_his").click(function() {
            account_id = $(this).attr('data-id');
            $("#account_history_body").html("");
            $.post(base_url+"account/json_get_account_history",
            {account_id: account_id},
            function(result){
                data = JSON.parse(result);
                console.log(data);
                html = `<table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="font-normal text-left" colspan="5">เลขที่ใบสำคัญ `+data.journal_ref+`</th>
                                </tr>
                            </thead>
                        </table>`;
                $("#account_history_body").append(html);
                for(i=0; i < data.accounts.length; i++) {
                    account = data.accounts[i];
                    console.log(account)
                    html = `<table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="font-normal" width="20%">วันที่</th>
                                        <th class="font-normal"> รายการ </th>
                                        <th class="font-normal" width="15%"> รหัสบัญชี </th>
                                        <th class="font-normal" width="15%"> เดบิต </th>
                                        <th class="font-normal" width="15%"> เครดิต </th>
                                    </tr>
                                    <tr>
                                        <th class="font-normal" width="20%"></th>
                                        <th class="font-normal">รายละเอียด</th>
                                        <th class="font-normal" width="15%">สถานะ</th>
                                        <th class="font-normal" width="15%"></th>
                                        <th class="font-normal" width="15%"></th>
                                    </tr>
                                </thead>
                                <tbody>`
                    for(j=0; j < account.details.length; j++) {
                        detail = account.details[j];
                        datetimethai = j==0 ? account.account_datetime_thai : "";
                        if(detail.account_type == "debit") {
                            html += `<tr>
                                        <td>`+datetimethai+`</td>
                                        <td width="35%" class="text_left">
                                            `+detail.account_chart+`
                                        </td>
                                        <td>`+detail.account_chart_id+`</td>
                                        <td class="text_right">`+numeral(detail.account_amount).format('0,0.00')+`</td>
                                        <td class="text_right"></td>
                                        <td class="text_right">
                                        </td>
                                    </tr>`;
                        } else {
                            html += `<tr>
                                        <td>`+datetimethai+`</td>
                                        <td width="35%" class="text_left">
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            `+detail.account_chart+`
                                        </td>
                                        <td>`+detail.account_chart_id+`</td>
                                        <td class="text_right"></td>
                                        <td class="text_right">`+numeral(detail.account_amount).format('0,0.00')+`</td>
                                        <td class="text_right">
                                        </td>
                                    </tr>`;
                        }
                    }

                    html += `       <tr>
                                        <td></td>
                                        <td class="text_left">`+account.account_description+`</td>
                                        <td class="text_right" colspan="4">
                                            ผู้ทำรายการ `+account.user_name+`&nbsp;&nbsp;
                                            วันที่ทำรายการ `+account.created_at_thai+`
                                        </td>
                                    </tr>
                                </tbody>
                            </table>`;
                    $("#account_history_body").append(html);
                }

                $("#account_history_modal").modal("show");
            });
        });

        $("#account_history_modal_close").click(function() {
            $("#account_history_modal").modal("hide");
        });

        $(document).on('select2:select', '.select_chart', function() {
            model = $("#modal_check").val();
            if(model == 1 && $('#add_account_cash').hasClass('in') && !$('#confirm_sp_req').hasClass('in')) {
                index = $(this).closest('tr').attr('data-index');
                $("#acc_desc_"+index).focus();
            } else if ((model == 2 || model == 3) && $('#add_account_tran').hasClass('in') && !$('#confirm_sp_req').hasClass('in')) {
                index = $(this).closest('tr').attr('data_index');
                $("#desc_input_"+index).focus();
            } else if (model == 4 && $('#add_account_sp').hasClass('in') && !$('#confirm_sp_req').hasClass('in')) {
                index = $(this).closest('tr').attr('data_index');
                $("#desc_input_"+index).focus();
            }
        });

        $(document).on('keydown', '.account_desc', function(e) {
            if(e.which == 13) {
                model = $("#modal_check").val();
                if(model == 1 && $('#add_account_cash').hasClass('in') && !$('#confirm_sp_req').hasClass('in')) {
                    index = $(this).closest('tr').attr('data-index');
                    $("#acc_"+index).focus();
                } else if ((model == 2 || model == 3) && $('#add_account_tran').hasClass('in') && !$('#confirm_sp_req').hasClass('in')) {
                    index = $(this).closest('tr').attr('data_index');
                    $("#debit_input"+index).focus();
                    $("#credit_input"+index).focus();
                } else if (model == 4 && $('#add_account_sp').hasClass('in') && !$('#confirm_sp_req').hasClass('in')) {
                    index = $(this).closest('tr').attr('data_index');
                    $("#debit_input"+index).focus();
                    $("#credit_input"+index).focus();
                }
            }
        });

        $(document).on('keydown', '.acc_input, .credit_input, .debit_input', function(e) {
            if(e.which == 13) {
                model = $("#modal_check").val();
                if(model == 1 && $('#add_account_cash').hasClass('in') && !$('#confirm_sp_req').hasClass('in')) {
                    $("#btn-add-account-detail").trigger("click");
                    index = $("#account_data_cash").attr('index');
                    $("#account_chart_id_cash_"+index).focus();
                } else if ((model == 2 || model == 3) && $('#add_account_tran').hasClass('in') && !$('#confirm_sp_req').hasClass('in')) {
                    index = add_account_detail($("#nature_check").val());
                    $("#sel_input_"+index).focus();
                } else if (model == 4 && $('#add_account_sp').hasClass('in') && !$('#confirm_sp_req').hasClass('in')) {
                    index = add_account_detail_sp($("#nature_check").val());
                    $("#sel_input_"+index).focus();
                }
            }
        });

        $(document).on('keydown', function( e ) {
            if (e.which == 9) {
                e.preventDefault();
                if($(':focus').attr('id') == 'account_datetime' || $(':focus').attr('id') == 'form_date_picker') {
                    $(':focus').datepicker("hide");
                }
                model = $("#modal_check").val();
                if (model == 2 || model == 3) {
                    if($("#account_data tr").last() && $('#add_account_tran').hasClass('in') && !$('#confirm_sp_req').hasClass('in')) {
                        var index = $("#account_data tr").last().attr('data_index');
                        var type = $("#account_data tr").last().attr('data_type');
                        var chart_id = $("#sel_input_"+index).val();
                        var desc = $("#desc_input_"+index).val();
                        var amount = $("#"+type+"_input"+index).val();

                        var switchType = 'credit';
                        if(type == 'credit') {
                            switchType = 'debit';
                        }
                        switchIndex = add_account_detail(switchType);

                        $("#list_"+index).remove();
                        if(!amount) {
                            var debit_input_now = 0;
                            var credit_input_now = 0
                            $(".debit_input").each(function() {
                                if(parseFloat(removeCommas($(this).val())) == NaN || $(this).val() == ''){
                                }else{
                                    debit_input_now += parseFloat(removeCommas($(this).val()));
                                }
                            });
                            $(".credit_input").each(function() {
                                if(parseFloat(removeCommas($(this).val())) == NaN || $(this).val() == ''){
                                }else{
                                    credit_input_now += parseFloat(removeCommas($(this).val()));
                                }
                            });
                            if(switchType == 'debit') {
                                amount = credit_input_now - debit_input_now;
                            } else {
                                amount = debit_input_now - credit_input_now;
                            }
                        }

                        if(chart_id) {
                            $("#sel_input_"+switchIndex).val(chart_id);
                            $("#sel_input_"+switchIndex).trigger('change');
                        }
                        if(desc) $("#desc_input_"+switchIndex).val(desc);
                        if(amount) $("#"+switchType+"_input"+switchIndex).val(amount);
                        $("#"+switchType+"_input"+switchIndex).focus();
                        call_sum_credit_debit(null,null)

                        $("#debit_input"+switchIndex).focus();
                        $("#credit_input"+switchIndex).focus();
                    }
                } else if (model == 4) {
                    if($("#account_data_sp tr").last() && $('#add_account_sp').hasClass('in') && !$('#confirm_sp_req').hasClass('in')) {
                        var index = $("#account_data_sp tr").last().attr('data_index');
                        var type = $("#account_data_sp tr").last().attr('data_type');
                        var chart_id = $("#sel_input_"+index).val();
                        var desc = $("#desc_input_"+index).val();
                        var amount = $("#"+type+"_input"+index).val();

                        var switchType = 'credit';
                        if(type == 'credit') {
                            switchType = 'debit';
                        }
                        switchIndex = add_account_detail_sp(switchType);

                        $("#list_"+index).remove();
                        if(!amount) {
                            var debit_input_now = 0;
                            var credit_input_now = 0
                            $(".debit_input").each(function() {
                                if(parseFloat(removeCommas($(this).val())) == NaN || $(this).val() == ''){
                                }else{
                                    debit_input_now += parseFloat(removeCommas($(this).val()));
                                }
                            });
                            $(".credit_input").each(function() {
                                if(parseFloat(removeCommas($(this).val())) == NaN || $(this).val() == ''){
                                }else{
                                    credit_input_now += parseFloat(removeCommas($(this).val()));
                                }
                            });
                            if(switchType == 'debit') {
                                amount = credit_input_now - debit_input_now;
                            } else {
                                amount = debit_input_now - credit_input_now;
                            }
                        }

                        if(chart_id) {
                            $("#sel_input_"+switchIndex).val(chart_id);
                            $("#sel_input_"+switchIndex).trigger('change');
                        }
                        if(desc) $("#desc_input_"+switchIndex).val(desc);
                        if(amount) $("#"+switchType+"_input"+switchIndex).val(amount);
                        $("#"+switchType+"_input"+switchIndex).focus();
                        call_sum_credit_debit(null,null)

                        $("#debit_input"+switchIndex).focus();
                        $("#credit_input"+switchIndex).focus();
                    }
                }
            }
        });
    });

    function add_account_detail(type){
        $("#nature_check").val(type);
        var void_input = 0;
        var debit_input = 0;
        var credit_input = 0;
        $('.account_detail').each(function(){
            if($(this).val()==''){
                void_input++;
            }
        });
        $('.debit_input').each(function(){
            debit_input = parseFloat(debit_input) + parseFloat(removeCommas($(this).val()));
        });
        $('.credit_input').each(function(){
            credit_input = parseFloat(credit_input) + parseFloat(removeCommas($(this).val()));
        });
        var input_number = $('#input_number').val();
        html = type == 'debit'
            ? `<tr id="list_`+input_number+`" class="add-tr" data_index="`+input_number+`" data_type="debit">
                    <td>
                        <select id="sel_input_`+input_number+`" class="form-control js-data-example-ajax select_chart" name="data[coop_account_detail][`+input_number+`][account_chart_id]">
                            <option value="">เลือกรหัสผังบัญชี</option>
                            <?php
                                foreach($account_chart as $key => $row) {
                            ?>
                                <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                            <?php
                                }
                            ?>
                        </select>
                        <input type="hidden" name="data[coop_account_detail][`+input_number+`][account_type]" id="input_type_`+input_number+`" value="debit">
                    </td>
                    <td>
                        <input type="text" class="form-control account_desc" id="desc_input_`+input_number+`" name="data[coop_account_detail][`+input_number+`][account_description]">
                    </td>
                    <td>
                        <input type="text" class="form-control account_detail debit_input" id="debit_input`+input_number+`" name="data[coop_account_detail][`+input_number+`][account_amount]" onkeyup="format_the_number_decimal(this)" onchange="call_sum_credit_debit(this.value,'credit');$('.countn').val(this.value);">
                    </td>
                    <td></td>
                    <td onclick=" $('#list_`+input_number+`').remove();call_sum_credit_debit(this.value,null);$('.countn').val(this.value);"><a href="#">ลบ</a></td>
                    <input class="countn" type="hidden" id="countnum`+input_number+`" name="countnum" value="`+input_number+`">
                </tr>`
            : `<tr id="list_`+input_number+`" class="add-tr" data_index="`+input_number+`" data_type="credit">
                    <td>
                        <select id="sel_input_`+input_number+`" class="form-control js-data-example-ajax select_chart" name="data[coop_account_detail][`+input_number+`][account_chart_id]">
                            <option value="">เลือกรหัสผังบัญชี</option>
                            <?php
                                foreach($account_chart as $key => $row) {
                            ?>
                                <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                            <?php
                                }
                            ?>
                        </select>
                        <input type="hidden" name="data[coop_account_detail][`+input_number+`][account_type]" id="input_type_`+input_number+`" value="credit">
                    </td>
                    <td>
                        <input type="text" class="form-control account_desc" id="desc_input_`+input_number+`" name="data[coop_account_detail][`+input_number+`][account_description]">
                    </td>
                    <td></td>
                    <td>
                        <input type="text" class="form-control account_detail credit_input" id="credit_input`+input_number+`" name="data[coop_account_detail][`+input_number+`][account_amount]" onkeyup="format_the_number_decimal(this)" onchange="call_sum_credit_debit(this.value,'credit');$('.countn').val(this.value);">
                    </td>
                    <td onclick=" $('#list_`+input_number+`').remove();call_sum_credit_debit(this.value,null);$('.countn').val(this.value);"><a href="#">ลบ</a></td>
                    <input class="countn" type="hidden" id="countnum`+input_number+`" name="countnum" value="`+input_number+`">
                </tr>`

        $('#account_data').append(html);
        input_number++;
        $('#input_number').val(input_number);
        createSelect2("add_account_tran");
        return (input_number - 1);
    }

    function add_account_detail_sp(type){
        $("#nature_check").val(type);
        var void_input = 0;
        var debit_input = 0;
        var credit_input = 0;
        $('.account_detail_sp').each(function(){
            if($(this).val()==''){
                void_input++;
            }
        });
        $('.debit_input').each(function(){
            debit_input = parseFloat(debit_input) + parseFloat(removeCommas($(this).val()));
        });
        $('.credit_input').each(function(){
            credit_input = parseFloat(credit_input) + parseFloat(removeCommas($(this).val()));
        });
        var input_number = $('#input_number_sp').val();
        html = type == 'debit'
            ? `<tr id="list_`+input_number+`" class="add-tr" data_index="`+input_number+`" data_type="debit">
                    <td>
                        <select id="sel_input_`+input_number+`" class="form-control js-data-example-ajax select_chart" name="data[coop_account_detail][`+input_number+`][account_chart_id]">
                            <option value="">เลือกรหัสผังบัญชี</option>
                            <?php
                                foreach($account_chart as $key => $row) {
                            ?>
                                <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                            <?php
                                }
                            ?>
                        </select>
                        <input type="hidden" name="data[coop_account_detail][`+input_number+`][account_type]" id="input_type_`+input_number+`" value="debit">
                    </td>
                    <td>
                        <input type="text" class="form-control account_desc" id="desc_input_`+input_number+`" name="data[coop_account_detail][`+input_number+`][account_description]">
                    </td>
                    <td>
                        <input type="text" class="form-control account_detail debit_input" id="debit_input`+input_number+`" name="data[coop_account_detail][`+input_number+`][account_amount]" onkeyup="format_the_number_decimal(this)" onchange="call_sum_credit_debit(this.value,'credit');$('.countn').val(this.value);">
                    </td>
                    <td></td>
                    <td onclick=" $('#list_`+input_number+`').remove();call_sum_credit_debit(this.value,null);$('.countn').val(this.value);"><a href="#">ลบ</a></td>
                    <input class="countn" type="hidden" id="countnum`+input_number+`" name="countnum" value="`+input_number+`">
                </tr>`
            : `<tr id="list_`+input_number+`" class="add-tr" data_index="`+input_number+`" data_type="credit">
                    <td>
                        <select id="sel_input_`+input_number+`" class="form-control js-data-example-ajax select_chart" name="data[coop_account_detail][`+input_number+`][account_chart_id]">
                            <option value="">เลือกรหัสผังบัญชี</option>
                            <?php
                                foreach($account_chart as $key => $row) {
                            ?>
                                <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
                            <?php
                                }
                            ?>
                        </select>
                        <input type="hidden" name="data[coop_account_detail][`+input_number+`][account_type]" id="input_type_`+input_number+`" value="credit">
                    </td>
                    <td>
                        <input type="text" class="form-control account_desc" id="desc_input_`+input_number+`" name="data[coop_account_detail][`+input_number+`][account_description]">
                    </td>
                    <td></td>
                    <td>
                        <input type="text" class="form-control account_detail credit_input" id="credit_input`+input_number+`" name="data[coop_account_detail][`+input_number+`][account_amount]" onkeyup="format_the_number_decimal(this)" onchange="call_sum_credit_debit(this.value,'credit');$('.countn').val(this.value);">
                    </td>
                    <td onclick=" $('#list_`+input_number+`').remove();call_sum_credit_debit(this.value,null);$('.countn').val(this.value);"><a href="#">ลบ</a></td>
                    <input class="countn" type="hidden" id="countnum`+input_number+`" name="countnum" value="`+input_number+`">
                </tr>`

        $('#account_data_sp').append(html);
        input_number++;
        $('#input_number_sp').val(input_number);
        createSelect2("add_account_sp");
        return (input_number - 1);
    }

    function open_daily() {
        swal({
            title: "ยืนยันการยกเลิกการปิดบัญชีรายวัน ของวันที่ <?php echo $this->center_function->ConvertToThaiDate($account_date,'1','0');?>",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                if($("#enable_edit_delete_permission").val() == 1) {
                    $("#confirm_sp_action").val("open_daily");
                    $("#confirm_sp_req").modal("show");
                } else {
                    run_open_daily()
                }
            }
        });
    }

    function run_open_daily() {
        $.ajax({
            url:base_url+"account/open_daily",
            method:"POST",
            data: {'date':'<?php echo $account_date;?>'},
            dataType:"text",
            success:function(data){
                if(data == 'success'){
                    window.open('<?php echo base_url(PROJECTPATH.'/account'); ?><?php echo !empty($_GET['page']) ? '?page='.$_GET['page'] : '';?>', '_self');
                }
            }
        });
    }

    function close_daily() {
        swal({
            title: "ยืนยันการปิดบัญชีรายวัน ของวันที่ <?php echo $this->center_function->ConvertToThaiDate($account_date,'1','0');?>",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                if($("#enable_edit_delete_permission").val() == 1) {
                    $("#confirm_sp_action").val("close_daily");
                    $("#confirm_sp_req").modal("show");
                } else {
                    run_close_daily()
                }
            }
        });
    }

    function run_close_daily() {
        $.ajax({
            url:base_url+"account/close_daily",
            method:"POST",
            data: {'date':'<?php echo $account_date;?>'},
            dataType:"text",
            success:function(data){
                if(data == 'success'){
                    window.open('<?php echo base_url(PROJECTPATH.'/account'); ?><?php echo !empty($_GET['page']) ? '?page='.$_GET['page'] : '';?>', '_self');
                }
            }
        });
    }
</script>
