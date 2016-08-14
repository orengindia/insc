<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="https://www.facebook.com/2008/fbml">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="description" content="<?php echo ($description!='' ? $description : SITE_DESCRIPTION); ?>">
        <meta name="keywords" content="<?php echo ($keywords!='' ? $keywords : SITE_KEYWORDS); ?>">
        <meta name="version" content="<?php echo SCRIPT_VERSION; ?>">
        <meta name="author" content="http://xandr.co">
        <title><?php echo $title; ?> | <?php echo SITE_NAME; ?></title>
        <base href="http<?php echo (HTTPS == 1 ? 's' : ''); ?>://<?php echo SITE_DOMAIN; ?>/">
        <link rel="shortcut icon" href="media/images/favicon.ico">
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700&subset=latin,cyrillic,cyrillic-ext' rel='stylesheet' type='text/css'>
        <link type="text/css" href="media/css/font-awesome.min.css?ver=4.4.0" rel="stylesheet">
        <link type="text/css" href="media/css/magnific-popup.min.css?ver=1.1.0" rel="stylesheet">
        <link type="text/css" href="media/css/select2.min.css?ver=4.0.2" rel="stylesheet">
        <link type="text/css" href="media/css/bootstrap.min.css?ver=3.3.6" rel="stylesheet">   
        <link type="text/css" href="template/style.css?ver=1.1.0" rel="stylesheet">   

        <script type="text/javascript" src="media/js/nanobar.min.js?ver=0.2.1"></script>
        <script type="text/javascript" src="media/js/jquery.min.js?ver=1.9.1"></script>
        <script type="text/javascript" src="media/js/scripts.js?ver=1.1.0"></script>
        <script type="text/javascript" src="media/js/tinymce/tinymce.min.js?ver=4.1.10"></script>
        <script type="text/javascript" src="media/js/maxlength.js?ver=1.1.0"></script>
        <script type="text/javascript" src="media/js/imgLiquid-min.js?ver=0.9.944"></script>
        <script type="text/javascript" src="media/js/jstz-1.0.4.min.js?ver=1.0.4"></script>
        <script type="text/javascript" src="media/js/jquery.magnific-popup.min.js?ver=1.1.0"></script>
        <script type="text/javascript" src="media/js/select2.min.js?ver=4.0.2"></script>
        <script type="text/javascript" src="media/js/bootstrap.min.js?ver=3.3.6"></script>
        <?php if(ADMIN==1) { ?>
            <script type="text/javascript" src="media/js/chart.min.js?ver=1.0.2"></script>
        <?php } ?>
        <?php if($js!="") { ?>
            <script type="text/javascript">
                $(document).ready(function(){
                    <?php echo $js; ?>
                });
            </script>
        <?php } ?>
        <?php if(SESSION_STATUS!=false) { ?>
            <script type="text/javascript">
                notify();
            </script>
        <?php } ?>
    </head>
    <body>