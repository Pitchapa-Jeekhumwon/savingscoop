<div class="layout-content">
    <div class="layout-content-body">
        <style>
            .form-group {
                margin-bottom: 1em;
            }

            .border1 {
                border: solid 1px #ccc;
                padding: 0 15px;
            }

            .mem_pic {
                margin-top: -1em;
                float: right;
                width: 150px;
            }

            .mem_pic img {
                width: 100%;
                border: solid 1px #ccc;
            }

            .mem_pic button {
                display: block;
                width: 100%;
            }

            .modal-backdrop.in {
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

            .font-normal {
                font-weight: normal;
            }

            .font-normal2 {
                font-weight: bold;
                font-size: 20px;
            }

            .font-normal3 {
                font-weight: bold;
                font-size: 16px;
            }

            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            .btn_deposit {
                margin-right: 5px;
            }

            .alert-success {
                background-color: #DBF6D3;
                border-color: #AED4A5;
                color: #569745;
                font-size: 14px;
            }

            .alert-danger {
                background-color: #F2DEDE;
                border-color: #e0b1b8;
                color: #B94A48;
            }

            .alert {
                border-radius: 0;
                -webkit-border-radius: 0;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.11);
                display: table;
                width: 100%;
            }

            .modal-header-deposit {
                padding: 9px 15px;
                border: 1px solid #ff7534;
                background-color: #ff7534;
                color: #fff;
                -webkit-border-top-left-radius: 5px;
                -webkit-border-top-right-radius: 5px;
                -moz-border-radius-topleft: 5px;
                -moz-border-radius-topright: 5px;
                border-top-left-radius: 5px;
                border-top-right-radius: 5px;
            }

            .modal-header-withdrawal {
                padding: 9px 15px;
                border: 1px solid #d50000;
                background-color: #d50000;
                color: #fff;
                -webkit-border-top-left-radius: 5px;
                -webkit-border-top-right-radius: 5px;
                -moz-border-radius-topleft: 5px;
                -moz-border-radius-topright: 5px;
                border-top-left-radius: 5px;
                border-top-right-radius: 5px;
            }

            .modal-dialog-account {
                margin: 0 auto;
                margin-top: 10%;
            }

            .modal-dialog-print {
                margin: 0 auto;
                margin-top: 15%;
                width: 350px;
            }

            .center {
                text-align: center;
            }

            th, td {
                text-align: center;
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

            .bg-table {
                background-color: #ff7534;
                border-color: #ff7534;
                color: #fff;
            }

            .modal-dialog-delete {
                margin: 0 auto;
                width: 350px;
                margin-top: 8%;
            }

            .modal-dialog-add {
                margin: 0 auto;
                width: 60%;
                margin-top: 5%;
            }

            #add_account {
                z-index: 5100 !important;
            }

            #search_member_add_modal {
                z-index: 5200 !important;
            }

            @media (min-width: 768px) {
                .modal-dialog {
                    width: 700px;
                }
            }
        </style>
        <h1 style="margin-bottom: 0">จัดการบัญชีเงินฝาก ATM </h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 padding-l-r-0">
                <a class="link-line-none" onclick="add_account('','')">
                    <button class="btn btn-primary btn-lg bt-add" type="button">
                        <span class="icon icon-plus-circle"></span>
                        เปิดบัญชีเงินฝาก ATM
                    </button>
                </a>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">

                    <div class="row">
                        <div class="col-sm-8">
                            <!--<div class="input-with-icon">
                            <input class="form-control input-thick pill m-b-2" type="text" placeholder="ค้นหา" name="search_text" id="search_text">
                            <span class="icon icon-search input-icon"></span>
                            </div>
                            -->
                            <label class="col-sm-2 control-label">รูปแบบค้นหา</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select id="search_list" name="search_list" class="form-control m-b-1">
                                        <option value="">เลือกรูปแบบค้นหา</option>
                                        <option value="member_id">รหัสสมาชิก</option>
                                        <option value="id_card">หมายเลขบัตรประชาชน</option>
                                        <option value="firstname_th">ชื่อสมาชิก</option>
                                        <option value="lastname_th">นามสกุล</option>
                                        <option value="account_id">หมายเลขบัญชี</option>
                                    </select>
                                </div>
                            </div>

                            <label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input id="search_text" name="search_text" class="form-control m-b-1"
                                               type="text" value="<?php echo @$data['id_card']; ?>">
                                        <span class="input-group-btn">
											<button type="button" onclick="check_search();"
                                                    class="btn btn-info btn-search"><span
                                                    class="icon icon-search"></span></button>
										</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4 text-right">
                            <p>จำนวนบัญชีเงินฝากทั้งหมด <?php echo number_format($num_rows); ?> บัญชี</p>
                        </div>
                    </div>

                    <div class="bs-example" data-example-id="striped-table">
                        <div id="tb_wrap">
                            <table class="table table-bordered table-striped table-center">
                                <thead>
                                <tr class='bg-primary'>
                                    <th>ลำดับ</th>
                                    <th>ประเภทบัญชี</th>
                                    <th>เลขบัญชี</th>
                                    <th>ชื่อบัญชี</th>
                                    <th>รหัสสมาชิก</th>
                                    <th>ชื่อ - นามสกุล</th>
                                    <th>วันที่เปิดบัญชี</th>
                                    <th>สถานะ</th>
                                    <th>จัดการ</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $account_status = array('0' => 'ใช้งาน', '1' => 'ไม่ใช้งาน');
                                foreach ($data as $key => $row) { ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $row['type_code']; ?></td>
                                        <td>
                                            <a href="<?php echo base_url(PROJECTPATH . '/deposit_atm/atm_account?account_id=' . $row['account_id']); ?>">
                                                <!--												--><?php //echo substr($row['account_id'], 0, 3) . "-" . substr($row['account_id'], 3, 2) . "-" . substr($row['account_id'], 5, 5) . "-" . substr($row['account_id'], 10); ?>

                                                <?php echo $this->center_function->format_account_number($row['account_id'], '##-#####');?>
                                            </a></td>
                                        <td style="text-align:left"><?php echo $row['account_name']; ?></td>
                                        <td><?php echo $row['mem_id']; ?></td>
                                        <td style="text-align:left"><?php echo $row['member_name']; ?></td>
                                        <td><?php echo $this->center_function->ConvertToThaiDate($row['create_date']); ?></td>
                                        <td><?php echo $account_status[$row['account_status']]; ?></td>
                                        <td>
                                            <?php if ($row['account_status'] == '0') { ?>
                                                <a onclick="add_account('<?php echo @$row["account_id"]; ?>','<?php echo $row['mem_id']; ?>')"
                                                   style="cursor:pointer;"> แก้ไข </a> |
                                                <a class="text-del"
                                                   onclick="close_account('<?php echo @$row["account_id"]; ?>')">ปิดบัญชี</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="page_wrap">
                    <?php echo $paging ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="add_account" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-add">
        <div class="modal-content">
            <div class="modal-header modal-header-info">
                <h2 class="modal-title">บัญชีเงินฝาก ATM</h2>
            </div>
            <div class="modal-body" id="add_account_space">

            </div>
        </div>
    </div>
</div>
<div class="modal modal_in_modal fade" id="search_member_add_modal" role="dialog">
    <div class="modal-dialog">
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
                                    <select id="member_search_list" name="member_search_list"
                                            class="form-control m-b-1">
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
                                        <input id="member_search_text" name="member_search_text"
                                               class="form-control m-b-1" type="text"
                                               value="<?php echo @$data['id_card']; ?>">
                                        <span class="input-group-btn">
									<button type="button" id="member_search" class="btn btn-info btn-search"><span
                                            class="icon icon-search"></span></button>
								</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bs-example" data-example-id="striped-table">
                    <table class="table table-striped">
                        <tbody id="result_add">
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
<div id="close_account" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-add" style="width:40% !important;">
        <div class="modal-content">
            <div class="modal-header modal-header-info">
                <h2 class="modal-title">ปิดบัญชี</h2>
            </div>
            <div class="modal-body">
                <div class="g24-col-sm-24 ">
                    <form data-toggle="validator" novalidate="novalidate"
                          action="<?php echo base_url(PROJECTPATH . '/save_money/close_account'); ?>" method="post">
                        <input type="hidden" name="account_id" id="close_account_id">
                        <!-- <div class="form-group">
							<?php foreach ($data as $key => $row) { ?>
								<?php if ($key == ''){?>
							<div class="g24-col-sm-24 m-b-1" style="text-align:center !important;">
								<label class="control-label">
										<?php echo substr($row['account_id'], 0, 3) . "-" . substr($row['account_id'], 3, 2) . "-" . substr($row['account_id'], 5, 5) . "-" . substr($row['account_id'], 10); ?>
									</label>
							</div>
							<?php } ?>
							<?php } ?>
						</div> -->
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label g24-col-sm-7 m-b-1"> เลขบัญชี </label>
                                <div class="g24-col-sm-11">
                                    <p id="close_account_id_view" class="form-control-static"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label g24-col-sm-7 m-b-1"> ชื่อบัญชี </label>
                                <div class="g24-col-sm-11">
                                    <p id="close_account_name" class="form-control-static"></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="g24-col-sm-24 m-b-1" style="text-align:center !important;">
                                <label class="control-label"> จำนวนเงินที่จะได้รับ </label>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label g24-col-sm-7 m-b-1"> ยอดปัจจุบัน </label>
                                <div class="g24-col-sm-11">
                                    <input type="text" class="form-control" name="close_account_principal"
                                           id="close_account_principal" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label g24-col-sm-7 m-b-1">&nbsp;</label>
                                <div class="g24-col-sm-11">
                                    <input type="checkbox" name="is_ignore_interest_return" id="is_ignore_interest_return" value="1"> คิดดอกเบี้ยเต็ม ไม่หักส่วนสหกรณ์
                                    <input type="hidden" id="tmp_close_account_interest_return" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label g24-col-sm-7 m-b-1"> ดอกเบี้ย </label>
                                <div class="g24-col-sm-11">
                                    <input type="text" class="form-control cal_close_account_total" name="close_account_interest" id="close_account_interest" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label g24-col-sm-7 m-b-1"> คืนดอกเบี้ย </label>
                                <div class="g24-col-sm-11">
                                    <input type="text" class="form-control cal_close_account_total" name="close_account_interest_return" id="close_account_interest_return" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label g24-col-sm-7 m-b-1"> เสียภาษี </label>
                                <div class="g24-col-sm-11">
                                    <input type="text" class="form-control cal_close_account_total" name="close_account_tax_return" id="close_account_tax_return" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label g24-col-sm-7 m-b-1"> ยอดปิดบัญชี </label>
                                <div class="g24-col-sm-11">
                                    <p id="close_account_total" class="form-control-static"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label g24-col-sm-7 m-b-1"> เลือกการรับเงิน </label>
                                <div class="g24-col-sm-11">
                                    <input type="radio" name="pay_type" value="0" checked> เงินสด
                                    <input type="radio" name="pay_type" value="1"> โอนเงิน
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <div class="g24-col-sm-24" style="text-align:center">
                                    <button type="submit" class="btn btn-primary min-width-100">ยืนยัน</button>
                                    <button class="btn btn-danger min-width-100" type="button"
                                            onclick="close_modal('close_account');"> ยกเลิก
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                &nbsp;
            </div>
        </div>
    </div>
</div>
<script type="application/javascript" src="<?php echo base_url('/assets/js/atm_deposit.js?v='.date('YmdHis'))?>"></script>

