        <div class="container stream">
            <div class="col-xs-12 col-sm-8 col-lg-8">
                <h3 class="title"><?php echo $search_word; ?></h3>
                <div class="search-results">
                    <?php echo $results; ?>
                </div>
            </div>

            <div class="col-xs-12 col-sm-4 col-lg-4">
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