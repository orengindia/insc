<?php

	/* Edit question page */

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	$question = $admin_session->get_question_info($_GET['id']);
	define("ID", $_GET["id"]);
	define("NAME", $question['question']);
	define("PHOTO", $question['image']);
	define("DESCRIPTION", $question['description']);
	define("CATEGORY", $admin_session->get_categories($question['category']));
	define("ADD_CATEGORY", $admin_session->select_category());

	if($_SERVER['REQUEST_METHOD']=="POST") {
		$admin_session->edit_question($_POST["id"], $_POST['question'], $_POST['description'], $_FILES['photo'], $_POST['categories']);
	}

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(5));
		$data['title'] = get_text(205,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_edit_question");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}