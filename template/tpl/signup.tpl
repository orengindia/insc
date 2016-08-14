        <div class="container-fluid">
            <div class="row-fluid">
                <div class="centering text-center">
                    <a href="">
                        <div class="logo"><?php echo SITE_NAME; ?></div>
                    </a>

                    <div class="tagline">
                        <?php echo SITE_DESCRIPTION; ?>
                    </div>

                    <div class="container sign-up-block">
                        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 block">
                            <?php if($_SESSION['msg']==11) { ?>
                                <?php unset($_SESSION['msg']); ?>
                                <center style="color:#555">
                                    <h3 style="padding-top: 40px; color:#3A64B5"><?php get_text(45)?><br><?php echo SITE_NAME; ?>!</h3>
                                    <p style="padding-bottom: 40px;"><?php get_text(46)?></p>
                                </center>
                            <?php } else if($_SESSION['msg']==12) { ?>
                                <?php unset($_SESSION['msg']); ?>
                                <center style="color:#555">
                                    <h3 style="padding-top: 40px; color:#3A64B5"><?php get_text(47)?></h3>
                                    <p style="padding-bottom: 20px;"><?php get_text(48)?></p>
                                    <p style="padding-bottom: 20px;"><a href="site/login" class="btn btn-primary"><?php get_text(9)?></a></p>
                                </center>
                            <?php } else { ?>
                                <form method="post" action="site/signup" id="setupForm" onsubmit="return setupComplete();" class="text-left">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control text" id="username" name="username" placeholder="<?php get_text(23)?>">
                                            <span id="usernameStatus"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="username"><span class="supporting"><?php get_text(54)?></span></label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="password" class="form-control text" id="password" name="password" placeholder="<?php get_text(24)?>">
                                            <span id="passwordStatus"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="password"><span class="supporting"><?php get_text(55)?></span></label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="password" class="form-control text" id="password2" name="password2" placeholder="<?php get_text(49)?>">
                                            <span id="passwordStatus"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="password2"><span class="supporting"><?php get_text(49)?></span></label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control text" id="name" name="name" placeholder="<?php get_text(50)?>">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="name"><span class="supporting"><?php get_text(56)?></span></label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control text" id="email" name="email" placeholder="<?php get_text(51)?>">
                                            <span id="emailStatus"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email"><span class="supporting"><?php get_text(57)?></span></label>
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
                                            <input type="text" class="form-control text" id="captcha" name="captcha" placeholder="Captcha">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="captcha"><img src='site/captcha' style="float:left;margin-right:5px;height:30px;margin-top:-3px"> <span class="supporting"><?php get_text(59)?></span></label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input class="btn btn-primary" type="submit" name="submit" value="<?php get_text(53)?>" style="width:100%">
                                        </div>
                                        <div class="col-sm-6">
                                            <?php get_text(60)?> <a href="site/terms" target="_blank"><?php get_text(61)?></a>
                                            <input type="checkbox" name="terms" id="terms" value="1" checked="checked" disabled>
                                        </div>
                                    </div>
                                </form>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="footer_nav">
                        <ul class="list-inline">
                            <li><a href="site/contact"><?php get_text(37)?></a></li>
                            <li><a href="site/about"><?php get_text(38)?></a></li>
                            <li><a href="site/points"><?php get_text(39)?></a></li>
                            <li><a href="site/privacy"><?php get_text(40)?></a></li>
                            <li><a href="site/terms"><?php get_text(36)?></a></li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <div id="errors">
            <?php switch ($_SESSION['msg']) {
                case 1: echo('<p class="error text-center"><b><i class="fa fa-exclamation-circle"></i> '.get_text(25,1).':</b> '.get_text(62,1).'</p>'); break;
                case 2: echo('<p class="error text-center"><b><i class="fa fa-exclamation-circle"></i> '.get_text(25,1).':</b> '.get_text(63,1).'</p>'); break;
                case 3: echo('<p class="error text-center"><b><i class="fa fa-exclamation-circle"></i> '.get_text(25,1).':</b> '.get_text(64,1).'</p>'); break;
            }
            unset($_SESSION['msg']); ?>
        </div>