        <div class="container stream">
            <div class="visible-xs col-xs-12 visible-lg col-lg-2 menu">
                <h3 class="title" style="margin-bottom:10px">
                    <?php echo ucfirst(get_text(118,1)) . ': ' . $number_want_answers; ?>
                </h3>
                <div style="padding:0 5px">
                    <div>
                        <?php echo $want_answers; ?>
                    </div>
                    <span class="latest_activity"><?php echo $last_want_answers; ?></span>
                </div>
                <br>
                <h3 class="title"><?php get_text(254)?></h3>
                <span class="question-views">
                    <i class="fa fa-eye"></i> <?php echo $q_views; ?> <?php get_text(233)?>
                </span>
                <br><br>
                <h3 class="title"><?php get_text(208)?></h3>
                <span class="question-topics-block">
                    <div class="TopicListItem">
                        <?php echo $q_categories; ?>
                    </div>
                </span>
            </div>

            <div class="col-xs-12 col-sm-8 col-lg-7">
                <input type="hidden" id="last_value" name="last_value" value="0">
                <input type="hidden" id="remain" name="remain" value="">
                <div class="question-main">
                    <div class="feed_item">
                        <div class="QuestionText">
                            <a class="question_link" href="q/<?php echo $q_url; ?>">
                                <span class="link_text"><?php echo $q_question; ?></span>
                            </a>
                        </div>
            
                        <div class="feed_item_answer_content answer_content">
                            <div class="expanded_q_text">
                                <?php if($q_description!='') { ?>
                                    <span><?php echo $q_description; ?></span>
                                <?php } ?>
                                <?php echo $q_image; ?>
                            </div>
                        </div>

                        <div class="Answer ActionBar <?php if (SESSION_STATUS == true) { echo 'one_question'; } ?>">
                            <?php echo $q_likes; ?>
                            <?php if(USER_ID!='') { ?>
                                <div class="action_item" style="cursor:pointer" data-toggle="modal" data-target="#reportModal" data-id="<?php echo $q_id;?>" data-type="1">
                                    <a class="downvote"><?php get_text(219)?></a>
                                </div>
                            <?php } ?>
                            <?php if($is_author!='') echo $is_author; ?>
                            <div class="action_item">
                                <span style="display: inline-block;margin: 0;padding: 5px 0 4px;font-weight: 400; font-size: 13px;"><?php get_text(255)?> <?php echo $q_time; ?> <?php get_text(490)?> <a href="<?php echo $user_asked; ?>" target="_blank" style="display: inline; color: #337ab7"><?php echo $user_asked; ?></a></span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if(USER_ID!='') {?>
                    <div class="answer_editor in_feed">
                        <form method="post" action="ajax/answer" enctype="multipart/form-data" onsubmit="return answerComplete();">
                            <input type="hidden" name="q_id" value="<?php echo $q_id;?>">
                            <div class="inner">
                                <div class="AnswerEditorHeader">
                                    <a href="<?php echo USER_USERNAME;?>">
                                        <img class="profile_photo_img" src="media/images/users/<?php echo USER_PHOTO;?>" width="100" alt="<?php echo $user_name; ?>" height="100">
                                    </a>
                                    <a class="user" href="<?php echo $user_name;?>"><?php echo $user_name; ?></a>
                                    <div class="signature">
                                        <?php if($user_info!='') { ?>
                                            <?php echo $user_info; ?>
                                        <?php } else { ?>
                                            <a href="site/settings"><?php get_text(227)?></a>
                                        <?php } ?>
                                    </div>
                                </div>
                                
                                <div class="AnswerEditorMain">
                                    <textarea name="answer_text" id="answer_text" placeholder="<?php get_text(206)?>"></textarea>
                                    <script type="text/javascript">
                                        tinymce.PluginManager.add('placeholder', function(editor) {
                                            editor.on('init', function() {
                                                var label = new Label;
                                                onBlur();
                                                tinymce.DOM.bind(label.el, 'click', onFocus);
                                                editor.on('focus', onFocus);
                                                editor.on('blur', onBlur);

                                                function onFocus(){
                                                    label.hide();
                                                    tinyMCE.execCommand('mceFocus', false, editor);
                                                }

                                                function onBlur(){
                                                    if(editor.getContent() == '') {
                                                        label.show();
                                                    }
                                                    else {
                                                        label.hide();
                                                    }
                                                }
                                            });

                                            var Label = function(){
                                                this.text = editor.getElement().getAttribute("placeholder");
                                                this.contentAreaContainer = editor.getContentAreaContainer();
                                                tinymce.DOM.setStyle(this.contentAreaContainer, 'position', 'relative');
                                                attrs = {style: {position: 'absolute', top:'5px', left:0, color: '#888', padding: '1%', width:'98%', overflow: 'hidden'} };
                                                this.el = tinymce.DOM.add( this.contentAreaContainer, "label", attrs, this.text );
                                            }

                                            Label.prototype.hide = function(){
                                                tinymce.DOM.setStyle( this.el, 'display', 'none' );
                                            }

                                            Label.prototype.show = function(){
                                                tinymce.DOM.setStyle( this.el, 'display', '' );   
                                            }
                                        });

                                        tinymce.PluginManager.add('mention', function(editor, url) {
                                            editor.addButton('mention', {
                                                title: get_text(485),
                                                text: '@',
                                                icon: false,
                                                onclick: function() {
                                                    editor.windowManager.open({
                                                        title: get_text(486),
                                                        body: [
                                                            {type: 'textbox', name: 'title', label: 'Username'}
                                                        ],
                                                        onsubmit: function(e) {
                                                            editor.insertContent('@'+e.data.title);
                                                        }
                                                    });
                                                }
                                            });
                                        });

                                        tinyMCE.PluginManager.add('stylebuttons', function(editor, url) {
                                          ['pre', 'p', 'code', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'].forEach(function(name){
                                           editor.addButton("style-" + name, {
                                               tooltip: "H " + name,
                                                 text: name.toUpperCase(),
                                                 onClick: function() { editor.execCommand('mceToggleFormat', false, name); },
                                                 onPostRender: function() {
                                                     var self = this, setup = function() {
                                                         editor.formatter.formatChanged(name, function(state) {
                                                             self.active(state);
                                                         });
                                                     };
                                                     editor.formatter ? setup() : editor.on('init', setup);
                                                 }
                                             })
                                          });
                                        });

                                        tinymce.init({
                                            selector: "#answer_text",
                                            theme: "modern",
                                            menubar: false,
                                            plugins: [
                                                "link autolink image lists contextmenu",
                                                "nonbreaking save directionality",
                                                "paste stylebuttons mention placeholder autoresize"
                                            ],
                                            toolbar1: "bold italic underline style-h2 blockquote link bullist | image mention",
                                            contextmenu: "paste",
                                            width: '100%',
                                            height: 50,
                                            setup: function(editor) {
                                              editor.on('init', function() {
                                                $('.tiny_mce_answer').attr('style','display: block !important');
                                                $('#answer_text_ifr').addClass('closed-tinymce');
                                              });
                                              editor.on('focus', function(e) {
                                                $('#answer_text_ifr').removeClass('closed-tinymce');
                                              });
                                            }
                                        });
                                    </script>
                                </div>
                                <button type="submit" name="answer_btn" id="answer_btn" class="btn btn-primary btn-sm tiny_mce_answer"><?php get_text(164)?> <i class="fa fa-spinner fa-spin fa-abtn none"></i></button>
                            </div>
                        </form>
                    </div>
                    <br>
                <?php } ?>

                <h3 class="title" style="margin-bottom:10px">
                    <?php if($q_answers!=1) get_text(169); else echo get_text(164); ?>: <?php echo $q_answers;?>
                </h3>

                <div class="comment-content">
                    <div id="comments"></div>
                    <div class="loadingstream" style="margin:15px auto;">
                        <center><i class="fa fa-spinner fa-spin fa-3x" style="color:grey;"></i></center>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-4 col-lg-3">
                <div class="menu hidden-lg hidden-xs">
                    <h3 class="title" style="margin-bottom:10px">
                        <?php echo ucfirst(get_text(118,1)) . ': ' . $number_want_answers; ?>
                    </h3>
                    <div style="padding:0 5px">
                        <div>
                            <?php echo $want_answers; ?>
                        </div>
                        <span class="latest_activity"><?php echo $last_want_answers; ?></span>
                    </div>
                    <br>
                    <h3 class="title"><?php get_text(254)?></h3>
                    <span class="question-views">
                        <i class="fa fa-eye"></i> <?php echo $q_views; ?> <?php get_text(233)?>
                    </span>
                    <br><br>
                    <h3 class="title"><?php get_text(208)?></h3>
                    <span class="question-topics-block">
                        <div class="TopicListItem">
                            <?php echo $q_categories; ?>
                        </div>
                    </span>
                </div>

                <h3 class="title"><?php get_text(185)?></h3>
                <br>
                <?php if(SITE_ADSENSE!='') { echo SITE_ADSENSE; } else { echo '<img class="ad-image" src="https://placehold.it/300x250">'; } ?>
                <br>
                <h3 class="title"><?php get_text(186)?></h3>
                <div class="right-content">
                    <div class="right-links">
                        <ul>
                            <li><a href="site/contact"><?php get_text(37)?></a></li>
                            <li><a href="site/points"><?php get_text(39)?></a></li>
                            <li><a href="site/about"><?php get_text(190)?></a></li>
                            <li><a href="site/privacy"><?php get_text(40)?></a></li>
                            <li><a href="site/terms"><?php get_text(61)?></a></li>
                            <li><a href="site/people"><?php get_text(155)?></a></li>
                            <li><a href="site/topics"><?php get_text(189)?></a></li>
                            <li><p>© <?php echo date("Y"); ?>, <?php echo SITE_DOMAIN; ?></p></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>