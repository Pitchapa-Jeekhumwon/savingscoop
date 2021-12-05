<div class="modal fade" id="myModal_icon" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title">เลือกไอคอน</h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-7">
                    </div>
                    <div class="form-group col-sm-5">
                        <input id="show_icon" name="show_icon" class="form-control" type="text" value="" readonly>
                    </div>
                </div>
                <div class="row">
                    <?php foreach ($icon as $icon_name) { ?>
                        <div class="form-group g24-col-sm-3">
                            <a onclick="select_icon('<?php echo $icon_name;?>')" >
                                <div>
                                    <span id="select_<?php echo $icon_name;?>" class="icon <?php echo $icon_name;?> fa-3x" style="color: #757575;"></span>
                                </div>
                            </a>
                        </div>
                    <? }?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="save_icon()">บันทึก</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>

<script>
    var before_icon = '';

    function select_icon(icon_name){
        console.log('icon_name',icon_name);
        var show_icon = document.getElementById("show_icon");
        var select_icon = document.getElementById("select_"+icon_name);
        var select_icon_before = document.getElementById("select_"+this.before_icon);

        if(show_icon != this.before_icon){
            show_icon.value = icon_name;
            select_icon.classList.add("border_icon_select");
            if(this.before_icon != ''){
                select_icon_before.classList.remove("border_icon_select");
            }
            this.before_icon = icon_name;
        }else{
            select_icon_before.classList.remove("border_icon_select");
            show_icon.value = '';
            this.before_icon = '';
        }
    }

    function save_icon(part_img){
        console.log('part_img',before_part_img);
        $('#myModal_icon').modal('toggle');

        var insert_menu_icon = document.getElementById("insert_menu_icon");
        insert_menu_icon.value = before_icon;

        document.getElementById("show_img_icon").innerHTML = '<span class="icon '+before_icon+' fa-4x" ></span>';

        var edit_menu_icon = document.getElementById("edit_menu_icon");
        edit_menu_icon.value = before_icon;

        document.getElementById("show_edit_img_icon").innerHTML = '<span class="icon '+before_icon+' fa-4x" ></span>';
    }
</script>