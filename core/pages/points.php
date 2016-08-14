<?php

	/* About points page: 		site/points */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";

	/* Getting page content. 2 - about points page */
	$page = $engine_session->get_page(2);
	$data['content'] = $page['content'];
	$data['time'] = get_time($page['time']);
	$data['title'] = get_text(163,1);

	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("header");
	$template_session->loadtpl("points", $data);
	$template_session->loadend();