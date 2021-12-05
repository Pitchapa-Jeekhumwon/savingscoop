<?php
    if(@$_GET['download']){
		header("Content-type: application/vnd.ms-excel;charset=utf-8;");
		header("Content-Disposition: attachment; filename=รายงานรับสมัครสมาชิก.xls"); 
		date_default_timezone_set('Asia/Bangkok');
    }
?>
<style>
			.header{
				font-family: AngsanaUPC, MS Sans Serif;
				font-size: 12px;
				font-weight: bold;
				text-align:center;
                color: #000;  
			}
			.header_h3{
				font-family: AngsanaUPC, MS Sans Serif;
				font-size: 16px;
				font-weight: bold;
				text-align:center;
                color: #000;  
			}
			.table {
				color: #000;
			}
            .footer{
                text-align: left;
                color: #000;
                padding-left: 1.8em;
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
			.text-right{
				text-align: right;
			}
			.table_title{
				font-family: AngsanaUPC, MS Sans Serif;
				font-size: 14px;
				font-weight: bold;
				text-align:center;
                color: #000;  
			}
			.table_title_right{
				font-family: AngsanaUPC, MS Sans Serif;
				font-size: 16px;
				font-weight: bold;
				text-align:right;
                color: #000;
                background-color: #FFF;
			}
			.table_header_top{
				font-family: AngsanaUPC, MS Sans Serif;
				font-size: 14px;
				font-weight: bold;
				text-align:center;
				border-top: thin solid black;
				border-left: thin solid black;
				border-right: thin solid black;
                color: #000;
                background-color: #FFF;
			}
			.table_header_mid{
				font-family: AngsanaUPC, MS Sans Serif;
				font-size: 19px;
				font-weight: bold;
				text-align:center;
				border-left: thin solid black;
				border-right: thin solid black;
                color: #000;
                background-color: #FFF;
			}
			.table_header_bot{
				font-family: AngsanaUPC, MS Sans Serif;
				font-size: 19px;
				font-weight: bold;
				text-align:center;
				border-bottom: thin solid black;
				border-left: thin solid black;
				border-right: thin solid black;
                color: #000;
                background-color: #FFF;
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
				font-size: 14px;
				border: thin solid black;
                color: #000;
                background-color: #FFF;
			}
			.table_body_right{
				font-family: AngsanaUPC, MS Sans Serif;
				font-size: 21px;
				border: thin solid black;
				text-align:right;
                color: #000;
                background-color: #FFF;
			}
			.title_header{
				color: #000;
				font-size: 21px;	
			}
		</style>
<?php 
    // foreach($datas as $page=>$data) {
?>
		<div style="width: 1000px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;height: 1420px;">
                <?php if($page = '1'){?>
                    <table style="width: 100%;">
						<tr>
						<?php if(@$_GET['download']!='excel'){ ?>
                            <td colspan="11" style="width:100px;vertical-align: top;" class="text-right">
                                <?php
                                    $get_param = '?';
                                    foreach(@$_GET as $key => $value){
                                            $get_param .= $key.'='.$value.'&';
                                    }
                                    $get_param = substr($get_param,0,-1);
                                ?>
                                <?php if(!@$_GET['download']){ ?>
                                    <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                                    <a href="<?php echo base_url(PROJECTPATH.'/report_refund_average/report_summarize_excel'.$get_param."&download=excel"); ?>" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>	
                                <?php } ?>
                            </td>
						<?php } ?>
						</tr>
                        <tr>
							<td colspan="8" width='400px' class="header"></td>
							<td class="text-right" width='200px' style="background-color: #FFF;">
								<span class="<?php echo(@$_GET['download']=='excel')?"header":"title_header";?>" style="text-align: right;">วันที่พิมพ์รายงาน:</span>
							</td>
                            <td class="text-right" width='100px' colspan="2" style="background-color: #FFF;mso-number-format:'@';">
                                <span class="<?php echo(@$_GET['download']=='excel')?"header":"title_header";?>" style="text-align: right;"><?php echo date("d/m/");echo date("Y",strtotime(date("Y/m/d")))+543 ; echo ' '.date("h:i:s");?></span>
							</td>
						</tr>
						<tr>
							<td colspan="8"></td>
							<td class="text-right" style="background-color: #FFF;">
                                <span class="<?php echo(@$_GET['download']=='excel')?"header":"title_header";?>" style="text-align: right;">หน้า:</span>
							</td>
                            <td class="text-right" colspan="2" style="background-color: #FFF;mso-number-format:'@';">
                                <span class="<?php echo(@$_GET['download']=='excel')?"header":"title_header";?>" style="text-align: right;">1/1</span>
							</td>
						</tr>
						<tr>
							<td class="text-center" colspan="11" style="background-color: #FFF;">
							    <!-- <h3 class="<?php echo(@$_GET['download']=='excel')? "header":"title_header";?>"><?php echo @$_SESSION['COOP_NAME'];?></h3> -->
                                <span class="<?php echo(@$_GET['download']=='excel')?"header_h3":"title_header";?>"> สรุปยอด จำนวนเงิน จำนวนราย ดอกเบี้ยเงินกู้และเงินเฉลี่ยคืนที่คำนวณได้ในปี <?php echo @$_GET['year'];?></span>
                                
                                
                            </td>
                        </tr>
                        <tr>
                        </tr> 
                    </table>
                <?php } ?>
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th class="<?php echo(@$_GET['download']=='excel')?"table_header_top":"title_view";?>" style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th colspan="5" class="<?php echo(@$_GET['download']=='excel')?"table_header_top":"title_view";?>" style="width: 750px;vertical-align: middle;">รายการ</th>
							<th colspan="1" class="<?php echo(@$_GET['download']=='excel')?"table_header_top":"title_view";?>" style="width: 150px;vertical-align: middle;">จำนวนคน</th> 
							<th colspan="2" class="<?php echo(@$_GET['download']=='excel')?"table_header_top":"title_view";?>" style="width: 305px;vertical-align: middle;">จำนวนหุ้น(รวม)</th>
							<th colspan="2" class="<?php echo(@$_GET['download']=='excel')?"table_header_top":"title_view";?>" style="width: 305px;vertical-align: middle;">จำนวนเงินปันผล(รวม) </th>
						</tr> 
					</thead>
					<tbody>
					    <?php
                            foreach($dividend_arr as $row) {
						?>
                        <tr> 
                            <td class="<?php echo(@$_GET['download']=='excel')?"table_body":"title_view";?>" style="text-align: center;"><?php echo ++$j;?></td>
                            <td colspan="5" class="<?php echo(@$_GET['download']=='excel')?"table_body":"title_view";?>" style="text-align: left;">
							<?php 
								if($row['dividend_drop']=='0'){
									echo 'สมาชิกที่มีสิทธิ์ได้รับเงินปันผล';
								}else if($row['dividend_drop']=='1'){
									echo 'สมาชิกที่ไม่มีสิทธิ์ได้รับเงินปันผล';
								}else if($row['dividend_drop']=='2'){
									echo 'สมาชิกที่มีสิทธิ์ได้รับเงินปันผลบางส่วน';
								}else if($row['dividend_drop']=='3'){
									echo 'สมาชิกที่ลาออกระหว่างปี';
								}
							?></td>
							<td colspan="1" class="<?php echo(@$_GET['download']=='excel')?"table_body":"title_view";?>" style="text-align: right;"><?php echo number_format($row['count_member_id'], 0, '.', ',').' คน'; ?></td>
                            <td colspan="2" class="<?php echo(@$_GET['download']=='excel')?"table_body":"title_view";?>" style="text-align: right;"><?php echo number_format($row['share_value_sum'], 0, '.', ',').' หุ้น'; ?></td>
							<td colspan="2" class="<?php echo(@$_GET['download']=='excel')?"table_body":"title_view";?>" style="text-align: right;"><?php echo number_format($row['dividend_value_sum'], 2, '.', ',').' บาท'; ?></td>
						</tr>
                        <?php 
                            }
                        ?>					
					</tbody>
					<thead> 
						<tr>
							<th class="<?php echo(@$_GET['download']=='excel')?"table_header_top":"title_view";?>" style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th colspan="5" class="<?php echo(@$_GET['download']=='excel')?"table_header_top":"title_view";?>" style="width: 750px;vertical-align: middle;">รายการ</th>
							<th colspan="1" class="<?php echo(@$_GET['download']=='excel')?"table_header_top":"title_view";?>" style="width: 150px;vertical-align: middle;">จำนวนคน</th> 
							<th colspan="2" class="<?php echo(@$_GET['download']=='excel')?"table_header_top":"title_view";?>" style="width: 305px;vertical-align: middle;">จำนวนหุ้น(รวม)</th>
							<th colspan="2" class="<?php echo(@$_GET['download']=='excel')?"table_header_top":"title_view";?>" style="width: 305px;vertical-align: middle;">จำนวนเงินเฉลี่ยคืน(รวม) </th>
						</tr> 
					</thead>
					<br>
					<tbody>
					    <?php
							$j = 0;
                            foreach($data_average_return_arr as $row) {
						?>
                        <tr> 
                            <td class="<?php echo(@$_GET['download']=='excel')?"table_body":"title_view";?>" style="text-align: center;"><?php echo ++$j;?></td>
                            <td colspan="5" class="<?php echo(@$_GET['download']=='excel')?"table_body":"title_view";?>" style="text-align: left;">
							<?php 
								if($row['average_drop']=='0'){
									echo 'สมาชิกที่มีสิทธิ์ได้รับเงินเฉลี่ยคืน';
								}else if($row['average_drop']=='1'){
									echo 'สมาชิกที่ไม่มีสิทธิ์ได้รับเงินเฉลี่ยคืน';
								}else if($row['average_drop']=='2'){
									echo 'สมาชิกที่มีสิทธิ์ได้รับเงินเฉลี่ยคืนบางส่วน';
								}else if($row['average_drop']=='3'){
									echo 'สมาชิกที่ลาออกระหว่างปี';
								}
							?></td>
							<td colspan="1" class="<?php echo(@$_GET['download']=='excel')?"table_body":"title_view";?>" style="text-align: right;"><?php echo number_format($row['count_member_id'], 0, '.', ',').' คน'; ?></td>
                            <td colspan="2" class="<?php echo(@$_GET['download']=='excel')?"table_body":"title_view";?>" style="text-align: right;"><?php echo number_format($row['share_value_sum'], 0, '.', ',').' หุ้น'; ?></td>
							<td colspan="2" class="<?php echo(@$_GET['download']=='excel')?"table_body":"title_view";?>" style="text-align: right;"><?php echo number_format($row['average_return_value_sum'], 2, '.', ',').' บาท'; ?></td>
						</tr>
                        <?php 
                            }
                        ?>					
					</tbody>
				</table>
			</div>
		</div>

<?php
    // }
?>