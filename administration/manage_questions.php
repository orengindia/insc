<?php

	/* Questions page in admin panel */

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	$page = $_GET["page"];
	$question = ucfirst(strtolower($_GET["question"]));
	if($page=="") $page = 0;

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(5));
		define("TABLE",$admin_session->get_questions_table($page,$question));
		$data['title'] = get_text(435,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_questions");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}