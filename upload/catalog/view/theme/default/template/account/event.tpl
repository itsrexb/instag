<?php foreach ($events as $event) { ?>
<?php
switch ($event['code'])
{
case 'start':
	$icon = 'play';
	break;
case 'stop':
	switch($event['message'])
	{
	case 'invalid_token':
		$icon = 'refresh';
		break;
	case 'no_activity':
		$icon = 'exclamation-triangle';
		break;
	case 'temp_ban': case 'temp_block':
		$icon = 'ban';
		break;
	default:
		$icon = 'square';
	}
	break;
case 'start_follow':
	$icon = 'arrow-up';
	break;
case 'start_unfollow':
	$icon = 'arrow-down';
	break;
case 'start_sleep':
	$icon = 'moon-o';
	break;
case 'end_sleep':
	$icon = 'moon-o';
	break;
}
?>
<div class="sidebar-item history-item">
  <i class="fa fa-<?php echo $icon; ?>"></i>
  <div class="item-content">
    <h4><?php echo $event['title']; ?></h4>
    <p>
      <?php echo $event['description']; ?>
    </p>
    <small class="time"><?php echo $event['date_added']; ?></small>
  </div>
</div>
<?php } ?>