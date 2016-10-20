(function () {angular
	.module('instag-app', ['ngTouch']);})(); (function () {angular
  .module('instag-app')
  .controller('CommonController',CommonController);

// Controller services injection
CommonController.$inject = ['$http','$compile','$scope','$window','$timeout'];

function CommonController ($http, $compile, $scope, $window, $timeout) {
  var vm = this;
  vm.checkBox        = checkBox;
  vm.openModal       = openModal;
  vm.closeModal      = closeModal;
  vm.checkNewsletter = checkNewsletter;


  function checkBox() {
    var checkbox = angular.element(document.getElementById('checkbox-remember'));
    if (checkbox.hasClass('checked')) {
      checkbox.removeClass('checked');
    } else {
      checkbox.addClass('checked');
    }
  };

  function openModal(modalId) {
    angular.element(document.getElementById(modalId)).addClass('displayed');
  };

  function closeModal($event) {
    var clickedElement = angular.element($event.currentTarget);
    clickedElement.parent().parent().parent().parent().removeClass('displayed');
  };

  function checkNewsletter() {
    var element = angular.element(document.getElementById('newsletter')).find('.check');
    if (!element.hasClass('checked')) {
      element.addClass('checked');
      angular.element(document.getElementById('newsletter-input')).val('1');
    } else {
      element.removeClass('checked');
      angular.element(document.getElementById('newsletter-input')).val('0');
    }
  }
}

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
}})(); (function () {angular
  .module('instag-app')
  .controller('HeaderController',HeaderController);

// Controller services injection  
HeaderController.$inject = ['$http','$timeout'];

function HeaderController ($http, $timeout) {
  var vm = this;
  vm.showMobileMenu = showMobileMenu;
  vm.displayLogin   = displayLogin;
  
  function showMobileMenu() {
    var topLinks = angular.element(document.getElementById('top-links'));
    if (!topLinks.hasClass('displayed')) {
      topLinks.addClass('displayed');
      topLinks.css('top','0px');
    } else {
      topLinks.css('top','-200px');
      topLinks.removeClass('displayed');
    }
  };

  function displayLogin(close) {
    var close = close || false;
    if (!close) {
      angular.element(document.getElementById('login-modal')).addClass('display').css('opacity','1');
    } else {
      angular.element(document.getElementById('login-modal')).css('opacity','0').removeClass('display');
    }
  };
}})(); (function () {angular
  .module('instag-app')
  .controller('LoadController',LoadController);

// Controller services injection  
LoadController.$inject = ['$scope']; 

function LoadController($scope) {
  var vm      = this;
  vm.loadFn   = loadFn;
  vm.showCols = showCols;
  vm.showTab  = showTab;

  $scope.load = vm.loadFn();

  /* loadFn() - makes the proper behavior when page it's loaded */
  function loadFn() {
    angular.element(document.getElementById('loader')).css('display','none');

    var scrollContainer = angular.element(window);

    scrollContainer.works,
    scrollContainer.plan,
    scrollContainer.grow,
    scrollContainer.measure,
    scrollContainer.devices = false;
    // Set height offset
    if (window.innerWidth > 760) {
      offset = 300;
    } else {
      offset = 0;
    }
    scrollContainer.bind("scroll", function() {
      if (!angular.element(document.getElementById('content')).hasClass('no-fixed')) {
        // Get Fixed header
        if (scrollContainer.scrollTop() > 60 && !angular.element(document.getElementById('header')).hasClass('fixed')) {
          angular.element(document.getElementById('header')).addClass('fixed');
        } else if (scrollContainer.scrollTop() == 0) {
          angular.element(document.getElementById('header')).removeClass('fixed');
        };
        // Check for current page == home
        if (angular.element(document.getElementById('header')).parent().hasClass('common-home')) {
          // "How Does It Work?" animation
          if ((scrollContainer.innerHeight() - offset) > document.getElementById('plan-col').getBoundingClientRect().top && !scrollContainer.works) {
            scrollContainer.works = true;
            vm.showCols();
          }
          // "Plan Your Strategy" animation
          if ((scrollContainer.innerHeight() - offset) > document.getElementById('plan-tabs').getBoundingClientRect().top && !scrollContainer.plan) {
            scrollContainer.plan = true;
            showTab('users');
          }
          // "Grow Your Account" animation
          if ((scrollContainer.innerHeight() - offset) > document.getElementById('grow-tabs').getBoundingClientRect().top && !scrollContainer.grow) {
            scrollContainer.grow = true;
            showTab('follow');
          }
          // "Measure Your results" animation
          if ((scrollContainer.innerHeight() - offset) > document.getElementById('measure').getBoundingClientRect().top && !scrollContainer.measure) {
            scrollContainer.measure = true;
            showTab('measure');
          }
          // "From any Device" animation
          if ((scrollContainer.innerHeight() - offset) > document.getElementById('devices').getBoundingClientRect().top && !scrollContainer.devices) {
            scrollContainer.devices = true;
            showTab('devices');
          }
        }
      }
    });
  }

  /* loadFn() - show columns in the homepage */
  function showCols() {
    var colContainer = angular.element(document.getElementById('home-how-works')).find('.container .col-item');
    var i = 0;
    setInterval(function(){
      if (i <= 3) {
        var colContainer = angular.element(document.getElementById('home-how-works')).find('.container .col-item');
        colContainer.eq(i).removeClass('appear-top').css('top','0');
        i++;
      }
    },200);
  }

  function showTab(id) {
    var tabContainer = angular.element(document.getElementById(id)).find('img');
    if (id == 'measure') {
      tabContainer.removeClass('appear-top').css('bottom','0px');
    } else if (id == 'devices') { 
      tabContainer.removeClass('appear-right').css('transform','translateX(50%)');
    } else {
      tabContainer.removeClass('appear-top').css('bottom','-30px');
    }
  }

}})(); (function () {angular
  .module('instag-app')
  .controller('LoginController',LoginController);

// Controller services injection
LoginController.$inject = ['$http','$timeout'];

function LoginController ($http, $timeout) {
  var vm = this;
  vm.closeLogin = closeLogin;
  vm.login      = login;

  function closeLogin($event) {
    angular.element(document.getElementById('login-modal')).css('opacity','0').removeClass('display');
  }

  function login() {
    var email    = angular.element(document.getElementById('login-email')).val(),
        password = angular.element(document.getElementById('login-password')).val();

    angular.element(document.getElementById('login-form-container')).find('button').addClass('loading-button').attr('disabled',true);

    var post_data = {
      email:    email,
      password: password
    };

    $http.post('index.php?route=customer/login/json', post_data).success(function(data) {
      if (data.success) {
        window.location.href = data.redirect;
      } else {
        angular.element(document.getElementById('login-error')).attr('data-tooltip',data.error_warning);
        $timeout(function() {
          angular.element(document.getElementById('login-error')).trigger('click');
        });
        angular.element(document.getElementById('login-form-container')).find('button').removeClass('loading-button').removeAttr('disabled');
      }
    });
  };
}})(); (function () {angular
  .module('instag-app')
  .controller('TooltipsController',TooltipsController);

// Controller services injection
TooltipsController.$inject = ['$scope','$rootScope'];

function TooltipsController($scope,$rootScope) {
  var vm = this;
  vm.displayTooltip     = displayTooltip;
  vm.hideTooltip        = hideTooltip;
  vm.triggerHideTooltip = triggerHideTooltip;
  vm.showChat           = showChat;


  /*
    displayTooltip() - Displays tooltip element
    Function parameters:
    $event: caller element
  */
  function displayTooltip($event) {
    // Get tooltip container
    var tooltipContainer = angular.element(document.getElementById('tooltip-container'));
    // Make sure tooltip it's not displayed
    if (!tooltipContainer.hasClass('display-tooltip')) {
      // Hide any tooltips
      vm.triggerHideTooltip();
      // Make tooltip visible
      tooltipContainer.addClass('display-tooltip');
      // Get caller tooltip settings (Position | Styles | Offset)
      var element = angular.element($event.currentTarget);
      var selectedHtmlObj = $event.currentTarget;
      var position = element.attr('data-tooltip-position');
      var styles = element.attr('data-tooltip-style');
      var offset = element.attr('data-tooltip-offset') || 0;
      var fire = element.attr('data-hide-fire') || 'mouseover';
      // Check for tooltip position and calculate it's CSS from it
      switch (position) {
        case 'top':
          var offsetY = (selectedHtmlObj.getBoundingClientRect().top - 10)+'px';
          var offsetX = (selectedHtmlObj.getBoundingClientRect().left + (selectedHtmlObj.offsetWidth/2))+'px';
          tooltipContainer.css({'top':offsetY,'left':offsetX,'transform':'translateY(-100%)'});
          break;
        case 'right':
          var offsetY = (selectedHtmlObj.getBoundingClientRect().top + (selectedHtmlObj.offsetHeight/2))+'px';
          var offsetX = (selectedHtmlObj.getBoundingClientRect().right + 25)+'px';
          tooltipContainer.css({'top':offsetY,'left':offsetX,'transform':'translatex(0)'});
          break;
        case 'bottom':
          var offsetY = (selectedHtmlObj.getBoundingClientRect().bottom - 10)+'px';
          var offsetX = (selectedHtmlObj.getBoundingClientRect().left + (selectedHtmlObj.offsetWidth/2))+'px';
          tooltipContainer.css({'top':offsetY,'left':offsetX,'transform':'translateY(50%)'});
          break;
        case 'left':
          var offsetY = (selectedHtmlObj.getBoundingClientRect().top + (selectedHtmlObj.offsetHeight/2))+'px';
          var offsetX = (selectedHtmlObj.getBoundingClientRect().left - 10 - offset)+'px';
          tooltipContainer.css({'top':offsetY,'left':offsetX,'transform':'translateX(-75%)'});
          break;
      }
      // Display tooltip container
      tooltipContainer.css('display','block');
      // Get tooltip element
      var tooltip = angular.element(document.getElementById('tooltip'));
      // Set it's content according to element setting
      tooltip.html(element.attr('data-tooltip'));
      // Clear tooltip position and assing the one from caller settings
      if (!tooltip.hasClass(position))
        tooltip.removeClass('top right bottom left').addClass(position);
      // Remove tooltip styles
        tooltip.removeClass('light dark wide').addClass(styles);
      // Set fire event
        tooltip.attr('data-hide-fire',fire);
      // Animate tooltip
      setTimeout(function(){
        tooltip.css({'opacity':'1','margin':'0px'});
      },10);
    }
  }

  /* hideTooltip() - Hides tooltip element */
  function hideTooltip($event) {
    // Get tooltip and tooltip container elements
    var tooltipContainer = angular.element(document.getElementById('tooltip-container')),
        tooltip          = angular.element(document.getElementById('tooltip'));
    // Check for event type and fire $event
    if ($event.type) {
      if (tooltip.attr('data-hide-fire') == $event.type) {
        // Remove styles, position, content, fire $event and CSS
        tooltip.removeAttr('style');
        tooltip.removeClass('top right bottom left dark light');
        tooltip.html('');
        tooltipContainer.removeAttr('style');
        tooltipContainer.removeAttr('data-hide-fire');
        // Hide tooltip
        tooltipContainer.removeClass('display-tooltip');
      }
    }
  }

  /* triggerHideTooltip() - Triggers hideTooltip() with a emulated event */
  function triggerHideTooltip() {
    // Get tooltip element
    var tooltip = angular.element(document.getElementById('tooltip'));
    // Check for event type and fire event
    if (tooltip.parent().hasClass('display-tooltip')) {
      var tooltipAttr = tooltip.attr('data-hide-fire')
      var emulatedEvent = { type: tooltipAttr };
      vm.hideTooltip(emulatedEvent);
    }
  }

  /*
    showChat() - Shows chat window
  */
  function showChat() {
    $zopim.livechat.window.show();
  }

  // Event Listeners
  $rootScope.$on('displayTooltip',function(e,$event){
    vm.displayTooltip($event);
  });

  $rootScope.$on('hideTooltip',function(e,$event){
    vm.hideTooltip($event);
  });

  $rootScope.$on('triggerHideTooltip',function(){
    vm.triggerHideTooltip();
  });

  $rootScope.$on('showChat',function(){
    vm.showChat();
  });
};})();