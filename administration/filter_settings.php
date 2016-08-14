<?php
	
	/* Filter settings */
	
	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	if($_SERVER['REQUEST_METHOD']=="POST") { 
		$admin_session->updatefilter($_POST["words"], $_POST["ips"]);
	}

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(9));
		$data['title'] = get_text(328,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_filter");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}