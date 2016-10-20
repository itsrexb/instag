/*$.payment.cards.push({
	type: 'hipercard',
	patterns: [38, 60],
	format: /(\d{1,4})(\d{1,4})?(\d{1,2})?(\d{1,3})? | (\d{1,4})(\d{1,4})?(\d{1,4})?(\d{1,4})? | (\d{1,4})(\d{1,4})?(\d{1,4})?(\d{1,7})?/,
	length: [13, 16, 19],
	cvcLength: [3],
	luhn: false
});*/

$.fn.validate = function(method) {
	if (typeof method === 'undefined') {
		if (this.attr('data-validation-method')) {
			method = this.data('validation-method');
		} else {
			method = 'default';
		}
	}

	switch(method)
	{
	case 'cpf':
		value = this.val().replace('-', '');
		value = value.replace(/\./g, '');

		// validating first digit
		add = 0;

		for (i = 0; i < 9; i++) {
			add += parseInt(value.charAt(i), 10) * (10 - i);
		}

		rev = 11 - (add % 11);

		if (rev == 10 || rev == 11) {
			rev = 0;
		}

		if (rev != parseInt(value.charAt(9), 10)) {
			return false;
		}

		// validating second digit
		add = 0;

		for (i = 0; i < 10; i++) {
			add += parseInt(value.charAt(i), 10) * (11 - i);
		}

		rev = 11 - (add % 11);

		if (rev == 10 || rev == 11) {
			rev = 0;
		}

		if (rev != parseInt(value.charAt(10), 10)) {
			return false;
		}
		break;
	case 'birthdate':
		// YYYY-MM-DD
		if (!/^\d{4}-\d{1,2}-\d{1,2}$/.test(this.val())) {
			return false;
		}

		// Parse the date parts to integers
		var parts = this.val().split('-'),
				year  = parseInt(parts[0], 10),
				month = parseInt(parts[1], 10),
				day   = parseInt(parts[2], 10);

		var date          = new Date(),
				current_year  = date.getFullYear(),
				current_month = date.getMonth() + 1,
				current_day   = date.getDate();

		// Check the ranges of month and year
		if (year < (current_year - 130) || year > current_year || month == 0 || month > 12) {
			return false;
		}

		// check to the day
		if (year == current_year) {
			if (month > current_month || (month == current_month && day > current_day)) {
				return false;
			}
		}

		var monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

		// Adjust for leap years
		if (year % 400 == 0 || (year % 100 != 0 && year % 4 == 0)) {
			monthLength[1] = 29;
		}

		// Check the range of the day
		return day > 0 && day <= monthLength[month - 1];
		break;
	default:
		if (!this.val()) {
			return false;
		}
	}

	return true;
}