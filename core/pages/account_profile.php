<?php
	
	/* Account image setting page:	site/profile */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";
	checkuserloggedoff();

	$user = $engine_session->getuserdata(USER_ID, "*");
	$data['photo_url'] = $user['photo'];

	if($_SERVER['REQUEST_METHOD']=="POST") {
		if($_POST['img']=='photo') {
			$photo_file = $_FILES["photo"];
			$engine_session->updateuserphoto($photo_file); 
		}
		else if($_POST['img']=='deletephoto') {
			$engine_session->deleteuserphoto();
		}
	}

	$data['title'] = get_text(287,1);
	$template_session->loadtpl("head", $data);
	$template_session->loadtpl("header");
	$template_session->loadtpl("acc_profile", $data);
	$template_session->loadend();