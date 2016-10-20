<div class="well">
  <div class="row">
    <div class="col-sm-4">
      <div>
        <label class="control-label" for="input-order-id"><?php echo $entry_order_id; ?></label>
        <input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" placeholder="<?php echo $entry_order_id; ?>" id="input-order-id" class="form-control">
      </div>
      <div>
        <label class="control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
        <select name="filter_order_status" id="input-order-status" class="form-control">
          <option value="*"></option>
          <?php if ($filter_order_status == '0') { ?>
          <option value="0" selected="selected"><?php echo $text_missing; ?></option>
          <?php } else { ?>
          <option value="0"><?php echo $text_missing; ?></option>
          <?php } ?>
          <?php foreach ($order_statuses as $order_status) { ?>
          <?php if ($order_status['order_status_id'] == $filter_order_status) { ?>
          <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
           <?php } else { ?>
          <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
          <?php } ?>
          <?php } ?>
        </select>
      </div>
    </div>
    <div class="col-sm-4">
      <div>
        <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
        <div class="input-group date">
          <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control">
          <span class="input-group-btn">
          <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
          </span></div>
      </div>
      <div>
        <label class="control-label" for="input-total"><?php echo $entry_total; ?></label>
        <input type="text" name="filter_total" value="<?php echo $filter_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control">
      </div>
    </div>
    <div class="col-sm-4">
      <div>
        <label class="control-label" for="input-date-modified"><?php echo $entry_date_modified; ?></label>
        <div class="input-group date">
          <input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" placeholder="<?php echo $entry_date_modified; ?>" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control">
          <span class="input-group-btn">
          <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
          </span></div>
      </div><br>
      <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
    </div>
  </div>
</div>
<form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"></td>
          <td class="text-right"><?php if ($sort == 'o.order_id') { ?>
            <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
            <?php } else { ?>
               <a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
            <?php } ?></td>
          <td class="text-left"><?php if ($sort == 'status') { ?>
            <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
            <?php } else { ?>
            <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
            <?php } ?></td>
          <td class="text-right"><?php if ($sort == 'o.total') { ?>
            <a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total; ?></a>
            <?php } else { ?>
            <a href="<?php echo $sort_total; ?>"><?php echo $column_total; ?></a>
            <?php } ?></td>
          <td class="text-left"><?php if ($sort == 'o.date_added') { ?>
            <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
            <?php } else { ?>
            <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
            <?php } ?></td>
          <td class="text-left"><?php if ($sort == 'o.date_modified') { ?>
            <a href="<?php echo $sort_date_modified; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_modified; ?></a>
            <?php } else { ?>
            <a href="<?php echo $sort_date_modified; ?>"><?php echo $column_date_modified; ?></a>
            <?php } ?></td>
          <td class="text-right"><?php echo $column_action; ?></td>
        </tr>
      </thead>
      <tbody>
        <?php if ($orders) { ?>
        <?php foreach ($orders as $order) { ?>
        <tr>
          <td class="text-center"><?php if (in_array($order['order_id'], $selected)) { ?>
            <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" checked="checked">
            <?php } else { ?>
            <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>">
            <?php } ?></td>
          <td class="text-right"><?php echo $order['order_id']; ?></td>
          <td class="text-left"><?php echo $order['status']; ?></td>
          <td class="text-right"><?php echo $order['total']; ?></td>
          <td class="text-left"><?php echo $order['date_added']; ?></td>
          <td class="text-left"><?php echo $order['date_modified']; ?></td>
          <td class="text-right"><a href="<?php echo $order['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a></td>
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
</form>
<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>

<script>
$('#button-filter').on('click', function() {
	var url = 'index.php?route=customer/customer/order&token=<?php echo $token; ?>&filter_customer_id=<?php echo $filter_customer_id; ?>';

	var filter_order_id = $('input[name="filter_order_id"]').val();

	if (filter_order_id) {
		url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
	}

	var filter_order_status = $('select[name="filter_order_status"]').val();

	if (filter_order_status != '*') {
		url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
	}

	var filter_total = $('input[name="filter_total"]').val();

	if (filter_total) {
		url += '&filter_total=' + encodeURIComponent(filter_total);
	}

	var filter_date_added = $('input[name="filter_date_added"]').val();

	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}

	var filter_date_modified = $('input[name="filter_date_modified"]').val();

	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}

	$(this).prop('disabled', true).css({'cursor' : 'wait'});

	$('#orders').load(url);
});
</script>
<script>
$('input[name^="selected"]').on('change', function() {
	$('#button-invoice').prop('disabled', true);

	var selected = $('input[name^="selected"]:checked');

	if (selected.length) {
		$('#button-invoice').prop('disabled', false);
	}
});

$('input[name^="selected"]:first').trigger('change');

// Login to the API
var token = '';

$.ajax({
	url: '<?php echo $store; ?>index.php?route=api/login',
	type: 'post',
	data: 'key=<?php echo $api_key; ?>',
	dataType: 'json',
	crossDomain: true,
	success: function(json) {
		if (json['error']) {
			if (json['error']['key']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['key'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}

			if (json['error']['ip']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['ip'] + ' <button type="button" id="button-ip-add" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-danger btn-xs pull-right"><i class="fa fa-plus"></i> <?php echo $button_ip_add; ?></button></div>');
			}
		}

		if (json['token']) {
			token = json['token'];
		}
	},
	error: function(xhr, ajaxOptions, thrownError) {
		alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
	}
});

$(document).delegate('#button-ip-add', 'click', function() {
	$.ajax({
		url: 'index.php?route=user/api/addip&token=<?php echo $token; ?>&api_id=<?php echo $api_id; ?>',
		type: 'post',
		data: 'ip=<?php echo $api_ip; ?>',
		dataType: 'json',
		beforeSend: function() {
			$('#button-ip-add').button('loading');
		},
		complete: function() {
			$('#button-ip-add').button('reset');
		},
		success: function(json) {
			$('.alert').remove();

			if (json['error']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}

			if (json['success']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('body').css({cursor: 'auto'});

jQuery(document).ready(function($) {
	$('#form-order thead a').on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();

		$('body').css({cursor:'wait'});
		$('#orders').load($(this).attr('href'));
	});

	$('#orders .pagination a').on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();

		$('body').css({cursor:'wait'});
		$('#orders').load($(this).attr('href'));
	});
});
</script>
<script>
$('.date').datetimepicker({
	pickTime: false
});
</script>