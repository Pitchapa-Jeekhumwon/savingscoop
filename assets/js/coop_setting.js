var _global_decimalPlaces, _global_places;
const  datepickerconfig = {
    prevText: "ก่อนหน้า",
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
};
function _load(){

    if (typeof base_url === "undefined")  return;
    new Promise(resolve => {
        $.post(base_url+'coop_setting/get_setting', {}, function(res){
            resolve(res);
        })
    }).then((res) => {
        _global_places = res.setting.rounding_calc_period_interest;
        _global_decimalPlaces = res.setting.round_interest;
    });
}
_load();

function round(number, decimalPlaces){
    decimalPlaces = decimalPlaces === undefined ? _global_decimalPlaces : decimalPlaces;
    const factorOfTen = Math.pow(10, decimalPlaces);
    if(factorOfTen==0){
        return Math.round(number);
    }
    return Math.round(number * factorOfTen) / factorOfTen;
}

function ceil(number, decimalPlaces){
    decimalPlaces = decimalPlaces === undefined ? _global_decimalPlaces : decimalPlaces;
    return Math.ceil(number);
}

function floor(number, decimalPlaces){
    decimalPlaces = decimalPlaces === undefined ? _global_decimalPlaces : decimalPlaces;
    // const factorOfTen = Math.pow(10, decimalPlaces);
    return Math.floor(number);
}

//ปัดหลัก
function round_nearest(number, places, method){
    method = method === undefined ? 'round' :  method;
    places = places === undefined ? _global_places : places;
    if(method.toLowerCase() === 'ceil') {
        return Math.ceil(number / places) * places;
    }else {
        return Math.round(number / places) * places;
    }
}

