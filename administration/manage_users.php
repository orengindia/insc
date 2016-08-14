<?php

	/* Users page in admin panel */

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	$page = $_GET["page"];
	$user = ucfirst(strtolower($_GET["user"]));
	if($page=="") $page = 0;

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(4));
		define("TABLE",$admin_session->get_users_table($page,$user));
		$data['title'] = get_text(441,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_users");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}