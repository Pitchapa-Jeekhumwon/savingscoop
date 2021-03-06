<div class="layout-content">
    <div class="layout-content-body">
        <style>
            label {
                padding-top: 7px;
            }

            .control-label {
                padding-top: 7px;
                text-align: right;
            }

            .control-label_2 {
                padding-top: 7px;
            }

            .indent {
                text-indent: 40px;
            }

            .modal-dialog-data {
                width: 90% !important;
                margin: auto;
                margin-top: 1%;
                margin-bottom: 1%;
            }

            table > thead > tr > th {
                text-align: center;
            }

            table > tbody > tr > td {
                text-align: center;
            }

            .text-center {
                text-align: center;
            }

            .text-right {
                text-align: right;
            }

            .bt-add {
                float: none;
            }

            .modal-dialog {
                width: 80%;
            }

            small {
                display: none !important;
            }
        </style>
        <?php
        $act = @$_GET['act'];
        $id = @$_GET['id'];

        ?>
        <?php if ($act != "add"  && $act != "duplicate") { ?>
            <h1 style="margin-bottom: 0">เงื่อนไขการกู้เงิน</h1>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                    <?php $this->load->view('breadcrumb'); ?>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
                    <button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_type();">
                        จัดการประเภทเงินกู้
                    </button>
                    <button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_loan_name();">
                        จัดการชื่อเงินกู้
                    </button>
                    <button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_loan_group();"> จัดการกลุ่มเงินกู้ </button>
                    <a class="link-line-none" href="?act=add">
                        <button class="btn btn-primary btn-lg bt-add" type="button"><span
                                    class="icon icon-plus-circle"></span> เพิ่มรายการ
                        </button>
                    </a>
                </div>
            </div>
        <?php } ?>

        <?php if ($act != "add"  && $act != "duplicate") { ?>

            <div class="row gutter-xs">
                <div class="col-xs-12 col-md-12">
                    <div class="panel panel-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <select id="type_id" class="form-control" onchange="change_type_id()">
                                    <option value="">เลือกประเภทเงินกู้</option>
                                    <?php foreach ($loan_type as $key => $value) { ?>
                                        <option value="<?php echo $value['id']; ?>" <?php echo @$_GET['type_id'] == $value['id'] ? 'selected' : ''; ?>><?php echo $value['loan_type']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="bs-example" data-example-id="striped-table">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="font-normal" width="5%">#</th>
                                    <th class="font-normal" style="width: 15%"> ประเภทการกู้เงิน</th>
                                    <th class="font-normal" style="width: 31%"> ชื่อเงินกู้</th>
                                    <th class="font-normal" style="width: 10%"> รหัสนำหน้าสัญญา</th>
                                    <th class="font-normal text-center" style="width: 8%"> อัตราดอกเบี้ย</th>
                                    <th class="font-normal text-center" style="width: 10%"> มีผลวันที่</th>
                                    <th class="font-normal text-center" style="width: 8%"> สถานะ</th>
                                    <th class="font-normal" style="width: 8%"> จัดการ</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($rs)) {
                                    foreach (@$rs as $key => $row) {
                                        $this->db->select(array('*'));
                                        $this->db->from('coop_term_of_loan');
                                        $this->db->where("type_id = '" . @$row['type_id'] . "' AND start_date <= '" . date('Y-m-d') . "'");
                                        $this->db->order_by('start_date DESC');
                                        $rs_status = $this->db->get()->result_array();
                                        $row_status = @$rs_status[0];
                                        ?>
                                        <tr>
                                            <td scope="row"><?php echo $i++; ?></td>
                                            <td class="text-left"><?php echo @$row['loan_type']; ?></td>
                                            <td class="text-left"><?php echo @$row['loan_name'] . " " . @$row['loan_name_description']; ?></td>
                                            <td><?php echo @$row['prefix_code']; ?></td>
                                            <td class="text-center"><a
                                                        href="<?= base_url() . "setting_credit_data/manage_interest?id=" . $row['id'] ?>"><?php echo @$row['interest_rate']; ?></a>
                                            </td>
                                            <!-- <td class="text-center"><a href="<?= base_url() . "setting_credit_data/manage_interest?id=" . $row['id'] ?>"><?php echo @$row['interest_rate']; ?></a></td>  -->
                                            <td class="text-center"><?php echo $this->center_function->ConvertToThaiDate(@$row['start_date'], '1', '0'); ?></td>
                                            <td><?php echo $row_status['id'] == @$row['id'] ? 'ใช้งาน' : 'ไม่ใช้งาน'; ?></td>
                                            <td>
                                                <a href="?act=add&id=<?php echo @$row["id"] ?>">แก้ไข</a> |
                                                <a href="?act=duplicate&id=<?php echo @$row["id"] ?>">คัดลอก</a> |
                                                <a href="#" onclick="del_coop_credit_data('<?php echo @$row['id']; ?>')"
                                                   class="text-del"> ลบ </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php echo @$paging ?>
                </div>
            </div>

        <?php } else { ?>

            <div class="col-md-10 col-md-offset-1">
                <h1 class="text-center m-t-1 m-b-2"> <?php echo (!empty($id)) ? "แก้ไขเงื่อนไขการกู้เงิน" : "เพิ่มเงื่อนไขการกู้เงิน "; ?></h1>

                <form id='form_save' data-toggle="validator" novalidate="novalidate"
                      action="<?php echo base_url(PROJECTPATH . '/setting_credit_data/coop_term_of_loan_save'); ?>"
                      method="post">
                    <?php if (!empty($id)) { 
                        $type_add = "edit";
					    	if($act == "duplicate"){
							    $type_add = "duplicate";
					}?>
                        <input name="type_add" type="hidden" value="<?=$type_add?>" required>
                        <input name="id" type="hidden" value="<?php echo $id; ?>" required>
                    <?php } else { ?>
                        <input name="type_add" type="hidden" value="add" required>
                    <?php } ?>
                    <input type="hidden" name="return_url" value="<?php echo @$_GET['return_url']; ?>">
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">มีผลวันที่</label>
                        <div class="col-sm-4">
                            <?php if (@$id != '' && $act != "duplicate") {
                                echo "<label class='control-label'>" . $this->center_function->mydate2date(@$row['start_date']) . "</label>";
                            } else {
                                ?>
                                <input id="start_date" name="start_date" class="form-control m-b-1"
                                       style="padding-left: 50px;" type="text"
                                       value="<?php echo $this->center_function->mydate2date(empty($row['start_date']) ? date('Y-m-d') : @date('Y-m-d')); ?>"
                                       data-date-language="th-th" required>
                                <span class="icon icon-calendar input-icon m-f-1"></span>
                            <?php } ?>
                        </div>
                        <label class="col-sm-4 control-label">&nbsp;</label>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label text-right" for="type_id">ประเภทการกู้เงิน</label>
                            <div class="col-sm-4">
                            <?php
								if($act == "duplicate") {
							?>
								<input type="hidden" name="type_id" value="<?php echo $row['loan_type_id'];?>">
								<input type="text" id="type_id_text" class="form-control m-b-1" value="<?php foreach($loan_type as $key => $value) {if($row['loan_type_id']==$value['id']) { echo $value["loan_type"];}}?>" readonly/>
								<?php
								} else {
							?>
                                <select class="form-control m-b-1" name="type_id" id="type_id" onchange="change_type('type_id');"
                                        required>
                                    <option value="" require>เลือกประเภทเงินกู้</option>
                                    <?php foreach ($loan_type as $key => $value) { ?>
                                        <option value="<?php echo $value['id']; ?>" <?php echo @$row['loan_type_id'] == $value['id'] ? 'selected' : ''; ?>><?php echo $value['loan_type']; ?></option>
                                    <?php } ?>
                                </select>
                            <?php 
                                } 
                            ?>		
                                <input name="type_name" id="type_name" type="hidden" value="<?php echo @$row['type_name']; ?>">                
                            </div>

                            <label class="col-sm-4 control-label">&nbsp;</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label text-right" for="loan_name_id">ชื่อเงินกู้ </label>
                            <div class="col-sm-4">
                            <?php
								if($act == "duplicate") {
							?>
								<input type="hidden" name="loan_name_id" value="<?php echo $row['type_id'];?>">
								<input type="text" id="loan_name_id_text" class="form-control m-b-1" value="<?php foreach($loan_name_choose as $key => $value) {if($row['type_id']==$value['loan_name_id']) { echo $value['loan_name']." ".$value['loan_name_description'];}}?>" readonly/>
							<?php
								} else {
							?>
                                <select class="form-control m-b-1" name="loan_name_id" id="loan_name_id"
                                        onchange="change_loan_name();" required>
                                    <option value="" require>เลือกชื่อเงินกู้</option>
                                    <?php if (!empty($loan_name_choose)) {
                                        foreach ($loan_name_choose as $key => $value) {
                                            ?>
                                            <option value="<?php echo $value['loan_name_id']; ?>" <?php echo $row['type_id'] == $value['loan_name_id'] ? 'selected' : ''; ?>><?php echo $value['loan_name'] . " " . $value['loan_name_description']; ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            <?php
								}
							?>
                            </div>
                            <input name="loan_name" id="loan_name" type="hidden"
                                   value="<?php echo @$row['type_name']; ?>">
                            <label class="col-sm-4 control-label">&nbsp;</label>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">สิทธิค้ำ/ประเมิน</label>
                        <div class="col-sm-4">
                            <input name="multiple_guarantee_estimate" id="multiple_guarantee_estimate"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['multiple_guarantee_estimate'] == '0') ? '' : @$row['multiple_guarantee_estimate']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">เท่าของเงินเดือน</label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">ไม่เกิน</label>
                        <div class="col-sm-4">
                            <input name="less_than_multiple_salary" id="less_than_multiple_salary"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['less_than_multiple_salary'] == '0') ? '' : @$row['less_than_multiple_salary']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">เท่าของเงินเดือน <a class=""
                                                                                    href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'less_than_multiple_salary' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">มีมูลค่าหุ้นสะสมไม่น้อยกว่า</label>
                        <div class="col-sm-4">
                            <input name="least_share_percent_for_loan" id="least_share_percent_for_loan"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['least_share_percent_for_loan'] == '0') ? '' : @$row['least_share_percent_for_loan']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">%ของวงเงินกู้ กรณีใช้บุคคลค้ำประกัน <a class=""
                                                                                                       href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'least_share_percent_for_loan' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">ต้องเป็นสมาชิกอย่างน้อย</label>
                        <div class="col-sm-4">
                            <input name="min_month_member" id="min_month_member" class="form-control m-b-1 check_number"
                                   type="text"
                                   value="<?php echo (@$row['min_month_member'] == '0') ? '' : @$row['min_month_member']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">เดือน <a class=""
                                                                         href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'min_month_member' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">จำนวนงวดชำระสูงสุด</label>
                        <div class="col-sm-4">
                            <input name="max_period" id="max_period" class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['max_period'] == '0') ? '' : @$row['max_period']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">งวด <a class=""
                                                                       href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'max_period' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">จำนวนชำระต้นขั้นต่ำ</label>
                        <div class="col-sm-4">
                            <input name="min_principal_amount" id="min_principal_amount"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['min_principal_amount'] == '0') ? '' : @$row['min_principal_amount']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">บาท <a class=""
                                                                       href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'min_principal_amount' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">อายุไม่เกิน</label>
                        <div class="col-sm-4">
                            <input name="age_limit" id="age_limit" class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['age_limit'] == '0') ? '' : @$row['age_limit']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">ปี <a class=""
                                                                      href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'age_limit' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">ผ่อนชำระมาแล้วไม่ต่ำกว่า</label>
                        <div class="col-sm-4">
                            <input name="min_installment" id="min_installment"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['min_installment'] == '0') ? '' : @$row['min_installment']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">งวด จึงจะกู้ใหม่ได้</label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">ผ่อนชำระมาแล้วไม่ต่ำกว่า</label>
                        <div class="col-sm-4">
                            <input name="min_installment_percent" id="min_installment_percent"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['min_installment_percent'] == '0') ? '' : @$row['min_installment_percent']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">% เงินกู้พิเศษเดิมจึงจะกู้ใหม่ได้ <a class=""
                                                                                                     href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'min_installment_percent' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">กู้วนซ้ำได้ ทุกๆ</label>
                        <div class="col-sm-4">
                            <input name="min_refinance_every" id="min_refinance_every"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['min_refinance_every'] == '0') ? '' : @$row['min_refinance_every']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">เดือน <a class=""
                                                                         href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'min_refinance_every' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label text-right" for="interest_rate">อัตราดอกเบี้ย</label>
                            <div class="col-sm-4">
                                <input name="interest_rate" id="interest_rate" class="form-control m-b-1 check_number"
                                       type="text"
                                       value="<?php echo (@$row['interest_rate'] == '0') ? '' : @$row['interest_rate']; ?>"
                                       required>
                            </div>
                            <label class="col-sm-4 control-label_2">% ต่อปี <a class=""
                                                                               href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'interest_rate' : "#") ?>"><i
                                            class="fa fa-cog" style="font-size:20px"></i></a></label>
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">สมาชิกหนึ่งคนค้ำประกันผู้กู้ ได้ไม่เกิน</label>
                        <div class="col-sm-4">
                            <input name="num_guarantee" id="num_guarantee" class="form-control m-b-1 check_number"
                                   type="text"
                                   value="<?php echo (@$row['num_guarantee'] == '0') ? '' : @$row['num_guarantee']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">คน ในเวลาเดียวกัน <a class=""
                                                                                     href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'num_guarantee' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">ใช้หุ้นค้ำประกันได้</label>
                        <div class="col-sm-4">
                            <input name="percent_share_guarantee" id="percent_share_guarantee"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['percent_share_guarantee'] == '0') ? '' : @$row['percent_share_guarantee']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">% ของหุ้นที่มี <a class=""
                                                                                  href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'percent_share_guarantee' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">ใช้กองทุนค้ำประกันได้</label>
                        <div class="col-sm-4">
                            <input name="percent_fund_quarantee" id="percent_fund_quarantee"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['percent_fund_quarantee'] == '0') ? '' : @$row['percent_fund_quarantee']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">% ของที่มี <a class=""
                                                                              href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'percent_fund_quarantee' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label text-right" for="prefix_petition">รหัสนำหน้าคำขอ</label>
                            <div class="col-sm-4">
                                <input name="prefix_petition" id="prefix_petition" class="form-control m-b-1" type="text"
                                       value="<?php echo @$row['prefix_petition']; ?>">
                            </div>
                            <label class="col-sm-4 control-label">&nbsp;</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label text-right" for="petition_pattern">รูปแบบเลขคำขอ</label>
                            <div class="col-sm-4">
                                <input name="petition_pattern" id="petition_pattern" class="form-control m-b-1"
                                       type="text" value="<?php echo @$row['petition_pattern']; ?>">
                            </div>
                            <label class="col-sm-4 control-label_2"> <a class="" href="#"
                                                                        onclick="contract_pattern_help()">วิธีการตั้งค่า
                                    <i class="fa fa-info" style="font-size:20px"></i></a></label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label text-right" for="prefix_code">รหัสนำหน้าสัญญา</label>
                            <div class="col-sm-4">
                                <input name="prefix_code" id="prefix_code" class="form-control m-b-1" type="text"
                                       value="<?php echo @$row['prefix_code']; ?>" required>
                            </div>
                            <label class="col-sm-4 control-label">&nbsp;</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label text-right" for="prefix_code">รูปแบบเลขสัญญา</label>
                            <div class="col-sm-4">
                                <input name="contract_pattern" id="contract_pattern" class="form-control m-b-1"
                                       type="text" value="<?php echo @$row['contract_pattern']; ?>" required>
                            </div>
                            <label class="col-sm-4 control-label_2"> <a class="" href="#"
                                                                        onclick="contract_pattern_help()">วิธีการตั้งค่า
                                    <i class="fa fa-info" style="font-size:20px"></i></a></label>
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">วงเงินสูงสุดที่กู้ได้</label>
                        <div class="col-sm-4">
                            <input name="credit_limit" id="credit_limit" class="form-control m-b-1 check_number"
                                   type="text"
                                   value="<?php echo (@$row['credit_limit'] == '0') ? '' : @$row['credit_limit']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">บาท <a class=""
                                                                       href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'credit_limit' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">จำนวนเงินกู้ขั้นต่ำ</label>
                        <div class="col-sm-4">
                            <input name="credit_minimum_percentage" id="credit_minimum_percentage" class="form-control m-b-1 check_number"
                                   type="text"
                                   value="<?php echo (@$row['credit_minimum_percentage'] == '0') ? '' : @$row['credit_minimum_percentage']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">% ของเงินได้รายเดือน <a class=""
                                                                       href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'credit_minimum_percentage' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">จำนวนเงินกู้ขั้นต่ำ</label>
                        <div class="col-sm-4">
                            <input name="credit_minimum_baht" id="credit_minimum_baht" class="form-control m-b-1 check_number"
                                   type="text"
                                   value="<?php echo (@$row['credit_minimum_baht'] == '0') ? '' : @$row['credit_minimum_baht']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">บาท <a class=""
                                                                                        href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'credit_minimum_baht' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">กู้ได้ไม่เกินร้อยละ</label>
                        <div class="col-sm-4">
                            <input name="credit_limit_share_percent" id="credit_limit_share_percent"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['credit_limit_share_percent'] == '0') ? '' : @$row['credit_limit_share_percent']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">ของหุ้นและกองทุนสำรองเลี้ยงชีพ <a class=""
                                                                                                  href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'credit_limit_share_percent' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>

                    <!--div class="row">
					<label class="col-sm-4 control-label text-right" >มีหุ้นสะสมและกองทุนสำรองเลี้ยงชีพรวมมากกว่า</label>
					<div class="col-sm-4">
						<input  name="min_share_fund_money" id="min_share_fund_money" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['min_share_fund_money'] == '0') ? '' : @$row['min_share_fund_money']; ?>">
					</div>
					<label class="col-sm-4 control-label_2" >บาท</label>
				</div-->

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">ต้องชำระค่าหุ้นมาแล้วไม่น้อยกว่า</label>
                        <div class="col-sm-4">
                            <input name="min_month_share_period" id="min_month_share_period"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['min_month_share_period'] == '0') ? '' : @$row['min_month_share_period']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">เดือน <a class=""
                                                                         href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'min_month_share_period' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">หุ้นสะสมต้องไม่น้อยกว่า</label>
                        <div class="col-sm-4">
                            <input name="min_share_total" id="min_share_total" class="form-control m-b-1 check_number"
                                   type="text"
                                   value="<?php echo (@$row['min_share_total'] == '0') ? '' : @$row['min_share_total']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">หุ้น <a class=""
                                                                        href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'min_share_total' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">เงินเหลือใช้จ่ายไม่น้อยกว่า</label>
                        <div class="col-sm-4">
                            <input name="money_use_balance" id="money_use_balance"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['money_use_balance'] == '0') ? '' : @$row['money_use_balance']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">% <a class=""
                                                                     href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'money_use_balance' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">หรือ เงินเหลือใช้จ่ายไม่น้อยกว่า</label>
                        <div class="col-sm-4">
                            <input name="money_use_balance_baht" id="money_use_balance_baht"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['money_use_balance_baht'] == '0') ? '' : @$row['money_use_balance_baht']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">บาท <a class=""
                                                                       href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'money_use_balance_baht' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">ต้องมีเงินในหุ้นหรือสมุดบัญชีมากกว่า</label>
                        <div class="col-sm-4">
                            <input name="least_share_or_blue_acc_percent" id="least_share_or_blue_acc_percent"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['least_share_or_blue_acc_percent'] == '0') ? '' : @$row['least_share_or_blue_acc_percent']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">% <a class=""
                                                                     href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'least_share_or_blue_acc_percent' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">การหักกลบต้องชำระเงินกู้มากกว่า</label>
                        <div class="col-sm-4">
                            <input name="prev_loan_period_min" id="prev_loan_period_min"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['prev_loan_period_min'] == '0') ? '' : @$row['prev_loan_period_min']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">งวด</label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">การหักกลบต้องได้รับเงินสุทธิไม่น้อยกว่า</label>
                        <div class="col-sm-4">
                            <input name="prev_loan_amount_min" id="prev_loan_amount_min"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['prev_loan_amount_min'] == '0') ? '' : @$row['prev_loan_amount_min']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">บาท</label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">จำนวนปีผ่อนชำระหลังเกษียณ</label>
                        <div class="col-sm-4">
                            <input name="year_retire" id="year_retire"
                                   class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo $row['year_retire']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">ปี</label>
                    </div>

                    <div class="row">
                        <label class="col-sm-4 control-label text-right">สามารถกู้ใหม่ได้เมื่อปิดสัญญา</label>
                        <div class="col-sm-2">
                            <select class="form-control m-b-1" name="close_loan_type_id" id="close_loan_type_id"
                                    onchange="change_close_loan();">
                                <option value="">เลือกประเภทเงินกู้</option>
                                <?php foreach ($loan_type as $key => $value) { ?>
                                    <option value="<?php echo $value['id']; ?>" <?php echo @$row['close_loan_type_id'] == $value['id'] ? 'selected' : ''; ?>><?php echo $value['loan_type']; ?></option>
                                <?php } ?>
                            </select>
                            <input name="close_type_name" id="close_type_name" type="hidden"
                                   value="<?php echo @$row['close_loan_type_id']; ?>">
                        </div>
                        <div class="col-sm-2">
                            <select class="form-control m-b-1" name="close_loan_name_id" id="close_loan_name_id"
                                    onchange="change_close_loan_name();">
                                <option value="">เลือกชื่อเงินกู้</option>
                                <?php if (!empty($close_loan_name_choose)) {
                                    foreach ($close_loan_name_choose as $key => $value) {
                                        ?>
                                        <option value="<?php echo $value['loan_name_id']; ?>" <?php echo $row['close_loan_name_id'] == $value['loan_name_id'] ? 'selected' : ''; ?>><?php echo $value['loan_name'] . " " . $value['loan_name_description']; ?></option>
                                    <?php }
                                } ?>
                            </select>
                            <input name="close_loan_name" id="close_loan_name" type="hidden"
                                   value="<?php echo @$row['close_loan_name_id']; ?>">
                        </div>
                    </div>


					<div class="row">
						<label class="col-sm-4 control-label text-right">หลักประกัน</label>
						<div class="col-sm-4">
							<input name="percent_guarantee" id="percent_guarantee" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['percent_guarantee'] == '0') ? '' : @$row['percent_guarantee']; ?>">
						</div>
						<label class="col-sm-4 control-label_2">% <a class="" href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'percent_guarantee' : "#") ?>"><i class="fa fa-cog" style="font-size:20px"></i></a></label>
					</div>
					<div class="row">
						<label class="col-sm-4 control-label text-right">คิดจาก</label>
						<div class="col-sm-4">
							<div class="radio" style="margin-top:0px;margin-bottom:0px;">
								<label><input type="radio" name="percent_guarantee_option" value="0" <?php echo @$row['percent_guarantee_option'] == '0' ? 'checked' : ''; ?>>ยอดกู้</label>
								<label><input type="radio" name="percent_guarantee_option" value="1" <?php echo @$row['percent_guarantee_option'] == '1' ? 'checked' : ''; ?>>ยอดเงินกู้ – ยอดหนี้ประเภทเดียวกัน</label>
							</div>
						</div>
						<label class="col-sm-4 control-label"></label>
					</div>
					<div class="row">
						<label class="col-sm-4 control-label text-right">ค่าธรรมเนียมการกู้</label>
						<div class="col-sm-4">
							<input name="loan_fee" id="loan_fee" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['loan_fee'] == '0') ? '' : @$row['loan_fee']; ?>">
						</div>
						<label class="col-sm-4 control-label_2">% <a class="" href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'loan_fee' : "#") ?>"><i class="fa fa-cog" style="font-size:20px"></i></a></label>
					</div>
					<div class="row">
						<label class="col-sm-4 control-label text-right">ค่าธรรมเนียมการกู้</label>
                        <div class="col-sm-4">
                            <input name="loan_fee_baht" id="loan_fee_baht" class="form-control m-b-1 check_number" type="text"
                                   value="<?php echo (@$row['loan_fee_baht'] == '0') ? '' : @$row['loan_fee_baht']; ?>">
                        </div>
                        <label class="col-sm-4 control-label_2">บาท <a class=""
                                                                     href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'loan_fee_baht' : "#") ?>"><i
                                        class="fa fa-cog" style="font-size:20px"></i></a></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">ค่าธรรมเนียมการเปลี่ยนสัญญา</label>
						<div class="col-sm-4">
							<input name="new_loan_fee" id="new_loan_fee" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['new_loan_fee'] == '0') ? '' : @$row['new_loan_fee']; ?>">
						</div>
						<label class="col-sm-4 control-label_2">% <a class="" href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'new_loan_fee' : "#") ?>"><i class="fa fa-cog" style="font-size:20px"></i></a></label>
					</div>
					<div class="row">
						<label class="col-sm-4 control-label text-right">หักส่งต่องวด (ต้น+ดบ.) ขั้นต่ำ</label>
						<div class="col-sm-4">
							<input name="money_per_period" id="money_per_period" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['money_per_period'] == '0') ? '' : @$row['money_per_period']; ?>">
						</div>
						<label class="col-sm-4 control-label_2">บาท <a class="" href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_condition?id=' . @$_GET['id'] . '&key=' . 'money_per_period' : "#") ?>"><i class="fa fa-cog" style="font-size:20px"></i></a></label>
					</div>
					<div class="row">
						<label class="col-sm-4 control-label text-right">กู้ได้ไม่เกิน</label>
						<div class="col-sm-4">
							<input name="max_contract_total" id="max_contract_total" class="form-control m-b-1 check_number" type="text" value="<?php echo (@$row['max_contract_total'] == '0') ? '' : @$row['max_contract_total']; ?>">
						</div>
						<label class="col-sm-4 control-label_2">สัญญา</label>
					</div>
                    <div class="row">
					<label class="col-sm-4 control-label text-right" >หนังสือคำขอกู้เงิน</label>
					<div class="col-sm-4">
						<select name="type_pdf_id" id="type_pdf_id" onchange="link_perview('type_pdf_id')" class="form-control m-b-1">
						<?php echo @$show['type_pdf_id']['id']!=''? '<option value="' . @$show['type_pdf_id']['id'] . '" selected>'.@$show['type_pdf_id']['details'].'</option>':'<option value="" selected>ไม่มีข้อมูลที่เลือกไว้</option>'; ?>
						</select>
					</div>
					<label class="col-sm-4 control-label_2" ><a class=""  id="pdf_preview_type_pdf_id" href="<?php echo @$show['type_pdf_id']['path_data']!=''?"/assets/document/loan_request/".@$show['type_pdf_id']['path_data']:'' ?>" target="_blank">Preview <i class="fa fa-eye" style="font-size:20px"aria-hidden="true"></i></a></label>
				</div>
				<div class="row">
					<label class="col-sm-4 control-label text-right" >หนังสือสัญญากู้เงิน</label>
					<div class="col-sm-4">
						<select name="transfer_pdf_id" id="transfer_pdf_id" onchange="link_perview('transfer_pdf_id')" class="form-control m-b-1">
						<?php echo @$show['transfer_pdf_id']['id']!=''? '<option value="' . @$show['transfer_pdf_id']['id'] . '" selected>'.@$show['transfer_pdf_id']['details'].'</option>':'<option value="" selected>ไม่มีข้อมูลที่เลือกไว้</option>'; ?>
						</select>
					</div>
					<label class="col-sm-4 control-label_2" ><a class=""  id="pdf_preview_transfer_pdf_id" href="<?php echo @$show['transfer_pdf_id']['path_data']!=''?"/assets/document/loan_request/".@$show['transfer_pdf_id']['path_data']:'' ?>" target="_blank">Preview <i class="fa fa-eye" style="font-size:20px"aria-hidden="true"></i></a></label>
				</div>
				<div class="row">
					<label class="col-sm-4 control-label text-right" >หนังสือค้ำประกัน</label>
					<div class="col-sm-4">
						<select name="surety_pdf_id" id="surety_pdf_id" onchange="link_perview('surety_pdf_id')" class="form-control m-b-1">
						<?php echo @$show['surety_pdf_id']['id']!=''? '<option value="' . @$show['surety_pdf_id']['id'] . '" selected>'.@$show['surety_pdf_id']['details'].'</option>':'<option value="" selected>ไม่มีข้อมูลที่เลือกไว้</option>'; ?>
						</select>
					</div>
					<label class="col-sm-4 control-label_2" ><a class=""  id="pdf_preview_surety_pdf_id" href="<?php echo @$show['surety_pdf_id']['path_data']!=''?"/assets/document/loan_request/".@$show['surety_pdf_id']['path_data']:'' ?>" target="_blank">Preview <i class="fa fa-eye" style="font-size:20px"aria-hidden="true"></i></a></label>
				</div>
				<div class="row">
					<label class="col-sm-4 control-label text-right" >หนังสือรับรองเงินเดือน</label>
					<div class="col-sm-4">
						<select name="append_pdf_id" id="append_pdf_id" onchange="link_perview('append_pdf_id')" class="form-control m-b-1">
						<?php echo @$show['append_pdf_id']['id']!=''? '<option value="' . @$show['append_pdf_id']['id'] . '" selected>'.@$show['append_pdf_id']['details'].'</option>':'<option value="" selected>ไม่มีข้อมูลที่เลือกไว้</option>'; ?>
						</select>
					</div>
					<label class="col-sm-4 control-label_2" ><a class=""  id="pdf_preview_append_pdf_id" href="<?php echo @$show['append_pdf_id']['path_data']!=''?"/assets/document/loan_request/".@$show['append_pdf_id']['path_data']:'' ?>" target="_blank">Preview <i class="fa fa-eye" style="font-size:20px"aria-hidden="true"></i></a></label>
				</div>
                <div class="row">
					<label class="col-sm-4 control-label text-right" >ตัวจัดการไฟล์เอกสาร</label>
					<label class="col-sm-4 control-label text_left"><a href="/Loan_document/manage_doc"  target="_blank"><i class="fa fa-file-text-o"style="font-size:20px" ></i>  จัดการไฟล์เอกสาร</a></label>
				</div>
				<div class="row">
					<div class="row">
						<label class="col-sm-4 control-label text-right">คิดจาก</label>
						<div class="col-sm-4">
							<div class="radio" style="margin-top:0px;margin-bottom:0px;">
								<label><input type="radio" name="loan_fee_option" value="0" <?php echo @$row['loan_fee_option'] == '0' ? 'checked' : ''; ?>>ยอดกู้</label>
								<label><input type="radio" name="loan_fee_option" value="1" <?php echo @$row['loan_fee_option'] == '1' ? 'checked' : ''; ?>>ยอดคงเหลือหลังหักกลบ</label>
							</div>
						</div>
						<label class="col-sm-4 control-label"></label>
					</div>
					<div class="row">
						<label class="col-sm-4 control-label text-right">ใช้หลักประกัน</label>
						<div class="col-sm-4">
							<label class="custom-control custom-control-primary custom-checkbox" style="padding-top: 9px;">
								<input type="checkbox" id="share_guarantee" name="share_guarantee" class="custom-control-input" value="1" <?php echo (@$row['share_guarantee'] == '1') ? 'checked' : ''; ?>>
								<span class="custom-control-indicator" style="margin-top: 9px;"></span>
								<span class="custom-control-label">ใช้หุ้นค้ำประกัน</span>
							</label>
						</div>
						<label class="col-sm-4 control-label"></label>
					</div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right"></label>
                        <div class="col-sm-4">
                            <label class="custom-control custom-control-primary custom-checkbox"
                                   style="padding-top: 9px;">
                                <input type="checkbox" id="person_guarantee" name="person_guarantee"
                                       class="custom-control-input"
                                       value="1" <?php echo (@$row['person_guarantee'] == '1') ? 'checked' : ''; ?>>
                                <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                <span class="custom-control-label">ใช้บุคคลค้ำประกัน</span>
                            </label>
                        </div>
                        <label class="col-sm-4 control-label text-left">
                            <a class="" href="<?= (@$_GET['id'] ? base_url() . 'setting_credit_data/manage_garantor?id=' . $_GET['id'] : "#") ?>">เงื่อนไขผู้ค้ำประกัน</a>
                        </label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right"></label>
                        <div class="col-sm-4">
                            <label class="custom-control custom-control-primary custom-checkbox"
                                   style="padding-top: 9px;">
                                <input type="checkbox" id="real_estate_guarantee" name="real_estate_guarantee"
                                       class="custom-control-input"
                                       value="1" <?php echo (@$row['real_estate_guarantee'] == '1') ? 'checked' : ''; ?>>
                                <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                <span class="custom-control-label">ใช้อสังหาริมทรัพย์ค้ำประกัน</span>
                            </label>
                        </div>
                        <label class="col-sm-4 control-label"></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right"></label>
                        <div class="col-sm-4">
                            <label class="custom-control custom-control-primary custom-checkbox"
                                   style="padding-top: 9px;">
                                <input type="checkbox" id="deposit_guarantee" name="deposit_guarantee"
                                       class="custom-control-input"
                                       value="1" <?php echo (@$row['deposit_guarantee'] == '1') ? 'checked' : ''; ?>>
                                <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                <span class="custom-control-label">ใช้เงินฝาก</span>
                            </label>
                        </div>
                        <label class="col-sm-4 control-label"></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right"></label>
                        <div class="col-sm-4">
                            <label class="custom-control custom-control-primary custom-checkbox"
                                   style="padding-top: 9px;">
                                <input type="checkbox" id="share_and_deposit_guarantee"
                                       name="share_and_deposit_guarantee" class="custom-control-input"
                                       value="1" <?php echo (@$row['share_and_deposit_guarantee'] == '1') ? 'checked' : ''; ?>>
                                <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                <span class="custom-control-label">ใช้ทุนเรือนหุ้น รวมกับบัญชีเงินฝาก</span>
                            </label>
                        </div>
                        <label class="col-sm-4 control-label"></label>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right">การคำนวณปันผลเฉลี่ยคืน</label>
                        <div class="col-sm-4">
                            <label class="custom-control custom-control-primary custom-checkbox"
                                   style="padding-top: 9px;">
                                <input type="checkbox" id="no_cal_dividend_average" name="no_cal_dividend_average"
                                       class="custom-control-input"
                                       value="1" <?php echo (@$row['no_cal_dividend_average'] == '1') ? 'checked' : ''; ?>>
                                <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                <span class="custom-control-label">ไม่คำนวณปันผลเฉลี่ยคืน</span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right"> กรณีเบี้ยวหนี้ </label>
                        <div class="col-sm-4">
                            <label class="custom-control custom-control-primary custom-checkbox"
                                   style="padding-top: 9px;">
                                <input type="checkbox" id="guarantee_interest" name="guarantee_interest"
                                       class="custom-control-input"
                                       value="1" <?php echo (@$row['guarantee_interest'] == '1') ? 'checked' : ''; ?>>
                                <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                <span class="custom-control-label">คิดดอกเบี้ยผู้ค้ำประกัน</span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right"> ประกันชีวิต </label>
                        <div class="col-sm-4">
                            <label class="custom-control custom-control-primary custom-checkbox"
                                   style="padding-top: 9px;">
                                <input type="checkbox" id="life_insurance" name="life_insurance"
                                       class="custom-control-input"
                                       value="1" <?php echo (@$row['life_insurance'] == '1') ? 'checked' : ''; ?>>
                                <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                <span class="custom-control-label">คำนวณเบี้ยประกันชีวิต</span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 control-label text-right"> แบ่งโอนเงินกู้ </label>
                        <div class="col-sm-4">
                            <label class="custom-control custom-control-primary custom-checkbox"
                                   style="padding-top: 9px;">
                                <input type="checkbox" id="installment_enabled" name="installment_enabled"
                                       class="custom-control-input"
                                       value="1" <?php echo (@$row['installment_enabled'] == '1') ? 'checked' : ''; ?>>
                                <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                <span class="custom-control-label">เปิดใช้งานการแบ่งโอนเงินกู้</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group text-center">&nbsp;</div>

                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary min-width-100">ตกลง</button>
                        <a href="?">
                            <button class="btn btn-danger min-width-100" type="button">ยกเลิก</button>
                        </a>
                    </div>

                </form>
            </div>

        <?php } ?>
    </div>
