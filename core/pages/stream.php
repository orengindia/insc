<?php

	/* 
	 * Stream page: 	site/stream/... 
	 * Types of stream:
	 * 0 - unanswered questions:	site/stream/unaswered;
	 * 1 - answered questions:		site/stream/aswered;
	 * 2 - popular questions:		site/stream/popular;
	 * 3 - followed questions:		site/stream/followed;
	 * 4 - all questions:			site/stream 	*/

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";
	
	$data['type'] = 4;

	/* Check if user follow at least one category */
	if(USER_ID!='') $follow = $engine_session->user_is_follow_category(USER_ID);

	if(USER_ID!='' && $follow!=1) {
		$data['title'] = get_text(200,1);
		$template_session->loadtpl("head", $data);
		$data['categories'] = $engine_session->get_main_categories(200);
		$template_session->loadtpl("all_categories", $data);
	}
	else {
		if($_GET['type']>=0 && $_GET['type']<4) {
			$data['type'] = $_GET['type'];
		}
		
		$data['title'] = get_text(8,1);
		$data['js'] = "stream(".$data['type'].");";
		$data['line'] = 1;

		$template_session->loadtpl("head", $data);
		$template_session->loadtpl("header", $data);
		$template_session->loadtpl("stream", $data);
	}
	$template_session->loadend();