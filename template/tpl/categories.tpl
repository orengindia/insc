        <script> 
            (function ($) {
                jQuery.expr[':'].Contains = function(a,i,m){
                    return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
                };
            
                function listFilter(header, list) {
                    var form = $("<form>").attr({"class":"filterform","action":"#"}),
                        input = $("<input>").attr({"class":"filterinput","type":"text","placeholder":"<?php get_text(462)?>","style":"margin:5px;padding:5px;width:100%"});
                    $(form).append(input).appendTo(header);
                    $(input).change(function(){
                        var filter = $(this).val();
                        if(filter) {
                            $(list).find(".category_item_desc:not(:Contains(" + filter + "))").parent().slideUp();
                            $(list).find(".category_item_desc:Contains(" + filter + ")").parent().slideDown();
                        } else {
                            $(list).find(".category_item").slideDown();
                        }
                        return false;
                    }).keyup( function () {
                        $(this).change();
                    });
                }
           
                $(function () {
                    listFilter($("#search_all_categories"), $("#all_categories_list"));
                    listFilter($("#search_all_categories2"), $("#all_categories_list"));
                });
            }(jQuery));
        </script> 


        <div class="container stream">
            <div class="visible-xs col-xs-12 visible-lg col-lg-2 menu">
                <h3 class="title"><?php get_text(212)?></h3>
                <span id="search_all_categories"></span>
                <?php if(USER_ID!='') echo $info; ?>
            </div>

            <div class="col-xs-12 col-sm-8 col-lg-7">
                <h3 class="title"><?php get_text(200)?></h3>
                <span id="all_categories_list">
                    <?php echo $all_categories; ?>
                </span>
            </div>

            <div class="col-xs-12 col-sm-4 col-lg-3">
                <div class="menu hidden-lg hidden-xs">
                    <h3 class="title"><?php get_text(212)?></h3>
                    <span id="search_all_categories2"></span>
                    <?php if(USER_ID!='') echo $info; ?>
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