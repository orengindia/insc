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
                            <form method="post" action="site/signup" id="setupForm" onsubmit="return socialComplete()" class="text-left">
                                <div class="row">
                                    <div style="position: relative; width: 180px; margin:10px auto">
                                        <?php if($_SESSION['regphoto_big']!="") { ?>
                                            <img src="<?php echo $_SESSION['regphoto_big']; ?>" style="width: 180px; height: 180px; display: block">
                                        <?php } ?>

                                        <?php if($_SESSION['regnetwork']=='facebook') { ?>
                                            <i class="fa fa-facebook sign-up-fb"></i>
                                        <?php } ?>

                                        <?php if($_SESSION['regnetwork']=='twitter') { ?>
                                            <i class="fa fa-twitter sign-up-twitter"></i>
                                        <?php } ?>
                                    </div>
                                </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control text" id="username" name="username" placeholder="<?php get_text(23)?>" value="<?php echo $_SESSION['name']; ?>">
                                            <span id="usernameStatus"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="username"><span class="supporting"><?php get_text(54)?></span></label>
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
                                            <input type="text" class="form-control text" id="email" name="email" placeholder="<?php get_text(51)?>" value="<?php echo $_SESSION['email']; ?>">
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
                                            <input class="btn btn-primary" type="submit" name="submit" value="<?php get_text(53)?>" style="width:100%">
                                        </div>
                                        <div class="col-sm-6">
                                            <?php get_text(60)?> <a href="site/terms" target="_blank"><?php get_text(61)?></a>
                                            <input type="checkbox" name="terms" id="terms" value="1" checked="checked" disabled>
                                        </div>
                                    </div>
                            </form>
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
            <div id="errors">
                <?php switch ($_SESSION['msg']) {
                    case 1: echo('<p class="error text-center"><b><i class="fa fa-exclamation-circle"></i> '.get_text(25,1).':</b> '.get_text(62,1).'</p>'); break;
                    case 2: echo('<p class="error text-center"><b><i class="fa fa-exclamation-circle"></i> '.get_text(25,1).':</b> '.get_text(63,1).'</p>'); break;
                } 
                unset($_SESSION['msg']); ?>
            </div>
        </div>