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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-free-checkout" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="braintree_status" id="input-status" class="form-control">
                <?php if ($braintree_status) { ?>
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
            <label class="col-sm-2 control-label" for="input-merchant_id"><?php echo $entry_merchant_id; ?></label>
            <div class="col-sm-10">
              <input type="text" name="braintree_merchant_id" value="<?php echo $braintree_merchant_id; ?>" placeholder="<?php echo $entry_merchant_id; ?>" id="input-merchant_id" class="form-control">
              <?php if ($error_merchant_id) { ?>
              <div class="text-danger"><?php echo $error_merchant_id; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-public_key"><?php echo $entry_public_key; ?></label>
            <div class="col-sm-10">
              <input type="text" name="braintree_public_key" value="<?php echo $braintree_public_key; ?>" placeholder="<?php echo $entry_public_key; ?>" id="input-public_key" class="form-control">
              <?php if ($error_public_key) { ?>
              <div class="text-danger"><?php echo $error_public_key; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-private_key"><?php echo $entry_private_key; ?></label>
            <div class="col-sm-10">
              <input type="text" name="braintree_private_key" value="<?php echo $braintree_private_key; ?>" placeholder="<?php echo $entry_private_key; ?>" id="input-private_key" class="form-control">
              <?php if ($error_private_key) { ?>
              <div class="text-danger"><?php echo $error_private_key; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-transaction_server"><?php echo $entry_transaction_server; ?></label>
            <div class="col-sm-10">
              <select name="braintree_transaction_server" id="input-transaction_server" class="form-control">
                <?php if ($braintree_transaction_server == 'production') { ?>
                <option value="production" selected="selected"><?php echo $text_production; ?></option>
                <?php } else { ?>
                <option value="production"><?php echo $text_production; ?></option>
                <?php } ?>
                <?php if ($braintree_transaction_server == 'sandbox') { ?>
                <option value="sandbox" selected="selected"><?php echo $text_sandbox; ?></option>
                <?php } else { ?>
                <option value="sandbox"><?php echo $text_sandbox; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-transaction_method"><?php echo $entry_transaction_method; ?></label>
            <div class="col-sm-10">
              <select name="braintree_transaction_method" id="input-transaction_method" class="form-control">
                <?php if ($braintree_transaction_method == 'authorization') { ?>
                <option value="authorization" selected="selected"><?php echo $text_authorization; ?></option>
                <?php } else { ?>
                <option value="authorization"><?php echo $text_authorization; ?></option>
                <?php } ?>
                <?php if ($braintree_transaction_method == 'capture') { ?>
                <option value="capture" selected="selected"><?php echo $text_capture; ?></option>
                <?php } else { ?>
                <option value="capture"><?php echo $text_capture; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order_status"><?php echo $entry_order_status; ?></label>
            <div class="col-sm-10">
              <select name="braintree_order_status_id" id="input-order_status" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $braintree_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort_order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="braintree_sort_order" value="<?php echo $braintree_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort_order" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-debug"><?php echo $entry_debug; ?></label>
            <div class="col-sm-10">
              <select name="braintree_debug" id="input-debug" class="form-control">
                <?php if ($braintree_debug) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <?php foreach ($currencies as $currency) { ?>
          <fieldset class="payment-currency">
            <legend><?php echo $currency['title']; ?> (<?php echo $currency['code']; ?>)</legend>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-currencies[<?php echo $currency['code']; ?>][status]"><?php echo $entry_visible; ?></label>
              <div class="col-sm-10">
                <select name="braintree_currencies[<?php echo $currency['code']; ?>][status]" id="input-currencies[<?php echo $currency['code']; ?>][status]" class="form-control">
                  <?php if (!empty($braintree_currencies[$currency['code']]['status'])) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group payment-currency-option">
              <label class="col-sm-2 control-label" for="input-currencies[<?php echo $currency['code']; ?>][merchant_account]"><?php echo $entry_merchant_account; ?></label>
              <div class="col-sm-10">
                <input type="text" name="braintree_currencies[<?php echo $currency['code']; ?>][merchant_account]" value="<?php echo (isset($braintree_currencies[$currency['code']]['merchant_account']) ? $braintree_currencies[$currency['code']]['merchant_account'] : ''); ?>" placeholder="<?php echo $entry_merchant_account; ?>" id="input-currencies[<?php echo $currency['code']; ?>][merchant_account]" class="form-control">
              </div>
            </div>
          </fieldset>
          <?php } ?>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 