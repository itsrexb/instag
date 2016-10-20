<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-category" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-source_interest" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-data" data-toggle="tab"><?php echo $tab_data; ?></a></li>
            <li><a href="#tab-accounts" data-toggle="tab"><?php echo $tab_accounts; ?></a></li>
            <li><a href="#tab-tags" data-toggle="tab"><?php echo $tab_tags; ?></a></li>
            <li><a href="#tab-history" data-toggle="tab"><?php echo $tab_history; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <ul class="nav nav-tabs" id="language">
                <?php foreach ($languages as $language) { ?>
                <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>"> <?php echo $language['name']; ?></a></li>
                <?php } ?>
              </ul>
              <div class="tab-content">
                <?php foreach ($languages as $language) { ?>
                <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-name<?php echo $language['language_id']; ?>"><?php echo $entry_name; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="source_interest_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($source_interest_description[$language['language_id']]) ? $source_interest_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name<?php echo $language['language_id']; ?>" class="form-control">
                      <?php if (isset($error_name[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>"><?php echo $entry_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="source_interest_description[<?php echo $language['language_id']; ?>][description]" placeholder="<?php echo $entry_description; ?>" id="input-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($source_interest_description[$language['language_id']]) ? $source_interest_description[$language['language_id']]['description'] : ''; ?></textarea>
                    </div>
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="tab-pane" id="tab-data">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-parent"><?php echo $entry_parent; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="path" value="<?php echo $path; ?>" placeholder="<?php echo $entry_parent; ?>" id="input-parent" class="form-control">
                  <input type="hidden" name="parent_id" value="<?php echo $parent_id; ?>">
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
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_excluded_countries; ?>"><?php echo $entry_excluded_countries; ?></span></label>
                <div class="col-sm-10">
                  <div class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($countries as $country) { ?>
                    <div class="checkbox">
                      <label>
                        <?php if (in_array($country['country_id'], $source_interest_excluded_countries)) { ?>
                        <input type="checkbox" name="source_interest_excluded_countries[]" value="<?php echo $country['country_id']; ?>" checked="checked">
                        <?php echo $country['name']; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="source_interest_excluded_countries[]" value="<?php echo $country['country_id']; ?>">
                        <?php echo $country['name']; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-accounts">
              <div class="row">
                <div class="col-sm-2">
                  <ul class="nav nav-pills nav-stacked" id="account_countries">
                    <?php foreach ($source_interest_accounts as $source_interest_account) { ?>
                    <?php if ($source_interest_account['country_id']) { ?>
                    <li><a href="#tab-account-country<?php echo $source_interest_account['country_id']; ?>" data-toggle="tab"><i class="fa fa-minus-circle" data-country_id="<?php echo $source_interest_account['country_id']; ?>"></i> <?php echo $source_interest_account['name']; ?></a></li>
                    <?php } else { ?>
                    <li><a href="#tab-account-country<?php echo $source_interest_account['country_id']; ?>" data-toggle="tab"><?php echo $source_interest_account['name']; ?></a></li>
                    <?php } ?>
                    <?php } ?>
                    <li>
                      <input type="text" name="account_country" value="" placeholder="<?php echo $entry_country; ?>" id="input-account-country" class="form-control">
                    </li>
                  </ul>
                </div>
                <div class="col-sm-10">
                  <div class="tab-content">
                    <?php $accounts_row = 0; ?>
                    <?php foreach ($source_interest_accounts as $source_interest_account) { ?>
                    <div class="tab-pane" id="tab-account-country<?php echo $source_interest_account['country_id']; ?>">
                      <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                          <thead>
                            <tr>
                              <td><?php echo $entry_account; ?></td>
                              <td><?php echo $entry_quality; ?></td>
                              <td></td>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($source_interest_account['accounts'] as $account) { ?>
                            <tr>
                              <td>
                                <input type="text" name="source_interest_accounts[<?php echo $source_interest_account['country_id']; ?>][<?php echo $accounts_row; ?>][account]" value="<?php echo $account['account']; ?>" placeholder="<?php echo $entry_account; ?>" class="form-control">
                                <?php if (isset($error_account[$source_interest_account['country_id']][$accounts_row])) { ?>
                                <div class="text-danger"><?php echo $error_account[$source_interest_account['country_id']][$accounts_row]; ?></div>
                                <?php } ?>
                              </td>
                              <td>
                                <select name="source_interest_accounts[<?php echo $source_interest_account['country_id']; ?>][<?php echo $accounts_row; ?>][quality]" class="form-control">
                                  <?php foreach ($qualities as $key => $quality) { ?>
                                  <?php if ($key == $account['quality']) { ?>
                                  <option value="<?php echo $key; ?>" selected="selected"><?php echo $quality; ?></option>
                                  <?php } else { ?>
                                  <option value="<?php echo $key; ?>"><?php echo $quality; ?></option>
                                  <?php } ?>
                                  <?php } ?>
                                </select>
                              </td>
                              <td><button type="button" onclick="$(this).closest('tr').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                            </tr>
                            <?php $accounts_row++; ?>
                            <?php } ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="2"></td>
                              <td><button type="button" onclick="addAccount(<?php echo $source_interest_account['country_id']; ?>);" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-tags">
              <div class="row">
                <div class="col-sm-2">
                  <ul class="nav nav-pills nav-stacked" id="tag_countries">
                    <?php foreach ($source_interest_tags as $source_interest_tag) { ?>
                    <?php if ($source_interest_tag['country_id']) { ?>
                    <li><a href="#tab-tag-country<?php echo $source_interest_tag['country_id']; ?>" data-toggle="tab"><i class="fa fa-minus-circle" data-country_id="<?php echo $source_interest_tag['country_id']; ?>"></i> <?php echo $source_interest_tag['name']; ?></a></li>
                    <?php } else { ?>
                    <li><a href="#tab-tag-country<?php echo $source_interest_tag['country_id']; ?>" data-toggle="tab"><?php echo $source_interest_tag['name']; ?></a></li>
                    <?php } ?>
                    <?php } ?>
                    <li>
                      <input type="text" name="tag_country" value="" placeholder="<?php echo $entry_country; ?>" id="input-tag-country" class="form-control">
                    </li>
                  </ul>
                </div>
                <div class="col-sm-10">
                  <div class="tab-content">
                    <?php $tags_row = 0; ?>
                    <?php foreach ($source_interest_tags as $source_interest_tag) { ?>
                    <div class="tab-pane" id="tab-tag-country<?php echo $source_interest_tag['country_id']; ?>">
                      <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                          <thead>
                            <tr>
                              <td><?php echo $entry_tag; ?></td>
                              <td><?php echo $entry_quality; ?></td>
                              <td></td>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($source_interest_tag['tags'] as $tag) { ?>
                            <tr>
                              <td>
                                <input type="text" name="source_interest_tags[<?php echo $source_interest_tag['country_id']; ?>][<?php echo $tags_row; ?>][tag]" value="<?php echo $tag['tag']; ?>" placeholder="<?php echo $entry_tag; ?>" class="form-control">
                                <?php if (isset($error_tag[$source_interest_tag['country_id']][$tags_row])) { ?>
                                <div class="text-danger"><?php echo $error_tag[$source_interest_tag['country_id']][$tags_row]; ?></div>
                                <?php } ?>
                              </td>
                              <td>
                                <select name="source_interest_tags[<?php echo $source_interest_tag['country_id']; ?>][<?php echo $tags_row; ?>][quality]" class="form-control">
                                  <?php foreach ($qualities as $key => $quality) { ?>
                                  <?php if ($key == $tag['quality']) { ?>
                                  <option value="<?php echo $key; ?>" selected="selected"><?php echo $quality; ?></option>
                                  <?php } else { ?>
                                  <option value="<?php echo $key; ?>"><?php echo $quality; ?></option>
                                  <?php } ?>
                                  <?php } ?>
                                </select>
                              </td>
                              <td><button type="button" onclick="$(this).closest('tr').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                            </tr>
                            <?php $tags_row++; ?>
                            <?php } ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="2"></td>
                              <td><button type="button" onclick="addTag(<?php echo $source_interest_tag['country_id']; ?>);" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-history">
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover multiselect-checkbox">
                      <thead>
                      <tr>
                        <td class="text-left"><?php if ($sort == 'c.firstname') { ?>
                             <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_customer; ?></a>
                          <?php } else  { ?>
                            <a href="<?php echo $sort_customer; ?>"><?php echo $column_customer; ?></a>
                          <?php } ?></td>
                        <td class="text-left"><?php if ($sort == 'a.username') { ?>
                          <a href="<?php echo $sort_username; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_customer; ?></a>  <?php } else { ?>
                          <a href="<?php echo $sort_username; ?>"><?php echo $column_account; ?></a>
                          <?php } ?></td>
                        <td class="text-left"><?php if ($sort == 'a.date_added') { ?>
                          <a href="<?php echo $sort_date; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_customer; ?></a>  <?php } else { ?>
                          <a href="<?php echo $sort_date; ?>"><?php echo $column_date_added; ?></a>
                          <?php } ?></td>
                      </tr>
                      </thead>
                      <tbody>
                      <?php if ($histories) { ?>
                      <?php foreach ($histories as $history) { ?>
                      <tr>
                        <td class="text-left"><?php echo $history['customer']; ?></td>
                        <td class="text-left"><?php echo $history['username']; ?></td>
                        <td class="text-left"><?php echo $history['date_added']; ?></td>
                      </tr>
                      <?php } ?>
                      <?php } else { ?>
                      <tr>
                        <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
                      </tr>
                      <?php } ?>
                      </tbody>
                    </table>
                  </div>
            </form>
            <div class="row">
              <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
              <div class="col-sm-6 text-right"><?php echo $results; ?></div>
            </div>
          </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
<?php foreach ($languages as $language) { ?>
$('#input-description<?php echo $language['language_id']; ?>').summernote({
	height: 300
});
<?php } ?>

$('input[name="path"]').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/source_interest/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				json.unshift({
					source_interest_id: 0,
					name: '<?php echo $text_none; ?>'
				});

				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['source_interest_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name="path"]').val(item['label']);
		$('input[name="parent_id"]').val(item['value']);
	}
});

$('#language a:first').tab('show');

var accounts_row = <?php echo $accounts_row; ?>;

function addAccount(country_id) {
	html  = '<tr>';
	html += '  <td>';
	html += '    <input type="text" name="source_interest_accounts[' + country_id + '][' + accounts_row + '][account]" value="" placeholder="<?php echo $entry_account; ?>" class="form-control">';
	html += '  </td>';
	html += '  <td>';
	html += '    <select name="source_interest_accounts[' + country_id + '][' + accounts_row + '][quality]" class="form-control">';

	<?php foreach ($qualities as $key => $quality) { ?>
	html += '      <option value="<?php echo $key; ?>"><?php echo $quality; ?></option>';
	<?php } ?>

	html += '    </select>';
	html += '  </td>';
	html += '  <td><button type="button" onclick="$(this).closest(\'tr\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';

	$('#tab-account-country' + country_id + ' table tbody').append(html);

	accounts_row++;
}

$('input[name="account_country"]').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/source_interest/country_autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['country_id'],
					}
				}));
			}
		});
	},
	'select': function(item) {
		html  = '<div class="tab-pane" id="tab-account-country' + item['value'] + '">';
		html += '  <div class="table-responsive">';
		html += '    <table class="table table-striped table-bordered table-hover">';
		html += '      <thead>';
		html += '        <tr>';
		html += '          <td><?php echo $entry_account; ?></td>';
		html += '          <td><?php echo $entry_quality; ?></td>';
		html += '          <td></td>';
		html += '        </tr>';
		html += '      </thead>';
		html += '      <tbody></tbody>';
		html += '      <tfoot>';
		html += '        <tr>';
		html += '          <td colspan="2"></td>';
		html += '          <td><button type="button" onclick="addAccount(' + item['value'] + ');" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>';
		html += '        </tr>';
		html += '      </tfoot>';
		html += '    </table>';
		html += '  </div>';
		html += '</div>';

		$('#tab-accounts .tab-content').append(html);

		$('#account_countries > li:last-child').before('<li><a href="#tab-account-country' + item['value'] + '" data-toggle="tab"><i class="fa fa-minus-circle"></i> ' + item['label'] + '</li>');

		$('#account_countries a[href="#tab-account-country' + item['value'] + '"]').tab('show');
	}
});

