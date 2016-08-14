<?php

	/* Anwers list in admin panel */

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	$page = $_GET["page"];
	$answer = ucfirst(strtolower($_GET["answer"]));
	if($page == "") $page = 0;

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(6));
		define("TABLE",$admin_session->get_answers_table($page,$answer));
		$data['title'] = get_text(445,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_answers");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}