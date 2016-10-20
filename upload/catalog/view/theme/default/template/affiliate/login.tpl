<?php echo $header; ?>
<div class="purple-padding"></div>
<div class="boxed-container text-center" id="affiliate-login-page">
  <div class="row">
    <div id="content" class="no-fixed">
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
    <?php } ?>
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
    <?php } ?>
      <h1><?php echo $heading_title; ?></h1>
      <?php echo $content_top; ?>
      <div class="row">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <input type="email" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control">
            <input type="password" name="password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control">
            <div class="pull-right">
              <a class="forgotten-password" href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>
            </div>
          </div>
          <input class="gold-button" type="submit" value="<?php echo $button_login; ?>" class="btn btn-primary">
          <?php if ($redirect) { ?>
          <input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
          <?php } ?>
        </form>
      </div>
      <div id="register-affiliate-link">
          <?php echo $text_register; ?> <a class="" href="<?php echo $register; ?>"><?php echo $text_register_link; ?></a>
      </div>
      <?php echo $content_bottom; ?></div>
  </div>
</div>
<?php echo $footer; ?>