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
    <div class="row">
      <div class="col-lg-6 col-md-12 col-sm-12 col-sx-12">
        <div class="col-lg-6 col-md-6 col-sm-6"><?php echo $order; ?></div>
        <div class="col-lg-6 col-md-6 col-sm-6"><?php echo $sale; ?></div>
        <div class="col-lg-6 col-md-6 col-sm-6"><?php echo $customer; ?></div>
        <div class="col-lg-6 col-md-6 col-sm-6"><?php echo $account; ?></div>
      </div>
      <div class="col-lg-6 col-md-12 col-sm-12 col-sx-12"><?php echo $chart_revenue; ?></div>
    </div>
    <div class="row">
      <div class="col-lg-6 col-md-12 col-sm-12 col-sx-12"><?php echo $chart; ?></div>
      <div class="col-lg-6 col-md-12 col-sm-12 col-sx-12"><?php echo $chart_customer; ?></div>
    </div>
    <div class="row">
      <div class="col-lg-3 col-md-12 col-sx-12 col-sm-12"><?php echo $activity; ?></div>
      <div class="col-lg-9 col-md-12 col-sm-12 col-sx-12"> <?php echo $recent; ?> </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
