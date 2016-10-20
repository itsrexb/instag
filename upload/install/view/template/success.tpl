<?php echo $header; ?>
<div class="container">
  <header>
    <div class="row">
      <div class="col-sm-6">
        <h3><?php echo $heading_success; ?><br>
          <small><?php echo $heading_success_small; ?></small></h3>
      </div>
      <div class="col-sm-6">
        <div id="logo" class="pull-right hidden-xs">
          <img src="view/image/logo.png" alt="<?php echo $text_application; ?>" title="<?php echo $text_application; ?>">
        </div>
      </div>
    </div>
  </header>
  <div class="alert alert-danger"><?php echo $text_forget; ?></div>
  <div class="visit">
    <div class="row">
      <div class="col-sm-5 col-sm-offset-1 text-center"> <img src="view/image/icon-store.png"> <a class="btn btn-secondary" href="../"><?php echo $text_frontend; ?></a> </div>
      <div class="col-sm-5 text-center"> <img src="view/image/icon-admin.png"> <a class="btn btn-secondary" href="../admin/"><?php echo $text_admin; ?></a> </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>