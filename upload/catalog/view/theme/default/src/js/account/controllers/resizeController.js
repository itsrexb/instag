angular
  .module('instag-app')
  .controller('ResizeController',ResizeController);

ResizeController.$inject = ['chartService','$scope','$compile','$timeout','$rootScope']

function ResizeController (chartService,$scope,$compile,$timeout,$rootScope) {
  var vm                    = this;
      vm.responsiveFlag     = false;
      vm.responsive         = responsive;
      vm.mobileResponsive   = mobileResponsive;
      // Services
      vm.changeHeight       = chartService.changeHeight;
      // Events Broadcast
      vm.closeLeftSidebar   = closeLeftSidebar;

  /*
    responsive() - DOM manipulation for mobile view
    Function parameters:
    width: window width
  */
  function responsive (width) {
    var width = width || window.innerWidth();
    // Check for dashboard tab selected before matching 'my influence' and 'my followers' cards
    if (angular.element('tab-header-middle').find('.current-tab').attr('data-tab') == 'dashboard') {
      vm.changeHeight();
    }
    // Check tab-header width
    var tabHeader      = angular.element(document.getElementById('tab-header-middle')),
        leftSidebar    = angular.element(document.getElementById('left-sidebar')),
        rightSidebar   = angular.element(document.getElementById('right-sidebar')),
        availableSpace = angular.element(window).innerWidth() - (leftSidebar.innerWidth() + rightSidebar.innerWidth());

    if ((!window.tabHeaderWidth && (angular.element(window).innerWidth() > 1200)) || (window.tabHeaderWidth <= 40)) {
      window.tabHeaderWidth = 0;

      // Summation of all tab-header elements width
      for (var i = 0; i< tabHeader.children().length; i++) {
        window.tabHeaderWidth += tabHeader.children().eq(i).outerWidth(true);
      }
      // Add Tab Header Padding
      window.tabHeaderWidth += 40;
    }

    if (window.tabHeaderWidth) {
      // Check if tab-header elements are wider tha available space
      if (window.tabHeaderWidth >= availableSpace) {
        if (!angular.element(document.getElementById('content')).hasClass('overwidth')) {
          angular.element(document.getElementById('content')).addClass('overwidth');
        }
      } else {
        if (angular.element(document.getElementById('content')).hasClass('overwidth')) {
          angular.element(document.getElementById('content')).removeClass('overwidth');
        }
      }
    }

    // Tablet Size
    if (width < 1200) {
      // Clear previous account-item on container (account image on header)
      angular.element(document.getElementById('logo-u-container')).find('.account-item').remove();
      // Clone current account
      var currentAccount = angular.element(document.getElementById('left-sidebar')).find('.current-account').clone();
      // Remove angular directives
      currentAccount.removeAttr('ng-click');
      currentAccount.find('.account-square-status').removeAttr('ng-click');
      currentAccount.find('.sidebar-item-title').remove();
      // Append account to logo-container so it can display image on header
      angular.element(document.getElementById('logo-u-container')).append($compile(currentAccount)($scope));
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
      vm.closeLeftSidebar();
      $timeout(function() {
        // Select current tab
        var content    = angular.element(document.getElementById('content')),
            currentTab = content.find('.tab-header .current-tab').attr('data-tab');
        content.find('.dropdown-menu .'+currentTab).triggerHandler('click');
      },0);
      // Call mobileResponsive()
      mobileResponsive(width);
    } else {
      // Clear header image
      angular.element(document.getElementById('logo-u-container')).find('.account-item').remove();
      // Call mobileResponsive()
      mobileResponsive(width);
    }

    // Take a look at window height
    var windowHeight      = angular.element(window).innerHeight(),
        leftSidebar       = angular.element(document.getElementById('left-sidebar')),
        instaAccounts     = leftSidebar.find('.instagram-accounts'),
        addInstaBtn       = leftSidebar.find('#add-account'),
        bottomLeftSidebar = leftSidebar.find('#bottom-left-sidebar');
    // Get Instagram Accounts Total Height
    if (!vm.instaAccountsHeight) {
      vm.instaAccountsHeight = 0;
      for (var i = 0; i<= instaAccounts.children().length ;i++) {
        vm.instaAccountsHeight += instaAccounts.children().eq(i).innerHeight();
      }
    }
    if (windowHeight <= (vm.instaAccountsHeight + addInstaBtn.outerHeight() + bottomLeftSidebar.outerHeight() + 70)) {
      if (!instaAccounts.hasClass('overflow')) {
        instaAccounts.addClass('overflow');
      }
      instaAccounts.css('maxHeight',(windowHeight - (bottomLeftSidebar.outerHeight() + addInstaBtn.outerHeight() + 70)));
    } else {
      if (instaAccounts.hasClass('overflow')) {
        instaAccounts.removeClass('overflow');
        instaAccounts.css('maxHeight','none');
      }
    }
  }

  /*
    mobileResponsive() - DOM manipulation for mobile view
    Function parameters:
    width: window width
  */
  function mobileResponsive(width) {
    if (width < 730) {
      // Get main content and right sidebar elements
      var content      = angular.element(document.getElementById('content')),
          rightSidebar = angular.element(document.getElementById('right-sidebar'));
      // If it's not kickoff
      if (!content.attr('data-kickoff')) {
        // Check if #account is not included on main content
        if (content.find('#account').length == 0) {
          // Include it
          var tabContent = angular.element(document.getElementById('tab-body-sidebar')).html();
          content.find('.middle-tab').prepend($compile(tabContent)($scope));
        }
        $timeout(function() {
          var content    = angular.element(document.getElementById('content')),
              // Check if current tab it's dashboard  
              currentTab = content.find('.tab-header .current-tab').attr('data-tab');
          if (currentTab == 'dashboard' && !vm.responsiveFlag) {
            vm.responsiveFlag = true;
            // Select "account" tab
            content.find('.dropdown-menu .account').triggerHandler('click');
          }
        },0);
        // Update chart options
        if (window.graphic) {
          if (window.graphic.options.maintainAspectRatio) {
            window.graphic.options.maintainAspectRatio = false;
            window.graphic.update();
          }
        }
      // If account it's on kickoff
      } else {
        // Check if start-account tab is not included on main content
        if (content.find('#start-account').length == 0) {
          // Include it
          var tabContent = angular.element(document.getElementById('tab-body-sidebar')).html();
          content.find('.middle-tab').prepend($compile(tabContent)($scope));
          content.find('#start-account').removeClass('current-account');
          // Empty Right sidebar
          angular.element(document.getElementById('tab-body-sidebar')).empty();
        }
        vm.responsiveFlag = false;
        $timeout(function() {
          var content = angular.element(document.getElementById('content'));
          // Check if current tab it's dashboard  
          var currentTab = content.find('.tab-header .current-tab').attr('data-tab');
          if (currentTab == 'kickoff') {
            // Select "account" tab
            content.find('.dropdown-menu .start-account').triggerHandler('click');
          }
        },0);
      }
    } else if (width > 730) {
      // Remove hide class from menu-dropdown
      angular.element(document.getElementById('tab-dropdown-middle')).removeClass('hide');
      // Get main content element
      var content = angular.element(document.getElementById('content'));
      // If it's not kickoff
      if (!content.attr('data-kickoff')) {
        $timeout(function() {
          var content = angular.element(document.getElementById('content'));
          var currentTab = content.find('.tab-header .current-tab').attr('data-tab');
          // Check if there's a current tab and if it's not a right-sidebar tab
          if (currentTab != 'account' && currentTab != 'activity' &&  currentTab != undefined) {
            // Select current tab
            content.find('.dropdown-menu .'+currentTab).triggerHandler('click');
          } else {
            // Select dashboard tab
            content.find('.dropdown-menu .dashboard').triggerHandler('click');
          }
          // Append right sidebar tabs to right sidebar
          var rightSidebar = angular.element(document.getElementById('tab-body-sidebar'));
          rightSidebar.append(content.find('#account'));
          $compile(rightSidebar.contents())($scope)
          rightSidebar.append(content.find('#activity'));
          $compile(rightSidebar.contents())($scope)
        },0);
        // Update chart options
        if (window.graphic) {
          if (window.graphic.options.maintainAspectRatio) {
            window.graphic.options.maintainAspectRatio = false;
            window.graphic.update();
          }
        }
      } else {
        // If account it's on kickoff
        $timeout(function() {
          var content = angular.element(document.getElementById('content'));
          var currentTab = content.find('.tab-header .current-tab').attr('data-tab');
          // Check if current tab doesn't belong to right-sidebar
          if (currentTab != 'start-account' && currentTab != undefined) {
            // Select tab
            content.find('.dropdown-menu .'+currentTab).triggerHandler('click');
          } else {
            // Select kickoff tab
            content.find('.dropdown-menu .kickoff').triggerHandler('click');
          }
          // Select start account tab on right sidebar
          var startAccountTab = content.find('#start-account');
          startAccountTab.addClass('current-tab');
          var sidebarTabBody = angular.element(document.getElementById('tab-body-sidebar'));
          sidebarTabBody.append($compile(startAccountTab)($scope));
          $scope.$digest();
        },0);
      }
    }
  }

  // Event Listeners
  $rootScope.$on('responsive',function(e,width){
    vm.responsive(width);
  });

  // Events broadcast
  function closeLeftSidebar($event) {
    // Listener: LeftSidebarController
    $rootScope.$broadcast('closeLeftSidebar',$event);
  }

};