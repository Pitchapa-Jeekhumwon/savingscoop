<?php
if(@$_GET['download']!="") {
    header("Content-type: application/vnd.ms-excel;charset=utf-8;");
    header("Content-Disposition: attachment; filename=export.xls");
    date_default_timezone_set('Asia/Bangkok');
}

$param = '';
if(!empty($_GET)){
    foreach(@$_GET as $key => $value){
        if(is_array($value[$_GET['report']])){
            $param .= $key.'=';
            foreach ($value[$_GET['report']] as $k => $v) {
                $param .= $v;
                if($k < (sizeof($value[$_GET['report']])-1)){
                    $param .= ',';
                }
            }
            $param .= '&';
        }else{
            $param .= $key.'='.$value.'&';
        }
    }
}
$i = 1;
?>
    <!--			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />-->
    <style>
        .table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
            font-size: 20px;
        }
        .table-view>tbody, .table-view>tbody>tr>td, .table-view>tbody>tr>th {
            font-size: 14px;
        }
        .table {
            color: #000;
        }
        .num {
            mso-number-format:General;
        }
        .text{
            mso-number-format:"\@";/*force text*/
        }
        .text-center{
            text-align: center;
        }
        .text-left{
            text-align: left;
        }
        .table_title{
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 22px;
            font-weight: bold;
            text-align:center;
        }
        .table_header_top{
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 19px;
            font-weight: bold;
            text-align:center;
            border-top: thin solid black;
            border-left: thin solid black;
            border-right: thin solid black;
        }
        .table_header_mid{
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 19px;
            font-weight: bold;
            text-align:center;
            border-left: thin solid black;
            border-right: thin solid black;
        }
        .table_header_bot{
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 19px;
            font-weight: bold;
            text-align:center;
            border-bottom: thin solid black;
            border-left: thin solid black;
            border-right: thin solid black;
        }
        .table_header_bot2{
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 19px;
            font-weight: bold;
            text-align:center;
            border: thin solid black;
        }
        .table_body{
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 21px;
            border: thin solid black;
        }
        .table_body_center{
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 21px;
            border: thin solid black;
            text-align:center;
        }
        .table_body_right{
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 21px;
            border: thin solid black;
            text-align:right;
        }
        .bottom {
            position:absolute; /* ตำแหน่งเทียบกับ container */
            bottom:0; /* กำหนดให้ชิดด้านล่าง */

            text-align:center; /* 2 บรรทัดนี้ถ้าต้องการให้ข้อความอยู่กลาง */
            width:98%;

            /*border:1px solid green; !* กำหนดกรอบให้เห็นภาพ *!*/
        }

        /*table tr td{*/
        /*border: 1px solid black;*/
        /*}*/
    </style>
