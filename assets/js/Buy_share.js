var base_url = $('#base_url').attr('class');
$( document ).ready(function() {


	$("#fix_date").datepicker({
        prevText : "ก่อนหน้า",
        nextText: "ถัดไป",
        currentText: "Today",
        changeMonth: true,
        changeYear: true,
        isBuddhist: true,
        monthNamesShort: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
        dayNamesMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
        constrainInput: true,
        dateFormat: "dd/mm/yy",
        yearRange: "c-50:c+10",
		autoclose: true,
	});
	
	$("#fix_date").change(function() {
		console.log( $(this).val() );
		var member_id = getUrlParameter('member_id');
		var fix_date = getUrlParameter('fix_date');
		var url = base_url+"buy_share?member_id="+member_id+"&fix_date="+$(this).val();
		window.location.href = url;
		// $("#cremation_debt").val(format_number($('option:selected', this).attr("data-debt")));
	});
});

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};
