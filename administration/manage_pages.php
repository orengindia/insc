<?php

	/* Pages list in admin panel */

	define("ADMIN", 1);
	require_once '../core.php';
	checkuserloggedoff();

	$about_page = $engine_session->get_page(1);
	$points_page = $engine_session->get_page(2);
	$privacy_page = $engine_session->get_page(3);
	$terms_page = $engine_session->get_page(4);
	define("ABOUT_US_C", $about_page['content']);
	define("POINTS_C", $points_page['content']);
	define("PRIVACY_C", $privacy_page['content']);
	define("TERMS_C", $terms_page['content']);

	if($_SERVER['REQUEST_METHOD']=="POST") {
		$admin_session->save_page($_POST["id"], $_POST["content"]);
	}

	if(USER_RANK==1) {
		define("serializeHmTabs", $admin_session->serializehmtabs(8));
		$data['title'] = get_text(361,1);
		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl("hm_pages");
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('');
	}