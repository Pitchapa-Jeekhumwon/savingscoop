<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.left {
				text-align: left;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			.modal-dialog-data {
				width:90% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal-dialog-cal {
				width:80% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal-dialog-file {
				width:50% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal_data_input{
				margin-bottom: 5px;
			}
			.form-group{
				margin-bottom: 5px;
			  }
			  .red{
				color: red;
			  }
			  .green{
				color: green;
			  }
            .warm {
                background-color: antiquewhite !important;
            }
            .normal {
                background-color: unset !important;
            }
            .point{
                cursor: pointer;
            }
            .inline{
                display: flex;
                justify-content: flex-end;
                align-items: baseline;
            }
            .btn.btn-small, .btn.btn-small:visited, .btn.btn-small:hover, .btn.btn-small:active{
                cursor:pointer;
                overflow: hidden;
                background: Transparent;
                background-repeat:no-repeat;
                border: unset !important;
                outline: unset !important;
            }
            .wi{
                width:50% !important;
            }
		</style> 
		<div class="row">
			<div class="form-group">
				<div class="col-sm-6">
					<h1 class="title_top">แก้ไขรายการเคลื่อนไหวสินเชื่อ</h1>
					<?php $this->load->view('breadcrumb'); ?>
				</div>
				<div class="col-sm-6">
				</div>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" id="panel-body" style="padding-top:0px !important;">
                    <div id="content-panel-body">
                        <!-- info -->
                        <div class="row">
                            <div class="col-md-offset-2 col-md-3">
                                <h3><b>รหัสสมาชิก</b></h3>
                            </div>
                            <div class="col-md-3">
                                <h3><?=@$member['member_id']?></h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-offset-2 col-md-3">
                                <h3><b>ชื่อ - สกุล</b></h3>
                            </div>
                            <div class="col-md-3">
                                <h3><?=@$member['prename_short']." ".@$member['firstname_th']." ".@$member['lastname_th']?></h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-offset-2 col-md-3">
                                <h3><b>รหัสสัญญาเงินกู้</b></h3>
                            </div>
                            <div class="col-md-3">
                                <h3><?=@$loan['contract_number']?></h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-offset-2 col-md-3">
                                <h3><b>วงเงินกู้</b></h3>
                            </div>
                            <div class="col-md-3">
                                <h3><?=@number_format($loan['loan_amount'], 2);?></h3>
                            </div>
                            <div class="col-md-1">
                                <h3><b>บาท</b></h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-offset-2 col-md-3">
                                <h3><b>คงเหลือ</b></h3>
                            </div>
                            <div class="col-md-3">
                                <h3><?=@number_format($loan['loan_amount_balance'], 2);?></h3>
                            </div>
                            <div class="col-md-1">
                                <h3><b>บาท</b></h3>
                            </div>
                        </div>
                        <!-- loan_transaction -->
                        <hr>
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="bs-example" data-example-id="striped-table">
                                <table class="table table-bordered table-striped table-center">


                                    <thead>
                                    <tr class="bg-primary">
                                        <th>ลำดับ</th>
                                        <th>วันที่ทำรายการ</th>
                                        <th>เลขที่ใบเสร็จ</th>
                                        <th>เงินต้น</th>
                                        <th>code</th>
                                        <th>ดอกเบี้ย</th>
                                        <th>เงินคงเหลือ</th>
                                        <th>คงค้างยกมา</th>
                                        <th>ยอดคำนวน</th>
                                        <th>ดอกเบี้ยสะสม</th>
                                        <th>จัดการ</th>
                                    </tr>
                                    </thead>

                                    <tbody id="table_first">
                                    <?php
                                    $loan_amount = $loan['loan_amount'];
                                    //echo "<pre>"; print_r($loan_transaction);exit;
                                    foreach(@$loan_transaction as $key => $value){
                                    $loan_amount = round($loan_amount - $value['principal'], 2, PHP_ROUND_HALF_UP);
                                    $date =  $this->center_function->ConvertToThaiDate(@date('Y-m-d',$value['transaction_datetime'] ), 1, 0);
                                        //echo $value['loan_type_code'];exit;
                                        if($value['loan_type_code']=="RM"){
                                            $type_code =1;
                                        }else{
                                            $type_code = 0;
                                        }

                                    if($key > 0) {
                                        if($type_code == 0){
                                        $data = array();
                                        $data['loan_id'] = @$loan_id;
                                        $data['entry_date'] = $value['transaction_datetime'];
                                        $data['loan_type'] = $this->LoanCalc->get_loan_type(@$loan_id);
                                        $data['principal'] = $value['principal'];
                                        $data['interest'] = $value['interest'];
                                        $data['limit'] = array('loan_transaction_id <>' => $value['loan_transaction_id'],'transaction_datetime <' => $value['transaction_datetime']);
                                        $result = $this->LoanCalc->calc('PL', $data);
                                        }
                                        else{
                                        //echo $value['loan_type_code'];exit;
                                        $data = array();
                                        $data['loan_id'] = @$loan_id;
                                        $data['transaction_datetime'] = $_GET['date'].' 00:00:00';
                                        $data['interest'] = $_GET['interest'];
                                        $data['principal'] = $_GET['principal'];
                                        $data['receipt_id'] = $_GET['receipt_id'];
                                        $data['loan_type'] = $this->LoanCalc->get_loan_type(@$loan_id);
                                        $result =$this->LoanCalc->calc('RM', $data);
                                            //echo print_r($result);exit;
                                        }

                                    }else{
                                        $result = array(
                                                'interest_arrears' => $value['interest_arrears'],
                                                'interest_calculate_arrears' => $value['interest_calculate_arrears'],
                                                'interest_arrear_bal' => $value['interest_arrear_bal'],

                                        );
                                    }
                                    $class_css = "normal";
                                    $invalid = false;
                                    $token = sha1(md5($value['loan_transaction_id']));

                                    if(number_format($loan_amount, 2, '.', '')!=number_format($value['loan_amount_balance'], 2, '.', '')){
                                        $invalid = true;
                                        $class_css = "warm";
                                        $info = '<i class="fa fa-exclamation" aria-hidden="true" style="color: red;" data-toggle="tooltip" data-placement="bottom" title="'.number_format($loan_amount+$value['principal'], 2).' - '.number_format($value['principal'], 2).' = '.number_format($loan_amount, 2).'"></i>';
                                    }else{
                                        $info = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                                    }

                                    ?>
                                        <tr>
                                            <td><?php echo $key+1; ?></td>
                                            <td><?php echo $value['transaction_datetime']   ; ?></td>
                                            <td align="right"><?php echo $value['receipt_id'] ;?></td>
                                            <td align="right" width="10%"><?php echo number_format($value['principal'], 2); ?>
                                                <i class="fa fa-info-circle" aria-hidden="true" title="" onclick="compare_transaction('<?php echo $value['loan_id']?>', '<?=$value['loan_transaction_id']?>', '<?=$token?>');">
                                                </i></td>
                                            <td align="right" ><?php echo $value['loan_type_code']; ?>
                                                </td>
                                            <td align="center"><?php echo number_format($value['interest'], 2); ?></td>
                                            <td>
                                               <div class="input-group">
                                                   <input class="form-control  m-1-b text-right" id="bal_<?=$value['loan_transaction_id']?>" value="<?=number_format($value['loan_amount_balance'], 2)?>"
                                                          data-default="<?=number_format($value['loan_amount_balance'], 2)?>" data-invalid="<?=$loan_amount?>" type="text" >
                                                   <div class="input-group-btn">

                                                       <?php
                                                       if($value['loan_type_code']=="RM"){
                                                           $loan_amount=$loan_amount+($loan_transaction[$key-1]['principal']);
                                                       }
                                                       if($value['loan_amount_balance'] != $loan_amount){?>
                                                       <button type="button" title="<?= number_format($value['loan_amount_balance'], 2) ?>"
                                                               class="btn btn-default btn-search" onclick="set_val('bal_<?= $value['loan_transaction_id'] ?>',
                                                       <?= $loan_amount ?>)">

                                                           <span class="fa fa-info-circle"></span>
                                                           <?php }else{?>
                                                           <button type="button" title="<?= number_format($loan_amount, 2) ?>"
                                                                   class="btn btn-info btn-search" onclick="set_val('bal_<?= $value['loan_transaction_id'] ?>',
                                                           <?= $loan_amount ?>)">
                                                           <span class="fa fa-check"></span>
                                                           <?php }?>
                                                       </button>
                                                   </div>



                                               </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input class="form-control  m-1-b text-right" type="text" title="<?= number_format($value['interest_arrears'], 2) ?>"
                                                           value="<?php echo $result['interest_arrears']?>" id="int_<?=$value['loan_transaction_id']?>">
                                                    <div class="input-group-btn">
                                                            <button type="button" title="<?= number_format($result['interest_arrears'], 2) ?>"
                                                                    class="btn btn-info btn-search" onclick="set_val('int_<?= $value['loan_transaction_id'] ?>',
                                                            <?= $value['interest_arrears'] ?>)">

                                                                <span class="fa fa-check"></span>

                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input class="form-control  m-1-b text-right" type="text" id="int_cal_<?=$value['loan_transaction_id']?>" value="<?php echo $value['interest_calculate_arrears']?>">
                                                    <div class="input-group-btn" >
                                                        <?php  if($value['interest_calculate_arrears']!=$result['interest_calculate_arrears']){?>
                                                        <button type="button"  title="<?= $result['interest_calculate_arrears']?>" class="btn btn-default btn-search" onclick="set_val('int_cal_<?= $value['loan_transaction_id'] ?>',
                                                        <?= $result['interest_calculate_arrears'] ?>)">
                                                            <span class="fa fa-info-circle"></span>
                                                        </button>
                                                        <?php }else{?>
                                                        <button type="button" title="<?= $result['interest_calculate_arrears']?>" class="btn btn-info btn-search">
                                                            <span class="fa fa-check"></span>
                                                        </button>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input class="form-control  m-1-b text-right" type="text" id="int_bal_<?=$value['loan_transaction_id']?>" value="<?php echo $value['interest_arrear_bal']?>">
                                                    <div class="input-group-btn">
                                                        <?php  if($value['interest_arrear_bal']!=$result['interest_arrear_bal']){
                                                            $value['interest_arrear_bal']=$result['interest_arrear_bal'];
                                                            ?>
                                                            <button type="button" title="<?= $result['interest_arrear_bal']?>" class="btn btn-default btn-search" onclick="set_val('int_bal_<?= $value['loan_transaction_id'] ?>',
                                                            <?= $result['interest_arrear_bal'] ?>)">
                                                                <span class="fa fa-info-circle"></span>
                                                            </button>
                                                        <?php }else{?>
                                                            <button type="button" title="<?= $result['interest_arrear_bal']?>" class="btn btn-info btn-search">
                                                                <span class="fa fa-check"></span>
                                                            </button>
                                                        <?php } ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td align="right">

                                                <?php
                                                if ($invalid || 1 == 1) {
                                                    ?>
                                                    <button type="button" class="btn btn-success btn-xs"
                                                            onclick="update_this(<?= $value['loan_id'] ?>, <?= $value['loan_transaction_id'] ?>, '<?= $token ?>');">
                                                        เฉพาะแถวนี้
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-xs"
                                                            onclick="update_after_this(<?= $value['loan_id'] ?>, <?= $value['loan_transaction_id'] ?>, '<?= $token ?>');">
                                                        ตั้งแต่แถวนี้
                                                    </button>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
<?php } ?>


                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
				</div>
			</div>
		</div>

		
	</div>
</div>
<!-- Modal -->
<style>
    .dialog-transaction{
        display: flex;
        width: 100%;
        justify-content: center;
        align-items: center;
    }
    .transfer{
        display: flex;
        justify-content: center;
        align-items: end;
        margin: auto;
    }

    .transfer .arrow {
        margin: auto 10px;
    }
</style>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Compress Match Transaction</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="dialog-transaction">
                    <div class="form-group">
                        <label class="control-label" for="receipt_amount_balance">ยอดคงเหลือในใบเสร็จ</label>
                        <div class="input-data">
                            <input class="form-control" type="text" id="receipt_amount_balance"/>
                        </div>
                    </div>
                    <div class="transfer">
                        <button type="button" class="btn arrow arrow-left" onclick="transfer('right')"> <= </button>
                        <button type="button"  class="btn arrow arrow-right" onclick="transfer('left')"> => </button>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="transaction_amount_balance">ยอดคงเหลือในรายการชำระ</label>
                        <div class="input-data">
                            <input class="form-control" type="text" id="transaction_amount_balance"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="transct_loan_id" value="">
                <input type="hidden" id="transct_receipt" value="">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submit_change_balance()">Save changes</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();

    $("body").on('change', '.numeral', function(){    // 2nd (B)
        var default_val = numeral($(this).data("default")).value();
        console.log("Default", default_val);
        var val = numeral($(this).val()).value();
        $(this).val(numeral(val).format('0,0.00'));
    });

});

