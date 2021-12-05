<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Address_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
  
    }

    public function address($member_id){
        $this->db->select(array('coop_mem_apply.*',
                                'coop_prename.prename_full',
                                'coop_district.district_name',
                                'coop_amphur.amphur_name',
                                'coop_province.province_name'));
        $this->db->from('coop_mem_apply');			
        $this->db->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left");		
        $this->db->join('coop_district', 'coop_district.district_id = coop_mem_apply.district_id', 'left');
        $this->db->join('coop_amphur', 'coop_amphur.amphur_id = coop_mem_apply.amphur_id', 'left');
        $this->db->join('coop_province', 'coop_province.province_id = coop_mem_apply.province_id', 'left');	
        $this->db->where("member_id = '".@$member_id."'");			
        $rs_member = $this->db->get()->result_array();
        $row_member = @$rs_member[0];
        $address1 = "";
        $address2 = "";
        $address3 = "";
        $address4 = "";
        if(@$row_member['address_no']) {
        $address1 .= " บ้านเลขที่ ".@$row_member['address_no'];
        }
        if(@$row_member['address_moo']) {
        $address1 .= " หมู่ ".@$row_member['address_moo'];
        }
        if(@$row_member['address_village']) {
        $address1 .= " หมู่บ้าน ".@$row_member['address_village'];
        }
        if(@$row_member['address_road']) {
        $address2 .= " ถนน ".@$row_member['address_road'];
        }
        if(@$row_member['address_soi']) {
        $address2 .= " ซอย ".@$row_member['address_soi'];
        }
        if(@$row_member['district_id']) {
        $address3 .= " ต.".@$row_member['district_name'];
        }
        if(@$row_member['amphur_id']) {
        $address3 .= "อ.".@$row_member['amphur_name'];
        }
        if(@$row_member['province_id']) {
        $address4 .= " จ.".@$row_member['province_name'];
        }
        if(@$row_member['zipcode']) {
        $address4 .= " รหัสไปรษณีย์ ".@$row_member['zipcode'];
        }
        $address_all = $address1.$address2.$address3.$address4;
        return $address_all;
    }


}