angular
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
}