//define const
const fileName = $('#filename');
const upload = $("#upload");
const fileAttach = $("#file_attach");
const bar = $('.bar');
const percent = $('.percent');
const status = $('#status');
const progress = $(".progress");

$(document).on('change', "#month, #year", function(){
    blockUI();
    $("#form1").submit();
});

$(function(){
    upload.prop("disabled", true);
    fileAttach.on('change', function(){
        if(typeof $(this).val() === "undefined" || $(this).val() === ""){
            upload.prop("disabled", true);
            fileName.html('ยังไม่ได้เลือกไฟล์');
        }else{
            upload.prop("disabled", false);
        }
        fileName.html($(this).val().split('\\').pop());
    });
});

$(function(){
    //progress.show();
    setTimeout(function(){
        $("#atm_file_upload").ajaxForm({
            beforeSend: function() {
                status.empty();
                var percentVal = '0%';
                bar.width(percentVal);
                percent.html(percentVal);
                progress.show();
            },
            uploadProgress: function(event, position, total, percentComplete) {
                var percentVal = percentComplete + '%';
                bar.width(percentVal);
                percent.html(percentVal);
            },
            complete: function(xhr) {
                progress.hide();
                let obj = xhr.responseJSON;
                let msg = '';
                console.log(obj);
                if(obj.status === 0){
                    msg = 'เกิดข้อผิดพลาด: '+ obj.msg;
                }else{
                    msg = obj.msg;
					setTimeout(function(){
                        window.location.reload();
                    }, 1000)
                }
                status.html(msg);
            }
        });
    }, 1000);

});

let activated = true;
const process = function(id){
    swal({
            title: "",
            text: "คุณต้องการประมวลผลไฟล์นี้ใช่หรือไม่",
            type: "warning",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "ตกลง",
            cancelButtonText: "ยกเลิก",
            showCancelButton: true,
            closeOnConfirm: true,
            showLoaderOnConfirm: true
        },
        function(isConfirm) {
            if(isConfirm) {
                activated = false;
                $('.fa-play').addClass('spinner_in').removeClass('fa-play');
                $.post(base_url+"deposit_atm/process_withdraw_file", {id : id},function (res) {
                    console.log(res);
                    if(res.msg === "success") {
                        swal("เสร็จสิ้น!", "", "success");
                        window.location.reload();
                    }else{
                        swal("ไม่สำเร็จ!", "", "error");
                        window.location.reload();
                    }
                    $('.spinner_in').addClass('fa-play').removeClass('spinner_in');
                    activated = true;
                });
            }else{
                swal("ยกเลิกแล้ว", "", "success");
            }
        });
};

const save = function(id){
    if(!activated) return;
    swal({
            title: "",
            text: "คุณต้องบันทึกรายการนี้ใช่หรือไม่",
            type: "warning",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "ตกลง",
            cancelButtonText: "ยกเลิก",
            showCancelButton: true,
            closeOnConfirm: true,
            showLoaderOnConfirm: true
        },
        function(isConfirm) {
            if(isConfirm) {
                activated = false;
                $('.fa-save').addClass('spinner_in').removeClass('fa-save');
                $.post(base_url+"deposit_atm/receive_file_transaction_data", {id: id},function (res) {
                    console.log(res);
                    if(res.status === 1){
                        swal("เสร็จสิ้น!", "", "success");
                        window.location.reload();
                    }else{
                        swal("ไม่สำเร็จ!", "", "error");
                        window.location.reload();
                    }
                    $('.spinner_in').addClass('fa-save').removeClass('spinner_in');

                    activated = true;
                });
            }else{
                swal("ยกเลิกแล้ว", "", "success");
            }
        })
};

const delete_file = function(id){
    if(!activated) return;
    swal({
            title: "",
            text: "คุณต้องลบไฟล์ข้อมูลนี้ใช่หรือไม่",
            type: "warning",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "ตกลง",
            cancelButtonText: "ยกเลิก",
            showCancelButton: true,
            closeOnConfirm: true,
            showLoaderOnConfirm: true
        },
        function(isConfirm) {
            if(isConfirm) {
                activated = false;
                $('.fa-save').addClass('spinner_in').removeClass('fa-save');
                $.post(base_url+"deposit_atm/delete_file_update", {id: id},function (res) {
                    console.log(res);
                    if(res.status === 1){
                        swal("เสร็จสิ้น!", "", "success");
                        window.location.reload();
                    }else{
                        swal("ไม่สำเร็จ!", "", "error");
                    }
                    $('.spinner_in').addClass('fa-save').removeClass('spinner_in');

                    activated = true;
                });
            }else{
                swal("ยกเลิกแล้ว", "", "success");
            }
        });
};

function view_receive_file(id){
    const table = $("#verify_list");
    const body = table.find('tbody');
    blockUI();
    $.get(base_url+'deposit_atm/check_receive_file', {id: id}, function(xhr){
        unblockUI();
        body.html(xhr);
        $('#modal_receive_check_list').modal('toggle');
    });
}

var call_upload = function(){
    if(!activated) return;
    swal({
        title: "",
        text: "คุณต้องอัพโหลดไฟล์ข้อมูลนี้ใช่หรือไม่",
        type: "warning",
        confirmButtonClass: "btn-primary",
        confirmButtonText: "ตกลง",
        cancelButtonText: "ยกเลิก",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: true
    },function(isConfirm){
        if(isConfirm){
            upload_file_ajax();
        }
    });
}

var upload_file_ajax = function(){
    var file_data = $('#file_attach').prop('files')[0];
    if(file_data != undefined) {
        var form_data = new FormData();
        form_data.append('file', file_data);
        $.ajax({
            type: 'POST',
            url: base_url+"deposit_atm/upload_file_withdraw",
            contentType: false,
            processData: false,
            data: form_data,
            success:function(response) {
				console.log(response);
                if(response.status === 1) {
                    swal("อัพไฟล์สำเร็จ", response.msg, "success");
                    setTimeout(function(){
                        window.location.reload();
                    }, 1500)
                } else{
                    swal("อัพไฟล์ไม่สำเร็จ", response.msg, "error");
                }
                activated = true;
                $('#file_attach').val('');
            }
        });
    }else{
        swal("อัพไฟล์ไม่สำเร็จ เนื่องจากเกิดข้อผิดพลาดบางอย่าง");
    }
    return false;
}
