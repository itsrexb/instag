<?php echo $header; ?>
<div class="purple-padding"></div>
<div class="boxed-container affiliate-page text-center" id="affiliate-customers">
  <div class="row">
    <div id="content" class="no-fixed">
      <?php echo $content_top; ?>
      <h1><?php echo $heading_title; ?></h1>
      <div id="filter-container" class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label class="control-label" for="input-date-added"><?php echo $entry_date_start; ?></label>
            <div class="input-group date">
              <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control">
              <span class="input-group-btn">
              <button type="button" class="btn gold-button"><i class="fa fa-calendar"></i></button>
              </span></div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label class="control-label" for="input-date-added"><?php echo $entry_date_end; ?></label>
            <div class="input-group date">
              <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control">
              <span class="input-group-btn">
              <button type="button" class="btn gold-button"><i class="fa fa-calendar"></i></button>
              </span></div>
          </div>
        </div>            
        <div class="col-sm-3">
          <div class="form-group">
            <label class="control-label" for="input-actie"><?php echo $entry_active; ?></label>
            <select name="filter_active" id="input-active" class="form-control">
              <option value="*"></option>
              <?php if ($filter_active) { ?>
              <option value="1" selected="selected"><?php echo $text_yes; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_yes; ?></option>
              <?php } ?>
              <?php if (!$filter_active && !is_null($filter_active)) { ?>
              <option value="0" selected="selected"><?php echo $text_no; ?></option>
              <?php } else { ?>
              <option value="0"><?php echo $text_no; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>            
        <div class="col-sm-3">
          <div class="form-group">
          <label class="control-label" for="input-status">&nbsp;</label><br />
            <button type="button" id="button-filter" class="btn gold-button pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <td class="text-left"><?php echo $column_date_added; ?></td>
              <td class="text-left"><?php echo $column_name; ?></td>
              <td class="text-left"><?php echo $column_total_commissions; ?></td>
              <td class="text-left"><?php echo $column_active; ?></td>
              <td class="text-right"><?php echo $column_actions; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php if ($customers) { ?>
            <?php foreach ($customers  as $customer) { ?>
            <tr>
              <td class="text-left"><?php echo $customer['date_added']; ?></td>
              <td class="text-left"><?php echo $customer['name']; ?></td>
              <td class="text-left"><?php echo $customer['total_commissions']; ?></td>
              <td class="text-left"><?php echo $customer['active']; ?></td>
              <td class="text-right"><a href="<?php echo $customer['href']; ?>" title="" class="btn btn-info" data-toggle="tooltip" data-original-title="<?php echo $text_view; ?>"><i class="fa fa-eye"></i></a></td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="text-center" colspan="4"><?php echo $text_empty_customers; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="text-right"><?php echo $pagination; ?></div>
      <div class="buttons clearfix">
        <div class="text-center">
          <a href="<?php echo $dashboard; ?>" class="btn btn-primary gold-button"><?php echo $button_back_to_dashboard; ?></a>
        </div>
      </div>
      <?php echo $content_bottom; ?>
    </div>
  </div>
</div>
<?php echo $footer; ?>