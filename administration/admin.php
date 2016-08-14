<?php

	/* Admin class used for admin panel. Is loaded only if ADMIN==1 */

	include_once '../core/engine.php';

	class admin extends engine {

		public function get_users_table($page,$user) {

			/* Function for getting all users */

			$onpage = 10;
			$openedpage = $page;
			$page = $page * $onpage;
			$this->message = '';
			$stmt = $this->conn->prepare("SELECT COUNT(*) FROM users");
			$stmt->execute();
			$total = $stmt->fetch(PDO::FETCH_ASSOC);
			$tot = $total['COUNT(*)'];
			$pages = intval(($tot-1) / $onpage) + 1;
			if(USER_RANK==1) {
				if($user!="") {
					$stmt = $this->conn->prepare("SELECT * FROM users WHERE username LIKE ? OR name LIKE ? OR email LIKE ? ORDER BY id DESC LIMIT ?, ?");
					$stmt->bindValue(1, '%'.$user.'%', PDO::PARAM_STR);
					$stmt->bindValue(2, '%'.$user.'%', PDO::PARAM_STR);
					$stmt->bindValue(3, '%'.$user.'%', PDO::PARAM_STR);
					$stmt->bindValue(4, $page, PDO::PARAM_INT);
					$stmt->bindValue(5, $onpage, PDO::PARAM_INT);
				}
				else {
					$stmt = $this->conn->prepare("SELECT * FROM users ORDER BY id DESC LIMIT ?, ?");
					$stmt->bindValue(1, $page, PDO::PARAM_INT);
					$stmt->bindValue(2, $onpage, PDO::PARAM_INT);
				}
				$stmt->execute();
				if($stmt->rowCount()!=0) {
					$this->message .= '<table class="table">';
					$this->message .= '<tr>';
					$this->message .= '<td> </td>';
					$this->message .= '<td><b>'.get_text(269,1).'</b></td>';
					$this->message .= '<td><b>'.get_text(23,1).'<br><small>'.get_text(50,1).'</small></b></td>';
					$this->message .= '<td><b>'.get_text(439,1).'</b></td>';
					$this->message .= '<td><b>'.get_text(440,1).'<br><small>'.get_text(270,1).'</small></b></td>';
					$this->message .= '<td><b>'.get_text(367,1).'</b></td>';
					$this->message .= '</tr>';
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {		
						$this->message .= '<tr>';
						if($ii['verified']=="" || $ii['verified']==0) {
							$list = '<input type="checkbox" onclick="verified(\''.$ii['username'].'\',1);" style="margin-left:10px">';
						}
						else {
							$list = '<input type="checkbox" onclick="verified(\''.$ii['username'].'\',0);" checked style="margin-left:10px">';
						}
						$this->message .= '<td>'.$list.'</td>';
						$this->message .= '<td><a href="'.$ii["username"].'" target="_blank"><img src="media/images/users/'.$ii["photo"].'" width="50" height="50"></a></td>';
						$this->message .= '<td><a href="'.$ii["username"].'" target="_blank">'.$ii["username"].'</a><br><small>'.$ii["name"].'</small></td>';
						$this->message .= '<td><i>'.$ii["email"].'</i><br>';
						$questions = engine::number_user_questions($ii['id']);
						$answers = engine::number_user_answers($ii['id']);
						$points = engine::getuserdata($ii['id'], "POINTS");
						$this->message .= '<a href="'.$ii["username"].'/questions" target="_blank">'.ucfirst(get_text(196,1)).': <b>'.$questions.'</b></a><br>';
						$this->message .= '<a href="'.$ii["username"].'/advices" target="_blank">'.get_text(169,1).': <b>'.$answers.'</b></a><br>';
						$this->message .= '<a href="'.$ii["username"].'/points" target="_blank">'.get_text(39,1).': <b>'.$points.'</b></a></td>';
						$this->message .= '<td>'.get_time($ii["visit"]).'<br><small>'.get_time($ii["reg_date"]).'<br><i>'.$ii['ip'].'</i></small></td>';
						$this->message .= '<td><a href="administration/delete_user.php?id='.$ii["id"].'" style="color:red">'.get_text(382,1).'</a></td>';
						$this->message .= '</tr>';				
					}
					$this->message .= '</table>';
					if($user=="") {
						$this->message .= '<div class="text-center"><ul class="pagination">';
						for($i=0;$i<$pages;$i++) {
							if($openedpage==$i) $active = ' class="active"'; else $active = '';
							$this->message .= '<li'.$active.'><a href="administration/manage_users.php?page='.$i.'">'.($i+1).'</a></li>';
						}
						$this->message .= '</ul></div>';
					}
				}
			}
			return $this->message;
		}

		public function get_questions_table($page,$question) {

			/* Function for showing all questions into DB */

			$onpage = 10;
			$openedpage = $page;
			$page = $page * $onpage;
			$this->message = '';
			$stmt = $this->conn->prepare("SELECT COUNT(*) FROM questions");
			$stmt->execute();
			$total = $stmt->fetch(PDO::FETCH_ASSOC);
			$tot = $total['COUNT(*)'];
			$pages = intval(($tot-1) / $onpage) + 1;
			if(USER_RANK==1) {
				if($question!="") {
					$stmt = $this->conn->prepare("SELECT * FROM questions WHERE question LIKE ? OR description LIKE ? ORDER BY time DESC LIMIT ?, ?");
					$stmt->bindValue(1, '%'.$question.'%', PDO::PARAM_STR);
					$stmt->bindValue(2, '%'.$question.'%', PDO::PARAM_STR);
					$stmt->bindValue(3, $page, PDO::PARAM_INT);
					$stmt->bindValue(4, $onpage, PDO::PARAM_INT);
				}
				else {
					$stmt = $this->conn->prepare("SELECT * FROM questions ORDER BY id DESC LIMIT ?, ?");
					$stmt->bindValue(1, $page, PDO::PARAM_INT);
					$stmt->bindValue(2, $onpage, PDO::PARAM_INT);
				}
				$stmt->execute();
				if($stmt->rowCount()!=0) {
					$this->message .= '<table class="table" style="font-size:13px">';
					$this->message .= '<tr>';
					$this->message .= '<td><b>'.get_text(242,1).'</b></td>';
					$this->message .= '<td style="width:300px"><b>'.get_text(243,1).'<br><small>'.get_text(415,1).'</small></b></td>';
					$this->message .= '<td><b>'.get_text(189,1).'</b></td>';
					$this->message .= '<td><b>'.get_text(444,1).'</b></td>';
					$this->message .= '<td><b>'.get_text(367,1).'</b></td>';
					$this->message .= '</tr>';
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {		
						$this->message .= '<tr>';
						$username = engine::get_user_name($ii['user_id']);
						$this->message .= '<td><a href="'.$username.'" target="_blank">'.$username.'</a></td>';
						$this->message .= '<td><a href="q/'.$ii["url"].'" target="_blank">'.$ii["question"].'</a><br><small style="font-size:11px">'.$ii["description"].'</small></td>';
						$categories = engine::get_question_categories_by_comma($ii['category']);
						$this->message .= '<td><small>'.$categories.'</small></td>';
						$this->message .= '<td><small>'.get_time($ii["time_asked"]).'<br><i>'.$ii['ip'].'</i></small></td>';
						$this->message .= '<td><a href="administration/edit_question.php?id='.$ii["id"].'">'.get_text(205,1).'</a><br>';
						$this->message .= '<a href="administration/delete_question.php?id='.$ii["id"].'" style="color:red">'.get_text(373,1).'</a></td>';
						$this->message .= '</tr>';				
					}
					$this->message .= '</table>';
					if($question=="") {
						$this->message .= '<div class="text-center"><ul class="pagination">';
						for($i=0;$i<$pages;$i++) {
							if($openedpage==$i) $active = ' class="active"'; else $active = '';
							$this->message .= '<li'.$active.'><a href="administration/manage_questions.php?page='.$i.'">'.($i+1).'</a></li>';
						}
						$this->message .= '</ul></div>';
					}
				}
			}
			return $this->message;
		}

		public function get_answers_table($page,$answer) {

			/* Functionn for getting all answers */

			$onpage = 10;
			$openedpage = $page;
			$page = $page * $onpage;
			$this->message = '';
			$stmt = $this->conn->prepare("SELECT COUNT(*) FROM answers");
			$stmt->execute();
			$total = $stmt->fetch(PDO::FETCH_ASSOC);
			$tot = $total['COUNT(*)'];
			$pages = intval(($tot-1) / $onpage) + 1;
			if(USER_RANK==1) {
				if($answer!="") {
					$stmt = $this->conn->prepare("SELECT * FROM answers WHERE answer LIKE ? ORDER BY time DESC LIMIT ?, ?");
					$stmt->bindValue(1, '%'.$answer.'%', PDO::PARAM_STR);
					$stmt->bindValue(2, $page, PDO::PARAM_INT);
					$stmt->bindValue(3, $onpage, PDO::PARAM_INT);
				}
				else {
					$stmt = $this->conn->prepare("SELECT * FROM answers ORDER BY id DESC LIMIT ?, ?");
					$stmt->bindValue(1, $page, PDO::PARAM_INT);
					$stmt->bindValue(2, $onpage, PDO::PARAM_INT);
				}
				$stmt->execute();
				if($stmt->rowCount()!=0) {
					$this->message .= '<table class="table">';
					$this->message .= '<tr>';
					$this->message .= '<td><b>'.get_text(242,1).'</b></td>';
					$this->message .= '<td><b>'.get_text(243,1).'</b><br><small>'.get_text(164,1).'</small></td>';
					$this->message .= '<td><b>'.get_text(367,1).'</b></td>';
					$this->message .= '</tr>';
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {		
						$this->message .= '<tr>';
						$username = engine::get_user_name($ii['user_id']);
						$this->message .= '<td><a href="'.$username.'" target="_blank">'.$username.'</a></td>';
						$stmt = $this->conn->prepare("SELECT url, question FROM questions WHERE id=?");
						$stmt->execute(array($ii['question_id']));
						$q = $stmt->fetch(PDO::FETCH_ASSOC);
						$this->message .= '<td><br><small><a href="q/'.$q["url"].'" target="_blank">'.$q["question"].'</a></small><hr>'.nl2br($ii['answer']).'<hr><small>'.get_text(168,1).': <b>'.$ii['upvotes'].'</b><br>'.get_text(444,1).': '.get_time($ii["time"]).'<br></small><br></td>';
						$this->message .= '<td><a href="administration/edit_answer.php?id='.$ii["id"].'">'.get_text(207,1).'</a><br>';
						$this->message .= '<a href="administration/delete_answer.php?id='.$ii["id"].'" style="color:red">'.get_text(375,1).'</a></td>';
						$this->message .= '</tr>';				
					}
					$this->message .= '</table>';
					if($answer=="") {
						$this->message .= '<div class="text-center"><ul class="pagination">';
						for($i=0;$i<$pages;$i++) {
							if($openedpage==$i) $active = ' class="active"'; else $active = '';
								$this->message .= '<li'.$active.'><a href="administration/manage_answers.php?page='.$i.'">'.($i+1).'</a></li>';
						}
						$this->message .= '</ul></div>';
					}
				}
			}
			return $this->message;
		}

		public function get_topics_table($page,$topic) {

			/* Function for showing topics table */

			$onpage = 10;
			$openedpage = $page;
			$page = $page * $onpage;
			$this->message = '';
			$stmt = $this->conn->prepare("SELECT COUNT(*) FROM categories");
			$stmt->execute();
			$total = $stmt->fetch(PDO::FETCH_ASSOC);
			$tot = $total['COUNT(*)'];
			$pages = intval(($tot-1) / $onpage) + 1;
			if(USER_RANK==1) {
				if($topic!="") {
					$stmt = $this->conn->prepare("SELECT * FROM categories WHERE name LIKE ? OR description LIKE ? ORDER BY id DESC LIMIT ?, ?");
					$stmt->bindValue(1, '%'.$topic.'%', PDO::PARAM_STR);
					$stmt->bindValue(2, '%'.$topic.'%', PDO::PARAM_STR);
					$stmt->bindValue(3, $page, PDO::PARAM_INT);
					$stmt->bindValue(4, $onpage, PDO::PARAM_INT);
				}
				else {
					$stmt = $this->conn->prepare("SELECT * FROM categories ORDER BY id DESC LIMIT ?, ?");
					$stmt->bindValue(1, $page, PDO::PARAM_INT);
					$stmt->bindValue(2, $onpage, PDO::PARAM_INT);
				}
				$stmt->execute();
				if($stmt->rowCount()!=0) {
					$this->message .= '<table class="table">';
					$this->message .= '<tr>';
					$this->message .= '<td><b>'.get_text(287,1).'</b></td>';
					$this->message .= '<td><b>'.get_text(426,1).'</b></td>';
					$this->message .= '<td><b>'.get_text(431,1).'</b></td>';
					$this->message .= '<td style="min-width:80px"><b>'.get_text(367,1).'</b></td>';
					$this->message .= '</tr>';
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {		
						$this->message .= '<tr>';
						$this->message .= '<td><img src="media/images/categories/'.$ii["image"].'" width="50" height="50"></td>';
						$this->message .= '<td><a href="category/'.$ii["url"].'" target="_blank">'.$ii["name"].' </a><br><small>['.$ii["followers"].'] '.get_text(118,1).'</small></td>';
						$this->message .= '<td>'.$ii["description"].'</td>';
						$this->message .= '<td><a href="administration/edit_topic.php?id='.$ii["id"].'">'.ucfirst(get_text(404,1)).'</a><br>';
						$this->message .= '<a href="administration/delete_topic.php?id='.$ii["id"].'" style="color:red">'.get_text(381,1).'</a></td>';
						$this->message .= '</tr>';				
					}
					$this->message .= '</table>';
					if($topic=="") {
						$this->message .= '<div class="text-center"><ul class="pagination">';
						for($i=0;$i<$pages;$i++) {
							if($openedpage==$i) $active = ' class="active"'; else $active = '';
							$this->message .= '<li'.$active.'><a href="administration/manage_topics.php?page='.$i.'">'.($i+1).'</a></li>';
						}
						$this->message .= '</ul></div>';
					}
				}
			}
			return $this->message;
		}

		public function get_user_info($id) {

			/* Function for getting information about user */

			$stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
			$stmt->execute(array($id));
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}

		public function delete_user($id) {

			/* Function for deleting user */

			if($id!='' && USER_RANK==1) {
				$stmt = $this->conn->prepare("SELECT category_id FROM follows WHERE user_id = ?");
				$stmt->execute(array($id));
				if($stmt->rowCount()!=0) {
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
						$stmt = $this->conn->prepare("UPDATE categories SET followers=followers-1 WHERE id = ?");
						$stmt->execute(array($ii['category_id']));
					}
				}

				$stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
				$stmt->execute(array($id));
				$stmt = $this->conn->prepare("DELETE FROM follows WHERE user_id = ?");
				$stmt->execute(array($id));
				$stmt = $this->conn->prepare("SELECT id FROM questions WHERE user_id = ?");
				$stmt->execute(array($id));
				if($stmt->rowCount()!=0) {
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
						$q_id = $ii['id'];
						$stmt = $this->conn->prepare("UPDATE questions SET category = REPLACE (category, '".$id." ', '') WHERE id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE answers_likes FROM answers_likes INNER JOIN answers ON answers_likes.answer_id = answers.id WHERE answers.question_id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE notifications FROM notifications INNER JOIN answers ON notifications.n_id = answers.id WHERE (notifications.type='1' OR notifications.type='2' OR notifications.type='4' OR notifications.type='7' OR notifications.type='8') AND answers.question_id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE points_log FROM points_log INNER JOIN answers ON points_log.log_id = answers.id WHERE points_log.log_type='2' AND answers.question_id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE reports FROM reports INNER JOIN answers ON reports.r_id = answers.id WHERE reports.type='2' AND answers.question_id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE FROM questions WHERE id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE FROM answers WHERE question_id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE FROM likes WHERE question_id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE FROM notifications WHERE (type='3' OR type='5' OR type='6') AND n_id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE FROM points_log WHERE log_type='1' AND log_id = ?");
						$stmt->execute(array($q_id));
					}
				}
				$stmt = $this->conn->prepare("DELETE FROM answers WHERE user_id = ?");
				$stmt->execute(array($id));
				$stmt = $this->conn->prepare("DELETE FROM answers_likes WHERE user_id = ?");
				$stmt->execute(array($id));
				$stmt = $this->conn->prepare("DELETE FROM likes WHERE user_id = ? OR to_user = ?");
				$stmt->execute(array($id, $id));
				$stmt = $this->conn->prepare("DELETE FROM notifications WHERE user_id = ? OR to_user = ?");
				$stmt->execute(array($id, $id));
				$stmt = $this->conn->prepare("DELETE FROM points_log WHERE from_user = ? OR to_user = ?");
				$stmt->execute(array($id, $id));
				$stmt = $this->conn->prepare("DELETE FROM reports WHERE user_id = ?");
				$stmt->execute(array($id));
				$_SESSION['msg'] = 13;
				engine::headerin("administration/manage_users.php");
				exit;	
			}
		}

		public function add_topic($name, $photo_file, $description) {

			/* Function for adding new topic */

			if($name!='' && $description!='' && USER_RANK==1) {
				$this->photo = $photo_file = $_FILES["photo"];
				$file = isset($this->photo) ? $this->photo : FALSE;
				if($file) {
					$url = engine::url_slug($name);
					$stmt = $this->conn->prepare("SELECT COUNT(*) FROM categories WHERE url LIKE '".$url."%'");
					$stmt->execute();
					$data = $stmt->fetch(PDO::FETCH_ASSOC);
					if($data['COUNT(*)']>0) {
						$url = $url.'-'.$data['COUNT(*)'];
					}

					preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $file["name"], $ext);
					$photo_crypt_name = engine::crypts(time(), 1, 0) . "." . $ext[1];
					$directory = '../media/images/categories/' . $photo_crypt_name;
					$stmt = $this->conn->prepare("INSERT INTO categories (url, name, image, description, followers) VALUES (?, ?, ?, ?, ?)");
					$stmt->execute(array($url, $name, $photo_crypt_name, $description, 0));
					move_uploaded_file($file["tmp_name"], $directory);
					if(SESSION_STATUS!=false) {
						if($stmt->rowCount()!=0) {
							$_SESSION['msg'] = 6;
							engine::headerin("administration/manage_topics.php");
							exit;
						} 
						else { 
							engine::headerin("administration/manage_topics.php");
							exit;
						}
					}
				}
			}
		}

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

		public function get_topic_info($id) {

			/* Function for getting information about topic */

			$stmt = $this->conn->prepare("SELECT * FROM categories WHERE id = ?");
			$stmt->execute(array($id));
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}

		public function get_question_info($id) {

			/* Function for getting information about question */

			$stmt = $this->conn->prepare("SELECT * FROM questions WHERE id = ?");
			$stmt->execute(array($id));
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}

		public function get_answer_info($id) {

			/* Function for getting information about answer */

			$stmt = $this->conn->prepare("SELECT * FROM answers WHERE id = ?");
			$stmt->execute(array($id));
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}

		public function delete_topic($id) {

			/* Function for deleting topic with all information woth it */

			if($id!='' && USER_RANK==1) {
				$stmt = $this->conn->prepare("DELETE FROM categories WHERE id=?");
				$stmt->execute(array($id));
				$stmt = $this->conn->prepare("DELETE FROM follows WHERE category_id = ?");
				$stmt->execute(array($id));
				$stmt = $this->conn->prepare("SELECT id FROM questions WHERE category = ?");
				$stmt->execute(array($id.' '));
				if($stmt->rowCount()!=0) {
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
						$q_id = $ii['id'];
						$stmt = $this->conn->prepare("UPDATE questions SET category = REPLACE (category, '".$id." ', '') WHERE id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE answers_likes FROM answers_likes INNER JOIN answers ON answers_likes.answer_id = answers.id WHERE answers.question_id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE notifications FROM notifications INNER JOIN answers ON notifications.n_id = answers.id WHERE (notifications.type='1' OR notifications.type='2' OR notifications.type='4' OR notifications.type='7' OR notifications.type='8') AND answers.question_id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE points_log FROM points_log INNER JOIN answers ON points_log.log_id = answers.id WHERE points_log.log_type='2' AND answers.question_id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE FROM questions WHERE id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE FROM answers WHERE question_id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE FROM likes WHERE question_id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE FROM notifications WHERE (type='3' OR type='5' OR type='6') AND n_id = ?");
						$stmt->execute(array($q_id));
						$stmt = $this->conn->prepare("DELETE FROM points_log WHERE log_type='1' AND log_id = ?");
						$stmt->execute(array($q_id));
					}
				}
				$_SESSION['msg'] = 7;
				engine::headerin("administration/manage_topics.php");
				exit;	
			}
		}

		public function save_page($id, $content) {

			/* Function for updating pages content */

			if(USER_RANK==1) {
				$stmt = $this->conn->prepare("UPDATE pages SET content = ?, time = ? WHERE id = ?");
				$stmt->execute(array($content, time(), $id));
				$_SESSION['msg'] = 1;
				engine::headerin("administration/manage_pages.php");
				exit;
			}
		}

		public function serializehmtabs($in_tab) {

			/* Function for creating menu in admin panel */

			$this->message = '';
			if($in_tab == 1) $this->message .= '<b>'; 
			$this->message .= '<a href="administration/index.php">'.get_text(312,1).'</a>';
			if($in_tab == 1) $this->message .= '</b>'; 
			if($in_tab == 2) $this->message .= '<b>'; 
			$this->message .= '<a href="administration/site_settings.php">'.get_text(105,1).'</a>';
			if($in_tab == 2) $this->message .= '</b>'; 
			if($in_tab == 3) $this->message .= '<b>'; 
			$this->message .= '<a href="administration/manage_topics.php">'.get_text(189,1).'</a>';
			if($in_tab == 3) $this->message .= '</b>';
			if($in_tab == 4) $this->message .= '<b>'; 
			$this->message .= '<a href="administration/manage_users.php">'.get_text(313,1).'</a>';
			if($in_tab == 4) $this->message .= '</b>'; 
			if($in_tab == 5) $this->message .= '<b>'; 
			$this->message .= '<a href="administration/manage_questions.php">'.ucfirst(get_text(196,1)).'</a>';
			if($in_tab == 5) $this->message .= '</b>'; 
			if($in_tab == 6) $this->message .= '<b>';
			$this->message .= '<a href="administration/manage_answers.php">'.get_text(169,1).'</a>';
			if($in_tab == 6) $this->message .= '</b>';
			if($in_tab == 7) $this->message .= '<b>';
			$this->message .= '<a href="administration/manage_reports.php">'.get_text(314,1).'</a>';
			if($in_tab == 7) $this->message .= '</b>';
			if($in_tab == 8) $this->message .= '<b>';
			$this->message .= '<a href="administration/manage_pages.php">'.get_text(315,1).'</a>';
			if($in_tab == 8) $this->message .= '</b>';
			if($in_tab == 9) $this->message .= '<b>';
			$this->message .= '<a href="administration/filter_settings.php">'.get_text(463,1).'</a>';
			if($in_tab == 9) $this->message .= '</b>';
			return $this->message;
		}

		public function get_reports_table($page,$report) {

			/* Function for showing all reports from DB */

			$onpage = 10;
			$openedpage = $page;
			$page = $page * $onpage;
			$this->message = '';
			$stmt = $this->conn->prepare("SELECT COUNT(*) FROM reports");
			$stmt->execute();
			$total = $stmt->fetch(PDO::FETCH_ASSOC);
			$tot = $total['COUNT(*)'];
			$pages = intval(($tot-1) / $onpage) + 1;
			if(USER_RANK==1) {
				if($report!="") {
					$stmt = $this->conn->prepare("SELECT * FROM reports WHERE message LIKE ? ORDER BY id DESC LIMIT ?, ?");
					$stmt->bindValue(1, '%'.$report.'%', PDO::PARAM_STR);
					$stmt->bindValue(2, $page, PDO::PARAM_INT);
					$stmt->bindValue(3, $onpage, PDO::PARAM_INT);
				}
				else {
					$stmt = $this->conn->prepare("SELECT * FROM reports ORDER BY id DESC LIMIT ?, ?");
					$stmt->bindValue(1, $page, PDO::PARAM_INT);
					$stmt->bindValue(2, $onpage, PDO::PARAM_INT);
				}
				$stmt->execute();
				if($stmt->rowCount()!=0) {
					$this->message .= '<table class="table">';
					$this->message .= '<tr>';
					$this->message .= '<td><b>'.get_text(370,1).'</b></td>';
					$this->message .= '<td><b>'.get_text(371,1).'</b></td>';
					$this->message .= '<td><b>'.get_text(372,1).'</b></td>';
					$this->message .= '<td><b>'.get_text(367,1).'</b></td>';
					$this->message .= '</tr>';
					foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {		
						$this->message .= '<tr>';
						$this->message .= '<td>'.get_time($ii['time']).'</td>';
						if($ii['type']==1) {
							$stmt = $this->conn->prepare("SELECT * FROM questions WHERE id=?");
							$stmt->execute(array($ii['r_id']));
							$question = $stmt->fetch(PDO::FETCH_ASSOC);
							$by_user = engine::get_user_name($question['user_id']);
							$data = '<a href="q/'.$question['url'].'" target="_blank">'.$question['question'].'</a><br><small>'.$question['description'].'<i>by <a href="'.$by_user.'" target="_blank">'.$by_user.'</a></i></small>';
						}
						else if($ii['type']==2) {
							$stmt = $this->conn->prepare("SELECT * FROM answers WHERE id=?");
							$stmt->execute(array($ii['r_id']));
							$answer = $stmt->fetch(PDO::FETCH_ASSOC);
							$stmt = $this->conn->prepare("SELECT * FROM questions WHERE id=?");
							$stmt->execute(array($answer['question_id']));
							$question = $stmt->fetch(PDO::FETCH_ASSOC);
							$by_user = engine::get_user_name($answer['user_id']);
							$data = '<a href="q/'.$question['url'].'" target="_blank">'.$answer['answer'].'</a><br><small><i>by <a href="'.$by_user.'" target="_blank">'.$by_user.'</a></i></small>';;
						}
						$this->message .= '<td>'.$data.'</td>';
						$by_user = engine::get_user_name($ii['user_id']);
						$this->message .= '<td>'.$ii["message"].'<br><small><i>by <a href="'.$by_user.'" target="_blank">'.$by_user.'</a></i></small></td>';
						if($ii["type"]==1) {
							$add = '<a href="administration/edit_question.php?id='.$ii["r_id"].'">'.get_text(222,1).'</a><br>';
							$add .= '<a href="administration/delete_question.php?id='.$ii["r_id"].'" style="color:red">'.get_text(373,1).'</a>';
						}
						elseif($ii["type"]==2) {
							$add = '<a href="administration/edit_answer.php?id='.$ii["r_id"].'">'.get_text(207,1).'</a><br>';
							$add .= '<a href="administration/delete_answer.php?id='.$ii["r_id"].'" style="color:red">'.get_text(375,1).'</a>';
						}
						$this->message .= '<td><a href="administration/delete_report.php?id='.$ii["id"].'" style="color:red">'.get_text(374,1).'</a><br>'.$add.'</td>';
						$this->message .= '</tr>';				
					}
					$this->message .= '</table>';
					if($report=="") {
						$this->message .= '<div class="text-center"><ul class="pagination">';
						for($i=0;$i<$pages;$i++) {
							if($openedpage==$i) $active = ' class="active"'; else $active = '';
							$this->message .= '<li'.$active.'><a href="administration/manage_reports.php?page='.$i.'">'.($i+1).'</a></li>';
						}
						$this->message .= '</ul></div>';
					}
				}
			}
			return $this->message;
		}

		public function delete_report($id) {

			/* Function for deleting report from DB */

			if($id!='' && USER_RANK==1) {
				$stmt = $this->conn->prepare("DELETE FROM reports WHERE id=?");
				$stmt->execute(array($id));
				$_SESSION['msg'] = 21;
				engine::headerin("administration/manage_reports.php");
				exit;	
			}
		}

		public function updatesitesettings($site_name, $description, $keywords, $fb_id, $fb_secret, $tw_key, $tw_secret, $adsense, $site_email,  $smtp_host, $smtp_port, $smtp_user, $smtp_pass, $auth_fb, $auth_tw, $signup_confirmation, $filter_word) {

			/* Function for updating website settings */

			if(USER_RANK==1) {
				$config = include '../core/config/config.php';
				$config['site_name'] = $site_name;
				$config['site_description'] = $description;
				$config['site_keywords'] = $keywords;
				$config['fb_id'] = $fb_id;
				$config['fb_secret'] = $fb_secret;
				$config['tw_key'] = $tw_key;
				$config['tw_secret'] = $tw_secret;
				$config['adsense'] = $adsense;
				$config['site_email'] = $site_email;
				$config['smtp_host'] = $smtp_host;
				$config['smtp_port'] = $smtp_port;
				$config['smtp_user'] = $smtp_user;
				$config['smtp_pass'] = $smtp_pass;
				$config['auth_fb'] = $auth_fb;
				$config['auth_tw'] = $auth_tw;
				$config['filter_word'] = $filter_word;
				$config['signup_confirmation'] = $signup_confirmation;
				file_put_contents('../core/config/config.php', '<?php return ' . var_export($config, true) . ';');
				$_SESSION['msg'] = 1;
				engine::headerin("administration/site_settings.php");
				exit;
			}
		}

		public function updatefilter($words, $ips) {

			/* Function for updating website settings */

			if(USER_RANK==1) {
				$config = include '../core/filter.php';
				$config['words'] = $words;
				$config['ips'] = $ips;
				file_put_contents('../core/filter.php', '<?php return ' . var_export($config, true) . ';');
				$_SESSION['msg'] = 1;
				engine::headerin("administration/filter_settings.php");
				exit;
			}
		}
		
		public function edit_question($id, $question, $description, $image, $categories) {

			/* Function for saving questions into database */

			if($id!='' && $question!='' && USER_RANK==1) {
				$categories = implode(" ", $categories).' ';
				$url = engine::url_slug($question);
				$stmt = $this->conn->prepare("SELECT COUNT(*) FROM questions WHERE url LIKE '".$url."%'");
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				if($data['COUNT(*)']>0) {
					$url = $url.'-'.$data['COUNT(*)'];
				}

				$image = $_FILES["photo"];
				$error = array();
				$file = isset($image) ? $image : FALSE;
				if($file && $_FILES['photo']['size']>0) {
					if(!preg_match("/^image\/(pjpeg|jpeg|jpg|png|gif|bmp)$/i", $file["type"]) || sizeof($error)) {
						engine::headerin("");
						exit;
					}
					else {
						preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $file["name"], $ext);
						$photo_crypt_name = engine::crypts(USER_USERNAME.time(), 1, 0).".".$ext[1];
						$directory = "../media/images/users/".''.$photo_crypt_name;
						$tdirectory = "../media/images/users/".'t_'.$photo_crypt_name;
						move_uploaded_file($file["tmp_name"], $directory);
						engine::resizeimg($directory, $tdirectory, 400, 300);
					}
					$stmt = $this->conn->prepare("UPDATE questions SET category=?, url=?, question=?, description=?, image=? WHERE id=?");
					$stmt->execute(array($categories, $url, $question, $description, $photo_crypt_name, $id));
				}
				else {
					$stmt = $this->conn->prepare("UPDATE questions SET category=?, url=?, question=?, description=? WHERE id=?");
					$stmt->execute(array($categories, $url, $question, $description, $id));
				}

				$stmt = $this->conn->prepare("SELECT user_id, question, url FROM questions WHERE id = ?");
				$stmt->execute(array($id));
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt = $this->conn->prepare("INSERT INTO notifications (to_user, type, user_id, n_id, time, viewed) VALUES (?, ?, ?, ?, ?, ?)");
				$stmt->execute(array($data['user_id'],'5',1,$id,time(),'0'));
				if(engine::getuserdata($data['user_id'], "NOT_SYSTEM")==1 && engine::getuserdata($data['user_id'], "ONLINE")==0) {
					$user_question_link = engine::get_user_name($data['user_id']);
					$theme = get_text(458,1).' '.SITE_NAME.' – '.SITE_DESCRIPTION;
					$body = get_text(83,1).' '.$user_question_link.',<br><br> '.get_text(180,1).' <a href="http://'.SITE_DOMAIN.'/q/'.$data['url'].'">'.$data['question'].'</a> '.get_text(178,1).'.<br>'.get_text(77,1).' '.SITE_NAME.'<br><br>'.get_text(78,1).',<br>'.SITE_NAME.' - '.SITE_DESCRIPTION.'<br><br><small>'.get_text(79,1).'.</small>';
					$to = engine::getuserdata($data['user_id'],"EMAIL");
					engine::send_email($to,$theme,$body);
				}

				$_SESSION['msg'] = 41;
				engine::headerin("administration/manage_questions.php");

			}
			else  {
				engine::headerin("administration/manage_questions.php");
			}
		}

		public function edit_answer($id, $answer) {

			/*  Function for edit answer */

			if($id!='' && $answer!='' && USER_RANK==1) {
				$stmt = $this->conn->prepare("UPDATE answers SET answer=? WHERE id=?");
				$stmt->execute(array($answer, $id));
				$stmt = $this->conn->prepare("SELECT user_id FROM answers WHERE id = ?");
				$stmt->execute(array($id));
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt = $this->conn->prepare("INSERT INTO notifications (to_user, type, user_id, n_id, time, viewed) VALUES (?, ?, ?, ?, ?, ?)");
				$stmt->execute(array($data['user_id'],'7',1,$id,time(),'0'));
				if(engine::getuserdata($data['user_id'], "NOT_SYSTEM")==1 && engine::getuserdata($data['user_id'], "ONLINE")==0) {
					$user_answer_link = engine::get_user_name($data['user_id']);
					$theme = get_text(449,1).' '.SITE_NAME;
					$theme = get_text(459,1).' '.SITE_NAME.' - '.SITE_DESCRIPTION;
					$answer = strip_tags($answer);
					$body = get_text(83,1).' '.$user_answer_link.',<br><br> '.get_text(182,1).' - '.$answer.' - '.get_text(178,1).'.<br>'.get_text(77,1).' '.SITE_NAME.'<br><br>'.get_text(78,1).',<br>'.SITE_NAME.' - '.SITE_DESCRIPTION.'<br><br><small>'.get_text(79,1).'.</small>';
					$to = engine::getuserdata($data['user_id'],"EMAIL");
					engine::send_email($to,$theme,$body);
				}
				$_SESSION['msg'] = 51;
				engine::headerin("administration/manage_answers.php");

			}
			else  {
				engine::headerin("administration/manage_answers.php");
			}
		}

		public function numofrows($table) {

			/* Get number of rows in a table for information in dashboard */

			if($table!=''){
				$stmt = $this->conn->prepare("SELECT COUNT(id) AS total FROM ".$table);
				$stmt->execute();
				$ii = $stmt->fetch(PDO::FETCH_ASSOC);
				return $ii['total'];
			}
		}

		public function get_chart($type, $limit=21) {

			/* Get information for chart in dashboard */

			if($type=='users') {
				$stmt = $this->conn->prepare("SELECT DAYOFYEAR(FROM_UNIXTIME(reg_date)) AS g_day, DATE_FORMAT(FROM_UNIXTIME(reg_date), '%Y-%m-%d') AS g_date, COUNT(*) AS g_cant FROM users GROUP BY DATE(FROM_UNIXTIME(reg_date)) ORDER BY g_date DESC LIMIT ".$limit);
			}
			else if($type=='questions') {
				$stmt = $this->conn->prepare("SELECT DAYOFYEAR(FROM_UNIXTIME(time_asked)) AS g_day, DATE_FORMAT(FROM_UNIXTIME(time_asked), '%Y-%m-%d') AS g_date, COUNT(*) AS g_cant FROM questions GROUP BY DATE(FROM_UNIXTIME(time_asked)) ORDER BY g_date DESC LIMIT ".$limit);
			}
			else if($type=='answers') {
				$stmt = $this->conn->prepare("SELECT DAYOFYEAR(FROM_UNIXTIME(time)) AS g_day, DATE_FORMAT(FROM_UNIXTIME(time), '%Y-%m-%d') AS g_date, COUNT(*) AS g_cant FROM answers GROUP BY DATE(FROM_UNIXTIME(time)) ORDER BY g_date DESC LIMIT ".$limit);
			}
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		public function get_categories($categories) {

			/* Function for getting topics for editing questions */

			$message="";
			if($categories!='' && $categories!=' ') {
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
					if($stmt->rowCount() != 0) {
						foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
							$message .= "<div class='question-category-box'>";
							$message .= "<label><input type='checkbox' id='cat-".$ii['id']."' name='categories[]' value='".$ii['id']."' checked='yes'><span>".$ii['name']." - ".$ii['followers']." ".get_text(118,1)."</span></label>";
							$message .= "</div>";
						}
					}
				}
			}
	        return $message;
	    }

	    public function select_category() {

	    	/* Function for selecting topics for editing questions */

	    	$message = "<select id='results' onchange='add_category_to_question_select(this.options[this.selectedIndex].getAttribute(\"id\"),this.options[this.selectedIndex].getAttribute(\"name\"),this.options[this.selectedIndex].getAttribute(\"followers\"))'>";
	    	$message .= "<option selected>".get_text(253,1)."</option>";
	    	$stmt = $this->conn->prepare("SELECT * FROM categories ORDER BY followers DESC");
			$stmt->execute();
			if($stmt->rowCount() != 0) {
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ii) {
					$message .= "<option value='".$ii['id']."' id='".$ii['id']."' name='".$ii['name']."' followers='".$ii['followers']."'>".$ii['name']." - ".$ii['followers']." ".get_text(118,1)."</option>";
				}
			}
	        $message.="</select>";
	        return $message;
	    }

	    public function delete_question($id) {

	    	/* Function for deleting question */

			if($id>0 && USER_RANK==1) {
				$stmt = $this->conn->prepare("SELECT * FROM questions WHERE id = ?");
				$stmt->execute(array($id));
				if($stmt->rowCount()!=0) {
					$data = $stmt->fetch(PDO::FETCH_ASSOC);
					$stmt = $this->conn->prepare("DELETE answers_likes FROM answers_likes INNER JOIN answers ON answers_likes.answer_id = answers.id WHERE answers.question_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE notifications FROM notifications INNER JOIN answers ON notifications.n_id = answers.id WHERE (notifications.type='1' OR notifications.type='2' OR notifications.type='4' OR notifications.type='7' OR notifications.type='8') AND answers.question_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE points_log FROM points_log INNER JOIN answers ON points_log.log_id = answers.id WHERE points_log.log_type='2' AND answers.question_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE reports FROM reports INNER JOIN answers ON reports.r_id = answers.id WHERE reports.type='2' AND answers.question_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE FROM questions WHERE id = ?");
					$stmt->execute(array($id));
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
					$stmt = $this->conn->prepare("INSERT INTO notifications (to_user, type, user_id, n_id, time, viewed, info) VALUES (?, ?, ?, ?, ?, ?, ?)");
					$stmt->execute(array($data['user_id'],'6',1,$id,time(),'0', $data['question']));
					if(engine::getuserdata($data['user_id'], "NOT_SYSTEM")==1 && engine::getuserdata($data['user_id'], "ONLINE")==0) {
						$user_question_link = engine::get_user_name($data['user_id']);
						$theme = get_text(460,1).' '.SITE_NAME.' - '.SITE_DESCRIPTION;
						$body = get_text(83,1).' '.$user_question_link.',<br><br> '.get_text(180,1).' <b>'.$data['question'].'</b> '.get_text(179,1).'.<br>'.get_text(77,1).' '.SITE_NAME.'<br><br>'.get_text(78,1).',<br>'.SITE_NAME.' - '.SITE_DESCRIPTION.'<br><br><small>'.get_text(79,1).'.</small>';
						$to = engine::getuserdata($data['user_id'],"EMAIL");
						engine::send_email($to,$theme,$body);
					}
					$_SESSION['msg'] = 42;
					engine::headerin("administration/manage_questions.php");
				}
				else {
					engine::headerin("administration/manage_questions.php");
				}
			}
			else {
				engine::headerin("administration/manage_questions.php");
			}
		}

		public function delete_answer($id) {

			/* Function for deleting answer */

			if($id>0 && USER_RANK==1) {
				$stmt = $this->conn->prepare("SELECT * FROM answers WHERE id = ?");
				$stmt->execute(array($id));
				if($stmt->rowCount()!=0) {
					$data = $stmt->fetch(PDO::FETCH_ASSOC);
					$stmt = $this->conn->prepare("UPDATE questions SET answers=answers-1 WHERE id = ?");
					$stmt->execute(array($data['question_id']));
					$stmt = $this->conn->prepare("DELETE FROM answers WHERE id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE FROM answers_likes WHERE answer_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE FROM notifications WHERE (type='1' OR type='2' OR type='4' OR type='7' OR type='8') AND n_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE FROM points_log WHERE log_type='2' AND log_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("DELETE FROM reports WHERE type='2' AND r_id = ?");
					$stmt->execute(array($id));
					$stmt = $this->conn->prepare("INSERT INTO notifications (to_user, type, user_id, n_id, time, viewed, info) VALUES (?, ?, ?, ?, ?, ?, ?)");
					$stmt->execute(array($data['user_id'],'8',1,$id,time(),'0', $data['answer']));
					if(engine::getuserdata($data['user_id'], "NOT_SYSTEM")==1 && engine::getuserdata($data['user_id'], "ONLINE")==0) {
						$user_answer_link = engine::get_user_name($data['user_id']);
						$theme = get_text(461,1).' '.SITE_NAME.' - '.SITE_DESCRIPTION;
						$body = get_text(83,1).' '.$user_answer_link.',<br><br> '.get_text(182,1).' <b>'.$data['answer'].'</b> '.get_text(179,1).'.<br>'.get_text(77,1).' '.SITE_NAME.'<br><br>'.get_text(78,1).',<br>'.SITE_NAME.' - '.SITE_DESCRIPTION.'<br><br><small>'.get_text(79,1).'.</small>';
						$to = engine::getuserdata($data['user_id'],"EMAIL");
						engine::send_email($to,$theme,$body);
					}
					$_SESSION['msg'] = 52;
					engine::headerin("administration/manage_answers.php");
				}
				else {
					engine::headerin("administration/manage_answers.php");
				}
			}
			else {
				engine::headerin("administration/manage_answers.php");
			}
		}
	}

?>