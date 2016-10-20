<?php echo $header; ?>
<div class="purple-padding"></div>
<div class="boxed-container text-center">
  <div class="row">
    <div id="content" class="no-fixed">
      <?php echo $content_top; ?>
      <h1><?php echo $heading_title; ?></h1>
      <p><?php echo $text_balance; ?> <strong><?php echo $balance; ?></strong>.</p>
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <td class="text-left"><?php echo $column_date_added; ?></td>
              <td class="text-left"><?php echo $column_description; ?></td>
              <td class="text-right"><?php echo $column_amount; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php if ($transactions) { ?>
            <?php $row_class = 'dark-row'; ?>
            <?php foreach ($transactions  as $transaction) { ?>
            <?php if ($row_class == '') { ?>
            <?php $row_class = 'dark-row'; ?>
            <?php } else { ?>
            <?php $row_class = ''; ?>
            <?php } ?>
            <tr>
              <td class="text-left"><?php echo $transaction['date_added']; ?></td>
              <td class="text-left"><?php echo $transaction['description']; ?></td>
              <td class="text-right"><?php echo $transaction['amount']; ?></td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="text-center" colspan="3"><?php echo $text_empty; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="text-right"><?php echo $pagination; ?></div>
      <div class="buttons clearfix">
        <div class="text-center">
          <a href="<?php echo $dashboard; ?>" class="btn btn-primary gold-button"><?php echo $button_back_to_dashboard; ?></a>
        </div>
      </div>
      <?php echo $content_bottom; ?></div>
  </div>
</div>
<?php echo $footer; ?>