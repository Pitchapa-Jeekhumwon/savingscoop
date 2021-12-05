<div class="modal fade" id="edit_menu_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title">แก้ไขเมนู</h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-8 control-label right"> รหัสเมนู </label>
                        <div class="g24-col-sm-11">
                            <div class="form-group">
                                <input id="edit_menu_id" name="edit_menu_id" class="form-control" type="text" value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-8 control-label right"> ชื่อเมนู </label>
                        <div class="g24-col-sm-11">
                            <div class="form-group">
                                <input id="edit_menu_name" name="edit_menu_name" class="form-control" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-8 control-label right"> url </label>
                        <div class="g24-col-sm-11">
                            <div class="form-group">
                                <input id="edit_menu_url" name="edit_menu_url" class="form-control" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-8 control-label right"> ไอคอน </label>
                        <div class="g24-col-sm-8">
                            <input id="edit_menu_icon" name="edit_menu_icon" class="form-control" type="text" value="" readonly>
                        </div>
                        <div class="g24-col-sm-4" id="menu_icon">
                            <a data-toggle="modal" data-target="#myModal_icon" href="#" onclick="get_edit_menu_icon();">
                                <div id="show_edit_img_icon">
                                    เพิ่มไอคอน
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-8 control-label right"> รูปภาพ </label>
                        <div class="g24-col-sm-8">
                            <input id="edit_menu_img" name="edit_menu_img" class="form-control" type="text" value="" readonly>
                        </div>
                        <div class="g24-col-sm-4" id="menu_img">
                            <a data-toggle="modal" data-target="#myModal_icon_web" href="#" onclick="get_edit_menu_img();">
                                <div id="show_edit_img_icon_web">
                                    เพิ่มรูปภาพ
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="check_edit_data()">บันทึก</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>

<script>
    var get_menu_icon = '';
    var get_menu_img = '';
    function edit_data(menu_id, menu_name, menu_url, menu_icon ,menu_img){
        console.log('icon', menu_icon);
        document.getElementById("edit_menu_id").value = menu_id;
        document.getElementById("edit_menu_name").value = menu_name;
        document.getElementById("edit_menu_url").value = menu_url;

        if(menu_icon != ''){
            document.getElementById("edit_menu_icon").value = menu_icon;
            document.getElementById("show_edit_img_icon").innerHTML = '<span class="icon '+menu_icon+' fa-4x" ></span>';
        }else{
            document.getElementById("edit_menu_icon").value = '';
            document.getElementById("show_edit_img_icon").innerHTML = 'เพิ่มไอคอน';
        }

        if(menu_img != ''){
            document.getElementById("edit_menu_img").value = menu_img;
            document.getElementById("show_edit_img_icon_web").innerHTML = '<img src="<?php echo base_url(PROJECTPATH . "/assets/images/icon_web/"); ?>'+menu_img+'" class="img-responsive m-auto" width="50" style="border-radius: 8px;position: relative;float:left;"> ';
        }else{
            document.getElementById("edit_menu_img").value = '';
            document.getElementById("show_edit_img_icon_web").innerHTML = 'เพิ่มรูป';
        }

        this.get_menu_icon = menu_icon;
        this.get_menu_img = menu_img;

    }

    function get_edit_menu_img(){
        select_icon_web(this.get_menu_img);
    }
    function get_edit_menu_icon(){
        select_icon(this.get_menu_icon);
    }

    function check_edit_data(){
        var menu_id = document.getElementById("edit_menu_id").value;
        var menu_name = document.getElementById("edit_menu_name").value;
        var menu_url = document.getElementById("edit_menu_url").value;
        var menu_icon = document.getElementById("edit_menu_icon").value;
        var menu_img = document.getElementById("edit_menu_img").value;

        // console.log('menu_id', menu_id);
        // console.log('menu_name', menu_name);
        // console.log('menu_url', menu_url);
        // console.log('menu_icon', menu_icon);
        // console.log('menu_img', menu_img);

        var url = base_url+"/Setting_main_menu/edit_data";

        $.ajax({
            url:url,
            method:"post",
            data:{
                menu_id: menu_id,
                menu_name: menu_name,
                menu_url: menu_url,
                menu_icon: menu_icon,
                menu_img: menu_img
            },
            dataType:"text",
            success:function(data)
            {
                if(data == 'success'){
                    swal("แก้ไข!", "ทำรายการสำเร็จแล้ว", "success");
                    location.reload()
                }
            }
        });

    }
</script>