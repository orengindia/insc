<?php

	/* Edit answer page in admin panel */

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	$answer = $admin_session->get_answer_info($_GET['id']);
	define("ID", $_GET["id"]);
	define("ANSWER", $answer['answer']);

	if($_SERVER['REQUEST_METHOD']=="POST") {
		$admin_session->edit_answer($_POST["id"], $_POST['answer']);
	}

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(6));
		$data['title'] = get_text(207,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_edit_answer");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}