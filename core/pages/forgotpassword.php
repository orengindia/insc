<?php

	/* Forgot password page:  	site/forgotpassword */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";
	checkuserloggedin();

	if($_SERVER['REQUEST_METHOD']=="POST") { 
		$data['response'] = $engine_session->recoverpassword($_POST["code"]); 
	}
	else {
		$data['response'] = '';
	}

	$data['title'] = get_text(41,1);

	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("forgotpassword", $data);
	$template_session->loadend();