function set_val(id, val_to){
    console.log("set to", val);
    var val = numeral(val_to).value();
    $("#"+id).val(numeral(val).format('0,0.00'));
}

function update_this(loan_id, loan_transaction_id, token){
    console.log("update_this", loan_transaction_id);
    var bal = numeral($("#bal_"+loan_transaction_id).val()).value();
    var int = numeral($("#int_"+loan_transaction_id).val()).value();
    var int_cal = numeral($("#int_cal_"+loan_transaction_id).val()).value();
    var int_bal = numeral($("#int_bal_"+loan_transaction_id).val()).value();
    $.ajax({
		url: base_url+'loan/update_loan_transaction_this_row',
		method: 'POST',
		data: {
			loan_id : loan_id,
            loan_transaction_id : loan_transaction_id,
            token : token,
            balance: bal,
            interest :int,
            interest_cal: int_cal,
            interest_bal: int_bal
		},
		success: function(res){
            var obj = JSON.parse(res);
            $("#row_"+loan_transaction_id).load(window.location.href+" #content_row_"+loan_transaction_id, function (response, status, xhr) {
                var invalid = $("#bal_"+loan_transaction_id).data("invalid");
                var val = numeral($("#bal_"+loan_transaction_id).val()).value();
                if(invalid==val){
                    $("#row_"+loan_transaction_id).removeClass("warm");
                    $("#row_"+loan_transaction_id).addClass("normal");
                }
            });
		}
	});

}

