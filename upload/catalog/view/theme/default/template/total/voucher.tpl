<div class="form-group">
  <div class="input-group">
    <input type="text" name="voucher" value="<?php echo $voucher; ?>" placeholder="<?php echo $entry_voucher; ?>" id="input-voucher" class="form-control">
    <span class="input-group-btn">
      <input type="button" value="<?php echo $button_apply; ?>" id="button-voucher" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary">
    </span>
  </div>
</div>
<script>
jQuery('#button-voucher').on('click', function() {
	jQuery.ajax({
		url: 'index.php?route=total/voucher/voucher',
		type: 'post',
		data: 'voucher=' + encodeURIComponent(jQuery('#input-voucher').val()),
		dataType: 'json',
		beforeSend: function() {
			jQuery('#button-voucher').button('loading');
		},
		complete: function() {
			jQuery('#button-voucher').button('reset');
		},
		success: function(json) {
			if (typeof voucherResponse == 'function') {
				voucherResponse(json);
			} else {
				if (json['error']) {
					alert(json['error']);
				}
			}
		}
	});
});
</script>