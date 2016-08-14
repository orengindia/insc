        <div class="container-fluid">
            <div class="row-fluid">
                <div class="centering text-center">
                    <a href="">
                        <div class="logo"><?php echo SITE_NAME; ?></div>
                    </a>

                    <div class="tagline">
                        <?php echo SITE_DESCRIPTION; ?>
                    </div>

                    <div class="container sign-in-block">
                        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 block">
                            <div class="col-sm-6 login">
                                <div class="title"><?php get_text(42)?></div>
                                <form method="post" action="site/forgotpassword">
                                    <input class="form-control text" type="text" name="code" id="code" placeholder="<?php get_text(43)?>" required>
                                    <input class="form-control btn-primary" type="submit" value="<?php get_text(44)?>" name="submit" id="submit-login">
                                </form>
                            </div>
                            <div id="errors">
                                <?php echo $response; ?>
                            </div>

                            <div class="col-sm-6 signup">
                                <div>
                                    <?php if(SITE_FB==1) {
                                        $params = array(
                                            'client_id'     => SITE_FB_ID,
                                            'redirect_uri'  => 'http://'.SITE_DOMAIN.'/core/modules/auth.php',
                                            'response_type' => 'code',
                                            'scope'         => 'email,user_birthday'
                                        );
                                        if(SITE_FB==1) echo $link = '<a class="fb-btn" href="https://www.facebook.com/dialog/oauth?' . urldecode(http_build_query($params)) . '"><i class="fa fa-facebook fa-3x" style="color: #2b5797"></i><br><span>'.get_text(33,1).' <br>Facebook</span></a>';
                                    } ?>

                                    <?php if(SITE_TW==1) {
                                        $oauth_nonce = md5(uniqid(rand(), true));
                                        $oauth_timestamp = time();

                                        $params = array(
                                          'oauth_callback=' . urlencode(TW_CALLBACK_URL) . '&',
                                          'oauth_consumer_key=' . TW_CONSUMER_KEY . '&',
                                          'oauth_nonce=' . $oauth_nonce . '&',
                                          'oauth_signature_method=HMAC-SHA1' . '&',
                                          'oauth_timestamp=' . $oauth_timestamp . '&',
                                          'oauth_version=1.0'
                                        );

                                        $oauth_base_text = implode('', array_map('urlencode', $params));
                                        $key = TW_CONSUMER_SECRET . '&';
                                        $oauth_base_text = 'GET' . '&' . urlencode(TW_REQUEST_TOKEN_URL) . '&' . $oauth_base_text;
                                        $oauth_signature = base64_encode(hash_hmac('sha1', $oauth_base_text, $key, true));

                                        $params = array(
                                          '&' . 'oauth_consumer_key=' . TW_CONSUMER_KEY,
                                          'oauth_nonce=' . $oauth_nonce,
                                          'oauth_signature=' . urlencode($oauth_signature),
                                          'oauth_signature_method=HMAC-SHA1',
                                          'oauth_timestamp=' . $oauth_timestamp,
                                          'oauth_version=1.0'
                                        );
                                        
                                        $url = TW_REQUEST_TOKEN_URL . '?oauth_callback=' . urlencode(TW_CALLBACK_URL) . implode('&', $params);
                                        $response = file_get_contents($url);
                                        parse_str($response, $response);

                                        $oauth_token = $response['oauth_token'];
                                        $oauth_token_secret = $response['oauth_token_secret'];
                                        $link = TW_AUTHORIZE_URL . '?oauth_token=' . $oauth_token;

                                        echo '<a class="tw-btn" href="' . $link . '"><i class="fa fa-twitter fa-3x" style="color: #64ccf1"></i><br><span>'.get_text(33,1).' <br>Twitter</span></a>';
                                    } ?>
                                </div>

                                <div class="text-left">
                                    <span class="tos-disclaimer"><a href="site/signup"><?php get_text(34)?></a>. <?php get_text(35)?> <a href="site/terms" target="_blank"><?php get_text(36)?></a>.</span>
                                </div>
                            </div>
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