<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invest extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('Invest_libraies', 'invest');
	}
	public function index() {
		$arr_data = array();

		$invest_types = $this->db->select("*")->from("coop_invest_type")->where("status = 1")->order_by("order")->get()->result_array();
		$invests = $this->db->select("t1.*, t2.end_date")
							->from("coop_invest as t1")
							->join("coop_invest_detail as t2", "t1.id = t2.invest_id AND t2.status = 1", "left")
							->where("t1.status != 3")
							->order_by("t1.start_date DESC")
							->get()->result_array();
		$invest_data = array();
		foreach($invests as $invest) {
			$invest_data[$invest['type']][] = $invest;
		}
		$arr_data['invest_types'] = $invest_types;
		$arr_data['invests'] = $invest_data;

		$total_data = $this->invest->get_total_data();
		$arr_data['total_data'] = $total_data;

		$this->libraries->template('invest/index',$arr_data);
	}

	public function edit() {
		$arr_data = array();

		$invest_type = !empty($_POST['invest_type']) ? $_POST['invest_type'] : $_POST['invest_type_sub'];

		if($invest_type == 1) {
			$sav_c = $_POST['sav_c'];
			$start_date = !empty($sav_c['start_date']) ? $this->center_function->ConvertToSQLDate($sav_c['start_date']) : NULL;
			$due_date = !empty($sav_c['due_date']) ? $this->center_function->ConvertToSQLDate($sav_c['due_date']) : NULL;
			$invest_id = $this->invest->edit_saving_coop($_POST['invest_id'], $sav_c['name'], $sav_c['amount'], $due_date, $sav_c['interest'], $sav_c['period'], $start_date, $sav_c['source'], $_POST['status']);
		} else if ($invest_type == 2) {
			$share_c = $_POST['share_c'];
			$invest_id = $this->invest->edit_share_coop($_POST['invest_id'], $share_c['name'], $share_c['period'], $share_c['source'], $_POST['status']);
		} else if ($invest_type == 3) {
			$bond = $_POST['bond'];
			$start_date = !empty($bond['start_date']) ? $this->center_function->ConvertToSQLDate($bond['start_date']) : NULL;
			$due_date = !empty($bond['due_date']) ? $this->center_function->ConvertToSQLDate($bond['due_date']) : NULL;
			$invest_id = $this->invest->edit_bond($_POST['invest_id'], $bond['aver_profit'], $bond['credit_rating'], $start_date, $due_date, $bond['invest_rate_text'], $bond['name'], $bond['department_name'], $bond['payment_method_text'], $bond['unit'], $bond['value_per_unit'], $bond['source'], $_POST['status']);
		} else if ($invest_type == 4) {
			$bond = $_POST['bond'];
			$start_date = !empty($bond['start_date']) ? $this->center_function->ConvertToSQLDate($bond['start_date']) : NULL;
			$due_date = !empty($bond['due_date']) ? $this->center_function->ConvertToSQLDate($bond['due_date']) : NULL;
			$invest_id = $this->invest->edit_private_share($_POST['invest_id'], $bond['aver_profit'], $bond['credit_rating'], $start_date, $due_date, $bond['invest_rate_text'], $bond['name'], $bond['department_name'], $bond['payment_method_text'], $bond['unit'], $bond['value_per_unit'], $bond['source'], $_POST['status']);
		} else if ($invest_type == 5) {
			$share_c = $_POST['share_s'];
			$invest_id = $this->invest->edit_set_share($_POST['invest_id'], $share_c['name'], $share_c['period'], $share_c['source'], $_POST['status']);
		}

		$arr_data['invest_id'] = $invest_id;
		echo json_encode($arr_data);
	}

	public function delete() {
		$result = $this->invest->delete($_POST['id']);
		echo json_encode($result);
	}

	public function get_invest_by_id() {
		$arr_data = array();

		$data = $this->invest->get_invest_data($_GET['id'], NULL, 1, 1, 1);
		$arr_data['data'] = $data;

		if($data['type'] == 5)  {
			$arr_data['share_value'] = $this->invest->get_invest_share_value($_GET['id'], NULL);
		}

		$total_data = $this->invest->get_total_data();
		$arr_data['total_data'] = $total_data;

		echo json_encode($arr_data);
	}

	public function add_interest() {
		$arr_data = array();
		$date = !empty($_POST['date']) ? $this->center_function->ConvertToSQLDate($_POST['date']) : NULL;

		$data = $this->invest->add_profit_transaction($_POST['id'], $_POST['invest_id'], $date, $_POST['rate'], $_POST['amount'], $_POST['note']);
		$arr_data['return'] = $data;

		echo json_encode($arr_data);
	}

	public function get_profit_transaction() {
		$arr_data = array();
		$transaction = $this->invest->get_profit_transaction_by_id($_GET['id']);
		$arr_data['data'] = $transaction;
		echo json_encode($arr_data);
	}

	public function add_transaction() {
		$date = !empty($_POST['date']) ? $this->center_function->ConvertToSQLDate($_POST['date']) : NULL;
		$data = $this->invest->add_transaction($_POST['id'], $_POST['invest_id'], $date, $_POST['unit'], $_POST['rate'], $_POST['amount'], $_POST['note'], $_POST['tran_type'], $_POST['fee']);
		$arr_data['return'] = $data;
		echo json_encode($arr_data);
	}

	public function get_transaction() {
		$arr_data = array();

		$data = $this->invest->get_transaction($_GET['id']);
		$arr_data['data'] = $data;
		echo json_encode($arr_data);
	}

	public function add_invest_share_value() {
		$arr_data = array();

		$invest_id = $_POST['invest_id'];
		$date = !empty($_POST['date']) ? $this->center_function->ConvertToSQLDate($_POST['date']) : NULL;
		$value = $_POST['amount'];
		$data = $this->invest->add_invest_share_value($invest_id, $date, $value);

		echo json_encode($arr_data);
	}

	public function get_invest_share_value() {
		$arr_data = array();

		$invest_id = $_GET['id'];
		$date = !empty($_GET['date']) ? $this->center_function->ConvertToSQLDate($_GET['date']) : NULL;
		$data = $this->invest->get_invest_share_value($invest_id, $date);
		$arr_data['share_val'] = $data;

		echo json_encode($arr_data);
	}

	public function remove_profit() {
		$arr_data = array();

		$data = $this->invest->remove_profit($_POST['id']);
		$arr_data['data'] = $data;
		echo json_encode($arr_data);
	}

	public function remove_transactrion() {
		$arr_data = array();

		$data = $this->invest->remove_transactrion($_POST['id']);
		$arr_data['data'] = $data;
		echo json_encode($arr_data);
	}

	public function detail() {
		$arr_data = array();

		$invest_types = $this->db->select("*")->from("coop_invest_type")->where("status = 1")->order_by("order")->get()->result_array();
		$arr_data['invest_id'] = $_POST['id'];
		$arr_data['invest_types'] = $invest_types;

		$this->libraries->template('invest/detail',$arr_data);
	}

	public function report_invest() {
		$invest_types = $this->db->select("*")->from("coop_invest_type")->where("status = 1")->order_by("order")->get()->result_array();
		$arr_data['invest_types'] = $invest_types;
		$this->libraries->template('invest/report_invest',$arr_data);
	}

	public function check_report_invest() {
		$where = "";
		if(!empty($_POST['type'])) {
			$where .= " AND type = '".$_POST['type']."'";
		}
		$invest = $this->db->select("*")->from("coop_invest")->where("status != 3".$where)->order_by("start_date DESC")->get()->row();
		if(!empty($invest)) {
			echo "success";
		} else {
			echo "no-data";
		}
	}

	public function report_invest_preview() {
		$where = "";
		if(!empty($_POST['type'])) {
			$where .= " AND type = '".$_POST['type']."'";
		}

		$invests = $this->db->select("t1.*, t2.name as type_name, t3.invest_date, t3.end_date")
							->from("coop_invest as t1")
							->join("coop_invest_type as t2" ,"t1.type = t2.id", "INNER")
							->join("coop_invest_detail as t3", "t1.id = t3.invest_id AND t3.status = 1", "LEFT")
							->where("t1.status != 3".$where)
							->order_by("t2.order, t1.name, t1.start_date")
							->get()->result_array();
		
		$arr_data['datas'] = $invest;
		$arr_data['total_data'] = $total_data;
		
		if($_POST['doc_type'] == "html") {
			$datas = array();
			$page = 0;
			$first_page_size = 18;
			$page_size = 30;
			$prev_level = "x";
			$index = 0;
			$first_page_level = 1;
			$page_index = 1;

			foreach($invests as $invest) {
				if($index < $first_page_size) {
					$page = 1;
					$page_index = 0;
				} else {
					if(($page_index > $first_page_size && $first_page_level == $page)
						|| ($page_index > $page_size && $first_page_level != $page)) {
						$page++;
						$page_index = 0;
					}
				}
				$datas[$page][] = $invest;
				$index++;
				$page_index++;
			}

			$arr_data['datas'] = $datas;
			$arr_data['page_all'] = $page;
			$arr_data['max'] = count($invests) - 1;
            $this->preview_libraries->template_preview('invest/report_invest_preview',$arr_data);
        } else if ($_POST['doc_type'] == "excel") {
			$arr_data['datas'] = $invests;
            $this->load->view('invest/report_invest_excel',$arr_data);
        }
	}

	public function reprot_profit_interest() {
		$invest_types = $this->db->select("*")->from("coop_invest_type")->where("status = 1 AND id IN (1,3,4)")->order_by("order")->get()->result_array();
		$arr_data['invest_types'] = $invest_types;
		$this->libraries->template('invest/report_profit_interest',$arr_data);
	}

	public function check_report_profit_interest() {
		$result = $this->invest->check_invest_profit($_POST['type'], array(1,3,4));
		echo $result;
	}

	public function report_profit_interest_preview() {
		$where = "";
		$result = $this->invest->get_profit($_POST['type'], array(1, 3, 4));

		$arr_data['datas'] = $invest;
		$arr_data['total_data'] = $total_data;

		if($_POST['doc_type'] == "html") {
			$datas = array();
			$page = 0;
			$first_page_size = 18;
			$page_size = 30;
			$prev_level = "x";
			$index = 0;
			$first_page_level = 1;
			$page_index = 1;

			$arr_data['datas'] = $result;
			$arr_data['page_all'] = $page;
			$arr_data['max'] = count($result) - 1;
            $this->preview_libraries->template_preview('invest/report_profit_interest_preview',$arr_data);
        } else if ($_POST['doc_type'] == "excel") {
			$arr_data['datas'] = $result;
            $this->load->view('invest/report_profit_interest_excel',$arr_data);
        }
	}

	public function reprot_profit_dividend() {
		$invest_types = $this->db->select("*")->from("coop_invest_type")->where("status = 1 AND id IN (2,5)")->order_by("order")->get()->result_array();
		$arr_data['invest_types'] = $invest_types;
		$this->libraries->template('invest/report_profit_dividend',$arr_data);
	}

	public function check_report_profit_dividend() {
		$result = $this->invest->check_invest_profit($_POST['type'], array(2,5));
		echo $result;
	}

	public function report_profit_dividend_preview() {
		$where = "";

		$result = $this->invest->get_profit($_POST['type'], array(2,5));

		$arr_data['datas'] = $invest;
		$arr_data['total_data'] = $total_data;

		if($_POST['doc_type'] == "html") {
			$datas = array();
			$page = 0;
			$first_page_size = 18;
			$page_size = 30;
			$prev_level = "x";
			$index = 0;
			$first_page_level = 1;
			$page_index = 1;

			$arr_data['datas'] = $result;
			$arr_data['page_all'] = $page;
			$arr_data['max'] = count($result) - 1;
            $this->preview_libraries->template_preview('invest/report_profit_dividend_preview',$arr_data);
        } else if ($_POST['doc_type'] == "excel") {
			$arr_data['datas'] = $result;
            $this->load->view('invest/report_profit_dividend_excel',$arr_data);
        }
	}

	public function report_maturity() {
		$invest_types = $this->db->select("*")->from("coop_invest_type")->where("status = 1 AND id IN (1,3,4)")->order_by("order")->get()->result_array();
		$arr_data['invest_types'] = $invest_types;
		$this->libraries->template('invest/report_maturity',$arr_data);
	}

	public function check_maturity() {
		$invest = $this->db->select("t1.id")
							->from("coop_invest as t1")
							->join("coop_invest_detail as t3", "t1.id = t3.invest_id AND t3.status = 1 AND DATE(t3.end_date) >= DATE(NOW())", "INNER")
							->where("t1.status != 3".$where)
							->get()->row();
		if(!empty($invest)) {
			echo "success";
		} else {
			echo "not-found-data";
		}
	}

	public function report_maturity_preview() {
		$where = "";
		if(!empty($_POST['type'])) {
			$where .= " AND t1.type = '".$_POST['type']."'";
		} else {
			$where .= " AND t1.type IN (1,3,4)";
		}

		$invests = $this->db->select("t1.*, t2.name as type_name, t3.invest_date, t3.end_date")
							->from("coop_invest as t1")
							->join("coop_invest_type as t2" ,"t1.type = t2.id", "INNER")
							->join("coop_invest_detail as t3", "t1.id = t3.invest_id AND t3.status = 1 AND DATE(t3.end_date) >= DATE(NOW())", "INNER")
							->where("t1.status != 3".$where)
							->order_by("t3.end_date DESC")
							->get()->result_array();
		
		$arr_data['datas'] = $invest;
		$arr_data['total_data'] = $total_data;
		
		if($_POST['doc_type'] == "html") {
			$datas = array();
			$page = 0;
			$first_page_size = 18;
			$page_size = 30;
			$prev_level = "x";
			$index = 0;
			$first_page_level = 1;
			$page_index = 1;

			foreach($invests as $invest) {
				if($index < $first_page_size) {
					$page = 1;
					$page_index = 0;
				} else {
					if(($page_index > $first_page_size && $first_page_level == $page)
						|| ($page_index > $page_size && $first_page_level != $page)) {
						$page++;
						$page_index = 0;
					}
				}
				$datas[$page][] = $invest;
				$index++;
				$page_index++;
			}

			$arr_data['datas'] = $datas;
			$arr_data['page_all'] = $page;
			$arr_data['max'] = count($invests) - 1;
            $this->preview_libraries->template_preview('invest/report_maturity_preview',$arr_data);
        } else if ($_POST['doc_type'] == "excel") {
			$arr_data['datas'] = $invests;
            $this->load->view('invest/report_maturity_excel',$arr_data);
        }
	}

	public function report_share_transactions() {
		$invest_types = $this->db->select("*")->from("coop_invest_type")->where("status = 1 AND id IN (2,5)")->order_by("order")->get()->result_array();
		$arr_data['invest_types'] = $invest_types;
		$this->libraries->template('invest/report_share_transactions',$arr_data);
	}

	public function check_share_transactions() {
		$from_date = $this->center_function->ConvertToSQLDate($_POST["from_date"]);
		$thru_date = $this->center_function->ConvertToSQLDate($_POST["thru_date"]);
        $transactions = $this->db->select("t1.id, t1.name, t2.amount, t2.fee, t2.tran_type, t3.name as type_name")
                                ->from("coop_invest as t1")
                                ->join("coop_invest_transaction as t2", "t1.id = t2.invest_id AND t2.status = 1 AND DATE(t2.date) >= DATE('".$from_date."') AND DATE(t2.date) <= DATE('".$thru_date."') AND t2.tran_type = 2", "INNER")
                                ->join("coop_invest_type as t3", "t1.type = t3.id", "INNER")
                                ->get()
								->row();
		if(!empty($transactions)) {
			echo "success";
		} else {
			echo "no-data";
		}
	}

	public function report_share_transaction_preview() {
		$arr_data = array();
		$from_date = $this->center_function->ConvertToSQLDate($_POST["from_date"]);
		$thru_date = $this->center_function->ConvertToSQLDate($_POST["thru_date"]);
		$arr_data['from_date'] = $from_date;
		$arr_data['thru_date'] = $thru_date;

		$result = $this->invest->get_share_transaction_report_data($from_date, $thru_date);
		$sell_transactions = $result['invest_sells'];
		$invest_balances = $result['invest_balances'];

		if($_POST['doc_type'] == "html") {
			$datas = array();
			$page = 0;
			$first_page_size = 18;
			$page_size = 30;
			$prev_level = "x";
			$index = 0;
			$first_page_level = 1;
			$page_index = 1;

			foreach($sell_transactions as $invest) {
				if($index < $first_page_size) {
					$page = 1;
					$page_index = 0;
				} else {
					if(($page_index > $first_page_size && $first_page_level == $page)
						|| ($page_index > $page_size && $first_page_level != $page)) {
						$page++;
						$page_index = 0;
					}
				}
				$datas[$page][] = $invest;
				$index++;
				$page_index++;
			}

			$arr_data['datas'] = $datas;
			$arr_data['invest_balances'] = $invest_balances;
			$arr_data['page_all'] = $page;
			$arr_data['max'] = count($sell_transactions) - 1;
            $this->preview_libraries->template_preview('invest/report_share_transaction_preview',$arr_data);
        } else if ($_POST['doc_type'] == "excel") {
			$arr_data['datas'] = $sell_transactions;
			$arr_data['invest_balances'] = $invest_balances;
            $this->load->view('invest/report_share_transaction_excel',$arr_data);
        }
	}

	public function report_invest_balance() {
		$invest_types = $this->db->select("*")->from("coop_invest_type")->where("status = 1 AND id IN (2,5)")->order_by("order")->get()->result_array();
		$arr_data['invest_types'] = $invest_types;
		$this->libraries->template('invest/report_invest_balance',$arr_data);
	}

	public function check_invest_balance() {
		$where = "";
		if(!empty($_POST['type'])) {
			$where .= " AND type = '".$_POST['type']."'";
		} else {
			$where .= " AND type IN (2,5)";
		}
		$invest = $this->db->select("*")->from("coop_invest")->where("status != 3".$where)->order_by("start_date DESC")->get()->row();
		if(!empty($invest)) {
			echo "success";
		} else {
			echo "no-data";
		}
	}

	public function report_invest_balance_preview() {
		$arr_data = array();

		$result = $this->invest->get_invest_balance($_POST['type'], array(2,5));

		if($_POST['doc_type'] == "html") {
			$datas = array();
			$page = 0;
			$first_page_size = 18;
			$page_size = 30;
			$prev_level = "x";
			$index = 0;
			$first_page_level = 1;
			$page_index = 1;

			foreach($result as $invest) {
				if($index < $first_page_size) {
					$page = 1;
					$page_index = 0;
				} else {
					if(($page_index > $first_page_size && $first_page_level == $page)
						|| ($page_index > $page_size && $first_page_level != $page)) {
						$page++;
						$page_index = 0;
					}
				}
				$datas[$page][] = $invest;
				$index++;
				$page_index++;
			}

			$arr_data['datas'] = $datas;
			$arr_data['invest_balances'] = $invest_balances;
			$arr_data['page_all'] = $page;
			$arr_data['max'] = count($result) - 1;
            $this->preview_libraries->template_preview('invest/report_invest_balance_preview',$arr_data);
        } else if ($_POST['doc_type'] == "excel") {
			$arr_data['datas'] = $result;
			$arr_data['invest_balances'] = $invest_balances;
            $this->load->view('invest/report_invest_balance_excel',$arr_data);
        }
	}

	
	public function report_expect_profit() {
		$invest_types = $this->db->select("*")->from("coop_invest_type")->where("status = 1 AND id IN (1,3,4)")->order_by("order")->get()->result_array();
		$arr_data['invest_types'] = $invest_types;
		$this->libraries->template('invest/report_expect_profit',$arr_data);
	}

	public function check_expect_profit() {
		$where = "";
		if(!empty($_POST['type'])) {
			$where .= " AND t1.type = '".$_POST['type']."'";
		} else {
			$where .= " AND t1.type IN (1,3,4)";
		}
		$invest = $this->db->select("*")
							->from("coop_invest as t1")
							->join("coop_invest_detail as t2", "t1.id = t2.invest_id AND DATE(t2.end_date) >= DATE(NOW()) AND t2.status = 1", "INNER")
							->where("t1.status != 3".$where)
							->get()->row();
		if(!empty($invest)) {
			echo "success";
		} else {
			echo "no-data";
		}
	}

	public function report_expect_profit_preview() {
		$arr_data = array();

		$result = $this->invest->get_expect_profit($_POST['type'], array(1,3,4));

		if($_POST['doc_type'] == "html") {
			$datas = array();
			$page = 0;
			$first_page_size = 18;
			$page_size = 30;
			$prev_level = "x";
			$index = 0;
			$first_page_level = 1;
			$page_index = 1;

			foreach($result as $invest) {
				if($index < $first_page_size) {
					$page = 1;
					$page_index = 0;
				} else {
					if(($page_index > $first_page_size && $first_page_level == $page)
						|| ($page_index > $page_size && $first_page_level != $page)) {
						$page++;
						$page_index = 0;
					}
				}
				$datas[$page][] = $invest;
				$index++;
				$page_index++;
			}

			$arr_data['datas'] = $datas;
			$arr_data['page_all'] = $page;
			$arr_data['max'] = count($result) - 1;
            $this->preview_libraries->template_preview('invest/report_expect_profit_preview',$arr_data);
        } else if ($_POST['doc_type'] == "excel") {
			$arr_data['datas'] = $result;
            $this->load->view('invest/report_expect_profit_excel',$arr_data);
        }
	}
}
