        <div class="container stream">
            <div class="visible-xs col-xs-12 visible-lg col-lg-2 menu">
                <h3 class="title"><?php get_text(256)?></h3>
                <a href="site/settings"><?php get_text(105)?></a>
                <a href="site/profile"><i class="fa fa-angle-double-right"></i> <?php get_text(287)?></a>
                <a href="site/notification"><?php get_text(102)?></a>
                <a href="site/disable"><?php get_text(91)?></a>
            </div>

            <div class="col-xs-12 col-sm-8 col-lg-7 settings-block">
                <h3 class="title"><?php get_text(287)?></h3>

                <?php switch($_SESSION['msg']) {
                        case 1: echo('<h3 class="title" style="color:red"><i class="fa fa-exclamation-circle"></i> <b>'.get_text(25,1).':</b> '.get_text(288, 1).'</h3>'); break;
                        case 2: echo('<h3 class="title" style="color:red"><i class="fa fa-exclamation-circle"></i> <b>'.get_text(25,1).':</b> '.get_text(289, 1).'</h3>'); break;
                        case 3: echo('<h3 class="title" style="color:red"><i class="fa fa-exclamation-circle"></i> <b>'.get_text(25,1).':</b> '.get_text(290, 1).'</h3>'); break;
                        case 4: echo('<h3 class="title" style="color:red"><i class="fa fa-exclamation-circle"></i> <b>'.get_text(25,1).':</b> '.get_text(291, 1).'</h3>'); break;
                        case 5: echo('<h3 class="title" style="color:red"><i class="fa fa-exclamation-circle"></i> <b>'.get_text(25,1).':</b> '.get_text(292, 1).'</h3>'); break;
                        case 6: echo('<h3 class="title" style="color:green"><i class="fa fa-check-circle-o"></i> <b>'.get_text(92,1).'</b> '.get_text(93,1).'</h3>'); break;
                    }
                    unset($_SESSION['msg']);
                ?>

                <form method="POST" action="site/profile" class="form-horizontal" enctype="multipart/form-data" id="profileForm">
                    <input type="hidden" name="img" value="photo">
                    <div class="row">
                        <div class="col-sm-6">
                            <span class="supporting"><?php get_text(293)?></span>
                            <small class="supporting"><?php get_text(295)?>: <i><?php echo PHOTO_MAX_SIZE ?></i>KB<br><?php get_text(296)?><br><?php get_text(297)?> <i><?php echo PHOTO_MAX_WIDTH ?></i>px <br> <?php get_text(298)?>  <i><?php echo PHOTO_MAX_HEIGHT ?></i>px</small>
                        </div>
                        <div class="col-sm-6">
                            <?php if($photo_url=="") { ?>
                              <img src="media/images/users/photo_default.png" alt="photo" width="75">
                            <?php } else { ?>
                              <img src="media/images/users/<?php echo $photo_url ?>" alt="photo" width="75">
                            <?php } ?>
                            <div id="photoPreview">
                                <a id="deletePhotoLink" href="#" onclick="deletePhoto(); return false"><i class="fa fa-camera"></i> <?php get_text(294)?></a>
                            </div>
                            <div id="photoUpload" style="display:none">
                                <input type="file" name="photo" id="photo" size="30">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">

                        </div>
                        <div class="col-sm-6">
                            <input id="saveProfileButton" type="submit" value="<?php get_text(137)?>" class="btn btn-primary">
                        </div>
                    </div>
                </form>

                <form method="POST" action="site/profile" class="form-horizontal" enctype="multipart/form-data" id="profileForm">
                    <input type="hidden" name="img" value="deletephoto">
                     <div class="row">
                        <div class="col-sm-6">
                            <small><?php get_text(299)?></small>
                        </div>
                        <div class="col-sm-6">
                            <input id="saveProfileButton" type="submit" value="<?php get_text(226)?>" class="btn btn-danger">
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xs-12 col-sm-4 col-lg-3">
                <div class="menu hidden-lg hidden-xs">
                    <h3 class="title"><?php get_text(256)?></h3>
                    <a href="site/settings"><?php get_text(105)?></a>
                    <a href="site/profile"><i class="fa fa-angle-double-right"></i> <?php get_text(287)?></a>
                    <a href="site/notification"><?php get_text(102)?></a>
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