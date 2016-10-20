angular
  .module('instag-app')
  .controller('AddSourcesController',AddSourcesController);

// Controller services injection
AddSourcesController.$inject = ['$http','$compile','$scope','$rootScope'];

function AddSourcesController($http,$compile,$scope,$rootScope) {
  var vm            = this;
  vm.addUsers       = addUsers;
  vm.addTags        = addTags;
  vm.addLocation    = addLocation;
  // Events Broadcast
  vm.checkUsersList = checkUsersList;
  
  /*
    addUsers() - Adds an user as source with a given ID
    Function parameters:
    accountId: Instagram user ID
    endpoint: backend endpoint
  */
  function addUsers($event,accountId,endpoint) {
    $event.stopPropagation();
    angular.element(document.getElementById('loader')).addClass('loading')
    var clickedElement =  angular.element($event.currentTarget),
        userid         =  clickedElement.parent().find('.user-id').attr('id'),
        username       =  clickedElement.parent().find('h4').attr('data-username'),
        post_data      =  {
                            account_id: accountId,
                            data: {}
                          };
    if (endpoint == 'whitelist_users') {
      post_data.data.whitelist_users = [];
      post_data.data.whitelist_users[0] = {
        id : userid,
        username: username
      }
    } else {
      post_data.data.follow_source_users = [];
      post_data.data.follow_source_users[0] = {
        id : userid,
        username: username
      }
    }
    $http.post('index.php?route=account/setting/edit', post_data).success(function(data){
      if (data.redirect) {
        window.location = data.redirect;
      }
      if (data.success) {
        if (endpoint == 'whitelist_users') {
          userid = data.data.whitelist_users[0].id;
          username = data.data.whitelist_users[0].username;
          var listContainer = angular.element(document.getElementById('whitelist-users-list')); 
        } else {
          userid = data.data.follow_source_users[0].id;
          username = data.data.follow_source_users[0].username;
          var listContainer = angular.element(document.getElementById('user-sources-list'));
        }
        var addedUsers = '';
        addedUsers += '<div class="user-list '+username.replace('.','')+' '+userid+'">';
        addedUsers += '<a href="http://www.instagram.com/'+username+'" target="blank"><h4><img src="catalog/view/theme/default/image/dashboard/default_user.jpg" />';
        addedUsers += clickedElement.parent().find('h4').html()+'</h4></a>';
        addedUsers += '<button class="gold-button" ng-controller="RemoveController as remove" ng-click="remove.removeFromList($event,\''+accountId+'\',\''+endpoint+'\',\''+userid+'\')">X</button>';
        addedUsers += '</div>';        
        listContainer.prepend($compile(addedUsers)($scope));
        if (endpoint === 'whitelist_users') {
          // Change add button for check icon
          var button   =  '<button ng-controller="RemoveController as remove" ng-click="remove.removeFromSearch($event,\''+accountId+'\',\'whitelist_users\',\''+userid+'\')" class="repeated-user">';
              button   += '<i class="fa fa-check"></i>';
              button   += '</button>';
          clickedElement.parent().append($compile(button)($scope));
          clickedElement.remove();
          // Update Users count
          var usersCount = parseInt(angular.element(document.getElementById('whitelist-notification-count')).html());
          usersCount ++;
          angular.element(document.getElementById('whitelist-count')).html(usersCount);
          angular.element(document.getElementById('whitelist-notification-count')).html(usersCount);
          angular.element(document.getElementById('tab-dropdown-middle')).find('.whitelist .notification-count').html(usersCount);
        } else {
          // Change add button for check icon
          var button   =  '<button ng-controller="RemoveController as remove" ng-click="remove.removeFromSearch($event,\''+accountId+'\',\'follow_source_users\',\''+userid+'\')" class="repeated-user">';
              button   += '<i class="fa fa-check"></i>';
              button   += '</button>';
          clickedElement.parent().append($compile(button)($scope));
          clickedElement.remove();
          // Update Users count
          var usersCount = parseInt(angular.element(document.getElementById('users-notification-count')).html());
          usersCount ++;
          angular.element(document.getElementById('user-sources-count')).html(usersCount);
          angular.element(document.getElementById('users-notification-count')).html(usersCount);
          angular.element(document.getElementById('tab-dropdown-middle')).find('.users .notification-count').html(usersCount);
          // Check if user is on top-sources card
          var sourcesTable = angular.element(document.getElementById('sources-table')).find('.table-body');
          if (sourcesTable.find('.'+userid).length) {
            sourcesTable.find('.'+userid).addClass('active-source');
          }
        }
        // Check if user is on Kickoff
        if (angular.element(document.getElementById('content')).attr('data-kickoff')) {
          vm.checkUsersList(listContainer);
        }
      } else if (data.errors) {
        var warningModal = angular.element(document.getElementById('list-warning'));
        warningModal.find('.message').html(data.errors);
        warningModal.addClass('displayed');
      }
      // Remove loader
      angular.element(document.getElementById('loader')).removeClass('loading');
    });
  }

  /*
    addTags() - Adds a tag as source with a given ID
    Function parameters:
    accountId: Instagram user ID
    endpoint: backend endpoint
  */
  function addTags($event,accountId) {
    angular.element(document.getElementById('loader')).addClass('loading');
    var clickedElement =  angular.element($event.currentTarget),
        tag            =  clickedElement.parent().find('h4').attr('data-tag'),
        post_data      =  {
                            account_id: accountId,
                            data: {
                              follow_source_tags : [tag]
                            }
                          };
    $http.post('index.php?route=account/setting/edit', post_data).success(function(data){
      if (data.redirect) {
        window.location = data.redirect;
      }
      if (data.success) {
        var addedTags = '';
        addedTags += '<div class="user-list '+tag.replace('.','')+'" ng-controller="RemoveController as remove">';
        addedTags += '<h4><i class="fa fa-hashtag"></i>';
        addedTags += clickedElement.parent().find('h4').html()+'</h4>';
        addedTags += '<button class="gold-button" ng-click="remove.removeFromList($event,\''+accountId+'\',\'follow_source_tags\',\''+data.data.follow_source_tags[0]+'\')">X</button>'
        addedTags += '</div>'
        angular.element(document.getElementById('hashtag-sources-list')).append($compile(addedTags)($scope));
        // Change add button for check icon
        var button   =  '<button ng-controller="RemoveController as remove" ng-click="remove.removeFromSearch($event,\''+accountId+'\',\'follow_source_tags\',\''+data.data.follow_source_tags[0]+'\')" class="repeated-user">';
            button   += '<i class="fa fa-check"></i>';
            button   += '</button>';
        clickedElement.parent().append($compile(button)($scope));
        clickedElement.remove();
        // Increase tags count
        var tagsCount = parseInt(angular.element(document.getElementById('tags-notification-count')).html());
        tagsCount ++;
        angular.element(document.getElementById('hashtags-count')).html(tagsCount);
        angular.element(document.getElementById('tags-notification-count')).html(tagsCount);
        angular.element(document.getElementById('tab-dropdown-middle')).find('.hashtags .notification-count').html(tagsCount);
        // Check if source is on top-sources card
        var sourcesTable = angular.element(document.getElementById('sources-table')).find('.table-body');
        if (sourcesTable.find('.'+data.data.follow_source_tags[0]).length) {
          sourcesTable.find('.'+data.data.follow_source_tags[0]).addClass('active-source');
        }
        // Check if user is on Kickoff
        if (angular.element(document.getElementById('content')).attr('data-kickoff')) {
          var listContainer = angular.element(document.getElementById('hashtag-sources-list'));
          vm.checkUsersList(listContainer);
        }
      } else if (data.errors) {
        var warningModal = angular.element(document.getElementById('list-warning'));
        warningModal.find('.message').html(data.errors);
        warningModal.addClass('displayed');
      }
      // Remove loader
      angular.element(document.getElementById('loader')).removeClass('loading');
    });
  }

  /*
    addTags() - Adds a tag as source with a given ID
    Function parameters:
    accountId: Instagram user ID
    endpoint: backend endpoint
  */
  function addLocation($event,accountId) {
    angular.element(document.getElementById('loader')).addClass('loading');
    var clickedElement   = angular.element($event.currentTarget),
        locationId       = clickedElement.parent().find('.location-id').attr('id'),
        locationName     = clickedElement.parent().find('h4').attr('data-location'),
        locationSubtitle = clickedElement.parent().find('h4').attr('data-subtitle'),
        post_data        = {
                            account_id: accountId,
                            data: {
                              follow_source_locations: [{
                                id:       locationId,
                                name:     locationName,
                                subtitle: locationSubtitle
                              }]
                            }
                         };
    $http.post('index.php?route=account/setting/edit', post_data).success(function(data){
      if (data.redirect) {
        window.location = data.redirect;
      }
      if (data.success) {
        // Append location element to locations sources list 
        var location      = data.data.follow_source_locations[0],
            locationId    = location.id,
            charLimit     = 0,
            addedLocation = '';
        // Deal with location string name length
        // Set char limit for usernames, depending on window width
        if (angular.element(window).innerWidth < 760) {
          charLimit = {
            name:     19,
            subtitle: 21
          };
        } else {
          charLimit = {
            name:     28,
            subtitle: 30
          };
        }
        addedLocation += '<div class="user-list '+locationId+'" ng-controller="RemoveController as remove">';
        addedLocation += '<i class="fa fa-map-marker"></i>';
        addedLocation += '<div class="user-description">';
        addedLocation += '<h4>';
        if (location.name.length > charLimit.name) {
          addedLocation += location.name.substr(0,charLimit.name-3)+'...';
        } else {
          addedLocation += location.name;
        }
        addedLocation += '</h4>';
        addedLocation += '<p>'
        if (location.subtitle.length > charLimit.subtitle) {
          addedLocation += location.subtitle.substr(0,charLimit.subtitle-3)+'...';
        } else {
          addedLocation += location.subtitle;
        }
        addedLocation += '</p>';
        addedLocation += '</div>';
        addedLocation += '<button class="gold-button" ng-click="remove.removeFromList($event,\''+accountId+'\',\'follow_source_locations\',\''+locationId+'\')">X</button>'
        addedLocation += '</div>'
        angular.element(document.getElementById('locations-sources-list')).prepend($compile(addedLocation)($scope));
        // Change add button for check icon
        var button   =  '<button ng-controller="RemoveController as remove" ng-click="remove.removeFromSearch($event,\''+accountId+'\',\'follow_source_locations\',\''+locationId+'\')" class="repeated-user">';
            button   += '<i class="fa fa-check"></i>';
            button   += '</button>';
        clickedElement.parent().append($compile(button)($scope));
        clickedElement.remove();
        // Increase locations count
        var locationsCount = parseInt(angular.element(document.getElementById('locations-notification-count')).html());
        locationsCount ++;
        angular.element(document.getElementById('locations-count')).html(locationsCount);
        angular.element(document.getElementById('locations-notification-count')).html(locationsCount);
        angular.element(document.getElementById('tab-dropdown-middle')).find('.locations .notification-count').html(locationsCount);
        // Check if user is on top-sources card
        var sourcesTable = angular.element(document.getElementById('sources-table')).find('.table-body');
        if (sourcesTable.find('.l'+locationId).length) {
          sourcesTable.find('.l'+locationId).addClass('active-source');
        }
        // Check if user is on Kickoff
        if (angular.element(document.getElementById('content')).attr('data-kickoff')) {
          var listContainer = angular.element(document.getElementById('locations-sources-list'));
          vm.checkUsersList(listContainer);
        }
      } else if (data.errors) {
        var warningModal = angular.element(document.getElementById('list-warning'));
        warningModal.find('.message').html(data.errors);
        warningModal.addClass('displayed');
      }
      // Remove loader
      angular.element(document.getElementById('loader')).removeClass('loading');
    });
  }

  // Events Broadcast
  function checkUsersList(listContainer) {
    // Listener: SourceInterestsController
    $rootScope.$broadcast('checkUsersList',listContainer);
  }  
}