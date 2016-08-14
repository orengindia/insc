        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                <h3 class="title"><?php get_text(205)?>: <?php echo NAME; ?></h3>

                <form method="post" action="administration/edit_question.php?id=<?php echo ID; ?>" id="editquestion" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo ID; ?>">
                    <div class="row col-xs-12">
                        <label for="question" class="control-label"><?php get_text(413)?> <span style="color:red">*</span></label>
                        <input type="text" name="question" id="question" class="form-control" placeholder="<?php get_text(413)?>" value="<?php echo NAME; ?>" required>
                        <small class="supporting"><?php get_text(413)?>. <?php get_text(414)?></small>
                    </div>
                    <div class="row col-xs-12">
                        <label for="description" class="control-label"><?php get_text(415)?></label>
                        <textarea name="description" id="description" class="form-control" placeholder="<?php get_text(415)?>"><?php echo DESCRIPTION; ?></textarea>
                        <small class="supporting"><?php get_text(416)?></small>
                    </div>
                    <?php if(PHOTO!='') { ?>
                        <div class="row col-xs-12" id="link_to_new_question_image">
                            <img src="media/images/users/<?php echo PHOTO; ?>" width="50" height="50">
                            <a href="#" onclick="upload_new_image_for_question(); return false;"><?php get_text(417)?></a>
                        </div>
                        <div class="row col-xs-12 none" id="new_question_image">
                            <label for="photo" class="control-label"><?php get_text(418)?></label>
                            <input type="file" name="photo" id="photo" class="form-control">
                            <small class="supporting"><?php get_text(419)?></small>
                        </div>
                    <?php } else { ?>
                        <div class="row col-xs-12" id="new_question_image">
                            <label for="photo" class="control-label"><?php get_text(418)?></label>
                            <input type="file" name="photo" id="photo" class="form-control">
                            <small class="supporting"><?php get_text(419)?></small>
                        </div>
                    <?php } ?>

                    <div class="row col-xs-12" id="category_row col-xs-12">
                        <label for="category" class="control-label" style="display:block;"><?php get_text(420)?>:</label>
                        <div>
                            <span class="content_last">
                                <?php echo CATEGORY; ?>
                            </span>
                            <?php echo ADD_CATEGORY; ?>
                        </div>
                    </div>
                    <div class="row col-xs-12">
                        <br>
                        <input type="submit" name="submit" value="<?php get_text(117)?>" class="btn btn-primary">
                         <a class="btn" href="administration/manage_questions.php"><?php get_text(380)?></a>
                        <br><br>
                    </div>
                </form>
            </div>
        </div>