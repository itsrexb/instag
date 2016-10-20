<style>
input[type=date].form-control {
	line-height: normal;
}
#cardholder-birthdate:before {
	content: "<?php echo $entry_cardholder_birthdate; ?>";
}
</style>
<form id="payment" action="" data-confirm="<?php echo $confirm; ?>" data-update="<?php echo $update; ?>" method="post" class="moip-payment">
  <div class="existing-payment-methods <?php echo (!$payment_methods ? 'hidden' : ''); ?>">
  <!-- Desktop Dropdown -->
  <div class="dropdown-select dropdown">
    <button class="btn btn-default dropdown-toggle"
        id="payment-methods-dropdown"
        type="button"
        data-value="<?php echo ($active_payment_method ? $active_payment_method['id'] : ''); ?>"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <?php if ($active_payment_method) { ?>
        <div class="payment-method-image <?php echo strtolower($active_payment_method['label']); ?>"></div>
        <?php if (strlen($active_payment_method['description']) > 14) { ?>
        <p><?php echo substr($active_payment_method['description'], 0, 14); ?>...</p>
        <?php } else { ?>
        <p><?php echo $active_payment_method['description']; ?></p>
        <?php } ?>
        <span class="pull-right"><?php echo $text_change_payment; ?></span>
        <?php } ?>
    </button>
    <ul class="dropdown-menu" aria-labelledby="payment-methods-dropdown">
    <?php foreach ($payment_methods as $payment_method) { ?>
      <?php if (strlen($payment_method['description']) > 14) { ?>
      <li data-value="<?php echo $payment_method['id']; ?>"
        ng-controller="TooltipsController as tooltip"
        ng-mouseover="tooltip.displayTooltip($event)"
                ng-mouseleave="tooltip.hideTooltip($event)"
        data-tooltip="<?php echo $payment_method['description']; ?>"
        data-tooltip-position="top"
        data-tooltip-style="dark"
        data-tooltip-offset="0"
        data-hide-fire="mouseleave">
        <div class="payment-method-image <?php echo strtolower($payment_method['label']); ?>"></div>
        <p><?php echo substr($payment_method['description'], 0, 14); ?>...</p>
        <span class="pull-right"><?php echo $text_change_payment; ?></span>
      </li>
    <?php } else { ?>
      <li data-value="<?php echo $payment_method['id']; ?>">
        <div class="payment-method-image <?php echo strtolower($payment_method['label']); ?>"></div>
        <p><?php echo $payment_method['description']; ?></p>
        <span class="pull-right"><?php echo $text_change_payment; ?></span>
      </li>
    <?php } ?>
    <?php } ?>
      <li data-value="" id="add-payment-method">
        <p><?php echo $text_add; ?></p>
      </li>
    </ul>
  </div>
  <!-- Mobile Select -->
  <select id="payment-method-id" name="payment_method_id" autocomplete="off">
    <?php foreach ($payment_methods as $payment_method) { ?>
    <?php if ($payment_method['id'] == $active_payment_method['id']) { ?>
    <option value="<?php echo $payment_method['id']; ?>" selected="selected"><?php echo $payment_method['label']; ?> - <?php echo $payment_method['description']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $payment_method['id']; ?>"><?php echo $payment_method['label']; ?> - <?php echo $payment_method['description']; ?></option>
    <?php } ?>
    <?php } ?>
    <option value=""><?php echo $text_add; ?></option>
  </select>
  </div>
  <div class="new-payment-method <?php echo ($payment_methods ? 'hidden' : ''); ?>">
    <div class="credit-card-info">
      <div id="cardholder-name-container" class="form-group">
        <input id="cardholder-name" type="text" class="form-control cardholder-name" placeholder="<?php echo $entry_cardholder_name; ?>" autocomplete="cc-name" required>
        <div class="help-icon"></div>
      </div>

      <div id="card-number-container" class="form-group">
        <input id="card-number" type="tel" class="form-control cc-number" placeholder="<?php echo $entry_card_number; ?>" autocomplete="cc-number" required>
        <div class="help-icon"></div>
      </div>

      <div id="card-expiration-date-container" class="form-group col-sm-6">
        <input id="card-expiration-date" type="tel" class="form-control cc-exp" placeholder="<?php echo $entry_expiration_date; ?>" autocomplete="cc-exp" required>
      </div>

      <div id="card-cvv-container" class="form-group col-sm-6">
        <input id="card-cvv" type="tel" class="form-control cc-cvc" placeholder="<?php echo $entry_cvv; ?>" autocomplete="cc-csc" required>
        <div class="help-icon"></div>
      </div>

      <div id="cardholder-cpf-container" class="form-group">
        <input id="cardholder-cpf" name="cardholder-cpf" type="tel" class="form-control cardholder-cpf" placeholder="<?php echo $entry_cardholder_cpf; ?>" maxlength="14" data-validation-method="cpf" required>
        <div class="help-icon"></div>
      </div>

      <div id="cardholder-birthdate-container" class="form-group">
        <input id="cardholder-birthdate" type="date" class="form-control cardholder-birthdate" placeholder="<?php echo $entry_cardholder_birthdate; ?>" data-validation-method="birthdate" required>
        <div class="help-icon"></div>
      </div>
    </div>
  </div>

  <div class="hidden"><input type="submit"></div>
  <input id="public-key" type="hidden" value="<?php echo $public_key; ?>">
