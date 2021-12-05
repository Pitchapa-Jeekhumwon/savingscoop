<?php
//set date
$year = @$_GET['year'] ? $_GET['year'] : date('Y');
$month = @$_GET['month'] ? $_GET['month'] : date('n');
$full_date = $year."-".str_pad($month, 2 ,'0', 0)."-01";
?>
<div class="layout-content">
    <div class="layout-content-body">
        <style>

            @keyframes spinner_in {
                to {transform: rotate(360deg);}
            }

            .spinner_in:before {
                content: '';
                box-sizing: border-box;
                position: absolute;
                top: 50%;
                left: 50%;
                width: 20px;
                height: 20px;
                margin-top: -10px;
                margin-left: -10px;
                border-radius: 50%;
                border-top: 4px solid #fcf9ff;
                border-right: 2px solid transparent;
                animation: spinner_in .6s linear infinite;
            }

            .btn-circle {
                display: inline-flex; margin: auto; justify-content: center; width: 30px; height: 30px;text-align: center; padding: 6px 0; font-size: 12px; line-height: 1.428571429; border-radius: 15px !important;
            }
            .btn-circle:hover, .btn-circle:focus{
               
            }
            .input-with-icon .form-control {
                padding-left: 54px;
            }

            .content-process{ display: flex; justify-content: center;}
            .progress { display: none; position:relative; width:400px; border: 1px solid #ddd; padding: 1px; border-radius: 3px; }
            .bar { background-color: #B4F5B4; width:0%; height:20px !important; border-radius: 3px; }
            .percent { position:absolute; display:inline-block; top:3px; left:48%; top: -1px; font-size: smaller;}

        </style>
        <div class="row">
            <div class="form-group">
                <div class="col-sm-6">
                    <h1 class="title_top">ปรับปรุงยอดกดเงินฝาก ATM</h1>
                    <?php $this->load->view('breadcrumb'); ?>
                </div>
                <div class="col-sm-6">
                    <div class="g24-col-sm-24" style="text-align:right;padding-right:0px;margin-right:0px;">
                    </div>
                </div>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:15px !important;">
                    <h3>อัพโหลดไฟล์การทำรายการถอนเงินฝาก ATM รายวัน</h3>
					<div class="g24-col-sm-24" style="text-align:right;padding-right:0px;margin-right:0px;">
                        <form class="form-group" id="atm_file_upload" name="atm_file_upload" method="post" enctype="multipart/form-data" action="">
                        <label class="g24-col-sm-6 control-label ">นำเข้าข้อมูลรายการกด ATM :</label>
                        <div class="g24-col-sm-6">
                            <span id="filename">ยังไม่ได้เลือกไฟล์</span>
                            <label class="fileContainer btn btn-info">
                                <span class="icon icon-paperclip"></span>
                                เลือกไฟล์
                                <input type="file" class="form-control" id="file_attach" name="file_attach[]" value="" multiple>
                            </label>
                        </div>
                        <div class="g24-col-md-3">
                            <button class="btn btn-primary" type="button" id="upload" onclick="call_upload()">
                                <span><i class="fa fa-cloud-upload"></i> อัพโหลด</span>
                            </button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <h3>รายการอัพโหลดไฟล์การทำรายการถอนเงินฝาก ATM รายวัน</h3>
                    <div class="row">
                        <form id="form1" method="get" action="?" enctype="multipart/form-data" accept-charset="UTF-8">
                            <div class="g24-col-sm-20">
                                <label class="g24-col-sm-1 control-label">เดือน</label>
                                <div class="form-group g24-col-sm-3">
                                    <select class="form-control" name="month" id="month">
                                        <?php foreach ($this->center_function->month_arr() as $key => $val){ ?>
                                            <option value="<?php echo $key?>" <?php echo date('n', strtotime($full_date)) == $key ? 'selected' :'' ?>><?php echo $val; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <label class="g24-col-sm-1 control-label">ปี</label>
                                <div class="form-group g24-col-sm-3">
                                    <select class="form-control" name="year" id="year">
                                        <?php for($year = date('Y')-5; $year <= date('Y')+5; $year++){ ?>
                                            <option value="<?php echo $year?>" <?php echo date('Y', strtotime($full_date)) == $year ? 'selected' :'' ?>><?php echo $year+543; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table class="table table-bordered table-striped table-center">
                        <thead class="bg-primary">
                        <tr>
                            <th width="5%">ลำดับ</th>
                            <th width="10%">วันที่</th>
                            <th width="30%">ชื่อไฟล์</th>
                            <th width="10%">สถานะ</th>
                            <th width="10%">วันที่อัพโหลด</th>
                            <th width="10%">วันที่ไฟล์</th>
                            <th width="10%">วันที่ทำรายการ</th>
                            <th width="15%">เครื่องมือ</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(date('Y-m-t', strtotime($full_date)) <= date('Y-m-t')){
                            for($date = 0; $date <= date('t', strtotime($full_date)); $date++){
                                $file = $list[date('Y-m-d', strtotime($full_date." +".$date." day"))];
                                if(date('Y-m-d', strtotime($full_date." +".$date." day")) > date('Y-m-d')
                                    || date('Y-m-t', strtotime($full_date)) < date('Y-m-d', strtotime($full_date." +".$date." day"))){
                                    continue;
                                }
                                ?>
                                <tr>
                                    <td><?php echo $date+1;?></td>
                                    <td><?php echo $this->center_function->ConvertToThaiDate( date('Y-m-d', strtotime($full_date." +".$date." day")), 1); ?></td>
                                    <?php if(!empty($file['file_name'])){ ?>
                                        <td class="text-left"><?php echo empty($file['file_name']) ? "" : $file['file_name']; ?></td>
                                        <td><?php echo empty($file['active_status']) ? "-" : ($file['active_status'] == 0 ? 'รอประมวลผล' : 'ประมวลผลแล้ว'); ?></td>
                                        <td><?php echo empty($file['createdatetime']) ? "-" : $this->center_function->ConvertToThaiDate( $file['createdatetime'], 1); ?></td>
                                        <td><?php echo empty($file['file_date']) ? "-" : $this->center_function->ConvertToThaiDate( $file['file_date'], 2, 1); ?></td>
                                        <td><?php echo empty($file['submit_date']) ? "ยังไม่ทำรายการ" : $this->center_function->ConvertToThaiDate( $file['submit_date'], 1); ?></td>
                                        <td>
                                            <?php if(!empty($file['id'])){?>
                                                <button class="btn btn-circle btn-primary" <?php echo !empty($file['active_status']) ? '' : 'disabled="disabled"' ?> onclick="view_receive_file(<?php echo $file['id']; ?>)" title="แสดงข้อมูล">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </button>
                                            <?php } ?>
											<?php if(!empty($file['id'])){?>
											<button class="btn btn-circle btn-primary" <?php echo $file['active_status'] == 0 ? '' : 'disabled="disabled"' ?> onclick="process(<?php echo $file['id']; ?>)" title="อ่านไฟล์ข้อมูล">
												<i class="fa fa-play" aria-hidden="true"></i>
											</button>
											<?php }?>
                                            <?php if(!empty($file['id'])){?>
                                                <button class="btn btn-circle btn-primary" <?php echo $file['status'] <> 2 && $file['active_status'] == 1 ? '' : 'disabled="disabled"' ?> onclick="save(<?php echo $file['id']; ?>)" title="บันทึกข้อมูล">
                                                    <i class="fa fa-save" aria-hidden="true"></i>
                                                </button>
                                            <?php }?>
                                            <?php if(!empty($file['id'])){?>
                                                <button class="btn btn-circle btn-primary" <?php echo $file['status'] <> 2 && $file['active_status'] == 0 ? '' : 'disabled="disabled"' ?> onclick="delete_file(<?php echo $file['id']; ?>)" title="ลบไฟล์">
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </button>
                                            <?php }?>
                                        </td>
                                    <?php }else{ ?>
                                        <td colspan="6" style="text-align: center">ไม่พบข้อมูลรายการ</td>
                                    <?php } ?>
                                </tr>
                            <?php }}else{ ?>
                            <tr>
                                <td style="text-align: center" colspan="7"> ไม่พบข้อมูลรายการ </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_receive_check_list" role="dialog">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header modal-header-info">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">ตรวจสอบข้อมูลการทำรายการถอนเงินฝาก ATM รายวัน </h3>
            </div>
            <div class="modal-body">
                <table class="table" id="verify_list">
                    <thead>
                        <tr>
                            <th class="text-center">ลำดับ</th>
                            <th class="text-center">เลขทะเบียนสมาชิก</th>
                            <th class="text-center">เลขที่บัญชีเงินฝาก</th>
                            <th class="text-center">เลขที่บัญชีเงินฝากสหกรณ์</th>
                            <th class="text-center">ยอดเงินทำรายการ</th>
                            <th class="text-center">วันที่ทำรายการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4">ไม่พบรายการ</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer text-center">
                <button class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>

<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH . 'assets/js/deposit_update_transaction.js?v=' . $v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
