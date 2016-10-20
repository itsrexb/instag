<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-url-alias" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-url-alias" class="form-horizontal">
          <table id="url-alias" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left"><?php echo $entry_route; ?></td>
                <td class="text-left"><?php echo $entry_keyword; ?></td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              <?php $url_alias_row = 0; ?>
              <?php foreach ($url_aliases as $url_alias) { ?>
              <tr id="url_alias-row<?php echo $url_alias_row; ?>">
                <td class="text-left"><input type="text" name="url_alias[<?php echo $url_alias_row; ?>][route]" value="<?php echo $url_alias['route']; ?>" placeholder="<?php echo $entry_route; ?>" class="form-control" /></td>
                <td class="text-left"><input type="text" name="url_alias[<?php echo $url_alias_row; ?>][keyword]" value="<?php echo $url_alias['keyword']; ?>" placeholder="<?php echo $entry_keyword; ?>" class="form-control" /></td>
                <td class="text-left"><a href="#remove" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></a></td>
              </tr>
              <?php $url_alias_row++; ?>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="2"></td>
                <td class="text-left"><a href="#add" data-toggle="tooltip" title="<?php echo $button_add_url_alias; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></a></td>
              </tr>
            </tfoot>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
var url_alias_row = <?php echo $url_alias_row; ?>;

$('a[href="#add"]').on('click', function() {
  var html = '';

  html += '<tr id="url_alias-row' + url_alias_row + '">';
  html += '  <td class="text-left"><input type="text" name="url_alias[' + url_alias_row + '][route]" value="" placeholder="<?php echo $entry_route; ?>" class="form-control" /></td>';
  html += '  <td class="text-left"><input type="text" name="url_alias[' + url_alias_row + '][keyword]" value="" placeholder="<?php echo $entry_keyword; ?>" class="form-control" /></td>';
  html += '  <td class="text-left"><a href="#remove" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></a></td>';
  html += '</tr>';

  $('#url-alias tbody').append(html);

  url_alias_row++;

  return false;
});

$('#url-alias tbody').on('click', 'tr a[href="#remove"]', function() {
  $(this).closest('tr').remove();

  return false;
});
//--></script>
<?php echo $footer; ?>
