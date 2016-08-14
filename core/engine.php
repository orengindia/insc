<?php

	/*
	 * Engine class with all functions */

	include "modules/phpmailer/class.smtp.php";
	require_once 'modules/phpmailer/class.phpmailer.php';

	class engine {

		protected $conn;

		function __construct() {

			/* 
			 * Class constructor;
			 * Add $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT); PDO::ERRMODE_EXCEPTION for testing db errors */

			if(DB_SERVER!="" && DB_USERNAME!="" && DB_PASSWORD!="" && DB_NAME!="") {
				try {
				    $this->conn = new PDO('mysql:host='.DB_SERVER.'; dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET names utf8"));
				    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				} 
				catch(PDOException $e) {
				    echo 'ERROR: '.$e->getMessage();
				}
			}
			else {
				$url = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
				die("<a href='".$url."site/install' style='display:block;position:absolute;top:50%;margin-top:-25px;left:50%;margin-left:-150px;width:300px;height:50px;text-align:center;text-decoration:none;background-color:#273e6a;color:#fff;font-size:18px;padding:10px 0'>QwikiA<br>Start installation</a>");
			}
		}

		public function send_email($to, $subject, $message) {

			/* 
			 * Sending email via SMTP. If SMTP is not set - will be used mail() function.
			 * $to - email address, where should be sent email message
			 * $subject - email subject
			 * $message - email message */

			if(SMTP_USER!='' && SMTP_PASS!='') {
				$mail = new PHPMailer(); 							// create a new object
				$mail->IsSMTP(); 									// enable SMTP
				$mail->SMTPDebug = 0; 								// debugging: 1 = errors and messages, 2 = messages only
				$mail->SMTPAuth = true; 							// authentication enabled
				$mail->SMTPSecure = 'ssl'; 							// secure transfer enabled REQUIRED for GMail
				$mail->Host = SMTP_HOST;							// Email host server "smtp.gmail.com"
				$mail->Port = SMTP_PORT;							// 465 or 587
				$mail->IsHTML(true);								// Html code
				$mail->Username = SMTP_USER;						// Email username
				$mail->Password = SMTP_PASS;						// Email password
				$mail->SetFrom(SMTP_USER, SITE_NAME);				// Email from text
		    	$mail->AddReplyTo(SMTP_USER, "No-Reply");			// Reply email
				$mail->Subject = $subject;
				$mail->Body = $message;
				$mail->AddAddress($to);
				$mail->Send();
			}
			else {
				$headers = 'From: '.SITE_NAME.' no-reply@'.SITE_DOMAIN.'' . "\r\n" ;
			    $headers .='Reply-To: '. $to . "\r\n" ;
			    $headers .='X-Mailer: PHP/' . phpversion();
			    $headers .= "MIME-Version: 1.0\r\n";
			    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
				mail($to, $subject, $message, $headers);
			}
		}
		
		public function resizeimg($filename, $smallImage, $w, $h) {

			/*
			 * Function for resizing images and make thumbnails.
			 * $filename - file which shuld be edited. Path to it.
			 * $smallImage - file which should be created. Path to it.
			 * $w - width for new image.
			 * $h - height for new image. */

			$ratio = $w/$h; 
			$size_img = getimagesize($filename);
			$src_ratio=$size_img[0]/$size_img[1]; 
			if($ratio<$src_ratio) $h = $w/$src_ratio; 
			else $w = $h*$src_ratio; 
			$dest_img = imagecreatetruecolor($w, $h);   
			$white = imagecolorallocate($dest_img, 255, 255, 255);  
			switch($size_img[2]) {
				case 1: {
					$src_img = imagecreatefromgif($filename);
					imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $w, $h, $size_img[0], $size_img[1]);                 
					imagegif($dest_img, $smallImage);
				}
				case 2: {
					$src_img = imagecreatefromjpeg($filename);                       
					imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $w, $h, $size_img[0], $size_img[1]);                 
					imagejpeg($dest_img, $smallImage);
				}
				case 3: {
					$src_img = imagecreatefrompng($filename);
					imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $w, $h, $size_img[0], $size_img[1]);                 
					imagepng($dest_img, $smallImage);
				}
			}    
			imagedestroy($dest_img); 
			imagedestroy($src_img); 
			return true;          
		}   

		public function get_user_id($username) {

			/* Return user id by username */

			if($username!="") {
				$stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
				$stmt->execute(array($username));
				$user = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $user[0]['id']; 	
			}	
		}
		
		public function get_user_name($id) {

			/* Return username by user id */

			if($id!="") {
				$stmt = $this->conn->prepare("SELECT username FROM users WHERE id = ?");
				$stmt->execute(array($id));
				$user = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $user[0]['username']; 	
			}	
		}
		
		public function onlineusers() {

			/* Return number of online users */

			$maxtime = time()-60;
			$stmt = $this->conn->query('SELECT id FROM users WHERE visit>"'.$maxtime.'"');
			return $stmt->rowCount();
		}
		
		public function total_questions($user) {

			/* Return number of total users */

			$stmt = $this->conn->prepare("SELECT id FROM questions WHERE user_id = ? AND status='1'");
			$stmt->execute(array($user));
			return $stmt->rowCount();
		}

		public function topics_information() {

			/* Information for topics page */

			$msg = '';
			$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM categories");
			$stmt->execute();
			$total = $stmt->fetch(PDO::FETCH_ASSOC);
			$follows = engine::number_user_topics(USER_ID);
			$reg_date = get_time(engine::getuserdata(USER_ID, 'REG_DATE'));
			$msg .= '<p class="left-form-info">'.get_text(209,1).' <b>'.$total['total'].'</b> '.get_text(210,1).' <b>'.$follows.'</b> '.get_text(211,1).'</p>';
			$stmt = $this->conn->prepare("SELECT time, category_id FROM follows WHERE user_id=? ORDER BY time DESC LIMIT 1");
			$stmt->execute(array(USER_ID));
			$last_time = $stmt->fetch(PDO::FETCH_ASSOC);
			$msg .= '<p class="left-form-info">'.get_text(213,1).' <b>'.$reg_date.'</b>. '.get_text(214,1).' <b>'.get_time($last_time['time']).'</b>.</p>';
			$stmt = $this->conn->prepare("SELECT name, url FROM categories WHERE id=?");
			$stmt->execute(array($last_time['category_id']));
			$category = $stmt->fetch(PDO::FETCH_ASSOC);
			$msg .= '<p class="left-form-info">'.get_text(215,1).': <b><a href="category/'.$category['url'].'" style="display:inline">'.$category['name'].'</a></b></p>';
			return $msg; 

		}

		public function getinfo($table_val, $mode) {

			/* Return config informations */

			$stmt = $this->conn->query('SELECT * FROM site_config LIMIT 1');
			$config = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($mode == 0 && $table_val!="*") return $config[0][''.$table_val.'']; 
			else if($mode == 0 && $table_val=="*") return $config[0];
			else if($mode == 1) echo $config[0][''.$table_val.'']; 
		}
		
		public function crypts($encrypt_val, $mode, $brute_pass) {

			/* Function for crypting */

			if($brute_pass!=0 && strlen($encrypt_val)>0 || strlen($encrypt_val)<800) {
				switch($mode) {
					case 1:		
					case "MD5":	{
						return md5($encrypt_val);
						break;
					}
					case 2:
					case "BASE_64":	{
						return base64_encode($encrypt_val);
						break;
					}
					case 3:
					case "SHA1": {
						return sha1($encrypt_val);
						break;
					}
					default: {
						return 0;
						break;
					}
				}
			}
			else if($brute_pass!=1) {
				return sha1(base64_encode(md5($encrypt_val)));
			}
		}
		
		public function captchaerror() {

			/* Captcha error function */

	        $_SESSION['msg'] = 3;
	        engine::headerin("site/signup");
	        exit;
	    }

		public function checkcookies($username, $password, $social = 'none', $id = '') {

			/* Function for users authentification */

			if($social!="none") {
				$stmt = $this->conn->prepare("SELECT id, username, password, rank, status FROM users WHERE social = ?");
				$stmt->execute(array($id));
				$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
				if($user_data['id']) {	
					if($user_data['status']==0) {
						$_SESSION['error'] = 1;
						$_SESSION['login'] = false;
						engine::headerin("site/login");
						exit;
					}
					else {
						$_SESSION['login'] = true;
						$_SESSION['id'] = $user_data['id'];
						$_SESSION['rank'] = $user_data['rank'];
						$_SESSION['social'] = $user_data['social'];
						$_SESSION['account_username'] = $user_data['username']; 
						engine::headerin();
						exit;
					}
				}
				else {
					if($social!="") {
						engine::headerin("site/signup");
						exit;
					}
					else {
						$_SESSION['error'] = 2;
						$_SESSION['login'] = false;
						engine::headerin("site/login");
						exit;
					}
				}
			}
			else {
				if(!preg_match('/^[a-za-zA-Z\d_]{1,32}$/i', $username)) {
					$_SESSION['error'] = 3;
					$_SESSION['login'] = false;
					engine::headerin("site/login");
					exit;	
				}
			}
			$pass = engine::crypts(engine::crypts(engine::crypts($password, "MD5", 0), 2, 0), "SHA1", 0);
			$stmt = $this->conn->prepare("SELECT id, username, password, rank, status FROM users WHERE username = ? AND password = ?");
			$stmt->execute(array($username,$pass));
			$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
			if($user_data['id']) {	
				if($user_data['status']==0) {
					$_SESSION['error'] = 1;
					$_SESSION['login'] = false;
					engine::headerin("site/login");
					exit;
				}
				else if($user_data['status']==10) {
					$_SESSION['error'] = 11;
					$_SESSION['login'] = false;
					engine::headerin("site/login");
					exit;	
				}
				else {
					$_SESSION['login'] = true;
					$_SESSION['id'] = $user_data['id'];
					$_SESSION['account_username'] = $user_data['username']; 
					$_SESSION['rank'] = $user_data['rank']; 
					engine::headerin();
					exit;
				}
			}
			else {
				$_SESSION['error'] = 2;
				$_SESSION['login'] = false;
				engine::headerin("site/login");
				exit;
			}
		}

		public function user_is_follow_category($user_id) {

			/* Check if user follow at least one topic */

			$stmt = $this->conn->prepare("SELECT id FROM follows WHERE user_id = ? LIMIT 1");
			$stmt->execute(array($user_id));
			if($stmt->rowCount()!=0) {
				return 1;
			}
			else {
				return 0;
			}
		}

		public function set_categories($categories) {

			/* Follow topic function */

			if(USER_ID) {
				$num = count($categories);
				$category = explode(",", $categories);
				$num = count($category);
				for($i=1;$i<=$num;$i++) {
					if($category[$i]) {
						$stmt = $this->conn->prepare("INSERT INTO follows (category_id, user_id, time) VALUES (?, ?, ?)");
						$stmt->execute(array($category[$i], USER_ID, time()));
						$stmt = $this->conn->prepare("UPDATE categories SET followers=followers+1 WHERE id=?");
						$stmt->execute(array($category[$i]));
					}
				}
			}
			engine::headerin("site/stream");
			exit;
		}
		
		public function reactive($username) {

			/* Function for recovery disabled user account */

			$stmt = $this->conn->prepare("UPDATE users SET status = '1' WHERE username = ?");
			$stmt->execute(array($username));
			if($stmt->rowCount()!=0) {	
				$_SESSION['error'] = 10;
				engine::headerin("site/login");
				exit;
			}
			else {
				$_SESSION['error'] = 1;
				engine::headerin("site/login");
				exit;
			} 
		}
		
		public function randomvar($length, $numbers, $upper) {

			/* Function for generating random variables */

			if(1>$length) {
				$length = 8;
			}
			$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$numChars = 62;
			if(!$numbers) {
				$numChars = 52;
				$chars = substr($chars, 10, $numChars);
			}	
			if(!$upper) {
				$numChars -= 26;
				$chars = substr($chars, 0, $numChars);
			}
			$string = '';
			for ($i = 0; $i < $length; $i++) {
				$string .= $chars[mt_rand(0, $numChars - 1)];
			}			
			return $string;		
		}
		
		public function recoverpassword($email, $pass_length = 15) {

			/* Function for changing password and sending to email new generated */

			$new_pass = engine::randomvar($pass_length, true, false);
			$password = engine::crypts(engine::crypts(engine::crypts($new_pass, "MD5", 0), 2, 0), "SHA1", 0);
			$message = '';
			if(isset($email) && SESSION_STATUS!=true) {
				if(strlen($email)>4) {
					$stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
					$stmt->execute(array($email));
					if($stmt->rowCount()!=0) {
						$stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE email = ?");
						$stmt->execute(array($password,$email));
						if($stmt->rowCount()!=0) {
							$subject = get_text(74,1).' '.SITE_NAME;
							$body = get_text(74,1).' '.SITE_NAME.' - '.get_text(74,1).' <b>'.$new_pass.'</b><br>'.get_text(77,1).' '.SITE_NAME.'<br><br>'.get_text(78,1).',<br>'.SITE_NAME.' - '.SITE_DESCRIPTION.'<br><br><small>'.get_text(79,1).'</small>';
							engine::send_email($email,$subject,$body);
							$message .= '<p class="popup_success">'.get_text(89,1).'</p>';
						}
					}
					else {
						$message .= '<p class="error"><b>'.get_text(25,1).': </b>'.get_text(80,1).'</p>';
					}
				}
				else {
					$message .= '<p class="error"><b>'.get_text(25,1).': </b>'.get_text(81,1).'</p>';
				}
			}
			return $message;
		}

		public function send_confirmation($username) {

			/* Function for sending confirmation emails */

			if(isset($username) && SESSION_STATUS!=true) {
				$stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
				$stmt->execute(array($username));
				if($stmt->rowCount()!=0) {
					$i = $stmt->fetch(PDO::FETCH_ASSOC);
					$email = $i['email'];
					$subject = get_text(82,1).' '.SITE_NAME;
					$code = engine::randomvar(5, true, false).$i['reg_date'].engine::randomvar(5, true, false).$i['id'];
					$body = get_text(83,1).' '.$username.'. '.get_text(84,1).' '.SITE_NAME.' - '.get_text(85,1).', <a href="http://'.SITE_DOMAIN.'/site/confirm/'.$code.'">'.get_text(86,1).'</a> '.get_text(87,1).'.<br>'.get_text(88,1).'.<br>'.get_text(77,1).' '.SITE_NAME.'<br><br>'.get_text(78,1).',<br>'.SITE_NAME.' - '.SITE_DESCRIPTION.'<br><br><small>'.get_text(79,1).'.</small>';
					engine::send_email($email,$subject,$body);
				}
			}
			$_SESSION['error'] = 9;
			engine::headerin('site/login');
			exit;
		}

		public function account_confirm($code) {

			/* Function for confirming account via email */

			if(isset($code)) {
				$reg_date = substr($code, 5, 10);
				$reg_id = substr($code, 20);
				$stmt = $this->conn->prepare("SELECT * FROM users WHERE reg_date = ? AND id = ?");
				$stmt->execute(array($reg_date, $reg_id));
				if($stmt->rowCount()!=0) {
					$stmt = $this->conn->prepare("UPDATE users SET status = ?");
					$stmt->execute(array('1'));
					$_SESSION['error'] = 10;
					engine::headerin('site/login');
					exit; 
				}
			}
		}

		public function updateusersettings($name, $password, $passwords, $email, $website, $bio, $country) {

			/* Function for updating user settings */	

			$name = strip_tags($name);
			$password = strip_tags($password);
			$passwords = strip_tags($passwords);
			$email = strip_tags($email);
			$website = strip_tags($website);
			$bio = strip_tags($bio);
			$country = strip_tags($country);
			$bio = substr($bio,0,55);
			if(SESSION_STATUS!=false) {	
				if($email!=engine::getuserdata(USER_ID, "EMAIL")) {
					if(engine::emailalreadyexists($email)==get_text(67,1)) {
						$_SESSION['msg'] = 3;
						engine::headerin('site/settings');
						exit;
					}
				}
				if($name=="" || $email=="") {
					$_SESSION['msg'] = 2;
					engine::headerin('site/settings');
					exit;
				}
				if($passwords!="" && strlen($passwords)>4) { 
					$pass = engine::crypts(engine::crypts(engine::crypts($password, "MD5", 0), 2, 0), "SHA1", 0);
					$stmt = $this->conn->prepare("SELECT id FROM users WHERE id = ? AND ( password = ? OR password = '' )");
					$stmt->execute(array(USER_ID,$pass));
					if($stmt->rowCount()!=0) {
						$modify_pass = 1; 
					}
					else {
						$_SESSION['msg'] = 405;
						engine::headerin('site/settings');
						exit;
					}	
				} 
				if($passwords!="" && strlen($passwords)<5) {
					$_SESSION['msg'] = 403;
					engine::headerin('site/settings');
					exit;
				} 
				elseif($passwords=="") { 
					$modify_pass = 0; 
				}
				$pass = engine::crypts(engine::crypts(engine::crypts($passwords, "MD5", 0), 2, 0), "SHA1", 0);
				if($modify_pass!=0) {	
					$stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, website = ?, bio = ?, country = ? WHERE id = ?");
					$stmt->execute(array($name,$email,$pass,$website,$bio,$country,USER_ID));
				}
				else {
					$stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, website = ?, bio = ?, country = ? WHERE id = ?");
					$stmt->execute(array($name,$email,$website,$bio,$country,USER_ID));
				}
				if($stmt->rowCount()!=0) {
					$_SESSION['msg'] = 1;
					engine::headerin('site/settings');
					exit;
				} 
				else { 
					engine::headerin('site/settings');
					exit; 
				}
			}
		}

		public function getuserdata($username, $data) {

			/* Function for getting information about user */

			$username = intval($username);
			if($username!=0) {
				$stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
				$stmt->execute(array($username));
				$readdata = $stmt->fetchAll(PDO::FETCH_ASSOC);
				switch($data) {
					case "USERNAME":	 	return $readdata[0]['username']; 
											break;
					case "PASSWORD": 		return $readdata[0]['password']; 
											break;
					case "PASS": 			if($readdata[0]['password']=="") return 0;
											else return 1;
											break;
					case "NAME": 			return $readdata[0]['name']; 
											break;
					case "RANK": 			return $readdata[0]['rank']; 
											break;
					case "EMAIL": 			return $readdata[0]['email']; 
											break;
					case "WEBSITE": 		return $readdata[0]['website']; 
											break;
					case "BIO": 			return $readdata[0]['bio']; 
											break; 
					case "PHOTO": 			return $readdata[0]['photo']; 
											break; 
					case "COUNTRY": 		return $readdata[0]['country']; 
											break;						
					case "STATUS": 			return $readdata[0]['status']; 
											break; 
					case "IP": 				return $readdata[0]['ip']; 
											break; 
					case "REG_DATE": 		return $readdata[0]['reg_date']; 
											break; 
					case "VISIT": 			return $readdata[0]['visit']; 
											break; 
					case "VERIFIED": 		return $readdata[0]['verified']; 
											break; 
					case "SOCIAL": 			return $readdata[0]['social']; 
											break;
					case "POINTS": 			$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM points_log WHERE to_user=?");
											$stmt->execute(array($username));
											$total = $stmt->fetchAll(PDO::FETCH_ASSOC);
											return $total[0]['total']; 
											break;
					case "NOT_UPVOTE": 		return $readdata[0]['not_upvote']; 
											break;
					case "NOT_ANSWER": 		return $readdata[0]['not_answer']; 
											break;
					case "NOT_FOLLOWER": 	return $readdata[0]['not_follower']; 
											break;
					case "NOT_MENTION": 	return $readdata[0]['not_mention']; 
											break;
					case "NOT_SYSTEM": 		return $readdata[0]['not_system']; 
											break;
					case "ONLINE":			$maxtime = time()-30;
											$stmt = $this->conn->prepare("SELECT visit FROM users WHERE visit>? AND id = ?");
											$stmt->execute(array($maxtime,$username));
											$number = $stmt->rowCount();
											if($number>0) return 1;
											else return 0;
											break;
					case "*":				return $readdata[0];
											break;
				}
			}
		}
		
		public function clearcookies() {

			/* Log out function */

			unset($_SESSION['id']);
			unset($_SESSION['account_username']);
			unset($_SESSION['__rights']);
			unset($_SESSION['login']);
			session_destroy();
			session_start();
			$_SESSION["log_verify"] = true;
			engine::headerin();
	        exit;
		}

		public function profileuserexists($user) {

			/* Check if profile exists */

			$stmt = $this->conn->prepare("SELECT id FROM users WHERE id = ?");
			$stmt->bindValue(1, $user, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount()>0) return 1;
			else return 0;
		}

		public function updateuserphoto($photo_file) {

			/* Function for updating user photo */

			$username = USER_ID;
			$photo = $photo_file = $_FILES["photo"];
			$error = array();
			$file = isset($photo) ? $photo : FALSE;
			if($file) {
				if(!preg_match("/^image\/(pjpeg|jpeg|jpg|png|gif|bmp)$/i", $file["type"])) {
					$error[] = 1;
					$_SESSION['msg'] = 1;
					engine::headerin('site/profile');
					exit;
				}
				else {
					if($file["size"]>PHOTO_MAX_SIZE) {
						$error[] = 2;
						$_SESSION['msg'] = 2;
						engine::headerin('site/profile');
						exit;
					}
					$photo_dimensions = getimagesize($file["tmp_name"]);
					if($photo_dimensions[0]>PHOTO_MAX_WIDTH) {
						$error[] = 3;
						$_SESSION['msg'] = 3;
						engine::headerin('site/profile');
						exit;
					}
					if($photo_dimensions[1]>PHOTO_MAX_HEIGHT) {
						$error[] = 4;
						$_SESSION['msg'] = 4;
						engine::headerin('site/profile');
						exit;
					}
				}
				if(sizeof($error)) {
					$_SESSION['msg'] = 5;
					engine::headerin('site/profile');
					exit;
				}
				else {
					preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $file["name"], $ext);
					$photo_crypt_name = engine::crypts(USER_USERNAME.time(), 1, 0).".".$ext[1];
					$directory = '../../media/images/users/b_'.$photo_crypt_name;
					$tdirectory = '../../media/images/users/'.$photo_crypt_name;
					$ttdirectory = '../../media/images/users/tt_'.$photo_crypt_name;
					move_uploaded_file($file["tmp_name"], $directory);
					engine::resizeimg($directory, $tdirectory, 300, 300); 
					engine::resizeimg($directory, $ttdirectory, 100, 100); 
					if(SESSION_STATUS!=false) {
						$stmt = $this->conn->prepare("UPDATE users SET photo = ? WHERE id = ?");
						$stmt->execute(array($photo_crypt_name,USER_ID));
						if($stmt->rowCount()!=0) {
							engine::headerin(''.USER_USERNAME);
							exit;
						} 
						else { 
							engine::headerin('site/profile');
							exit;
						}
					}
				}
			}
		}

		public function updateusernotification($not_upvote, $not_answer, $not_follower, $not_mention, $not_system) {

			/* Function for updating notifications on email */

			if(SESSION_STATUS!=false) {
				$stmt = $this->conn->prepare("UPDATE users SET not_upvote = ?, not_answer = ?, not_follower = ?, not_mention = ?, not_system = ? WHERE id = ?");
				$stmt->execute(array($not_upvote,$not_answer,$not_follower,$not_mention,$not_system,USER_ID));
				if($stmt->rowCount()!=0) {
					$_SESSION['msg'] = 1;
				}
				engine::headerin('site/notification');
					exit; 
			}
		}
		
		public function disableaccount() {

			/* Function for disabling account */

			if(SESSION_STATUS!=false) {
				if(engine::getuserdata(USER_ID, "STATUS")) {
					$stmt = $this->conn->prepare("UPDATE users SET status = '0' WHERE id = ?");
					$stmt->execute(array(USER_ID));
					if($stmt->rowCount()!=0) {
						engine::clearcookies();
						exit;
					} 
					else { 
						die('Error: You can not disable account! Contact administration!'); 
					}
				}
			}
		}

		public function get_main_categories($limit=80) {

			/* Get all categories for pop-up window if categories are not choosed */

			$message = '';
			$stmt = $this->conn->prepare("SELECT * FROM categories ORDER BY followers DESC LIMIT ".$limit);
			$stmt->execute();
			if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$message .= '<div class="topic_photo_card" id="category_'.$ii['id'].'" onclick="selectCategory('.$ii['id'].');">';
					$message .= '<div class="photo">';
					$message .= '<div class="TopicPhoto">';
					$message .= '<a><img class="topic_photo_img" src="media/images/categories/'.$ii['image'].'" width="200" alt="'.$ii['name'].'" height="200"></a>';
					$message .= '</div>';
					$message .= '</div>';
					$message .= '<div class="info_wrapper">';
					$message .= '<span class="info_background">';
					$message .= '<div class="check_wrapper"><div class="check"></div></div>';
					$message .= '<div class="info"><span class="TopicName">'.$ii['name'].'</span> <span class="TopicFollowers">'.$ii['followers'].' '.get_text(118,1).'</span></div>';
					$message .= '</span>';
					$message .= '</div>';
					$message .= '</div>';
				}
			}
			return $message;
		}

		public function get_follow_button_in_category($id, $followers) {

			/* Get UI for follow button on category page */

			$message = '';
			if(USER_ID) {
				$stmt = $this->conn->prepare("SELECT id FROM follows WHERE user_id = ? and category_id = ?");
			    $stmt->execute(array(USER_ID,$id));
			    if($stmt->rowCount()>0) {
			       	$message .= "<a id='follow-btn-".$id."' class='unfollow_b' onclick='unfollow(".$id.");'><span class='uu'>".get_text(237,1)."</span><span class='ff' style='display:none'>".get_text(238,1)."</span><span class='count'>".$followers."</span></a>";
			    } else {
			        $message .= "<a id='follow-btn-".$id."' class='follow_b' onclick='follow(".$id.");'><span class='ff'>".get_text(238,1)."</span><span class='uu' style='display:none'>".get_text(237,1)."</span><span class='count'>".$followers."</span></a>";
			    }
			}
			return $message;
		}

		public function notify() {	

			/* Get notification for live counter (Check if appear new notifications via AJAX) */

			header('Content-type: application/json'); 
			$stmt = $this->conn->prepare("SELECT COUNT(*) FROM notifications WHERE to_user = ? AND viewed != '1'");
			$stmt->execute(array(USER_ID));
			$i = $stmt->fetch(PDO::FETCH_ASSOC);
			$total = $i['COUNT(*)'];
			$json = array("response" => $total);
			echo json_encode($json);		
		}

		public function get_question($q_url) {

			/* Function to get question data on question page */

			if($q_url) {
				$stmt = $this->conn->prepare("SELECT * FROM questions WHERE url = ?");
				$stmt->execute(array($q_url));
				if($stmt->rowCount()!=0) {
					$iii = $stmt->fetch(PDO::FETCH_ASSOC);
					return $iii;
				}
				else return false;
			}
			else return false;
		}

		public function get_category($c_url) {

			/* Function to get category data on category page */

			if($c_url) {
				$stmt = $this->conn->prepare("SELECT * FROM categories WHERE url = ?");
				$stmt->execute(array($c_url));
				if($stmt->rowCount()!=0) {
					$iii = $stmt->fetch(PDO::FETCH_ASSOC);
					return $iii;
				}
				else return false;
			}
			else return false;
		}

		public function last_want_answers($q_id) {

			/* Function to get last activity in who want answers section */

			if($q_id) {
				$stmt = $this->conn->prepare("SELECT time FROM likes WHERE question_id = ? ORDER BY time DESC LIMIT 1");
				$stmt->execute(array($q_id));
				if($stmt->rowCount()!=0) {
					$iii = $stmt->fetch(PDO::FETCH_ASSOC);
					return get_text(232,1).': '.get_time($iii['time']);
				}
				else return get_text(231,1);
			}
			else return get_text(231,1);
		}

		public function count_want_answers($q_id) {

			/* Function to get number of users, who want answers */

			if($q_id) {
				$stmt = $this->conn->prepare("SELECT COUNT(*) FROM likes WHERE question_id = ?");
				$stmt->execute(array($q_id));
				if($stmt->rowCount()!=0) {
					$iii = $stmt->fetch(PDO::FETCH_ASSOC);
					return countnumber($iii['COUNT(*)']);
				}
				else return 0;
			}
			else return 0;
		}

		public function want_answers($q_id) {

			/* Show users, who followed question */

			$message='';
			$stmt = $this->conn->prepare("SELECT * FROM likes WHERE question_id = ? ORDER BY time DESC LIMIT 10");
			$stmt->execute(array($q_id));
			foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
				$stmt = $this->conn->prepare("SELECT username, name, photo FROM users WHERE id = ?");
				$stmt->execute(array($ii['user_id']));
				$iii = $stmt->fetch(PDO::FETCH_ASSOC);
				$message .= '<a class="question_want_answer" href="'.$iii['username'].'">';
				if(file_exists('../../media/images/users/tt_'.$iii['photo'].'')) {
					$message .= '<div class="img imgLiquid profile_photo_img" style="width:22px; height:22px"><img src="media/images/users/tt_'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
				}
				else {
					$message .= '<div class="img imgLiquid profile_photo_img" style="width:22px; height:22px"><img src="media/images/users/'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
				}
				$message .= '</a>';
			}
			return $message;
		}

		public function get_question_categories($category) {

			/* Function for getting question categories like tags */

			$message='';
			$question_categories = explode(" ", $category);
			foreach($question_categories as $category_type) {
				$stmt = $this->conn->prepare("SELECT url, name FROM categories WHERE id = ?");
				$stmt->execute(array($category_type));
				$category = $stmt->fetch(PDO::FETCH_ASSOC);
				if($category['name']!='') {
					$message .= '<a class="topic" id="'.$category_type.'" href="category/'.$category['url'].'">';
					$message .= '<span class="name_text"><span class="TopicName">'.$category['name'].'</span></span>';
					$message .= '</a>';
				}
			}
			return $message;
		}

		public function get_question_categories_by_space($category) {

			/* Function for getting question categories delimited by space (For edit questions tags) */

			$message='';
			$question_categories = explode(" ", $category);
			foreach($question_categories as $category_type) {
				$stmt = $this->conn->prepare("SELECT name FROM categories WHERE id = ?");
				$stmt->execute(array($category_type));
				$category = $stmt->fetch(PDO::FETCH_ASSOC);
				$message .= $category['name'].' ';
			}
			return $message;
		}

		public function get_question_categories_by_comma($category) {

			/* Function for getting question categories delimited by space (For edit questions tags) */

			$message='';
			$question_categories = explode(" ", $category);
			foreach($question_categories as $category_type) {
				$stmt = $this->conn->prepare("SELECT name FROM categories WHERE id = ?");
				$stmt->execute(array($category_type));
				$category = $stmt->fetch(PDO::FETCH_ASSOC);
				$message .= $category['name'].', ';
			}
			return substr($message, 0, -4);
		}

		public function check_stream($stream_type, $id) {

			/* Function for checking for new questions in stream */

			header('Content-type: application/json');
			$stmt = $this->conn->prepare("SELECT category_id FROM follows WHERE user_id = ?");
			$stmt->execute(array(USER_ID));
			if($stmt->rowCount()!=0) {
				$user_categories = "AND (";
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					if($stream_type==2) 	$user_categories .= "questions.category LIKE '%".$ii['category_id']." %' OR ";
					else 					$user_categories .= "category LIKE '%".$ii['category_id']." %' OR ";
				}
				if($stream_type==2)		$user_categories .= "questions.category LIKE '0')";
				else 					$user_categories .= "category LIKE '0')";
			}

			switch($stream_type) {
				case 0: 		/* Unanswered */
								$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM questions WHERE status='1' AND answers<1 ".$user_categories." AND time>?");
	            				$stmt->execute(array($id));
	            				$result = $stmt->fetch(PDO::FETCH_ASSOC);
					            break;
				case 1: 		/* Answered */
								$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM questions WHERE status='1' AND answers>0  ".$user_categories." AND time>?");
	            				$stmt->execute(array($id));
	            				$result = $stmt->fetch(PDO::FETCH_ASSOC);
					            break;
				case 2: 		/* Popular */
								$stmt = $this->conn->prepare("SELECT questions.*, COUNT( likes.question_id ) AS number FROM questions, likes WHERE questions.id = likes.question_id AND questions.status = '1' ".$t_user_categories." GROUP BY likes.question_id ORDER BY number DESC LIMIT 1");
								$stmt->execute();
	            				$max = $stmt->fetch(PDO::FETCH_ASSOC);
								$stmt = $this->conn->prepare("SELECT questions.*, COUNT( likes.question_id ) AS number FROM questions, likes WHERE questions.id = likes.question_id AND questions.status = '1' ".$user_categories." AND questions.time>? GROUP BY likes.question_id HAVING COUNT(*)>?");
	            				$stmt->execute(array($id,$max['number']/2));
	            				$result['total'] = $stmt->rowCount();
					            break;
				case 3: 		/* Followed */
								$result['total'] = 0;
								break;
				case 4: 		/* All */
								$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM questions WHERE status='1' ".$user_categories." AND time>?");
					            $stmt->execute(array($id));
					            $result = $stmt->fetch(PDO::FETCH_ASSOC);
					            break;
			}
	        if($result['total']>0) {
	        	$json = array ('response'=>"1");
	        }
	        else {
	        	$json = array ('response'=>'0');
	        }
	        echo json_encode($json);
		}

		public function report($r_type, $r_id, $r_reason) {

			/* Function for saving reports in DB */

			if(($r_type==1 || $r_type==2) && $r_id) {
				$stmt = $this->conn->prepare("INSERT INTO reports (type, r_id, user_id, message, time, ip) VALUES (?, ?, ?, ?, ?, ?)");
				$stmt->execute(array($r_type, $r_id, USER_ID, $r_reason, time(), USER_IP));
			}
		}

		public function get_notifications($n_type=10, $from_id, $limit=20) {

			/* Function for notifications stream */

			$message = "";
			$json = array ('response'=>'success');	
			header('Content-type: application/json');

			if($n_type>0 && $n_type<5) {
				$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM notifications WHERE to_user=? AND type=? AND id<?");
	            $stmt->execute(array(USER_ID,$n_type,$from_id));
				$iii = $stmt->fetch(PDO::FETCH_ASSOC);
				$remain = $iii['total']-$limit;

				$stmt = $this->conn->prepare("SELECT * FROM notifications WHERE to_user=? AND type=? AND id<? ORDER BY id DESC LIMIT ?");
				$stmt->bindValue(1, USER_ID, PDO::PARAM_INT);
				$stmt->bindValue(2, $n_type, PDO::PARAM_STR);
				$stmt->bindValue(3, $from_id, PDO::PARAM_INT);
				$stmt->bindValue(4, $limit, PDO::PARAM_INT);
				$stmt->execute();
			}
			elseif($n_type==5) {
				$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM notifications WHERE to_user=? AND (type=? OR type=? OR type=? OR type=?) AND id<?");
	            $stmt->execute(array(USER_ID,'5','6','7','8',$from_id));
				$iii = $stmt->fetch(PDO::FETCH_ASSOC);
				$remain = $iii['total']-$limit;

				$stmt = $this->conn->prepare("SELECT * FROM notifications WHERE to_user=? AND (type=? OR type=? OR type=? OR type=?) AND id<? ORDER BY id DESC LIMIT ?");
				$stmt->bindValue(1, USER_ID, PDO::PARAM_INT);
				$stmt->bindValue(2, '5', PDO::PARAM_STR);
				$stmt->bindValue(3, '6', PDO::PARAM_STR);
				$stmt->bindValue(4, '7', PDO::PARAM_STR);
				$stmt->bindValue(5, '8', PDO::PARAM_STR);
				$stmt->bindValue(6, $from_id, PDO::PARAM_INT);
				$stmt->bindValue(7, $limit, PDO::PARAM_INT);
				$stmt->execute();
			}
			else {
				$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM notifications WHERE to_user=? AND id<?");
	            $stmt->execute(array(USER_ID,$from_id));
				$iii = $stmt->fetch(PDO::FETCH_ASSOC);
				$remain = $iii['total']-$limit;

				$stmt = $this->conn->prepare("SELECT * FROM notifications WHERE to_user=? AND id<? ORDER BY id DESC LIMIT ?");
				$stmt->bindValue(1, USER_ID, PDO::PARAM_INT);
				$stmt->bindValue(2, $from_id, PDO::PARAM_INT);
				$stmt->bindValue(3, $limit, PDO::PARAM_INT);
				$stmt->execute();
			}
			
	        if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$id = $ii['id'];
					$user_id = $ii['user_id'];
					$n_id = $ii['n_id'];
					$time = $ii['time'];
					$viewed = $ii['viewed'];
					$info = $ii['info'];
					
					switch ($ii['type']) {
						case 1:			$stmt = $this->conn->prepare("SELECT question_id FROM answers WHERE id=?");
							            $stmt->execute(array($n_id));
										$iii = $stmt->fetch(PDO::FETCH_ASSOC);
										$stmt = $this->conn->prepare("SELECT url, question FROM questions WHERE id=?");
							            $stmt->execute(array($iii['question_id']));
										$iii = $stmt->fetch(PDO::FETCH_ASSOC);
										$n_url = 'q/'.$iii['url'];
										$n_title = $iii['question'];
										$n_text = get_text(174,1);	
										break;
						case 2:			$stmt = $this->conn->prepare("SELECT question_id FROM answers WHERE id=?");
							            $stmt->execute(array($n_id));
										$iii = $stmt->fetch(PDO::FETCH_ASSOC);
										$stmt = $this->conn->prepare("SELECT url, question FROM questions WHERE id=?");
							            $stmt->execute(array($iii['question_id']));
										$iii = $stmt->fetch(PDO::FETCH_ASSOC);
										$n_url = 'q/'.$iii['url'];
										$n_title = $iii['question'];
										$n_text = get_text(175,1);
										break;
						case 3:			$stmt = $this->conn->prepare("SELECT url, question FROM questions WHERE id=?");
							            $stmt->execute(array($n_id));
										$iii = $stmt->fetch(PDO::FETCH_ASSOC);
										$n_url = 'q/'.$iii['url'];
										$n_title = $iii['question'];
										$n_text = get_text(176,1);	
										break;
						case 4:			$stmt = $this->conn->prepare("SELECT question_id FROM answers WHERE id=?");
							            $stmt->execute(array($n_id));
										$iii = $stmt->fetch(PDO::FETCH_ASSOC);
										$stmt = $this->conn->prepare("SELECT url, question FROM questions WHERE id=?");
							            $stmt->execute(array($iii['question_id']));
										$iii = $stmt->fetch(PDO::FETCH_ASSOC);
										$n_url = 'q/'.$iii['url'];
										$n_title = $iii['question'];
										$n_text = get_text(177,1);
										break;
						case 5:			$stmt = $this->conn->prepare("SELECT url, question FROM questions WHERE id=?");
							            $stmt->execute(array($n_id));
							            $iii = $stmt->fetch(PDO::FETCH_ASSOC);
										$n_url = 'q/'.$iii['url'];
										$n_title = '<a style="color:#2b6dad">'.$iii['question'].'</a>';
										$n_text = get_text(178,1);	
										break;
						case 6:			$n_url = '';
										$n_title = '<a style="color:#2b6dad">'.strip_tags($info).'</a>';
										$n_text = get_text(179,1);
										break;
						case 7:			$stmt = $this->conn->prepare("SELECT question_id FROM answers WHERE id=?");
							            $stmt->execute(array($n_id));
										$iii = $stmt->fetch(PDO::FETCH_ASSOC);
										$stmt = $this->conn->prepare("SELECT url, question FROM questions WHERE id=?");
							            $stmt->execute(array($iii['question_id']));
										$iii = $stmt->fetch(PDO::FETCH_ASSOC);
										$n_url = 'q/'.$iii['url'];
										$n_title = '<a style="color:#2b6dad">'.$iii['question'].'</a>';
										$n_text = get_text(178,1);
										break;
						case 8:			$n_url = '';
										$n_title = '<a style="color:#2b6dad">'.strip_tags($info).'</a>';
										$n_text = get_text(179,1);	
										break;
					}

					$stmt = $this->conn->prepare("SELECT username, name, photo FROM users WHERE id = ?");
					$stmt->execute(array($user_id));
					$iii = $stmt->fetch(PDO::FETCH_ASSOC);
					if(!$iii['photo']) $iii['photo'] = "photo_default.png";

					$message .= '<div class="pagedlist_item" id="nn_'.$id.'">';
					if($viewed==1) 		$viewed = 'seen';
					else 				$viewed = 'new';
		    		$message .= '<div class="Notif '.$viewed.'">';
		    		if($ii['type']<6) {
		    			$message .= '<a class="overlay" href="'.$n_url.'"></a>';
		    		}
		    		$message .= '<div class="notif_item">';
		    		$message .= '<div class="notif_photo">';
		    		if(file_exists('../../media/images/users/tt_'.$iii['photo'].'')) {
						$message .= '<div class="img imgLiquid profile_photo_img" style="width:25px; height:25px"><img class="profile_photo_img" src="media/images/users/tt_'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
					}
					else {
						$message .= '<div class="img imgLiquid profile_photo_img" style="width:25px; height:25px"><img class="profile_photo_img" src="media/images/users/'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
					}
		    		$message .= '</div>';
		    		$message .= '<div class="notif_title">';
		    		if($ii['type']<5) {
		    			$message .= '<a class="user" href="'.$iii['username'].'">'.$iii['username'].'</a>';
						$message .= ' '.$n_text.' ';
						$message .= '<a class="question_link" href="'.$n_url.'">'.$n_title.'</a>';
						$message .= '<a class="timestamp"><i class="fa fa-clock-o"></i> '.get_time($time).'</a>';
					}
					elseif($ii['type']>4 && $ii['type']<7) {
						$message .= get_text(180,1).' ';
						$message .= $n_title.' ';
						$message .= $n_text;
						$message .= ' <a class="timestamp"><i class="fa fa-clock-o"></i> '.get_time($time).'</a>';
					}
					elseif($ii['type']==7) {
						$message .= get_text(181,1).' ';
						$message .= $n_title.' ';
						$message .= $n_text;
						$message .= ' <a class="timestamp"><i class="fa fa-clock-o"></i> '.get_time($time).'</a>';
					}
					elseif($ii['type']==8) {
						$message .= get_text(182,1).' ';
						$message .= $n_title.' ';
						$message .= $n_text;
						$message .= ' <a class="timestamp"><i class="fa fa-clock-o"></i> '.get_time($time).'</a>';
					}
		    		$message .= '</div>';
		    		$message .= '</div>';
		    		$message .= '</div>';
		    		$message .= '</div>';
				}
			}
			else
			{
				$message .= '<span class="no-notifications">'.get_text(183,1).'</span>';
			}
			$json = array("response" => $message,"last_id" => $id, "remain" => $remain);
			echo json_encode($json);	
		}

		public function readed_notifications($n_type=10) {

			/* Function for making notifications readed */

			header('Content-type: application/json');
			if(($n_type>0 && $n_type<5) || $n_type==6) {
				$stmt = $this->conn->prepare("UPDATE notifications SET viewed='1' WHERE to_user=? AND type=?");
	            $stmt->execute(array(USER_ID,$n_type));
			}
			elseif($n_type==5) {
				$stmt = $this->conn->prepare("UPDATE notifications SET viewed='1' WHERE to_user=? AND (type=? OR type=? OR type=? OR type=?)");
	            $stmt->execute(array(USER_ID,'5','6','7','8'));
			}
			else {
				$stmt = $this->conn->prepare("UPDATE notifications SET viewed='1' WHERE to_user=?");
	            $stmt->execute(array(USER_ID));
			}
			$json = array ('response'=>'success');
			echo json_encode($json);	
		}

		public function stream($stream_type=2, $from_id, $limit=15) {

			/* Function for loading stream */

			$message = "";
			$json = array ('response'=>'success');	
			header('Content-type: application/json');

			$stmt = $this->conn->prepare("SELECT category_id FROM follows WHERE user_id = ?");
			$stmt->execute(array(USER_ID));
			if($stmt->rowCount()!=0) {
				$user_categories = "AND (";
				$t_user_categories = "AND (";
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$user_categories .= "category LIKE '%".$ii['category_id']." %' OR ";
					$t_user_categories .= "questions.category LIKE '%".$ii['category_id']." %' OR ";
				}
				$user_categories .= "category LIKE '0')";
				$t_user_categories .= "questions.category LIKE '0')";
			}

			switch($stream_type) {
				case 0: 		/* Unaswered questions */
								$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM questions WHERE status='1' AND answers<1 ".$user_categories." AND time<?");
	            				$stmt->execute(array($from_id));
					            $iii = $stmt->fetch(PDO::FETCH_ASSOC);
								$remain = $iii['total']-$limit;

					            $stmt = $this->conn->prepare("SELECT * FROM questions WHERE status='1' AND answers<1  ".$user_categories." AND time<? ORDER BY time desc LIMIT ?");
					            $stmt->bindValue(1, $from_id, PDO::PARAM_INT);
								$stmt->bindValue(2, $limit, PDO::PARAM_INT);
					            $stmt->execute();
					            break;
				case 1: 		/* Answered questions */
								$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM questions WHERE status='1' AND answers>0  ".$user_categories." AND time<?");
	            				$stmt->execute(array($from_id));
					            $iii = $stmt->fetch(PDO::FETCH_ASSOC);
								$remain = $iii['total']-$limit;

					            $stmt = $this->conn->prepare("SELECT * FROM questions WHERE status='1' AND answers>0  ".$user_categories." AND time<? ORDER BY time desc LIMIT ?");
					            $stmt->bindValue(1, $from_id, PDO::PARAM_INT);
								$stmt->bindValue(2, $limit, PDO::PARAM_INT);
					            $stmt->execute();
					            break;
				case 2: 		/* Popular questions */
								$stmt = $this->conn->prepare("SELECT questions.*, COUNT( likes.question_id ) AS number FROM questions, likes WHERE questions.id = likes.question_id AND questions.status = '1' ".$t_user_categories." GROUP BY likes.question_id ORDER BY number DESC LIMIT 1");
								$stmt->execute();
	            				$max = $stmt->fetch(PDO::FETCH_ASSOC);
								$stmt = $this->conn->prepare("SELECT questions.*, COUNT( likes.question_id ) AS number FROM questions, likes WHERE questions.id = likes.question_id AND questions.status = '1' ".$t_user_categories." AND questions.time<? GROUP BY likes.question_id HAVING COUNT(*)>?");
	            				$stmt->execute(array($from_id,$max['number']/2));
	            				$total = $stmt->rowCount();
								$remain = $total-$limit;

					            $stmt = $this->conn->prepare("SELECT questions. * , COUNT( likes.question_id ) AS number FROM questions, likes WHERE questions.id = likes.question_id AND questions.status = '1' ".$t_user_categories." AND questions.time<? GROUP BY likes.question_id HAVING COUNT(*)>? ORDER BY time DESC LIMIT ?");
					            $stmt->bindValue(1, $from_id, PDO::PARAM_INT);
								$stmt->bindValue(2, $max['number']/2, PDO::PARAM_INT);
								$stmt->bindValue(3, $limit, PDO::PARAM_INT);
					            $stmt->execute();
					            break;
				case 3: 		/* Followed questions */
								$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM likes WHERE user_id=? AND id<?");
	            				$stmt->execute(array(USER_ID,$from_id));
					            $iii = $stmt->fetch(PDO::FETCH_ASSOC);
								$remain = $iii['total']-$limit;

					            $stmt = $this->conn->prepare("SELECT * FROM likes WHERE user_id=? AND id<? ORDER BY id DESC LIMIT ?");
					            $stmt->bindValue(1, USER_ID, PDO::PARAM_INT);
					            $stmt->bindValue(2, $from_id, PDO::PARAM_INT);
								$stmt->bindValue(3, $limit, PDO::PARAM_INT);
					            $stmt->execute();
					            break;
				case 4: 		/* All questions */
								$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM questions WHERE status='1' ".$user_categories." AND time<?");
	            				$stmt->execute(array($from_id));
					            $iii = $stmt->fetch(PDO::FETCH_ASSOC);
								$remain = $iii['total']-$limit;

					            $stmt = $this->conn->prepare("SELECT * FROM questions WHERE status='1' ".$user_categories." AND time<? ORDER BY time DESC LIMIT ?");
					            $stmt->bindValue(1, $from_id, PDO::PARAM_INT);
								$stmt->bindValue(2, $limit, PDO::PARAM_INT);
					            $stmt->execute();
					            break;
			}
			
	        if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					if($stream_type==3) {
						$last_id = $ii['id'];
						$stmt = $this->conn->prepare("SELECT * FROM questions WHERE id=?");
						$stmt->execute(array($ii['question_id']));
						$ii = $stmt->fetch(PDO::FETCH_ASSOC);
					}
					$id = $ii['id'];
					$user = $ii['user_id'];
					$question = nl2br($ii['question']);
					if(strlen($ii['description'])>130) {
						$description = substr($ii['description'],0,130).'... <a href="q/'.$ii['url'].'">('.get_text(235,1).')</a>';
						$description = nl2br(txt2link(format_text($description)));
					}
					else {
						$description = nl2br(txt2link(format_text($ii['description']))); 
					}
					$image = $ii['image'];
					$url = $ii['url'];
					$status = $ii['status']; 
					$time = $ii['time']; 
					$views = $ii['views']; 
					$category = $ii['category'];
					$answers = $ii['answers'];

					$stmt = $this->conn->prepare("SELECT username, bio, name, photo, visit FROM users WHERE id = ?");
					$stmt->execute(array($user));
					$iii = $stmt->fetch(PDO::FETCH_ASSOC);
					if(!$iii['photo']) $iii['photo'] = "photo_default.png";
					$username = $iii['username'];

					$message .= '<div class="feed_item" id="qq_'.$id.'" time="'.$ii['time'].'">';

					$message .= '<div class="TopicListItem">';
					$message .= engine::get_question_categories($category);
					$message .= '</div>';

					$message .= '<div class="feed_author">';
					$message .= '<a href="'.$username.'" class="feed_author_image">';
					if(file_exists('../../media/images/users/tt_'.$iii['photo'])) {
						$message .= '<div class="img imgLiquid feed_author_photo" style="width:32px; height:32px"><img src="media/images/users/tt_'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
					}
					else {
						$message .= '<div class="img imgLiquid profile_photo_img" style="width:32px; height:32px"><img src="media/images/users/'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
					}
					$message .= '</a>';

					$message .= '<span class="feed_user_info">';
					$message .= '<span class="feed_author_username">';
					$message .= '<a class="user" href="'.$username.'">'.$username.'</a> <span>'.$iii['bio'].'</span>';
					$message .= '</span>';
					$message .= '<span class="feed_question_status">';
					if($answers>0) {
						$message .= "<span style='color:green'>".get_text(239,1).'<span class="bullet" style="color:green"> • </span><a href="q/'.$url.'" style="color:green">'.get_time($time).'</a></span>';
					}
					else {
						$message .= "<span style='color:rgb(153, 0, 56)'>".get_text(240,1).'<span class="bullet" style="color:rgb(153, 0, 56)"> • </span><a href="q/'.$url.'" target="_blank" style="color:rgb(153, 0, 56)">'.get_time($time).'</a></span>';
					}
					$message .= '</span>';
					$message .= '</span>';
					$message .= '</div>';

					$message .= '<div class="feed_question">';
					$message .= '<a class="feed_question_link" href="q/'.$url.'">';
					$message .= '<span>'.$question.'</span>';
					$message .= '</a>';
					$message .= '</div>';

					$message .= '<div class="feed_description">';
					$message .= '<span>'.$description.'</span>';
					if($image!="") {
						$message .= '<a href="media/images/users/'.$image.'" class="image-link"><img src="media/images/users/'.$image.'" class="question-image"></a>';
					}
					$message .= '</div>';

					$message .= '<div class="feed_counter">';
					$message .= engine::getlikes($id,$ii['user_id'],1);
					if(SESSION_STATUS!=false) {
						$message .= '<div class="action_item" style="cursor:pointer" data-toggle="modal" data-target="#reportModal" data-id="'.$id.'" data-type="1"><a>'.get_text(219,1).'</a></div>';
					}
					if($ii['user_id']==USER_ID && SESSION_STATUS!=false) {	
						$message .= '<div class="action_item" onclick="q_delete('.$id.');" style="cursor:pointer"><a>'.get_text(226,1).'</a></div>';
						$message .= '<div class="action_item" style="cursor:pointer" data-toggle="modal" data-target="#editqModal" data-id="'.$id.'"><a>'.get_text(227,1).'</a></div>';
						$message .= '<input type="hidden" id="db_qq_'.$id.'" value="'.htmlentities($ii['question'], ENT_QUOTES, "UTF-8").'">';
						$message .= '<input type="hidden" id="db_qd_'.$id.'" value="'.htmlentities($ii['description'], ENT_QUOTES, "UTF-8").'">';
						$message .= '<input type="hidden" id="db_qc_'.$id.'" value="'.$ii['category'].'">';
					}
					$message .= '</div>';
					$message .= '</div>';
				}
			}
			if($stream_type!=3) {
				$json = array("response" => $message,"last_id" => $ii['time'], "remain" => $remain);
			}
			else {
				$json = array("response" => $message,"last_id" => $last_id, "remain" => $remain);
			}
			echo json_encode($json);	
		}

		public function get_category_questions($c_id, $from_id, $limit=15) {

			/* Function for loading questions in category page */

			$message = "";
			$json = array ('response'=>'success');	
			header('Content-type: application/json');
			$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM questions WHERE status='1' AND ( category LIKE '".$c_id." %' OR category LIKE '% ".$c_id." %' ) AND time<?");
			$stmt->execute(array($from_id));
			$iii = $stmt->fetch(PDO::FETCH_ASSOC);
			$remain = $iii['total']-$limit;
			$stmt = $this->conn->prepare("SELECT * FROM questions WHERE status='1' AND ( category LIKE '".$c_id." %' OR category LIKE '% ".$c_id." %' ) AND time<? ORDER BY time DESC LIMIT ?");
			$stmt->bindValue(1, $from_id, PDO::PARAM_INT);
			$stmt->bindValue(2, $limit, PDO::PARAM_INT);
			$stmt->execute();
	        if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$id = $ii['id'];
					$user = $ii['user_id'];
					$question = nl2br($ii['question']);
					if(strlen($ii['description'])>130) {
						$description = substr($ii['description'],0,130).'... <a href="q/'.$ii['url'].'">('.get_text(235,1).')</a>';
						$description = nl2br(txt2link(format_text($description)));
					}
					else {
						$description = nl2br(txt2link(format_text($ii['description']))); 
					}
					$image = $ii['image'];
					$url = $ii['url'];
					$status = $ii['status']; 
					$time = $ii['time']; 
					$views = $ii['views']; 
					$category = $ii['category'];
					$answers = $ii['answers'];

					$stmt = $this->conn->prepare("SELECT username, bio, name, photo, visit FROM users WHERE id = ?");
					$stmt->execute(array($user));
					$iii = $stmt->fetch(PDO::FETCH_ASSOC);
					if(!$iii['photo']) $iii['photo'] = "photo_default.png";
					$username = $iii['username'];

					$message .= '<div class="feed_item" id="qq_'.$id.'" time="'.$ii['time'].'">';

					$message .= '<div class="TopicListItem">';
					$message .= engine::get_question_categories($category);
					$message .= '</div>';

					$message .= '<div class="feed_author">';
					$message .= '<a href="'.$username.'" class="feed_author_image">';
					if(file_exists('../../media/images/users/tt_'.$iii['photo'])) {
						$message .= '<div class="img imgLiquid feed_author_photo" style="width:32px; height:32px"><img src="media/images/users/tt_'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
					}
					else {
						$message .= '<div class="img imgLiquid profile_photo_img" style="width:32px; height:32px"><img src="media/images/users/'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
					}
					$message .= '</a>';

					$message .= '<span class="feed_user_info">';
					$message .= '<span class="feed_author_username">';
					$message .= '<a class="user" href="'.$username.'">'.$username.'</a> <span>'.$iii['bio'].'</span>';
					$message .= '</span>';
					$message .= '<span class="feed_question_status">';
					if($answers>0) {
						$message .= "<span style='color:green'>".get_text(239,1).'<span class="bullet" style="color:green"> • </span><a href="q/'.$url.'" style="color:green">'.get_time($time).'</a></span>';
					}
					else {
						$message .= "<span style='color:rgb(153, 0, 56)'>".get_text(240,1).'<span class="bullet" style="color:rgb(153, 0, 56)"> • </span><a href="q/'.$url.'" target="_blank" style="color:rgb(153, 0, 56)">'.get_time($time).'</a></span>';
					}
					$message .= '</span>';
					$message .= '</span>';
					$message .= '</div>';

					$message .= '<div class="feed_question">';
					$message .= '<a class="feed_question_link" href="q/'.$url.'">';
					$message .= '<span>'.$question.'</span>';
					$message .= '</a>';
					$message .= '</div>';

					$message .= '<div class="feed_description">';
					$message .= '<span>'.$description.'</span>';
					if($image!="") {
						$message .= '<a href="media/images/users/'.$image.'" class="image-link"><img src="media/images/users/'.$image.'" class="question-image"></a>';
					}
					$message .= '</div>';

					$message .= '<div class="feed_counter">';
					$message .= engine::getlikes($id,$ii['user_id'],1);
					if(SESSION_STATUS!=false) {
						$message .= '<div class="action_item" style="cursor:pointer" data-toggle="modal" data-target="#reportModal" data-id="'.$id.'" data-type="1"><a>'.get_text(219,1).'</a></div>';
					}
					if($ii['user_id']==USER_ID && SESSION_STATUS!=false) {	
						$message .= '<div class="action_item" onclick="q_delete('.$id.');" style="cursor:pointer"><a>'.get_text(226,1).'</a></div>';
						$message .= '<div class="action_item" style="cursor:pointer" data-toggle="modal" data-target="#editqModal" data-id="'.$id.'"><a>'.get_text(227,1).'</a></div>';
						$message .= '<input type="hidden" id="db_qq_'.$id.'" value="'.htmlentities($ii['question'], ENT_QUOTES, "UTF-8").'">';
						$message .= '<input type="hidden" id="db_qd_'.$id.'" value="'.htmlentities($ii['description'], ENT_QUOTES, "UTF-8").'">';
						$message .= '<input type="hidden" id="db_qc_'.$id.'" value="'.$ii['category'].'">';
					}
					$message .= '</div>';
					$message .= '</div>';
				}
			}
			$json = array("response" => $message,"last_id" => $ii['time'], "remain" => $remain);
			echo json_encode($json);	
		}

		public function get_user_questions($user_id, $from_id, $limit=15) {

			/* Function for loading questions on user`s page */

			$message = "";
			$json = array ('response'=>'success');	
			header('Content-type: application/json');
			$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM questions WHERE status='1' AND user_id=? AND time<?");
			$stmt->execute(array($user_id,  $from_id));
			$iii = $stmt->fetch(PDO::FETCH_ASSOC);
			$remain = $iii['total']-$limit;
			$stmt = $this->conn->prepare("SELECT * FROM questions WHERE status='1' AND user_id=? AND time<? ORDER BY time desc LIMIT ?");
			$stmt->bindValue(1, $user_id, PDO::PARAM_INT);
			$stmt->bindValue(2, $from_id, PDO::PARAM_INT);
			$stmt->bindValue(3, $limit, PDO::PARAM_INT);
			$stmt->execute();
	        if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$id = $ii['id'];
					$user = $ii['user_id'];
					$question = nl2br($ii['question']);
					if(strlen($ii['description'])>130) {
						$description = substr($ii['description'],0,130).'... <a href="q/'.$ii['url'].'">('.get_text(235,1).')</a>';
						$description = nl2br(txt2link(format_text($description)));
					}
					else {
						$description = nl2br(txt2link(format_text($ii['description']))); 
					}
					$image = $ii['image'];
					$url = $ii['url'];
					$status = $ii['status']; 
					$time = $ii['time']; 
					$views = $ii['views']; 
					$category = $ii['category'];
					$answers = $ii['answers'];

					$stmt = $this->conn->prepare("SELECT username, bio, name, photo, visit FROM users WHERE id = ?");
					$stmt->execute(array($user));
					$iii = $stmt->fetch(PDO::FETCH_ASSOC);
					if(!$iii['photo']) $iii['photo'] = "photo_default.png";
					$username = $iii['username'];

					$message .= '<div class="feed_item" id="qq_'.$id.'" time="'.$ii['time'].'">';

					$message .= '<div class="TopicListItem">';
					$message .= engine::get_question_categories($category);
					$message .= '</div>';

					$message .= '<div class="feed_author">';
					$message .= '<a href="'.$username.'" class="feed_author_image">';
					if(file_exists('../../media/images/users/tt_'.$iii['photo'])) {
						$message .= '<div class="img imgLiquid feed_author_photo" style="width:32px; height:32px"><img src="media/images/users/tt_'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
					}
					else {
						$message .= '<div class="img imgLiquid profile_photo_img" style="width:32px; height:32px"><img src="media/images/users/'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
					}
					$message .= '</a>';

					$message .= '<span class="feed_user_info">';
					$message .= '<span class="feed_author_username">';
					$message .= '<a class="user" href="'.$username.'">'.$username.'</a> <span>'.$iii['bio'].'</span>';
					$message .= '</span>';
					$message .= '<span class="feed_question_status">';
					if($answers>0) {
						$message .= "<span style='color:green'>".get_text(239,1).'<span class="bullet" style="color:green"> • </span><a href="q/'.$url.'" style="color:green">'.get_time($time).'</a></span>';
					}
					else {
						$message .= "<span style='color:rgb(153, 0, 56)'>".get_text(240,1).'<span class="bullet" style="color:rgb(153, 0, 56)"> • </span><a href="q/'.$url.'" target="_blank" style="color:rgb(153, 0, 56)">'.get_time($time).'</a></span>';
					}
					$message .= '</span>';
					$message .= '</span>';
					$message .= '</div>';

					$message .= '<div class="feed_question">';
					$message .= '<a class="feed_question_link" href="q/'.$url.'">';
					$message .= '<span>'.$question.'</span>';
					$message .= '</a>';
					$message .= '</div>';

					$message .= '<div class="feed_description">';
					$message .= '<span>'.$description.'</span>';
					if($image!="") {
						$message .= '<a href="media/images/users/'.$image.'" class="image-link"><img src="media/images/users/'.$image.'" class="question-image"></a>';
					}
					$message .= '</div>';

					$message .= '<div class="feed_counter">';
					$message .= engine::getlikes($id,$ii['user_id'],1);
					if(SESSION_STATUS!=false) {
						$message .= '<div class="action_item" style="cursor:pointer" data-toggle="modal" data-target="#reportModal" data-id="'.$id.'" data-type="1"><a>'.get_text(219,1).'</a></div>';
					}
					if($ii['user_id']==USER_ID && SESSION_STATUS!=false) {	
						$message .= '<div class="action_item" onclick="q_delete('.$id.');" style="cursor:pointer"><a>'.get_text(226,1).'</a></div>';
						$message .= '<div class="action_item" style="cursor:pointer" data-toggle="modal" data-target="#editqModal" data-id="'.$id.'"><a>'.get_text(227,1).'</a></div>';
						$message .= '<input type="hidden" id="db_qq_'.$id.'" value="'.htmlentities($ii['question'], ENT_QUOTES, "UTF-8").'">';
						$message .= '<input type="hidden" id="db_qd_'.$id.'" value="'.htmlentities($ii['description'], ENT_QUOTES, "UTF-8").'">';
						$message .= '<input type="hidden" id="db_qc_'.$id.'" value="'.$ii['category'].'">';
					}
					$message .= '</div>';
					$message .= '</div>';
				}
			}
			$json = array("response" => $message,"last_id" => $ii['time'], "remain" => $remain);
			echo json_encode($json);	
		}

		public function get_user_answers($user_id, $from_id, $limit=15) {

			/* Function for loading questions on user`s page */

			$message = "";
			$json = array ('response'=>'success');	
			header('Content-type: application/json');
			$stmt = $this->conn->prepare("SELECT * FROM answers WHERE user_id=? AND time<? GROUP BY question_id");
			$stmt->execute(array($user_id, $from_id));
			$iii = $stmt->rowCount();
			$remain = $iii-$limit;
			$stmt = $this->conn->prepare("SELECT * FROM answers WHERE user_id=? AND time<? GROUP BY question_id ORDER BY time desc LIMIT ?");
			$stmt->bindValue(1, $user_id, PDO::PARAM_INT);
			$stmt->bindValue(2, $from_id, PDO::PARAM_INT);
			$stmt->bindValue(3, $limit, PDO::PARAM_INT);
			$stmt->execute();
	        if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $i) {
					$stmt = $this->conn->prepare("SELECT * FROM questions WHERE id=?");
					$stmt->execute(array($i['question_id']));
					$ii = $stmt->fetch(PDO::FETCH_ASSOC);
					$id = $ii['id'];
					$user = $ii['user_id'];
					$question = nl2br($ii['question']);
					if(strlen($ii['description'])>130) {
						$description = substr($ii['description'],0,130).'... <a href="q/'.$ii['url'].'">('.get_text(235,1).')</a>';
						$description = nl2br(txt2link(format_text($description)));
					}
					else {
						$description = nl2br(txt2link(format_text($ii['description']))); 
					}
					$image = $ii['image'];
					$url = $ii['url'];
					$status = $ii['status']; 
					$time = $ii['time']; 
					$views = $ii['views']; 
					$category = $ii['category'];
					$answers = $ii['answers'];

					$stmt = $this->conn->prepare("SELECT username, bio, name, photo, visit FROM users WHERE id = ?");
					$stmt->execute(array($user));
					$iii = $stmt->fetch(PDO::FETCH_ASSOC);
					if(!$iii['photo']) $iii['photo'] = "photo_default.png";
					$username = $iii['username'];

					$message .= '<div class="feed_item" id="qq_'.$id.'" time="'.$ii['time'].'">';

					$message .= '<div class="TopicListItem">';
					$message .= engine::get_question_categories($category);
					$message .= '</div>';

					$message .= '<div class="feed_author">';
					$message .= '<a href="'.$username.'" class="feed_author_image">';
					if(file_exists('../../media/images/users/tt_'.$iii['photo'])) {
						$message .= '<div class="img imgLiquid feed_author_photo" style="width:32px; height:32px"><img src="media/images/users/tt_'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
					}
					else {
						$message .= '<div class="img imgLiquid profile_photo_img" style="width:32px; height:32px"><img src="media/images/users/'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
					}
					$message .= '</a>';

					$message .= '<span class="feed_user_info">';
					$message .= '<span class="feed_author_username">';
					$message .= '<a class="user" href="'.$username.'">'.$username.'</a> <span>'.$iii['bio'].'</span>';
					$message .= '</span>';
					$message .= '<span class="feed_question_status">';
					if($answers>0) {
						$message .= "<span style='color:green'>".get_text(239,1).'<span class="bullet" style="color:green"> • </span><a href="q/'.$url.'" style="color:green">'.get_time($time).'</a></span>';
					}
					else {
						$message .= "<span style='color:rgb(153, 0, 56)'>".get_text(240,1).'<span class="bullet" style="color:rgb(153, 0, 56)"> • </span><a href="q/'.$url.'" target="_blank" style="color:rgb(153, 0, 56)">'.get_time($time).'</a></span>';
					}
					$message .= '</span>';
					$message .= '</span>';
					$message .= '</div>';

					$message .= '<div class="feed_question">';
					$message .= '<a class="feed_question_link" href="q/'.$url.'">';
					$message .= '<span>'.$question.'</span>';
					$message .= '</a>';
					$message .= '</div>';

					$message .= '<div class="feed_description">';
					$message .= '<span>'.$description.'</span>';
					if($image!="") {
						$message .= '<a href="media/images/users/'.$image.'" class="image-link"><img src="media/images/users/'.$image.'" class="question-image"></a>';
					}
					$message .= '</div>';

					$message .= '<div class="feed_counter">';
					$message .= engine::getlikes($id,$ii['user_id'],1);
					if(SESSION_STATUS!=false) {
						$message .= '<div class="action_item" style="cursor:pointer" data-toggle="modal" data-target="#reportModal" data-id="'.$id.'" data-type="1"><a>'.get_text(219,1).'</a></div>';
					}
					if($ii['user_id']==USER_ID && SESSION_STATUS!=false) {	
						$message .= '<div class="action_item" onclick="q_delete('.$id.');" style="cursor:pointer"><a>'.get_text(226,1).'</a></div>';
						$message .= '<div class="action_item" style="cursor:pointer" data-toggle="modal" data-target="#editqModal" data-id="'.$id.'"><a>'.get_text(227,1).'</a></div>';
						$message .= '<input type="hidden" id="db_qq_'.$id.'" value="'.htmlentities($ii['question'], ENT_QUOTES, "UTF-8").'">';
						$message .= '<input type="hidden" id="db_qd_'.$id.'" value="'.htmlentities($ii['description'], ENT_QUOTES, "UTF-8").'">';
						$message .= '<input type="hidden" id="db_qc_'.$id.'" value="'.$ii['category'].'">';
					}
					$message .= '</div>';
					$message .= '</div>';
				}
			}
			$json = array("response" => $message,"last_id" => $ii['time'], "remain" => $remain);
			echo json_encode($json);	
		}

		public function get_answers($q_id, $from_id, $limit=5) {

			/* Function for loading answers */

			$message = "";
			$json = array ('response'=>'success');	
			header('Content-type: application/json');

			$answers_array = array();
			$exist_b_a = 0;

			if($from_id==0) {
				/* Load firstly best answer */
				$stmt = $this->conn->prepare("SELECT * FROM answers WHERE status='1' AND question_id=? ORDER BY upvotes DESC LIMIT 1");
				$stmt->bindValue(1, $q_id, PDO::PARAM_INT);
				$stmt->execute();
				$best_answer = $stmt->fetch(PDO::FETCH_ASSOC);

				/* Checking if best answer exists */
				if($best_answer['id']!='' && $best_answer['upvotes']>0) 		$exist_b_a = 1;
			}
			
			/* Calculating number of questions remaining after loading first, without best answer */
			if($exist_b_a==1) {
				$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM answers WHERE status='1' AND question_id=? AND id<>?");
				$stmt->execute(array($q_id, $best_answer['id']));
			}
			else {
				$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM answers WHERE status='1' AND question_id=?");
				$stmt->execute(array($q_id));
			}
			$iii = $stmt->fetch(PDO::FETCH_ASSOC);
			$remain = $iii['total']-$limit-$from_id;

			/* Loading from database answers, without best answer */
			if($exist_b_a==1) {
				$stmt = $this->conn->prepare("SELECT * FROM answers WHERE status='1' AND question_id=? AND id<>? ORDER BY time DESC LIMIT $from_id, $limit");
				$stmt->bindValue(1, $q_id, PDO::PARAM_INT);
				$stmt->bindValue(2, $best_answer['id'], PDO::PARAM_INT);
			}
			else {
				$stmt = $this->conn->prepare("SELECT * FROM answers WHERE status='1' AND question_id=? ORDER BY time DESC LIMIT $from_id, $limit");
				$stmt->bindValue(1, $q_id, PDO::PARAM_INT);
			}
			$stmt->execute();
			$arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if($exist_b_a==1) {
				array_unshift($arr, $best_answer);
			}
	        
	        if(count($arr)>0) {
	        	$pos = 0;
				foreach($arr as $ii) {
					$pos++;
					$id = $ii['id'];
					$user = $ii['user_id'];
					$username = engine::get_user_name($ii['user_id']);
					$answer = nl2br(format_text($ii['answer']));
					$answer = mb_convert_encoding($answer, 'HTML-ENTITIES', 'utf-8');
					/* Make links with _blank */
					$doc = new DOMDocument();
					$doc->loadHTML($answer);
					$links = $doc->getElementsByTagName('a');
					foreach($links as $item) {
					    if(!$item->hasAttribute('target')) {
					        $item->setAttribute('target','_blank');  
					    }
					}
					$answer=$doc->saveHTML();
					$url = $ii['url'];
					$time = $ii['time']; 
					$stmt = $this->conn->prepare("SELECT bio, name, photo, visit FROM users WHERE id = ?");
					$stmt->execute(array($user));
					$iii = $stmt->fetch(PDO::FETCH_ASSOC);
					if(!$iii['photo']) $iii['photo'] = "photo_default.png";
					if($exist_b_a==1 && $pos==1) {
						$message .= '<div class="feed_item feed_best" id="aa_'.$id.'">';
						$message .= '<b><i class="fa fa-check"></i> '.get_text(491,1).'</b>';
					}
					else {
						$message .= '<div class="feed_item" id="aa_'.$id.'">';
					}
					$message .= '<div class="answer_info">';
					$message .= '<a href="'.$username.'" class="answer_image_link">';
					if(file_exists('../../media/images/users/tt_'.$iii['photo'])) {
						$message .= '<div class="img imgLiquid profile_photo_img" style="width:32px; height:32px"><img src="media/images/users/tt_'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
					}
					else {
						$message .= '<div class="img imgLiquid profile_photo_img" style="width:32px; height:32px"><img src="media/images/users/'.$iii['photo'].'" alt="'.$iii['name'].'"></div>';
					}
					$message .= '</a>';
					$message .= '<div class="answer_author">';
				    $message .= '<span class="feed_item_answer_user">';
					$message .= '<a class="user" href="'.$username.'">'.$username.'</a> <span>'.$iii['bio'].'</span>';
					$message .= '</span>';
					$message .= '<span class="answer_voters">';
					$message .= get_text(310,1).' '.get_time($ii['time']);
					$message .= '</span>';
					$message .= '</div>';
					$message .= '</div>';
					$message .= '<span class="comment">'.$answer.'</span>';
					$message .= '<div class="answer_action">';
					$message .= engine::getupvotes($id,$ii['user_id']);
					if(SESSION_STATUS!=false) {
						$message .= '<div class="action_item" style="cursor:pointer" data-toggle="modal" data-target="#reportModal" data-id="'.$id.'" data-type="2"><a class="downvote">'.get_text(219,1).'</a></div>';
					}
					if($user==USER_ID && SESSION_STATUS!=false) {	
						$message .= '<div class="action_item" onclick="a_delete('.$id.');" style="cursor:pointer"><a class="downvote">'.get_text(226,1).'</a></div>';
						$message .= '<div class="action_item" style="cursor:pointer" data-toggle="modal" data-target="#editaModal" data-id="'.$id.'"><a class="downvote">'.get_text(227,1).'</a></div>';
						$message .= '<input type="hidden" id="db_a_'.$id.'" value="'.htmlentities($ii['answer'], ENT_QUOTES, "UTF-8").'">';
					}
					$message .= '</div>';
					$message .= '</div>';
				}
			}
			$json = array("response" => $message,"last_id" => $limit+$from_id, "remain" => $id);
			echo json_encode($json);	
		}

		public function url_slug($str, $options = array())  {

			/* Convert text in url */

			$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());	
			$defaults = array(
				'delimiter' => '-',
				'limit' => null,
				'lowercase' => true,
				'replacements' => array(),
				'transliterate' => true,
			);
			
			$options = array_merge($defaults, $options);
			$char_map = array(
				'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C', 
				'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 
				'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O', 
				'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH', 
				'ß' => 'ss', 
				'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 
				'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 
				'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 
				'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 
				'ÿ' => 'y',
				'©' => '(c)',
				'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
				'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
				'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
				'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
				'Ϋ' => 'Y',
				'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
				'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
				'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
				'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
				'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
				'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
				'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g', 
				'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
				'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
				'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
				'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
				'Я' => 'Ya',
				'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
				'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
				'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
				'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
				'я' => 'ya',
				'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
				'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
				'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U', 
				'Ž' => 'Z', 
				'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
				'ž' => 'z', 
				'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z', 
				'Ż' => 'Z', 
				'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
				'ż' => 'z',
				'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 
				'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
				'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
				'š' => 's', 'ū' => 'u', 'ž' => 'z'
			);
			
			$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
			if ($options['transliterate']) {
				$str = str_replace(array_keys($char_map), $char_map, $str);
			}
			$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
			$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
			$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
			$str = trim($str, $options['delimiter']);
			return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
		}

		public function ask($question, $description, $image, $categories) {

			/* Function for saving questions into database */

			$question = strip_tags($question);
			$description = strip_tags($description);

			if(strlen($question)>2 && USER_ID && $categories) {
				$bad_words = explode(', ', FILTER_WORDS);
				for($i=0; $i<sizeof($bad_words); $i++) {
					if($bad_words[$i]!=' ' && $bad_words[$i]!='') {
						$question = preg_replace('/\b'.$bad_words[$i].'\b/iu', FILTER_WORD, $question);
						$description = preg_replace('/\b'.$bad_words[$i].'\b/iu', FILTER_WORD, $description);
					}
		        }
				$categories = array_slice($categories, 0, 10);
				$categories = implode(" ", $categories).' ';
				$url = engine::url_slug($question);
				$stmt = $this->conn->prepare("SELECT COUNT(*) FROM questions WHERE url LIKE '".$url."%'");
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				if($data['COUNT(*)']>0) {
					$url = $url.'-'.$data['COUNT(*)'];
				}

				$image = $_FILES["image"];
				$error = array();
				$file = isset($image) ? $image : FALSE;
				if($file && $_FILES['image']['size']>0) {
					if(!preg_match("/^image\/(pjpeg|jpeg|jpg|png|gif|bmp)$/i", $file["type"]) || sizeof($error)) {
						engine::headerin('');
						exit;
					}
					else {
						preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $file["name"], $ext);
						$photo_crypt_name = engine::crypts(USER_USERNAME.time(), 1, 0).".".$ext[1];
						$directory = "../../media/images/users/".''.$photo_crypt_name;
						$tdirectory = "../../media/images/users/".'t_'.$photo_crypt_name;
						move_uploaded_file($file["tmp_name"], $directory);
						engine::resizeimg($directory, $tdirectory, 400, 300);
					}
				}
				else {
					$photo_crypt_name = '';
				}
				$stmt = $this->conn->prepare("INSERT INTO questions (category, url, user_id, question, description, image, status, ip, views, answers, time, time_asked) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$stmt->execute(array($categories, $url, USER_ID, $question, $description, $photo_crypt_name, '1', USER_IP, 0, 0, time(), time()));
				$stmt = $this->conn->prepare("SELECT id FROM questions WHERE url = '".$url."'");
				$stmt->execute();
				$id = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt = $this->conn->prepare("INSERT INTO likes (question_id, user_id, to_user, time) VALUES (?, ?, ?, ?)");
				$stmt->execute(array($id['id'],USER_ID,USER_ID,time()));
				engine::headerin('q/'.$url);

			}
			else {
				engine::headerin('');
			}
		}

		public function edit_question($id, $question, $description, $image, $categories) {

			/* Function for saving questions into database */

			if($id!='' && $question!='' && USER_ID!='' && $categories!='') {
				$bad_words = explode(', ', FILTER_WORDS);
				for($i=0; $i<sizeof($bad_words); $i++) {
					if($bad_words[$i]!=' ' && $bad_words[$i]!='') {
						$question = preg_replace('/\b'.$bad_words[$i].'\b/iu', FILTER_WORD, $question);
						$description = preg_replace('/\b'.$bad_words[$i].'\b/iu', FILTER_WORD, $description);
					}
			    }
				$categories = array_slice($categories, 0, 10);
				$categories = implode(" ", $categories).' ';
				$url = engine::url_slug($question);
				$stmt = $this->conn->prepare("SELECT COUNT(*) FROM questions WHERE url LIKE '".$url."%'");
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				if($data['COUNT(*)']>0) {
					$url = $url.'-'.$data['COUNT(*)'];
				}

				$image = $_FILES["image_p"];
				$error = array();
				$file = isset($image) ? $image : FALSE;
				if($file && $_FILES['image_p']['size']>0) {
					if(!preg_match("/^image\/(pjpeg|jpeg|jpg|png|gif|bmp)$/i", $file["type"]) || sizeof($error)) {
						engine::headerin('');
						exit;
					}
					else {
						preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $file["name"], $ext);
						$photo_crypt_name = engine::crypts(USER_USERNAME.time(), 1, 0).".".$ext[1];
						$directory = "../../media/images/users/".$photo_crypt_name;
						$tdirectory = "../../media/images/users/".'t_'.$photo_crypt_name;
						move_uploaded_file($file["tmp_name"], $directory);
						engine::resizeimg($directory, $tdirectory, 400, 300);
					}
					$stmt = $this->conn->prepare("UPDATE questions SET category=?, url=?, question=?, description=?, image=? WHERE id=? AND user_id=?");
					$stmt->execute(array($categories, $url, $question, $description, $photo_crypt_name, $id, USER_ID));
				}
				else {
					$stmt = $this->conn->prepare("UPDATE questions SET category=?, url=?, question=?, description=? WHERE id=? AND user_id=?");
					$stmt->execute(array($categories, $url, $question, $description, $id, USER_ID));
				}
				engine::headerin('q/'.$url);
			}
			else  {
				engine::headerin('');
			}
		}

		public function edit_answer($id, $answer) {

			/* Function for saving edited answer */
			
			if($id!='' && $answer!='') {
				$bad_words = explode(', ', FILTER_WORDS);
				for($i=0; $i<sizeof($bad_words); $i++) {
					if($bad_words[$i]!=' ' && $bad_words[$i]!='') {
						$answer = preg_replace('/\b'.$bad_words[$i].'\b/iu', FILTER_WORD, $answer);
					}
			    }

	            $answer = str_replace("<p>&nbsp;</p>", "", $answer);
				$stmt = $this->conn->prepare("UPDATE answers SET answer=? WHERE id=? AND user_id=?");
				$stmt->execute(array($answer,$id,USER_ID));
				header('Content-type: application/json'); 
				$answer = nl2br(format_text($answer));
				$json = array ('response'=>$answer);
				echo(json_encode($json));
			} 
		}

		public function like($id) {

			/* Want answers function for question */

			$id = intval($id);
			$json = array ('response'=>'success');
			$stmt = $this->conn->prepare("SELECT user_id, url, question FROM questions WHERE id = ?");
			$stmt->execute(array($id));
			if($stmt->rowCount()!=0) {
				$iii = $stmt->fetch(PDO::FETCH_ASSOC);
				$user_question = $iii['user_id'];
				$url = $iii['url'];
				$question = $iii['question'];
				if($user_question!=USER_ID) {
					if(SESSION_STATUS!=false) {
						$stmt = $this->conn->prepare("SELECT * FROM likes WHERE question_id = ? AND user_id = ?");
						$stmt->execute(array($id,USER_ID));
						if($stmt->rowCount()!=0) {
							$stmt = $this->conn->prepare("DELETE FROM likes WHERE question_id = ? AND user_id = ?");
							$stmt->execute(array($id,USER_ID));
							if($stmt->rowCount()!=0) {
								$stmt = $this->conn->prepare("DELETE FROM points_log WHERE log_type='1' AND log_id = ? AND from_user = ?");
								$stmt->execute(array($id,USER_ID));
								$stmt = $this->conn->prepare("DELETE FROM notifications WHERE type=? AND n_id = ? AND user_id = ?");
								$stmt->execute(array('3',$id,USER_ID));
								header('Content-type: application/json'); 
								$json = array("type" => "delete");
								echo json_encode($json);
							}
							else { 
								die('Error: Wntanswr-del-01! Contact administration!'); 
							}
						}
						else {
							$stmt = $this->conn->prepare("INSERT INTO likes (question_id, user_id, to_user, time) VALUES (?, ?, ?, ?)");
							$stmt->execute(array($id,USER_ID,$user_question,time()));
							if($stmt->rowCount()!=0) {
								$stmt = $this->conn->prepare("INSERT INTO points_log (log_type, log_id, from_user, to_user, time) VALUES (?, ?, ?, ?, ?)");
								$stmt->execute(array('1',$id,USER_ID,$user_question,time()));
								if($user_question!=USER_ID) {
									$stmt = $this->conn->prepare("INSERT INTO notifications (to_user, type, user_id, n_id, time, viewed) VALUES (?, ?, ?, ?, ?, ?)");
									$stmt->execute(array($user_question,'3',USER_ID,$id,time(),'0'));
									if(engine::getuserdata($user_question, "NOT_FOLLOWER") == 1 && engine::getuserdata($user_question, "ONLINE") == 0) {
										$user_question_link = engine::get_user_name($user_question);
										$theme = get_text(452,1).' '.SITE_NAME.' - '.SITE_DESCRIPTION;
										$body = get_text(83,1).' '.$user_question_link.',<br><br>'.get_text(242,1).' <a href="http://'.SITE_DOMAIN.'/'.USER_USERNAME.'">'.USER_USERNAME.'</a> '.get_text(454,1).' – "'.$question.'". <a href="http://'.SITE_DOMAIN.'/q/'.$url.'">'.get_text(252,1).'</a> '.get_text(453,1).'.<br>'.get_text(77,1).' '.SITE_NAME.'<br><br>'.get_text(78,1).',<br>'.SITE_NAME.' - '.SITE_DESCRIPTION.'<br><br><small>'.get_text(79,1).'.</small>';
										$to = engine::getuserdata($user_question,"EMAIL");
										engine::send_email($to,$theme,$body);
									}
								}
								header('Content-type: application/json'); 
								$json = array("type" => "add");
								echo(json_encode($json)); 
							}
							else { 
								die('Error: Wtnanswr-add-01! Contact administration!'); 
							}
						}
					}
				}
			}	
		}

		public function upvote($id) {

			/* Upvote function for answers */

			$id = intval($id);
			$json = array ('response'=>'success');
			$stmt = $this->conn->prepare("SELECT question_id, user_id, answer FROM answers WHERE id = ?");
			$stmt->execute(array($id));
			if($stmt->rowCount()!=0) {
				$iii = $stmt->fetch(PDO::FETCH_ASSOC);
				$user_answer = $iii['user_id'];
				$question_id = $iii['question_id'];
				$answer_text = $iii['answer'];
				if($user_answer!=USER_ID) {
					if(SESSION_STATUS!=false) {
						$stmt = $this->conn->prepare("SELECT * FROM answers_likes WHERE answer_id = ? AND user_id = ?");
						$stmt->execute(array($id,USER_ID));
						if($stmt->rowCount()!=0) {
							$stmt = $this->conn->prepare("DELETE FROM answers_likes WHERE answer_id = ? AND user_id = ?");
							$stmt->execute(array($id,USER_ID));
							if($stmt->rowCount()!=0) {
								$stmt = $this->conn->prepare("UPDATE answers SET upvotes=upvotes-1 WHERE id = ?");
								$stmt->execute(array($id));
								$stmt = $this->conn->prepare("DELETE FROM points_log WHERE log_type='2' AND log_id = ? AND from_user = ?");
								$stmt->execute(array($id,USER_ID));
								$stmt = $this->conn->prepare("DELETE FROM notifications WHERE type=? AND n_id = ? AND user_id = ?");
								$stmt->execute(array('1',$id,USER_ID));
								header('ContentHeader-type: application/json'); 
								$json = array("type" => "delete");
								echo json_encode($json);
							}
							else { 
								die('Error: Wntanswr-del-01! Contact administration!'); 
							}
						}
						else {
							$stmt = $this->conn->prepare("INSERT INTO answers_likes (answer_id, user_id, to_user, time) VALUES (?, ?, ?, ?)");
							$stmt->execute(array($id,USER_ID,$user_answer,time()));
							if($stmt->rowCount()!=0) {
								$stmt = $this->conn->prepare("UPDATE answers SET upvotes=upvotes+1 WHERE id = ?");
								$stmt->execute(array($id));
								$stmt = $this->conn->prepare("INSERT INTO points_log (log_type, log_id, from_user, to_user, time) VALUES (?, ?, ?, ?, ?)");
								$stmt->execute(array('2',$id,USER_ID,$user_answer,time()));
								if($user_answer!=USER_ID) {
									$stmt = $this->conn->prepare("INSERT INTO notifications (to_user, type, user_id, n_id, time, viewed) VALUES (?, ?, ?, ?, ?, ?)");
									$stmt->execute(array($user_answer,'1',USER_ID,$id,time(),'0'));
									if(engine::getuserdata($user_answer, "NOT_UPVOTE") == 1 && engine::getuserdata($user_answer, "ONLINE") == 0) {
										$user_answer_link = engine::get_user_name($user_answer);
										$stmt = $this->conn->prepare("SELECT url FROM questions WHERE id=?");
										$stmt->execute(array($question_id));
										$iiii = $stmt->fetch(PDO::FETCH_ASSOC);
										$theme = get_text(449,1).' '.SITE_NAME.' - '.SITE_DESCRIPTION;
										$answer_text = strip_tags($answer_text);
										$body = get_text(83,1).' '.$user_answer_link.',<br><br>'.get_text(242,1).' <a href="http://'.SITE_DOMAIN.'/'.USER_USERNAME.'">'.USER_USERNAME.'</a> '.get_text(455,1).' - '.$answer_text.'. <a href="http://'.SITE_DOMAIN.'/q/'.$iiii['url'].'">'.get_text(252,1).'</a> '.get_text(453,1).'.<br>'.get_text(77,1).' '.SITE_NAME.'<br><br>'.get_text(78,1).',<br>'.SITE_NAME.' - '.SITE_DESCRIPTION.'<br><br><small>'.get_text(79,1).'.</small>';
										$to = engine::getuserdata($user_answer,"EMAIL");
										engine::send_email($to,$theme,$body);
									}
								}
								header('Content-type: application/json'); 
								$json = array("type" => "add");
								echo(json_encode($json)); 
							}
							else { 
								die('Error: Wtnanswr-add-01! Contact administration!'); 
							}
						}
					}
				}
			}	
		}

		public function contact($name, $email, $subject, $message) {

			/* Contact form function - send to email from contact form */

			$theme = 'New message from contact form on '.SITE_NAME;
			$body = 'You have received new message from <a href="http://'.SITE_DOMAIN.'">'.SITE_NAME.'</a>.<br> Name: '.$name;
			if(SESSION_STATUS==1) {
				$body .= ' (User: <a href="'.USER_USERNAME.'">'.USER_USERNAME.'</a>, IP: '.USER_IP.')';
			}
			$body .= ';<br>Email: <a href="mailto:'.$email.'">'.$email.'</a>;<br> Subject: '.$subject.';<br> Message: '.$message.'.';
			$to = SITE_EMAIL;
			engine::send_email($to,$theme,$body);
		}
		
		public function q_delete($id) {

			/* Function for deleting questions */

			if(SESSION_STATUS!=false && $id>0) {
				$stmt = $this->conn->prepare("SELECT * FROM questions WHERE id = ? AND user_id = ?");
				$stmt->execute(array($id,USER_ID));
				if($stmt->rowCount()!=0) {
					$stmt = $this->conn->prepare("DELETE answers_likes FROM answers_likes INNER JOIN answers ON answers_likes.answer_id = answers.id WHERE answers.question_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE notifications FROM notifications INNER JOIN answers ON notifications.n_id = answers.id WHERE (notifications.type='1' OR notifications.type='2' OR notifications.type='4' OR notifications.type='7' OR notifications.type='8') AND answers.question_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE points_log FROM points_log INNER JOIN answers ON points_log.log_id = answers.id WHERE points_log.log_type='2' AND answers.question_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE reports FROM reports INNER JOIN answers ON reports.r_id = answers.id WHERE reports.type='2' AND answers.question_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE FROM questions WHERE id = ? AND user_id = ?");
					$stmt->execute(array($id,USER_ID));
					$stmt = $this->conn->prepare("DELETE FROM answers WHERE question_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE FROM likes WHERE question_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE FROM notifications WHERE (type='3' OR type='5' OR type='6') AND n_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE FROM points_log WHERE log_type='1' AND log_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE FROM reports WHERE type='1' AND r_id = ?");
					$stmt->execute(array($id));
				}
			}
			header('Content-type: application/json'); 
			$json = array ('response'=>'success');
			echo json_encode($json);
		}

		public function a_delete($id) {

			/* Function for deleting answers */

			if(SESSION_STATUS!=false && $id>0) {
				$stmt = $this->conn->prepare("SELECT upvotes, question_id FROM answers WHERE id = ? AND user_id = ?");
				$stmt->execute(array($id,USER_ID));
				if($stmt->rowCount()!=0) {
					$iii = $stmt->fetch(PDO::FETCH_ASSOC);
					$stmt = $this->conn->prepare("UPDATE questions SET answers=answers-1 WHERE id = ?");
					$stmt->execute(array($iii['question_id']));
					$stmt = $this->conn->prepare("DELETE FROM answers WHERE id = ? AND user_id = ?");
					$stmt->execute(array($id,USER_ID));
					$stmt = $this->conn->prepare("DELETE FROM answers_likes WHERE answer_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE FROM notifications WHERE (type='1' OR type='2' OR type='4' OR type='7' OR type='8') AND n_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE FROM points_log WHERE log_type='2' AND log_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE FROM reports WHERE type='2' AND r_id = ?");
					$stmt->execute(array($id));

					$stmt = $this->conn->prepare("SELECT time FROM answers WHERE question_id = ? ORDER BY time DESC LIMIT 1");
					$stmt->execute(array($iii['question_id']));
					$ii = $stmt->fetch(PDO::FETCH_ASSOC);
					if($ii['time']) {
						$stmt = $this->conn->prepare("UPDATE questions SET time=? WHERE id=?");
						$stmt->execute(array($ii['time'], $iii['question_id']));
					}
				}
			}
			header('Content-type: application/json'); 
			$json = array ('response'=>'success');
			echo json_encode($json);
		}
		
		public function headerin($adress) {
			/* Function redirect */
			$https = (HTTPS == 1 ? 's' : '');
			header('Location: http' . $https . '://'.SITE_DOMAIN.'/'.$adress.'');
		}
		
		public function answer($q_id, $answer) {

			/* Function for answering questions */

			$bad_words = explode(', ', FILTER_WORDS);
			for($i=0; $i<sizeof($bad_words); $i++) {
				if($bad_words[$i]!=' ' && $bad_words[$i]!='') {
					$answer = preg_replace('/\b'.$bad_words[$i].'\b/iu', FILTER_WORD, $answer);
				}
		    }

			$answer = ucfirst(strip_tags($answer, '<p><a><b><hr><h2><h3><i><strong><em><span><blockquote><ol><ul><li><br><img>'));
			preg_match_all('/(@\w+)/', $answer, $mentions);
			$image = '';
			if((is_numeric($q_id)) && $q_id>0 && strlen($answer)>2) {
				$stmt = $this->conn->prepare("SELECT id, url, user_id, question FROM questions WHERE id = ?");
				$stmt->execute(array($q_id));
				if($stmt->rowCount()!=0) {
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
						$user_id = $ii['user_id'];
						$url = $ii['url'];
						$question = $ii['question'];
					}
					$status = 1;
					$stmt = $this->conn->prepare("INSERT INTO answers (question_id, answer, status, user_id, upvotes, time) VALUES (?, ?, ?, ?, ?, ?)");
					$stmt->execute(array($q_id, $answer, $status, USER_ID, 0, time()));
					if($stmt->rowCount()!=0) {
						$stmt = $this->conn->prepare("UPDATE questions SET answers=answers+1, time=? WHERE id=?");
						$stmt->execute(array(time(),$q_id));
						/* Notification */
						if($user_id!=USER_ID) {
							$stmt = $this->conn->prepare("SELECT id FROM answers WHERE question_id=? AND user_id=? ORDER BY time DESC LIMIT 1");
							$stmt->execute(array($q_id,USER_ID));
							$iiii = $stmt->fetch(PDO::FETCH_ASSOC);
							$stmt = $this->conn->prepare("INSERT INTO notifications (to_user, type, user_id, n_id, time, viewed) VALUES (?, ?, ?, ?, ?, ?)");
							$stmt->execute(array($user_id,'2',USER_ID,$iiii['id'],time(),'0'));
							if(engine::getuserdata($user_id, "NOT_ANSWER") == 1 && engine::getuserdata($user_id, "ONLINE") == 0) {
								$user_id_link = engine::get_user_name($user_id);
								$stmt = $this->conn->prepare("SELECT question FROM questions WHERE id=?");
								$stmt->execute(array($q_id));
								$i = $stmt->fetch(PDO::FETCH_ASSOC);
								$theme = get_text(456,1).' '.SITE_NAME.' – '.SITE_DESCRIPTION;
								$body = get_text(83,1).' '.$user_id_link.',<br><br>'.get_text(242,1).' <a href="http://'.SITE_DOMAIN.'/'.USER_USERNAME.'">'.USER_USERNAME.'</a> '.get_text(451,1).' - "'.$question.'". <a href="http://'.SITE_DOMAIN.'/q/'.$url.'">'.get_text(252,1).'</a> '.get_text(453,1).'.<br>'.get_text(77,1).' '.SITE_NAME.'<br><br>'.get_text(78,1).',<br>'.SITE_NAME.' - '.SITE_DESCRIPTION.'<br><br><small>'.get_text(79,1).'.</small>';
								$to = engine::getuserdata($user_id,"EMAIL");
								engine::send_email($to,$theme,$body);
							}
						}
						$total_mentions = count($mentions);
						if($total_mentions>0) {
							for($i=0;$i<$total_mentions;$i++) {
								$mentioned = str_replace("@","",$mentions[0][$i]);
								$mentioned = str_replace(" ","",$mentioned);
								if($mentioned) {
									$stmtt = $this->conn->prepare("SELECT id FROM users WHERE username=?");
									$stmtt->execute(array($mentioned));
									if($stmtt->rowCount() != 0) {
										$mentioned_user = $stmtt->fetch(PDO::FETCH_ASSOC);
										$stmt = $this->conn->prepare("SELECT id FROM answers WHERE question_id=? AND user_id=? ORDER BY time DESC LIMIT 1");
										$stmt->execute(array($q_id,USER_ID));
										$iiii = $stmt->fetch(PDO::FETCH_ASSOC);
										$stmt = $this->conn->prepare("INSERT INTO notifications (to_user, type, user_id, n_id, time, viewed) VALUES (?, ?, ?, ?, ?, ?)");
										$stmt->execute(array($mentioned_user['id'],'4',USER_ID,$iiii['id'],time(),'0'));
										if(engine::getuserdata($mentioned_user['id'], "NOT_MENTION") == 1 && engine::getuserdata($mentioned_user['id'], "ONLINE") == 0) {
											$user_id_link = engine::get_user_name($mentioned_user['id']);
											$theme = get_text(457,1).' '.SITE_NAME.' – '.SITE_DESCRIPTION;
											$answer = strip_tags($answer);
											$body = get_text(83,1).' '.$user_id_link.',<br><br>'.get_text(242,1).' <a href="http://'.SITE_DOMAIN.'/'.USER_USERNAME.'">'.USER_USERNAME.'</a> '.get_text(177,1).' - "'.$answer.'". <a href="http://'.SITE_DOMAIN.'/q/'.$url.'">'.get_text(252,1).'</a> '.get_text(453,1).'.<br>'.get_text(77,1).' '.SITE_NAME.'<br><br>'.get_text(78,1).',<br>'.SITE_NAME.' - '.SITE_DESCRIPTION.'<br><br><small>'.get_text(79,1).'.</small>';
											$to = engine::getuserdata($mentioned_user['id'],"EMAIL");
											engine::send_email($to,$theme,$body);
										}
									}
								}
							}
						}
						/* Notify all whom follow this question */
						$stmt = $this->conn->prepare("SELECT id FROM answers WHERE question_id=? AND user_id=? ORDER BY time DESC LIMIT 1");
						$stmt->execute(array($q_id,USER_ID));
						$iiii = $stmt->fetch(PDO::FETCH_ASSOC);
						$stmt = $this->conn->prepare("SELECT user_id FROM likes WHERE question_id=?");
						$stmt->bindValue(1, $q_id, PDO::PARAM_INT);
						$stmt->execute();
				        if($stmt->rowCount()!=0) {
							foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
								if($ii['user_id']!=USER_ID && $ii['user_id']!=$user_id) {
									$stmt = $this->conn->prepare("INSERT INTO notifications (to_user, type, user_id, n_id, time, viewed) VALUES (?, ?, ?, ?, ?, ?)");
									$stmt->execute(array($ii['user_id'],'2',USER_ID,$iiii['id'],time(),'0'));
									if(engine::getuserdata($ii['user_id'], "NOT_ANSWER") == 1 && engine::getuserdata($ii['user_id'], "ONLINE") == 0) {
										$user_id_link = engine::get_user_name($ii['user_id']);
										$theme = get_text(456,1).' '.SITE_NAME.' – '.SITE_DESCRIPTION;
										$body = get_text(83,1).' '.$user_id_link.',<br><br>'.get_text(242,1).' <a href="http://'.SITE_DOMAIN.'/'.USER_USERNAME.'">'.USER_USERNAME.'</a> '.get_text(451,1).' - "'.$question.'". <a href="http://'.SITE_DOMAIN.'/q/'.$url.'">'.get_text(252,1).'</a> '.get_text(453,1).'.<br>'.get_text(77,1).' '.SITE_NAME.'<br><br>'.get_text(78,1).',<br>'.SITE_NAME.' - '.SITE_DESCRIPTION.'<br><br><small>'.get_text(79,1).'.</small>';
										$to = engine::getuserdata($ii['user_id'],"EMAIL");
										engine::send_email($to,$theme,$body);
									}
								}
							}
						}
						$_SESSION['msg'] = 1;
						engine::headerin('q/'.$url);
						exit;
					}
					else {
						$_SESSION['msg'] = 0;
						engine::headerin('q/'.$url);
						exit;
					}
				}
				else {
					$_SESSION['msg'] = 0;
					engine::headerin('q/'.$url);
					exit;
				}
			}
			else {
				$_SESSION['msg'] = 0;
				engine::headerin('q/'.$url);
				exit;
			}
		}
		
		public function checkname($username) {

			/* Function for checking name at registration */

			$max_lenght = 20;
			$min_lenght = 4;
			if(strlen($username)<$min_lenght) {
				$json['error'] = get_text(69,1);
			}
			else if(strlen($username)>$max_lenght) {
				$json['error'] = get_text(70,1);
			}
			else if($username == "install" || $username == "config" || $username == "help" || $username == "privacy" || 
					$username == "uploads" ||  $username == "core" ||  $username == "images" ||  $username == "exit" ||  $username == "login" ||  $username == SITE_NAME || $username == "recovery" || $username == "settings" || $username == "search" || $username == "options" || $username == "categories" || $username == "category" || $username == "remove" || $username == "admin" || $username == "administration" || $username == "delete" || $username == "people" || $username == "change" || $username == "ajax" || $username == "site" || $username == "register") {
				$json['error'] = get_text(71,1);
			}
			else if(engine::usernameexists($username)==get_text(65,1)) {
				$json['error'] = get_text(72,1);
			}
			else if(strstr($username, ' ')!=false || strstr($username, ',')!=false || strstr($username, '.')!=false || strstr($username, '@')!= false) {
				$json['error'] = get_text(73,1);
			}
			else {
				$json['success'] = NULL;
			}
			$encoded = json_encode($json);
			die($encoded);
		}

		public function emailexists($email) {

			/* Function for checking email availability at registration */

			$stmt = $this->conn->prepare("SELECT email FROM users WHERE email = ?");
			$stmt->execute(array($email));
			if($stmt->rowCount()>0) return 1;
			else return 0;
		}

		public function checkemail($email) {

			/* Function for checking email validation at registration */

			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			    $json['error'] = 'Email is not valid';
			}
			else if(engine::emailexists($email) != 0) {
				$json['error'] = 'This email is already used';
			}
			else {
				$json['success'] = NULL;
			}
			$encoded = json_encode($json);
			die($encoded);
		}

		public function createuseraccount($username, $password, $name, $email, $country) {

			/* Function used in creating account */

			$pass = $password;
			$username = strtolower(strip_tags($username));
			$password = strip_tags($password);
			$name = ucwords(strip_tags($name));
			$email = strip_tags($email);
			$country = strip_tags($country);
			if(!empty($username) && !empty($password) && !empty($name) && !empty($email) && preg_match("/^[a-zA-Z0-9]+$/", $username)==1) { 
				$password = engine::crypts(engine::crypts(engine::crypts($password, "MD5", 0), 2, 0), "SHA1", 0);  
				$rank = 0; 
				$website = 'http://'.SITE_DOMAIN.'/'.$username; 
				$null = '';
				$photo = "photo_default.png";
				if(SIGNUP_CONFIRMATION==1) {
					$status = 10; 
				}
				else {
					$status = 1;
				}
				$ip = USER_IP;  
				$reg_date = time();
				if(engine::usernameexists($username) == get_text(65,1)) {
					$_SESSION['msg'] = 2;
					engine::headerin("site/signup");
					exit;
				}
				if(strlen($username)<4) {
					$_SESSION['msg'] = 2;
					engine::headerin("site/signup");
					exit;
				}
				if($username == "install" || $username == "config" || $username == "help" || $username == "privacy" || 
					$username == "uploads" ||  $username == "core" ||  $username == "images" ||  $username == "exit" ||  $username == "login" ||  $username == SITE_NAME || $username == "recovery" || $username == "settings" || $username == "search" || $username == "options" || $username == "categories" || $username == "category" || $username == "remove" || $username == "admin" || $username == "administration" || $username == "delete" || $username == "people" || $username == "change" || $username == "ajax" || $username == "site" || $username == "register") {
					$_SESSION['msg'] = 2;
					engine::headerin("site/signup");
					exit;
				}
				if(strstr($username, ' ')!=false || strstr($username, ',')!=false || strstr($username, '.')!=false || strstr($username, '@')!= false) {
					$_SESSION['msg'] = 2;
					engine::headerin("site/signup");
					exit;
				}
				if(strlen($email)<5) {
					$_SESSION['msg'] = 1;
					engine::headerin("site/signup");
					exit;
				}
				if(engine::emailalreadyexists($email) == get_text(67,1)) {
					$_SESSION['msg'] = 1;
					engine::headerin("site/signup");
					exit;
				}
				if(strlen($password)<3) {
					$_SESSION['msg'] = 1;
					engine::headerin("site/signup");
					exit;
				}
				$mysqlmessage["user"] = "INSERT INTO users ";
				$mysqlmessage["user"] .= "(username, password, name, rank, verified, email, country, website, bio, photo, status, ip, reg_date, visit) ";
				$mysqlmessage["user"] .= "VALUES ";
				$mysqlmessage["user"] .= "(?, ?, ?, '0', '0', ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$stmt = $this->conn->prepare($mysqlmessage["user"]);
				$stmt->execute(array($username, $password, $name, $email, $country, $website, $null, $photo, $status, $ip, $reg_date, $reg_date));
				if($stmt->rowCount()!=0 && SESSION_STATUS!=true) {
					if(SIGNUP_CONFIRMATION==1) {
						$_SESSION['msg'] = 11;
						engine::send_confirmation($username);
						engine::headerin("site/signup");
						exit;
					}
					else {
						engine::checkcookies($username, $pass);
					}
				}
			}
		}
		
		public function createuseraccountsocial($username, $email, $network, $id, $name, $photo, $country) {

			/* Function for creating account via Facebook or Twitter */

			$username = strtolower(strip_tags($username));
			$name = ucwords($name);
			$email = strip_tags($email);
			$country = strip_tags($country);
			if(!empty($username) && preg_match("/^[a-zA-Z0-9]+$/", $username)==1) {
				$rank = 0;  
				$website = 'http://'.SITE_DOMAIN.'/'.$username; 
				$null = '';
				$status = 1; 
				$ip = USER_IP;  
				$reg_date = time();
				if(engine::usernameexists($username) == get_text(65,1)) {
					$_SESSION['msg'] = 2;
					engine::headerin("site/signup");
					exit;
				}
				if(strlen($username)<4) {
					$_SESSION['msg'] = 2;
					engine::headerin("site/signup");
					exit;
				}
				if($username == "install" || $username == "config" || $username == "help" || $username == "privacy" || 
					$username == "uploads" ||  $username == "core" ||  $username == "images" ||  $username == "exit" ||  $username == "login" ||  $username == SITE_NAME || $username == "recovery" || $username == "settings" || $username == "search" || $username == "options" || $username == "categories" || $username == "category" || $username == "remove" || $username == "admin" || $username == "administration" || $username == "delete" || $username == "people" || $username == "change" || $username == "ajax" || $username == "site" || $username == "register") {
					$_SESSION['msg'] = 2;
					engine::headerin("site/signup");
					exit;
				}
				if(strstr($username, ' ')!=false || strstr($username, ',')!=false || strstr($username, '.')!=false || strstr($username, '@')!= false) {
					$_SESSION['msg'] = 2;
					engine::headerin("site/signup");
					exit;
				}
				if(strlen($email)<5) {
					$_SESSION['msg'] = 1;
					engine::headerin("site/signup");
					exit;
				}
				if(engine::emailalreadyexists($email) == get_text(67,1)) {
					$_SESSION['msg'] = 1;
					engine::headerin("site/signup");
					exit;
				}
				if($photo!="") {
					if(substr($photo,0,8)=='https://') {
						$photo = substr($photo,8);
						$photo = "http://".$photo;
					}
					$photo_crypt_name = engine::crypts($username.time(), 1, 0).".jpg";
					$root = realpath($_SERVER["DOCUMENT_ROOT"]);
					$directory = '../../media/images/users/'.$photo_crypt_name;	
					$image = file_get_contents($photo);
					file_put_contents($directory, $image);
					$photo = $photo_crypt_name;
				}
				else {
					$photo = "photo_default.png";
				}
				$mysqlmessage["user"] = "INSERT INTO users ";
				$mysqlmessage["user"] .= "(username, password, name, rank, verified, email, country, website, bio, photo, status, ip, reg_date, visit, social) ";
				$mysqlmessage["user"] .= "VALUES ";
				$mysqlmessage["user"] .= "(?, '', ?, '0', '0', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$stmt = $this->conn->prepare($mysqlmessage["user"]);
				$stmt->execute(array($username, $name, $email, $country, $website, $null, $photo, $status, $ip, $reg_date, $reg_date, $id));
				if($stmt->rowCount()!=0) {
					if(SESSION_STATUS!=true) {
						unset($_SESSION['regnetwork']);
						unset($_SESSION['regid']);
						unset($_SESSION['regfirst_name']);
						unset($_SESSION['reglast_name']);
						unset($_SESSION['regbdate']);
						unset($_SESSION['regphoto_big']);
						unset($_SESSION['regcountry']);
						unset($_SESSION['regcity']);
						engine::checkcookies("", "", $network, $id);
					}
				}
			}
		}
		
		public function category_questions($id) {

			/* Show number of questions in current category */

			if($id) {
				$stmt = $this->conn->prepare("SELECT id FROM questions WHERE category LIKE '%".$id." %'");
				$stmt->execute();
				return $stmt->rowCount();
			}
			else {
				return 0;
			}
		}
		
		public function emailalreadyexists($email) {

			/* Function for checking if email exists */

			if(isset($email)===true) {
				$stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
				$stmt->execute(array($email));
				if($stmt->rowCount()!=0) {
					return get_text(67,1);
				}
				else {
					return get_text(68,1);
				}
			}
		}
		
		public function usernameexists($username) {

			/* Function for checking if username already exists */

			if(strlen($username)>3) {
				$stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
				$stmt->execute(array($username));
				if($stmt->rowCount()!=0) {
					return get_text(65,1);
				}
				else {
					return get_text(66,1);
				}
			}
		}

		public function saveview($q_id) {

			/* Save question view, when it is opened */

			$stmt = $this->conn->prepare("UPDATE questions SET views = views+1 WHERE id = ?");
			$stmt->execute(array($q_id));
		}
		
		public function savevisit() {

			/* Save last user activity time */

			$time = time();
			$stmt = $this->conn->prepare("UPDATE users SET visit = ? WHERE id = ?");
			$stmt->execute(array($time,USER_ID));
		}	

		public function toppeople($type) {

			/* Function to get top users by points */

	        $message = "";
	        $nr = 50;
			$i = 1;
	        $json = array('response' => 'success');
	        header('Content-type: application/json');
	        $stmt = $this->conn->prepare("SELECT id, to_user, COUNT(*) AS total FROM points_log GROUP BY to_user ORDER BY total DESC LIMIT ?");
			$stmt->bindValue(1, $nr, PDO::PARAM_INT);
			$stmt->execute();
			$text = get_text(39,1);
	        if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$message .= "<div class='top-people'><h3>#".$i."</h3>";
					$username = $ii['to_user'];
					$userlink = engine::get_user_name($username);
					$count = $ii['total'];
	                $photo = engine::getuserdata($username, "PHOTO");
					if(file_exists('../../media/images/users/'.$photo.'')) {
	                    $image = "<a href='http://".SITE_DOMAIN."/".$userlink."'><div class='img imgLiquid' style='width:60px; height:60px'><img src='http://".SITE_DOMAIN."/media/images/users/".$photo."'></div></a>";
	                } 
					else  {
	                    $image = "<a href='http://".SITE_DOMAIN."/".$userlink."'><div class='img imgLiquid' style='width:60px; height:60px'><img src='http://".SITE_DOMAIN."/media/images/users/photo_default.png'></div></a>";
	                }
					$status = engine::getuserdata($username, "WHAT_ASK");
					$message .= "<div class='top-people-image'>".$image."</div>";
					$message .= "<div class='top-people-description'><a href='http://".SITE_DOMAIN."/".$userlink."'>".$userlink."</a><br>".$text.": <b>".$count."</b></div>";
					$message .= "</div>";
					$i++;
				}
	        }
	        $json = array("response" => "$message");
	        echo json_encode($json);
	    }

	    public function verified($user,$type) {

	    	/* Function for add for user verified badge */

			$json = array ('response'=>'success');
			if(USER_RANK==1) {	
				if($type==0 || $type==1) {
					$stmt = $this->conn->prepare("UPDATE users SET verified = ? where username = ?");
					$stmt->execute(array($type,$user));
					header('Content-type: application/json'); 
					$json = array("done" => "yes, verified done");
					echo json_encode($json);
				}
				else {
					header('Content-type: application/json'); 
					$json = array("done" => "Value 0 or 1!");
					echo json_encode($json);
				}
			}
			else {
				header('Content-type: application/json'); 
				$json = array("done" => "no");
				echo json_encode($json);
			}
		}

	    public function checkquestion($question) {

	    	/* Function for checking if question exists */

	    	$message="";
	    	$question = str_replace("?", "", $question);
	    	$question = str_replace("'", "", $question);
	        $json=array('response' => 'success');
	        header('Content-type: application/json');
	        $words = explode(" ", $question);
			if(!is_null($words)) {
				foreach($words as $search) {
			   		if(strlen($search)>2) {
			     		$descriptionQuery[] = "$search";
			     	}
			   	}
				$condition = "SELECT url, question, MATCH (question) AGAINST ('".implode(" ", $descriptionQuery)."') AS score FROM questions WHERE MATCH (question) AGAINST ('".implode(" ", $descriptionQuery)."' IN BOOLEAN MODE) ORDER BY score DESC LIMIT 5";
				$stmt = $this->conn->prepare($condition);
				$stmt->execute();
				if($stmt->rowCount()!=0) {
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
						$message .= "<div class='question-ask-box'>";
						$message .= "<a href='q/".$ii['url']."'>".$ii['question']."</a>";
						$message .= "</div>";
					}
				}
				/*
			   	foreach($words as $search) {
			   		if(strlen($search)>2) {
			     		$descriptionQuery[] = " question LIKE '%{$search}%' ";
			     	}
			   	}
			   	$condition = "WHERE " . implode(" OR ", $descriptionQuery)." ORDER BY views DESC, time DESC LIMIT 6";
			   	$stmt = $this->conn->prepare("SELECT * FROM questions ".$condition);
				$stmt->execute();
				if($stmt->rowCount()!=0) {
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
						$message .= "<div class='question-ask-box'>";
						$message .= "<a href='q/".$ii['url']."' target='_blank'>".$ii['question']."</a>";
						$message .= "</div>";
					}
				}*/
			}
	        $json = array("response" => "$message");
	        echo json_encode($json);
	    }

	    public function search($query) {

	    	/* Function for search */

	    	$message="";
	        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE name LIKE '%".$query."%' OR description LIKE '%".$query."%' ORDER BY followers DESC LIMIT 10");
			$stmt->execute();
			if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$message .= "<a href='category/".$ii['url']."'><div class='result-search'>";
					$message .= get_text(241,1).": <b>".$ii['name'].'</b><br><small>'.$ii['description'].'</small>';
					$message .= "</div></a>";
				}
			}
			$stmt = $this->conn->prepare("SELECT * FROM users WHERE name LIKE '%".$query."%' OR username LIKE '%".$query."%' ORDER BY visit DESC LIMIT 20");
			$stmt->execute();
			if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$message .= "<a href='".$ii['username']."'><div class='result-search'>";
					$message .= get_text(242,1).": <b>".$ii['username']."</b> (".$ii['name'].")";
					$message .= "</div></a>";
				}
			}
			//$stmt = $this->conn->prepare("SELECT url, question, description, MATCH (question) AGAINST ('".$query."') AS score FROM questions WHERE MATCH (question) AGAINST ('".$query."' IN BOOLEAN MODE) ORDER BY score DESC LIMIT 30");
			$stmt = $this->conn->prepare("SELECT * FROM questions WHERE question LIKE '%".$query."%' LIMIT 30");
			$stmt->execute();
			if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$message .= "<a href='q/".$ii['url']."'><div class='result-search'>";
					$message .= get_text(243,1).": <b>".$ii['question'].'</b><br><small>'.$ii['description'].'</small>';
					$message .= "</div></a>";
				}
			}
			/*$stmt = $this->conn->prepare("SELECT * FROM answers WHERE answer LIKE '%".$query."%' ORDER BY upvotes DESC LIMIT 30");
			$stmt->execute();
			if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$stmt = $this->conn->prepare("SELECT url, question FROM questions WHERE id = ?");
					$stmt->execute(array($ii['question_id']));
					$iii = $stmt->fetch(PDO::FETCH_ASSOC);
					$message .= "<a href='q/".$iii['url']."' target='_blank'><div class='result-search'>";
					$message .= get_text(164,1).": <b>".$ii['answer']."</b><br><small>".get_text(244,1)." ".$iii['question'].'</small>';
					$message .= "</div></a>";
				}
			}*/
	        return $message;
	    }

	    public function ajax_search($query) {

	    	/* Function for ajax search - live search */

	    	$message="";
	    	$query = str_replace("?", "", $query);
	    	$query = str_replace("'", "", $query);
	        header('Content-type: application/json');
	        $stmt = $this->conn->prepare("SELECT name, url FROM categories WHERE name LIKE '%".$query."%' LIMIT 5");
			$stmt->execute();
			if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$message .= "<a href='category/".$ii['url']."'><div class='result-ajax'>";
					$message .= get_text(241,1).": ".$ii['name'];
					$message .= "</div></a>";
				}
			}
			$stmt = $this->conn->prepare("SELECT name, username FROM users WHERE name LIKE '%".$query."%' OR username LIKE '%".$query."%' LIMIT 5");
			$stmt->execute();
			if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$message .= "<a href='".$ii['username']."'><div class='result-ajax'>";
					$message .= get_text(242,1).": ".$ii['username']." (".$ii['name'].")";
					$message .= "</div></a>";
				}
			}
			$words = explode(" ", $query);
			foreach($words as $search) {
			   	if(strlen($search)>2) {
			     	$descriptionQuery[] = "$search";
			    }
			}
		
			$stmt = $this->conn->prepare("SELECT * FROM questions WHERE question LIKE '%".$query."%' LIMIT 10");
			//$stmt = $this->conn->prepare("SELECT IF(question='$query' OR question LIKE '$query%',1,0) AS exact, url, question FROM questions WHERE question RLIKE '".implode("|", $descriptionQuery)."' order by exact LIMIT 5");
			$stmt->execute();
			if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$message .= "<a href='q/".$ii['url']."'><div class='result-ajax'>";
					$message .= get_text(243,1).": ".$ii['question'];
					$message .= "</div></a>";
				}
			}
	        $json = array("response" => "$message");
	        echo json_encode($json);
	    }

	    public function get_categories($question) {

	    	/* Get categories function at asking question */

	    	$message="";
	    	$question = str_replace("?", "", $question);
	    	$question = str_replace("'", "", $question);
	        $json=array('response' => 'success');
	        header('Content-type: application/json');
	        $words = explode(" ", $question);
			if(!is_null($words)) {
			   	foreach($words as $search) {
			   		if(strlen($search)>2) {
			     		$descriptionQuery[] = " name LIKE '%{$search}%' ";
			     	}
			   	}
			   	$condition = "WHERE " . implode(" OR ", $descriptionQuery)." LIMIT 6";
			   	$stmt = $this->conn->prepare("SELECT * FROM categories ".$condition);
				$stmt->execute();
				if($stmt->rowCount()!=0) {
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
						$message .= "<div class='question-category-box'>";
						$message .= "<label><input type='checkbox' id='cat-".$ii['id']."' name='categories[]' value='".$ii['id']."' checked='yes'><span>".$ii['name']." - ".$ii['followers']." ".ucfirst(get_text(118,1))."</span></label>";
						$message .= "</div>";
					}
				}
			}
	        $json = array("response" => "$message");
	        echo json_encode($json);
	    }

	    public function get_categories_by_id($categories) {

	    	/* Get categories function at asking question */

	    	$message="";
	        $json=array('response' => 'success');
	        header('Content-type: application/json');
	        $words = explode(" ", $categories);
			if(!is_null($words)) {
			   	foreach($words as $search) {
			   		if($search>0) {
			     		$descriptionQuery[] = " id=$search";
			     	}
			   	}
			   	$condition = "WHERE " . implode(" OR ", $descriptionQuery);
			   	$stmt = $this->conn->prepare("SELECT * FROM categories ".$condition);
				$stmt->execute();
				if($stmt->rowCount()!=0) {
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
						$message .= "<div class='question-category-box'>";
						$message .= "<label><input type='checkbox' id='cat-".$ii['id']."' name='categories[]' value='".$ii['id']."' checked='yes'><span>".$ii['name']." - ".$ii['followers']." ".ucfirst(get_text(118,1))."</span></label>";
						$message .= "</div>";
					}
				}
			}
	        $json = array("response" => "$message");
	        echo json_encode($json);
	    }

	    public function search_category($query) {

	    	/* Function for getting category in ask modal via search */

	    	$message="<ul id='results'>";
	    	if(strlen($query)>1) {
			   	$stmt = $this->conn->prepare("SELECT * FROM categories WHERE name LIKE '%".$query."%'");
				$stmt->execute();
				if($stmt->rowCount()!=0) {
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
						$message .= "<li onclick='add_category_to_question(".$ii['id'].",\"".$ii['name']."\",".$ii['followers']."); return false;'>".$ii['name']." - ".$ii['followers']." ".ucfirst(get_text(244,1))."</li>";
					}
				}
			}
	        echo $message."</ul>";
	    }

	    public function select_category() {

	    	/* Function for getting category in ask modal via select box */

	    	$message = "<select id='results' onchange='add_category_to_question_select(this.options[this.selectedIndex].getAttribute(\"id\"),this.options[this.selectedIndex].getAttribute(\"name\"),this.options[this.selectedIndex].getAttribute(\"followers\"))'>";
	    	$message .= "<option selected>".get_text(253,1)."</option>";
	    	$stmt = $this->conn->prepare("SELECT * FROM categories ORDER BY followers DESC");
			$stmt->execute();
			if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$message .= "<option value='".$ii['id']."' id='".$ii['id']."' name='".$ii['name']."' followers='".$ii['followers']."'>".$ii['name']." - ".$ii['followers']." ".ucfirst(get_text(118,1))."</option>";
				}
			}
	        $message.="</select>";
	        echo $message;
	    }

	    public function select_category_p() {

	    	/* Function for getting category in ask modal via select box when user edit question */

	    	$message = "<select id='results' onchange='add_category_to_question_select_p(this.options[this.selectedIndex].getAttribute(\"id\"),this.options[this.selectedIndex].getAttribute(\"name\"),this.options[this.selectedIndex].getAttribute(\"followers\"))'>";
	    	$message .= "<option selected>".get_text(253,1)."</option>";
	    	$stmt = $this->conn->prepare("SELECT * FROM categories ORDER BY followers DESC");
			$stmt->execute();
			if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$message .= "<option value='".$ii['id']."' id='".$ii['id']."' name='".$ii['name']."' followers='".$ii['followers']."'>".$ii['name']." - ".$ii['followers']." ".ucfirst(get_text(118,1))."</option>";
				}
			}
	        $message.="</select>";
	        echo $message;
	    }

	    public function all_categories() {

	    	/* Function for getting all categories for categories page */

	    	$message = "";
	    	$stmt = $this->conn->prepare("SELECT * FROM categories ORDER BY name ASC");
			$stmt->execute();
			if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$message .= "<div class='category_item'>";
					$message .= "<div class='category_item_image'><a href='category/".$ii['url']."'><div class='img imgLiquid' style='width:80px; height:80px'><img src='media/images/categories/".$ii['image']."'></div></a></div>";
					$message .= "<div class='category_item_desc'>";
					$message .= "<a href='category/".$ii['url']."'>".$ii['name']."</a>";
					$message .= "<br><small>".$ii['description']."</small>";
					$message .= "<div class='ActionBar TopicPage'>";
					$message .= engine::get_follow_button_in_category($ii['id'],$ii["followers"]);
					$message .= "</div>";
					$message .= "</div>";
					$message .= "</div>";
				}
			}
	        return $message;
	    }

	    public function getlikes($id,$user,$type="1") {

 			/* Function to get question likes and comments */

			$message = '<div class="primary_item">';
			$stmt = $this->conn->prepare("SELECT id FROM likes WHERE question_id = ?");
			$stmt->execute(array($id));
			$total = $stmt->rowCount();
			if($total>0) {
				$stmt = $this->conn->prepare("SELECT id FROM likes WHERE question_id = ? AND user_id = ?");
				$stmt->execute(array($id,USER_ID));
				if($stmt->rowCount()!=0) {	
					$message .= '<span id="btn-'.$id.'" class="unfollow_b"';
					if(SESSION_STATUS==false || $user==USER_ID) {
						$message .= " style='cursor:default;background-color:#E2E2E2;'";
					}
					$message .= '><a class="vote_item_link add_upvote"';
					if(SESSION_STATUS!=false && $user!=USER_ID) {
						$message .= " onclick='like($id)'";
					}
					$message .= '><span class="unfollow_text"><i class="fa fa-spinner fa-spin fa-btn none"></i>'.get_text(245,1).'</span><span class="follow_text none">'.get_text(246,1).'</span><span class="count"><span class="countlike'.$id.'">'.$total.'</span></span></a></span>';
				}
				else {
					$message .= '<span id="btn-'.$id.'" class="follow_b"';
					if(SESSION_STATUS==false || $user==USER_ID) {
						$message .= " style='cursor:default;background-color:#E2E2E2;'";
					}
					$message .= '><a class="vote_item_link add_upvote"';
					if(SESSION_STATUS!=false && $user!=USER_ID) {
						$message .= " onclick='like($id)'";
					}
					$message .= '><span class="follow_text"><i class="fa fa-spinner fa-spin fa-btn none"></i>'.get_text(246,1).'</span><span class="unfollow_text none">'.get_text(245,1).'</span><span class="count"><span class="countlike'.$id.'">'.$total.'</span></a></span>';
				}
			}
			else {
				$message .= '<span id="btn-'.$id.'" class="follow_b"';
				if(SESSION_STATUS==false || $user==USER_ID) {
					$message .= " style='cursor:default;background-color:#E2E2E2;'";
				}
				$message .= '><a class="vote_item_link add_upvote"';
				if(SESSION_STATUS!=false && $user!=USER_ID) {
					$message .= " onclick='like($id)'";
				}
				$message .= '><span class="follow_text"><i class="fa fa-spinner fa-spin fa-btn none"></i>'.get_text(246,1).'</span><span class="unfollow_text none">'.get_text(245,1).'</span><span class="count"><span class="countlike'.$id.'">'.$total.'</span></a></span>';
			}
			$message .= '</div>';

			if($type!=0) {
				$stmt = $this->conn->prepare("SELECT id FROM answers WHERE question_id = ?");
				$stmt->execute(array($id));
				$answers = $stmt->rowCount();
				$stmt = $this->conn->prepare("SELECT url FROM questions WHERE id = ?");
				$stmt->execute(array($id));
				$iii = $stmt->fetch(PDO::FETCH_ASSOC);
				$message .= '<div class="primary_item">';
				$message .= '<span class="follow_b"><a class="vote_item_link add_upvote" href="q/'.$iii['url'].'">'.get_text(247,1).'<span class="count">'.$answers.'</span></a></span>';
				$message .= '</div>';
			}
			return $message;
		}

	    public function getupvotes($id,$user) {

	    	/* Function to get upvotes for answers */

			$message = '<div class="primary_item">';
			$stmt = $this->conn->prepare("SELECT upvotes FROM answers WHERE id = ?");
			$stmt->execute(array($id));
			$iii = $stmt->fetch(PDO::FETCH_ASSOC);
			$total = $iii['upvotes'];
			if($total>0) {
				$stmt = $this->conn->prepare("SELECT id FROM answers_likes WHERE answer_id = ? AND user_id = ?");
				$stmt->execute(array($id,USER_ID));
				if($stmt->rowCount()!=0) {	
					$message .= '<span id="btn-'.$id.'" class="unfollow_b"';
					if(SESSION_STATUS==false || $user==USER_ID) {
						$message .= " style='cursor:default;background-color:#E2E2E2;'";
					}
					$message .= '><a class="vote_item_link add_upvote"';
					if(SESSION_STATUS!=false && $user!=USER_ID) {
						$message .= " onclick='upvote($id)'";
					}
					$message .= '><i class="fa fa-spinner fa-spin fa-btn none"></i><span class="unfollow_text">'.get_text(249,1).'</span><span class="follow_text none">'.get_text(248,1).'</span><span class="count"><span class="countlike'.$id.'">'.$total.'</span></span></a></span>';
				}
				else {
					$message .= '<span id="btn-'.$id.'" class="follow_b"';
					if(SESSION_STATUS==false || $user==USER_ID) {
						$message .= " style='cursor:default;background-color:#E2E2E2;'";
					}
					$message .= '><a class="vote_item_link add_upvote"';
					if(SESSION_STATUS!=false && $user!=USER_ID) {
						$message .= " onclick='upvote($id)'";
					}
					$message .= '><i class="fa fa-spinner fa-spin fa-btn none"></i><span class="follow_text">'.get_text(248,1).'</span><span class="unfollow_text none">'.get_text(249,1).'</span><span class="count"><span class="countlike'.$id.'">'.$total.'</span></a></span>';
				}
			}
			else {
				$message .= '<span id="btn-'.$id.'" class="follow_b"';
				if(SESSION_STATUS==false || $user==USER_ID) {
					$message .= " style='cursor:default;background-color:#E2E2E2;'";
				}
				$message .= '><a class="vote_item_link add_upvote"';
				if(SESSION_STATUS!=false && $user!=USER_ID) {
					$message .= " onclick='upvote($id)'";
				}
				$message .= '><i class="fa fa-spinner fa-spin fa-btn none"></i><span class="follow_text">'.get_text(248,1).'</span><span class="unfollow_text none">'.get_text(249,1).'</span><span class="count"><span class="countlike'.$id.'">'.$total.'</span></a></span>';
			}
			$message .= '</div>';
			return $message;
		}

		public function number_user_topics($user) {

			/* Return number of user topics for profile page */

			if($user) {
				$stmt = $this->conn->prepare("SELECT id FROM follows WHERE user_id = ?");
				$stmt->execute(array($user));
				return $stmt->rowCount();
			}
		}

		public function number_user_questions($user) {

			/* Return number of user questions for profile page */

			if($user) {
				$stmt = $this->conn->prepare("SELECT id FROM questions WHERE user_id = ?");
				$stmt->execute(array($user));
				return $stmt->rowCount();
			}
		}

		public function number_user_answers($user) {

			/* Return number of user answers for profile page */

			if($user) {
				$stmt = $this->conn->prepare("SELECT id FROM answers WHERE user_id = ? GROUP BY question_id");
				$stmt->execute(array($user));
				return $stmt->rowCount();
			}
		}

		public function users_topics($user, $nr, $q) {

			/* Return topics, which are followed by user */

			$message = '';
			if($q=='') $q=100;
			$stmt = $this->conn->prepare("SELECT follows.id AS id, categories.image AS c_image, categories.id AS c_id, categories.url AS c_url, categories.name AS c_name, categories.description AS c_desc, categories.followers AS c_foll FROM follows, categories WHERE follows.user_id = ? AND follows.category_id = categories.id ORDER BY follows.time DESC LIMIT ?, ?");
			$stmt->bindValue(1, $user, PDO::PARAM_STR);
			$stmt->bindValue(2, intval($nr), PDO::PARAM_INT);
			$stmt->bindValue(3, intval($q), PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$id = $ii['id'];
					$photo = $ii['c_image'];
					$category_id = $ii['c_id'];
					$category_link = $ii['c_url'];
					$category_name = $ii['c_name'];
					$category_desc = $ii['c_desc'];
					$category_followers = $ii['c_foll'];
					$message .= '<div class="profile_topic" id="follow_topic_'.$id.'">';
					if (file_exists('../../media/images/categories/'.$photo)) {
						$message .= '<a href="http://'.SITE_DOMAIN.'/category/'.$category_link.'">';
						$message .= '<div class="img imgLiquid" style="width:50px; height:50px; margin:5px; float: left">';
						$message .= '<img src="media/images/categories/'.$photo.'" class="topic-photo">';
						$message .= '</div>';
						$message .= "</a>";
					}
					$message .= '<a href="http://'.SITE_DOMAIN.'/category/'.$category_link.'">';
					$message .= '<b>'.$category_name.'</b>';  
					$message .= "</a>";
					$message .= '<p>'.$category_desc.'</p>';
					if(USER_ID) {
						$stmt = $this->conn->prepare("SELECT id FROM follows WHERE user_id = ? and category_id = ?");
			            $stmt->execute(array(USER_ID,$category_id));
			            if($stmt->rowCount()>0) {
			            	$message .= "<a id='follow-btn-".$category_id."' class='unfollow_b' onclick='unfollow(".$category_id.");'><span class='uu'>".get_text(237,1)."</span><span class='ff' style='display:none'>".get_text(238,1)."</span><span class='count'>".$category_followers."</span></a>";
			       	    } else {
			                $message .= "<a id='follow-btn-".$category_id."' class='follow_b' onclick='follow(".$category_id.");'><span class='ff'>".get_text(238,1)."</span><span class='uu' style='display:none'>".get_text(237,1)."</span><span class='count'>".$category_followers."</span></a>";
			            }
			        }
					$message .= '</div>';
				}
			}
			return $message;
		}

		public function follow($category_id) {

			/* Function for follow topic */

			if(USER_ID) {
				$stmt = $this->conn->prepare("SELECT id FROM follows WHERE category_id=? AND user_id=?");
				$stmt->execute(array($category_id,USER_ID));
				if($stmt->rowCount()==0) {
					$stmt = $this->conn->prepare("INSERT INTO follows (category_id, user_id, time) VALUES (?, ?, ?)");  
					$stmt->execute(array($category_id,USER_ID,time()));
					if($stmt->rowCount()!=0) {
						$stmt = $this->conn->prepare("UPDATE categories SET followers=followers+1 WHERE id=?");  
						$stmt->execute(array($category_id));
					}
				}
			}
		}

		public function unfollow($category_id) {

			/* Function for unfollow topic */

			if(USER_ID) {
				$stmt = $this->conn->prepare("DELETE FROM follows WHERE category_id=? AND user_id=?");  
				$stmt->execute(array($category_id,USER_ID));
				if($stmt->rowCount()!=0) {
					$stmt = $this->conn->prepare("UPDATE categories SET followers=followers-1 WHERE id=?");  
					$stmt->execute(array($category_id));
				}
			}
		}

		public function deleteuserphoto() {
			/* Function for deleting user photo and restore default image */
			$stmt = $this->conn->prepare("UPDATE users SET photo = 'photo_default.png' WHERE id = ?");
			$stmt->execute(array(USER_ID));
			engine::headerin('site/profile');
			exit;
		}

		public function get_points($user_id, $from_id, $limit=15) {

			/* Function for loading points summary */

			$message = "";
			$json = array ('response'=>'success');	
			header('Content-type: application/json');
	        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM points_log WHERE to_user=? AND id<?");
	        $stmt->execute(array($user_id,$from_id));
			$iii = $stmt->fetch(PDO::FETCH_ASSOC);
			$remain = $iii['total']-$limit;
			$stmt = $this->conn->prepare("SELECT * FROM points_log WHERE to_user=? AND id<? ORDER BY id DESC LIMIT ?");
	        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
	        $stmt->bindValue(2, $from_id, PDO::PARAM_INT);
			$stmt->bindValue(3, $limit, PDO::PARAM_INT);
			$stmt->execute();
	        if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$id = $ii['id'];
					$username = engine::get_user_name($user_id); 
					$time = get_time($ii['time']);
					$type = $ii['log_type'];
					$message .= '<div class="feed_item" id="pp_'.$id.'">';
					$message .= '<b>'.$username.'</b> '.get_text(250,1).' ';
					if($type==2) {
						$stmt = $this->conn->prepare("SELECT question_id FROM answers WHERE id = ?");
						$stmt->execute(array($ii['log_id']));
						$iii = $stmt->fetch(PDO::FETCH_ASSOC);
						$stmt = $this->conn->prepare("SELECT question, url FROM questions WHERE id = ?");
						$stmt->execute(array($iii['question_id']));
						$iii = $stmt->fetch(PDO::FETCH_ASSOC);
						$question = nl2br(txt2link(format_text($iii['question'])));
						$message .= get_text(251,1);
					}
					else {
						$stmt = $this->conn->prepare("SELECT question, url FROM questions WHERE id = ?");
						$stmt->execute(array($ii['log_id']));
						$iii = $stmt->fetch(PDO::FETCH_ASSOC);
						$question = nl2br(txt2link(format_text($iii['question'])));
						$message .= get_text(252,1);
					}
					$message .= ' <a href="q/'.$iii['url'].'">'.$iii['question'].'</a>';
					$message .= '<br><small>'.$time.'</small>';
					$message .= '</div>';
				}
			}
			$json = array("response" => $message,"last_id" => $id, "remain" => $remain);
			echo json_encode($json);	
		}

		public function get_page($id) {
			/* Return content of page with last edited time */
			$stmt = $this->conn->prepare("SELECT content, time FROM pages WHERE id = ?");
			$stmt->execute(array($id));
			$content = $stmt->fetch(PDO::FETCH_ASSOC);
			return $content;
		}

		public function getallcountries($selected_id) {
			/* Function for getting all countries into select input. Parameter - selected id in output list */
			$list = '';
			$stmt = $this->conn->prepare("SELECT * FROM countries");
			$stmt->execute();
			if($stmt->rowCount()!=0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					if($ii['id']==$selected_id) {
						$list .= '<option value="'.$ii['id'].'" selected="selected">'.$ii['country_name'].'</option>';
					}
					else {
						$list .= '<option value="'.$ii['id'].'">'.$ii['country_name'].'</option>';
					}
				}
			}
			return $list;
		}

		public function get_location_name($id) {
			/* Function for getting name of country/location by id */
			if($id>0) {
				$stmt = $this->conn->prepare("SELECT country_name FROM countries WHERE id = ?");
				$stmt->execute(array($id));
				$name = $stmt->fetch(PDO::FETCH_ASSOC);
				return $name['country_name'];
			}
			else {
				return false;
			}
		}
	}