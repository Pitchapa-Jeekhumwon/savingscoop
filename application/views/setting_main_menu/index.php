<?php
if($_GET['dev'] == 'dev'){
    echo '<pre>';print_r($side_menus);exit;
}
?>

<div class="layout-content">
    <div class="layout-content-body">
        <style>
            .center {
                text-align: center;
            }
            .right {
                text-align: right;
            }
            .modal-dialog-account {
                margin:auto;
                margin-top:7%;
            }
            label{
                padding-top:7px;
            }
            th {
                text-align: center;
            }
            .modal-dialog-cal {
                width:80% !important;
                margin:auto;
                margin-top:1%;
                margin-bottom:1%;
            }

            .modal-dialog-search {
                width: 700px;
            }
            .sidenav-heading {
                color: #9e9e9e;
                font-size: 12px;
                font-weight: 500;
                line-height: 1;
                margin-bottom: 0;
                 margin-top: 15px;
                overflow: hidden;
                 padding: 0px 0px;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-family: upbean;
                font-size: 18px;
            }
            .border_icon_web{
                border: 6px outset #067c3b;
                box-shadow: 3px 3px 5px #888888;
            }
            .border_icon_select{
                border: 4px outset #067c3b;
                box-shadow: 3px 3px 5px #888888;
            }
            .border_icon{
                border: 4px outset #067c3b;
                box-shadow: 3px 3px 5px #888888;
            }
        </style>
        <form action="<?php echo base_url(PROJECTPATH.'/Setting_main_menu/edit_menu_active'); ?>" id="form1" method="POST">
        <h1 style="margin-bottom: 0">จัดการเมนู</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
                <button class="btn btn-primary btn-lg bt-add" type="submit" style="width: 150px !important;"> บันทึก</button>
            </div>
        </div>

        <div class="row gutter-xs">
            <div class="col-md-12">
                <div class="panel panel-body" style="padding-top:15px !important;">
                    <h3 class="text-center font-menu-main p-t-sm" style="float: left;"><?php echo$menu_parent["menu_name"] ;?></h3>
                    <?php if(!empty($_GET['menu_id'])){ ?>
                    <div class="col-md-12" style="padding-right:0px;text-align:left;">
                        <a href="<?php echo base_url(PROJECTPATH . '/setting_main_menu?menu_id=' . @$menu_parent['menu_parent_id']); ?>"><h4><span class="icon fa-arrow-circle-left"></span> ย้อนกลับ <?php echo $menu_parent_before?></h4></a>
                    </div>
                    <?php }?>
                    <table class="table table-bordered table-striped table-center">
                        <thead>
                        <tr class="bg-primary">
                            <th>ลำดับ</th>
                            <th>ชื่อเมนู</th>
                            <th>URL</th>
                            <th>ไอคอน</th>
                            <th>รูปเมนู</th>
                            <th>แสดงเมนู</th>
                            <th>ซ่อนเมนูด้านซ้าย</th>
                            <th>รายละเอียด</th>
                        </tr>
                        </thead>

                        <tbody id="table_first">
                        <?php
                        if(!empty($data)) {
                            foreach ($data as $key => $value) { ?>
                                <tr>
                                    <td class="text-center"> <?php echo $key+1; ?> </td>

                                    <td class="text-center">
                                        <a href="<?php echo base_url(PROJECTPATH . '/setting_main_menu?menu_id=' . @$value['menu_id']); ?>">
                                            <?php if($value['count_menu_id'] > 0){ ?>
                                                <span class="icon icon-folder-open-o"></span>
                                            <?php } ?>
                                            <?php echo $value['menu_name']; ?>
                                        </a>
                                    </td>
                                    <td class="text-center"><?php echo $value['menu_url']; ?></td>
                                    <td class="text-center">
                                        <span class="sidenav-icon icon fa-2x <?php echo $value["menu_icon"]; ?>" style="float: none;display: inline;margin-right: 0px;"></span>
                                    </td>
                                    <td class="text-center"><img
                                                src="<?php echo base_url(PROJECTPATH . "/assets/images/icon_web/" . $value["menu_img"]); ?>"
                                                class="img-responsive m-auto" width="75"
                                                style="border-radius: 8px;position: relative;">
                                    </td>
                                    <td class="text-center">
                                        <input type="text" name="menu_active[<?php echo @$value['menu_id'];?>]" value="0" style="display:none">
                                        <input type="checkbox" name="menu_active[<?php echo @$value['menu_id'];?>]" value="1" <?php echo $value['menu_active'] == '1' ? 'checked' : '' ?> >
                                    </td>
                                    <td class="text-center">
                                        <input type="text" name="nav_hidden[<?php echo @$value['menu_id'];?>]" value="0" style="display:none">
                                        <input type="checkbox"  name="nav_hidden[<?php echo @$value['menu_id'];?>]" value="1" <?php echo $value['nav_hidden'] == '1' ? 'checked' : '' ?>>
                                    </td>
                                    <td class="text-center">
<!--                                        <a href="--><?php //echo base_url(PROJECTPATH . '/setting_main_menu?menu_id=' . @$value['menu_id']); ?><!--">-->
<!--                                            ดูรายละเอียด-->
<!--                                        </a> |-->
                                        <a data-toggle="modal" data-target="#edit_menu_modal" href="#" onclick="edit_data('<?php echo @$value['menu_id'];?>', '<?php echo @$value['menu_name'];?>', '<?php echo @$value['menu_url'];?>', '<?php echo @$value['menu_icon'];?>', '<?php echo @$value['menu_img'];?>')">
                                            แก้ไข
                                        </a>
                                        |
                                        <a>
                                            <span class="text-del del" onclick="del_data('<?php echo @$value['menu_id'] ?>')">ลบ</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php }
                        }else{ ?>
                            <tr><td colspan="9"> ไม่พบข้อมูล </td></tr>
                        <?php } ?>
                        <tr>
                            <td colspan="9">
                                <button data-toggle="modal" data-target="#insert_menu_modal" href="#" class="btn btn-primary" type="button" onclick="insert_data()" style="width: 200px;">
                                    <span class="icon icon-plus-circle"></span> เพิ่มเมนู
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </form>

        <?php //$this->load->view('Setting_main_menu/menu_list'); ?>
    </div>
