<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Loan_document extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
		$this->load->model("Loan_request_form_model");
		$this->load->model('report_loan_data_model');
    }

    public function index()
    {
        $this->manage_doc();
    }
    public function Draw()
    {
        $this->load->view("loan_document/draw");
    }
    public function edit_doc(){
        $result = $this->db->query("SELECT coop_type_pdf_details.*,t1.detail_data,t1.ref FROM coop_type_pdf_details LEFT JOIN coop_text_pdf as t1 on coop_type_pdf_details.data_name = t1.code WHERE coop_type_pdf_details.type_pdf_id = " . $_GET['id'] . " ORDER BY id asc")->result_array();
        $arr_data['data_details'] = $result;
        $text = $this->db->query("SELECT * FROM coop_text_pdf ORDER BY code asc")->result_array();
        $arr_data['text'] = $text;
        $result2 = $this->db->query("SELECT * FROM coop_type_pdf where id ='" . $_GET['id'] . "'")->result_array();
        $arr_data['path_data'] = $result2;
        $this->libraries->template('loan_document/draw_edit', $arr_data);
    }

    public function manage_doc(){
        $result = $this->db->query("SELECT * FROM coop_type_pdf")->result_array();
        foreach ($result as $k => $results) {
            if ($results['type_loan'] == '11') {
                $result[$k]['type_loan'] = "หนังสือสัญญา";
            }
            if ($results['type_loan'] == '12') {
                $result[$k]['type_loan'] = "หนังสือค้ำประกัน";
            }
            if ($results['type_loan'] == '13') {
                $result[$k]['type_loan'] = "หนังสือรับรองเงินเดือน";
            }
            if ($results['type_loan'] == '1') {
                $result[$k]['type_loan'] = "คำขอกู้เงินกู้ฉุกเฉิน";
            }
            if ($results['type_loan'] == '2') {
                $result[$k]['type_loan'] = "คำขอกู้เงินกู้สามัญ";
            }
            if ($results['type_loan'] == '3') {
                $result[$k]['type_loan'] = "คำขอกู้เงินกู้พิเศษ";
            }
            if ($results['type_loan'] == '5') {
                $result[$k]['type_loan'] = "คำขอกู้เงินกู้เพื่อการเคหะฯ";
            }
        }
        $array_data['result'] = $result;
        $this->libraries->template('loan_document/manage_file', $array_data);
    }

    public function get_text(){ 
        $text_pdf_type = $this->db->query("SELECT * FROM coop_text_pdf_type")->result_array();
        $text_pdf = $this->db->query("SELECT * FROM coop_text_pdf WHERE `condition` = '' ORDER BY type_id asc")->result_array();
        $result['text_pdf_type']= $text_pdf_type;
        $result['text_pdf']= $text_pdf;
        echo json_encode($result);
    }

    public function get_box(){
        $result = $this->db->query("SELECT * FROM coop_text_pdf WHERE `condition` != '' ORDER BY code asc")->result_array();
        echo json_encode($result);
    }

    public function get_loan(){
        $result = $this->db->query("SELECT loan_type,id,order_by FROM coop_loan_type")->result_array();
        echo json_encode($result);
    }

    public function save(){
        $this->db->set('type_pdf_id',  $_POST['type_pdf']);
        $this->db->set('type_loan', $_POST['loan_type']);
        $this->db->set('details',  $_POST['file_name']);
        $this->db->where('id',  $_POST['type_id']);
        $this->db->update('coop_type_pdf');

        $condi = $this->db->query("SELECT * FROM `coop_text_pdf`")->result_array();

        for ($i = 0; $i < $_POST["max_count"];) {
            $data =
                array(
                    'type_pdf_id' =>  $_POST['type_id'],
                    'page_no' => $_POST['page_no_' . $i],
                    'x_point' => $_POST['label_x_' . $i],
                    'y_point' => $_POST['label_y_' . $i],
                    'text_width' => $_POST['w_' . $i],
                    'text_height' => $_POST['h_' . $i],
                    'data_name' => $_POST['data_name_' . $i],
                    'detail' => '',
                    'fonts' => $_POST['fonts_' . $i],
                    'fonts_size' => $_POST['font_size_' . $i],
                    'id' =>  $_POST['id_' . $i],
                    'text_point' =>  $_POST['point_' . $i],
                );

            foreach ($condi as $k => $c) {
                for ($is = 1; $is <= $_POST["max_count"];) {
                    if ($data['data_name'] == $c['code']) {
                        $data['detail'] = $c['condition'];
                    }
                    $is++;
                }
            }

            if ($data['id'] != '') {
                # UPDATE
                $this->db->where('id', $data['id']);
                $this->db->update('coop_type_pdf_details', $data);
            } else {
                if ($data['data_name'] != '') {
                    $array = array(
                        'type_pdf_id' => $data['type_pdf_id'],
                        'page_no' => $data['page_no'],
                        'x_point' => $data['x_point'],
                        'y_point' => $data['y_point'],
                        'text_width' => $data['text_width'],
                        'text_height' => $data['text_height'],
                        'data_name' => $data['data_name'],
                        'detail' => $data['detail'],
                        'fonts' => $data['fonts'],
                        'fonts_size' => $data['fonts_size'],
                        'text_point' =>  $data['text_point']
                    );
                    $this->db->set($array);
                    $this->db->insert('coop_type_pdf_details');
                } else {
                    continue;
                }
            }
            $i++;
        }
        header('Location: /Loan_document/manage_doc');
    }

    public function get_iframe(){
        $result = $this->db->query("SELECT * FROM coop_type_pdf WHERE id = '" . $_GET['id'] . "'")->result_array();
        echo json_encode($result);
    }

    function add_file_pdf(){
        $config['upload_path']          = FCPATH . 'assets/document/loan_request';
        $config['allowed_types']        = 'pdf';
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('pdf')) {
            $message = "อัพโหลดไม่สำเร็จ";
        } else {
            $id = $this->db->query("SELECT max(id) as id FROM coop_type_pdf ")->result_array();
            $data = array('upload_data' => $this->upload->data());
            $data_details = array(
                "id" => ($id[0]['id'] + 1),
                "type_pdf_id" => $_POST['type_pdf'],
                "type_loan" => $_POST['loan_type'],
                "path_data" => $data['upload_data']['file_name'],
                "details" => $_POST['file_name'],
                "createdatetime" => date('Y-m-d')
            );
            $this->db->insert('coop_type_pdf', $data_details);
            $arr_data['message'] = "อัพโหลดสำเร็จ";
            $this->manage_doc($arr_data);
        }
        header('Location: /Loan_document/manage_doc');

    }

    function remove_data_details(){
        $id =  $_POST['id'];
        $this->db->query("DELETE FROM coop_type_pdf_details WHERE id = '" . $id . "'");
        echo json_encode("ลบข้อมูลเรียบร้อย");
    }

    public function remove_file_loan(){
        $id = $_GET['id'];
        $this->db->query("DELETE FROM coop_type_pdf_details WHERE type_pdf_id = '" . $id . "'");
        $this->db->query("DELETE FROM coop_type_pdf WHERE id = '" . $id . "'");
        header('Location: /Loan_document/manage_doc');
    }

	public function loan_request_form_pdf()
	{
		$arr_data=$this->Loan_request_form_model->loan_request_form_pdf_model($_GET['member_id'],$_GET['loan_id']);
		$this->load->view('loan_document/petition_pdf',$arr_data);
    }
}