<?php //$this->load->view('breadcrumb');
//echo '<pre>';
//print_r($datas);exit;
if(true){
    $txt_loan_type = str_replace('เงินกู้','',$loan_type[$loan_type_id]);
    foreach ($datas as $loan_date => $pages) {
        $loan_amount_total = 0;
        $pay_amount_total = 0;
        $loan_deduct_amount_total = 0;
        $interest_amount_total = 0;
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
                                    <!--                            <img src="--><?php //echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?><!--" alt="Logo" style="height: 80px;" />-->
                                </td>
                            <?php } ?>
                            <td class="text-center" style="text-align: center;" <?php echo @$_GET['download']!=""? "colspan='17'":"colspan='2'"?>>
                                <h3 class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>">สรุปรายละเอียดการขอกู้<?php echo @$txt_loan_type.@$_SESSION['COOP_NAME'];?></h3>
                                <h3 class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>">ประจำวันที่ <?php echo $txt_loan_date; ?></h3>
                            </td>
                            <?php if(@$_GET['download']==""){ ?>
                                <td style="width:100px;vertical-align: top;" class="text-right">
                                    <?php if($i == '1'){?>
                                        <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
<!--                                        <a class="no_print" target="_blank" onclick="goto()">-->
<!--                                            <button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>-->
<!--                                        </a>-->
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>
                    </table>
                    <br>
                    <table class="table table-view table-center">
                        <thead>
                        <tr>
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">ลำดับ</th>
<!--                            <th class="--><?php //echo @$_GET['download']!=""? "table_header_top":""?><!--" rowspan="2">เช็คเล่มที่</th>-->
<!--                            <th class="--><?php //echo @$_GET['download']!=""? "table_header_top":""?><!--" rowspan="2">เช็คเลขที่</th>-->
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">รายชื่อผู้กู้</th>
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">เลขที่สมาชิก</th>
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">สัญญาเงินกู้<?php echo $txt_loan_type;?>เลขที่</th>
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">วงเงินกู้ที่อนมัติ</th>
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" colspan="3">รายการหัก</th>
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">เงินที่ต้องจ่าย</th>
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" colspan="2">การออกใบสร็จรับเงิน</th>
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">หมายเหตุ</th>
                        </tr>
                        <tr>
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">หนี้เดิม</th>
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">ค่าธรรมเนียม</th>
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">ดอกเบี้ย (*)</th>
<!--                            <th class="--><?php //echo @$_GET['download']!=""? "table_header_top":""?><!--">ดอกเบี้ย (จนท.)</th>-->
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">เล่มที่</th>
                            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">เลขที่</th>
                        </tr>

                        </thead>
                        <tbody>
                        <?php
                        foreach ($data as $key => $value) {
                            $i=0;
                            $loan_amount_total += $value['loan_amount'];
                            $pay_amount_total += $value['pay_amount'];
                            $loan_deduct_amount_total += $value['loan_deduct_amount'];
                            $interest_amount_total += $value['interest_amount'];
                            $sum_money_pay  = $value['loan_amount'] - $value['pay_amount'] - $value['loan_deduct_amount'] - $value['interest_amount'];
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
<!--                                        <td class="--><?php //echo @$_GET['download']!=""? "table_body_center":""?><!--">--><?php //?><!--</td>-->
<!--                                        <td class="--><?php //echo @$_GET['download']!=""? "table_body_center":""?><!--">--><?php //?><!--</td>-->
                                        <td class="<?php echo @$_GET['download']!=""? "table_body":"text-left"?>"><?php echo $value['full_name']; ?></td>
                                        <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>" style="mso-number-format:'@';"><?php echo $value['member_id']; ?></td>
                                        <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"><?php echo $value['contract_number']; ?></td>
                                        <!--                                    <td class="--><?php //echo @$_GET['download']!=""? "table_body_right":""?><!--">--><?php //echo number_format($value['salary'], 2); ?><!--</td>-->
                                        <td class="<?php echo @$_GET['download']!=""? "table_body_right":"text-right"?>"><?php echo number_format($value['loan_amount'], 2); ?></td>
                                        <td class="<?php echo @$_GET['download']!=""? "table_body_center":"text-right"?>"><?php echo number_format($value['pay_amount'], 2); ?></td>
                                        <td class="<?php echo @$_GET['download']!=""? "table_body_right":"text-right"?>"><?php echo number_format($value['loan_deduct_amount'], 2);?></td>
                                        <td class="<?php echo @$_GET['download']!=""? "table_body_center":"text-right"?>"><?php echo number_format($value['interest_amount'], 2);?></td>
<!--                                        <td class="--><?php //echo @$_GET['download']!=""? "table_body_center":"text-right"?><!--">--><?php // ?><!--</td>-->
                                        <td class="<?php echo @$_GET['download']!=""? "table_body":"text-right"?>"><?php echo number_format($sum_money_pay, 2); ?></td>
                                        <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"><?php  ?></td>
                                        <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"><?php  echo $value['deduct_receipt_id'];?></td>
                                        <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"><?php  ?></td>
                                    </tr>
                                <?php }
                            }
                        } ?>
                        <?php if($count_pages == $page){?>
                        <tr style="background: #eee">
                            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
<!--                            <td class="--><?php //echo @$_GET['download']!=""? "table_body":""?><!--"></td>-->
<!--                            <td class="--><?php //echo @$_GET['download']!=""? "table_body_right":""?><!--"></td>-->
                            <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"></td>
                            <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"></td>
                            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
                            <td class="<?php echo @$_GET['download']!=""? "table_body_right":"text-right"?>"><?php echo number_format($loan_amount_total, 2);?></td>
                            <td class="<?php echo @$_GET['download']!=""? "table_body":"text-right"?>"><?php echo number_format($pay_amount_total, 2);?></td>
                            <td class="<?php echo @$_GET['download']!=""? "table_body":"text-right"?>"><?php echo number_format($loan_deduct_amount_total, 2);?></td>
                            <td class="<?php echo @$_GET['download']!=""? "table_body":"text-right"?>"><?php echo number_format($interest_amount_total, 2);?></td>
<!--                            <td class="--><?php //echo @$_GET['download']!=""? "table_body":"text-right"?><!--"></td>-->
                            <td class="<?php echo @$_GET['download']!=""? "table_body":"text-right"?>"><?php echo number_format($sum_money_pay_total, 2);?></td>
                            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
                            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
                            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" colspan="1"></td>
                        </tr>
                        <?php } ?>

                        </tbody>
                    </table>
                    <div class="bottom">
                        <table style="width: 100%;">
                            <thead>
                            <tr>

                                <th class="text-center" style="text-align: center;height: 50px" <?php echo @$_GET['download']!=""? "colspan='17'":"colspan='3'"?> colspan="2">
                                    <h4 class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>" style="font-size: 24px;"> จึงเรียนมาเพื่อโปรดอนุมัติ และลงนามในเช็คที่แนบมาพร้อมนี้</h4>
                                </th>
                            </tr>
                            </thead>
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
                                        <?php echo $draw_line?>
                                    </div>
                                </td>
                                <td width="100 px" height="20 px"></td>
                                <td style="text-align: center;">
                                    <div style="text-decoration: underline;">
                                        <?php echo $draw_line?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;" valign="bottom">
                                    ( ............................................................................................... )
                                </td>
                                <td height="30 px"></td>
                                <td style="text-align: center" valign="bottom">
                                    ( ............................................................................................... )
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center; font-size: 14px;"  height="40 px;">
                                    เจ้าหน้าที่สหกรณ์ฯ
                                </td>
                                <td></td>
                                <td style="text-align: center; font-size: 14px;">
                                    ผู้ช่วยผู้จัดการสหกรณ์ฯ
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center; font-size: 14px;" height="50 px; font-size: 16px;">
                                    ลงนามแล้ว
                                </td>
                                <td></td>
                                <td style="text-align: center; font-size: 14px;">
                                    อนุมัติและลงนามแล้ว
                                </td>
                            </tr>


                            <tr>
                                <td style="text-align: center;">
                                    <div style="text-decoration: underline;">
                                        <?php echo $draw_line?>
                                    </div>
                                </td>
                                <td height="20 px"></td>
                                <td style="text-align: center;">
                                    <div style="text-decoration: underline;">
                                        <?php echo $draw_line?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;" valign="bottom">
                                    ( ............................................................................................... )
                                </td>
                                <td height="30 px"></td>
                                <td style="text-align: center" valign="bottom">
                                    ( ............................................................................................... )
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;  font-size: 14px;" height="30 px">
                                    กรรมการสหกรณ์ฯ
                                </td>
                                <td></td>
                                <td style="text-align: center;  font-size: 14px;">
                                    กรรมการและผู้จัดการสหกรณ์ฯ
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
        }

        $get_data['pages']= $pages;
        $get_data['loan_date']= $loan_date;
//        $get_data['data']= $data;
//        $get_data['txt_loan_date']= $txt_loan_date;
//        $get_data['page']= $page;
        $this->load->view('report_summary_loan_detail/bbcoop_payment_cash_check_preview',$get_data);
    }
}?>

<?php //$this->load->view('report_summary_loan_detail/bbcoop_payment_cash_check_preview');?>

<?php if(@$_GET['download']==""){ ?>
    <script>
        function goto(){
            // console.log(window.location.href );
            window.open(window.location.href+'&download=1');
        }
    </script>
<?php } ?>