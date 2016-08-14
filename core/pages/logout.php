<?php

	/* Page used for logout:	site/logout */

	require_once dirname(dirname(dirname(__FILE__)))."/core.php";
	$engine_session->clearcookies();