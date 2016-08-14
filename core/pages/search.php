<?php
	
	/* Search page:		site/search?q=... */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";

	if($_SERVER['REQUEST_METHOD']=="GET" && $_GET['q']!='') { 
		$data['search_word'] = $_GET["q"];
		$results = $engine_session->search($_GET["q"]);
		if($results) {
			$data['results'] = $results;
		}
		else {
			$data['results'] = get_text(243,1);
		}
	}
	else { 
		$data['results'] = get_text(243,1);
	}
	
	$data['title'] = get_text(90,1);
	$template_session->loadtpl("head", $data);  
	$template_session->loadtpl("header");  
	$template_session->loadtpl("search", $data);
	$template_session->loadend();