function update_after_this(loan_id, loan_transaction_id, token){
    console.log("update_after_this", loan_transaction_id);
    var bal = numeral($("#bal_"+loan_transaction_id).val()).value();
    var int = numeral($("#int_"+loan_transaction_id).val()).value();
    var int_cal = numeral($("#int_cal_"+loan_transaction_id).val()).value();
    var int_bal = numeral($("#int_bal_"+loan_transaction_id).val()).value();
    $.ajax({
		url: base_url+'loan/update_loan_transaction_after_this_row',
		method: 'POST',
		data: {
			loan_id : loan_id,
            loan_transaction_id : loan_transaction_id,
            token : token,
            balance: bal,
            interest :int,
            interest_cal: int_cal,
            interest_bal: int_bal
		},
		success: function(res){
            var obj = JSON.parse(res);
            $("#panel-body").load(window.location.href+" #content-panel-body", function (response, status, xhr) {
                var invalid = $("#bal_"+loan_transaction_id).data("invalid");
                var val = numeral($("#bal_"+loan_transaction_id).val()).value();
                if(invalid==val){
                    $("#row_"+loan_transaction_id).removeClass("warm");
                    $("#row_"+loan_transaction_id).addClass("normal");
                }
            });

		}
	});
}

function compare_transaction(loan_id, loan_transaction_id, token){
    const data = {loan_id: loan_id, loan_transaction_id: loan_transaction_id, token: token};
    $.post(base_url+'/loan/compare_transaction', data, function(res){
        console.log(res.data.receipt.loan_balance_amount, res.data.transaction.loan_balance_amount);
        $('#exampleModal #receipt_amount_balance').val(res.data.receipt.loan_balance_amount);
        $('#exampleModal #transaction_amount_balance').val(res.data.transaction.loan_balance_amount);
        $('#exampleModal #transct_loan_id').val(res.data.loan_id);
        $('#exampleModal #transct_receipt').val(res.data.receipt_id);
        $('#exampleModal').modal('show');
    });
}


