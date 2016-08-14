<?php

    /* Category page with questions:    category/... */

    require_once dirname(dirname(dirname(__FILE__)))."/core.php";
    $category_data = $engine_session->get_category($_GET['url']);

    if($category_data['id']!='') {
        $data['c_id'] = $category_data['id'];
        $data['c_url'] = $category_data['url'];
        $data['c_name'] = $category_data['name'];
        $data['c_image'] = $category_data['image'];
        $data['c_description'] = $category_data["description"];
        $data['c_followers'] = $category_data["followers"];
        $data['c_button'] = $engine_session->get_follow_button_in_category($category_data['id'],$category_data["followers"]);
        $data['total_questions'] = $engine_session->category_questions($category_data['id']);
    }

    if($category_data['id']!='') {
        $data['title'] = $category_data["name"];
        $data['description'] = $category_data["description"];
        $data['js'] = "category_questions(".$category_data['id'].");";
        $template_session->loadtpl("head", $data);
        $template_session->loadtpl("header");
        $template_session->loadtpl("category", $data);
    }     
    else {
        $data['title'] = get_text(216,1);
        $template_session->loadtpl("head", $data);
        $template_session->loadtpl("header");
        $template_session->loadtpl("error"); 
    }
    $template_session->loadend();