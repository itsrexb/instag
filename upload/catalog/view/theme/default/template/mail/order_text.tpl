<?php echo $text_greeting; ?>


<?php echo $text_order_id; ?> <?php echo $order_id; ?>

<?php echo $text_order_status; ?> <?php echo $order_status; ?>

<?php if ($affiliate) { ?>
<?php echo $text_affiliate; ?> <?php echo $affiliate; ?>

<?php } ?>
<?php if ($ext_aff_id) { ?>
<?php echo $text_ext_aff_id; ?> <?php echo $ext_aff_id; ?>

<?php } ?>

<?php echo $text_products; ?>

<?php foreach ($products as $product) { ?>
<?php echo $product['name']; ?>  <?php echo $product['price']; ?>
<?php if ($product['account_username']) { ?>

<?php if ($product['account_type']) { ?>
    - <?php echo ucwords($product['account_type']); ?> <?php echo $text_account; ?> <?php echo $product['account_username']; ?>
<?php } else { ?>
    - <?php echo $text_account; ?> <?php echo $product['account_username']; ?>
<?php } ?>
<?php } ?>

<?php } ?>
<?php foreach ($vouchers as $voucher) { ?>
<?php echo $voucher['description']; ?>  <?php echo $voucher['price']; ?>

<?php } ?>

<?php foreach ($totals as $total) { ?>
<?php echo $total['title']; ?>: <?php echo $total['text']; ?>

<?php } ?>