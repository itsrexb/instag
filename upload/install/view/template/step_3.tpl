<?php echo $header; ?>
<div class="container">
  <header>
    <div class="row">
      <div class="col-sm-6">
        <h3><?php echo $heading_step_3; ?><br>
          <small><?php echo $heading_step_3_small; ?></small></h3>
      </div>
      <div class="col-sm-6">
        <div id="logo" class="pull-right hidden-xs">
          <img src="view/image/logo.png" alt="<?php echo $text_application; ?>" title="<?php echo $text_application; ?>">
        </div>
      </div>
    </div>
  </header>
  <?php if ($error_warning) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <div class="row">
    <div class="col-sm-9">
      <form action="<?php echo $link_action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
        <p><?php echo $text_config_database; ?></p>
        <fieldset>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-db-driver"><?php echo $entry_db_driver; ?></label>
            <div class="col-sm-10">
              <select name="db_driver" id="input-db-driver" class="form-control">
              	<?php foreach ($db_drivers as $key => $label) { ?>
              	<?php if ($db_driver == $key) { ?>
                <option value="<?php echo $key; ?>" selected="selected"><?php echo $label; ?></option>
                <?php } else { ?>
                <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                <?php } ?>
              	<?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-db-hostname"><?php echo $entry_db_hostname; ?></label>
            <div class="col-sm-10">
              <input type="text" name="db_hostname" value="<?php echo $db_hostname; ?>" id="input-db-hostname" class="form-control">
              <?php if ($error_db_hostname) { ?>
              <div class="text-danger"><?php echo $error_db_hostname; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-db-port"><?php echo $entry_db_port; ?></label>
            <div class="col-sm-10">
              <input type="text" name="db_port" value="<?php echo $db_port; ?>" id="input-db-port" class="form-control">
              <?php if ($error_db_port) { ?>
              <div class="text-danger"><?php echo $error_db_port; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-db-database"><?php echo $entry_db_database; ?></label>
            <div class="col-sm-10">
              <input type="text" name="db_database" value="<?php echo $db_database; ?>" id="input-db-database" class="form-control">
              <?php if ($error_db_database) { ?>
              <div class="text-danger"><?php echo $error_db_database; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-db-username"><?php echo $entry_db_username; ?></label>
            <div class="col-sm-10">
              <input type="text" name="db_username" value="<?php echo $db_username; ?>" id="input-db-username" class="form-control">
              <?php if ($error_db_username) { ?>
              <div class="text-danger"><?php echo $error_db_username; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-db-password"><?php echo $entry_db_password; ?></label>
            <div class="col-sm-10">
              <input type="password" name="db_password" value="<?php echo $db_password; ?>" id="input-db-password" class="form-control">
            </div>
          </div>     
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-db-prefix"><?php echo $entry_db_prefix; ?></label>
            <div class="col-sm-10">
              <input type="text" name="db_prefix" value="<?php echo $db_prefix; ?>" id="input-db-prefix" class="form-control">
              <?php if ($error_db_prefix) { ?>
              <div class="text-danger"><?php echo $error_db_prefix; ?></div>
              <?php } ?>
           </div>
          </div>
        </fieldset>
        <p><?php echo $text_config_instaghive; ?></p>
        <fieldset>
          <!--<div class="form-group required">
            <label class="col-sm-2 control-label" for="input-instaghive_environment"><?php echo $entry_environment; ?></label>
            <div class="col-sm-10">
            	<select name="instaghive_environment" id="input-instaghive_environment" class="form-control">
              	<?php foreach ($instaghive_environments as $key => $label) { ?>
              	<?php if ($instaghive_environment == $key) { ?>
                <option value="<?php echo $key; ?>" selected="selected"><?php echo $label; ?></option>
                <?php } else { ?>
                <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                <?php } ?>
              	<?php } ?>
              </select>
            </div>
          </div>-->
          <input type="hidden" name="instaghive_environment" value="development">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-instaghive_key"><?php echo $entry_key; ?></label>
            <div class="col-sm-10">
              <input type="text" name="instaghive_key" value="<?php echo $instaghive_key; ?>" id="input-instaghive_key" class="form-control">
              <?php if ($error_instaghive_key) { ?>
              <div class="text-danger"><?php echo $error_instaghive_key; ?></div>
              <?php } ?>
            </div>
          </div>
        </fieldset>
        <p><?php echo $text_config_instagram; ?></p>
        <fieldset>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-instagram_client_id"><?php echo $entry_client_id; ?></label>
            <div class="col-sm-10">
              <input type="text" name="instagram_client_id" value="<?php echo $instagram_client_id; ?>" id="input-instagram_client_id" class="form-control">
              <?php if ($error_instagram_client_id) { ?>
              <div class="text-danger"><?php echo $error_instagram_client_id; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-instagram_client_secret"><?php echo $entry_client_secret; ?></label>
            <div class="col-sm-10">
              <input type="text" name="instagram_client_secret" value="<?php echo $instagram_client_secret; ?>" id="input-instagram_client_secret" class="form-control">
              <?php if ($error_instagram_client_secret) { ?>
              <div class="text-danger"><?php echo $error_instagram_client_secret; ?></div>
              <?php } ?>
            </div>
          </div>
        </fieldset>
        <p><?php echo $text_config_admin_user; ?></p>
        <fieldset>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-username"><?php echo $entry_username; ?></label>
            <div class="col-sm-10">
              <input type="text" name="username" value="<?php echo $username; ?>" id="input-username" class="form-control">
              <?php if ($error_username) { ?>
              <div class="text-danger"><?php echo $error_username; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-password"><?php echo $entry_password; ?></label>
            <div class="col-sm-10">
              <input type="password" name="password" value="<?php echo $password; ?>" id="input-password" class="form-control">
              <?php if ($error_password) { ?>
              <div class="text-danger"><?php echo $error_password; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></label>
            <div class="col-sm-10">
              <input type="text" name="email" value="<?php echo $email; ?>" id="input-email" class="form-control">
              <?php if ($error_email) { ?>
              <div class="text-danger"><?php echo $error_email; ?></div>
              <?php } ?>
            </div>
          </div>
        </fieldset>
        <div class="buttons">
          <div class="pull-left"><a href="<?php echo $link_back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
          <div class="pull-right">
            <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary">
          </div>
        </div>
      </form>
    </div>
    <div class="col-sm-3">
      <ul class="list-group">
        <li class="list-group-item"><?php echo $text_license; ?></li>
        <li class="list-group-item"><?php echo $text_installation; ?></li>
        <li class="list-group-item"><b><?php echo $text_configuration; ?></b></li>
        <li class="list-group-item"><?php echo $text_finished; ?></li>
      </ul>
    </div>
  </div>
</div>
<?php echo $footer; ?>