$('#account_countries').on('click', '.fa-minus-circle', function() {
	var $this      = $(this),
			country_id = $this.data('country_id');

	$this.closest('li').remove();
	$('#tab-account-country' + country_id).remove();
	$('#account_countries a:first').tab('show');
});

var tags_row = <?php echo $tags_row; ?>;

function addTag(country_id) {
	html  = '<tr>';
	html += '  <td>';
	html += '    <input type="text" name="source_interest_tags[' + country_id + '][' + tags_row + '][tag]" value="" placeholder="<?php echo $entry_tag; ?>" class="form-control">';
	html += '  </td>';
	html += '  <td>';
	html += '    <select name="source_interest_tags[' + country_id + '][' + tags_row + '][quality]" class="form-control">';

	<?php foreach ($qualities as $key => $quality) { ?>
	html += '      <option value="<?php echo $key; ?>"><?php echo $quality; ?></option>';
	<?php } ?>

	html += '    </select>';
	html += '  </td>';
	html += '  <td><button type="button" onclick="$(this).closest(\'tr\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';

	$('#tab-tag-country' + country_id + ' table tbody').append(html);

	tags_row++;
}

$('input[name="tag_country"]').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/source_interest/country_autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['country_id'],
					}
				}));
			}
		});
	},
	'select': function(item) {
		html  = '<div class="tab-pane" id="tab-tag-country' + item['value'] + '">';
		html += '  <div class="table-responsive">';
		html += '    <table class="table table-striped table-bordered table-hover">';
		html += '      <thead>';
		html += '        <tr>';
		html += '          <td><?php echo $entry_tag; ?></td>';
		html += '          <td><?php echo $entry_quality; ?></td>';
		html += '          <td></td>';
		html += '        </tr>';
		html += '      </thead>';
		html += '      <tbody></tbody>';
		html += '      <tfoot>';
		html += '        <tr>';
		html += '          <td colspan="2"></td>';
		html += '          <td><button type="button" onclick="addTag(' + item['value'] + ');" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>';
		html += '        </tr>';
		html += '      </tfoot>';
		html += '    </table>';
		html += '  </div>';
		html += '</div>';

		$('#tab-tags .tab-content').append(html);

		$('#tag_countries > li:last-child').before('<li><a href="#tab-tag-country' + item['value'] + '" data-toggle="tab"><i class="fa fa-minus-circle"></i> ' + item['label'] + '</li>');

		$('#tag_countries a[href="#tab-tag-country' + item['value'] + '"]').tab('show');
	}
});

$('#tag_countries').on('click', '.fa-minus-circle', function() {
	var $this      = $(this),
			country_id = $this.data('country_id');

	$this.closest('li').remove();
	$('#tab-tag-country' + country_id).remove();
	$('#tag_countries a:first').tab('show');
});

$('#account_countries a:first').tab('show');
$('#tag_countries a:first').tab('show');

</script>
<?php echo $footer; ?>