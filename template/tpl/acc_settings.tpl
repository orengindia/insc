        <div class="container stream">
            <div class="visible-xs col-xs-12 visible-lg col-lg-2 menu">
                <h3 class="title"><?php get_text(256)?></h3>
                <a href="site/settings"><i class="fa fa-angle-double-right"></i> <?php get_text(105)?></a>
                <a href="site/profile"><?php get_text(287)?></a>
                <a href="site/notification"><?php get_text(102)?></a>
                <a href="site/disable"><?php get_text(91)?></a>
            </div>

            <div class="col-xs-12 col-sm-8 col-lg-7 settings-block">
                <h3 class="title"><?php get_text(105)?></h3>

                <?php switch($_SESSION['msg']) {
                        case 1: echo('<h3 class="title" style="color:green"><i class="fa fa-check-circle-o"></i> <b>'.get_text(92,1).'</b> '.get_text(93,1).'</h3>'); break;
                        case 2: echo('<h3 class="title" style="color:red"><i class="fa fa-exclamation-circle"></i> <b>'.get_text(25,1).':</b> '.get_text(274,1).'</h3>'); break;
                        case 3: echo('<h3 class="title" style="color:red"><i class="fa fa-exclamation-circle"></i> <b>'.get_text(25,1).':</b> '.get_text(140,1).'</h3>'); break;
                        case 403: echo('<h3 class="title" style="color:red"><i class="fa fa-exclamation-circle"></i> <b>'.get_text(25,1).':</b> '.get_text(275,1).'</h3>'); break;
                        case 405: echo('<h3 class="title" style="color:red"><i class="fa fa-exclamation-circle"></i> <b>'.get_text(25,1).':</b> '.get_text(124,1).'</h3>'); break;
                    }
                    unset($_SESSION['msg']); 
                ?>

                <form method="post" action="site/settings" id="settingsForm" onsubmit="saveSettings(); return false;">
                    <input type="hidden" name="settingsForm" value="1">
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control text" id="username" name="username" value="<?php echo $settings_username; ?>" disabled="true" style="background-color:#eee;cursor:not-allowed">
                        </div>
                        <div class="col-sm-6">
                            <label for="username"><span class="supporting"><?php get_text(281)?></span></label>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control text" id="name" name="name" placeholder="<?php get_text(50)?>" value="<?php echo $settings_name; ?>">
                        </div>
                        <div class="col-sm-6">
                            <label for="name"><span class="supporting"><?php get_text(282)?></span></label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control text" id="email" name="email" placeholder="<?php get_text(51)?>" value="<?php echo $settings_email; ?>">
                            <small id="emailStatus" style="color:red"></small>
                        </div>
                        <div class="col-sm-6">
                            <label for="email"><span class="supporting"><?php get_text(283)?></span></label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control text" id="website" name="website" placeholder="<?php get_text(276)?>" value="<?php echo $settings_website; ?>">
                        </div>
                        <div class="col-sm-6">
                            <label for="website"><span class="supporting"><?php get_text(276)?></span></label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <select class="form-control text" id="country" name="country" style="height:32px;background-color:#fff">
                                <option><?php get_text(52)?></option>
                                <?php echo $countries; ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="country"><span class="supporting"><?php get_text(58)?></span></label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control text" id="bio" name="bio" data-maxsize="50" data-output="numcounts" placeholder="<?php get_text(277)?>" value="<?php echo $settings_bio; ?>">
                        </div>
                        <div class="col-sm-6">
                            <label for="bio"><span class="supporting"><?php get_text(284)?> [<span id="numcounts" style="color:red"></span>]</span></label>
                        </div>
                    </div>

                    <div class="row" id="changePasswordLink">
                        <div class="col-sm-6">
                            <?php if($settings_pass!=0) { ?>
                                <a href="#" class="btn" onclick="changePassword(); return false;"><?php get_text(278)?></a>
                            <?php } else { ?>
                                <a href="#" class="btn" onclick="changePassword(); return false;"><?php get_text(279)?></a>
                            <?php } ?>
                        </div>
                        <div class="col-sm-6">
                            <?php if($settings_pass!=0) { ?>
                                <span class="supporting"><?php get_text(285)?></span>
                            <?php } else { ?>
                                <span class="supporting"><?php get_text(286)?></span>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="row" id="changePassword" style="display:none">
                        <div class="col-sm-6">
                           <?php if($settings_pass!=0) { ?>
                                <input type="password" class="form-control text" name="password" id="password" autocomplete="off" style="display:none" placeholder="<?php get_text(280)?>">
                            <?php } else { ?>   
                                <input type="password" class="form-control text" name="password" id="password" autocomplete="off" style="display:none" placeholder="<?php get_text(94)?>">
                            <?php } ?>
                            <span id="passwordStatus"></span>
                        </div>
                        <div class="col-sm-6">
                             <?php if($settings_pass!=0) { ?>
                                <label for="password"><span class="supporting"><?php get_text(280)?></span></label>
                            <?php } else { ?>
                                <label for="password"><span class="supporting"><?php get_text(94)?></span></label>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="row" id="changePassword2" style="display:none">
                        <div class="col-sm-6">
                           <?php if($settings_pass!=0) { ?>
                                <input type="password" class="form-control text" name="password2" id="password2" autocomplete="off" style="display:none" placeholder="<?php get_text(94)?>">
                                <span id="password2Status"></span>
                            <?php } else { ?>
                                <input type="password" class="form-control text" name="password2" id="password2" autocomplete="off" style="display:none" placeholder="<?php get_text(49)?>">
                                <span id="password2Status"></span>
                            <?php } ?>
                            <span id="password2Status"></span>
                        </div>
                        <div class="col-sm-6">
                            <?php if($settings_pass!=0) { ?>
                                <label for="password2"><span class="supporting"><?php get_text(94)?></span></label>
                            <?php } else { ?>
                                <label for="password2"><span class="supporting"><?php get_text(49)?></span></label>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 text-right">
                            <input id="saveSettingsButton" class="btn btn-primary" type="submit" name="submit" value="<?php get_text(117)?>">
                        </div>
                        <div class="col-sm-6">
                            <i class="fa fa-spinner fa-spin indicator" id="saveSettingsIndicator" style="display:none;"></i>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xs-12 col-sm-4 col-lg-3">
                <div class="menu hidden-lg hidden-xs">
                    <h3 class="title"><?php get_text(256)?></h3>
                    <a href="site/settings"><i class="fa fa-angle-double-right"></i> <?php get_text(105)?></a>
                    <a href="site/profile"><?php get_text(287)?></a>
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