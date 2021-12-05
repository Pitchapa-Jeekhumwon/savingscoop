<?php
$sum_status = 0;
if(isset($keep_lists) && sizeof($keep_lists)){
    foreach($keep_lists as $index => $item){
        if($item['run_status']=="0"){
            $sum_status++;
        }
    }
}
//echo "<pre>"; print_r($sum_status);exit;
?>
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

    .title-tab{
        margin-top: 0px;
        margin-bottom: 0px;
    }

    .text-underline-double{
        text-decoration: underline;
        text-decoration-color: #2C2C2C;
        text-decoration-style: double;
    }
</style>
<div class="layout-content">
    <div class="layout-content-body">
        <div class="row">
            <div class="form-group">
                <div class="col-sm-6">
                    <h1 class="title_top">ยกเลิกเรียกเก็บ</h1>
                    <?php $this->load->view('breadcrumb'); ?>
                </div>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <?php $this->load->view('search_member_new'); ?>
                    <div class="g24-col-sm-24">
                        <div class="form-group g24-col-sm-8">
                            <label class="g24-col-sm-10 control-label " for="form-control-2">ประเภทสมัคร</label>
                            <div class="g24-col-sm-14">
                                <input id="form-control-2"  class="form-control " type="text" value="<?php echo $mem_apply_type[$row_member['apply_type_id']];?>"  readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-8">
                            <label class="g24-col-sm-10 control-label" for="form-control-2">เงินเดือน</label>
                            <div class="g24-col-sm-14" >
                                <input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$row_member['salary']); ?>"  readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-8">
                            <label class="g24-col-sm-10 control-label" for="form-control-2">รายได้อื่นๆ</label>
                            <div class="g24-col-sm-14" >
                                <input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$row_member['other_income']); ?>"  readonly>
                            </div>
                        </div>
                    </div>
                    <div class="g24-col-sm-24">
                        <div class="form-group g24-col-sm-8">
                            <label class="g24-col-sm-10 control-label" for="form-control-2">รวมรายได้</label>
                            <div class="g24-col-sm-14" >
                                <input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$row_member['salary']+@$row_member['other_income']); ?>"  readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-8">
                            <label class="g24-col-sm-10 control-label" for="form-control-2">พินัยกรรม</label>
                            <div class="g24-col-sm-14" >

                                <input id="form-control-2"  class="form-control " type="text" <?php echo @$style_testament;?> value="<?php echo @$testament; ?>"  readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-8">
                            <label class="g24-col-sm-10 control-label ">ทุนเรือนหุ้นสะสม</label>
                            <div class="g24-col-sm-14">
                                <input class="form-control" type="text" value="<?php echo number_format(@$cal_share,0); ?>" readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-8">
                            <label class="g24-col-sm-10 control-label ">ส่งหุ้นงวดละ</label>
                            <div class="g24-col-sm-14">
                                <input class="form-control" type="text" value="<?php echo number_format(@$row_member['share_month']); ?>"  readonly>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-8">
                            <label class="g24-col-sm-10 control-label" for="form-control-2">หมายเหตุ</label>
                            <div class="g24-col-sm-14" >
                                <div class="input-group">
                                    <input id="note_remark" class="form-control" type="text" value="<?php echo strip_tags(@$row_member['note']); ?>" readonly>
                                    <span class="input-group-btn">
											<a id="test_remark" class="fancybox_share fancybox.iframe" href="#" onclick="read_more()">
												<button id="" type="button" class="btn btn-info btn-search"><span class="fa fa-plus-square"></span></button>
											</a>
										</span>
                                </div>
                            </div>
                        </div>
                        <div class="form=group g24-col-sm-8">
                            <label class="g24-col-sm-10 control-label" for="form-control-2">หมายศาล</label>
                            <div class="g24-col-sm-14" >
                                <input id="court_writ_note_in" class="form-control" type="text" value="<?php echo(@$row_member['court_writ_'] == '1') ? "มีหมายศาล" : "ไม่มีหมายศาล"; ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="g24-col-sm-24">
                        <div class="form-group g24-col-sm-8">
                            <label class="g24-col-sm-10 control-label" for="form-control-2">งดหุ้น</label>
                            <div class="g24-col-sm-14">
                                <input class="form-control" type="text" <?php echo @$refrain_share_txt != "ไม่ระบุ" ? 'style="color:red;"' : "" ?> value="<?php echo @$refrain_share_txt; ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-body">
                    <div class="g24-col-sm-24">
                        <div class="form-group g24-col-sm-24 g24-col-lg-24">
                            <label class="g24-col-sm-4 g24-col-lg-2 text-left">รายการเรียกเก็บ</label>

                            <label class="g24-col-sm-2 g24-col-lg-1 text-center" onchange="">เดือน</label>
                            <select class="form-control g24-col-sm-6 g24-col-lg-4" id="profile_month" name="profile_month">
                                <?php foreach(@$profile_month as $value){ ?>
                                    <option value="<?php echo $value; ?>" <?php echo $value == date("n") ? "selected" : ''; ?>><?php echo $month_arr[$value]; ?></option>
                                <?php } ?>
                            </select>
                            <label class="g24-col-sm-2 g24-col-lg-1 text-center">ปี</label>
                            <select class="form-control g24-col-sm-6 g24-col-lg-4" id="profile_year" name="profile_year" onchange="change_year('profile_year','profile_month')">
                                <?php foreach(@$profile_year as $value){ ?>
                                    <option value="<?php echo $value; ?>" <?php echo $value == date("Y")+543 ? "selected" : ''; ?>><?php echo $value; ?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" id="profile_id" value="<?php echo $profile_id; ?>">
                            <input type="hidden" id="profile_year" value="<?php echo $profile_year; ?>">
                            <input type="hidden" id="profile_month" value="<?php echo $profile_month; ?>">

                            <div class="g24-col-sm-offset-21 text-center ">
                                <button type="button" class="btn btn-primary" data-profile_id="<?php echo $profile_id; ?>" data-member_id="<?php echo $member_id; ?>" onclick="dialogAllOpen(this)"
                                    <?php echo $sum_status == '0' ? "disabled" : ""; ?>>
                                     <i class="fa fa-list"></i> ยกเลิกทั้งหมด
                                </button>
                            </div>
                        </div>
                        <div id="keeping-list">
                            <table class="table table-bordered table-striped ">
                                <thead>
                                    <tr class="bg-primary table-center">
                                        <th class="text-center" style="width: 5%;">ลำดับ</th>
                                        <th class="text-center">รายการ</th>
                                        <th class="text-center">เงินต้น</th>
                                        <th class="text-center">ดอกเบี้ย</th>
                                        <th class="text-center">รวม</th>
                                        <th class="text-center">#</th>
                                    </tr>
                                </thead>
                                <tbody id="table-result">
                                    <?php if(isset($keep_lists) && sizeof($keep_lists)){ ?>
                                    <?php $i=1;
                                        foreach($keep_lists as $index => $item){
                                        $pt+=$item['principal'];
                                        $it+=$item['interest'];
                                    ?>
                                    <tr class="<?php echo $item['run_status'] != '0' ? "red": "";?>">
                                        <td class="text-center"><?php echo $i;?></td>
                                        <td ><?php echo $item['deduct_detail'];?></td>
                                        <td class="text-right"><?php echo number_format($item['principal'], 2);?></td>
                                        <td class="text-right"><?php echo number_format($item['interest'], 2);?></td>
                                        <td class="text-right"><?php echo number_format($item['principal']+$item['interest'], 2);?></td>
                                        <td class="text-center">
                                            <button type="button" class=" btn-small <?php echo $item['run_status'] != '0' ? "btn-default": "btn-primary";?> "
                                                    data-profile_id="<?=$item['profile_id']?>" data-member_id="<?=$item['member_id']?>" data-deduct_code="<?=$item['deduct_code']?>" data-ref_id="<?=$item['ref_id']?>"
                                                    onclick="dialogOpen(this)" <?php echo $item['run_status'] != '0' ? "disabled" : ""; ?>>
                                                <i class="fa fa-list"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php $i++; }?>
                                    <tr>
                                        <td colspan="4" class="text-center">รวมทั้งหมด</td>
                                        <td class="text-right"><span class="text-underline-double"><?php echo number_format($pt+$it, 2);?></span></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <?}else{ ?>
                                        <tr>
                                            <td colspan="6" class="text-center">ไม่พบข้อมูลเรียกเก็บ</td>
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
<?php $this->load->view('search_member_new_modal'); ?>
<div class="modal fade" id="search_member_loan_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ข้อมูลสมาชิก</h4>
            </div>
            <div class="modal-body">
                <div class="input-with-icon">
                    <input class="form-control input-thick pill m-b-2" type="text" placeholder="กรอกเลขทะเบียนหรือชื่อ-สกุล" name="search_text" id="search_member_loan">
                    <span class="icon icon-search input-icon"></span>
                </div>

                <div class="bs-example" data-example-id="striped-table">
                    <table class="table table-striped">
                        <tbody id="result_member_search">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="input_id">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view("dismiss/keep/dialog_dismiss_view");?>
<script type="application/javascript">
    $('#search_member_loan').keyup(function(){
        var txt = $(this).val();
        if(txt != ''){
            $.ajax({
                url:base_url+"/ajax/search_member_jquery",
                method:"post",
                data:{search:txt, member_id_not_allow: $('#member_id').val()},
                dataType:"text",
                success:function(data)
                {
                    //console.log(data);
                    $('#result_member_search').html(data);
                }
            });
        }else{

        }
    });
    function search_member_modal(id){
        $('#input_id').val(id);
        $('#search_member_loan_modal').modal('show');
    }

    $(document).on('change', "#profile_montn, #profile_year", function(){
        receiver().then((html) => {

        }).catch((err) =>{

        })
    });

    const receiver = (year, month) =>{
        return new Promise((resolve, reject) => {
            $.post(base_url+"finance/get_keeping_month", data, (txt) => {
                if(txt == ""){
                    return  reject({error: 'handler error for finance keeping month.'})
                }
                return resolve(txt);
            });
        });
    }

    const receiverKeepList = (data) => {
        return new Promise((resolve, reject) => {
           $.post(base_url+"dismiss/get_keep_items", data, (res, status, xhr) => {
              console.log(status);
               if(status === 'success'){
                   return resolve(res);
               }
               return reject(xhr);
           })
        });
    }

    const dialogOpen = (element) => {
        let data = {};
        const selector = $(element);
        data.profile_id= selector.data("profile_id");
        data.member_id=selector.data("member_id");
        data.deduct_code=selector.data("deduct_code");
        data.ref_id=selector.data("ref_id");
        receiverKeepList(data).then((res) => {
            console.log(res);
            $("#container-keeping").html(res);
        }).then(() => {
            $("#dialog_dismiss_view").modal("show");
        }).catch(xhr => {
            console.log("error: ",xhr.responseText)
        });
    }

    const dialogClose = () => {
        $("#dialog_dismiss_view").modal("dismiss");
    }

    const dialogAllOpen = (element) => {
        let data = {};
        const selector = $(element);
        data.profile_id = selector.data("profile_id");
        data.member_id = selector.data("member_id");
        receiverKeepAllList(data).then((res) => {
            console.log(res)
            $("#container-all-keeping").html(res);
        }).then(() => {
            $("#dialog_dismiss_all_view").modal("show")
        }).catch((xhr) => {
            console.log("error: ",xhr.responseText)
        });
    }

    const receiverKeepAllList = (data) => {
        return new Promise((resolve, reject) => {
            $.post(base_url+"dismiss/get_keep_item_all", data, (res, status, xhr) => {
               if(status === "success"){
                   return resolve(res);
               }
               return reject(xhr);
            });
        });
    }

    $(document).on("change","#profile_month,#profile_year",function () {
        let data = {};
        data.month = $('#profile_month').val();
        data.year= $('#profile_year').val();
        data.member_id = $('.member_id').val();
        searchMonthYear(data).then((res) => {
            console.log(res)
            $("#keeping-list").replaceWith($(res).find("#keeping-list"));
        });
    });

    const change_year = (x,y) => {
        let list = [];
        let data = {};
        let i = 0;
        data.profile_year = $('#profile_year').val();
        data.run_id = list;
        console.log("data:", data);
        $.post(base_url+"dismiss/get_year", data, (txt) => {
            $('#profile_month').empty();
            console.log("data:", txt);
            var obj = JSON.parse(txt);
            console.log("data:", obj.rs_year);
            var month = {1:"มกราคม",2:"กุมภาพันธ์",3:"มีนาคม",4:"เมษายน",5:"พฤษภาคม",6:"มิถุนายน",7:"กรกฎาคม",8:"สิงหาคม",9:"กันยายน",10:"ตุลาคม",11:"พฤศจิกายน",12:"ธันวาคม"};
            var i;
            for (i = 0; i < obj.rs_year.length; i++) {
                console.log( obj.rs_year[i].profile_month);
                console.log(month[obj.rs_year[i].profile_month]);
                $('#profile_month').append(`<option value="`+obj.rs_year[i].profile_month+`">`+month[obj.rs_year[i].profile_month]+`</option>`);
            }
        });
    }

    const searchMonthYear = (data) => {
        return new Promise((resolve, reject) => {
            $.get(base_url+"dismiss/keep", data, (res, status, xhr) => {
                if(status === "success"){
                    return resolve(res);
                }
                return reject(xhr);
            });
        });
    }

    const dismiss_result = () => {
        let list = [];
        let data = {};
        let i = 0;
        $('#container-all-keeping input[type=checkbox]:checked').each(function(i, v){
            console.log($(this).val());
            list[i] = $(this).val();
            i++;
        });
        data.profile_id = $('#profile_id').val();
        data.member_id = $('.member_id').val();
        data.profile_month = $('#profile_month').val();
        data.profile_year = $('#profile_year').val();
        data.run_id = list;
        //console.log("data:", data);
        $.post(base_url+"dismiss/run_process", data, (txt) => {
            if(data='success') {
                swal({
                        title: "ทำรายการสำเร็จ",
                        type: "success",
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: 'ตกลง',
                        closeOnConfirm: false,
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            window.location.reload();
                        } else {

                        }
                    });
                $(this).attr('disabled', false)
            }else{
                $('#alertNotFindModal').appendTo("body").modal('show')
            }
        });

    }
    const dismiss_only_result = () => {
        let list = [];
        let data = {};
        let i = 0;
        $('#container-keeping input[type=checkbox]:checked').each(function(i, v){
            console.log($(this).val());
            list[i] = $(this).val();
            i++;
        });
        data.profile_id = $('#profile_id').val();
        data.member_id = $('.member_id').val();
        data.profile_month = $('#profile_month').val();
        data.profile_year = $('#profile_year').val();
        data.run_id = list;
        //console.log("data:", data);
        $.post(base_url+"dismiss/run_process", data, (txt) => {
            if(data='success') {
                swal({
                        title: "ทำรายการสำเร็จ",
                        type: "success",
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: 'ตกลง',
                        closeOnConfirm: false,
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            window.location.reload();
                        } else {

                        }
                    });
                $(this).attr('disabled', false)
            }else{
                $('#alertNotFindModal').appendTo("body").modal('show')
            }
        });

    }


</script>
