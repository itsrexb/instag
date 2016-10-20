<div id="profile">
  <?php if ($image) { ?>
  <div>
    <img src="<?php echo $image; ?>" alt="<?php echo $firstname; ?> <?php echo $lastname; ?>" title="<?php echo $username; ?>" class="img-circle">
  </div>
  <?php } ?>
  <div>
    <h4><?php echo $firstname; ?> <?php echo $lastname; ?></h4>
    <small><?php echo $user_group; ?></small>
  </div>
</div>