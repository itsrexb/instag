<?php echo $header; ?>
<div class="purple-padding"></div>
<div class="boxed-container affiliate-page text-center" id="affiliate-customer">
  <div class="row">
    <div id="content" class="no-fixed">
      <?php echo $content_top; ?>
      <h1><?php echo $heading_title; ?></h1>
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <tbody>
            <tr>
              <td class="text-left"><?php echo $column_name; ?></td>
              <td class="text-left"><?php echo $name; ?></td>
            </tr>
            <tr>
              <td class="text-left"><?php echo $column_email; ?></td>
              <td class="text-left"><?php echo $email; ?></td>
            </tr>
            <tr>
              <td class="text-left"><?php echo $column_telephone; ?></td>
              <td class="text-left"><?php echo $telephone; ?></td>
            </tr>
            <tr>
              <td class="text-left"><?php echo $column_active; ?></td>
              <td class="text-left"><?php echo $active; ?></td>
            </tr>
            <tr>
              <td class="text-left"><?php echo $column_date_added; ?></td>
              <td class="text-left"><?php echo $date_added; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
      <h1><?php echo $text_commissions; ?></h1>
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <td class="text-left"><?php echo $column_date_added; ?></td>
              <td class="text-left"><?php echo $column_description; ?></td>
              <td class="text-right"><?php echo $column_commission; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php if ($commissions) { ?>
            <?php foreach ($commissions  as $commission) { ?>
            <tr>
              <td class="text-left"><?php echo $commission['date_added']; ?></td>
              <td class="text-left"><?php echo $commission['description']; ?></td>
              <td class="text-right"><?php echo $commission['amount']; ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td class="text-right" colspan="2"><strong><?php echo $column_total_commissions; ?></strong></td>
              <td class="text-right"><strong><?php echo $total_commissions; ?></strong></td>
            </tr>
            <?php } else { ?>
            <tr>
              <td class="text-center" colspan="3"><?php echo $text_empty_commissions; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="buttons clearfix">
        <div class="text-center">
          <a href="<?php echo $href_customers; ?>" class="btn btn-primary gold-button"><?php echo $button_back_to_customers; ?></a>
        </div>
      </div>
      <?php echo $content_bottom; ?></div>
  </div>
</div>
<?php echo $footer; ?>