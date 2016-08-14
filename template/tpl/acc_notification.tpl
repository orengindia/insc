        <div class="container stream">
            <div class="visible-xs col-xs-12 visible-lg col-lg-2 menu">
                <h3 class="title"><?php get_text(256)?></h3>
                <a href="site/settings"><?php get_text(105)?></a>
                <a href="site/profile"><?php get_text(287)?></a>
                <a href="site/notification"><i class="fa fa-angle-double-right"></i> <?php get_text(102)?></a>
                <a href="site/disable"><?php get_text(91)?></a>
            </div>

            <div class="col-xs-12 col-sm-8 col-lg-7 settings-block">
                <h3 class="title"><?php get_text(102)?></h3>

                <?php switch($_SESSION['msg']) {
                        case 1: echo('<h3 class="title" style="color:green"><i class="fa fa-check-circle-o"></i> <b>'.get_text(92,1).'</b> '.get_text(93,1).'</h3>'); break;
                    }
                    unset($_SESSION['msg']);
                ?>

                <small class="text-center"><?php get_text(300)?></small>

                <form method="POST" action="site/notification" id="notificationForm">
                    <input type="hidden" name="settingsForm" value="1">
                    <div class="row">
                        <div class="col-sm-6 col-xs-3 text-right">
                            <input type="checkbox" name="not_upvote" class="form-control" id="not_upvote" <?php if($not_upvote==1) echo "checked"; ?>>
                        </div>
                        <div class="col-sm-6 col-xs-9">
                            <label for="not_upvote"><span class="supporting"><?php get_text(301)?></span></label>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-6 col-xs-3 text-right">
                            <input type="checkbox" name="not_answer" class="form-control" id="not_answer" <?php if($not_answer==1) echo "checked"; ?>>
                        </div>
                        <div class="col-sm-6 col-xs-9">
                            <label for="not_answer"><span class="supporting"><?php get_text(302)?></span></label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-xs-3 text-right">
                            <input type="checkbox" name="not_follower" class="form-control" id="not_follower" <?php if($not_follower==1) echo "checked"; ?>>
                        </div>
                        <div class="col-sm-6 col-xs-9">
                            <label for="not_follower"><span class="supporting"><?php get_text(303)?></span></label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-xs-3 text-right">
                            <input type="checkbox" name="not_mention" class="form-control" id="not_mention" <?php if($not_mention==1) echo "checked"; ?>>
                        </div>
                        <div class="col-sm-6 col-xs-9">
                            <label for="not_mention"><span class="supporting"><?php get_text(304)?></span></label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-xs-3 text-right">
                            <input type="checkbox" name="not_system" class="form-control" id="not_system" <?php if($not_system==1) echo "checked"; ?>>
                        </div>
                        <div class="col-sm-6 col-xs-9">
                            <label for="not_system"><span class="supporting"><?php get_text(305)?></span></label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-xs-3 text-right">
                            <input id="saveNotificationButton" class="btn btn-primary" type="submit" name="submit" value="<?php get_text(117)?>">
                        </div>
                        <div class="col-sm-6 col-xs-9">
                            <i class="fa fa-spinner fa-spin indicator" id="saveSettingsIndicator" style="display:none;"></i>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xs-12 col-sm-4 col-lg-3">
                <div class="menu hidden-lg hidden-xs">
                    <h3 class="title"><?php get_text(256)?></h3>
                    <a href="site/settings"><?php get_text(105)?></a>
                    <a href="site/profile"><?php get_text(287)?></a>
                    <a href="site/notification"><i class="fa fa-angle-double-right"></i> <?php get_text(102)?></a>
                    <a href="site/disable"><?php get_text(91)?></a>
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