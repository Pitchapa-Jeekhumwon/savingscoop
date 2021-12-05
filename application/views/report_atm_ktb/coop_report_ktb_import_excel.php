<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานนำเข้าข้อมูล แนบไฟล์ CSV ที่ได้จาก KTB.xls"); 
date_default_timezone_set('Asia/Bangkok');
?>
<pre>
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
					font-size: 22px;
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
				.table_body_right{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 21px;
					border: thin solid black;
					text-align:right;
				}
			</style>
		</head>
		<body>
		
			<table class="table table-bordered">	
				<tr>
					<tr>
						<th class="table_title" colspan="6">รายงานนำเข้าข้อมูล แนบไฟล์ CSV ที่ได้จาก KTB</th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="6">
							<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>				
							<span class="title_view">   เวลา <?php echo date('H:i:s');?></span>	
						</th>
					</tr>
				</tr> 
			</table>
		
			<table class="table table-bordered">
				<thead> 
				
					<tr>
						<th class="table_header_top" style="width: 40px;vertical-align: middle;">ลำดับ</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;">วันที่ทำรายการ</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;">รหัสสมาชิก</th>
						<th class="table_header_top" style="width: 500px;vertical-align: middle;">ชื่อสกุล</th>
						<th class="table_header_top" style="width: 200px;vertical-align: middle;">บัญชี KTB</th>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;">สถานะ</th>
					</tr> 
				</thead>
				<tbody>
				<?php
					$runno = 0;				
					if(!empty($datas)) {
						foreach($datas as $data) {
							$runno++;
				?>			
						<tr> 
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo @$runno; ?></td>
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo (@$data['import_date_file'] != '')?$this->center_function->ConvertToThaiDate($data['import_date_file'],0,0,0):''; ?></td>
							<td class="table_body" style='text-align: center;vertical-align: top;mso-number-format:"\@";'><?php echo @$data['import_mem_id']; ?></td>
							<td class="table_body" style="text-align: left;vertical-align: top;width: 500px;"><?php echo @$data['member_name']; ?></td>
							<td class="table_body" style='text-align: center;vertical-align: top;mso-number-format:"\@";'><?php echo @$data['import_acct']; ?></td>						 
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo @$data['import_result']; ?></td>						  
						</tr>									
				
				<?php									
						}
					}
				?>	
				</tbody>    
			</table>
		</body>
	</html>
</pre>