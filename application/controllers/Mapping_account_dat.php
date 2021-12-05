<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapping_account_dat extends CI_Controller {
	public $authConfig = array(
		"key" => "KJAUUMJZYRHL5TDX",
		"secret" => "srF4J^+cJ6e9YFV9tt#hrR^ufKENbCVh"
	);
	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
        $this->load->helper('file');
	}
	public function gen_file()
	{
		set_time_limit (1000);	
		$authConfig = $this->authConfig;
		$data_setting_atm = $this->get_setting_atm_online();
		
		//$date_gen = date('Y-m-d'); //สำหรับ run ตอนเ 5 ทุ่ม
		$date_gen = date('Y-m-d',strtotime(date('Y-m-d') . "+1 days")); // //สำหรับ run ตอนเ 5 ทุ่ม แล้วสร้างไฟล์เป็นวันถัดไป
		//$date_gen = date('Y-m-d',strtotime(date('Y-m-d') . "-1 days")); //สำหรับ run ตอนเที่ยงคืน ต้องเอาวันปัจจุบัน -1 วัน
		//$date_gen = '2020-02-15';
		$gen_mm_dd = date('md',strtotime($date_gen)); //gen เดือนวัน		
		$today = date('dmY',strtotime($date_gen));;
		$today_en = date('Y-m-d');
		//$today_en = $date_gen;
		
		$coop_id = '50';
		$name_file = $gen_mm_dd.$coop_id;
		$path_file = 'mapping_account_file/'.$name_file;
		$path_file_full = FCPATH.$path_file;

		$tab8 = '        ';
		$tab9 = '         ';
		$tab14 = '              ';
		$tab16 = '                ';
		$tab35 = '                                   ';
		$tab81 = '                                                                                 ';
		$tab87 = '                                                                                       ';

		$data = '';
		//Header
		$data .= 'HCOOP'.$today.$tab87;
		$data .= "\n";

		//Detailed		
		//เงินฝาก
		$this->db->select('t1.account_id,t1.account_id_atm,t1.mem_id AS member_id,t1.account_status,t1.account_id_atm_update');
		$this->db->from("coop_maco_account AS t1");
		$this->db->where("t1.account_status = '0' AND t1.account_id_atm_update LIKE '".$today_en."%'");
		$this->db->order_by('t1.account_id ASC');
		$rs_account = $this->db->get()->result_array();
		//echo $this->db->last_query(); echo '<br>';
		$detail_records = 0;
		$bank_id = $data_setting_atm['bank_id'];
		$coop_number = $data_setting_atm['coop_number'];
		//echo '<pre>'; print_r($rs_account); echo '</pre>';
		//add
		$action = 'A';
		$records_a = 0;
		$records_u = 0;
		$records_d = 0;
		if(!empty($rs_account)){
			foreach($rs_account as $key => $row_account){	
				$this->db->select('account_id,account_id_atm,account_id_atm_update,account_atm_status');
				$this->db->from('coop_account_atm_log');
				$this->db->where("account_id = '".$row_account['account_id']."'  AND account_id_atm_update LIKE '".$today_en."%'");
				$this->db->order_by('account_id_atm_update ASC,id ASC');
				$row_account_atm_log = $this->db->get()->result_array();				
				
				$this->db->select('account_id,account_id_atm,account_id_atm_update');
				$this->db->from('coop_account_atm_log');
				$this->db->where("account_id = '".$row_account['account_id']."'  AND account_id_atm_update NOT LIKE '".$today_en."%'");
				$this->db->order_by('account_id_atm_update DESC');
				$this->db->limit('1');
				$row_account_id_atm = $this->db->get()->row_array();
				//echo $this->db->last_query(); echo '<br>';
							
				if(!empty($row_account_atm_log)){
					foreach($row_account_atm_log AS $key_2=>$val_2){
						if(@$val_2['account_atm_status'] !=''){
							if($val_2['account_atm_status'] == 'A'){
								$action = 'A';
								$account_id_atm = @$val_2['account_id_atm'].$tab9;
								$records_a++;
							}else if($val_2['account_atm_status'] == 'D'){							
								$action = 'D';
								$chk_account_id_atm_d= (@$row_account_id_atm['account_id_atm']!='')?@$row_account_id_atm['account_id_atm']:$row_account_atm_log[($key_2-1)]['account_id_atm'];
								$account_id_atm = $chk_account_id_atm_d.$tab9;
								$records_d++;
							}else if($val_2['account_atm_status'] == 'U'){
								$action = 'U';
								$account_id_atm = @$val_2['account_id_atm'].$tab9;
								$records_u++;
							}
							$member_id = @$row_account['member_id'].$tab14;
							$data .= 'D'.@$bank_id.@$account_id_atm.@$coop_number.@$member_id.$tab16.@$action.$tab35;
							$data .= "\n";
						}
					}
				}					
			}
		}
				
		$detail_records_a = sprintf("%06d", @$records_a);
		$detail_records_u = sprintf("%06d", @$records_u);
		$detail_records_d = sprintf("%06d", @$records_d);
		//Trailer
		$data .= 'T'.@$detail_records_a.@$detail_records_u.@$detail_records_d.@$tab81;
		$data .= "\n";

		//sw file
		$name_file_sw = 'sw'.$gen_mm_dd.$coop_id;
		$path_file_sw = 'mapping_account_file/'.$name_file_sw;
		$path_file_full_sw = FCPATH.$path_file_sw;	
		$data_sw  = $gen_mm_dd;
		
		
		if(write_file($path_file_full, $data) == FALSE){
		   echo '';
		} else {
			echo $path_file;  
			if(write_file($path_file_full_sw, $data_sw) == FALSE){
				
			}else{
				echo '<br>';
				echo $path_file_sw;  
			}	
		}
		
		/***SFTP***/		
		$localFile = $path_file_full;
		$remoteFile = $data_setting_atm['remote_ftp_path'].$name_file;
		$host = $data_setting_atm['remote_ftp_host'];
		$port = $data_setting_atm['remote_ftp_port'];
		$user = $data_setting_atm['remote_ftp_user'];
		$pass = $data_setting_atm['remote_ftp_pass'];
		 
		$connection = ssh2_connect($host, $port);
		ssh2_auth_password($connection, $user, $pass);
		$sftp = ssh2_sftp($connection);
		
		$stream = fopen("ssh2.sftp://".intval($sftp).$remoteFile, "w");
		$file = @file_get_contents($localFile);
		fwrite($stream, $file);
		//fclose($stream);
		
		//file sw 
		$localFile_sw = $path_file_full_sw;
		$remoteFile_sw = $data_setting_atm['remote_ftp_path'].$name_file_sw;
		$stream_sw = @fopen("ssh2.sftp://".intval($sftp).$remoteFile_sw, "w");
		$file_sw = @file_get_contents($localFile_sw);
		fwrite($stream_sw, $file_sw);
		//fclose($stream_sw);

		exit;
		
	}
	
	public function get_setting_atm_online(){				
		$row = $this->db->select(array('*'))
				->from('coop_setting_atm_online')->limit(1)	
				->get()->row_array();
		if(!empty($row)){
			$result = $row;
		}else{
			$result = array();
		}
		return $result;
		
	}

}
