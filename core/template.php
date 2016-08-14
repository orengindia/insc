<?php

	/* template.php - class used for loading tpl files */

	class template {

		public function loadtpl($template, $data = null) {

			/* 
			 * Function for loading template file. 
			 * $template - name of template from template folder without extension.
			 * $data - array of variables which should be loaded into tpl file. */

			if(is_array($data)) {
				extract($data);
			}

			require_once dirname(dirname(__FILE__)).'/template/tpl/'.$template.'.tpl';
		}
		
		public function loadend() {
			/* Function for loading bottom of each page */
			echo '</body>';
			echo '</html>';
		}
		
		public function showerror($error_title, $error_message) {
			/* Function for showing errors */
			echo '<center><h1>'.$error_title.'</h1><p>'.$error_message.'</p></center>';
		}
	}