function check_empty(type){
    var month = '';
    var year = '';
    var mem_type_id = '';

    if(type === 'PROCESSOR'){
        mem_type_id = $('#processor_mem_type').val();
        month = $('#processor_month').val();
        year = $('#processor_year').val();
    }else{
        return;
    }

    var data = {
        type: type,
        month: month,
        year: year,
        mem_type_id: mem_type_id
    };

    blockUI();
    $.post(base_url+'text_file_deduction/check_empty', data, function(data){
            console.log(data);
            unblockUI(1000);
            if(data.data_type === 'SHARE'){
                if(data.has_data  === 1){
                    $("#form1").submit();
                }else{
                    swal('ไม่มีข้อมูล', '','warning');
                }
            }else if(data.data_type === 'LOAN'){
                if(data.has_data  === 1){
                    $("#form2").submit();
                }else{
                    swal('ไม่มีข้อมูล', '','warning');
                }
            }else if(data.data_type === 'DEPOSIT'){
                if(data.has_data  === 1){
                    $("#form3").submit();
                }else{
                    swal('ไม่มีข้อมูล', '','warning');
                }
            }else if(data.data_type === 'PROCESSOR'){
                if(data.has_data  === 1){
                    $("#form4").submit();
                }else{
                    swal('ไม่มีข้อมูล', '','warning');
                }
            }else{
                swal('เกิดข้อผิดพลาด', '','error');
            }

    });

}


$(document).ready(function(){
	check_total_share('SHARE');
	check_total_share('LOAN');
	check_total_share('DEPOSIT');
	check_total_share('PROCESSOR');
});


function check_total_share(type){
	var month = '';
    var year = '';
    var mem_type_id = '';
    var post_url = '';
    var mem_type_arr = [];
    if(type === 'PROCESSOR'){
        mem_type_id = $('#processor_mem_type').val();
        month = $('#processor_month').val();
        year = $('#processor_year').val();
        post_url = 'text_file_deduction/sumary_processor';
        this.mem_type.forEach(element => {
            var checkBox = document.getElementById("mem_type["+element+"]");
            if(checkBox.checked){
                mem_type_arr.push(element);
            }else{
                document.getElementById("mem_type_all").checked = false;
            }
            }
        );
    }else{
        return;
    }

    var data = {
        type: type,
        month: month,
        year: year,
        mem_type_id: mem_type_id,
        mem_type_arr: mem_type_arr
    };
	
	blockUI();
    $.post(base_url+post_url, data, function(result){
			var obj = JSON.parse(result);
            //console.log(obj);
            unblockUI(1000);
            if(type === 'PROCESSOR'){
                $("#total_share").html(obj.sumary);
            }else{
                swal('เกิดข้อผิดพลาด', '','error');
            }
    });
}
function check_all(){
    var mem_type_all = document.getElementById("mem_type_all");
    if(mem_type_all.checked){
        this.mem_type.forEach(element => {
                var checkBox = document.getElementById("mem_type["+element+"]");
                checkBox.checked = true;
            }
        );
    }
    check_total_share('PROCESSOR');
}
