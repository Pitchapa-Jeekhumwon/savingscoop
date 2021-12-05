<style>
	label { padding-top: 7px; }
	.form-r { background-color: #e0e0e0; }
	.table thead tr th { text-align: center; }
</style>
<?php
	$act = @$_GET['act'];
	$id = (int)$_GET['id'];
	if( $act == 'add' ) {
		$this->load->view('meeting/inc.meeting.manage.php');
	} elseif($act == 'reward') {
		$this->load->view('meeting/inc.meeting.reward.php', ['meeting_id' => $id, 'page_title' => 'งานประชุมใหญ่']);
		$this->load->view('search_member_modal_jquery');
	} elseif($act == 'detail') {
		$this->load->view('meeting/inc.meeting.detail.php');
	} else {
		$this->load->view('meeting/inc.meeting.php');
	}