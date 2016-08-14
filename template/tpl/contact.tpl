        <div class="container stream">
            <div class="visible-xs col-xs-12 visible-lg col-lg-2 menu">
                <h3 class="title"><?php get_text(256)?></h3>
                <a href="site/about"><?php get_text(38)?></a>
                <a href="site/contact"><i class="fa fa-angle-double-right"></i> <?php get_text(148)?></a>
                <a href="site/points"><?php get_text(39)?></a>
                <a href="site/people"><?php get_text(155)?></a>
                <a href="site/privacy"><?php get_text(40)?></a>
                <a href="site/terms"><?php get_text(61)?></a>
            </div>

            <div class="col-xs-12 col-sm-8 col-lg-7">
                <h3 class="title"><?php get_text(148)?></h3>
                <h3><?php get_text(156)?></h3>
                <?php if($success==1) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php get_text(158)?>
                    </div>
                    <br>
                <?php } else { ?>
                    <small><?php get_text(157)?></small>
                    <center>
                        <form method="post" action="site/contact" id="contactForm">
                            <br>
                            <input type="text" name="name" id="name" placeholder="<?php get_text(159)?>" class="form-control text" required <?php if(SESSION_STATUS!='') { echo 'value="'.NAME.'"'; } ?>>
                            <br>
                            <input type="text" name="email" id="email" placeholder="<?php get_text(160)?>" class="form-control text" required <?php if(SESSION_STATUS!='') { echo 'value="'.EMAIL.'"'; } ?>>
                            <br>
                            <input type="text" name="subject" id="subject" placeholder="<?php get_text(161)?>" class="form-control text" required>
                            <br>
                            <textarea name="message" id="message" placeholder="<?php get_text(162)?>" class="form-control text" required></textarea>
                            <br>
                            <input class="btn btn-primary" type="submit" name="submit" value="<?php get_text(136)?>">
                        </form>
                    </center>
                <?php } ?>
            </div>

            <div class="col-xs-12 col-sm-4 col-lg-3">
                <div class="menu hidden-lg hidden-xs">
                    <h3 class="title"><?php get_text(256)?></h3>
                    <a href="site/about"><?php get_text(38)?></a>
                    <a href="site/contact"><i class="fa fa-angle-double-right"></i> <?php get_text(148)?></a>
                    <a href="site/points"><?php get_text(39)?></a>
                    <a href="site/people"><?php get_text(155)?></a>
                    <a href="site/privacy"><?php get_text(40)?></a>
                    <a href="site/terms"><?php get_text(61)?></a>   
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