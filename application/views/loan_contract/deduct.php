<style type="text/css">
    .deduct-list{
        padding-top: 25px;
    }
</style>
<div class="deduct-list">
    <?php if(isset($loan_deduct_list) && sizeof($loan_deduct_list) >= 1){ ?>
        <?php
        $j = 1;
        $has_deduct_coop_saving = 0;
        for ($i = 0; $i < round(count($loan_deduct_list) / 2); $i++) {
            ?>
            <div class="g24-col-sm-24 modal_data_input">
                <?php if(isset($loan_deduct_list_odd) && $loan_deduct_list_odd[$i]['loan_deduct_list'] != ''){ ?>
                    <?php if($loan_deduct_list_odd[$i]['loan_deduct_list_code'] != "deduct_insurance" && $loan_deduct_list_odd[$i]['loan_deduct_list_code'] != "deduct_coop_saving"){ ?>
                        <label class="g24-col-sm-4 control-label" <?php echo $loan_deduct_list_odd[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : ''; ?>><?php echo $j++ . ". " . $loan_deduct_list_odd[$i]['loan_deduct_list']; ?></label>
                        <div class="g24-col-sm-5" <?php echo $loan_deduct_list_odd[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : ''; ?>>
                            <div class="form-group">
                                <input class="form-control loan_deduct text-right" type="text" name="data[loan_deduct][<?php echo $loan_deduct_list_odd[$i]['loan_deduct_list_code']; ?>]"
                                       id="<?php echo $loan_deduct_list_odd[$i]['loan_deduct_list_code']; ?>" value="0.00" onkeyup="format_the_number_decimal(this);" onblur="cal_estimate_money()">
                            </div>
                        </div>
                    <?php
                            } elseif($loan_deduct_list_odd[$i]['loan_deduct_list_code'] == "deduct_coop_saving") {
                                $has_deduct_coop_saving = 1;
                            }else{
                    ?>
                        <label class="g24-col-sm-4 control-label" for="deduct_insurance"><?php echo $j++ . ". " . $loan_deduct_list_odd[$i]['loan_deduct_list']; ?></label>
                        <div class="g24-col-sm-2">
                            <div class="form-group">
                                <input class="form-control loan_deduct text-right" type="text" name="data[loan_deduct][deduct_insurance]" id="deduct_insurance" value="0.00"  onkeyup="format_the_number_decimal(this);" onblur="cal_estimate_money()"/>
                            </div>
                        </div>
                        <label class="g24-col-sm-2 text-center" style="margin: 0 -15px">ทุนเอาประกัน</label>
                        <div class="g24-col-sm-4">
                            <div class="form-group">
                                <input class="form-control text-right" type="text" value="0.00" name="data[coop_left_insurance]" id="left_insurance" onkeyup="format_the_number_decimal(this);">
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
                <?php if (isset($loan_deduct_list_even) && $loan_deduct_list_even[$i]['loan_deduct_list'] != '') { ?>
                    <?php if($loan_deduct_list_even[$i]['loan_deduct_list_code'] != "deduct_insurance"){ ?>
                        <label class="g24-col-sm-4 control-label" <?php echo $loan_deduct_list_even[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : ''; ?>><?php echo $j++ . ". " . $loan_deduct_list_even[$i]['loan_deduct_list']; ?></label>
                        <div class="g24-col-sm-5" <?php echo $loan_deduct_list_even[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : ''; ?>>
                            <div class="form-group">
                                <input class="form-control loan_deduct text-right" type="text" name="data[loan_deduct][<?php echo $loan_deduct_list_even[$i]['loan_deduct_list_code']; ?>]"
                                       id="<?php echo $loan_deduct_list_even[$i]['loan_deduct_list_code']; ?>" value="0.00"  onkeyup="format_the_number_decimal(this);" onblur="cal_estimate_money()">
                            </div>
                        </div>
                        <?php
                            } elseif($loan_deduct_list_even[$i]['loan_deduct_list_code'] == "deduct_coop_saving") {
                                $has_deduct_coop_saving = 1;
                            }else{
                        ?>
                        <label class="g24-col-sm-4 control-label" for="deduct_insurance"><?php echo $j++ . ". " . $loan_deduct_list_even[$i]['loan_deduct_list']; ?></label>
                        <div class="g24-col-sm-2">
                            <div class="form-group">
                                <input class="form-control loan_deduct text-right" type="text" name="data[loan_deduct][deduct_insurance]" id="deduct_insurance" value="0.00"  onkeyup="format_the_number_decimal(this);" onblur="cal_estimate_money()"/>
                            </div>
                        </div>
                        <label class="g24-col-sm-2 text-center" style="margin: 0 -15px" for="left_insurance">ทุนเอาประกัน</label>
                        <div class="g24-col-sm-4">
                            <div class="form-group">
                                <input class="form-control text-right" type="text" value="0.00" name="data[coop_left_insurance]" id="left_insurance" onkeyup="format_the_number_decimal(this);">
                            </div>
                        </div>
                    <?php } ?>
                <?php }else{ ?>
                <?php } ?>
            </div>
            <?php
        }
        if(!empty($has_deduct_coop_saving)) {
            ?>
            <div class="g24-col-sm-24 modal_deduct_input" id="modal_deduct_input" data_coop_saving_deduct_index="<?php echo !empty($deduct_coop_savings) ? count($deduct_coop_savings) + 1 : 1;?>">
                <div class="row" id="coop_saving_deduct_row_1">
                    <label class="g24-col-sm-4 control-label"><?php echo $j++;?>. เงินฝากบัญชีภายใน</label>
                    <div class="g24-col-sm-5" >
                        <div class="form-group" class="coop_saving_deduct_div">
                            <select name="coop_saving_deduct_acc[]" id="coop_saving_deduct_acc_1" class="form-control">
                                <option value="">เลือกบัญชี</option>
                                <?php foreach($saving_accounts as $key => $value){ ?>
                                    <option value="<?php echo $value['account_id']; ?>"><?php echo $value['account_id']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <label class="g24-col-sm-2 text-center" style="margin: 0 -15px" for="left_insurance">จำนวน</label>
                    <div class="g24-col-sm-4">
                        <div class="form-group">
                            <input class="form-control text-right coop_saving_deduct" type="text" value="0.00" name="coop_saving_deduct_amt[]" id="coop_saving_deduct_amt_1" onkeyup="format_the_number_decimal(this);" onblur="cal_estimate_money()">
                        </div>
                    </div>
                    <div class="g24-col-sm-4">
                        <div class="form-group">
                            &nbsp;<button type="button" class="btn btn-primary" id="coop_saving_deduct_add">เพิ่ม</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php
            if(!empty($deduct_coop_savings)) {
                $deduct_coop_saving_index = 1;
                foreach($deduct_coop_savings as $deduct_coop_saving) {
                    $deduct_coop_saving_index++;
        ?>
            <div class="row" id="coop_saving_deduct_row_<?php echo $deduct_coop_saving_index;?>">
                <label class="g24-col-sm-4 control-label"></label>
                <div class="g24-col-sm-5" >
                    <div class="form-group" class="coop_saving_deduct_div">
                        <select name="coop_saving_deduct_acc[]" id="coop_saving_deduct_acc_<?php echo $deduct_coop_saving_index;?>" class="form-control">
                            <option value="">เลือกบัญชี</option>
                            <?php
                                if($saving_accounts) {
                                foreach($saving_accounts as $key => $value){ ?>
                                <option value="<?php echo $value['account_id']; ?>" <?php echo $value['account_id'] == $deduct_coop_saving['account_id'] ? 'selected' : ''?>><?php echo $value['account_id']; ?></option>
                            <?php }} ?>
                        </select>
                    </div>
                </div>
                <label class="g24-col-sm-2 text-center" style="margin: 0 -15px" for="left_insurance">จำนวน</label>
                <div class="g24-col-sm-4">
                    <div class="form-group">
                        <input class="form-control text-right coop_saving_deduct" type="text" value="<?php echo !empty($deduct_coop_saving['amount']) ? number_format($deduct_coop_saving['amount'], 2) : '0.00'?>" name="coop_saving_deduct_amt[]" id="coop_saving_deduct_amt_<?php echo $deduct_coop_saving_index;?>" onkeyup="format_the_number_decimal(this);" onblur="cal_estimate_money()">
                    </div>
                </div>
                <div class="g24-col-sm-4">
                    <div class="form-group">
                        &nbsp;<button type="button" class="btn btn-danger coop_saving_deduct_delete" data_index="<?php echo $deduct_coop_saving_index;?>" id="coop_saving_deduct_delete_<?php echo $deduct_coop_saving_index;?>">ลบ</button>
                    </div>
                </div>
            </div>
        <?php
                }
            }
        }
    }
    ?>
</div>
