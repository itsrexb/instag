<?php if (count($report_source_total) > 1) { ?>
<div id="sources-table">
  <div class="table-header">
    <div id="followback-heading">
      <?php echo $text_followback_header; ?>
    </div>
    <div class="sources-header">
      <?php echo $text_sources; ?>
    </div>
    <div class="col-actions">
      <?php echo $text_actions; ?>
    </div>
    <div class="col-commenters">
      <?php echo $text_commenters; ?>
    </div>
    <div class="col-likers">
      <?php echo $text_likers; ?>
    </div>
    <div class="col-followers">
      <?php echo $text_followers; ?>
    </div>
    <div class="col-followbacks">
      <?php echo $text_total; ?>
    </div>
    <div class="col-ratio">
      <?php echo $text_ratio; ?>
    </div>
  </div>
  <div class="table-body">
    <?php for ($i=0; $i<count($report_source_total); $i++) { ?>
      <div   class="table-row
            <?php
              if (is_int($i/2)) echo 'bg-stripe';
              if ($report_source_total[$i]['type'] == 'unfollow' || $report_source_total[$i]['type'] == 'total') echo ' bold';
            ?>">
        <div  class="users-column <?php echo $report_source_total[$i]['source']; ?>"
              data-type="<?php echo $report_source_total[$i]['type']; ?>"
              data-source="<?php echo $report_source_total[$i]['source']; ?>">
          <i class="fa fa-<?php if ($report_source_total[$i]['type'] != 'unfollow' && $report_source_total[$i]['type'] != 'total') echo $report_source_total[$i]['type']; ?>"></i> 
          <?php echo $report_source_total[$i]['name']; ?>
          <?php if ($report_source_total[$i]['type'] == 'user' || $report_source_total[$i]['type'] == 'location' || $report_source_total[$i]['type'] == 'tag') { ?>
          <i  class="fa fa-trash"
              ng-controller="ModalsController as modals"
              ng-click="modals.topSourcesWarning('source-remove','<?php echo $report_source_total[$i]['source']; ?>','<?php echo $report_source_total[$i]['type']; ?>')"></i>
          <?php } ?>
        </div>
        <div class="col-actions">
          <?php echo $report_source_total[$i]['actions']; ?>
        </div>
        <div class="col-commenters">
          <?php echo $report_source_total[$i]['commenters']; ?>
        </div>
        <div class="col-likers">
          <?php echo $report_source_total[$i]['likers']; ?>
        </div>
        <div class="col-followers">
          <?php echo $report_source_total[$i]['followers']; ?>
        </div>
        <div class="col-followbacks">
          <?php echo $report_source_total[$i]['followbacks']; ?>
        </div>
        <div class="col-ratio">
          <?php echo $report_source_total[$i]['ratio']; ?>
        </div>
      </div>
    <?php } ?>
  </div>
</div>
<?php } else { ?>
<div id="top-sources-empty">
  <i class="fa fa-cogs"></i>
  <p>
    <?php echo $text_empty_sources; ?>
  </p>
</div>
<?php } ?>