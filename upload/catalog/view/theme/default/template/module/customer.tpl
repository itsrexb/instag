<div class="list-group">
  <?php if (!$logged) { ?>
  <a href="<?php echo $login; ?>" class="list-group-item"><?php echo $text_login; ?></a>
  <a href="<?php echo $register; ?>" class="list-group-item"><?php echo $text_register; ?></a>
  <a href="<?php echo $forgotten; ?>" class="list-group-item"><?php echo $text_forgotten; ?></a>
  <?php } ?>
  <a href="<?php echo $dashboard; ?>" class="list-group-item"><?php echo $text_dashboard; ?></a>
  <a href="<?php echo $profile; ?>" class="list-group-item"><?php echo $text_profile; ?></a>
  <?php if ($reward) { ?>
  <a href="<?php echo $reward; ?>" class="list-group-item"><?php echo $text_reward; ?></a>
  <?php } ?>
  <a href="<?php echo $transaction; ?>" class="list-group-item"><?php echo $text_transaction; ?></a>
  <?php if ($logged) { ?>
  <a href="<?php echo $logout; ?>" class="list-group-item"><?php echo $text_logout; ?></a>
  <?php } ?>
</div>