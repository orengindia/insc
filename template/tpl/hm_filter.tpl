        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                <h3 class="title"><?php get_text(463)?></h3>

                <?php switch($_SESSION['msg']) {
                    case 1: echo('<h3 class="title admin-message"><b>'.get_text(92,1).'</b> '.get_text(93, 1).'</h3>'); break;
                } unset($_SESSION['msg']); ?>

                <p style="margin-left: 15px"><i class="fa fa-info" style="padding-right:5px;color:#000"></i> <?php get_text(464)?></p>
                <form method="post" action="administration/filter_settings.php" id="sitefilter" class="form-horizontal">
                    <div class="row col-xs-12">
                        <label for="words" class="control-label"><?php get_text(465)?>:</label>
                        <br>
                        <textarea name="words" id="words" class="form-control"><?php echo FILTER_WORDS; ?></textarea>
                        <small class="supporting"><?php get_text(466)?></small>
                    </div>
                    <div class="row col-xs-12">
                        <label for="ips" class="control-label"><?php get_text(467)?>:</label>
                        <br>
                        <textarea name="ips" id="ips" class="form-control"><?php echo FILTER_IPS; ?></textarea>
                        <small class="supporting"><?php get_text(468)?></small>
                    </div>
                    <div class="row col-xs-12">
                        <br>
                        <input type="submit" value="<?php get_text(117)?>" class="btn btn-primary">
                        <br><br>
                    </div>
                </form>
            </div>
        </div>