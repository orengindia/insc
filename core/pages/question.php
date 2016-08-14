<?php

    /* Question page:   q/... */

    require_once dirname(dirname(dirname(__FILE__)))."/core.php";

    $question_url = $_GET['url'];
    $question_data = $engine_session->get_question($question_url);

    if($question_data['id']!='') {
        $engine_session->saveview($question_data['id']);

        $data['user_name'] = $engine_session->getuserdata(USER_ID,"NAME");
        $data['user_info'] = $engine_session->getuserdata(USER_ID,"BIO");
        $data['q_id'] = $question_data['id'];
        $data['q_url'] = $question_data['url'];
        $data['q_answers'] = $question_data['answers'];
        $data['q_views'] = $question_data['views']+1;
        $data['q_time'] = get_time($question_data["time_asked"]);
        $data['q_question'] = nl2br($question_data["question"]);
        $data['q_description'] = nl2br(txt2link(format_text($question_data["description"])));
        $user = $engine_session->getuserdata($question_data["user_id"],"*");
        $data['user_asked'] = $user['username'];
        $data['q_categories'] = $engine_session->get_question_categories($question_data['category']);
        if($question_data['image']!='') {
            $data['q_image'] = '<a href="media/images/users/'.$question_data['image'].'" class="image-link"><img src="media/images/users/'.$question_data['image'].'" class="question-image"></a>';
        }
        else {
            $data['q_image'] = '';
        }
        $data['q_likes'] = $engine_session->getlikes($question_data["id"],$question_data["user_id"], 0);
        if($question_data['answers']>0) {
            $data['q_w_answers'] = get_text(224,1);
        }
        else {
            $data['q_w_answers'] = get_text(225,1);
        }
     
        $data['number_want_answers'] = $engine_session->count_want_answers($question_data['id']);
        $data['want_answers'] = $engine_session->want_answers($question_data['id']);
        $data['last_want_answers'] = $engine_session->last_want_answers($question_data['id']);

        if($question_data["user_id"]==USER_ID && SESSION_STATUS!=false) { 
            $message = '<div class="action_item" onclick="q_delete_r('.$data['q_id'].');" style="cursor:pointer"><a class="downvote">'.get_text(226,1).'</a></div>';
            $message .= '<div class="action_item" style="cursor:pointer" data-toggle="modal" data-target="#editqModal" data-id="'.$data['q_id'].'"><a class="downvote">'.get_text(227,1).'</a></div>';
            $message .= '<input type="hidden" id="db_qq_'.$data['q_id'].'" value="'.htmlentities($question_data['question'], ENT_QUOTES, "UTF-8").'">';
            $message .= '<input type="hidden" id="db_qd_'.$data['q_id'].'" value="'.htmlentities($question_data['description'], ENT_QUOTES, "UTF-8").'">';
            $message .= '<input type="hidden" id="db_qc_'.$data['q_id'].'" value="'.$question_data['category'].'">';
            $data['is_author'] = $message;
        }

        $time = get_time($user['visit']);
        $now = get_text(7,1);
        $somesecondsago = get_text(1,1);
        if($time==$now || strlen(strstr($time,$somesecondsago))>0) {
          define("ONLINE", get_text(7,1).' '.get_text(228,1));
        } 
        else {
          define("ONLINE", get_text(229,1).' '.$time);
        }
    }

    if($question_data['id']!='') {
        $data['title'] = $question_data["question"];
        $data['description'] = substr(trim(preg_replace('/\s\s+/', ' ', $question_data["description"])), 0, 155);
        $data['js'] = "get_answers(".$data['q_id'].");";

        $template_session->loadtpl("head", $data);
        $template_session->loadtpl("header");
        $template_session->loadtpl("question", $data);
    }
    else {
        $data['title'] = get_text(230,1);
        $template_session->loadtpl("head", $data); 
        $template_session->loadtpl("header"); 
        $template_session->loadtpl("error");
    }
    $template_session->loadend();