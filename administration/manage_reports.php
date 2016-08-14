<?php

	/* Reports page in admin panel */

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	$page = $_GET["page"];
	$report = ucfirst(strtolower($_GET["report"]));
	if($page=="") $page = 0;

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(7));
		define("TABLE",$admin_session->get_reports_table($page,$report));
		$data['title'] = get_text(366,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_reports");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}