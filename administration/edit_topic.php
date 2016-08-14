<?php

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	$topic = $admin_session->get_topic_info($_GET['id']);
	define("ID", $_GET["id"]);
	define("NAME", $topic['name']);
	define("URL", $topic['url']);
	define("PHOTO", $topic['image']);
	define("DESCRIPTION", $topic['description']);

	if($_SERVER['REQUEST_METHOD']=="POST") {
		$admin_session->edit_topic($_POST["id"], $_POST['topic_name'], $_POST['topic_url'], $_FILES['photo'], $_POST['topic_description']);
	}

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(3));
		$data['title'] = ucfirst(get_text(404,1));
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_edit_topic");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}