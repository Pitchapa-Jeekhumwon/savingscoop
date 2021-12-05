<div class="layout-content">
    <div class="layout-content-body">
        <div class="row">
            <div class="form-group">
                <div class="col-sm-6">
                    <h1 class="title_top">ดาวน์โหลดข้อมูลยอดสรุปกู้เงินฉุกเฉิน ATM </h1>
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
                    <div class="g24-col-sm-24">
                        <label class="g24-col-sm-10 control-label">ดาวน์โหลดไฟล์สรุปยอดเงินกู้ ATM :</label>
                        <a class="btn btn-primary" href="<?php echo base_url('loan_atm/down_load_file');?>" title="ดาวน์โหลด" target="_blank">
                            <span><i class="fa fa-download"></i> ดาวน์โหลด</span>
                        </a>
                    </div>
                    <div class="g24-col-sm-24">
                        <label class="g24-col-sm-10 control-label">ข้อมูล ณ</label>                        
                        <label class="g24-col-sm-10 control-label text-left"><span id="time"></span></label>					
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	function updateClock() {
		var now = new Date(), // current date
			time = 'เวลา '+now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds(), // again, you get the idea

			// a cleaner way than string concatenation
			date = [now.getDate(), 
					//months[now.getMonth()],
					now.getMonth(),
					(now.getFullYear()+543)].join('/');

		// set the content of the element with the ID time to the formatted string
		document.getElementById('time').innerHTML = [date, time].join('  ');

		// call this function again in 1000ms
		setTimeout(updateClock, 1000);
	}
	updateClock(); // initial call
</script>
