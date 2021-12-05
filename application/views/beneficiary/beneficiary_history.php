<div class="beneficiary_history">
    <h3>ประวัติการแก้ไขข้อมูล</h3>
    <div class="g24-col-sm-24">
        <table class="table table-bordered table-striped table-center">
            <thead>
                <tr class="bg-primary">
                    <th style="width:15%">วันที่เวลา</th>
                    <th style="width:75%">รายการแก้ไข</th>
                    <th style="width:10%">ผู้ทำรายการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($history)){?>
                    <?php foreach ($history as $index => $item){ ?>
                <tr>
                    <td><?php echo $this->center_function->ConvertToThaiDate($item['created_at']); ?></td>
                    <td>
                        <?php
                        foreach ($item['detail'] as  $key => $value) { ?>
                            <a href="#" data-change-name="<?php echo @$value['name']; ?>" data-change-id="<?php echo @$value['id']; ?>" data-target="#changeModal" class="change_link_pop_up"><?php echo @$value['name'] ?></a>
                            <?php if($key < sizeof($item['detail'])-1){?>
                                ,&nbsp;
                            <?php } ?>
                        <?}?>
                    </td>
                    <td><?php echo @$item['user_name']; ?></td>
                </tr>
                <?php } ?>
                <?php } else{ ?>
                <tr>
                    <td colspan="3">ไม่พบข้อมูล</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script type="application/javascript">
    $(".change_link_pop_up").click(function() {
        let id = $(this).attr("data-change-id");
        let name = $(this).attr("data-change-name");
        new Promise(((resolve, reject) => {
            $.ajax({
                type: "POST",
                url: base_url+'beneficiary/get_change_detail',
                data: {
                    id : id
                },
                success: function(data) {
                    resolve(data);
                    return;
                }
            })
        })).then((data) => {
            $("#changeModal_td_name").html(name);
            $("#changeModal_td_old_val").html(data.old_value);
            $("#changeModal_td_new_val").html(data.new_value);
            $("#changeModal").modal("show");
            unblockUI();
        });

    });
</script>