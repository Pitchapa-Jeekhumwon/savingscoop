

<style>
    input.larger{
        width: 20px;
        height: 20px;
    }
</style>
<div class="modal fade" id="dialog_dismiss_view" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">ยกเลิกรายการเรียกเก็บ</h3>
            </div>
            <div class="modal-body col-sm-12">
                <table class="table table-striped">
                    <thead class="bg-primary">
                    <tr>
                        <th class="text-center"  style="width: 10%">เลือก</th>
                        <th class="text-center"  style="width: 45%">รายการ</th>
                        <th class="text-center"  style="width: 30%">จำนวน</th>
                    </tr>
                    </thead>
                    <tbody id="container-keeping">
                    <tr>
                        <td class="text-center">1</td>
                        <td class="text-left" >LABEL</td>
                        <td class="text-right" >100,000.00</td>
                        <td class="text-center">
                                <input class="larger" type="checkbox" value=""/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td class="text-right">
                            <span class="text-underline-double">100,000.00</span>
                        </td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="input_id">
                <button type="button" class="btn btn-primary" onclick="dismiss_only_result()">ยืนยัน</button>
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="dialog_dismiss_all_view" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">ยกเลิกรายการเรียกเก็บ</h3>
            </div>
            <div class="modal-body col-sm-12">
                <table class="table table-striped">
                    <thead class="bg-primary">
                    <tr>
                        <th class="text-center"  style="width: 10%">เลือก</th>
                        <th class="text-center"  style="width: 45%">รายการ</th>
                        <th class="text-center"  style="width: 30%">จำนวน</th>
                    </tr>
                    </thead>
                    <tbody id="container-all-keeping">

                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-right">
                            <span class="text-underline-double">100,000.00</span>
                        </td>

                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="input_id">
                <button type="button" class="btn btn-primary" onclick="dismiss_result()">ยืนยัน</button>
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>
<script>

</script>