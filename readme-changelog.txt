Change log:
---------------------------------------------------------------------------------------------------------------------------------------------
From 1.1.0 to 1.1.1:
Changes:
- Fixed html code injection
- Fixed function for getting user IP
- Fixed blocking per IP
- Added possibility to edit topic url in admin panel

How to update:
Edit next files:
1)  core.php
	-	At then end of file added this function:

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

	- 	Line 33 replace:
		From:
				define("USER_IP", getenv("REMOTE_ADDR"));

		Into:
				define("USER_IP", get_client_ip());

	-	Line 36 replace:
		From:
				define("SCRIPT_VERSION", "1.1.0");

		Into:
				define("SCRIPT_VERSION", "1.1.1");

2)  core/engine.php:
	-	function ask()
		From:
				public function ask($question, $description, $image, $categories) {

					/* Function for saving questions into database */

					if($question && USER_ID && $categories) {

		Into:

				public function ask($question, $description, $image, $categories) {

					/* Function for saving questions into database */

					$question = strip_tags($question);
					$description = strip_tags($description);

					if(strlen($question)>2 && USER_ID && $categories) {



3) 	administration/edit_topic.php
	-	Replace code:
		From:
				define("ID", $_GET["id"]);
				define("NAME", $topic['name']);
				define("PHOTO", $topic['image']);
				define("DESCRIPTION", $topic['description']);

				if($_SERVER['REQUEST_METHOD']=="POST") {
					$admin_session->edit_topic($_POST["id"], $_POST['topic_name'], $_FILES['photo'], $_POST['topic_description']);
				}

		Into:
				define("ID", $_GET["id"]);
				define("NAME", $topic['name']);
				define("URL", $topic['url']);
				define("PHOTO", $topic['image']);
				define("DESCRIPTION", $topic['description']);

				if($_SERVER['REQUEST_METHOD']=="POST") {
					$admin_session->edit_topic($_POST["id"], $_POST['topic_name'], $_POST['topic_url'], $_FILES['photo'], $_POST['topic_description']);
				}

4) administration/admin.php
	-	Replace function edit_topic()
		From:	
				public function edit_topic($id, $name, $photo_file, $description) {

					/* Function for editing topic information */

					if($id!='' && $name!='' && $description!='' && USER_RANK==1) {
						$this->photo = $photo_file = $_FILES["photo"];
						$file = isset($this->photo) ? $this->photo : FALSE;
						$stmt = $this->conn->prepare("SELECT * FROM categories WHERE id = ?");
						$stmt->execute($id);
						$data = $stmt->fetch(PDO::FETCH_ASSOC);
						if($data['name']!=$name) {
							$url = engine::url_slug($name);
							$stmt = $this->conn->prepare("SELECT COUNT(*) FROM categories WHERE url LIKE '".$url."%'");
							$stmt->execute();
							$datac = $stmt->fetch(PDO::FETCH_ASSOC);
							if($datac['COUNT(*)']>0) {
								$url = $url.'-'.$datac['COUNT(*)'];
							}
						}
						else {
							$url = $data['url'];
						}

						if($_FILES['photo']['size']>0) {
							preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $file["name"], $ext);
							$photo_crypt_name = engine::crypts(time(), 1, 0) . "." . $ext[1];
							$directory = '../media/images/categories/' . $photo_crypt_name;
							move_uploaded_file($file["tmp_name"], $directory);
							$stmt = $this->conn->prepare("UPDATE categories SET url = ?, name = ?, image = ?, description = ? WHERE id = ?");
							$stmt->execute(array($url, $name, $photo_crypt_name, $description, $id));
						}
						else {
							$stmt = $this->conn->prepare("UPDATE categories SET url = ?, name = ?, description = ? WHERE id = ?");
							$stmt->execute(array($url, $name, $description, $id));
						}
						$_SESSION['msg'] = 10;
						header('Location: http://'.SITE_DOMAIN.'/administration/manage_topics.php');
						exit;
					}
				}

		Into:
				public function edit_topic($id, $name, $url, $photo_file, $description) {

					/* Function for editing topic information */

					$url = engine::url_slug($url);
					$stmt = $this->conn->prepare("SELECT id FROM categories WHERE url=?");
					$stmt->execute(array($url));
					if($stmt->rowCount()==0) {
						if($id!='' && $name!='' && $description!='' && USER_RANK==1) {
							$this->photo = $photo_file = $_FILES["photo"];
							$file = isset($this->photo) ? $this->photo : FALSE;
							$stmt = $this->conn->prepare("SELECT * FROM categories WHERE id = ?");
							$stmt->execute($id);
							$data = $stmt->fetch(PDO::FETCH_ASSOC);

							if($_FILES['photo']['size']>0) {
								preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $file["name"], $ext);
								$photo_crypt_name = engine::crypts(time(), 1, 0) . "." . $ext[1];
								$directory = '../media/images/categories/' . $photo_crypt_name;
								move_uploaded_file($file["tmp_name"], $directory);
								$stmt = $this->conn->prepare("UPDATE categories SET url = ?, name = ?, image = ?, description = ? WHERE id = ?");
								$stmt->execute(array($url, $name, $photo_crypt_name, $description, $id));
							}
							else {
								$stmt = $this->conn->prepare("UPDATE categories SET url = ?, name = ?, description = ? WHERE id = ?");
								$stmt->execute(array($url, $name, $description, $id));
							}
							$_SESSION['msg'] = 10;
							header('Location: http://'.SITE_DOMAIN.'/administration/manage_topics.php');
							exit;
						}
					}
				}

