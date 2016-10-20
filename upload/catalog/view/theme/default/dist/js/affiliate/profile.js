(function () {$('select[name="country_id"]').on('change', function() {
  $.ajax({
    url: 'index.php?route=affiliate/profile/country&country_id=' + this.value,
    dataType: 'json',
    beforeSend: function() {
      $('select[name="country_id"]').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
    },
    complete: function() {
      $('.fa-spin').remove();
    },
    success: function(json) {
      if (json['postcode_required'] == '1') {
        $('input[name="postcode"]').parent().parent().addClass('required');
      } else {
        $('input[name="postcode"]').parent().parent().removeClass('required');
      }
      html = '<option value=""><?php echo $text_select; ?></option>';

      if (json['zone'] && json['zone'] != '') {
        for (i = 0; i < json['zone'].length; i++) {
          html += '<option value="' + json['zone'][i]['zone_id'] + '"';

          if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
            html += ' selected="selected"';
          }

          html += '>' + json['zone'][i]['name'] + '</option>';
        }
      } else {
        html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
      }
      $('select[name="zone_id"]').html(html);
    },
    error: function(xhr, ajaxOptions, thrownError) {
      alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
  });
});
$('select[name="country_id"]').trigger('change');
$('input[name="payment"]').on('change', function() {
  $('.payment').hide();

  $('#payment-' + this.value).show();
});
$('input[name="payment"]:checked').trigger('change');})();