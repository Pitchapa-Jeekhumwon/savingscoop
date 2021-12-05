<?php
//foreach ($datas as $loan_date => $pages) {
$sum_money_pay_total = 0;
$num_loan_all_total = 0;
$count_pages = count($pages);
$count_pages -= 1;
    foreach ($pages as $page => $data){
        $txt_loan_date = $this->center_function->ConvertToThaiDate($loan_date,'0');
        ?>
        <div style="width: 1500px;" class="page-break">
            <div class="panel panel-body" style="padding-top:20px !important;height: 100%;min-height: 1000px;position:relative;">
                <table style="width: 100%;">
                    <tr>
                        <?php if(@$_GET['download']==""){ ?>
                            <td style="width:100px;vertical-align: top;">
                            </td>
                        <?php } ?>
                        <td class="text-center" style="text-align: center;" <?php echo @$_GET['download']!=""? "colspan='17'":"colspan='2'"?>>
                            <h3 class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>">ทะเบียนจ่ายเช็ค <?php echo @$_SESSION['COOP_NAME'];?></h3>
                            <br>
                        </td>
                        <?php if(@$_GET['download']==""){ ?>
                            <td style="width:100px;vertical-align: top;" class="text-right">
                            </td>
                        <?php } ?>
                    </tr>
                </table>
                <br>
                <table class="table table-view table-center">
                    <thead>
                    <tr>
                        <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">ลำดับ</th>
                        <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">วัน เดือน ปี</th>
                        <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">เช็คเล่มที่</th>
                        <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">เช็คเลขที่</th>
                        <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">จ่ายให้ใคร</th>
                        <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">ค่าอะไร</th>
                        <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">จำนวนเงิน</th>
                        <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">เซ็นชื่อผู้รับเช็ค  </th>
                        <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">วันที่รับเช็ค</th>
                        <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">หมายเหตุ</th>
                    </tr>

                    </thead>
                    <tbody>
                    <?php
                    foreach ($data as $key => $value) {
                        $i=0;
                        $sum_money_pay = $value['loan_amount'] - $value['pay_amount'] - $value['loan_deduct_amount'] - $value['interest_amount'];
                        $sum_money_pay_total += $sum_money_pay;
                        $num_loan_all_total++;
                        foreach ($value['guarantee']['person_id'] as $order => $guarantee) {
                            $i++;
                            $date_start_period = $this->center_function->ConvertToThaiDateMMYY($value['date_start_period']);
                            $date_start_period = '';
                            $max_date_period = $this->center_function->ConvertToThaiDateMMYY($value['max_date_period']);
                            $show_date = $this->center_function->mydate2date($loan_date);
                            if($i == '1'){
                                ?>
                                <tr>
                                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"><?php echo ($page*10)+$key+1; ?></td>
                                    <td class="<?php echo @$_GET['download']!=""? "table_body":"text-left"?>"><?php echo $txt_loan_date; ?></td>
                                    <td class="<?php echo @$_GET['download']!=""? "table_body":"text-left"?>"><?php  ?></td>
                                    <td class="<?php echo @$_GET['download']!=""? "table_body":"text-left"?>"><?php  ?></td>
                                    <td class="<?php echo @$_GET['download']!=""? "table_body":"text-left"?>"><?php echo $value['full_name']; ?></td>
                                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>" style="mso-number-format:'@';"><?php echo @$loan_type[$value['loan_type_id']]; ?></td>
                                    <td class="<?php echo @$_GET['download']!=""? "table_body_right":"text-right"?>"><?php echo number_format($sum_money_pay, 2); ?></td>
                                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":"text-right"?>"><?php ?></td>
                                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":"text-right"?>"><?php ?></td>
                                    <td class="<?php echo @$_GET['download']!=""? "table_body":"text-right"?>"><?php ?></td>
                                </tr>
                            <?php }
                        }
                    } ?>
                    <?php if($count_pages == $page){?>
                    <tr style="background: #eee">
                        <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
                        <td class="<?php echo @$_GET['download']!=""? "table_body_right":"text-right"?>"><?php ?></td>
                        <td class="<?php echo @$_GET['download']!=""? "table_body":"text-right"?>"><?php ?></td>
                        <td class="<?php echo @$_GET['download']!=""? "table_body":"text-right"?>"><?php ?></td>
                        <td class="<?php echo @$_GET['download']!=""? "table_body":"text-right"?>"><?php ?></td>
                        <td class="<?php echo @$_GET['download']!=""? "table_body":"text-right"?>"></td>
                        <td class="<?php echo @$_GET['download']!=""? "table_body":"text-right"?>"><?php echo number_format($sum_money_pay_total, 2);?></td>
                        <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
                        <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
                        <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
                    </tr>
                    <?php } ?>

                    </tbody>
                </table>
                <div class="bottom">
                    <table style="width: 100%;">
                        <tbody>
                        <?php
                        $draw_line = '';
                        for ($i=0;$i<95;$i++){
                            $draw_line .='&nbsp;';
                        }
                        ?>
                        <tr>
                            <td class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>" style="text-align: center;">
                                <div style="text-decoration: underline;">
                                    <?php echo $draw_line;?>
                                </div>
                            </td>
                            <td class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>" style="text-align: center; font-size: 14px;">
                                ผู้พิมพ์เช็ค
                            </td>
                            <td class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>" style="text-align: center;">
                                <div style="text-decoration: underline;">
                                    <?php echo $draw_line;?>
                                </div>
                            </td>
                            <td class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>" style="text-align: center; font-size: 14px;">
                                ผู้สั่งจ่ายเช็คคนที่หนึ่ง
                            </td>
                            <td class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>" style="text-align: center;">
                                <div style="text-decoration: underline;">
                                    <?php echo $draw_line;?>
                                </div>
                            </td>
                            <td class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>" style="text-align: center; font-size: 14px;">
                                ผู้สั่งจ่ายเช็คคนที่สอง
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;" valign="bottom" colspan="" height="30 px">
                                ( ............................................................................................... )
                            </td>
                            <td></td>
                            <td style="text-align: center;" valign="bottom" colspan="">
                                ( ............................................................................................... )
                            </td>
                            <td></td>
                            <td style="text-align: center" valign="bottom" colspan="">
                                ( ............................................................................................... )
                            </td>
                            <td></td>
                        </tr>


                        <tr>
                            <td style="text-align: center;" height="40" valign="bottom">
                                <div style="text-decoration: underline;">
                                    <?php echo $draw_line?>
                                </div>
                            </td>
                            <td class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>" style="text-align: center;  font-size: 14px;" valign="bottom">
                                ผู้จ่ายเช็ค
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;" valign="bottom" colspan="" height="30 px">
                                ( ............................................................................................... )
                            </td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php }
//} ?>