</div>


<!-- Modal -->
<?php $this->load->view('setting_main_menu/edit_menu_modal'); ?>

<!-- Modal -->
<?php $this->load->view('setting_main_menu/insert_menu_modal'); ?>

<!-- Modal -->
<?php $this->load->view('setting_main_menu/model_icon_web'); ?>

<!-- Modal -->
<?php $this->load->view('setting_main_menu/model_icon');?>


<script>

    function open_modal(id){
        $('#'+id).modal('show');
    }
    $( document ).ready(function() {
        $('#search_member_modal').on('shown.bs.modal', function() {
            $('#search_member').focus();
        });
        $('#search_member').keyup(function(){
            var txt = $(this).val();
            if(txt != ''){
                $.ajax({
                    url:base_url+"/ajax/search_member_jquery",
                    method:"post",
                    data:{search:txt},
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
    });
    function del_data(menu_id){
        swal({
            title: 'ท่านต้องการลบข้อมูลใช่หรือไม่',
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                // document.location.href = base_url+'Setting_main_menu/manage_emergency_individual_delete/'+non_pay_id;
                $.ajax({
                    url:base_url+"/Setting_main_menu/del_data",
                    method:"post",
                    data:{
                        menu_id: menu_id
                    },
                    dataType:"text",
                    success:function(data)
                    {
                        if(data == 'success'){
                            swal("ลบ!", "ทำรายการสำเร็จแล้ว", "success");
                            location.reload()
                        }
                    }
                });
            } else {

            }
        });
    }

</script>