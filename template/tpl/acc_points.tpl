        <div class="container stream">
            <div class="visible-xs col-xs-12 visible-lg col-lg-2 menu">
                <h3 class="title"><?php get_text(242)?></h3>
                <div id="imageform">
                    <a href="media/images/users/<?php echo $photo_link; ?>" class="profile-photo image-link">
                        <div class="img imgLiquid img-thumbnail" style="width:120px; height:120px; margin: auto 0; border-radius:15px">
                            <img src="media/images/users/<?php echo $profile_photo; ?>">
                        </div>
                    </a>
                </div>
                <a href="<?php echo $profile_user; ?>">@<?php echo $profile_user; ?></a>
                <div class="profile-counters">
                    <a href="<?php echo $profile_user; ?>">
                        <span class="profile-count-label"><?php echo $follows; ?></span>
                        <span class="profile-info-label"><?php get_text(189)?></span>
                    </a>
                    <a href="<?php echo $profile_user; ?>/answers">
                        <span class="profile-count-label"><?php echo $answers; ?></span>
                        <span class="profile-info-label"><?php get_text(169)?></span>
                    </a>
                    <a href="<?php echo $profile_user; ?>/questions">
                        <span class="profile-count-label"><?php echo $questions; ?></span>
                        <span class="profile-info-label"><?php echo ucfirst(get_text(196,1)); ?></span>
                    </a>
                    <a href="<?php echo $profile_user; ?>/points">
                        <span class="profile-count-label"><?php echo $points; ?></span>
                        <span class="profile-info-label"><i class="fa fa-angle-double-right"></i> <?php get_text(39)?></span>
                    </a>
                </div>
            </div>

            <div class="col-xs-12 col-sm-8 col-lg-7">
                <h3 class="title">
                    <?php if($verified==1) {?>
                        <i class="fa fa-check-circle" style="color:#449d44;" title="<?php get_text(269)?>"></i>
                    <?php } ?>
                    <?php echo $profile_name; ?> <?php get_text(39)?>
                </h3>

                <input type="hidden" id="last_value" name="last_value" value="999999999">
                <input type="hidden" id="remain" name="remain" value="">
                <div id="points"></div>
                <div class="loadingstream"><center><i class="fa fa-spinner fa-spin fa-3x" style="color:grey"></i></center></div>
            </div>

            <div class="col-xs-12 col-sm-4 col-lg-3">
                <div class="menu hidden-lg hidden-xs text-center">
                    <h3 class="title"><?php get_text(242)?></h3>
                    <div id="imageform">
                        <a href="media/images/users/<?php echo $photo_link; ?>" class="profile-photo image-link">
                            <div class="img imgLiquid img-thumbnail" style="width:120px; height:120px; margin: auto 0; border-radius:15px">
                                <img src="media/images/users/<?php echo $profile_photo; ?>">
                            </div>
                        </a>
                    </div>
                    <a href="<?php echo $profile_user; ?>">@<?php echo $profile_user; ?></a>
                    <div class="profile-counters">
                        <a href="<?php echo $profile_user; ?>">
                            <span class="profile-count-label"><?php echo $follows; ?></span>
                            <span class="profile-info-label"><?php get_text(189)?></span>
                        </a>
                        <a href="<?php echo $profile_user; ?>/answers">
                            <span class="profile-count-label"><?php echo $answers; ?></span>
                            <span class="profile-info-label"><?php get_text(169)?></span>
                        </a>
                        <a href="<?php echo $profile_user; ?>/questions">
                            <span class="profile-count-label"><?php echo $questions; ?></span>
                            <span class="profile-info-label"><?php echo ucfirst(get_text(196,1)); ?></span>
                        </a>
                        <a href="<?php echo $profile_user; ?>/points">
                            <span class="profile-count-label"><?php echo $points; ?></span>
                            <span class="profile-info-label"><i class="fa fa-angle-double-right"></i> <?php get_text(39)?></span>
                        </a>
                    </div>
                </div>

                <h3 class="title">
                    <?php get_text(38)?>
                    <?php if(SESSION_STATUS==true && PROFILE_ID==USER_ID) { ?>
                        <a href="site/settings" style="float: right;"<span style="color:green"><i class="fa fa-cogs"></i> <?php get_text(268)?></span></a>
                    <?php } ?>
                </h3>
                <div class="about-user">
                    <?php if($profile_bio!="" && $profile_bio!=" ") { ?>
                        <p><i class="fa fa-info-circle"></i> <?php echo $profile_bio; ?></p>
                    <?php } ?>
                    <?php if($profile_web!="" && $profile_web!=" ") { ?>
                        <p><a href="<?php echo $profile_web; ?>" target="_blank" rel="nofollow"><i class="fa fa-globe"></i> <?php echo $profile_web; ?></a></p>
                    <?php } ?>
                    <p>
                        <?php if($profile_country!="") { ?>
                            <i class="fa fa-location-arrow"></i> <?php get_text(272)?> <?php echo $profile_country; ?> 
                        <?php } ?>
                    </p>
                    <p>
                        <?php 
                            $time = get_time($profile_visit);
                            $now = get_text(7,1);
                            $somesecondsago = get_text(1,1);
                            if($time==$now || strlen(strstr($time,$somesecondsago))>0) { ?>
                                 <i class="fa fa-circle" style="color:#449d44"></i> <?php get_text(228)?>
                            <?php } else { ?>
                                <i class="fa fa-circle" style="color:red"></i> <?php get_text(270)?> <?php echo get_time($profile_visit); ?>
                        <?php } ?>
                    </p>
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