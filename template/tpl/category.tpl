        <div class="container stream">
            <div class="visible-xs col-xs-12 visible-lg col-lg-2 menu">
                <img src="media/images/categories/<?php echo $c_image; ?>" class="category-page-image">
                <?php if(USER_ID!='') { ?>
                    <div class="ActionBar TopicPage">
                        <?php echo $c_button; ?>
                    </div>
                <?php } ?>
                
                <h3 class="title"><?php get_text(217)?></h3>
                <p class="left-form-info"><?php get_text(218)?> <b><?php echo $total_questions; ?></b> <?php get_text(196)?>.</p>
            </div>

            <div class="col-xs-12 col-sm-8 col-lg-7">
                <h3 class="title"><?php echo $c_name; ?></h3>
                <input type="hidden" id="last_value" name="last_value" value="999999999">
                <input type="hidden" id="remain" name="remain" value="">
                <div class="category-center">
                    <span class="TopicWiki">
                        <p><?php echo $c_description; ?></p>
                    </span>
                </div>
                <br>
                <h3 class="title" style="margin-bottom:10px"><?php echo ucfirst(get_text(196,1)); ?></h3>
                <div id="questions"></div>
                <div class="loadingstream" style="margin:15px auto;">
                    <center><i class="fa fa-spinner fa-spin fa-3x" style="color:grey;"></i></center>
                </div>
            </div>

            <div class="col-xs-12 col-sm-4 col-lg-3">
                <div class="menu hidden-lg hidden-xs">
                    <img src="media/images/categories/<?php echo $c_image; ?>" class="category-page-image">
                    <?php if(USER_ID!='') { ?>
                        <div class="ActionBar TopicPage">
                            <?php echo $c_button; ?>
                        </div>
                    <?php } ?>
                    
                    <h3 class="title"><?php get_text(217)?></h3>
                    <p class="left-form-info"><?php get_text(218)?> <b><?php echo $total_questions; ?></b> <?php get_text(196)?>.</p>
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