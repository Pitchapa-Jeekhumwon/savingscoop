<div class="modal fade" id="insert_menu_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title">เพิ่มเมนู</h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-8 control-label right"> รหัสเมนู </label>
                        <div class="g24-col-sm-11">
                            <div class="form-group">
                                <input id="insert_menu_id" name="insert_menu_id" class="form-control" type="text" value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-8 control-label right"> ชื่อเมนู </label>
                        <div class="g24-col-sm-11">
                            <div class="form-group">
                                <input id="insert_menu_name" name="insert_menu_name" class="form-control" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-8 control-label right"> url </label>
                        <div class="g24-col-sm-11">
                            <div class="form-group">
                                <input id="insert_menu_url" name="insert_menu_url" class="form-control" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-8 control-label right"> ไอคอน </label>
                        <div class="g24-col-sm-8">
                            <input id="insert_menu_icon" name="insert_menu_icon" class="form-control" type="text" value="" readonly>
                        </div>
                        <div class="g24-col-sm-4" id="menu_icon">
                            <a data-toggle="modal" data-target="#myModal_icon" href="#">
                                <div id="show_img_icon">
                                    เพิ่มไอคอน
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-8 control-label right"> รูปภาพ </label>
                        <div class="g24-col-sm-8">
                            <input id="insert_menu_img" name="insert_menu_img" class="form-control" type="text" value="" readonly>
                        </div>
                        <div class="g24-col-sm-4" id="menu_img">
                            <a data-toggle="modal" data-target="#myModal_icon_web" href="#">
                                <div id="show_img_icon_web">
                                    เพิ่มรูปภาพ
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="check_insert_data('<?php echo $_GET['menu_id']?>')">บันทึก</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>

<script>
    function insert_data(){
        document.getElementById("insert_menu_id").value = '';
        document.getElementById("insert_menu_name").value = '';
        document.getElementById("insert_menu_url").value = '';
        document.getElementById("insert_menu_icon").value = '';
        document.getElementById("show_img_icon").innerHTML = 'เพิ่มไอคอน';
        document.getElementById("insert_menu_img").value = '';
        document.getElementById("show_img_icon_web").innerHTML = 'เพิ่มรูป';
    }

    function check_insert_data(menu_parent_id){
        var menu_id = document.getElementById("insert_menu_id").value;
        var menu_name = document.getElementById("insert_menu_name").value;
        var menu_url = document.getElementById("insert_menu_url").value;
        var menu_icon = document.getElementById("insert_menu_icon").value;
        var menu_img = document.getElementById("insert_menu_img").value;

        console.log('----------------------------------');
        console.log('menu_id', menu_id);
        console.log('menu_parent_id', menu_parent_id);
        console.log('menu_name', menu_name);
        console.log('menu_url', menu_url);
        console.log('menu_icon', menu_icon);
        console.log('menu_img', menu_img);

        var url = base_url+"/Setting_main_menu/insert_data";

        $.ajax({
            url:url,
            method:"post",
            data:{
                menu_id: menu_id,
                menu_parent_id: menu_parent_id,
                menu_name: menu_name,
                menu_url: menu_url,
                menu_icon: menu_icon,
                menu_img: menu_img
            },
            dataType:"text",
            success:function(data)
            {
                if(data == 'success'){
                    swal("บันทึก!", "ทำรายการสำเร็จแล้ว", "success");
                    location.reload()
                }
            }
        });

    }
</script>