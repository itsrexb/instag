angular
  .module('instag-app')
  .controller('HeaderController',HeaderController);

// Controller services injection
HeaderController.$inject = ['$scope','$timeout','$http','$compile','$rootScope'];

function HeaderController ($scope,$timeout,$http,$compile,$rootScope) {
  var vm = this;
  vm.openMenu         = openMenu;
  vm.closeMenu        = closeMenu;
  vm.openLeftSidebar  = openLeftSidebar;
  // Events Broadcast
  vm.closeLeftSidebar = closeLeftSidebar;

	/*
	  openMenu() = It opens and closes menu dropdown 
	  Function parameters:
	  mobile = tells the function that screen is in mobile view
	*/
  function openMenu(mobile) {
    var mobile = mobile || false;
    var dropdown = angular.element(document.getElementById('tab-dropdown-middle'));
    // Check if clicked element is in mobile view or tablet view
    if (window.innerWidth > 730 || mobile) {
      // Check if it's open or not
      if (dropdown.find('ul').hasClass('displayed')) {
        dropdown.find('ul').removeClass('displayed');
        dropdown.find('ul').addClass('hide');
      } else {
        dropdown.find('ul').addClass('displayed');
        dropdown.find('ul').removeClass('hide');
      }
    }
  }

  /* closeMenu() = It closes menu dropdown */
  function closeMenu() {
    var dropdown = angular.element(document.getElementById('tab-dropdown-middle'));
    if (dropdown.find('ul').hasClass('displayed')) {
      dropdown.find('ul').removeClass('displayed');
      dropdown.find('ul').addClass('hide');
    }
  }

  /*
    openLeftSidebar() - Opens leftsidebar
    Function parameters:
    notMobile: it tells if view it's on mobile or not
  */
  function openLeftSidebar(notMobile) {
    var notMobile   = notMobile || false,
        leftSidebar = angular.element(document.getElementById('left-sidebar'));
    // Check if window is mobile
    if (!leftSidebar.hasClass('open-sidebar')) {
      if (notMobile && window.innerWidth <= 1570 && window.innerWidth > 1200) {
        if (window.innerWidth <= 1570)
          leftSidebar.addClass('open-sidebar');
      } else if (!notMobile && window.innerWidth <= 1200) {
        leftSidebar.addClass('open-sidebar');
        angular.element(document.getElementById('close-sidebar')).addClass('opened-close-sidebar');
      }
    } else {
      leftSidebar.removeClass('open-sidebar');
    }
  }

  // Events broadcast
  function closeLeftSidebar() {
    // Listener: LeftSidebarController
    $rootScope.$broadcast('closeLeftSidebar');
  }

}