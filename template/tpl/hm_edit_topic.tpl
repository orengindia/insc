        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                <h3 class="title"><?php echo ucfirst(get_text(404,1)); ?>: <?php echo NAME; ?></h3>

                <form method="post" action="administration/edit_topic.php?id=<?php echo ID; ?>" id="edittopic" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo ID; ?>">
                    <div class="row col-xs-12" id="link_to_new_topic_image">
                        <img src="media/images/categories/<?php echo PHOTO; ?>" width="50" height="50">
                        <a href="#" onclick="upload_new_image_for_topic(); return false;"><?php get_text(417)?></a>
                    </div>
                    <div class="row col-xs-12 none" id="new_topic_image">
                        <label for="photo" class="control-label"><?php get_text(428)?></label>
                        <input type="file" name="photo" id="photo" class="form-control">
                        <small class="supporting"><?php get_text(434)?> <span style="color:green"><?php get_text(430)?></span></small>
                    </div>
                    <div class="row col-xs-12">
                        <label for="topic_name" class="control-label"><?php get_text(426)?> <span style="color:red">*</span></label>
                        <input type="text" name="topic_name" id="topic_name" class="form-control" placeholder="<?php get_text(426)?>" value="<?php echo NAME; ?>" required>
                        <small class="supporting"><?php get_text(427)?></small>
                    </div>
                    <div class="row col-xs-12">
                        <label for="topic_url" class="control-label">Topic URL <span style="color:red">*</span></label>
                        <input type="text" name="topic_url" id="topic_url" class="form-control" placeholder="Enter your url link" value="<?php echo URL; ?>" required>
                        <small class="supporting">Enter new url link without spaces and '/'</small>
                    </div>
                    <div class="row col-xs-12">
                        <label for="topic_description" class="control-label"><?php get_text(431)?> <span style="color:red">*</span></label>
                        <textarea name="topic_description" id="topic_description" class="form-control" placeholder="<?php get_text(431)?>" required><?php echo DESCRIPTION; ?></textarea>
                        <small class="supporting"><?php get_text(432)?></small>
                    </div>
                    <div class="row col-xs-12">
                        <br>
                        <input type="submit" name="submit" value="<?php get_text(117)?>" class="btn btn-primary">
                         <a class="btn" href="administration/manage_topics.php"><?php get_text(380)?></a>
                        <br><br>
                    </div>
                </form>
            </div>
        </div>