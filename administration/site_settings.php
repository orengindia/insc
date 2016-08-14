<?php

	/* Website settings: 	administration/site_settings.php */

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	if($_SERVER['REQUEST_METHOD']=="POST") { 
		$admin_session->updatesitesettings($_POST["sitename"], $_POST["metadesc"], $_POST["metakey"], $_POST["fb_id"], $_POST["fb_secret"], $_POST["tw_key"], $_POST["tw_secret"], $_POST["adsense"], $_POST["site_email"], $_POST["smtp_host"], $_POST["smtp_port"], $_POST["smtp_user"], $_POST["smtp_pass"], $_POST["site_fb"], $_POST["site_tw"], $_POST["signup_confirmation"], $_POST["filter_word"]);
	}

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(2));
		$data['title'] = get_text(328,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_settings");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}