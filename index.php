<?php

	/*
	 * Qwikia 1.1.0 index page.
	 * Loading all files via core.php. */

	require_once 'core.php';

	$data['type'] = 4; 	// Default stream type is 4 - all questions and answers.

	/* Check if user follow at least one category */
	if(USER_ID!='') $follow = $engine_session->user_is_follow_category(USER_ID);

	if(USER_ID!='' && $follow!=1) {
		$data['title'] = get_text(200,1);
		$template_session->loadtpl("head", $data);
		$data['categories'] = $engine_session->get_main_categories(200);
		$template_session->loadtpl("all_categories", $data);
	}
	else {
		$data['title'] = get_text(8,1);
    	$data['js'] = "stream(".$data['type'].");";

	    $template_session->loadtpl("head", $data);
		$template_session->loadtpl("header");
		$template_session->loadtpl('stream', $data);
	}
	$template_session->loadend();