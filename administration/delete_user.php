<?php

	/* Delete user page */

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	$topic = $admin_session->get_user_info($_GET['id']);
	define("ID", $_GET["id"]);
	define("USERNAME", $topic['username']);
	define("NAME", $topic['name']);

	if($_SERVER['REQUEST_METHOD']=="POST") {
		$admin_session->delete_user($_POST["id"]);
	}

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(4));
		$data['title'] = get_text(382,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_delete_user");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}