</form>

<script>
jQuery(function($) {
	$('#card-number').payment('formatCardNumber');
	$('#card-expiration-date').payment('formatCardExpiry');
	$('#card-cvv').payment('formatCardCVC');

	$('#cardholder-cpf').mask('000.000.000-00');

	$.fn.toggleInputError = function(erred) {
		this.parent('.form-group').toggleClass('has-error', erred);
		return this;
	};

	$('input', '#payment').on('focus', function() {
		$(this).parent().addClass('focused');
	});

	$('input', '#payment').on('blur', function() {
		$(this).parent().removeClass('focused');
	});

	$('#card-number').on('keyup paste', function() {
		var $this                  = $(this),
				$card_number_container = $('#card-number-container'),
				$card_cvv_container    = $('#card-cvv-container'),
				card_type              = $.payment.cardType($this.val());

		$card_number_container.removeClass('visa mastercard amex dinersclub discover unionpay jcb visaelectron maestro forbrugsforeningen dankort elo');
		$card_cvv_container.removeClass('visa mastercard amex dinersclub discover unionpay jcb visaelectron maestro forbrugsforeningen dankort elo');

		if (card_type) {
			$card_number_container.addClass(card_type);
			$card_cvv_container.addClass(card_type);
		}
	});

	$('#card-number').on('blur', function() {
		var $this = $(this);

		if ($this.hasClass('identified')) {
			$this.toggleInputError(!$.payment.validateCardNumber($this.val()));
		} else {
			$this.toggleInputError(false);
			$('#card-expiration-date').toggleInputError(false);
			$('#card-cvv').toggleInputError(false);
		}
	});

	$('#card-expiration-date').on('blur', function() {
		var $this = $(this);

		if ($('#card-number').hasClass('identified')) {
			$this.toggleInputError(!$.payment.validateCardExpiry($.payment.cardExpiryVal($this.val())));
		} else {
			$this.toggleInputError(false);
		}
	});

	$('#card-cvv').on('blur', function() {
		var $this     = $(this),
				card_type = $.payment.cardType($('#card-number').val());

		if ($('#card-number').hasClass('identified')) {
			$this.toggleInputError(!$.payment.validateCardCVC($this.val(), card_type));
		} else {
			$this.toggleInputError(false);
		}
	});

	$('#cardholder-name').on('blur', function() {
		var $this = $(this);

		$this.toggleInputError(!$this.validate());
	});

	$('#cardholder-cpf').on('blur', function() {
		var $this = $(this);

		$this.toggleInputError(!$this.validate());
	});

	$('#cardholder-birthdate').on('blur', function() {
		var $this = $(this);

		if ($this.val()) {
			$this.addClass('not-empty');
		} else {
			$this.removeClass('not-empty');
		}

		$this.toggleInputError(!$this.validate());
	});

	$('.existing-payment-methods .dropdown-menu', '#payment').on('click', 'li', function() {
		$('#payment-method-id').val($(this).data('value')).trigger('change');
	});

	// show/hide the new payment method form
	$('#payment-method-id').on('change', function() {
		var $this = $(this), $payment_methods_dropdown = $('#payment-methods-dropdown');

		if ($this.val()) {
			$('.new-payment-method', '#payment').addClass('hidden');
			$payment_methods_dropdown.removeClass('new-payment');
		} else {
			$('.new-payment-method', '#payment').removeClass('hidden');
			$payment_methods_dropdown.addClass('new-payment');
		}

		var $payment_method = $('li[data-value="' + $this.val() + '"]', '#payment .existing-payment-methods .dropdown-menu');

		$payment_methods_dropdown.html($payment_method.html()).attr('data-value', $payment_method.attr('data-value'));
	});

	$('input[type="submit"]', '#payment').on('click', function() {
		if ($('#payment-method-id').val()) {
			moip_action({
				payment_method_id: $('#payment-method-id').val()
			});
		} else {
			var card_number     = $('#card-number').val(),
					card_exp_date   = $.payment.cardExpiryVal($('#card-expiration-date').val()),
					card_cvv        = $('#card-cvv').val(),
					card_type       = $.payment.cardType(card_number),
					public_key      = $('#public-key').val(),
					valid_name      = $('#cardholder-name').validate(),
					valid_cpf       = $('#cardholder-cpf').validate(),
					valid_birthdate = $('#cardholder-birthdate').validate();

			var cc = new Moip.CreditCard({
				number   : card_number,
				cvc      : card_cvv,
				expMonth : card_exp_date.month,
				expYear  : card_exp_date.year,
				pubKey   : public_key
			});

			if(cc.isValid() && valid_name && valid_cpf && valid_birthdate) {
				moip_action({
					cardholder: {
						name      : $('#cardholder-name').val(),
						cpf       : $('#cardholder-cpf').val(),
						birthdate : $('#cardholder-birthdate').val()
					},
					cc: {
						number    : cc.number,
						exp_month : cc.expMonth,
						exp_year  : cc.expYear,
						cvv       : cc.cvc
					}
				});
			} else {
				$('#cardholder-name').toggleInputError(!valid_name);
				$('#card-number').toggleInputError(!$.payment.validateCardNumber(card_number));
				$('#card-expiration-date').toggleInputError(!$.payment.validateCardExpiry(card_exp_date));
				$('#card-cvv').toggleInputError(!$.payment.validateCardCVC(card_cvv, card_type));
				$('#cardholder-cpf').toggleInputError(!valid_cpf);
				$('#cardholder-birthdate').toggleInputError(!valid_birthdate);

				$('#button-confirm').button('reset');
				$('#button-update').button('reset');
			}
		}

		return false;
	});

	function moip_action(obj) {
		var form_action = $('#payment').attr('action');

		if (form_action) {
			$.ajax({
				url: form_action,
				type: 'post',
				data: obj,
				dataType: 'json',
				success: function(json) {
					// update payment methods
					if (json.success) {
						if (json.payment_methods) {
							// update actual select dropdown
							var html_data = [];

							for (var i = 0, payment_method; payment_method = json.payment_methods[i]; ++i) {
								if (payment_method.id == json.payment_method_id) {
									html_data.push('<option value="' + payment_method.id + '" selected="selected">' + payment_method.label + ' - ' + payment_method.description + '</option>');
								} else {
									html_data.push('<option value="' + payment_method.id + '">' + payment_method.label + ' - ' + payment_method.description + '</option>');
								}
							}

							html_data.push('<option value=""><?php echo $text_add; ?></option>');

							$('#payment-method-id').html(html_data.join(''));

							// update pretty dropdown
							html_data = [];

							for (var i = 0, payment_method; payment_method = json.payment_methods[i]; ++i) {
								if (payment_method.description.length > 14) {
									html_data.push('<li data-value="' + payment_method.id + '" ng-controller="TooltipsController as tooltip" ng-mouseover="tooltip.displayTooltip($event)" ng-mouseleave="tooltip.hideTooltip($event)" data-tooltip="' + payment_method.description + '" data-tooltip-position="top" data-tooltip-style="dark" data-tooltip-offset="0" data-hide-fire="mouseleave">');
									html_data.push('  <div class="payment-method-image ' + payment_method.label.toLowerCase() + '"></div>');
									html_data.push('  <p>' + payment_method.description.substring(0, 14) + '...</p>');
									html_data.push('  <span class="pull-right"><?php echo $text_change_payment; ?></span>');
									html_data.push('</li>');
								} else {
									html_data.push('<li data-value="' + payment_method.id + '">');
									html_data.push('  <div class="payment-method-image ' + payment_method.label.toLowerCase() + '"></div>');
									html_data.push('  <p>' + payment_method.description + '</p>');
									html_data.push('  <span class="pull-right"><?php echo $text_change_payment; ?></span>');
									html_data.push('</li>');
								}
							}

							html_data.push('<li data-value="" id="add-payment-method">');
							html_data.push('  <p><?php echo $text_add; ?></p>');
							html_data.push('</li>');

							$('.existing-payment-methods .dropdown-menu', '#payment').html(html_data.join(''));

							// show existing payment methods and hide new payment method
							$('.existing-payment-methods', '#payment').removeClass('hidden');
							$('.new-payment-method', '#payment').addClass('hidden');
						}

						$('#payment-method-id').trigger('change');

						$('input', '#payment').not('[type="hidden"]').val('');
						$('#card-number-container').removeClass('visa mastercard amex dinersclub discover unionpay jcb visaelectron maestro forbrugsforeningen dankort elo');
						$('#card-number').removeClass('visa mastercard amex dinersclub discover unionpay jcb visaelectron maestro forbrugsforeningen dankort elo identified');
						$('#card-cvv-container').removeClass('visa mastercard amex dinersclub discover unionpay jcb visaelectron maestro forbrugsforeningen dankort elo');
						$('#cardholder-birthdate').removeClass('not-empty');
					}

					$('.new-payment-method .credit-card-info', '#payment').removeClass('hidden');

					if (form_action == $('#payment').data('confirm')) {
						// changing plans
						if (typeof checkoutResponse == 'function') {
							checkoutResponse(json);
						}
					} else {
						// updating payment information
						if (typeof updatePaymentMethodResponse == 'function') {
							updatePaymentMethodResponse(json);
						}
					}

					// reset payment form action
					$('#payment').attr('action', '');
				}
			});
		} else {
			$('#button-confirm').button('reset');
			$('#button-update').button('reset');
		}
	}
});
</script>