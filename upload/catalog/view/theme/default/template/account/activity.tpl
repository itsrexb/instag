<?php if ($activities) { ?>
<?php foreach ($activities as $activity) { ?>
<div 	class="sidebar-item activity-item">
  <i class="fa fa-<?php echo strtolower($activity->Activity); ?> activity-icon"></i>
  <div class="item-content">
    <p>
      <?php echo $activity->Description; ?>
    </p>
    <?php if (isset($activity->OriginType)) { ?>
    <p>
      <?php if ($activity->OriginType == 'tagger') { ?>
      #<?php echo $activity->Origin; ?>
      <?php } else if ($activity->OriginType == 'location') { ?>
      <i class="fa fa-map-marker"></i> <?php echo (isset($activity->OriginName) ? $activity->OriginName : $activity->Origin); ?>
      <?php } else { ?>
      <i class="fa fa-<?php echo strtolower($activity->OriginType); ?>"></i> @<?php echo $activity->OriginUsername; ?>
      <?php } ?>
    </p>
    <?php } ?>
    <small data-time="<?php echo str_replace('-', '/', $activity->AddedDateTime); ?>"></small>
  </div>
</div>
<?php } ?>
<div class="last-key" data-key="<?php echo $last_evaluated_key; ?>"></div>
<button id="loading-activity" class="more-activities loading-button"></button>
<?php } else { ?>
<div class="sidebar-item no-activity-item"><img src="catalog/view/theme/default/image/no-activity.jpg"></div>
<?php } ?>