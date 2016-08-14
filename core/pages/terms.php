<?php
	
	/* Terms page: 		site/terms */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";
	
	/* Getting page content. 4 - terms page */
	$page = $engine_session->get_page(4);
	$data['content'] = $page['content'];
	$data['time'] = get_time($page['time']);
	$data['title'] = get_text(61,1);

	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("header");
	$template_session->loadtpl("terms", $data);
	$template_session->loadend();