<?php

	/* Main admin page - dashboard: 	administration/index.php */

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	define("QUESTIONS_ROWS", $admin_session->numofrows("questions"));
	define("ANSWERS_ROWS", $admin_session->numofrows("answers"));
	define("REPORTS_ROWS", $admin_session->numofrows("reports"));
	define("USERS_ROWS", $admin_session->numofrows("users"));
	define("ONLINE_USERS_ROWS", $engine_session->onlineusers());

	$gr_users = $admin_session->get_chart('users');
	$today = date('z')+1;
	$users_array = array();
	for($i=$today-21;$i<=$today;$i++) {
		$users_array[$i]['g_day'] = $i;
		$ago = date('z')-$i;
		$users_array[$i]['g_date'] = date('d.m', strtotime('now -'.$ago.' days') );
		$users_array[$i]['g_cant'] = 0;
		foreach($gr_users as $ii) {
			if($ii['g_day']==$i) {
				$users_array[$i-1]['g_day'] = $ii['g_day'];
				$users_array[$i-1]['g_date'] = date("d.m", strtotime($ii['g_date']));
				$users_array[$i-1]['g_cant'] = $ii['g_cant'];
			}
		}
	}
	$user_labels = '';
	foreach($users_array as $ii) {
		$user_labels .= ', "'.$ii['g_date'].'"';
	}
	$user_labels = substr($user_labels, 1);
	$user_datasets = '';
	foreach($users_array as $ii) {
		$user_datasets .= ', '.$ii['g_cant'];
	}
	$user_datasets = substr($user_datasets, 1);
	define("USERS_GR_LABELS", $user_labels);
	define("USERS_GR_DATASETS", $user_datasets);

	$gr_questions = $admin_session->get_chart('questions');
	$today = date('z')+1;
	$users_array = array();
	for($i=$today-21;$i<=$today;$i++) {
		$questions_array[$i]['g_day'] = $i;
		$ago = date('z')-$i;
		$questions_array[$i]['g_date'] = date('d.m', strtotime('now -'.$ago.' days') );
		$questions_array[$i]['g_cant'] = 0;
		foreach($gr_questions as $ii) {
			if($ii['g_day']==$i) {
				$questions_array[$i-1]['g_day'] = $ii['g_day'];
				$questions_array[$i-1]['g_date'] = date("d.m", strtotime($ii['g_date']));
				$questions_array[$i-1]['g_cant'] = $ii['g_cant'];
			}
		}
	}	
	$questions_labels = '';
	foreach($questions_array as $ii) {
		$questions_labels .= ', "'.$ii['g_date'].'"';
	}
	$questions_labels = substr($questions_labels, 1);
	$questions_datasets = '';
	foreach($questions_array as $ii) {
		$questions_datasets .= ', '.$ii['g_cant'];
	}
	$questions_datasets = substr($questions_datasets, 1);
	define("QUESTIONS_GR_LABELS", $questions_labels);
	define("QUESTIONS_GR_DATASETS", $questions_datasets);

	$gr_answers = $admin_session->get_chart('answers');
	$today = date('z')+1;
	$answers_array = array();
	for($i=$today-21;$i<=$today;$i++) {
		$answers_array[$i]['g_day'] = $i;
		$ago = date('z')-$i;
		$answers_array[$i]['g_date'] = date('d.m', strtotime('now -'.$ago.' days') );
		$answers_array[$i]['g_cant'] = 0;
		foreach($gr_answers as $ii) {
			if($ii['g_day']==$i) {
				$answers_array[$i-1]['g_day'] = $ii['g_day'];
				$answers_array[$i-1]['g_date'] = date("d.m", strtotime($ii['g_date']));
				$answers_array[$i-1]['g_cant'] = $ii['g_cant'];
			}
		}
	}
	$answer_labels = '';
	foreach($answers_array as $ii) {
		$answer_labels .= ', "'.$ii['g_date'].'"';
	}
	$answer_labels = substr($answer_labels, 1);
	$answer_datasets = '';
	foreach($answers_array as $ii) {
		$answer_datasets .= ', '.$ii['g_cant'];
	}
	$answer_datasets = substr($answer_datasets, 1);
	define("ANSWERS_GR_LABELS", $answer_labels);
	define("ANSWERS_GR_DATASETS", $answer_datasets);

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(1));
		$data['title'] = get_text(312,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("dashboard");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin();
	}