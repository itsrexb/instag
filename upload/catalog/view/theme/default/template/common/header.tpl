<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html ng-app="instag-app" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="lang-<?php echo $lang; ?>">
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
  <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,400i,300,700">
  <?php foreach ($styles as $style) {  ?>
  <link rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" href="<?php echo $style['href']; ?>">
  <?php } ?>
  <?php foreach ($scripts as $script) { ?>
  <script src="<?php echo $script; ?>"></script>
  <?php } ?>
  <?php foreach ($analytics as $analytic) { ?>
  <?php echo $analytic; ?>
  <?php } ?>
</head>
<body class="<?php echo $class; if ($class != 'common-home') echo ' non-landing'; if ($logged) echo ' logged';?>">

<div id="loader" ng-controller="LoadController" ng-init="load()">
  <div class="loader"><div id="loaderImage"></div></div>
</div>

<header id="header" ng-controller="HeaderController as header">
  <div class="container">
    <div id="logo">
      <a href="<?php echo $href_home; ?>">
        <img src="<?php echo $logo; ?>" id="text-logo">
        <div id="logo-u-container">
          <img src="catalog/view/theme/default/image/logo-u.svg" onerror="getElementById('logo-u').remove()" id="logo-u">
        </div>
      </a>
    </div>
    <div id="mobile-menu" ng-click="header.showMobileMenu()">
      <span></span>
    </div>
    <div id="top-links" class="pull-right">
      <?php if ($logged) { ?>
      <li class="dropdown">
        <div id="header-username">
          <a href="<?php echo $href_dashboard; ?>" title="<?php echo $email; ?>" class="dropdown-toggle" data-toggle="dropdown">
            <span></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-right">
            <li><a href="<?php echo $href_dashboard; ?>"><?php echo $text_dashboard; ?></a></li>
            <li><a id="profile-top" href="<?php echo $href_profile; ?>"><?php echo $text_profile; ?></a></li>
            <li><a href="<?php echo $href_history; ?>"><?php echo $text_order_history;; ?></a></li>
            <li><a id="logout-top" href="<?php echo $href_logout; ?>"><?php echo $text_logout; ?></a></li>
          </ul>
        </div>
      </li>
      <?php } else { ?>
        <a id="link-home" href="<?php echo $href_home; ?>">
          <?php echo $link_home; ?>
        </a>
        <button id="button-login" ng-click="header.displayLogin()">
          <?php echo $button_login; ?>
        </button>
        <a id="link-register" href="<?php echo $href_register; ?>">
          <button id="button-register">
            <?php echo $button_register; ?>
          </button>
        </a>
      <?php } ?>
    </div>
  </div>
</header>

<div  id="tooltip-container"
      ng-controller="TooltipsController as tooltip"
      ng-mouseover="tooltip.hideTooltip($event)"
      ng-click="tooltip.hideTooltip($event)">
  <div id="tooltip"></div>
</div>


<div class="modal" id="login-modal">
  <div  class="modal-shadow"
        ng-controller="LoginController as login"
        ng-click="login.closeLogin($event)">
  </div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="text-center">
        <h4 style="color: #fff; font-family: 'G', Helvetica, Arial, sans-serif; margin-top: 0px;">Welcome Back</h4>
      </div>

      <div id="login-form-container">
        <form  action="" novalidate onsubmit="return false">
          <div class="email-container">
            <div  id="login-error"
                  ng-controller="TooltipsController as tooltip"
                  ng-click="tooltip.displayTooltip($event)"
                  ng-mouseover="tooltip.hideTooltip($event)"
                  data-tooltip=""
                  data-tooltip-position="top"
                  data-tooltip-style="light"
                  data-hide-fire="mouseover focus"></div>
            <input type="email" name="email" id="login-email" placeholder="<?php echo $entry_email; ?>" ng-focus="tooltip.hideTooltip($event)" data-hide-fire="focus">
          </div>
          <div class="password-container">
            <input type="password" name="password" id="login-password" placeholder="<?php echo $entry_password; ?>" ng-focus="tooltip.hideTooltip($event)" data-hide-fire="focus">
          </div>
          <div id="password-settings">
            <div class="forgot-password">
              <a href="<?php echo $href_forgotten; ?>"><?php echo $text_forgot_password; ?></a>
            </div>
          </div>
          <button ng-controller="LoginController as login" type="submit" class="gold-button" ng-click="login.login()"><?php echo $button_login; ?></button>
        </form>
      </div>
      <div class="sign-up-link">
        <span>
          <?php echo $text_no_account; ?>
          <a href="<?php echo $href_register; ?>"><?php echo $text_sign_up ?></a>
        </span>
      </div>
    </div>
  </div>
</div>