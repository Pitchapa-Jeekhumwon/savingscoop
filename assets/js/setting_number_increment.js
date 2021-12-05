function edit_number(loan_name_id, loan_named) {
    $('#edit_number').modal('show');
    $("#loan_named").html(loan_named);
    $("#loan_name_id").val(loan_name_id);
}

$('input[name=run_contract_number]').keyup(function () {
    var val = parseInt($(this).val()) + 1;
    $("#next_contract_number").val(val);
});

$("#submit_edit").click(function () {
    $("#frm").submit();
});
