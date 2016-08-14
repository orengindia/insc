        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                <h3 class="title"><?php get_text(445); ?></h3>

                <?php switch($_SESSION['msg']) {
                    case 51: echo('<h3 class="title admin-message"><b>'.get_text(92,1).'</b> '.get_text(446,1).'</h3>'); break;
                    case 52: echo('<h3 class="title admin-message"><b>'.get_text(92,1).'</b> '.get_text(447,1).'</h3>'); break;
                } unset($_SESSION['msg']); ?>

                <form id="custom-search-form" class="form-search form-horizontal" action="administration/manage_answers.php" method="get">
                    <div class="input-append span12">
                        <input type="text" class="search-query" id="query" name="answer" placeholder="<?php get_text(90)?>" <?php if($_GET['answer']!='') echo 'value="'.$_GET['answer'].'"'; ?>>
                        <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                    </div>
                </form>

                <br>

                <div class="admin_block">
                    <?php echo TABLE; ?>
                </div>
            </div>
        </div>