<div class="modal fade" id="myModal_icon_web" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title">เลือกภาพ</h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-7">
                    </div>
                    <div class="form-group col-sm-5">
                        <input id="show_part_img" name="show_part_img" class="form-control" type="text" value="" readonly>
                    </div>
                </div>
                <div class="row">
                    <?php foreach ($icon_img as $part_img) { ?>
                        <a onclick="select_icon_web('<?php echo $part_img;?>')" >
                            <div class="form-group g24-col-sm-2">
                                <img id="select_<?php echo $part_img;?>" src="<?php echo base_url(PROJECTPATH . "/assets/images/icon_web/" . $part_img); ?>" class="img-responsive m-auto" width="75" style="border-radius: 8px;position: relative;">
                            </div>
                        </a>
                    <? }?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="save_icon_web()">บันทึก</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>

<script>
    var before_part_img = '';

    function select_icon_web(part_img){
        console.log('part_img',part_img);
        var show_part_img = document.getElementById("show_part_img");
        var select_icon_web = document.getElementById("select_"+part_img);
        var select_part_img = document.getElementById("select_"+this.before_part_img);

        if(part_img != this.before_part_img){
            show_part_img.value = part_img;
            select_icon_web.classList.add("border_icon_web");
            if(this.before_part_img != ''){
                select_part_img.classList.remove("border_icon_web");
            }
            this.before_part_img = part_img;
        }else{
            select_part_img.classList.remove("border_icon_web");
            show_part_img.value = '';
            this.before_part_img = '';
        }
    }

    function save_icon_web(part_img){
        console.log('part_img',before_part_img);
        $('#myModal_icon_web').modal('toggle');

        var insert_menu_img = document.getElementById("insert_menu_img");
        insert_menu_img.value = before_part_img;

        document.getElementById("show_img_icon_web").innerHTML = '<img src="<?php echo base_url(PROJECTPATH . "/assets/images/icon_web/"); ?>'+before_part_img+'" class="img-responsive m-auto" width="50" style="border-radius: 8px;position: relative;float:left;"> ';

        var edit_menu_img = document.getElementById("edit_menu_img");
        edit_menu_img.value = before_part_img;

        document.getElementById("show_edit_img_icon_web").innerHTML = '<img src="<?php echo base_url(PROJECTPATH . "/assets/images/icon_web/"); ?>'+before_part_img+'" class="img-responsive m-auto" width="50" style="border-radius: 8px;position: relative;float:left;"> ';
    }
</script>