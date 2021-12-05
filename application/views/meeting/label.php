<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}
	.table-view-2>thead>tr>th{
	    border-top: 1px solid #000 !important;
		border-bottom: 1px solid #000 !important;
		font-size: 16px;
	}
	.table-view-2>tbody>tr>td{
	    border: 0px !important;
		/*font-family: upbean;
		font-size: 16px;*/
		font-family: Tahoma;
		font-size: 12px;
	}
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}

	.foot-border{
	    border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}
	
	.label_list { text-align: left; color: #000; width: 80%; }
	.label_list > .cell { float: left; width: 50%; border: solid 1px #000; }
	.label_list .col1 { float: left; width: 50%; height: 170px; border-right: solid 1px #000; text-align: center; }
	.label_list .col2 { float: left; width: 50%; padding: 30px 15px 15px 15px; position: relative; font-size: 16px; }
	.label_list .logo { position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; z-index: 0; opacity: 0.2; }
	.label_list .num { font-size: 70px; }
	.label_list .barcode { max-width: 90%; height: 50px; }
	.label_list .chk { position: relative; display: inline-block; width: 16px; height: 16px; border: solid 1px #000; }
	.label_list .caption { position: relative; display: inline-block; padding-left: 5px; }
	.label_list .remark { position: relative; margin-top: 40px; }
</style>

<div style="width: 1000px;" class="page-break">
	<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
		<table style="width: 100%;">
			<tr>
				<td style="width:150px;vertical-align: top;"></td>
				<td class="text-center"></td>
				 <td style="width:150px;vertical-align: top;" class="text-right">
					<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
				</td>
			</tr>
		</table>

		<div class="label_list">
			<?php
			$i = 0;
			for($num = $start_num; $num <= $end_num; $num++) { ?>
				<div class="cell">
					<div class="col1">
						<div class="num"><?php echo $num; ?></div>
						<img src="/meeting/label/barcode?c=<?php echo $num; ?>" class="barcode">
					</div>
					<div class="col2">
						<img src="/assets/images/logo/logo.png" class="logo">
						<div class="chk"></div>
						<div class="caption">รับเงิน</div>
						<br>
						<div class="chk"></div>
						<div class="caption">รับของที่ระลึก</div>
						<div class="remark">กรุณาเก็บไว้เป็นหลักฐาน</div>
					</div>
				</div>
				<?php if(++$i % 16 == 0) { ?>
					<div style="page-break-after: always;"></div>
					<div>&nbsp;</div>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>