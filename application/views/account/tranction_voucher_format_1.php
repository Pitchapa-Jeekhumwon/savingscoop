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
            .control-label{
                text-align:right;
                padding-top:5px;
            }
            .text_left{
                text-align:left;
            }
            .text_right{
                text-align:right;
            }
        </style>

        <h1 style="margin-bottom: 0">บัญชีรายวัน</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
        </div>
        <div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
            <div class="panel panel-body" style="padding-top:0px !important;">
                <h3 >บัญชีรายวัน</h3>
                <form method="GET" action="">
                    <div class="g24-col-sm-24">
                        <label class="g24-col-sm-3 control-label datepicker1" for="approve_date">เลือกวันที่บันทึกบัญชี</label>
                        <div class="input-with-icon g24-col-sm-3">
                            <div class="form-group">
                                <input id="approve_date" name="approve_date" class="form-control m-b-1 form_date_picker" type="text" value="<?php echo (@$_GET['approve_date'] != '')?@$_GET['approve_date']:''; ?>" data-date-language="th-th" autocomplete="off">
                                <span class="icon icon-calendar input-icon m-f-1"></span>
                            </div>
                        </div>
                        <div class="g24-col-sm-1">
                            <input type="submit" class="btn btn-primary" value="ค้นหา">
                        </div>
                    </div>
                </form>
                <div class="g24-col-sm-24 text-right">
                    <input type="button" class="btn btn-primary" id="print-select" value="พิมพ์">
                </div>
            </div>
        </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="font-normal" width="5%"><input type="checkbox" id="check_all"></th>
                                <th class="font-normal" width="15%">วันที่</th>
                                <th class="font-normal"> รายการ </th>
                                <th class="font-normal" width="15%">รหัสบัญชี</th>
                                <th class="font-normal" width="15%">เดบิต</th>
                                <th class="font-normal" width="15%">เครดิต</th>
                                <th class="font-normal" width="20%">transaction</th>
                            </tr>
                            </thead>
                            <tbody>
                            <form action="<?php echo base_url(PROJECTPATH.'/account/account_pdf_tranction_voucher'); ?>" method="post" id="print_form" target="blank">
                                <input type="hidden" id="account_date" name="date" value="<?php echo !empty($approve_date) ? $approve_date : date('Y-m-d');?>"/>
                            <?php
                            $k_count=1;
                            $i=1;
                            foreach($data_account_detail as $key_main => $row) {
                                foreach($row as $key => $row) {
                                    $i=1;
                                    $description = "";
                                    foreach($row as $key2 => $row_detail){
                            ?>
                                        <tr>
                                            <td><?php if($i == 1) { ?><input type="checkbox" class="check_item" name="account_detail_ids[]" id="account_detail_id_<?php echo $key;?>" value="<?php echo $key;?>"><?php } ?></td>
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
                                        $description = $row_detail["account_description"];
                                        $i++;
                                    }
                            ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="text_left"><?php echo $description;?></td>
                                        <td></td>
                                        <td class="text_right" colspan="3">
                                            <button name="bt_add" id="bt_add" type="button" class="btn btn-primary" style="width:unset;" onclick="account_pdf_tranction_voucher('<?php echo  $key ?>','<?php echo  $key_main ?>')" >
                                                <span>transaction pdf</span>
                                            </button>
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
                            </form>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-body">
    <form action="<?php echo base_url(PROJECTPATH.'/account/account_pdf_tranction_voucher'); ?>" method="post" id="from_pdf_day" target="_blank">
        <input id="detail_pdf" name="account_detail_ids[]" type="hidden" value="">
        <input id="date_pdf" name="date" type="hidden" value="">
    </form>
    <form action="<?php echo base_url(PROJECTPATH.'/account/account_excel_tranction_voucher'); ?>" method="post" id="from_excel_day">
        <input id="detail" name="detail" type="hidden" value="">
        <input id="date" name="date" type="hidden" value="">
    </form>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/account.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
<script>
    $(document).ready(function() {
        $("#check_all").change(function() {
            var status = $(this).is(":checked") ? true : false;
            $(".check_item").prop("checked",status);
        });
        $(".check_item").change(function() {
            var status = $(this).is(":checked") ? true : false;
            if(!status)$("#check_all").prop("checked",false);
        });
        $("#print-select").click(function() {
            $("#print_form").submit();
        });
    });
</script>