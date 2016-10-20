<?php echo $header; ?>
<div class="purple-padding"></div>
<div class="boxed-container text-center" id="order-page">
  <div class="row">
    <div id="content">
      <?php echo $content_top; ?>
      <h1><?php echo $heading_title; ?></h1>
      <?php if ($orders) { ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <td class="text-center"><?php echo $column_order_id; ?></td>
              <td class="text-center"><?php echo $column_date_added; ?></td>
              <td class="text-center"><?php echo $column_total; ?></td>
              <td class="text-center"><?php echo $column_status; ?></td>   
              <td></td>
            </tr>
          </thead>
          <tbody>
            <?php $row_class = 'dark-row'; ?>
            <?php foreach ($orders as $order) { ?>
            <?php if ($row_class == '') { ?>
            <?php $row_class = 'dark-row'; ?>
            <?php } else { ?>
            <?php $row_class = ''; ?>
            <?php } ?>
            <tr class="<?php echo $row_class; ?>">
              <td class="text-center">#<?php echo $order['order_id']; ?></td>
              <td class="text-center"><?php echo $order['date_added']; ?></td>
              <td class="text-center"><?php echo $order['total']; ?></td>
              <td class="text-center"><?php echo $order['status']; ?></td>
              <td class="text-center"><a href="<?php echo $order['href']; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="text-right"><?php echo $pagination; ?></div>
      <?php } else { ?>
      <p><?php echo $text_empty; ?></p>
      <?php } ?>
      <br><br><br>
      <?php echo $content_bottom; ?>
    </div>
  </div>
</div>
<?php echo $footer; ?>