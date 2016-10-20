<?php echo $header; ?>
<div class="container" id="checkout-page">
  <br>
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
    <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
  </div>
  <?php } ?>
  <?php if ($error_warning) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
    <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
  </div>
  <?php } ?>
  <div class="row">
    <div id="content" class="col-sm-12">
      <div class="form-inline">
        <div class="form-group">
          <label class="control-label sr-only" for="input-customer"><?php echo $entry_customer; ?></label>
          <input type="text" id="input-customer" value="" placeholder="<?php echo $entry_customer; ?>" class="form-control">
          <input type="hidden" id="input-customer_id" name="customer_id" value="">
        </div>
        <div id="customer-or" class="form-group hidden-xs">
          <?php echo $text_or; ?>
        </div>
        <div id="form-group-new-customer" class="form-group">
          <button class="btn btn-primary" data-toggle="modal" data-target="#modal-new-customer"><i class="fa fa-user-plus"></i> <?php echo $button_new_customer; ?></button>
        </div>
        <div id="button-reset" class="form-group hidden">
          <a href="<?php echo $action_reset; ?>" class="btn btn-warning"><i class="fa fa-refresh"></i> <?php echo $button_reset; ?></a>
        </div>
      </div>

      <br>

      <div id="panel-instagram" class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-instagram"></i> <?php echo $text_instagram; ?></h3>
          <div class="pull-right">
            <i class="fa fa-refresh fa-spin hidden"></i>
            <span class="hidden"><a href="#add-instagram-account" data-toggle="modal" data-target="#modal-add-instagram" data-toggle="tooltip" title="<?php echo $tooltip_add_instagram; ?>"><i class="fa fa-plus"></i></a></span>
          </div>
        </div>

        <div class="panel-body hidden">
          <div class="table-responsive">
            <table id="instagram-accounts" class="table">
              <thead>
                <tr>
                  <td class="text-left"><?php echo $column_account; ?></td>
                  <td class="text-left"><?php echo $column_time; ?></td>
                  <td class="text-left"><?php echo $column_plan; ?></td>
                  <td class="text-left"><?php echo $column_price; ?></td>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6">
          <div id="panel-customer" class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title"><i class="fa fa-user"></i> <?php echo $text_customer; ?></h3>
              <div class="pull-right">
                <i class="fa fa-refresh fa-spin hidden"></i>
              </div>
            </div>

            <div class="panel-body hidden">
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="control-label sr-only" for="input-firstname"><?php echo $entry_firstname; ?></label>
                  <input type="text" id="input-firstname" name="firstname" value="" placeholder="<?php echo $entry_firstname; ?>" class="form-control">
                </div>
                <div class="form-group">
                  <label class="control-label sr-only" for="input-lastname"><?php echo $entry_lastname; ?></label>
                  <input type="text" id="input-lastname" name="lastname" value="" placeholder="<?php echo $entry_lastname; ?>" class="form-control">
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group">
                  <label class="control-label sr-only" for="input-email"><?php echo $entry_email; ?></label>
                  <input type="text" id="input-email" name="email" value="" placeholder="<?php echo $entry_email; ?>" class="form-control">
                </div>
                <div class="form-group">
                  <label class="control-label sr-only" for="input-telephone"><?php echo $entry_telephone; ?></label>
                  <input type="text" id="input-telephone" name="telephone" value="" placeholder="<?php echo $entry_telephone; ?>" class="form-control">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6">
          <div id="panel-payment" class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title"><i class="fa fa-credit-card"></i> <?php echo $text_payment; ?></h3>
              <div class="pull-right">
                <i class="fa fa-refresh fa-spin hidden"></i>
                <h3><strong id="total"></strong></h3>
              </div>
            </div>

            <div class="panel-body hidden">
              <div id="payment-form"></div>
              <div class="buttons">
                <div class="pull-right">
                  <input type="button" id="button-confirm" value="<?php echo $button_confirm; ?>" class="btn btn-success" data-loading-text="<?php echo $text_processing_payment; ?>" data-success="<?php echo $action_success; ?>">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-new-customer" tabindex="-1" role="dialog" aria-labelledby="new-customer-label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="new-customer-label"><?php echo $text_new_customer; ?></h4>
      </div>
      <div class="modal-body">
        <form action="<?php echo $action_new_customer; ?>" method="get" enctype="multipart/form-data" id="form-new-customer">
          <div class="form-group">
            <label class="sr-only" for="customer_firstname"><?php echo $entry_firstname; ?></label>
            <input type="text" id="customer_firstname" name="firstname" placeholder="<?php echo $entry_firstname; ?>" class="form-control">
          </div>
          <div class="form-group">
            <label class="sr-only" for="customer_lastname"><?php echo $entry_lastname; ?></label>
            <input type="text" id="customer_lastname" name="lastname" placeholder="<?php echo $entry_lastname; ?>" class="form-control">
          </div>
          <div class="form-group">
            <label class="sr-only" for="customer_email"><?php echo $entry_email; ?></label>
            <input type="text" id="customer_email" name="email" placeholder="<?php echo $entry_email; ?>" class="form-control">
          </div>
          <div class="form-group">
            <label class="sr-only" for="customer_password"><?php echo $entry_password; ?></label>
            <input type="password" id="customer_password" name="password" placeholder="<?php echo $entry_password; ?>" class="form-control">
          </div>
          <input type="submit" class="sr-only">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_cancel; ?></button>
        <button type="button" id="button-new-customer" class="btn btn-primary disabled" data-loading-text="<?php echo $text_creating; ?>"><?php echo $button_new_customer; ?></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-add-instagram" tabindex="-1" role="dialog" aria-labelledby="add-instagram-label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="add-instagram-label"><?php echo $text_add_instagram; ?></h4>
      </div>
      <div class="modal-body">
        <div class="alert alert-success" role="alert">
          <?php echo $text_privacy_password; ?>
        </div>
        <form action="<?php echo $action_new_instagram; ?>" method="get" enctype="multipart/form-data" id="form-add-instagram">
          <div class="form-group">
            <label class="sr-only" for="instagram_username"><?php echo $entry_username; ?></label>
            <input type="text" id="instagram_username" name="username" placeholder="<?php echo $entry_username; ?>" class="form-control">
          </div>
          <div class="form-group">
            <label class="sr-only" for="instagram_password"><?php echo $entry_password; ?></label>
            <input type="password" id="instagram_password" name="password" placeholder="<?php echo $entry_password; ?>" class="form-control">
          </div>
          <input type="submit" class="sr-only">
          <input type="hidden" id="instagram-customer_id" name="customer_id">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_cancel; ?></button>
        <button type="button" id="button-add-instagram" class="btn btn-primary disabled" data-loading-text="<?php echo $text_adding; ?>"><?php echo $button_add_account; ?></button>
      </div>
    </div>
  </div>
