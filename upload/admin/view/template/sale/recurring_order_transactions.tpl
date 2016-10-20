<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <td class="text-left"><?php echo $entry_date_added; ?></td>
      <td class="text-left"><?php echo $entry_order_id; ?></td>
      <td class="text-left"><?php echo $entry_amount; ?></td>
      <td class="text-left"><?php echo $entry_status; ?></td>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($transactions as $transaction) { ?>
    <tr>
      <td class="text-left"><?php echo $transaction['date_added']; ?></td>
      <td class="text-left"><a href="<?php echo $transaction['href']; ?>"><?php echo $transaction['order_id']; ?></a></td>
      <td class="text-left"><?php echo $transaction['total']; ?></td>
      <td class="text-left"><?php echo $transaction['status']; ?></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>