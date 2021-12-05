<?php


class Installment extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Installment_Model", "installment");

    }


    /**
     * Show modal installment
     * @rounter /installment/:id || /installment
     */
    public function index($id){
        $arr_data = array();

        //$loan_id = $this->input->post('loan_id', TRUE); //defined contract id
        $loan_id = $id;

        $contract = $this->contract->findContract($loan_id);
        $reason = array_column($this->contract->getLoanReason(), 'loan_reason', 'loan_reason_id');
        $arr_data['contract'] = (array)$contract;
        $arr_data['contract']['loan_reason'] = $reason[$contract->loan_reason];
        $arr_data['member'] = $this->info->member($contract->member_id)->getInfoArray();
        $arr_data['member']['full_name_th'] = $this->info->getFullName();
        $arr_data['installment'] = $this->installment->getList($loan_id);
        $arr_data['status'] = array("อนุมัติแล้ว", "โอนแล้ว");

        $this->libraries->template("installment/index", $arr_data);
    }

    public function ajax_approve_contract(){

    }

    public function update_amt(){

        $response = array();

        $data = $this->input->post();

        $arr_data = array(
           'installment_amount' => $data['amount']
        );

        if(!isset($data['loan_id']) || empty($data['loan_id'])){
            $response = array('status' => 400, 'status_code' => "error", "data" => $data,"msg" => "no contract", );
        }else{
            $response = $this->installment->update_amount($data['loan_id'], $arr_data);
        }

        $this->output->set_content_type("application/json", "utf8");
        $this->output->_display();
        echo json_encode($response);
        exit;
    }

    public function approve(){
        $res = array();
        $data = $this->input->post();
        $data['installment']['transaction_datetime'] =  date('Y-m-d', strtotime(str_replace('/','-', $data['installment']['datetime']). " -543 Year"));
        $data['installment']['amount'] = str_replace(",", "", $data['installment']['amount']);
        $data['loan']['date_approve'] =  date('Y-m-d', strtotime(str_replace('/','-', $data['loan']['date_approve']). " -543 Year"));

        unset($data['installment']['datetime']);
        if(!isset($data['loan']) || empty($data['loan'])){
            $res = $this->installment->getErr();
        }else{
            $res = $this->installment->approve($data);
        }
        $this->output->set_content_type("application/json", "utf8");
        $this->output->_display();
        echo json_encode($res);
        exit;
    }

    function loan_transfer_save(){

        $amount_transfer = @str_replace(',','',@$_POST['amount_transfer']);

        $this->db->select(array(
            'coop_loan.loan_amount',
            'coop_loan.loan_type',
            'coop_loan.member_id',
            'coop_loan_name.loan_type_id',
            'coop_loan.loan_application_id'
        ));
        $this->db->from('coop_loan');
        $this->db->join('coop_loan_name','coop_loan.loan_type = coop_loan_name.loan_name_id','inner');
        $this->db->where("coop_loan.id = '".@$_POST['loan_id']."'");
        $row_loan = $this->db->get()->result_array();
        $row_loan = $row_loan[0];
        $loan_amount = @$row_loan['loan_amount'];
        $member_id = @$row_loan['member_id'];

        $date_arr = explode('/',@$_POST['date_transfer']);
        $date_transfer = ($date_arr[2]-543)."-".$date_arr[1]."-".$date_arr[0]." ".@$_POST['time_transfer'];

        $data_insert = array();
        $data_insert['loan_id'] = $_POST['loan_id'];
        $data_insert['account_id'] = $_POST['account_id'];
        $data_insert['date_transfer'] = $date_transfer;
        $data_insert['createdatetime'] = date('Y-m-d H:i:s');
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['transfer_status'] = '0';
        $data_insert['amount_transfer'] = @$amount_transfer;
        $data_insert['pay_type'] = @$_POST['pay_type'];
        if(@$_POST['pay_type'] == '2'){
            $data_insert['dividend_bank_id'] = @$_POST['dividend_bank_id'];
            $data_insert['dividend_acc_num'] = @$_POST['dividend_acc_num'];
        }

        if(@$_POST['pay_type'] == '4'){
            $data_insert['cheque_book_no'] = $_POST['cheque_book_no'];
            $data_insert['cheque_no'] = $_POST['cheque_no'];
        }
        $this->db->insert('coop_loan_transfer', $data_insert);

        $data['transfer_status'] = 1;
        $data['last_editor'] = $_SESSION['USER_ID'];
        $this->installment->modify($_POST['loan_id'], $_POST['seq'], $data);

        $this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
        echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan/loan_transfer?loan_id='.$_POST['loan_id'])."' </script>";
        exit;
    }

}
