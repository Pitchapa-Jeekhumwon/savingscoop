<?php 
if(@$_GET['download']!=""){
    header("Content-type: application/vnd.ms-excel;charset=utf-8;");
    header("Content-Disposition: attachment; filename=รายงานการทำรายการประจำวัน (statement) ".$account_id.".xls"); 
    date_default_timezone_set('Asia/Bangkok');

?>
<style>
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
		font-size: 24px;
		font-weight: bold;
		text-align:center;
	}
	.table_title_right{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 16px;
		font-weight: bold;
		text-align:right;
	}
	.table_header_top{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 24px;
		font-weight: bold;
		text-align:center;
		border-top: thin solid black;
		border-left: thin solid black;
		border-right: thin solid black;
		color: #000000 !important;
	}
	.table_header_mid{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 24px;
		font-weight: bold;
		text-align:center;
		border-left: thin solid black;
		border-right: thin solid black;
	}
	.table_header_bot{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 24px;
		font-weight: bold;
		text-align:center;
		border-bottom: thin solid black;
		border-left: thin solid black;
		border-right: thin solid black;
	}
	.table_header_bot2{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 24px;
		font-weight: bold;
		text-align:center;
		border: thin solid black;
	}
	.table_body{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 24px;
		border: thin solid black;
		color: #000000 !important;
	}
	.table_body_right{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 24px;
		border: thin solid black;
		text-align:right;
	}
	
	h3{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 30px;
		color: #000000 !important;
	}
	
	.body-excel{
		background: #FFFFFF !important;
		width: 100%;
	}
	
	.title_view{
		font-family: AngsanaUPC, MS Sans Serif;
		color: #000000 !important;
		font-size: 24px !important;
	}
	
	.title_view_header{
		font-family: AngsanaUPC, MS Sans Serif;
		color: #000000 !important;
		font-size: 24px !important;
	}
