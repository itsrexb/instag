angular
  .module('instag-app')
  .controller('TopSourcesController',TopSourcesController);

// Controller services injection
TopSourcesController.$inject = ['$scope','$compile','$http','$rootScope'];

function TopSourcesController($scope,$compile,$http,$rootScope) {
  var vm        = this;
  vm.getSources = getSources;

  /*
    getSources() - Changes selected tab on dashboard container and right-sidebar
    Function parameters:
    $event: caller element
    tabGroup: caller tab group
  */
  function getSources(accountId,dateRange,$event) {
        // Check for date range
    var dateRange        = dateRange || '-30 days',
        $event           = $event || false,
        // Set post data
        post_data        =  {
                            account_id: accountId,
                            date_start: dateRange
                            },
        sourcesContainer = angular.element(document.getElementById('top-sources')).find('.card-content');
    sourcesContainer.empty().append('<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>');
    $http.post('index.php?route=account/report_source_total',post_data).success(function(data){
      sourcesContainer.empty().append($compile(data)($scope));
      // Change button dropdown data
      if ($event) {
        var clicked = angular.element($event.currentTarget);
        clicked.parent().parent().find('#top-sources-range').html(clicked.html()+' <span class="caret pull-right"></span>');
      }
      // Check for active sources
      var sourcesTable           = sourcesContainer.find('#sources-table .table-body'),
          usersListContainer     = angular.element(document.getElementById('user-sources-list')),
          tagsListContainer      = angular.element(document.getElementById('hashtag-sources-list')),
          locationsListContainer = angular.element(document.getElementById('locations-sources-list'));
      for (var i = 0; i < sourcesTable.children().length; i++) {
        var sourceType = sourcesTable.children().eq(i).find('.users-column').attr('data-type'),
            sourceName = sourcesTable.children().eq(i).find('.users-column').attr('data-source');        
        switch (sourceType) {
          case 'user':
            if (usersListContainer.find('.'+sourceName).length) {
              sourcesTable.children().eq(i).find('.users-column').addClass('active-source');
            }
            break;
          case 'tag':
            if (tagsListContainer.find('.'+sourceName).length) {
              sourcesTable.children().eq(i).find('.users-column').addClass('active-source');
            }
            break;
          case 'location':
            if (locationsListContainer.find('.'+sourceName.substring(1)).length) {
              sourcesTable.children().eq(i).find('.users-column').addClass('active-source');
            }
            break;
        }
      }
    });
  }
  
  // Events Listeners
  $rootScope.$on('getSources',function(e,accountId){
    vm.getSources(accountId);
  });

}