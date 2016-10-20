$(document).ready(function() {
  // chosen
  var config = {
    '.chosen-select': {
      'disable_search_threshold': 20
    }
  }

  // Chosen touch support.
  if ($('.chosen-container').length > 0) {
    $('.chosen-container').on('touchstart', function(e){
      e.stopPropagation(); e.preventDefault();
      // Trigger the mousedown event.
      $(this).trigger('mousedown');
    });
  }

  for (var selector in config) {
  	if ($(selector).length > 0) {
      $(selector).chosen(config[selector]);
    }
  }

  // Highlight any found errors
  $('.text-danger').each(function() {
    var element = $(this).parent().parent();

    if (element.hasClass('form-group')) {
      element.addClass('has-error');
    }
  });

  // Currency
  $('#currency .currency-select').on('click', function(e) {
    e.preventDefault();

    $('#currency input[name=\'code\']').attr('value', $(this).attr('name'));

    $('#currency').submit();
  });

  // Language
  $('#language a').on('click', function(e) {
    e.preventDefault();

    $('#language input[name=\'code\']').attr('value', $(this).attr('href'));

    $('#language').submit();
  });

  // Menu
  $('#menu .dropdown-menu').each(function() {
    var menu = $('#menu').offset();
    var dropdown = $(this).parent().offset();

    var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());

    if (i > 0) {
      $(this).css('margin-left', '-' + (i + 5) + 'px');
    }
  });

  // Checkout
  $(document).on('keydown', '#collapse-checkout-option input[name=\'email\'], #collapse-checkout-option input[name=\'password\']', function(e) {
    if (e.keyCode == 13) {
      $('#collapse-checkout-option #button-login').trigger('click');
    }
  });
});

