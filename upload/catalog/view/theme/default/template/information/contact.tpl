<?php echo $header; ?>
<div class="purple-padding"></div>
<div class="boxed-container text-center" id="contact-page">
  <div class="row">
    <div id="content">
      <?php echo $content_top; ?>
      <h1><?php echo $heading_title; ?></h1>
      <?php if ($locations) { ?>
      <h3><?php echo $text_locations; ?></h3>
      <div class="panel-group" id="accordion">
        <?php foreach ($locations as $location) { ?>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title"><a href="#collapse-location<?php echo $location['location_id']; ?>" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion"><?php echo $location['name']; ?> <i class="fa fa-caret-down"></i></a></h4>
          </div>
          <div class="panel-collapse collapse" id="collapse-location<?php echo $location['location_id']; ?>">
            <div class="panel-body">
              <div class="row">
                <?php if ($location['image']) { ?>
                <div class="col-sm-3"><img src="<?php echo $location['image']; ?>" alt="<?php echo $location['name']; ?>" title="<?php echo $location['name']; ?>" class="img-thumbnail"></div>
                <?php } ?>
                <div class="col-sm-3"><strong><?php echo $location['name']; ?></strong><br>
                  <address>
                  <?php echo $location['address']; ?>
                  </address>
                  <?php if ($location['geocode']) { ?>
                  <a href="https://maps.google.com/maps?q=<?php echo urlencode($location['geocode']); ?>&hl=<?php echo $geocode_hl; ?>&t=m&z=15" target="_blank" class="btn btn-info"><i class="fa fa-map-marker"></i> <?php echo $button_map; ?></a>
                  <?php } ?>
                </div>
                <div class="col-sm-3"> <strong><?php echo $text_telephone; ?></strong><br>
                  <?php echo $location['telephone']; ?><br>
                  <br>
                </div>
                <div class="col-sm-3">
                  <?php if ($location['open']) { ?>
                  <strong><?php echo $text_open; ?></strong><br>
                  <?php echo $location['open']; ?><br>
                  <br>
                  <?php } ?>
                  <?php if ($location['comment']) { ?>
                  <strong><?php echo $text_comment; ?></strong><br>
                  <?php echo $location['comment']; ?>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
      <?php } ?>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
        <fieldset>
          <div class="form-group required">
            <div class="col-sm-12">
              <input type="text" name="email" value="<?php echo $email; ?>" id="input-email" class="form-control" placeholder="<?php echo $entry_email; ?>">
              <?php if ($error_email) { ?>
              <div class="text-danger"><?php echo $error_email; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <div class="col-sm-12">
              <textarea name="message" rows="10" id="input-message" class="form-control" placeholder="<?php echo $entry_message; ?>"><?php echo $message; ?></textarea>
              <?php if ($error_message) { ?>
              <div class="text-danger"><?php echo $error_message; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php echo $captcha; ?>
        </fieldset>
        <div class="buttons">
          <input class="btn btn-primary gold-button" type="submit" value="<?php echo $button_submit; ?>">
        </div>
      </form>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>