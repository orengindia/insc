<?php
require_once dirname(dirname(dirname(__FILE__)))."/core.php";
if(isset($_GET['code'])) {
	$client_id = SITE_FB_ID;
	$client_secret = SITE_FB_SECRET;
	$redirect_uri = 'http://'.SITE_DOMAIN.'/core/modules/auth.php';
	$url = 'https://www.facebook.com/dialog/oauth';
	$params = array(
	    'client_id'     => $client_id,
	    'redirect_uri'  => $redirect_uri,
	    'response_type' => 'code',
	    'scope'         => 'email,user_birthday'
	);
	$result = false;
	$params = array(
	    'client_id'     => $client_id,
	    'redirect_uri'  => $redirect_uri,
	    'client_secret' => $client_secret,
	    'code'          => $_GET['code']
	);
	$url = 'https://graph.facebook.com/oauth/access_token';
	$tokenInfo = null;
	parse_str(file_get_contents($url . '?' . http_build_query($params)), $tokenInfo);
	if(count($tokenInfo) > 0 && isset($tokenInfo['access_token'])) {
	    $params = array('access_token' => $tokenInfo['access_token']);
		$userInfo = json_decode(file_get_contents('https://graph.facebook.com/me' . '?' . urldecode(http_build_query($params))), true);
		if(isset($userInfo['id'])) {
	        $userInfo = $userInfo;
	        $result = true;
	    }
	}
	if($result) {
		session_start();
		unset($_SESSION['regnetwork']);
		unset($_SESSION['regid']);
		unset($_SESSION['regfirst_name']);
		unset($_SESSION['reglast_name']);
		unset($_SESSION['regphoto_big']);
		unset($_SESSION['email']);
		$name = explode(' ',trim($userInfo['name']));
		$_SESSION['regnetwork'] = 'facebook';
		$_SESSION['regid'] = $userInfo['id'];
		$_SESSION['email'] = $userInfo['email'];
		$_SESSION['regfirst_name'] = $name[0];
		$_SESSION['reglast_name'] = $name[1];
		$_SESSION['name'] = $name[0].' '.$name[1];
		$_SESSION['regphoto_big'] = 'http://graph.facebook.com/' . $userInfo['id'] . '/picture?type=large';
		$engine_session->checkcookies("", "", 'facebook', $userInfo['id']);
	}
}
?>