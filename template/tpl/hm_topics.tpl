        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                <h3 class="title"><?php get_text(421)?></h3>

                <?php switch($_SESSION['msg']) {
                    case 6: echo('<h3 class="title admin-message"><b>'.get_text(92,1).'</b> '.get_text(423,1).'</h3>'); break;
                    case 7: echo('<h3 class="title admin-message"><b>'.get_text(92,1).'</b> '.get_text(424,1).'</h3>'); break; 
                    case 10: echo('<h3 class="title admin-message"><b>'.get_text(92,1).'</b> '.get_text(425,1).'</h3>'); break;
                }
                unset($_SESSION['msg']); ?>

                <a class="btn btn-primary" role="button" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">+ <?php get_text(422)?></a>
                <br>
                <div class="collapse" id="collapseExample">
                    <div class="well">
                        <form action="administration/manage_topics.php" method="post" enctype="multipart/form-data">
                            <div class="row col-xs-12">
                                <label for="topic_name" class="control-label"><?php get_text(426)?> <span style="color:red">*</span></label>
                                <input type="text" name="topic_name" id="topic_name" class="form-control" placeholder="<?php get_text(426)?>" required>
                                <small class="supporting"><?php get_text(427)?></small>
                            </div>
                            <div class="row col-xs-12">
                                <label for="photo" class="control-label"><?php get_text(428)?> <span style="color:red">*</span></label>
                                <input type="file" name="photo" id="photo" class="form-control" required>
                                <small class="supporting"><?php get_text(429)?> <span style="color:green"><?php get_text(430)?></span></small>
                            </div>
                            <div class="row col-xs-12">
                                <label for="topic_description" class="control-label"><?php get_text(431)?> <span style="color:red">*</span></label>
                                <textarea name="topic_description" id="topic_description" class="form-control" placeholder="<?php get_text(431)?>" required></textarea>
                                <small class="supporting"><?php get_text(432)?></small>
                            </div>
                            <input type="submit" class="btn btn-primary" id="addSubmit" value="<?php get_text(433)?>">
                        </form>
                    </div>
                </div>

                <form id="custom-search-form" class="form-search form-horizontal" action="administration/manage_topics.php" method="get">
                    <div class="input-append span12">
                        <input type="text" class="search-query" id="query" name="topic" placeholder="<?php get_text(90)?>" <?php if($_GET['topic']!='') echo 'value="'.$_GET['topic'].'"'; ?>>
                        <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                    </div>
                </form>

                <br>

                <div class="admin_block">
                    <?php echo TABLE; ?>
                </div>
            </div>
        </div>