<?php echo $header; ?>
<div class="purple-padding"></div>
<div class="boxed-container affiliate-page text-center" id="affiliate-dashboard">
  <div class="row">
    <div id="content" class="no-fixed">
      <?php if ($success) { ?>
      <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
      <?php } ?>
      <?php echo $content_top; ?>
      <h1><?php echo $heading_title; ?></h1>
      <p><?php echo $text_balance; ?> <strong><?php echo $balance; ?></strong>.<br>
        <a href="<?php echo $href_transaction; ?>"><?php echo $text_transaction; ?></a></p>
      <p>
        <div class="input-group">
          <input type="text" id="tracking-link" value="<?php echo $tracking_link; ?>" class="form-control">
          <span class="input-group-btn">
            <button type="button" id="copy-tracking-link" class="btn gold-button" data-clipboard-target="#tracking-link"><?php echo $button_copy; ?></button>
          </span>
        </div>
      </p>
      <p><a href="<?php echo $href_profile; ?>"><?php echo $text_profile; ?></a></p>
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <td class="text-left"><?php echo $column_date_added; ?></td>
              <td class="text-left"><?php echo $column_name; ?></td>
              <td class="text-right"><?php echo $column_actions; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php if ($customers) { ?>
            <?php foreach ($customers  as $customer) { ?>
            <tr>
              <td class="text-left"><?php echo $customer['date_added']; ?></td>
              <td class="text-left"><?php echo $customer['name']; ?></td>
              <td class="text-right"><a href="<?php echo $customer['href']; ?>" title="" class="btn btn-info" data-toggle="tooltip" data-original-title="<?php echo $text_view; ?>"><i class="fa fa-eye"></i></a></td>
            </tr>
            <?php } ?>
            <tr>
            	<td class="text-right" colspan="3"><a href="<?php echo $href_customers; ?>" title="<?php echo $text_view_all; ?>"><?php echo $text_view_all; ?></a></td>
            </tr>
            <?php } else { ?>
            <tr>
              <td class="text-center" colspan="3"><?php echo $text_empty_customers; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <?php echo $content_bottom; ?></div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.5/clipboard.min.js"></script>
<script>
jQuery(document).ready(function() {
	new Clipboard('#copy-tracking-link');
});
</script>

<?php echo $footer; ?>