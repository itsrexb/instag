angular
  .module('instag-app')
  .controller('LeftSidebarController',LeftSidebarController);

// Controller services injection
LeftSidebarController.$inject = ['checkStatusService','timeAgoService','chartService','$scope','$timeout','$http','$compile','$rootScope'];

function LeftSidebarController(checkStatusService,timeAgoService,chartService,$scope,$timeout,$http,$compile,$rootScope) {
  var vm                      = this;
  vm.searchAccounts           = searchAccounts;
  vm.setCurrent               = setCurrent;
  vm.changeAccountStatus      = changeAccountStatus;
  vm.addAccountModal          = addAccountModal;
  vm.closeLeftSidebar         = closeLeftSidebar;
  vm.logoHover                = logoHover;
  vm.removeLogoHover          = removeLogoHover;
  // Services
  vm.checkStatus              = checkStatusService.checkStatus;
  vm.timeAgo                  = timeAgoService.timeAgo;
  vm.historyMarkup            = timeAgoService.historyMarkup;
  vm.showChart                = chartService.showChart;
  vm.changeHeight             = chartService.changeHeight;
  // Events broadcast
  vm.updateFeed               = updateFeed;
  vm.responsive               = responsive;
  vm.getChart                 = getChart;
  vm.getSources               = getSources;

  /*
    searchAccounts() - Filters account listing on left sidebar
  */
  function searchAccounts(searchQuery) {
    var accountsContainer = angular.element(document.getElementById('dashboard-container')).find('.instagram-accounts');
    if (searchQuery.length) {
      var accounts = accountsContainer.find('.account-item');
      for (var i = 0; i < accounts.length; i++) {
        var accountName = angular.element(accounts[i]).find('.sidebar-item-title span').attr('data-username');
        if (accountName.indexOf(searchQuery) < 0) {
          angular.element(accounts[i]).addClass('hide');
        } else {
          angular.element(accounts[i]).removeClass('hide');
        }
      }
    } else {
      accountsContainer.find('.hide').removeClass('hide');
    }
  }

  /*
    setCurrent() - Sets current account and display it's information on dashboard
    Function parameters:
    $event: caller element
    callback: callback function
  */
  function setCurrent($event,callback) {
    // Teardown braintree
    if (typeof braintree_integration === 'object') {
      braintree_integration.teardown(function () {
        braintree_integration = false;
      });
    };
    var clickedElement = angular.element($event.currentTarget);
    // Check if changeAccountStatus() isn't running
    if (!clickedElement.find('.account-square-status').hasClass('loading')) {
      // Show Loader
      angular.element(document.getElementById('loader')).addClass('loading');
      // Get current account id and storage it 
      var accountId = clickedElement.attr('data-id');
      try {
        localStorage.setItem('account_id', accountId);
      } catch (error) {
        // safari sucks
      }
      // Start request...
      $http.get('index.php?route=account/instagram/account&account_id='+accountId).success(function(data){
        if (data.redirect) {
          window.location = data.redirect;
        }
        // Add data to main-container
        var scope = angular.element(document.getElementById('main-container'));
        angular.element(document.getElementById('main-container')).contents().remove();
        angular.element(document.getElementById('main-container')).html(data);
        $compile(angular.element(document.getElementById('main-container')).contents())($scope);
        // Make sure account is not on kickoff
        if (!angular.element(document.getElementById('content')).attr('data-kickoff')) {
          // Reset listeners
          if ($rootScope.$$listeners.changeSettings.length > 0)
            $rootScope.$$listeners.changeSettings.splice(1);
          // Reset listeners
          if ($rootScope.$$listeners.getSources.length > 0)
            $rootScope.$$listeners.getSources.splice(1);
          // Reset listeners
          if ($rootScope.$$listeners.updateBilling.length > 0)
            $rootScope.$$listeners.updateBilling.splice(1);
          // Reset listeners
          if ($rootScope.$$listeners.updateSpeed.length > 0)
            $rootScope.$$listeners.updateSpeed.splice(1);
        };
        // Header Username
        var username = angular.element(document.getElementById('sidebar-username')).clone();
        angular.element(document.getElementById('header-username')).remove();
        username.attr('id','header-username')
        angular.element(document.getElementById('header')).append(username);
        // Account must not be on kickoff
        if (!angular.element(document.getElementById('content')).attr('data-kickoff')) {
          // Draw Chart for dashboard tab
          var post_data = {
            account_id: accountId
          }
          $http.post('index.php?route=account/report_follower_growth',post_data).success(function(data){
            if (data) {
              // Append data
              angular.element(document.getElementById('chart-card')).find('.card-content').empty().append($compile(data)($scope));
              // Draw Chart for dashboard tab
              vm.showChart();
              // Match height of Chart and Influence cards
              vm.changeHeight();
            }
          });
        }
        // Set time ago and add it to event's history container
        var history = angular.element(document.getElementById('history-content'));
        var time = "";
        for (var i=0;i<history.find('.history-item .item-content small').length;i++) {
          var added = new Date(history.children().eq(i).find('.item-content small').attr('data-time'));
          var offset = angular.element(document.getElementById('history-content')).attr('data-offset');
          time = vm.timeAgo(added,offset);
          history.find('.history-item .item-content small').eq(i).html(time);
        }
        // Set clicked account to be new current account
        clickedElement.parent().find('.current-account').removeClass('current-account');     
        clickedElement.addClass('current-account');
        // Checkout responsive
        vm.responsive(window.innerWidth);
        // Check if clicked account is a new one and append the response image to it 
        if (clickedElement.hasClass('new-account')) {
          clickedElement.removeClass('new-account');
          var image = angular.element(document.getElementById('profile-pic')).find('img').clone();
          clickedElement.find('.account-img').append(image);
        }
        // Get Top sources data
        vm.getSources(accountId);
        // Check account status
        vm.checkStatus(angular.element(document.getElementById('content')).attr('data-msg'));
        // Place input range track
        angular.element(document.getElementById('follow-range')).find('.input-range').trigger('change');
        angular.element(document.getElementById('unfollow-range')).find('.input-range').trigger('change');
        var cancelRequest = true;
        // Set billing flag as false so billing tab can load again - billingTab() at tabsController
        window.billingFlag = false;
        // Remove loader
        angular.element(document.getElementById('loader')).removeClass('loading');
      });
    };
  }

  /*
    changeAccountStatus() - Changes account status to Started | Stopped
    Function parameters:
    accountId: selected account ID
    $event: caller element
  */
  function changeAccountStatus($event,accountId) {
    // Show loader
    angular.element(document.getElementById('loader')).addClass('loading');
        // Clicked Element
    var clickedElement = angular.element($event.currentTarget),
        // Search for clicked account on left-sidebar
        accounts       = angular.element(document.getElementById('left-sidebar')).find('.account-item'),
        // Set a list of classes to clear on elements
        removeClasses  = 'reconnect kickoff stopped started start stop disabled expired loading';
    for (var i=0;i<accounts.length;i++) {
      if (accounts.eq(i).attr('data-id') == accountId) {
        var accountElement = accounts.eq(i);
      }
    }
    if (accountElement) {
      // Check if button is disable (only left-sidebar)
      if (!clickedElement.parent().attr('data-false')) {
        // Set action to be performed and endpoint
        var action = accountElement.find('.account-square-status').attr('data-action');
        if (action == "started") {
          var endpoint = 'stop';
        } else if (action == "stopped" || action == 'disabled' || action == 'start') {
          var endpoint = 'start';
        };
        // Check if account has reconnnect status
        if (clickedElement.attr('data-action') == 'reconnect') {
          // Display Modal
          vm.checkStatus('invalid_token');
          angular.element(document.getElementById('loader')).removeClass('loading');
        } else if (clickedElement.attr('data-action') == 'expired') {
          vm.checkStatus('expired');
          angular.element(document.getElementById('loader')).removeClass('loading');
        } else {
          // Adding loading styles
          // Check if clicked button belong to current account before update right-sidebar
          if (accountElement.hasClass('current-account')) {
            var buttonAccount = angular.element(document.getElementById('change-status-btn'));                  // Right-sidebar Button
            buttonAccount.addClass('loading');
          };
          accountElement.find('.account-square-status').removeClass(removeClasses).addClass('loading');
          // Start Request...
          $http.get('index.php?route=account/account/'+endpoint+'&account_id=' + accountId).success(function(data){
            if (data.redirect) {
              window.location = data.redirect;
            };
            if (data.success) {
              var squareStatus = accountElement.find('.account-square-status'),                               // Left-sidebar change status button
                  dotStatus    = accountElement.find('.account-status');
              if (endpoint == 'start') {
                // Check if clicked button belong to current account before update right-sidebar
                if (accountElement.hasClass('current-account')) {
                  // Update right-sidebar elements
                  buttonAccount.removeClass(removeClasses).addClass('started').html('Stop').attr('data-action','stop');
                  buttonAccount.parent().find('i').removeClass('fa-stopped').addClass('fa-started');
                  buttonAccount.parent().find('.item-content p').html('Started');
                };
                // Change left-sidebar button and dot
                squareStatus.removeClass(removeClasses).addClass('started').attr('data-action','started');
                dotStatus.removeClass(removeClasses).addClass('started');
                // Check for mobile and change status
                if (window.innerWidth < 1200) {
                  angular.element(document.getElementById('logo-u-container')).find('.account-item .account-status').removeClass(removeClasses).addClass('started');
                }
              } else if (endpoint == 'stop') {
                // Check if clicked button belong to current account before update right-sidebar
                if (accountElement.hasClass('current-account')) {
                  // Update right-sidebar elements
                  buttonAccount.removeClass(removeClasses).addClass('stopped').html('Start').attr('data-action','start');
                  buttonAccount.parent().find('i').removeClass('fa-started fa-stopped').addClass('fa-stopped');
                  buttonAccount.parent().find('.item-content p').html('Stopped');
                };
                // Change left-sidebar button
                squareStatus.removeClass(removeClasses).addClass('stopped').attr('data-action','stopped');
                dotStatus.removeClass(removeClasses).addClass('stopped');
                // Check for mobile and change status
                if (window.innerWidth < 1200) {
                  angular.element(document.getElementById('logo-u-container')).find('.account-item .account-status').removeClass(removeClasses).addClass('stopped');
                }
              };
              // Change tooltips
              if (data.tooltip) {
                accountElement.find('.account-square-status').attr('data-tooltip',data.tooltip);
                angular.element(document.getElementById('account')).find('.account-status .account-status-icon').attr('data-tooltip',data.tooltip);
              } else {
                accountElement.find('.account-square-status').attr('data-tooltip','');
                angular.element(document.getElementById('account')).find('.account-status .account-status-icon').attr('data-tooltip','');
              }
              // Check if clicked button belong to current account before update right-sidebar
              if (accountElement.hasClass('current-account')) {
                // Update right-sidebar $events
                var historyContent = angular.element(document.getElementById('history-content'));
                if (data.$event) {
                  var item = vm.historyMarkup(data.$event,true);
                  historyContent.prepend(item);
                }
              }
            }
            // Add event card to right-sidebar
            if (data.event) {
              var offset = angular.element(document.getElementById('history-content')).attr('data-offset'),
                  date = new Date(data.event.date_added),
                  html =  '<div class="sidebar-item history-item">';
                  html += '<i class="fa fa-event-'+data.event.code+' history-icon"></i>';
                  html += '<div class="item-content">';
                  html += '<h4>'+data.event.title+'</h4>';
                  html += '<p>'+data.event.description+'</p>';
                  html += '<small data-time="'+data.event.date_added+'">'+vm.timeAgo(date,offset)+'</small>';
              angular.element(document.getElementById('history-content')).prepend(html);  
            }
            angular.element(document.getElementById('loader')).removeClass('loading');
          });
        }
      }
    }
  }

  /* addAccountModal() - It opens or closes account modal depending on argument */
  function addAccountModal(){
    angular.element(document.getElementById('modal-add-instagram')).addClass('displayed');
    if (angular.element(window).innerWidth() < 1200)
      vm.closeLeftSidebar();
  }

  /* closeLeftSidebar() - Closes leftsidebar */
  function closeLeftSidebar(notMobile) {
    var notMobile = notMobile || false;
    // Check if window is mobile
    if (notMobile && window.innerWidth <= 1570 && window.innerWidth > 1200) {
      angular.element(document.getElementById('left-sidebar')).removeClass('open-sidebar');
      if (window.innerWidth <= 1200)
        angular.element(document.getElementById('close-sidebar')).removeClass('opened-close-sidebar');
    } else if (!notMobile) {
      angular.element(document.getElementById('left-sidebar')).removeClass('open-sidebar');
      if (window.innerWidth <= 1200)
        angular.element(document.getElementById('close-sidebar')).removeClass('opened-close-sidebar');
    }
  }

  /* logoHover() = it defines behavior for logo on mobile */
  function logoHover() {
    if (window.innerWidth <= 1570 && window.innerWidth > 730) {
      angular.element(document.getElementById('logo')).addClass('hover');
    }
  }

  /* removeLogoHover = it defines behavior for logo on mobile */
  function removeLogoHover() {
    if (window.innerWidth <= 1570) {
      angular.element(document.getElementById('logo')).removeClass('hover');
      vm.closeLeftSidebar();
    }
  }

  // Events Listeners
  $rootScope.$on('changeAccountStatus',function(e,$event,accountId){
    vm.changeAccountStatus($event,accountId);
  });

  $rootScope.$on('closeLeftSidebar',function(){
    vm.closeLeftSidebar();
  });

  // Events broadcast
  function updateFeed($event) {
    // Listener: TabsController
    $rootScope.$broadcast('updateFeed',$event);
  }

  function responsive (width) {
    // Listener: ResizeController
    $rootScope.$broadcast('responsive',width);
  }

  function getChart() {
    // Listener: ChartController
    $rootScope.$broadcast('getChart');
  }

  function getSources(accountId) {
    // Listener: TopSourcesController
    $rootScope.$broadcast('getSources',accountId);
  }

}