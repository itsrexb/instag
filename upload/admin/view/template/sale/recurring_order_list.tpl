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
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-customer"><?php echo $entry_customer; ?></label>
                <input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" placeholder="<?php echo $entry_customer; ?>" id="input-customer" class="form-control">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-account_username"><?php echo $entry_account_username; ?></label>
                <input type="text" name="filter_account_username" value="<?php echo $filter_account_username; ?>" placeholder="<?php echo $entry_account_username; ?>" id="input-account_username" class="form-control">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                <input type="text" name="filter_email" value="<?php echo $filter_email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-account_type"><?php echo $entry_account_type; ?></label>
                <select name="filter_account_type" id="input-account_type" class="form-control">
                  <?php foreach ($account_types as $key => $account_type) { ?>
                  <option value="<?php echo $key; ?>" <?php echo ($key == $filter_account_type ? 'selected' : ''); ?>><?php echo $account_type; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-active"><?php echo $entry_status; ?></label>
                <select name="filter_active" id="input-active" class="form-control">
                  <option value="*" <?php echo (is_null($filter_active) ? 'selected' : ''); ?>><?php echo $text_all_statuses; ?></option>
                  <option value="1" <?php echo ($filter_active === '1' ? 'selected' : ''); ?>><?php echo $text_active; ?></option>
                  <option value="0" <?php echo ($filter_active === '0' ? 'selected' : ''); ?>><?php echo $text_inactive; ?></option>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-date_added"><?php echo $entry_date_added; ?></label>
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
          </div>
          <div class="row">
            <div class="col-sm-12">
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter ?></button>
            </div>
          </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data" id="form">
          <div class="table-responsive">
            <table class="table table-bordered table-hover multiselect-checkbox">
              <thead>
                <tr>
                  <td width="10" class="text-left"><?php if ($sort == 'ro.recurring_order_id') { ?>
                    <a href="<?php echo $sort_recurring_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_recurring_order; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_recurring_order; ?>"><?php echo $entry_recurring_order; ?></a>
                    <?php }  ?></td>
                  <td class="text-left"><?php if ($sort == 'customer') { ?>
                    <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_customer; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_customer; ?>"><?php echo $entry_customer; ?></a>
                    <?php }  ?></td>
                  <td class="text-left"><?php if ($sort == 'ro.account_type') { ?>
                    <a href="<?php echo $sort_account_type; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_account_type; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_account_type; ?>"><?php echo $entry_account_type; ?></a>
                    <?php }  ?></td>
                  <td class="text-left"><?php if ($sort == 'account_username') { ?>
                    <a href="<?php echo $sort_account_username; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_account_username; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_account_username; ?>"><?php echo $entry_account_username; ?></a>
                    <?php }  ?></td>
                  <td class="text-left"><?php if ($sort == 'c.email') { ?>
                    <a href="<?php echo $sort_email; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_email; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_email; ?>"><?php echo $entry_email; ?></a>
                    <?php }  ?></td>
                  <td class="text-left"><?php if ($sort == 'ro.active') { ?>
                    <a href="<?php echo $sort_active; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_active; ?>"><?php echo $entry_status; ?></a>
                    <?php }  ?></td>
                  <td class="text-left"><?php if ($sort == 'ro.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $entry_date_added; ?></a>
                    <?php }  ?></td>
                  <td class="text-right"><?php echo $entry_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($recurring_orders) { ?>
                <?php foreach ($recurring_orders as $recurring_order) { ?>
                <tr>
                  <td class="text-left"><?php echo $recurring_order['recurring_order_id']; ?></td>
                  <td class="text-left"><?php echo $recurring_order['customer']; ?></td>
                  <td class="text-left"><?php echo $recurring_order['account_type']; ?></td>
                  <td class="text-left"><?php echo $recurring_order['account_username']; ?></td>
                  <td class="text-left"><?php echo $recurring_order['email']; ?></td>
                  <td class="text-left"><?php echo $recurring_order['status']; ?></td>
                  <td class="text-left"><?php echo $recurring_order['date_added']; ?></td>
                  <td class="text-right"><a href="<?php echo $recurring_order['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
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
	var url      = 'index.php?route=sale/recurring_order&token=<?php echo $token; ?>',
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

$('#form input').keydown(function(e) {
	if (e.keyCode == 13) {
		filter();
	}
});

$('.date').datetimepicker({ 
	pickTime: false 
});
</script>
<?php echo $footer; ?>
