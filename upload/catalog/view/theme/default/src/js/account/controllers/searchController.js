angular
  .module('instag-app')
  .controller('SearchController',SearchController);

// Controller services injection
SearchController.$inject = ['$http','$scope','$timeout','$compile'];

function SearchController ($http,$scope,$timeout,$compile) {
  var vm                 = this,
      timeOut;
  vm.searchUsers         = searchInstagram;
  vm.searchTags          = searchTags;
  vm.searchLocations     = searchLocations;
  vm.closeSearchDropdown = closeSearchDropdown;
  vm.numberWithCommas    = numberWithCommas;

  /*
    searchInstagram() - Makes an instagram search for users
    Function parameters:
    searchInput: searched value
    parentId: element parent container ID attribute
    accountId: selected account ID
  */
  function searchInstagram(searchInput,parentId,accountId) {
    // Get element parent container
    var searchElementContainer = angular.element(document.getElementById(parentId));
    // If user inputs 2+ characters
    if (searchInput.length > 2) {
      // Clear previous timeouts
     clearTimeout(timeOut);
     timeOut = setTimeout(function(){
        // Get input value and show search-box container
        var currentSearchValue = searchElementContainer.find('.input-search input').val();
        if (currentSearchValue.length) {
          searchElementContainer.find('.search-results').addClass('search-box-displayed').empty().append(('<img class="loading-gif" src="catalog/view/theme/default/image/dashboard/loading.gif" />'));
          document.getElementById("dashboard-container").addEventListener("click",closeSearchDropdown);
          var results = '';
          // Set endpoints depending on parent ID and user list elements
          if (parentId == 'search-whitelist') {
            var addEndpoint = 'whitelist_users',
                list        = angular.element(document.getElementById('whitelist-users-list'));
          } else {
            var addEndpoint = 'follow_source_users',
                list        = angular.element(document.getElementById('user-sources-list'));
          }
          // Add account id to request
          currentSearchValue += '&account_id='+accountId;
          // Make the request
          $http.get('index.php?route=account/instagram/search_users&username='+currentSearchValue).success(function(data){
            // Check for redirections
            if (data.redirect) {
              window.location = data.redirect;
            }
            // If request response has data
            if (data.users) {
              // Set char limit for usernames, depending on window width
              if (window.innerWidth < 760) {
                charLimit = 19;
              } else {
                charLimit = 24;
              }
              // Construct the HTML to add into search results container
              for (var i=0;i<data.users.length;i++) {
                results += '<div class="user-search-result" ng-controller="AddSourcesController as addUsers">';
                results += '<img src="'+data.users[i].profile_picture+'" />';
                results += '<div class="user-id" id="'+data.users[i].id+'">';
                // Set char limit if needed
                if (data.users[i].username.length <= charLimit) {
                  results += '<h4 data-username="'+data.users[i].username+'">'+data.users[i].username+'</h4>';
                } else {
                  results += '<h4 data-username="'+data.users[i].username+'">'+data.users[i].username.substr(0,charLimit)+'...</h4>';
                };
                results += '<p>'+data.users[i].full_name+'</p>';
                results += '</div>';
                // Check if user is already in the users list and set proper button
                if (!list.find('.'+data.users[i].username.replace('.','')).length) {
                  results += '<button ng-click="addUsers.addUsers($event,\''+accountId+'\',\''+addEndpoint+'\')"><i class="fa fa-plus"></i></button>';
                } else {
                  results += '<button ng-controller="RemoveController as remove" ng-click="remove.removeFromSearch($event,\''+accountId+'\',\''+addEndpoint+'\',\''+data.users[i].id+'\')" class="repeated-user">';
                  results += '<i class="fa fa-check"></i>';
                  results += '</button>';
                }
                results += '</div>';
              }
              // Append elements
              searchElementContainer.find('.search-results').empty().append($compile(results)($scope));
            } else {
              // Hide and empty search results container
              searchElementContainer.find('.search-results').removeClass('search-box-displayed').empty();
            }
          });
        } else {
          // Hide and empty search results container
          searchElementContainer.find('.search-results').removeClass('search-box-displayed').empty();
        }
      },600);
    } else if (searchInput.length < 3) {
      // Hide and empty search results container
      searchElementContainer.find('.search-results').removeClass('search-box-displayed').empty();
    }
  }

  /*
    searchTags() - Makes an instagram search for tags
    Function parameters:
    searchInput: searched value
    parentId: element parent container ID attribute
    accountId: selected account ID
  */
  function searchTags(searchInput,parentId,accountId) {
    var searchElementContainer = angular.element(document.getElementById(parentId));
    if (searchInput.length > 2) {
      clearTimeout(timeOut);
      timeOut = setTimeout(function(){
        var currentSearchValue = searchElementContainer.find('.input-search input').val().replace('#','');
        if (currentSearchValue) {
          searchElementContainer.find('.search-results').addClass('search-box-displayed').empty().append('<img class="loading-gif" src="catalog/view/theme/default/image/dashboard/loading.gif" />');
          document.getElementById("dashboard-container").addEventListener("click",closeSearchDropdown);
          var results = '';
          // Add account id to request
          currentSearchValue += '&account_id='+accountId;
          $http.get('index.php?route=account/instagram/search_tags&tag='+currentSearchValue).success(function(data){
            if (data.redirect) {
              window.location = data.redirect;
            };
            if (data.tags) {
              if (window.innerWidth < 760) {
                charLimit = 17;
              } else {
                charLimit = 24;
              }
              for (var i=0;i<data.tags.length;i++) {
                mediaCount = vm.numberWithCommas(data.tags[i].media_count);
                results += '<div class="user-search-result" ng-controller="AddSourcesController as addTags">';
                results += '<i class="fa fa-hashtag"></i>';
                results += '<div class="user-id" id="'+data.tags[i].name+'">';
                if (data.tags[i].name.length <= charLimit) {
                  results += '<h4 data-tag="'+data.tags[i].name+'">'+data.tags[i].name+'</h4>';
                } else {
                  results += '<h4 data-tag="'+data.tags[i].name+'">'+data.tags[i].name.substr(0,charLimit)+'...</h4>';
                };
                results += '<p>'+mediaCount+' posts</p>';
                results += '</div>';
                if (!angular.element(document.getElementById('hashtag-sources-list')).find('.'+data.tags[i].name.replace('.','')).length) {
                  results += '<button ng-click="addTags.addTags($event,\''+accountId+'\')"><i class="fa fa-plus"></i></button>';
                } else {
                  var listItem = data.tags[i].name.replace('.',''); 
                  results += '<button ng-controller="RemoveController as remove" ng-click="remove.removeFromSearch($event,\''+accountId+'\',\'follow_source_tags\',\''+listItem+'\')" class="repeated-user">';
                  results += '<i class="fa fa-check"></i>';
                  results += '</button>';
                }
                results += '</div>';
              }
              searchElementContainer.find('.search-results').empty().append($compile(results)($scope));
            } else {
              searchElementContainer.find('.search-results').removeClass('search-box-displayed').empty();
            }
          });
        } else {
          searchElementContainer.find('.search-results').removeClass('search-box-displayed').empty();
        }
      },600);
    } else if (searchInput.length < 2) {
      searchElementContainer.find('.search-results').removeClass('search-box-displayed').empty();
    }
  }

  /*
    searchLocations() - Makes an instagram search for locations
    Function parameters:
    searchInput: searched value
    parentId: element parent container ID attribute
    accountId: selected account ID
  */
  function searchLocations(searchInput,parentId,accountId) {
    var searchElementContainer = angular.element(document.getElementById(parentId));
    if (searchInput.length > 2) {
      clearTimeout(timeOut);
      timeOut = setTimeout(function(){
        var currentSearchValue = searchElementContainer.find('.input-search input').val();
        if (currentSearchValue) {
          searchElementContainer.find('.search-results').addClass('search-box-displayed').empty().append('<img class="loading-gif" src="catalog/view/theme/default/image/dashboard/loading.gif" />');
          document.getElementById("dashboard-container").addEventListener("click",closeSearchDropdown);
          var results    = '',
              post_data  = {
                                account_id: accountId,
                                query: currentSearchValue
                              };
          // Make the request
          $http.post('index.php?route=account/instagram/search_locations',post_data).success(function(data){
            if (data.redirect) {
              window.location = data.redirect;
            };
            if (data.locations) {
              if (window.innerWidth < 760) {
                charLimit = 20;
              } else {
                charLimit = 30;
              }
              for (var i=0;i<data.locations.length;i++) {
                results += '<div class="user-search-result" ng-controller="AddSourcesController as add">';
                results += '<i class="fa fa-map-marker"></i>';
                results += '<div class="location-id" id="'+data.locations[i].id+'">';
                if (data.locations[i].name.length <= charLimit) {
                  results += '<h4 data-location="'+data.locations[i].name+'" ';
                  results += 'data-subtitle="'+data.locations[i].subtitle+'">';
                  results += data.locations[i].name+'</h4>';
                } else {
                  results += '<h4 data-location="'+data.locations[i].name+'" ';
                  results += 'data-subtitle="'+data.locations[i].subtitle+'">';
                  results += data.locations[i].name.substr(0,charLimit)+'...</h4>';
                };
                results += '<p>'+data.locations[i].subtitle+'</p>';
                results += '</div>';
                var locationId = data.locations[i].id;
                if (!angular.element(document.getElementById('locations-sources-list')).find('.'+locationId).length) {
                  results += '<button ng-click="add.addLocation($event,\''+accountId+'\')"><i class="fa fa-plus"></i></button>';
                } else {
                  results += '<button ng-controller="RemoveController as remove" ng-click="remove.removeFromSearch($event,\''+accountId+'\',\'follow_source_locations\',\''+locationId+'\')" class="repeated-user">';
                  results += '<i class="fa fa-check"></i>';
                  results += '</button>';
                }
                results += '</div>';
              }
              searchElementContainer.find('.search-results').empty().append($compile(results)($scope));
            } else {
              searchElementContainer.find('.search-results').removeClass('search-box-displayed').empty();
            }
          });
        } else {
          searchElementContainer.find('.search-results').removeClass('search-box-displayed').empty();
        }
      },600);
    } else if (searchInput.length < 2) {
      searchElementContainer.find('.search-results').removeClass('search-box-displayed').empty();
    }
  }

  /*
    closeSearchDropdown(e) - Closes search dropdown if user clicks outside the search container
    Function parameters:
    e: event
  */
  function closeSearchDropdown(e) {
    var searchBox = angular.element(document.getElementById("dashboard-container")).find('.search-box-displayed');
    if (!(searchBox.find(e.target).length || angular.element(e.target).hasClass('search-results'))) {
      if (!angular.element(e.target).parent().hasClass('user-list')) {
        window.clickedTarget = e.target;
        searchBox.removeClass('search-box-displayed');
        document.getElementById("dashboard-container").removeEventListener("click",closeSearchDropdown);
      }
    }
  }

  // http://stackoverflow.com/questions/2901102/how-to-print-a-number-with-commas-as-thousands-separators-in-javascript
  function numberWithCommas(x) {
    var parts = x.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join(".");
  }

}