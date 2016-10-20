angular
  .module('instag-app')
  .controller('RemoveController',RemoveController);

// Controller services injection
RemoveController.$inject = ['$http','$timeout','$compile','$scope','$rootScope'];

function RemoveController($http,$timeout,$compile,$scope,$rootScope) {
  var vm                  = this;
  vm.removeSource         = removeSource;
  vm.removeFromList       = removeFromList;
  vm.removeFromWarning    = removeFromWarning;
  vm.removeFromSearch     = removeFromSearch;
  window.searchRemoveFlag = false;
  vm.checkTopSourcesTable    = checkTopSourcesTable;
  vm.clearList            = clearList;
  // Events Broadcast
  vm.checkUsersList       = checkUsersList;

  /*
    removeSource() - Removes an especific source from the corresponding list
    Function parameters:
    accountId: account ID
    list: selected list to remove source from
    source: source to be removed
  */
  function removeSource(accountId,list,source) {
    angular.element(document.getElementById('loader')).addClass('loading');
    var post_data = {
                      account_id: accountId,
                      list: list,
                      data: [source]
                    };
    if (!window.searchRemoveFlag) {
      angular.element(document.getElementById("dashboard-container")).find('.search-box-displayed').removeClass('search-box-displayed');
    } else {
      window.searchRemoveFlag = false;
    }
    $http.post('index.php?route=account/setting/remove_from_list',post_data).success(function(data){
      if (data.redirect) {
        window.location = data.redirect;
      }
      if (data.success) {
        switch (list) {
          case 'follow_source_users':
            var usersCount    = parseInt(angular.element(document.getElementById('users-notification-count')).html()),
                listContainer = angular.element(document.getElementById('user-sources-list'));
            // Update Count
            usersCount --;
            angular.element(document.getElementById('users-notification-count')).html(usersCount);
            angular.element(document.getElementById('tab-dropdown-middle')).find('.users .notification-count').html(usersCount);
            break;
          case 'follow_source_tags':
            var tagsCount     = parseInt(angular.element(document.getElementById('tags-notification-count')).html()),
                listContainer = angular.element(document.getElementById('hashtag-sources-list'));
            // Update Count
            tagsCount --;
            angular.element(document.getElementById('tags-notification-count')).html(tagsCount);
            angular.element(document.getElementById('tab-dropdown-middle')).find('.hashtags .notification-count').html(tagsCount);
            break;
          case 'follow_source_locations':
            var locationsCount = parseInt(angular.element(document.getElementById('locations-notification-count')).html()),
                listContainer  = angular.element(document.getElementById('locations-sources-list'));
            // Update Count
            locationsCount --;
            angular.element(document.getElementById('locations-notification-count')).html(locationsCount);
            angular.element(document.getElementById('tab-dropdown-middle')).find('.locations .notification-count').html(locationsCount);
            break;
          case 'whitelist_users':
            var usersCount    = parseInt(angular.element(document.getElementById('whitelist-notification-count')).html()),
                listContainer = angular.element(document.getElementById('whitelist-users-list'));
            // Update Count
            usersCount --;
            angular.element(document.getElementById('whitelist-notification-count')).html(usersCount);
            angular.element(document.getElementById('tab-dropdown-middle')).find('.whitelist .notification-count').html(usersCount);
            break;
        }
        // Remove source from list
        listContainer.find('.'+source).remove();
        // Check if users is on kickoff
        if (angular.element(document.getElementById('content')).attr('data-kickoff')) {
          vm.checkUsersList(listContainer);
        } else {
          // Check if removed source is on top sources table
          if (list == 'follow_source_locations') {
            vm.checkTopSourcesTable('l'+source);
          } else {
            vm.checkTopSourcesTable(source);
          }
        }
      }
      angular.element(document.getElementById('loader')).removeClass('loading');
    });
  }

  /*
    removeFromList() - Removes an especific source from the corresponding list
    Function parameters:
    accountId: account ID
    list: selected list to remove source from
    source: source to be removed
  */
  function removeFromList($event,accountId,list,source) {
    vm.removeSource(accountId,list,source);
  }

  /*
    removeFromWarning() - Removes an especific source from the corresponding list
    Function Parameters:
    $event:    Function trigger event
    accountId: Current account ID
  */
  function removeFromWarning ($event,accountId) {
    var clickedElement = angular.element($event.currentTarget);
    switch (clickedElement.attr('data-type')) {
      case 'user':
        var list   = 'follow_source_users',
            source = clickedElement.attr('data-source');
        break;
      case 'tag':
        var list   = 'follow_source_tags',
            source = clickedElement.attr('data-source');
        break;
      case 'location':
        var list   = 'follow_source_locations',
            source = clickedElement.attr('data-source').substring(1);
        break;
    }
    if (list)
      vm.removeSource(accountId,list,source);
    angular.element(document.getElementById('source-remove')).removeClass('displayed');
  }

  /*
    removeFromSearch() - Removes an especific source from the corresponding list
    Function parameters:
    list: selected list
    source: selected source id
  */
  function removeFromSearch($event,accountId,list,source) {
    $timeout(function(){
      window.searchRemoveFlag = true;
      var clickedElement = angular.element($event.currentTarget);
      switch (list) {
        case 'follow_source_users':
          var button = '<button ng-click="addUsers.addUsers($event,\''+accountId+'\',\''+list+'\')"><i class="fa fa-plus"></i></button>';
          break;
        case 'follow_source_tags':
          var button = '<button ng-click="addTags.addTags($event,\''+accountId+'\')"><i class="fa fa-plus"></i></button>';
          break;
        case 'follow_source_locations':
          var button = '<button ng-click="add.addLocation($event,\''+accountId+'\')"><i class="fa fa-plus"></i></button>';
          break;
        case 'whitelist_users':
          var button = '<button ng-click="addUsers.addUsers($event,\''+accountId+'\',\''+list+'\')"><i class="fa fa-plus"></i></button>';
          break;
      }
      vm.removeSource(accountId,list,source);
      clickedElement.parent().append($compile(button)($scope));
      clickedElement.remove();
    },0);
  }
  
  /*
    checkTopSourcesTable() - Checks if specific source it's on top sources table and removes the trash can if so
    Function parameters:
    sourceClass: Selected item class
  */
  function checkTopSourcesTable (sourceClass) {
    angular.element(document.getElementById('sources-table')).find('.'+sourceClass).removeClass('active-source');
  }

  /*
    clearList() - Removes all sources from the corresponding list
    Function parameters:
    accountId: account ID
    list: selected list to remove item from
  */
  function clearList(accountId,list,callback) {
    var callback  = callback || false,
        post_data = {
                      list: list,
                      account_id: accountId
                    };
    $http.post('index.php?route=account/setting/clear_list',post_data).success(function(data){
      if (data.redirect) {
        window.location = data.redirect;
      }
      switch (list) {
        case 'follow_source_users':
          var listContainer = angular.element(document.getElementById('user-sources-list')),
              mobileCount   = '.users .notification-count',
              desktopCount  = 'users-notification-count';
          break;
        case 'follow_source_tags':
          var listContainer = angular.element(document.getElementById('hashtag-sources-list')),
              mobileCount   = '.hashtags .notification-count',
              desktopCount  = 'tags-notification-count';
          break;
        case 'follow_source_locations':
          var listContainer = angular.element(document.getElementById('locations-sources-list')),
              mobileCount   = '.locations .notification-count',
              desktopCount  = 'locations-notification-count';
          break;
        case 'whitelist_users':
          var listContainer = angular.element(document.getElementById('whitelist-users-list')),
              mobileCount   = '.whitelist .notification-count',
              desktopCount  = 'whitelist-notification-count';
          break;
      }
      if (list != 'whitelist_users') {
        // Check for active sources
        var sourcesTable  = angular.element(document.getElementById('sources-table')).find('.table-body');
        for (var i = 0; i < sourcesTable.children().length; i++) {
          var source = sourcesTable.children().eq(i).find('.users-column');
          // Check if source type is location and remove first (l) character if so
          if (source.attr('data-type') == 'location') {
            sourceClass = '.'+source.attr('data-source').substring(1);
          } else {
            sourceClass = '.'+source.attr('data-source');
          }
          // If source it's on list, remove the 'active-source' class
          if ( listContainer.find(sourceClass).length ) {
            source.removeClass('active-source');
          }
        }
      }
      // Reset Count
      listContainer.empty();
      angular.element(document.getElementById('tab-dropdown-middle')).find(mobileCount).html('0');
      angular.element(document.getElementById(desktopCount)).html('0');
      angular.element(document.getElementById('dashboard')).find('.warning-modal').removeClass('displayed');
      if (callback)
        callback(accountId);
    });
  }

  // Events Listeners
  $rootScope.$on('clearList',function(e,accountId,list,callback){
    vm.clearList(accountId,list,callback);
  });

  // Events Broadcast
  function checkUsersList(listContainer) {
    // Listener: SourceInterestsController
    $rootScope.$broadcast('checkUsersList',listContainer);
  }
}