5) 	template/tpl/hm_edit_topic.tpl
	-	Replace next code:
		From:	
					<div class="row col-xs-12">
                        <label for="topic_name" class="control-label"><?php get_text(426)?> <span style="color:red">*</span></label>
                        <input type="text" name="topic_name" id="topic_name" class="form-control" placeholder="<?php get_text(426)?>" value="<?php echo NAME; ?>" required>
                        <small class="supporting"><?php get_text(427)?></small>
                    </div>
       
       	Into:
       				<div class="row col-xs-12">
                        <label for="topic_name" class="control-label"><?php get_text(426)?> <span style="color:red">*</span></label>
                        <input type="text" name="topic_name" id="topic_name" class="form-control" placeholder="<?php get_text(426)?>" value="<?php echo NAME; ?>" required>
                        <small class="supporting"><?php get_text(427)?></small>
                    </div>
                    <div class="row col-xs-12">
                        <label for="topic_url" class="control-label">Topic URL <span style="color:red">*</span></label>
                        <input type="text" name="topic_url" id="topic_url" class="form-control" placeholder="Enter your url link" value="<?php echo URL; ?>" required>
                        <small class="supporting">Enter new url link without spaces and '/'</small>
                    </div>


---------------------------------------------------------------------------------------------------------------------------------------------
From 1.0.1 to 1.1.0:
Changes:
- New interface with bootstrap 3.3.6;
- Removed deprecated function eregi;
- Added https option for secured urls;
- Added mail() function if SMTP is not set;
- Fixed bad words filter;
- Added notification counter in page title;
- Added automatic url transliteration;
- Fixes with UTF-8 encoding for PHP 5.4;
- Fixes with password recovery form;
- Added description for questions page;
- Added user, who asked question on questions page;
- Removed uppercase for username;
- Added insert image url in answer editor;
- Changed sorting by time;
- Added best answer for each question;
- Fixed loading questions in user profile;
- Other Code optimization;


How to update:
! All custom modifications from previous versions will be deleted !
1) Create backup for all server files;
2) Open core folder and rename texts.php file into texts.old.php;
3) Open core/config folder and rename config.php into config.old.php and mysql.config.php into mysql.config.old.php;
4) Replace all files from new version to old version;
5) Open core/config/config.php and set configs from old config file - config.old.php (step 2). Do not replace all code! Copy only values in new file;
6) Delete core/config/mysql.config.php and rename mysql.config.old.php into mysql.config.php;
7) Restore language file from old version with adding new words. Do not replace all code! Copy only old words in new file and translate the rest of words;


Changed files:
The majority of files has major changes, which can not be edited yourself. It is strongly recommended to replace all files instead editing. In version 1.1.0 was changed 80% of all functions and some functions affect majority of files.
Database, language file is 100% compatible with previous version.
There is new interface, based on bootstrap, so all tpl files was changed with template/style.css file.




---------------------------------------------------------------------------------------------------------------------------------------------
From 1.0.0 to 1.0.1:
Changes:
- Fixes with social authentication
- Fixes with UTF-8 in answers
- Fixes with password uppercase
- Added bad words filter
- Added IPs filter
- Show users IP in admin panel for users and questions
- Disabled button 'Write answer' if user is not signed in
- Installation with PDO connection instead mysql
- Checking username and password for latin symbols


Changed files (But, please, if is possible - just replace all files for correct work):
1) [edited]		core.php

2) [edited]		install/index.php

3) [edited]		core/engine.php
- function __construct()
- public function getlikes();
- public function checkcookies();
- public function createuseraccount();
- public function createuseraccountsocial();
- public function get_answers();
- public function ask();
- public function edit_question();
- public function answer();
- public function edit_answer();
- public function updateusersettings();

4) [edited]	 	core/texts.php
- 463, 464, 465, 466, 467, 468

5) [added] 		core/filter.php

6) [edited] 	core/modules/auth.php

7) [edited]		core/pages/error.php

8) [edited]		template/tpl/m-more.tpl

9) [added] 		template/tpl/hm_filter.tpl

10) [edited] 	admin/admin.php
- public function serializehmtabs();
- public function updatefilter();
- public function get_users_table();
- public function get_questions_table();

11) [edited] 	admin/index.php

12) [added] 	admin/filter_settings.php

13) [edited]	template/style.css
 	#username, #name {
	  text-transform: capitalize;
	}
	into:
	#name {
	  text-transform: capitalize;
	}

14) [edited]	media/js/script.js
	From:
	$('#username').bind('keyup',checkUsername);

	Into:
	$('#username').bind('keyup',checkUsername);
	$("#username").keyup(function(){
	    var value = $(this).val().replace(/[^-a-zA-Z0-9]/g, "");
	    $(this).val(value)
	})

15) [edited]	media/js/m_script.js
	From:
	$('#username').bind('keyup',checkUsername);

	Into:
	$('#username').bind('keyup',checkUsername);
	$("#username").keyup(function(){
	    var value = $(this).val().replace(/[^-a-zA-Z0-9]/g, "");
	    $(this).val(value)
	})

