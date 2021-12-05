<style type="text/css">
    .overdue>thead>tr>th{
        text-align: center;
    }
</style>
<div class="table-responsive">
    <table class="table overdue">
        <thead>
            <tr>
                <th>#</th>
                <th>ประเภท</th>
                <th>สัญญาเงินกู้</th>
                <th>ยอกเงินกู้</th>
                <th>ชำระต่องวด</th>
                <th>งวด</th>
                <th>เงินต้น</th>
                <th>ดอกเบี้ย</th>
            </tr>
        </thead>
        <tbody>
        <?php if(isset($non_pay) && sizeof($non_pay) > 0){ ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php } else { ?>
            <tr>
                <td colspan="7" class="text-center">ไม่พบข้อมูลรายการค้างชำระ</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>