		<div class="container stream">
			<div class="visible-xs col-xs-12 visible-lg col-lg-2 menu">
				<h3 class="title"><?php get_text(187)?></h3>
				<a href="site/stream"><i class="fa fa-angle-double-right"></i> <?php get_text(191)?></a>
				<?php if(USER_ID) { ?>
					<a href="site/stream/followed"><i class="fa fa-angle-double-right"></i> <?php get_text(192)?></a>
					<a href="site/stream/popular"><i class="fa fa-angle-double-right"></i> <?php get_text(193)?></a>
				<?php } ?>
				<a href="site/stream/unaswered"><i class="fa fa-angle-double-right"></i> <?php get_text(194)?></a>
				<a href="site/stream/aswered"><i class="fa fa-angle-double-right"></i> <?php get_text(195)?></a>
			</div>

			<div class="col-xs-12 col-sm-8 col-lg-7">
				<h3 class="title">
					<?php
						switch($type){
							case 0:		get_text(194); 
										echo ' '; 
										get_text(196);
										break;

							case 1:		get_text(195);
										echo ' ';
										get_text(196);
										break;

							case 2:		get_text(197);
										break;

							case 3:		get_text(198);
										break;

							case 4:		get_text(191);
										break;
						}
					?>
					<a href="site/topics" style="float: right;"<span style="color:green"><i class="fa fa-tasks"></i> <?php get_text(200)?></span></a>
				</h3>
				<input type="hidden" id="first_value" name="first_value" value="">
				<input type="hidden" id="last_value" name="last_value" value="99999999999">
				<input type="hidden" id="l_v" name="last_value" value="99999999999">
				<input type="hidden" id="remain" name="remain" value="">
				<div class="MoreStoriesIndicator none" onclick="new_stream(<?php echo $type; ?>);">
					<span class="text"><i class="fa fa-refresh"></i> <?php get_text(199)?></span>
				</div>
			    <div id="stream"></div>
			    <div class="loadingstream"><center><i class="fa fa-spinner fa-spin fa-2x end_stream" style="color:grey"></i></center></div>
			</div>

			<div class="col-xs-12 col-sm-4 col-lg-3">
				<div class="menu hidden-lg hidden-xs">
					<h3 class="title"><?php get_text(187)?></h3>
					<a href="site/stream"><i class="fa fa-angle-double-right"></i> <?php get_text(191)?></a>
					<?php if(USER_ID) { ?>
						<a href="site/stream/followed"><i class="fa fa-angle-double-right"></i> <?php get_text(192)?></a>
						<a href="site/stream/popular"><i class="fa fa-angle-double-right"></i> <?php get_text(193)?></a>
					<?php } ?>
					<a href="site/stream/unaswered"><i class="fa fa-angle-double-right"></i> <?php get_text(194)?></a>
					<a href="site/stream/aswered"><i class="fa fa-angle-double-right"></i> <?php get_text(195)?></a>
				</div>

				<h3 class="title"><?php get_text(185)?></h3>
				<br>
				<?php if(SITE_ADSENSE!='') { echo SITE_ADSENSE; } else { echo '<img class="ad-image" src="https://placehold.it/300x250">'; } ?>
				<br>
				<h3 class="title"><?php get_text(186)?></h3>
				<div class="right-content">
					<div class="right-links">
						<ul>
							<li><a href="site/contact"><?php get_text(37)?></a></li>
							<li><a href="site/points"><?php get_text(39)?></a></li>
							<li><a href="site/about"><?php get_text(190)?></a></li>
							<li><a href="site/privacy"><?php get_text(40)?></a></li>
							<li><a href="site/terms"><?php get_text(61)?></a></li>
							<li><a href="site/people"><?php get_text(155)?></a></li>
		                  	<li><a href="site/topics"><?php get_text(189)?></a></li>
		                  	<li><p>© <?php echo date("Y"); ?>, <?php echo SITE_DOMAIN; ?></p></li>
						</ul>
					</div>
				</div>
			</div>
		</div>