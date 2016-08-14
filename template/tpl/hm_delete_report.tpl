        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                <h3 class="title"><?php get_text(374)?></h3>
                
                <p><?php get_text(376)?></p>
                <p><?php get_text(377)?>.<br><?php get_text(378)?></p>
                <form method="post" action="administration/delete_report.php?id=<?php echo ID; ?>">
                    <input type="hidden" name="id" value="<?php echo ID; ?>">
                    <input type="submit" name="submit" value="<?php get_text(379)?>" class="btn btn-primary" style="padding: 5px">
                    <a class="btn" href="administration/manage_reports.php"><?php get_text(380)?></a>
                </form>
                <br><br>
            </div>
        </div>