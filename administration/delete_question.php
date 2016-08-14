<?php

	/* Delete question pafge */

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	$topic = $admin_session->get_question_info($_GET['id']);
	define("ID", $_GET["id"]);
	define("NAME", $topic['question']);

	if($_SERVER['REQUEST_METHOD']=="POST") {
		$admin_session->delete_question($_POST["id"]);
	}

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(5));
		$data['title'] = get_text(373,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_delete_question");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}