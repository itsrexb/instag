<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html ng-app="instag-app" resizeDirective dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="lang-<?php echo $lang; ?>">
<!--<![endif]-->
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $title; ?> - <?php echo $name; ?></title>
  <base href="<?php echo $base; ?>">
  <?php if ($description) { ?>
  <meta name="description" content="<?php echo $description; ?>">
  <?php } ?>
  <?php if ($keywords) { ?>
  <meta name="keywords" content= "<?php echo $keywords; ?>">
  <?php } ?>
  <?php foreach ($links as $link) { ?>
  <link rel="<?php echo $link['rel']; ?>" href="<?php echo $link['href']; ?>">
  <?php } ?>
  <link type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,400i,300,700">
  <?php foreach ($styles as $style) { ?>
  <link type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" href="<?php echo $style['href']; ?>">
  <?php } ?>
  <?php foreach ($scripts as $script) { ?>
  <script src="<?php echo $script; ?>"></script>
  <?php } ?>
  <?php foreach ($analytics as $analytic) { ?>
  <?php echo $analytic; ?>
  <?php } ?>
</head>
<body class="<?php echo $class; ?>" id="dashboard">
<div ng-controller="ResizeController"></div>
<div id="loader" ng-controller="LoadController" ng-init="load()">
  <div class="loader"><div id="loaderImage"></div></div>
</div>
<header id="header" ng-controller="HeaderController as header">
  <div  id="logo" 
        ng-mouseover="header.openLeftSidebar(true)">
    <img src='catalog/view/theme/default/image/logo.svg' id="text-logo">
    <div id="logo-u-container" ng-click="header.openLeftSidebar()">
      <img src="catalog/view/theme/default/image/logo-u.svg" onerror="getElementById('logo-u').remove()" id="logo-u">
    </div>
    <div id="close-sidebar" ng-click="header.closeLeftSidebar()">
      <i class="fa fa-chevron-circle-left"></i>
    </div>
  </div>
  <div id="header-menu">
    <i class="fa fa-bars" ng-click="header.openMenu(true)"></i>
  </div>
</header>
<div  id="tooltip-container"
      ng-controller="TooltipsController as tooltip"
      ng-mouseover="tooltip.hideTooltip($event)"
      ng-click="tooltip.hideTooltip($event)">
  <div id="tooltip"></div>
</div>