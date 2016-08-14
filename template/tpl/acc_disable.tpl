        <div class="container stream">
            <div class="visible-xs col-xs-12 visible-lg col-lg-2 menu">
                <h3 class="title"><?php get_text(256)?></h3>
                <a href="site/settings"><?php get_text(105)?></a>
                <a href="site/profile"><?php get_text(287)?></a>
                <a href="site/notification"><?php get_text(102)?></a>
                <a href="site/disable"><i class="fa fa-angle-double-right"></i> <?php get_text(91)?></a>
            </div>

            <div class="col-xs-12 col-sm-8 col-lg-7 settings-block">
                <h3 class="title"><?php get_text(91)?></h3>

                <form method="post" action="site/disable" id="disform">
                    <div class="form_column" style="padding-left: 15px;">
                        <p class="supporting"><?php get_text(306)?>. <?php get_text(307)?>.</p>
                        <input type="hidden" name="disable" value="1">
                        <input type="submit" name="submit" value="<?php get_text(91)?>" class="btn btn-warning">
                    </div>
                </form>
            </div>

            <div class="col-xs-12 col-sm-4 col-lg-3">
                <div class="menu hidden-lg hidden-xs">
                    <h3 class="title"><?php get_text(256)?></h3>
                    <a href="site/settings"><?php get_text(105)?></a>
                    <a href="site/profile"><?php get_text(287)?></a>
                    <a href="site/notification"><?php get_text(102)?></a>
                    <a href="site/disable"><i class="fa fa-angle-double-right"></i> <?php get_text(91)?></a>
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