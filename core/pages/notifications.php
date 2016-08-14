<?php
	
	/* Notifications page:		site/notifications */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";
	checkuserloggedoff();

	if($_GET['type']>0 && $_GET['type']<6) {
		$load=$_GET['type'];
	}
	else {
		$load=10;
	}

	switch($load) {
		case 1:		// Upvotes
					define("TYPE", get_text(168,1));
					break;
		case 2:		// Answers
					define("TYPE", get_text(169,1));
					break;
		case 3:		// Follow question
					define("TYPE", get_text(170,1));
					break;
		case 4:		// Mention
					define("TYPE", get_text(171,1));
					break;
		case 5:		// System
					define("TYPE", get_text(172,1));
					break;
		case 10:	// All notifications
					define("TYPE", get_text(173,1));
					break;
	}

	$data['line'] = 2;
	$data['n_t'] = $load;
	$data['title'] = TYPE." ".get_text(102,1);
	$data['js'] = "get_notifications(".$load.");";

	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("header", $data);
	$template_session->loadtpl("notifications", $data);
	$template_session->loadend();