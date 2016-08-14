<?php

	/* Notification settings:	site/notification */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";
	checkuserloggedoff();

	$user = $engine_session->getuserdata(USER_ID, "*");
	$data['not_upvote'] = $user['not_upvote'];
	$data['not_answer'] = $user['not_answer'];
	$data['not_follower'] = $user['not_follower'];
	$data['not_mention'] = $user['not_mention'];
	$data['not_system'] = $user['not_system'];

	if($_SERVER['REQUEST_METHOD']=="POST") {
		if($_POST["not_upvote"]=='on') $not_upvote=1; else $not_upvote=0;
		if($_POST["not_answer"]=='on') $not_answer=1; else $not_answer=0;
		if($_POST["not_follower"]=='on') $not_follower=1; else $not_follower=0;
		if($_POST["not_mention"]=='on') $not_mention=1; else $not_mention=0;
		if($_POST["not_system"]=='on') $not_system=1; else $not_system=0;
		$engine_session->updateusernotification($not_upvote, $not_answer, $not_follower, $not_mention, $not_system);
	}

	$data['title'] = get_text(102,1);
	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("header");
	$template_session->loadtpl("acc_notification", $data);
	$template_session->loadend();