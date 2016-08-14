<?php

	/* Delete report page */

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	define("ID", $_GET["id"]);

	if($_SERVER['REQUEST_METHOD']=="POST") {
		$admin_session->delete_report($_POST["id"]);
	}

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(7));
		$data['title'] = get_text(374,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_delete_report");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}