        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                
                <h3 class="title"><?php get_text(328)?></h3>

                <?php
                  switch($_SESSION['msg']) {
                    case 1: echo('<h3 class="title admin-message"><b>'.get_text(92,1).'</b> '.get_text(93, 1).'</h3>'); break;
                  }
                  unset($_SESSION['msg']);
                ?>
                <p style="margin-left: 15px"><i class="fa fa-info" style="padding-right:5px;color:#000"></i> <?php get_text(99)?></p>
                
                <div class="col-xs-12 col-sm-8">
                <form method="post" action="administration/site_settings.php" id="sitesettings" class="form-horizontal">
                  <div class="row">
                    <label for="sitename" class="control-label"><?php get_text(331)?></label>
                    <input type="text" name="sitename" id="sitename" class="form-control" value="<?php echo SITE_NAME; ?>">
                    <small class="supporting"><?php get_text(332)?></small>
                  </div>
                  <div class="row">
                    <label for="metakey" class="control-label"><?php get_text(334)?></label>
                    <input type="text" name="metakey" id="metakey" class="form-control" value="<?php echo SITE_KEYWORDS; ?>">
                    <small class="supporting"><?php get_text(335)?></small>
                  </div>
                  <div class="row">
                    <label for="metadesc" class="control-label"><?php get_text(336)?></label>
                    <input type="text" name="metadesc" id="metadesc" class="form-control" value="<?php echo SITE_DESCRIPTION; ?>">
                    <small class="supporting"><?php get_text(337)?></small>
                  </div>
                  <div class="row">
                    <label for="site_email" class="control-label"><?php get_text(329)?></label>
                    <input type="text" name="site_email" id="site_email" class="form-control" value="<?php echo SITE_EMAIL; ?>">
                    <small class="supporting"><?php get_text(330)?></small>
                  </div>
                  <div class="row">
                    <label for="site_fb" class="control-label"><?php get_text(359)?></label>
                      <select name="site_fb" id="site_fb" class="form-control" style="width: 500px;">
                        <option value="1" <?php if(SITE_FB==1) echo 'selected="selected"'; ?>><?php get_text(357)?></option>
                        <option value="0" <?php if(SITE_FB==0) echo 'selected="selected"'; ?>><?php get_text(358)?></option>
                      </select>
                    <small class="supporting"><?php get_text(359)?></small>
                  </div>
                  <div class="row">
                    <label for="fb_id" class="control-label"><?php get_text(341)?></label>
                    <input type="text" name="fb_id" id="fb_id" class="form-control" value="<?php echo SITE_FB_ID; ?>">
                    <small class="supporting"><?php get_text(342)?></small>
                  </div>
                  <div class="row">
                    <label for="fb_secret" class="control-label"><?php get_text(343)?></label>
                    <input type="text" name="fb_secret" id="fb_secret" class="form-control" value="<?php echo SITE_FB_SECRET; ?>">
                    <small class="supporting"><?php get_text(344)?></small>
                  </div>
                  <div class="row">
                    <label for="site_tw" class="control-label"><?php get_text(360)?></label>
                      <select name="site_tw" id="site_tw" class="form-control" style="width: 500px;">
                        <option value="1" <?php if(SITE_TW==1) echo 'selected="selected"'; ?>><?php get_text(357)?></option>
                        <option value="0" <?php if(SITE_TW==0) echo 'selected="selected"'; ?>><?php get_text(358)?></option>
                      </select>
                    <small class="supporting"><?php get_text(360)?></small>
                  </div>
                  <div class="row">
                    <label for="tw_key" class="control-label"><?php get_text(351)?></label>
                    <input type="text" name="tw_key" id="tw_key" class="form-control" value="<?php echo TW_CONSUMER_KEY; ?>">
                    <small class="supporting"><?php get_text(352)?></small>
                  </div>
                  <div class="row">
                    <label for="tw_secret" class="control-label"><?php get_text(353)?></label>
                    <input type="text" name="tw_secret" id="tw_secret" class="form-control" value="<?php echo TW_CONSUMER_SECRET; ?>">
                    <small class="supporting"><?php get_text(354)?></small>
                  </div>

                  <div class="row">
                    <label for="adsense" class="control-label">AdSense 300x250</label>
                    <textarea name="adsense" id="adsense" class="form-control"><?php echo SITE_ADSENSE; ?></textarea>
                    <small class="supporting"><?php get_text(333)?></small>
                  </div>
                  <div class="row">
                    <label for="smtp_host" class="control-label"><?php get_text(339)?></label>
                    <input type="text" name="smtp_host" id="smtp_host" class="form-control" value="<?php echo SMTP_HOST; ?>">
                    <small class="supporting"><?php get_text(340)?></small>
                  </div>
                  <div class="row">
                    <label for="smtp_port" class="control-label"><?php get_text(345)?></label>
                    <input type="text" name="smtp_port" id="smtp_port" class="form-control" value="<?php echo SMTP_PORT; ?>">
                    <small class="supporting"><?php get_text(346)?></small>
                  </div>
                  <div class="row">
                    <label for="smtp_user" class="control-label"><?php get_text(347)?></label>
                    <input type="text" name="smtp_user" id="smtp_user" class="form-control" value="<?php echo SMTP_USER; ?>">
                    <small class="supporting"><?php get_text(348)?></small>
                  </div>
                  <div class="row">
                    <label for="smtp_pass" class="control-label"><?php get_text(349)?></label>
                    <input type="text" name="smtp_pass" id="smtp_pass" class="form-control" value="<?php echo SMTP_PASS; ?>">
                    <small class="supporting"><?php get_text(350)?></small>
                  </div>
                  <div class="row">
                    <label for="signup_confirmation" class="control-label"><?php get_text(355)?></label>
                      <select name="signup_confirmation" id="signup_confirmation" class="form-control" style="width: 500px;">
                        <option value="1" <?php if(SIGNUP_CONFIRMATION==1) echo 'selected="selected"'; ?>><?php get_text(357)?></option>
                        <option value="0" <?php if(SIGNUP_CONFIRMATION==0) echo 'selected="selected"'; ?>><?php get_text(358)?></option>
                      </select>
                    <small class="supporting"><?php get_text(356)?></small>
                  </div>
                  <div class="row">
                    <label for="filter_word" class="control-label"><?php get_text(492)?></label>
                    <input type="text" name="filter_word" id="filter_word" class="form-control" value="<?php echo FILTER_WORD; ?>">
                    <small class="supporting"><?php get_text(493)?></small>
                  </div>
                  <br>
                  <div class="row">
                    <input type="submit" value="<?php get_text(117)?>" class="btn btn-primary">
                  </div>
                </form>

                <br><br>
            </div>
        </div>