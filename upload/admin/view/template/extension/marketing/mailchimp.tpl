<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-free-checkout" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-free-checkout" class="form-horizontal" autocomplete="off">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="mailchimp_status" id="input-status" class="form-control" autocomplete="off">
                <?php if ($mailchimp_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-debug"><?php echo $entry_debug; ?></label>
            <div class="col-sm-10">
              <select name="mailchimp_debug" id="input-debug" class="form-control" autocomplete="off">
                <?php if ($mailchimp_debug) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-api_key"><?php echo $entry_api_key; ?></label>
            <div class="col-sm-10">
              <input type="text" name="mailchimp_api_key" value="<?php echo $mailchimp_api_key; ?>" placeholder="<?php echo $entry_api_key; ?>" id="input-api_key" class="form-control" autocomplete="off">
              <?php if ($error_api_key) { ?>
              <div class="text-danger"><?php echo $error_api_key; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php if ($mailchimp_lists) { ?>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-customer_list_id"><?php echo $entry_customer_list; ?></label>
            <div class="col-sm-10">
              <select name="mailchimp_customer_list_id" id="input-customer_list_id" class="form-control" autocomplete="off">
                <option value=""><?php echo $text_select; ?></option>
                <?php foreach ($mailchimp_lists as $mailchimp_list) { ?>
                <?php if ($mailchimp_list->id == $mailchimp_customer_list_id) { ?>
                <option value="<?php echo $mailchimp_list->id; ?>" selected="selected"><?php echo $mailchimp_list->name; ?></option>
                <?php } else { ?>
                <option value="<?php echo $mailchimp_list->id; ?>"><?php echo $mailchimp_list->name; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
              <?php if ($error_customer_list) { ?>
              <div class="text-danger"><?php echo $error_customer_list; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
          <?php if ($mailchimp_fields) { ?>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-store_id"><?php echo $entry_store_id; ?></label>
            <div class="col-sm-10">
              <input type="text" name="mailchimp_store_id" value="<?php echo $mailchimp_store_id; ?>" placeholder="<?php echo $entry_store_id; ?>" id="input-store_id" class="form-control" autocomplete="off">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-account_status_tag"><?php echo $entry_account_status_field; ?></label>
            <div class="col-sm-10">
              <select name="mailchimp_account_status_tag" id="input-account_status_tag" class="form-control" autocomplete="off">
                <option value=""><?php echo $text_none; ?></option>
                <?php foreach ($mailchimp_fields as $mailchimp_field) { ?>
                <?php if ($mailchimp_field->tag == $mailchimp_account_status_tag) { ?>
                <option value="<?php echo $mailchimp_field->tag; ?>" selected="selected"><?php echo $mailchimp_field->name; ?></option>
                <?php } else { ?>
                <option value="<?php echo $mailchimp_field->tag; ?>"><?php echo $mailchimp_field->name; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-country_tag"><?php echo $entry_country_field; ?></label>
            <div class="col-sm-10">
              <select name="mailchimp_country_tag" id="input-country_tag" class="form-control" autocomplete="off">
                <option value=""><?php echo $text_none; ?></option>
                <?php foreach ($mailchimp_fields as $mailchimp_field) { ?>
                <?php if ($mailchimp_field->tag == $mailchimp_country_tag) { ?>
                <option value="<?php echo $mailchimp_field->tag; ?>" selected="selected"><?php echo $mailchimp_field->name; ?></option>
                <?php } else { ?>
                <option value="<?php echo $mailchimp_field->tag; ?>"><?php echo $mailchimp_field->name; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-currency_tag"><?php echo $entry_currency_field; ?></label>
            <div class="col-sm-10">
              <select name="mailchimp_currency_tag" id="input-currency_tag" class="form-control" autocomplete="off">
                <option value=""><?php echo $text_none; ?></option>
                <?php foreach ($mailchimp_fields as $mailchimp_field) { ?>
                <?php if ($mailchimp_field->tag == $mailchimp_currency_tag) { ?>
                <option value="<?php echo $mailchimp_field->tag; ?>" selected="selected"><?php echo $mailchimp_field->name; ?></option>
                <?php } else { ?>
                <option value="<?php echo $mailchimp_field->tag; ?>"><?php echo $mailchimp_field->name; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-plan_tag"><?php echo $entry_plan_field; ?></label>
            <div class="col-sm-10">
              <select name="mailchimp_plan_tag" id="input-plan_tag" class="form-control" autocomplete="off">
                <option value=""><?php echo $text_none; ?></option>
                <?php foreach ($mailchimp_fields as $mailchimp_field) { ?>
                <?php if ($mailchimp_field->tag == $mailchimp_plan_tag) { ?>
                <option value="<?php echo $mailchimp_field->tag; ?>" selected="selected"><?php echo $mailchimp_field->name; ?></option>
                <?php } else { ?>
                <option value="<?php echo $mailchimp_field->tag; ?>"><?php echo $mailchimp_field->name; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <?php } ?>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 