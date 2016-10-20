function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}

$(document).ready(function() {
	// chosen
	var config = {
		'.chosen-select': {
			'search_contains': true
		}
	}

	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}

	//Form Submit for IE Browser
	$('button[type="submit"]').on('click', function() {
		$("form[id*='form-']").submit();
	});

	// Highlight any found errors
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();

		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});

	// Set last page opened on the menu
	$('#menu a[href]').on('click', function() {
		sessionStorage.setItem('menu', $(this).attr('href'));
	});

	if (!sessionStorage.getItem('menu')) {
		$('#menu #dashboard').addClass('active');
	} else {
		// Sets active and open to selected page in the left column menu.
		$('#menu a[href="' + sessionStorage.getItem('menu') + '"]').parents('li').addClass('active open');
	}

	if (localStorage != "undefined" && localStorage != undefined && localStorage.getItem('column-left') == 'active') {
		$('#button-menu i').replaceWith('<i class="fa fa-dedent fa-lg"></i>');

		$('#column-left').addClass('active');

		// Slide Down Menu
		$('#menu li.active').has('ul').children('ul').addClass('collapse in');
		$('#menu li').not('.active').has('ul').children('ul').addClass('collapse');
	} else {
		$('#button-menu i').replaceWith('<i class="fa fa-indent fa-lg"></i>');

		$('#menu li li.active').has('ul').children('ul').addClass('collapse in');
		$('#menu li li').not('.active').has('ul').children('ul').addClass('collapse');
	}

	// Menu button
	$('#button-menu').on('click', function() {
		// Checks if the left column is active or not.
		if ($('#column-left').hasClass('active')) {
			try {
				localStorage.setItem('column-left', '');
			} catch (error) {
				// ios sucks
			}

			$('#button-menu i').replaceWith('<i class="fa fa-indent fa-lg"></i>');

			$('#column-left').removeClass('active');

			$('#menu > li > ul').removeClass('in collapse');
			$('#menu > li > ul').removeAttr('style');
		} else {
			try {
				localStorage.setItem('column-left', 'active');
			} catch (error) {
				// ios sucks
			}

			$('#button-menu i').replaceWith('<i class="fa fa-dedent fa-lg"></i>');

			$('#column-left').addClass('active');

			// Add the slide down to open menu items
			$('#menu li.open').has('ul').children('ul').addClass('collapse in');
			$('#menu li').not('.open').has('ul').children('ul').addClass('collapse');
		}
	});

	// Menu
	$('#menu').find('li').has('ul').children('a').on('click', function() {
		if ($('#column-left').hasClass('active')) {
			$(this).parent('li').toggleClass('open').children('ul').collapse('toggle');
			$(this).parent('li').siblings().removeClass('open').children('ul.in').collapse('hide');
		} else if (!$(this).parent().parent().is('#menu')) {
			$(this).parent('li').toggleClass('open').children('ul').collapse('toggle');
			$(this).parent('li').siblings().removeClass('open').children('ul.in').collapse('hide');
		}
	});

	// Override summernotes image manager
	$('button[data-event="showImageDialog"]').attr('data-toggle', 'image').removeAttr('data-event');

	$(document).delegate('button[data-toggle="image"]', 'click', function() {
		$('#modal-image').remove();

		$(this).parents('.note-editor').find('.note-editable').focus();

		$.ajax({
			url: 'index.php?route=common/filemanager&token=' + getURLVar('token'),
			dataType: 'html',
			beforeSend: function() {
				$('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
				$('#button-image').prop('disabled', true);
			},
			complete: function() {
				$('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
				$('#button-image').prop('disabled', false);
			},
			success: function(html) {
				$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

				$('#modal-image').modal('show');
			}
		});
	});

	// Image Manager
	$(document).delegate('a[data-toggle="image"]', 'click', function(e) {
		e.preventDefault();

		$('.popover').popover('hide', function() {
			$('.popover').remove();
		});

		var element = this;

		$(element).popover({
			html: true,
			placement: 'right',
			trigger: 'manual',
			content: function() {
				return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
			}
		});

		$(element).popover('show');

		$('#button-image').on('click', function() {
			$('#modal-image').remove();

			$.ajax({
				url: 'index.php?route=common/filemanager&token=' + getURLVar('token') + '&target=' + $(element).parent().find('input').attr('id') + '&thumb=' + $(element).attr('id'),
				dataType: 'html',
				beforeSend: function() {
					$('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
					$('#button-image').prop('disabled', true);
				},
				complete: function() {
					$('#button-image i').replaceWith('<i class="fa fa-pencil"></i>');
					$('#button-image').prop('disabled', false);
				},
				success: function(html) {
					$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

					$('#modal-image').modal('show');
				}
			});

			$(element).popover('hide', function() {
				$('.popover').remove();
			});
		});

		$('#button-clear').on('click', function() {
			$(element).find('img').attr('src', $(element).find('img').attr('data-placeholder'));

			$(element).parent().find('input').attr('value', '');

			$(element).popover('hide', function() {
				$('.popover').remove();
			});
		});
	});

	// tooltips on hover
	$('[data-toggle="tooltip"]').tooltip({container: 'body', html: true});

	// Makes tooltips work on ajax generated content
	$(document).ajaxStop(function() {
		$('[data-toggle="tooltip"]').tooltip({container: 'body'});
	});

	// https://github.com/opencart/opencart/issues/2595
	$.event.special.remove = {
		remove: function(o) {
			if (o.handler) {
				o.handler.apply(this, arguments);
			}
		}
	}

	$('[data-toggle="tooltip"]').on('remove', function() {
		$(this).tooltip('destroy');
	});

	$('.multiselect-checkbox tbody input[type="checkbox"]').click(function(e) {
		var $this = $(this),
				$tr   = $this.parent('td').parent('tr');

		if ($this.prop('checked')) {
			$tr.addClass('tr-hover');
		} else {
			$tr.removeClass('tr-hover');
		}
	});

	$('.multiselect-checkbox input[type="checkbox"]').change(function() {
		if ($('.multiselect-checkbox tbody input[type="checkbox"]:checked').length > 0) {
			var selectedCheckbox = $('.multiselect-checkbox tbody input[type="checkbox"]:checked').length + ' Selected';

			if (!$('#selectedCheckbox').length) {
				$('.multiselect-checkbox').before($('<div id="selectedCheckbox" class="label label-warning" style="position: absolute;margin-top: -15px;">').html(selectedCheckbox));
			} else {
				$('#selectedCheckbox').html(selectedCheckbox).show();
			}
		} else {
			$('#selectedCheckbox').hide();
		}
	});

	$('.multiselect-checkbox tbody tr').shiftcheckbox({
		checkboxSelector: ':checkbox',
		selectAll       : $('.multiselect-checkbox thead input[type="checkbox"]'),
		ignoreClick     : 'a',
		onChange: function(checked) {
			var $this = $(this),
					$tr   = $this.parent('td').parent('tr');

			if ($this.prop('checked')) {
				$tr.addClass('tr-hover');
			} else {
				$tr.removeClass('tr-hover');
			}
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
				var k = event.keyCode || event.which;
				switch(k) {
					case 13: //enter
						if ($(this).parent('div').find('ul.dropdown-menu').is(':visible')) {
							event.preventDefault();

							value = $(this).parent('div').find('ul.dropdown-menu li.active').attr('data-value');

							if (value && this.items[value]) {
								this.select(this.items[value]);
								this.hide();
							}
						}
						break;
					case 27: // escape
						this.hide();
						event.preventDefault();
						break;
					case 38: //up
						var cur = $(this).parent('div').find('ul.dropdown-menu').find('li.active').index();
						var total = window.totalAutoCompleteResponse;
						$(this).parent('div').find('ul.dropdown-menu li').removeClass('active');
						var newindex = 0;
						if(cur > 0 ){
							newindex = cur - 1;
						}else{
							newindex = total;
						}
						$(this).parent('div').find('ul.dropdown-menu li').eq(newindex).addClass('active');
						break;
					case 40: //down
						var cur = $(this).parent('div').find('ul.dropdown-menu').find('li.active').index();
						var total = window.totalAutoCompleteResponse;
						$(this).parent('div').find('ul.dropdown-menu li').removeClass('active');
						var newindex = 0;
						if (cur < total ) {
							newindex = cur + 1;
						} else {
							newindex = 0;
						}
						$(this).parent('div').find('ul.dropdown-menu li').eq(newindex).addClass('active');
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
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'].replace( new RegExp("("+$(this).val()+")", "ig"),"$1") + '</a></li>';
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
							html += '<li data-value="' + category[i]['item'][j]['value'] + '" ><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'].replace(new RegExp("("+$(this).val()+")", "ig"),"$1") + '</a></li>';
						}
					}
					window.totalAutoCompleteResponse = json.length - 1;
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


//Multiple shift checkbox
(function($) {
  var ns = '.shiftcheckbox';

  $.fn.shiftcheckbox = function(opts) {
    opts = $.extend({
      checkboxSelector : null,
      selectAll        : null,
      onChange         : null,
      ignoreClick      : null
    }, opts);

    if (typeof opts.onChange != 'function') {
      opts.onChange = function(checked) { };
    }

    $.fn.scb_changeChecked = function(opts, checked) {
      this.prop('checked', checked);
      opts.onChange.call(this, checked);
      return this;
    }

    var $containers,
        $checkboxes,
        $containersSelectAll,
        $checkboxesSelectAll,
        $otherSelectAll,
        $containersAll,
        $checkboxesAll;

    if (opts.selectAll) {
      // We need to set up a "select all" control
      $containersSelectAll = $(opts.selectAll);
      if ($containersSelectAll && !$containersSelectAll.length) {
        $containersSelectAll = false;
      }
    }

    if ($containersSelectAll) {
      $checkboxesSelectAll = $containersSelectAll
        .filter(':checkbox')
        .add($containersSelectAll.find(':checkbox'));

      $containersSelectAll = $containersSelectAll.not(':checkbox');
      $otherSelectAll = $containersSelectAll.filter(function() {
        return !$(this).find($checkboxesSelectAll).length;
      });
      $containersSelectAll = $containersSelectAll.filter(function() {
        return !!$(this).find($checkboxesSelectAll).length;
      }).each(function() {
        $(this).data('childCheckbox', $(this).find($checkboxesSelectAll)[0]);
      });
    }

    if (opts.checkboxSelector) {

      // checkboxSelector means that the elements we need to attach handlers to
      // ($containers) are not actually checkboxes but contain them instead

      $containersAll = this.filter(function() {
        return !!$(this).find(opts.checkboxSelector).filter(':checkbox').length;
      }).each(function() {
        $(this).data('childCheckbox', $(this).find(opts.checkboxSelector).filter(':checkbox')[0]);
      }).add($containersSelectAll);

      $checkboxesAll = $containersAll.map(function() {
        return $(this).data('childCheckbox');
      });

    } else {

      $checkboxesAll = this.filter(':checkbox');

    }

    if ($checkboxesSelectAll && !$checkboxesSelectAll.length) {
      $checkboxesSelectAll = false;
    } else {
      $checkboxesAll = $checkboxesAll.add($checkboxesSelectAll);
    }

    if ($otherSelectAll && !$otherSelectAll.length) {
      $otherSelectAll = false;
    }

    if ($containersAll) {
      $containers = $containersAll.not($containersSelectAll);
    }
    $checkboxes = $checkboxesAll.not($checkboxesSelectAll);

    if (!$checkboxes.length) {
      return;
    }

    var lastIndex = -1;

    var checkboxClicked = function(e) {
      var checked = !!$(this).prop('checked');

      var curIndex = $checkboxes.index(this);
      if (curIndex < 0) {
        if ($checkboxesSelectAll.filter(this).length) {
          $checkboxesAll.scb_changeChecked(opts, checked);
        }
        return;
      }

      if (e.shiftKey && lastIndex != -1) {
        var di = (curIndex > lastIndex ? 1 : -1);
        for (var i = lastIndex; i != curIndex; i += di) {
          $checkboxes.eq(i).scb_changeChecked(opts, checked);
        }
      }

      if ($checkboxesSelectAll) {
        if (checked && !$checkboxes.not(':checked').length) {
          $checkboxesSelectAll.scb_changeChecked(opts, true);
        } else if (!checked) {
          $checkboxesSelectAll.scb_changeChecked(opts, false);
        }
      }

      lastIndex = curIndex;
    };

    if ($checkboxesSelectAll) {
      $checkboxesSelectAll
        .prop('checked', !$checkboxes.not(':checked').length)
        .filter(function() {
          return !$containersAll.find(this).length;
        }).on('click' + ns, checkboxClicked);
    }

    if ($otherSelectAll) {
      $otherSelectAll.on('click' + ns, function() {
        var checked;
        if ($checkboxesSelectAll) {
          checked = !!$checkboxesSelectAll.eq(0).prop('checked');
        } else {
          checked = !!$checkboxes.eq(0).prop('checked');
        }
        $checkboxesAll.scb_changeChecked(opts, !checked);
      });
    }

    if (opts.checkboxSelector) {
      $containersAll.on('click' + ns, function(e) {
        if ($(e.target).closest(opts.ignoreClick).length) {
          return;
        }
        var $checkbox = $($(this).data('childCheckbox'));
        $checkbox.not(e.target).each(function() {
          var checked = !$checkbox.prop('checked');
          $(this).scb_changeChecked(opts, checked);
        });

        $checkbox[0].focus();
        checkboxClicked.call($checkbox, e);

        // If the user clicked on a label inside the row that points to the
        // current row's checkbox, cancel the event.
        var $label = $(e.target).closest('label');
        var labelFor = $label.attr('for');
        if (labelFor && labelFor == $checkbox.attr('id')) {
          if ($label.find($checkbox).length) {
            // Special case:  The label contains the checkbox.
            if ($checkbox[0] != e.target) {
              return false;
            }
          } else {
            return false;
          }
        }
      }).on('mousedown' + ns, function(e) {
        if (e.shiftKey) {
          // Prevent selecting text by Shift+click
          return false;
        }
      });
    } else {
      $checkboxes.on('click' + ns, checkboxClicked);
    }

    return this;
  };
})(jQuery);