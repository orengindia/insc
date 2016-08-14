<?php

	/* Disable page setting:	site/disable */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";
	checkuserloggedoff();

	if($_SERVER['REQUEST_METHOD']=="POST") { 
		$engine_session->disableaccount(); 
	}

	$data['title'] = get_text(91,1);
	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("header");
	$template_session->loadtpl("acc_disable");
	$template_session->loadend();