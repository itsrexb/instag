<?php echo $header; ?>
<div class="purple-padding"></div>
<div class="boxed-container register-affiliate text-center" id="register-page">
  <div class="row">
    <div id="content">
      <?php if ($error_warning) { ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
      <?php } ?>
      <?php echo $content_top; ?>
      <h1><?php echo $heading_title; ?></h1>
      <p><?php echo $text_signup; ?></p>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
        <input type="hidden" name="agree" value="1">
        <fieldset>
          <div class="form-group required">
            <input type="text" name="firstname" value="<?php echo $firstname; ?>" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname" class="form-control">
            <?php if ($error_firstname) { ?>
            <div class="text-danger"><?php echo $error_firstname; ?></div>
              <?php } ?>
          </div>
          <div class="form-group required">
            <input type="text" name="lastname" value="<?php echo $lastname; ?>" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname" class="form-control">
            <?php if ($error_lastname) { ?>
            <div class="text-danger"><?php echo $error_lastname; ?></div>
            <?php } ?>
          </div>
          <div class="form-group required">
            <input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control">
            <?php if ($error_email) { ?>
            <div class="text-danger"><?php echo $error_email; ?></div>
            <?php } ?>
          </div>
          <div class="form-group required">
            <input type="text" name="telephone" value="<?php echo $telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control">
            <?php if ($error_telephone) { ?>
            <div class="text-danger"><?php echo $error_telephone; ?></div>
            <?php } ?>
          </div>
        </fieldset>
        <fieldset>
          <legend><?php echo $text_your_address; ?></legend>
          <div class="form-group">
            <input type="text" name="company" value="<?php echo $company; ?>" placeholder="<?php echo $entry_company; ?>" id="input-company" class="form-control">
          </div>
          <div class="form-group">
            <input type="text" name="website" value="<?php echo $website; ?>" placeholder="<?php echo $entry_website; ?>" id="input-website" class="form-control">
          </div>
          <div class="form-group required">
            <input type="text" name="address_1" value="<?php echo $address_1; ?>" placeholder="<?php echo $entry_address_1; ?>" id="input-address-1" class="form-control">
            <?php if ($error_address_1) { ?>
            <div class="text-danger"><?php echo $error_address_1; ?></div>
            <?php } ?>
          </div>
          <div class="form-group">
            <input type="text" name="address_2" value="<?php echo $address_2; ?>" placeholder="<?php echo $entry_address_2; ?>" id="input-address-2" class="form-control">
          </div>
          <div class="form-group required">
            <input type="text" name="city" value="<?php echo $city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control">
            <?php if ($error_city) { ?>
            <div class="text-danger"><?php echo $error_city; ?></div>
            <?php } ?>
          </div>
          <div class="form-group required">
            <input type="text" name="postcode" value="<?php echo $postcode; ?>" placeholder="<?php echo $entry_postcode; ?>" id="input-postcode" class="form-control">
            <?php if ($error_postcode) { ?>
            <div class="text-danger"><?php echo $error_postcode; ?></div>
            <?php } ?>
          </div>
          <legend><?php echo $text_country; ?></legend>
          <div class="form-group required">
            <select name="country_id" id="input-country" class="form-control">
              <option value="false"><?php echo $text_select; ?></option>
              <?php foreach ($countries as $country) { ?>
              <?php if ($country['country_id'] == $country_id) { ?>
              <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
            <?php if ($error_country) { ?>
            <div class="text-danger"><?php echo $error_country; ?></div>
            <?php } ?>
          </div>
          <div class="form-group required">
            <select name="zone_id" id="input-zone" class="form-control">
            </select>
            <?php if ($error_zone) { ?>
            <div class="text-danger"><?php echo $error_zone; ?></div>
            <?php } ?>
          </div>
        </fieldset>
        <fieldset>
          <legend><?php echo $text_payment; ?></legend>
          <div class="form-group">
            <input type="text" name="tax" value="<?php echo $tax; ?>" placeholder="<?php echo $entry_tax; ?>" id="input-tax" class="form-control">
          </div>
          <div class="form-group">
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
                <?php if ($payment == 'check') { ?>
                <input type="radio" name="payment" value="check" checked="checked">
                <?php } else { ?>
                <input type="radio" name="payment" value="check">
                <?php } ?>
                <?php echo $text_check; ?></label>
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
          <div class="form-group payment" id="payment-check">
            <input type="text" name="payment_data[check]" value="<?php echo (isset($payment_data['check']) ? $payment_data['check'] : ''); ?>" placeholder="<?php echo $entry_check; ?>" id="input-check" class="form-control">
          </div>
          <div class="form-group payment" id="payment-paypal">
            <input type="text" name="payment_data[paypal]" value="<?php echo (isset($payment_data['paypal']) ? $payment_data['paypal'] : ''); ?>" placeholder="<?php echo $entry_paypal; ?>" id="input-paypal" class="form-control">
          </div>
          <div class="payment" id="payment-bank">
            <div class="form-group">
              <input type="text" name="payment_data[bank_name]" value="<?php echo (isset($payment_data['bank_name']) ? $payment_data['bank_name'] : ''); ?>" placeholder="<?php echo $entry_bank_name; ?>" id="input-bank-name" class="form-control">
            </div>
            <div class="form-group">
              <input type="text" name="payment_data[bank_branch_number]" value="<?php echo (isset($payment_data['bank_branch_number']) ? $payment_data['bank_branch_number'] : ''); ?>" placeholder="<?php echo $entry_bank_branch_number; ?>" id="input-bank-branch-number" class="form-control">
            </div>
            <div class="form-group">
              <input type="text" name="payment_data[bank_swift_code]" value="<?php echo (isset($payment_data['bank_swift_code']) ? $payment_data['bank_swift_code'] : ''); ?>" placeholder="<?php echo $entry_bank_swift_code; ?>" id="input-bank-swift-code" class="form-control">
            </div>
            <div class="form-group">
              <input type="text" name="payment_data[bank_account_name]" value="<?php echo (isset($payment_data['bank_account_name']) ? $payment_data['bank_account_name'] : ''); ?>" placeholder="<?php echo $entry_bank_account_name; ?>" id="input-bank-account-name" class="form-control">
            </div>
            <div class="form-group">
              <input type="text" name="payment_data[bank_account_number]" value="<?php echo (isset($payment_data['bank_account_number']) ? $payment_data['bank_account_number'] : ''); ?>" placeholder="<?php echo $entry_bank_account_number; ?>" id="input-bank-account-number" class="form-control">
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend><?php echo $text_your_password; ?></legend>
          <div class="form-group required">
            <input type="password" name="password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control">
            <?php if ($error_password) { ?>
            <div class="text-danger"><?php echo $error_password; ?></div>
            <?php } ?>
          </div>
          <div class="form-group required">
            <input type="password" name="confirm" value="<?php echo $confirm; ?>" placeholder="<?php echo $entry_confirm; ?>" id="input-confirm" class="form-control">
            <?php if ($error_confirm) { ?>
            <div class="text-danger"><?php echo $error_confirm; ?></div>
            <?php } ?>
          </div>
        </fieldset>
        <div class="buttons clearfix">
          <div class="text-center">
            <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary">
          </div>
        </div>
        <div class="register-terms">
          <p>
            <?php if ($text_agree) { ?>
            <?php echo $text_agree; ?>
            <?php } ?>
          </p>
        </div>
      </form>
      <?php echo $content_bottom; ?></div>
  </div>
</div>

<?php echo $footer; ?>