<div class="layout-content">
    <div class="layout-content-body">
	<h1 style="margin-bottom: 0">ย้ายสังกัดสมาชิก</h1>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 " style="padding-right:0px;padding-left:0px">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;padding-left:0px">
			<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
			
		</div>
	</div>


	<div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                  
          <div class="bs-example" data-example-id="striped-table">

           <table class="table table-striped"> 
             <thead> 
			 <tr>
					<th class="text-center">#</th>
					<th class="text-center" colspan="3">เดิม</th>
					<th class="text-center" colspan="4">ใหม่</th>
                </tr> 
                <tr>
					<th class="text-center">#</th>
					<th class="text-left">หน่วยงานหลัก</th>
					<th class="text-left">หน่วยงานรอง</th>
					<th class="text-center">จำนวน</th>
					<th class="text-left" width="200">หน่วยงานหลัก</th>
					<th class="text-left" width="200">หน่วยงานรอง</th>
					<th class="text-left" width="200">หน่วยงานย่อย</th>
					<th class="text-center">จำนวน</th>
					<th></th> 
                </tr> 
             </thead>
                  <tbody class="mem_group_space">
						<?php  
							if(!empty($rs)){
								foreach(@$rs as $key => $row){ 
						?>
								<tr>
									<th class="text-center"><?php echo $i++; ?></th>
									<td class="text-left"><?php echo $row['department_name']; ?></td>
									<td class="text-left"><?php echo $row['faction_name']; ?></td>
									<td class="text-center"><?php echo $row['c']; ?></td>
									<td class="text-left">
										<select id="department[<?php echo $key; ?>]" name="department[<?php echo $key; ?>]" class="form-control department">
											<option value=""></option>
											<?php foreach($rs_dep as $_key => $_row) { ?>
												<option value="<?php echo $_row['id']; ?>"<?php if($_row['id'] == $row['department_new']) { ?> selected="selected"<?php } ?>><?php echo $_row['mem_group_name']; ?></option>
											<?php } ?>
										</select>
									</td>
									<td class="text-left">
										<select id="faction[<?php echo $key; ?>]" name="faction[<?php echo $key; ?>]" class="form-control faction" data-select="<?php echo $row['faction_new']; ?>"></select>
									</td>
									<td class="text-left">
										<select id="level[<?php echo $key; ?>]" name="level[<?php echo $key; ?>]" class="form-control level" data-select="<?php echo $row['level_new']; ?>"></select>
									</td>
									<td class="text-center c_new"><?php echo $row['c_new']; ?></td>
									<td align="right">
										<button type="button" class="btn btn-primary btn-block btn_save" data-index="<?php echo $key; ?>">บันทึก</button>
										<input type="hidden" id="department_old[<?php echo $key; ?>]" name="department_old[<?php echo $key; ?>]" value="<?php echo $row['department']; ?>" class="department_old">
										<input type="hidden" id="faction_old[<?php echo $key; ?>]" name="faction_old[<?php echo $key; ?>]" value="<?php echo $row['faction']; ?>" class="faction_old">
									</td> 
								</tr>
						<?php 
								}
							} 
						?>
                  </tbody> 
                  </table> 
          </div>

			</div>
		  </div>
		</div>

	</div>
</div>

<script>
	$(function() {
		var base_url = $('#base_url').attr('class');

		function get_faction_list(obj) {
			var faction = obj.closest("tr").find(".faction");
			faction.empty();
			faction.append(`<option value=""></option>`);

			if(obj.val() != "") {
				$.ajax({
					url: base_url + 'member_group_move/get_faction_list',
					method: 'POST',
					data: {
						'id': obj.val()
					},
					success: function(msg){
						var $json = JSON.parse(msg);
						$.each( $json.data, function($key, $row) {
							faction.append(`<option value="${$row.id}"` + ($row.id == obj.closest("tr").find(".faction").data("select") ? ` selected="selected"` : ``) + `>${$row.name}</option>`);
						});
						faction.change();
					}
				});
			}
			else {
				faction.change();
			}
		}

		function get_level_list(obj) {
			var level = obj.closest("tr").find(".level");
			level.empty();
			level.append(`<option value=""></option>`);

			if(obj.val() != "") {
				$.ajax({
					url: base_url + 'member_group_move/get_level_list',
					method: 'POST',
					data: {
						'id': obj.val()
					},
					success: function(msg){
						var $json = JSON.parse(msg);
						$.each( $json.data, function($key, $row) {
							level.append(`<option value="${$row.id}"` + ($row.id == obj.closest("tr").find(".level").data("select") ? ` selected="selected"` : ``) + `>${$row.name}</option>`);
						});
					}
				});
			}
		}

		function save(obj) {
			var tr = obj.closest("tr");

			$.ajax({
				url: base_url + 'member_group_move/save',
				method: 'POST',
				data: {
					'department_old': tr.find(".department_old").val(),
					'faction_old': tr.find(".faction_old").val(),
					'department': tr.find(".department").val(),
					'faction': tr.find(".faction").val(),
					'level': tr.find(".level").val()
				},
				success: function(msg){
					var $json = JSON.parse(msg);
					tr.find(".c_new").html($json.c > 0 ? $json.c : '');
					swal('บันทึกข้อมูลเรียบร้อยแล้ว' , '' , 'success');
				}
			});
		}

		$(".department").change(function() {
			get_faction_list($(this));
		});

		$(".faction").change(function() {
			get_level_list($(this));
		});

		$(".btn_save").click(function() {
			save($(this));
		});

		$(".department").change();

	});
</script>