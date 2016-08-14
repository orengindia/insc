        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                <h3 class="title"><?php get_text(375)?>:</h3>
                
                <div style="border:solid 1px grey;padding:0 15px;background-color:#E3E6EC"><?php echo ANSWER; ?></div>
                <p><?php get_text(383)?></p>
                <p><?php get_text(384)?></p>
                <p>- <?php get_text(385)?>;</p>
                <p>- <?php get_text(386)?>;</p>
                <p>- <?php get_text(387)?>;</p>
                <p style="color:green"><?php get_text(388)?></p>
                <form method="post" action="administration/delete_answer.php?id=<?php echo ID; ?>" id="deleteanswer">
                    <input type="hidden" name="id" value="<?php echo ID; ?>">
                    <input type="submit" name="submit" value="<?php get_text(379)?>" class="btn btn-primary">
                    <a class="btn" href="administration/manage_answers.php"><?php get_text(380)?></a>
                </form>
                <br><br>
            </div>
        </div>