</div>

<script>
function load_customer(customer_id) {
	$input_customer = $('#input-customer');

	$input_customer.prop('disabled', true);

	$('#button-confirm').prop('disabled', true);

	$('#customer-or').addClass('hidden');
	$('#form-group-new-customer').addClass('hidden');
	$('#button-reset').removeClass('hidden');

	// show refresh animations
	$('#panel-customer .fa-refresh').removeClass('hidden');
	$('#panel-instagram .fa-refresh').removeClass('hidden');
	//$('#panel-billing .fa-refresh').removeClass('hidden');
	$('#panel-payment .fa-refresh').removeClass('hidden');

	$.ajax({
		url: 'index.php?route=csr/checkout/customer&customer_id=' + customer_id,
		dataType: 'json',
		success: function(json) {
			if (json.redirect) {
				location = json.redirect;
			}

			$input_customer.val(json.firstname + ' ' + json.lastname);

			$('#input-customer_id').val(customer_id);
			$('#instagram-customer_id').val(customer_id);

			var new_width = ($input_customer.width() > 320 ? $input_customer.css('width') : '320px');

			if ($('*', $input_customer.parent()).not('#input-customer, #input-customer_id').length) {
				$('*', $input_customer.parent()).not('#input-customer, #input-customer_id').prop('disabled', true).fadeOut(200, function() {
				$(this).remove();

				if ($input_customer.css('width') != '100%') {
					$input_customer.animate({'width': new_width}, 200, 'swing', function() {
						$input_customer.css('font-weight', 'bold');
					});
					}
				});
			} else {
				if ($input_customer.css('width') != '100%') {
					$input_customer.animate({'width': new_width}, 200, 'swing', function() {
						$input_customer.css('font-weight', 'bold');
					});
				}
			}

			// customer information
			$('input[name="firstname"]').val(json.firstname);
			$('input[name="lastname"]').val(json.lastname);
			$('input[name="email"]').val(json.email);
			$('input[name="telephone"]').val(json.telephone);

			var instagram_account_data = [];

			for (key in json.instagram_accounts) {
				if (json.instagram_accounts.hasOwnProperty(key)) {
					var account = json.instagram_accounts[key];

					var html_data = [];

					html_data.push('<tr id="account_id-' + account.account_id + '">');
					html_data.push('  <td class="text-left">');

					if (account.image) {
						html_data.push('    <img src="' + account.image + '">');
					}

					html_data.push('    ' + account.username);
					html_data.push('    <input type="hidden" name="accounts[' + account.account_id + '][account_id]" value="' + account.account_id + '">');
					html_data.push('  <td class="text-left vertical-middle">' + account.time + '</td>');
					html_data.push('  <td class="text-left vertical-middle">');

					<?php if ($categories) { ?>
					html_data.push('    <select name="accounts[' + account.account_id + '][product_id]" class="form-control">');

					if (!account.recurring_product_id) {
						if (account.time == '<?php echo $text_expired; ?>') {
							html_data.push('      <option value="0" data-price="0.00" selected><?php echo $text_none; ?> <?php echo $text_current; ?></option>');
						} else {
							html_data.push('      <option value="0" data-price="0.00" selected><?php echo $text_free_trial; ?> <?php echo $text_current; ?></option>');
						}

						html_data.push('      <option value="" disabled></option>');
					}

					<?php foreach ($categories as $category) { ?>
					html_data.push('      <optgroup label="<?php echo $category['name']; ?>">');

					<?php foreach ($category['products'] as $product) { ?>
					if (account.recurring_product_id == <?php echo $product['product_id']; ?>) {
						html_data.push('        <option value="<?php echo $product['product_id']; ?>" data-price="0.00" selected><?php echo $product['name']; ?> <?php echo $text_current; ?></option>');
					} else {
						html_data.push('        <option value="<?php echo $product['product_id']; ?>" data-price="<?php echo $product['price']; ?>"><?php echo $product['name']; ?></option>');
					}
					<?php } ?>

					html_data.push('      </optgroup>');
					<?php } ?>

					html_data.push('    </select></td>');
					html_data.push('  <td class="text-left vertical-middle"><input type="text" name="accounts[' + account.account_id + '][price]" value="0.00" class="form-control price"></td>');
					<?php } else { ?>
					html_data.push('  </td>');
					html_data.push('  <td class="text-left vertical-middle"></td>');
					<?php } ?>

					html_data.push('</tr>');

					instagram_account_data.push(html_data.join(''));
				}
			}

			$('#instagram-accounts tbody').html(instagram_account_data.join(''));

			$('#instagram-accounts').DataTable({
				autoWidth:  false,
				info:       false,
				paging:     false,
				searching:  false,
				columnDefs: [{
					orderable:  false,
					targets:    [2, 3]
				}]
			});

			// add totals to tooltip
			var total_html_data = [], totals_html_data = [];

			for (var i = 0, total; total = json.totals[i]; ++i) {
				totals_html_data.push(total.title + ': ' + total.text);
			}

			if (totals_html_data.length) {
				total_html_data.push('<span id="order-totals-tooltip" data-toggle="tooltip" data-html="true" data-original-title="' + totals_html_data.join('<br>') + '"><i class="fa fa-question-circle"></i></span>');
			}

			total_html_data.push(json.total);

			$('#order-totals-tooltip').tooltip('destroy');

			$('#total').html(total_html_data.join(''));

			$('#payment-form').html(json.payment_html);
		},
		complete: function() {
			$('#button-confirm').prop('disabled', false);

			// show panel content
			$('.panel-body.hidden', '#panel-customer').removeClass('hidden');
			$('.panel-body.hidden, .panel-heading .hidden', '#panel-instagram').removeClass('hidden');
			$('.panel-body.hidden', '#panel-payment').removeClass('hidden');

			// hide refresh animations
			$('#panel-customer .fa-refresh').addClass('hidden');
			$('#panel-instagram .fa-refresh').addClass('hidden');
			$('#panel-payment .fa-refresh').addClass('hidden');
		}
	});
}

