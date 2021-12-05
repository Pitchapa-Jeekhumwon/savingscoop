/**
 * require loan_contract.js
 */
const defaultGuaranteePerson = (obj) => {
    clearDefaultGuaranteePerson();
    for (let i = 0; i < obj.length; i++){
        createRow(obj[i]);
    }
};

const clearDefaultGuaranteePerson = () => {
    $(".guarantee-table tbody tr .content-guarantee-type").each(function (e, index) {
        if($(this).val() === '1') {
            remove($(this));
        }
    })
};

const getDefaultGuarantee = (data) => {
  return new Promise((resolve, reject) => {
     $.post(base_url+"loan_contract/get_guarantee_amount", data, (res, status, xhr) => {
         if(status === "success"){
             return resolve(res);
         }else{
             return reject(xhr);
         }
     })
  });
};

const getListGuarantee = (amount, loan) => {
  return new Promise(resolve => {
      let estimate = [];
      let _loan = loan;
      for (let i=0; i < amount; i++){
          let per = Math.floor(loan / amount);
          if(i === 0){
              estimate[i] = Math.ceil(loan / amount);
          }else {
              estimate[i] = per;
          }
          _loan = (_loan-per);
      }
      let data = [];
      for (let i=0; i < amount; i++){
          let obj = {};
          obj.typeName = 'สมาชิกคำประกัน';
          obj.typeId = 1;
          obj.number = '';
          obj.name = '';
          obj.estimate = 0;
          obj.amount = estimate[i];
          obj.remark = '';
          obj.salary = 0;
          data[i] = obj;
      }
      return resolve(data);
  })
};

const setDefaultGuarantee = () => {
    const loan = parseFloat($("#loan_amount").val().split(",").join(""));
    const loan_type = $("#loan_type_select").val();
    const date = $("#createdatetime").val();
    const data = {amount: loan, createdatetime: date, loan_type: loan_type};
    getDefaultGuarantee(data)
        .then((res) => {
            if(res.amount > 0){
                return getListGuarantee(res.amount, loan);
            }
        })
        .then(defaultGuaranteePerson)
        .catch((err) => {

        })
};

const getLimitMaximumAddShare = (data) => {
    return new Promise((resolve, reject) => {
        $.post(base_url+"/loan_contract/get_credit_limit", data, (res, status, xhr) => {
            if(res.status === "success" && res.credit_limit >= 0){
                return resolve(res);
            }
            return reject(xhr);
        })
    });
};

const setCreditLimitMaximum = (res) => {
    setMaxLoanLimit(res.credit_limit, true);
};

$(document).on("blur", "input[name='data[loan_deduct][deduct_share]']", (e) => {
    const member_id = $(".member_id").val();
    if(typeof member_id === "undefined" ||  member_id === ''){
        return false;
    }

    let data  = {};
    data.member_id = member_id;
    data.deduct_share = removeCommas($("input[name='data[loan_deduct][deduct_share]']").val());

    getLimitMaximumAddShare(data).then(setCreditLimitMaximum).catch((err) => {
        console.log("error: ", err);
    });
});

var timeIncome = null;
$(document).on("change blur", "#loan_amount, .net_balance", () => {
    if(timeIncome !== null){
       clearTimeout(timeIncome);
    }
    timeIncome = setTimeout(checkNetIncome, 3000);
});

const checkNetIncome = () => {
    let data = {};
    data.member_id = memberID.val();
    data.loan_type = $("#loan_type_select").val();
    data.amount = removeCommas($("#loan_amount").val());
    getRequireNetIncome(data).then(resultCheckedNetIncome).catch((xhr) => {
        console.log("err:" , xhr.responseText);
    })

};

const resultCheckedNetIncome = (res) => {
    const requireNetIncome = res.requireNet;
    const netBalance = removeCommas($(".net_balance").val());
    if(requireNetIncome > netBalance){
        swal("แจ้งเตือน","เงินได้สุทธ์คงเหลือไม่ถึง "+addCommas(requireNetIncome)+"บาท", "info");
    }
    timeIncome = null;
}

const getRequireNetIncome= (data) => {
    return new Promise((resolve, reject) => {
        $.post(base_url+"/loan_contract/check_net_income", data, (res, status, xhr) =>{
            if(res.status === "success"){
                return resolve(res);
            }
            return reject(xhr);
        });
    });
}

