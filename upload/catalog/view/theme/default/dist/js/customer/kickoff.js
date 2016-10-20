(function () {angular
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
  .controller('AccountsController',['$http','$compile','$scope','$window','$timeout',function($http,$compile,$scope,$window,$timeout){

/* 
  addAccountModal() - It opens or closes account modal depending on argument
  Function arguments:
  close = tells function to close account modal
*/
  this.addAccountModal = function (close){
    var close = close || false;
    angular.element(document.getElementById('modal-add-instagram')).addClass('displayed');
    closeLeftSidebar();
    if (close) {
      angular.element(document.getElementById('modal-add-instagram')).removeClass('displayed');
    }
  };

  /* addAccount() - Check's for form input fields, validates them and add a new user if data is correct */
  this.addAccount = function () {
    // Check for empty username
    var username = angular.element(document.getElementById('add-account-username')).val();
    if (username == '') {
      $timeout(function() {
        angular.element(document.getElementById('username-empty')).trigger('click');
      },0);
    } else {
      // Check for empty password
      var password = angular.element(document.getElementById('add-account-password')).val();
      if (password == '') {
        $timeout(function() {
          angular.element(document.getElementById('password-empty')).trigger('click');
        },0);
      } else {
        // Hide any tooltips
        this.triggerHideTooltip();
        // Disable button
        var clickedElement = angular.element(document.getElementById('button-add-instagram'));
        clickedElement.addClass('loading-button').attr('disabled','true');
        // Make the request
        var post_data = {
          username: username,
          password: password,
          redirect: 1
        };
        var self = this;
        $http.post('index.php?route=account/instagram/insert',post_data).success(function(data){
          // Check for redirections
          if (data.redirect) {
            window.location = data.redirect;
          }
          // Check for success on response
          if (!data.success) {
            $timeout(function(){
              angular.element(document.getElementById('add-instagram-next')).trigger('click');
            },0);
          }
          // Enable button
          clickedElement.removeClass('loading-button').removeAttr('disabled');
        });
      };
    };
  };

/*
  retryAddAccount() - It repeats login attempt
*/
  this.retryAddAccount = function () {

    // Check for empty username
    var username = angular.element(document.getElementById('add-account-username')).val();
    if (username == '') {
      $timeout(function() {
        angular.element(document.getElementById('username-empty')).trigger('click');
      },0);
    } else {
      // Check for empty password
      var password = angular.element(document.getElementById('add-account-password')).val();
      if (password == '') {
        $timeout(function() {
          angular.element(document.getElementById('password-empty')).trigger('click');
        },0);
      } else {
        // Hide any tooltips
        this.triggerHideTooltip();
        // Disable button
        var clickedElement = angular.element(document.getElementById('retry-add-account'));
        clickedElement.addClass('loading-button').attr('disabled','true');
        // Make the request
        var post_data = {
          username: username,
          password: password,
          redirect: 1
        };
        var self = this;
        $http.post('index.php?route=account/instagram/insert',post_data).success(function(data){
          // Check for redirections
          if (data.redirect) {
            window.location = data.redirect;
          }
          // Check for success on response
          if (!data.success) {
            angular.element(document.getElementById('kickoff-error-modal')).addClass('displayed');
            // Enable button
            clickedElement.removeClass('loading-button').removeAttr('disabled');
          }
        });
      };
    };
  }

/* 
  videoModal() - It opens or closes tutorial modal depending on argument
  Function arguments:
  close = tells function to close tutorial modal
*/
  this.videoModal = function (close) {
    var close = close || false;
    angular.element(document.getElementById('video-modal')).addClass('displayed');
    closeLeftSidebar();
    if (close) {
      angular.element(document.getElementById('video-modal')).removeClass('displayed');
    }
  };

/* 
  helpModal() - Changes embed video src and calls videoModal()
  Function arguments:
  $event = provides video ID through data-video-id attribute
*/
  this.helpModal = function ($event) {
    var videoContainer =  angular.element(document.getElementById('help-video'));
    this.videoModal();
    $timeout(function(){
      videoContainer.parent().find('#help-video-button').trigger('click');
    },0);
  }

  /*
    showChat() - Shows chat window
  */
  this.showChat = function () {
    $zopim.livechat.window.show();
  }

  /* openMenu() = It opens and closes menu dropdown */
  this.openMenu = function () {
    var dropdown = angular.element(document.getElementById('tab-dropdown-middle'));
    // Check if it's open or not
    if (dropdown.find('ul').hasClass('displayed')) {
      dropdown.find('ul').removeClass('displayed');
      dropdown.find('ul').addClass('hide');
    } else {
      dropdown.find('ul').addClass('displayed');
      dropdown.find('ul').removeClass('hide');
    }
  }

  /* closeMenu() = It closes menu dropdown */
  this.closeMenu = function () {
    var dropdown = angular.element(document.getElementById('tab-dropdown-middle'));
    dropdown.find('ul').removeClass('displayed');
    dropdown.find('ul').addClass('hide');
  }

  /* closeLeftSidebar() = It closes left sidebar */
  function closeLeftSidebar () {
    angular.element(document.getElementById('left-sidebar')).removeClass('open-sidebar');
    if (window.innerWidth <= 1200)
      angular.element(document.getElementById('close-sidebar')).removeClass('opened-close-sidebar');
  };

/*
  openLeftSidebar() = It opens left sidebar
  Function arguments:
  notmobile: it tells if view it's on mobile or not
*/
  this.openLeftSidebar = function(notmobile) {
    var notmobile = notmobile || false;
    // Check if window is mobile
    if (notmobile && window.innerWidth <= 1570 && window.innerWidth > 1200) {
      if (window.innerWidth <= 1570)
        angular.element(document.getElementById('left-sidebar')).addClass('open-sidebar');
    } else if (!notmobile && window.innerWidth <= 1200) {
      angular.element(document.getElementById('left-sidebar')).addClass('open-sidebar');
      angular.element(document.getElementById('close-sidebar')).addClass('opened-close-sidebar');
    }
  };

/* closeLeftSidebar() angular equivalent for closeLeftSidebar() */
  this.closeLeftSidebar = function(notmobile) {
    var notmobile = notmobile || false;
    // Check if window is mobile
    if (notmobile && window.innerWidth <= 1570 && window.innerWidth > 1200) {
      closeLeftSidebar();
    } else if (!notmobile) {
      closeLeftSidebar();
    }
  };

  /* logoHover() = it defines behavior for logo on mobile */
  this.logoHover = function() {
    if (window.innerWidth <= 1570 && window.innerWidth > 730) {
      angular.element(document.getElementById('logo')).addClass('hover');
    }
  }

  /* removeLogoHover = it defines behavior for logo on mobile */
  this.removeLogoHover = function() {
    if (window.innerWidth <= 1570) {
      angular.element(document.getElementById('logo')).removeClass('hover');
    }
  }

/*
  mobileResponsive() - DOM manipulation for mobile view
  Function arguments:
  width: window width
*/
  // Define responsive status to check if view is already on mobile or not
  var responsiveStatus = false;
  function mobileResponsive (width) {
    if (width < 730) {
      // Get main content and right sidebar elements
      var content = angular.element(document.getElementById('content'));
      var rightSidebar = angular.element(document.getElementById('right-sidebar'));
      // If it's not kickoff
      if (!content.attr('data-kickoff')) {
        // Check if #account is not included on main content
        if (content.find('#account').length == 0) {
          // Include it
          var tabContent = angular.element(document.getElementById('tab-body-sidebar')).html();
          content.find('.middle-tab').prepend($compile(tabContent)($scope));
        }
        $timeout(function() {
          var content = angular.element(document.getElementById('content'));
          // Check if current tab it's dashboard  
          var currentTab = content.find('.tab-header .current-tab').attr('data-tab');
          if (currentTab == 'dashboard' && !responsiveStatus) {
            responsiveStatus = true;
            // Select "account" tab
            content.find('.dropdown-menu .account').trigger('click');
          }
        },0);
      } else {        // If account it's on kickoff
        // Check if start-account tab is not included on main content
        if (content.find('#start-account').length == 0) {
          // Include it
          var tabContent = angular.element(document.getElementById('tab-body-sidebar')).html();
          content.find('.middle-tab').prepend($compile(tabContent)($scope));
          content.find('#start-account').removeClass('current-account');
          // Empty Right sidebar
          angular.element(document.getElementById('tab-body-sidebar')).empty();
        }
        responsiveStatus = false;
        $timeout(function() {
          var content = angular.element(document.getElementById('content'));
          // Check if current tab it's dashboard  
          var currentTab = content.find('.tab-header .current-tab').attr('data-tab');
          if (currentTab == 'kickoff') {
            // Select "account" tab
            content.find('.dropdown-menu .start-account').trigger('click');
          }
        },0);
      }
    } else if (width > 730) {           // If window it's not mobile
      // Get main content element
      var content = angular.element(document.getElementById('content'));
      // If it's not kickoff
      if (!content.attr('data-kickoff')) {
        $timeout(function() {
          var content = angular.element(document.getElementById('content'));
          var currentTab = content.find('.tab-header .current-tab').attr('data-tab');
          // Check if there's a current tab and if it's not a right-sidebar tab
          if (currentTab != 'account' && currentTab != 'activity' && currentTab != 'followback' && currentTab != undefined) {
            // Select current tab
            content.find('.dropdown-menu .'+currentTab).trigger('click');
          } else {
            // Select dashboard tab
            content.find('.dropdown-menu .dashboard').trigger('click');
          }
          // Append right sidebar tabs to right sidebar
          var rightSidebar = angular.element(document.getElementById('right-sidebar'));
          rightSidebar.append(content.find('#account'));
          rightSidebar.append(content.find('#activity'));
          rightSidebar.append(content.find('#followback'));
        },0);
      } else {                      // If account it's on kickoff
        $timeout(function() {
          var content = angular.element(document.getElementById('content'));
          var currentTab = content.find('.tab-header .current-tab').attr('data-tab');
          // Check if current tab doesn't belong to right-sidebar
          if (currentTab != 'start-account' && currentTab != undefined) {
            // Select tab
            content.find('.dropdown-menu .'+currentTab).trigger('click');
          } else {
            // Select kickoff tab
            content.find('.dropdown-menu .kickoff').trigger('click');
          }
          // Select start account tab on right sidebar
          var startAccountTab = content.find('#start-account');
          startAccountTab.addClass('current-tab');
          var sidebarTabBody = angular.element(document.getElementById('tab-body-sidebar'));
          sidebarTabBody.append(startAccountTab);
        },0);
      }
    }
  }

/*
  responsive() - DOM manipulation for mobile view
  Function arguments:
  width: window width
*/
  function responsive (width) {
    if (width < 1200) {
      // Clear previous account-item on container (account image on header)
      angular.element(document.getElementById('logo-u-container')).find('.account-item').remove();
      // Clone current account
      var currentAccount = angular.element(document.getElementById('left-sidebar')).find('.current-account').clone();
      // Remove angular directives
      currentAccount.removeAttr('ng-click');
      currentAccount.find('.account-square-status').removeAttr('ng-click');
      // Append account to logo-container so it can display image on header
      angular.element(document.getElementById('logo-u-container')).append(currentAccount);
      // If there's no image on account item
      if (currentAccount.find('.account-img img').length < 1) {
        // Show instag "U" logo
        angular.element(document.getElementById('logo-u')).addClass('displayed');
        angular.element(document.getElementById('logo-u-container')).find('.account-status').remove();
      } else {
        // Hide instag "U" logo
        angular.element(document.getElementById('logo-u')).removeClass('displayed');
      }
      // Close left-sidebar
      closeLeftSidebar();
      $timeout(function() {
        // Select current tab
        var content = angular.element(document.getElementById('content'));
        var currentTab = content.find('.tab-header .current-tab').attr('data-tab');
        content.find('.dropdown-menu .'+currentTab).trigger('click');
      },0);
      // Call mobileResponsive()
      mobileResponsive(width);
    } else {
      // Clear header image
      angular.element(document.getElementById('logo-u-container')).find('.account-item').remove();
      // Call mobileResponsive()
      mobileResponsive(width);
    }
  };

/*
  displayTooltip() - Displays tooltip element
  Function arguments:
  $event: caller element
*/
  this.displayTooltip = function($event) {
    // Get tooltip container
    var tooltipContainer = angular.element(document.getElementById('tooltip-container'));
    // Hide any tooltips
    this.triggerHideTooltip();
    // Make sure tooltip it's not displayed
    if (!tooltipContainer.hasClass('display-tooltip')) {
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
      // Get tooltip style and assing the one from caller settings
      if (!tooltip.styles)
        tooltip.removeClass('light dark').addClass(styles);
      // Set fire event
        tooltip.attr('data-hide-fire',fire);
      // Animate tooltip
      setTimeout(function(){
        tooltip.css({'opacity':'1','margin':'0px'});
      },10);
    }
  }

  /* hideTooltip() - Hides tooltip element */
  this.hideTooltip = function($event) {
    // Get tooltip and tooltip container elements
    var tooltipContainer = angular.element(document.getElementById('tooltip-container'));
    var tooltip = angular.element(document.getElementById('tooltip'));
    // Check for event type and fire event
    if ($event.type) {
      if (tooltip.attr('data-hide-fire') == $event.type) {
        // Remove styles, position, content, fire event and CSS
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
  this.triggerHideTooltip = function() {
    // Get tooltip element
    var tooltip = angular.element(document.getElementById('tooltip'));
    // Check for event type and fire event
    if (tooltip.parent().hasClass('display-tooltip')) {
      var tooltipAttr = tooltip.attr('data-hide-fire')
      var emulatedEvent = { type: tooltipAttr };
      this.hideTooltip(emulatedEvent);
    }
  }

  this.closeModal = function($event) {
    var $event = $event || false;
    if ($event) {
      var clickedElement = angular.element($event.currentTarget);
      if (angular.element($event.target).hasClass('modal')) {
        clickedElement.removeClass('displayed');
        if ($event.target.id == 'video-modal') {
          $timeout(function(){
            angular.element(document.getElementById('stop-video')).trigger('click');
          });
        }
        this.triggerHideTooltip();
      }
    } else {
      angular.element(document).find('.modal.displayed').removeClass('displayed');
    }
  }

  this.displayWarning = function(container) {
    angular.element(document.getElementById(container)).addClass('displayed');
  }

  this.cancelAction = function(container) {
    angular.element(document.getElementById(container)).removeClass('displayed');
  }

  this.closeDialog = function ($event) {
    var clickedElement = angular.element($event.currentTarget);                   // Clicked Element
    clickedElement.removeClass('displayed');
  };

  window.addEventListener('resize',function($scope){
    responsive(window.innerWidth);
    carouselHeight();
  });

  function moveLinks (primary,secondary,data) {
    var primaryElement = angular.element(document.getElementById(primary));
    angular.element(document.getElementById(secondary)).attr('href',primaryElement.attr(data));
    primaryElement.remove();
  };

  function carouselHeight() {
    var items = angular.element(document.getElementById('kickoff-carousel')).find('.item');
    var maxHeight = 0;
    for (var i = 0; i<= items.length; i++) {
      items.eq(i).innerHeight('auto')
      if (items.eq(i).innerHeight() > maxHeight)
        maxHeight = items.eq(i).innerHeight();
    }
    items.innerHeight(maxHeight);
    angular.element(document.getElementById('kickoff-carousel')).innerHeight(maxHeight+10);
  }

  $scope.load = function () {
    // Checkout responsive
    responsive(window.innerWidth);
    carouselHeight();
    angular.element(document.getElementById('loader')).css('display','none');
  };

}]);

// jQuery Functions
//  It Centers the content for X and Y axis - "relative" Arg = reference element to center
jQuery.fn.center = function (relative) {
  var relative = relative || this.parent();
  this.css("position","absolute");
  this.css("top", Math.max(0, (($(relative).outerHeight() - $(this).outerHeight()) / 2) + $(relative).scrollTop()) + "px");
  this.css("left", Math.max(0, (($(relative).width() - $(this).width()) / 2) + $(relative).scrollLeft()) + "px");
  return this;
}})();