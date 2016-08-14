        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                <h3 class="title"><?php get_text(207)?></h3>

                <form method="post" action="administration/edit_answer.php?id=<?php echo ID; ?>" id="editanswer">
                    <input type="hidden" name="id" value="<?php echo ID; ?>">
                    <textarea name="answer" id="answer_text" placeholder="<?php get_text(206)?>"><?php echo ANSWER; ?></textarea>
                    <script type="text/javascript">
                      tinymce.PluginManager.add('placeholder', function(editor) {
                        editor.on('init', function() {
                          var label = new Label;   
                          onBlur();
                          tinymce.DOM.bind(label.el, 'click', onFocus);
                          editor.on('focus', onFocus);
                          editor.on('blur', onBlur);
                          label.hide();
                          function onFocus(){
                            label.hide();
                            tinyMCE.execCommand('mceFocus', false, editor);
                          }

                          function onBlur(){
                            if(editor.getContent()=='') {
                              label.show();
                            }else{
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
                          title: 'Mention user',
                          text: '@',
                          icon: false,
                          onclick: function() {
                            editor.windowManager.open({
                              title: 'Insert username',
                              body: [{type: 'textbox', name: 'title', label: 'Username'}],
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
                            tooltip: "Heading " + name,
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
                          "autolink lists link anchor pagebreak",
                          "insertdatetime nonbreaking save contextmenu directionality",
                          "paste stylebuttons mention placeholder autoresize"
                        ],
                        toolbar1: "bold italic underline style-h2 blockquote numlist bullist | mention ",
                        width: '100%',
                        height: 50,
                        setup: function(editor) {
                          editor.on('init', function() {
                            $('.tiny_mce_answer').attr('style','display: block !important');
                          });
                        }
                      });
                    </script>

                    <p style="color:green"><?php get_text(450)?></p>
                    <input type="submit" name="submit" value="<?php get_text(117)?>" class="btn btn-primary">
                    <a class="btn" href="administration/manage_answers.php"><?php get_text(380)?></a>
                </form>
                <br><br>
            </div>
        </div>