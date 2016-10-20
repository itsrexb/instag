<?php echo $header; ?>
<div class="container">
  <header>
    <div class="row">
      <div class="col-sm-6">
        <h3><?php echo $heading_upgrade; ?><br>
          <small><?php echo $heading_upgrade_small; ?></small></h3>
      </div>
      <div class="col-sm-6">
        <div id="logo" class="pull-right hidden-xs">
          <img src="view/image/logo.png" alt="<?php echo $text_application; ?>" title="<?php echo $text_application; ?>">
        </div>
      </div>
    </div>
  </header>
  <div class="row">
    <div class="col-sm-9">
      <form action="<?php echo $link_action; ?>" method="post" enctype="multipart/form-data">
        <fieldset>
          <ol>
            <li><?php echo $text_upgrade_1; ?></li>
            <li><?php echo $text_upgrade_2; ?></li>
            <li><?php echo $text_upgrade_3; ?></li>
          </ol>
        </fieldset>
        <div class="buttons">
          <div class="pull-right">
            <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary">
          </div>
        </div>
      </form>
    </div>
    <div class="col-sm-3">
      <ul class="list-group">
        <li class="list-group-item"><b><?php echo $text_upgrade; ?></b></li>
        <li class="list-group-item"><?php echo $text_finished; ?></li>
      </ul>
    </div>
  </div>
</div>
<?php echo $footer; ?>