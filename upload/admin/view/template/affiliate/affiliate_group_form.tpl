<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-affiliate-group" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-affiliate-group" class="form-horizontal">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group required">
                <label class="col-sm-2 control-label"><?php echo $entry_name; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>"></span>
                    <input type="text" name="affiliate_group_descriptions[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($affiliate_group_descriptions[$language['language_id']]) ? $affiliate_group_descriptions[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" class="form-control">
                  </div>
                  <?php if (isset($error_name[$language['language_id']])) { ?>
                  <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
                  <?php } ?>
                  <?php } ?>
                </div>
              </div>
              <?php foreach ($languages as $language) { ?>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>"><?php echo $entry_description; ?></label>
                <div class="col-sm-10">
                  <div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>"></span>
                    <textarea name="affiliate_group_descriptions[<?php echo $language['language_id']; ?>][description]" rows="5" placeholder="<?php echo $entry_description; ?>" id="input-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($affiliate_group_descriptions[$language['language_id']]) ? $affiliate_group_descriptions[$language['language_id']]['description'] : ''; ?></textarea>
                  </div>
                </div>
              </div>
              <?php } ?>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title"><?php echo $entry_commission_levels; ?></h3>
            </div>
            <div class="panel-body">
              <div class="col-sm-12">
                <?php if (isset($affiliate_group_commissions[0])) { ?>
                <?php end($affiliate_group_commissions[0]); $last_level = key($affiliate_group_commissions[0]); ?>
                <?php foreach ($affiliate_group_commissions[0] as $level => $level_commission) { ?>
                <div class="form-group commission-level">
                  <label class="col-sm-1 control-label" for="input-commission-<?php echo $level; ?>"><?php echo $level; ?><span <?php echo ($level != $last_level ? 'style="display: none;"' : ''); ?>>+</span></label>
                  <div class="col-sm-1">
                    <input type="text" name="affiliate_group_commissions[0][<?php echo $level; ?>]" value="<?php echo $level_commission; ?>" id="input-commission-<?php echo $level; ?>" class="form-control">
                  </div>
                  <div class="col-sm-1">
                    <a href="#delete-commission-level" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" <?php echo ($level != $last_level ? 'style="display: none;"' : ''); ?>><i class="fa fa-trash-o"></i></a>
                  </div>
                </div>
                <?php } ?>
                <?php } ?>
                <div class="form-group">
                  <div class="col-sm-1"></div>
                  <div class="col-sm-11">
                    <a href="#add-commission-level" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $button_add_level; ?></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php foreach ($affiliate_groups as $affiliate_group) { ?>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title"><?php echo $affiliate_group['name']; ?> <?php echo $entry_commission_overrides; ?></h3>
            </div>
            <div class="panel-body">
              <div class="col-sm-12">
                <?php if (isset($affiliate_group_commissions[$affiliate_group['affiliate_group_id']])) { ?>
                <?php end($affiliate_group_commissions[$affiliate_group['affiliate_group_id']]); $last_level = key($affiliate_group_commissions[$affiliate_group['affiliate_group_id']]); ?>
                <?php foreach ($affiliate_group_commissions[$affiliate_group['affiliate_group_id']] as $level => $level_commission) { ?>
                <div class="form-group commission-override">
                  <label class="col-sm-1 control-label" for="input-commission-<?php echo $level; ?>"><?php echo $level; ?></label>
                  <div class="col-sm-1">
                    <input type="text" name="affiliate_group_commissions[<?php echo $affiliate_group['affiliate_group_id']; ?>][<?php echo $level; ?>]" value="<?php echo $level_commission; ?>" id="input-commission-<?php echo $level; ?>" class="form-control">
                  </div>
                  <div class="col-sm-1">
                    <a href="#delete-commission-override" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" <?php echo ($level != $last_level ? 'style="display: none;"' : ''); ?>><i class="fa fa-trash-o"></i></a>
                  </div>
                </div>
                <?php } ?>
                <?php } ?>
                <div class="form-group">
                  <div class="col-sm-1"></div>
                  <div class="col-sm-11">
                    <a href="#add-commission-override" data-affiliate_group_id="<?php echo $affiliate_group['affiliate_group_id']; ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $button_add_level; ?></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php } ?>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
$('a[href="#add-commission-level"]').on('click', function() {
	var $this      = $(this),
			$container = $this.closest('div.panel-body'),
			level      = $('.commission-level', $container).length,
			html_data  = [];

	html_data.push('<div class="form-group commission-level">');
	html_data.push('  <label class="col-sm-1 control-label" for="input-commission-' + level + '">' + level + '<span>+</span></label>');
	html_data.push('  <div class="col-sm-1">');
	html_data.push('    <input type="text" name="affiliate_group_commissions[0][' + level + ']" value="0" id="input-commission-' + level + '" class="form-control">');
	html_data.push('  </div>');
	html_data.push('  <div class="col-sm-1">');
	html_data.push('    <a href="#delete-commission-level" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>');
	html_data.push('  </div>');
	html_data.push('</div>');

	$('.commission-level label span', $container).hide();
	$('.commission-level a[href="#delete-commission-level"]', $container).hide();
	$this.closest('.form-group').before(html_data.join(''));

	return false;
});

$(document).on('click', 'a[href="#delete-commission-level"]', function() {
	// TODO
	var $this = $(this), $container = $this.closest('div.panel-body');

	$this.closest('.commission-level').remove();
	$('.commission-level label span', $container).last().show();
	$('.commission-level a[href="#delete-commission-level"]', $container).last().show();

	return false;
});

$('a[href="#add-commission-override"]').on('click', function() {
	var $this              = $(this),
			$container         = $this.closest('div.panel-body'),
			affiliate_group_id = $this.data('affiliate_group_id'),
			level              = $('.commission-override', $container).length + 1,
			html_data          = [];

	html_data.push('<div class="form-group commission-override">');
	html_data.push('  <label class="col-sm-1 control-label" for="input-commission-' + level + '">' + level + '</label>');
	html_data.push('  <div class="col-sm-1">');
	html_data.push('    <input type="text" name="affiliate_group_commissions[' + affiliate_group_id + '][' + level + ']" value="0" id="input-commission-' + level + '" class="form-control">');
	html_data.push('  </div>');
	html_data.push('  <div class="col-sm-1">');
	html_data.push('    <a href="#delete-commission-override" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>');
	html_data.push('  </div>');
	html_data.push('</div>');

	$('.commission-override a[href="#delete-commission-override"]', $container).hide();
	$this.closest('.form-group').before(html_data.join(''));

	return false;
});

$(document).on('click', 'a[href="#delete-commission-override"]', function() {
	// TODO
	var $this = $(this), $container = $this.closest('div.panel-body');

	$this.closest('.commission-override').remove();
	$('.commission-override a[href="#delete-commission-override"]', $container).last().show();

	return false;
});
</script>
<?php echo $footer; ?>