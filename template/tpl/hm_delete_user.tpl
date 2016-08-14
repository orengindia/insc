        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                <h3 class="title"><?php get_text(382)?>: <a href="<?php echo USERNAME; ?>" target="_blank"><?php echo NAME; ?></a></h3>

                <p><?php get_text(406)?></p>
                <p><?php get_text(384)?></p>
                <p>- <?php get_text(407)?>;</p>
                <p>- <?php get_text(408)?>;</p>
                <p>- <?php get_text(409)?>;</p>
                <p>- <?php get_text(410)?>;</p>
                <p>- <?php get_text(411)?>.</p>
                <p style="color:red"><?php get_text(412)?></p>
                <form method="post" action="administration/delete_user.php?id=<?php echo ID; ?>" id="deleteuser">
                    <input type="hidden" name="id" value="<?php echo ID; ?>">
                    <input type="submit" name="submit" value="<?php get_text(379)?>" class="btn btn-primary">
                     <a class="btn" href="administration/manage_users.php"><?php get_text(380)?></a>
                </form>
                <br><br>
            </div>
        </div>