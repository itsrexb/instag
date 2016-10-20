<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-coupon" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-coupon" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <?php if ($coupon_id) { ?>
            <li><a href="#tab-history" data-toggle="tab"><?php echo $tab_history; ?></a></li>
            <?php } ?>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control">
                  <?php if ($error_name) { ?>
                  <div class="text-danger"><?php echo $error_name; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-code"><span data-toggle="tooltip" title="<?php echo $help_code; ?>"><?php echo $entry_code; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="code" value="<?php echo $code; ?>" placeholder="<?php echo $entry_code; ?>" id="input-code" class="form-control">
                  <?php if ($error_code) { ?>
                  <div class="text-danger"><?php echo $error_code; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-type"><span data-toggle="tooltip" title="<?php echo $help_type; ?>"><?php echo $entry_type; ?></span></label>
                <div class="col-sm-10">
                  <select name="type" id="input-type" class="form-control">
                    <?php if ($type == 'P') { ?>
                    <option value="P" selected="selected"><?php echo $text_percent; ?></option>
                    <?php } else { ?>
                    <option value="P"><?php echo $text_percent; ?></option>
                    <?php } ?>
                    <?php if ($type == 'F') { ?>
                    <option value="F" selected="selected"><?php echo $text_amount; ?></option>
                    <?php } else { ?>
                    <option value="F"><?php echo $text_amount; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-discount"><?php echo $entry_discount; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="discount" value="<?php echo $discount; ?>" placeholder="<?php echo $entry_discount; ?>" id="input-discount" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="total" value="<?php echo $total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_recurring; ?>"><?php echo $entry_recurring; ?></span></label>
                <div class="col-sm-10">
                  <label class="radio-inline">
                    <?php if ($recurring) { ?>
                    <input type="radio" name="recurring" value="1" checked="checked">
                    <?php echo $text_yes; ?>
                    <?php } else { ?>
                    <input type="radio" name="recurring" value="1">
                    <?php echo $text_yes; ?>
                    <?php } ?>
                  </label>
                  <label class="radio-inline">
                    <?php if (!$recurring) { ?>
                    <input type="radio" name="recurring" value="0" checked="checked">
                    <?php echo $text_no; ?>
                    <?php } else { ?>
                    <input type="radio" name="recurring" value="0">
                    <?php echo $text_no; ?>
                    <?php } ?>
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-recurring_limit"><span data-toggle="tooltip" title="<?php echo $help_recurring_limit; ?>"><?php echo $entry_recurring_limit; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="recurring_limit" value="<?php echo $recurring_limit; ?>" placeholder="<?php echo $entry_recurring_limit; ?>" id="input-recurring_limit" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-product"><span data-toggle="tooltip" title="<?php echo $help_product; ?>"><?php echo $entry_product; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="product" value="" placeholder="<?php echo $entry_product; ?>" id="input-product" class="form-control">
                  <div id="coupon-product" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($coupon_product as $coupon_product) { ?>
                    <div id="coupon-product<?php echo $coupon_product['product_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $coupon_product['name']; ?>
                      <input type="hidden" name="coupon_product[]" value="<?php echo $coupon_product['product_id']; ?>">
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div> 
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $help_category; ?>"><?php echo $entry_category; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control">
                  <div id="coupon-category" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($coupon_category as $coupon_category) { ?>
                    <div id="coupon-category<?php echo $coupon_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $coupon_category['name']; ?>
                      <input type="hidden" name="coupon_category[]" value="<?php echo $coupon_category['category_id']; ?>">
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-affiliate"><span data-toggle="tooltip" title="<?php echo $help_affiliate; ?>"><?php echo $entry_affiliate; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="affiliate" value="<?php echo $affiliate; ?>" placeholder="<?php echo $entry_affiliate; ?>" id="input-affiliate" class="form-control">
                  <input type="hidden" name="affiliate_id" value="<?php echo $affiliate_id; ?>">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-affiliate_fee"><span data-toggle="tooltip" title="<?php echo $help_affiliate_fee; ?>"><?php echo $entry_affiliate_fee; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="affiliate_fee" value="<?php echo $affiliate_fee; ?>" placeholder="<?php echo $entry_affiliate_fee; ?>" id="input-affiliate_fee" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-date-start"><?php echo $entry_date_start; ?></label>
                <div class="col-sm-3">
                  <div class="input-group date">
                    <input type="text" name="date_start" value="<?php echo $date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control">
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span></div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-date-end"><?php echo $entry_date_end; ?></label>
                <div class="col-sm-3">
                  <div class="input-group date">
                    <input type="text" name="date_end" value="<?php echo $date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control">
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span></div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-uses-total"><span data-toggle="tooltip" title="<?php echo $help_uses_total; ?>"><?php echo $entry_uses_total; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="uses_total" value="<?php echo $uses_total; ?>" placeholder="<?php echo $entry_uses_total; ?>" id="input-uses-total" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-uses-customer"><span data-toggle="tooltip" title="<?php echo $help_uses_customer; ?>"><?php echo $entry_uses_customer; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="uses_customer" value="<?php echo $uses_customer; ?>" placeholder="<?php echo $entry_uses_customer; ?>" id="input-uses-customer" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="status" id="input-status" class="form-control">
                    <?php if ($status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <?php if ($coupon_id) { ?>
            <div class="tab-pane" id="tab-history">
              <div id="history"></div>
            </div>
            <?php } ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
$('input[name="product"]').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name="product"]').val('');
		
		$('#coupon-product' + item['value']).remove();
		
		$('#coupon-product').append('<div id="coupon-product' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="coupon_product[]" value="' + item['value'] + '"></div>');	
	}
});

$('#coupon-product').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

// Category
$('input[name="category"]').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['category_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name="category"]').val('');
		
		$('#coupon-category' + item['value']).remove();
		
		$('#coupon-category').append('<div id="coupon-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="coupon_category[]" value="' + item['value'] + '"></div>');
	}	
});

$('#coupon-category').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

// Affiliate
$('#input-affiliate').autocomplete({
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=affiliate/affiliate/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['affiliate_id']
          }
        }));
      }
    });
  },
  select: function(item) {
    $('input[name="affiliate"]').val(item['label']);
    $('input[name="affiliate_id"]').val(item['value']);
  }
});

$('#input-affiliate').on('keyup', function() {
	if ($(this).val() === '') {
		$('input[name="affiliate_id"]').val(0);
	}
});

$('.date').datetimepicker({
	pickTime: false
});
</script>

<?php if ($coupon_id) { ?>
<script>
$('#history').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();
	
	$('#history').load(this.href);
});			

$('#history').load('index.php?route=marketing/coupon/history&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');
</script>
<?php } ?>

<?php echo $footer; ?>