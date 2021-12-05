<style type="text/css">
    .contract > thead > tr > th {
        text-align: center;
    }
</style>
<div class="table-responsive">
    <table class="table contract">
        <thead>
        <tr>
            <th>#</th>
            <th>วันที่ทำรายการ</th>
            <th>เลขที่คำร้อง/สัญญา</th>
            <th>ยอดเงินกู้</th>
            <th>หักกลบ</th>
            <th>จำนวนที่ได้รับ</th>
            <th>ดอกเบี้ย</th>
            <th>ผู้ทำรายการ</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($contract) && isset($atm_contract)){?>
            <?php $num = 1; foreach ($contract  as $key => $val){ ?>
                <tr>
                    <td class="text-center"><? echo $num++; ?></td>
                    <td class="text-center"><?php echo $this->center_function->mydate2date($val['approve_date']); ?></td>
                    <td class="text-center"><?php echo $val['petition_number'].'/'.$val['contract_number'] ?></td>
                    <td class="text-right"><?php echo number_format($val['loan_amount'], 2); ?></td>
                    <td class="text-center">
                        <div class="checkbox">
                            <label style="font-size: 1.3em">
                                <input type="checkbox" class="previous-deduct" data-type="<?php echo $val['loan_type'];?>" value="<?php echo $val['id']?>" >
                                <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                            </label>
                        </div>
                    </td>
                    <td class="text-right" >
                        <input type="text" id="contract_balance_input_<?php echo $val['id']?>" data_id="<?php echo $val['id']?>" value="<?php echo number_format($val['loan_amount_balance'], 2) ?>" class="form-control text-right loan_balance_input" onblur="number_format(this)">
                    </td>
                    <td class="text-right" id="contract_interest_<?php echo $val['id']?>">
                        <input type="text" id="contract_interest_input_<?php echo $val['id']?>" data_id="<?php echo $val['id']?>" value="<?php echo number_format(0, 2) ?>" class="form-control text-right loan_interest_input" onblur="number_format(this)">
                    </td>
                    <td class="text-center"><?php echo $val['admin_id'] == "" ? 'N/A' : $val['admin_id'] ?></td>
                </tr>
            <?php } ?>
            <?php foreach ($atm_contract as $key => $val ){ ?>
                <tr>
                    <td class="text-center"><? echo $num++; ?></td>
                    <td class="text-center"><?php echo $this->center_function->mydate2date($val['approve_date']); ?></td>
                    <td class="text-center"><?php echo $val['petition_number'].'/'.$val['contract_number'] ?></td>
                    <td class="text-right"><?php echo number_format($val['loan_amount'], 2); ?></td>
                    <td class="text-center">
                        <div class="checkbox">
                            <label style="font-size: 1.3em">
                                <input type="checkbox" class="previous-deduct" data-type="<?php echo $val['loan_type'];?>" value="<?php echo $val['loan_atm_id']?>" >
                                <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                            </label>
                        </div>
                    </td>
                    <td class="text-right"><?php echo number_format($val['loan_amount_balance'], 2) ?></td>
                    <td class="text-right" id="contract_interest_<?=$val['loan_atm_id']?>" ><?php echo number_format(0, 2) ?></td>
                    <td class="text-center" ><?php echo $val['admin_id'] == "" ? 'N/A' : $val['admin_id'] ?></td>
                </tr>
            <?php } ?>
        <?php }else{ ?>
            <tr>
                <td colspan="7" class="text-center">ไม่พบข้อมูลการกู้เงิน</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <input type="hidden" value="">
</div>