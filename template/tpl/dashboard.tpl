        <div class="container stream">
            <div class="col-xs-12 col-sm-2 menu">
                <h3 class="title">MENU</h3>
                <?php echo serializeHmTabs; ?>
            </div>

            <div class="col-xs-12 col-sm-10">
                
                <h3 class="title"><?php get_text(101)?></h3>
                <p><?php get_text(316)?>: <b><?php echo USERS_ROWS; ?></b>. <a href="administration/manage_users.php"><?php get_text(317)?></a> <?php get_text(318)?>. <?php get_text(319)?>: <b><?php echo ONLINE_USERS_ROWS; ?></b>.<br><?php get_text(320)?>:</p>
                <canvas id="usersChart" style="width:100%; height: auto; max-height: 300px"></canvas>
    
                <script type="text/javascript">
                    var ctx = $("#usersChart").get(0).getContext("2d");
                    var myNewChart = new Chart(ctx);
                    var data = {
                        labels: [
                            <?php echo USERS_GR_LABELS; ?>
                        ],
                        datasets: [
                            {
                                label: "Users chart",
                                fillColor: "rgba(220,220,220,0.2)",
                                strokeColor: "rgba(220,220,220,1)",
                                pointColor: "rgba(220,220,220,1)",
                                pointStrokeColor: "#fff",
                                pointHighlightFill: "#fff",
                                pointHighlightStroke: "rgba(220,220,220,1)",
                                data: [
                                  <?php echo USERS_GR_DATASETS; ?>
                                ]
                            }
                        ]
                    };
                    var myLineChart = new Chart(ctx).Line(data);
                </script>
                
                <br>
                <p><?php get_text(321)?>: <b><?php echo QUESTIONS_ROWS; ?></b>. <a href="administration/manage_questions.php"><?php get_text(317)?></a> <?php get_text(322)?>.<br><?php get_text(327)?>:</p>
                <canvas id="questionsChart" style="width:100%; height: auto; max-height: 300px"></canvas>
        
                <script type="text/javascript">
                    var ctx = $("#questionsChart").get(0).getContext("2d");
                    var myNewChart = new Chart(ctx);
                    var data = {
                        labels: [
                            <?php echo QUESTIONS_GR_LABELS; ?>
                        ],
                        datasets: [
                            {
                                label: "Questions chart",
                                fillColor: "rgba(180, 96, 232, 0.2)",
                                strokeColor: "rgba(180, 96, 232, 1)",
                                pointColor: "rgba(180, 96, 232, 1)",
                                pointStrokeColor: "#fff",
                                pointHighlightFill: "#fff",
                                pointHighlightStroke: "rgba(180, 96, 232, 1)",
                                data: [
                                  <?php echo QUESTIONS_GR_DATASETS; ?> 
                                ]
                            }
                        ]
                    };
                    var myLineChart = new Chart(ctx).Line(data);
                </script>

                <br>
                <p><?php get_text(323)?>: <b><?php echo ANSWERS_ROWS; ?></b> <?php echo strtolower(get_text(169,1))?>. <a href="administration/manage_answers.php"><?php get_text(317)?></a> <?php get_text(326)?>.<br><?php get_text(324)?>:</p>
                <canvas id="answersChart" style="width:100%; height: auto; max-height: 300px"></canvas>

                <script type="text/javascript">
                    var ctx = $("#answersChart").get(0).getContext("2d");
                    var myNewChart = new Chart(ctx);
                    var data = {
                        labels: [
                            <?php echo ANSWERS_GR_LABELS; ?>
                        ],
                        datasets: [
                            {
                                label: "Answers chart",
                                fillColor: "rgba(151,187,205,0.2)",
                                strokeColor: "rgba(151,187,205,1)",
                                pointColor: "rgba(151,187,205,1)",
                                pointStrokeColor: "#fff",
                                pointHighlightFill: "#fff",
                                pointHighlightStroke: "rgba(151,187,205,1)",
                                data: [
                                  <?php echo ANSWERS_GR_DATASETS; ?> 
                                ]
                            }
                        ]
                    };
                    var myLineChart = new Chart(ctx).Line(data);
                </script>

                <a href="administration/manage_reports.php">
                    <h3 style="color:red"><?php get_text(258)?> <?php echo REPORTS_ROWS; ?> <?php get_text(325)?>!</h3>
                </a>
                <br>

                <div class="well">
                    <small><?php get_text(97)?>: <?php echo SCRIPT_VERSION; ?></small>
                    <br>
                    <small><?php get_text(98)?>: <a href="<?php echo AUTHOR; ?>"><?php echo AUTHOR; ?></a></small>
                    <br>
                    <small>GD Library: <?php if (extension_loaded('gd') && function_exists('gd_info')) echo "Installed"; else echo "Not installed"; ?></small>
                    <br>
                    <small>PHP version: <?php echo phpversion();?><br>PDO Library: <?php if(class_exists('PDO')) echo 'PDO enabled'; ?></small>
                </div>
            </div>
        </div>