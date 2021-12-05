<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Run_import_excel_update extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Deposit_modal", "deposit_modal");
    }

    public function index()
    {  $date = $_GET['date'];
        //=== balance st ===
        $date_b = explode('-', $date);
        if($date_b[1]=='01'){
            $date_b0 =  ($date_b[0]-1);
            $date_before = $date_b0."-12-15";
        }
        else{$date_b[1]=$date_b[1]-1;
            $date_before = $date_b[0]."0".$date_b[1]."15";
        }
        $date_month_before = date('Y-m-t', strtotime($date_before));
        //echo date('Y-m-t', strtotime($date_before));exit;
        //$this->db->select(array('account_id'));
        $this->db->select(array('account_id'));
        $this->db->from('temp_import_account_transaction');
        $this->db->where("date_data LIKE '%".$date."%' ");
        $temp_import = $this->db->get()->result_array();
        foreach($temp_import as $key=>$value){
            $acc_id= explode('-', $value['account_id']);
            $account_id = $acc_id[0].$acc_id[1].$acc_id[2];
            $temp_import[$key]['account_id']= $account_id ;

            $this->db->select('account_id,transaction_time');
            $this->db->from('coop_account_transaction');
            $this->db->where("transaction_time LIKE  '%".$date_month_before."%' AND account_id ='".$account_id."'");
            $data[$key]= $this->db->get()->row_array();

            if(empty($data[$key])){
                $this->db->select('account_id,transaction_time');
                $this->db->from('coop_account_transaction');
                $this->db->where("transaction_time LIKE  '%".$date."%' AND account_id ='".$account_id."'");
                $data[$key]= $this->db->get()->row_array();
            }
        }
        //echo"<pre>";echo COUNT($data);exit;
        //echo $this->db->last_query();exit;
        //$this->Run_import_excel_update->Update_balance($data);
        //echo "<pre>";print_r($data);exit;
        foreach ($data as $key1 => $val1 ) {
            $account_id = $val1['account_id'];
            $transaction_time = $date_month_before;
            //echo $transaction_time;exit;
            $where = "1=1";
            if (@$account_id != '') {
                $where .= " AND account_id = '{$account_id}'";
            }
            $sql1 = "	SELECT
						account_id,
						mem_id,
						created,
						account_status,
						close_account_date
					FROM
						coop_maco_account 
					WHERE
						{$where}
					ORDER BY
						account_id ASC";
            $row1 = $this->db->query($sql1)->result_array();
            if (!empty($row1)) {
                foreach ($row1 AS $key1 => $val1) {
                    $account_id = $val1['account_id'];
                    //$transaction_time = $val1['created'];
                    //$account_id = '1A00025';
                    //$transaction_time = '2020-12-22';

                    $affected_rows = 0;
                    $sql2 = "	SELECT
								account_id,
								transaction_time,
								transaction_id,
								transaction_list,
								old_acc_int,
								accu_int_item
							FROM
								coop_account_transaction 
							WHERE
								transaction_time > '{$transaction_time}' 
								AND account_id = '{$account_id}'
							ORDER BY
								account_id ASC , transaction_time ASC";
                    $row2 = $this->db->query($sql2)->result_array();
                    $old_acc_int = 0;
                    if (!empty($row2)) {
                        foreach ($row2 AS $key2 => $val2) {
                            $data_update = array();
                            $data_update['date'] = $transaction_time;
                            $data_update['account_id'] = $account_id;
                            //echo '<pre>'; print_r($data_update); echo '</pre>';
                            $this->update_st->Run_import_excel_update($data_update);
                            //echo  "<pre>"; print_r($this->db->affected_rows());exit;
                            if ($this->db->affected_rows()) {
                                $affected_rows++;
                            }
                        }
                    }

                    if ($affected_rows > 0) {
                        echo $account_id . "=>success";
                        echo '<br>';
                        //echo json_encode(["result" => "success"]);
                        //exit;
                    } else {
                        echo $account_id . "=>error";
                        echo '<br>';
                        //echo json_encode(["result" => "error"]);
                        //exit;
                    }
                }
            }


        }
    }
    public function run_accu_inter()
    { $date = $_GET['date'];
        $this->db->select(array('account_id'));
        $this->db->from('temp_import_account_transaction');
        $this->db->where("date_data LIKE '%".$date."%' ");
        $temp_import = $this->db->get()->result_array();
        foreach($temp_import as $key=>$value){
            $acc_id= explode('-', $value['account_id']);
            $account_id = $acc_id[0].$acc_id[1].$acc_id[2];
            $temp_import[$key]['account_id']= $account_id ;

            $date_b = explode('-', $date);
            $date_before = $date_b[0].$date_b[1].$date_b[2];

            $date_b = explode('-', $date);
            if($date_b[1]=='01'){
                $date_b0 =  ($date_b[0]-1);
                $date_before = $date_b0."-12-15";
            }
            else{$date_b[1]=$date_b[1]-1;
                $date_before = $date_b[0]."0".$date_b[1]."15";
            }

            $date_month_before = date('Y-m-t', strtotime($date_before));
            $this->db->select('account_id,transaction_time');
            $this->db->from('coop_account_transaction');
            $this->db->where("transaction_time LIKE  '%".$date_month_before."%' AND account_id ='".$account_id."'");
            $this->db->order_by("transaction_time DESC") ;
            $this->db->limit(1);
            $data[$key]= $this->db->get()->row_array();

            if(empty($data[$key])){
                $this->db->select('account_id,transaction_time');
                $this->db->from('coop_account_transaction');
                $this->db->where("transaction_time LIKE  '%".$date."%' AND account_id ='".$account_id."'");
                $data[$key]= $this->db->get()->row_array();
            }
        }
//echo'<pre>';print_r($data);exit;
        foreach($data as $key => $val) {
            $sql1 = "	SELECT
						account_id,
						mem_id,
						created,
						account_status,
						close_account_date
					FROM
						coop_maco_account 
					WHERE
						account_id = '".$val['account_id']."'
					ORDER BY
						account_id ASC";
            $row1 = $this->db->query($sql1)->result_array();
            if (!empty($row1)) {
                foreach ($row1 AS $key1 => $val1) {
                    $account_id = $val1['account_id'];
                    //$transaction_time = $val1['created'];
                    //$account_id = '1A00025';
                    //$transaction_time = '2020-12-22';
                    $transaction_time = $date;

                    $affected_rows = 0;
                    $sql2 = "	SELECT
								account_id,
								transaction_time,
								transaction_id,
								transaction_list,
								old_acc_int,
								accu_int_item
							FROM
								coop_account_transaction 
							WHERE
								transaction_time > '{$transaction_time}' 
								AND account_id = '{$account_id}'
							ORDER BY
								account_id ASC , transaction_time ASC";
                    $row2 = $this->db->query($sql2)->result_array();
                    $old_acc_int = 0;
                    if (!empty($row2)) {
                        foreach ($row2 AS $key2 => $val2) {
                            //@start เรียกใช้ ข้อมูล ดอกเบี้ยสะสม
                            $data_cal = array();
                            $data_cal['account_id'] = $val2['account_id'];
                            $data_cal['date_cal'] = $val2['transaction_time'];
                            $data_cal['date_start_cal'] = $val2['transaction_time'];
                            $data_cal_accu_int = $this->deposit_modal->cal_accu_int($data_cal);

                            $transaction_id = $val2['transaction_id'];

                            //if($val2['transaction_list'] == 'INT'){
                            if (in_array($val2['transaction_list'], array('INT', 'IN'))) {
                                $old_acc_int = 0;
                                $data_cal_accu_int['old_acc_int'] = 0;
                                $data_cal_accu_int['accu_int_item'] = 0;
                                echo $val2['transaction_list'] . '<br>';
                            }
                            //echo '<pre>'; print_r($data_cal_accu_int); echo '</pre>';exit;
                            if ($key2 == 0) {
                                $old_acc_int = $data_cal_accu_int['old_acc_int'];
                            } else {
                                $old_acc_int += $data_cal_accu_int['accu_int_item'];
                            }
//echo '<pre>'; print_r($data_cal_accu_int); echo '</pre>';
                            $old_acc_int = number_format($old_acc_int, 2, '.', '');
                            $data_insert['old_acc_int'] = $old_acc_int;
                            $data_insert['accu_int_item'] = $data_cal_accu_int['accu_int_item'];
                            $this->db->where('transaction_id', $transaction_id);
                            $this->db->update('coop_account_transaction', $data_insert);
                            if ($this->db->affected_rows()) {
                                $affected_rows++;
                            }
                        }

                    }
                    if ($affected_rows > 0) {
                        echo $account_id . "=>success";
                        echo '<br>';
                        //echo json_encode(["result" => "success"]);
                        //exit;
                    } else {
                        echo $account_id . "=>error";
                        echo '<br>';
                        //echo json_encode(["result" => "error"]);
                        //exit;
                    }
                }
            }
        }
        exit;
    }



}
