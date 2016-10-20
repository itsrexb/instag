// Sort the custom fields
jQuery('.form-group[data-sort]').detach().each(function() {
	if (jQuery(this).attr('data-sort') >= 0 && jQuery(this).attr('data-sort') <= jQuery('.form-group').length) {
		jQuery('.form-group').eq(jQuery(this).attr('data-sort')).before(this);
	} 

	if (jQuery(this).attr('data-sort') > jQuery('.form-group').length) {
		jQuery('.form-group:last').after(this);
	}

	if (jQuery(this).attr('data-sort') < -jQuery('.form-group').length) {
		jQuery('.form-group:first').before(this);
	}
});

jQuery('.date').datetimepicker({
	pickTime: false
});

jQuery('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});

jQuery('.time').datetimepicker({
	pickDate: false
});