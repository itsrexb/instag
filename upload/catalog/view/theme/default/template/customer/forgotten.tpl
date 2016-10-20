<?php echo $header; ?>
<div class="purple-padding"></div>
<div class="boxed-container text-center" id="profile-page">
  <div class="row">
    <div id="content">
      <?php if ($error_warning) { ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
      <?php } ?>
          <?php echo $content_top; ?>
          <h1><?php echo $heading_title; ?></h1>
          <p><?php echo $text_email; ?></p>
          <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <fieldset>
              <legend><?php echo $text_your_email; ?></legend>
              <div class="form-group required">
                <input type="text" name="email" value="" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control">
              </div>
            </fieldset>
            <div class="buttons clearfix">
              <div>
                <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary">
              </div>
            </div>
          </form>
          <?php echo $content_bottom; ?>
        </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>