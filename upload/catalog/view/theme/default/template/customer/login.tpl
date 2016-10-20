<?php echo $header; ?>
<div class="purple-padding"></div>
<div class="boxed-container text-center" id="customer-login-page">
  <div class="row">
    <div id="content">
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
    <?php } ?>
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
    <?php } ?>
      <h1><?php echo $heading_title; ?></h1>
      <?php echo $content_top; ?>
      <div class="row">
        <form id="login-form" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <input type="email" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control">
          </div>
          <div class="form-group">
            <input type="password" name="password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control">
          </div>
          <div class="form-group">
            <div class="text-right">
              <a class="forgotten-password" href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>
            </div>
          </div>
          <div class="form-group">
            <input  class="btn btn-primary" type="submit" value="<?php echo $button_login; ?>"
                    ng-controller="CustomerLoginController as customerLogin"
                    ng-click="customerLogin.showLoader($event)">
          </div>
          <?php if ($redirect) { ?>
          <input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
          <?php } ?>
        </form>
      </div>
      <div>
        <?php echo $text_register; ?> <a href="<?php echo $register; ?>"><?php echo $text_sign_up; ?></a>
      </div>
      <?php echo $content_bottom; ?></div>
  </div>
</div>
<?php echo $footer; ?>