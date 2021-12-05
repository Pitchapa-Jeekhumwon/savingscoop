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
    setTimeout(function(){
        $("#atm_file_upload").ajaxForm({
            beforeSend: function() {
                status.empty();
                progress.show();
                var percentVal = '0%';
                bar.width(percentVal);
                percent.html(percentVal);
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
                }
                status.html(msg);
                setTimeout(function(){
					window.location.reload();
                }, 1000);
            }
        });
    }, 800);

});


function process(id){
    const table = $("#verify_list");
    const body = table.find('tbody');
    blockUI();
    $.get(base_url+'loan_atm/check_list', {id: id}, function(xhr){
        unblockUI();
        body.html(xhr);
        $('#modal_verify_check_list').modal('toggle');
    });
}

function save(id){

    swal({
            title: 'ปรับปรุงข้อมูลการผูกบัญชี',
            text: "ท่านต้องการปรับปรุงข้อมูลการผูกบัญชี",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm){
        if(isConfirm){
            blockUI();
            $.post(base_url+'loan_atm/update_contract', {id: id}, function(){
                setTimeout(function(){
                    unblockUI();
                    swal('ปรับปรุงข้อมูลการผูกบัญชีสำเร็จ','','success');
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000);
                }, 1500);
            });
        }
    });

}

function delete_file(id){

    swal({
            title: 'ลบข้อมูลการผูกบัญชี',
            text: "ท่านต้องการลบข้อมูลการผูกบัญชี",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm){
        if(isConfirm){
            blockUI();
            $.post(base_url+'loan_atm/del_verify_bank_upload', {id: id}, function(){
                setTimeout(function(){
                    unblockUI();
                    swal('ลบข้อมูลการผูกบัญชีสำเร็จ','','success');
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000);
                }, 1500);
            });
        }
    });

}
