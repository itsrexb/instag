<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="index.php?route=customer/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>&store_id=0" class="btn btn-info" target="_blank" data-toggle="tooltip" title="<?php echo $button_login; ?>"><i class="fa fa-sign-in"></i></a>
        <button type="submit" form="form-customer" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
        <?php if (!$deleted) { ?>
            <a onclick="confirm('<?php echo $text_confirm; ?>') ? location.href = '<?php echo $delete; ?>' : false;" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash"></i></a>
        <?php } ?>
      </div>
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
    <?php if ($deleted) { ?>
    <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $info_deleted; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-customer" class="form-horizontal">
          <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer_id; ?>">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-account-id"><?php echo $entry_account_id; ?></label>
            <div class="col-sm-6">
              <p class="form-control-static"><?php echo $account_id; ?></p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-network_id"><?php echo $entry_network_id; ?></label>
            <div class="col-sm-6">
              <p class="form-control-static"><?php echo $network_id; ?></p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-lastname"><?php echo $entry_username; ?></label>
            <div class="col-sm-6">
              <p class="form-control-static"><?php echo $username; ?></p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-type"><?php echo $entry_type; ?></label>
            <div class="col-sm-6">
              <p class="form-control-static"><?php echo $type; ?></p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-customer"><?php echo $entry_customer; ?></label>
            <div class="col-sm-6">
              <p class="form-control-static"><a href="<?php echo $href_customer; ?>"><?php echo $customer; ?></a></p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-date_added"><?php echo $entry_move_to; ?></label>
            <div class="col-sm-6 input-group datetime">
                <input type="text" value="" placeholder="<?php echo $entry_move_to; ?>" id="input-new_customer" class="form-control">
                <input type="hidden" name="new_customer_id" value="">
            </div>
          </div> 
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-date_added"><?php echo $entry_date_expires; ?></label>
            <div class="col-sm-6 input-group datetime">
              <input type="text" name="date_expires" value="<?php echo $date_expires; ?>" placeholder="<?php echo $entry_from; ?>" data-date-format="MM/DD/YYYY h:mm:ss a" id="input-date_expires" class="form-control">
              <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
              </span>
              <?php if ($error_date_expires) { ?>
              <div class="text-danger"><?php echo $error_date_expires; ?></div>
              <?php } ?>
            </div>
          </div>          
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-date_added"><?php echo $entry_date_added; ?></label>
            <div class="col-sm-6">
              <p class="form-control-static"><?php echo $date_added; ?></p>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade bs-delete" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><?php echo $text_confirmation; ?></h4>
        </div>
        <div class="modal-body">
           <h5><i class="fa fa-exclamation-triangle"></i> <?php echo $text_warning; ?></h5>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_cancel; ?></button>
          <a id="delete" class="btn btn-primary" href="javascript:void(0)" data-dismiss="modal"><?php echo $button_delete; ?></a>
        </div>
    </div>
  </div>
</div>
<script>
$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});

// Customer Name Autocomplete
$('#input-new_customer').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=customer/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request) + '&filter_email=' +  encodeURIComponent(request)+'&implode=or',
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'] + ' (' + item['email'] + ')',
						value: item['customer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('#input-new_customer').val(item['label']);
		$('input[name="new_customer_id"]').val(item['value']);
	}
});
</script>
<?php echo $footer; ?>