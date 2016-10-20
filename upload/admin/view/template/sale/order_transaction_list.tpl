<?php if ($error_warning) { ?>
<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
<?php } ?>
<table class="table">
  <thead>
    <tr>
      <td class="left"><?php echo $column_date_added; ?></td>
      <td class="left"><?php echo $column_payment_method; ?></td>
      <td class="left"><?php echo $column_transaction_id; ?></td>
      <td class="left"><?php echo $column_status; ?></td>
      <td class="right"><?php echo $column_amount; ?></td>
      <td class="right"><?php echo $column_actions; ?></td>
    </tr>
  </thead>
  <?php if ($order_transactions) { ?>
  <tbody>
    <?php foreach ($order_transactions as $order_transaction) { ?>
    <tr>
      <td class="left"><?php echo $order_transaction['date_added']; ?></td>
      <td class="left"><?php echo $order_transaction['payment_method']; ?></td>
      <td class="left"><?php echo $order_transaction['transaction_id']; ?></td>
      <td class="left"><?php echo $order_transaction['status']; ?></td>
      <td class="right"><?php echo $order_transaction['amount']; ?></td>
      <td class="right"><?php if ($order_transaction['actions']) { ?>
        <?php foreach ($order_transaction['actions'] as $action) { ?>
        <?php if ($action['class'] == 'refund') { ?>
        <?php if ($total_amount_paid_value > 0) { ?>
        <input type="text" name="amount" value="<?php echo $total_amount_paid_value; ?>" size="10"><br>
        <a href="<?php echo $action['href']; ?>" class="button <?php echo $action['class']; ?>"><?php echo $action['label']; ?></a>
        <?php } ?>
        <?php } else { ?>
        <a href="<?php echo $action['href']; ?>" class="button <?php echo $action['class']; ?>"><?php echo $action['label']; ?></a>
        <?php } ?>
        <?php } ?>
        <?php } ?></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <tr>
      <td class="right" colspan="4"><strong><?php echo $text_total_amount_paid; ?></strong></td>
      <td class="right"><strong><?php echo $total_amount_paid; ?></strong></td>
      <td></td>
    </tr>
  </tfoot>
  <?php } else { ?>
  <tbody>
    <tr>
      <td class="center" colspan="6"><?php echo $text_no_results; ?></td>
    </tr>
  </tbody>
  <?php } ?>
</table>

<?php if ($permission_modify) { ?>
<script>
$('a.capture, a.refund', '#transaction').on('click', function() {
	var $this = $(this);

	$.ajax({
		url: this.href,
		type: 'post',
		data: 'amount=' + encodeURIComponent($('input[name="amount"]', $this.parent()).val()),
		dataType: 'json',
		beforeSend: function() {
			$this.prop('disabled', true);

			$('.alert').remove();

			$('#transaction').prepend('<div class="alert alert-warning"><?php echo $text_wait; ?></div>');
		},
		success: function(json) {
			if (json.order_status_id) {
				$('#input-order-status').val(json.order_status_id);

				addOrderHistory(json.order_status_id, false, false, (json.comment ? json.comment : ''));
			}

			if (json.html) {
				$('#transaction').html(json.html);
			}
		}
	});

	return false;
});
</script>
<?php } ?>