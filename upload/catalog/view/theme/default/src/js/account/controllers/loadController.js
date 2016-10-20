angular
  .module('instag-app')
  .controller('LoadController',LoadController);

// Controller services injection
LoadController.$inject = ['$timeout','$scope','$compile','$window','$rootScope'];

function LoadController ($timeout,$scope,$compile,$window,$rootScope) {
  var vm        = this;
  vm.responsive = responsive;
  vm.listenKeys = listenKeys;
  $scope.load   = scopeLoad;

  /* scopeLoad() - Executes tasks on $scope load */
  function scopeLoad() {
    // Checkout responsive
    vm.responsive(window.innerWidth);

    // Check for a previous selected account, otherwise, select the first one
    if (localStorage != "undefined" && localStorage != undefined && localStorage.account_id != "undefined" && localStorage.account_id != undefined) {
      $timeout(function() {
        var accounts = angular.element(document.getElementById('left-sidebar')).find('.account-item');
        var clickedFlag = false;
        for (var i=0;i < accounts.length;i++) {
          if (accounts.eq(i).attr('data-id') == localStorage.account_id) {
            accounts.eq(i).trigger('click');
            clickedFlag = true;
          }
        }
        if (!clickedFlag) {
          $timeout(function() {
            angular.element(document.getElementById('left-sidebar')).find('.account-item').eq(0).trigger('click');
          }, 0);
        }
      }, 0);
    } else {
      $timeout(function() {
        angular.element(document.getElementById('left-sidebar')).find('.account-item').eq(0).trigger('click');
      }, 0);
    };

    // Add keys event listener
    vm.listenKeys();

    // Remove Loader
    angular.element(document.getElementById('loader')).css('display','none');
  }

  // Keypress event listener
  function listenKeys() {
    document.addEventListener("keydown", keyDownTextField, false);

    function keyDownTextField(e) {
      var keyCode = e.keyCode;
      switch (keyCode) {
        case 27:
          angular.element(document).find('.search-box-displayed').removeClass('search-box-displayed');
          break;
      }
    }
  }

  // Events broadcast
  function responsive(width) {
    // Listener: ResizeController
    $rootScope.$broadcast('responsive',width);
  }

  // Set responsive listener for document resize
  angular.element($window).bind('resize',function(){
   vm.responsive(window.innerWidth);
  });

}