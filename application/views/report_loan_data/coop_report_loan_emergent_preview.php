<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 16px;
	}		
	.table {
		color: #000;
	}
	@page { size: landscape; }
</style>		
<?php
$param = '';
if(!empty($_GET)){
	foreach($_GET AS $key=>$val){
		$param .= $key.'='.$val.'&';
	}
}
			
if(@$_GET['report_date'] != ''){
	$date_arr = explode('/',@$_GET['report_date']);
	$day = (int)@$date_arr[0];
	$month = (int)@$date_arr[1];
	$year = (int)@$date_arr[2];
	$year -= 543;
	$file_name_text = $day."_".$month_arr[$month]."_".($year+543);
}else{
	if(@$_GET['month']!='' && $_GET['year']!=''){
		$day = '';
		$month = @$_GET['month'];
		$year = (@$_GET['year']-543);
		$file_name_text = $month_arr[$month]."_".($year+543);
	}else{
		$day = '';
		$month = '';
		$year = (@$_GET['year']-543);
		$file_name_text = ($year+543);
	}
}

if($month!=''){
	$month_start = $month;
	$month_end = $month;
}else{
	$month_start = 1;
	$month_end = 12;
}
//echo"<pre>";print_r($data);exit;
$i = 0;
for($m = $month_start; $m <= $month_end; $m++){
$i++;	
		$s_date = $year.'-'.sprintf("%02d",@$m).'-01'.' 00:00:00.000';
		$e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
		$where_check = " AND t1.createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
		$this->db->select(array('t1.id as loan_id'));
		$this->db->from('coop_loan as t1');
		$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
		$this->db->join("coop_prename as t3 ", "t2.prename_id = t3.prename_id", "left");
		$this->db->join("coop_loan_reason as t4 ", "t1.loan_reason = t4.loan_reason_id", "inner");
		$this->db->join("coop_loan_name as t5", "t1.loan_type = t5.loan_name_id", "left");
		$this->db->join("coop_loan_type as t6", "t5.loan_type_id = t6.id", "left");
		$this->db->where("t6.id = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where_check}");
		//$this->db->where("t1.loan_type = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where_check}");
		$this->db->order_by('t1.createdatetime ASC');
		$rs_check = $this->db->get()->result_array();
		$row_check = @$rs_check[0];
	
		if(@$row_check['loan_id']=='' && @$_GET['report_date']==''){
			continue;
		}
?>
		
		<div style="width: 1500px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;height: 1000px;">
				<table style="width: 100%;">
					<tr>
						<td style="width:100px;vertical-align: top;">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
						</td>
						<td class="text-center">
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view"><?php echo "ทะเบียน".@$loan_type[@$_GET['loan_type']]."  เดือน  ".@$month_arr[$m]." ".(@$year+543);?></h3>
							 <p>&nbsp;</p>	
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<?php if($i == '1'){?>
								<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
								<a href="<?php echo base_url(PROJECTPATH.'/report_loan_data/coop_report_loan_emergent_excel?'.$param); ?>" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>	
							<?php } ?>
						 </td>
					</tr> 
					<tr>
						<td colspan="3">
							<h3 class="title_view">
							</h3>
						</td>
					</tr> 
				</table>
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th colspan="2" style="width: 100px;vertical-align: middle;">หนังสือกู้สำหรับ <?php echo $loan_type[@$_GET['loan_type']];?></th>
							<th colspan="4" style="width: 80px;vertical-align: middle;">ผู้กู้</th>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">จำนวนเงินกู้</th> 
							<th colspan="3" style="width: 200px;vertical-align: middle;">การส่งเงินงวดชำระหนี้</th>  
							<th rowspan="2" style="width: 200px;vertical-align: middle;">เหตุผลในการขอกู้</th> 
						</tr> 
						<tr>
							<th style="width: 85px;vertical-align: middle;">ที่</th>
							<th style="width: 85px;vertical-align: middle;">วันที่</th>
							<th style="width: 85px;vertical-align: middle;">ทะเบียนสมาชิก</th>
							<th style="width: 85px;vertical-align: middle;">รหัสพนักงาน</th>
							<th style="width: 85px;vertical-align: middle;">ชื่อ -สกุล</th>
							<th style="width: 85px;vertical-align: middle;">หน่วยงาน</th>
							<th style="width: 85px;vertical-align: middle;">งวดละ</th>
							<th style="width: 85px;vertical-align: middle;">ตั้งแต่</th>
							<th style="width: 85px;vertical-align: middle;">ถึง</th> 
						</tr> 
					</thead>
					<tbody>
					  <?php 
						$where = '';
						if($day != ''){
							$s_date = $year.'-'.sprintf("%02d",@$m).'-'.sprintf("%02d",@$day).' 00:00:00.000';
							$e_date = $year.'-'.sprintf("%02d",@$m).'-'.sprintf("%02d",@$day).' 23:59:59.000';
							$where .= " AND t1.createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
						}else{
							$s_date = $year.'-'.sprintf("%02d",@$m).'-01'.' 00:00:00.000';
							$e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
							$where .= " AND t1.createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
						}
						$this->db->select(array('t1.id as loan_id',
												't1.contract_number',
												't1.createdatetime',
												't2.member_id',
												't2.employee_id',
												't3.prename_short',
												't2.firstname_th',
												't2.lastname_th',
												't2.level',
												't1.period_amount',
												't1.loan_amount',
												't1.money_period_1',
												't4.loan_reason'));
						$this->db->from('coop_loan as t1');
						$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
						$this->db->join("coop_prename as t3 ", "t2.prename_id = t3.prename_id", "left");
						$this->db->join("coop_loan_reason as t4 ", "t1.loan_reason = t4.loan_reason_id", "inner");
						$this->db->join("coop_loan_name as t5", "t1.loan_type = t5.loan_name_id", "left");
						$this->db->join("coop_loan_type as t6", "t5.loan_type_id = t6.id", "left");
						$this->db->where("t6.id = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where}");
						//$this->db->where("t1.loan_type = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where}");
						$this->db->order_by('t1.createdatetime ASC');
						$rs = $this->db->get()->result_array();
						$count_loan = 0;
						$loan_amount=0;
						//print_r($this->db->last_query());
						if(!empty($rs)){
							foreach($rs as $key => $row){		
								$i+=1;
								$this->db->select(array('period_count','date_period'));
								$this->db->from('coop_loan_period');
								$this->db->where("loan_id = '".@$row['loan_id']."'");
								$this->db->order_by('period_count ASC');
								$rs_period = $this->db->get()->result_array();;
								
								$first_period = '';
								$last_period = '';
								if(!empty($rs_period)){
									foreach($rs_period as $key => $row_period){	
										if(@$row_period['period_count'] == '1'){
											$first_period = @$row_period['date_period'];
										}
										$last_period = @$row_period['date_period'];
									}
								}

								$loan_amount += @$row['loan_amount'];						
						?>
						  <tr> 
							  <td style="text-align: center;"><?php echo @$row['contract_number']?></td>
							  <td style="text-align: center;"><?php echo $this->center_function->mydate2date(@$row['createdatetime']); ?></td>						 
							  <td style="text-align: left;"><?php echo @$row['member_id']; ?></td>						 
							  <td style="text-align: left;"><?php echo @$row['employee_id']; ?></td> 							 
							  <td style="text-align: left;"><?php echo @$row['prename_short'].@$row['firstname_th'].'  '.@$row['lastname_th']; ?></td> 							 
							  <td style="text-align: left;"><?php echo @$mem_group_arr[@$row['level']]; ?></td> 							 
							  <td style="text-align: center;"><?php echo number_format(@$row['loan_amount'],2); ?></td> 						 
							  <td style="text-align: right;"><?php echo number_format(@$row['money_period_1'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo ($first_period)?@$month_short_arr[(int)date('m',strtotime($first_period))]." ".substr((date('Y',strtotime($first_period))+543),2,2):'';?></td> 					 
							  <td style="text-align: center;"><?php echo ($last_period)?@$month_short_arr[(int)date('m',strtotime($last_period))]." ".substr((date('Y',strtotime($last_period))+543),2,2):'';?></td> 					 
							  <td style="text-align: center;"><?php echo @$row['loan_reason'];?></td> 								 						 
						  </tr>
					<?php 
								$count_loan++;
							}
						} 
					?>							
					</tbody>  
				</table>
				
				<table style="width: 100%;" class="m-t-2">
					<tr>
						<td style="width: 200px;"></td>
						<td style="width: 150px;"><h3 class="title_view"><?php echo "เดือน ".$month_arr[$m];?></h3></td>
						<td style="width: 40px;"><h3 class="title_view"><?php echo "รวม " ;?></h3></td>
						<td style="width: 50px;    text-align: center;"><h3 class="title_view"><?php echo number_format($count_loan);?></h3></td>
						<td style="width: 150px;"><h3 class="title_view"><?php echo "สัญญา ";?></h3></td>
						<td style="width: 110px;"><h3 class="title_view"><?php echo "เป็นเงินจำนวน " ;?></h3></td>
						<td style="width: 150px;    text-align: center;"><h3 class="title_view"><?php echo number_format($loan_amount) ;?></h3></td>
						<td style="width: 50px;"><h3 class="title_view"><?php echo "บาท " ;?></h3></td>
						<td></td>
					</tr>
				</table>
			</div>
		</div>
<?php } ?>		