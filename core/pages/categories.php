<?php

  /* Topics page:   site/topics */
  
  require_once dirname(dirname(dirname(__FILE__)))."/core.php";

  $data['title'] = get_text(208,1);
  $data['all_categories'] = $engine_session->all_categories();
  $data['info'] = $engine_session->topics_information();
    
  $template_session->loadtpl("head", $data);
  $template_session->loadtpl("header");
  $template_session->loadtpl("categories", $data);
  $template_session->loadend();