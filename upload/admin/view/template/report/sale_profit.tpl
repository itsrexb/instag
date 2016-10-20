<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-month"><?php echo $text_month; ?></label>
                <select name="filter_month" id="input-month" class="form-control">
                  <?php if(!$filter_month){ ?>
                      <option value="" selected="selected"><?php echo $text_all_month; ?></option>
                  <?php }else{ ?>
                         <option value=""><?php echo $text_all_month; ?></option>
                   <?php } ?>
                  <?php foreach ($months as $month) { ?>
                    <?php if ($month == $filter_month) { ?>
                        <option value="<?php echo $month; ?>" selected="selected"><?php echo $month; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-year"><?php echo $text_year; ?></label>
                <select name="filter_year" id="input-year" class="form-control">
                  <option value="<?php echo date('Y'); ?>"  selected="selected"><?php echo date('Y'); ?></option>
                  <?php foreach ($years as $key=>$year) { ?>
                  <?php if ($year == $filter_year) { ?>
                  <option value="<?php echo $year; ?>" selected="selected"><?php echo $year; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <td class="text-left"><?php echo $column_month; ?></td>
                <td class="text-left"><?php echo $column_orders; ?></td>
                <td class="text-right"><?php echo $column_products; ?></td>
                <td class="text-right"><?php echo $column_gross; ?></td>
                <td class="text-right"><?php echo $column_acquisition; ?></td>
                <td class="text-right"><?php echo $column_discount; ?></td>
                <td class="text-right"><?php echo $column_credit; ?></td>
                <td class="text-right"><?php echo $column_net; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if ($orders) { ?>
                      <?php foreach ($orders as $order) { ?>
                                <tr>
                                  <td class="text-right"><?php echo $order['month']; ?></td>
                                  <td class="text-right"><?php echo $order['orders']; ?></td>
                                  <td class="text-right"><?php echo $order['products']; ?></td>
                                  <td class="text-right"><?php echo $order['gross']; ?></td>
                                  <td class="text-right"><?php echo $order['acquisition_cost']; ?></td>
                                  <td class="text-right"><?php echo $order['discount']; ?></td>
                                  <td class="text-right"><?php echo $order['credit']; ?></td>
                                  <td class="text-right"><?php echo $order['net']; ?></td>
                                </tr>
                      <?php 
                      } 
                      if(!$filter_month){
                        ?>
                          <tr>
                                  <td class="text-right"  colspan="3"><?php echo $text_total; ?></td>
                                  <td class="text-right"><?php echo $gross_total; ?></td>
                                  <td class="text-right"><?php echo $acquisition_cost_total; ?></td>
                                  <td class="text-right"><?php echo $discount_total; ?></td>
                                  <td class="text-right"><?php echo $credit_total; ?></td>
                                  <td class="text-right"><?php echo $net_total; ?></td>
                          </tr>        
                        <?php
                      }
                      ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
  $('#button-filter').on('click', function() {
	url = 'index.php?route=report/sale_profit&token=<?php echo $token; ?>';
	
	var filter_year = $('select[name=\'filter_year\'] option:selected').val();
	
	if (filter_year) {
		url += '&filter_year=' + encodeURIComponent(filter_year);
	}

	var filter_month = $('select[name=\'filter_month\'] option:selected').val();
	
	if (filter_month) {
		url += '&filter_month=' + encodeURIComponent(filter_month);
	}
	location = url;
});
//--></script> 
<?php echo $footer; pr($filter_month); ?>