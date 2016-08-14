        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                <h3 class="title"><?php get_text(361); ?></h3>

                <?php switch($_SESSION['msg']) {
                    case 1: echo('<h3 class="title admin-message"><b>'.get_text(92,1).'</b> '.get_text(93, 1).'</h3>'); break;
                } unset($_SESSION['msg']); ?>

                <div class="admin_block about_us_block" id="about_us_block">
                  <p style="font-size: 15px; background-color: #2B6DAD; margin: -5px; color: #fff; padding: 5px 10px; margin-bottom: 15px; cursor: pointer" onclick="show_page_content(1);"><?php get_text(362)?></p>
                  <form action="administration/manage_pages.php" method="post">
                    <input type="hidden" name="id" value="1">
                    <textarea name="content" id="about_us_content"><?php echo ABOUT_US_C; ?></textarea>
                    <script>
                      tinymce.init({
                        selector:'#about_us_content',
                      });
                    </script>
                    <br><input type="submit" class="btn btn-primary" id="saveSubmit" value="<?php get_text(117)?>"> or <a href="site/about" target="_blank">open page</a>
                  </form>
                </div>

                <hr>

                <div class="admin_block points_block" id="points_block">
                  <p style="font-size: 15px; background-color: #2B6DAD; margin: -5px; color: #fff; padding: 5px 10px; margin-bottom: 15px; cursor: pointer" onclick="show_page_content(2);"><?php get_text(363)?></p>
                  <form action="administration/manage_pages.php" method="post">
                    <input type="hidden" name="id" value="2">
                    <textarea name="content" id="points_content"><?php echo POINTS_C; ?></textarea>
                    <script>
                      tinymce.init({
                        selector:'#points_content',
                      });
                    </script>
                    <br><input type="submit" class="btn btn-primary" id="saveSubmit" value="<?php get_text(117)?>"> or <a href="site/points" target="_blank">open page</a>
                  </form>
                </div>

                <hr>

                <div class="admin_block privacy_block" id="privacy_block">
                  <p style="font-size: 15px; background-color: #2B6DAD; margin: -5px; color: #fff; padding: 5px 10px; margin-bottom: 15px; cursor: pointer" onclick="show_page_content(3);"><?php get_text(364)?></p>
                  <form action="administration/manage_pages.php" method="post">
                    <input type="hidden" name="id" value="3">
                    <textarea name="content" id="privacy_content"><?php echo PRIVACY_C; ?></textarea>
                    <script>
                      tinymce.init({
                        selector:'#privacy_content',
                      });
                    </script>
                    <br><input type="submit" class="btn btn-primary" id="saveSubmit" value="<?php get_text(117)?>"> or <a href="site/privacy" target="_blank">open page</a>
                  </form>
                </div>

                <hr>

                <div class="admin_block terms_block" id="terms_block">
                  <p style="font-size: 15px; background-color: #2B6DAD; margin: -5px; color: #fff; padding: 5px 10px; margin-bottom: 15px; cursor: pointer" onclick="show_page_content(4);"><?php get_text(365)?></p>
                  <form action="administration/manage_pages.php" method="post">
                    <input type="hidden" name="id" value="4">
                    <textarea name="content" id="terms_content"><?php echo TERMS_C; ?></textarea>
                    <script>
                      tinymce.init({
                        selector:'#terms_content',
                      });
                    </script>
                    <br><input type="submit" class="btn btn-primary" id="saveSubmit" value="<?php get_text(117)?>"> or <a href="site/terms" target="_blank">open page</a>
                  </form>
                </div>
            </div>
        </div>