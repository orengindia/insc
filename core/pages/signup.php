<?php

	/* Registration page:	site/signup */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";
	checkuserloggedin();

	$signUp["username"] = strtolower($_POST["username"]);
	$signUp["password"] = $_POST["password"];
	$signUp["name"] = ucfirst(strtolower($_POST["name"]));
	$signUp["email"] = strtolower($_POST["email"]);
	$signUp["country"] = $_POST["country"];

	if($_SERVER['REQUEST_METHOD']=="POST") {
		if($_SESSION['regnetwork']) {
			$engine_session->createuseraccountsocial($signUp["username"], $signUp["email"], $_SESSION['regnetwork'], $_SESSION['regid'], $signUp["name"], $_SESSION['regphoto_big'], $signUp['country']);
		}
		else {
			if($_POST['captcha']==$_SESSION['cap_code']) {
				$engine_session->createuseraccount($signUp["username"], $signUp["password"], $signUp["name"], $signUp["email"], $signUp['country']); 
			}
			else {
				$engine_session->captchaerror();
			}
		}
	}

	$data['title'] = get_text(53,1);
	$data['countries'] = $engine_session->getallcountries();

	$template_session->loadtpl("head", $data);
	if($_SESSION['regnetwork']) {
		$template_session->loadtpl("signupsocial", $data);
	}
	else {
		$template_session->loadtpl("signup", $data);
	}
	$template_session->loadend();