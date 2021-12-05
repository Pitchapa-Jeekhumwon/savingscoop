<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');


class Keep extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

        //set member_id
    }

    private $_memberId = null;

    private function _coopManagementKeep($date)
    {
        //ToDo ดึงข้อมูลตั้งค่ารอบเรียกเก็บตามรอบของสหกรณ์

        return $date;
    }

    /**
     * @return string|null
     */
    public function getMemberId()
    {
        return $this->_memberId;
    }

    /**
     * เชต memberId
     * @param null $memberId
     * @return $this
     */
    public function setMemberId($memberId)
    {
        $this->_memberId = $memberId;
        return $this;
    }

    private $_currentDate = null;
    private $_buddhaYear = null;
    private $_year = null;
    private $_month = null;
    private $_format_dash_Ymd = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/';
    private $_format_dash_dmY = '/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/';


    private function __covertDate($date){

        //Handle format date
        $_date = substr(str_replace('/', '-', $date), 0, 10);
        if(preg_match($this->_format_dash_dmY, $_date)){                //check format dmY
            $_arr = array_reverse(explode('-', $_date));
            $_date = join("-", $_arr);
        }

        //Handle buddha year
        $_chk = explode('-', $_date);                           //check buddha year
        if($_chk[0]+543 > date('Y')+543){
            $_chk[0] -= 543;
            $_date = join("-", $_chk);
        }
        return $_date;
    }

    /**
     * เซตค่าตัวแปรเกี่ยวกับวันที่
     * @param $date
     */
    private function __date($date){
        $this->_currentDate = $date;
        $this->_month = date("m", strtotime($date));
        $this->_year = date("Y", strtotime($date));
        $this->_buddhaYear = $this->_year + 543;
    }

    /**
     * จัดการข้อมูลวันที่ในการค้นหาข้อมูลเรียกเก็บ
     * @param $date
     * @return $this
     */
    public function setCurrentDate($date){

        if($date == ""){
            $date = date("Y-m-d");
        }

        $date = self::__covertDate($date);                              //handle date format
        if(preg_match($this->_format_dash_Ymd, $date)){                 //check date format dash Y-m-d

            //Todo เพิ่มตัวตั้งค่าในการดึงข้อมูลเรียกเก็บตามรอบของสหกรณ์
            $date = self::_coopManagementKeep($date);

            self::__date($date);
            return $this;
        }else{
            echo "wrong date";
            exit;
        }
    }

    public function find($date, $member_id){
        self::setMemberId($member_id);
        self::setCurrentDate($date);
        return $this;
    }

    /**
     * ค้นหา profile ตามวันที่
     * @return mixed
     */
    public function currentProfile(){
        $where['profile_month'] = $this->_month;
        $where['profile_year'] = $this->_buddhaYear;
        return $this->db->get_where("coop_finance_month_profile", $where, 1)->row()->profile_id;
    }

    private $_object = null;                                            //result object

    /**
     * ค้นหาข้อมูลเรียกเก็บ
     * @return $this
     */
    public function currentDetail(){

        $where['profile_id'] = self::currentProfile();
        if($this->_memberId != null){
         $where['member_id'] = $this->_memberId;
        }
        $this->_object =  $this->db->get_where("coop_finance_month_detail", $where)->row_array();
        return $this;
    }

    public function getSummary(){
        $where = array(
            'profile_id' => self::currentProfile(),
            'member_id' => self::getMemberId()
        );
        $this->db->select(array("deduct_code", "","SUM(real_pay_amount) as sum_amount"));
        $this->db->from("coop_finance_month_detail")->where($where);
        $this->db->group_by("pay_type, deduct_code");
        return $this->db->get()->result_array();
    }

    public function getLoan(){
        $where = array(
            'member_id' => self::getMemberId()
        );
        $loan_row = $this->db->get_where("coop_loan", array("id" => $_POST['loan_id']) )->result()[0];
        $interest =  $this->Interest_modal->get_interest($loan_row->term_of_loan_id, date("Y-m-d"),  $loan_row->id);
        return $this->db->get()->result_array();
    }

    /**
     * เรียกข้อมูลผลลัพที่ค้นหา
     * @return object|null
     */
    public function row(){
        return $this->_object;
    }

    /**
     * เรียกข้อมูลผลลัพที่ค้นหา
     * @return array
     */
    public function row_array(){
        return (array) $this->_object;
    }
    public function getPaymentDeposit(){
            $member_id = self::getMemberId();
            $deduction_year = date('Y',strtotime('+543 year'));
            $deduction_month = date('m',strtotime('+1 month'));
            $where = " AND member_id = '".$member_id."' AND deduction_year <= '".$deduction_year."' AND deduction_month <= cast('".$deduction_month."' as decimal) AND deduction_type = '0' ";
            $this->db->select('*');
            $this->db->from("coop_deposit_month_transaction");
            $this->db->where("1=1 {$where}");
            $this->db->order_by("id DESC");
            return $this->db->get()->result_array();
        }
}
