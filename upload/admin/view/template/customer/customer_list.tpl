<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-customer').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div id="filter-container" class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control">
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
                <label class="control-label" for="input-country"><?php echo $entry_country; ?></label>
                <input type="text" name="filter_country" value="<?php echo $filter_country; ?>" placeholder="<?php echo $entry_country; ?>" id="input-country" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-account_status"><?php echo $entry_account_status; ?></label>
                <select name="filter_account_status" id="input-account_status" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($account_statuses as $key => $account_status) { ?>
                  <?php if ($key == $filter_account_status) { ?>
                  <option value="<?php echo $key; ?>" selected="selected"><?php echo $account_status; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $key; ?>"><?php echo $account_status; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-customer-group"><?php echo $entry_customer_group; ?></label>
                <select name="filter_customer_group_id" id="input-customer-group" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($customer_groups as $customer_group) { ?>
                  <?php if ($customer_group['customer_group_id'] == $filter_customer_group_id) { ?>
                  <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-affiliate"><?php echo $entry_affiliate; ?></label>
                <input type="text" value="<?php echo $filter_affiliate; ?>" placeholder="<?php echo $entry_affiliate; ?>" id="input-affiliate" class="form-control">
                <input type="hidden" name="filter_affiliate_id" value="<?php echo $filter_affiliate_id; ?>">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-ext-aff-id"><?php echo $entry_ext_aff_id; ?></label>
                <input type="text" name="filter_ext_aff_id"  value="<?php echo $filter_ext_aff_id; ?>" placeholder="<?php echo $entry_ext_aff_id; ?>" id="input-ext-aff-id" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added_start" value="<?php echo $filter_date_added_start; ?>" placeholder="<?php echo $entry_from; ?>" data-date-format="YYYY-MM-DD" id="input-date-date_added_start" class="form-control">
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
                <br>
                <div class="input-group date">
                  <input type="text" name="filter_date_added_end" value="<?php echo $filter_date_added_end; ?>" placeholder="<?php echo $entry_to; ?>" data-date-format="YYYY-MM-DD" id="input-date-date_added_end" class="form-control">
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
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-customer">
          <div class="table-responsive">
            <table class="table table-bordered table-hover multiselect-checkbox">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"></td>
                  <td class="text-left"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'c.email') { ?>
                    <a href="<?php echo $sort_email; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_email; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_email; ?>"><?php echo $column_email; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'country') { ?>
                    <a href="<?php echo $sort_country; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_country; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_country; ?>"><?php echo $column_country; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $column_account_status; ?></td>
                  <td class="text-left"><?php if ($sort == 'total_accounts') { ?>
                    <a href="<?php echo $sort_total_accounts; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total_accounts; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_total_accounts; ?>"><?php echo $column_total_accounts; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'deleted_accounts') { ?>
                    <a href="<?php echo $sort_deleted_accounts; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_deleted_accounts; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_deleted_accounts; ?>"><?php echo $column_deleted_accounts; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'total_revenue') { ?>
                    <a href="<?php echo $sort_total_revenue; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total_revenue; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_total_revenue; ?>"><?php echo $column_total_revenue; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'plan') { ?>
                    <a href="<?php echo $sort_plan; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_plan; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_plan; ?>"><?php echo $column_plan; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'customer_group') { ?>
                    <a href="<?php echo $sort_customer_group; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_customer_group; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_customer_group; ?>"><?php echo $column_customer_group; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'affiliate') { ?>
                    <a href="<?php echo $sort_affiliate; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_affiliate; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_affiliate; ?>"><?php echo $column_affiliate; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'ext_aff_id') { ?>
                    <a href="<?php echo $sort_ext_aff_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_ext_aff_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_ext_aff_id; ?>"><?php echo $column_ext_aff_id; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'c.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($customers) { ?>
                <?php foreach ($customers as $customer) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($customer['customer_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $customer['customer_id']; ?>" checked="checked">
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $customer['customer_id']; ?>">
                    <?php } ?></td>
                  <td class="text-left"><?php echo $customer['name']; ?></td>
                  <td class="text-left"><?php echo $customer['email']; ?></td>
                  <td class="text-left"><?php echo $customer['country']; ?></td>
                  <td class="text-left"><?php echo $customer['account_status']; ?></td>
                  <td class="text-left"><a href="<?php echo $customer['href_accounts']; ?>"><?php echo $customer['total_accounts']; ?></a></td>
                  <td class="text-left"><?php echo $customer['deleted_accounts']; ?></td>
                  <td class="text-left"><?php echo $customer['total_revenue']; ?></td>
                  <td class="text-left"><?php echo $customer['plan']; ?></td>
                  <td class="text-left"><?php echo $customer['customer_group']; ?></td>
                  <td class="text-left"><?php echo $customer['affiliate']; ?></td>
                  <td class="text-left"><?php echo $customer['ext_aff_id']; ?></td>
                  <td class="text-left"><?php echo $customer['date_added']; ?></td>
                  <td class="text-right"><?php if ($stores) { ?>
                    <div class="btn-group" data-toggle="tooltip" title="<?php echo $button_login; ?>">
                      <button type="button" data-toggle="dropdown" class="btn btn-info dropdown-toggle"><i class="fa fa-sign-in"></i></button>
                      <ul class="dropdown-menu pull-right">
                        <li><a href="index.php?route=customer/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $customer['customer_id']; ?>&store_id=0" target="_blank"><?php echo $text_default; ?></a></li>
                        <?php foreach ($stores as $store) { ?>
                        <li><a href="index.php?route=customer/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $customer['customer_id']; ?>&store_id=<?php echo $store['store_id']; ?>" target="_blank"><?php echo $store['name']; ?></a></li>
                        <?php } ?>
                      </ul>
                    </div>
                    <?php } else { ?>
                    <a href="index.php?route=customer/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $customer['customer_id']; ?>&store_id=0" target="_blank" data-toggle="tooltip" title="<?php echo $button_login; ?>" class="btn btn-info"><i class="fa fa-sign-in"></i></a>
                    <?php } ?>
                    <?php if ($customer['unlock']) { ?>
                    <a href="<?php echo $customer['unlock']; ?>" data-toggle="tooltip" title="<?php echo $button_unlock; ?>" class="btn btn-warning"><i class="fa fa-unlock"></i></a>
                    <?php } ?>
                    <a href="<?php echo $customer['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="14"><?php echo $text_no_results; ?></td>
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
		$('#button-filter').trigger('click');
	}
});

$('#button-filter').on('click', function() {
	var url      = 'index.php?route=customer/customer&token=<?php echo $token; ?>',
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
$('input[name="filter_name"]').autocomplete({
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
		$('input[name="filter_name"]').val(item['label']);
	}
});

// Customer Email Autocomplete
$('input[name="filter_email"]').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=customer/customer/autocomplete&token=<?php echo $token; ?>&filter_email=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['email'],
						value: item['customer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name="filter_email"]').val(item['label']);
	}
});

// Affiliate Autocomplete
$('#input-affiliate').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=affiliate/affiliate/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['affiliate_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('#input-affiliate').val(item['label']);
		$('input[name="filter_affiliate_id"]').val(item['value']);
	}
});

$('#input-affiliate').on('keyup', function() {
	if ($(this).val() === '') {
		$('input[name="filter_affiliate_id"]').val(0);
	}
});

$('.date').datetimepicker({
	pickTime: false
});
</script>
<?php echo $footer; ?>
