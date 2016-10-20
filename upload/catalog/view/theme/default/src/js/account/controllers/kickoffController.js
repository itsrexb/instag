angular
  .module('instag-app')
  .controller('KickoffController',KickoffController);

// Controller services injection
KickoffController.$inject = ['checkStatusService','timeAgoService','$scope','$http','$compile','$timeout','$rootScope'];

function KickoffController (checkStatusService,timeAgoService,$scope,$http,$compile,$timeout,$rootScope) {
	var vm            = this;
	vm.startKickoff   = startKickoff;
  vm.skipWhitelist  = skipWhitelist;
  // Services  
  vm.checkStatus    = checkStatusService.checkStatus;
  vm.timeAgo        = timeAgoService.timeAgo;
  // Events broadcast
  vm.responsive     = responsive;
  vm.changeTab      = changeTab;
  vm.checkUsersList = checkUsersList;

	/*
		startKickoff() - Starts account and leaves kickoff 
		Function Parameters:
		accountId - Account ID
	*/
	function startKickoff(accountId) {
    var startKickoffButton = angular.element(document.getElementById('start-kickoff-button'));
    if (startKickoffButton.hasClass('disabled-users')) {
      var tabBtn = { currentTarget: angular.element(document.getElementById('tab-header-middle')).find('.users') };
      vm.changeTab(tabBtn,'middle');
    } else if (startKickoffButton.hasClass('disabled-whitelist')) {
      var tabBtn = { currentTarget: angular.element(document.getElementById('tab-header-middle')).find('.whitelist') };
      vm.changeTab(tabBtn,'middle');
    } else {
      angular.element(document.getElementById('loader')).addClass('loading');
      startKickoffButton.addClass('loading-button');
      $http.get('index.php?route=account/account/start&account_id=' + accountId).success(function(data){
        if (data.redirect) {
          window.location = data.redirect;
        }
        if (data.success) {
          // Set account as current
          $timeout(function() {
            angular.element(document.getElementById('left-sidebar')).find('.current-account').trigger('click');
            vm.checkStatus(angular.element(document.getElementById('content')).attr('data-msg'));
          }, 0);
        }
      });
    }
  }

  /*
    skipWhitelist() - Enables skipping whitelist
  */
  function skipWhitelist(accountId) {
    var post_data = {
      account_id: accountId
    };
    $http.post('index.php?route=account/account/skip_whitelist',post_data).success(function(data){
      if (data.redirect) {
        window.location = data.redirect;
      }
      if (data.success) {
        var skipWhitelistButton = angular.element(document.getElementById('skip-whitelist-card')).find('.gold-button'),
            listContainer       = angular.element(document.getElementById('whitelist-users-list'));
        // Disable skip whitelist button
        console.log('here');
        console.log(skipWhitelistButton);
        skipWhitelistButton.attr('disabled','true');
        skipWhitelistButton.html(skipWhitelistButton.attr('data-language'));
        // Mark step as completed
        angular.element(document.getElementById('start-kickoff-button')).attr('data-skip-whitelist','1');
        vm.checkUsersList(listContainer);
        angular.element(document.getElementById('skip-whitelist-warning')).removeClass('displayed');
      }
    });
  }

  // Events broadcast
  function responsive (width) {
    // Listener: ResizeController
    $rootScope.$broadcast('responsive',width);
  }

  function changeTab(event,tabGroup) {
    // Listener: TabsController
    $rootScope.$broadcast('changeTab',event,tabGroup);
  }

  function checkUsersList(listcontainer) {
    // Listener: sourceInterestsController
    $rootScope.$broadcast('checkUsersList',listcontainer);
  }
}