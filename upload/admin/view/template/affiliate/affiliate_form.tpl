<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-affiliate" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-affiliate" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-payment" data-toggle="tab"><?php echo $tab_payment; ?></a></li>
            <li><a href="#tab-managed-billing" data-toggle="tab"><?php echo $tab_managed_billing; ?></a></li>
            <?php if ($affiliate_id) { ?>
            <li><a href="#tab-transaction" data-toggle="tab"><?php echo $tab_transaction; ?></a></li>
            <?php } ?>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <fieldset>
                <legend><?php echo $text_affiliate_detail; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-path"><?php echo $entry_parent; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="path" value="<?php echo $path; ?>" placeholder="<?php echo $entry_parent; ?>" id="input-path" class="form-control">
                    <input type="hidden" name="parent_id" value="<?php echo $parent_id; ?>">
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-firstname"><?php echo $entry_firstname; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="firstname" value="<?php echo $firstname; ?>" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname" class="form-control">
                    <?php if ($error_firstname) { ?>
                    <div class="text-danger"><?php echo $error_firstname; ?></div>
                    <?php } ?>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-lastname"><?php echo $entry_lastname; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="lastname" value="<?php echo $lastname; ?>" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname" class="form-control">
                    <?php if ($error_lastname) { ?>
                    <div class="text-danger"><?php echo $error_lastname; ?></div>
                    <?php } ?>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control">
                    <?php if ($error_email) { ?>
                    <div class="text-danger"><?php echo $error_email; ?></div>
                    <?php  } ?>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="telephone" value="<?php echo $telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control">
                    <?php if ($error_telephone) { ?>
                    <div class="text-danger"><?php echo $error_telephone; ?></div>
                    <?php  } ?>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-code"><span data-toggle="tooltip" title="<?php echo $help_code; ?>"><?php echo $entry_code; ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="code" value="<?php echo $code; ?>" placeholder="<?php echo $entry_code; ?>" id="input-code" class="form-control">
                    <?php if ($error_code) { ?>
                    <div class="text-danger"><?php echo $error_code; ?></div>
                    <?php } ?>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-password"><?php echo $entry_password; ?></label>
                  <div class="col-sm-10">
                    <input type="password" name="password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>" autocomplete="off" id="input-password" class="form-control" >
                    <?php if ($error_password) { ?>
                    <div class="text-danger"><?php echo $error_password; ?></div>
                    <?php  } ?>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-confirm"><?php echo $entry_confirm; ?></label>
                  <div class="col-sm-10">
                    <input type="password" name="confirm" value="<?php echo $confirm; ?>" placeholder="<?php echo $entry_confirm; ?>" autocomplete="off" id="input-confirm" class="form-control">
                    <?php if ($error_confirm) { ?>
                    <div class="text-danger"><?php echo $error_confirm; ?></div>
                    <?php  } ?>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                  <div class="col-sm-10">
                    <select name="status" id="input-status" class="form-control">
                      <?php if ($status) { ?>
                      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                      <option value="0"><?php echo $text_disabled; ?></option>
                      <?php } else { ?>
                      <option value="1"><?php echo $text_enabled; ?></option>
                      <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </fieldset>
              <fieldset>
                <legend><?php echo $text_affiliate_address; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-company"><?php echo $entry_company; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="company" value="<?php echo $company; ?>" placeholder="<?php echo $entry_company; ?>" id="input-company" class="form-control">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-website"><?php echo $entry_website; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="website" value="<?php echo $website; ?>" placeholder="<?php echo $entry_website; ?>" id="input-website" class="form-control">
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-address-1"><?php echo $entry_address_1; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="address_1" value="<?php echo $address_1; ?>" placeholder="<?php echo $entry_address_1; ?>" id="input-address-1" class="form-control">
                    <?php if ($error_address_1) { ?>
                    <div class="text-danger"><?php echo $error_address_1; ?></div>
                    <?php  } ?>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-address-2"><?php echo $entry_address_2; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="address_2" value="<?php echo $address_2; ?>" placeholder="<?php echo $entry_address_2; ?>" id="input-address-2" class="form-control">
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-city"><?php echo $entry_city; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="city" value="<?php echo $city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control">
                    <?php if ($error_city) { ?>
                    <div class="text-danger"><?php echo $error_city ?></div>
                    <?php  } ?>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-postcode"><?php echo $entry_postcode; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="postcode" value="<?php echo $postcode; ?>" placeholder="<?php echo $entry_postcode; ?>" id="input-postcode" class="form-control">
                    <?php if ($error_postcode) { ?>
                    <div class="text-danger"><?php echo $error_postcode ?></div>
                    <?php  } ?>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-country"><?php echo $entry_country; ?></label>
                  <div class="col-sm-10">
                    <select name="country_id" id="input-country" class="form-control">
                      <option value=""><?php echo $text_select; ?></option>
                      <?php foreach ($countries as $country) { ?>
                      <?php if ($country['country_id'] == $country_id) { ?>
                      <option value="<?php echo $country['country_id']; ?>" selected="selected"> <?php echo $country['name']; ?> </option>
                      <?php } else { ?>
                      <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select>
                    <?php if ($error_country) { ?>
                    <div class="text-danger"><?php echo $error_country; ?></div>
                    <?php } ?>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-zone"><?php echo $entry_zone; ?></label>
                  <div class="col-sm-10">
                    <select name="zone_id" id="input-zone" class="form-control">
                    </select>
                    <?php if ($error_zone) { ?>
                    <div class="text-danger"><?php echo $error_zone; ?></div>
                    <?php } ?>
                  </div>
                </div>
              </fieldset>
            </div>
            <div class="tab-pane" id="tab-payment">
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-affiliate_group_id"><?php echo $entry_affiliate_group; ?></label>
                <div class="col-sm-10">
                  <select name="affiliate_group_id" id="input-affiliate_group_id" class="form-control">
                    <option value=""><?php echo $text_select; ?></option>
                    <?php foreach ($affiliate_groups as $affiliate_group) { ?>
                    <?php if ($affiliate_group['affiliate_group_id'] == $affiliate_group_id) { ?>
                    <option value="<?php echo $affiliate_group['affiliate_group_id']; ?>" selected="selected"> <?php echo $affiliate_group['name']; ?> </option>
                    <?php } else { ?>
                    <option value="<?php echo $affiliate_group['affiliate_group_id']; ?>"><?php echo $affiliate_group['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <?php if ($error_affiliate_group) { ?>
                  <div class="text-danger"><?php echo $error_affiliate_group; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-account_fee"><span data-toggle="tooltip" title="<?php echo $help_account_fee; ?>"><?php echo $entry_account_fee; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="account_fee" value="<?php echo $account_fee; ?>" placeholder="<?php echo $entry_account_fee; ?>" id="input-account_fee" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-tax"><?php echo $entry_tax; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="tax" value="<?php echo $tax; ?>" placeholder="<?php echo $entry_tax; ?>" id="input-tax" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_payment; ?></label>
                <div class="col-sm-10">
                  <div class="radio">
                    <label>
                      <?php if ($payment == 'check') { ?>
                      <input type="radio" name="payment" value="check" checked="checked">
                      <?php } else { ?>
                      <input type="radio" name="payment" value="check">
                      <?php } ?>
                      <?php echo $text_check; ?></label>
                  </div>
                  <div class="radio">
                    <label>
                      <?php if ($payment == 'paypal') { ?>
                      <input type="radio" name="payment" value="paypal" checked="checked">
                      <?php } else { ?>
                      <input type="radio" name="payment" value="paypal">
                      <?php } ?>
                      <?php echo $text_paypal; ?></label>
                  </div>
                  <div class="radio">
                    <label>
                      <?php if ($payment == 'bank') { ?>
                      <input type="radio" name="payment" value="bank" checked="checked">
                      <?php } else { ?>
                      <input type="radio" name="payment" value="bank">
                      <?php } ?>
                      <?php echo $text_bank; ?></label>
                  </div>
                </div>
              </div>
              <div id="payment-check" class="payment">
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-check"><?php echo $entry_check; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="payment_data[check]" value="<?php echo (isset($payment_data['check']) ? $payment_data['check'] : ''); ?>" placeholder="<?php echo $entry_check; ?>" id="input-check" class="form-control">
                    <?php if ($error_check) { ?>
                    <div class="text-danger"><?php echo $error_check; ?></div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div id="payment-paypal" class="payment">
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-paypal"><?php echo $entry_paypal; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="payment_data[paypal]" value="<?php echo (isset($payment_data['paypal']) ? $payment_data['paypal'] : ''); ?>" placeholder="<?php echo $entry_paypal; ?>" id="input-paypal" class="form-control">
                    <?php if ($error_paypal) { ?>
                    <div class="text-danger"><?php echo $error_paypal; ?></div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div id="payment-bank" class="payment">
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-bank-name"><?php echo $entry_bank_name; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="payment_data[bank_name]" value="<?php echo (isset($payment_data['bank_name']) ? $payment_data['bank_name'] : ''); ?>" placeholder="<?php echo $entry_bank_name; ?>" id="input-bank-name" class="form-control">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-bank-branch-number"><?php echo $entry_bank_branch_number; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="payment_data[bank_branch_number]" value="<?php echo (isset($payment_data['bank_branch_number']) ? $payment_data['bank_branch_number'] : ''); ?>" placeholder="<?php echo $entry_bank_branch_number; ?>" id="input-bank-branch-number" class="form-control">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-bank-swift-code"><?php echo $entry_bank_swift_code; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="payment_data[bank_swift_code]" value="<?php echo (isset($payment_data['bank_swift_code']) ? $payment_data['bank_swift_code'] : ''); ?>" placeholder="<?php echo $entry_bank_swift_code; ?>" id="input-bank-swift-code" class="form-control">
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-bank-account-name"><?php echo $entry_bank_account_name; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="payment_data[bank_account_name]" value="<?php echo (isset($payment_data['bank_account_name']) ? $payment_data['bank_account_name'] : ''); ?>" placeholder="<?php echo $entry_bank_account_name; ?>" id="input-bank-account-name" class="form-control">
                    <?php if ($error_bank_account_name) { ?>
                    <div class="text-danger"><?php echo $error_bank_account_name; ?></div>
                    <?php } ?>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-bank-account-number"><?php echo $entry_bank_account_number; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="payment_data[bank_account_number]" value="<?php echo (isset($payment_data['bank_account_number']) ? $payment_data['bank_account_number'] : ''); ?>" placeholder="<?php echo $entry_bank_account_number; ?>" id="input-bank-account-number" class="form-control">
                    <?php if ($error_bank_account_number) { ?>
                    <div class="text-danger"><?php echo $error_bank_account_number; ?></div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-managed-billing">
                <fieldset>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-braintree_customer_id"><?php echo $entry_braintree_customer_id; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="braintree_customer_id" value="<?php echo $braintree_customer_id; ?>" placeholder="<?php echo $entry_braintree_customer_id; ?>" id="input-braintree_customer_id" class="form-control">
                    </div>
                  </div>
                </fieldset>
            </div>
            <?php if ($affiliate_id) { ?>
            <div class="tab-pane" id="tab-transaction">
              <div id="transaction"></div>
              <br>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-description"><?php echo $entry_description; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="description" value="" placeholder="<?php echo $entry_description; ?>" id="input-description" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-amount"><?php echo $entry_amount; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="amount" value="" placeholder="<?php echo $entry_amount; ?>" id="input-amount" class="form-control">
                </div>
              </div>
              <div class="text-right">
                <button type="button" id="button-transaction" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $button_transaction_add; ?></button>
              </div>
            </div>
            <?php } ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Path / Parent
$('#input-path').autocomplete({
	source: function(request, response) {
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
	select: function(item) {
		$('input[name="path"]').val(item['label']);
		$('input[name="parent_id"]').val(item['value']);
	}
});

$('#input-path').on('keyup', function() {
	if ($(this).val() === '') {
		$('input[name="parent_id"]').val(0);
	}
});

// Country
$('select[name="country_id"]').on('change', function() {
	$.ajax({
		url: 'index.php?route=localisation/country/country&token=<?php echo $token; ?>&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name="country_id"]').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
		},
		complete: function() {
			$('.fa-spin').remove();
		},
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('input[name="postcode"]').parent().parent().addClass('required');
			} else {
				$('input[name="postcode"]').parent().parent().removeClass('required');
			}

			html = '<option value=""><?php echo $text_select; ?></option>';

			if (json['zone'] && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
					html += '<option value="' + json['zone'][i]['zone_id'] + '"';
					
					if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
						html += ' selected="selected"';
					}

					html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}

			$('select[name="zone_id"]').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name="country_id"]').trigger('change');

// Payment
$('input[name="payment"]').on('change', function() {
	$('.payment').hide();

	$('#payment-' + this.value).show();
});

$('input[name="payment"]:checked').trigger('change');

// Transactions
$('#transaction').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#transaction').load(this.href);
});

$('#transaction').load('index.php?route=affiliate/affiliate/transaction&token=<?php echo $token; ?>&affiliate_id=<?php echo $affiliate_id; ?>');

$('#button-transaction').on('click', function() {
	$.ajax({
		url: 'index.php?route=affiliate/affiliate/addtransaction&token=<?php echo $token; ?>&affiliate_id=<?php echo $affiliate_id; ?>',
		type: 'post',
		dataType: 'json',
		data: 'description=' + encodeURIComponent($('#tab-transaction input[name="description"]').val()) + '&amount=' + encodeURIComponent($('#tab-transaction input[name="amount"]').val()),
		beforeSend: function() {
			$('#button-transaction').button('loading');
		},
		complete: function() {
			$('#button-transaction').button('reset');
		},
		success: function(json) {
			$('.alert').remove();

			if (json['error']) {
				 $('#tab-transaction').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div></div>');
			}

			if (json['success']) {
				$('#tab-transaction').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div></div>');

				$('#transaction').load('index.php?route=affiliate/affiliate/transaction&token=<?php echo $token; ?>&affiliate_id=<?php echo $affiliate_id; ?>');

				$('#tab-transaction input[name="amount"]').val('');
				$('#tab-transaction input[name="description"]').val('');			
			}
		}
	});
});
</script>

<?php echo $footer; ?>