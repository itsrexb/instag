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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row" id="filter-container">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-country"><?php echo $entry_country; ?></label>
                <input type="text" name="filter_country" value="<?php echo $filter_country; ?>" placeholder="<?php echo $entry_country; ?>" id="input-country" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-recurring_order"><?php echo $entry_recurring_order; ?></label>
                <select name="filter_recurring_order" id="input-recurring_order" class="form-control">
                  <option value="*"></option>
                  <?php if (!is_null($filter_recurring_order) && !$filter_recurring_order) { ?>
                  <option value="0" selected="selected"><?php echo $text_no; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_no; ?></option>
                  <?php } ?>
                  <?php if (!is_null($filter_recurring_order) && $filter_recurring_order) { ?>
                  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_yes; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-start"><?php echo $entry_date_start; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-end"><?php echo $entry_date_end; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <?php if ($export) { ?>
        <div class="row" style="margin-bottom: 20px;"><div class="col-sm-12 text-right"><a href="<?php echo $export; ?>" class="btn btn-primary"><i class="fa fa-share"></i> <?php echo $button_export; ?></a></div></div>
        <?php } ?>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left"><?php echo $column_customer; ?></td>
                <td class="text-left"><?php echo $column_account; ?></td>
                <td class="text-left"><?php echo $column_email; ?></td>
                <td class="text-left"><?php echo $column_telephone; ?></td>
                <td class="text-left"><?php echo $column_country; ?></td>
                <td class="text-left"><?php echo $column_recurring_order; ?></td>
                <td class="text-right"><?php echo $column_total_spent; ?></td>
                <td class="text-right"><?php echo $column_date_last_decline; ?></td>
                <td class="text-right"><?php echo $column_action; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if ($account_declines) { ?>
              <?php foreach ($account_declines as $account_decline) { ?>
              <tr>
                <td class="text-left"><a href="<?php echo $account_decline['href_customer']; ?>"><?php echo $account_decline['customer']; ?></a></td>
                <td class="text-left"><a href="<?php echo $account_decline['href_account']; ?>"><?php echo $account_decline['username']; ?></a></td>
                <td class="text-right"><?php echo $account_decline['email']; ?></td>
                <td class="text-right"><?php echo $account_decline['telephone']; ?></td>
                <td class="text-right"><?php echo $account_decline['country']; ?></td>
                <td class="text-right"><?php echo $account_decline['recurring_order']; ?></td>
                <td class="text-right"><?php echo $account_decline['total_spent']; ?></td>
                <td class="text-right"><a href="<?php echo $account_decline['href_order']; ?>"><?php echo $account_decline['date_last_decline']; ?></a></td>
                <td class="text-right">
                  <a href="<?php echo $account_decline['href_login']; ?>" data-toggle="tooltip" title="<?php echo $button_login; ?>" class="btn btn-info" target="_blank"><i class="fa fa-sign-in"></i></a>
                </td>
              </tr>
              <?php } ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
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
	var url      = 'index.php?route=report/account_decline&token=<?php echo $token; ?>',
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

$('.date').datetimepicker({
	pickTime: false
});
</script>
<?php echo $footer; ?>