<?php

	/* Terms page: 		site/privacy */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";

	/* Getting page content. 3 - privacy page */
	$page = $engine_session->get_page(3);
	$data['content'] = $page['content'];
	$data['time'] = get_time($page['time']);
	$data['title'] = get_text(40,1);

	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("header");
	$template_session->loadtpl("privacy", $data);
	$template_session->loadend();