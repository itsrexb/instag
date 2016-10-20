angular
  .module('instag-app')
  .controller('WhitelistController',WhitelistController);

// Controller services injection
WhitelistController.$inject = ['modalsService','$scope','$timeout','$http','$compile','$rootScope'];

function WhitelistController (modalsService,$scope,$timeout,$http,$compile,$rootScope) {
  var vm = this;
  vm.setWhitelistLimit = setWhitelistLimit;
  vm.importWhitelist   = importWhitelist;
  vm.checkWhitelist    = checkWhitelist;
  vm.displayWarning    = displayWarning;
  // Events broadcast
  vm.clearList         = clearList;
  vm.closeModal        = closeModal;
  // Flags
  var checkingFlag     = false;

  /* setWhitelistLimit() - Sets whitelist users importation limit */
  function setWhitelistLimit($event) {
    var $event = $event || false;
    if ($event) {
      var clickedElement = angular.element($event.currentTarget);
      angular.element(document.getElementById('whitelist-limit')).attr('data-limit',clickedElement.html());
    } else {
      var clickedElement = angular.element(document.getElementById('select-whitelist'));
      angular.element(document.getElementById('whitelist-limit')).attr('data-limit',clickedElement.val());
    }
    vm.displayWarning('warning-whitelist');
  }

  /*
    importWhitelist() - Imports whitelist users based on limits
    Function Parameters:
    id: account id
  */
  function importWhitelist(id) {    
    angular.element(document.getElementById('loader')).addClass('loading');
    angular.element(document.getElementById('warning-whitelist')).removeClass('displayed');                                         // Waiting for backend feedback
    var limit = angular.element(document.getElementById('whitelist-limit')).attr('data-limit');
    var post_data = {
      account_id: id,
      limit: limit
    }
    $http.post('index.php?route=account/instagram/import_whitelist_users',post_data).success(function(data){
      if (data.redirect) {
        window.location = data.redirect;
      };
      if (data.data) {
        var addedUsers = '';
        for (var i=0;i<data.data.length;i++) {
          addedUsers += '<div class="user-list" ng-controller="RemoveController as remove">';
          addedUsers += '<a href="http://www.instagram.com/'+data.data[i].username+'" target="blank">';
          addedUsers += '<h4><img src="catalog/view/theme/default/image/dashboard/default_user.jpg" />';
          addedUsers += data.data[i].username+'</h4></a>';
          addedUsers += '<button class="gold-button" ng-click="remove.removeFromList($event,\''+id+'\',\'remove_whitelist_users\',\''+data.data[i].id+'\')">X</button>';
          addedUsers += '</div>';
        }
        angular.element(document.getElementById('whitelist-users-list')).append($compile(addedUsers)($scope));
        if (angular.element(document.getElementById('content')).attr('data-kickoff')) {
          angular.element(document.getElementById('rocket-item')).find('.right-sidebar-whitelist i').removeClass('fa-chevron-right').addClass('fa-check-circle');
          var cardButton = angular.element(document.getElementById('kickoff')).find('.card').eq(0).find('.gold-button');
          cardButton.html(cardButton.attr('data-add-more'));
        }
        var usersCount = parseInt(angular.element(document.getElementById('whitelist-notification-count')).html());
        usersCount += data.data.length;
        angular.element(document.getElementById('whitelist-count')).html(usersCount);
        angular.element(document.getElementById('whitelist-notification-count')).html(usersCount);
        angular.element(document.getElementById('tab-dropdown-middle')).find('.whitelist .notification-count').html(usersCount);
      }
      checkingFlag = false;
      angular.element(document.getElementById('loader')).removeClass('loading');
    });
  }

  /*
    checkWhitelist() - Checks for whitelist users before making an importation, if there's any user added on the whitelist, it wipes the list before making the import
    Function Parameters:
    id: account id
  */

  function checkWhitelist(id) {
    angular.element(document.getElementById('loader')).addClass('loading');
    if (!checkingFlag) {
      if (angular.element(document.getElementById('whitelist-users-list')).children().length > 0) {
        checkingFlag = true;
        vm.clearList(id,'whitelist_users',vm.importWhitelist);
        vm.closeModal();
      } else {
        vm.importWhitelist(id);
        vm.closeModal();
      }
    }
  }

  // Get services functions
  function displayWarning(container) {
    modalsService.displayWarning(container);
  }

  // Events broadcast
  function clearList(id,list,callback) {
    // Listener: RemoveController
    $rootScope.$broadcast('clearList',id,list,callback);
  }

  function closeModal() {
    // Listener: ModalsController
    $rootScope.$broadcast('closeModal');
  }

}