<div class="row gutter-xs">
    <div class="col-md-3">
        <div class="panel panel-body" style="padding-top:0px !important;">
            <div class="layout-main">
                <!--                        <div class="layout-sidebar">-->
                <!--                            <div class="layout-sidebar-body">-->
                <div class="custom-scrollbar">
                    <nav id="sidenav" class="sidenav-collapse collapse">
                        <?php
                        $start_menu_id = @$menu_paths[0]["id"];
                        ?>
                        <ul class="sidenav">
                            <?php

                            if($menu_id == -1) {
                                $side_menus = array(array("id" => 0, "name" => "Program", "submenus" => $side_menus));
                            }
                            foreach($side_menus as $menu) {
                                ?>

                                <li class="sidenav-heading">
                                    <a
                                        <?php if(@$menu["onclick"]!=''){ ?>
                                            href="#" onclick="<?php echo @$menu["onclick"]; ?>"
                                        <?php }else{ ?>
                                            href="<?php echo '#'; ?>"
                                        <?php } ?>
                                        <?php if(@$menu["hidden"]=='hidden'){ echo "style='display:none'"; }?> aria-haspopup="true" target="<?php echo $submenu["target"]; ?>" style='display:contents;' onclick="editFunction('<?php echo $menu['id'];?>')">
                                        <span class="sidenav-icon icon  <?php echo $menu["icon"]; ?>"></span>
                                        <!--                                                        <span style='font-size: 18px;'>--><?php //echo $menu["name"]; ?><!--</span>-->
                                        <!--                                                        <span class="sidenav-label">--><?php //echo $menu["name"]; ?><!--</span>-->
                                        <span style='font-size: 18px;'><?php echo $menu["name"]; ?></span>
                                    </a>

                                    <!--buttom>
                                                        <span style='font-size: 18px;' onclick="editFunction('<?php echo $menu['id'];?>')">แก้ไข</span>
                                                    </buttom-->
                                    <?php
                                    if(!empty($menu["submenus"])) { ?>
                                        <ul class="sidenav-subnav collapse has-subnav">
                                            <?php foreach(@$menu["submenus"] as $submenu) {
                                                if(!isset($submenu["submenus"])){
                                                    $submenu["submenus"] = array();
                                                }
                                                ?>
                                                <?php
                                                if($submenu["url"] == $current_path) {
                                                    $_SESSION['permission_id'] = @$submenu['id'];
                                                }
                                                ?>
                                                <li class="sidenav-item ">
                                                    <a
                                                        <?php if(@$submenu["onclick"]!=''){ ?>
                                                            href="#" onclick="<?php echo @$submenu["onclick"]; ?>"
                                                        <?php }else{ ?>

                                                        <?php } ?>
                                                        <?php if(@$submenu["hidden"]=='hidden'){ echo "style='display:none'"; }?> aria-haspopup="true" target="<?php echo $submenu["target"]; ?>" onclick="editFunction('<?php echo $submenu['id'];?>')">
                                                        <span class="sidenav-icon icon <?php echo $submenu["icon"]; ?>"></span>
                                                        <span class="sidenav-label"><?php echo $submenu["name"]; ?></span>
                                                    </a>
                                                    <?php if(!empty($submenu["submenus"]) && $start_menu_id != 0) { ?>
                                                        <ul class="sidenav-subnav collapse">
                                                            <?php foreach($submenu["submenus"] as $submenu2) { ?>

                                                                <li <?php if($submenu2["url"] == $current_path) { ?> class="active"<?php } ?>>
                                                                    <a
                                                                        <?php if(@$submenu2["onclick"]!=''){ ?>
                                                                            href="#" onclick="<?php echo @$submenu2["onclick"]; ?>"
                                                                        <?php }else{ ?>

                                                                        <?php } ?>
                                                                            target="<?php echo $submenu2["target"]; ?>"
                                                                            style="padding: 8px 15px 8px 84px;" onclick="editFunction('<?php echo $submenu2['id'];?>')">
                                                                        <span class="sidenav-icon icon <?php echo $submenu2["icon"]; ?>"></span>
                                                                        <span class="sidenav-label"><?php echo $submenu2["name"]; ?></span>
                                                                    </a>

                                                                    <?php
                                                                    // เพิ่ม
                                                                    if(!empty($submenu2["submenus"]) && $start_menu_id != 0) { ?>
                                                                        <ul class="sidenav-subnav collapse">
                                                                            <?php foreach($submenu2["submenus"] as $submenu3) { ?>

                                                                                <li <?php if($submenu3["url"] == $current_path) { ?> class="active"<?php } ?>>
                                                                                    <a
                                                                                        <?php if(@$submenu3["onclick"]!=''){ ?>
                                                                                            href="#" onclick="<?php echo @$submenu3["onclick"]; ?>"
                                                                                        <?php }else{ ?>

                                                                                        <?php } ?>
                                                                                            target="<?php echo $submenu3["target"]; ?>"
                                                                                            style="padding: 8px 15px 8px 126px;" onclick="editFunction('<?php echo $submenu3['id'];?>')">
                                                                                        <span class="sidenav-icon icon <?php echo $submenu3["icon"]; ?>"></span>
                                                                                        <span class="sidenav-label"><?php echo $submenu3["name"]; ?></span>
                                                                                    </a>

                                                                                    <?php
                                                                                    // เพิ่ม 2
                                                                                    if(!empty($submenu3["submenus"]) && $start_menu_id != 0) { ?>
                                                                                        <ul class="sidenav-subnav collapse">
                                                                                            <?php foreach($submenu3["submenus"] as $submenu4) { ?>

                                                                                                <li <?php if($submenu4["url"] == $current_path) { ?> class="active"<?php } ?>>
                                                                                                    <a
                                                                                                        <?php if(@$submenu4["onclick"]!=''){ ?>
                                                                                                            href="#" onclick="<?php echo @$submenu4["onclick"]; ?>"
                                                                                                        <?php }else{ ?>

                                                                                                        <?php } ?>
                                                                                                            target="<?php echo $submenu4["target"]; ?>"
                                                                                                            style="padding: 8px 15px 8px 168px;" onclick="editFunction('<?php echo $submenu4['id'];?>')">
                                                                                                        <span class="sidenav-icon icon <?php echo $submenu4["icon"]; ?>"></span>
                                                                                                        <span class="sidenav-label"><?php echo $submenu4["name"]; ?></span>
                                                                                                    </a>
                                                                                                </li>
                                                                                            <?php } ?>
                                                                                        </ul>
                                                                                    <?php }
                                                                                    // ถึงตรงนี้
                                                                                    ?>
                                                                                </li>
                                                                            <?php } ?>
                                                                        </ul>
                                                                    <?php }
                                                                    // ถึงตรงนี้
                                                                    ?>

                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    <?php } ?>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                        <?php
                                    }
                                    //                                                }
                                    ?>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </nav>
                </div>
                <!--                            </div>-->
                <!--                        </div>-->
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="panel panel-body" style="padding-top:0px !important;">
            <div class="col-md-12">
                <div id="data_menu_head">
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <div id="data_menu">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function editFunction(menu_id){
        console.log(menu_id);
        console.log("<?php echo base_url(PROJECTPATH);?>" ,base_url);
        if(menu_id != 'null'){
            var url = base_url+"/Setting_main_menu/show_detail";
        }else{
            var url = "<?php echo base_url(PROJECTPATH);?>Setting_main_menu/show_detail";
        }
        $.ajax({
            url:url,
            method:"post",
            data:{menu_id:menu_id},
            dataType:"text",
            success:function(data)
            {
                $('#data_menu_head').html(data);
            }
        });
    }
    editFunction('null');
</script>