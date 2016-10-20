<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $href_cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="row">
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-shopping-cart"></i> <?php echo $text_order_details; ?></h3>
          </div>
          <table class="table">
            <tbody>
              <tr>
                <td style="width: 1%;"><button data-toggle="tooltip" title="<?php echo $text_store; ?>" class="btn btn-info btn-xs"><i class="fa fa-shopping-cart fa-fw"></i></button></td>
                <td><a href="<?php echo $store_url; ?>" target="_blank"><?php echo $store_name; ?></a></td>
              </tr>
              <tr>
                <td><button data-toggle="tooltip" title="<?php echo $text_date_added; ?>" class="btn btn-info btn-xs"><i class="fa fa-calendar fa-fw"></i></button></td>
                <td><?php echo $date_added; ?></td>
              </tr>
              <tr>
                <td><button data-toggle="tooltip" title="<?php echo $text_payment_method; ?>" class="btn btn-info btn-xs"><i class="fa fa-credit-card fa-fw"></i></button></td>
                <td><?php echo $payment_method; ?></td>
              </tr>
              <tr>
                <td><button data-toggle="tooltip" title="<?php echo $text_status; ?>" class="btn btn-info btn-xs"><i class="fa fa-info fa-fw"></i></button></td>
                <td><?php echo $status; ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-user"></i> <?php echo $text_customer_details; ?></h3>
          </div>
          <table class="table">
            <tr>
              <td style="width: 1%;"><button data-toggle="tooltip" title="<?php echo $text_customer; ?>" class="btn btn-info btn-xs"><i class="fa fa-user fa-fw"></i></button></td>
              <td><?php if ($customer_href) { ?>
                <a href="<?php echo $customer_href; ?>"><?php echo $customer_name; ?></a>
                <?php } else { ?>
                <?php echo $customer_name; ?>
                <?php } ?></td>
            </tr>
            <?php if ($customer_group) { ?>
            <tr>
              <td><button data-toggle="tooltip" title="<?php echo $text_customer_group; ?>" class="btn btn-info btn-xs"><i class="fa fa-group fa-fw"></i></button></td>
              <td><?php echo $customer_group; ?></td>
            </tr>
            <?php } ?>
            <?php if ($customer_email) { ?>
            <tr>
              <td><button data-toggle="tooltip" title="<?php echo $text_email; ?>" class="btn btn-info btn-xs"><i class="fa fa-envelope-o fa-fw"></i></button></td>
              <td><a href="mailto:<?php echo $customer_email; ?>"><?php echo $customer_email; ?></a></td>
            </tr>
            <?php } ?>
            <?php if ($customer_telephone) { ?>
            <tr>
              <td><button data-toggle="tooltip" title="<?php echo $text_telephone; ?>" class="btn btn-info btn-xs"><i class="fa fa-phone fa-fw"></i></button></td>
              <td><?php echo $customer_telephone; ?></td>
            </tr>
            <?php } ?>
          </table>
        </div>
      </div>
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-user"></i> <?php echo $text_recurring_details; ?></h3>
          </div>
          <table class="table">
            <?php if ($account_username) { ?>
            <tr>
              <td style="width: 1%;"><button data-toggle="tooltip" title="<?php echo ${'text_' . $account_type . '_account'}; ?>" class="btn btn-info btn-xs"><i class="fa fa-<?php echo $account_type; ?> fa-fw"></i></button></td>
              <td><a href="<?php echo $account_href; ?>"><?php echo $account_username; ?></a></td>
            </tr>
            <?php } ?>
            <tr>
              <td style="width: 1%;"><button data-toggle="tooltip" title="<?php echo $text_product; ?>" class="btn btn-info btn-xs"><i class="fa fa-tag fa-fw"></i></button></td>
              <td><?php echo $product_name; ?></td>
            </tr>
            <tr>
              <td><button data-toggle="tooltip" title="<?php echo $text_frequency; ?>" class="btn btn-info btn-xs"><i class="fa fa-refresh fa-fw"></i></button></td>
              <td><?php echo $recurring_price; ?> <?php echo $text_every; ?> <?php echo $recurring_cycle; ?> <?php echo $recurring_frequency; ?>, <?php echo $recurring_duration; ?></td>
            </tr>
            <?php if ($date_next_recurring) { ?>
            <tr>
              <td><button data-toggle="tooltip" title="<?php echo $text_date_next_recurring; ?>" class="btn btn-info btn-xs"><i class="fa fa-calendar fa-fw"></i></button></td>
              <td><?php echo $date_next_recurring; ?></td>
            </tr>
            <?php } ?>
            <?php if ($coupon_code) { ?>
            <tr>
              <td><button data-toggle="tooltip" title="<?php echo $text_coupon; ?>" class="btn btn-info btn-xs"><i class="fa fa-star fa-fw"></i></button></td>
              <td><?php echo $coupon_code; ?> <?php if ($coupon_remaining) { ?>(<?php echo $coupon_remaining; ?>)<?php } ?></td>
            </tr>
            <?php } ?>
          </table>
        </div>
      </div>
    </div>
    <?php if ($trial_status) { ?>
    <div class="row">
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-user"></i> <?php echo $text_trial_details; ?></h3>
          </div>
          <table class="table">
            <tr>
              <td style="width: 1%;"><button data-toggle="tooltip" title="<?php echo $text_price; ?>" class="btn btn-info btn-xs"><i class="fa fa-money fa-fw"></i></button></td>
              <td><?php echo $trial_price; ?></td>
            </tr>
            <tr>
              <td><button data-toggle="tooltip" title="<?php echo $text_frequency; ?>" class="btn btn-info btn-xs"><i class="fa fa-refresh fa-fw"></i></button></td>
              <td><?php echo $trial_cycle; ?> <?php echo $trial_frequency; ?></td>
            </tr>
            <tr>
              <td><button data-toggle="tooltip" title="<?php echo $text_duration; ?>" class="btn btn-info btn-xs"><i class="fa fa-clock-o fa-fw"></i></button></td>
              <td><?php echo $trial_duration; ?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <?php } ?>
    <?php if ($buttons) { ?>
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="pull-right">
          <?php foreach ($buttons as $button) { ?>
          <a href="<?php echo $button['href']; ?>" class="btn btn-danger"><?php echo $button['text']; ?></a>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_transactions; ?></h3>
      </div>
      <div class="panel-body">
      	<div id="transactions"></div>
      </div>
    </div>
  </div>
</div>
<script>
$('#transactions').on('click', '.pagination a', function(e) {
	e.preventDefault();

	$('#transactions').load(this.href);
});

$('#transactions').load('index.php?route=sale/recurring_order/transactions&token=<?php echo $token; ?>&recurring_order_id=<?php echo $recurring_order_id; ?>');
</script>
<?php echo $footer; ?>