<?php

	/* Topics page:		administration/manage_topics.php */

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	$page = $_GET["page"];
	$topic = ucfirst(strtolower($_GET["topic"]));
	if($page=="") $page = 0;

	if($_SERVER['REQUEST_METHOD']=="POST") {
		$admin_session->add_topic($_POST["topic_name"], $_FILES["photo"], $_POST["topic_description"]);
	}

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(3));
		define("TABLE",$admin_session->get_topics_table($page,$topic));
		$data['title'] = get_text(421,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_topics");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}