        <script> 
            (function ($) {
                jQuery.expr[':'].Contains = function(a,i,m){
                    return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
                };
            
                function listFilter(header, list) {
                    var form = $("<form>").attr({"class":"filterform","action":"#"}),
                        input = $("<input>").attr({"class":"filterinput","type":"text","placeholder":"<?php get_text(462)?>","style":"margin:5px;padding:5px"});
                    $(form).append(input).appendTo(header);
           
                    $(input).change(function(){
                        var filter = $(this).val();
                        if(filter) {
                            $(list).find(".info_wrapper:not(:Contains(" + filter + "))").parent().slideUp();
                            $(list).find(".info_wrapper:Contains(" + filter + ")").parent().slideDown();
                        } else {
                            $(list).find(".topic_photo_card").slideDown();
                        }
                        return false;
                    }).keyup(function(){
                        $(this).change();
                    });
                }
           
                $(function(){
                    listFilter($("#search_all_categories"), $(".dynamic_interests"));
                });
            }(jQuery)
        );
        </script> 

        <form method="post" action="ajax/set_categories">

            <nav class="navbar-fixed-bottom all-categories-nav">
                <div class="container">
                    <div class="form-group navbar-left">
                        <span id="search_all_categories"></span>
                    </div>

                    <button type="submit" class="btn btn-primary disabled navbar-btn navbar-right" id="sbmt"><span class="needed"> <span id="counter">5</span> <?php get_text(116)?></span><span class="enough none"><?php get_text(117)?></span></button>
                </div>
            </nav>

            <div class="container all-categories">
                <input type="hidden" name="categories" value="" id="categories">
                <input type="hidden" name="number" value="0" id="number">
                <h3 class="title">
                    <?php get_text(114)?>
                    <?php if(USER_RANK==1) { ?>
                        <a href="administration/index.php" class="btn pull-right" style="margin-top: -12px"><?php echo get_text(103,1); ?></a>
                    <?php } ?>
                </h3>
                <small style="display:block; padding-left:10px; padding-bottom: 10px"><?php get_text(115)?></small>
                <div class="topic_photo_card_wrapper">
                    <div class="dynamic_interests">
                        <?php echo $categories; ?>
                    </div>
                </div>
            </div>

        </form>