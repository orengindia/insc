        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                <h3 class="title"><?php get_text(381)?>: <?php echo NAME; ?></h3>
                
                <p><?php get_text(395)?></p>
                <p><p><?php get_text(384)?></p></p>
                <p>- <?php get_text(396)?>;</p>
                <p>- <?php get_text(397)?>;</p>
                <p>- <?php get_text(398)?>;</p>
                <p>- <?php get_text(399)?>;</p>
                <p>- <?php get_text(400)?>;</p>
                <p>- <?php get_text(401)?>.</p>
                <p style="color:red"><?php get_text(402)?></p>
                <p style="color:green"><?php get_text(403)?> <a href="administration/edit_topic.php?id=<?php echo ID; ?>"><?php get_text(404)?></a> <?php get_text(405)?></p>
                
                <form method="post" action="administration/delete_topic.php?id=<?php echo ID; ?>" id="deletetopic">
                    <input type="hidden" name="id" value="<?php echo ID; ?>">
                    <input type="submit" name="submit" value="<?php get_text(379)?>" class="btn btn-primary">
                    <a class="btn" href="administration/manage_topics.php"><?php get_text(380)?></a>
                </form>
                <br><br>
            </div>
        </div>