function transfer(action){
    const receipt = $('#exampleModal #receipt_amount_balance');
    const transaction = $('#exampleModal #transaction_amount_balance');
    if(action === 'right'){
        receipt.val(transaction.val());
    }else {
        transaction.val(receipt.val());
    }
}

function submit_change_balance(){
    const loan_id = $('#exampleModal #transct_loan_id').val();
    const receipt_id = $('#exampleModal #transct_receipt').val();
    const receipt = $('#exampleModal #receipt_amount_balance');
    const transaction = $('#exampleModal #transaction_amount_balance');
    const data = {loan_id: loan_id, receipt_id, amount_receipt: receipt.val(), amount_transaction: transaction.val()};
    $.post(base_url+"loan/update_transfer_balance", data, function (res) {
        $('#exampleModal').modal('hide');
        $("#panel-body").load(window.location.href+" #content-panel-body", function (response, status, xhr) {

        });
    })

}


function delete_trash_transaction(loan_id, loan_transaction_id, token){
	const bal = numeral($("#bal_"+loan_transaction_id).val()).value();
	swal({
		title: "คุณแน่ใจใช่หรือไม่",
		text: "ถ้าลบข้อมูลนี้จะไม่สามารถกู้คืนได้",
		type: "warning",
		showCancelButton: true,
		confirmButtonClass: "btn-danger",
		confirmButtonText: "ใช่",
		cancelButtonText: "ไม่",
		closeOnConfirm: true,
		closeOnCancel: true
	}, function(isConfirm){
		if(isConfirm){
			$.post(base_url+'/loan/delete_trash_transaction', {
				loan_id: loan_id,
				loan_transaction_id: loan_transaction_id,
				token: token,
				balance: bal
			}, function(response, status, xhr){
				console.log('On Deleted: ', response);
				$("#panel-body").load(window.location.href+" #content-panel-body", function (response, status, xhr) {

				});
			});
		}
	});

}

</script>
