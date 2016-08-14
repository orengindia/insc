        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                <h3 class="title"><?php get_text(366); ?></h3>

                <?php switch($_SESSION['msg']) {
                    case 21: echo('<h3 class="title admin-message"><b>'.get_text(92,1).'</b> '.get_text(368,1).'</h3>'); break;
                } unset($_SESSION['msg']); ?>

                <form id="custom-search-form" class="form-search form-horizontal" action="administration/manage_reports.php" method="get">
                    <div class="input-append span12">
                        <input type="text" class="search-query" id="query" name="report" placeholder="<?php get_text(90)?>" <?php if($_GET['report']!='') echo 'value="'.$_GET['report'].'"'; ?>>
                        <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                    </div>
                </form>

                <br>

                <div class="admin_block">
                    <?php echo TABLE; ?>
                </div>
            </div>
        </div>