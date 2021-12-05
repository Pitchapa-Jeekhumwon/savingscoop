class Validation {

    constructor() {

    }

    set meta(newMeta){ this._meta = newMeta }
  
    get meta(){ return this._meta }

    set loanTypeId(newLoanTypeId){ this._loanTypeId = newLoanTypeId }
  
    get loanTypeId(){ return this._loanTypeId}

    set memberId(newmemberId){ this._memberId = newmemberId }
  
    get memberId(){ return this._memberId}

    set loan(loan){ this._loan = loan }

    get loan(){ return this._loan}

    rule(optional){
        optional = optional || "";
        var val = "";
        var max = "";
        var min = "";
        var valueMax = "";
        var valueMin = "";
        var message = "";
        let fn = new Validation();
        jQuery.ajax({
            method: 'POST',
            url: base_url+"MetaRest/rule",
            data: {
                result_type         : this._meta, 
                term_of_loan_id     : this._loanTypeId,
                member_id           : this._memberId,
                optional            : optional,
                loan                : this._loan
            },
            success: function (result) {
                var obj = result;
                val = obj.value;
                max = obj.global_value;
                min = obj.global_value;
                operator = obj.operator;

                if(operator == ">"){
                    valueMax = val
                    valueMin = 0
                }else if(operator == ">="){
                    valueMax = val
                    valueMin = 0
                }else if(operator == "<"){
                    valueMax = 0
                    valueMin = val
                }else if(operator == "<="){
                    valueMax = 0
                    valueMin = val
                }else if(operator == "="){

                }else if(operator == "=!"){

                }else{
                    valueMax = val
                    valueMin = 0
                }
                
                if(val > max){
                    valueMax = val
                }
                message = obj.message;
            },
            error: function(xhr,status,error){

            },
            async: false
        });

        return {valueMax : valueMax, valueMin: valueMin, message: message, operator: operator};

    }

    is_numeric(str){
        return /^\d+$/.test(str);
    }

    operator(a, b, op){
        var operator = {
            '>': function (x, y) { return x > y },
            '>=': function (x, y) { return x >= y },
            '<': function (x, y) { return x < y },
            '<=': function (x, y) { return x <= y },
            '==': function (x, y) { return x == y },
            '!=': function (x, y) { return x != y }
        };
  
        if(op==null || op==""){
            return false;
        }
        return operator[op](a, b);
    }
}
