<?php
	
	/*
	 * Core file of application.
	 * Used for loading all classes and functions.
	 * Include main functions, like: getting words from text.php file, showing date, time, etc... */
	
	session_start();
	error_reporting(1);
	ini_set('display_errors', 1);

	/* Load config file and get settings */
	$config = include 'core/config/config.php';
	require_once 'core/config/mysql.config.php';
	require_once 'core/engine.php';
	require_once 'core/template.php';

	$engine_session = new engine();
	$template_session = new template();
	if(ADMIN==1) {
		require_once 'administration/admin.php';
		$admin_session = new admin();
	}

	/* Setting global variables */
	date_default_timezone_set($config['server_time_zone']);
	define("USER_ID", $_SESSION['id']);
	define("USER_USERNAME", $_SESSION['account_username']);
	$photo = $engine_session->getuserdata(USER_ID, "PHOTO");
	if($photo=='') $photo='photo_default.png';
	define("USER_PHOTO", $photo);
	define("USER_RANK", $_SESSION['rank']);
	define("USER_IP", get_client_ip());
	define("SESSION_STATUS", $_SESSION['login']);
	define("SESSION_MESSAGE", $_SESSION['msg']);
	define("SCRIPT_VERSION", "1.1.1");
	define("AUTHOR", "http://xandr.co/");

	define("PHOTO_MAX_SIZE", $config['photo_max_size']);
	define("PHOTO_MAX_WIDTH", $config['photo_max_width']);
	define("PHOTO_MAX_HEIGHT", $config['photo_max_height']);
	define("SITE_NAME", $config['site_name']);
	define("SITE_DOMAIN", $config['site_domain']);
	define("SITE_DESCRIPTION", $config['site_description']);
	define("SITE_KEYWORDS", $config['site_keywords']);
	define("SITE_FB_ID", $config['fb_id']);
	define("SITE_FB_SECRET", $config['fb_secret']);
	define('TW_CONSUMER_KEY', $config['tw_key']);
	define('TW_CONSUMER_SECRET', $config['tw_secret']);
	define('TW_REQUEST_TOKEN_URL', 'https://api.twitter.com/oauth/request_token');
	define('TW_AUTHORIZE_URL', 'https://api.twitter.com/oauth/authorize');
	define('TW_ACCESS_TOKEN_URL', 'https://api.twitter.com/oauth/access_token');
	define('TW_ACCOUNT_DATA_URL', 'https://api.twitter.com/1.1/users/show.json');
	define('TW_CALLBACK_URL', 'http://'.SITE_DOMAIN.'/core/modules/twitter-auth.php');
	define("SITE_ADSENSE", $config['adsense']);
	define("SITE_EMAIL", $config['site_email']);
	define("SMTP_HOST", $config['smtp_host']);
	define("SMTP_PORT", $config['smtp_port']);
	define("SMTP_USER", $config['smtp_user']);
	define("SMTP_PASS", $config['smtp_pass']);
	define("SAVE_VISIT", $config['save_visit']);
	define("CONVERT_NUMBERS", $config['convert_numbers']);
	define("ENABLE_SMILES", $config['enable_smiles']);
	define("ENABLE_MENTIONS", $config['enable_mentions']);
	define("SITE_FB", $config['auth_fb']);
	define("SITE_TW", $config['auth_tw']);
	define("SIGNUP_CONFIRMATION", $config['signup_confirmation']);
	define("HTTPS", $config['https']);
	define("FILTER_WORD", $config['filter_word']);

	/* Load words & ip filter */
	$filter = include 'core/filter.php';
	define("FILTER_WORDS", $filter['words']);
	define("FILTER_IPS", $filter['ips']);

	if(ERROR_PAGE!=1) {
		/* If this is not error page - check if ip is not blocked and redirect to error page */
		$bad_ips = explode(', ', FILTER_IPS);
		if(in_array(USER_IP, $bad_ips) && $bad_ips!=' ') {
			header('Location: http://'.SITE_DOMAIN.'/site/error');
		}
	}

	if(USER_ID!="") {
		/* Save user last activity time if option is enabled */
		if(SAVE_VISIT==1) {
			$engine_session->savevisit();
		}
	}

	function get_text($id, $return=0) {
		/* Echo or return words from texts file in core folder */
		include (dirname(__FILE__).'/core/texts.php');
		$res = is_numeric($id) ? true : false;
		if($res && $return==0) {
			echo $arr[$id];
		}
		elseif($res && $return!=0) {
			return $arr[$id];
		}
	}

	function countnumber($num) {	
		/* Convert number in words if option is enabled. E.g.: 130.000 -> 130K, 1.300.000 -> 1.3M */
		if(CONVERT_NUMBERS==1) {
			$precision = 2;
	   		if ($num >= 1000 && $num < 1000000) {
	    		$n_format = number_format($num/1000,$precision).'K';
	    	} else if ($num >= 1000000 && $num < 1000000000) {
	    		$n_format = number_format($num/1000000,$precision).'M';
	   		} else if ($num >= 1000000000) {
	   			$n_format=number_format($num/1000000000,$precision).'B';
	   		} else {
	   			$n_format = $num;
	    	}
  			return $n_format;
  		}
  		else {
  			return $num;
  		}
  	}
	
	function usertime($date) {
		/* Convert time in user timezone */
		if($_SESSION['tzname']!="") {
			$tz = new DateTimeZone($_SESSION['tzname']);
			$dtStr = date("c", $date);
			$date = new DateTime($dtStr);
			$date->setTimeZone($tz);
			return $date->format('d.m.Y H:i:s');
		}
		else {
			return date('d.m.Y H:i:s',$date);
		}	
	}

  	function get_time($date) {	
		/* Convert timestamp into user timezone time with words */
		$date = usertime($date);	
		$ndate = strtotime($date);
		$time = usertime(time());
		$time = strtotime($time);
		$time = $time - $ndate; 
		$tokens = array (
			86400 => get_text(4,1),
			3600 => get_text(3,1),
			60 => get_text(2,1),
			1 => get_text(1,1)
		);
		if($time==0 || $time<4) {
			return get_text(7,1);
		}
		elseif($time>3 && $time<604800) {
			foreach($tokens as $unit => $text)  {
				if($time<$unit) continue;
				$numberOfUnits = floor($time/$unit);
				if($numberOfUnits!=1) {
					$text .= get_text(469,1);
				}
				return $numberOfUnits.' '.$text.' '.get_text(5,1);
			}
		}
		else {
			$datestamp = strtotime($date);
			$month = 10 + intval(date("m", $datestamp));
			$month = ' '.get_text($month,1).' ';
			return date("d", $datestamp).$month.date("Y", $datestamp);
		}
	}

	function format_text($text) {
		/* Function for editing text before it appear for user */
		if(ENABLE_MENTIONS==1) {
			$in = array('/\B@([A-Za-z0-9\/\.]*)/');
	    	$out = array('<a href="http://'.SITE_DOMAIN.'/$1" class=hashtag rel=nofollow target=_blank>@$1</a> ');
	    	$text = preg_replace($in, $out, $text);
		}
		if(ENABLE_SMILES==1) {
			$text = preg_replace("#]:\)#is","<img class='emoticon' src='media/images/smiles/12.gif'>", $text);
			$text = preg_replace("#:\)#is","<img class='emoticon' src='media/images/smiles/1.gif'>", $text);
			$text = preg_replace("#:\*#is","<img class='emoticon' src='media/images/smiles/2.gif'>", $text);
			$text = preg_replace("#\(:\|#is","<img class='emoticon' src='media/images/smiles/3.gif'>", $text);
			$text = preg_replace("#:\(#is","<img class='emoticon' src='media/images/smiles/4.gif'>", $text);
			$text = preg_replace("#-_#is","<img class='emoticon' src='media/images/smiles/5.gif'>", $text);
			$text = preg_replace("#:D#is","<img class='emoticon' src='media/images/smiles/6.gif'>", $text);
			$text = preg_replace("#\(o.o\)#is","<img class='emoticon' src='media/images/smiles/7.gif'>", $text);
			$text = preg_replace("#;\)#is","<img class='emoticon' src='media/images/smiles/8.gif'>", $text);
			$text = preg_replace("#:P#is","<img class='emoticon' src='media/images/smiles/9.gif'>", $text);
			$text = preg_replace("#\(:S\)#is","<img class='emoticon' src='media/images/smiles/10.gif'>", $text);
			$text = preg_replace("#\(8\)\)#is","<img class='emoticon' src='media/images/smiles/11.gif'>", $text);
			$text = preg_replace("#\(envy\)#is","<img class='emoticon' src='media/images/smiles/13.gif'>", $text);
			$text = preg_replace("#\(lol\)#is","<img class='emoticon' src='media/images/smiles/14.gif'>", $text);
			$text = preg_replace("#\(love\)#is","<img class='emoticon' src='media/images/smiles/15.gif'>", $text);
			$text = preg_replace("# :\/#is","<img class='emoticon' src='media/images/smiles/16.gif'>", $text);
		}
		return $text;
	}
	
	function txt2link($url) {
		/* Convert text into links and embed videos */
		$url = str_replace("\\r","\r",$url);
		$url = str_replace("\\n","\n<BR>",$url);
		$url = str_replace("\\n\\r","\n\r",$url);
		if(strpos($url,'youtube')==false) {
		    $in = array('`http://(\S+[[:alnum:]]/?)`si');
	    	$out = array('<a href="http://$1" class=hashtag rel=nofollow target=_blank>$1</a> ');
	    	$url = preg_replace($in, $out, $url);
	    	$in = array('`www.(\S+[[:alnum:]]/?)`si');
	    	$out = array('<a href="http://$1" class=hashtag rel=nofollow target=_blank>$1</a> ');
	    	$url = preg_replace($in, $out, $url);
		}
		$in = array('`@(\S+[[:alnum:]]/?)`si');
	    $out = array('<a href="http://'.SITE_DOMAIN.'/$1" class=hashtag rel=nofollow target=_blank>@$1</a> ');
	    $url = preg_replace($in, $out, $url);
	    $in = array('`#(\S+[[:alnum:]]/?)`si');
	    $out = array('<a href="http://'.SITE_DOMAIN.'/search?hashtag=$1" class=hashtag>#$1</a> ');
	    $url = preg_replace($in, $out, $url);
		return preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", " <br /><iframe width=\"400\" height=\"225\" src=\"http://www.youtube.com/embed/$1?wmode=transparent\" frameborder=\"0\" wmode=\"Opaque\" allowfullscreen></iframe><br />", $url);
	}

	function checkuserloggedoff() {
		/* Check if user is logged out for redirecting to sign in page */
		if(SESSION_STATUS!=true) { 
			header('Location: http://'.SITE_DOMAIN.'/site/login');
		} 
	}

	function checkuserloggedin() {	
		/* Check if user is logged in for redirecting to stream page */
		if(SESSION_STATUS!=false) {
			header('Location: http://'.SITE_DOMAIN.'/site/stream');
		} 
	}

	function get_client_ip() {
		$ipaddress = '';
	    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    } else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR']; 
		} else if(isset($_SERVER['HTTP_FORWARDED'])) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} else if(isset($_SERVER['REMOTE_ADDR'])) { 
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		} else {
			$ipaddress = 'UNKNOWN';
		}
		return $ipaddress;
	}