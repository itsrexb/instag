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
		<label class="control-label" for="input-customer"><?php echo $entry_customer; ?></label>
		<input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" placeholder="<?php echo $entry_customer; ?>" id="input-customer" class="form-control">
	      </div>
	      <div class="form-group">
		<label class="control-label" for="input-account"><?php echo $entry_account; ?></label>
		<input type="text" name="filter_account" value="<?php echo $filter_account; ?>" placeholder="<?php echo $entry_account; ?>" id="input-account" class="form-control">
	      </div>
	    </div>          
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-date-start"><?php echo $entry_date_start; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
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
                <td class="text-left"><?php echo $column_customer; ?></td>
                <td class="text-left"><?php echo $column_account; ?></td>
                <td class="text-right"><?php echo $column_followers_beginning; ?></td>
                <td class="text-right"><?php echo $column_followers_ending; ?></td>
                <td class="text-right"><?php echo $column_change; ?></td>
                <td class="text-right"><?php echo $column_change_percentage; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if ($account_followers) { ?>
              <?php foreach ($account_followers as $account_follower) { ?>
              <tr>
                <td class="text-left"><?php echo $account_follower['customer']; ?></td>
                <td class="text-left"><?php echo $account_follower['username']; ?></td>
                <td class="text-right"><?php echo $account_follower['followers_beginning']; ?></td>
                <td class="text-right"><?php echo $account_follower['followers_ending']; ?></td>
                <td class="text-right"><?php echo $account_follower['change']; ?></td>
                <td class="text-right"><?php echo $account_follower['change_percentage']; ?></td>
              </tr>
              <?php } ?>
              <tr>
                <td class="text-left"></td>
                <td class="text-left"></td>
                <td class="text-right"><b><?php echo $total_followers_beginning; ?></b></td>
                <td class="text-right"><b><?php echo $total_followers_ending; ?></b></td>
                <td class="text-right"><b><?php echo $total_change; ?></b></td>
                <td class="text-right"><b><?php echo $total_change_percentage; ?></b></td>                
              </tr>              
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	var url      = 'index.php?route=report/account_follower_growth&token=<?php echo $token; ?>',
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
// Account Autocomplete
$('input[name="filter_account"]').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=report/account_follower_growth/autocomplete&token=<?php echo $token; ?>&filter_username=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['username'],
						value: item['account_id']
					}
				}));
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}			
		});
	},
	'select': function(item) {
		$('input[name="filter_account"]').val(item['label']);
	}	
});
//--></script> 
<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>
