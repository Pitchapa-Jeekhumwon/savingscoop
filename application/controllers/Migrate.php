<?php
class Migrate extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * เปลี่ยนแปลง Text Case ให้ตรงกับชื่อตาราง
     * @param String $str `รับค่า string ที่ต้องการ`
     * @param string $textCase `รับค่า TextCase ที่ต้องการเปลี่ยนแปลง lowercase, uppercase`
     * @return string
     */
    private function modTextCase($str, $textCase = "lowercase"){
        if(strtolower($textCase) == "uppercase") {
            return strtoupper($str);
        }else{
            return strtolower($str);
        }
    }

    /**
     * Migrate ข้อมูลที่อยู่
     */
    public function district(){

        /** @var String $database */
        $database = "police"; // กำหนดชื่อฐานข้อมูล
        /** @var String $textCase */
        $textCase = "lowercase";        //กำหนด text case

        $src = $this->db->get_where($database.".".self::modTextCase("sc_mem_m_member_address", $textCase), array("address_type" => 0 ))->result_array();
        $data = array();
        $num = 0;
        foreach ($src as $key => $area){

            $provinceName = $this->db->select("PROVINCE_NAME")->from($database.".".self::modTextCase("SC_MEM_M_UCF_PROVINCE", $textCase))->where(array('PROVINCE_CODE' => $area['PROVINCE_CODE']))->get()->row_array();
            $amphurName = $this->db->select("DISTRICT_NAME")->from($database.".".self::modTextCase("SC_MEM_M_UCF_DISTRICT", $textCase))->where(array('PROVINCE_CODE' => $area['PROVINCE_CODE'], 'DISTRICT_CODE' => $area['DISTRICT_CODE']))->get()->row_array();

            $provinceId =  $this->db->select("province_id")->from("coop_province")->where("province_name like '%". $provinceName['PROVINCE_NAME']."%' ")->get()->row_array();
            $amphurId = $this->db->select("amphur_id")->from("coop_amphur")->where("amphur_name Like '%".$amphurName['DISTRICT_NAME']."%' AND province_id ={$provinceId['province_id']}")->get()->row_array();

            if(empty($area['TAMBOL'])) {
                $districtId = array();
            }else{
                $districtId = $this->db->select("district_id")->from("coop_district")->where("district_name like '%" . preg_replace('/[^ก-๛]+/', '',$area['TAMBOL']) . "%' AND amphur_id = '" . $amphurId['amphur_id'] . "' AND province_id='" . $provinceId['province_id'] . "' AND district_name not like '%*%'  ")->get()->row_array();
            }
            $data[$num]['member_id'] =  $area['MEMBERSHIP_NO'];
            $data[$num]['province_id'] = $provinceId['province_id'];
            $data[$num]['amphur_id'] = $amphurId['amphur_id'];
            $data[$num]['district_id'] = $districtId['district_id'];

            $num++;
        }

        $this->db->update_batch("coop_mem_apply", $data, 'member_id');

    }

    /**
     * Migrate ข้อมูลเบอร์โทรศัพ
     */
    public function mobile(){

        $src = $this->db->select("member_id, note as mem_tel")
            ->from("coop_mem_apply")
            ->where('mobile is null')
            ->get()->result_array();
        //$src = $this->db->select("mem_no as member_id, mem_tel")->from("tmp_mem_tel")->get()->result_array();

        $data = array();
        $num = 0;
        foreach ($src as $key => $val ){

            if(!empty($val['mem_tel'])) {

                //$tel = preg_replace('/([ก-๛]|\/+|(\s)+|\s\s+|\s{2,}|,)+/', ' ', $val['mem_tel']);
                //$mem_tel_arr = explode(',', $tel);

                $mem_tel_arr = preg_split('/([ก-๛]|\/+|(\s)+|\s\s+|\s{2,}|,|\.)+/', $val['mem_tel']);

                $mobile = "";
                foreach ($mem_tel_arr as $index => $value){
                    $value = preg_replace('/([ก-๛]|\/+|(\s)+|\s\s+|\s{2,}|-)/', '', $value);
                    if(strlen($value) < 10){
                        $value = str_pad($value, 10, 0, STR_PAD_LEFT);
                    }
                    if(in_array(substr($value, 0, 2), array('06', '08', '09')) && strlen($value) == 10){
                        $mobile = $value;
                    }
                }

                if($mobile){
                    $data[$num]['member_id'] = $val['member_id'];
                    $data[$num]['mobile'] = $mobile;
                    $num++;
                }
            }
        }

        if($_GET['run'] == 'execute') {
            $this->db->update_batch('coop_mem_apply', $data, 'member_id');
            //$this->db->update_batch("tmp_mem_tel", $data, 'mem_no');
            echo "successful.";
        }else{

            echo "rows: ".sizeof($data); echo "<hr>";
            echo "<pre>"; print_r($data); exit;
        }
    }

    public function member_group(){
        $group = $this->db->get('coop_mem_group')->result_array();
        $data = array();
        $num = 0;
        foreach ($group as $key => $value){

            $num++;
            $parent = 1;

            $data[$num]['mem_group_id'] = $value['mem_group_id'];
            $data[$num]['mem_group_full_name'] = 'ไม่ระบุ';
            $data[$num]['mem_group_name'] = 'ไม่ระบุ';
            $data[$num]['mem_group_type'] = $parent;

            for($i=0; $i < 2; $i++){
                $data[$num]['mem_group_id'] = '';
                $data[$num]['mem_group_full_name'] = 'ไม่ระบุ';
                $data[$num]['mem_group_name'] = 'ไม่ระบุ';
                $data[$num]['mem_group_type'] = $parent;
                $num++;
                $parent++;
            }
        }
    }

    public function set_interest(){

        ini_set('precision', 16);
        $data = array();
        $num = 0;
        foreach (self::lon_step_rate() as $number => $step){

            if(self::getCounter($step['LOAN_TYPE'], $step['EFFECTIVE_DATE']) == 0) {
                $condition = self::get_term_of_loan($step['LOAN_TYPE']);
                unset($condition['id']);
                $condition['interest_rate'] = round($step['LOAN_INTEREST_RATE']*100, 2);
                $condition['start_date'] = $step['EFFECTIVE_DATE'];
                $data[$num] = $condition;
                $num++;
            }
        }

//        header('content-type: application/json; charset: utf8;');
//        echo json_encode($data);
//        exit;
        if(sizeof($data)) {
            $this->db->insert_batch('coop_term_of_loan', $data);
        }

    }

    public function getCounter($loanType = "", $effective = ""){
        return $this->db->select('*')->from('coop_term_of_loan')->where(array('prefix_code' => $loanType, 'start_date' => $effective))->count_all_results();
    }

    public function get_term_of_loan($loanType = ""){
        return $this->db->select('*')->from('coop_term_of_loan')->where(array('prefix_code' => $loanType))
            ->order_by('start_date', 'desc')->limit(1)->get()->row_array();
    }

    public function lon_step_rate(){
        return $this->db->get_where('police.sc_lon_m_int_step_rate', "EFFECTIVE_DATE > '2020-01-01'")->result_array();
    }


}