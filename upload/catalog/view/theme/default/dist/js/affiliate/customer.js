(function () {jQuery('#button-filter').on('click', function() {
	var url  = 'index.php?route=affiliate/customer',
	$inputs  = jQuery('input[name]', '#filter-container'),
	$selects = jQuery('select[name]', '#filter-container');

	for (var i = 0, input; input = $inputs[i]; ++i) {
		var $input = $(input), value = $input.val();

		if (value) {
			url += '&' + $input.attr('name') + '=' + encodeURIComponent(value);
		}
	}

	for (var i = 0, select; select = $selects[i]; ++i) {
		var $select = $(select), value = $select.val();

		if (value != '*') {
			url += '&' + $select.attr('name') + '=' + encodeURIComponent(value);
		}
	}

	location = url;
});

jQuery('.date').datetimepicker({
	pickTime: false
});})();