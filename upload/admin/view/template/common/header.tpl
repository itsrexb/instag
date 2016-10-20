<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $title; ?></title>
  <base href="<?php echo $base; ?>">
  <?php foreach ($links as $link) { ?>
  <link rel="<?php echo $link['rel']; ?>" href="<?php echo $link['href']; ?>">
  <?php } ?>
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
  <link type="text/css" rel="stylesheet" href="view/javascript/summernote/summernote.<?php echo filemtime('view/javascript/summernote/summernote.css'); ?>.css">
  <link type="text/css" rel="stylesheet" href="view/javascript/chosen/chosen.bootstrap.min.<?php echo filemtime('view/javascript/chosen/chosen.bootstrap.min.css'); ?>.css">
  <link type="text/css" rel="stylesheet" media="screen" href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.<?php echo filemtime('view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css'); ?>.css">
  <link type="text/css" rel="stylesheet" media="screen" href="view/stylesheet/stylesheet.<?php echo filemtime('view/stylesheet/stylesheet.css'); ?>.css">
  <?php foreach ($styles as $style) { ?>
  <link type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" href="<?php echo $style['href']; ?>">
  <?php } ?>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <script src="view/javascript/summernote/summernote.<?php echo filemtime('view/javascript/summernote/summernote.js'); ?>.js"></script>
  <script src="view/javascript/chosen/chosen.jquery.min.<?php echo filemtime('view/javascript/chosen/chosen.jquery.min.js'); ?>.js"></script>
  <script src="view/javascript/jquery/datetimepicker/moment.<?php echo filemtime('view/javascript/jquery/datetimepicker/moment.js'); ?>.js"></script>
  <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.<?php echo filemtime('view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js'); ?>.js"></script>
  <script src="view/javascript/common.<?php echo filemtime('view/javascript/common.js'); ?>.js"></script>
  <?php foreach ($scripts as $script) { ?>
  <script src="<?php echo $script; ?>"></script>
  <?php } ?>
</head>
<body>
<div id="container">
<header id="header" class="navbar navbar-static-top">
  <?php if ($logged) { ?>
  <a type="button" id="button-menu" class="pull-left"><i class="fa fa-indent fa-lg"></i></a>
  <?php } ?>
  <a href="<?php echo $home; ?>" class="navbar-brand"><img src="view/image/logo.png" alt="<?php echo $heading_title; ?>" title="<?php echo $heading_title; ?>"></a>
  <?php if ($logged) { ?>
  <ul class="nav pull-right">
    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-life-ring fa-lg"></i></a>
      <ul class="dropdown-menu dropdown-menu-right">
        <li class="dropdown-header"><?php echo $text_store; ?></li>
        <?php foreach ($stores as $store) { ?>
        <li><a href="<?php echo $store['href']; ?>" target="_blank"><?php echo $store['name']; ?></a></li>
        <?php } ?>
        <li class="dropdown-header">&nbsp;</li>
        <li class="dropdown-header"><?php echo $text_help; ?></li>
        <li><a href="https://instagsocialhelp.zendesk.com/agent/" target="_blank"><?php echo $text_zendesk; ?></a></li>
        <li><a href="https://dashboard.zopim.com/" target="_blank"><?php echo $text_zopim; ?></a></li>
        <li><a href="https://instagsocialhelp.zendesk.com/hc/" target="_blank"><?php echo $text_support; ?></a></li>
        <li><a href="https://instagsocial.com/webmail/" target="_blank"><?php echo $text_webmail; ?></a></li>
      </ul>
    </li>
    <li><a href="<?php echo $logout; ?>"><span class="hidden-xs hidden-sm hidden-md"><?php echo $text_logout; ?></span> <i class="fa fa-sign-out fa-lg"></i></a></li>
  </ul>
  <?php } ?>
</header>