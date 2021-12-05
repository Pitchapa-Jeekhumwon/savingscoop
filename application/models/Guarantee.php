<?php


class Guarantee extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private $_guarantee = null;

    public function current($member_id)
    {

        $this->db->select(array(
            't2.id',
            't2.petition_number',
            't2.contract_number',
            't2.member_id',
            't3.firstname_th',
            't3.lastname_th',
            't2.loan_amount',
            't2.loan_amount_balance'
        ));
        $this->db->from('coop_loan_guarantee_person as t1');
        $this->db->join('coop_loan as t2', 't1.loan_id = t2.id', 'inner');
        $this->db->join('coop_mem_apply as t3', 't2.member_id = t3.member_id', 'inner');
        $this->db->where("t1.guarantee_person_id = '" . $member_id . "' AND t2.loan_status IN('1','2')");
        $this->_guarantee = $this->db->get()->result_array();

        return $this;
    }

    public function get()
    {

        return $this->_guarantee;
    }

    public function getBalance()
    {
        $result = 0;
        foreach ($this->_guarantee as $key => $item) {
            $result += $item['loan_amount_balance'];
        }

        return $result;
    }

    public function itemCount()
    {
        return sizeof($this->_guarantee);
    }

    public function getGuaranteeAmount($amount, $date, $loan_type)
    {
        $id = self::getTermOfLoan($loan_type, $date);
        $list = self::getCondition($id);
        if(!empty($list)) {
            foreach ($list as $key => $item) {
                $condition = self::getConditionList($item['col_id']);
                if (self::operator($amount, self::getValOpt($condition), $condition['operation'])) {
                    return self::getCountGuarantee($item['col_id']);
                }
            }
        }
        return 0;
    }

    public function getTermOfLoan($loan_type_id, $start_date)
    {
        $this->db->where('start_date <=', $start_date);
        $this->db->where("type_id", $loan_type_id);
        $this->db->order_by("start_date desc, id desc");
        $term_of_loan = $this->db->get("coop_term_of_loan", 1)->row_array();
        return $term_of_loan['id'];
    }

    public function getCondition($term_of_loan_id)
    {
        $this->db->where("result_type = 'guarantor'");
        return $this->db->get_where("coop_condition_of_loan", array(
            "term_of_loan_id" => $term_of_loan_id
        ))->result_array();
    }

    public function getConditionList($col_id = array())
    {
        return $this->db->get_where("coop_condition_list", array(
            "col_id" => $col_id
        ))->row_array();
    }

    public function getMetaCondition($id)
    {
        return $this->db->get_where("coop_meta_condition", array(
            "id" => $id
        ))->row_array();
    }

    public function getValOpt($condition, $params = "")
    {
        if (!empty($condition)) {
            if (!empty($condition['fieldname'])) {
                if (empty($condition['req_field'])) {
                    return $this->db->query($condition['fieldname'])->row()->value;
                } else {
                    return $this->db->query($condition['fieldname'], $params)->row()->value;
                }
            } else {
                return $condition['value'];
            }
        } else {
            return $condition['value'];
        }
    }

    public function getConditionGuarantee($col_id)
    {
        return $this->db->get_where("coop_condition_of_loan_sub_guarantor",
            array( "col_id" => $col_id ))->result();
    }

    public function getCountGuarantee($col_id)
    {
        $this->db->where(array( "col_id" => $col_id ));
        return $this->db->count_all_results("coop_condition_of_loan_sub_guarantor");
    }

    private function operator($a, $b, $op)
    {
        $val = false;
        switch ($op) {
            case '>':
                $val = ($a > $b) ? true : false;
                break;
            case '>=':
                $val = ($a >= $b) ? true : false;
                break;
            case '<':
                $val = ($a < $b) ? true : false;
                break;
            case '<=':
                $val = ($a <= $b) ? true : false;
                break;
            case '==':
                $val = ($a == $b) ? true : false;
                break;
            case '!=':
                $val = ($a != $b) ? true : false;
                break;
            case '+':
                $val = ($a + $b);
                break;
            case '-':
                $val = ($a - $b);
                break;
            case '*':
                $val = ($a * $b);
                break;
            case '/':
                $val = ($a / $b);
                break;
            case '^':
                $val = ($a ^ $b);
                break;
            default:
                $val = false;
                break;
        }
        return $val;
    }

}