// Load all customer information when selected via autocomplete dropdown
$('#input-customer').autocomplete({
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=csr/checkout/autocomplete_customer&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label:       item.firstname + ' ' + item.lastname + ' (' + item.email + ')',
						value:       item.customer_id,
						customer_id: item.customer_id,
						firstname:   item.firstname,
						lastname:    item.lastname,
						email:       item.email
					}
				}));
			}
		});
	},
	select: function(item) {
		load_customer(item.customer_id);

		return false;
	},
	focus: function(event, ui) {
		return false;
	}
});

$(document).on('change', 'select[name$="[product_id]"], input[name$="[price]"], input[name="coupon"]', function() {
	var $this = $(this), $button = $('#button-confirm');

	// when changing products, update price
	if ($this.attr('name').indexOf('[product_id]') > -1) {
		$('input[name$="[price]"]', $this.closest('tr')).val($('option:selected', $this).data('price'));
	}

	$.ajax({
		url: 'index.php?route=csr/checkout/cart',
		type: 'post',
		data: $('input[name][type="text"], input[name][type="checkbox"]:checked, input[name][type="hidden"], select[name]', '#content'),
		dataType: 'json',
		beforeSend: function() {
			$button.prop('disabled', true);
			$('#panel-instagram .pull-right > *').addClass('hidden');
			$('#panel-instagram .fa-refresh').removeClass('hidden');
		},
		complete: function() {
			$button.prop('disabled', false);
			$('.panel-heading .hidden', '#panel-instagram').removeClass('hidden');
			$('#panel-instagram .fa-refresh').addClass('hidden');
		},
		success: function(json) {
			if (json.redirect) {
				location = json.redirect;
			}

			// Account Totals
			if (json.account_totals != '') {
				for (var i = 0, account_total; account_total = json.account_totals[i]; ++i) {
					$('#total-' + account_total.account_id).html(account_total.total);
				}
			}

			// add totals to tooltip
			var total_html_data = [], totals_html_data = [];

			for (var i = 0, total; total = json.totals[i]; ++i) {
				totals_html_data.push(total.title + ': ' + total.text);
			}

			if (totals_html_data.length) {
				total_html_data.push('<span id="order-totals-tooltip" data-toggle="tooltip" data-html="true" data-original-title="' + totals_html_data.join('<br>') + '"><i class="fa fa-question-circle"></i></span>');
			}

			total_html_data.push(json.total);

			$('#order-totals-tooltip').tooltip('destroy');

			$('#total').html(total_html_data.join(''));
		}
	});
});

