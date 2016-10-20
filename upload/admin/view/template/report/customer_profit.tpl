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
                <label class="control-label" for="input-group"><?php echo $entry_group; ?></label>
                <select name="filter_group" id="input-group" class="form-control">
                  <?php foreach ($groups as $group) { ?>
                  <?php if ($group['value'] == $filter_group) { ?>
                  <option value="<?php echo $group['value']; ?>" selected="selected"><?php echo $group['text']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $group['value']; ?>"><?php echo $group['text']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>              
            </div>
            <div class="col-sm-3 pull-right">
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <td class="text-left"><?php echo $column_date_start; ?></td>
                <td class="text-left"><?php echo $column_date_end; ?></td>
                <td class="text-right"><?php echo $column_total_customers; ?></td>
                <td class="text-right"><?php echo $column_total_paid_accounts; ?></td>
                <td class="text-right"><?php echo $column_total; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if ($orders) { ?>
              <?php foreach ($orders as $order) { ?>
              <tr>
                <td class="text-left"><?php echo $order['date_start']; ?></td>
                <td class="text-left"><?php echo $order['date_end']; ?></td>
                <td class="text-right"><?php echo $order['total_customers']; ?></td>
                <td class="text-right"><?php echo $order['total_paid_accounts']; ?></td>
                <td class="text-right"><?php echo $order['total']; ?></td>
              </tr>
              <?php } ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
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
		if(!$(this).siblings('ul.dropdown-menu').is(':visible')){
      $('#button-filter').trigger('click');
    }
	}
});

$('#button-filter').on('click', function() {
	var url      = 'index.php?route=report/customer_profit&token=<?php echo $token; ?>',
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