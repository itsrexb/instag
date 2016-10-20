<?php echo $header; ?>
<div class="purple-padding"></div>
<div class="boxed-container" id="customer-kickoff" ng-init='load()' ng-controller="AccountsController as accounts">
  <div class="row">
    <div id="content">
      <div id="kickoff-carousel" class="carousel slide" data-ride="carousel" data-interval="false">
        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
          <div id="add-account-form" class="item active">
            <?php echo $content_top; ?>
            <form action="" novalidate onsubmit="return false" class="form-horizontal">
              <fieldset id="account">
                <div class="form-group required">
                  <input  type="text" id="add-account-username" name="username"
                          placeholder="<?php echo $entry_username; ?>" class="form-control"
                          ng-focus="accounts.hideTooltip($event)"
                          data-hide-fire="focus">
                  <i class="fa fa-instagram"></i>
                </div>
                <div class="form-group required">
                  <input  type="password" id="add-account-password" name="password"
                          placeholder="<?php echo $entry_password; ?>" class="form-control"
                          ng-focus="accounts.hideTooltip($event)"
                          data-hide-fire="focus">
                  <i class="fa fa-lock"></i>
                </div>
                <div class="buttons">
                  <button type="submit" id="button-add-instagram" class="btn btn-primary"
                          ng-click="accounts.addAccount()">
                    <?php echo $button_add_account; ?>
                  </button>
                </div>
              </fieldset>
            </form>
            <p class="small text-center" style="font-weight: bold;"><?php echo $text_privacy; ?></p>
          </div>
          <div id="verify-account" class="item">
            <?php echo $content_bottom; ?>
            <p class="text-center">
              <button id="retry-add-account" class="gold-button" ng-click="accounts.retryAddAccount()"><?php echo $text_verified_account; ?></button>
              <a id="add-instagram-prev" href="#kickoff-carousel" role="button" data-slide="prev"><?php echo $button_change_account_info; ?></a>
              <a id="add-instagram-next" href="#kickoff-carousel" role="button" data-slide="next"></a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="kickoff-error-modal" class="modal warning-modal" ng-click="accounts.closeModal($event)">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <p><?php echo $text_failed_modal; ?></p>
        </div>
        <div class="modal-footer">
          <button class="gold-button" onclick="$zopim.livechat.window.show();" ng-click="accounts.closeModal()">
            <?php echo $button_chat; ?>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<?php foreach ($conversions as $conversion) { ?>
<?php echo $conversion; ?>
<?php } ?>
<?php echo $footer; ?>