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
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="control-label" for="input-date-start"><?php echo $entry_date_start; ?></label>
                        <div class="input-group date">
                          <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                          <span class="input-group-btn">
                          <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                          </span></div>
                      </div>
                    </div>
                    <div class="col-sm-6">
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
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left"><?php if ($sort == 'name') { ?>
                  <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                  <?php } ?></td>
                <td class="text-left"><?php echo $column_accounts; ?></td>
                <td class="text-left"><?php echo $column_tags; ?></td>
                <td class="text-left"><?php echo $column_noused; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if ($account_source_interest) { ?>
              <?php foreach ($account_source_interest as $source_interest) { ?>
              <tr>
                <td class="text-left"><?php echo $source_interest['name']; ?></td>
                <td class="text-left"><?php echo $source_interest['total_accounts_low']; ?> / <?php echo $source_interest['total_accounts_medium']; ?> / <?php echo $source_interest['total_accounts_high']; ?> / <b><?php echo $source_interest['total_accounts']; ?></b></td>
                <td class="text-left"><?php echo $source_interest['total_tags_low']; ?> / <?php echo $source_interest['total_tags_medium']; ?> / <?php echo $source_interest['total_tags_high']; ?> / <b><?php echo $source_interest['total_tags']; ?></b></td>
                <td class="text-left"><?php echo $source_interest['history']; ?></td>
              </tr>
              <?php } ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="5"><?php echo $text_no_results; ?></td>
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
$('#button-filter').on('click', function() {
	var url      = 'index.php?route=report/account_source_interest&token=<?php echo $token; ?>',
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