<?php if ($event_activities) { ?>
<?php foreach ($event_activities as $event) { ?>
<div  class="sidebar-item activity-item">
  <i class="fa fa-<?php echo strtolower($event->Code); ?> activity-icon"></i>
  <div class="item-content">
    <p>
      <?php echo $event->Description; ?>
    </p>
    <?php if (isset($event->OriginType)) { ?>
    <p>
      <?php if ($event->OriginType == 'tagger') { ?>
      #<?php echo $event->Origin; ?>
      <?php } else if ($event->OriginType == 'location') { ?>
      <i class="fa fa-map-marker"></i> <?php echo (isset($event->OriginName) ? $event->OriginName : $event->Origin); ?>
      <?php } else { ?>
      <i class="fa fa-<?php echo strtolower($event->OriginType); ?>"></i> <?php if (isset($event->OriginUsername)) { ?>@<?php echo $event->OriginUsername; ?><?php } ?>
      <?php } ?>
    </p>
    <?php } ?>
    <small data-time="<?php echo str_replace('-', '/', $event->AddedDateTime); ?>"></small>
  </div>
</div>
<?php } ?>
<div class="last-key" data-key="<?php echo $last_evaluated_key; ?>"></div>
<button id="loading-activity" class="more-activities loading-button"></button>
<?php } else { ?>
<div class="sidebar-item no-activity-item"><img src="catalog/view/theme/default/image/no-activity.jpg"></div>
<?php } ?>