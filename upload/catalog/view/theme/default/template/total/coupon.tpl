<div class="form-group">
  <div class="input-group">
    <input type="text" name="coupon" value="<?php echo $coupon; ?>" placeholder="<?php echo $entry_coupon; ?>" id="input-coupon" class="form-control">
    <span class="input-group-btn">
      <input type="button" value="<?php echo $button_apply; ?>" id="button-coupon" class="btn btn-primary" ng-click="billing.applyCoupon()">
    </span>
  </div>
</div>