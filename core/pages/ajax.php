<?php

	/* Ajax request for url`s ajax/... */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";

	if(isset($_GET["cmd"])) {

		switch($_GET["cmd"]) {

			case "ask":						
										/* Function for saving question into database */
										$engine_session->ask($_POST["question-data"], $_POST["description-data"], $_FILES["image"], $_POST['categories']);
										break;

			case "sticker":				
										/* Verified profile sticker - added from admin panel */
										if($_POST['verified']!="") {
											$engine_session->verified($_POST["user"],$_POST['verified']);
										}
										break;

			case "like":					
										/* Want answers function for questions */
										$engine_session->like($_POST["id"]);
										break;

			case "get_points":				
										/* Get points log */
										$engine_session->get_points($_POST['user_id'], $_POST['last_id']);
										break;

			case "upvote":					
										/* Upvote answers function */
										$engine_session->upvote($_POST["id"]);
										break;

			case "toppeople":				
										/* Get list for top people */
										$engine_session->toppeople();
										break;

			case "notify":					
										/* Get live notifications */
										$engine_session->notify();
										break;

			case "get_notifications":		
										/* Get notifications on notification page via filter */
										$engine_session->get_notifications($_POST['type'], $_POST['last_id']);
										break;

			case "readed_notifications":	
										/* Make notifications readed for selected type */
										$engine_session->readed_notifications($_POST['type']);
										break;

			case "confirm":					
										/* Confirm account via email */
										$engine_session->account_confirm($_GET['code']);
										break;

			case "report":					
										/* Report function */
										$engine_session->report($_POST['r_type'], $_POST['r_id'], $_POST['r_reason']);
										break;

			case "stream":					
										/* Get questions on stream page. Option 0-all questions, 1-only user`s categories */
										$engine_session->stream($_POST['type'], $_POST['last_id']);
										break;

			case "get_category_questions":	
										/* Get questions on categories pages */
										$engine_session->get_category_questions($_POST['c_id'], $_POST['last_id']);
										break;

			case "get_users_questions":		
										/* Get questions on user`s pages */
										$engine_session->get_user_questions($_POST['user_id'], $_POST['last_id']);
										break;

			case "get_users_answers":		
										/* Get questions on user`s pages */
										$engine_session->get_user_answers($_POST['user_id'], $_POST['last_id']);
										break;

			case "get_answers":				
										/* Get question answers */
										$engine_session->get_answers($_POST['q_id'], $_POST['last_id']);
										break;

			case "edit_answer":				
										/* Edit and save answer */
										$engine_session->edit_answer($_POST['id'], $_POST['answer']);
										break;

			case "edit_question":			
										/* Edit and save question */
										$engine_session->edit_question($_POST['id_p'], $_POST['add_question_p'], $_POST['add_description_p'], $_FILES['image_p'], $_POST['categories']);
										break;

			case "getcategories":			
										/* Get categories found in question */
										$engine_session->get_categories($_POST['question']);
										break;

			case "getcategoriesbyid":		
										/* Get categories found in question by categories id */
										$engine_session->get_categories_by_id($_POST['categories']);
										break;

			case "answer":					
										/* Answer question function */
										$engine_session->answer($_POST["q_id"], $_POST["answer_text"]);
										break;

			case "checkusername":		
										/* Check if username is not used already */
										$engine_session->checkname(strtolower($_POST["username"]));
										break;   

			case "checkemail":				
										/* Check if email is not used already */
										$engine_session->checkemail(strtolower($_POST["email"]));
										break;

			case "checkquestion":			
										/* Find similar questions function */
										$engine_session->checkquestion($_POST["question"]);
										break;

			case "check_stream":			
										/* Check stream for new questions */
										$engine_session->check_stream($_POST["type"], $_POST["id"]);
										break;

			case "select_category":			
										/* List of all categories */
										$engine_session->select_category();
										break;

			case "select_category_p":		
										/* List of all categories for editing question */
										$engine_session->select_category_p();
										break;

			case "follow":					
										/* Follow category function */
										if($_POST["id"]!="" && USER_ID!='') { 
											$engine_session->follow($_POST['id']);
										}
										break;	

			case "unfollow":				
										/* Unfollow category function */
										if($_POST["id"]!="" && USER_ID!='') { 
											$engine_session->unfollow($_POST['id']);
										}
										break;

			case "search":					
										/* Live search function */
										$engine_session->ajax_search($_POST['query']);	
										break;

			case "tz": 						
										/* Save user timezone */
										session_start();
										$_SESSION['tzname'] = $_POST['tzname'];
										break;

			case "set_categories":			
										/* Set categories after "allcategories" pop-up */
										$engine_session->set_categories($_POST["categories"]);
										break;

			case "a_delete":				
										/* Function for deleting answers */
										$engine_session->a_delete($_POST["id"]);
										break;

			case "q_delete":				
										/* Function for deleting question */
										$engine_session->q_delete($_POST["id"]);
										break;

			default:						
										/* Error - refresh page */
										$engine_session->headerin();
										break;
		}
	}
	else {
		$engine_session->headerin();
	}
	
?>