<style type="text/css">
    .table-view > thead, .table-view > thead > tr > td, .table-view > thead > tr > th {
        font-size: 15px;
    }

    .spacing-table-view {
        width: 100px;
        text-align: right;
    }

    .spacing-value {
        width: 120px;
        text-align: right;
    }
</style>
<div style="width: 1400px" class="page-break">
    <div class="panel panel-body flex-box" style="height: 1000px;">
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: top;" class="text-right">
                    <a class="no_print" onclick="window.print();">
                        <button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print"
                                                                                            aria-hidden="true"></span>
                        </button>
                    </a>
                </td>
            </tr>
        </table>
        <table style="width: 100%;">
            <tr>
                <td>
                    <h3 class="title_view text-center"><?php echo @$_SESSION['COOP_NAME']; ?></h3>
                    <h3 class="title_view text-center">ทะเบียนหุ้น และบัญชีเงินกู้ ประเภทสหกรณ์ออมทรัพย์</h3>
                    <p>&nbsp;</p>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <h3 class="title_view">
                    </h3>
                </td>
            </tr>
        </table>
        <table style="width: 100%; font-size: 14px">
            <tr>
                <td style="text-align: right">เลขทะเบียนที่ <?php echo $row_member['member_id'] ?></td>
            </tr>
            <tr>
                <td style="text-align: right"><?php echo $row_member['fullname_th'] ?></td>
            </tr>
        </table>
        <table class="table table-view table-center">
            <thead>
            <tr>
                <th>วันที่</th>
                <th>มูลค่าที่หัก</th>
                <th>จำนวนหุ้นรวม</th>
                <th>มูลค่าหุ้นรวม</th>
                <th>หนังสือกู้ฉุกเฉิน</th>
                <th>วงเงินกู้</th>
                <th>งวดที่</th>
                <th>เงินต้น</th>
                <th>ดอกเบี้ย</th>
                <th>เงินกู้คงเหลือ</th>
                <th>หนังสือกู้สามัญ</th>
                <th>วงเงินกู้</th>
                <th>งวดที่</th>
                <th>เงินต้น</th>
                <th>ดอกเบี้ย</th>
                <th>เงินกู้คงเหลือ</th>
                <th>หนังสือกู้พิเศษ</th>
                <th>วงเงินกู้</th>
                <th>งวดที่</th>
                <th>เงินต้น</th>
                <th>ดอกเบี้ย</th>
                <th>เงินกู้คงเหลือ</th>
                <th>รวมเงิน</th>
                <th>หมายเหตุ</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1;
            foreach (@$container as $key => $lines) { ?>
                <?php foreach ($lines as $i => $line) { ?>
                        <tr>
                            <td><?php echo $this->center_function->ConvertToThaiDate($key, 1, 0, 0, 1); ?></td>
                            <?php if(!empty($line[0])){?>
                                <td><?php echo number_format($line[0]['total_amount'], 0); ?></td>
                                <td><?php echo number_format($line[0]['share_collect'], 0); ?></td>
                                <td><?php echo number_format($line[0]['share_collect_value'], 0); ?></td>
                            <?php }else{ ?>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php } ?>
                            <?php if(!empty($line[1])){?>
                                <td><?php echo $line[1]['contract_number'] == "" ? "" : $this->center_function->bbcoop_format_contract($line[1]['contract_number']); ?></td>
                                <td><?php echo $line[1]['loan_amount'] == "" ? "" : number_format($line[1]['loan_amount'], 2); ?></td>
                                <td><?php echo $line[1]['period_count'] == "" ? "" : $line[1]['period_count']; ?></td>
                                <td><?php echo $line[1]['principal_payment'] == "" ? "" : number_format($line[1]['principal_payment'], 2); ?></td>
                                <td><?php echo $line[1]['interest'] == "" ? "" : number_format($line[1]['interest'], 2); ?></td>
                                <td><?php echo $line[1]['loan_amount_balance'] == "" ? "" : number_format($line[1]['loan_amount_balance'], 2); ?></td>
                            <?php }else{ ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php } ?>

                            <?php if(!empty($line[2])){?>
                                <td><?php echo $line[2]['contract_number'] == "" ? "" : $this->center_function->bbcoop_format_contract($line[2]['contract_number']); ?></td>
                                <td><?php echo $line[2]['loan_amount'] == "" ? "" : number_format($line[2]['loan_amount'], 2); ?></td>
                                <td><?php echo $line[2]['period_count'] == "" ? "" : $line[2]['period_count']; ?></td>
                                <td><?php echo $line[2]['principal_payment'] == "" ? "" : number_format($line[2]['principal_payment'], 2); ?></td>
                                <td><?php echo $line[2]['interest'] == "" ? "" : number_format($line[2]['interest'], 2); ?></td>
                                <td><?php echo $line[2]['loan_amount_balance'] == "" ? "" : number_format($line[2]['loan_amount_balance'], 2); ?></td>
                            <?php }else{ ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php } ?>

                            <?php if(!empty($line[3])){?>
                                <td><?php echo $line[3]['contract_number'] == "" ? "" : $this->center_function->bbcoop_format_contract($line[3]['contract_number']); ?></td>
                                <td><?php echo $line[3]['loan_amount'] == "" ? "" : number_format($line[3]['loan_amount'], 2); ?></td>
                                <td><?php echo $line[3]['period_count'] == "" ? "" : $line[3]['period_count']; ?></td>
                                <td><?php echo $line[3]['principal_payment'] == "" ? "" : number_format($line[3]['principal_payment'], 2); ?></td>
                                <td><?php echo $line[3]['interest'] == "" ? "" : number_format($line[3]['interest'], 2); ?></td>
                                <td><?php echo $line[3]['loan_amount_balance'] == "" ? "" : number_format($line[3]['loan_amount_balance'], 2); ?></td>
                            <?php }else{ ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php } ?>
                            <td><?php echo $line['sumcount'] == "" ? "" : number_format($line['sumcount'], 2);?></td>
                            <td></td>
                        </tr>
                <?php } ?>
                <?php $i++;
            } ?>
            </tbody>
        </table>
    </div>
</div>