$(':input', '#payment-method').on('keydown', function(e) {
	if (e.keyCode == 13) {
		$('#button-confirm').trigger('click');
	}
});

$('#button-confirm').on('click', function() {
	$button = $(this), $payment = $('#payment');

	$payment.attr('action', $payment.data('confirm'));

	$.ajax({
		url: 'index.php?route=csr/checkout/validate',
		type: 'post',
		data: $('input[name][type="text"], input[name][type="checkbox"]:checked, input[name][type="hidden"], select[name]', '#content'),
		dataType: 'json',
		cache: false,
		beforeSend: function() {
			$button.button('loading');
		},
		success: function(json) {
			if (json['redirect']) {
				location = json['redirect'];
			}

			$('.text-danger').remove();

			if (json['success']) {
				$('input[type="submit"]', $payment).trigger('click');
			} else {
				if (json['error']) {
					if (json['error']['firstname']) {
						$('input[name="firstname"]').after('<div class="text-danger">' + json['error']['firstname'] + '</div>');
					}

					if (json['error']['lastname']) {
						$('input[name="lastname"]').after('<div class="text-danger">' + json['error']['lastname'] + '</div>');
					}

					if (json['error']['email']) {
						$('input[name="email"]').after('<div class="text-danger">' + json['error']['email'] + '</div>');
					}

					if (json['error']['empty']) {
						alert(json['error']['empty'].replace('&amp;', '&'));
					}
				}

				$button.button('reset');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			$button.button('reset');
		}
	});

	return false;
});

function checkoutResponse(json) {
	if (json['success']) {
		location = $('#button-confirm').data('success');
	} else {
		if (json['error']) {
			alert(json['error']);
		}

		$('#button-confirm').button('reset');
	}
}

///////////////
// NEW CUSTOMER
//
$('#customer_firstname, #customer_lastname, #customer_email, #customer_password').on('keyup', function() {
	if ($('#customer_firstname').val() && $('#customer_lastname').val() && $('#customer_email').val() && $('#customer_password').val()) {
		$('#button-new-customer').removeClass('disabled');
	} else {
		$('#button-new-customer').addClass('disabled');
	}
});

$('#button-new-customer').on('click', function() {
	$('#form-new-customer').submit();
});

$('#form-new-customer').on('submit', function() {
	var $this = $(this);

	$.ajax({
		url: $this.attr('action'),
		type: 'post',
		data: $this.serialize(),
		dataType: 'json',
		beforeSend: function() {
			$('#button-new-customer').button('loading');
			$('.btn', '#modal-new-customer').addClass('disabled');
			$('.alert-danger', '#modal-new-customer').remove();
		},
		complete: function() {
			$('#modal-new-customer').modal('hide');
			$('#button-new-customer').button('reset');
			$('.btn', '#modal-new-customer').removeClass('disabled');
		},
		success: function(json) {
			if (json.errors) {
				var errors = [];

				for (var key in json.errors) {
					if (json.errors.hasOwnProperty(key)) {
						var error = json.errors[key];

						errors.push(error);
					}
				}

				$this.before('<div class="alert alert-danger" role="alert">' + errors.join('<br>') + '</div>');
			}

			if (json.customer_id) {
				load_customer(json.customer_id);
			}
		}
	});

	return false;
});

////////////
// INSTAGRAM
//
$('#instagram_username, #instagram_password').on('keyup', function() {
	if ($('#instagram_username').val() && $('#instagram_password').val()) {
		$('#button-add-instagram').removeClass('disabled');
	} else {
		$('#button-add-instagram').addClass('disabled');
	}
});

$('#button-add-instagram').on('click', function() {
	$('#form-add-instagram').submit();
});

$('#form-add-instagram').on('submit', function() {
	var $this = $(this);

	$.ajax({
		url: $this.attr('action'),
		type: 'post',
		data: $this.serialize(),
		dataType: 'json',
		beforeSend: function() {
			$('#button-add-instagram').button('loading');
			$('.btn', '#modal-add-instagram').addClass('disabled');
			$('.alert-danger', '#modal-add-instagram').remove();
		},
		complete: function() {
			$('#button-add-instagram').button('reset');
			$('.btn', '#modal-add-instagram').removeClass('disabled');
		},
		success: function(json) {
			if (json.errors) {
				var errors = [];

				for (var key in json.errors) {
					if (json.errors.hasOwnProperty(key)) {
						var error = json.errors[key];

						errors.push(error);
					}
				}

				$this.before('<div class="alert alert-danger" role="alert">' + errors.join('<br>') + '</div>');
			}

			if (json.data) {
				var row = ['', '', '', '', ''];

				row[0] += '<img src="' + json.data.image + '">';
				row[0] += '    ' + json.data.username + '    ';
				row[0] += '<input type="hidden" name="accounts[' + json.data.account_id + '][account_id]" value="' + json.data.account_id + '">';

				row[1] += json.data.time;

				<?php if ($categories) { ?>
				row[2] += '<select name="accounts[' + json.data.account_id + '][product_id]" class="form-control">';
				row[2] += '<option value="0" data-price="0.00" selected><?php echo $text_free_trial; ?></option>';
				row[2] += '<option value="" disabled></option>';

				<?php foreach ($categories as $category) { ?>
				row[2] += '<optgroup label="<?php echo $category['name']; ?>">';
				<?php foreach ($category['products'] as $product) { ?>
				row[2] += '<option value="<?php echo $product['product_id']; ?>" data-price="<?php echo $product['price']; ?>"><?php echo $product['name']; ?></option>';
				<?php } ?>
				row[2] += '</optgroup>';
				<?php } ?>

				row[2] += '</select>';

				row[3] += '<input type="text" name="accounts[' + json.data.account_id + '][price]" value="<?php echo $categories[0]['products'][0]['price']; ?>" class="form-control price">';
				<?php } ?>

				$('#instagram-accounts').DataTable().row.add(row).draw();

				$('#modal-add-instagram').modal('hide');
			}
		}
	});

	return false;
});
</script>
<?php echo $footer; ?>