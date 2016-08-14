<?php

	/* default error page: 	site/error */

	define("ERROR_PAGE", 1);
	require_once dirname(dirname(dirname(__FILE__)))."/core.php";

	$data['title'] = get_text(25,1);
	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("header");
	$template_session->loadtpl("error");
	$template_session->loadend();