<?php

	/* User profile page - with all answers:	 %user_profile%/answers */
	
	require_once dirname(dirname(dirname(__FILE__)))."/core.php";
	$header['line'] = 3;

	$data['profile_user'] = $_GET["user_profile"];
	$data['profile_id'] = $engine_session->get_user_id($data['profile_user']);
	$user_data = $engine_session->getuserdata($data['profile_id'], "*");
	$data['profile_photo'] = $user_data['photo']; 
	$data['profile_country'] = $engine_session->get_location_name($user_data['country']);
	$data['profile_name'] = $user_data['name'];
	$data['profile_web'] = $user_ata['website'];
	$data['profile_bio'] = $user_data['bio'];
	$data['profile_visit'] = $user_data['visit'];
	$data['verified'] = $user_data['verified'];

	$data['points'] = $engine_session->getuserdata($data['profile_id'], "POINTS");
	$data['questions'] = countnumber($engine_session->number_user_questions($data['profile_id']));
	$data['answers'] = countnumber($engine_session->number_user_answers($data['profile_id']));
	$data['follows'] = countnumber($engine_session->number_user_topics($data['profile_id']));

	if(!file_exists('../../media/images/users/' . $data['profile_photo'])) { 
		$data['profile_photo'] = 'photo_default.png';
	}

	if(file_exists('../../media/images/users/b_' . $data['profile_photo'])) { 
		$data['photo_link'] = 'b_' . $data['profile_photo'];
	}
	else {
		$data['photo_link'] = $data['profile_photo'];
	}

	if($engine_session->profileuserexists($data['profile_id'])!=0) {
		$title['title'] = $data['profile_user'];
		$title['js'] = "users_answers(".$data['profile_id'].");";
		$template_session->loadtpl("head", $title); 
		$template_session->loadtpl("header", $header); 
		$template_session->loadtpl("answers", $data);
		$template_session->loadend();
	}
	else {
		$engine_session->headerin('site/error');
	}