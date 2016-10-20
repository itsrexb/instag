angular
  .module('instag-app')
  .controller('RightSidebarController',RightSidebarController);

// Controller services injection
RightSidebarController.$inject = ['timeAgoService','$scope','$http','$timeout','$rootScope'];

function RightSidebarController (timeAgoService,$scope,$http,$timeout,$rootScope) {
  var vm                 = this;
  vm.changeFlow          = changeFlow;
  window.changingFlow    = false;
  // Services
  vm.historyMarkup       = timeAgoService.historyMarkup;
  // Events broadcast
  vm.accountStatus       = accountStatus;
  vm.changeSettings      = changeSettings;
  
  /*
    updateSetting() - Changes Follow | Unfollow flow on right sidebar
    Function parameters:
    selectedOption: setting selected 
    setting: setting container id
  */
  function changeFlow(button,accountId) {
    if (!window.changingFlow) {
      window.changingFlow = true;
      angular.element(document.getElementById('loader')).addClass('loading');
      var followBtn   = angular.element(document.getElementById('follow-button')),
          unFollowBtn = angular.element(document.getElementById('unfollow-button')),
          flow        = button;
      if (button == 'follow') {
        if (followBtn.hasClass('active')) {
          flow = 'unfollow';
          followBtn.removeClass('active').addClass('inactive');
          unFollowBtn.removeClass('inactive').addClass('active');
        } else {
          followBtn.removeClass('inactive').addClass('active');
          unFollowBtn.removeClass('active').addClass('inactive');
        }
      } else {
        if (unFollowBtn.hasClass('active')) {
          flow = 'follow';
          unFollowBtn.removeClass('active').addClass('inactive');
          followBtn.removeClass('inactive').addClass('active');
        } else {
          unFollowBtn.removeClass('inactive').addClass('active');
          followBtn.removeClass('active').addClass('inactive');
        }
      }
      $http.get('index.php?route=account/account/flow&account_id='+accountId+'&flow='+flow).success(function(data){
        if (data.redirect) {
          window.location = data.redirect;
        };
        var historyContent = angular.element(document.getElementById('history-content')),
            item           = vm.historyMarkup(data.event,true);
        historyContent.prepend(item);
        window.changingFlow = false;
        angular.element(document.getElementById('loader')).removeClass('loading');
      });
    }
  };

  // Events broadcast
  function accountStatus($event,accountId) {
    // Event Listener: LeftSidebarController
    if ($event.bubbles) {
      $event.bubbles = false;
      $rootScope.$broadcast('changeAccountStatus',$event,accountId);
    }
  }

  function changeSettings($event,endpoint,id) {
    // Event Listener: SettingsTabController
    if ($event.bubbles) {
      $event.bubbles = false;
      $rootScope.$broadcast('changeSettings',$event,endpoint,id);
    }
  }

}