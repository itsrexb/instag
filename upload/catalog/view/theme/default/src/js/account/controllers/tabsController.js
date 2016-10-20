angular
  .module('instag-app')
  .controller('TabsController',TabsController);

// Controller services injection
TabsController.$inject = ['timeAgoService','chartService','$scope','$timeout','$http','$compile','$rootScope'];

function TabsController (timeAgoService,chartService,$scope,$timeout,$http,$compile,$rootScope) {
  var vm = this;
  vm.changeTab         = changeTab;
  vm.billingTab        = billingTab;
  vm.uncollapseBilling = uncollapseBilling;
  vm.collapse          = collapse;
  vm.requestActivity   = requestActivity;
  vm.updateFeed        = updateFeed;
  vm.switchBtns        = switchBtns;
  // Events broadcast
  vm.updateSpeed       = updateSpeed;
  vm.updateBilling     = updateBilling;
  // Get Services function
  vm.timeAgo           = timeAgoService.timeAgo;
  vm.changeHeight      = chartService.changeHeight;


  /*
    changeTab() - Changes selected tab on dashboard container and right-sidebar
    Function parameters:
    $event: caller element
    tabGroup: caller tab group
  */
  function changeTab($event,tabGroup) {
    // Clicked Element
    var clickedElement = angular.element($event.currentTarget);
    if (!clickedElement.hasClass('current-tab')) {
      // Scroll to top
      window.scrollTo(0, 0);
    }
    // Get middle content tab-header and body depending on caller container
    if (tabGroup == 'middle') {
      var tabHeader = angular.element(document.getElementById('tab-header-middle'));
      var tabBody = angular.element(document.getElementById('tab-body-middle'));
    } else {
      var tabHeader = angular.element(document.getElementById('tab-header-sidebar'));  
      var tabBody = angular.element(document.getElementById('tab-body-sidebar'));
    }
    // If selected tab it's activity and it's the first call, request a list of activities
    if (clickedElement.hasClass('request')) {
      clickedElement.removeClass('request');
      vm.requestActivity('activity');
    }
    // Store data-tab attr of clicked element
    var currentClass = clickedElement.attr('data-tab');
    // Update and replace classes on header and body
    tabHeader.find('.current-tab').removeClass('current-tab');
    tabHeader.find('.'+currentClass).addClass('current-tab');
    tabBody.find('.current-tab').removeClass('current-tab');
    tabBody.find('#'+currentClass).addClass('current-tab');
    // If selected tab it's dashboard, rearrange my-influence height
    if (clickedElement.attr('data-tab') == 'dashboard') {
      vm.changeHeight();
    }
    // Check for mobile/tablet dropdown
    if (window.innerWidth < 1200 && tabGroup == 'middle') {
      var clickedClass = clickedElement.attr('data-tab'),
          tabLanguage  = clickedElement.attr('data-tab-language'),
          dropdown     = angular.element(document.getElementById('tab-dropdown-middle'));
      dropdown.find('.current-tab').removeClass('current-tab');
      // Check for clickedElement data-tab attribute
      if (clickedElement.attr('data-tab')) {
        dropdown.find('.'+clickedClass).addClass('current-tab');
        dropdown.find('button').html('<i class="fa fa-bars"></i> '+tabLanguage);
      } else {
        dropdown.find('button').html('<i class="fa fa-bars"></i> '+clickedElement.html());
      }
      // Check for kickoff to show/hide start-account floating button
      var startAccountButton = angular.element(document.getElementById('start-kickoff-button'));
      if ((clickedElement.attr('data-tab') == 'users') && (startAccountButton.hasClass('disabled-users'))) {
        // Hide floating start-account button
        angular.element(document.getElementById('tab-body-middle')).find('.row .start-account-button').addClass('hide');
      } else if ((clickedElement.attr('data-tab') == 'whitelist') && (startAccountButton.hasClass('disabled-whitelist'))) {
        // Hide floating start-account button
        angular.element(document.getElementById('tab-body-middle')).find('.row .start-account-button').addClass('hide');
      } else {
        angular.element(document.getElementById('tab-body-middle')).find('.row .start-account-button').removeClass('hide');
      }
    }
  }

  /*
    billingTab() - Changes selected tab to billing and make actions according to client's resolution
    Function parameters:
    $event: caller element
  */
  function billingTab($event) {
    vm.changeTab($event,'middle');
    // Change tab to billing
    if (!window.billingFlag) {
      // Show Loader
      angular.element(document.getElementById('loader')).addClass('loading');
      window.billingFlag = true;
      var accountId = angular.element(document.getElementById('left-sidebar')).find('.current-account').attr('data-id'),
          post_data = { account_id: accountId};
      $http.post('index.php?route=account/billing',post_data).success(function(data){
        var scope = angular.element(document.getElementById('billing')).scope();
        angular.element(document.getElementById('billing')).append($compile(data)(scope));
        // Reset listeners
        if ($rootScope.$$listeners.updateBilling.length > 0)
          $rootScope.$$listeners.updateBilling.splice(1);
        if ($rootScope.$$listeners.updateSpeed.length > 0)
          $rootScope.$$listeners.updateSpeed.splice(1);
        // Uncollapse speed/billing if not mobile or no plan on mobile
        if (angular.element(window).innerWidth() > 760 || angular.element(document.getElementById('billing')).find('.has-speed').attr('data-selected-product') == '0') {
          // Uncollapse tabs
          vm.uncollapseBilling(true);
        }
        // Remove loader
        angular.element(document.getElementById('loader')).removeClass('loading');
      });
    }
  }

  /* uncollapseBilling() - Uncollapses ALL collapsable elements inside billing tab */
  function uncollapseBilling(hasSpeed) {
    var speedContainer = angular.element(document.getElementById('speeds'));
    var productContainer = angular.element(document.getElementById('products'));
    var hasSpeed = hasSpeed || false;
    // Check if account is in trial
    var selected = angular.element(document.getElementById('billing')).find('.has-speed').attr('data-selected-product');
    if (selected == '0') {
      // get default speed and billing for accounts without a plan
      var default_speed   = speedContainer.children().eq(0);
      var default_billing = productContainer.find('#category-' + default_speed.attr('data-category-id')).children().eq(0);

      // Emulate updateSpeed function to select first speed
      vm.updateSpeed({
        currentTarget : default_speed
      });

      // Emulate updateBilling function to select first product
      vm.updateBilling({
        currentTarget : default_billing
      });
    } else {    // If account it's not on trial
      // Check for selected product and assign a category and product class
      switch (selected) {
        case "1":
        case "2":
        case "3":
          var category = ".category-1";
          break;
        case "4":
        case "5":
        case "6":
          var category = ".category-2";
          break;
        case "7":
        case "8":
        case "9":
          var category = ".category-3";
          break;
      }
      var product = '.product-'+selected;
      // Trigger selected category and product click
      $timeout(function(){
        speedContainer.find(category).trigger('click');
        productContainer.find(product).trigger('click');
      });
    };
    // Uncollapse containers
    vm.collapse();
  };

  /* collapse() = Collapses all elements with */
  function collapse() {
    // Go through all collapse items in billing
    var collapse = angular.element(document.getElementById('billing')).find('.collapse');
    collapse.addClass('opened');
    collapse.find('.collapse-header').removeClass('has-speed has-billing');
  }

  /*
    requestActivity() - Request for activity feeds on Activity | Followback tabs
    Function parameters:
    endpoint: feed endpoint
  */
  function requestActivity(endpoint) {
    // Check for received endpoint on call
    var feedContainer = '#activity';
    if (endpoint == 'activity') {
      var route = 'activity';
    } else if (endpoint == 'followback') {
      var route = 'event_activity';
    }
    // Check for device to select scrollcontainer and feed container
    if (window.innerWidth > 730) {
      var feed = angular.element(document.getElementById('right-sidebar')).find(feedContainer);
      var scrollContainer = angular.element(document.getElementById('right-sidebar-content'));
    } else {
      var feed = angular.element(document.getElementById('tab-body-middle')).find(feedContainer);
      var scrollContainer = angular.element(window);
    }
    // Drop all events
    feed.find('.activity-content').empty()
    // Check if feed it's not already loading activities
    var refreshFeed = feed.find('.refresh-act-feed');
    if (!refreshFeed.hasClass('loading-button')) {
      // Mark feed as loading activities
      feed.find('.refresh-act-feed').addClass('loading-button');
      // Set post data
      var id = angular.element(document.getElementById('left-sidebar')).find('.current-account').attr('data-id');
      var post_data = {
        account_id: id,
        limit: 12
      }
      // Make the request
      $http.post('index.php?route=account/'+route+'/html',post_data).success(function(data){
        // Check for redirection on response
        if (data.redirect) {
          window.location = data.redirect;
        };
        // Empty feed container and append received data
        feed.find('.activity-content').append($compile(data)($scope));
        // Check for received feeds time and calculate time ago
        var time = "";
        var offset = angular.element(document.getElementById('history-content')).attr('data-offset');
        for (var i=0;i<feed.find('.activity-item .item-content small').length;i++) {
          var added = new Date(feed.find('.activity-content').children().eq(i).find('.item-content small').attr('data-time'));
          time = vm.timeAgo(added,offset);
          feed.find('.activity-item .item-content small').eq(i).html(time);
        }
        //
        if (feed.find('.activity-item').length < 6 && feed.find('.activity-item').length > 0) {
          // Add no more activity image
          feed.find('#loading-activity').remove();
          var noActivity = '<div class="sidebar-item no-activity-item"><img src="catalog/view/theme/default/image/no-activity.jpg"></div>';
          feed.find('.activity-content').append($compile(noActivity)($scope));
        }
        // Mark feed as non-loading activities
        feed.find('.refresh-act-feed').removeClass('loading-button');
        // Bind scroll to defined scroll container
        scrollContainer.bind("scroll", function(e) {
          e.preventDefault();
          var scrolledElement = scrollContainer;
          // Calculate scroll position
          var scrolled = (scrolledElement.scrollTop() || window.pageYOffset)  - (scrolledElement.clientTop || 0);
          scrolled += scrollContainer.innerHeight();
          // Compare scroll position with feed height to see if it's at bottom
          if (scrolled > feed.innerHeight() && !feed.hasClass('loading-activities') && feed.find('.no-activity-item').length == 0) {
            // Mark feed as loading activities
            feed.addClass('loading-activities');
            var lastKey = angular.element(feed.find('.last-key')).last().attr('data-key');
            if (lastKey.length) {
              // Set post data
              var id = angular.element(document.getElementById('left-sidebar')).find('.current-account').attr('data-id');
              var post_data = {
                account_id: id,
                limit: 12,
                last_evaluated_key: lastKey
              };
              // Check active feed
              var endpoint = angular.element(document.getElementById('activity')).find('.active').attr('data-feed');
              if (endpoint == 'activity') {
                var route = 'activity';
              } else if (endpoint == 'followback') {
                var route = 'event_activity';
              }
              // Make the request
              $http.post('index.php?route=account/'+route+'/html',post_data).success(function(data){
                // Check for redirection on response
                if (data.redirect) {
                  window.location = data.redirect;
                };
                // Remove activities loader
                feed.find('#loading-activity').remove();
                // Append activities markup
                var activitiesLength = feed.find('.activity-item .item-content small').length;
                feed.find('.activity-content').append($compile(data)($scope));
                // Check for received feeds time and calculate time ago
                var time = "";
                var offset = angular.element(document.getElementById('history-content')).attr('data-offset');
                for (var i=activitiesLength;i<feed.find('.activity-item .item-content small').length;i++) {
                  var added = new Date(feed.find('.activity-content .activity-item').eq(i).find('.item-content small').attr('data-time'));
                  time = vm.timeAgo(added,offset);
                  feed.find('.activity-item .item-content small').eq(i).html(time);
                }
                // Mark feed as non-loading activities
                feed.removeClass('loading-activities');
              });
            } else {    // There's no last-key
              // Remove activities loader
              angular.element(document.getElementById('loading-activity')).css('opacity','0').remove();
              // Mark feed as non-loading activities
              feed.removeClass('loading-activities');
              // Add no more activity image
              var noActivity = '<div class="sidebar-item no-activity-item"><img src="catalog/view/theme/default/image/no-activity.jpg"></div>';
              feed.find('.activity-content').append(noActivity);
            }
          }
        });
      });
    }
  }

  /*
    updateFeed() - Update activity tab feed
  */
  function updateFeed($event) {
    if ($event) {
      var clickedElement = angular.element($event.currentTarget);
      var endpoint = clickedElement.parent().find('.active').attr('data-feed');
      requestActivity(endpoint);
    }
  }

  /*
    switchBtns() - swiches active group buttons
  */
  function switchBtns($event) {
    var clickedElement = angular.element($event.target);
    clickedElement.parent().find('.active').removeClass('active');
    clickedElement.addClass('active');
  }

  // Events Listeners
  $rootScope.$on('requestActivity',function(e,endpoint){
    requestActivity(endpoint);
  });

  $rootScope.$on('updateFeed',function(e,$event){
    updateFeed($event);
  });

  $rootScope.$on('switchBtns',function(e,$event){
    switchBtns($event);
  });

  $rootScope.$on('changeTab',function(e,event,tabGroup){
    changeTab(event,tabGroup);
  });

  // Events broadcast
  function updateSpeed($event) {
    // Listener: BillingTabController
    $rootScope.$broadcast('updateSpeed',$event);
  }

  function updateBilling($event) {
    // Listener: BillingTabController
    $rootScope.$broadcast('updateBilling',$event);
  }

}