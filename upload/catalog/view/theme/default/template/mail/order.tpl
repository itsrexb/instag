<div style="background: #fff; color: #666; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px;">
  <div style="background: #8137B0; margin-bottom: 20px; padding: 17px 20px 18px 20px;">
    <div style="max-width: 666px; margin: 0 auto;"><a href="<?php echo $store_url; ?>" title="<?php echo $store_name; ?>">
      <img src="<?php echo $logo; ?>" alt="<?php echo $store_name; ?>" style="border: none; max-height: 25px;" /></a>
    </div>
  </div>
  <div style="min-width: 320px; max-width: 680px; margin: 0 auto; text-align: center;">
    <p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_greeting; ?></p>
    <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
      <thead>
        <tr>
          <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;" colspan="2"><?php echo $text_order_detail; ?></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><b><?php echo $text_customer; ?></b> <?php echo $customer; ?><br />
            <b><?php echo $text_email; ?></b> <?php echo $email; ?><br />
            <?php if ($telephone) { ?>
            <b><?php echo $text_telephone; ?></b> <?php echo $telephone; ?><br />
            <?php } ?>
            <b><?php echo $text_order_status; ?></b> <?php echo $order_status; ?><br /></td>
          <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><b><?php echo $text_order_id; ?></b> <?php echo $order_id; ?><br />
            <b><?php echo $text_date_added; ?></b> <?php echo $date_added; ?><br />
            <?php if ($affiliate) { ?>
            <b><?php echo $text_affiliate; ?></b> <?php echo $affiliate; ?><br />
            <?php } ?>
            <?php if ($ext_aff_id) { ?>
            <b><?php echo $text_ext_aff_id; ?></b> <?php echo $ext_aff_id; ?><br />
            <?php } ?></td>
        </tr>
      </tbody>
    </table>
    <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
      <thead>
        <tr>
          <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;" colspan="2"><?php echo $text_product; ?></td>
          <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;"><?php echo $text_price; ?></td>
          <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;"><?php echo $text_total; ?></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $product) { ?>
        <tr>
          <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;" colspan="2"><?php echo $product['name']; ?>
            <?php if ($product['account_username']) { ?>
            <br />
            <?php if ($product['account_type']) { ?>
            &nbsp;<small> - <?php echo ucwords($product['account_type']); ?> <?php echo $text_account; ?> <?php echo $product['account_username']; ?></small>
            <?php } else { ?>
            &nbsp;<small> - <?php echo $text_account; ?> <?php echo $product['account_username']; ?></small>
            <?php } ?>
            <?php } ?></td>
          <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $product['price']; ?></td>
          <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $product['total']; ?></td>
        </tr>
        <?php } ?>
        <?php foreach ($vouchers as $voucher) { ?>
        <tr>
          <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $voucher['description']; ?></td>
          <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"></td>
          <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $voucher['amount']; ?></td>
          <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $voucher['amount']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <?php foreach ($totals as $total) { ?>
        <tr>
          <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;" colspan="3"><b><?php echo $total['title']; ?>:</b></td>
          <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $total['text']; ?></td>
        </tr>
        <?php } ?>
      </tfoot>
    </table>
    <?php if ($comment) { ?>
    <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
      <thead>
        <tr>
          <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?php echo $text_instruction; ?></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $comment; ?></td>
        </tr>
      </tbody>
    </table>
    <?php } ?>
    <p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_footer; ?></p>
  </div>
</div>