</div>
<div id="loan_type_modal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-data">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h2 class="modal-title"><span id="title_1">จัดการประเภทเงินกู้</span></h2>
            </div>
            <div class="modal-body">
                <div class="form-group" style="padding-bottom: 30px;">
                    <form id='form1' data-toggle="validator" novalidate="novalidate"
                          action="<?php echo base_url(PROJECTPATH . '/setting_credit_data/coop_loan_type_save'); ?>"
                          method="post">
                        <input type="hidden" class="form-control" id="loan_type_id" name="loan_type_id" value="">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="loan_type">ประเภทเงินกู้</label>
                            <div class="col-sm-4">
                                <input id="loan_type" name="loan_type" class="form-control m-b-1" type="text" value=""
                                       required>
                            </div>
                        </div>
                        <div class="form-group col-sm-12" style="text-align:center;margin-top:10px;">
                            <label class="col-sm-4 control-label" for="loan_name_description">สถานะ</label>
                            <div class="col-sm-4 text-left">
                                <label class="custom-control custom-control-primary custom-checkbox"
                                       style="padding-top: 9px;">
                                    <input type="checkbox" id="loan_type_status" name="loan_type_status"
                                           class="custom-control-input" value="1" checked>
                                    <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                    <span class="custom-control-label">แสดง</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12" style="text-align:center;margin-top:20px;margin-bottom:20px;">
                                <button type="button" class="btn btn-primary" onclick="save_type()">บันทึก</button>&nbsp;&nbsp;&nbsp;
                                <button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
                            </div>
                        </div>

                        <table id="group_table" class="table table-bordered table-striped table-center">
                            <thead>
                            <tr class="bg-primary">
                                <th width="80px">ลำดับ</th>
                                <th>ประเภทเงินกู้</th>
                                <th>สถานะ</th>
                                <th width="100px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $j = 1;
                            if (!empty($loan_type)) {
                                foreach (@$loan_type as $key => $value) {
                                    ?>
                                    <tr>
                                        <td><?php echo @$j++; ?></td>
                                        <td style="text-align:left;"><?php echo @$value['loan_type']; ?></td>
                                        <td style="text-align:center;"><?php echo @$arr_loan_type_status[@$value['loan_type_status']]; ?></td>
                                        <td>
                                            <a style="cursor:pointer;"
                                               onclick="edit_type('<?php echo @$value['id']; ?>','<?php echo @$value['loan_type']; ?>','<?php echo @$value['loan_type_status']; ?>');">แก้ไข</a>
                                            |
                                            <a style="cursor:pointer;"
                                               onclick="del_type('<?php echo @$value['id']; ?>');"
                                               class="text-del">ลบ</a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="loan_name_modal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-data">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h2 class="modal-title"><span id="title_1">จัดการชื่อเงินกู้</span></h2>
            </div>
            <div class="modal-body">
                <div class="form-group" style="padding-bottom: 30px;">
                    <form id='form2' data-toggle="validator" novalidate="novalidate"
                          action="<?php echo base_url(PROJECTPATH . '/setting_credit_data/coop_loan_name_save'); ?>"
                          method="post">
                        <input type="hidden" class="form-control" id="loan_name_id" name="loan_name_id" value="">
                        <div class="form-group col-sm-12">
                            <label class="col-sm-4 control-label" for="loan_type">ประเภทการกู้เงิน</label>
                            <div class="col-sm-4">
                                <select name="loan_type_id" id="choose_loan_type_id" class="form-control" required>
                                    <option value=""> เลือกประเภทเงินกู้</option>
                                    <?php foreach (@$loan_type as $key => $value) { ?>
                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['loan_type']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-4"></div>
                        </div>
                        <div class="form-group col-sm-12" style="text-align:center;margin-top:10px;">
						<label class="col-sm-4 control-label" for="choose_loan_group_id">กลุ่มเงินกู้</label>
						<div class="col-sm-4">
							<select name="loan_group_id" id="choose_loan_group_id" class="form-control" required>
								<option value=""> เลือกกลุ่มเงินกู้ </option>
								<?php foreach(@$group_name as $key => $value){ ?>
									<option value="<?php echo $value['loan_group_id']; ?>"><?php echo $value['loan_group_name']; ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-sm-4"></div>
					</div>
                        <div class="form-group col-sm-12" style="text-align:center;margin-top:10px;">
                            <label class="col-sm-4 control-label" for="loan_name">ชื่อเงินกู้</label>
                            <div class="col-sm-4">
                                <input id="loan_name" name="loan_name" class="form-control m-b-1" type="text" value=""
                                       required>
                            </div>
                        </div>
                        <div class="form-group col-sm-12" style="text-align:center;margin-top:10px;">
                            <label class="col-sm-4 control-label" for="loan_name_description">คำอธิบาย</label>
                            <div class="col-sm-4">
                                <input id="loan_name_description" name="loan_name_description"
                                       class="form-control m-b-1" type="text" value="">
                            </div>
                        </div>
                        <div class="form-group col-sm-12" style="text-align:center;margin-top:10px;">
                            <label class="col-sm-4 control-label" for="loan_name_description">สถานะ</label>
                            <div class="col-sm-4 text-left">
                                <label class="custom-control custom-control-primary custom-checkbox"
                                       style="padding-top: 9px;">
                                    <input type="checkbox" id="loan_name_status" name="loan_name_status"
                                           class="custom-control-input" value="1" checked>
                                    <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                    <span class="custom-control-label">แสดง</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-sm-12" style="text-align:center;margin-top:0px;">
                            <label class="col-sm-4 control-label" for="loan_name_description"></label>
                            <div class="col-sm-4 text-left">
                                <label class="custom-control custom-control-primary custom-checkbox"
                                       style="padding-top: 9px;">
                                    <input type="checkbox" id="loan_app" name="loan_app" class="custom-control-input">
                                    <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                    <span class="custom-control-label">ใช้งานเงินกู้ผ่านแอพ</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-sm-12" style="text-align:center;margin-top:0px;">
                            <label class="col-sm-4 control-label" for="loan_name_description"></label>
                            <div class="col-sm-4 text-left">
                                <label class="custom-control custom-control-primary custom-checkbox" style="padding-top: 9px;">
                                    <input type="checkbox" id="check_contract_main" name="check_contract_main"
                                           class="custom-control-input" value="1">
                                    <span class="custom-control-indicator" style="margin-top: 9px;"></span>
                                    <span class="custom-control-label">เงินกู้หลัก</span>
                                </label>

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12" style="text-align:center;margin-top:10px;margin-bottom:10px;">
                                <button type="button" class="btn btn-primary" onclick="save_loan_name()">บันทึก</button>&nbsp;&nbsp;&nbsp;
                                <button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
                            </div>
                        </div>

                        <table id="group_table" class="table table-bordered table-striped table-center">
                            <thead>
                            <tr class="bg-primary">
                                <th width="80px">ลำดับ</th>
                                <th width="25%">ประเภทเงินกู้</th>
                                <th width="15%">กลุ่มเงินกู้</th>
                                <th>ชื่อเงินกู้</th>
                                <th>สถานะ</th>
                                <th>ใช้งานเงินกู้ผ่านแอพ</th>
                                <th>สถานะสัญญาหลัก</th>
                                <th width="100px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $j = 1;
                            if (!empty($loan_name)) {
                                foreach (@$loan_name as $key => $value) {
                                    ?>
                                    <tr>
                                        <td><?php echo @$j++; ?></td>
                                        <td style="text-align:left;"><?php echo @$value['loan_type']; ?></td>
                                        <td style="text-align:left;"><?php echo @$value['loan_group_name']; ?></td>
                                        <td style="text-align:left;"><?php echo @$value['loan_name'] . " " . @$value['loan_name_description']; ?></td>
                                        <td style="text-align:center;"><?php echo @$arr_loan_name_status[@$value['loan_name_status']]; ?></td>
                                        <td style="text-align:center;"><?php echo @$arr_loan_app_status[@$value['loan_app']]; ?>
                                        </td>
                                        <td style="text-align:center;"><?php echo @$arr_loan_check_contract_main[$value['check_contract_main']]; ?>
                                        </td>
                                        <td>
                                        <a style="cursor:pointer;"
                                               onclick="edit_loan_name('<?php echo @$value['loan_name_id']; ?>','<?php echo @$value['loan_type_id']; ?>','<?php echo @$value['loan_name']; ?>','<?=$value['loan_group_id'];?>','<?php echo @$value['loan_name_description']; ?>','<?php echo @$value['loan_name_status']; ?>','<?php echo @$value['loan_app']; ?>','<?php echo @$value['check_contract_main']; ?>');">แก้ไข</a>
                                            |
                                            <a style="cursor:pointer;"
                                               onclick="del_loan_name('<?php echo @$value['loan_name_id']; ?>');"
                                               class="text-del">ลบ</a>
                                        </td>
                                    </tr>
                                    <?php

                                }

                            }
                            ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="contract_pattern_help" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-data">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h2 class="modal-title"><span id="title_1">วิธีการตั้งค่า รูปแบบเลขสัญญา</span></h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-offset-2 col-md-8">
                        <ul>
                            <li>Y - ปี ค.ศ. 4 หลัก (2020)</li>
                            <li>y - ปี ค.ศ. 2 หลักสุดท้าย (20)</li>
                            <li>YTH - ปี พ.ศ. 4 หลัก (2563)</li>
                            <li>yth - ปี พ.ศ. 2 หลักสุดท้าย (63)</li>
                            <li>m - ลำดับเดือน 2 หลัก (01 - 12)</li>
                            <li>n - ลำดับเดือน แบบไม่มีเลข 0 นำหน้าเมื่อเป็นลำดับเดือนที่มี 1 หลัก (1 to 12)</li>
                            <li>PREFIX - รหัสนำหน้าสัญญา (ค่าที่กรอกในช่อง รหัสนำหน้าสัญญา)</li>
                            <li>RUNNO1 - เลขรันตามประเภทเงินกู้ (1)</li>
                            <li>RUNNO2 - เลขรันตามประเภทเงินกู้ (01)</li>
                            <li>RUNNO3 - เลขรันตามประเภทเงินกู้ (001)</li>
                            <li>RUNNO4 - เลขรันตามประเภทเงินกู้ (0001)</li>
                            <li>RUNNO5 - เลขรันตามประเภทเงินกู้ (00001)</li>
                            <li>RUNNO6 - เลขรันตามประเภทเงินกู้ (000001)</li>
                        </ul>

                        <div class="well">
                            <p>
                                <b>ตย.</b>
                                <span style="color: red;">YTH</span>
                                <span style="color: green;">/</span>
                                <span style="color: blue;">RUNNO4</span>
                                <span> = </span>
                                <span style="color: red;">2563</span>
                                <span style="color: green;">/</span>
                                <span style="color: blue;">0074</span>
                            </p>

                            <p>
                                <b>ตย.</b>
                                <span style="color: red;">Y</span>
                                <span style="color: green;">m</span>
                                <span style="color: blue;">RUNNO6</span>
                                <span> = </span>
                                <span style="color: red;">2020</span>
                                <span style="color: green;">09</span>
                                <span style="color: blue;">000027</span>
                            </p>

                            <p>
                                <b>ตย.</b>
                                <span style="color: red;">ABC</span>
                                <span style="color: green;">yth</span>
                                <span style="color: blue;">RUNNO5</span>
                                <span> = </span>
                                <span style="color: red;">ABC</span>
                                <span style="color: green;">63</span>
                                <span style="color: blue;">03066</span>
                            </p>

                            <p>
                                <b>ตย.</b>
                                <span style="color: red;">PREFIX</span>
                                <span style="color: green;">.</span>
                                <span style="color: blue;">yth</span>
                                <span style="color: purple;">RUNNO5</span>
                                <span> = </span>
                                <span style="color: red;">สห</span>
                                <span style="color: green;">.</span>
                                <span style="color: blue;">63</span>
                                <span style="color: purple;">03066</span>
                            </p>

                        </div>
                        <!-- // Y - A four digit representation of a year
                        // y - A two digit representation of a year
                        // YTH - A four digit representation of a year THAI
                        // yth - A two digit representation of a year THAI
                        // m - A numeric representation of a month (from 01 to 12)
                        // n - A numeric representation of a month, without leading zeros (1 to 12)
                        // RUNNO1 - เลขรันตามประเภทเงินกู้ 1
                        // RUNNO2 - เลขรันตามประเภทเงินกู้ 01
                        // RUNNO3 - เลขรันตามประเภทเงินกู้ 001
                        // RUNNO4 - เลขรันตามประเภทเงินกู้ 0001
                        // RUNNO5 - เลขรันตามประเภทเงินกู้ 00001
                        // RUNNO6 - เลขรันตามประเภทเงินกู้ 000001

                        //e.g. "YTH/RUNNO4" = 2563/0074
                        //e.g. "YTHnRUNNO6" = 256309000027
                        //e.g. "PREFIX.ythRUNNO5" = สห.6303066 -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('setting_credit_data/mng_loan_group'); ?>
<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH . 'assets/js/coop_term_of_loan.js?v=' . $v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
