        <div class="page">

            <div class="navbar navbar-fixed-top">
                <div class="container">
                    <div class="navbar-header">
                        <a href="" class="navbar-brand">
                            <?php echo SITE_NAME; ?>
                        </a>
                        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                            <i class="fa fa-ellipsis-h"></i>
                        </button>
                    </div>
                  
                    <div class="navbar-collapse collapse" id="navbar-main">
                        <ul class="nav navbar-nav navbar-right">
                            <?php if(SESSION_STATUS!=true) { ?>
                                <li class="text-center"><a href="site/login"><?php get_text(9)?></a></li>
                                <a href="site/signup" class="btn btn-success navbar-btn"><?php get_text(53)?></a>
                            <?php } else { ?>
                                <li class="hidden-sm text-center <?php if($line==1) echo 'with-line'; ?>"><a href=""><i class="fa fa-home"></i> <?php get_text(101)?></a></li>
                                <li class="text-center <?php if($line==2) echo 'with-line'; ?>"><a href="site/notifications"><i class="fa fa-bell"></i> <span class="hidden-sm"><?php get_text(102)?></span> <span class="notification_counter none">0</span></a></li>
                                <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img class="profile_photo_img" src="media/images/users/<?php echo USER_PHOTO;?>" width="34" alt="<?php echo USER_USERNAME;?>" height="34"> <span class="visible-xs"><?php echo USER_USERNAME;?></span> <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <?php if(USER_RANK>0) { ?>
                                            <li><a href="administration/index.php"><i class="fa fa-database"></i> <?php get_text(103)?></a></li>
                                            <li role="separator" class="divider"></li>
                                        <?php } ?>
                                        <li><a href="<?php echo USER_USERNAME; ?>"><i class="fa fa-user"></i> <?php get_text(104)?></a></a></li>
                                        <li><a href="site/settings"><i class="fa fa-cogs"></i> <?php get_text(105)?></a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="site/logout"><i class="fa fa-sign-out"></i> <?php get_text(106)?></a></li>
                                    </ul>
                                </li>
                                <a class="btn btn-success navbar-btn" data-toggle="modal" data-target="#askModal"><i class="fa fa-plus-square"></i> <?php get_text(100)?></a>   
                            <?php } ?>
                        </ul>

                        <form class="navbar-form search_form" role="search" method="GET" action="site/search">
                            <div class="search_input" style="display:inline;">
                                <div class="input-group search" style="display:table;">
                                    <span style="width:1%; padding: 10px; display: table-cell;"> </span>
                                    <span class="fa fa-search"></span>
                                    <input class="form-control" name="q" id="search_input" placeholder="<?php get_text(90)?>" autocomplete="off" maxlength="250" type="text" tabindex="1">
                                    <span style="width:1%; padding: 10px; display: table-cell;"> </span>
                                </div>
                                <div class="results_frame hidden-xs"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- New question modal -->
            <div class="modal fade" id="askModal" role="dialog" aria-labelledby="askModalLabel">
                <div class="vertical-alignment-helper">
                    <div class="modal-dialog vertical-align-center" role="document">
                        <div class="modal-content">
                            <form method="post" action="ajax/ask" enctype="multipart/form-data" id="askForm">
                                <input type="hidden" name="question-data" id="question-data">
                                <input type="hidden" name="description-data" id="description-data">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="askModalLabel">
                                        <span class="ask-step-1-title"><?php get_text(107)?></span>
                                        <span class="ask-step-2-title none"><?php get_text(108)?> <span class="ask-step-2-question"></span></span>
                                        <span class="ask-step-3-title none"><?php get_text(109)?> <span class="ask-step-3-question"></span></span>
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    <div class="content_row">
                                        <textarea name="question" class="add_question" id="add_question" data-maxsize="250" data-output="numq" placeholder="<?php get_text(110)?>"></textarea>
                                        <small class="help-block"><span id="numq"></span></small>
                                        <span class="add_q_error"></span>
                                        <div class="question-details">
                                            <a href="#" onclick="add_question_description(); return false;" class="add_desc_link"><i class="fa fa-plus-circle"></i> <?php get_text(111)?></a>
                                            <br>
                                            <textarea name="description" class="add_question none" id="add_description" data-maxsize="2000" data-output="numd" placeholder="<?php get_text(112)?>"></textarea>
                                            <small class="help-block"><span id="numd" class="none"></span></small>
                                            <small class="add_image none"><?php get_text(113)?>: <input type="file" name="image" id="image"></small>
                                        </div>
                                    </div>
        
                                    <div class="content_last"></div>

                                    <div class="question-categories none">
                                        <?php /*<input id="search_category" type="text" placeholder="Select one or more topics"> */ ?>
                                        <span class="results"></span>
                                        <span class="error_cat"></span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <a id="step_1" class="btn btn-primary disabled" onclick="next_ask_step(); return false;"><?php get_text(204)?></a>
                                    <a id="step_2" class="btn btn-primary none" onclick="last_ask_step(); return false;"><?php get_text(236)?></a>
                                    <input type="submit" class="btn btn-primary none" disabled="disabled" id="step_3" onclick="check_for_categories(); return false;" value="<?php get_text(100)?>">
                                    <div class="loadingask none"><i class="fa fa-spinner fa-spin"></i></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report modal -->
            <div class="modal fade" id="reportModal" role="dialog" aria-labelledby="reportModalLabel">
                <div class="vertical-alignment-helper">
                    <div class="modal-dialog vertical-align-center" role="document">
                        <div class="modal-content">
                            <form method="post" action="ajax/report" id="reportForm">
                                <input type="hidden" name="r_id" id="r_id">
                                <input type="hidden" name="r_type" id="r_type">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="reportModalLabel"><?php get_text(219)?></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="sent">
                                        <textarea name="r_reason" class="add_question" id="r_reason" required data-maxsize="500" data-output="rq" placeholder="<?php get_text(220)?>"></textarea>
                                        <small class="help-block"><span id="rq"></span></small>
                                    </div>
                                    <div class="success none">
                                        <p class="bg-success" style="padding: 10px 5px"><?php get_text(221)?></p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="<?php get_text(136)?>" onclick="send_report(); return false;">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit question modal -->
            <div class="modal fade" id="editqModal" role="dialog" aria-labelledby="editqModalLabel">
                <div class="vertical-alignment-helper">
                    <div class="modal-dialog vertical-align-center" role="document">
                        <div class="modal-content">
                            <form method="post" action="ajax/edit_question" enctype="multipart/form-data" id="editqForm">
                                <input type="hidden" name="id_p" id="id_p">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="editqModalLabel"><?php get_text(222)?></h4>
                                </div>
                                <div class="modal-body">
                                    <textarea name="add_question_p" class="add_question" id="add_question_p" required data-maxsize="250" data-output="numeq" placeholder="<?php get_text(110)?>"></textarea>
                                    <small class="help-block"><span id="numeq"></span></small>
                                    <span class="add_q_error"></span>
                                    <div class="question-details">
                                        <textarea name="add_description_p" class="add_question" id="add_description_p" data-maxsize="2000" data-output="numed" placeholder="<?php get_text(112)?>"></textarea>
                                        <small class="help-block"><span id="numed"></span></small>
                                        <small class="q_p_t" style="cursor: pointer" onclick="edit_question_image();"><?php get_text(223)?></small>
                                        <input type="file" name="image_p" id="image_p" class="add_image_p none">
                                    </div>
                                    <br><div class="content_last_p"></div>
                                    <span class="results_p"></span>
                                    <p class="error_cat_e"></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" onclick="check_for_categories_e(); return false;"><?php get_text(117)?> <i class="fa fa-spinner fa-spin fa-abtn none" style="margin:0"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit answer modal -->
            <div class="modal fade" id="editaModal" role="dialog" aria-labelledby="editaModalLabel">
                <div class="vertical-alignment-helper">
                    <div class="modal-dialog vertical-align-center" role="document">
                        <div class="modal-content">
                            <form method="post" action="ajax/edit_answer" id="editaForm">
                                <input type="hidden" name="answer_id" id="answer_id">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="editaModalLabel"><?php get_text(207)?></h4>
                                </div>
                                <div class="modal-body">
                                    <textarea name="answer" id="answer_text_p" placeholder="<?php get_text(206)?>"></textarea>
                                    <span class="edit_a_error"></span>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <a class="btn btn-primary" onclick="edit_answer_save(); return false;"><?php get_text(117)?> <i class="fa fa-spinner fa-spin fa-abtn none"></i></a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Words, used in javascript file -->
            <span class="none" id="w_118"><?php get_text(118)?></span>
            <?php for($i=470; $i<490; $i++) { ?>
                <span class="none" id="w_<?php echo $i; ?>"><?php get_text($i); ?></span>
            <?php } ?>