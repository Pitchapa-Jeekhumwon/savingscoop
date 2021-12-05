<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Import_deposit_monthly extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Import_account_transaction", "imp_account_trx");
    }
    public function import_excel()
    {
        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');

        $where = " YEAR(`date_data`)='{$year}' AND MONTH(`date_data`)='{$month}' ";
        $data = $this->db->select('*')->from('temp_import_account_transaction_file')->get()->result_array();
        $list = array();
        foreach ($data as $key => $item) {
            $list[date('Y-m-d', strtotime($item['date_data']))] = $item;
        }
        $arr_data['file_list'] = $list;
        $arr_data['data'] = $data;
        $this->libraries->template('import_excell/import_excell', $arr_data);
    }

    public function file_save()
    {
        $file = $_FILES['file']['name'];
        $date_1 = str_replace('ถอนเงินฝากซ์้อหุ้น', '', $file);
        $date_2 = str_replace('.xls', '', $date_1);
        $day = substr($date_2, -6, 2);
        $month = substr($date_2, -4, 2);
        $year = substr($date_2, -2, 2);
        $sum_date = '25' . $year . '-' . $month . '-' . $day;
        $date = date('Y-m-d H:i:s', strtotime("-543 year", strtotime($sum_date)));
        $save_file = $_FILES['file'];
        $submit_date = date('Y-m-d H:i:s');
        //echo  $date ;exit;
        $check_file = $this->db->select('*')->from('temp_import_account_transaction_file')
        ->where("date_data = '$date'")->get()->row_array();

        $this->load->library('myexcel'); //Load Library PHP_EXCEL
        $files = array();
        if(!empty($_FILES)){
            $files = $this->imp_account_trx->uploadTempTransaction($_FILES);
        }

        if (!empty($check_file)) {
            echo json_encode(["result" => "null"]);
            exit;
        } else {

            $sheetName = $this->imp_account_trx->getSheetName($files);
            $_char = '25'.substr($sheetName, 4, 2)."-".substr($sheetName, 2, 2)."-".substr($sheetName, 0, 2);
            $_char = date("Y-m-d", strtotime($_char. "-543 YEAR"));
            //dmy to Y-m-d
                if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$_char)){ //check format Y-m-d
                $_char = $_char." 00:00:00";
                $insert_file = array();
                $insert_file["id"] = "0";
                $insert_file["file_name"] =  $save_file["name"];
                $insert_file["submit_date"] = $submit_date;
                $insert_file["date_data"] = $_char;
                $this->db->insert('temp_import_account_transaction_file', $insert_file);
                }else{
                    return false ;
                }

        }

        $input_file_id = $this->db->select('id')->from('temp_import_account_transaction_file')
            ->where("date_data = '$_char'")->get()->row_array();

        if (!empty($_FILES)) {

            $datas = $this->read_excel($files);

            if (!empty($datas)) {
                foreach ($datas as $key => $data) {
                    if (!empty($data["A"])) {

                        $account_id = $data["D"];
                        $data_insert = array();
                        $data_insert["id"] = "0";
                        $data_insert["member_id"] =  sprintf('%06d',$data["B"]);
                        $data_insert["account_id"] = $data["D"];
                        $data_insert["transaction_withdrawal"] = str_replace(',', '', $data["H"]);
                        $data_insert["transaction_deposit"] = "0";
                        $data_insert["date_data"] = $_char;
                        $data_insert["file_id"] =  $input_file_id['id'];
                        $data_inserts[] = $data_insert;
                    }
                }
            }
            if (!empty($data_inserts)) {
                $this->db->insert_batch('temp_import_account_transaction', $data_inserts);
                echo json_encode(["result" => "true"]);
            }
            exit;
        }
    }



    public function read_excel($files)
    {
        return $this->excelReader($files);
    }

    public function excelReader($fileName){
        $types = array('Excel2007', 'Excel5', 'Excel2016');
        foreach ($types as $type) {
            $reader = PHPExcel_IOFactory::createReader($type);
            if ($reader->canRead($fileName['file_path'])) {
                $valid = true;
                break;
            }
        }

        if (!empty($valid)) {
            $objPHPExcel = PHPExcel_IOFactory::load($fileName['file_path']);

            $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
            $SheetNames = $objPHPExcel->getSheetNames();
            $datas = array();
            foreach ($cell_collection as $cell) {
                if ($objPHPExcel->getActiveSheet()->getCell($cell)->getValue() == '') {
                    continue;
                }

                $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                if ($row > 3) {
                    $datas[$row][$column] = $data_value;
                }
            }

            unlink($fileName['file_path']);
            return $datas;
        }
        return array();
    }

    public function show_data_file()
    {
        $get = $this->input->get();

        if (isset($get['file_id'])) {
            $this->db->select('member_id,account_id,transaction_withdrawal,date_data');
            $this->db->from('temp_import_account_transaction');
            $this->db->where("file_id = '" . $get['file_id'] . "' ");
            $show_data = $this->db->get()->result_array();
            $html = "";
            $num = 0;
            foreach ($show_data as $key => $rows) {
                $html .= "<tr>";
                $html .= "<td class=\"text-center\">" . (++$num) . "</td>";
                foreach ($rows as $key_2 => $item) {
                    if ($key_2 == 'transaction_withdrawal') {
                        $html .= "<td class=\"text-right\">" . (!empty($item) ? number_format($item, 2) : "") . '</td>';
                    } else if ($key_2 == 'date_data') {
                        $html .= "<td class=\"text-center\">" . $this->center_function->ConvertToThaiDate(date('Y-m-d', strtotime($item))) . '</td>';
                    } else if ($key_2 == 'member_id') {
                        $html .= "<td class=\"text-center\">" . sprintf("%06d", $item) . '</td>';
                    } else {
                        $html .= "<td class=\"text-center\">" . $item . '</td>';
                    }
                }
                $html .= "</tr>";
            }
            echo $html;
            exit;
        }
        echo "";
        exit;
    }
}
