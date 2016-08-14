<?php

	/* Sign in page:	 site/login */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";
	checkuserloggedin();

	$username = strtolower($_POST["user_name"]);
	$password = $_POST["password"];

	if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['submit'])) {
		$_SESSION['logtry'] = $username;
		$_SESSION['passtry'] = $password;
		$engine_session->checkcookies($username, $password); 
	}
	else if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['reactive'])) {
		$engine_session->reactive($_SESSION['logtry']); 
	}
	else if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['send_confirmation'])) {
		$engine_session->send_confirmation($_SESSION['logtry']); 
	}

	$data[title] = get_text(9,1);

	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("login");
	$template_session->loadend();