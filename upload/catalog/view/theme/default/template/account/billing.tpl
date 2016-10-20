<div class="row">
  <div id="billing-notifications"></div>

  <div class="card" ng-controller="TabsController as tabs">
    <div class="collapse">
      <div  class="collapse-header has-speed" ng-click="tabs.uncollapseBilling(true)"
            data-selected-product="<?php echo $recurring_product_id; ?>">
        <div  id="speed-selected-<?php echo $recurring_product_id; ?>" class="selected-speed-container">
          <div class="icon-container">
            <i class="fa fa-bolt"></i>
          </div>
          <div class="change-text"><?php echo $text_change; ?></div>
          <h5><?php echo $text_speed; ?></h5>
          <span><?php echo $current_speed; ?></span>
          <p><?php echo $speed_card_header; ?></p>
        </div> 
      </div>
      <div id="speeds">
        <?php foreach($categories as $category) { ?>
        <div  class=" collapse-item speed-item category-<?php echo $category['category_id']; ?>
                      <?php if ($category['description']) { ?>
                      has-description
                      <?php } ?>"
              data-category-id="<?php echo $category['category_id']; ?>"
              ng-click="tabs.updateSpeed($event)">
          <div class="icon-container">
            <i class="fa fa-speed-<?php echo $category['category_id']; ?>"></i>
          </div>
          <h5><?php echo $category['name']; ?></h5>
          <div class="description">
            <p class="pull-left">
              <span>
                <?php echo $category['description']; ?>
              </span>
            </p>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
  <div class="card" ng-controller="TabsController as tabs">
    <div class="collapse">
      <div class="collapse-header has-billing" ng-click="tabs.uncollapseBilling(true)"
            data-selected-product="<?php echo $recurring_product_id; ?>">
        <div id="billing-selected-<?php echo $recurring_product_id; ?>" class="selected-billing-container">
          <div class="icon-container">
            <i class="fa fa-recycle"></i>
          </div>
          <div class="change-text"><?php echo $text_change; ?></div>
          <h5><?php echo $text_billing; ?></h5>
          <span><?php echo $current_billing; ?></span>
          <p><?php echo $billing_card_header; ?></p>
        </div> 
      </div>
      <div id="products">
      <?php foreach($categories as $category) { ?>
        <div id="category-<?php echo $category['category_id']; ?>" class="category-container">
      <?php $i = 0; ?>
      <?php foreach($category['products'] as $product) { ?>
          <div class="collapse-item product-item
                      product-<?php echo $product['product_id']; ?>
                      reference-<?php echo $i; ?>
                      <?php if ($product['description']) { ?>
                      has-description
                      <?php } ?>
                      "
              data-product-reference="<?php echo $i; ?>"
              data-product-id="<?php echo $product['product_id']; ?>"
              ng-click="tabs.updateBilling($event)">
            <div class="icon-container">
              <i class="fa fa-recycle"></i>
            </div>
            <div class="price"><?php echo $product['price']; ?></div>
            <h5><?php echo $product['name']; ?></h5>
            <?php if ($product['description']) { ?>
            <div class="description"><?php echo $product['description']; ?></div>
            <?php } ?>
          </div>
      <?php $i++; ?>
      <?php } ?>
        </div>
      <?php } ?>
      </div>
    </div>
  </div>
  <div class="card" id="payment-card">
    <div class="card-content row">
      <div class="confirm-payment <?php echo ($recurring_product_id ? 'hidden' : ''); ?>">
        <h5><?php echo $text_confirm_payment; ?></h5>
        <p><?php echo $text_confirm_payment_card; ?></p>
        <?php if ($text_customer_discount) { ?>
        <div class="alert alert-success"><?php echo $text_customer_discount; ?></div>
        <?php } ?>
        <div id="modules">
          <span id="coupon-trigger" ng-click="billing.displayCoupon($event)"><?php echo $text_have_coupon; ?></span>
          <?php foreach ($modules as $module) { ?>
          <?php echo $module; ?>
          <?php } ?>
        </div>
      </div>
      <div class="update-payment <?php echo ($recurring_product_id ? '' : 'hidden'); ?>">
        <h5><?php echo $text_update_payment; ?></h5>
        <p><?php echo $text_update_payment_card; ?></p>
      </div>
      <div id="order-totals" class="confirm-payment">
        <table class="table"></table>
      </div>
      <div id="payment-method">
        <?php echo $payment_method; ?>
        <div class="buttons">
          <button id="button-confirm" class="btn btn-primary confirm-payment <?php echo ($recurring_product_id ? 'hidden' : ''); ?>"
                  data-reset-text="<?php echo $button_confirm; ?>"
                  data-loading-text="<?php echo $text_processing_payment; ?>"
                  ng-click="billing.validatePayment($event)">
            <?php echo $button_confirm; ?>
          </button>
          <button id="button-update" class="btn btn-primary update-payment <?php echo ($recurring_product_id ? '' : 'hidden'); ?>"
                  data-reset-text="<?php echo $button_update; ?>"
                  data-loading-text="<?php echo $text_updating_payment_details; ?>"
                  ng-click="billing.validatePayment($event)">
            <?php echo $button_update; ?>
          </button>
        </div>
        <?php if (!$recurring_product_id) { ?>
        <!--<a href="<?php echo $link_profile; ?>" class="currency-link">
          <?php echo $text_currency_link; ?>
        </a>-->
        <?php } ?>
        <div class="confirm-payment <?php echo ($recurring_product_id ? 'hidden' : ''); ?>"><?php echo $text_confirm_payment_bottom; ?></div>
      </div>
    </div>
  </div>

  <?php if ($upcoming_orders) { ?>
  <div class="card" id="card-payments">
    <div id="payments" class="card-content row">
      <h5><?php echo $text_upcoming_payments; ?></h5>
      <p><?php echo $upcoming_payments_description; ?></p>
      <table class="table">
        <?php foreach($upcoming_orders as $upcoming_order) { ?>
        <tr>
          <td class="text-left">
            <?php foreach ($upcoming_order['products'] as $upcoming_order_product) { ?>
            <strong><?php echo $upcoming_order_product['name']; ?></strong>
            <?php } ?>
            <br>
            <?php echo $upcoming_order['date']; ?>
          </td>
          <td class="text-right">
            <strong><?php echo $upcoming_order['total']; ?></strong><br>&nbsp;
          </td>
        </tr>
        <?php } ?>
      </table>
      <?php if ($upcoming_orders) { ?>
      <a href="<?php echo $href_customer_order; ?>"><?php echo $text_view_all_payments; ?></a>
      <?php } ?>
    </div>
  </div>
  <?php } else { ?>
  <div class="card hide" id="card-payments">
    <div id="payments" class="card-content row">
      <h5><?php echo $text_upcoming_payments; ?></h5>
      <p><?php echo $upcoming_payments_description; ?></p>
      <table class="table"></table>
      <a href="<?php echo $href_customer_order; ?>"><?php echo $text_view_all_payments; ?></a>
    </div>
  </div>
  <? } ?>

  <div class="clearfix"></div>
  <hr>

  <div class="card">
    <div class="card-header row">
      <h4><?php echo $text_cancel_account_title; ?></h4>
    </div>
    <div class="card-content row">
      <p><?php echo $text_cancel_account_description; ?></p><br>
      <button class="btn btn-danger btn-lg" data-toggle="modal" data-target="#cancel-account-modal"><i class="fa fa-trash"></i> <?php echo $button_cancel_account; ?></button><br><br>
      <p><?php echo $text_cancel_account_notice; ?></p>
    </div>
  </div>
</div>

<div id="cancel-account-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <i id="bigwarning" class="fa fa-exclamation-triangle"></i>
        <h4 class="modal-title">
          <?php echo $text_cancel_account_title; ?>
        </h4>
        <p><?php echo $text_cancel_account_confirm; ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default cancel" data-dismiss="modal"><?php echo $button_no; ?></button>
        <button type="button" id="button-cancel-account" class="btn btn-danger confirm"
                ng-controller="BillingTabController as billing"
                ng-click="billing.cancelAccount('<?php echo $account_id; ?>')"
                data-loading="<?php echo $button_loading; ?>"
                data-yes="<?php echo $button_yes; ?>"><?php echo $button_yes; ?></button>
      </div>
    </div>
  </div>
</div>
<script src="//www.googleadservices.com/pagead/conversion_async.js"></script>
<script>
jQuery(document).ready(function($) {
	window.updating_cart = false;

	window.couponResponse = function(json) {
		jQuery('#billing-notifications').html('');

		if (json['success']) {
			updateCart();
		} else if (json['error']) {
			jQuery('#billing-notifications').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
		}
	}

	window.voucherResponse = function(json) {
		jQuery('#billing-notifications').html('');

		if (json['success']) {
			updateCart();
		} else if (json['error']) {
			jQuery('#billing-notifications').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
		}
	}
});
</script>