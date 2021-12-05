<?php
/**
 * Created by PhpStorm.
 * User: macmini2
 * Date: 2019-12-18
 * Time: 11:30
 */

class calc_slip_model extends CI_Model
{

    /**
     * @var $_member
     * ข้อมูลสมาชิก
     */
    private $_member;

    /**
     * @var
     * ข้อมูลผู้ค้ำประกัน
     */
    private $_guarantor;

    private $_rate_salary;

    public function __construct()
    {
        parent::__construct();
    }

    public function calc()
    {

    }


    public function member($member_id)
    {
        $cond = array(
            'member_id' => $member_id
        );
        $this->_member = $this->db->select('*')->from('coop_mem_apply')->where($cond)->get()->row_object();
        return $this;
    }

    public function guarantor($member_id)
    {
        $cond = array(
            'member_id' => $member_id
        );
        $this->_guarantor = $this->db->select('*')->from('coop_mem_apply')->where($cond)->get()->row_object();
        return $this;
    }


    public function getLoanRateBySalary($salary)
    {
        $sql = "SELECT * FROM coop_normal_condition_list WHERE min_salary <= ? ORDER BY loan DESC LIMIT 1;";
        return $this->db->query($sql, array($salary))->row_array();
    }

    public function getMinNetBalanceBySalary()
    {

        return $this;
    }

    public function getLoaRateByNetBalance()
    {
        return $this;
    }

    public function calcNetBalance()
    {
        return 0;
    }

}
