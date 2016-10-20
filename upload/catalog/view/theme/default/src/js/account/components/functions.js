function checkoutResponse(json) {
  angular.element(document.getElementById('billing')).scope().checkoutResponse(json);
}

function updatePaymentMethodResponse(json) {
  angular.element(document.getElementById('billing')).scope().updatePaymentMethodResponse(json);
}

// jQuery Functions
//  It Centers the content for X and Y axis - "relative" Arg = reference element to center
jQuery.fn.center = function (relative) {
  var relative = relative || this.parent();
  this.css("position","absolute");
  this.css("top", Math.max(0, (($(relative).outerHeight() - $(this).outerHeight()) / 2) + $(relative).scrollTop()) + "px");
  this.css("left", Math.max(0, (($(relative).width() - $(this).width()) / 2) + $(relative).scrollLeft()) + "px");
  return this;
}