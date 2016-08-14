<?php

	/* Contact page:	site/contact */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";

	/* For not showing success sent message */
	$data['success'] = 0;

	if(SESSION_STATUS!='') {
		$u_data = $engine_session->getuserdata(USER_ID, "*");
		define("EMAIL", $u_data['email']);
		define("NAME", $u_data['name']);
	}

	if($_SERVER['REQUEST_METHOD']=="POST") {
		$engine_session->contact($_POST['name'], $_POST['email'], $_POST['subject'], $_POST['message']);
		$data['success'] = 1;
	}

	$data['title'] = get_text(148,1);

	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("header");
	$template_session->loadtpl("contact", $data);
	$template_session->loadend();