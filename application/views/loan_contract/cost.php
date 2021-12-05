<style type="text/css">
    .cost-list{
        padding-top: 25px;
    }
</style>
<div class="cost-list">
<?php if(isset($outgoings) && count($outgoings) >= 1){
         //ceil(count($outgoings)+count($outgoings)%2)
    for($row = 0; $row <= ceil(count($outgoings)); $row+=2 ){ ?>
        <?php if($outgoings[$row]['outgoing_code'] == '') continue; ?>
        <div class="g24-col-sm-24 modal_data_input">
            <label class="g24-col-sm-6 control-label " <?php echo $outgoings[$row]['outgoing_show'] <> 1 ? 'style="display: none;"' : ''; ?>><?php echo $outgoings[$row]['outgoing_name']; ?></label>
            <div class="g24-col-sm-5" <?php echo $outgoings[$row]['outgoing_show'] <> 1 ? 'style="display: none;"' : ''; ?>>
                <input class="form-control maxlength_number_decimal loan_cost " id="<?php echo $outgoings[$row]['outgoing_code']; ?>" data-key="<?php echo $outgoings[$row]['outgoing_code']; ?>" type="text" name="data[coop_loan_cost][<?php echo $outgoings[$row]['outgoing_code']; ?>]" value="" onkeyup="format_the_number_decimal(this);" onblur="check_period();calcCost()">
            </div>
            <label class="g24-col-sm-4 control-label " <?php echo $outgoings[$row+1]['outgoing_show'] <> 1 ? 'style="display: none;"' : ''; ?>><?php echo $outgoings[$row+1]['outgoing_name']; ?></label>
            <div class="g24-col-sm-5" <?php echo $outgoings[$row+1]['outgoing_show'] <> 1 ? 'style="display: none;"' : ''; ?>>
                <input class="form-control maxlength_number_decimal loan_cost " id="<?php echo $outgoings[$row+1]['outgoing_code']; ?>" data-key="<?php echo $outgoings[$row+1]['outgoing_code']; ?>" type="text" name="data[coop_loan_cost][<?php echo $outgoings[$row+1]['outgoing_code']; ?>]" value="" onkeyup="format_the_number_decimal(this);" onblur="check_period();calcCost()">
            </div>
        </div>
    <?php
    } ?>
<?php } ?>
</div>
