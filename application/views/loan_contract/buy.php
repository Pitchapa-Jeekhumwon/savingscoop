<style type="text/css">
    .buy-list{
        padding-top: 25px;
    }
</style>
<div class="buy-list">
    <?php if(isset($loan_buy_list) && count($loan_buy_list) >= 1){ ?>
        <?php
        $j = 1;
        for ($i = 0; $i < round(count($loan_buy_list) / 2); $i++) {
            ?>
            <div class="g24-col-sm-24 modal_data_input">
                    <label class="g24-col-sm-4 control-label" <?php echo $loan_buy_list_odd[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : ''; ?>><?php echo $j++ . ". " . $loan_buy_list_odd[$i]['loan_deduct_list']; ?></label>
                    <div class="g24-col-sm-5" <?php echo $loan_buy_list_odd[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : ''; ?>>
                        <div class="form-group">
                            <input class="form-control loan_deduct" type="text" name="data[loan_deduct][<?php echo $loan_buy_list_odd[$i]['loan_deduct_list_code']; ?>]"
                                   id="<?php echo $loan_buy_list_odd[$i]['loan_deduct_list_code']; ?>" onkeyup="format_the_number_decimal(this);cal_estimate_money()">
                        </div>
                    </div>
                <?php if (@$loan_buy_list_even[$i]['loan_deduct_list'] != '') { ?>
                    <label class="g24-col-sm-4 control-label" <?php echo $loan_buy_list_even[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : ''; ?>><?php echo $j++ . ". " . $loan_buy_list_even[$i]['loan_deduct_list']; ?></label>
                    <div class="g24-col-sm-5" <?php echo $loan_buy_list_even[$i]['loan_deduct_show'] <> 1 ? ' style="display: none" ' : ''; ?>>
                        <div class="form-group">
                            <input class="form-control loan_deduct" type="text" name="data[loan_deduct][<?php echo $loan_buy_list_even[$i]['loan_deduct_list_code']; ?>]"
                                   id="<?php echo $loan_buy_list_even[$i]['loan_deduct_list_code']; ?>" onkeyup="format_the_number(this);cal_estimate_money();check_life_insurance();">
                        </div>
                    </div>
                <?php }else{ ?><div class="g24-col-sm-12">&nbsp;</div>
                <?php } ?>
            </div>
            <?php
        }
    }
    ?>
</div>
