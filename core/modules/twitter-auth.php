<?php
require_once dirname(dirname(dirname(__FILE__)))."/core.php";

define('CONSUMER_KEY', TW_CONSUMER_KEY);
define('CONSUMER_SECRET', TW_CONSUMER_SECRET);
define('REQUEST_TOKEN_URL', TW_REQUEST_TOKEN_URL);
define('AUTHORIZE_URL', TW_AUTHORIZE_URL);
define('ACCESS_TOKEN_URL', TW_ACCESS_TOKEN_URL);
define('ACCOUNT_DATA_URL', TW_ACCOUNT_DATA_URL);
define('CALLBACK_URL', TW_CALLBACK_URL);

define('URL_SEPARATOR', '&');
$oauth_nonce = md5(uniqid(rand(), true));
$oauth_timestamp = time();

if (!empty($_GET['oauth_token']) && !empty($_GET['oauth_verifier'])) {
    $oauth_nonce = md5(uniqid(rand(), true));
    $oauth_timestamp = time();
    $oauth_token = $_GET['oauth_token'];
    $oauth_verifier = $_GET['oauth_verifier'];
    $oauth_base_text = "GET&";
    $oauth_base_text .= urlencode(ACCESS_TOKEN_URL)."&";

    $params = array(
        'oauth_consumer_key=' . CONSUMER_KEY . URL_SEPARATOR,
        'oauth_nonce=' . $oauth_nonce . URL_SEPARATOR,
        'oauth_signature_method=HMAC-SHA1' . URL_SEPARATOR,
        'oauth_token=' . $oauth_token . URL_SEPARATOR,
        'oauth_timestamp=' . $oauth_timestamp . URL_SEPARATOR,
        'oauth_verifier=' . $oauth_verifier . URL_SEPARATOR,
        'oauth_version=1.0'
    );

    $key = CONSUMER_SECRET . URL_SEPARATOR . $oauth_token_secret;
    $oauth_base_text = 'GET' . URL_SEPARATOR . urlencode(ACCESS_TOKEN_URL) . URL_SEPARATOR . implode('', array_map('urlencode', $params));
    $oauth_signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

    $params = array(
        'oauth_nonce=' . $oauth_nonce,
        'oauth_signature_method=HMAC-SHA1',
        'oauth_timestamp=' . $oauth_timestamp,
        'oauth_consumer_key=' . CONSUMER_KEY,
        'oauth_token=' . urlencode($oauth_token),
        'oauth_verifier=' . urlencode($oauth_verifier),
        'oauth_signature=' . urlencode($oauth_signature),
        'oauth_version=1.0'
    );
    $url = ACCESS_TOKEN_URL . '?' . implode('&', $params);

    $response = file_get_contents($url);
    parse_str($response, $response);

    $oauth_nonce = md5(uniqid(rand(), true));
    $oauth_timestamp = time();

    $oauth_token = $response['oauth_token'];
    $oauth_token_secret = $response['oauth_token_secret'];
    $screen_name = $response['screen_name'];

    $params = array(
        'oauth_consumer_key=' . CONSUMER_KEY . URL_SEPARATOR,
        'oauth_nonce=' . $oauth_nonce . URL_SEPARATOR,
        'oauth_signature_method=HMAC-SHA1' . URL_SEPARATOR,
        'oauth_timestamp=' . $oauth_timestamp . URL_SEPARATOR,
        'oauth_token=' . $oauth_token . URL_SEPARATOR,
        'oauth_version=1.0' . URL_SEPARATOR,
        'screen_name=' . $screen_name
    );
    $oauth_base_text = 'GET' . URL_SEPARATOR . urlencode(ACCOUNT_DATA_URL) . URL_SEPARATOR . implode('', array_map('urlencode', $params));

    $key = CONSUMER_SECRET . '&' . $oauth_token_secret;
    $signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

    $params = array(
        'oauth_consumer_key=' . CONSUMER_KEY,
        'oauth_nonce=' . $oauth_nonce,
        'oauth_signature=' . urlencode($signature),
        'oauth_signature_method=HMAC-SHA1',
        'oauth_timestamp=' . $oauth_timestamp,
        'oauth_token=' . urlencode($oauth_token),
        'oauth_version=1.0',
        'screen_name=' . $screen_name
    );

    $url = ACCOUNT_DATA_URL . '?' . implode(URL_SEPARATOR, $params);

    $response = file_get_contents($url);
    $user_data = json_decode($response, true);

    session_start();
	unset($_SESSION['regnetwork']);
	unset($_SESSION['regid']);
	unset($_SESSION['regfirst_name']);
	unset($_SESSION['reglast_name']);
	unset($_SESSION['regphoto_big']);
	unset($_SESSION['email']);
	$_SESSION['regnetwork'] = 'twitter';
	$_SESSION['regid'] = 'https://twitter.com/'.$user_data['id'];
	$_SESSION['email'] = '';
	$_SESSION['regfirst_name'] = $user_data['name'];
	$_SESSION['reglast_name'] = $user_data['name'];
	$_SESSION['name'] = $user_data['name'];
	$_SESSION['regphoto_big'] = $user_data['profile_image_url'];
	$engine_session->checkcookies("", "", 'twitter', 'https://twitter.com/'.$user_data['id']);
}
?>