</style>
<?php }else{ ?>
<style>
@page {
  size: 210mm 297mm; 
  /* Chrome sets own margins, we change these printer settings */
  margin: 15mm 0mm 15mm 0mm; 
}

@media print {
	body {zoom: 50%;}
}

span.title_view {
    font-size : 24px !important;
}

th, td {
    font-size : 20px !important;
}
.title_view_header{
	color: #000000 !important;
	font-size: 24px !important;
}
</style>
<?php } ?>
		
		<div style="width: 90%;" class="text-center" >
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
				<table style="width: 100%;">
				<?php 
					
					// if(@$page == 1){
				?>	
					<tr>
                        <?php
                            if(@$_GET['download']==""){
                                ?>
                                    <td width=150>
                        
                                    </td>
                                    <td align="center" colspan="4">
                                        <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	

                                        <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                                        <h3 class="title_view">รายงานการทำรายการประจำวัน (statement)</h3>
                                        
                                        <h3 class="title_view">
                                            ประจำวันที่ <?=@$start_date?> ถึง <?=@$end_date?>
                                        </h3>
                                    </td>
                                    <td  width=150>
                                        <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                                        <?php
                                            $get_param = '?';
                                            foreach(@$_GET as $key => $value){
                                                //if($key != 'month' && $key != 'year' && $value != ''){
                                                    $get_param .= $key.'='.$value.'&';
                                                //}
                                            }
                                            $get_param = substr($get_param,0,-1);
                                        ?>
                                        <a class="no_print" target="_blank" href="<?php echo base_url(PROJECTPATH.'/save_money/statement_preview/?download=1'); ?>">
                                        <!-- <a class="no_print"  target="_blank" href="<?php echo base_url('/report_deposit_data/coop_report_account_transaction_excel'.$get_param); ?>"> -->
                                            <button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
                                        </a>
                                    </td>
                                <?php
                            }else{
                                ?>
                                    <td align="center" colspan="6">
                                        <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                                        <h3 class="title_view">รายงานการทำรายการประจำวัน (statement)</h3>
                                        
                                        <h3 class="title_view">
                                            ประจำวันที่ <?=@$start_date?> ถึง <?=@$end_date?>
                                        </h3>
                                    </td>
                                <?php
                            }
                        ?>
					</tr>  					
                    
                    <tr>
                        <td colspan='6' align=right>
                            <span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span><br>		
                            <span class="title_view">เวลา <?php echo date('H:i:s');?></span><br>
                            <span class="title_view">ผู้ทำรายการ <?=$st_by_name?></span>	
                        </td>
                    </tr>

                    

				</table>

                <table style="width: 100%;">					
                    <tr>
                        <td colspan='4' align=left width='70%'>
                            <span class="title_view_header">เลขที่บัญชี : <?php echo $this->center_function->format_account_number($account_id); ?></span><br>
                            <span class="title_view_header">ยอดเงินคงเหลือ : <?php echo number_format($balance, 2); ?> บาท</span><br>
                            <span class="title_view_header">รหัสสมาชิก : <?php echo $member_id;?></span><br>
                            <span class="title_view_header">วันที่เปิดบัญชี : <?php echo $this->center_function->ConvertToThaiDate(@$open_date,1,0);?></span><br>
                            
                        </td>
                        <td colspan='2' align=left>
							<span class="title_view_header">ชื่อบัญชี : <?php echo $account_name; ?></span><br>
                            <span class="title_view_header">ประเภทเงินฝาก : <?php echo $account_type; ?></span><br>
                            <span class="title_view_header">ชื่อสมาชิก : <?php echo $member_name; ?></span><br>
                            <span class="title_view_header">จำนวนเงินที่เปิดบัญชี : <?php echo number_format($open_balance, 2); ?> บาท</span><br>
                            <span class="title_view_header">ดอกเบี้ยสะสม : <?php echo number_format($last_old_acc_int, 2); ?> บาท</span>
                        </td>
                    </tr>
                </table>
                <br>

				<table style="width: 100%;" border=1 class="st">
					<thead> 
						<tr class="st">
							<th class="text-center table_header_top" style="padding: 7px;" width='80' align='center' >ลำดับ</th>
							<th class="text-center table_header_top" style="padding: 7px;" width="240" align='center'>วันที่</th>
							<th class="text-center table_header_top" style="vertical-align: middle;padding: 7px;" width="240" align='center'>รายการ</th>
							<th class="text-center table_header_top" style="vertical-align: middle;padding: 7px;" width="200" align='center'>เงินฝาก</th>
							<th class="text-center table_header_top" style="vertical-align: middle;padding: 7px;" width="200" align='center'>เงินถอน</th>
							<th class="text-center table_header_top" style="vertical-align: middle;padding: 7px;" width="200" align='center'>คงเหลือ</th>
							<th class="text-center table_header_top" style="vertical-align: middle;padding: 7px;" width="200" align='center'>ดอกเบี้ยสะสม</th>
							<th class="text-center table_header_top" style="vertical-align: middle;padding: 7px;" width="200" align='center'>ผู้ทำรายการ</th>
							<!--<th style="vertical-align: middle;">ยอดเงิน</th>-->
						</tr>  
					</thead>
					<tbody>
                        <?php
                            foreach ($st as $key => $value) {
                                ?>
                                    <tr class="st">
                                        <td align="center" style="padding: 7px;" class="table_body"><?php echo $key+1;?></td>
                                        <td align="center" style="padding: 7px;" class="table_body"><?php echo $this->center_function->ConvertToThaiDate($value->transaction_time, 1 ,1); ?></td>
                                        <td align="center" style="padding: 7px;" class="table_body"><?php echo $value->transaction_list; ?></td>
                                        <td align="right" style="padding: 7px;" class="table_body"><?php echo number_format($value->transaction_deposit, 2); ?></td>
                                        <td align="right" style="padding: 7px;" class="table_body"><?php echo number_format($value->transaction_withdrawal, 2); ?></td>
                                        <td align="right" style="padding: 7px;" class="table_body"><?php echo number_format($value->transaction_balance, 2); ?></td>
										<td align="right" style="padding: 7px;" class="table_body"><?php echo number_format($value->accu_int_item, 2); ?></td>
										<td align="center" style="padding: 7px;" class="table_body">		<?php 
											if($value->user_name!=''){
												echo $value->user_name;
											}else if($value->member_id_atm != ''){
												echo "ATM";
											}else if($value->status_process != '0' ){
												echo $value->status_process;
											}else{
												echo "N/A";
											}
										?></td>
                                    </tr>
                                <?php
                            }
                        ?>
					</tbody>    
				</table>
			</div>
		</div>
<?php 
// 	}
// } 
?>

<style>
table.st {
    border-collapse: collapse;
}
tr:nth-child(even).st {background-color: #f2f2f2;}
tr:hover.st {background-color: #f5f5f5;}
</style>
