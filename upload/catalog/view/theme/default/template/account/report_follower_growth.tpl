<?php if ($report_follower_growth['historical']) { ?>
<div id="chart-data">
  <?php foreach ($report_follower_growth as $datakey => $dataset) { ?>
  <div class="<?php echo $datakey; ?>-data" data-key="<?php echo ${'text_' . $datakey}; ?>">
    <?php foreach ($dataset as $data) { ?>
      <div data-date="<?php echo date('m-d',strtotime($data['date'])); ?>" data-followers="<?php echo $data['followers']; ?>"></div>
    <?php } ?>
  </div>
  <?php } ?>
</div>
<div class="chart-body">
  <canvas id="chart" width="400" height="200"></canvas>
</div>
<?php } else { ?>
<div id="follower-growth-empty">
  <i class="fa fa-cogs"></i>
  <p>
    <?php echo $text_no_graph; ?>
  </p>
</div>
<?php } ?>