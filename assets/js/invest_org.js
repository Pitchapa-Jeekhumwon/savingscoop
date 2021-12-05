$(document).ready(function() {
    $("#add_org").click(function() {
        $("#modal_id").val("");
        $("#add_name").val("");
        $("#edit_modal").modal("show");
    });
    $(".edit_btn").click(function() {
        $.blockUI({
            message: 'กรุณารอสักครู่...',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .5,
                color: '#fff'
            },
            baseZ: 6000,
            bindEvents: false
        });
        id = $(this).attr("data-id");
        $.post(base_url+"invest/get_organization_ajax",
        {id:id},
        function(result) {
            data = JSON.parse(result);
            $("#modal_id").val(data.data.id);
            $("#add_name").val(data.data.name);
            $("#edit_modal").modal("show");
            $.unblockUI();
        });
    });
    $(".del_btn").click(function() {
        $.blockUI({
            message: 'กรุณารอสักครู่...',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .5,
                color: '#fff'
            },
            baseZ: 6000,
            bindEvents: false
        });
        id = $(this).attr("data-id");
        $.post(base_url+"invest/edit_organization_ajax",
        {id:id,
        status:3},
        function(result) {
            window.location.href = 'organization';
            $.unblockUI();
        });
    });
    $("#submit_add").click(function() {
        text_alert = '';
        if($('#add_name').val() == ''){
            text_alert += '-ชื่อองค์กร\n';
        }

        if(text_alert != ''){
            swal("กรุณากรอกข้อมูล", text_alert, "warning")
        }else{
            $.blockUI({
                message: 'กรุณารอสักครู่...',
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff'
                },
                baseZ: 6000,
                bindEvents: false
            });
            $.post(base_url+"invest/edit_organization_ajax",
            $("#add_form").serialize(),
            function(result) {
                window.location.href = 'organization';
                $.unblockUI();
            });
        }
    });
    $("#cancel_add").click(function() {
        $("#edit_modal").modal("hide");
    });
});
