        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                <h3 class="title"><?php get_text(373)?>: <?php echo NAME; ?></h3>
                
                <p><?php get_text(389)?></p>
                <p><?php get_text(384)?>:</p>
                <p>- <?php get_text(390)?>;</p>
                <p>- <?php get_text(391)?>;</p>
                <p>- <?php get_text(392)?>;</p>
                <p style="color:red"><?php get_text(393)?></p>
                <p style="color:green"><?php get_text(394)?></p>
                <form method="post" action="administration/delete_question.php?id=<?php echo ID; ?>" id="deletequestion">
                    <input type="hidden" name="id" value="<?php echo ID; ?>">
                    <input type="submit" name="submit" value="<?php get_text(379)?>" class="btn btn-primary">
                     <a class="btn" href="administration/manage_questions.php"><?php get_text(380)?></a>
                </form>
                <br><br>
            </div>
        </div>