// Voucher add remove functions
var voucher = {
  'add': function() {

  },
  'remove': function(key) {
    $.ajax({
      url: 'index.php?route=checkout/cart/remove',
      type: 'post',
      data: 'key=' + key,
      dataType: 'json',
      beforeSend: function() {
        $('#cart > button').button('loading');
      },
      complete: function() {
        $('#cart > button').button('reset');
      },
      success: function(json) {
        if (json.redirect) {
          window.location = data.redirect;
        };
        // Need to set timeout otherwise it wont update the total
        setTimeout(function () {
          $('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
        }, 100);

        if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
          location = 'index.php?route=checkout/cart';
        } else {
          $('#cart > ul').load('index.php?route=common/cart/info ul li');
        }
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  }
}

/* Agree to Terms */
$(document).delegate('.agree', 'click', function(e) {
  e.preventDefault();

  $('#modal-agree').remove();

  var element = this;

  $.ajax({
    url: $(element).attr('href'),
    type: 'get',
    dataType: 'html',
    success: function(data) {
      if (data.redirect) {
        window.location = data.redirect;
      };
      html  = '<div id="modal-agree" class="modal">';
      html += '  <div class="modal-dialog">';
      html += '    <div class="modal-content">';
      html += '      <div class="modal-header">';
      html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
      html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
      html += '      </div>';
      html += '      <div class="modal-body">' + data + '</div>';
      html += '    </div';
      html += '  </div>';
      html += '</div>';

      $('body').append(html);

      $('#modal-agree').modal('show');
    }
  });
});

// Autocomplete */
(function($) {
  $.fn.autocomplete = function(option) {
    return this.each(function() {
      this.timer = null;
      this.items = new Array();

      $.extend(this, option);

      $(this).attr('autocomplete', 'off');

      // Focus
      $(this).on('focus', function() {
        this.request();
      });

      // Blur
      $(this).on('blur', function() {
        setTimeout(function(object) {
          object.hide();
        }, 200, this);
      });

      // Keydown
      $(this).on('keydown', function(event) {
        switch(event.keyCode) {
          case 27: // escape
            this.hide();
            break;
          default:
            this.request();
            break;
        }
      });

      // Click
      this.click = function(event) {
        event.preventDefault();

        value = $(event.target).parent().attr('data-value');

        if (value && this.items[value]) {
          this.select(this.items[value]);
        }
      }

      // Show
      this.show = function() {
        var pos = $(this).position();

        $(this).siblings('ul.dropdown-menu').css({
          top: pos.top + $(this).outerHeight(),
          left: pos.left
        });

        $(this).siblings('ul.dropdown-menu').show();
      }

      // Hide
      this.hide = function() {
        $(this).siblings('ul.dropdown-menu').hide();
      }

      // Request
      this.request = function() {
        clearTimeout(this.timer);

        this.timer = setTimeout(function(object) {
          object.source($(object).val(), $.proxy(object.response, object));
        }, 200, this);
      }

      // Response
      this.response = function(json) {
        html = '';

        if (json.length) {
          for (i = 0; i < json.length; i++) {
            this.items[json[i]['value']] = json[i];
          }

          for (i = 0; i < json.length; i++) {
            if (!json[i]['category']) {
              html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
            }
          }

          // Get all the ones with a categories
          var category = new Array();

          for (i = 0; i < json.length; i++) {
            if (json[i]['category']) {
              if (!category[json[i]['category']]) {
                category[json[i]['category']] = new Array();
                category[json[i]['category']]['name'] = json[i]['category'];
                category[json[i]['category']]['item'] = new Array();
              }

              category[json[i]['category']]['item'].push(json[i]);
            }
          }

          for (i in category) {
            html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

            for (j = 0; j < category[i]['item'].length; j++) {
              html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
            }
          }
        }

        if (html) {
          this.show();
        } else {
          this.hide();
        }

        $(this).siblings('ul.dropdown-menu').html(html);
      }

      $(this).after('<ul class="dropdown-menu"></ul>');
      $(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

    });
  }
})(window.jQuery);

// Loader
  var cSpeed=15;
  var cWidth=51;
  var cHeight=17;
  var cTotalFrames=60;
  var cFrameWidth=51;
  var cImageSrc='catalog/view/theme/default/image/sprites.png';
  
  var cImageTimeout=false;
  var cIndex=0;
  var cXpos=0;
  var cPreloaderTimeout=false;
  var SECONDS_BETWEEN_FRAMES=0;
  
  function startAnimation(){
    
    document.getElementById('loaderImage').style.backgroundImage='url('+cImageSrc+')';
    document.getElementById('loaderImage').style.width=cWidth+'px';
    document.getElementById('loaderImage').style.height=cHeight+'px';
    
    //FPS = Math.round(100/(maxSpeed+2-speed));
    FPS = Math.round(100/cSpeed);
    SECONDS_BETWEEN_FRAMES = 1 / FPS;
    
    cPreloaderTimeout=setTimeout('continueAnimation()', SECONDS_BETWEEN_FRAMES/1000);
    
  }
  
  function continueAnimation(){
    
    cXpos += cFrameWidth;
    //increase the index so we know which frame of our animation we are currently on
    cIndex += 1;
     
    //if our cIndex is higher than our total number of frames, we're at the end and should restart
    if (cIndex >= cTotalFrames) {
      cXpos =0;
      cIndex=0;
    }
    
    if(document.getElementById('loaderImage'))
      document.getElementById('loaderImage').style.backgroundPosition=(-cXpos)+'px 0';
    
    cPreloaderTimeout=setTimeout('continueAnimation()', SECONDS_BETWEEN_FRAMES*1000);
  }
  
  function stopAnimation(){//stops animation
    clearTimeout(cPreloaderTimeout);
    cPreloaderTimeout=false;
  }
  
  function imageLoader(s, fun)//Pre-loads the sprites image
  {
    clearTimeout(cImageTimeout);
    cImageTimeout=0;
    genImage = new Image();
    genImage.onload=function (){cImageTimeout=setTimeout(fun, 0)};
    genImage.onerror=new Function('alert(\'Could not load the image\')');
    genImage.src=s;
  }
  
  //The following code starts the animation
  new imageLoader(cImageSrc, 'startAnimation()');