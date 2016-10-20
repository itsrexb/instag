<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div id="filter-container" class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-network_id"><?php echo $entry_network_id; ?></label>
                <input type="text" name="filter_network_id" value="<?php echo $filter_network_id; ?>" placeholder="<?php echo $entry_network_id; ?>" id="input-network_id" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-username"><?php echo $entry_username; ?></label>
                <input type="text" name="filter_username" value="<?php echo $filter_username; ?>" placeholder="<?php echo $entry_username; ?>" id="input-username" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-type"><?php echo $entry_type; ?></label>
                <select name="filter_type" id="input-type" class="form-control">
                  <?php foreach ($account_types as $key => $account_type) { ?>
                  <option value="<?php echo $key; ?>" <?php echo ($key == $filter_type ? 'selected' : ''); ?>><?php echo $account_type; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-customer"><?php echo $entry_customer; ?></label>
                <input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" placeholder="<?php echo $entry_customer; ?>" id="input-customer" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                <input type="text" name="filter_email" value="<?php echo $filter_email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-customer"><?php echo $entry_status; ?></label>
                <select  name="filter_status" id="input-status" class="form-control">
                  <option value="*"><?php echo $text_all_statuses; ?></option>
                  <?php foreach ($statuses as $status) { ?>
                  <option value="<?php echo $status; ?>" <?php echo ($status == $filter_status ? 'selected' : ''); ?>><?php echo $status; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-deleted"><?php echo $entry_deleted; ?></label>
                <select  name="filter_deleted" id="input-deleted" class="form-control">
                  <option value="*"></option>
                  <option value="0" <?php echo ($filter_deleted === '0' ? 'selected' : ''); ?>><?php echo $text_no; ?></option>
                  <option value="1" <?php echo ($filter_deleted === '1' ? 'selected' : ''); ?>><?php echo $text_yes; ?></option>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date_expires"><?php echo $entry_date_expires; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_expires_start" value="<?php echo $filter_date_expires_start; ?>" placeholder="<?php echo $entry_from; ?>" data-date-format="YYYY-MM-DD" id="input-date_expires_start" class="form-control">
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
                <br>
                <div class="input-group date">
                  <input type="text" name="filter_date_expires_end" value="<?php echo $filter_date_expires_end; ?>" placeholder="<?php echo $entry_to; ?>" data-date-format="YYYY-MM-DD" id="input-date_expires_end" class="form-control">
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date_added"><?php echo $entry_date_added; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added_start" value="<?php echo $filter_date_added_start; ?>" placeholder="<?php echo $entry_from; ?>" data-date-format="YYYY-MM-DD" id="input-date_added_start" class="form-control">
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
                <br>
                <div class="input-group date">
                  <input type="text" name="filter_date_added_end" value="<?php echo $filter_date_added_end; ?>" placeholder="<?php echo $entry_to; ?>" data-date-format="YYYY-MM-DD" id="input-date_added_end" class="form-control">
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-sm-3 pull-right">
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <?php if ($export) { ?>
          <div class="row" style="margin-bottom: 20px;"><div class="col-sm-12 text-right"><a href="<?php echo $export; ?>" class="btn btn-primary"><i class="fa fa-share"></i> <?php echo $button_export; ?></a></div></div>
          <?php } ?>
        <form action="#" method="post" enctype="multipart/form-data" id="form-customer">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td class="text-left"><?php if ($sort == 'a.username') { ?>
                    <a href="<?php echo $sort_username; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_username; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_username; ?>"><?php echo $column_username; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'a.type') { ?>
                    <a href="<?php echo $sort_type; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_type; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_type; ?>"><?php echo $column_type; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'customer') { ?>
                    <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_customer; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_customer; ?>"><?php echo $column_customer; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'c.email') { ?>
                    <a href="<?php echo $sort_email; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_email; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_email; ?>"><?php echo $column_email; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'a.status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'a.deleted') { ?>
                    <a href="<?php echo $sort_deleted; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_deleted; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_deleted; ?>"><?php echo $column_deleted; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'a.date_expires') { ?>
                    <a href="<?php echo $sort_date_expires; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_expires; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_expires; ?>"><?php echo $column_date_expires; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'a.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($accounts) { ?>
                <?php foreach ($accounts as $account) { ?>
                <tr>
                  <td class="text-left"><?php echo $account['username']; ?></td>
                  <td class="text-left"><?php echo $account['type']; ?></td>
                  <td class="text-left"><a href="<?php echo $account['href_customer']; ?>"><?php echo $account['customer']; ?></a></td>
                  <td class="text-left"><?php echo $account['email']; ?></td>
                  <td class="text-left"><?php echo $account['status']; ?></td>
                  <td class="text-left"><?php echo $account['deleted']; ?></td>
                  <td class="text-left"><?php echo $account['date_expires']; ?></td>
                  <td class="text-left"><?php echo $account['date_added']; ?></td>
                  <td class="text-right">
                    <a href="index.php?route=customer/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $account['customer_id']; ?>&store_id=0" class="btn btn-info" target="_blank" data-toggle="tooltip" title="<?php echo $button_login; ?>"><i class="fa fa-sign-in"></i></a>
                    <a href="<?php echo $account['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="18"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
$('input[name]').on('keydown', function(e) {
	if (e.keyCode == 13) {
		if(!$(this).siblings('ul.dropdown-menu').is(':visible')){
			$('#button-filter').trigger('click');
		}
	}
});

$('#button-filter').on('click', function() {
	var url      = 'index.php?route=customer/account&token=<?php echo $token; ?>',
			$inputs  = $('input[name]', '#filter-container'),
			$selects = $('select[name]', '#filter-container');

	for (var i = 0, input; input = $inputs[i]; ++i) {
		var $input = $(input), value = $input.val();

		if (value) {
			url += '&' + $input.attr('name') + '=' + encodeURIComponent(value);
		}
	}

	for (var i = 0, select; select = $selects[i]; ++i) {
		var $select = $(select), value = $select.val();

		if (value != '*') {
			url += '&' + $select.attr('name') + '=' + encodeURIComponent(value);
		}
	}

	location = url;
});

// Customer Name Autocomplete
$('input[name="filter_customer"]').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=customer/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['customer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name="filter_customer"]').val(item['label']);
	}
});

$('.date').datetimepicker({
	pickTime: false 
});
</script>
<?php echo $footer; ?>