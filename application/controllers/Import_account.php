<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Import_account extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Import_account_transaction", "imp_account_trx");
    }

    public function index(){
        echo "555";
    }

    public function run_script(){

        $data = $this->imp_account_trx->execute();
        $upStatus = array();
        $upStatus['status'] =  1;
        $this->db->update('temp_import_account_transaction_file', $upStatus);
        $this->db->where('id',$_POST['id']);
        echo "<pre>"; print_r($data); exit;
    }

    public function run_receipt_script(){
        $upStatus = array();
        $upStatus['receipt_status'] =  1;
        $this->db->update('temp_import_account_transaction_file', $upStatus);
        $this->db->where('file_id',$_POST['id']);
        echo "<pre>"; print_r($this->imp_account_trx->executeReceipt()); exit;
    }
    public function delete_file($id = ""){
        $this->db->where("id", $id);
        $this->db->delete("temp_import_account_transaction_file");

        $this->db->where("file_id", $id);
        $this->db->delete("temp_import_account_transaction");

        $this->center_function->toast('ลบข้อมูลเรียบร้อยแล้ว');
        echo "<script>document.location.href='" . base_url(PROJECTPATH . '/import_deposit_monthly/import_excel') . "'</script>";
    }

}