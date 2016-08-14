		<div class="container stream">
			<div class="visible-xs col-xs-12 visible-lg col-lg-2 menu">
				<h3 class="title"><?php get_text(187)?></h3>
				<a href="site/notifications"><i class="fa fa-angle-double-right"></i> <?php get_text(188)?></a>
				<a href="site/notifications/upvotes"><i class="fa fa-angle-double-right"></i> <?php get_text(168)?></a>
				<a href="site/notifications/answers"><i class="fa fa-angle-double-right"></i> <?php get_text(169)?></a>
				<a href="site/notifications/followers"><i class="fa fa-angle-double-right"></i> <?php get_text(170)?></a>
				<a href="site/notifications/mentions"><i class="fa fa-angle-double-right"></i> <?php get_text(171)?></a>
				<a href="site/notifications/system"><i class="fa fa-angle-double-right"></i> <?php get_text(172)?></a>
			</div>

			<div class="col-xs-12 col-sm-8 col-lg-7">
				<h3 class="title">
					<?php echo TYPE; ?> <?php get_text(102)?>
					<a href="#" style="float: right;" onclick="readed_notifications(<?php echo $n_t; ?>); return false;"><span style="color:green"><i class="fa fa-check-square-o"></i> <?php get_text(184)?></span></a>
				</h3>
				
				<input type="hidden" id="last_value" name="last_value" value="999999999">
				<input type="hidden" id="remain" name="remain" value="">
	    		<div id="notifications"></div>
	    		<div class="loadingstream"><center><i class="fa fa-spinner fa-spin fa-2x" style="color:grey"></i></center></div>
			</div>

			<div class="col-xs-12 col-sm-4 col-lg-3">
				<div class="menu hidden-lg hidden-xs">
					<h3 class="title"><?php get_text(187)?></h3>
					<a href="site/notifications"><i class="fa fa-angle-double-right"></i> <?php get_text(188)?></a>
					<a href="site/notifications/upvotes"><i class="fa fa-angle-double-right"></i> <?php get_text(168)?></a>
					<a href="site/notifications/answers"><i class="fa fa-angle-double-right"></i> <?php get_text(169)?></a>
					<a href="site/notifications/followers"><i class="fa fa-angle-double-right"></i> <?php get_text(170)?></a>
					<a href="site/notifications/mentions"><i class="fa fa-angle-double-right"></i> <?php get_text(171)?></a>
					<a href="site/notifications/system"><i class="fa fa-angle-double-right"></i> <?php get_text(172)?></a>
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