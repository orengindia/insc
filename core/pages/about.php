<?php

	/* About website page: 	site/about */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";

	/* Getting page content. 1 - about website page */
	$page = $engine_session->get_page(1);
	$data['content'] = $page['content'];
	$data['time'] = get_time($page['time']);
	$data['title'] = get_text(38,1);

	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("header");
	$template_session->loadtpl("about", $data);
	$template_session->loadend();