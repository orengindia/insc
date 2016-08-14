<?php

	/* Account settings page:	site/settings */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";
	checkuserloggedoff();

	$user = $engine_session->getuserdata(USER_ID, "*");
	$data['settings_username'] = $user['username'];
	$data['settings_name'] = $user['name'];
	$data['settings_email'] = $user['email'];
	$data['settings_website'] = $user['website'];
	$data['settings_bio'] = $user['bio'];
	$data['settings_country'] = $user['country'];
	$data['settings_pass'] = $engine_session->getuserdata(USER_ID, "PASS");
	$data['countries'] = $engine_session->getallcountries($data['settings_country']);

	if($_SERVER['REQUEST_METHOD']=="POST") { 
		$engine_session->updateusersettings($_POST["name"], $_POST["password"], $_POST["password2"], $_POST["email"], $_POST["website"], $_POST["bio"], $_POST["country"]); 
	}

	$data['title'] = get_text(105,1);
	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("header");
	$template_session->loadtpl("acc_settings", $data);
	$template_session->loadend();