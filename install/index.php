<?php
	error_reporting(1);
	ini_set('display_errors', 1);
	session_start();

	$get["step"] = isset($_GET["step"]) ? strtolower($_GET["step"]) : FALSE; 
	$get["action"] = isset($_GET["action"]) ? strtolower($_GET["action"]) : FALSE;
	$blocked["names"] = array('Js', 'Css', 'Core', 'Media', 'Install', 'Images');

	if(is_numeric($get["step"])==false) {
		echo '<script>window.location.href = "install/index.php?step=1";</script>';
	}

	function addhtml($mode) {
		if(empty($mode)) {
			return '';
		}
		$message = '';
		if(isset($mode)) {
			if($mode=="header") {
				$message .= "<html>";
				$message .= "<head>";
				$message .= "<title>QwikiA - Installation</title>";
				$message .= '<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic">';
				$message .= "<style type='text/css'>";
				$message .= "* {
								font-family: 'Roboto';
								border: 0;
								line-height: 25px;
								margin: 0;
								padding: 0
							}

							body {
								background-color: #EEE;
							}

							.navigation {
							    background-color: #273e6a;
    							padding: 10px;
							}

							.step-form {
								margin: 15px 25px;
    							border-top: solid 1px #e1e1e1;
    							padding-top: 15px;
    							text-align: center;
							}

							.step-title {
								display: block;
							    width: 100%;
							    text-align: center;
							    font-size: 20px;
							    padding: 15px 0;
							}

							.step-desc {
							    color: #777;
							    font-size: 15px;
							    display: block;
							    padding: 0px 30px;
							    line-height: 18px;
							}

							.step-desc i {
								font-size: 15px;
								line-height: 18px;
							}

							.label {
								font-size: 15px;
    							margin-right: 15px;
							}

							.input-text {
							    border-bottom: solid 1px #ccc;
    							padding: 0 10px;
    							width: 200px;
    							margin-bottom: 5px;
							}

							.step {
								display: block;
							    color: #fff;
							    background-color: #ff8a6e;
							    border-radius: 100%;
							    width: 60px;
							    height: 60px;
							    margin: 10px auto;
							    text-align: center;
							    line-height: 60px;
							    font-size: 30px;
							}

							.submit {
								padding: 5px 15px;
							    color: #FFF;
							    background-color: #39a1f4;
							    font-size: 14px;
							    display: block;
							    float: right;
							    margin-top: 20px;
							    cursor: pointer;
							}

							.submit:hover {
								opacity: 0.8;
							}

							.footer {
								font-size: 14px;
							    display: block;
							    float: left;
							    margin: 15px 25px;
							}

							.footer a {
								color: black;
								text-decoration: overline;
							}

							.error {
							    position: fixed;
							    top: 0;
							    left: 0;
							    background-color: #FF8080;
							    color: #fff;
							    padding: 10px 0;
							    width: 100%;
							    font-size: 16px;
							}

							#wrap {							
							    position: absolute;
							    width: 640px;
							    height: 480px;
							    top: 50%;
							    left: 50%;
							    margin-top: -240px;
							    margin-left: -320px;
							    box-shadow: 0 5px 10px rgba(0,0,0,0.19), 0 3px 3px rgba(0,0,0,0.23);
    							background-color: #fff;
							}";
				$message .= "</style>";
				$message .= "</head>";
				$message .= "<body>";
				$message .= '<div id="wrap">';
	  			$message .= '<div class="navigation">';
	      		$message .= '<p style="color:#fff">QwikiA</p>';
	      		$message .= '</div>';
			}
			else if($mode=="footer") {
				$message .= "<span class='footer'><a href='http://xandr.co' target='_blank'>Author</a>, QwikiA, 2016</span>";
				$message .= "</div>";
				$message .= "</body>";
				$message .= "</html>";
			}
			return $message;
		}
	}

	function updateWeb($site_name, $domain, $site_email) {
		$config = include '../core/config/config.php';
				$config['site_name'] = $site_name;
				$config['site_domain'] = $domain;
				$config['site_email'] = $site_email;
				$_SESSION['link'] = $domain;
		if(file_put_contents('../core/config/config.php', '<?php return ' . var_export($config, true) . ';')) {
			echo '<script>window.location.href = "install.php?step=4";</script>';
		}
		else {
			echo "Clear database from previous installation!";
		}
	}

	function getstructure() {
		include("../core/config/mysql.config.php");
		try {
			$db = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME.';charset=utf8', DB_USERNAME, DB_PASSWORD);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$stmt = $db->query("CREATE TABLE IF NOT EXISTS `answers` (
									  `id` int(11) NOT NULL AUTO_INCREMENT,
									  `question_id` int(11) NOT NULL,
									  `answer` text NOT NULL,
									  `status` enum('0','1','2') NOT NULL DEFAULT '0',
									  `user_id` int(11) NOT NULL,
									  `upvotes` int(11) NOT NULL DEFAULT '0',
									  `time` varchar(20) NOT NULL,
									  PRIMARY KEY (`id`)
									) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			$stmt = $db->query("CREATE TABLE IF NOT EXISTS `answers_likes` (
									  `id` int(11) NOT NULL AUTO_INCREMENT,
									  `answer_id` int(11) NOT NULL,
									  `user_id` int(11) NOT NULL,
									  `to_user` int(11) NOT NULL,
									  `time` varchar(20) NOT NULL,
									  PRIMARY KEY (`id`)
									) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			$stmt = $db->query("CREATE TABLE IF NOT EXISTS `categories` (
									  `id` int(11) NOT NULL AUTO_INCREMENT,
									  `url` varchar(250) NOT NULL,
									  `name` varchar(250) NOT NULL,
									  `image` varchar(250) NOT NULL,
									  `description` varchar(500) NOT NULL,
									  `followers` int(11) NOT NULL DEFAULT '0',
									  PRIMARY KEY (`id`)
									) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			$stmt = $db->query("CREATE TABLE IF NOT EXISTS `countries` (
									  `id` int(11) NOT NULL AUTO_INCREMENT,
									  `country_code` varchar(2) NOT NULL DEFAULT '',
									  `country_name` varchar(100) NOT NULL DEFAULT '',
									  PRIMARY KEY (`id`)
									) ENGINE=MyISAM AUTO_INCREMENT=243 DEFAULT CHARSET=utf8;");
			$stmt = $db->query("INSERT INTO `countries` (`id`, `country_code`, `country_name`) VALUES
										(1, 'US', 'United States'),
										(2, 'CA', 'Canada'),
										(3, 'AF', 'Afghanistan'),
										(4, 'AL', 'Albania'),
										(5, 'DZ', 'Algeria'),
										(6, 'DS', 'American Samoa'),
										(7, 'AD', 'Andorra'),
										(8, 'AO', 'Angola'),
										(9, 'AI', 'Anguilla'),
										(10, 'AQ', 'Antarctica'),
										(11, 'AG', 'Antigua and/or Barbuda'),
										(12, 'AR', 'Argentina'),
										(13, 'AM', 'Armenia'),
										(14, 'AW', 'Aruba'),
										(15, 'AU', 'Australia'),
										(16, 'AT', 'Austria'),
										(17, 'AZ', 'Azerbaijan'),
										(18, 'BS', 'Bahamas'),
										(19, 'BH', 'Bahrain'),
										(20, 'BD', 'Bangladesh'),
										(21, 'BB', 'Barbados'),
										(22, 'BY', 'Belarus'),
										(23, 'BE', 'Belgium'),
										(24, 'BZ', 'Belize'),
										(25, 'BJ', 'Benin'),
										(26, 'BM', 'Bermuda'),
										(27, 'BT', 'Bhutan'),
										(28, 'BO', 'Bolivia'),
										(29, 'BA', 'Bosnia and Herzegovina'),
										(30, 'BW', 'Botswana'),
										(31, 'BV', 'Bouvet Island'),
										(32, 'BR', 'Brazil'),
										(33, 'IO', 'British lndian Ocean Territory'),
										(34, 'BN', 'Brunei Darussalam'),
										(35, 'BG', 'Bulgaria'),
										(36, 'BF', 'Burkina Faso'),
										(37, 'BI', 'Burundi'),
										(38, 'KH', 'Cambodia'),
										(39, 'CM', 'Cameroon'),
										(40, 'CV', 'Cape Verde'),
										(41, 'KY', 'Cayman Islands'),
										(42, 'CF', 'Central African Republic'),
										(43, 'TD', 'Chad'),
										(44, 'CL', 'Chile'),
										(45, 'CN', 'China'),
										(46, 'CX', 'Christmas Island'),
										(47, 'CC', 'Cocos (Keeling) Islands'),
										(48, 'CO', 'Colombia'),
										(49, 'KM', 'Comoros'),
										(50, 'CG', 'Congo'),
										(51, 'CK', 'Cook Islands'),
										(52, 'CR', 'Costa Rica'),
										(53, 'HR', 'Croatia (Hrvatska)'),
										(54, 'CU', 'Cuba'),
										(55, 'CY', 'Cyprus'),
										(56, 'CZ', 'Czech Republic'),
										(57, 'DK', 'Denmark'),
										(58, 'DJ', 'Djibouti'),
										(59, 'DM', 'Dominica'),
										(60, 'DO', 'Dominican Republic'),
										(61, 'TP', 'East Timor'),
										(62, 'EC', 'Ecuador'),
										(63, 'EG', 'Egypt'),
										(64, 'SV', 'El Salvador'),
										(65, 'GQ', 'Equatorial Guinea'),
										(66, 'ER', 'Eritrea'),
										(67, 'EE', 'Estonia'),
										(68, 'ET', 'Ethiopia'),
										(69, 'FK', 'Falkland Islands (Malvinas)'),
										(70, 'FO', 'Faroe Islands'),
										(71, 'FJ', 'Fiji'),
										(72, 'FI', 'Finland'),
										(73, 'FR', 'France'),
										(74, 'FX', 'France, Metropolitan'),
										(75, 'GF', 'French Guiana'),
										(76, 'PF', 'French Polynesia'),
										(77, 'TF', 'French Southern Territories'),
										(78, 'GA', 'Gabon'),
										(79, 'GM', 'Gambia'),
										(80, 'GE', 'Georgia'),
										(81, 'DE', 'Germany'),
										(82, 'GH', 'Ghana'),
										(83, 'GI', 'Gibraltar'),
										(84, 'GR', 'Greece'),
										(85, 'GL', 'Greenland'),
										(86, 'GD', 'Grenada'),
										(87, 'GP', 'Guadeloupe'),
										(88, 'GU', 'Guam'),
										(89, 'GT', 'Guatemala'),
										(90, 'GN', 'Guinea'),
										(91, 'GW', 'Guinea-Bissau'),
										(92, 'GY', 'Guyana'),
										(93, 'HT', 'Haiti'),
										(94, 'HM', 'Heard and Mc Donald Islands'),
										(95, 'HN', 'Honduras'),
										(96, 'HK', 'Hong Kong'),
										(97, 'HU', 'Hungary'),
										(98, 'IS', 'Iceland'),
										(99, 'IN', 'India'),
										(100, 'ID', 'Indonesia'),
										(101, 'IR', 'Iran (Islamic Republic of)'),
										(102, 'IQ', 'Iraq'),
										(103, 'IE', 'Ireland'),
										(104, 'IL', 'Israel'),
										(105, 'IT', 'Italy'),
										(106, 'CI', 'Ivory Coast'),
										(107, 'JM', 'Jamaica'),
										(108, 'JP', 'Japan'),
										(109, 'JO', 'Jordan'),
										(110, 'KZ', 'Kazakhstan'),
										(111, 'KE', 'Kenya'),
										(112, 'KI', 'Kiribati'),
										(113, 'KP', 'Korea, Democratic People''s Republic of'),
										(114, 'KR', 'Korea, Republic of'),
										(115, 'XK', 'Kosovo'),
										(116, 'KW', 'Kuwait'),
										(117, 'KG', 'Kyrgyzstan'),
										(118, 'LA', 'Lao People''s Democratic Republic'),
										(119, 'LV', 'Latvia'),
										(120, 'LB', 'Lebanon'),
										(121, 'LS', 'Lesotho'),
										(122, 'LR', 'Liberia'),
										(123, 'LY', 'Libyan Arab Jamahiriya'),
										(124, 'LI', 'Liechtenstein'),
										(125, 'LT', 'Lithuania'),
										(126, 'LU', 'Luxembourg'),
										(127, 'MO', 'Macau'),
										(128, 'MK', 'Macedonia'),
										(129, 'MG', 'Madagascar'),
										(130, 'MW', 'Malawi'),
										(131, 'MY', 'Malaysia'),
										(132, 'MV', 'Maldives'),
										(133, 'ML', 'Mali'),
										(134, 'MT', 'Malta'),
										(135, 'MH', 'Marshall Islands'),
										(136, 'MQ', 'Martinique'),
										(137, 'MR', 'Mauritania'),
										(138, 'MU', 'Mauritius'),
										(139, 'TY', 'Mayotte'),
										(140, 'MX', 'Mexico'),
										(141, 'FM', 'Micronesia, Federated States of'),
										(142, 'MD', 'Moldova, Republic of'),
										(143, 'MC', 'Monaco'),
										(144, 'MN', 'Mongolia'),
										(145, 'ME', 'Montenegro'),
										(146, 'MS', 'Montserrat'),
										(147, 'MA', 'Morocco'),
										(148, 'MZ', 'Mozambique'),
										(149, 'MM', 'Myanmar'),
										(150, 'NA', 'Namibia'),
										(151, 'NR', 'Nauru'),
										(152, 'NP', 'Nepal'),
										(153, 'NL', 'Netherlands'),
										(154, 'AN', 'Netherlands Antilles'),
										(155, 'NC', 'New Caledonia'),
										(156, 'NZ', 'New Zealand'),
										(157, 'NI', 'Nicaragua'),
										(158, 'NE', 'Niger'),
										(159, 'NG', 'Nigeria'),
										(160, 'NU', 'Niue'),
										(161, 'NF', 'Norfork Island'),
										(162, 'MP', 'Northern Mariana Islands'),
										(163, 'NO', 'Norway'),
										(164, 'OM', 'Oman'),
										(165, 'PK', 'Pakistan'),
										(166, 'PW', 'Palau'),
										(167, 'PA', 'Panama'),
										(168, 'PG', 'Papua New Guinea'),
										(169, 'PY', 'Paraguay'),
										(170, 'PE', 'Peru'),
										(171, 'PH', 'Philippines'),
										(172, 'PN', 'Pitcairn'),
										(173, 'PL', 'Poland'),
										(174, 'PT', 'Portugal'),
										(175, 'PR', 'Puerto Rico'),
										(176, 'QA', 'Qatar'),
										(177, 'RE', 'Reunion'),
										(178, 'RO', 'Romania'),
										(179, 'RU', 'Russian Federation'),
										(180, 'RW', 'Rwanda'),
										(181, 'KN', 'Saint Kitts and Nevis'),
										(182, 'LC', 'Saint Lucia'),
										(183, 'VC', 'Saint Vincent and the Grenadines'),
										(184, 'WS', 'Samoa'),
										(185, 'SM', 'San Marino'),
										(186, 'ST', 'Sao Tome and Principe'),
										(187, 'SA', 'Saudi Arabia'),
										(188, 'SN', 'Senegal'),
										(189, 'RS', 'Serbia'),
										(190, 'SC', 'Seychelles'),
										(191, 'SL', 'Sierra Leone'),
										(192, 'SG', 'Singapore'),
										(193, 'SK', 'Slovakia'),
										(194, 'SI', 'Slovenia'),
										(195, 'SB', 'Solomon Islands'),
										(196, 'SO', 'Somalia'),
										(197, 'ZA', 'South Africa'),
										(198, 'GS', 'South Georgia South Sandwich Islands'),
										(199, 'ES', 'Spain'),
										(200, 'LK', 'Sri Lanka'),
										(201, 'SH', 'St. Helena'),
										(202, 'PM', 'St. Pierre and Miquelon'),
										(203, 'SD', 'Sudan'),
										(204, 'SR', 'Suriname'),
										(205, 'SJ', 'Svalbarn and Jan Mayen Islands'),
										(206, 'SZ', 'Swaziland'),
										(207, 'SE', 'Sweden'),
										(208, 'CH', 'Switzerland'),
										(209, 'SY', 'Syrian Arab Republic'),
										(210, 'TW', 'Taiwan'),
										(211, 'TJ', 'Tajikistan'),
										(212, 'TZ', 'Tanzania, United Republic of'),
										(213, 'TH', 'Thailand'),
										(214, 'TG', 'Togo'),
										(215, 'TK', 'Tokelau'),
										(216, 'TO', 'Tonga'),
										(217, 'TT', 'Trinidad and Tobago'),
										(218, 'TN', 'Tunisia'),
										(219, 'TR', 'Turkey'),
										(220, 'TM', 'Turkmenistan'),
										(221, 'TC', 'Turks and Caicos Islands'),
										(222, 'TV', 'Tuvalu'),
										(223, 'UG', 'Uganda'),
										(224, 'UA', 'Ukraine'),
										(225, 'AE', 'United Arab Emirates'),
										(226, 'GB', 'United Kingdom'),
										(227, 'UM', 'United States minor outlying islands'),
										(228, 'UY', 'Uruguay'),
										(229, 'UZ', 'Uzbekistan'),
										(230, 'VU', 'Vanuatu'),
										(231, 'VA', 'Vatican City State'),
										(232, 'VE', 'Venezuela'),
										(233, 'VN', 'Vietnam'),
										(234, 'VG', 'Virgin Islands (British)'),
										(235, 'VI', 'Virgin Islands (U.S.)'),
										(236, 'WF', 'Wallis and Futuna Islands'),
										(237, 'EH', 'Western Sahara'),
										(238, 'YE', 'Yemen'),
										(239, 'YU', 'Yugoslavia'),
										(240, 'ZR', 'Zaire'),
										(241, 'ZM', 'Zambia'),
										(242, 'ZW', 'Zimbabwe');");
				$stmt = $db->query("CREATE TABLE IF NOT EXISTS `follows` (
									  `id` int(11) NOT NULL AUTO_INCREMENT,
									  `category_id` int(11) NOT NULL,
									  `user_id` int(11) NOT NULL,
									  `time` varchar(20) NOT NULL,
									  PRIMARY KEY (`id`)
									) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
				$stmt = $db->query("CREATE TABLE IF NOT EXISTS `likes` (
									  `id` int(11) NOT NULL AUTO_INCREMENT,
									  `question_id` int(11) NOT NULL,
									  `user_id` int(11) NOT NULL,
									  `to_user` int(11) NOT NULL,
									  `time` int(20) NOT NULL,
									  PRIMARY KEY (`id`)
									) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
				$stmt = $db->query("CREATE TABLE IF NOT EXISTS `notifications` (
									  `id` int(11) NOT NULL AUTO_INCREMENT,
									  `to_user` int(11) NOT NULL,
									  `type` int(11) NOT NULL,
									  `user_id` int(11) NOT NULL,
									  `n_id` int(11) NOT NULL,
									  `time` varchar(20) NOT NULL,
									  `viewed` enum('0','1') NOT NULL DEFAULT '0',
									  `info` text,
									  PRIMARY KEY (`id`)
									) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
				$stmt = $db->query("CREATE TABLE IF NOT EXISTS `pages` (
									  `id` int(11) NOT NULL AUTO_INCREMENT,
									  `content` text NOT NULL,
									  `time` varchar(20) NOT NULL,
									  PRIMARY KEY (`id`)
									) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;");
				$stmt = $db->query("INSERT INTO `pages` (`id`, `content`, `time`) VALUES
										(1, ' ', '".time()."'),
										(2, ' ', '".time()."'),
										(3, ' ', '".time()."'),
										(4, ' ', '".time()."'),
										(5, ' ', '".time()."');");
				$stmt = $db->query("CREATE TABLE IF NOT EXISTS `points_log` (
									  `id` int(11) NOT NULL AUTO_INCREMENT,
									  `log_type` enum('1','2') NOT NULL,
									  `log_id` int(11) NOT NULL,
									  `from_user` int(11) NOT NULL,
									  `to_user` int(11) NOT NULL,
									  `time` varchar(20) NOT NULL,
									  PRIMARY KEY (`id`)
									) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
				$stmt = $db->query("CREATE TABLE IF NOT EXISTS `questions` (
									  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
									  `category` varchar(100) NOT NULL,
									  `url` varchar(500) NOT NULL,
									  `user_id` int(11) NOT NULL,
									  `question` text NOT NULL,
									  `description` text NOT NULL,
									  `image` varchar(250) NOT NULL,
									  `status` set('0','1') NOT NULL,
									  `ip` varchar(30) NOT NULL,
									  `views` int(11) NOT NULL DEFAULT '0',
									  `answers` int(11) NOT NULL DEFAULT '0',
									  `time` varchar(30) NOT NULL,
									  `time_asked` varchar(30) DEFAULT NULL,
									  PRIMARY KEY (`id`),
									  KEY `id` (`id`),
									  FULLTEXT KEY `questions` (`question`)
									) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
				$stmt = $db->query("CREATE TABLE IF NOT EXISTS `reports` (
									  `id` int(11) NOT NULL AUTO_INCREMENT,
									  `type` enum('1','2') NOT NULL,
									  `r_id` int(10) unsigned NOT NULL,
									  `user_id` int(10) unsigned NOT NULL,
									  `message` varchar(520) NOT NULL,
									  `time` varchar(20) NOT NULL,
									  `ip` varchar(20) NOT NULL,
									  PRIMARY KEY (`id`)
									) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
				$stmt = $db->query("CREATE TABLE IF NOT EXISTS `users` (
									  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
									  `username` varchar(100) NOT NULL,
									  `password` text NOT NULL,
									  `name` varchar(400) NOT NULL,
									  `rank` enum('0','1') NOT NULL,
									  `verified` enum('0','1') NOT NULL,
									  `email` varchar(200) NOT NULL,
									  `country` int(11) NOT NULL,
									  `website` varchar(300) NOT NULL,
									  `bio` text NOT NULL,
									  `photo` text NOT NULL,
									  `status` enum('0','1','10') NOT NULL,
									  `ip` varchar(50) NOT NULL,
									  `reg_date` varchar(20) NOT NULL,
									  `visit` int(20) NOT NULL,
									  `social` varchar(100) NOT NULL,
									  `not_upvote` enum('0','1') NOT NULL DEFAULT '1',
									  `not_answer` enum('0','1') NOT NULL DEFAULT '1',
									  `not_follower` enum('0','1') NOT NULL DEFAULT '1',
									  `not_mention` enum('0','1') NOT NULL DEFAULT '1',
									  `not_system` enum('0','1') DEFAULT '1',
									  PRIMARY KEY (`id`),
									  UNIQUE KEY `username` (`username`),
									  KEY `id` (`id`)
									) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
			return 1;
		} catch (PDOException $e) {
		    //print "Error!: " . $e->getMessage() . "<br/>";
		   	//die();
		   	return 0;
		}
	}

	function crypts($encrypt_val, $mode, $brute_pass) {
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

	function createMyAdmin($username, $password, $name, $email) {
		include("../core/config/mysql.config.php");
		$db = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME.';charset=utf8', DB_USERNAME, DB_PASSWORD);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$username = strtolower($username);
		$pass = crypts(crypts(crypts($password, "MD5", 0), 2, 0), "SHA1", 0);
		$name = ucfirst($name);
		$IP_ADD = getenv("REMOTE_ADDR");
		$date = time();
		$stmt = $db->prepare("INSERT INTO users (username, password, name, rank, verified, email, country, website, bio, photo, status, ip, reg_date, visit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->execute(array($username, $pass, $name, '1', '1', $email, '', '', '', 'photo_default.png', '1', $IP_ADD, $date, $date));
		$stmt = $db->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
		$stmt->execute(array($username));
		if($stmt->rowCount()!=0) {
			echo '<script>window.location.href = "install.php?step=3";</script>';
		}
		else {
			$stmt = $db->prepare("DELETE FROM users WHERE 1=1");
			$stmt->execute();
			createMyAdmin($username, $password, $name, $email);
		}	
	}

	function createConfig($dbServer, $dbUser, $dbPass, $dbDatabase) {
		$filename = '../core/config/mysql.config.php';
		$save = "<?php
					define('DB_SERVER', '$dbServer');
					define('DB_USERNAME', '$dbUser');
					define('DB_PASSWORD', '$dbPass');
					define('DB_NAME', '$dbDatabase');
				?>";
		if(is_writable($filename)) {
			if(!$handle=fopen($filename, 'w')) {
				 echo "Cannot open file ($filename)";
				 exit;
			}
			if(fwrite($handle, $save)===FALSE) {
				echo "Cannot write to file ($filename)";
				exit;
			}
			fclose($handle);
		} 
		else {
			echo "The file $filename is not writable";
		}
	}

	echo addhtml("header");

	if($get["step"]>0 && $get["step"]<5) { ?>

	    <?php if($get["step"]==1) { ?>    

	       	<span class="step">1</span>
			<span class="step-title">MySql PDO connection</span>
			<span class="step-desc">	
			   	QwikiA use database for storing data. Please, create database using PhpMyAdmin or another alternative. Find on Google how to create it or contact hosting support. At this step will be needed to create database with user with all privilegies. <i>Check if database exist before submit form.</i> And if you start new installation - use new database.<br>
			</span>
			
			<?php
				$filename = '../core/config/mysql.config.php';
				if(is_writable($filename)) {
					echo '<div class="step-form">';
					if($get["action"]=="db") {
						if(!empty($_POST["db_server"]) || !empty($_POST["db_user"]) || !empty($_POST["db_pass"]) || !empty($_POST["db_database"])) {
							try {
								$db = new PDO('mysql:host='.$_POST["db_server"].';dbname='.$_POST["db_database"].';charset=utf8', $_POST["db_user"], $_POST["db_pass"]);
								$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
								$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
								createConfig($_POST["db_server"], $_POST["db_user"], $_POST["db_pass"], $_POST["db_database"]);
								if(getstructure()) {
									echo '<script>window.location.href = "index.php?step=2";</script>';
								}
								else {
									echo('<p class="error"><strong>Error!</strong>  - Unable to create database structure.</p>');
								}
							} catch (PDOException $e) {
						    	//print "Error!: " . $e->getMessage() . "<br/>";
						    	//die();
						    	echo('<p class="error"><strong>Error!</strong>  - Could not connect to PDO::MySql, please check your data is entered correctly.</p>');
							}
						}
						else { 
							echo('<p class="error"><strong>Error!</strong>  - All fields must be completed.</p>');
						}
					}
					echo '<form action="install?step=1&action=db" method="post">
							<label for="db_server" class="label">MySql hostname</label>
							<input name="db_server" id="db_server" type="text" class="input-text" value="localhost">
							<br>
						    <label for="db_user" class="label">MySql username</label>
						    <input name="db_user" id="db_user" type="text" class="input-text" value="root">
							<br>
						    <label for="db_pass" class="label">MySql password</label>
						    <input name="db_pass" id="db_pass" type="text" class="input-text" placeholder="Password">
						    <br>
						    <label for="db_database" class="label">Database name</label>
						    <input name="db_database" id="db_database" type="text" class="input-text" placeholder="Database name">
						    <br>
							<input type="submit" class="submit" value="Continue">
						</form></div>';
				} 
				else
				{
					echo '<p class="error"><strong>Attention!</strong> File "mysql.config" (core/config/mysql.config) is NOT avaible for editing! You can not continue installation! Change file permission for core folder, config folder and mysql.config file into 777.<br>After installation restore it like was at start.</p>';
					echo '<p class="error"><a href="http://www.dummies.com/how-to/content/how-to-change-file-permissions-using-filezilla-on-.html" target="_blank" style="color:#fff;text-decoration:none">How to change file permissions</a></p>';
				} 
			?>
	     
		<?php } else if($get["step"]==2) { ?>

			<span class="step">2</span>
			<span class="step-title">Admin account</span>
			<span class="step-desc">	
			   	Congratulations, connection to the database is successful. Now you need to create an administrator account. Please, remember username and password. With this account, you will have access to the administration panel. <i>Username must contain only letters or numbers!</i> <i style="color:red"> Password should contain only lowercase symbols at this step.</i><br>
			</span>
	        
	        <div class="step-form">
			<?php
			if($get["action"]=="adm") {
				if(!empty($_POST["adm_username"]) || !empty($_POST["adm_pass"]) || !empty($_POST["adm_name"]) || !empty($_POST["adm_email"])) {
					$admUser = ucfirst(strtolower($_POST["adm_username"]));
					if(!in_array($admUser, $blocked["names"])) {
						if(!strstr($_POST["adm_username"], ' ')) {
							createMyAdmin($_POST["adm_username"], $_POST["adm_pass"], $_POST["adm_name"], $_POST["adm_email"]);
						}
						else { 
							echo '<p class="error"><strong>Error!</strong> - Invalid characters in the username.</p>';
						}				
					}
					else { 
						echo '<p class="error"><strong>Error!</strong> - This username is blocked, choose another.</p>';
					}
				}
				else { 
					echo '<p class="error"><strong>Error!</strong> - All fields must be completed.</p>';
				}
			}
			?>

	        <form action="install?step=2&action=adm" method="post">
	        	<label for="adm_username" class="label">Username</label>
			    <input name="adm_username" id="adm_username" type="text" class="input-text" placeholder="Admin username" maxlength="32">
				<br>
				<label for="adm_pass" class="label">Password</label>
			    <input name="adm_pass" id="adm_pass" type="text" class="input-text" placeholder="Admin password">
				<br>
				<label for="adm_name" class="label">Real name</label>
			    <input name="adm_name" id="adm_name" type="text" class="input-text" placeholder="Admin name">
				<br>
				<label for="adm_email" class="label">Email</label>
			    <input name="adm_email" id="adm_email" type="text" class="input-text" placeholder="Admin email">
				<br>
	        	<input class="submit" type="submit" value="Continue">
	        </form>

	    	</div>

	    <?php } else if($get["step"]==3) { ?>

	        <span class="step">3</span>
			<span class="step-title">Site configuration</span>
			<span class="step-desc">	
			   	Congratulations, administration account was created successful. Now, you need to add information about site name and site domain. This data will be stored in config file in core/config folder. <i>You can change settings by admin account from admin panel</i><br>
			</span>

			<div class="step-form">
			<?php 
				if($get["action"]=="web") {
					if(!empty($_POST["web_name"]) || !empty($_POST["web_url"])) {
						if(empty($_POST["web_name"])) {
							echo('<p class="error"><strong>Error!</strong> - All fields must be completed.</p');			
						}
						else {
							updateWeb($_POST["web_name"], $_POST["web_url"], $_POST["web_email"]);
						}
					}
					else { 
						echo('<p class="error"><strong>Error!</strong> - All fields must be completed.</p>');
					}
				}
			?>
	        <form action="install.php?step=3&action=web" method="post">
	        	<label for="web_name" class="label">Website name</label>
			    <input name="web_name" id="web_name" type="text" class="input-text" placeholder="Website name">
				<br>
				<label for="web_url" class="label">Website domain</label>
			    http:// <input name="web_url" id="web_url" type="text" class="input-text" placeholder="mysite.com">/
				<br>
				<label for="web_email" class="label">Website email</label>
			    <input name="web_email" id="web_email" type="text" class="input-text" placeholder="email@gmail.com">
				<br>
				<br>
			    <br>
	        	<input class="submit" type="submit" value="Continue">
	        </form>

	        </div>

	    <?php } elseif($get["step"]==4) { ?>
				
			<span class="step">4</span>
			<span class="step-title">Installation finished</span>
			<span class="step-desc">	
			   	QwikiA was successful installed on your server. Thank you for installing!<br><i>For security reasons rename or delete INSTALL folder!</i><br>
			</span>

			<div class="step-form">
			Now, you need:<br>
			- Import <a href="https://www.dropbox.com/s/ty21dsjacgtvi9o/demo_content.zip" target="_blank">demo content</a>. Check readme file from it for successful installation.
			<br><i>or</i><br>
			- Sign in with admin account<br>
			- From admin panel add topics (At least 5 topics)<br>
			- Edit website settings<br>
			- Connect STMP email for working without errors<br>

	        <a href="http://<?php echo $_SESSION['link']; ?>/" class="submit" style="margin-top:15px;text-decoration:none">Home page</a>

	        </div>
	    <?php } ?>
	        
	<?php } ?>
	<?php 
	echo(addhtml("footer")); 

?>