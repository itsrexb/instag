<?php echo $header; ?>
<div class="purple-padding"></div>
<div class="boxed-container text-center" id="register-page">
  <div class="row">
    <div id="content" class="no-fixed">
      <?php echo $content_top; ?>
      <h1><?php echo $text_order_detail; ?></h1>
      <table class="table table-bordered table-hover">
        <tbody>
          <tr>
            <td class="text-center" style="width: 50%;"><?php if ($invoice_no) { ?>
              <b><?php echo $text_invoice_no; ?></b> <?php echo $invoice_no; ?><br />
              <?php } ?>
              <b><?php echo $text_order_id; ?></b> #<?php echo $order_id; ?><br />
              <b><?php echo $text_date_added; ?></b> <?php echo $date_added; ?></td>
          </tr>
        </tbody>
      </table>
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <td class="text-left"><?php echo $column_name; ?></td>
              <td class="text-right"><?php echo $column_price; ?></td>
              <td class="text-right"><?php echo $column_total; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php $row_class = 'dark-row'; ?>
            <?php foreach ($products as $product) { ?>
            <?php if ($row_class == '') { ?>
            <?php $row_class = 'dark-row'; ?>
            <?php } else { ?>
            <?php $row_class = ''; ?>
            <?php } ?>
            <tr class="<?php echo $row_class; ?>">
              <td class="text-left">
                <?php echo $product['name']; ?>
                <?php if ($product['account_username']) { ?>
                <br>
                <?php if ($product['account_type']) { ?>
                &nbsp;<small> - <?php echo ucwords($product['account_type']); ?> <?php echo $text_account; ?> <?php echo $product['account_username']; ?></small>
                <?php } else { ?>
                &nbsp;<small> - <?php echo $text_account; ?> <?php echo $product['account_username']; ?></small>
                <?php } ?>
                <?php } ?></td>
              <td class="text-right"><?php echo $product['price']; ?></td>
              <td class="text-right"><?php echo $product['total']; ?></td>
            </tr>
            <?php } ?>
            <?php $row_class = 'dark-row'; ?>
            <?php foreach ($vouchers as $voucher) { ?>
            <?php if ($row_class == '') { ?>
            <?php $row_class = 'dark-row'; ?>
            <?php } else { ?>
            <?php $row_class = ''; ?>
            <?php } ?>
            <tr class="<?php echo $row_class; ?>">
              <td class="text-left"><?php echo $voucher['description']; ?></td>
              <td class="text-right"><?php echo $voucher['amount']; ?></td>
              <td class="text-right"><?php echo $voucher['amount']; ?></td>
            </tr>
            <?php } ?>
          </tbody>
          <tfoot>
            <?php $row_class = 'dark-row'; ?>
            <?php foreach ($totals as $total) { ?>
            <?php if ($row_class == '') { ?>
            <?php $row_class = 'dark-row'; ?>
            <?php } else { ?>
            <?php $row_class = ''; ?>
            <?php } ?>
            <tr class="<?php echo $row_class; ?>">
              <td></td>
              <td class="text-right"><b><?php echo $total['title']; ?></b></td>
              <td class="text-right"><?php echo $total['text']; ?></td>
            </tr>
            <?php } ?>
          </tfoot>
        </table>
      </div>
      <?php if ($comment) { ?>
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <td class="text-left"><?php echo $text_comment; ?></td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-left"><?php echo $comment; ?></td>
          </tr>
        </tbody>
      </table>
      <?php } ?>
      <?php if ($histories) { ?>
      <h3><?php echo $text_history; ?></h3>
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <td class="text-center"><?php echo $column_date_added; ?></td>
            <td class="text-center"><?php echo $column_status; ?></td>
            <td class="text-center"><?php echo $column_comment; ?></td>
          </tr>
        </thead>
        <tbody>
          <?php $row_class = 'dark-row'; ?>
          <?php foreach ($histories as $history) { ?>
          <?php if ($row_class == '') { ?>
          <?php $row_class = 'dark-row'; ?>
          <?php } else { ?>
          <?php $row_class = ''; ?>
          <?php } ?>
          <tr>
            <td class="text-center"><?php echo $history['date_added']; ?></td>
            <td class="text-center"><?php echo $history['status']; ?></td>
            <td class="text-center"><?php echo $history['comment']; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } ?>
      <?php echo $content_bottom; ?>
    </div>
  </div>
</div>
<?php echo $footer; ?>