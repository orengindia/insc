<?php

	/* Leaderboard page:	site/people */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";

	$data['points'] = $engine_session->getuserdata(USER_ID, "POINTS");
	$data['title'] = get_text(155,1);
	$data['js']	= "toppeople();";

	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("header");
	$template_session->loadtpl("people", $data);
  	$template_session->loadend();